<?php
/**
 * Build Stage 18 Work archive data: bilingual demo/concept projects.
 *
 * Run with:
 * wp eval-file tools/stage18-build-work.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) || ! function_exists( 'pll_save_term_translations' ) ) {
	WP_CLI::error( 'Polylang functions are not available.' );
}

const SHEMO_STAGE18_AR_LABEL = 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري';
const SHEMO_STAGE18_EN_LABEL = 'Demo / Concept Project - Not commissioned by a client';

function shemo_stage18_term( string $taxonomy, string $name, string $slug, string $lang ): int {
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

function shemo_stage18_term_pair( string $taxonomy, string $ar_name, string $ar_slug, string $en_name, string $en_slug ): array {
	$ar_id = shemo_stage18_term( $taxonomy, $ar_name, $ar_slug, 'ar' );
	$en_id = shemo_stage18_term( $taxonomy, $en_name, $en_slug, 'en' );

	pll_save_term_translations(
		array(
			'ar' => $ar_id,
			'en' => $en_id,
		)
	);

	return array(
		'ar' => $ar_id,
		'en' => $en_id,
	);
}

function shemo_stage18_upsert_project( array $project, string $lang, array $terms ): int {
	$is_ar  = 'ar' === $lang;
	$slug   = $is_ar ? $project['ar_slug'] : $project['en_slug'];
	$found  = get_page_by_path( $slug, OBJECT, 'project' );
	$title  = $is_ar ? $project['ar_title'] : $project['en_title'];
	$body   = $is_ar ? $project['ar_process'] : $project['en_process'];
	$label  = $is_ar ? SHEMO_STAGE18_AR_LABEL : SHEMO_STAGE18_EN_LABEL;
	$status = $is_ar ? 'publish' : 'publish';

	$postarr = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'project',
		'post_status'  => $status,
		'post_excerpt' => $is_ar ? $project['ar_summary'] : $project['en_summary'],
		'post_content' => $body,
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
	update_post_meta( $post_id, 'shemo_short_summary', $is_ar ? $project['ar_summary'] : $project['en_summary'] );
	update_post_meta( $post_id, 'shemo_project_date', '2026-07-01' );
	update_post_meta( $post_id, 'shemo_project_goal', $is_ar ? $project['ar_goal'] : $project['en_goal'] );
	update_post_meta( $post_id, 'shemo_challenge', $is_ar ? $project['ar_challenge'] : $project['en_challenge'] );
	update_post_meta( $post_id, 'shemo_creative_direction', $is_ar ? $project['ar_direction'] : $project['en_direction'] );
	update_post_meta( $post_id, 'shemo_deliverables', $is_ar ? $project['ar_deliverables'] : $project['en_deliverables'] );
	update_post_meta( $post_id, 'shemo_featured', 1 );
	update_post_meta( $post_id, '_shemo_project_status_label', $label );
	update_post_meta( $post_id, 'rank_math_title', $title . ' - Shemo Studio' );
	update_post_meta( $post_id, 'rank_math_description', $label . ' - ' . ( $is_ar ? $project['ar_summary'] : $project['en_summary'] ) );
	delete_post_meta( $post_id, 'shemo_results' );
	delete_post_meta( $post_id, 'shemo_testimonial' );

	foreach ( $terms as $taxonomy => $pair_keys ) {
		$term_ids = array();

		foreach ( $pair_keys as $key ) {
			$term_ids[] = $GLOBALS['shemo_stage18_terms'][ $taxonomy ][ $key ][ $lang ];
		}

		wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
	}

	return $post_id;
}

$GLOBALS['shemo_stage18_terms'] = array(
	'service'        => array(
		'video'      => shemo_stage18_term_pair( 'service', 'مونتاج وموشن', 'service-video-motion-ar', 'Video Editing & Motion', 'service-video-motion-en' ),
		'design'     => shemo_stage18_term_pair( 'service', 'تصميم جرافيك', 'service-graphic-design-ar', 'Graphic Design', 'service-graphic-design-en' ),
		'storyboard' => shemo_stage18_term_pair( 'service', 'Storyboard وتخطيط إبداعي', 'service-storyboard-ar', 'Storyboarding & Creative Planning', 'service-storyboard-en' ),
		'branding'   => shemo_stage18_term_pair( 'service', 'هوية واتجاه بصري', 'service-branding-ar', 'Branding', 'service-branding-en' ),
	),
	'project_type'   => array(
		'showcase' => shemo_stage18_term_pair( 'project_type', 'عرض خدمة تجريبي', 'project-type-demo-showcase-ar', 'Concept service showcase', 'project-type-demo-showcase-en' ),
		'study'    => shemo_stage18_term_pair( 'project_type', 'دراسة إبداعية داخلية', 'project-type-internal-study-ar', 'Internal creative study', 'project-type-internal-study-en' ),
	),
	'industry'       => array(
		'food'      => shemo_stage18_term_pair( 'industry', 'قطاع أطعمة ومشروبات تخيلي', 'industry-food-concept-ar', 'Food & beverage concept', 'industry-food-concept-en' ),
		'education' => shemo_stage18_term_pair( 'industry', 'تعليم ومهارات تخيلي', 'industry-education-concept-ar', 'Education concept', 'industry-education-concept-en' ),
		'creator'   => shemo_stage18_term_pair( 'industry', 'صنّاع محتوى تخيلي', 'industry-creator-concept-ar', 'Creator concept', 'industry-creator-concept-en' ),
	),
	'platform'       => array(
		'instagram' => shemo_stage18_term_pair( 'platform', 'Instagram', 'platform-instagram-ar', 'Instagram', 'platform-instagram-en' ),
		'youtube'   => shemo_stage18_term_pair( 'platform', 'YouTube', 'platform-youtube-ar', 'YouTube', 'platform-youtube-en' ),
		'web'       => shemo_stage18_term_pair( 'platform', 'Web', 'platform-web-ar', 'Web', 'platform-web-en' ),
	),
	'tool'           => array(
		'figma'   => shemo_stage18_term_pair( 'tool', 'Figma', 'tool-figma-ar', 'Figma', 'tool-figma-en' ),
		'premiere'=> shemo_stage18_term_pair( 'tool', 'Premiere Pro', 'tool-premiere-ar', 'Premiere Pro', 'tool-premiere-en' ),
		'ae'      => shemo_stage18_term_pair( 'tool', 'After Effects', 'tool-after-effects-ar', 'After Effects', 'tool-after-effects-en' ),
		'procreate'=> shemo_stage18_term_pair( 'tool', 'Procreate', 'tool-procreate-ar', 'Procreate', 'tool-procreate-en' ),
	),
	'content_format' => array(
		'film'      => shemo_stage18_term_pair( 'content_format', 'فيلم إطلاق قصير', 'format-launch-film-ar', 'Short launch film', 'format-launch-film-en' ),
		'social'    => shemo_stage18_term_pair( 'content_format', 'حزمة سوشيال', 'format-social-kit-ar', 'Social content kit', 'format-social-kit-en' ),
		'story'     => shemo_stage18_term_pair( 'content_format', 'Storyboard', 'format-storyboard-ar', 'Storyboard', 'format-storyboard-en' ),
	),
	'client_type'    => array(
		'demo' => shemo_stage18_term_pair( 'client_type', 'مشروع تجريبي / Concept', 'client-type-demo-concept-ar', 'Demo / Concept Project', 'client-type-demo-concept-en' ),
	),
	'visual_style'   => array(
		'noir'      => shemo_stage18_term_pair( 'visual_style', 'Cinematic Noir', 'visual-cinematic-noir-ar', 'Cinematic Noir', 'visual-cinematic-noir-en' ),
		'editorial' => shemo_stage18_term_pair( 'visual_style', 'Editorial بسيط', 'visual-editorial-ar', 'Minimal editorial', 'visual-editorial-en' ),
		'sketch'    => shemo_stage18_term_pair( 'visual_style', 'خطوط مرسومة', 'visual-sketch-lines-ar', 'Hand-drawn lines', 'visual-sketch-lines-en' ),
	),
);

$projects = array(
	array(
		'key'             => 'frame-pulse',
		'order'           => 30,
		'ar_slug'         => 'frame-pulse-launch-film',
		'en_slug'         => 'frame-pulse-launch-film-en',
		'ar_title'        => 'Frame Pulse - فيلم إطلاق تجريبي',
		'en_title'        => 'Frame Pulse - Demo Launch Film',
		'ar_summary'      => 'تصور لفيلم إطلاق قصير يشرح فكرة منتج تخيلي بإيقاع واضح، من لقطة أولى إلى CTA نهائي.',
		'en_summary'      => 'A short launch-film concept for an imaginary product, shaped from first frame to final CTA with clear rhythm.',
		'ar_goal'         => 'اختبار كيف يمكن تحويل فكرة منتج عامة إلى تسلسل فيديو قصير يصلح للسوشيال والويب بدون استخدام لقطات عميل.',
		'en_goal'         => 'To test how a broad product idea can become a compact video sequence for social and web without client footage.',
		'ar_challenge'    => 'التحدي كان بناء إحساس بالإيقاع والوضوح من مواد أصلية داخلية فقط، مع تجنب أي ادعاء أن المنتج أو العميل حقيقي.',
		'en_challenge'    => 'The challenge was creating rhythm and clarity using only internal original material, without implying that the product or client is real.',
		'ar_direction'    => 'اتجاه Cinematic Noir: خلفية داكنة، لقطات نصية قصيرة، حركة بسيطة، ولمسات Ember تقود العين من المشكلة إلى الحل.',
		'en_direction'    => 'A Cinematic Noir direction: dark canvas, short text frames, restrained motion, and Ember accents guiding the eye from problem to solution.',
		'ar_deliverables' => array( 'Storyboard مختصر من 6 لقطات.', 'نسخة فيديو قصيرة مقترحة 20-30 ثانية.', 'إطارات عنوان وCTA.', 'قائمة export للسوشيال والويب.' ),
		'en_deliverables' => array( 'Compact 6-frame storyboard.', 'Suggested 20-30 second short video cut.', 'Title and CTA frames.', 'Export list for social and web.' ),
		'ar_process'      => '<p>بدأ المشروع من جملة واحدة: منتج يحتاج أن يبدو واضحًا خلال ثوانٍ. تم تقسيم الرسالة إلى افتتاح، مشكلة، تحول بصري، ثم CTA.</p><p>بعد ذلك تم رسم تسلسل لقطات بسيط وتحديد أين تدخل الحركة وأين يظل الإطار ساكنًا حتى لا يصبح الفيديو مزدحمًا.</p>',
		'en_process'      => '<p>The project started from one sentence: a product needs to feel clear within seconds. The message was split into opening, problem, visual turn, and CTA.</p><p>Then a simple frame sequence defined where motion should enter and where the frame should stay still to avoid visual noise.</p>',
		'terms'           => array(
			'service'        => array( 'video', 'storyboard' ),
			'project_type'   => array( 'showcase' ),
			'industry'       => array( 'creator' ),
			'platform'       => array( 'instagram', 'youtube' ),
			'tool'           => array( 'premiere', 'ae', 'figma' ),
			'content_format' => array( 'film' ),
			'client_type'    => array( 'demo' ),
			'visual_style'   => array( 'noir' ),
		),
	),
	array(
		'key'             => 'ember-menu',
		'order'           => 20,
		'ar_slug'         => 'ember-menu-social-kit',
		'en_slug'         => 'ember-menu-social-kit-en',
		'ar_title'        => 'Ember Menu - حزمة سوشيال تجريبية',
		'en_title'        => 'Ember Menu - Demo Social Kit',
		'ar_summary'      => 'حزمة تصميمات سوشيال لعلامة طعام تخيلية، تركز على ترتيب الرسالة والمنتج والعرض داخل نظام بصري واحد.',
		'en_summary'      => 'A social design kit for an imaginary food brand, focused on message, product, and offer hierarchy inside one visual system.',
		'ar_goal'         => 'بناء مثال يوضح كيف يمكن لحملة صغيرة أن تظهر متماسكة عبر بوستات متعددة بدون اختراع عميل أو نتائج بيع.',
		'en_goal'         => 'To show how a small campaign can feel consistent across multiple posts without inventing a client or sales results.',
		'ar_challenge'    => 'التحدي كان جعل كل قطعة مفهومة وحدها، لكنها تظل جزءًا من نظام واحد في اللون، التكوين، ونبرة العنوان.',
		'en_challenge'    => 'The challenge was making each piece understandable on its own while still belonging to one system of color, layout, and headline tone.',
		'ar_direction'    => 'تم استخدام اتجاه Editorial بسيط مع تباين داكن ولمسات Ember، حتى تبدو التصميمات شهية وواضحة من غير زخرفة زائدة.',
		'en_direction'    => 'The direction uses minimal editorial layouts with dark contrast and Ember accents, keeping the visuals appetizing and clear without extra decoration.',
		'ar_deliverables' => array( 'ثلاثة layouts للسوشيال.', 'نظام عناوين قصير.', 'ألوان واستخدامات accent.', 'اقتراح تحويل قطعة واحدة إلى motion.' ),
		'en_deliverables' => array( 'Three social layouts.', 'Short headline system.', 'Color and accent usage.', 'Suggestion for turning one asset into motion.' ),
		'ar_process'      => '<p>تم تحديد القطع المطلوبة كأنها حملة إطلاق مصغرة: إعلان المنتج، عرض قصير، وتذكير بصري.</p><p>كل تصميم بُني على hierarchy واضح: صورة/وصف/CTA، مع الحفاظ على مساحة تنفس حتى لا تصبح القطع مزدحمة.</p>',
		'en_process'      => '<p>The assets were framed as a compact launch campaign: product announcement, short offer, and visual reminder.</p><p>Each layout uses clear hierarchy: image, description, CTA, with enough breathing room to keep the set from feeling crowded.</p>',
		'terms'           => array(
			'service'        => array( 'design', 'branding' ),
			'project_type'   => array( 'showcase' ),
			'industry'       => array( 'food' ),
			'platform'       => array( 'instagram' ),
			'tool'           => array( 'figma' ),
			'content_format' => array( 'social' ),
			'client_type'    => array( 'demo' ),
			'visual_style'   => array( 'editorial', 'noir' ),
		),
	),
	array(
		'key'             => 'line-course',
		'order'           => 10,
		'ar_slug'         => 'line-course-storyboard-study',
		'en_slug'         => 'line-course-storyboard-study-en',
		'ar_title'        => 'Line Course - دراسة Storyboard تجريبية',
		'en_title'        => 'Line Course - Demo Storyboard Study',
		'ar_summary'      => 'دراسة storyboard لكورس تعليمي تخيلي، توضّح كيف تتحول فكرة مجردة إلى مشاهد قابلة للإنتاج.',
		'en_summary'      => 'A storyboard study for an imaginary learning product, showing how an abstract idea becomes producible scenes.',
		'ar_goal'         => 'إظهار قيمة التخطيط البصري قبل إنتاج فيديو تعليمي أو إعلان قصير، باستخدام اسكتشات وملاحظات مشهدية.',
		'en_goal'         => 'To show the value of visual planning before producing an educational video or short ad, using sketches and scene notes.',
		'ar_challenge'    => 'الفكرة التعليمية قد تصبح جافة إذا عُرضت كنص مباشر فقط. المطلوب كان ترتيبها كمشاهد تقود الانتباه خطوة بخطوة.',
		'en_challenge'    => 'A learning idea can feel dry if presented as direct text only. The task was arranging it as scenes that guide attention step by step.',
		'ar_direction'    => 'اتجاه خطوط مرسومة فوق خلفية داكنة، مع انتقال تدريجي من سؤال بسيط إلى نتيجة قابلة للفهم.',
		'en_direction'    => 'A hand-drawn line direction over a dark canvas, moving from a simple question to a clear understandable outcome.',
		'ar_deliverables' => array( 'Storyboard من 8 مشاهد.', 'ملاحظات حركة لكل مشهد.', 'اسكتشات أولية للشخصية/العنصر.', 'اقتراح صوت/نص تعليق مختصر.' ),
		'en_deliverables' => array( '8-scene storyboard.', 'Motion notes for each scene.', 'Initial character/object sketches.', 'Short voiceover/script suggestion.' ),
		'ar_process'      => '<p>بدأت الدراسة بتحديد نقطة الحيرة عند المتعلم، ثم تحويلها إلى سؤال بصري يظهر في أول مشهد.</p><p>تم تقسيم الشرح إلى وحدات صغيرة: مقدمة، مثال، تحول، خلاصة. كل وحدة حصلت على sketch سريع وملاحظة حركة.</p>',
		'en_process'      => '<p>The study started by identifying the learner’s point of confusion, then turning it into a visual question in the first frame.</p><p>The explanation was split into small units: setup, example, turn, takeaway. Each unit received a quick sketch and motion note.</p>',
		'terms'           => array(
			'service'        => array( 'storyboard' ),
			'project_type'   => array( 'study' ),
			'industry'       => array( 'education' ),
			'platform'       => array( 'web', 'youtube' ),
			'tool'           => array( 'procreate', 'figma' ),
			'content_format' => array( 'story' ),
			'client_type'    => array( 'demo' ),
			'visual_style'   => array( 'sketch', 'noir' ),
		),
	),
);

$built = array();

foreach ( $projects as $project ) {
	$ar_id = shemo_stage18_upsert_project( $project, 'ar', $project['terms'] );
	$en_id = shemo_stage18_upsert_project( $project, 'en', $project['terms'] );

	pll_save_post_translations(
		array(
			'ar' => $ar_id,
			'en' => $en_id,
		)
	);

	$built[ $project['key'] ] = array(
		'ar' => $ar_id,
		'en' => $en_id,
	);
}

foreach ( $built as $key => $ids ) {
	$related_ar = array();
	$related_en = array();

	foreach ( $built as $related_key => $related_ids ) {
		if ( $related_key === $key ) {
			continue;
		}

		$related_ar[] = $related_ids['ar'];
		$related_en[] = $related_ids['en'];
	}

	update_post_meta( $ids['ar'], 'shemo_related_projects', $related_ar );
	update_post_meta( $ids['en'], 'shemo_related_projects', $related_en );
}

if ( function_exists( 'PLL' ) && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
	PLL()->model->clean_languages_cache();
}

flush_rewrite_rules( false );

foreach ( $built as $key => $ids ) {
	WP_CLI::log(
		sprintf(
			'%s: ar=%d %s | en=%d %s',
			$key,
			$ids['ar'],
			get_permalink( $ids['ar'] ),
			$ids['en'],
			get_permalink( $ids['en'] )
		)
	);
}

WP_CLI::success( 'Stage 18 demo/concept projects built and linked in Polylang.' );
