<?php

/** Okay, this class is not a pure model -- it contains all the functions
  * necessary to successfully provide an update mechanism for FormidablePro!
  */
class FrmUpdate{
  var $plugin_name;
  var $plugin_slug;
  var $plugin_url;
  var $pro_script;
  var $pro_mothership;
  
  var $pro_cred_store;
  var $pro_auth_store;
  
  var $pro_username_label;
  var $pro_password_label;
  
  var $pro_username_str;
  var $pro_password_str;
  
  var $pro_error_message_str;
  
  var $pro_check_interval;
  var $pro_last_checked_store;
  
  var $pro_username;
  var $pro_password;
  var $pro_mothership_xmlrpc_url;

  function FrmUpdate(){
    // Where all the vitals are defined for this plugin
    $this->plugin_name          = FRM_PLUGIN_NAME.'/formidable.php';
    $this->plugin_slug          = FRM_PLUGIN_NAME;
    $this->plugin_url           = 'http://blog.strategy11.com/formidable-wordpress-plugin';
    $this->pro_script           = FRM_PATH . '/pro/'. FRM_PLUGIN_NAME .'-pro.php';
    $this->pro_mothership       = 'http://formidablepro.com';
    $this->pro_cred_store       = 'frmpro-credentials';
    $this->pro_auth_store       = 'frmpro-authorized';
    $this->pro_last_checked_store = 'frmpro_last_checked_update';
    $this->pro_username_label    = __(FRM_PLUGIN_TITLE .' Pro Username', FRM_PLUGIN_NAME);
    $this->pro_password_label    = __(FRM_PLUGIN_TITLE .' Pro Password', FRM_PLUGIN_NAME);
    $this->pro_error_message_str = __('Your '.FRM_PLUGIN_TITLE.' Pro Username or Password was Invalid', FRM_PLUGIN_NAME);
    
    // Don't modify these variables
    $this->pro_check_interval = 60*60; // Checking every hour
    $this->pro_username_str = 'proplug-username';
    $this->pro_password_str = 'proplug-password';
    $this->pro_mothership_xmlrpc_url = $this->pro_mothership . '/xmlrpc.php';
    
    // Retrieve Pro Credentials
    $creds = get_option($this->pro_cred_store);
    if($creds and is_array($creds)){
      extract($creds);
      $this->pro_username = ((isset($username) and !empty($username))?$username:'');
      $this->pro_password = ((isset($password) and !empty($password))?$password:'');

      // Plugin Update Actions -- gotta make sure the right url is used with pro ... don't want any downgrades of course
      add_action('update_option_update_plugins', array($this, 'check_for_update_now')); // for WordPress 2.7
      add_action('update_option__transient_update_plugins', array($this, 'check_for_update_now')); // for WordPress 2.8
      add_action("admin_init", array($this, 'periodically_check_for_update'));
    }
  }

  function pro_is_installed(){
    return file_exists($this->pro_script);
  }

  function pro_is_authorized($force_check=false){
    if( !empty($this->pro_username) and !empty($this->pro_password) ){
      $authorized = get_option($this->pro_auth_store);
      if(!$force_check and isset($authorized))
        return $authorized;
      else{
        $new_auth = $this->authorize_user($this->pro_username,$this->pro_password);
        update_option($this->pro_auth_store, $new_auth);
        return $new_auth;
      }
    }

    return false;
  }

  function pro_is_installed_and_authorized(){
    return ($this->pro_is_installed() and $this->pro_is_authorized());
  }

  function authorize_user($username, $password){
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if ( !$client->query( 'proplug.is_user_authorized', $username, $password ) )
      return false;

    return $client->getResponse();
  }

  function user_allowed_to_download(){
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if ( !$client->query( 'proplug.is_user_allowed_to_download', $this->pro_username, $this->pro_password, get_option('home') ) )
      return false;

    return $client->getResponse();
  }

  function pro_cred_form(){ ?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: Pro Account Information</h2>      
    <?php
    if(isset($_POST) and isset($_POST['process_cred_form']) and $_POST['process_cred_form'] == 'Y'){
      if($this->process_pro_cred_form()){
        if(!$this->pro_is_installed()){
          $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $this->plugin_name, 'upgrade-plugin_' . $this->plugin_name);

          ?>
<div id="message" class="updated fade">
<strong><?php printf(__('Your Username & Password was accepted<br/>Now you can %1$sUpgrade Automatically!%2$s', FRM_PLUGIN_NAME), "<a href=\"{$inst_install_url}\">","</a>"); ?></strong>
</div>
          <?php
        }
      }else{
        ?>
<div class="error">
  <ul>
    <li><strong><?php _e('ERROR', FRM_PLUGIN_NAME); ?></strong>: <?php echo $this->pro_error_message_str; ?></li>
  </ul>
</div>
        <?php
      }
    }

