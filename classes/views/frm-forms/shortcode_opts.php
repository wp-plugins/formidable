<div id="sc-opts-<?php echo $shortcode ?>" class="frm_shortcode_option">
    <input type="radio" name="frmsc" value="<?php echo esc_attr($shortcode) ?>" id="sc-<?php echo esc_attr($shortcode) ?>" style="display:none;" />
<?php
if ( ! empty($form_id) ) {
?>
    <h4 for="frmsc_<?php echo $shortcode .'_'. $form_id ?>" class="frm_left_label"><?php _e('Select a form:', 'formidable') ?></h4>
    <?php FrmFormsHelper::forms_dropdown( 'frmsc_'. $shortcode .'_'. $form_id ); ?>
    <div class="frm_box_line"></div>
<?php
}

if ( ! empty($opts) ) { ?>
    <h4><?php _e('Options', 'formidable') ?></h4>
    <ul>
<?php
foreach ( $opts as $opt => $val ) {
    if ( isset($val['type']) && 'text' == $val['type'] ) { ?>
        <li><label class="setting" for="frmsc_<?php echo $shortcode .'_'. $opt ?>">
            <span><?php echo $val['label'] ?></span>
            <input type="text" id="frmsc_<?php echo $shortcode .'_'. $opt ?>" value="<?php echo esc_attr($val['val']) ?>" />
            </label>
        <li>
    <?php } else if ( isset($val['type']) && 'select' == $val['type'] ) { ?>
        <li><label class="setting" for="frmsc_<?php echo $shortcode .'_'. $opt ?>">
            <span><?php echo $val['label'] ?></span>
            <select id="frmsc_<?php echo $shortcode .'_'. $opt ?>">
                <?php foreach ( $val['opts'] as $select_opt => $select_label ) { ?>
                <option value="<?php echo esc_attr($select_opt) ?>"><?php echo $select_label ?></option>
                <?php } ?>
            </select>
            </label>
        </li>
    <?php } else { ?>
        <li><label class="setting" for="frmsc_<?php echo $shortcode .'_'. $opt ?>"><input type="checkbox" id="frmsc_<?php echo $shortcode .'_'. $opt ?>" value="<?php echo esc_attr($val['val']) ?>" /> <?php echo $val['label'] ?></label><li>
<?php
        }
    }
    ?>
    </ul>
<?php
} ?>
</div>