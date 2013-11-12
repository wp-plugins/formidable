<div class="wrap">
    <div id="icon-edit-pages" class="icon32"><br/></div>
    <h2><?php echo ($form) ? (FrmAppHelper::truncate($form->name, 25) .' ') : ''; _e('Entries', 'formidable'); ?></h2>

    <?php require(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php'); ?>

    <?php if($form) FrmAppController::get_form_nav($form->id, true); ?>

    <?php FrmAppController::update_message('view, search, export, and bulk delete your saved entries'); ?>

    <?php if(!$form or $entry_count){ ?>
    <img src="<?php echo FrmAppHelper::plugin_url() ?>/screenshot-3.png" alt="Entries List" style="max-width:100%"/>
    <?php }else{ ?>
    <table class="wp-list-table widefat post fixed" cellspacing="0">
        <thead>
            <tr><th class="manage-column" scope="col"> </th></tr>
        </thead>
        <tbody>
            <tr><td>
            <?php include(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/no_entries.php'); ?>
            </td></tr>
        </tbody>
        <tfoot>
            <tr><th class="manage-column" scope="col"> </th></tr>
        </tfoot>
    </table>
    <?php } ?>
</div>

