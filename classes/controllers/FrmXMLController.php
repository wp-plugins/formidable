<?php

class FrmXMLController{

    public static function menu() {
        add_submenu_page('formidable', 'Formidable | Import/Export', 'Import/Export', 'frm_edit_forms', 'formidable-import', 'FrmXMLController::route');
    }

    public static function add_default_templates() {
        if ( !function_exists( 'libxml_disable_entity_loader' ) ){
    		// XML import is not enabled on your server
    		return;
    	}

        $set_err = libxml_use_internal_errors(true);
        $loader = libxml_disable_entity_loader( true );

        $files = apply_filters('frm_default_templates_files', array(FrmAppHelper::plugin_path() .'/classes/views/xml/default-templates.xml'));

        foreach ( (array) $files as $file ) {
            FrmXMLHelper::import_xml($file);
            unset($file);
        }
        /*
        if(is_wp_error($result))
            $errors[] = $result->get_error_message();
        else if($result)
            $message = $result;
        */

        unset($files);

        libxml_use_internal_errors( $set_err );
    	libxml_disable_entity_loader( $loader );
    }

    public static function route() {
        $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = FrmAppHelper::get_param($action);
        if($action == 'import_xml') {
            return self::import_xml();
        } else if($action == 'export_xml') {
            return self::export_xml();
        } else {
            if ( apply_filters('frm_xml_route', true, $action) ){
                return self::form();
            }
        }
    }

    public static function form($errors = array(), $message = '') {
        $forms = FrmForm::getAll("status is NULL OR status = '' OR status = 'published'", 'name');

        $export_types = apply_filters('frm_xml_export_types',
            array('forms' => __('Forms', 'formidable'))
        );

        $export_format = apply_filters('frm_export_formats', array(
            'xml' => array( 'name' => 'XML', 'support' => 'forms', 'count' => 'multiple'),
        ));

        if ( FrmAppHelper::pro_is_installed() ) {
            $frmpro_settings = new FrmProSettings();
            $csv_format = $frmpro_settings->csv_format;
        } else {
            $csv_format = 'UTF-8';
        }

        include(FrmAppHelper::plugin_path() .'/classes/views/xml/import_form.php');
    }

    public static function import_xml() {
        $errors = array();
        $message = '';

        if ( !current_user_can('frm_edit_forms') || ! isset($_POST['import-xml']) || ! wp_verify_nonce($_POST['import-xml'], 'import-xml-nonce') ) {
            $frm_settings = FrmAppHelper::get_settings();
            $errors[] = $frm_settings->admin_permission;
            self::form($errors);
            return;
        }

        if ( ! isset($_FILES) || ! isset($_FILES['frm_import_file']) || empty($_FILES['frm_import_file']['name']) || (int) $_FILES['frm_import_file']['size'] < 1 ) {
            $errors[] = __( 'Oops, you didn\'t select a file.', 'formidable' );
            self::form($errors);
            return;
        }

        $file = $_FILES['frm_import_file']['tmp_name'];

        if ( !is_uploaded_file($file) ) {
            unset($file);
            $errors[] = __( 'The file does not exist, please try again.', 'formidable' );
            self::form($errors);
            return;
        }

        //add_filter('upload_mimes', 'FrmXMLController::allow_mime');

        $export_format = apply_filters('frm_export_formats', array(
            'xml' => array( 'name' => 'XML', 'support' => 'forms', 'count' => 'multiple'),
        ));

        $file_type = strtolower(pathinfo($_FILES['frm_import_file']['name'], PATHINFO_EXTENSION));
        if ( $file_type != 'xml' && isset($export_format[$file_type]) ) {
            // allow other file types to be imported
            do_action('frm_before_import_'. $file_type );
            return;
        }
        unset($file_type);

        //$media_id = FrmProAppHelper::upload_file('frm_import_file');
        //if(is_numeric($media_id)){

            if ( !function_exists( 'libxml_disable_entity_loader' ) ) {
        		$errors[] = __('XML import is not enabled on your server.', 'formidable');
        		self::form($errors);
        		return;
        	}

            $set_err = libxml_use_internal_errors(true);
            $loader = libxml_disable_entity_loader( true );

            $result = FrmXMLHelper::import_xml($file);
            FrmXMLHelper::parse_message($result, $message, $errors);

            unset($file);

            libxml_use_internal_errors( $set_err );
        	libxml_disable_entity_loader( $loader );
        //}else{
        //    foreach ($media_id->errors as $error)
        //        echo $error[0];
        //}

        self::form($errors, $message);
    }

