<div id="postbox-container-1" class="postbox-container">
<div id="submitdiv" class="postbox">
    <h3 class="hndle"><span><?php _e('Entry Actions', 'formidable') ?></span></h3>
    <div class="inside">
        <div class="submitbox">
        <div id="minor-publishing" style="border:none;">
            <div class="misc-pub-section">
                <?php do_action('frm_show_entry_publish_box', $entry); ?>
                <div class="clear"></div>
            </div>
            <div id="misc-publishing-actions">
                <?php include(dirname(__FILE__) .'/_sidebar-shared-pub.php'); ?>
                <div class="misc-pub-section">
                    <span class="dashicons dashicons-format-aside wp-media-buttons-icon"></span>
                    <a href="#" onclick="window.print();return false;"><?php _e('Print', 'formidable') ?></a>
                </div>
            </div>
        </div>
    	<div id="major-publishing-actions">
    	    <?php if ( current_user_can('frm_delete_entries') ) { ?>
    	    <div id="delete-action">
    	        <a href="?page=formidable-entries&amp;frm_action=destroy&amp;id=<?php echo $id; ?>&amp;form=<?php echo $entry->form_id ?>" class="submitdelete deletion" onclick="return confirm('<?php _e('Are you sure you want to delete that entry?', 'formidable') ?>');" title="<?php _e('Delete') ?>"><?php _e('Delete') ?></a>
    	        <?php if ( ! empty($entry->post_id) ) { ?>
        	    <a href="?page=formidable-entries&amp;frm_action=destroy&amp;id=<?php echo $id; ?>&amp;form=<?php echo $entry->form_id ?>&amp;keep_post=1" class="submitdelete deletion" style="margin-left:10px;" onclick="return confirm('<?php _e('Are you sure you want to delete this entry?', 'formidable') ?>);" title="<?php _e('Delete entry but leave the post', 'formidable') ?>"><?php _e('Delete without Post', 'formidable') ?></a>
        	    <?php } ?>
    	    </div>
    	    <?php } ?>

            <?php do_action('frm_entry_major_pub', $entry); ?>
            <div class="clear"></div>
        </div>
        </div>
    </div>
</div>
<?php do_action('frm_show_entry_sidebar', $entry); ?>
</div>