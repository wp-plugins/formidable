<?php
/**
 * @package Formidable
 */
/*
Plugin Name: Formidable
Description: Quickly and easily create drag-and-drop forms
Version: 1.05.0b
Plugin URI: http://blog.strategy11.com/formidable-wordpress-plugin
Author URI: http://blog.strategy11.com
Author: Strategy11
*/

/*  Copyright 2010  Strategy11  (email : support@strategy11.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('FRM_PLUGIN_TITLE', 'Formidable');
define('FRM_PLUGIN_NAME', 'formidable');
define('FRM_PATH', WP_PLUGIN_DIR.'/'.FRM_PLUGIN_NAME);
define('FRM_MODELS_PATH', FRM_PATH.'/classes/models');
define('FRM_VIEWS_PATH', FRM_PATH.'/classes/views');
define('FRM_HELPERS_PATH', FRM_PATH.'/classes/helpers');
define('FRM_CONTROLLERS_PATH', FRM_PATH.'/classes/controllers');
define('FRM_TEMPLATES_PATH', FRM_PATH.'/classes/templates');

global $frm_blogurl;
$frm_blogurl = (get_option('home')) ? get_option('home') : get_option('siteurl');
if(is_ssl() and !preg_match('/^https:\/\/.*\..*$/', $frm_blogurl)){
    $frm_blogurl = str_replace('http://', 'https://', $frm_blogurl);
    define('FRM_URL',str_replace('http://', 'https://', WP_PLUGIN_URL.'/'.FRM_PLUGIN_NAME));
}else
    define('FRM_URL', WP_PLUGIN_URL.'/'.FRM_PLUGIN_NAME);  //plugins_url('/'.FRM_PLUGIN_NAME)
    
define('FRM_SCRIPT_URL', $frm_blogurl .'/index.php?plugin=' . FRM_PLUGIN_NAME);
define('FRM_IMAGES_URL', FRM_URL.'/images');

require_once(FRM_MODELS_PATH.'/FrmSettings.php');

load_plugin_textdomain('formidable', false, FRM_PLUGIN_NAME.'/languages/' );

// Check for WPMU installation
if (!defined ('IS_WPMU')){
    global $wpmu_version;
    $is_wpmu = ((function_exists('is_multisite') and is_multisite()) or $wpmu_version) ? 1 : 0;
    define('IS_WPMU', $is_wpmu);
}

global $frm_version, $frm_db_version;
$frm_version = '1.05.0';
$frm_db_version = 5;


global $frm_siteurl;
global $frm_ajax_url;
$frm_siteurl = get_option('siteurl');
$frm_ajax_url = admin_url('admin-ajax.php');

global $frm_load_css, $frm_forms_loaded, $frm_css_loaded, $frm_loaded_fields;
$frm_load_css = $frm_css_loaded = false;
$frm_forms_loaded = $frm_loaded_fields = array();

require_once(FRM_HELPERS_PATH. "/FrmAppHelper.php");
global $frm_app_helper;
$frm_app_helper = new FrmAppHelper();

/***** SETUP SETTINGS OBJECT *****/
global $frm_settings;

$frm_settings = get_option('frm_options');

// If unserializing didn't work
if(!is_object($frm_settings)){
    $frm_settings = new FrmSettings();
    update_option('frm_options',$frm_settings);
}

$frm_settings->set_default_options(); // Sets defaults for unset options


// Instansiate Models
require_once(FRM_MODELS_PATH.'/FrmDb.php');  
require_once(FRM_MODELS_PATH.'/FrmField.php');
require_once(FRM_MODELS_PATH.'/FrmForm.php');
require_once(FRM_MODELS_PATH.'/FrmEntry.php');
require_once(FRM_MODELS_PATH.'/FrmEntryMeta.php');
require_once(FRM_MODELS_PATH.'/FrmNotification.php');
require_once(FRM_MODELS_PATH.'/FrmUpdate.php');

global $frmdb;
global $frm_field;
global $frm_form;
global $frm_entry;
global $frm_entry_meta;
global $frm_notification;
global $frm_update;

$frmdb              = new FrmDb();
$frm_field          = new FrmField();
$frm_form           = new FrmForm();
$frm_entry          = new FrmEntry();
$frm_entry_meta     = new FrmEntryMeta();
$frm_notification   = new FrmNotification();
$frm_update         = new FrmUpdate();


// Instansiate Controllers
require_once(FRM_CONTROLLERS_PATH . "/FrmApiController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmAppController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmFieldsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmFormsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmEntriesController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmSettingsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmStatisticsController.php");

global $frm_api_controller;
global $frm_app_controller;
global $frm_entries_controller;
global $frm_fields_controller;
global $frm_forms_controller;
global $frm_settings_controller;

$frm_api_controller         = new FrmApiController();
$frm_app_controller         = new FrmAppController();
$frm_entries_controller     = new FrmEntriesController();
$frm_fields_controller      = new FrmFieldsController();
$frm_forms_controller       = new FrmFormsController();
$frm_settings_controller    = new FrmSettingsController();

// Instansiate Helpers
require_once(FRM_HELPERS_PATH. "/FrmEntriesHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmFieldsHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmFormsHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmSettingsHelper.php");

global $frm_fields_helper;
global $frm_settings_helper;

$frm_fields_helper = new FrmFieldsHelper();
$frm_settings_helper = new FrmSettingsHelper();

global $frmpro_is_installed;
$frmpro_is_installed = $frm_update->pro_is_installed_and_authorized();

if($frmpro_is_installed)
  require_once(FRM_PATH.'/pro/formidable-pro.php');
    
// The number of items per page on a table
global $frm_page_size;
$frm_page_size = 20;

// Register Widgets
if(class_exists('WP_Widget')){
    require_once(FRM_PATH . "/classes/widgets/FrmShowForm.php");
    add_action('widgets_init', create_function('', 'return register_widget("FrmShowForm");'));
}

?>