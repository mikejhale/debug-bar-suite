<?php
/**
 * Debug Bar WP Constants - Debug Bar Panel.
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


if ( ! class_exists( 'Debug_Bar_WP_Constants' ) && class_exists( 'Debug_Bar_Constants_Panel' ) ) {

	/**
	 * Debug Bar WP Constants.
	 */
	class Debug_Bar_WP_Constants extends Debug_Bar_Constants_Panel {


		/**
		 * Constructor.
		 */
		public function init() {
			$this->title( __( 'WP Constants', 'debug-bar-constants' ) );
		}


		/**
		 * Limit visibility of the output to super admins on multi-site and
		 * admins on non multi-site installations.
		 */
		public function prerender() {
			$this->set_visible( is_super_admin() );
		}


		/**
		 * Render the output.
		 */
		public function render() {
			$constants = get_defined_constants( true );
			if ( isset( $constants['user'] ) && ( ! empty( $constants['user'] ) && is_array( $constants['user'] ) ) ) {
				echo '
		<h2><span>', esc_html__( 'Constants within WP:', 'debug-bar-constants' ), '</span>', (int) count( $constants['user'] ), '</h2>';
				$this->dbc_render_table( $constants['user'] );

			} else {
				// Should never happen.
				echo '<p>', esc_html__( 'No constants found... this is really weird...', 'debug-bar-constants' ), '</p>';
			}
		}
	} // End of class Debug_Bar_WP_Constants.

} // End of if class_exists wrapper.
