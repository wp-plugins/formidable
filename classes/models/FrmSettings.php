<?php
class FrmSettings{
    // Page Setup Variables
    var $preview_page_id;
    var $preview_page_id_str;
    var $lock_keys;
    
    var $custom_style;
    var $custom_stylesheet;
    var $accordion_js;
    
    var $success_msg;
    var $failed_msg;
    var $submit_value;
    var $login_msg;
    
    var $email_to;
    
    var $frm_view_forms;
    var $frm_edit_forms;
    var $frm_delete_forms;
    var $frm_change_settings;
    var $frm_view_entries;
    var $frm_create_entries;
    var $frm_edit_entries;
    var $frm_delete_entries;
    var $frm_view_reports;
    var $frm_edit_displays;


    function FrmSettings(){
        $this->set_default_options();
    }

    function set_default_options(){
        if(!isset($this->preview_page_id))
          $this->preview_page_id = 0;
          
        $this->preview_page_id_str = 'frm-preview-page-id';
        
        if(!isset($this->lock_keys))
            $this->lock_keys = true;
        
        if(!isset($this->custom_style))
            $this->custom_style = true;
        if(!isset($this->custom_stylesheet))
            $this->custom_stylesheet = false;
        if(!isset($this->accordion_js))
            $this->accordion_js = false;
            
        if(!isset($this->success_msg))
            $this->success_msg = __('Your responses were successfully submitted. Thank you!', FRM_PLUGIN_NAME);
        $this->success_msg = stripslashes($this->success_msg);
        
        if(!isset($this->failed_msg))
            $this->failed_msg = __('We\'re sorry. There was an error processing your responses.', FRM_PLUGIN_NAME);
        $this->failed_msg = stripslashes($this->failed_msg);
        
        if(!isset($this->submit_value))
            $this->submit_value = __('Submit', FRM_PLUGIN_NAME);
        
        if(!isset($this->login_msg))    
            $this->login_msg = __('You do not have permission to view this form.', FRM_PLUGIN_NAME);
        $this->login_msg = stripslashes($this->login_msg);
        
        $this->email_to = get_option('admin_email');
        
        $frm_roles = FrmAppHelper::frm_capabilities();
        foreach($frm_roles as $frm_role => $frm_role_description){
            if(!isset($this->$frm_role))
                $this->$frm_role = 'administrator';
        }
    }

    function validate($params,$errors){   
        //if($params[ $this->preview_page_id_str ] == 0)
        //  $errors[] = "The Preview Page Must Not Be Blank.";
        $errors = apply_filters( 'frm_validate_settings', $errors, $params );
        return $errors;
    }

    function update($params){
        global $wp_roles;
        $this->preview_page_id = (int)$params[ $this->preview_page_id_str ];
        $this->lock_keys = isset($params['frm_lock_keys']) ? 1 : 0;
        
        $this->custom_style = isset($params['frm_custom_style']) ? 1 : 0;
        $this->custom_stylesheet = isset($params['frm_custom_stylesheet']) ? 1 : 0;
        $this->accordion_js = isset($params['frm_accordion_js']) ? 1 : 0;
        
        $this->success_msg = isset($params['frm_success_msg']) ? $params['frm_success_msg'] : __('Your responses were successfully submitted. Thank you!', 'formidable');
        $this->failed_msg = isset($params['frm_failed_msg']) ? $params['frm_failed_msg'] : __('We\'re sorry. There was an error processing your responses.', 'formidable');
        $this->submit_value = isset($params['frm_submit_value']) ? $params['frm_submit_value'] : __('Submit', 'formidable');
        $this->login_msg = isset($params['frm_login_msg']) ? $params['frm_login_msg'] : __('You must log in', 'formidable');
        
        //update roles
        $frm_roles = FrmAppHelper::frm_capabilities();
        $roles = get_editable_roles();
        foreach($frm_roles as $frm_role => $frm_role_description){
            $this->$frm_role = isset($params[$frm_role]) ? $params[$frm_role] : 'administrator';
            
            foreach ($roles as $role => $details){
                if($this->$frm_role == $role or ($this->$frm_role == 'editor' and $role == 'administrator') or ($this->$frm_role == 'author' and in_array($role, array('administrator','editor'))) or ($this->$frm_role == 'contributor' and in_array($role, array('administrator','editor','author'))) or $this->$frm_role == 'subscriber')
    			    $wp_roles->add_cap( $role, $frm_role );	
    			else
    			    $wp_roles->remove_cap( $role, $frm_role );
    		}	
		}
        
        do_action( 'frm_update_settings', $params );
    }

    function store(){
        // Save the posted value in the database
        update_option( 'frm_options', $this);

        do_action( 'frm_store_settings' );
    }
  
}
?>