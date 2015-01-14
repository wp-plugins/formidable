<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<?php wp_head(); ?>
<style type="text/css">
body:before{content:normal !important;}
body{padding:25px;}
</style>
</head>
<body>
<?php echo FrmFormsController::show_form($form->id, '', true, true) ?>
<?php wp_footer(); ?>
</body>
</html>