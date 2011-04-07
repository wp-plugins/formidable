<?php
/**
 * @package Formidable
 */
 
class FrmEntriesController{
    
    function FrmEntriesController(){
        add_action('admin_menu', array( &$this, 'menu' ), 20);
    }
    
    function menu(){
        global $frmpro_is_installed;
        if(!$frmpro_is_installed){
            add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' |'. __('Pro Entries', 'formidable'), __('Pro Entries', 'formidable'), 'administrator', FRM_PLUGIN_NAME.'-entries',array(&$this, 'list_entries'));
            //add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-entries', array(&$this, 'head'));
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
            return __('Please select a valid form', 'formidable');
        else if ($form->logged_in and !$user_ID){
            global $frm_settings;
            return $frm_settings->login_msg;
        }
            
        $form->options = stripslashes_deep(maybe_unserialize($form->options));
        if($form->logged_in and $user_ID and isset($form->options['logged_in_role']) and $form->options['logged_in_role'] != ''){
            if(FrmAppHelper::user_has_permission($form->options['logged_in_role']))
                return FrmEntriesController::get_form(FRM_VIEWS_PATH.'/frm-entries/frm-entry.php', $form, $title, $description);
            else{
                global $frm_settings;
                return $frm_settings->login_msg;
            }
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
        global $frm_form;

        if(!$form)
            $form = $frm_form->getAll('', ' ORDER BY name', ' LIMIT 1');
            
        $action = apply_filters('frm_show_new_entry_page', FrmAppHelper::get_param('action', 'new'), $form);
        $default_values = array('id' => '', 'form_name' => '', 'paged' => 1, 'form' => $form->id, 'form_id' => $form->id, 'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'action' => $action);
        
        $values['posted_form_id'] = FrmAppHelper::get_param('form_id');
        if (!is_numeric($values['posted_form_id']))
            $values['posted_form_id'] = FrmAppHelper::get_param('form');

        if ($form->id == $values['posted_form_id']){ //if there are two forms on the same page, make sure not to submit both
            foreach ($default_values as $var => $default){
                $values[$var] = FrmAppHelper::get_param($var, $default);
                unset($var);
                unset($default);
            }
        }else{
            foreach ($default_values as $var => $default){
                $values[$var] = $default;
                unset($var);
                unset($default);
            }
        }

        return $values;
    }
    
}
?>