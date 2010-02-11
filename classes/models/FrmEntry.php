<?php
class FrmEntry{
    var $table_name;

    function FrmEntry(){
      global $wpdb;
      $this->table_name = "{$wpdb->prefix}frm_items";
    }

    function create( $values ){
        global $wpdb, $frm_entry_meta;
        
        $new_values = array();
        $new_values['item_key'] = FrmAppHelper::get_unique_key($values['item_key'], $this->table_name, 'item_key');
        $new_values['name'] = isset($values['name']) ? $values['name'] : $values['item_key'];
        $new_values['description'] = serialize(array('ip' => $_SERVER['REMOTE_ADDR'], 
                                                'browser' => $_SERVER['HTTP_USER_AGENT'], 
                                                'referrer' => $_SERVER['HTTP_REFERER']));
        $new_values['form_id'] = isset($values['form_id']) ? (int)$values['form_id']: null;
        //$new_values['parent_item_id'] = isset($values['parent_item_id'])?(int)$values['parent_item_id']: null;
        $new_values['created_at'] = current_time('mysql', 1);

        $query_results = $wpdb->insert( $this->table_name, $new_values );

        if($query_results){
            $entry_id = $wpdb->insert_id;
            if (isset($values['item_meta']))
                $frm_entry_meta->update_entry_metas($entry_id, $values['item_meta']);
            do_action('frm_after_create_entry', $entry_id);
            return $entry_id;
        }else
           return false;
    }
    
    function duplicate( $id ){
        global $wpdb, $frm_entry, $frm_entry_meta;

        $values = $frm_entry->getOne( $id );

        $new_values = array();
        $new_values['item_key'] = FrmAppHelper::get_unique_key('', $this->table_name, 'item_key');
        $new_values['name'] = $values->name;
        $new_values['form_id'] = ($values->form_id)?(int)$values->form_id: null;
        //$new_values['parent_item_id'] = ($values->parent_item_id)?(int)$values->parent_item_id: null;
        $new_values['created_at'] = current_time('mysql', 1);

        $query_results = $wpdb->insert( $this->table_name, $new_values );

        if($query_results){
            $frm_entry_meta->duplicate_entry_metas($id);
            return $wpdb->insert_id;
        }else
            return false;
    }

    function update( $id, $values ){
      global $wpdb, $frm_entry_meta, $frm_field;
       
      $new_values = array();
      if (isset($values['item_key']))
          $new_values['item_key'] = FrmAppHelper::get_unique_key($values['item_key'], $this->table_name, 'item_key', $id);
          
      $new_values['name'] = isset($values['name'])?$values['name']:'';
      $new_values['form_id'] = isset($values['form_id'])?(int)$values['form_id']: null;
      //$new_values['parent_item_id'] = isset($values['parent_item_id'])?(int)$values['parent_item_id']: null;

      $query_results = $wpdb->update( $this->table_name, $new_values, array( 'id' => $id ) );
      
      if (isset($values['item_meta']))
          $frm_entry_meta->update_entry_metas($id, $values['item_meta']);
      do_action('frm_after_update_entry', $id);
      return $query_results;
    }

    function destroy( $id ){
      global $wpdb, $frm_entry_meta;
      
      // Disconnect the child items from this parent item
      $query_results = $wpdb->update( $this->table_name, array('parent_item_id' => null), array( 'parent_item_id' => $id ) );

      $reset = 'DELETE FROM ' . $frm_entry_meta->table_name .  ' WHERE item_id=' . $id;
      $destroy = 'DELETE FROM ' . $this->table_name .  ' WHERE id=' . $id;

      $wpdb->query($reset);
      return $wpdb->query($destroy);
    }
    
    function update_form( $id, $value, $form_id ){
      global $wpdb;
      $form_id = isset($value) ? $form_id : NULL;
      return $wpdb->update( $this->table_name, array('form_id' => $form_id), array( 'id' => $id ) );
    }
    
    function getOne( $id ){
      global $wpdb, $frm_form;
      $query = 'SELECT it.*, ' .
                'fr.name as form_name, ' .
                'fr.form_key as form_key ' .
                'FROM '. $this->table_name . ' it ' .
                'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON it.form_id=fr.id';
      if(is_numeric($id))
        $query .= ' WHERE it.id=' . $id;
      else
        $query .= " WHERE it.item_key='" . $id ."'";
      return $wpdb->get_row($query);
    }
    
    function exists( $id ){
        global $wpdb, $frm_form;
        $query = 'SELECT id FROM '. $this->table_name;
        if(is_numeric($id))
            $query .= ' WHERE id=' . $id;
        else
            $query .= " WHERE item_key='" . $id ."'";
        $id = $wpdb->get_var($query);
        if ($id && $id > 0)
            return true;
        else
            return false;
    }

