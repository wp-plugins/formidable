<script type="text/javascript">
jQuery(document).ready(function(){
jQuery(".frm_ipe_field_option").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_field_option_ipe",show_buttons:true,default_text:'(Blank)'});
     
jQuery(".frm_ipe_field_option_select").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_field_option_ipe",show_buttons:true,default_text:'(Blank)',
callback: function(original_element, html, original){jQuery("#"+original_element+'_select').html("The updated text is: "+html);return(html);}
});
    
jQuery(".frm_delete_field_option").click(function(){var thisid=this.getAttribute('id');jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_delete_field_option&field="+thisid,success:function(msg){jQuery('#'+thisid+'_container').hide('highlight');}});return false;});
    
jQuery(".frm_ipe_form_name").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_form_name_in_place_edit&form_id=<?php echo $id; ?>",value_required:"true",show_buttons:true});
jQuery(".frm_ipe_form_desc").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_form_desc_in_place_edit&form_id=<?php echo $id; ?>",field_type:"textarea",show_buttons:true,textarea_rows:3,textarea_cols:60,default_text:"(Click here to add form description or instructions)",});
jQuery(".frm_ipe_field_label").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_field_name_in_place_edit",value_required:"true",show_buttons:true});
     
jQuery(".frm_ipe_field_desc").editInPlace({url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",params:"action=frm_field_desc_in_place_edit",default_text:"(Click here to add optional description or instructions)",show_buttons:true,field_type:'textarea',textarea_rows:1});
         
jQuery(".frm_mark_required").click(function(){var thisid=this.getAttribute('id');jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_mark_required&field="+thisid,success:function(msg){jQuery('#'+thisid).switchClass('frm_mark_required','frm_unmark_required');}});});
       
jQuery(".frm_unmark_required").click(function(){var thisid=this.getAttribute('id');jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_unmark_required&field="+thisid,success:function(msg){jQuery('#'+thisid).switchClass('frm_unmark_required','frm_mark_required');}});});
    
jQuery(".frm_add_field_option").click(function(){var thisid=this.getAttribute('id');jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_add_field_option&field="+thisid,success:function(msg){jQuery('#frm_add_'+thisid).before(msg);}});return false;});
     
jQuery("#new_fields").sortable({cursor:'move',accepts:'field_type_list',revert:true,
    receive:function(event,ui){
        var new_id = (ui.item).attr('id');
        jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
            data:"action=frm_insert_field&form_id=<?php echo $id; ?>&position="+ui.position+"&field="+new_id,
            success:function(msg){jQuery('#new_fields .frmbutton#'+new_id).replaceWith(msg);}
        });
    },
    update:function(){var order=jQuery('#new_fields').sortable('serialize');jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_update_field_order&"+order});}});
});
jQuery('.field_type_list > li').draggable({connectToSortable:'#new_fields',cursor:'move',helper:'clone',revert:'invalid',delay:10});
jQuery("ul.field_type_list, .field_type_list li").disableSelection();

window.onunload = function(){jQuery.ajax({type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",data:"action=frm_delete_form_wo_fields&form_id=<?php echo $id; ?>"});return false;};
</script>