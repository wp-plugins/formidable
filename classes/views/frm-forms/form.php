
<div id="form_desc" class="edit_form_item frm_field_box frm_head_box">
    <h2 class="frm_ipe_form_name" id="frmform_<?php echo $id; ?>"><?php echo $values['name']; ?></h2>
    <div class="frm_ipe_form_desc"><?php echo wpautop($values['description']); ?></div>
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
<div class="postbox">
    <h3 class="trigger">Advanced Form Options</h3> 
    <div class="toggle_container inside">  
        <span class="alignright"><a title="<?php _e("Edit HTML" , FRM_PLUGIN_NAME) ?>" href="#TB_inline?height=500&width=700&inlineId=frm_editable_html" class="thickbox button"><?php _e("Edit HTML" , FRM_PLUGIN_NAME)  ?></a></span>
          
        <p style="clear:both;"><label>Form ShortCodes:</label> 
            [formidable id=<?php echo $id; ?> title=true description=true]  [formidable key=<?php echo $values['form_key']; ?>]
        </p>
        
        <p><label>Form Key</label>
            <input type="text" name="form_key" value="<?php echo $values['form_key']; ?>" />
        </p>
        
        <p><label>Styling</label><input type="checkbox" name="options[custom_style]" <?php echo ($values['custom_style']) ? (' checked="checked"') : (''); ?> />
            Use Formidable styling for this form
        </p> 
        
        <p><label>Email Form Responses to</label>
            <input type="text" name="options[email_to]" value="<?php echo $values['email_to']; ?>" />
        </p> 
        
        <p><label>New Entry Submit Button Label</label>
            <input type="text" name="options[submit_value]" value="<?php echo $values['submit_value']; ?>" />
        </p>
        
        <p><label>New Entry Success Message</label>
            <input type="text" name="options[success_msg]" size="55" value="<?php echo $values['success_msg']; ?>" />
        </p>
        
        <?php do_action('frm_additional_form_options', $values); ?> 
        
        <?php if (function_exists( 'akismet_http_post' )){ ?>
        <p><input type="checkbox" name="options[akismet]" id="akismet" value="1" <?php checked($values['akismet'], 1); ?> /> Use Akismet to check entries for spam</p>
        <?php } ?>
    </div>
</div>
<div id="frm_editable_html" style="display:none;">
    <div class="alignleft" style="width:500px">
        <p><label class="frm_pos_top">Before Fields</label>
        <textarea name="options[before_html]" rows="4" style="width:100%"><?php echo $values['before_html']?></textarea></p>

        <div id="add_html_fields">
            <?php 
            if (isset($values['fields'])){
                foreach($values['fields'] as $field){
                    if (apply_filters('frm_show_normal_field_type', true, $field['type'])){ ?>
                        <p><label class="frm_pos_top"><?php echo $field['name'] ?></label>
                        <textarea name="field_options[custom_html_<?php echo $field['id'] ?>]" rows="7" style="width:100%"><?php echo $field['custom_html'] ?></textarea></p>
                    <?php }
                }
            } ?>
        </div>

        <p><label class="frm_pos_top">After Fields</label>
        <textarea name="options[after_html]" rows="3" style="width:100%"><?php echo $values['after_html']?></textarea></p> 
    </div>
    
    <div class="alignright" style="width:150px;">
        <h4>Key</h4>
        <ul>
            <li><b>Form Name:</b> [form_name]</li>
            <li><b>Form Description:</b> [form_description]</li>
        </ul>
        <ul>
            <li><b>Field Id:</b> [id]</li>
            <li><b>Field Key:</b> [key]</li>
            <li><b>Field Name:</b> [field_name]</li>
            <li><b>Field Description:</b> [description]</li>
            <li><b>Label Position:</b> [label_position]</li>
            <li><b>Required label:</b> [required_label]</li>
            <li><b>Input Field:</b> [input]</li>
            <li><b>Add class name if field is required:</b> [required_class]</li>
            <li><b>Add class name if field has an error on form submit:</b> [error_class]</li>
        </ul>
    </div>    
</div>    
<?php } ?>
