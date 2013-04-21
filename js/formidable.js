jQuery(document).ready(function($){
var trigger=$('.frm_blank_field').closest('.frm_toggle_container').prev('.frm_trigger');if(trigger)frmToggleSection(trigger);
});

function frmToggleSection($sec){
$sec.next('.frm_toggle_container').slideToggle(200);
if($sec.hasClass('active')){
	$sec.removeClass('active'),$sec.children('.ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e'); 
	$sec.children('.ui-icon-triangle-1-e').removeClass('ui-icon-triangle-1-s');
}else{
	$sec.addClass("active"), $sec.children('.ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
	$sec.children('.ui-icon-triangle-1-s').removeClass('ui-icon-triangle-1-e');
}
}

function frmCheckParents(id){ 
var $chk=jQuery('#'+id);var ischecked=$chk.is(":checked");
if(!ischecked) return;
$chk.parent().parent().siblings().children("label").children("input").each(function(){
	var b= this.checked;ischecked=ischecked || b;
});
frmCheckParentNodes(ischecked, $chk);
}
function frmCheckParentNodes(b,$obj){$prt=frmFindParentObj($obj);if($prt.length !=0){$prt[0].checked=b;frmCheckParentNodes(b,$prt);}}
function frmFindParentObj($obj){return $obj.parent().parent().parent().prev().children("input");}

function frmClearDefault(default_value,thefield){
var default_value=default_value.replace(/(\n|\r\n)/g, '\r');var this_val=thefield.value.replace(/(\n|\r\n)/g, '\r');
if(this_val==default_value){thefield.value='';jQuery(thefield).removeClass('frm_default');}
}
function frmReplaceDefault(default_value,thefield){
var default_value=default_value.replace(/(\n|\r\n)/g, '\r');
if(thefield.value==''){thefield.value=default_value;jQuery(thefield).addClass('frm_default');}
}

function frmCheckDependent(selected,field_id){
if(typeof(__FRMRULES)!='undefined') var rules=__FRMRULES;
if(typeof(__FRMURL)!='undefined') var ajax_url=__FRMURL;
if(typeof(rules)=='undefined') return;
			
rules=rules[field_id];
if(typeof(rules)=='undefined') return;
	
var this_opts=new Array();
for(var i=0;i<rules.length;i++){
    var rule=rules[i];
    if(typeof(rule)!='undefined'){
        for(var j=0;j<rule.Conditions.length;j++){
			var c=rule.Conditions[j];
			c.HideField=rule.Setting.FieldName;
			c.MatchType=rule.MatchType;
			c.Show=rule.Show;
            this_opts.push(c);
        }
    }
}
var show_fields=new Array();var hide_later=new Array();	
var len=this_opts.length;
for(i=0; i<len; i++){
  (function(i){
	var f=this_opts[i];	
	if(typeof(show_fields[f.HideField])=='undefined') show_fields[f.HideField]=new Array();
		
	/*if(f.MatchType=='any' && frmInArray(true, show_fields[f.HideField])){
		if(f.Show=='show'){jQuery('#frm_field_'+f.HideField+'_container').fadeIn('slow');}
		else{jQuery('#frm_field_'+f.HideField+'_container').fadeOut('slow');}
		return;
	}*/

	if(f.FieldName!=field_id || typeof(selected)=='undefined'){
		var prevSel=selected;
		if(f.Type=='radio' || f.Type=='data-radio')
			selected=jQuery("input[name='item_meta["+f.FieldName+"]']:checked, input[type='hidden'][name='item_meta["+f.FieldName+"]']").val();
		else if(f.Type=='select' || f.Type=='data-select')
			selected=jQuery("select[name='item_meta["+f.FieldName+"]'], input[type='hidden'][name='item_meta["+f.FieldName+"]']").val();
		else if(f.Type !='checkbox' && f.Type !='data-checkbox')
			selected=jQuery("input[name='item_meta["+f.FieldName+"]']").val();
	}

	if(typeof(selected)=='undefined'){
		selected=jQuery("input[type=hidden][name='item_meta["+f.FieldName+"]']").val();
		if(typeof(selected)=='undefined') selected='';
	}
    if(f.Type=='checkbox'){
        show_fields[f.HideField][i]=false;
        jQuery("input[name='item_meta["+f.FieldName+"][]']:checked, input[type='hidden'][name='item_meta["+f.FieldName+"][]']").each(function(){
			var match=frmOperators(f.Condition,f.Value,jQuery(this).val());
			if(show_fields[f.HideField][i]==false && match)
				show_fields[f.HideField][i]=true;
		});
    }else if(f.Type=='data-radio'){
		if(typeof(f.DataType)=='undefined' || f.DataType=='' || f.DataType=='data'){
	        if(selected==''){	
				show_fields[f.HideField][i]=false;
				jQuery('#frm_field_'+f.HideField+'_container').fadeOut('slow');
				jQuery('#frm_data_field_'+f.HideField+'_container').html('');
			}else{
				if(typeof(f.DataType)=='undefined') show_fields[f.HideField][i]=frmOperators(f.Condition,f.Value,selected);	
				else show_fields[f.HideField][i]={'funcName':'frmGetData','f':f,'sel':selected};
			}
		}else{
			if(selected==''){show_fields[f.HideField][i]=false;}
            else{show_fields[f.HideField][i]={'funcName':'frmGetDataOpts','f':f,'sel':selected};}
		}
    }else if(f.Type=='data-checkbox'){
		var checked_vals=new Array();
		jQuery("input[name='item_meta["+f.FieldName+"][]']:checked, input[type='hidden'][name='item_meta["+f.FieldName+"][]']").each(function(){checked_vals.push(jQuery(this).val());});
		if(typeof(f.DataType)=='undefined' || f.DataType=='' || f.DataType=='data'){
	        if(checked_vals.length==0){
				show_fields[f.HideField][i]=false;
				jQuery('#frm_field_'+f.HideField+'_container').fadeOut('slow');
				jQuery('#frm_data_field_'+f.HideField+'_container').html('');
			}else{
				show_fields[f.HideField][i]=true;
				jQuery('#frm_data_field_'+f.HideField+'_container').html('');
				frmGetData(f,checked_vals,ajax_url,1);
				//jQuery.each(checked_vals, function(ckey,cval){frmGetData(f,cval,ajax_url,1); });
			}
		}else{
	        if(checked_vals.length==0){show_fields[f.HideField][i]=false;}
			else{show_fields[f.HideField][i]={'funcName':'frmGetDataOpts','f':f,'sel':checked_vals};}
        }
    }else if(f.Type=='data-select' && typeof(f.LinkedField)!='undefined'){
		if(f.DataType=='' || f.DataType=='data'){
            if(selected==''){show_fields[f.HideField][i]=false; jQuery('#frm_data_field_'+f.HideField+'_container').html('');}
			else if(selected && jQuery.isArray(selected)){
				show_fields[f.HideField][i]=true;
				jQuery('#frm_data_field_'+f.HideField+'_container').html('');
				frmGetData(f,selected,ajax_url,1);
			}else{show_fields[f.HideField][i]={'funcName':'frmGetData','f':f,'sel':selected};}
        }else{
            if(selected==''){show_fields[f.HideField][i]=false;}
            else{show_fields[f.HideField][i]={'funcName':'frmGetDataOpts','f':f,'sel':selected};}
        }
    }else{
		if(typeof(f.Value)=='undefined' && f.Type.indexOf('data') === 0){
			if(selected=='') f.Value='1';
			else f.Value=selected;
			show_fields[f.HideField][i]=frmOperators(f.Condition,f.Value,selected);
			f.Value=undefined;
		}else{
			show_fields[f.HideField][i]=frmOperators(f.Condition,f.Value,selected);
		}
    }
	if(f.FieldName!=field_id) selected=prevSel;
	if(f.MatchType=='any'){
		if(show_fields[f.HideField][i]!=false){
			if(f.Show=='show'){
				if(show_fields[f.HideField][i]!=true){frmShowField(show_fields[f.HideField][i],ajax_url,f.FieldName);}
				else{jQuery('#frm_field_'+f.HideField+'_container').show();}
			}else{jQuery('#frm_field_'+f.HideField+'_container').hide();}
		}else{
			hide_later.push({'result':show_fields[f.HideField][i],'show':f.Show,'match':'any','fname':f.FieldName,'fkey':f.HideField});
		}
	}else if(f.MatchType=='all'){
		hide_later.push({'result':show_fields[f.HideField][i],'show':f.Show,'match':'all','fname':f.FieldName,'fkey':f.HideField});
	}
	
	if(i==(len-1)){
		jQuery.each(hide_later, function(hkey,hvalue){ 
			if(typeof(hvalue)!='undefined' && typeof(hvalue.result)!='undefined'){
				if((hvalue.match=='any' && !frmInArray(true, show_fields[hvalue.fkey])) || (hvalue.match=='all' && frmInArray(false, show_fields[hvalue.fkey]))){
					if(hvalue.show=='show'){jQuery('#frm_field_'+hvalue.fkey+'_container:hidden').hide(); jQuery('#frm_field_'+hvalue.fkey+'_container').hide();}
					else{ jQuery('#frm_field_'+hvalue.fkey+'_container').show();}
				}else{
					if(hvalue.show=='show'){ jQuery('#frm_field_'+hvalue.fkey+'_container').show();}
					else{jQuery('#frm_field_'+hvalue.fkey+'_container:hidden').hide(); jQuery('#frm_field_'+hvalue.fkey+'_container').hide();}
				}
				if(typeof(hvalue.result)!=false && typeof(hvalue.result)!=true) frmShowField(hvalue.result,ajax_url,hvalue.fname);
				delete hide_later[hkey];
			}
		});
	}
  })(i);
}
}

function frmOperators(op,a,b){
	if(typeof(b)=='undefined') var b='';
	if(jQuery.isArray(b) && jQuery.inArray(a,b) > -1) var b=a;
	if(String(a).search(/^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/) != -1){var a=parseFloat(a);var b=parseFloat(b);}
	var operators = {
	    '==': function(c,d){ return c == d },
		'!=': function(c,d){ return c != d },
	    '<': function(c,d){ return c > d },
		'>': function(c,d){ return c < d },
		'LIKE': function(c,d){return d.indexOf(c) != -1},
		'not LIKE': function(c,d){return d.indexOf(c) == -1}
	};
	return operators[op](a,b);
}

function frmInArray(needle, haystack){
	if(typeof(haystack)=='undefined')
		return false;
    var len=haystack.length;
    for(var i=0; i<len; i++){ if(haystack[i] == needle) return true;}
    return false;
}

function frmShowField(funcInfo,ajax_url,field_id){
if(funcInfo.funcName=='frmGetDataOpts'){frmGetDataOpts(funcInfo.f,funcInfo.sel,ajax_url,field_id);}
else if(funcInfo.funcName=='frmGetData'){frmGetData(funcInfo.f,funcInfo.sel,ajax_url,0);}
}

function frmGetData(f,selected,ajax_url,append){
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=fields&frm_action=ajax_get_data&entry_id="+selected+"&field_id="+f.LinkedField+"&current_field="+f.HideField,
		success:function(html){
			if(html!='') jQuery('#frm_field_'+f.HideField+'_container').show(); 
			if(append){jQuery('#frm_data_field_'+f.HideField+'_container').append(html);}
			else{
				jQuery('#frm_data_field_'+f.HideField+'_container').html(html);
				var val=jQuery('#frm_data_field_'+f.HideField+'_container').children('input').val();
				if(html=='' || val=='') jQuery('#frm_field_'+f.HideField+'_container').hide();
				frmCheckDependent(selected,f.HideField);
			}
			return true;
		}
	});
}

