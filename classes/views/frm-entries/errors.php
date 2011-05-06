<?php global $frm_settings; 
if (isset($message) && $message != ''){ 
    if(is_admin()){ ?><div id="message" class="updated fade" style="padding:5px;"><?php echo $message ?></div><?php 
    }else{ 
        echo $message; 
    }
} 

if( isset($errors) && is_array($errors) && !empty($errors) ){
    global $frm_settings;
?>
<div class="<?php echo (is_admin()) ? 'error' : 'frm_error_style' ?>"> 
<?php 
if(!is_admin()){ 
    $img = apply_filters('frm_error_icon', FRM_IMAGES_URL . '/error.png');
    if($img and !empty($img)){
    ?><img src="<?php echo $img ?>" alt="" />
<?php 
    }
} 
    echo stripslashes($frm_settings->invalid_msg);
?>
</div>
<?php } ?>