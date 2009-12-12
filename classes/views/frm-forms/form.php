

<div id="form_desc" class="edit_form_item frm_field_box frm_head_box">
    <h2 class="frm_ipe_form_name" id="frmform_<?php echo $id; ?>"><?php echo $values['name']; ?></h2>
    <div class="frm_ipe_form_desc"><?php echo wpautop($values['description']); ?></div>
</div>

<ul id="new_fields">
<?php 
if (isset($values['fields'])){
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
        <p><label>Form ShortCodes:</label> 
            [formidable id=<?php echo $id; ?> title=true description=true]  [formidable key=<?php echo $values['form_key']; ?>]
        </p>

        <p><label>Form Key</label>
            <input type="text" name="form_key" value="<?php echo $values['form_key']; ?>" />
        </p> 
        
        <p><label>Email Form Responses to</label>
            <input type="text" name="options[email_to]" value="<?php echo $values['email_to']; ?>" />
        </p> 
        <?php do_action('frm_additional_form_options', $values); ?> 
    </div>
</div>
<?php } ?>
