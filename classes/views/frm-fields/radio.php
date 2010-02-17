<?php
if (is_array($field['options'])){
    foreach($field['options'] as $opt_key => $opt){
        $checked = ((!is_array($field['value']) && $field['value'] == $opt ) || (is_array($field['value']) && in_array($opt, $field['value'])))?' checked="true"':'';
        require('single-option.php');
    }  
}
?>