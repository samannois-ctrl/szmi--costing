<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use function PHPUnit\Framework\isNull;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Cell\Datavalidation_cell;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use \avadim\FastExcelReader\Excel;

#[\AllowDynamicProperties]

class Cost extends CI_Controller {
 
	public function __construct()

	{
		parent::__construct();
		 	
		$this->load->model('Menu_model');
		$this->load->model('Login_model');
		$this->load->model('Cost_model');
		$this->load->model('Def_model');
		$this->load->model('Import_model');
		$this->load->model('Calc_model');
		
		
		$this->Login_model->auto_check_login();
 
	}
    public function index()
	{
		
			 

	}


	public function testnum() {
		echo format_num_general(12345689.9303900);echo "<br>";
		echo format_num_general(12345689.000900);echo "<br>";
		echo format_num_general(12345689.0);echo "<br>";
		echo format_num_general(12345689);echo "<br>";
		echo format_num_general(12345689.450);echo "<br>";
		echo format_num_general(12345689.1000);echo "<br>";
		echo format_num_general(12345689.9303900);echo "<br>";
		echo format_num_general('');echo "<br>";
		echo format_num_general(null);echo "<br>";
		echo format_num_general('0000');echo "<br>";
		echo format_num_general('12,999,300.0987800');echo "<br>";
 
	}

	public function list()
	{

		$data = [];
        $cur_path_menu = 'cost/list';
        verifyAccessMenu($cur_path_menu);
        $data = [];
        $data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);

		//get year month
		$data['select_year'] = get_select_year_from_input();
		$data['select_month'] = get_select_month_from_input();
		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); //part_menu = strtolower(uri_string())
		

		$data['list_all_upload_files'] = $this->Cost_model->getListAllFilesYearMonth($data['select_year'],$data['select_month']);

		// print_r_html($data['list_all_upload_files']);exit;

		$this->load->view('_header', $header_data);
		$this->load->view('cost/cost_list_upload_view', $data);
		$this->load->view('_script_header');
		$this->load->view('cost/cost_list_upload_js', $data);
		$this->load->view('_footer');


	}



	 public function upload_file_year_month(){

		
		$data = [];
        $cur_path_menu = 'cost/list';
        verifyAccessMenu($cur_path_menu);
        $data = [];
        $data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);
		showNotEditable($data['is_editable']);

		//get year month
		
		$data['file_category']=$this->input->get('fc');
		$data['select_year']=$this->input->get('y');
		$data['select_month']=$this->input->get('m');
		$data['file_category_info'] = $this->Cost_model->getFileCategoryInfo($data['file_category']);
		if(empty($data['file_category_info'])){
			show_404();
			exit;
		}
//		$data['file_display_tokenize'] = strReplaceYMDPattern($data['file_category_info']['file_name'],$data['select_year'],$data['select_month']);
		$data['file_display'] = $data['file_category_info']['file_display'];

		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); 


		$data['suggestion'] = $this->Def_model->getSuggestionOfFileUpload($data['file_category'], $data['select_year'], $data['select_month']);

		$this->load->view('_header', $header_data);
		$this->load->view('cost/cost_fileupload_view', $data);
		$this->load->view('_script_header');
		$this->load->view('cost/cost_fileupload_js', $data);
		$this->load->view('_footer');




	 }



	 public function detail_upload_file_year_month(){

		
		$data = [];
        $cur_path_menu = 'cost/list';
        verifyAccessMenu($cur_path_menu);
        $data = [];
        $data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);
		showNotEditable($data['is_editable']);

		//get year month
		
		$data['file_category']=$this->input->get('fc');
		$data['select_year']=$this->input->get('y');
		$data['select_month']=$this->input->get('m');
		$data['file_category_info'] = $this->Cost_model->getFileCategoryInfo($data['file_category']);
		if(empty($data['file_category_info'])){
			show_404();
			exit;
		}
		//$data['file_display_tokenize'] = strReplaceYMDPattern($data['file_category_info']['file_name'],$data['select_year'],$data['select_month']);
		$data['file_display'] = $data['file_category_info']['file_display'];

		
		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); 

		$this->load->view('_header', $header_data);
		$this->load->view('cost/cost_filedetail_view', $data);
		$this->load->view('_script_header');
		$this->load->view('cost/cost_filedetail_js', $data);
		$this->load->view('_footer');




	 }

