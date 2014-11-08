<?php
if ( !defined('ABSPATH') ) die('You are not allowed to call this page directly.');

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
        $defaults = array(
            'forms' => 0, 'fields' => 0, 'terms' => 0,
            'posts' => 0, 'views' => 0, 'actions' => 0,
        );

        $imported = array(
            'imported' => $defaults,
            'updated' => $defaults,
            'forms' => array(),
        );

        unset($defaults);

        if ( !defined('WP_IMPORTING') ) {
            define('WP_IMPORTING', true);
        }

        if ( !class_exists('DOMDocument') ) {
            return new WP_Error( 'SimpleXML_parse_error', __( 'Your server does not have XML enabled', 'formidable' ), libxml_get_errors() );
        }

        $dom = new DOMDocument;
		$success = $dom->loadXML( file_get_contents( $file ) );
		if ( !$success ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this XML file', 'formidable' ), libxml_get_errors() );
		}

		$xml = simplexml_import_dom( $dom );
		unset( $dom );

		// halt if loading produces an error
		if ( !$xml ) {
			return new WP_Error( 'SimpleXML_parse_error', __( 'There was an error when reading this XML file', 'formidable' ), libxml_get_errors() );
		}

        // add terms, forms (form and field ids), posts (post ids), and entries to db, in that order

        // grab cats, tags and terms
        if ( isset($xml->term) ) {
            $imported = self::import_xml_terms($xml->term, $imported);
		    unset($xml->term);
        }

		if ( isset($xml->form) ) {
            $imported = self::import_xml_forms($xml->form, $imported);
		    unset($xml->form);
		}

		// grab posts/views
		if ( isset($xml->view) ) {
		    $imported = self::import_xml_views($xml->view, $imported);
		    unset($xml->view);
	    }

	    $return = apply_filters('frm_importing_xml', $imported, $xml );

	    return $return;
    }

    public static function import_xml_terms($terms, $imported) {
        foreach ( $terms as $t ) {
			if ( term_exists((string) $t->term_slug, (string) $t->term_taxonomy) ) {
			    continue;
			}

            $term_id = wp_insert_term( (string) $t->term_name, (string) $t->term_taxonomy, array(
                'slug'          => (string) $t->term_slug,
                'description'   => (string) $t->term_description,
                'term_parent'   => (string) $t->term_parent,
                'slug'          => (string) $t->term_slug,
            ));

            if ( $term_id ) {
                $imported['imported']['terms']++;
            }

            unset($term_id);
            unset($t);
		}

		return $imported;
    }

    public static function import_xml_forms($forms, $imported) {
        $frm_form = new FrmForm();
		$frm_field = new FrmField();

		foreach ( $forms as $item ) {
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
		        'status'        => (string) $item->status,
		        'created_at'    => date('Y-m-d H:i:s', strtotime((string) $item->created_at)),
		    );

		    $form['options'] = FrmAppHelper::maybe_json_decode($form['options']);

		    // if template, allow to edit if form keys match, otherwise, creation date must also match
		    $edit_query = array('form_key' => $form['form_key'], 'is_template' => $form['is_template']);
            if ( !$form['is_template'] ) {
                $edit_query['created_at'] = $form['created_at'];
            }

		    $edit_query = apply_filters('frm_match_xml_form', $edit_query, $form);

            $this_form = $frm_form->getAll($edit_query, '', 1);
            unset($edit_query);

            if ( !empty($this_form) ) {
                $old_id = $form_id = $this_form->id;
                $frm_form->update($form_id, $form );
                $imported['updated']['forms']++;

                $form_fields = $frm_field->get_all_for_form($form_id);
                $old_fields = array();
                foreach ( $form_fields as $f ) {
                    $old_fields[$f->id] = $f;
                    $old_fields[$f->field_key] = $f->id;
                    unset($f);
                }
                $form_fields = $old_fields;
                unset($old_fields);
            } else {
                $old_id = false;
                //form does not exist, so create it
                if ( $form_id = $frm_form->create( $form ) ) {
                    $imported['imported']['forms']++;
                }
            }

    		foreach ( $item->field as $field ) {
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

    		    if ( is_array($f['default_value']) && in_array($f['type'], array(
    		        'text', 'email', 'url', 'textarea',
    		        'number','phone', 'date', 'time',
    		        'hidden', 'password', 'tag', 'image',
    		    )) ) {
    		        if ( count($f['default_value']) === 1 ) {
    		            $f['default_value'] = '['. reset($f['default_value']) .']';
    		        } else {
    		            $f['default_value'] = reset($f['default_value']);
    		        }
    		    }

    		    $f = apply_filters('frm_duplicated_field', $f);

    		    if ( $this_form ) {
    		        // check for field to edit by field id
    		        if ( isset($form_fields[$f['id']]) ) {
    		            $frm_field->update( $f['id'], $f );
    		            $imported['updated']['fields']++;

    		            unset($form_fields[$f['id']]);

    		            //unset old field key
    		            if ( isset($form_fields[$f['field_key']]) ) {
    		                unset($form_fields[$f['field_key']]);
    		            }
    		        } else if ( isset($form_fields[$f['field_key']]) ) {
    		            // check for field to edit by field key
    		            unset($f['id']);

    		            $frm_field->update( $form_fields[$f['field_key']], $f );
    		            $imported['updated']['fields']++;

    		            unset($form_fields[$form_fields[$f['field_key']]]); //unset old field id
    		            unset($form_fields[$f['field_key']]); //unset old field key
    		        } else if ( $frm_field->create( $f ) ) {
    		            // if no matching field id or key in this form, create the field
    		            $imported['imported']['fields']++;
    		        }
    		    } else if ( $frm_field->create( $f ) ) {
		            $imported['imported']['fields']++;
    		    }

    		    unset($field);
    		}


    		// Delete any fields attached to this form that were not included in the template
    		if ( isset($form_fields) && !empty($form_fields) ) {
                foreach ($form_fields as $field){
                    if ( is_object($field) ) {
                        $frm_field->destroy($field->id);
                    }
                    unset($field);
                }
                unset($form_fields);
            }


		    // Update field ids/keys to new ones
		    do_action('frm_after_duplicate_form', $form_id, $form, array('old_id' => $old_id));

            $imported['forms'][ (int) $item->id] = $form_id;

		    unset($form);
		    unset($item);
		}

		unset($frm_form);
		unset($frm_field);

		return $imported;
    }

    public static function import_xml_views($views, $imported) {
        global $frm_duplicate_ids;

        $imported['posts'] = array();
        $form_action_type = FrmFormActionsController::$action_post_type;

        foreach ( $views as $item ) {
			$post = array(
				'post_title'    => (string) $item->title,
				'post_name'     => (string) $item->post_name,
				'post_type'     => (string) $item->post_type,
				'post_password' => (string) $item->post_password,
				'guid'          => (string) $item->guid,
				'post_status'   => (string) $item->status,
				'post_author'   => FrmAppHelper::get_user_id_param( (string) $item->post_author ),
				'post_id'       => (int) $item->post_id,
				'post_parent'   => (int) $item->post_parent,
				'menu_order'    => (int) $item->menu_order,
				'post_content'  => FrmFieldsHelper::switch_field_ids((string) $item->content),
				'post_excerpt'  => FrmFieldsHelper::switch_field_ids((string) $item->excerpt),
				'is_sticky'     => (string) $item->is_sticky,
				'comment_status' => (string) $item->comment_status,
				'post_date'     => (string) $item->post_date,
				'post_date_gmt' => (string) $item->post_date_gmt,
				'ping_status'   => (string) $item->ping_status,
			);

			if ( isset($item->attachment_url) ) {
				$post['attachment_url'] = (string) $item->attachment_url;
			}

			if ( $post['post_type'] == $form_action_type ) {
			    // update to new form id
			    $post['menu_order'] = $imported['forms'][ (int) $post['menu_order'] ];
			}

            $post['postmeta'] = array();

			foreach ( $item->postmeta as $meta ) {
			    $m = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value
				);

				//switch old form and field ids to new ones
				if ( $m['key'] == 'frm_form_id' && isset($imported['forms'][ (int) $meta->meta_value]) ) {
				    $m['value'] = $imported['forms'][ (int) $meta->meta_value];
				} else {
				    $m['value'] = FrmAppHelper::maybe_json_decode($m['value']);

        		    if ( !empty($frm_duplicate_ids) ) {

        		        if ( $m['key'] == 'frm_dyncontent' ) {
        		            $m['value'] = FrmFieldsHelper::switch_field_ids($m['value']);
            		    } else if ( $m['key'] == 'frm_options' ) {

                            foreach ( array('date_field_id', 'edate_field_id') as $setting_name ) {
            		            if ( isset($m['value'][$setting_name]) && is_numeric($m['value'][$setting_name]) && isset($frm_duplicate_ids[$m['value'][$setting_name]]) ) {
            		                $m['value'][$setting_name] = $frm_duplicate_ids[$m['value'][$setting_name]];
            		            }
            		        }

                            $check_dup_array = array();
            		        if ( isset($m['value']['order_by']) && !empty($m['value']['order_by']) ) {
            		            if ( is_numeric($m['value']['order_by']) && isset($frm_duplicate_ids[$m['value']['order_by']]) ) {
            		                $m['value']['order_by'] = $frm_duplicate_ids[$m['value']['order_by']];
            		            } else if ( is_array($m['value']['order_by']) ) {
                                    $check_dup_array[] = 'order_by';
            		            }
            		        }

            		        if ( isset($m['value']['where']) && !empty($m['value']['where']) ) {
            		            $check_dup_array[] = 'where';
            		        }

                            foreach ( $check_dup_array as $check_k ) {
                                foreach ( (array) $m['value'][$check_k] as $mk => $mv ) {
        		                    if ( isset($frm_duplicate_ids[$mv]) ) {
        		                        $m['value'][$check_k][$mk] = $frm_duplicate_ids[$mv];
        		                    }
        		                    unset($mk, $mv);
        		                }
                            }
            		    }
        		    }
				}
				if ( !is_array($m['value']) ) {
				    $m['value'] = FrmAppHelper::maybe_json_decode($m['value']);
				}

				$post['postmeta'][(string) $meta->meta_key] = $m['value'];
				unset($m, $meta);
			}

			//Add terms
			$post['tax_input'] = array();
			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ){
				    $taxonomy = (string) $att['domain'];
				    if ( is_taxonomy_hierarchical($taxonomy) ) {
				        $name = (string) $att['nicename'];
				        $h_term = get_term_by('slug', $name, $taxonomy);
				        if ( $h_term ) {
				            $name = $h_term->term_id;
				        }
				        unset($h_term);
				    } else {
				        $name = (string) $c;
				    }

				    if ( !isset($post['tax_input'][$taxonomy]) ) {
				        $post['tax_input'][$taxonomy] = array();
				    }

				    $post['tax_input'][$taxonomy][] = $name;
				    unset($name);
				}
			}

			unset($item);

			// edit view if the key and created time match
			$old_id = $post['post_id'];
			$match_by =  array(
			    'post_type'     => $post['post_type'],
			    'name'          => $post['post_name'],
			    'post_status'   => $post['post_status'],
			    'posts_per_page' => 1,
			);

			if ( in_array($post['post_status'], array('trash', 'draft')) ) {
			    $match_by['include'] = $post['post_id'];
			    unset($match_by['name']);
			}

			$editing = get_posts($match_by);

            if ( !empty($editing) && current($editing)->post_date == $post['post_date'] ) {
                $post['ID'] = current($editing)->ID;
            }

            unset($editing);

            if ( $post['post_type'] == $form_action_type ) {
                $action_control = FrmFormActionsController::get_form_actions( $post['post_excerpt'] );
                $post['post_content'] = FrmAppHelper::maybe_json_decode($post['post_content']);
                $post_id = $action_control->duplicate_one( (object) $post, $post['menu_order']);
                unset($action_control);
            } else {
                //create post
                $post_id = wp_insert_post( $post );
            }

            if ( !is_numeric($post_id) ) {
                continue;
            }

            foreach ( $post['postmeta'] as $k => $v ) {
                if ( '_edit_last' == $k ) {
                    $v = FrmAppHelper::get_user_id_param($v);
                } else if ( '_thumbnail_id' == $k ) {
                    //change the attachment ID
                    $v = FrmProXMLHelper::get_file_id($v);
                }

                update_post_meta($post_id, $k, $v);

                unset($k, $v);
            }

            switch ( $post['post_type'] ) {
                case 'frm_display':
                    $this_type = 'views';
                break;
                case $form_action_type:
                    $this_type = 'actions';
                break;
                default:
                    $this_type = 'posts';
                break;
            }

            if ( isset($post['ID']) ) {
                $imported['updated'][ $this_type ]++;
            } else {
                $imported['imported'][ $this_type ]++;
            }

            unset($post);

			$imported['posts'][ (int) $old_id] = $post_id;
		}

		return $imported;
    }

	public static function cdata( $str ) {
	    $str = maybe_unserialize($str);
	    if ( is_array($str) ) {
	        $str = json_encode($str);
	    } else if (seems_utf8( $str ) == false ) {
			$str = utf8_encode( $str );
		}

        if ( is_numeric($str) ) {
            return $str;
        }

		// $str = ent2ncr(esc_html($str));
		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

}
