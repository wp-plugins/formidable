<div class="wrap">
    <div class="frmicon"><br></div>
    
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Create Form', FRM_PLUGIN_NAME) ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

    <div class="frm_form_builder alignleft" id="poststuff">
        <form name="form1" method="post" action="">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php wp_nonce_field('update-options'); ?>

            <?php require(FRM_VIEWS_PATH.'/frm-forms/form.php'); ?>

            <p class="submit">
                <input type="submit" name="Submit" value="<?php _e('Create', FRM_PLUGIN_NAME) ?>" class="button-primary" /> <?php _e('or', FRM_PLUGIN_NAME) ?> 
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=destroy&id=<?php echo $id; ?>"><?php _e('Cancel', FRM_PLUGIN_NAME) ?></a>
            </p>
        </form>
    </div>

<?php require('add_field_links.php'); ?>
</div> 
<?php require('footer.php'); ?>