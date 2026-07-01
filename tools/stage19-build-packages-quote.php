<?php
/**
 * Build Stage 19 bilingual pages, quote forms, and SureCart test links.
 *
 * Run with LocalWP PHP CLI from the repo root:
 * php -d extension_dir="...\ext" -d extension=mysqli -d mysqli.default_port=10004 tools/stage19-build-packages-quote.php
 */

$wp_load = 'C:/Users/ahmed/Local Sites/Shemo-Studio-Clean-Start/app/public/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	fwrite( STDERR, "WordPress loader not found.\n" );
	exit( 1 );
}

require_once $wp_load;

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
	fwrite( STDERR, "Polylang functions are not available.\n" );
	exit( 1 );
}

function shemo_stage19_wrap( string $body ): string {
	return '<!-- wp:group {"tagName":"main","className":"shemo-home shemo-page shemo-stage19","layout":{"type":"default"}} -->' . "\n"
		. '<main class="wp-block-group shemo-home shemo-page shemo-stage19">' . "\n"
		. '<!-- wp:html -->' . "\n"
		. $body . "\n"
		. '<!-- /wp:html -->' . "\n"
		. '</main>' . "\n"
		. '<!-- /wp:group -->';
}

function shemo_stage19_upsert_page( string $slug, string $title, string $content, string $lang, string $seo_title, string $seo_description ): int {
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
		fwrite( STDERR, $post_id->get_error_message() . "\n" );
		exit( 1 );
	}

	pll_set_post_language( $post_id, $lang );

	update_post_meta( $post_id, '_generate-disable-headline', 'true' );
	update_post_meta( $post_id, '_generate-full-width-content', 'true' );
	update_post_meta( $post_id, 'rank_math_title', $seo_title );
	update_post_meta( $post_id, 'rank_math_description', $seo_description );

	return (int) $post_id;
}

