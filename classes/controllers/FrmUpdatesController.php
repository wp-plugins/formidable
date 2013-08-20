<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

// Contains all the functions necessary to provide an update mechanism for FormidablePro!

class FrmUpdatesController{
    var $plugin_nicename;
    var $plugin_name;
    var $plugin_url;
    var $pro_script;
    var $pro_mothership;

    var $pro_cred_store;
    var $pro_auth_store;
    var $pro_wpmu_store;

    var $pro_license_label;
    var $pro_license_str;

    var $pro_error_message_str;

    var $pro_check_interval;
    var $pro_last_checked_store;

    var $pro_username;
    var $pro_password;
    var $license;
    var $pro_mothership_xmlrpc_url;
    var $timeout;

    function FrmUpdatesController(){
        // Where all the vitals are defined for this plugin
        $this->plugin_nicename      = 'formidable';
        $this->plugin_name          = 'formidable/formidable.php';
        $this->plugin_url           = 'http://formidablepro.com/formidable-wordpress-plugin';
        $this->pro_script           = FRM_PATH . '/pro/formidable-pro.php';
        $this->pro_mothership       = 'http://api.strategy11.com/plugin-updates/';
        $this->pro_cred_store       = 'frmpro-credentials';
        $this->pro_auth_store       = 'frmpro-authorized';
        $this->pro_wpmu_store       = 'frmpro-wpmu-sitewide';
        $this->pro_last_checked_store = 'frm_autoupdate';
        $this->pro_license_label    = __('Formidable Pro License', 'formidable');
        $this->pro_error_message_str = __('Your Formidable Pro License was Invalid', 'formidable');
        $this->pro_check_interval = 60*60*12; // Checking every 12 hours
        $this->timeout = 10;
        
        // Don't modify these variables
        $this->pro_license_str = 'proplug-license';
        $this->pro_mothership_xmlrpc_url = 'http://formidablepro.com/xmlrpc.php';
        
        add_filter('site_transient_update_plugins', array( &$this, 'queue_update' ) );
        //add_action('admin_notices', array( &$this, 'activation_warning'));
        add_action('wp_ajax_frm_activate_license', array( &$this, 'activate'));
        add_action('wp_ajax_frm_deactivate_license', array( &$this, 'deactivate'));
        
        // Retrieve Pro Credentials
        $this->pro_wpmu = false;
        if (IS_WPMU and get_site_option($this->pro_wpmu_store)){
            $creds = get_site_option($this->pro_cred_store);
            $this->pro_wpmu = true;
        }else
            $creds = get_option($this->pro_cred_store);

        $license = '';
        if($creds and is_array($creds)){
            extract($creds);
            $this->license = (isset($license) and !empty($license)) ? $license : '';
            $this->pro_username = (isset($username) and !empty($username)) ? $username : '';
            $this->pro_password = (isset($password) and !empty($password)) ? $password : '';
        }
    }

    function pro_is_installed(){
        return file_exists($this->pro_script);
    }

    function pro_is_authorized($force_check=false){
        if( empty($this->license) and empty($this->pro_username) and empty($this->pro_password))
            return false;
         
        if( empty($this->license) ){
            $license = $this->get_user_license();
            if(empty($license))
                return false;
        }
            
        if (IS_WPMU and $this->pro_wpmu)
            $authorized = get_site_option($this->pro_auth_store);
        else
            $authorized = get_option($this->pro_auth_store);
           
        if(!$force_check)
            return $authorized;
        
        if( !empty($this->license) ){
            $new_auth = $this->check_license();
            return $new_auth['auth'];
        }

        return false;
    }

    function pro_is_installed_and_authorized(){
        return ($this->pro_is_installed() and $this->pro_is_authorized());
    }

    public function get_user_license(){
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );

        $client = new IXR_Client( $this->pro_mothership_xmlrpc_url, false, 80, $this->timeout );
      
        if( !$client->query( 'proplug.get_license', $this->pro_username, $this->pro_password ) )
            return false;
        
        $license = $client->getResponse();

        if($license and !empty($license))  
            $this->_update_auth(array('license' => $license, 'wpmu' => $this->pro_wpmu));

