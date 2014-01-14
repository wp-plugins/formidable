<?php

class FrmXMLHelper{
    
    public static function get_xml_values($opt, $padding){
        if(is_array($opt)){
            foreach($opt as $ok => $ov){
                echo "\n". $padding;
                echo '<'. (is_numeric($ok) ? 'key:' : '') . $ok .'>';
                self::get_xml_values($ov, $padding .'    ');
                if(is_array($ov))
                    echo "\n". $padding;
                echo '</'. (is_numeric($ok) ? 'key:' : '') . $ok .'>';
            }
        }else{
            echo self::cdata($opt);
        }    
    }
    
    public static function import_xml($file){
        $terms = $forms = array();
        $imported = $updated = array(
            'forms' => 0, 'fields' => 0, 'items' => 0,
            'views' => 0, 'posts' => 0, 'terms' => 0,
        );
        
        $dom = new DOMDocument;
		$success = $dom->loadXML( file_get_contents( $file ) );
		if ( !$success )
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this XML file', 'formidable' ), libxml_get_errors() );
		
		$xml = simplexml_import_dom( $dom );
		unset( $dom );

		// halt if loading produces an error
		if ( !$xml )
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this XML file', 'formidable' ), libxml_get_errors() );
        
        // add terms, forms (form and field ids), posts (post ids), and entries to db, in that order
        
        // grab cats, tags and terms
        if(isset($xml->term)){
		foreach ( $xml->term as $t ) {
			$term_id = term_exists((string) $t->term_slug, (string) $t->term_taxonomy);
			if($term_id){
			    $terms[(int) $t->term_id] = $term_id;
			    continue;
			}
            $term_id = wp_insert_term( (string) $t->term_name, (string) $t->term_taxonomy, array(
                'slug'          => (string) $t->term_slug,
                'description'   => (string) $t->term_description,
                'term_parent'   => (string) $t->term_parent,
                'slug'          => (string) $t->term_slug,
            ));
            
            $terms[(int) $t->term_id] = $term_id;
            
            unset($t);
		}
		unset($xml->term);
		$imported['terms'] = count($terms);
	    }
		
		if(isset($xml->form)){
		$frm_form = new FrmForm();
		$frm_field = new FrmField();
		foreach ( $xml->form as $item ) {
		    $form = array(
		        'id'            => (int) $item->id,
		        'form_key'      => (string) $item->form_key,
		        'name'          => (string) $item->name,
		        'description'   => (string) $item->description,
		        'options'       => (string) $item->options,
		        'logged_in'     => (int) $item->logged_in,
		        'is_template'   => (int) $item->is_template,
		        'default_template' => (int) $item->default_template,
		        'editable'      => (int) $item->editable,
		        'status'        => (string) $item->status
		    );
		    
		    $form['options'] = FrmAppHelper::maybe_json_decode($form['options']);
		    
		    //if template, allow to edit if form keys match, otherwise, form ids must also match
		    $template_query = array('form_key' => $form['form_key'], 'is_template' => $form['is_template']);
		    if ( !$form['is_template'] ) {
		        $template_query['id'] = $form['id'];
		    }
		    
            $this_form = $frm_form->getAll($template_query, '', 1);
            
            if (!empty($this_form)){
                $form_id = $this_form->id;
                $u = $frm_form->update($form_id, $form );
                if ( $u ) {
                    $updated['forms']++;
                }
                $form_fields = $frm_field->getAll(array('fi.form_id' => $form_id), 'field_order');
                $old_fields = array();
                foreach($form_fields as $f){
                    $old_fields[$f->id] = $f;
                    if($form['is_template']){
                        $old_fields[$f->field_key] = $f->id;
                    }
                    unset($f);
                }
                $form_fields = $old_fields;
                unset($old_fields);
            }else{
                //form does not exist, so create it
                if ( $form_id = $frm_form->create( $form ) ) {
                    $imported['forms']++;
                }
            }
    		
    		foreach($item->field as $field){
    		    $f = array(
    		        'id'            => (int) $field->id,
    		        'field_key'     => (string) $field->field_key,
    		        'name'          => (string) $field->name,
    		        'description'   => (string) $field->description,
    		        'type'          => (string) $field->type,
    		        'default_value' => FrmAppHelper::maybe_json_decode( (string) $field->default_value),
    		        'field_order'   => (int) $field->field_order,
    		        'form_id'       => (int) $form_id,
    		        'required'      => (int) $field->required,
    		        'options'       => FrmAppHelper::maybe_json_decode( (string) $field->options),
    		        'field_options' => FrmAppHelper::maybe_json_decode( (string) $field->field_options)
    		    );
    		    
    		    if ($this_form){
    		        // check for field to edit
    		        if(isset($form_fields[$f['id']])){
    		            $u = $frm_field->update( $f['id'], $f );
    		            if ( $u ) {
    		                $updated['fields']++;
    		            }
    		            unset($form_fields[$f['id']]);
    		            
    		            //unset old field key
    		            if(isset($form_fields[$f['field_key']]))
    		                unset($form_fields[$f['field_key']]);
    		        }else if(isset($form_fields[$f['field_key']])){
    		            unset($f['id']);
    		            $u = $frm_field->update( $form_fields[$f['field_key']], $f );
    		            if ( $u ) {
    		                $updated['fields']++;
    		            }
    		            unset($form_fields[$form_fields[$f['field_key']]]); //unset old field id
    		            unset($form_fields[$f['field_key']]); //unset old field key
    		        }else{
    		            if ( $frm_field->create( $f ) ) {
    		                $imported['fields']++;
    		            }
    		        }
    		    }else{
    		        if ( $frm_field->create( $f ) ) {
		                $imported['fields']++;
		            }
    		    }
    		    
    		    unset($field);
    		}
    		
    		
    		// Delete any fields attached to this form that were not included in the template
    		if (isset($form_fields) and !empty($form_fields)){
                foreach ($form_fields as $field){
                    if(is_object($field)){
                        $frm_field->destroy($field->id);
                    }
                    unset($field);
                }
                unset($form_fields);
            }
		    
		    
		    // Update field ids/keys to new ones
		    if($this_form)
                do_action('frm_after_duplicate_form', $form_id, $form);
                
            $forms[(int) $item->id] = $form_id;
		    
		    unset($form);
		    unset($item);
		}
		unset($frm_form);
		unset($frm_field);
		}
        
        global $frm_duplicate_ids, $wpdb;
        
		// grab posts
		if(isset($xml->view)){
		foreach ( $xml->view as $item ) {
			$post = array(
				'post_title' => (string) $item->title,
				'guid' => (string) $item->guid,
			);
            
			$post['post_content'] = FrmFieldsHelper::switch_field_ids((string) $item->content);
			$post['post_excerpt'] = FrmFieldsHelper::switch_field_ids((string) $item->excerpt);

			foreach(array('post_author', 'post_id', 'post_date', 'post_date_gmt', 'comment_status', 'ping_status', 'post_name', 'status', 'post_parent', 'menu_order', 'post_type', 'post_password', 'is_sticky') as $f){
			    $post[$f] = (string)$item->{$f};
			    unset($f);
			}

			if ( isset($item->attachment_url) )
				$post['attachment_url'] = (string) $item->attachment_url;

			foreach ( $item->postmeta as $meta ) {
			    $m = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value
				);
				
				//switch old form and field ids to new ones
				if($m['key'] == 'frm_form_id' and isset($forms[(int) $meta->meta_value])){
				    $m['value'] = $forms[(int) $meta->meta_value];
				}else{
				    $m['value'] = FrmAppHelper::maybe_json_decode($m['value'], true);
        		    
        		    if(!empty($frm_duplicate_ids)){
        		        if($m['key'] == 'frm_dyncontent'){
        		            $m['value'] = FrmFieldsHelper::switch_field_ids($m['value']);
            		    }else if($m['key'] == 'frm_options'){
            		        if(isset($m['value']['date_field_id']) and is_numeric($m['value']['date_field_id']) and isset($frm_duplicate_ids[$m['value']['date_field_id']])){
            		            $m['value']['date_field_id'] = $frm_duplicate_ids[$m['value']['date_field_id']];
            		        }else if(isset($m['value']['edate_field_id']) and is_numeric($m['value']['edate_field_id']) and isset($frm_duplicate_ids[$m['value']['edate_field_id']])){
            		            $m['value']['edate_field_id'] = $frm_duplicate_ids[$m['value']['edate_field_id']];
            		        }else if(isset($m['value']['order_by']) and !empty($m['value']['order_by'])){
            		            if(is_numeric($m['value']['order_by']) and isset($frm_duplicate_ids[$m['value']['order_by']])){
            		                $m['value']['order_by'] = $frm_duplicate_ids[$m['value']['order_by']];
            		            }else if(is_array($m['value']['order_by'])){
            		                foreach($m['value']['order_by'] as $mk => $mv){
            		                    if(isset($frm_duplicate_ids[$mv]))
            		                        $m['value']['order_by'][$mk] = $mv;
            		                    unset($mk);
            		                    unset($mv);
            		                }
            		            }
            		        }
            		    }
        		    }
				}
				
				$m['value'] = FrmAppHelper::maybe_json_decode($m['value']);
				
				$post['postmeta'][(string) $meta->meta_key] = $m;
				unset($m);
				unset($meta);
			}
			
			//Add terms
			$post['tax_input'] = array();
			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ){
				    $taxonomy = (string) $att['domain'];
				    $name = ( is_taxonomy_hierarchical($taxonomy) ) ? (string) $att['nicename'] : (string) $c;
				    
				    if(!isset($post['tax_input'][$taxonomy]))   
				        $post['tax_input'][$taxonomy] = array();
				    
				    $post['tax_input'][$taxonomy][] = $name;
				    unset($name);
				}
			}
			
