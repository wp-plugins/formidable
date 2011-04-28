jQuery(document).ready(function($){
$('.frm_toggle_container').hide();
$('.frm_trigger').toggle(function(){ 
	$(this).addClass("active"), $(this).children('.ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
	$(this).children('.ui-icon-triangle-1-s').removeClass('ui-icon-triangle-1-e');
	},function(){
	$(this).removeClass("active"),$(this).children('.ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e'); 
	$(this).children('.ui-icon-triangle-1-e').removeClass('ui-icon-triangle-1-s');
	}); 
$('.frm_trigger').click(function(){ $(this).next(".frm_toggle_container").slideToggle("slow");});
});

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
        if(entry_id==''){jQuery('#frm_field_'+field_data[0]+'_container').html('');}
        else{jQuery.ajax({type:"POST",url:ajax_url,data:"controller=fields&action=ajax_get_data&entry_id="+entry_id+"&field_id="+field_data[1],
			success:function(html){jQuery('#frm_field_'+field_data[0]+'_container').html(html);}});
        }
    }else if(type=='data-checkbox'){
		if(field_data[2]=='undefined' || field_data[2]=='' || field_data[2]=='data'){
	        var replace_it=false;
	        jQuery("input[name='item_meta["+field_id+"][]']:checked").each(function(){
	        if(selected!=''){jQuery.ajax({type:"POST",url:ajax_url,data:"controller=fields&action=ajax_get_data&entry_id="+selected+"&field_id="+field_data[1],
				success:function(html){jQuery('#frm_data_field_'+field_data[0]+'_container').append(html);replace_it=true;}});
	        }
	        });
	        if(replace_it!=true) jQuery('#frm_data_field_'+field_data[0]+'_container').html('');
		}else{
			var checked_vals=new Array();
			jQuery("input[name='item_meta["+field_id+"][]']:checked").each(function(){checked_vals.push(jQuery(this).val());});
	        if(checked_vals.length==0){jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');}
			else{jQuery.ajax({type:"POST",url:ajax_url,
				data:"controller=fields&action=ajax_data_options&hide_field="+field_id+"&entry_id="+checked_vals+"&selected_field_id="+field_data[1]+"&field_id="+field_data[0],
				success:function(html){
					jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow'); 
					jQuery('#frm_data_field_'+field_data[0]+'_container').html(html);
				}
				});
	        }
        }
    }else if(type=='data-select' && typeof field_data[2]!='undefined'){
		if(field_data[2]=='' || field_data[2]=='data'){
            if(selected==''){jQuery('#frm_data_field_'+field_data[0]+'_container').html('');}
            else{jQuery.ajax({type:"POST",url:ajax_url,data:"controller=fields&action=ajax_get_data&entry_id="+selected+"&field_id="+field_data[1],
				success:function(html){jQuery('#frm_data_field_'+field_data[0]+'_container').html(html);}});}
        }else{
            if(selected==''){jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');}
            else{jQuery.ajax({type:"POST",url:ajax_url,data:"controller=fields&action=ajax_data_options&hide_field="+field_id+"&entry_id="+selected+"&selected_field_id="+field_data[1]+"&field_id="+field_data[0],
				success:function(html){
					jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow'); 
					jQuery('#frm_data_field_'+field_data[0]+'_container').html(html);
				}});
			};
        }
    }else{
        if(selected==field_data[1]) jQuery('#frm_field_'+field_data[0]+'_container').fadeIn('slow');
        else jQuery('#frm_field_'+field_data[0]+'_container').fadeOut('slow');
    }
  })(i);
}
}