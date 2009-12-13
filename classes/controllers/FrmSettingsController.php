<?php

class FrmSettingsController{
    function FrmSettingsController(){
        add_action('admin_menu', array( $this, 'menu' ), 25);
        add_action('admin_head-'.FRM_PLUGIN_NAME.'-settings', array($this,'head'));
        $this->views = FRM_VIEWS_PATH.'/frm-settings/';
    }

    function menu(){
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | Settings', 'Settings', 8, FRM_PLUGIN_NAME.'-settings', array($this,'route'));
        //add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. FRM_PLUGIN_TITLE . ' Pro', FRM_PLUGIN_TITLE . ' Pro', 8, FRM_PLUGIN_NAME.'-pro-settings', array($this,'pro_settings'));
    }

    function head(){
      $css_file = 'frm_admin.css';
      $js_file  = 'list-items.js';
      require_once(FRM_VIEWS_PATH . '/shared/admin_head.php');
    }

    function display_form(){
      global $frm_settings;

      require_once($this->views . 'form.php');
    }

    function process_form(){
      global $frm_settings;

      $errors = $frm_settings->validate($_POST,$errors);

      $frm_settings->update($_POST);

      if( empty($errors) ){
        $frm_settings->store();
        $message = 'Settings Saved';
      }

      require_once($this->views . 'form.php');
    }
    
    function pro_settings(){
        global $frm_utils, $frmpro_is_installed, $frm_app_controller;
        
        $action = $frm_app_controller->get_param('action');
        $errors = array();
        
        // variables for the field and option names 
        $frmpro_username = 'frmpro_username';
        $frmpro_password = 'frmpro_password';
        $hidden_field_name = 'frm_update_options';

        // Read in existing option value from database
        $frmpro_username_val = get_option( $frmpro_username );
        $frmpro_password_val = get_option( $frmpro_password );

        if($action == 'force-pro-reinstall'){
          $frm_utils->download_and_install_pro($frmpro_username_val, $frmpro_password_val, true);
          $message = _e(FRM_PLUGIN_TITLE .' Pro Successfully Reinstalled.', FRM_PLUGIN_NAME );
        }else if($action == 'pro-uninstall'){
          $frm_utils->uninstall_pro();
          $message = _e(FRM_PLUGIN_TITLE .' Pro Successfully Uninstalled.', FRM_PLUGIN_NAME );
        }else{
          // See if the user has posted us some information
          // If they did, this hidden field will be set to 'Y'
          if( $frm_app_controller->get_param('$hidden_field_name') == 'Y' ){
            // Validate This
            // This is where the remote username / password will be validated

            // Read their posted value
            $prlipro_username_val = stripslashes($_POST[ $prlipro_username ]);
            $prlipro_password_val = stripslashes($_POST[ $prlipro_password ]);

            $user_type = $prli_utils->get_pro_user_type($prlipro_username_val, $prlipro_password_val);
            if(empty($user_type))
              $errors[] = "Your user account couldn't be validated...";

            if( count($errors) > 0 ){
              require(FRM_VIEWS_PATH.'/shared/errors.php');
            }else{
              // Save the posted value in the database
              update_option( $frmpro_username, $frmpro_username_val );
              update_option( $frmpro_password, $frmpro_password_val );

              // Put an options updated message on the screen
              $message = $prli_utils->download_and_install_pro($prlipro_username_val, $prlipro_password_val);

              $message = (($message == 'SUCCESS')?FRM_PLUGIN_TITLE. 'has been installed':$message);
            }
          }
        }
        require_once($this->views . 'pro-settings.php');
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
