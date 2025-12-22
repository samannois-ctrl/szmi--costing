<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Login_model extends CI_Model {



	public function __construct()

	{
		parent::__construct();

		$this->wait_time = 5*60; // 5 min
		$this->max_failed_lock = 10; // 10 ครั้ง
		$this->next_login_date_text = '';  
		$this->current_fail_count = '';  
 

	}

	public function hashPassword($str)
	{
		//return password_hash($str, PASSWORD_DEFAULT);
		return password_hash($str, PASSWORD_BCRYPT);

		
	}

	public function checkPassword($str,$hashed)
	{
		return password_verify(strval($str), strval($hashed) );
	}

	public function check_is_admin(){

		if($this->session->userdata('is_admin') == 1){
			return 1;
		}else{
			return 0;
		}

	}

	public function clearExpireLoginFail()
	{
		// $d = date('Y-m-d H:i:s',time()-$this->wait_time );
		
		// $this->db->where("first_login_dtm <= '".$d."'  ");
		// $this->db->delete('login_failed');
	}


	public function clearLoginFail($user_id)
	{
		
		
		$this->db->where("user_id",$user_id);
		$this->db->delete('login_failed');
	}

	public function checkIsPasswordFailedLock($login_name){

		$this->db->select('*');
		$this->db->where('login_name',$login_name);
		$this->db->where('ip_address',$_SERVER['REMOTE_ADDR']);
		$this->db->where("failed_count >= ".$this->max_failed_lock);

		if(  $res = $this->db->get('login_failed')->row_array()   ){

			$this->next_login_date_text = date('H:i:s',strtotime($res['first_login_dtm']) + $this->wait_time);
			return 1;

		}


		return 0;


	}


	public function addFailedPassword($user_id,$login_name)
	{
		
		//check last failed login of this user

		$this->db->select('*');
		$this->db->where('user_id',$user_id);
		$this->db->where('ip_address',$_SERVER['REMOTE_ADDR']);

		if(  $res = $this->db->get('login_failed')->row_array()   ){

			$new_failed_count = $res['failed_count'] + 1;

			$this->db->where('user_id',$user_id);
			$this->db->update('login_failed',array('failed_count'=>$new_failed_count,'last_login_dtm'=>date('Y-m-d H:i:s')));
			$this->current_fail_count = $new_failed_count;

		}else{
			$ins = [];
			$ins['user_id'] = $user_id;
			$ins['login_name'] = $login_name;
			$ins['first_login_dtm'] = date('Y-m-d H:i:s');
			$ins['last_login_dtm'] = date('Y-m-d H:i:s');
			$ins['ip_address'] = $_SERVER['REMOTE_ADDR'];
			$ins['failed_count'] = 1;
			$this->current_fail_count = 1;
			$this->db->insert('login_failed', $ins);
			

		}




	}



	public function getPermission($user_id){

	


	}



	public function is_login()
	{
		if( $this->session->userdata('login')==1  && $this->session->userdata('appname') == CURRENT_APP_NAME ){


			return 1;
		}else{
			return 0;
		}
	}


	public function auto_check_login()
	{
		
		if( $this->is_login()  ){

			return;
		}else{


			//check is ajax request api
			if(isset($_SERVER['HTTP_APPREQTYPE']) && $_SERVER['HTTP_APPREQTYPE']=='api'){

				$res = [];
				$res['result'] = 'logout';
				response_json($res);exit;

			}

			
			header('Location: '.base_url('login'));
			exit;
		}
	}


	public function getPermissionInfo($user_id)
	{
		$res = [];
		$res['permission_menu_id'] = [];
		$res['permission_menu_path'] = [];


		$this->db->select('a.*,b.path');
		$this->db->from('user_menu_permission a');
		$this->db->join('menu b','a.menu_id = b.id');


		$this->db->where('user_id',$user_id);

		if($p = $this->db->get()->result_array()){

			foreach ($p as $k => $v) {
				$res['permission_menu_id'][$v['menu_id']] = $v['permission'];
				$res['permission_menu_path'][strtolower($v['path'])] = $v['permission'];
			}


		}

		return $res;
	}
}