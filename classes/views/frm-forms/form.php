<div id="frm_form_editor_container">
<div id="titlediv">
<div id="form_desc" class="edit_form_item frm_field_box frm_head_box">
    <h2 class="frm_ipe_form_name" id="frmform_<?php echo $id; ?>"><?php echo $values['name']; ?></h2>
    <div class="frm_ipe_form_desc"><?php echo $values['description']; ?></div>
</div>
</div>

<ul id="new_fields">
<?php
if (isset($values['fields']) && !empty($values['fields'])){
    $count = 0;
    foreach($values['fields'] as $field){
        $count++;
        $field_name = "item_meta[". $field['id'] ."]";
        require(FRM_VIEWS_PATH .'/frm-forms/add_field.php');
        unset($field);
        unset($field_name);
    }
    unset($count);
} ?>
</ul>

</div>