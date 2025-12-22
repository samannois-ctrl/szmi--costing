<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Cell\Datavalidation_cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Worksheet;
use \avadim\FastExcelReader\Excel;
use \avadim\FastExcelWriter\Excel as ExcelWriter;
use \avadim\FastExcelWriter\Style;
#[\AllowDynamicProperties]

class Import_model extends CI_Model {

    public static $def_sheet_structor = [];
    public static $def_sheet_structor_compact_version = [];
    public static $def_sheet_info = [];


	public function __construct()

	{
		parent::__construct();
 

	}



    public function getContentFromExcelSheet($spreadsheet,$sheet,$def_sheet)
    {
        $result = array('is_valid_sheet'=>0,'msg'=>'','tables'=>[]);
        $arr_real_excel_header = [];


            //check special Hot Sheet
            if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='summary'){
                //special case for stockvalue summary sheet
                //print_r_html(Import_model::$def_sheet_structor);exit;

                $result = $this->getContentFromStockvalueSummarySheet($spreadsheet,$sheet,$def_sheet);
                return $result;
            
            }        


        // print_r_html($def_sheet);exit;
        $highestRow         = $sheet->getHighestRow(); // e.g. 10
        $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

      
        $max_col_test_header = 5; //max row to test head table
        if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='summary'){
            $max_col_test_header = 3;
        }

        if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='wiprmcon'){
            $max_col_test_header = 3;
            // echo "set max col test header to 3";exit;
        }

        $all_table_valid = 1;
        
        //loop each table main snd sub
        foreach ($def_sheet['def_tables'] as $tbl_name => $def_table) {
            

           
            //find Head Row
            $foundHeadTable  = 0;
            $rowHeadTableNum = 0;
            $def_table_name = $def_table['def_table_name'];
            $table_title = $def_table['table_title'];
            $result_table = array('table_title'=>$table_title,'is_valid_table'=>0,'msg'=>'','content'=>[]);


            for ($row = 1; $row <= $highestRow ; ++$row) {
                $is_all_match = 1;
                //loop each col
                $col_count_test = 1;
                foreach ($def_table['def_col'] as $def_col) {
                    if($col_count_test > $max_col_test_header) break; //limit max col to test header
                    //get excel col and header
                    $excel_col = $def_col['excel_col'];
                    $excel_header = trim(strval($def_col['excel_header']));
                    
                    $header_cell = trim(strval($sheet->getCell($excel_col.$row)->getValue()));
                    if(has_val($header_cell)){
                        if( $header_cell instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                             
                            $header_cell = $header_cell->getPlainText();
                        }
                    }

                        // if($def_sheet['sheet']=='pvcd5' ){
                        //     echo $def_sheet['sheet']."->".$def_sheet['real_sheet_name_on_excel']."</br>";
                        //     echo "Testing Row $row => $excel_col,$row : header_cell=$header_cell , excel_header=$excel_header isallmatch=$is_all_match\n<br>";
                        // }
                        

                    if(  !compare_cell_header($header_cell,$excel_header)   )
                    {
                        $is_all_match = 0;
                        break;

                    }


                    $col_count_test++;
                }//loop each col




                if($is_all_match==1)
                {

                    // if($def_sheet['sheet']=='wiprmcon' ){
                    //    echo "==============Found Head Table $table_title at row $row\n<br>";
                    // }


                    $foundHeadTable = 1;
                    $rowHeadTableNum = $row;


                    //keep real excel header
                    $arr_real_excel_header = [];
                    foreach ($def_table['def_col'] as $def_col) {
                        $excel_col = $def_col['excel_col'];
                        $header_cell = trim(strval($sheet->getCell($excel_col.$row)->getValue()));
                        if(has_val($header_cell)){
                            if( $header_cell instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                                 
                                $header_cell = $header_cell->getPlainText();
                            }
                        }
                        $arr_real_excel_header[$def_col['col_name']] = $header_cell;
                    }

                    //push real excel header to def table
                    $this->pushRealExcelHeaderToDefTable($def_sheet['sheet'],$tbl_name,$arr_real_excel_header);

                    break;
                }

            }

            if($foundHeadTable==0)
            {   
                $all_table_valid=0;
                
                $result_table['is_valid_table'] = 0;
                $result_table['msg'] = 'ไม่พบหัวตาราง '.$table_title;
                $result_table['content'] = [];
                $result['tables'][$tbl_name] = $result_table;
            }


            //found head table
            //echo "Found Head Table $table_title at row $rowHeadTableNum <br>";
            //get content below head table
            $data_table = [];

             
            $shift_row_from_header = 1;
            if(  in_array($def_sheet['sheet'], ['wipfgflexo'])  ){
                $shift_row_from_header = 2;
            }
            $rowStartContent = $rowHeadTableNum + $shift_row_from_header;
            for ($row = $rowStartContent; $row <= $highestRow ; ++$row) {
                $is_all_empty = 1;
                $data_row = [];

                //check skip row condition
                $is_skip_row = 0;
                if( !empty($def_table['cond_skip_row'])){

                    //---- EMPTY_COL_AB -----//
                    if($def_table['cond_skip_row']=='EMPTY_COL_AB'){
                        //check if col A B  all empty
                        $arr_vals = [];
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        
                        if( is_arr_all_not_has_val($arr_vals) ){
                            //echo "Skip Row $row <br>";
                            $is_skip_row = 1;
                        }
                    }
                    //---- EMPTY_COL_AB -----//

                    //---- EMPTY_COL_ABC -----//
                    if($def_table['cond_skip_row']=='EMPTY_COL_ABC'){
                        //check if col A B C all empty
                        $arr_vals = [];
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                        //echo "Row $row : A=$valA , B=$valB , C=$valC <br>";
                        if( is_arr_all_not_has_val($arr_vals) ){
                            //echo "Skip Row $row <br>";
                            $is_skip_row = 1;
                        }
                    }
                    //---- EMPTY_COL_ABC -----//




                    //---- EMPTY_COL_BCD -----//
                    if($def_table['cond_skip_row']=='EMPTY_COL_BCD'){
                        //check if col B C D all empty
                        $arr_vals = [];
                        $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                        $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('D'.$row));

                        // if($def_sheet['sheet']=='wipfgflexo' && $row==11) {

                        //     $arr_vals = [];
                        //     $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        //     $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                        //     $arr_vals[]  = $this->getCellValueFromCellObject($sheet->getCell('D'.$row));
    
                        //     print_r_html($arr_vals);
                        //     echo "done...";
                        //     echo is_arr_all_not_has_val($arr_vals);
                        //     exit;
                        // }
                        //echo $def_sheet['sheet'];
                        //print_r_html($arr_vals);
                        if( is_arr_all_not_has_val($arr_vals) ){
                            //echo "Skip Row $row <br>";
                            $is_skip_row = 1;
                        }
                    }
                    //---- EMPTY_COL_BCD -----//
 

                }

                
                //check end content condition
                $is_end_content = 0;
                if( !empty($def_table['cond_end_content'])){

                    //---- EMPTY_COL_ABC_2ROW -----//
                    if($def_table['cond_end_content']=='EMPTY_COL_ABC_2ROW'){
                        //check if col A B C all empty 2 row
                        $arr_vals = [];
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                        if($row+1 <= $highestRow){
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.($row+1)));
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.($row+1)));
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.($row+1)));
                            if( is_arr_all_not_has_val($arr_vals) ){
                                $is_end_content = 1;
                            }
                        }else{
                            //last row
                            $is_end_content = 1;
                        }
                    }
                    //---- EMPTY_COL_ABC_2ROW -----//


                    //---- EMPTY_COL_BCD_2ROW -----//
                    if($def_table['cond_end_content']=='EMPTY_COL_BCD_2ROW'){
                        //check if col B C D all empty 2 row
                        //echo  'EMPTY_COL_BCD_2ROW';
                        $arr_vals = [];
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                        $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('D'.$row)); 
                        if($row+1 <= $highestRow){
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.($row+1)));
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.($row+1)));
                            $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('D'.($row+1)));
                            if( is_arr_all_not_has_val($arr_vals) ){
                                $is_end_content = 1;
                            }
                        }else{
                            //last row
                            $is_end_content = 1;
                        }
                    }
                    //---- EMPTY_COL_BCD_2ROW -----//


                    //---- GRANDTOTAL_B -----//
                    if($def_table['cond_end_content']=='GRANDTOTAL_B'){
                        
                        $compare_word = strtolower(trim(strval($this->getCellValueFromCellObject($sheet->getCell('B'.$row))) ));
                        if(  $compare_word == 'grand total' ||  $compare_word == 'grandtotal' || $compare_word == 'total'  ){
                            $is_end_content = 1;
                        }
                    }
                    //---- GRANDTOTAL_B -----//


                }

                //check last result of skip row and end content
                if( $is_end_content==1){
                    break; //end content loop
                }
                if( $is_skip_row==1){
                    continue;//go to next row
                }


                foreach ($def_table['def_col'] as $def_col) {
                    //get excel col and header
                    $excel_col = $def_col['excel_col'];
                    $col_name = $def_col['col_name'];


                    $cell_value = $this->getCellValueFromCellObject($sheet->getCell($excel_col.$row));
                    if( has_val($cell_value) ){
                        $is_all_empty = 0;
                    } 
 
                    $data_row[$excel_col] = $cell_value;
                }//end loop col

                if($is_all_empty==1){
                    //echo "All empty row $row , stop process<br>";
                }else{
                    //echo "Add Data Row $row<br>";
                    $data_table[] = $data_row;
                }

            }//end loop row

           // echo "Data Table : <br>";
           // print_r_html($data_table);
            // echo "<br><br>";

            //post process data table if pivot need auto fill empty value from previous row
            // if($tbl_name=='table_name2' &&  $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='stockvalue'){
            //     $data_table = $this->post_process_import_pivot($data_table,'autofill');
            // }

            //check pivot autofill

            // print_r($def_table);

            if($def_table['is_pivot']==1 && $def_table['pivot_collect_mode']=='autofill'){
                $data_table = $this->post_process_import_pivot($data_table,'autofill');
            }

            $result_table['is_valid_table'] = 1;
            $result_table['msg'] = 'success';
            $result_table['content'] = $data_table;
            $result['tables'][$tbl_name] = $result_table;
            
        }

        //check table valid
        if($all_table_valid == 1)   {

            $result['is_valid_sheet'] = 1;
            $result['msg'] = 'success';


            //post process auto fill some extra sheet
            if($def_sheet['sheet']=='wipfgflexo'){
                $result = $this->extra_post_process_import('wipfgflexo',$result);
                
            }
           
            return $result;
        }else{
            $result['is_valid_sheet'] = 0;
            $result['msg'] = 'ไม่ผ่านการตรวจสอบ';
            return $result;
        }



    }


    public function getCellValueFromCellObject($cell)
    {
        $cell_value = $cell->getValue();
        if($cell_value instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                            
            $cell_value = $cell_value->getPlainText();
        }

        if($cell->isFormula()){

            $cell_value = $cell->getCalculatedValue();

        }


        if( is_numeric($cell_value) ){
            //check if date
            if( SharedDate::isDateTime($cell)) {
                //convert to date string
                if(SharedDate::excelToDateTimeObject($cell_value)->format('H:i:s') === '00:00:00'){
                    $cell_value = SharedDate::excelToDateTimeObject($cell_value)->format('Y-m-d H:i:s');
                    return $cell_value;
                }else{
                    $cell_value = SharedDate::excelToDateTimeObject($cell_value)->format('Y-m-d');
                    return $cell_value;
                }
            }
        }


        // if(is_object($cell_value)){
        //     //$cell_value = strval($cell_value);
        //     print_r( $cell_value);
        //     echo  $cell_value->getPlainText()."xxxx<br>";
        //     echo "Warning: Convert Object to String : $cell_value \n<br>";exit;
        // }
        // if(is_array($cell_value)){
        //     //$cell_value = strval($cell_value);
        //     print_r( $cell_value);
        //     echo "Warning: Convert array to String : $cell_value \n<br>";exit;
        // }



        return $cell_value;


    }


    public function extra_post_process_import($type,$result) {


        if($type=='wipfgflexo'){


            // echo "pto wipfgflexo";
            
            
            
            $content = $result['tables']['main']['content'];

            

            foreach ($content as $k => $row) {

                // if($k>0) {echo $k."<<".$content[$k-1]['A'];}
                $old_A =  $row['A'];
                if(!has_val($row['A']) && $k>0 && isset($content[$k-1]) && has_val($content[$k-1]['A']) && ($content[$k]['B']!=1)){
                    $content[$k]['A'] = $content[$k-1]['A']; 
                }

                if(!has_val($row['B']) && $k>0 && isset($content[$k-1]) && has_val($content[$k-1]['B']) && !has_val($old_A)){
                    $content[$k]['B'] = $content[$k-1]['B'];                   
                }


            }

        

            $result['tables']['main']['content'] = $content;


        }




        return $result;
        
    }

    public function post_process_import_pivot($datatable,$mode='autofill') {

        $new_datatable = $datatable;

        if($mode=='autofill'){
           
            foreach ($new_datatable as $k => $row) {
                foreach ($row as $col => $val) {
                    if(!has_val($val) && isset($new_datatable[$k-1]) && has_val($new_datatable[$k-1][$col])){
                        $new_datatable[$k][$col] = $new_datatable[$k-1][$col];
                    }
                }
            }
        }



        return $new_datatable;
        
    }
    

