jQuery(document).ready(function($){
$(".frm_elastic_text").elastic();	
	
window.onscroll = document.documentElement.onscroll = frmSetMenuOffset;
frmSetMenuOffset();

if ($("input[name='options[success_action]']:checked").val() == 'redirect')
    $('.success_action_redirect_box.success_action_box').show();
else if ($("input[name='options[success_action]']:checked").val() == 'page')
	$('.success_action_page_box.success_action_box').show();
else
	$('.success_action_message_box.success_action_box').show();
              
$("input[name='options[success_action]']").change(function(){
	$('.success_action_box').hide();
    if ($(this).val() == 'redirect')
        $('.success_action_redirect_box.success_action_box').show();
    else if ($(this).val() == 'page')
        $('.success_action_page_box.success_action_box').show();
	else
		$('.success_action_message_box.success_action_box').show();
});

jQuery('.select-all-item-action-checkboxes').change(function(){
if (jQuery(this).attr("checked")){
jQuery(".item-action-checkbox").attr("checked","checked");
jQuery(".select-all-item-action-checkboxes").attr("checked","checked");
}else{
jQuery(".item-action-checkbox").removeAttr("checked");
jQuery(".select-all-item-action-checkboxes").removeAttr("checked");
}
});

$('.item-action-checkbox').change(function(){ if(!$(this).attr("checked")){ $(".select-all-item-action-checkboxes").removeAttr("checked");}});

jQuery('.item-list-form').submit(function(){
if(jQuery('#bulkaction').val() == 'delete'){return confirm('Are you sure you want to delete each of the selected items below?');}
});

jQuery('.frm_single_option').hover(
function(){jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_spacer").hide();},
function(){jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_spacer").show();}
);

jQuery('li.ui-state-default').click(function(evt){
	var target = evt.target;
	$('.frm-show-hover').hide(); $(this).children(".frm-show-hover").show();
	$('.frm-show-click').hide(); $(this).children(".frm-show-click").show(); 
	$('li.ui-state-default.selected').removeClass('selected'); $(this).addClass('selected');
	if(!$(target).is('.inplace_field') && !$(target).is('.frm_ipe_field_label') && !$(target).is('.frm_ipe_field_desc') && !$(target).is('.frm_ipe_field_desc').children() && !$(target).is('.frm_ipe_field_option')){ $('.inplace_field').blur();}
});
$("img.frm_help[title]").tooltip({tip:'#frm_tooltip',lazy:true});
$("img.frm_help_text[title]").tooltip({tip:'#frm_tooltip_text',lazy:true});
$("img.frm_help_big[title]").tooltip({tip:'#frm_tooltip_big',lazy:true});

jQuery('.field_type_list > li').draggable({connectToSortable:'#new_fields',cursor:'move',helper:'clone',revert:'invalid',delay:10});
jQuery("ul.field_type_list, .field_type_list li").disableSelection();
});

function add_frm_field_link(form_id, field_type, ajax_url){
    jQuery.ajax({type:"POST",url:ajax_url,
       data:"action=frm_insert_field&form_id="+form_id+"&field="+field_type,
       success:function(msg){jQuery('#new_fields').append(msg);}
    });
};

function frm_duplicate_field(field_id, ajax_url){  
    jQuery.ajax({type:"POST",url:ajax_url,
       data:"action=frm_duplicate_field&field_id="+field_id,
       success:function(msg){jQuery('#new_fields').append(msg);}
    });
};

function frm_add_field_option(field_id, ajax_url){
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_add_field_option&field_id="+field_id,
        success:function(msg){jQuery('#frm_add_field_'+field_id).before(msg);}
    });
};

function frm_delete_field_option(field_id, opt_key, ajax_url){
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_delete_field_option&field_id="+field_id+"&opt_key="+opt_key,
        success:function(msg){ jQuery('#frm_delete_field_'+field_id+'-'+opt_key+'_container').fadeOut("slow");}
    });
};

function frm_field_hover(show, field_id){
	var html_id = '#frm_field_id_'+field_id;
	if(show){jQuery(html_id).children(".frm-show-hover").show();}
	else{if(!jQuery(html_id).is('.selected')){jQuery(html_id).children(".frm-show-hover").hide();}}
}

