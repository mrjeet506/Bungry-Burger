<?php
/**
 * Blog index: the posts page set under Settings > Reading (grid of cards, category filter, pagination).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

get_header();

$blog_page_id      = (int) get_option( 'page_for_posts' );
$bungry_blog_title = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'The Bungry blog', 'bungry-burger' );
$categories        = get_categories( array( 'hide_empty' => true ) );
?>
<section class="bungry-blog-hero">
	<p class="bungry-kicker"><?php esc_html_e( 'Blog', 'bungry-burger' ); ?></p>
	<h1><?php echo esc_html( $bungry_blog_title ); ?></h1>
</section>

<?php if ( ! empty( $categories ) ) : ?>
	<nav class="bungry-blog-cats" aria-label="<?php esc_attr_e( 'Blog categories', 'bungry-burger' ); ?>">
		<a class="bungry-blog-cats__item<?php echo is_home() && ! is_paged() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'All', 'bungry-burger' ); ?></a>
		<?php foreach ( $categories as $category ) : ?>
			<a class="bungry-blog-cats__item" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
		<?php endforeach; ?>
	</nav>
<?php endif; ?>

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
		<p class="bungry-blog-empty"><?php esc_html_e( 'No posts yet. Check back soon.', 'bungry-burger' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
