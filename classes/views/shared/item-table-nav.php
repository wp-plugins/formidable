<div class="tablenav">
<?php
  // Only show the pager bar if there is more than 1 page
  if($page_count > 1){ ?>
      <?php do_action('frm-item-list-actions', $footer); ?>
      <div class='tablenav-pages'><span class="displaying-num"><?php printf(__('Displaying %1$s&#8211;%2$s of %3$s', FRM_PLUGIN_NAME), $page_first_record, $page_last_record, $record_count); ?></span>
        
    <?php $page_param = 'paged'; require('pagination.php'); ?>
    </div>  
<?php } 

do_action('frm_before_table', $footer, $params['form']); ?>

</div>
<div style="clear:both;"></div>