<?php
/**
 * JCORE Hardening.
 *
 * @package Jcore\Ydin\Security
 */

namespace Jcore\Ydin\Security;

class Hardening {
	static function init() {
		// Block REST API user endpoint for non-logged-in users
		add_filter(
			'rest_authentication_errors',
			array( __CLASS__, 'block_user_enumeration' )
		);
	}

	static function block_user_enumeration( $result ) {
		// Skip if a previous authentication check was applied
		if ( ! empty( $result ) ) {
			return $result;
		}

		// Block user enumeration for /wp-json/wp/v2/users endpoint
		if ( strpos( $_SERVER['REQUEST_URI'], '/wp-json/wp/v2/users' ) !== false && ! is_user_logged_in() ) {
			return new \WP_Error( 'rest_login_required', 'REST API restricted to authenticated users.', array( 'status' => 403 ) );
		}

		return $result;
	}
}
