<?php

class FrmAppController{
    function FrmAppController(){
        add_action('admin_menu', array( &$this, 'menu' ), 1);
        add_filter('frm_nav_array', array( &$this, 'frm_nav'), 1);
        add_filter('plugin_action_links_'.FRM_PLUGIN_NAME.'/'.FRM_PLUGIN_NAME.'.php', array( &$this, 'settings_link'), 10, 2 );
        add_action('after_plugin_row_'.FRM_PLUGIN_NAME.'/'.FRM_PLUGIN_NAME.'.php', array( &$this,'frmpro_action_needed'));
        add_action('admin_notices', array( &$this,'frmpro_get_started_headline'));
        add_filter('the_content', array( &$this, 'page_route' ), 1);
        add_action('init', array(&$this, 'front_head'));
        add_action('admin_init', array( &$this, 'admin_js'));
        register_activation_hook(FRM_PATH."/formidable.php", array( &$this, 'install' ));
        add_action('wp_ajax_frm_uninstall', array(&$this, 'uninstall') );

        // Used to process standalone requests
        add_action('init', array(&$this,'parse_standalone_request'));
        
        //Shortcodes
        add_shortcode('formidable', array(&$this,'get_form_shortcode'));
        add_filter( 'widget_text', array(&$this,'widget_text_filter'), 9 );
    }
    
