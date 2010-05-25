=== Formidable Forms ===
Contributors: sswells
Donate link: http://blog.strategy11.com/donate
Tags: WPMU, widget, Post, plugin, template, contact, contact form, form, forms, captcha, spam, email, database, admin, AJAX, javascript, jquery, poll, survey, feedback
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 1.03.01

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
* Create forms from existing templates or add your own. A contact form template is included.
* Direct links available for previews and emailing surveys with and without integration with your current theme. Make these links pretty with [Pretty Link](http://blog.strategy11.com/prettylink "Pretty Link") integration
* Select an email address to send form responses under "Form Notification Options"
* Input default values into form fields with the option to clear when clicked
* Saves all responses to the database for future retrieval, reports, and display in [Formidable Pro](http://formidablepro.com/ "Formidable Pro")
* PHP ninjas can display data in templates using functions in FrmApiController. However, there is currently no documentation for these functions.

== Installation ==
1. Upload `formidable` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu
3. Go to the Formidable 'Settings' menu to select a front-end preview page.
4. Create a new custom form or use the existing Contact Form template.
5. Use shortcode [formidable id=x] in pages, posts, or text widgets. (Requires WordPress version 2.8 or higher)

== Screenshots ==
1. Form creation page


== Frequently Asked Questions ==
= Q. Why aren’t I getting any emails? =

A. As of version 1.02.01, I believe any obstacles in the code have been removed. Try the following steps:

   1. Double check to make sure your email address is present and correct under “Advanced Form Options” at the bottom of your form editor page
   2. Check your SPAM box
   3. Try a different email address.
   4. Install WP Mail SMPT
   5. If none of these steps fix the problem, let me know and I’ll try to help you find the bottleneck.

= Q. How do I make a field required? =

A. I have tried to keep the Formidable user interface as quick and simple as possible. Just click on the star next to the field you would like required.

= Q. How do I edit the field name? =

A. The field and form names and descriptions are all changed with in-place edit. Just click on the text you would like to change, and it will turn into a text field. Don’t forget to hit save!

= Q. Why isn’t the form builder page working right after I updated? =

A. Try clearing your browser cache. As I make plugin modifications, I frequently change javascript and stylesheets. However, the previous versions may be cached so you aren’t using the modified files. After clearing your cache and you’re still having issues, please let me know.

== Changelog ==

= 1.03.01 =
* PRO: Fixed auto-update for WP 2.9

= 1.03.0 =
* Added the option of showing the form with the success message or not
* Added settings options for default messages and option to exclude the stylesheet from your header
* PRO: Added auto responder and made the notification email customizable
* PRO: Added options to redirect or render content from another page
* PRO: Added option to only allow only submission per user, IP, or cookie
* PRO: Added option to export a custom template as a PHP file so it can be used on other sites
* PRO: Added option to specify alternate folder from which to import templates
* PRO: Added number field
* PRO: Added auto increment default value [auto_id start=1]
* PRO: Added a field width option to the sidebar widget
* PRO: Added a rich text editor to the custom display page
* PRO: Added an edit link shortcode for use in custom displays [editlink]
* PRO: Added a drop-down select to insert the field shortcodes for custom displays
* PRO: Added year range option to date fields
* PRO: Fixed bug causing collapsed section to open and immediately close if there are multiple forms on the same page
* PRO: Fixed bug preventing styling options from saving for some users
* PRO: Added styling options: disable submit button styling, field border style and thickness, form border color and thickness, submit button border and background image
* PRO: Added read-only fields with option to enable all fields in the shortcode [formidable id=x readonly=disabled]
* PRO: Added entry_id option to form shortcode [formidable id=x entry_id=x]. The entry_id can either be the number of the entry id or use "last" to get the last entry.
* PRO: Added taxonomy support with a tags field
* PRO: Added "where" options to custom displays so only specified entries will be shown.
* PRO: Fixed bug preventing file upload fields from accurately requiring a file
* PRO: Added type=collapsible to the frm-entry-links shortcode for a collapsible archive list

= 1.02.01 =
* Emailer now works for everyone! (hopefully)
* Optionally Reset HTML. Just clear out the box for the HTML for that field and hit update.
* PRO: Fixed collapsable section to use correct default HTML. 
* PRO: Only call rich text javascript on entries pages
* PRO: A few small reports modifications. Report for the User ID field will show the percentage of your users who have submitted the form if you are allowing edits with only one submission per user.

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