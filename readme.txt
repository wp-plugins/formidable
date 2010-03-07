=== Formidable ===
Contributors: sswells
Donate link: http://blog.strategy11.com/donate
Tags: WPMU, form, builder, drag, drop, widget, sidebar, Post, posts, page, wordpress, plugin, template, contact, contact form, forms, captcha, email, database, save, admin, akismet, AJAX, links, javascript, jquery, theme, spam, content, image, images, poll, survey, feedback
Requires at least: 2.8
Tested up to: 2.9.2
Stable tag: 1.02.0

Quickly and easily build forms with a simple drag-and-drop interface and in-place editing.

== Description ==
Quickly and easily build forms with a simple drag-and-drop interface and in-place editing.
There are dozens of form-building plugins out there to create forms, but most are confusing and overly complicated. With Formidable, it is easy to create forms within a simple drag-and-drop interface. You can construct custom forms or generate them from a template. Shortcodes can be used as well as spam catching services.

= Upgrade to Formidable Pro =

Formidable Pro is an upgrade to Formidable with more form fields, flexibility, and power. Learn more at:

http://formidablepro.com

= Features =
* Integrates with WP reCAPTCHA and Akismet for Spam control
* Shortcode [formidable id=x] for use in pages, posts, or text widgets for WordPress version 2.8 and above.
* Alternatively use `<?php echo FrmEntriesController::show_form(2, $key = '', $title=true, $description=true); ?>` in your template
* Customize most HTML when editing the form (code for editing HTML when creating the form is soon to follow... and documentation too)
* Create forms from existing templates or add your own
* Direct links available for previews and emailing surveys with and without integration with your current theme. Make these links pretty with [Pretty Link](http://blog.strategy11.com/prettylink "Pretty Link") integration
* Select an email address to send form responses under "Advanced Form Options"
* Input default data into form fields with the option to clear when clicked
* Saves responses to the database for future retrieval, reports, and display in [Formidable Pro](http://formidablepro.com/ "Formidable Pro")
* PHP ninjas can display data in templates using functions in FrmApiController. However, there is currently no documentation for these functions.

= PRO Features =
* Visual form styling editor
* Additional fields which include page breaks for multiple paged forms, file uploads, section headers, rich text editor, date with calendar, email, phone, website, and a dynamic field populated with data from other entries.
* View graphical reports for the form results (replace Google docs surveys)
* Add, edit, search, and export entries from the WordPress admin
* Make your default values dynamic
* Conditionally hide and show fields
* Display your gathered data in a page, post, or widget


== Installation ==
1. Upload `formidable` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu
3. Go to the Formidable 'Settings' menu to select a front-end preview page.
4. Create a new custom form or use the existing Contact Form template.
5. Use shortcode [formidable id=x] in pages, posts, or text widgets. (Requires WordPress version 2.8 or higher)

== Screenshots ==
[Formidable Screenshots](http://blog.strategy11.com/formidable-wordpress-plugin/ "Formidable Screenshots")

[Formidable Pro Screenshots](http://blog.strategy11.com/formidablepro/ "Formidable Pro Screenshots")

== Frequently Asked Questions ==
[Formidable FAQs](http://blog.strategy11.com/formidable-faqs/ "Formidable FAQs")

== Changelog ==
= 1.02.0 =
* Updated in-place edit to save more easily and not wipe HTML when editing
* Updated email notifications to hopefully work for more users, send from the first email address submitted in the form, and send one email per email address in the form options
* Changed form to show after being submitted instead of only showing the success message.
* Fixed bug causing newly added fields to be ordered incorrectly when dragged into the form
* Made the field list sticky temporarily until the UI gets further updates
* Fixed quotation marks and apostrophes in form input fields so data won't be lost after them
* Radio buttons and check boxes are now truly required
* PRO: Added a Page Break field for multiple paged forms
* PRO: Added a Rich Text Editor field
* PRO: Added a widget to list entries linking to entry detail page to be used along-side a single custom display
* PRO: Added an option to order entries by ascending or descending in the entry display settings
* PRO: Added front-end pagination for displaying entries
* PRO: Fixed bug with multiple forms on single page causing 2nd form to be hidden when 1st form submitted with errors
* PRO: Updated custom displays for faster front-end loading and more efficiency
* PRO: Added `size` option parameter to file upload shortcodes and `show` parameter to data from entries field for use in custom displays
* PRO: Added customizable HTML for section divider and page break
* PRO: Added page to view entries in a read-only format in the admin

= 1.01.04 =
* Updated in-place edit to work with more characters and function without the save buttons
* Fixed bug causing several form options to be lost when the form name or description was edited without also clicking update for the whole form
* Made more user interface modifications
* PRO: Added dynamic default values for GET/POST variables
* PRO: Added shortcode for fetching field-wide calculations `[frm-stats id=5 type=(count, total, average, or median)]`
* PRO: Added icon link to duplicate an individual field
* PRO: Increased the WPMU efficiency so the templates are only updated if the database version is changed
* PRO: Added functionality to the 'Data From Entries' field to use another observed 'Data From Entries' field to join a third form
* PRO: Fixed admin entry searches to start on first page of results if search was submitted from a higher page

= 1.01.03 =
* Fixed bug preventing field options from showing on a newly-added field
* PRO: Added option to activate Pro sitewide for WPMU
* PRO: Added option to copy forms and entry displays to other blogs in WPMU
* PRO: Fixed checkbox bug when editing an entry

= 1.01.02 =
* Updated the form builder page with a little more simplicity and less clutter
* PRO: Added a warning message if Pro is not activated

= 1.01.01 =
* Fixed bug preventing stylesheet override on individual forms
* PRO: Backed out pretty permalinks 
* PRO: Fixed bug duplicating displayed data

= 1.01.0 =
* Added checkboxes to optionally include default stylesheet
* Completely validated HTML this time (hopefully)
* PRO: Added a FREAKING AWESOME form styling editor
* PRO: Made the link to view entries pretty if default permalinks are not in use
* PRO: Fixed bug preventing external shortcodes from getting replaced when custom displayed data is not inserted automatically
* PRO: Added shortcode for front-end search

= 1.0.12 =
* Validated HTML markup for front-end form
* Simplified the way a default template is created so it will also get updated with any changes
* Really fixed the after HTML field this time
* Changed option to email form to default to admin's email address instead of blank
* PRO: Ability to switch from one field type to another
* PRO: Finished the 'Data from Entries' field
* PRO: Added the first overall report (daily submissions)
* PRO: Added two new form templates (more to come of course)
* PRO: Editable Submit button and Success message for editing entries
* PRO: Added option to sort by most fields when creating/editing the custom display settings

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