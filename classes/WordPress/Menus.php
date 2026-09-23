<?php
/**
 * Navigation menu registration.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Menus
 *
 * Registers the navigation menus declared through the `jcore_menus` filter, which
 * is the same filter the Timber context uses to expose them to Twig.
 *
 * @since 4.3.0
 */
class Menus {
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

		add_action( 'after_setup_theme', array( static::class, 'register' ) );
		add_filter( 'body_class', array( static::class, 'body_class' ) );
	}

	/**
	 * Register the menus added through the `jcore_menus` filter.
	 *
	 * @return void
	 */
	public static function register(): void {
		$menus = apply_filters( 'jcore_menus', array() );
		if ( empty( $menus ) ) {
			return;
		}

		register_nav_menus( $menus );
	}

	/**
	 * Add a `{post_type}-{slug}` class to the body, for targeting a single page.
	 *
	 * @param array $classes The body classes.
	 *
	 * @return array
	 */
	public static function body_class( $classes ): array {
		$post = get_post();
		if ( $post instanceof \WP_Post ) {
			$classes[] = $post->post_type . '-' . $post->post_name;
		}

		return $classes;
	}
}