// HOT SHEET SPECIAL PROCESS start-------------


public function getContentFromStockvalueSummarySheet($spreadsheet,$sheet,$def_sheet)
{
    $result = array('is_valid_sheet'=>0,'msg'=>'','tables'=>[]);

    //check sheet data wiprmcon
    if(empty(Import_model::$def_sheet_structor['wiprmcon']['import_result'])){
        $result['is_valid_sheet'] = 0;
        $result['msg'] = 'ไม่พบข้อมูลชีท '.Import_model::$def_sheet_structor['wiprmcon']['excel_sheet_name'];
        return $result;
    }

    if(empty(Import_model::$def_sheet_structor['stockvalue']['import_result'])){
        $result['is_valid_sheet'] = 0;
        $result['msg'] = 'ไม่พบข้อมูลชีท '.Import_model::$def_sheet_structor['stockvalue']['excel_sheet_name'];
        return $result;
    }

    $wiprmcon_pivot_content = Import_model::$def_sheet_structor['wiprmcon']['import_result']['tables']['table_name2']['content'];
    $wiprmcon_pivot_def_col = Import_model::$def_sheet_structor['wiprmcon']['def_tables']['table_name2']['def_col'];
    $stockvalue_pivot_content = Import_model::$def_sheet_structor['stockvalue']['import_result']['tables']['table_name2']['content'];
    $stockvalue_pivot_def_col = Import_model::$def_sheet_structor['stockvalue']['def_tables']['table_name2']['def_col'];

    //copysheet
    $hotsheet = clone $sheet; // create a copy of the current sheet
    $hotsheet->setTitle('hotsheet_'.$def_sheet['sheet']);
    $spreadsheet->addSheet($hotsheet);

    

    $highestRow         = $hotsheet->getHighestRow(); // e.g. 10
    $highestColumn      = $hotsheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

  
    $max_col_test_header = 3; //max row to test head table
  
    $all_table_valid = 1;

    //GET inventory ==============
    $tbl_name = 'table_name1';
    $def_table = $def_sheet['def_tables'][$tbl_name];

    //find Head Row
    $foundHeadTable  = 0;
    $rowHeadTableNum = 0;
    $def_table_name = $def_table['def_table_name'];
    $table_title = $def_table['table_title'];
    $result_table = array('table_title'=>$table_title,'is_valid_table'=>0,'msg'=>'','content'=>[]);



    for ($row = 1; $row <= $highestRow ; ++$row) {
        $is_all_match = 1;
        //loop each col
        $col_count_test = 1;
        foreach ($def_table['def_col'] as $def_col) {
            if($col_count_test > $max_col_test_header) break; //limit max col to test header
            //get excel col and header
            $excel_col = $def_col['excel_col'];
            $excel_header = trim(strval($def_col['excel_header']));
            
            $header_cell = trim(strval($hotsheet->getCell($excel_col.$row)->getValue()));
            if(has_val($header_cell)){
                if( $header_cell instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                     
                    $header_cell = $header_cell->getPlainText();
                }
            }

            if(strtolower($header_cell) != strtolower($excel_header))
            {
                $is_all_match = 0;
                break;

            }
            $col_count_test++;
        }

        if($is_all_match==1)
        {
            $foundHeadTable = 1;
            $rowHeadTableNum = $row;
            break;
        }

    }

    if($foundHeadTable==0)
    {   
        $all_table_valid=0;
        
        $result_table['is_valid_table'] = 0;
        $result_table['msg'] = 'ไม่พบหัวตาราง '.$table_title;
        $result_table['content'] = [];
        $result['tables'][$tbl_name] = $result_table;
    }

    //get and set content below head table
    $data_table = [];

    if($foundHeadTable==1){
             
        $shift_row_from_header = 1;

        $rowStartContent = $rowHeadTableNum + $shift_row_from_header;
        for ($row = $rowStartContent; $row <= $highestRow ; ++$row) {
            $is_all_empty = 1;
            $data_row = [];

            //check skip row condition
            $is_skip_row = 0;
            $is_end_content = 0;

            //---- EMPTY_COL_ABC -----//
        
                //check if col A B C all empty
                $arr_vals = [];
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.$row)); 
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                //echo "Row $row : A=$valA , B=$valB , C=$valC <br>";
                if( is_arr_all_not_has_val($arr_vals) ){
                    //echo "Skip Row $row <br>";
                    $is_end_content = 1;
                }
            
            //---- EMPTY_COL_ABC -----//


            //check last result of skip row and end content
            if( $is_end_content==1){
                break; //end content loop
            }
            if( $is_skip_row==1){
                continue;//go to next row
            }


            //condition to get and set value to hotsheet BY Code ================ START


            //Condition Paper start
            $rm_val = trim(strval($this->getCellValueFromCellObject($sheet->getCell('A'.$row))));
            if(strtolower($rm_val)=='paper'){
                //get wip_value for paper
                $wip_value = 0;

                $wip_value = $this->getProcessedContentDataSum("Sum of value",$wiprmcon_pivot_content,$wiprmcon_pivot_def_col,array('eq'=>array('Res'=>'DMGB'))) +
                $this->getProcessedContentDataSum("Sum of value",$wiprmcon_pivot_content,$wiprmcon_pivot_def_col,array('eq'=>array('Res'=>'DMKL')));

                //echo "Set Paper WIP value at row $row : $wip_value <br>";exit;

                $hotsheet->setCellValue('B'.$row, $wip_value);
            
            }
            //Condition Paper end

            

            //Condition Not paper start
            $rm_val = trim(strval($this->getCellValueFromCellObject($sheet->getCell('A'.$row))));
            if(strtolower($rm_val)=='not paper'){
                //get wip_value for paper
                $wip_value = 0;
                $stk_wh_value = 0;

                // 213,784.77 
                // =SUM(GETPIVOTDATA("Sum of value",'WIP RM & CONSUM'!$B$360,"Res","DMGL")+GETPIVOTDATA("Sum of value",'WIP RM & CONSUM'!$B$360,"Res","DMIS")+GETPIVOTDATA("Sum of value",'WIP RM & CONSUM'!$B$360,"Res","DMVN"))


            // =SUM('STOCK VALUE 0425'!F2270:F2281)+GETPIVOTDATA("Sum of Value",'STOCK VALUE 0425'!$B$2233,"RES","STTB","Commodity","STRING","TYPE","RAW MATERIALS")


                $wip_value = 
                $this->getProcessedContentDataSum("Sum of value",$wiprmcon_pivot_content,$wiprmcon_pivot_def_col,array('in'=>array('Res'=>array('DMGL','DMIS','DMVN'))));

                //echo "Set Not Paper WIP value at row $row : $wip_value <br>";

                $hotsheet->setCellValue('B'.$row, $wip_value);

                //stk_wh_value get RAW MATERIALS is not paper
                // $stk_wh_value1 = 
                // $stk_wh_value2 = $this->getProcessedContentDataSum("Sum of value",$wiprmcon_pivot_content,$wiprmcon_pivot_def_col,array('RES'=>'STTB','Commodity'=>'STRING','TYPE'=>'RAW MATERIALS'));
                
                $stk_wh_value = $this->getProcessedContentDataSum("Sum of value",$stockvalue_pivot_content,$stockvalue_pivot_def_col,array( 'eq'=>array('TYPE'=>'RAW MATERIALS')  ,'not'=>array('Commodity'=>'PAPER')));
                //echo  "Set Not Paper STK WH value at row $row : $stk_wh_value <br>";
    
                $hotsheet->setCellValue('C'.$row, $stk_wh_value);

            
            }
            //Condition Not paper end


            
            //Condition SP start
            $rm_val = trim(strval($this->getCellValueFromCellObject($sheet->getCell('A'.$row))));
            if(strtolower($rm_val)=='sp'){
                //get wip_value for paper
                $stk_wh_value = 0;
            // =SUM(GETPIVOTDATA("Sum of Value",'STOCK VALUE 0425'!$B$2233,"RES","SP00","Commodity","SPARE PART","TYPE","SPARE PART"))

                $stk_wh_value = 
                $this->getProcessedContentDataSum("Sum of value",$stockvalue_pivot_content,$stockvalue_pivot_def_col,array('eq'=>array('Commodity'=>'SPARE PART','TYPE'=>'SPARE PART')));

                //echo "Set Not Paper WIP value at row $row : $wip_value <br>";

                $hotsheet->setCellValue('C'.$row, $stk_wh_value);   
    

            
            }
            //Condition SP end

            // =SUM(GETPIVOTDATA("Sum of value",'WIP RM & CONSUM'!$B$352,"Res","IMCM")+
            // GETPIVOTDATA("Sum of value",'WIP RM & CONSUM'!$B$352,"Res","IMPC"))
            // IMCM
            //Condition IMCM start
            if(strtolower($rm_val)=='imcm'){
                $wip_value = $this->getProcessedContentDataSum("Sum of value",$wiprmcon_pivot_content,$wiprmcon_pivot_def_col,array('in'=>array('Res'=>array('IMCM','IMPC'))));
                $hotsheet->setCellValue('B'.$row, $wip_value);
            }
            //Condition IMCM end
            
            //condition to get and set value to hotsheet BY Code ================ END


            //collectdata from all column
            foreach ($def_table['def_col'] as $def_col) {
                //get excel col and header
                $excel_col = $def_col['excel_col'];
                $col_name = $def_col['col_name'];


                $cell_value = $this->getCellValueFromCellObject($hotsheet->getCell($excel_col.$row));
                if( has_val($cell_value) ){
                    $is_all_empty = 0;
                } 

                $data_row[$excel_col] = $cell_value;
            }//end loop col

            if($is_all_empty==1){
                //echo "All empty row $row , stop process<br>";
            }else{
                //echo "Add Data Row $row<br>";
                $data_table[] = $data_row;
            }


        }//end loop
        
        $result_table['is_valid_table'] = 1;
        $result_table['msg'] = 'success';
        $result_table['content'] = $data_table;
        $result['tables'][$tbl_name] = $result_table;
    }//if found header


    //GET MMR ===========================
    $tbl_name = 'table_name2';
    $def_table = $def_sheet['def_tables'][$tbl_name];

    //find Head Row
    $foundHeadTable  = 0;
    $rowHeadTableNum = 0;
    $def_table_name = $def_table['def_table_name'];
    $table_title = $def_table['table_title'];
    $result_table = array('table_title'=>$table_title,'is_valid_table'=>0,'msg'=>'','content'=>[]);



    for ($row = 1; $row <= $highestRow ; ++$row) {
        $is_all_match = 1;
        //loop each col
        $col_count_test = 1;
        foreach ($def_table['def_col'] as $def_col) {
            if($col_count_test > $max_col_test_header) break; //limit max col to test header
            //get excel col and header
            $excel_col = $def_col['excel_col'];
            $excel_header = trim(strval($def_col['excel_header']));
            
            $header_cell = trim(strval($hotsheet->getCell($excel_col.$row)->getValue()));
            if(has_val($header_cell)){
                if( $header_cell instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                     
                    $header_cell = $header_cell->getPlainText();
                }
            }

            if(strtolower($header_cell) != strtolower($excel_header))
            {
                $is_all_match = 0;
                break;

            }
            $col_count_test++;
        }

        if($is_all_match==1)
        {
            $foundHeadTable = 1;
            $rowHeadTableNum = $row;
            break;
        }

    }

    if($foundHeadTable==0)
    {   
        $all_table_valid=0;
        
        $result_table['is_valid_table'] = 0;
        $result_table['msg'] = 'ไม่พบหัวตาราง '.$table_title;
        $result_table['content'] = [];
        $result['tables'][$tbl_name] = $result_table;
    }

    //get and set content below head table
    $data_table = [];

    if($foundHeadTable==1){

        //echo "rowHeadTableNum:".$rowHeadTableNum."<br>";
             
        $shift_row_from_header = 1;

        $rowStartContent = $rowHeadTableNum + $shift_row_from_header;
        for ($row = $rowStartContent; $row <= $highestRow ; ++$row) {
            $is_all_empty = 1;
            $data_row = [];

            //check skip row condition
            $is_skip_row = 0;
            $is_end_content = 0;

            //---- EMPTY_COL_ABC -----//
        
                //check if col A B C all empty
                $arr_vals = [];
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('A'.$row)); 
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('B'.$row)); 
                $arr_vals[] = $this->getCellValueFromCellObject($sheet->getCell('C'.$row)); 
                
                if( is_arr_all_not_has_val($arr_vals) ){
                    //echo "Skip Row $row <br>";
                    $is_skip_row = 1;
                }
            
            //---- EMPTY_COL_ABC -----//


            //check last result of skip row and end content
            if( $is_end_content==1){
                break; //end content loop
            }
            if( $is_skip_row==1){
                continue;//go to next row
            }


            //condition to get and set value to hotsheet BY Code ================ START
 
            
            //condition to get and set value to hotsheet BY Code ================ END


            //collectdata from all column
            foreach ($def_table['def_col'] as $def_col) {
                //echo "get content<br>";
                //get excel col and header
                $excel_col = $def_col['excel_col'];
                $col_name = $def_col['col_name'];


                $cell_value = $this->getCellValueFromCellObject($hotsheet->getCell($excel_col.$row));
                if( has_val($cell_value) ){
                    $is_all_empty = 0;
                } 

                $data_row[$excel_col] = $cell_value;
            }//end loop col

            if($is_all_empty==1){
                //echo "All empty row $row , stop process<br>";
            }else{
                //echo "Add Data Row $row<br>";
                $data_table[] = $data_row;
            }

           
        }//end loop
        $result_table['is_valid_table'] = 1;
        $result_table['msg'] = 'success';
        $result_table['content'] = $data_table;
        $result['tables'][$tbl_name] = $result_table;

    }//if found header

   
        //print_r($result);exit;
    

    //check table valid ================
    if($all_table_valid == 1)   {

        $result['is_valid_sheet'] = 1;
        $result['msg'] = 'success';
        return $result;
    }else{
        $result['is_valid_sheet'] = 0;
        $result['msg'] = 'ไม่ผ่านการตรวจสอบ';
        return $result;
    }


}

