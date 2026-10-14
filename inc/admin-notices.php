<?php
/**
 * Admin notices: recommend Elementor when it is missing or too old. The theme keeps working either way.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_elementor_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! did_action( 'elementor/loaded' ) ) {
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'Bungry Burger is built for Elementor. Install and activate Elementor (3.20 or newer) to use the Bungry widgets, Site Settings kit and demo pages. The coded header and footer still work without it.', 'bungry-burger' )
		);
		return;
	}

	if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.20.0', '<' ) ) {
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html__( 'Bungry Burger requires Elementor 3.20 or newer. Please update Elementor.', 'bungry-burger' )
		);
	}
}
add_action( 'admin_notices', 'bungry_elementor_notice' );
