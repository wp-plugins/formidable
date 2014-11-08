<?php
if ( ! defined('ABSPATH') ) {
    die('You are not allowed to call this page directly.');
}

class FrmEntriesController{

    public static function load_hooks(){
        add_action('admin_menu', array(__CLASS__, 'menu'), 12);
        add_filter('contextual_help', array(__CLASS__, 'contextual_help'), 10, 3 );
        add_filter('set-screen-option', array(__CLASS__, 'save_per_page'), 10, 3);
        add_filter('update_user_metadata', array(__CLASS__, 'check_hidden_cols'), 10, 5);
        add_action('updated_user_meta', array(__CLASS__, 'update_hidden_cols'), 10, 4);

        add_action('wp', array(__CLASS__, 'process_entry'), 10, 0);
        add_action('frm_wp', array(__CLASS__, 'process_entry'), 10, 0);
        add_filter('frm_redirect_url', array(__CLASS__, 'delete_entry_before_redirect'), 50, 3);
        add_action('frm_after_entry_processed', array(__CLASS__, 'delete_entry_after_save'), 100);
        add_filter('frm_email_value', array(__CLASS__, 'filter_email_value'), 10, 3);
        add_filter('frmpro_fields_replace_shortcodes', array(__CLASS__, 'filter_shortcode_value'), 10, 4);
    }

