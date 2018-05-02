<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Gallery.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Gallery' ) ) :

	/**
	 * Information on the standard WP [gallery] shortcode.
	 */
	class Debug_Bar_Shortcode_Info_Gallery extends Debug_Bar_Shortcode_Info_Defaults {

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
		public $info_url = 'http://codex.wordpress.org/Gallery_Shortcode';


		/**
		 * Constructor - set some properties which need to be translated.
		 */
		public function __construct() {
			$this->name        = __( 'Image Gallery', 'debug-bar-shortcodes' );
			$this->description = __( 'The Gallery feature allows you to add one or more image galleries to your posts and pages.', 'debug-bar-shortcodes' );

			$this->parameters['optional'] = array(
				'orderby'    => __( 'Specify how to sort the display thumbnails. The default is "menu_order".', 'debug-bar-shortcodes' ),
				'order'      => __( 'Specify the sort order used to display thumbnails. ASC or DESC.', 'debug-bar-shortcodes' ),
				'columns'    => __( 'Specify the number of columns. The gallery will include a break tag at the end of each row, and calculate the column width as appropriate. The default value is 3. If columns is set to 0, no row breaks will be included.', 'debug-bar-shortcodes' ),
				'id'         => __( 'Specify the post ID. The gallery will display images which are attached to that post. The default behavior, if no ID is specified, is to display images attached to the current post.', 'debug-bar-shortcodes' ),
				'size'       => __( 'specify the image size to use for the thumbnail display. Valid values include "thumbnail", "medium", "large", "full". The default value is "thumbnail".', 'debug-bar-shortcodes' ),
				'itemtag'    => __( 'The name of the XHTML tag used to enclose each item in the gallery. The default is "dl".', 'debug-bar-shortcodes' ),
				'icontag'    => __( 'The name of the XHTML tag used to enclose each thumbnail icon in the gallery. The default is "dt".', 'debug-bar-shortcodes' ),
				'captiontag' => __( 'The name of the XHTML tag used to enclose each caption. The default is "dd".', 'debug-bar-shortcodes' ),
				'link'       => __( 'You can set it to "file" so each image will link to the image file. The default value links to the attachment\'s permalink.', 'debug-bar-shortcodes' ),
				'include'    => __( 'Comma separated attachment IDs to show only the images from these attachments. ', 'debug-bar-shortcodes' ),
				'exclude'    => __( 'Comma separated attachment IDs excludes the images from these attachments. Please note that include and exclude cannot be used together.', 'debug-bar-shortcodes' ),
			);
		}
	} // End of class.

endif; // End of if class_exists.
