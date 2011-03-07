<div class="wrap">
    <div class="frmicon icon32"><br/></div>    
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Create Form', 'formidable') ?></h2>
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
    <?php require('add_field_links.php'); ?>
    <div id="post-body">
    <div id="post-body-content">
    <div class="frm_form_builder<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>">
        <form method="post" action="">
            <input type="hidden" name="action" value="create" />
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <?php wp_nonce_field('update-options'); ?>

            <?php require(FRM_VIEWS_PATH.'/frm-forms/form.php'); ?>

            <p>
                <input type="submit" name="Submit" value="<?php _e('Create', 'formidable') ?>" class="button-primary" />
                <?php _e('or', 'formidable') ?>
                <a class="button-secondary cancel" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=destroy&amp;id=<?php echo $id; ?>"><?php _e('Cancel', 'formidable') ?></a>
            </p>
        </form>
    </div>
    </div>
    </div>
    </div>
</div> 
<?php require('footer.php'); ?>