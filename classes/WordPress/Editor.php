<?php
/**
 * Block editor policy: which blocks and variations editors get to use.
 *
 * @package Jcore\Ydin\WordPress
 */

namespace Jcore\Ydin\WordPress;

use Jcore\Ydin\InitOnce;

/**
 * Class Editor
 *
 * Trims the block inserter down to the blocks JCORE projects actually use, and
 * registers the shared spacer block styles.
 *
 * Every list is filterable, so a project that needs a block back only has to
 * remove it from the list instead of re-implementing the whole restriction.
 *
 * @since 4.3.0
 */
class Editor {
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

		add_action( 'init', array( static::class, 'register_spacer_styles' ) );
		add_action( 'after_setup_theme', array( static::class, 'remove_core_patterns' ), 20 );
		add_action( 'enqueue_block_editor_assets', array( static::class, 'enqueue_restrictions' ) );
		add_filter( 'register_block_type_args', array( static::class, 'spacer_default_height' ), 10, 2 );
	}

	/**
	 * Core blocks that should not be available in the inserter.
	 *
	 * @return string[]
	 */
	public static function get_restricted_blocks(): array {
		return (array) apply_filters(
			'jcore_restricted_blocks',
			array(
				'core/archives',
				'core/calendar',
				'core/categories',
				'core/column',
				'core/columns',
				'core/latest-comments',
				'core/latest-posts',
				'core/media-text',
				'core/more',
				'core/navigation',
				'core/nextpage',
				'core/page-list',
				'core/query',
				'core/query-no-results',
				'core/query-pagination',
				'core/quote',
				'core/site-tagline',
				'core/site-title',
				'core/social-link',
				'core/social-links',
				'core/tag-cloud',
				'core/term-description',
				'core/verse',
				'core/widget-group',
			)
		);
	}

	/**
	 * Block variations that should not be available in the inserter.
	 *
	 * Keyed by block name, each value is a list of variation names.
	 *
	 * @return array<string, string[]>
	 */
	public static function get_restricted_variations(): array {
		return (array) apply_filters(
			'jcore_restricted_block_variations',
			array(
				'core/group' => array(
					'group-grid',
					'group-row',
					'group-stack',
				),
				'core/embed' => array(
					'amazon-kindle',
					'animoto',
					'bluesky',
					'cloudup',
					'crowdsignal',
					'dailymotion',
					'flickr',
					'imgur',
					'issuu',
					'kickstarter',
					'mixcloud',
					'pinterest',
					'pocket-casts',
					'reddit',
					'reverbnation',
					'screencast',
					'scribd',
					'smugmug',
					'soundcloud',
					'speaker-deck',
					'spotify',
					'ted',
					'tiktok',
					'tumblr',
					'twitter',
					'videopress',
					'vimeo',
					'wolfram-cloud',
					'wordpress',
					'wordpress-tv',
				),
			)
		);
	}

	/**
	 * Enqueue the generated restriction script.
	 *
	 * The script is generated from the filterable lists above, so there is no asset
	 * to build or keep in sync.
	 *
	 * @return void
	 */
	public static function enqueue_restrictions(): void {
		if ( ! static::should_restrict() ) {
			return;
		}

		$blocks     = array_values( array_unique( static::get_restricted_blocks() ) );
		$variations = static::get_restricted_variations();

		if ( empty( $blocks ) && empty( $variations ) ) {
			return;
		}

		$handle = 'jcore-editor-restrictions';
		// The script has no source file, only the inline script below, so it is versioned by the lists it is built from.
		$version = substr( md5( (string) wp_json_encode( array( $blocks, $variations ) ) ), 0, 8 );

		wp_register_script( $handle, false, array( 'wp-blocks', 'wp-dom-ready' ), $version, true );
		wp_enqueue_script( $handle );

		$script = sprintf(
			'wp.domReady(function(){var b=%s,v=%s;' .
			'b.forEach(function(n){if(wp.blocks.getBlockType(n)){wp.blocks.unregisterBlockType(n);}});' .
			'Object.keys(v).forEach(function(n){if(!wp.blocks.getBlockType(n)){return;}' .
			'v[n].forEach(function(x){wp.blocks.unregisterBlockVariation(n,x);});});});',
			wp_json_encode( $blocks ),
			wp_json_encode( (object) $variations )
		);

		wp_add_inline_script( $handle, $script );
	}

	/**
	 * Whether the restrictions apply to the editor currently being loaded.
	 *
	 * The Site Editor is left alone by default: templates are built by developers
	 * and legitimately use blocks, such as the Query Loop, that content editors
	 * have no business inserting into a page.
	 *
	 * @return bool
	 */
	public static function should_restrict(): bool {
		$restrict = true;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen instanceof \WP_Screen && ! empty( $screen->id ) && str_contains( $screen->id, 'site-editor' ) ) {
				$restrict = apply_filters( 'jcore_restrict_blocks_in_site_editor', false );
			}
		}

		return (bool) apply_filters( 'jcore_restrict_blocks', $restrict );
	}

	/**
	 * Register the responsive spacer block styles.
	 *
	 * @return void
	 */
	public static function register_spacer_styles(): void {
		$styles = apply_filters(
			'jcore_spacer_styles',
			array(
				'sm' => __( 'Small', 'jcore' ),
				'md' => __( 'Medium', 'jcore' ),
				'lg' => __( 'Large', 'jcore' ),
				'xl' => __( 'X-Large', 'jcore' ),
			)
		);

		foreach ( $styles as $name => $label ) {
			register_block_style(
				'core/spacer',
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
	}

	/**
	 * Make a newly inserted spacer small, since the spacer styles carry the height.
	 *
	 * @param array  $args The block type arguments.
	 * @param string $name The block name.
	 *
	 * @return array
	 */
	public static function spacer_default_height( array $args, string $name ): array {
		if ( 'core/spacer' !== $name ) {
			return $args;
		}
		if ( isset( $args['attributes']['height']['default'] ) ) {
			$args['attributes']['height']['default'] = apply_filters( 'jcore_spacer_default_height', '1rem' );
		}

		return $args;
	}

	/**
	 * Remove the core block patterns, which rarely fit a JCORE design.
	 *
	 * @return void
	 */
	public static function remove_core_patterns(): void {
		if ( ! apply_filters( 'jcore_remove_core_block_patterns', true ) ) {
			return;
		}
		remove_theme_support( 'core-block-patterns' );
	}
}
