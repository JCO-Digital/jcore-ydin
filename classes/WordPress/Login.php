<?php
/**
 * Login screen branding.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Login
 *
 * Points the login logo at the site instead of wordpress.org, swaps in the custom
 * logo, and loads the theme's login stylesheet if it has one.
 *
 * @since 4.3.0
 */
class Login {
	use InitOnce;

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! static::should_init() ) {
			return;
		}

		add_action( 'login_enqueue_scripts', array( static::class, 'styles' ) );
		add_filter( 'login_headerurl', array( static::class, 'header_url' ) );
		add_filter( 'login_headertext', 'get_custom_logo' );
	}

	/**
	 * Enqueue the login stylesheet, if the theme ships one.
	 *
	 * @return void
	 */
	public static function styles(): void {
		$file = apply_filters( 'jcore_login_stylesheet', '/dist/css/wplogin.css' );
		if ( empty( $file ) ) {
			return;
		}

		Assets::style_register( 'jcore-login-style', $file );
		wp_enqueue_style( 'jcore-login-style' );
	}

	/**
	 * Link the login logo to the site home.
	 *
	 * @return string
	 */
	public static function header_url(): string {
		return home_url();
	}
}
