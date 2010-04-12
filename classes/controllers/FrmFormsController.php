<?php

class FrmFormsController{
    function FrmFormsController(){
        add_action('admin_menu', array( $this, 'menu' ));
        add_action('admin_head-toplevel_page_'.FRM_PLUGIN_NAME, array($this,'head'));
        add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-new', array($this,'head'));
        add_action('admin_head-'.FRM_PLUGIN_NAME.'_page_'.FRM_PLUGIN_NAME.'-templates', array($this,'head'));
        add_action('wp_ajax_frm_form_name_in_place_edit', array($this, 'edit_name') );
        add_action('wp_ajax_frm_form_desc_in_place_edit', array($this, 'edit_description') );
        add_action('wp_ajax_frm_delete_form_wo_fields',array($this, 'destroy_wo_fields'));
    }
    
    function menu(){
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Forms', FRM_PLUGIN_NAME), __('Forms', FRM_PLUGIN_NAME), 'administrator', FRM_PLUGIN_NAME, array($this,'route'));
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Create a Form', FRM_PLUGIN_NAME), __('Create a Form', FRM_PLUGIN_NAME), 'administrator', FRM_PLUGIN_NAME.'-new', array($this,'new_form'));
        add_submenu_page(FRM_PLUGIN_NAME, FRM_PLUGIN_TITLE .' | '. __('Templates', FRM_PLUGIN_NAME), __('Templates', FRM_PLUGIN_NAME), 'administrator', FRM_PLUGIN_NAME.'-templates', array($this, 'template_list'));
    }
    
    function head(){
        $css_file = apply_filters('get_frm_stylesheet', FRM_URL .'/css/frm_display.css');
        $js_file  = array(FRM_URL . '/js/jquery/jquery-ui-themepicker.js', FRM_URL.'/js/jquery/jquery.editinplace.packed.js');
        require(FRM_VIEWS_PATH . '/shared/head.php');
    }
    
    function list_form(){
        $params = $this->get_params();
        $errors = apply_filters('frm_admin_list_form_action', $errors);
        return $this->display_forms_list($params, '', false, false, $errors);
    }
    
    function template_list(){
        $_POST['template'] = 1;
        return $this->display_forms_list();
    }
    
    function new_form(){
        global $frm_app_controller, $frm_form, $frmpro_is_installed, $frm_ajax_url;
        
        $action = $frm_app_controller->get_param('action');
        if ($action == 'create')
            return $this->create();
        else if ($action == 'new'){
            $frm_field_selection = FrmFieldsHelper::field_selection();  
            $values = FrmFormsHelper::setup_new_vars();
            $id = $frm_form->create( $values );
            require_once(FRM_VIEWS_PATH.'/frm-forms/new.php');
        }else{
            $all_templates = $frm_form->getAll('is_template=1',' ORDER BY name');
            require_once(FRM_VIEWS_PATH.'/frm-forms/new-selection.php');
        }
    }
    
    function create(){
        global $frm_app_controller, $frm_app_helper, $frm_entry, $frm_form, $frm_field, $frmpro_is_installed;
        $errors = $frm_form->validate($_POST);
        $id = $frm_app_controller->get_param('id');
        
        if( count($errors) > 0 ){
            $frm_field_selection = FrmFieldsHelper::field_selection();
            $record = $frm_form->getOne( $id );
            $fields = $frm_field->getAll("fi.form_id=$id", ' ORDER BY field_order');
            $values = FrmAppHelper::setup_edit_vars($record,'forms',$fields,true);
            require_once(FRM_VIEWS_PATH.'/frm-forms/new.php');
        }else{
            $items = $frm_entry->getAll('',' ORDER BY it.name');
            $record = $frm_form->update( $id, $_POST, true );
            $message = __('Form was Successfully Created', FRM_PLUGIN_NAME);
            $params = $this->get_params();
            return $this->display_forms_list($params, $message);
        }
         
    }
    
    function edit(){
        global $frm_app_controller;
        $id = $frm_app_controller->get_param('id');
        return $this->get_edit_vars($id);
    }
    
    function edit_name(){
        global $frm_form;
        $values = array('name' => $_POST['update_value']);
        $form = $frm_form->update($_POST['form_id'], $values);
        echo stripslashes($_POST['update_value']);  
        die();
    }

    function edit_description(){
        global $frm_form;
        $form = $frm_form->update($_POST['form_id'], array('description' => $_POST['update_value']));
        echo wpautop(stripslashes($_POST['update_value']));
        die();
    }
    
