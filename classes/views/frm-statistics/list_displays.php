<div class="wrap">
    <div id="icon-themes" class="icon32"><br/></div>
    <h2><?php _e('Views', 'formidable'); ?></h2>

    <?php 
        if($form) FrmAppController::get_form_nav($form);
		require(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php');
        FrmAppController::update_message('display collected data in lists, calendars, and other formats'); 
    ?>

    <img src="http://fp.strategy11.com/images/custom-display-settings.png" alt="Display" style="max-width:100%"/>

</div>