        return $client->getResponse();
    }
    
    public function pro_cred_form(){ 
        global $frmpro_is_installed; 
        if(isset($_POST) and isset($_POST['process_cred_form']) and $_POST['process_cred_form'] == 'Y'){
            $response = $this->process_form();
            if($response['auth']){ ?>
<div id="message" class="updated fade"><strong>
<?php
            if(!$this->pro_is_authorized() and !$this->pro_is_installed()){
                $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $this->plugin_name, 'upgrade-plugin_' . $this->plugin_name);
                printf(__('Your License was accepted<br/>Now you can %1$sUpgrade Automatically!%2$s', 'formidable'), "<a href='{$inst_install_url}'>","</a>"); 
            }else if($this->pro_is_installed()){ 
                $frmpro_is_installed = $this->pro_is_installed_and_authorized();
                _e('Your Pro installation is now active. Enjoy!', 'formidable');
            } ?>
</strong></div>
<?php       }else{ ?>
<div class="error">
    <ul>
        <li><strong><?php _e('ERROR', 'formidable'); ?></strong>: <?php echo $response['response']; ?></li>
    </ul>
</div>
<?php
            }
        } 
?>
<div style="float:left;width:55%">
    <?php $this->display_form(); 
    
    if(!$frmpro_is_installed){ ?>
    <p>Already signed up? <a href="http://formidablepro.com/account/" target="_blank"><?php _e('Click here', 'formidable') ?></a> to get your license number.</p>
    <?php } ?>
</div>

<?php if($frmpro_is_installed){ ?>
<div class="frm_pro_installed">
<div><strong class="alignleft" style="margin-right:10px;"><?php _e('Formidable Pro is Installed', 'formidable') ?></strong>
    <a href="javascript:frm_show_auth_form()" class="button-secondary alignleft"><?php _e('Enter new license', 'formidable') ?></a>
    <a href="javascript:frm_deauthorize()" onclick="return confirm('<?php echo esc_attr(__('Are you sure you want to deactivate Formidable Pro on this site?', 'formidable')) ?>')" id="frm_deauthorize_link" class="button-secondary alignright"><?php _e('Deauthorize this site', 'formidable') ?></a>
</div>
<div class="clear"></div>
</div>
<p class="frm_aff_link"><a href="http://formidablepro.com/account/" target="_blank"><?php _e('Account', 'formidable') ?></a> |
    <a href="http://formidablepro.com/affiliate-dashboard/" target="_blank"><?php _e('Affiliate Dashboard', 'formidable') ?></a>
</p>

<script type="text/javascript">
function frm_show_auth_form(){
jQuery('#pro_cred_form,.frm_pro_installed').toggle();
}
function frm_deauthorize(){
jQuery('#frm_deauthorize_link').replaceWith('<img src="<?php echo FRM_IMAGES_URL; ?>/wpspin_light.gif" alt="<?php _e('Loading...', 'formidable'); ?>" id="frm_deauthorize_link" />');
jQuery.ajax({type:'POST',url:ajaxurl,data:'action=frm_deauthorize',
success:function(msg){jQuery('#frm_deauthorize_link').fadeOut('slow'); frm_show_auth_form();}
});
};
</script>
<?php   }else{ ?>   

<div style="float:right;width:40%">       
    <p><?php _e('Ready to take your forms to the next level?<br/>Formidable Pro will help you style forms, manage data, and get reports.', 'formidable') ?></p>
    <a href="http://formidablepro.com"><?php _e('Learn More', 'formidable') ?> &#187;</a>
</div>
<?php   } ?>

<div class="clear"></div>

