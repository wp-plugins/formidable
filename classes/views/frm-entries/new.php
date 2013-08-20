<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
<form enctype="<?php echo apply_filters('frm_form_enctype', 'multipart/form-data', $form) ?>" method="post" class="frm-show-form <?php do_action('frm_form_classes', $form) ?>" id="form_<?php echo $form->form_key ?>" <?php echo ($frm_settings->use_html) ? '' : 'action=""'; ?>>
<?php 
include(FRM_VIEWS_PATH.'/frm-entries/errors.php');
$form_action = 'create';
require(FRM_VIEWS_PATH.'/frm-entries/form.php'); 
?>
</form>
</div>