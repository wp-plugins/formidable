<script type="text/javascript">
jQuery(document).ready(function(){
jQuery('#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_single_show_hover').hide();
jQuery('#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_single_visible_hover').attr('visibility','hidden');

jQuery('#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container.frm_single_option').hover(
    function(){jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_single_visible_hover").css('visibility','visible');},
    function(){jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_single_visible_hover").css('visibility','hidden');}
);    
jQuery("#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_ipe_field_option").editInPlace({
url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_option_ipe",default_text:"<?php _e('(Blank)', 'formidable') ?>"
});
});
</script>