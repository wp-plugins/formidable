<div class="wrap">
    <div class="frmicon icon32"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php echo ($params['template'])? __('Templates', FRM_PLUGIN_NAME) : __('Forms', FRM_PLUGIN_NAME); ?></h2>
  
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
  
    <?php do_action('frm_before_item_nav',$sort_str, $sdir_str, $search_str); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

<?php if ($params['template']) require('default-templates.php'); ?>
        
<form class="form-fields item-list-form" name="item_list_form" id="posts-filter" method="post" action="">
  <input type="hidden" name="action" value="list-form"/>
  <input type="hidden" name="template" value="<?php echo $params['template'] ?>" />   
<?php $footer = false; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
<table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
        <?php if ($params['template']){ ?>
            <th class="manage-column" width="">
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&sort=name<?php echo (($sort_str == 'name' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('Name', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'name')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
            <th class="manage-column">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&sort=description<?php echo (($sort_str == 'description' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('Description', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'description')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
        <?php }else{?>
            <th class="manage-column" width="50px">
                <?php do_action('frm_column_header'); ?> <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=id<?php echo (($sort_str == 'id' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('ID', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'id')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
            <th class="manage-column" width="">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=name<?php echo (($sort_str == 'name' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('Name', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'name')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
            <th class="manage-column">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=description<?php echo (($sort_str == 'description' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('Description', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'description')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
            <th class="manage-column" width="70px">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=form_key<?php echo (($sort_str == 'form_key' and $sdir_str == 'asc')?'&sdir=desc':''); ?>"><?php _e('Key', FRM_PLUGIN_NAME) ?><?php echo (($sort_str == 'form_key')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a>
            </th>
            <th class="manage-column" width="60px"><?php _e('Entries', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column" width="115px"><?php _e('Direct Link', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column" width="115px"><?php _e('ShortCode', FRM_PLUGIN_NAME) ?></th>
        <?php } ?>
    </tr>
    </thead>
<?php if($record_count <= 0){ ?>
    <tr>
      <td colspan="<?php echo ($params['template'])? '2':'7'; ?>"><?php _e('No Forms Found', FRM_PLUGIN_NAME) ?></td>
    </tr>
<?php
}else{
    foreach($forms as $form){
?>
      <tr style="min-height: 75px; height: 75px;" class="iedit">
          <?php if ($params['template']){ ?>
              <td class="post-title">
                  <a class="row-title" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="<?php _e('Edit', FRM_PLUGIN_NAME) ?> <?php echo stripslashes($form->name); ?>"><?php echo stripslashes($form->name); ?></a>
                <br/>
                <div class="row-actions">
                    <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>" title="<?php _e('Copy', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?>"><?php _e('Create Form from Template', FRM_PLUGIN_NAME) ?></a></span>
                  | <span class="edit"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="<?php _e('Edit', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?>"><?php _e('Edit', FRM_PLUGIN_NAME) ?></a></span>
                  <?php do_action('frm_template_action_links', $form); ?>
                  | <span class="trash"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=destroy&id=<?php echo $form->id; ?>"  onclick="return confirm(<?php printf(__('Are you sure you want to delete your %1$s Form?'), $form->name) ?>);" title="<?php _e('Delete', FRM_PLUGIN_NAME) ?> <?php echo $form->form_key; ?>"><?php _e('Delete', FRM_PLUGIN_NAME) ?></a></span>
                </div>
              </td>
              <td><?php echo $form->description ?></td>
          <?php }else{ ?>
              <td><?php do_action('frm_first_col', $form->id); ?> <?php echo $form->id ?></td>
              <td class="post-title">
                  <a class="row-title" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="<?php _e('Edit', FRM_PLUGIN_NAME) ?> <?php echo stripslashes($form->name); ?>"><?php echo stripslashes($form->name); ?></a>
                <br/>
                <div class="row-actions">
                  <span class="edit"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="<?php _e('Edit', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?>"><?php _e('Edit', FRM_PLUGIN_NAME) ?></a></span> |
                  <?php if($frmpro_is_installed){ ?>
                  <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&action=new&form=<?php echo $form->id; ?>" title="<?php _e('New', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?> <?php _e('Entry', FRM_PLUGIN_NAME) ?>"><?php _e('New Entry', FRM_PLUGIN_NAME) ?></a></span> |
                  <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&form=<?php echo $form->id; ?>" title="<?php echo $form->name; ?> Entries"><?php _e('Entries', FRM_PLUGIN_NAME) ?></a></span> |
                  <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-reports&form=<?php echo $form->id; ?>" title="<?php echo $form->name; ?> Reports"><?php _e('Reports', FRM_PLUGIN_NAME) ?></a></span> |
                  <?php } ?>
                  <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>" title="<?php _e('Copy', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?>"><?php _e('Duplicate', FRM_PLUGIN_NAME) ?></a></span> |
                  <span><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>&template=1" title="<?php _e('Create', FRM_PLUGIN_NAME) ?> <?php echo $form->name; ?> <?php _e('Template', FRM_PLUGIN_NAME) ?>"><?php _e('Create Template', FRM_PLUGIN_NAME) ?></a></span> |
                  <span class="trash"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=destroy&id=<?php echo $form->id; ?>"  onclick="return confirm(<?php printf(__('Are you sure you want to delete your %1$s Form?'), $form->name) ?>);" title="<?php _e('Delete', FRM_PLUGIN_NAME) ?> <?php echo $form->form_key; ?>"><?php _e('Delete', FRM_PLUGIN_NAME) ?></a></span>
                </div>
              </td>
              <td><?php echo stripslashes($form->description) ?></td>
              <td><?php echo $form->form_key ?></td>
              <td><?php echo apply_filters('frm_view_entries_link', $frm_entry->getRecordCount("it.form_id=$form->id") . ' '. __('Entries', FRM_PLUGIN_NAME), $form->id); ?></td>
              <td>
                  <input type='text' style="font-size: 10px; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='<?php echo $target_url = FrmFormsHelper::get_direct_link($form->form_key, $form->prli_link_id); ?>' /><br/><a href="<?php echo $target_url; ?>" target="blank"><?php _e('View Form', FRM_PLUGIN_NAME) ?></a>
              </td>
              <td><input type='text' style="font-size: 10px; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='[formidable id=<?php echo $form->id; ?>]' /></td>
        <?php } ?>
      </tr>
      <?php
    }
  }
?>
    <tfoot>
    <tr>
        <?php if ($params['template']){ ?>
            <th class="manage-column"><?php _e('Name', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Description', FRM_PLUGIN_NAME) ?></th>
        <?php }else{ ?>
            <th class="manage-column"><?php do_action('frm_column_header'); ?> <?php _e('ID', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Name', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Description', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Key', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Entries', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('Direct Link', FRM_PLUGIN_NAME) ?></th>
            <th class="manage-column"><?php _e('ShortCode', FRM_PLUGIN_NAME) ?></th>
        <?php } ?>
    </tr>
    </tfoot>
</table>
<?php $footer = true; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
</form>

</div>