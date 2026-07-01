<?php
/**
 * Stage 22: enrich all editable demo/trial content.
 *
 * Run with:
 * wp eval-file tools/stage22-enrich-demo-content.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) || ! function_exists( 'pll_save_term_translations' ) ) {
	WP_CLI::error( 'Polylang functions are not available.' );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

const SHEMO_STAGE22_AR_LABEL = 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري';
const SHEMO_STAGE22_EN_LABEL = 'Demo / Concept Project - Not commissioned by a client';
const SHEMO_STAGE22_PACKAGE_AR_LABEL = 'أسعار Demo/Trial قابلة للتعديل - ليست اعتمادًا تجاريًا نهائيًا';
const SHEMO_STAGE22_PACKAGE_EN_LABEL = 'Demo / Trial pricing - editable and not final commercial approval';

function shemo_stage22_enable_polylang_types(): void {
	$options = get_option( 'polylang', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$post_types = isset( $options['post_types'] ) && is_array( $options['post_types'] ) ? $options['post_types'] : array();
	foreach ( array( 'project', 'package', 'testimonial' ) as $type ) {
		if ( ! in_array( $type, $post_types, true ) ) {
			$post_types[] = $type;
		}
	}
	$options['post_types'] = array_values( $post_types );
	update_option( 'polylang', $options );
}

function shemo_stage22_term( string $taxonomy, string $name, string $slug, string $lang ): int {
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing && ! is_wp_error( $existing ) ) {
		$term_id = (int) $existing->term_id;
	} else {
		$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
		if ( is_wp_error( $created ) ) {
			WP_CLI::error( $created->get_error_message() );
		}
		$term_id = (int) $created['term_id'];
	}

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $term_id, $lang );
	}

	return $term_id;
}

function shemo_stage22_term_pair( string $taxonomy, string $ar_name, string $ar_slug, string $en_name, string $en_slug ): array {
	$ar_id = shemo_stage22_term( $taxonomy, $ar_name, $ar_slug, 'ar' );
	$en_id = shemo_stage22_term( $taxonomy, $en_name, $en_slug, 'en' );
	pll_save_term_translations( array( 'ar' => $ar_id, 'en' => $en_id ) );
	return array( 'ar' => $ar_id, 'en' => $en_id );
}

function shemo_stage22_asset_id( array $asset ): int {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => '_shemo_asset_key',
			'meta_value'     => $asset['key'],
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$temp_file = download_url( $asset['download_url'] );
	if ( is_wp_error( $temp_file ) ) {
		WP_CLI::error( 'Media download failed for ' . $asset['key'] . ': ' . $temp_file->get_error_message() );
	}

	$file_array = array(
		'name'     => $asset['key'] . '.jpg',
		'tmp_name' => $temp_file,
	);

	$attachment_id = media_handle_sideload( $file_array, 0, $asset['title'] );
	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $temp_file );
		WP_CLI::error( 'Media upload failed for ' . $asset['key'] . ': ' . $attachment_id->get_error_message() );
	}

	$attachment_id = (int) $attachment_id;
	wp_update_post(
		array(
			'ID'           => $attachment_id,
			'post_title'   => $asset['title'],
			'post_excerpt' => $asset['credit'],
			'post_content' => $asset['source_url'] . ' | ' . $asset['license'],
		)
	);
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $asset['alt'] );
	update_post_meta( $attachment_id, '_shemo_asset_key', $asset['key'] );
	update_post_meta( $attachment_id, '_shemo_asset_source_url', $asset['source_url'] );
	update_post_meta( $attachment_id, '_shemo_asset_license', $asset['license'] );
	update_post_meta( $attachment_id, '_shemo_asset_credit', $asset['credit'] );

	return $attachment_id;
}

function shemo_stage22_page( string $slug, string $lang, string $title, string $content, string $description, string $status = 'publish' ): int {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( ! $page ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => $status,
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => $content,
			),
			true
		);
	} else {
		$post_id = wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_status'  => $status,
				'post_title'   => $title,
				'post_content' => $content,
			),
			true
		);
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	$post_id = (int) $post_id;
	pll_set_post_language( $post_id, $lang );
	update_post_meta( $post_id, '_generate-disable-headline', 'true' );
	update_post_meta( $post_id, '_generate-full-width-content', 'true' );
	update_post_meta( $post_id, 'rank_math_title', $title . ' - Shemo Studio' );
	update_post_meta( $post_id, 'rank_math_description', $description );

	return $post_id;
}

function shemo_stage22_pair_pages( array $ar, array $en, string $status = 'publish' ): array {
	$ar_id = shemo_stage22_page( $ar['slug'], 'ar', $ar['title'], $ar['content'], $ar['description'], $status );
	$en_id = shemo_stage22_page( $en['slug'], 'en', $en['title'], $en['content'], $en['description'], $status );
	pll_save_post_translations( array( 'ar' => $ar_id, 'en' => $en_id ) );
	return array( 'ar' => $ar_id, 'en' => $en_id );
}

function shemo_stage22_content_shell( string $class, string $html ): string {
	return '<!-- wp:group {"tagName":"main","className":"shemo-home shemo-page ' . esc_attr( $class ) . '","layout":{"type":"default"}} -->' . "\n"
		. '<main class="wp-block-group shemo-home shemo-page ' . esc_attr( $class ) . '">' . "\n"
		. '<!-- wp:html -->' . "\n"
		. $html . "\n"
		. '<!-- /wp:html -->' . "\n"
		. '</main>' . "\n"
		. '<!-- /wp:group -->';
}

function shemo_stage22_upsert_project( array $project, string $lang, array $terms, array $asset_ids ): int {
	$is_ar   = 'ar' === $lang;
	$slug    = $project[ $lang ]['slug'];
	$found   = get_page_by_path( $slug, OBJECT, 'project' );
	$content = $project[ $lang ]['process'];
	$label   = $is_ar ? SHEMO_STAGE22_AR_LABEL : SHEMO_STAGE22_EN_LABEL;

	$postarr = array(
		'post_title'   => $project[ $lang ]['title'],
		'post_name'    => $slug,
		'post_type'    => 'project',
		'post_status'  => 'publish',
		'post_excerpt' => $project[ $lang ]['summary'],
		'post_content' => $content,
		'menu_order'   => (int) $project['order'],
	);

	if ( $found ) {
		$postarr['ID'] = $found->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	$post_id = (int) $post_id;
	pll_set_post_language( $post_id, $lang );

	update_post_meta( $post_id, '_generate-disable-headline', 'true' );
	update_post_meta( $post_id, '_generate-full-width-content', 'true' );
	update_post_meta( $post_id, 'shemo_client_label', 'personal' );
	update_post_meta( $post_id, 'shemo_client_name', '' );
	update_post_meta( $post_id, 'shemo_short_summary', $project[ $lang ]['summary'] );
	update_post_meta( $post_id, 'shemo_project_date', '2026-07-02' );
	update_post_meta( $post_id, 'shemo_project_goal', $project[ $lang ]['goal'] );
	update_post_meta( $post_id, 'shemo_challenge', $project[ $lang ]['challenge'] );
	update_post_meta( $post_id, 'shemo_creative_direction', $project[ $lang ]['direction'] );
	update_post_meta( $post_id, 'shemo_deliverables', $project[ $lang ]['deliverables'] );
	update_post_meta( $post_id, 'shemo_results', $project[ $lang ]['results'] );
	update_post_meta( $post_id, 'shemo_credits', $project[ $lang ]['credits'] );
	update_post_meta( $post_id, 'shemo_featured', (int) $project['featured'] );
	update_post_meta( $post_id, '_shemo_project_status_label', $label );
	update_post_meta( $post_id, 'rank_math_title', $project[ $lang ]['title'] . ' - Shemo Studio' );
	update_post_meta( $post_id, 'rank_math_description', $label . ' - ' . $project[ $lang ]['summary'] );
	delete_post_meta( $post_id, 'shemo_testimonial' );

	$cover_id = $asset_ids[ $project['assets']['cover'] ];
	set_post_thumbnail( $post_id, $cover_id );

	foreach ( array( 'shemo_sketch_image' => 'sketch', 'shemo_before_image' => 'before', 'shemo_after_image' => 'after', 'shemo_gallery' => 'gallery' ) as $meta_key => $asset_key ) {
		delete_post_meta( $post_id, $meta_key );
		$keys = (array) $project['assets'][ $asset_key ];
		foreach ( $keys as $key ) {
			add_post_meta( $post_id, $meta_key, $asset_ids[ $key ] );
		}
	}

	foreach ( $terms as $taxonomy => $pair_keys ) {
		$term_ids = array();
		foreach ( $pair_keys as $key ) {
			$term_ids[] = $GLOBALS['shemo_stage22_terms'][ $taxonomy ][ $key ][ $lang ];
		}
		wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
	}

	return $post_id;
}

function shemo_stage22_upsert_package( array $package, string $lang ): int {
	$slug  = $package[ $lang ]['slug'];
	$found = get_page_by_path( $slug, OBJECT, 'package' );

	$postarr = array(
		'post_title'   => $package[ $lang ]['title'],
		'post_name'    => $slug,
		'post_type'    => 'package',
		'post_status'  => 'publish',
		'post_excerpt' => $package[ $lang ]['summary'],
		'post_content' => $package[ $lang ]['content'],
		'menu_order'   => (int) $package['order'],
	);

	if ( $found ) {
		$postarr['ID'] = $found->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	$post_id = (int) $post_id;
	pll_set_post_language( $post_id, $lang );
	update_post_meta( $post_id, 'shemo_package_label', 'ar' === $lang ? SHEMO_STAGE22_PACKAGE_AR_LABEL : SHEMO_STAGE22_PACKAGE_EN_LABEL );
	update_post_meta( $post_id, 'shemo_package_price_from', $package['price_from'] );
	update_post_meta( $post_id, 'shemo_package_price_to', $package['price_to'] );
	update_post_meta( $post_id, 'shemo_package_currency', 'EGP' );
	update_post_meta( $post_id, 'shemo_package_price_note', $package[ $lang ]['price_note'] );
	update_post_meta( $post_id, 'shemo_package_scope', $package[ $lang ]['scope'] );
	update_post_meta( $post_id, 'shemo_package_best_for', $package[ $lang ]['best_for'] );
	update_post_meta( $post_id, 'shemo_package_timeline', $package[ $lang ]['timeline'] );
	update_post_meta( $post_id, 'shemo_package_revisions', $package['revisions'] );
	update_post_meta( $post_id, 'shemo_package_deposit_percent', 50 );
	update_post_meta( $post_id, 'shemo_package_checkout_url', home_url( '/checkout/?mode=test&shemo_package=' . rawurlencode( $package['key'] ) . '&deposit=50' ) );
	update_post_meta( $post_id, 'shemo_package_featured', (int) $package['featured'] );
	update_post_meta( $post_id, 'rank_math_description', ( 'ar' === $lang ? SHEMO_STAGE22_PACKAGE_AR_LABEL : SHEMO_STAGE22_PACKAGE_EN_LABEL ) . ' - ' . $package[ $lang ]['summary'] );

	return $post_id;
}

function shemo_stage22_upsert_testimonial( array $testimonial, string $lang ): int {
	$slug  = $testimonial[ $lang ]['slug'];
	$found = get_page_by_path( $slug, OBJECT, 'testimonial' );

	$postarr = array(
		'post_title'   => $testimonial[ $lang ]['title'],
		'post_name'    => $slug,
		'post_type'    => 'testimonial',
		'post_status'  => 'publish',
		'post_excerpt' => $testimonial[ $lang ]['summary'],
		'post_content' => $testimonial[ $lang ]['quote'],
		'menu_order'   => (int) $testimonial['order'],
	);

	if ( $found ) {
		$postarr['ID'] = $found->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	$post_id = (int) $post_id;
	pll_set_post_language( $post_id, $lang );
	update_post_meta( $post_id, 'shemo_testimonial_label', 'ar' === $lang ? 'تقييم تجريبي / Demo - ليس من عميل حقيقي' : 'Demo Testimonial - Not from a real client' );
	update_post_meta( $post_id, 'shemo_testimonial_author_name', $testimonial[ $lang ]['author'] );
	update_post_meta( $post_id, 'shemo_testimonial_author_role', $testimonial[ $lang ]['role'] );
	update_post_meta( $post_id, 'shemo_testimonial_service_focus', $testimonial[ $lang ]['service'] );
	update_post_meta( $post_id, 'shemo_testimonial_rating', $testimonial['rating'] );
	update_post_meta( $post_id, 'shemo_testimonial_source_note', $testimonial[ $lang ]['note'] );
	update_post_meta( $post_id, 'rank_math_description', ( 'ar' === $lang ? 'تقييم تجريبي موسوم بوضوح، وليس شهادة عميل حقيقي.' : 'Clearly labeled demo testimonial, not a real client quote.' ) );

	return $post_id;
}

shemo_stage22_enable_polylang_types();

$assets = array(
	array(
		'key'          => 'video-editor-color',
		'title'        => 'Demo stock - video editor color grading workspace',
		'download_url' => 'https://images.pexels.com/photos/32479534/pexels-photo-32479534.jpeg?cs=srgb&dl=pexels-andreeusebio-32479534.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/32479534/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / andreeusebio',
		'alt'          => 'Video editing workspace used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'video-workspace-bw',
		'title'        => 'Demo stock - black and white editing desk',
		'download_url' => 'https://images.pexels.com/photos/11025646/pexels-photo-11025646.jpeg?cs=srgb&dl=pexels-amar-11025646.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/11025646/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Amar',
		'alt'          => 'Black and white editing desk used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'storyboard-close',
		'title'        => 'Demo stock - storyboard sketch close-up',
		'download_url' => 'https://images.pexels.com/photos/8085954/pexels-photo-8085954.jpeg?cs=srgb&dl=pexels-ron-lach-8085954.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/woman-filling-storyboard-in-8085954/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Ron Lach',
		'alt'          => 'Storyboard sketch close-up used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'storyboard-alt',
		'title'        => 'Demo stock - storyboard planning sheet',
		'download_url' => 'https://images.pexels.com/photos/8086356/pexels-photo-8086356.jpeg?cs=srgb&dl=pexels-ron-lach-8086356.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/8086356/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Ron Lach',
		'alt'          => 'Storyboard planning sheet used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'interior-swatches',
		'title'        => 'Demo stock - color and material swatches',
		'download_url' => 'https://images.pexels.com/photos/6580001/pexels-photo-6580001.jpeg?cs=srgb&dl=pexels-cottonbro-6580001.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/6580001/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / cottonbro studio',
		'alt'          => 'Color and material swatches used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'brand-strategy',
		'title'        => 'Demo stock - brand strategy moodboard',
		'download_url' => 'https://images.pexels.com/photos/7598009/pexels-photo-7598009.jpeg?cs=srgb&dl=pexels-leeloothefirst-7598009.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/7598009/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Leeloo The First',
		'alt'          => 'Brand strategy moodboard used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'brand-strategy-wide',
		'title'        => 'Demo stock - wide brand strategy board',
		'download_url' => 'https://images.pexels.com/photos/7598022/pexels-photo-7598022.jpeg?cs=srgb&dl=pexels-leeloothefirst-7598022.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/7598022/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Leeloo The First',
		'alt'          => 'Wide brand strategy board used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'material-board',
		'title'        => 'Demo stock - creative direction material board',
		'download_url' => 'https://images.pexels.com/photos/19328891/pexels-photo-19328891.jpeg?cs=srgb&dl=pexels-tiago-alves-710726839-19328891.jpg&fm=jpg',
		'source_url'   => 'https://www.pexels.com/photo/19328891/',
		'license'      => 'Pexels License - free for commercial use; no attribution required.',
		'credit'       => 'Pexels / Tiago Alves',
		'alt'          => 'Creative direction material board used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'tablet-sketch',
		'title'        => 'Demo stock - digital illustration tablet sketch',
		'download_url' => 'https://images.unsplash.com/photo-1730641884360-0f6bb86e70e6?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=1800',
		'source_url'   => 'https://unsplash.com/photos/a-person-drawing-on-a-tablet-with-a-pen-l5AXHs3Dfkk',
		'license'      => 'Unsplash License - free for commercial and non-commercial use.',
		'credit'       => 'Unsplash / Rodrigo Rodrigues | WOLF ART',
		'alt'          => 'Digital illustration tablet sketch used as licensed demo stock imagery.',
	),
	array(
		'key'          => 'whiteboard-planning',
		'title'        => 'Demo stock - whiteboard creative planning',
		'download_url' => 'https://images.unsplash.com/photo-1532619187608-e5375cab36aa?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=80&w=1800',
		'source_url'   => 'https://unsplash.com/photos/man-drawing-on-dry-erase-board-7lryofJ0H9s',
		'license'      => 'Unsplash License - free for commercial and non-commercial use.',
		'credit'       => 'Unsplash / Kaleidico',
		'alt'          => 'Whiteboard creative planning used as licensed demo stock imagery.',
	),
);

$asset_ids = array();
foreach ( $assets as $asset ) {
	$asset_ids[ $asset['key'] ] = shemo_stage22_asset_id( $asset );
}

$GLOBALS['shemo_stage22_terms'] = array(
	'service'        => array(
		'video'     => shemo_stage22_term_pair( 'service', 'مونتاج وموشن', 'service-video-motion-ar', 'Video Editing & Motion', 'service-video-motion-en' ),
		'design'    => shemo_stage22_term_pair( 'service', 'تصميم جرافيك', 'service-graphic-design-ar', 'Graphic Design', 'service-graphic-design-en' ),
		'sketch'    => shemo_stage22_term_pair( 'service', 'اسكتش وإليستريشن', 'service-sketch-illustration-ar', 'Sketch & Illustration', 'service-sketch-illustration-en' ),
		'story'     => shemo_stage22_term_pair( 'service', 'Storyboard وتخطيط إبداعي', 'service-storyboard-ar', 'Storyboarding & Creative Planning', 'service-storyboard-en' ),
		'branding'  => shemo_stage22_term_pair( 'service', 'هوية واتجاه بصري', 'service-branding-ar', 'Branding', 'service-branding-en' ),
		'direction' => shemo_stage22_term_pair( 'service', 'إدارة إبداعية مخصصة', 'service-creative-direction-ar', 'Creative Direction / Custom', 'service-creative-direction-en' ),
	),
	'project_type'   => array(
		'showcase' => shemo_stage22_term_pair( 'project_type', 'عرض خدمة تجريبي', 'project-type-demo-showcase-ar', 'Concept service showcase', 'project-type-demo-showcase-en' ),
		'study'    => shemo_stage22_term_pair( 'project_type', 'دراسة إبداعية داخلية', 'project-type-internal-study-ar', 'Internal creative study', 'project-type-internal-study-en' ),
		'system'   => shemo_stage22_term_pair( 'project_type', 'نظام بصري تجريبي', 'project-type-visual-system-ar', 'Demo visual system', 'project-type-visual-system-en' ),
	),
	'industry'       => array(
		'food'      => shemo_stage22_term_pair( 'industry', 'قطاع أطعمة ومشروبات تخيلي', 'industry-food-concept-ar', 'Food & beverage concept', 'industry-food-concept-en' ),
		'education' => shemo_stage22_term_pair( 'industry', 'تعليم ومهارات تخيلي', 'industry-education-concept-ar', 'Education concept', 'industry-education-concept-en' ),
		'creator'   => shemo_stage22_term_pair( 'industry', 'صنّاع محتوى تخيلي', 'industry-creator-concept-ar', 'Creator concept', 'industry-creator-concept-en' ),
		'culture'   => shemo_stage22_term_pair( 'industry', 'ثقافة وفعاليات تخيلية', 'industry-culture-concept-ar', 'Culture & events concept', 'industry-culture-concept-en' ),
		'startup'   => shemo_stage22_term_pair( 'industry', 'منتج ناشئ تخيلي', 'industry-startup-concept-ar', 'Startup product concept', 'industry-startup-concept-en' ),
	),
	'platform'       => array(
		'instagram' => shemo_stage22_term_pair( 'platform', 'Instagram', 'platform-instagram-ar', 'Instagram', 'platform-instagram-en' ),
		'youtube'   => shemo_stage22_term_pair( 'platform', 'YouTube', 'platform-youtube-ar', 'YouTube', 'platform-youtube-en' ),
		'web'       => shemo_stage22_term_pair( 'platform', 'Web', 'platform-web-ar', 'Web', 'platform-web-en' ),
		'print'     => shemo_stage22_term_pair( 'platform', 'طباعة وعرض', 'platform-print-display-ar', 'Print & display', 'platform-print-display-en' ),
	),
	'tool'           => array(
		'figma'     => shemo_stage22_term_pair( 'tool', 'Figma', 'tool-figma-ar', 'Figma', 'tool-figma-en' ),
		'premiere'  => shemo_stage22_term_pair( 'tool', 'Premiere Pro', 'tool-premiere-ar', 'Premiere Pro', 'tool-premiere-en' ),
		'ae'        => shemo_stage22_term_pair( 'tool', 'After Effects', 'tool-after-effects-ar', 'After Effects', 'tool-after-effects-en' ),
		'procreate' => shemo_stage22_term_pair( 'tool', 'Procreate', 'tool-procreate-ar', 'Procreate', 'tool-procreate-en' ),
		'notes'     => shemo_stage22_term_pair( 'tool', 'Sketch notes', 'tool-sketch-notes-ar', 'Sketch notes', 'tool-sketch-notes-en' ),
	),
	'content_format' => array(
		'film'       => shemo_stage22_term_pair( 'content_format', 'فيلم إطلاق قصير', 'format-launch-film-ar', 'Short launch film', 'format-launch-film-en' ),
		'social'     => shemo_stage22_term_pair( 'content_format', 'حزمة سوشيال', 'format-social-kit-ar', 'Social content kit', 'format-social-kit-en' ),
		'storyboard' => shemo_stage22_term_pair( 'content_format', 'Storyboard', 'format-storyboard-ar', 'Storyboard', 'format-storyboard-en' ),
		'identity'   => shemo_stage22_term_pair( 'content_format', 'نظام هوية مصغر', 'format-identity-system-ar', 'Mini identity system', 'format-identity-system-en' ),
		'guide'      => shemo_stage22_term_pair( 'content_format', 'دليل اتجاه إبداعي', 'format-direction-guide-ar', 'Creative direction guide', 'format-direction-guide-en' ),
	),
	'client_type'    => array(
		'demo' => shemo_stage22_term_pair( 'client_type', 'مشروع تجريبي / Concept', 'client-type-demo-concept-ar', 'Demo / Concept Project', 'client-type-demo-concept-en' ),
	),
	'visual_style'   => array(
		'noir'      => shemo_stage22_term_pair( 'visual_style', 'Cinematic Noir', 'visual-cinematic-noir-ar', 'Cinematic Noir', 'visual-cinematic-noir-en' ),
		'editorial' => shemo_stage22_term_pair( 'visual_style', 'Editorial بسيط', 'visual-editorial-ar', 'Minimal editorial', 'visual-editorial-en' ),
		'sketch'    => shemo_stage22_term_pair( 'visual_style', 'خطوط مرسومة', 'visual-sketch-lines-ar', 'Hand-drawn lines', 'visual-sketch-lines-en' ),
		'warm'      => shemo_stage22_term_pair( 'visual_style', 'دفء بصري هادئ', 'visual-warm-quiet-ar', 'Warm quiet direction', 'visual-warm-quiet-en' ),
	),
);

$projects = array(
	array(
		'key'      => 'frame-pulse',
		'order'    => 80,
		'featured' => true,
		'assets'   => array( 'cover' => 'video-editor-color', 'sketch' => 'storyboard-close', 'before' => 'whiteboard-planning', 'after' => 'video-editor-color', 'gallery' => array( 'video-editor-color', 'video-workspace-bw', 'whiteboard-planning' ) ),
		'terms'    => array( 'service' => array( 'video', 'story' ), 'project_type' => array( 'showcase' ), 'industry' => array( 'startup' ), 'platform' => array( 'instagram', 'youtube' ), 'tool' => array( 'premiere', 'ae', 'figma' ), 'content_format' => array( 'film' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'noir' ) ),
		'ar'       => array(
			'slug'         => 'frame-pulse-launch-film',
			'title'        => 'Frame Pulse - فيلم إطلاق تجريبي',
			'summary'      => 'تصور لفيلم إطلاق قصير يشرح فكرة منتج تخيلي بإيقاع واضح، من لقطة أولى إلى CTA نهائي.',
			'goal'         => 'تحويل فكرة منتج عامة إلى تسلسل فيديو قصير يصلح للسوشيال والويب بدون استخدام لقطات عميل.',
			'challenge'    => 'المشروع بدأ من فكرة بلا مادة خام حقيقية. المطلوب كان بناء إيقاع، عنوان، وانتقال بصري من الصفر مع وسمه كـDemo.',
			'direction'    => 'Cinematic Noir، كادرات داكنة، طبقات نص قصيرة، ولمسات Ember تقود العين من المشكلة إلى اللقطة النهائية.',
			'deliverables' => array( 'Storyboard من 6 لقطات.', 'مسار مونتاج لفيديو 20-30 ثانية.', 'إطارات عنوان وCTA.', 'قائمة export للسوشيال والويب.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'تسلسل مفهوم قابل للإنتاج، بدون أي نتائج سوقية مدّعاة.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Concept / Direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>بدأ المشروع من سؤال بسيط: كيف نشرح قيمة منتج جديد في أقل من نصف دقيقة؟ تم تقسيم الرسالة إلى افتتاح، مشكلة، تحول، وإغلاق.</p><p>بعد ذلك تم رسم تسلسل سريع، ثم اختبار أين تحتاج اللقطة حركة وأين يكفي تثبيت الإطار حتى تظل الرسالة واضحة.</p>',
		),
		'en'       => array(
			'slug'         => 'frame-pulse-launch-film-en',
			'title'        => 'Frame Pulse - Demo Launch Film',
			'summary'      => 'A short launch-film concept for an imaginary product, shaped from first frame to final CTA with clear rhythm.',
			'goal'         => 'Turn a broad product idea into a compact social and web video sequence without using client footage.',
			'challenge'    => 'The project started with no real source footage. The task was to build rhythm, headline, and visual transition from scratch while labeling it as demo work.',
			'direction'    => 'Cinematic Noir, dark frames, compact text layers, and Ember accents guiding the eye from problem to final frame.',
			'deliverables' => array( '6-frame storyboard.', '20-30 second edit route.', 'Title and CTA frames.', 'Export list for social and web.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A clear producible sequence, with no claimed market results.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Concept / Direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>The project started with one question: how can a new product feel clear in under half a minute? The message was split into opening, problem, turn, and close.</p><p>Then a rough sequence tested where motion is useful and where a still frame keeps the message stronger.</p>',
		),
	),
	array(
		'key'      => 'ember-menu',
		'order'    => 70,
		'featured' => true,
		'assets'   => array( 'cover' => 'brand-strategy', 'sketch' => 'brand-strategy-wide', 'before' => 'interior-swatches', 'after' => 'brand-strategy', 'gallery' => array( 'brand-strategy', 'brand-strategy-wide', 'interior-swatches' ) ),
		'terms'    => array( 'service' => array( 'design', 'branding' ), 'project_type' => array( 'showcase' ), 'industry' => array( 'food' ), 'platform' => array( 'instagram' ), 'tool' => array( 'figma' ), 'content_format' => array( 'social' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'editorial', 'warm' ) ),
		'ar'       => array(
			'slug'         => 'ember-menu-social-kit',
			'title'        => 'Ember Menu - حزمة سوشيال تجريبية',
			'summary'      => 'حزمة تصميمات سوشيال لعلامة طعام تخيلية، تركّز على ترتيب الرسالة والمنتج والعرض داخل نظام بصري واحد.',
			'goal'         => 'بناء مثال لحملة صغيرة متماسكة عبر بوستات متعددة بدون ادعاء عميل أو نتائج بيع.',
			'challenge'    => 'كل قطعة يجب أن تعمل منفردة وتبقى جزءًا من نظام واحد في اللون، التكوين، ونبرة العنوان.',
			'direction'    => 'Editorial بسيط، مساحات تنفس واضحة، وتباين دافئ يجعل المنتج متخيلًا لكنه قابل للتطبيق.',
			'deliverables' => array( '3 layouts للسوشيال.', 'نظام عناوين قصير.', 'اقتراح ألوان وaccent.', 'نسخة motion مقترحة لقطعة واحدة.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'نظام منشورات واضح لحملة تخيلية، بدون أرقام أداء حقيقية.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Design system concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels licensed sources' ) ),
			'process'      => '<p>تم التعامل مع الحزمة كإطلاق مصغر: إعلان منتج، عرض قصير، وتذكير بصري. لكل قطعة وظيفة واضحة داخل الرحلة.</p><p>تم بناء hierarchy ثابت للصورة والعنوان والـCTA، ثم تعديل المساحات حتى تبدو الحملة متماسكة لا مزدحمة.</p>',
		),
		'en'       => array(
			'slug'         => 'ember-menu-social-kit-en',
			'title'        => 'Ember Menu - Demo Social Kit',
			'summary'      => 'A social design kit for an imaginary food brand, focused on message, product, and offer hierarchy inside one visual system.',
			'goal'         => 'Build a compact campaign example that feels consistent across several posts without claiming a client or sales results.',
			'challenge'    => 'Each piece needed to work alone while still belonging to one system of color, composition, and headline tone.',
			'direction'    => 'Minimal editorial layouts, clear breathing room, and warm contrast that makes the imaginary product feel applicable.',
			'deliverables' => array( '3 social layouts.', 'Short headline system.', 'Color and accent proposal.', 'Motion suggestion for one asset.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A clear social system for an imaginary campaign, with no real performance numbers.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Design system concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels licensed sources' ) ),
			'process'      => '<p>The kit was treated as a small launch: product announcement, short offer, and visual reminder. Each piece has a clear role in the journey.</p><p>A fixed hierarchy for image, headline, and CTA was tested, then spacing was refined to keep the campaign consistent rather than crowded.</p>',
		),
	),
	array(
		'key'      => 'line-course',
		'order'    => 60,
		'featured' => true,
		'assets'   => array( 'cover' => 'storyboard-close', 'sketch' => 'storyboard-close', 'before' => 'storyboard-alt', 'after' => 'whiteboard-planning', 'gallery' => array( 'storyboard-close', 'storyboard-alt', 'whiteboard-planning' ) ),
		'terms'    => array( 'service' => array( 'story', 'sketch' ), 'project_type' => array( 'study' ), 'industry' => array( 'education' ), 'platform' => array( 'web', 'youtube' ), 'tool' => array( 'procreate', 'figma', 'notes' ), 'content_format' => array( 'storyboard' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'sketch' ) ),
		'ar'       => array(
			'slug'         => 'line-course-storyboard-study',
			'title'        => 'Line Course - دراسة Storyboard تجريبية',
			'summary'      => 'دراسة storyboard لكورس تعليمي تخيلي، توضّح كيف تتحول فكرة مجردة إلى مشاهد قابلة للإنتاج.',
			'goal'         => 'إظهار قيمة التخطيط البصري قبل إنتاج فيديو تعليمي أو إعلان قصير.',
			'challenge'    => 'الفكرة التعليمية قد تصبح جافة إذا عُرضت كنص مباشر فقط، لذلك احتاجت إلى مشاهد تقود الانتباه خطوة بخطوة.',
			'direction'    => 'خطوط مرسومة، ملاحظات حركة بسيطة، وتسلسل ينتقل من سؤال إلى مثال ثم خلاصة.',
			'deliverables' => array( 'Storyboard من 8 مشاهد.', 'ملاحظات حركة لكل مشهد.', 'اسكتشات أولية.', 'اقتراح تعليق صوتي مختصر.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'خريطة إنتاج مفهومة لفيديو تعليمي تخيلي.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Storyboard concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>بدأت الدراسة بتحديد نقطة الحيرة عند المتعلم، ثم تحويلها إلى سؤال بصري يظهر في أول مشهد.</p><p>تم تقسيم الشرح إلى وحدات صغيرة: مقدمة، مثال، تحول، خلاصة. كل وحدة حصلت على sketch سريع وملاحظة حركة.</p>',
		),
		'en'       => array(
			'slug'         => 'line-course-storyboard-study-en',
			'title'        => 'Line Course - Demo Storyboard Study',
			'summary'      => 'A storyboard study for an imaginary learning product, showing how an abstract idea becomes producible scenes.',
			'goal'         => 'Show the value of visual planning before producing an educational video or short ad.',
			'challenge'    => 'The learning idea could feel dry if presented as direct text only, so it needed scenes that guide attention step by step.',
			'direction'    => 'Hand-drawn lines, simple motion notes, and a sequence that moves from question to example to takeaway.',
			'deliverables' => array( '8-scene storyboard.', 'Motion notes per scene.', 'Initial sketches.', 'Short voiceover suggestion.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A clear production map for an imaginary learning video.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Storyboard concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>The study started by naming the learner’s confusion point, then turning it into a visual question in the first frame.</p><p>The explanation was split into small units: setup, example, turn, takeaway. Each unit received a quick sketch and motion note.</p>',
		),
	),
	array(
		'key'      => 'ink-signal',
		'order'    => 50,
		'featured' => true,
		'assets'   => array( 'cover' => 'tablet-sketch', 'sketch' => 'tablet-sketch', 'before' => 'storyboard-alt', 'after' => 'brand-strategy-wide', 'gallery' => array( 'tablet-sketch', 'storyboard-close', 'brand-strategy-wide' ) ),
		'terms'    => array( 'service' => array( 'sketch', 'branding' ), 'project_type' => array( 'study' ), 'industry' => array( 'creator' ), 'platform' => array( 'instagram', 'web' ), 'tool' => array( 'procreate', 'figma' ), 'content_format' => array( 'identity', 'social' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'sketch', 'editorial' ) ),
		'ar'       => array(
			'slug'         => 'ink-signal-illustration-system',
			'title'        => 'Ink Signal - نظام Illustration تجريبي',
			'summary'      => 'نظام إليستريشن بسيط لشخصية/رمز تخيلي، يوضح كيف يصبح الاسكتش أصلًا بصريًا قابلًا للاستخدام.',
			'goal'         => 'تحويل اسكتش أولي إلى لغة خطوط تصلح لأيقونات، بوستات، ولقطات موشن قصيرة.',
			'challenge'    => 'الاسكتش وحده جميل لكنه غير كافٍ كنظام. كان لازم تحديد سماكة الخط، الإيقاع، ومتى يظهر اللون.',
			'direction'    => 'خطوط يدوية نظيفة، مساحات واسعة، ولمسة Ember صغيرة تتحرك كإشارة.',
			'deliverables' => array( '3 رموز line-art.', 'قواعد استخدام الخط.', 'مثال بوست واحد.', 'اقتراح حركة قصيرة.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'أسلوب illustration قابل للتوسيع، بدون استخدام شخصية أو عميل حقيقي.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Illustration direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>تم اختيار شكل أولي بسيط ثم اختباره في ثلاثة أحجام: أيقونة صغيرة، إطار سوشيال، ولقطة موشن.</p><p>الهدف كان ألا يبدو الرسم كقطعة منفصلة، بل كجزء من نظام يمكن تكراره وتعديله.</p>',
		),
		'en'       => array(
			'slug'         => 'ink-signal-illustration-system-en',
			'title'        => 'Ink Signal - Demo Illustration System',
			'summary'      => 'A simple illustration system for an imaginary character/mark, showing how a sketch becomes usable visual language.',
			'goal'         => 'Turn an initial sketch into a line language for icons, posts, and short motion frames.',
			'challenge'    => 'A sketch can be beautiful but still fail as a system. Line weight, rhythm, and color moments needed rules.',
			'direction'    => 'Clean hand-drawn lines, generous space, and a small Ember signal that can move.',
			'deliverables' => array( '3 line-art marks.', 'Line-use rules.', 'One post example.', 'Short motion suggestion.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'An expandable illustration style, without using a real character or client.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Illustration direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>A simple starting shape was tested at three sizes: small icon, social frame, and motion frame.</p><p>The goal was to make the drawing feel like a repeatable system, not a one-off piece.</p>',
		),
	),
	array(
		'key'      => 'quiet-launch',
		'order'    => 40,
		'featured' => true,
		'assets'   => array( 'cover' => 'brand-strategy-wide', 'sketch' => 'interior-swatches', 'before' => 'material-board', 'after' => 'brand-strategy-wide', 'gallery' => array( 'brand-strategy-wide', 'brand-strategy', 'material-board' ) ),
		'terms'    => array( 'service' => array( 'branding', 'design' ), 'project_type' => array( 'system' ), 'industry' => array( 'startup' ), 'platform' => array( 'web', 'instagram' ), 'tool' => array( 'figma' ), 'content_format' => array( 'identity' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'warm', 'editorial' ) ),
		'ar'       => array(
			'slug'         => 'quiet-launch-brand-system',
			'title'        => 'Quiet Launch - نظام هوية تجريبي',
			'summary'      => 'نظام هوية مصغر لمنتج ناشئ تخيلي، يوازن بين الوضوح والهدوء بدل الصخب البصري.',
			'goal'         => 'تجهيز أساس هوية يمكن الانطلاق منه: ألوان، typography usage، ونماذج تطبيق.',
			'challenge'    => 'المطلوب بناء ثقة مبكرة من غير ادعاء تاريخ أو نجاحات غير موجودة.',
			'direction'    => 'لوحات هادئة، contrast واضح، وقواعد تطبيق عملية على الويب والسوشيال.',
			'deliverables' => array( 'Moodboard.', 'نظام ألوان أولي.', 'قواعد typography.', '3 تطبيقات مصغرة.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'نواة هوية قابلة للمراجعة، لا تمثل عميلًا حقيقيًا.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Brand system concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels licensed sources' ) ),
			'process'      => '<p>بدأ النظام بتحديد شعور البراند: هادئ، واضح، غير متكلف. ثم ترجم الشعور إلى اختيارات ألوان وتكوينات.</p><p>تم اختبار الهوية على hero صغير، بوست، وبطاقة تعريف للتأكد أنها تعمل خارج moodboard.</p>',
		),
		'en'       => array(
			'slug'         => 'quiet-launch-brand-system-en',
			'title'        => 'Quiet Launch - Demo Brand System',
			'summary'      => 'A mini identity system for an imaginary startup product, balancing clarity and calm instead of visual noise.',
			'goal'         => 'Prepare an identity base: colors, typography usage, and application samples.',
			'challenge'    => 'The work needed to build early trust without claiming history or achievements that do not exist.',
			'direction'    => 'Quiet boards, clear contrast, and practical rules for web and social usage.',
			'deliverables' => array( 'Moodboard.', 'Initial color system.', 'Typography rules.', '3 small applications.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A reviewable identity base, not a real client identity.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Brand system concept', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels licensed sources' ) ),
			'process'      => '<p>The system started by naming the brand feeling: calm, clear, and not overbuilt. That feeling was translated into color and composition choices.</p><p>The identity was tested on a small hero, a post, and a profile card to make sure it works beyond the moodboard.</p>',
		),
	),
	array(
		'key'      => 'studio-north',
		'order'    => 30,
		'featured' => false,
		'assets'   => array( 'cover' => 'material-board', 'sketch' => 'interior-swatches', 'before' => 'whiteboard-planning', 'after' => 'material-board', 'gallery' => array( 'material-board', 'interior-swatches', 'whiteboard-planning' ) ),
		'terms'    => array( 'service' => array( 'direction', 'branding' ), 'project_type' => array( 'study' ), 'industry' => array( 'culture' ), 'platform' => array( 'print', 'web' ), 'tool' => array( 'figma', 'notes' ), 'content_format' => array( 'guide' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'warm', 'noir' ) ),
		'ar'       => array(
			'slug'         => 'studio-north-creative-direction',
			'title'        => 'Studio North - اتجاه إبداعي تجريبي',
			'summary'      => 'دليل اتجاه إبداعي لمعرض/فعالية تخيلية، يربط المزاج البصري بالتطبيقات العملية.',
			'goal'         => 'توضيح كيف يساعد Creative Direction على اتخاذ قرارات شكلية قبل الإنتاج.',
			'challenge'    => 'وجود صور جميلة لا يكفي. كان المطلوب تحويل المزاج إلى قواعد اختيار وتطبيق.',
			'direction'    => 'مواد ملموسة، درجات هادئة، وتكوينات يمكن ترجمتها إلى poster وlanding section.',
			'deliverables' => array( 'Moodboard منظم.', 'قواعد اختيار الصور.', 'اتجاه poster.', 'إرشادات تطبيق للويب.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'دليل اتجاه يساعد على الإنتاج، بدون فعالية أو عميل حقيقي.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Creative direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>تمت قراءة المزاج المطلوب كمساحة هادئة ومنظمة، ثم اختيار مواد وصور تقود نفس الإحساس.</p><p>بعد ذلك تحولت اللوحة إلى قرارات: ما نوع الصورة؟ أين تظهر المساحة؟ ما الحدود بين الهدوء والبرود؟</p>',
		),
		'en'       => array(
			'slug'         => 'studio-north-creative-direction-en',
			'title'        => 'Studio North - Demo Creative Direction',
			'summary'      => 'A creative direction guide for an imaginary exhibition/event, connecting visual mood to practical applications.',
			'goal'         => 'Show how creative direction helps make visual decisions before production starts.',
			'challenge'    => 'Beautiful references are not enough. The mood needed to become selection and application rules.',
			'direction'    => 'Tactile materials, quiet tones, and compositions that can translate into a poster and landing section.',
			'deliverables' => array( 'Organized moodboard.', 'Image selection rules.', 'Poster direction.', 'Web application guidance.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A production-ready direction guide, without a real event or client.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Creative direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>The desired mood was read as calm and organized, then materials and images were selected to carry that feeling.</p><p>The board then became decisions: what kind of image, where space should appear, and where calm becomes too cold.</p>',
		),
	),
	array(
		'key'      => 'reel-clinic',
		'order'    => 20,
		'featured' => false,
		'assets'   => array( 'cover' => 'video-workspace-bw', 'sketch' => 'whiteboard-planning', 'before' => 'video-workspace-bw', 'after' => 'video-editor-color', 'gallery' => array( 'video-workspace-bw', 'video-editor-color', 'whiteboard-planning' ) ),
		'terms'    => array( 'service' => array( 'video' ), 'project_type' => array( 'study' ), 'industry' => array( 'creator' ), 'platform' => array( 'youtube', 'instagram' ), 'tool' => array( 'premiere', 'ae' ), 'content_format' => array( 'film', 'social' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'noir' ) ),
		'ar'       => array(
			'slug'         => 'reel-clinic-editing-study',
			'title'        => 'Reel Clinic - دراسة مونتاج تجريبية',
			'summary'      => 'دراسة قبل/بعد تجريبية لفيديو قصير، تركز على الإيقاع، حذف الزوائد، وبناء افتتاح أقوى.',
			'goal'         => 'إظهار طريقة تفكير Shemo Studio في تحسين فيديو قصير بدون ادعاء أنه فيديو عميل.',
			'challenge'    => 'النسخة الأولى المتخيلة بطيئة ومشتتة. المطلوب بناء مسار قطع أوضح وافتتاح أقصر.',
			'direction'    => 'مونتاج قائم على النَفَس: cut سريع عند الحاجة، توقف قصير عند الرسالة، ونهاية واضحة.',
			'deliverables' => array( 'تشخيص rhythm.', 'خطة cutdown.', 'اقتراح lower thirds.', 'نسخة publish checklist.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'خطة تحسين مونتاج قابلة للتطبيق، بدون قياس retention حقيقي.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Edit direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>تم تخيل نسخة أولى بطيئة، ثم تفكيكها إلى لحظات: أين يبدأ الانتباه؟ أين يهبط؟ وأين يجب أن يصل المشاهد؟</p><p>من هنا خرجت خطة cutdown تحافظ على الرسالة وتزيل التكرار.</p>',
		),
		'en'       => array(
			'slug'         => 'reel-clinic-editing-study-en',
			'title'        => 'Reel Clinic - Demo Editing Study',
			'summary'      => 'A demo before/after study for a short video, focused on rhythm, trimming, and a stronger opening.',
			'goal'         => 'Show Shemo Studio’s editing thinking without claiming this is a client video.',
			'challenge'    => 'The imagined first cut was slow and scattered. It needed a clearer cut path and shorter opening.',
			'direction'    => 'Breath-led editing: fast cuts where useful, short pauses around the message, and a clear ending.',
			'deliverables' => array( 'Rhythm diagnosis.', 'Cutdown plan.', 'Lower-third suggestion.', 'Publish checklist.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'A practical edit-improvement plan, without real retention metrics.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Edit direction', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>An intentionally slow first cut was imagined, then broken into attention moments: where it starts, where it drops, and where the viewer should land.</p><p>That produced a cutdown plan that keeps the message and removes repetition.</p>',
		),
	),
	array(
		'key'      => 'scene-map',
		'order'    => 10,
		'featured' => false,
		'assets'   => array( 'cover' => 'whiteboard-planning', 'sketch' => 'whiteboard-planning', 'before' => 'storyboard-alt', 'after' => 'brand-strategy', 'gallery' => array( 'whiteboard-planning', 'storyboard-alt', 'brand-strategy' ) ),
		'terms'    => array( 'service' => array( 'story', 'direction', 'design' ), 'project_type' => array( 'system' ), 'industry' => array( 'culture' ), 'platform' => array( 'instagram', 'web', 'print' ), 'tool' => array( 'figma', 'notes' ), 'content_format' => array( 'storyboard', 'guide' ), 'client_type' => array( 'demo' ), 'visual_style' => array( 'sketch', 'editorial' ) ),
		'ar'       => array(
			'slug'         => 'scene-map-campaign-planning',
			'title'        => 'Scene Map - تخطيط حملة تجريبي',
			'summary'      => 'خريطة مشاهد لحملة تخيلية متعددة القنوات، تجمع storyboard وsocial direction وlanding section.',
			'goal'         => 'إظهار كيف يمكن ترتيب حملة صغيرة من الفكرة إلى التسليمات قبل التنفيذ.',
			'challenge'    => 'الحملة المتخيلة لها أكثر من قناة، والخطر أن تتشتت الرسالة بين بوست وفيديو وصفحة.',
			'direction'    => 'خريطة مشاهد واحدة تربط كل قناة بنفس الوعد البصري والنبرة.',
			'deliverables' => array( 'Campaign scene map.', 'قائمة تسليمات حسب القناة.', 'ملاحظات storyboard.', 'نظام CTA موحد.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'خطة حملة قابلة للتنفيذ، بدون إطلاق حقيقي أو أرقام أداء.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_AR_LABEL ) ),
			'credits'      => array( array( 'role' => 'Campaign planning', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>تمت كتابة وعد واحد للحملة، ثم توزيعه على ثلاث قنوات: فيديو قصير، بوستات، وقسم landing.</p><p>كل قناة حصلت على دور واضح حتى لا يكرر المحتوى نفسه، بل يدفع المتلقي للخطوة التالية.</p>',
		),
		'en'       => array(
			'slug'         => 'scene-map-campaign-planning-en',
			'title'        => 'Scene Map - Demo Campaign Planning',
			'summary'      => 'A scene map for an imaginary multi-channel campaign, combining storyboard, social direction, and landing section.',
			'goal'         => 'Show how a small campaign can be organized from idea to deliverables before production.',
			'challenge'    => 'The imaginary campaign has more than one channel, so the message could easily scatter between post, video, and page.',
			'direction'    => 'One scene map ties every channel back to the same visual promise and tone.',
			'deliverables' => array( 'Campaign scene map.', 'Channel deliverables list.', 'Storyboard notes.', 'Unified CTA system.' ),
			'results'      => array( array( 'metric' => 'Demo outcome', 'value' => 'An executable campaign plan, without a real launch or performance numbers.' ), array( 'metric' => 'Transparency', 'value' => SHEMO_STAGE22_EN_LABEL ) ),
			'credits'      => array( array( 'role' => 'Campaign planning', 'name' => 'Shemo Studio Demo' ), array( 'role' => 'Stock imagery', 'name' => 'Pexels / Unsplash licensed sources' ) ),
			'process'      => '<p>One campaign promise was written, then distributed across three channels: short video, posts, and landing section.</p><p>Each channel received a clear role so the content does not repeat itself but moves the viewer to the next step.</p>',
		),
	),
);

$built_projects = array();
foreach ( $projects as $project ) {
	$ar_id = shemo_stage22_upsert_project( $project, 'ar', $project['terms'], $asset_ids );
	$en_id = shemo_stage22_upsert_project( $project, 'en', $project['terms'], $asset_ids );
	pll_save_post_translations( array( 'ar' => $ar_id, 'en' => $en_id ) );
	$built_projects[ $project['key'] ] = array( 'ar' => $ar_id, 'en' => $en_id );
}

foreach ( $built_projects as $key => $ids ) {
	$related_ar = array();
	$related_en = array();
	foreach ( $built_projects as $related_key => $related_ids ) {
		if ( $related_key === $key ) {
			continue;
		}
		$related_ar[] = $related_ids['ar'];
		$related_en[] = $related_ids['en'];
		if ( count( $related_ar ) >= 3 ) {
			break;
		}
	}
	update_post_meta( $ids['ar'], 'shemo_related_projects', $related_ar );
	update_post_meta( $ids['en'], 'shemo_related_projects', $related_en );
}

$packages = array(
	array(
		'key' => 'sketch-sprint', 'order' => 10, 'price_from' => 8000, 'price_to' => 14000, 'revisions' => 1, 'featured' => false,
		'ar' => array( 'slug' => 'sketch-sprint-package', 'title' => 'Sketch Sprint', 'summary' => 'جلسة تصور سريعة لتحويل فكرة غامضة إلى اتجاه مرئي قابل للمراجعة.', 'price_note' => 'نطاق محلي تجريبي مبني على أعمال sketch/storyboard محدودة؛ السعر النهائي بعد brief.', 'scope' => array( 'Brief قصير.', '2-3 اتجاهات rough.', 'اتجاه واحد مصقول.', 'مراجعة واحدة.' ), 'best_for' => 'اسكتشات أولية، storyboard صغير، أو اتجاه حملة محدود.', 'timeline' => '3-5 أيام عمل', 'content' => 'هذه باقة Demo/Trial قابلة للتعديل، وليست عرضًا تجاريًا نهائيًا.' ),
		'en' => array( 'slug' => 'sketch-sprint-package-en', 'title' => 'Sketch Sprint', 'summary' => 'A fast concept session that turns a vague idea into a reviewable visual direction.', 'price_note' => 'Local demo range based on limited sketch/storyboard scope; final quote follows the brief.', 'scope' => array( 'Short brief.', '2-3 rough directions.', 'One refined direction.', 'One revision.' ), 'best_for' => 'Early sketches, compact storyboard, or a small campaign direction.', 'timeline' => '3-5 business days', 'content' => 'This is editable Demo/Trial package data, not final commercial approval.' ),
	),
	array(
		'key' => 'motion-cut', 'order' => 20, 'price_from' => 12000, 'price_to' => 24000, 'revisions' => 2, 'featured' => true,
		'ar' => array( 'slug' => 'motion-cut-package', 'title' => 'Motion Cut', 'summary' => 'مونتاج وموشن قصير لقطعة إطلاق أو إعلان اجتماعي واضح.', 'price_note' => 'نطاق مبني على متوسط ساعات مونتاج/موشن عالمية مع تكييف للسوق المحلي.', 'scope' => array( 'Scope مكتوب.', 'مونتاج قصير أو motion sequence.', 'إعداد export للسوشيال.', 'مراجعتان.' ), 'best_for' => 'Reels، إعلان خدمة، فيديو launch مختصر.', 'timeline' => '5-8 أيام عمل', 'content' => 'نطاق تجريبي قابل للتعديل حسب طول الفيديو وحجم المواد الخام.' ),
		'en' => array( 'slug' => 'motion-cut-package-en', 'title' => 'Motion Cut', 'summary' => 'Short editing and motion for a clear launch asset or social ad.', 'price_note' => 'Range informed by global video/motion hourly benchmarks and adapted to a local starter scope.', 'scope' => array( 'Written scope.', 'Short edit or motion sequence.', 'Social export setup.', 'Two revisions.' ), 'best_for' => 'Reels, service ads, compact launch videos.', 'timeline' => '5-8 business days', 'content' => 'Demo scope, editable depending on video length and source material.' ),
	),
	array(
		'key' => 'social-design-kit', 'order' => 30, 'price_from' => 10000, 'price_to' => 18000, 'revisions' => 2, 'featured' => false,
		'ar' => array( 'slug' => 'social-design-kit-package', 'title' => 'Social Design Kit', 'summary' => 'حزمة تصميمات سوشيال صغيرة بنظام بصري واضح وقابل للتكرار.', 'price_note' => 'مبنية على مراجع social graphics عالمية وأسعار تصميم محلية للمشاريع الصغيرة.', 'scope' => array( '3-6 تصميمات حسب النوع.', 'نظام عناوين.', 'ألوان وتكوينات.', 'ملفات نشر منظمة.' ), 'best_for' => 'حملة مصغرة أو إطلاق محتوى شهري محدود.', 'timeline' => '5-7 أيام عمل', 'content' => 'باقة تجريبية قابلة للتعديل حسب عدد القطع والمنصات.' ),
		'en' => array( 'slug' => 'social-design-kit-package-en', 'title' => 'Social Design Kit', 'summary' => 'A small social design kit with a clear repeatable visual system.', 'price_note' => 'Informed by global social graphics ranges and local small-project design pricing.', 'scope' => array( '3-6 assets depending on type.', 'Headline system.', 'Colors and layouts.', 'Organized publishing files.' ), 'best_for' => 'A compact campaign or limited monthly content launch.', 'timeline' => '5-7 business days', 'content' => 'Editable demo package depending on asset count and platforms.' ),
	),
	array(
		'key' => 'storyboard-illustration-study', 'order' => 40, 'price_from' => 9000, 'price_to' => 18000, 'revisions' => 2, 'featured' => false,
		'ar' => array( 'slug' => 'storyboard-illustration-study-package', 'title' => 'Storyboard / Illustration Study', 'summary' => 'خطة مشاهد ورسوم أولية لفكرة فيديو أو حملة تحتاج توضيح قبل الإنتاج.', 'price_note' => 'النطاق يأخذ في الاعتبار أسعار storyboard marketplace العالمية وحجم التنفيذ المحلي.', 'scope' => array( '6-10 مشاهد أو رموز.', 'ملاحظات حركة.', 'اتجاه illustration.', 'تسليم PDF/صور.' ), 'best_for' => 'فيديو تعليمي، إعلان قصير، أو شرح خدمة.', 'timeline' => '4-8 أيام عمل', 'content' => 'هذه ليست تكلفة نهائية؛ عدد المشاهد ومستوى التفصيل يغيران السعر.' ),
		'en' => array( 'slug' => 'storyboard-illustration-study-package-en', 'title' => 'Storyboard / Illustration Study', 'summary' => 'Scene planning and rough illustration for a video or campaign idea before production.', 'price_note' => 'Range considers global storyboard marketplace pricing and local execution depth.', 'scope' => array( '6-10 scenes or marks.', 'Motion notes.', 'Illustration direction.', 'PDF/image handoff.' ), 'best_for' => 'Learning videos, short ads, or service explainers.', 'timeline' => '4-8 business days', 'content' => 'Not a final quote; scene count and detail level change the price.' ),
	),
	array(
		'key' => 'launch-visual-system', 'order' => 50, 'price_from' => 25000, 'price_to' => 45000, 'revisions' => 3, 'featured' => true,
		'ar' => array( 'slug' => 'launch-visual-system-package', 'title' => 'Launch Visual System', 'summary' => 'نظام بصري أوسع يجمع direction، تصميمات، وموشن/Storyboard حسب احتياج الإطلاق.', 'price_note' => 'النطاق مستند إلى أسعار هوية مصرية منشورة ومراجع freelance identity محلية وعالمية.', 'scope' => array( 'Discovery مختصر.', 'Visual direction.', 'مجموعة تسليمات متعددة.', 'مراجعات مجدولة.', 'Handoff منظم.' ), 'best_for' => 'إطلاق خدمة/منتج أو تحديث حضور بصري كامل.', 'timeline' => '2-4 أسابيع', 'content' => 'باقة demo قابلة للتخصيص ولا تعتمد سعرًا نهائيًا قبل scope.' ),
		'en' => array( 'slug' => 'launch-visual-system-package-en', 'title' => 'Launch Visual System', 'summary' => 'A broader visual system combining direction, design, and motion/storyboard according to launch needs.', 'price_note' => 'Range informed by published Egypt identity pricing plus local/global freelance identity references.', 'scope' => array( 'Compact discovery.', 'Visual direction.', 'Multiple deliverables.', 'Scheduled revisions.', 'Organized handoff.' ), 'best_for' => 'Service/product launch or fuller visual refresh.', 'timeline' => '2-4 weeks', 'content' => 'Customizable demo package; no final price before scope.' ),
	),
);

$built_packages = array();
foreach ( $packages as $package ) {
	$ar_id = shemo_stage22_upsert_package( $package, 'ar' );
	$en_id = shemo_stage22_upsert_package( $package, 'en' );
	pll_save_post_translations( array( 'ar' => $ar_id, 'en' => $en_id ) );
	$built_packages[ $package['key'] ] = array( 'ar' => $ar_id, 'en' => $en_id );
}

$testimonials = array(
	array(
		'order' => 10, 'rating' => 5,
		'ar' => array( 'slug' => 'demo-testimonial-brief-clarity', 'title' => 'Demo Testimonial - وضوح البريف', 'summary' => 'تقييم تجريبي يصف تجربة مثالية متوقعة، وليس شهادة عميل.', 'quote' => 'كتقييم تجريبي، الفكرة هنا أن العميل يشعر أن البريف اتنظم بسرعة: ما الهدف؟ ما التسليمات؟ وما الخطوة التالية؟ هذا نص Concept وليس شهادة حقيقية.', 'author' => 'شخصية تجريبية: مؤسس منتج', 'role' => 'Demo persona - غير عميل حقيقي', 'service' => 'Creative Direction', 'note' => 'موسوم كـDemo لتوضيح تجربة مستهدفة فقط.' ),
		'en' => array( 'slug' => 'demo-testimonial-brief-clarity-en', 'title' => 'Demo Testimonial - Brief Clarity', 'summary' => 'Demo testimonial describing a desired experience, not a real client quote.', 'quote' => 'As a demo note, the intended experience is that the brief becomes clear quickly: what is the goal, what gets delivered, and what happens next. This is concept copy, not a real testimonial.', 'author' => 'Demo persona: Product founder', 'role' => 'Demo persona - not a real client', 'service' => 'Creative Direction', 'note' => 'Clearly labeled as Demo to describe a target experience only.' ),
	),
	array(
		'order' => 20, 'rating' => 5,
		'ar' => array( 'slug' => 'demo-testimonial-visual-system', 'title' => 'Demo Testimonial - نظام بصري', 'summary' => 'تقييم تجريبي عن وضوح النظام البصري.', 'quote' => 'النص التجريبي يتخيل عميلًا يحتاج لغة بصرية واحدة بدل قطع منفصلة. القيمة المقصودة هي أن كل تصميم يبدو من نفس العالم، بدون ادعاء مشروع حقيقي.', 'author' => 'شخصية تجريبية: مدير محتوى', 'role' => 'Demo persona - غير عميل حقيقي', 'service' => 'Graphic Design / Branding', 'note' => 'ليس تقييمًا من عميل؛ مجرد مثال على نوع الانطباع المطلوب.' ),
		'en' => array( 'slug' => 'demo-testimonial-visual-system-en', 'title' => 'Demo Testimonial - Visual System', 'summary' => 'Demo testimonial about visual-system clarity.', 'quote' => 'This demo copy imagines a client who needs one visual language instead of disconnected assets. The intended value is that every design feels from the same world, without claiming a real project.', 'author' => 'Demo persona: Content manager', 'role' => 'Demo persona - not a real client', 'service' => 'Graphic Design / Branding', 'note' => 'Not a client review; only an example of the desired impression.' ),
	),
	array(
		'order' => 30, 'rating' => 5,
		'ar' => array( 'slug' => 'demo-testimonial-editing-rhythm', 'title' => 'Demo Testimonial - إيقاع المونتاج', 'summary' => 'تقييم تجريبي عن تجربة مونتاج مفترضة.', 'quote' => 'هذا المثال يصف ما نريد أن يحدث في مشروع مونتاج حقيقي: الإيقاع يصبح أهدأ، الرسالة تظهر أسرع، والملفات تُسلّم منظمة. لا توجد نتيجة أداء حقيقية هنا.', 'author' => 'شخصية تجريبية: صانع محتوى', 'role' => 'Demo persona - غير عميل حقيقي', 'service' => 'Video Editing & Motion', 'note' => 'لا يدّعي مشاهدة أو retention أو نتيجة تجارية.' ),
		'en' => array( 'slug' => 'demo-testimonial-editing-rhythm-en', 'title' => 'Demo Testimonial - Editing Rhythm', 'summary' => 'Demo testimonial about an imagined editing experience.', 'quote' => 'This example describes what should happen in a real editing project: calmer rhythm, faster message clarity, and organized delivery files. No real performance outcome is claimed here.', 'author' => 'Demo persona: Content creator', 'role' => 'Demo persona - not a real client', 'service' => 'Video Editing & Motion', 'note' => 'No view count, retention, or commercial result is claimed.' ),
	),
);

foreach ( $testimonials as $testimonial ) {
	$ar_id = shemo_stage22_upsert_testimonial( $testimonial, 'ar' );
	$en_id = shemo_stage22_upsert_testimonial( $testimonial, 'en' );
	pll_save_post_translations( array( 'ar' => $ar_id, 'en' => $en_id ) );
}

$packages_ar_html = '<section class="shemo-section shemo-hero" aria-labelledby="packages-title"><div><p class="shemo-kicker">Packages</p><h1 id="packages-title">باقات تجريبية مبنية على بحث سوقي.</h1><p class="shemo-lead">الأرقام هنا Demo/Trial قابلة للتعديل من لوحة التحكم وليست اعتمادًا تجاريًا نهائيًا. تم تضييق النطاقات بمقارنة أسعار مصرية منشورة للهوية، ومراجع عالمية للفيديو والموشن والتصميم والـstoryboard.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/request-a-quote/">اطلب عرض سعر</a><a class="shemo-button shemo-button--secondary" href="/process/">طريقة العمل</a></div></div><div class="shemo-frame" aria-label="Package scope frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">market-informed / editable / not final</p></div></div></section><section class="shemo-section"><p class="shemo-kicker">Editable package CPT</p><h2>الباقات والأسعار من حقول قابلة للتعديل</h2>[shemo_packages]</section><section class="shemo-section shemo-split"><div><p class="shemo-kicker">Pricing research note</p><h2>كيف اتحدد النطاق؟</h2></div><div><p class="shemo-lead">المراجع شملت: أسعار هوية منشورة في مصر تبدأ تقريبًا من 20,700 إلى 31,100 جنيه، مراجع freelance محلية لهوية كاملة 15,000-60,000 جنيه، Upwork للفيديو 10-60 دولار/ساعة، motion 18-35 دولار/ساعة كمدخل marketplace، التصميم 15-35 دولار/ساعة، social graphics عالميًا 200-800 دولار، وFiverr للـstoryboard كنطاق marketplace منخفض. تم اختيار أرقام محلية كبداية بوتيك صغيرة، مع بقاء السعر النهائي مرتبطًا بالـbrief.</p></div></section>';

$packages_en_html = '<section class="shemo-section shemo-hero" aria-labelledby="packages-title"><div><p class="shemo-kicker">Packages</p><h1 id="packages-title">Demo packages informed by market research.</h1><p class="shemo-lead">These are editable Demo/Trial ranges, not final commercial approval. The ranges were narrowed using published Egypt identity pricing plus global references for video, motion, design, and storyboard work.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/request-a-quote-en/">Request a Quote</a><a class="shemo-button shemo-button--secondary" href="/en/process-en/">Process</a></div></div><div class="shemo-frame" aria-label="Package scope frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">market-informed / editable / not final</p></div></div></section><section class="shemo-section"><p class="shemo-kicker">Editable package CPT</p><h2>Packages and prices are field-driven</h2>[shemo_packages]</section><section class="shemo-section shemo-split"><div><p class="shemo-kicker">Pricing research note</p><h2>How the range was shaped</h2></div><div><p class="shemo-lead">References included published Egypt identity packages around EGP 20,700-31,100, local freelance identity references around EGP 15,000-60,000, Upwork video at USD 10-60/hr, motion at USD 18-35/hr, graphic design at USD 15-35/hr, global social graphics packages around USD 200-800, and Fiverr storyboard marketplace ranges. The selected numbers are local boutique starter ranges, with final pricing tied to the brief.</p></div></section>';

shemo_stage22_pair_pages(
	array( 'slug' => 'packages', 'title' => 'الباقات', 'description' => 'باقات Demo/Trial قابلة للتعديل مبنية على بحث سوقي وليست أسعارًا نهائية.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-packages-page', $packages_ar_html ) ),
	array( 'slug' => 'packages-en', 'title' => 'Packages', 'description' => 'Editable Demo/Trial package ranges informed by market research, not final pricing.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-packages-page', $packages_en_html ) )
);

$about_ar_html = '<section class="shemo-section shemo-hero" aria-labelledby="about-title"><div><p class="shemo-kicker">About</p><h1 id="about-title">استوديو صغير يقوده مؤسس واضح، لا واجهة مجهولة.</h1><p class="shemo-lead">Shemo Studio مبني على فكرة بسيطة: قبل أن يظهر التصميم أو الفيديو على الشاشة، لازم الفكرة تتشاف كاسكتش واضح. هنا يأتي دور شيمو كمؤسس وقائد إبداعي: يترجم البريف إلى اتجاه بصري، ثم يحوله إلى تسليمات قابلة للنشر والمراجعة.</p></div><div class="shemo-frame" aria-label="Founder-led studio frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">founder-led / hybrid studio / sketch to screen</p></div></div></section><section class="shemo-section shemo-split"><div><p class="shemo-kicker">Founder story</p><h2>القصة ليست عن أرقام كبيرة، بل عن طريقة عمل.</h2></div><div><p>بدأ اتجاه Shemo Studio من منطقة بين الرسم والتصميم والمونتاج: لحظة يكون فيها العميل لديه إحساس عام بما يريد، لكن الرسالة لم تتحول بعد إلى مشاهد أو نظام واضح. بدل القفز مباشرة إلى التنفيذ، يبدأ العمل بتفكيك الفكرة: ما الذي يجب أن يفهمه الجمهور؟ ما أول لقطة؟ ما الذي لا نحتاجه؟</p><p>وجود شيمو ظاهرًا في البراند مقصود. العميل يعرف من يقود التفكير، وفي نفس الوقت يظل الاستوديو قابلًا للنمو كشبكة تعاون حسب احتياج المشروع: تصميم، illustration، motion، أو creative direction. هذا هو معنى Hybrid studio هنا: قيادة شخصية واضحة، وتنفيذ منظم يمكن أن يكبر بدون فقدان النبرة.</p><p>إلى أن تتوفر أعمال عملاء حقيقية قابلة للنشر، يعرض الموقع مشاريع Demo/Concept موسومة بوضوح. الهدف ليس اختراع تاريخ وهمي، بل توضيح طريقة التفكير، مستوى العناية، ونوع الأسئلة التي يقود بها الاستوديو المشروع من الاسكتش إلى الشاشة.</p></div></section><section class="shemo-section shemo-grid--two"><div><p class="shemo-kicker">How we work</p><h2>وضوح قبل الجماليات.</h2><p>الجماليات مهمة، لكنها تأتي بعد وضوح الهدف. كل مشروع يبدأ بسؤال عملي ثم يتحول إلى moodboard أو storyboard أو layout قبل الإنتاج النهائي.</p></div><div><p class="shemo-kicker">What we avoid</p><h2>لا مبالغة ولا ادعاءات.</h2><p>لا نستخدم أسماء عملاء غير حقيقية، ولا نتائج تسويقية متخيلة، ولا شهادات كأنها واقعية. المحتوى التجريبي هنا معلّم كـDemo حتى يبقى الثقة مبنية على الصراحة.</p></div></section>';

$about_en_html = '<section class="shemo-section shemo-hero" aria-labelledby="about-title"><div><p class="shemo-kicker">About</p><h1 id="about-title">A small studio led by a visible founder, not a faceless front.</h1><p class="shemo-lead">Shemo Studio is built on a simple idea: before design or video reaches the screen, the idea needs to be seen clearly as a sketch. Shemo leads that translation from brief to visual direction, then into publish-ready deliverables.</p></div><div class="shemo-frame" aria-label="Founder-led studio frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">founder-led / hybrid studio / sketch to screen</p></div></div></section><section class="shemo-section shemo-split"><div><p class="shemo-kicker">Founder story</p><h2>The story is not about inflated numbers. It is about a way of working.</h2></div><div><p>The direction behind Shemo Studio grew from the space between drawing, design, and editing: the moment when a client has a general feeling for what they want, but the message has not yet become scenes or a clear system. Instead of jumping straight into production, the work starts by breaking the idea down: what should the audience understand, what is the first frame, and what can be removed?</p><p>Shemo being visible in the brand is intentional. The client knows who is leading the thinking, while the studio can still grow as a collaboration network depending on the project: design, illustration, motion, or creative direction. That is what hybrid studio means here: clear personal leadership and structured delivery that can scale without losing the voice.</p><p>Until publishable client work is available, the site uses clearly labeled Demo/Concept projects. The point is not to invent a history, but to show the thinking, care, and questions that move a project from sketch to screen.</p></div></section><section class="shemo-section shemo-grid--two"><div><p class="shemo-kicker">How we work</p><h2>Clarity before aesthetics.</h2><p>Aesthetics matter, but they come after the goal is clear. Each project starts with a practical question, then becomes a moodboard, storyboard, or layout before final production.</p></div><div><p class="shemo-kicker">What we avoid</p><h2>No hype and no invented proof.</h2><p>We do not use fake client names, imagined marketing results, or quotes presented as real testimonials. Demo content is labeled clearly so trust is built through honesty.</p></div></section>';

shemo_stage22_pair_pages(
	array( 'slug' => 'about', 'title' => 'عن Shemo Studio', 'description' => 'قصة مؤسس Shemo Studio كنموذج hybrid studio بقيادة مؤسس ظاهر.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-about-page', $about_ar_html ) ),
	array( 'slug' => 'about-en', 'title' => 'About', 'description' => 'The Shemo Studio founder-led hybrid studio story.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-about-page', $about_en_html ) )
);

$testimonials_ar_html = '<section class="shemo-section shemo-hero" aria-labelledby="testimonials-title"><div><p class="shemo-kicker">Testimonials</p><h1 id="testimonials-title">تقييمات تجريبية، لا شهادات عملاء.</h1><p class="shemo-lead">هذه النصوص Demo personas توضّح نوع التجربة التي نريد تقديمها في المشاريع الحقيقية. لا يوجد هنا عميل حقيقي أو نتيجة حقيقية.</p></div><div class="shemo-frame" aria-label="Demo testimonials frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">demo personas / clearly labeled</p></div></div></section><section class="shemo-section"><p class="shemo-kicker">Editable testimonial CPT</p><h2>كل تقييم قابل للتعديل من لوحة التحكم</h2>[shemo_testimonials]</section>';
$testimonials_en_html = '<section class="shemo-section shemo-hero" aria-labelledby="testimonials-title"><div><p class="shemo-kicker">Testimonials</p><h1 id="testimonials-title">Demo testimonials, not client reviews.</h1><p class="shemo-lead">These are demo personas describing the kind of experience the studio aims to provide in real projects. No real client or real result is implied.</p></div><div class="shemo-frame" aria-label="Demo testimonials frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">demo personas / clearly labeled</p></div></div></section><section class="shemo-section"><p class="shemo-kicker">Editable testimonial CPT</p><h2>Every testimonial is editable in the admin</h2>[shemo_testimonials]</section>';

shemo_stage22_pair_pages(
	array( 'slug' => 'testimonials', 'title' => 'الشهادات', 'description' => 'تقييمات تجريبية موسومة بوضوح وقابلة للتعديل، وليست شهادات عملاء حقيقية.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-testimonials-page', $testimonials_ar_html ) ),
	array( 'slug' => 'testimonials-en', 'title' => 'Testimonials', 'description' => 'Clearly labeled editable demo testimonials, not real client reviews.', 'content' => shemo_stage22_content_shell( 'shemo-stage22 shemo-testimonials-page', $testimonials_en_html ) )
);

$policy_pages = array(
	'terms' => array(
		'ar_title' => 'الشروط العامة',
		'en_title' => 'Terms',
		'ar_slug' => 'terms',
		'en_slug' => 'terms-en',
		'ar_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>الشروط العامة</h1><p class="shemo-lead">مسودة مراجعة قانونية: هذا النص تجريبي للتجهيز قبل الإطلاق، ويجب مراجعته قانونيًا قبل الاعتماد النهائي أو الاستخدام التجاري.</p></div></section><section class="shemo-section shemo-policy-list"><h2>نطاق الاتفاق</h2><p>يبدأ أي مشروع بعد قبول عرض سعر مكتوب يوضح التسليمات، المدة التقريبية، عدد المراجعات، وطريقة الدفع. أي طلب خارج النطاق المتفق عليه يُعامل كتوسيع نطاق ويحتاج موافقة مكتوبة قبل التنفيذ.</p><h2>مسؤوليات العميل</h2><p>يلتزم العميل بتوفير المواد، النصوص، الشعارات، المراجع، والموافقات في الوقت المناسب. أي تأخير في توفير المدخلات قد يغيّر جدول التسليم.</p><h2>حقوق الاستخدام</h2><p>بعد سداد كامل قيمة المشروع، يحصل العميل على حق استخدام التسليمات النهائية حسب النطاق المتفق عليه. ملفات العمل المفتوحة أو الأصول الوسيطة لا تُسلّم إلا إذا نص العرض على ذلك.</p><h2>المحتوى التجريبي</h2><p>مشاريع Demo/Concept المعروضة في الموقع ليست أعمال عملاء ولا يجوز اعتبارها وعدًا بنتائج تجارية.</p></section>',
		'en_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>Terms</h1><p class="shemo-lead">Legal review draft: this text is prepared for pre-launch content completeness and must be reviewed legally before final approval or commercial use.</p></div></section><section class="shemo-section shemo-policy-list"><h2>Agreement scope</h2><p>Work starts after a written quote is accepted. The quote defines deliverables, estimated timeline, revision count, and payment method. Any request outside the agreed scope is treated as scope expansion and requires written approval before execution.</p><h2>Client responsibilities</h2><p>The client provides materials, copy, logos, references, and approvals on time. Delays in inputs may change the delivery schedule.</p><h2>Usage rights</h2><p>After full payment, the client receives usage rights for final deliverables according to the agreed scope. Working files or intermediate assets are delivered only when the quote states so.</p><h2>Demo content</h2><p>Demo/Concept projects shown on the site are not client work and must not be treated as a promise of commercial results.</p></section>',
	),
	'revision-policy' => array(
		'ar_title' => 'سياسة المراجعات',
		'en_title' => 'Revision Policy',
		'ar_slug' => 'revision-policy',
		'en_slug' => 'revision-policy-en',
		'ar_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>سياسة المراجعات</h1><p class="shemo-lead">مسودة مراجعة قانونية: عدد المراجعات وحدودها يجب تثبيتها في عرض السعر النهائي.</p></div></section><section class="shemo-section shemo-policy-list"><h2>ما المقصود بالمراجعة؟</h2><p>المراجعة هي تعديل داخل الاتجاه والنطاق المتفق عليه، مثل ضبط نص، ترتيب لقطة، لون، أو تفصيلة تصميم. تغيير الفكرة الأساسية أو إضافة تسليمات جديدة ليس مراجعة، بل نطاق جديد.</p><h2>طريقة إرسال الملاحظات</h2><p>تُجمع الملاحظات في دفعة واحدة قدر الإمكان، عبر نقاط واضحة أو تعليقات على ملف. الملاحظات المتفرقة قد تطيل الجدول.</p><h2>متى تُحسب مراجعة إضافية؟</h2><p>إذا تم اعتماد اتجاه ثم طلب تغييره جذريًا، أو ظهرت مدخلات جديدة بعد مرحلة التنفيذ، قد تُحسب مراجعة أو تكلفة إضافية حسب التأثير.</p></section>',
		'en_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>Revision Policy</h1><p class="shemo-lead">Legal review draft: revision count and limits must be confirmed in the final quote.</p></div></section><section class="shemo-section shemo-policy-list"><h2>What counts as a revision?</h2><p>A revision is an adjustment within the agreed direction and scope, such as copy, shot order, color, or design detail. Changing the core idea or adding deliverables is not a revision; it is new scope.</p><h2>How feedback is sent</h2><p>Feedback should be grouped into one clear round whenever possible, using bullet points or comments on a file. Scattered feedback can extend the schedule.</p><h2>When extra revisions apply</h2><p>If a direction is approved and then changed substantially, or new inputs appear after production starts, an extra revision or cost may apply depending on impact.</p></section>',
	),
	'deposit-policy' => array(
		'ar_title' => 'سياسة العربون',
		'en_title' => 'Deposit Policy',
		'ar_slug' => 'deposit-policy',
		'en_slug' => 'deposit-policy-en',
		'ar_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>سياسة العربون</h1><p class="shemo-lead">مسودة مراجعة قانونية: نسبة 50% المستخدمة في صفحات Demo ليست اعتمادًا نهائيًا إلا بعد موافقة تجارية صريحة.</p></div></section><section class="shemo-section shemo-policy-list"><h2>الغرض من العربون</h2><p>العربون يحجز وقت التنفيذ ويغطي مرحلة الاكتشاف والتخطيط والبداية الإبداعية. لا يبدأ العمل قبل تأكيد الدفع أو أي ترتيب مكتوب بديل.</p><h2>النسبة وطريقة الدفع</h2><p>النسبة المقترحة في النسخة التجريبية هي 50% من قيمة المشروع، مع سداد الباقي قبل تسليم الملفات النهائية عالية الجودة. النسبة قابلة للتعديل في العرض النهائي.</p><h2>إلغاء المشروع بعد البداية</h2><p>إذا أُلغي المشروع بعد بدء العمل، يتم تقييم ما تم تنفيذه مقارنة بالعربون، وقد لا يكون العربون مستردًا بالكامل إذا تم تخصيص وقت أو إنتاج مواد فعلية.</p></section>',
		'en_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>Deposit Policy</h1><p class="shemo-lead">Legal review draft: the 50% deposit shown in demo pages is not final commercial approval unless explicitly approved later.</p></div></section><section class="shemo-section shemo-policy-list"><h2>Purpose of the deposit</h2><p>The deposit reserves production time and covers discovery, planning, and initial creative work. Work does not begin before payment confirmation or another written arrangement.</p><h2>Percentage and payment</h2><p>The demo suggestion is 50% of project value, with the remaining balance due before final high-resolution files are delivered. The percentage can be changed in the final quote.</p><h2>Cancellation after start</h2><p>If the project is cancelled after work begins, completed work is assessed against the deposit. The deposit may not be fully refundable if time or production work has already been allocated.</p></section>',
	),
	'refund-policy' => array(
		'ar_title' => 'سياسة الاسترجاع',
		'en_title' => 'Refund Policy',
		'ar_slug' => 'refund-policy',
		'en_slug' => 'refund-policy-en',
		'ar_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>سياسة الاسترجاع</h1><p class="shemo-lead">مسودة مراجعة قانونية: يجب مراجعة هذه السياسة قانونيًا وربطها بطريقة الدفع الفعلية قبل الإنتاج.</p></div></section><section class="shemo-section shemo-policy-list"><h2>قبل بدء العمل</h2><p>إذا لم يبدأ العمل ولم يتم حجز وقت إنتاج فعلي، يمكن مراجعة طلب الاسترداد حسب حالة الدفع والرسوم البنكية أو رسوم منصة الدفع.</p><h2>بعد بدء العمل</h2><p>بعد بدء الاكتشاف أو التصميم أو المونتاج، يتم احتساب الجزء المنفذ من المشروع. لا يتم رد قيمة العمل الذي تم تنفيذه أو الوقت المحجوز بالفعل.</p><h2>الملفات المسلمة</h2><p>بعد تسليم الملفات النهائية أو اعتمادها، لا يكون الاسترداد متاحًا عادةً إلا في حالة خطأ واضح غير قابل للإصلاح ضمن النطاق المتفق عليه.</p></section>',
		'en_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>Refund Policy</h1><p class="shemo-lead">Legal review draft: this policy must be legally reviewed and connected to the actual payment method before production.</p></div></section><section class="shemo-section shemo-policy-list"><h2>Before work starts</h2><p>If work has not started and production time has not been reserved, refund requests can be reviewed according to payment status and any bank or platform fees.</p><h2>After work starts</h2><p>After discovery, design, or editing starts, completed work is counted. Work already completed or time already reserved is not usually refundable.</p><h2>Delivered files</h2><p>After final files are delivered or approved, refunds are usually unavailable unless there is a clear issue that cannot be fixed within the agreed scope.</p></section>',
	),
	'delivery-policy' => array(
		'ar_title' => 'سياسة التسليم',
		'en_title' => 'Delivery Policy',
		'ar_slug' => 'delivery-policy',
		'en_slug' => 'delivery-policy-en',
		'ar_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>سياسة التسليم</h1><p class="shemo-lead">مسودة مراجعة قانونية: المدد المذكورة في الباقات تقديرية وتحتاج تثبيت حسب المشروع.</p></div></section><section class="shemo-section shemo-policy-list"><h2>صيغة التسليم</h2><p>يتم تسليم الملفات النهائية بصيغ مناسبة للنشر حسب الاتفاق: صور، PDF، MP4، أو ملفات أخرى. الملفات المفتوحة تحتاج اتفاقًا صريحًا.</p><h2>جدول التسليم</h2><p>الجدول يبدأ بعد استلام المدخلات والعربون والموافقة على النطاق. أي تأخير في الملاحظات أو المواد قد يؤجل التسليم.</p><h2>الاحتفاظ بالملفات</h2><p>قد يحتفظ الاستوديو بنسخ عمل لفترة محدودة لأغراض المراجعة أو إعادة التصدير. لا يُعتبر ذلك تخزينًا دائمًا أو نسخة احتياطية للعميل.</p></section>',
		'en_body' => '<section class="shemo-section shemo-hero"><div><p class="shemo-kicker">Legal draft</p><h1>Delivery Policy</h1><p class="shemo-lead">Legal review draft: package timelines are estimates and must be confirmed per project.</p></div></section><section class="shemo-section shemo-policy-list"><h2>Delivery format</h2><p>Final files are delivered in publish-ready formats according to the agreement: images, PDF, MP4, or other relevant formats. Source files require explicit agreement.</p><h2>Delivery schedule</h2><p>The schedule starts after inputs, deposit, and scope approval are received. Delays in feedback or materials may move the delivery date.</p><h2>File retention</h2><p>The studio may keep working copies for a limited period for revision or re-export. This is not permanent storage or a client backup service.</p></section>',
	),
);

foreach ( $policy_pages as $policy ) {
	shemo_stage22_pair_pages(
		array( 'slug' => $policy['ar_slug'], 'title' => $policy['ar_title'], 'description' => 'مسودة سياسة قابلة للمراجعة القانونية قبل الاعتماد النهائي.', 'content' => shemo_stage22_content_shell( 'shemo-stage20 shemo-stage22 shemo-policy-page', $policy['ar_body'] ) ),
		array( 'slug' => $policy['en_slug'], 'title' => $policy['en_title'], 'description' => 'Legal review draft policy content before final approval.', 'content' => shemo_stage22_content_shell( 'shemo-stage20 shemo-stage22 shemo-policy-page', $policy['en_body'] ) ),
		'publish'
	);
}

flush_rewrite_rules( false );

WP_CLI::success(
	sprintf(
		'Stage 22 enrichment complete: %d assets, %d project pairs, %d package pairs, %d demo testimonial pairs.',
		count( $asset_ids ),
		count( $built_projects ),
		count( $built_packages ),
		count( $testimonials )
	)
);
