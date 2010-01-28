<div class="wrap">
    <div class="frmicon"><br></div>
    <h2><?php echo FRM_PLUGIN_TITLE ?>: <?php echo ($params['template'])? 'Templates' : 'Forms'; ?></h2>
  
    <?php require(FRM_VIEWS_PATH.'/shared/errors.php'); ?>
  
    <?php do_action('frm_before_item_nav',$sort_str, $sdir_str, $search_str); ?>
    <?php require(FRM_VIEWS_PATH.'/shared/nav.php'); ?>

<?php if ($params['template']) require('default-templates.php'); ?>
        
<form class="form-fields item-list-form" name="item_list_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <input type="hidden" name="action" value="list-form"/> 
  <input type="hidden" name="template" value="<?php echo $params['template'] ?>" />   
<?php $footer = false; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
<table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
        <?php if ($params['template']){ ?>
            <th class="manage-column" width="30%">
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&sort=name<?php echo (($sort_str == 'name' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">Name<?php echo (($sort_str == 'name')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
              <th class="manage-column"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-templates&sort=description<?php echo (($sort_str == 'description' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">Description<?php echo (($sort_str == 'description')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
        <?php }else{?>
            <th class="manage-column" width="50px">
                <?php do_action('frm_column_header'); ?> <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=id<?php echo (($sort_str == 'id' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">ID<?php echo (($sort_str == 'id')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
            <th class="manage-column" width="">
                <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=name<?php echo (($sort_str == 'name' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">Name<?php echo (($sort_str == 'name')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
            <th class="manage-column"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=description<?php echo (($sort_str == 'description' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">Description<?php echo (($sort_str == 'description')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
            <th class="manage-column" width="70px"><a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&sort=form_key<?php echo (($sort_str == 'form_key' and $sdir_str == 'asc')?'&sdir=desc':''); ?>">Key<?php echo (($sort_str == 'form_key')?' &nbsp; <img src="'.FRM_URL.'/images/'.(($sdir_str == 'desc')?'arrow_down.png':'arrow_up.png').'"/>':'') ?></a></th>
            <th class="manage-column" width="70px">Entries</th>
            <th class="manage-column">Direct Link</th>
            <th class="manage-column" width="115px">ShortCode</th>
        <?php } ?>
    </tr>
    </thead>
<?php if($record_count <= 0){ ?>
    <tr>
      <td colspan="<?php echo ($params['template'])? '2':'7'; ?>">No Forms Found</td>
    </tr>
<?php
}else{
    foreach($forms as $form){
?>
      <tr style="min-height: 75px; height: 75px;">
          <?php if ($params['template']){ ?>
              <td class="edit_item">
                  <a class="item_name" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="Edit <?php echo stripslashes($form->name); ?>"><?php echo stripslashes($form->name); ?></a>
                <br/>
                <div class="item_actions">
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>" title="Copy <?php echo $form->name; ?>">Create Form from Template</a>
                  | <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="Edit <?php echo $form->name; ?>">Edit</a>
                  | <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=destroy&id=<?php echo $form->id; ?>"  onclick="return confirm('Are you sure you want to delete your <?php echo $form->name; ?> Form?');" title="Delete <?php echo $form->form_key; ?>">Delete</a>
                </div>
              </td>
              <td><?php echo $form->description ?></td>
          <?php }else{ ?>
              <td><?php do_action('frm_first_col', $form->id); ?> <?php echo $form->id ?></td>
              <td class="edit_item">
                  <a class="item_name" href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="Edit <?php echo stripslashes($form->name); ?>"><?php echo stripslashes($form->name); ?></a>
                <br/>
                <div class="item_actions">
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=edit&id=<?php echo $form->id; ?>" title="Edit <?php echo $form->name; ?>">Edit</a> |
                  <?php if($frmpro_is_installed){ ?>
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&action=new&form=<?php echo $form->id; ?>" title="New <?php echo $form->name; ?> Entry">New Entry</a> |
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>-entries&form=<?php echo $form->id; ?>" title="<?php echo $form->name; ?> Entries">View Entries</a> |
                  <?php } ?>
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>" title="Copy <?php echo $form->name; ?>">Duplicate</a> |
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>&template=1" title="Create <?php echo $form->name; ?> Template">Create Template</a> |
                  <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=destroy&id=<?php echo $form->id; ?>"  onclick="return confirm('Are you sure you want to delete your <?php echo $form->name; ?> Form?');" title="Delete <?php echo $form->form_key; ?>">Delete</a>
                </div>
              </td>
              <td><?php echo stripslashes($form->description) ?></td>
              <td><?php echo $form->form_key ?></td>
              <td><?php echo apply_filters('frm_view_entries_link', $frm_entry->getRecordCount("it.form_id=$form->id") . ' Entries', $form->id); ?></td>
              <td>
                  <input type='text' style="font-size: 10px; width: 100%;" readonly="true" onclick='this.select();' onfocus='this.select();' value='<?php echo $target_url = FrmFormsHelper::get_direct_link($form->form_key, $form->prli_link_id); ?>' /><br/><a href="<?php echo $target_url; ?>" target="blank">View Form</a>
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
            <th class="manage-column">Name</th>
            <th class="manage-column">Description</th>
        <?php }else{ ?>
            <th class="manage-column"><?php do_action('frm_column_header'); ?> ID</th>
            <th class="manage-column">Name</th>
            <th class="manage-column">Description</th>
            <th class="manage-column">Key</th>
            <th class="manage-column">Entries</th>
            <th class="manage-column">Direct Link</th>
            <th class="manage-column">ShortCode</th>
        <?php } ?>
    </tr>
    </tfoot>
</table>
<?php $footer = true; require(FRM_VIEWS_PATH.'/shared/item-table-nav.php'); ?>
</form>

</div>