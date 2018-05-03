<?php
/**
 * Admin settings for Debug_Bar_Suite.
 *
 * @package Debug_Bar_Suite
 */

namespace Debug_Bar_Suite;

/**
 * Class Debug_Bar_Suite_Admin
 *
 * @package Debug_Bar_Suite
 */
class Debug_Bar_Suite_Admin {

	/**
	 * Holds debug_bar_suite_enabled_addons option field.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Debug_Bar_Suite_Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'settings_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_page_init' ) );
	}

	/**
	 * Add options page.
	 *
	 * @return void;
	 */
	public function settings_menu() {
		add_options_page(
			'Debug Bar Suite Settings',
			'Debug Bar Suite',
			'manage_options',
			'debug-bar-suite-admin',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Options page callback
	 *
	 * @return void
	 */
	public function admin_page() {

		$this->options = apply_filters(
			'debug_bar_suite_enabled_addons',
			get_option( 'debug_bar_suite_enabled_addons' )
		);
		?>
			<div class="wrap">
				<h1>Debug Bar Suite Settings</h1>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'enabled_addons' );
					do_settings_sections( 'debug-bar-suite-admin' );
					submit_button();
					?>
				</form>
			</div>
		<?php
	}

	/**
	 * Handles admin_init hook
	 *
	 * @return void
	 */
	public function admin_page_init() {

		register_setting(
			'enabled_addons',
			'debug_bar_suite_enabled_addons',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'debug_bar_suite_settings_section',
			'Enabled Add-ons',
			null,
			'debug-bar-suite-admin'
		);

		// add an enabled checkbof for each addon.
		foreach ( array_keys( Debug_Bar_Suite_Loader::get_addons() ) as $addon ) {

			add_settings_field(
				$addon,
				ucwords( str_replace( '-', ' ', $addon ) ),
				array( $this, 'addon_enable_field' ),
				'debug-bar-suite-admin',
				'debug_bar_suite_settings_section',
				array(
					'addon'     => $addon,
					'label_for' => $addon . '_enable',
				)
			);

		}
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 * @return mixed
	 */
	public function sanitize( $input ) {

		$new_input = array();

		foreach ( $input as $field => $value ) {
			$new_input[ $field ] = sanitize_text_field( $value );
		}

		return $new_input;
	}

	/**
	 * Displays the Addon Enable Option.
	 *
	 * @param array $args Settings args.
	 * @return void
	 */
	public function addon_enable_field( $args ) {

		$addon = isset( $args['addon'] ) ? $args['addon'] : '';

		printf(
			'<input type="checkbox" id="%1$s_enable" name="debug_bar_suite_enabled_addons[%1$s]" %2$s />',
			esc_attr( $args['addon'] ),
			checked( isset( $this->options[ $addon ] ) ? esc_attr( $this->options[ $addon ] ) : '', 'on', false )
		);
	}

}
