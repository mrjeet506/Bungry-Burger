<?php
/**
 * Elementor widget: Bungry Button.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Button extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-button';
	}

	public function get_title(): string {
		return __( 'Bungry Button', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-button';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'button', 'cta', 'order' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bungry-burger' ) ) );
		$this->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Explore the menu', 'bungry-burger' ),
			)
		);
		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://',
				'description' => __( 'The button only renders when a link is set.', 'bungry-burger' ),
			)
		);
		$this->add_control(
			'style',
			array(
				'label'   => __( 'Style', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => array(
					'primary'       => __( 'Primary (orange)', 'bungry-burger' ),
					'dark'          => __( 'Dark (forest)', 'bungry-burger' ),
					'outline-light' => __( 'Outline light', 'bungry-burger' ),
					'outline-dark'  => __( 'Outline dark', 'bungry-burger' ),
				),
			)
		);
		$this->add_control(
			'size',
			array(
				'label'   => __( 'Size', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'bungry-burger' ),
					'small'   => __( 'Small', 'bungry-burger' ),
				),
			)
		);
		$this->add_control(
			'arrow',
			array(
				'label'        => __( 'Show arrow', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$link     = $settings['link'] ?? array();
		$url      = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : '';

		$html = bungry_button(
			array(
				'label'        => (string) $settings['text'],
				'url'          => $url,
				'style'        => (string) $settings['style'],
				'size'         => (string) $settings['size'],
				'arrow'        => 'yes' === ( $settings['arrow'] ?? '' ),
				'target_blank' => ! empty( $link['is_external'] ),
			)
		);

		if ( '' === $html && $this->is_edit_mode() ) {
			echo bungry_placeholder_tag( __( 'Add link', 'bungry-burger' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes.
			return;
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bungry_button escapes internally.
	}

	private function is_edit_mode(): bool {
		return class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
	}
}
