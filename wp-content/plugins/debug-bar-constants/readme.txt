=== Debug Bar Constants ===
Contributors: jrf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=995SSNDTCVBJG
Tags: debugbar, debug-bar, Debug Bar, Constants, Debug Bar Constants
Requires at least: 3.4
Tested up to: 4.9
Stable tag: 2.0.0
Requires PHP: 5.2.4
Depends: Debug Bar
License: GPLv2

Debug Bar Constants adds three new panels to the Debug Bar that display the defined WP and PHP constants for the current request.

== Description ==

Debug Bar Constants adds three new panels to the Debug Bar that display the defined constants available to you as a developer for the current request:

*	WP Constants
*	WP Class Constants
*	PHP Constants

= Important =

This plugin requires the [Debug Bar](https://wordpress.org/plugins/debug-bar/) plugin to be installed and activated.

Also note that this plugin should be used solely for debugging and/or in a development environment and is not intended for use on a production site.

***********************************

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-constants) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-constants) or in the [GitHub repository](https://github.com/jrfnl/Debug-Bar-Constants/issues).



== Frequently Asked Questions ==

= Can it be used on live site ? =
**PLEASE DON'T!** Amongst the defined constants are your database credentials, so you really do not want to do this.
This plugin is only meant to be used for development purposes.


= What are constants ? =
[From PHP.net:](http://php.net/language.constants)
> A constant is an identifier (name) for a simple value. As the name suggests, that value cannot change during the execution of the script. A constant is case-sensitive by default. By convention, constant identifiers are always uppercase.

> Like super globals, the scope of a constant is global. You can access constants anywhere in your script without regard to scope. For more information on scope, read the manual section on [variable scope](http://php.net/language.variables.scope).


= I don't see my constants in the WP Constants list. What gives ? =
Congratulations! Sounds like you're practicing lean programming (or something is going wrong... ;-) ).

The constants you see are the ones available in the current request. If you define constants in a conditionally included file - for instance you only include the file when on a certain page -, these constants will not be available if the conditions have not been met.


=  The number of constants is different on each page/most pages. How come ? =
See the previous question.


= What are class constants ? =
[From PHP.net:](http://php.net/language.oop5.constants)
>  It is possible to define constant values on a per-class basis remaining the same and unchangeable. Constants differ from normal variables in that you don't use the $ symbol to declare or use them.

> The value must be a constant expression, not (for example) a variable, a property, a result of a mathematical operation, or a function call.


= Why do plugins and themes use class constants instead of normal constants ? =
It's good coding practice to avoid littering the global namespace with your own variables and constants. This is a good way to avoid this.


= Why is it useful to have insight into the defined class constants ? =
If your plugin/theme interacts with other plugins and/or themes, you may want to use their constants.
Example: A plugin might have their version number saved as a class constant (good practice!). On your part, your plugin may have been set up to only work if the related plugin has been upgraded to version x. In that case, you may want to check other plugins version number before your plugin interacts with it.

Don't forget to always check whether the class constant exists before you use it! It may not be available on all pages and surely not on all WP installs.
`
if( defined( 'class_name::constant_name' ) ) {
	// Your code here
}
`

= I don't see my class in the WP Class Constants list. What gives ? =

*	Depending on how lean you've programmed your plugin/theme, your class may not be included (and therefore not be declared) for the page you are viewing.
*	The name of your class may be the same as the name of one of the native/extension PHP classes. PHP is normally configured with a limited number of Extensions. A large number of the available extensions declare classes when they are loaded and the WP Class Constants list is filtered for these classes. It's good practice not to use the names of those PHP native/extension classes for your own classes. Try renaming your class. If that doesn't work, please [let me know](https://github.com/jrfnl/Debug-Bar-Constants/issues) and I'll have a look at it.


= Why are *all* native/extension PHP Classes filtered out - and not just the ones for the extensions loaded on my server -, you ask ? =
Easy: It's bad practise to name your class the same as one of the PHP native/extension classes.

In general, when you're developing a plugin/theme, you are developing for an unknown group of other people with unknown server configurations - including which extensions are(n't) installed -, so you should always make sure that your class will not interfere with any of the PHP native/extension classes.


= I see some PHP classes and their constants in the WP Class Constants list. What gives ? =
I've tried to exclude all PHP classes from this list, however, I might have missed some. Also some new extensions and/or classes may have been introduced in PHP since the last version of this plugin was released.
Please [let me know](https://github.com/jrfnl/Debug-Bar-Constants/issues) which one(s) you found and I'll add it/them to the exclusion list.


= Why won't the plugin activate ? =
Have you read what it says in the beautifully red bar at the top of your plugins page ? As it says there, the Debug Bar plugin needs to be active for this plugin to work. If the Debug Bar plugin is not active, this plugin will automatically de-activate itself.


== Changelog ==

= 2.0.0 =
_Release date: 2018-01-22_

* Improved compatibility with PHP 7.2.
* Improved compatibility with the upcoming v 0.10.0 of the Debug Bar plugin.
* I18n/I10n: Improved text strings for localization.
* Updated the pretty print dependency to v1.8.0.
* Refactoring of part of the code base. No functional changes.
* General housekeeping.
* Added minimum PHP requirement header. (PHP 5.2.4, in line with WP itself.)
* Tested & found compatible WP 4.9.

= 1.7.0 =
_Release date: 2017-07-10_

* Improved usability of the admin notice in case the Debug Bar plugin is not active.
* The plugin will now add itself to the list of "recently active" plugins if it self-deactivates bcause the Debug Bar plugin is not active.
* Defer to just in time loading of translations for WP > 4.5.
* Updated the pretty print dependency to v1.7.0.
* Minor housekeeping.
* The minimum supported WP version is now 3.4, in line with the 0.9 version of the Debug Bar.
* Tested & found compatible WP 4.8.

= 1.6.1 =
_Release date: 2016-04-15_

* Get the leaner loading of text domains right (at last).

= 1.6.0 =
_Release date: 2016-04-12_

* Hard-coded the text domains for compatibility with [GlotPress](https://translate.wordpress.org/projects/wp-plugins/debug-bar-constants).
* Make loading of text-domain compatible with use of the plugin in the `must-use` plugins directory.
* Updated the pretty print class to v1.6.0.
* Minor housekeeping.
* Tested & found compatible WP 4.5.

= 1.5.0.3 =
_Release date: 2016-01-13_

* Fix very minor layout inconsistency in combination with Twenty-Sixteen theme.
* Updated the pretty print class to v1.5.2.

= 1.5.0.2 =
_Release date: 2016-01-10_

* Fix weird table layout on front-end in combination with Twenty-Sixteen theme.
* Leaner loading of text domains.

= 1.5.0.1 =
_Release date: 2015-12-07_

* Fix typo.

= 1.5 =
_Release date: 2015-12-05_

* Updated PHP Classes list for PHP7.
* Fixed (some) links to PHP manual resulting in 404s.
* Tested & found compatible with WP 4.4.

= 1.4 =
_Release date: 2015-04-18_

* Updated the pretty print class to v1.5.0 which now allows for limiting of the recursion depth.
* Tested & found compatible with WP 4.2.
* Minor tidying up.
* Updated language files.

= 1.3.1 =
_Release date: 2014-09-05_

* Fix compatibility with the [Plugin Dependencies](https://wordpress.org/plugins/plugin-dependencies/) plugin.
* Tested & found compatible with WP 4.0.

= 1.3 =
_Release date: 2013-12-02_

* Moved pretty print class to separate repository as several plugins are using it now.

= 1.2.1.2 =
_Release date: 2013-11-30_

* Fixed minor HTML validation issue.
* Fixed auto-disable message display in case the Debug Bar Plugin is not activated.
* Changed style and script file names to comply with current guidelines (.min).
* Tested with WP 3.8-beta1.

= 1.2.1.1 =
_Release date: 2013-10-01_

* Compliance with the [WordPress coding standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).
* Some minor code cleanup inspired by some suggestions by [Maik Penz](https://github.com/goatherd).

= 1.2.1 =
_Release date: 2013-05-27_

* Minor fix to comply with strict standards. Thanks [Azizur Rahman for reporting](https://wordpress.org/support/topic/declaration-of-debug_bar_constantsinit-should-be-compatible-with-debug_bar_p).

= 1.2 =
_Release date: 2013-05-05_

* [New!] Added a panel for Class Constants.
* [Fix] Some left over references to functions not in this plugin.
* Headers for the PHP Constants now link to their PHP manual page.
* Moved more output rendering to the pretty-output class and applied a higher level of abstraction.

= 1.1 =
_Release date: 2013-04-30 / not released_

* [New!] Added auto-deactivation if the Debug Bar plugin is not active.
* [Fix] Removed js debug alert (oops..).
* [Fix] Adjusted the sorting to be case-insensitive.
* [Fix] Some small-HTML/CSS tweaks.
* Added object output helper method.

= 1.0 =
_Release date: 2013-04-28_

* Initial release.


== Upgrade Notice ==

= 1.5 =
* Updated PHP Classes list for PHP7.

= 1.4 =
* Upgrade highly recommended - multi-plugin compatibility issue.

= 1.3 =
* Upgrade highly recommended - multi-plugin compatibility issue.

= 1.2.1.2 =
* Small fixes, nothing exciting.

= 1.2 =
* New! panel for Class Constants. This release also fixes a number of bugs.

= 1.0 =
* Initial release.


== Installation ==

1. Install Debug Bar if not already installed (https://wordpress.org/plugins/debug-bar/).
1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the "Plugins" menu in WordPress.

Don't use this plugin on a live site. This plugin is **only** intended to be used for development purposes.


== Screenshots ==
1. Debug Bar displaying WP Constants
1. Debug Bar displaying WP Class Constants
1. Debug Bar displaying PHP Constants

