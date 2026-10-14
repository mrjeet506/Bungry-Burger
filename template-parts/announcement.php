<?php
/**
 * Announcement strip. Hidden unless it is switched on AND has text.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

$text = trim( (string) bungry_setting( 'announcement_text' ) );
if ( ! bungry_setting( 'announcement_enabled' ) || '' === $text ) {
	return;
}
?>
<div class="bungry-announcement" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'bungry-burger' ); ?>">
	<p class="bungry-announcement__text"><?php echo esc_html( $text ); ?></p>
</div>