public function  getProcessedContentDataSum($val_col,$src_content,$src_def_col,$arr_conditions,$is_use_col_letter=0){

    $res_sum = 0;
    $refined_condition = [];


    //format arr_condition eq noteq in
    // array('in'=>array('cond_col'=>array('1','2','3')), 
    //     'eq'=>array('cond_col'=>'val'), array('cond_col'=>'val') ,
    //     'not'=>array('cond_col'=>'val'), array('cond_col'=>'val')  )

    if( !has_val($val_col) || empty($src_content) || empty($src_def_col) || empty($arr_conditions) ){
        return $res_sum;
    }

    $compare_operator =array('in','eq','not');

    foreach ($compare_operator as  $comp) {
        $refined_condition[$comp] =[];
        if(!isset($arr_conditions[$comp])){
            $arr_conditions[$comp] = [];
        }
    }

    // print_r($arr_conditions);
    // print_r($refined_condition);

    if($is_use_col_letter==1){
        //val col is col letter
        $val_col_letter = $val_col;

        foreach ($compare_operator as  $comp) {
            foreach ($arr_conditions[$comp] as $cond_col => $cond_val) {
                //let col_letter = cond_col
                $refined_condition[$comp][$cond_col] = array('col_letter'=>$cond_col,'col_val'=>$cond_val);
            }
        }

       

    }else{
        //val col is excel header

        //find val col letter
        $found_val_col = 0;
        $val_col_letter = $this->getColLetterFromExcelHeader($val_col,$src_def_col);
       
      
        foreach ($compare_operator as  $comp) {

            foreach ($arr_conditions[$comp] as $cond_col => $cond_val) {
                //find col letter from def col
    
                $col_letter = $this->getColLetterFromExcelHeader($cond_col,$src_def_col);
                if(has_val($col_letter)){
                    $refined_condition[$comp][$cond_col] = array('col_letter'=>$col_letter,'col_val'=>$cond_val);
                }
    
            }

        }




    }// if use col letter



    //loop content

    foreach ($src_content as $row) {

        $meet_all_conditions = 1;

        //eq
        foreach ($refined_condition['eq'] as $cond_col => $conds) {
            $letter  = $conds['col_letter'];
            if(!array_key_exists($letter, $row)) {$meet_all_conditions=0;break;}
            if(!array_key_exists($val_col_letter, $row)) {$meet_all_conditions=0;break;}

            if( strtolower(strval($row[$conds['col_letter']])) != strtolower(strval($conds['col_val'])) ){
                $meet_all_conditions=0;
                
            }
        }

        //not
        foreach ($refined_condition['not'] as $cond_col => $conds) {
            $letter  = $conds['col_letter'];
            if(!array_key_exists($letter, $row)) {$meet_all_conditions=0;break;}
            if(!array_key_exists($val_col_letter, $row)) {$meet_all_conditions=0;break;}

            if( strtolower(strval($row[$conds['col_letter']])) == strtolower(strval($conds['col_val'])) ){
                $meet_all_conditions=0;
                
            }
        }

        //in
        foreach ($refined_condition['in'] as $cond_col => $conds) {
            $letter  = $conds['col_letter'];
            if(!array_key_exists($letter, $row)) {$meet_all_conditions=0;break;}
            if(!array_key_exists($val_col_letter, $row)) {$meet_all_conditions=0;break;}

            if( !  in_array_ci($row[$conds['col_letter']],$conds['col_val']) ) {
                $meet_all_conditions=0;
                
            }
        }

        if($meet_all_conditions==1){
            $res_sum += $row[$val_col_letter];

        }


    }


    return $res_sum;



}


