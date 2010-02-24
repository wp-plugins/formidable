jQuery(document).ready(function($){
  jQuery('.form_item_actions').hide();
  jQuery('.edit_form_item').hover(
      function(){jQuery(this).children(".form_item_actions").show();},
      function(){jQuery(this).children(".form_item_actions").hide();}
  );
      
  jQuery('.item_actions').hide();
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
    if(!jQuery(this).attr("checked")){
      jQuery(".select-all-item-action-checkboxes").removeAttr("checked");
    }
  });

  jQuery('.item-list-form').submit(function(){
    if(jQuery('#bulkaction').val() == 'delete'){
      return confirm('Are you sure you want to delete each of the selected items below?');
    }
  });

jQuery('.frm_single_show_hover').hide();
jQuery('.frm_single_option').hover(
  function(){jQuery(this).children(".frm_single_show_hover").show(); jQuery(this).children(".frm_spacer").hide();},
  function(){jQuery(this).children(".frm_single_show_hover").hide(); jQuery(this).children(".frm_spacer").show();}
);

jQuery('li.ui-state-default').hover(
    function(){jQuery(this).children(".frm-show-hover").show();},
    function(){if(jQuery(this).is('.selected')){}else{ jQuery(this).children(".frm-show-hover").hide();}}
);

jQuery('li.ui-state-default').click(function(){
	$('.frm-show-click').hide(); $(this).children(".frm-show-click").show(); 
	$('.frm-show-hover').hide(); $(this).children(".frm-show-hover").show();
	$(".ui-accordion-header").hide(); $(this).children(".ui-accordion-header").show();
	$('li.ui-state-default.selected').removeClass('selected'); $(this).addClass('selected');
});
});