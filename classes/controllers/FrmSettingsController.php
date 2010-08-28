<?php

class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( &$this, 'menu' ), 25);
        add_action('admin_menu', array( &$this, 'pro_menu' ), 19);
        //add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-settings', array($this,'head'));
    }

    function menu(){
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Settings', 'formidable'), __('Settings', 'formidable'), 'frm_change_settings', FRM_PLUGIN_NAME.'-settings', array($this,'route'));
    }
    
    function pro_menu(){
        global $frm_update;
        if (IS_WPMU and !is_site_admin() and get_site_option($frm_update->pro_wpmu_store))
            return;
            
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. FRM_PLUGIN_TITLE . ' Pro', FRM_PLUGIN_TITLE . ' Pro', 'administrator', FRM_PLUGIN_NAME.'-pro-settings', array($frm_update,'pro_cred_form'));
    }

    function display_form(){
      global $frm_settings, $frm_ajax_url;
      $frm_roles = FrmAppHelper::frm_capabilities();
      require_once(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }

    function process_form(){
      global $frm_settings, $frm_ajax_url;

      //$errors = $frm_settings->validate($_POST,array());
      $errors = array();
      $frm_settings->update($_POST);
      
      if( empty($errors) ){
        $frm_settings->store();
        $message = __('Settings Saved', 'formidable');
      }
      $frm_roles = FrmAppHelper::frm_capabilities();
      require_once(FRM_VIEWS_PATH . '/frm-settings/form.php');
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
