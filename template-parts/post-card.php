<?php
/**
 * Blog card used by the blog grid, archives and related posts.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

$categories = get_the_category();
$category   = ! empty( $categories ) ? $categories[0] : null;
?>
<article <?php post_class( 'bungry-post-card' ); ?>>
	<a class="bungry-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php
			the_post_thumbnail(
				'bungry-card',
				array(
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		<?php else : ?>
			<span class="bungry-post-card__placeholder">
				<?php echo bungry_art( 'illustrations/burger-classic', 'bungry-post-card__art' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
			</span>
		<?php endif; ?>
	</a>
	<div class="bungry-post-card__body">
		<p class="bungry-post-card__meta">
			<?php if ( $category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<span aria-hidden="true">&middot;</span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<h2 class="bungry-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="bungry-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
		<a class="bungry-post-card__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'bungry-burger' ); ?> <span aria-hidden="true">&rarr;</span>
			<span class="screen-reader-text"><?php the_title(); ?></span>
		</a>
	</div>
</article>
