<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'shemo_child_enqueue_styles' );
add_action( 'after_setup_theme', 'shemo_child_theme_setup' );
add_action( 'enqueue_block_editor_assets', 'shemo_child_enqueue_editor_assets' );
add_action( 'init', 'shemo_child_register_block_styles' );
add_action( 'pre_get_posts', 'shemo_child_filter_project_archive_query' );
add_action( 'wp_head', 'shemo_child_project_archive_canonical_tag', 1 );
add_filter( 'language_attributes', 'shemo_child_language_attributes_dir', 20 );
add_filter( 'generate_sidebar_layout', 'shemo_child_sidebar_layout' );

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

function shemo_child_current_language(): string {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );

		if ( is_string( $lang ) && '' !== $lang ) {
			return $lang;
		}
	}

	return is_rtl() ? 'ar' : 'en';
}

function shemo_child_language_attributes_dir( string $output ): string {
	if ( false !== strpos( $output, 'dir=' ) ) {
		return $output;
	}

	return trim( $output . ' dir="' . esc_attr( is_rtl() ? 'rtl' : 'ltr' ) . '"' );
}

function shemo_child_project_archive_canonical_tag(): void {
	if ( ! is_post_type_archive( 'project' ) ) {
		return;
	}

	$archive = get_post_type_archive_link( 'project' );
	if ( ! $archive ) {
		return;
	}

	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( trailingslashit( $archive ) ) );
}

function shemo_child_sidebar_layout( string $layout ): string {
	if ( is_page() || is_post_type_archive( 'project' ) || is_singular( 'project' ) || is_search() || is_404() ) {
		return 'no-sidebar';
	}

	return $layout;
}

function shemo_child_demo_project_label( ?string $lang = null ): string {
	$lang = $lang ?: shemo_child_current_language();

	return 'ar' === $lang
		? 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري'
		: 'Demo / Concept Project - Not commissioned by a client';
}

function shemo_child_project_archive_filters(): array {
	if ( 'ar' === shemo_child_current_language() ) {
		return array(
			'service'        => 'الخدمة',
			'project_type'   => 'نوع المشروع',
			'industry'       => 'القطاع',
			'platform'       => 'المنصة',
			'tool'           => 'الأداة',
			'content_format' => 'صيغة المحتوى',
			'client_type'    => 'حالة العميل',
			'visual_style'   => 'الاتجاه البصري',
		);
	}

	return array(
		'service'        => 'Service',
		'project_type'   => 'Project Type',
		'industry'       => 'Industry',
		'platform'       => 'Platform',
		'tool'           => 'Tool',
		'content_format' => 'Content Format',
		'client_type'    => 'Client Type',
		'visual_style'   => 'Visual Style',
	);
}

function shemo_child_filter_project_archive_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'project' ) ) {
		return;
	}

	$query->set( 'posts_per_page', 9 );
	$query->set( 'orderby', 'menu_order date' );
	$query->set( 'order', 'DESC' );

	$tax_query = array();

	foreach ( array_keys( shemo_child_project_archive_filters() ) as $taxonomy ) {
		if ( empty( $_GET[ $taxonomy ] ) ) {
			continue;
		}

		$slug = sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) );
		$term = get_term_by( 'slug', $slug, $taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slug,
		);
	}

	if ( ! empty( $tax_query ) ) {
		$tax_query['relation'] = 'AND';
		$query->set( 'tax_query', $tax_query );
	}
}
