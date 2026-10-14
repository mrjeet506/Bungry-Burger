<?php
/**
 * Fallback template for pages and posts. Elementor-built pages render through the Theme Builder or the_content().
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="bungry-container bungry-page">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			?>
			<article <?php post_class( 'bungry-article' ); ?>>
				<h1 class="screen-reader-text"><?php the_title(); ?></h1>
				<div class="bungry-prose">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		}
	} else {
		?>
		<h1 class="screen-reader-text"><?php esc_html_e( 'Nothing found', 'bungry-burger' ); ?></h1>
		<?php
	}
	?>
</div>
<?php
get_footer();
