<?php
/**
 * @package Formidable
 */
 
class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( &$this, 'menu' ), 26);
        add_filter('frm_nav_array', array( &$this, 'frm_nav'), 30);
        //add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-settings', array(&$this, 'head'));
    }

    function menu(){
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Settings', 'formidable'), __('Settings', 'formidable'), 'frm_change_settings', FRM_PLUGIN_NAME.'-settings', array(&$this, 'route'));
    }
    
    function frm_nav($nav=array()){
        if(current_user_can('frm_change_settings'))
            $nav[FRM_PLUGIN_NAME.'-settings'] = __('Settings', 'formidable');
        
        return $nav;
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
        $action = FrmAppHelper::get_param('action');
        if($action == 'process-form')
            return $this->process_form();
        else
            return $this->display_form();
    }
}
?>
