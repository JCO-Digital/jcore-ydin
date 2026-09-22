<?php
/**
 * Comment handling.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Comments
 *
 * Turns comments off across the whole site: front end, admin, and REST. Most JCORE
 * sites do not use comments, so this is initialized by themes that want them gone.
 *
 * @since 4.3.0
 */
class Comments {
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

		add_action( 'init', array( static::class, 'disable_frontend' ) );
		add_action( 'admin_init', array( static::class, 'disable_admin' ) );
		add_action( 'admin_menu', array( static::class, 'remove_menu' ) );
	}

	/**
	 * Close comments on the front end and hide the ones that already exist.
	 *
	 * @return void
	 */
	public static function disable_frontend(): void {
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );
		add_filter( 'comments_array', '__return_empty_array', 10, 2 );

		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}

		add_filter( 'rest_endpoints', array( static::class, 'remove_rest_endpoints' ) );
	}

	/**
	 * Remove the comment routes from the REST API.
	 *
	 * @param array $endpoints The registered REST endpoints.
	 *
	 * @return array
	 */
	public static function remove_rest_endpoints( $endpoints ): array {
		unset( $endpoints['/wp/v2/comments'] );
		unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );

		return $endpoints;
	}

	/**
	 * Remove the comments menu item.
	 *
	 * @return void
	 */
	public static function remove_menu(): void {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Redirect away from the comments screen, and drop comment support everywhere.
	 *
	 * @return void
	 */
	public static function disable_admin(): void {
		global $pagenow;

		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}
}