public function getColLetterFromExcelHeader($excel_header,$src_def_col){

    if( !has_val($excel_header) || empty($src_def_col) ){
        return '';
    }

    //find val col letter
    $found_val_col = 0;
    $val_col_letter = '';
    foreach ($src_def_col as $def_col) {
        if(strtolower(strval($def_col['excel_header'])) == strtolower(strval($excel_header)) ){
            $val_col_letter = $def_col['excel_col'];
            $found_val_col = 1;
            break;  
        }
    }
   

    return $val_col_letter;

}

public function saveFileUploadInfoToDB($year,$month,$file_category,$file_name,$file_path) {
     
    $ins = [
        'year' => $year,
        'month' => $month,
        'file_category' => $file_category,
        'file_name' => $file_name,
        'file_path' => $file_path,

    ];


    if ($this->db->insert('file_upload', $ins)) {
        return $this->db->insert_id();
    } else {
        return '';
    }

    
}

public function commitCleanFileUploadInfoOld($year,$month,$file_category,$file_upload_id) {
     
   $this->db->where("id <> '$file_upload_id'");
   $this->db->where('year',$year);
   $this->db->where('month',$month);
   $this->db->where('is_deleted',0);
   $this->db->where('file_category',$file_category);
    
   $this->db->update('file_upload', array('is_deleted'=>1));

//    echo $this->db->last_query();exit;

   $this->deleteOldWaitingFile(3);
 
}


function deleteOldWaitingFile($day=3){


    $dir = FCPATH."uploads/waiting";

    $list_delete = delete_oldfile_in_dir($dir,(3600*24*$day));

    if(!empty($list_delete)){
        return 1;
    }else{
        return 0;
    }
    

}

public function saveUploadedDefSheetStructorToDB($file_upload_id){

        $res_save_db = [];
        $res_save_db['result'] = 'success';
        $res_save_db['fail_msgs'] = [];
        $res_save_db['sheets'] = [];

        
		//loop $def_sheet_structor
        $sheet_count=0;
        $all_sheet_success=1;
        $year = Import_model::$def_sheet_info['year'];
        $month = Import_model::$def_sheet_info['month'];
        $file_category = Import_model::$def_sheet_info['file_category'];

        if(!has_val($year) || !has_val($month)){
            $res_save_db['result'] = 'failed';
            $res_save_db['fail_msgs'][] = "ไม่ได้ระบุปี หรือเดือนของข้อมูลที่จะอัพโหลด";
            return $res_save_db;
        }
        if(!has_val($file_category) ){
            $res_save_db['result'] = 'failed';
            $res_save_db['fail_msgs'][] = "ไม่ได้ระบุประเภทไฟล์ของข้อมูลที่จะอัพโหลด";
            return $res_save_db;
        }
            
        $this->db->trans_begin();

      

		foreach (Import_model::$def_sheet_structor as $sheet_name => $sheet_data) {
            $sheet_count++;
            $res_save_db['sheets'][$sheet_count] = [];
            $res_save_db['sheets'][$sheet_count]['result'] = "";
            $res_save_db['sheets'][$sheet_count]['sheet_name'] = $sheet_name;
            $res_save_db['sheets'][$sheet_count]['excel_sheet_name'] = $sheet_data['excel_sheet_name'];
            $res_save_db['sheets'][$sheet_count]['real_sheet_name_on_excel'] = $sheet_data['real_sheet_name_on_excel'];
            $res_save_db['sheets'][$sheet_count]['tables'] = [];
            
            $all_table_success = 1;
			foreach ($sheet_data['import_result']['tables'] as $table_name => $table_data) {
                $res_save_db['sheets'][$sheet_count]['tables'][$table_name] = [];
                $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['table_title'] = $table_data['table_title'];
                $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "";

                //transform EXCEL COL to DB COL
                $map_excel_col_to_db_col = $this->mapDefColFromExcelColToDBCol( $sheet_data['def_tables'][$table_name]['def_col']);
                $insert_table_all_rows = [];
                $all_row_success = 1;
                foreach ($table_data['content'] as $idx => $row) {

                    $ins = [];
                    $all_col_success = 1;
                    foreach($row as $col => $value){
                        if(isset($map_excel_col_to_db_col[$col])){
                            $ins[$map_excel_col_to_db_col[$col]] = $value;
                        }else{
                            $all_col_success = 0;
                            $res_save_db['fail_msgs'][] = "ไม่พบ ชื่อคอลัมน์ฐานข้อมูล สำหรับ excel คอลัมน์ '$col' ";
                            break;
                        }
                        
                    }
                    
                    if($all_col_success == 1){
                        //add year month file_upload_id
                        $ins['year'] = $year;
                        $ins['month'] = $month;
                        $ins['file_upload_id'] = $file_upload_id;
                        $insert_table_all_rows[] = $ins; 
                        
                    }else{
                        $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในการเก็บข้อมูลเพื่อบันทึกแถวที่ ".($idx+1);
                        $all_row_success == 0;
                        break;  
                    }

                }

                if($all_row_success == 1){

                    //before insert
                    //delete old sheet with year month file_category
                    
                    $this->db->where('year',$year);
                    $this->db->where('month',$month);
                    $this->db->delete($sheet_data['def_tables'][$table_name]['data_table_name']);

                    // echo $sheet_data['def_tables'][$table_name]['data_table_name'];
                    // print_r($insert_table_all_rows);
                    if(!empty($insert_table_all_rows)){
                        // print_r($insert_table_all_rows);exit;
                        $count_insert = $this->db->insert_batch($sheet_data['def_tables'][$table_name]['data_table_name'], $insert_table_all_rows);
                        //echo $count_insert;exit;
                    }else{
                        $count_insert = 0;
                    }
                 
                    if($count_insert===false){
                        $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "failed";
                        $all_table_success = 0;
                    }else{
                        $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "success"; 
                        $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['count_insert'] = $count_insert;
                    }

                }else{
                    $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในการเก็บข้อมูลของตาราง $table_name";
                    $all_table_success = 0;
                    break;

                }



            }

            if($all_table_success==1){
                $res_save_db['sheets'][$sheet_count]['result'] = "success";
    
                //delete old file_sheet_upload
                $this->db->where('sheet_id',$sheet_data['id']);
                $this->db->update('file_sheet_upload',array('is_deleted'=>1));
    
                //insert file_sheet_upload
                $ins_fsheet = [];
                $ins_fsheet['file_upload_id'] = $file_upload_id;
                $ins_fsheet['sheet_id'] = $sheet_data['id'];
                $ins_fsheet['file_category'] = $file_category;
                $ins_fsheet['sheet'] = $sheet_name;
                $ins_fsheet['real_sheet_name_on_excel'] = $sheet_data['real_sheet_name_on_excel'];
                $ins_fsheet['real_sheet_idx_on_excel'] = $sheet_data['real_sheet_idx_on_excel'];
    
                $this->db->insert('file_sheet_upload',$ins_fsheet);
    
            }else{
                $res_save_db['sheets'][$sheet_count]['result'] = "failed";
                $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในชีท ".$sheet_data['excel_sheet_name'];
                $all_sheet_success=0;
            }


		}





        if($all_sheet_success==0){
            $res_save_db['result'] = 'failed';
        }




        if ($this->db->trans_status() === FALSE || $all_sheet_success==0)
        {
            $res_save_db['result'] = 'failed';
            $this->db->trans_rollback();
        }else{
            $this->db->trans_complete();
        }


        return $res_save_db;
        
	// [def_tables] => Array
        // (
        //     [main] => Array
        //         (
        //             [table_title] => 
        //             [def_table_name] => def_stockvalue_master
        //             [data_table_name] => sheet_stockvalue_master




}



