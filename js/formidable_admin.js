jQuery(document).ready(function($){

window.onscroll=document.documentElement.onscroll=frmSetMenuOffset;
frmSetMenuOffset();
$("input[name='options[success_action]']").change(function(){
$('.success_action_box').hide();
if($(this).val()=='redirect'){$('.success_action_redirect_box.success_action_box').fadeIn('slow');}
else if($(this).val()=='page'){$('.success_action_page_box.success_action_box').fadeIn('slow');}
else{$('.frm_show_form_opt').show();$('.success_action_message_box.success_action_box').fadeIn('slow');}
});

if($('#new_fields').length){
$('#new_fields').sortable({
    placeholder:'sortable-placeholder',axis:'y',cursor:'move',opacity:0.65,
    cancel:'.widget,.frm_field_opts_list,input,textarea,select',
    accepts:'field_type_list',revert:true,forcePlaceholderSize:true,
    receive:function(event,ui){
        var new_id=(ui.item).attr('id');
        jQuery('#new_fields .frmbutton.frm_t'+new_id).replaceWith('<img class="frmbutton frmbutton_loadingnow" id="'+new_id+'" src="'+frm_js.images_url+'/ajax_loader.gif" alt="'+frm_js.loading+'" />');
        jQuery.ajax({
            type:"POST",url:ajaxurl,data:"action=frm_insert_field&form_id="+$('input[name="id"]').val()+"&field="+new_id,
            success:function(msg){ 
                $('.frmbutton_loadingnow#'+new_id).replaceWith(msg);
                var regex = /id="(\S+)"/; match=regex.exec(msg);
                $('#'+match[1]+' .frm_ipe_field_label').mouseover().click();
                var order= $('#new_fields').sortable('serialize');
                jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_update_field_order&"+order});
            }
        });
    },
    update:function(){
        var order= $('#new_fields').sortable('serialize');
        jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_update_field_order&"+order});
    }
});
if($('input[name="name"]').val() == '')
	$('input[name="name"]').focus();
}

$('#new_fields').on('click', '.frm_req_field', frm_mark_required)

if($('#frm_adv_info').length || $('.frm_field_list').length){
	$('#frm_adv_info').before('<div id="frm_position_ele"></div>');

	$('.frm_code_list a').addClass('frm_noallow');

	$(document).on('focusin focusout', 'form input, form textarea, .wpbody-content', function(e){
		if(e.type=='focusin') var id=$(this).attr('id'); else var id=''; frmToggleAllowedShortcodes(id,e.type);
	});
	$('#postbox-container-1').on('mousedown', '#frm_adv_info a, .frm_field_list a', function(e){e.preventDefault();});

	if(typeof(tinymce)=='object'){  
		DOM=tinymce.DOM; 
		if(typeof(DOM.events) !='undefined'){
			DOM.events.add( DOM.select('.wp-editor-wrap'), 'mouseover', function(e){
				if($('*:focus').length>0)return;
				if(this.id)frmToggleAllowedShortcodes(this.id.slice(3,-5),'focusin');});
			DOM.events.add( DOM.select('.wp-editor-wrap'), 'mouseout', function(e){
				if($('*:focus').length>0)return;
				if(this.id)frmToggleAllowedShortcodes(this.id.slice(3,-5),'focusin');});
		}else{
			$('#frm_dyncontent').on('mouseover mouseout', '.wp-editor-wrap', function(e){
	    		if($('*:focus').length>0)return; 
	    		if(this.id)frmToggleAllowedShortcodes(this.id.slice(3,-5),'focusin');
			});
		}
	}
	
	$('.hide_dyncontent,#entry_select_container,#date_select_container').hide();
	frm_show_count($("input[name='show_count']:checked").val());
	frm_show_loc($('#insert_loc').val());
}

if($('.hide_editable').length>0){
$('.hide_editable, .hide_ar, .hide_save_draft').hide();

if( $('#save_draft').is(':checked')) $('.hide_save_draft').show();
$('#save_draft').change(function(){if( $(this).is(':checked')) $('.hide_save_draft').fadeIn('slow'); else $('.hide_save_draft').fadeOut('slow');});

if( $('#editable').is(':checked')) $('.hide_editable').show();
$('#editable').change(function(){if( $(this).is(':checked')) $('.hide_editable').fadeIn('slow'); else $('.hide_editable').fadeOut('slow');});

if( $('#auto_responder').is(':checked')) $('.hide_ar').show();

$('.hide_logged_in, .hide_single_entry').frmInvisible();
if( $('#logged_in').is(':checked')) $('.hide_logged_in').frmVisible();
$('#logged_in').change(function(){if( $(this).is(':checked')) $('.hide_logged_in').frmVisible(); else $('.hide_logged_in').frmInvisible();});

if( $('#single_entry').is(':checked')) $('.hide_single_entry').frmVisible();
$('#single_entry').change(function(){if( $(this).is(':checked')) $('.hide_single_entry').frmVisible(); else $('.hide_single_entry').frmInvisible();});
}

if($('.widget-top').length){
if($.isFunction($.fn.on)){ $(document).on('click', '.widget-top', frmClickWidget); }
else{ $('.widget-top').live('click', frmClickWidget); }
$('.widget-top,a.widget-action').click(function(){ $(this).closest('div.widget').siblings().children('.widget-inside').slideUp('fast');});
}

if($('.frm_ipe_form_desc').length){
var form_id=$('input[name="id"]').val();
$('.frm_form_builder form:first').submit(function(){$('.inplace_field').blur();})

$('.frm_ipe_form_key').editInPlace({
url:ajaxurl,params:"action=frm_form_key_in_place_edit&form_id="+form_id,
show_buttons:"true",value_required:"true",
save_button: '<a class="inplace_save save button button-small">'+frm_admin_js.ok+'</a>',
cancel_button:'<a class="inplace_cancel cancel">'+frm_admin_js.cancel+'</a>',
bg_over:"#fffbcc",bg_out:"#fffbcc"
});

$('.frm_ipe_form_desc').editInPlace({
url:ajaxurl,params:"action=frm_form_desc_in_place_edit&form_id="+form_id,
field_type:"textarea",textarea_rows:3,textarea_cols:60,default_text:frm_admin_js.desc,
show_buttons:"true",
save_button: '<a class="inplace_save save button button-small">'+frm_admin_js.ok+'</a>',
cancel_button:'<a class="inplace_cancel cancel">'+frm_admin_js.cancel+'</a>',
});

$('#new_fields').on('keypress', '.frm_ipe_field_label, .frm_ipe_field_option, .frm_ipe_field_option_select, .frm_ipe_field_option_key', frmBlurField);
$('#new_fields').on('mouseenter', '.frm_ipe_field_option, .frm_ipe_field_option_select, .frm_ipe_field_option_key', frmSetIPEOpts);
$('#new_fields').on('mouseenter', '.frm_ipe_field_key', frmSetIPEKey);
$('#new_fields').on('mouseenter', '.frm_ipe_field_label', frmSetIPELabel);
$('#new_fields').on('mouseenter', '.frm_ipe_field_desc', frmSetIPEDesc);

$('select[name^="item_meta"], textarea[name^="item_meta"]').css('float','left');
$('input[name^="item_meta"]').not(':radio, :checkbox').css('float','left');

if($('.frm_field_loading').length){
var load_field_id=$('.frm_field_loading').first().attr('id').replace('frm_field_id_', '');
frmLoadField(load_field_id);
}
}

// tabs
$('.frm-category-tabs a').click(function(){
	var t = $(this).attr('href');
	if(typeof(t)!='undefined'){
		frmClickTab(t, $(this));
	}
	return false;
});

$('.item-list-form').submit(function(){
if($('#bulkaction').val()=='delete'){return confirm('Are you sure you want to delete each of the selected items below?');}
});

if($('#frm_tooltip').length==0){$('#wpfooter,#footer').prepend('<div id="frm_tooltip" class="frm_tooltip">&nbsp;</div>');}

$("select[name='frm_theme_selector'] option").each(function(){
$(this).hover(function(){$('#frm_show_cal').removeClass().addClass($(this).attr('id'));},'');
});

$('.frm_reset_style').click(function(){
	jQuery.ajax({
		type:'POST',url:ajaxurl,
	    data:'action=frm_settings_reset',
		success:function(errObj){
			errObj=errObj.replace(/^\s+|\s+$/g,'');
			if(errObj.indexOf('{') === 0)
				var errObj=jQuery.parseJSON(errObj);
			for (var key in errObj){
				$('input[name="frm_'+key+'"], select[name="frm_'+key+'"]').val(errObj[key]);
			}
			$('select[name="frm_theme_selector"]').val(errObj['theme_css']).change();
			$('#frm_submit_style, #frm_auto_width').prop('checked', false); //checkboxes
			$('#frm_fieldset').change();
		}
	});
});

$("select[name='frm_theme_selector']").change(function(){
if($(this).val() == -1){
	var themeName=-1;
	var css=-1;
}else{
	var css='https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/themes/'+$(this).val()+'/jquery-ui.css';
	var themeName=$("select[name='frm_theme_selector'] option[value='"+$(this).val()+"']").text();
}
frmUpdateUICSS(css);
$('input[name="frm_theme_css"]').val($(this).val()); $('input[name="frm_theme_name"]').val(themeName);
return false;
});

jQuery('.field_type_list > li').draggable({connectToSortable:'#new_fields',cursor:'move',helper:'clone',revert:'invalid',delay:10});
jQuery('ul.field_type_list, .field_type_list li, ul.frm_code_list, .frm_code_list li, .frm_code_list li a, #frm_adv_info #category-tabs li, #frm_adv_info #category-tabs li a').disableSelection();

$('#post_settings').on('change', 'select.frm_single_post_field', frmCheckDupPost);
$('#new_fields').on('mouseenter mouseleave', '.frm_single_option', frmHoverVis);
$('#new_fields').on('click', 'li.ui-state-default', frmClickVis);
$('.frm_form_builder').on('keyup', 'input[name^="item_meta"], textarea[name^="item_meta"]', frmTriggerDefaults);
$('.frm_form_builder').on('change', 'select[name^="item_meta"]', frmTriggerDefaults);
$(document).on('mouseenter mouseleave', '.frm_help', frmShowTooltip);

$('.frm_select_box').click(function(){this.select();});
$('.frm_select_box').focus(function(){this.select();});

jQuery('#frm_single_entry_type').change(function(){
if(jQuery('#frm_single_entry_type option:selected').val()=="cookie"){jQuery('#frm_cookie_expiration').fadeIn('slow');}
else{jQuery('#frm_cookie_expiration').fadeOut('slow');}
});

jQuery('#single_entry').change(function(){
if(jQuery('#single_entry').is(':checked') && jQuery('#frm_single_entry_type option:selected').val()=='cookie'){jQuery('#frm_cookie_expiration').fadeIn('slow');}
else{jQuery('#frm_cookie_expiration').fadeOut('slow');}
});

if($(".frm_exclude_cat_list .frm_catlevel_2").length>0){
$('.frm_exclude_cat_list').each(function(){
	var frm_lev=$(this).find('.frm_catlevel_2');
	if(frm_lev.length) $(this).find('.check_lev1_label, .check_lev2_label').show();
	var frm_lev=$(this).find('.frm_catlevel_3'); if(frm_lev.length>0) $(this).find('.check_lev3_label').show();
	var frm_lev=$(this).find('.frm_catlevel_4'); if(frm_lev.length>0) $(this).find('.check_lev4_label').show();
});
}

$('a.edit-frm_shortcode').click(function() {
	if ($('#frm_shortcodediv').is(":hidden")) {
		$('#frm_shortcodediv').slideDown('fast', function(){frmSetMenuOffset()});
		$(this).hide();
	}
	return false;
});

$('.cancel-frm_shortcode', '#frm_shortcodediv').click(function() {
	$('#frm_shortcodediv').slideUp('fast', function(){frmSetMenuOffset()});
	$('#frm_shortcodediv').siblings('a.edit-frm_shortcode').show();
	return false;
});
});

