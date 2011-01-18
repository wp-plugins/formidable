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
	if(!$(target).is('.inplace_field') && !$(target).is('.frm_ipe_field_label') && !$(target).is('.frm_ipe_field_desc') && !$(target).is('.frm_ipe_field_option')){ $('.inplace_field').blur();}
});
$("img.frm_help[title]").tooltip({tip:'#frm_tooltip',lazy:true});
$("img.frm_help_text[title]").tooltip({tip:'#frm_tooltip_text',lazy:true});
$("img.frm_help_big[title]").tooltip({tip:'#frm_tooltip_big',lazy:true});

jQuery('.field_type_list > li').draggable({connectToSortable:'#new_fields',cursor:'move',helper:'clone',revert:'invalid',delay:10});
jQuery("ul.field_type_list, .field_type_list li").disableSelection();

/*
jQuery('.frm-grid').find('input').live('keydown',function(e){
	if(e.which >= 37 && e.which <= 40){
		var matches;
		var pattern= /_([0-9]+)_([0-9]+)$/;
		if (matches=this.id.match(pattern)){
			var row=parseInt(matches[1]); 
			var col=parseInt(matches[2]);
			var table=jQuery('#'+this.id).parents('.frm-grid');
			var max_row=jQuery('#'+this.id).parents('.frm-grid').find('tr').length - 2; // the -2 comes from 1 for the header row and 1 for the fact that we're 0-based
			var max_col=jQuery('#'+this.id).parents('tr').find('td input').length -1; // the -1 is for the fact that we're 0-based
			switch (e.which){
			case 37: // left arrow
				if (col > 0){col--;}else if(row > 0){col=max_col;row--;}
				break;
			case 38: // up arrow
				if(row > 0){row--;}else if(col > 0){col--;row=max_row;}
				e.preventDefault(); // prevent list of previously entered values showing up and confusing
				break;
			case 39: // right arrow
				if (col < max_col){col++;}else if (row < max_row){row++;col=0;}
				break;
			case 40: // down arrow
				if (row < max_row){row++;}else if (col < max_col){col++;row=0;}
				break;
			}
			if (row != parseInt(matches[1]) || col != parseInt(matches[2])){
				// need to reset the focus
				jQuery('#' + this.id.replace(pattern,'_'+row+'_'+col)).focus();
			}
		}
	}
}); */
});

function frm_select_item_checkbox(checked){if(!checked){jQuery(".select-all-item-action-checkboxes").removeAttr("checked");}}

function frm_select_all_checkboxes(checked){
if(checked){
	jQuery(".item-action-checkbox").attr("checked","checked");jQuery(".select-all-item-action-checkboxes").attr("checked","checked");
}else{
	jQuery(".item-action-checkbox").removeAttr("checked");jQuery(".select-all-item-action-checkboxes").removeAttr("checked");
}
}

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

function frm_mark_required(field_id, required, images_url, ajax_url){
    var thisid='req_field_'+field_id;
    if(required=='0'){var switch_to='1';var atitle='Click to Mark as Not Required';}
	else{var switch_to='0';var atitle='Click to Mark as Required';}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_mark_required('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="alignleft frm_required'+switch_to+'" id="'+thisid+'" title="'+atitle+'"><img src="'+images_url+'/required.png" alt="required"></a>');
    jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_mark_required&field="+field_id+"&required="+switch_to});
};

function frm_clear_on_focus(field_id, active, images_url, ajax_url){
    var thisid='clear_field_'+field_id;
    if (active=='1'){var switch_to='0';var new_class='frm_inactive_icon';}
    else{var switch_to='1';var new_class='';}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_clear_on_focus('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="'+new_class +' frm-show-hover" id="'+thisid+'"><img src="'+images_url+'/reload.png"></a>');
    jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_clear_on_focus&field="+field_id+"&active="+switch_to});
};

function frm_default_blank(field_id, active, images_url, ajax_url){
    var thisid='default_blank_'+field_id;
    if(active=='1'){var switch_to='0';var new_class='frm_inactive_icon';}
	else{var switch_to='1';var new_class='';}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_default_blank('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="'+new_class+' frm-show-hover" id="'+thisid+'"><img src="'+images_url+'/error.png"></a>');
    jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_default_blank&field="+field_id+"&active="+switch_to});
};