function frmGetDataOpts(f,selected,ajax_url,field_id){
	var prev=new Array();
	if(f.DataType=='checkbox' || f.DataType=='radio'){
		jQuery("input[name='item_meta["+f.HideField+"][]']:checked").each(function(){prev.push(jQuery(this).val());});
	}else if(f.DataType=='select'){
		//TODO: get previous value for multi-selects and set it!
		if(jQuery("select[name='item_meta["+f.HideField+"][]']").length>0) var mult='[]'; else var mult='';
		prev.push(jQuery("select[name='item_meta["+f.HideField+"]"+mult+"']").val());
	}else{prev.push(jQuery("input[name='item_meta["+f.HideField+"]']").val());}
	if(prev.length==0) var prev='';
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=fields&frm_action=ajax_data_options&hide_field="+field_id+"&entry_id="+selected+"&selected_field_id="+f.LinkedField+"&field_id="+f.HideField,
		success:function(html){
			if(html=='') jQuery('#frm_field_'+f.HideField+'_container').hide(); 
			else if(f.MatchType!='all') jQuery('#frm_field_'+f.HideField+'_container').show();
			jQuery('#frm_data_field_'+f.HideField+'_container').html(html);
			if(html!='' && prev!=''){
				jQuery.each(prev, function(ckey,cval){
					if(f.DataType=='checkbox'){jQuery("#field_"+f.HideField+"-"+cval).attr('checked','checked');}
					else if(f.DataType=='select'){
						jQuery("select[name='item_meta["+f.HideField+"]"+mult+"']").val(cval);}
					else{jQuery("input[name='item_meta["+f.HideField+"]']").val(cval);}
				});
			}
			frmCheckDependent('',f.HideField);
		}
	});
}

