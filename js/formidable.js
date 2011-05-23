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
	var $chk = jQuery('#'+id);
	var ischecked = $chk.is(":checked");
	if(!ischecked) return;
	$chk.parent().parent().siblings().children("label").children("input").each(function(){
		var b= this.checked;ischecked=ischecked || b;
	});
	frmCheckParentNodes(ischecked, $chk);
}
function frmCheckParentNodes(b,$obj){
$prt=frmFindParentObj($obj);if($prt.length !=0){$prt[0].checked=b;frmCheckParentNodes(b,$prt);}
}
function frmFindParentObj($obj){return $obj.parent().parent().parent().prev().children("input");}

function frmClearDefault(default_value,thefield){if(thefield.value==default_value){thefield.value='';thefield.style.fontStyle='inherit';}}
function frmReplaceDefault(default_value,thefield){if(thefield.value==''){thefield.value=default_value;thefield.style.fontStyle='italic';}}

function frmCheckDependent(selected,type,field_id,opts,ajax_url){
var atts=String(opts).split('||');
if(atts.length==1){var this_opts=new Array();this_opts[0]=opts;}
else{var this_opts=atts;}
var len=this_opts.length;
for(i=0; i<len; i++){
  (function(i){
	var field_data = String(this_opts[i]).split('|');
    if(type=='checkbox'){
        var show_field=false;
        jQuery("input[name='item_meta["+field_id+"][]']:checked").each(function(){if(show_field==false && jQuery(this).val() == field_data[1]){
			show_field=true;jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow');
		}});
        if(show_field==false){jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');}
    }else if(type=='data-radio'){        
        var entry_id=jQuery("input[name='item_meta["+field_id+"]']:checked").val();
        if(entry_id==''){
			jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');
			jQuery('#frm_field_'+field_data[0]+'_container').html('');
		}else{frmGetData(field_data,entry_id,ajax_url,0);}
    }else if(type=='data-checkbox'){
		if(field_data[2]=='undefined' || field_data[2]=='' || field_data[2]=='data'){
	        var replace_it=false;
	        if(selected!=''){replace_it=frmGetData(field_data,selected,ajax_url,1);}
	        if(replace_it!=true){
				jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');
				jQuery('#frm_data_field_'+field_data[0]+'_container').html('');
			}
		}else{
			var checked_vals=new Array();
			jQuery("input[name='item_meta["+field_id+"][]']:checked").each(function(){checked_vals.push(jQuery(this).val());});
	        if(checked_vals.length==0){jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');}
			else{frmGetDataOpts(field_data,checked_vals,ajax_url,field_id);}
        }
    }else if(type=='data-select' && typeof field_data[2]!='undefined'){
		if(field_data[2]=='' || field_data[2]=='data'){
            if(selected==''){jQuery('#frm_data_field_'+field_data[0]+'_container').html('');}
            else{frmGetData(field_data,selected,ajax_url,0);}
        }else{
            if(selected==''){jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');}
            else{frmGetDataOpts(field_data,selected,ajax_url,field_id);}
        }
    }else{
        if(selected==field_data[1]) jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow');
        else jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');
    }
  })(i);
}
}

function frmGetData(field_data,selected,ajax_url,append){
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=fields&action=ajax_get_data&entry_id="+selected+"&field_id="+field_data[1],
		success:function(html){
			if(html != '') jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow'); 
			
			if(append){jQuery('#frm_data_field_'+field_data[0]+'_container').append(html);}
			else{
				jQuery('#frm_data_field_'+field_data[0]+'_container').html(html);
				if(html == '') jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');
			}
			return true;
		}
	});
}

function frmGetDataOpts(field_data,selected,ajax_url,field_id){
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=fields&action=ajax_data_options&hide_field="+field_id+"&entry_id="+selected+"&selected_field_id="+field_data[1]+"&field_id="+field_data[0],
		success:function(html){
			if(html != '') jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow'); 
			else jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow'); 
			jQuery('#frm_data_field_'+field_data[0]+'_container').html(html);
		}
	});
}

