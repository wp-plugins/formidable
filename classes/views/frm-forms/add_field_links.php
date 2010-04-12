<div id="frm_form_options" class="alignright">
    <?php if (!$values['is_template']){ ?>
    <p class="howto"><?php _e('Copy this code and paste it into your post, page or text widget', FRM_PLUGIN_NAME) ?>:
    <input type='text' style="text-align:center; font-weight:bold; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='[formidable id=<?php echo $id; ?>]' /></p>
    <?php } ?>
    
    <p class="frm_orange"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="_blank"><?php _e('Preview Form', FRM_PLUGIN_NAME) ?></a>
    <?php global $frm_settings; if ($frm_settings->preview_page_id > 0){ ?>
        or <a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="_blank"><?php _e('Preview in Current Theme', FRM_PLUGIN_NAME) ?></a>
    <?php } ?>
    </p>
    
    <p class="howto"><?php _e('Click on or drag a field into your form', FRM_PLUGIN_NAME) ?></p>
    <div class="themeRoller clearfix">
    	<div id="rollerTabs">

    	<fieldset class="clearfix">
    	    <div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e"><?php _e('Collapse', FRM_PLUGIN_NAME) ?></span>
    				<a href="#"><?php _e('Basic Fields', FRM_PLUGIN_NAME) ?></a>
    			</div><!-- /theme group Error -->
    			<div class="theme-group-content corner-bottom clearfix">
                    <div class="clearfix">
    					<ul class="field_type_list">
                        <?php foreach ($frm_field_selection as $field_key => $field_type){ ?>
                            <li class="frmbutton button" id="<?php echo $field_key ?>"><a href="javascript:add_frm_field_link(<?php echo $id ?>, '<?php echo $field_key ?>', '<?php echo $frm_ajax_url ?>');"><?php echo $field_type ?></a></li>
                         <?php } ?>
                         <div class="clear"></div>
                         <?php if (!array_key_exists('captcha', $frm_field_selection) && !function_exists( 'akismet_http_post' )){
                                global $frm_siteurl; ?>
                                <p class="howto"><?php printf(__('Hint: Download and activate %1$sWP-reCAPTCHA%2$s to add a captcha to your form. Alternatively activate Akismet for captcha-free spam screening.', FRM_PLUGIN_NAME), '<a href="'.$frm_siteurl.'/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=wp-recaptcha&amp;TB_iframe=true&amp;width=640&amp;height=593" class="thickbox onclick" title="WP-reCAPTCHA">', '</a>'); ?></p>
                        <?php } ?>
                         </ul>
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->
    		
    		<div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e"><?php _e('Collapse', FRM_PLUGIN_NAME) ?></span>
    				<a href="#"><?php _e('Pro Fields', FRM_PLUGIN_NAME) ?></a>
    			</div><!-- /theme group Error -->
    			<div class="theme-group-content corner-bottom clearfix">
                    <div class="clearfix">
    					 <ul<?php echo apply_filters('frm_drag_field_class','') ?>>
                         <?php foreach (FrmFieldsHelper::pro_field_selection() as $field_key => $field_type){ ?>
                             <li class="frmbutton button" id="<?php echo $field_key ?>"><?php echo apply_filters('frmpro_field_links',$field_type, $id, $field_key) ?></li>
                        <?php } ?>
                         <div class="clear"></div>
                         </ul>
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->
    		
    		
    		<div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e"><?php _e('Collapse', FRM_PLUGIN_NAME) ?></span>
    				<a href="#"><?php _e('Key', FRM_PLUGIN_NAME) ?></a>
    			</div><!-- /theme group Content -->
    			<div class="theme-group-content corner-bottom clearfix">

    				<div class="clearfix">
                        <ul class="frm_key_icons">
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required"></span> 
                                = <?php _e('required field', FRM_PLUGIN_NAME) ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required"></span> 
                                = <?php _e('not required', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> 
                                = <?php _e('clear default text on click', FRM_PLUGIN_NAME) ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> 
                                = <?php _e('do not clear default text on click', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> 
                                = <?php _e('default value will NOT pass validation', FRM_PLUGIN_NAME) ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> 
                                = <?php _e('default value will pass validation', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/readonly.png" alt="read-only"></span> 
                                = <?php _e('read-only field', FRM_PLUGIN_NAME) ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/readonly.png" alt="read-only"></span> 
                                = <?php _e('not a read-only field', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="Delete"></span> 
                                = <?php _e('delete field and all inputed data', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/duplicate.png" alt="Move"></span> 
                                = <?php _e('duplicate field', FRM_PLUGIN_NAME) ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/move.png" alt="Move"></span> = 
                                <?php _e('move field', FRM_PLUGIN_NAME) ?></li>
                        </ul>
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->		
            <?php do_action('frm_extra_form_instructions'); ?>
    	</fieldset>

        </div>
        <p class="howto"><?php _e('Enter or select default values into fields on this form.', FRM_PLUGIN_NAME) ?></p>
    </div><!-- /themeroller -->

     
     
</div>