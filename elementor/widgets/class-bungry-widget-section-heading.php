<?php
/**
 * Elementor widget: Section Heading (eyebrow, title, text, optional button).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Section_Heading extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-section-heading';
	}

	public function get_title(): string {
		return __( 'Bungry Section Heading', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-heading';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'heading', 'eyebrow', 'title' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bungry-burger' ) ) );
		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'THE BUNGRY LINE-UP',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'MEET THE BUNGRY LINE-UP.',
				'rows'    => 2,
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'   => __( 'Title HTML tag', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'Five burger concepts. Find the one that fits your craving.',
			)
		);
		$this->add_control(
			'align',
			array(
				'label'                => __( 'Alignment', 'bungry-burger' ),
				'type'                 => \Elementor\Controls_Manager::CHOOSE,
				'default'              => 'left',
				'options'              => array(
					'left'   => array(
						'title' => __( 'Left', 'bungry-burger' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'bungry-burger' ),
						'icon'  => 'eicon-text-align-center',
					),
				),
				'selectors_dictionary' => array(
					'left'   => 'text-align: left; align-items: flex-start;',
					'center' => 'text-align: center; align-items: center;',
				),
				'selectors'            => array( '{{WRAPPER}} .bungry-section-heading' => '{{VALUE}}' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'cta', array( 'label' => __( 'Button (optional)', 'bungry-burger' ) ) );
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'View the full menu',
			)
		);
		$this->add_control(
			'button_link',
			array(
				'label'       => __( 'Button link', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$tag   = in_array( $s['title_tag'], array( 'h1', 'h2', 'h3', 'h4' ), true ) ? $s['title_tag'] : 'h2';
		$link  = is_array( $s['button_link'] ?? null ) ? $s['button_link'] : array();
		$align = 'center' === ( $s['align'] ?? '' ) ? 'is-center' : '';
		?>
		<div class="bungry-section-heading <?php echo esc_attr( $align ); ?>">
			<?php if ( '' !== $s['eyebrow'] ) : ?>
				<p class="bungry-eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
			<?php endif; ?>
			<<?php echo esc_html( $tag ); ?> class="bungry-section-heading__title"><?php echo nl2br( esc_html( $s['title'] ) ); ?></<?php echo esc_html( $tag ); ?>>
			<?php if ( '' !== $s['text'] ) : ?>
				<p class="bungry-section-heading__text"><?php echo esc_html( $s['text'] ); ?></p>
			<?php endif; ?>
			<?php
			echo bungry_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapes internally.
				array(
					'label'        => (string) $s['button_text'],
					'url'          => (string) ( $link['url'] ?? '' ),
					'style'        => 'outline-dark',
					'arrow'        => true,
					'target_blank' => ! empty( $link['is_external'] ),
				)
			);
			?>
		</div>
		<?php
	}
}
