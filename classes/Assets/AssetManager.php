<?php

namespace Jcore\Ydin\Assets;

class AssetManager {
	/**
	 * Register script wrapper.
	 *
	 * @param string      $name Script name.
	 * @param string      $file Filename.
	 * @param array       $dependencies Dependencies.
	 * @param string|null $version Optional version number.
	 * @param true[]      $args Arguments passed to the script register.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_register_script/ wp_register_script for more info on the arguments.
	 */
	public static function script_register( string $name, string $file, array $dependencies = array(), string $version = null, array $args = array( 'in_footer' => true ) ): void {
		global $wp_version;
		$info = self::get_file_info( $file, $version );

		if ( false !== $info ) {
			// Backward compat, sets the in_footer argument to true if WP version is less than 6.3.
			// See: https://make.wordpress.org/core/2023/07/14/registering-scripts-with-async-and-defer-attributes-in-wordpress-6-3/ for more info.
			if ( version_compare( $wp_version, '6.3', '<' ) ) {
				$args = true;
			}
			wp_register_script(
				$name,
				$info['uri'],
				$dependencies,
				$info['version'],
				$args
			);
		}
	}

	/**
	 * Register style wrapper.
	 *
	 * @param string $name Style name.
	 * @param string $file Filename.
	 * @param array $dependencies Dependencies.
	 * @param string $version Optional version number.
	 */
	public static function style_register( string $name, string $file, array $dependencies = array(), string $version = '' ) {
		$info = self::get_file_info( $file, $version );

		if ( false !== $info ) {
			wp_register_style(
				$name,
				$info['uri'],
				$dependencies,
				$info['version']
			);
		}
	}

	/**
	 * Get file info for script/style registration.
	 *
	 * @param string $file Filename.
	 * @param string $version Optional version number.
	 *
	 * @return bool|string[]
	 */
	public static function get_file_info( string $file, string $version = '' ): array|bool {
		if ( ! empty( $version ) ) {
			$version .= '-';
		}
		foreach (
			array(
				array(
					'path' => self::join_path( WP_CONTENT_DIR, $file ),
					'uri'  => self::join_path( content_url(), $file ),
				),
				array(
					'path' => self::join_path( get_stylesheet_directory(), $file ),
					'uri'  => self::join_path( get_stylesheet_directory_uri(), $file ),
				),
				array(
					'path' => self::join_path( get_template_directory(), $file ),
					'uri'  => self::join_path( get_template_directory_uri(), $file ),
				),
			) as $location ) {
			if ( file_exists( $location['path'] ) ) {
				$version .= filemtime( $location['path'] );

				return array(
					'uri'     => $location['uri'],
					'path'    => $location['path'],
					'version' => $version,
				);
			}
		}
		return false;
	}

	/**
	 * A function that joins together all parts of a path.
	 *
	 * @param string $path Base path.
	 * @param string ...$parts Path parts to be joined.
	 *
	 * @return string
	 */
	public static function join_path( string $path, string ...$parts ): string {
		foreach ( $parts as $part ) {
			$path .= '/' . trim( $part, '/ ' );
		}

		return $path;
	}
}
