<script type="text/javascript">
jQuery(document).ready(function($){
$(".frm_ipe_form_name").editInPlace({
	url:"<?php echo $frm_ajax_url ?>",params:"action=frm_form_name_in_place_edit&form_id=<?php echo $id; ?>",
	value_required:"true", bg_out:'#fff'
});

$(".frm_ipe_form_desc").editInPlace({
	url:"<?php echo $frm_ajax_url ?>",params:"action=frm_form_desc_in_place_edit&form_id=<?php echo $id; ?>",
	field_type:"textarea",textarea_rows:3,textarea_cols:60,
	default_text:"<?php _e('(Click here to add form description or instructions)', 'formidable') ?>"
});

$(".frm_ipe_field_option").editInPlace({url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_option_ipe",default_text:'(Blank)'});
     
$(".frm_ipe_field_option_select").editInPlace({url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_option_ipe",default_text:'(Blank)'});
    

$(".frm_ipe_field_label").editInPlace({
    url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_name_in_place_edit",value_required:"true"
});
     
$(".frm_ipe_field_desc").editInPlace({
    url:"<?php echo $frm_ajax_url ?>",params:"action=frm_field_desc_in_place_edit",
    default_text:"(Click here to add optional description or instructions)",
    field_type:'textarea',textarea_rows:3
});

$("#new_fields").sortable({
    cursor:'move',
    accepts:'field_type_list',
    revert:true,
    receive:function(event,ui){
        var new_id=(ui.item).attr('id');
        jQuery('#new_fields .frmbutton#'+new_id).replaceWith('<img class="frmbutton frmbutton_loadingnow" id="'+new_id+'" src="<?php echo FRM_IMAGES_URL; ?>/ajax_loader.gif" alt="<?php _e('Loading...', 'formidable'); ?>" />');
        jQuery.ajax({
            type:"POST",url:"<?php echo $frm_ajax_url ?>",data:"action=frm_insert_field&form_id=<?php echo $id; ?>&field="+new_id,
            success:function(msg){ $('.frmbutton_loadingnow#'+new_id).replaceWith(msg);
                var order= $('#new_fields').sortable('serialize');
                jQuery.ajax({type:"POST",url:"<?php echo $frm_ajax_url ?>",data:"action=frm_update_field_order&"+order});
            }
        });
    },
    update:function(){
        var order= $('#new_fields').sortable('serialize');
        jQuery.ajax({type:"POST",url:"<?php echo $frm_ajax_url ?>",data:"action=frm_update_field_order&"+order});
    }
});
});

function frm_delete_field(field_id){ 
    if(confirm("<?php _e('Are you sure you want to delete this field and all data associated with it?', 'formidable'); ?>")){
    jQuery.ajax({
        type:"POST",url:"<?php echo $frm_ajax_url ?>",
        data:"action=frm_delete_field&field_id="+field_id,
        success:function(msg){jQuery("#frm_field_id_"+field_id).fadeOut("slow");}
    });
    }
};

</script>