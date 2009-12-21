<?php
class FrmEntryMeta{
  var $table_name;

  function FrmEntryMeta(){
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}frm_item_metas";
  }

  function add_entry_meta($item_id, $field_id, $meta_key, $meta_value){
    global $wpdb;

    $new_values = array();
    $new_values['meta_key'] = $meta_key;
    $new_values['meta_value'] = $meta_value;
    $new_values['item_id'] = $item_id;
    $new_values['field_id'] = $field_id;
    $new_values['created_at'] = current_time('mysql', 1);
    
    return $wpdb->insert( $this->table_name, $new_values );
  }

  function update_entry_meta($item_id, $field_id, $meta_key, $meta_value){
    global $wpdb;
    //$this->delete_entry_meta($item_id, $field_id);
    if ($meta_value)
        $this->add_entry_meta($item_id, $field_id, $meta_key, $meta_value);
  }
  
  function update_entry_metas($item_id, $values){
    global $frm_field;
    $this->delete_entry_metas($item_id);
    foreach($values as $field_id => $meta_value){
        $meta_key = $frm_field->getOne( $field_id )->field_key;
        $meta_value = maybe_serialize($values[$field_id]);
        $this->update_entry_meta($item_id, $field_id, $meta_key, $meta_value);
    }
  }
  
  function duplicate_entry_metas($item_id){
      foreach ($this->get_entry_meta_info($item_id) as $meta)
          $this->update_entry_meta($item_id, $meta->field_id, $meta->meta_key, $meta->meta_value);
  }

  function delete_entry_meta($item_id, $field_id){
    global $wpdb;

    $query_str = "DELETE FROM {$this->table_name} WHERE field_id=%d AND item_id=%d";
    $query = $wpdb->prepare($query_str, $field_id, $item_id);
    return $wpdb->query($query);
  }
  
  function delete_entry_metas($item_id){
    global $wpdb;

    $query_str = "DELETE FROM {$this->table_name} WHERE item_id=%d";
    $query = $wpdb->prepare($query_str, $item_id);
    return $wpdb->query($query);
  }
  
  function get_entry_meta_by_field($item_id, $field_id, $return_var=false){
      global $wpdb;
      $query = "SELECT meta_value FROM {$this->table_name} WHERE field_id='{$field_id}' and item_id='{$item_id}'";
      if($return_var)
          return $wpdb->get_var("{$query} LIMIT 1");
      else
          return $wpdb->get_col($query, 0);
  }
  
  function get_entry_meta($item_id,$meta_key,$return_var=true){
      global $wpdb;
      $query_str = "SELECT meta_value FROM {$this->table_name} WHERE meta_key=%s and item_id=%d";
      $query = $wpdb->prepare($query_str,$meta_key,$item_id);

      if($return_var)
        return stripslashes($wpdb->get_var("{$query} LIMIT 1"));
      else
        return $wpdb->get_col($query, 0);
  }

  function get_entry_metas($item_id){
      global $wpdb;
      $query_str = "SELECT meta_value FROM {$this->table_name} WHERE item_id=%d";
      $query = $wpdb->prepare($query_str,$item_id);

      return $wpdb->get_col($query, 0);
  }
  
  function get_entry_meta_info($item_id){
      global $wpdb;
      $query_str = "SELECT * FROM {$this->table_name} WHERE item_id=%d";
      $query = $wpdb->prepare($query_str,$item_id);

      return $wpdb->get_results($query, 0);
  }
  
  function get_entry_meta_info_by_key($item_id, $meta_key){
      global $wpdb;
      $query_str = "SELECT * FROM {$this->table_name} WHERE meta_key=%s and item_id=%d";
      $query = $wpdb->prepare($query_str,$meta_key,$item_id);

      return $wpdb->get_results($query, 0);
  }
    
  function getAll($where = '', $order_by = '', $limit = ''){
    global $wpdb, $frm_field, $frm_app_helper;
    $query = 'SELECT it.*, ' .
              'fi.type as field_type, ' .
              'fi.required as required, ' .
              'fi.form_id as field_form_id, ' .
              'fi.name as field_name ' .
              'FROM '. $this->table_name . ' it ' .
              'LEFT OUTER JOIN ' . $frm_field->table_name . ' fi ON it.field_id=fi.id' . 
              $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;

    if ($limit == ' LIMIT 1')
        $results = $wpdb->get_row($query);
    else    
        $results = $wpdb->get_results($query);
    return $results;     
  }
  
  function getEntryIds($where = '', $order_by = '', $limit = ''){
    global $wpdb, $frm_field, $frm_app_helper;
    $query = "SELECT DISTINCT it.item_id FROM $this->table_name it LEFT OUTER JOIN $frm_field->table_name fi ON it.field_id=fi.id". $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
    if ($limit == ' LIMIT 1')
        $results = $wpdb->get_var($query);
    else    
        $results = $wpdb->get_col($query);
    
    return $results;     
  }
  
  function getRecordCount($where=""){
    global $wpdb, $frm_app_helper, $frm_field;
    $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' it ' .
        'LEFT OUTER JOIN ' . $frm_field->table_name . ' fi ON it.field_id=fi.id' .
        $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
    return $wpdb->get_var($query);
  }
  
  function search_entry_metas($search, $meta_key='', $operator){
      global $wpdb, $frm_app_helper;
      if (is_array($search)){
          $where = '';
            foreach ($search as $field => $value){
              if ($field == 'year' and $value > 0)
                  $where .= " meta_value {$operator} '%{$value}' and";
              if ($field == 'month' and $value > 0)
                  $where .= " meta_value {$operator} '{$value}%' and";
              if ($field == 'day' and $value > 0)
                  $where .= " meta_value {$operator} '%/{$value}/%' and";      
            }
            $where .= " meta_key='{$meta_key}'";
            $query = "SELECT DISTINCT item_id FROM {$this->table_name}". $frm_app_helper->prepend_and_or_where(' WHERE ', $where);
        }else{
            if ($operator == 'LIKE')
                $search = "%{$search}%";
            $query = $wpdb->prepare("SELECT DISTINCT item_id FROM {$this->table_name} WHERE meta_value {$operator} '{$search}' and meta_key='{$meta_key}'");
      }
      return $wpdb->get_col($query, 0);
  }
  
  function validate( $errors, $field, $key, $value, $values ){
      global $wpdb, $frm_field;
      
      $field_options = unserialize($field->field_options);

      return $errors;      
  } 
}
?>