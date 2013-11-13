<?php
/**
 * @package Formidable
 */
if(!defined('ABSPATH')) die(__('You are not allowed to call this page directly.', 'formidable'));

if(class_exists('FrmAppController'))
    return;

class FrmAppController{
    function FrmAppController(){
        add_action('admin_menu', 'FrmAppController::menu', 1);
        add_action('admin_head', 'FrmAppController::menu_css');
        add_filter('plugin_action_links_formidable/formidable.php', 'FrmAppController::settings_link', 10, 2 );
        add_action('after_plugin_row_formidable/formidable.php', 'FrmAppController::pro_action_needed');
        add_action('admin_notices', 'FrmAppController::pro_get_started_headline');
        add_filter('the_content', 'FrmAppController::page_route', 10);
        add_action('plugins_loaded', 'FrmAppController::load_lang');
        add_action('init', 'FrmAppController::front_head');
        add_action('wp_footer', 'FrmAppController::footer_js', 1, 0);
        add_action('admin_init', 'FrmAppController::admin_js', 11);
        register_activation_hook(FrmAppHelper::plugin_path().'/formidable.php', 'FrmAppController::install');
        add_action('wp_ajax_frm_install', 'FrmAppController::install');
        add_action('wp_ajax_frm_uninstall', 'FrmAppController::uninstall');
        add_action('wp_ajax_frm_deauthorize', 'FrmAppController::deauthorize');

        // Used to process standalone requests
        add_action('init', 'FrmAppController::parse_standalone_request', 40);
        // Update the session data
        add_action('init', 'FrmAppController::referer_session', 1);
        
        //Shortcodes
        add_shortcode('formidable', 'FrmAppController::get_form_shortcode');
        add_filter( 'widget_text', 'FrmAppController::widget_text_filter', 9 );
    }
    
    public static function menu(){
        global $frm_vars, $frm_settings;
        
        if(is_super_admin() and !current_user_can('frm_view_forms')){
            global $current_user;
            $frm_roles = FrmAppHelper::frm_capabilities();
            foreach($frm_roles as $frm_role => $frm_role_description)
                $current_user->add_cap( $frm_role );
            unset($frm_roles);
            unset($frm_role);
            unset($frm_role_description);
        }
        
        $count = count(get_post_types( array( 'show_ui' => true, '_builtin' => false, 'show_in_menu' => true ) ));
        $pos = ((int)$count > 0) ? '22.7' : '29.3';
        $pos = apply_filters('frm_menu_position', $pos);
        
        if(current_user_can('frm_view_forms')){
            add_menu_page('Formidable', $frm_settings->menu, 'frm_view_forms', 'formidable', 'FrmFormsController::route', 'div', $pos);
        }else if(current_user_can('frm_view_entries') and $frm_vars['pro_is_installed']){
            add_menu_page('Formidable', $frm_settings->menu, 'frm_view_entries', 'formidable', 'FrmProEntriesController::route', 'div', $pos);
        }
    }
    
    public static function menu_css(){ ?>
<style type="text/css">#adminmenu .toplevel_page_formidable div.wp-menu-image, .frm-buttons-icon{background:url(<?php echo FrmAppHelper::plugin_url() ?>/images/form_16.png) no-repeat center -26px;}#adminmenu .toplevel_page_formidable:hover .wp-menu-image, #adminmenu .wp-has-current-submenu.toplevel_page_formidable .wp-menu-image{background-position:center 5px;}.menu-icon-frmdisplay .wp-menu-image img{display:none;}.frm-buttons-icon{display:inline-block;height:16px;margin:0 2px;vertical-align: text-top;width:16px;background-position:left -30px;background-size:15px 44px;}.wp-media-buttons .frm_insert_form{padding-left:0.4em;}</style>    
<?php
    }
    
