<div class="frm_forms">
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <form name="form1" method="post" action="">
        <input type="hidden" name="action" value="create">
        <?php wp_nonce_field('update-options'); ?>

        <?php require(FRM_VIEWS_PATH.'/frm-entries/form.php'); ?>

        <?php if (!$form->is_template){ ?>
        <p class="submit">
        <input type="submit" name="Submit" value="Submit" />
        </p>
        <?php } ?>

    </form>
</div>