<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
    <?php global $frm_blogurl; ?>
    <?php if ($custom_style){
        $css = apply_filters('get_frm_stylesheet', FRM_URL .'/css/frm_display.css'); 
    ?>
    <link type="text/css" href="<?php echo $css; ?>" rel="Stylesheet" />
    <?php } ?>
    <?php do_action('frm_direct_link_head'); ?>
</head>
<body>
    <?php require_once('frm-entry.php'); ?>
</body>
</html>