<?php
/**
 * Nolan Starter Theme Customizer
 *
 * @package StarterTheme
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 */
function starter_theme_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', [
			'selector'        => '.site-title a',
			'render_callback' => 'starter_theme_customize_partial_blogname',
		] );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', [
			'selector'        => '.site-description',
			'render_callback' => 'starter_theme_customize_partial_blogdescription',
		] );
	}
}
add_action( 'customize_register', 'starter_theme_customize_register' );

function starter_theme_customize_partial_blogname() {
	bloginfo( 'name' );
}

function starter_theme_customize_partial_blogdescription() {
	bloginfo( 'description' );
}