    public static function get_form_nav($id, $show_nav=false){
        global $pagenow;
        
        $show_nav = FrmAppHelper::get_param('show_nav', $show_nav);
        
        if($show_nav)
            include(FrmAppHelper::plugin_path() .'/classes/views/shared/form-nav.php');
    }

    // Adds a settings link to the plugins page
    public static function settings_link($links, $file){
        $settings = '<a href="'. admin_url('admin.php?page=formidable-settings') .'">' . __('Settings', 'formidable') . '</a>';
        array_unshift($links, $settings);
        
        return $links;
    }
    
    public static function pro_action_needed( $plugin ){
        $frm_update = new FrmUpdatesController();
        if( $frm_update->pro_is_authorized() and !$frm_update->pro_is_installed() ){
            if (is_multisite() and $frm_update->pro_wpmu and !is_super_admin())
                return;
            $frm_update->manually_queue_update();
            $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $plugin, 'upgrade-plugin_' . $plugin);
    ?>
      <td colspan="3" class="plugin-update"><div class="update-message" style="-moz-border-radius:5px;border:1px solid #CC0000;; margin:5px;background-color:#FFEBE8;padding:3px 5px;"><?php echo apply_filters('frm_pro_update_msg', sprintf(__('Your Formidable Pro installation isn\'t quite complete yet.<br/>%1$sAutomatically Upgrade to Enable Formidable Pro%2$s', 'formidable'), '<a href="'.$inst_install_url.'">', '</a>'), $inst_install_url); ?></div></td>
    <?php
        }
    }

    public static function pro_get_started_headline(){
        $frm_update = new FrmUpdatesController();

        // Don't display this error as we're upgrading the thing... cmon
        if(isset($_GET['action']) and $_GET['action'] == 'upgrade-plugin')
            return;
    
        if (is_multisite() and !is_super_admin())
            return;
         
        if(!isset($_GET['activate'])){  
            global $frm_vars;
            $db_version = get_option('frm_db_version');
            $pro_db_version = ($frm_vars['pro_is_installed']) ? get_option('frmpro_db_version') : false;
            if(((int)$db_version < (int)FrmAppHelper::DBVERSION) or ($frm_vars['pro_is_installed'] and (int)$pro_db_version < 23)){ //this number should match the db_version in FrmDb.php
            ?>
<div class="error" id="frm_install_message" style="padding:7px;"><?php _e('Your Formidable database needs to be updated.<br/>Please deactivate and reactivate the plugin to fix this or', 'formidable'); ?> <a id="frm_install_link" href="javascript:void(0)"><?php _e('Update Now', 'formidable') ?></a></div>
<script type="text/javascript">
jQuery(document).ready(function($){ $('#frm_install_link').click(function(){frm_install_now()}); });
function frm_install_now(){
	jQuery('#frm_install_link').replaceWith('<img src="<?php echo FrmAppHelper::plugin_url() ?>/images/wpspin_light.gif" alt="<?php _e('Loading&hellip;') ?>" />');
	jQuery.ajax({
		type:"POST",url:ajaxurl,data:"action=frm_install",
		success:function(msg){jQuery("#frm_install_message").fadeOut("slow");}
	});
}
</script>
<?php
            }
        }
            
        if( $frm_update->pro_is_authorized() and !$frm_update->pro_is_installed()){
            $frm_update->manually_queue_update();
            $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $frm_update->plugin_name, 'upgrade-plugin_' . $frm_update->plugin_name);
        ?>
    <div class="error" style="padding:7px;"><?php echo apply_filters('frm_pro_update_msg', sprintf(__('Your Formidable Pro installation isn\'t quite complete yet.<br/>%1$sAutomatically Upgrade to Enable Formidable Pro%2$s', 'formidable'), '<a href="'.$inst_install_url.'">', '</a>'), $inst_install_url); ?></div>  
        <?php 
        }
    }
    
    public static function admin_js(){
        global $pagenow;
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        
        if(isset($_GET) and (isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])) or ($pagenow == 'edit.php' and isset($_GET) and isset($_GET['post_type']) and $_GET['post_type'] == 'frm_display')){
            $version = FrmAppHelper::plugin_version();
            add_filter('admin_body_class', 'FrmAppController::admin_body_class');
            
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('admin-widgets');
            wp_enqueue_style('widgets');
            wp_enqueue_script('formidable_admin', FrmAppHelper::plugin_url() .'/js/formidable_admin.js', array('jquery', 'jquery-ui-draggable'), $version);
            wp_enqueue_script('formidable');
            self::localize_script('admin');
            
            wp_enqueue_style('formidable-admin', FrmAppHelper::plugin_url() .'/css/frm_admin.css', array(), $version);
            add_thickbox();
            
            wp_register_script('formidable-editinplace', FrmAppHelper::plugin_url() .'/js/jquery/jquery.editinplace.packed.js', array('jquery'), '2.3.0');
            wp_register_script('jquery-frm-themepicker', FrmAppHelper::plugin_url() .'/js/jquery/jquery-ui-themepicker.js', array('jquery'), $version);
            
        }else if($pagenow == 'post.php' or ($pagenow == 'post-new.php' and isset($_REQUEST['post_type']) and $_REQUEST['post_type'] == 'frm_display')){
            if(isset($_REQUEST['post_type'])){
                $post_type = $_REQUEST['post_type'];
            }else if(isset($_REQUEST['post']) and !empty($_REQUEST['post'])){
                $post = get_post($_REQUEST['post']);
                if(!$post)
                    return;
                $post_type = $post->post_type;
            }else{
                return;
            }
            
            if($post_type == 'frm_display'){
                $version = FrmAppHelper::plugin_version();
                wp_enqueue_script('jquery-ui-draggable');
                wp_enqueue_script('formidable_admin', FrmAppHelper::plugin_url() . '/js/formidable_admin.js', array('jquery', 'jquery-ui-draggable'), $version);
                wp_enqueue_style('formidable-admin', FrmAppHelper::plugin_url(). '/css/frm_admin.css', array(), $version);
            }
        }
    }
    
    public static function admin_body_class($classes){
        global $wp_version;
        if(version_compare( $wp_version, '3.4.9', '>'))
            $classes .= ' frm_35_trigger';
        
        return $classes;
    }
    
    public static function load_lang(){
        load_plugin_textdomain('formidable', false, 'formidable/languages/' );
    }
    
    public static function front_head(){
        global $frm_settings;

        if (is_multisite()){
            global $frm_vars;
            
            $old_db_version = get_option('frm_db_version');
            $pro_db_version = ($frm_vars['pro_is_installed']) ? get_option('frmpro_db_version') : false;
            if(((int)$old_db_version < (int)FrmAppHelper::DBVERSION) or ($frm_vars['pro_is_installed'] and (int)$pro_db_version < 23))
                self::install($old_db_version);
        }
        
        $version = FrmAppHelper::plugin_version();
        wp_register_script('formidable', FrmAppHelper::plugin_url() . '/js/formidable.min.js', array('jquery'), $version, true);
        if(!is_admin() or defined('DOING_AJAX'))
            self::localize_script('front');
        
        wp_register_script('recaptcha-ajax', 'http'. (is_ssl() ? 's' : '').'://www.google.com/recaptcha/api/js/recaptcha_ajax.js', '', true);
        wp_enqueue_script('jquery');
        wp_register_script('jquery-placeholder', FrmAppHelper::plugin_url() .'/js/jquery/jquery-placeholder.js', array('jquery'), '2.0.7', true);
        
        $style = apply_filters('get_frm_stylesheet', array('frm-forms' => FrmAppHelper::plugin_url() .'/css/frm_display.css'));
        if($style){
            foreach((array)$style as $k => $file){
                wp_register_style($k, $file, array(), $version);
                if((!is_admin() or defined('DOING_AJAX')) and $frm_settings->load_style == 'all')
                    wp_enqueue_style($k);
                unset($k);
                unset($file);
            }
        }
        
        if((!is_admin() or defined('DOING_AJAX')) and $frm_settings->load_style == 'all'){                
            global $frm_vars;
            $frm_vars['css_loaded'] = true;
        }
    }
    
    public static function localize_script($location){
        wp_localize_script('formidable', 'frm_js', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'images_url' => FrmAppHelper::plugin_url() .'/images',
            'loading' => __('Loading&hellip;')
        ));
        
        if($location == 'admin'){
            wp_localize_script('formidable_admin', 'frm_admin_js', array(
                'templates_updated' => __('Templates Updated', 'formidable'),
                'confirm_uninstall' => __('Are you sure you want to do this? Clicking OK will delete all forms, form data, and all other Formidable data. There is no Undo.', 'formidable'),
                'get_page' => (isset($_GET) and isset($_GET['page'])) ? $_GET['page'] : '',
                'desc' => __('(Click here to add form description or instructions)', 'formidable'),
                'blank' => __('(Blank)', 'formidable'),
                'saving' => esc_attr(__('Saving', 'formidable')),
                'saved' => esc_attr(__('Saved', 'formidable')),
                'ok'    => __('OK'),
                'cancel' => __('Cancel'),
                'clear_default' => __('Clear default value when typing', 'formidable'),
                'no_clear_default' => __('Do not clear default value when typing', 'formidable'),
                'valid_default' => __('Default value will pass form validation', 'formidable'),
                'no_valid_default' => __('Default value will NOT pass form validation', 'formidable'),
                'deauthorize' => __('Are you sure you want to deactivate Formidable Pro on this site?', 'formidable')
            ));
        }
    }
    
    public static function footer_js($location='footer'){
        global $frm_settings, $frm_vars;

        if($frm_vars['load_css'] and (!is_admin() or defined('DOING_AJAX')) and ($frm_settings->load_style != 'none')){
            if($frm_vars['css_loaded'])
                $css = apply_filters('get_frm_stylesheet', array());
            else
                $css = apply_filters('get_frm_stylesheet', array('frm-forms' => FrmAppHelper::plugin_url() .'/css/frm_display.css'));
             
            if(!empty($css)){
                echo "\n".'<script type="text/javascript">';
                foreach((array)$css as $css_key => $file){
                    echo 'jQuery("head").append(unescape("%3Clink rel=\'stylesheet\' id=\''. ($css_key + $frm_vars['css_loaded']) .'-css\' href=\''. $file. '\' type=\'text/css\' media=\'all\' /%3E"));';
                    //wp_enqueue_style($css_key);
                    unset($css_key);
                    unset($file);
                }
                unset($css);

                echo '</script>'."\n";
            }
        }

        if((!is_admin() or defined('DOING_AJAX')) and $location != 'header' and !empty($frm_vars['forms_loaded'])) //load formidable js  
            FrmAppHelper::load_scripts(array('formidable'));
    }
  
    public static function install($old_db_version=false){
        global $frmdb;
        $frmdb->upgrade($old_db_version);
    }
    
    public static function uninstall(){
        if(is_super_admin()){
            global $frmdb;
            $frmdb->uninstall();
            echo true;
        }else{
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        die();
    }
    
    public static function deauthorize(){
        delete_option('frmpro-credentials');
        delete_option('frmpro-authorized');
        delete_site_option('frmpro-credentials');
        delete_site_option('frmpro-authorized');
    }
    
    // Routes for wordpress pages -- we're just replacing content here folks.
    public static function page_route($content){
        global $post, $frm_settings;

        if( $post && $post->ID == $frm_settings->preview_page_id && isset($_GET['form'])){
            $content = FrmFormsController::page_preview();
        }

        return $content;
    }
    
    public static function referer_session() {
    	global $frm_settings;
    	
    	if(!isset($frm_settings->track) or !$frm_settings->track or defined('WP_IMPORTING'))
    	    return;
    	    
    	if ( !isset($_SESSION) )
    		session_start();
    	
    	if ( !isset($_SESSION['frm_http_pages']) or !is_array($_SESSION['frm_http_pages']) )
    		$_SESSION['frm_http_pages'] = array("http://". $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI']);
    	
    	if ( !isset($_SESSION['frm_http_referer']) or !is_array($_SESSION['frm_http_referer']) )
    		$_SESSION['frm_http_referer'] = array();
    	
    	if (!isset($_SERVER['HTTP_REFERER']) or (isset($_SERVER['HTTP_REFERER']) and (strpos($_SERVER['HTTP_REFERER'], FrmAppHelper::site_url()) === false) and ! (in_array($_SERVER['HTTP_REFERER'], $_SESSION['frm_http_referer'])) )) {
    		if (! isset($_SERVER['HTTP_REFERER'])){
    		    $direct = __('Type-in or bookmark', 'formidable');
    		    if(!in_array($direct, $_SESSION['frm_http_referer']))
    			    $_SESSION['frm_http_referer'][] = $direct;
    		}else{
    			$_SESSION['frm_http_referer'][] = $_SERVER['HTTP_REFERER'];	
    		}
    	}
    	
    	if ($_SESSION['frm_http_pages'] and !empty($_SESSION['frm_http_pages']) and (end($_SESSION['frm_http_pages']) != "http://". $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI']))
    		$_SESSION['frm_http_pages'][] = "http://". $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI'];
    		
    	//keep the page history below 100
    	if(count($_SESSION['frm_http_pages']) > 100){
    	    foreach($_SESSION['frm_http_pages'] as $pkey => $ppage){
    	        if(count($_SESSION['frm_http_pages']) <= 100)
    	            break;
    	            
    		    unset($_SESSION['frm_http_pages'][$pkey]);
    		}
    	}
    }

    public static function parse_standalone_request(){
        $plugin     = FrmAppHelper::get_param('plugin');
        $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = FrmAppHelper::get_param($action);  
        $controller = FrmAppHelper::get_param('controller');
        
        if( !empty($plugin) and $plugin == 'formidable' and !empty($controller) ){
            if($controller == 'forms')
                FrmFormsController::preview(FrmAppHelper::get_param('form'));
            else
                do_action('frm_standalone_route', $controller, $action);

            do_action('frm_ajax_'. $controller .'_'. $action);
            die();
        }
    }
    
    //formidable shortcode
    public static function get_form_shortcode($atts){
        global $frm_vars;
        if(isset($frm_vars['skip_shortcode']) and $frm_vars['skip_shortcode']){
            $sc = '[formidable';
            foreach($atts as $k => $v)
                $sc .= ' '. $k .'="'. $v .'"';
            return $sc .']';
        }
        
        $shortcode_atts = shortcode_atts(array('id' => '', 'key' => '', 'title' => false, 'description' => false, 'readonly' => false, 'entry_id' => false, 'fields' => array(), 'exclude_fields' => array()), $atts);
        do_action('formidable_shortcode_atts', $shortcode_atts, $atts);
        extract($shortcode_atts);
        return FrmEntriesController::show_form($id, $key, $title, $description); 
    }

    //filter form shortcode in text widgets
    public static function widget_text_filter( $content ){
    	$regex = '/\[\s*formidable\s+.*\]/';
    	return preg_replace_callback( $regex, 'FrmAppController::widget_text_filter_callback', $content );
    }


    public static function widget_text_filter_callback( $matches ) {
        return do_shortcode( $matches[0] );
    }
    
    public static function update_message($features){
        include(FrmAppHelper::plugin_path() .'/classes/views/shared/update_message.php');
    }
    
    public static function get_postbox_class(){
        if(version_compare( $GLOBALS['wp_version'], '3.3.2', '>'))
            return 'postbox-container';
        else
            return 'inner-sidebar';
    }

}
