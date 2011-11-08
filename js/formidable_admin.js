jQuery(document).ready(function($){
if(typeof(__FRMURL)!='undefined')
	var ajax_url=__FRMURL;
if(typeof(__FRMDEFDESC)!='undefined')
	var def_desc=__FRMDEFDESC;
var form_id=$('input[name="id"]').val();
		
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

if($(".frm_ipe_form_name").length>0){
$(".frm_ipe_form_name").editInPlace({
url:ajax_url,params:"action=frm_form_name_in_place_edit&form_id="+form_id,value_required:"true",bg_out:'#fff'
});

$(".frm_ipe_form_desc").editInPlace({
	url:ajax_url,params:"action=frm_form_desc_in_place_edit&form_id="+form_id,
	field_type:"textarea",textarea_rows:3,textarea_cols:60,default_text:def_desc
});
     
$(".frm_ipe_field_desc").editInPlace({
url:ajax_url,params:"action=frm_field_desc_in_place_edit",default_text:def_desc,field_type:'textarea',textarea_rows:3
});

$(".frm_ipe_field_option, .frm_ipe_field_option_select").editInPlace({url:ajax_url,params:"action=frm_field_option_ipe",default_text:'(Blank)'});
$(".frm_ipe_field_label").editInPlace({url:ajax_url,params:"action=frm_field_name_in_place_edit",value_required:"true"});
}

jQuery('.item-list-form').submit(function(){
if(jQuery('#bulkaction').val()=='delete'){return confirm('Are you sure you want to delete each of the selected items below?');}
});

jQuery('.frm_single_option').live("mouseenter mouseleave", function(evt){
if(evt.type=='mouseenter'){
jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_single_visible_hover").css('visibility','visible');
}else{
jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_single_visible_hover").css('visibility','hidden');
}
});

jQuery('li.ui-state-default').live('click', function(evt){
	var target=evt.target;
	$('.frm-show-hover').css('visibility','hidden'); $(this).children('.frm-show-hover').css('visibility','visible');
	$('.frm-show-click').hide(); $(this).find('.frm-show-click').show();
	var i=$(this).find('input[name^="item_meta"], select[name^="item_meta"], textarea[name^="item_meta"]')[0];
	if($(i).val()) $(this).find('.frm_default_val_icons').show().css('visibility', 'visible');
	else $(this).find('.frm_default_val_icons').hide().css('visibility', 'hidden');
	$('li.ui-state-default.selected').removeClass('selected'); $(this).addClass('selected');
	if(!$(target).is('.inplace_field') && !$(target).is('.frm_ipe_field_label') && !$(target).is('.frm_ipe_field_desc') && !$(target).is('.frm_ipe_field_option')){ $('.inplace_field').blur();}
});

$("img.frm_help[title]").hover(
	function(){frm_title=$(this).attr('title');$(this).removeAttr('title');$('#frm_tooltip').html(frm_title).fadeIn();},
	function(){$('#frm_tooltip').fadeOut();$(this).attr('title',frm_title);}
);

$("select[name='frm_theme_selector'] option").each(function(){
$(this).hover(function(){$('#frm_show_cal').html('<img src="'+ $(this).attr('id') +'" alt="" height="27px" width="30px"/>');},'');
});

$("select[name='frm_theme_selector']").change(function(){
var css='https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/'+$(this).val()+'/jquery-ui.css';
frmUpdateCSS(css);
var themeName=$(this).text();
$('input[name="frm_theme_css"]').val(css);
$('input[name="frm_theme_name"]').val(themeName);
return false;
});

jQuery('.field_type_list > li').draggable({connectToSortable:'#new_fields',cursor:'move',helper:'clone',revert:'invalid',delay:10});
jQuery('ul.field_type_list, .field_type_list li').disableSelection();

$('.frm_form_builder input[name^="item_meta"], .frm_form_builder textarea[name^="item_meta"]').keyup(function(){
var n=$(this).attr('name');
n=n.substring(10,n.length-1);
frmShowDefaults(n,jQuery(this).val());	
});
$('.frm_form_builder select[name^="item_meta"]').change(function(){
var n=$(this).attr('name');
n=n.substring(10,n.length-1);
frmShowDefaults(n,jQuery(this).val());	
});

if($(".frm_exclude_cat_list .frm_catlevel_2").length>0){
$('.frm_exclude_cat_list').each(function(){
	var frm_lev=$(this).find('.frm_catlevel_2');
	if(frm_lev.length>0) $(this).find('.check_lev1_label, .check_lev2_label').show();
	var frm_lev=$(this).find('.frm_catlevel_3'); if(frm_lev.length>0) $(this).find('.check_lev3_label').show();
	var frm_lev=$(this).find('.frm_catlevel_4'); if(frm_lev.length>0) $(this).find('.check_lev4_label').show();
});
}
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

function frmCheckAll(checked,n){
if(checked){jQuery("input[name='"+n+"[]']").attr("checked","checked");}
else{jQuery("input[name='"+n+"[]']").removeAttr("checked");}
}

function frmCheckAllLevel(checked,n,level){
var $kids=jQuery(".frm_catlevel_"+level).children(".frm_checkbox").children('label');
if(checked){$kids.children("input[name='"+n+"[]']").attr("checked","checked");}
else{$kids.children("input[name='"+n+"[]']").removeAttr("checked");}	
}

function frmAddNewForm(form,action){
if(form !='') window.location='?page=formidable&action='+action+'&id='+form;
}
function frmRedirectToForm(form,action){
if(form !='') window.location='?page=formidable-entries&action='+action+'&form='+form;
}

function frm_add_logic_row(id,ajax_url,form_id){
jQuery.ajax({
    type:"POST",url:ajax_url,
    data:"action=frm_add_logic_row&form_id="+form_id+"&field_id="+id+"&meta_name="+jQuery('#frm_logic_row_'+id+' > div').size(),
    success:function(html){jQuery('#frm_logic_row_'+id).append(html);}
});
}

function frmGetFieldValues(field_id,current_field_id,row,ajax_url){ 
    if(field_id){
    jQuery.ajax({
        type:"POST",url:ajax_url,
        data:"action=frm_get_field_values&current_field="+current_field_id+"&field_id="+field_id,
        success:function(msg){jQuery("#frm_show_selected_values_"+current_field_id+'_'+row).html(msg);} 
    });
    }
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

function frmShowDefaults(n,fval){
	if(fval){jQuery('#frm_clear_on_focus_'+n+',#frm_clear_on_focus_'+n+' a').css('visibility','visible').fadeIn('slow');}
	else{jQuery('#frm_clear_on_focus_'+n+',#frm_clear_on_focus_'+n+' a').css('visibility','visible').fadeOut('slow');}
}

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

function frm_delete_field(field_id){ 
    if(confirm("Are you sure you want to delete this field and all data associated with it?")){
	if(typeof(__FRMURL)!='undefined')
		var ajax_url=__FRMURL;
    jQuery.ajax({
        type:"POST",url:ajax_url,
        data:"action=frm_delete_field&field_id="+field_id,
        success:function(msg){jQuery("#frm_field_id_"+field_id).fadeOut("slow");}
    });
    }
};

function frm_field_hover(show, field_id){
	var html_id = '#frm_field_id_'+field_id;
	if(show){jQuery(html_id).children('.frm-show-hover').css('visibility','visible');}
	else{if(!jQuery(html_id).is('.selected')){jQuery(html_id).children(".frm-show-hover").css('visibility','hidden');}}
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

function frmGetFieldSelection(form_id,field_id,ajax_url){ 
    if(form_id){
    jQuery.ajax({type:"POST",url:ajax_url,
        data:"action=frm_get_field_selection&field_id="+field_id+"&form_id="+form_id,
        success:function(msg){ jQuery("#frm_show_selected_fields_"+field_id).html(msg);} 
    });
    }
};

function frmShowPostOpts(post_field,field_id){
    jQuery(".frm_custom_field_"+field_id+",.frm_exclude_cat_"+field_id).hide();
    if(post_field){
        if(post_field=='post_custom'){
            jQuery(".frm_custom_field_"+field_id).fadeIn('slow');
        }else if(post_field=='post_category'){
            jQuery(".frm_exclude_cat_"+field_id).fadeIn('slow');
            //get_cats to display
        }
        if(post_field!='') jQuery(".frm_post_title").fadeIn('slow');
    }
};

function frmSettingsTab(tab, id){
	var t = tab.attr('href');
	tab.parent().addClass('tabs').siblings('li').removeClass('tabs');
	jQuery('.general_settings,.styling_settings,#form_settings_page .tabs-panel,.mailchimp_settings').hide();
	jQuery('.'+id+'_settings').show();
	return false;
}

//function to append a new theme stylesheet with the new style changes
function frmUpdateCSS(locStr){
	var cssLink = jQuery('<link href="'+locStr+'" type="text/css" rel="Stylesheet" class="ui-theme" />');
	jQuery("head").append(cssLink);
	
	if( jQuery("link.ui-theme").size() > 3){
		jQuery("link.ui-theme:first").remove();
	}	
}