jQuery(document).ready(function($){
$(".frm_elastic_text").elastic();

window.onscroll = document.documentElement.onscroll = frmSetMenuOffset;
frmSetMenuOffset();

if ($("input[name='options[success_action]']:checked").val() == 'redirect')
    $('.success_action_redirect_box.success_action_box').fadeIn('slow');
else if ($("input[name='options[success_action]']:checked").val() == 'page')
	$('.success_action_page_box.success_action_box').fadeIn('slow');
else
	$('.success_action_message_box.success_action_box').fadeIn('slow');
              
$("input[name='options[success_action]']").change(function(){
	$('.success_action_box').hide();
    if($(this).val()=='redirect')
        $('.success_action_redirect_box.success_action_box').fadeIn('slow');
    else if($(this).val()=='page')
        $('.success_action_page_box.success_action_box').fadeIn('slow');
	else
		$('.success_action_message_box.success_action_box').fadeIn('slow');
});

jQuery('.item-list-form').submit(function(){
if(jQuery('#bulkaction').val()=='delete'){return confirm('Are you sure you want to delete each of the selected items below?');}
});

jQuery('.frm_single_option').hover(
function(){jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_single_visible_hover").css('visibility','visible');},
function(){jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_single_visible_hover").css('visibility','hidden');}
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


});

function frmUpdateOpts(field_id, ajax_url, opts){
	jQuery('#frm_field_'+field_id+'_opts').html('').addClass('frm-loading-img');
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:{action:'frm_import_options',field_id:field_id,opts:opts},
		success:function(html){jQuery('#frm_field_'+field_id+'_opts').html(html).removeClass('frm-loading-img');}
	});	
}

function frm_remove_tag(html_tag){jQuery(html_tag).remove();}

function frmToggleLogic(id){
$ele = jQuery('#'+id);
$ele.fadeOut('slow'); $ele.next('.frm_logic_rows').fadeIn('slow');
}
function frm_show_div(div,value,show_if,class_id){
if(value==show_if) jQuery(class_id+div).fadeIn('slow'); else jQuery(class_id+div).fadeOut('slow');
}
function frm_select_item_checkbox(checked){if(!checked){jQuery(".select-all-item-action-checkboxes").removeAttr("checked");}}

function frm_select_all_checkboxes(checked){
if(checked){
jQuery(".item-action-checkbox").attr("checked","checked");jQuery(".select-all-item-action-checkboxes").attr("checked","checked");
}else{
jQuery(".item-action-checkbox").removeAttr("checked");jQuery(".select-all-item-action-checkboxes").removeAttr("checked");
}
}

function frmAddNewForm(form,action){
	if(form !='') window.location='?page=formidable&action='+action+'&id='+form;
}
function frmRedirectToForm(form,action){
	if(form !='') window.location='?page=formidable-entries&action='+action+'&form='+form;
}

function add_frm_field_link(form_id, field_type, ajax_url){
jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_insert_field&form_id="+form_id+"&field="+field_type,
success:function(msg){jQuery('#new_fields').append(msg);}
});
};

function frm_duplicate_field(field_id,ajax_url){  
jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_duplicate_field&field_id="+field_id,
success:function(msg){jQuery('#new_fields').append(msg);}
});
};

function frm_mark_required(field_id, required, images_url, ajax_url){
    var thisid='req_field_'+field_id;
    if(required=='0'){var switch_to='1';var atitle='Click to Mark as Not Required';var checked='checked="checked"';
	jQuery('.frm_required_details'+field_id).fadeIn('slow');}
	else{var switch_to='0';var atitle='Click to Mark as Required';var checked='';
	jQuery('.frm_required_details'+field_id).fadeOut('slow');}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_mark_required('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="alignleft frm_required'+switch_to+'" id="'+thisid+'" title="'+atitle+'"><img src="'+images_url+'/required.png" alt="required" /></a>');
	jQuery('#frm_'+thisid).replaceWith('<input type="checkbox" id="frm_'+thisid+'" name="field_options[required_'+field_id+']" value="1" '+checked+' onclick="frm_mark_required('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" />');
    jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_mark_required&field="+field_id+"&required="+switch_to});
};

function frm_clear_on_focus(field_id, active, images_url, ajax_url){
    var thisid='clear_field_'+field_id;
    if (active=='1'){var switch_to='0';var new_class='frm_inactive_icon';}
    else{var switch_to='1';var new_class='';}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_clear_on_focus('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="'+new_class +' frm-show-hover" id="'+thisid+'"><img src="'+images_url+'/reload.png" alt="reload" /></a>');
    jQuery.ajax({type:"POST",url:ajax_url,data:"action=frm_clear_on_focus&field="+field_id+"&active="+switch_to});
};

function frm_default_blank(field_id, active, images_url, ajax_url){
    var thisid='default_blank_'+field_id;
    if(active=='1'){var switch_to='0';var new_class='frm_inactive_icon';}
	else{var switch_to='1';var new_class='';}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_default_blank('+field_id+','+switch_to+',\''+images_url+'\',\''+ajax_url+'\')" class="'+new_class+' frm-show-hover" id="'+thisid+'"><img src="'+images_url+'/error.png" alt="error" /></a>');
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

function frmSettingsTab(tab, id){
	var t = tab.attr('href');
	tab.parent().addClass('tabs').siblings('li').removeClass('tabs');
	jQuery('#general_settings,#styling_settings').hide();
	jQuery('#'+id+'_settings').show();
	return false;
}