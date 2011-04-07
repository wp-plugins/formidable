<?php
/**
 * @package Formidable
 */
 
class FrmFieldsController{
    function FrmFieldsController(){
        add_action('wp_ajax_frm_insert_field', array(&$this, 'create') );
        add_action('wp_ajax_frm_field_name_in_place_edit', array(&$this, 'edit_name') );
        add_action('wp_ajax_frm_field_desc_in_place_edit', array(&$this, 'edit_description') );
        add_action('wp_ajax_frm_mark_required', array(&$this, 'mark_required') );
        add_action('wp_ajax_frm_clear_on_focus', array(&$this, 'clear_on_focus') );
        add_action('wp_ajax_frm_default_blank', array(&$this, 'default_blank') );
        add_action('wp_ajax_frm_duplicate_field', array(&$this, 'duplicate') );
        add_action('wp_ajax_frm_delete_field', array(&$this, 'destroy') );
        add_action('wp_ajax_frm_add_field_option',array(&$this, 'add_option'));
        add_action('wp_ajax_frm_field_option_ipe', array(&$this, 'edit_option') );
        add_action('wp_ajax_frm_delete_field_option',array(&$this, 'delete_option'));
        add_action('wp_ajax_frm_update_field_order', array(&$this, 'update_order') );
        add_filter('frm_field_type',array( &$this, 'change_type'));
        add_filter('frm_display_field_options', array(&$this, 'display_field_options'));
        add_action('frm_field_input_html', array(&$this, 'input_html'));
    }
    
    function create(){
        global $frm_field, $frm_ajax_url;
        $field_data = $_POST['field'];
        $form_id = $_POST['form_id'];
        
        $field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars($field_data, $form_id));
        
        $field_id = $frm_field->create( $field_values );
        
