<?php wp_nonce_field('frm_save_form_nonce', 'frm_save_form'); ?>

<div id="frm_form_editor_container">
<div id="titlediv">
    <input type="text" name="name" value="<?php echo esc_attr($values['name']); ?>" id="title" placeholder="<?php esc_attr_e('Enter title here') ?>" />
    <div id="edit-slug-box" class="hide-if-no-js">
        <div class="alignright" style="width:13em;max-width:30%">
        <strong><?php _e('Form Key:', 'formidable') ?></strong>
        <div id="editable-post-name" class="frm_ipe_form_key" title="<?php _e('Click to edit.', 'formidable') ?>"><?php echo $values['form_key']; ?></div>
        </div>
        <div class="frm_ipe_form_desc alignleft" style="width:70%"><?php echo $values['description']; ?></div>
        <div style="clear:both"></div>
    </div>
</div>

<ul id="new_fields">
<?php
if (isset($values['fields']) && !empty($values['fields'])){
    $count = 0;
    foreach($values['fields'] as $field){
        $count++;
        $field_name = "item_meta[". $field['id'] ."]";
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-forms/add_field.php');
        unset($field);
        unset($field_name);
    }
    unset($count);
} ?>
</ul>

</div>