function frmGetFormErrors(object,ajax_url){
	if(typeof(__FRMURL)!='undefined') var ajax_url=__FRMURL;
	jQuery(object).find('input[type="submit"]').attr('disabled','disabled');
	jQuery.ajax({
		type:"POST",url:ajax_url,
	    data:jQuery(object).serialize()+"&controller=entries&_ajax_nonce=1",
	    success:function(errObj){
			errObj=errObj.replace(/^\s+|\s+$/g,'');
			if(errObj.indexOf('{') === 0)
				var errObj=jQuery.parseJSON(errObj);
			jQuery(object).find('input[type="submit"]').removeAttr('disabled');
	    	if(errObj=='' || !errObj || errObj=='0' || (typeof(errObj) != 'object' && errObj.indexOf('<!DOCTYPE') === 0)){
	            if(jQuery("#frm_loading").length){
					var file_val=jQuery(object).find('input[type=file]').val();
					if(typeof(file_val)!='undefined' && file_val!=''){window.setTimeout(function(){jQuery("#frm_loading").fadeIn('slow');},2000);}
				}
				if(jQuery(object).find('#recaptcha_area').length)
					jQuery(object).find('#recaptcha_area').replaceWith('');
	            object.submit();
			}else if(typeof(errObj) != 'object'){
				var jump=jQuery(object).closest('#frm_form_'+jQuery(object).find('input[name="form_id"]').val()+'_container');
				var newPos=jump.offset();
				jump.replaceWith(errObj);
				var cOff=document.documentElement.scrollTop || document.body.scrollTop;
				if(newPos && newPos.top > 0 && cOff > newPos.top) window.scrollTo(newPos.left,newPos.top);
	        }else{
	            //show errors
				var cont_submit=true;
	            jQuery('.form-field').removeClass('frm_blank_field');
	            jQuery('.form-field .frm_error').replaceWith('');
				var jump=''; var show_captcha=false;
	            for (var key in errObj){
					if(jQuery(object).find('#frm_field_'+key+'_container').length && jQuery('#frm_field_'+key+'_container').is(":visible")){
						cont_submit=false;
						if(jump==''){
							jump='#frm_field_'+key+'_container';
							var newPos=jQuery(object).find(jump).offset();
							var cOff=document.documentElement.scrollTop || document.body.scrollTop;
							if(newPos && cOff > newPos.top) window.scrollTo(newPos.left,newPos.top);
						}
						if(jQuery(object).find('#frm_field_'+key+'_container #recaptcha_area').length){
							var show_captcha=true;
							Recaptcha.reload();
						}
						jQuery(object).find('#frm_field_'+key+'_container').addClass('frm_blank_field');
						if(typeof(frmThemeOverride_frmPlaceError) == 'function'){frmThemeOverride_frmPlaceError(key,errObj);}
						else{jQuery(object).find('#frm_field_'+key+'_container').append('<div class="frm_error">'+errObj[key]+'</div>');}
					}
				}
				if(show_captcha!=true) jQuery(object).find('#recaptcha_area').replaceWith('');
				if(cont_submit) object.submit();
	        }
	    },
		error:function(html){jQuery(object).find('input[type="submit"]').removeAttr('disabled');object.submit();}
	});
}

