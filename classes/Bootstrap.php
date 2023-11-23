<?php

namespace Jcore\Ydin;

use Timber\Timber;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * The bootstrap class, should be used by all dependencies.
 */
class Bootstrap {
	/**
	 * The singleton instance.
	 *
	 * @var Bootstrap|null
	 */
	private static ?Bootstrap $instance;

	/**
	 * Bootstrap constructor.
	 */
	private function __construct() {
		Timber::init();
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return Bootstrap
	 */
	public static function get_instance(): Bootstrap {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
