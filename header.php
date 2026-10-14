<?php
/**
 * Document head and header. An Elementor Theme Builder header takes priority when one is assigned.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bungry-skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'bungry-burger' ); ?></a>
<?php
get_template_part( 'template-parts/announcement' );
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	get_template_part( 'template-parts/header' );
	get_template_part( 'template-parts/mobile-menu' );
}
?>
<main id="content" class="bungry-main" tabindex="-1">
