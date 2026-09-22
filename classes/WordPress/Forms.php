<?php
/**
 * Form plugin integrations.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;
use WP_HTML_Tag_Processor;

/**
 * Class Forms
 *
 * Makes Gravity Forms output fit the theme: the submit button gets the same class
 * as every other button, and confirmations scroll into view.
 *
 * @since 4.3.0
 */
class Forms {
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

		add_filter( 'gform_confirmation_anchor', '__return_true' );
		add_filter( 'gform_submit_button', array( static::class, 'submit_button' ), 10, 2 );
	}

	/**
	 * Add the theme's button class to the Gravity Forms submit button.
	 *
	 * @param string $button The HTML markup for the button.
	 * @param array  $form   The form being rendered.
	 *
	 * @return string
	 */
	public static function submit_button( $button, $form ): string {
		$classes = apply_filters( 'jcore_form_button_classes', array( 'btn' ), $form );
		if ( empty( $classes ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
			return $button;
		}

		$processor = new WP_HTML_Tag_Processor( $button );
		if ( ! $processor->next_tag() ) {
			return $button;
		}
		foreach ( $classes as $class ) {
			$processor->add_class( $class );
		}

		return $processor->get_updated_html();
	}
}
