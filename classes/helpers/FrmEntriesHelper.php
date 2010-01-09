<?php

class FrmEntriesHelper{

    function setup_new_vars($fields){
        global $frm_app_controller, $frm_form;
        $values = array();
        foreach (array('name' => '', 'description' => '', 'item_key' => '') as $var => $default)
            $values[$var] = stripslashes($frm_app_controller->get_param($var, $default));
        
        $values['fields'] = array();
        if ($fields){
            foreach($fields as $field){
              $default = $field->default_value;

              $field_options = unserialize($field->field_options);
              $new_value = ($_POST and isset($_POST['item_meta'][$field->id])) ? $_POST['item_meta'][$field->id] : $default;
              if ($field->type != 'checkbox')
                $new_value = stripslashes($new_value);
                
              $field_array = array('id' => $field->id,
                    'value' => $new_value,
                    'default_value' => $new_value,
                    'name' => stripslashes($field->name),
                    'description' => stripslashes($field->description),
                    'type' => apply_filters('frm_field_type',$field->type, $field),
                    'options' => stripslashes_deep(unserialize($field->options)),
                    'required' => $field->required,
                    'field_key' => $field->field_key,
                    'field_order' => $field->field_order,
                    'form_id' => $field->form_id);
              
              foreach (array('size' => 75,'max' => '','label' => 'top','invalid' => '','required_indicator' => '','blank' => '', 'clear_on_focus' => 0) as $opt => $default_opt)
                  $field_array[$opt] = (isset($field_options[$opt]) && $field_options[$opt] != '') ? $field_options[$opt] : $default_opt;
                
             $values['fields'][] = apply_filters('frm_setup_new_fields_vars', $field_array, $field);
            }
        }
        return $values;
    }
    
    function setup_edit_vars($values, $record){
        //$values['description'] = unserialize( $record->description );
        $values['item_key'] = (($_POST and isset($_POST['item_key']) and $record == null)?$_POST['item_key']:$record->item_key);
        return $values;
    }
    
    function entries_dropdown( $form_id, $field_name, $field_value='', $blank=true ){
        global $frm_app_controller, $frm_entry;

        $entries = $frm_entry->getAll("it.form_id=".$form_id,' ORDER BY name');
        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown">
            <?php if ($blank){ ?>
            <option value=""></option>
            <?php } ?>
            <?php foreach($entries as $entry){ ?>
                <option value="<?php echo $entry->id; ?>" <?php selected($field_value, $entry->id); ?>><?php echo (!empty($entry->name)) ? $entry->name : $entry->item_key; ?></option>
            <?php } ?>
        </select>
        <?php
    }
}

?>