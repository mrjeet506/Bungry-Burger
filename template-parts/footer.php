<?php
/**
 * Coded footer: brand, quick links, need help, connect. Empty business details show placeholder tags.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

$socials      = array(
	'instagram' => 'social_instagram',
	'facebook'  => 'social_facebook',
	'tiktok'    => 'social_tiktok',
	'youtube'   => 'social_youtube',
);
$social_links = array();
foreach ( $socials as $icon => $setting ) {
	$url = esc_url( (string) bungry_setting( $setting ) );
	if ( '' !== $url ) {
		$social_links[] = array(
			'icon' => $icon,
			'url'  => $url,
		);
	}
}
$copyright = trim( (string) bungry_setting( 'copyright_text' ) );
if ( '' === $copyright ) {
	/* translators: %s: current year */
	$copyright = sprintf( __( '© %s Bungry Burger. All rights reserved.', 'bungry-burger' ), gmdate( 'Y' ) );
}
$address = trim( (string) bungry_setting( 'address' ) );
$phone   = trim( (string) bungry_setting( 'phone' ) );
$email   = trim( (string) bungry_setting( 'email' ) );
?>
<footer class="bungry-footer" role="contentinfo">
	<div class="bungry-footer__inner">
		<div class="bungry-footer__top">
			<div class="bungry-footer__brand">
				<?php echo bungry_logo_markup( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
				<p class="bungry-footer__tagline"><?php esc_html_e( 'Fresh. Sealed. Different.', 'bungry-burger' ); ?></p>
				<p class="bungry-footer__blurb"><?php echo esc_html( (string) bungry_setting( 'footer_blurb' ) ); ?></p>
			</div>

			<nav class="bungry-footer__col" aria-labelledby="bungry-ft-quick">
				<h2 class="bungry-footer__heading" id="bungry-ft-quick"><?php esc_html_e( 'Quick links', 'bungry-burger' ); ?></h2>
				<?php
				echo wp_nav_menu( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes menu output.
					array(
						'theme_location' => 'footer-quick',
						'container'      => false,
						'menu_class'     => 'bungry-footer__links',
						'fallback_cb'    => false,
						'echo'           => false,
					)
				);
				?>
			</nav>

			<nav class="bungry-footer__col" aria-labelledby="bungry-ft-help">
				<h2 class="bungry-footer__heading" id="bungry-ft-help"><?php esc_html_e( 'Need help?', 'bungry-burger' ); ?></h2>
				<?php
				echo wp_nav_menu( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes menu output.
					array(
						'theme_location' => 'footer-help',
						'container'      => false,
						'menu_class'     => 'bungry-footer__links',
						'fallback_cb'    => false,
						'echo'           => false,
					)
				);
				?>
			</nav>

			<div class="bungry-footer__col" aria-labelledby="bungry-ft-connect">
				<h2 class="bungry-footer__heading" id="bungry-ft-connect"><?php esc_html_e( 'Connect', 'bungry-burger' ); ?></h2>
				<ul class="bungry-footer__contact">
					<li>
						<span class="bungry-footer__label"><?php esc_html_e( 'Address', 'bungry-burger' ); ?></span>
						<?php echo '' !== $address ? esc_html( $address ) : bungry_placeholder_tag(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above or in helper. ?>
					</li>
					<li>
						<span class="bungry-footer__label"><?php esc_html_e( 'Phone', 'bungry-burger' ); ?></span>
						<?php echo '' !== $phone ? bungry_contact_value( $phone, 'phone' ) : bungry_placeholder_tag(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					</li>
					<li>
						<span class="bungry-footer__label"><?php esc_html_e( 'Email', 'bungry-burger' ); ?></span>
						<?php echo '' !== $email ? bungry_contact_value( $email, 'email' ) : bungry_placeholder_tag( __( 'Verify email', 'bungry-burger' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
					</li>
				</ul>
				<div class="bungry-social">
					<?php foreach ( $social_links as $social_link ) : ?>
						<a class="bungry-social__link" href="<?php echo esc_url( $social_link['url'] ); ?>" rel="me noopener noreferrer" target="_blank">
							<span class="screen-reader-text"><?php echo esc_html( ucfirst( $social_link['icon'] ) ); ?></span>
							<?php echo bungry_art( 'icons/' . $social_link['icon'], 'bungry-social__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
						</a>
					<?php endforeach; ?>
					<?php if ( empty( $social_links ) ) : ?>
						<?php echo bungry_placeholder_tag( __( 'Official links TBC', 'bungry-burger' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="bungry-footer__bottom">
			<p><?php echo esc_html( $copyright ); ?></p>
			<p><?php esc_html_e( 'New Zealand', 'bungry-burger' ); ?></p>
		</div>
	</div>
</footer>
