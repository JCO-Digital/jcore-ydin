<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Jcore\Ydin;

use Timber\Timber;
use Jcore\Ydin\Settings\Customizer;
use Jcore\Ydin\Environment\Environment;
use Jcore\Ydin\Timber\ContextProvider;

$autoloader = __DIR__ . '/../vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

/**
 * The bootstrap class, should be used by all dependencies.
 */
class Bootstrap implements BootstrapInterface {
	/**
	 * The singleton instance.
	 *
	 * @var Bootstrap|null
	 */
	private static ?Bootstrap $instance = null;

	/**
	 * Bootstrap constructor.
	 */
	private function __construct() {
		Timber::init();
		Customizer::init();
		Environment::init();
		ContextProvider::init();
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return Bootstrap
	 */
	public static function init(): Bootstrap {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
