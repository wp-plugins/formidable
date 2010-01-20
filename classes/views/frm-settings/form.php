<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: Settings</h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    

    <form name="frm_settings_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="action" value="process-form">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table">
            <tr class="form-field">
              <td valign="top" width="10%"><?php _e('Preview Page', FRM_PLUGIN_NAME); ?>: </td>
              <td>
                <?php FrmAppHelper::wp_pages_dropdown( $frm_settings->preview_page_id_str, $frm_settings->preview_page_id )?>
              </td>
            </tr>
            
            <?php do_action('frm_settings_form', $frm_settings); ?>
        </table>

        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', FRM_PLUGIN_NAME) ?>" />
        </p>

    </form>
</div>
