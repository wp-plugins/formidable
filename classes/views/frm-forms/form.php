<div id="frm_form_editor_container">
<div id="titlediv">
<div id="form_desc" class="edit_form_item frm_field_box frm_head_box">
    <h2 class="frm_ipe_form_name" id="frmform_<?php echo $id; ?>"><?php echo $values['name']; ?></h2>
    <div class="frm_ipe_form_desc"><?php echo $values['description']; ?></div>
</div>
</div>

<ul id="new_fields">
<?php 
if (isset($values['fields']) && !empty($values['fields'])){
    foreach($values['fields'] as $field){
        $field_name = "item_meta[". $field['id'] ."]";
        require('add_field.php');
    }
} ?>
</ul>

<?php if (!$values['is_template']){ ?>
<div class="themeRoller clearfix">
    <div class="theme-group clearfix">
	    <div class="theme-group-header state-default">
		    <span class="icon icon-triangle-1-e"><?php _e('Collapse', 'formidable') ?></span>
		    <a href="#"><?php _e('Advanced Form Options', 'formidable') ?></a>
		</div><!-- /theme group Error -->
		<div class="theme-group-content corner-bottom clearfix">
            <div class="clearfix">
                <table class="form-table">
                    <tr>
                        <td width="200px"><label><?php _e('Form ShortCodes', 'formidable') ?>:</label> <a href="http://formidablepro.com/publish-your-forms/" target="_blank"><img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('Key and id are generally synonymous. For more information on using this shortcode, click now.', 'formidable') ?>" /></a></td>
                        <td>[formidable id=<?php echo $id; ?> title=true description=true]  or [formidable key=<?php echo $values['form_key']; ?>]</td>
                    </tr>

                    <tr>
                        <td><label><?php _e('Form Key', 'formidable') ?>:</label></td>
                        <td><input type="text" name="form_key" value="<?php echo $values['form_key']; ?>" /></td>
                    </tr>

                    <tr><td><label><?php _e('Styling', 'formidable') ?>:</label></td>
                        <td><input type="checkbox" name="options[custom_style]" id="custom_style" <?php echo ($values['custom_style']) ? (' checked="checked"') : (''); ?> />
                        <label for="custom_style"><?php _e('Use Formidable styling for this form', 'formidable') ?></label></td>
                    </tr> 

                    <tr><td><label><?php _e('Submit Button Text', 'formidable') ?>:</label></td>
                        <td><input type="text" name="options[submit_value]" value="<?php echo $values['submit_value']; ?>" /></td>
                    </tr>
                    
                    <tr><td valign="top"><label><?php _e('Action After Form Submission', 'formidable') ?>:</label>
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
                    
                    <tr class="success_action_message_box success_action_box"><td valign="top"><label><?php _e('Confirmation Message', 'formidable') ?>:</label></td>
                        <td><?php if($frmpro_is_installed){ FrmProFieldsHelper::get_shortcode_select($values['id'], 'success_msg'); echo '<br/>'; } ?>
                            <textarea id="success_msg" name="options[success_msg]" cols="50" rows="4" class="frm_elastic_text" style="width:98%"><?php echo $values['success_msg']; ?></textarea> <br/>
                        <input type="checkbox" name="options[show_form]" id="show_form" value="1" <?php checked($values['show_form'], 1) ?>> <label for="show_form"><?php _e('Show the form with the success message.', 'formidable')?></label>
                        <td>
                    </tr>

                    <?php do_action('frm_additional_form_options', $values); ?> 

                    <?php if (function_exists( 'akismet_http_post' )){ ?>
                    <tr><td colspan="2"><input type="checkbox" name="options[akismet]" id="akismet" value="1" <?php checked($values['akismet'], 1); ?> /> <?php _e('Use Akismet to check entries for spam', 'formidable') ?></td></tr>
                    <?php } ?>
                </table>
			</div>
		</div><!-- /theme group content -->
	</div><!-- /theme group -->
	
	<div class="theme-group clearfix">
		<div class="theme-group-header state-default">
			<span class="icon icon-triangle-1-e"><?php _e('Collapse', 'formidable') ?></span>
			<a href="#"><?php _e('Form Notification Options', 'formidable') ?></a>
		</div><!-- /theme group Error -->
		<div class="theme-group-content corner-bottom clearfix">
            <div class="clearfix">
				 <table class="form-table">
                      <tr>
                          <td width="200px"><label><?php _e('Email Form Responses to', 'formidable') ?>:</label> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help_text" title="<?php _e('To send to multiple addresses, separate each address with a comma', 'formidable') ?>" /></td>
                          <td><input type="text" name="options[email_to]" size="55" value="<?php echo $values['email_to']; ?>" style="width:98%" /></td>
                     </tr>
                     <?php do_action('frm_additional_form_notification_options', $values); ?> 
                  </table>
			</div>
		</div><!-- /theme group content -->
	</div><!-- /theme group -->
</div>

</div>
<div id="frm_editable_html" style="display:none;">
    <div class="alignleft" style="width:500px">
        <p><label class="frm_pos_top"><?php _e('Before Fields', 'formidable') ?></label>
        <textarea name="options[before_html]" rows="4" style="width:100%"><?php echo $values['before_html']?></textarea></p>

        <div id="add_html_fields">
            <?php 
            if (isset($values['fields'])){
                foreach($values['fields'] as $field){
                    if (apply_filters('frm_show_custom_html', true, $field['type'])){ ?>
                        <p><label class="frm_pos_top"><?php echo $field['name'] ?></label>
                        <textarea name="field_options[custom_html_<?php echo $field['id'] ?>]" rows="7" style="width:100%"><?php echo $field['custom_html'] ?></textarea></p>
                    <?php }
                }
            } ?>
        </div>

        <p><label class="frm_pos_top"><?php _e('After Fields', 'formidable') ?></label>
        <textarea name="options[after_html]" rows="3" style="width:100%"><?php echo $values['after_html']?></textarea></p> 
    </div>
    
    <div class="alignright" style="width:150px;">
        <h4><?php _e('Key', 'formidable') ?></h4>
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
                <?php _e('Show a single radio or checkbox option by replacing "1" with the order of the option','formidable') ?>: <pre>[input opt=1]</pre><br/>
                <?php _e('Hide the option labels','formidable') ?>: <pre>[input label=0]</pre>
            </li>
            <li><b><?php _e('Add class name if field is required', 'formidable') ?>:</b> <pre>[required_class]</pre></li>
            <li><b><?php _e('Add class name if field has an error on form submit', 'formidable') ?>:</b> <pre>[error_class]</pre></li>
        </ul>
    </div> 
<?php } ?>
</div>