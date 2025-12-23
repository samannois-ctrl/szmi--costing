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

    // แสดงเมนูเป็น HTML สำหรับ Top Menu (Horizontal)
    private function renderMenu($menuTree,$current_part_menu,$is_child = 0) {
        $html = "";
        foreach ($menuTree as $menu) {

            if(empty($menu['children'])){
                // Menu item without children
                if($is_child == 1){
                    // Dropdown item
                    $active_class = '';
                    if(strtolower($current_part_menu) == strtolower($menu['path'])){
                        $active_class = 'active';
                    }
                    $html .= '<li><a class="dropdown-item '.$active_class.'" href="'.base_url($menu['path']).'">'.$menu['title'].'</a></li>';
                    
                }else{
                    // Top level menu item
                    $active_class = '';
                    if($current_part_menu == strtolower($menu['path']) ){
                        $active_class = 'active';
                    }
                    
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link '.$active_class.'" href="'.base_url($menu['path']).'">'.$menu['title'].'</a>';
                    $html .= '</li>';
                }

            }else{
                // Menu item with children (dropdown)
                $active_class = '';
                foreach ($menu['children'] as $child) {
                    if(strtolower($current_part_menu) == strtolower($child['path'])){
                        $active_class = 'active';
                        break;
                    }
                }
               
                $html .= '<li class="nav-item dropdown">';
            $html .= '<a class="nav-link dropdown-toggle '.$active_class.'" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            $html .= $menu['title'];
                $html .= '</a>';
                $html .= '<ul class="dropdown-menu">';
                
                $html .= $this->renderMenu($menu['children'],$current_part_menu,1);

                $html .= '</ul>';
                $html .= '</li>';
            }
        }
         
        return $html;
    }


}//end class