public function saveUploadedDefSheetStructorToDBCompact($file_upload_id){

    $res_save_db = [];
    $res_save_db['result'] = 'success';
    $res_save_db['fail_msgs'] = [];
    $res_save_db['sheets'] = [];

    
    //loop $def_sheet_structor
    $sheet_count=0;
    $all_sheet_success=1;
    $year = Import_model::$def_sheet_info['year'];
    $month = Import_model::$def_sheet_info['month'];
    $file_category = Import_model::$def_sheet_info['file_category'];

    if(!has_val($year) || !has_val($month)){
        $res_save_db['result'] = 'failed';
        $res_save_db['fail_msgs'][] = "ไม่ได้ระบุปี หรือเดือนของข้อมูลที่จะอัพโหลด";
        return $res_save_db;
    }
    if(!has_val($file_category) ){
        $res_save_db['result'] = 'failed';
        $res_save_db['fail_msgs'][] = "ไม่ได้ระบุประเภทไฟล์ของข้อมูลที่จะอัพโหลด";
        return $res_save_db;
    }
        
    $this->db->trans_begin();



    foreach (Import_model::$def_sheet_structor_compact_version as $sheet_name => $sheet_data) {
        $sheet_count++;
        $res_save_db['sheets'][$sheet_count] = [];
        $res_save_db['sheets'][$sheet_count]['result'] = "";
        $res_save_db['sheets'][$sheet_count]['sheet_name'] = $sheet_name;
        $res_save_db['sheets'][$sheet_count]['excel_sheet_name'] = $sheet_data['excel_sheet_name'];
        $res_save_db['sheets'][$sheet_count]['real_sheet_name_on_excel'] = $sheet_data['real_sheet_name_on_excel'];
        $res_save_db['sheets'][$sheet_count]['tables'] = [];
        
        $all_table_success = 1;

        //collect real excel header to json
        $real_excel_header = [];    
        foreach($sheet_data['def_tables'] as $tbl_name => $def_table){
            $real_excel_header[$tbl_name] = [];
            foreach($def_table['def_col'] as $def_col){
                $real_excel_header[$tbl_name][$def_col['col_name']] = $def_col['real_excel_header'];
            }
        }
        $json_real_excel_header = json_encode($real_excel_header);


        foreach ($sheet_data['compact_result']['tables'] as $table_name => $table_data) {
            $res_save_db['sheets'][$sheet_count]['tables'][$table_name] = [];
            $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['table_title'] = $table_data['table_title'];
            $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "";

            //transform EXCEL COL to DB COL
            $map_excel_col_to_db_col = $this->mapDefColFromExcelColToDBCol( $sheet_data['def_tables'][$table_name]['def_col']);
            $map_idx_col_to_db_col = $this->mapDefColFromIdxColToDBCol( $sheet_data['def_tables'][$table_name]['def_col']);

            $map_idx_col_to_col_type = $this->mapDefColFromIdxColToColType( $sheet_data['def_tables'][$table_name]['def_col']);
            $insert_table_all_rows = [];
            $all_row_success = 1;
            foreach ($table_data['content'] as $idx => $row) {

                $ins = [];
                $all_col_success = 1;


                foreach($row as $idx => $value){
                    if(isset($map_idx_col_to_db_col[$idx])){
                        
                        //set value null if empty string for INT DECIMAL
                        if(in_array( strtoupper(strval($map_idx_col_to_col_type[$idx])  ) , ['INT','DECIMAL','DOUBLE'])){
                            //convert excel date to mysql date
                            $value = setNullEmpty($value);
                        }
                        
                        $ins[$map_idx_col_to_db_col[$idx]] = $value;
                        
                    }else{
                        $all_col_success = 0;
                        $res_save_db['fail_msgs'][] = "ไม่พบ ชื่อคอลัมน์ฐานข้อมูล สำหรับ excel คอลัมน์ '$idx' ";
                        break;
                    }
                    
                }
                
                if($all_col_success == 1){
                    //add year month file_upload_id
                    $ins['year'] = $year;
                    $ins['month'] = $month;
                    $ins['file_upload_id'] = $file_upload_id;
                    $insert_table_all_rows[] = $ins; 
                    
                }else{
                    $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในการเก็บข้อมูลเพื่อบันทึกแถวที่ ".($idx+1);
                    $all_row_success == 0;
                    break;  
                }

            }

            if($all_row_success == 1){

                //before insert
                //delete old sheet with year month file_category
                
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->delete($sheet_data['def_tables'][$table_name]['data_table_name']);

                // echo $sheet_data['def_tables'][$table_name]['data_table_name'];
                // print_r($insert_table_all_rows);
                if(!empty($insert_table_all_rows)){
                    // print_r($insert_table_all_rows);exit;
                    $count_insert = $this->db->insert_batch($sheet_data['def_tables'][$table_name]['data_table_name'], $insert_table_all_rows);
                    //echo $count_insert;exit;
                }else{
                    $count_insert = 0;
                }
             
                if($count_insert===false){
                    $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "failed";
                    $all_table_success = 0;
                }else{
                    $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['result'] = "success"; 
                    $res_save_db['sheets'][$sheet_count]['tables'][$table_name]['count_insert'] = $count_insert;
                }

            }else{
                $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในการเก็บข้อมูลของตาราง $table_name";
                $all_table_success = 0;
                break;

            }



        }


        if($all_table_success==1){
            $res_save_db['sheets'][$sheet_count]['result'] = "success";

            //delete old file_sheet_upload
            $this->db->where('sheet_id',$sheet_data['id']);
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            $this->db->where('file_category',$file_category);
            $this->db->update('file_sheet_upload',array('is_deleted'=>1));

            //insert file_sheet_upload
            $ins_fsheet = [];
            
            $ins_fsheet['year'] = $year;
            $ins_fsheet['month'] = $month;
            $ins_fsheet['file_upload_id'] = $file_upload_id;
            $ins_fsheet['sheet_id'] = $sheet_data['id'];
            $ins_fsheet['file_category'] = $file_category;
            $ins_fsheet['sheet'] = $sheet_name;
            $ins_fsheet['real_sheet_name_on_excel'] = $sheet_data['real_sheet_name_on_excel'];
            $ins_fsheet['real_sheet_idx_on_excel'] = $sheet_data['real_sheet_idx_on_excel'];
            $ins_fsheet['json_real_excel_header'] = $json_real_excel_header;


            $this->db->insert('file_sheet_upload',$ins_fsheet);

        }else{
            $res_save_db['sheets'][$sheet_count]['result'] = "failed";
            $res_save_db['fail_msgs'][] = "พบข้อผิดพลาดในชีท ".$sheet_data['excel_sheet_name'];
            $all_sheet_success=0;
        }


    }





    if($all_sheet_success==0){
        $res_save_db['result'] = 'failed';
    }




    if ($this->db->trans_status() === FALSE || $all_sheet_success==0)
    {
        $res_save_db['result'] = 'failed';
        $this->db->trans_rollback();
    }else{
        $this->db->trans_complete();
    }


    return $res_save_db;
    
// [def_tables] => Array
    // (
    //     [main] => Array
    //         (
    //             [table_title] => 
    //             [def_table_name] => def_stockvalue_master
    //             [data_table_name] => sheet_stockvalue_master




}

