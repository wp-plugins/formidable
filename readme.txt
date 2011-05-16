=== Formidable Forms ===
Contributors: sswells
Donate link: http://strategy11.com/donate
Tags: admin, AJAX, captcha, contact, contact form, database, email, feedback, form, forms, javascript, jquery, page, plugin, poll, Post, spam, survey, template, widget, wpmu
Requires at least: 2.8
Tested up to: 3.1.1
Stable tag: 1.05.02

Quickly and easily build forms with a simple drag-and-drop interface and in-place editing.

== Description ==
Quickly and easily build forms with a simple drag-and-drop interface and in-place editing.
There are dozens of form-building plugins out there to create forms, but most are confusing and overly complicated. With Formidable, it is easy to create forms within a simple drag-and-drop interface. You can construct custom forms or generate them from a template. Shortcodes can be used as well as spam catching services.

= Features =
* Saves all responses to the database (even in the free version) for future retrieval, reports, and display in [Formidable Pro](http://formidablepro.com/ "Formidable Pro") Learn more at: http://formidablepro.com
* Integrates with WP reCAPTCHA and Akismet for Spam control
* Shortcode [formidable id=x] for use in pages, posts, or text widgets for WordPress version 2.8 and above.
* Alternatively use `<?php echo FrmEntriesController::show_form(2, $key = '', $title=true, $description=true); ?>` in your template
* Customize most HTML when editing the form (code for editing HTML when creating the form is soon to follow... and documentation too)
* Create forms from existing templates or add your own. A contact form template is included.
* Direct links available for previews and emailing surveys with and without integration with your current theme. Make these links pretty with [Pretty Link](http://blog.strategy11.com/prettylink "Pretty Link") integration
* Select an email address to send form responses under "Form Notification Options"
* Input default values into form fields with the option to clear when clicked
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

A. Try the following steps:

   1. Double check to make sure your email address is present and correct under “Advanced Form Options” at the bottom of your form editor page
   2. Make sure you are receiving other emails from your site (ie comment notifications, forgot password...)
   3. Check your SPAM box
   4. Try a different email address.
   5. Install WP Mail SMPT or another similar plugin and configure the SMTP settings
   6. If none of these steps fix the problem, let me know and I’ll try to help you find the bottleneck.

= Q. How do I edit the field name? =

A. The field and form names and descriptions are all changed with in-place edit. Just click on the text you would like to change, and it will turn into a text field.

= Q. Why isn’t the form builder page working after I updated? =

A. Try clearing your browser cache. As plugin modifications are made, frequent javascript and stylesheet changes are also made. However, the previous versions may be cached so you aren’t using the modified files. After clearing your cache and you’re still having issues, please let me know.

== Changelog ==
= 1.05.03 =
* Updated user role options to work more reliably with WP 3.1
* Added functionality for "Fit Select Boxes into SideBar" checkbox and field size in widget in free version
* Moved reCaptcha error message to individual field options
* PRO: Added "clickable" option for use in custom displays to make email addresses and URLs into links. ex `[25 clickable=1]`
* PRO: Added option to select the taxonomy type
* PRO: Updated form styling to work better in IE
* PRO: Updated emails to work with Data from entries checkbox fields
* PRO: Updated dependent Data from entries fields to work with checkboxes
* PRO: Adjusted [date] and [time] values to adjust for WordPress timezone settings
* PRO: Updated the way conditionally hidden fields save in the admin to prevent lingering dependencies
* PRO: Fixed link to duplicate entries
* PRO: Updated file upload indicator to show up sooner
* PRO: Updated referring URL and added tracking throughout the visit
* PRO: Added ajax delete to [deletelink] shortcode
* PRO: Updated admin only fields to show for administrators on the front-end
* PRO: Added more attributes to the [display-frm-entries] shortcode: limit="5", page_size="5", order_by="rand" or field ID, order="DESC" or "ASC"
* PRO: Fixed custom display bulk delete
* PRO: Updated WPMU copy features to work with WP 3.0+
* PRO: Switched the email "add/or" drop-down to check boxes
* PRO: Added box for message to be displayed if there are no entries for a custom display
* PRO: Moved styling options into a tab on the settings page
* PRO: Added limited "data from entries" options to the custom display "where" row. Entry keys or IDs can be used

= 1.05.02 =
* Fixed issue with PHP4 that was causing the field options to get cleared out and only show a "0" or "<" instead of the field
* Prevent javascript from getting loaded twice
* Updated stylesheets for better looking left aligned field labels. In the Pro version, setting the global labels to one location and setting a single field to another will keep the field description and error messages aligned.
* PRO: Fixed issue causing form to be hidden on front-end edit if it was set not to show with the success message
* PRO: Show the linked image instead of the url when a file is linked in a "just show it" data from entries field
* PRO: Added functionality for ordering by post fields in a custom display

= 1.05.01 = 
* PRO: Fix custom display settings for posts

= 1.05.0 =
* Moved a form widget from Pro into the free version
* Updated some templates with fields aligned in a row
* Moved error messages underneath input fields
* Added option to display labels "hidden" instead of just none. This makes aligning fields in a row with only one label easier
* Additional XHTML compliance for multiple forms on one 
* Removed the HTML5 required attribute (temporarily)
* Corrected the label position styling in the regular version
* A little UI clean up
* Added hook for recaptcha customizations
* PRO: Added custom post type support
* PRO: Added hierarchy to post categories
* PRO: Added a loading indicator while files are uploading
* PRO: Added a `[default-message]` shortcode for use in the email message. Now you can add to the default message without completely replacing it 
* PRO: Added default styling to the formresults shortcode, as well as additional shortcode options: `[formresults id=x style=1 no_entries="No Entries Found" fields="25,26,27"]`
* PRO: Added localizations options to calendar
* PRO: Fixed collapsible Section headings to work with updated HTML
* PRO: Added functionality to admin search to check data from entries fields
* PRO: Added start and end time options for time fields
* PRO: Added 'type' to `[frm-graph]` shortcode to force 'pie' or 'bar': `[frm-graph id=x type=pie]`
* PRO: Added post_id option to the `[frm-search]` shortcode. This will set the action link for the search form. Ex: `[frm-search post_id=3]`
* PRO: Fixed `[frm-search]` shortcode for use on dynamic custom displays. If searching on a detailed entry page, the search will return to the listing page.
* PRO: Updated post fields to work in "data from entries" fields

= 1.04.07 =
* Minor bug fixes
* PRO: Fixed bug preventing some hidden field values from being saved
* PRO: Removed PHP warnings some users were seeing on the form entries page

= 1.04.06 =
* Additional back-end XHTML compliance
* PRO: Fixed conditionally hidden fields bug some users were experiencing

= 1.04.05 =
* Added duplicate entry checks
* Added a checkbox to mark fields required
* Moved the duplicate field option into free version
* Show the success message even if the form isn't displayed with it
* Added option to not use dynamic stylesheet loading
* PRO: Added option to resend email notification and autoresponse
* PRO: Fixes for editing forms with unique fields
* PRO: Fixes for editing multi-paged forms with validation errors
* PRO: Fixes for multiple multi-paged form on the same page
* PRO: Added linked fields into the field drop-downs for inserting shortcodes and sending emails
* PRO: Added field calculations
* PRO: Allow hidden fields to be edited from the WordPress admin
* PRO: Allow sections of fields to be hidden conditionally with the Section Header fields
* PRO: Added user_id option to the `[frm-graph]` shortcode
* PRO: Updated the custom display settings interface

= 1.04.04 =
* Switched to the Google version of reCAPTCHA to no longer require an extra plugin. IMPORTANT: Please check that your reCAPTCHAs are still working. If not, you will need to go to http://www.google.com/recaptcha and either migrate your old keys or get new ones.
* Updated Akismet protection to work more accurately
* Added Portuguese translation thanks to Abner Jacobsen. He also pointed out an awesome plugin to help with translating: [Codestyling Localization](http://wordpress.org/extend/plugins/codestyling-localization/ "Codestyling Localization]")
* PRO: Added unique field validation
* PRO: Added admin-only fields
* PRO: Updated javascript for more speed and allow more than two dependent data from entries fields (makes Country/State/Region/City selectors possible if you do the data population)
* PRO: Added success message styling
* PRO: Fix bug preventing all image sizes from getting created
* PRO: Changed the name of the scale field from "10radio" to "scale". This may affect users with add-on plugins using this name
* PRO: Added `[deletelink]` option for use in custom HTML
* PRO: Added `not_equal` parameter for conditionally displaying content. ie `[if XX not_equal="Blah Blah"]stuff[/if XX]`å

= 1.04.03 = 
* Load styling before any forms are loaded
* Fixed in-place edit in IE (finally! Sorry guys!)
* PRO: Include styling on multi-paged forms
* PRO: Allow floating decimals in the number field
* PRO: Don't load jQuery CSS in the admin
* PRO: Moved javascript for hidden fields to the footer (wp_footer)
* PRO: Added field options to the user id and hidden fields

= 1.04.02 = 
* PRO: Fixed drop-down hidden field dependencies
* PRO: Added options to the time field (12 or 24 hours, minute step)

= 1.04.01 =
* Changed the ID of the select, user id, and hidden fields to "field_" plus field key
* Moved the "Edit HTML" button out of the "Advanced Form Options" area
* Only load css when needed
* Jump to form on page after errors
* Added option to use [admin_email] in the "Email Form Responses to" line to save time for those who only want to change their email address in one place.
* Free only: If no email address is inserted, the email will be sent to the admin email
* PRO: Added Time field
* PRO: Added option to use posted data in the redirect URL
* PRO: Added option to set the range for the scale field
* PRO: Added option to attach file uploads to email notifications
* PRO: Only load date javascript when a date field has been loaded
* PRO: Moved file uploads to uploads/formidable
* PRO: Optimized the css file by writing it to uploads/formidable/css instead of loading a php file
* PRO: Added styling for field description, and gradients and shadows on the submit button
* PRO: Updated default values to work with radio, check box, and select fields.
* PRO: Fixed front-end reports to work in IE and Chrome
* PRO: Added option to dynamically get stats for the currently logged-in user with the `[frm-stats]` shortcode ie. `[frm-stats id=x user_id=current]`
* PRO: Added 'round' option to frm-stats to specify the number of decimal places to show ie `[frm-stats id=x round=2]`
* PRO: Added 'response_count' to frm-graph to increase the maximum number of responses for a text field ie `[frm-graph id=x response_count=10]`
* PRO: Added 'truncate' and 'truncate_label' to frm-graph to adjust the number of characters shown for the graph title and the labels of the graph ie `[frm-graph id=x truncate=40 truncate_label=7]`
* PRO: Added fields to the drop-down list for limiting submissions. Now you can "Allow Only One Entry for Each" email address or whatever other field you may have in your form.
* PRO: Change the hidden User ID field to a drop-down for admins editing entries in the back-end
* PRO: Removed the sanitizing from the custom field name to make it possible to use any custom field name desired
* PRO: Update to check for calendar css in the uploads/formidable/css folder before using it from https://ajax.googleapis.com
* PRO: Added options to number field to specify the range and steps used in the HTML5 field
* PRO: More form options are exported in templates 
* PRO: Fixed bug preventing fields with an ' or " from getting copied correctly when duplicating and creating/exporting templates
* PRO: Post categories now work as a drop-down
* PRO: Limit form entries to one per [whatever field here]. For example, only allow one submission per email address.
* Other bug fixes and optimization

= 1.04.0 =
* Added icon link on post/page editor for inserting forms
* Added parameters to show individual radio/checkbox options in the custom HTML using the `[input]` tag. example: `[input opt=1]` where opt is the option order. Also hide the labels with `[input label=0]`. Now grid fields are much easier.
* PRO: Added post integration! Pro forms can now be used for creating and editing posts
* PRO: Added a calendar option to the custom display, allowing entries to be displayed in a monthly calendar
* PRO: Added page_id parameter to `[frm-entry-links]` shortcode to remove the requirement to place entry list on the same page as the form for editing entries
* PRO: Updated email, url, and number fields to use HTML5
* PRO: Updated custom displays to work with the `[frm-search]` shortcode
* PRO: Named submit buttons according the the page break name if using multi-paged forms
* PRO: Added boxes for before/after custom display content box for non-repeating content
* PRO: Use entry values in the success message
* PRO: Switch out the Rich Text Editor for a text box if users are on a mobile device
* PRO: Fixed the confirmation options to work when editing an entry
* PRO: Added default value for [time]
* PRO: Fixed admin search
* PRO: Fixed field drop-down on custom display page to work on the Visual tab

= 1.03.03 =
* Added options to allow users other than admins to access Formidable
* Added uninstall button
* Fixed multiple submissions for pages with multiple forms
* PRO: Added [frm-graph] shortcode for front-end graphical reports! Default values: `[frm-graph id=x include_js=1 colors="#EF8C08,#21759B,#1C9E05" bg_color="#FFFFFF" height=400 width=400]`. Show multiple fields with `[frm-graph id="x,y,z"]`
* PRO: Added "value" parameter to the frm-stats shortcode for counting number of entries with specified value `[frm-stats id=8 value="Hello" type=count]`
* PRO: Added a field drop-down for searching specific fields on the entries page
* PRO: Added option to allow users to edit any entry instead of only their own and other user-role options
* PRO: Added calendar date format option on the Formidable Settings page
* PRO: Changed "entry_id" in the "display-frm-data" to accept multiple entry IDs. ex: `[display-frm-data id=x entry_id="34,35,36"]`
* PRO: Added "equals" option to if statements. ex: `[if 283 equals=hello]show this if the field with id 283 equals hello[/if 283]`

= 1.03.02 =
* Fixed admin pagination to navigate correctly with the arrow
* Fixed most Internet Explorer admin issues
* PRO: Added option to only show certain fields in a shortcode `[formidable id=x fields="field1,field2,field3"]`
* PRO: Added a user_id parameter to the frm-stats shortcode to get only the averages and totals for that user `[frm-stats id=8 user_id=19]`
* PRO: Fixed custom display to correctly show a single entry for all users.
* PRO: Fixed bug that prevented some of the dynamic default values from getting replaced if the was no value to replace it with
* PRO: Fixed bug causing "Array" to be shown in the email notification if more than one check box was selected
* PRO: Fixed "Data from Entries" check box javascript and display on entries page
* PRO: Fixed new fields to default to position set on the Formidable settings
* PRO: Updated country field in the User Information template
* PRO: Fixed hidden field to not lose its value if updated from the admin
* PRO: If using `[frm-entry-links]` with type=collapse, the first year and month now default to open and fixed div uneveness 
* PRO: Corrected values when using a "Data from Entries" drop down from an image url field to show the url
* PRO: Editable 'You have already submitted that form' message
* Other fixes

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
* PRO: Added auto increment default value `[auto_id start=1]`
* PRO: Added a field width option to the sidebar widget
* PRO: Added a rich text editor to the custom display page
* PRO: Added an edit link shortcode for use in custom displays `[editlink]`
* PRO: Added a drop-down select to insert the field shortcodes for custom displays
* PRO: Added year range option to date fields
* PRO: Fixed bug causing collapsed section to open and immediately close if there are multiple forms on the same page
* PRO: Fixed bug preventing styling options from saving for some users
* PRO: Added styling options: disable submit button styling, field border style and thickness, form border color and thickness, submit button border and background image
* PRO: Added read-only fields with option to enable all fields in the shortcode [formidable id=x readonly=disabled]
* PRO: Added entry_id option to form shortcode `[formidable id=x entry_id=x]`. The entry_id can either be the number of the entry id or use "last" to get the last entry.
* PRO: Added taxonomy support with a tags field
* PRO: Added "where" options to custom displays so only specified entries will be shown.
* PRO: Fixed bug preventing file upload fields from accurately requiring a file
* PRO: Added type=collapsible to the frm-entry-links shortcode for a collapsible archive list
* PRO: Added referrer and user action tracking that is recorded with each entry submitted

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