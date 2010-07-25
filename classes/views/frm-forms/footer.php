<script type="text/javascript">
jQuery(document).ready(function($){
$(".frm_ipe_form_name").editInPlace({
	url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
	params:"action=frm_form_name_in_place_edit&form_id=<?php echo $id; ?>",
	value_required:"true", bg_out:'#fff'
});

$(".frm_ipe_form_desc").editInPlace({
	url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
	params:"action=frm_form_desc_in_place_edit&form_id=<?php echo $id; ?>",
	field_type:"textarea",textarea_rows:3,textarea_cols:60,
	default_text:"<?php _e('(Click here to add form description or instructions)', 'formidable') ?>"
});

$(".frm_ipe_field_option").editInPlace({
    url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
    params:"action=frm_field_option_ipe",
    default_text:'(Blank)'
});
     
$(".frm_ipe_field_option_select").editInPlace({
    url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
    params:"action=frm_field_option_ipe",
    default_text:'(Blank)'
});
    

$(".frm_ipe_field_label").editInPlace({
    url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
    params:"action=frm_field_name_in_place_edit",
    value_required:"true"
});
     
$(".frm_ipe_field_desc").editInPlace({
    url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
    params:"action=frm_field_desc_in_place_edit",
    default_text:"(Click here to add optional description or instructions)",
    field_type:'textarea',
    textarea_rows:1
});

$("#new_fields").sortable({
    cursor:'move',
    accepts:'field_type_list',
    revert:true,
    receive:function(event,ui){
        var new_id = (ui.item).attr('id');
        jQuery('#new_fields .frmbutton#'+new_id).replaceWith('<img class="frmbutton frmbutton_loadingnow" id="' + new_id + '" src="<?php echo FRM_IMAGES_URL; ?>/ajax_loader.gif" alt="<?php _e('Loading...', FRM_PLUGIN_NAME); ?>" />');
        jQuery.ajax({
            type:"POST",
            url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
            data:"action=frm_insert_field&form_id=<?php echo $id; ?>&field="+new_id,
            success:function(msg){ $('.frmbutton_loadingnow#'+new_id).replaceWith(msg);
                var order= $('#new_fields').sortable('serialize');
                jQuery.ajax({
                    type:"POST",
                    url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
                    data:"action=frm_update_field_order&"+order
                });
            }
        });
    },
    update:function(){
        var order= $('#new_fields').sortable('serialize');
        jQuery.ajax({
            type:"POST",
            url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
            data:"action=frm_update_field_order&"+order
        });
    }
});
});

function frm_mark_required(field_id, required){
    var thisid= 'req_field_' + field_id;
    if (required == '0'){
        var switch_to = '1';
        var atitle = 'Click to Mark as Not Required';
    }else{
        var switch_to = '0';
        var atitle = 'Click to Mark as Required';
    }
    jQuery('#'+thisid).replaceWith('<img id="' + thisid + '" class="ui-icon alignleft" src="<?php echo FRM_IMAGES_URL; ?>/required_loader.gif" alt="<?php _e('Loading...', FRM_PLUGIN_NAME); ?>" />');
    jQuery.ajax({
        type:"POST",
        url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_mark_required&field="+field_id+"&required="+switch_to,
        success:function(msg){ jQuery('#'+thisid).replaceWith('<a href="javascript:frm_mark_required('+field_id+','+switch_to+')" class="alignleft frm_required'+switch_to+'" id="'+thisid+'" title="'+atitle+'"><img src="<?php echo FRM_IMAGES_URL?>/required.png" alt="required"></a>');}
    });
};

function frm_clear_on_focus(field_id, active){
    var thisid= 'clear_field_' + field_id;
    if (active == '1'){
        var switch_to = '0';
        var new_class = 'frm_inactive_icon';
    }else{
        var switch_to = '1';
        var new_class = '';
    }
    jQuery('#'+thisid).replaceWith('<img id="' + thisid + '" src="<?php echo FRM_IMAGES_URL; ?>/wpspin_light.gif" alt="<?php _e('Loading...', FRM_PLUGIN_NAME); ?>" />');
    jQuery.ajax({
        type:"POST",
        url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_clear_on_focus&field="+field_id+"&active="+switch_to,
        success:function(msg){ jQuery('#'+thisid).replaceWith('<a href="javascript:frm_clear_on_focus( '+field_id+',  '+switch_to+')" class="'+new_class +' frm-show-hover" id="'+thisid+'"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></a>');}
    });
};

function frm_default_blank(field_id, active){
    var thisid= 'default_blank_' + field_id;
    if (active == '1'){
        var switch_to = '0';
        var new_class = 'frm_inactive_icon';
    }else{
        var switch_to = '1';
        var new_class = '';
    }
    jQuery('#'+thisid).replaceWith('<img id="' + thisid + '" src="<?php echo FRM_IMAGES_URL; ?>/wpspin_light.gif" alt="<?php _e('Loading...', FRM_PLUGIN_NAME); ?>" />');
    jQuery.ajax({
        type:"POST",
        url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_default_blank&field="+field_id+"&active="+switch_to,
        success:function(msg){ jQuery('#'+thisid).replaceWith('<a href="javascript:frm_default_blank('+field_id+', '+switch_to+')" class="'+new_class +' frm-show-hover" id="'+thisid+'"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></a>');}
    });
};

function frm_read_only(field_id, active){
    var thisid= 'read_only_' + field_id;
    if (active == '1'){
        var switch_to = '0';
        var new_class = 'frm_inactive_icon';
    }else{
        var switch_to = '1';
        var new_class = '';
    }
    jQuery('#'+thisid).replaceWith('<img id="'+thisid+'" src="<?php echo FRM_IMAGES_URL; ?>/wpspin_light.gif" alt="<?php _e('Loading...', FRM_PLUGIN_NAME); ?>" />');
    jQuery.ajax({
        type:"POST",url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_read_only&field="+field_id+"&active="+switch_to,
        success:function(msg){ jQuery('#'+thisid).replaceWith('<a href="javascript:frm_read_only('+field_id+', '+switch_to+')" class="'+new_class+' frm-show-hover" id="'+thisid+'"><img src="<?php echo FRM_IMAGES_URL?>/readonly.png"></a>');}
    });
};

function frm_delete_field(field_id){ 
    if(confirm("<?php _e('Are you sure you want to delete this field and all data associated with it?', FRM_PLUGIN_NAME); ?>")){
    jQuery.ajax({
        type:"POST",
        url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_delete_field&field_id="+field_id,
        success:function(msg){jQuery("#frm_field_id_"+field_id).fadeOut("slow");}
    });
    }
};

</script>