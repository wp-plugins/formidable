<?php

class FrmFieldsController{
    function FrmFieldsController(){
        add_filter('frm_field_type',array( $this, 'change_type'));
        add_action('wp_ajax_frm_insert_field', array($this, 'create') );
        add_action('wp_ajax_frm_field_name_in_place_edit', array($this, 'edit_name') );
        add_action('wp_ajax_frm_field_desc_in_place_edit', array($this, 'edit_description') );
        add_action('wp_ajax_frm_mark_required', array($this, 'mark_required') );
        add_action('wp_ajax_frm_unmark_required', array($this, 'unmark_required') );
        add_action('wp_ajax_frm_delete_field', array($this, 'destroy') );
        add_action('wp_ajax_frm_add_field_option',array($this, 'add_option'));
        add_action('wp_ajax_frm_field_option_ipe', array($this, 'edit_option') );
        add_action('wp_ajax_frm_delete_field_option',array($this, 'delete_option'));
        add_action('wp_ajax_frm_update_field_order', array($this, 'update_order') );
    }
    
    function create(){
        global $frm_field, $frm_recaptcha_enabled;
        $field_data = $_POST['field'];
        $form_id = $_POST['form_id'];
        
        $field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars($field_data, $form_id));
        if (isset($_POST['position']))
            $field_values['field_order'] = $_POST['position'];
        
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
        $values = array('name' => $_POST['update_value']);
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
        $id = str_replace('req_field_', '', $_POST['field']);
        $frm_field->update($id, array('required' => '1'));
        die();
    }

    function unmark_required(){
        global $frm_field;
        $id = str_replace('req_field_', '', $_POST['field']);
        $frm_field->update($id, array('required' => '0'));
        die();
    }
    
    function destroy(){
        global $frm_field;
        $field_id = $frm_field->destroy($_POST['field_id']);
        die();
    }   

    /* Field Options */
    function add_option(){
        global $frm_field;

        $id = str_replace('field_', '', $_POST['field']);
        $field = $frm_field->getOne($id);
        $options = unserialize($field->options);
        $last = max(array_keys($options));
        $opt_key = $last + 1;
        $opt = 'Option '.(count($options)+1);
        $options[$opt_key] = $opt;
        $frm_field->update($id, array('options' => serialize($options)));
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
        $options = unserialize($field->options);
        $options[$ids[1]] = $_POST['update_value'];
        $frm_field->update($id, array('options' => maybe_serialize($options)));
        echo stripslashes($_POST['update_value']);
        die();
    }

    function delete_option(){
        global $frm_field;
        $ids = explode('-',$_POST['field']);
        $id = str_replace('frm_delete_field_', '', $ids[0]);
        $field = $frm_field->getOne($id);
        $options = unserialize($field->options);
        unset($options[$ids[1]]);
        $frm_field->update($id, array('options' => serialize($options)));
        die();
    }
    

    function update_order(){
        global $frm_field;
        foreach ($_POST['frm_field_id'] as $position => $item)
            $frm_field->update($item, array('field_order' => $position));
        die();
    }
    
    
    function change_type($type){
        global $frm_field_selection, $frmpro_is_installed;

        if($frmpro_is_installed)
            return $type;

        $types = array_keys($frm_field_selection);
        if (!in_array($type, $types))
            $type = 'text';

        return $type;
    }
}
?>