public function getFileCategoryDetail($mode=null,$mode_filter=null){

	
	$res=[];
	$file_category = $this->input->post('file_category');
	$year = $this->input->post('year');
	$month = $this->input->post('month');

	if($mode=='arr'){
		$file_category = $mode_filter['file_category'];
		$year = $mode_filter['year'];
		$month =  $mode_filter['month'];
	}

	


	Import_model::$def_sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($file_category);

	//fill year month file_category
	Import_model::$def_sheet_info['year'] = $year;
	Import_model::$def_sheet_info['month'] = $month;
	Import_model::$def_sheet_info['file_category'] = $file_category;
	Import_model::$def_sheet_info['is_all_valid_sheet'] = 1;
	Import_model::$def_sheet_info['count_sheet_found'] = 0;




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

	//print_r($arrSheetInfo);exit;
		
	
	foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {
 
		
		if(isset($arrSheetInfo[$sheet_name])){
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 1;
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_db'] = 1;
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = $arrSheetInfo[$sheet_name]['real_sheet_name_on_excel']; 
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = $arrSheetInfo[$sheet_name]['real_sheet_idx_on_excel'];
			Import_model::$def_sheet_info['count_sheet_found']++;

			//get json_real_excel_header
			$arr_real_excel_header = json_decode($arrSheetInfo[$sheet_name]['json_real_excel_header'],true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$arr_real_excel_header = [];
			}

			foreach( $def_sheet['def_tables'] as $tbl_name => $def_table ){
				foreach( $def_table['def_col'] as $k => $def_col ){
					if( isset($arr_real_excel_header[$tbl_name][$def_col['col_name']]) ){
						Import_model::$def_sheet_structor[$sheet_name]['def_tables'][$tbl_name]['def_col'][$k]['real_excel_header'] = $arr_real_excel_header[$tbl_name][$def_col['col_name']];
					}else{
						Import_model::$def_sheet_structor[$sheet_name]['def_tables'][$tbl_name]['def_col'][$k]['real_excel_header'] = $def_col['excel_header'];
					}
				}
			}

		}else{
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 0;
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_db'] = 0;
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = ''; 
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = null;
		}

		 
	}

		//get content from DB

	foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {
		$is_compact=1;
		$is_for_display=0;
		if($def_sheet['is_found_in_excel']==1){
			 
			$result_get_content = $this->Cost_model->getSheetContentFromDB($def_sheet,$year,$month,$is_compact,$is_for_display);
			
			
			Import_model::$def_sheet_structor[$sheet_name]['compact_result'] = $result_get_content;
			

		}
		

	}

	if($mode=='arr'){

		return Import_model::$def_sheet_structor;
	
	}else{
		$res['result'] = 'success';
		$res['str_json_sheets']    = json_encode(Import_model::$def_sheet_structor);
		$res['str_json_info']    = json_encode(Import_model::$def_sheet_info);

		response_json($res);exit();
	}

}



