<h2>
    <a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>&amp;action=edit&amp;id=<?php echo $id ?>"><?php _e('Build', 'formidable') ?></a> >
    <a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>&amp;action=settings&amp;id=<?php echo $id ?>"><?php _e('Settings', 'formidable') ?></a> >
    <a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>-entries&amp;action=list&amp;form=<?php echo $id ?>&amp;show_nav=1"><?php _e('Entries', 'formidable') ?></a> >
    <a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>-reports&amp;action=show&amp;form=<?php echo $id ?>&amp;show_nav=1"><?php _e('Reports', 'formidable') ?></a>
</h2>