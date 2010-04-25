<?php

class FrmAppHelper{
    function FrmAppHelper(){}
    
    function get_pages(){
      return get_posts( array('post_type' => 'page', 'post_status' => 'published', 'numberposts' => 99, 'order_by' => 'post_title', 'order' => 'ASC'));
    }
  
    function wp_pages_dropdown($field_name, $page_id){
        global $frm_app_controller;
        
        $field_value = $frm_app_controller->get_param($field_name);
        $pages = get_posts( array('post_type' => 'page', 'post_status' => 'published', 'numberposts' => 99, 'order_by' => 'post_title', 'order' => 'ASC'));
    ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown frm-pages-dropdown">
            <option value=""></option>
            <?php foreach($pages as $page){ ?>
                <option value="<?php echo $page->ID; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $page->ID) or (!isset($_POST[$field_name]) and $page_id == $page->ID))?' selected="selected"':''); ?>><?php echo $page->post_title; ?> </option>
            <?php } ?>
        </select>
    <?php
    }

    function value_is_checked_with_array($field_name, $index, $field_value){
      if( ( $_POST['action'] == 'process_form' and isset( $_POST[ $field_name ][ $index ] ) ) or ( $_POST['action'] != 'process_form' and isset($field_value) ) )
        echo ' checked="checked"';
    }
    
    function get_unique_key($name='', $table_name, $column, $id = 0,$num_chars = 6){
        global $wpdb;

        if ($name == ''){
            $max_slug_value = pow(36,$num_chars);
            $min_slug_value = 37; // we want to have at least 2 characters in the slug
            $key = base_convert( rand($min_slug_value,$max_slug_value), 10, 36 );
        }else
            $key = sanitize_title_with_dashes($name);

        if (is_numeric($key) or in_array($key, array('id','key','created-at', 'detaillink', 'editlink', 'siteurl', 'evenodd')))
            $key = $key .'a';
            
        $query = "SELECT $column FROM $table_name WHERE $column = %s AND ID != %d LIMIT 1";
        $key_check = $wpdb->get_var($wpdb->prepare($query, $key, $id));
        
        if ($key_check or is_numeric($key_check)){
            $suffix = 2;
			do {
				$alt_post_name = substr($key, 0, 200-(strlen($suffix)+1)). "$suffix";
				$key_check = $wpdb->get_var($wpdb->prepare($query, $alt_post_name, $id));
				$suffix++;
			} while ($key_check || is_numeric($key_check));
			$key = $alt_post_name;
        }
        return $key;
    }

    //Editing a Form or Entry
    function setup_edit_vars($record, $table, $fields='', $default=false){
        if(!$record) return false;
        global $frm_entry_meta, $frm_form, $frm_app_controller, $frm_settings;
        $values = array();

        $values['id'] = $record->id;

        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default_val)
              $values[$var] = stripslashes($frm_app_controller->get_param($var, $default_val));
        $values['description'] = wpautop($values['description']);
        $values['fields'] = array();
        if ($fields){
            foreach($fields as $field){

                if ($default)
                    $meta_value = $field->default_value;
                else
                    $meta_value = $frm_entry_meta->get_entry_meta_by_field($record->id, $field->id, true);

                $field_options = stripslashes_deep(unserialize($field->field_options));
                $field_type = isset($_POST['field_options']['type_'.$field->id]) ? $_POST['field_options']['type_'.$field->id] : $field->type;
                $new_value = (isset($_POST['item_meta'][$field->id])) ? $_POST['item_meta'][$field->id] : $meta_value;
                $new_value = stripslashes_deep(maybe_unserialize($new_value));
                  
                $field_array = array('id' => $field->id,
                      'value' => str_replace('"', '&quot;', $new_value),
                      'default_value' => str_replace('"', '&quot;', stripslashes($field->default_value)),
                      'name' => stripslashes($field->name),
                      'description' => stripslashes($field->description),
                      'type' => apply_filters('frm_field_type',$field_type, $field),
                      'options' => str_replace('"', '&quot;', stripslashes_deep(unserialize($field->options))),
                      'required' => $field->required,
                      'field_key' => $field->field_key,
                      'field_order' => $field->field_order,
                      'form_id' => $field->form_id);
                
                foreach (array('size' => '', 'max' => '', 'label' => 'top', 'invalid' => '', 'required_indicator' => '*', 'blank' => '', 'clear_on_focus' => 0, 'custom_html' => '', 'default_blank' => 0) as $opt => $default_opt){
                    $field_array[$opt] = ($_POST and isset($_POST['field_options'][$opt.'_'.$field->id]) ) ? $_POST['field_options'][$opt.'_'.$field->id] : (isset($field_options[$opt]) ? $field_options[$opt] : $default_opt);
                    if($opt == 'blank' and $field_array[$opt] == '')
                        $field_array[$opt] = $field_array['name'] . ' ' . __('can\'t be blank', FRM_PLUGIN_NAME);
                    else if($opt == 'invalid' and $field_array[$opt] == '')
                        $field_array[$opt] = $field_array['name'] . ' ' . __('is an invalid format', FRM_PLUGIN_NAME);
                }
                    
                if ($field_array['custom_html'] == '')
                    $field_array['custom_html'] = FrmFieldsHelper::get_default_html($field_type);

               $values['fields'][] = apply_filters('frm_setup_edit_fields_vars', stripslashes_deep($field_array), $field, $values['id']);   
            }
        }
      
