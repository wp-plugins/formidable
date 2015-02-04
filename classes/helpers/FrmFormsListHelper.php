<?php
if ( !defined('ABSPATH') ) die('You are not allowed to call this page directly.');

class FrmFormsListHelper extends FrmListHelper {
    var $status = '';

	function __construct($args) {
		$this->status = isset( $_REQUEST['form_type'] ) ? $_REQUEST['form_type'] : '';

		parent::__construct( $args );
	}

	function prepare_items() {
	    global $wpdb, $per_page, $mode;

	    $mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		$default_orderby = 'name';
		$default_order = 'ASC';

        $orderby = ( isset( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : $default_orderby;
		$order = ( isset( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : $default_order;

		$page = $this->get_pagenum();
		$per_page = $this->get_items_per_page( 'formidable_page_formidable_per_page' );

		$start = ( isset( $_REQUEST['start'] ) ) ? $_REQUEST['start'] : (( $page - 1 ) * $per_page);

		$s_query = ' (parent_form_id IS NULL OR parent_form_id < 1) AND ';
		switch ( $this->status ) {
		    case 'template':
		        $s_query .=  "is_template = 1 AND status != 'trash'";
		        break;
		    case 'draft':
		        $s_query .=  "is_template = 0 AND status = 'draft'";
		        break;
		    case 'trash':
		        $s_query .= "status='trash'";
		        break;
		    default:
		        $s_query .= "is_template = 0 AND status != 'trash'";
		        break;
		}

        $s = isset( $_REQUEST['s'] ) ? stripslashes($_REQUEST['s']) : '';
	    if ( $s != '' ) {
	        preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches);
		    $search_terms = array_map('trim', $matches[0]);
	        foreach ( (array) $search_terms as $term ) {
	            if ( !empty($s_query) ) {
                    $s_query .= " AND";
                }

	            $term = FrmAppHelper::esc_like($term);

	            $s_query .= $wpdb->prepare(" (name like %s OR description like %s OR created_at like %s)", '%'. $term .'%', '%'. $term .'%', '%'. $term .'%');

	            unset($term);
            }
	    }

        $this->items = FrmForm::getAll($s_query, $orderby .' '. $order, $start .','. $per_page);
        $total_items = FrmAppHelper::getRecordCount($s_query, $wpdb->prefix .'frm_forms');


		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page
		) );
	}

	function no_items() {
	    if ( 'template' == $this->status ) {
            _e('No Templates Found', 'formidable') ?>.
            <br/><br/><?php _e('To add a new template','formidable') ?>:
            <ol><li><?php printf(__('Create a new %1$sform%2$s.', 'formidable'), '<a href="?page=formidable&amp;frm_action=new-selection">', '</a>') ?></li>
                <li><?php printf(__('After your form is created, go to Formidable -> %1$sForms%2$s.', 'formidable'), '<a href="?page=formidable">', '</a>') ?></li>
                <li><?php _e('Place your mouse over the name of the form you just created, and click the "Create Template" link.', 'formidable') ?></li>
            </ol>
<?php   }else{
            _e('No Forms Found', 'formidable') ?>.
            <a href="?page=formidable&amp;frm_action=new-selection"><?php _e('Add New', 'formidable'); ?></a>
<?php   }
	}

	function get_bulk_actions(){
	    $actions = array();

	    if ( in_array($this->status, array('', 'published')) ) {
	        $actions['bulk_create_template'] = __('Create Template', 'formidable');
	    }

	    if ( 'trash' == $this->status ) {
	        if ( current_user_can('frm_edit_forms') ) {
	            $actions['bulk_untrash'] = __('Restore', 'formidable');
	        }

	        if ( current_user_can('frm_delete_forms') ) {
	            $actions['bulk_delete'] = __('Delete Permanently', 'formidable');
	        }
	    } else if ( EMPTY_TRASH_DAYS && current_user_can('frm_delete_forms') ) {
	        $actions['bulk_trash'] = __('Move to Trash', 'formidable');
	    } else if ( current_user_can('frm_delete_forms') ) {
	        $actions['bulk_delete'] = __('Delete');
	    }

        return $actions;
    }

    function extra_tablenav( $which ) {
        if ( 'top' != $which || 'template' != $this->status ) {
            return;
        }

        $where = apply_filters('frm_forms_dropdown', "(parent_form_id IS NULL OR parent_form_id < 1) AND is_template=0 AND (status is NULL OR status = '' OR status = 'published')", '');

        $forms = FrmForm::getAll($where, 'name');

        $base = admin_url('admin.php?page=formidable&form_type=template');
        $args = array(
            'frm_action'    => 'duplicate',
            'template'      => true,
        );

?>
    <div class="alignleft actions" style="overflow:visible;">
    <div class="button dropdown" style="margin-top:1px;">
        <a href="#" id="frm-templateDrop" class="frm-dropdown-toggle" data-toggle="dropdown"><?php _e('Create New Template', 'formidable') ?> <b class="caret"></b></a>
		<ul class="frm-dropdown-menu" role="menu" aria-labelledby="frm-templateDrop">
		<?php foreach ( $forms as $form ) {
		        $args['id'] = $form->id; ?>
			<li><a href="<?php echo add_query_arg($args, $base); ?>" tabindex="-1"><?php echo empty($form->name) ? __('(no title)') : FrmAppHelper::truncate($form->name, 33); ?></a></li>
			<?php
			    unset($form);
			} ?>
		</ul>
	</div>
	</div>
<?php
	}

	function get_views() {

		$statuses = array(
		    'published' => __('My Forms', 'formidable'),
		    'template'  => __('Templates', 'formidable'),
		    'draft'     => __('Drafts', 'formidable'),
		    'trash'     => __('Trash', 'formidable'),
		);

	    $links = array();
	    $counts = FrmForm::get_count();

	    foreach ( $statuses as $status => $name ) {

	        if ( (isset($_REQUEST['form_type']) && $status == $_REQUEST['form_type']) || ( !isset($_REQUEST['form_type']) && 'published' == $status ) ) {
    			$class = ' class="current"';
    		} else {
    		    $class = '';
    		}

    		if ( $counts->{$status} || 'published' == $status ) {
		        $links[$status] = '<a href="?page=formidable&form_type='. $status .'" '. $class .'>'. sprintf( __('%1$s <span class="count">(%2$s)</span>', 'formidable'), $name, number_format_i18n( $counts->{$status} ) ) .'</a>';
		    }

		    unset($status, $name);
	    }

		return $links;
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination ( $which );

		if ( 'top' == $which ) {
			$this->view_switcher( $mode );
		}
	}

	function single_row( $item, $style='') {
	    global $frm_vars, $mode;

		// Set up the hover actions for this user
		$actions = array();
        $edit_link = '?page=formidable&frm_action=edit&id='. $item->id;
	    $duplicate_link = '?page=formidable&frm_action=duplicate&id='. $item->id;

        $this->get_actions($actions, $item, $edit_link, $duplicate_link);

        $action_links = $this->row_actions( $actions );

		// Set up the checkbox ( because the user is editable, otherwise its empty )
		$checkbox = '<input type="checkbox" name="item-action[]" id="cb-item-action-'. $item->id .'" value="'. $item->id .'" />';

		$r = '<tr id="item-action-'. $item->id .'"'. $style .'>';

		list( $columns, $hidden ) = $this->get_column_info();

        $format = 'Y/m/d';
        if ( 'list' != $mode ) {
            $format .= ' \<\b\r \/\> g:i:s a';
		}

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = 'class="'. $column_name .' column-'. $column_name . ( ('name' == $column_name) ? ' post-title page-title column-title' : '' ) .'"';

			$style = '';
			if ( in_array( $column_name, $hidden ) ) {
				$style = ' style="display:none;"';
			}

			$attributes = $class . $style;

			switch ( $column_name ) {
				case 'cb':
					$r .= '<th scope="row" class="check-column">'. $checkbox .'</th>';
					break;
				case 'id':
				case 'form_key':
				    $val = $item->{$column_name};
				    break;
				case 'name':
				    $val = $this->get_form_name( $item, $actions, $edit_link );
			        $val .= $action_links;

				    break;
				case 'created_at':
				    $date = date($format, strtotime($item->created_at));
					$val = '<abbr title="'. date('Y/m/d g:i:s A', strtotime($item->created_at)) .'">'. $date .'</abbr>';
					break;
				case 'shortcode':
				    $val = '<input type="text" readonly="true" class="frm_select_box" value="'. esc_attr("[formidable id={$item->id}]") .'" /><br/>';
				    if ( 'excerpt' == $mode ) {
				        $val .= '<input type="text" readonly="true" class="frm_select_box" value="'. esc_attr("[formidable key={$item->form_key}]") .'" />';
				    }
			        break;
			    case 'entries':
			        if( isset($item->options['no_save']) && $item->options['no_save'] ) {
			            $val = '<i class="frm_icon_font frm_forbid_icon frm_bstooltip" title="'. esc_attr('Entries are not being saved', 'formidable') .'"></i>';
			        } else {
			            $text = FrmEntry::getRecordCount($item->id);
                        $val = (current_user_can('frm_view_entries')) ? '<a href="'. esc_url(admin_url('admin.php') .'?page=formidable-entries&form='. $item->id ) .'">'. $text .'</a>' : $text;
                        unset($text);
                    }
			        break;
                case 'type':
                    $val = ( $item->is_template && $item->default_template ) ? __('Default', 'formidable') : __('Custom', 'formidable');
                    break;
			}

			if ( isset($val) ) {
			    $r .= "<td $attributes>";
			    $r .= $val;
			    $r .= '</td>';
			}
			unset($val);
		}
		$r .= '</tr>';

		return $r;
	}