function frmSetMenuOffset() { 
	var fields = jQuery('#frm_form_options .themeRoller');
	if (!fields) return;
	var currentOffset = document.documentElement.scrollTop || document.body.scrollTop; // body for Safari
	var desiredOffset = 315 - currentOffset;
	if (desiredOffset < 10) desiredOffset = 10;
	//if (desiredOffset != parseInt(header.style.top)) 
		fields.attr('style', 'top:'+desiredOffset + 'px;');
}

function frmDisplayFormSelected(form_id, ajax_url){
    if (form_id == '') return;
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_get_field_tags&form_id="+form_id,
        success:function(html){ jQuery('#content_fields').html(html);}
    });
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_get_field_tags&target_id=dyncontent&form_id="+form_id,
        success:function(html){ jQuery('#dyncontent_fields').html(html);}
    });
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_get_entry_select&form_id="+form_id,
        success:function(html){ jQuery('#entry_select_container').html(html);}
    });
};

function frmInsertFieldCode(element_id, variable){
	var content_box=jQuery("#"+element_id);
	if(document.selection){content_box[0].focus();document.selection.createRange().text=variable;}
	else if(content_box[0].selectionStart){obj = content_box[0];obj.value=obj.value.substr(0,obj.selectionStart)+variable+obj.value.substr(obj.selectionEnd,obj.value.length);}
	else{content_box.val(variable+content_box.val());}
}
/*
delete_row = function(field_id,row){
	jQuery(function(){
		if (jQuery('#frm-table-' + field_id + ' tr').length == 2){ // header row and only one data row
			alert('Sorry, you must leave at least one row in this table.');
		}
		else{
			var data_exists = false;
			jQuery('#frm-table-' + field_id + ' tr.row-' + row).find('input').each(function(){
				if (jQuery(this).val() != "") data_exists = true;
			})
			if (!data_exists || confirm('Are you sure you wish to permanently delete this row?  This cannot be undone.')){
				jQuery('#frm-table-' + field_id + ' tr.row-' + row).remove();
				adjust_row_numbers(field_id);
				post_delete_row(field_id);
			}
		}
	});
}

adjust_row_numbers = function(field_id){
	var row_num;
	jQuery('#frm-table-' + field_id + ' tr').each(function(){
		if (row_num == null){
			// skip the first row (column headers)
			row_num = 0;
		}else{
			// This searches for inputs and readjusts their name to match the new row numbering scenario
			jQuery(this).find('input').each(function(){  //input[name^=item_meta]
				var name = jQuery(this).attr('name');
				name = name.replace(/\[[0-9]+\]\[\]/,'[' + row_num + '][]');
				jQuery(this).attr('name',name);

				var id = jQuery(this).attr('id');
				id = id.replace(/_[0-9]+(_[0-9]+)$/,'_' + row_num + '$1');
				jQuery(this).attr('id',id);
			});
			
			// Now replace the javascript (for delete_row)
			jQuery(this).find('a').each(function(){
				var href = jQuery(this).attr('href');
				href = href.replace(/(delete_row\([0-9]+,)[0-9]+/,'$1'+row_num);
				jQuery(this).attr('href',href);
			});
			
			// Finally, need to reset the class for the row
			jQuery(this).get(0).className = jQuery(this).get(0).className.replace(/\brow-.*?\b/g, '');
			jQuery(this).addClass("row-" + row_num);

			row_num++;
		}
	});
}

var active_requests = 0; // = false;
add_row = function(field_id){
    jQuery.ajax({
        type:"POST",
        url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
        data:"action=frm_add_table_row&field_id="+field_id+"&row_num="+(jQuery('#frm-table-' + field_id + ' tr').length-1+active_requests++),
        success:function(msg){
			active_requests--;
			jQuery('#frm-table-' + field_id + ' tr:last').after(msg);
			post_add_row(field_id,jQuery('#frm-table-' + field_id + ' tr:last'));
		}
    });
}

post_add_row = function(field_id,new_row){
	// Just a stub that can be overridden by another script
}
post_delete_row = function(field_id){
	// Just a stub that can be overridden by another script
} */