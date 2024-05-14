<?php // phpcs:ignore

namespace Jcore\Ydin\WordPress;

/**
 * Taxonomy related functions.
 *
 * @since 3.0.0
 * @package Jcore\Ydin\WordPress
 */
class Taxonomy {

	/**
	 * Register taxonomy wrapper.
	 *
	 * @param string $slug The taxonomy slug.
	 * @param array  $post_types Array of post types to associate taxonomy with.
	 * @param array  $label_array Array of labels for taxonomy.
	 * @param array  $arguments Array of custom arguments for taxonomy.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_taxonomy/ For further documentations for arguments.
	 *
	 * @return void
	 */
	public static function register( string $slug, array $post_types = array( 'post', 'page' ), array $label_array = array(), array $arguments = array() ): void {
		$labels        = array(
			'name'          => str_replace( '_', ' ', ucfirst( $slug ) ),
			'singular_name' => str_replace( '_', ' ', ucfirst( $slug ) ),
		);
		$parsed_labels = wp_parse_args( $label_array, $labels );

		$rewrite     = array(
			'slug'         => $slug,
			'with_front'   => true,
			'hierarchical' => true,
		);
		$args        = array(
			'labels'            => $parsed_labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'rewrite'           => $rewrite,
			'query_var'         => true,
		);
		$parsed_args = wp_parse_args( $arguments, $args );

		register_taxonomy( $slug, $post_types, $parsed_args );
	}
}
