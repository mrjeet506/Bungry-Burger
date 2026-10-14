<?php
/**
 * Menu items: `bungry_menu` custom post type, `bungry_category` taxonomy, meta fields and seed data.
 * No ACF dependency. Menu cards, the home line-up and the allergen table all read from here.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

function bungry_register_menu_cpt(): void {
	register_post_type(
		'bungry_menu',
		array(
			'labels'        => array(
				'name'          => __( 'Menu items', 'bungry-burger' ),
				'singular_name' => __( 'Menu item', 'bungry-burger' ),
				'add_new_item'  => __( 'Add menu item', 'bungry-burger' ),
				'edit_item'     => __( 'Edit menu item', 'bungry-burger' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-carrot',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'rewrite'       => array( 'slug' => 'menu' ),
		)
	);

	register_taxonomy(
		'bungry_category',
		'bungry_menu',
		array(
			'labels'       => array(
				'name'          => __( 'Menu categories', 'bungry-burger' ),
				'singular_name' => __( 'Menu category', 'bungry-burger' ),
			),
			'hierarchical' => false,
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'menu-category' ),
		)
	);
}
add_action( 'init', 'bungry_register_menu_cpt' );

/**
 * @return array<string,array{type:string,default:mixed}>
 */
function bungry_menu_meta_schema(): array {
	return array(
		'_bungry_diet'               => array(
			'type'    => 'string',
			'default' => '',
		),
		'_bungry_price'              => array(
			'type'    => 'string',
			'default' => '',
		),
		'_bungry_price_tbc'          => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'_bungry_allergen_notes'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'_bungry_verification_notes' => array(
			'type'    => 'string',
			'default' => '',
		),
		'_bungry_order_url'          => array(
			'type'    => 'string',
			'default' => '',
		),
		'_bungry_available'          => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'_bungry_illustration'       => array(
			'type'    => 'string',
			'default' => '',
		),
	);
}

