<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties] 
class Menu_model extends CI_Model {



	public function __construct()
	{
		parent::__construct();
   
	}

    public function get_html_menu_header($current_part_menu)
    {
        $this->db->where('is_active',1);
        $this->db->order_by('parent_id');
        $this->db->order_by('seq');
        //build array s
        if( $res = $this->db->get('menu')->result_array() ){

             
            $menu_tree = $this->buildMenuTree($res,null);

            //print_r($menu_tree);
            return $this->renderMenu($menu_tree,$current_part_menu);
        }

    }
    

    // สร้าง tree จาก array แบบ recursive
    private function buildMenuTree($menus, $parentId = null) {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parentId) {
                $children = $this->buildMenuTree($menus, $menu['id']);
                if ($children) {
                    $menu['children'] = $children;
                }
                $branch[] = $menu;
            }
        }
        return $branch;
    }

    // แสดงเมนูเป็น HTML แบบ nested <ul>
    private function renderMenu($menuTree,$current_part_menu,$is_child = 0) {
        $html = "";
        foreach ($menuTree as $menu) {


            if(empty($menu['children'])){

                if($is_child == 1){
                    $active_dropdown_child = '';
                    if(strtolower($current_part_menu) == strtolower($menu['path'])){
                        $active_dropdown_child = 'active';
                    }
                    $html .= '<a class="dropdown-item '.$active_dropdown_child.' " href="'.base_url($menu['path']).'">'.$menu['title'].'</a>';
                    
                }else{

                    $html .= '<li class="nav-item d-none d-sm-inline-block ">';

                    if($current_part_menu == strtolower($menu['path']) ){
                        $html .= '<span class="nav-link active" >'.$menu['title'].'</span>';
                    }else{
                        $html .= '<a href="'.base_url($menu['path']).'" class="nav-link">'.$menu['title'].'</a>';
                    }

                    $html .= "</li>";
                }

            }else{
                $active_dropdown_header = '';
                foreach ($menu['children'] as $child) {
                    if(strtolower($current_part_menu) == strtolower($child['path'])){
                        $active_dropdown_header = 'active';
                        break;
                    }
                }
               
                $html .= '<li class="nav-item dropdown">';
                $html .= '<a class="nav-link dropdown-toggle '. $active_dropdown_header.' " href="#" id="menu-appform" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$menu['title'].'</a>';

                $html .= '<div class="dropdown-menu" aria-labelledby="menu-appform">';



                 
                $html .= $this->renderMenu($menu['children'],$current_part_menu,1);



                



            }








  





        }
         
        return $html;
    }




}//end class