<?php

namespace Jcore\Ydin\Settings;

use WP_Customize_Media_Control;

/**
 * The Customizer class is used to initialize the Customizer.
 * Hook into the jcore_init_customizer_fields filter to add fields.
 *
 * @since 3.0.0
 */
class Customizer extends Option {

	/**
	 * Array that contains the "saved" settings.
	 *
	 * @var array
	 */
	protected static array $data;

	/**
	 * Array that contains all the fields.
	 *
	 * @var array
	 */
	protected static array $fields;

	/**
	 * Init function that registers action.
	 *
	 * @return void
	 */
	public static function init(): void {
		parent::init();
		add_action( 'customize_register', '\jcore\Customizer::customize_register' );
	}

	/**
	 * Return the setting definition array.
	 *
	 * @return array[]
	 */
	protected static function get_fields(): array {
		return apply_filters(
			'jcore_init_customizer_fields',
			array()
		);
	}

	/**
	 * Generate the color choices.
	 *
	 * @return array
	 */
	public static function get_color_choices(): array {
		$choices = array();
		foreach ( static::$fields['color']['fields'] as $key => $value ) {
			$choices[ $key ] = ucfirst( $key );
		}
		$choices['none'] = __( 'None', 'jcore' );

		return $choices;
	}

	/**
	 * Action hook that registers customizer settings.
	 *
	 * @param object $wp_customize The WP Customizer object.
	 *
	 * @return void
	 */
	public static function customize_register( $wp_customize ): void {
		// Remove the custom CSS part.
		$wp_customize->remove_section( 'custom_css' );

		$priority = 160;
		foreach ( static::get_fields() as $group => $data ) {
			$section = $wp_customize->get_section( $group );

			if ( empty( $section ) ) {
				// Add the group.
				$wp_customize->add_section(
					$group,
					array(
						'title'       => $data['title'],
						'description' => $data['description'] ?? '',
						'priority'    => $priority++,
					)
				);
			} else {
				$section->description = $data['description'];
			}

			// Loop all fields.
			foreach ( $data['fields'] as $field => $field_data ) {
				static::add_field( $wp_customize, $group, $field, $field_data );
			}
		}
	}

	/**
	 * Add basic field to customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize The WP Customizer object.
	 * @param string               $group        Group name.
	 * @param string               $field        Field name.
	 * @param array                $field_data   Field data.
	 *
	 * @return void
	 */
	private static function add_field( WP_Customize_Manager $wp_customize, string $group, string $field, array $field_data ): void {
		$field_name = static::get_field_name( $field, $group );
		$wp_customize->add_setting(
			$field_name,
			array(
				'default'    => $field_data['default'],
				'capability' => 'edit_theme_options',
				'type'       => 'theme_mod',

			)
		);
		if ( 'image' === $field_data['type'] ) {
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					"jcore_control_{$group}_$field",
					array(
						'label'     => $field_data['label'],
						'section'   => $group,
						'settings'  => $field_name,
						'mime_type' => $field_data['type'],
					)
				)
			);
		} elseif ( 'jcore_color' === $field_data['type'] ) {
			$wp_customize->add_control(
				new ColorControl(
					$wp_customize,
					"jcore_control_{$group}_$field",
					array(
						'label'     => $field_data['label'],
						'section'   => $group,
						'settings'  => $field_name,
						'mime_type' => $field_data['type'],
						'choices'   => $field_data['choices'] ?? null,
					)
				)
			);
		} else {
			$wp_customize->add_control(
				"jcore_control_{$group}_$field",
				array(
					'label'    => $field_data['label'],
					'section'  => $group,
					'settings' => $field_name,
					'type'     => $field_data['type'],
					'choices'  => $field_data['choices'] ?? null,
				)
			);
		}
	}

	/**
	 * Create styles from customizer.
	 *
	 * @return string
	 */
	public static function get_styles(): string {
		$style = ":root {\n";
		foreach ( static::get( 'color' ) as $item => $value ) {
			$style .= static::add_style( "--bs-$item: $value" );
		}
		foreach ( array( 'navigation_desktop', 'navigation_mobile', 'site' ) as $type ) {
			foreach ( static::get( $type ) as $key => $value ) {
				if ( str_ends_with( $key, '_color' ) ) {
					$name  = str_replace( '_', '-', 'jcore-' . $type . '-' . $key );
					$color = static::get( 'color', $value ) ?? 'transparent';

					$style .= static::add_style( "--$name: $color" );
				}
			}
		}
		$style .= '}';

		return $style . self::get_color_classes();
	}

	/**
	 * Generate CSS from admin.
	 *
	 * @return string
	 */
	public static function get_admin_styles(): string {
		$style = ":root {\n";
		foreach ( static::get( 'color' ) as $item => $value ) {
			$style .= static::add_style( "--bs-$item: $value" );
		}
		$style .= '}';

		return $style . self::get_color_classes( true );
	}

	public static function get_color_classes( $admin = false ): string {
		$style = '';
		foreach ( static::get( 'color' ) as $item => $value ) {
			$style .= "\n.has-$item-background-color { background-color: var(--bs-$item) }";
			if ( $admin ) {
				$fill   = self::is_light( $value ) ? 'dark' : 'light';
				$style .= "\n.has-$item-background-color svg { fill: var(--bs-$fill) }";
			}
			$style .= "\n.has-$item-overlay-color { background-color: var(--bs-$item) }";
			$style .= "\n.has-$item-color { color: var(--bs-$item) }";
			$style .= "\n.hover-$item-color:hover { color: var(--bs-$item) }";
		}

		return $style;
	}

	/**
	 * Checks if hex color is light or not.
	 *
	 * @param string $rgb Color value to check.
	 *
	 * @return bool
	 */
	public static function is_light( string $rgb ): bool {
		return ( hexdec( substr( $rgb, 1, 2 ) ) + hexdec( substr( $rgb, 3, 2 ) ) + hexdec( substr( $rgb, 5, 2 ) ) > 400 );
	}

	/**
	 * Get individual value.
	 *
	 * @param string $field_name Field name.
	 * @param mixed  $default    Default value.
	 *
	 * @return mixed
	 */
	protected static function get_value( string $field_name, mixed $default ): mixed {
		return get_theme_mod( $field_name, $default );
	}

	/**
	 * Add the colors to gutenberg editor.
	 *
	 * @return void
	 */
	public static function gutenberg_add_colors(): void {
		$color_array = array();
		foreach ( static::get( 'color' ) as $item => $value ) {
			$color_array[] = array(
				'name'  => ucwords( $item ),
				'slug'  => $item,
				'color' => $value,
			);
		}
		add_theme_support(
			'editor-color-palette',
			$color_array
		);
	}
}