function bungry_register_menu_meta(): void {
	foreach ( bungry_menu_meta_schema() as $key => $schema ) {
		register_post_meta(
			'bungry_menu',
			$key,
			array(
				'type'              => $schema['type'],
				'single'            => true,
				'default'           => $schema['default'],
				'show_in_rest'      => true,
				'sanitize_callback' => 'bungry_sanitize_menu_meta_value',
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
}
add_action( 'init', 'bungry_register_menu_meta' );

/**
 * Sanitiser for REST writes. Meta box saves sanitise per field.
 *
 * @param mixed $value Raw value.
 * @return mixed
 */
function bungry_sanitize_menu_meta_value( $value ) {
	return is_bool( $value ) ? $value : sanitize_text_field( (string) $value );
}

/**
 * Price is a plain decimal number in NZD. Anything else is stripped.
 */
function bungry_clean_price( string $raw ): string {
	$raw = trim( $raw );
	if ( '' === $raw ) {
		return '';
	}
	$number = preg_replace( '/[^0-9.]/', '', $raw );
	return (string) $number;
}

function bungry_add_menu_meta_box(): void {
	add_meta_box(
		'bungry-menu-details',
		__( 'Menu item details', 'bungry-burger' ),
		'bungry_render_menu_meta_box',
		'bungry_menu',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'bungry_add_menu_meta_box' );

function bungry_render_menu_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'bungry_menu_save', 'bungry_menu_nonce' );
	$m = array();
	foreach ( array_keys( bungry_menu_meta_schema() ) as $key ) {
		$m[ $key ] = get_post_meta( $post->ID, $key, true );
	}
	$diets         = array(
		''           => __( '— Select —', 'bungry-burger' ),
		'beef'       => __( 'Beef', 'bungry-burger' ),
		'chicken'    => __( 'Chicken', 'bungry-burger' ),
		'vegetarian' => __( 'Vegetarian', 'bungry-burger' ),
	);
	$illustrations = array(
		''                  => __( '— None —', 'bungry-burger' ),
		'burger-classic'    => __( 'Classic', 'bungry-burger' ),
		'burger-double'     => __( 'Double', 'bungry-burger' ),
		'burger-smoky'      => __( 'Smoky', 'bungry-burger' ),
		'burger-free-range' => __( 'Free-range', 'bungry-burger' ),
		'burger-green'      => __( 'Green', 'bungry-burger' ),
	);
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="bungry_diet"><?php esc_html_e( 'Diet category', 'bungry-burger' ); ?></label></th>
			<td>
				<select id="bungry_diet" name="bungry_diet">
					<?php foreach ( $diets as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $m['_bungry_diet'], $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="bungry_price"><?php esc_html_e( 'Price (NZD)', 'bungry-burger' ); ?></label></th>
			<td>
				<input type="text" id="bungry_price" name="bungry_price" class="small-text" inputmode="decimal" value="<?php echo esc_attr( (string) $m['_bungry_price'] ); ?>">
				<label><input type="checkbox" name="bungry_price_tbc" value="1" <?php checked( '' === $m['_bungry_price_tbc'] || (bool) $m['_bungry_price_tbc'] ); ?>> <?php esc_html_e( 'Price to be confirmed (shows placeholder)', 'bungry-burger' ); ?></label>
				<p class="description"><?php esc_html_e( 'Leave empty until the approved price is supplied.', 'bungry-burger' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="bungry_illustration"><?php esc_html_e( 'Illustration', 'bungry-burger' ); ?></label></th>
			<td>
				<select id="bungry_illustration" name="bungry_illustration">
					<?php foreach ( $illustrations as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $m['_bungry_illustration'], $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="bungry_allergen_notes"><?php esc_html_e( 'Allergen notes', 'bungry-burger' ); ?></label></th>
			<td>
				<textarea id="bungry_allergen_notes" name="bungry_allergen_notes" rows="2" class="large-text"><?php echo esc_textarea( (string) $m['_bungry_allergen_notes'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Publish item-level allergen information only after the kitchen verifies it. Empty shows "to be verified".', 'bungry-burger' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="bungry_verification_notes"><?php esc_html_e( 'Verification notes (internal)', 'bungry-burger' ); ?></label></th>
			<td>
				<textarea id="bungry_verification_notes" name="bungry_verification_notes" rows="2" class="large-text"><?php echo esc_textarea( (string) $m['_bungry_verification_notes'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Internal checks for the kitchen. Not shown on the public site.', 'bungry-burger' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="bungry_order_url"><?php esc_html_e( 'Order URL override', 'bungry-burger' ); ?></label></th>
			<td>
				<input type="url" id="bungry_order_url" name="bungry_order_url" class="regular-text" value="<?php echo esc_attr( (string) $m['_bungry_order_url'] ); ?>">
				<p class="description"><?php esc_html_e( 'Optional. Falls back to the site ordering URL. The order action only shows when a URL exists.', 'bungry-burger' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Available', 'bungry-burger' ); ?></th>
			<td>
				<label><input type="checkbox" name="bungry_available" value="1" <?php checked( '' === $m['_bungry_available'] || (bool) $m['_bungry_available'] ); ?>> <?php esc_html_e( 'Show this item on the site', 'bungry-burger' ); ?></label>
			</td>
		</tr>
	</table>
	<?php
}

function bungry_save_menu_meta( int $post_id ): void {
	if ( ! isset( $_POST['bungry_menu_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bungry_menu_nonce'] ) ), 'bungry_menu_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$text = static fn( string $key ): string => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $_POST[ $key ] ) ) : '';
	$area = static fn( string $key ): string => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( (string) $_POST[ $key ] ) ) : '';

	$diet = $text( 'bungry_diet' );
	update_post_meta( $post_id, '_bungry_diet', in_array( $diet, array( 'beef', 'chicken', 'vegetarian' ), true ) ? $diet : '' );

	update_post_meta( $post_id, '_bungry_price', bungry_clean_price( $text( 'bungry_price' ) ) );
	update_post_meta( $post_id, '_bungry_price_tbc', empty( $_POST['bungry_price_tbc'] ) ? '0' : '1' );

	$illustration = $text( 'bungry_illustration' );
	update_post_meta( $post_id, '_bungry_illustration', preg_match( '/^burger-[a-z-]+$/', $illustration ) ? $illustration : '' );

	update_post_meta( $post_id, '_bungry_allergen_notes', $area( 'bungry_allergen_notes' ) );
	update_post_meta( $post_id, '_bungry_verification_notes', $area( 'bungry_verification_notes' ) );
	update_post_meta( $post_id, '_bungry_order_url', isset( $_POST['bungry_order_url'] ) ? esc_url_raw( wp_unslash( (string) $_POST['bungry_order_url'] ) ) : '' );
	update_post_meta( $post_id, '_bungry_available', empty( $_POST['bungry_available'] ) ? '0' : '1' );
}
add_action( 'save_post_bungry_menu', 'bungry_save_menu_meta' );

/**
 * Published menu items, ordered by menu_order then title. Items marked unavailable are excluded.
 * Items with no availability flag yet count as available.
 *
 * @param array<string,mixed> $args Extra WP_Query args (e.g. 'posts_per_page', 'tax_query').
 * @return WP_Post[]
 */
function bungry_get_menu_items( array $args = array() ): array {
	$query = new WP_Query(
		array_merge(
			array(
				'post_type'                  => 'bungry_menu',
				'post_status'                => 'publish',
				'posts_per_page'             => -1,
				'orderby'                    => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'no_found_rows'              => true,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded to one post type, available flag only.
								'meta_query' => array(
									'relation' => 'OR',
									array(
										'key'     => '_bungry_available',
										'value'   => '0',
										'compare' => '!=',
									),
									array(
										'key'     => '_bungry_available',
										'compare' => 'NOT EXISTS',
									),
								),
			),
			$args
		)
	);
	return $query->posts;
}

/**
 * Display helpers for a menu item.
 *
 * @return array{diet:string,diet_label:string,price:string,price_tbc:bool,allergen_notes:string,order_url:string,illustration:string}
 */
function bungry_menu_item_data( int $post_id ): array {
	$diet_labels = array(
		'beef'       => __( 'Beef', 'bungry-burger' ),
		'chicken'    => __( 'Chicken', 'bungry-burger' ),
		'vegetarian' => __( 'Vegetarian', 'bungry-burger' ),
	);
	$diet        = (string) get_post_meta( $post_id, '_bungry_diet', true );
	$order       = (string) get_post_meta( $post_id, '_bungry_order_url', true );
	if ( '' === $order ) {
		$order = bungry_order_url();
	}
	return array(
		'diet'           => $diet,
		'diet_label'     => $diet_labels[ $diet ] ?? '',
		'price'          => (string) get_post_meta( $post_id, '_bungry_price', true ),
		'price_tbc'      => (bool) get_post_meta( $post_id, '_bungry_price_tbc', true ),
		'allergen_notes' => (string) get_post_meta( $post_id, '_bungry_allergen_notes', true ),
		'order_url'      => esc_url( $order ),
		'illustration'   => (string) get_post_meta( $post_id, '_bungry_illustration', true ),
	);
}

/**
 * Renders the price, or a placeholder tag when no approved price exists.
 */
function bungry_price_html( array $data ): string {
	if ( '' !== $data['price'] && ! $data['price_tbc'] ) {
		return '<span class="bungry-price">NZD $' . esc_html( $data['price'] ) . '</span>';
	}
	return bungry_placeholder_tag( __( 'Price TBC', 'bungry-burger' ), 'light' );
}

/**
 * Seed data: the five proposed burgers. Copy follows the Figma menu/home designs and the brief.
 * Prices are intentionally empty. Verification notes are internal and never printed publicly.
 *
 * @return array<int,array<string,string>>
 */
function bungry_menu_seed_items(): array {
	return array(
		array(
			'title'        => 'Classic Bungry',
			'illustration' => 'burger-classic',
			'diet'         => 'beef',
			'excerpt'      => 'The signature classic. Beef, cheese, lettuce, tomato, onion and Bungry sauce.',
			'verification' => 'Confirm beef patty, cheese type, sauce ingredients and final build.',
		),
		array(
			'title'        => 'Double Bungry',
			'illustration' => 'burger-double',
			'diet'         => 'beef',
			'excerpt'      => 'Double the beef, layered with cheese and signature Bungry sauce.',
			'verification' => 'Confirm patty weights, number of cheese slices and final build.',
		),
		array(
			'title'        => 'Smoky Bungry',
			'illustration' => 'burger-smoky',
			'diet'         => 'beef',
			'excerpt'      => 'A bold smoky-style beef burger with cheese, crispy elements and smoky Bungry sauce.',
			'verification' => 'Confirm what "crispy elements" are and whether the sauce contains allergens.',
		),
		array(
			'title'        => 'Free-Range Bungry',
			'illustration' => 'burger-free-range',
			'diet'         => 'chicken',
			'excerpt'      => 'Crispy chicken with lettuce, cheese and signature Bungry sauce.',
			'verification' => 'Do not publish the "free-range" claim until sourcing evidence and the final product specification are approved.',
		),
		array(
			'title'        => 'Green Bungry',
			'illustration' => 'burger-green',
			'diet'         => 'vegetarian',
			'excerpt'      => 'A vegetarian option with a plant-based patty, fresh vegetables, cheese and signature Bungry sauce.',
			'verification' => 'Confirm the patty is fully plant-based, cheese and sauce are vegetarian, and cross-contact risk. Do not call it vegan without full confirmation.',
		),
	);
}

/**
 * Creates the five menu items once. Re-running is safe: existing titles are skipped.
 */
function bungry_seed_menu_items(): int {
	$created = 0;
	foreach ( bungry_menu_seed_items() as $index => $item ) {
		$existing = bungry_find_post_by_title( $item['title'], 'bungry_menu' );
		if ( $existing ) {
			continue;
		}
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'bungry_menu',
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_excerpt' => $item['excerpt'],
				'menu_order'   => $index + 1,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_bungry_diet', $item['diet'] );
		update_post_meta( $post_id, '_bungry_price', '' );
		update_post_meta( $post_id, '_bungry_price_tbc', '1' );
		update_post_meta( $post_id, '_bungry_illustration', $item['illustration'] );
		update_post_meta( $post_id, '_bungry_verification_notes', $item['verification'] );
		update_post_meta( $post_id, '_bungry_available', '1' );
		++$created;
	}
	update_option( 'bungry_menu_seeded', BUNGRY_VERSION );
	return $created;
}
