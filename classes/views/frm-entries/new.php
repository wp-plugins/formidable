<div class="frm_forms">
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <form action="" enctype="multipart/form-data" method="post" class="frm-show-form">
        <input type="hidden" name="action" value="create">
        <?php wp_nonce_field('update-options'); ?>

        <?php require(FRM_VIEWS_PATH.'/frm-entries/form.php'); ?>

        <?php if (!$form->is_template){ ?>
        <p class="submit">
        <input type="submit" name="<?php echo $submit ?>" value="<?php echo $submit ?>" />
        </p>
        <?php } ?>

    </form>
</div>