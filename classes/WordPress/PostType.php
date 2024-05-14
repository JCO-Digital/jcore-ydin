<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Jcore\Ydin\WordPress;

/**
 * Post type specific functionality
 *
 * @since 3.0.0
 * @package Jcore\Ydin\WordPress
 */
class PostType {

	/**
	 * Register post type wrapper.
	 *
	 * @param string $slug The slug of CPT..
	 * @param array  $label_array Array of labels for CPT.
	 * @param array  $arguments Array of custom arguments for CPT.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_post_type/ For further documentations for arguments.
	 *
	 * @return void
	 */
	public static function register( string $slug, array $label_array = array(), array $arguments = array() ): void {
		$labels        = array(
			'name'          => str_replace( '_', ' ', ucfirst( $slug ) ),
			'singular_name' => str_replace( '_', ' ', ucfirst( $slug ) ),
		);
		$parsed_labels = wp_parse_args( $label_array, $labels );

		$args = array(
			'labels'              => $parsed_labels,
			'description'         => $parsed_labels['name'],
			'public'              => true,
			'show_ui'             => true,
			'delete_with_user'    => false,
			'show_in_rest'        => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'map_meta_cap'        => true,
			'rewrite'             => array(
				'slug'       => $slug,
				'with_front' => true,
			),
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		);

		$parsed_args = wp_parse_args( $arguments, $args );

		register_post_type( $slug, $parsed_args );
	}
}
