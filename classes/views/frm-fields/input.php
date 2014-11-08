<?php if ( in_array($field['type'], array('email', 'url', 'text')) ) { ?>
<input type="<?php echo ( $frm_settings->use_html || $field['type'] == 'password' ) ? $field['type'] : 'text'; ?>" id="<?php echo $html_id ?>" name="<?php echo $field_name ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php }else if ($field['type'] == 'textarea'){ ?>
<textarea name="<?php echo $field_name ?>" id="<?php echo $html_id ?>" <?php
if ( $field['max'] ) {
    echo 'rows="'. $field['max'] .'" ';
}
do_action('frm_field_input_html', $field);
?>><?php echo FrmAppHelper::esc_textarea($field['value']) ?></textarea>
<?php

} else if ( $field['type'] == 'radio' ) {
    if ( isset($field['read_only']) && $field['read_only'] && ( ! isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') && ! FrmAppHelper::is_admin() ) {
    ?>
<input type="hidden" value="<?php echo esc_attr($field['value']) ?>" name="<?php echo $field_name ?>" />
<?php
    }

    if ( isset($field['post_field']) && $field['post_field'] == 'post_category' ) {
        do_action('frm_after_checkbox', array('field' => $field, 'field_name' => $field_name, 'type' => $field['type']));
    } else if ( is_array($field['options']) ) {
        foreach ( $field['options'] as $opt_key => $opt ) {
            if ( isset($atts) && isset($atts['opt']) && ($atts['opt'] != $opt_key)) {
                continue;
            }

            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
            ?>
<div class="<?php echo apply_filters('frm_radio_class', 'frm_radio', $field, $field_val)?>"><?php

            if ( !isset($atts) || !isset($atts['label']) || $atts['label'] ) {
?><label for="<?php echo $html_id ?>-<?php echo $opt_key ?>"><?php
            }

            $checked = FrmAppHelper::check_selected($field['value'], $field_val) ? 'checked="checked" ' : ' ';

            //Check if this is an "Other" option
            if ( $opt_key && strpos( $opt_key, 'other' ) !== false ) {
                $other_opt = true;

                //Set field value to blank for radio buttons so this value is not saved
                $field_val = '';

                //Check if field value equals any of the options. If it does not match any options, put it in the other text field.
                $other_val = FrmAppHelper::check_other_selected( $field['value'], $field['options'], $field['type'] );
                if ( $other_val ) {
                    $checked = 'checked="checked" ';
                }

                //Set up name for other field
                $other_name = preg_replace('/\[' . $field['id'] . '\]$/', '', $field_name);
                $other_name = $other_name . '[other]' . '[' . $field['id'] . ']';
                //Converts item_meta[field_id] => item_meta[other][field_id] and
                    //item_meta[parent][0][field_id] => item_meta[parent][0][other][field_id]
                    //What if section number and field ID are same
            } else {
                $other_opt = false;
            }?>

            <input type="radio" name="<?php echo $field_name ?>" id="<?php echo $html_id ?>-<?php echo $opt_key ?>" value="<?php echo esc_attr($field_val) ?>" <?php
            echo $checked;
            do_action('frm_field_input_html', $field);
?>/><?php

            if ( !isset($atts) || !isset($atts['label']) || $atts['label'] ) {
                echo ' '. $opt .'</label>';
            }

            if ( $other_opt ) { ?>
                <input type="text" class="frm_other_input <?php echo ( $checked != ' ' ? '' : ' frm_pos_none' ); ?>" name="<?php echo $other_name ?>" value="<?php echo esc_attr($other_val); ?>"><?php
            }
            unset($other_opt, $other_val, $other_name);
?></div>
<?php
        }
    }

}else if ($field['type'] == 'select'){
    if ( isset($field['post_field']) && $field['post_field'] == 'post_category' ) {
        echo FrmFieldsHelper::dropdown_categories(array('name' => $field_name, 'field' => $field) );
    }else{
        if ( isset($field['read_only']) && $field['read_only'] && (!isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') && ! FrmAppHelper::is_admin() ) { ?>
<input type="hidden" value="<?php echo esc_attr($field['value']) ?>" name="<?php echo $field_name ?>" id="<?php echo $html_id ?>" />
<select disabled="disabled" <?php do_action('frm_field_input_html', $field) ?>>
<?php   }else{ ?>
<select name="<?php echo $field_name ?>" id="<?php echo $html_id ?>" <?php do_action('frm_field_input_html', $field) ?>>
<?php   }
    foreach ($field['options'] as $opt_key => $opt){
        $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
        $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field); ?>
<option value="<?php echo esc_attr($field_val) ?>" <?php
if (FrmAppHelper::check_selected($field['value'], $field_val)) echo ' selected="selected"'; ?>><?php echo ($opt == '') ? ' ' : $opt; ?></option>
    <?php } ?>
</select>
<?php }

}else if ($field['type'] == 'checkbox'){
    $checked_values = $field['value'];

    if ( isset($field['read_only']) && $field['read_only'] && ( ! isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') && ! FrmAppHelper::is_admin() ) {
        if ( $checked_values ) {
            foreach ( (array) $checked_values as $checked_value ) { ?>
<input type="hidden" value="<?php echo esc_attr($checked_value) ?>" name="<?php echo $field_name ?>[]" />
<?php
            }
        } else { ?>
<input type="hidden" value="<?php echo esc_attr($checked_values) ?>" name="<?php echo $field_name ?>[]" />
<?php
        }
    }

    if ( isset($field['post_field']) && $field['post_field'] == 'post_category' ) {
        do_action('frm_after_checkbox', array('field' => $field, 'field_name' => $field_name, 'type' => $field['type']));
    } else if ( $field['options'] ) {
        foreach ( $field['options'] as $opt_key => $opt ) {
            if ( isset($atts) && isset($atts['opt']) && ($atts['opt'] != $opt_key) ) {
                continue;
            }

            $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
            $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
            $checked = FrmAppHelper::check_selected($checked_values, $field_val) ? ' checked="checked"' : '';

            ?>
<div class="<?php echo apply_filters('frm_checkbox_class', 'frm_checkbox', $field, $field_val) ?>" id="frm_checkbox_<?php echo $field['id']?>-<?php echo $opt_key ?>"><?php

            if ( !isset($atts) || !isset($atts['label']) || $atts['label'] ) {
                ?><label for="<?php echo $html_id ?>-<?php echo $opt_key ?>"><?php
            }

            //Check if this is an "Other" option
            if ( $opt_key && strpos( $opt_key, 'other' ) !== false ) {
                $other_opt = true;

                //Check if current Other option has a value
                $other_val = FrmAppHelper::check_other_selected( $field['value'], $field['options'], $field['type'], $opt_key );
                if ( $other_val ) {
                   $checked = ' checked="checked"';
                }

                //Set up name for other field
                $other_name = preg_replace('/\[' . $field['id'] . '\]$/', '', $field_name);
                $other_name = $other_name . '[other]' . '[' . $field['id'] . '][' . $opt_key . ']';
                //Converts item_meta[field_id][] => item_meta[other][field_id][opt_key] and
                //item_meta[parent][0][field_id][] => item_meta[parent][0][other][field_id][opt_key]
            } else {
                $other_opt = false;
            }

            ?><input type="checkbox" name="<?php echo $field_name ?>[<?php echo ( $other_opt ? $opt_key : '' ) ?>]" id="<?php echo $html_id ?>-<?php echo $opt_key ?>" value="<?php echo esc_attr($field_val) ?>" <?php echo $checked ?> <?php do_action('frm_field_input_html', $field) ?> /><?php

            if ( !isset($atts) || !isset($atts['label']) || $atts['label'] ) {
                echo ' '. $opt .'</label>';
            }

            if ( $other_opt ) {?>
                <input type="text" class="frm_other_input <?php echo ( $checked ? '' : 'frm_pos_none' ); ?>" name="<?php echo $other_name ?>" value="<?php echo esc_attr($other_val);?>"><?php
            }

            unset($other_opt, $other_val, $checked);

            ?></div>
<?php
        }
    }

} else if ( $field['type'] == 'captcha' && ! FrmAppHelper::is_admin() ) {
    $error_msg = null;

    if ( !empty($errors) ) {
        foreach ( $errors as $error_key => $error ) {
            if ( strpos($error_key, 'captcha-') === 0 ) {
                $error_msg = preg_replace('/^captcha-/', '', $error_key);
            }
            unset($error);
        }
    }

    $frm_settings = FrmAppHelper::get_settings();
    if ( !empty($frm_settings->pubkey) ) {
        FrmFieldsHelper::display_recaptcha($field, $error_msg);
    }
} else {
    do_action('frm_form_fields', $field, $field_name, array('errors' => $errors));
}