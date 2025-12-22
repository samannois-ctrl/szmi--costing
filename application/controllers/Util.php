<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Util extends CI_Controller {


	public function __construct()

	{
		parent::__construct();
		$this->load->model('Appointwait_model');
		$this->load->model('Loc_model');


	}

	
	public function index()
	{
		

	}


	public function setyearsession(){
		$y = $this->input->post('year');
		set_select_year($y);

	}


	public function getyearsession(){
		get_select_year();

	}





}
