<?php
/**
 * "Import Bungry demo pages & menu" tool (Appearance > Bungry Demo).
 * Creates the seven pages (Privacy and Terms as drafts), sets Home as the front page,
 * builds the menus and seeds the five burgers. Elementor page JSON is not imported yet.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,array{title:string,status:string,description:string,body:string}>
 */
function bungry_demo_pages(): array {
	return array(
		'home'      => array(
			'title'       => 'Home',
			'status'      => 'publish',
			'description' => 'Bungry Burger | Fresh. Sealed. Different. Meet Bungry Burger, a distinctive take on a familiar favourite. Explore our signature sealed-burger concept, view the menu and find us in New Zealand.',
			'body'        => '<h1>FRESH. SEALED. DIFFERENT.</h1><p>A new take on your burger cravings.</p>',
		),
		'menu'      => array(
			'title'       => 'Menu',
			'status'      => 'publish',
			'description' => 'Explore the Bungry Burger line-up, including Classic Bungry, Double Bungry, Smoky Bungry, Free-Range Bungry and Green Bungry. Final items and availability may vary.',
			'body'        => '<h1>THE BUNGRY LINE-UP</h1><p>Five burger concepts. One signature sealed-burger idea.</p>',
		),
		'our-story' => array(
			'title'       => 'Our Story',
			'status'      => 'publish',
			'description' => 'Discover the idea behind Bungry Burger and the distinctive sealed-burger concept at the heart of our brand.',
			'body'        => '<h1>A FAMILIAR FAVOURITE, WITH A BUNGRY TWIST.</h1>',
		),
		'find-us'   => array(
			'title'       => 'Find Us',
			'status'      => 'publish',
			'description' => 'Find Bungry Burger in New Zealand. Check opening hours, contact details and ordering options.',
			'body'        => '<h1>COME FIND YOUR BUNGRY.</h1>',
		),
		'contact'   => array(
				'title'       => 'Contact Us',
				'status'      => 'publish',
				'description' => 'Contact Bungry Burger about the menu, your order or allergens. Send us a message and we will reply to the email address you provide.',
				'body'        => '<h1>GET IN TOUCH.</h1>',
			),
		'faqs'      => array(
			'title'       => 'Food Allergens & FAQs',
			'status'      => 'publish',
			'description' => 'Answers about the Bungry menu, ordering and allergen information. Check current ingredient and allergen information before you order.',
			'body'        => '<h1>ALLERGENS &amp; FAQS</h1>',
		),
		'blog'      => array(
			'title'       => 'Blog',
			'status'      => 'publish',
			'description' => 'Stories and updates from Bungry Burger.',
			'body'        => '',
		),
		'privacy'   => array(
			'title'       => 'Privacy Policy',
			'status'      => 'draft',
			'description' => '',
			'body'        => '<p><strong>DRAFT OUTLINE. NOT LEGAL ADVICE.</strong> To be reviewed before publishing.</p>',
		),
		'terms'     => array(
			'title'       => 'Terms / Ordering Information',
			'status'      => 'draft',
			'description' => '',
			'body'        => '<p><strong>DRAFT OUTLINE. NOT LEGAL ADVICE.</strong> Publish only if online ordering is enabled.</p>',
		),
	);
}

function bungry_add_demo_page(): void {
	add_theme_page(
		__( 'Bungry Demo', 'bungry-burger' ),
		__( 'Bungry Demo', 'bungry-burger' ),
		'manage_options',
		'bungry-demo',
		'bungry_render_demo_page'
	);
}
add_action( 'admin_menu', 'bungry_add_demo_page' );

