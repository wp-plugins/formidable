<?php
class FrmNotification{
    function FrmNotification(){
        add_action('frm_after_create_entry', array($this, 'entry_created'));
    }
    
    function entry_created($entry_id){
        if (apply_filters('frm_stop_standard_email', false, $entry_id)) return;
        global $frm_blogurl, $frm_form, $frm_entry, $frm_entry_meta;

        $frm_blogname = get_option('blogname');
        $entry = $frm_entry->getOne($entry_id);
        $form = $frm_form->getOne($entry->form_id);
        $values = $frm_entry_meta->getAll("it.item_id = $entry->id", " ORDER BY fi.field_order");
        
        $form_options = unserialize($form->options);
        $to_email = $form_options['email_to'];
        if ($to_email == '')
            return;
        $to_emails = explode(',', $to_email);
        
        $reply_to = '';
            
        $opener = sprintf(__('%1$s form has been submitted on %2$s.', FRM_PLUGIN_NAME), $form->name, $frm_blogname) ."\r\n\r\n";
        
        $entry_data = '';
        foreach ($values as $value){
            $val = apply_filters('frm_email_value', maybe_unserialize($value->meta_value), $value);
            if (is_array($val))
                $val = implode(', ', $val);
            
            $entry_data .= $value->field_name . ': ' . $val . "\r\n\r\n";
            if ($reply_to == '' and is_email($val))
                $reply_to = $val;
        }
          
        $data = unserialize($entry->description);  
        $user_data = __('User Information', FRM_PLUGIN_NAME) ."\r\n";
        $user_data .= __('IP Address', FRM_PLUGIN_NAME) . ": ". $entry->ip ."\r\n";
        $user_data .= __('User-Agent (Browser/OS)', FRM_PLUGIN_NAME) . ": ". $data['browser']."\r\n";
        $user_data .= __('Referrer', FRM_PLUGIN_NAME) . ": ". $data['referrer']."\r\n";

        $mail_body = $opener . $entry_data ."\r\n". $user_data;
        $subject = sprintf(__('%1$s Form submitted on %2$s', FRM_PLUGIN_NAME), $form->name, $frm_blogname); //subject

        if(is_array($to_emails)){
            foreach($to_emails as $to_email)
                $this->send_notification_email(trim($to_email), $subject, $mail_body, $reply_to);
        }else
            $this->send_notification_email($to_email, $subject, $mail_body, $reply_to);
    }
  
    function send_notification_email($to_email, $subject, $message, $reply_to='', $reply_to_name='', $plain_text=true){
        $content_type   = ($plain_text) ? 'text/plain' : 'text/html';
        $reply_to_name  = ($reply_to_name == '') ? get_option('blogname') : $reply_to_name; //senders name
        $reply_to       = ($reply_to == '') ? get_option('admin_email') : $reply_to; //senders e-mail address
        $recipient      = $to_email; //recipient
        $header         = "From: {$reply_to_name} <{$reply_to}>\r\n Reply-To: {$reply_to_name} <{$reply_to}>\r\n Content-Type: {$content_type}; charset=\"" . get_option('blog_charset') . "\"\r\n"; //optional headerfields
        $subject        = html_entity_decode(strip_tags(stripslashes($subject)));
        $message        = stripslashes($message);
        if($plain_text)
            $message    = html_entity_decode(strip_tags($message));

        if (!wp_mail($recipient, $subject, $message, $header))
            mail($recipient, $subject, $message, $header);

        do_action('frm_notification', $recipient, $subject, $message);
    }

}
?>