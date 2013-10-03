<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php echo ($params['template'])? __('Form Templates', 'formidable') : __('Forms', 'formidable'); 
        if(!$params['template'] and current_user_can('frm_edit_forms')){ ?>
        <a href="?page=formidable-new" class="button add-new-h2"><?php _e('Add New'); ?></a>
        <?php } ?>
    </h2>
  
<?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>

<form id="posts-filter" method="get">
    <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>" />
    <input type="hidden" name="frm_action" value="list" />
<?php $wp_list_table->search_box( __( 'Search', 'formidable' ), 'entry' ); 

if ($params['template']) require(FRM_VIEWS_PATH .'/frm-forms/default-templates.php');

$wp_list_table->display(); ?>
</form>

</div>