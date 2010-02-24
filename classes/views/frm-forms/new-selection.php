<div class="wrap">
    <div class="frmicon"><br></div>
    
    <h2><?php echo FRM_PLUGIN_TITLE ?>: Create Form</h2>
    
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <br/>
    <p>
        <?php FrmFormsHelper::get_template_dropdown($all_templates); ?>
        or
        <a href="<?php echo add_query_arg('action','new') ?>">Create New Form</a>
    </p>

</div>