    $this->display_pro_cred_form(); ?>
</div>
    <?php    
  }

  function display_pro_cred_form(){
    // Yah, this is the view for the credentials form -- this class isn't a true model
    extract($this->get_pro_cred_form_vals());
    ?>
<form name="cred_form" method="post" action="">
  <input type="hidden" name="process_cred_form" value="Y">
  <?php wp_nonce_field('cred_form'); ?>

  <table class="form-table">
    <tr class="form-field">
      <td valign="top" width="15%"><?php echo $this->pro_username_label; ?>:</td>
      <td width="85%">
        <input type="text" name="<?php echo $this->pro_username_str; ?>" value="<?php echo $username; ?>"/>
      </td>
    </tr>
    <tr class="form-field">
      <td valign="top" width="15%"><?php echo $this->pro_password_label; ?>:</td>
      <td width="85%">
        <input type="password" name="<?php echo $this->pro_password_str; ?>" value="<?php echo $password; ?>"/>
      </td>
    </tr>
  </table>
  <p class="submit">
    <input type="submit" name="Submit" value="<?php _e('Save', FRM_PLUGIN_NAME); ?>" />
  </p>
</form>
    <?php
  }

  function process_pro_cred_form(){
    $creds = $this->get_pro_cred_form_vals();
    $user_authorized = $this->authorize_user($creds['username'], $creds['password']);

    if(!empty($user_authorized) and $user_authorized){
      update_option($this->pro_cred_store, $creds);
      update_option($this->pro_auth_store, $user_authorized);

      extract($creds);
      $this->pro_username = ((isset($username) and !empty($username))?$username:'');
      $this->pro_password = ((isset($password) and !empty($password))?$password:'');

      if(!$this->pro_is_installed())
        $this->queue_update(true);
    }

    return $user_authorized;
  }

  function get_pro_cred_form_vals(){
    $username = ((isset($_POST[$this->pro_username_str]))?$_POST[$this->pro_username_str]:$this->pro_username);
    $password = ((isset($_POST[$this->pro_password_str]))?$_POST[$this->pro_password_str]:$this->pro_password);

    return compact('username','password');
  }

  function get_download_url($version){
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if( !$client->query( 'proplug.get_encoded_download_url', $this->pro_username, $this->pro_password, $version ) )
        return false;

    return base64_decode($client->getResponse());
  }

  function get_current_version(){
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if( !$client->query( 'proplug.get_current_version' ) )
      return false;

    return $client->getResponse();
  }

  function queue_update($force=false){
    static $already_set_option, $already_set_transient;
    
    if(!is_admin())
      return;

    // Make sure this method doesn't check back with the mothership too often
    if($already_set_option or $already_set_transient)
      return;

    if($this->pro_is_authorized()){
      // If pro is authorized but not installed then we need to force an upgrade
      if(!$this->pro_is_installed())
        $force=true;

      $plugin_updates = ((function_exists('get_transient'))?get_transient("update_plugins"):get_option("update_plugins")); 

      $curr_version = $this->get_current_version();
      $installed_version = $plugin_updates->checked[$this->plugin_name];

      if( $force or ( $curr_version != $installed_version ) ){
        $download_url = $this->get_download_url($curr_version);

        if(!empty($download_url) and $download_url and $this->user_allowed_to_download()){  
          if(isset($plugin_updates->response[$this->plugin_name]))
            unset($plugin_updates->response[$this->plugin_name]);

          $plugin_updates->response[$this->plugin_name]              = new stdClass();
          $plugin_updates->response[$this->plugin_name]->id          = '0';
          $plugin_updates->response[$this->plugin_name]->slug        = $this->plugin_slug;
          $plugin_updates->response[$this->plugin_name]->new_version = $curr_version;
          $plugin_updates->response[$this->plugin_name]->url         = $this->plugin_url;
          $plugin_updates->response[$this->plugin_name]->package     = $download_url;
        }
      }else{
        if(isset($plugin_updates->response[$this->plugin_name]))
          unset($plugin_updates->response[$this->plugin_name]);
      }

      if ( function_exists('set_transient') and !$already_set_transient ){
        $already_set_transient = true;
        set_transient("update_plugins", $plugin_updates); // for WordPress 2.8+
      }

      if( !$already_set_option ){
        $already_set_option = true;
        update_option("update_plugins", $plugin_updates); // for WordPress 2.7
      }
    }
  }

  function check_for_update_now(){
    $this->queue_update();
  }

  function periodically_check_for_update(){
    $last_checked = get_option($this->pro_last_checked_store);

    if(!$last_checked or ((time() - $last_checked) >= $this->pro_check_interval)){
      $this->queue_update();
      update_option($this->pro_last_checked_store, time());
    }
  }
}
?>