<?php
/**
 * ACF backed options pages.
 *
 * @package Jcore\Ydin\Settings
 */

namespace Jcore\Ydin\Settings;

use Jcore\Ydin\InitOnce;

/**
 * Class AcfOptions
 *
 * Builds ACF options pages, and the field groups behind them, from a simple
 * declarative array. The array is filterable through `jcore_init_settings_fields`,
 * so projects can add their own groups and fields without touching this class.
 *
 * Values are read with `AcfOptions::get( $group, $field )`.
 *
 * @since 4.3.0
 */
class AcfOptions extends Option {
	use InitOnce;

	/**
	 * Array that contains the "saved" settings.
	 *
	 * @var array
	 */
	protected static array $data = array();

	/**
	 * Array that contains all the fields.
	 *
	 * @var array
	 */
	protected static array $fields = array();

	/**
	 * Register the hooks.
	 *
	 * The actual registration is deferred to `acf/init` so that themes and plugins
	 * have a chance to add their own fields through the filter before we build them.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! static::should_init() ) {
			return;
		}
		add_action( 'acf/init', array( static::class, 'register' ), 5 );
	}

	/**
	 * Build the field groups and options pages.
	 *
	 * @return void
	 */
	public static function register(): void {
		parent::init();
		static::create_acf();
		static::add_menu_page();

		$maps_key = static::get( 'keys', 'google_maps_key' );
		if ( ! empty( $maps_key ) && function_exists( 'acf_update_setting' ) ) {
			acf_update_setting( 'google_api_key', $maps_key );
		}
	}

	/**
	 * Return the setting definition array.
	 *
	 * @return array[]
	 */
	protected static function get_fields(): array {
		return apply_filters(
			'jcore_init_settings_fields',
			array(
				'keys' => array(
					'title'       => __( 'Keys & IDs', 'jcore' ),
					'description' => __( 'Settings for APIs and other.', 'jcore' ),
					'capability'  => 'manage_options',
					'fields'      => array(
						'google_maps_key'    => array(
							'type'    => 'text',
							'label'   => __( 'Google Maps Key', 'jcore' ),
							'default' => '',
						),
						'google_analytics'   => array(
							'type'    => 'text',
							'label'   => __( 'Google Analytics ID', 'jcore' ),
							'default' => '',
						),
						'google_tag_manager' => array(
							'type'    => 'text',
							'label'   => __( 'Google Tag Manager ID', 'jcore' ),
							'default' => '',
						),
						'matomo_tag_manager' => array(
							'type'        => 'text',
							'label'       => __( 'Matomo Tag Manager Uri', 'jcore' ),
							'placeholder' => 'https://analytics.example.com/js/container_XXXXXXXX.js',
							'default'     => '',
						),
					),
				),
			)
		);
	}

	/**
	 * Method to get values for settings.
	 *
	 * @param string $field_name The name of the field to get.
	 * @param mixed  $fallback   Fallback value to return if not set.
	 *
	 * @return mixed
	 */
	protected static function get_value( string $field_name, mixed $fallback ): mixed {
		return function_exists( 'get_field' ) ? get_field( $field_name, 'option' ) : $fallback;
	}

	/**
	 * Add an options sub page for every group.
	 *
	 * @return void
	 */
	public static function add_menu_page(): void {
		if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
			return;
		}
		foreach ( static::$fields as $key => $group ) {
			acf_add_options_sub_page(
				array(
					'page_title'  => $group['description'] ?? $group['title'],
					'menu_title'  => $group['title'],
					'menu_slug'   => $key,
					'capability'  => $group['capability'] ?? 'manage_options',
					'parent_slug' => $group['parent_slug'] ?? 'options-general.php',
				)
			);
		}
	}

	/**
	 * Create the local ACF field groups.
	 *
	 * @return void
	 */
	private static function create_acf(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		foreach ( static::$fields as $key => $group ) {
			$fields = array();
			foreach ( $group['fields'] as $name => $field ) {
				$fields[] = array(
					'key'           => static::get_field_name( $name, $key ),
					'label'         => $field['label'],
					'name'          => static::get_field_name( $name, $key ),
					'type'          => $field['type'],
					'placeholder'   => $field['placeholder'] ?? '',
					'instructions'  => $field['instructions'] ?? '',
					'choices'       => $field['choices'] ?? array(),
					'wrapper'       => array(
						'width' => $field['width'] ?? '100',
						'class' => '',
						'id'    => '',
					),
					'default_value' => $field['default'],
				);
			}

			acf_add_local_field_group(
				array(
					'key'                   => 'jcore_settings_' . $key,
					'title'                 => $group['title'],
					'fields'                => $fields,
					'location'              => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => $key,
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => true,
					'description'           => '',
					'show_in_rest'          => 0,
				)
			);
		}
	}
}