//------Excel Import Export------start//

	public function uploadFileCategoryRecieve(){


		$file_category = $this->input->post('file_category');
		$year = $this->input->post('excel_year_upload');
		$month = $this->input->post('excel_month_upload');

        $res = [];
		$file_info = [];
		// print_r($_FILES["file_excel"]);exit;

        //get file

        if (isset($_FILES["file_excel"]) && $_FILES["file_excel"]['error'] == 0) {

 
	
			
			$file_info['name'] = $_FILES["file_excel"]["name"];
			$file_info['full_path'] = $_FILES["file_excel"]["full_path"];
			$file_info['type'] = $_FILES["file_excel"]["type"];
			$file_info['tmp_name'] = $_FILES["file_excel"]["tmp_name"];
			$file_info['size'] = $_FILES["file_excel"]["size"];
			$file_info['file_parts'] = pathinfo($_FILES["file_excel"]["name"]);

			 
			
			
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($_FILES["file_excel"]["tmp_name"]);
            $reader        = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            //$reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($_FILES["file_excel"]["tmp_name"]);
            //$map_excel_column = $this->Cost_model->map_excel_col_appform_acc;


        }else{


            $res['result'] = 'fail';
            $res['errormsg']    = 'ไฟล์ไม่ถูกต้อง';
            response_json($res);exit();


        }

		//get def of sheet
		Import_model::$def_sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($file_category);

		//fill year month file_category
		Import_model::$def_sheet_info['year'] = $year;
		Import_model::$def_sheet_info['month'] = $month;
		Import_model::$def_sheet_info['file_category'] = $file_category;
		Import_model::$def_sheet_info['is_all_valid_sheet'] = 1;
		Import_model::$def_sheet_info['count_sheet_found'] = 0;
		
		//check sheet name in excel file
		$sheetNames = $spreadsheet->getSheetNames();
		//print_r_html(Import_model::$def_sheet_structor);exit;


		//map sheet from excel to def_sheet_structor
		foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {
			$cur_sheet = $def_sheet['sheet'];
			$excel_sheet_name_search = $def_sheet['excel_sheet_name'];
			$result_search_sheet = $this->Cost_model->searchSheetNameInArray($cur_sheet,$excel_sheet_name_search,$sheetNames,$year,$month,$file_category);
		    
			if(  $result_search_sheet['found'] ==1 ){
				//found
				Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 1;
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = $result_search_sheet['real_sheet_name_on_excel']; 
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = $result_search_sheet['sheet_idx'];
				Import_model::$def_sheet_info['count_sheet_found']++;
				 
			}else{
				//not found
				Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 0;
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = ''; 
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = null;
				Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
			}
		}

		if(Import_model::$def_sheet_info['count_sheet_found'] == 0){
			Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
		}
		 
		
		//get content from excel each sheet
		// print('<pre>'.print_r($Import_model::def_sheet_structor['summary'],true).'</pre>');exit();
		foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {

			if($def_sheet['is_found_in_excel']==1){
				$cur_excel_sheet =  $spreadsheet->getSheet($def_sheet['real_sheet_idx_on_excel']);

				$result_get_content = $this->Import_model->getContentFromExcelSheet($spreadsheet,$cur_excel_sheet,$def_sheet);

				Import_model::$def_sheet_structor[$sheet_name]['import_result'] = $result_get_content;
				

			}
			

		}


	

		$view_data = [];
		 
		$gen_file_name_prefix = gen_uuid();
		$waiting_file_name = $gen_file_name_prefix."_".$file_info['name'];
		$waiting_file_path = "uploads/waiting/".$waiting_file_name;
        move_uploaded_file($file_info['tmp_name'], FCPATH.$waiting_file_path);

		$file_info['waiting_file_path'] = $waiting_file_path;
		
		$res['result'] = 'success';
		$res['str_json_sheets']    = json_encode(Import_model::$def_sheet_structor);
		$res['str_json_info']    = json_encode(Import_model::$def_sheet_info);

		
		$res['file_info'] = $file_info;
		$res['html']    = $this->load->view('cost/cost_fileupload_sub_preview',$view_data,true);
		response_json($res);exit();

	

	}


	public function uploadFileCategoryRecieveObject(){


		$file_category = $this->input->post('file_category');
		$year = $this->input->post('excel_year_upload');
		$month = $this->input->post('excel_month_upload');

        $res = [];
		$file_info = [];
		
        //get file

		

        if (isset($_FILES["file_excel"]) && $_FILES["file_excel"]['error'] == 0) {

			$file_info['name'] = $_FILES["file_excel"]["name"];
			$file_info['full_path'] = $_FILES["file_excel"]["full_path"];
			$file_info['type'] = $_FILES["file_excel"]["type"];
			$file_info['tmp_name'] = $_FILES["file_excel"]["tmp_name"];
			$file_info['size'] = $_FILES["file_excel"]["size"];
			$file_info['file_parts'] = pathinfo($_FILES["file_excel"]["name"]);

			// $cache = new MyCustomPsr16Implementation();

			// \PhpOffice\PhpSpreadsheet\Settings::setCache($cache);

            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($_FILES["file_excel"]["tmp_name"]);
            $reader        = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
			$reader->setReadDataOnly(true);
			$reader->setReadEmptyCells(false);
			$reader->setIgnoreRowsWithNoCells(true);
// exit;

            //$reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($_FILES["file_excel"]["tmp_name"]);
			//die('0112');


        }else{


            $res['result'] = 'fail';
            $res['errormsg']    = 'ไฟล์ไม่ถูกต้อง';
            response_json($res);exit();


        }
// display_memory();
		//display_memory();

		//get def of sheet
		Import_model::$def_sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($file_category);

		//fill year month file_category
		Import_model::$def_sheet_info['year'] = $year;
		Import_model::$def_sheet_info['month'] = $month;
		Import_model::$def_sheet_info['file_category'] = $file_category;
		Import_model::$def_sheet_info['is_all_valid_sheet'] = 1;
		Import_model::$def_sheet_info['count_sheet_found'] = 0;
		
		//check sheet name in excel file
		$sheetNames = $spreadsheet->getSheetNames();
		//print_r_html(Import_model::$def_sheet_structor);exit;


		//map sheet from excel to def_sheet_structor
		foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {
			$cur_sheet = $def_sheet['sheet'];
			$excel_sheet_name_search = $def_sheet['excel_sheet_name'];
			$result_search_sheet = $this->Cost_model->searchSheetNameInArray($cur_sheet,$excel_sheet_name_search,$sheetNames,$year,$month,$file_category);
		    // print_r($def_sheet);exit;

			$is_optional = $def_sheet['is_optional'];

			if(  $result_search_sheet['found'] ==1 ){
				//found
				Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 1;
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = $result_search_sheet['real_sheet_name_on_excel']; 
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = $result_search_sheet['sheet_idx'];
				Import_model::$def_sheet_info['count_sheet_found']++;
				 
			}else{
				//not found
				Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 0;
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = ''; 
				Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = null;
				if($is_optional==0){
					Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
				}
			}
		}

		if(Import_model::$def_sheet_info['count_sheet_found'] == 0){
			Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
		}
		 
		
		//get content from excel each sheet
		//print('<pre>'.print_r(Import_model::$def_sheet_structor,true).'</pre>');exit();
		foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {

			if($def_sheet['is_found_in_excel']==1 ){
				$cur_excel_sheet =  $spreadsheet->getSheet($def_sheet['real_sheet_idx_on_excel']);

				$result_get_content = $this->Import_model->getContentFromExcelSheet($spreadsheet,$cur_excel_sheet,$def_sheet);

				Import_model::$def_sheet_structor[$sheet_name]['import_result'] = $result_get_content;
				

			}
			

		}

		// exit;


	

		$view_data = [];
		 
		$gen_file_name_prefix = gen_uuid();
		$waiting_file_name = $gen_file_name_prefix."_".$file_info['name'];
		$waiting_file_path = "uploads/waiting/".$waiting_file_name;
        move_uploaded_file($file_info['tmp_name'], FCPATH.$waiting_file_path);

		$file_info['waiting_file_path'] = $waiting_file_path;


		$this->Import_model->convertDefSheetStructorToCompact(0,1); //not for display and not delete import result
		$res['str_json_sheets_compact_data']    = json_encode(Import_model::$def_sheet_structor_compact_version);
		// $this->Import_model->convertDefSheetStructorToCompact(1,1); //for display and  delete import result
		// $res['str_json_sheets_compact_display']    = json_encode(Import_model::$def_sheet_structor_compact_version);

		
		$res['result'] = 'success';
		//$res['str_json_sheets']    = json_encode(Import_model::$def_sheet_structor);
		$res['str_json_info']    = json_encode(Import_model::$def_sheet_info);

		
		$res['file_info'] = $file_info;
		//$res['html']    = $this->load->view('cost/cost_fileupload_sub_preview',$view_data,true);



		response_json($res);exit();

	

	}


	public function	uploadFileCategoryConfirmSave(){
		$res= [];	

		//check if empty
		if(is_null($this->input->post('str_json_sheets')) || is_null($this->input->post('file_info')) || is_null($this->input->post('str_json_info'))){

			$res['result'] = 'failed';
			$res['errormsg'] = 'ข้อมูลยืนยันตอบกลับมาไม่ครบถ้วน';

			response_json($res);exit();

		}
		//get data
		Import_model::$def_sheet_structor = json_decode($this->input->post('str_json_sheets'), true);
		Import_model::$def_sheet_info = json_decode($this->input->post('str_json_info'), true);
		$file_info = $this->input->post('file_info');

		// print_r(Import_model::$def_sheet_structor);
		// print_r(Import_model::$def_sheet_info);exit;

		$uploaded_file = copy_waiting_to_file_uploaded($file_info['waiting_file_path']);

		if($uploaded_file==''){
			$res['result_keep_file'] = 'fail';
			$res['uploaded_file'] = '';
			$res['result_keep_file_msg'] = 'ไม่สามารถเก็บไฟล์ที่ใช้สำหรับการอัพโหลดครั้งนี้ในระบบ';
		}else{

			$res['result_keep_file'] = 'success';
			$res['uploaded_file'] = $uploaded_file;
			$res['result_keep_file_msg'] = 'เก็บไฟล์ที่ใช้สำหรับการอัพโหลดครั้งนี้ในระบบสำเร็จ';

		}
		


			$year = Import_model::$def_sheet_info['year'];
			$month = Import_model::$def_sheet_info['month'];
			$file_category = Import_model::$def_sheet_info['file_category'];
			$file_name = (!empty($uploaded_file))?($file_info['file_parts']['basename']):('');
			$file_path = $uploaded_file;

			//save file upload info to DB

			$file_upload_id = $this->Import_model->saveFileUploadInfoToDB($year,$month,$file_category,$file_name,$file_path);
 
		if(empty($file_upload_id)){
			$res['result'] = 'failed';
			$res['errormsg'] = 'ไม่สามารถบันทึกข้อมูลการอัพโหลดไฟล์';

			response_json($res);exit();
		}
		 

		$res_save_db = $this->Import_model->saveUploadedDefSheetStructorToDB($file_upload_id);

		//clean upload file
		if($res_save_db['result']=='success'){
			//ลบรายการ ในตาราง file_upload ของเก่า และลบไฟล์เก่าใน waiting
			$this->Import_model->commitCleanFileUploadInfoOld($year,$month,$file_category,$file_upload_id);
		} 
		


		$res['res_save_db'] = $res_save_db;


		if($res_save_db['result']=='success'){
			
			$res['result'] = 'success';

		}else{
			$res['result'] = 'failed';

		}

		response_json($res);exit();
	}




	public function	uploadFileCategoryConfirmSaveObject(){

		// print_r($_POST);
		// echo "exit 00000";exit;
		$res= [];	

		//check if empty
		if(is_null($this->input->post('str_json_sheets_compact')) || is_null($this->input->post('file_info')) || is_null($this->input->post('str_json_info'))){

			$res['result'] = 'failed';
			$res['errormsg'] = 'ข้อมูลยืนยันตอบกลับมาไม่ครบถ้วน';

			response_json($res);exit();

		}
		// echo "exit 00001";
		//get data
		Import_model::$def_sheet_structor_compact_version = json_decode($this->input->post('str_json_sheets_compact'), true);
		Import_model::$def_sheet_info = json_decode($this->input->post('str_json_info'), true);
		$file_info = $this->input->post('file_info');

		// print_r(Import_model::$def_sheet_structor_compact_version);
		// print_r(Import_model::$def_sheet_info);exit;

		$uploaded_file = copy_waiting_to_file_uploaded($file_info['waiting_file_path']);
		// $uploaded_file='test';

		// echo "exit 00002";exit;

		if($uploaded_file==''){
			$res['result_keep_file'] = 'fail';
			$res['uploaded_file'] = '';
			$res['result_keep_file_msg'] = 'ไม่สามารถเก็บไฟล์ที่ใช้สำหรับการอัพโหลดครั้งนี้ในระบบ';
		}else{

			$res['result_keep_file'] = 'success';
			$res['uploaded_file'] = $uploaded_file;
			$res['result_keep_file_msg'] = 'เก็บไฟล์ที่ใช้สำหรับการอัพโหลดครั้งนี้ในระบบสำเร็จ';

		}
		


			$year = Import_model::$def_sheet_info['year'];
			$month = Import_model::$def_sheet_info['month'];
			$file_category = Import_model::$def_sheet_info['file_category'];
			$file_name = (!empty($uploaded_file))?($file_info['file_parts']['basename']):('');
			$file_path = $uploaded_file;

			//save file upload info to DB

			$file_upload_id = $this->Import_model->saveFileUploadInfoToDB($year,$month,$file_category,$file_name,$file_path);
 
		if(empty($file_upload_id)){
			$res['result'] = 'failed';
			$res['errormsg'] = 'ไม่สามารถบันทึกข้อมูลการอัพโหลดไฟล์';

			response_json($res);exit();
		}

		$res_save_db = $this->Import_model->saveUploadedDefSheetStructorToDBCompact($file_upload_id);


		//clean upload file
		if($res_save_db['result']=='success'){
			//ลบรายการ ในตาราง file_upload ของเก่า และลบไฟล์เก่าใน waiting
			$this->Import_model->commitCleanFileUploadInfoOld($year,$month,$file_category,$file_upload_id);

			//delete firstcalc
			$this->Calc_model->deleteFirstCalc($year,$month,$file_category);
		} 
		

		$res['res_save_db'] = $res_save_db;


		if($res_save_db['result']=='success'){
			
			$res['result'] = 'success';

		}else{
			$res['result'] = 'failed';

		}

		response_json($res);exit();
	}

