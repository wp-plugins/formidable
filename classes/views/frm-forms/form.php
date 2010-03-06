<div id="frm_form_editor_container">
<div id="form_desc" class="edit_form_item frm_field_box frm_head_box">
    <h2 class="frm_ipe_form_name" id="frmform_<?php echo $id; ?>"><?php echo $values['name']; ?></h2>
    <div class="frm_ipe_form_desc"><?php echo $values['description']; ?></div>
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
    <h3 class="ui-accordion-header ui-state-default">
        <span class="ui-icon ui-icon-triangle-1-e"></span>
        <a href="#"><?php _e('Advanced Form Options', 'formidable') ?></a>
    </h3> 
    <div class="ui-widget-content ui-corner-bottom">
        <span class="alignright"><a title="<?php _e('Edit HTML', 'formidable') ?>" href="#TB_inline?height=500&width=700&inlineId=frm_editable_html" class="thickbox button"><?php _e('Edit HTML', 'formidable') ?></a></span>
          
        <p style="clear:both;"><label><?php _e('Form ShortCodes') ?>:</label> 
            [formidable id=<?php echo $id; ?> title=true description=true]  [formidable key=<?php echo $values['form_key']; ?>]
        </p>
        
        <p><label><?php _e('Form Key') ?></label>
            <input type="text" name="form_key" value="<?php echo $values['form_key']; ?>" />
        </p>
        
        <p><label><?php _e('Styling', 'formidable') ?></label><input type="checkbox" name="options[custom_style]" <?php echo ($values['custom_style']) ? (' checked="checked"') : (''); ?> />
            <?php _e('Use Formidable styling for this form', 'formidable') ?>
        </p> 
        
        <p><label><?php _e('Email Form Responses to', 'formidable') ?></label>
            <input type="text" name="options[email_to]" size="55" value="<?php echo $values['email_to']; ?>" />
        </p> 
        
        <p><label><?php _e('New Entry Submit Button Label', 'formidable') ?></label>
            <input type="text" name="options[submit_value]" value="<?php echo $values['submit_value']; ?>" />
        </p>
        
        <p><label><?php _e('New Entry Success Message', 'formidable') ?></label>
            <input type="text" name="options[success_msg]" size="55" value="<?php echo $values['success_msg']; ?>" />
        </p>
        
        <?php do_action('frm_additional_form_options', $values); ?> 
        
        <?php if (function_exists( 'akismet_http_post' )){ ?>
        <p><input type="checkbox" name="options[akismet]" id="akismet" value="1" <?php checked($values['akismet'], 1); ?> /> <?php _e('Use Akismet to check entries for spam', 'formidable') ?></p>
        <?php } ?>
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
            <li><b><?php _e('Form Name', 'formidable') ?>:</b> [form_name]</li>
            <li><b><?php _e('Form Description', 'formidable') ?>:</b> [form_description]</li>
        </ul>
        <ul>
            <li><b><?php _e('Field Id', 'formidable') ?>:</b> [id]</li>
            <li><b><?php _e('Field Key', 'formidable') ?>:</b> [key]</li>
            <li><b><?php _e('Field Name', 'formidable') ?>:</b> [field_name]</li>
            <li><b><?php _e('Field Description', 'formidable') ?>:</b> [description]</li>
            <li><b><?php _e('Label Position', 'formidable') ?>:</b> [label_position]</li>
            <li><b><?php _e('Required label', 'formidable') ?>:</b> [required_label]</li>
            <li><b><?php _e('Input Field', 'formidable') ?>:</b> [input]</li>
            <li><b><?php _e('Add class name if field is required', 'formidable') ?>:</b> [required_class]</li>
            <li><b><?php _e('Add class name if field has an error on form submit', 'formidable') ?>:</b> [error_class]</li>
        </ul>
    </div>    
    
<?php } ?>
</div>