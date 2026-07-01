<?php
/**
 * Build Stage 20 bilingual Start a Project, Contact, utility, and policy pages.
 *
 * Run with:
 * wp eval-file tools/stage20-build-start-contact-policies.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
	WP_CLI::error( 'Polylang functions are not available.' );
}

function shemo_stage20_wrap( string $body ): string {
	return '<!-- wp:group {"tagName":"main","className":"shemo-home shemo-page shemo-stage20","layout":{"type":"default"}} -->' . "\n"
		. '<main class="wp-block-group shemo-home shemo-page shemo-stage20">' . "\n"
		. '<!-- wp:html -->' . "\n"
		. $body . "\n"
		. '<!-- /wp:html -->' . "\n"
		. '</main>' . "\n"
		. '<!-- /wp:group -->';
}

function shemo_stage20_upsert_page( string $slug, string $title, string $content, string $lang, string $seo_title, string $seo_description ): int {
	$page = get_page_by_path( $slug, OBJECT, 'page' );

	$postarr = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_content' => $content,
	);

	if ( $page ) {
		$postarr['ID'] = $page->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	pll_set_post_language( $post_id, $lang );

	update_post_meta( $post_id, '_generate-disable-headline', 'true' );
	update_post_meta( $post_id, '_generate-full-width-content', 'true' );
	update_post_meta( $post_id, 'rank_math_title', $seo_title );
	update_post_meta( $post_id, 'rank_math_description', $seo_description );

	return (int) $post_id;
}

function shemo_stage20_enable_polylang_language_urls(): void {
	$options = get_option( 'polylang' );

	if ( is_array( $options ) ) {
		$options['redirect_lang'] = true;
		update_option( 'polylang', $options );
	}

	if ( function_exists( 'PLL' ) && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
		PLL()->model->clean_languages_cache();
	}

	flush_rewrite_rules( false );
}

function shemo_stage20_field( int $index, string $element, string $name, string $label, string $placeholder = '', bool $required = false, array $options = array() ): array {
	$field = array(
		'index'      => $index,
		'element'    => $element,
		'attributes' => array(
			'name'        => $name,
			'value'       => '',
			'id'          => '',
			'class'       => '',
			'placeholder' => $placeholder,
		),
		'settings'   => array(
			'container_class'   => '',
			'label'             => $label,
			'admin_field_label' => $label,
			'label_placement'   => '',
			'help_message'      => '',
			'validation_rules'  => array(
				'required' => array(
					'value'   => $required,
					'message' => 'This field is required',
					'global'  => true,
				),
			),
			'conditional_logics' => array(),
		),
		'editor_options' => array(
			'title'    => $label,
			'template' => 'textarea' === $element ? 'inputTextarea' : 'inputText',
		),
		'uniqElKey' => 'stage20_' . $name,
	);

	if ( 'input_email' === $element ) {
		$field['attributes']['type'] = 'email';
		$field['settings']['validation_rules']['email'] = array(
			'value'   => true,
			'message' => 'This field must contain a valid email',
			'global'  => true,
		);
	} elseif ( 'textarea' === $element ) {
		$field['attributes']['rows'] = 5;
		$field['attributes']['cols'] = 2;
	} elseif ( 'select' === $element ) {
		$field['settings']['placeholder'] = $placeholder;
		$field['options'] = $options;
		$field['editor_options']['template'] = 'select';
	} else {
		$field['attributes']['type'] = 'text';
	}

	return $field;
}

function shemo_stage20_turnstile_field( int $index ): array {
	return array(
		'index'      => $index,
		'element'    => 'turnstile',
		'attributes' => array(
			'name' => 'cf-turnstile-response',
		),
		'settings'   => array(
			'label'            => '',
			'label_placement'  => '',
			'validation_rules' => array(),
		),
		'editor_options' => array(
			'title'              => 'Turnstile',
			'icon_class'         => 'ff-edit-recaptha',
			'why_disabled_modal' => 'turnstile',
			'template'           => 'turnstile',
		),
		'uniqElKey' => 'stage20_turnstile',
	);
}

function shemo_stage20_start_form_fields( string $lang ): array {
	$is_ar  = 'ar' === $lang;
	$fields = array(
		shemo_stage20_field( 1, 'input_text', 'client_name', $is_ar ? 'الاسم' : 'Name', $is_ar ? 'اسمك أو اسم الشركة' : 'Your name or company', true ),
		shemo_stage20_field( 2, 'input_email', 'email', $is_ar ? 'البريد الإلكتروني' : 'Email', 'name@example.com', true ),
		shemo_stage20_field( 3, 'input_text', 'brand_or_company', $is_ar ? 'اسم البراند أو المشروع' : 'Brand or project name', $is_ar ? 'اختياري' : 'Optional', false ),
		shemo_stage20_field(
			4,
			'select',
			'project_type',
			$is_ar ? 'نوع المشروع الأقرب' : 'Closest project type',
			$is_ar ? 'اختر الأقرب' : 'Choose the closest',
			true,
			$is_ar
				? array(
					'video-motion'     => 'Video Editing & Motion',
					'graphic-design'   => 'Graphic Design',
					'illustration'      => 'Sketch & Illustration',
					'storyboard'        => 'Storyboarding / Creative Planning',
					'branding'          => 'Branding',
					'creative-direction'=> 'Creative Direction / Custom',
					'not-sure'          => 'لست متأكدًا بعد',
				)
				: array(
					'video-motion'      => 'Video Editing & Motion',
					'graphic-design'    => 'Graphic Design',
					'illustration'      => 'Sketch & Illustration',
					'storyboard'        => 'Storyboarding / Creative Planning',
					'branding'          => 'Branding',
					'creative-direction'=> 'Creative Direction / Custom',
					'not-sure'          => 'Not sure yet',
				)
		),
		shemo_stage20_field( 5, 'textarea', 'project_goal', $is_ar ? 'ما الهدف من المشروع؟' : 'What should this project achieve?', $is_ar ? 'اكتب الهدف، الجمهور، والمنصة الأساسية.' : 'Share the goal, audience, and main platform.', true ),
		shemo_stage20_field( 6, 'textarea', 'deliverables', $is_ar ? 'ما التسليمات المتوقعة؟' : 'Expected deliverables', $is_ar ? 'مثال: فيديو 30 ثانية، 5 تصميمات، storyboard...' : 'Example: 30-second video, 5 designs, storyboard...', false ),
		shemo_stage20_field( 7, 'input_text', 'budget_range', $is_ar ? 'ميزانية تقريبية' : 'Approximate budget', $is_ar ? 'اختياري - مثال: 300-600 USD' : 'Optional - example: USD 300-600', false ),
		shemo_stage20_field( 8, 'input_text', 'timeline', $is_ar ? 'الموعد المتوقع' : 'Expected timeline', $is_ar ? 'مثال: خلال أسبوعين' : 'Example: within two weeks', false ),
		shemo_stage20_field( 9, 'input_text', 'reference_links', $is_ar ? 'روابط أو مراجع' : 'Links or references', $is_ar ? 'اختياري - رابط درايف/موقع/مرجع' : 'Optional - Drive/site/reference link', false ),
		shemo_stage20_turnstile_field( 10 ),
	);

	return array(
		'fields'       => $fields,
		'submitButton' => array(
			'uniqElKey'  => 'stage20_submit_' . $lang,
			'element'    => 'button',
			'attributes' => array(
				'type'  => 'submit',
				'class' => '',
			),
			'settings'   => array(
				'align'           => 'left',
				'button_style'    => 'default',
				'container_class' => '',
				'help_message'    => '',
				'background_color'=> '#FF5A2C',
				'button_size'     => 'md',
				'color'           => '#0E0F12',
				'button_ui'       => array(
					'type'    => 'default',
					'text'    => $is_ar ? 'إرسال brief المشروع' : 'Send project brief',
					'img_url' => '',
				),
			),
			'editor_options' => array(
				'title' => 'Submit Button',
			),
		),
	);
}

function shemo_stage20_upsert_fluent_form( string $key, string $title, string $lang ): int {
	global $wpdb;

	$forms_table = $wpdb->prefix . 'fluentform_forms';
	$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
	$form        = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$forms_table} WHERE title = %s LIMIT 1", $title ) );
	$now         = current_time( 'mysql' );
	$fields      = wp_json_encode( shemo_stage20_start_form_fields( $lang ), JSON_UNESCAPED_UNICODE );

	$data = array(
		'title'               => $title,
		'status'              => 'published',
		'appearance_settings' => null,
		'form_fields'         => $fields,
		'has_payment'         => 0,
		'type'                => 'form',
		'conditions'          => null,
		'created_by'          => 1,
		'updated_at'          => $now,
	);

	if ( $form ) {
		$wpdb->update( $forms_table, $data, array( 'id' => (int) $form->id ) );
		$form_id = (int) $form->id;
	} else {
		$data['created_at'] = $now;
		$wpdb->insert( $forms_table, $data );
		$form_id = (int) $wpdb->insert_id;
	}

	$settings = array(
		'confirmation' => array(
			'redirectTo'           => 'samePage',
			'messageToShow'        => 'ar' === $lang ? 'تم استلام brief المشروع. سنراجعه ونرد عليك بخطوة تالية واضحة.' : 'Your project brief was received. We will review it and reply with a clear next step.',
			'customPage'           => null,
			'samePageFormBehavior' => 'hide_form',
			'customUrl'            => null,
		),
		'restrictions' => array(
			'limitNumberOfEntries' => array( 'enabled' => false ),
			'scheduleForm'         => array( 'enabled' => false ),
			'requireLogin'         => array( 'enabled' => false ),
			'denyEmptySubmission'  => array( 'enabled' => false ),
		),
		'layout' => array(
			'labelPlacement'       => 'top',
			'helpMessagePlacement' => 'with_label',
			'errorMessagePlacement'=> 'inline',
			'cssClassName'         => 'shemo-start-form',
			'asteriskPlacement'    => 'asterisk-right',
		),
		'delete_entry_on_submission' => 'no',
	);

	$metas = array(
		'stage20_form_key'             => $key,
		'formSettings'                 => wp_json_encode( $settings, JSON_UNESCAPED_UNICODE ),
		'_primary_email_field'         => 'email',
		'step_data_persistency_status' => 'no',
	);

	foreach ( $metas as $meta_key => $value ) {
		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$meta_table} WHERE form_id = %d AND meta_key = %s LIMIT 1", $form_id, $meta_key ) );
		if ( $existing ) {
			$wpdb->update( $meta_table, array( 'value' => $value ), array( 'id' => (int) $existing ) );
		} else {
			$wpdb->insert(
				$meta_table,
				array(
					'form_id'  => $form_id,
					'meta_key' => $meta_key,
					'value'    => $value,
				)
			);
		}
	}

	return $form_id;
}

function shemo_stage20_start_content( string $lang, int $form_id ): string {
	$is_ar = 'ar' === $lang;
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="start-title"><div><p class="shemo-kicker">Start a Project</p><h1 id="start-title">ابدأ بفكرة واضحة، حتى لو لم يكتمل شكلها بعد.</h1><p class="shemo-lead">هذا النموذج يجمع ما يكفي لفهم الهدف والمنصة ونوع التسليم. مبني على Fluent Forms Free: صفحة واحدة، بدون save &amp; resume، وبدون PDF تلقائي.</p><div class="shemo-hero__actions"><a class="shemo-button" href="#start-form-title">املأ brief المشروع</a><a class="shemo-button shemo-button--secondary" href="/packages/">راجع الباقات</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">brief / direction / scope / reply</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="start-title"><div><p class="shemo-kicker">Start a Project</p><h1 id="start-title">Start with a clear idea, even if the shape is still rough.</h1><p class="shemo-lead">This form collects enough context to understand the goal, platform, and deliverables. It uses Fluent Forms Free: one page, no save and resume, and no automatic PDF generation.</p><div class="shemo-hero__actions"><a class="shemo-button" href="#start-form-title">Fill the project brief</a><a class="shemo-button shemo-button--secondary" href="/en/packages-en/">Review packages</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">brief / direction / scope / reply</p></div></div></section>';

	$body .= '<section class="shemo-section shemo-grid--two" aria-labelledby="start-before-title"><div><p class="shemo-kicker">' . esc_html( $is_ar ? 'قبل الإرسال' : 'Before sending' ) . '</p><h2 id="start-before-title">' . esc_html( $is_ar ? 'اكتب ما تعرفه الآن. التفاصيل تتضح في الرد التالي.' : 'Share what you know now. The next reply can clarify the rest.' ) . '</h2></div><div><ul class="shemo-check-list"><li>' . esc_html( $is_ar ? 'الهدف والجمهور والمنصة أهم من وصف طويل.' : 'Goal, audience, and platform matter more than a long description.' ) . '</li><li>' . esc_html( $is_ar ? 'الميزانية والموعد يساعدان على اقتراح نطاق واقعي.' : 'Budget and timing help shape a realistic scope.' ) . '</li><li>' . esc_html( $is_ar ? 'أي روابط أو مراجع بصرية مفيدة، حتى لو كانت أولية.' : 'Any links or visual references help, even if they are rough.' ) . '</li></ul></div></section>';
	$body .= '<section class="shemo-section shemo-form-section" aria-labelledby="start-form-title"><div><p class="shemo-kicker">Form</p><h2 id="start-form-title">' . esc_html( $is_ar ? 'Brief المشروع' : 'Project brief' ) . '</h2><p>' . esc_html( $is_ar ? 'تم تفعيل Cloudflare Turnstile على هذا النموذج لحماية الإرسال من السبام، بدون إضافة مدفوعة.' : 'Cloudflare Turnstile is enabled on this form for spam protection, without a paid add-on.' ) . '</p></div><div class="shemo-form-shell">[fluentform id="' . (int) $form_id . '"]</div></section>';
	$body .= '<section class="shemo-section"><div class="shemo-notice-panel"><p class="shemo-kicker">' . esc_html( $is_ar ? 'حدود النسخة المجانية' : 'Free-version limits' ) . '</p><p>' . esc_html( $is_ar ? 'النموذج مبسّط عمدًا حسب حدود Fluent Forms Free: لا multi-step Pro، لا حفظ واستكمال لاحقًا، ولا توليد PDF تلقائي. لو احتاج المشروع هذه الميزات لاحقًا، تُراجع كقرار منفصل.' : 'The form is intentionally simple within Fluent Forms Free: no Pro multi-step flow, no save and resume, and no automatic PDF generation. If those features become necessary later, they should be reviewed as a separate decision.' ) . '</p></div></section>';

	return shemo_stage20_wrap( $body );
}

function shemo_stage20_contact_content( string $lang ): string {
	$is_ar = 'ar' === $lang;
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="contact-title"><div><p class="shemo-kicker">Contact</p><h1 id="contact-title">للتواصل السريع أو سؤال قبل الـbrief.</h1><p class="shemo-lead">لو لديك فكرة واضحة، ابدأ من نموذج Start a Project. لو السؤال أبسط، استخدم البريد أو صفحة طلب عرض السعر.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a><a class="shemo-button shemo-button--secondary" href="mailto:ahmeddarhous@gmail.com">راسلنا بالبريد</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">question / brief / reply</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="contact-title"><div><p class="shemo-kicker">Contact</p><h1 id="contact-title">For a quick question before the brief.</h1><p class="shemo-lead">If you have a clear project idea, start with the Start a Project form. If the question is lighter, use email or the quote page.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/start-a-project-en/">Start a Project</a><a class="shemo-button shemo-button--secondary" href="mailto:ahmeddarhous@gmail.com">Email us</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">question / brief / reply</p></div></div></section>';
	$body .= '<section class="shemo-section shemo-grid" aria-label="Contact paths"><article class="shemo-mini-card"><h2>' . esc_html( $is_ar ? 'مشروع جديد' : 'New project' ) . '</h2><p>' . esc_html( $is_ar ? 'استخدم brief المشروع لو تريد نطاقًا أو عرض سعر أولي.' : 'Use the project brief when you want scope or an initial quote.' ) . '</p><a class="shemo-button shemo-button--ghost" href="' . esc_url( $is_ar ? '/start-a-project/' : '/en/start-a-project-en/' ) . '">' . esc_html( $is_ar ? 'ابدأ مشروعك' : 'Start a Project' ) . '</a></article><article class="shemo-mini-card"><h2>' . esc_html( $is_ar ? 'عرض سعر سريع' : 'Quick quote' ) . '</h2><p>' . esc_html( $is_ar ? 'لو النطاق مختصر، نموذج طلب العرض مناسب أكثر.' : 'If the scope is compact, the quote form may be enough.' ) . '</p><a class="shemo-button shemo-button--ghost" href="' . esc_url( $is_ar ? '/request-a-quote/' : '/en/request-a-quote-en/' ) . '">' . esc_html( $is_ar ? 'اطلب عرض سعر' : 'Request a Quote' ) . '</a></article><article class="shemo-mini-card"><h2>' . esc_html( $is_ar ? 'سياسات وروابط' : 'Policies and links' ) . '</h2><p>' . esc_html( $is_ar ? 'راجع السياسات المسودة قبل أي اتفاق مكتوب.' : 'Review the draft policies before any written agreement.' ) . '</p><a class="shemo-button shemo-button--ghost" href="' . esc_url( $is_ar ? '/terms/' : '/en/terms-en/' ) . '">' . esc_html( $is_ar ? 'راجع الشروط' : 'Review terms' ) . '</a></article></section>';

	return shemo_stage20_wrap( $body );
}

function shemo_stage20_thank_you_content( string $lang ): string {
	$is_ar = 'ar' === $lang;
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="thanks-title"><div><p class="shemo-kicker">Thank You</p><h1 id="thanks-title">تم استلام رسالتك.</h1><p class="shemo-lead">سنراجع التفاصيل ونرد بخطوة تالية واضحة. لو أرسلت brief مشروع، فالرد الأول غالبًا يكون سؤال توضيحي أو نطاق مقترح.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/work/">شاهد العمل</a><a class="shemo-button shemo-button--secondary" href="/">العودة للرئيسية</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">received / review / next step</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="thanks-title"><div><p class="shemo-kicker">Thank You</p><h1 id="thanks-title">Your message has been received.</h1><p class="shemo-lead">We will review the details and reply with a clear next step. If you sent a project brief, the first reply is usually a clarifying question or proposed scope.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/work/">View Work</a><a class="shemo-button shemo-button--secondary" href="/en/">Back Home</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">received / review / next step</p></div></div></section>';

	return shemo_stage20_wrap( $body );
}

function shemo_stage20_search_page_content( string $lang ): string {
	$is_ar  = 'ar' === $lang;
	$action = $is_ar ? '/' : '/en/';
	$body   = '<section class="shemo-section shemo-hero" aria-labelledby="search-page-title"><div><p class="shemo-kicker">Search</p><h1 id="search-page-title">' . esc_html( $is_ar ? 'ابحث داخل الموقع.' : 'Search the site.' ) . '</h1><p class="shemo-lead">' . esc_html( $is_ar ? 'استخدم البحث للوصول إلى صفحة خدمة، مشروع Demo/Concept، أو سياسة.' : 'Use search to find a service page, demo/concept project, or policy.' ) . '</p><form class="shemo-search-form" role="search" method="get" action="' . esc_url( $action ) . '"><label class="screen-reader-text" for="stage20-search">' . esc_html( $is_ar ? 'بحث' : 'Search' ) . '</label><input id="stage20-search" type="search" name="s" placeholder="' . esc_attr( $is_ar ? 'اكتب كلمة البحث' : 'Search term' ) . '"><button type="submit">' . esc_html( $is_ar ? 'بحث' : 'Search' ) . '</button></form></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">find / page / next action</p></div></div></section>';

	return shemo_stage20_wrap( $body );
}

function shemo_stage20_policy_content( string $lang, string $type ): string {
	$is_ar = 'ar' === $lang;
	$data  = array(
		'terms' => array(
			'ar_title' => 'الشروط العامة',
			'en_title' => 'Terms',
			'ar_lead' => 'مسودة شروط عامة للتعامل مع Shemo Studio. لا تُعد عقدًا قانونيًا نهائيًا قبل المراجعة والموافقة.',
			'en_lead' => 'Draft general terms for working with Shemo Studio. They are not final legal terms until reviewed and approved.',
			'ar_items' => array( 'أي مشروع يبدأ بنطاق مكتوب أو brief واضح قبل تثبيت السعر أو الموعد.', 'الأسعار والباقات الحالية أمثلة قابلة للتعديل وليست قرارًا تجاريًا نهائيًا.', 'لا توجد وعود بنتائج تجارية مضمونة أو أرقام أداء غير مثبتة.', 'أي استخدام لملفات أو منصات أو خدمات خارجية يخضع لشروطها أيضًا.' ),
			'en_items' => array( 'Each project starts with written scope or a clear brief before price or timing is confirmed.', 'Current package prices are editable examples, not final approved commercial pricing.', 'No guaranteed business results or unsupported performance metrics are promised.', 'Any use of third-party files, platforms, or services is also subject to their terms.' ),
		),
		'revision' => array(
			'ar_title' => 'سياسة المراجعات',
			'en_title' => 'Revision Policy',
			'ar_lead' => 'مسودة قابلة للمراجعة توضّح كيف تُدار التعديلات بدون فتح نطاق المشروع بلا نهاية.',
			'en_lead' => 'A review draft explaining how revisions are handled without leaving project scope open-ended.',
			'ar_items' => array( 'عدد جولات المراجعة يجب أن يظهر داخل عرض السعر أو الاتفاق المكتوب.', 'التعديل الصغير يعالج ما تم الاتفاق عليه داخل النطاق الأصلي.', 'تغيير الهدف أو إضافة تسليمات جديدة يُعامل كتغيير نطاق ويحتاج تقديرًا جديدًا.', 'تبدأ المراجعة من ملاحظات مجمّعة وواضحة حتى لا يتكرر الرجوع لنفس المرحلة.' ),
			'en_items' => array( 'The number of revision rounds should appear in the quote or written agreement.', 'A minor revision adjusts what was already agreed within the original scope.', 'Changing the goal or adding deliverables is treated as a scope change and needs a new estimate.', 'Review works best from clear consolidated notes instead of repeated partial feedback.' ),
		),
		'deposit' => array(
			'ar_title' => 'سياسة العربون',
			'en_title' => 'Deposit Policy',
			'ar_lead' => 'مسودة فقط: لم يتم اعتماد سياسة عربون نهائية أو نسبة دفع ثابتة بعد.',
			'en_lead' => 'Draft only: no final deposit policy or fixed payment percentage has been approved yet.',
			'ar_items' => array( 'أي عربون محتمل يجب أن يظهر في عرض سعر مكتوب قبل الدفع.', 'النسبة أو المبلغ ليسا معتمدين نهائيًا في هذه المرحلة.', 'تدفق SureCart الحالي اختبار محلي فقط، وليس عملية دفع إنتاجية.', 'لا يتم اعتبار أي دفع مطلوبًا حتى تُعتمد السياسة وسير الدفع النهائيان صراحة.' ),
			'en_items' => array( 'Any possible deposit must appear in a written quote before payment.', 'The percentage or amount is not final or approved at this stage.', 'The current SureCart flow is local test-only, not production payment.', 'No payment should be treated as required until the final policy and payment flow are explicitly approved.' ),
		),
		'refund' => array(
			'ar_title' => 'سياسة الاسترجاع',
			'en_title' => 'Refund Policy',
			'ar_lead' => 'مسودة قابلة للمراجعة، وليست وعدًا قانونيًا نهائيًا أو سياسة مالية معتمدة.',
			'en_lead' => 'A review draft, not a final legal promise or approved financial policy.',
			'ar_items' => array( 'أي استرجاع يعتمد على مرحلة المشروع وما تم تنفيذه فعليًا.', 'تكاليف خارجية غير قابلة للاسترداد، إن وُجدت، يجب توضيحها قبل استخدامها.', 'بعد تسليم ملفات نهائية أو بدء إنتاج واسع، تختلف إمكانية الاسترجاع حسب النطاق المكتوب.', 'السياسة النهائية تحتاج مراجعة وموافقة صريحة قبل الإطلاق العام.' ),
			'en_items' => array( 'Any refund depends on the project stage and work already completed.', 'Non-refundable third-party costs, if any, should be disclosed before use.', 'After final files are delivered or major production begins, refund eligibility depends on written scope.', 'The final policy needs review and explicit approval before public launch.' ),
		),
		'delivery' => array(
			'ar_title' => 'سياسة التسليم',
			'en_title' => 'Delivery Policy',
			'ar_lead' => 'مسودة عملية توضّح كيف يتم تسليم الملفات والمواعيد بدون وعود غير واقعية.',
			'en_lead' => 'A practical draft explaining file handoff and timing without unrealistic promises.',
			'ar_items' => array( 'كل عرض سعر يجب أن يحدد صيغ الملفات المطلوبة ومكان التسليم.', 'المواعيد تعتمد على اكتمال المواد المطلوبة وسرعة المراجعة.', 'أي تأخير في إرسال الملاحظات أو الملفات قد يغيّر الموعد المتوقع.', 'التسليم النهائي يكون بملفات منظمة مناسبة للنشر أو الاستخدام المتفق عليه.' ),
			'en_items' => array( 'Each quote should define required file formats and delivery location.', 'Timelines depend on receiving required materials and timely review.', 'Delays in feedback or source files may change the expected delivery date.', 'Final handoff uses organized files suited to the agreed publishing or usage context.' ),
		),
	);

	$page = $data[ $type ];
	$body = '<section class="shemo-section shemo-hero shemo-policy-hero" aria-labelledby="policy-title"><div><p class="shemo-kicker">' . esc_html( $is_ar ? 'مسودة قابلة للمراجعة' : 'Review draft' ) . '</p><h1 id="policy-title">' . esc_html( $is_ar ? $page['ar_title'] : $page['en_title'] ) . '</h1><p class="shemo-lead">' . esc_html( $is_ar ? $page['ar_lead'] : $page['en_lead'] ) . '</p></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">draft / review / approve later</p></div></div></section>';
	$body .= '<section class="shemo-section"><div class="shemo-notice-panel"><strong>' . esc_html( $is_ar ? 'تنبيه مهم' : 'Important note' ) . '</strong><p>' . esc_html( $is_ar ? 'هذه الصفحة منشورة كمسودة محتوى للمراجعة داخل مرحلة بناء الموقع. لا تضيف قرارًا جديدًا ولا تعتمد سياسة مالية أو قانونية نهائية.' : 'This page is published as review-copy during site build. It does not add a new decision or approve a final legal or financial policy.' ) . '</p></div></section>';
	$body .= '<section class="shemo-section"><ul class="shemo-policy-list">';
	foreach ( $is_ar ? $page['ar_items'] : $page['en_items'] as $item ) {
		$body .= '<li>' . esc_html( $item ) . '</li>';
	}
	$body .= '</ul></section>';
	$body .= '<section class="shemo-section shemo-split"><div><p class="shemo-kicker">' . esc_html( $is_ar ? 'الخصوصية والكوكيز' : 'Privacy and cookies' ) . '</p><h2>' . esc_html( $is_ar ? 'المصدر المنشور الحالي هو مستند Complianz.' : 'The current published source is the Complianz document.' ) . '</h2></div><div><p>' . esc_html( $is_ar ? 'تم ترك صفحة Privacy Policy القديمة كمسودة غير منشورة، ولم يتم حذفها أو تحويلها لسياسة نهائية. الرابط الآمن الحالي يشير إلى Privacy Statement (EU) وCookie Policy (EU) المنشورتين من Complianz.' : 'The old Privacy Policy page remains an unpublished draft. It was not deleted or turned into a final policy. The safe current link points to the published Complianz Privacy Statement (EU) and Cookie Policy (EU).' ) . '</p><div class="shemo-button-row"><a class="shemo-button shemo-button--secondary" href="/privacy-statement-eu/">Privacy Statement</a><a class="shemo-button shemo-button--secondary" href="/cookie-policy-eu/">Cookie Policy</a></div></div></section>';

	return shemo_stage20_wrap( $body );
}

function shemo_stage20_fix_known_english_cta_links(): void {
	global $wpdb;

	$replacements = array(
		'/en/start-a-project/' => '/en/start-a-project-en/',
		'/en/contact/'         => '/en/contact-en/',
	);

	foreach ( $replacements as $from => $to ) {
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_type = 'page' AND post_content LIKE %s",
				$from,
				$to,
				'%' . $wpdb->esc_like( $from ) . '%'
			)
		);
	}
}

$start_ar_id = shemo_stage20_upsert_fluent_form( 'stage20_start_ar', 'Shemo Start a Project - AR', 'ar' );
$start_en_id = shemo_stage20_upsert_fluent_form( 'stage20_start_en', 'Shemo Start a Project - EN', 'en' );

$pages = array(
	'start-a-project' => array(
		'ar' => shemo_stage20_upsert_page( 'start-a-project', 'ابدأ مشروعك', shemo_stage20_start_content( 'ar', $start_ar_id ), 'ar', 'ابدأ مشروعك - Shemo Studio', 'نموذج brief مشروع فعلي من Shemo Studio مبني على Fluent Forms Free مع Cloudflare Turnstile.' ),
		'en' => shemo_stage20_upsert_page( 'start-a-project-en', 'Start a Project', shemo_stage20_start_content( 'en', $start_en_id ), 'en', 'Start a Project - Shemo Studio', 'Project brief form for Shemo Studio built with Fluent Forms Free and Cloudflare Turnstile.' ),
	),
	'contact' => array(
		'ar' => shemo_stage20_upsert_page( 'contact', 'تواصل معنا', shemo_stage20_contact_content( 'ar' ), 'ar', 'تواصل معنا - Shemo Studio', 'طرق التواصل مع Shemo Studio وبدء مشروع أو طلب عرض سعر.' ),
		'en' => shemo_stage20_upsert_page( 'contact-en', 'Contact', shemo_stage20_contact_content( 'en' ), 'en', 'Contact - Shemo Studio', 'Contact Shemo Studio, start a project, or request a quote.' ),
	),
	'thank-you' => array(
		'ar' => shemo_stage20_upsert_page( 'thank-you', 'شكرًا لك', shemo_stage20_thank_you_content( 'ar' ), 'ar', 'شكرًا لك - Shemo Studio', 'صفحة شكر بعد إرسال نموذج Shemo Studio.' ),
		'en' => shemo_stage20_upsert_page( 'thank-you-en', 'Thank You', shemo_stage20_thank_you_content( 'en' ), 'en', 'Thank You - Shemo Studio', 'Thank you page after sending a Shemo Studio form.' ),
	),
	'search' => array(
		'ar' => shemo_stage20_upsert_page( 'search', 'بحث', shemo_stage20_search_page_content( 'ar' ), 'ar', 'بحث - Shemo Studio', 'صفحة بحث داخل موقع Shemo Studio.' ),
		'en' => shemo_stage20_upsert_page( 'search-en', 'Search', shemo_stage20_search_page_content( 'en' ), 'en', 'Search - Shemo Studio', 'Search page for the Shemo Studio website.' ),
	),
	'terms' => array(
		'ar' => shemo_stage20_upsert_page( 'terms', 'الشروط العامة', shemo_stage20_policy_content( 'ar', 'terms' ), 'ar', 'الشروط العامة - Shemo Studio', 'مسودة شروط Shemo Studio العامة للمراجعة، وليست سياسة نهائية معتمدة.' ),
		'en' => shemo_stage20_upsert_page( 'terms-en', 'Terms', shemo_stage20_policy_content( 'en', 'terms' ), 'en', 'Terms - Shemo Studio', 'Review draft of Shemo Studio terms, not a final approved policy.' ),
	),
	'revision-policy' => array(
		'ar' => shemo_stage20_upsert_page( 'revision-policy', 'سياسة المراجعات', shemo_stage20_policy_content( 'ar', 'revision' ), 'ar', 'سياسة المراجعات - Shemo Studio', 'مسودة سياسة مراجعات Shemo Studio للمراجعة.' ),
		'en' => shemo_stage20_upsert_page( 'revision-policy-en', 'Revision Policy', shemo_stage20_policy_content( 'en', 'revision' ), 'en', 'Revision Policy - Shemo Studio', 'Review draft of the Shemo Studio revision policy.' ),
	),
	'deposit-policy' => array(
		'ar' => shemo_stage20_upsert_page( 'deposit-policy', 'سياسة العربون', shemo_stage20_policy_content( 'ar', 'deposit' ), 'ar', 'سياسة العربون - Shemo Studio', 'مسودة سياسة عربون Shemo Studio، بدون اعتماد مالي نهائي.' ),
		'en' => shemo_stage20_upsert_page( 'deposit-policy-en', 'Deposit Policy', shemo_stage20_policy_content( 'en', 'deposit' ), 'en', 'Deposit Policy - Shemo Studio', 'Review draft of the Shemo Studio deposit policy; no final payment policy approved.' ),
	),
	'refund-policy' => array(
		'ar' => shemo_stage20_upsert_page( 'refund-policy', 'سياسة الاسترجاع', shemo_stage20_policy_content( 'ar', 'refund' ), 'ar', 'سياسة الاسترجاع - Shemo Studio', 'مسودة سياسة استرجاع Shemo Studio للمراجعة.' ),
		'en' => shemo_stage20_upsert_page( 'refund-policy-en', 'Refund Policy', shemo_stage20_policy_content( 'en', 'refund' ), 'en', 'Refund Policy - Shemo Studio', 'Review draft of the Shemo Studio refund policy.' ),
	),
	'delivery-policy' => array(
		'ar' => shemo_stage20_upsert_page( 'delivery-policy', 'سياسة التسليم', shemo_stage20_policy_content( 'ar', 'delivery' ), 'ar', 'سياسة التسليم - Shemo Studio', 'مسودة سياسة تسليم الملفات في Shemo Studio للمراجعة.' ),
		'en' => shemo_stage20_upsert_page( 'delivery-policy-en', 'Delivery Policy', shemo_stage20_policy_content( 'en', 'delivery' ), 'en', 'Delivery Policy - Shemo Studio', 'Review draft of the Shemo Studio delivery policy.' ),
	),
);

foreach ( $pages as $translation ) {
	pll_save_post_translations(
		array(
			'ar' => $translation['ar'],
			'en' => $translation['en'],
		)
	);
}

if ( get_post( 18 ) && 'publish' === get_post_status( 18 ) ) {
	update_option( 'wp_page_for_privacy_policy', 18 );
}

shemo_stage20_fix_known_english_cta_links();
shemo_stage20_enable_polylang_language_urls();

echo 'start_form_ar=' . $start_ar_id . PHP_EOL;
echo 'start_form_en=' . $start_en_id . PHP_EOL;
echo 'privacy_policy_option=' . (int) get_option( 'wp_page_for_privacy_policy' ) . PHP_EOL;
foreach ( $pages as $key => $translation ) {
	echo $key . ': ar=' . $translation['ar'] . ' en=' . $translation['en'] . PHP_EOL;
}
echo "Stage 20 pages and Start a Project forms built.\n";
