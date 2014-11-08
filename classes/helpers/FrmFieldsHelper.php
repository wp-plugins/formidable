<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmFieldsHelper{

    public static function field_selection(){
        $fields = apply_filters('frm_available_fields', array(
            'text'      => __('Single Line Text', 'formidable'),
            'textarea'  => __('Paragraph Text', 'formidable'),
            'checkbox'  => __('Checkboxes', 'formidable'),
            'radio'     => __('Radio Buttons', 'formidable'),
            'select'    => __('Dropdown', 'formidable'),
            'email'     => __('Email Address', 'formidable'),
            'url'       => __('Website/URL', 'formidable'),
            'captcha'   => __('reCAPTCHA', 'formidable'),
        ));

        return $fields;
    }

    public static function pro_field_selection(){
        return apply_filters('frm_pro_available_fields', array(
            'end_divider' => array(
                'name'  => __('End Section', 'formidable'),
                'switch_from' => 'divider',
            ),
            'divider'   => array(
                'name'  => __('Section', 'formidable'),
                'types' => array(
                    ''   => __('Heading', 'formidable'),
                    'slide'  => __('Collapsible', 'formidable'),
                    'repeat' => __('Repeatable', 'formidable'),
                ),
            ),
            'break'     => __('Page Break', 'formidable'),
            'file'      => __('File Upload', 'formidable'),
            'rte'       => __('Rich Text', 'formidable'),
            'number'    => __('Number', 'formidable'),
            'phone'     => __('Phone Number', 'formidable'),
            'date'      => __('Date', 'formidable'),
            'time'      => __('Time', 'formidable'),
            'image'     => __('Image URL', 'formidable'),
            'scale'     => __('Scale', 'formidable'),
            'data'      => array(
                'name'  => __('Dynamic Field', 'formidable'),
                'types' => array(
                    'select'    => __('Dropdown', 'formidable'),
                    'radio'     => __('Radio Buttons', 'formidable'),
                    'checkbox'  => __('Checkboxes', 'formidable'),
                    'data'      => __('List', 'formidable'),
                ),
            ),
            'form'      => __('Embed Form', 'formidable'),
            'hidden'    => __('Hidden Field', 'formidable'),
            'user_id'   => __('User ID (hidden)', 'formidable'),
            'password'  => __('Password', 'formidable'),
            'html'      => __('HTML', 'formidable'),
            'tag'       => __('Tags', 'formidable')
            //'address' => 'Address' //Address line 1, Address line 2, City, State/Providence, Postal Code, Select Country
            //'city_selector' => 'US State/County/City selector',
            //'full_name' => 'First and Last Name',
            //'quiz'    => 'Question and Answer' // for captcha alternative
        ));
    }

    public static function is_no_save_field($type) {
        return in_array($type, self::no_save_fields());
    }

    public static function no_save_fields() {
        return array('divider', 'end_divider', 'captcha', 'break', 'html');
    }

    public static function is_multiple_select($field) {
        return isset($field['multiple']) && $field['multiple'] && ( ( $field['type'] == 'select' || ( $field['type'] == 'data' && isset($field['data_type']) && $field['data_type'] == 'select') ) );
    }

    public static function setup_new_vars($type = '', $form_id = '') {

        if ( strpos($type, '|') ) {
            list($type, $setting) = explode('|', $type);
        }

        $defaults = self::get_default_field_opts($type, $form_id);
        $defaults['field_options']['custom_html'] = FrmFieldsHelper::get_default_html($type);

        $values = array();

        foreach ( $defaults as $var => $default ) {
            if ( $var == 'field_options' ) {
                $values['field_options'] = array();
                foreach ( $default as $opt_var => $opt_default ) {
                    $values['field_options'][$opt_var] = $opt_default;
                    unset($opt_var, $opt_default);
                }

            } else {
                $values[$var] = $default;
            }
            unset($var, $default);
        }

        if ( isset($setting) && !empty($setting) ) {
            if ( 'data' == $type ) {
                $values['field_options']['data_type'] = $setting;
            }else{
                $values['field_options'][$setting] = 1;
            }
        }

        if ( $type == 'radio' || $type == 'checkbox' ) {
            $values['options'] = serialize( array(
                __('Option 1', 'formidable'),
                __('Option 2', 'formidable'),
            ) );
        } else if ( $type == 'select' ) {
            $values['options'] = serialize( array(
                '', __('Option 1', 'formidable'),
            ) );
        } else if ( $type == 'textarea' ) {
            $values['field_options']['max'] = '5';
        } else if ( $type == 'captcha' ) {
            $frm_settings = FrmAppHelper::get_settings();
            $values['invalid'] = $frm_settings->re_msg;
        } else if ( 'url' == $type ) {
            $values['name'] = __('Website', 'formidable');
        }

        $fields = self::field_selection();
        $fields = array_merge($fields, self::pro_field_selection());

        if ( isset($fields[$type]) ) {
            $values['name'] = is_array($fields[$type]) ? $fields[$type]['name'] : $fields[$type];
        }

        unset($fields);

        return $values;
    }

    public static function get_html_id($field, $plus = '') {
        return apply_filters('frm_field_html_id', 'field_'. $field['field_key'] . $plus, $field);
    }

    public static function setup_edit_vars($record, $doing_ajax=false){
        global $frm_entry_meta;

        $values = array('id' => $record->id, 'form_id' => $record->form_id);
        $defaults = array('name' => $record->name, 'description' => $record->description);
        $default_opts = array(
            'field_key' => $record->field_key, 'type' => $record->type,
            'default_value'=> $record->default_value, 'field_order' => $record->field_order,
            'required' => $record->required
        );

        if($doing_ajax){
            $values = $values + $defaults + $default_opts;
            $values['form_name'] = '';
        }else{
            foreach ($defaults as $var => $default){
                $values[$var] = htmlspecialchars(FrmAppHelper::get_param($var, $default));
                unset($var);
                unset($default);
            }

            foreach (array('field_key' => $record->field_key, 'type' => $record->type, 'default_value'=> $record->default_value, 'field_order' => $record->field_order, 'required' => $record->required) as $var => $default){
                $values[$var] = FrmAppHelper::get_param($var, $default);
                unset($var);
                unset($default);
            }

            $frm_form = new FrmForm();
            $values['form_name'] = ($record->form_id) ? $frm_form->getName( $record->form_id ) : '';
            unset($frm_form);
        }

        unset($defaults, $default_opts);

        $values['options'] = $record->options;
        $values['field_options'] = $record->field_options;

        $defaults = self::get_default_field_opts($values['type'], $record, true);

        if($values['type'] == 'captcha'){
            $frm_settings = FrmAppHelper::get_settings();
            $defaults['invalid'] = $frm_settings->re_msg;
        }

        foreach($defaults as $opt => $default){
            $values[$opt] = (isset($record->field_options[$opt])) ? $record->field_options[$opt] : $default;
            unset($opt, $default);
        }

        $values['custom_html'] = (isset($record->field_options['custom_html'])) ? $record->field_options['custom_html'] : self::get_default_html($record->type);

        return apply_filters('frm_setup_edit_field_vars', $values, array('doing_ajax' => $doing_ajax));
    }

    public static function get_default_field_opts($type, $field, $limit=false){
        $field_options = array(
            'size' => '', 'max' => '', 'label' => '', 'blank' => '',
            'required_indicator' => '*', 'invalid' => '', 'separate_value' => 0,
            'clear_on_focus' => 0, 'default_blank' => 0, 'classes' => '',
            'custom_html' => ''
        );

        if($limit)
            return $field_options;

        global $wpdb;

        $form_id = (is_numeric($field)) ? $field : $field->form_id;

        $key = is_numeric($field) ? FrmAppHelper::get_unique_key('', $wpdb->prefix .'frm_fields', 'field_key') : $field->field_key;

        $query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}frm_fields fi LEFT JOIN {$wpdb->prefix}frm_forms fr ON (fi.form_id = fr.id) WHERE fr.id = %d OR fr.parent_form_id = %d", $form_id, $form_id);
        $field_count = $wpdb->get_var($query);

        $frm_settings = FrmAppHelper::get_settings();
        return array(
            'name' => __('Untitled', 'formidable'), 'description' => '',
            'field_key' => $key, 'type' => $type, 'options'=>'', 'default_value'=>'',
            'field_order' => $field_count+1, 'required' => false,
            'blank' => $frm_settings->blank_msg, 'unique_msg' => $frm_settings->unique_msg,
            'invalid' => __('This field is invalid', 'formidable'), 'form_id' => $form_id,
            'field_options' => $field_options
        );
    }

    /*
    * @since 2.0
    */
    public static function get_error_msg($field, $error){
        $frm_settings = FrmAppHelper::get_settings();
        $default_settings = $frm_settings->default_options();

        $defaults = array(
            'unique_msg' => array('full' => $default_settings['unique_msg'], 'part' => $field->name.' '. __('must be unique', 'formidable')),
            'invalid'   => array('full' => __('This field is invalid', 'formidable'), 'part' => $field->name.' '. __('is invalid', 'formidable'))
        );

        $msg = ($field->field_options[$error] == $defaults[$error]['full'] || empty($field->field_options[$error])) ? ($defaults[$error]['part']) : $field->field_options[$error];
        return $msg;
    }

    public static function get_form_fields($form_id, $error=false){
        global $frm_field;
        $fields = $frm_field->get_all_for_form($form_id);
        $fields = apply_filters('frm_get_paged_fields', $fields, $form_id, $error);
        return $fields;
    }

    public static function get_default_html($type='text'){
        if (apply_filters('frm_normal_field_type_html', true, $type)){
            $input = (in_array($type, array('radio', 'checkbox', 'data'))) ? '<div class="frm_opt_container">[input]</div>' : '[input]';
            $for = '';
            if(!in_array($type, array('radio', 'checkbox', 'data', 'scale')))
                $for = 'for="field_[key]"';

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

    public static function replace_shortcodes($html, $field, $errors = array(), $form = false, $args = array()) {
        $html = apply_filters('frm_before_replace_shortcodes', $html, $field, $errors, $form);

        $defaults = array(
            'field_name'  => 'item_meta['. $field['id'] .']',
            'field_id'    => $field['id'],
            'field_plus_id' => '',
        );
        $args = wp_parse_args($args, $defaults);
        $field_name = $args['field_name'];
        $field_id = $args['field_id'];
        $html_id = self::get_html_id($field, $args['field_plus_id']);

        if ( self::is_multiple_select($field) ) {
            $field_name .= '[]';
        }

        //replace [id]
        $html = str_replace('[id]', $field_id, $html);

        //replace [key]
        $html = str_replace('[key]', $field['field_key'], $html);

        //replace [description] and [required_label] and [error]
        $required = ($field['required'] == '0') ? '' : $field['required_indicator'];
        if(!is_array($errors))
            $errors = array();
        $error = isset($errors['field'. $field_id]) ? $errors['field'. $field_id] : false;

        //If field type is section heading, add class so a bottom margin can be added to either the h3 or description
        if ( $field['type'] == 'divider' ) {
            if ( isset( $field['description'] ) && $field['description'] ) {
                $html = str_replace( 'frm_description', 'frm_description frm_section_spacing', $html );
            } else {
                $html = str_replace('[label_position]', '[label_position] frm_section_spacing', $html);
            }
        }

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
        $field['label'] = apply_filters('frm_html_label_position', $field['label'], $field, $form);
        $field['label'] = ($field['label'] and $field['label'] != '') ? $field['label'] : 'top';
        $html = str_replace('[label_position]', ( ( in_array( $field['type'], array('divider', 'end_divider', 'break') ) ) ? $field['label'] : ' frm_primary_label'), $html);

        //replace [field_name]
        $html = str_replace('[field_name]', $field['name'], $html);

        //replace [error_class]
        $error_class = isset($errors['field'. $field_id]) ? ' frm_blank_field' : '';
        $error_class .= ' frm_'. $field['label'] .'_container' ;

        //Add frm_first_half if inline confirmation field
        if ( isset($field['conf_field']) && $field['conf_field'] == 'inline') {
            $error_class .= ' frm_first_half';
        }

        //Add class if field includes other option
        if ( isset( $field['other'] ) && true == $field['other'] ) {
            $error_class .= ' frm_other_container';
        }

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
        global $frm_vars;
        $frm_settings = FrmAppHelper::get_settings();

        foreach ( $shortcodes[0] as $short_key => $tag ) {
            $atts = shortcode_parse_atts( $shortcodes[2][$short_key] );
            $tag = self::get_shortcode_tag($shortcodes, $short_key, array('conditional' => false, 'conditional_check' => false));

            $replace_with = '';

            if ( $tag == 'input' ) {
                if ( isset($atts['opt']) ) {
                    $atts['opt']--;
                }

                $field['input_class'] = isset($atts['class']) ? $atts['class'] : '';
                if ( isset($atts['class']) ) {
                    unset($atts['class']);
                }

                $field['shortcodes'] = $atts;
                ob_start();
                include(FrmAppHelper::plugin_path() .'/classes/views/frm-fields/input.php');
                $replace_with = ob_get_contents();
                ob_end_clean();
            } else if ( $tag == 'deletelink' && class_exists('FrmProEntriesController') ) {
                $replace_with = FrmProEntriesController::entry_delete_link($atts);
            }

            $html = str_replace($shortcodes[0][$short_key], $replace_with, $html);
        }

        if($form){
            $form = (array) $form;

            //replace [form_key]
            $html = str_replace('[form_key]', $form['form_key'], $html);

            //replace [form_name]
            $html = str_replace('[form_name]', $form['name'], $html);
        }
        $html .= "\n";

        //Return html if conf_field to prevent loop
        if ( isset($field['conf_field']) && $field['conf_field'] == 'stop' ) {
            return $html;
        }

        $html = apply_filters('frm_replace_shortcodes', $html, $field, array('errors' => $errors, 'form' => $form));

        // remove [collapse_this] when running the free version
        if (preg_match('/\[(collapse_this)\]/s', $html))
            $html = str_replace('[collapse_this]', '', $html);

        return $html;
    }

    public static function get_shortcode_tag($shortcodes, $short_key, $args) {
        $args = wp_parse_args($args, array('conditional' => false, 'conditional_check' => false, 'foreach' => false));
        if ( ( $args['conditional'] || $args['foreach'] ) && ! $args['conditional_check'] ) {
            $args['conditional_check'] = true;
        }

        $prefix = '';
        if ( $args['conditional_check'] ) {
            if ( $args['conditional'] ) {
                $prefix = 'if ';
            } else if ( $args['foreach'] ) {
                $prefix = 'foreach ';
            }
        }

        $with_tags = $args['conditional_check'] ? 3 : 2;
        if ( ! empty($shortcodes[$with_tags][$short_key]) ) {
            $tag = str_replace( '[' . $prefix, '', $shortcodes[0][$short_key]);
            $tag = str_replace(']', '', $tag);
            $tags = explode(' ', $tag);
            if ( is_array($tags) ) {
                $tag = $tags[0];
            }
        } else {
            $tag = $shortcodes[$with_tags - 1][$short_key];
        }

        return $tag;
    }

    public static function display_recaptcha($field, $error=null){
    	global $frm_vars;

    	if ( ! function_exists('recaptcha_get_html') ) {
            require(FrmAppHelper::plugin_path().'/classes/recaptchalib.php');
        }

        $frm_settings = FrmAppHelper::get_settings();

        $lang = apply_filters('frm_recaptcha_lang', $frm_settings->re_lang, $field);

        if ( FrmAppHelper::doing_ajax() ) {
            if ( ! isset($frm_vars['recaptcha_loaded']) || ! $frm_vars['recaptcha_loaded'] ) {
                $frm_vars['recaptcha_loaded'] = '';
            }

            $frm_vars['recaptcha_loaded'] .= "Recaptcha.create('". $frm_settings->pubkey ."','field_". $field['field_key'] ."',{theme:'". $frm_settings->re_theme ."',lang:'". $lang ."'". apply_filters('frm_recaptcha_custom', '', $field) ."});";
?>
<div id="field_<?php echo $field['field_key'] ?>"></div>
<?php   }else{ ?>
<script type="text/javascript">var RecaptchaOptions={theme:'<?php echo $frm_settings->re_theme ?>',lang:'<?php echo $lang ?>'<?php echo apply_filters('frm_recaptcha_custom', '', $field) ?>};</script>
<?php       echo recaptcha_get_html($frm_settings->pubkey .'&hl='. $lang, $error, is_ssl());
        }
    }

    public static function show_single_option($field) {
        $field_name = $field['name'];
        $html_id = FrmFieldsHelper::get_html_id($field);
        foreach ( $field['options'] as $opt_key => $opt ) {
            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
            require(FrmAppHelper::plugin_path() .'/classes/views/frm-fields/single-option.php');
        }
    }

    public static function dropdown_categories($args){

        $defaults = array('field' => false, 'name' => false, 'show_option_all' => ' ');
        $args = wp_parse_args($args, $defaults);

        if ( ! $args['field'] ) {
            return;
        }

        if ( ! $args['name'] ) {
            $args['name'] = 'item_meta['. $args['field']['id'] .']';
        }

        $id = self::get_html_id($args['field']);
        $class = $args['field']['type'];

        $exclude = (is_array($args['field']['exclude_cat'])) ? implode(',', $args['field']['exclude_cat']) : $args['field']['exclude_cat'];
        $exclude = apply_filters('frm_exclude_cats', $exclude, $args['field']);

        if ( is_array($args['field']['value']) ) {
            if ( ! empty($exclude) ) {
                $args['field']['value'] = array_diff($args['field']['value'], explode(',', $exclude));
            }
            $selected = reset($args['field']['value']);
        }else{
            $selected = $args['field']['value'];
        }

        $tax_atts = array(
            'show_option_all' => $args['show_option_all'], 'hierarchical' => 1, 'name' => $args['name'],
            'id' => $id, 'exclude' => $exclude, 'class' => $class, 'selected' => $selected,
            'hide_empty' => false, 'echo' => 0, 'orderby' => 'name',
        );

        $tax_atts = apply_filters('frm_dropdown_cat', $tax_atts, $args['field']);

        if ( class_exists('FrmProFormsHelper') ) {
            $post_type = FrmProFormsHelper::post_type($args['field']['form_id']);
            $tax_atts['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $args['field']);
            if ( ! $tax_atts['taxonomy'] ) {
                return;
            }

            if ( is_taxonomy_hierarchical($tax_atts['taxonomy']) ) {
                $tax_atts['exclude_tree'] = $exclude;
            }
        }

        $dropdown = wp_dropdown_categories($tax_atts);

        $add_html = FrmFieldsController::input_html($args['field'], false);

        if ( FrmAppHelper::pro_is_installed() ) {
            $add_html .= FrmProFieldsController::input_html($args['field'], false);
        }

        $dropdown = str_replace("<select name='". $args['name'] ."' id='$id' class='$class'", "<select name='". $args['name'] ."' id='$id' ". $add_html, $dropdown);

        if ( is_array($args['field']['value']) ) {
            $skip = true;
            foreach ( $args['field']['value'] as $v ) {
                if($skip){
                    $skip = false;
                    continue;
                }
                $dropdown = str_replace(' value="'. $v. '"', ' value="'. $v .'" selected="selected"', $dropdown);
                unset($v);
            }
        }

        return $dropdown;
    }

    public static function get_term_link($tax_id) {
        $tax = get_taxonomy($tax_id);
        if ( !$tax ) {
            return;
        }

        $link = sprintf(
            __('Please add options from the WordPress "%1$s" page', 'formidable'),
            '<a href="'. esc_url(admin_url('edit-tags.php?taxonomy='. $tax->name)) .'" target="_blank">'. ( empty($tax->labels->name) ? __('Categories') : $tax->labels->name ) .'</a>'
        );
        unset($tax);

        return $link;
    }

    public static function value_meets_condition($observed_value, $cond, $hide_opt) {
        if ( is_array($observed_value) ) {
            return self::array_value_condition($observed_value, $cond, $hide_opt);
        }

        $m = false;
        if ( $cond == '==' ) {
            $m = $observed_value == $hide_opt;
        } else if ( $cond == '!=' ) {
            $m = $observed_value != $hide_opt;
        } else if ( $cond == '>' ) {
            $m = $observed_value > $hide_opt;
        } else if ( $cond == '<' ) {
            $m = $observed_value < $hide_opt;
        } else if ( $cond == 'LIKE' || $cond == 'not LIKE' ) {
            $m = strpos($observed_value, $hide_opt);
            if ( $cond == 'not LIKE' ) {
                $m = ( $m === false ) ? true : false;
            } else {
                $m = ( $m === false ) ? false : true;
            }
        }
        return $m;
    }

    public static function array_value_condition($observed_value, $cond, $hide_opt) {
        $m = false;
        if ( $cond == '==' ) {
            if ( is_array($hide_opt) ) {
                $m = array_intersect($hide_opt, $observed_value);
                $m = empty($m) ? false : true;
            } else {
                $m = in_array($hide_opt, $observed_value);
            }
        } else if ( $cond == '!=' ) {
            $m = ! in_array($hide_opt, $observed_value);
        } else if ( $cond == '>' ) {
            $min = min($observed_value);
            $m = $min > $hide_opt;
        } else if ( $cond == '<' ) {
            $max = max($observed_value);
            $m = $max < $hide_opt;
        } else if ( $cond == 'LIKE' || $cond == 'not LIKE' ) {
            foreach ( $observed_value as $ob ) {
                $m = strpos($ob, $hide_opt);
                if ( $m !== false ) {
                    $m = true;
                    break;
                }
            }

            if ( $cond == 'not LIKE' ) {
                $m = ( $m === false ) ? true : false;
            }
        }

        return $m;
    }

    /*
    * Replace a few basic shortcodes and field ids
    * @since 2.0
    * @returns string
    */
    public static function basic_replace_shortcodes($value, $form, $entry) {
        if ( strpos($value, '[sitename]') !== false ) {
            $new_value = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            $value = str_replace('[sitename]', $new_value, $value);
        }

        $value = apply_filters('frm_content', $value, $form, $entry);
        $value = do_shortcode($value);

        return $value;
    }

    public static function get_shortcodes($content, $form_id){
        $frm_field = new FrmField();
        $fields = $frm_field->getAll("fi.type not in ('". implode("','", self::no_save_fields() ) ."') and fi.form_id=". (int) $form_id);

        $tagregexp = 'editlink|admin_email|siteurl|sitename|id|key|post[-|_]id|ip|created[-|_]at|updated[-|_]at|updated[-|_]by';
        foreach ( $fields as $field ) {
            $tagregexp .= '|'. $field->id . '|'. $field->field_key;
        }

        preg_match_all("/\[(if )?($tagregexp)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $content, $matches, PREG_PATTERN_ORDER);

        return $matches;
    }

    public static function replace_content_shortcodes($content, $entry, $shortcodes) {

        foreach ( $shortcodes[0] as $short_key => $tag ) {
            $atts = shortcode_parse_atts( $shortcodes[3][$short_key] );

            if ( ! empty($shortcodes[3][$short_key]) ) {
                $tag = str_replace( array('[', ']'), '', $shortcodes[0][$short_key]);
                $tags = explode(' ', $tag);
                if ( is_array($tags) ) {
                    $tag = $tags[0];
                }
            } else {
                $tag = $shortcodes[2][$short_key];
            }

            switch ( $tag ) {
                case 'id':
                    $content = str_replace($shortcodes[0][$short_key], $entry->id, $content);
                break;

                case 'key':
                    $content = str_replace($shortcodes[0][$short_key], $entry->item_key, $content);
                break;

                case 'ip':
                    $content = str_replace($shortcodes[0][$short_key], $entry->ip, $content);
                break;

                case 'user_agent':
                case 'user-agent':
                    $entry->description = maybe_unserialize($entry->description);
                    $content = str_replace($shortcodes[0][$short_key], $entry->description['browser'], $content);
                break;

                case 'created_at':
                case 'created-at':
                case 'updated_at':
                case 'updated-at':
                    if ( isset($atts['format']) ) {
                        $time_format = ' ';
                    } else {
                        $atts['format'] = get_option('date_format');
                        $time_format = false;
                    }

                    $this_tag = str_replace('-', '_', $tag);
                    $date = FrmAppHelper::get_formatted_time($entry->{$this_tag}, $atts['format'], $time_format);
                    unset($this_tag);

                    $content = str_replace($shortcodes[0][$short_key], $date, $content);
                break;

                case 'created_by':
                case 'created-by':
                case 'updated_by':
                case 'updated-by':
                    $this_tag = str_replace('-', '_', $tag);
                    $replace_with = self::get_display_value($entry->{$this_tag}, (object)array('type' => 'user_id'), $atts);

                    $content = str_replace($shortcodes[0][$short_key], $replace_with, $content);

                    unset($this_tag, $replace_with);
                break;

                case 'admin_email':
                    $content = str_replace($shortcodes[0][$short_key], get_option('admin_email'), $content);
                break;

                case 'siteurl':
                    $content = str_replace($shortcodes[0][$short_key], FrmAppHelper::site_url(), $content);
                break;

                case 'frmurl':
                    $content = str_replace($shortcodes[0][$short_key], FrmAppHelper::plugin_url(), $content);
                break;

                case 'sitename':
                    $content = str_replace($shortcodes[0][$short_key], get_option('blogname'), $content);
                break;

                case 'get':
                    if(isset($atts['param'])){
                        $param = $atts['param'];
                        $replace_with = FrmAppHelper::get_param($param);
                        if(is_array($replace_with))
                            $replace_with = implode(', ', $replace_with);

                        $content = str_replace($shortcodes[0][$short_key], $replace_with, $content);
                        unset($param);
                        unset($replace_with);
                    }
                break;

                default:
                    $frm_field = new FrmField();
                    $field = $frm_field->getOne( $tag );
                    if ( ! $field ) {
                        break;
                    }

                    $sep = isset($atts['sep']) ? $atts['sep'] : ', ';

                    $frm_entry_meta = new FrmEntryMeta();
                    $replace_with = $frm_entry_meta->get_entry_meta_by_field($entry->id, $field->id);

                    $atts['entry_id'] = $entry->id;
                    $atts['entry_key'] = $entry->item_key;
                    //$replace_with = apply_filters('frmpro_fields_replace_shortcodes', $replace_with, $tag, $atts, $field);


                    if ( is_array($replace_with) ) {
                        $replace_with = implode($sep, $replace_with);
                    }

                    if ( isset($atts['show']) && $atts['show'] == 'field_label' ) {
                        $replace_with = $field->name;
                    } else if ( isset($atts['show']) && $atts['show'] == 'description' ) {
                        $replace_with = $field->description;
                    } else if ( empty($replace_with) && $replace_with != '0' ) {
                        $replace_with = '';
                    } else {
                        $replace_with = self::get_display_value($replace_with, $field, $atts);
                    }

                    $content = str_replace($shortcodes[0][$short_key], $replace_with, $content);

                    unset($field, $replace_with);
                break;
            }

            unset($atts, $conditional);
         }

         return $content;
    }

    public static function get_display_value($replace_with, $field, $atts = array()) {
        $sep = (isset($atts['sep'])) ? $atts['sep'] : ', ';

        $replace_with = apply_filters('frm_get_display_value', $replace_with, $field, $atts);

        if ( $field->type == 'textarea' || $field->type == 'rte' ) {
            $autop = isset($atts['wpautop']) ? $atts['wpautop'] : true;
            if ( apply_filters('frm_use_wpautop', $autop) ) {
                if ( is_array($replace_with) ) {
                    $replace_with = implode("\n", $replace_with);
                }
                $replace_with = wpautop($replace_with);
            }
             unset($autop);
         } else if ( is_array($replace_with) ) {
             $replace_with = implode($sep, $replace_with);
         }

         return $replace_with;
     }

    public static function get_field_types($type){
        $frm_field_selection = FrmFieldsHelper::field_selection();
        $field_types = array();

        $single_input = array(
            'text', 'textarea', 'rte', 'number', 'email', 'url',
            'image', 'file', 'date', 'phone', 'hidden', 'time',
            'user_id', 'tag', 'password'
        );
        $multiple_input = array('radio', 'checkbox', 'select', 'scale');
        $other_type = array('divider', 'html', 'break');

        $pro_field_selection = FrmFieldsHelper::pro_field_selection();

        if ( in_array($type, $single_input) ) {
            foreach ( $single_input as $input ) {
                $field_types[$input] = ( isset($pro_field_selection[$input]) ) ? $pro_field_selection[$input] : $frm_field_selection[$input];
                unset($input);
            }
        } else if ( in_array($type, $multiple_input) ) {
            foreach ( $multiple_input as $input ) {
                $field_types[$input] = ( isset($pro_field_selection[$input]) ) ? $pro_field_selection[$input] : $frm_field_selection[$input];
                unset($input);
            }
        } else if ( in_array($type, $other_type) ) {
            foreach ( $other_type as $input ) {
                $field_types[$input] = ( isset($pro_field_selection[$input]) ) ? $pro_field_selection[$input] : $frm_field_selection[$input];
                unset($input);
            }
        } else {
            $field_types[$type] = ( isset($pro_field_selection[$type]) ) ? $pro_field_selection[$type] : $frm_field_selection[$type];
        }

        return $field_types;
    }

    public static function show_onfocus_js($clear_on_focus){ ?>
    <a class="frm_bstooltip <?php echo ($clear_on_focus) ? '' : 'frm_inactive_icon '; ?>frm_default_val_icons frm_action_icon frm_reload_icon frm_icon_font" title="<?php echo esc_attr($clear_on_focus ? __('Clear default value when typing', 'formidable') : __('Do not clear default value when typing', 'formidable')); ?>"></a>
    <?php
    }

    public static function show_default_blank_js($default_blank){ ?>
    <a class="frm_bstooltip <?php echo ($default_blank) ? '' :'frm_inactive_icon '; ?>frm_default_val_icons frm_action_icon frm_error_icon frm_icon_font" title="<?php echo $default_blank ? __('Default value will NOT pass form validation', 'formidable') : __('Default value will pass form validation', 'formidable'); ?>"></a>
    <?php
    }

    public static function switch_field_ids($val){
        global $frm_duplicate_ids;
        $replace = array();
        $replace_with = array();
        foreach ( (array) $frm_duplicate_ids as $old => $new ) {
            $replace[] = '[if '. $old .']';
            $replace_with[] = '[if '. $new .']';
            $replace[] = '[if '. $old .' ';
            $replace_with[] = '[if '. $new .' ';
            $replace[] = '[/if '. $old .']';
            $replace_with[] = '[/if '. $new .']';
            $replace[] = '['. $old .']';
            $replace_with[] = '['. $new .']';
            $replace[] = '['. $old .' ';
            $replace_with[] = '['. $new .' ';
            unset($old);
            unset($new);
        }
        if(is_array($val)){
            foreach($val as $k => $v){
                $val[$k] = str_replace($replace, $replace_with, $v);
                unset($k);
                unset($v);
            }
        }else{
            $val = str_replace($replace, $replace_with, $val);
        }

        return $val;
    }
}
