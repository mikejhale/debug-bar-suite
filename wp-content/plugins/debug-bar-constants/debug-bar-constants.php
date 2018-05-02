<?php
/**
 * Debug Bar Constants, a WordPress plugin.
 *
 * @package     WordPress\Plugins\Debug Bar Constants
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Constants
 * @version     2.0.0
 *
 * @copyright   2013-2018 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Debug Bar Constants
 * Plugin URI:  https://wordpress.org/plugins/debug-bar-constants/
 * Description: Debug Bar Constants adds new panels to Debug Bar that display all the defined constants for the current request. Requires "Debug Bar" plugin.
 * Version:     2.0.0
 * Author:      Juliette Reinders Folmer
 * Author URI:  http://www.adviesenzo.nl/
 * Depends:     Debug Bar
 * Text Domain: debug-bar-constants
 * Domain Path: /languages
 * Copyright:   2013-2018 Juliette Reinders Folmer
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'Debug_Bar_Constants_Init' ) ) {

	/**
	 * Initialize plugin.
	 */
	class Debug_Bar_Constants_Init {

		/**
		 * Plugin name for use with text-domains and CSS classes.
		 *
		 * @var string
		 */
		const DBC_NAME = 'debug-bar-constants';


		/**
		 * Initialize the plugin.
		 *
		 * @return void
		 */
		public static function init() {
			/*
			 * Initialize the main class.
			 *
			 * @internal The wp_installing() function was introduced in WP 4.4.
			 */
			if ( ( function_exists( 'wp_installing' ) && wp_installing() === false )
				|| ( ! function_exists( 'wp_installing' )
					&& ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) )
			) {
				include_once plugin_dir_path( __FILE__ ) . 'class-debug-bar-constants.php';
				$GLOBALS['debug_bar_constants'] = new Debug_Bar_Constants();
			}

			// Show admin notice & de-activate itself if debug-bar plugin not active.
			add_action( 'admin_init', array( __CLASS__, 'has_debug_bar' ) );

			add_action( 'init', array( __CLASS__, 'load_textdomain' ) );
		}


		/**
		 * Load the plugin text strings.
		 *
		 * Compatible with use of the plugin in the must-use plugins directory.
		 *
		 * {@internal No longer needed since WP 4.6, though the language loading in
		 * WP 4.6 only looks at the `wp-content/languages/` directory and disregards
		 * any translations which may be included with the plugin.
		 * This is acceptable for plugins hosted on org, especially if the plugin
		 * is new and never shipped with it's own translations, but not when the plugin
		 * is hosted elsewhere.
		 * Can be removed if/when the minimum required version for this plugin is ever
		 * upped to 4.6. The `languages` directory can be removed in that case too.
		 * See: {@link https://core.trac.wordpress.org/ticket/34213} and
		 * {@link https://core.trac.wordpress.org/ticket/34114} }}
		 */
		public static function load_textdomain() {
			$domain = self::DBC_NAME;

			if ( function_exists( '_load_textdomain_just_in_time' ) ) {
				return;
			}

			if ( is_textdomain_loaded( $domain ) ) {
				return;
			}

			$lang_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';
			if ( false === strpos( __FILE__, basename( WPMU_PLUGIN_DIR ) ) ) {
				load_plugin_textdomain( $domain, false, $lang_path );
			} else {
				load_muplugin_textdomain( $domain, $lang_path );
			}
		}


		/**
		 * Check for the Debug Bar plugin being installed & active.
		 *
		 * @return void
		 */
		public static function has_debug_bar() {
			$file = plugin_basename( __FILE__ );

			if ( is_admin()
				&& ( ! class_exists( 'Debug_Bar' ) && current_user_can( 'activate_plugins' ) )
				&& is_plugin_active( $file )
			) {
				add_action( 'admin_notices', array( __CLASS__, 'display_admin_notice' ) );

				deactivate_plugins( $file, false, is_network_admin() );

				// Add to recently active plugins list.
				$insert = array(
					$file => time(),
				);

				if ( ! is_network_admin() ) {
					update_option( 'recently_activated', ( $insert + (array) get_option( 'recently_activated' ) ) );
				} else {
					update_site_option( 'recently_activated', ( $insert + (array) get_site_option( 'recently_activated' ) ) );
				}

				// Prevent trying to activate again on page reload.
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
			}
		}


		/**
		 * Display admin notice about activation failure when dependency not found.
		 *
		 * @return void
		 */
		public static function display_admin_notice() {
			echo '<div class="error"><p>';
			printf(
				/* translators: 1: strong open tag; 2: strong close tag; 3: link to plugin installation page; 4: link close tag. */
				esc_html__( 'Activation failed: Debug Bar must be activated to use the %1$sDebug Bar Constants%2$s Plugin. %3$sVisit your plugins page to install & activate%4$s.', 'debug-bar-constants' ),
				'<strong>',
				'</strong>',
				'<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=debug+bar' ) ) . '">',
				'</a>'
			);
			echo '</p></div>';
		}
	}
}

add_action( 'plugins_loaded', array( 'Debug_Bar_Constants_Init', 'init' ) );
