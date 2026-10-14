<?php
/**
 * Closes <main> and renders the footer (or an Elementor Theme Builder footer when assigned).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'template-parts/footer' );
}
?>
</div>
<?php wp_footer(); ?>
</body>
</html>
