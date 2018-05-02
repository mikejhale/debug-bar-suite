<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Video.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Video' ) ) :

	/**
	 * Information on the standard WP [video] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Video extends Debug_Bar_Shortcode_Info_Defaults {

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
		public $info_url = 'http://codex.wordpress.org/Video_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Video Media', 'debug-bar-shortcodes' );
			$this->description = __( 'The Video feature allows you to embed video files and play them back. This was added as of WordPress 3.6.', 'debug-bar-shortcodes' );

			/* translators: %s = height/weight. */
			$string = __( 'Defines %s of the media. Value is automatically detected on file upload.', 'debug-bar-shortcodes' );

			$this->parameters['required'] = array(
				'height' => sprintf( $string, __( 'height', 'debug-bar-shortcodes' ) ),
				'width'  => sprintf( $string, __( 'width', 'debug-bar-shortcodes' ) ),
			);

			/* translators: %s = file extension. */
			$string = __( 'Source of %s fallback file.', 'debug-bar-shortcodes' );

			$this->parameters['optional'] = array(
				'src'      => __( 'The source of your video file. If not included it will auto-populate with the first video file attached to the post.', 'debug-bar-shortcodes' ),
				'mp4'      => sprintf( $string, 'mp4' ),
				'm4v'      => sprintf( $string, 'm4v' ),
				'webm'     => sprintf( $string, 'webm' ),
				'ogv'      => sprintf( $string, 'ogv' ),
				'wmv'      => sprintf( $string, 'wmv' ),
				'flv'      => sprintf( $string, 'flv' ),
				'poster'   => __( 'Defines image to show as placeholder before the media plays. Defaults to "none".', 'debug-bar-shortcodes' ),
				'loop'     => __( 'Allows for the looping of media. Defaults to "off"', 'debug-bar-shortcodes' ),
				'autoplay' => __( 'Causes the media to automatically play as soon as the media file is ready. Defaults to "off".', 'debug-bar-shortcodes' ),
				'preload'  => __( 'Specifies if and how the video should be loaded when the page loads. Defaults to "metadata".', 'debug-bar-shortcodes' ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
