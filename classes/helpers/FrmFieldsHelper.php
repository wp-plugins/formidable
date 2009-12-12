<?php

class FrmFieldsHelper{
    
    function setup_new_vars($type='',$form_id=''){
        global $frm_field;
        
        $field_count = $frm_field->getRecordCount("form_id=$form_id");
        $key = FrmAppHelper::get_unique_key('', $frm_field->table_name, 'field_key');
        
        $values = array();
        foreach (array('name' => 'Untitled', 'description' => '', 'field_key' => $key, 'type' => $type, 'options'=>'', 'default_value'=>'', 'field_order' => $field_count+1, 'required' => false, 'blank' => 'Untitled can\'t be blank', 'invalid' => 'Untitled is an invalid format', 'form_id' => $form_id) as $var => $default)
            $values[$var] = $default;
        
        $values['field_options'] = array();
        foreach (array('size' => '50', 'max' => '', 'label' => 'top', 'required_indicator' => '*') as $var => $default)
            $values['field_options'][$var] = $default;
            
        if ($type == 'radio' || ($type == 'checkbox'))
            $values['options'] = serialize(array(1 => 'Option 1', 2 => 'Option 2'));
        else if ( $type == 'select')
            $values['options'] = serialize(array(1 => '', 2 => 'Option 1', 3 => 'Option 2'));
        else if ($type == 'textarea'){
            $values['field_options']['size'] = '45';
            $values['field_options']['max'] = '5';
        }
        
        return $values;
    }
    
    function setup_edit_vars($record){
        global $frm_entry_meta, $frm_form, $frm_app_controller;
        
        $values = array();
        $values['id'] = $record->id;

        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default)
              $values[$var] = htmlspecialchars(stripslashes($frm_app_controller->get_param($var, $default)));

        $values['form_name'] = ($record->form_id)?($frm_form->getName( $record->form_id )):('');
        
        foreach (array('field_key' => $record->field_key, 'type' => $record->type, 'default_value'=> $record->default_value, 'field_order' => $record->field_order, 'required' => $record->required) as $var => $default)
            $values[$var] = $frm_app_controller->get_param($var, $default);
            
        $values['options'] = unserialize($record->options);
        $field_options = unserialize($record->field_options);
        $values['field_options'] = $field_options;
        $values['size'] = (isset($field_options['size']))?($field_options['size']):('75'); 
        $values['max'] = (isset($field_options['max']))?($field_options['max']):(''); 
        $values['label'] = (isset($field_options['label']))?($field_options['label']):('top'); 
        $values['blank'] = (isset($field_options['blank']))?($field_options['blank']):(''); 
        $values['required_indicator'] = (isset($field_options['required_indicator']))?($field_options['required_indicator']):('*'); 
        $values['invalid'] = (isset($field_options['invalid']))?($field_options['invalid']):('');
        
        return $values;
    }
    
}

?>