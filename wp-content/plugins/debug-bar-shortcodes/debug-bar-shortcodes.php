<?php
/**
 * Debug Bar Shortcodes, a WordPress plugin.
 *
 * @package     WordPress\Plugins\Debug Bar Shortcodes
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Shortcodes
 * @since       1.0
 * @version     2.0.3
 *
 * @copyright   2013-2016 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 *
 * @wordpress-plugin
 * Plugin Name:	Debug Bar Shortcodes
 * Plugin URI:	https://wordpress.org/plugins/debug-bar-shortcodes/
 * Description:	Debug Bar Shortcodes adds a new panel to Debug Bar that displays all the registered shortcodes for the current request. Requires "Debug Bar" plugin.
 * Version:		2.0.3
 * Author:		Juliette Reinders Folmer
 * Author URI:	http://www.adviesenzo.nl/
 * Depends:     Debug Bar
 * Text Domain:	debug-bar-shortcodes
 * Domain Path:	/languages/
 * Copyright:	2013-2016 Juliette Reinders Folmer
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if ( ! function_exists( 'debug_bar_shortcodes_has_parent_plugin' ) ) {
	/**
	 * Show admin notice & de-activate if debug-bar plugin not active.
	 */
	function debug_bar_shortcodes_has_parent_plugin() {
		if ( is_admin() && ( ! class_exists( 'Debug_Bar' ) && current_user_can( 'activate_plugins' ) ) ) {
			add_action( 'admin_notices', create_function( null, 'echo \'<div class="error"><p>\' . sprintf( __( \'Activation failed: Debug Bar must be activated to use the <strong>Debug Bar Shortcodes</strong> Plugin. %sVisit your plugins page to activate.\', \'debug-bar-shortcodes\' ), \'<a href="\' . admin_url( \'plugins.php#debug-bar\' ) . \'">\' ) . \'</a></p></div>\';' ) );

			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

	add_action( 'admin_init', 'debug_bar_shortcodes_has_parent_plugin' );
}


if ( ! function_exists( 'debug_bar_shortcodes_autoload' ) ) {
	/**
	 * Auto load our class files.
	 *
	 * @param string $class Class name.
	 *
	 * @return void
	 */
	function debug_bar_shortcodes_autoload( $class ) {
		static $classes = null;

		if ( null === $classes ) {
			$classes = array(
				'debug_bar_shortcodes'                => 'class-debug-bar-shortcodes.php',
				'debug_bar_shortcodes_render'         => 'class-debug-bar-shortcodes-render.php',
				'debug_bar_shortcode_info'            => 'shortcode-info/class-shortcode-info.php',
				'debug_bar_shortcode_info_defaults'   => 'shortcode-info/class-shortcode-info-defaults.php',
				'debug_bar_shortcode_info_audio'      => 'shortcode-info/class-shortcode-info-audio.php',
				'debug_bar_shortcode_info_caption'    => 'shortcode-info/class-shortcode-info-caption.php',
				'debug_bar_shortcode_info_embed'      => 'shortcode-info/class-shortcode-info-embed.php',
				'debug_bar_shortcode_info_gallery'    => 'shortcode-info/class-shortcode-info-gallery.php',
				'debug_bar_shortcode_info_playlist'   => 'shortcode-info/class-shortcode-info-playlist.php',
				'debug_bar_shortcode_info_video'      => 'shortcode-info/class-shortcode-info-video.php',
				'debug_bar_shortcode_info_wp_caption' => 'shortcode-info/class-shortcode-info-wp-caption.php',
				'debug_bar_shortcode_info_lhr'        => 'shortcode-info/class-shortcode-info-lhr.php',
				'debug_bar_shortcode_info_reflection' => 'shortcode-info/class-shortcode-info-reflection.php',
				'debug_bar_shortcode_info_from_file'  => 'shortcode-info/class-shortcode-info-from-file.php',
				'debug_bar_shortcode_info_shortcake'  => 'shortcode-info/class-shortcode-info-shortcake.php',
				'debug_bar_shortcode_info_validator'  => 'shortcode-info/class-shortcode-info-validator.php',
			);
		}

		$classname = strtolower( $class );

		if ( isset( $classes[ $classname ] ) ) {
			include_once plugin_dir_path( __FILE__ ) . $classes[ $classname ];
		}
	}

	spl_autoload_register( 'debug_bar_shortcodes_autoload' );
}


if ( ! function_exists( 'debug_bar_shortcodes_panel' ) ) {
	/**
	 * Add the Debug Bar Shortcodes panel to the Debug Bar.
	 *
	 * @param array $panels Existing debug bar panels.
	 *
	 * @return array
	 */
	function debug_bar_shortcodes_panel( $panels ) {
		$panels[] = new Debug_Bar_Shortcodes();
		return $panels;
	}

	add_filter( 'debug_bar_panels', 'debug_bar_shortcodes_panel' );
}



if ( ! function_exists( 'debug_bar_shortcodes_ajax' ) ) {
	/**
	 * Verify validity of ajax request and pass it to the internal handler.
	 */
	function debug_bar_shortcodes_do_ajax() {
		// Verify this is a valid ajax request.
		if ( ! isset( $_POST['dbs-nonce'] ) || false === wp_verify_nonce( $_POST['dbs-nonce'], 'debug-bar-shortcodes' ) ) {
			exit( '-1' );
		}

		// Verify we have received the data needed to do anything.
		if ( ! isset( $_POST['shortcode'] ) || '' === trim( wp_unslash( $_POST['shortcode'] ) ) ) {
			exit( '-1' );
		}


		$output_rendering = new Debug_Bar_Shortcodes_Render();
		$shortcode        = trim( wp_unslash( $_POST['shortcode'] ) );
		$action           = trim( wp_unslash( $_POST['action'] ) );

		// Exit early if this is a non-existent shortcode - shouldn't happen, but hack knows ;-).
		if ( false === shortcode_exists( $shortcode ) ) {
			$response = array(
				'id'     => 0,
				'data'   => '',
				'action' => $action,
			);
			$output_rendering->send_ajax_response( $response );
			exit;
		}

		// Send the request to our handler.
		switch ( $action ) {
			case 'debug-bar-shortcodes-find':
				$output_rendering->ajax_find_shortcode_uses( $shortcode, $action );
				break;

			case 'debug-bar-shortcodes-retrieve':
				$output_rendering->ajax_retrieve_details( $shortcode, $action );
				break;

			default:
				// Intentionally empty.
				break;
		}

		/*
		   No valid action received (redundancy, can't really happen as WP wouldn't then call this
		   function, but would return 0 and exit already.
		 */
		exit( '-1' );
	}

	/* Add our ajax actions. */
	add_action( 'wp_ajax_debug-bar-shortcodes-find', 'debug_bar_shortcodes_do_ajax' );
	add_action( 'wp_ajax_debug-bar-shortcodes-retrieve', 'debug_bar_shortcodes_do_ajax' );
}
