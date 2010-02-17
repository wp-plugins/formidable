<script>
jQuery(document).ready(function($){
    jQuery('#frm_field_id_<?php echo $field['id']; ?> .ui-icon-trash').hide();
    jQuery('#frm_field_id_<?php echo $field['id']; ?> .ui-icon-arrowthick-2-n-s').hide();
    jQuery('#frm_field_id_<?php echo $field['id']; ?>.ui-state-default').hover(
        function(){
    		jQuery(this).children(".ui-icon-trash").show();
    		jQuery(this).children(".ui-icon-arrowthick-2-n-s").show(); 
    		jQuery(this).children(".ui-accordion-header").show();
    	},
        function(){
    		jQuery(this).children(".ui-icon-trash").hide();
    		jQuery(this).children(".ui-icon-arrowthick-2-n-s").hide(); 
    		jQuery(this).children(".ui-accordion-header").hide();
    		jQuery(this).children(".ui-accordion-header.ui-state-active").show();
    	}
    );
    
    $('#frm_form_editor_container #frm_field_id_<?php echo $field['id']; ?> .ui-accordion-header').addClass('ui-corner-all').spinDown();
    

    jQuery('#frm_field_id_<?php echo $field['id']; ?> .ui-icon-trash.frm_delete_field_option').hide();
    jQuery('#frm_field_id_<?php echo $field['id']; ?> .frm_single_option').hover(
      function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").show(); jQuery(this).children(".frm_spacer").hide();},
      function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").hide(); jQuery(this).children(".frm_spacer").show();}
    );
    
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_option").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_option_ipe",
         show_buttons:true,
         default_text:'(Blank)'
     });
     
     jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_option_select").editInPlace({
          url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
          params:"action=frm_field_option_ipe",
          show_buttons:true,
          default_text:'(Blank)',
          callback: function(original_element, html, original){
              jQuery("#"+original_element+'_select').html("The updated text is: " + html);
              return(html);
          }
      });
    
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_delete_field_option").click( function(){
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
     
     jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_label").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_name_in_place_edit",
         value_required:"true",
         show_buttons:true
     });
     
     jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_ipe_field_desc").editInPlace({
         url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
         params:"action=frm_field_desc_in_place_edit",
         default_text:"(Click here to add optional field description or instructions)",
         show_buttons:true,
         field_type:'textarea',
         textarea_rows:1
     });
         
     jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_mark_required").click( function(){
          var thisid=this.getAttribute('id');
          jQuery.ajax({
             type:"POST",
             url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
             data:"action=frm_mark_required&field="+thisid,
             success:function(msg){
                 jQuery('#'+thisid).switchClass('frm_mark_required','frm_unmark_required');
            } 
          });
     });
       
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_unmark_required").click( function(){
           var thisid=this.getAttribute('id');
           jQuery.ajax({
              type:"POST",
              url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
              data:"action=frm_unmark_required&field="+thisid,
              success:function(msg){
                  jQuery('#'+thisid).switchClass('frm_unmark_required','frm_mark_required');
             } 
           });
    });
    
    jQuery("#frm_field_id_<?php echo $field['id']; ?> .frm_add_field_option").click( function(){
         var thisid=this.getAttribute('id');
         jQuery.ajax({
            type:"POST",
            url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
            data:"action=frm_add_field_option&field="+thisid,
            success:function(msg){
                jQuery('#frm_add_'+thisid).before(msg);
           } 
         });
         return false;
    });
});
</script>