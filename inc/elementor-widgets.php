<?php
/**
 * Elementor integration: "Bungry" widget category and widget registration.
 * Widgets are only loaded when Elementor is active, so the theme never fatals without it.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget class files, keyed by file slug. Each file defines a class named Bungry_Widget_{Slug}.
 *
 * @return array<string,string> slug => class name
 */
function bungry_widget_map(): array {
	return array(
		'button'          => 'Bungry_Widget_Button',
		'placeholder-tag' => 'Bungry_Widget_Placeholder_Tag',
		'section-heading' => 'Bungry_Widget_Section_Heading',
		'menu-cards'      => 'Bungry_Widget_Menu_Cards',
		'notice'          => 'Bungry_Widget_Notice',
		'faq'             => 'Bungry_Widget_FAQ',
		'info-rows'       => 'Bungry_Widget_Info_Rows',
		'cta-band'        => 'Bungry_Widget_CTA_Band',
	);
}

function bungry_register_elementor_category( $elements_manager ): void {
	$elements_manager->add_category(
		'bungry',
		array(
			'title' => __( 'Bungry', 'bungry-burger' ),
			'icon'  => 'eicon-star',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'bungry_register_elementor_category' );

function bungry_register_elementor_widgets( $widgets_manager ): void {
	foreach ( bungry_widget_map() as $slug => $class ) {
		$file = BUNGRY_DIR . '/elementor/widgets/class-bungry-widget-' . $slug . '.php';
		if ( ! is_readable( $file ) ) {
			continue;
		}
		require_once $file;
		if ( class_exists( $class ) ) {
			$widgets_manager->register( new $class() );
		}
	}
}
add_action( 'elementor/widgets/register', 'bungry_register_elementor_widgets' );

/**
 * Enqueue the widget-facing script/styles only where Elementor widgets render.
 */
function bungry_elementor_frontend_assets(): void {
	wp_enqueue_style( 'bungry-widgets', BUNGRY_URI . '/assets/css/widgets.css', array( 'bungry-base' ), BUNGRY_VERSION );
}
add_action( 'elementor/frontend/after_enqueue_styles', 'bungry_elementor_frontend_assets' );
