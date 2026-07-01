<?php
/**
 * Verify Stage 20 pages, forms, Turnstile, privacy link, and plugin baseline.
 *
 * Run with:
 * wp eval-file tools/stage20-verify.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

function shemo_stage20_fail( string $message ): void {
	WP_CLI::error( $message );
}

$expected_pages = array(
	'start-a-project'  => array( 'start-a-project', 'start-a-project-en' ),
	'contact'          => array( 'contact', 'contact-en' ),
	'thank-you'        => array( 'thank-you', 'thank-you-en' ),
	'search'           => array( 'search', 'search-en' ),
	'terms'            => array( 'terms', 'terms-en' ),
	'revision-policy'  => array( 'revision-policy', 'revision-policy-en' ),
	'deposit-policy'   => array( 'deposit-policy', 'deposit-policy-en' ),
	'refund-policy'    => array( 'refund-policy', 'refund-policy-en' ),
	'delivery-policy'  => array( 'delivery-policy', 'delivery-policy-en' ),
);

foreach ( $expected_pages as $key => $slugs ) {
	$ar = get_page_by_path( $slugs[0], OBJECT, 'page' );
	$en = get_page_by_path( $slugs[1], OBJECT, 'page' );

	if ( ! $ar || ! $en ) {
		shemo_stage20_fail( 'Missing page pair for ' . $key );
	}

	$ar_id = (int) $ar->ID;
	$en_id = (int) $en->ID;

	if ( 'publish' !== $ar->post_status || 'publish' !== $en->post_status ) {
		shemo_stage20_fail( 'Page pair is not published for ' . $key );
	}

	if ( 'ar' !== pll_get_post_language( $ar_id ) || 'en' !== pll_get_post_language( $en_id ) ) {
		shemo_stage20_fail( 'Language mismatch for ' . $key );
	}

	if ( (int) pll_get_post( $ar_id, 'en' ) !== $en_id || (int) pll_get_post( $en_id, 'ar' ) !== $ar_id ) {
		shemo_stage20_fail( 'Translation link mismatch for ' . $key );
	}

	if ( ! get_post_meta( $ar_id, 'rank_math_description', true ) || ! get_post_meta( $en_id, 'rank_math_description', true ) ) {
		shemo_stage20_fail( 'Missing Rank Math description for ' . $key );
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
		'Shemo Start a Project - AR',
		'Shemo Start a Project - EN'
	)
);

if ( 2 !== count( $forms ) ) {
	shemo_stage20_fail( 'Expected 2 Stage 20 Fluent Forms.' );
}

foreach ( $forms as $form ) {
	$fields = json_decode( $form->form_fields, true );
	if ( empty( $fields['fields'] ) || count( $fields['fields'] ) < 10 ) {
		shemo_stage20_fail( 'Start form has too few fields: ' . $form->title );
	}

	$elements = wp_list_pluck( $fields['fields'], 'element' );
	if ( ! in_array( 'turnstile', $elements, true ) ) {
		shemo_stage20_fail( 'Start form missing Turnstile: ' . $form->title );
	}

	if ( 'published' !== $form->status ) {
		shemo_stage20_fail( 'Start form is not published: ' . $form->title );
	}

	echo 'form=' . (int) $form->id . ' title=' . $form->title . ' fields=' . count( $fields['fields'] ) . ' has_turnstile=yes' . PHP_EOL;
}

$turnstile = get_option( '_fluentform_turnstile_details' );
if ( empty( $turnstile['siteKey'] ) || empty( $turnstile['secretKey'] ) || ! get_option( '_fluentform_turnstile_keys_status', false ) ) {
	shemo_stage20_fail( 'Turnstile is not configured and validated in Fluent Forms settings.' );
}

$start = get_page_by_path( 'start-a-project', OBJECT, 'page' );
if ( false === strpos( $start->post_content, '[fluentform id="' ) ) {
	shemo_stage20_fail( 'Start a Project page is missing Fluent Forms shortcode.' );
}

$deposit = get_page_by_path( 'deposit-policy', OBJECT, 'page' );
if ( false === strpos( $deposit->post_content, 'لم يتم اعتماد سياسة عربون نهائية' ) ) {
	shemo_stage20_fail( 'Deposit Policy is missing non-final policy notice.' );
}

$privacy_option = (int) get_option( 'wp_page_for_privacy_policy' );
if ( 18 !== $privacy_option || 'publish' !== get_post_status( $privacy_option ) ) {
	shemo_stage20_fail( 'WordPress privacy policy option is not safely linked to published Complianz page ID 18.' );
}

if ( 'draft' !== get_post_status( 3 ) ) {
	shemo_stage20_fail( 'Legacy Privacy Policy page ID 3 should remain draft.' );
}

$active_plugins = get_option( 'active_plugins', array() );
if ( 15 !== count( $active_plugins ) ) {
	shemo_stage20_fail( 'Expected 15 active plugins, got ' . count( $active_plugins ) );
}

echo 'turnstile=configured' . PHP_EOL;
echo 'privacy_policy_option=' . $privacy_option . PHP_EOL;
echo 'legacy_privacy_status=' . get_post_status( 3 ) . PHP_EOL;
echo 'active_plugins=' . count( $active_plugins ) . PHP_EOL;
echo "Stage 20 verification complete.\n";
