<?php

class FrmEntriesController{
    var $views;
    
    function FrmEntriesController(){
        //add_action('admin_menu', array( $this, 'menu' ));
        $this->views = FRM_VIEWS_PATH.'/frm-entries/';
    }
    
    function menu(){
        global $frmpro_is_installed;
        if(!$frmpro_is_installed)
            add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | Pro Entries', 'Pro Entries', 8, FRM_PLUGIN_NAME.'-entries',array($this,'list'));
    }
    
    function list_entries(){
        require_once($this->views .'list.php');
    }
    
    function show_form($id='', $key='', $title=false, $description=false){
        global $frm_form, $user_ID;
        if ($id) $form = $frm_form->getOne($id);
        else if ($key) $form = $frm_form->getOneByKey($key);
        if (!$form or $form->is_template or $form->status == 'draft')
            return 'Please select a valid form';
        else if ($form->logged_in and !$user_ID)
            return 'You must log in';
        else 
            return FrmEntriesController::get_form(FRM_VIEWS_PATH.'/frm-entries/frm-entry.php', $form, $title, $description);
    }
    
    function new_entry($form){
        global $frm_form, $frm_field, $frm_entry, $frm_entry_meta, $frm_recaptcha_enabled, $user_ID;
        $fields = $frm_field->getAll("fi.form_id='$form->id'", ' ORDER BY field_order');
        $values = FrmEntriesHelper::setup_new_vars($fields);
        $form_name = $form->name;

        $params = $this->get_params($form);
        $message = '';
        $errors = '';

        do_action('frm_display_form_action', $params, $fields, $form, $title, $description);
        if (apply_filters('frm_continue_to_new', true)){
            $values = FrmEntriesHelper::setup_new_vars($fields);
            require_once($this->views .'new.php');
        }
    }
    
    function create($form){
        global $frm_form, $frm_field, $frm_entry, $frm_entry_meta, $frm_recaptcha_enabled, $user_ID;
        $fields = $frm_field->getAll("fi.form_id='$form->id'", ' ORDER BY field_order');
        $values = FrmEntriesHelper::setup_new_vars($fields);
        $form_name = $form->name;

        $failed_message = "We're sorry. There was an error processing your responses.";
        $saved_message = "Your responses were successfully submitted. Thank you!";

        $params = $this->get_params($form);
        $message = '';

        $errors = $frm_entry->validate($_POST);

        if( count($errors) > 0 ){
            $values = FrmEntriesHelper::setup_new_vars($fields);
            require_once($this->views.'new.php');
        }else{
            do_action('frm_validate_form_creation', $params, $fields, $form, $title, $description);
            if (apply_filters('frm_continue_to_create', true)){
                if ($frm_entry->create( $_POST ))
                    echo $saved_message;
                else
                    echo $failed_message;
            }
        }
    }
    
    function get_form($filename, $form, $title, $description) {
        if (is_file($filename)) {
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
    
    function get_params($form=null){
        global $frm_app_controller, $frm_form;

        if(!$form)
            $form = $frm_form->getAll('',' ORDER BY name',' LIMIT 1');
        $action = apply_filters('frm_show_new_entry_page','new',$form);
        $values = array();
        foreach (array('id' => '','form_name' => '', 'paged' => 1,'form' => $form->id,'field_id' => '', 'search' => '','sort' => '','sdir' => '', 'form' => $form->id, 'action' => $action) as $var => $default)
            $values[$var] = $frm_app_controller->get_param($var, $default);

        return $values;
    }

    function route($form=false){
        global $frm_app_controller;
        $action = $frm_app_controller->get_param('action');
        if (!$form)
            $form = $frm_app_controller->get_param('form');
        $action = apply_filters('frm_show_new_entry_page', $action, $form);
        if($action=='create')
            return $this->create($form);
        else
            return $this->new_entry($form);
    }
    
}
?>