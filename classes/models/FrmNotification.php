<?php
class FrmNotification{
    function FrmNotification(){
        add_action('frm_after_create_entry', array($this, 'entry_created'));
    }
    
    function entry_created($entry){
        global $frm_blogname, $frm_blogurl, $frm_form, $frm_entry_meta;

        $form = $frm_form->getOne($entry->form_id);
        $values = $frm_entry_meta->getAll("it.item_id = $entry->id", " ORDER BY fi.field_order");
        
        $form_options = unserialize($form->options);
        $to_email = $form_options['email_to'];
        if ($to_email == '')
            return;
            
        $opener = sprintf(__('%1$s form has been submitted on %2$s.', FRM_PLUGIN_NAME), $form->name, $frm_blogname);
        
        $entry_data = '';
        foreach ($values as $value){
            $val = maybe_unserialize($value->meta_value);
            if (is_array($val))
                $val = implode(', ', $val);
            $entry_data .= $value->field_name . ': ' . $val . "\n";
        }
          
        $data = unserialize($entry->description);  
        $user_data = "User Information\n";
        $user_data .= "IP Address: ". $data['ip'] ."\n";
        $user_data .= "User-Agent (Browser/OS): ". $data['browser']."\n";
        $user_data .= "Referrer: ". $data['referrer']."\n";

        $mail_body =<<<MAIL_BODY
{$opener}

{$entry_data}

{$user_data}
MAIL_BODY;
        $subject = sprintf(__('%1$s Form submitted on %2$s', FRM_PLUGIN_NAME), $form->name, $frm_blogname); //subject

        $this->send_notification_email($to_email, $subject, $mail_body, 'friend_request');
    }
  
    function send_notification_email($to_email, $subject, $message, $message_type){
        global $frm_blogname;

        if(isset($user->hide_notifications[$message_type]))
          return;

        $from_name     = $frm_blogname; //senders name
        $from_email    = get_option('admin_email'); //senders e-mail address
        $recipient     = "<{$to_email}>"; //recipient
        $header        = "From: {$from_name} <{$from_email}>\r\n"; //optional headerfields
        $subject       = html_entity_decode(strip_tags(stripslashes($subject)));
        $message       = html_entity_decode(strip_tags(stripslashes($message)));
        $signature     = $this->get_mail_signature();

        //$to_email      = $user->email;
        //$to_name       = $user->full_name;
        //$full_to_email = "{$to_name} <{$to_email}>";

        wp_mail($to_email, $subject, $message.$signature, $header);

        do_action('frm_notification', $to_email, $subject, $message.$signature);
    }
    
    function get_mail_signature(){
        global $frm_blogname;

        $thanks              = __('Thanks!', FRM_PLUGIN_NAME);
        $team                = sprintf(__('%s Team', FRM_PLUGIN_NAME), $frm_blogname);
        //$manage_subscription = sprintf(__('If you want to stop future emails like this from coming to you, please modify your form settings.', FRM_PLUGIN_NAME));

        $signature =<<<MAIL_SIGNATURE


{$thanks}

{$team}

MAIL_SIGNATURE;

        return $signature;
    }
}
?>