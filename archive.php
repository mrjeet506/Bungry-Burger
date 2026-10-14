<?php
/**
 * Category, tag, author and date archives.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bungry-blog-hero">
	<p class="bungry-kicker"><?php esc_html_e( 'Blog', 'bungry-burger' ); ?></p>
	<h1><?php echo esc_html( get_the_archive_title() ); ?></h1>
	<?php if ( get_the_archive_description() ) : ?>
		<div class="bungry-blog-hero__text"><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
	<?php endif; ?>
</section>

<div class="bungry-blog-wrap">
	<?php if ( have_posts() ) : ?>
		<div class="bungry-post-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/post-card' );
			}
			?>
		</div>
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( 'Newer', 'bungry-burger' ),
				'next_text' => __( 'Older', 'bungry-burger' ),
			)
		);
		?>
	<?php else : ?>
		<p class="bungry-blog-empty"><?php esc_html_e( 'No posts found in this archive.', 'bungry-burger' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
