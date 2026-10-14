<?php
/**
 * Navigation walker: flat links with the orange active bar from the Figma header.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Flat navigation: sub-menu items are output as top-level links.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		// Intentionally empty.
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		// Intentionally empty.
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes  = empty( $item->classes ) ? array() : (array) $item->classes;
		$active   = (bool) array_intersect( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-page-ancestor' ) );
		$link_cls = 'bungry-nav__link' . ( $active ? ' is-active' : '' );
		$aria     = $active ? ' aria-current="page"' : '';

		$output .= '<li class="bungry-nav__item">';
		$output .= sprintf(
			'<a class="%1$s" href="%2$s"%3$s><span class="bungry-nav__text">%4$s</span><span class="bungry-nav__bar" aria-hidden="true"></span></a>',
			esc_attr( $link_cls ),
			esc_url( (string) $item->url ),
			$aria, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string built above.
			esc_html( (string) $item->title )
		);
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}
}
