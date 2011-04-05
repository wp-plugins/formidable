<?php

class FrmFieldsHelper{
    
    function field_selection(){
        $fields = apply_filters('frm_available_fields', array(
            'text' => __('Text Input (One Line)', 'formidable'),
            'textarea' => __('Paragraph Input (Multiple Lines)', 'formidable'),
            'checkbox' => __('Multiple Selection (Check Boxes)', 'formidable'),
            'radio' => __('Select One (Radio)', 'formidable'),
            'select' => __('Drop-Down (Select)', 'formidable'),
            'captcha' => __('reCAPTCHA (SPAM Control)', 'formidable')
            //'nucaptcha' => __('NuCaptcha (SPAM Control)', 'formidable')
        ));
        
        return $fields;
    }
    
    function pro_field_selection(){
        return apply_filters('frm_pro_available_fields', array(
            'divider' => __('Section Heading', 'formidable'),
            'break' => __('Page Break', 'formidable'),
            'file' => __('File Upload', 'formidable'),
            'rte' => __('Rich Text', 'formidable'), 
            'number' => __('Number', 'formidable'), 
            'phone' => __('Phone Number', 'formidable'), 
            'email' => __('Email Address', 'formidable'),
            'date' => __('Date', 'formidable'), 
            'time' => __('Time', 'formidable'),
            'url' => __('Website/URL', 'formidable'),
            'image' => __('Image URL', 'formidable'), 
            'scale' => __('Scale', 'formidable'),
            //'grid' => __('Grid', 'formidable'),
            'data' => __('Data from Entries', 'formidable'),
            'hidden' => __('Hidden Field', 'formidable'), 
            'user_id' => __('Hidden User ID', 'formidable'),
            'html' => __('HTML', 'formidable'),
            'tag' => __('Tags', 'formidable')
            //'multiple' => 'Multiple Select Box', //http://code.google.com/p/jquery-asmselect/
            //'address' => 'Address' //Address line 1, Address line 2, City, State/Providence, Postal Code, Select Country 
            //'city_selector' => 'US State/County/City selector', 
            //'full_name' => 'First and Last Name', 
            //'terms' => 'Terms of Use',// checkbox or show terms (integrate with Terms of use plugin)
            //'quiz' => 'Question and Answer' // for captcha alternative
        ));
    }
    
    function setup_new_vars($type='',$form_id=''){
        global $frmdb, $frm_app_helper;
        
        $field_count = $frm_app_helper->getRecordCount("form_id='$form_id'", $frmdb->fields);
        $key = FrmAppHelper::get_unique_key('', $frmdb->fields, 'field_key');
        
        $values = array();
        foreach (array('name' => __('Untitled', 'formidable'), 'description' => '', 'field_key' => $key, 'type' => $type, 'options'=>'', 'default_value'=>'', 'field_order' => $field_count+1, 'required' => false, 'blank' => __('This field cannot be blank', 'formidable'), 'invalid' => __('This field is invalid', 'formidable'), 'form_id' => $form_id) as $var => $default)
            $values[$var] = $default;
        
        $values['field_options'] = array();
        foreach (array('size' => '', 'max' => '', 'label' => 'top', 'required_indicator' => '*', 'clear_on_focus' => 0, 'custom_html' => FrmFieldsHelper::get_default_html($type), 'default_blank' => 0) as $var => $default)
            $values['field_options'][$var] = $default;
            
        if ($type == 'radio' || ($type == 'checkbox'))
            $values['options'] = serialize(array(__('Option 1', 'formidable'), __('Option 2', 'formidable')));
        else if ( $type == 'select')
            $values['options'] = serialize(array('', __('Option 1', 'formidable')));
        else if ($type == 'textarea')
            $values['field_options']['max'] = '5';
        
        return $values;
    }
    
    function setup_edit_vars($record){
        global $frm_entry_meta, $frm_form;
        
        $values = array();
        $values['id'] = $record->id;
        $values['form_id'] = $record->form_id;
        foreach (array('name' => $record->name, 'description' => $record->description) as $var => $default)
              $values[$var] = htmlspecialchars(stripslashes(FrmAppHelper::get_param($var, $default)));

        $values['form_name'] = ($record->form_id)?($frm_form->getName( $record->form_id )):('');
        
        foreach (array('field_key' => $record->field_key, 'type' => $record->type, 'default_value'=> $record->default_value, 'field_order' => $record->field_order, 'required' => $record->required) as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);
        
        $values['options'] = stripslashes_deep(maybe_unserialize($record->options));
        $values['field_options'] = $record->field_options;
        $defaults = array(
            'size' => '', 'max' => '', 'label' => 'top', 'blank' => '', 
            'required_indicator' => '*', 'invalid' => '', 
            'clear_on_focus' => 0, 'default_blank' => 0
        );
        
        foreach($defaults as $opt => $default)
            $values[$opt] = (isset($record->field_options[$opt])) ? $record->field_options[$opt] : $default; 

        $values['custom_html'] = (isset($record->field_options['custom_html'])) ? stripslashes($record->field_options['custom_html']) : FrmFieldsHelper::get_default_html($record->type);
        
        return apply_filters('frm_setup_edit_field_vars', $values, $values['field_options']);
    }
    
