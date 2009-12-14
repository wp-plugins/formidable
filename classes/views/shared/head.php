<link type="text/css" href="<?php echo FRM_URL; ?>/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="Stylesheet" />
<link rel="stylesheet" href="<?php echo FRM_URL; ?>/css/<?php echo $css_file; ?>" type="text/css" media="screen,projection" />
<?php 
if (isset($js_file)){ 
    if (is_array($js_file)){
        foreach ($js_file as $file)
            echo '<script type="text/javascript" src="'.FRM_URL.'/js/'. $file .'"></script>';
    }else{?>
<script type="text/javascript" src="<?php echo FRM_URL; ?>/js/<?php echo $js_file; ?>"></script>
<?php 
    }
}
?>