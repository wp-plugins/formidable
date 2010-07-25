<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Create Form', FRM_PLUGIN_NAME) ?></h2>
    
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <br/>
    <p>
        <?php FrmFormsHelper::get_template_dropdown($all_templates); ?>
        <?php _e('or', FRM_PLUGIN_NAME) ?>
        <a href="<?php echo add_query_arg('action','new') ?>"><?php _e('Create New Form', FRM_PLUGIN_NAME) ?></a>
    </p>

</div>