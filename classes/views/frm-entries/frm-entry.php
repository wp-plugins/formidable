<?php
global $frm_field, $frm_entry, $frm_entry_meta, $user_ID, $frm_settings, $frm_vars;
$form_name = $form->name;

$frm_form = new FrmForm();
$submit = isset($form->options['submit_value']) ? $form->options['submit_value'] : $frm_settings->submit_value;
$saved_message = isset($form->options['success_msg']) ? $form->options['success_msg'] : $frm_settings->success_msg;

$params = FrmEntriesController::get_params($form);

$message = $errors = '';

FrmEntriesHelper::enqueue_scripts($params);

if($params['action'] == 'create' and $params['posted_form_id'] == $form->id and isset($_POST)){
    $errors = isset($frm_vars['created_entries'][$form->id]) ? $frm_vars['created_entries'][$form->id]['errors'] : array();

    if( !empty($errors) ){
        $fields = FrmFieldsHelper::get_form_fields($form->id, true);
        $values = $fields ? FrmEntriesHelper::setup_new_vars($fields, $form) : array();
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/new.php'); 
?>
<script type="text/javascript">jQuery(document).ready(function($){frmScrollMsg(<?php echo $form->id ?>);})</script><?php        
    }else{
        $fields = FrmFieldsHelper::get_form_fields($form->id);
        do_action('frm_validate_form_creation', $params, $fields, $form, $title, $description);
        if (apply_filters('frm_continue_to_create', true, $form->id)){
            $values = FrmEntriesHelper::setup_new_vars($fields, $form, true);
            $created = (isset($frm_vars['created_entries']) and isset($frm_vars['created_entries'][$form->id])) ? $frm_vars['created_entries'][$form->id]['entry_id'] : 0;
            $saved_message = apply_filters('frm_content', $saved_message, $form, $created);
            $conf_method = apply_filters('frm_success_filter', 'message', $form, $form->options);
            if (!$created or !is_numeric($created) or $conf_method == 'message'){
                if($created and is_numeric($created))
                    $message = '<div class="frm_message" id="message">'. wpautop(do_shortcode($saved_message)) .'</div>';
                else
                    $message = '<div class="frm_error_style">'. $frm_settings->failed_msg .'</div>';
                if (!isset($form->options['show_form']) or $form->options['show_form']){
                    require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/new.php');
                }else{ 
                    global $frm_vars;
                    $frm_vars['forms_loaded'][] = $form; 
                    if($values['custom_style']) $frm_vars['load_css'] = true;

                    if((!isset($frm_vars['css_loaded']) || !$frm_vars['css_loaded']) && $frm_vars['load_css']){
                        echo FrmAppController::footer_js('header');
                        $frm_vars['css_loaded'] = true;
                    }
?>
<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container"><?php echo $message ?></div>
<?php
                }
            }else
                do_action('frm_success_action', $conf_method, $form, $form->options, $created);
                
            do_action('frm_after_entry_processed', array( 'entry_id' => $created, 'form' => $form));
        }
    }
}else{
    $fields = FrmFieldsHelper::get_form_fields($form->id);
    do_action('frm_display_form_action', $params, $fields, $form, $title, $description);
    if (apply_filters('frm_continue_to_new', true, $form->id, $params['action'])){
        $values = FrmEntriesHelper::setup_new_vars($fields, $form);
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/new.php');
    }
}

?>