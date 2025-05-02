<?php
/**
 * JCORE environment handler.
 *
 * @package Jcore\Ydin\Environment
 */

namespace Jcore\Ydin\Environment;

class Environment {

	static function init() {
		add_action( 'environment_changed', __CLASS__ . '::environment_changed' );

		$last_known = get_option( 'last_known_environment', 'unknown' );
		if ( $last_known !== wp_get_environment_type() ) {
			error_log( sprintf( 'Environment changed from %s to %s.', $last_known, wp_get_environment_type() ) );

			do_action( 'environment_changed', wp_get_environment_type(), $last_known );
			update_option( 'last_known_environment', wp_get_environment_type() );
		}
	}

	static function environment_changed( $new_environment ) {
		if ( $new_environment === 'production' ) {
			update_option( 'blog_public', '1' );
		} else {
			update_option( 'blog_public', '0' );
		}
	}
}