function bungry_render_demo_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display-only success flag.
	$done = isset( $_GET['bungry_done'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['bungry_done'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import Bungry demo pages & menu', 'bungry-burger' ); ?></h1>
		<p><?php esc_html_e( 'Creates the seven site pages, sets Home as the front page, builds the Primary and Footer menus and seeds the five burgers. Existing pages with the same title are reused, not overwritten. Privacy and Terms are created as drafts.', 'bungry-burger' ); ?></p>
		<?php if ( '' !== $done ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Demo content imported.', 'bungry-burger' ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="bungry_import_demo">
			<?php wp_nonce_field( 'bungry_import_demo' ); ?>
			<?php submit_button( __( 'Import Bungry demo pages & menu', 'bungry-burger' ) ); ?>
		</form>
	</div>
	<?php
}

function bungry_handle_demo_import(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'bungry-burger' ), 403 );
	}
	check_admin_referer( 'bungry_import_demo' );

	$ids = array();
	foreach ( bungry_demo_pages() as $key => $page ) {
		$existing = bungry_find_post_by_title( $page['title'], 'page' );
		if ( $existing ) {
			$ids[ $key ] = (int) $existing->ID;
		} else {
			$ids[ $key ] = (int) wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $page['title'],
					'post_status'  => $page['status'],
					'post_content' => wp_kses_post( $page['body'] ),
				)
			);
		}
		if ( '' !== $page['description'] && $ids[ $key ] ) {
			update_post_meta( $ids[ $key ], '_bungry_meta_description', sanitize_textarea_field( $page['description'] ) );
		}
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	update_option( 'page_for_posts', $ids['blog'] );

	bungry_seed_menu_items();
	bungry_apply_elementor_kit();

	bungry_build_menu(
		'Bungry Primary',
		'primary',
		array( 'menu', 'our-story', 'find-us', 'faqs' ),
		$ids
	);
	bungry_build_menu(
		'Bungry Footer Quick Links',
		'footer-quick',
		array( 'menu', 'our-story', 'find-us', 'faqs' ),
		$ids
	);
	bungry_build_menu(
		'Bungry Footer Need Help',
		'footer-help',
		array( 'find-us', 'faqs', 'terms', 'privacy' ),
		$ids
	);

	bungry_import_theme_builder_templates();
	wp_safe_redirect( add_query_arg( 'bungry_done', '1', admin_url( 'themes.php?page=bungry-demo' ) ) );
	exit;
}
add_action( 'admin_post_bungry_import_demo', 'bungry_handle_demo_import' );

/**
 * Builds (or rebuilds) a nav menu from page keys and assigns it to a theme location.
 *
 * @param string            $name      Menu name.
 * @param string            $location  Theme location.
 * @param array<int,string> $page_keys Keys from bungry_demo_pages().
 * @param array<string,int> $ids       Map of page key => post ID.
 */
function bungry_build_menu( string $name, string $location, array $page_keys, array $ids ): void {
	$menu    = wp_get_nav_menu_object( $name );
	$menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu( $name );
	if ( ! $menu_id || is_wp_error( $menu_id ) ) {
		return;
	}

	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
		wp_delete_post( (int) $item->ID, true );
	}

	foreach ( $page_keys as $position => $key ) {
		if ( empty( $ids[ $key ] ) ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object-id' => $ids[ $key ],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position + 1,
			)
		);
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Creates or updates the Elementor Theme Builder header and footer from the bundled templates.
 * Needs Elementor Pro (Theme Builder). Without Pro the coded header and footer stay in use.
 */
function bungry_import_theme_builder_templates(): void {
	if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
		return;
	}
	$templates = array(
		'header' => array(
			'title' => 'Bungry Header',
			'file'  => 'bungry-header.json',
		),
		'footer' => array(
			'title' => 'Bungry Footer',
			'file'  => 'bungry-footer.json',
		),
	);
	foreach ( $templates as $type => $tpl ) {
		$path = BUNGRY_DIR . '/elementor/templates/' . $tpl['file'];
		if ( ! is_readable( $path ) ) {
			continue;
		}
		$data = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! is_array( $data ) || empty( $data['content'] ) ) {
			continue;
		}
		$existing = bungry_find_post_by_title( $tpl['title'], 'elementor_library' );
		$post_id  = $existing ? $existing->ID : wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_title'  => $tpl['title'],
				'post_status' => 'publish',
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		update_post_meta( $post_id, '_elementor_template_type', $type );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $post_id, '_elementor_data', wp_json_encode( $data['content'] ) );
		update_post_meta( $post_id, '_elementor_page_settings', array() );
		update_post_meta( $post_id, '_elementor_conditions', array( 'include/general' ) );
	}
}