function frmClickTab(t, link){
	var c = t.replace('#', '.');
	var pro=jQuery('#taxonomy-linkcategory .frm-category-tabs li').length > 2;
	link.closest('li').addClass('tabs active').siblings('li').removeClass('tabs active');
	if(link.closest('div').find('.tabs-panel').length>0) link.closest('div').children('.tabs-panel').hide();
	else{link.closest('div.inside').find('.tabs-panel, .hide_with_tabs').hide();
	if(link.closest('ul').hasClass('frm-form-setting-tabs')){
		if(t=='#html_settings'){
			if(pro){
				jQuery('#taxonomy-linkcategory .frm-category-tabs li').hide();
				jQuery('#frm_html_tab').show();
			}
			jQuery('#frm_html_tags_tab').click();
		}else if(jQuery('#frm_html_tags_tab').is(':visible')){
			if(pro){jQuery('#taxonomy-linkcategory .frm-category-tabs li').show();jQuery('#frm_html_tab').hide();}
			jQuery('#frm_insert_fields_tab').click();
		}
	}}
	jQuery(t).show();
	jQuery(c).show();
	
	if(jQuery('.frm_form_settings').length){
		jQuery('.frm_form_settings').attr('action', '?page=formidable&frm_action=settings&id='+jQuery('.frm_form_settings input[name="id"]').val()+'&t='+t.replace('#', ''));
	}else{
		jQuery('.frm_settings_form').attr('action', '?page=formidable-settings&t='+t.replace('#', ''));
	}
}

