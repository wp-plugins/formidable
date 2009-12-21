<?php

class FrmAppHelper{
    function FrmAppHelper(){}
    
    function get_pages(){
      return get_posts( array('post_type' => 'page', 'post_status' => 'published', 'numberposts' => 99, 'order_by' => 'post_title', 'order' => 'ASC'));
    }
  
    function value_is_selected($field_name, $field_value, $selected_value){
      if( (isset($_POST[$field_name]) and $_POST[$field_name] == $selected_value) or (!isset($_POST[$field_name]) and $field_value == $selected_value) )
        echo ' selected="selected"';
    }

    function value_is_checked($field_name, $field_value){
      if( (isset($_POST) and $_POST[$field_name] == '1') or (!isset($_POST) and $field_value == '1') )
        echo ' checked="checked"';
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
            
        $query = "SELECT $column FROM $table_name WHERE $column = %s AND ID != %d LIMIT 1";
        $key_check = $wpdb->get_var($wpdb->prepare($query, $key, $id));
        if ($key_check){
            $suffix = 2;
			do {
				$alt_post_name = substr($key, 0, 200-(strlen($suffix)+1)). "$suffix";
				$key_check = $wpdb->get_var($wpdb->prepare($query, $alt_post_name, $id));
				$suffix++;
			} while ($key_check);
			$key = $alt_post_name;
        }
        return $key;
    }

    function setup_edit_vars($record, $table, $fields='', $default=false){
        if(!$record) return false;
        global $frm_entry_meta, $frm_form, $frm_app_controller;
        $values = array();

        $values['id'] = $record->id;

        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default_val)
              $values[$var] = stripslashes($frm_app_controller->get_param($var, $default_val));

        $values['form_name'] = ($record->form_id)?($frm_form->getName( $record->form_id )):('');

        $values['fields'] = array();
        if ($fields){ 
            foreach($fields as $field){

                if ($default)
                    $meta_value = $field->default_value;
                else
                    $meta_value = $frm_entry_meta->get_entry_meta_by_field($record->id, $field->id, true);

                $field_options = stripslashes_deep(unserialize($field->field_options));
                
                $new_value = (isset($_POST['item_meta'][$field->id])) ? $_POST['item_meta'][$field->id] : $meta_value;
                if ($field->type != 'checkbox')
                    $new_value = stripslashes($new_value);
                    
                $field_array = array('id' => $field->id,
                      'value' => $new_value,
                      'default_value' => stripslashes($field->default_value),
                      'name' => stripslashes($field->name),
                      'description' => stripslashes($field->description),
                      'type' => apply_filters('frm_field_type',$field->type, $field),
                      'options' => stripslashes_deep(unserialize($field->options)),
                      'required' => $field->required,
                      'field_key' => $field->field_key,
                      'field_order' => $field->field_order,
                      'form_id' => $field->form_id);

                foreach (array('size' => 75,'max' => '','label' => 'top','invalid' => '','required_indicator' => '*','blank' => '') as $opt => $default_opt)
                    $field_array[$opt] = ($_POST and isset($_POST['field_options'][$opt.'_'.$field->id]) ) ? $_POST['field_options'][$opt.'_'.$field->id] : (isset($field_options[$opt]) ? $field_options[$opt]: $default_opt);
                  
               $values['fields'][] = apply_filters('frm_setup_edit_fields_vars', $field_array, $field);   
            }
      }
      
      if ($table == 'entries')
          $values = FrmEntriesHelper::setup_edit_vars( $values, $record );
      else if ($table == 'forms')
          $values = FrmFormsHelper::setup_edit_vars( $values, $record );

      return $values;
    }
    
    function frm_get_main_message( $message = ''){
      /*
      global $frmpro_is_installed;
      include_once(ABSPATH."/wp-includes/class-IXR.php");

      if($frmpro_is_installed){
        $client = new IXR_Client('http://prettylinkpro.com/xmlrpc.php');
        if ($client->query('prlipro.get_main_message'))
          $message = $client->getResponse();
      }else{
        $client = new IXR_Client('http://blairwilliams.com/xmlrpc.php');
        if ($client->query('prli.get_main_message'))
          $message = $client->getResponse();
      } */
      return $message;
    }
    
    function display_recaptcha($errors) {
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
    
    function truncate($str, $length, $minword = 3){
        $sub = '';
        $len = 0;

        foreach (explode(' ', $str) as $word){
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);

            if (strlen($word) > $minword && strlen($sub) >= $length)
                break;
        }

        return $sub . (($len < strlen($str)) ? '...' : '');
    }
    
    function prepend_and_or_where( $starts_with = ' WHERE', $where = '' ){
      return (( $where == '' )?'':$starts_with . $where);
    }
    
    // For Pagination
    function getLastRecordNum($r_count,$current_p,$p_size){
      return (($r_count < ($current_p * $p_size))?$r_count:($current_p * $p_size));
    }

    // For Pagination
    function getFirstRecordNum($r_count,$current_p,$p_size){
      if($current_p == 1)
        return 1;
      else
        return ($this->getLastRecordNum($r_count,($current_p - 1),$p_size) + 1);
    }
    
}

?>