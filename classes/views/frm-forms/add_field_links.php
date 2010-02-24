<div id="frm_form_options" class="alignright">
    <?php if (!$values['is_template']){ ?>
    <p class="howto">Copy this code and paste it into your post, page or text widget:</p>
    <input type='text' style="text-align:center; font-weight:bold; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='[formidable id=<?php echo $id; ?>]' /><br/><br/>
    <?php } ?>
    
    <ul class="frmbutton nodrag">
        <li class="ui-widget-header"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="blank" >Preview Form</a></li>
    <?php global $frm_settings; if ($frm_settings->preview_page_id > 0){ ?>
        <li class="ui-widget-header"><a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="blank" class="frmbutton">Preview Form in Current Theme</a></li>
    <?php } ?>
    </ul>
    
    <p class="howto">Click on or drag a field into your form</p>
    <div id="themeRoller" class="clearfix">
    	<div id="rollerTabs">

    	<fieldset class="clearfix">
    	    <div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e">Collapse</span>
    				<a href="#">Basic Fields</a>
    			</div><!-- /theme group Error -->
    			<div class="theme-group-content corner-bottom clearfix">
                    <div class="clearfix">
    					<ul class="field_type_list">
                        <?php foreach ($frm_field_selection as $field_key => $field_type){ ?>
                            <li class="frmbutton button" id="<?php echo $field_key ?>"><a href="javascript:add_frm_field_link(<?php echo $id ?>, '<?php echo $field_key ?>');"><?php echo $field_type ?></a></li>
                         <?php } ?>
                         <?php if (!$frm_recaptcha_enabled && !function_exists( 'akismet_http_post' )){
                                global $frm_siteurl;
                                echo '<p class="howto">Hint: Download and activate <a href="'.$frm_siteurl.'/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=wp-recaptcha&amp;TB_iframe=true&amp;width=640&amp;height=593" class="thickbox onclick" title="WP-reCAPTCHA 2.9.6">WP-reCAPTCHA</a> to add a captcha to your form. Alternatively activate Akismet for captcha-free spam screening.</p>';
                                } ?>
                         </ul>
    				</div>
    				
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->
    		
    		<div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e">Collapse</span>
    				<a href="#">Pro Fields</a>
    			</div><!-- /theme group Error -->
    			<div class="theme-group-content corner-bottom clearfix">
                    <div class="clearfix">
    					 <ul class="field_type_list">
                         <?php 
                         if($frmpro_is_installed){  
                             foreach ($frm_pro_field_selection as $field_key => $field_type){ ?>
                                 <li class="frmbutton button" id="<?php echo $field_key ?>"><a href="javascript:add_frm_field_link(<?php echo $id ?>, '<?php echo $field_key ?>');"><?php echo $field_type ?></a></li>
                        <?php }
                         }else
                             foreach ($frm_pro_field_selection as $field_key => $field_type) 
                                echo '<li class="frmbutton">'.$field_type.'</li>';
                         ?>
                         </ul>
    				</div>
    				
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->
    		
    		
    		<div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e">Collapse</span>
    				<a href="#">Key</a>
    			</div><!-- /theme group Content -->
    			<div class="theme-group-content corner-bottom clearfix">

    				<div class="clearfix">
                        <ul class="ui-state-default">
                            <li><span class="ui-icon ui-icon-star alignleft"></span> = required field</li>
                            <li><span class="frm_inactive_icon ui-icon ui-icon-star alignleft"></span> = not required</li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> = clear default text on click</li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> = do not clear default text on click</li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> = default value will NOT pass validation</li>
                             <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> = default value will pass validation</li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="Delete"></span> = delete field and all inputed data</li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/duplicate.png" alt="Move"></span> = duplicate field</li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/move.png" alt="Move"></span> = move field</li>
                        </ul>
                        
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->		
            <?php do_action('frm_extra_form_instructions'); ?>
    	</fieldset>

        </div>
    </div><!-- /themeroller -->

     <p class="howto">Enter or select default values into fields on this form.</p>
     
</div>