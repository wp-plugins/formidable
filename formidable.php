<?php
/*
Plugin Name: Formidable
Description: Quickly and easily create drag-and-drop forms
Version: 1.07.04
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

require(dirname( __FILE__ ) .'/classes/models/FrmSettings.php');

global $frm_vars;
$frm_vars = array(
    'load_css' => false, 'forms_loaded' => array(),
    'created_entries' => array(), 'pro_is_installed' => false
);

require(dirname( __FILE__ ) .'/classes/helpers/FrmAppHelper.php');
$obj = new FrmAppHelper();

/***** SETUP SETTINGS OBJECT *****/
global $frm_settings;

$frm_settings = get_transient('frm_options');
if(!is_object($frm_settings)){
    if($frm_settings){ //workaround for W3 total cache conflict
        $frm_settings = unserialize(serialize($frm_settings));
    }else{
        $frm_settings = get_option('frm_options');

        // If unserializing didn't work
        if(!is_object($frm_settings)){
            if($frm_settings) //workaround for W3 total cache conflict
                $frm_settings = unserialize(serialize($frm_settings));
            else
                $frm_settings = new FrmSettings();
            update_option('frm_options', $frm_settings);
            set_transient('frm_options', $frm_settings);
        }
    }
}
$frm_settings->set_default_options(); // Sets defaults for unset options

$frm_path = FrmAppHelper::plugin_path();

// Instansiate Models
require($frm_path .'/classes/models/FrmDb.php');  
require($frm_path .'/classes/models/FrmField.php');
require($frm_path .'/classes/models/FrmForm.php');
require($frm_path .'/classes/models/FrmEntry.php');
require($frm_path .'/classes/models/FrmEntryMeta.php');
require($frm_path .'/classes/models/FrmNotification.php');

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
$obj = new FrmNotification();


// Instansiate Controllers
require($frm_path .'/classes/controllers/FrmAppController.php');
require($frm_path .'/classes/controllers/FrmFieldsController.php');
require($frm_path .'/classes/controllers/FrmFormsController.php');
require($frm_path .'/classes/controllers/FrmEntriesController.php');
require($frm_path .'/classes/controllers/FrmSettingsController.php');
require($frm_path .'/classes/controllers/FrmStatisticsController.php');

$obj = new FrmAppController();
$obj = new FrmEntriesController();
$obj = new FrmFieldsController();
$obj = new FrmFormsController();
$obj = new FrmSettingsController();
$obj = new FrmStatisticsController();

// Instansiate Helpers
require($frm_path .'/classes/helpers/FrmEntriesHelper.php');
require($frm_path .'/classes/helpers/FrmFieldsHelper.php');
require($frm_path .'/classes/helpers/FrmFormsHelper.php');

if(FrmAppController::pro_is_installed())
  require($frm_path .'/pro/formidable-pro.php');

// Register Widgets
if(class_exists('WP_Widget')){
    require($frm_path . '/classes/widgets/FrmShowForm.php');
    add_action('widgets_init', create_function('', 'return register_widget("FrmShowForm");'));
}

include($frm_path .'/deprecated.php');
unset($frm_path);