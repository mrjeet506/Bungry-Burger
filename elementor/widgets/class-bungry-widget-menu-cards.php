<?php
/**
 * Elementor widget: Menu Cards. Reads the bungry_menu CPT. Supports category filter and responsive columns.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Menu_Cards extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-menu-cards';
	}

	public function get_title(): string {
		return __( 'Bungry Menu Cards', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'menu', 'burger', 'cards', 'products' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'query', array( 'label' => __( 'Query', 'bungry-burger' ) ) );
		$this->add_control(
			'category',
			array(
				'label'       => __( 'Category slug (blank = all)', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Matches the bungry_category taxonomy, e.g. beef, chicken, vegetarian.', 'bungry-burger' ),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Number of items (-1 = all)', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => -1,
				'min'     => -1,
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'display', array( 'label' => __( 'Display', 'bungry-burger' ) ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'bungry-burger' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '5',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'selectors'      => array( '{{WRAPPER}} .bungry-menu-cards' => '--bungry-cols: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'show_price',
			array(
				'label'        => __( 'Show price', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_order',
			array(
				'label'        => __( 'Show order button (only when ordering URL set)', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'allergen_link',
			array(
				'label'       => __( 'Allergen information link', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => '/food-allergens-faqs/',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$args = array(
			'posts_per_page' => (int) $s['limit'],
		);
		if ( '' !== trim( (string) $s['category'] ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- single taxonomy, bounded to the menu CPT.
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'bungry_category',
								'field'    => 'slug',
								'terms'    => sanitize_title( (string) $s['category'] ),
							),
						);
		}
		$items = bungry_get_menu_items( $args );
		if ( empty( $items ) ) {
			return;
		}

		$allergen = is_array( $s['allergen_link'] ?? null ) ? (string) ( $s['allergen_link']['url'] ?? '' ) : '';
		?>
		<div class="bungry-menu-cards">
			<?php
			foreach ( $items as $post ) :
				$data  = bungry_menu_item_data( $post->ID );
				$art   = $data['illustration'] ? bungry_art( 'illustrations/' . $data['illustration'], 'bungry-menu-card__art' ) : '';
				$media = has_post_thumbnail( $post ) ? get_the_post_thumbnail(
					$post,
					'large',
					array(
						'loading' => 'lazy',
						'alt'     => '',
					)
				) : '';
				?>
				<article class="bungry-menu-card">
					<div class="bungry-menu-card__media">
						<?php echo $media ? $media : $art; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes the image; bundled SVG. ?>
					</div>
					<h3 class="bungry-menu-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
					<?php if ( $data['diet_label'] ) : ?>
						<p class="bungry-menu-card__diet"><?php echo esc_html( $data['diet_label'] ); ?></p>
					<?php endif; ?>
					<p class="bungry-menu-card__text"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
					<div class="bungry-menu-card__meta">
						<?php if ( 'yes' === ( $s['show_price'] ?? '' ) ) : ?>
							<?php echo bungry_price_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
						<?php endif; ?>
						<?php if ( '' !== $allergen ) : ?>
							<a class="bungry-menu-card__allergen" href="<?php echo esc_url( $allergen ); ?>"><?php esc_html_e( 'Allergen info', 'bungry-burger' ); ?></a>
						<?php else : ?>
							<?php echo bungry_placeholder_tag( __( 'Allergens to be verified', 'bungry-burger' ), 'light' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes. ?>
						<?php endif; ?>
					</div>
					<?php if ( 'yes' === ( $s['show_order'] ?? '' ) && '' !== $data['order_url'] ) : ?>
						<?php
						echo bungry_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapes internally.
							array(
								'label' => __( 'Order', 'bungry-burger' ),
								'url'   => $data['order_url'],
								'style' => 'dark',
								'size'  => 'small',
								'arrow' => true,
							)
						);
						?>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
