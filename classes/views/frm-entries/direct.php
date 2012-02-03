<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?></title>
<?php wp_head() ?>
<style type="text/css">
.frm_forms.with_frm_style{max-width:750px;}
</style>
</head>
<body>
<?php echo FrmEntriesController::show_form($form->id, '', true, true) ?>
<?php wp_footer(); ?>
</body>
</html>