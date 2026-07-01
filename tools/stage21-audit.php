<?php
/**
 * Stage 21 database/readback QA for bilingual content, SEO, forms, and security baselines.
 *
 * Run with:
 * wp eval-file tools/stage21-audit.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

function shemo_stage21_fail( string $message ): void {
	WP_CLI::error( $message );
}

function shemo_stage21_warn( string $message ): void {
	WP_CLI::warning( $message );
}

$page_pairs = array(
	'home'                       => array( '', 'home-en' ),
	'about'                      => array( 'about', 'about-en' ),
	'services'                   => array( 'services', 'services-en' ),
	'video-editing-motion'       => array( 'services/video-editing-motion', 'services-en/video-editing-motion-en' ),
	'graphic-design'             => array( 'services/graphic-design', 'services-en/graphic-design-en' ),
	'sketch-illustration'        => array( 'services/sketch-illustration', 'services-en/sketch-illustration-en' ),
	'storyboarding-planning'     => array( 'services/storyboarding-creative-planning', 'services-en/storyboarding-creative-planning-en' ),
	'branding'                   => array( 'services/branding', 'services-en/branding-en' ),
	'creative-direction-custom'  => array( 'services/creative-direction-custom', 'services-en/creative-direction-custom-en' ),
	'packages'                   => array( 'packages', 'packages-en' ),
	'request-a-quote'            => array( 'request-a-quote', 'request-a-quote-en' ),
	'process'                    => array( 'process', 'process-en' ),
	'testimonials'               => array( 'testimonials', 'testimonials-en' ),
	'faq'                        => array( 'faq', 'faq-en' ),
	'start-a-project'            => array( 'start-a-project', 'start-a-project-en' ),
	'contact'                    => array( 'contact', 'contact-en' ),
	'thank-you'                  => array( 'thank-you', 'thank-you-en' ),
	'search'                     => array( 'search', 'search-en' ),
	'terms'                      => array( 'terms', 'terms-en' ),
	'revision-policy'            => array( 'revision-policy', 'revision-policy-en' ),
	'deposit-policy'             => array( 'deposit-policy', 'deposit-policy-en' ),
	'refund-policy'              => array( 'refund-policy', 'refund-policy-en' ),
	'delivery-policy'            => array( 'delivery-policy', 'delivery-policy-en' ),
);

foreach ( $page_pairs as $key => $slugs ) {
	if ( '' === $slugs[0] ) {
		$ar_id = (int) get_option( 'page_on_front' );
		$ar    = get_post( $ar_id );
	} else {
		$ar = get_page_by_path( $slugs[0], OBJECT, 'page' );
	}

	$en = get_page_by_path( $slugs[1], OBJECT, 'page' );

	if ( ! $ar || ! $en ) {
		shemo_stage21_fail( 'Missing page pair: ' . $key );
	}

	$ar_id = (int) $ar->ID;
	$en_id = (int) $en->ID;

	if ( 'publish' !== $ar->post_status || 'publish' !== $en->post_status ) {
		shemo_stage21_fail( 'Unpublished page in pair: ' . $key );
	}

	if ( 'ar' !== pll_get_post_language( $ar_id ) || 'en' !== pll_get_post_language( $en_id ) ) {
		shemo_stage21_fail( 'Language mismatch: ' . $key );
	}

	if ( (int) pll_get_post( $ar_id, 'en' ) !== $en_id || (int) pll_get_post( $en_id, 'ar' ) !== $ar_id ) {
		shemo_stage21_fail( 'Polylang translation mismatch: ' . $key );
	}

	if ( ! get_post_meta( $ar_id, 'rank_math_description', true ) || ! get_post_meta( $en_id, 'rank_math_description', true ) ) {
		shemo_stage21_fail( 'Missing Rank Math description: ' . $key );
	}

	echo sprintf( "page=%s ar=%d en=%d\n", $key, $ar_id, $en_id );
}

$projects = get_posts(
	array(
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

$project_counts = array( 'ar' => 0, 'en' => 0 );
foreach ( $projects as $project ) {
	$lang = pll_get_post_language( $project->ID );
	if ( isset( $project_counts[ $lang ] ) ) {
		++$project_counts[ $lang ];
	}

	$other_lang = 'ar' === $lang ? 'en' : 'ar';
	if ( ! pll_get_post( $project->ID, $other_lang ) ) {
		shemo_stage21_fail( 'Project missing translation: ' . $project->post_name );
	}

	$description = get_post_meta( $project->ID, 'rank_math_description', true );
	$label       = 'ar' === $lang
		? 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري'
		: 'Demo / Concept Project - Not commissioned by a client';

	if ( false === strpos( (string) $description, $label ) ) {
		shemo_stage21_fail( 'Project Rank Math description missing transparency label: ' . $project->post_name );
	}
}

if ( 3 !== $project_counts['ar'] || 3 !== $project_counts['en'] ) {
	shemo_stage21_fail( 'Expected 3 AR and 3 EN projects, got ar=' . $project_counts['ar'] . ' en=' . $project_counts['en'] );
}

global $wpdb;

$old_english_links = $wpdb->get_var(
	"SELECT COUNT(*) FROM {$wpdb->posts}
	WHERE post_status IN ('publish','draft')
	AND post_type IN ('page','project')
	AND (post_content LIKE '%/en/start-a-project/%' OR post_content LIKE '%/en/contact/%')"
);

if ( (int) $old_english_links > 0 ) {
	shemo_stage21_fail( 'Old English CTA links still exist in post content: ' . $old_english_links );
}

$active_plugins = get_option( 'active_plugins', array() );
if ( 15 !== count( $active_plugins ) ) {
	shemo_stage21_fail( 'Expected 15 active plugins, got ' . count( $active_plugins ) );
}

$forms_table = $wpdb->prefix . 'fluentform_forms';
$forms       = $wpdb->get_results(
	"SELECT id,title,status,form_fields FROM {$forms_table}
	WHERE title IN (
		'Shemo Request a Quote - AR',
		'Shemo Request a Quote - EN',
		'Shemo Start a Project - AR',
		'Shemo Start a Project - EN'
	)
	ORDER BY id"
);

if ( 4 !== count( $forms ) ) {
	shemo_stage21_fail( 'Expected 4 Shemo Fluent Forms, got ' . count( $forms ) );
}

foreach ( $forms as $form ) {
	if ( 'published' !== $form->status ) {
		shemo_stage21_fail( 'Unpublished Fluent Form: ' . $form->title );
	}

	$fields = json_decode( $form->form_fields, true );
	if ( empty( $fields['fields'] ) || ! is_array( $fields['fields'] ) ) {
		shemo_stage21_fail( 'Invalid Fluent Form fields JSON: ' . $form->title );
	}

	$field_markup = wp_json_encode( $fields['fields'] );
	if ( false === strpos( (string) $field_markup, 'email' ) ) {
		shemo_stage21_warn( 'Form field audit did not find an email marker: ' . $form->title );
	}

	echo 'form=' . (int) $form->id . ' title=' . $form->title . ' fields=' . count( $fields['fields'] ) . PHP_EOL;
}

$submissions_table = $wpdb->prefix . 'fluentform_submissions';
$submission_counts = $wpdb->get_results(
	"SELECT form_id, COUNT(*) AS total FROM {$submissions_table}
	WHERE form_id IN (3,4,5,6)
	GROUP BY form_id
	ORDER BY form_id"
);

foreach ( $submission_counts as $submission_count ) {
	echo 'submissions_form_' . (int) $submission_count->form_id . '=' . (int) $submission_count->total . PHP_EOL;
}

$turnstile = get_option( '_fluentform_turnstile_details' );
if ( empty( $turnstile['siteKey'] ) || empty( $turnstile['secretKey'] ) || ! get_option( '_fluentform_turnstile_keys_status', false ) ) {
	shemo_stage21_fail( 'Turnstile is not configured and validated in Fluent Forms.' );
}

$admin_users = get_users( array( 'role' => 'Administrator' ) );
if ( 1 !== count( $admin_users ) ) {
	shemo_stage21_warn( 'Administrator count is ' . count( $admin_users ) . ', expected one named admin.' );
}

foreach ( $admin_users as $admin_user ) {
	if ( 'admin' === strtolower( $admin_user->user_login ) ) {
		shemo_stage21_fail( 'Default admin username is exposed.' );
	}
}

if ( '0' !== (string) get_option( 'blog_public' ) ) {
	shemo_stage21_fail( 'LocalWP should remain noindex while pre-launch; blog_public is not 0.' );
}

if ( 18 !== (int) get_option( 'wp_page_for_privacy_policy' ) || 'publish' !== get_post_status( 18 ) ) {
	shemo_stage21_fail( 'Privacy policy option should point to published Complianz page ID 18.' );
}

if ( 'draft' !== get_post_status( 3 ) ) {
	shemo_stage21_fail( 'Legacy Privacy Policy page ID 3 should remain draft.' );
}

echo 'projects_ar=' . $project_counts['ar'] . ' projects_en=' . $project_counts['en'] . PHP_EOL;
echo 'old_english_links=' . (int) $old_english_links . PHP_EOL;
echo 'active_plugins=' . count( $active_plugins ) . PHP_EOL;
echo 'turnstile=configured' . PHP_EOL;
echo 'blog_public=' . get_option( 'blog_public' ) . PHP_EOL;
echo "Stage 21 database audit complete.\n";