function frmSettingsTab(tab, id){
	var t = jQuery('.'+id+'_settings');
	if(jQuery(t).length){
		tab.parent().addClass('active').siblings('li').removeClass('active');
		tab.closest('div.inside').children('.tabs-panel').hide();
		jQuery(t).show();
	}
	return false;
}

function frmLoadField(field_id){
	if(jQuery('#frm_field_id_'+field_id).next('.frm_field_loading').length > 0){
		var next_id=jQuery('#frm_field_id_'+field_id).next('.frm_field_loading').attr('id').replace('frm_field_id_', '');
		setTimeout(function(){frmLoadField(next_id);}, 400);
	}
	var f=jQuery('#frm_field_id_'+field_id+' .frm_hidden_fdata').html();
	jQuery.ajax({
		type:"POST",url:ajaxurl,
		data:{action:'frm_load_field',field_id:field_id,field:f},
		success:function(html){
			jQuery('#frm_field_id_'+field_id).replaceWith(html);
		}
	});
}

function frmSubmitBuild(b){
	var p=jQuery(b).val();
	jQuery(b).val(frm_admin_js.saving);
	jQuery(b).nextAll('.frm-loading-img').css('visibility', 'visible');
	var form=jQuery('#frm_build_form');
	var v=JSON.stringify(form.serializeArray());
	jQuery('#frm_compact_fields').val(v);
	jQuery.ajax({
		type:"POST",url:ajaxurl,
	    data:{action:'frm_save_form','frm_compact_fields':v},
	    success:function(errObj){
			jQuery(b).val(frm_admin_js.saved);
			jQuery(b).nextAll('.frm-loading-img').css('visibility', 'hidden');
			setTimeout(function(){jQuery(b).fadeOut('slow', function(){jQuery(b).val(p);jQuery(b).show();});}, 2000);
		},
		error:function(html){jQuery('#frm_js_build_form').submit();}
	});
}

function frmSubmitNoAjax(b){
	var p=jQuery(b).val();
	jQuery(b).val(frm_admin_js.saving);
	jQuery(b).nextAll('.frm-loading-img').css('visibility', 'visible');
	var form=jQuery('#frm_build_form');
	jQuery('#frm_compact_fields').val(JSON.stringify(form.serializeArray()));
	jQuery('#frm_js_build_form').submit();
}

function frmClickWidget(){
if(jQuery(this).hasClass('widget-action')) return;
if(jQuery(this).parents().hasClass('frm_35_trigger')) return;
inside=jQuery(this).closest('div.widget').children('.widget-inside');
if(inside.is(':hidden')){inside.slideDown('fast');}else{inside.slideUp('fast');}
}

function frmBlurField(e){
if(e.which == 13){
	jQuery('.inplace_field').blur();return false;
}
}
function frmTriggerDefaults(){
var n=jQuery(this).attr('name');
if(typeof(n)=='undefined') return false;
var n=n.substring(10,n.length-1);
frmShowDefaults(n,jQuery(this).val());	
}

