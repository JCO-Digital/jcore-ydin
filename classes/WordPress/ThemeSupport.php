<?php
/**
 * Default theme supports.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class ThemeSupport
 *
 * The `add_theme_support` calls that every JCORE theme wants. A theme is free to
 * add more of its own; this only covers the shared baseline.
 *
 * @since 4.3.0
 */
class ThemeSupport {
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

		add_action( 'after_setup_theme', array( static::class, 'setup' ) );
	}

	/**
	 * Declare the theme supports.
	 *
	 * @return void
	 */
	public static function setup(): void {
		add_theme_support( 'align-wide' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'custom-units' );
		add_theme_support( 'menus' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'title-tag' );

		add_theme_support(
			'html5',
			array(
				'caption',
				'comment-form',
				'comment-list',
				'gallery',
				'script',
				'search-form',
				'style',
			)
		);

		add_theme_support(
			'custom-logo',
			apply_filters(
				'jcore_custom_logo_args',
				array(
					'height'               => 64,
					'width'                => 200,
					'flex-height'          => true,
					'flex-width'           => true,
					'header-text'          => array( 'site-title', 'site-description' ),
					'unlink-homepage-logo' => false,
				)
			)
		);

		// Pages get an excerpt too, which teases and search results rely on.
		add_post_type_support( 'page', 'excerpt' );

		static::editor_styles();
		static::woocommerce();
	}

	/**
	 * Register the theme's editor stylesheet.
	 *
	 * @return void
	 */
	private static function editor_styles(): void {
		$stylesheet = apply_filters( 'jcore_editor_stylesheet', 'dist/css/editor.css' );
		if ( empty( $stylesheet ) ) {
			return;
		}

		add_theme_support( 'editor-styles' );
		add_editor_style( $stylesheet );
	}

	/**
	 * Declare WooCommerce support, but only when WooCommerce is active.
	 *
	 * @return void
	 */
	private static function woocommerce(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$grid = array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'max_rows'        => 10,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 4,
		);

		add_theme_support(
			'woocommerce',
			array(
				'product_grid'   => $grid,
				'product_blocks' => $grid,
			)
		);
	}
}
