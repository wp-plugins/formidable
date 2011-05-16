<?php
class FrmField{

    function create( $values, $return=true ){
        global $wpdb, $frmdb;

        $new_values = array();
        $key = isset($values['field_key']) ? $values['field_key'] : $values['name'];
        $new_values['field_key'] = FrmAppHelper::get_unique_key($key, $frmdb->fields, 'field_key');

        foreach (array('name', 'description', 'type', 'default_value') as $col)
            $new_values[$col] = stripslashes($values[$col]);
        
        $new_values['options'] = $values['options'];

        $new_values['field_order'] = isset($values['field_order'])?(int)$values['field_order']:NULL;
        $new_values['required'] = isset($values['required'])?(int)$values['required']:NULL;
        $new_values['form_id'] = isset($values['form_id'])?(int)$values['form_id']:NULL;
        $new_values['field_options'] = is_array($values['field_options']) ? serialize($values['field_options']) : $values['field_options'];
        $new_values['created_at'] = current_time('mysql', 1);
        
        //if(isset($values['id']) and is_numeric($values['id']))
        //    $new_values['id'] = $values['id'];

        $query_results = $wpdb->insert( $frmdb->fields, $new_values );
        if($return){
            if($query_results)
                return $wpdb->insert_id;
            else
                return false;
        }
    }

    function duplicate($old_form_id, $form_id, $copy_keys=false, $blog_id=false){
        global $frmdb;
        foreach ($this->getAll("fi.form_id = $old_form_id", '', '', $blog_id) as $field){
            $values = array();
            $new_key = ($copy_keys) ? $field->field_key : '';
            $values['field_key'] = FrmAppHelper::get_unique_key($new_key, $frmdb->fields, 'field_key');
            $values['field_options'] = maybe_unserialize($field->field_options);
            $values['form_id'] = $form_id;
            foreach (array('name', 'description', 'type', 'default_value', 'options', 'field_order', 'required') as $col)
                $values[$col] = $field->{$col};
            $this->create($values, false);
            unset($field);
        }
    }

    function update( $id, $values ){
        global $wpdb, $frmdb;

        if (isset($values['field_key']))
            $values['field_key'] = FrmAppHelper::get_unique_key($values['field_key'], $frmdb->fields, 'field_key', $id);

        if (isset($values['field_options']) and is_array($values['field_options']))
            $values['field_options'] = serialize($values['field_options']);

        $query_results = $wpdb->update( $frmdb->fields, $values, array( 'id' => $id ) );
        unset($values);
        
        if($query_results){
            global $frm_loaded_fields;
            unset($frm_loaded_fields[$id]);
        }
        return $query_results;
    }

    function destroy( $id ){
      global $wpdb, $frmdb;

      $wpdb->query("DELETE FROM $frmdb->entry_metas WHERE field_id='$id'");
      return $wpdb->query("DELETE FROM $frmdb->fields WHERE id='$id'");
    }

    function getOne( $id ){
        global $wpdb, $frmdb, $frm_loaded_fields;
        if(isset($frm_loaded_fields[$id])) 
            return $frm_loaded_fields[$id];
            
        if (is_numeric($id))
            $query = "SELECT * FROM $frmdb->fields WHERE id=$id";
        else
            $query = "SELECT * FROM $frmdb->fields WHERE field_key='$id'";
        $results = $wpdb->get_row($query);
        
        if($results)
            $results->field_options = maybe_unserialize($results->field_options);
        return $results;
    }

    function getAll($where = '', $order_by = '', $limit = '', $blog_id=false){
        global $wpdb, $frmdb, $frm_app_helper, $frm_loaded_fields;
        
        if ($blog_id and IS_WPMU){
            global $wpmuBaseTablePrefix;
            if($wpmuBaseTablePrefix)
                $prefix = "{$wpmuBaseTablePrefix}{$blog_id}_";
            else
                $prefix = $wpdb->get_blog_prefix( $blog_id );
            
            $table_name = "{$prefix}frm_fields"; 
            $form_table_name = "{$prefix}frm_forms";
        }else{
            $table_name = $frmdb->fields;
            $form_table_name = $frmdb->forms;
        }
        
        $query = 'SELECT fi.*, ' .
                 'fr.name as form_name ' . 
                 'FROM '. $table_name . ' fi ' .
                 'LEFT OUTER JOIN ' . $form_table_name . ' fr ON fi.form_id=fr.id' . 
                 $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
        
        if ($limit == ' LIMIT 1')
            $results = $wpdb->get_row($query);
        else
            $results = $wpdb->get_results($query);
        
        if($results){
            if(is_array($results)){
                foreach($results as $r_key => $result){
                    $results[$r_key]->field_options = maybe_unserialize($result->field_options);
                    $frm_loaded_fields[$result->id] = $frm_loaded_fields[$result->field_key] = $result;
                }
            }else{
                $results->field_options = maybe_unserialize($results->field_options);
                $frm_loaded_fields[$results->id] = $frm_loaded_fields[$results->field_key] = $results;
            }
        }
        return $results;
    }

    function getIds($where = '', $order_by = '', $limit = ''){
        global $wpdb, $frmdb, $frm_app_helper;
        $query = "SELECT fi.id  FROM $frmdb->fields fi " .
                 "LEFT OUTER JOIN $frmdb->forms fr ON fi.form_id=fr.id" . 
                 $frm_app_helper->prepend_and_or_where(' WHERE ', $where) . $order_by . $limit;
        if ($limit == ' LIMIT 1')
            $results = $wpdb->get_row($query);
        else
            $results = $wpdb->get_results($query);
        return $results;
    }
}
?>