function frmCheckUniqueOpt(id,html,text){
	jQuery('#'+id).replaceWith('<label id="'+id+'" class="'+ jQuery('#'+id).attr('class')+'">'+html+'</label>');
	if(id.indexOf('field_key_') === 0){
		var a=id.split('-');
		jQuery.each(jQuery('label[id^="'+a[0]+'"]'), function(k,v){
			var c=false;
			if(!c && jQuery(v).attr('id') != id && jQuery(v).html() == text){
				var c=true;
				alert('Saved values cannot be identical.');
			}
		});
	}
}

function frmSetIPEKey(){
jQuery(this).editInPlace({
	show_buttons:"true",value_required:"true",
	save_button: '<a class="inplace_save save button button-small">'+frm_admin_js.ok+'</a>',
	cancel_button:'<a class="inplace_cancel cancel">'+frm_admin_js.cancel+'</a>',
	bg_over:"#fffbcc",bg_out:"#fffbcc",
	callback:function(x,text){jQuery(this).next('input').val(text);return text;}
});
}

function frmSetIPELabel(){
jQuery(this).editInPlace({
	url:ajaxurl,params:'action=frm_field_name_in_place_edit',
	value_required:"true"
});
}

function frmSetIPEDesc(){ 
jQuery(this).editInPlace({
	url:ajaxurl,params:'action=frm_field_desc_in_place_edit',
	default_text:frm_admin_js.desc,
	field_type:'textarea',textarea_rows:3
});
}

function frmSetIPEOpts(){
jQuery(this).editInPlace({
	default_text:frm_admin_js.blank,
	callback:function(d,text){
		var id=jQuery(this).attr('id');
		jQuery.ajax({
			type:"POST",url:ajaxurl,
			data:{action:'frm_field_option_ipe',update_value:text,element_id:id},
			success:function(html){frmCheckUniqueOpt(id,html,text);}
		});
	}
});
}

function frmUpdateOpts(field_id,opts){
	jQuery('#frm_field_'+field_id+'_opts').html('').addClass('frm-loading-img');
	jQuery.ajax({
		type:"POST",url:ajaxurl,
		data:{action:'frm_import_options',field_id:field_id,opts:opts},
		success:function(html){jQuery('#frm_field_'+field_id+'_opts').html(html).removeClass('frm-loading-img');
		if(jQuery('select[name="item_meta['+field_id+']"]').length>0){
			var o=opts.replace(/\s\s*$/,'').split("\n");
			var sel='';
		    for (var i=0;i<o.length;i++){sel +='<option value="'+o[i]+'">'+o[i]+'</option>';}
		    jQuery('select[name="item_meta['+field_id+']"]').html(sel);
		}
		}
	});	
}

function frm_remove_tag(html_tag){jQuery(html_tag).remove();}

function frmToggleLogic(id){
$ele = jQuery('#'+id);
$ele.fadeOut('slow'); $ele.next('.frm_logic_rows').fadeIn('slow');
}
function frmToggleDiv(div){jQuery(div).fadeToggle('fast');}
function frm_show_div(div,value,show_if,class_id){
if(value==show_if) jQuery(class_id+div).fadeIn('slow'); else jQuery(class_id+div).fadeOut('slow');
}
function frm_select_item_checkbox(checked){if(!checked){jQuery(".select-all-item-action-checkboxes").removeAttr("checked");}}

function frmCheckAll(checked,n){
if(checked){jQuery("input[name^='"+n+"']").attr("checked","checked");}
else{jQuery("input[name^='"+n+"']").removeAttr("checked");}
}

function frmCheckAllLevel(checked,n,level){
var $kids=jQuery(".frm_catlevel_"+level).children(".frm_checkbox").children('label');
if(checked){$kids.children("input[name^='"+n+"']").attr("checked","checked");}
else{$kids.children("input[name^='"+n+"']").removeAttr("checked");}	
}

function frmAddNewForm(form,action){if(form !='') window.location='?page=formidable&frm_action='+action+'&id='+form;}
function frmRedirectToForm(form,action){if(form !='') window.location='?page=formidable-entries&frm_action='+action+'&form='+form;}
function frmRedirectToDisplay(form,action){if(form !='') window.location='?page=formidable-entry-templates&frm_action='+action+'&form='+form;}

function frm_add_logic_row(id,form_id){
jQuery.ajax({
    type:"POST",url:ajaxurl,
    data:"action=frm_add_logic_row&form_id="+form_id+"&field_id="+id+"&meta_name="+jQuery('#frm_logic_row_'+id+' > div').size(),
    success:function(html){jQuery('#frm_logic_row_'+id).append(html);}
});
}

function frmAddFormLogicRow(id,form_id){
if(jQuery('#frm_notification_'+id+' .frm_logic_row').length>0)
	var meta_name=1+parseInt(jQuery('#frm_notification_'+id+' .frm_logic_row:last').attr('id').replace('frm_logic_'+id+'_', ''));
else var meta_name=0;
jQuery.ajax({
    type:"POST",url:ajaxurl,
    data:"action=frm_add_form_logic_row&form_id="+form_id+"&email_id="+id+"&meta_name="+meta_name,
    success:function(html){jQuery('#frm_logic_row_'+id).append(html);}
});
}

function frmGetFieldValues(f,cur,r,t,n){
if(f){
    jQuery.ajax({
        type:"POST",url:ajaxurl,
        data:"action=frm_get_field_values&current_field="+cur+"&field_id="+f+'&name='+n+'&t='+t,
        success:function(msg){jQuery("#frm_show_selected_values_"+cur+'_'+r).html(msg);} 
    });
}
}

