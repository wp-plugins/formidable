<?php

class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( $this, 'menu' ), 25);
        add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-settings', array($this,'head'));
    }

    function menu(){
        global $frm_update;
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | Settings', 'Settings', 8, FRM_PLUGIN_NAME.'-settings', array($this,'route'));
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. FRM_PLUGIN_TITLE . ' Pro', FRM_PLUGIN_TITLE . ' Pro', 8, FRM_PLUGIN_NAME.'-pro-settings', array($frm_update,'pro_cred_form'));
    }

    function head(){
        global $frm_settings;
        $css_file = array($frm_settings->theme_css,  FRM_URL. '/css/frm_admin.css');
        $js_file  = 'jquery/jquery-ui-themepicker.js';
      ?>
        <link type="text/css" rel="stylesheet" href="http://jqueryui.com/themes/base/ui.all.css" />
          <script>
          jQuery(document).ready(function($){
            $('#frm_switcher').themeswitcher();
          });
          </script>
        <?php
        require_once(FRM_VIEWS_PATH . '/shared/head.php');
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
