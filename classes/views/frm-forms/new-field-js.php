<script type="text/javascript">
jQuery(document).ready(function($){
<?php if(!isset($partial_js) or !$partial_js){ ?>
$('#frm_form_editor_container #frm_field_id_<?php echo $field['id']; ?> .theme-group-header').addClass('corner-all').spinDown();
<?php } ?>
$('input[name^="item_meta"], select[name^="item_meta"], textarea[name^="item_meta"]').css('float','left');
jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_option").editInPlace({
    url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_option_ipe", default_text:"<?php _e('(Blank)', 'formidable') ?>"
});
jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_label").editInPlace({
    url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_name_in_place_edit", value_required:"true"
});
jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_desc").editInPlace({
    url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_desc_in_place_edit",field_type:'textarea',textarea_rows:3,
    default_text:"(<?php _e('Click here to add optional description or instructions', 'formidable') ?>)"
});
});
</script>