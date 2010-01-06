<script>
jQuery(document).ready(function(){
    jQuery('#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .ui-icon-trash.frm_delete_field_option').hide();
    jQuery('#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container.frm_single_option').hover(
      function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").show(); jQuery(this).children(".frm_spacer").hide();},
      function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").hide(); jQuery(this).children(".frm_spacer").show();}
    );
    
     jQuery("#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_ipe_field_option").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_option_ipe",
         show_buttons:true,
         default_text:'(Blank)'
     });
     
     jQuery("#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_ipe_field_option_select").editInPlace({
          url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
          params:"action=frm_field_option_ipe",
          show_buttons:true,
          default_text:'(Blank)',
          callback: function(original_element, html, original){
              jQuery("#"+original_element+'_select').html("The updated text is: " + html);
              return(html);
          }
      });
    
    jQuery("#frm_delete_field_<?php echo $field['id']; ?>-<?php echo $opt_key ?>_container .frm_delete_field_option").click( function(){
         var thisid=this.getAttribute('id');
         jQuery.ajax({
            type:"POST",
            url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
            data:"action=frm_delete_field_option&field="+thisid,
            success:function(msg){
                jQuery('#'+thisid+'_container').hide('highlight');
           } 
         });
         return false;
    });
});
</script>