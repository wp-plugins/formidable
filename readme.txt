=== Formidable ===
Contributors: sswells
Donate link: http://blog.strategy11.com/donate
Tags: WPMU, form, builder, drag, drop, widget, sidebar, Post, posts, page, wordpress, plugin, template, contact, captcha, email, database, save, admin, akismet, AJAX, links, javascript, jquery, theme, spam, content, image, images, 
Requires at least: 2.5
Tested up to: 2.9.1
Stable tag: 1.0.10

Quickly and easily build forms with a simple drag-and-drop interface.

== Description ==
Quickly and easily build forms with a simple drag-and-drop interface.

= Features =
* Integrates with Pretty Link, WP reCAPTCHA, and Akismet
* Shortcode [formidable id=x] for use in pages, posts, or text widgets for WordPress version 2.8 and above.
* Alternatively use `<?php echo FrmEntriesController::show_form(2, $key = '', $title=true, $description=true); ?>` in your template
* Customize most HTML when editing the form (code for editing HTML when creating the form is soon to follow... and documentation too)
* Create forms from existing templates or add your own
* Direct links available with and without integration with your current theme
* Select an email address to send form responses under "Advanced Form Options"
* Input default data into form fields with the option to clear when clicked
* Saves responses to the database for future retrieval, reports, and display (Pro version Coming Soon)
* PHP ninjas can display data in templates using functions in FrmApiController. However, there is currently no documentation for these functions.

Feedback and requests are welcome.

== Installation ==
1. Upload `formidable` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu
3. Go to the Formidable 'Settings' menu to select a front-end preview page.
4. Create a new custom form or use the existing Contact Form template.
5. Use shortcode [formidable id=x] in pages, posts, or text widgets. (Requires WordPress version 2.8 or higher)

== Screenshots ==
1. List forms.
2. List templates.
3. Form builder.

== Changelog ==
= 1.0.11 =
* Added a selectable shortcode on the forms listing page
* Fixed the before and after HTML fields to display properly
* Added option to clear default text on a textarea (paragraph input)
* Added option for validation to ignore default values

= 1.0.10 =
* Started HTML customization. Will be updated, but for now you can only edit the HTML when editing the form.
* Added 'Settings' link on plugin page

= 1.0.09 =
* Fixes for PHP 4 compatibility

= 1.0.08 =
* Allow required indicator to be blank
* Hide paragraph tags if field description is empty
* General code cleanup

= 1.0.07 =
* Added Akismet integration
* Replaced all instances of `<?` with `<?php`
* Fixed bug preventing multiple forms from showing on the same page

= 1.0.06 =
* Added option to rename submit button
* Added option to customize success message
* Moved default form values from pro to free version
* Added option to clear default text when field is clicked

= 1.0.05 =
* Added loading indicator to required star and when field is added by dragging
* Added confirmation before field is deleted
* Fixed field options for radio buttons to correctly save
* Don't call pluggable.php if functions are already defined (To remove conflict with Role Scoper)
* Added Pro auto-update code for testing

= 1.0.4 =
* Fix captcha for WPMU
* Hide captcha field if WP reCAPTCHA is not installed

= 1.0.3 =
* Allow `<?php echo FrmEntriesController::show_form(id, key, title, description);?>` to be used in a template

= 1.0.2 =
* Fixed error on submission from direct link

= 1.0.1 =
* Fixed shortcode for text widget
* Removed extra menu item