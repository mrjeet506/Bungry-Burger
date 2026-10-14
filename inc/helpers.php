<?php
/**
 * Shared helpers: settings access, bundled SVG art, placeholder tags and buttons.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default settings. An empty value means "not confirmed": the related UI is hidden,
 * or a placeholder tag is shown while the global "show tags" toggle is on.
 *
 * @return array<string,mixed>
 */
function bungry_default_settings(): array {
	return array(
		'logo_dark_url'        => '',
		'logo_light_url'       => '',
		'announcement_enabled' => 0,
		'announcement_text'    => '',
		'order_label'          => 'Order Online',
		'order_url'            => '',
		'address'              => '',
		'phone'                => '',
		'email'                => '',
		'hours'                => '',
		'social_instagram'     => '',
		'social_facebook'      => '',
		'social_tiktok'        => '',
		'social_youtube'       => '',
		'footer_blurb'         => 'Meet Bungry Burger. A fresh take on a familiar favourite, made memorable by our signature sealed-burger concept.',
		'copyright_text'       => '',
		'show_tags'            => 1,
		'analytics_notice'     => '',
	);
}

/**
 * @return array<string,mixed>
 */
function bungry_get_settings(): array {
	$saved = get_option( 'bungry_settings', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), bungry_default_settings() );
}

/**
 * @param string $key Setting key from bungry_default_settings().
 * @return mixed
 */
function bungry_setting( string $key ) {
	$settings = bungry_get_settings();
	return $settings[ $key ] ?? '';
}

function bungry_tags_enabled(): bool {
	return (bool) bungry_setting( 'show_tags' );
}

/**
 * "Client to confirm" placeholder pill. Renders nothing when the global toggle is off.
 *
 * @param string $label Optional label; defaults to "Client to confirm" (CSS uppercases it).
 * @param string $tone  'dark' for use on forest backgrounds, 'light' for cream backgrounds.
 */
function bungry_placeholder_tag( string $label = '', string $tone = 'dark' ): string {
	if ( ! bungry_tags_enabled() ) {
		return '';
	}
	$label = '' !== $label ? $label : __( 'Client to confirm', 'bungry-burger' );
	$tone  = in_array( $tone, array( 'dark', 'light' ), true ) ? $tone : 'dark';

	return sprintf(
		'<span class="bungry-tag bungry-tag--%1$s">%2$s</span>',
		esc_attr( $tone ),
		esc_html( $label )
	);
}

/**
 * Ordering URL, or an empty string when no ordering destination is confirmed.
 */
function bungry_order_url(): string {
	return esc_url( (string) bungry_setting( 'order_url' ) );
}

/**
 * Bundled Figma export (SVG) by relative path, e.g. "icons/arrow-right".
 * Returns an empty string when the file is missing, so missing art never breaks a page.
 */
function bungry_svg( string $path ): string {
	if ( ! preg_match( '#^(logos|icons|illustrations|patterns)/[a-z0-9-]+$#', $path ) ) {
		return '';
	}
	$file = BUNGRY_DIR . '/assets/svg/' . $path . '.svg';
	if ( ! is_readable( $file ) ) {
		return '';
	}
	// Bundled, trusted vector exports from the Figma design file (see assets/svg/manifest.json).
	$svg = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return (string) preg_replace( '/<\?xml[^>]*\?>/', '', $svg );
}

/**
 * Decorative SVG wrapper. Output is hidden from assistive tech.
 */
