<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <form action="" enctype="multipart/form-data" method="post" class="frm-show-form" id="form_<?php echo $form->form_key ?>">
        <?php $form_action = 'create'; ?>

        <?php require(FRM_VIEWS_PATH.'/frm-entries/form.php'); ?>

        <?php if (!$form->is_template){ ?>
        <p class="submit">
        <?php if (!isset($submit)) $submit = 'Submit';?>
        <input type="submit" name="<?php echo $submit ?>" value="<?php echo $submit ?>" />
        </p>
        <?php } ?>

    </form>
</div>