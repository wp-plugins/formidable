<?php

$values['name'] = __('Contact Us', 'formidable');
$values['description'] = __('We would like to hear from you. Please send us a message by filling out the form below and we will get back with you shortly.', 'formidable');
$values['options']['custom_style'] = 1;

if ($form){
    $form_id = $form->id;
    $frm_form->update($form_id, $values );
    $form_fields = $frm_field->getAll("fi.form_id='$form_id'");
    if (!empty($form_fields)){
        foreach ($form_fields as $field)
            $frm_field->destroy($field->id);
    }
}else
    $form_id = $frm_form->create( $values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('text', $form_id));
$field_values['name'] = 'Name';
$field_values['description'] = 'First';
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'First name cannot be blank';
$field_values['field_options']['size'] = 22;
$field_values['field_options']['custom_html'] = '<div id="frm_field_[id]_container" class="form-field [required_class] [error_class]" style="float:left;margin-right:10px;">'."\n".'<label class="frm_pos_[label_position]">[field_name] <span class="frm_required">[required_label]</span>'."\n</label>\n".'[input]'."\n".'[if description]<div class="frm_description">[description]</div>[/if description]'."\n</div>";
$frm_field->create( $field_values );

$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('text', $form_id));
$field_values['name'] = $field_values['description'] = 'Last';
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'Last name cannot be blank';
$field_values['field_options']['size'] = 22;
$field_values['field_options']['label'] = 'hidden';
$field_values['field_options']['custom_html'] = '<div id="frm_field_[id]_container" class="form-field [required_class] [error_class]" style="float:left;">'."\n".'<label class="frm_pos_[label_position]">[field_name] <span class="frm_required">[required_label]</span>'."\n</label>\n".'[input]'."\n".'[if description]<div class="frm_description">[description]</div>[/if description]'."\n</div>\n".'<div style="clear:both"></div>';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('email', $form_id));
$field_values['name'] = __('Email', 'formidable');
$field_values['required'] = 1;
$field_values['field_options']['blank'] = __('Email cannot be blank', 'formidable');
$field_values['field_options']['invalid'] = __('Please enter a valid email address', 'formidable');
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('url', $form_id));
$field_values['name'] = __('Website', 'formidable');
$field_values['field_options']['blank'] = __('Website cannot be blank', 'formidable');
$field_values['field_options']['invalid'] = __('Website is an invalid format', 'formidable');
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('text', $form_id));
$field_values['name'] = __('Subject', 'formidable');
$field_values['required'] = 1;
$field_values['field_options']['blank'] = __('Subject cannot be blank', 'formidable');
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('textarea', $form_id));
$field_values['name'] = __('Message', 'formidable');
$field_values['required'] = 1;
$field_values['field_options']['blank'] = __('Message cannot be blank', 'formidable');
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('captcha', $form_id));
$field_values['name'] = __('Captcha', 'formidable');
$field_values['field_options']['label'] = 'none';
$frm_field->create( $field_values );

  
?>