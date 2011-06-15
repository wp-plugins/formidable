<?php

class FrmAppHelper{
    function FrmAppHelper(){}
    
    function get_param($param, $default=''){
        return (isset($_POST[$param])?$_POST[$param]:(isset($_GET[$param])?$_GET[$param]:$default));
    }
    
    function get_pages(){
      return get_posts( array('post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 999, 'orderby' => 'title', 'order' => 'ASC'));
    }
  
    function wp_pages_dropdown($field_name, $page_id, $truncate=false){
        $field_value = FrmAppHelper::get_param($field_name);
        $pages = FrmAppHelper::get_pages();
    ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown frm-pages-dropdown">
            <option value=""></option>
            <?php foreach($pages as $page){ ?>
                <option value="<?php echo $page->ID; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $page->ID) or (!isset($_POST[$field_name]) and $page_id == $page->ID))?' selected="selected"':''); ?>><?php echo ($truncate)? FrmAppHelper::truncate($page->post_title, $truncate) : $page->post_title; ?> </option>
            <?php } ?>
        </select>
    <?php
    }
    
    function wp_roles_dropdown($field_name, $capability){
        $field_value = FrmAppHelper::get_param($field_name);
    	$editable_roles = get_editable_roles();

    ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown frm-pages-dropdown">
            <?php foreach($editable_roles as $role => $details){ 
                $name = translate_user_role($details['name'] ); ?>
                <option value="<?php echo esc_attr($role) ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $role) or (!isset($_POST[$field_name]) and $capability == $role))?' selected="selected"':''); ?>><?php echo $name ?> </option>
            <?php } ?>
        </select>
    <?php
    }
    
    function frm_capabilities(){
        global $frmpro_is_installed;
        $cap = array(
            'frm_view_forms' => __('View Forms and Templates', 'formidable'),
            'frm_edit_forms' => __('Add/Edit Forms and Templates', 'formidable'),
            'frm_delete_forms' => __('Delete Forms and Templates', 'formidable'),
            'frm_change_settings' => __('Access this Settings Page', 'formidable')
        );
        if($frmpro_is_installed){
            $cap['frm_view_entries'] = __('View Entries from Admin Area', 'formidable');
            $cap['frm_create_entries'] = __('Add Entries from Admin Area', 'formidable');
            $cap['frm_edit_entries'] = __('Edit Entries from Admin Area', 'formidable');
            $cap['frm_delete_entries'] = __('Delete Entries from Admin Area', 'formidable');
            $cap['frm_view_reports'] = __('View Reports', 'formidable');
            $cap['frm_edit_displays'] = __('Add/Edit Custom Displays', 'formidable');
        }
        return $cap;
    }
    
    function user_has_permission($needed_role){        
        if($needed_role == '' or current_user_can($needed_role))
            return true;
            
        $roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
        foreach ($roles as $role){
        	if (current_user_can($role))
        		return true;
        	if ($role == $needed_role)
        		break;
        }
        return false;
    }
    
    function is_super_admin($user_id=false){
        if(function_exists('is_super_admin'))
            return is_super_admin($user_id);
        else
            return is_site_admin($user_id);
    }

    function value_is_checked_with_array($field_name, $index, $field_value){
      if( ( $_POST['action'] == 'process_form' and isset( $_POST[ $field_name ][ $index ] ) ) or ( $_POST['action'] != 'process_form' and isset($field_value) ) )
        echo ' checked="checked"';
    }
    
    function checked($values, $current){
        if(in_array($current, (array)$values))
            echo ' checked="checked"';
    }
    
    function esc_textarea( $text ) {
        $safe_text = str_replace('&quot;', '"', $text);
        $safe_text = htmlspecialchars( $safe_text, ENT_NOQUOTES );
    	return apply_filters( 'esc_textarea', $safe_text, $text );
    }
    
    function get_file_contents($filename){
        if (is_file($filename)){
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }
    
    function get_unique_key($name='', $table_name, $column, $id = 0, $num_chars = 6){
        global $wpdb;

        if ($name == ''){
            $max_slug_value = pow(36, $num_chars);
            $min_slug_value = 37; // we want to have at least 2 characters in the slug
            $key = base_convert( rand($min_slug_value, $max_slug_value), 10, 36 );
        }else{
            if(function_exists('sanitize_key'))
                $key = sanitize_key($name);
            else
                $key = sanitize_title_with_dashes($name);
        }

        if (is_numeric($key) or in_array($key, array('id', 'key', 'created-at', 'detaillink', 'editlink', 'siteurl', 'evenodd')))
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
        global $frm_entry_meta, $frm_form, $frm_settings, $frm_sidebar_width;
        $values = array();

        $values['id'] = $record->id;

        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default_val)
              $values[$var] = stripslashes(FrmAppHelper::get_param($var, $default_val));
        $values['description'] = wpautop($values['description']);
        $values['fields'] = array();
        
        if ($fields){
            foreach($fields as $field){
                $field->field_options = stripslashes_deep(maybe_unserialize($field->field_options));

                if ($default){
                    $meta_value = $field->default_value;
                }else{
                    if($record->post_id and class_exists('FrmProEntryMetaHelper') and isset($field->field_options['post_field']) and $field->field_options['post_field']){
                        $meta_value = FrmProEntryMetaHelper::get_post_value($record->post_id, $field->field_options['post_field'], $field->field_options['custom_field'], array('truncate' => false, 'type' => $field->type, 'form_id' => $field->form_id, 'field' => $field));
                    }else if(isset($record->metas))
                        $meta_value = isset($record->metas[$field->id]) ? $record->metas[$field->id] : false;
                    else
                        $meta_value = $frm_entry_meta->get_entry_meta_by_field($record->id, $field->id, true);
                }
                
                $field_type = isset($_POST['field_options']['type_'.$field->id]) ? $_POST['field_options']['type_'.$field->id] : $field->type;
                $new_value = (isset($_POST['item_meta'][$field->id])) ? $_POST['item_meta'][$field->id] : $meta_value;
                $new_value = stripslashes_deep(maybe_unserialize($new_value));
                  
                $field_array = array(
                    'id' => $field->id,
                    'value' => str_replace('"', '&quot;', $new_value),
                    'default_value' => str_replace('"', '&quot;', stripslashes($field->default_value)),
                    'name' => stripslashes($field->name),
                    'description' => stripslashes($field->description),
                    'type' => apply_filters('frm_field_type', $field_type, $field, $new_value),
                    'options' => str_replace('"', '&quot;', stripslashes_deep(maybe_unserialize($field->options))),
                    'required' => $field->required,
                    'field_key' => $field->field_key,
                    'field_order' => $field->field_order,
                    'form_id' => $field->form_id
                );
                
                foreach (array('size' => '', 'max' => '', 'label' => 'top', 'invalid' => '', 'required_indicator' => '*', 'blank' => '', 'clear_on_focus' => 0, 'custom_html' => '', 'default_blank' => 0) as $opt => $default_opt){
                    $field_array[$opt] = ($_POST and isset($_POST['field_options'][$opt.'_'.$field->id]) ) ? $_POST['field_options'][$opt.'_'.$field->id] : (isset($field->field_options[$opt]) ? $field->field_options[$opt] : $default_opt);
                    if($opt == 'blank' and $field_array[$opt] == ''){
                        $field_array[$opt] = __('This field cannot be blank', 'formidable');
                    }else if($opt == 'invalid' and $field_array[$opt] == ''){
                        if($field_type == 'captcha')
                            $field_array[$opt] = $frm_settings->re_msg;
                        else
                            $field_array[$opt] = $field_array['name'] . ' ' . __('is invalid', 'formidable');
                    }
                }
                    
                if ($field_array['custom_html'] == '')
                    $field_array['custom_html'] = FrmFieldsHelper::get_default_html($field_type);
                
                if ($field_array['size'] == '')
                    $field_array['size'] = $frm_sidebar_width;
                
                $values['fields'][] = apply_filters('frm_setup_edit_fields_vars', stripslashes_deep($field_array), $field, $values['id']);
                unset($field);   
            }
        }
      
        if ($table == 'entries')
            $form = $frm_form->getOne( $record->form_id );
        else if ($table == 'forms')
            $form = $frm_form->getOne( $record->id );

        if ($form){
            $form->options = maybe_unserialize($form->options);
            $values['form_name'] = (isset($record->form_id))?($form->name):('');
            if (is_array($form->options)){
                foreach ($form->options as $opt => $value)
                    $values[$opt] = FrmAppHelper::get_param($opt, $value);
            }
        }

        $email = get_option('admin_email');
        foreach (array('custom_style' => ($frm_settings->load_style != 'none'), 'email_to' => $email) as $opt => $default){
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
            $values['custom_style'] = ($_POST and isset($_POST['options']['custom_style'])) ? $_POST['options']['custom_style'] : ($frm_settings->load_style != 'none');

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
        $client = new IXR_Client($url.'xmlrpc.php',  false, 80, 15);
        
        if ($client->query('frm.get_main_message'))
            $message = $client->getResponse();

      return $message;
    }
    
    function truncate($str, $length, $minword = 3, $continue = '...'){
        $str = stripslashes(esc_attr(strip_tags($str)));
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
        if(is_numeric($where))
            return ceil((int)$where / (int)$p_size);
        else
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
    
    function get_referer_query($query) {
    	if (strpos($query, "google.")) {
    	    //$pattern = '/^.*\/search.*[\?&]q=(.*)$/';
            $pattern = '/^.*[\?&]q=(.*)$/';
    	} else if (strpos($query, "bing.com")) {
    		$pattern = '/^.*q=(.*)$/';
    	} else if (strpos($query, "yahoo.")) {
    		$pattern = '/^.*[\?&]p=(.*)$/';
    	} else if (strpos($query, "ask.")) {
    		$pattern = '/^.*[\?&]q=(.*)$/';
    	} else {
    		return false;
    	}
    	preg_match($pattern, $query, $matches);
    	$querystr = substr($matches[1], 0, strpos($matches[1], '&'));
    	return urldecode($querystr);
    }
    
    function get_referer_info(){
        $referrerinfo = '';
    	$keywords = array();
    	$i = 1;
    	if(isset($_SESSION) and isset($_SESSION['frm_http_referer']) and $_SESSION['frm_http_referer']){
        	foreach ($_SESSION['frm_http_referer'] as $referer) {
        		$referrerinfo .= str_pad("Referer $i: ",20) . $referer. "\r\n";
        		$keywords_used = FrmAppHelper::get_referer_query($referer);
        		if ($keywords_used)
        			$keywords[] = $keywords_used;

        		$i++;
        	}
	    }
    	$referrerinfo .= "\r\n";

    	$i = 1;
    	if(isset($_SESSION) and isset($_SESSION['frm_http_pages']) and $_SESSION['frm_http_pages']){
        	foreach ($_SESSION['frm_http_pages'] as $page) {
        		$referrerinfo .= str_pad("Page visited $i: ",20) . $page. "\r\n";
        		$i++;
        	}
	    }
    	$referrerinfo .= "\r\n";

    	$i = 1;
    	foreach ($keywords as $keyword) {
    		$referrerinfo .= str_pad("Keyword $i: ",20) . $keyword. "\r\n";
    		$i++;
    	}
    	$referrerinfo .= "\r\n";
    	
    	return $referrerinfo;
    }    
    
}

?>