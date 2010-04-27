<?php

class FrmAppController{
    function FrmAppController(){
        add_action('admin_menu', array( $this, 'menu' ));
        add_filter( 'plugin_action_links_'.FRM_PLUGIN_NAME.'/'.FRM_PLUGIN_NAME.'.php', array( $this, 'settings_link'), 10, 2 );
        add_action('after_plugin_row_'.FRM_PLUGIN_NAME.'/'.FRM_PLUGIN_NAME.'.php', array( $this,'frmpro_action_needed'));
        add_action('admin_notices', array( $this,'frmpro_get_started_headline'));
        add_filter('the_content', array( $this, 'page_route' ), 1);
        add_action('init', array($this, 'front_head'));
        add_action('admin_init', array( $this, 'admin_js'));
        register_activation_hook(FRM_PATH."/formidable.php", array( $this, 'install' ));

        // Used to process standalone requests
        add_action('init', array($this,'parse_standalone_request'));
        
        //Shortcodes
        add_shortcode('formidable', array($this,'get_form_shortcode'));
        add_filter( 'widget_text', array($this,'widget_text_filter'), 9 );
    }
    
    function menu(){
        global $frm_forms_controller;
        
        add_menu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE, 'administrator', FRM_PLUGIN_NAME, array($frm_forms_controller,'route'), FRM_URL . '/images/icon_16.png');
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
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-tools', FRM_URL.'/js/jquery/jquery.tools.min.js', array('jquery'), '1.1.2');
        if(isset($_GET) and isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])){
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('formidable', FRM_URL . '/js/formidable.js', array('jquery'));
            wp_enqueue_style('formidable-admin', FRM_URL. '/css/frm_admin.css');
            wp_enqueue_script('jquery-elastic', FRM_URL.'/js/jquery/jquery.elastic.js', array('jquery'));
            add_thickbox();
        }
    }
    
    function front_head(){
        global $frm_settings;
        
        if (IS_WPMU){
            $db_version = 1.0; // this is the version of the database we're moving to
            $old_db_version = get_option('frm_db_version');
            if ($db_version != $old_db_version)
                $this->install();
        }
        
        if(!is_admin() and !$frm_settings->custom_stylesheet){
            $css = apply_filters('get_frm_stylesheet', FRM_URL .'/css/frm_display.css');
            wp_enqueue_style('frm-forms', $css);
        }
    }
  
    function install(){
      global $wpdb, $frm_form, $frm_field, $frm_app_helper;
      $db_version = 1.03; // this is the version of the database we're moving to
      $old_db_version = get_option('frm_db_version');

      if ($db_version != $old_db_version){
          $fields_table     = $wpdb->prefix . "frm_fields";
          $forms_table      = $wpdb->prefix . "frm_forms";
          $items_table      = $wpdb->prefix . "frm_items";
          $item_metas_table = $wpdb->prefix . "frm_item_metas";
          
          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      
      $charset_collate = '';
      if( $wpdb->has_cap( 'collation' ) ){
          if( !empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
          if( !empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";
      }

      /* Create/Upgrade Fields Table */
      $sql = "CREATE TABLE {$fields_table} (
                id int(11) NOT NULL auto_increment,
                field_key varchar(255) default NULL,
                name varchar(255) default NULL,
                description text default NULL,
                type text default NULL,
                default_value longtext default NULL,
                options longtext default NULL,
                field_order int(11) default 0,
                required int(1) default NULL,
                field_options longtext default NULL,
                form_id int(11) default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY form_id (form_id)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Forms Table */
      $sql = "CREATE TABLE {$forms_table} (
                id int(11) NOT NULL auto_increment,
                form_key varchar(255) default NULL,
                name varchar(255) default NULL,
                description text default NULL,
                logged_in boolean default NULL,
                editable boolean default NULL,
                is_template boolean default 0,
                default_template boolean default 0,
                status varchar(255) default NULL,
                prli_link_id int(11) default NULL,
                options longtext default NULL,
                notifications longtext default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Items Table */
      $sql = "CREATE TABLE {$items_table} (
                id int(11) NOT NULL auto_increment,
                item_key varchar(255) default NULL,
                name varchar(255) default NULL,
                description text default NULL,
                ip text default NULL,
                form_id int(11) default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY form_id (form_id)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Meta Table */
      $sql = "CREATE TABLE {$item_metas_table} (
                id int(11) NOT NULL auto_increment,
                meta_key varchar(255) default NULL,
                meta_value longtext default NULL,
                field_id int(11) NOT NULL,
                item_id int(11) NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY field_id (field_id),
                KEY item_id (item_id)
              ) {$charset_collate};";

      dbDelta($sql);
      
      /**** MIGRATE DATA ****/
      if ($db_version == 1.03){
          global $frm_entry;
          $all_entries = $frm_entry->getAll();
          foreach($all_entries as $ent){
              $opts = maybe_unserialize($ent->description);
              if(is_array($opts))
                $wpdb->update( $frm_entry->table_name, array('ip' => $opts['ip']), array( 'id' => $ent->id ) );
          }
      }
      
      /**** ADD DEFAULT TEMPLATES ****/
      FrmFormsController::add_default_templates(FRM_TEMPLATES_PATH);

      
      /***** SAVE DB VERSION *****/
      update_option('frm_db_version',$db_version);
      }
      
      do_action('frm_after_install');
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
        extract(shortcode_atts(array('id' => '', 'key' => '', 'title' => false, 'description' => false, 'readonly' => false, 'entry_id' => false), $atts));
        do_action('formidable_shortcode_atts', array('id' => $id, 'key' => $key, 'title' => $title, 'description' => $description, 'readonly' => $readonly, 'entry_id' => $entry_id));
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