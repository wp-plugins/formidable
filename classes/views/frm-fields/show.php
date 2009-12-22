<div id="frm_field_<?php echo $field['id'] ?>_container" class="form-field<?php echo ($field['required'] == '0')?(''):(' form-required'); if (isset($error_keys) && is_array($error_keys)) echo in_array('field'.$field['id'], $error_keys)? ' frm_blank_field':''; ?>">
    <label class="frm_pos_<?php echo $field['label'] ?>"><?php echo $field['name'] ?> 
        <span class="frm_required"><?php echo ($field['required'] == '0')?(''):($field['required_indicator']); ?></span>
    </label>   

<?php if ($field['type'] == 'text'){ ?>
    <input type="text" id="<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo $field['value'] ?>" size="<?php echo $field['size'] ?>" maxlength="<?php echo $field['max'] ?>" <?php echo ($field['clear_on_focus']) ? 'onfocus="frmClearDefault(\''.$field['default_value'].'\', this)" onblur="frmReplaceDefault(\''.$field['default_value'].'\', this)"':''; ?>/>
    
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>" cols="<?php echo $field['size'] ?>" rows="<?php echo $field['max'] ?>"><?php echo $field['value'] ?></textarea> 
    
<?php }else if ($field['type'] == 'radio'){
            if (is_array($field['options'])){
                foreach($field['options'] as $opt){
                    $checked = ($field['value'] == $opt ) ?' checked="true"':''; ?>
                    <input type='radio' name='<?php echo $field_name ?>' value='<?php echo $opt ?>'<?php echo $checked ?>/> 
                    <?php echo $opt ?><br/>
        <?php   }  
            } ?>   
<?php }else if ($field['type'] == 'select'){?>
    <select name="<?php echo $field_name ?>" id="<?php echo $field_name ?>">
        <?php foreach ($field['options'] as $opt){ ?>
            <option value='<?php echo $opt ?>'<?php echo ($field['value'] == $opt)?(' selected="true"'):(''); ?>><?php echo $opt ?></option>
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
    <p class="description"><?php echo $field['description']; ?></p>
</div>