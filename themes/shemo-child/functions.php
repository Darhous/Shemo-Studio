<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'shemo_child_enqueue_styles' );

function shemo_child_enqueue_styles() {
	wp_enqueue_style(
		'generatepress',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'shemo-child',
		get_stylesheet_uri(),
		array( 'generatepress' ),
		wp_get_theme()->get( 'Version' )
	);
}
