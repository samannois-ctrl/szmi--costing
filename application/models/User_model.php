<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class User_model extends CI_Model {



	public function __construct()

	{
		parent::__construct();

	 

	}

 
 	public function getUserList()
 	{
 		$this->db->select('`id`,`login_name`,`user_name`,`is_deleted`,`is_active`,`last_login_dtm`,`created`,`updated`');
 		$this->db->where('is_deleted',0);

 		if(  $res = $this->db->get('user')->result_array() ){

 			return $res;

 		}

 		return [];
 	}
 


 	public function getMenuList( )
 	{
 		$this->db->select('*');
 		$this->db->order_by('seq');

 		if(  $res = $this->db->get('menu')->result_array() ){

 			return $res;

 		}

 		return []; 	
 	}


 	public function getUserMenuPermissionExisted( )
 	{
 		$this->db->select('*');

 		if(  $res = $this->db->get('user_menu_permission')->result_array() ){

 			return $res;

 		}

 		return []; 	
 	}

 	public function getUserMenuPermissionList()
 	{


 		$user_list = $this->getUserList();

 		$menu_list = $this->getMenuList();


 		//init user_menu_perm

 		$user_menu_perm = [];

 		foreach ($user_list as $k => $user) {
 			 
 			$menus = [];
 
 			foreach ($menu_list as $b => $menu) {
 				 
 				$menus[$menu['id']] = '0';

 			}
 
 			$user_menu_perm[$user['id']] = $menus;


 		}

// print_r_html($user_menu_perm);
 
 		$perm = $this->getUserMenuPermissionExisted();
//print_r_html($perm); 
 		foreach ($perm as $k => $v) {
 			
 			$user_id = $v['user_id'];
 			$menu_id = $v['menu_id'];
 			$permission = $v['permission'];




 			if(isset($user_menu_perm[$user_id][$menu_id])){
 				// echo "found";
 				$user_menu_perm[$user_id][$menu_id] = $permission;
 			}




 		}


// print_r_html($user_menu_perm);exit;


 		return $user_menu_perm;
 	}
 
 	public function getProfile($id)
 	{
 		$this->db->select('`id`,`login_name`,`user_name`, `is_deleted`,`is_active`,`last_login_dtm`,`created`,`updated`');
 		$this->db->where('id',$id);

 		if(  $res = $this->db->get('user')->row_array() ){

 			return $res;

 		}

 		return [];
 	}

	public function checkExistedUserLoginName($new_code,$except_id=null)
	{
		$this->db->where('login_name',$new_code);

		if(!is_null($except_id)){

				$this->db->where('id <> '.$except_id);
	
		}



		if( $row = $this->db->get('user')->row_array()){
			if(count($row) > 0){
				return 1;
			}
		}

		return 0;
	}




}//end class