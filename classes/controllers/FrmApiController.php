<?php
/**
 * @package Formidable
 */
//new FrmApiController();

class FrmApiController{
    public static $base = '/frm_api';
    
    function FrmApiController(){
        if ( strpos( $_SERVER['REQUEST_URI'], self::get_api_base() ) !== false)
    		add_action( 'wp_loaded', 'FrmApiController::load_api' );
    	
    	add_action( 'wp_json_server_before_serve', 'FrmApiController::default_filters', 10, 1 );
    }
    
    function default_filters($server) {
        global $wp_json_posts;
        $wp_json_posts = new WP_JSON_Posts($server);
	    add_filter( 'json_endpoints', array( $wp_json_posts, 'registerRoutes' ), 0 );
	}
	
    public static function get_api_base() {
        //TODO: tack on API key here
    	return '/' . trim( self::$base, '/' ) . '/';
    }

    public static function api_base_url() {
    	return home_url( get_api_base() );
    }
	
	public static function get_forms(){
	    $frm_form = new FrmForm();
	    $forms = $frm_form->getAll();
	    return (array)$forms;
	}
	
	public static function get_form($app, $id){
	    $form = FrmAppController::get_form_shortcode(array('id' => $id));
	    return (array)$form;
	}
	
	public static function get_fields($app, $id){
	    $frm_field = new FrmField();
	    $where = array();
	    if(is_numeric($id))
	        $where['fi.form_id'] = $id;
	    else
	        $where['fr.form_key'] = $id;
	    $fields = $frm_field->getAll($where, 'field_order');
	    return (array)$fields;
	}
	
	public static function get_entries($app, $form_id){
	    //?Filter1=EntryId+Is_equal_to+1
	    //?Filter1=EntryId+Is_after+1&Filter2=EntryId+Is_before+200&match=AND
	    //pageStart=0&pageSize=25
	    //orderby={ID}&order={DESC|ASC}
	    $frm_entry = new FrmEntry();
	    $where = array('fi.form_id' => $form_id);
	    $entries = $frm_entry->getAll($where);
	    return (array)$entries;
	}
	
	public static function get_entry($app, $id){
	    $frm_entry = new FrmEntry();
	    $entry = $frm_entry->getOne($id);
	    return array('entry' => (array)$entry);
	}

    public static function create_entry(){
        $frm_entry = new FrmEntry();
        $response = array();
        
        if(!isset($_POST['item_meta']) or empty($_POST['item_meta'])){
            foreach($_POST as $k => $v){
                if(is_numeric($k))
                    $_POST['item_meta'][$k] = $v;
                unset($k);
                unset($v);
            }
        }
        
        $errors = $frm_entry->validate($_POST, false, true);
        
        if(!empty($errors)){
            $response['success'] = '0';
            $response['errors'] = $errors;
            return $response;
        }
        
        if($id = $frm_entry->create($_POST)){
            $response['success'] = 1;
            $response['entry_id'] = $id;
            //$response['entry_link'] = FrmApiController::api_base_url() .'/v'. $version .'/entry/'. $id;
            //$response['redirect_url']
        }else{
            $response['success'] = '0';
        }
        return $response;
    }
    
    public static function update_entry($app, $id){
        
    }
    
    public static function delete_entry($app, $id){
        
    }
    
    /********* DISPLAY DATA *************/
    function frm_filter_content($args){
        global $frm_entry;
        $args = explode(",",$args[1]);

        $form_key = sanitize_title($args[0]);

        $where = '';//" fr.form_key = '$form_key'";
    	$items = $frm_entry->getAll($where);

    	$list = $form_key;
    	foreach ($items as $item){
    	    $list .= $item->name;
    	}

    	return $list;
    }

    function get_frm_items($args = null){ 
        global $frm_entry;

        $defaults = array(
        	'form_key' => '', 'order' => '', 'limit' => '',
        	'search' =>'', 'search_type' => '',
        	'search_field' => '', 'search_operator' => 'LIKE'
        );

        $r = wp_parse_args( $args, $defaults ); 

        $frm_form = new FrmForm();
        $form = $frm_form->getOne($r['form_key']);

        $where = " (it.form_id='". $form->id ."')";

        if (!($r['order'] == ''))
            $r['order'] = " ORDER BY {$r['order']}";

        if (!($r['limit'] == ''))
            $r['limit'] = " LIMIT {$r['limit']}";

        if (!($r['search'] == '') and $r['search_type'] == '')
            $where .= " and (it.item_key LIKE '%{$r['search']}%' or it.description LIKE '%{$r['search']}%' or it.name LIKE '%{$r['search']}%')";

    	$items = $frm_entry->getAll($where, $r['order'], $r['limit']);

    	if (!($r['search'] == '') and $r['search_type'] == 'meta'){ //search meta values
    	    $frm_entry_meta = new FrmEntryMeta();
    	    $item_ids = $frm_entry_meta->search_entry_metas($r['search'], $r['search_field'], $r['search_operator']);
            $item_list = array();
            foreach ($items as $item){
                if (in_array($item->id, $item_ids))
                    $item_list[] = $item;
            }
            return $item_list;
    	}else
            return $items; 
    }

    function get_frm_item($item_key){
        global $frm_entry;
        return $frm_entry->getOne( $item_key );
    }

    function get_frm_item_by_id($id){
        global $frm_entry;
        return $frm_entry->getOne( $id );
    }

}
