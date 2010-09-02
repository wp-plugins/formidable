<?php if ($field['type'] == 'text'){ ?>
    <input type="text" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo $field['value'] ?>" <?php do_action('frm_field_input_html', $field) ?>/>
    
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" cols="<?php echo $field['size'] ?>" rows="<?php echo $field['max'] ?>" <?php do_action('frm_field_input_html', $field) ?>><?php echo $field['value'] ?></textarea> 
    
<?php }else if ($field['type'] == 'radio'){
        if (is_array($field['options'])){
            foreach($field['options'] as $opt_key => $opt){
                if(isset($atts) and isset($atts['opt']) and ($atts['opt'] != $opt_key)) continue;
                $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            ?>
            <div class="frm_radio"><input type="radio" name="<?php echo $field_name ?>" id="item_meta_val<?php echo $field['id'] .'_'. sanitize_title_with_dashes($field_val) ?>" value="<?php echo $field_val ?>" <?php echo ($field['value'] == $field_val) ? 'checked="checked"' : ''; ?> /><?php if(!isset($atts) or !isset($atts['label']) or $atts['label']){ ?><label for="item_meta_val<?php echo $field['id'] .'_'. sanitize_title_with_dashes($field_val) ?>"><?php echo $opt ?></label><?php } ?></div>
    <?php   }  
        } ?>   
<?php }else if ($field['type'] == 'select'){ ?>
    <?php $auto_width = (isset($field['size']) && $field['size']) ? 'class="auto_width"' : ''; ?>
    <select name="<?php echo $field_name ?>" id="item_meta<?php echo $field['id'] ?>" <?php echo $auto_width ?>>
        <?php foreach ($field['options'] as $opt_key => $opt){ 
            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field); ?>
            <option value="<?php echo $field_val ?>" <?php if ($field['value'] == $field_val) echo 'selected="selected"'; ?>><?php echo $opt ?></option>
        <?php } ?>
    </select>
<?php }else if ($field['type'] == 'captcha'){
        if (array_key_exists('captcha', FrmFieldsHelper::field_selection()))
            FrmAppHelper::display_recaptcha();
      }else if ($field['type'] == 'checkbox'){
        $checked_values = $field['value'];
        foreach ($field['options'] as $opt_key => $opt){
            if(isset($atts) and isset($atts['opt']) and ($atts['opt'] != $opt_key)) continue;
            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            $checked = ((!is_array($checked_values) && $checked_values == $field_val ) || (is_array($checked_values) && in_array($field_val, $checked_values)))?' checked="checked"':''; ?>
            <div class="frm_checkbox"><input type="checkbox" name="<?php echo $field_name ?>[]" id="item_meta_val<?php echo $field['id'] .'_'. sanitize_title_with_dashes($field_val) ?>" value="<?php echo $field_val ?>" <?php echo $checked ?> /><?php if(!isset($atts) or !isset($atts['label']) or $atts['label']){ ?><label for="item_meta_val<?php echo $field['id'] .'_'. sanitize_title_with_dashes($field_val) ?>"><?php echo $opt ?></label><?php } ?></div>
        <?php
        }
      }else do_action('frm_form_fields',$field, $field_name);
?>
