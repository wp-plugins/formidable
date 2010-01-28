<?php echo FrmFormsHelper::replace_shortcodes($values['before_html'], $form, $title, $description); ?>

<input type="hidden" name="form_id" value="<?php echo $form->id ?>">
<?php if (isset($controller) && isset($plugin)){ ?>
<input type="hidden" name="controller" value="<?php echo $controller; ?>">
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>">
<?php } ?>
<div id="frm_form_fields">
    <div>
    <?php

    if (isset($errors) && is_array($errors))
        $error_keys = array_keys($errors);
    $error_keys = (isset($error_keys)) ? $error_keys : array();
    
    foreach($values['fields'] as $field){
        $field_name = "item_meta[". $field['id'] ."]";
        if (apply_filters('frm_show_normal_field_type', true, $field['type']))
            echo FrmFieldsHelper::replace_shortcodes($field['custom_html'], $field, $error_keys);
        else
            do_action('frm_show_other_field_type', $field);
        
        do_action('frm_get_field_scripts', $field);
    }    

    global $frm_settings;
    ?>
    <?php if (is_admin() && !$frm_settings->lock_keys){ ?>
        <div class="form_field">
        <label class="frm_pos_top">Entry Key</label>   
        <input type="text" id="item_key" name="item_key" value="<?php echo $values['item_key'] ?>" />
        </div>
    <?php } ?>
    </div>
</div>

<?php do_action('frm_entries_footer_scripts',$values['fields']); ?>
<script type="text/javascript">
function frmClearDefault(default_value,thefield){if(thefield.value==default_value)thefield.value='';}
function frmReplaceDefault(default_value,thefield){if(thefield.value=='')thefield.value=default_value;}
</script>