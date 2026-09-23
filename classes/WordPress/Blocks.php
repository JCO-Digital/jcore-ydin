<?php
/**
 * Block registration helpers.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use DirectoryIterator;
use Jcore\Ydin\InitOnce;

/**
 * Class Blocks
 *
 * Registers every built block found in the theme's block directory, and adds the
 * shared JCORE block category.
 *
 * @since 4.3.0
 */
class Blocks {
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

		add_action( 'init', array( static::class, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( static::class, 'block_categories' ) );
	}

	/**
	 * Register every block directory found in the theme's build output.
	 *
	 * A block directory is any sub directory containing a `block.json`.
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		foreach ( static::get_directories() as $directory ) {
			if ( ! is_dir( $directory ) ) {
				continue;
			}
			$dir = new DirectoryIterator( $directory );
			foreach ( $dir as $fileinfo ) {
				if ( $fileinfo->isDot() || ! $fileinfo->isDir() ) {
					continue;
				}
				if ( ! file_exists( $fileinfo->getRealPath() . '/block.json' ) ) {
					continue;
				}
				register_block_type( $fileinfo->getRealPath() );
			}
		}
	}

	/**
	 * The directories to scan for blocks.
	 *
	 * @return string[]
	 */
	public static function get_directories(): array {
		$directories = array( get_stylesheet_directory() . '/dist/blocks' );

		if ( get_stylesheet_directory() !== get_template_directory() ) {
			$directories[] = get_template_directory() . '/dist/blocks';
		}

		return (array) apply_filters( 'jcore_blocks_directories', $directories );
	}

	/**
	 * Add the JCORE block category.
	 *
	 * @param array $categories The registered block categories.
	 *
	 * @return array
	 */
	public static function block_categories( array $categories ): array {
		foreach ( $categories as $category ) {
			if ( isset( $category['slug'] ) && 'jcore-blocks' === $category['slug'] ) {
				return $categories;
			}
		}

		$categories[] = array(
			'slug'  => 'jcore-blocks',
			'title' => __( 'JCORE blocks', 'jcore' ),
		);

		return $categories;
	}
}
