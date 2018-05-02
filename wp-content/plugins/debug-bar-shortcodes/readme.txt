=== Debug Bar Shortcodes ===
Contributors: jrf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=995SSNDTCVBJG
Tags: debugbar, debug-bar, Debug Bar, Shortcodes, Debug Bar Shortcodes, shortcode list, list shortcodes, shortcodes reference
Requires at least: 3.6
Tested up to: 4.4
Stable tag: 2.0.3
Depends: Debug Bar
License: GPLv2

Debug Bar Shortcodes adds a new panel to the Debug Bar that displays the registered shortcodes for the current request.

== Description ==

Debug Bar Shortcodes adds a new panel to the [Debug Bar](https://wordpress.org/plugins/debug-bar/) that displays the registered shortcodes for the current request.

Additionally it will show you:

* Which function/method is called by the shortcode
* Whether the shortcode is used on the current post/page/post type and how (only when on singular)
* Any additional information available about the shortcode, such as a description, which parameters it takes, whether or not it is self-closing.
  _Want to control the additional information displayed about your own shortcodes ? You can! See the [FAQ](https://wordpress.org/plugins/debug-bar-shortcodes/faq/) for more info._
* Find out all pages/posts/etc on which a shortcode is used

This plugin is compatible with the [LRH-Shortcode list](https://wordpress.org/plugins/lrh-shortcode-list/) and the [Shortcake/Shortcode UI](https://wordpress.org/plugins/shortcode-ui/) plugins. Additional information about a shortcode provided to these plugins will be taken into account and made available through this plugin too.


= Why is it useful to have insight into the shortcodes ? =

There are a number of typical uses I can think of:

* If you are a **_blog author_**:
	- to know which shortcodes you can use in your posts/pages.
	- if you switch plugins or remove a plugin, to know in which posts/pages you need to remove/replace old shortcodes

* If you are a **_web designer / web master_**:
	- if you switch plugins or remove a plugin, to know in which posts/pages you need to remove/replace old shortcodes.
	- to know which shortcodes you can use in theme files.
	- to avoid name conflicts for website specific shortcodes.

* If you are a **_developer_**:
	- to avoid name conflicts with shortcodes registered by other plugins/themes.
	- to check whether your shortcode registers properly and whether the conditionals are applied correctly.


= Important =

This plugin requires the [Debug Bar](https://wordpress.org/plugins/debug-bar/) plugin to be installed and activated.

= Credits =
* The additional information functionality is inspired by [LRH-Shortcode list](https://wordpress.org/plugins/lrh-shortcode-list/) and [Shortcode reference](https://wordpress.org/plugins/shortcode-reference/).
* The finding of shortcode uses throughout the site is inspired by [TR All Shortcodes](https://wordpress.org/plugins/tr-all-shortcodes/)


------------------

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-shortcodes) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-shortcodes) or in the [GitHub repository](https://github.com/jrfnl/Debug-Bar-Shortcodes/issues).



== Frequently Asked Questions ==

= Can it be used on a live site ? =
This plugin is only meant to be used for development purposes, but shouldn't cause any issues if run on a production site.


= What are shortcodes ? =
> A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.
[Source](http://en.support.wordpress.com/shortcodes/)

For more information about using shortcodes in WordPress:
-	[WP Codex on shortcodes](http://codex.wordpress.org/Shortcode)
-	[WP Codex on the Shortcode API](http://codex.wordpress.org/Shortcode_API)


= Why is my shortcode not listed ? =

There are two possibilities here:

* Either your shortcode has [not been properly registered](http://codex.wordpress.org/Function_Reference/add_shortcode) using the ShortCode API.
* Or your shortcode might only be registered conditionally and the current page does not meet those conditions.


= The number of shortcodes differs depending on the requested page. How come ? =
See the previous answer.


= I'm using shortcode *abc* in page *xyz* and it doesn't show as used! =

To determine whether a shortcode is used in a page, only the _**post content**_ is evaluated. If you add content to the page using shortcodes in other areas (for example: widgets) or via the theme, those uses will not be recognized.


= Can I use these shortcodes in the theme I'm building ? =
Generally speaking you can. However, don't forget to always [check whether the shortcode is registered](http://codex.wordpress.org/Function_Reference/shortcode_exists) before you use it! It may not be available on all pages and surely not on all WP installs.
`
if ( shortcode_exists( 'shortcode' ) ) {
	/* Your code here */
	// echo do_shortcode( 'some content containing a [shortcode /]' );
}
`

= I'm a developer and would like to enrich the information displayed by this plugin about my shortcode. =
I've tried to make this as easy and painless as possible.

Just add a filter to enrich the information this plugin has about your shortcode. The easiest way is to use the `db_shortcodes_info_{shortcode}` filter which will only be applied to your shortcode.
`
add_filter( 'db_shortcodes_info_{your_shortcode}', 'filter_my_shortcode_info' );
function filter_my_shortcode_info( $info ) {
	// enrich the object
	return $info;
}
`

The `$info` object you receive and are expected to return will contain the currently known information about the shortcode.

`$info` is expected to contain (a selection of) the following parameters:
`stdClass(
	$name         = (string) 'Friendly name for your shortcode',
	$description  = (string) 'Description of your shortcode',
	$self_closing = (bool) true/bool, // whether the shortcode is self-closing
	$parameters   = array(
		'required'		=> array(
			(string) 'attribute_name'		=> (string) 'attribute description',
		),
		'optional'		=> array(
			(string) 'attribute_name'		=> (string) 'attribute description',
		),
	),
	$info_url     = '',
)
`

If you happen to already provide similar information using the `sim_{shortcode}` filter for the [LHR-Shortcode list](https://wordpress.org/plugins/lrh-shortcode-list/) plugin, no need to do anything extra, that information will be picked up by this plugin.

Similarly, if you provide information for the [Shortcake/Shortcode UI](https://wordpress.org/plugins/shortcode-ui/) feature plugin, that information will be used automatically to enrich the available information.


= Hang on - the filter behaviour has changed ?!? =
In version 1.0 of the plugin `$info` variable passed to the filter was an array. This has changed in version 2.0.

I'm aware that this is a backward compatibility break, but I've done some quite extensive searches and considering I did not find any plugin using the filter (yet), I decided this backward compatibility break would have little to no effect and therefore would be safe to implement.

If you *did* already have a filter in place, sorry I didn't find your plugin/theme! Not to worry though, I've tried to make it really easy to upgrade your code.
First off, you'll need to change the `add_filter()` hook in code and your function signature to now received two variables. The first variable will be the new object, but the second variable will still be an array in the format which was passed in 1.0 so you can continue to use that in your function to enrich the information.
Secondly, as all this plugin uses are the properties of the object, you can just cast your array to an object in the return and it'll work again.

Old code for v1.0:
`
add_filter( 'db_shortcodes_info_{your_shortcode}', 'filter_my_shortcode_info' );
function filter_my_shortcode_info( $info ) {
	// enrich the array
	return $info;
}
`

Updated code for v2.0:
`
add_filter( 'db_shortcodes_info_{your_shortcode}', 'filter_my_shortcode_info', 10, 2 );
function filter_my_shortcode_info( $info_object, $info ) {
	// enrich the array
	return (object) $info;
}
`


= Why won't the plugin activate ? =
Have you read what it says in the beautifully red bar at the top of your plugins page ? As it says there, the Debug Bar plugin needs to be active for this plugin to work. If the Debug Bar plugin is not active, this plugin will automatically de-activate itself.


== Changelog ==

= 2.0.3 (2016-04-29) =
* Make loading of text-domain compatible with use of the plugin in the `must-use` plugins directory.
* Minor housekeeping.
* Tested & found compatible WP 4.5.

= 2.0.2 (2016-01-10) =
* Fix spinner for ajax request which had stopped working since WP 4.2.

= 2.0.1 (2015-12-26) =
* Fix weird table layout on front-end in combination with Twenty-Sixteen theme.

= 2.0 (2015-12-14) =
IMPORTANT: if you are a plugin/theme developer and you were using the `'db_shortcodes_info_{your_shortcode}'` filter: the behaviour of this filter has changed from passing an array, to passing an object. Please read the [FAQ](https://wordpress.org/plugins/debug-bar-shortcodes/faq/) for information on how to deal with this change !

* Enhancement: Added support for shortcode information available through [Shortcake](https://wordpress.org/plugins/shortcode-ui/).
* Enhancement: At least try and detect the plugin url if more than one plugin was found in the same directory.
* Bug Fix: Information was not obtained for shortcodes with a closure as callback. (Reflection object was not obtained for closures.)
* Compatibility fix: Minor html change to fix layout for WP 4.4.
* Usability: Improved table header alignment.
* Usability: Row actions now visible on hover over row, not just title.
* Usability: Improved compatibility with Glotpress / WP translations.
* Minor housekeeping.
* Tested & found compatible WP 4.4.

= 1.0.3 (2014-12-18) =
* Added: more detailed information about the WP native `playlist` shortcode.
* Tested & found compatible WP 4.1.

= 1.0.2 (2014-09-05) =
* Fix compatibility with the [Plugin Dependencies](https://wordpress.org/plugins/plugin-dependencies/) plugin.
* Tested & found compatible WP 4.0.

= 1.0.1 (2014-04-19) =
* Fixed: better finding of shortcodes within post content.
* Fixed: minor html error.
* Fixed: minor PHP error.

= 1.0 (2013-12-22) =
* Initial release.


== Upgrade Notice ==

= 2.0 =
* Added ShortCake support.

= 1.0 =
* Initial release.


== Installation ==

1. Install Debug Bar if not already installed (https://wordpress.org/plugins/debug-bar/)
1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the "Plugins" menu in WordPress.

Be careful when you use this plugin on a live site. This plugin is intended for development purposes.


== Screenshots ==
1. Debug Bar displaying Shortcodes
1. Debug Bar displaying Shortcodes on web front-end singular
1. Debug  Bar Shortcodes - Example of detailed information about a shortcode if provided by the author
1. Debug  Bar Shortcodes - Example of detailed information about a shortcode based on information retrieved from the shortcode documentation
1. Debug  Bar Shortcodes - Example of shortcode usage found throughout the site