    public static function export_xml() {
        FrmAppHelper::ajax_permission_check('frm_edit_forms', 'show');

        if (isset($_POST['frm_export_forms'])) {
            $ids = $_POST['frm_export_forms'];
        } else {
            $ids = array();
        }

        if ( isset($_POST['type']) ){
            $type = $_POST['type'];
        } else {
            $type = false;
        }

        $format = isset($_POST['format']) ? $_POST['format'] : 'xml';

        if ( ! headers_sent() && ! $type ) {
            wp_redirect(admin_url('admin.php?page=formidable-import'));
            die();
        }

        if ( $format == 'xml' ) {
            self::generate_xml($type, compact('ids'));
        } else {
            do_action('frm_export_format_'. $format, compact('ids'));
        }

        die();
    }

    public static function generate_xml($type, $args = array() ) {
    	global $wpdb;

	    $type = (array) $type;
        if ( in_array('items', $type) && ! in_array('forms', $type) ) {
            // make sure the form is included if there are entries
            $type[] = 'forms';
        }

	    if ( in_array('forms', $type) ) {
            // include actions with forms
	        $type[] = 'actions';
	    }

	    $tables = array(
	        'items'     => $wpdb->prefix .'frm_items',
	        'forms'     => $wpdb->prefix .'frm_forms',
	        'posts'     => $wpdb->posts,
	        'styles'    => $wpdb->posts,
	        'actions'   => $wpdb->posts,
	    );

	    $defaults = array('ids' => false);
	    $args = wp_parse_args( $args, $defaults );

        $sitename = sanitize_key( get_bloginfo( 'name' ) );

    	if ( ! empty($sitename) ) $sitename .= '.';
    	$filename = $sitename . 'formidable.' . date( 'Y-m-d' ) . '.xml';

    	header( 'Content-Description: File Transfer' );
    	header( 'Content-Disposition: attachment; filename=' . $filename );
    	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

        //make sure ids are numeric
    	if ( is_array($args['ids']) && !empty($args['ids']) ) {
	        $args['ids'] = implode(',', array_filter( $args['ids'], 'is_numeric' ));
	    }

	    $records = array();

	    foreach($type as $tb_type){
            $where = $join = '';
            $table = $tables[$tb_type];

            $select = "$table.id";

            switch ( $tb_type ) {
                case 'forms':
                    //add forms
                    $where = $wpdb->prepare( "$table.status != %s" , 'draft' );
                    if ( $args['ids'] ){
                	    $where .= " AND $table.id IN (". $args['ids'] .")";
                	}
                break;
                case 'actions':
                    $select = "$table.ID";
                	$where = $wpdb->prepare('post_type=%s', FrmFormActionsController::$action_post_type);
                    if ( ! empty($args['ids']) ) {
                        $where .= " AND menu_order IN (". $args['ids'] .")";
                    }
                break;
                case 'items':
                    //$join = "INNER JOIN {$wpdb->prefix}frm_item_metas im ON ($table.id = im.item_id)";
                    if ( $args['ids'] ) {
                        $where = "$table.form_id IN (". $args['ids'] .")";
                    }
                break;
                case 'styles':
                    // Loop through all exported forms and get their selected style IDs
                    $form_ids = explode( ',', $args['ids'] );
                    $style_ids = array();
                    foreach ( $form_ids as $form_id ) {
                        $form_data = FrmForm::getOne( $form_id );
                        $style_ids[] = $form_data->options['custom_style'];
                        unset( $form_id, $form_data );
                    }
                    $select = "$table.ID";
                    $where = $wpdb->prepare('post_type=%s', 'frm_styles');

                    // Only export selected styles
                    if ( ! empty( $style_ids ) ) {
                        $where .= " AND ID IN (". implode( ',', $style_ids ) .")";
                    }
                break;
                default:
                    $select = "$table.ID";
                    $join = "INNER JOIN $wpdb->postmeta pm ON (pm.post_id=$table.ID)";
                    $where = "pm.meta_key='frm_form_id' AND pm.meta_value ";
                    if ( empty($args['ids']) ) {
                        $where .= "> 0";
                    } else {
                        $where .= "IN (". $args['ids'] .")";
                    }
                break;
            }

            if ( ! empty($where) ) {
                $where = "WHERE ". $where;
            }

            $records[$tb_type] = $wpdb->get_col( "SELECT $select FROM $table $join $where" );
            unset($tb_type);
        }

        echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
        include(FrmAppHelper::plugin_path() .'/classes/views/xml/xml.php');
    }

    function allow_mime($mimes) {
        if ( !isset($mimes['csv']) ) {
            // allow csv files
            $mimes['csv'] = 'text/csv';
        }

        if ( !isset($mimes['xml']) ) {
            // allow xml
            $mimes['xml'] = 'text/xml';
        }

        return $mimes;
    }

}