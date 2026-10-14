<?php
/**
 * Page preloader: a short loading screen shown on full page loads.
 * Enabled from Appearance > Bungry Settings. Hides when the page has loaded, or after 4 seconds at most.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when the preloader is switched on and this is a normal front-end view (not the Elementor editor).
 */
function bungry_preloader_active(): bool {
	if ( ! (int) bungry_setting( 'preloader_enabled' ) || is_admin() ) {
		return false;
	}
	if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		return false;
	}
	return true;
}

function bungry_preloader_enqueue(): void {
	if ( ! bungry_preloader_active() ) {
		return;
	}
	wp_enqueue_style( 'bungry-preloader', BUNGRY_URI . '/assets/css/preloader.css', array( 'bungry-base' ), BUNGRY_VERSION );
	wp_enqueue_script(
		'bungry-preloader',
		BUNGRY_URI . '/assets/js/preloader.js',
		array(),
		BUNGRY_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'bungry_preloader_enqueue' );

/**
 * Outputs the loading screen. Shows the optional text setting, or the logo when the text is empty.
 */
function bungry_preloader_markup(): void {
	if ( ! bungry_preloader_active() ) {
		return;
	}
	$text = trim( (string) bungry_setting( 'preloader_text' ) );
	$mark = '' !== $text
		? '<span class="bungry-preloader__text">' . esc_html( $text ) . '</span>'
		: bungry_logo_markup( 'dark' );
	?>
	<div class="bungry-preloader" role="status" aria-live="polite">
		<span class="screen-reader-text"><?php esc_html_e( 'Loading', 'bungry-burger' ); ?></span>
		<div class="bungry-preloader__inner">
			<?php echo $mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above or built from escaped parts. ?>
			<span class="bungry-preloader__bar" aria-hidden="true"></span>
		</div>
	</div>
	<?php
}
add_action( 'wp_body_open', 'bungry_preloader_markup', 1 );
