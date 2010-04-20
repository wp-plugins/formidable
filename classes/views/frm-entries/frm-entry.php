<?php
global $frm_form, $frm_field, $frm_entry, $frm_entry_meta, $user_ID, $frm_settings;
$form_name = $form->name;
$form_options = stripslashes_deep(maybe_unserialize($form->options));

$submit = isset($form_options['submit_value'])?$form_options['submit_value'] : $frm_settings->submit_value;
$saved_message = isset($form_options['success_msg'])? $form_options['success_msg'] : $frm_settings->success_msg;

$params = FrmEntriesController::get_params($form);
$message = '';
$errors = '';

FrmEntriesHelper::enqueue_scripts($params);

if($params['action'] == 'create' && $params['form_id'] == $form->id){
    $errors = $frm_entry->validate($_POST);

    if( !empty($errors) ){
        $fields = FrmFieldsHelper::get_form_fields($form->id, true);
        $values = FrmEntriesHelper::setup_new_vars($fields, $form);
        require('new.php');
    }else{
        $fields = FrmFieldsHelper::get_form_fields($form->id);
        do_action('frm_validate_form_creation', $params, $fields, $form, $title, $description);
        if (apply_filters('frm_continue_to_create', true, $form->id)){
            $values = FrmEntriesHelper::setup_new_vars($fields, $form, true);
            $created = $frm_entry->create( $_POST );
            $conf_method = apply_filters('frm_success_filter', 'message', $form, $form_options);
            if (!$created or $conf_method == 'message'){
                echo '<div class="frm_message">hello '.($created) ? $saved_message : $frm_settings->failed_msg.'</div>';
                if (!isset($form_options['show_form']) or $form_options['show_form'])
                    require('new.php');
            }else
                do_action('frm_success_action', $conf_method, $form, $form_options);
        }
    }
}else{
    $fields = FrmFieldsHelper::get_form_fields($form->id);
    do_action('frm_display_form_action', $params, $fields, $form, $title, $description);
    if (apply_filters('frm_continue_to_new', true, $form->id, $params['action'])){
        $values = FrmEntriesHelper::setup_new_vars($fields, $form);
        require('new.php');
    }
}

?>