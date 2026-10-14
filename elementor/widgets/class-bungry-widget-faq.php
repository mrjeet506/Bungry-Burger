<?php
/**
 * Elementor widget: FAQ accordion. Native <details>/<summary> (keyboard and screen-reader accessible).
 * Optional FAQPage JSON-LD uses only the items the editor typed, so it never invents facts.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_FAQ extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-faq';
	}

	public function get_title(): string {
		return __( 'Bungry FAQ', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-accordion';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'faq', 'accordion', 'questions' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Questions', 'bungry-burger' ) ) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'question',
			array(
				'label'   => __( 'Question', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$repeater->add_control(
			'answer',
			array(
				'label'   => __( 'Answer', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
			)
		);
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'bungry-burger' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(),
				'title_field' => '{{{ question }}}',
			)
		);
		$this->add_control(
			'open_first',
			array(
				'label'        => __( 'Open first item', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'schema',
			array(
				'label'        => __( 'Output FAQPage structured data', 'bungry-burger' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Only enable once the answers are confirmed and match the visible text.', 'bungry-burger' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$items = array_values(
			array_filter(
				(array) $s['items'],
				static fn( $item ) => '' !== trim( (string) ( $item['question'] ?? '' ) )
			)
		);
		if ( empty( $items ) ) {
			return;
		}

		echo '<div class="bungry-faq">';
		foreach ( $items as $index => $item ) {
			$open = ( 0 === $index && 'yes' === ( $s['open_first'] ?? '' ) ) ? ' open' : '';
			printf(
				'<details class="bungry-faq__item"%1$s><summary class="bungry-faq__question">%2$s</summary><div class="bungry-faq__answer">%3$s</div></details>',
				esc_attr( $open ),
				esc_html( (string) $item['question'] ),
				wp_kses_post( (string) ( $item['answer'] ?? '' ) )
			);
		}
		echo '</div>';

		if ( 'yes' === ( $s['schema'] ?? '' ) ) {
			$entities = array();
			foreach ( $items as $item ) {
				$entities[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( (string) $item['question'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( (string) ( $item['answer'] ?? '' ) ),
					),
				);
			}
			$json = wp_json_encode(
				array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $entities,
				),
				JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
			);
			printf( '<script type="application/ld+json">%s</script>', (string) $json ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with HEX_TAG.
		}
	}
}
