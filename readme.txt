=== EDD Wish Lists ===

Plugin URI: http://easydigitaldownloads.com/extensions/edd-wish-lists?ref=166
Author: Andrew Munro, Sumobi
Author URI: http://sumobi.com/

Requires Easy Digital Downloads 1.9 or greater

== Demo ==
http://edd-wish-lists.sumobithemes.com/

== Documentation ==
http://sumobi.com/docs/edd-wish-lists/

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

After activation, configure the plugin from downloads -> settings -> extensions

== Changelog ==

If you have made modifications to the wish-list-view.php template, make sure edd_wl_wish_list_item_purchase() is renamed to edd_wl_item_purchase()

= 1.0.6 =
* Fix: Compatibility with EDD v1.9.9
* Fix: Modified a redirect action so it doesn't conflict with other plugin redirects 
* Fix: Added a default value for the $id in the edd_wl_the_title() function.

= 1.0.5 =
* Fix: shortcodes weren't showing on page templates when Wish Lists plugin was active

= 1.0.4 =
* New: edd_wl_allowed_post_types() function
* New: edd_wl_item_title_permalink filter hook
* Fix: issue where add to cart button wasn't working on wish list page when variable priced download was used

= 1.0.3 =
* Fix: when no text is shown in settings, don't show default text on front-end
* Fix: filter with same name as another
* Fix: removed options passed into the edd_wl_delete_list_link function on the wish-list-edit.php template. These were overriding (as they should) the plugin's options.
* Fix: link size CSS class names.
* Tweak: small CSS adjustment for when add to wish list link does not have any text. The icon now aligns better
* Tweak: Moved text from delete list modal into the edd_wl_messages() function

= 1.0.2 =
* New: added email sharing as option in extension settings
* New: added filter to remove delete link
* Tweak: improved script handling
* Tweak: JS
* Tweak: CSS
* Tweak: improved script loading
* Tweak: improved handling of list queries
* Tweak: admin settings improvements
* Tweak: code refactoring
* Tweak: drastically reduced code in view and wish lists templates
* Tweak: Create new list button is now hidden when create page is not selected in settings
* Tweak: Edit links are now hidden when no edit page selected in options

= 1.0.1 =
* Fix: PHP 5.2 Compatibility
* Tweak: Different list creation messages for guest/logged in users

= 1.0 =
* Initial release