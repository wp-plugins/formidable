<?php

class FrmFieldsHelper{
    
    function field_selection(){
        $fields = apply_filters('frm_available_fields', array(
            'text' => __('Single Line Text', 'formidable'),
            'textarea' => __('Paragraph Text', 'formidable'),
            'checkbox' => __('Checkboxes', 'formidable'),
            'radio' => __('Radio Buttons', 'formidable'),
            'select' => __('Dropdown', 'formidable'),
            'captcha' => __('reCAPTCHA', 'formidable')
            //'nucaptcha' => __('NuCaptcha (SPAM Control)', 'formidable')
        ));
        
        return $fields;
    }
    
    function pro_field_selection(){
        return apply_filters('frm_pro_available_fields', array(
            'email' => __('Email Address', 'formidable'),
            'url' => __('Website/URL', 'formidable'),
            'divider' => __('Section Heading', 'formidable'),
            'break' => __('Page Break', 'formidable'),
            'file' => __('File Upload', 'formidable'),
            'rte' => __('Rich Text', 'formidable'), 
            'number' => __('Number', 'formidable'), 
            'phone' => __('Phone Number', 'formidable'), 
            'date' => __('Date', 'formidable'), 
            'time' => __('Time', 'formidable'),
            'image' => __('Image URL', 'formidable'), 
            'scale' => __('Scale', 'formidable'),
            //'grid' => __('Grid', 'formidable'),
            'data' => __('Data from Entries', 'formidable'),
            //'form' => __('SubForm', 'formidable'),
            'hidden' => __('Hidden Field', 'formidable'), 
            'user_id' => __('User ID (hidden)', 'formidable'),
            'password'  => __('Password', 'formidable'),
            'html' => __('HTML', 'formidable'),
            'tag' => __('Tags', 'formidable')
            //'address' => 'Address' //Address line 1, Address line 2, City, State/Providence, Postal Code, Select Country 
            //'city_selector' => 'US State/County/City selector', 
            //'full_name' => 'First and Last Name', 
            //'quiz' => 'Question and Answer' // for captcha alternative
        ));
    }
    
    function setup_new_vars($type='', $form_id=''){
        global $frm_settings;
        
        $defaults = FrmFieldsHelper::get_default_field_opts($type, $form_id);
        $defaults['field_options']['custom_html'] = FrmFieldsHelper::get_default_html($type);

        $values = array();
        
        foreach ($defaults as $var => $default){
            if($var == 'field_options'){
                $values['field_options'] = array();
                foreach ($default as $opt_var => $opt_default){
                    $values['field_options'][$opt_var] = $opt_default;
                    unset($opt_var);
                    unset($opt_default);
                }
            }else{
                $values[$var] = $default;
            }
            unset($var);
            unset($default);
        }
            
        if ($type == 'radio' || ($type == 'checkbox'))
            $values['options'] = serialize(array(__('Option 1', 'formidable'), __('Option 2', 'formidable')));
        else if ( $type == 'select')
            $values['options'] = serialize(array('', __('Option 1', 'formidable')));
        else if ($type == 'textarea')
            $values['field_options']['max'] = '5';
        else if ($type == 'captcha')
            $values['invalid'] = $frm_settings->re_msg;
        
        return $values;
    }
    
    function setup_edit_vars($record){
        global $frm_entry_meta, $frm_form;
        
        $values = array('id' => $record->id, 'form_id' => $record->form_id);
        //$record->field_options = maybe_unserialize($record->field_options);

        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default)
              $values[$var] = htmlspecialchars(FrmAppHelper::get_param($var, $default));

        $values['form_name'] = ($record->form_id) ? $frm_form->getName( $record->form_id ) : '';
        
        foreach (array('field_key' => $record->field_key, 'type' => $record->type, 'default_value'=> $record->default_value, 'field_order' => $record->field_order, 'required' => $record->required) as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);
        
        $values['options'] = $record->options;
        $values['field_options'] = $record->field_options;
        
        $defaults = FrmFieldsHelper::get_default_field_opts($values['type'], $record, true);
        