//------Excel Import Export------start//

//test
public function del(){
	$this->Import_model->deleteOldWaitingFile();
}




//FAST Excel Reader ===========================================

public function uploadFileCategoryRecieveObjectFastExcel(){


	$file_category = $this->input->post('file_category');
	$year = $this->input->post('excel_year_upload');
	$month = $this->input->post('excel_month_upload');

	$res = [];
	$file_info = [];
	
	//get file

	

	if (isset($_FILES["file_excel"]) && $_FILES["file_excel"]['error'] == 0) {

		$file_info['name'] = $_FILES["file_excel"]["name"];
		$file_info['full_path'] = $_FILES["file_excel"]["full_path"];
		$file_info['type'] = $_FILES["file_excel"]["type"];
		$file_info['tmp_name'] = $_FILES["file_excel"]["tmp_name"];
		$file_info['size'] = $_FILES["file_excel"]["size"];
		$file_info['file_parts'] = pathinfo($_FILES["file_excel"]["name"]);



		// echo $_FILES["file_excel"]["tmp_name"];exit;


		// copy($_FILES["file_excel"]["tmp_name"],'/var/www/html/f.xlsx');exit;
		// Open XLSX-file
		$excel = Excel::open($_FILES["file_excel"]["tmp_name"]);
		$excel->dateFormatter('Y-m-d H:i:s');

		//get row height

 		// $sheet = $excel->selectSheet('DB#CD4');
		
		// // Get the height of row 1

		// //print_r($sheet->actualDimension());

		// for ($i=0; $i < 1400; $i++) { 
		// 	$rowHeight = $sheet->getRowHidden($i);
		// 	echo "\n $i :".$rowHeight;
		// }
		// exit;
		
		

		// Read all values as a flat array from current sheet
		// $result = $excel->readRowsWithStyles();

		// $result = $excel->readRowsWithStyles();
		// print_r($result[6]);exit;
		//$result = [];
		// $excel->readCallback(function ($row, $col, $val) use(&$result) {
		// 	// Any manipulation here
		// 	$result[$row][$col] = (string)$val;
		
		// 	// if the function returns true then data reading is interrupted  
		// 	return false;
		// });

		// display_memory();
		// echo count($result);exit;
		// print_r($result);exit;
	
		
				

	}else{


		$res['result'] = 'fail';
		$res['errormsg']    = 'ไฟล์ไม่ถูกต้อง';
		response_json($res);exit();


	}
// display_memory();
	//display_memory();

	//get def of sheet
	Import_model::$def_sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($file_category);

	//fill year month file_category
	Import_model::$def_sheet_info['year'] = $year;
	Import_model::$def_sheet_info['month'] = $month;
	Import_model::$def_sheet_info['file_category'] = $file_category;
	Import_model::$def_sheet_info['is_all_valid_sheet'] = 1;
	Import_model::$def_sheet_info['count_sheet_found'] = 0;
	
	//check sheet name in excel file
	$sheetNames = $excel->getSheetNames();
	
	//print_r_html(Import_model::$def_sheet_structor);exit;

	
	//map sheet from excel to def_sheet_structor
	foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {
		$cur_sheet = $def_sheet['sheet'];
		$excel_sheet_name_search = $def_sheet['excel_sheet_name'];
		$result_search_sheet = $this->Cost_model->searchSheetNameInArray($cur_sheet,$excel_sheet_name_search,$sheetNames,$year,$month,$file_category);
		// print_r($def_sheet);exit;

		$is_optional = $def_sheet['is_optional'];

		if(  $result_search_sheet['found'] ==1 ){
			//found
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 1;
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = $result_search_sheet['real_sheet_name_on_excel']; 
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = $result_search_sheet['sheet_idx'];
			Import_model::$def_sheet_info['count_sheet_found']++;
			 
		}else{
			//not found
			Import_model::$def_sheet_structor[$sheet_name]['is_found_in_excel'] = 0;
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_name_on_excel'] = ''; 
			Import_model::$def_sheet_structor[$sheet_name]['real_sheet_idx_on_excel'] = null;
			if($is_optional==0){
				Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
			}
		}
	}

	if(Import_model::$def_sheet_info['count_sheet_found'] == 0){
		Import_model::$def_sheet_info['is_all_valid_sheet'] = 0;
	}
	
	 
	
	//get content from excel each sheet
	// print('<pre>'.print_r(Import_model::$def_sheet_structor,true).'</pre>');exit();
	foreach (Import_model::$def_sheet_structor as $sheet_name => $def_sheet) {

		if($def_sheet['is_found_in_excel']==1   ){
			$cur_excel_sheet =  $excel->getSheetById($def_sheet['real_sheet_idx_on_excel']);
			// $cur_excel_sheet =  $excel->getSheetById(2);

			$result_get_content = $this->Import_model->getContentFromExcelSheetFastExcel($excel,$cur_excel_sheet,$def_sheet);

			Import_model::$def_sheet_structor[$sheet_name]['import_result'] = $result_get_content;
			// unset($result_get_content);
			

		}
		

	}

	// exit;

	// print_r(Import_model::$def_sheet_structor);
	// die('stop');


	$view_data = [];
	 
	$gen_file_name_prefix = gen_uuid();
	$waiting_file_name = $gen_file_name_prefix."_".$file_info['name'];
	$waiting_file_path = "uploads/waiting/".$waiting_file_name;
	move_uploaded_file($file_info['tmp_name'], FCPATH.$waiting_file_path);

	$file_info['waiting_file_path'] = $waiting_file_path;


	$this->Import_model->convertDefSheetStructorToCompact(0,1); //not for display and not delete import result
	$res['str_json_sheets_compact_data']    = json_encode(Import_model::$def_sheet_structor_compact_version);
	// $this->Import_model->convertDefSheetStructorToCompact(1,1); //for display and  delete import result
	// $res['str_json_sheets_compact_display']    = json_encode(Import_model::$def_sheet_structor_compact_version);

	
	$res['result'] = 'success';
	//$res['str_json_sheets']    = json_encode(Import_model::$def_sheet_structor);
	$res['str_json_info']    = json_encode(Import_model::$def_sheet_info);

	
	$res['file_info'] = $file_info;
	//$res['html']    = $this->load->view('cost/cost_fileupload_sub_preview',$view_data,true);

	// display_memory();exit;

	response_json($res);exit();



}



