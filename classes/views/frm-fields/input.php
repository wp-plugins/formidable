<?php if ($field['type'] == 'text'){ ?>
    <input type="text" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo $field['value'] ?>" size="<?php echo $field['size'] ?>" maxlength="<?php echo $field['max'] ?>" <?php echo ($field['clear_on_focus']) ? 'onfocus="frmClearDefault(\''.$field['default_value'].'\', this)" onblur="frmReplaceDefault(\''.$field['default_value'].'\', this)"':''; ?>/>
    
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>" cols="<?php echo $field['size'] ?>" rows="<?php echo $field['max'] ?>" <?php echo ($field['clear_on_focus']) ? 'onfocus="frmClearDefault(\''.$field['default_value'].'\', this)" onblur="frmReplaceDefault(\''.$field['default_value'].'\', this)"':''; ?>><?php echo $field['value'] ?></textarea> 
    
<?php }else if ($field['type'] == 'radio'){
            if (is_array($field['options'])){
                foreach($field['options'] as $opt){ ?>
                    <input type='radio' name='<?php echo $field_name ?>' value='<?php echo $opt ?>' <?php if ($field['value'] == $opt) echo 'checked="checked"'; ?>/> 
                    <?php echo $opt ?><br/>
        <?php   }  
            } ?>   
<?php }else if ($field['type'] == 'select'){?>
    <select name="<?php echo $field_name ?>" id="item_meta<?php echo $field['id'] ?>">
        <?php foreach ($field['options'] as $opt){ ?>
            <option value='<?php echo $opt ?>' <?php if ($field['value'] == $opt) echo 'selected="selected"'; ?>><?php echo $opt ?></option>
        <?php } ?>
    </select>
<?php }else if ($field['type'] == 'captcha'){
        global $frm_recaptcha_enabled;
        if ($frm_recaptcha_enabled)
            FrmAppHelper::display_recaptcha($errors);
      }else if ($field['type'] == 'checkbox'){
        $checked_values = stripslashes_deep(maybe_unserialize($field['value']));
        foreach ($field['options'] as $opt){
            $checked = ((!is_array($checked_values) && $checked_values == $opt ) || (is_array($checked_values) && in_array($opt, $checked_values)))?' checked="true"':'';
            echo "<input type='checkbox' name='". $field_name ."[]' value='".$opt."'". $checked ."'/> ".$opt."<br/>";
        }
      }else do_action('frm_form_fields',$field, $field_name);
?>