    function getAll($where = '', $order_by = '', $limit = ''){
      global $wpdb, $frm_form, $frm_app_helper;
      $query = 'SELECT it.*, ' .
                'fr.name as form_name, ' .
                'fr.form_key as form_key ' .
                'FROM '. $this->table_name . ' it ' .
                'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON it.form_id=fr.id' . 
                $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
      return $wpdb->get_results($query);
    }

    // Pagination Methods
    function getRecordCount($where=""){
      global $wpdb, $frm_app_helper, $frm_form;
      $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' it ' .
          'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON it.form_id=fr.id' .
          $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
      return $wpdb->get_var($query);
    }

    function getPageCount($p_size, $where=""){
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
    }

    function getPage($current_p,$p_size, $where = "", $order_by = ''){
      global $wpdb, $frm_app_helper, $frm_form;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $query = 'SELECT it.*, ' .
                'fr.name as form_name ' .
               'FROM ' . $this->table_name . ' it ' .
               'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON it.form_id=fr.id' . 
               $frm_app_helper->prepend_and_or_where(' WHERE', $where) . $order_by . ' ' . 
               'LIMIT ' . $start_index . ',' . $p_size . ';';
      $results = $wpdb->get_results($query);
      return $results;
    }

    function validate( $values ){
        global $wpdb, $frm_field, $frm_entry_meta;

        $errors = array();   
        
        if (!isset($values['name']) and isset($values['item_meta'])){
            foreach($values['item_meta'] as $key => $value){
                $field = $frm_field->getOne($key);
                if ($field->required == '1' and $field->type == 'text' and !isset($_POST['name']))
                    $_POST['name'] = $value;
            }
        }

        if( !isset($values['item_key']) or $values['item_key'] == '' )
            $_POST['item_key'] = FrmAppHelper::get_unique_key('', $this->table_name, 'item_key');
        
        if (isset($values['item_meta'])){    
            foreach($values['item_meta'] as $key => $value){
                $field = $frm_field->getOne($key);
                if ($field->required == '1' and ($field->form_id == $values['form_id'])){
                    $field_options = unserialize($field->field_options);
                    
                    if ($values['item_meta'][$key] == null or $values['item_meta'][$key] == '' or (isset($field_options['default_blank']) and $field_options['default_blank'] and $value == $field->default_value))
                        $errors['field'.$field->id] = ($field_options['blank'] == 'Untitled cannot be blank' || $field_options['blank'] == '')?($field->name." can't be blank"):$field_options['blank'];  
                }
                $errors = apply_filters('frm_validate_field_entry', $errors, $key, $value);
            }
                
        }

        if (isset($_POST['recaptcha_challenge_field']) and $_POST['action'] == 'create'){
            global $recaptcha_opt;

            if (empty($_POST['recaptcha_response_field']) || $_POST['recaptcha_response_field'] == '') {
         		$errors['field_captcha'] = $recaptcha_opt['error_blank'];
            }else{
         	    $response = recaptcha_check_answer($recaptcha_opt['privkey'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );

         	    if (!$response->is_valid)
         		    if ($response->error == 'incorrect-captcha-sol')
         			    $errors['field_captcha'] = $recaptcha_opt['error_incorrect'];

            }
        }
        
        if ( empty($errors) && function_exists( 'akismet_http_post' ) && (get_option('wordpress_api_key') || $wpcom_api_key) && $this->akismet($values)){
            global $frm_form;
            $form = $frm_form->getOne($field->form_id);
            $form_options = stripslashes_deep(unserialize($form->options));

            if (isset($form_options['akismet']) && $form_options['akismet'])
    	        $errors['spam'] = 'Your entry appears to be spam!';
    	}
        
      return $errors;
    }
    
    //Check entries for spam -- returns true if is spam
    function akismet($values) {
	    global $akismet_api_host, $akismet_api_port, $frm_blogurl;

		$content = '';
		foreach ( $values as $val ) {
			if ( $content != '' )
				$content .= "\n\n";
			$content .= $val;
		}
		
		if ($content == '')
		    return false;
        
        $datas = array();
		$datas['blog'] = $frm_blogurl;
		$datas['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
		$datas['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$datas['referrer'] = $_SERVER['HTTP_REFERER'];
		$datas['comment_type'] = 'formidable';
		if ( $permalink = get_permalink() )
			$datas['permalink'] = $permalink;

		$datas['comment_content'] = $content;

		foreach ( $_SERVER as $key => $value )
			if ( !in_array($key, array('HTTP_COOKIE', 'argv')) )
				$datas["$key"] = $value;

		$query_string = '';
		foreach ( $datas as $key => $data )
			$query_string .= $key . '=' . urlencode( stripslashes( $data ) ) . '&';

		$response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port );
		return ( $response[1] == 'true' ) ? true : false;
    }
    
}
?>