function add_frm_field_link(form_id,field_type){
jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_insert_field&form_id="+form_id+"&field="+field_type,
success:function(msg){jQuery('#new_fields').append(msg); jQuery('#new_fields li:last .frm_ipe_field_label').mouseover().click();}
});
};

function frm_duplicate_field(field_id){
jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_duplicate_field&field_id="+field_id,
success:function(msg){jQuery('#new_fields').append(msg);}
});
};

function frmToggleMultSel(val,field_id){
if(val=='select') jQuery('#frm_multiple_cont_'+field_id).fadeIn('fast');
else jQuery('#frm_multiple_cont_'+field_id).fadeOut('fast');
}

function frm_mark_required(){
	var thisid=jQuery(this).attr('id').replace('frm_', '');
	var field_id=thisid.replace('req_field_', '');
	if(jQuery(this).attr('id').indexOf('frm_') >= 0){
		//checkbox was clicked
		var checked=jQuery(this).is(':checked');
	}else{
		//link was clicked
		var checked=(jQuery('#frm_'+thisid).is(':checked')) ? false : true;
	}
	
    if(checked){
		var atitle='Click to Mark as Not Required';
		jQuery('.frm_required_details'+field_id).fadeIn('fast').closest('.frm_validation_msg').fadeIn('fast');
	}else{
		var atitle='Click to Mark as Required';
		var v=jQuery('.frm_required_details'+field_id).fadeOut('fast').closest('.frm_validation_box').children(':not(.frm_required_details'+field_id+'):visible').length;
		if(v==0)
			jQuery('.frm_required_details'+field_id).closest('.frm_validation_msg').fadeOut('fast');
	}
    jQuery('#'+thisid).removeClass('frm_required0 frm_required1').addClass('frm_required'+(checked ? 1 : 0)).attr('title', atitle);
	jQuery('#frm_'+thisid).prop('checked', checked);
};

function frmMarkUnique(field_id){
    var thisid='uniq_field_'+field_id;
    if(jQuery('#frm_'+thisid).is(':checked')){
		jQuery('.frm_unique_details'+field_id).fadeIn('fast').closest('.frm_validation_msg').fadeIn('fast');
	}else{
		var v=jQuery('.frm_unique_details'+field_id).fadeOut('fast').closest('.frm_validation_box').children(':not(.frm_unique_details'+field_id+'):visible').length;
		if(v==0)
			jQuery('.frm_unique_details'+field_id).closest('.frm_validation_msg').fadeOut('fast');
	}
};

function frmSeparateValue(field_id){
	jQuery('.field_'+field_id+'_option_key').toggle();
	jQuery('.field_'+field_id+'_option').toggleClass('frm_with_key');
	jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_update_ajax_option&field="+field_id+"&separate_value=1"});
}

function frmShowDefaults(n,fval){
	if(fval){jQuery('#frm_clear_on_focus_'+n+',#frm_clear_on_focus_'+n+' a').css('visibility','visible').fadeIn('slow');}
	else{jQuery('#frm_clear_on_focus_'+n+',#frm_clear_on_focus_'+n+' a').css('visibility','visible').fadeOut('slow');}
}

function frm_clear_on_focus(field_id, active){
    var thisid='clear_field_'+field_id;
    if (active=='1'){var switch_to='0';var new_class='frm_inactive_icon';var t=frm_admin_js.no_clear_default;}
    else{var switch_to='1';var new_class='';var t=frm_admin_js.clear_default;}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_clear_on_focus('+field_id+','+switch_to+')" class="'+new_class +' frm_action_icon frm_reload_icon" id="'+thisid+'" title="'+t+'"></a>');
    jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_update_ajax_option&field="+field_id+"&clear_on_focus="+switch_to});
};

function frm_default_blank(field_id,active){
    var thisid='default_blank_'+field_id;
    if(active=='1'){var switch_to='0';var new_class='frm_inactive_icon'; var t=frm_admin_js.valid_default;}
	else{var switch_to='1';var new_class=''; var t=frm_admin_js.no_valid_default;}
    jQuery('#'+thisid).replaceWith('<a href="javascript:frm_default_blank('+field_id+','+switch_to+')" class="'+new_class+' frm_action_icon frm_error_icon" id="'+thisid+'" title="'+t+'"></a>');
    jQuery.ajax({type:"POST",url:ajaxurl,data:"action=frm_update_ajax_option&field="+field_id+"&default_blank="+switch_to});
};

function frm_add_field_option(field_id,table){
	var data = {action:'frm_add_field_option',field_id:field_id,t:table};
	jQuery.post(ajaxurl,data,function(msg){
		jQuery('#frm_field_'+field_id+'_opts').append(msg);
		if(table=='row'){ jQuery('#frm-grid-'+field_id+' tr:last').after(msg);}
	});
};

function frm_delete_field_option(field_id, opt_key){
    jQuery.ajax({type:"POST",url:ajaxurl,
        data:"action=frm_delete_field_option&field_id="+field_id+"&opt_key="+opt_key,
        success:function(msg){ jQuery('#frm_delete_field_'+field_id+'-'+opt_key+'_container').fadeOut("slow");}
    });
};

function frm_delete_field(field_id){ 
    if(confirm("Are you sure you want to delete this field and all data associated with it?")){
	jQuery.ajax({
        type:"POST",url:ajaxurl,
        data:"action=frm_delete_field&field_id="+field_id,
        success:function(msg){jQuery("#frm_field_id_"+field_id).fadeOut("slow");}
    });
    }
};

