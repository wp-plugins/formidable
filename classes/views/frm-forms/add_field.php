<?php $display = apply_filters('frm_display_field_options', array('type' => $field['type'], 'field_data' => $field, 'required' => true, 'description' => true, 'options' => true, 'label_position' => true, 'invalid' => false, 'size' => false, 'clear_on_focus' => false, 'default_blank' => true)); ?>

<li id="frm_field_id_<?php echo $field['id']; ?>" class="form-field edit_form_item frm_field_box ui-state-default frm_hide_options<?php echo $display['options'] ?> edit_field_type_<?php echo $display['type'] ?>" onmouseover="frm_field_hover(1,<?php echo $field['id']; ?>)" onmouseout="frm_field_hover(0,<?php echo $field['id']; ?>)">
    <a href="javascript:void(0);" class="alignright frm-show-hover frm-move" title="Move Field"><img src="<?php echo FRM_IMAGES_URL ?>/move.png" alt="Move"></a>
    <a href="javascript:frm_delete_field(<?php echo $field['id']; ?>)" class="alignright frm-show-hover" id="frm_delete_field<?php echo $field['id']; ?>" title="Delete Field"><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="Delete"></a>
    <a href="javascript:frm_duplicate_field(<?php echo $field['id']; ?>,'<?php echo $frm_ajax_url ?>')" class="alignright frm-show-hover" title="<?php _e('Duplicate Field', 'formidable') ?>"><img src="<?php echo FRM_IMAGES_URL ?>/duplicate.png" alt="<?php _e('Duplicate', 'formidable') ?>"></a>
    <?php do_action('frm_extra_field_actions', $field['id']); ?>
    
    <?php if ($display['required']){ ?>
    <span id="require_field_<?php echo $field['id']; ?>">
        <a href="javascript:frm_mark_required(<?php echo $field['id']; ?>,<?php echo $field_required = ($field['required'] == '0')?('0'):('1'); ?>,'<?php echo FRM_IMAGES_URL ?>','<?php echo $frm_ajax_url?>')" class="alignleft frm_required<?php echo $field_required ?>" id="req_field_<?php echo $field['id']; ?>" title="Click to Mark as <?php echo ($field['required'] == '0')?'':'not '; ?>Required"><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required"></a>
    </span>
    <?php } ?>
    <label class="frm_ipe_field_label frm_pos_<?php echo $field['label']; ?>" id="field_<?php echo $field['id']; ?>"><?php echo $field['name'] ?></label>
    
<?php if ($display['type'] == 'text'){ ?>
    <input type="text" name="<?php echo $field_name ?>" value="<?php echo $field['default_value']; ?>" <?php echo (isset($field['size']) && $field['size']) ? 'style="width:auto" size="'.$field['size'] .'"' : ''; ?> /> 
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>"<?php if ($field['size']) echo ' style="width:auto" cols="'.$field['size'].'"' ?> rows="<?php echo $field['max']; ?>"><?php echo $field['default_value']; ?></textarea> 
    
<?php 

}else if ($field['type'] == 'radio' or $field['type'] == 'checkbox'){
    $field['default_value'] = maybe_unserialize($field['default_value']); 
    if(isset($field['post_field']) and $field['post_field'] == 'post_category')
        do_action('frm_after_checkbox', array('field' => $field, 'field_name' => $field_name, 'type' => $field['type']));
    else
        require(FRM_VIEWS_PATH.'/frm-fields/radio.php');   
        
?>
    <div id="frm_add_field_<?php echo $field['id']; ?>" class="frm-show-click">
        <a href="javascript:frm_add_field_option(<?php echo $field['id']; ?>,'<?php echo $frm_ajax_url ?>')" class="frm_orange frm_add_opt">+ <?php _e('Add an Option', 'formidable') ?></a>
    </div>
<?php

}else if ($field['type'] == 'select'){ 
    if(isset($field['post_field']) and $field['post_field'] == 'post_category'){
        echo FrmFieldsHelper::dropdown_categories(array('name' => $field_name, 'field' => $field) );
    }else{ ?>
    <select name="<?php echo $field_name ?>" id="<?php echo $field_name ?>" <?php echo (isset($field['size']) && $field['size']) ? 'style="width:auto"' : ''; ?>>
        <?php foreach ($field['options'] as $opt_key => $opt){ 
            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            $selected = ($field['default_value'] == $field_val)?(' selected="selected"'):(''); ?>
            <option value="<?php echo $field_val ?>"<?php echo $selected ?>><?php echo $opt ?></option>
        <?php } ?>
    </select>
<?php } 
    if ($display['default_blank']) FrmFieldsHelper::show_default_blank_js($field['id'], $field['default_blank']); ?>
    <br/>
    <div class="frm-show-click">
    <?php if(isset($field['post_field']) and $field['post_field'] == 'post_category'){ ?>
        <p class="howto"><?php _e('Please add options from the WordPress "Categories" page', 'formidable') ?></p>
    <?php }else if(!isset($field['post_field']) or $field['post_field'] != 'post_status'){
        foreach ($field['options'] as $opt_key => $opt) require(FRM_VIEWS_PATH.'/frm-fields/single-option.php'); ?>
        <div id="frm_add_field_<?php echo $field['id']; ?>">
            <a href="javascript:frm_add_field_option(<?php echo $field['id']; ?>,'<?php echo $frm_ajax_url ?>')" class="frm_orange frm_add_opt">+ <?php _e('Add an Option', 'formidable') ?></a>
            <?php do_action('frm_add_multiple_opts', $field); ?>
        </div>
<?php } ?>
    </div>
<?php
}else if ($field['type'] == 'captcha'){ ?>
    <img src="<?php echo FRM_URL ?>/images/<?php echo $frm_settings->re_theme ?>-captcha.png" alt="captcha" />
    <span class="howto"><?php printf(__('Hint: Change colors in the %1$sFormidable settings', 'formidable'), '<a href="?page=formidable-settings">') ?></a></span>
    <input type="hidden" name="<?php echo $field_name ?>" value="1"/>
<?php 
}else
    do_action('frm_display_added_fields',$field);

