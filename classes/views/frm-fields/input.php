<?php $auto_width = (isset($field['size']) && $field['size'] > 0) ? 'class="auto_width"' : ''; ?>
<?php if ($field['type'] == 'text'){ ?>
    <input type="text" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo $field['value'] ?>" size="<?php echo $field['size'] ?>" maxlength="<?php echo $field['max'] ?>" <?php echo ($field['clear_on_focus']) ? 'onfocus="frmClearDefault(\''.$field['default_value'].'\', this)" onblur="frmReplaceDefault(\''.$field['default_value'].'\', this)"':''; ?> <?php echo $auto_width ?>/>
    
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>" cols="<?php echo $field['size'] ?>" rows="<?php echo $field['max'] ?>" <?php echo ($field['clear_on_focus']) ? 'onfocus="frmClearDefault(\''.$field['default_value'].'\', this)" onblur="frmReplaceDefault(\''.$field['default_value'].'\', this)"':''; ?> <?php echo $auto_width ?>><?php echo $field['value'] ?></textarea> 
    
<?php }else if ($field['type'] == 'radio'){
            if (is_array($field['options'])){
                foreach($field['options'] as $opt){ ?>
                    <div class="frm_radio"><input type="radio" name="<?php echo $field_name ?>" value="<?php echo $opt ?>" <?php if ($field['value'] == $opt) echo 'checked="checked"'; ?> /><span><?php echo $opt ?></span></div>
        <?php   }  
            } ?>   
<?php }else if ($field['type'] == 'select'){?>
    <select name="<?php echo $field_name ?>" id="item_meta<?php echo $field['id'] ?>" <?php echo $auto_width ?>>
        <?php foreach ($field['options'] as $opt){ ?>
            <option value="<?php echo $opt ?>" <?php if ($field['value'] == $opt) echo 'selected="selected"'; ?>><?php echo $opt ?></option>
        <?php } ?>
    </select>
<?php }else if ($field['type'] == 'captcha'){
        if (array_key_exists('captcha', FrmFieldsHelper::field_selection()))
            FrmAppHelper::display_recaptcha();
      }else if ($field['type'] == 'checkbox'){
        $checked_values = $field['value'];
        foreach ($field['options'] as $opt){
            $checked = ((!is_array($checked_values) && $checked_values == $opt ) || (is_array($checked_values) && in_array($opt, $checked_values)))?' checked="true"':''; ?>
            <div class="frm_checkbox"><input type="checkbox" name="<?php echo $field_name ?>[]" value="<?php echo $opt ?>" <?php echo $checked ?> /><span><?php echo $opt ?></span></div>
        <?php
        }
      }else do_action('frm_form_fields',$field, $field_name);
?>
