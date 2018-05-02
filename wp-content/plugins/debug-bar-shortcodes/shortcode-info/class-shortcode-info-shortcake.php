<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Shortcake.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Shortcake' ) ) :

	/**
	 * Information on a shortcode based on what's available through the Shortcake UI registration.
	 *
	 * @see [Shortcake](https://wordpress.org/plugins/shortcode-ui/)
	 * @see https://github.com/wp-shortcake/Shortcake/
	 *
	 * @todo Potentially provide support for shortcake ui registrations from other plugins even
	 * when shortcake itself is not available. I.e. run their hooks to obtain the info.
	 *
	 * Current implementation based on Shortcake 0.6.2.
	 */
	class Debug_Bar_Shortcode_Info_Shortcake extends Debug_Bar_Shortcode_Info_From_File {

		/**
		 * Shortcode as registered in Shortcake.
		 *
		 * @var array
		 */
		private $shortcake;


		/**
		 * Constructor - set the properties.
		 *
		 * @param string $shortcode The shortcode for which to retrieve info.
		 */
		public function __construct( $shortcode = '' ) {
			$shortcake = shortcode_ui_get_register_shortcode( $shortcode );

			if ( ! isset( $shortcake ) ) {
				return;
			}

			$this->shortcake = $shortcake;

			Debug_Bar_Shortcode_Info_Reflection::__construct( $shortcode ); // Skip parent, do grandparent.

			$this->set_name();
			$this->set_description();
			$this->set_self_closing();
			$this->set_parameters();
			$this->set_info_url();
		}


		/**
		 * Set the name property if Shortcake provided us with usable information.
		 */
		private function set_name() {
			if ( ! empty( $this->shortcake['label'] ) && ( is_string( $this->shortcake['label'] ) && $this->shortcake['label'] !== $this->shortcode ) ) {
				$this->name = $this->shortcake['label'];
			}
		}


		/**
		 * Set the description property if Shortcake provided us with usable information.
		 *
		 * @internal Description field support does not exist yet within Shortcake.
		 * @see https://github.com/wp-shortcake/Shortcake/issues/386
		 */
		private function set_description() {
			if ( ! empty( $this->shortcake['description'] ) && is_string( $this->shortcake['description'] ) ) {
				$this->description = $this->shortcake['description'];
			}
		}


		/**
		 * Set the self_closing property if Shortcake provided us with usable information.
		 */
		private function set_self_closing() {
			if ( ! empty( $this->shortcake['inner_content'] ) && is_array( $this->shortcake['inner_content'] ) ) {
				$this->self_closing = false;
			}
			else {
				$this->self_closing = true;
			}
		}


		/**
		 * Set the parameter property if Shortcake provided us with usable information.
		 */
		private function set_parameters() {
			if ( ! empty( $this->shortcake['attrs'] ) && is_array( $this->shortcake['attrs'] ) ) {

				foreach ( $this->shortcake['attrs'] as $attr_array ) {

					if ( ! isset( $attr_array['attr'] ) ) {
						continue;
					}

					$text = '';
					foreach ( array( 'label', 'description' ) as $key ) {
						if ( ! empty( $attr_array[ $key ] ) && is_string( $attr_array[ $key ] ) ) {
							$text .= $attr_array[ $key ] . '. ';
						}
					}
					unset( $key );


					// Does not exist yet - issue https://github.com/wp-shortcake/Shortcake/issues/132 .
					if ( isset( $attr_array['required'] ) && true === $attr_array['required'] ) {
						$this->parameters['required'][ $attr_array['attr'] ] = $text;
					}
					else {
						$this->parameters['optional'][ $attr_array['attr'] ] = $text;
					}
				}
			}
		}


		/**
		 * Set the info URL.
		 *
		 * @internal Maybe move this to an ajax call once Shortcake is used more as is expensive.
		 */
		private function set_info_url() {
			if ( $this->reflection_object instanceof ReflectionFunctionAbstract ) {
				$this->info_url = $this->get_plugin_url_from_file( $this->reflection_object->getFileName() );
			}
		}
	} // End of class.

endif; // End of if class_exists.
