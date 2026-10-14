<?php
/**
 * Bungry Burger theme bootstrap.
 *
 * @package BungryBurger
 */

defined( 'ABSPATH' ) || exit;

define( 'BUNGRY_VERSION', '0.1.0' );
define( 'BUNGRY_DIR', get_template_directory() );
define( 'BUNGRY_URI', get_template_directory_uri() );

require_once BUNGRY_DIR . '/inc/helpers.php';
require_once BUNGRY_DIR . '/inc/setup.php';
require_once BUNGRY_DIR . '/inc/enqueue.php';
require_once BUNGRY_DIR . '/inc/settings.php';
require_once BUNGRY_DIR . '/inc/cpt-menu.php';
require_once BUNGRY_DIR . '/inc/class-bungry-nav-walker.php';
require_once BUNGRY_DIR . '/inc/elementor-kit.php';
require_once BUNGRY_DIR . '/inc/elementor-widgets.php';
require_once BUNGRY_DIR . '/inc/admin-notices.php';
require_once BUNGRY_DIR . '/inc/demo-importer.php';