///----- CALC --------------------------------------------//
public function calc()
{

	$data = [];
	$cur_path_menu = 'cost/list';
	verifyAccessMenu($cur_path_menu);
	$data = [];
	$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);

	//get year month
	$data['select_year'] = get_select_year_from_input();
	$data['select_month'] = get_select_month_from_input();


	//check is upload file completed
	$data['is_file_completed'] = $this->Cost_model->getIsFileUploadComplete($data['select_year'], $data['select_month']);
	$data['list_all_file_upload'] = $this->Cost_model->getListAllFilesYearMonth($data['select_year'], $data['select_month']);


	$is_calc_fc = $this->Calc_model->checkFilecategoryIsCalcCost($data['select_year'], $data['select_month'],'datacost');
	$data['is_calc_fc'] = $is_calc_fc;
	$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); //part_menu = strtolower(uri_string())


	if($data['is_file_completed']==0){
		$this->load->view('_header', $header_data);
		$this->load->view('cost/cost_list_calc_no_file_view', $data);
		$this->load->view('_script_header');
		$this->load->view('_footer');
		

	}else{

		$data['list_sheet'] = $this->Calc_model->getSheetOfDataCostCalcListSheet($data['select_year'], $data['select_month']);


		// print_r($data['list_sheet']);exit;
	
		$this->load->view('_header', $header_data);
		$this->load->view('cost/cost_list_calc_view', $data);
		$this->load->view('_script_header');
		$this->load->view('cost/cost_list_calc_js', $data);
		$this->load->view('_footer');

	}


}



