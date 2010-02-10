<?php

$values['name'] = 'Contact Us';
$values['description'] = 'We would like to hear from you. Please send us a message by filling out the form below and we will get back with you shortly.';

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
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'Name cannot be blank';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('email', $form_id));
$field_values['name'] = 'Email';
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'Email cannot be blank';
$field_values['field_options']['invalid'] = 'Please enter a valid email address';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('website', $form_id));
$field_values['name'] = 'Website';
$field_values['field_options']['blank'] = 'Website cannot be blank';
$field_values['field_options']['invalid'] = 'Website is an invalid format';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('text', $form_id));
$field_values['name'] = 'Subject';
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'Subject cannot be blank';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('textarea', $form_id));
$field_values['name'] = 'Message';
$field_values['required'] = 1;
$field_values['field_options']['blank'] = 'Message cannot be blank';
$frm_field->create( $field_values );


$field_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('captcha', $form_id));
$field_values['name'] = 'Captcha';
$field_values['field_options']['label'] = 'none';
$frm_field->create( $field_values );

  
?>