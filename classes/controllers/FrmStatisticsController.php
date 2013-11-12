<?php
if(!defined('ABSPATH')) die(__('You are not allowed to call this page directly.', 'formidable'));

if(class_exists('FrmStatisticsController'))
    return;

class FrmStatisticsController{
    function FrmStatisticsController(){
        add_action('admin_menu', 'FrmStatisticsController::menu', 24);
    }
    
    public static function menu(){
        global $frm_vars;
        if($frm_vars['pro_is_installed'])
            return;
            
        add_submenu_page('formidable', 'Formidable | '. __('Custom Displays', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Custom Displays', 'formidable') .'</span>', 'administrator', 'formidable-entry-templates', 'FrmStatisticsController::list_displays');
        
        add_submenu_page('formidable', 'Formidable | '. __('Reports', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Reports', 'formidable') .'</span>', 'administrator', 'formidable-reports', 'FrmStatisticsController::list_reports');
    }
    
    public static function list_reports(){
        $form = FrmAppHelper::get_param('form', false);
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-statistics/list.php');
    }
    
    public static function list_displays(){
        $form = FrmAppHelper::get_param('form', false);
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-statistics/list_displays.php');
    }

}
