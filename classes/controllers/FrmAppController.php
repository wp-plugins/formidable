<?php

class FrmAppController{
    function FrmAppController(){
        add_action('admin_menu', array( $this, 'menu' ));
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
        global $frmpro_is_installed, $frm_forms_controller;
        
        add_menu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE, 8, FRM_PLUGIN_NAME, array($frm_forms_controller,'route'), FRM_URL . '/images/icon_16.png');
        
        //if(!$frmpro_is_installed)
            //add_submenu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE .' | Pro Statistics', 'Pro Statistics', 8, FRM_PLUGIN_TITLE.'-statistics',array($this,''));
        
    }
    
    function head(){
        $css_file = 'frm_admin.css';
        $js_file  = 'list-items.js';
        require_once(FRM_VIEWS_PATH . '/shared/head.php');
    }
    
    function admin_js(){
        wp_enqueue_script('jQuery');
        wp_enqueue_script('jQuery-custom', FRM_URL.'/js/jquery/jquery-ui-1.7.1.custom.min.js'); 
        wp_enqueue_script('jQuery-in-place-edit-patched', FRM_URL.'/js/jquery/jquery.editinplace.packed.js');

        add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
        if ( user_can_richedit() )
        	wp_enqueue_script('editor');
        add_thickbox();
    }
    
    function front_head(){
        wp_enqueue_style('frm-forms', FRM_URL.'/css/frm_display.css');
    }
  
    function install(){
      global $wpdb, $frm_form, $frm_field, $frm_app_helper;
      $db_version = 1.0; // this is the version of the database we're moving to
      $old_db_version = get_option('frm_db_version');

      $fields_table     = $wpdb->prefix . "frm_fields";
      $forms_table      = $wpdb->prefix . "frm_forms";
      $items_table      = $wpdb->prefix . "frm_items";
      $item_metas_table = $wpdb->prefix . "frm_item_metas";

      if ($db_version != $old_db_version){
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

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
              );";

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
                created_at datetime NOT NULL,
                PRIMARY KEY  (id)
              );";

      dbDelta($sql);

      /* Create/Upgrade Items Table */
      $sql = "CREATE TABLE {$items_table} (
                id int(11) NOT NULL auto_increment,
                item_key varchar(255) default NULL,
                name varchar(255) default NULL,
                description text default NULL,
                form_id int(11) default NULL,
                parent_item_id int(11) default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY form_id (form_id),
                KEY parent_item_id (parent_item_id)
              );";

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
              );";

      dbDelta($sql);

      /**** ADD DEFAULT FORMS ****/
      if ($frm_app_helper->getRecordCount("form_key='contact' and is_template='1'", $forms_table) <= 0){
          $values = FrmFormsHelper::setup_new_vars();
            $values['name'] = 'Contact Us';
            $values['form_key'] = 'contact';
            $values['description'] = 'We would like to hear from you. Please send us a message by filling out the form below and we will get back with you shortly.';
            $values['is_template'] = 1;
            $values['default_template'] = 1;
            $form_id = $frm_form->create( $values );

            $field_options = array();
            $field_options['label'] = 'top';
            $field_options['size'] = '75';
            $field_options['max'] = '';
            $field_options['required_indicator'] = '*';


            $field_options['blank'] = 'Name cannot be blank';
            $field_options['invalid'] = '';
            $frm_field->create( array(
                'field_key' => 'name', 
                'name' => 'Name', 
                'description' => '', 
                'type' => 'text', 
                'default_value' => '', 
                'options' => '', 
                'form_id' => $form_id, 
                'field_order' => 1, 
                'required' => true, 
                'field_options' => $field_options ));

            $field_options['blank'] = 'Email cannot be blank';
            $field_options['invalid'] = 'Please enter a valid email address';
            $frm_field->create( array(
                  'field_key' => 'email', 
                  'name' => 'Email', 
                  'description' => '', 
                  'type' => 'email', 
                  'default_value' => '', 
                  'options' => '', 
                  'form_id' => $form_id, 
                  'field_order' => 2, 
                  'required' => true, 
                  'field_options' => $field_options ));

          $field_options['blank'] = 'Website cannot be blank';
          $field_options['invalid'] = 'Website is an invalid format';
          $frm_field->create( array(
                'field_key' => 'website', 
                'name' => 'Website', 
                'description' => '', 
                'type' => 'website', 
                'default_value' => '', 
                'options' => '', 
                'form_id' => $form_id, 
                'field_order' => 3, 
                'required' => false, 
                'field_options' => $field_options ));

          $field_options['blank'] = 'Subject cannot be blank';
          $field_options['invalid'] = '';
          $frm_field->create( array(
                'field_key' => 'subject', 
                'name' => 'Subject', 
                'description' => '', 
                'type' => 'text', 
                'default_value' => '', 
                'options' => '', 
                'form_id' => $form_id, 
                'field_order' => 4, 
                'required' => true, 
                'field_options' => $field_options ));

          $field_options['size'] = '65';
          $field_options['max'] = '5';
          $field_options['blank'] = 'Message cannot be blank';
          $frm_field->create( array(
                'field_key' => 'message', 
                'name' => 'Message', 
                'description' => '', 
                'type' => 'textarea', 
                'default_value' => '', 
                'options' => '', 
                'form_id' => $form_id, 
                'field_order' => 5, 
                'required' => true, 
                'field_options' => $field_options ));
                
            $field_options['label'] = 'none';
            $field_options['size'] = '';
            $field_options['max'] = '';
            $frm_field->create( array(
                  'field_key' => 'captcha', 
                  'name' => 'Captcha', 
                  'description' => '', 
                  'type' => 'captcha', 
                  'default_value' => '', 
                  'options' => '', 
                  'form_id' => $form_id, 
                  'field_order' => 6, 
                  'required' => false, 
                  'field_options' => $field_options ));
        }

      /***** SAVE DB VERSION *****/
        update_option('frm_db_version',$db_version);
      }
      do_action('frm_after_install');
    }
    
    
    // Routes for wordpress pages -- we're just replacing content here folks.
    function page_route($content){
        global $post, $frm_settings, $frm_forms_controller;

        if( $post->ID == $frm_settings->preview_page_id){  
            $frm_forms_controller->page_preview();
            return '';
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

        if($controller=='forms'){
          //if($action=='preview')
            $frm_forms_controller->preview($this->get_param('form'));
        }else
            do_action('frm_standalone_route', $controller, $action);
    }

    // Utility function to grab the parameter whether it's a get or post
    function get_param($param, $default=''){
        return (isset($_POST[$param])?$_POST[$param]:(isset($_GET[$param])?$_GET[$param]:$default));
    }


    function get_form_shortcode($atts){
        global $frm_entries_controller;
        extract(shortcode_atts(array('id' => '', 'key' => '', 'title' => false, 'description' => false), $atts));
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