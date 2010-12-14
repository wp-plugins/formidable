<script>
jQuery(document).ready(function($){
    jQuery('#frm_field_id_<?php echo $field['id']; ?>.ui-state-default').click(function(){
    	$('.frm-show-click').hide(); $(this).children(".frm-show-click").show(); 
        $('.frm-show-hover').hide(); $(this).children(".frm-show-hover").show();
        $('li.ui-state-default.selected').removeClass('selected'); $(this).addClass('selected');
    });
    
    $('#frm_form_editor_container #frm_field_id_<?php echo $field['id']; ?> .theme-group-header').addClass('corner-all').spinDown();

    jQuery('#frm_field_id_<?php echo $field['id']; ?> .frm_single_option').hover(
      function(){jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_spacer").hide();},
      function(){jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_spacer").show();}
    );
    
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_option").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_option_ipe", default_text:"<?php _e('(Blank)', 'formidable') ?>"
    });
     
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_label").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_name_in_place_edit", value_required:"true"
    });
     
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_desc").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_desc_in_place_edit",
         default_text:"<?php _e('(Click here to add optional description or instructions)', 'formidable') ?>",
         field_type:'textarea', textarea_rows:1
    });
});
</script>