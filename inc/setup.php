<?php
/**
 * Theme setup, head metadata and per-page SEO description field.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_setup(): void {
	load_theme_textdomain( 'bungry-burger', BUNGRY_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	// Lets Elementor Theme Builder replace the coded header/footer (see template-parts).
	add_theme_support( 'elementor_header_footer' );

	register_nav_menus(
		array(
			'primary'      => __( 'Primary (header)', 'bungry-burger' ),
			'footer-quick' => __( 'Footer: quick links', 'bungry-burger' ),
			'footer-help'  => __( 'Footer: need help', 'bungry-burger' ),
		)
	);

	add_image_size( 'bungry-card', 600, 450, true );
}
add_action( 'after_setup_theme', 'bungry_setup' );

/**
 * Per-page meta description (pages, menu items). Falls back to the excerpt.
 */
function bungry_register_meta(): void {
	foreach ( array( 'page', 'bungry_menu' ) as $type ) {
		register_post_meta(
			$type,
			'_bungry_meta_description',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}
add_action( 'init', 'bungry_register_meta' );

function bungry_add_meta_box(): void {
	add_meta_box(
		'bungry-seo',
		__( 'Bungry SEO', 'bungry-burger' ),
		'bungry_render_seo_box',
		array( 'page', 'bungry_menu' ),
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'bungry_add_meta_box' );

function bungry_render_seo_box( WP_Post $post ): void {
	wp_nonce_field( 'bungry_seo_save', 'bungry_seo_nonce' );
	$value = (string) get_post_meta( $post->ID, '_bungry_meta_description', true );
	printf(
		'<p><label for="bungry_meta_description"><strong>%1$s</strong></label></p><textarea id="bungry_meta_description" name="bungry_meta_description" rows="3" class="widefat" maxlength="320">%2$s</textarea><p class="description">%3$s</p>',
		esc_html__( 'Meta description', 'bungry-burger' ),
		esc_textarea( $value ),
		esc_html__( 'Aim for 120–160 characters. Leave empty to use the excerpt.', 'bungry-burger' )
	);
}

function bungry_save_seo_box( int $post_id ): void {
	if ( ! isset( $_POST['bungry_seo_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bungry_seo_nonce'] ) ), 'bungry_seo_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	$value = isset( $_POST['bungry_meta_description'] )
		? sanitize_textarea_field( wp_unslash( $_POST['bungry_meta_description'] ) )
		: '';
	update_post_meta( $post_id, '_bungry_meta_description', $value );
}
add_action( 'save_post', 'bungry_save_seo_box' );

/**
 * Meta description and Open Graph tags. The <title> is handled by title-tag support.
 * Structured data (Restaurant, Menu, FAQPage) is intentionally not emitted until the
 * client supplies confirmed data.
 */
function bungry_head_meta(): void {
	$description = '';
	$title       = get_bloginfo( 'name' );
	$url         = home_url( '/' );

	if ( is_singular() ) {
		$post_id     = get_queried_object_id();
		$description = (string) get_post_meta( $post_id, '_bungry_meta_description', true );
		if ( '' === $description ) {
			$description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		}
		$title = get_the_title( $post_id ) . ' | ' . get_bloginfo( 'name' );
		$url   = get_permalink( $post_id );
	} elseif ( is_front_page() ) {
		$description = get_bloginfo( 'description' );
	}

	$description = trim( wp_strip_all_tags( $description ) );
	if ( '' !== $description ) {
		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( mb_substr( $description, 0, 300 ) ) );
	}

	printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta property=\"og:type\" content=\"%s\">\n", is_singular() ? 'article' : 'website' );
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
	if ( '' !== $description ) {
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( mb_substr( $description, 0, 300 ) ) );
	}
}
add_action( 'wp_head', 'bungry_head_meta', 5 );
