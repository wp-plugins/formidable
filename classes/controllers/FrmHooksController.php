<?php

class FrmHooksController {
    /**
     * Trigger plugin-wide hook loading
     */
    public static function trigger_load_hook( $hooks = 'load_hooks' ) {
        $controllers = apply_filters( 'frm_load_controllers', array( 'FrmHooksController' ) );

        $trigger_hooks = $hooks;
        $hooks = (array) $hooks;

        if ( 'load_hooks' == $trigger_hooks ) {
            if ( is_admin() ) {
                $hooks[] = 'load_admin_hooks';
                if ( defined( 'DOING_AJAX' ) ) {
                    $hooks[] = 'load_ajax_hooks';
                    $hooks[] = 'load_form_hooks';
                }
            }

            if ( is_multisite() ) {
                $hooks[] = 'load_multisite_hooks';
            }
        } else {
            // Make sure the hooks are only triggered once
            add_filter('frm'. str_replace('load', '', $trigger_hooks) .'_loaded', '__return_true');
        }
        unset($trigger_hooks);

        // Instansiate Controllers
        foreach ( $controllers as $c ) {
            foreach ( $hooks as $hook ) {
				call_user_func( array( $c, $hook ) );
				unset( $hook );
            }
			unset( $c );
        }

    }

	public static function load_hooks() {
        if ( ! is_admin() ) {
            add_filter('the_content', 'FrmAppController::page_route', 10);
        }

        add_action('plugins_loaded', 'FrmAppController::load_lang' );
        add_action('init', 'FrmAppController::front_head' );
        add_filter('widget_text', 'FrmAppController::widget_text_filter', 8 );
        add_action('wp_footer', 'FrmAppController::footer_js', 1, 0);

        // Entries controller
        add_action('wp', 'FrmEntriesController::process_entry', 10, 0);
        add_action('frm_wp', 'FrmEntriesController::process_entry', 10, 0);
        add_filter('frm_redirect_url', 'FrmEntriesController::delete_entry_before_redirect', 50, 3);
        add_action('frm_after_entry_processed', 'FrmEntriesController::delete_entry_after_save', 100);
        add_filter('frm_email_value', 'FrmEntriesController::filter_email_value', 10, 3);
        add_filter('frmpro_fields_replace_shortcodes', 'FrmEntriesController::filter_shortcode_value', 10, 4);

        // Form Actions Controller
        add_action('init', 'FrmFormActionsController::register_post_types', 1);
		add_action( 'frm_after_create_entry', 'FrmFormActionsController::trigger_create_actions', 20, 3 );

        // Forms Controller
        add_action('widgets_init', 'FrmFormsController::register_widgets' );
        add_filter('frm_content', 'FrmFormsController::filter_content', 10, 3);
        add_filter('frm_replace_content_shortcodes', 'FrmFormsController::replace_content_shortcodes', 20, 3);
        add_action('admin_bar_init', 'FrmFormsController::admin_bar_css' );
        add_action('wp_before_admin_bar_render', 'FrmFormsController::admin_bar_configure' );
        add_action('wp_scheduled_delete', 'FrmFormsController::scheduled_delete' );

        // Form Shortcodes
        add_shortcode('formidable', 'FrmFormsController::get_form_shortcode' );

        // Styles Controller
        add_action('init', 'FrmStylesController::register_post_types', 0);
        add_filter('frm_get_style_opts', 'FrmStylesController::get_style_opts');
        add_filter('frm_add_form_style_class', 'FrmStylesController::get_form_style_class', 10, 2);
        add_filter('frm_show_entry_styles', 'FrmStylesController::show_entry_styles');
    }

	public static function load_admin_hooks() {
        add_action('admin_menu', 'FrmAppController::menu', 1);
        add_action('admin_enqueue_scripts', 'FrmAppController::load_wp_admin_style' );
        add_filter('update_plugin_complete_actions', 'FrmAppController::update_action_links', 10, 2 );
        add_action('admin_notices', 'FrmAppController::pro_get_started_headline' );
		add_action( 'admin_init', 'FrmAppController::admin_init', 11 );
        register_activation_hook(FrmAppHelper::plugin_path().'/formidable.php', 'FrmAppController::activation_install' );

        // Entries Controller
        add_action('admin_menu', 'FrmEntriesController::menu', 12);
        add_filter('contextual_help', 'FrmEntriesController::contextual_help', 10, 3 );
        add_filter('set-screen-option', 'FrmEntriesController::save_per_page', 10, 3);
        add_filter('update_user_metadata', 'FrmEntriesController::check_hidden_cols', 10, 5);
        add_action('updated_user_meta', 'FrmEntriesController::update_hidden_cols', 10, 4);

        // Fields Controller
        add_filter('frm_display_field_options', 'FrmFieldsController::display_field_options');
        add_action('frm_field_options_form', 'FrmFieldsController::add_conditional_update_msg', 50);

        // Form Actions Controller
        if ( FrmAppHelper::is_admin_page('formidable' ) ) {
            add_action('frm_before_update_form_settings', 'FrmFormActionsController::update_settings');
        }
        add_action('frm_after_duplicate_form', 'FrmFormActionsController::duplicate_form_actions', 20, 3);

        // Forms Controller
        add_action('admin_menu', 'FrmFormsController::menu', 10);
        add_action('admin_head-toplevel_page_formidable', 'FrmFormsController::head' );

        add_filter('set-screen-option', 'FrmFormsController::save_per_page', 10, 3);
        add_action('admin_footer',  'FrmFormsController::insert_form_popup' );
        add_filter('media_buttons_context', 'FrmFormsController::insert_form_button' );

        // Forms Model
        add_action('frm_after_duplicate_form', 'FrmForm::after_duplicate', 10, 2);

        // Settings Controller
        add_action('admin_menu', 'FrmSettingsController::menu', 45);
        add_action('frm_before_settings', 'FrmSettingsController::license_box');

        // Stats Controller
        add_action('admin_menu', 'FrmStatisticsController::menu', 24);
        add_action('frm_form_action_reports', 'FrmStatisticsController::list_reports');

        // Styles Controller
        add_action('admin_menu', 'FrmStylesController::menu', 14);
        add_action('admin_init', 'FrmStylesController::admin_init');

        // XML Controller
        add_action('admin_menu', 'FrmXMLController::menu', 41);
    }