function frmGetFormErrors(object,ajax_url){
	jQuery.ajax({
		type:"POST",url:ajax_url,dataType:'json',
	    data:jQuery(object).serialize()+"&controller=entries",
	    success:function(errObj){
	    	if(errObj=='' || !errObj){
	            if(jQuery("#frm_loading").length){window.setTimeout(function(){jQuery("#frm_loading").fadeIn('slow');},2000);}
	            object.submit();
	        }else{
	            //show errors
				var cont_submit=true;
	            jQuery('.form-field').removeClass('frm_blank_field');
	            jQuery('.form-field .frm_error').replaceWith('');
				var jump='';
	            for (var key in errObj){
					if(jQuery('#frm_field_'+key+'_container').length){
						cont_submit=false;
						if(jump==''){
							jump='#frm_field_'+key+'_container';
							var new_position=jQuery(jump).offset();
							if(new_position)
								window.scrollTo(new_position.left,new_position.top);
						}
					    jQuery('#frm_field_'+key+'_container').append('<div class="frm_error">'+errObj[key]+'</div>').addClass('frm_blank_field');
					}
				}
				if(cont_submit) object.submit();
	        }
	    },
		error:function(html){object.submit();}
	});
}

function frmGetEntryToEdit(form_id,entry_id,post_id,ajax_url){
jQuery.ajax({
	type:"POST",url:ajax_url,
	data:"controller=entries&action=edit_entry_ajax&id="+form_id+"&post_id="+post_id+"entry_id="+entry_id,
	success:function(form){jQuery('#frm_form_'+form_id+'_container').replaceWith(form);}
});
}

function frmEditEntry(entry_id,ajax_url,prefix,post_id,form_id,cancel){
	var label=jQuery('#frm_edit_'+entry_id).text();
	var orig=jQuery('#'+prefix+entry_id).html();
	jQuery('#'+prefix+entry_id).html('<span class="frm-loading-img" id="frm_edit_container_'+entry_id+'"></span><div class="frm_orig_content" style="display:none">'+orig+'</div>');
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=entries&action=edit_entry_ajax&post_id="+post_id+"&entry_id="+entry_id+"&id="+form_id,
		success:function(html){
			jQuery('#'+prefix+entry_id).children('.frm-loading-img').replaceWith(html);
			jQuery('#frm_edit_'+entry_id).replaceWith('<span id="frm_edit_'+entry_id+'"><a onclick="frmCancelEdit('+entry_id+',\''+prefix+'\',\''+label+'\',\''+ajax_url+'\','+post_id+','+form_id+')">'+cancel+'</a></span>');
			
		}
	});
}

function frmCancelEdit(entry_id,prefix,label,ajax_url,post_id,form_id){
	var cancel=jQuery('#frm_edit_'+entry_id).text();
	jQuery('#'+prefix+entry_id).children('.frm_forms').replaceWith('');
	jQuery('#'+prefix+entry_id).children('.frm_orig_content').fadeIn('slow').removeClass('frm_orig_content');
	jQuery('#frm_edit_'+entry_id).replaceWith('<a id="frm_edit_'+entry_id+'" class="frm_edit_link" href="javascript:frmEditEntry('+entry_id+',\''+ajax_url+'\',\''+prefix+'\','+post_id+','+form_id+',\''+cancel+'\')">'+label+'</a>');
}

function frmDeleteEntry(entry_id,ajax_url,prefix){	
	jQuery('#frm_delete_'+entry_id).replaceWith('<span class="frm-loading-img" id="frm_delete_'+entry_id+'"></span>');
	jQuery.ajax({
		type:"POST",url:ajax_url,
		data:"controller=entries&action=destroy&entry="+entry_id,
		success:function(html){
			if(html == 'success')
				jQuery('#'+prefix+entry_id).fadeOut('slow');
			else
				jQuery('#frm_delete_'+entry_id).replaceWith(html);
			
		}
	});
}