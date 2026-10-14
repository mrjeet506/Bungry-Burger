<?php
/**
 * Contact form: a Contact Form 7 form created by the demo import, shown with [bungry_contact_form].
 * The shortcode is what the Elementor templates use, so the form can be changed in Contact Form 7 without editing the page.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_cf7_active(): bool {
	return function_exists( 'wpcf7' ) || class_exists( 'WPCF7_ContactForm' );
}

/**
 * [bungry_contact_form]: outputs the Bungry contact form, or a setup note for administrators.
 */
function bungry_contact_form_shortcode(): string {
	$form_id = (int) get_option( 'bungry_cf7_form_id' );
	if ( bungry_cf7_active() && $form_id && get_post( $form_id ) ) {
		return do_shortcode( sprintf( '[contact-form-7 id="%d"]', $form_id ) );
	}
	if ( current_user_can( 'manage_options' ) ) {
		return '<p class="bungry-field-note">' . esc_html__( 'Contact form not set up yet. Install and activate Contact Form 7, then run Appearance > Bungry Demo.', 'bungry-burger' ) . '</p>';
	}
	return '';
}
add_shortcode( 'bungry_contact_form', 'bungry_contact_form_shortcode' );

/**
 * Creates the Bungry contact form once (never overwrites an existing one).
 * Mail goes to the site admin address and replies go to the sender.
 */
function bungry_create_contact_form(): void {
	if ( ! bungry_cf7_active() ) {
		return;
	}
	$existing = (int) get_option( 'bungry_cf7_form_id' );
	if ( $existing && get_post( $existing ) ) {
		return;
	}

	$form = implode(
		"\n",
		array(
			'<p class="bungry-field-label">Name (optional)</p>',
			'[text your-name class:bungry-input]',
			'<p class="bungry-field-label">Email</p>',
			'[email* your-email class:bungry-input]',
			'<p class="bungry-field-label">Topic</p>',
			'[select your-topic class:bungry-input "General enquiry" "The menu" "Order or delivery" "Something else"]',
			'<p class="bungry-field-label">Message</p>',
			'[textarea* your-message class:bungry-input]',
			'[acceptance privacy-consent]I agree that Bungry Burger can reply to my message. Read our <a href="/privacy-policy/">Privacy Policy</a>.[/acceptance]',
			'[submit class:bungry-btn class:bungry-btn--primary "Send message"]',
		)
	);

	$mail = array(
		'subject'            => 'Bungry website enquiry: [your-topic]',
		'sender'             => '[_site_title] <' . get_option( 'admin_email' ) . '>',
		'recipient'          => get_option( 'admin_email' ),
		'body'               => "From: [your-name] <[your-email]>\nTopic: [your-topic]\n\nMessage:\n[your-message]",
		'additional_headers' => 'Reply-To: [your-email]',
		'attachments'        => '',
		'use_html'           => false,
		'exclude_blank'      => false,
	);

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wpcf7_contact_form',
			'post_title'  => 'Bungry Contact',
			'post_status' => 'publish',
		),
		true
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}
	update_post_meta( $post_id, '_form', $form );
	update_post_meta( $post_id, '_mail', $mail );
	update_post_meta( $post_id, '_mail_2', array( 'active' => false ) );
	update_option( 'bungry_cf7_form_id', $post_id );
}