//============ CALC COST ======================//
public function startCalcCostBasic(){

		
	$data = [];
	$cur_path_menu = 'cost/list';
	verifyAccessMenu($cur_path_menu);
	$data = [];
	$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);

	if($data['is_editable']==0){
		$res=[];
		$res['result']=['failed'];
		response_json($res);exit;
	}
	//get year month
	

	$data['select_year']=$this->input->post('select_year');
	$data['select_month']=$this->input->post('select_month');
	$data['first_calc']=$this->input->post('first_calc');
	$is_compare =$this->input->post('is_compare');
	$data['is_compare']=$is_compare;

	if($is_compare==1){
		$this->Calc_model->is_compare_old_new_calc =1;
	}

	//list DBCD sheet in year month
	$file_category = 'datacost';
	$arrSheets = $this->Cost_model->getFileSheetUpload($data['select_year'],$data['select_month'],$file_category);
	foreach ($arrSheets as $sheet_name => $sheet_info) {
		if(in_array($sheet_name,$this->Calc_model->dbcd_member_sheet)){
			$this->Calc_model->sheetCalc_DBCD($sheet_name,$data['select_year'],$data['select_month']);
		}
	}

	//buid total lowmargin pivot

	
	

 

	//$this->benchmark->mark('code_end');

	//echo "***".$this->benchmark->elapsed_time('code_start', 'code_end')."***";

	if($data['first_calc']==1){
		//calc first time 
		$this->Calc_model->updateFirstCalc($data['select_year'],$data['select_month'],'datacost');
	}

	$res=[];
	$res['result'] = 'success';


	if($is_compare==1){
		$res['str_json_compare_calc_result']  = json_encode($this->Calc_model->html_compare_old_new_calc);
	}

	response_json($res);exit();



}