public function mapDefColFromExcelColToDBCol($def_col) {
    $map_db_col = []; //Excel Col is key and db col is value
    foreach ($def_col as $col) {
        $excel_col = $col['excel_col'];
        $db_col = $col['col_name'];
        $map_db_col[$excel_col] = $db_col;
    }
    return $map_db_col;
}

public function mapDefColFromIdxColToDBCol($def_col) {
    $map_db_col = []; //Idx Col is key and db col is value
    foreach ($def_col as $idx => $col) {
        $db_col = $col['col_name'];
        $map_db_col[$idx] = $db_col;
    }
    return $map_db_col;
}


public function mapDefColFromIdxColToColType($def_col) {
    $map_db_col = []; //Idx Col is key and col type is value each VARCHAR INT DATE  DECIMAL
    foreach ($def_col as $idx => $col) {
        $db_col = $col['col_type'];
        $map_db_col[$idx] = $db_col;
    }
    return $map_db_col;
}


public function convertDefSheetStructorToCompact($is_for_display=0,$is_delete_import_result=1){
    $sheet_names = array_keys(Import_model::$def_sheet_structor);
    foreach($sheet_names as $sheet_name ){
        $this->convertSheetCompact($sheet_name,$is_for_display,$is_delete_import_result);
    }
}

public function convertSheetCompact($sheet_name,$is_for_display=0,$is_delete_import_result=1){

    if( !isset($this::$def_sheet_structor[$sheet_name]['import_result']) ){
        return;
    }

    $this::$def_sheet_structor_compact_version[$sheet_name] = $this::$def_sheet_structor[$sheet_name];

    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result'] = [];
    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['is_valid_sheet'] = $this::$def_sheet_structor[$sheet_name]['import_result']['is_valid_sheet'];
    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['msg'] = $this::$def_sheet_structor[$sheet_name]['import_result']['msg'];

    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'] = [];
    




    foreach($this::$def_sheet_structor[$sheet_name]['import_result']['tables'] as $table_name => $table_data){

        $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name] = [];
        $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['table_title'] = $table_data['table_title'];
        $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['is_valid_table'] = $table_data['is_valid_table'];
        $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['msg'] = $table_data['msg'];


  
        foreach($table_data['content'] as $row_idx => $row_data ){

            $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['content'][$row_idx] = [];
            //sort by defcol
            foreach($this::$def_sheet_structor_compact_version[$sheet_name]['def_tables'][$table_name]['def_col'] as $col_idx => $each_def_col){

                $excel_col = $each_def_col['excel_col'];
                $col_name = $each_def_col['col_name'];
                
                //check value in cell
                if($is_for_display){
                    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['content'][$row_idx][] = $this->Cost_model->refineRawCellContentToFormatDisplay($each_def_col,$row_data[$excel_col]);

                }else{
                    $this::$def_sheet_structor_compact_version[$sheet_name]['compact_result']['tables'][$table_name]['content'][$row_idx][] = $row_data[$excel_col];

                }

            }

        }



    }

    if($is_delete_import_result){
        unset($this::$def_sheet_structor_compact_version[$sheet_name]['import_result']);
    }




}

//HOT SHEET SPECIAL PROCESS end-------------

    // (
    //     [id] => 12
    //     [file_category] => stockvalue
    //     [sheet] => master
    //     [excel_sheet_name] => MASTER
    //     [is_pivot] => 0
    //     [is_multi_table] => 0
    //     [table_name1] => 
    //     [table_name2] => 
    //     [table_name3] => 
    //     [table_name4] => 
    //     [def_tables] => Array
    //         (
    //             [main] => Array
    //                 (
    //                     [table_title] => 
    //                     [def_table_name] => def_stockvalue_master
    //                     [def_col] => Array
    //                         (
    //                             [0] => Array
    //                                 (
    //                                     [id] => 1
    //                                     [excel_col] => A
    //                                     [excel_header] => Part
    //                                     [col_name] => part
    //                                     [col_type] => VARCHAR
    //                                     [col_length] => 30
    //                                     [is_hide] => 0
    //                                     [formula] => 
    //                                     [format_cell] => 
    //                                     [is_calc_sys] => 0
    //                                     [calc_func] => 
    //                                     [extra_flag] => 
    //                                 )
    
    //                             [1] => Array
    //                                 (
    //                                     [id] => 2
    //                                     [excel_col] => B
    //                                     [excel_header] => Description
    //                                     [col_name] => description
    //                                     [col_type] => TEXT
    //                                     [col_length] => 
    //                                     [is_hide] => 0
    //                                     [formula] => 
    //                                     [format_cell] => 
    //                                     [is_calc_sys] => 0
    //                                     [calc_func] => 
    //                                     [extra_flag] => 
    //                                 )
    
    //                             [2] => Array
    //                                 (
    //                                     [id] => 3
    //                                     [excel_col] => C
    //                                     [excel_header] => Unit
    //                                     [col_name] => unit
    //                                     [col_type] => VARCHAR
    //                                     [col_length] => 20
    //                                     [is_hide] => 0
    //                                     [formula] => 
    //                                     [format_cell] => 
    //                                     [is_calc_sys] => 0
    //                                     [calc_func] => 
    //                                     [extra_flag] => 
    //                                 )
    
    //                             [3] => Array
    //                                 (
    //                                     [id] => 4
    //                                     [excel_col] => D
    //                                     [excel_header] => Commodity
    //                                     [col_name] => commodity
    //                                     [col_type] => VARCHAR
    //                                     [col_length] => 40
    //                                     [is_hide] => 0
    //                                     [formula] => 
    //                                     [format_cell] => 
    //                                     [is_calc_sys] => 0
    //                                     [calc_func] => 
    //                                     [extra_flag] => 
    //                                 )
    
    //                             [4] => Array
    //                                 (
    //                                     [id] => 5
    //                                     [excel_col] => E
    //                                     [excel_header] => PC
    //                                     [col_name] => pc
    //                                     [col_type] => VARCHAR
    //                                     [col_length] => 50
    //                                     [is_hide] => 0
    //                                     [formula] => 
    //                                     [format_cell] => 
    //                                     [is_calc_sys] => 0
    //                                     [calc_func] => 
    //                                     [extra_flag] => 
    //                                 )
    

    public function pushRealExcelHeaderToDefTable($sheet_name,$tbl_name,$arr_real_excel_header){

        if( !isset(Import_model::$def_sheet_structor[$sheet_name]['def_tables'][$tbl_name]) ){
            return 0;
        }

        foreach (Import_model::$def_sheet_structor[$sheet_name]['def_tables'][$tbl_name]['def_col'] as $idx => $col) {
            if( isset($arr_real_excel_header[$col['col_name']]) ){
                Import_model::$def_sheet_structor[$sheet_name]['def_tables'][$tbl_name]['def_col'][$idx]['real_excel_header'] = $arr_real_excel_header[$col['col_name']];
            }
        }

        return 1;

    }