function frm_add_field_option(field_id, ajax_url, table){
	var data = {action:'frm_add_field_option',field_id:field_id,t:table};
	jQuery.post(ajax_url,data,function(msg){
		jQuery('#frm_add_field_'+field_id).before(msg);
		if(table=='row'){ jQuery('#frm-grid-'+field_id+' tr:last').after(msg);}
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
	if(!element_id || element_id == 'content'){
		send_to_editor(variable);
		return;
	}
	var content_box=jQuery("#"+element_id);
	if(content_box){
		if(document.selection){content_box[0].focus();document.selection.createRange().text=variable;}
		else if(content_box[0].selectionStart){obj=content_box[0];obj.value=obj.value.substr(0,obj.selectionStart)+variable+obj.value.substr(obj.selectionEnd,obj.value.length);}
		else{content_box.val(variable+content_box.val());}
	}
}

function frm_delete_row(field_id,opt_key,ajax_url){
	if (jQuery('#frm-grid-'+field_id+' tr').length == 2){ // header row and only one data row
		alert('Sorry, you must leave at least one row in this table.');
	}else if(confirm('Are you sure you wish to permanently delete this row? This cannot be undone.')){
		jQuery.ajax({type:"POST",url:ajax_url,
	        data:"action=frm_delete_field_option&field_id="+field_id+"&opt_key="+opt_key,
	        success:function(msg){jQuery('#frm-grid-'+field_id+' tr.'+opt_key).fadeOut("slow");frm_adjust_row_numbers(field_id);}
	    });
	}
}

function frm_delete_col(field_id,opt_key,ajax_url){
	if (jQuery('#frm-grid-'+field_id+' th').length == 2){ // header col and only one data col
		alert('Sorry, you must leave at least one column in this table.');
	}else if(confirm('Are you sure you wish to permanently delete this column? This cannot be undone.')){
		jQuery.ajax({type:"POST",url:ajax_url,
			data:"action=frm_delete_field_option&field_id="+field_id+"&opt_key="+opt_key,
		    success:function(msg){
				jQuery('#frm-grid-'+field_id+' td.'+opt_key).fadeOut("slow");
				jQuery('#frm-grid-'+field_id+' th.'+opt_key).fadeOut("slow");
				frm_adjust_row_numbers(field_id);
			}
		});
	}
}

function frm_adjust_row_numbers(field_id){
	var row_num;
	jQuery('#frm-grid-'+field_id+' tr').each(function(){
		if (row_num == null){
			// skip the first row (column headers)
			row_num=0;
		}else{
			// This searches for inputs and readjusts their name to match the new row numbering scenario
			jQuery(this).find('input').each(function(){  //input[name^=item_meta]
				var name=jQuery(this).attr('name');
				name=name.replace(/\[[0-9]+\]\[\]/,'['+row_num+'][]');
				jQuery(this).attr('name',name);

				var id=jQuery(this).attr('id');
				id=id.replace(/_[0-9]+(_[0-9]+)$/,'_'+row_num+'$1');
				jQuery(this).attr('id',id);
			});
			
			// Now replace the javascript (for delete_row)
			jQuery(this).find('a').each(function(){
				var href=jQuery(this).attr('href');
				href=href.replace(/(delete_row\([0-9]+,)[0-9]+/,'$1'+row_num);
				jQuery(this).attr('href',href);
			});
			
			// Finally, need to reset the class for the row
			jQuery(this).get(0).className=jQuery(this).get(0).className.replace(/\brow-.*?\b/g,'');
			jQuery(this).addClass("row-"+row_num);
			row_num++;
		}
	});
}

var frm_active_requests=0; // = false;
function frm_add_row(field_id,ajax_url){
    jQuery.ajax({
        type:"POST",url:ajax_url,
        data:"action=frm_add_table_row&field_id="+field_id+"&row_num="+(jQuery('#frm-grid-'+field_id+' tr').length-1+frm_active_requests++),
        success:function(msg){
			frm_active_requests--;jQuery('#frm-grid-'+field_id+' tr:last').after(msg);
			frm_post_add_row(field_id,jQuery('#frm-grid-'+field_id+' tr:last'));
		}
    });
}

function frm_post_add_row(field_id,new_row){
	// Just a stub that can be overridden by another script
}