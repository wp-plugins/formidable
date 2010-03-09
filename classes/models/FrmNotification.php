<?php
class FrmNotification{
    function FrmNotification(){
        add_action('frm_after_create_entry', array($this, 'entry_created'));
    }
    
    function entry_created($entry_id){
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
        
        $from_email = '';
            
        $opener = sprintf(__('%1$s form has been submitted on %2$s.', FRM_PLUGIN_NAME), $form->name, $frm_blogname) ."\r\n\r\n";
        
        $entry_data = '';
        foreach ($values as $value){
            $val = apply_filters('frm_email_value', maybe_unserialize($value->meta_value), $value);
            if (is_array($val))
                $val = implode(', ', $val);
            
            $entry_data .= $value->field_name . ': ' . $val . "\r\n\r\n";
            if ($from_email == '' and is_email($val))
                $from_email = $val;
        }
          
        $data = unserialize($entry->description);  
        $user_data = __('User Information', FRM_PLUGIN_NAME) ."\r\n";
        $user_data .= __('IP Address', FRM_PLUGIN_NAME) . ": ". $data['ip'] ."\r\n";
        $user_data .= __('User-Agent (Browser/OS)', FRM_PLUGIN_NAME) . ": ". $data['browser']."\r\n";
        $user_data .= __('Referrer', FRM_PLUGIN_NAME) . ": ". $data['referrer']."\r\n";

        $mail_body = $opener . $entry_data ."\r\n". $user_data;
        $subject = sprintf(__('%1$s Form submitted on %2$s', FRM_PLUGIN_NAME), $form->name, $frm_blogname); //subject

        if(is_array($to_emails)){
            foreach($to_emails as $to_email)
                $this->send_notification_email(trim($to_email), $subject, $mail_body, $from_email);
        }else
            $this->send_notification_email($to_email, $subject, $mail_body, $from_email);
    }
  
    function send_notification_email($to_email, $subject, $message, $from_email=''){
        $from_name     = get_option('blogname'); //senders name
        $from_email    = ($from_email == '') ? get_option('admin_email') : $from_email; //senders e-mail address
        $recipient     = $to_email; //recipient
        $header        = "From: {$from_email}\r\n"; //optional headerfields
        $subject       = html_entity_decode(strip_tags(stripslashes($subject)));
        $message       = html_entity_decode(strip_tags(stripslashes($message)));
        $signature     = '';//$this->get_mail_signature();

        //$to_email      = $user->email;
        //$to_name       = $user->full_name;
        //$full_to_email = "{$to_name} <{$to_email}>";

        if (!wp_mail($recipient, $subject, $message.$signature, $header)){
            $header .= "Reply-To: {$from_email}\r\n Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\r\n";
            mail($recipient, $subject, $message, $header);
        }

        do_action('frm_notification', $recipient, $subject, $message.$signature);
    }
    
    function get_mail_signature(){
        $thanks              = __('Thanks!', FRM_PLUGIN_NAME);
        $team                = sprintf(__('%s Team', FRM_PLUGIN_NAME), get_option('blogname'));
        //$manage_subscription = sprintf(__('If you want to stop future emails like this from coming to you, please modify your form settings.', FRM_PLUGIN_NAME));

        $signature =<<<MAIL_SIGNATURE


{$thanks}

{$team}

MAIL_SIGNATURE;

        return $signature;
    }
}
?>