    function menu(){
        if(current_user_can('administrator') and !current_user_can('frm_view_forms')){
            global $wp_roles;
            $frm_roles = FrmAppHelper::frm_capabilities();
            foreach($frm_roles as $frm_role => $frm_role_description)
                $wp_roles->add_cap( 'administrator', $frm_role );
        }
        global $frmpro_is_installed;
        if(current_user_can('frm_view_forms')){
            global $frm_forms_controller;
            add_menu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE, 'frm_view_forms', FRM_PLUGIN_NAME, array($frm_forms_controller,'route'), FRM_URL . '/images/icon_16.png');
        }elseif(current_user_can('frm_view_entries') and $frmpro_is_installed){
            global $frmpro_entries_controller;
            add_menu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE, 'frm_view_entries', FRM_PLUGIN_NAME, array($frmpro_entries_controller,'route'), FRM_URL . '/images/icon_16.png');
        }
    }
    
    function frm_nav(){
        $nav = array();
        if(current_user_can('frm_view_forms'))
            $nav[FRM_PLUGIN_NAME] = __('Forms', FRM_PLUGIN_NAME);
            
        if(current_user_can('frm_edit_forms'))
            $nav[FRM_PLUGIN_NAME . '-new'] = __('Create a Form', FRM_PLUGIN_NAME);
        
        if(current_user_can('frm_view_forms'))
            $nav[FRM_PLUGIN_NAME . '-templates'] = __('Templates', FRM_PLUGIN_NAME);
        return $nav;
    }

    // Adds a settings link to the plugins page
    function settings_link($links, $file){
        $settings = '<a href="'.admin_url('admin.php?page='.FRM_PLUGIN_NAME).'">' . __('Settings', FRM_PLUGIN_NAME) . '</a>';
        array_unshift($links, $settings);
        
        return $links;
    }
    
    function frmpro_action_needed( $plugin ){
        global $frm_update;
       
        if( $frm_update->pro_is_authorized() and !$frm_update->pro_is_installed() ){
            if (IS_WPMU and $frm_update->pro_wpmu and !is_site_admin())
                return;
            $frm_update->queue_update(true);
            $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $plugin, 'upgrade-plugin_' . $plugin);
    ?>
      <td colspan="3" class="plugin-update"><div class="update-message" style="-moz-border-radius:5px; border:1px solid #CC0000;; margin:5px; background-color:#FFEBE8; padding:3px 5px;"><?php printf(__('Your Formidable Pro installation isn\'t quite complete yet.<br/>%1$sAutomatically Upgrade to Enable Formidable Pro%2$s', FRM_PLUGIN_NAME), '<a href="'.$inst_install_url.'">', '</a>'); ?></div></td>
    <?php
        }
    }

    function frmpro_get_started_headline(){
        global $frm_update;

        // Don't display this error as we're upgrading the thing... cmon
        if(isset($_GET['action']) and $_GET['action'] == 'upgrade-plugin')
            return;
    
        if (IS_WPMU and $frm_update->pro_wpmu and !is_site_admin())
            return;
            
        if( $frm_update->pro_is_authorized() and !$frm_update->pro_is_installed()){
            $frm_update->queue_update(true);
            $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $frm_update->plugin_name, 'upgrade-plugin_' . $frm_update->plugin_name);
        ?>
    <div class="error" style="padding:7px;"><?php printf(__('Your Formidable Pro installation isn\'t quite complete yet.<br/>%1$sAutomatically Upgrade to Enable Formidable Pro%2$s', FRM_PLUGIN_NAME), '<a href="'.$inst_install_url.'">','</a>'); ?></div>  
        <?php 
        }
    }
    
    function admin_js(){
        global $frm_version;
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        if(!(isset($_GET) and isset($_GET['page'])) or (isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])))
            wp_enqueue_script('jquery-tools', FRM_URL.'/js/jquery/jquery.tools.min.js', array('jquery'), '1.1.2');
        if(isset($_GET) and isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])){
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('formidable', FRM_URL . '/js/formidable.js', array('jquery'), $frm_version);
            wp_enqueue_style('formidable-admin', FRM_URL. '/css/frm_admin.css', $frm_version);
            wp_enqueue_script('jquery-elastic', FRM_URL.'/js/jquery/jquery.elastic.js', array('jquery'));
            add_thickbox();
        }
    }
    
    function front_head(){
        global $frm_settings, $frm_version;
        
        if (IS_WPMU){
            $db_version = 1.03; // this is the version of the database we're moving to
            $old_db_version = get_option('frm_db_version');
            if ($db_version != $old_db_version)
                $this->install();
        }
        
        if(!is_admin() and !$frm_settings->custom_stylesheet){
            $css = apply_filters('get_frm_stylesheet', FRM_URL .'/css/frm_display.css');
            wp_enqueue_style('frm-forms', $css, array(), $frm_version);
        }
    }
  
    function install(){
        global $frmdb;
        $frmdb->upgrade();
    }
    
    function uninstall(){
        if(current_user_can('administrator')){
            global $frmdb;
            $frmdb->uninstall();
            wp_die(__('Formidable was successfully uninstalled.', 'formidable'));
        }else
            wp_die(__('You don\'t have permission to do that!', 'formidable'));
    }
    
    // Routes for wordpress pages -- we're just replacing content here folks.
    function page_route($content){
        global $post, $frm_settings;

        if( $post && $post->ID == $frm_settings->preview_page_id && isset($_GET['form'])){
            global $frm_forms_controller;
            $frm_forms_controller->page_preview();
            return;
        }

        return $content;
    }

    // The tight way to process standalone requests dogg...
    function parse_standalone_request(){
        $plugin     = $this->get_param('plugin');
        $action     = $this->get_param('action');
        $controller = $this->get_param('controller');

        if( !empty($plugin) and $plugin == FRM_PLUGIN_NAME and !empty($controller) ){
          $this->standalone_route($controller, $action);
          exit;
        }
    }

    // Routes for standalone / ajax requests
    function standalone_route($controller, $action=''){
        global $frm_forms_controller;

        if($controller=='forms' and $action != 'export' and $action != 'import')
            $frm_forms_controller->preview($this->get_param('form'));
        else
            do_action('frm_standalone_route', $controller, $action);
    }

    // Utility function to grab the parameter whether it's a get or post
    function get_param($param, $default=''){
        return (isset($_POST[$param])?$_POST[$param]:(isset($_GET[$param])?$_GET[$param]:$default));
    }


    function get_form_shortcode($atts){
        global $frm_entries_controller;
        extract(shortcode_atts(array('id' => '', 'key' => '', 'title' => false, 'description' => false, 'readonly' => false, 'entry_id' => false, 'fields' => array()), $atts));
        do_action('formidable_shortcode_atts', compact('id', 'key', 'title', 'description', 'readonly', 'entry_id', 'fields'));
        return $frm_entries_controller->show_form($id, $key, $title, $description); 
    }


    function widget_text_filter( $content ){
    	$regex = '/\[\s*formidable\s+.*\]/';
    	return preg_replace_callback( $regex, array($this, 'widget_text_filter_callback'), $content );
    }


    function widget_text_filter_callback( $matches ) {
        return do_shortcode( $matches[0] );
    }

}
?>