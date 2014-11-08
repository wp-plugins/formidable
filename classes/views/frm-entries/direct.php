<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<?php wp_head();
if ( ! FrmAppHelper::pro_is_installed() ) { ?>
<style type="text/css">.frm_forms.with_frm_style{max-width:750px;}</style>
<?php } ?>
</head>
<body>
<div style="padding:25px;">
<?php echo FrmFormsController::show_form($form->id, '', true, true) ?>
</div>
<?php wp_footer(); ?>
</body>
</html>