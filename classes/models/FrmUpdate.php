<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

/** Okay, this class is not a pure model -- it contains all the functions
  * necessary to successfully provide an update mechanism for FormidablePro!
  */
class FrmUpdate{
    var $plugin_nicename;
    var $plugin_name;
    var $plugin_url;
    var $pro_script;
    var $pro_mothership;

    var $pro_cred_store;
    var $pro_auth_store;
    var $pro_wpmu_store;

    var $pro_username_label;
    var $pro_password_label;

    var $pro_error_message_str;

    var $pro_check_interval;
    var $pro_last_checked_store;

    var $pro_username;
    var $pro_password;
    var $pro_mothership_xmlrpc_url;
    var $timeout;

    function FrmUpdate(){
        // Where all the vitals are defined for this plugin
        $this->plugin_nicename      = 'formidable';
        $this->plugin_name          = 'formidable/formidable.php';
        $this->plugin_url           = 'http://formidablepro.com/formidable-wordpress-plugin';
        $this->pro_script           = FRM_PATH . '/pro/formidable-pro.php';
        $this->pro_mothership       = 'http://formidablepro.com';
        $this->pro_cred_store       = 'frmpro-credentials';
        $this->pro_auth_store       = 'frmpro-authorized';
        $this->pro_wpmu_store       = 'frmpro-wpmu-sitewide';
        $this->pro_last_checked_store = 'frm_autoupdate';
        $this->pro_username_label    = __('Formidable Pro Username', 'formidable');
        $this->pro_password_label    = __('Formidable Pro Password', 'formidable');
        $this->pro_error_message_str = __('Your Formidable Pro Username or Password was Invalid', 'formidable');

        // Don't modify these variables
        $this->pro_check_interval = 60*60*12; // Checking every 12 hours
        $this->pro_mothership_xmlrpc_url = $this->pro_mothership . '/xmlrpc.php';
        $this->timeout = 10;
        
        add_filter('site_transient_update_plugins', array( &$this, 'queue_update' ) );
        
        // Retrieve Pro Credentials
        $this->pro_wpmu = false;
        if (IS_WPMU and get_site_option($this->pro_wpmu_store)){
            $creds = get_site_option($this->pro_cred_store);
            $this->pro_wpmu = true;
        }else
            $creds = get_option($this->pro_cred_store);

        if($creds and is_array($creds)){
          extract($creds);
          $this->pro_username = ((isset($username) and !empty($username))?$username:'');
          $this->pro_password = ((isset($password) and !empty($password))?$password:'');
        }
    }

    function pro_is_installed(){
        return file_exists($this->pro_script);
    }