function shemo_stage19_enable_polylang_front_page_language_urls(): void {
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

function shemo_stage19_field( int $index, string $element, string $name, string $label, string $placeholder = '', bool $required = false, array $options = array() ): array {
	$field = array(
		'index'          => $index,
		'element'        => $element,
		'attributes'     => array(
			'name'        => $name,
			'value'       => '',
			'id'          => '',
			'class'       => '',
			'placeholder' => $placeholder,
		),
		'settings'       => array(
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
		'uniqElKey'      => 'stage19_' . $name,
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

function shemo_stage19_quote_form_fields( string $lang ): array {
	$is_ar  = 'ar' === $lang;
	$fields = array(
		shemo_stage19_field( 1, 'input_text', 'client_name', $is_ar ? 'الاسم' : 'Name', $is_ar ? 'اسمك أو اسم الشركة' : 'Your name or company', true ),
		shemo_stage19_field( 2, 'input_email', 'email', $is_ar ? 'البريد الإلكتروني' : 'Email', $is_ar ? 'name@example.com' : 'name@example.com', true ),
		shemo_stage19_field(
			3,
			'select',
			'project_type',
			$is_ar ? 'نوع المشروع' : 'Project type',
			$is_ar ? 'اختر الأقرب' : 'Choose the closest',
			true,
			$is_ar
				? array(
					'video-motion'       => 'فيديو / موشن',
					'graphic-design'     => 'تصميم جرافيك',
					'branding'           => 'هوية / Branding',
					'storyboard'         => 'Storyboard / تخطيط',
					'custom-direction'   => 'اتجاه إبداعي مخصص',
					'not-sure'           => 'لست متأكدًا بعد',
				)
				: array(
					'video-motion'       => 'Video / Motion',
					'graphic-design'     => 'Graphic Design',
					'branding'           => 'Branding',
					'storyboard'         => 'Storyboard / Planning',
					'custom-direction'   => 'Custom creative direction',
					'not-sure'           => 'Not sure yet',
				)
		),
		shemo_stage19_field(
			4,
			'select',
			'package_interest',
			$is_ar ? 'الباقة الأقرب' : 'Closest package',
			$is_ar ? 'اختيار مبدئي فقط' : 'Initial choice only',
			false,
			$is_ar
				? array(
					'sketch-sprint' => 'Sketch Sprint',
					'content-kit'   => 'Content Kit',
					'launch-system' => 'Launch Visual System',
					'custom'        => 'عرض مخصص',
				)
				: array(
					'sketch-sprint' => 'Sketch Sprint',
					'content-kit'   => 'Content Kit',
					'launch-system' => 'Launch Visual System',
					'custom'        => 'Custom quote',
				)
		),
		shemo_stage19_field( 5, 'input_text', 'budget_range', $is_ar ? 'ميزانية تقريبية' : 'Approximate budget', $is_ar ? 'مثال: 300-600 USD أو غير محدد' : 'Example: 300-600 USD or not set', false ),
		shemo_stage19_field( 6, 'input_text', 'timeline', $is_ar ? 'الموعد المتوقع' : 'Expected timeline', $is_ar ? 'مثال: خلال أسبوعين' : 'Example: within two weeks', false ),
		shemo_stage19_field( 7, 'textarea', 'project_notes', $is_ar ? 'وصف مختصر' : 'Short project note', $is_ar ? 'اكتب الهدف، المنصة، وما تحتاج تسليمه.' : 'Share the goal, platform, and what needs to be delivered.', true ),
	);

	return array(
		'fields'       => $fields,
		'submitButton' => array(
			'uniqElKey'      => 'stage19_submit_' . $lang,
			'element'        => 'button',
			'attributes'     => array(
				'type'  => 'submit',
				'class' => '',
			),
			'settings'       => array(
				'align'           => 'left',
				'button_style'    => 'default',
				'container_class' => '',
				'help_message'    => '',
				'background_color'=> '#FF5A2C',
				'button_size'     => 'md',
				'color'           => '#0E0F12',
				'button_ui'       => array(
					'type'    => 'default',
					'text'    => $is_ar ? 'إرسال طلب العرض' : 'Send quote request',
					'img_url' => '',
				),
			),
			'editor_options' => array(
				'title' => 'Submit Button',
			),
		),
	);
}

function shemo_stage19_upsert_fluent_form( string $key, string $title, string $lang ): int {
	global $wpdb;

	$forms_table = $wpdb->prefix . 'fluentform_forms';
	$meta_table  = $wpdb->prefix . 'fluentform_form_meta';
	$form        = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$forms_table} WHERE title = %s LIMIT 1", $title ) );
	$now         = current_time( 'mysql' );
	$fields      = wp_json_encode( shemo_stage19_quote_form_fields( $lang ), JSON_UNESCAPED_UNICODE );

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
			'messageToShow'        => 'ar' === $lang ? 'تم استلام طلب العرض. سنراجعه ونرد عليك بخطوة تالية واضحة.' : 'Your quote request was received. We will review it and reply with a clear next step.',
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
		'layout'       => array(
			'labelPlacement'       => 'top',
			'helpMessagePlacement' => 'with_label',
			'errorMessagePlacement'=> 'inline',
			'cssClassName'         => 'shemo-quote-form',
			'asteriskPlacement'    => 'asterisk-right',
		),
		'delete_entry_on_submission' => 'no',
	);

	$metas = array(
		'stage19_form_key'             => $key,
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

function shemo_stage19_cards( array $packages, string $lang ): string {
	$is_ar = 'ar' === $lang;
	$html  = '<div class="shemo-package-grid">';

	foreach ( $packages as $package ) {
		$title    = esc_html( $package['title'] );
		$price    = esc_html( $is_ar ? $package['price_ar'] : $package['price_en'] );
		$summary  = esc_html( $is_ar ? $package['summary_ar'] : $package['summary_en'] );
		$best_for = esc_html( $is_ar ? $package['best_for_ar'] : $package['best_for_en'] );
		$quote    = $is_ar ? '/request-a-quote/?package=' : '/en/request-a-quote-en/?package=';
		$checkout = '/checkout/?mode=test&shemo_package=' . rawurlencode( $package['key'] ) . '&deposit=50';
		$html    .= '<article class="shemo-package-card">'
			. '<p class="shemo-kicker">' . esc_html( $is_ar ? 'اقتراح قابل للتعديل' : 'Editable proposal' ) . '</p>'
			. '<h3>' . $title . '</h3>'
			. '<p class="shemo-price">' . $price . '</p>'
			. '<p>' . $summary . '</p>'
			. '<p class="shemo-package-card__fit"><strong>' . esc_html( $is_ar ? 'مناسب لـ:' : 'Best for:' ) . '</strong> ' . $best_for . '</p>'
			. '<ul class="shemo-check-list">';

		foreach ( $is_ar ? $package['items_ar'] : $package['items_en'] as $item ) {
			$html .= '<li>' . esc_html( $item ) . '</li>';
		}

		$html .= '</ul><div class="shemo-button-row">'
			. '<a class="shemo-button" href="' . esc_url( $quote . $package['key'] ) . '">' . esc_html( $is_ar ? 'اطلب عرضًا' : 'Request a quote' ) . '</a>'
			. '<a class="shemo-button shemo-button--secondary" href="' . esc_url( $checkout ) . '">' . esc_html( $is_ar ? 'اختبار SureCart' : 'Test SureCart' ) . '</a>'
			. '</div></article>';
	}

	return $html . '</div>';
}

function shemo_stage19_packages_content( string $lang ): string {
	$is_ar    = 'ar' === $lang;
	$packages = shemo_stage19_package_data();
	$body     = $is_ar ? '<section class="shemo-section shemo-hero" aria-labelledby="packages-title"><div><p class="shemo-kicker">Packages</p><h1 id="packages-title">باقات تبدأ النقاش، ولا تغلقه.</h1><p class="shemo-lead">هذه أسعار مقترحة قابلة للتعديل وليست قرارًا تجاريًا نهائيًا. الهدف أن يعرف العميل حجم النطاق قبل أن نثبت عرض سعر مخصص.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/request-a-quote/">اطلب عرض سعر</a><a class="shemo-button shemo-button--secondary" href="/process/">طريقة العمل</a></div></div><div class="shemo-frame" aria-label="Package scope frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">scope / deposit / review / delivery</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="packages-title"><div><p class="shemo-kicker">Packages</p><h1 id="packages-title">Packages that start the conversation, not close it.</h1><p class="shemo-lead">These are editable suggested prices, not approved final commercial pricing. They help a client understand scope before a custom quote is confirmed.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/request-a-quote-en/">Request a Quote</a><a class="shemo-button shemo-button--secondary" href="/en/process-en/">Process</a></div></div><div class="shemo-frame" aria-label="Package scope frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">scope / deposit / review / delivery</p></div></div></section>';

	$body .= '<section class="shemo-section" aria-labelledby="package-list-title"><p class="shemo-kicker">' . esc_html( $is_ar ? 'Suggested scopes' : 'Suggested scopes' ) . '</p><h2 id="package-list-title">' . esc_html( $is_ar ? 'ثلاث نقاط دخول واضحة' : 'Three clear entry points' ) . '</h2>' . shemo_stage19_cards( $packages, $lang ) . '</section>';

	$body .= $is_ar
		? '<section class="shemo-section shemo-split"><div><p class="shemo-kicker">SureCart Free</p><h2>تدفق الدفع هنا اختبار فقط.</h2></div><div><p class="shemo-lead">أزرار SureCart تقود إلى checkout المحلي في وضع اختبار، بدون مفاتيح إنتاج وبدون شراء. فكرة العربون 50% معروضة كنص مقترح فقط، ولا تصبح سياسة بيع معتمدة إلا بموافقة صريحة لاحقة.</p><ul class="shemo-check-list"><li>SureCart Free/Launch معتمد كأداة.</li><li>الأسعار والعربون وسير البيع النهائي غير معتمدين بعد.</li><li>الخطوة العملية الآمنة الآن: طلب عرض سعر أولًا، ثم دفع اختبار عند توفر إعداد SureCart كامل.</li></ul></div></section>'
		: '<section class="shemo-section shemo-split"><div><p class="shemo-kicker">SureCart Free</p><h2>The checkout flow here is test-only.</h2></div><div><p class="shemo-lead">SureCart buttons point to the local checkout in test mode, with no production keys and no purchase. The 50% deposit idea is only proposed copy and does not become an approved sales policy without explicit later approval.</p><ul class="shemo-check-list"><li>SureCart Free/Launch is the approved tool.</li><li>Pricing, deposit rules, and final sales flow are not approved yet.</li><li>The safe next step now is quote-first, then test payment when SureCart is fully configured.</li></ul></div></section>';

	return shemo_stage19_wrap( $body );
}

function shemo_stage19_quote_content( string $lang, int $form_id ): string {
	$is_ar = 'ar' === $lang;
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="quote-title"><div><p class="shemo-kicker">Request a Quote</p><h1 id="quote-title">اكتب نطاقًا سريعًا، وسنحوّله إلى عرض واضح.</h1><p class="shemo-lead">النموذج مبسّط عمدًا لحدود Fluent Forms Free: صفحة واحدة، بدون save & resume، وبدون PDF تلقائي. المطلوب فقط معلومات كافية لبدء تقدير واقعي.</p></div><div class="shemo-frame" aria-label="Quote frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">brief / scope / quote / next step</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="quote-title"><div><p class="shemo-kicker">Request a Quote</p><h1 id="quote-title">Send a quick scope, and we will turn it into a clear quote.</h1><p class="shemo-lead">The form is intentionally simple for Fluent Forms Free: one page, no save and resume, and no automatic PDF generation. We only ask for enough detail to estimate responsibly.</p></div><div class="shemo-frame" aria-label="Quote frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">brief / scope / quote / next step</p></div></div></section>';

	$body .= '<section class="shemo-section shemo-form-section" aria-labelledby="quote-form-title"><div><p class="shemo-kicker">Form</p><h2 id="quote-form-title">' . esc_html( $is_ar ? 'طلب عرض سعر سريع' : 'Quick quote request' ) . '</h2><p>' . esc_html( $is_ar ? 'بعد الإرسال، القراءة الأولى تكون للنطاق والموعد وليس اعتماد السعر النهائي.' : 'After submission, the first review is about scope and timing, not final price approval.' ) . '</p></div><div class="shemo-form-shell">[fluentform id="' . (int) $form_id . '"]</div></section>';

	return shemo_stage19_wrap( $body );
}

function shemo_stage19_process_content( string $lang ): string {
	$is_ar = 'ar' === $lang;
	$steps = $is_ar
		? array( 'Brief قصير يحدد الهدف والمنصة والموعد.', 'اتجاه بصري أو sketch يثبت القرار قبل الإنتاج.', 'تنفيذ أولي مع نقطة مراجعة محددة.', 'مراجعة نهائية وتجهيز الملفات.', 'تسليم منظم مع ملاحظات استخدام واضحة.' )
		: array( 'A short brief defines the goal, platform, and timing.', 'Visual direction or sketch locks the decision before production.', 'First production pass with a defined review point.', 'Final review and export preparation.', 'Organized handoff with clear usage notes.' );
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="process-title"><div><p class="shemo-kicker">Process</p><h1 id="process-title">طريقة عمل مرئية ومنظمة.</h1><p class="shemo-lead">نقلّل المفاجآت: نبدأ بسؤال واضح، نختبر الاتجاه قبل التنفيذ الثقيل، ثم نسلّم ملفات عملية.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/request-a-quote/">اطلب عرض سعر</a><a class="shemo-button shemo-button--secondary" href="/work/">شاهد العمل</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">01 / 02 / 03 / 04 / 05</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="process-title"><div><p class="shemo-kicker">Process</p><h1 id="process-title">A visual, organized way to work.</h1><p class="shemo-lead">We reduce surprises: start with a clear question, test direction before heavy production, then hand over practical files.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/request-a-quote-en/">Request a Quote</a><a class="shemo-button shemo-button--secondary" href="/en/work/">View Work</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">01 / 02 / 03 / 04 / 05</p></div></div></section>';

	$body .= '<section class="shemo-section" aria-labelledby="steps-title"><p class="shemo-kicker">Steps</p><h2 id="steps-title">' . esc_html( $is_ar ? 'من الفكرة إلى التسليم' : 'From idea to handoff' ) . '</h2><div class="shemo-timeline">';
	foreach ( $steps as $index => $step ) {
		$body .= '<article><span>' . esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span><p>' . esc_html( $step ) . '</p></article>';
	}
	$body .= '</div></section>';
	$body .= $is_ar
		? '<section class="shemo-section shemo-split"><div><p class="shemo-kicker">Boundaries</p><h2>المراجعات جزء من النطاق، وليست بابًا مفتوحًا بلا نهاية.</h2></div><div><p class="shemo-lead">كل عرض سعر يجب أن يوضح عدد المراجعات، الملفات المطلوبة، وما الذي يعتبر تغيير نطاق. هذه الصياغة عملية وليست سياسة نهائية معتمدة بعد.</p></div></section>'
		: '<section class="shemo-section shemo-split"><div><p class="shemo-kicker">Boundaries</p><h2>Reviews are part of scope, not an endless open door.</h2></div><div><p class="shemo-lead">Each quote should state review count, required files, and what counts as a scope change. This is working copy, not an approved final policy yet.</p></div></section>';

	return shemo_stage19_wrap( $body );
}

function shemo_stage19_testimonials_content( string $lang ): string {
	$is_ar = 'ar' === $lang;
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="testimonials-title"><div><p class="shemo-kicker">Testimonials</p><h1 id="testimonials-title">لا توجد شهادات منشورة بعد.</h1><p class="shemo-lead">لن نخترع أسماء عملاء أو اقتباسات أو نتائج. هذه الصفحة مساحة مستقبلية لشهادات حقيقية عند توفر موافقة نشر واضحة.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/work/">شاهد مشاريع Demo/Concept</a><a class="shemo-button shemo-button--secondary" href="/request-a-quote/">اطلب عرض سعر</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">future proof / no fake proof</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="testimonials-title"><div><p class="shemo-kicker">Testimonials</p><h1 id="testimonials-title">No published testimonials yet.</h1><p class="shemo-lead">We will not invent client names, quotes, or results. This page is a future space for real testimonials once clear publishing approval exists.</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/work/">View Demo/Concept Work</a><a class="shemo-button shemo-button--secondary" href="/en/request-a-quote-en/">Request a Quote</a></div></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">future proof / no fake proof</p></div></div></section>';
	$body .= '<section class="shemo-section"><div class="shemo-empty-state"><p class="shemo-kicker">' . esc_html( $is_ar ? 'شفافية' : 'Transparency' ) . '</p><h2>' . esc_html( $is_ar ? 'الثقة هنا تأتي من وضوح العملية والعمل التجريبي الموسوم.' : 'Trust here comes from clear process and clearly labeled demo work.' ) . '</h2><p>' . esc_html( $is_ar ? 'إلى أن تتوفر شهادات حقيقية، نستخدم صفحة Work لعرض طريقة التفكير والتنفيذ بدون ادعاء أنها أعمال عملاء.' : 'Until real testimonials exist, the Work page shows thinking and craft without pretending the pieces are client commissions.' ) . '</p></div></section>';

	return shemo_stage19_wrap( $body );
}

function shemo_stage19_faq_content( string $lang ): string {
	$is_ar = 'ar' === $lang;
	$faqs  = $is_ar
		? array(
			array( 'هل الأسعار نهائية؟', 'لا. الأسعار المعروضة اقتراحات قابلة للتعديل، وليست قرارًا تجاريًا نهائيًا. كل مشروع يحتاج نطاقًا مكتوبًا قبل تثبيت السعر.' ),
			array( 'هل يمكن الدفع مباشرة؟', 'حاليًا التدفق عبر SureCart اختبار فقط على LocalWP، بدون مفاتيح إنتاج وبدون شراء. المسار العملي هو طلب عرض سعر أولًا.' ),
			array( 'هل تستخدمون Fluent Forms Pro؟', 'لا. النموذج الحالي مبني على Fluent Forms Free، لذلك هو صفحة واحدة بدون save & resume أو PDF تلقائي.' ),
			array( 'هل توجد شهادات عملاء؟', 'لا توجد شهادات منشورة بعد. لن ننشر أسماء أو اقتباسات أو نتائج إلا إذا كانت حقيقية ومصرحًا بنشرها.' ),
			array( 'ما الذي يحدث بعد طلب العرض؟', 'نراجع الهدف والمنصة والموعد، ثم نرد بخطوة تالية: سؤال توضيحي، نطاق مقترح، أو عرض سعر مبدئي.' ),
		)
		: array(
			array( 'Are these final prices?', 'No. The prices shown are editable suggestions, not approved final commercial pricing. Each project needs written scope before price is confirmed.' ),
			array( 'Can I pay directly?', 'For now the SureCart flow is test-only on LocalWP, with no production keys and no purchase. The practical path is quote-first.' ),
			array( 'Are you using Fluent Forms Pro?', 'No. The current form uses Fluent Forms Free, so it is one page with no save and resume or automatic PDF generation.' ),
			array( 'Do you have client testimonials?', 'No published testimonials yet. We will not publish names, quotes, or results unless they are real and approved for publication.' ),
			array( 'What happens after I request a quote?', 'We review the goal, platform, and timing, then reply with a next step: a clarifying question, proposed scope, or initial quote.' ),
		);
	$body  = $is_ar
		? '<section class="shemo-section shemo-hero" aria-labelledby="faq-title"><div><p class="shemo-kicker">FAQ</p><h1 id="faq-title">أسئلة سريعة قبل البدء.</h1><p class="shemo-lead">إجابات مباشرة عن الباقات، النماذج، SureCart، والشهادات بدون تضخيم أو ادعاءات.</p></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">clear answers / clear scope</p></div></div></section>'
		: '<section class="shemo-section shemo-hero" aria-labelledby="faq-title"><div><p class="shemo-kicker">FAQ</p><h1 id="faq-title">Quick questions before we begin.</h1><p class="shemo-lead">Direct answers about packages, forms, SureCart, and testimonials without hype or invented claims.</p></div><div class="shemo-frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">clear answers / clear scope</p></div></div></section>';
	$body .= '<section class="shemo-section shemo-faq-list">';
	foreach ( $faqs as $faq ) {
		$body .= '<details><summary>' . esc_html( $faq[0] ) . '</summary><p>' . esc_html( $faq[1] ) . '</p></details>';
	}
	$body .= '</section>';

	return shemo_stage19_wrap( $body );
}

function shemo_stage19_package_data(): array {
	return array(
		array(
			'key'         => 'sketch-sprint',
			'title'       => 'Sketch Sprint',
			'price_ar'    => 'اقتراح: 150-250 USD',
			'price_en'    => 'Suggested: USD 150-250',
			'summary_ar'  => 'جلسة تصور سريعة لتحويل فكرة غامضة إلى اتجاه مرئي قابل للمراجعة.',
			'summary_en'  => 'A focused visualization sprint that turns a vague idea into a reviewable visual direction.',
			'best_for_ar' => 'اسكتشات، storyboard أولي، أو اتجاه حملة صغير.',
			'best_for_en' => 'Sketches, early storyboard, or a small campaign direction.',
			'items_ar'    => array( 'Brief قصير', '2-3 rough directions', 'اتجاه واحد مصقول', 'مراجعة واحدة' ),
			'items_en'    => array( 'Short brief', '2-3 rough directions', 'One refined direction', 'One review round' ),
		),
		array(
			'key'         => 'content-kit',
			'title'       => 'Content Kit',
			'price_ar'    => 'اقتراح: 350-650 USD',
			'price_en'    => 'Suggested: USD 350-650',
			'summary_ar'  => 'مجموعة تصميم/موشن صغيرة لإطلاق فكرة أو حملة اجتماعية بشكل متماسك.',
			'summary_en'  => 'A compact design or motion kit for launching an idea or social campaign with visual consistency.',
			'best_for_ar' => 'منشورات، فيديو قصير، أو kit إطلاق مصغر.',
			'best_for_en' => 'Posts, short video, or a compact launch kit.',
			'items_ar'    => array( 'Scope مكتوب', '3-6 قطع حسب النوع', 'مراجعتان', 'ملفات نشر منظمة' ),
			'items_en'    => array( 'Written scope', '3-6 pieces depending on type', 'Two review rounds', 'Organized publish-ready files' ),
		),
		array(
			'key'         => 'launch-system',
			'title'       => 'Launch Visual System',
			'price_ar'    => 'اقتراح: 900-1,500 USD',
			'price_en'    => 'Suggested: USD 900-1,500',
			'summary_ar'  => 'نظام بصري أوسع يجمع direction، تصميمات، وموشن/Storyboard حسب احتياج الإطلاق.',
			'summary_en'  => 'A wider visual system combining direction, design, and motion/storyboard depending on launch needs.',
			'best_for_ar' => 'إطلاق خدمة/منتج أو تحديث حضور بصري كامل.',
			'best_for_en' => 'Service/product launch or a fuller visual presence refresh.',
			'items_ar'    => array( 'Discovery مختصر', 'Visual direction', 'مجموعة تسليمات متعددة', 'مراجعات مجدولة', 'Handoff منظم' ),
			'items_en'    => array( 'Compact discovery', 'Visual direction', 'Multiple deliverables', 'Scheduled reviews', 'Organized handoff' ),
		),
	);
}

$quote_ar_id = shemo_stage19_upsert_fluent_form( 'stage19_quote_ar', 'Shemo Request a Quote - AR', 'ar' );
$quote_en_id = shemo_stage19_upsert_fluent_form( 'stage19_quote_en', 'Shemo Request a Quote - EN', 'en' );

$pages = array(
	'packages' => array(
		'ar' => shemo_stage19_upsert_page( 'packages', 'الباقات', shemo_stage19_packages_content( 'ar' ), 'ar', 'باقات Shemo Studio - أسعار مقترحة قابلة للتعديل', 'باقات وأسعار مقترحة قابلة للتعديل من Shemo Studio، مع ربط اختبار SureCart وطلب عرض سعر.' ),
		'en' => shemo_stage19_upsert_page( 'packages-en', 'Packages', shemo_stage19_packages_content( 'en' ), 'en', 'Shemo Studio Packages - Editable suggested pricing', 'Editable suggested package pricing for Shemo Studio, with test-only SureCart links and quote-first flow.' ),
	),
	'request-a-quote' => array(
		'ar' => shemo_stage19_upsert_page( 'request-a-quote', 'طلب عرض سعر', shemo_stage19_quote_content( 'ar', $quote_ar_id ), 'ar', 'طلب عرض سعر - Shemo Studio', 'نموذج طلب عرض سعر سريع من Shemo Studio مبني على Fluent Forms Free.' ),
		'en' => shemo_stage19_upsert_page( 'request-a-quote-en', 'Request a Quote', shemo_stage19_quote_content( 'en', $quote_en_id ), 'en', 'Request a Quote - Shemo Studio', 'Quick quote request form for Shemo Studio built with Fluent Forms Free.' ),
	),
	'process' => array(
		'ar' => shemo_stage19_upsert_page( 'process', 'طريقة العمل', shemo_stage19_process_content( 'ar' ), 'ar', 'طريقة العمل - Shemo Studio', 'طريقة عمل Shemo Studio من brief إلى sketch وتنفيذ وتسليم.' ),
		'en' => shemo_stage19_upsert_page( 'process-en', 'Process', shemo_stage19_process_content( 'en' ), 'en', 'Process - Shemo Studio', 'Shemo Studio process from brief to sketch, production, review, and handoff.' ),
	),
	'testimonials' => array(
		'ar' => shemo_stage19_upsert_page( 'testimonials', 'الشهادات', shemo_stage19_testimonials_content( 'ar' ), 'ar', 'الشهادات - Shemo Studio', 'صفحة شهادات Shemo Studio بشفافية: لا توجد شهادات منشورة بعد ولا اقتباسات وهمية.' ),
		'en' => shemo_stage19_upsert_page( 'testimonials-en', 'Testimonials', shemo_stage19_testimonials_content( 'en' ), 'en', 'Testimonials - Shemo Studio', 'Shemo Studio testimonials page with transparent no-testimonials-yet messaging.' ),
	),
	'faq' => array(
		'ar' => shemo_stage19_upsert_page( 'faq', 'الأسئلة الشائعة', shemo_stage19_faq_content( 'ar' ), 'ar', 'الأسئلة الشائعة - Shemo Studio', 'أسئلة شائعة عن باقات Shemo Studio، Fluent Forms Free، SureCart، وطريقة طلب عرض السعر.' ),
		'en' => shemo_stage19_upsert_page( 'faq-en', 'FAQ', shemo_stage19_faq_content( 'en' ), 'en', 'FAQ - Shemo Studio', 'FAQ about Shemo Studio packages, Fluent Forms Free, SureCart, and quote requests.' ),
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

shemo_stage19_enable_polylang_front_page_language_urls();

echo 'quote_form_ar=' . $quote_ar_id . PHP_EOL;
echo 'quote_form_en=' . $quote_en_id . PHP_EOL;
foreach ( $pages as $key => $translation ) {
	echo $key . ': ar=' . $translation['ar'] . ' en=' . $translation['en'] . PHP_EOL;
}
echo "Stage 19 pages and quote forms built.\n";
