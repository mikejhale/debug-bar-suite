<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Embed.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Embed' ) ) :

	/**
	 * Information on the standard WP [embed] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Embed extends Debug_Bar_Shortcode_Info_Defaults {

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
		public $info_url = 'http://codex.wordpress.org/Embed_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Embed videos, images, and other content', 'debug-bar-shortcodes' );
			$this->description = __( 'You can opt to wrap a URL in the [embed] shortcode. It will accomplish the same effect as having it on a line of it\'s own, but does not require the "Auto-embeds" setting to be enabled. It also allows you to set a maximum (but not fixed) width and height.If WordPress fails to embed your URL you will get a hyperlink to the URL.', 'debug-bar-shortcodes' );

			/* translators: %s = height/weight. */
			$string = __( 'Maximum %s for the embedded object.', 'debug-bar-shortcodes' );

			$this->parameters['optional'] = array(
				'height' => sprintf( $string, __( 'height', 'debug-bar-shortcodes' ) ),
				'width'  => sprintf( $string, __( 'width', 'debug-bar-shortcodes' ) ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
