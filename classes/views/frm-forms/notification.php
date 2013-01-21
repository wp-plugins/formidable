<div id="frm_notification_<?php echo $email_key ?>" class="tabs-panel notification_settings <?php if(!$first_email) echo 'panel_secondary' ?>" style="display:none;">
<table class="form-table">
<tr valign="top">
    <td width="100px"><label><?php _e('From/Reply to', 'formidable') ?></label> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('Usually the name and email of the person filling out the form. Select from Text, Email, User ID, or hidden fields for the name. &lt;br/&gt;Defaults to your site name and admin email found on the WordPress General Settings page.', 'formidable') ?>" /></td>
    <td class="frm_email_reply_container"><span class="howto"><?php _e('Name', 'formidable') ?></span> 
        
        <select name="notification[<?php echo $email_key ?>][reply_to_name]" id="reply_to_name_<?php echo $email_key ?>" onchange="frmCheckCustomEmail(this.value,'reply_to_name',<?php echo $email_key ?>)">
        <option value=""><?php echo FrmAppHelper::truncate(get_option('blogname'), 80); ?></option>
        <option value="custom" <?php selected($notification['reply_to_name'], 'custom'); ?>><?php _e('Custom Name', 'formidable') ?></option>
        <?php 
        if(!empty($values['fields'])){ ?>
        <optgroup label="<?php _e('Fields', 'formidable') ?>">
        <?php
        $field_select = array('text', 'email', 'user_id', 'hidden');
        foreach($values['fields'] as $val_key => $fo){
            if(in_array($fo['type'], $field_select)){ ?>
                <option value="<?php echo $fo['id'] ?>" <?php selected($notification['reply_to_name'], $fo['id']); ?>><?php echo FrmAppHelper::truncate($fo['name'], 40) ?></option>
    <?php }else if($fo['type'] == 'data' and $fo['data_type'] != 'data'){
            if(isset($values['fields'][$val_key]['linked'])){
                foreach($values['fields'][$val_key]['linked'] as $linked_field){ 
                if(!in_array($linked_field->type, $field_select)) continue; ?>
                <option value="<?php echo $fo['id'] ?>|<?php echo $linked_field->id ?>" <?php selected($notification['reply_to_name'], $fo['id'] .'|'. $linked_field->id); ?>><?php echo $fo['name'] .': '. FrmAppHelper::truncate($linked_field->name, 40) ?></option>
            <?php } 
            }
            }
        }
        } ?>
    </select>

    <span class="howto" ><?php _e('Email', 'formidable') ?></span> 
    <select name="notification[<?php echo $email_key ?>][reply_to]" id="reply_to_<?php echo $email_key ?>" onchange="frmCheckCustomEmail(this.value,'reply_to',<?php echo $email_key ?>)">
        <option value=""><?php echo get_option('admin_email') ?></option>
        <option value="custom" <?php selected($notification['reply_to'], 'custom'); ?>><?php _e('Custom Address', 'formidable') ?></option>
        <?php 
        if(!empty($values['fields'])){ ?>
        <optgroup label="<?php _e('Fields', 'formidable') ?>">
        <?php
        foreach($values['fields'] as $val_key => $fo){
            if(in_array($fo['type'], $field_select)){ ?>
                <option value="<?php echo $fo['id'] ?>" <?php selected($notification['reply_to'], $fo['id']); ?>><?php echo FrmAppHelper::truncate($fo['name'], 40) ?></option>
        <?php }else if($fo['type'] == 'data' and $fo['data_type'] != 'data'){
                if(isset($values['fields'][$val_key]['linked'])){ ?>
                <?php foreach($values['fields'][$val_key]['linked'] as $linked_field){ 
                    if(!in_array($linked_field->type, $field_select)) continue; ?>
                    <option value="<?php echo $fo['id'] ?>|<?php echo $linked_field->id ?>" <?php selected($notification['reply_to'], $fo['id'] .'|'. $linked_field->id); ?>><?php echo $fo['name'] .': '. FrmAppHelper::truncate($linked_field->name, 40) ?></option>
                <?php } 
                }
            }
        }
        } ?>
    </select>
    
    <div id="frm_cust_reply_container_<?php echo $email_key ?>" <?php echo ($notification['reply_to_name'] == 'custom' or $notification['reply_to'] == 'custom') ? '' : 'style="display:none"'; ?>>
    <span class="howto" style="visibility:hidden;"><?php _e('Name', 'formidable') ?></span> 
    <input type="text" name="notification[<?php echo $email_key ?>][cust_reply_to_name]" value="<?php echo esc_attr($notification['cust_reply_to_name']) ?>" id="cust_reply_to_name_<?php echo $email_key ?>" title="<?php _e('Name', 'formidable') ?>" <?php echo ($notification['reply_to_name'] == 'custom') ? '' : 'style="visibility:hidden;"'; ?> />
    <span class="howto" style="visibility:hidden;"><?php _e('Email', 'formidable') ?></span> 
    <input type="text" name="notification[<?php echo $email_key ?>][cust_reply_to]" value="<?php echo esc_attr($notification['cust_reply_to']) ?>" id="cust_reply_to_<?php echo $email_key ?>" title="<?php _e('Email Address', 'formidable') ?>" <?php echo ($notification['reply_to'] == 'custom') ? '' : 'style="visibility:hidden;"'; ?> />
    </div>
    </td>
</tr>

 <tr>
     <td colspan="2"><label><?php _e('Email Recipients', 'formidable') ?></label> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('To send to multiple addresses, separate each address with a comma. You can use [admin_email] to dynamically use the address on your WordPress General Settings page. &lt;br/&gt;PRO only: Leave blank if you do not want email notifications for this form.', 'formidable') ?>" />
    <input type="text" name="notification[<?php echo $email_key ?>][email_to]" value="<?php echo esc_attr($notification['email_to']); ?>" class="frm_not_email_to frm_long_input" id="email_to_<?php echo $email_key ?>" /></td>
</tr>
<?php if(!$frmpro_is_installed){ ?>
<tr><td colspan="2">
    <?php FrmAppController::update_message('customize your email notifications and send auto responders'); ?>
</td></tr>
<?php } ?>
<?php do_action('frm_additional_form_notification_options', $values, compact('notification', 'email_key')); ?> 
</table>
</div>