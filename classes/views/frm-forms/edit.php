<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Edit Form', 'formidable') ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

    <div class="frm_form_builder alignleft<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="poststuff">
    <form name="form1" method="post" action="">
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" /> <?php _e('or', 'formidable') ?>
            <a href="?page=<?php echo FRM_PLUGIN_NAME ?>"><?php _e('Cancel', 'formidable') ?></a>
        </p>
        
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php wp_nonce_field('update-options'); ?>

        <?php require(FRM_VIEWS_PATH.'/frm-forms/form.php'); ?>

        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" /> <?php _e('or', 'formidable') ?>
            <a href="?page=<?php echo FRM_PLUGIN_NAME ?>"><?php _e('Cancel', 'formidable') ?></a>
        </p>
    </form>
    </div>

<?php require('add_field_links.php'); ?>
</div>
<?php require('footer.php'); ?>