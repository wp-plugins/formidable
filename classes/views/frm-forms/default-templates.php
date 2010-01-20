<?php FrmFormsHelper::get_template_dropdown($all_templates); ?>

<h3>Default Templates</h3>
<table class="widefat post fixed" cellspacing="0">
    <thead>
    <tr>
        <th class="manage-column" width="30%">Name</th>
        <th class="manage-column">Description</th>
    </tr>
    </thead>
<?php if(empty($default_templates)){ ?>
    <tr><td colspan="2">No Templates Found</td></tr>
<?php
}else{
    foreach($default_templates as $form){
?>
      <tr style="min-height: 60px; height: 60px;">
          <td class="edit_item">
               <a class="item_name" href="<?php echo $url = FrmFormsHelper::get_direct_link($form->form_key); ?>" title="Preview <?php echo stripslashes($form->name); ?>" target="blank"><?php echo stripslashes($form->name); ?></a>
             <br/>
             <div class="item_actions">
               <a href="?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id=<?php echo $form->id; ?>" title="Copy <?php echo $form->name; ?>">Create Form from Template</a> |
               <a href="<?php echo $url ?>" title="View <?php echo stripslashes($form->name); ?>" target="blank">View</a>
             </div>
           </td>
           <td><?php echo $form->description ?></td>
      </tr>
      <?php
    }
  }
?>
    <tfoot>
    <tr>
        <th class="manage-column">Name</th>
        <th class="manage-column">Description</th>
    </tr>
    </tfoot>
</table>

<br/><br/><h3>Custom Templates</h3>
