<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Audio.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Audio' ) ) :

	/**
	 * Information on the standard WP [audio] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Audio extends Debug_Bar_Shortcode_Info_Defaults {

		/**
		 * Whether the shortcode is self-closing or not.
		 *
		 * @var bool|null $self_closing
		 */
		public $self_closing = true;

		/**
		 * Info URL.
		 *
		 * @var string $info_url
		 */
		public $info_url = 'http://codex.wordpress.org/Audio_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Audio Media', 'debug-bar-shortcodes' );
			$this->description = __( 'The Audio feature allows you to embed audio files and play them back. This was added as of WordPress 3.6.', 'debug-bar-shortcodes' );

			/* translators: %s = file format. */
			$string = __( 'Source of %s fallback file', 'debug-bar-shortcodes' );

			$this->parameters['optional'] = array(
				'src'      => __( 'The source of your audio file. If not included it will auto-populate with the first audio file attached to the post.', 'debug-bar-shortcodes' ),
				'mp3'      => sprintf( $string, 'mp3' ),
				'm4a'      => sprintf( $string, 'm4a' ),
				'ogg'      => sprintf( $string, 'ogg' ),
				'wav'      => sprintf( $string, 'wav' ),
				'wma'      => sprintf( $string, 'wma' ),
				'loop'     => __( 'Allows for the looping of media. Defaults to "off".', 'debug-bar-shortcodes' ),
				'autoplay' => __( 'Causes the media to automatically play as soon as the media file is ready. Defaults to "off".', 'debug-bar-shortcodes' ),
				'preload'  => __( 'Specifies if and how the audio should be loaded when the page loads. Defaults to "none".', 'debug-bar-shortcodes' ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
