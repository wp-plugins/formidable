<?php
/**
 * @package Formidable
 */
 
class FrmEntriesController{
    
    function FrmEntriesController(){
        add_action('admin_menu', array( &$this, 'menu' ), 20);
        add_filter('frm_redirect_msg', array( &$this, 'delete_entry_before_redirect'), 50, 3);
        add_action('frm_after_create_entry', array(&$this, 'delete_entry_after_save'), 100, 2);
        add_filter('frm_email_value', array(&$this, 'filter_email_value'), 10, 3);
    }
    
    function menu(){
        global $frmpro_is_installed;
        if(!$frmpro_is_installed){
            add_submenu_page('formidable', 'Formidable |'. __('Entries', 'formidable'), '<span style="opacity:.5;filter:alpha(opacity=50);">'. __('Entries', 'formidable') .'</span>', 'administrator', 'formidable-entries',array(&$this, 'list_entries'));
        }
    }
    
    function list_entries(){
        global $frm_form, $frm_entry;
        $form_select = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name');
        $form_id = FrmAppHelper::get_param('form', false);
        if($form_id)
            $form = $frm_form->getOne($form_id);
        else
            $form = (isset($form_select[0])) ? $form_select[0] : 0;
        
        if($form)
            $entry_count = $frm_entry->getRecordCount($form->id);
            
        require(FRM_VIEWS_PATH.'/frm-entries/list.php');
    }
    
    function show_form($id='', $key='', $title=false, $description=false){
        global $frm_form, $user_ID, $frm_settings, $post;
        if ($id) $form = $frm_form->getOne((int)$id);
        else if ($key) $form = $frm_form->getOne($key);
        
        $form = apply_filters('frm_pre_display_form', $form);
        
        if(!$form or 
            (($form->is_template or $form->status == 'draft') and !isset($_GET) and !isset($_GET['form']) and 
                (!isset($_GET['preview']) or $post and $post->ID != $frm_settings->preview_page_id))
            ){
            return __('Please select a valid form', 'formidable');
        }else if ($form->logged_in and !$user_ID){
            global $frm_settings;
            return $frm_settings->login_msg;
        }

        $form->options = stripslashes_deep(maybe_unserialize($form->options));
        if($form->logged_in and $user_ID and isset($form->options['logged_in_role']) and $form->options['logged_in_role'] != ''){
            if(FrmAppHelper::user_has_permission($form->options['logged_in_role'])){
                return FrmEntriesController::get_form(FRM_VIEWS_PATH.'/frm-entries/frm-entry.php', $form, $title, $description);
            }else{
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
    
    //Delete entry if it shouldn't be saved before redirect
    function delete_entry_before_redirect($redirect_msg, $atts){
        $this->_delete_entry($atts['entry_id'], $atts['form']);
        return $redirect_msg;
    }
    
    //Delete entry if not redirected
    function delete_entry_after_save($entry_id, $form_id){
        global $frm_form;
        $form = $frm_form->getOne($form_id);
            
        $this->_delete_entry($entry_id, $form);
    }
    
    private function _delete_entry($entry_id, $form){
        if(!$form)
            return;
        
        $form->options = maybe_unserialize($form->options);
        if(isset($form->options['no_save']) and $form->options['no_save']){
            global $frm_entry;
            $frm_entry->destroy( $entry_id );
        }
    }
    
    function &filter_email_value($value, $meta, $entry){
        global $frm_field;
        
        $field = $frm_field->getOne($meta->field_id);
        if(!$field)
            return $value; 
            
        $value = $this->filter_display_value($value, $field);
        return $value;
    }
    
    function &filter_display_value($value, $field){
        $field->field_options = maybe_unserialize($field->field_options);
        
        if(!in_array($field->type, array('radio', 'checkbox', 'radio')) or !isset($field->field_options['separate_value']) or !$field->field_options['separate_value'])
            return $value;
            
        $field->options = maybe_unserialize($field->options);
        $f_values = array();
        $f_labels = array();
        foreach($field->options as $opt_key => $opt){
            if(!is_array($opt))
                continue;
            
            $f_labels[$opt_key] = isset($opt['label']) ? $opt['label'] : reset($opt);
            $f_values[$opt_key] = isset($opt['value']) ? $opt['value'] : $f_labels[$opt_key];
            if($f_labels[$opt_key] == $f_values[$opt_key]){
                unset($f_values[$opt_key]);
                unset($f_labels[$opt_key]);
            }
            unset($opt_key);
            unset($opt);
        }
        
        if(!empty($f_values)){
            foreach((array)$value as $v_key => $val){
                if(in_array($val, $f_values)){
                    $opt = array_search($val, $f_values);
                    $value[$v_key] = $f_labels[$opt];
                }
                unset($v_key);
                unset($val);
            }
        }
        
        return $value;
    }
    
    function get_params($form=null){
        global $frm_form;

        if(!$form)
            $form = $frm_form->getAll(array(), 'name', 1);
           
        $action_var = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = apply_filters('frm_show_new_entry_page', FrmAppHelper::get_param($action_var, 'new'), $form);
        
        $default_values = array(
            'id' => '', 'form_name' => '', 'paged' => 1, 'form' => $form->id, 'form_id' => $form->id, 
            'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'action' => $action
        );
            
        $values['posted_form_id'] = FrmAppHelper::get_param('form_id');
        if (!is_numeric($values['posted_form_id']))
            $values['posted_form_id'] = FrmAppHelper::get_param('form');

        if ($form->id == $values['posted_form_id']){ //if there are two forms on the same page, make sure not to submit both
            foreach ($default_values as $var => $default){
                if($var == 'action')
                    $values[$var] = FrmAppHelper::get_param($action_var, $default);
                else
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

        if(in_array($values['action'], array('create', 'update')) and (!isset($_POST) or (!isset($_POST['action']) and !isset($_POST['frm_action']))))
            $values['action'] = 'new';

        return $values;
    }
    
}
?>