        if($values['type'] == 'captcha'){
            global $frm_settings;
            $defaults['invalid'] = $frm_settings->re_msg;
        }
            
        foreach($defaults as $opt => $default)
            $values[$opt] = (isset($record->field_options[$opt])) ? $record->field_options[$opt] : $default; 

        $values['custom_html'] = (isset($record->field_options['custom_html'])) ? $record->field_options['custom_html'] : FrmFieldsHelper::get_default_html($record->type);
        
        return apply_filters('frm_setup_edit_field_vars', $values, $values['field_options']);
    }
    
    function get_default_field_opts($type, $field, $limit=false){
        $field_options = array(
            'size' => '', 'max' => '', 'label' => '', 'blank' => '', 
            'required_indicator' => '*', 'invalid' => '', 'separate_value' => 0,
            'clear_on_focus' => 0, 'default_blank' => 0, 'classes' => '',
            'custom_html' => ''
        );
        
        if($limit)
            return $field_options;
        
        global $frmdb, $frm_app_helper, $frm_settings;
        
        $form_id = (is_numeric($field)) ? $field : $field->form_id;
        
        $key = is_numeric($field) ? FrmAppHelper::get_unique_key('', $frmdb->fields, 'field_key') : $field->field_key;
        $field_count = $frm_app_helper->getRecordCount("form_id='$form_id'", $frmdb->fields);
        
        return array(
            'name' => __('Untitled', 'formidable'), 'description' => '', 
            'field_key' => $key, 'type' => $type, 'options'=>'', 'default_value'=>'', 
            'field_order' => $field_count+1, 'required' => false, 
            'blank' => $frm_settings->blank_msg, 'unique_msg' => $frm_settings->unique_msg,
            'invalid' => __('This field is invalid', 'formidable'), 'form_id' => $form_id,
            'field_options' => $field_options
        );
    }
    
    function get_form_fields($form_id, $error=false){
        global $frm_field;
        $fields = apply_filters('frm_get_paged_fields', false, $form_id, $error);
        if (!$fields)
            $fields = $frm_field->getAll(array('fi.form_id' => $form_id), 'field_order');
        return $fields;
    }
    
    function get_default_html($type='text'){
        if (apply_filters('frm_normal_field_type_html', true, $type)){
            if(in_array($type, array('radio', 'checkbox', 'data'))){
                $for = 'for="field_[key]"';
                $input = '<div class="frm_opt_container">[input]</div>';
            }else{
                $for = '';
                $input = '[input]';
            }
            
            $default_html = <<<DEFAULT_HTML
<div id="frm_field_[id]_container" class="frm_form_field form-field [required_class][error_class]">
    <label $for class="frm_primary_label">[field_name]
        <span class="frm_required">[required_label]</span>
    </label>
    $input
    [if description]<div class="frm_description">[description]</div>[/if description]
    [if error]<div class="frm_error">[error]</div>[/if error]
</div>
DEFAULT_HTML;
        }else
            $default_html = apply_filters('frm_other_custom_html', '', $type);

        return apply_filters('frm_custom_html', $default_html, $type);
    }
    
    function replace_shortcodes($html, $field, $errors=array(), $form=false){
        global $frm_readonly;
        
        $html = stripslashes($html);
        $html = apply_filters('frm_before_replace_shortcodes', $html, $field, $errors, $form);
        
        $field_name = 'item_meta['. $field['id'] .']';
        if(isset($field['multiple']) and $field['multiple'] and ($field['type'] == 'select' or ($field['type'] == 'data' and isset($field['data_type']) and $field['data_type'] == 'select')))
            $field_name .= '[]';
        
        //replace [id]
        $html = str_replace('[id]', $field['id'], $html);
        
        //replace [key]        
        $html = str_replace('[key]', $field['field_key'], $html);
        
        //replace [description] and [required_label] and [error]
        $required = ($field['required'] == '0') ? '' : $field['required_indicator'];
        if(!is_array($errors))
            $errors = array();
        $error = (isset($errors['field'. $field['id']])) ? stripslashes($errors['field'. $field['id']]) : false; 
        foreach (array('description' => $field['description'], 'required_label' => $required, 'error' => $error) as $code => $value){
            if (!$value or $value == '')
                $html = preg_replace('/(\[if\s+'.$code.'\])(.*?)(\[\/if\s+'.$code.'\])/mis', '', $html);
            else{
                $html = str_replace('[if '.$code.']', '', $html); 
        	    $html = str_replace('[/if '.$code.']', '', $html);
            }

            $html = str_replace('['.$code.']', $value, $html);
        }        
        
        //replace [required_class]
        $required_class = ($field['required'] == '0') ? '' : ' frm_required_field';            
        $html = str_replace('[required_class]', $required_class, $html);  
        
        //replace [label_position]
        $field['label'] = apply_filters('frm_html_label_position', $field['label'], $field);
        $field['label'] = ($field['label'] and $field['label'] != '') ? $field['label'] : 'top';
        $html = str_replace('[label_position]', (($field['type'] == 'divider' or $field['type'] == 'break') ? $field['label'] : ' frm_primary_label'), $html);
        
        //replace [field_name]
        $html = str_replace('[field_name]', $field['name'], $html);
            
        //replace [error_class] 
        $error_class = isset($errors['field'. $field['id']]) ? ' frm_blank_field' : '';
        $error_class .= ' frm_'. $field['label'] .'_container' ;
        //insert custom CSS classes
        if(!empty($field['classes'])){
            if(!strpos($html, 'frm_form_field '))
                $error_class .= ' frm_form_field';
            $error_class .= ' '. $field['classes'];
        }
        $html = str_replace('[error_class]', $error_class, $html);
        
        //replace [entry_key]
        $entry_key = (isset($_GET) and isset($_GET['entry'])) ? $_GET['entry'] : '';
        $html = str_replace('[entry_key]', $entry_key, $html);
        
        //replace [input]
        preg_match_all("/\[(input|deletelink)\b(.*?)(?:(\/))?\]/s", $html, $shortcodes, PREG_PATTERN_ORDER);

        foreach ($shortcodes[0] as $short_key => $tag){
            $atts = shortcode_parse_atts( $shortcodes[2][$short_key] );

            if(!empty($shortcodes[2][$short_key])){
                $tag = str_replace('[', '',$shortcodes[0][$short_key]);
                $tag = str_replace(']', '', $tag);
                $tags = explode(' ', $tag);
                if(is_array($tags))
                    $tag = $tags[0];
            }else
                $tag = $shortcodes[1][$short_key];
               
            $replace_with = ''; 
            
            if($tag == 'input'){
                if(isset($atts['opt'])) $atts['opt']--;
                $field['input_class'] = isset($atts['class']) ? $atts['class'] : '';
                if(isset($atts['class']))
                    unset($atts['class']);
                $field['shortcodes'] = $atts;
                ob_start();
                include(FRM_VIEWS_PATH.'/frm-fields/input.php');
                $replace_with = ob_get_contents();
                ob_end_clean();
            }else if($tag == 'deletelink' and class_exists('FrmProEntriesController'))
                $replace_with = FrmProEntriesController::entry_delete_link($atts);
            
            $html = str_replace($shortcodes[0][$short_key], $replace_with, $html);
        }
        
        if($form){
            $form = (array)$form;
            
            //replace [form_key]
            $html = str_replace('[form_key]', $form['form_key'], $html);
            
            //replace [form_name]
            $html = str_replace('[form_name]', $form['name'], $html);
        }
        $html .= "\n";
        
        return apply_filters('frm_replace_shortcodes', $html, $field, array('errors' => $errors, 'form' => $form));
    }
    
    function display_recaptcha($field, $error=null){
    	global $frm_settings;
    	
    	if(!function_exists('recaptcha_get_html'))
            require_once(FRM_PATH.'/classes/recaptchalib.php');
        
        $lang = apply_filters('frm_recaptcha_lang', $frm_settings->re_lang, $field);
        
        if(defined('DOING_AJAX')){ 
            global $frm_recaptcha_loaded;
            if(!$frm_recaptcha_loaded)
                $frm_recaptcha_loaded = '';
            
            $frm_recaptcha_loaded .= "Recaptcha.create('". $frm_settings->pubkey ."','field_". $field['field_key'] ."',{theme:'". $frm_settings->re_theme ."',lang:'". $lang ."'". apply_filters('frm_recaptcha_custom', '', $field) ."});";
?>
<div id="field_<?php echo $field['field_key'] ?>"></div>
<?php   }else{ ?>
<script type="text/javascript">var RecaptchaOptions={theme:'<?php echo $frm_settings->re_theme ?>',lang:'<?php echo $lang ?>'<?php echo apply_filters('frm_recaptcha_custom', '', $field) ?>};</script>
<?php       echo recaptcha_get_html($frm_settings->pubkey .'&hl='. $lang, $error, is_ssl());
        }
    }
    
    function dropdown_categories($args){
        global $frmpro_is_installed;
        
        $defaults = array('field' => false, 'name' => false);
        extract(wp_parse_args($args, $defaults));
        
        if(!$field) return;
        if(!$name) $name = "item_meta[$field[id]]";
        $id = 'field_'. $field['field_key'];
        $class = $field['type'];

        $exclude = (is_array($field['exclude_cat'])) ? implode(',', $field['exclude_cat']) : $field['exclude_cat'];
        $exclude = apply_filters('frm_exclude_cats', $exclude, $field);
        
        if(is_array($field['value'])){
            if(!empty($exclude))
                $field['value'] = array_diff($field['value'], explode(',', $exclude));
            $selected = reset($field['value']);
        }else{
            $selected = $field['value'];
        }      
        
        $args = array(
            'show_option_all' => ' ', 'hierarchical' => 1, 'name' => $name,
            'id' => $id, 'exclude' => $exclude, 'class' => $class, 'selected' => $selected, 
            'hide_empty' => false, 'echo' => 0, 'orderby' => 'name'
        );
        
        $args = apply_filters('frm_dropdown_cat', $args, $field);
        
        if(class_exists('FrmProForm')){
            $post_type = FrmProForm::post_type($field['form_id']);
            if(function_exists('get_object_taxonomies')){
                $args['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $field);
                if(!$args['taxonomy'])
                    return;
            }
        }
        
        $dropdown = wp_dropdown_categories($args);

        $add_html = FrmFieldsController::input_html($field, false);
        
        if($frmpro_is_installed)
            $add_html .= FrmProFieldsController::input_html($field, false);
        
        $dropdown = str_replace("<select name='$name' id='$id' class='$class'", "<select name='$name' id='$id' ". $add_html, $dropdown);
        return $dropdown;
    }
    
    function show_onfocus_js($field_id, $clear_on_focus){ ?>
    <a href="javascript:frm_clear_on_focus(<?php echo $field_id; ?>,<?php echo $clear_on_focus; ?>)" class="<?php echo ($clear_on_focus) ?'':'frm_inactive_icon '; ?>frm_default_val_icons frm_action_icon frm_reload_icon" id="clear_field_<?php echo $field_id; ?>" title="<?php printf(__('%1$sclear default value when field is clicked', 'formidable'), ($clear_on_focus) ? '': __('Do not', 'formidable').' ' ); ?>"></a>
    <?php
    }
    
    function show_default_blank_js($field_id, $default_blank){ ?>
    <a href="javascript:frm_default_blank(<?php echo $field_id; ?>,<?php echo $default_blank ?>)" class="<?php echo ($default_blank) ?'':'frm_inactive_icon '; ?>frm_default_val_icons frm_action_icon frm_error_icon" id="default_blank_<?php echo $field_id; ?>" title="<?php printf(__('Default value will %1$spass form validation', 'formidable'), ($default_blank) ? __('not', 'formidable').' ' :'' ); ?>"></a>
    <?php
    }
    
}
