<?php
/**
 * Elementor Kit (Site Settings) setup: global colours, typography, container width and breakpoints.
 * Applied once when the theme is activated (and on demand from the Bungry Settings page).
 * Editors can retheme from Elementor > Site Settings afterwards; this only sets the starting values.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brand tokens. Values come from the Figma style guide ("Brand Colors" collection).
 * Global colour ids are letters only, so widgets can reference var(--e-global-color-{id}).
 *
 * @return array<string,array{title:string,color:string}>
 */
function bungry_kit_colors(): array {
	return array(
		'bungryforest'     => array(
			'title' => 'Forest (primary)',
			'color' => '#185B28',
		),
		'bungryforestdark' => array(
			'title' => 'Forest dark',
			'color' => '#10431D',
		),
		'bungrycream'      => array(
			'title' => 'Cream (page background)',
			'color' => '#F6F7EE',
		),
		'bungrycreamdark'  => array(
			'title' => 'Cream dark (borders)',
			'color' => '#E9EBD7',
		),
		'bungryorange'     => array(
			'title' => 'Orange (CTA accent)',
			'color' => '#F6A531',
		),
		'bungryorangedark' => array(
			'title' => 'Orange dark (text on cream)',
			'color' => '#E08F12',
		),
		'bungryolive'      => array(
			'title' => 'Olive',
			'color' => '#98A75B',
		),
		'bungrydeepolive'  => array(
			'title' => 'Deep olive (details)',
			'color' => '#6D7A38',
		),
		'bungryleaf'       => array(
			'title' => 'Leaf',
			'color' => '#5E8441',
		),
		'bungryink'        => array(
			'title' => 'Ink (body text)',
			'color' => '#0E3A1A',
		),
		'bungrywhite'      => array(
			'title' => 'White',
			'color' => '#FFFFFF',
		),
	);
}

/**
 * Typography roles. Sizes are desktop values with mobile overrides from the Figma text styles.
 * Mobile sizes marked "derived" were not in the variable export and should be checked against the 390 frames.
 *
 * @return array<string,array<string,mixed>>
 */
function bungry_kit_typography(): array {
	return array(
		'bungryh1'      => array(
			'title'  => 'H1',
			'family' => 'Bakbak One',
			'size'   => 80,
			'size_m' => 44,
			'lh'     => 84,
			'lh_m'   => 48,
			'ls'     => -1,
			'weight' => '400',
			'upper'  => true,
		),
		'bungryh2'      => array(
			'title'  => 'H2',
			'family' => 'Bakbak One',
			'size'   => 56,
			'size_m' => 36,
			'lh'     => 60,
			'lh_m'   => 40,
			'ls'     => -0.5,
			'weight' => '400',
			'upper'  => true,
		),
		'bungryh3'      => array(
			'title'  => 'H3',
			'family' => 'Bakbak One',
			'size'   => 40,
			'size_m' => 30,
			'lh'     => 44,
			'lh_m'   => 34,
			'ls'     => 0,
			'weight' => '400',
			'upper'  => true,
		),
		'bungryh4'      => array(
			'title'  => 'H4',
			'family' => 'Bakbak One',
			'size'   => 28,
			'size_m' => 24,
			'lh'     => 32,
			'lh_m'   => 28,
			'ls'     => 0,
			'weight' => '400',
			'upper'  => true,
		),
		'bungryh5'      => array(
			'title'  => 'H5',
			'family' => 'Bakbak One',
			'size'   => 22,
			'size_m' => 20,
			'lh'     => 26,
			'lh_m'   => 24,
			'ls'     => 0.5,
			'weight' => '400',
			'upper'  => true,
		),
		'bungrybodyl'   => array(
			'title'  => 'Body L',
			'family' => 'Montserrat',
			'size'   => 20,
			'size_m' => 18,
			'lh'     => 32,
			'lh_m'   => 28,
			'ls'     => 0,
			'weight' => '500',
			'upper'  => false,
		),
		'bungrybody'    => array(
			'title'  => 'Body',
			'family' => 'Montserrat',
			'size'   => 16,
			'size_m' => 16,
			'lh'     => 26,
			'lh_m'   => 26,
			'ls'     => 0,
			'weight' => '500',
			'upper'  => false,
		),
		'bungrysmall'   => array(
			'title'  => 'Small',
			'family' => 'Montserrat',
			'size'   => 14,
			'size_m' => 14,
			'lh'     => 22,
			'lh_m'   => 22,
			'ls'     => 0,
			'weight' => '500',
			'upper'  => false,
		),
		'bungrybutton'  => array(
			'title'  => 'Button',
			'family' => 'Montserrat',
			'size'   => 15,
			'size_m' => 15,
			'lh'     => 20,
			'lh_m'   => 20,
			'ls'     => 3,
			'weight' => '700',
			'upper'  => true,
		),
		'bungryeyebrow' => array(
			'title'  => 'Eyebrow',
			'family' => 'Montserrat',
			'size'   => 14,
			'size_m' => 13,
			'lh'     => 20,
			'lh_m'   => 20,
			'ls'     => 10,
			'weight' => '700',
			'upper'  => true,
		),
		'bungrynav'     => array(
			'title'  => 'Nav',
			'family' => 'Montserrat',
			'size'   => 15,
			'size_m' => 15,
			'lh'     => 20,
			'lh_m'   => 20,
			'ls'     => 0.3,
			'weight' => '600',
			'upper'  => true,
		),
		'bungrylabel'   => array(
			'title'  => 'Label',
			'family' => 'Montserrat',
			'size'   => 12,
			'size_m' => 12,
			'lh'     => 16,
			'lh_m'   => 16,
			'ls'     => 8,
			'weight' => '700',
			'upper'  => true,
		),
	);
}

