<div class="frm_forms" id="frm_form_<?php echo $form->id ?>_container">
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <form action="" enctype="multipart/form-data" method="post" class="frm-show-form" name="form_<?php echo $form->form_key ?>" id="form_<?php echo $form->form_key ?>">
        <input type="hidden" name="action" value="create" />

        <?php require(FRM_VIEWS_PATH.'/frm-entries/form.php'); ?>

        <?php if (!$form->is_template){ ?>
        <p class="submit">
        <?php if (!isset($submit)) $submit = 'Submit';?>
        <input type="submit" name="<?php echo $submit ?>" value="<?php echo $submit ?>" />
        </p>
        <?php } ?>

    </form>
</div>