//get Sheet json for Calc page
public function getCalcSheetObject(){

	 
	//get year month
	$fc = $this->input->post('fc');
	$sheet_name = $this->input->post('sheetname');
	$year = $this->input->post('select_year');
	$month = $this->input->post('select_month');
	$is_compact=1;
	$is_for_display=0;

	$sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($fc, $sheet_name);



	 
	$sheet_structor[$sheet_name]['calc_result'] = $this->Cost_model->getSheetContentFromDB($sheet_structor[$sheet_name],$year,$month,$is_compact,$is_for_display);
	
	
	$tbl = $this->Def_model->getSheetDataTableName($fc,$sheet_name,'');
	// echo $tbl;exit;
	$manual_lock_list = $this->Calc_model->mlock_get_table_item_list($year,$month,$tbl);
	$res['result'] = 'success';
	$res['str_json_sheets_calc_data']  = json_encode($sheet_structor[$sheet_name]);
	$res['calc_col_front_dbcd']  =json_encode($this->Calc_model->calc_col_front_dbcd);
	$res['calc_col_edit_dbcd']  =json_encode($this->Calc_model->calc_col_edit_dbcd);
	$res['calc_col_all_dbcd']  =json_encode($this->Calc_model->calc_col_all_dbcd);
	$res['dbcd_member_sheet']  =json_encode($this->Calc_model->dbcd_member_sheet);
	$res['manual_lock_list']  =json_encode($manual_lock_list);
	$res['sheet']    = $sheet_name;
	$res['year']    = $year;
	$res['month']    = $month;
	response_json($res);exit();

}



