<?php
/**
 * JCORE environment handler.
 *
 * @package Jcore\Ydin\Environment
 */

namespace Jcore\Ydin\Environment;

use Jcore\Ydin\InitOnce;

/**
 * Class Environment
 *
 * Handles environment-specific logic: detects when a site has been moved between
 * environments, keeps it out of search engines everywhere but production, and
 * stops local sites from sending real mail.
 */
class Environment {
	use InitOnce;

	/**
	 * Initialize the environment handler.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! static::should_init() ) {
			return;
		}

		add_action( 'environment_changed', __CLASS__ . '::environment_changed', 10, 2 );
		add_action( 'phpmailer_init', __CLASS__ . '::phpmailer_init' );

		static::detect_change();
	}

	/**
	 * Compare the current environment to the last one we saw, and fire the
	 * `environment_changed` action when they differ.
	 *
	 * @return void
	 */
	public static function detect_change(): void {
		$current    = wp_get_environment_type();
		$last_known = get_option( 'last_known_environment', 'unknown' );

		if ( $last_known === $current ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( sprintf( 'Environment changed from %s to %s.', $last_known, $current ) );

		do_action( 'environment_changed', $current, $last_known );
		update_option( 'last_known_environment', $current );
	}

	/**
	 * Handle environment change.
	 *
	 * @param string $new_environment The new environment type.
	 *
	 * @return void
	 */
	public static function environment_changed( $new_environment ) {
		update_option( 'blog_public', 'production' === $new_environment ? '1' : '0' );

		if ( 'local' !== $new_environment ) {
			return;
		}

		// Mail plugins talk to real inboxes, which is the last thing a local site should do.
		$plugins = apply_filters(
			'jcore_local_deactivate_plugins',
			array(
				'mailgun/mailgun.php',
				'smtp2go/smtp2go-wordpress-plugin.php',
			)
		);

		if ( ! empty( $plugins ) && function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $plugins );
		}
	}

	/**
	 * Point local mail at the development mail catcher instead of a real SMTP server.
	 *
	 * @param \PHPMailer\PHPMailer\PHPMailer $phpmailer The PHPMailer instance.
	 *
	 * @return void
	 */
	public static function phpmailer_init( $phpmailer ): void {
		if ( 'local' !== wp_get_environment_type() ) {
			return;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->Host = apply_filters( 'jcore_local_mail_host', 'mailhog' );
		$phpmailer->Port = (int) apply_filters( 'jcore_local_mail_port', 1025 );
		$phpmailer->isSMTP();
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}
}
