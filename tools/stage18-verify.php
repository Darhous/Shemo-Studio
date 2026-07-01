<?php
/**
 * Verify Stage 18 Work archive and demo/concept projects.
 *
 * Run with:
 * wp eval-file tools/stage18-verify.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

$projects = array(
	'frame-pulse' => array( 'frame-pulse-launch-film', 'frame-pulse-launch-film-en' ),
	'ember-menu'  => array( 'ember-menu-social-kit', 'ember-menu-social-kit-en' ),
	'line-course' => array( 'line-course-storyboard-study', 'line-course-storyboard-study-en' ),
);

$required = array(
	'ar' => 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري',
	'en' => 'Demo / Concept Project - Not commissioned by a client',
);

foreach ( $projects as $name => $slugs ) {
	$ar = get_page_by_path( $slugs[0], OBJECT, 'project' );
	$en = get_page_by_path( $slugs[1], OBJECT, 'project' );

	if ( ! $ar || ! $en ) {
		WP_CLI::error( 'Missing project pair for ' . $name );
	}

	$ar_id = (int) $ar->ID;
	$en_id = (int) $en->ID;

	if ( 'ar' !== pll_get_post_language( $ar_id ) || 'en' !== pll_get_post_language( $en_id ) ) {
		WP_CLI::error( 'Language mismatch for ' . $name );
	}

	if ( (int) pll_get_post( $ar_id, 'en' ) !== $en_id || (int) pll_get_post( $en_id, 'ar' ) !== $ar_id ) {
		WP_CLI::error( 'Translation link mismatch for ' . $name );
	}

	foreach ( array( 'service', 'project_type', 'industry', 'platform', 'tool', 'content_format', 'client_type', 'visual_style' ) as $taxonomy ) {
		if ( ! has_term( '', $taxonomy, $ar_id ) || ! has_term( '', $taxonomy, $en_id ) ) {
			WP_CLI::error( 'Missing taxonomy ' . $taxonomy . ' for ' . $name );
		}
	}

	$ar_meta = get_post_meta( $ar_id, 'rank_math_description', true );
	$en_meta = get_post_meta( $en_id, 'rank_math_description', true );

	if ( false === strpos( $ar_meta, $required['ar'] ) || false === strpos( $en_meta, $required['en'] ) ) {
		WP_CLI::error( 'Required Demo/Concept label missing from metadata for ' . $name );
	}

	WP_CLI::log(
		sprintf(
			'%s: ar=%d lang=%s en=%d lang=%s ar_to_en=%d en_to_ar=%d ar_url=%s en_url=%s',
			$name,
			$ar_id,
			pll_get_post_language( $ar_id ),
			$en_id,
			pll_get_post_language( $en_id ),
			(int) pll_get_post( $ar_id, 'en' ),
			(int) pll_get_post( $en_id, 'ar' ),
			get_permalink( $ar_id ),
			get_permalink( $en_id )
		)
	);
}

$ar_count = new WP_Query(
	array(
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'lang'           => 'ar',
	)
);
$en_count = new WP_Query(
	array(
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'lang'           => 'en',
	)
);

WP_CLI::log( 'project_count_ar=' . (int) $ar_count->found_posts . ' project_count_en=' . (int) $en_count->found_posts );
WP_CLI::success( 'Stage 18 project verification complete.' );
