<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Validator.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Validator' ) ) :

	/**
	 * Retrieve information on a shortcode using Reflection.
	 *
	 * Utility class. Not meant to be called directly, but meant to be extended.
	 */
	class Debug_Bar_Shortcode_Info_Validator extends Debug_Bar_Shortcode_Info_Defaults {

		/**
		 * Unvalidated shortcode info.
		 *
		 * @var object $dirty
		 */
		private $dirty;


		/**
		 * Validate the shortcode info before using it to make sure that it's still in a usable form.
		 *
		 * @param object|array $info Shortcode info.
		 */
		public function __construct( $info ) {
			if ( is_object( $info ) ) {
				$this->dirty = $info;
			}
			elseif ( is_array( $info ) && ! empty( $info ) ) {
				$this->dirty = (object) $info;
			}
			else {
				// No valid input received, will effectively return the default properties.
				return;
			}

			$this->validate_name();
			$this->validate_description();
			$this->validate_self_closing();
			$this->validate_parameters();
			$this->validate_info_url();

			unset( $this->dirty );
		}


		/**
		 * Validate a shortcode name.
		 */
		private function validate_name() {
			if ( isset( $this->dirty->name ) && is_string( $this->dirty->name ) && '' !== trim( $this->dirty->name ) ) {
				$this->name = sanitize_text_field( trim( $this->dirty->name ) );
			}
		}


		/**
		 * Validate a shortcode description.
		 */
		private function validate_description() {
			if ( isset( $this->dirty->description ) && is_string( $this->dirty->description ) && '' !== trim( $this->dirty->description ) ) {
				$this->description = wp_kses( trim( $this->dirty->description ), array( 'br' => array() ) );
			}
		}


		/**
		 * Validate the self_closing information.
		 */
		private function validate_self_closing() {
			if ( isset( $this->dirty->self_closing ) ) {
				// Work around flacky behaviour of PHP for the FILTER_NULL_ON_FAILURE flag.
				if ( is_bool( $this->dirty->self_closing ) ) {
					$this->self_closing = $this->dirty->self_closing;
				}
				elseif ( function_exists( 'filter_var' ) ) {
					$this->self_closing = filter_var( $this->dirty->self_closing, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
				}
			}
		}


		/**
		 * Validate the shortcode parameters information.
		 */
		private function validate_parameters() {
			if ( ! empty( $this->dirty->parameters ) && is_array( $this->dirty->parameters ) ) {
				foreach ( $this->parameters as $k => $v ) {
					if ( ! empty( $this->dirty->parameters[ $k ] ) && is_array( $this->dirty->parameters[ $k ] ) ) {
						foreach ( $this->dirty->parameters[ $k ] as $attr => $explanation ) {
							if ( ( ( is_string( $attr ) && '' !== trim( $attr ) ) || ( is_int( $attr ) && $attr >= 0 ) ) && ( is_string( $explanation ) && '' !== trim( $explanation ) ) ) {
								$this->parameters[ $k ][ sanitize_key( trim( $attr ) ) ] = sanitize_text_field( trim( $explanation ) );
							}
						}
						unset( $attr, $explanation );
					}
				}
				unset( $k, $v );
			}
		}


		/**
		 * Validate the shortcode info URL.
		 */
		private function validate_info_url() {
			if ( ( isset( $this->dirty->info_url ) && is_string( $this->dirty->info_url ) && preg_match( '`http(s?)://(.+)`i', $this->dirty->info_url ) ) ) {
				$this->info_url = esc_url_raw( trim( $this->dirty->info_url ) );
			}
		}
	} // End of class.

endif; // End of if class_exists.