    function pro_is_authorized($force_check=false){
        if( !empty($this->pro_username) and !empty($this->pro_password) ){
            if (IS_WPMU and $this->pro_wpmu)
                $authorized = get_site_option($this->pro_auth_store);
            else
                $authorized = get_option($this->pro_auth_store);
            
            if(!$force_check and isset($authorized)){
                return $authorized;
            }else{
                $new_auth = $this->authorize_user($this->pro_username,$this->pro_password);
                if (IS_WPMU and $this->pro_wpmu)
                    update_site_option($this->pro_auth_store, $new_auth);
                else
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

        $client = new IXR_Client($this->pro_mothership_xmlrpc_url, false, 80, $this->timeout );

        if ( !$client->query( 'proplug.is_user_authorized', $username, $password ) )
          return false;

        return $client->getResponse();
    }

    function pro_cred_form(){ 
        global $frmpro_is_installed, $frm_ajax_url; 
        if(isset($_POST) and isset($_POST['process_cred_form']) and $_POST['process_cred_form'] == 'Y'){
            if($this->process_pro_cred_form()){ ?>
<div id="message" class="updated fade"><strong>
<?php
            if(!$this->pro_is_authorized()){
                $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $this->plugin_name, 'upgrade-plugin_' . $this->plugin_name);
                printf(__('Your Username & Password were accepted<br/>Now you can %1$sUpgrade Automatically!%2$s', 'formidable'), "<a href='{$inst_install_url}'>","</a>"); 
            }else{ 
                $frmpro_is_installed = $this->pro_is_installed_and_authorized();
                _e('Your Pro installation is now active. Enjoy!', 'formidable');
            } ?>
</strong></div>
<?php       }else{ ?>
<div class="error">
    <ul>
        <li><strong><?php _e('ERROR', 'formidable'); ?></strong>: <?php echo $this->pro_error_message_str; ?></li>
    </ul>
</div>
<?php
            }
        } 
?>
<div style="float:left;width:55%">
    <?php $this->display_pro_cred_form(); ?>
</div>

<?php if($frmpro_is_installed){ ?>
<div class="frm_pro_installed">
<p><strong class="alignleft" style="margin-right:10px;">Formidable Pro is Installed</strong>
    <a href="javascript:frm_show_auth_form()" class="button-secondary alignleft"><?php _e('Enter new credentials', 'formidable') ?></a>
    <a href="javascript:frm_deauthorize()" onclick="return confirm('<?php echo esc_attr(__('Are you sure you want to deactivate Formidable Pro on this site?', 'formidable')) ?>')" id="frm_deauthorize_link" class="button-secondary alignright"><?php _e('Deauthorize this site', 'formidable') ?></a>
</p>
<div class="clear"></div>
</div>
<p><strong><?php _e('Edit/Update Your Profile', 'formidable') ?>:</strong><br/>
    <span class="howto"><?php _e('Use your account username and password to log in to your Account and Affiliate Control Panel', 'formidable') ?></span></p>
<p><a href="http://formidablepro.com/payment/member.php" target="_blank"><?php _e('Account', 'formidable') ?></a> |
    <a href="http://formidablepro.com/payment/aff_member.php" target="_blank"><?php _e('Affiliate Control Panel', 'formidable') ?></a>
</p>

<script type="text/javascript">
function frm_show_auth_form(){
jQuery('#pro_cred_form,.frm_pro_installed').toggle();
}
function frm_deauthorize(){
jQuery('#frm_deauthorize_link').replaceWith('<img src="<?php echo FRM_IMAGES_URL; ?>/wpspin_light.gif" alt="<?php _e('Loading...', 'formidable'); ?>" id="frm_deauthorize_link" />');
jQuery.ajax({type:"POST",url:"<?php echo $frm_ajax_url ?>",data:"action=frm_deauthorize",
success:function(msg){jQuery("#frm_deauthorize_link").fadeOut("slow"); frm_show_auth_form();}
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

    function display_pro_cred_form(){
        global $frmpro_is_installed;
        
        // Yah, this is the view for the credentials form -- this class isn't a true model
        extract($this->get_pro_cred_form_vals());
        ?>
<div id="pro_cred_form" <?php echo ($frmpro_is_installed) ? 'style="display:none;"' : ''; ?>>
    <form name="cred_form" method="post" autocomplete="off">
    <input type="hidden" name="process_cred_form" value="Y" />
    <?php wp_nonce_field('cred_form'); ?>

    <table class="form-table">
        <tr class="form-field">
            <td valign="top" width="150px"><?php echo $this->pro_username_label; ?></td>
            <td><input type="text" name="proplug-username" value=""/></td>
        </tr>
        <tr class="form-field">
            <td valign="top"><?php echo $this->pro_password_label; ?></td>
            <td><input type="password" name="proplug-password" value=""/></td>
        </tr>
        <?php if (IS_WPMU){ ?>
        <tr>
            <td valign="top"><?php _e('WordPress MU', 'formidable'); ?></td>
            <td valign="top">
                <input type="checkbox" value="1" name="proplug-wpmu" <?php checked($wpmu, 1) ?> />
                <?php _e('Use this username and password to enable Formidable Pro site-wide', 'formidable'); ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="2">
                <input class="button-secondary" type="submit" value="<?php _e('Save', 'formidable'); ?>" />
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

    function process_pro_cred_form(){
        $creds = $this->get_pro_cred_form_vals();
        $user_authorized = $this->authorize_user($creds['username'], $creds['password']);

        if(!empty($user_authorized) and $user_authorized){
            if (IS_WPMU)
                update_site_option($this->pro_wpmu_store, $creds['wpmu']);

            if ($creds['wpmu']){
                update_site_option($this->pro_cred_store, $creds);
                update_site_option($this->pro_auth_store, $user_authorized);
            }else{
                update_option($this->pro_cred_store, $creds);
                update_option($this->pro_auth_store, $user_authorized);
            }

            extract($creds);
            $this->pro_username = (isset($username) and !empty($username)) ? $username : '';
            $this->pro_password = (isset($password) and !empty($password)) ? $password : '';

            if(!$this->pro_is_installed())
                $this->manually_queue_update();
        }

        return $user_authorized;
    }

    function get_pro_cred_form_vals(){
        $username = (isset($_POST['proplug-username'])) ? $_POST['proplug-username'] : $this->pro_username;
        $password = (isset($_POST['proplug-password'])) ? $_POST['proplug-password'] : $this->pro_password;
        $wpmu = (isset($_POST['proplug-wpmu'])) ? true : $this->pro_wpmu;

        return compact('username', 'password', 'wpmu');
    }
    
    public function get_current_info($version, $force=false, $plugin=false){
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );

        $client = new IXR_Client( $this->pro_mothership_xmlrpc_url, false, 80, $this->timeout );

        $force = $force ? 'true' : 'false';
        $plugin = $plugin ? $plugin : $this->plugin_nicename;
      
        if( !$client->query( 'proplug.get_current_info', $this->pro_username, $this->pro_password, $version, $force, 
            get_option('siteurl'), $plugin) )
            return false;

        return $client->getResponse();
    }
  
    //Check if free version will be downloaded. If so, switch it to the Pro version
    function queue_update($transient, $force=false){
        if(!is_object($transient))
            return $transient;
           
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
            $transient->response[$this->plugin_name]->url == 'http://wordpress.org/extend/plugins/'. $this->plugin_nicename .'/'))){

            if( $this->pro_is_authorized()) {
                if( !$this->pro_is_installed() or (isset($transient->response[$this->plugin_name]) and $transient->response[$this->plugin_name]->url == 'http://wordpress.org/extend/plugins/'. $this->plugin_nicename .'/'))
                    $force = true;
                    
                $plugin = $this;
                $transient = $this->get_plugin_info($transient, $plugin, $force);
            }
        }
        
        return $transient;
    }
    
    public function manually_queue_update() {
        $transient = get_site_transient('update_plugins');
        set_site_transient('update_plugins', $this->queue_update($transient, true));
    }
    
    function queue_addon_update($transient, $plugin, $force=false){
        if(!is_object($transient) or empty($transient->checked))
            return $transient;

        global $frmpro_is_installed;
        if($frmpro_is_installed)
            $transient = $this->get_plugin_info($transient, $plugin, $force);
        
        return $transient;
    }
    
    function get_plugin_info($transient, $plugin, $force=false){
        if(empty($transient->checked) or empty($transient->checked[ $plugin->plugin_name ]))
            return $transient;
        
        $update = get_site_transient($plugin->pro_last_checked_store);

        if($update and $force){
            if($update == 'latest' or version_compare($transient->checked[ $plugin->plugin_name ], $update->new_version, '<'))
                $update = false;
        }
        
        $expired = false;
        if(!$update){
            $update = $this->get_current_info( $transient->checked[ $plugin->plugin_name ], $force, $plugin->plugin_nicename );
            $expired = true;
        }
         
        //only check periodically   
        if($expired){
            if(!$update or empty($update))
                $update = 'latest';
            else
                $update = (object) $update;
  
            set_site_transient($plugin->pro_last_checked_store, $update, $plugin->pro_check_interval );
        }

        if( $update and !empty( $update ) and $update != 'latest'){
            $update = (object) $update;
            
            if(!$force and isset($update->new_version) and version_compare($transient->checked[ $plugin->plugin_name ], $update->new_version, '>=')){
                if(isset($transient->response[ $plugin->plugin_name ]))
                    unset($transient->response[ $plugin->plugin_name ]);
                set_site_transient($plugin->pro_last_checked_store, 'latest', $plugin->pro_check_interval );
            }else{
                $transient->response[ $plugin->plugin_name ] = $update;
            }
        }
        
        return $transient;
    }
}
