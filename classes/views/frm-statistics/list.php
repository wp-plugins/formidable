<div class="wrap">
    <div class="frm_report_icon icon32"><br/></div>
    <h2><?php _e('Reports', 'formidable') ?></h2>

    <?php 
        require(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php');
        if($form) FrmAppController::get_form_nav($form, true);
    ?>
    <div class="clear"></div>
    <?php FrmAppController::update_message('view reports and statistics on your saved entries'); ?>

    <img src="http://fp.strategy11.com/wp-content/themes/formidablepro/images/reports1.png" alt="Reports" style="max-width:100%"/>

</div>