=== Debug Bar Post Types ===
Contributors: jrf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=995SSNDTCVBJG
Tags: debugbar, debug-bar, Debug Bar, Post Types, Debug Bar Post Types, Custom Post Type, Custom Post Types, CPT, cpts
Requires at least: 3.4
Tested up to: 4.9
Stable tag: 2.0.0
Requires PHP: 5.2.4
Depends: Debug Bar
License: GPLv2

Debug Bar Post Types adds a new panel to the Debug Bar with detailed information about registered post types. Requires "Debug Bar" plugin.

== Description ==

Debug Bar Post Types adds a new panel to the Debug Bar that displays detailed information about the registered post types for your site.

= Important =

This plugin requires the [Debug Bar](https://wordpress.org/plugins/debug-bar/) plugin to be installed and activated.

Also note that this plugin should be used solely for debugging and/or in a development environment and is not intended for use on a production site.

***********************************

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-post-types) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-post-types) or in the [GitHub repository](https://github.com/jrfnl/Debug-Bar-Post-Types/issues).



== Frequently Asked Questions ==

= Can it be used on live site ? =
This plugin is only meant to be used for development purposes, but shouldn't cause any issues if run on a production site.


= What are post types ? =
>WordPress can hold and display many different types of content. A single item of such a content is generally called a post, although post is also a specific post type. Internally, all the post types are stored in the same place, in the wp_posts database table, but are differentiated by a column called post_type.
>
>WordPress 3.0 gives you the capability to add your own custom post types and to use them in different ways.
[More information in the Codex](http://codex.wordpress.org/Post_Types)


= Why won't the plugin activate ? =
Have you read what it says in the beautifully red bar at the top of your plugins page ? As it says there, the Debug Bar plugin needs to be active for this plugin to work. If the Debug Bar plugin is not active, this plugin will automatically de-activate itself.


== Changelog ==

= 2.0.0 =
_Release date: 2018-01-22_

* Improved compatibility with PHP 7.2.
* Updated the pretty print dependency to v1.8.0.
* Refactoring of part of the code base. No functional changes.
* General housekeeping.
* Added minimum PHP requirement header. (PHP 5.2.4, in line with WP itself.)
* Tested & found compatible WP 4.9.

= 1.4.0 =
_Release date: 2017-07-10_

* Improved usability of the admin notice in case the Debug Bar plugin is not active.
* The plugin will now add itself to the list of "recently active" plugins if it self-deactivates bcause the Debug Bar plugin is not active.
* Defer to just in time loading of translations for WP > 4.5.
* Updated the pretty print dependency to v1.7.0.
* Some code refactoring.
* Minor housekeeping.
* The minimum supported WP version is now 3.4, in line with the 0.9 version of the Debug Bar.
* Tested & found compatible WP 4.8.

= 1.3.0 =
_Release date: 2016-04-12_

* Hard-coded the text-domain for better compatibility with [GlotPress](https://translate.wordpress.org/projects/wp-plugins/debug-bar-post-types).
* Make loading of text-domain compatible with use of the plugin in the `must-use` plugins directory.
* Fix very minor layout inconsistency in combination with Twenty-Sixteen theme.
* Updated the pretty print class to v1.6.0.
* Minor housekeeping.
* Tested & found compatible with WP 4.5

= 1.2.2 =
_Release date: 2015-12-26_

* Fix weird table layout on front-end in combination with Twenty-Sixteen theme.
* Minor tidying up.

= 1.2.1 =
_Release date: 2015-12-05_

* Updated pretty print class & minor tidying up.
* Tested & found compatible with WP 4.4

= 1.2 =
_Release date: 2015-04-18_

* Added a count of the registered Custom Post Types to the top of the page.
* Split the display of Post Type properties. Standard post type properties are now shown first. Non-standard properties added by Custom Post Types are shown in a separate table.
* Added a table showing the defined labels for Post Types.
* Updated the pretty print class which now allows for limiting of the recursion depth when displaying the property values - props [Joy](https://wordpress.org/support/profile/joyously) for reporting issues with the Easy Post Types plugin. These should now be solved with this update.
* Tested & found compatible with WP 4.2
* Minor tidying up
* Updated language files
* Updated screenshots

= 1.1.1 =
_Release date: 2014-09-05_

* Fix compatibility with the [Plugin Dependencies](https://wordpress.org/plugins/plugin-dependencies/) plugin
* Tested & found compatible with WP 4.0

= 1.1 =
_Release date: 2013-12-02_

* Minor tidying up
* Moved pretty print class to separate repository as several plugins are using it now.

= 1.0 =
_Release date: 2013-11-29_

* Initial release


== Upgrade Notice ==

= 1.2 =
* Upgrade highly recommended - fix for fatal error in combination with Easy Post Types plugin.

= 1.1 =
* Upgrade highly recommended - multi-plugin compatibility issue

= 1.0 =
* Initial release


== Installation ==

1. Install Debug Bar if not already installed (https://wordpress.org/plugins/debug-bar/)
1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the "Plugins" menu in WordPress.


== Screenshots ==
1. Debug Bar Post Types - Standard Post Type Properties view
1. Debug Bar Post Types - Custom Post Type Properties view
1. Debug Bar Post Types - Capabilities view
1. Debug Bar Post Types - Defined labels view

