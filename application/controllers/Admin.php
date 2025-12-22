<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Admin extends CI_Controller {


	public function __construct()

	{
		parent::__construct();
		$this->load->model('Menu_model');

		$this->load->model('Login_model');
		$this->load->model('User_model');
		$this->load->model('Def_model');

		$this->Login_model->auto_check_login();

	}

	
	public function user()
	{
		$cur_path_menu = 'admin/user';
 
		
		verifyAccessMenu($cur_path_menu);
 

		$data = [];

		$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);

		//print_r($data);exit;

		$header_data = [];
		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header($cur_path_menu);  

	 

		$this->load->view('_header',$header_data);
		$this->load->view('admin/admin_user_view', $data);
		$this->load->view('_script_header');
		$this->load->view('admin/admin_user_js', $data);
		$this->load->view('_footer');


	}





	public function permission()
	{
		
		$cur_path_menu = 'admin/permission';
		verifyAccessMenu($cur_path_menu);
		$data = [];
		$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);


		$header_data = [];
		$header_data['menu_item_html'] = $this->Menu_model->get_html_menu_header($cur_path_menu);  
		$this->load->view('_header',$header_data);
		$this->load->view('admin/admin_permission_view', $data);
		$this->load->view('_script_header');
		$this->load->view('admin/admin_permission_js', $data);
		$this->load->view('_footer');


	}





	public function getUserTable()
	{
		 
		$cur_path_menu = 'admin/user';
		
		verifyAccessMenu($cur_path_menu);

		$data = [];

		$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);

		$data['user_list'] = $this->User_model->getUserList();

		$res = [];

		$res['result'] = 'success';
		$res['html'] = $this->load->view('admin/admin_user_table', $data,true);

		response_json($res);


	}


	public function checkExistedCode()
	{
		$new_code = trim(strval($this->input->post('new_code')));


		$res = [];
		$res['existed'] = 0;
		if($this->User_model->checkExistedUserLoginName($new_code)){

			$res['existed'] = 1;

		}



		response_json($res);





	}


	

	public function addNewUser()
	{
		


		if(! checkCurrentIsWriteMenu('admin/user') ) { 
				$res = [];
				$res['result'] = 'admin_fail';
			
				response_json($res);
				exit();
		}

		//print_r($_POST);exit;


		if(! has_keys_in_array(array('login_name','user_name','user_password'), $_POST) ){
				$res = [];
				$res['result'] = 'failed';

				response_json($res);
				exit();
		}





		$login_name = trim(strval($this->input->post('login_name')));
		$user_name = trim(strval($this->input->post('user_name')));
		$user_password = trim(strval($this->input->post('user_password')));
		
		//check exitsed ccode
		
		if( $this->User_model->checkExistedUserLoginName($login_name)  ){

				$res = [];
				$res['result'] = 'code_duplicate';
			
				response_json($res);
				exit();



		}


		

		$upd = [];
		$upd['login_name']=$login_name;
		$upd['user_name']=$user_name;
		$upd['hash_password']=$this->Login_model->hashPassword($user_password);
		 

		$res = [];
		$res['result'] = 'failed';

		if(  $this->db->insert('user',$upd)   ){

			$res['result'] = 'success';

		}

		response_json($res);


	}



	public function editUser()
	{
		


		if(! checkCurrentIsWriteMenu('admin/user') ) { 
				$res = [];
				$res['result'] = 'admin_fail';
			
				response_json($res);
				exit();
		}

		//print_r($_POST);exit;

//Array ( [id] => 5 [login_name] => admin03 [new_user_password] => 345 [user_name] => 555 5555 )
		if(! has_keys_in_array(array('id','login_name','user_name','new_user_password'), $_POST) ){
				$res = [];
				$res['result'] = 'failed';

				response_json($res);
				exit();
		}





		$id = trim(strval($this->input->post('id')));
		$login_name = trim(strval($this->input->post('login_name')));
		$user_name = trim(strval($this->input->post('user_name')));
		$new_user_password = strval($this->input->post('new_user_password'));
		
		//check exitsed ccode
		
		if( $this->User_model->checkExistedUserLoginName($login_name,$id)  ){

				$res = [];
				$res['result'] = 'code_duplicate';
			
				response_json($res);
				exit();



		}


		

		$upd = [];
		$upd['login_name']=$login_name;
		$upd['user_name']=$user_name;
		if( $new_user_password!=="" && $new_user_password!==null && strlen(trim(strval($new_user_password))) > 0  ){
			$upd['hash_password']=$this->Login_model->hashPassword($new_user_password);
		}
		 

		$res = [];
		$res['result'] = 'failed';
		$this->db->where('id', $id);
		if(  $this->db->update('user',$upd)   ){

			$res['result'] = 'success';

		}

		response_json($res);


	}



	public function updateDeleteUser()
	{
		


		if(! checkCurrentIsWriteMenu('admin/user') ) { 
				$res = [];
				$res['result'] = 'admin_fail';
			
				response_json($res);
				exit();
		}

		//print_r($_POST);exit;

//Array ( [id] => 5 [login_name] => admin03 [new_user_password] => 345 [user_name] => 555 5555 )
		if(! has_keys_in_array(array('id'), $_POST) ){
				$res = [];
				$res['result'] = 'failed';

				response_json($res);
				exit();
		}





		$id = trim(strval($this->input->post('id')));
		 
		 

		

		$upd = [];
		$upd['is_deleted']=1;
		

		$res = [];
		$res['result'] = 'failed';
		$this->db->where('id', $id);
		if(  $this->db->update('user',$upd)   ){

			$res['result'] = 'success';

		}

		response_json($res);


	}



	public function updateIsActiveUser()
	{
		


		if(! checkCurrentIsWriteMenu('admin/user') ) { 
				$res = [];
				$res['result'] = 'admin_fail';
			
				response_json($res);
				exit();
		}

		if(! has_keys_in_array(array('id','value'), $_POST) ){
				$res = [];
				$res['result'] = 'failed';

				response_json($res);
				exit();
		}


		$id = trim(strval($this->input->post('id')));
		$value = trim(strval($this->input->post('value')));
		 
		 

		

		$upd = [];
		$upd['is_active']=$value;
		

		$res = [];
		$res['result'] = 'failed';
		$this->db->where('id', $id);
		if(  $this->db->update('user',$upd)   ){

			$res['result'] = 'success';

		}

		response_json($res);


	}





