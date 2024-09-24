<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, Squiz.Commenting.FileComment.Missing

namespace Jcore\Ydin\Blocks;

/**
 * Class Blocks
 * Handles registering all the blocks.
 *
 * @package Jcore\Blocks
 */
class Blocks {
	/**
	 * Initialize things.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_blocks' ) );
	}

	/**
	 * Initialize all blocks here as the example
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		/**
		 * Filter the list of blocks to be registered.
		 *
		 * @param array $blocks An array of block class names.
		 */
		$blocks = apply_filters( 'jcore_blocks_get_blocks', self::list_blocks() );
		foreach ( $blocks as $block_class ) {
			if ( is_readable( $block_class['path'] ) ) {
				require_once $block_class['path'];
			}
			$class_name = $block_class['class'];
			if ( class_exists( $class_name ) ) {
				new $class_name();
			}
		}
	}

	/**
	 * List all custom blocks in themes blocks folder.
	 *
	 * @param string $folder Folder to scan for blocks, defaults to child /blocks folder.
	 * @param string $class_prefix The class namespace.
	 *
	 * @return array
	 */
	public static function list_blocks( string $folder = '', string $class_prefix = 'Jcore\Ydin\Blocks\\' ): array {
		if ( empty( $folder ) ) {
			return array();
		}
		$blocks = array();
		if ( is_dir( $folder ) ) {
			foreach ( scandir( $folder ) as $file ) {
				if ( preg_match( '/([^.]+)\.php/', $file, $matches ) ) {
					$blocks[] = array(
						'class' => $class_prefix . $matches[1],
						'path'  => trailingslashit( $folder ) . $file,
					);
				}
			}
		}

		return $blocks;
	}
}
