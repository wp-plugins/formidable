<?php
global $frm_form, $frm_field, $frm_entry, $frm_entry_meta, $frm_recaptcha_enabled, $user_ID;
$fields = $frm_field->getAll("fi.form_id='$form->id'", ' ORDER BY field_order');
$values = FrmEntriesHelper::setup_new_vars($fields);
$form_name = $form->name;

$failed_message = "We're sorry. There was an error processing your responses.";
$saved_message = "Your responses were successfully submitted. Thank you!";

$params = FrmEntriesController::get_params($form);
$message = '';
$errors = '';

if($params['action'] == 'create'){
    $errors = $frm_entry->validate($_POST);

    if( count($errors) > 0 ){
        $values = FrmEntriesHelper::setup_new_vars($fields);
        require_once('new.php');
    }else{
        do_action('frm_validate_form_creation', $params, $fields, $form, $title, $description);
        if (apply_filters('frm_continue_to_create', true)){
            if ($frm_entry->create( $_POST ))
                echo $saved_message;
            else
                echo $failed_message;
        }
    }
}else{
    do_action('frm_display_form_action', $params, $fields, $form, $title, $description);
    if (apply_filters('frm_continue_to_new', true)){
        $values = FrmEntriesHelper::setup_new_vars($fields);
        require_once('new.php');
    }
}

?>