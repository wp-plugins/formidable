<div class="general_settings metabox-holder tabs-panel" style="min-height:0px;border-bottom:none;display:<?php echo ($a == 'general_settings') ? 'block' : 'none'; ?>;">
<?php if (!is_multisite() or is_super_admin()){ ?>
    <div class="postbox">
        <h3 class="hndle manage-menus"><div id="icon-ms-admin" class="icon32 frm_postbox_icon"><br/></div> <?php _e('Formidable Pro License', 'formidable')?></h3>
        <div class="inside">
            <p class="frm_aff_link">Already signed up? <a href="http://formidablepro.com/account/" target="_blank"><?php _e('Click here', 'formidable') ?></a> to get your license number.</p>
            
            <div style="float:left;width:50%;">      
            <p><?php _e('Ready to take your forms to the next level?<br/>Formidable Pro will help you style forms, manage data, and get reports.', 'formidable') ?></p>
            <a href="http://formidablepro.com"><?php _e('Learn More', 'formidable') ?> &#187;</a>
            </div>
            
            <div class="clear"></div>
        </div>
    </div>
<?php } ?>
</div>