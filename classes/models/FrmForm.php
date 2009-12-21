<?php
class FrmForm{
  var $table_name;

  function FrmForm(){
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}frm_forms";
  }

  function create( $values ){
    global $wpdb;
    
    $new_values = array();
    $new_values['form_key'] = FrmAppHelper::get_unique_key($values['form_key'], $this->table_name, 'form_key');
    $new_values['name'] = $values['name'];
    $new_values['description'] = $values['description'];
    $new_values['status'] = isset($values['status'])?$values['status']:'draft';
    $new_values['is_template'] = isset($values['is_template'])?(int)$values['is_template']:0;
    $new_values['default_template'] = isset($values['default_template'])?(int)$values['default_template']:0;
    $new_values['prli_link_id'] = isset($link_id)?(int)$link_id:0;
    $new_values['created_at'] = current_time('mysql', 1);

    $query_results = $wpdb->insert( $this->table_name, $new_values );
    
    return $wpdb->insert_id;
  }
  
  function duplicate( $id, $template=false ){
    global $wpdb, $frm_form, $frm_field;
    
    $values = $frm_form->getOne( $id );
    
    $new_values = array();
    $new_values['form_key'] = FrmAppHelper::get_unique_key('', $this->table_name, 'form_key');
    $new_values['name'] = $values->name;
    $new_values['description'] = $values->description;
    $new_values['status'] = (!$template)?'draft':'';
    $new_values['options'] = $values->options;
    $new_values['logged_in'] = $values->logged_in ? $values->logged_in : 0;
    $new_values['editable'] = $values->editable ? $values->editable : 0;
    $new_values['created_at'] = current_time('mysql', 1);
    $new_values['is_template'] = ($template) ? 1 : 0;

    $query_results = $wpdb->insert( $this->table_name, $new_values );
    
   if($query_results){
       $form_id = $wpdb->insert_id;
       $frm_field->duplicate($id, $form_id);
           
      return $form_id;
   }else
      return false;
  }

  function update( $id, $values, $create_link = false ){
    global $wpdb, $frm_field;

    if ($create_link)
        $values['status'] = 'published';
        
    if (isset($values['form_key']))
        $values['form_key'] = FrmAppHelper::get_unique_key($values['form_key'], $this->table_name, 'form_key', $id);
    
    $form_fields = array('form_key','name','description','status','prli_link_id');
    
    $options = array();
    $options['email_to'] = isset($values['options']['email_to']) ? $values['options']['email_to'] : ''; 
    $options = apply_filters('frm_form_options_before_update', $options, $values);
    
    $new_values = array();
    foreach ($values as $value_key => $value){
        if (in_array($value_key, $form_fields))
            $new_values[$value_key] = $value;
    }
    $new_values['options'] = serialize($options);
    
    $query_results = $wpdb->update( $this->table_name, $new_values, array( 'id' => $id ) );

    $all_fields = $frm_field->getAll("fi.form_id=$id");
    if ($all_fields && isset($values['item_meta'])){
        $existing_keys = array_keys($values['item_meta']);
        foreach ($all_fields as $fid){
            if (!in_array($fid->id, $existing_keys))
                $values['item_meta'][$fid->id] = '';
        }
        foreach ($values['item_meta'] as $field_id => $default_value){
            $field_options = array();
            foreach (array('size','max','label','invalid','required_indicator','blank') as $opt)
                $field_options[$opt] = isset($values['field_options'][$opt.'_'.$field_id]) ? $values['field_options'][$opt.'_'.$field_id] : '';
            $field_values = apply_filters('frm_update_field_options', array('default_value' => '', 'field_options' => $field_options), $field_id, $values);
            $frm_field->update($field_id, $field_values);
        }
    }    
    
    if (isset($values['form_key']) && class_exists('PrliLink')){
        $form = $this->getOne($id);
        global $prli_link;
        $prlink = $prli_link->getOne($form->prli_link_id);
        if ($prlink){
            $prli = array();
            $prli['url'] = FrmFormsHelper::get_direct_link($values['form_key']);
            $prli['slug'] = $prlink->slug;
            $prli['name'] = $prlink->name;
            $prli['param_forwarding'] = $prlink->param_forwarding;
            $prli['param_struct'] = $prlink->param_struct;
            $prli['redirect_type'] = $prlink->redirect_type;
            $prli['description'] = $prlink->description;
            $prli['track_me'] = $prlink->track_me;
            $prli['nofollow'] = $prlink->nofollow;
            $prli['group_id'] = $prlink->group_id;
            $prli_link->update($form->prli_link_id, $prli); //update target url
        }else if($create_link && $form->is_template != 1){
            $link_id = prli_create_pretty_link(FrmFormsHelper::get_direct_link($values['form_key']), $values['form_key'], $form->name, $form->description, $group_id = '' );
            $wpdb->update( $this->table_name, array('prli_link_id' => $link_id), array( 'id' => $id ) );
        }
        do_action('frm_update_form', $id, $values);
    }    
     
    return $query_results;
  }

  function destroy( $id ){
    global $wpdb, $frm_entry, $frm_field, $wp_rewrite;

    $form = $this->getOne($id);
    if (!$form or $form->default_template)
        return false;
        
    // Disconnect the items from this form
    foreach ($frm_entry->getAll('it.form_id='.$id) as $item)
        $frm_entry->destroy($item->id);

    // Disconnect the fields from this form
    $query = 'DELETE FROM ' . $frm_field->table_name . ' WHERE form_id='.$id;
    $query_results = $wpdb->query($query);

    $destroy = 'DELETE FROM ' . $this->table_name .  ' WHERE id=' . $id;
    return $wpdb->query($destroy);
  }
  
  function getName( $id ){
      global $wpdb;
      $query = 'SELECT name FROM ' . $this->table_name . ' WHERE id=' . $id . ';';
      return $wpdb->get_var($query);
  }
  
  function getOneByKey( $key ){
      global $wpdb;
      $query = 'SELECT * FROM ' . $this->table_name . ' WHERE form_key="' . $key . '";';
      return $wpdb->get_row($query);
  }
  
  function getIdByName( $name ){
      global $wpdb;
      $query = 'SELECT id FROM ' . $this->table_name . ' WHERE name="' . $name . '";';
      return $wpdb->get_var($query);
  }

  function getOne( $id ){
      global $wpdb;
      if (is_numeric($id))
          $query = 'SELECT * FROM ' . $this->table_name . ' WHERE id=' . $id . ';';
      else
          $query = 'SELECT * FROM ' . $this->table_name . ' WHERE form_key="' . $id . '";';
      return $wpdb->get_row($query);
  }

  function getAll( $where = '', $order_by = '', $limit = '' ){
      global $wpdb, $frm_app_helper;
      $query = 'SELECT * FROM ' . $this->table_name . $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
      if ($limit == ' LIMIT 1')
        $results = $wpdb->get_row($query);
      else
        $results = $wpdb->get_results($query);
      return $results;
  }

  // Pagination Methods
  function getRecordCount($where=""){
      global $wpdb, $frm_app_helper;
      $query = 'SELECT COUNT(*) FROM ' . $this->table_name . $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
      return $wpdb->get_var($query);
  }

  function getPageCount($p_size, $where=""){
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
  }

  function getPage($current_p,$p_size, $where = "", $order_by = ''){
      global $wpdb, $frm_app_helper;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $query = 'SELECT *  FROM ' . $this->table_name . $frm_app_helper->prepend_and_or_where(' WHERE', $where) . $order_by .' LIMIT ' . $start_index . ',' . $p_size;
      $results = $wpdb->get_results($query);
      return $results;
  }

  function validate( $values ){
      global $frm_field, $frm_entry_meta;
      $errors = array();

      /*if( $values['form_key'] == null or $values['form_key'] == '' ){
          if( $values['name'] == null or $values['name'] == '' )
              $errors[] = "Key can't be blank";
          else 
             $_POST['form_key'] = $values['name'];
      }

      if( $values['name'] == null or $values['name'] == '' )
        $errors[] = "Form must have a name."; 

      */

      return apply_filters('frm_validate_form', $errors, $values);
  }
}
?>