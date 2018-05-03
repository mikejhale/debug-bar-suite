<?php
/**
 * Plugin bootstrap file
 *
 * @package Debug_Bar_Suite
 * @version 0.1.0
 */

/*
Plugin Name: Debug Bar Suite
Plugin URI:  http://mikehale.me/plugins/debug-bar-suite
Description: Enables Debug Bar and Add-ons
Author:      MikeHale
Version:     0.1.0
Author URI:  http://mikehale.me
*/

namespace Debug_Bar_Suite;

/**
 * Class Debug_Bar_Suite_Loader
 *
 * @package Debug_Bar_Suite
 */
class Debug_Bar_Suite_Loader {

	/**
	 * Debug Bar Addon Files.
	 *
	 * @var array debug_suite_files List of Debug Bar Add-on files to include.
	 */
	public $debug_suite_files;

	/**
	 * Debug_Bar_Suite constructor.
	 */
	public function __construct() {

		require __DIR__ . '/vendor/autoload.php';

		$this->debug_suite_files = self::get_addons();

		$enabled_addons = apply_filters(
			'debug_bar_suite_enabled_addons',
			get_option( 'debug_bar_suite_enabled_addons' )
		);

		if ( $enabled_addons && is_array( $enabled_addons ) ) {
			foreach ( $enabled_addons as $addon => $enabled ) {
				if ( array_key_exists( $addon, $this->debug_suite_files ) && 'on' === $enabled ) {
					$file_path = sprintf(
						'%s/%s',
						__DIR__,
						$this->debug_suite_files[ $addon ]
					);

					require $file_path;
				}
			}
		}

		// init admin.
		new Debug_Bar_Suite_Admin();

		// register activation.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
	}

	/**
	 * Get list of Debug Bar Addons
	 *
	 * @return array
	 */
	public static function get_addons() {
		return apply_filters(
			'debug_bar_suite_addon_files',
			array(
				'debug-bar-action-and-filters-addon' => 'wp-content/plugins/debug-bar-actions-and-filters-addon/debug-bar-action-and-filters-addon.php',
				'debug-bar-console'                  => 'wp-content/plugins/debug-bar-console/debug-bar-console.php',
				'debug-bar-constants'                => 'wp-content/plugins/debug-bar-constants/debug-bar-constants.php',
				'debug-bar-cron'                     => 'wp-content/plugins/debug-bar-cron/debug-bar-cron.php',
				'debug-bar-list-dependencies'        => 'wp-content/plugins/debug-bar-list-dependencies/debug-bar-list-dependencies.php',
				'debug-bar-post-types'               => 'wp-content/plugins/debug-bar-post-types/debug-bar-post-types.php',
				'debug-bar-remote-requests'          => 'wp-content/plugins/debug-bar-remote-requests/debug-bar-remote-requests.php',
				'debug-bar-shortcodes'               => 'wp-content/plugins/debug-bar-shortcodes/debug-bar-shortcodes.php',
				'debug-bar-transients'               => 'wp-content/plugins/debug-bar-transients/debug-bar-transients.php',
				'query-monitor'                      => 'wp-content/plugins/query-monitor/query-monitor.php',
			)
		);
	}

	/**
	 * Handles `register_activation_hook`
	 * Generates debug_bar_suite_enabled_addons option all enabled by default.
	 *
	 * @return void
	 */
	public function activate() {

		$enabled_addons = array();

		foreach ( array_keys( self::get_addons() ) as $addon ) {
			$enabled_addons[ $addon ] = 'on';
		}

		update_option( 'debug_bar_suite_enabled_addons', $enabled_addons, true );
	}
}

new Debug_Bar_Suite_Loader();