    function update(){
        global $frm_form, $frm_app_controller;
        $errors = $frm_form->validate($_POST);
        $id = $frm_app_controller->get_param('id');
        if( count($errors) > 0 ){
            return $this->get_edit_vars($id, $errors);
        }else{
            $record = $frm_form->update( $_POST['id'], $_POST );
            $message = __('Form was Successfully Updated', FRM_PLUGIN_NAME);
            return $this->get_edit_vars($id, '', $message);
        }
    }
    
    function duplicate(){
        global $frm_form;
        
        $params = $this->get_params();
        $record = $frm_form->duplicate( $params['id'], $params['template'] );
        $message = ($params['template']) ? __('Form template was Successfully Created', FRM_PLUGIN_NAME) : __('Form was Successfully Copied', FRM_PLUGIN_NAME);
        if ($record)
            return $this->get_edit_vars($record, '', $message, true);
        else
            return $this->display_forms_list($params, __('There was a problem creating new template.', FRM_PLUGIN_NAME));
    }
    
    function page_preview(){
        global $frm_form;
        $description = $title = true;
        $params = $this->get_params();
        if (!$params['form']) return;
        $form = $frm_form->getOne($params['form']);
        require(FRM_VIEWS_PATH.'/frm-entries/frm-entry.php');
    }

    function preview(){
        global $frm_form, $frm_settings;
        if ( !defined( 'ABSPATH' ) && !defined( 'XMLRPC_REQUEST' )) {
            $root = dirname(dirname(dirname(dirname(__FILE__))));
            include_once( $root.'/wp-config.php' );
            $wp->init();
            $wp->register_globals();
        }

        header("Content-Type: text/html; charset=utf-8");

        $plugin     = FrmAppController::get_param('plugin');
        $controller = FrmAppController::get_param('controller');
        $key = (isset($_GET['form']) ? $_GET['form'] : (isset($_POST['form']) ? $_POST['form'] : ''));
        $form = $frm_form->getAll("form_key='$key'",'',' LIMIT 1');
        if (!$form) $form = $frm_form->getAll('','',' LIMIT 1');
        $form_options = stripslashes_deep(maybe_unserialize($form->options));
        $description = $title = true;
        $custom_style = (isset($form_options['custom_style'])) ? $form_options['custom_style'] : $frm_settings->custom_style;
        
        require_once(FRM_VIEWS_PATH.'/frm-entries/direct.php');   
    }
    
    function destroy(){
        global $frm_form;
        $params = $this->get_params();
        $message = '';
        if ($frm_form->destroy( $params['id'] ))
            $message = __('Form was Successfully Deleted', FRM_PLUGIN_NAME);
        $this->display_forms_list($params, $message, '', 1);
    }
    
    function destroy_wo_fields(){
        global $frm_field, $frm_form, $frm_app_helper;
        $id = $_POST['form_id'];
        if ($frm_app_helper->getRecordCount('form_id='.$id, $frm_field->table_name) <= 0)
            $frm_form->destroy($id);
        die();
    }

    function display_forms_list($params=false, $message='', $page_params_ov = false, $current_page_ov = false, $errors = array()){
        global $wpdb, $frm_app_helper, $frm_form, $frm_entry, $frm_page_size, $frmpro_is_installed;
        
        if(!$params)
            $params = $this->get_params();
            
        if($message=='')
            $message = FrmAppHelper::frm_get_main_message();

        $controller_file = FRM_PLUGIN_NAME;
        $page_params = '';
        $where_clause = " (status is NULL OR status = '' OR status = 'published') AND default_template=0 AND is_template = ".$params['template'];

        if ($params['template']){
            $default_templates = $frm_form->getAll('default_template=1');
            $all_templates = $frm_form->getAll('is_template=1',' ORDER BY name');
        }

        $form_vars = $this->get_form_sort_vars($params, $where_clause);

        if($current_page_ov)
          $current_page = $current_page_ov;
        else
          $current_page = $params['paged'];

        if($page_params_ov)
          $page_params = $page_params_ov;
        else
          $page_params = $form_vars['page_params'];

        $sort_str = $form_vars['sort_str'];
        $sdir_str = $form_vars['sdir_str'];
        $search_str = $form_vars['search_str'];

        $record_count = $frm_app_helper->getRecordCount($form_vars['where_clause'], $frm_form->table_name);
        $page_count = $frm_app_helper->getPageCount($frm_page_size,$form_vars['where_clause'], $frm_form->table_name);
        $forms = $frm_app_helper->getPage($current_page, $frm_page_size, $form_vars['where_clause'], $form_vars['order_by'], $frm_form->table_name);
        $page_last_record = $frm_app_helper->getLastRecordNum($record_count,$current_page,$frm_page_size);
        $page_first_record = $frm_app_helper->getFirstRecordNum($record_count,$current_page,$frm_page_size);
        require_once(FRM_VIEWS_PATH.'/frm-forms/list.php');
    }
    