function bungry_art( string $path, string $css_class = '' ): string {
	$svg = bungry_svg( $path );
	if ( '' === $svg ) {
		return '';
	}
	return sprintf( '<span class="bungry-art %1$s" aria-hidden="true">%2$s</span>', esc_attr( $css_class ), $svg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Bungry button. Returns an empty string when no URL is given, so order/CTA buttons
 * never render without a working destination.
 *
 * @param array{label?:string,url?:string,style?:string,size?:string,arrow?:bool,class?:string,target_blank?:bool} $args
 */
function bungry_button( array $args ): string {
	$url = isset( $args['url'] ) ? esc_url( (string) $args['url'] ) : '';
	if ( '' === $url ) {
		return '';
	}
	$style = $args['style'] ?? 'primary';
	$style = in_array( $style, array( 'primary', 'dark', 'outline-light', 'outline-dark' ), true ) ? $style : 'primary';
	$size  = ( $args['size'] ?? 'default' ) === 'small' ? 'small' : 'default';
	$arrow = ! empty( $args['arrow'] );
	$label = (string) ( $args['label'] ?? '' );

	$classes = array( 'bungry-btn', 'bungry-btn--' . $style, 'bungry-btn--' . $size );
	if ( ! empty( $args['class'] ) ) {
		$classes[] = sanitize_html_class( (string) $args['class'] );
	}

	return sprintf(
		'<a class="%1$s" href="%2$s"%3$s><span class="bungry-btn__label">%4$s</span>%5$s</a>',
		esc_attr( implode( ' ', $classes ) ),
		$url,
		! empty( $args['target_blank'] ) ? ' target="_blank" rel="noopener noreferrer"' : '',
		esc_html( $label ),
		$arrow ? '<span class="bungry-btn__arrow" aria-hidden="true">&rarr;</span>' : ''
	);
}

/**
 * Logo for the header ("dark" on cream) or footer ("light" on forest).
 * Order of preference: uploaded logo URL in Bungry Settings, bundled Figma SVG, text wordmark.
 */
function bungry_logo_markup( string $tone = 'dark' ): string {
	$light = 'light' === $tone;
	$url   = esc_url( (string) bungry_setting( $light ? 'logo_light_url' : 'logo_dark_url' ) );
	if ( '' !== $url ) {
		return sprintf(
			'<img class="bungry-logo__img" src="%1$s" alt="%2$s" width="182" height="46" decoding="async">',
			$url,
			esc_attr( get_bloginfo( 'name' ) )
		);
	}

	$svg = bungry_art( 'logos/' . ( $light ? 'logo-full-light' : 'logo-full-dark' ), 'bungry-logo__svg' );
	if ( '' !== $svg ) {
		return $svg;
	}

	return '<span class="bungry-wordmark"><span class="bungry-wordmark__main">Bungry</span> <span class="bungry-wordmark__sub">Burger</span></span>';
}

/**
 * Renders a contact value, or a placeholder tag when it is not confirmed.
 * Returns an empty string when empty and tags are off (the row is hidden).
 *
 * @param string $value Stored value.
 * @param string $type  'phone', 'email', 'url' or 'text'.
 */
function bungry_contact_value( string $value, string $type = 'text' ): string {
	$value = trim( $value );
	if ( '' === $value ) {
		return bungry_placeholder_tag();
	}
	switch ( $type ) {
		case 'phone':
			$tel = preg_replace( '/[^0-9+]/', '', $value );
			return sprintf( '<a href="tel:%1$s">%2$s</a>', esc_attr( (string) $tel ), esc_html( $value ) );
		case 'email':
			return sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( sanitize_email( $value ) ) );
		default:
			return esc_html( $value );
	}
}

/**
 * Finds a published-or-draft post of a type by its title (slug match). Replaces the deprecated get_page_by_title().
 */
function bungry_find_post_by_title( string $title, string $post_type ): ?WP_Post {
	$posts = get_posts(
		array(
			'post_type'        => $post_type,
			'post_status'      => 'any',
			'name'             => sanitize_title( $title ),
			'numberposts'      => 1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);
	return $posts ? $posts[0] : null;
}

/**
 * Renders a registered menu location with the Bungry walker.
 *
 * @param string $location  Registered theme location.
 * @param string $list_class Extra class for the <ul>.
 */
function bungry_nav( string $location, string $list_class = '' ): string {
	return (string) wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => trim( 'bungry-nav__list ' . $list_class ),
			'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'walker'         => new Bungry_Nav_Walker(),
			'fallback_cb'    => 'bungry_nav_fallback',
			'echo'           => false,
		)
	);
}

/**
 * Fallback before menus are assigned: lists published top-level pages.
 */
function bungry_nav_fallback(): string {
	$pages = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'number'      => 6,
			'parent'      => 0,
		)
	);
	if ( empty( $pages ) ) {
		return '';
	}
	$out = '<ul class="bungry-nav__list">';
	foreach ( $pages as $page ) {
		$active = is_page( $page->ID ) ? ' is-active' : '';
		$out   .= sprintf(
			'<li class="bungry-nav__item"><a class="bungry-nav__link%1$s" href="%2$s"><span class="bungry-nav__text">%3$s</span><span class="bungry-nav__bar" aria-hidden="true"></span></a></li>',
			esc_attr( $active ),
			esc_url( get_permalink( $page ) ),
			esc_html( get_the_title( $page ) )
		);
	}
	return $out . '</ul>';
}
