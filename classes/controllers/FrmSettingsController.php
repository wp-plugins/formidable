<?php

class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( $this, 'menu' ), 25);
        add_action('admin_head-'.FRM_PLUGIN_NAME.'-settings', array($this,'head'));
    }

    function menu(){
        global $frm_update;
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | Settings', 'Settings', 8, FRM_PLUGIN_NAME.'-settings', array($this,'route'));
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. FRM_PLUGIN_TITLE . ' Pro', FRM_PLUGIN_TITLE . ' Pro', 8, FRM_PLUGIN_NAME.'-pro-settings', array($frm_update,'pro_cred_form'));
    }

    function head(){
      $css_file = 'frm_admin.css';
      $js_file  = 'list-items.js';
      require_once(FRM_VIEWS_PATH . '/shared/admin_head.php');
    }

    function display_form(){
      global $frm_settings;
      require_once(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }

    function process_form(){
      global $frm_settings;

      //$errors = $frm_settings->validate($_POST,array());
      $errors = array();
      $frm_settings->update($_POST);

      if( empty($errors) ){
        $frm_settings->store();
        $message = 'Settings Saved';
      }

      require_once(FRM_VIEWS_PATH . '/frm-settings/form.php');
    }
    
    function route(){
        global $frm_app_controller;
        $action = $frm_app_controller->get_param('action');
        if($action=='process-form')
            return $this->process_form();
        else
            return $this->display_form();
    }
}
?>
