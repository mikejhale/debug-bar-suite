<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info Defaults.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_Defaults' ) ) :

	/**
	 * Shortcode Info defaults.
	 */
	class Debug_Bar_Shortcode_Info_Defaults {

		/**
		 * Friendly name for the shortcode.
		 *
		 * @var string $name
		 */
		public $name = '';

		/**
		 * Longer description of what the shortcode is for.
		 *
		 * @var string $description
		 */
		public $description = '';

		/**
		 * Whether the shortcode is self-closing or not.
		 * - `true` if of form: [shortcode],
		 * - `false` if of form: [shortcode]...[/shortcode]
		 * Defaults to `null` (=unknown)
		 *
		 * @var bool|null $self_closing
		 */
		public $self_closing;

		/**
		 * Parameters which can be passed to the shortcode.
		 *
		 * Array format:
		 * - [required]	=> array Required parameters.
		 *                       Array format:
		 *                       - key   = name of the parameter.
		 *                       - value = description of the parameter.
		 * - [optional] => array Optional parameters.
		 *                       Follows same format as for required parameters.
		 *
		 * @var array $parameters
		 */
		public $parameters = array(
			'required' => array(),
			'optional' => array(),
		);

		/**
		 * Info URL.
		 *
		 * @var string $info_url
		 */
		public $info_url = '';

	} // End of class.

endif; // End of if class_exists.
