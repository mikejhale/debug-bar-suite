<?php
/**
 * Debug Bar Shortcodes - Debug Bar Shortcodes Render.
 *
 * @package     WordPress\Plugins\Debug Bar Shortcodes
 * @author      Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 * @link        https://github.com/jrfnl/Debug-Bar-Shortcodes
 * @since       1.0
 * @since       2.0 Class renamed - was: Debug_Bar_Shortcodes_Info
 *
 * @copyright   2013-2016 Juliette Reinders Folmer
 * @license     http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 */

// Avoid direct calls to this file.
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


/**
 * The classes in this file extend the functionality provided by the parent plugin "Debug Bar".
 */
if ( ! class_exists( 'Debug_Bar_Shortcodes_Render' ) ) :

	/**
	 * Debug Bar Shortcodes - Debug Bar Panel Renderer.
	 */
	class Debug_Bar_Shortcodes_Render {

		/**
		 * Plugin name for use in localization, class names etc.
		 *
		 * @var	string $name
		 */
		public static $name = 'debug-bar-shortcodes';

		/**
		 * The amount of shortcodes before the table header will be doubled at the bottom of the table.
		 *
		 * @var int
		 */
		public $double_min = 8;


		/**
		 * Render the actual panel.
		 */
		public function display() {
			$shortcodes = $GLOBALS['shortcode_tags'];

			$count  = count( $shortcodes );
			$double = ( ( $count >= $this->double_min ) ? true : false ); // Whether to repeat the row labels at the bottom of the table.

			echo '
		<h2><span>', esc_html__( 'Total Registered Shortcodes:', 'debug-bar-shortcodes' ), '</span>', absint( $count ), '</h2>';


			$output = '';

			if ( is_array( $shortcodes ) && ! empty( $shortcodes ) ) {

				uksort( $shortcodes, 'strnatcasecmp' );

				$is_singular = ( is_main_query() && is_singular() );
				$header_row  = $this->render_table_header( $is_singular );

				$output .= '
				<table id="' . esc_attr( self::$name ) . '">
					<thead>' . $header_row . '</thead>
					' . ( ( true === $double ) ? '<tfoot>' . $header_row . '</tfoot>' : '' ) . '
					<tbody>';


				$i = 1;
				foreach ( $shortcodes as $shortcode => $callback ) {

					$sc_info     = new Debug_Bar_Shortcode_Info( $shortcode );
					$info        = $sc_info->get_info_object();
					$has_details = $sc_info->has_details();
					$class       = ( ( $i % 2 ) ? '' : ' class="even"' );

					$output .= '
						<tr' . $class . '>
							<td>' . $i . '</td>
							<td class="column-title">
								<strong>[<code>' . esc_html( $shortcode ) . '</code>]</strong>
								' . $this->render_action_links( $shortcode, $has_details, $info ) . '
							</td>
							<td>' . $this->determine_callback_type( $callback ) . '</td>';

					if ( true === $is_singular ) {
						$in_use  = $this->has_shortcode( $shortcode );
						$output .= '
							<td>' . $this->render_image_based_on_bool( array( 'true' => esc_html__( 'Shortcode is used', 'debug-bar-shortcodes' ), 'false' => esc_html__( 'Shortcode not used', 'debug-bar-shortcodes' ) ), $in_use, true ) . '</td>
							<td>' . ( ( true === $in_use ) ? $this->find_shortcode_usage( $shortcode ) : '&nbsp;' ) . '</td>';
						unset( $in_use );
					}

					$output .= '
						</tr>';

					if ( true === $has_details ) {
						$class   = ( ( $i % 2 ) ? ' class="' . esc_attr( self::$name . '-details' ) . '"' : ' class="even ' . esc_attr( self::$name . '-details' ) . '"' );
						$output .= '
						<tr' . $class . '>
							<td>&nbsp;</td>
							<td colspan="' . ( ( true === $is_singular ) ? 4 : 2 ) . '">
								' . $this->render_details_table( $shortcode, $info ) . '
							</td>
						</tr>
						';
					}
					$i++;
				}
				unset( $shortcode, $callback, $sc_info, $info, $has_details, $class, $i );

				$output .= '
					</tbody>
				</table>';

			}
			else {
				$output = '<p>' . esc_html__( 'No shortcodes found.', 'debug-bar-shortcodes' ) . '</p>';
			}

			echo $output; // WPCS: xss ok.
		}


		/**
		 * Generate the table header/footer row html.
		 *
		 * @param bool $is_singular Whether we are viewing a singular page/post/post type.
		 *
		 * @return string
		 */
		private function render_table_header( $is_singular ) {
			$output = '<tr>
							<th>#</th>
							<th>' . esc_html__( 'Shortcode', 'debug-bar-shortcodes' ) . '</th>
							<th>' . esc_html__( 'Rendered by', 'debug-bar-shortcodes' ) . '</th>';

			if ( true === $is_singular ) {
				$output .= '
							<th>' . esc_html__( 'In use?', 'debug-bar-shortcodes' ) . '</th>
							<th>' . esc_html__( 'Usage', 'debug-bar-shortcodes' ) . '</th>';
			}

			$output .= '</tr>';

			return $output;
		}


		/**
		 * Generate the action links for a shortcode.
		 *
		 * @param string                             $shortcode   Current shortcode.
		 * @param bool                               $has_details Whether or not the $info is equal to the defaults.
		 * @param \Debug_Bar_Shortcode_Info_Defaults $info        Shortcode info.
		 *
		 * @return string
		 */
		private function render_action_links( $shortcode, $has_details, $info ) {
			$links = array();

			if ( true === $has_details ) {
				$links[] = '<a href="#" class="' . esc_attr( self::$name . '-view-details' ) . '" title="' . esc_html__( 'View more detailed information about the shortcode.', 'debug-bar-shortcodes' ) . '">' . esc_html__( 'View details', 'debug-bar-shortcodes' ) . '</a>';
			}
			else {
				$links[] = '<a href="#' . esc_attr( $shortcode ) . '" class="' . esc_attr( self::$name . '-get-details' ) . '" title="' . esc_html__( 'Try and retrieve more detailed information about the shortcode.', 'debug-bar-shortcodes' ) . '">' . esc_html__( 'Retrieve details', 'debug-bar-shortcodes' ) . '</a>';
			}

			$links[] = '<a href="#' . esc_attr( $shortcode ) . '" class="' . esc_attr( self::$name . '-find' ) . '" title="' . esc_html__( 'Find out where this shortcode is used (if at all)', 'debug-bar-shortcodes' ) . '">' . esc_html__( 'Find uses', 'debug-bar-shortcodes' ) . '</a>';

			if ( true === $has_details && '' !== $info->info_url ) {
				$links[] = $this->render_view_online_link( $info->info_url );
			}

			return '<span class="spinner"></span><div class="row-actions">' . implode( ' | ', $links ) . '</div>';
		}


		/**
		 * Generate 'View online' link.
		 *
		 * @internal Separated from render_action_links() to also be able to use it as supplemental for ajax retrieve.
		 *
		 * @param string $url The URL to link to.
		 *
		 * @return string
		 */
		private function render_view_online_link( $url ) {
			return '<a href="' . esc_url( $url ) . '" target="_blank" title="' . __( 'View extended info about the shortcode on the web', 'debug-bar-shortcodes' ) . '" class="' . esc_attr( self::$name . '-external-link' ) . '" >' . esc_html__( 'View online', 'debug-bar-shortcodes' ) . '</a>';
		}


		/**
		 * Function to retrieve a displayable string representing the callback.
		 *
		 * @internal Similar to callback determination in the Debug Bar Actions and Filters plugin,
		 * keep them in line with each other.
		 *
		 * @param mixed $callback A callback.
		 *
		 * @return string
		 */
		private function determine_callback_type( $callback ) {

			if ( ( ! is_string( $callback ) && ! is_object( $callback ) ) && ( ! is_array( $callback ) || ( is_array( $callback ) && ( ! is_string( $callback[0] ) && ! is_object( $callback[0] ) ) ) ) ) {
				// Type 1 - not a callback.
				return '';
			}
			elseif ( self::is_closure( $callback ) ) {
				// Type 2 - closure.
				return '[<em>closure</em>]';
			}
			elseif ( ( is_array( $callback ) || is_object( $callback ) ) && self::is_closure( $callback[0] ) ) {
				// Type 3 - closure within an array/object.
				return '[<em>closure</em>]';
			}
			elseif ( is_string( $callback ) && false === strpos( $callback, '::' ) ) {
				// Type 4 - simple string function (includes lambda's).
				return sanitize_text_field( $callback ) . '()';
			}
			elseif ( is_string( $callback ) && false !== strpos( $callback, '::' ) ) {
				// Type 5 - static class method calls - string.
				return '[<em>class</em>] ' . str_replace( '::', ' :: ', sanitize_text_field( $callback ) ) . '()';
			}
			elseif ( is_array( $callback ) && ( is_string( $callback[0] ) && is_string( $callback[1] ) ) ) {
				// Type 6 - static class method calls - array.
				return '[<em>class</em>] ' . sanitize_text_field( $callback[0] ) . ' :: ' . sanitize_text_field( $callback[1] ) . '()';
			}
			elseif ( is_array( $callback ) && ( is_object( $callback[0] ) && is_string( $callback[1] ) ) ) {
				// Type 7 - object method calls.
				return '[<em>object</em>] ' . get_class( $callback[0] ) . ' -> ' . sanitize_text_field( $callback[1] ) . '()';
			}
			else {
				// Type 8 - undetermined.
				return '<pre>' . var_export( $callback, true ) . '</pre>';
			}
		}


		/**
		 * Whether the current (singular) post contains the specified shortcode.
		 *
		 * Freely based on WP native implementation:
		 * Source	http://core.trac.wordpress.org/browser/trunk/src/wp-includes/shortcodes.php#L144
		 * Last compared against source: 2015-12-14.
		 *
		 * @global object $post Current post object.
		 *
		 * @static array $matches Regex matches for the post in the form [id] -> [matches].
		 *
		 * @param string $shortcode The shortcode to check for.
		 *
		 * @return bool
		 */
		private function has_shortcode( $shortcode ) {
			static $matches;

			/* Have we got post content ? */
			if ( ! is_object( $GLOBALS['post'] ) || ! isset( $GLOBALS['post']->post_content ) || '' === $GLOBALS['post']->post_content ) {
				return false;
			}

			$content = $GLOBALS['post']->post_content; // Current post.

			/* Use WP native function if available (WP 3.6+). */
			if ( function_exists( 'has_shortcode' ) ) {
				return has_shortcode( $content, $shortcode );
			}


			/* Otherwise use adjusted copy of the native function (WP < 3.6). */
			$post_id = $GLOBALS['post']->ID;

			// Cache retrieved shortcode matches in a static for efficiency.
			if ( ! isset( $matches ) || ( is_array( $matches ) && ! isset( $matches[ $post_id ] ) ) ) {
				preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches[ $post_id ], PREG_SET_ORDER );
			}

			if ( empty( $matches[ $post_id ] ) ) {
				return false;
			}
			foreach ( $matches[ $post_id ] as $found ) {
				if ( $shortcode === $found[2] ) {
					return true;
				}
				elseif ( ! empty( $shortcode[5] ) && has_shortcode( $shortcode[5], $shortcode ) ) {
					return true;
				}
			}
			return false;
		}


		/**
		 * Find the uses of a shortcode within the current post.
		 *
		 * @param string      $shortcode The requested shortcode.
		 * @param string|null $content   (optional) Content to search through for the shortcode.
		 *                               Defaults to the content of the current post/page/etc.
		 *
		 * @return string
		 */
		private function find_shortcode_usage( $shortcode, $content = null ) {
			$result = __( 'Not found', 'debug-bar-shortcodes' );

			if ( ! isset( $content ) && ( ! isset( $GLOBALS['post'] ) || ! is_object( $GLOBALS['post'] ) || ! isset( $GLOBALS['post']->post_content ) ) ) {
				return $result;
			}

			if ( ! isset( $content ) ) {
				$content = $GLOBALS['post']->post_content;
			}

			$shortcode = preg_quote( $shortcode );
			$regex     = '`(?:^|[^\[])(\[' . $shortcode . '[^\]]*\])(?:.*?(\[/' . $shortcode . '\])(?:[^\]]|$))?`s';
			$count     = preg_match_all( $regex, $content, $matches, PREG_SET_ORDER );


			if ( is_int( $count ) && $count > 0 ) {
				// Only one result, keep it simple.
				if ( 1 === $count ) {
					$result = '<code>' . esc_html( $matches[0][1] );
					if ( isset( $matches[0][2] ) && '' !== $matches[0][2] ) {
						$result .= '&hellip;' . esc_html( $matches[0][2] );
					}
					$result .= '</code>';
				}
				// More results, let's make it a neat list.
				else {
					$result = '<ol>';

					foreach ( $matches as $match ) {
						$result .= '<li><code>' . esc_html( $match[1] );

						if ( isset( $match[2] ) && '' !== $match[2] ) {
							$result .= '&hellip;' . esc_html( $match[2] );
						}
						$result .= '</code></li>';
					}
					unset( $match );

					$result .= '</ol>';
				}
			}
			return $result;
		}


		/**
		 * Retrieve a html image tag based on a value.
		 *
		 * @param array     $alt        Array with only three allowed keys:
		 *                                ['true']	=>	Alt value for true image.
		 *                                ['false']	=>	Alt value for false image.
		 *                                ['null']	=>	Alt value for null image (status unknown).
		 * @param bool|null $bool       The value to base the output on, either boolean or null.
		 * @param bool      $show_false Whether to show an image if false or to return an empty string.
		 * @param bool      $show_null  Whether to show an image if null or to return an empty string.
		 *
		 * @return string
		 */
		private function render_image_based_on_bool( $alt = array(), $bool = null, $show_false = false, $show_null = false ) {
			static $images;

			if ( ! isset( $images ) ) {
				$images = array(
					'true'		=> plugins_url( 'images/badge-circle-check-16.png', __FILE__ ),
					'false'		=> plugins_url( 'images/badge-circle-cross-16.png', __FILE__ ),
					'null'		=> plugins_url( 'images/help.png', __FILE__ ),
				);
			}

			$img = ( ( isset( $bool ) ) ? ( ( true === $bool ) ? $images['true'] : $images['false'] ) : $images['null'] );

			$alt_value = '';
			if ( isset( $bool ) ) {
				if ( true === $bool && isset( $alt['true'] ) ) {
					$alt_value = $alt['true'];
				}
				elseif ( false === $bool && isset( $alt['false'] ) ) {
					$alt_value = $alt['false'];
				}
			}
			elseif ( isset( $alt['null'] ) ) {
				$alt_value = $alt['null'];
			}

			$title_tag = '';
			$alt_tag   = '';
			if ( '' !== $alt_value ) {
				$title_tag = ' title="' . esc_attr( $alt_value ) . '"';
				$alt_tag   = ' alt="' . esc_attr( $alt_value ) . '"';
			}

			$return = '';
			if ( ( null === $bool && true === $show_null ) || ( true === $bool || ( false === $bool && true === $show_false ) ) ) {
				$return = '<img src="' . esc_url( $img ) . '" width="16" height="16"' . $alt_tag . $title_tag . '/>';
			}
			return $return;
		}


		/**
		 * Generate the html for a shortcode detailed info table.
		 *
		 * @param string                             $shortcode Current shortcode.
		 * @param \Debug_Bar_Shortcode_Info_Defaults $info      Shortcode info.
		 *
		 * @return string
		 */
		private function render_details_table( $shortcode, $info ) {
			$rows = array();

			if ( '' !== $info->name ) {
				$rows['name'] = '
								<tr>
									<th colspan="2">' . esc_html__( 'Name', 'debug-bar-shortcodes' ) . '</th>
									<td>' . esc_html( $info->name ) . '</td>
								</tr>';
			}


			if ( '' !== $info->description ) {
				$rows['description'] = '
								<tr>
									<th colspan="2">' . esc_html__( 'Description', 'debug-bar-shortcodes' ) . '</th>
									<td>' . $info->description . '</td>
								</tr>';
			}


			$rows['syntax'] = $this->render_details_syntax_row( $shortcode, $info );


			if ( '' !== $info->info_url ) {
				$rows['info_url'] = '
								<tr>
									<th colspan="2">' . esc_html__( 'Info Url', 'debug-bar-shortcodes' ) . '</th>
									<td><a href="' . esc_url( $info->info_url ) . '" target="_blank" class="' . esc_attr( self::$name . '-external-link' ) . '">' . esc_html( $info->info_url ) . '</a></td>
								</tr>';
			}


			if ( ! empty( $info->parameters['required'] ) ) {
				$rows['rp'] = $this->render_details_parameter_row(
					$info,
					'required',
					__( 'Required parameters', 'debug-bar-shortcodes' )
				);
			}


			if ( ! empty( $info->parameters['optional'] ) ) {
				$rows['op'] = $this->render_details_parameter_row(
					$info,
					'optional',
					__( 'Optional parameters', 'debug-bar-shortcodes' )
				);
			}


			/* Ignore the result if syntax is the only info row (as it's always there). */
			if ( 1 >= count( $rows ) && isset( $rows['syntax'] ) ) {
				$output = '';
			}
			else {
				$output = '
								<h4>' . esc_html__( 'Shortcode details', 'debug-bar-shortcodes' ) . '</h4>
								<table>' . implode( $rows ) . '
								</table>';
			}
			return $output;
		}


		/**
		 * Generate the html for a shortcode detailed info table syntax row.
		 *
		 * @param string                             $shortcode Current shortcode.
		 * @param \Debug_Bar_Shortcode_Info_Defaults $info      Shortcode info.
		 *
		 * @return string
		 */
		private function render_details_syntax_row( $shortcode, $info ) {
			$row = '
								<tr>
									<th colspan="2">' . esc_html__( 'Syntax', 'debug-bar-shortcodes' ) . '</th>
									<td>';

			if ( isset( $info->self_closing ) ) {
				$param = ( ( ! empty( $info->parameters['required'] ) || ! empty( $info->parameters['optional'] ) ) ? ' <em>[parameters]</em> ' : '' );
				if ( true === $info->self_closing ) {
					$row .= '<code>[' . esc_html( $shortcode ) . $param . ' /]</code>';
				}
				else {
					$row .= '<code>[' . esc_html( $shortcode ) . $param . '] &hellip; [/' . esc_html( $shortcode ) . ']</code>';
				}
			}
			else {
				$row .= '<em>' . esc_html__( 'Unknown', 'debug-bar-shortcodes' ) . '</em>';
			}

			$row .= '</td>
								</tr>';

			return $row;
		}


		/**
		 * Generate the html for a shortcode detailed info table parameter row.
		 *
		 * @param \Debug_Bar_Shortcode_Info_Defaults $info      Shortcode info.
		 * @param string                             $type      Parameter type: 'required' or 'optional'.
		 * @param string                             $label     Parameter label.
		 *
		 * @return string
		 */
		private function render_details_parameter_row( $info, $type, $label ) {
			$row   = '
							<tr class="' . esc_attr( self::$name . '-sc-parameters' ) . '">
								<th rowspan="' . count( $info->parameters[ $type ] ) . '">' . esc_html( $label ) . '</th>';
			$first = true;
			foreach ( $info->parameters[ $type ] as $pm => $explain ) {
				if ( true !== $first ) {
					$row .= '
							<tr>';
				}
				else {
					$first = false;
				}
				$row .= '
								<td>' . esc_html( $pm ) . '</td>
								<td>' . esc_html( $explain ) . '</td>
							</tr>';
			}
			return $row;
		}



		/* ************** METHODS TO HANDLE AJAX REQUESTS ************** */


		/**
		 * Try and retrieve more information about the shortcode from the actual php code.
		 *
		 * @param string $shortcode Validated shortcode.
		 * @param string $action    The AJAX action which led to this function being called.
		 *
		 * @return void
		 */
		public function ajax_retrieve_details( $shortcode, $action ) {
			$sc_info = new Debug_Bar_Shortcode_Info( $shortcode, true );

			if ( false === $sc_info->has_details() ) {
				$response = array(
					'id'     => 0,
					'data'   => '',
					'action' => $action,
				);
				$this->send_ajax_response( $response );
				exit;
			}

			$info     = $sc_info->get_info_object();
			$response = array(
				'id'        => 1,
				'data'      => $this->render_details_table( $shortcode, $info ),
				'action'    => $action,
				'tr_class'  => self::$name . '-details',
			);
			if ( isset( $info->info_url ) && '' !== $info->info_url ) {
				$response['supplemental'] = $this->render_view_online_link( $info->info_url );
			}

			$this->send_ajax_response( $response );
			exit;
		}


		/**
		 * Find out if a shortcode is used anywhere.
		 *
		 * Liberally nicked from TR All Shortcodes plugin & adjusted based on WP posts-list-table code.
		 * Source: https://wordpress.org/plugins/tr-all-shortcodes/
		 * Source: http://core.trac.wordpress.org/browser/trunk/src/wp-admin/includes/class-wp-posts-list-table.php#L473
		 *
		 * @param string $shortcode Validated shortcode.
		 * @param string $action    The AJAX action which led to this function being called.
		 *
		 * @return void
		 */
		public function ajax_find_shortcode_uses( $shortcode, $action ) {

			// '_' is a wildcard in mysql, so escape it.
			$query = $GLOBALS['wpdb']->prepare(
				'select * from `' . $GLOBALS['wpdb']->posts . '`
					where `post_status` <> "inherit"
						and `post_type` <> "attachment"
						and `post_content` like %s
					order by `post_type` ASC, `post_date` DESC;',
				'%[' . str_replace( '_', '\_', $shortcode ) . '%'
			);
			$posts = $GLOBALS['wpdb']->get_results( $query );


			/* Do we have posts ? */
			if ( 0 === $GLOBALS['wpdb']->num_rows ) {
				$response = array(
					'id'     => 0,
					'data'   => '',
					'action' => $action,
				);
				$this->send_ajax_response( $response );
				exit;
			}


			/* Ok, we've found some posts using the shortcode. */
			$output = '
						<h4>' . __( 'Shortcode found in the following posts/pages/etc:', 'debug-bar-shortcodes' ) . '</h4>
						<table>
							<thead>
								<tr>
									<th>#</th>
									<th>' . esc_html__( 'Title', 'debug-bar-shortcodes' ) . '</th>
									<th>' . esc_html__( 'Post Type', 'debug-bar-shortcodes' ) . '</th>
									<th>' . esc_html__( 'Status', 'debug-bar-shortcodes' ) . '</th>
									<th>' . esc_html__( 'Author', 'debug-bar-shortcodes' ) . '</th>
									<th>' . esc_html__( 'Shortcode usage(s)', 'debug-bar-shortcodes' ) . '</th>
								</tr>
							</thead>
							<tbody>';


			foreach ( $posts as $i => $post ) {
				$edit_link          = get_edit_post_link( $post->ID );
				$title              = _draft_or_post_title( $post->ID );
				$post_type_object   = get_post_type_object( $post->post_type );
				$can_edit_post      = current_user_can( 'edit_post', $post->ID );
				$post_status_string = $this->get_post_status_string( $post->post_status );

				$actions = array();
				if ( $can_edit_post && 'trash' !== $post->post_status ) {
					/* translators: no need to translate, WP standard translation will be used. */
					$actions['edit'] = '<a href="' . $edit_link . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">';
					/* translators: no need to translate, WP standard translation will be used. */
					$actions['edit'] .= __( 'Edit' ) . '</a>';
				}
				if ( $post_type_object->public ) {
					if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ), true ) ) {
						if ( $can_edit_post ) {
							/* translators: no need to translate, WP standard translation will be used. */
							$actions['view'] = '<a href="' . esc_url( apply_filters( 'preview_post_link', set_url_scheme( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">';
							/* translators: no need to translate, WP standard translation will be used. */

							$actions['view'] .= __( 'Preview' ) . '</a>';

						}
					}
					elseif ( 'trash' !== $post->post_status ) {
						/* translators: no need to translate, WP standard translation will be used. */
						$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">';
						/* translators: no need to translate, WP standard translation will be used. */
						$actions['view'] .= __( 'View' ) . '</a>';
					}
				}


				$output .= '
								<tr>
									<td>' . ( $i + 1 ) . '</td>
									<td class="column-title"><strong>' . $title . '</strong>';

				if ( ! empty( $actions ) ) {
					$output .= '<div class="row-actions">' . implode( ' | ', $actions ) . '</div>';
				}

				$output .= '
									</td>
									<td>' . esc_html( $post_type_object->labels->singular_name ) . '</td>
									<td>' . esc_html( $post_status_string ) . '</td>
									<td>' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</td>
									<td>' . $this->find_shortcode_usage( $shortcode, $post->post_content ) . '</td>
								</tr>';
			}
			unset( $i, $post );

			$output .= '
							</tbody>
						</table>';


			$response = array(
				'id'        => 1,
				'data'      => $output,
				'action'    => $action,
				'tr_class'  => self::$name . '-uses',
			);
			$this->send_ajax_response( $response );
			exit;
		}


		/**
		 * Translate a post status keyword to a human readable localized string.
		 *
		 * @param string $post_status The post status to translate.
		 *
		 * @return string
		 */
		private function get_post_status_string( $post_status ) {
			switch ( $post_status ) {
				case 'publish':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Published' );
					break;

				case 'future':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Scheduled' );
					break;

				case 'private':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Private' );
					break;

				case 'pending':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Pending Review' );
					break;

				case 'draft':
				case 'auto-draft':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Draft' );
					break;

				case 'trash':
					/* translators: no need to translate, WP standard translation will be used. */
					$post_status_string = __( 'Trash' );
					break;

				default:
					$post_status_string = __( 'Unknown', 'debug-bar-shortcodes' );
					break;
			}

			return $post_status_string;
		}


		/**
		 * Send ajax response.
		 *
		 * @param array $response Part response in the format:
		 *                          [id]           => 0 = no result, 1 = result.
		 *                          [data]         => html string (can be empty if no result).
		 *                          [supplemental] => (optional) supplemental info to pass.
		 *                          [tr_class]     => (optional) class for the wrapping row.
		 *
		 * @return void
		 */
		public function send_ajax_response( $response ) {
			$tr_class = '';
			if ( isset( $response['tr_class'] ) && '' !== $response['tr_class'] ) {
				$tr_class = ' class="' . esc_attr( $response['tr_class'] ) . '"';
			}

			$data = '';
			if ( '' !== $response['data'] ) {
				$data = '<tr' . $tr_class . '>
							<td>&nbsp;</td>
							<td colspan="{colspan}">
								' . $response['data'] . '
							</td>
						</tr>';
			}

			$supplemental = array();
			// Only accounts for the expected new view online link, everything else will be buggered.
			if ( isset( $response['supplemental'] ) && '' !== $response['supplemental'] ) {
				$supplemental['url_link'] = ' | ' . $response['supplemental'];
			}

			/* Send the response. */
			$ajax_response = new WP_Ajax_Response();
			$ajax_response->add(
				array(
					'what'			=> self::$name,
					'action'		=> $response['action'],
					'id'			=> $response['id'],
					'data'			=> $data,
					'supplemental'	=> $supplemental,
				)
			);
			$ajax_response->send();
			exit;
		}


		/* ************** HELPER METHODS ************** */


		/**
		 * Check if a callback is a closure.
		 *
		 * @param mixed $arg Function name.
		 *
		 * @return bool
		 */
		public static function is_closure( $arg ) {
			if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
				return false;
			}

			include_once plugin_dir_path( __FILE__ ) . 'php53/php5.3-closure-test.php';
			return debug_bar_shortcodes_is_closure( $arg );
		}
	} // End of class Debug_Bar_Shortcodes_Render.

endif; // End of if class_exists wrapper.
