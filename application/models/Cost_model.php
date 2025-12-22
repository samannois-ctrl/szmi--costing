<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Cost_model extends CI_Model {



	public function __construct()

	{
		parent::__construct();

        $this->load->model('Def_model');
        $this->load->model('Util_model');
 

	}



    public function getUploadedFilesYearMonthArray($year,$month)
    {
        
        $this->db->select('*');
        $this->db->from('file_upload');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $list_uploaded = [];
        if($res = $this->db->get()->result_array()){

            foreach ($res as $k => $row) {
           
            $list_uploaded[$row['file_category']] = $row;
            # code...
            }

        }

        return $list_uploaded;

    }

    public function getFileCategoryInfo($file_category)
    {
        $this->db->select('*');
        $this->db->from('defexcel_file_category');
        $this->db->where('file_category',$file_category);
        if($res = $this->db->get()->row_array()){
            return $res;
        }else{
            return [];
        }
    }


    public function getListAllFilesYearMonth($year,$month)
    {

        $list_uploaded = $this->getUploadedFilesYearMonthArray($year,$month);
        $list_all = [];
        $this->db->select('*');
        $this->db->from('defexcel_file_category');
        $this->db->order_by('seq');

        
        if($res = $this->db->get()->result_array()){

            foreach ($res as $k => $row) {
                if(isset($list_uploaded[$row['file_category']])){
                    $row['uploaded_data'] = $list_uploaded[$row['file_category']];
                    $row['is_uploaded']=1;
                }else{
                    $row['uploaded_data'] = null;
                    $row['is_uploaded']=0;
                }
            $list_all[] = $row;
            }
            
            

        }
        
        return $list_all;
    }  


    public function searchSheetNameInArray($sheet,$excel_sheet_name_search,$sheetNames,$year,$month,$file_category)
    {   
        $excel_sheet_name_search = trim(strval($excel_sheet_name_search));
        $patterns = array('[MM]','[MMM]','[MM-1]','[MMM-1]','[YY]','[YYYY]');
        $res = array('found'=>0,'real_sheet_name_on_excel'=>'','sheet_idx'=> null);
        foreach ($sheetNames as $k => $sheet_name) {
            $sheet_name = trim(strval($sheet_name));
             
            

            $found_each_sheet=0;
            if( strtolower($excel_sheet_name_search) == strtolower($sheet_name) ){
                    $res = array('found'=>1,'real_sheet_name_on_excel'=>$sheet_name,'sheet_idx'=> $k);
                    $found_each_sheet=1;
                return $res;
            //if has pattern
            }else if(  checkStringHasAnyWord($excel_sheet_name_search,$patterns) ){
                //echo "has pattern<br>";
                $sheetname_final = strReplaceYMDPattern($excel_sheet_name_search,$year,$month);
                //echo "compare [$sheetname_final] with [$sheet_name] <br>";

                if( strtolower($sheetname_final) == strtolower($sheet_name) ){
                    $res = array('found'=>1,'real_sheet_name_on_excel'=>$sheet_name,'sheet_idx'=> $k);
                    $found_each_sheet=1;
                    return $res;
                }
            }

            //if still not found
            //other case chhek sheet name
            if($found_each_sheet==0){ 

                //stock value sheet 
                 
                if($file_category == 'stockvalue' && $sheet=='stockvalue'){

                     
                    if(stristr(strtolower($sheet_name),'stock value') !== false){
                        $res = array('found'=>1,'real_sheet_name_on_excel'=>$sheet_name,'sheet_idx'=> $k);
                        return $res;
                    }
                }

                if($file_category == 'dbtrim' && $sheet=='master'){

                     
                    if(stristr(strtolower($sheet_name),'master data') !== false){
                        $res = array('found'=>1,'real_sheet_name_on_excel'=>$sheet_name,'sheet_idx'=> $k);
                        return $res;
                    }
                }


            }// if($found_each_sheet==0){ 

            
        }
        return $res;
        
    }


 

    public function refineRawCellContentToFormatDisplay($def_col,$col_val){

                    $col_type = $def_col['col_type'];
                    $format_cell = $def_col['format_cell'];
  
        return  $this->Util_model->convertRawColumnToDisplay($col_type,$col_val,$format_cell);

    }



    public function getSheetContentFromDB($def_sheet,$year,$month,$is_compact=0,$is_for_display=0,$filter=[])
    {

        // print_r($def_sheet);
        // echo $year.'-'.$month;
        // print_r($filter);exit; 


        $result = array('is_valid_sheet'=>0,'msg'=>'','tables'=>[]);
 
 
        //loop each table main snd sub
        foreach ($def_sheet['def_tables'] as $tbl_name => $def_table) {
            
            if(isset($filter['table_name'])){
                if($filter['table_name'] != $tbl_name){
                    continue;
                }
            }
 
            $data_table = [];

            //get tablename
            $sheet_db_table_name = $this->Def_model->getSheetDataTableName($def_sheet['file_category'], $def_sheet['sheet'],$def_table['table_title']);
            $sheetres = [];
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            $this->db->order_by('id');

            if(isset($filter['arr_ids'])){
                $this->db->where_in('id',$filter['arr_ids']);
            }



            if($sheetres = $this->db->get($sheet_db_table_name)->result_array()){

               

            }else{
               // echo $this->db->last_query();exit;
                $sheetres = [];
            }

            // echo $this->db->last_query();exit;

              
            foreach ($sheetres as $row_idx =>$sheet_row) {
                 
                $data_row = [];

                $data_row['id'] = $sheet_row['id'];
                foreach ($def_table['def_col'] as $def_col_idx => $def_col) {
                    //get excel col and header
                    $excel_col = $def_col['excel_col'];
                    $col_name = $def_col['col_name'];

                        if (array_key_exists($col_name,$sheet_row)){
                            $col_value =$sheet_row[$col_name];
                        }else{
                            $col_value = 'NODATA';
                        }
                        

                        //check value in cell
                        if($is_for_display){
                            $col_value = $this->Cost_model->refineRawCellContentToFormatDisplay($def_col,$col_value);

                        }
 
                        if($is_compact==1){
                            $data_row[$def_col_idx] = $col_value;
                        }else if($is_compact==2){
                            $data_row[$excel_col] = $col_value;
                        }else{
                            $data_row[$col_name] = $col_value;
                        }
                        
                     
  
                }//end loop col

                $data_table[] = $data_row;

            }//end loop row
 
 

            $result_table['is_valid_table'] = 1;
            $result_table['msg'] = 'success';
            $result_table['table_title'] = $def_table['table_title'];
            $result_table['content'] = $data_table;
            $result['tables'][$tbl_name] = $result_table;
            
        }

    

            $result['is_valid_sheet'] = 1;
            $result['msg'] = 'success';
 
           
            return $result;
        



    }


    //calc ================
    public function calcSheetCostCD($year,$month,$file_category,$sheet){







    }



    


    public function getIsFileUploadComplete($year, $month){

        $list = $this->getListAllFilesYearMonth($year, $month);

        $is_upload_all=1;
        foreach ($list as $k => $v) {
            if($v['is_uploaded']==0){
                $is_upload_all=0;
            }
        }

        return $is_upload_all;

     }



     public function  updateSheetCellValue($table_name,$id,$col_name,$new_val){
        $this->db->where('id',$id);
        $this->db->update($table_name, array($col_name => $new_val));
     }


     public function getFileSheetUpload($year,$month,$file_category){
        //get file_sheet_upload
        $this->db->where('file_category',$file_category);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('is_deleted',0);
        $arrSheetInfo = [];
        if($res_fs = $this->db->get('file_sheet_upload')->result_array()){
            foreach ($res_fs as $key => $row_fs) {
                $arrSheetInfo[$row_fs['sheet']] = $row_fs;
            }
        }

        return $arrSheetInfo;
     }


   
}