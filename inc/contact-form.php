<?php
/**
 * Contact Form 7 setup matched to the Figma contact form, with a branded HTML email to the site admin.
 * [bungry_contact_form] outputs the form, so the Elementor Find Us page never needs editing.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_cf7_active(): bool {
	return function_exists( 'wpcf7' ) || class_exists( 'WPCF7_ContactForm' );
}

/**
 * [bungry_contact_form]: the Bungry form, or a setup note for administrators.
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
 * Form markup (Figma: Name (optional), Email, Topic, Message, privacy line, SEND MESSAGE).
 */
function bungry_cf7_form_markup(): string {
	return implode(
		"\n",
		array(
			'<p class="bungry-field-label">Name (optional)</p>',
			'[text your-name class:bungry-input placeholder "Your name"]',
			'<p class="bungry-field-label">Email</p>',
			'[email* your-email class:bungry-input placeholder "you@example.com"]',
			'<p class="bungry-field-label">Topic</p>',
			'[select* your-topic class:bungry-input first_as_label "Select a topic" "General enquiry" "The menu" "Order or delivery" "Something else"]',
			'<p class="bungry-field-label">Message</p>',
			'[textarea* your-message class:bungry-input placeholder "How can we help?"]',
			'[acceptance privacy-consent]We’ll use your details only to reply. Read our <a href="/privacy-policy/">Privacy Policy</a>.[/acceptance]',
			'[submit class:bungry-btn class:bungry-btn--primary "Send message"]',
		)
	);
}

/**
 * Branded HTML email the admin receives. Mail tags are filled in by Contact Form 7.
 */
function bungry_cf7_mail_html(): string {
	return <<<'HTML'
<!doctype html>
<html>
<body style="margin:0;padding:0;background:#F6F7EE;font-family:Montserrat,Arial,sans-serif;color:#0E3A1A;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F6F7EE;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#FFFFFF;border-radius:20px;overflow:hidden;">
<tr><td style="background:#185B28;padding:28px 32px;">
<div style="font-family:'Bakbak One',Arial,sans-serif;font-size:26px;line-height:30px;color:#F6F7EE;text-transform:uppercase;">Bungry Burger</div>
<div style="font-size:12px;font-weight:700;letter-spacing:1.4px;color:#F6A531;margin-top:6px;text-transform:uppercase;">New website enquiry</div>
</td></tr>
<tr><td style="padding:32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:0 0 16px;font-size:12px;font-weight:700;letter-spacing:1px;color:#6D7A38;text-transform:uppercase;">Name</td></tr>
<tr><td style="padding:0 0 20px;font-size:16px;line-height:26px;">[your-name]</td></tr>
<tr><td style="padding:0 0 16px;font-size:12px;font-weight:700;letter-spacing:1px;color:#6D7A38;text-transform:uppercase;">Email</td></tr>
<tr><td style="padding:0 0 20px;font-size:16px;line-height:26px;"><a href="mailto:[your-email]" style="color:#185B28;">[your-email]</a></td></tr>
<tr><td style="padding:0 0 16px;font-size:12px;font-weight:700;letter-spacing:1px;color:#6D7A38;text-transform:uppercase;">Topic</td></tr>
<tr><td style="padding:0 0 20px;font-size:16px;line-height:26px;">[your-topic]</td></tr>
<tr><td style="padding:0 0 16px;font-size:12px;font-weight:700;letter-spacing:1px;color:#6D7A38;text-transform:uppercase;">Message</td></tr>
<tr><td style="padding:16px 20px;background:#F6F7EE;border-radius:12px;font-size:16px;line-height:26px;">[your-message]</td></tr>
</table>
</td></tr>
<tr><td style="padding:20px 32px;background:#E9EBD7;font-size:13px;line-height:22px;color:#0E3A1A;">
Reply to this email to answer the sender directly. Sent from [_site_url] on [_date] at [_time].
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;
}

/**
 * Creates the Bungry contact form once; never overwrites an existing one.
 */
function bungry_create_contact_form(): void {
	if ( ! bungry_cf7_active() ) {
		return;
	}
	$existing = (int) get_option( 'bungry_cf7_form_id' );
	if ( $existing && get_post( $existing ) ) {
		return;
	}

	$admin = get_option( 'admin_email' );
	$mail  = array(
		'subject'            => 'Bungry website enquiry: [your-topic]',
		'sender'             => '[_site_title] <' . $admin . '>',
		'recipient'          => $admin,
		'body'               => bungry_cf7_mail_html(),
		'additional_headers' => 'Reply-To: [your-email]',
		'attachments'        => '',
		'use_html'           => true,
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
	update_post_meta( $post_id, '_form', bungry_cf7_form_markup() );
	update_post_meta( $post_id, '_mail', $mail );
	update_post_meta( $post_id, '_mail_2', array( 'active' => false ) );
	update_option( 'bungry_cf7_form_id', $post_id );
}
