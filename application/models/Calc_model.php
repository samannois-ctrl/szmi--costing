<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Calc_model extends CI_Model {

    public static $preload_table = [];

	public function __construct()

	{
		parent::__construct();

        $this->load->model('Def_model');
        $this->load->model('Util_model');

        $this->mm_to_inch_const = 25.43;

        $this->is_debug=0;
        $this->is_compare_old_new_calc=1;
        $this->html_compare_old_new_calc=[];
        $this->before_calc_table=[];

        $this->calc_step=0;
        $this->calc_step_info=[];

        $this->hash_table=[];
        $this->is_use_hash_table=1;

        $this->calc_col_dbcd = ['MV','MW','MX','MY','MZ','NA','NB','NC','ND','NE','NF','NG','NH','NI','NJ','NK','NL','NM','NN','NO','NP','NQ','NR','NS','NT','NU','NV','NW','NX','NY','NZ','OA','OB','OC','OD','OE','OF','OG','OH','OI','OJ','OK','OL','OM','ON','OO','OP','OQ'];
        
        $this->calc_col_front_dbcd = ['A','B','C','D','E','F','G','H','I','J','K','L'];
        $this->calc_col_edit_dbcd = ['MV','MW','MX','MY','MZ','NA','NB','NC','ND','NE','NF','NG','NH','NI','NJ','NK','NL','NM','NN','NO','NP','NQ','NR','NS','NT','NU','NV','NW','NX','NY','NZ','OA','OB','OC','OD','OE','OF','OG','OH','OI','OJ','OK','OL','OM','ON','OO','OP','OQ'];
        $this->calc_col_all_dbcd = array_merge($this->calc_col_front_dbcd, $this->calc_col_edit_dbcd);

        $this->dbcd_member_sheet = ['dbcd4','dbcd6','dbcd5','dbcd6lx','dbk6'];


        $this->calc_datacost_sheet_calc = ['dbcd4','dbcd6','dbcd5','dbcd6lx','dbk6','lowmargin','total','pivot'];
        $this->calc_datacost_sheet_machine = [   'dbcd4'=>['optional'=>0,'title'=>'CD4']
                                                ,'dbcd6'=>['optional'=>0,'title'=>'CD6']
                                                ,'dbcd5'=>['optional'=>0,'title'=>'CD5']
                                                ,'dbcd6lx'=>['optional'=>0,'title'=>'CD6LX']
                                                ,'dbk6'=>['optional'=>1,'title'=>'CD6 K6']
                                            ];
       
        


        $this->arr_manual_lock_data_of_sheet_id_col = [];        // table_name => id => arr of cols

        $this->is_calc_save_to_db = 1; // save result to db or not
        $this->arr_table_calc_save_result = []; // table_name => arr of calc result rows

        $this->excel_calc_consequence_map = []; // excel col => arr of excel col which depend on this col  
        $this->update_lock_value_list_after_save_manual_lock = [];     
        $this->effect_recalc_id_after_save_manual_lock = [];     
        
        $this->low_margin_limit = 6;



        

	}


    public function getExcelCalcConsequenceMap($sheet_type){


        $dbcd_map = [
            'NK'=>['NK','NL','NM'],    
        ];


        $this->excel_calc_consequence_map['DBCD'] = $dbcd_map;
        
        return $this->excel_calc_consequence_map[$sheet_type];
    }


    public function get_manual_lock_sheet_id_col_list($year,$month,$table_name)
    {   
        $arr = [];
        $res = $this->mlock_get_table_item_list($year,$month,$table_name);
        foreach ($res as $key => $row) {
            if(!isset($arr[$row['row_id']])){
                $arr[$row['row_id']] = [];
            }
            $arr[$row['row_id']][] = $row['col_name'];
        }
        $this->arr_manual_lock_data_of_sheet_id_col[$table_name] = $arr;
        return $arr;
    }

    public function checkIsManualLockWithPreLoadData($id,$col_name,$table_name){
        $is_lock = 0;
        $col_name = strtolower(strval($col_name));
        if(isset($this->arr_manual_lock_data_of_sheet_id_col[$table_name][$id])){
            if(in_array($col_name,$this->arr_manual_lock_data_of_sheet_id_col[$table_name][$id])){
                $is_lock = 1;
            }
        }
        return $is_lock;
    }

    public function sheetCalc_AfterDBCD($year,$month){

        //Calc Low Margin Total Pivot
        //Low margin
        $this->calcSheet_lowmargin($year,$month);

        //Total
        $this->calcSheet_datacost_total($year,$month);

        //Pivot
        $this->calcSheet_datacost_pivot($year,$month);


    }

    public function calcSheet_lowmargin($year,$month){
                //Low margin =============
        //select margin less than 6%
        //Load def col of lowmargin
        $low_maring_def_col_table =$this->Def_model->getSheetDefTableName('datacost', 'lowmargin','');
        $low_maring_data_table =$this->Def_model->getSheetDataTableName('datacost', 'lowmargin','');
        $map_lowmargin_dbcd = [];
        $map_lowmargin_dbcd['customer']     = ['excel_col'=>'K', 'col_name'=>'customer'];
        $map_lowmargin_dbcd['product_code'] = ['excel_col'=>'L', 'col_name'=>'product_code'];
        $map_lowmargin_dbcd['new_product_code']     = ['excel_col'=>'M', 'col_name'=>'new_product_code'];
        $map_lowmargin_dbcd['product_description']  = ['excel_col'=>'N', 'col_name'=>'product_description'];
        $map_lowmargin_dbcd['input_printing_sheet'] = ['excel_col'=>'O', 'col_name'=>'input_p'];
        $map_lowmargin_dbcd['fg']           = ['excel_col'=>'OO', 'col_name'=>'fg'];
        $map_lowmargin_dbcd['sale_price']   = ['excel_col'=>'OP', 'col_name'=>'sale_price'];
        $map_lowmargin_dbcd['margin_percent']   = ['excel_col'=>'OQ', 'col_name'=>'margin_percent'];
 
        //get def col
        if($def_col_res = $this->db->get($low_maring_def_col_table)->result_array()){

            $ins = [];
            //Loop sheet
            foreach($this->calc_datacost_sheet_machine as $sheet_name => $sheet_info){
                $sheet_title = $sheet_info['title'];
                //get table
                $table_sheet = $this->Def_model->getSheetDataTableName('datacost',$sheet_name,'');

                //get data sheet where lowmargin
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->where('margin_percent < 6');
                $this->db->where('margin_percent IS NOT NULL');

                if($cd_res = $this->db->get($table_sheet)->result_array()){

                    // echo $this->db->last_query();

                    foreach($cd_res as $idx => $cd_row){
                        $ins_row=[];
                        $ins_row['machinery'] = $sheet_title;
                        $ins_row['year'] = $year;
                        $ins_row['month'] = $month;

                        foreach ($map_lowmargin_dbcd  as $lm_col => $lm_map) {
                            $ins_row[$lm_col] = $cd_row[$lm_map['col_name']];
                        }

                        $ins[]=$ins_row;

                    }
                }

                //delete old
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->delete($low_maring_data_table);

                if(!empty($ins)){

                    
                    $this->db->insert_batch($low_maring_data_table, $ins);
                }


            }

        }

    }


    public function calcSheet_datacost_total($year,$month){
        //Low margin =============
        //select margin less than 6%
        //Load def col of lowmargin
        $datacosttotal_def_col_table =$this->Def_model->getSheetDefTableName('datacost', 'total','');
        $datacosttotal_data_table =$this->Def_model->getSheetDataTableName('datacost', 'total','');
        $map_datacosttotal_dbcd = [];
        $map_datacosttotal_dbcd['pt_no']     = ['excel_col'=>'A', 'col_name'=>'pt'];
        $map_datacosttotal_dbcd['customer']     = ['excel_col'=>'K', 'col_name'=>'customer'];
        $map_datacosttotal_dbcd['product_code'] = ['excel_col'=>'L', 'col_name'=>'product_code'];
        $map_datacosttotal_dbcd['new_product_code']     = ['excel_col'=>'M', 'col_name'=>'new_product_code'];
        $map_datacosttotal_dbcd['product_description']  = ['excel_col'=>'N', 'col_name'=>'product_description'];
        $map_datacosttotal_dbcd['diecut_no'] = ['excel_col'=>'FD', 'col_name'=>'diecut_no'];
        $map_datacosttotal_dbcd['diecut_date'] = ['excel_col'=>'FF', 'col_name'=>'diecut_date']; 
        $map_datacosttotal_dbcd['output_d'] = ['excel_col'=>'FL', 'col_name'=>'input_d_diecut'];
        $map_datacosttotal_dbcd['pack_date'] = ['excel_col'=>'KW', 'col_name'=>'pack_date'];
        $map_datacosttotal_dbcd['input_pk'] = ['excel_col'=>'LB', 'col_name'=>'input_pk_pack'];
        $map_datacosttotal_dbcd['output_pk'] = ['excel_col'=>'LC', 'col_name'=>'output_pk_pack'];
        $map_datacosttotal_dbcd['fg']           = ['excel_col'=>'OO', 'col_name'=>'fg'];
        $map_datacosttotal_dbcd['sale_price']   = ['excel_col'=>'OP', 'col_name'=>'sale_price'];
        $map_datacosttotal_dbcd['margin_percent']   = ['excel_col'=>'OQ', 'col_name'=>'margin_percent'];




        //get def col
        if($def_col_res = $this->db->get($datacosttotal_def_col_table)->result_array()){

            $ins = [];
            //Loop sheet
            foreach($this->calc_datacost_sheet_machine as $sheet_name => $sheet_info){
                $sheet_title = $sheet_info['title'];
                //get table
                $table_sheet = $this->Def_model->getSheetDataTableName('datacost',$sheet_name,'');

                //get data sheet where margin not null
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->where('margin_percent IS NOT NULL');

                if($cd_res = $this->db->get($table_sheet)->result_array()){

                    // echo $this->db->last_query();

                    foreach($cd_res as $idx => $cd_row){
                        $ins_row=[];
                        $ins_row['matchine'] = $sheet_title;
                        $ins_row['year'] = $year;
                        $ins_row['month'] = $month;

                        foreach ($map_datacosttotal_dbcd  as $lm_col => $lm_map) {
                            $ins_row[$lm_col] = $cd_row[$lm_map['col_name']];
                        }

                        $ins[]=$ins_row;

                    }
                }

                //delete old
                $this->db->where('year',$year);
                $this->db->where('month',$month);
                $this->db->delete($datacosttotal_data_table);

                if(!empty($ins)){

                    $this->db->insert_batch($datacosttotal_data_table, $ins);
                }


            }

        }

}



public function calcSheet_datacost_pivot($year,$month){
    //Pivot get average of FG =============
    //select margin less than 6%
    //Load def col of lowmargin
    $datacostpivot_def_col_table =$this->Def_model->getSheetDefTableName('datacost', 'pivot','');
    $datacostpivot_data_table =$this->Def_model->getSheetDataTableName('datacost', 'pivot','');
    $map_datacostpivot_total = [];
    $map_datacostpivot_total['pt_no']     = ['excel_col'=>'B', 'col_name'=>'pt_no'];
    $map_datacostpivot_total['product_code'] = ['excel_col'=>'D', 'col_name'=>'product_code'];
    $map_datacostpivot_total['new_product_code']     = ['excel_col'=>'E', 'col_name'=>'new_product_code'];
    $map_datacostpivot_total['product_description']  = ['excel_col'=>'F', 'col_name'=>'product_description'];
    $map_datacostpivot_total['diecut_no'] = ['excel_col'=>'G', 'col_name'=>'diecut_no'];
    $map_datacostpivot_total['output_d'] = ['excel_col'=>'I', 'col_name'=>'output_d'];
    $map_datacostpivot_total['pack_date'] = ['excel_col'=>'J', 'col_name'=>'pack_date'];
    $map_datacostpivot_total['output_pk'] = ['excel_col'=>'L', 'col_name'=>'output_pk'];
    $map_datacostpivot_total['matchine']           = ['excel_col'=>'A', 'col_name'=>'matchine'];


    $list_total_col = [];
    foreach ($map_datacostpivot_total as $idx => $col) {
        $list_total_col[]=$col['col_name'];
    }
    

        $total_table = $this->Def_model->getSheetDataTableName('datacost','total','');

        $ins = [];
        //Loop sheet group by
        $this->db->select(implode(',',$list_total_col));
        $this->db->select("AVG(fg) as avg_fg");
        $this->db->where('year',$year);
        $this->db->where('month',$month);

        $this->db->group_by($list_total_col);
        $this->db->order_by('pt_no');
        $this->db->order_by('product_code');
        $this->db->order_by('diecut_no desc');
        $this->db->order_by('output_d desc');
        $this->db->order_by('pack_date desc');
        
            //get data sheet where margin not null

            if($total_res = $this->db->get($total_table)->result_array()){

                // echo $this->db->last_query();

                foreach($total_res as $idx => $total_row){
                    $ins_row=[];
                    $ins_row['year'] = $year;
                    $ins_row['month'] = $month;
                    $ins_row['total'] = $total_row['avg_fg'];

                    foreach ($map_datacostpivot_total  as $pivot_col => $pivot_map) {
                        $ins_row[$pivot_col] = $total_row[$pivot_map['col_name']];
                    }

                    $ins[]=$ins_row;

                }
            }

            //delete old
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            $this->db->delete($datacostpivot_data_table);

            if(!empty($ins)){

                $this->db->insert_batch($datacostpivot_data_table, $ins);
            }

 

}



    public function sheetCalc_DBCD($sheet_name,$year,$month,$apply_origin_ids=[],$is_save_db=1){

        $this->is_calc_save_to_db = $is_save_db;

        // echo "<br>---- Start Calc Sheet DBCD : ".$sheet_name;
        // echo "<br>---- Year Month : ".$year."-".$month;
        // echo "<br>---- Apply Origin Ids : ";
        // print_r($apply_origin_ids);
        // echo "<br>---- is_save_db : ".$is_save_db;
        //exit;


        if($this->is_calc_save_to_db){
            //Preload $this->arr_table_calc_save_result
            $table_preload_name = $this->Def_model->getSheetDataTableName('datacost',$sheet_name,'');
            $this->db->select('*');
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            if(isset($apply_origin_ids) && !empty($apply_origin_ids)){
                $this->db->where_in('id',$apply_origin_ids);
            }
            if($res = $this->db->get($table_preload_name)->result_array()){
                $this->arr_table_calc_save_result[$table_preload_name] = convertArrayKeyId($res,'id');
            }
            
        }
        // echo "<br>---- Start Calc Sheet DBCD : ".$sheet_name;
        // print_r($apply_origin_ids);
        // exit;

        //if want compare table keep old table
        if($this->is_compare_old_new_calc){
            $table_old_name = $this->Def_model->getSheetDataTableName('datacost',$sheet_name,'');
            
            $this->before_calc_table[$table_old_name]=[];
            $calc_fld_dbcd = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$this->calc_col_dbcd);
            $this->db->select('id,product_code,'.implode(',',$calc_fld_dbcd));
            $this->db->where('year',$year);
            $this->db->where('month',$month);
            
            if($res = $this->db->get($table_old_name)->result_array()){
                foreach ($res as $key => $row) {
                    $this->before_calc_table[$table_old_name][$row['id']] = $row;
                }
            }

        }
        



        $filter = array('year'=>$year,'month'=>$month);
        if(!empty($apply_origin_id)){
            
            $filter['apply_origin_ids'] = $apply_origin_ids;
        }
        $origin_file_category = 'datacost';
        $mc_sheet = $this->Def_model->map_machine_sheet;


        //get manual lock data in sheet
        $this->get_manual_lock_sheet_id_col_list($year,$month,$this->Def_model->getSheetDataTableName('datacost',$sheet_name,''));


        // print_r($this->arr_manual_lock_data_of_sheet_id_col);
        // exit;



        //echo "<br>---- Start Calc Sheet DBCD : ";print_r($mc_sheet);exit;


        $calc_col=[];
        //Col:MV ==================
        //formula =+VLOOKUP(L506,'D:\ning\Trimming\2025\05.MAY\[Up date of DB &Trimming_  MAY''25_trimming - ALLOCAST COST.xlsx]E-FLUTE'!B$2:C$173,2,FALSE)
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = 'eflute';
        $dest['table_name'] = 'eflute';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['B']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['search_excel_col']);
        $dest['return_excel_col'] = 'C'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        
        $origin=[];
        $origin['calc_excel_col'] = 'MV';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="MV"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //

        // print_r($dest);
        // print_r($origin);
 

        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);

        //Col ==================

        //Col:MW - SKIP
        //Col:MX ===============
        //VLOOKUP(L506,'D:\ning\Trimming\2025\05.MAY\[Up date of DB &Trimming_  MAY''25_trimming - ALLOCAST COST.xlsx]E-FLUTE'!B$2:F$173,5,FALSE) - Vlookup จากชีท e-flute ค่ะ คอลัม f ค่ะ
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = 'eflute';
        $dest['table_name'] = 'eflute';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['B']; //col which search
        $dest['search_fld_name']=$this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['search_excel_col']);
        $dest['return_excel_col'] = 'F'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        
        $origin=[];
        $origin['calc_excel_col'] = 'MX';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =['L']; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="MX"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //

        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);
        
        //Col:MY ===============
        //Col:MZ - หาความเชื่อมโยง สูตร ไม่ได้
        //Col:NA - หาความเชื่อมโยง สูตร ไม่ได้
        //Col:NB - หาความเชื่อมโยง สูตร ไม่ได้
        //Col:NB - ไม่ใช้
        //Col:NC - ไม่ใช้
        //Col:ND - ไม่ใช้
        //Col:NE - ไม่ใช้
        //Col:NF - ไม่ใช้  
        //Col:NG - ไม่ใช้
        //Col:NH - ไม่ใช้
        //Col:NI - ไม่ใช้  
        //Col:NJ - ไม่ใช้ 
 

        // //Col:NK ==============
        // //formula =+VLOOKUP(L6,'D:\ning\Trimming\2025\05.MAY\[Up date of DB &Trimming_  MAY''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:F$528,6,FALSE)
        
        // $dest=[];
        // $dest['file_category'] = 'dbtrim';
        // $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        // $dest['table_name'] = '';
        // $dest['filter'] = $filter;
        // //$dest['search_excel_col']= ['F','A']; //col which search
        // $dest['search_excel_col']= ['A','L']; //col which search
        // $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        // $dest['return_excel_col'] = ''; //col which return value
        // $dest['return_fld_name'] = 'code';//$this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        // $dest['step_search_fld_name'] = $dest['search_fld_name']; //set step search rule if must have first ctriteria then search with second critiria

        // //print_r($dest['step_search_fld_name'] );
        
        // $origin=[];
        // $origin['calc_excel_col'] = 'NK';
        // $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        // $origin['calc_method'] = 'vlookup';
        // $origin['file_category'] = $origin_file_category;
        // $origin['sheet'] = $sheet_name;
        // $origin['table_name'] = '';
        // $origin['filter'] = $filter;
        // $origin['critiria_excel_col'] =["L",'E']; //col which critiria col
        // //$origin['critiria_excel_col'] =["L"]; //col which critiria col
        // $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        // $origin['result_excel_col'] ="NK"; //col which save update result
        // $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        // $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);         

         //Col:NK ==============
        //formula =+VLOOKUP(L6,'D:\ning\Trimming\2025\05.MAY\[Up date of DB &Trimming_  MAY''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:F$528,6,FALSE)
        
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['F','C','A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = ''; //col which return value
        $dest['return_fld_name'] = 'code';//$this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        $dest['step_search_fld_name'] = $dest['search_fld_name']; //set step search rule if must have first ctriteria then search with second critiria

        //print_r($dest['step_search_fld_name'] );
        
        $origin=[];
        $origin['calc_excel_col'] = 'NK';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L",'B','E']; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NK"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);   

        //Col:NL =============
        //=+VLOOKUP(L6,'D:\ning\Trimming\2025\08.AUG\[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:I$457,9,FALSE)
        // $dest=[];
        // $dest['file_category'] = 'dbtrim';
        // $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        // $dest['table_name'] = '';
        // $dest['filter'] = $filter;
        // //$dest['search_excel_col']= ['F','A']; //col which search
        // $dest['search_excel_col']= ['A']; //col which search
        // $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        // $dest['return_excel_col'] = 'I'; //col which return value
        // $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        // $origin=[];
        // $origin['calc_excel_col'] = 'NL';
        // $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        // $origin['calc_method'] = 'vlookup';
        // $origin['file_category'] = $origin_file_category;
        // $origin['sheet'] = $sheet_name;
        // $origin['table_name'] = '';
        // $origin['filter'] = $filter;
        // //$origin['critiria_excel_col'] =["L",'E']; //col which critiria col
        // $origin['critiria_excel_col'] =["L"]; //col which critiria col
        // $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        // $origin['result_excel_col'] ="NL"; //col which save update result
        // $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['F','C','A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = ''; //col which return value
        $dest['return_fld_name'] = 'order_paper_size_gsm_actual';//$this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        $dest['step_search_fld_name'] = $dest['search_fld_name']; //set step search rule if must have first ctriteria then search with second critiria

        //print_r($dest['step_search_fld_name'] );
        
        $origin=[];
        $origin['calc_excel_col'] = 'NL';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L",'B','E']; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NL"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  




        //Col:NM =============
        //=+VLOOKUP(L6,'D:\ning\Trimming\2025\08.AUG\[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:H$457,8,FALSE)
        // $dest=[];
        // $dest['file_category'] = 'dbtrim';
        // $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        // $dest['table_name'] = '';
        // $dest['filter'] = $filter;
        // //$dest['search_excel_col']= ['F','A']; //col which search
        // $dest['search_excel_col']= ['A']; //col which search
        // $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        // $dest['return_excel_col'] = 'H'; //col which return value
        // $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        // $origin=[];
        // $origin['calc_excel_col'] = 'NM';
        // $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        // $origin['calc_method'] = 'vlookup';
        // $origin['file_category'] = $origin_file_category;
        // $origin['sheet'] = $sheet_name;
        // $origin['table_name'] = '';
        // $origin['filter'] = $filter;
        // //$origin['critiria_excel_col'] =["L",'E']; //col which critiria col
        // $origin['critiria_excel_col'] =["L"]; //col which critiria col
        // $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        // $origin['result_excel_col'] ="NM"; //col which save update result
        // $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['F','C','A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = ''; //col which return value
        $dest['return_fld_name'] = 'sheet_roll';//$this->Def_model->getDB_colname_from_excel_col('dbtrim','eflute','eflute',$dest['return_excel_col']);
        $dest['step_search_fld_name'] = $dest['search_fld_name']; //set step search rule if must have first ctriteria then search with second critiria

        //print_r($dest['step_search_fld_name'] );
        
        $origin=[];
        $origin['calc_excel_col'] = 'NM';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L",'B','E']; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NM"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //


        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  



        //Col:NN =============
        //=+VLOOKUP(L8,'D:\ning\Trimming\2025\08.AUG\[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:M$457,13,FALSE)
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['A','F']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = 'M'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'NN';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        //$origin['critiria_excel_col'] =["L",'E']; //col which critiria col
        $origin['critiria_excel_col'] =["L","NK"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NN"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  





        //Col:NO =============
        //=+VLOOKUP(NK236,'D:\ning\Trimming\2025\08.AUG\[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]Average All Roll Form'!B$6:D$287,3,FALSE)
        //USE for unit_rm = 'ROLL' to update formaula but small_sheet_roll_sheet=null for unit_rm = 'SHEET'
        //Problem บาง row ไม่ได้ใช้สูตรแต่แบบ manual ถึง column อื่นที่อ้างอิง ค่าในนี้ NY, NZ, OA
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = 'average'; // get sheet average
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        //$dest['search_excel_col']= ['F','A']; //col which search
        $dest['search_excel_col']= ['B']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = 'D'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'NO';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        //$origin['critiria_excel_col'] =["L",'E']; //col which critiria col
        $origin['critiria_excel_col'] =["NK"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NO"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
        $origin['apply_origin_where_qstr'] = " UPPER(unit_rm) = 'ROLL' "; //apply origin where filter  
        $origin['post_sql_update'] = ['where'=>" UPPER(unit_rm) = 'SHEET' ",'arr_updates'=>['small_sheet_roll_sheet'=>null]]; //manual update post
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest); 
        
        //Col:NP,NQ =============
        //Extract size of paper from col NL
        $origin=[];
        $origin['calc_excel_col'] = 'NP,NQ';
        $origin['calc_fld_name'] = 'NP,NQ';
        $origin['calc_method'] = 'order_paper_size_formula';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['from_excel_col'] ="NL"; //col which critiria col
        $origin['from_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['from_excel_col']);
        $origin['to_excel_col_size_1'] ="NP"; //col which save update result
        $origin['to_fld_name_size_1'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col_size_1']); 
        $origin['to_excel_col_size_2'] ="NQ"; //col which save update result
        $origin['to_fld_name_size_2'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col_size_2']);
        $origin['pattern_type'] = 'inch'; //pattern type to extract size
        $origin['apply_origin_where_qstr'] = " UPPER(unit_rm) = 'SHEET' "; //apply origin where filter  
        $origin['post_sql_update'] = ['where'=>" UPPER(unit_rm) = 'ROLL' ",'arr_updates'=>[$origin['to_fld_name_size_1']=>null,$origin['to_fld_name_size_2']=>null]]; //manual update post


        $this->process_calc_update_order_paper_size_formula($year, $month,$origin); 

        //Col:NR =============
        //=+VLOOKUP(L8,'D:\ning\Trimming\2025\08.AUG\[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:J$457,10,FALSE)
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = 'J'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'NR';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="NR"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  



        //Col:NS,NT =============
        //Extract size of paper from col NR
        $origin=[];
        $origin['calc_excel_col'] = 'NS,NT';
        $origin['calc_fld_name'] = 'NS,NT';
        $origin['calc_method'] = 'order_paper_size_formula';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['from_excel_col'] ="NR"; //col which critiria col
        $origin['from_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['from_excel_col']);
        $origin['to_excel_col_size_1'] ="NS"; //col which save update result
        $origin['to_fld_name_size_1'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col_size_1']); 
        $origin['to_excel_col_size_2'] ="NT"; //col which save update result
        $origin['to_fld_name_size_2'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col_size_2']);
        $origin['pattern_type'] = 'mm'; //pattern type to extract size

        $this->process_calc_update_order_paper_size_formula($year, $month,$origin); 



        //Col:NU =============
        //Convert size inch to mm from col NP
        $origin['calc_excel_col'] = 'NU';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'convert_size';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['from_excel_col'] ="NP"; //col which critiria col
        $origin['from_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['from_excel_col']);
        $origin['to_excel_col'] ="NU"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_convert_size($year, $month,$origin,'inch_to_mm'); 


        //Col:NV =============
        //Convert size inch to mm from col NQ
        $origin['calc_excel_col'] = 'NV';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'convert_size';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['from_excel_col'] ="NQ"; //col which critiria col
        $origin['from_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['from_excel_col']);
        $origin['to_excel_col'] ="NV"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_convert_size($year, $month,$origin,'inch_to_mm');
        
        
        //Col:NW =============
        //Ration size NV / NT
        $origin['calc_excel_col'] = 'NW';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'simple_math';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['num1_excel_col'] ="NV"; //col which critiria col
        $origin['num1_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['num1_excel_col']);
        $origin['num2_excel_col'] ="NT"; //col which critiria col
        $origin['num2_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['num2_excel_col']);
        $origin['to_excel_col'] ="NW"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_simple_math($year, $month,$origin,'ratio');

        //Col:NX หาความเชื่อมโยง สูตร ไม่ได้

        //Col:NY ==================
        //sheet to pcs
        //if ROLL use colNO
        //if SHEET use Rounddown(NW)
        $origin['calc_excel_col'] = 'NY';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'sheet_roll_to_pcs';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['unit_excel_col'] ="NM"; //col which critiria col
        $origin['unit_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['unit_excel_col']);
        $origin['roll_to_sheet_excel_col'] ="NO"; //col 
        $origin['roll_to_sheet_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['roll_to_sheet_excel_col']);
        $origin['pcs_excel_col'] ="NW"; //col 
        $origin['pcs_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['pcs_excel_col']);
        $origin['to_excel_col'] ="NY"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_sheet_roll_to_pcs($year, $month,$origin);



        //Col:NZ =============
        //Ration size NN / NY
        $origin['calc_excel_col'] = 'NZ';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'simple_math';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['num1_excel_col'] ="NN"; //col which critiria col
        $origin['num1_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['num1_excel_col']);
        $origin['num2_excel_col'] ="NY"; //col which critiria col
        $origin['num2_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['num2_excel_col']);
        $origin['to_excel_col'] ="NZ"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_simple_math($year, $month,$origin,'ratio');

        //Col: OA =============
        //=+(NB6+NZ6)/GI6  use sum and divide
        $origin['calc_excel_col'] = 'OA';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'sum_div';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['sum_excel_col'] =["NB","NZ"]; //col which critiria col
        $origin['sum_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['sum_excel_col']);
        $origin['div_excel_col'] ="GI"; //col which critiria col
        $origin['div_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['div_excel_col']);
        $origin['to_excel_col'] ="OA"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_sum_div($year, $month,$origin);

        //Col: OB =============
        //cal for e-flute only by check col MV 
        //=1.6*NS660/$NU$3*NT660/$NU$3/1000/GI660
        $origin=[];
        $origin['calc_excel_col'] = 'OB';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'lamination';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['condition_excel_col'] ="MV"; //col which critiria col
        $origin['condition_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['condition_excel_col']);

        $origin['arg1_excel_col'] ="NS"; //col which critiria col
        $origin['arg1_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg1_excel_col']);
        $origin['arg2_excel_col'] ="NT"; //col which critiria col
        $origin['arg2_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg2_excel_col']);
        $origin['div_excel_col'] ="GI"; //col which critiria col
        $origin['div_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['div_excel_col']);
        
        $origin['to_excel_col'] ="OB"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 
        $origin['apply_origin_where_qstr'] = "(".$origin['condition_fld_name']." IS NOT NULL AND ".$origin['condition_fld_name']." <> '' )"; //apply origin where filter  
        $origin['post_sql_update'] = ['where'=>" (".$origin['condition_fld_name']." IS NULL OR ".$origin['condition_fld_name']." = '') ",'arr_updates'=>[$origin['to_fld_name']=>null]]; //manual update post


        $this->process_calc_update_lamination($year, $month,$origin);





        //Col: OC =============

        //=+VLOOKUP(L678,'D:/ning/Trimming/2025/08.AUG/[Up date of DB &Trimming_  AUG''25_trimming - ALLOCAST COST.xlsx]PV #CD4'!A$5:K$457,11,FALSE)
        $dest=[];
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = 'K'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'OC';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup_formula';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["L"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="OC"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  


        //Col: OD =============
        //=LEFT(OC6,1)*0.09
        $origin['calc_excel_col'] = 'OD';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'printing_cost_from_color';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['arg1_excel_col'] ="OC"; //col which critiria col
        $origin['arg1_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg1_excel_col']);
        $origin['to_excel_col'] ="OD"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_printing_cost_from_color($year, $month,$origin);

        //Col: OE , OF,  OG,  OH =============
        //สูตรการคำนวณ cost ในไฟล์ DATE of …
        // OE		        OF		        OG		       OH
        // Coating cost  	 calendering  	 OPP/UV 	 Die cut  
        // $NU$3 = 25.43
        // 1.เช็คคอลัมน์ L – Product code เอาค่าไปเทียบกับ ไฟล์ Trimming PV#CD.. คอลัมน์ FG Resource code (new)
        //     ถ้ามี ค่า matt varnish / vanish ให้ เข้าช่อง Coating cost  สูตร 0.3/GL{row}
        //     ถ้ามี ค่า calendar ให้ เข้าช่อง Calendering    =2*NS1153/$NU$3*NT1153/$NU$3/1000/GI1153
        //     ถ้ามี ค่า uv เข้าช่อง OPP/UV     สูตร =1.663*NS163/$NU$3*NT163/$NU$3/1000/GI163
        //     ถ้ามี ค่า gloss  เข้าช่อง OPP/UV     สูตร =3.5*NS616/$NU$3*NT616/$NU$3/1000/GI616
        //     ถ้ามี ค่า matt  เข้าช่อง OPP/UV     สูตร =4*NS836/$NU$3*NT836/$NU$3/1000/GI836
        // ถ้าไม่มีอะไรพิเศษ ใส่  Coating cost  = 0.06
        // 2.ถ้า ช่อง B Job เป็น Die-cut ก็ให้ ข้ามไปใส่ช่อง Diecut อย่างเดียว ไม่ต้องใส่ทั้งสามช่อง

        //problem FDBSTGT01321-02-M มี MATT OPP แต่ในexcel ไม่คิดสูตร ตาม matt
        //        FDBSTGT01320-02-M มี MATT OPP คิดสูตร ตาม matt ปกติ
        //          FDBCHL00008-00 ใส่ค่า 0 แทนที่จะเป็น 0.6
        //  FDBSTGT01212-00-M ค่า ใน GI = #DIV/0! ทำให้คำนวณไม่ได้ และเป็นหลายแถว โดยเฉพาะแถวที่อยู๋ใน Group 2 ซึ่งใน excel ชีท DB#CD จะมี row 2 Group คือ1กับ2 Group 2 จะซ่อนไว้ และไม่ถูกรวมมาในชีท Total เลยไม่แน่ในว่าพวก Row Group 2 ได้ใช้งานมั้ย ต้อง import มาด้วยมั้ย ต้องให้คำนวณด้วย เพราะใน excel ผลการคำนวณ จะ คอลัมน์ FG Saleprice margin จะเป็น #DIV/0! และ #N/A เยอะเลย

        
        $dest=[];  
        $dest['file_category'] = 'dbtrim';
        $dest['sheet'] = getValueFromIdxArrayByKey($mc_sheet ,'datacost',$sheet_name,'dbtrim_pv'); // get sheet cd4 in dbtrim which map with dbcd4 in datacost
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= 'A'; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['chk_excel_col'] = 'B'; //col which check pattern value
        $dest['chk_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['chk_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'OE,OF, OG,OH';
        $origin['calc_fld_name'] = 'OE,OF, OG,OH';
        $origin['calc_method'] = 'subcosting';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] ="L"; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['jobno_excel_col'] ="B"; //col which critiria col
        $origin['jobno_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['jobno_excel_col']); 
        $origin['result_coating_excel_col'] ="OE"; //col which save update result
        $origin['result_coating_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_coating_excel_col']); //
        $origin['result_calendering_excel_col'] ="OF"; //col which save update result
        $origin['result_calendering_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_calendering_excel_col']); //
        $origin['result_oppuv_excel_col'] ="OG"; //col which save update result
        $origin['result_oppuv_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_oppuv_excel_col']); //
        $origin['result_diecut_excel_col'] ="OH"; //col which save update result
        $origin['result_diecut_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_diecut_excel_col']); //

        $origin['arg_up_excel_col'] ="GI"; //col which save update result
        $origin['arg_up_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg_up_excel_col']); //
        $origin['arg_l_excel_col'] ="NS"; //col which save update result
        $origin['arg_l_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg_l_excel_col']); //
        $origin['arg_w_excel_col'] ="NT"; //col which save update result
        $origin['arg_w_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg_w_excel_col']); //


        $this->process_calc_update_subcosting($year, $month,$origin,$dest);  


        //Col: OI ===========
        //Manual Gluing
        //การติดกาวมือ ถุงกระดาษ 3 บาท สินค้ากระดาษลอน (e-flute) 1 บาท เราต้องดูสินค้าเอง ทาง planning ไม่ได้ระบุรายละเอียดมาให้
        //สรุปยังไม่มีสูตร

        //Col: OJ
        //m/c gluing
        //นอกเหนือจากการติดกาวมือจะเป็นการติดกาวเครื่องหมดเลยค่ะ
        //ถ้าค่า ใน OI ไม่มี ก็ให้ใส่ 0.8
        //problem : มีบาง row ที่ไม่มีทั้ง Manual Gluing และ m/c gluing
        $origin['calc_excel_col'] = 'OJ';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'mc_gluing';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['arg1_excel_col'] ="OI"; //col which critiria col
        $origin['arg1_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['arg1_excel_col']);
        $origin['to_excel_col'] ="OJ"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_mc_gluing($year, $month,$origin);


        //Col: OK ===========
        //packing
        //const 0.05
        $origin['calc_excel_col'] = 'OK';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'update_val';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['to_excel_col'] ="OK"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 
        $this->process_calc_update_val($year, $month,$origin,0.05);


        //Col: OL ============
        //String (Paper bag)
        //ใช้เฉพาะสินค้าถุงกระดาษ ราคาจะไม่เท่ากัน แล้วแต่ว่าจะใช้แบบไหน ทางบัญชีเป็นคนดูราคาเอง
        //ไม่มีสูตร

        //Col: OM ============
        //Sticker cost
        //ไม่ค่อยได้มีสินค้าที่ติดหน้าต่างแล้วค่ะ จะใช้การเคลือบ opp แทน
        //ไม่มีสูตร

        //Col: ON ============
        //HOT STAMPING
        //นานๆๆจะมีสินค้าที่ใช้ hot stamping ซึ่งทางบัญชีจะคำนวณราคาอีกทีเมื่อมีสินค้า
        //ไม่มีสูตร

        //Col: OO ============
        //FG
        //=+OA6+OB6+OD6+OE6+OF6+OG6+OH6+OI6+OJ6+OK6+OL6+OM6+ON6
        $origin['calc_excel_col'] = 'OO';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'sum_simple';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['sum_excel_col'] =['OA','OB','OD','OE','OF','OG','OH','OI','OJ','OK','OL','OM','ON']; //col which sum
        $origin['sum_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['sum_excel_col']);
        $origin['to_excel_col'] ="OO"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); 

        $this->process_calc_update_sum_simple($year, $month,$origin);


        //Col: OP =============
        //Sale price
        //=+VLOOKUP(M7,'D:/ning/[NRV 2025.xlsx]PV #0825'!A$6:C$737,3,FALSE)
        $dest=[];
        $dest['file_category'] = 'nrv';
        $dest['sheet'] = 'pv';
        $dest['table_name'] = '';
        $dest['filter'] = $filter;
        $dest['search_excel_col']= ['A']; //col which search
        $dest['search_fld_name']= $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['search_excel_col']);
        $dest['return_excel_col'] = 'C'; //col which return value
        $dest['return_fld_name'] = $this->Def_model->getDB_colname_from_excel_col($dest['file_category'],$dest['sheet'],$dest['table_name'],$dest['return_excel_col']);
 
        
        $origin=[];
        $origin['calc_excel_col'] = 'OP';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'vlookup_formula';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;
        $origin['critiria_excel_col'] =["M"]; //col which critiria col
        $origin['critiria_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['critiria_excel_col']); 
        $origin['result_excel_col'] ="OP"; //col which save update result
        $origin['result_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['result_excel_col']); //
        $origin['critiria_filter_method'] = 'remove_after_space'; //way to get critiria in this case Product code no space
  
        $this->process_calc_update_vlookup_formula($year, $month,$origin,$dest);  


        //Col: OQ ============
        //%Margin
        //=+(OP6-OO6)/OP6
        
        $origin['calc_excel_col'] = 'OQ';
        $origin['calc_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['calc_excel_col']);
        $origin['calc_method'] = 'percent_margin';
        $origin['file_category'] = $origin_file_category;
        $origin['sheet'] = $sheet_name;
        $origin['table_name'] = '';
        $origin['filter'] = $filter;

        $origin['sale_excel_col'] ='OP'; //col which sale
        $origin['sale_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['sale_excel_col']);
        $origin['cost_excel_col'] ="OO"; //col which cost
        $origin['cost_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['cost_excel_col']); 
        $origin['to_excel_col'] ="OQ"; //col which save update result
        $origin['to_fld_name'] = $this->Def_model->getDB_colname_from_excel_col('datacost',$sheet_name,'',$origin['to_excel_col']); //

        
        $this->process_calc_update_percent_margin($year, $month,$origin);


        //calc Low margin / Total/ Pivot
        $this->Calc_model->sheetCalc_AfterDBCD($year, $month);


        //compare
        if($this->is_compare_old_new_calc){
            
        $this->html_compare_old_new_calc[$sheet_name] = $this->displayCompareValueTableAfterCalc($table_old_name,$year,$month,$origin_file_category,$sheet_name,'',$calc_fld_dbcd);
        
        }

        //bench mark
        if($this->is_debug){
            for ($i=1; $i <= $this->calc_step; $i++) { 
                echo "\n<br> STEP: ".$i;
                echo "\n<br> info: ".$this->calc_step_info[$i]['calc_excel_col'].'-'.$this->calc_step_info[$i]['calc_method'];
                echo "\n<br> time: ".$this->benchmark->elapsed_time('step_start_'.$i, 'step_end_'.$i);
            }

        }
        


    } 


    
    public function process_calc_update_percent_margin($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,product_code');
        $this->db->select($origin['sale_fld_name']);
        $this->db->select($origin['cost_fld_name']);
        $this->db->select($origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){
            $n=0;
            foreach ($ori_res as $k => $row) {

                $sale = floatval($row[$origin['sale_fld_name']]);
                $cost = floatval($row[$origin['cost_fld_name']]);
                $percent = null;
                if($sale==0){
                    $percent = null;
                }else{
                    $percent = (($sale - $cost) / $sale)*100;
                }
             
                $res_val = $percent;
                 
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }

    public function process_calc_update_sum_simple($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,product_code,'.implode(',',$origin['sum_fld_name']));
        $this->db->select($origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){
            $n=0;
            foreach ($ori_res as $k => $row) {

                
                $arr_sum_values = [];
                foreach($origin['sum_fld_name'] as $sum_fld){
                    if(has_val($row[$sum_fld]) && is_numeric($row[$sum_fld])){
                        $arr_sum_values[]= floatval($row[$sum_fld]);
                    }
                }
                if(count($arr_sum_values)>0){
                    $sum_value = array_sum($arr_sum_values);
                }else{  
                    $sum_value = 0;
                }

                $res_val = $sum_value;
                // echo $row['product_code'];
                // print_r($arr_sum_values);

              

                if(round(floatval($row[$origin['to_fld_name']]),2) != round(floatval($res_val),2) && has_val($row[$origin['to_fld_name']])){
                    // echo "\n<br>".($n++)."-id: ".$row['id'].':'.$row['product_code']." : sum cols ";
                    // foreach($origin['sum_fld_name'] as $sum_fld){
                    //     echo " , ".$sum_fld." : ".$row[$sum_fld];                
                    // }
                    // echo "\n<br>  => old : ".$row[$origin['to_fld_name']];
                    // echo "  => new : ".$res_val;
                }


                // update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]); 
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }



    public function process_calc_update_val($year, $month,$origin,$val){

        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id');
        $this->db->select($origin['to_fld_name']);

        
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        $res_val = $val;

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

           

                // //check
                // if(floatval($row[$origin['to_fld_name']]) == floatval($res_val)){
                //     echo "<br>id: ".$row['id'];
                //     echo "  => old : ".$row[$origin['to_fld_name']];         
                //     echo "  => new : ".$res_val;         
                // }
                
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }



    }


    public function process_calc_update_mc_gluing($year, $month,$origin){

        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,product_code,'.$origin['arg1_fld_name']);
        $this->db->select($origin['to_fld_name']);

        
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $res_val = 0;
                if(!has_val($row[$origin['arg1_fld_name']]) || $row[$origin['arg1_fld_name']]==0){
                    $res_val = 0.08;
                }

                // //check
                // if(floatval($row[$origin['to_fld_name']]) != floatval($res_val)){
                //     echo "<br>id: ".$row['id']."-".$row['product_code']." : arg1 : ".$row[$origin['arg1_fld_name']];
                //     echo "  => old : ".$row[$origin['to_fld_name']];         
                //     echo "  => new : ".$res_val;         
                // }
                
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]);

                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }



    }

    public function process_calc_update_subcosting($year, $month,$origin,$dest)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }
        

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);
        $dest_table_sheet = $this->Def_model->getSheetDataTableName($dest['file_category'],$dest['sheet'],$dest['table_name']);

        //load origin data join dest data
        $this->db->select('o.id,o.'.$origin['critiria_fld_name'].',o.'.$origin['jobno_fld_name'].',d.'.$dest['chk_fld_name'].',d.'.$dest['search_fld_name']);
        $this->db->select('o.'.$origin['result_coating_fld_name'].',o.'.$origin['result_calendering_fld_name'].',o.'.$origin['result_oppuv_fld_name'].',o.'.$origin['result_diecut_fld_name']);
        $this->db->select('o.'.$origin['arg_up_fld_name']);
        $this->db->select('o.'.$origin['arg_l_fld_name'].',o.'.$origin['arg_w_fld_name']);
        
        $this->db->from($origin_table_sheet.' as o');
        $this->db->join($dest_table_sheet.' as d', 'o.'.$origin['critiria_fld_name'].' = d.'.$dest['search_fld_name'], 'left');
        $this->db->where('o.year',$origin['filter']['year']);
        $this->db->where('o.month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('o.id',$origin['filter']['apply_origin_ids']);
        }

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {
                $is_coat_special = 0;
                $res_coating = null;
                $res_calendering = null;
                $res_oppuv = null;
                $res_diecut = null;

                $jobno = strtoupper(trim(strval($row[$origin['jobno_fld_name']])));
                $jobno = str_replace(' ', '', $jobno);
                $jobno = str_replace('-', '', $jobno);
                $chk_val = strtoupper(trim(strval($row[$dest['chk_fld_name']])));
                $chk_val = str_replace('  ', ' ', $chk_val);
                $chk_val = str_replace('  ', ' ', $chk_val);

                //remove product code 
                $chk_val = trim(str_replace(strtoupper(strval($row[$dest['search_fld_name']])), '', $chk_val));



                //cal res die cut use every row
                $row[$origin['arg_up_fld_name']] = trim(strval($row[$origin['arg_up_fld_name']]));
                if(!has_val($row[$origin['arg_up_fld_name']]) ||  !is_numeric($row[$origin['arg_up_fld_name']]) || $row[$origin['arg_up_fld_name']] == 0){
                    $res_diecut = null;
                }else{
                    $res_diecut = 0.3/floatval($row[$origin['arg_up_fld_name']]);
                    //$res_diecut = round($res_diecut,6);
                }
                
                //varnish = ['VANIS','VARNIS'];
                //caledering 'CALEN','CARLEN'
                //uv = ['UV','GLOSS','MATT'];
                //gloss = ['GLOSS'];
                //matt = ['MATT']; NOT VANIS VARNIS


                if($jobno!='DIECUT'){

                    //UV
                    if(strpos($chk_val,'UV') !== false ){
                        $a1 = $row[$origin['arg_l_fld_name']];
                        $a2 = $row[$origin['arg_w_fld_name']];
                        $div = $row[$origin['arg_up_fld_name']];
                        $mm_in = $this->mm_to_inch_const;
                        //UV     สูตร =1.663*NS163/$NU$3*NT163/$NU$3/1000/GI163
                        if( has_val($a1) && is_numeric($a1) && has_val($a2) && is_numeric($a2) && has_val($div) && is_numeric($div) && floatval($div)!=0){
                            $a1 = floatval($a1);
                            $a2 = floatval($a2);
                            $div = floatval($div); 
                            $res_oppuv =  1.663 * $a1 / $mm_in * $a2 / $mm_in  / 1000 / $div; 
                            //$res_oppuv = round($res_oppuv,6);
                                 
                        }
                        $is_coat_special=1;
                    }


                    //GLOSS
                    if(strpos($chk_val,'GLOSS') !== false){
                        //gloss
                        $a1 = $row[$origin['arg_l_fld_name']];//NS
                        $a2 = $row[$origin['arg_w_fld_name']];//NT
                        $div = $row[$origin['arg_up_fld_name']];//GI
                        $mm_in = $this->mm_to_inch_const;
                        //3.5*NS616/$NU$3*NT616/$NU$3/1000/GI616

                        if( has_val($a1) && is_numeric($a1) && has_val($a2) && is_numeric($a2) && has_val($div) && is_numeric($div) && floatval($div)!=0){
                           $a1 = floatval($a1);
                           $a2 = floatval($a2);
                           $div = floatval($div); 
                           $res_oppuv =  3.5 * $a1 / $mm_in * $a2 / $mm_in  / 1000 / $div; 
                           //$res_oppuv = round($res_oppuv,6);     
                           
                        }
                        $is_coat_special=1;
                    }


                    
                    if(strpos($chk_val,'CALEN') !== false || strpos($chk_val,'CARLEN') !== false){
                        $a1 = $row[$origin['arg_l_fld_name']];
                        $a2 = $row[$origin['arg_w_fld_name']];
                        $div = $row[$origin['arg_up_fld_name']];
                        $mm_in = $this->mm_to_inch_const;
                        //ช่อง Calendaring    =2*NS1153/$NU$3*NT1153/$NU$3/1000/GI1153
                        if( has_val($a1) && is_numeric($a1) && has_val($a2) && is_numeric($a2) && has_val($div) && is_numeric($div) && floatval($div)!=0){
                            $a1 = floatval($a1);
                            $a2 = floatval($a2);
                            $div = floatval($div); 
                            $res_calendering =  2 * $a1 / $mm_in * $a2 / $mm_in  / 1000 / $div; 
                            //$res_calendering = round($res_calendering,6);  
                                
                        }
                        $is_coat_special=1;
                    }

                    //    ถ้ามี ค่า matt varnish / vanish ให้ เข้าช่อง Coating cost  สูตร 0.3/GL{row}
                    if( (strpos($chk_val,'VARNISH') !== false || strpos($chk_val,'VANISH') !== false ) 
                         && strpos($chk_val,'FUNGUS')===false && strpos($chk_val,'LAMINATE USE')===false  ){
                        //matt
                        $a1 = $row[$origin['arg_l_fld_name']];
                        $a2 = $row[$origin['arg_w_fld_name']];
                        $div = $row[$origin['arg_up_fld_name']];
                        $mm_in = $this->mm_to_inch_const;
                       
                        if( floatval($div)!=0 ){
                           $a1 = floatval($a1);
                           $a2 = floatval($a2);
                           $div = floatval($div); 
                           $res_coating =  0.3 / $div; 
                           //$res_coating = round($res_coating,6);     
                           
                        }
                        $is_coat_special=1;
                    }else if(strpos($chk_val,'MATT') !== false /*&& strpos($chk_val,'MATT OPP') === false*/){

                        //matt
                        $a1 = $row[$origin['arg_l_fld_name']];
                        $a2 = $row[$origin['arg_w_fld_name']];
                        $div = $row[$origin['arg_up_fld_name']];
                        $mm_in = $this->mm_to_inch_const;
                        //ถ้ามี ค่า matt  เข้าช่อง OPP/UV     สูตร =4*NS836/$NU$3*NT836/$NU$3/1000/GI836

                        if( has_val($a1) && is_numeric($a1) && has_val($a2) && is_numeric($a2) && has_val($div) && is_numeric($div) && floatval($div)!=0){
                           $a1 = floatval($a1);
                           $a2 = floatval($a2);
                           $div = floatval($div); 
                           $res_oppuv =  4 * $a1 / $mm_in * $a2 / $mm_in  / 1000 / $div; 
                           //$res_oppuv = round($res_oppuv,6);     
                           
                        }
                        $is_coat_special=1;
                    }

                    //ถ้าไม่มีอะไรพิเศษ ใส่  Coating cost  = 0.06
                    if(!$is_coat_special){

                        $res_coating = 0.06;

                    }



                }// NOT DIE CUT

                //check
                // if( floatval($row[$origin['result_coating_fld_name']]) != floatval($res_coating) ||
                // floatval($row[$origin['result_calendering_fld_name']]) != floatval($res_calendering) ||
                // floatval($row[$origin['result_oppuv_fld_name']]) != floatval($res_oppuv) ||
                // floatval($row[$origin['result_diecut_fld_name']]) != floatval($res_diecut) 
                
                // ){
                //     echo "\n<br> product:".$row[$origin['critiria_fld_name']];
                //     echo "\n<br> chk_val:".$chk_val;
                //     echo "\n<br>- coating:".'old:'.$row[$origin['result_coating_fld_name']]." new:".$res_coating;
                //     echo (floatval($row[$origin['result_coating_fld_name']])!=floatval($res_coating))?('<i style="color:red;">xxxxx</i>'):('');
                    
                //     echo "\n<br>- calende:".'old:'.$row[$origin['result_calendering_fld_name']]." new:".$res_calendering;
                //     echo (floatval($row[$origin['result_calendering_fld_name']])!=floatval($res_calendering))?('<i style="color:red;">xxxxx</i>'):('');
                    
                //     echo "\n<br>- oppuv  :".'old:'.$row[$origin['result_oppuv_fld_name']]." new:".$res_oppuv;
                //     echo (floatval($row[$origin['result_oppuv_fld_name']])!=floatval($res_oppuv))?('<i style="color:red;">xxxxx</i>'):('');
                    
                //     echo "\n<br>- diecut :".'old:'.$row[$origin['result_diecut_fld_name']]." new:".$res_diecut;
                //     echo (floatval($row[$origin['result_diecut_fld_name']])!=floatval($res_diecut))?('<i style="color:red;">xxxxx</i>'):('');
                // }



     
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['result_coating_fld_name']=>$res_coating,
                //     $origin['result_calendering_fld_name']=>$res_calendering,
                //     $origin['result_oppuv_fld_name']=>$res_oppuv,
                //     $origin['result_diecut_fld_name']=>$res_diecut,                    
                // ]);

                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['result_coating_fld_name']=>$res_coating,
                    $origin['result_calendering_fld_name']=>$res_calendering,
                    $origin['result_oppuv_fld_name']=>$res_oppuv,
                    $origin['result_diecut_fld_name']=>$res_diecut,                    
                ]);
                

                       

            }// foreach ($ori_res as $k => $row) {
        }//if($ori_res = $this->db->get()->result_array()){


        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }

}

    public function process_calc_update_printing_cost_from_color($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['arg1_fld_name']);
        $this->db->select($origin['to_fld_name']);

        
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $a1 = $row[$origin['arg1_fld_name']];

                if($a1 === null || $a1 === '' || $a1 == 0){
                    $res_val = null;
                }else{
                    $num_color = substr(strval($a1),0,1);
                    if(is_numeric($num_color)){
                        $res_val =  floatval($num_color) * 0.09 ;
                    }else{
                        $res_val = null;    
                    }
                }


                // if($row[$origin['to_fld_name']] != $res_val){
                //     echo "<br>id: ".$row['id']." : arg1 : ".$a1;
                //     echo "  => old : ".$row[$origin['to_fld_name']];         
                //     echo "  => new : ".$res_val;         
                // }
                
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }




    public function process_calc_update_lamination($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['condition_fld_name'].','.$origin['arg1_fld_name'].','.$origin['arg2_fld_name'].','.$origin['div_fld_name'].','.$origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        // echo "query : ".$this->db->get_compiled_select()."<br>";exit;

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $a1 = $row[$origin['arg1_fld_name']];
                $a2 = $row[$origin['arg2_fld_name']];
                $div = $row[$origin['div_fld_name']];
                $mm_in = $this->mm_to_inch_const;

                if( has_val($a1) && is_numeric($a1) && has_val($a2) && is_numeric($a2) && has_val($div) && is_numeric($div) && floatval($div)!=0){
                   $a1 = floatval($a1);
                   $a2 = floatval($a2);
                   $div = floatval($div); 
                   $res_val =  1.6 * $a1 / $mm_in * $a2 / $mm_in  / 1000 / $div; 
                //    $res_val = round($res_val,6);     
                   
                }else{
                    $res_val = null;
                }


                // if(1){//$row[$origin['to_fld_name']] != $res_val){
                // echo "<br>id: ".$row['id']." : ".$origin['condition_fld_name'];
                // echo " arg1 : ".$a1;
                // echo " arg2 : ".$a2;             
                // echo " div : ".$div;
                // echo "  => old : ".$row[$origin['to_fld_name']];
                // echo "  => new : ".$res_val;
                // }

                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }


       
        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }

    public function process_calc_update_sum_div($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.implode(',',$origin['sum_fld_name']).','.$origin['div_fld_name'].','.$origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);
        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                
                $arr_sum_values = [];
                foreach($origin['sum_fld_name'] as $sum_fld){
                    if(has_val($row[$sum_fld]) && is_numeric($row[$sum_fld])){
                        $arr_sum_values[]= floatval($row[$sum_fld]);
                    }
                }
                if(count($arr_sum_values)>0){
                    $sum_value = array_sum($arr_sum_values);
                }else{  
                    $sum_value = null;
                }

                $div_value = $row[$origin['div_fld_name']];

                if( $sum_value !== null && has_val($div_value) && is_numeric($div_value) && floatval($div_value)!=0){
                    $res_val = floatval($sum_value) / floatval($div_value);
                }else{
                    $res_val = null;
                }

                // if($row[$origin['to_fld_name']] != $res_val){
                // // echo "<br>id: ".$row['id']." : sum cols ";
                // // foreach($origin['sum_fld_name'] as $sum_fld){
                // //     echo " , ".$sum_fld." : ".$row[$sum_fld];                
                // // }
                // // echo " div col ".$origin['div_fld_name']." : ".$div_value;
                // // echo "  => old : ".$row[$origin['to_fld_name']];
                // // echo "  => new : ".$res_val;
                // }
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]); 
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }

            


    public function process_calc_update_sheet_roll_to_pcs($year, $month,$origin)
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['unit_fld_name'].','.$origin['roll_to_sheet_fld_name'].','.$origin['pcs_fld_name'].','.$origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }        
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $unit_value = strtoupper(trim(strval($row[$origin['unit_fld_name']])));

                if($unit_value=='ROLL'){
                    $sht_pcs = $row[$origin['roll_to_sheet_fld_name']];
                }else if($unit_value=='SHEET'){
                    $sht_pcs = roundByCustomNum($row[$origin['pcs_fld_name']],0.85);
                }else{
                    $sht_pcs = null;
                }


                if($row[$origin['to_fld_name']] != $sht_pcs){
                // echo "<br>id: ".$row['id']." : ".$origin['unit_fld_name'];
                // echo " unit value : ".$unit_value;
                // echo " roll_to_sheet : ".$row[$origin['roll_to_sheet_fld_name']];
                // echo " pcs : ".$row[$origin['pcs_fld_name']];
                // echo "  => old : ".$row[$origin['to_fld_name']];
                // echo "  => new : ".$sht_pcs;
                }

                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$sht_pcs
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$sht_pcs
                ]);
            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }


    public function process_calc_update_simple_math($year, $month,$origin,$type='ratio')
    {

        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }

        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['num1_fld_name'].','.$origin['num2_fld_name'].','.$origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }

        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $num1_value = $row[$origin['num1_fld_name']];
                $num2_value = $row[$origin['num2_fld_name']];

                if($type=='ratio'){


                    if( has_val($num1_value) && is_numeric($num1_value) && has_val($num2_value) && is_numeric($num2_value) && floatval($num2_value)!=0){
                        $res_val = floatval($num1_value) / floatval($num2_value);
                    }else{
                        $res_val = null;
                    }

                }else{
                     
                }

                if($origin['to_excel_col']=='NZ' &&               $row[$origin['to_fld_name']] != $res_val){


                // echo "<br>id: ".$row['id']." : ".$origin['num1_fld_name'];
                // echo " num1 value : ".$num1_value;
                // echo " , num2 value : ".$num2_value;
                // echo "  => old : ".$row[$origin['to_fld_name']];
                // echo "  => new : ".$res_val;
                }

                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_val
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_val
                ]);

            }

        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }
    }

    public function process_calc_update_convert_size($year, $month,$origin,$type='inch_to_mm')
    {
        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }
        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['from_fld_name'].','.$origin['to_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }
        
        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $size_value = $row[$origin['from_fld_name']];

                if($type=='inch_to_mm'){
                    if( has_val($size_value) && is_numeric($size_value)){
                        $res_size = floatval($size_value) * $this->mm_to_inch_const;
                    }else{
                        $res_size = null;
                    }

                }else{
                     
                }
                // echo "<br>from_fld_name: ".$origin['from_fld_name'];
                // echo " order site : ".$size_value;
                // echo "  => old:".$row[$origin['to_fld_name']]." updated to: ".$res_size;



                
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name']=>$res_size
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name']=>$res_size
                ]);

            }
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }

    }

    public function process_calc_update_order_paper_size_formula($year, $month,$origin){

        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }
        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);

        $this->db->select('id,'.$origin['from_fld_name']);
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }

        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){

            foreach ($ori_res as $k => $row) {

                $paper_size_str = $row[$origin['from_fld_name']];
                $res_size = $this->Util_model->extractWHFromPaperSize($paper_size_str,$origin['pattern_type']);
                // echo "<br>from_fld_name: ".$origin['from_fld_name'].' type='.$origin['pattern_type'];
                // echo " Paper Size String: ".$paper_size_str;
                // print_r($res_size);
                //update
                // $this->db->where('id',$row['id']);
                // $this->db->update($origin_table_sheet, [
                //     $origin['to_fld_name_size_1']=>$res_size['1'],
                //     $origin['to_fld_name_size_2']=>$res_size['2']
                // ]);
                $this->updateResultToOrigin($origin_table_sheet,$row['id'], [
                    $origin['to_fld_name_size_1']=>$res_size['1'],
                    $origin['to_fld_name_size_2']=>$res_size['2']
                ]);

            }
        }

        
        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }

    }

    public function process_calc_update_vlookup_formula($year, $month,$origin,$dest){
        
        

        if($this->is_debug){
            $this->calc_step++;         
            $this->calc_step_info[$this->calc_step]['calc_excel_col'] = $origin['calc_excel_col'];
            $this->calc_step_info[$this->calc_step]['calc_fld_name'] = $origin['calc_fld_name'];
            $this->calc_step_info[$this->calc_step]['calc_method'] = $origin['calc_method'];
            $this->benchmark->mark('step_start_'.$this->calc_step);
        }


        $update_result = $this->getUpdateResultFromLookup($dest,$origin);
        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);


        // print_r($update_result);exit;
  
        foreach ($update_result as $id => $upds) {
             $this->updateResultToOrigin($origin_table_sheet,$id, $upds);
 
        }

        //post sql update
        $this->post_sql_update_after_process_calc($origin);
        
        if($this->is_debug){
            $this->benchmark->mark('step_end_'.$this->calc_step);
        }

    }

    public function post_sql_update_after_process_calc($origin){
        //post sql update
        
        if(isset($origin['post_sql_update'])){
            $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);
            $where_qstr = $origin['post_sql_update']['where'];
            $arr_updates = $origin['post_sql_update']['arr_updates'];

            

            $this->db->where('year',$origin['filter']['year']);
            $this->db->where('month',$origin['filter']['month']);
            if(isset($origin['filter']['apply_origin_ids'])){
                $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
            }
            $this->db->where($where_qstr);

            $this->db->select('id');
            $this->db->from($origin_table_sheet);
            if($res = $this->db->get()->result_array()){
               foreach($res as $k => $row){
                     
                    //check manual lock by id and col_name before update
                    foreach($arr_updates as $col_name => $val_update){
                    $is_manual_lock_id_col = $this->checkIsManualLockWithPreLoadData($row['id'],$col_name,$origin_table_sheet);
                        if(!$is_manual_lock_id_col){
                            //update by id ======
                            $this->db->where('id',$row['id']);
                            $this->db->update($origin_table_sheet, [
                                $col_name=>$val_update
                            ]);
                            //=============================
                        }
                    }       
               }
            }
            
        }

    }


    public function getUpdateResultFromLookup($dest,$origin){

        $update_results = [];
        $res_str = '';
        $dest_table_sheet = $this->Def_model->getSheetDataTableName($dest['file_category'],$dest['sheet'],$dest['table_name']);
        $origin_table_sheet = $this->Def_model->getSheetDataTableName($origin['file_category'],$origin['sheet'],$origin['table_name']);
        
       // $this->getPreloadTable($dest_table_sheet,$dest['filter']);
   
        if(is_array($origin['critiria_fld_name'])){
            $arr_critiria_fld_name = $origin['critiria_fld_name'];
        }else{
            $arr_critiria_fld_name = [$origin['critiria_fld_name']];
        }
        //loop origin
        $this->db->select('id,'.implode(',',$origin['critiria_fld_name']));
        $this->db->select($origin['result_fld_name']);
        // if( $origin['calc_excel_col'] == 'NK' ){

        //     print_r($origin['critiria_fld_name']);exit;
            

        // }
        
        $this->db->where('year',$origin['filter']['year']);
        $this->db->where('month',$origin['filter']['month']);

        if(isset($origin['filter']['apply_origin_ids'])){
            $this->db->where_in('id',$origin['filter']['apply_origin_ids']);
        }

        if(isset($origin['apply_origin_where_qstr'])){
            $this->db->where($origin['apply_origin_where_qstr']);
        }
        
        $this->db->from($origin_table_sheet);

        if($ori_res = $this->db->get()->result_array()){


             

  
            foreach ($ori_res as $k => $row) {

               
    
              

                foreach($arr_critiria_fld_name as $ck=>$critiria_col){
                    if(isset($origin['critiria_filter_method']) && $origin['critiria_filter_method']== 'remove_after_space'){
                        $search_values[$ck] = remove_after_space($row[$critiria_col]);
                    }else{
                        $search_values[$ck] = $row[$critiria_col];
                    }

                    
                }

 

                //check seacrh type
                if(isset($dest['step_search_fld_name'])  ){
                    
                    $res_value = $this->searchStepDBTableOneResult($dest_table_sheet,$dest['filter'],$dest['search_fld_name'],$search_values,$dest['return_fld_name'],$dest['step_search_fld_name']);
               
                }else{
                   $res_value = $this->searchArrColsTableOneResult($dest_table_sheet,$dest['filter'],$dest['search_fld_name'],$search_values,$dest['return_fld_name']);

                }

                

                
                if(!isset($update_results[$row['id']])){
                    $update_results[$row['id']]=[];
                }

     
                    $update_results[$row['id']]= array($origin['result_fld_name']=>$res_value);
                

                
            }
        }

        return $update_results;
 

    }


 


    public function updateResultToOrigin($origin_table_sheet,$id,$update_results){

        if(empty($update_results)) return;

        //print_r($update_results);exit;

        //check manual lock by id and col_name before update
        foreach($update_results as $col_name => $val){
            $is_manual_lock_id_col = $this->checkIsManualLockWithPreLoadData($id,$col_name,$origin_table_sheet);
            if($is_manual_lock_id_col){
                //unset update for this col
                unset($update_results[$col_name]);
            }
        }
        
        //check empty again
        if(empty($update_results)) return;

        if($this->is_calc_save_to_db==1){
            //save to db

            $new_value_row = [];
            foreach($update_results as $col_name => $val){
                $new_value_row[$col_name]=$val;
            }

            //update by id important ======
            if(!empty($new_value_row)){
                //print_r($new_value_row);exit;
                $this->db->where('id',$id);
                $this->db->update($origin_table_sheet, $new_value_row);
            }
            //=============================
        }else{
            //save to array for save later
            foreach($update_results as $ur){
                //$new_value_row[$col_name]=$val;
                $arr_table_calc_save_result[$origin_table_sheet][$id][$col_name] = $val;
            }
            
        }   
    }

 


    public function getPreloadTable($table_sheet,$filter){

        if(!isset(Calc_model::$preload_table[$table_sheet])){

            $this->db->where('year',$filter['year']);
            $this->db->where('month',$filter['month']);
            if($res = $this->db->get($table_sheet)->result_array()){
                Calc_model::$preload_table[$table_sheet] = $res;
            }else{
                Calc_model::$preload_table[$table_sheet] = [];
            }

        }

    }


    public function searchPreloadTableOneResult($table_sheet,$search_col,$search_vals,$return_col){

        if($search_vals[0]=='FDBHYH-01-HM-513-0-03-M'){
            // print_r($search_col);
            // print_r($search_vals);
            // print_r(Calc_model::$preload_table[$table_sheet]);
            // exit;
        }
        $n=0;
        foreach(Calc_model::$preload_table[$table_sheet] as $k=>$row){
            //echo "\n check is match =>" . $n++;
           // print_r($search_col);
            $is_match=0;

            foreach($search_col as $ck=>$sc){
                if(20 > $n++){
                    echo "<br>Comparing ".$row[$sc]." == ".$search_vals[$ck];
                }
                
                if($row[$sc]==$search_vals[$ck]){
                    $is_match=1;
                }else{
                    $is_match=0;
                    break;
                }
            }

            if($is_match){
                // echo "found : ";
                // print_r($search_col);
                // print_r($search_vals);
                // print_r($row);                
                // echo "result :".$row[$return_col];
                return $row[$return_col];
            }
         
        }

        return null;

    }

    public function searchStepDBTableOneResult($table_sheet,$filter,$search_col,$search_vals,$return_col,$step_search_col){

        //build where
        $where = [];

        $arr_search_col_val_pair = [];
        for($i=0;$i<count($step_search_col);$i++){
            $arr_search_col_val_pair[$step_search_col[$i]] = $search_vals[$i];
        }
        // print_r($arr_search_col_val_pair);

        $this->db->where($step_search_col[0],$arr_search_col_val_pair[$step_search_col[0]]);
        $this->db->where('year',$filter['year']);
        $this->db->where('month',$filter['month']);

        if($result_step1 = $this->db->get($table_sheet)->result_array()){

            // echo $this->db->last_query();

            //found first step , search next step
            //filter other step by seaerch in array of result
            $cur_result_step = $result_step1;

            // print_r($cur_result_step);
            foreach($step_search_col as $step => $step_col){
                if($step==0) continue; //skip first step
                $filtered_result = [];
                // echo $step_col;
                foreach($cur_result_step as $k=>$row){

                    //replace ® with R
                    if($step_col=='printing_job_no_new'){
                        $row[$step_col] = str_replace('®','R',$row[$step_col]);
                    }

                    // echo strtoupper(strval($row[$step_col]))."==".strtoupper(strval($arr_search_col_val_pair[$step_col]));
                    if( strtoupper(strval($row[$step_col])) == strtoupper(strval($arr_search_col_val_pair[$step_col]))){
                        $filtered_result[]=$row;
                    }
                }
                // echo "filtered_result\n";
                // print_r($filtered_result);
                //check filter result is not empty  
                if(empty($filtered_result)){
                    // echo 'empty filtered_result';
                    // echo $cur_result_step[0][$return_col];
                    return $cur_result_step[0][$return_col]; //not found return current step result
                }else{
                    // echo "cur_result_step = filtered_result";
                    // print_r($filtered_result);
                    $cur_result_step = $filtered_result; //and go to next step
                }
                
            }
            //after all step search , return final result
            // echo 'after all step search , return final result'. $cur_result_step[0][$return_col];
            return $cur_result_step[0][$return_col];

        }

        return null;

    }


    public function searchArrColsTableOneResult($table_sheet,$filter,$search_col,$search_vals,$return_col){
       
       // echo "\n".$table_sheet;
       // print_r($search_col);
        

        if($this->is_use_hash_table){
            return $this->searchArrColsHashTableOneResult($table_sheet,$filter,$search_col,$search_vals,$return_col);
        }


        //build where
        $where = [];

        foreach($search_col as $k=>$each_col){
            //echo "<br>Searching DB Table ".$table_sheet." Where ".$each_col."=".$search_vals[$k];
            $where[$each_col] = $search_vals[$k];
        }
        $this->db->where($where);
        $this->db->where('year',$filter['year']);
        $this->db->where('month',$filter['month']);

        if($row = $this->db->get($table_sheet)->row_array()){
           // echo "\nquery found : ".$this->db->last_query();
            return $row[$return_col];
        }

        return null;

    }


    public function searchArrColsHashTableOneResult($table_sheet,$filter,$search_col,$search_vals,$return_col){
       
        //echo 'use hash table';
        if(!isset($this->hash_table[$table_sheet])){
            $this->hash_table[$table_sheet]=[];
            $this->db->where('year',$filter['year']);
            $this->db->where('month',$filter['month']);
    
            if($res = $this->db->get($table_sheet)->result_array()){
             
                foreach ($res as $k => $row) {
                    $this->hash_table[$table_sheet][$row['id']]=$row;
                }
                
            }


        }
 

        //search
        foreach ($this->hash_table[$table_sheet] as $id => $row) {
            
            $is_found=1;
            foreach($search_col as $k=>$each_col){
                if($row[$each_col]!=$search_vals[$k]){
                    $is_found=0;
                    break;
                }
            }

            if($is_found){
                return $row[$return_col];
            }

        }
 
 
 
         return null;
 
     }


     public function displayCompareValueTableAfterCalc($table_origin,$year,$month,$file_category,$sheet_name,$table_name,$calc_fld_dbcd){

        // print_r($this->before_calc_table);exit;

        $old_table =  $this->before_calc_table[$table_origin];
        $arr_compare = [];
        //get new
        $this->db->select('id,product_code,'.implode(',',$calc_fld_dbcd));
        $this->db->where('year',$year);
        $this->db->where('month',$month);

        
        //$this->Def_model->getDB_colname_from_excel_col($file_category,$sheet_name,$table_name,$calc_fld_dbcd);

        if($new_res = $this->db->get($table_origin)->result_array()){


            foreach ($new_res as $k => $row) {
                $id = $row['id'];
                $row_is_diff = 0;
                $row_compare = [];
                foreach ($calc_fld_dbcd as $c_idx => $col) {
                    $old_val = $old_table[$id][$col];
                    $new_val = $row[$col];

                        if($old_val!=$new_val){
                            if(is_numeric($old_val) && is_numeric($new_val) && round(floatval($old_val),6)!=round(floatval($new_val),6)){
                                $row_is_diff = 1;
                            }
                        }

                    $row_compare['id']= $row['id'];
                    $row_compare['product_code']= $row['product_code'];
                    $row_compare[$col]=[];
                    $row_compare[$col]['old'] = $old_val;
                    $row_compare[$col]['new'] = $new_val;
                    $row_compare['row_is_diff'] = $row_is_diff;
                    
                }
                $arr_compare[$id]= $row_compare;
                
            }


           

            $data['arr_compare'] = $arr_compare;
            $data['calc_fld_list'] = $calc_fld_dbcd;
            $data['calc_excel_col_list'] = $this->Def_model->getExcel_col_from_DB_colname($file_category,$sheet_name,$table_name,$calc_fld_dbcd);

            
            // print_r($data['arr_compare']);exit;
            return $this->load->view('cost/cost_compare_view',$data,true);
            

        }





     }




     public function getSheetOfDataCostCalcListSheet($year,$month){


        //select from db file upload
        $this->db->select('*');
        $this->db->from('file_sheet_upload');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('file_category','datacost');
        $this->db->where('is_deleted',0);

        $list=[];
        if($res = $this->db->get()->result_array()){

            foreach($this->calc_datacost_sheet_calc as $sheet){

                foreach($res as $i =>$row){

                    if($row['sheet']==$sheet){
                        $list[] = $row;
                    }

                }

            }

        }


        return $list;

        

     }



     public function mlock_get_table_item_list($year,$month,$table_name){

        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->from('manual_lock_value');

        $item_list = [];
        if($res = $this->db->get()->result_array()){
            return $res;
        }

        return [];        
     }

 



     public function mlock_get_by_col_name($year,$month,$table_name,$col_name){

        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->where('col_name',$col_name);
        $this->db->from('manual_lock_value');

        $item_list = [];
        if($res = $this->db->get()->result_array()){
            return $res;
        }

        return [];        
     }

     public function mlock_get_by_excel_col($year,$month,$table_name,$excel_col){

        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->where('excel_col',$excel_col);
        $this->db->from('manual_lock_value');

        $item_list = [];
        if($res = $this->db->get()->result_array()){
            return $res;
        }

        return [];        
     }


     public function mlock_check_is_manual_by_excel_col_row_id($year,$month,$table_name,$excel_col,$row_id){

        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->where('excel_col',$excel_col);
        $this->db->where('row_id',$row_id);
        $this->db->from('manual_lock_value');

        if($res = $this->db->get()->row_array()){
            return 1;
        }

        return 0;        
     }

     public function mlock_check_is_manual_by_col_name_row_id($year,$month,$table_name,$col_name,$row_id){

        $this->db->select('*');
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->where('col_name',$col_name);
        $this->db->where('row_id',$row_id);
        $this->db->from('manual_lock_value');

        if($res = $this->db->get()->row_array()){
            return 1;
        }

        return 0;        
     }


     public function mlock_add_col($year,$month,$table_name,$col_name,$excel_col,$row_id){
        
        //check exist
        if($this->mlock_check_is_manual_by_col_name_row_id($year,$month,$table_name,$col_name,$row_id)){
            return;
        }
        $this->db->insert('manual_lock_value',[
            'year'=>$year,
            'month'=>$month,
            'table_name'=>$table_name,
            'col_name'=>$col_name,
            'excel_col'=>$excel_col,
            'row_id'=>$row_id
        ]);
      
     }

     public function mlock_remove_col($year,$month,$table_name,$col_name,$row_id){
        
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->where('table_name',$table_name);
        $this->db->where('col_name',$col_name);
        $this->db->where('row_id',$row_id);
        $this->db->delete('manual_lock_value');
      
     }



     public function calcRowUpdateByIds($type_sheet,$fc,$sheet_name,$year,$month,$id){

        if($type_sheet=='DBCD'){
            
            $this->sheetCalc_DBCD($sheet_name,$year,$month,[$id]);
            //$table_name = $this->Def_model->getSheetDataTableName($fc,$sheet_name,'');
        }
     }



     public function checkFilecategoryIsCalcCost($year,$month,$file_category){
      
        $this->db->select('*');
        $this->db->where('file_category',$file_category);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->from('calc_sheet_data');

        if($row = $this->db->get()->row_array()){
            if($row['is_calc']==1){
                return 1;
            }
        }

        return 0;
     }

     public function updateFirstCalc($year,$month,$file_category){

        //check if exist
        $this->db->where('file_category',$file_category);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->delete('calc_sheet_data');
        
        //insert
        $ins = [];
        $ins['file_category'] = $file_category;
        $ins['year'] = $year;
        $ins['month'] = $month;
        $ins['is_calc'] = 1;
        $ins['calc_date'] = date('Y-m-d H:i:s');

        $this->db->insert('calc_sheet_data',$ins);

     }

     public function deleteFirstCalc($year,$month,$file_category){

        //check if exist
        $this->db->where('file_category',$file_category);
        $this->db->where('year',$year);
        $this->db->where('month',$month);
        $this->db->delete('calc_sheet_data');
        
     

     }


     public function saveSheetFromManualLockList($year,$month,$table_name,$fc,$sheet_name,$table_of_sheet,$calc_manual_lock_list)
     {

        //set manual lock list
        $calc_id_col = [];// id=>col_name
        $update_lock_value_list = []; //id,col_name,val
        foreach ($calc_manual_lock_list as $k => $lock_item) {
            $id = $lock_item['id'];
            $row_id = $lock_item['row_id'];    
            $col_name = $lock_item['col_name'];    
            $excel_col = $lock_item['excel_col'];    
            $lock_value = $lock_item['lock_value'];    
                //have in db
                if($lock_item['id']>0){
                    
                    if($lock_item['status']==1){

                        if($lock_item['lock_value']!=$lock_item['old_lock_value']){
                            //add effect row id
                            if(!isset($calc_id_col[$row_id])) 
                            {
                                $calc_id_col[$row_id]=[];
                            }
                            $calc_id_col[$row_id][]=$lock_item['col_name'];
                            $update_lock_value_list[]=['id'=>$row_id,'col_name'=> $col_name,'val'=>$lock_value];
                        }


                    }else{
                        //remove out
                        $this->db->where('id',$lock_item['id']);
                        $this->db->delete('manual_lock_value');

                        //add effect row id
                        if(!isset($calc_id_col[$row_id])) 
                        {
                            $calc_id_col[$row_id]=[];
                        }
                        $calc_id_col[$row_id][]=$lock_item['col_name'];

                    }


                }else{
                    //new in lock manual
                    //check if exitsted //check again เผื่อว่ามีการอัพเดทที่อื่น แต่ถ้ากรณีนี้เกิดน้อย ก็เอาออก
                    $this->db->where('year',$year);
                    $this->db->where('month',$month);
                    $this->db->where('table_name',$table_name);
                    $this->db->where('row_id',$row_id);
                    $this->db->where('col_name',$col_name);

                    if($existed_row = $this->db->get('manual_lock_value')->row_array()){
                        $existed_id = $existed_row['id'];
                        if($lock_item['status']==1){
                            //update
                        
                            $upd = [];
                            $upd['lock_value'] = $lock_value;
                            $this->db->where('id',$existed_id);
                            $this->db->update('manual_lock_value',$upd);
                            $update_lock_value_list[]=['id'=>$row_id,'col_name'=> $col_name,'val'=>$lock_value];


                        }else{
                            //remove
                            $this->db->where('id',$existed_id);
                            $this->db->delete('manual_lock_value');
                        }
                    

                    }else{

                        if($lock_item['status']==1){
                            //insert
                            $ins = [];
                            $ins['year'] = $year;
                            $ins['month'] = $month;
                            $ins['table_name'] = $table_name;
                            $ins['row_id'] = $row_id;
                            $ins['col_name'] = $col_name;
                            $ins['excel_col'] = $excel_col;
                            $ins['lock_value'] = $lock_value;

                            $this->db->insert('manual_lock_value',$ins);
                            $update_lock_value_list[]=['id'=>$row_id,'col_name'=> $col_name,'val'=>$lock_value];
                        }

                    }

                    //add effect row id
                    if(!isset($calc_id_col[$row_id])) 
                    {
                        $calc_id_col[$row_id]=[];
                    }
                    $calc_id_col[$row_id][]=$lock_item['col_name'];
                
            
                }//if($lock_item['id']>0){


       
 
		 
		 

        }//foreach ($calc_manual_lock_list as $k => $lock_item) {

        //$this->update_lock_value_list_after_save_manual_lock = $update_lock_value_list;

        $this->updateLockValueListToTable($table_name,$update_lock_value_list);

        //$this->effect_recalc_id_after_save_manual_lock = $calc_id_col;

        $this->recalc_effect_id($year,$month,$fc,$sheet_name,$table_of_sheet,$calc_id_col);

    }


    public function updateLockValueListToTable($table_name,$update_lock_value_list){

        foreach ($update_lock_value_list as $idx => $item) {
            $upd = [$item['col_name']=>$item['val']];
            $this->db->where('id',$item['id']);
            $this->db->update($table_name, $upd);
        }

    }


    public function recalc_effect_id($year,$month,$fc,$sheet_name,$table_of_sheet,$calc_id_col){

        $relist_id = [];
        foreach ($calc_id_col as $id => $value) {
            $relist_id[]=$id;
        }

        if($fc=='datacost'){
            $this->sheetCalc_DBCD($sheet_name,$year,$month,$relist_id,1);
        }

    }

    
//end class
}