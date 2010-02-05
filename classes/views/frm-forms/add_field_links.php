<div id="frm_form_options" class="alignright">
    <?php if (!$values['is_template']){ ?>
    <div>Copy this code and paste it into your post, page or text widget: 
        <h4 class="frmcenter">[formidable id=<?php echo $id ?>]</h4>
    </div>
    <?php } ?>
    
    <ul class="frmbutton nodrag">
        <li><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="blank" >Preview Form</a></li>
    <?php global $frm_settings; if ($frm_settings->preview_page_id > 0){ ?>
        <li><a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="blank" class="frmbutton">Preview Form in Current Theme</a></li>
    <?php } ?>
    </ul>
    
    <h4>Basic Fields</h4>
    <ul class="field_type_list">
    <?php foreach ($frm_field_selection as $field_key => $field_type){ ?>
        <li class="frmbutton" id="<?php echo $field_key ?>"><a href="javascript:void(0);" class="add_frm_field_link" id="<?php echo $field_key ?>"><?php echo $field_type ?></a></li>
     <?php } ?>
     <?php if (!$frm_recaptcha_enabled && !function_exists( 'akismet_http_post' )){
            global $frm_siteurl;
            echo '<p class="howto">Hint: Download and activate <a href="'.$frm_siteurl.'/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=wp-recaptcha&amp;TB_iframe=true&amp;width=640&amp;height=593" class="thickbox onclick" title="WP-reCAPTCHA 2.9.6">WP-reCAPTCHA</a> to add a captcha to your form. Alternatively activate Akismet for captcha-free spam screening.</p>';
            } ?>
     </ul>
     
     <h4>Pro Fields <span class="howto" style="display:inline;">Coming Soon</span></h4>
     <ul class="field_type_list">
     <?php 
     if($frmpro_is_installed){  
         foreach ($frm_pro_field_selection as $field_key => $field_type){ ?>
             <li class="frmbutton" id="<?php echo $field_key ?>"><a href="javascript:void(0);" class="add_frm_field_link" id="<?php echo $field_key ?>"><?php echo $field_type ?></a></li>
    <?php }
     }else
         foreach ($frm_pro_field_selection as $field_key => $field_type) 
            echo '<li class="frmbutton">'.$field_type.'</li>';
     ?>
     </ul>
     
     <br/>
     <h3>KEY</h3>
     <ul class="ui-state-default" style="border:none; font-weight:normal">
         <li><span class="ui-icon ui-icon-star alignleft"></span> = required field</li>
         <li><span class="frm_inactive_icon ui-icon ui-icon-star alignleft"></span> = not required</li>
         <li><span><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> = clear default data on click</li>
         <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></span> = do not clear default data on click</li>
         <li><span><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> = default value will NOT pass validation</li>
          <li><span class="frm_inactive_icon"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></span> = default value will pass validation</li>
         <li><span class="ui-icon ui-icon-trash alignleft"></span> = delete field and all inputed data</li>
     </ul>

     <p class="howto">Enter or select default values into fields on this form.</p>
     <?php do_action('frm_extra_form_instructions'); ?>
</div>

<script>
jQuery(document).ready(function(){    
jQuery(".add_frm_field_link").click(function(){
    jQuery.ajax({
       type:"POST",
       url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
       data:"action=frm_insert_field&form_id=<?php echo $id; ?>&field="+this.getAttribute('id'),
       success:function(msg){jQuery('#new_fields').append(msg);}
    });
 });
 })
</script>