<?php
/**
 * @package Formidable
 */
 
class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( &$this, 'menu' ), 26);
    }

    function menu(){
        add_submenu_page('formidable', 'Formidable | '. __('Global Settings', 'formidable'), __('Global Settings', 'formidable'), 'frm_change_settings', 'formidable-settings', array(&$this, 'route'));
    }

    function display_form(){
      global $frm_settings, $frm_ajax_url, $frmpro_is_installed, $frm_update;
      $frm_roles = FrmAppHelper::frm_capabilities();
      
      $uploads = wp_upload_dir();
      $target_path = $uploads['basedir'] . "/formidable/css";
      $sections = apply_filters('frm_add_settings_section', array());
      
      require(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }

    function process_form(){
      global $frm_settings, $frm_ajax_url, $frmpro_is_installed, $frm_update;

      //$errors = $frm_settings->validate($_POST,array());
      $errors = array();
      $frm_settings->update($_POST);
      
      if( empty($errors) ){
        $frm_settings->store();
        $message = __('Settings Saved', 'formidable');
      }
      $frm_roles = FrmAppHelper::frm_capabilities();
      $sections = apply_filters('frm_add_settings_section', array());
      
      require(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }
    
    function route(){
        $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = FrmAppHelper::get_param($action);
        if($action == 'process-form')
            return $this->process_form();
        else
            return $this->display_form();
    }
}
