<?php
/**
 * Media and upload handling.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Media
 *
 * Allows SVG uploads, raises the JPEG quality WordPress uses when it generates
 * image sizes, and can inline an SVG site logo.
 *
 * @since 4.3.0
 */
class Media {
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

		add_filter( 'upload_mimes', array( static::class, 'mime_types' ) );
		add_filter( 'jpeg_quality', array( static::class, 'jpeg_quality' ), 10, 2 );

		if ( apply_filters( 'jcore_inline_svg_logo', false ) ) {
			add_filter( 'get_custom_logo', array( static::class, 'get_logo' ) );
		}
	}

	/**
	 * Add the extra mime types we allow to be uploaded.
	 *
	 * @param array $mimes Allowed mime types.
	 *
	 * @return array
	 */
	public static function mime_types( $mimes ): array {
		$extra = apply_filters(
			'jcore_upload_mimes',
			array(
				'svg' => 'image/svg+xml',
			)
		);

		return array_merge( (array) $mimes, (array) $extra );
	}

	/**
	 * Set JPEG quality on resize.
	 *
	 * @param int    $quality The quality setting passed to the function.
	 * @param string $context The type of image.
	 *
	 * @return int
	 */
	public static function jpeg_quality( $quality, $context = '' ): int {
		$target = (int) apply_filters( 'jcore_jpeg_quality', 92 );

		if ( 'image/jpeg' === $context && $quality > 85 ) {
			return (int) $quality;
		}

		return $target;
	}

	/**
	 * Replaces an `<img>` pointing at an SVG with the inline SVG itself.
	 *
	 * Inlining the logo lets CSS reach into it, which is the usual reason to want
	 * an SVG logo in the first place.
	 *
	 * @param string $html The HTML returned by get_custom_logo.
	 *
	 * @return string
	 */
	public static function get_logo( string $html ): string {
		$html = preg_replace( '_<a [^>]*>(.*)</a>_sm', '\1', $html );
		if ( ! preg_match( '_<img[^>]+src="([^"]+(/wp-content/[^"]+\.svg))"[^>]*/?>_', $html, $matches ) ) {
			return $html;
		}

		$filename = rtrim( ABSPATH, '/' ) . $matches[2];
		if ( file_exists( $filename ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$svg = preg_replace( '_<\?xml[^>]+>_', '', file_get_contents( $filename ) );

			return str_replace( $matches[0], '<div class="custom-logo">' . $svg . '</div>', $html );
		}

		// On local sites the uploads folder is not always reachable on disk, so fetch it.
		if ( 'local' !== wp_get_environment_type() ) {
			return $html;
		}

		$transient_name = 'jcore_custom_svg_logo';
		$cached         = get_transient( $transient_name );
		if ( false !== $cached ) {
			return $cached;
		}

		// We are requesting a local site from a local site, so ignore SSL errors.
		add_filter( 'https_ssl_verify', '__return_false' );
		$request = wp_safe_remote_get( $matches[1] );
		remove_filter( 'https_ssl_verify', '__return_false' );

		if ( is_wp_error( $request ) ) {
			return $html;
		}

		$svg     = preg_replace( '_<\?xml[^>]+>_', '', wp_remote_retrieve_body( $request ) );
		$inlined = str_replace( $matches[0], '<div class="custom-logo">' . $svg . '</div>', $html );
		set_transient( $transient_name, $inlined, DAY_IN_SECONDS );

		return $inlined;
	}
}
