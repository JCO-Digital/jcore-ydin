<?php
/**
 * Admin area tweaks.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Admin
 *
 * Loads the theme's admin stylesheet, if it has one.
 *
 * @since 4.3.0
 */
class Admin {
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

		add_action( 'admin_enqueue_scripts', array( static::class, 'styles' ) );
	}

	/**
	 * Enqueue the admin stylesheet.
	 *
	 * @return void
	 */
	public static function styles(): void {
		$file = apply_filters( 'jcore_admin_stylesheet', '/dist/css/admin.css' );
		if ( empty( $file ) ) {
			return;
		}

		Assets::style_register( 'jcore-admin-style', $file );
		wp_enqueue_style( 'jcore-admin-style' );
	}
}
