<?php

class FrmFieldsHelper{
    
    function setup_new_vars($type='',$form_id=''){
        global $frm_field, $frm_app_helper;
        
        $field_count = $frm_app_helper->getRecordCount("form_id=$form_id", $frm_field->table_name);
        $key = FrmAppHelper::get_unique_key('', $frm_field->table_name, 'field_key');
        
        $values = array();
        foreach (array('name' => 'Untitled', 'description' => '', 'field_key' => $key, 'type' => $type, 'options'=>'', 'default_value'=>'', 'field_order' => $field_count+1, 'required' => false, 'blank' => 'Untitled can\'t be blank', 'invalid' => 'Untitled is an invalid format', 'form_id' => $form_id) as $var => $default)
            $values[$var] = $default;
        
        $values['field_options'] = array();
        foreach (array('size' => '', 'max' => '', 'label' => 'top', 'required_indicator' => '*', 'clear_on_focus' => 0, 'custom_html' => FrmFieldsHelper::get_default_html($type), 'default_blank' => 0) as $var => $default)
            $values['field_options'][$var] = $default;
            
        if ($type == 'radio' || ($type == 'checkbox'))
            $values['options'] = serialize(array(1 => 'Option 1', 2 => 'Option 2'));
        else if ( $type == 'select')
            $values['options'] = serialize(array(1 => '', 2 => 'Option 1'));
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
        $values['form_id'] = $record->form_id;
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
        $values['clear_on_focus'] = (isset($field_options['clear_on_focus']))?($field_options['clear_on_focus']):(0);
        $values['default_blank'] = (isset($field_options['default_blank']))?($field_options['default_blank']):(0);
        $values['custom_html'] = (isset($field_options['custom_html']))? stripslashes($field_options['custom_html']): FrmFieldsHelper::get_default_html($record->type);
        
        return $values;
    }
    
    function get_default_html($type){
        if (apply_filters('frm_show_normal_field_type', true, $type)){
            $default_html = <<<DEFAULT_HTML
<div id="frm_field_[id]_container" class="form-field [required_class] [error_class]">
    <label class="frm_pos_[label_position]">[field_name]
        <span class="frm_required">[required_label]</span>
    </label>
    [input]
    [if description]<p class="description">[description]</p>[/if description]
</div>
DEFAULT_HTML;
        }else{
            $default_html = apply_filters('frm_other_custom_html', '', $type);
        }

        return apply_filters('frm_custom_html', $default_html, $type);
    }
    
    function replace_shortcodes($html, $field, $error_keys=array()){
        $field_name = "item_meta[". $field['id'] ."]";
        //replace [id]
        $html = str_replace('[id]', $field['id'], $html);
        
        //replace [description] and [required_label]
        $required = ($field['required'] == '0')?(''):($field['required_indicator']);
        foreach (array('description' => $field['description'], 'required_label' => $required) as $code => $value){
            if ($value == '')
                $html = preg_replace('/(\[if\s+'.$code.'\])(.*?)(\[\/if\s+'.$code.'\])/mis', '', $html);
            else{
                $html = str_replace('[if '.$code.']','',$html); 
        	    $html = str_replace('[/if '.$code.']','',$html);
            }
            $html = str_replace('['.$code.']', $value, $html);
        }
        
        //replace [required_class]
        $required_class = ($field['required'] == '0')?(''):(' form-required');
        $html = str_replace('[required_class]', $required_class, $html);  
        
        //replace [label_position]
        $html = str_replace('[label_position]', $field['label'], $html);
        
        //replace [field_name]
        $html = str_replace('[field_name]', $field['name'], $html);
            
        //replace [error_class] 
        $error_class = in_array('field'.$field['id'], $error_keys) ? ' frm_blank_field':''; 
        $html = str_replace('[error_class]', $error_class, $html);
        
        //replace [input]
        ob_start();
        include(FRM_VIEWS_PATH.'/frm-fields/input.php');
        $contents = ob_get_contents();
        ob_end_clean();
        $html = str_replace('[input]', $contents, $html);
        
        return $html;
    }
    
    function show_onfocus_js($field_id, $clear_on_focus){ ?>
    <a href="javascript:frm_clear_on_focus(<?php echo $field_id; ?>,<?php echo $clear_on_focus; ?>)" class="<?php echo ($clear_on_focus) ?'':'frm_inactive_icon'; ?> frm-show-hover" id="clear_field_<?php echo $field_id; ?>" title="Set this field to <?php echo ($clear_on_focus)?'not ':''; ?>clear on click"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></a>
    <?php
    }
    
    function show_default_blank_js($field_id, $default_blank){ ?>
    <a href="javascript:frm_default_blank(<?php echo $field_id; ?>,<?php echo $default_blank ?>)" class="<?php echo ($default_blank) ?'':'frm_inactive_icon'; ?> frm-show-hover" id="default_blank_<?php echo $field_id; ?>" title="This default value should <?php echo ($default_blank)?'not ':''; ?>be considered blank"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></a>
    <?php
    }
    
}

?>