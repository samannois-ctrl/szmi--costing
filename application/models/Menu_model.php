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

    // แสดงเมนูเป็น HTML สำหรับ Argon Dashboard Sidebar
    private function renderMenu($menuTree,$current_part_menu,$is_child = 0) {
        $html = "";
        foreach ($menuTree as $menu) {

            if(empty($menu['children'])){
                // Menu item without children
                if($is_child == 1){
                    // Submenu item
                    $active_class = '';
                    if(strtolower($current_part_menu) == strtolower($menu['path'])){
                        $active_class = 'active';
                    }
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link '.$active_class.'" href="'.base_url($menu['path']).'">';
                    $html .= '<span class="sidenav-mini-icon"> • </span>';
                    $html .= '<span class="sidenav-normal">'.$menu['title'].'</span>';
                    $html .= '</a>';
                    $html .= '</li>';
                    
                }else{
                    // Top level menu item
                    $active_class = '';
                    if($current_part_menu == strtolower($menu['path']) ){
                        $active_class = 'active';
                    }
                    
                    $html .= '<li class="nav-item">';
                    $html .= '<a class="nav-link '.$active_class.'" href="'.base_url($menu['path']).'">';
                    $html .= '<div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">';
                    $html .= '<i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>';
                    $html .= '</div>';
                    $html .= '<span class="nav-link-text ms-1">'.$menu['title'].'</span>';
                    $html .= '</a>';
                    $html .= '</li>';
                }

            }else{
                // Menu item with children (collapsible)
                $active_class = '';
                $show_class = '';
                foreach ($menu['children'] as $child) {
                    if(strtolower($current_part_menu) == strtolower($child['path'])){
                        $active_class = 'active';
                        $show_class = 'show';
                        break;
                    }
                }
                
                $collapse_id = 'collapse'.str_replace(' ', '', $menu['title']);
               
                $html .= '<li class="nav-item">';
                $html .= '<a data-bs-toggle="collapse" href="#'.$collapse_id.'" class="nav-link '.$active_class.'" aria-controls="'.$collapse_id.'" role="button" aria-expanded="false">';
                $html .= '<div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center d-flex align-items-center justify-content-center me-2">';
                $html .= '<i class="ni ni-folder-17 text-warning text-sm opacity-10"></i>';
                $html .= '</div>';
                $html .= '<span class="nav-link-text ms-1">'.$menu['title'].'</span>';
                $html .= '</a>';

                $html .= '<div class="collapse '.$show_class.'" id="'.$collapse_id.'">';
                $html .= '<ul class="nav ms-4 ps-3">';
                
                $html .= $this->renderMenu($menu['children'],$current_part_menu,1);

                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
         
        return $html;
    }


}//end class