//----Fast Excel Reader ----//
public function getContentFromExcelSheetFastExcel(&$spreadsheet,&$sheet,&$def_sheet)
{
    $result = array('is_valid_sheet'=>0,'msg'=>'','tables'=>[]);
    $arr_real_excel_header = [];


        //check special Hot Sheet
        if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='summary'){
            //special case for stockvalue summary sheet
            //print_r_html(Import_model::$def_sheet_structor);exit;

            // $result = $this->getContentFromStockvalueSummarySheetFastExcel($spreadsheet,$sheet,$def_sheet);
            // return $result;
        
        }        

 
  
    $max_col_test_header = 5; //max row to test head table
    if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='summary'){
        $max_col_test_header = 3;
    }

    if( $def_sheet['file_category'] == 'stockvalue' && $def_sheet['sheet']=='wiprmcon'){
        $max_col_test_header = 3;
        // echo "set max col test header to 3";exit;
    }

    $all_table_valid = 1;

    //build sheet_table
    // $sheet_table = $sheet->readRows();

    // display_memory();exit;
    $sheet_with_style = $sheet->readRowsWithStyles();
    $this->sheet_autofill_empty_row_style($sheet_with_style);
    $max_row = count($sheet_with_style);
    $sheet_table = $this->sheet_style_to_normal($sheet_with_style);


    // // print_r_html($sheet_table);exit;

    // foreach($sheet_with_style as $r => $row){
    //     if(array_key_exists('GI', $row)==true){
    //         if($row['GI']['v'] === ''){
    //             echo "found empty \n";
    //             continue;
    //         }
    //     }
    //     // // if( !has_val($row['GI']['v']) ) continue; //skip
    //     // (())
    //     // echo " \n ";
    //     // echo($row['GI']['v']);
    //     // echo " | ";
    //     // echo($row['GI']['o']);
        
    // }
    // exit;


    // display_memory();exit;

    //loop each table main snd sub
    foreach ($def_sheet['def_tables'] as $tbl_name => $def_table) {
        

       
        //find Head Row
        $foundHeadTable  = 0;
        $rowHeadTableNum = 0;
        $def_table_name = $def_table['def_table_name'];
        $table_title = $def_table['table_title'];
        $result_table = array('table_title'=>$table_title,'is_valid_table'=>0,'msg'=>'','content'=>[]);

        
        $row = 0;
       
        foreach($sheet_table as $row => $rowData){
            
            $is_all_match = 1;
            //loop each col
            $col_count_test = 1;
            foreach ($def_table['def_col'] as $def_col) {
                if($col_count_test > $max_col_test_header) break; //limit max col to test header
                //get excel col and header
                $excel_col = $def_col['excel_col'];
                $excel_header = trim(strval($def_col['excel_header']));

                $header_cell = trim(isset($rowData[$excel_col]) ? $rowData[$excel_col] : '');
                
            //    echo "Row $row , Col $excel_col : header_cell = '$header_cell' , excel_header = '$excel_header' <br>";
                 
                if(  !compare_cell_header($header_cell,$excel_header)   )
                {
                    $is_all_match = 0;
                    break;

                }


                $col_count_test++;
            }//loop each col


            // echo "Row $row : is_all_match = $is_all_match <br>";

            if($is_all_match==1)
            {
 

                $foundHeadTable = 1;
                $rowHeadTableNum = $row;

                // print_r($rowData);
                //keep real excel header
                $arr_real_excel_header = [];
                foreach ($def_table['def_col'] as $def_col) {
                    $excel_col = $def_col['excel_col'];
                    $header_cell = trim(isset($rowData[$excel_col]) ? $rowData[$excel_col] : '');
                    $arr_real_excel_header[$def_col['col_name']] = $header_cell;
                }

                //push real excel header to def table
                $this->pushRealExcelHeaderToDefTable($def_sheet['sheet'],$tbl_name,$arr_real_excel_header);

                break;
            }

        }

        if($foundHeadTable==0)
        {   
            $all_table_valid=0;
            
            $result_table['is_valid_table'] = 0;
            $result_table['msg'] = 'ไม่พบหัวตาราง '.$table_title;
            $result_table['content'] = [];
            $result['tables'][$tbl_name] = $result_table;
        }

        //print_r_html($this::$def_sheet_structor);
        // die("stop here...");


        //found head table
        //echo "Found Head Table $table_title at row $rowHeadTableNum <br>";
        //get content below head table
        $data_table = [];

         
        $shift_row_from_header = 1;
        if(  in_array($def_sheet['sheet'], ['wipfgflexo'])  ){
            $shift_row_from_header = 2;
        }
        $rowStartContent = $rowHeadTableNum + $shift_row_from_header;

        // $sheet->reset();
        // $sheet->seek($rowStartContent);
        
        //$sheet_data = $sheet->readRowsWithStyles();
        // $sheet->dimensions();
        // $max_row = $sheet->maxRows();
        //print_r_html($sheet_table);exit;
        
        foreach($sheet_table as $row => $rowData){
            
        

                $rowDataWithStyle = $sheet_with_style[$row] ?? [];
          // echo "Processing Table $table_title : Row $row / $max_row <br>";

            //skip header row
            if($row < $rowStartContent) {continue;} 
            //echo "Row $row =>".($rowData['A'] ?? '')."<br>";
            $is_all_empty = 1;
            $data_row = [];

            //check skip row condition
            $is_skip_row = 0;
            if( !empty($def_table['cond_skip_row'])){

                //---- EMPTY_COL_AB -----//
                if($def_table['cond_skip_row']=='EMPTY_COL_AB'){
                    //check if col A B  all empty
                    $arr_vals = [];
                    $arr_vals[] = isset($rowData['A']) ? $rowData['A'] : '';
                    $arr_vals[] = isset($rowData['B']) ? $rowData['B'] : '';
                    
                    if( is_arr_all_not_has_val($arr_vals) ){
                        //echo "Skip Row $row <br>";
                        $is_skip_row = 1;
                    }
                }
                //---- EMPTY_COL_AB -----//

                //---- EMPTY_COL_ABC -----//
                if($def_table['cond_skip_row']=='EMPTY_COL_ABC'){
                    //check if col A B C all empty
                    $arr_vals = [];
                    $arr_vals[] = isset($rowData['A']) ? $rowData['A'] : '';
                    $arr_vals[] = isset($rowData['B']) ? $rowData['B'] : ''; 
                    $arr_vals[] = isset($rowData['C']) ? $rowData['C'] : '';
                    //echo "Row $row : A=$valA , B=$valB , C=$valC <br>";
                    if( is_arr_all_not_has_val($arr_vals) ){
                        //echo "Skip Row $row <br>";
                        $is_skip_row = 1;
                    }
                }
                //---- EMPTY_COL_ABC -----//




                //---- EMPTY_COL_BCD -----//
                if($def_table['cond_skip_row']=='EMPTY_COL_BCD'){
                    //check if col B C D all empty
                    $arr_vals = [];
                    $arr_vals[]  = isset($rowData['B']) ? $rowData['B'] : '';  
                    $arr_vals[]  = isset($rowData['C']) ? $rowData['C'] : '';
                    $arr_vals[]  = isset($rowData['D']) ? $rowData['D'] : '';
 
                    //print_r_html($arr_vals);
                    if( is_arr_all_not_has_val($arr_vals) ){
                        //echo "Skip Row $row <br>";
                        $is_skip_row = 1;
                    }
                }
                //---- EMPTY_COL_BCD -----//


            }

            
            //check end content condition
            $is_end_content = 0;
            if( !empty($def_table['cond_end_content'])){

                //---- EMPTY_COL_ABC -----//
                if($def_table['cond_end_content']=='EMPTY_COL_ABC'){
                    //check if col A B C all empty 2 row
                    $arr_vals = [];
                    $arr_vals[] = isset($rowData['A']) ? $rowData['A'] : '';
                    $arr_vals[] = isset($rowData['B']) ? $rowData['B'] : ''; 
                    $arr_vals[] = isset($rowData['C']) ? $rowData['C'] : '';
                    if( is_arr_all_not_has_val($arr_vals) ){
                        $is_end_content = 1;
                    }
                }
                //---- EMPTY_COL_ABC -----//


                //---- EMPTY_COL_ABC_2ROW -----//
                if($def_table['cond_end_content']=='EMPTY_COL_ABC_2ROW'){
                    //check if col A B C all empty 2 row
                    $arr_vals = [];
                    $arr_vals[] = isset($rowData['A']) ? $rowData['A'] : '';
                    $arr_vals[] = isset($rowData['B']) ? $rowData['B'] : ''; 
                    $arr_vals[] = isset($rowData['C']) ? $rowData['C'] : '';
                    if($row+1 <= $max_row && isset($sheet_table[$row+1]) ){
                        $arr_vals[] =  $sheet_table[$row+1]['A'] ?? '';
                        $arr_vals[] =  $sheet_table[$row+1]['B'] ?? '';
                        $arr_vals[] =  $sheet_table[$row+1]['C'] ?? '';
                        if( is_arr_all_not_has_val($arr_vals) ){
                            $is_end_content = 1;
                        }
                    }else{
                        //last row
                        $is_end_content = 1;
                    }
                }
                //---- EMPTY_COL_ABC_2ROW -----//


                //---- EMPTY_COL_BCD_2ROW -----//
                if($def_table['cond_end_content']=='EMPTY_COL_BCD_2ROW'){
                    //check if col B C D all empty 2 row
                    //echo  'EMPTY_COL_BCD_2ROW';
                    $arr_vals = [];
                    
                    $arr_vals[] = isset($rowData['B']) ? $rowData['B'] : ''; 
                    $arr_vals[] = isset($rowData['C']) ? $rowData['C'] : '';
                    $arr_vals[] = isset($rowData['D']) ? $rowData['D'] : '';
                    if($row+1 <= $max_row && isset($sheet_table[$row+1]) ){
                        
                        $arr_vals[] =  $sheet_table[$row+1]['B'] ??  '';
                        $arr_vals[] =  $sheet_table[$row+1]['C'] ??  '';
                        $arr_vals[] =  $sheet_table[$row+1]['D'] ??  '';
                        if( is_arr_all_not_has_val($arr_vals) ){
                            $is_end_content = 1;
                        }
                    }else{
                        //last row
                        $is_end_content = 1;
                    }
                }
                //---- EMPTY_COL_BCD_2ROW -----//


                //---- GRANDTOTAL_B -----//
                if($def_table['cond_end_content']=='GRANDTOTAL_B'){
                    
                    $compare_word = strtolower(trim(strval(  (isset($rowData['B']) ? $rowData['B'] : '')   ) ));
                    if(  $compare_word == 'grand total' ||  $compare_word == 'grandtotal' || $compare_word == 'total'  ){
                        $is_end_content = 1;
                    }
                }
                //---- GRANDTOTAL_B -----//


            }

            //check last result of skip row and end content
            if( $is_end_content==1){
                break; //end content loop
            }
            if( $is_skip_row==1){
                continue;//go to next row
            }


            foreach ($def_table['def_col'] as $def_col) {
                //get excel col and header
                $excel_col = $def_col['excel_col'];
                $col_name = $def_col['col_name'];
                

                $cell_value = $rowData[$excel_col] ?? '';

                //check #N/A #DIV/0!
                if($cell_value=='#N/A' || $cell_value=='#DIV/0!'){
                    $cell_value=null;
                }

                //check percent_0
                if($def_col['format_cell']=='percent_0'){

                    
                    if($cell_value===null || $cell_value==='' || !is_numeric($cell_value )){
                        $cell_value = null;
                    }else{
                        $cell_value = floatval($cell_value)*100;
                    }
                    
                }

                $cell_value = $this->getCellValueFastExcel($cell_value,$def_col['col_type'],($rowDataWithStyle[$excel_col] ?? []) );

                if( has_val($cell_value) ){
                    $is_all_empty = 0;
                } 

                $data_row[$excel_col] = $cell_value;
            }//end loop col

            if($is_all_empty==1){
                //echo "All empty row $row , stop process<br>";
            }else{
                //echo "Add Data Row $row<br>";
                $data_table[] = $data_row;
            }

        }//end loop row

      

        if($def_table['is_pivot']==1 && $def_table['pivot_collect_mode']=='autofill'){
            $data_table = $this->post_process_import_pivot($data_table,'autofill');
        }

        $result_table['is_valid_table'] = 1;
        $result_table['msg'] = 'success';
        $result_table['content'] = $data_table;
        $result['tables'][$tbl_name] = $result_table;
        
    }

    //unset
    // unset($sheet_with_style);
    // unset($sheet_table);

    //check table valid
    if($all_table_valid == 1)   {

        $result['is_valid_sheet'] = 1;
        $result['msg'] = 'success';


        //post process auto fill some extra sheet
        if($def_sheet['sheet']=='wipfgflexo'){
            $result = $this->extra_post_process_import('wipfgflexo',$result);
            
        }
       
        return $result;
    }else{
        $result['is_valid_sheet'] = 0;
        $result['msg'] = 'ไม่ผ่านการตรวจสอบ';
        return $result;
    }



}

