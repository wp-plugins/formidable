<div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php _e('Settings', 'formidable'); ?></h2>

    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    
    <form name="frm_settings_form" method="post" action="">
        <input type="hidden" name="action" value="process-form" />
        <?php wp_nonce_field('update-options'); ?>
        <p class="submit" style="padding-bottom:0;">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options', 'formidable') ?>" />
        </p>
        <table class="form-table">
            <tr class="form-field">
              <td valign="top" width="200px"><?php _e('Preview Page', 'formidable'); ?>: </td>
              <td>
                <?php FrmAppHelper::wp_pages_dropdown( $frm_settings->preview_page_id_str, $frm_settings->preview_page_id ) ?>
              </td>
            </tr>
            
            <tr class="form-field">
                <td valign="top"><?php _e('Stylesheets', 'formidable'); ?>: </td>
                <td>
                    
                    <p><?php _e('Load Formidable styling', 'formidable') ?>
                        <select id="frm_load_style" name="frm_load_style">
                        <option value="all" <?php selected($frm_settings->load_style, 'all') ?>><?php _e('on every page of your site', 'formidable') ?> 
                        <option value="dynamic" <?php selected($frm_settings->load_style, 'dynamic') ?>><?php _e('only on applicable pages', 'formidable') ?> 
                        <option value="none" <?php selected($frm_settings->load_style, 'none') ?>><?php _e('Don\'t use Formidable styling on any page', 'formidable') ?> 
                        </select>
                    </p>
                    
                    <?php if($frmpro_is_installed){ ?>
                    <p><input type="checkbox" value="1" id="frm_jquery_css" name="frm_jquery_css" <?php checked($frm_settings->jquery_css, 1) ?> />
                    <?php _e('Include the jQuery CSS on ALL pages', 'formidable'); ?> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The styling for the date field calendar. Some users may be using this css on pages other than just the ones that include a date field.', 'formidable') ?>" /></p>
                    <?php } ?>
                </td>
            </tr>
            
            <tr class="form-field">
                <td valign="top"><?php _e('User Permissions', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('Select users that are allowed access to Formidable. Without access to View Forms, users will be unable to see the Formidable menu.', 'formidable') ?>" /></td>
                <td>
                    <?php foreach($frm_roles as $frm_role => $frm_role_description){ ?>
                        <label style="width:200px;float:left;text-align:right;padding-right:10px;"><?php echo $frm_role_description ?>:</label> <?php FrmAppHelper::wp_roles_dropdown( $frm_role, $frm_settings->$frm_role ) ?><br/>
                    <?php } ?>
                    
                </td>    
            </tr>
            
            <tr class="form-field" valign="top">
                <td><?php _e('reCAPTCHA', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_big" title="<?php _e('reCAPTCHA is a free, accessible CAPTCHA service that helps to digitize books while blocking spam on your blog. reCAPTCHA asks commenters to retype two words scanned from a book to prove that they are a human. This verifies that they are not a spambot.', 'formidable') ?>" />
                </td>
            	<td>
        			reCAPTCHA requires an API key, consisting of a "public" and a "private" key. You can sign up for a <a href="http://recaptcha.net/api/getkey?domain=localhost.localdomain&amp;app=wordpress">free reCAPTCHA key</a>.
        			<br/>
        			
        				<!-- reCAPTCHA public key -->
        				<label style="width:135px;float:left;text-align:right;padding-right:10px;"><?php _e('Public Key', 'formidable') ?>:</label>
        				<input name="frm_pubkey" id="frm_pubkey" size="42" value="<?php echo $frm_settings->pubkey ?>" />
        				<br/>
        				<!-- reCAPTCHA private key -->
        				<label style="width:135px;float:left;text-align:right;padding-right:10px;"><?php _e('Private Key', 'formidable') ?>:</label>
        				<input name="frm_privkey" id="frm_privkey" size="42" value="<?php echo $frm_settings->privkey ?>" />
        			
            	</td>
            </tr>

            <tr class="form-field" valign="top">
            	<td></td>
            	<td>
        		    <label style="width:135px;float:left;text-align:right;padding-right:10px;"><?php _e('reCAPTCHA Theme', 'formidable') ?>:</label>
        			<select name="frm_re_theme" id="frm_re_theme">
        			<?php foreach(array('red' => __('Red', 'formidable'), 'white' => __('White', 'formidable'), 'blackglass' => __('Black Glass', 'formidable'), 'clean' => __('Clean', 'formidable')) as $theme_value => $theme_name){ ?>
        			<option value="<?php echo $theme_value ?>" <?php selected($frm_settings->re_theme, $theme_value) ?>><?php echo $theme_name ?></option>
        			<?php } ?>
        			</select><br/>
            		
    			    <label style="width:135px;float:left;text-align:right;padding-right:10px;"><?php _e('reCAPTCHA Language', 'formiable') ?>:</label>
    				<select name="frm_re_lang" id="frm_re_lang">
    				    <?php foreach(array('en' => __('English', 'formidable'), 'nl' => __('Dutch', 'formidable'), 'fr' => __('French', 'formidable'), 'de' => __('German', 'formidable'), 'pt' => __('Portuguese', 'formidable'), 'ru' => __('Russian', 'formidable'), 'es' => __('Spanish', 'formidable'), 'tr' => __('Turkish', 'formidable')) as $lang => $lang_name){ ?>
        				<option value="<?php echo $lang ?>" <?php selected($frm_settings->re_lang, $lang) ?>><?php echo $lang_name ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>    
                            
            
            <tr class="form-field">
                <td valign="top"><?php _e('Default Messages', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('You can override the success message and submit button settings on individual forms.', 'formidable') ?>" /></td>
                <td>
                    <?php _e('Success Message', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The default message seen after a form is submitted.', 'formidable') ?>" /><br/>
                    <textarea id="frm_success_msg" name="frm_success_msg"><?php echo stripslashes($frm_settings->success_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Failed or Duplicate Entry Message', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The message seen when a form is submitted and passes validation, but something goes wrong. You will likely never see this error.', 'formidable') ?>" /><br/>
                    <textarea id="frm_failed_msg" name="frm_failed_msg"><?php echo stripslashes($frm_settings->failed_msg) ?></textarea>
                </td>
            </tr> 
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Incorrect Captcha Message', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The message seen when a captcha response is either incorrect or missing.', 'formidable') ?>" /><br/>
                    <textarea id="frm_re_msg" name="frm_re_msg"><?php echo stripslashes($frm_settings->re_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>        
                    <?php _e('Login Message', 'formidable'); ?>: <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('The message seen when a user who is not logged-in views a form only logged-in users can submit.', 'formidable') ?>" /><br/>
                    <textarea id="frm_login_msg" name="frm_login_msg"><?php echo stripslashes($frm_settings->login_msg) ?></textarea>
                </td>
            </tr>
            
            <tr class="form-field">
                <td></td>
                <td>    
                    <?php _e('Default Submit Button', 'formidable'); ?>:<br/>
                    <input type="text" value="<?php echo $frm_settings->submit_value ?>" id="frm_submit_value" name="frm_submit_value" />
                </td>
            </tr>
            
            <?php do_action('frm_settings_form', $frm_settings); ?>
            
        </table>
        
        <p class="alignright frm_uninstall"><a href="<?php echo $frm_ajax_url ?>?action=frm_uninstall" onClick="return confirm('<?php _e('Are you sure you want to do this? Clicking OK will delete all forms, form data, and all other Formidable data. There is no Undo.', 'formidable') ?>')"><?php _e('Uninstall Formidable', 'formidable') ?></a></p>
        <p class="submit">
        <input class="button-primary" type="submit" name="Submit" value="<?php _e('Update Options', 'formidable') ?>" />
        </p>

    </form>
</div>