    public static function menu() {
        if ( current_user_can('administrator') && !current_user_can('frm_view_entries') ) {
            global $wp_roles;
            $wp_roles->add_cap( 'administrator', 'frm_view_entries' );
            $wp_roles->add_cap( 'administrator', 'frm_delete_entries' );
        }

        $frm_settings = FrmAppHelper::get_settings();

        add_submenu_page('formidable', $frm_settings->menu .' | '. __('Entries', 'formidable'), __('Entries', 'formidable'), 'frm_view_entries', 'formidable-entries', array(__CLASS__, 'route') );

        if ( ! isset($_GET['frm_action']) || ! in_array($_GET['frm_action'], array('edit', 'show')) ) {
            add_filter('manage_'. sanitize_title($frm_settings->menu) .'_page_formidable-entries_columns', array(__CLASS__, 'manage_columns'));
            add_filter('manage_'. sanitize_title($frm_settings->menu) .'_page_formidable-entries_sortable_columns', array(__CLASS__, 'sortable_columns'));
            add_filter('get_user_option_manage'. sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden', array(__CLASS__, 'hidden_columns'));
        }
    }

    /* Display in Back End */
    public static function route(){
        $action = FrmAppHelper::get_param('frm_action');

        switch ( $action ) {
            case 'show':
            case 'destroy':
            case 'destroy_all':
                return self::$action();

            case 'list-form':
                return self::bulk_actions($action);

            default:
                do_action('frm_entry_action_route', $action);
                if ( apply_filters('frm_entry_stop_action_route', false, $action) ) {
                    return;
                }

                return self::display_list();
        }
    }

    public static function get_admin_params($form=null){
        if(!$form){
            $frm_form = new FrmForm();
            $form = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", 'name', 1);
        }

        $values = array();
        foreach (array('id' => '', 'form_name' => '', 'paged' => 1, 'form' => (($form) ? $form->id : 0), 'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'fid' => '', 'keep_post' => '') as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

        return $values;
    }

    public static function contextual_help($help, $screen_id, $screen) {
        // Only add to certain screens. add_help_tab was introduced in WordPress 3.3
        if ( $screen_id != 'formidable_page_formidable-entries' || ! method_exists( $screen, 'add_help_tab' ) ){
            return $help;
        }

        if ( ! isset($_GET) || ! isset($_GET['page']) || $_GET['page'] != 'formidable-entries' || ( isset($_GET['frm_action']) && $_GET['frm_action'] != 'list' ) ) {
            return $help;
        }

        $screen->add_help_tab( array(
            'id'      => 'formidable-entries-tab',
            'title'   => __( 'Overview', 'formidable' ),
            'content' => '<p>' . __('This screen provides access to all of your entries. You can customize the display of this screen to suit your workflow.', 'formidable') .'</p>
            <p>'. __('Hovering over a row in the entries list will display action links that allow you to manage your entry.', 'formidable') . '</p>',
        ));

        $screen->set_help_sidebar(
    	    '<p><strong>' . __('For more information:', 'formidable') . '</strong></p>' .
    	    '<p><a href="http://formidablepro.com/knowledgebase/manage-entries-from-the-back-end/" target="_blank">' . __('Documentation on Entries', 'formidable') . '</a></p>' .
    	    '<p><a href="http://formidablepro.com/help-topics/" target="_blank">' . __('Support', 'formidable') . '</a></p>'
    	);

        return $help;
    }

    public static function manage_columns($columns){
        global $frm_vars;
        $form_id = FrmProAppHelper::get_current_form_id();

        $columns[$form_id .'_id'] = 'ID';
        $columns[$form_id .'_item_key'] = __('Entry Key', 'formidable');

        $frm_field = new FrmField();
        $form_cols = $frm_field->getAll("fi.type not in ('". implode("','", FrmFieldsHelper::no_save_fields() ) ."') and (fr.parent_form_id = ". (int) $form_id ." OR fi.form_id=". (int) $form_id .")", 'field_order ASC');

        foreach ( $form_cols as $form_col ) {
            if ( isset($form_col->field_options['separate_value']) && $form_col->field_options['separate_value'] ) {
                $columns[$form_id .'_frmsep_'. $form_col->field_key] = FrmAppHelper::truncate($form_col->name, 35);
            }

            if ( $form_col->type == 'form' && isset($form_col->field_options['form_select']) && !empty($form_col->field_options['form_select']) ) {
                $sub_form_cols = $frm_field->getAll("fi.type not in ('". implode("','", FrmFieldsHelper::no_save_fields() ) ."') and fi.form_id=". (int) $form_col->field_options['form_select'], 'field_order ASC');

                if ( $sub_form_cols ) {
                    foreach ( $sub_form_cols as $sub_form_col ) {
                        $columns[$form_id .'_'. $sub_form_col->field_key .'-_-'. $form_col->id] = FrmAppHelper::truncate($sub_form_col->name, 35);
                        unset($sub_form_col);
                    }
                }
                unset($sub_form_cols);
            } else if ( $form_col->form_id != $form_id ) {
                $columns[$form_id .'_'. $form_col->field_key .'-_-form'. $form_col->form_id] = FrmAppHelper::truncate($form_col->name, 35);
            } else {
                $columns[$form_id .'_'. $form_col->field_key] = FrmAppHelper::truncate($form_col->name, 35);
            }

        }

        $columns[$form_id .'_created_at'] = __('Entry creation date', 'formidable');
        $columns[$form_id .'_updated_at'] = __('Entry update date', 'formidable');
        $columns[$form_id .'_ip'] = 'IP';

        $frm_vars['cols'] = $columns;

        if ( FrmAppHelper::is_admin_page('formidable-entries') && ( ! isset($_GET['frm_action']) || $_GET['frm_action'] == 'list' || $_GET['frm_action'] == 'destroy' ) ) {
            add_screen_option( 'per_page', array('label' => __('Entries', 'formidable'), 'default' => 20, 'option' => 'formidable_page_formidable_entries_per_page') );
        }

        return $columns;
    }

    public static function check_hidden_cols($check, $object_id, $meta_key, $meta_value, $prev_value){
        $frm_settings = FrmAppHelper::get_settings();
        if ( $meta_key != 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden' || $meta_value == $prev_value ) {
            return $check;
        }

        if ( empty($prev_value) )
    		$prev_value = get_metadata('user', $object_id, $meta_key, true);

        global $frm_vars;
        //add a check so we don't create a loop
        $frm_vars['prev_hidden_cols'] = ( isset($frm_vars['prev_hidden_cols']) && $frm_vars['prev_hidden_cols'] ) ? false : $prev_value;

        return $check;
    }

    //add hidden columns back from other forms
    public static function update_hidden_cols($meta_id, $object_id, $meta_key, $meta_value ){
        $frm_settings = FrmAppHelper::get_settings();

        if($meta_key != 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden')
            return;

        global $frm_vars;
        if ( ! isset($frm_vars['prev_hidden_cols']) || ! $frm_vars['prev_hidden_cols'] ) {
            return; //don't continue if there's no previous value
        }

        foreach($meta_value as $mk => $mv){
            //remove blank values
            if(empty($mv))
                unset($meta_value[$mk]);
        }

        $cur_form_prefix = reset($meta_value);
        $cur_form_prefix = explode('_', $cur_form_prefix);
        $cur_form_prefix = $cur_form_prefix[0];
        $save = false;

        foreach ( (array) $frm_vars['prev_hidden_cols'] as $prev_hidden ) {
            if ( empty($prev_hidden) || in_array($prev_hidden, $meta_value) ) {
                //don't add blank cols or process included cols
                continue;
            }

            $form_prefix = explode('_', $prev_hidden);
            $form_prefix = $form_prefix[0];
            if($form_prefix == $cur_form_prefix) //don't add back columns that are meant to be hidden
                continue;

            $meta_value[] = $prev_hidden;
            $save = true;
            unset($form_prefix);
        }

        if($save){
            $user = wp_get_current_user();
            update_user_option($user->ID, 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden', $meta_value, true);
        }
    }

    public static function save_per_page($save, $option, $value){
        if ( $option == 'formidable_page_formidable_entries_per_page' ) {
            $save = (int) $value;
        }
        return $save;
    }

    public static function sortable_columns(){
        $form_id = FrmProAppHelper::get_current_form_id();

        $frm_field = new FrmField();
        $fields = $frm_field->get_all_for_form($form_id);
        unset($frm_field);

        $columns = array(
            $form_id .'_id'         => 'id',
            $form_id .'_created_at' => 'created_at',
            $form_id .'_updated_at' => 'updated_at',
            $form_id .'_ip'         => 'ip',
            $form_id .'_item_key'   => 'item_key',
            $form_id .'_is_draft'   => 'is_draft'
        );

        foreach ( $fields as $field ) {
		    if ( $field->type != 'checkbox' && (!isset($field->field_options['post_field']) || $field->field_options['post_field'] == '')) { // Can't sort on checkboxes because they are stored serialized, or post fields
			    $columns[ $form_id .'_'. $field->field_key ] = 'meta_'. $field->id;
		    }
        }

        return $columns;
    }

    public static function hidden_columns($result){
        global $frm_vars;

        $form_id = FrmProAppHelper::get_current_form_id();

        $return = false;
        foreach ( (array) $result as $r ) {
            if(!empty($r)){
                $form_prefix = explode('_', $r);
                $form_prefix = $form_prefix[0];

                if ( (int) $form_prefix == (int) $form_id ) {
                    $return = true;
                    break;
                }

                unset($form_prefix);
            }
        }

        if($return)
            return $result;

        $i = isset($frm_vars['cols']) ? count($frm_vars['cols']) : 0;
        $max_columns = 8;
        if($i <= $max_columns)
            return $result;

        global $frm_vars;
        if(isset($frm_vars['current_form']) and $frm_vars['current_form'])
            $frm_vars['current_form']->options = maybe_unserialize($frm_vars['current_form']->options);

        if(isset($frm_vars['current_form']) and $frm_vars['current_form'] and isset($frm_vars['current_form']->options['hidden_cols']) and !empty($frm_vars['current_form']->options['hidden_cols'])){
            $result = $frm_vars['current_form']->options['hidden_cols'];
        }else{
            $cols = $frm_vars['cols'];
            $cols = array_reverse($cols, true);

            $result[] = $form_id .'_id';
            $i--;

            $result[] = $form_id .'_item_key';
            $i--;

            foreach($cols as $col_key => $col){
                if($i > $max_columns)
                    $result[] = $col_key; //remove some columns by default
                $i--;
                unset($col_key);
                unset($col);
            }
        }

        return $result;
    }

    public static function display_list($params=array(), $message='', $errors = array()){
        global $wpdb, $frmdb, $frm_entry, $frm_entry_meta, $frm_field, $frm_vars;

        if(empty($params))
            $params = self::get_admin_params();

        $frm_form = new FrmForm();
        $form_select = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", 'name');

        $form = self::get_form_select($params['form'], $form_select);

        if($form){
            $params['form'] = $form->id;
            $frm_vars['current_form'] = $form;
	        $where_clause = " it.form_id=$form->id";

	        if ( 'trash' == $form->status ) {
	            $delete_timestamp = time() - ( DAY_IN_SECONDS * EMPTY_TRASH_DAYS );
	            $time_to_delete = FrmProAppHelper::human_time_diff( $delete_timestamp, (isset($form->options['trash_time']) ? ($form->options['trash_time']) : time()));
	            $errors['trash'] = sprintf(__('This form is in the trash and is scheduled to be deleted permanently in %s along with any entries.', 'formidable'), $time_to_delete);
	            unset($time_to_delete, $delete_timestamp);
	        }
        }else{
            $where_clause = '';
		}

        $table_class = apply_filters('frm_entries_list_class', 'FrmEntriesListHelper');

        $wp_list_table = new $table_class( array('params' => $params) );

        $pagenum = $wp_list_table->get_pagenum();

        $wp_list_table->prepare_items();

        $total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
        if ( $pagenum > $total_pages && $total_pages > 0 ) {
            $url = add_query_arg( 'paged', $total_pages );
            if ( headers_sent() ) {
                echo FrmAppHelper::js_redirect($url);
            } else {
                wp_redirect($url);
            }
            die();
        }

        if ( empty($message) && isset($_GET['import-message']) ) {
            $message = __('Your import is complete', 'formidable');
        }

        require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/list.php');
    }

    public static function get_form_select($form_id, $form_select) {
        if ( $form_id ) {
            $frm_form = new FrmForm();
            return $frm_form->getOne($form_id);
        }

        return isset($form_select[0]) ? $form_select[0] : 0;
    }

    /* Back End CRUD */
    public static function show($id = false){
        FrmAppHelper::permission_check('frm_view_entries');

        global $frm_entry, $frm_field, $frm_entry_meta;
        if ( ! $id ) {
            $id = FrmAppHelper::get_param('id');
            
            if ( ! $id ) {
                $id = FrmAppHelper::get_param('item_id');
            }
        }

        $entry = $frm_entry->getOne($id, true);
        $data = maybe_unserialize($entry->description);
        if ( ! is_array($data) || ! isset($data['referrer']) ) {
            $data = array('referrer' => $data);
        }

        $fields = $frm_field->get_all_for_form($entry->form_id);
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        include(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/show.php');
    }

    public static function destroy(){
        FrmAppHelper::permission_check('frm_delete_entries');

        global $frm_entry;
        $params = self::get_admin_params();

        if ( isset($params['keep_post']) && $params['keep_post'] ) {
            //unlink entry from post
            global $wpdb, $frmdb;
            $wpdb->update( $frmdb->entries, array('post_id' => ''), array('id' => $params['id']) );
        }

        $message = '';
        if ( $frm_entry->destroy( $params['id'] ) ) {
            $message = __('Entry was Successfully Destroyed', 'formidable');
        }

        self::display_list($params, $message);
    }

    public static function destroy_all(){
        if ( ! current_user_can('frm_delete_entries') ) {
            $frm_settings = FrmAppHelper::get_settings();
            wp_die($frm_settings->admin_permission);
        }

        global $frm_entry, $wpdb;
        $params = self::get_admin_params();
        $message = '';
        $errors = array();
        $form_id = (int) $params['form'];

        if ( $form_id ) {
            $entry_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE form_id=%d", $form_id));
            $action = FrmFormActionsHelper::get_action_for_form($form_id, 'wppost', 1);

            if ( $action ) {
                // this action takes a while, so only trigger it if there are posts to delete
                foreach ( $entry_ids as $entry_id ) {
                    do_action('frm_before_destroy_entry', $entry_id);
                    unset($entry_id);
                }
            }

            $wpdb->query($wpdb->prepare("DELETE em.* FROM {$wpdb->prefix}frm_item_metas as em INNER JOIN {$wpdb->prefix}frm_items as e on (em.item_id=e.id) and form_id=%d", $form_id));
            $results = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}frm_items WHERE form_id=%d", $form_id));
            if ( $results ) {
                $message = __('Entries were Successfully Destroyed', 'formidable');
            }
        } else {
            $errors = __('No entries were specified', 'formidable');
        }

        self::display_list($params, $message, $errors);
    }

    public static function show_form($id='', $key='', $title=false, $description=false){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmFormsController::show_form()' );
        return FrmFormsController::show_form($id, $key, $title, $description);
    }

    public static function get_form($filename, $form, $title, $description) {
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmFormsController::get_form()' );
        return FrmFormsController::get_form($form, $title, $description);
    }

    public static function process_entry($errors='', $ajax=false){
        if ( FrmAppHelper::is_admin() || ! isset($_POST) || ! isset($_POST['form_id']) || ! is_numeric($_POST['form_id']) || ! isset($_POST['item_key']) ) {
            return;
        }

        global $frm_entry, $frm_vars;

        $frm_form = new FrmForm();
        $form = $frm_form->getOne($_POST['form_id']);
        if(!$form)
            return;

        $params = self::get_params($form);

        if(!isset($frm_vars['form_params']))
            $frm_vars['form_params'] = array();
        $frm_vars['form_params'][$form->id] = $params;

        if(isset($frm_vars['created_entries'][$_POST['form_id']]))
            return;

        if ( $errors == '' ) {
            $errors = $frm_entry->validate($_POST);
        }
        $frm_vars['created_entries'][$_POST['form_id']] = array('errors' => $errors);

        if( empty($errors) ){
            $_POST['frm_skip_cookie'] = 1;
            if($params['action'] == 'create'){
                if ( apply_filters('frm_continue_to_create', true, $_POST['form_id']) && ! isset($frm_vars['created_entries'][$_POST['form_id']]['entry_id']) ) {
                    $frm_vars['created_entries'][$_POST['form_id']]['entry_id'] = $frm_entry->create( $_POST );
                }
            }

            do_action('frm_process_entry', $params, $errors, $form, array('ajax' => $ajax));
            unset($_POST['frm_skip_cookie']);
        }
    }

    public static function delete_entry_before_redirect($url, $form, $atts){
        self::_delete_entry($atts['id'], $form);
        return $url;
    }

    //Delete entry if not redirected
    public static function delete_entry_after_save($atts){
        self::_delete_entry($atts['entry_id'], $atts['form']);
    }

    private static function _delete_entry($entry_id, $form){
        if(!$form)
            return;

        $form->options = maybe_unserialize($form->options);
        if ( isset($form->options['no_save']) && $form->options['no_save'] ) {
            global $frm_entry;
            $frm_entry->destroy( $entry_id );
        }
    }

    public static function show_entry_shortcode($atts){
        $atts = shortcode_atts(array(
            'id' => false, 'entry' => false, 'fields' => false, 'plain_text' => false,
            'user_info' => false, 'include_blank' => false, 'default_email' => false,
            'form_id' => false, 'format' => 'text', 'direction' => 'ltr',
            'font_size' => '', 'text_color' => '',
            'border_width' => '', 'border_color' => '',
            'bg_color' => '', 'alt_bg_color' => '',
        ), $atts);

        if ( $atts['format'] != 'text' ) {
            //format options are text, array, or json
            $atts['plain_text'] = true;
        }

        global $frm_entry;

        if ( ! $atts['entry'] || ! is_object($atts['entry']) ) {
            if ( ! $atts['id'] && ! $atts['default_email'] ) {
                return;
            }

            if ( $atts['id'] ) {
                $atts['entry'] = $frm_entry->getOne($atts['id'], true);
            }
        }

        if ( $atts['entry'] ) {
            $atts['form_id'] = $atts['entry']->form_id;
            $atts['id'] = $atts['entry']->id;
        }

        if ( ! $atts['fields'] || ! is_array($atts['fields']) ) {
            global $frm_field;
            $atts['fields'] = $frm_field->get_all_for_form($atts['form_id']);
        }

        $values = array();
        foreach ( $atts['fields'] as $f ) {
            FrmEntriesHelper::fill_entry_values($atts, $f, $values);
            unset($f);
        }

        FrmEntriesHelper::fill_entry_user_info($atts, $values);

        if ( $atts['format'] == 'json' ) {
            return json_encode($values);
        }

        $content = array();
        FrmEntriesHelper::convert_entry_to_content($values, $atts, $content);

        if ( 'text' == $atts['format'] ) {
            $content = implode('', $content);
        }

        return $content;
    }

    public static function &filter_email_value($value, $meta, $entry, $atts=array()){
        $frm_field = new FrmField();
        $field = $frm_field->getOne($meta->field_id);
        if ( ! $field ) {
            return $value;
        }

        $value = self::filter_display_value($value, $field, $atts);
        return $value;
    }

    public static function &filter_shortcode_value($value, $tag, $atts, $field) {
        return self::filter_display_value($value, $field, $atts);
    }

    public static function &filter_display_value($value, $field, $atts=array()){
        $saved_value = ( isset($atts['saved_value']) && $atts['saved_value'] ) ? true : false;
        if ( ! in_array($field->type, array('radio', 'checkbox', 'radio', 'select')) || ! isset($field->field_options['separate_value']) || ! $field->field_options['separate_value'] || $saved_value ) {
            return $value;
        }

        $f_values = $f_labels = array();

        foreach ( $field->options as $opt_key => $opt ) {
            if ( ! is_array($opt) ) {
                continue;
            }

            $f_labels[$opt_key] = isset($opt['label']) ? $opt['label'] : reset($opt);
            $f_values[$opt_key] = isset($opt['value']) ? $opt['value'] : $f_labels[$opt_key];
            if ( $f_labels[$opt_key] == $f_values[$opt_key] ) {
                unset($f_values[$opt_key], $f_labels[$opt_key]);
            }
            unset($opt_key, $opt);
        }

        if ( ! empty($f_values) ) {
            foreach ( (array) $value as $v_key => $val ) {
                if ( in_array($val, $f_values) ) {
                    $opt = array_search($val, $f_values);
                    if ( is_array($value) ) {
                        $value[$v_key] = $f_labels[$opt];
                    } else {
                        $value = $f_labels[$opt];
                    }
                }
                unset($v_key, $val);
            }
        }

        return $value;
    }

    public static function get_params($form=null){
        global $frm_vars;

        $frm_form = new FrmForm();
        if ( ! $form ) {
            $form = $frm_form->getAll(array(), 'name', 1);
        } else if ( ! is_object($form) ) {
            $form = $frm_form->getOne($form);
        }

        if(isset($frm_vars['form_params']) && is_array($frm_vars['form_params']) && isset($frm_vars['form_params'][$form->id]))
            return $frm_vars['form_params'][$form->id];

        $action_var = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = apply_filters('frm_show_new_entry_page', FrmAppHelper::get_param($action_var, 'new'), $form);

        $default_values = array(
            'id' => '', 'form_name' => '', 'paged' => 1, 'form' => $form->id, 'form_id' => $form->id,
            'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'action' => $action
        );

        $values = array();
        $values['posted_form_id'] = FrmAppHelper::get_param('form_id');
        if (!is_numeric($values['posted_form_id']))
            $values['posted_form_id'] = FrmAppHelper::get_param('form');

        if ($form->id == $values['posted_form_id']){ //if there are two forms on the same page, make sure not to submit both
            foreach ($default_values as $var => $default){
                if($var == 'action')
                    $values[$var] = FrmAppHelper::get_param($action_var, $default);
                else
                    $values[$var] = FrmAppHelper::get_param($var, $default);
                unset($var);
                unset($default);
            }
        }else{
            foreach ($default_values as $var => $default){
                $values[$var] = $default;
                unset($var);
                unset($default);
            }
        }

        if ( in_array($values['action'], array('create', 'update')) && ( ! isset($_POST) || ( ! isset($_POST['action']) && ! isset($_POST['frm_action']) ) ) ) {
            $values['action'] = 'new';
        }

        return $values;
    }

}
