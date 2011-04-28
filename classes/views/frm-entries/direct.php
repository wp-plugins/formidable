<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head() ?>
</head>
<body>
    <?php require('frm-entry.php'); ?>
    <?php wp_footer(); ?>
</body>
</html>