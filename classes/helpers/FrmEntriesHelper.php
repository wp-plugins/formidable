<?php

class FrmEntriesHelper{

    function setup_new_vars($fields, $form='', $reset=false){
        global $frm_form, $frm_settings;
        $values = array();
        foreach (array('name' => '', 'description' => '', 'item_key' => '') as $var => $default)
            $values[$var] = stripslashes(FrmAppHelper::get_param($var, $default));
            
        $values['fields'] = array();
        if ($fields){
            foreach($fields as $field){
              $default = $field->default_value;
              
              $field_options = unserialize($field->field_options);
              if ($reset)
                  $new_value = $default;
              else
                  $new_value = ($_POST and isset($_POST['item_meta'][$field->id]) and $_POST['item_meta'][$field->id] != '') ? $_POST['item_meta'][$field->id] : $default;
                  
              $new_value = stripslashes_deep(maybe_unserialize($new_value));
              if (!is_array($new_value))
                $new_value = apply_filters('frm_get_default_value', $new_value, $field);
                
              $new_value = str_replace('"', '&quot;', $new_value);
                
              $field_array = array('id' => $field->id,
                    'value' => $new_value,
                    'default_value' => $new_value,
                    'name' => stripslashes($field->name),
                    'description' => stripslashes($field->description),
                    'type' => apply_filters('frm_field_type',$field->type, $field),
                    'options' => str_replace('"', '&quot;', stripslashes_deep(unserialize($field->options))),
                    'required' => $field->required,
                    'field_key' => $field->field_key,
                    'field_order' => $field->field_order,
                    'form_id' => $field->form_id);

              foreach (array('size' => '','max' => '','label' => 'top','invalid' => '','required_indicator' => '','blank' => '', 'clear_on_focus' => 0, 'custom_html' => '', 'default_blank' => 0) as $opt => $default_opt)
                  $field_array[$opt] = (isset($field_options[$opt]) && $field_options[$opt] != '') ? $field_options[$opt] : $default_opt;
                  
              if ($field_array['custom_html'] == '')
                  $field_array['custom_html'] = FrmFieldsHelper::get_default_html($field->type);

             $values['fields'][] = apply_filters('frm_setup_new_fields_vars', stripslashes_deep($field_array), $field);
             
             if (!$form or !isset($form->id))
                 $form = $frm_form->getOne($field->form_id);
            }

            $options = stripslashes_deep(unserialize($form->options));

            if (is_array($options)){
                foreach ($options as $opt => $value)
                    $values[$opt] = FrmAppHelper::get_param($opt, $value);
            }
            
            if (!isset($values['custom_style']))
                $values['custom_style'] = ($frm_settings->load_style != 'none');
                
            if (!isset($values['email_to']))
                $values['email_to'] = '';

            if (!isset($values['submit_value']))
                $values['submit_value'] = $frm_settings->submit_value;

            if (!isset($values['success_msg']))
                $values['success_msg'] = $frm_settings->success_msg;

            if (!isset($values['akismet']))
                $values['akismet'] = 0;

            if (!isset($values['before_html']))
                $values['before_html'] = FrmFormsHelper::get_default_html('before');

            if (!isset($values['after_html']))
                $values['after_html'] = FrmFormsHelper::get_default_html('after');
        }
        return $values;
    }
    
    function setup_edit_vars($values, $record){
        //$values['description'] = unserialize( $record->description );
        $values['item_key'] = ($_POST and isset($_POST['item_key']))?$_POST['item_key']:$record->item_key;
        $values['form_id'] = $record->form_id;
        return apply_filters('frm_setup_edit_entry_vars', $values, $record);
    }

    function entries_dropdown( $form_id, $field_name, $field_value='', $blank=true, $blank_label='', $onchange=false ){
        global $frm_entry;

        $entries = $frm_entry->getAll("it.form_id=".$form_id,' ORDER BY name');
        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown" <?php if ($onchange) echo 'onchange="'.$onchange.'"'; ?>>
            <?php if ($blank){ ?>
            <option value=""><?php echo $blank_label; ?></option>
            <?php } ?>
            <?php foreach($entries as $entry){ ?>
                <option value="<?php echo $entry->id; ?>" <?php selected($field_value, $entry->id); ?>><?php echo substr((!empty($entry->name)) ? stripslashes($entry->name) : $entry->item_key, 0, 50); ?></option>
            <?php } ?>
        </select>
        <?php
    }
    
    function enqueue_scripts($params){
        do_action('frm_enqueue_form_scripts', $params);
    }
}

?>