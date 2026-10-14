<?php
/**
 * "Bungry Settings" admin page (Appearance > Bungry Settings).
 * Every business fact defaults to empty. Empty values hide the related UI or show a placeholder tag.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_add_settings_page(): void {
	add_theme_page(
		__( 'Bungry Settings', 'bungry-burger' ),
		__( 'Bungry Settings', 'bungry-burger' ),
		'manage_options',
		'bungry-settings',
		'bungry_render_settings_page'
	);
}
add_action( 'admin_menu', 'bungry_add_settings_page' );

/**
 * @return array<string,array{0:string,1:string,2:string,3:string}> key => [section, label, type, help]
 */
function bungry_settings_fields(): array {
	return array(
		'logo_dark_url'        => array( 'brand', __( 'Logo (dark, for cream backgrounds)', 'bungry-burger' ), 'url', __( 'Full SVG logo URL. Leave empty to use the text wordmark.', 'bungry-burger' ) ),
		'logo_light_url'       => array( 'brand', __( 'Logo (light, for forest backgrounds)', 'bungry-burger' ), 'url', __( 'Full SVG logo URL for the footer.', 'bungry-burger' ) ),
		'announcement_enabled' => array( 'announcement', __( 'Show announcement strip', 'bungry-burger' ), 'checkbox', __( 'Only show a real update, e.g. "Now serving in [confirmed suburb]".', 'bungry-burger' ) ),
		'announcement_text'    => array( 'announcement', __( 'Announcement text', 'bungry-burger' ), 'text', '' ),
		'order_label'          => array( 'ordering', __( 'Order button label', 'bungry-burger' ), 'text', __( 'The header button only appears when an ordering URL is set.', 'bungry-burger' ) ),
		'order_url'            => array( 'ordering', __( 'Ordering URL', 'bungry-burger' ), 'url', __( 'Official ordering link (provider). Leave empty until confirmed.', 'bungry-burger' ) ),
		'address'              => array( 'business', __( 'Street address / suburb', 'bungry-burger' ), 'text', '' ),
		'phone'                => array( 'business', __( 'Phone', 'bungry-burger' ), 'text', '' ),
		'email'                => array( 'business', __( 'Email', 'bungry-burger' ), 'email', __( 'Use a monitored business address.', 'bungry-burger' ) ),
		'hours'                => array( 'business', __( 'Opening hours', 'bungry-burger' ), 'textarea', '' ),
		'social_instagram'     => array( 'social', __( 'Instagram URL', 'bungry-burger' ), 'url', '' ),
		'social_facebook'      => array( 'social', __( 'Facebook URL', 'bungry-burger' ), 'url', '' ),
		'social_tiktok'        => array( 'social', __( 'TikTok URL', 'bungry-burger' ), 'url', '' ),
		'social_youtube'       => array( 'social', __( 'YouTube URL', 'bungry-burger' ), 'url', '' ),
		'footer_blurb'         => array( 'footer', __( 'Footer blurb', 'bungry-burger' ), 'textarea', '' ),
		'copyright_text'       => array( 'footer', __( 'Copyright line', 'bungry-burger' ), 'text', __( 'Leave empty for "© {year} Bungry Burger. All rights reserved."', 'bungry-burger' ) ),
		'show_tags'            => array( 'compliance', __( 'Show "client to confirm" tags', 'bungry-burger' ), 'checkbox', __( 'Turn off once every placeholder is confirmed and removed. Empty fields then hide instead of showing a tag.', 'bungry-burger' ) ),
		'analytics_notice'     => array( 'compliance', __( 'Cookie / analytics notice text', 'bungry-burger' ), 'textarea', __( 'Placeholder until the actual analytics and cookie setup is confirmed.', 'bungry-burger' ) ),
	);
}

function bungry_register_settings(): void {
	register_setting(
		'bungry_settings_group',
		'bungry_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bungry_sanitize_settings',
			'default'           => bungry_default_settings(),
		)
	);

	$sections = array(
		'brand'        => __( 'Brand', 'bungry-burger' ),
		'announcement' => __( 'Announcement strip', 'bungry-burger' ),
		'ordering'     => __( 'Online ordering', 'bungry-burger' ),
		'business'     => __( 'Business details', 'bungry-burger' ),
		'social'       => __( 'Social links', 'bungry-burger' ),
		'footer'       => __( 'Footer', 'bungry-burger' ),
		'compliance'   => __( 'Launch & compliance', 'bungry-burger' ),
	);
	foreach ( $sections as $id => $title ) {
		add_settings_section( 'bungry_' . $id, $title, '__return_false', 'bungry-settings' );
	}

	foreach ( bungry_settings_fields() as $key => $field ) {
		add_settings_field(
			'bungry_' . $key,
			$field[1],
			'bungry_render_field',
			'bungry-settings',
			'bungry_' . $field[0],
			array(
				'key'  => $key,
				'type' => $field[2],
				'help' => $field[3],
			)
		);
	}
}
add_action( 'admin_init', 'bungry_register_settings' );

/**
 * @param array{key:string,type:string,help:string} $args
 */
function bungry_render_field( array $args ): void {
	$settings = bungry_get_settings();
	$key      = $args['key'];
	$value    = $settings[ $key ] ?? '';
	$id       = 'bungry_' . $key;
	$name     = 'bungry_settings[' . $key . ']';

	switch ( $args['type'] ) {
		case 'checkbox':
			printf(
				'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (int) $value, 1, false ),
				esc_html__( 'Enabled', 'bungry-burger' )
			);
			break;
		case 'textarea':
			printf(
				'<textarea class="large-text" rows="3" id="%1$s" name="%2$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( (string) $value )
			);
			break;
		default:
			$input_type = in_array( $args['type'], array( 'url', 'email' ), true ) ? $args['type'] : 'text';
			printf(
				'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s">',
				esc_attr( $input_type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
	}

	if ( '' !== $args['help'] ) {
		printf( '<p class="description">%s</p>', esc_html( $args['help'] ) );
	}
}

/**
 * @param mixed $input Raw posted values.
 * @return array<string,mixed>
 */
function bungry_sanitize_settings( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$clean = array();

	foreach ( bungry_settings_fields() as $key => $field ) {
		$raw = $input[ $key ] ?? '';
		switch ( $field[2] ) {
			case 'checkbox':
				$clean[ $key ] = empty( $raw ) ? 0 : 1;
				break;
			case 'url':
				$clean[ $key ] = esc_url_raw( (string) $raw );
				break;
			case 'email':
				$clean[ $key ] = sanitize_email( (string) $raw );
				break;
			case 'textarea':
				$clean[ $key ] = sanitize_textarea_field( (string) $raw );
				break;
			default:
				$clean[ $key ] = sanitize_text_field( (string) $raw );
		}
	}

	return $clean;
}

function bungry_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bungry Settings', 'bungry-burger' ); ?></h1>
		<p><?php esc_html_e( 'Empty fields are never filled with sample data. While "Show client to confirm tags" is on, unconfirmed details display a placeholder tag; turn it off at launch.', 'bungry-burger' ); ?></p>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'bungry_settings_group' );
			do_settings_sections( 'bungry-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
