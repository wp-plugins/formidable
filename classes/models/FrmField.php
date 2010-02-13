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

    function duplicate($old_form_id,$form_id, $copy_keys){
        foreach ($this->getAll("fi.form_id = $old_form_id") as $field){
            $values = array();
            $new_key = ($copy_keys) ? $values->form_key : '';
            $values['field_key'] = FrmAppHelper::get_unique_key($new_key, $this->table_name, 'field_key');
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
            $values['field_options'] = serialize($values['field_options']);

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

    function getOne( $id ){
        global $wpdb;
        if (is_numeric($id))
            $query = "SELECT * FROM {$this->table_name} WHERE id=" . $id;
        else
            $query = "SELECT * FROM {$this->table_name} WHERE field_key='" . $id . "'";
        return $wpdb->get_row($query);
    }

    function getAll($where = '', $order_by = '', $limit = ''){
        global $wpdb, $frm_form, $frm_app_helper;
        $query = 'SELECT fi.*, ' .
                 'fr.name as form_name ' . 
                 'FROM '. $this->table_name . ' fi ' .
                 'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON fi.form_id=fr.id' . 
                 $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
        if ($limit == ' LIMIT 1')
            $results = $wpdb->get_row($query);
        else
            $results = $wpdb->get_results($query);
        return $results;
    }

    function getIds($where = '', $order_by = '', $limit = ''){
        global $wpdb, $frm_form, $frm_app_helper;
        $query = 'SELECT fi.id ' . 
                 'FROM '. $this->table_name . ' fi ' .
                 'LEFT OUTER JOIN ' . $frm_form->table_name . ' fr ON fi.form_id=fr.id' . 
                 $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
        if ($limit == ' LIMIT 1')
            $results = $wpdb->get_row($query);
        else
            $results = $wpdb->get_results($query);
        return $results;
    }

    function validate( $values ){
      global $wpdb, $frm_blogurl;

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