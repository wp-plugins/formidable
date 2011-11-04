<div class="wrap">
    <div id="icon-edit-pages" class="icon32"><br/></div>
    <h2><?php _e('Form Entries', 'formidable') ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <?php if(isset($id)) FrmAppController::get_form_nav($id); ?>

    
    <div class="frm_update_msg">
    This plugin version does not give you access to view, search, export, and bulk delete your saved entries.<br/>
    <a href="http://formidablepro.com/pricing/" target="_blank">Compare</a> our plans to see about upgrading to Pro.
    </div>
    <img src="http://static.strategy11.com.s3.amazonaws.com/entries-list.png" alt="Entries List" style="max-width:100%"/>

</div>