if ($display['clear_on_focus']){
    FrmFieldsHelper::show_onfocus_js($field['id'], $field['clear_on_focus']);

    if ($display['default_blank'])
        FrmFieldsHelper::show_default_blank_js($field['id'], $field['default_blank']);
    
    do_action('frm_extra_field_display_options', $field);
}

if ($display['description']){ ?> 
    <div class="frm_ipe_field_desc description frm-show-click" id="field_<?php echo $field['id']; ?>"><?php echo $field['description']; ?></div> 
<?php
}

if ($display['options']){ ?>
    <div class="clearfix themeRoller">
        <div class="theme-group clearfix">
    	    <div class="theme-group-header state-default">
    		    <span class="icon icon-triangle-1-e"><?php _e('Collapse', 'formidable') ?></span>
    		    <a href="#"><?php _e('Field Options', 'formidable') ?> (ID <?php echo $field['id'] ?>)</a>
    		</div><!-- /theme group Error -->
    		<div class="theme-group-content corner-bottom clearfix">
                <div class="clearfix">
                    <table class="form-table">
                    <?php if ($display['size']){ ?>
                    <tr><td width="150px"><label><?php _e('Field Size', 'formidable') ?>:</label></td>
                        <td><input type="text" name="field_options[size_<?php echo $field['id'] ?>]" value="<?php echo $field['size']; ?>" size="5" /> <span class="howto"><?php echo ($field['type'] == 'textarea' || $field['type'] == 'rte')? __('columns wide', 'formidable') : __('characters wide', 'formidable') ?></span>

                        <input type="text" name="field_options[max_<?php echo $field['id'] ?>]" value="<?php echo $field['max']; ?>" size="5" /> <span class="howto"><?php echo ($field['type'] == 'textarea' || $field['type'] == 'rte')? __('rows high', 'formidable') : __('characters maximum', 'formidable') ?></span></td>
                    </tr>
                    <?php } ?>
                    <?php if ($display['label_position']){ ?>
                    <tr><td width="150px"><label><?php _e('Label Position', 'formidable') ?>:</label></td>
                        <td><select name="field_options[label_<?php echo $field['id'] ?>]">
                            <option value="top"<?php selected($field['label'], 'top'); ?>><?php _e('Top', 'formidable') ?></option>
                            <option value="left"<?php selected($field['label'], 'left'); ?>><?php _e('Left', 'formidable') ?></option>
                            <option value="right"<?php selected($field['label'], 'right'); ?>><?php _e('Right', 'formidable') ?></option>
                            <option value="none"<?php selected($field['label'], 'none'); ?>><?php _e('None', 'formidable') ?></option>
                            <option value="hidden"<?php selected($field['label'], 'hidden'); ?>><?php _e('Hidden', 'formidable') ?></option>
                        </select>
                        </td>  
                    </tr>
                    <?php } ?>
                    <?php if ($display['required']){ ?>
                    <tr>
                        <td><label><?php _e('Required Field', 'formidable') ?>:</label></td>
                        <td><input type="checkbox" id="frm_req_field_<?php echo $field['id'] ?>" name="field_options[required_<?php echo $field['id'] ?>]" value="1" <?php echo ($field['required']) ? 'checked="checked"': ''; ?> onclick="frm_mark_required(<?php echo $field['id'] ?>,<?php echo $field_required ?>,'<?php echo FRM_IMAGES_URL ?>','<?php echo $frm_ajax_url?>')" /> <span><?php _e('Required', 'formidable') ?></span>
                        <span class="frm_required_details<?php echo $field['id'] ?>" <?php if(!$field['required']) echo 'style="display:none;"'?>>&mdash; <span class="howto"><?php _e('Indicate required field with', 'formidable') ?>:</span>
                            <input type="text" name="field_options[required_indicator_<?php echo $field['id'] ?>]" value="<?php echo htmlentities($field['required_indicator']); ?>" />
                        </span>
                        </td>
                    </tr>
                    <tr class="frm_required_details<?php echo $field['id'] ?>"<?php if(!$field['required']) echo 'style="display:none;"'?>><td><label><?php _e('Error message if required field is left blank', 'formidable') ?>:</label></td>  
                        <td><input type="text" name="field_options[blank_<?php echo $field['id'] ?>]" value="<?php echo $field['blank']; ?>" class="frm_long_input" /></td>
                    </tr>
                    <?php } ?>
                    <?php if ($display['invalid']){ ?>
                    <tr><td><label><?php _e('Error message if submission is an invalid format', 'formidable') ?>:</label></td>  
                        <td><input type="text" name="field_options[invalid_<?php echo $field['id'] ?>]" value="<?php echo $field['invalid']; ?>" class="frm_long_input" /></td>
                    </tr>
                    <?php } ?>
                    <?php do_action('frm_field_options_form', $field, $display); ?>
                    </table>
                </div>
            </div>
        </div>
    </div>    
<?php } ?>         
</li>
