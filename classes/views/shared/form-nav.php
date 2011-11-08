<ul class="frm_form_nav">
	 <li class="last"> <a class="current_page" href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>-reports&amp;action=show&amp;form=<?php echo $id ?>&amp;show_nav=1"><?php _e('Reports', 'formidable') ?></a></li>
	<li> <a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>-entries&amp;action=list&amp;form=<?php echo $id ?>&amp;show_nav=1"><?php _e('Entries', 'formidable') ?></a></li>
    <li><a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>&amp;action=settings&amp;id=<?php echo $id ?>"><?php _e('Settings', 'formidable') ?></a> </li>
<li class="first"><a href="<?php echo admin_url('admin.php?page='.FRM_PLUGIN_NAME) ?>&amp;action=edit&amp;id=<?php echo $id ?>"><?php _e('Build', 'formidable') ?></a> </li>
  <div style="clear:both;"></div>
</ul>