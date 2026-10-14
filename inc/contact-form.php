<?php
/**
 * Contact form: the WPForms form titled "Bungry Contact" (import forms/bungry-contact-wpforms.json).
 * [bungry_contact_form] outputs it, so the Elementor Find Us page never needs editing.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * Finds the WPForms form titled "Bungry Contact". Returns its ID, or 0 when it does not exist.
 */
function bungry_contact_form_id(): int {
	if ( ! function_exists( 'wpforms' ) ) {
		return 0;
	}
	$forms = get_posts(
		array(
			'post_type'        => 'wpforms',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		)
	);
	foreach ( $forms as $form ) {
		if ( 'Bungry Contact' === $form->post_title ) {
			return (int) $form->ID;
		}
	}
	return 0;
}

/**
 * [bungry_contact_form]: the Bungry form, or a setup note for administrators.
 */
function bungry_contact_form_shortcode(): string {
	$form_id = bungry_contact_form_id();
	if ( $form_id ) {
		return do_shortcode( sprintf( '[wpforms id="%d"]', $form_id ) );
	}
	if ( current_user_can( 'manage_options' ) ) {
		return '<p class="bungry-field-note">' . esc_html__( 'Contact form not set up yet. Install WPForms, then import forms/bungry-contact-wpforms.json (WPForms > Tools > Import).', 'bungry-burger' ) . '</p>';
	}
	return '';
}
add_shortcode( 'bungry_contact_form', 'bungry_contact_form_shortcode' );
