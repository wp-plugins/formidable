<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Settings', FRM_PLUGIN_NAME); ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    
    <form name="frm_settings_form" method="post" action="">
        <input type="hidden" name="action" value="process-form">
        <?php wp_nonce_field('update-options'); ?>
        <p class="submit" style="padding-bottom:0;">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options', FRM_PLUGIN_NAME) ?>" />
        </p>
        <table class="form-table">
            <tr class="form-field">
              <td valign="top" width="200px"><?php _e('Preview Page', FRM_PLUGIN_NAME); ?>: </td>
              <td>
                <?php FrmAppHelper::wp_pages_dropdown( $frm_settings->preview_page_id_str, $frm_settings->preview_page_id ) ?>
              </td>
            </tr>
            
            <tr class="form-field">
                <td valign="top"><?php _e('Stylesheet', FRM_PLUGIN_NAME); ?>: </td>
                <td>
                    <p><input type="checkbox" value="1" id="frm_custom_style" name="frm_custom_style" <?php checked($frm_settings->custom_style, 1) ?>>
                    <?php _e('Use Formidable styling settings for my forms', FRM_PLUGIN_NAME); ?> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('You can override this setting on individual forms.', FRM_PLUGIN_NAME) ?>" />
                    </p>
                    
                    <p><input type="checkbox" value="1" id="frm_custom_stylesheet" name="frm_custom_stylesheet" <?php checked($frm_settings->custom_stylesheet, 1) ?>>
                    <?php _e('Exclude the Formidable stylesheet from ALL forms', FRM_PLUGIN_NAME); ?> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('You cannot override this setting on individual forms, so only check this box if you will not be using the stylesheet on any forms.', FRM_PLUGIN_NAME) ?>" /></p>
                </td>
            </tr>
            
            <tr class="form-field">
                <td valign="top"><?php _e('User Permissions', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('Select users that are allowed access to Formidable. Without access to View Forms, users will be unable to see the Formidable menu.', FRM_PLUGIN_NAME) ?>" /></td>
                <td>
                    <?php foreach($frm_roles as $frm_role => $frm_role_description){ ?>
                        <label style="width:200px;float:left;text-align:right;padding-right:10px;"><?php echo $frm_role_description ?>:</label> <?php FrmAppHelper::wp_roles_dropdown( $frm_role, $frm_settings->$frm_role ) ?><br/>
                    <?php } ?>
                    
                </td>    
            </tr>
            
            <tr class="form-field">
                <td valign="top"><?php _e('Default Messages', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('You can override the success message and submit button settings on individual forms.', FRM_PLUGIN_NAME) ?>" /></td>
                <td>
                    <?php _e('Success Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The default message seen after a form is submitted.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_success_msg" name="frm_success_msg" class="frm_elastic_text"><?php echo stripslashes($frm_settings->success_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Failed Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The message seen when a form is submitted and passes validation, but something goes wrong. You will likely never see this error.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_failed_msg" name="frm_failed_msg" class="frm_elastic_text"><?php echo stripslashes($frm_settings->failed_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Login Message', FRM_PLUGIN_NAME); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The message seen when a user who is not logged-in views a form only logged-in users can submit.', FRM_PLUGIN_NAME) ?>" /><br/>
                    <textarea id="frm_login_msg" name="frm_login_msg" class="frm_elastic_text"><?php echo stripslashes($frm_settings->login_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>    
                    <?php _e('Submit Button', FRM_PLUGIN_NAME); ?>:<br/>
                    <input type="text" value="<?php echo $frm_settings->submit_value ?>" id="frm_submit_value" name="frm_submit_value">
                </td>
            </tr>
            
            <?php do_action('frm_settings_form', $frm_settings); ?>
            
        </table>
        
        <p class="alignright frm_uninstall"><a href="<?php echo $frm_ajax_url ?>?action=frm_uninstall" onClick="confirm('<?php _e('Are you sure you want to do this? Clicking OK will delete all forms, form data, and all other Formidable data. There is no Undo.', 'formidable') ?>')"><?php _e('Uninstall Formidable', 'formidable') ?></a></p>
        <p class="submit">
        <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options', FRM_PLUGIN_NAME) ?>" />
        </p>

    </form>
</div>
