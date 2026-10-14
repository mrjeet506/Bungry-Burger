<?php
/**
 * Full-screen forest mobile menu overlay. Opened by the header toggle; see assets/js/nav.js.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

$order_url   = bungry_order_url();
$order_label = (string) bungry_setting( 'order_label' );
$order_label = '' !== trim( $order_label ) ? $order_label : __( 'Order Online', 'bungry-burger' );
?>
<div class="bungry-mobile-menu" id="bungry-mobile-menu" hidden>
	<div class="bungry-mobile-menu__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'bungry-burger' ); ?>">
		<div class="bungry-mobile-menu__top">
			<?php echo bungry_logo_markup( 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
			<button type="button" class="bungry-menu-close">
				<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'bungry-burger' ); ?></span>
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<nav class="bungry-mobile-menu__nav" aria-label="<?php esc_attr_e( 'Mobile', 'bungry-burger' ); ?>">
			<?php echo bungry_nav( 'primary', 'bungry-nav__list--stacked' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- walker escapes output. ?>
		</nav>
		<?php
		echo bungry_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bungry_button escapes internally.
			array(
				'label' => $order_label,
				'url'   => $order_url,
				'style' => 'primary',
				'class' => 'bungry-mobile-menu__order',
			)
		);
		?>
	</div>
</div>
