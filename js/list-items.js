jQuery(document).ready(function(){
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

   jQuery('.toggle_container').hide(); 
   jQuery('.trigger').toggle(
       	function(){jQuery(this).addClass("active");}, 
       	function(){jQuery(this).removeClass("active");}
   );
   jQuery('.trigger').click(function(){jQuery(this).next(".toggle_container").slideToggle("slow");});

jQuery('.ui-icon-trash.frm_delete_field_option').hide();
jQuery('.frm_single_option').hover(
  function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").show(); jQuery(this).children(".frm_spacer").hide();},
  function(){jQuery(this).children(".ui-icon-trash.frm_delete_field_option").hide(); jQuery(this).children(".frm_spacer").show();}
);

jQuery('.edit_form_item .ui-icon-trash').hide();
jQuery('.edit_form_item .ui-icon-arrowthick-2-n-s').hide();
jQuery('.edit_form_item .postbox').hide();
jQuery('li.ui-state-default').hover(
    function(){
		jQuery(this).children(".ui-icon-trash").show();
		jQuery(this).children(".ui-icon-arrowthick-2-n-s").show(); 
		jQuery(this).children(".ui-accordion-header").show();
	},
    function(){
		jQuery(this).children(".ui-icon-trash").hide();
		jQuery(this).children(".ui-icon-arrowthick-2-n-s").hide(); 
		jQuery(this).children(".ui-accordion-header").hide();
		jQuery(this).children(".ui-accordion-header.ui-state-active").show();
	}
);
});