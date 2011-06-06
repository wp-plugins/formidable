<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Add New Form', 'formidable') ?></h2>
    
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <br/>
    <p>
        <?php FrmFormsHelper::get_template_dropdown($all_templates); ?>
        <?php _e('or', 'formidable') ?>
        <a href="<?php echo add_query_arg('action', 'new') ?>" class="button"><?php _e('Add New Form', 'formidable') ?></a>
    </p>

</div>