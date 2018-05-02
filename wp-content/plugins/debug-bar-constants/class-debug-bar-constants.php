<?php
/**
 * Debug Bar Constants.
 *
 * @package     WordPress\Plugins\Debug Bar Constants
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Constants
 *
 * @copyright   2013-2018 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if ( ! class_exists( 'Debug_Bar_Constants' ) ) {

	/**
	 * Plugin controller.
	 */
	class Debug_Bar_Constants {

		/**
		 * Plugin version nr for use with enqueuing styles.
		 *
		 * @var string
		 */
		const DBC_STYLES_VERSION = '2.0.0';

		/**
		 * Plugin version nr for use with enqueuing scripts.
		 *
		 * @var string
		 */
		const DBC_SCRIPT_VERSION = '2.0.0';


		/**
		 * Constructor.
		 */
		public function __construct() {
			spl_autoload_register( array( $this, 'auto_load' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Low priority, no need for it to be high up in the list.
			add_filter( 'debug_bar_panels', array( $this, 'add_panels' ), 12 );
		}


		/**
		 * Auto load our class files.
		 *
		 * @param string $class Class name.
		 *
		 * @return void
		 */
		public function auto_load( $class ) {
			static $classes = null;

			if ( null === $classes ) {
				$classes = array(
					'debug_bar_constants_panel'    => 'class-debug-bar-constants-panel.php',
					'debug_bar_wp_constants'       => 'class-debug-bar-wp-constants.php',
					'debug_bar_wp_class_constants' => 'class-debug-bar-wp-class-constants.php',
					'debug_bar_php_constants'      => 'class-debug-bar-php-constants.php',

					'debug_bar_pretty_output'      => 'inc/debug-bar-pretty-output/class-debug-bar-pretty-output.php',
					'debug_bar_list_php_classes'   => 'inc/debug-bar-pretty-output/class-debug-bar-list-php-classes.php',
				);
			}

			$cn = strtolower( $class );

			if ( isset( $classes[ $cn ] ) ) {
				include_once plugin_dir_path( __FILE__ ) . $classes[ $cn ];
			}
		}


		/**
		 * Enqueue js and css files.
		 */
		public function enqueue_scripts() {
			$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
			wp_enqueue_style(
				Debug_Bar_Constants_Init::DBC_NAME,
				plugins_url( 'css/' . Debug_Bar_Constants_Init::DBC_NAME . $suffix . '.css', __FILE__ ),
				array( 'debug-bar' ),
				self::DBC_STYLES_VERSION
			);
			wp_enqueue_script(
				Debug_Bar_Constants_Init::DBC_NAME,
				plugins_url( 'js/jquery.ui.totop' . $suffix . '.js', __FILE__ ),
				array( 'jquery' ),
				self::DBC_SCRIPT_VERSION,
				true
			);
		}


		/**
		 * Add the Debug Bar Constant panels to the Debug Bar.
		 *
		 * @param array $panels Existing debug bar panels.
		 *
		 * @return array
		 */
		public function add_panels( $panels ) {
			$panels[] = new Debug_Bar_WP_Constants();
			$panels[] = new Debug_Bar_WP_Class_Constants();
			$panels[] = new Debug_Bar_PHP_Constants();
			return $panels;
		}

	} // End of class Debug_Bar_Constants.

} // End of if class_exists wrapper.
