<div id="form_settings_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Edit Form', 'formidable') ?></h2>
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>
    <div class="alignright">
        <div id="frm_form_options">
            <?php if(!isset($hide_preview) or !$hide_preview){ 
                if (!$values['is_template']){ ?>
            <p class="howto" style="margin-top:0;"><?php _e('Insert into a post, page or text widget', 'formidable') ?>
            <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus='this.select();' value='[formidable id=<?php echo $id; ?>]' /></p>
            <?php } ?>

            <p class="frm_orange"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="_blank"><?php _e('Preview Form', 'formidable') ?></a>
            <?php global $frm_settings; 
                if ($frm_settings->preview_page_id > 0){ ?>
                or <a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="_blank"><?php _e('Preview in Current Theme', 'formidable') ?></a>
            <?php } ?>
            </p>
            <?php
            } ?>
        </div>
    </div>
    <div class="alignleft">
    <?php FrmAppController::get_form_nav($id, true); ?>
    </div>
    
    <div class="clear"></div> 
    <form method="post"> 
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="action" value="update_settings" />
    <div id="poststuff" class="metabox-holder">
    <div id="post-body">
        <div class="meta-box-sortables">
        <div class="categorydiv postbox">
        <h3 class="hndle"><span><?php _e('Settings', 'formidable') ?></span></h3>
        <div class="inside">
        <ul id="category-tabs" class="category-tabs">
        	<li class="tabs"><a onclick="frmSettingsTab(jQuery(this),'advanced');"><?php _e('General', 'formidable') ?></a></li>
        	<li><a onclick="frmSettingsTab(jQuery(this),'notification');"><?php _e('Emails', 'formidable') ?></a></li>
            <li><a onclick="frmSettingsTab(jQuery(this),'html');"><?php _e('Customize HTML', 'formidable') ?></a></li>
            <?php foreach($sections as $sec_name => $section){ ?>
                <li><a onclick="frmSettingsTab(jQuery(this),'<?php echo $sec_name ?>');"><?php echo ucfirst($sec_name) ?></a></li>
            <?php } ?>
        </ul>

        <div style="display:block;" class="advanced_settings tabs-panel">
        	<table class="form-table">
                <tr>
                    <td width="200px"><label><?php _e('Form ShortCodes', 'formidable') ?></label> <a href="http://formidablepro.com/publish-your-forms/" target="_blank"><img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('Key and id are generally synonymous. For more information on using this shortcode, click now.', 'formidable') ?>" /></a></td>
                    <td>[formidable id=<?php echo $id; ?> title=true description=true] or [formidable key=<?php echo $values['form_key']; ?>]</td>
                </tr>

                <tr>
                    <td><label><?php _e('Form Key', 'formidable') ?></label></td>
                    <td><input type="text" name="form_key" value="<?php echo $values['form_key']; ?>" /></td>
                </tr>

                <tr><td><label><?php _e('Styling', 'formidable') ?></label></td>
                    <td><input type="checkbox" name="options[custom_style]" id="custom_style" <?php echo ($values['custom_style']) ? (' checked="checked"') : (''); ?> />
                    <label for="custom_style"><?php _e('Use Formidable styling for this form', 'formidable') ?></label></td>
                </tr> 

                <tr><td><label><?php _e('Submit Button Text', 'formidable') ?></label></td>
                    <td><input type="text" name="options[submit_value]" value="<?php echo $values['submit_value']; ?>" /></td>
                </tr>
                
                <tr><td valign="top"><label><?php _e('Action After Form Submission', 'formidable') ?></label>
                    <?php if(!$frmpro_is_installed){ ?>
                    <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('To use the second two options, you must upgrade to Formidable Pro.', 'formidable') ?>" />
                    <?php } ?>
                    </td>
                    <td>
                        <input type="radio" name="options[success_action]" id="success_action_message" value="message" <?php checked($values['success_action'], 'message') ?> /> <label for="success_action_message"><?php _e('Display a Message', 'formidable') ?></label>
                        <input type="radio" name="options[success_action]" id="success_action_page" value="page" <?php checked($values['success_action'], 'page') ?> <?php if(!$frmpro_is_installed) echo 'disabled="disabled" '; ?>/> <label for="success_action_page"><?php _e('Display content from another page', 'formidable') ?></label>
                        <input type="radio" name="options[success_action]" id="success_action_redirect" value="redirect" <?php checked($values['success_action'], 'redirect') ?> <?php if(!$frmpro_is_installed) echo 'disabled="disabled" '; ?>/> <label for="success_action_redirect"><?php _e('Redirect', 'formidable') ?></label>
                    </td>
                </tr>
                
                <tr class="success_action_message_box success_action_box"><td valign="top"><label><?php _e('Confirmation Message', 'formidable') ?></label></td>
                    <td><?php if($frmpro_is_installed and isset($values['id'])){ FrmProFieldsHelper::get_shortcode_select($values['id'], 'success_msg'); echo '<br/>'; } ?>
                        <textarea id="success_msg" name="options[success_msg]" cols="50" rows="4" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($values['success_msg']); ?></textarea> <br/>
                    <input type="checkbox" name="options[show_form]" id="show_form" value="1" <?php checked($values['show_form'], 1) ?>> <label for="show_form"><?php _e('Show the form with the success message.', 'formidable')?></label>
                    <td>
                </tr>

                <?php do_action('frm_additional_form_options', $values); ?> 

                <?php if (function_exists( 'akismet_http_post' )){ ?>
                <tr><td colspan="2"><input type="checkbox" name="options[akismet]" id="akismet" value="1" <?php checked($values['akismet'], 1); ?> /> <?php _e('Use Akismet to check entries for spam', 'formidable') ?></td></tr>
                <?php } ?>
            </table>
        </div>

        <div class="notification_settings tabs-panel" style="display:none;">
        	<table class="form-table">
                  <tr>
                      <td width="200px"><label><?php _e('Email Form Responses to', 'formidable') ?></label> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('To send to multiple addresses, separate each address with a comma', 'formidable') ?>" /></td>
                      <td><input type="text" name="options[email_to]" value="<?php echo $values['email_to']; ?>" class="frm_long_input" /></td>
                 </tr>
                 <?php do_action('frm_additional_form_notification_options', $values); ?> 
             </table>
        </div>
        
        <div class="html_settings tabs-panel has-right-sidebar" style="display:none;">
            <div class="inner-sidebar frm_html_legend postbox" style="width:240px;min-width:100px;">
                <h3><?php _e('Key', 'formidable') ?></h3>
                <div class="inside">
                <ul>
                    <li><b><?php _e('Form Name', 'formidable') ?>:</b> <pre>[form_name]</pre></li>
                    <li><b><?php _e('Form Description', 'formidable') ?>:</b> <pre>[form_description]</pre></li>
                    <li><b><?php _e('Form Key', 'formidable') ?>:</b> <pre>[form_key]</pre></li>
                    <li><b><?php _e('Delete Entry Link', 'formidable') ?>:</b> <pre>[deletelink]</pre></li>
                </ul>
                <ul>
                    <li><b><?php _e('Field Id', 'formidable') ?>:</b> <pre>[id]</pre></li>
                    <li><b><?php _e('Field Key', 'formidable') ?>:</b> <pre>[key]</pre></li>
                    <li><b><?php _e('Field Name', 'formidable') ?>:</b> <pre>[field_name]</pre></li>
                    <li><b><?php _e('Field Description', 'formidable') ?>:</b> <pre>[description]</pre></li>
                    <li><b><?php _e('Label Position', 'formidable') ?>:</b> <pre>[label_position]</pre></li>
                    <li><b><?php _e('Required label', 'formidable') ?>:</b> <pre>[required_label]</pre></li>
                    <li><b><?php _e('Input Field', 'formidable') ?>:</b> <pre>[input]</pre><br/>
                        <?php _e('Show a single radio or checkbox option by replacing "1" with the order of the option', 'formidable') ?>: <pre>[input opt=1]</pre><br/>
                        <?php _e('Hide the option labels', 'formidable') ?>: <pre>[input label=0]</pre>
                    </li>
                    <li><b><?php _e('Add class name if field is required', 'formidable') ?>:</b> <pre>[required_class]</pre></li>
                    <li><b><?php _e('Add class name if field has an error on form submit', 'formidable') ?>:</b> <pre>[error_class]</pre></li>
                </ul>
                </div>
            </div>
            
            <div id="post-body-content" class="frm_top_container" style="margin-right:260px;">
                <p><label class="frm_primary_label"><?php _e('Before Fields', 'formidable') ?></label>
                <textarea name="options[before_html]" rows="4" class="frm_long_input"><?php echo $values['before_html']?></textarea></p>

                <div id="add_html_fields">
                    <?php 
                    if (isset($values['fields'])){
                        foreach($values['fields'] as $field){
                            if (apply_filters('frm_show_custom_html', true, $field['type'])){ ?>
                                <p><label class="frm_primary_label"><?php echo $field['name'] ?></label>
                                <textarea name="field_options[custom_html_<?php echo $field['id'] ?>]" rows="7" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($field['custom_html']) ?></textarea></p>
                            <?php }
                            unset($field);
                        }
                    } ?>
                </div>

                <p><label class="frm_primary_label"><?php _e('After Fields', 'formidable') ?></label>
                <textarea name="options[after_html]" rows="3" class="frm_long_input"><?php echo $values['after_html']?></textarea></p> 
            </div>
        </div>
        
        <?php foreach($sections as $sec_name => $section){
            if(isset($section['class'])){
                call_user_func(array($section['class'], $section['function']), $values); 
            }else{
                call_user_func((isset($section['function']) ? $section['function'] : $section), $values); 
            }
        } ?>
    
        <?php do_action('frm_add_form_option_section', $values); ?>
        </div>
        </div>
        </div>
</div>

</div>

    <p>        
        <input type="submit" name="Submit" value="<?php _e('Update', 'formidable') ?>" class="button-primary" />
        <?php _e('or', 'formidable') ?>
        <a class="button-secondary cancel" href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>&amp;action=edit&amp;id=<?php echo $id ?>"><?php _e('Cancel', 'formidable') ?></a>
    </p>
    </form>
</div>