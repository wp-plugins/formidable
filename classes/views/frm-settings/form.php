<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Settings', FRM_PLUGIN_NAME); ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    
    <form name="frm_settings_form" method="post" action="">
        <input type="hidden" name="action" value="process-form">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table">
            <tr class="form-field">
              <td valign="top" width="200px"><?php _e('Preview Page', FRM_PLUGIN_NAME); ?>: </td>
              <td>
                <?php FrmAppHelper::wp_pages_dropdown( $frm_settings->preview_page_id_str, $frm_settings->preview_page_id ) ?>
              </td>
            </tr>
            
            <tr class="form-field">
                <td><?php _e('Styling', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('You can override this setting on individual forms.', FRM_PLUGIN_NAME) ?>" /></td>
                <td>
                    <input type="checkbox" value="1" id="frm_custom_style" name="frm_custom_style" <?php checked($frm_settings->custom_style, 1) ?>>
                    <?php _e('Use Formidable styling settings for my forms', FRM_PLUGIN_NAME); ?>
                </td>
            </tr>
            
            <tr class="form-field">
                <td><?php _e('Default Messages', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('You can override the success message and submit button settings on individual forms.', FRM_PLUGIN_NAME) ?>" /></td>
                <td>
                    <?php _e('Success Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The default message seen after a form is submitted.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_success_msg" name="frm_success_msg" class="frm_elastic_text"><?php echo $frm_settings->success_msg ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Failed Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The message seen when a form is submitted and passes validation, but something goes wrong. You will likely never see this error.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_failed_msg" name="frm_failed_msg" class="frm_elastic_text"><?php echo $frm_settings->failed_msg ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Login Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The message seen when a user who is not logged-in views a form only logged-in users can submit.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_login_msg" name="frm_login_msg" class="frm_elastic_text"><?php echo $frm_settings->login_msg ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>    
                    <?php _e('Submit Button', FRM_PLUGIN_NAME); ?>:
                    <input type="text" value="<?php echo $frm_settings->submit_value ?>" id="frm_submit_value" name="frm_submit_value">
                </td>
            </tr>
        
            
            <?php do_action('frm_settings_form', $frm_settings); ?>
            
        </table>

        <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Update Options', FRM_PLUGIN_NAME) ?>" />
        </p>

    </form>
</div>