/**
 * @return array<string,mixed> Elementor page settings for the kit post.
 */
function bungry_kit_page_settings(): array {
	$system_colors = array(
		array(
			'_id'   => 'primary',
			'title' => 'Primary',
			'color' => '#185B28',
		),
		array(
			'_id'   => 'secondary',
			'title' => 'Secondary',
			'color' => '#F6A531',
		),
		array(
			'_id'   => 'text',
			'title' => 'Text',
			'color' => '#0E3A1A',
		),
		array(
			'_id'   => 'accent',
			'title' => 'Accent',
			'color' => '#E08F12',
		),
	);

	$custom_colors = array();
	foreach ( bungry_kit_colors() as $id => $c ) {
		$custom_colors[] = array(
			'_id'   => $id,
			'title' => $c['title'],
			'color' => $c['color'],
		);
	}

	$system_typography = array(
		array(
			'_id'                    => 'primary',
			'title'                  => 'Primary',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Bakbak One',
		),
		array(
			'_id'                    => 'secondary',
			'title'                  => 'Secondary',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Montserrat',
		),
		array(
			'_id'                    => 'text',
			'title'                  => 'Text',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Montserrat',
		),
		array(
			'_id'                    => 'accent',
			'title'                  => 'Accent',
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Montserrat',
		),
	);

	$custom_typography = array();
	foreach ( bungry_kit_typography() as $id => $t ) {
		$custom_typography[] = array(
			'_id'                           => $id,
			'title'                         => $t['title'],
			'typography_typography'         => 'custom',
			'typography_font_family'        => $t['family'],
			'typography_font_weight'        => $t['weight'],
			'typography_font_size'          => array(
				'unit'  => 'px',
				'size'  => $t['size'],
				'sizes' => array(),
			),
			'typography_font_size_mobile'   => array(
				'unit'  => 'px',
				'size'  => $t['size_m'],
				'sizes' => array(),
			),
			'typography_line_height'        => array(
				'unit'  => 'px',
				'size'  => $t['lh'],
				'sizes' => array(),
			),
			'typography_line_height_mobile' => array(
				'unit'  => 'px',
				'size'  => $t['lh_m'],
				'sizes' => array(),
			),
			'typography_letter_spacing'     => array(
				'unit'  => 'px',
				'size'  => $t['ls'],
				'sizes' => array(),
			),
			'typography_text_transform'     => $t['upper'] ? 'uppercase' : 'none',
		);
	}

	return array(
		'system_colors'              => $system_colors,
		'custom_colors'              => $custom_colors,
		'system_typography'          => $system_typography,
		'custom_typography'          => $custom_typography,
		'container_width'            => array(
			'unit'  => 'px',
			'size'  => 1280,
			'sizes' => array(),
		),
		'viewport_md'                => 768,
		'viewport_lg'                => 1025,
		'body_background_background' => 'classic',
		'body_background_color'      => '#F6F7EE',
		'body_color'                 => '#0E3A1A',
	);
}

/**
 * Creates the Kit post if missing, then writes the Bungry settings to it.
 *
 * @return bool True when the kit was written.
 */
function bungry_apply_elementor_kit(): bool {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id || 'publish' !== get_post_status( $kit_id ) ) {
		$kit_id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_title'  => 'Bungry Burger Kit',
				'post_status' => 'publish',
			),
			true
		);
		if ( is_wp_error( $kit_id ) ) {
			return false;
		}
		update_post_meta( $kit_id, '_elementor_template_type', 'kit' );
		update_post_meta( $kit_id, '_elementor_data', '[]' );
		update_option( 'elementor_active_kit', $kit_id );
	}

	$existing = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$existing = is_array( $existing ) ? $existing : array();
	update_post_meta( $kit_id, '_elementor_page_settings', array_merge( $existing, bungry_kit_page_settings() ) );

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	update_option( 'bungry_kit_applied', BUNGRY_VERSION );
	return true;
}

/**
 * Applies the kit on activation, and once more after Elementor is loaded (if it was not active yet).
 */
function bungry_on_theme_switch(): void {
	bungry_apply_elementor_kit();
	bungry_seed_menu_items();
}
add_action( 'after_switch_theme', 'bungry_on_theme_switch' );

function bungry_maybe_apply_kit_late(): void {
	if ( get_option( 'bungry_kit_applied' ) !== BUNGRY_VERSION && did_action( 'elementor/loaded' ) ) {
		bungry_apply_elementor_kit();
	}
}
add_action( 'init', 'bungry_maybe_apply_kit_late', 20 );
