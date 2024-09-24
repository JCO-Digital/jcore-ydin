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
			$class_name = '\Jcore\Blocks\Blocks\\' . $block_class;
			if ( class_exists( $class_name ) ) {
				new $class_name();
			}
		}
	}

	/**
	 * List all custom blocks in themes blocks folder.
	 *
	 * @param string $folder Folder to scan for blocks, defaults to child /blocks folder.
	 *
	 * @return array
	 */
	public static function list_blocks( string $folder = '' ): array {
		if ( empty( $folder ) ) {
			return array();
		}
		$blocks = array();
		if ( is_dir( $folder ) ) {
			foreach ( scandir( $folder ) as $file ) {
				if ( preg_match( '/([^.]+)\.php/', $file, $matches ) ) {
					$blocks[] = $matches[1];
				}
			}
		}

		return $blocks;
	}
}
