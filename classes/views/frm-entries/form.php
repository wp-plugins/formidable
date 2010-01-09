<div id="poststuff">
<?php if ($title == true || $title == 'true'){ ?>
<h3><?php echo $form->name ?></h3>
<?php } ?>

<?php if ($description == true || $description == 'true'){ ?>
<p class="frm_description"><?php echo $form->description ?></p>
<?php } ?>

<input type="hidden" name="form_id" value="<?php echo $form->id ?>">
<?php if (isset($controller) && isset($plugin)){ ?>
<input type="hidden" name="controller" value="<?php echo $controller; ?>">
<input type="hidden" name="plugin" value="<?php echo $plugin; ?>">
<?php } ?>
<div id="frm_form_fields">
    <div>
    <?php

    if (isset($errors) && is_array($errors))
        $error_keys = array_keys($errors);
        
    foreach($values['fields'] as $field){
        $field_name = "item_meta[". $field['id'] ."]";
        if (apply_filters('frm_show_normal_field_type', true, $field))
            require(FRM_VIEWS_PATH.'/frm-fields/show.php');
        else
            do_action('frm_show_other_field_type', $field);
        
        do_action('frm_get_field_scripts', $field);
    }    

    ?>
    </div>
</div>
</div>
<?php do_action('frm_entries_footer_scripts',$values['fields']); ?>
<script type="text/javascript">
function frmClearDefault(default_value,thefield){if(thefield.value==default_value)thefield.value='';}
function frmReplaceDefault(default_value,thefield){if(thefield.value=='')thefield.value=default_value;}
</script>