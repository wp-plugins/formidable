<?php 
if (isset($css_file)){ 
    if (is_array($css_file)){
        foreach ($css_file as $file)
            echo '<link rel="Stylesheet" href="'. $file .'" type="text/css" />';
    }else{?>
<link rel="stylesheet" href="<?php echo $css_file; ?>" type="text/css" />
<?php } 
}

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