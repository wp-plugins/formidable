<?php

class FrmNotification{
    function __construct(){
        if ( ! defined('ABSPATH') ) {
            die('You are not allowed to call this page directly.');
        }
        add_action('frm_trigger_email_action', array(__CLASS__, 'trigger_email'), 10, 3);
    }

    public static function trigger_email($action, $entry, $form) {
        if ( defined('WP_IMPORTING') ) {
            return;
        }

        global $wpdb, $frm_entry_meta;

        $notification = $action->post_content;
        $email_key = $action->ID;

        // Set the subject
        if ( empty($notification['email_subject']) ) {
            $notification['email_subject'] = sprintf(__('%1$s Form submitted on %2$s', 'formidable'), $form->name, '[sitename]');
        }

        $plain_text = $notification['plain_text'] ? true : false;

        //Filter these fields
        $filter_fields = array(
            'email_to', 'cc', 'bcc',
            'reply_to', 'from',
            'email_subject', 'email_message',
        );

        add_filter('frm_plain_text_email', ($plain_text ? '__return_true' : '__return_false'));
        add_filter('frmpro_fields_replace_shortcodes', 'FrmFormsController::filter_email_value', 20, 4);

        //Get all values in entry in order to get User ID field ID
        $values = $frm_entry_meta->getAll('it.field_id != 0 and it.item_id ='. $entry->id .' ORDER BY fi.field_order');
        $user_id_field = $user_id_key = '';
        foreach ( $values as $value ) {
            if ( $value->field_type == 'user_id' ) {
                $user_id_field = $value->field_id;
                $user_id_key = $value->field_key;
                break;
            }
            unset($value);
        }

        //Filter and prepare the email fields
        foreach ( $filter_fields as $f ) {
            //Don't allow empty From
            if  ( in_array($f, array('from')) && empty($notification[$f]) ) {
                $notification[$f] = '[admin_email]';
            //Remove brackets
            } else if ( in_array($f, array('email_to', 'cc', 'bcc', 'reply_to', 'from')) ) {
                //Add a space in case there isn't one
                $notification[$f] = str_replace('<', ' ', $notification[$f]);
                $notification[$f] = str_replace(array('"', '>'), '', $notification[$f]);

                //Switch userID shortcode to email address
                if ( strpos($notification[$f], '[' . $user_id_field . ']' ) !== false || strpos($notification[$f], '[' . $user_id_key . ']' ) !== false ) {
                    $user_data = get_userdata($entry->metas[$user_id_field]);
                    $user_email = $user_data->user_email;
                    $notification[$f] = str_replace( array('[' . $user_id_field . ']','[' . $user_id_key . ']') , $user_email , $notification[$f]);
                }
            }

            $notification[$f] = FrmFieldsHelper::basic_replace_shortcodes($notification[$f], $form, $entry);
        }

        //Put recipients, cc, and bcc into an array if they aren't empty
        $to_emails = ( ! empty( $notification['email_to'] ) ? preg_split( "/(,|;)/", $notification['email_to'] ) : '' );
        $cc = ( ! empty( $notification['cc'] ) ? preg_split( "/(,|;)/", $notification['cc'] ) : '' );
        $bcc = ( ! empty( $notification['bcc'] ) ? preg_split( "/(,|;)/", $notification['bcc'] ) : '' );

        $to_emails = apply_filters('frm_to_email', $to_emails, array(), $form->id, compact('email_key', 'entry', 'form'));

        // Stop now if there aren't any recipients
        if ( empty($to_emails) ) {
            return;
        }

        $to_emails = array_unique( (array) $to_emails );

        $prev_mail_body = $mail_body = $notification['email_message'];
        $mail_body = FrmEntriesHelper::replace_default_message($mail_body, array(
            'id' => $entry->id, 'entry' => $entry, 'plain_text' => $plain_text,
            'user_info' => (isset($notification['inc_user_info']) ? $notification['inc_user_info'] : false),
        ) );

        // Add the user info if it isn't already included
        if ( $notification['inc_user_info'] && $prev_mail_body == $mail_body ) {
            $data = maybe_unserialize($entry->description);
            $mail_body .= "\r\n\r\n" . __('User Information', 'formidable') ."\r\n";
            $mail_body .= __('IP Address', 'formidable') . ": ". $entry->ip ."\r\n";
            $mail_body .= __('User-Agent (Browser/OS)', 'formidable') . ": ". $data['browser']."\r\n";
            $mail_body .= __('Referrer', 'formidable') . ": ". $data['referrer']."\r\n";
        }
        unset($prev_mail_body);

        // Add attachments
        $attachments = apply_filters('frm_notification_attachment', array(), $form, compact('entry', 'email_key') );

        if ( ! empty($notification['email_subject']) ) {
            $notification['email_subject'] = apply_filters('frm_email_subject', $notification['email_subject'], compact('form', 'entry', 'email_key'));
        }

        // check for a phone number
        foreach ( (array) $to_emails as $email_key => $e ) {
            $e = trim($e);
            if ( $e != '[admin_email]' && ! is_email($e) ) {
                $e = explode(' ', $e);

                //If to_email has name <test@mail.com> format
                if ( is_email(end($e)) ) {
                    continue;
                }

                do_action('frm_send_to_not_email', array(
                    'e'         => $e,
                    'subject'   => $notification['email_subject'],
                    'mail_body' => $mail_body,
                    'reply_to'  => $notification['reply_to'],
                    'from'      => $notification['from'],
                    'plain_text' => $plain_text,
                    'attachments' => $attachments,
                    'form'      => $form,
                    'email_key' => $email_key,
                ) );

                unset($to_emails[$email_key]);
            }
        }

        // Send the email now
        $sent_to = self::send_email( array(
            'to_email'      => $to_emails,
            'subject'       => $notification['email_subject'],
            'message'       => $mail_body,
            'from'          => $notification['from'],
            'plain_text'    => $plain_text,
            'reply_to'      => $notification['reply_to'],
            'attachments'   => $attachments,
            'cc'            => $cc,
            'bcc'           => $bcc,
        ) );

        return $sent_to;
    }