function frmGetEntryToEdit(form_id,entry_id,post_id,ajax_url){
jQuery.ajax({
	type:"POST",url:ajax_url,
	data:"controller=entries&frm_action=edit_entry_ajax&id="+form_id+"&post_id="+post_id+"entry_id="+entry_id,
	success:function(form){jQuery('#frm_form_'+form_id+'_container').replaceWith(form);}
});
}

function frmEditEntry(entry_id,ajax_url,prefix,post_id,form_id,cancel,hclass){
	var label=jQuery('#frm_edit_'+entry_id).text();
	var orig=jQuery('#'+prefix+entry_id).html();
	jQuery('#'+prefix+entry_id).html('<span class="frm-loading-img" id="'+prefix+entry_id+'"></span><div class="frm_orig_content" style="display:none">'+orig+'</div>');
	jQuery.ajax({
		type:"POST",url:ajax_url,dataType:"html",
		data:"controller=entries&frm_action=edit_entry_ajax&post_id="+post_id+"&entry_id="+entry_id+"&id="+form_id,
		success:function(html){
			jQuery('#'+prefix+entry_id).children('.frm-loading-img').replaceWith(html);
			jQuery('#frm_edit_'+entry_id).replaceWith('<span id="frm_edit_'+entry_id+'"><a onclick="frmCancelEdit('+entry_id+',\''+prefix+'\',\''+label+'\',\''+ajax_url+'\','+post_id+','+form_id+',\''+hclass+'\')" class="'+hclass+'">'+cancel+'</a></span>');
		}
	});
}

