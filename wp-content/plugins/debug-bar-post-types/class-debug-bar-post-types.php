<?php
/**
 * Debug Bar Post Types, a WordPress plugin.
 *
 * @package     WordPress\Plugins\Debug Bar Post Types
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Post-Types
 * @since       1.0
 * @version     2.0.0
 *
 * @copyright   2013-2018 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


if ( ! class_exists( 'Debug_Bar_Post_Types' ) && class_exists( 'Debug_Bar_Panel' ) ) {

	/**
	 * This class extends the functionality provided by the parent plugin "Debug Bar" by adding a
	 * panel showing information about the defined WP Post Types.
	 */
	class Debug_Bar_Post_Types extends Debug_Bar_Panel {

		/**
		 * Plugin version nr for use with enqueuing scripts and styles.
		 *
		 * @var string
		 */
		const DBPT_STYLES_VERSION = '2.0.0';

		/**
		 * Post Type names - used as column labels.
		 *
		 * @var array
		 */
		private $names = array();

		/**
		 * Custom Post Types, i.e. post types which are not by default included in WP.
		 *
		 * @var array
		 */
		private $custom_pt = array();

		/**
		 * Standard post type properties.
		 *
		 * @var array
		 */
		private $properties = array();

		/**
		 * Non-standard post type properties.
		 *
		 * @var array
		 */
		private $custom_prop = array();

		/**
		 * Post Type labels.
		 *
		 * @var array
		 */
		private $labels = array();

		/**
		 * Post Type capabilities.
		 *
		 * @var array
		 */
		private $caps = array();

		/**
		 * Number of non-standard post types registered.
		 *
		 * @var int
		 */
		private $count_cpt = 0;

		/**
		 * Whether to repeat the row labels on the other side of the table.
		 *
		 * @var bool
		 */
		private $double = false;


		/**
		 * Constructor.
		 */
		public function init() {
			$this->title( __( 'Post Types', 'debug-bar-post-types' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}


		/**
		 * Enqueue css file.
		 */
		public function enqueue_scripts() {
			$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
			wp_enqueue_style(
				Debug_Bar_Post_Types_Init::DBPT_NAME,
				plugins_url( 'css/' . Debug_Bar_Post_Types_Init::DBPT_NAME . $suffix . '.css', __FILE__ ),
				array( 'debug-bar' ),
				self::DBPT_STYLES_VERSION
			);
		}


		/**
		 * Should the tab be visible ?
		 * You can set conditions here so something will for instance only show on the front- or the
		 * back-end.
		 */
		public function prerender() {
			$this->set_visible( true );
		}


		/**
		 * Render the tab content.
		 */
		public function render() {

			$wp_post_types = $GLOBALS['wp_post_types'];
			$this->names   = array_keys( $wp_post_types );
			$count         = count( $wp_post_types );
			$this->double  = ( ( $count > 4 ) ? true : false ); // Whether to repeat the row labels on the other side of the table.

			if ( ! class_exists( 'Debug_Bar_Pretty_Output' ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'inc/debug-bar-pretty-output/class-debug-bar-pretty-output.php';
			}

			// Limit recursion depth if possible - method available since DBPO v1.4.
			if ( method_exists( 'Debug_Bar_Pretty_Output', 'limit_recursion' ) ) {
				Debug_Bar_Pretty_Output::limit_recursion( 2 );
			}


			echo '
		<h2><span>', esc_html__( 'Total Post Types:', 'debug-bar-post-types' ), '</span>', (int) $count, '</h2>';

			if ( is_array( $wp_post_types ) && $count > 0 ) {

				$this->collect_info( $wp_post_types );

				if ( ! empty( $this->custom_pt ) ) {
					$this->count_cpt = count( $this->custom_pt );
					echo '
		<h2><span>', esc_html__( 'Custom Post Types:', 'debug-bar-post-types' ), '</span>', (int) $this->count_cpt, '</h2>';
				}

				$this->render_standard_properties_table();
				$this->render_custom_properties_table();
				$this->render_capabilities_table();
				$this->render_labels_table();

			} else {
				echo '<p>', esc_html__( 'No post types found.', 'debug-bar-post-types' ), '</p>';
			}

			unset( $names, $properties, $caps );

			// Unset recursion depth limit if possible - method available since DBPO v1.4.
			if ( method_exists( 'Debug_Bar_Pretty_Output', 'unset_recursion_limit' ) ) {
				Debug_Bar_Pretty_Output::unset_recursion_limit();
			}
		}


		/**
		 * Collect the necessary information from the $post_types array.
		 *
		 * @param array $post_types Registered post types.
		 */
		private function collect_info( $post_types ) {
			foreach ( $post_types as $name => $tax_obj ) {
				$props = get_object_vars( $tax_obj );

				if ( ! empty( $props ) && is_array( $props ) ) {
					foreach ( $props as $key => $value ) {
						// Add to list of custom post_types.
						if ( '_builtin' === $key && true !== $value ) {
							$this->custom_pt[] = $name;
						}

						if ( is_object( $value ) && in_array( $key, array( 'cap', 'labels' ), true ) ) {
							$this->collect_caps_labels( $key, $name, $value );

						} else {
							// Standard properties.
							if ( property_exists( $post_types['post'], $key ) ) {
								$this->properties[ $key ][ $name ] = $value;

							} else {
								// Custom properties.
								$this->custom_prop[ $key ][ $name ] = $value;
							}
						}
					}
					unset( $key, $value );
				}
				unset( $props );
			}
			unset( $name, $tax_obj );
		}


		/**
		 * Collect the relevant information about capabilities and labels.
		 *
		 * @param string $key            Whether this is a capability object or a label object.
		 * @param string $name           Name of the post type this object applies to.
		 * @param object $caps_or_labels A capabilities or label object.
		 */
		private function collect_caps_labels( $key, $name, $caps_or_labels ) {
			$object_vars = get_object_vars( $caps_or_labels );

			if ( ! empty( $object_vars ) && is_array( $object_vars ) ) {
				foreach ( $object_vars as $k => $v ) {
					if ( 'cap' === $key ) {
						$this->caps[ $v ][ $name ] = $v;
					} elseif ( 'labels' === $key ) {
						$this->labels[ $k ][ $name ] = $v;
					}
				}
				unset( $k, $v );
			}
		}


		/**
		 * Create the properties table for the standard properties.
		 */
		private function render_standard_properties_table() {
			if ( count( $this->properties ) > 0 ) {
				$this->render_property_table(
					$this->properties,
					$this->names,
					__( 'Standard Post Type Properties:', 'debug-bar-post-types' ),
					$this->double
				);
			}
		}


		/**
		 * Create the properties table for the custom properties.
		 */
		private function render_custom_properties_table() {
			if ( count( $this->custom_prop ) > 0 ) {
				$this->render_property_table(
					$this->custom_prop,
					$this->custom_pt,
					__( 'Custom Post Type Properties:', 'debug-bar-post-types' ),
					( ( $this->count_cpt > 4 ) ? true : false )
				);
			}
		}


		/**
		 * Create the capabilities table.
		 */
		private function render_capabilities_table() {
			if ( count( $this->caps ) > 0 ) {
				$this->render_capability_table(
					$this->caps,
					$this->names,
					$this->double
				);
			}
		}


		/**
		 * Create the table for the defined labels.
		 */
		private function render_labels_table() {
			if ( count( $this->labels ) > 0 ) {
				$this->render_property_table(
					$this->labels,
					$this->names,
					__( 'Defined Labels:', 'debug-bar-post-types' ),
					$this->double
				);
			}
		}


		/**
		 * Create a property table for standard/custom properties.
		 *
		 * @since 1.2
		 *
		 * @param array  $properties Array of post type properties.
		 * @param array  $names      Array of post type names.
		 * @param string $table_name Translated name for this table.
		 * @param bool   $double     Whether or not to repeat the row labels at the end of the table.
		 */
		protected function render_property_table( $properties, $names, $table_name, $double ) {

			/* Create header row. */
			$header_row = '
		<tr>
			<th>' . esc_html__( 'Property', 'debug-bar-post-types' ) . '</th>';
			foreach ( $names as $name ) {
				$header_row .= '
			<th>' . esc_html( $name ) . '</th>';
			}
			unset( $name );
			if ( true === $double ) {
				$header_row .= '
			<th class="' . Debug_Bar_Post_Types_Init::DBPT_NAME . '-table-end">' . esc_html__( 'Property', 'debug-bar-post-types' ) . '</th>';
			}
			$header_row .= '
		</tr>';


			echo // WPCS: XSS ok.
			'
		<h3>', esc_html( $table_name ), '</h3>
		<table class="debug-bar-table ', Debug_Bar_Post_Types_Init::DBPT_NAME, '">
			<thead>
			', $header_row, '
			</thead>
			<tfoot>
			', $header_row, '
			</tfoot>
			<tbody>';
			unset( $header_row );


			/* Sort. */
			uksort( $properties, 'strnatcasecmp' );


			/* Output. */
			foreach ( $properties as $key => $value ) {
				echo '
			<tr>
				<th>', esc_html( $key ), '</th>';

				foreach ( $names as $name ) {
					echo '
				<td>';

					if ( isset( $value[ $name ] ) ) {
						if ( defined( 'Debug_Bar_Pretty_Output::VERSION' ) ) {
							echo Debug_Bar_Pretty_Output::get_output( $value[ $name ], '', true, '', true ); // WPCS: XSS ok.
						} else {
							// An old version of the pretty output class was loaded.
							Debug_Bar_Pretty_Output::output( $value[ $name ], '', true, '', true );
						}
					} else {
						echo '&nbsp;';
					}

					echo '
				</td>';
				}
				unset( $name );

				if ( true === $double ) {
					echo // WPCS: XSS ok.
					'
				<th class="', Debug_Bar_Post_Types_Init::DBPT_NAME, '-table-end">', esc_html( $key ), '</th>'; // WPCS: XSS ok.
				}

				echo '
			</tr>';
			}
			unset( $key, $value );

			echo '
			</tbody>
		</table>
	';
		}

		/**
		 * Create a capability table for standard/custom properties.
		 *
		 * @since 1.2
		 *
		 * @param array $caps   Array of post type capabilities.
		 * @param array $names  Array of post type names.
		 * @param bool  $double Whether or not to repeat the row labels at the end of the table.
		 */
		protected function render_capability_table( $caps, $names, $double ) {
			/* Create header row. */
			$header_row = '
			<tr>
				<th>' . esc_html__( 'Capability', 'debug-bar-post-types' ) . '</th>';
			foreach ( $names as $name ) {
				$header_row .= '
				<th>' . esc_html( $name ) . '</th>';
			}
			unset( $name );
			if ( true === $double ) {
				$header_row .= '
				<th>' . esc_html__( 'Capability', 'debug-bar-post-types' ) . '</th>';
			}
			$header_row .= '
			</tr>';


			echo // WPCS: XSS ok.
			'
		<h3>', esc_html__( 'Post Type Capabilities:', 'debug-bar-post-types' ), '</h3>
		<table class="debug-bar-table ', Debug_Bar_Post_Types_Init::DBPT_NAME, ' ', Debug_Bar_Post_Types_Init::DBPT_NAME, '-caps">
			<thead>
			', $header_row, '
			</thead>
			<tfoot>
			', $header_row, '
			</tfoot>
			<tbody>';
			unset( $header_row );


			/* Sort. */
			uksort( $caps, 'strnatcasecmp' );


			/* Output. */
			foreach ( $caps as $key => $value ) {
				echo '
			<tr>
				<th>', esc_html( $key ), '</th>';

				foreach ( $names as $name ) {
					$img = ( ( isset( $value[ $name ] ) ) ? 'check' : 'cross' );
					$alt = ( ( isset( $value[ $name ] ) ) ? esc_html__( 'Has capability', 'debug-bar-post-types' ) : esc_html__( 'Does not have capability', 'debug-bar-post-types' ) );

					echo '
				<td><img src="', esc_url( plugins_url( 'images/badge-circle-' . $img . '-16.png', __FILE__ ) ), '" width="16" height="16" alt="', esc_attr( $alt ), '" /></td>';
					unset( $img, $alt );
				}
				unset( $name );

				if ( true === $double ) {
					echo // WPCS: XSS ok.
					'
				<th class="', Debug_Bar_Post_Types_Init::DBPT_NAME, '-table-end">', esc_html( $key ), '</th>';
				}

				echo '
			</tr>';
			}
			unset( $key, $value );

			echo '
			</tbody>
		</table>
';
		}
	} // End of class Debug_Bar_Post_Types.

} // End of if class_exists wrapper.
