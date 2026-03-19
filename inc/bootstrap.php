<?php
/**
 * Module bootstrap — registers all theme modules
 * Hooked to after_setup_theme at priority 20 (after theme setup)
 *
 * @package StarterTheme
 */

defined( 'ABSPATH' ) || exit;

use StarterTheme\Module\ActionLog\ActionLogModule;

// Suppress $wpdb HTML error output on REST requests to prevent JSON corruption and SQL leaks
add_action( 'rest_api_init', function () {
	global $wpdb;
	$wpdb->suppress_errors( true );
	$wpdb->show_errors( false );
} );

add_action( 'after_setup_theme', function () {
	ActionLogModule::init();

	// Register more modules here:
	// YourModuleModule::init();

	do_action( 'starter_modules_loaded' );
}, 20 );
