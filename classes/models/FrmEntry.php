<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmEntry{

    function create( $values ){
        global $wpdb, $frm_entry_meta;

        $values = apply_filters('frm_pre_create_entry', $values);

        //Clear leftover "other" data
        if ( isset( $values['item_meta']['other'] ) ) {
            unset( $values['item_meta']['other'] );
        }

        $new_values = array(
            'item_key'  => FrmAppHelper::get_unique_key($values['item_key'], $wpdb->prefix .'frm_items', 'item_key'),
            'name'      => isset($values['name']) ? $values['name'] : $values['item_key'],
            'ip'        => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'is_draft'  => ( ( isset($values['frm_saving_draft']) && $values['frm_saving_draft'] == 1 ) ||  ( isset($values['is_draft']) && $values['is_draft'] == 1) ) ? 1 : 0,
            'form_id'   => isset($values['form_id']) ? (int) $values['form_id']: null,
            'post_id'   => isset($values['post_id']) ? (int) $values['post_id']: null,
            'parent_item_id' => isset($values['parent_item_id']) ? (int) $values['parent_item_id']: null,
            'created_at' => isset($values['created_at']) ? $values['created_at'] : current_time('mysql', 1),
            'updated_at' => isset($values['updated_at']) ? $values['updated_at'] : ( isset($values['created_at']) ? $values['created_at'] : current_time('mysql', 1) ),
        );

        if(is_array($new_values['name']))
            $new_values['name'] = reset($new_values['name']);

        if(isset($values['description']) and !empty($values['description'])){
            $new_values['description'] = maybe_serialize($values['description']);
        }else{
            $referrerinfo = FrmAppHelper::get_referer_info();

            $new_values['description'] = serialize(array('browser' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                                                        'referrer' => $referrerinfo));
        }

        //if(isset($values['id']) and is_numeric($values['id']))
        //    $new_values['id'] = $values['id'];

        if ( isset($values['frm_user_id']) && ( is_numeric($values['frm_user_id']) || FrmAppHelper::is_admin() ) ) {
            $new_values['user_id'] = $values['frm_user_id'];
        } else {
            $user_ID = get_current_user_id();
            $new_values['user_id'] = $user_ID ? $user_ID : 0;
        }

        $new_values['updated_by'] = isset($values['updated_by']) ? $values['updated_by'] : $new_values['user_id'];

        //check for duplicate entries created in the last 5 minutes
        if(!defined('WP_IMPORTING')){
            $create_entry = true;

            $check_val = $new_values;
            $check_val['created_at >'] = date('Y-m-d H:i:s', (strtotime($new_values['created_at']) - (60*5)));
            unset($check_val['created_at']);
            unset($check_val['updated_at']);
            unset($check_val['is_draft']);
            unset($check_val['id']);
            unset($check_val['item_key']);
            if($new_values['item_key'] == $new_values['name'])
                unset($check_val['name']);

            global $frmdb;
            $entry_exists = $frmdb->get_records($wpdb->prefix .'frm_items', $check_val, 'created_at DESC', '', 'id');
            unset($frmdb);

            if($entry_exists and !empty($entry_exists)){
                foreach($entry_exists as $entry_exist){
                    if($create_entry){
                        $create_entry = false;
                        //add more checks here to make sure it's a duplicate
                        if (isset($values['item_meta'])){
                            $metas = $frm_entry_meta->get_entry_meta_info($entry_exist->id);
                            $field_metas = array();
                            foreach($metas as $meta)
                                $field_metas[$meta->field_id] = $meta->meta_value;

                            $diff = array_diff_assoc($field_metas, array_map('maybe_serialize', $values['item_meta']));
                            foreach($diff as $field_id => $meta_value){
                                if(!empty($meta_value) and !$create_entry)
                                    $create_entry = true;
                            }
                        }
                    }
                }
            }

            if ( !$create_entry ) {
                return false;
            }
        }

        $query_results = $wpdb->insert( $wpdb->prefix .'frm_items', $new_values );

        if ( $query_results ) {
            $entry_id = $wpdb->insert_id;

            global $frm_vars;
            if ( !isset($frm_vars['saved_entries']) ) {
                $frm_vars['saved_entries'] = array();
            }
            $frm_vars['saved_entries'][] = (int) $entry_id;

            if ( isset($values['item_meta']) ) {
                $frm_entry_meta->update_entry_metas($entry_id, $values['item_meta']);
            }

            do_action('frm_after_create_entry', $entry_id, $new_values['form_id']);
            do_action('frm_after_create_entry_'. $new_values['form_id'], $entry_id);
            return $entry_id;
        } else {
            return false;
        }
    }

    function duplicate( $id ){
        global $wpdb, $frm_entry, $frm_entry_meta;

        $values = $frm_entry->getOne( $id );

        $new_values = array();
        $new_values['item_key'] = FrmAppHelper::get_unique_key('', $wpdb->prefix .'frm_items', 'item_key');
        $new_values['name'] = $values->name;
        $new_values['is_draft'] = $values->is_draft;
        $new_values['user_id'] = $new_values['updated_by'] = (int) $values->user_id;
        $new_values['form_id'] = $values->form_id ? (int) $values->form_id: null;
        $new_values['created_at'] = $new_values['updated_at'] = current_time('mysql', 1);

        $query_results = $wpdb->insert( $wpdb->prefix .'frm_items', $new_values );
        if ( $query_results ) {
            $entry_id = $wpdb->insert_id;

            global $frm_vars;
            if(!isset($frm_vars['saved_entries']))
                $frm_vars['saved_entries'] = array();
            $frm_vars['saved_entries'][] = (int) $entry_id;

            $frm_entry_meta->duplicate_entry_metas($id, $entry_id);

            do_action('frm_after_duplicate_entry', $entry_id, $new_values['form_id']);
            return $entry_id;
        } else {
            return false;
        }
    }

    function update( $id, $values ){
        global $wpdb, $frm_entry_meta, $frm_field, $frm_vars;
        if ( isset($frm_vars['saved_entries']) && is_array($frm_vars['saved_entries']) && in_array( (int) $id, (array) $frm_vars['saved_entries'] ) ) {
            return;
        }

        $values = apply_filters('frm_pre_update_entry', $values, $id);

        $user_ID = get_current_user_id();

        $new_values = array(
            'name'      => isset($values['name']) ? $values['name'] : '',
            'form_id'   => isset($values['form_id']) ? (int) $values['form_id'] : null,
            'is_draft'  => ( ( isset($values['frm_saving_draft']) && $values['frm_saving_draft'] == 1 ) ||  ( isset($values['is_draft']) && $values['is_draft'] == 1) ) ? 1 : 0,
            'updated_at' => current_time('mysql', 1),
            'updated_by' => isset($values['updated_by']) ? $values['updated_by'] : $user_ID,
        );

        if ( isset($values['post_id']) ) {
            $new_values['post_id'] = (int) $values['post_id'];
        }

        if ( isset($values['item_key']) ) {
            $new_values['item_key'] = FrmAppHelper::get_unique_key($values['item_key'], $wpdb->prefix .'frm_items', 'item_key', $id);
        }

        if ( isset($values['parent_item_id']) ) {
            $new_values['parent_item_id'] = (int) $values['parent_item_id'];
        }

        if ( isset($values['frm_user_id']) && is_numeric($values['frm_user_id']) ) {
            $new_values['user_id'] = $values['frm_user_id'];
        }

        $new_values = apply_filters('frm_update_entry', $new_values, $id);
        $query_results = $wpdb->update( $wpdb->prefix .'frm_items', $new_values, compact('id') );

        if ( $query_results ) {
            wp_cache_delete( $id, 'frm_entry');
        }

        if ( !isset($frm_vars['saved_entries']) ) {
            $frm_vars['saved_entries'] = array();
        }

        $frm_vars['saved_entries'][] = (int) $id;

        if (isset($values['item_meta']))
            $frm_entry_meta->update_entry_metas($id, $values['item_meta']);
        do_action('frm_after_update_entry', $id, $new_values['form_id']);
        do_action('frm_after_update_entry_'. $new_values['form_id'], $id);
        return $query_results;
    }

    function &destroy( $id ){
        global $wpdb;
        $id = (int) $id;

        $entry = $this->getOne($id);
        if ( !$entry ) {
            $result = false;
            return $result;
        }

        do_action('frm_before_destroy_entry', $id, $entry);

        wp_cache_delete( $id, 'frm_entry');
        $wpdb->query('DELETE FROM ' . $wpdb->prefix .'frm_item_metas WHERE item_id=' . $id);
        $result = $wpdb->query('DELETE FROM ' . $wpdb->prefix .'frm_items WHERE id=' . $id);
        return $result;
    }

    function &update_form( $id, $value, $form_id ){
        global $wpdb;
        $form_id = isset($value) ? $form_id : NULL;
        $result = $wpdb->update( $wpdb->prefix .'frm_items', array('form_id' => $form_id), array( 'id' => $id ) );
        if($result)
            wp_cache_delete( $id, 'frm_entry');
        return $result;
    }

    function getOne( $id, $meta = false){
        global $wpdb;

        $query = "SELECT it.*, fr.name as form_name, fr.form_key as form_key FROM {$wpdb->prefix}frm_items it
                  LEFT OUTER JOIN {$wpdb->prefix}frm_forms fr ON it.form_id=fr.id WHERE ";
        $query .= $wpdb->prepare( is_numeric($id) ? 'it.id=%d' : 'it.item_key=%s', $id);

        if ( ! $meta ) {
            $entry = FrmAppHelper::check_cache( $id .'_nometa', 'frm_entry', $query, 'get_row' );
            return stripslashes_deep($entry);
        }

        $entry = FrmAppHelper::check_cache( $id, 'frm_entry' );
        if ( $entry !== false ) {
            return stripslashes_deep($entry);
        }

        $entry = $wpdb->get_row($query);
        $entry = $this->get_meta($entry);

        return stripslashes_deep($entry);
    }

    function get_meta($entry) {
        if ( ! $entry ) {
            return $entry;
        }

        global $wpdb;
        $metas = $wpdb->get_results($wpdb->prepare("SELECT field_id, meta_value, field_key, item_id FROM {$wpdb->prefix}frm_item_metas m LEFT JOIN {$wpdb->prefix}frm_fields f ON m.field_id=f.id WHERE item_id=%d and field_id != %d", $entry->id, 0));

        $entry->metas = array();

        foreach ( $metas as $meta_val ) {
            if ( $meta_val->item_id == $entry->id ) {
                $entry->metas[$meta_val->field_id] = maybe_unserialize($meta_val->meta_value);
                 continue;
            }

            // include sub entries in an array
            if ( ! isset( $entry_metas[$meta_val->field_id]) ) {
                $entry->metas[$meta_val->field_id] = array();
            }

            $entry->metas[$meta_val->field_id][] = maybe_unserialize($meta_val->meta_value);

            unset($meta_val);
        }
        unset($metas);

        wp_cache_set( $entry->id, $entry, 'frm_entry');

        return $entry;
    }

    function &exists( $id ){
        global $wpdb;

        if ( FrmAppHelper::check_cache( $id, 'frm_entry' ) ) {
            $exists = true;
            return $exists;
        }

        $where = (is_numeric($id)) ? 'id=%d' : 'item_key=%s';

        $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE $where", $id));

        $exists = ($id && $id > 0) ? true : false;
        return $exists;
    }

    function getAll($where, $order_by = '', $limit = '', $meta=false, $inc_form=true){
        global $wpdb;

        $limit = FrmAppHelper::esc_limit($limit);

        $cache_key = maybe_serialize($where) . $order_by . $limit . $inc_form;
        $entries = wp_cache_get($cache_key, 'frm_entry');

        if ( false === $entries ) {
            if ( $inc_form ) {
                $query = "SELECT it.*, fr.name as form_name,fr.form_key as form_key
                    FROM {$wpdb->prefix}frm_items it LEFT OUTER JOIN {$wpdb->prefix}frm_forms fr ON it.form_id=fr.id" .
                    FrmAppHelper::prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
            } else {
                $query = "SELECT it.id, it.item_key, it.name, it.ip, it.form_id, it.post_id, it.user_id, it.updated_by,
                    it.created_at, it.updated_at, it.is_draft FROM {$wpdb->prefix}frm_items it" .
                    FrmAppHelper::prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
            }

            if ( preg_match( '/ meta_([0-9]+)/', $order_by, $order_matches ) ) {
    		    // sort by a requested field
    		    $query = str_replace( " FROM {$wpdb->prefix}frm_items ", ", (SELECT meta_value FROM {$wpdb->prefix}frm_item_metas WHERE field_id = {$order_matches[1]} AND item_id = it.id) as meta_{$order_matches[1]} FROM {$wpdb->prefix}frm_items ", $query );
		    }

            $entries = $wpdb->get_results($query, OBJECT_K);
            unset($query);

            wp_cache_set($cache_key, $entries, 'frm_entry', 300);
        }

        if ( !$meta || !$entries ) {
            return stripslashes_deep($entries);
        }
        unset($meta);

        if ( !is_array($where) && preg_match('/^it\.form_id=\d+$/', $where) ) {
            $where = array('it.form_id' => substr($where, 11));
        }

        if ( $limit == '' && is_array($where) && count($where) == 1 && isset($where['it.form_id']) ) {
            $meta_where = $wpdb->prepare('fi.form_id=%d', $where['it.form_id']);
        } else {
            $meta_where = "item_id in (". implode(',', array_filter(array_keys($entries), 'is_numeric')) .")";
        }

        $query = "SELECT item_id, meta_value, field_id, field_key, form_id FROM {$wpdb->prefix}frm_item_metas it
            LEFT OUTER JOIN {$wpdb->prefix}frm_fields fi ON it.field_id=fi.id
            WHERE $meta_where and field_id != 0";

        $cache_key = 'metas_'. sanitize_title_with_dashes($meta_where);
        $metas = FrmAppHelper::check_cache($cache_key, 'frm_entry', $query, 'get_results');
        unset($query, $cache_key);

        if ( ! $metas ) {
            return stripslashes_deep($entries);
        }

        foreach ( $metas as $m_key => $meta_val ) {
            if ( !isset($entries[$meta_val->item_id]) ) {
                continue;
            }

            if ( !isset($entries[$meta_val->item_id]->metas) ) {
                $entries[$meta_val->item_id]->metas = array();
            }

            $entries[$meta_val->item_id]->metas[$meta_val->field_id] = maybe_unserialize($meta_val->meta_value);

            unset($m_key, $meta_val);
        }

        foreach ( $entries as $entry ) {
            wp_cache_set( $entry->id, $entry, 'frm_entry');
            unset($entry);
        }

        return stripslashes_deep($entries);
    }

    // Pagination Methods
    function getRecordCount($where=''){
        global $wpdb;
        $cache_key = 'count_'. maybe_serialize($where);

        if ( is_numeric($where) ) {
            $query = $wpdb->prepare('SELECT COUNT(*) FROM '. $wpdb->prefix .'frm_items WHERE form_id=%d', $where);
        }else{
            $query = 'SELECT COUNT(*) FROM '. $wpdb->prefix .'frm_items it LEFT OUTER JOIN '. $wpdb->prefix .'frm_forms fr ON it.form_id=fr.id' .
                FrmAppHelper::prepend_and_or_where(' WHERE ', $where);
        }

        $count = FrmAppHelper::check_cache($cache_key, 'frm_entry', $query, 'get_var');

        return $count;
    }

    function getPageCount($p_size, $where=''){
        if ( is_numeric($where) ) {
            return ceil( (int) $where / (int) $p_size );
        } else {
            return ceil( (int) $this->getRecordCount($where) / (int) $p_size );
        }
    }

    function validate( $values, $exclude=false ){
        global $wpdb, $frm_field, $frm_entry_meta;

        $errors = array();

        if ( ! isset($values['form_id']) || ! isset($values['item_meta']) ) {
            $errors['form'] = __('There was a problem with your submission. Please try again.', 'formidable');
            return $errors;
        }

        if ( is_admin() && is_user_logged_in() && ( ! isset($values['frm_submit_entry_'. $values['form_id']]) || ! wp_verify_nonce($values['frm_submit_entry_'. $values['form_id']], 'frm_submit_entry_nonce') ) ) {
            $errors['form'] = __('You do not have permission to do that', 'formidable');
        }

        if ( ! isset($values['item_key']) || $values['item_key'] == '' ) {
            $_POST['item_key'] = $values['item_key'] = FrmAppHelper::get_unique_key('', $wpdb->prefix .'frm_items', 'item_key');
        }

        $where = apply_filters('frm_posted_field_ids', $wpdb->prepare('fi.form_id=%d', $values['form_id']));
        if ( $exclude ) {
            $where .= " and fi.type not in ('". implode("','", array_filter($exclude, 'esc_sql')) ."')";
        }

        $posted_fields = $frm_field->getAll($where, 'field_order');

        //Set Radio button and checkbox meta equal to "other" value, if it is set
        $values = FrmEntriesHelper::set_other_vals( $values );

        foreach ( $posted_fields as $posted_field ) {
            self::validate_field($posted_field, $errors, $values);
            unset($posted_field);
        }


        // check for spam
        $this->spam_check($exclude, $values, $errors);

        $errors = apply_filters('frm_validate_entry', $errors, $values);

        return $errors;
    }

    function validate_field($posted_field, &$errors, $values, $args = array()) {
        $defaults = array(
            'id'    => $posted_field->id,
            'parent_field_id' => '', // the id of the repeat or embed form
            'key_pointer' => '', // the pointer in the posted array
        );
        $args = wp_parse_args( $args, $defaults );

        // Set values for "other" option in repeated fields
        $values = FrmEntriesHelper::set_other_repeating_vals( $values, $posted_field );

        if ( empty($args['parent_field_id']) ) {
            $value = isset($values['item_meta'][$args['id']]) ? $values['item_meta'][$args['id']] : '';
        } else {
            // value is from a nested form
            $value = $values;
        }

        if ( isset($posted_field->field_options['default_blank']) && $posted_field->field_options['default_blank'] && $value == $posted_field->default_value ) {
            $value = '';
        }

        // Check for an array with only one value or no values
        if ( is_array($value) ) {
            // If the field has an "other" option
            if ( isset( $posted_field->field_options['other'] ) && $posted_field->field_options['other'] ) {
                if ( count( array_filter( $value) ) == 0 ) {
                    $value = '';
                }
            } else if ( count( $value ) == 1 ) {
                $value = reset($value);
            }
        }

        if ( $posted_field->required == '1' && !is_array($value) && trim($value) == '' ) {
            $frm_settings = FrmAppHelper::get_settings();
            $errors['field'. $args['id']] = (!isset($posted_field->field_options['blank']) || $posted_field->field_options['blank'] == '') ? $frm_settings->blank_msg : $posted_field->field_options['blank'];
        } else if ( $posted_field->type == 'text' && !isset($_POST['name']) ) {
            $_POST['name'] = $value;
        }

        $this->validate_url_field($errors, $posted_field, $value, $args);
        $this->validate_email_field($errors, $posted_field, $value, $args);

        FrmEntriesHelper::set_posted_value($posted_field, $value, $args);

        $this->validate_recaptcha($errors, $posted_field);

        $errors = apply_filters('frm_validate_field_entry', $errors, $posted_field, $value, $args);
    }

    function validate_url_field(&$errors, $field, &$value, $args) {
        if ( $value == '' || ! in_array($field->type, array('website', 'url', 'image')) ) {
            return;
        }

        if ( trim($value) == 'http://' ) {
            $value = '';
        } else {
            $value = esc_url_raw( $value );
            $value = preg_match('/^(https?|ftps?|mailto|news|feed|telnet):/is', $value) ? $value : 'http://'. $value;
        }

        //validate the url format
        if ( ! preg_match('/^http(s)?:\/\/([\da-z\.-]+)\.([\da-z\.-]+)/i', $value) ) {
            $errors['field'. $args['id']] = FrmFieldsHelper::get_error_msg($field, 'invalid');
        }
    }

    function validate_email_field(&$errors, $field, $value, $args) {
        if ( $value == '' || $field->type != 'email' ) {
            return;
        }

        //validate the email format
        if ( ! is_email($value) ) {
            $errors['field'. $args['id']] = FrmFieldsHelper::get_error_msg($field, 'invalid');
        }
    }

    function validate_recaptcha(&$errors, $field) {
        if ( $field->type != 'captcha' || ! isset($_POST['recaptcha_challenge_field']) ) {
            return;
        }

        $frm_settings = FrmAppHelper::get_settings();

        if ( ! function_exists('recaptcha_check_answer') ) {
            require(FrmAppHelper::plugin_path().'/classes/recaptchalib.php');
        }

        $response = recaptcha_check_answer($frm_settings->privkey,
                                        $_SERVER['REMOTE_ADDR'],
                                        $_POST['recaptcha_challenge_field'],
                                        $_POST['recaptcha_response_field']);

        if ( ! $response->is_valid ) {
            // What happens when the CAPTCHA was entered incorrectly
            $errors['captcha-'. $response->error] = $errors['field'. $args['id']] = ( ! isset($field->field_options['invalid']) || $field->field_options['invalid'] == '' ) ? $frm_settings->re_msg : $field->field_options['invalid'];
        }
    }

    /*
    * check for spam
    */
    function spam_check($exclude, $values, &$errors) {
        if ( ! empty($exclude) || ! isset($values['item_meta']) || empty($values['item_meta']) || ! empty($errors) ) {
            // only check spam if there are no other errors
            return;
        }

        global $wpcom_api_key;
        if ( ( function_exists( 'akismet_http_post' ) || is_callable('Akismet::http_post') ) && ( get_option('wordpress_api_key') || $wpcom_api_key ) && $this->akismet($values) ) {
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($values['form_id']);

            if ( isset($form->options['akismet']) && ! empty($form->options['akismet']) && ( $form->options['akismet'] != 'logged' || ! is_user_logged_in() ) ) {
	            $errors['spam'] = __('Your entry appears to be spam!', 'formidable');
	        }
	    }

	    // check for blacklist keys
    	if ( $this->blacklist_check($values) ) {
            $errors['spam'] = __('Your entry appears to be spam!', 'formidable');
    	}
    }

    // check the blacklisted words
    function blacklist_check( $values ) {
        if ( ! apply_filters('frm_check_blacklist', true, $values) ) {
            return false;
        }

    	$mod_keys = trim( get_option( 'blacklist_keys' ) );

    	if ( empty( $mod_keys ) ) {
    		return false;
    	}

    	$content = FrmEntriesHelper::entry_array_to_string($values);

		if ( empty($content) ) {
		    return false;
		}

    	$words = explode( "\n", $mod_keys );

    	foreach ( (array) $words as $word ) {
    		$word = trim( $word );

    		if ( empty($word) ) {
    			continue;
    		}

    		if ( preg_match('#' . preg_quote( $word, '#' ) . '#', $content) ) {
    			return true;
    		}
    	}

    	return false;
    }

    //Check entries for spam -- returns true if is spam
    function akismet($values) {
	    $content = FrmEntriesHelper::entry_array_to_string($values);

		if ( empty($content) ) {
		    return false;
		}

        $datas = array();
		$datas['blog'] = FrmAppHelper::site_url();
		$datas['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$datas['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$datas['referrer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
		$datas['comment_type'] = 'formidable';
		if ( $permalink = get_permalink() )
			$datas['permalink'] = $permalink;

		$datas['comment_content'] = $content;

		foreach ( $_SERVER as $key => $value ) {
			if ( !in_array($key, array('HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW')) && is_string($value) ) {
				$datas["$key"] = $value;
			} else {
			    $datas["$key"] = '';
			}

			unset($key, $value);
		}

		$query_string = '';
		foreach ( $datas as $key => $data ) {
			$query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';
			unset($key, $data);
		}

        if ( is_callable('Akismet::http_post') ) {
            $response = Akismet::http_post($query_string, 'comment-check');
        } else {
            global $akismet_api_host, $akismet_api_port;
            $response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
        }

		return ( is_array($response) and $response[1] == 'true' ) ? true : false;
    }

}