function frmHoverVis(e){
if(e.type=='mouseenter'){
	jQuery(this).children('.frm_single_show_hover').show(); jQuery(this).children('.frm_single_visible_hover').css('visibility','visible');
}else{
	jQuery(this).children('.frm_single_show_hover').hide(); jQuery(this).children('.frm_single_visible_hover').css('visibility','hidden');
}
}

function frm_field_hover(show, field_id){
	var html_id = '#frm_field_id_'+field_id;
	if(show){jQuery(html_id).children('.frm-show-hover').css('visibility','visible');}
	else{if(!jQuery(html_id).is('.selected')){jQuery(html_id).children('.frm-show-hover').css('visibility','hidden');}}
}

function frmClickVis(e){
	if(jQuery(this).hasClass('selected')) return;
	jQuery('.frm-show-hover').css('visibility','hidden'); jQuery(this).children('.frm-show-hover').css('visibility','visible');
	jQuery('.frm-show-click').hide(); jQuery(this).find('.frm-show-click').show();
	var i=jQuery(this).find('input[name^="item_meta"], select[name^="item_meta"], textarea[name^="item_meta"]')[0];
	if(jQuery(i).val()) jQuery(this).find('.frm_default_val_icons').show().css('visibility', 'visible');
	else jQuery(this).find('.frm_default_val_icons').hide().css('visibility', 'hidden');
	jQuery('li.ui-state-default.selected').removeClass('selected'); jQuery(this).addClass('selected');
	if(!jQuery(e.target).is('.inplace_field, .frm_ipe_field_label, .frm_ipe_field_desc, .frm_ipe_field_option, .frm_ipe_field_option_key')){ jQuery('.inplace_field').blur();}
}

function frmAddEmailList(form_id){
	var len=jQuery('.frm_not_email_subject:last').attr('id').replace('email_subject_', '');
    jQuery.ajax({
        type:"POST",url:ajaxurl,
        data:"action=frm_add_email_list&list_id="+(parseInt(len)+1)+"&form_id="+form_id,
        success:function(html){jQuery('#frm_email_add_button').before(html);jQuery('.notification_settings').fadeIn('slow');}
    });
}

function frmRemoveEmailList(id){
    jQuery('#frm_notification_'+id).fadeOut('slow').replaceWith('');
}

function frmCheckCustomEmail(value,id,key){
if(value=='custom'){jQuery('#cust_'+id+'_'+key).css('visibility','visible'); jQuery('#frm_cust_reply_container_'+key).show();}
else{
jQuery('#cust_'+id+'_'+key).css('visibility','hidden');
if(id=='reply_to') var a='reply_to_name'; else var a='reply_to';
if(jQuery('#cust_'+a+'_'+key).css('visibility')=='hidden') jQuery('#frm_cust_reply_container_'+key).hide();	
}
}

function frmSetMenuOffset(){ 
	var fields = jQuery('#postbox-container-1 .frm_field_list');
	if(fields.length){
		var offset=283;
	}else{
		var fields = jQuery('#frm_adv_info');
		if(fields.length==0) return;
		var offset=455;
	}
	
	var currentOffset = document.documentElement.scrollTop || document.body.scrollTop; // body for Safari
	if(currentOffset == 0){ fields.removeAttr('style'); return;}
	if(jQuery('#frm_position_ele').length>0){ 
		var eleOffset=jQuery('#frm_position_ele').offset();
		var offset=eleOffset.top;
	}
	var desiredOffset = offset + 2 - currentOffset;
	if (desiredOffset < 35) desiredOffset = 35;
	//if (desiredOffset != parseInt(header.style.top)) 
		fields.attr('style', 'top:'+desiredOffset + 'px;');
}

function frmDisplayFormSelected(form_id){
    if (form_id == '') return;
    jQuery.ajax({type:"POST",url:ajaxurl,
        data:"action=frm_get_cd_tags_box&form_id="+form_id,
        success:function(html){ jQuery('#frm_adv_info .categorydiv').html(html);}
    });
    jQuery.ajax({type:"POST",url:ajaxurl,
        data:"action=frm_get_entry_select&form_id="+form_id,
        success:function(html){ jQuery('#entry_select_container').html(html);}
    });
    jQuery.ajax({type:"POST",url:ajaxurl,
        data:"action=frm_get_date_field_select&form_id="+form_id,
        success:function(html){ jQuery('#date_field_id').html(html);}
    });
};

function frmInsertFieldCode(element,variable){
	if(typeof(element)=='object'){
		var element_id=element.closest('div').attr('class').split(' ')[1];
		if(element.hasClass('frm_noallow')) return;
	}else{var element_id=element;}

	if(!element_id) var rich=true;
	else var rich=jQuery('#wp-'+element_id+'-wrap.wp-editor-wrap').length > 0;

	if(element_id.substring(0,11)=='frm_classes')
		variable=variable+' ';
	else
		variable='['+variable+']';
	if(rich){
		wpActiveEditor=element_id;
		send_to_editor(variable);
		return;
	}
	var content_box=jQuery('#'+element_id);
	if(!content_box)
		return false;
		
	if(content_box.hasClass('frm_not_email_to')) var variable=', '+variable;
	if(variable=='[default-html]' || variable=='[default-plain]'){
		var p=0;
		if(variable=='[default-plain]') var p=1;
		jQuery.ajax({type:"POST",url:ajaxurl,
			data:"action=frm_get_default_html&form_id="+jQuery('input[name="id"]').val()+'&plain_text='+p,
		    success:function(msg){frmInsertContent(content_box,msg);} 
		});
	}else{
		frmInsertContent(content_box,variable);
	}
	return false;
}

