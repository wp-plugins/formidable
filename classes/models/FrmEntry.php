<?php
class FrmEntry{

    function create( $values ){
        global $wpdb, $frmdb, $frm_entry_meta;
        
        $new_values = array();
        $new_values['item_key'] = FrmAppHelper::get_unique_key($values['item_key'], $frmdb->entries, 'item_key');
        $new_values['name'] = isset($values['name']) ? $values['name'] : $values['item_key'];
        $new_values['ip'] = $_SERVER['REMOTE_ADDR'];
        
        if(isset($values['description']) and !empty($values['description']))
            $new_values['description'] = $values['description'];
        else
            $new_values['description'] = serialize(array('browser' => $_SERVER['HTTP_USER_AGENT'], 
                                                'referrer' => $_SERVER['HTTP_REFERER']));
        $new_values['form_id'] = isset($values['form_id']) ? (int)$values['form_id']: null;
        $new_values['created_at'] = current_time('mysql', 1);
        
        //if(isset($values['id']) and is_numeric($values['id']))
        //    $new_values['id'] = $values['id'];
            
        if(isset($values['frm_user_id']) and is_numeric($values['frm_user_id']))
            $new_values['user_id'] = $values['frm_user_id'];

        //check for duplicate entries created in the last 5 minutes
        $check_val = $new_values;
        $check_val['created_at >'] = date('Y-m-d H:i:s', (strtotime($new_values['created_at']) - (60*60*5))); 
        unset($check_val['created_at']);
        unset($check_val['id']);
        unset($check_val['item_key']);
        if($new_values['item_key'] == $new_values['name'])
            unset($check_val['name']);
        
        $create_entry = true;
        $entry_exists = $frmdb->get_records($frmdb->entries, $check_val, 'created_at DESC', '', 'id');
        if($entry_exists and !empty($entry_exists)){
            foreach($entry_exists as $entry_exist){
                if($create_entry){
                    $create_entry = false;
                    //add more checks here to make sure it's a duplicate
                    if (isset($values['item_meta'])){
                        $metas = FrmEntryMeta::get_entry_meta_info($entry_exist->id);
                        $field_metas = array();
                        foreach($metas as $meta)
                            $field_metas[$meta->field_id] = $meta->meta_value;
                            
                        $diff = array_diff_assoc($field_metas, $values['item_meta']);
                        foreach($diff as $field_id => $meta_value){
                            if(!empty($meta_value) and !$create_entry)
                                $create_entry = true;
                        }
                    }   
                }
            }
        }
        
        if($create_entry)
            $query_results = $wpdb->insert( $frmdb->entries, $new_values );

        if(isset($query_results) and $query_results){
            $entry_id = $wpdb->insert_id;
            if (isset($values['item_meta']))
                $frm_entry_meta->update_entry_metas($entry_id, $values['item_meta']);
            do_action('frm_after_create_entry', $entry_id, $new_values['form_id']);
            return $entry_id;
        }else
           return false;
    }
    
    function duplicate( $id ){
        global $wpdb, $frmdb, $frm_entry, $frm_entry_meta;

        $values = $frm_entry->getOne( $id );

        $new_values = array();
        $new_values['item_key'] = FrmAppHelper::get_unique_key('', $frmdb->entries, 'item_key');
        $new_values['name'] = $values->name;
        $new_values['user_id'] = $values->user_id;
        $new_values['form_id'] = ($values->form_id)?(int)$values->form_id: null;
        $new_values['created_at'] = current_time('mysql', 1);

        $query_results = $wpdb->insert( $frmdb->entries, $new_values );
        if($query_results){
            $entry_id = $wpdb->insert_id;
            $frm_entry_meta->duplicate_entry_metas($id);
            return $entry_id;
        }else
            return false;
    }

    function update( $id, $values ){
      global $wpdb, $frmdb, $frm_entry_meta, $frm_field;
       
      $new_values = array();

      if (isset($values['item_key']))
          $new_values['item_key'] = FrmAppHelper::get_unique_key($values['item_key'], $frmdb->entries, 'item_key', $id);

      $new_values['name'] = isset($values['name'])?$values['name']:'';
      $new_values['form_id'] = isset($values['form_id'])?(int)$values['form_id']: null;
      if(isset($values['frm_user_id']) and is_numeric($values['frm_user_id']))
          $new_values['user_id'] = $values['frm_user_id'];

      $query_results = $wpdb->update( $frmdb->entries, $new_values, compact('id') );
      
      if (isset($values['item_meta']))
          $frm_entry_meta->update_entry_metas($id, $values['item_meta']);
      do_action('frm_after_update_entry', $id);
      return $query_results;
    }

    function destroy( $id ){
      global $wpdb, $frmdb;
      
      // Disconnect the child items from this parent item
      //$query_results = $wpdb->update( $frmdb->entries, array('parent_item_id' => null), array( 'parent_item_id' => $id ) );
      do_action('frm_before_destroy_entry', $id);
      
      $reset = 'DELETE FROM ' . $frmdb->entry_metas .  ' WHERE item_id=' . $id;
      $destroy = 'DELETE FROM ' . $frmdb->entries .  ' WHERE id=' . $id;
      
      $wpdb->query($reset);
      return $wpdb->query($destroy);
    }
    
    function update_form( $id, $value, $form_id ){
      global $wpdb, $frmdb;
      $form_id = isset($value) ? $form_id : NULL;
      return $wpdb->update( $frmdb->entries, array('form_id' => $form_id), array( 'id' => $id ) );
    }
    