//===== PERMISSION ========/// START



	public function getPermissionTable()
	{
		 

		$cur_path_menu = 'admin/permission';
		verifyAccessMenu($cur_path_menu);
		$data = [];
		$data['is_editable'] = checkCurrentIsWriteMenu($cur_path_menu);



		$data['user_menu_perm_list'] = $this->User_model->getUserMenuPermissionList();
		$data['user_list'] = $this->User_model->getUserList();
		$data['menu_list'] = $this->User_model->getMenuList();

		$res = [];

		$res['result'] = 'success';
		$res['html'] = $this->load->view('admin/admin_permission_table', $data,true);

		response_json($res);


	}
	
	public function setPermissionUserMenu()
	{
		$user_id = $this->input->post('user_id');
		$menu_id = $this->input->post('menu_id');
		$permission = $this->input->post('permission');


		$res=[];

		//check existed
		$this->db->where('user_id', $user_id);
		$this->db->where('menu_id', $menu_id);

		if($p = $this->db->get('user_menu_permission')->row_array()){

			//update
			$this->db->where('id', $p['id']);
			if($this->db->update('user_menu_permission', array('permission'=>$permission))){
				$res['result'] = 'success';
				response_json($res);exit;



			}



		}else{

			//insert
			$ins= [] ;
			$ins['user_id'] = $user_id;
			$ins['menu_id'] = $menu_id;
			$ins['permission'] = $permission;

			if($this->db->insert('user_menu_permission', $ins)){
				$res['result'] = 'success';
				response_json($res);exit;

			}



		}



		$res['result'] = 'failed';
		response_json($res);exit;


	}

//===== PERMISSION ========/// END







//===== DEF ========/// START

	public function recreatedef()
	{
		
		$this->load->model('Def_model');

		//loop all file_category
		$this->db->select('file_category');
		$this->db->from('defexcel_file_category');

		if($file_categories = $this->db->get()->result_array()){

			foreach ($file_categories as $row) {
				$file_category = $row['file_category'];
				$res = $this->Def_model->create_def_of_file($file_category);
				echo "<h3>File Category: " . $file_category . "</h3>";
				echo msg_array_to_html($res);
				echo "<hr>";
			}
		}


		 


	}


}//end class