<div id="poststuff">
<?php if ($title == true || $title == 'true'){ ?>
<h3><?php echo $form->name ?></h3>
<? } ?>

<?php if ($description == true || $description == 'true'){ ?>
<p class="frm_description"><?php echo $form->description ?></p>
<? } ?>

<input type="hidden" name="form_id" value="<?php echo $form->id ?>">
<?php if (isset($controller) && isset($plugin){ ?>
<input type="hidden" name="controller" value="<?php echo $controller; ?>">
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>">
<?php } ?>
<div id="frm_form_fields">
<?php
global $frm_in_section;
$frm_in_section = false;

if (is_array($errors))
    $error_keys = array_keys($errors);
foreach($values['fields'] as $field){
    $field_name = "item_meta[". $field['id'] ."]";
    if (apply_filters('frm_show_normal_field_type', true, $field))
        require(FRM_VIEWS_PATH.'/frm-fields/show.php');
    else
        do_action('frm_show_other_field_type', $field);
}    
global $frm_in_section;
if($frm_in_section)
    echo "</div>\n";
?>
</div>
</div>
<?php do_action('frm_entries_footer_scripts',$values['fields']); ?>