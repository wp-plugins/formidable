<?php
if ( ! is_array($field['options']) ) {
    return;
}

foreach ( $field['options'] as $opt_key => $opt ) {
    $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
    $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);

    //If option is an "other" option, and there is a value set for this field, check if the value belongs in the current "Other" option text field
    if ( isset( $field['other'] ) && $field['other'] == true && $opt_key && strpos( $opt_key, 'other' ) !== false && isset( $field['value'] ) ) {
        $other_val = FrmAppHelper::check_other_selected( $field['value'], $field['options'], $field['type'], $opt_key );
    } else {
        $other_val = '';
    }

    $checked = ( ( isset( $other_val ) && $other_val ) || isset($field['value']) &&  (( ! is_array($field['value']) && $field['value'] == $field_val ) || (is_array($field['value']) && in_array($field_val, $field['value']) ) ) ) ? ' checked="true"':'';

    if ( $opt_key && strpos( $opt_key, 'other' ) !== false ) {
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/other-option.php');
    } else {
        include(FrmAppHelper::plugin_path() .'/classes/views/frm-fields/single-option.php');
    }

    unset($checked, $other_val);
}
