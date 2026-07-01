<?php
/**
 * Verify Stage 19 bilingual pages, Fluent Forms, and SureCart test baseline.
 *
 * Run with LocalWP PHP CLI from the repo root:
 * php -d extension_dir="...\ext" -d extension=mysqli -d mysqli.default_port=10004 tools/stage19-verify.php
 */

$wp_load = 'C:/Users/ahmed/Local Sites/Shemo-Studio-Clean-Start/app/public/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	fwrite( STDERR, "WordPress loader not found.\n" );
	exit( 1 );
}

require_once $wp_load;

function shemo_stage19_fail( string $message ): void {
	fwrite( STDERR, $message . "\n" );
	exit( 1 );
}

$expected_pages = array(
	'packages'        => array( 'packages', 'packages-en' ),
	'request-a-quote' => array( 'request-a-quote', 'request-a-quote-en' ),
	'process'         => array( 'process', 'process-en' ),
	'testimonials'    => array( 'testimonials', 'testimonials-en' ),
	'faq'             => array( 'faq', 'faq-en' ),
);

foreach ( $expected_pages as $key => $slugs ) {
	$ar = get_page_by_path( $slugs[0], OBJECT, 'page' );
	$en = get_page_by_path( $slugs[1], OBJECT, 'page' );

	if ( ! $ar || ! $en ) {
		shemo_stage19_fail( 'Missing page pair for ' . $key );
	}

	$ar_id = (int) $ar->ID;
	$en_id = (int) $en->ID;

	if ( 'ar' !== pll_get_post_language( $ar_id ) || 'en' !== pll_get_post_language( $en_id ) ) {
		shemo_stage19_fail( 'Language mismatch for ' . $key );
	}

	if ( (int) pll_get_post( $ar_id, 'en' ) !== $en_id || (int) pll_get_post( $en_id, 'ar' ) !== $ar_id ) {
		shemo_stage19_fail( 'Translation link mismatch for ' . $key );
	}

	if ( ! get_post_meta( $ar_id, 'rank_math_description', true ) || ! get_post_meta( $en_id, 'rank_math_description', true ) ) {
		shemo_stage19_fail( 'Missing Rank Math description for ' . $key );
	}

	echo sprintf(
		"%s: ar=%d lang=%s en=%d lang=%s ar_to_en=%d en_to_ar=%d\n",
		$key,
		$ar_id,
		pll_get_post_language( $ar_id ),
		$en_id,
		pll_get_post_language( $en_id ),
		(int) pll_get_post( $ar_id, 'en' ),
		(int) pll_get_post( $en_id, 'ar' )
	);
}

global $wpdb;

$forms_table = $wpdb->prefix . 'fluentform_forms';
$forms       = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT id,title,status,form_fields FROM {$forms_table} WHERE title IN (%s,%s) ORDER BY id",
		'Shemo Request a Quote - AR',
		'Shemo Request a Quote - EN'
	)
);

if ( 2 !== count( $forms ) ) {
	shemo_stage19_fail( 'Expected 2 Stage 19 Fluent Forms.' );
}

foreach ( $forms as $form ) {
	$fields = json_decode( $form->form_fields, true );
	if ( empty( $fields['fields'] ) || count( $fields['fields'] ) < 7 ) {
		shemo_stage19_fail( 'Quote form has too few fields: ' . $form->title );
	}
	if ( 'published' !== $form->status ) {
		shemo_stage19_fail( 'Quote form is not published: ' . $form->title );
	}
	echo 'form=' . (int) $form->id . ' title=' . $form->title . ' fields=' . count( $fields['fields'] ) . PHP_EOL;
}

$packages = get_page_by_path( 'packages', OBJECT, 'page' );
$quote    = get_page_by_path( 'request-a-quote', OBJECT, 'page' );
$test     = get_page_by_path( 'testimonials', OBJECT, 'page' );

if ( false === strpos( $packages->post_content, 'ليست قرارًا تجاريًا نهائيًا' ) ) {
	shemo_stage19_fail( 'Arabic packages page is missing non-final pricing notice.' );
}

if ( false === strpos( $packages->post_content, '/checkout/?mode=test' ) ) {
	shemo_stage19_fail( 'Packages page is missing SureCart test checkout links.' );
}

if ( false === strpos( $quote->post_content, '[fluentform id="' ) ) {
	shemo_stage19_fail( 'Request a Quote page is missing Fluent Forms shortcode.' );
}

if ( false === strpos( $test->post_content, 'لا توجد شهادات منشورة بعد' ) ) {
	shemo_stage19_fail( 'Testimonials page is missing transparent empty-state copy.' );
}

$active_plugins = get_option( 'active_plugins', array() );
if ( 15 !== count( $active_plugins ) ) {
	shemo_stage19_fail( 'Expected 15 active plugins, got ' . count( $active_plugins ) );
}

$surecart_forms = new WP_Query(
	array(
		'post_type'      => 'sc_form',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	)
);

echo 'active_plugins=' . count( $active_plugins ) . PHP_EOL;
echo 'surecart_sc_form_count=' . (int) $surecart_forms->found_posts . PHP_EOL;
echo "Stage 19 verification complete.\n";
