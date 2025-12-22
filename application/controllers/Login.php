<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]
class Login extends CI_Controller {


	public function __construct()

	{
		parent::__construct();
		
		$this->load->model('Login_model');
	}

	
	public function index()
	{
		$this->load->view('login_view');



	}
 
	public function auth()
	{
		$login_name	 = $this->input->post('username');
		$pass = ($this->input->post('password'));

		$arr_res = [];



		//ถ้า user lock =======================

	 
		$this->db->select('*');
		$this->db->from('user');
		$this->db->where('login_name',$login_name);
		$this->db->where('is_active',1);
		$this->db->where('is_deleted',0);

		if( ($res_rows = $this->db->get()->row_array()) ){

			if(  $this->Login_model->checkPassword($pass,strval($res_rows['hash_password']))  ){



				$arr_res['result'] = 'granted';
				$user_info = array('user_id'=>$res_rows['id'],
								   'user_name'=>$res_rows['user_name'],
								   'login_name'=>$res_rows['login_name'],
							);
				$permission_info = $this->Login_model->getPermissionInfo($res_rows['id']);
				$arr_res['redirect_to'] = base_url();
				$this->submitAuth($user_info,$permission_info);



			}else{

			//รหัสผ่านไม่ถูกต้อง 

			$arr_res['result'] = 'fail';
			$arr_res['error_msg'] = 'รหัสผ่านไม่ถูกต้อง';


			}

		}else{

			$arr_res['result'] = 'fail';
			$arr_res['error_msg'] = 'ชื่อผู้ใช้ หรือรหัสผ่านไม่ถูกต้อง';



		}


		response_json($arr_res);
		

	}


	//prevent call from outside
	private function submitAuth($user_info,$permission_info)
	{

		$this->session->set_userdata($user_info);
		
		$this->session->set_userdata('login',1);
		$this->session->set_userdata('appname',CURRENT_APP_NAME);
		$this->session->set_userdata('login_start',time());
		$this->session->set_userdata('permission_info',$permission_info);


		//save last login
		$this->db->where('id',$user_info['user_id']);
		$this->db->update('user', array('last_login_dtm'=> date('Y-m-d H:i:s')));


	}


	//prevent call from outside
	public function out()
	{
		session_destroy();

		header('Location: '.base_url('login/'));
		exit;

	}




	
}



?>
