<?php
/**
 * Analytics and tag manager output.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;
use Jcore\Ydin\Settings\AcfOptions;

/**
 * Class Analytics
 *
 * Prints the Google Analytics, Google Tag Manager and Matomo Tag Manager snippets
 * based on the IDs entered under Settings -> Keys & IDs.
 *
 * Nothing is printed when the corresponding setting is empty, and nothing is
 * printed for logged in users when `jcore_analytics_skip_logged_in` returns true.
 *
 * @since 4.3.0
 */
class Analytics {
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

		add_action( 'wp_head', array( static::class, 'head' ), 1 );
		add_action( 'wp_body_open', array( static::class, 'body_open' ), 1 );
	}

	/**
	 * Whether analytics should be printed on this request.
	 *
	 * @return bool
	 */
	public static function enabled(): bool {
		$enabled = ! is_admin();

		if ( apply_filters( 'jcore_analytics_skip_logged_in', false ) && is_user_logged_in() ) {
			$enabled = false;
		}

		return apply_filters( 'jcore_analytics_enabled', $enabled );
	}

	/**
	 * Read and trim a setting from the "keys" group.
	 *
	 * @param string $field The field name.
	 *
	 * @return string
	 */
	private static function setting( string $field ): string {
		return trim( (string) AcfOptions::get( 'keys', $field, '' ) );
	}

	/**
	 * Print the scripts that belong in the document head.
	 *
	 * @return void
	 */
	public static function head(): void {
		if ( ! static::enabled() ) {
			return;
		}

		$tag_manager = static::setting( 'google_tag_manager' );
		if ( ! empty( $tag_manager ) ) {
			?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','<?php echo esc_js( $tag_manager ); ?>');</script>
			<!-- End Google Tag Manager -->
			<?php
		}

		$analytics = static::setting( 'google_analytics' );
		if ( ! empty( $analytics ) ) {
			?>
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- The gtag loader has to be inline in the head, before anything else runs. ?>
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo rawurlencode( $analytics ); ?>"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', '<?php echo esc_js( $analytics ); ?>');
			</script>
			<!-- End Global site tag -->
			<?php
		}

		$matomo = static::setting( 'matomo_tag_manager' );
		if ( ! empty( $matomo ) ) {
			?>
			<!-- Matomo Tag Manager -->
			<script>
				var _mtm = window._mtm = window._mtm || [];
				_mtm.push({'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start'});
				(function() {
					var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
					g.async=true; g.src='<?php echo esc_url( $matomo ); ?>'; s.parentNode.insertBefore(g,s);
				})();
			</script>
			<!-- End Matomo Tag Manager -->
			<?php
		}
	}

	/**
	 * Print the no-script fallback directly after the opening body tag.
	 *
	 * @return void
	 */
	public static function body_open(): void {
		if ( ! static::enabled() ) {
			return;
		}

		$tag_manager = static::setting( 'google_tag_manager' );
		if ( empty( $tag_manager ) ) {
			return;
		}
		?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo rawurlencode( $tag_manager ); ?>"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php
	}
}