    function get_form_fields($form_id, $error=false){ 
        global $frm_field;
        $fields = apply_filters('frm_get_paged_fields', false, $form_id, $error);
        if (!$fields)
            $fields = $frm_field->getAll("fi.form_id='$form_id'", ' ORDER BY field_order');
        return $fields;
    }
    
    function get_default_html($type='text'){
        if (apply_filters('frm_normal_field_type_html', true, $type)){
            $default_html = <<<DEFAULT_HTML
<div id="frm_field_[id]_container" class="form-field [required_class] [error_class]">
    <label class="frm_pos_[label_position]">[field_name]
        <span class="frm_required">[required_label]</span>
    </label>
    [input]
    [if description]<div class="frm_description">[description]</div>[/if description]
    [if error]<div class="frm_error">[error]</div>[/if error]
</div>
DEFAULT_HTML;
        }else
            $default_html = apply_filters('frm_other_custom_html', '', $type);

        return apply_filters('frm_custom_html', $default_html, $type);
    }
    
    function replace_shortcodes($html, $field, $errors=array(), $form=false){
        $field_name = "item_meta[". $field['id'] ."]";
        //replace [id]
        $html = str_replace('[id]', $field['id'], $html);
        
        //replace [key]        
        $html = str_replace('[key]', $field['field_key'], $html);
        
        //replace [description] and [required_label] and [error]
        $required = ($field['required'] == '0')?(''):($field['required_indicator']);
        $error = isset($errors['field'. $field['id']]) ? stripslashes($errors['field'. $field['id']]) : false; 
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
        $required_class = ($field['required'] == '0')?(''):(' form-required');
        $html = str_replace('[required_class]', $required_class, $html);  
        
        //replace [label_position]
        $html = str_replace('[label_position]', $field['label'], $html);
        
        //replace [field_name]
        $html = str_replace('[field_name]', $field['name'], $html);
            
        //replace [error_class] 
        $error_class = isset($errors['field'. $field['id']]) ? ' frm_blank_field':''; 
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
        
        return apply_filters('frm_replace_shortcodes', $html, $field);
    }
    
    function display_recaptcha($field, $error=null){
    	global $frm_settings;
    	
    	if(!function_exists('recaptcha_get_html'))
            require_once(FRM_PATH.'/classes/recaptchalib.php');
        ?>
        <script type="text/javascript">var RecaptchaOptions={theme:'<?php echo $frm_settings->re_theme ?>',lang:'<?php echo $frm_settings->re_lang ?>'};</script>
        <?php echo recaptcha_get_html($frm_settings->pubkey, $error, is_ssl()) ?>
<?php
    }
    
    function dropdown_categories($args){
        $defaults = array('field' => false, 'name' => false);
        extract(wp_parse_args($args, $defaults));
        
        if(!$field) return;
        if(!$name) $name = "item_meta[$field[id]]";
        
        $selected = is_array($field['value']) ? reset($field['value']) : $field['value'];

        $exclude = (is_array($field['exclude_cat'])) ? implode(',', $field['exclude_cat']) : $field['exclude_cat'];
        return wp_dropdown_categories(array('show_option_all' => ' ', 'hierarchical' => 1, 'name' => $name, 'id' => 'field_'. $field['field_key'], 'exclude' => $exclude, 'class' => $field['type'], 'selected' => $selected, 'hide_empty' => false, 'echo' => 0, 'orderby' => 'name'));
    }
    
    function show_onfocus_js($field_id, $clear_on_focus){ 
        global $frm_ajax_url; ?>
    <a href="javascript:frm_clear_on_focus(<?php echo $field_id; ?>,<?php echo $clear_on_focus; ?>,'<?php echo FRM_IMAGES_URL ?>','<?php echo $frm_ajax_url?>')" class="<?php echo ($clear_on_focus) ?'':'frm_inactive_icon '; ?>frm-show-hover" id="clear_field_<?php echo $field_id; ?>" title="<?php printf(__('Set this field to %1$sclear on click', 'formidable'), ($clear_on_focus) ? __('not', 'formidable').' ' :'' ); ?>"><img src="<?php echo FRM_IMAGES_URL?>/reload.png"></a>
    <?php
    }
    
    function show_default_blank_js($field_id, $default_blank){ 
        global $frm_ajax_url; ?>
    <a href="javascript:frm_default_blank(<?php echo $field_id; ?>,<?php echo $default_blank ?>,'<?php echo FRM_IMAGES_URL ?>','<?php echo $frm_ajax_url?>')" class="<?php echo ($default_blank) ?'':'frm_inactive_icon '; ?>frm-show-hover" id="default_blank_<?php echo $field_id; ?>" title="<?php printf(__('This default value should %1$sbe considered blank', 'formidable'), ($default_blank) ? __('not', 'formidable').' ' :'' ); ?>"><img src="<?php echo FRM_IMAGES_URL?>/error.png"></a>
    <?php
    }
    
}

?>