<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Playlist.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Playlist' ) ) :

	/**
	 * Information on the standard WP [playlist] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Playlist extends Debug_Bar_Shortcode_Info_Defaults {

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
		public $info_url = 'http://codex.wordpress.org/Playlist_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Media Playlist', 'debug-bar-shortcodes' );
			$this->description = __( 'The playlist shortcode implements the functionality of displaying a collection of WordPress audio or video files in a post using a simple Shortcode.', 'debug-bar-shortcodes' );

			$this->parameters['optional'] = array(
				'type'         => __( 'Type of playlist to display. Accepts "audio" or "video". Defaults to "audio".', 'debug-bar-shortcodes' ),
				'order'        => __( 'Designates ascending or descending order of items in the playlist. Accepts "ASC", "DESC". Defaults to "ASC".', 'debug-bar-shortcodes' ),
				'orderby'      => __( 'Any column, or columns, to sort the playlist by. Accepts "rand" to play the list in random order. Defaults to "menu_order ID". If `$ids` are passed, this defaults to the order of the $ids array (\'post__in\').', 'debug-bar-shortcodes' ),
				'id'           => __( 'If an explicit `$ids` array is not present, this parameter will determine which attachments are used for the playlist. Defaults to the current post ID.', 'debug-bar-shortcodes' ),
				'ids'          => __( 'Create a playlist out of these explicit attachment IDs. If empty, a playlist will be created from all `$type` attachments of `$id`.', 'debug-bar-shortcodes' ),
				'exclude'      => __( 'List of specific attachment IDs to exclude from the playlist.', 'debug-bar-shortcodes' ),
				'style'        => __( 'Playlist style to use. Accepts "light" or "dark". Defaults to "light".', 'debug-bar-shortcodes' ),
				'tracklist'    => __( 'Whether to show or hide the playlist. Defaults to (bool) true.', 'debug-bar-shortcodes' ),
				'tracknumbers' => __( 'Whether to show or hide the numbers next to entries in the playlist. Defaults to (bool) true.', 'debug-bar-shortcodes' ),
				'images'       => __( 'Show or hide the video or audio thumbnail (Featured Image/post thumbnail). Defaults to (bool) true.', 'debug-bar-shortcodes' ),
				'artists'      => __( 'Whether to show or hide artist name in the playlist. Defaults to (bool) true.', 'debug-bar-shortcodes' ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
