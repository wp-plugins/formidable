<div id="form_settings_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Edit Form', 'formidable') ?>
        <a href="?page=formidable-new" class="button add-new-h2"><?php _e('Add New', 'formidable'); ?></a>
    </h2>
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php');
    
    if(version_compare( $GLOBALS['wp_version'], '3.3.3', '<')){ ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
    <?php   
        require(FRM_VIEWS_PATH .'/frm-forms/sidebar-settings.php'); 
    }else{ ?>
    <div id="poststuff">
    <?php } ?>
    
        <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
            <div class="alignleft">
            <?php FrmAppController::get_form_nav($id, true); ?>
            </div>
            
<form method="post">     
    <p style="clear:left;">        
        <input type="submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" />
        <?php _e('or', 'formidable') ?>
        <a class="button-secondary cancel" href="<?php echo esc_url(admin_url('admin.php?page=formidable') . '&frm_action=edit&id='. $id) ?>"><?php _e('Cancel', 'formidable') ?></a>
        <span style="margin-left:8px;">
        <?php FrmFormsHelper::forms_dropdown('frm_switcher', '', __('Switch Form', 'formidable'), false, "frmAddNewForm(this.value,'settings')"); ?>
        </span>
        <?php if($frmpro_is_installed and function_exists('icl_t')){ ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=formidable') .'&frm_action=translate&id='. $id) ?>" class="button-secondary"><?php _e('Translate Form', 'formidable') ?></a>
        <?php } ?>
    </p>
    
    <div class="clear"></div> 

    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="hidden" name="frm_action" value="update_settings" />
    <div id="poststuff" class="metabox-holder">
    <div id="post-body">
        <div class="meta-box-sortables">
        <div class="categorydiv postbox">
        <h3 class="hndle"><span><?php echo FrmAppHelper::truncate($values['name'], 40) .' '. __('Settings', 'formidable') ?></span></h3>
        <div class="inside">
        <div class="contextual-help-tabs">
        <ul class="frm-category-tabs <?php if(version_compare( $GLOBALS['wp_version'], '3.3.0', '<')) echo 'category-tabs" id="category-tabs'; ?> frm-form-setting-tabs">
        	<li class="tabs active"><a onclick="frmSettingsTab(jQuery(this),'advanced');"><?php _e('General', 'formidable') ?></a></li>
        	<li><a href="#notification_settings"><?php _e('Emails', 'formidable') ?></a></li>
            <li><a href="#html_settings"><?php _e('Customize HTML', 'formidable') ?></a></li>
            <li><a href="#post_settings"><?php _e('Create Posts', 'formidable') ?></a></li>
            <?php foreach($sections as $sec_name => $section){ ?>
                <li><a onclick="frmSettingsTab(jQuery(this),'<?php echo $sec_name ?>');"><?php echo ucfirst($sec_name) ?></a></li>
            <?php } ?>
        </ul>
        </div>
        <div style="display:block;" class="advanced_settings tabs-panel">
        	<table class="form-table">
                <tr>
                    <td><label><?php _e('Form Key', 'formidable') ?></label></td>
                    <td><input type="text" name="form_key" value="<?php echo esc_attr($values['form_key']); ?>" /></td>
                </tr>

                <tr><td><label><?php _e('Submit Button Text', 'formidable') ?></label></td>
                    <td><input type="text" name="options[submit_value]" value="<?php echo esc_attr($values['submit_value']); ?>" /></td>
                </tr>
                
                <tr><td colspan="2"><input type="checkbox" name="options[custom_style]" id="custom_style" <?php echo ($values['custom_style']) ? ' checked="checked"' : ''; ?> value="1" />
                    <label for="custom_style"><?php _e('Use Formidable styling for this form', 'formidable') ?></label></td>
                </tr>
                
                <tr><td valign="top" colspan="2"><label><?php _e('Action After Form Submission', 'formidable') ?></label><br/>
                    <?php if(!$frmpro_is_installed){ ?>
                    <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('You must upgrade to Formidable Pro to get access to the second two options.', 'formidable') ?>" />
                    <?php } ?>

                        <input type="radio" name="options[success_action]" id="success_action_message" value="message" <?php checked($values['success_action'], 'message') ?> /> <label for="success_action_message"><?php _e('Display a Message', 'formidable') ?></label>
                        <input type="radio" name="options[success_action]" id="success_action_page" value="page" <?php checked($values['success_action'], 'page') ?> <?php if(!$frmpro_is_installed) echo 'disabled="disabled" '; ?>/> <label for="success_action_page" <?php echo $pro_feature ?>><?php _e('Display content from another page', 'formidable') ?></label>
                        <input type="radio" name="options[success_action]" id="success_action_redirect" value="redirect" <?php checked($values['success_action'], 'redirect') ?> <?php if(!$frmpro_is_installed) echo 'disabled="disabled" '; ?>/> <label for="success_action_redirect" <?php echo $pro_feature ?>><?php _e('Redirect to URL', 'formidable') ?></label>
                    </td>
                </tr>
                
                <tr class="success_action_redirect_box success_action_box" <?php echo ($values['success_action'] == 'redirect') ? '' : 'style="display:none;"'; ?>><td valign="top" colspan="2"><label><?php _e('Redirect to URL', 'formidable') ?></label>
                    <input type="text" name="options[success_url]" id="success_url" value="<?php if(isset($values['success_url'])) echo esc_attr($values['success_url']); ?>" size="55"></td>
                </tr>
                
                <tr class="success_action_message_box success_action_box" <?php echo ($values['success_action'] == 'message') ? '' : 'style="display:none;"'; ?>><td valign="top" colspan="2"><label><?php _e('Confirmation Message', 'formidable') ?></label>
                    <textarea id="success_msg" name="options[success_msg]" cols="50" rows="3" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($values['success_msg']); ?></textarea> <br/>
                    <div class="frm_show_form_opt">
                    <input type="checkbox" name="options[show_form]" id="show_form" value="1" <?php checked($values['show_form'], 1) ?> /> <label for="show_form"><?php _e('Show the form with the success message.', 'formidable')?></label>
                    </div>
                    <td>
                </tr>


                <?php do_action('frm_additional_form_options', $values); ?> 
                
                <tr><td colspan="2"><input type="checkbox" name="options[no_save]" id="no_save" value="1" <?php checked($values['no_save'], 1); ?> /> <?php _e('Do not store any entries submitted from this form.', 'formidable') ?> <span class="howto"><?php _e('Warning: There is no way retrieve unsaved entries.', 'formidable') ?></span></td></tr>
                
                <?php if (function_exists( 'akismet_http_post' )){ ?>
                <tr><td colspan="2"><?php _e('Use Akismet to check entries for spam for', 'formidable') ?>
                        <select name="options[akismet]">
                            <option value=""><?php _e('no one', 'formidable') ?></option>
                            <option value="1" <?php selected($values['akismet'], 1)?>><?php _e('everyone', 'formidable') ?></option>
                            <option value="logged" <?php selected($values['akismet'], 'logged')?>><?php _e('visitors who are not logged in', 'formidable') ?></option>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <?php
            $first_email = true;
            foreach($values['notification'] as $email_key => $notification){
                include(FRM_VIEWS_PATH .'/frm-forms/notification.php');
                unset($email_key);
                unset($notification);
                $first_email = false;
            } 
        if($frmpro_is_installed){ ?>
        <div id="frm_email_add_button" class="notification_settings hide_with_tabs" style="display:none;margin-top:10px;margin-left:150px;">
            <a href="javascript:frmAddEmailList(<?php echo $values['id'] ?>)" class="button-secondary">+ <?php _e('Add Notification', 'formidable') ?></a></td>
        </div>
        <?php } ?>
        
        <div id="html_settings" class="tabs-panel" style="display:none;">
            
            <div id="post-body-content" class="frm_top_container" style="margin-right:260px;">
                <p><label class="frm_primary_label"><?php _e('Before Fields', 'formidable') ?></label>
                <textarea name="options[before_html]" rows="4" id="before_html" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea(stripslashes($values['before_html'])) ?></textarea></p>

                <div id="add_html_fields">
                    <?php 
                    if (isset($values['fields'])){
                        foreach($values['fields'] as $field){
                            if (apply_filters('frm_show_custom_html', true, $field['type'])){ ?>
                                <p><label class="frm_primary_label"><?php echo $field['name'] ?></label>
                                <textarea name="field_options[custom_html_<?php echo $field['id'] ?>]" rows="7" id="custom_html_<?php echo $field['id'] ?>" class="field_custom_html frm_long_input"><?php echo FrmAppHelper::esc_textarea(stripslashes($field['custom_html'])) ?></textarea></p>
                            <?php }
                            unset($field);
                        }
                    } ?>
                </div>

                <p><label class="frm_primary_label"><?php _e('After Fields', 'formidable') ?></label>
                <textarea name="options[after_html]" rows="3" id="after_html" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea(stripslashes($values['after_html'])) ?></textarea></p> 
            </div>
        </div>
        <div id="post_settings" class="tabs-panel" style="display:none;">
            <?php if($frmpro_is_installed)
                FrmProFormsController::post_options($values);
            else
                FrmAppController::update_message('create and edit posts, pages, and custom post types through your forms');
            ?>
        </div>
        
        <?php foreach($sections as $sec_name => $section){
            if(isset($section['class'])){
                call_user_func(array($section['class'], $section['function']), $values); 
            }else{
                call_user_func((isset($section['function']) ? $section['function'] : $section), $values); 
            }
        } ?>
    
        <?php do_action('frm_add_form_option_section', $values); ?>
        <div class="clear"></div>
        </div>
        </div>
        </div>
</div>

</div>

    <p>        
        <input type="submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" />
        <?php _e('or', 'formidable') ?>
        <a class="button-secondary cancel" href="<?php echo admin_url('admin.php?page=formidable') ?>&amp;frm_action=edit&amp;id=<?php echo $id ?>"><?php _e('Cancel', 'formidable') ?></a>
    </p>
    </form>


    </div>
    <?php
        if(version_compare( $GLOBALS['wp_version'], '3.3.2', '>'))
            require(FRM_VIEWS_PATH .'/frm-forms/sidebar-settings.php'); 
    ?>
    </div>
</div>
</div>
<script type="text/javascript">
__FRMURL='<?php echo $frm_ajax_url ?>';
</script>