function frmCancelEdit(entry_id,prefix,label,ajax_url,post_id,form_id,hclass){
	var cancel=jQuery('#frm_edit_'+entry_id).text();
	jQuery('#'+prefix+entry_id).children('.frm_forms').replaceWith('');
	jQuery('#'+prefix+entry_id).children('.frm_orig_content').fadeIn('slow').removeClass('frm_orig_content');
	jQuery('#frm_edit_'+entry_id).replaceWith('<a id="frm_edit_'+entry_id+'" class="frm_edit_link '+hclass+'" href="javascript:frmEditEntry('+entry_id+',\''+ajax_url+'\',\''+prefix+'\','+post_id+','+form_id+',\''+cancel+'\',\''+hclass+'\')">'+label+'</a>');
}

function frmUpdateField(entry_id,field_id,value,message,ajax_url,num){
	jQuery('#frm_update_field_'+entry_id+'_'+field_id).html('<span class="frm-loading-img"></span>');
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=entries&frm_action=update_field_ajax&entry_id="+entry_id+"&field_id="+field_id+"&value="+value,
		success:function(html){
			if(message == '')
				jQuery('#frm_update_field_'+entry_id+'_'+field_id+'_'+num).fadeOut('slow');
			else
				jQuery('#frm_update_field_'+entry_id+'_'+field_id+'_'+num).replaceWith(message);
		}
	});
}

function frmDeleteEntry(entry_id,ajax_url,prefix){	
	jQuery('#frm_delete_'+entry_id).replaceWith('<span class="frm-loading-img" id="frm_delete_'+entry_id+'"></span>');
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=entries&frm_action=destroy&entry="+entry_id,
		success:function(html){
			if(html == 'success')
				jQuery('#'+prefix+entry_id).fadeOut('slow');
			else
				jQuery('#frm_delete_'+entry_id).replaceWith(html);
			
		}
	});
}

function frmRemoveDiv(id){
jQuery(id).fadeOut('slow').replaceWith('');	
}

function frmNextUpload(obj,id,remove){
obj.wrap('<div class="frm_file_names frm_uploaded_files">'); 
obj.after(obj.attr('value')+' <a href="#" onclick="frmClearFile(jQuery(this))">'+remove+'</a>');
obj.hide(); jQuery('#frm_field_'+id+'_container').append('<input name="file'+id+'[]" type="file" onchange="frmNextUpload(jQuery(this),'+id+',\''+remove+'\')"/>');
}

function frmClearFile(file){
file.parent('.frm_file_names').replaceWith('');
return false;
}