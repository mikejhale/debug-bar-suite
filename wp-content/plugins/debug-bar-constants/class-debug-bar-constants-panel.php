<?php
/**
 * Debug Bar Constants Panel - Base class for a Debug Bar Constants Debug Bar Panels.
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


if ( ! class_exists( 'Debug_Bar_Constants_Panel' ) && class_exists( 'Debug_Bar_Panel' ) ) {

	/**
	 * Base class.
	 */
	abstract class Debug_Bar_Constants_Panel extends Debug_Bar_Panel {

		/**
		 * Should the tab be visible ?
		 * You can set conditions here so something will for instance only show on the front- or the
		 * back-end.
		 */
		public function prerender() {
			$this->set_visible( true );
		}


		/**
		 * Helper method to render the output in a table.
		 *
		 * @param array             $array Array to be shown in the table.
		 * @param string|null       $col1  Label for the first table column.
		 * @param string|null       $col2  Label for the second table column.
		 * @param string|array|null $class One or more CSS classes to add to the table.
		 */
		public function dbc_render_table( $array, $col1 = null, $col2 = null, $class = null ) {

			$classes = Debug_Bar_Constants_Init::DBC_NAME;
			if ( isset( $class ) ) {
				if ( is_string( $class ) && '' !== $class ) {
					$classes .= ' ' . $class;
				} elseif ( ! empty( $class ) && is_array( $class ) ) {
					$classes = $classes . ' ' . implode( ' ', $class );
				}
			}
			$col1 = ( isset( $col1 ) ? $col1 : __( 'Name', 'debug-bar-constants' ) );
			$col2 = ( isset( $col2 ) ? $col2 : __( 'Value', 'debug-bar-constants' ) );

			uksort( $array, 'strnatcasecmp' );

			if ( defined( 'Debug_Bar_Pretty_Output::VERSION' ) ) {
				echo Debug_Bar_Pretty_Output::get_table( $array, $col1, $col2, $classes ); // WPCS: xss ok.

			} else {
				// An old version of the pretty output class was loaded.
				Debug_Bar_Pretty_Output::render_table( $array, $col1, $col2, $classes );
			}
		}
	} // End of class Debug_Bar_Constants_Panel.

} // End of if class_exists wrapper.
