<?php
/**
 * Elementor widget: CTA band (orange card with up to two buttons).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_CTA_Band extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-cta-band';
	}

	public function get_title(): string {
		return __( 'Bungry CTA Band', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-call-to-action';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'cta', 'band', 'banner' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bungry-burger' ) ) );
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'YOUR NEXT BUNGRY IS CLOSER THAN YOU THINK.',
			)
		);
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);
		$this->add_control(
			'button1_text',
			array(
				'label'   => __( 'Button 1 text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Find us',
			)
		);
		$this->add_control(
			'button1_link',
			array(
				'label'       => __( 'Button 1 link', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => '/find-us/',
			)
		);
		$this->add_control(
			'button2_text',
			array(
				'label'   => __( 'Button 2 text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Order online',
			)
		);
		$this->add_control(
			'button2_link',
			array(
				'label'       => __( 'Button 2 link', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$s   = $this->get_settings_for_display();
		$one = is_array( $s['button1_link'] ?? null ) ? $s['button1_link'] : array();
		$two = is_array( $s['button2_link'] ?? null ) ? $s['button2_link'] : array();
		?>
		<section class="bungry-cta-band">
			<div class="bungry-cta-band__copy">
				<h2 class="bungry-cta-band__title"><?php echo esc_html( $s['title'] ); ?></h2>
				<?php if ( '' !== $s['text'] ) : ?>
					<p class="bungry-cta-band__text"><?php echo esc_html( $s['text'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="bungry-cta-band__actions">
				<?php
				echo wp_kses_post(
					bungry_button(
						array(
							'label' => (string) $s['button1_text'],
							'url'   => (string) ( $one['url'] ?? '' ),
							'style' => 'dark',
							'arrow' => true,
						)
					)
				); // Output is escaped by bungry_button().
				echo wp_kses_post(
					bungry_button(
						array(
							'label' => (string) $s['button2_text'],
							'url'   => (string) ( $two['url'] ?? '' ),
							'style' => 'outline-dark',
							'arrow' => true,
						)
					)
				); // Output is escaped by bungry_button().
				?>
			</div>
		</section>
		<?php
	}
}
