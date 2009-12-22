<?php
/*
Plugin Name: Formidable
Description: Easily create drag-and-drop forms
Version: 1.0.06
Plugin URI: http://blog.strategy11.com/formidable-wordpress-plugin
Author URI: http://blog.strategy11.com
Author: Stephanie Wells
*/

if (!function_exists('set_current_user'))
    require_once(ABSPATH . WPINC . '/pluggable.php');
    
define('FRM_PLUGIN_TITLE','Formidable');
define('FRM_PLUGIN_NAME','formidable');
define('FRM_PATH',WP_PLUGIN_DIR.'/'.FRM_PLUGIN_NAME);
define('FRM_MODELS_PATH',FRM_PATH.'/classes/models');
define('FRM_VIEWS_PATH',FRM_PATH.'/classes/views');
define('FRM_HELPERS_PATH',FRM_PATH.'/classes/helpers');
define('FRM_CONTROLLERS_PATH',FRM_PATH.'/classes/controllers');
define('FRM_URL',WP_PLUGIN_URL.'/'.FRM_PLUGIN_NAME);
define('FRM_SCRIPT_URL', get_option('home') . '/index.php?plugin=' . FRM_PLUGIN_NAME);
define('FRM_IMAGES_URL',FRM_URL.'/images');

require_once(FRM_MODELS_PATH.'/FrmSettings.php');

global $frm_version;
$frm_version = '1.0.06';


// Check for WPMU installation
if (!defined ('IS_WPMU')){
    global $wpmu_version;
    define('IS_WPMU', ($wpmu_version) ? 1 : 0);
}

global $frm_blogurl;
global $frm_siteurl;
global $frm_blogname;
global $frm_blogdescription;

$frm_blogurl = ((get_option('home'))?get_option('home'):get_option('siteurl'));
$frm_siteurl = get_option('siteurl');
$frm_blogname = get_option('blogname');
$frm_blogdescription = get_option('blogdescription');

/***** SETUP SETTINGS OBJECT *****/
global $frm_settings;

$frm_settings = get_option('frm_options');

// If unserializing didn't work
if(!$frm_settings){
  $frm_settings = new FrmSettings();
  update_option('frm_settings',$frm_settings);
}else
  $frm_settings->set_default_options(); // Sets defaults for unset options
  
require_once(FRM_MODELS_PATH.'/FrmField.php');
require_once(FRM_MODELS_PATH.'/FrmForm.php');
require_once(FRM_MODELS_PATH.'/FrmEntry.php');
require_once(FRM_MODELS_PATH.'/FrmEntryMeta.php');
require_once(FRM_MODELS_PATH.'/FrmNotification.php');
require_once(FRM_MODELS_PATH.'/FrmUpdate.php');

global $frm_field;
global $frm_form;
global $frm_entry;
global $frm_entry_meta;
global $frm_notification;
global $frm_update;

$frm_field          = new FrmField();
$frm_form           = new FrmForm();
$frm_entry          = new FrmEntry();
$frm_entry_meta     = new FrmEntryMeta();
$frm_notification   = new FrmNotification();
$frm_update         = new FrmUpdate();


// Instansiate Controllers
require_once(FRM_CONTROLLERS_PATH . "/FrmAppController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmFieldsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmFormsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmEntriesController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmSettingsController.php");
require_once(FRM_CONTROLLERS_PATH . "/FrmStatisticsController.php");

global $frm_app_controller;
global $frm_entries_controller;
global $frm_fields_controller;
global $frm_forms_controller;
global $frm_settings_controller;

$frm_app_controller         = new FrmAppController();
$frm_entries_controller     = new FrmEntriesController();
$frm_fields_controller      = new FrmFieldsController();
$frm_forms_controller       = new FrmFormsController();
$frm_settings_controller    = new FrmSettingsController();

// Instansiate Helpers
require_once(FRM_HELPERS_PATH. "/FrmAppHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmEntriesHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmFieldsHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmFormsHelper.php");
require_once(FRM_HELPERS_PATH. "/FrmSettingsHelper.php");

global $frm_app_helper;
global $frm_fields_helper;
global $frm_settings_helper;

$frm_app_helper = new FrmAppHelper();
$frm_fields_helper = new FrmFieldsHelper();
$frm_settings_helper = new FrmSettingsHelper();

global $frmpro_is_installed;
$frmpro_is_installed = $frm_update->pro_is_installed_and_authorized();

if($frmpro_is_installed)
  require_once(FRM_PATH.'/pro/formidable-pro.php');
    
// The number of items per page on a table
global $frm_page_size;
$frm_page_size = 10;

global $frm_field_selection;

$frm_field_selection = array(
    'text' => 'Text Input (One Line)',
    'textarea' => 'Paragraph Input (Multiple Lines)',
    'checkbox' => 'Multiple Selection (Check Boxes)',
    'radio' => 'Select One (Radio)',
    'select' => 'Drop-Down (Select)'
);

global $frm_recaptcha_enabled;

$frm_recaptcha_enabled = ( in_array('wp-recaptcha/wp-recaptcha.php', get_option('active_plugins')) )?(true):(false);
if ($frm_recaptcha_enabled)
    $frm_field_selection['captcha'] = 'reCAPTCHA Field';
    
global $frm_pro_field_selection;

$frm_pro_field_selection = array(
    'divider' => 'Section Divider',
    'image' => 'Image URL', 
    //'upload' => 'File Upload',
    //'rte' => 'Rich Text Editor', 
    'phone' => 'Phone', 
    'email' => 'Email',
    'date' => 'Date', 
    //'time' => 'Time',
    'hidden' => 'Hidden Field', 
    'user_id' => 'Hidden User Id',
    'website' => 'Website',
    '10radio' => '1-10 radio',
    'data' => 'Data from Entries'
    //'multiple' => 'Multiple Select Box', //http://code.google.com/p/jquery-asmselect/
    //'title' => 'Entry Title', 
    //'key' => 'Entry Key',// (for calling entry from template) 
    //'address' => 'Address', //Address line 1, Address line 2, City, State/Providence, Postal Code, Select Country 
    //'city_selector' => 'US State/County/City selector', 
    //'full_name' => 'First and Last Name', 
    //'terms' => 'Terms of Use',// checkbox or show terms (use with Terms of use plugin)
    //'quiz' => 'Question and Answer' // for captcha alternative
);

?>