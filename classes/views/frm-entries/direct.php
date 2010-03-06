<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

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
    <?php //wp_footer(); ?>
</body>
</html>