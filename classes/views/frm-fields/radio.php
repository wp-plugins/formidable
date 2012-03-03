<?php
if (is_array($field['options'])){
    foreach($field['options'] as $opt_key => $opt){
        $field_val = apply_filters('frm_field_value_saved', $opt, $opt_key, $field);
        $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
        $checked = (isset($field['value']) and ((!is_array($field['value']) && $field['value'] == $opt ) || (is_array($field['value']) && in_array($opt, $field['value'])))) ? ' checked="true"':'';
        require('single-option.php');
        
        unset($checked);
    }  
}
?>