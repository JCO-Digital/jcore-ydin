<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Jcore\Ydin;

use Timber\Timber;
use Jcore\Ydin\Timber\ContextProvider;
use Jcore\Ydin\Environment\Environment;

$autoloader = __DIR__ . '/../vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

/**
 * The bootstrap class, should be used by all dependencies.
 *
 * This starts the parts of Ydin that every project needs: Timber, the Timber
 * context and the environment handler. Everything else in Ydin is opt-in, and
 * initialized by the theme with `Feature::init()`.
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
		ContextProvider::init();
		Environment::init();

		add_action( 'init', array( __CLASS__, 'load_modules' ) );
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

	/**
	 * Initialize every module registered through the `jcore_theme_load_modules` filter.
	 *
	 * A module is any class name implementing a static `init()` method, which is
	 * what the JCORE plugin bootstraps do.
	 *
	 * @return void
	 */
	public static function load_modules(): void {
		$modules = (array) apply_filters( 'jcore_theme_load_modules', array() );
		$loaded  = array();

		foreach ( $modules as $module ) {
			if ( ! class_exists( $module ) || ! method_exists( $module, 'init' ) ) {
				continue;
			}
			$module::init();
			$loaded[] = $module;
		}

		do_action( 'jcore_modules_loaded', $loaded );
	}
}