			unset($item);
			
			$old_id = $post['post_id'];
			$editing = get_post( $post['post_id'] );
            
            if( $editing && $editing->post_type == $post['post_type'] && $editing->post_name == $post['post_name'] ) {
                $post['ID'] = $editing;
            }
            
            unset($editing);
            
            //create post
            $post_id = wp_insert_post( $post );
            
            if ( is_numeric($post_id) && isset($post['ID']) ) {
                $updated[ ($post['post_type'] == 'frm_display' ? 'views' : 'posts') ]++;
            } else if ( is_numeric($post_id) ) {
                $imported[ ($post['post_type'] == 'frm_display' ? 'views' : 'posts') ]++;
            }
            
            
            unset($post);
            
			$posts[(int) $old_id] = $post_id;
		}
		unset($xml->view);
	    }
	    
	    // get entries
	    if(isset($xml->item)){
	    $frm_entry = new FrmEntry();
	    foreach($xml->item as $item){
	        $entry = array(
	            'id'            => (int) $item->id,
		        'item_key'      => (string) $item->item_key,
		        'name'          => (string) $item->name,
		        'description'   => FrmAppHelper::maybe_json_decode((string) $item->description),
		        'ip'            => (string) $item->ip,
		        'form_id'       => (isset($forms[(int) $item->form_id]) ? $posts[(int) $item->form_id] : (int) $item->form_id),
		        'post_id'       => (isset($posts[(int) $item->post_id]) ? $posts[(int) $item->post_id] : (int) $item->post_id),
		        'user_id'       => (string) $item->user_id,
		        'parent_item_id' => (int) $item->parent_item_id,
		        'is_draft'      => (int) $item->is_draft,
		        'updated_by'    => (string) $item->updated_by,
		        'created_at'    => (string) $item->created_at,
		        'updated_at'    => (string) $item->updated_at,
	        );
	        
	        $metas = array();
    		foreach($item->item_meta as $meta){
    		    $m = array(
    		        'field_id'      => (int) $meta->field_id,
    		        'meta_value'    => FrmAppHelper::maybe_json_decode((string) $meta->meta_value)
    		    );
    		    $metas[] = $m;
    		    unset($meta);
    		}
    		
    		unset($item);
    		
            $entry['item_metas'] = $metas;
            
            $editing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE id=%d AND item_key=%s", $entry['id'], $entry['item_key']));
            
            if ( $editing && $frm_entry->update($entry['id'], $entry) ) {
                $updated['items']++;
            } else if ( !$editing && $frm_entry->create($entry) ) {
                $imported['items']++;
            }
		    
		    unset($entry);
	    }
	    unset($xml->item);
	    }
	    
	    return compact('imported', 'updated');
    }
	
	public static function cdata( $str ) {
	    $str = maybe_unserialize($str);
	    if(is_array($str))
	        $str = json_encode($str);
	    else if (seems_utf8( $str ) == false )
			$str = utf8_encode( $str );
        
        if(is_numeric($str))
            return $str;
        
		// $str = ent2ncr(esc_html($str));
		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

}