    function entry_created($entry_id, $form_id, $create = true) {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmFormActionsController::trigger_actions("create", '. $form_id .', '. $entry_id .', "email")');
        FrmFormActionsController::trigger_actions('create', $form_id, $entry_id, 'email');
    }

    function send_notification_email($to_email, $subject, $message, $from = '', $from_name = '', $plain_text = true, $attachments = array(), $reply_to = '') {
        _deprecated_function( __FUNCTION__, '2.0', 'FrmNotification::send_email' );

        return self::send_email(compact(
            'to_email', 'subject', 'message',
            'from', 'from_name', 'plain_text',
            'attachments', 'reply_to'
        ));
    }

    static function send_email($atts) {
        $admin_email = get_option('admin_email');
        $defaults = array(
            'to_email'      => $admin_email,
            'subject'       => '',
            'message'       => '',
            'from'          => $admin_email,
            'from_name'     => '',
            'cc'            => '',
            'bcc'           => '',
            'plain_text'    => true,
            'reply_to'      => $admin_email,
            'attachments'   => array(),
        );
        $atts = wp_parse_args($atts, $defaults);

        //senders e-mail address
        $atts['from'] = ( empty($atts['from']) || $atts['from'] == '[admin_email]' ) ? $admin_email : $atts['from'];

        //Allow name <test@mail.com> format in To, BCC, CC, Reply To, and From fields
        $filter_fields = array('to_email','bcc','cc','from','reply_to');
        foreach ( $filter_fields as $f ) {
            if ( empty($atts[$f]) ) {
                continue;
            }
            if ( is_array($atts[$f]) ) {//to_email, cc, and bcc can be an array
                foreach ( $atts[$f] as $key => $val ) {
                    $val = trim($val);
                    if ( is_email($val) ) {
                        continue;
                    } else {
                        $parts = explode(' ', $val);
                        $part_2 = end($parts);
                        $part_1 = trim(str_replace($part_2, '', $val));
                        $atts[$f][$key] = $part_1 . ' <'. $part_2 .'>';
                        $atts[$f][$key] = str_replace('"', '', $atts[$f][$key]);
                        unset($part_1,$part_2,$val);
                    }
                }
                unset($f);
                continue;
            }
            if ( is_email($atts[$f]) ) {
                // add sender's name if not included in $from
                if ( $f == 'from' ) {
                    $part_2 = $atts[$f];
                    $part_1  = ( '' == $atts['from_name'] ) ? wp_specialchars_decode( get_option('blogname'), ENT_QUOTES ) : $atts['from_name'];
                } else {
                    continue;
                }
            } else {
                $parts = explode(' ', $atts[$f]);
                $part_2 = end($parts);
                $part_1 = trim(str_replace($part_2, '', $atts[$f]));
            }
            $atts[$f] = $part_1 . ' <'. $part_2 .'>';
            $atts[$f] = str_replace('"', '', $atts[$f]);
            unset($part_1, $part_2, $f);
        }

        if ( empty($atts['reply_to']) ) {
            $atts['reply_to'] = $atts['from'];
        }

        if ( ! is_array($atts['to_email']) && '[admin_email]' == $atts['to_email'] ) {
            $atts['to_email'] = $admin_email;
        }

        $recipient      = $atts['to_email']; //recipient
        $header         = array();
        $header[]       = 'From: ' . $atts['from'];

        //Allow for cc and bcc arrays
        $array_fields = array('CC' => $atts['cc'], 'BCC' => $atts['bcc']);
        foreach ( $array_fields as $key => $a_field ) {
            if ( empty($a_field) ) {
                continue;
            }
            if ( is_array($a_field ) ) {
                foreach ( $a_field as $email ) {
                    $header[] = $key . ': ' . $email;
                }
            } else {
                $header[] = $key . ': ' . $a_field;
            }
            unset($key, $a_field);
        }

        $content_type   = $atts['plain_text'] ? 'text/plain' : 'text/html';
        $charset        = get_option('blog_charset');

        $header[]       = 'Reply-To: '. $atts['reply_to'];
        $header[]       = 'Content-Type: '. $content_type .'; charset="'. $charset . '"';
        $atts['subject'] = wp_specialchars_decode(strip_tags(stripslashes($atts['subject'])), ENT_QUOTES );

        $message        = do_shortcode($atts['message']);
        $message        = wordwrap($message, 70, "\r\n"); //in case any lines are longer than 70 chars
        if ( $atts['plain_text'] ) {
            $message    = wp_specialchars_decode(strip_tags($message), ENT_QUOTES );
        }

        $header         = apply_filters('frm_email_header', $header, array(
            'to_email' => $atts['to_email'], 'subject' => $atts['subject'])
        );

        if ( apply_filters('frm_encode_subject', 1, $atts['subject'] ) ) {
            $atts['subject'] = '=?'. $charset .'?B?'. base64_encode($atts['subject']) .'?=';
        }

        remove_filter('wp_mail_from', 'bp_core_email_from_address_filter' );
        remove_filter('wp_mail_from_name', 'bp_core_email_from_name_filter');

        $sent = wp_mail($recipient, $atts['subject'], $message, $header, $atts['attachments']);
        if ( ! $sent ) {
            $header = 'From: '. $atts['from'] ."\r\n";
            $recipient = implode(',', (array) $recipient);
            $sent = mail($recipient, $atts['subject'], $message, $header);
        }

        do_action('frm_notification', $recipient, $atts['subject'], $message);

        if ( $sent ) {
            if ( apply_filters('frm_echo_emails', false) ) {
                $temp = str_replace('<', '&lt;', $atts['to_email']);
                echo implode(', ', (array) $temp);
            }
            return $atts['to_email'];
        }
    }

}
