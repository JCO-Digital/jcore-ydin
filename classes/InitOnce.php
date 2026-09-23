<?php
/**
 * Trait for making feature initialization idempotent.
 *
 * @package Jcore\Ydin
 */

namespace Jcore\Ydin;

/**
 * Trait InitOnce
 *
 * Features in Ydin are opt-in: the theme calls `Feature::init()` explicitly. This
 * trait guards against a feature being initialized twice, which would otherwise
 * register every hook a second time.
 *
 * @since 4.3.0
 */
trait InitOnce {

	/**
	 * Whether the feature has been initialized.
	 *
	 * @var bool
	 */
	private static bool $initialized = false;

	/**
	 * Marks the feature as initialized, and tells the caller whether it should continue.
	 *
	 * @return bool True the first time it is called, false on every subsequent call.
	 */
	protected static function should_init(): bool {
		if ( self::$initialized ) {
			return false;
		}
		self::$initialized = true;

		return true;
	}
}
