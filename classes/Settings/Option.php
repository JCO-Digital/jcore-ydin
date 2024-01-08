<?php

namespace Jcore\Ydin\Settings;

abstract class Option {
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
	 * Initialize everything.
	 */
	public static function init(): void {
		static::$fields = apply_filters( 'jcore_init_fields', static::get_fields() );
	}

	/**
	 * Method that checks if value exists or if it can be gotten, and gets it if so.
	 *
	 * @param string $group Group name.
	 * @param string $field Field name.
	 *
	 * @return bool
	 */
	protected static function check_value( string $group, string $field ): bool {

		if ( static::check_group( $group ) ) {
			if ( isset( static::$data[ $group ][ $field ] ) ) {
				// Value exists, return true.
				return true;
			}

			$fields = static::$fields[ $group ]['fields'];
			if ( isset( $fields[ $field ]['default'] ) ) {
				// Value can be got.
				static::$data[ $group ][ $field ] = static::get_value( static::get_field_name( $field, $group ), $fields[ $field ]['default'] );

				return true;
			}
		}

		// Return false by default.
		return false;
	}

	/**
	 * Check if valid group, and optionally fetch all group data.
	 *
	 * @param string $group Group name.
	 * @param bool   $fetch Fetch all group data.
	 *
	 * @return bool
	 */
	protected static function check_group( string $group, bool $fetch = false ): bool {
		if ( empty( static::$data[ $group ] ) || ! is_array( static::$data[ $group ] ) ) {
			// Data is empty.
			if ( isset( static::$fields[ $group ] ) ) {
				// Create the group array.
				static::$data[ $group ] = array();
			} else {
				// Not a valid group.
				return false;
			}
		}

		if ( $fetch && empty( static::$data[ $group ] ) ) {
			// Populate group data.
			foreach ( static::$fields[ $group ]['fields'] as $field => $data ) {
				static::$data[ $group ][ $field ] = static::get_value( static::get_field_name( $field, $group ), $data['default'] );
			}
		}

		return true;
	}

	/**
	 * Check value and return it if available.
	 *
	 * @param string      $group Group name.
	 * @param string|null $field Field name.
	 *
	 * @return mixed
	 */
	public static function get( string $group, string $field = null ): mixed {
		if ( null === $field ) {
			// Get the entire group.
			if ( static::check_group( $group, true ) ) {
				return static::$data[ $group ];
			}
		} elseif ( static::check_value( $group, $field ) ) {
			return static::$data[ $group ][ $field ];
		}

		return null;
	}

	/**
	 * Wrapper for adding styles.
	 *
	 * @param string $style  CSS style.
	 * @param string $prefix Optional prefix.
	 * @param string $suffix Suffix, defaults to semicolon.
	 *
	 * @return string
	 */
	protected static function add_style( string $style, string $prefix = '', string $suffix = ';' ): string {
		if ( ! str_starts_with( $style, $prefix ) ) {
			$style = "$prefix$style";
		}
		if ( substr( $style, 0 - strlen( $suffix ) ) !== $suffix ) {
			$style = "$style$suffix";
		}

		return "$style\n";
	}


	/**
	 * Generate the setting name based on group and field name.
	 *
	 * @param string $field The name of the setting.
	 * @param string $group The group of the setting.
	 *
	 * @return string
	 */
	protected static function get_field_name( string $field, string $group = '' ): string {
		if ( ! empty( $group ) ) {
			$field = "{$group}_$field";
		}

		return "jcore_$field";
	}

	/**
	 * Abstract method to return the settings fields.
	 *
	 * @return array
	 */
	abstract protected static function get_fields(): array;

	/**
	 * Method to get individual value.
	 *
	 * @param string $field_name The field name.
	 * @param mixed  $default    The Default value.
	 *
	 * @return mixed
	 */
	abstract protected static function get_value( string $field_name, mixed $default ): mixed;
}