        if ($table == 'entries')
            $form = $frm_form->getOne( $record->form_id );
        else if ($table == 'forms')
            $form = $frm_form->getOne( $record->id );

        if ($form){
            $values['form_name'] = (isset($record->form_id))?($form->name):('');
            $options = stripslashes_deep(unserialize($form->options));
            if (is_array($options)){
                foreach ($options as $opt => $value)
                    $values[$opt] = $frm_app_controller->get_param($opt, $value);
            }
        }

        $email = get_option('admin_email');
        foreach (array('custom_style' => $frm_settings->custom_style, 'email_to' => $email) as $opt => $default){
            if (!isset($values[$opt]))
                $values[$opt] = ($_POST and isset($_POST['options'][$opt])) ? $_POST['options'][$opt] : $default;
        }
        
        foreach (array('submit_value' => $frm_settings->submit_value, 'success_action' => 'message', 'success_msg' => $frm_settings->success_msg, 'show_form' => 1) as $opt => $default){
            if (!isset($values[$opt]) or $values[$opt] == '')
                $values[$opt] = ($_POST and isset($_POST['options'][$opt])) ? $_POST['options'][$opt] : $default;
        }
        if (!isset($values['show_form']))
            $values['show_form'] = ($_POST and isset($_POST['options']['show_form'])) ? 1 : 0;
            
        if (!isset($values['custom_style']))
            $values['custom_style'] = ($_POST and isset($_POST['options']['custom_style'])) ? $_POST['options']['custom_style'] : $frm_settings->custom_style;

        if (!isset($values['akismet']))
            $values['akismet'] = ($_POST and isset($_POST['options']['akismet'])) ? 1 : 0;

        if (!isset($values['before_html']))
            $values['before_html'] = (isset($_POST['options']['before_html']) ? $_POST['options']['before_html'] : FrmFormsHelper::get_default_html('before'));

        if (!isset($values['after_html']))
            $values['after_html'] = (isset($_POST['options']['after_html'])?$_POST['options']['after_html'] : FrmFormsHelper::get_default_html('after'));

        if ($table == 'entries')
            $values = FrmEntriesHelper::setup_edit_vars( $values, $record );
        else if ($table == 'forms')
            $values = FrmFormsHelper::setup_edit_vars( $values, $record );

        return $values;
    }
    
    function frm_get_main_message( $message = ''){
        global $frmpro_is_installed;
        include_once(ABSPATH."/wp-includes/class-IXR.php");

        $url = ($frmpro_is_installed) ? 'http://formidablepro.com/' : 'http://blog.strategy11.com/';
        $client = new IXR_Client($url.'xmlrpc.php');
        
        if ($client->query('frm.get_main_message'))
            $message = $client->getResponse();

      return $message;
    }
    
    function display_recaptcha() {
    	global $recaptcha_opt;

    	$format = <<<END
<script type='text/javascript'>var RecaptchaOptions={theme:'{$recaptcha_opt['re_theme_reg']}',lang:'{$recaptcha_opt['re_lang']}',tabindex:30};</script>
END;

    	$comment_string = <<<COMMENT_FORM
<script type='text/javascript'>document.getElementById('recaptcha_table').style.direction='ltr';</script>
COMMENT_FORM;

    	$use_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? true : false;
            
        echo $format . recaptcha_wp_get_html(isset($_GET['rerror'])?$_GET['rerror']:'', $use_ssl);
    }
    
    function truncate($str, $length, $minword = 3, $continue = '...'){
        $sub = '';
        $len = 0;

        foreach (explode(' ', $str) as $word){
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);

            if (strlen($word) > $minword && strlen($sub) >= $length)
                break;
        }

        return $sub . (($len < strlen($str)) ? $continue : '');
    }
    
    function prepend_and_or_where( $starts_with = ' WHERE ', $where = '' ){
      return (( $where == '' )?'':$starts_with . $where);
    }
    
    // Pagination Methods
    function getLastRecordNum($r_count,$current_p,$p_size){
      return (($r_count < ($current_p * $p_size))?$r_count:($current_p * $p_size));
    }

    function getFirstRecordNum($r_count,$current_p,$p_size){
      if($current_p == 1)
        return 1;
      else
        return ($this->getLastRecordNum($r_count,($current_p - 1),$p_size) + 1);
    }
    
    function getRecordCount($where="", $table_name){
        global $wpdb, $frm_app_helper;
        $query = 'SELECT COUNT(*) FROM ' . $table_name . $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
        return $wpdb->get_var($query);
    }

    function getPageCount($p_size, $where="", $table_name){
        return ceil((int)$this->getRecordCount($where, $table_name) / (int)$p_size);
    }

    function getPage($current_p,$p_size, $where = "", $order_by = '', $table_name){
        global $wpdb, $frm_app_helper;
        $end_index = $current_p * $p_size;
        $start_index = $end_index - $p_size;
        $query = 'SELECT *  FROM ' . $table_name . $frm_app_helper->prepend_and_or_where(' WHERE', $where) . $order_by .' LIMIT ' . $start_index . ',' . $p_size;
        $results = $wpdb->get_results($query);
        return $results;
    }
    
}

?>