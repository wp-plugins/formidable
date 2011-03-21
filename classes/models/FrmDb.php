<?php
class FrmDb{
    var $fields;
    var $forms;
    var $entries;
    var $entry_metas;
    
    function FrmDb(){
        global $wpdb;
        $this->fields         = $wpdb->prefix . "frm_fields";
        $this->forms          = $wpdb->prefix . "frm_forms";
        $this->entries        = $wpdb->prefix . "frm_items";
        $this->entry_metas    = $wpdb->prefix . "frm_item_metas";
    }
    
    function upgrade($old_db_version=false){
      global $wpdb, $frm_form, $frm_field, $frm_db_version;
      //$frm_db_version is the version of the database we're moving to
      if(!$old_db_version)
          $old_db_version = get_option('frm_db_version');

      if ($frm_db_version != $old_db_version){
          require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      
      $charset_collate = '';
      if( $wpdb->has_cap( 'collation' ) ){
          if( !empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
          if( !empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";
      }

      /* Create/Upgrade Fields Table */
      $sql = "CREATE TABLE `{$this->fields}` (
                `id` int(11) NOT NULL auto_increment,
                `field_key` varchar(255) default NULL,
                `name` varchar(255) default NULL,
                `description` text default NULL,
                `type` text default NULL,
                `default_value` longtext default NULL,
                `options` longtext default NULL,
                `field_order` int(11) default 0,
                `required` int(1) default NULL,
                `field_options` longtext default NULL,
                `form_id` int(11) default NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY `id` (`id`),
                KEY `form_id` (`form_id`),
                UNIQUE KEY `field_key` (`field_key`)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Forms Table */
      $sql = "CREATE TABLE {$this->forms} (
                `id` int(11) NOT NULL auto_increment,
                `form_key` varchar(255) default NULL,
                `name` varchar(255) default NULL,
                `description` text default NULL,
                `logged_in` boolean default NULL,
                `editable` boolean default NULL,
                `is_template` boolean default 0,
                `default_template` boolean default 0,
                `status` varchar(255) default NULL,
                `prli_link_id` int(11) default NULL,
                `options` longtext default NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY `id` (`id`),
                UNIQUE KEY `form_key` (`form_key`)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Items Table */
      $sql = "CREATE TABLE {$this->entries} (
                `id` int(11) NOT NULL auto_increment,
                `item_key` varchar(255) default NULL,
                `name` varchar(255) default NULL,
                `description` text default NULL,
                `ip` text default NULL,
                `form_id` int(11) default NULL,
                `post_id` int(11) default NULL,
                `user_id` int(11) default NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY `id` (`id`),
                KEY `form_id` (`form_id`),
                KEY `post_id` (`post_id`),
                KEY `user_id` (`user_id`),
                UNIQUE KEY `item_key` (`item_key`)
              ) {$charset_collate};";

      dbDelta($sql);

      /* Create/Upgrade Meta Table */
      $sql = "CREATE TABLE {$this->entry_metas} (
                `id` int(11) NOT NULL auto_increment,
                `meta_value` longtext default NULL,
                `field_id` int(11) NOT NULL,
                `item_id` int(11) NOT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY `id` (`id`),
                KEY `field_id` (`field_id`),
                KEY `item_id` (`item_id`)
              ) {$charset_collate};";

      dbDelta($sql);
      
      /**** MIGRATE DATA ****/
      if ($frm_db_version == 1.03){
          global $frm_entry;
          $all_entries = $frm_entry->getAll();
          foreach($all_entries as $ent){
              $opts = maybe_unserialize($ent->description);
              if(is_array($opts))
                $wpdb->update( $this->entries, array('ip' => $opts['ip']), array( 'id' => $ent->id ) );
          }
      }else if($frm_db_version >= 4 and $old_db_version < 4){
          $user_ids = FrmEntryMeta::getAll("fi.type='user_id'");
          foreach($user_ids as $user_id)
              $wpdb->update( $this->entries, array('user_id' => $user_id->meta_value), array('id' => $user_id->item_id) );
      }
      
      /**** ADD DEFAULT TEMPLATES ****/
      FrmFormsController::add_default_templates(FRM_TEMPLATES_PATH);

      
      /***** SAVE DB VERSION *****/
      update_option('frm_db_version',$frm_db_version);
      }
      
      do_action('frm_after_install');
    }
    
    function get_count($table, $args=array()){
      global $wpdb;
      extract(FrmDb::get_where_clause_and_values( $args ));

      $query = "SELECT COUNT(*) FROM {$table}{$where}";
      $query = $wpdb->prepare($query, $values);
      return $wpdb->get_var($query);
    }

    function get_where_clause_and_values( $args ){
      $where = '';
      $values = array();
      if(is_array($args)){
          foreach($args as $key => $value){
            if(!empty($where))
              $where .= ' AND';
            else
              $where .= ' WHERE';

            $where .= " {$key}=";

            if(is_numeric($value))
              $where .= "%d";
            else
              $where .= "%s";

            $values[] = $value;
          }
      }

      return compact('where','values');
    }
    
    function get_var($table, $args=array(), $field='id', $order_by=''){
      global $wpdb;

      extract(FrmDb::get_where_clause_and_values( $args ));
      if(!empty($order_by))
          $order_by = " ORDER BY {$order_by}";

      $query = $wpdb->prepare("SELECT {$field} FROM {$table}{$where}{$order_by} LIMIT 1", $values);
      return $wpdb->get_var($query);
    }
    
    function get_col($table, $args=array(), $field='id', $order_by=''){
      global $wpdb;

      extract(FrmDb::get_where_clause_and_values( $args ));
      if(!empty($order_by))
          $order_by = " ORDER BY {$order_by}";

      $query = $wpdb->prepare("SELECT {$field} FROM {$table}{$where}{$order_by}", $values);
      return $wpdb->get_col($query);
    }

    function get_one_record($table, $args=array(), $fields='*'){
      global $wpdb;

      extract(FrmDb::get_where_clause_and_values( $args ));

      $query = "SELECT {$fields} FROM {$table}{$where} LIMIT 1";
      $query = $wpdb->prepare($query, $values);
      return $wpdb->get_row($query);
    }

    function get_records($table, $args=array(), $order_by='', $limit='', $fields='*'){
      global $wpdb;

      extract(FrmDb::get_where_clause_and_values( $args ));

      if(!empty($order_by))
        $order_by = " ORDER BY {$order_by}";

      if(!empty($limit))
        $limit = " LIMIT {$limit}";

      $query = "SELECT {$fields} FROM {$table}{$where}{$order_by}{$limit}";
      $query = $wpdb->prepare($query, $values);
      return $wpdb->get_results($query);
    }
    
    function uninstall(){
        if(!current_user_can('administrator')){
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        
        global $frm_update, $wpdb;
        $wpdb->query('DROP TABLE IF EXISTS '. $this->fields);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->forms);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->entries);
        $wpdb->query('DROP TABLE IF EXISTS '. $this->entry_metas);
        
        delete_option('frm_options');
        delete_option('frm_db_version');
        delete_option($frm_update->pro_last_checked_store);
        delete_option($frm_update->pro_auth_store);
        delete_option($frm_update->pro_cred_store);
        
        do_action('frm_after_uninstall');
    }
}
?>