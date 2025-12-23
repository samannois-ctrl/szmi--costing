<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
 
	public function __construct()

	{
		parent::__construct();
		 	
		$this->load->model('Menu_model');
		$this->load->model('Login_model');

		
		$this->Login_model->auto_check_login();
 
	}
 
	public function index()
{
	$data = [];
	
	// Load required models
	$this->load->model('Cost_model');
	$this->load->model('Calc_model');
	
	// Get year/month from query params or use current date
	$data['select_year'] = get_select_year_from_input();
	$data['select_month'] = get_select_month_from_input();
	
	// Check if files are uploaded and calculations exist
	$data['is_file_completed'] = $this->Cost_model->getIsFileUploadComplete($data['select_year'], $data['select_month']);
	$is_calc_fc = $this->Calc_model->checkFilecategoryIsCalcCost($data['select_year'], $data['select_month'], 'datacost');
	$data['is_calc_fc'] = $is_calc_fc;
	
	// Get sheet list if calculation exists
	if ($data['is_file_completed'] && $is_calc_fc) {
		$data['list_sheet'] = $this->Calc_model->getSheetOfDataCostCalcListSheet($data['select_year'], $data['select_month']);
	} else {
		$data['list_sheet'] = [];
	}
	 
	$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string()));
	
	$this->load->view('_header', $header_data);
	$this->load->view('main/main_view', $data);
	$this->load->view('_script_header');
	$this->load->view('main/main_js', $data);
	$this->load->view('_footer');

}	 
}