public function getCellValueFastExcel($cell_value, $col_type, $rowStyleData)
{

    if($col_type=='DATE'){
        if(!empty($cell_value)){

            if(is_numeric($cell_value) && $cell_value > 20000 ){ // case excel number date ex.43752
                $d_str = SharedDate::excelToDateTimeObject($cell_value)->format('Y-m-d');
                if(strtotime($d_str)!==false){
                    return $d_str;
                }else{
                    return null;
                }
            }else if( $d = DateTime::createFromFormat('Y-m-d H:i:s', $cell_value) ) {
                $date_part = explode(' ',$cell_value);
                return ($date_part[0]!='0000-00-00' && has_val($date_part[0]))?($date_part[0]):(null);
            }else{
                return null;
            }
        }else{
            return null;
        }

    }


    if($col_type=='DATETIME'){
        if(!empty($cell_value)){
            if( $d = DateTime::createFromFormat('Y-m-d H:i:s', $cell_value) ) {

                return $cell_value!='0000-00-00 00:00:00' && has_val($cell_value)?($cell_value):(null);
            }
        }else{
            return null;
        }
    }



    if($col_type=='INT' || $col_type=='DECIMAL' || $col_type=='DOUBLE'){
        if(is_numeric($cell_value)){
            return $cell_value;
        }else{
            return null;
        }
    }

    if($col_type=='VARCHAR' || $col_type=='TEXT'){
        return $rowStyleData['o'] ?? $cell_value;
    }

    

    return $cell_value;


}

//----Fast Excel Reader ----//


public function sheet_autofill_empty_row(&$sheet_table){

    //find max key of $sheet_table
    $max_keys = max(array_keys($sheet_table));


    for($i=1;$i<=$max_keys;$i++){
        if( !array_key_exists($i, $sheet_table) ){
            $sheet_table[$i] = [];
            
        }
    }
    ksort($sheet_table);


}


public function sheet_autofill_empty_row_style(&$sheet_style){

    //find max key of $sheet_style
    $max_keys = max(array_keys($sheet_style));


    for($i=1;$i<=$max_keys;$i++){
        if( !array_key_exists($i, $sheet_style) ){
            $sheet_style[$i] = [];
            
        }
    }
    ksort($sheet_style);


}

public function sheet_style_to_normal(&$sheet_style){

    //find max key of $sheet_style
    $sheet_normal = [];
    $max_keys = max(array_keys($sheet_style));

    foreach($sheet_style as $k => $row){
            
        $sheet_normal[$k] = [];

        foreach($row as $col => $data){

            //$sheet_normal[$k][$col]=[];
            $sheet_normal[$k][$col] = $data['v'];
        }


    }

    return $sheet_normal;
    

}

//Export Excel =============
public function creatExcelFromSheetStructor($sheet_structor,$file_export){

  
    $invalidCharacters = \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::getInvalidCharacters();

    // $excel->download('simple.xlsx');
    $excel = ExcelWriter::create('first');
    $excel->removeSheet('first');

     foreach ($sheet_structor as $sheet_name => $each_sheet) {

        //$each_sheet['excel_sheet_name']!=='DB#CD4') continue;
        if($each_sheet['is_found_in_db']==0)  continue;
    
        $excel_sheet_display_name = strip_sheetname($each_sheet['excel_sheet_name'],$invalidCharacters);
        
        
        $excel->makeSheet($excel_sheet_display_name);

        $sheet = $excel->sheet($excel_sheet_display_name);
        $excel_row = 1;
 
        $headStyles = [

            Style::FONT => [
                Style::FONT_STYLE => Style::FONT_STYLE_BOLD
                  
            ],
            Style::BORDER => [
                Style::BORDER_BOTTOM => [
                    Style::BORDER_STYLE => Style::BORDER_THIN,
                    Style::BORDER_COLOR => '#000000',  
                ],
            ],
        ];
        
       
        foreach($each_sheet['def_tables'] as $table_name => $each_def_table){
            if($excel_row!=1) {$excel_row+=3;}
            $table_title = $each_def_table['table_title'];

            //add header
            foreach($each_def_table['def_col'] as $col_def_idx => $def_col){
 
                    $sheet->writeTo($def_col['excel_col'].$excel_row,   $def_col['real_excel_header'],  $headStyles);
                  
            }
            $excel_row++;

            //add data
            foreach($each_sheet['compact_result']['tables'][$table_name]['content'] as $content_idx => $content_row){

                foreach($each_def_table['def_col'] as $col_def_idx => $def_col){


                    if(!empty($def_col['format_cell'])){
                        $str_format_cell = $def_col['format_cell'];
    
                        if( $str_format_cell == 'accounting_2'){
                            $str_format_cell = '#,##0.00';
                        }
                        if( $str_format_cell == 'percent_0'){
                            $str_format_cell = '#,##0\%';
                        }
                        if( $str_format_cell == 'general_num'){
                            $str_format_cell = 'General';
                        }
    
                        // accounting_2
                        // percent_0
                        // general_num
                        //check number
                        if($def_col['col_type']=='INT' || $def_col['col_type']=='DOUBLE' ||  $def_col['col_type']=='DECIMAL'){
                            $cur_val = ($content_row[$col_def_idx]===null)?(null):( floatval($content_row[$col_def_idx]));
                            $sheet->writeTo($def_col['excel_col'].$excel_row,  $cur_val,['format'=>$str_format_cell]);
                        }else{
                            $sheet->writeTo($def_col['excel_col'].$excel_row,  $content_row[$col_def_idx],['format'=>$str_format_cell]);
                        }

    
                        //echo 'format=>'.$str_format_cell.'<br>';
                    }else{
                        if(is_numeric($content_row[$col_def_idx])){
                            $cur_val = floatval($content_row[$col_def_idx]) ;
                        }else{
                            $cur_val = $content_row[$col_def_idx];
                        }
                        $sheet->writeTo($def_col['excel_col'].$excel_row,  $cur_val);
                    }
    
                   
                }
                $excel_row++;

            }



        }




    }

   $excel->download($file_export.'.xlsx');

 
}

public function creatExcelFromSheetStructorPhpSpreadsheet($sheet_structor,$file_export){

    

    $spreadsheet = new Spreadsheet();

    $spreadsheet->removeSheetByIndex(0);
    $invalidCharacters = \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::getInvalidCharacters();


    //loop each sheet from file_category
    foreach ($sheet_structor as $sheet_name => $each_sheet) {
    
        $excel_sheet_display_name = strip_sheetname($each_sheet['excel_sheet_name'],$invalidCharacters);

        $cur_sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $excel_sheet_display_name);
        $spreadsheet->addSheet($cur_sheet);

        //loop table in sheet
        $excel_row = 1;
        //shift 3 row when next table in same sheet
        if($excel_row!=1) {$excel_row+=3;}
        foreach($each_sheet['def_tables'] as $table_name => $each_def_table){

            $table_title = $each_def_table['table_title'];

            //add header
            foreach($each_def_table['def_col'] as $col_def_idx => $def_col){

                $cur_sheet->setCellValue($def_col['excel_col'].$excel_row,   $def_col['real_excel_header']);

            }
            $excel_row++;

            //add data
            foreach($each_sheet['compact_result']['tables'][$table_name]['content'] as $content_idx => $content_row){

                foreach($each_def_table['def_col'] as $col_def_idx => $def_col){

                    $cur_sheet->setCellValue($def_col['excel_col'].$excel_row,   $content_row[$col_def_idx]);
                   
                }
                $excel_row++;

            }



        }
        
       
        
    }

    $spreadsheet->setActiveSheetIndex(0);

    //================================
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_export.'.xlsx"');
        header("Content-Transfer-Encoding: binary ");
        $writer->save('php://output');
        exit();
    //================================


}




//Export Excel =============





}
?>