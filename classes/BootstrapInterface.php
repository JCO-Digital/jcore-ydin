<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Jcore\Ydin;

/**
 * The bootstrap interface, should be used by all dependencies.
 *
 * @package Jcore\Ydin
 * @since 3.5.0
 */
interface BootstrapInterface {

	/**
	 * Should be called to initialize the bootstrap.
	 *
	 * @return BootstrapInterface
	 */
	public static function init(): BootstrapInterface;
}
