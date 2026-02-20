<?php
/**
 * UP6 Suara Semasa — Child theme functions.
 *
 * @package UP6_Suara_Semasa
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

define( 'UP6_VERSION', wp_get_theme()->get( 'Version' ) );

/* ---------------------------------------------------------------
 * 1. Theme Setup
 * ------------------------------------------------------------- */
function up6_setup() {
	// Editor styles for front-end / editor parity.
	add_editor_style( 'assets/css/global.css' );
	add_editor_style( 'assets/css/editor/editor-overrides.css' );

	// Custom image sizes.
	add_image_size( 'up6-card-thumb', 600, 338, true );   // 16 : 9
	add_image_size( 'up6-hero', 1200, 675, true );         // 16 : 9
	add_image_size( 'up6-square-thumb', 300, 300, true );  // 1 : 1 sidebar

	// Make custom sizes selectable in the editor.
	add_filter( 'image_size_names_choose', function ( $sizes ) {
		return array_merge( $sizes, [
			'up6-card-thumb'   => __( 'Card Thumbnail (16:9)', 'up6-suara-semasa' ),
			'up6-hero'         => __( 'Hero Image (16:9)', 'up6-suara-semasa' ),
			'up6-square-thumb' => __( 'Square Thumbnail', 'up6-suara-semasa' ),
		] );
	} );
}
add_action( 'after_setup_theme', 'up6_setup' );

/* ---------------------------------------------------------------
 * 2. Front-end Styles
 * ------------------------------------------------------------- */
function up6_enqueue_styles() {
	// Parent theme.
	wp_enqueue_style(
		'twentytwentyfive-style',
		get_template_directory_uri() . '/style.css',
		[],
		wp_get_theme( 'twentytwentyfive' )->get( 'Version' )
	);

	// Child global.
	wp_enqueue_style(
		'up6-global',
		get_stylesheet_directory_uri() . '/assets/css/global.css',
		[ 'twentytwentyfive-style' ],
		UP6_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'up6_enqueue_styles' );

/* ---------------------------------------------------------------
 * 3. Block Component Styles (conditional loading)
 * ------------------------------------------------------------- */
function up6_enqueue_block_component_styles() {
	$components = [
		'core/group'       => 'card',
		'core/paragraph'   => 'kicker-label',
		'core/image'       => 'caption-credit',
		'core/post-terms'  => 'tag-chips',
		'core/query'       => 'trending-topics',
	];

	foreach ( $components as $block => $component ) {
		$path = get_stylesheet_directory() . "/assets/css/components/{$component}.css";
		if ( ! file_exists( $path ) ) {
			continue;
		}
		wp_enqueue_block_style( $block, [
			'handle' => "up6-{$component}",
			'src'    => get_stylesheet_directory_uri() . "/assets/css/components/{$component}.css",
			'path'   => $path,
		] );
	}
}
add_action( 'init', 'up6_enqueue_block_component_styles' );

/* ---------------------------------------------------------------
 * 4. Block Style Variations
 * ------------------------------------------------------------- */
function up6_register_block_styles() {
	// Card variants on core/group.
	$card_styles = [
		'card-standard' => __( 'Card — Standard', 'up6-suara-semasa' ),
		'card-accent'   => __( 'Card — Accent', 'up6-suara-semasa' ),
		'card-list'     => __( 'Card — List', 'up6-suara-semasa' ),
	];
	foreach ( $card_styles as $name => $label ) {
		register_block_style( 'core/group', compact( 'name', 'label' ) );
	}

	// Text component styles on core/paragraph.
	register_block_style( 'core/paragraph', [
		'name'  => 'kicker',
		'label' => __( 'Kicker Label', 'up6-suara-semasa' ),
	] );
	register_block_style( 'core/paragraph', [
		'name'  => 'dateline',
		'label' => __( 'Dateline', 'up6-suara-semasa' ),
	] );
	register_block_style( 'core/paragraph', [
		'name'  => 'breaking',
		'label' => __( 'Breaking / Live', 'up6-suara-semasa' ),
	] );
}
add_action( 'init', 'up6_register_block_styles' );

/* ---------------------------------------------------------------
 * 5. Navigation A11y Script (conditional)
 * ------------------------------------------------------------- */
function up6_navigation_a11y_script() {
	if ( has_block( 'core/navigation' ) ) {
		wp_enqueue_script(
			'up6-navigation-a11y',
			get_stylesheet_directory_uri() . '/assets/js/navigation-a11y.js',
			[],
			UP6_VERSION,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
	}
}
add_action( 'wp_enqueue_scripts', 'up6_navigation_a11y_script' );

/* ---------------------------------------------------------------
 * 6. Block Pattern Category
 * ------------------------------------------------------------- */
function up6_pattern_categories() {
	register_block_pattern_category( 'up6-suara-semasa', [
		'label' => __( 'UP6 Suara Semasa', 'up6-suara-semasa' ),
	] );
	register_block_pattern_category( 'up6-cards', [
		'label' => __( 'UP6 Cards', 'up6-suara-semasa' ),
	] );
	register_block_pattern_category( 'up6-article', [
		'label' => __( 'UP6 Article', 'up6-suara-semasa' ),
	] );
}
add_action( 'init', 'up6_pattern_categories' );

/* ---------------------------------------------------------------
 * 7. Disable core patterns we don't need
 * ------------------------------------------------------------- */
function up6_disable_remote_patterns() {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'up6_disable_remote_patterns' );
