<div id="postbox-container-1" class="<?php echo FrmAppController::get_postbox_class(); ?>">
    <?php if(!isset($hide_preview) or !$hide_preview){ 
        if (!$values['is_template']){ ?>
    <p class="howto" style="margin-top:0;"><?php _e('Insert into a post, page or text widget', 'formidable') ?>:
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus='this.select();' value='[formidable id=<?php echo $id; ?>]' /></p>
    <?php } ?>
    
    <?php if(isset($show_preview)){ ?>
    <p class="frm_orange"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="_blank"><?php _e('Preview Form', 'formidable') ?></a>
    <?php global $frm_settings; 
        if ($frm_settings->preview_page_id > 0){ ?>
        <?php _e('or', 'formidable') ?> 
        <a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="_blank"><?php _e('Preview in Current Theme', 'formidable') ?></a>
    <?php } ?>
    </p>
    <?php } 
    } ?>
    <div id="frm_position_ele"></div>
    
    <div class="postbox frm_field_list">
    <div class="inside">
    <div id="taxonomy-linkcategory" class="categorydiv">
        <ul id="category-tabs" class="category-tabs frm-category-tabs">
    		<li class="tabs" ><a href="#frm-insert-fields"><?php _e( 'Fields', 'formidable' ); ?></a></li>
    		<li class="hide-if-no-js"><a href="#frm-keys-and-actions"><?php _e( 'Key', 'formidable' ); ?></a></li>
    		<?php do_action('frm_extra_form_instruction_tabs'); ?>
    	</ul>

    	<div id="frm-insert-fields" class="tabs-panel" style="max-height:none;overflow:visible;">
    	    <p class="howto"><?php _e('Click on or drag a field into your form', 'formidable') ?></p>
			<ul class="field_type_list">
            <?php 
            $col_class = 'frm_col_one';
            foreach ($frm_field_selection as $field_key => $field_type){ ?>
                <li class="frmbutton button <?php echo $col_class ?> frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>"><a href="javascript:add_frm_field_link(<?php echo $id ?>,'<?php echo $field_key ?>');"><?php echo $field_type ?></a></li>
             <?php
             $col_class = (empty($col_class)) ? 'frm_col_one' : '';
             } ?>
             </ul>
             <div class="clear"></div>

        <h4 class="title" style="margin-bottom:0;margin-top:4px;"><?php _e('Pro Fields', 'formidable') ?></h4>
			 <ul<?php echo apply_filters('frm_drag_field_class', '') ?> style="margin-top:2px;">
             <?php $col_class = 'frm_col_one';
             foreach (FrmFieldsHelper::pro_field_selection() as $field_key => $field_type){ ?>
                 <li class="frmbutton button <?php echo $col_class ?> frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>"><?php echo apply_filters('frmpro_field_links',$field_type, $id, $field_key) ?></li>
            <?php 
            $col_class = (empty($col_class)) ? 'frm_col_one' : '';
            } ?>
             </ul>
             <div class="clear"></div>
        </div>
    	
    	<div id="frm-keys-and-actions" class="tabs-panel" style="display:none;max-height:none;">
		        
            <ul class="frm_key_icons">
                <li><span class="frm_action_icon frm_required_icon"></span> 
                    = <?php _e('required field', 'formidable') ?></li>
                <li><span class="frm_inactive_icon frm_action_icon frm_required_icon"></span> 
                    = <?php _e('not required', 'formidable') ?></li>
                <li><span class="frm_action_icon frm_reload_icon"></span> 
                    = <?php _e('clear default text on click', 'formidable') ?></li>
                <li><span class="frm_inactive_icon frm_action_icon frm_reload_icon"></span> 
                    = <?php _e('do not clear default text on click', 'formidable') ?></li>
                <li><span class="frm_action_icon frm_error_icon"></span> 
                    = <?php _e('default value will NOT pass validation', 'formidable') ?></li>
                <li><span class="frm_inactive_icon frm_action_icon frm_error_icon"></span> 
                    = <?php _e('default value will pass validation', 'formidable') ?></li>
                <li><span><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="<?php echo esc_attr(__('Delete', 'formidable')) ?>" /></span> 
                    = <?php _e('delete field and all inputed data', 'formidable') ?></li>
                <li><span><img src="<?php echo FRM_IMAGES_URL ?>/duplicate.png" alt="<?php echo esc_attr(__('Move', 'formidable')) ?>" /></span> 
                    = <?php _e('duplicate field', 'formidable') ?></li>
                <li><span><img src="<?php echo FRM_IMAGES_URL ?>/move.png" alt="<?php echo esc_attr(__('Move', 'formidable')) ?>" /></span> 
                    = <?php _e('move field', 'formidable') ?></li>
            </ul>

    	</div>
    	
    	<?php do_action('frm_extra_form_instructions'); ?>
    </div>
    </div>
    </div>
</div>