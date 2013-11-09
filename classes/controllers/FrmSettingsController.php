<?php
/**
 * @package Formidable
 */

if(!defined('ABSPATH')) die(__('You are not allowed to call this page directly.', 'formidable'));

if(class_exists('FrmSettingsController'))
    return;
 
class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', 'FrmSettingsController::menu', 26);
    }

    public static function menu(){
        add_submenu_page('formidable', 'Formidable | '. __('Global Settings', 'formidable'), __('Global Settings', 'formidable'), 'frm_change_settings', 'formidable-settings', 'FrmSettingsController::route');
    }

    public static function display_form(){
      global $frm_settings, $frmpro_is_installed;
      
      $frm_update = new FrmUpdatesController();
      $frm_roles = FrmAppHelper::frm_capabilities();
      
      $uploads = wp_upload_dir();
      $target_path = $uploads['basedir'] . "/formidable/css";
      $sections = apply_filters('frm_add_settings_section', array());
      
      require(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }

    public static function process_form($stop_load=false){        
        global $frm_settings, $frmpro_is_installed;
        
        if(!isset($_POST['process_form']) or !wp_verify_nonce($_POST['process_form'], 'process_form_nonce'))
            wp_die($frm_settings->admin_permission);
        
        global $frm_settings_routed;
        if(!$frm_settings_routed){
            $frm_update = new FrmUpdatesController();
            //$errors = $frm_settings->validate($_POST,array());
            $errors = array();
            $frm_settings->update($_POST);

            if( empty($errors) ){
                $frm_settings->store();
                $message = __('Settings Saved', 'formidable');
            }
        }
        
        if($stop_load == 'stop_load'){
            $frm_settings_routed = true;
            return;
        }
        
        $frm_roles = FrmAppHelper::frm_capabilities();
        $sections = apply_filters('frm_add_settings_section', array());
      
        require(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }
    
    public static function route($stop_load=false){
        $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = FrmAppHelper::get_param($action);
        if($action == 'process-form')
            return self::process_form($stop_load);
        else if($stop_load != 'stop_load')
            return self::display_form();
    }
}
