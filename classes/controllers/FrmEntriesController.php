<?php

class FrmEntriesController{
    var $views;
    
    function FrmEntriesController(){
        add_action('admin_menu', array( $this, 'menu' ), 20);
    }
    
    function menu(){
        global $frmpro_is_installed;
        if(!$frmpro_is_installed){
            add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' |'. __('Pro Entries', FRM_PLUGIN_NAME), __('Pro Entries', FRM_PLUGIN_NAME), 'administrator', FRM_PLUGIN_NAME.'-entries',array($this,'list_entries'));
            //add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-entries', array($this,'head'));
        }
    }
    
    function list_entries(){
        require_once(FRM_VIEWS_PATH.'/frm-entries/list.php');
    }
    
    function show_form($id='', $key='', $title=false, $description=false){
        global $frm_form, $user_ID;
        if ($id) $form = $frm_form->getOne($id);
        else if ($key) $form = $frm_form->getOne($key);
        if (!$form or $form->is_template or $form->status == 'draft')
            return __('Please select a valid form', FRM_PLUGIN_NAME);
        else if ($form->logged_in and !$user_ID){
            global $frm_settings;
            return $frm_settings->login_msg;
        }else
            return FrmEntriesController::get_form(FRM_VIEWS_PATH.'/frm-entries/frm-entry.php', $form, $title, $description);
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
            
        $action = apply_filters('frm_show_new_entry_page', $frm_app_controller->get_param('action', 'new'), $form);
        $default_values = array('id' => '', 'form_name' => '', 'paged' => 1, 'form' => $form->id, 'form_id' => $form->id, 'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'action' => $action);
        
        $posted_form_id = $frm_app_controller->get_param('form_id');
        if ($posted_form_id == '')
            $posted_form_id = $frm_app_controller->get_param('form');
            
        if ($form->id == $posted_form_id){ //if there are two forms on the same page, make sure not to submit both
            foreach ($default_values as $var => $default)
            $values[$var] = $frm_app_controller->get_param($var, $default);
        }else{
            foreach ($default_values as $var => $default)
                $values[$var] = $default;
        }

        return $values;
    }
    
}
?>