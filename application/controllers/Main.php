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
		 
		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header(strtolower(uri_string())); //part_menu = strtolower(uri_string())
		
		$this->load->view('_header', $header_data);
		$this->load->view('main/main_view', $data);
		$this->load->view('_script_header');
		$this->load->view('main/main_js', $data);
		$this->load->view('_footer');


	}



	 
}