    private function get_actions( &$actions, $item, $edit_link, $duplicate_link ) {
		if ( 'trash' == $this->status ) {
		    $actions['restore'] = FrmFormsHelper::delete_trash_link($item->id, $item->status, 'short');
		    if ( current_user_can('frm_delete_forms') && ! $item->default_template ) {
    		    $actions['trash'] = '<a href="' . esc_url(wp_nonce_url( '?page=formidable&form_status=trash&frm_action=destroy&id='. $item->id, 'destroy_form_'. $item->id )) .'" class="submitdelete"  onclick="return confirm(\''. __('Are you sure you want to permanently delete that?', 'formidable') .'\')">' . __( 'Delete Permanently' ) . '</a>';
    		}
            return;
		}

		if ( current_user_can('frm_edit_forms') ) {
            if ( ! $item->is_template || ! $item->default_template ) {
		        $actions['frm_edit'] = '<a href="'. esc_url( $edit_link ) . '">'. __('Edit') .'</a>';
            }

		    if ( $item->is_template ) {
		        $actions['frm_duplicate'] = '<a href="'. wp_nonce_url( $duplicate_link ) .'">'. __('Create Form from Template', 'formidable') .'</a>';
            } else {
    		    $actions['frm_settings'] = '<a href="'. esc_url('?page=formidable&frm_action=settings&id='. $item->id ) . '">'. __('Settings', 'formidable') .'</a>';

    		    if ( FrmAppHelper::pro_is_installed() ) {
        	        $actions['duplicate'] = '<a href="' . wp_nonce_url( $duplicate_link ) . '">'. __('Duplicate', 'formidable') .'</a>';
        	    }
        	}
        }

        if ( ! $item->default_template ) {
		    $actions['trash'] = FrmFormsHelper::delete_trash_link($item->id, $item->status, 'short');
		}

		$actions['view'] = '<a href="'. FrmFormsHelper::get_direct_link($item->form_key, $item) .'" target="_blank">'. __('Preview') .'</a>';
    }

    private function get_form_name( $item, $actions, $edit_link ) {
        $form_name = $item->name;
        if ( trim($form_name) == '' ) {
            $form_name = __('(no title)');
        }
        $form_name = FrmAppHelper::truncate(strip_tags($form_name), 50);

        $val = '<strong>';
        if ( 'trash' == $this->status ) {
            $val .= $form_name;
        } else {
            $val .= '<a href="'. ( isset($actions['frm_edit']) ? $edit_link : FrmFormsHelper::get_direct_link($item->form_key, $item) ) .'" class="row-title">'. $form_name .'</a> ';
        }

        $this->add_draft_label( $item, $val );
        $val .= '</strong>';

        $this->add_form_description( $item, $val );

        return $val;
    }

    private function add_draft_label( $item, &$val ) {
        if ( 'draft' == $item->status && 'draft' != $this->status ) {
            $val .= ' - <span class="post-state">'. __('Draft', 'formidable') .'</span>';
        }
    }

    private function add_form_description( $item, &$val ) {
        global $mode;
        if ( 'excerpt' == $mode ) {
            $val .= FrmAppHelper::truncate(strip_tags($item->description), 50);
        }
    }
}
