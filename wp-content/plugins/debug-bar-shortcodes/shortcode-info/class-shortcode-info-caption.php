<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Caption.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Caption' ) ) :

	/**
	 * Information on the standard WP [caption] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Caption extends Debug_Bar_Shortcode_Info_Defaults {

		/**
		 * Whether the shortcode is self-closing or not.
		 *
		 * @var bool|null $self_closing
		 */
		public $self_closing = false;

		/**
		 * Info URL.
		 *
		 * @var string $info_url
		 */
		public $info_url = 'http://codex.wordpress.org/Caption_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Wrap captions around content', 'debug-bar-shortcodes' );
			$this->description = __( 'The Caption feature allows you to wrap captions around content. This is primarily used with individual images.', 'debug-bar-shortcodes' );

			$this->parameters['required'] = array(
				'caption' => __( 'The actual text of your caption.', 'debug-bar-shortcodes' ),
				'width'   => __( 'How wide the caption should be in pixels.', 'debug-bar-shortcodes' ),
			);

			$this->parameters['optional'] = array(
				'id'    => __( 'A unique HTML ID that you can change to use within your CSS.', 'debug-bar-shortcodes' ),
				'align' => __( 'The alignment of the caption within the post. Valid values are: alignnone (default), aligncenter, alignright, and alignleft.', 'debug-bar-shortcodes' ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
