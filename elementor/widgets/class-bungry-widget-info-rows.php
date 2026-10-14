<?php
/**
 * Elementor widget: Info Rows (address, hours, phone, email). Defaults to Bungry Settings.
 * Unconfirmed values show a placeholder tag while the global toggle is on, otherwise the row is hidden.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Info_Rows extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-info-rows';
	}

	public function get_title(): string {
		return __( 'Bungry Info Rows', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-info-box';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'address', 'hours', 'phone', 'email', 'store' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Source', 'bungry-burger' ) ) );
		$this->add_control(
			'use_settings',
			array(
				'label'        => __( 'Use Bungry Settings details', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Off = use the custom rows below. Edit business details under Appearance > Bungry Settings.', 'bungry-burger' ),
			)
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$repeater->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);
		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Custom rows', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'use_settings' => '' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		if ( 'yes' === ( $s['use_settings'] ?? '' ) ) {
			$rows = array(
				array(
					'icon'  => 'pin',
					'label' => __( 'Address', 'bungry-burger' ),
					'value' => (string) bungry_setting( 'address' ),
					'type'  => 'text',
				),
				array(
					'icon'  => 'clock',
					'label' => __( 'Opening hours', 'bungry-burger' ),
					'value' => (string) bungry_setting( 'hours' ),
					'type'  => 'text',
				),
				array(
					'icon'  => 'phone',
					'label' => __( 'Phone', 'bungry-burger' ),
					'value' => (string) bungry_setting( 'phone' ),
					'type'  => 'phone',
				),
				array(
					'icon'  => 'mail',
					'label' => __( 'Email', 'bungry-burger' ),
					'value' => (string) bungry_setting( 'email' ),
					'type'  => 'email',
				),
			);
		} else {
			$rows = array();
			foreach ( (array) $s['rows'] as $row ) {
				$rows[] = array(
					'icon'  => 'pin',
					'label' => (string) $row['label'],
					'value' => (string) $row['value'],
					'type'  => 'text',
				);
			}
		}

		$html = '';
		foreach ( $rows as $row ) {
			$value = trim( $row['value'] );
			if ( '' === $value && ! bungry_tags_enabled() ) {
				continue; // Hidden when unconfirmed and tags are off.
			}
			$value_html = '' === $value ? bungry_placeholder_tag() : bungry_contact_value( $value, $row['type'] );
			$icon       = $row['icon'] ? bungry_art( 'icons/' . $row['icon'], 'bungry-info-row__icon' ) : '';
			$html      .= sprintf(
				'<li class="bungry-info-row"><span class="bungry-info-row__icon-wrap">%1$s</span><div><p class="bungry-info-row__label">%2$s</p><div class="bungry-info-row__value">%3$s</div></div></li>',
				$icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG.
				esc_html( $row['label'] ),
				$value_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helpers.
			);
		}

		if ( '' !== $html ) {
			echo '<ul class="bungry-info-rows">' . $html . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
		}
	}
}
