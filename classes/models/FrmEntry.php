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

        if($query_results){ //TODO: save checkbox values in serialized array
            $entry_id = $wpdb->insert_id;
            if (isset($values['item_meta']))
                $frm_entry_meta->update_entry_metas($entry_id, $values['item_meta']);
            $entry = $this->getOne($entry_id);
            do_action('frm_after_create_entry', $entry);
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
      $new_values['name'] = isset($values['name'])?$values['name']:'';
      $new_values['form_id'] = isset($values['form_id'])?(int)$values['form_id']: null;
      //$new_values['parent_item_id'] = isset($values['parent_item_id'])?(int)$values['parent_item_id']: null;

      $query_results = $wpdb->update( $this->table_name, $new_values, array( 'id' => $id ) );
      
      if (isset($values['item_meta']))
          $frm_entry_meta->update_entry_metas($id, $values['item_meta']);

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

    function getOneByKey( $key ){
      global $wpdb, $frm_entry_meta;
      $query = "SELECT it.*".//", meta.* " .
                "FROM {$this->table_name} it ".
                //"LEFT OUTER JOIN {$frm_entry_meta->table_name} meta ON meta.item_id=it.id " .
                "WHERE it.item_key='" . $key . "'";
      return $wpdb->get_row($query);
    }
    
    function getOne( $id ){
      global $wpdb, $frm_form;
      $query = 'SELECT it.*, ' .
                'gr.name as form_name, ' .
                'gr.form_key as form_key ' .
                'FROM '. $this->table_name . ' it ' .
                'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON it.form_id=gr.id' .
                ' WHERE it.id=' . $id;
      return $wpdb->get_row($query);
    }

    function getAll($where = '', $order_by = '', $limit = ''){
      global $wpdb, $frm_form, $frm_utils;
      $query = 'SELECT it.*, ' .
                'gr.name as form_name, ' .
                'gr.form_key as form_key ' .
                'FROM '. $this->table_name . ' it ' .
                'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON it.form_id=gr.id' . 
                $frm_utils->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
      return $wpdb->get_results($query);
    }

    // Pagination Methods
    function getRecordCount($where=""){
      global $wpdb, $frm_utils, $frm_form;
      $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' it ' .
          'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON it.form_id=gr.id' .
          $frm_utils->prepend_and_or_where(' WHERE ', $where);
      return $wpdb->get_var($query);
    }

    function getPageCount($p_size, $where=""){
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
    }

    function getPage($current_p,$p_size, $where = "", $order_by = ''){
      global $wpdb, $frm_utils, $frm_form;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $query = 'SELECT it.*, ' .
                'gr.name as form_name ' .
               'FROM ' . $this->table_name . ' it ' .
               'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON it.form_id=gr.id' . 
               $frm_utils->prepend_and_or_where(' WHERE', $where) . $order_by . ' ' . 
               'LIMIT ' . $start_index . ',' . $p_size . ';';
      $results = $wpdb->get_results($query);
      return $results;
    }

    function validate( $values ){
        global $wpdb, $frm_utils, $frm_field, $frm_entry_meta;

        $errors = array();   
        
        if (!isset($values['name']) and isset($values['item_meta'])){
            foreach($values['item_meta'] as $key => $value){
                $field = $frm_field->getOne($key);
                if ($field->required == '1' and $field->type == 'text' and !isset($_POST['name']))
                    $_POST['name'] = $value;
            }
        }

        if( !isset($values['item_key']) or $values['item_key'] == '' )
            $_POST['item_key'] = FrmAppHelper::get_unique_key('', $this->table_name, 'item_key');;
            
        //if( $values['name'] == null or $values['name'] == '' )
        //    $errors[] = "Name can't be blank";
        
        if (isset($values['item_meta'])){    
            foreach($values['item_meta'] as $key => $value){
                $field = $frm_field->getOne($key);
                if ($field->required == '1' and ($values['item_meta'][$key] == null or $values['item_meta'][$key] == '') and ($field->form_id == $values['form_id'])){
                    $field_options = unserialize($field->field_options);
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
        
      return $errors;
    }
    
}
?>