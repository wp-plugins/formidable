<?php
 
class FrmStatisticsController{
    function FrmStatisticsController(){
        add_action('admin_menu', array( &$this, 'menu' ), 24);
    }
    
    function menu(){
        global $frmpro_is_installed;
        if($frmpro_is_installed)
            return;
            
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Custom Displays', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Custom Displays', 'formidable') .'</span>', 'administrator', FRM_PLUGIN_NAME .'-entry-templates', array(&$this, 'list_displays'));
        
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Reports', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Reports', 'formidable') .'</span>', 'administrator', FRM_PLUGIN_NAME .'-reports', array(&$this, 'list_reports'));
    }
    
    function list_reports(){
        require(FRM_VIEWS_PATH . '/frm-statistics/list.php');
    }
    
    function list_displays(){
        require(FRM_VIEWS_PATH . '/frm-statistics/list_displays.php');
    }

}

?>