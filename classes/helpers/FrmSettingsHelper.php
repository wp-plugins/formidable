<?php

class FrmSettingsHelper{
    function wp_pages_dropdown($field_name, $page_id){
        global $frm_app_controller;
        
        $field_value = $frm_app_controller->get_param($field_name);
        $pages = get_posts( array('post_type' => 'page', 'post_status' => 'published', 'numberposts' => 99, 'order_by' => 'post_title', 'order' => 'ASC'));
    ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown frm-pages-dropdown">
            <option value=""></option>
            <?php foreach($pages as $page){ ?>
                <option value="<?php echo $page->ID; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $page->ID) or (!isset($_POST[$field_name]) and $page_id == $page->ID))?' selected="selected"':''); ?>><?php echo $page->post_title; ?> </option>
            <?php } ?>
        </select>
    <?php
    }
}
?>