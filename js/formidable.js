jQuery(document).ready(function($){
window.onscroll = document.documentElement.onscroll = frmSetMenuOffset;
frmSetMenuOffset();
	
jQuery('.edit_form_item').hover(
    function(){jQuery(this).children(".form_item_actions").show();},
    function(){jQuery(this).children(".form_item_actions").hide();}
);

jQuery('.edit_item').hover(
  function(){jQuery(this).children(".item_actions").show();},
  function(){jQuery(this).children(".item_actions").hide();}
);

jQuery('.select-all-item-action-checkboxes').change(function(){
  if (jQuery(this).attr("checked")){
    jQuery(".item-action-checkbox").attr("checked","checked");
    jQuery(".select-all-item-action-checkboxes").attr("checked","checked");
  }else{
    jQuery(".item-action-checkbox").removeAttr("checked");
    jQuery(".select-all-item-action-checkboxes").removeAttr("checked");
  }
});

jQuery('.item-action-checkbox').change(function(){
  if(!jQuery(this).attr("checked")){jQuery(".select-all-item-action-checkboxes").removeAttr("checked");}
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
});

function frm_field_hover(show, field_id){
	var html_id = '#frm_field_id_'+field_id;
	if(show){jQuery(html_id).children(".frm-show-hover").show();}
	else{if(!jQuery(html_id).is('.selected')){jQuery(html_id).children(".frm-show-hover").hide();}}
}

function frmSetMenuOffset() { 
	var fields = jQuery('#frm_form_options #themeRoller');
	if (!fields) return;
	var currentOffset = document.documentElement.scrollTop || document.body.scrollTop; // body for Safari
	var desiredOffset = 340 - currentOffset;
	if (desiredOffset < 10) desiredOffset = 10;
	//if (desiredOffset != parseInt(header.style.top)) 
		fields.attr('style', 'top:'+desiredOffset + 'px;');
}
