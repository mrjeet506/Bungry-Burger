<?php
/**
 * Coded header: logo, primary nav with active bar, Order Online (only when a URL is set), mobile toggle.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

$order_url   = bungry_order_url();
$order_label = (string) bungry_setting( 'order_label' );
$order_label = '' !== trim( $order_label ) ? $order_label : __( 'Order Online', 'bungry-burger' );
?>
<header class="bungry-header" role="banner">
	<div class="bungry-header__inner">
		<a class="bungry-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php echo bungry_logo_markup( 'dark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
		</a>

		<nav class="bungry-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'bungry-burger' ); ?>">
			<?php echo bungry_nav( 'primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- walker escapes output. ?>
		</nav>

		<div class="bungry-header__actions">
			<?php
			echo bungry_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bungry_button escapes internally.
				array(
					'label' => $order_label,
					'url'   => $order_url,
					'size'  => 'small',
					'style' => 'primary',
					'class' => 'bungry-header__order',
				)
			);
			?>
			<button type="button" class="bungry-menu-toggle" aria-expanded="false" aria-controls="bungry-mobile-menu">
				<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'bungry-burger' ); ?></span>
				<?php echo bungry_art( 'icons/menu', 'bungry-menu-toggle__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
			</button>
		</div>
	</div>
</header>
