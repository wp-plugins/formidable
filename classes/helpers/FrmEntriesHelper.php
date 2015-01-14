<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmEntriesHelper{

    public static function setup_new_vars($fields, $form='', $reset=false){
        global $frm_vars;
        $values = array();
        foreach ( array('name' => '', 'description' => '', 'item_key' => '') as $var => $default ) {
            $values[$var] = FrmAppHelper::get_post_param($var, $default);
        }

        $values['fields'] = array();
        if ( empty($fields) ) {
            return apply_filters('frm_setup_new_entry', $values);
        }

        foreach ( (array) $fields as $field ) {
            $default = $field->default_value;
            $posted_val = false;
            $new_value = $default;

            if ( ! $reset && $_POST && isset($_POST['item_meta'][$field->id]) && $_POST['item_meta'][$field->id] != '' ) {
                $new_value = stripslashes_deep($_POST['item_meta'][$field->id]);
                $posted_val = true;
            } else if ( isset($field->field_options['clear_on_focus']) && $field->field_options['clear_on_focus'] ) {
                $new_value = '';
            }

            $is_default = ($new_value == $default) ? true : false;

    		//If checkbox, multi-select dropdown, or checkbox data from entries field, set return array to true
            $return_array = FrmFieldsHelper::is_field_with_multiple_values( $field );

            $field->default_value = apply_filters('frm_get_default_value', $field->default_value, $field, true, $return_array);

            if ( !is_array($new_value) ) {
                if ( $is_default ) {
                    $new_value = $field->default_value;
                } else if ( !$posted_val ) {
                    $new_value = apply_filters('frm_filter_default_value', $new_value, $field);
                }

                $new_value = str_replace('"', '&quot;', $new_value);
            }

            unset($is_default, $posted_val);

            $field_array = array(
                'id' => $field->id,
                'value' => $new_value,
                'default_value' => $field->default_value,
                'name' => $field->name,
                'description' => $field->description,
                'type' => apply_filters('frm_field_type', $field->type, $field, $new_value),
                'options' => $field->options,
                'required' => $field->required,
                'field_key' => $field->field_key,
                'field_order' => $field->field_order,
                'form_id' => $field->form_id
            );

            $opt_defaults = FrmFieldsHelper::get_default_field_opts($field_array['type'], $field, true);
            $opt_defaults['required_indicator'] = '';

            foreach ($opt_defaults as $opt => $default_opt){
                $field_array[$opt] = (isset($field->field_options[$opt]) && $field->field_options[$opt] != '') ? $field->field_options[$opt] : $default_opt;
                unset($opt, $default_opt);
            }

            unset($opt_defaults);

            $field_array['size'] = FrmAppHelper::get_field_size($field_array);

            if ( $field_array['custom_html'] == '' ) {
                $field_array['custom_html'] = FrmFieldsHelper::get_default_html($field->type);
            }

            $field_array = apply_filters('frm_setup_new_fields_vars', $field_array, $field);

            foreach ( (array) $field->field_options as $k => $v ) {
                if ( ! isset($field_array[$k]) ) {
                        $field_array[$k] = $v;
                }
                unset($k, $v);
            }

            $values['fields'][] = $field_array;

            if ( ! $form || ! isset($form->id) ) {
                $form = FrmForm::getOne($field->form_id);
            }
        }

        $form->options = maybe_unserialize($form->options);
        if ( is_array($form->options) ) {
            foreach ( $form->options as $opt => $value ) {
                $values[$opt] = FrmAppHelper::get_post_param($opt, $value);
            }
        }

        $frm_settings = FrmAppHelper::get_settings();

        $form_defaults = array(
            'custom_style'  => ($frm_settings->load_style != 'none'),
            'email_to'      => '',
            'submit_value'  => $frm_settings->submit_value,
            'success_msg'   => $frm_settings->success_msg,
            'akismet'       => '',
            'form_class'    => '',
        );

        $values = array_merge($form_defaults, $values);

        if ( ! isset($values['before_html']) ) {
            $values['before_html'] = FrmFormsHelper::get_default_html('before');
        }

        if ( ! isset($values['after_html']) ) {
            $values['after_html'] = FrmFormsHelper::get_default_html('after');
        }

        if ( ! isset($values['submit_html']) ) {
            $values['submit_html'] = FrmFormsHelper::get_default_html('submit');
        }

        return apply_filters('frm_setup_new_entry', $values);
    }

    public static function setup_edit_vars($values, $record){
        //$values['description'] = maybe_unserialize( $record->description );
        $values['item_key'] = isset($_POST['item_key']) ? $_POST['item_key'] : $record->item_key;
        $values['form_id'] = $record->form_id;
        $values['is_draft'] = $record->is_draft;
        return apply_filters('frm_setup_edit_entry_vars', $values, $record);
    }

    public static function get_admin_params($form=null){
        $form_id = $form;
        if ( $form === null ) {
            $form_id = self::get_current_form_id();
        } else if ( $form && is_object($form) ) {
            $form_id = $form->id;
        }

        $values = array();
        foreach ( array(
            'id' => '', 'form_name' => '', 'paged' => 1, 'form' => $form_id,
            'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'fid' => '',
            'keep_post' => ''
        ) as $var => $default ) {
            $values[$var] = FrmAppHelper::get_param($var, $default);
        }

        return $values;
    }

    public static function set_current_form($form_id){
        global $frm_vars, $wpdb;

        $query = $wpdb->prepare('is_template=%d AND (status is NULL OR status = %s OR status = %s)', '0', '', 'published');
        if ( $form_id ) {
            $query .= $wpdb->prepare(' AND id = %d', $form_id);
        }
        $frm_vars['current_form'] = FrmForm::getAll($query, 'name', 1);

        return $frm_vars['current_form'];
    }

    public static function get_current_form($form_id = 0) {
        global $frm_vars, $wpdb;

        if ( isset($frm_vars['current_form']) && $frm_vars['current_form'] ) {
            if ( ! $form_id || $form_id == $frm_vars['current_form']->id ) {
                return $frm_vars['current_form'];
            }
        }

        $form_id = FrmAppHelper::get_param('form', $form_id);
        return self::set_current_form($form_id);
    }

    public static function get_current_form_id(){
        $form = self::get_current_form();
        $form_id = $form ? $form->id : 0;

        return $form_id;
    }

    /*
    * If $entry is numeric, get the entry object
    */
    public static function maybe_get_entry( &$entry ) {
        if ( $entry && is_numeric($entry) ) {
            $entry = FrmEntry::getOne($entry);
        }
    }

    public static function fill_entry_values($atts, $f, array &$values) {
        if ( FrmFieldsHelper::is_no_save_field($f->type) ) {
            return;
        }

        if ( $atts['default_email'] ) {
            $values[$f->id] = array('label' => '['. $f->id .' show=field_label]', 'val' => '['. $f->id .']');
            return;
        }

        //Remove signature from default-message shortcode
        if ( $f->type == 'signature' ) {
            return;
        }

        if ( $atts['entry'] && !isset($atts['entry']->metas[$f->id]) ) {
            // In case include_blank is set
            $atts['entry']->metas[$f->id] = '';

            if ( FrmAppHelper::pro_is_installed() ) {
                // If field is a post field
                if ( $atts['entry']->post_id  && ( $f->type == 'tag' || (isset($f->field_options['post_field']) && $f->field_options['post_field'])) ) {
                    $p_val = FrmProEntryMetaHelper::get_post_value($atts['entry']->post_id, $f->field_options['post_field'], $f->field_options['custom_field'], array(
                        'truncate' => (($f->field_options['post_field'] == 'post_category') ? true : false),
                        'form_id' => $atts['entry']->form_id, 'field' => $f, 'type' => $f->type,
                        'exclude_cat' => (isset($f->field_options['exclude_cat']) ? $f->field_options['exclude_cat'] : 0)
                    ));
                    if ( $p_val != '' ) {
                        $atts['entry']->metas[$f->id] = $p_val;
                    }
                }

                // If field is in a repeating section
                if ( $atts['entry']->form_id != $f->form_id ) {
                    // get entry ids linked through repeat field or embeded form
                    $child_entries = FrmProEntry::get_sub_entries($atts['entry']->id, true);
                    $val = FrmProEntryMetaHelper::get_sub_meta_values($child_entries, $f);
                    if ( !empty( $val ) ) {
                        $atts['entry']->metas[$f->id] = $val;
                    }
                }
            }

            // Don't include blank values
            if ( ! $atts['include_blank'] && FrmAppHelper::is_empty_value( $atts['entry']->metas[$f->id] ) ) {
                return;
            }
        }

        $val = '';
        if ( $atts['entry'] ) {
            $prev_val = maybe_unserialize($atts['entry']->metas[$f->id]);
            $meta = array('item_id' => $atts['id'], 'field_id' => $f->id, 'meta_value' => $prev_val, 'field_type' => $f->type);

            //This filter applies to the default-message shortcode and frm-show-entry shortcode only
            $val = apply_filters('frm_email_value', $prev_val, (object) $meta, $atts['entry']);
        }

        self::textarea_display_value( $val, $f->type, $atts['plain_text'] );

        if ( is_array($val) && $atts['format'] == 'text' ) {
            $val = implode(', ', $val);
        }

        if ( $atts['format'] != 'text' ) {
            $values[$f->field_key] = $val;
        } else {
            $values[$f->id] = array('label' => $f->name, 'val' => $val);
        }
    }

    /*
    * Replace returns with HTML line breaks for display
    * @ since 2.0
    */
    public static function textarea_display_value( &$value, $type, $plain_text ) {
        if ( $type == 'textarea' && ! $plain_text ) {
            $value = str_replace(array("\r\n", "\r", "\n"), ' <br/>', $value);
        }
    }

    public static function fill_entry_user_info($atts, array &$values) {
        if ( ! $atts['user_info'] ) {
            return;
        }

        if ( isset($atts['entry']->description) ) {
            $data = maybe_unserialize($atts['entry']->description);
        } else if ( $atts['default_email'] ) {
            $atts['entry']->ip = '[ip]';
            $data = array(
                'browser' => '[browser]',
                'referrer' => '[referrer]',
            );
        } else {
            $data = array(
                'browser' => '',
                'referrer' => '',
            );
        }

        if ( $atts['format'] != 'text' ) {
            $values['ip'] = $atts['entry']->ip;
            $values['browser'] = $data['browser'];
            $values['referrer'] = $data['referrer'];
        } else {
            //$content .= "\r\n\r\n" . __('User Information', 'formidable') ."\r\n";
            $values['ip'] = array('label' => __('IP Address', 'formidable'), 'val' => $atts['entry']->ip);
            $values['browser'] = array('label' => __('User-Agent (Browser/OS)', 'formidable'), 'val' => $data['browser']);
            $values['referrer'] = array('label' => __('Referrer', 'formidable'), 'val' => $data['referrer']);
        }
    }

    public static function convert_entry_to_content($values, $atts, array &$content) {

        if ( $atts['plain_text'] ) {
            $bg_color_alt = $row_style = '';
        } else {
            $default_settings = apply_filters('frm_show_entry_styles', array(
                'border_color' => 'dddddd',
                'bg_color' => 'f7f7f7',
                'text_color' => '444444',
                'font_size' => '12px',
                'border_width' => '1px',
                'alt_bg_color' => 'ffffff',
            ) );

            // merge defaults, global settings, and shortcode options
            foreach ( $default_settings as $key => $setting ) {
                if ( $atts[$key] != '' ) {
                    continue;
                }

                $atts[$key] = $setting;
                unset($key, $setting);
            }

            unset($default_settings);

            $content[] = '<table cellspacing="0" style="font-size:'. $atts['font_size'] .';line-height:135%; border-bottom:'. $atts['border_width'] .' solid #'. $atts['border_color'] .';"><tbody>'."\r\n";
            $atts['bg_color'] = ' style="background-color:#'. $atts['bg_color'] .';"';
            $bg_color_alt = ' style="background-color:#'. $atts['alt_bg_color'] .';"';
            $row_style = 'style="text-align:'. ( $atts['direction'] == 'rtl' ? 'right' : 'left' ) .';color:#'. $atts['text_color'] .';padding:7px 9px;border-top:'. $atts['border_width'] .' solid #'. $atts['border_color'] .'"';
        }

        $odd = true;
        foreach ( $values as $id => $value ) {
            if ( $atts['plain_text'] ) {
                if ( 'rtl' == $atts['direction'] ) {
                    $content[] =  $value['val'] . ' :'. $value['label'] ."\r\n";
                } else {
                    $content[] = $value['label'] . ': '. $value['val'] ."\r\n";
                }
                continue;
            }

            if ( $atts['default_email'] && is_numeric($id) ) {
                $content[] = '[if '. $id .']<tr style="[frm-alt-color]">';
            } else {
                $content[] = '<tr'. ( $odd ? $atts['bg_color'] : $bg_color_alt ) .'>';
            }

            if ( 'rtl' == $atts['direction'] ) {
                $content[] = '<td '. $row_style .'>'. $value['val'] .'</td><th '. $row_style .'>'. $value['label'] . '</th>';
            } else {
                $content[] = '<th '. $row_style .'>'. $value['label'] .'</th><td '. $row_style .'>'. $value['val'] .'</td>';
            }
            $content[] = '</tr>'. "\r\n";

            if ( $atts['default_email'] && is_numeric($id) ) {
                $content[] = '[/if '. $id .']';
            }
            $odd = $odd ? false : true;
        }

        if ( ! $atts['plain_text'] ) {
            $content[] = '</tbody></table>';
        }
    }

    public static function replace_default_message($message, $atts) {
        if ( strpos($message, '[default-message') === false &&
            strpos($message, '[default_message') === false &&
            !empty($message) ) {
            return $message;
        }

        if ( empty($message) ) {
            $message = '[default-message]';
        }

        preg_match_all("/\[(default-message|default_message)\b(.*?)(?:(\/))?\]/s", $message, $shortcodes, PREG_PATTERN_ORDER);

        foreach ( $shortcodes[0] as $short_key => $tag ) {
            $add_atts = shortcode_parse_atts( $shortcodes[2][$short_key] );
            if ( $add_atts ){
                $this_atts = array_merge($atts, $add_atts);
            } else {
                $this_atts = $atts;
            }

            $default = FrmEntriesController::show_entry_shortcode($this_atts);

            // Add the default message
            $message = str_replace($shortcodes[0][$short_key], $default, $message);
        }

        return $message;
    }

    public static function prepare_display_value($entry, $field, $atts) {
		$field_value = isset($entry->metas[$field->id]) ? $entry->metas[$field->id] : false;
        if ( FrmAppHelper::pro_is_installed() ) {
		    FrmProEntriesHelper::get_dfe_values($field, $entry, $field_value);
        }

        if ( $field->form_id == $entry->form_id || empty($atts['embedded_field_id']) ) {
            return self::display_value($field_value, $field, $atts);
        }

        // this is an embeded form
        $val = '';

	    if ( strpos($atts['embedded_field_id'], 'form') === 0 ) {
            //this is a repeating section
            $child_entries = FrmEntry::getAll( array('it.parent_item_id' => $entry->id) );
        } else {
            // get all values for this field
	        $child_values = isset($entry->metas[$atts['embedded_field_id']]) ? $entry->metas[$atts['embedded_field_id']] : false;

            if ( $child_values ) {
	            $child_entries = FrmEntry::getAll('it.id in ('. implode(',', array_filter( (array) $child_values, 'is_numeric') ) .')');
	            //$atts['post_id']
	        }
	    }

	    $field_value = array();

        if ( ! isset($child_entries) || ! $child_entries || ! FrmAppHelper::pro_is_installed() ) {
            return $val;
        }

        foreach ( $child_entries as $child_entry ) {
            $atts['item_id'] = $child_entry->id;
            $atts['post_id'] = $child_entry->post_id;

            // get the value for this field -- check for post values as well
            $entry_val = FrmProEntryMetaHelper::get_post_or_meta_value($child_entry, $field);

            if ( $entry_val ) {
                // foreach entry get display_value
                $field_value[] = self::display_value($entry_val, $field, $atts);
            }

            unset($child_entry);
        }

        $val = implode(', ', (array) $field_value );

        return $val;
    }

    /*
    * Prepare the saved value for display
    * @return string
    */
    public static function display_value($value, $field, $atts = array()) {

        $defaults = array(
            'type' => '', 'html' => false, 'show_filename' => true,
            'truncate' => false, 'sep' => ', ', 'post_id' => 0,
            'form_id' => $field->form_id, 'field' => $field, 'keepjs' => 0,
        );

        $atts = wp_parse_args( $atts, $defaults );
        $atts = apply_filters('frm_display_value_atts', $atts, $field, $value);

        if ( ! isset($field->field_options['post_field']) ) {
            $field->field_options['post_field'] = '';
        }

        if ( ! isset($field->field_options['custom_field']) ) {
            $field->field_options['custom_field'] = '';
        }

        if ( FrmAppHelper::pro_is_installed() && $atts['post_id'] && ( $field->field_options['post_field'] || $atts['type'] == 'tag' ) ) {
            $atts['pre_truncate'] = $atts['truncate'];
            $atts['truncate'] = true;
            $atts['exclude_cat'] = isset($field->field_options['exclude_cat']) ? $field->field_options['exclude_cat'] : 0;

            $value = FrmProEntryMetaHelper::get_post_value($atts['post_id'], $field->field_options['post_field'], $field->field_options['custom_field'], $atts);
            $atts['truncate'] = $atts['pre_truncate'];
        }

        if ( $value == '' ) {
            return $value;
        }

        $value = apply_filters('frm_display_value_custom', maybe_unserialize($value), $field, $atts);

        $new_value = '';

        if ( is_array($value) && $atts['type'] != 'file' ) {
            foreach ( $value as $val ) {
                if ( is_array($val) ) { //TODO: add options for display (li or ,)
                    $new_value .= implode($atts['sep'], $val);
                    if ( $atts['type'] != 'data' ) {
                        $new_value .= '<br/>';
                    }
                }
                unset($val);
            }
        }

        if ( ! empty($new_value) ) {
            $value = $new_value;
        } else if ( is_array($value) && $atts['type'] != 'file' ) {
            $value = implode($atts['sep'], $value);
        }

        if ( $atts['truncate'] && $atts['type'] != 'image' ) {
            $value = FrmAppHelper::truncate($value, 50);
        }

        return apply_filters('frm_display_value', $value, $field, $atts);
    }

    /*
    * Sets radio or checkbox value equal to "other" value if it is set
    * @since 2.0
    * @return array of updated POST values
    */
    public static function set_other_vals( $values ){
        if ( ! isset( $values['item_meta']['other'] ) ) {
            return $values;
        }

        $other_array = $values['item_meta']['other'];
        foreach ( $other_array as $f_id => $o_val ) {
            //For checkboxes
            if ( is_array( $o_val ) ) {
                foreach ( $o_val as $opt_key => $opt_val ) {
                    $_POST['item_meta'][$f_id][$opt_key] = $values['item_meta'][$f_id][$opt_key] = $opt_val;
                    unset( $opt_key, $opt_val );
                }
            //For radio buttons
            } else if ( $o_val ) {
                $_POST['item_meta'][$f_id] = $values['item_meta'][$f_id] = $o_val;
            }
        }
        unset( $_POST['item_meta']['other'] );

        return $values;
    }

    /*
    * Sets radio or checkbox value equal to "other" value if it is set - FOR REPEATING SECTIONS
    * @since 2.0
    * @return array of updated POST values
    */
    public static function set_other_repeating_vals( $values, $field ){
        if ( ( $field->type == 'divider' && $field->field_options['repeat'] ) || $field->type == 'form' ) {
            // do nothing
        } else {
            return $values;
        }

        foreach ( $values['item_meta'][$field->id] as $k => $val ) {
            if ( ! isset( $val['other'] ) || ! is_array( $val['other'] ) ) {
                continue;
            }

            foreach ( $val['other'] as $sub_fid => $o_val ) {

                //For checkboxes
                if ( is_array( $o_val ) ) {
                    foreach ( $o_val as $opt_key => $opt_val ) {
                        $values['item_meta'][$field->id][$k][$sub_fid][$opt_key] = $opt_val;
                        unset( $values['item_meta'][$field->id][$k]['other'][$sub_fid][$opt_key] );
                        unset( $opt_key, $opt_val );
                    }

                //For radio buttons
                } else if ( $o_val ) {
                    $values['item_meta'][$field->id][$k][$sub_fid] = $o_val;
                    unset( $values['item_meta'][$field->id][$k]['other'][$sub_fid] );
                }
                unset( $sub_fid, $o_val);
            }

            unset( $k, $val );
        }

        return $values;
    }

    public static function set_posted_value($field, $value, $args) {
        if ( empty($args['parent_field_id']) ) {
            $_POST['item_meta'][$field->id] = $value;
        } else {
            $_POST['item_meta'][$args['parent_field_id']][$args['key_pointer']][$field->id] = $value;
        }
    }

    public static function get_posted_value($field, &$value, $args) {
        if ( is_object( $field ) ) {
            $field_id = $field->id;
        } else {
            $field_id = $field;
        }
        if ( empty($args['parent_field_id']) ) {
            $value = isset($_POST['item_meta'][$field_id]) ? $_POST['item_meta'][$field_id] : '';
        } else {
            $value = $_POST['item_meta'][$args['parent_field_id']][$args['key_pointer']][$field_id];
        }
    }

    public static function entries_dropdown() {
        _deprecated_function( __FUNCTION__, '1.07.09');
    }

    public static function enqueue_scripts($params){
        do_action('frm_enqueue_form_scripts', $params);
    }

    // Add submitted values to a string for spam checking
    public static function entry_array_to_string($values) {
        $content = '';
		foreach ( $values['item_meta'] as $val ) {
			if ( $content != '' ) {
				$content .= "\n\n";
			}

			if ( is_array($val) ) {
			    $val = implode(',', $val);
			}

			$content .= $val;
		}

		return $content;
    }
}
