<?php
/**
 * Verify Stage 17 page language links.
 *
 * Run with:
 * wp eval-file tools/stage17-verify.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

$groups = array(
	'about'                           => array( 'about', 'about-en' ),
	'services'                        => array( 'services', 'services-en' ),
	'video-editing-motion'            => array( 'services/video-editing-motion', 'services-en/video-editing-motion-en' ),
	'graphic-design'                  => array( 'services/graphic-design', 'services-en/graphic-design-en' ),
	'sketch-illustration'             => array( 'services/sketch-illustration', 'services-en/sketch-illustration-en' ),
	'storyboarding-creative-planning' => array( 'services/storyboarding-creative-planning', 'services-en/storyboarding-creative-planning-en' ),
	'branding'                        => array( 'services/branding', 'services-en/branding-en' ),
	'creative-direction-custom'       => array( 'services/creative-direction-custom', 'services-en/creative-direction-custom-en' ),
);

foreach ( $groups as $name => $paths ) {
	$ar_page = get_page_by_path( $paths[0], OBJECT, 'page' );
	$en_page = get_page_by_path( $paths[1], OBJECT, 'page' );

	if ( ! $ar_page || ! $en_page ) {
		WP_CLI::error( 'Missing page for ' . $name );
	}

	$ar_id = (int) $ar_page->ID;
	$en_id = (int) $en_page->ID;

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

WP_CLI::success( 'Stage 17 translation verification complete.' );
