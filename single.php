<?php
/**
 * Single blog post: hero, featured image, article, tags, previous/next and related posts.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$categories = get_the_category();
	$category   = ! empty( $categories ) ? $categories[0] : null;
	?>
	<article <?php post_class( 'bungry-article-single' ); ?>>
		<header class="bungry-blog-hero">
			<?php if ( $category ) : ?>
				<p class="bungry-kicker"><a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></p>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<p class="bungry-blog-meta">
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<span aria-hidden="true">&middot;</span>
				<?php echo esc_html( get_the_author() ); ?>
			</p>
		</header>

		<div class="bungry-blog-wrap bungry-blog-wrap--narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="bungry-blog-figure">
					<?php
					the_post_thumbnail(
						'large',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="bungry-prose bungry-blog-prose">
				<?php the_content(); ?>
			</div>

			<?php if ( has_tag() ) : ?>
				<p class="bungry-blog-tags"><?php the_tags( '', ' ' ); ?></p>
			<?php endif; ?>

			<nav class="bungry-blog-pager" aria-label="<?php esc_attr_e( 'Posts', 'bungry-burger' ); ?>">
				<?php
				previous_post_link( '<span class="bungry-blog-pager__label">' . esc_html__( 'Previous', 'bungry-burger' ) . '</span>%link', '%title' );
				next_post_link( '<span class="bungry-blog-pager__label">' . esc_html__( 'Next', 'bungry-burger' ) . '</span>%link', '%title' );
				?>
			</nav>
		</div>
	</article>

	<?php
	$related = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'cat'                 => $category ? $category->term_id : '',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $related->have_posts() ) :
		?>
		<section class="bungry-blog-related" aria-labelledby="bungry-related-title">
			<h2 id="bungry-related-title" class="bungry-blog-related__title"><?php esc_html_e( 'More from the blog', 'bungry-burger' ); ?></h2>
			<div class="bungry-post-grid">
				<?php
				while ( $related->have_posts() ) {
					$related->the_post();
					get_template_part( 'template-parts/post-card' );
				}
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
