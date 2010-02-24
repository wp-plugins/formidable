<span id="frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container" class="frm_single_option">
    <span class="frm_spacer"></span>
    <a href="javascript:frm_delete_field_option(<?php echo $field['id']?>, <?php echo $opt_key ?>);" class="frm_single_show_hover alignleft" ><img src="<?php echo FRM_IMAGES_URL ?>/trash.png" alt="Delete"></a>
    <?php if ($field['type'] != 'select'){ ?>
    <input type='<?php echo $field['type'] ?>' name='<?php echo $field_name ?><?php echo ($field['type'] == 'checkbox')?'[]':''; ?>' value='<?php echo $opt ?>'<?php echo isset($checked)? $checked : ''; ?>/> 
    <?php } ?>
    <span class="frm_ipe_field_option" id="field_<?php echo $field['id']?>-<?php echo $opt_key ?>"><?php echo $opt ?></span>
</span>
<div class="clear"></div>