function frmInsertContent(content_box,variable){
	if(document.selection){content_box[0].focus();document.selection.createRange().text=variable;
	}else if(content_box[0].selectionStart){obj=content_box[0];var e=obj.selectionEnd;obj.value=obj.value.substr(0,obj.selectionStart)+variable+obj.value.substr(obj.selectionEnd,obj.value.length);
		var s=e+variable.length;obj.focus();obj.setSelectionRange(s,s);
	}else{content_box.val(variable+content_box.val());}
	content_box.keyup(); //trigger change
}

function frmToggleAllowedShortcodes(id,f){
	if(typeof(id)=='undefined') var id='';
	var c=id;
	if(id !=='' && jQuery('#'+id).attr('class') && id !== 'wpbody-content' && id !== 'content' && id !== 'dyncontent' && id != 'success_msg'){
		var d=jQuery('#'+id).attr('class').split(' ')[0];
		if(d=='frm_long_input' || typeof(d)=='undefined') var d='';
		else var id=jQuery.trim(d);
		var c=c+' '+d;
	}
  	jQuery('#frm-insert-fields-box,#frm-conditionals,#frm-adv-info-tab,#frm-html-tags,#frm-layout-classes,#frm-dynamic-values').removeClass().addClass('tabs-panel '+c);
  	var a=['content','wpbody-content','dyncontent','success_url','success_msg','edit_msg','frm_dyncontent','frm_not_email_message',
'frm_not_email_subject'];
  	var b=['before_content','after_content','frm_not_email_to','after_html','before_html','submit_html','field_custom_html',
'dyn_default_value', 'frm_classes'];
  	if(jQuery.inArray(id, a) >= 0){
    	jQuery('.frm_code_list a').removeClass('frm_noallow').addClass('frm_allow');
		jQuery('.frm_code_list a.hide_'+id).addClass('frm_noallow').removeClass('frm_allow');
  	}else if(jQuery.inArray(id, b) >= 0){
    	jQuery('.frm_code_list a').not('.show_'+id).addClass('frm_noallow').removeClass('frm_allow');
    	jQuery('.frm_code_list a.show_'+id).removeClass('frm_noallow').addClass('frm_allow');
  	}else{
		jQuery('.frm_code_list a').addClass('frm_noallow').removeClass('frm_allow');
  	}

	//Automatically select a tab
	if(id=='dyn_default_value'){
		jQuery('#frm_dynamic_values_tab').click();
	}else if(id=='frm_classes'){
		jQuery('#frm_layout_classes_tab').click();
	}else if(jQuery('.frm_form_builder').length && 
		(f=='focusin' || jQuery('#frm-dynamic-values').is(':visible') || jQuery('#frm-layout-classes').is(':visible'))){
		jQuery('#frm_insert_fields_tab').click();
	}
}

function frmToggleKeyID(switch_to){
	jQuery('.frm_code_list .frmids, .frm_code_list .frmkeys').hide();
	jQuery('.frm_code_list .'+switch_to).show();
	jQuery('.frmids, .frmkeys').removeClass('current');
	jQuery('.'+switch_to).addClass('current');
}

function frm_add_postmeta_row(id){
var meta_name=frmGetMetaValue('frm_postmeta_', jQuery('#frm_postmeta_rows > div').size());
jQuery.ajax({
    type:"POST",url:ajaxurl,
    data:"action=frm_add_postmeta_row&form_id="+id+"&meta_name="+meta_name,
    success:function(html){jQuery('#frm_postmeta_rows').append(html);}
});
}

function frm_add_posttax_row(id){
var post_type=jQuery('select[name="options[post_type]"]').val();
var meta_name=frmGetMetaValue('frm_posttax_', jQuery('#frm_posttax_rows > div').size());
jQuery.ajax({
    type:"POST",url:ajaxurl,
    data:"action=frm_add_posttax_row&form_id="+id+"&post_type="+post_type+"&meta_name="+meta_name,
    success:function(html){jQuery('#frm_posttax_rows').append(html);}
});
}

function frm_insert_where_options(value,where_key){
	jQuery.ajax({
		type:"POST",url:ajaxurl,
		data:"action=frm_add_where_options&where_key="+where_key+"&field_id="+value,
		success: function(html){jQuery('#where_field_options_'+where_key).html(html);}
	}); 
}

function frm_add_where_row(){
	var form_id=jQuery('#form_id').val();
	if(jQuery('#frm_where_options div:last').length>0)
    	var l=jQuery('#frm_where_options div:last').attr('id').replace('frm_where_field_', '');
	else
    	var l=0;
	jQuery.ajax({type:"POST",url:ajaxurl,
		data:"action=frm_add_where_row&form_id="+form_id+"&where_key="+(parseInt(l)+1),
		success:function(html){jQuery('#frm_where_options').append(html);}
	});
}

function frm_show_loc(val){
	if(val=='none') jQuery('#post_select_container').hide();
	else jQuery('#post_select_container').show();
}

function frm_show_count(value){
	if(value=='dynamic' || value=='calendar'){ jQuery('.hide_dyncontent').show();}
	else jQuery('.hide_dyncontent').hide();       
	if(value=='one'){jQuery('#entry_select_container').show();jQuery('.limit_container').hide();}
	else{jQuery("#entry_select_container").hide();jQuery('.limit_container').show();}
	if(value=='calendar'){jQuery('#date_select_container').show();jQuery('.limit_container').hide();}
	else{jQuery('#date_select_container').hide();}
}

