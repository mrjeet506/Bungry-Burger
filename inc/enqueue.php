<?php
/**
 * Front-end assets. Vanilla JS only, no jQuery. Fonts are self-hosted with font-display: swap.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_enqueue_assets(): void {
	wp_enqueue_style( 'bungry-fonts', BUNGRY_URI . '/assets/fonts/fonts.css', array(), BUNGRY_VERSION );
	wp_enqueue_style( 'bungry-base', BUNGRY_URI . '/assets/css/base.css', array( 'bungry-fonts' ), BUNGRY_VERSION );
	wp_enqueue_style( 'bungry-blog', BUNGRY_URI . '/assets/css/blog.css', array( 'bungry-base' ), BUNGRY_VERSION );
	wp_enqueue_script(
		'bungry-nav',
		BUNGRY_URI . '/assets/js/nav.js',
		array(),
		BUNGRY_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bungry_enqueue_assets' );

/**
 * Let the Elementor editor preview use the same base tokens and header/footer styles.
 */
function bungry_enqueue_editor_assets(): void {
	wp_enqueue_style( 'bungry-fonts', BUNGRY_URI . '/assets/fonts/fonts.css', array(), BUNGRY_VERSION );
	wp_enqueue_style( 'bungry-base', BUNGRY_URI . '/assets/css/base.css', array( 'bungry-fonts' ), BUNGRY_VERSION );
}
add_action( 'elementor/editor/after_enqueue_styles', 'bungry_enqueue_editor_assets' );
