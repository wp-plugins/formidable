<div id="postbox-container-1" class="<?php echo FrmAppController::get_postbox_class(); ?>">
    <?php if(!isset($hide_preview) or !$hide_preview){ 
        if (!$values['is_template']){ ?>
    <p class="howto" style="margin-top:0;"><?php _e('Insert into a post, page or text widget', 'formidable') ?>
        <a href="http://formidablepro.com/knowledgebase/publish-your-forms/" target="_blank"><img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('Key and id are generally synonymous. For more information on using this shortcode, click now.', 'formidable') ?>" /></a>
        <a href="#" onclick="frmToggleDiv('.frm_ext_sc')"><?php _e('Show more', 'formidable') ?></a>
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus="this.select();" value="[formidable id=<?php echo $id; ?>]" />
    <span class="frm_ext_sc">
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus="this.select();" value="[formidable id=<?php echo $id; ?> title=true description=true]" />
    <input type="text" style="text-align:center;font-weight:bold;width:100%;" readonly="true" onclick="this.select();" onfocus="this.select();" value="[formidable key=<?php echo $values['form_key']; ?>]" /><br/>
    
    <?php _e('Insert in a template', 'formidable') ?>
    <input type="text" style="font-size:10px;width:100%;text-align:center;" readonly="true" onclick="this.select();" onfocus="this.select();" value="&lt;?php echo FrmAppController::get_form_shortcode(array('id' => <?php echo $id; ?>, 'title' => false, 'description' => false)); ?&gt;" />
    </span>
    </p>
    </tr>
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