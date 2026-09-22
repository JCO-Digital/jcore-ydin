<?php
/**
 * ACF JSON storage handling.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Acf
 *
 * Keeps ACF field groups, post types and taxonomies in version control by saving
 * them as JSON in the stylesheet directory, under readable file names.
 *
 * @since 4.3.0
 */
class Acf {
	use InitOnce;

	/**
	 * The sub folders we manage inside `acf-json`.
	 *
	 * @var string[]
	 */
	private const TYPES = array( 'fields', 'post-types', 'taxonomy' );

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! static::should_init() ) {
			return;
		}

		add_filter( 'acf/settings/save_json/type=acf-field-group', array( static::class, 'field_save_location' ), 5, 0 );
		add_filter( 'acf/settings/save_json/type=acf-post-type', array( static::class, 'post_type_save_location' ), 5, 0 );
		add_filter( 'acf/settings/save_json/type=acf-taxonomy', array( static::class, 'taxonomy_save_location' ), 5, 0 );
		add_filter( 'acf/json/save_file_name', array( static::class, 'save_name' ), 10, 3 );
		add_filter( 'acf/settings/load_json', array( static::class, 'load_paths' ), 5, 1 );
		add_filter( 'acf/settings/l10n_textdomain', array( static::class, 'textdomain' ), 10, 0 );
	}

	/**
	 * Save location for ACF field groups.
	 *
	 * @return string
	 */
	public static function field_save_location(): string {
		return static::path( 'fields' );
	}

	/**
	 * Save location for ACF post types.
	 *
	 * @return string
	 */
	public static function post_type_save_location(): string {
		return static::path( 'post-types' );
	}

	/**
	 * Save location for ACF taxonomies.
	 *
	 * @return string
	 */
	public static function taxonomy_save_location(): string {
		return static::path( 'taxonomy' );
	}

	/**
	 * The textdomain ACF uses when translating the JSON files.
	 *
	 * @return string
	 */
	public static function textdomain(): string {
		return apply_filters( 'jcore_acf_textdomain', 'jcore' );
	}

	/**
	 * Give the saved JSON files names a human can read in a diff.
	 *
	 * @param string $filename  The name of the file to save.
	 * @param array  $post      The data for the current ACF "post" to save.
	 * @param string $load_path The path where the ACF "post" is being loaded from.
	 *
	 * @return string
	 */
	public static function save_name( string $filename, array $post, string $load_path ): string {
		$our_paths = array_map( array( static::class, 'path' ), self::TYPES );

		if ( ! empty( $load_path ) ) {
			$load_path = dirname( $load_path );
			if ( ! in_array( $load_path, $our_paths, true ) ) {
				return $filename;
			}
		}
		if ( isset( $post['post_type'] ) && str_contains( $filename, 'post_type' ) ) {
			return 'post_type_' . $post['post_type'] . '.json';
		}
		if ( isset( $post['taxonomy'] ) && str_contains( $filename, 'taxonomy' ) ) {
			return 'taxonomy_' . $post['taxonomy'] . '.json';
		}
		if ( isset( $post['title'] ) && str_contains( $filename, 'group_' ) ) {
			return 'fields_' . sanitize_title( $post['title'] ) . '.json';
		}

		return $filename;
	}

	/**
	 * Append our custom paths to the paths ACF loads JSON from.
	 *
	 * @param array $paths The current list of paths to load ACF JSON files from.
	 *
	 * @return array
	 */
	public static function load_paths( array $paths ): array {
		foreach ( self::TYPES as $type ) {
			$paths[] = static::path( $type );
		}

		return $paths;
	}

	/**
	 * Build (and create) the storage path for a given type.
	 *
	 * @param string $name The type of options to save. e.g. post-types, taxonomy.
	 *
	 * @return string
	 */
	public static function path( string $name ): string {
		$base = apply_filters( 'jcore_acf_json_path', untrailingslashit( get_stylesheet_directory() ) . '/acf-json' );
		$path = $base . '/' . $name;
		if ( ! is_dir( $path ) ) {
			wp_mkdir_p( $path );
		}

		return $path;
	}
}
