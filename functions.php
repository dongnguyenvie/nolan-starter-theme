<?php
/**
 * Nolan Starter Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package StarterTheme
 */

if ( ! defined( 'STARTER_VERSION' ) ) {
	define( 'STARTER_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function starter_theme_setup() {
	load_theme_textdomain( 'starter-theme', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( [
		'primary' => esc_html__( 'Primary', 'starter-theme' ),
		'footer'  => esc_html__( 'Footer', 'starter-theme' ),
	] );

	add_theme_support( 'html5', [
		'search-form', 'comment-form', 'comment-list',
		'gallery', 'caption', 'style', 'script',
	] );

	add_theme_support( 'custom-background', apply_filters( 'starter_theme_custom_background_args', [
		'default-color' => 'ffffff',
		'default-image' => '',
	] ) );

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'custom-logo', [
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	] );
}
add_action( 'after_setup_theme', 'starter_theme_setup' );

/**
 * Set the content width in pixels.
 */
function starter_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'starter_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'starter_theme_content_width', 0 );

/**
 * Register widget area.
 */
function starter_theme_widgets_init() {
	register_sidebar( [
		'name'          => esc_html__( 'Sidebar', 'starter-theme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'starter-theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	] );
}
add_action( 'widgets_init', 'starter_theme_widgets_init' );

/**
 * Enqueue base styles.
 */
function starter_theme_scripts() {
	wp_enqueue_style( 'starter-style', get_stylesheet_uri(), [], STARTER_VERSION );
	wp_style_add_data( 'starter-style', 'rtl', 'replace' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'starter_theme_scripts' );

/**
 * Enqueue Alpine.js, Tailwind CSS, and bundled app JS.
 */
add_action( 'wp_enqueue_scripts', function () {
	// Tailwind CSS (built via: npm run build:css)
	$tw_file = get_template_directory() . '/assets/tailwind.css';
	wp_enqueue_style( 'starter-tailwind', get_template_directory_uri() . '/assets/tailwind.css', [], file_exists( $tw_file ) ? filemtime( $tw_file ) : STARTER_VERSION );

	// Alpine.js from CDN
	wp_enqueue_script( 'alpinejs', 'https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js', [], '3.15.8', true );

	// Localize theme data for JS
	wp_localize_script( 'alpinejs', 'starterData', [
		'nonce'      => wp_create_nonce( 'wp_rest' ),
		'apiBase'    => rest_url( 'starter/v1' ),
		'isLoggedIn' => is_user_logged_in(),
		'siteUrl'    => home_url(),
	] );

	// Single bundled JS (built via: npm run build:js)
	// Load in HEAD so store functions are defined before Alpine boots in footer
	$js_file = get_template_directory() . '/assets/app.min.js';
	wp_enqueue_script( 'starter-app', get_template_directory_uri() . '/assets/app.min.js', [], file_exists( $js_file ) ? filemtime( $js_file ) : STARTER_VERSION, false );
} );

/**
 * Ensure lazy loading + async decoding on all WP-generated images.
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( ! isset( $attr['loading'] ) )  $attr['loading']  = 'lazy';
	if ( ! isset( $attr['decoding'] ) ) $attr['decoding'] = 'async';
	return $attr;
} );

/**
 * Inject dark-mode scoped CSS and [x-cloak] utility.
 */
add_action( 'wp_enqueue_scripts', function () {
	$css = '
		html.dark body { background: #030712; color: #f9fafb; }
		html.dark #colophon { background: #111827 !important; color: #9ca3af; border-top: 1px solid #1f2937; }
		html.dark #masthead { background: #030712 !important; border-bottom: 1px solid #1f2937; }
		html.dark .site-title a, html.dark .site-description { color: #f9fafb; }
		html.dark .main-navigation a { color: #d1d5db; }
		html.dark .main-navigation a:hover { color: #3b82f6; }
		[x-cloak] { display: none !important; }
	';
	wp_add_inline_style( 'starter-style', $css );
}, 20 );

/**
 * Theme includes — core utilities.
 */
require get_template_directory() . '/inc/theme-page-setup.php';
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Composer autoloader (required — run `composer install` after cloning).
 */
require_once get_template_directory() . '/vendor/autoload.php';

/**
 * Module bootstrap — registers all theme modules.
 */
require_once get_template_directory() . '/inc/bootstrap.php';
