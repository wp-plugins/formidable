<div class="tablenav">
    <div class="alignleft actions">
        <?php do_action('frm_before_table', $footer, $params['form']);  ?>
    </div>
<?php
  // Only show the pager bar if there is more than 1 page
  if($page_count > 1){ ?>
      <div class='tablenav-pages'><span class="displaying-num"><?php printf(__('Displaying %1$s&#8211;%2$s of %3$s', 'formidable'), $page_first_record, $page_last_record, $record_count); ?></span>
        
    <?php $page_param = 'paged'; require('pagination.php'); ?>
    </div>  
<?php } ?>

</div>
<div style="clear:both;"></div>