<div id="postbox-container-1" class="postbox-container">

    <?php
    $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
    $action = FrmAppHelper::get_param($action);
    $button = ($action == 'new' or $action == 'duplicate') ? __('Create', 'formidable') : __('Update', 'formidable');
    ?>

    <?php include(FrmAppHelper::plugin_path() .'/classes/views/frm-forms/_publish_box.php') ?>

    <div id="frm_position_ele"></div>


    <div class="postbox frm_field_list">
    <div class="inside">
    <div id="taxonomy-linkcategory" class="categorydiv">
        <ul id="category-tabs" class="category-tabs frm-category-tabs">
    		<li class="tabs" ><a href="#frm-insert-fields" id="frm_insert_fields_tab"><?php _e( 'Fields', 'formidable' ); ?></a></li>
    		<li class="hide-if-no-js"><a href="#frm-layout-classes" id="frm_layout_classes_tab"><?php _e( 'Layout', 'formidable' ); ?></a></li>
    		<?php do_action('frm_extra_form_instruction_tabs'); ?>
    	</ul>

    	<div id="frm-insert-fields" class="tabs-panel" style="max-height:none;overflow:visible;">
		    <ul class="field_type_list">
            <?php
            $col_class = 'frm_col_one';
            foreach ( $frm_field_selection as $field_key => $field_type ) { ?>
                <li class="frmbutton button <?php echo $col_class ?> frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>"><a href="#" class="frm_add_field"><?php echo $field_type ?></a></li>
            <?php
                $col_class = (empty($col_class)) ? 'frm_col_one' : '';
                unset($field_key, $field_type);
            } ?>
            </ul>
            <div class="clear"></div>
            <hr/>
			<ul<?php echo apply_filters('frm_drag_field_class', '') ?> style="margin-top:2px;">
            <?php $col_class = 'frm_col_one';
            $no_allow_class = apply_filters('frm_noallow_class', 'frm_noallow');
            foreach ( FrmFieldsHelper::pro_field_selection() as $field_key => $field_type ) {

                if ( is_array($field_type) ) {
                    $field_label = $field_type['name'];

                    if ( isset($field_type['switch_from']) ) { ?>
                <li class="frmbutton button <?php echo $col_class .' '. $no_allow_class ?> frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>" data-switchto="<?php echo $field_type['switch_from'] ?>" style="display:none !important;"><?php echo apply_filters('frmpro_field_links', $field_label, $id, $field_key) ?></li>
<?php
                        continue;
                    }

?>
                <li class="frmbutton button <?php echo $col_class .' '. $no_allow_class ?> frm_t<?php echo $field_key ?> dropdown" id="<?php echo $field_key ?>" <?php echo (isset($field_type['switch_to'])) ? 'data-switchto="'. $field_type['switch_to'] .'"' : ''; ?>>
	                <a href="#" id="frm-<?php echo $field_key ?>Drop" class="frm-dropdown-toggle" data-toggle="dropdown"><?php echo $field_label ?> <b class="caret"></b></a>

                    <ul class="frm-dropdown-menu" role="menu" aria-labelledby="frm-<?php echo $field_key ?>Drop">
                	<?php foreach ( $field_type['types'] as $k => $type ) {
                        ?>
                        <li class="frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>|<?php echo $k ?>"><?php echo apply_filters('frmpro_field_links', $type, $id, $field_key .'|'. $k) ?></li>
                	<?php
                	        unset($k, $type);
                	    } ?>
                	</ul>
                </li>
<?php
                } else {
                    $field_label = $field_type;
                    ?>
                    <li class="frmbutton button <?php echo $col_class .' '. $no_allow_class ?> frm_t<?php echo $field_key ?>" id="<?php echo $field_key ?>"><?php echo apply_filters('frmpro_field_links', $field_label, $id, $field_key) ?></li>
                    <?php
                }

                $col_class = empty($col_class) ? 'frm_col_one' : '';
                unset($field_key, $field_type, $field_label);
            } ?>
            </ul>
            <div class="clear"></div>
        </div>
    	<?php do_action('frm_extra_form_instructions'); ?>

    	<div id="frm-layout-classes" class="tabs-panel" style="display:none;max-height:none;">
			<p class="howto"><?php _e('Add classes in the "CSS layout classes" field option', 'formidable') ?></p>
    	    <ul class="frm_code_list">
    	    <?php $classes = array(
    	            'frm_first_half' => __('First 1/2', 'formidable'),
    	            'frm_last_half' => __('Last 1/2', 'formidable'),
    	            'frm_first_third' => __('First 1/3', 'formidable'),
    	            'frm_last_third' => __('Last 1/3', 'formidable'),
    	            'frm_first_two_thirds' => __('First 2/3', 'formidable'),
    	            'frm_last_two_thirds' => __('Last 2/3', 'formidable'),
    	            'frm_third' => __('1/3', 'formidable'),
    	            'frm_fourth' => __('1/4', 'formidable'),
    	            'frm_first_fourth' => array('label' => __('First 1/4', 'formidable')),
    	            'frm_last_fourth' => __('Last 1/4', 'formidable'),
    	            'frm_first_fifth' => __('First 1/5', 'formidable'),
    	            'frm_last_fifth' => __('Last 1/5', 'formidable'),
    	            'frm_fifth' => __('1/5', 'formidable'),
    	            'frm_inline' => array('label' => __('Inline', 'formidable'), 'title' => __('Align fields in a row without a specific width.', 'formidable')),
    	            'frm_first_inline' => array('label' => __('First Inline', 'formidable'), 'title' => __('Align fields at the beginning of a row without a specific width.', 'formidable')),
    	            'frm_last_inline' => array('label' => __('Last Inline', 'formidable'), 'title' => __('Align fields at the end of a row without a specific width.', 'formidable')),

    	            'frm_full' => array('label' => __('100% width', 'formidable'), 'title' => __('Force the field to fill the full space with 100% width.', 'formidable')),
    	            'frm_grid_first' => __('First Grid Row', 'formidable'),
    	            'frm_grid' => __('Even Grid Row', 'formidable'),
    	            'frm_grid_odd' => __('Odd Grid Row', 'formidable'),
    	            'frm_two_col' => array('label' => __('2 Col Options', 'formidable'), 'title' => __('Put your radio button or checkbox options into two columns.', 'formidable')),
    	            'frm_three_col' => array('label' => __('3 Col Options', 'formidable'), 'title' => __('Put your radio button or checkbox options into three columns.', 'formidable')),
    	            'frm_four_col' => array('label' => __('4 Col Options', 'formidable'), 'title' => __('Put your radio button or checkbox options into four columns.', 'formidable')),
    	            'frm_total' => array('label' => __('Total', 'formidable'), 'title' => __('Add this to a read-only field to display the text in bold without a border or background.', 'formidable')),
    	            'frm_scroll_box' => array('label' => __('Scroll Box', 'formidable'), 'title' => __('If you have many checkbox or radio button options, you may add this class to allow your user to easily scroll through the options.', 'formidable'))
    	        );
    	        $classes = apply_filters('frm_layout_classes', $classes);
    	        $col = 'one';
    	        foreach($classes as $c => $d){
    	            $title = ( ! empty($d) && is_array($d) && isset($d['title']) ) ? $d['title'] : '';
    	        ?>
    	        <li class="frm_col_<?php echo $col ?>">
                    <a href="javascript:void(0);" class="frmbutton frm_insert_code button show_frm_classes<?php
                    if ( ! empty($title) ) {
                        echo ' frm_help';
                    } ?>" data-code="<?php echo esc_attr($c) ?>" <?php
                    if ( ! empty($title) ) {
                        ?>title="<?php echo esc_attr($title); ?>"<?php
                    } ?>>
                <?php
                        if ( empty($d) ) {
                            echo $c;
                        } else if ( !is_array($d) ) {
                            echo $d;
                        } else if ( isset($d['label']) ) {
                            echo $d['label'];
                        }
                ?>
                    </a>
                </li>
                <?php
                    $col = ($col == 'one') ? 'two' : 'one';
    	            unset($c);
    	            unset($d);
    	        }
    	    ?>
    	    </ul>
    	</div>
    </div>
    </div>

    <div class="submitbox" id="major-publishing-actions">
        <div id="delete-action">
            <?php echo FrmFormsHelper::delete_trash_link($id, $values['status']); ?>
        </div>

        <div id="publishing-action">
            <form method="post" id="frm_js_build_form">
            <span class="spinner"></span>
		    <input type="hidden" id="frm_compact_fields" name="frm_compact_fields" value="" />
    	    <input type="button" value="<?php echo esc_attr($button) ?>" class="frm_submit_form frm_submit_<?php echo ( isset($values['ajax_load']) && $values['ajax_load'] ) ? '': 'no_'; ?>ajax button-primary button-large" id="frm_submit_side" />
    	    </form>
		</div>
        <div class="clear"></div>
    </div><!-- #major-publishing-actions -->



    </div>
</div>
