<?php
class FrmField{
  var $table_name;

  function FrmField(){
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}frm_fields";
  }

  function create( $values, $return=true ){
    global $wpdb;

    $new_values = array();
    $key = isset($values['field_key']) ? $values['field_key'] : $values['name'];
    $new_values['field_key'] = FrmAppHelper::get_unique_key($key, $this->table_name, 'field_key');
    
    foreach (array('name','description','type','default_value','options') as $col)
        $new_values[$col] = stripslashes($values[$col]);
    
    $new_values['field_order'] = isset($values['field_order'])?(int)$values['field_order']:NULL;
    $new_values['required'] = isset($values['required'])?(int)$values['required']:NULL;
    $new_values['form_id'] = isset($values['form_id'])?(int)$values['form_id']:NULL;
    $new_values['field_options'] = serialize($values['field_options']);
    $new_values['created_at'] = current_time('mysql', 1);
                                                      
    $query_results = $wpdb->insert( $this->table_name, $new_values );
    if($return){
        if($query_results)
            return $wpdb->insert_id;
        else
            return false;
    }
  }
  
  function duplicate($old_form_id,$form_id){
      foreach ($this->getAll("fi.form_id = $old_form_id") as $field){
          $values = array();
          $values['field_key'] = $field->field_key;
          $values['field_options'] = unserialize($field->field_options);
          $values['form_id'] = $form_id;
          foreach (array('name','description','type','default_value','options','field_order','required') as $col)
              $values[$col] = $field->$col;
          $this->create($values, false);
        }
  }

  function update( $id, $values ){
      global $wpdb;
      
      if (isset($values['field_key']))
          $values['field_key'] = FrmAppHelper::get_unique_key($values['field_key'], $this->table_name, 'field_key', $id);

      if (isset($values['field_options']))
          $values['field_options'] = serialize(stripslashes_deep($values['field_options']));
      
      $query_results = $wpdb->update( $this->table_name, $values, array( 'id' => $id ) );
      
      return $query_results;
  }

  function destroy( $id ){
    global $wpdb, $frm_entry_meta;
    
    $reset = 'DELETE FROM ' . $frm_entry_meta->table_name .  ' WHERE field_id=' . $id;
    $destroy = 'DELETE FROM ' . $this->table_name .  ' WHERE id=' . $id;

    $wpdb->query($reset);
    return $wpdb->query($destroy);
  }
  
  function getOneByKey( $key ){
    global $wpdb, $frm_entry_meta;
    $query = "SELECT * FROM {$this->table_name} WHERE field_key='{$key}'";
    return $wpdb->get_row($query);
  }

  function getOne( $id ){
    global $wpdb;
    $query = "SELECT * FROM {$this->table_name} WHERE id=" . $id;
    return $wpdb->get_row($query);
  }

  function getAll($where = '', $order_by = '', $limit = ''){
      global $wpdb, $frm_form, $frm_utils;
      $query = 'SELECT fi.*, ' .
               'gr.name as form_name ' . 
               'FROM '. $this->table_name . ' fi ' .
               'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON fi.form_id=gr.id' . 
               $frm_utils->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
      if ($limit == ' LIMIT 1')
          $results = $wpdb->get_row($query);
      else
          $results = $wpdb->get_results($query);
      return $results;
  }

  // Pagination Methods
  function getRecordCount($where=""){
    global $wpdb, $frm_utils;
    $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' fi' . $frm_utils->prepend_and_or_where(' WHERE ', $where);
    return $wpdb->get_var($query);
  }

  function getPageCount($p_size, $where=""){
    return ceil((int)$this->getRecordCount($where) / (int)$p_size);
  }

  function getPage($current_p,$p_size, $where = "", $order_by = ''){
    global $wpdb, $frm_utils, $frm_form;
    $end_index = $current_p * $p_size;
    $start_index = $end_index - $p_size;
    $query = 'SELECT fi.*, ' .
             'gr.name as form_name ' .
             'FROM ' . $this->table_name . ' fi ' .
             'LEFT OUTER JOIN ' . $frm_form->table_name . ' gr ON fi.form_id=gr.id' . 
             $frm_utils->prepend_and_or_where(' WHERE', $where) . $order_by . ' ' . 
             'LIMIT ' . $start_index . ',' . $p_size . ';';
    $results = $wpdb->get_results($query);
    return $results;
  }

  function validate( $values ){
    global $wpdb, $frm_utils, $frm_blogurl;

    $errors = array();

    if( $values['field_key'] == null or $values['field_key'] == '' ){
        if( $values['name'] == null or $values['name'] == '' )
            $errors[] = "Key can't be blank";
        else 
           $_POST['field_key'] = $values['name'];
    }
        
    if( $values['name'] == null or $values['name'] == '' )
      $errors[] = "Label can't be blank";
    
    if( $values['type'] == null or $values['type'] == '' ){
      $errors[] = "Type can't be blank";
    }else{
      if(($values['type'] == 'select' or $values['type'] == 'radio') and ($values['options'] == null or $values['options'] == ''))
        $errors[] = "Options cannot be blank for that field type";
    }

    return $errors;
  }
}
?>