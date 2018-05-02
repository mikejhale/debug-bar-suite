<?php
/**
 * Debug Bar PHP Constants - Debug Bar Panel.
 *
 * @package     WordPress\Plugins\Debug Bar Constants
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Constants
 *
 * @copyright   2013-2017 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if ( ! class_exists( 'Debug_Bar_PHP_Constants' ) && class_exists( 'Debug_Bar_Constants_Panel' ) ) {

	/**
	 * Debug Bar PHP Constants.
	 */
	class Debug_Bar_PHP_Constants extends Debug_Bar_Constants_Panel {


		/**
		 * Constructor.
		 */
		public function init() {
			$this->title( __( 'PHP Constants', 'debug-bar-constants' ) );
		}

		/**
		 * Render the output.
		 */
		public function render() {

			$constants = get_defined_constants( true );
			unset( $constants['user'] );

			if ( ! empty( $constants ) && is_array( $constants ) ) {
				uksort( $constants, 'strnatcasecmp' );

				foreach ( $constants as $category => $set ) {
					echo '
		<h2><a href="#dbcphp-', esc_attr( $category ), '"><span>', esc_html( $category ), ':</span>', (int) count( $set ), '</a></h2>';
				}
				unset( $category, $set );

				foreach ( $constants as $category => $set ) {
					if ( ! empty( $set ) && is_array( $set ) ) {

						// Set url to correct page in the PHP manual for more info.
						$url = $this->get_php_manual_url( $category );

						/* TRANSLATORS: %s = the name of a PHP extension. */
						$title_attr = sprintf( __( 'Visit the PHP manual page about the %s constants.', 'debug-bar-constants' ), $category );

						echo '
		<h3 id="dbcphp-', esc_attr( $category ), '"><em>';

						if ( ! empty( $url ) ) {
							echo '<a href="', esc_url( $url ), '" target="_blank" title="', esc_attr( $title_attr ), '">', esc_html( ucfirst( $category ) ), '</a>';
						} else {
							echo esc_html( ucfirst( $category ) );
						}

						echo '</em> ', esc_html__( 'Constants:', 'debug-bar-constants' ), '</h3>';

						$this->dbc_render_table( $set );
					}
				}
				unset( $category, $set, $title_attr );

			} else {
				// Should never happen.
				echo '<p>', esc_html__( 'No PHP constants found... this is really weird...', 'debug-bar-constants' ), '</p>';
			}
		}


		/**
		 * Retrieve the PHP manual URL for the constants page of a specific PHP extension.
		 *
		 * Works round some of the peculiarities of the PHP.net URL scheme.
		 *
		 * @param string $category The PHP Extension for which to retrieve the URL.
		 *
		 * @return string URL
		 */
		protected function get_php_manual_url( $category ) {
			$category = strtolower( $category );

			switch ( $category ) {
				case 'core':
					$url = 'http://php.net/reserved.constants';
					break;

				case 'date':
					$url = 'http://php.net/datetime.constants';
					break;

				case 'gd':
					$url = 'http://php.net/image.constants';
					break;

				case 'odbc':
					$url = 'http://php.net/uodbc.constants';
					break;

				case 'standard':
					$url = ''; // Definitions are all over, part of core.
					break;

				case 'tokenizer':
					$url = 'http://php.net/tokens';
					break;

				case 'xdebug':
					$url = 'http://xdebug.com/docs/';
					break;

				default:
					$url = 'http://php.net/' . rawurlencode( $category ) . '.constants';
					break;
			}

			return $url;
		}
	} // End of class Debug_Bar_PHP_Constants.

} // End of if class_exists wrapper.
