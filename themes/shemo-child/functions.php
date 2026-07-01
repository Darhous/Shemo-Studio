<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'shemo_child_enqueue_styles' );
add_action( 'after_setup_theme', 'shemo_child_theme_setup' );
add_action( 'enqueue_block_editor_assets', 'shemo_child_enqueue_editor_assets' );
add_action( 'init', 'shemo_child_register_block_styles' );

require_once get_stylesheet_directory() . '/inc/rank-math-polylang.php';

function shemo_child_theme_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}

function shemo_child_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Aref+Ruqaa:wght@400;700&family=Fraunces:opsz,wght@9..144,600..900&family=Inter:wght@400;600;700;800&family=Noto+Kufi+Arabic:wght@400;600;700;800&display=swap';
}

function shemo_child_enqueue_styles() {
	wp_enqueue_style(
		'generatepress',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'shemo-fonts',
		shemo_child_fonts_url(),
		array(),
		null
	);

	wp_enqueue_style(
		'shemo-child',
		get_stylesheet_uri(),
		array( 'generatepress', 'shemo-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
}

function shemo_child_enqueue_editor_assets() {
	wp_enqueue_style(
		'shemo-fonts-editor',
		shemo_child_fonts_url(),
		array(),
		null
	);
}

function shemo_child_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-primary',
			'label' => __( 'Shemo Primary', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-secondary',
			'label' => __( 'Shemo Secondary', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-ghost',
			'label' => __( 'Shemo Ghost', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'shemo-card',
			'label' => __( 'Shemo Card', 'shemo-child' ),
		)
	);
}