	public static function load_ajax_hooks() {
		add_action( 'wp_ajax_frm_silent_upgrade', 'FrmAppController::ajax_install' );
		add_action( 'wp_ajax_nopriv_frm_silent_upgrade', 'FrmAppController::ajax_install' );
        add_action('wp_ajax_frm_install', 'FrmAppController::install' );
        add_action('wp_ajax_frm_uninstall', 'FrmAppController::uninstall' );
        add_action('wp_ajax_frm_deauthorize', 'FrmAppController::deauthorize' );

        add_action('wp_ajax_frmpro_css', 'FrmAppController::load_css' );
        add_action('wp_ajax_nopriv_frmpro_css', 'FrmAppController::load_css' );

        // Fields Controller
        add_action('wp_ajax_frm_load_field', 'FrmFieldsController::load_field');
        add_action('wp_ajax_frm_insert_field', 'FrmFieldsController::create');
        add_action('wp_ajax_frm_update_field_form_id', 'FrmFieldsController::update_form_id');
        add_action('wp_ajax_frm_field_name_in_place_edit', 'FrmFieldsController::edit_name');
        add_action('wp_ajax_frm_update_ajax_option', 'FrmFieldsController::update_ajax_option');
        add_action('wp_ajax_frm_duplicate_field', 'FrmFieldsController::duplicate');
        add_action('wp_ajax_frm_delete_field', 'FrmFieldsController::destroy');
        add_action('wp_ajax_frm_add_field_option', 'FrmFieldsController::add_option');
        add_action('wp_ajax_frm_field_option_ipe', 'FrmFieldsController::edit_option');
        add_action('wp_ajax_frm_delete_field_option', 'FrmFieldsController::delete_option');
        add_action('wp_ajax_frm_import_choices', 'FrmFieldsController::import_choices');
        add_action('wp_ajax_frm_import_options', 'FrmFieldsController::import_options');
        add_action('wp_ajax_frm_update_field_order', 'FrmFieldsController::update_order');

        // Form Actions Controller
        add_action('wp_ajax_frm_add_form_action', 'FrmFormActionsController::add_form_action');
        add_action('wp_ajax_frm_form_action_fill', 'FrmFormActionsController::fill_action');

        // Forms Controller
		add_action( 'wp_ajax_frm_create_from_template', 'FrmFormsController::_create_from_template' );
		add_action( 'wp_ajax_frm_save_form', 'FrmFormsController::route' );
		add_action( 'wp_ajax_frm_form_key_in_place_edit', 'FrmFormsController::edit_key' );
		add_action( 'wp_ajax_frm_form_desc_in_place_edit', 'FrmFormsController::edit_description' );
        add_action('wp_ajax_frm_get_default_html', 'FrmFormsController::get_email_html' );
        add_action('wp_ajax_frm_get_shortcode_opts',  'FrmFormsController::get_shortcode_opts' );
        add_action('wp_ajax_frm_forms_preview', 'FrmFormsController::preview' );
        add_action('wp_ajax_nopriv_frm_forms_preview', 'FrmFormsController::preview' );

        // Styles Controller
        add_action('wp_ajax_frm_settings_reset', 'FrmStylesController::reset_styling');
        add_action('wp_ajax_frm_change_styling', 'FrmStylesController::change_styling');
        add_action('wp_ajax_frmpro_load_css', 'FrmStylesController::load_css');
        add_action('wp_ajax_nopriv_frmpro_load_css', 'FrmStylesController::load_css');

        // XML Controller
        add_action('wp_ajax_frm_export_xml', 'FrmXMLController::export_xml');
    }

	public static function load_form_hooks() {
        // Fields Controller
        add_filter('frm_field_type', 'FrmFieldsController::change_type');
        add_action('frm_field_input_html', 'FrmFieldsController::input_html');
        add_filter('frm_field_value_saved', 'FrmFieldsController::check_value', 50, 3);
        add_filter('frm_field_label_seen', 'FrmFieldsController::check_label');

        // Styles Controller
        add_filter('frm_use_important_width', 'FrmStylesController::important_style', 10, 2 );
    }

	public static function load_view_hooks() {
        // Hooks go here when a view is loaded
    }

	public static function load_multisite_hooks() {
		add_action( 'wpmu_upgrade_site', 'FrmAppController::network_upgrade_site' );

        // drop tables when mu site is deleted
        add_filter( 'wpmu_drop_tables', 'FrmAppController::drop_tables' );
    }
}