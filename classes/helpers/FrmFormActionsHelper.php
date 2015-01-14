<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

class FrmFormActionsHelper{

    public static function get_action_for_form($form_id, $type = 'all', $limit = 99) {
        $action_controls = FrmFormActionsController::get_form_actions( $type );
        if ( empty($action_controls) ) {
            // don't continue if there are no available actions
            return array();
        }

        if ( 'all' != $type ) {
            return $action_controls->get_all( $form_id, $limit );
        }

        $args = array(
            'menu_order'    => $form_id,
            'post_type'     => FrmFormsController::$action_post_type,
            'post_status'   => 'publish',
            'numberposts'   => 99,
            'orderby'       => 'title',
            'order'         => 'ASC',
        );

        $actions = FrmAppHelper::check_cache(serialize($args), 'frm_actions');
        if ( false == $actions ) {
            $actions = get_posts( $args );
            wp_cache_set(serialize($args), $actions, 'frm_actions', 300);
        }

        if ( ! $actions ) {
            return array();
        }

        $settings = array();
        foreach ( $actions as $action ) {
            if ( ! isset($action_controls[$action->post_excerpt]) || count($settings) >= $limit ) {
                continue;
            }

            $action = $action_controls[$action->post_excerpt]->prepare_action($action);

            $settings[$action->ID] = $action;
        }

        if ( 1 === $limit ) {
            $settings = reset($settings);
        }

        return $settings;
    }

    public static function action_conditions_met($action, $entry) {
        $notification = $action->post_content;
        $stop = false;
        $met = array();

        if ( !isset($notification['conditions']) || empty($notification['conditions']) ) {
            return $stop;
        }

        foreach ( $notification['conditions'] as $k => $condition ) {
            if ( !is_numeric($k) ) {
                continue;
            }

            if ( $stop && 'any' == $notification['conditions']['any_all'] && 'stop' == $notification['conditions']['send_stop'] ) {
                continue;
            }

            if ( is_array($condition['hide_opt']) ) {
                $condition['hide_opt'] = reset($condition['hide_opt']);
            }

            $observed_value = isset($entry->metas[$condition['hide_field']]) ? $entry->metas[$condition['hide_field']] : '';
            if ( $condition['hide_opt'] == 'current_user' ) {
                $condition['hide_opt'] = get_current_user_id();
            }

            $stop = FrmFieldsHelper::value_meets_condition($observed_value, $condition['hide_field_cond'], $condition['hide_opt']);

            if ( $notification['conditions']['send_stop'] == 'send' ) {
                $stop = $stop ? false : true;
            }

            $met[$stop] = $stop;
        }

        if ( $notification['conditions']['any_all'] == 'all' && !empty($met) && isset($met[0]) && isset($met[1]) ) {
            $stop = ($notification['conditions']['send_stop'] == 'send') ? true : false;
        } else if ( $notification['conditions']['any_all'] == 'any' && $notification['conditions']['send_stop'] == 'send' && isset($met[0]) ) {
            $stop = false;
        }

        return $stop;
    }

    public static function default_action_opts($class = ''){
        return array(
            'classes'   => 'frm_icon_font '. $class,
            'active'    => false,
            'limit'     => 0,
        );
    }

    /* Prepare and json_encode post content
    *
    * Since 2.0
    *
    * @param $post_content array
    * @return $post_content string ( json encoded array )
    */
    public static function prepare_and_encode( $post_content ) {

        //Loop through array to strip slashes and add only the needed ones
        foreach( $post_content as $key => $val ) {
            if ( !is_array( $val ) ) {
                // Strip all slashes so everything is the same, no matter where the value is coming from
                $val = stripslashes( $val );

                // Add backslashes before double quotes and forward slashes only
                $post_content[$key] = addcslashes( $val, '"\\/' );
            }
            unset( $key, $val );
        }

        // json_encode the array
        $post_content = json_encode( $post_content );

	    // add extra slashes for \r\n since WP strips them
	    $post_content = str_replace( array('\\r', '\\n', '\\u'), array('\\\\r', '\\\\n', '\\\\u'), $post_content );

        // allow for &quot
	    $post_content = str_replace( '&quot;', '\\"', $post_content );

        return $post_content;
    }
}
