<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmFormsHelper{
    public static function get_direct_link($key, $form = false ) {
        $target_url = esc_url(admin_url('admin-ajax.php') . '?action=frm_forms_preview&form='. $key);
        $target_url = apply_filters('frm_direct_link', $target_url, $key, $form);

        return $target_url;
    }

    public static function forms_dropdown( $field_name, $field_value='', $args = array() ) {
        $defaults = array(
            'blank'     => true,
            'field_id'  => false,
            'onchange'  => false,
            'exclude'   => false,
        );
        $args = wp_parse_args( $args, $defaults );

        if ( ! $args['field_id'] ) {
            $args['field_id'] = $field_name;
        }

        global $wpdb;

        $query = "is_template=0 AND (status is NULL OR status = '' OR status = 'published') AND (parent_form_id IS NULL OR parent_form_id < 1)";
        if ( $args['exclude'] ) {
            $query .= $wpdb->prepare(" AND id != %d", $args['exclude']);
        }

        $where = apply_filters('frm_forms_dropdown', $query, $field_name);
        $frm_form = new FrmForm();
        $forms = $frm_form->getAll($where, 'name');
        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $args['field_id'] ?>" <?php
            if ( $args['onchange'] ) {
                echo 'onchange="'. $args['onchange'] .'"';
            } ?>>
            <?php if ( $args['blank'] ) { ?>
            <option value=""><?php echo ( $args['blank'] == 1 ) ? ' ' : '- '. $args['blank'] .' -'; ?></option>
            <?php } ?>
            <?php foreach($forms as $form){ ?>
                <option value="<?php echo $form->id; ?>" <?php selected($field_value, $form->id); ?>><?php echo '' == $form->name ? __('(no title)', 'formidable') : FrmAppHelper::truncate($form->name, 33); ?></option>
            <?php } ?>
        </select>
        <?php
    }

    public static function form_switcher(){
        $where = apply_filters('frm_forms_dropdown', "(parent_form_id IS NULL OR parent_form_id < 1) AND is_template=0 AND (status is NULL OR status = '' OR status = 'published')", '');

        $frm_form = new FrmForm();
        $forms = $frm_form->getAll($where, 'name');
        unset($frm_form);

        $args = array('id' => 0, 'form' => 0);
        if ( isset($_GET['id']) && ! isset($_GET['form']) ) {
            unset($args['form']);
        } else if(isset($_GET['form']) && ! isset($_GET['id']) ) {
            unset($args['id']);
        }

        if ( FrmAppHelper::is_admin_page('formidable-entries') && isset($_GET['frm_action']) && in_array($_GET['frm_action'], array('edit', 'show', 'destroy_all')) ) {
            $args['frm_action'] = 'list';
            $args['form'] = 0;
        }else if ( FrmAppHelper::is_admin_page('formidable') && isset($_GET['frm_action']) && $_GET['frm_action'] == 'new' ) {
            $args['frm_action'] = 'edit';
        }else if(isset($_GET['post'])){
            $args['form'] = 0;
            $base = admin_url('edit.php?post_type=frm_display');
        }

        ?>
		<li class="dropdown last" id="frm_bs_dropdown">
			<a href="#" id="frm-navbarDrop" class="frm-dropdown-toggle" data-toggle="dropdown"><?php _e('Switch Form', 'formidable') ?> <b class="caret"></b></a>
		    <ul class="frm-dropdown-menu" role="menu" aria-labelledby="frm-navbarDrop">
			<?php foreach($forms as $form){
			    if(isset($args['id']))
			        $args['id'] = $form->id;
			    if(isset($args['form']))
			        $args['form'] = $form->id;
                ?>
				<li><a href="<?php echo isset($base) ? add_query_arg($args, $base) : add_query_arg($args); ?>" tabindex="-1"><?php echo empty($form->name) ? __('(no title)') : FrmAppHelper::truncate($form->name, 33); ?></a></li>
			<?php
			        unset($form);
			    } ?>
			</ul>
		</li>
        <?php
    }

    public static function get_sortable_classes($col, $sort_col, $sort_dir){
        echo ($sort_col == $col) ? 'sorted' : 'sortable';
        echo ($sort_col == $col and $sort_dir == 'desc') ? ' asc' : ' desc';
    }

    public static function setup_new_vars($values=array()){
        global $wpdb, $frmdb;

        if(!empty($values)){
            $post_values = $values;
        }else{
            $values = array();
            $post_values = isset($_POST) ? $_POST : array();
        }

        foreach (array('name' => '', 'description' => '') as $var => $default){
            if(!isset($values[$var]))
                $values[$var] = FrmAppHelper::get_param($var, $default);
        }

        $values['description'] = FrmAppHelper::use_wpautop($values['description']);

        foreach (array('form_id' => '', 'logged_in' => '', 'editable' => '', 'default_template' => 0, 'is_template' => 0, 'status' => 'draft', 'parent_form_id' => 0) as $var => $default){
            if(!isset($values[$var]))
                $values[$var] = FrmAppHelper::get_param($var, $default);
        }

        if(!isset($values['form_key']))
            $values['form_key'] = ($post_values and isset($post_values['form_key'])) ? $post_values['form_key'] : FrmAppHelper::get_unique_key('', $wpdb->prefix .'frm_forms', 'form_key');

        $values = self::fill_default_opts($values, false, $post_values);

        if ( $post_values && isset($post_values['options']['custom_style']) ) {
            $values['custom_style'] = $post_values['options']['custom_style'];
        } else {
            $frm_settings = FrmAppHelper::get_settings();
            $values['custom_style'] = ( $frm_settings->load_style != 'none' );
        }

        $values['before_html'] = FrmFormsHelper::get_default_html('before');
        $values['after_html'] = FrmFormsHelper::get_default_html('after');
        $values['submit_html'] = FrmFormsHelper::get_default_html('submit');

        return apply_filters('frm_setup_new_form_vars', $values);
    }

    public static function setup_edit_vars($values, $record, $post_values=array()){
        if(empty($post_values))
            $post_values = stripslashes_deep($_POST);

        $values['form_key'] = isset($post_values['form_key']) ? $post_values['form_key'] : $record->form_key;
        $values['default_template'] = isset($post_values['default_template']) ? $post_values['default_template'] : $record->default_template;
        $values['is_template'] = isset($post_values['is_template']) ? $post_values['is_template'] : $record->is_template;
        $values['status'] = $record->status;

        $values = self::fill_default_opts($values, $record, $post_values);

        return apply_filters('frm_setup_edit_form_vars', $values);
    }

    public static function fill_default_opts($values, $record, $post_values) {

        $defaults = FrmFormsHelper::get_default_opts();
        foreach ($defaults as $var => $default){
            if ( is_array($default) ) {
                if(!isset($values[$var]))
                    $values[$var] = ($record && isset($record->options[$var])) ? $record->options[$var] : array();

                foreach($default as $k => $v){
                    $values[$var][$k] = ($post_values && isset($post_values[$var][$k])) ? $post_values[$var][$k] : (($record && isset($record->options[$var]) && isset($record->options[$var][$k])) ? $record->options[$var][$k] : $v);

                    if ( is_array($v) ) {
                        foreach ( $v as $k1 => $v1 ) {
                            $values[$var][$k][$k1] = ($post_values && isset($post_values[$var][$k][$k1])) ? $post_values[$var][$k][$k1] : (($record && isset($record->options[$var]) && isset($record->options[$var][$k]) && isset($record->options[$var][$k][$k1])) ? $record->options[$var][$k][$k1] : $v1);
                            unset($k1, $v1);
                        }
                    }

                    unset($k, $v);
                }

            }else{
                $values[$var] = ($post_values && isset($post_values['options'][$var])) ? $post_values['options'][$var] : (($record && isset($record->options[$var])) ? $record->options[$var] : $default);
            }

            unset($var, $default);
        }

        return $values;
    }

    public static function get_default_opts(){
        $frm_settings = FrmAppHelper::get_settings();

        return array(
            'submit_value' => $frm_settings->submit_value, 'success_action' => 'message',
            'success_msg' => $frm_settings->success_msg, 'show_form' => 0, 'akismet' => '',
            'no_save' => 0, 'ajax_load' => 0, 'form_class' => '',
        );
    }

    public static function get_default_html($loc){
        if($loc == 'submit'){
            $sending = __('Sending', 'formidable');
            $draft_link = self::get_draft_link();
            $img = '[frmurl]/images/ajax_loader.gif';
            $default_html = <<<SUBMIT_HTML
<div class="frm_submit">
[if back_button]<input type="button" value="[back_label]" name="frm_prev_page" formnovalidate="formnovalidate" class="frm_prev_page" [back_hook] />[/if back_button]
<input type="submit" value="[button_label]" [button_action] />
<img class="frm_ajax_loading" src="$img" alt="$sending" style="visibility:hidden;" />
$draft_link
</div>
SUBMIT_HTML;
        }else if ($loc == 'before'){
            $default_html = <<<BEFORE_HTML
[if form_name]<h3>[form_name]</h3>[/if form_name]
[if form_description]<div class="frm_description">[form_description]</div>[/if form_description]
BEFORE_HTML;
        }else{
            $default_html = '';
        }

        return $default_html;
    }

    public static function get_draft_link(){
        $link = '[if save_draft]<a class="frm_save_draft" [draft_hook]>[draft_label]</a>[/if save_draft]';
        return $link;
    }

    public static function get_custom_submit($html, $form, $submit, $form_action, $values){
        $button = FrmFormsHelper::replace_shortcodes($html, $form, $submit, $form_action, $values);
        if ( ! strpos($button, '[button_action]') ) {
            return;
        }

        $button_parts = explode('[button_action]', $button);
        echo $button_parts[0];
        //echo ' id="frm_submit_"';

        $classes = apply_filters('frm_submit_button_class', array(), $form);
        if ( ! empty($classes) ) {
            echo ' class="'. implode(' ', $classes) .'"';
        }

        do_action('frm_submit_button_action', $form, $form_action);
        echo $button_parts[1];
    }

    public static function replace_shortcodes($html, $form, $title=false, $description=false, $values=array()){
        foreach (array('form_name' => $title, 'form_description' => $description, 'entry_key' => true) as $code => $show){
            if ( $code == 'form_name' ) {
                $replace_with = $form->name;
            } else if ( $code == 'form_description' ) {
                $replace_with = FrmAppHelper::use_wpautop($form->description);
            } else if ( $code == 'entry_key' && isset($_GET) && isset($_GET['entry']) ) {
                $replace_with = $_GET['entry'];
            } else {
                $replace_with = '';
            }

            if ( FrmAppHelper::is_true($show) && $replace_with != '' ) {
                $html = str_replace('[if '.$code.']', '', $html);
        	    $html = str_replace('[/if '.$code.']', '', $html);
            } else {
                $html = preg_replace('/(\[if\s+'.$code.'\])(.*?)(\[\/if\s+'.$code.'\])/mis', '', $html);
            }
            $html = str_replace('['.$code.']', $replace_with, $html);
        }

        //replace [form_key]
        $html = str_replace('[form_key]', $form->form_key, $html);

        //replace [frmurl]
        $html = str_replace('[frmurl]', FrmAppHelper::plugin_url(), $html);

        if(strpos($html, '[button_label]')){
            $replace_with = apply_filters('frm_submit_button', $title, $form);
            $html = str_replace('[button_label]', $replace_with, $html);
        }

        $html = apply_filters('frm_form_replace_shortcodes', $html, $form, $values);

        if(strpos($html, '[if back_button]'))
            $html = preg_replace('/(\[if\s+back_button\])(.*?)(\[\/if\s+back_button\])/mis', '', $html);

        if(strpos($html, '[if save_draft]'))
            $html = preg_replace('/(\[if\s+save_draft\])(.*?)(\[\/if\s+save_draft\])/mis', '', $html);

        return $html;
    }

    public static function get_form_style_class($form = false) {
        $style = self::get_form_style($form);

        if ( empty($style) ) {
            return;
        }

        $class = ' with_frm_style';

        //If submit button needs to be inline or centered
        if ( is_object($form) ) {
            if ( isset( $form->options['submit_align'] ) && $form->options['submit_align'] ) {
                if ( $form->options['submit_align'] == 'inline' ) {
                    $class .= ' frm_inline_form';
                } else if ( $form->options['submit_align'] == 'center' ) {
                    $class .= ' frm_center_submit';
                }
            }
        } else if ( isset($form['submit_align']) && $form['submit_align'] ) {
            if ( $form['submit_align'] == 'inline' ) {
                $class .= ' frm_inline_form';
            } else if ( $form['submit_align'] == 'center' ) {
                $class .= ' frm_center_submit';
            }
        }
        $class = apply_filters('frm_add_form_style_class', $class, $style);

        return $class;
    }

    public static function get_form_style() {
        if ( empty($form) ) {
            $style = 1;
        } else if ( is_object($form) ) {
            $style = isset($form->options['custom_style']) ? $form->options['custom_style'] : 1;
        } else if ( is_array($form) ) {
            $style = isset($form['custom_style']) ? $form['custom_style'] : 1;
        } else if ( 'default' == 'form' ) {
            $style = 1;
        } else {
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($form);
            $style = ( $form && isset($form->options['custom_style']) ) ? $form->options['custom_style'] : 1;
        }

        return $style;
    }

    public static function form_loaded($form, $this_load, $global_load) {
        global $frm_vars;
        $small_form = new stdClass();
        foreach ( array('id', 'form_key', 'name' ) as $var ) {
            $small_form->{$var} = $form->{$var};
            unset($var);
        }

        $frm_vars['forms_loaded'][] = $small_form;

        if ( $this_load && empty($global_load) ) {
            $global_load = $frm_vars['load_css'] = true;
        }

        if ( ( ! isset($frm_vars['css_loaded']) || ! $frm_vars['css_loaded'] ) && $global_load ) {
            echo FrmAppController::footer_js('header');
            $frm_vars['css_loaded'] = true;
        }
    }

    public static function get_scroll_js($form_id) {
        ?><script type="text/javascript">jQuery(document).ready(function($){frmFrontForm.scrollMsg(<?php echo $form_id ?>);})</script><?php
    }

    public static function edit_form_link($form_id) {
        if ( is_object($form_id) ) {
            $form = $form_id;
            $name = $form->name;
            $form_id = $form->id;
        } else {
            $frm_form = new FrmForm();
            $name = $frm_form->getName($form_id);
        }

        if ( $form_id ) {
            $val = '<a href="'. admin_url('admin.php') .'?page=formidable&frm_action=edit&id='. $form_id .'">'. ( '' == $name ? __('(no title)') : FrmAppHelper::truncate($name, 40) ) .'</a>';
	    } else {
	        $val = '';
	    }

	    return $val;
	}

    public static function delete_trash_link($id, $status, $length = 'long') {
        $link = '';
        $labels = array(
            'restore' => array(
                'long'  => __('Restore from Trash', 'formidable'),
                'short' => __('Restore', 'formidable'),
            ),
            'trash' => array(
                'long'  => __('Move to Trash', 'formidable'),
                'short' => __('Trash', 'formidable'),
            ),
            'delete' => array(
                'long'  => __('Delete Permanently', 'formidable'),
                'short' => __('Delete', 'formidable'),
            ),
        );

        $current_page = isset( $_REQUEST['form_type'] ) ? $_REQUEST['form_type'] : '';
        $base_url = '?page=formidable&form_type='. $current_page .'&id='. $id;
        if ( 'trash' == $status ) {
            $link = '<a class="submitdelete deletion" href="'. esc_url(wp_nonce_url( $base_url .'&frm_action=untrash', 'untrash_form_' . $id )) .'" >'. $labels['restore'][$length] .'</a>';
        } else if ( current_user_can('frm_delete_forms') ) {
            if ( EMPTY_TRASH_DAYS ) {
                $link = '<a class="submitdelete deletion" href="'. wp_nonce_url( $base_url .'&frm_action=trash', 'trash_form_' . $id ) .'">'. $labels['trash'][$length] .'</a>';
            } else {
                $link = '<a class="submitdelete deletion" href="'. wp_nonce_url( $base_url .'&frm_action=destroy', 'destroy_form_' . $id ) .'" onclick="return confirm(\''. __('Are you sure you want to delete this form and all its entries?', 'formidable') .'\')">'. $labels['delete'][$length] .'</a>';
            }
        }

        return $link;
    }

    public static function status_nice_name($status) {
        if ( 'draft' == $status ) {
            $name = __('Draft', 'formidable');
        } else if ( 'trash' == $status ) {
            $name = __('Trash', 'formidable');
        } else {
            $name = __('Published', 'formidable');
        }

        return $name;
    }

}
