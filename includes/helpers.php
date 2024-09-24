<?php
/**
 * Helper functions for use in modules.
 *
 * @package Jcore\Ydin
 */

namespace Jcore\Ydin;

/**
 * Handles registering a Timber location.
 *
 * @param string $path The path to the location.
 *
 * @return void
 * @since 3.6.0 Added the function.
 */
function register_timber_location( string $path ): void {
	if ( ! is_dir( $path ) ) {
		return;
	}
	add_filter(
		'timber/locations',
		static function ( $locations ) use ( $path ) {
			$locations['__main__'][] = $path;
			return $locations;
		}
	);
}
