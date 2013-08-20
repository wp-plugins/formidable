<?php
/*
Plugin Name: Formidable
Description: Quickly and easily create drag-and-drop forms
Version: 1.07.01
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


define('FRM_PATH', WP_PLUGIN_DIR.'/'. dirname( plugin_basename( __FILE__ ) ));
define('FRM_VIEWS_PATH', FRM_PATH.'/classes/views');
$frm_models_path = FRM_PATH .'/classes/models';
$frm_helpers_path = FRM_PATH .'/classes/helpers';
$frm_controllers_path = FRM_PATH .'/classes/controllers';
define('FRM_TEMPLATES_PATH', FRM_PATH.'/classes/templates');

global $frm_siteurl;
$frm_siteurl = site_url();
if(is_ssl() and (!preg_match('/^https:\/\/.*\..*$/', $frm_siteurl) or !preg_match('/^https:\/\/.*\..*$/', WP_PLUGIN_URL))){
    $frm_siteurl = str_replace('http://', 'https://', $frm_siteurl);
    define('FRM_URL', str_replace('http://', 'https://', WP_PLUGIN_URL.'/formidable'));
}else
    define('FRM_URL', WP_PLUGIN_URL.'/formidable');  //plugins_url('/formidable')
    
define('FRM_SCRIPT_URL', $frm_siteurl . (is_admin() ? '/wp-admin' : '') .'/index.php?plugin=formidable');
define('FRM_IMAGES_URL', FRM_URL.'/images');

load_plugin_textdomain('formidable', false, 'formidable/languages/' );

require_once($frm_models_path .'/FrmSettings.php');

// Check for WPMU installation
if (!defined ('IS_WPMU')){
    global $wpmu_version;
    $is_wpmu = ((function_exists('is_multisite') and is_multisite()) or $wpmu_version) ? 1 : 0;
    define('IS_WPMU', $is_wpmu);
}

global $frm_version, $frm_db_version;
$frm_version = '1.07.01';
$frm_db_version = 9;

global $frm_ajax_url;
$frm_ajax_url = admin_url('admin-ajax.php');

global $frm_load_css, $frm_forms_loaded, $frm_css_loaded, $frm_saved_entries;
$frm_load_css = $frm_css_loaded = false;
$frm_forms_loaded = $frm_saved_entries = array();

require_once($frm_helpers_path .'/FrmAppHelper.php');
$frm_app_helper = new FrmAppHelper();

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

// Instansiate Models
require_once($frm_models_path .'/FrmDb.php');  
require_once($frm_models_path .'/FrmField.php');
require_once($frm_models_path .'/FrmForm.php');
require_once($frm_models_path .'/FrmEntry.php');
require_once($frm_models_path .'/FrmEntryMeta.php');
require_once($frm_models_path .'/FrmNotification.php');
//include_once($frm_models_path .'/FrmUpdate.php');
unset($frm_models_path);

global $frmdb;
global $frm_field;
global $frm_form;
global $frm_entry;
global $frm_entry_meta;
global $frm_notification;

$frmdb              = new FrmDb();
$frm_field          = new FrmField();
$frm_form           = new FrmForm();
$frm_entry          = new FrmEntry();
$frm_entry_meta     = new FrmEntryMeta();
$frm_notification   = new FrmNotification();
//$frm_update         = new FrmUpdate();


// Instansiate Controllers
require_once($frm_controllers_path .'/FrmApiController.php');
require_once($frm_controllers_path .'/FrmAppController.php');
require_once($frm_controllers_path .'/FrmFieldsController.php');
require_once($frm_controllers_path .'/FrmFormsController.php');
require_once($frm_controllers_path .'/FrmEntriesController.php');
require_once($frm_controllers_path .'/FrmSettingsController.php');
require_once($frm_controllers_path .'/FrmStatisticsController.php');
require_once($frm_controllers_path .'/FrmUpdatesController.php');
unset($frm_controllers_path);

$obj = new FrmAppController();
$obj = new FrmEntriesController();
$obj = new FrmFieldsController();
$obj = new FrmFormsController();
$obj = new FrmSettingsController();
$obj = new FrmStatisticsController();
$frm_update  = new FrmUpdatesController();

// Instansiate Helpers
require_once($frm_helpers_path .'/FrmEntriesHelper.php');
require_once($frm_helpers_path .'/FrmFieldsHelper.php');
require_once($frm_helpers_path .'/FrmFormsHelper.php');
unset($frm_helpers_path);

global $frmpro_is_installed;
$frmpro_is_installed = $frm_update->pro_is_installed_and_authorized();

if($frmpro_is_installed)
  require_once(FRM_PATH .'/pro/formidable-pro.php');
    
// The number of items per page on a table
global $frm_page_size;
$frm_page_size = 20;

global $frm_sidebar_width;
$frm_sidebar_width = '';

// Register Widgets
if(class_exists('WP_Widget')){
    require_once(FRM_PATH . '/classes/widgets/FrmShowForm.php');
    add_action('widgets_init', create_function('', 'return register_widget("FrmShowForm");'));
}