    function get_form_sort_vars($params,$where_clause = ''){
        $order_by = '';
        $page_params = '';

        // These will have to work with both get and post
        $sort_str = $params['sort'];
        $sdir_str = $params['sdir'];
        $search_str = $params['search'];

        // Insert search string
        if(!empty($search_str)){
            $search_params = explode(" ", $search_str);

            foreach($search_params as $search_param){
                if(!empty($where_clause))
                    $where_clause .= " AND";

                $where_clause .= " (name like '%$search_param%' OR description like '%$search_param%' OR created_at like '%$search_param%')";
            }

            $page_params .="&search=$search_str";
        }

        // make sure page params stay correct
        if(!empty($sort_str))
            $page_params .="&sort=$sort_str";

        if(!empty($sdir_str))
            $page_params .= "&sdir=$sdir_str";

        // Add order by clause
        switch($sort_str){
            case "id":
            case "name":
            case "description":
            case "form_key":
                $order_by .= " ORDER BY $sort_str";
                break;
            default:
                $order_by .= " ORDER BY name";
        }

        // Toggle ascending / descending
        if((empty($sort_str) and empty($sdir_str)) or $sdir_str == 'asc'){
            $order_by .= ' ASC';
            $sdir_str = 'asc';
        }else{
            $order_by .= ' DESC';
            $sdir_str = 'desc';
        }

        return array('order_by' => $order_by,
                     'sort_str' => $sort_str, 
                     'sdir_str' => $sdir_str, 
                     'search_str' => $search_str, 
                     'where_clause' => $where_clause, 
                     'page_params' => $page_params);
    }

    function get_edit_vars($id, $errors = '', $message='', $create_link=false){
        global $frm_app_helper, $frm_entry, $frm_form, $frm_field, $frmpro_is_installed, $frm_ajax_url;
        $record = $frm_form->getOne( $id );
        $items = $frm_entry->getAll('',' ORDER BY it.name');
        $frm_field_selection = FrmFieldsHelper::field_selection();
        $fields = $frm_field->getAll("fi.form_id=$id", ' ORDER BY field_order');
        $values = FrmAppHelper::setup_edit_vars($record,'forms',$fields,true);
        if (isset($values['default_template']) && $values['default_template'])
            wp_die(__('That template cannot be edited', FRM_PLUGIN_NAME));
        else if($create_link)
            require_once(FRM_VIEWS_PATH.'/frm-forms/new.php');
        else
            require_once(FRM_VIEWS_PATH.'/frm-forms/edit.php');
    }
    
    function get_params(){
        global $frm_app_controller;
        $values = array();
        foreach (array('template' => 0,'id' => '','paged' => 1,'form' => '','search' => '','sort' => '','sdir' => '') as $var => $default)
            $values[$var] = $frm_app_controller->get_param($var, $default);

        return $values;
    }
    
    function add_default_templates($path, $default=true){
        global $frm_form, $frm_field;
        $templates = glob($path."/*.php");
        
        for($i = count($templates) - 1; $i >= 0; $i--){
            $filename = preg_replace("#".$path."/#","",$templates[$i]);
            $filename = str_replace('.php','', $filename);
            $template_query = "form_key='{$filename}' and is_template='1'";
            if($default) $template_query .= " and default_template='1'";
            $form = $frm_form->getAll($template_query, '', ' LIMIT 1');
            
            $values = FrmFormsHelper::setup_new_vars();
            $values['form_key'] = $filename;
            $values['is_template'] = 1;
            $values['status'] = 'published';
            if($default) $values['default_template'] = 1;
            
            require_once($templates[$i]);
        }
    }

    function route(){
        $action = FrmAppController::get_param('action');
        if($action=='new')
            return $this->new_form();
        else if($action=='create')
            return $this->create();
        else if($action=='edit')
            return $this->edit();
        else if($action=='update')
            return $this->update();
        else if($action=='duplicate')
            return $this->duplicate();
        else if($action == 'destroy')
            return $this->destroy();
        else if($action == 'list-form')
            return $this->list_form();        
        else
            return $this->display_forms_list();
    }

}
?>