    function getOne( $id, $meta=false){
      global $wpdb, $frmdb;
      $query = "SELECT it.*, fr.name as form_name, fr.form_key as form_key FROM $frmdb->entries it 
                LEFT OUTER JOIN $frmdb->forms fr ON it.form_id=fr.id";
      if(is_numeric($id))
        $query .= ' WHERE it.id=' . $id;
      else
        $query .= " WHERE it.item_key='" . $id ."'";
      $entry = $wpdb->get_row($query);
      
      if($meta and $entry){
            $metas = FrmEntryMeta::getAll("item_id=$entry->id");
            $entry_metas = array();
            foreach($metas as $meta_val)
                $entry_metas[$meta_val->field_id] = $entry_metas[$meta_val->field_key] = $meta_val->meta_value;

            $entry->metas = $entry_metas;
      }
      return $entry;
    }
    
    function exists( $id ){
        global $wpdb, $frmdb;
        $query = "SELECT id FROM $frmdb->entries";
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

    function getAll($where = '', $order_by = '', $limit = '', $meta=false){
      global $wpdb, $frmdb, $frm_app_helper;
      $query = "SELECT it.*, fr.name as form_name,fr.form_key as form_key
                FROM $frmdb->entries it LEFT OUTER JOIN $frmdb->forms fr ON it.form_id=fr.id" . 
                $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
      $entries = $wpdb->get_results($query);
      if($meta){
          foreach($entries as $key => $entry){
              $metas = FrmEntryMeta::getAll("item_id=$entry->id");
              $entry_metas = array();
              foreach($metas as $meta_val)
                  $entry_metas[$meta_val->field_id] = $entry_metas[$meta_val->field_key] = $meta_val->meta_value;

              $entries[$key]->metas = $entry_metas;
          }
      }
      return $entries;
    }

    // Pagination Methods
    function getRecordCount($where=""){
      global $wpdb, $frmdb, $frm_app_helper;
      $query = "SELECT COUNT(*) FROM $frmdb->entries it LEFT OUTER JOIN $frmdb->forms fr ON it.form_id=fr.id" .
          $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
      return $wpdb->get_var($query);
    }

    function getPageCount($p_size, $where=""){
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
    }

    function getPage($current_p,$p_size, $where = '', $order_by = ''){
      global $wpdb, $frmdb, $frm_app_helper;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $results = $this->getAll($where, $order_by, " LIMIT $start_index,$p_size;", true);
      return $results;
    }

    function validate( $values ){
        global $wpdb, $frmdb, $frm_field, $frm_entry_meta;

        $errors = array();

        if( !isset($values['item_key']) or $values['item_key'] == '' )
            $_POST['item_key'] = $values['item_key'] = FrmAppHelper::get_unique_key('', $frmdb->entries, 'item_key');
        
        $where = apply_filters('frm_posted_field_ids', 'fi.form_id='.$values['form_id']);
        $posted_fields = $frm_field->getAll($where, ' ORDER BY fi.field_order');

        foreach($posted_fields as $posted_field){ 
            $value = '';
            $field_options = unserialize($posted_field->field_options);
            if (isset($values['item_meta'][$posted_field->id]))
                $value = $values['item_meta'][$posted_field->id];
                
            if (isset($field_options['default_blank']) and $field_options['default_blank'] and $value == $posted_field->default_value)
                $_POST['item_meta'][$posted_field->id] = $value = '';            
                  
            if ($posted_field->required == '1' and $value == ''){
                $errors['field'.$posted_field->id] = (!isset($field_options['blank']) or $field_options['blank'] == __('Untitled cannot be blank', 'formidable') or $field_options['blank'] == '') ? ($posted_field->name . ' '. __('cannot be blank', 'formidable')) : $field_options['blank'];  
            }else if ($posted_field->type == 'text' and !isset($_POST['name']))
                $_POST['name'] = $value;
                
            $errors = apply_filters('frm_validate_field_entry', $errors, $posted_field, $value);
        }

        if (isset($_POST['recaptcha_challenge_field'])){
            global $frm_settings;

            if(!function_exists('recaptcha_check_answer'))
                require_once(FRM_PATH.'/classes/recaptchalib.php');
                
            $response = recaptcha_check_answer($frm_settings->privkey,
                                            $_SERVER["REMOTE_ADDR"],
                                            $_POST["recaptcha_challenge_field"],
                                            $_POST["recaptcha_response_field"]);

            if (!$response->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                $errors['captcha-'.$response->error] = $frm_settings->re_msg;
            }
            
        }
        
        global $wpcom_api_key;
        if (isset($values['item_meta']) and !empty($values['item_meta']) and empty($errors) and function_exists( 'akismet_http_post' ) and ((get_option('wordpress_api_key') or $wpcom_api_key)) and $this->akismet($values)){
            global $frm_form;
            $form = $frm_form->getOne($values['form_id']);
            $form_options = stripslashes_deep(unserialize($form->options));

            if (isset($form_options['akismet']) && $form_options['akismet'])
    	        $errors['spam'] = __('Your entry appears to be spam!', 'formidable');
    	}
        
        return $errors;
    }
    
    //Check entries for spam -- returns true if is spam
    function akismet($values) {
	    global $akismet_api_host, $akismet_api_port, $frm_blogurl;

		$content = '';
		foreach ( $values['item_meta'] as $val ) {
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