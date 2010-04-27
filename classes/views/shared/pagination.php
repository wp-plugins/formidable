<?php if($current_page > 1){ // Only show the prev page button if the current page is not the first page ?>
<a class='prev page-numbers' href="<?php echo add_query_arg(array($page_param => $current_page - 1)); ?>">&laquo;</a> <?php 
}
      
// First page is always displayed
if($current_page==1){ ?>
<span class='page-numbers current'>1</span><?php 
}else{ ?>
<a class='page-numbers' href="<?php echo add_query_arg(array($page_param => 1)); ?>">1</a> <?php 
}

// If the current page is more than 2 spaces away from the first page then we put some dots in here
if($current_page >= 5){ ?>
<span class='page-numbers dots'>...</span> <?php 
}
      
// display the current page icon and the 2 pages beneath and above it
$low_page = (($current_page >= 5)?($current_page-2):2);
$high_page = ((($current_page + 2) < ($page_count-1))?($current_page+2):($page_count-1));
for($i = $low_page; $i <= $high_page; $i++){
    if($current_page==$i){  ?>
        <span class='page-numbers current'><?php print $i; ?></span> <?php
    }else{ ?>
        <a class='page-numbers' href="<?php echo add_query_arg(array($page_param => $i)); ?>"><?php print $i; ?></a> <?php
    }
}
      
// If the current page is more than 2 away from the last page then show ellipsis
if($current_page < ($page_count - 3)){ ?>
    <span class='page-numbers dots'>...</span> <?php 
}
      
// Display the last page icon
if($current_page == $page_count){ ?>
    <span class='page-numbers current'><?php print $page_count; ?></span><?php 
}else{ ?>
    <a class='page-numbers' href="<?php echo add_query_arg(array($page_param => $page_count)); ?>"><?php print $page_count; ?></a><?php 
}
      
// Display the next page icon if there is a next page
if($current_page < $page_count){ ?>
    <a class='next page-numbers' href="<?php echo add_query_arg(array($page_param => $current_page + 1)); ?>">&raquo;</a><?php 
} ?>