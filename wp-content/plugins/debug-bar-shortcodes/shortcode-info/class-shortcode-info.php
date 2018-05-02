<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info' ) ) :

	/**
	 * Retrieve shortcode info.
	 */
	class Debug_Bar_Shortcode_Info {

		/**
		 * The shortcode this object applies to.
		 *
		 * @var string $shortcode
		 */
		protected $shortcode = '';

		/**
		 * Defaults.
		 *
		 * @var	\Debug_Bar_Shortcode_Info_Defaults $defaults
		 */
		protected $defaults;

		/**
		 * Shortcode info object.
		 *
		 * Can be of type Debug_Bar_Shortcode_Info_Defaults (or one of it's children), but doesn't have to be.
		 *
		 * @var object $info
		 */
		protected $info;

		/**
		 * The names of the shortcodes which are included with WP by default.
		 *
		 * @var array $wp_shortcodes
		 */
		protected $wp_shortcodes = array(
			'audio',
			'video',
			'caption',
			'wp_caption',
			'gallery',
			'embed',
			'playlist',
		);

		/**
		 * Try and enrich the shortcode with additional information.
		 *
		 * @param string $shortcode      Current shortcode.
		 * @param bool   $use_reflection Whether or not to get additional information from the function documentation.
		 *                               Defaults to false.
		 */
		public function __construct( $shortcode, $use_reflection = false ) {

			$this->defaults = new Debug_Bar_Shortcode_Info_Defaults();
			$this->info     = $this->defaults;

			// Bail out if we didn't receive a valid shortcode.
			if ( empty( $shortcode ) || ! is_string( $shortcode ) ) {
				return;
			}


			$this->shortcode = $shortcode;

			// Low priority to allow override by better data.
			add_filter( 'db_shortcodes_info_' . $this->shortcode, array( $this, 'lhr_shortcode_info' ), 8 );
			add_filter( 'db_shortcodes_info_' . $this->shortcode, array( $this, 'shortcake_shortcode_info' ), 9 );
			add_filter( 'db_shortcodes_info_' . $this->shortcode, array( $this, 'wp_shortcode_info' ), 10 );


			if ( true === $use_reflection && true === $this->is_registered() ) {
				add_filter( 'db_shortcodes_info_' . $this->shortcode, array( $this, 'shortcode_info_from_documentation' ), 12 );
			}

			/*
			 * Casts objects to array before passing them into the filter for backwards compatibility
			 * with the original (pre-2.0) documentation.
			 *
			 * @todo document filters
			 */
			$info = (object) apply_filters( 'db_shortcodes_info', $this->defaults, $this->shortcode, (array) $this->defaults );
			$info = (object) apply_filters( 'db_shortcodes_info_' . $this->shortcode, $info, (array) $info );

			// Make sure that the information is usable.
			$this->info = new Debug_Bar_Shortcode_Info_Validator( $info );
		}


		/**
		 * Get the info object for this shortcode.
		 *
		 * @return \Debug_Bar_Shortcode_Info_Defaults
		 */
		public function get_info_object() {
			return $this->info;
		}


		/**
		 * Check whether the shortcode has details beyond the defaults.
		 *
		 * @return bool True if it has, false otherwise.
		 */
		public function has_details() {
			return ( (array) $this->info !== (array) $this->defaults );
		}


		/**
		 * Check whether the shortcode has been registered in WP.
		 *
		 * @return bool True if registered, false otherwise.
		 */
		public function is_registered() {
			return ( ! empty( $this->shortcode ) && isset( $GLOBALS['shortcode_tags'][ $this->shortcode ] ) );
		}


		/* ************** METHODS TO RETRIEVE SHORTCODE INFO ************** */


		/**
		 * Get potentially provided info for a shortcode in the lhr shortcode plugin format.
		 *
		 * @param object $info Shortcode info.
		 *
		 * @return object Updated shortcode info.
		 */
		public function lhr_shortcode_info( $info ) {
			// If the current shortcode is native to WP Core, don't use the lhr info as the info
			// in this plugin is better.
			if ( in_array( $this->shortcode, $this->wp_shortcodes, true ) || false === has_filter( 'sim_' . $this->shortcode ) ) {
				return $info;
			}

			$additional = new Debug_Bar_Shortcode_Info_LHR( $this->shortcode );
			return $this->merge_info_objects( $additional, $info );
		}


		/**
		 * Get potentially provided info for a shortcode based on the Shortcake UI registration.
		 *
		 * @param object $info Shortcode info.
		 *
		 * @return object Updated shortcode info.
		 */
		public function shortcake_shortcode_info( $info ) {
			// Bail out if Shortcake is not available.
			if ( ! function_exists( 'shortcode_ui_get_register_shortcode' ) ) {
				return $info;
			}

			$additional = new Debug_Bar_Shortcode_Info_Shortcake( $this->shortcode );
			return $this->merge_info_objects( $additional, $info );
		}


		/**
		 * Enrich the information for the standard WP shortcodes.
		 *
		 * @param object $info Shortcode info.
		 *
		 * @return object Updated shortcode info.
		 */
		public function wp_shortcode_info( $info ) {
			// Return early if not a WP Core native shortcode.
			if ( ! in_array( $this->shortcode, $this->wp_shortcodes, true ) ) {
				return $info;
			}

			$class      = 'Debug_Bar_Shortcode_Info_' . $this->shortcode;
			$additional = new $class;
			return $this->merge_info_objects( $additional, $info );
		}


		/**
		 * Conditionally retrieve additional info about a shortcode based on the function/method documentation.
		 *
		 * @param object $info Shortcode info.
		 *
		 * @return object Updated shortcode info.
		 */
		public function shortcode_info_from_documentation( $info ) {
			$additional = new Debug_Bar_Shortcode_Info_From_File( $this->shortcode );
			return $this->merge_info_objects( $additional, $info );
		}


		/* ************** HELPER METHODS ************** */

		/**
		 * Merge two info objects into one making sure that the old values are not overruled by 'empties'.
		 *
		 * The received value *could* be an array if someone still uses the old filter logic.
		 *
		 * @param object|array $new The new values.
		 * @param object|array $old The old values.
		 *
		 * @return object
		 */
		public function merge_info_objects( $new, $old ) {
			// Filter out the empties, but preserve the value for self_closing (which could be false).
			$new_filtered                 = array_filter( (array) $new );
			$new_filtered['self_closing'] = isset( $new->self_closing ) ? $new->self_closing : null;

			return (object) $this->array_merge_recursive_distinct( (array) $old, $new_filtered );
		}


		/**
		 * Recursively merge a variable number of arrays, using the left array as base,
		 * giving priority to the right array.
		 *
		 * Difference with native array_merge_recursive():
		 * `array_merge_recursive()` converts values with duplicate keys to arrays rather than
		 * overwriting the value in the first array with the duplicate value in the second array.
		 *
		 * `array_merge_recursive_distinct()` does not change the data types of the values in the arrays.
		 * Matching keys' values in the second array overwrite those in the first array, as is the
		 * case with array_merge.
		 *
		 * Freely based on information found on http://www.php.net/manual/en/function.array-merge-recursive.php.
		 *
		 * @params array $arrays 2 or more arrays to merge.
		 *
		 * @return array
		 */
		private function array_merge_recursive_distinct() {

			$arrays = func_get_args();
			if ( count( $arrays ) < 2 ) {
				if ( empty( $arrays ) ) {
					return array();
				}
				else {
					return $arrays[0];
				}
			}

			$merged = array_shift( $arrays );

			foreach ( $arrays as $array ) {
				foreach ( $array as $key => $value ) {
					if ( is_array( $value ) && ( isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) ) {
						$merged[ $key ] = self::array_merge_recursive_distinct( $merged[ $key ], $value );
					}
					else {
						$merged[ $key ] = $value;
					}
				}
				unset( $key, $value );
			}
			return $merged;
		}
	} // End of class.

endif; // End of if class_exists.
