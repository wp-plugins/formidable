<span id="frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container" class="frm_single_option">
    <?php if ($field['type'] != 'select'){ ?>
    <input type='<?php echo $field['type'] ?>' name='<?php echo $field_name ?><?php echo ($field['type'] == 'checkbox')?'[]':''; ?>' value='<?php echo $opt ?>'<?php echo isset($checked)? $checked : ''; ?>/> 
    <?php } ?>
    <span class="frm_ipe_field_option" id="field_<?php echo $field['id']?>-<?php echo $opt_key ?>"><?php echo $opt ?></span>
    <span class="frm_spacer"></span>
    <a href="javascript:void(0);" class="ui-icon ui-icon-trash alignleft frm_delete_field_option" id="frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>"></a>
</span>
<div class="clear"></div>