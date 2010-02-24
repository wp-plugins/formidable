<div class="wrap">
    <div class="frmicon"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: Edit Form</h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

    <div class="frm_form_builder alignleft" id="poststuff">
    <form name="form1" method="post" action="">
        <p class="submit">
            <input type="submit" name="Submit" value="Update" class="button-primary" /> or
            <a href="?page=<?php echo FRM_PLUGIN_NAME ?>">Cancel</a>
        </p>
        
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php wp_nonce_field('update-options'); ?>

        <?php require(FRM_VIEWS_PATH.'/frm-forms/form.php'); ?>

        <p class="submit">
            <input type="submit" name="Submit" value="Update" class="button-primary" /> or
            <a href="?page=<?php echo FRM_PLUGIN_NAME ?>">Cancel</a>
        </p>
    </form>
    </div>

<?php require('add_field_links.php'); ?>
</div>
<?php require('footer.php'); ?>