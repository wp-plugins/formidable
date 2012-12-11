<div id="frm_adv_info" class="postbox">
<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span><?php _e('Content Customization', 'formidable') ?></span></h3>
<div class="inside">
<div id="taxonomy-linkcategory" class="categorydiv">
    <ul id="category-tabs" class="category-tabs frm-category-tabs">
		<li class="tabs" ><a href="#frm-insert-fields" id="frm_insert_fields_tab" ><?php _e( 'Insert Fields', 'formidable' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#frm-html-tags" id="frm_html_tags_tab" ><?php _e( 'HTML Tags', 'formidable' ); ?></a></li>
	</ul>
	
	<div id="frm-insert-fields" class="tabs-panel" style="max-height:none;padding-right:0;">
        <?php 
        if($frmpro_is_installed){
            $settings_tab = true;
            include(FRM_VIEWS_PATH .'/shared/mb_insert_fields.php');
            unset($settings_tab); 
        }else{
            echo '<br/><br/>';
            FrmAppController::update_message('insert field values into your messages');
        } ?>
	</div>
	
	<div id="frm-html-tags" class="tabs-panel" style="display:none;max-height:none;padding-right:0;">
	    <ul class="frm_code_list">
        <?php
        $col = 'one';
        $entry_shortcodes = array(
            'id' => __('Field ID', 'formidable'), 
            'key' => __('Field Key', 'formidable'),
            'field_name' => __('Field Name', 'formidable'),
            'description' => __('Field Description', 'formidable'),
            'label_position' => __('Label Position', 'formidable'),
            'required_label' => __('Required Label', 'formidable'),
            'input' => __('Input Field', 'formidable'),
            'input opt=1' => array('label' => __('Single Option', 'formidable'), 'title' => __('Show a single radio or checkbox option by replacing "1" with the order of the option', 'formidable')),
            'input label=0' => __('Hide Option Label', 'formidable'),
            'required_class' => array('label' => __('Required Class', 'formidable'), 'title' => __('Add class name if field is required', 'formidable')),
            'error_class' => array('label' => __('Error Class', 'formidable'), 'title' => __('Add class name if field has an error on form submit', 'formidable'))
        );

        foreach($entry_shortcodes as $skey => $sname){
        ?>
    	<li class="frm_col_<?php echo $col ?>">
    	    <a class="show_field_custom_html frmbutton button <?php echo is_array($sname) ? 'frm_help' : ''; ?>" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $skey ?>');return false;" href="#" <?php echo is_array($sname) ? 'title="'. $sname['title'] .'"' : ''; ?>><?php echo is_array($sname) ? $sname['label'] : $sname; ?></a>
    	</li>
        <?php
            $col = ($col == 'one') ? 'two' : 'one';
            unset($skey);
            unset($sname);
        }
        ?>
        </ul>
        
        <ul class="frm_code_list clear">
            <?php 
            $col = 'one';
            foreach(array(
                'form_name' => __('Form Name', 'formidable'), 'form_description' => __('Form Description', 'formidable'), 
                'form_key' => __('Form Key', 'formidable'), 'deletelink' => __('Delete Entry Link', 'formidable')) as $skey => $sname){ ?>
            <li class="frm_col_<?php echo $col ?>">
        	    <a class="show_before_html show_after_html frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $skey ?>');return false;" href="#"><?php echo $sname; ?></a>
        	</li>
            <?php
                $col = ($col == 'one') ? 'two' : 'one'; 
            } ?>
        </ul>
	</div>
</div>
</div>
</div>