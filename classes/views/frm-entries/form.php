<?php 
global $frm_forms_loaded, $frm_load_css, $frm_css_loaded;
$frm_forms_loaded[] = $form; 
if($values['custom_style']) $frm_load_css = true;

if(!$frm_css_loaded and $frm_load_css){
echo FrmAppController::footer_js('header');
$frm_css_loaded = true;
}

echo FrmFormsHelper::replace_shortcodes($values['before_html'], $form, $title, $description); ?>
<div id="frm_form_fields">
<fieldset>
<div>
<input type="hidden" name="action" value="<?php echo $form_action ?>" />
<input type="hidden" name="form_id" value="<?php echo $form->id ?>" />
<input type="hidden" name="form_key" value="<?php echo $form->form_key ?>" />
<?php if (isset($id)){ ?><input type="hidden" name="id" value="<?php echo $id ?>" /><?php } ?>
<?php if (isset($controller) && isset($plugin)){ ?>
<input type="hidden" name="controller" value="<?php echo $controller; ?>" />
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>" />
<?php }

$error_keys = array();
if (isset($errors) && is_array($errors)){
    foreach($errors as $error_id => $error_msg){
        if(!is_numeric($error_id))
            $error_keys[] = $error_id;
    }
}

if($values['fields']){
foreach($values['fields'] as $field){
    $field_name = "item_meta[". $field['id'] ."]";
    if (apply_filters('frm_show_normal_field_type', true, $field['type']))
        echo FrmFieldsHelper::replace_shortcodes($field['custom_html'], $field, $error_keys, $form);
    else
        do_action('frm_show_other_field_type', $field, $form);
    
    do_action('frm_get_field_scripts', $field);
}    
}

global $frm_settings;

if (is_admin() && !$frm_settings->lock_keys){ ?>
<div class="form-field">
<label><?php _e('Entry Key', 'formidable') ?></label>   
<input type="text" id="item_key" name="item_key" value="<?php echo $values['item_key'] ?>" />
</div>
<?php }else{ ?>
<input type="hidden" id="item_key" name="item_key" value="<?php echo $values['item_key'] ?>" />
<?php } ?>
<?php do_action('frm_entry_form', $form) ?>
</div>
</fieldset>
</div>
<?php echo FrmFormsHelper::replace_shortcodes($values['after_html'], $form); ?>
<script type="text/javascript">
<?php do_action('frm_entries_footer_scripts',$values['fields'], $form); ?>
function frmClearDefault(default_value,thefield){if(thefield.value==default_value)thefield.value='';thefield.style.fontStyle='inherit'}
function frmReplaceDefault(default_value,thefield){if(thefield.value==''){thefield.value=default_value;thefield.style.fontStyle='italic';}}</script>