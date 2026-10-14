<?php
/**
 * Background animation: a fixed canvas behind the page content.
 * Mode is set in Bungry Settings > Brand > Background animation (off, calm, crazy).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns 'off', 'calm' or 'crazy'.
 */
function bungry_fx_mode(): string {
	$mode = (string) bungry_setting( 'fx_mode' );
	return in_array( $mode, array( 'calm', 'crazy' ), true ) ? $mode : 'off';
}

function bungry_fx_enqueue(): void {
	if ( 'off' === bungry_fx_mode() ) {
		return;
	}
	wp_enqueue_style( 'bungry-fx', BUNGRY_URI . '/assets/css/fx.css', array( 'bungry-base' ), BUNGRY_VERSION );
	wp_enqueue_script(
		'bungry-fx',
		BUNGRY_URI . '/assets/js/fx.js',
		array(),
		BUNGRY_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bungry_fx_enqueue' );

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function bungry_fx_body_class( array $classes ): array {
	if ( 'off' !== bungry_fx_mode() ) {
		$classes[] = 'bungry-fx-on';
	}
	return $classes;
}
add_filter( 'body_class', 'bungry_fx_body_class' );

/**
 * Outputs the canvas layer. Called from wp_body_open(), which header.php runs.
 */
function bungry_fx_layer(): void {
	$mode = bungry_fx_mode();
	if ( 'off' === $mode ) {
		return;
	}
	printf(
		'<div class="bungry-fx" data-mode="%s" aria-hidden="true"><canvas></canvas></div>',
		esc_attr( $mode )
	);
}
add_action( 'wp_body_open', 'bungry_fx_layer' );
