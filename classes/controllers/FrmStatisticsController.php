<?php
 
class FrmStatisticsController{
    function FrmStatisticsController(){
        add_action('admin_menu', 'FrmStatisticsController::menu', 24);
    }
    
    public static function menu(){
        global $frmpro_is_installed;
        if($frmpro_is_installed)
            return;
            
        add_submenu_page('formidable', 'Formidable | '. __('Custom Displays', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Custom Displays', 'formidable') .'</span>', 'administrator', 'formidable-entry-templates', 'FrmStatisticsController::list_displays');
        
        add_submenu_page('formidable', 'Formidable | '. __('Reports', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Reports', 'formidable') .'</span>', 'administrator', 'formidable-reports', 'FrmStatisticsController::list_reports');
    }
    
    public static function list_reports(){
        $form = FrmAppHelper::get_param('form', false);
        require(FRM_VIEWS_PATH . '/frm-statistics/list.php');
    }
    
    public static function list_displays(){
        $form = FrmAppHelper::get_param('form', false);
        require(FRM_VIEWS_PATH . '/frm-statistics/list_displays.php');
    }

}