function frmGetFieldSelection(form_id,field_id){ 
    if(form_id){
    jQuery.ajax({type:"POST",url:ajaxurl,
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

function frmCheckDupPost(){
jQuery('select.frm_single_post_field').removeAttr('style');
var t=jQuery(this);
var v=t.val();
if(v=='' || v=='checkbox') return false;
jQuery('select.frm_single_post_field').each(function(){
if(jQuery(this).val() == v && jQuery(this).attr('name')!=t.attr('name')){
	jQuery(this).css('border-color', 'red');t.val('');
	alert('Oops. You have already used that field.');
	return false;
}
});
}

//function to append a new theme stylesheet with the new style changes
function frmUpdateUICSS(locStr){
	if(locStr == -1){
		jQuery('link.ui-theme').remove();
		return false;
	}
	var cssLink = jQuery('<link href="'+locStr+'" type="text/css" rel="Stylesheet" class="ui-theme" />');
	jQuery('head').append(cssLink);
	
	if( jQuery('link.ui-theme').size() > 3)
		jQuery('link.ui-theme:first').remove();
}

function frmUpdateCSS(locStr){
	jQuery("head").append('<link href="'+ ajaxurl +'?action=frmpro_css&amp;'+ locStr +'" type="text/css" rel="Stylesheet" class="frm-custom-theme"/>');
	if( jQuery("link.frm-custom-theme").size() > 3){
		jQuery("link.frm-custom-theme:first").remove();
	}
}

function frm_import_templates(thisid){
    jQuery('#'+thisid).replaceWith('<img id="' + thisid + '" src="'+ frm_js.images_url +'/wpspin_light.gif" alt="'+ frm_js.loading +'" />');
    jQuery.ajax({
		type:"POST",url:ajaxurl,
		data:{'action':'frm_forms_import','path':jQuery('#frm_template_path').val()},
        success:function(msg){ 
			jQuery('#'+thisid).replaceWith(frm_admin_js.templates_updated);
		}
    });
}

function frmImportCsv(formID){
	if(typeof(__FRMURLVARS)!='undefined') var urlVars=__FRMURLVARS;
	else urlVars='';
	
    jQuery('#frm_import_link').replaceWith('<img src="'+ frm_js.images_url +'/wpspin_light.gif" alt="'+ frm_js.loading +'" />');
    jQuery.ajax({
		type:"POST",url:ajaxurl,
		data:"action=frm_import_csv&frm_skip_cookie=1"+urlVars,
    success:function(count){
        if(parseInt(count) > 0){
			jQuery("#frm_import_message .frm_message").html('The next 250 of the remaining '+count+' entries are importing.<br/> If your browser doesn&#8217;t start loading the next set automatically, click this button: <a id="frm_import_link"  class="button-secondary" href="javascript:frmImportCsv('+formID+')">Import Now</a>');
            location.href = "?page="+frm_admin_js.get_page+"&frm_action=import&step=import"+urlVars;
        }else{ 
            jQuery("#frm_import_message").fadeOut("slow");
            location.href = "?page=formidable-entries&frm_action=list&form="+formID;
        }
    }
    });
}

function frmSetPosClass(value){
if(value=='none') value='top';
jQuery('.frm_pos_container').removeClass('frm_top_container frm_left_container frm_right_container').addClass('frm_'+value+'_container');    
}

function frmGetMetaValue(id, meta_name){
    if(jQuery('#'+id+meta_name).length>0) var new_meta=frmGetMetaValue(id,meta_name+1);
    else var new_meta=meta_name;
    return new_meta;
}

function frmShowTooltip(e){
	if(e.type === 'mouseenter'){
	   	frm_title=jQuery(this).attr('title');jQuery(this).removeAttr('title');jQuery('#frm_tooltip').html(frm_title).fadeIn('fast');
	}else{
		jQuery('#frm_tooltip').fadeOut('fast');jQuery(this).attr('title',frm_title);
	}
}

function frm_install_now(){ 
	jQuery('#frm_install_link').replaceWith('<img src="'+ frm_js.images_url +'/wpspin_light.gif" alt="'+ frm_js.loading +'" />');
	jQuery.ajax({
		type:"POST",url:ajaxurl,data:"action=frm_install",
		success:function(msg){jQuery("#frm_install_message").fadeOut("slow");}
	});
}

function frm_uninstall_now(){ 
if(confirm(frm_admin_js.confirm_uninstall)){
    jQuery('.frm_uninstall a').replaceWith('<img src="'+ frm_js.images_url +'/wpspin_light.gif" alt="'+ frm_js.loading +'" />');
    jQuery.ajax({
		type:"POST",url:ajaxurl,data:"action=frm_uninstall",
    	success:function(msg){jQuery(".frm_uninstall").fadeOut("slow");}
    });
}
}

function frm_show_auth_form(){
jQuery('#pro_cred_form,.frm_pro_installed').toggle();
}

function frm_deauthorize(){
if(!confirm(frm_admin_js.deauthorize))
	return;
jQuery('#frm_deauthorize_link').replaceWith('<img src="'+ frm_js.images_url +'/wpspin_light.gif" alt="'+ frm_js.loading +'" id="frm_deauthorize_link" />');
jQuery.ajax({type:'POST',url:ajaxurl,data:'action=frm_deauthorize',
success:function(msg){jQuery('#frm_deauthorize_link').fadeOut('slow'); frm_show_auth_form();}
});
};