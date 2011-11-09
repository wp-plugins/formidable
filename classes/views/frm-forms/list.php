<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php echo ($params['template'])? __('Form Templates', 'formidable') : __('Forms', 'formidable'); 
        if(!$params['template'] and current_user_can('frm_edit_forms')){ ?>
        <a href="?page=<?php echo FRM_PLUGIN_NAME ?>-new" class="button add-new-h2"><?php _e('Add New', 'formidable'); ?></a>
        <?php } ?>
    </h2>
  
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
  
    <?php do_action('frm_before_item_nav',$sort_str, $sdir_str, $search_str, false); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

<?php if ($params['template']) require('default-templates.php'); ?>
        
<form class="form-fields item-list-form" name="item_list_form" id="posts-filter" method="post" >
  <input type="hidden" name="action" value="list-form"/>
  <input type="hidden" name="template" value="<?php echo $params['template'] ?>" />   
<?php $footer = false; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>
        <th class="manage-column check-column" scope="col"> <?php do_action('frm_column_header'); ?> </th>
        <?php if ($params['template']){ ?>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('name', $sort_str, $sdir_str); ?>" width="">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&amp;sort=name&amp;sdir=<?php echo (($sort_str == 'name' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('Name', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('description', $sort_str, $sdir_str); ?>">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&amp;sort=description&amp;sdir=<?php echo (($sort_str == 'description' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('Description', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
        <?php }else{?>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('id', $sort_str, $sdir_str); ?>" width="50px">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;sort=id&amp;sdir=<?php echo (($sort_str == 'id' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('ID', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('name', $sort_str, $sdir_str); ?>" width="350px">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;sort=name&amp;sdir=<?php echo (($sort_str == 'name' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('Name', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('description', $sort_str, $sdir_str); ?>">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;sort=description&amp;sdir=<?php echo (($sort_str == 'description' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('Description', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="manage-column <?php FrmFormsHelper::get_sortable_classes('form_key', $sort_str, $sdir_str); ?>" width="70px">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;sort=form_key&amp;sdir=<?php echo (($sort_str == 'form_key' and $sdir_str == 'desc')?'asc':'desc'); ?>">
                    <span><?php _e('Key', 'formidable') ?></span>
                    <span class="sorting-indicator"></span>
                </a>
            </th>
            <th class="manage-column" width="60px"><?php _e('Entries', 'formidable') ?></th>
            <th class="manage-column" width="115px"><?php _e('Direct Link', 'formidable') ?></th>
            <th class="manage-column" width="115px"><?php _e('ShortCodes', 'formidable') ?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
<?php if($record_count <= 0){ ?>
    <tr>
      <td colspan="<?php echo ($params['template'])? '2':'7'; ?>"><?php _e('No Forms Found', 'formidable') ?>. 
          <a href="?page=<?php echo FRM_PLUGIN_NAME ?>-new"><?php _e('Add New', 'formidable'); ?></a>
      </td>
    </tr>
<?php
}else{
    $alternate = '';
    foreach($forms as $form){
        $alternate = (empty($alternate)) ? ' alternate' : '';
?>
    <tr class="iedit<?php echo $alternate ?>" >
        <th class="check-column" scope="row"><?php do_action('frm_first_col', $form->id); ?></th>
        <?php if ($params['template']){ ?>
        <td class="post-title">
            <?php if(current_user_can('frm_edit_forms')){ ?>
            <a class="row-title" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=edit&amp;id=<?php echo $form->id; ?>" title="<?php _e('Edit', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))); ?>"><?php echo stripslashes($form->name); ?></a>
            <?php }else{    
                    echo stripslashes($form->name); 
                  } ?>
            <br/>
            <div class="row-actions">
                <?php if(current_user_can('frm_edit_forms')){ ?>
                <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=duplicate&amp;id=<?php echo $form->id; ?>" title="<?php _e('Copy', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))); ?>"><?php _e('Create Form from Template', 'formidable') ?></a></span>
                | <span class="edit"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=edit&amp;id=<?php echo $form->id; ?>" title="<?php _e('Edit', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))); ?>"><?php _e('Edit', 'formidable') ?></a></span>
                <?php } ?>
                <?php do_action('frm_template_action_links', $form); ?>
                <?php if(current_user_can('frm_delete_forms')){ ?>
                | <span class="trash"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=destroy&amp;id=<?php echo $form->id; ?>"  onclick="return confirm('<?php printf(__('Are you sure you want to delete your %1$s Form?', 'formidable'), strip_tags(stripslashes($form->name))) ?>');" title="<?php _e('Delete', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))); ?>"><?php _e('Delete', 'formidable') ?></a></span>
                <?php } ?>
            </div>
        </td>
        <td><?php echo $form->description ?></td>
        <?php }else{ ?>
        <td><?php echo $form->id ?></td>
        <td class="post-title">
            <?php if(current_user_can('frm_edit_forms')){ ?>
            <a class="row-title" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=edit&amp;id=<?php echo $form->id; ?>" title="<?php _e('Edit', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))); ?>"><?php echo stripslashes($form->name); ?></a>
            <?php }else{
                    echo stripslashes($form->name); 
                  }
            ?>
            <br/>
            <div class="row-actions">
                <?php if(current_user_can('frm_edit_forms')){ ?>
                <span class="edit"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=edit&amp;id=<?php echo $form->id; ?>" title="<?php _e('Edit', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?>"><?php _e('Edit', 'formidable') ?></a></span>
                <?php } ?>
                <?php if($frmpro_is_installed){ 
                    if(current_user_can('frm_create_entries')){ ?>
                    | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&amp;action=new&amp;form=<?php echo $form->id; ?>" title="<?php _e('New', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?> <?php _e('Entry', 'formidable') ?>"><?php _e('New Entry', 'formidable') ?></a></span>
                    <?php } 
                    
                    if(current_user_can('frm_view_entries')){ ?>
                    | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&amp;form=<?php echo $form->id; ?>" title="<?php echo esc_attr(strip_tags(stripslashes($form->name))); ?> Entries"><?php _e('Entries', 'formidable') ?></a></span>
                    <?php } ?>
                    | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-reports&amp;form=<?php echo $form->id; ?>" title="<?php echo esc_attr(strip_tags(stripslashes($form->name))); ?> Reports"><?php _e('Reports', 'formidable') ?></a></span>
                <?php } 
                
                if(current_user_can('frm_edit_forms')){ ?>
                | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=settings&amp;id=<?php echo $form->id; ?>" title="<?php echo esc_attr(strip_tags(stripslashes($form->name))) ?> <?php _e('Settings', 'formidable') ?>"><?php _e('Settings', 'formidable') ?></a></span>
                | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=duplicate&amp;id=<?php echo $form->id; ?>" title="<?php _e('Copy', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?>"><?php _e('Duplicate', 'formidable') ?></a></span>
                | <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=duplicate&amp;id=<?php echo $form->id; ?>&amp;template=1" title="<?php _e('Create', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?> <?php _e('Template', 'formidable') ?>"><?php _e('Create Template', 'formidable') ?></a></span>
                    <?php if($frmpro_is_installed){ ?>
                | <span><a href="<?php echo FRM_SCRIPT_URL ?>&amp;controller=forms&amp;action=export&amp;id=<?php echo $form->id; ?>" title="<?php _e('Export', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?> <?php _e('Template', 'formidable') ?>"><?php _e('Export Template', 'formidable') ?></a></span>
                <?php }
                } 
                
                if(current_user_can('frm_delete_forms')){ ?>
                | <span class="trash"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&amp;action=destroy&amp;id=<?php echo $form->id; ?>"  onclick="return confirm('<?php printf(__('Are you sure you want to delete your %1$s Form?', 'formidable'), strip_tags(stripslashes($form->name))) ?>');" title="<?php _e('Delete', 'formidable') ?> <?php echo esc_attr(strip_tags(stripslashes($form->name))) ?>"><?php _e('Delete', 'formidable') ?></a></span>
                <?php } ?>
            </div>
        </td>
        <td><?php echo stripslashes($form->description) ?></td>
        <td><?php echo $form->form_key ?></td>
        <td><?php echo apply_filters('frm_view_entries_link', $frm_entry->getRecordCount($form->id) . ' '. __('Entries', 'formidable'), $form->id); ?></td>
        <td>
            <input type='text' style="font-size: 10px; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='<?php echo $target_url = FrmFormsHelper::get_direct_link($form->form_key, $form->prli_link_id); ?>' /><br/><a href="<?php echo $target_url; ?>" target="blank"><?php _e('View Form', 'formidable') ?></a>
        </td>
        <td><input type='text' style="font-size:10px;width:100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='[formidable id=<?php echo $form->id; ?>]' /><br/>
            <input type='text' style="font-size:10px;width:100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='[formidable key=<?php echo $form->form_key ?>]' />
        </td>
        <?php } ?>
    </tr>
      <?php
    }
  }
?>
    </tbody>
    <tfoot>
    <tr>
        <th class="manage-column check-column" scope="col"> <?php do_action('frm_column_header'); ?> </th>
        <?php if ($params['template']){ ?>
            <th class="manage-column"><?php _e('Name', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Description', 'formidable') ?></th>
        <?php }else{ ?>
            <th class="manage-column"><?php _e('ID', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Name', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Description', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Key', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Entries', 'formidable') ?></th>
            <th class="manage-column"><?php _e('Direct Link', 'formidable') ?></th>
            <th class="manage-column"><?php _e('ShortCodes', 'formidable') ?></th>
        <?php } ?>
    </tr>
    </tfoot>
</table>
<?php $footer = true; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
</form>

</div>