<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Reflection.
 *
 * @package     WordPress\Plugins\Debug Bar Shortcodes
 * @subpackage  WordPress\Plugins\Debug Bar Shortcodes\Shortcode Info
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Shortcodes
 * @since       2.0
 *
 * @copyright   2013-2016 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Reflection' ) ) :

	/**
	 * Retrieve information on a shortcode using Reflection.
	 *
	 * Utility class. Not meant to be called directly, but meant to be extended.
	 */
	class Debug_Bar_Shortcode_Info_Reflection extends Debug_Bar_Shortcode_Info_Defaults {

		/**
		 * The current shortcode.
		 *
		 * @var string $shortcode
		 */
		protected $shortcode = '';

		/**
		 * Reflection object for the function being called for the current shortcode.
		 *
		 * @var object $reflection_object
		 */
		protected $reflection_object;


		/**
		 * Constructor - set the properties.
		 *
		 * @param string $shortcode The shortcode for which to retrieve info.
		 */
		public function __construct( $shortcode = '' ) {
			if ( ! empty( $shortcode ) && is_string( $shortcode ) ) {
				$this->shortcode = $shortcode;
			}
			$this->set_reflection_object();
		}


		/**
		 * Retrieve a Reflection object for the file a shortcode is in.
		 *
		 * @uses $this->shortcode
		 * @uses $this->reflection_object
		 */
		private function set_reflection_object() {
			$shortcodes = $GLOBALS['shortcode_tags'];

			if ( empty( $this->shortcode ) || ! isset( $shortcodes[ $this->shortcode ] ) ) {
				// Not a registered shortcode.
				return;
			}

			$callback = $shortcodes[ $this->shortcode ];

			if ( ! is_string( $callback ) && ( ! is_array( $callback ) || ( is_array( $callback ) && ( ! is_string( $callback[0] ) && ! is_object( $callback[0] ) ) ) ) && ( ! is_object( $callback ) || ( is_object( $callback ) && ! Debug_Bar_Shortcodes_Render::is_closure( $callback ) ) ) ) {
				// Not a valid callback.
				return;
			}


			/* Set up reflection. */
			if ( ( is_string( $callback ) && false === strpos( $callback, '::' ) ) || ( is_object( $callback ) && Debug_Bar_Shortcodes_Render::is_closure( $callback ) ) ) {
				$this->reflection_object = new ReflectionFunction( $callback );
			}
			elseif ( is_string( $callback ) && false !== strpos( $callback, '::' ) ) {
				$this->reflection_object = new ReflectionMethod( $callback );
			}
			elseif ( is_array( $callback ) ) {
				$this->reflection_object = new ReflectionMethod( $callback[0], $callback[1] );
			}


			if ( isset( $this->reflection_object ) && false === $this->reflection_object->isUserDefined() ) {
				// Not a user defined callback, i.e. native PHP, nothing to find out about it (shouldn't ever happen).
				$this->reflection_object = null;
			}
		}
	} // End of class.

endif; // End of if class_exists.
