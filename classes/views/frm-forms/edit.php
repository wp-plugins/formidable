<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php echo __('Edit', 'formidable') .' '. (($values['is_template']) ? __('Template') : __('Form')); ?>
        <a href="?page=<?php echo FRM_PLUGIN_NAME ?>-new" class="button add-new-h2"><?php _e('Add New', 'formidable'); ?></a>
    </h2>
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <?php if (!$values['is_template']){ ?>
        <div class="alignleft">
            <?php FrmAppController::get_form_nav($id, true); ?>
        </div>
    <?php } ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
    <?php 
    $show_preview = true;
    require('add_field_links.php'); 
    ?>
    <div id="post-body">
    <div id="post-body-content">
    <div class="frm_form_builder<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>">
    <form method="post" >
        <p style="margin-top:0;">
            <input type="submit" name="Submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" />
            <?php _e('or', 'formidable') ?>
            <a class="button-secondary cancel" href="?page=<?php echo FRM_PLUGIN_NAME ?>"><?php _e('Cancel', 'formidable') ?></a>
        </p>
        
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <?php wp_nonce_field('update-options'); ?>

        <?php require(FRM_VIEWS_PATH.'/frm-forms/form.php'); ?>

        <p>            
            <input type="submit" name="Submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" />
            <?php _e('or', 'formidable') ?>
            <a class="button-secondary cancel" href="?page=<?php echo FRM_PLUGIN_NAME ?>"><?php _e('Cancel', 'formidable') ?></a>
        </p>
    </form>
    </div>
    </div>
    </div>
    </div>
</div>
<?php require('footer.php'); ?>