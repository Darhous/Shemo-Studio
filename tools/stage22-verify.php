<?php
/**
 * Verify Stage 22 enriched demo content from WordPress/DB.
 *
 * Run with:
 * wp eval-file tools/stage22-verify.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

function shemo_stage22_verify_fail( string $message ): void {
	WP_CLI::error( $message );
}

function shemo_stage22_verify_posts( string $type, string $lang, int $expected ): array {
	$posts = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'lang'           => $lang,
			'orderby'        => 'menu_order date',
			'order'          => 'DESC',
		)
	);

	if ( count( $posts ) !== $expected ) {
		shemo_stage22_verify_fail( sprintf( '%s %s count expected %d, got %d', $type, $lang, $expected, count( $posts ) ) );
	}

	return $posts;
}

if ( ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'pll_get_post_translations' ) ) {
	shemo_stage22_verify_fail( 'Polylang functions are unavailable.' );
}

$options = get_option( 'polylang', array() );
$post_types = is_array( $options ) && isset( $options['post_types'] ) && is_array( $options['post_types'] ) ? $options['post_types'] : array();
foreach ( array( 'project', 'package', 'testimonial' ) as $type ) {
	if ( ! in_array( $type, $post_types, true ) ) {
		shemo_stage22_verify_fail( 'Polylang post_types missing ' . $type );
	}
}

$projects_ar = shemo_stage22_verify_posts( 'project', 'ar', 8 );
$projects_en = shemo_stage22_verify_posts( 'project', 'en', 8 );
$packages_ar = shemo_stage22_verify_posts( 'package', 'ar', 5 );
$packages_en = shemo_stage22_verify_posts( 'package', 'en', 5 );
$testimonials_ar = shemo_stage22_verify_posts( 'testimonial', 'ar', 3 );
$testimonials_en = shemo_stage22_verify_posts( 'testimonial', 'en', 3 );

foreach ( $projects_ar as $project ) {
	$translations = pll_get_post_translations( $project->ID );
	if ( empty( $translations['en'] ) ) {
		shemo_stage22_verify_fail( 'Project missing English translation: ' . $project->post_title );
	}

	if ( ! has_post_thumbnail( $project->ID ) ) {
		shemo_stage22_verify_fail( 'Project missing featured image: ' . $project->post_title );
	}

	$gallery = get_post_meta( $project->ID, 'shemo_gallery', false );
	if ( count( array_filter( $gallery ) ) < 3 ) {
		shemo_stage22_verify_fail( 'Project gallery has fewer than 3 images: ' . $project->post_title );
	}

	$description = (string) get_post_meta( $project->ID, 'rank_math_description', true );
	if ( false === strpos( $description, 'مشروع تجريبي / Concept' ) ) {
		shemo_stage22_verify_fail( 'Project Rank Math description missing AR demo label: ' . $project->post_title );
	}
}

foreach ( $projects_en as $project ) {
	$description = (string) get_post_meta( $project->ID, 'rank_math_description', true );
	if ( false === strpos( $description, 'Demo / Concept Project' ) ) {
		shemo_stage22_verify_fail( 'Project Rank Math description missing EN demo label: ' . $project->post_title );
	}
}

foreach ( array_merge( $packages_ar, $packages_en ) as $package ) {
	foreach ( array( 'shemo_package_price_from', 'shemo_package_price_to', 'shemo_package_scope', 'shemo_package_timeline' ) as $meta_key ) {
		$value = get_post_meta( $package->ID, $meta_key, true );
		if ( empty( $value ) ) {
			shemo_stage22_verify_fail( 'Package missing meta ' . $meta_key . ': ' . $package->post_title );
		}
	}
}

foreach ( $packages_ar as $package ) {
	$translations = pll_get_post_translations( $package->ID );
	if ( empty( $translations['en'] ) ) {
		shemo_stage22_verify_fail( 'Package missing English translation: ' . $package->post_title );
	}
}

foreach ( array_merge( $testimonials_ar, $testimonials_en ) as $testimonial ) {
	$label = (string) get_post_meta( $testimonial->ID, 'shemo_testimonial_label', true );
	if ( '' === $label || ( false === stripos( $label, 'demo' ) && false === strpos( $label, 'تجريبي' ) ) ) {
		shemo_stage22_verify_fail( 'Testimonial missing demo label: ' . $testimonial->post_title );
	}
}

foreach ( $testimonials_ar as $testimonial ) {
	$translations = pll_get_post_translations( $testimonial->ID );
	if ( empty( $translations['en'] ) ) {
		shemo_stage22_verify_fail( 'Testimonial missing English translation: ' . $testimonial->post_title );
	}
}

$assets = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'meta_key'       => '_shemo_asset_key',
	)
);
if ( count( $assets ) < 10 ) {
	shemo_stage22_verify_fail( 'Expected at least 10 sourced media assets, got ' . count( $assets ) );
}

foreach ( $assets as $asset ) {
	foreach ( array( '_shemo_asset_source_url', '_shemo_asset_license', '_shemo_asset_credit' ) as $meta_key ) {
		if ( '' === (string) get_post_meta( $asset->ID, $meta_key, true ) ) {
			shemo_stage22_verify_fail( 'Asset missing source/license meta: ' . $asset->post_title );
		}
	}
}

$service_terms_ar = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => true, 'lang' => 'ar' ) );
$service_terms_en = get_terms( array( 'taxonomy' => 'service', 'hide_empty' => true, 'lang' => 'en' ) );
if ( is_wp_error( $service_terms_ar ) || count( $service_terms_ar ) < 6 ) {
	shemo_stage22_verify_fail( 'Arabic service coverage expected >= 6.' );
}
if ( is_wp_error( $service_terms_en ) || count( $service_terms_en ) < 6 ) {
	shemo_stage22_verify_fail( 'English service coverage expected >= 6.' );
}

$packages_page = get_page_by_path( 'packages' );
$testimonials_page = get_page_by_path( 'testimonials' );
$about_page = get_page_by_path( 'about' );
$terms_page = get_page_by_path( 'terms' );
if ( ! $packages_page || false === strpos( $packages_page->post_content, '[shemo_packages]' ) ) {
	shemo_stage22_verify_fail( 'Packages page does not use [shemo_packages].' );
}
if ( ! $testimonials_page || false === strpos( $testimonials_page->post_content, '[shemo_testimonials]' ) ) {
	shemo_stage22_verify_fail( 'Testimonials page does not use [shemo_testimonials].' );
}
if ( ! $about_page || false === strpos( $about_page->post_content, 'Hybrid studio' ) ) {
	shemo_stage22_verify_fail( 'About page does not contain expanded founder-led story.' );
}
if ( ! $terms_page || false === strpos( $terms_page->post_content, 'مسودة مراجعة قانونية' ) ) {
	shemo_stage22_verify_fail( 'Terms page missing legal draft warning.' );
}

$meta_boxes = apply_filters( 'rwmb_meta_boxes', array() );
$box_ids = wp_list_pluck( $meta_boxes, 'id' );
foreach ( array( 'shemo_project_details', 'shemo_package_details', 'shemo_testimonial_details' ) as $box_id ) {
	if ( ! in_array( $box_id, $box_ids, true ) ) {
		shemo_stage22_verify_fail( 'Meta Box group missing: ' . $box_id );
	}
}

WP_CLI::success(
	sprintf(
		'Stage 22 verified: projects=%d/%d packages=%d/%d testimonials=%d/%d sourced_assets=%d service_terms=%d/%d meta_boxes=%d',
		count( $projects_ar ),
		count( $projects_en ),
		count( $packages_ar ),
		count( $packages_en ),
		count( $testimonials_ar ),
		count( $testimonials_en ),
		count( $assets ),
		count( $service_terms_ar ),
		count( $service_terms_en ),
		count( $meta_boxes )
	)
);
