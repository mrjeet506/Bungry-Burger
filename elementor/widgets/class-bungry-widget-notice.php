<?php
/**
 * Elementor widget: Notice (info / warning / closed).
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

class Bungry_Widget_Notice extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'bungry-notice';
	}

	public function get_title(): string {
		return __( 'Bungry Notice', 'bungry-burger' );
	}

	public function get_icon(): string {
		return 'eicon-info-circle-o';
	}

	public function get_categories(): array {
		return array( 'bungry' );
	}

	public function get_keywords(): array {
		return array( 'bungry', 'notice', 'alert', 'closed', 'info' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Content', 'bungry-burger' ) ) );
		$this->add_control(
			'type',
			array(
				'label'   => __( 'Type', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'info',
				'options' => array(
					'info'    => __( 'Info', 'bungry-burger' ),
					'warning' => __( 'Warning', 'bungry-burger' ),
					'closed'  => __( 'Closed', 'bungry-burger' ),
				),
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'bungry-burger' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
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
		$this->end_controls_section();
	}

	protected function render(): void {
		$s    = $this->get_settings_for_display();
		$type = in_array( $s['type'], array( 'info', 'warning', 'closed' ), true ) ? $s['type'] : 'info';
		if ( '' === trim( (string) $s['title'] . (string) $s['text'] ) ) {
			return;
		}
		$role = 'closed' === $type ? 'status' : 'note';
		?>
		<div class="bungry-notice bungry-notice--<?php echo esc_attr( $type ); ?>" role="<?php echo esc_attr( $role ); ?>">
			<?php if ( '' !== $s['title'] ) : ?>
				<p class="bungry-notice__title"><?php echo esc_html( $s['title'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $s['text'] ) : ?>
				<p class="bungry-notice__text"><?php echo nl2br( esc_html( $s['text'] ) ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
