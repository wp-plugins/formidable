<div class="wrap">
    <div id="icon-edit-pages" class="icon32"><br/></div>
    <h2><?php _e('Form Entries', 'formidable') ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <?php if(isset($id)) FrmAppController::get_form_nav($id); ?>

    <?php FrmAppController::update_message('view, search, export, and bulk delete your saved entries'); ?>

    <img src="http://static.strategy11.com.s3.amazonaws.com/entries-list.png" alt="Entries List" style="max-width:100%"/>

</div>

