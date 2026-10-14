<?php
/**
 * Fallback template for pages and posts. Elementor-built pages render through the Theme Builder or the_content().
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<?php
// Elementor-built pages run full width (sections set their own width). Other content keeps the 1280px container.
$bungry_doc  = is_singular() && class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::$instance->documents->get( get_queried_object_id() ) : false;
$bungry_wrap = $bungry_doc && $bungry_doc->is_built_with_elementor() ? 'bungry-page-elementor' : 'bungry-container bungry-page';
?>
<div class="<?php echo esc_attr( $bungry_wrap ); ?>">
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