public function updateCalcSheetCellAndRecalc(){

	// rowdata,excel_col,new_val,year,month,fc,sheet_name,table_name

	$rowdata = json_decode($this->input->post('json_rowdata'), true);
	$excel_col = $this->input->post('excel_col');
	$col_name = $this->input->post('col_name');
	$new_val = $this->input->post('new_val');
	$year = $this->input->post('year');
	$month = $this->input->post('month');
	$fc = $this->input->post('fc');
	$sheet_name = $this->input->post('sheet_name');
	$table_name = $this->input->post('table_data_name');
	$table_of_sheet = $this->input->post('table_of_sheet');
	
	//update manual lock
	$this->Calc_model->mlock_add_col($year,$month,$table_name,$col_name,$excel_col,$rowdata['id']);
   
	//update cell value
	$this->Cost_model->updateSheetCellValue($table_name,$rowdata['id'],$col_name,$new_val);

	//calc row and update dependent cell
	$this->Calc_model->calcRowUpdateByIds('DBCD',$fc,$sheet_name,$year,$month,$rowdata['id']);

	//get updated sheet content
	$table_of_sheet = ($table_of_sheet=='')?('main'):($table_of_sheet);
	$is_compact=2; //use excel col as key
	$is_for_display=0;
	$filer_condition = [];
	$filer_condition['arr_ids'] = $rowdata['id'];	
	$filer_condition['table_name'] = $table_of_sheet;
	$sheet_structor = $this->Def_model->getDefSheetStructorOfFileCategory($fc, $sheet_name);
	//print_r($sheet_structor);exit;	 
	$sheet_structor[$sheet_name]['calc_result'] = $this->Cost_model->getSheetContentFromDB($sheet_structor[$sheet_name],$year,$month,$is_compact,$is_for_display,$filer_condition);

	// print_r($rowdata);
	// print_r($sheet_structor[$sheet_name]['calc_result']['tables']['main']['content'][0]);exit;

	$new_data = array_merge($rowdata,$sheet_structor[$sheet_name]['calc_result']['tables']['main']['content'][0]);

	$new_data['MX']='new mock';


	$res_update=[];
	$res_update['result'] = 'success';
	$res_update['json_new_data'] = json_encode($new_data);
	response_json($res_update);exit();


}


public function saveCalcSheetRecalc()
{
	// year,month,fc,sheet_name
	$year = $this->input->post('select_year');
	$month = $this->input->post('select_month');
	$calc_manual_lock_list = json_decode($this->input->post('json_calc_manual_lock_list'),true);
	$fc = $this->input->post('fc');
	$sheet_name = $this->input->post('sheet_name');
	$table_name = $this->input->post('table_data_name');
	$table_of_sheet = $this->input->post('table_of_sheet');

	//check manual lock and save
	if(!empty($calc_manual_lock_list)){

		// foreach ($calc_manual_lock_list as $mlock_item) {
		// 	print_r($mlock_item);
		// }

		$this->Calc_model->saveSheetFromManualLockList($year,$month,$table_name,$fc,$sheet_name,$table_of_sheet,$calc_manual_lock_list);



		// $this->update_lock_value_list_after_save_manual_lock ;
        // $this->effect_recalc_id_after_save_manual_lock ;

		// print_r($this->Calc_model->update_lock_value_list_after_save_manual_lock);
		// print_r($this->Calc_model->effect_recalc_id_after_save_manual_lock);




	}

	$res = [];
	$res['result'] = 'success';
	response_json($res);exit();

}


public function report(){
	
	$data = [];
		 
	$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); //part_menu = strtolower(uri_string())
	
	$this->load->view('_header', $header_data);
	$this->load->view('report/report_view', $data);
	$this->load->view('_script_header');
	$this->load->view('report/report_js', $data);
	$this->load->view('_footer');



}


//Export Calc Result ====================================
public function getCalcExport(){
	// var_dump($this->input->get('filter_data'));exit;
	$filter_data = json_decode($this->input->get('filter_data'),true);
	$year = $filter_data['year'];
	$month = $filter_data['month'];



	$file_export =   "คำนวณต้นทุน_".$year."_".$month;
	$mode_filter = ['year'=>$year, 'month'=>$month, 'file_category'=>'datacost'];
	$sheet_structor = $this->getFileCategoryDetail('arr',$mode_filter);

	$this->Import_model->creatExcelFromSheetStructor($sheet_structor,$file_export);
 


}
//Export Calc Result ====================================


//end class
}