        if ($field_id){
            $field = FrmFieldsHelper::setup_edit_vars($frm_field->getOne($field_id));
            $field_name = "item_meta[$field_id]";
            $id = $form_id;
            require(FRM_VIEWS_PATH.'/frm-forms/add_field.php'); 
            require(FRM_VIEWS_PATH.'/frm-forms/new-field-js.php'); 
        }
        die();
    }
    
    function edit_name(){
        global $frm_field;
        $id = str_replace('field_', '', $_POST['element_id']);
        $values = array('name' => trim($_POST['update_value']));
        if ($_POST['original_html'] == 'Untitled')
            $values['field_key'] = $_POST['update_value'];
        $form = $frm_field->update($id, $values);
        echo stripslashes($_POST['update_value']);  
        die();
    }
    

    function edit_description(){
        global $frm_field;
        $id = str_replace('field_', '', $_POST['element_id']);
        $frm_field->update($id, array('description' => $_POST['update_value']));
        echo stripslashes($_POST['update_value']);
        die();
    } 
    
    function mark_required(){
        global $frm_field;
        $frm_field->update($_POST['field'], array('required' => $_POST['required']));
        die();
    }
    
    function clear_on_focus(){
        global $frm_field;
        $field = $frm_field->getOne($_POST['field']);
        $field->field_options['clear_on_focus'] = $_POST['active'];
        $frm_field->update($_POST['field'], array('field_options' => $field->field_options));
        die();
    }
    
    function default_blank(){
        global $frm_field;
        $field = $frm_field->getOne($_POST['field']);
        $field->field_options['default_blank'] = $_POST['active'];
        $frm_field->update($_POST['field'], array('field_options' => $field->field_options));
        die();
    }
    
    function duplicate(){
        global $frmdb, $frm_field, $frm_app_helper, $frm_ajax_url;
        
        $copy_field = $frm_field->getOne($_POST['field_id']);
        if (!$copy_field) return;
            
        $values = array();
        $values['field_key'] = FrmAppHelper::get_unique_key('', $frmdb->fields, 'field_key');
        $values['field_options'] = maybe_unserialize($copy_field->field_options);
        $values['form_id'] = $copy_field->form_id;
        foreach (array('name', 'description', 'type', 'default_value', 'options', 'required') as $col)
            $values[$col] = $copy_field->{$col};
        $field_count = $frm_app_helper->getRecordCount("form_id='$copy_field->form_id'", $frmdb->fields);
        $values['field_order'] = $field_count + 1;
        
        $field_id = $frm_field->create($values);
        
        if ($field_id){
            $field = FrmFieldsHelper::setup_edit_vars($frm_field->getOne($field_id));
            $field_name = "item_meta[$field_id]";
            $id = $field['form_id'];
            require(FRM_VIEWS_PATH.'/frm-forms/add_field.php'); 
            require(FRM_VIEWS_PATH.'/frm-forms/new-field-js.php'); 
        }
        die();
    }
    
    function destroy(){
        global $frm_field;
        $field_id = $frm_field->destroy($_POST['field_id']);
        die();
    }   

    /* Field Options */
    function add_option(){
        global $frm_field, $frm_ajax_url;

        $id = $_POST['field_id'];
        $field = $frm_field->getOne($id);
        $options = maybe_unserialize($field->options);
        if(!empty($options))
            $last = max(array_keys($options));
        else
            $last = 0;
        $opt_key = $last + 1;
        $opt = 'Option '.(count($options)+1);
        $options[$opt_key] = $opt;
        $frm_field->update($id, array('options' => maybe_serialize($options)));
        $checked = '';

        $field_data = $frm_field->getOne($id);
        $field = array();
        $field['type'] = $field_data->type;
        $field['id'] = $id;
        $field_name = "item_meta[$id]";

        require(FRM_VIEWS_PATH.'/frm-fields/single-option.php'); 
        require(FRM_VIEWS_PATH.'/frm-forms/new-option-js.php'); 
        die();
    }

    function edit_option(){
        global $frm_field;
        $ids = explode('-',$_POST['element_id']);
        $id = str_replace('field_', '', $ids[0]);
        $field = $frm_field->getOne($id);
        $options = maybe_unserialize($field->options);
        $options[$ids[1]] = $_POST['update_value'];
        $frm_field->update($id, array('options' => maybe_serialize($options)));
        echo stripslashes($_POST['update_value']);
        die();
    }

    function delete_option(){
        global $frm_field;
        $field = $frm_field->getOne($_POST['field_id']);
        $options = maybe_unserialize($field->options);
        unset($options[$_POST['opt_key']]);
        $frm_field->update($_POST['field_id'], array('options' => serialize($options)));
        die();
    }
    

    function update_order(){
        global $frm_field;
        foreach ($_POST['frm_field_id'] as $position => $item)
            $frm_field->update($item, array('field_order' => $position));
        die();
    }
    
    function change_type($type){
        global $frmpro_is_installed;

        if ($frmpro_is_installed) return $type;
        
        if($type == 'scale' || $type == '10radio')
            $type = 'radio';
        else if($type == 'rte')
            $type = 'textarea';
            
        $frm_field_selection = FrmFieldsHelper::field_selection();
        $types = array_keys($frm_field_selection);
        if (!in_array($type, $types) && $type != 'captcha')
            $type = 'text';

        return $type;
    }
    
    function display_field_options($display){
        if ($display['type'] == 'captcha'){
            $display['required'] = false;
            $display['default_blank'] = false;
        }else if ($display['type'] == 'radio'){
            $display['default_blank'] = false;
        }else if ($display['type'] == 'text'){
            $display['size'] = true;
            $display['clear_on_focus'] = true;
        }else if ($display['type'] == 'textarea'){
            $display['size'] = true;
            $display['clear_on_focus'] = true;
        }
        
        return $display;
    }
    
    function input_html($field){
        $class = $field['type'];
        if($field['type'] == 'date')
            $class .= " frm_date";
        
        if(isset($field['size']) and $field['size'] > 0){
            if($field['type'] != 'textarea' and $field['type'] != 'select' and $field['type'] != 'data')
                echo ' size="'. $field['size'] .'"';
            $class .= " auto_width";
        }
        
        if(isset($field['max']) and !in_array($field['type'], array('textarea', 'rte')) and !empty($field['max']))
            echo ' maxlength="'. $field['max'] .'"';
        
        if(!is_admin() or !isset($_GET) or !isset($_GET['page']) or $_GET['page'] == 'formidable_entries'){
            $action = FrmAppHelper::get_param('action');
            if(isset($field['required']) and $field['required']){
                //echo ' required="required"';
                    
                if($field['type'] == 'file' and $action == 'edit'){
                    //don't add the required class if this is a file upload when editing
                }else
                    $class .= " required";
            }

            if(isset($field['default_value']) and !empty($field['default_value']) and !in_array($field['type'], array('select', 'radio', 'checkbox', 'hidden'))) 
                echo ' placeholder="'.$field['default_value'].'"';

            if(isset($field['clear_on_focus']) and $field['clear_on_focus']){
                echo ' onfocus="frmClearDefault(\''.addslashes($field['default_value']).'\', this)" onblur="frmReplaceDefault(\''.addslashes($field['default_value']).'\', this)"';
                
                if($field['value'] == $field['default_value'])
                    echo ' style="font-style:italic;"';
            }
        }
        
        $class = apply_filters('frm_field_classes', $class, $field);
        echo ' class="'.$class.'"';
    }
}
?>