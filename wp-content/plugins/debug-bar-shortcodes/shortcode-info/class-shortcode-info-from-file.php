<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcode Info From File.
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

if ( ! class_exists( 'Debug_Bar_Shortcode_Info_From_File' ) ) :

	/**
	 * Information on a shortcode based on documentation found for the function the
	 * shortcode points to.
	 */
	class Debug_Bar_Shortcode_Info_From_File extends Debug_Bar_Shortcode_Info_Reflection {


		/**
		 * Constructor - set the properties.
		 *
		 * @param string $shortcode The shortcode for which to retrieve info.
		 */
		public function __construct( $shortcode = '' ) {
			parent::__construct( $shortcode );
			$this->set_info_properties();
		}


		/**
		 * Enrich shortcode information.
		 *
		 * If a Reflection object can be obtained for the function/method the shortcode points to,
		 * use the documentation of that function to enrich the available information.
		 */
		private function set_info_properties() {
			if ( $this->reflection_object instanceof ReflectionFunctionAbstract ) {
				$this->description = nl2br( $this->strip_comment_markers( $this->reflection_object->getDocComment() ) );

				$this->self_closing = true;
				if ( $this->reflection_object->getNumberOfRequiredParameters() > 1 ) {
					$this->self_closing = false;
				}

				$this->info_url = $this->get_plugin_url_from_file( $this->reflection_object->getFileName() );
			}
		}


		/**
		 * Strip all comment markings and extra whitespace from a comment string.
		 *
		 * Strips for each line of the comment:
		 * - '/*[*]', '//', '#', '*' from the beginning of a line.
		 * - '*\/' from the end of a line.
		 * - spaces and tabs from the beginning of a line.
		 * - carriage returns (\r) from the end of a line.
		 * - merges any combination of spaces and tabs into one space.
		 *
		 * @param string $comment The comment string to examine.
		 *
		 * @return string
		 */
		private function strip_comment_markers( $comment ) {
			static $search  = array(
				'`(^[\s]*(/\*+[\s]*(?:\*[ \t]*)?)|[\s]*(\*+/)[\s]*$|^[\s]*(\*+[ \t]*)|^[\s]*(/{2,})[\s]*|^[\s]*(#+)[\s]*)`m',
				'`^([ \t]+)`m',
				'`(\r)+[\n]?$`m',
				'`([ \t\r]{2,})`',
			);
			static $replace = array(
				'',
				'',
				'',
				' ',
			);

			// Parse out all the line endings and comment delimiters.
			$comment = trim( preg_replace( $search, $replace, trim( $comment ) ) );
			return $comment;
		}


		/**
		 * Get the URL where you can find more information about the shortcode.
		 *
		 * Inspired by Shortcode reference, heavily adjusted to work more accurately.
		 * Source: https://wordpress.org/plugins/shortcode-reference/
		 *
		 * @param string $path_to_file Path to file containing the callback function.
		 *
		 * @return string URL.
		 */
		protected function get_plugin_url_from_file( $path_to_file ) {

			/* Make sure the paths use the same slashing to make them comparable. */
			$path_to_file       = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path_to_file );
			$wp_abs_path        = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, ABSPATH );
			$wp_includes_path   = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, ABSPATH . WPINC ) . DIRECTORY_SEPARATOR;
			$wp_plugins_path    = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, WP_PLUGIN_DIR ) . DIRECTORY_SEPARATOR;
			$wp_mu_plugins_path = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, WPMU_PLUGIN_DIR ) . DIRECTORY_SEPARATOR;

			/* Check what type of file this is. */
			if ( false !== strpos( $path_to_file, $wp_includes_path ) ) {
				// WP native.
				return 'http://codex.wordpress.org/index.php?title=Special:Search&search=' . urlencode( $this->shortcode ) . '_Shortcode';
			}


			$is_plugin       = strpos( $path_to_file, $wp_plugins_path );
			$is_mu_plugin    = strpos( $path_to_file, $wp_mu_plugins_path );
			$plugin_data     = array();
			$plugin_basename = '';

			/* Is this a plugin in the normal plugin directory ? */
			if ( false !== $is_plugin ) {
				// Plugin in the plugins directory.
				$relative_path = substr( $path_to_file, ( $is_plugin + strlen( $wp_plugins_path ) ) );

				if ( function_exists( 'get_plugins' ) && false !== strpos( $relative_path, DIRECTORY_SEPARATOR ) ) {
					// Subdirectory plugin.
					$folder  = substr( $relative_path, 0, strpos( $relative_path, DIRECTORY_SEPARATOR ) );
					$folder  = DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
					$plugins = get_plugins( $folder );

					/*
					   We'd expect only one file in the directory to have plugin data, otherwise we have
					   a problem as we won't know which file is the parent of the one containing the shortcode.
					 */
					if ( is_array( $plugins ) && 1 === count( $plugins ) ) {
						// Only one item, but we don't know the key, use foreach to set the variables we need.
						foreach ( $plugins as $plugin_basename => $plugin_data ) {
							break;
						}
					}

					/*
					   So in the case of several plugins within a directory - check if the file containing
					   the shortcode callback is one of the plugin main files. If so, accept.
					   Otherwise, ignore altogether.
					 */
					else {
						$found = false;
						foreach ( $plugins as $plugin_basename => $plugin_data ) {
							if ( false !== strpos( $relative_path, DIRECTORY_SEPARATOR . $plugin_basename ) ) {
								$found = true;
								break;
							}
						}
						if ( false === $found ) {
							unset( $plugin_basename, $plugin_data );
						}
						unset( $found );
					}
					unset( $plugins, $folder );
				}
				elseif ( function_exists( 'get_plugin_data' ) ) {
					// File directly in the plugins dir, just get straight plugin_data.
					$plugin_basename = $relative_path;
					$plugin_data     = get_plugin_data( $path_to_file, false, false );
				}
				unset( $relative_path );
			}
			/* Is this a plugin in the mu plugin directory ? (`get_plugin_data()` only available on admin side.) */
			elseif ( function_exists( 'get_plugin_data' ) && false !== $is_mu_plugin ) {
				$relative_path = substr( $path_to_file, ( $is_mu_plugin + strlen( $wp_mu_plugins_path ) ) );

				if ( false !== strpos( $relative_path, DIRECTORY_SEPARATOR ) ) {
					// Subdirectory file, presume the mu-dir plugin bootstrap file is called directory-name.php.
					$relative_path = substr( $relative_path, 0, strpos( $relative_path, DIRECTORY_SEPARATOR ) ) . '.php';
				}
				$plugin_basename = $relative_path;
				$plugin_data     = get_plugin_data( $wp_mu_plugins_path . $relative_path, false, false );
				unset( $relative_path );
			}

			/* Let's see if we've got some results. */
			if ( is_array( $plugin_data ) && ! empty( $plugin_data ) ) {
				if ( isset( $plugin_data['PluginURI'] ) && trim( $plugin_data['PluginURI'] ) !== '' ) {
					return trim( $plugin_data['PluginURI'] );
				}
				elseif ( isset( $plugin_data['AuthorURI'] ) && trim( $plugin_data['AuthorURI'] ) !== '' ) {
					return trim( $plugin_data['AuthorURI'] );
				}
			}

			/* Not exited yet ? Ok, then we didn't have either or the info items, let's try another way. */
			if ( '' !== $plugin_basename ) {
				$uri = $this->wp_repo_exists( $plugin_basename );
				if ( false !== $uri ) {
					return $uri;
				}
				else {
					return 'http://www.google.com/search?q=Wordpress+' . urlencode( '"' . $plugin_basename . '"' ) . '+shortcode+' . urlencode( '"' . $this->shortcode . '"' );
				}
			}

			/*
			   If all else fails, Google is your friend, but let's try not to reveal our server path.
			 */
			$is_wp = strpos( $path_to_file, $wp_abs_path );
			if ( false !== $is_wp ) {
				$sort_of_safe_path = substr( $path_to_file, ( $is_wp + strlen( $wp_abs_path ) ) );
				return 'http://www.google.com/search?q=Wordpress+' . urlencode( '"' . $sort_of_safe_path . '"' ) . '+shortcode+' . urlencode( '"' . $this->shortcode . '"' );
			}
			return 'http://www.google.com/search?q=Wordpress+shortcode+' . urlencode( '"' . $this->shortcode . '"' );
		}


		/**
		 * Try to check if a wp plugin repository exists for a given plugin.
		 *
		 * @todo check WP for alternative way to do this, i.e. using flexible http_base class or something
		 * which will auto switch depending on call methods available.
		 *
		 * @param string $plugin_basename Plugin basename in the format dir/file.php.
		 *
		 * @return string|false URL or false if unsuccessful.
		 */
		private function wp_repo_exists( $plugin_basename ) {
			if ( ! extension_loaded( 'curl' ) || '' === $plugin_basename ) {
				// May be check using another method ? Nah, google is good enough.
				return false;
			}

			/* Set up curl. */
			$curl = curl_init();

			// Issue a HEAD request.
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_NOBODY, true );
			// Follow any redirects.
			$open_basedir = ini_get( 'open_basedir' );
			if ( false === $this->ini_get_bool( 'safe_mode' ) && ( ( ! isset( $open_basedir ) || empty( $open_basedir ) ) || 'none' === $open_basedir ) ) {
				curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt( $curl, CURLOPT_MAXREDIRS, 5 );
			}
			unset( $open_basedir );
			// Bypass servers which refuse curl.
			curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
			// Set a time-out.
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 5 );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );


			/* Figure out what the repo should be called. */
			if ( strpos( $plugin_basename, DIRECTORY_SEPARATOR ) ) {
				$plugin_basename = substr( $plugin_basename, 0, strpos( $plugin_basename, DIRECTORY_SEPARATOR ) );
			}
			if ( strpos( $plugin_basename, '.php' ) ) {
				$plugin_basename = substr( $plugin_basename, 0, strpos( $plugin_basename, '.php' ) );
			}

			/* Check if it exists. */
			if ( '' !== $plugin_basename ) {
				$plugin_uri = 'https://wordpress.org/plugins/' . urlencode( $plugin_basename );

				/* Get the http headers for the given url. */
				curl_setopt( $curl, CURLOPT_URL, $plugin_uri );
				$header = curl_exec( $curl );

				/* If we didn't get an error, interpret the headers. */
				if ( ( false !== $header && ! empty( $header ) ) && ( 0 === curl_errno( $curl ) ) ) {
					/* Get the http status. */
					$statuscode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
					if ( false === $statuscode && preg_match( '/^HTTP\/1\.[01] (\d\d\d)/', $header, $matches ) ) {
						$statuscode = (int) $matches[1];
					}

					/* No http error response, so presume valid uri. */
					if ( 400 > $statuscode ) {
						curl_close( $curl );
						return $plugin_uri;
					}
				}
			}

			curl_close( $curl );
			return false;
		}


		/* ************** HELPER METHODS ************** */


		/**
		 * Test a boolean PHP ini value.
		 *
		 * @since 3.0
		 *
		 * @param string $ini_key Key of the value you want to get.
		 *
		 * @return bool
		 */
		private function ini_get_bool( $ini_key ) {
			$value = ini_get( $ini_key );

			switch ( strtolower( $value ) ) {
				case 'on':
				case 'yes':
				case 'true':
					return 'assert.active' !== $ini_key;

				default:
					return (bool) (int) $value;
			}
		}
	} // End of class.

endif; // End of if class_exists.
