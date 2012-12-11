<div id="postbox-container-1" class="<?php echo FrmAppController::get_postbox_class(); ?>">
    <?php if(!isset($hide_preview) or !$hide_preview){ 
        if (!$values['is_template']){ ?>
    <p class="howto" style="margin-top:0;"><?php _e('Insert into a post, page or text widget', 'formidable') ?>
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus="this.select();" value="[formidable id=<?php echo $id; ?>]" /></p>
    <?php } ?>

    <p class="frm_orange"><a href="<?php echo FrmFormsHelper::get_direct_link($values['form_key']); ?>" target="_blank"><?php _e('Preview', 'formidable') ?></a>
    <?php global $frm_settings; 
        if ($frm_settings->preview_page_id > 0){ ?>
        <?php _e('or', 'formidable') ?> 
        <a href="<?php echo add_query_arg('form', $values['form_key'], get_permalink($frm_settings->preview_page_id)) ?>" target="_blank"><?php _e('Preview in Current Theme', 'formidable') ?></a>
    <?php } ?>
    </p>
    <?php
    } ?>
    
    <?php include(FRM_VIEWS_PATH .'/frm-forms/mb_insert_fields.php') ?>
</div>