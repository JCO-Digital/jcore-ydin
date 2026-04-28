<?php
/**
 * JCORE environment handler.
 *
 * @package Jcore\Ydin\Environment
 */

namespace Jcore\Ydin\Environment;

/**
 * Class Environment
 *
 * Handles environment-specific logic.
 */
class Environment {

	/**
	 * Initialize the environment handler.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'environment_changed', __CLASS__ . '::environment_changed' );

		$last_known = get_option( 'last_known_environment', 'unknown' );
		if ( $last_known !== wp_get_environment_type() ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'Environment changed from %s to %s.', $last_known, wp_get_environment_type() ) );

			do_action( 'environment_changed', wp_get_environment_type(), $last_known );
			update_option( 'last_known_environment', wp_get_environment_type() );
		}
	}

	/**
	 * Handle environment change.
	 *
	 * @param string $new_environment The new environment type.
	 *
	 * @return void
	 */
	public static function environment_changed( $new_environment ) {
		if ( $new_environment === 'production' ) {
			update_option( 'blog_public', '1' );
		} else {
			update_option( 'blog_public', '0' );
		}
	}
}
