<div id="frm_form_options" class="inner-sidebar">
    <?php if(!isset($hide_preview) or !$hide_preview){ 
        if (!$values['is_template']){ ?>
    <p class="howto"><?php _e('Insert into a post, page or text widget', 'formidable') ?>:
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus='this.select();' value='[formidable id=<?php echo $id; ?>]' /></p>
    <?php } ?>
    
    <?php if(isset($show_preview)){ ?>
    <p class="frm_orange"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="_blank"><?php _e('Preview Form', 'formidable') ?></a>
    <?php global $frm_settings; 
        if ($frm_settings->preview_page_id > 0){ ?>
        or <a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="_blank"><?php _e('Preview in Current Theme', 'formidable') ?></a>
    <?php } ?>
    </p>
    <?php } 
    } ?>
    
    <p class="howto"><?php _e('Click on or drag a field into your form', 'formidable') ?></p>
    <div class="themeRoller clearfix">
    	<div id="rollerTabs">

    	<fieldset class="clearfix">
    	    <div class="theme-group clearfix">
    			<div class="theme-group-header state-default state-active ui-state-active corner-top">
    				<span class="icon icon-triangle-1-e icon-triangle-1-s"><?php _e('Collapse', 'formidable') ?></span>
    				<a href="#"><?php _e('Basic Fields', 'formidable') ?></a>
    			</div><!-- /theme group Error -->
    			<div class="theme-group-content corner-bottom clearfix" style="display: block;">
                    <div class="clearfix">
    					<ul class="field_type_list">
                        <?php 
                        $col_class = 'frm_col_one';
                        foreach ($frm_field_selection as $field_key => $field_type){ ?>
                            <li class="frmbutton button <?php echo $col_class ?>" id="<?php echo $field_key ?>"><a href="javascript:add_frm_field_link(<?php echo $id ?>, '<?php echo $field_key ?>', '<?php echo $frm_ajax_url ?>');"><?php echo $field_type ?></a></li>
                         <?php
                         $col_class = (empty($col_class)) ? 'frm_col_one' : '';
                         } ?>
                         </ul>
                         <div class="clear"></div>
    				</div>

    	            <h4 class="title"><?php _e('Pro Fields', 'formidable') ?></h4>
                    <div class="clearfix">
    					 <ul<?php echo apply_filters('frm_drag_field_class', '') ?>>
                         <?php $col_class = 'frm_col_one';
                         foreach (FrmFieldsHelper::pro_field_selection() as $field_key => $field_type){ ?>
                             <li class="frmbutton button <?php echo $col_class ?>" id="<?php echo $field_key ?>"><?php echo apply_filters('frmpro_field_links',$field_type, $id, $field_key) ?></li>
                        <?php 
                        $col_class = (empty($col_class)) ? 'frm_col_one' : '';
                        } ?>
                         </ul>
                         <div class="clear"></div>
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->
    		
    		<?php do_action('frm_extra_form_instructions'); ?>
    		<div class="theme-group clearfix">
    			<div class="theme-group-header state-default">
    				<span class="icon icon-triangle-1-e"><?php _e('Collapse', 'formidable') ?></span>
    				<a href="#"><?php _e('Key', 'formidable') ?></a>
    			</div><!-- /theme group Content -->
    			<div class="theme-group-content corner-bottom clearfix">

    				<div class="clearfix">
                        <ul class="frm_key_icons">
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required" style="width:10px" /></span> 
                                = <?php _e('required field', 'formidable') ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required" style="width:10px" /></span> 
                                = <?php _e('not required', 'formidable') ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/reload.png" alt="reload" /></span> 
                                = <?php _e('clear default text on click', 'formidable') ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/reload.png" alt="reload" /></span> 
                                = <?php _e('do not clear default text on click', 'formidable') ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL?>/error.png" alt="error" /></span> 
                                = <?php _e('default value will NOT pass validation', 'formidable') ?></li>
                            <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/error.png" alt="error" /></span> 
                                = <?php _e('default value will pass validation', 'formidable') ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="Delete" /></span> 
                                = <?php _e('delete field and all inputed data', 'formidable') ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/duplicate.png" alt="Move" /></span> 
                                = <?php _e('duplicate field', 'formidable') ?></li>
                            <li><span><img src="<?php echo FRM_IMAGES_URL ?>/move.png" alt="Move" /></span> = 
                                <?php _e('move field', 'formidable') ?></li>
                        </ul>
    				</div>
    			</div><!-- /theme group content -->
    		</div><!-- /theme group -->		
            
    	</fieldset>

        </div>
        <p class="howto"><?php _e('Enter or select default values into fields on this form.', 'formidable') ?></p>
    </div><!-- /themeroller -->

     
     
</div>