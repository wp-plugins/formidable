<?php global $frm_settings; 
if (isset($message) && $message != ''){ 
    if(is_admin()){ ?><div id="message" class="updated fade" style="padding:5px;"><?php } 
    echo $message; 
    if(is_admin()){ ?></div><?php } 
} 

if( isset($errors) && is_array($errors) && !empty($errors) ){ 
    global $frm_settings;
?>
<div class="frm_error_style error"> 
    <img src="<?php echo apply_filters('frm_error_icon', FRM_IMAGES_URL . '/error.png') ?>" alt="" />
    <?php echo stripslashes($frm_settings->invalid_msg) ?>
</div>
<?php } ?>