<?php    
    }

    function display_form(){
        global $frmpro_is_installed;
        
        // Yah, this is the view for the credentials form -- this class isn't a true model
        extract($this->get_pro_cred_form_vals());
        ?>
<div id="pro_cred_form" <?php echo ($frmpro_is_installed) ? 'style="display:none;"' : ''; ?>>
    <form name="cred_form" method="post" autocomplete="off">
    <input type="hidden" name="process_cred_form" value="Y" />
    <?php wp_nonce_field('cred_form'); ?>

    <table class="form-table frm_lics_form">
        <tr class="form-field">
            <td valign="top" width="150px"><?php echo $this->pro_license_label; ?></td>
            <td><input type="text" name="<?php echo $this->pro_license_str; ?>" value="" style="width:97%;"/></td>
        </tr>
        
        <?php if (IS_WPMU){ ?>
        <tr>
            <td valign="top"><?php _e('WordPress MU', 'formidable'); ?></td>
            <td valign="top">
                <input type="checkbox" value="1" name="proplug-wpmu" <?php checked($wpmu, 1) ?> />
                <?php _e('Use this license to enable Formidable Pro site-wide', 'formidable'); ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td></td>
            <td>    
                <input class="button-secondary" type="submit" value="<?php _e('Save License', 'formidable'); ?>" />
                <?php if($frmpro_is_installed){ 
                    _e('or', 'formidable'); 
                ?>
                <a href="javascript:frm_show_auth_form()" class="button-secondary"><?php _e('Cancel', 'formidable'); ?></a>
                <?php } ?>
            </td>
        </tr>
      </table>
    </form>
</div>
<?php
    }

    function process_form(){
        $creds = $this->get_pro_cred_form_vals();
        $user_authorized = $this->check_license($creds['license']);

        if(!empty($user_authorized['auth']) and $user_authorized['auth']){
            $this->_update_auth($creds);

            if(!$this->pro_is_installed())
                $this->manually_queue_update();
        }

        return $user_authorized;
    }
    
    private function _update_auth($creds){
        if (IS_WPMU)
            update_site_option($this->pro_wpmu_store, $creds['wpmu']);

        if ($creds['wpmu']){
            update_site_option($this->pro_cred_store, $creds);
            update_site_option($this->pro_auth_store, true);
        }else{
            update_option($this->pro_cred_store, $creds);
            update_option($this->pro_auth_store, true);
        }

        extract($creds);
        $this->license = (isset($license) and !empty($license)) ? $license : '';
    }

    function get_pro_cred_form_vals(){
        $license = (isset($_POST[$this->pro_license_str])) ? $_POST[$this->pro_license_str] : $this->license;
        $wpmu = (isset($_POST['proplug-wpmu'])) ? true : $this->pro_wpmu;

        return compact('license', 'wpmu');
    }
    
    function activate(){
        $message = '';
        $errors = array();
        
        if(!isset($_POST['hlpdsk_license']) or empty($_POST['hlpdsk_license'])){
            $errors[] = __('Please enter a license number', 'formidable');
            include(FRM_PATH .'/classes/views/shared/errors.php'); 
            die();
        }
            
        $this->license = stripslashes($_POST['hlpdsk_license']);
        $domain = home_url();
        $args = compact('domain');
        
        try{
            $act = $this->send_mothership_request($this->plugin_nicename .'/activate/'. $hlpdsk_settings->license, $args);

            if(!is_array($act)){
                $errors[] = $act;
            }else{    
                $this->manually_queue_update();
                $hlpdsk_settings->store(false);
                $message = $act['message'];
            }
        }
        catch(Exception $e){
            $errors[] = $e->getMessage();
        }
        
        include(FRM_PATH .'/classes/views/shared/errors.php'); 
        die();
    }
    
    function check_license($license=false){
        $save = true;
        if(empty($license)){
            $license = $this->license;
            $save = false;
        }
            
        if(empty($license))
            return array('auth' => false, 'response' => __('Please enter a license number', 'formidable'));
        
        $domain = home_url();
        $args = compact('domain');
        
        $act = $this->send_mothership_request($this->plugin_nicename .'/activate/'. $license, $args);
         
        if($save){  
            $auth = false;
            if(!is_array($act)){
                $errors[] = $act;
            }else{    
                $this->manually_queue_update();
                $message = $act['message'];
                    
                $auth = is_array($act) ? true : false;

                $wpmu = (isset($_POST) and isset($_POST['proplug-wpmu'])) ? true : $this->pro_wpmu;

                //save response
                if (IS_WPMU)
                    update_site_option($this->pro_wpmu_store, $wpmu);

                if ($wpmu){
                    update_site_option($this->pro_cred_store, compact('license', 'wpmu'));
                    update_site_option($this->pro_auth_store, $auth);
                }else{
                    update_option($this->pro_cred_store, compact('license', 'wpmu'));
                    update_option($this->pro_auth_store, $auth);
                }

            }
            
            return array('auth' => $auth, 'response' => $act);
        }
        
        return array('auth' => false, 'response' => __('Please enter a license number', 'formidable'));
    }

    function deactivate(){
        delete_option($this->pro_cred_store);
        delete_option($this->pro_auth_store);
              
        if(empty($this->license))
            return;
            
        $domain = home_url();
        $args = compact('domain');

        try{
            $act = $this->send_mothership_request($this->plugin_nicename .'/deactivate/'. $this->license, $args);
            if(!is_array($act))
                $errors[] = $act;
            else
                $message = $act['message'];
        }
        catch(Exception $e){
            $errors[] = $e->getMessage();
        }

        include(FRM_PATH .'/classes/views/shared/errors.php'); 
        die();
    }

    function queue_update($transient, $force=false){
        if(!is_object($transient))
            return $transient;

        $plugin = $this;
        
        //make sure it doesn't show there is an update if plugin is up-to-date
        if($this->pro_is_installed() and !empty( $transient->checked ) and 
            isset($transient->checked[ $this->plugin_name ]) and 
            ((isset($transient->response) and isset($transient->response[$this->plugin_name]) and 
            $transient->checked[ $this->plugin_name ] == $transient->response[$this->plugin_name]->new_version) or
            (!isset($transient->response)) or empty($transient->response))){

            if(isset($transient->response[$this->plugin_name]))        
                unset($transient->response[$this->plugin_name]);
            set_site_transient( $this->pro_last_checked_store, 'latest', $this->pro_check_interval );
        }else if(!empty( $transient->checked ) or
            (isset($transient->response) and isset($transient->response[$this->plugin_name]) and  
            (($transient->response[$this->plugin_name] == 'latest' and !$this->pro_is_installed()) or 
            $transient->response[$this->plugin_name]->url == 'http://wordpress.org/plugins/'. $this->plugin_nicename .'/'))){

            if( $this->pro_is_authorized() and !$this->pro_is_installed()){
                $version_info = get_site_transient( $this->pro_last_checked_store );
                global $frm_version;
                
                //don't force an api check if the transient has already been forced
                if($version_info and is_array($version_info) and $transient->response[$this->plugin_name]->url == 'http://formidablepro.com/' and isset($version_info['version']) and version_compare($version_info['version'], $frm_version, '=') and isset($version_info['url']) and $version_info['url'] == $transient->response[$this->plugin_name]->package)
                    $force = false;
                else
                    $force = true;
            }
                
            $transient = $this->queue_addon_update($transient, $plugin, $force, false);
        }
        
        return $transient;
    }
    
    function queue_addon_update($transient, $plugin, $force=false, $checked=true){
        if(!is_object($transient) or ($checked and empty($transient->checked)))
            return $transient;
        
        $version_info = $this->get_version($transient->checked[ $plugin->plugin_name ], $force, $plugin);
        $installed_version = $transient->checked[$plugin->plugin_name];

        if($version_info and isset($version_info['version']) and ($force or version_compare($version_info['version'], $installed_version, '>'))){
            $transient->response[$plugin->plugin_name] = new stdClass();
            $transient->response[$plugin->plugin_name]->id = 0;
            $transient->response[$plugin->plugin_name]->slug = $plugin->plugin_name;
            $transient->response[$plugin->plugin_name]->new_version = $version_info['version'];
            $transient->response[$plugin->plugin_name]->url = 'http://formidablepro.com/';
            
            if(isset($version_info['url'])){
                $transient->response[$plugin->plugin_name]->package = $version_info['url'];
            }else{
                //new version available, but no permission
                $expired = isset($version_info['expired']) ? __('expired', 'formidable') : __('invalid', 'formidable');
                $transient->response[$plugin->plugin_name]->upgrade_notice = sprintf(__('An update is available, but your license is %s.', 'formidable'), $expired);
            }
            
            set_site_transient('update_plugins', $transient);
        }else if(!$version_info and isset($transient->response[$plugin->plugin_name])){
            unset( $transient->response[$plugin->plugin_name] );
            delete_site_transient( $plugin->pro_last_checked_store );
        }
        
        return $transient;
    }
    
    function get_version($version, $force=false, $plugin=false){
        if($plugin and $plugin->plugin_nicename != $this->plugin_nicename){
            //don't check for update if pro is not installed
            global $frmpro_is_installed;
            if(!$frmpro_is_installed)
                return false;
        }
        
        if(!$force)
            $version_info = get_site_transient( $plugin->pro_last_checked_store );
        
        if(isset($version_info) and $version_info and !is_array($version_info))
            $version_info = false;
        
        if(!isset($version_info) or !$version_info){
            $download_url = '';
            $errors = false;
            
            if(empty($this->license) and !empty($this->pro_username) and !empty($this->pro_password) ){
                //get license from credentials
                $this->get_user_license();
            }
            
            if(!empty($this->license)){
                $domain = home_url();
                $args = compact('domain');

                $version_info = $this->send_mothership_request($plugin->plugin_nicename .'/info/'. $this->license, $args);
                if(!is_array($version_info))
                    $errors = true;
            }
            
            if(!isset($version_info) or $errors){
                // query for the current version
                $version_info = $this->send_mothership_request($plugin->plugin_nicename .'/latest');
                $errors = !is_array($version_info) ? true : false;
            }
            
            if($errors)
                return false;
            
            // store in transient for 24 hours
            set_site_transient( $plugin->pro_last_checked_store, $version_info, $plugin->pro_check_interval );
        }

        return (array)$version_info;
    }

    function manually_queue_update(){
        $transient = get_site_transient('update_plugins');
        set_site_transient('update_plugins', $this->queue_update($transient, true));
    }

    function queue_button(){ ?>
<a href="<?php echo admin_url('admin.php?page=helpdesk-options&action=queue&_wpnonce=' . wp_create_nonce($this->manually_queue_update)); ?>" class="button"><?php _e('Check for Update', 'formidable')?></a>
<?php
    }

    function send_mothership_request( $endpoint, $args=array(), $domain=false){
        global $frm_version;
        
        if(empty($domain))
            $domain = $this->pro_mothership;
        $uri = "{$domain}{$endpoint}";

        $arg_array = array( 'body'      => $args,
                            'timeout'   => 15,
                            'sslverify' => false,
                            'user-agent' => 'Formidable/'. $frm_version .'; '. get_bloginfo( 'url' )
                          );

        $resp = wp_remote_post($uri, $arg_array);
        $body = wp_remote_retrieve_body( $resp );

        if(is_wp_error($resp)){
            $message = sprintf(__('You had an error communicating with Strategy11\'s API. %1$sClick here%2$s for more information.', 'formidable'), '<a href="http://formidablepro.com/knowledgebase/why-cant-i-activate-formidable-pro/" target="_blank">', '</a>');
            if(is_wp_error($resp))
                $message .= ' '. $resp->get_error_message();
            return $message;
        }else if($body == 'error' or is_wp_error($body)){
            return __('You had an HTTP error connecting to Strategy11\'s API', 'formidable');
        }else{
            if(null !== ($json_res = json_decode($body, true))){
                if(isset($json_res['error']))
                    return $json_res['error'];
                else
                    return $json_res;
            }else if(isset($resp['response']) and isset($resp['response']['code'])){
                return 'There was a '. $resp['response']['code'] .' error: '. $resp['response']['message'];
            }else{
                return __( 'Your License Key was invalid', 'formidable');
            }
        }

        return false;
    }

    function activation_warning(){
        $hlpdsk_settings = HlpdskSettings::fetch();

        if(empty($hlpdsk_settings->license) and (!isset($_REQUEST['page']) or $_REQUEST['page'] != 'hlp-settings'))
            include(FRM_PATH . '/classes/views/update/activation_warning.php');  
    }
}

