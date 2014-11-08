<?php
/*
Plugin Name: Formidable
Description: Quickly and easily create drag-and-drop forms
Version: 2.0a
Plugin URI: http://formidablepro.com/
Author URI: http://strategy11.com
Author: Strategy11
Text Domain: formidable
*/

/*  Copyright 2010  Strategy11  (email : support@strategy11.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

global $frm_vars;
$frm_vars = array(
    'load_css' => false, 'forms_loaded' => array(),
    'created_entries'   => array(),
    'pro_is_authorized' => false,
);

function frm_forms_autoloader($class_name) {
    // Only load Frm classes here
    if ( ! preg_match('/^Frm.+$/', $class_name) ) {
        return;
    }

    $filepath = dirname(__FILE__);
    if ( preg_match('/^FrmPro.+$/', $class_name) || 'FrmUpdatesController' == $class_name ) {
        $filepath .= '/pro';
    }
    $filepath .= '/classes';

    if ( preg_match('/^.+Helper$/', $class_name) ) {
        $filepath .= '/helpers/';
    } else if ( preg_match('/^.+Controller$/', $class_name) ) {
        $filepath .= '/controllers/';
    } else {
        $filepath .= '/models/';
    }

    $filepath .= $class_name .'.php';

    if ( file_exists($filepath) ) {
        include($filepath);
    }
}

// if __autoload is active, put it on the spl_autoload stack
if ( is_array(spl_autoload_functions()) && in_array('__autoload', spl_autoload_functions()) ) {
    spl_autoload_register('__autoload');
}

// Add the autoloader
spl_autoload_register('frm_forms_autoloader');


// Instansiate Models
global $frmdb;
global $frm_field;
global $frm_form;
global $frm_entry;
global $frm_entry_meta;

$frmdb              = new FrmDb();
$frm_field          = new FrmField();
$frm_form           = new FrmForm();
$frm_entry          = new FrmEntry();
$frm_entry_meta     = new FrmEntryMeta();


// Instansiate Controllers
FrmAppController::load_hooks();
FrmEntriesController::load_hooks();
FrmFieldsController::load_hooks();
FrmFormsController::load_hooks();
FrmFormActionsController::load_hooks();
FrmStylesController::load_hooks();

if ( is_admin() ) {
    FrmSettingsController::load_hooks();
    FrmStatisticsController::load_hooks();
    FrmXMLController::load_hooks();
}


$frm_path = dirname(__FILE__);
if ( file_exists($frm_path . '/pro/formidable-pro.php') ) {
    require_once($frm_path .'/pro/formidable-pro.php');
}

include_once($frm_path .'/deprecated.php');
unset($frm_path);