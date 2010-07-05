<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
    <form action="" enctype="multipart/form-data" method="post" class="frm-show-form" id="form_<?php echo $form->form_key ?>">
        <?php include(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
        <?php $form_action = 'create'; ?>
        <?php require(FRM_VIEWS_PATH.'/frm-entries/form.php'); ?>

        <?php if (!$form->is_template){ ?>
        <p class="submit">
        <?php if (!isset($submit)) $submit = $frm_settings->submit_value; ?>
        <input type="submit" name="<?php echo $submit ?>" value="<?php echo $submit ?>" <?php do_action('frm_submit_button', $form, $form_action); ?>/>
        </p>
        <?php } ?>
    </form>
</div>