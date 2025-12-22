<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Def_model extends CI_Model {

    

	public function __construct()

	{
		parent::__construct();


        $this->map_machine_sheet = [];
 
        $this->initMachineSheetMapping();
	}


    public function initMachineSheetMapping()
    {
        if(empty($this->map_machine_sheet)) {
            $this->map_machine_sheet = [];
            $this->db->select('*');
            $this->db->from('defmachine_sheet_relation');
            if ($res = $this->db->get()->result_array()) {
                foreach ($res as $k => $row) {
                    $this->map_machine_sheet[] = $row;
                }
            }
        }     
    }



    public function create_def_of_file($file_category)
    {
        $msg_result = [];
        //check sheet in file_category
        $this->db->select('*');
        $this->db->from('defexcel_sheet');
        $this->db->where('file_category', $file_category);

        $arr_sheet_tables = [];
        if($res = $this->db->get()->result_array()){

            foreach ($res as  $row) {
                $sheet_name = $row['sheet'];
                if(($row['is_multi_table'])==1) {
                    for ($i=1; $i <=4 ; $i++) { 
                        if(!empty($row['table_name'.$i])) {
                            $arr_sheet_tables[] =  array('file_category'=>$file_category,'sheet'=>$sheet_name,'table_name'=>$row['table_name'.$i])  ;
                        }
                    }
                }else{
                    $arr_sheet_tables[] =  array('file_category'=>$file_category,'sheet'=>$sheet_name,'table_name'=>'')  ;
                }

            }


        }


 
            foreach ($arr_sheet_tables as  $row) {

              

                $sheet_data_tbl =  $this->getSheetDataTableName($row['file_category'], $row['sheet'], $row['table_name']);  
                
                //check table of sheet is existed

               

                if ($this->db->table_exists($sheet_data_tbl )) {
 
                    $msg_result[] = $sheet_data_tbl . " - has existed";
                   
                     
                } else {
                     
                   
                    //create table of sheet
                    //get def of sheet name
                    $sheet_def_tbl =  $this->getSheetDefTableName($row['file_category'], $row['sheet'],$row['table_name'] );
                    if ($this->db->table_exists($sheet_def_tbl )) {
                        
                        //get def of sheet  
                        $this->db->select('*');
                        $this->db->from($sheet_def_tbl);
                        if($sheet_def = $this->db->get()->result_array()){


                            
                            if($this->createSheetDataFromSheetDef($sheet_def_tbl, $sheet_data_tbl)){
                                $msg_result[] = "Create " .$sheet_data_tbl . " - from ".$sheet_def_tbl." successfully";
                            }else{
                                $msg_result[] = "Fail to create " .$sheet_data_tbl . " - ";
                            }

                            
                        }


                    }else{
                        $msg_result[] = " Fail to create " .$sheet_data_tbl . " - ".$sheet_def_tbl." is not existed";
                    }

 
                     
                }


            }
       



        return $msg_result;
    }

    public function getSheetDataTableName($file_category, $sheet,$table_name='')
    {
        if(!empty($table_name) && $table_name!='main') {
            return 'sheet_' . strtolower( strval($file_category)).'_'.strtolower( strval($sheet)).'_'.$table_name;
        }
        return 'sheet_' . strtolower( strval($file_category)).'_'.strtolower( strval($sheet));
  
    }

    public function getSheetDefTableName($file_category, $sheet,$table_name='')
    {
        if(!empty($table_name)) {
            return 'def_' . strtolower( strval($file_category)).'_'.strtolower( strval($sheet)).'_'.$table_name;
        }
        return 'def_' . strtolower( strval($file_category)).'_'.strtolower( strval($sheet));
  
    }


    public function createSheetDataFromSheetDef($sheet_def_tbl, $sheet_data_tbl)
    {
        //get def of sheet
        $this->db->select('*');
        $this->db->from($sheet_def_tbl);
        if($sheet_def = $this->db->get()->result_array()){

            //insert data to sheet data table
            $query_create_table_start = "CREATE TABLE IF NOT EXISTS  `".$sheet_data_tbl."` ";
            $col_defs = [];
            foreach ($sheet_def as $row) {
                $col_length = !empty($row['col_length']) ? ("(".$row['col_length'].")") : '';
                $col_defs[] = "`".$row['col_name']."` ".strtolower(strval($row['col_type'])).$col_length." COMMENT ".$this->db->escape($row['excel_header'])."";
            }

            if(!empty($col_defs)){
                $query_create_table = $query_create_table_start . " (id INT(11) NOT NULL AUTO_INCREMENT, year SMALLINT NOT NULL, month SMALLINT NOT NULL,file_upload_id INT(11), " . implode(',', $col_defs) . ", `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $this->db->query($query_create_table);


                //add index for year month file_upload_id
                $this->db->query("ALTER TABLE `".$sheet_data_tbl."` ADD INDEX idx_year_month(`year`,`month`);");
                $this->db->query("ALTER TABLE `".$sheet_data_tbl."` ADD INDEX (`file_upload_id`);");

                //other index
                foreach ($sheet_def as $row) {
                    if($row['is_index']==1){
                        $this->db->query("ALTER TABLE `".$sheet_data_tbl."` ADD INDEX (`".$row['col_name']."`);");
                    }
                }

                return 1;
              
            }
        }


        return 0;

    }


    public function getDefSheetStructorOfFileCategory($file_category,$single_sheet=null)
    {
        $arr = [];
        $this->db->select('*');
        $this->db->from('defexcel_sheet');
        $this->db->where('file_category',$file_category);
        if(!empty($single_sheet)){
            $this->db->where('sheet',$single_sheet);
        }
        $this->db->order_by('seq','ASC');
        if($res = $this->db->get()->result_array()){
            foreach ($res as $k => $row) {
                $sheet = $row['sheet'];
                $arr[$sheet] = $row;
                $arr[$sheet]['def_tables'] = [];


                if(($row['is_multi_table'])==1) {

                    if(!empty($row['json_subtable_prop'])){
                        $table_prop = json_decode($row['json_subtable_prop'],true);
                    }else{
                        $table_prop = [];
                    }
                    for ($i=1; $i <=4 ; $i++) { 
                        if(!empty($row['table_name'.$i])) {
                            $def_sheet_tbl = $this->getSheetDefTableName($file_category, $sheet, $row['table_name'.$i]);
                            $data_sheet_tbl = $this->getSheetDataTableName($file_category, $sheet, $row['table_name'.$i]);
                            $arr[$sheet]['def_tables']['table_name'.$i] = [];
                            $arr[$sheet]['def_tables']['table_name'.$i]['table_title'] =  $row['table_name'.$i];
                            $arr[$sheet]['def_tables']['table_name'.$i]['def_table_name'] =  $def_sheet_tbl;
                            $arr[$sheet]['def_tables']['table_name'.$i]['data_table_name'] =  $data_sheet_tbl;
                            $arr[$sheet]['def_tables']['table_name'.$i]['def_col'] = $this->getColDefFromSheetDefTable( $def_sheet_tbl);
                           
                            if(isset($table_prop[$i]['cond_skip_row'])){
                                $arr[$sheet]['def_tables']['table_name'.$i]['cond_skip_row'] = $table_prop[$i]['cond_skip_row'];
                            }else{
                                $arr[$sheet]['def_tables']['table_name'.$i]['cond_skip_row'] = "";
                            }
                            
                            if(isset($table_prop[$i]['cond_end_content'])){
                                $arr[$sheet]['def_tables']['table_name'.$i]['cond_end_content'] = $table_prop[$i]['cond_end_content'];
                            }else{
                                $arr[$sheet]['def_tables']['table_name'.$i]['cond_end_content'] = "";
                            }

                            if(isset($table_prop[$i]['is_pivot'])){
                                $arr[$sheet]['def_tables']['table_name'.$i]['is_pivot'] = $table_prop[$i]['is_pivot'];
                            }else{
                                $arr[$sheet]['def_tables']['table_name'.$i]['is_pivot'] = 0;
                            }

                            if(isset($table_prop[$i]['pivot_collect_mode'])){
                                $arr[$sheet]['def_tables']['table_name'.$i]['pivot_collect_mode'] = $table_prop[$i]['pivot_collect_mode'];
                            }else{
                                $arr[$sheet]['def_tables']['table_name'.$i]['pivot_collect_mode'] = null;
                            }                            
                            
                        }
                    }
                }else{
                    $def_sheet_tbl = $this->getSheetDefTableName($file_category, $sheet);
                    $data_sheet_tbl = $this->getSheetDataTableName($file_category, $sheet);
                    $arr[$sheet]['def_tables']['main'] = [];
                    $arr[$sheet]['def_tables']['main']['table_title'] = '';
                    $arr[$sheet]['def_tables']['main']['def_table_name'] =  $def_sheet_tbl;
                    $arr[$sheet]['def_tables']['main']['data_table_name'] =  $data_sheet_tbl;
                    $arr[$sheet]['def_tables']['main']['def_col'] = $this->getColDefFromSheetDefTable( $def_sheet_tbl);
                    $arr[$sheet]['def_tables']['main']['cond_skip_row'] =$row['cond_skip_row'];
                    $arr[$sheet]['def_tables']['main']['cond_end_content'] =$row['cond_end_content'];
                    $arr[$sheet]['def_tables']['main']['is_pivot'] =$row['is_pivot'];
                    $arr[$sheet]['def_tables']['main']['pivot_collect_mode'] =$row['pivot_collect_mode'];

                     
                }
               
            }
        }

        return $arr;
    }

    public function getSheetPatternOfFileCategory($file_category){

            $arr = [];
            $this->db->select('*');
            $this->db->from('defexcel_sheet');
            $this->db->where('file_category',$file_category);
            
            $this->db->order_by('seq','ASC');
            if($res = $this->db->get()->result_array()){
            
                return $res;
            }

            return $res;
    }

    
    public function getSuggestionOfFileUpload($file_category, $year, $month){
        $sheet_structors = $this->getSheetPatternOfFileCategory($file_category);

        $fc_info = $this->Cost_model->getFileCategoryInfo($file_category);

        if(empty($fc_info)) return '';

        $sheet_names = [];
        $str = '*** ไฟล์ '.$fc_info['file_display'].' ที่อัพโหลดต้องมีชีท ';
        foreach($sheet_structors as $val){

            $optional = "";
            if($val['is_optional']){
                $optional = " (ไม่จำเป็น)";
            }

            $sheetname_final = strReplaceYMDPattern($val['excel_sheet_name'],$year,$month);

            $sheet_names[] = '<span style="color: #204f89;">'.$sheetname_final.$optional.'</span>';
            
        }

        

        return $str . implode(', ',$sheet_names);

    }


    public function getColDefFromSheetDefTable($sheet_def_tbl)
    {
        $arr = [];
        $this->db->select('*');
        $this->db->from($sheet_def_tbl);
        if($res = $this->db->get()->result_array()){
            foreach ($res as $k => $row) {
                $row['real_excel_header'] = $row['excel_header'];
                $arr[] = $row;
               
            }
        }

        return $arr;
    }

    //Function to get db column name from excel column name
    //if input is array of excel column names, return array of db column names
    //if input is single excel column name, return single db column name
    public function getDB_colname_from_excel_col($file_category,$sheet,$table_name,$excel_col){

        $tbl = $this->getSheetDefTableName($file_category,$sheet,$table_name);

        if(!is_array($excel_col)){
            $arr_excel_col = [$excel_col];
        }else{
            $arr_excel_col = $excel_col;
        }
        $return_arr = [];
        foreach($arr_excel_col as $k=>$each_excel_col){
            $this->db->where('excel_col',$each_excel_col);
            if($res=$this->db->get($tbl)->row_array()){
                $return_arr[] = $res['col_name'];
            }else{
                $return_arr[] = '';
            }

        }

       

        if(!is_array($excel_col)){
            return $return_arr[0];
        }else{  
            return $return_arr;
        }
            

    }

    //Function to get excel column from db column name
    //if input is db column names array return array of excel column names,  
    //if input is single db column name, return single excel column names
    public function getExcel_col_from_DB_colname($file_category,$sheet,$table_name,$db_colname){

        $tbl = $this->getSheetDefTableName($file_category,$sheet,$table_name);

        if(!is_array($db_colname)){
            $arr_db_colname = [$db_colname];
        }else{
            $arr_db_colname = $db_colname;
        }
        $return_arr = [];
        foreach($arr_db_colname as $k=>$each_db_colname){
            $this->db->where('col_name',$each_db_colname);
            if($res=$this->db->get($tbl)->row_array()){
                $return_arr[] = $res['excel_col'];
            }else{
                $return_arr[] = '';
            }

        }

       

        if(!is_array($db_colname)){
            return $return_arr[0];
        }else{  
            return $return_arr;
        }
            

    }



}