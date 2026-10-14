<?php
/**
 * Elementor widget: Placeholder Tag ("Client to confirm"). Hidden site-wide when the global toggle is off.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Placeholder_Tag extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-placeholder-tag';
	}

	public function get_title(): string {
		return __( 'Bungry Placeholder Tag', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-tags';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'placeholder', 'confirm', 'tbc' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bungry-burger' ) ) );
		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Client to confirm', 'bungry-burger' ),
			)
		);
		$this->add_control(
			'tone',
			array(
				'label'   => __( 'Tone', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => array(
					'dark'  => __( 'Dark (on forest)', 'bungry-burger' ),
					'light' => __( 'Light (on cream)', 'bungry-burger' ),
				),
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		echo bungry_placeholder_tag( (string) $settings['label'], (string) $settings['tone'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes.
	}
}
