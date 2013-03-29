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