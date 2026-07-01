<?php
/**
 * Build the bilingual Stage 17 About and Services pages in WordPress.
 *
 * Run with:
 * wp eval-file tools/stage17-build-about-services.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
	WP_CLI::error( 'Polylang functions are not available.' );
}

function shemo_stage17_page_path( string $slug, int $parent_id = 0 ): string {
	if ( ! $parent_id ) {
		return $slug;
	}

	$parent = get_post( $parent_id );

	return $parent ? trim( $parent->post_name . '/' . $slug, '/' ) : $slug;
}

function shemo_stage17_upsert_page( string $slug, string $title, string $content, string $lang, string $seo_title, string $seo_description, int $parent_id = 0 ): int {
	$page_path = shemo_stage17_page_path( $slug, $parent_id );
	$page      = get_page_by_path( $page_path, OBJECT, 'page' );

	$postarr = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_parent'  => $parent_id,
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

function shemo_stage17_enable_polylang_front_page_language_urls(): void {
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

function shemo_stage17_link( string $url, string $label, string $classes = 'shemo-button' ): string {
	return '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
}

function shemo_stage17_list( array $items, string $class = 'shemo-check-list' ): string {
	$html = '<ul class="' . esc_attr( $class ) . '">';

	foreach ( $items as $item ) {
		$html .= '<li>' . esc_html( $item ) . '</li>';
	}

	return $html . '</ul>';
}

function shemo_stage17_workflow( array $steps ): string {
	$html  = '<div class="shemo-workflow">';
	$count = 1;

	foreach ( $steps as $step ) {
		$html .= '<div class="shemo-workflow__step"><span>' . esc_html( str_pad( (string) $count, 2, '0', STR_PAD_LEFT ) ) . '</span><p>' . esc_html( $step ) . '</p></div>';
		$count++;
	}

	return $html . '</div>';
}

function shemo_stage17_wrap( string $body ): string {
	return '<!-- wp:group {"tagName":"main","className":"shemo-home shemo-page","layout":{"type":"default"}} -->' . "\n"
		. '<main class="wp-block-group shemo-home shemo-page">' . "\n"
		. '<!-- wp:html -->' . "\n"
		. $body . "\n"
		. '<!-- /wp:html -->' . "\n"
		. '</main>' . "\n"
		. '<!-- /wp:group -->';
}

function shemo_stage17_about_content( string $lang ): string {
	if ( 'ar' === $lang ) {
		$body = <<<'HTML'
<section class="shemo-section shemo-hero" aria-labelledby="about-title">
	<div>
		<p class="shemo-kicker">About Shemo Studio</p>
		<h1 id="about-title">استوديو صغير برؤية مؤسس واضحة</h1>
		<p class="shemo-lead">Shemo Studio يقوده شيمو كصوت إبداعي ظاهر: شخص يفكر معك في الفكرة، يرسم اتجاهها، ثم يحوّلها إلى تصميم أو فيديو جاهز للاستخدام. الحضور الشخصي هنا وسيلة للثقة، وليس تحويل الموقع إلى سيرة شخصية.</p>
		<div class="shemo-hero__actions">
			<a class="shemo-button" href="/services/">استكشف الخدمات</a>
			<a class="shemo-button shemo-button--secondary" href="/start-a-project/">ابدأ مشروعك</a>
		</div>
	</div>
	<div class="shemo-frame" aria-label="إطار يوضح حضور المؤسس داخل نظام الاستوديو">
		<div class="shemo-frame__screen"><p class="shemo-frame__label">Founder eye / studio system / clear delivery</p></div>
	</div>
</section>

<section class="shemo-section shemo-narrative" aria-labelledby="founder-title">
	<div class="shemo-narrative__body">
		<p class="shemo-kicker">Founder-led, not founder-only</p>
		<h2 id="founder-title">شيمو يقود الاتجاه. الاستوديو يبني النظام.</h2>
		<p>في المشاريع الإبداعية الصغيرة والمتوسطة، الثقة لا تأتي من أسماء كبيرة مكتوبة على الصفحة، بل من شخص واضح يتحمّل مسؤولية القرار البصري. لذلك يظهر شيمو كمؤسس وقائد إبداعي: يراجع الفكرة، يضع المراجع، يختبر الإيقاع، ويتأكد أن التسليم يخدم الهدف.</p>
		<p>في نفس الوقت، Shemo Studio ليس براند شخصي مغلق. طريقة العمل مصممة كاستوديو بوتيك يمكنه التوسع بفريق أو متعاونين عند الحاجة، مع بقاء الاتجاه والمراجعة الإبداعية منضبطين.</p>
	</div>
	<aside class="shemo-aside-note"><strong>ما نعد به</strong>تفكير بصري واضح، نطاق عمل مكتوب، مراجعات محددة، وتسليم منظم. لا نستخدم أسماء عملاء أو نتائج أو شهادات غير حقيقية لبناء الثقة.</aside>
</section>

<section class="shemo-section" aria-labelledby="principles-title">
	<p class="shemo-kicker">Principles</p>
	<h2 id="principles-title">ما يحكم طريقة العمل</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>وضوح قبل الجمال</h3><p>الشكل مهم، لكنه يبدأ من رسالة مفهومة وجمهور واضح ومنصة محددة.</p></article>
		<article class="shemo-mini-card"><h3>اسكتش قبل الإنتاج</h3><p>نحوّل الفكرة إلى مسودة يمكن رؤيتها ومناقشتها قبل الدخول في التنفيذ الثقيل.</p></article>
		<article class="shemo-mini-card"><h3>نظام لا فوضى</h3><p>كل مشروع يحتاج brief، اتجاه، مراجعات، وتسليم ملفات مرتبة.</p></article>
	</div>
</section>

<section class="shemo-section shemo-split" aria-labelledby="work-style-title">
	<div>
		<p class="shemo-kicker">Working Style</p>
		<h2 id="work-style-title">قريب بما يكفي ليفهم الفكرة، ومنظم بما يكفي ليسلّمها.</h2>
	</div>
	<div>
		<p class="shemo-lead">النبرة المقصودة بسيطة: شراكة إبداعية مباشرة، بدون لغة وكالات ضخمة وبدون وعود مبالغ فيها. نريد للعميل أن يعرف ماذا يحدث في كل خطوة: لماذا اخترنا هذا الاتجاه؟ ما الذي سيتم تسليمه؟ وما حدود المراجعة؟</p>
		<ul class="shemo-pill-list"><li>Video</li><li>Motion</li><li>Design</li><li>Illustration</li><li>Brand direction</li><li>Planning</li></ul>
	</div>
</section>

<section class="shemo-section" aria-labelledby="about-process-title">
	<p class="shemo-kicker">Process</p>
	<h2 id="about-process-title">من محادثة أولى إلى ملف جاهز</h2>
	<div class="shemo-workflow">
		<div class="shemo-workflow__step"><span>01</span><p>نفهم الهدف والمنصة والجمهور والقيود.</p></div>
		<div class="shemo-workflow__step"><span>02</span><p>نبني اتجاهًا بصريًا ومراجع أولية.</p></div>
		<div class="shemo-workflow__step"><span>03</span><p>ننفذ بتدرج مع نقاط مراجعة واضحة.</p></div>
		<div class="shemo-workflow__step"><span>04</span><p>نسلّم الملفات بصيغ عملية للنشر والاستخدام.</p></div>
	</div>
</section>

<section class="shemo-section" aria-labelledby="about-cta-title">
	<div class="shemo-cta-panel">
		<p class="shemo-kicker">ابدأ من فكرة واضحة</p>
		<h2 id="about-cta-title">لو مشروعك يحتاج عينًا إبداعية تقوده، فلنبدأ بالـbrief.</h2>
		<p class="shemo-lead">اكتب لنا ما تريد بناءه، وسنقترح الشكل الأنسب: فيديو، تصميم، storyboard، branding، أو اتجاه إبداعي مخصص.</p>
		<div class="shemo-button-row"><a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a><a class="shemo-button shemo-button--secondary" href="/services/">شاهد الخدمات</a></div>
	</div>
</section>
HTML;
		return shemo_stage17_wrap( $body );
	}

	$body = <<<'HTML'
<section class="shemo-section shemo-hero" aria-labelledby="about-title">
	<div>
		<p class="shemo-kicker">About Shemo Studio</p>
		<h1 id="about-title">A small studio with visible founder direction</h1>
		<p class="shemo-lead">Shemo Studio is led by Shemo as a visible creative voice: someone who thinks through the idea with you, sketches the direction, and turns it into usable design or video work. The founder presence builds trust without turning the site into a personal brand only.</p>
		<div class="shemo-hero__actions">
			<a class="shemo-button" href="/en/services-en/">Explore Services</a>
			<a class="shemo-button shemo-button--secondary" href="/en/start-a-project/">Start a Project</a>
		</div>
	</div>
	<div class="shemo-frame" aria-label="Frame showing founder presence inside a studio system">
		<div class="shemo-frame__screen"><p class="shemo-frame__label">Founder eye / studio system / clear delivery</p></div>
	</div>
</section>

<section class="shemo-section shemo-narrative" aria-labelledby="founder-title">
	<div class="shemo-narrative__body">
		<p class="shemo-kicker">Founder-led, not founder-only</p>
		<h2 id="founder-title">Shemo leads the direction. The studio gives it a system.</h2>
		<p>For small and mid-sized creative projects, trust does not come from invented logos on a page. It comes from a clear person taking responsibility for the visual choices. Shemo appears as founder and creative lead: reviewing the idea, setting references, shaping rhythm, and making sure delivery serves the goal.</p>
		<p>At the same time, Shemo Studio is not a closed personal brand. The work is structured as a boutique studio that can scale with collaborators when a project needs more hands, while keeping direction and review consistent.</p>
	</div>
	<aside class="shemo-aside-note"><strong>What we promise</strong>Clear visual thinking, written scope, defined review points, and organized delivery. We do not use fake client names, metrics, or testimonials to create trust.</aside>
</section>

<section class="shemo-section" aria-labelledby="principles-title">
	<p class="shemo-kicker">Principles</p>
	<h2 id="principles-title">What shapes the work</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>Clarity before beauty</h3><p>The visual layer matters, but it starts with a clear message, audience, and platform.</p></article>
		<article class="shemo-mini-card"><h3>Sketch before production</h3><p>We turn the idea into something visible and reviewable before heavy production begins.</p></article>
		<article class="shemo-mini-card"><h3>System over noise</h3><p>Every project needs a brief, direction, review rhythm, and organized files.</p></article>
	</div>
</section>

<section class="shemo-section shemo-split" aria-labelledby="work-style-title">
	<div>
		<p class="shemo-kicker">Working Style</p>
		<h2 id="work-style-title">Close enough to understand the idea, structured enough to deliver it.</h2>
	</div>
	<div>
		<p class="shemo-lead">The intended tone is simple: direct creative partnership without oversized agency language or inflated promises. You should know what is happening at each step: why this direction, what will be delivered, and where the review boundaries are.</p>
		<ul class="shemo-pill-list"><li>Video</li><li>Motion</li><li>Design</li><li>Illustration</li><li>Brand direction</li><li>Planning</li></ul>
	</div>
</section>

<section class="shemo-section" aria-labelledby="about-process-title">
	<p class="shemo-kicker">Process</p>
	<h2 id="about-process-title">From first conversation to ready files</h2>
	<div class="shemo-workflow">
		<div class="shemo-workflow__step"><span>01</span><p>We understand the goal, platform, audience, and constraints.</p></div>
		<div class="shemo-workflow__step"><span>02</span><p>We shape visual direction and early references.</p></div>
		<div class="shemo-workflow__step"><span>03</span><p>We build in stages with clear review points.</p></div>
		<div class="shemo-workflow__step"><span>04</span><p>We deliver practical files for publishing and use.</p></div>
	</div>
</section>

<section class="shemo-section" aria-labelledby="about-cta-title">
	<div class="shemo-cta-panel">
		<p class="shemo-kicker">Start with a clear idea</p>
		<h2 id="about-cta-title">If your project needs a creative eye to lead it, start with the brief.</h2>
		<p class="shemo-lead">Tell us what you need to build, and we will recommend the right shape: video, design, storyboard, branding, or custom creative direction.</p>
		<div class="shemo-button-row"><a class="shemo-button" href="/en/start-a-project/">Start a Project</a><a class="shemo-button shemo-button--secondary" href="/en/services-en/">View Services</a></div>
	</div>
</section>
HTML;

	return shemo_stage17_wrap( $body );
}

function shemo_stage17_services_overview( string $lang, array $services ): string {
	$is_ar = 'ar' === $lang;
	$base  = $is_ar ? '/services/' : '/en/services-en/';
	$cards = '';

	foreach ( $services as $service ) {
		$slug   = $is_ar ? $service['ar_slug'] : $service['en_slug'];
		$title  = $is_ar ? $service['title'] : $service['title'];
		$label  = $is_ar ? $service['ar_label'] : $service['en_label'];
		$intro  = $is_ar ? $service['ar_intro'] : $service['en_intro'];
		$button = $is_ar ? 'افتح الخدمة' : 'Open service';
		$cards .= '<article class="shemo-mini-card shemo-service-card"><span class="shemo-page__eyebrow">' . esc_html( $label ) . '</span><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $intro ) . '</p>' . shemo_stage17_link( $base . $slug . '/', $button, 'shemo-button shemo-button--secondary' ) . '</article>';
	}

	if ( $is_ar ) {
		$body = '<section class="shemo-section shemo-hero" aria-labelledby="services-title"><div><p class="shemo-kicker">Services</p><h1 id="services-title">خدمات تربط الفكرة بالشكل النهائي</h1><p class="shemo-lead">كل خدمة هنا مصممة لتقليل الضباب حول المشروع: ما المشكلة؟ ماذا سنسلّم؟ وكيف نمشي من أول brief إلى ملفات جاهزة للنشر؟</p><div class="shemo-hero__actions"><a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a><a class="shemo-button shemo-button--secondary" href="/about/">اعرف الاستوديو</a></div></div><div class="shemo-frame" aria-label="إطار خدمات Shemo Studio"><div class="shemo-frame__screen"><p class="shemo-frame__label">Plan / sketch / build / deliver</p></div></div></section>'
			. '<section class="shemo-section" aria-labelledby="service-list-title"><p class="shemo-kicker">Overview</p><h2 id="service-list-title">اختر نقطة الدخول المناسبة</h2><div class="shemo-grid">' . $cards . '</div></section>'
			. '<section class="shemo-section shemo-split" aria-labelledby="choose-title"><div><p class="shemo-kicker">How to choose</p><h2 id="choose-title">لو لا تعرف الخدمة المناسبة بعد، ابدأ من الهدف.</h2></div><div><p class="shemo-lead">أحيانًا يبدأ المشروع كفيديو، ثم يحتاج storyboard. وأحيانًا يبدأ كهوية، ثم يحتاج مواد اجتماعية وحركة. سنساعدك على تحديد النطاق قبل تثبيت العرض.</p>' . shemo_stage17_list( array( 'لإطلاق خدمة أو منتج: Branding + Graphic Design + Motion.', 'لفيديو تعريفي أو إعلان: Storyboarding + Video Editing & Motion.', 'لفكرة تحتاج تصورًا سريعًا: Sketch & Illustration أو Creative Direction.' ) ) . '</div></section>'
			. '<section class="shemo-section"><div class="shemo-cta-panel"><p class="shemo-kicker">CTA</p><h2>لديك هدف وليس اسم خدمة؟ هذا كاف كبداية.</h2><p class="shemo-lead">أرسل الفكرة والموعد والمنصة، وسنقترح الطريق الأقصر من brief إلى تسليم واضح.</p><div class="shemo-button-row"><a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a><a class="shemo-button shemo-button--secondary" href="/contact/">تواصل معنا</a></div></div></section>';

		return shemo_stage17_wrap( $body );
	}

	$body = '<section class="shemo-section shemo-hero" aria-labelledby="services-title"><div><p class="shemo-kicker">Services</p><h1 id="services-title">Services that connect the idea to the finished frame</h1><p class="shemo-lead">Each service is built to reduce uncertainty: what problem are we solving, what will be delivered, and how do we move from brief to publish-ready files?</p><div class="shemo-hero__actions"><a class="shemo-button" href="/en/start-a-project/">Start a Project</a><a class="shemo-button shemo-button--secondary" href="/en/about-en/">About the Studio</a></div></div><div class="shemo-frame" aria-label="Shemo Studio services frame"><div class="shemo-frame__screen"><p class="shemo-frame__label">Plan / sketch / build / deliver</p></div></div></section>'
		. '<section class="shemo-section" aria-labelledby="service-list-title"><p class="shemo-kicker">Overview</p><h2 id="service-list-title">Choose the right entry point</h2><div class="shemo-grid">' . $cards . '</div></section>'
		. '<section class="shemo-section shemo-split" aria-labelledby="choose-title"><div><p class="shemo-kicker">How to choose</p><h2 id="choose-title">If you do not know the right service yet, start with the goal.</h2></div><div><p class="shemo-lead">Sometimes a project starts as a video and needs a storyboard. Sometimes it starts as an identity and needs social assets and motion. We help define the scope before locking the proposal.</p>' . shemo_stage17_list( array( 'For a product or service launch: Branding + Graphic Design + Motion.', 'For an explainer or ad: Storyboarding + Video Editing & Motion.', 'For an idea that needs quick visualization: Sketch & Illustration or Creative Direction.' ) ) . '</div></section>'
		. '<section class="shemo-section"><div class="shemo-cta-panel"><p class="shemo-kicker">CTA</p><h2>Have a goal, not a service name? That is enough to begin.</h2><p class="shemo-lead">Send the idea, timeline, and platform. We will suggest the shortest route from brief to clear delivery.</p><div class="shemo-button-row"><a class="shemo-button" href="/en/start-a-project/">Start a Project</a><a class="shemo-button shemo-button--secondary" href="/en/contact/">Contact</a></div></div></section>';

	return shemo_stage17_wrap( $body );
}

function shemo_stage17_service_content( array $service, string $lang ): string {
	$is_ar       = 'ar' === $lang;
	$title       = $service['title'];
	$label       = $is_ar ? $service['ar_label'] : $service['en_label'];
	$intro       = $is_ar ? $service['ar_intro'] : $service['en_intro'];
	$problem     = $is_ar ? $service['ar_problem'] : $service['en_problem'];
	$deliver     = $is_ar ? $service['ar_deliverables'] : $service['en_deliverables'];
	$process     = $is_ar ? $service['ar_process'] : $service['en_process'];
	$fit         = $is_ar ? $service['ar_fit'] : $service['en_fit'];
	$servicesurl = $is_ar ? '/services/' : '/en/services-en/';
	$starturl    = $is_ar ? '/start-a-project/' : '/en/start-a-project/';
	$startlabel  = $is_ar ? 'ابدأ مشروعك' : 'Start a Project';
	$backlabel   = $is_ar ? 'كل الخدمات' : 'All services';
	$problem_h   = $is_ar ? 'المشكلة التي نعالجها' : 'The problem this solves';
	$deliver_h   = $is_ar ? 'ما يتم تسليمه' : 'What gets delivered';
	$process_h   = $is_ar ? 'طريقة العمل' : 'How the work runs';
	$fit_h       = $is_ar ? 'متى تكون مناسبة؟' : 'When this fits';
	$cta_h       = $is_ar ? 'لنجعل النطاق واضحًا قبل التنفيذ.' : 'Let us make the scope clear before production.';
	$cta_p       = $is_ar ? 'أرسل الهدف والمنصة والموعد المتوقع، وسنقترح شكل الخدمة المناسب بدون تضخيم للنطاق.' : 'Send the goal, platform, and expected timeline. We will recommend the right shape without inflating the scope.';

	$body = '<section class="shemo-section shemo-hero" aria-labelledby="service-title"><div><p class="shemo-kicker">' . esc_html( $label ) . '</p><h1 id="service-title">' . esc_html( $title ) . '</h1><p class="shemo-lead">' . esc_html( $intro ) . '</p><div class="shemo-hero__actions">' . shemo_stage17_link( $starturl, $startlabel ) . shemo_stage17_link( $servicesurl, $backlabel, 'shemo-button shemo-button--secondary' ) . '</div></div><div class="shemo-frame" aria-label="' . esc_attr( $title ) . '"><div class="shemo-frame__screen"><p class="shemo-frame__label">' . esc_html( $service['frame'] ) . '</p></div></div></section>'
		. '<section class="shemo-section shemo-narrative" aria-labelledby="problem-title"><div class="shemo-narrative__body"><p class="shemo-kicker">Problem</p><h2 id="problem-title">' . esc_html( $problem_h ) . '</h2><p>' . esc_html( $problem ) . '</p></div><aside class="shemo-aside-note"><strong>' . esc_html( $fit_h ) . '</strong>' . esc_html( $fit ) . '</aside></section>'
		. '<section class="shemo-section shemo-grid--two" aria-labelledby="deliver-title"><div><p class="shemo-kicker">Deliverables</p><h2 id="deliver-title">' . esc_html( $deliver_h ) . '</h2>' . shemo_stage17_list( $deliver ) . '</div><div><p class="shemo-kicker">Process</p><h2>' . esc_html( $process_h ) . '</h2>' . shemo_stage17_workflow( $process ) . '</div></section>'
		. '<section class="shemo-section"><div class="shemo-cta-panel"><p class="shemo-kicker">CTA</p><h2>' . esc_html( $cta_h ) . '</h2><p class="shemo-lead">' . esc_html( $cta_p ) . '</p><div class="shemo-button-row">' . shemo_stage17_link( $starturl, $startlabel ) . shemo_stage17_link( $servicesurl, $backlabel, 'shemo-button shemo-button--secondary' ) . '</div></div></section>';

	return shemo_stage17_wrap( $body );
}

$services = array(
	array(
		'title'            => 'Video Editing & Motion',
		'ar_slug'          => 'video-editing-motion',
		'en_slug'          => 'video-editing-motion-en',
		'ar_label'         => 'مونتاج وموشن',
		'en_label'         => 'Editing and motion',
		'frame'            => 'cut / rhythm / motion / export',
		'ar_intro'         => 'مونتاج وموشن يساعدان الفكرة على الظهور بإيقاع واضح، من فيديو قصير للسوشيال إلى افتتاحية أو إعلان مصغّر.',
		'en_intro'         => 'Editing and motion that give the idea a clear rhythm, from short social videos to opening frames and compact ads.',
		'ar_problem'       => 'الفيديو الخام غالبًا يحمل فكرة جيدة لكن بلا ترتيب: بداية بطيئة، رسالة غير واضحة، أو انتقالات تشتت الانتباه. هنا نعيد بناء الإيقاع حتى يخدم كل cut الهدف.',
		'en_problem'       => 'Raw footage often carries a good idea without structure: a slow start, unclear message, or distracting transitions. We rebuild the rhythm so every cut serves the goal.',
		'ar_deliverables'  => array( 'نسخة فيديو نهائية بالمقاسات المطلوبة.', 'قصّات قصيرة أو variants عند الحاجة.', 'موشن بسيط للعناوين، الانتقالات، أو الافتتاحية.', 'ملفات تصدير منظمة حسب المنصة.' ),
		'en_deliverables'  => array( 'Final video export in the needed formats.', 'Short cuts or variants when needed.', 'Simple motion for titles, transitions, or openings.', 'Organized export files per platform.' ),
		'ar_process'       => array( 'نراجع الهدف والمواد المتاحة.', 'نرتب structure أولي ونحدد الإيقاع.', 'ننفذ cut أول ثم مراجعة محددة.', 'نضيف motion/export ونجهز الملفات.' ),
		'en_process'       => array( 'Review the goal and source material.', 'Shape the first structure and rhythm.', 'Build a first cut with defined review.', 'Add motion, export, and prepare files.' ),
		'ar_fit'           => 'مناسب عندما لديك مادة خام أو فكرة فيديو وتحتاج تحويلها إلى محتوى قصير وواضح قابل للنشر.',
		'en_fit'           => 'Best when you have footage or a video idea that needs to become short, clear, publish-ready content.',
	),
	array(
		'title'            => 'Graphic Design',
		'ar_slug'          => 'graphic-design',
		'en_slug'          => 'graphic-design-en',
		'ar_label'         => 'تصميم جرافيك',
		'en_label'         => 'Graphic systems',
		'frame'            => 'layout / hierarchy / visual set',
		'ar_intro'         => 'تصميمات اجتماعية، عروض، ومواد إطلاق تحافظ على وضوح الرسالة وتماسك شكل البراند.',
		'en_intro'         => 'Social visuals, decks, and launch materials that keep the message clear and the brand visually consistent.',
		'ar_problem'       => 'المشكلة ليست نقص تصميمات فقط، بل عدم وجود hierarchy واضح: ما الذي يراه الجمهور أولًا؟ ما الرسالة؟ وكيف تبقى القطع متماسكة؟',
		'en_problem'       => 'The issue is not just needing more visuals. It is missing hierarchy: what should the audience see first, what is the message, and how do the pieces stay consistent?',
		'ar_deliverables'  => array( 'تصميمات ثابتة للسوشيال أو الحملات.', 'قوالب بسيطة قابلة للتكرار.', 'عناصر بصرية مساعدة مثل icons أو textures.', 'تجهيز ملفات استخدام أو تصدير نهائية.' ),
		'en_deliverables'  => array( 'Static social or campaign visuals.', 'Simple repeatable templates.', 'Supporting visual elements such as icons or textures.', 'Final exports or editable handoff files.' ),
		'ar_process'       => array( 'نحدد الرسالة والمنصة.', 'نقترح اتجاه layout ونظام ألوان/عناصر.', 'نصمم المجموعة الأولى للمراجعة.', 'نجهز النسخ والصيغ النهائية.' ),
		'en_process'       => array( 'Define the message and platform.', 'Propose layout direction and visual elements.', 'Design the first set for review.', 'Prepare final versions and formats.' ),
		'ar_fit'           => 'مناسب للحملات الصغيرة، مواد الإطلاق، المنشورات، والعروض التي تحتاج شكلًا واضحًا بلا ضجيج.',
		'en_fit'           => 'Good for small campaigns, launch materials, posts, and decks that need clear visuals without noise.',
	),
	array(
		'title'            => 'Sketch & Illustration',
		'ar_slug'          => 'sketch-illustration',
		'en_slug'          => 'sketch-illustration-en',
		'ar_label'         => 'اسكتش ورسوم',
		'en_label'         => 'Sketch and illustration',
		'frame'            => 'rough idea / drawing / refined visual',
		'ar_intro'         => 'اسكتشات ورسوم تجعل الفكرة قابلة للرؤية والمراجعة قبل أن تتحول إلى تصميم أو فيديو كامل.',
		'en_intro'         => 'Sketches and illustrations that make the idea visible and reviewable before it becomes a full design or video.',
		'ar_problem'       => 'بعض الأفكار يصعب شرحها بالكلام وحده. الاسكتش يحوّل الاحتمالات إلى شكل يمكن مراجعته بدل الدوران حول وصف عام.',
		'en_problem'       => 'Some ideas are hard to explain with words alone. Sketching turns possibilities into something reviewable instead of circling vague descriptions.',
		'ar_deliverables'  => array( 'اسكتشات اتجاه أو thumbnails.', 'رسوم توضيحية مفردة أو مجموعة صغيرة.', 'تحسين بصري لرسمة أولية.', 'ملفات جاهزة للاستخدام في تصميم أو فيديو.' ),
		'en_deliverables'  => array( 'Direction sketches or thumbnails.', 'Single illustration or small illustration set.', 'Refinement of an early drawing.', 'Files ready to use in design or video.' ),
		'ar_process'       => array( 'نحدد الفكرة وما يجب توضيحه.', 'نرسم rough options بسرعة.', 'نختار الاتجاه ونصقله.', 'نسلم النسخة النهائية بالصيغ المناسبة.' ),
		'en_process'       => array( 'Define the idea and what needs to be shown.', 'Create rough options quickly.', 'Choose and refine the direction.', 'Deliver final files in useful formats.' ),
		'ar_fit'           => 'مناسب عندما تحتاج تصورًا سريعًا لشخصية، مشهد، فكرة حملة، أو عنصر بصري قبل الإنتاج.',
		'en_fit'           => 'Fits when you need quick visualization for a character, scene, campaign idea, or visual element before production.',
	),
	array(
		'title'            => 'Storyboarding & Creative Planning',
		'ar_slug'          => 'storyboarding-creative-planning',
		'en_slug'          => 'storyboarding-creative-planning-en',
		'ar_label'         => 'Storyboard وتخطيط إبداعي',
		'en_label'         => 'Storyboard and planning',
		'frame'            => 'scene / sequence / production map',
		'ar_intro'         => 'تخطيط المشاهد قبل التنفيذ حتى يصبح الفيديو أو الحملة سلسلة قرارات واضحة وليست ارتجالًا متأخرًا.',
		'en_intro'         => 'Scene planning before production, so the video or campaign becomes a clear chain of choices instead of late improvisation.',
		'ar_problem'       => 'البدء في التنفيذ بدون خريطة يسبب إعادة عمل وتشتتًا: لقطات ناقصة، انتقالات غير مفهومة، ورسالة تتغير أثناء الإنتاج.',
		'en_problem'       => 'Starting production without a map creates rework and confusion: missing shots, unclear transitions, and a message that changes mid-production.',
		'ar_deliverables'  => array( 'Storyboard أو shot list مبسط.', 'تسلسل مشاهد مع ملاحظات الإيقاع.', 'توجيه بصري ومراجع.', 'خطة تنفيذ مختصرة للفريق أو العميل.' ),
		'en_deliverables'  => array( 'Storyboard or simplified shot list.', 'Scene sequence with rhythm notes.', 'Visual direction and references.', 'Short production plan for the team or client.' ),
		'ar_process'       => array( 'نفهم الرسالة والمدة والمنصة.', 'نقسم الفكرة إلى مشاهد.', 'نرسم أو نكتب التسلسل للمراجعة.', 'نثبت الخطة قبل الإنتاج.' ),
		'en_process'       => array( 'Understand message, duration, and platform.', 'Break the idea into scenes.', 'Sketch or write the sequence for review.', 'Lock the plan before production.' ),
		'ar_fit'           => 'مناسب قبل أي فيديو تعريفي، إعلان قصير، motion piece، أو حملة تحتاج ترتيبًا بصريًا.',
		'en_fit'           => 'Useful before explainers, short ads, motion pieces, or campaigns that need visual order.',
	),
	array(
		'title'            => 'Branding',
		'ar_slug'          => 'branding',
		'en_slug'          => 'branding-en',
		'ar_label'         => 'هوية واتجاه بصري',
		'en_label'         => 'Brand direction',
		'frame'            => 'mark / palette / usage system',
		'ar_intro'         => 'اتجاه بصري وعناصر أساسية تساعد البراند على الظهور بثبات في التصميم والفيديو والمنصات.',
		'en_intro'         => 'Visual direction and core elements that help a brand appear consistently across design, video, and platforms.',
		'ar_problem'       => 'البراند قد يمتلك اسمًا جيدًا لكنه يظهر كل مرة بشكل مختلف. الهوية هنا ليست زخرفة؛ هي نظام قرارات يساعد كل قطعة لاحقة.',
		'en_problem'       => 'A brand may have a good name but appear differently every time. Identity is not decoration; it is a decision system for everything that follows.',
		'ar_deliverables'  => array( 'اتجاه بصري أو moodboard عملي.', 'ألوان وخطوط وعناصر أساسية.', 'استخدامات أولية مثل social lockups أو covers.', 'ملف إرشادي مختصر حسب النطاق.' ),
		'en_deliverables'  => array( 'Practical visual direction or moodboard.', 'Colors, type, and core elements.', 'Initial applications such as social lockups or covers.', 'Compact usage guide depending on scope.' ),
		'ar_process'       => array( 'نراجع الهدف والشخصية والجمهور.', 'نقترح اتجاهات بصرية محدودة.', 'نصقل النظام المختار.', 'نسلم عناصر قابلة للاستخدام.' ),
		'en_process'       => array( 'Review goal, personality, and audience.', 'Propose focused visual directions.', 'Refine the selected system.', 'Deliver usable brand elements.' ),
		'ar_fit'           => 'مناسب للبراندات الصغيرة أو الخدمات التي تحتاج بداية منظمة قبل تصميم مواد كثيرة.',
		'en_fit'           => 'Fits small brands or services that need an organized start before creating many assets.',
	),
	array(
		'title'            => 'Creative Direction / Custom',
		'ar_slug'          => 'creative-direction-custom',
		'en_slug'          => 'creative-direction-custom-en',
		'ar_label'         => 'قيادة إبداعية مخصصة',
		'en_label'         => 'Custom creative direction',
		'frame'            => 'brief / references / scope / review',
		'ar_intro'         => 'مسار مرن للمشاريع التي لا تدخل في خدمة واحدة: نرتب الفكرة، نحدد النطاق، ونقود الاختيارات البصرية.',
		'en_intro'         => 'A flexible path for projects that do not fit one service: we structure the idea, define scope, and lead visual choices.',
		'ar_problem'       => 'أحيانًا لا تكون المشكلة تنفيذ قطعة واحدة، بل غياب قرار إبداعي جامع: ما الاتجاه؟ ما الأولوية؟ ومن يربط كل القطع ببعضها؟',
		'en_problem'       => 'Sometimes the issue is not producing one asset. It is the absence of a central creative decision: what direction, what priority, and who connects the pieces?',
		'ar_deliverables'  => array( 'جلسة direction أو brief مفصل.', 'مراجع واتجاه بصري مكتوب.', 'خطة تسليم أو scope واضح.', 'مراجعة إبداعية لقطع موجودة أو قيد التنفيذ.' ),
		'en_deliverables'  => array( 'Direction session or detailed brief.', 'References and written visual direction.', 'Delivery plan or clear scope.', 'Creative review for existing or in-progress assets.' ),
		'ar_process'       => array( 'نسمع المشكلة ونفصل أجزاءها.', 'نقترح خيارات direction عملية.', 'نثبت نطاقًا قابلًا للتنفيذ.', 'نراجع أو نوجه الإنتاج حتى التسليم.' ),
		'en_process'       => array( 'Listen to the problem and break it down.', 'Suggest practical direction options.', 'Lock an executable scope.', 'Review or guide production through delivery.' ),
		'ar_fit'           => 'مناسب عندما تعرف أن المشروع يحتاج قيادة ووضوحًا، لكن لا تريد حصره في خدمة جاهزة من البداية.',
		'en_fit'           => 'Best when the project needs leadership and clarity, but should not be forced into a fixed service too early.',
	),
);

$about_ar_id = shemo_stage17_upsert_page(
	'about',
	'عن Shemo Studio',
	shemo_stage17_about_content( 'ar' ),
	'ar',
	'عن Shemo Studio - استوديو إبداعي بقيادة شيمو',
	'تعرف على Shemo Studio: استوديو إبداعي بوتيك بقيادة مؤسس ظاهر، يجمع بين التفكير البصري والتنفيذ المنظم.'
);

$about_en_id = shemo_stage17_upsert_page(
	'about-en',
	'About',
	shemo_stage17_about_content( 'en' ),
	'en',
	'About Shemo Studio - Founder-led creative studio',
	'About Shemo Studio, a founder-led boutique creative studio built around visual thinking, clear scope, and organized delivery.'
);

pll_save_post_translations(
	array(
		'ar' => $about_ar_id,
		'en' => $about_en_id,
	)
);

$services_ar_id = shemo_stage17_upsert_page(
	'services',
	'الخدمات',
	shemo_stage17_services_overview( 'ar', $services ),
	'ar',
	'خدمات Shemo Studio - فيديو وتصميم وهوية وتخطيط إبداعي',
	'صفحة خدمات Shemo Studio: مونتاج وموشن، تصميم جرافيك، اسكتش ورسوم، storyboard، branding، وcreative direction.'
);

$services_en_id = shemo_stage17_upsert_page(
	'services-en',
	'Services',
	shemo_stage17_services_overview( 'en', $services ),
	'en',
	'Shemo Studio Services - Video, design, branding, planning',
	'Shemo Studio services overview: video editing and motion, graphic design, sketch and illustration, storyboarding, branding, and custom creative direction.'
);

pll_save_post_translations(
	array(
		'ar' => $services_ar_id,
		'en' => $services_en_id,
	)
);

$built = array(
	'about'    => array( 'ar' => $about_ar_id, 'en' => $about_en_id ),
	'services' => array( 'ar' => $services_ar_id, 'en' => $services_en_id ),
);

foreach ( $services as $service ) {
	$ar_id = shemo_stage17_upsert_page(
		$service['ar_slug'],
		$service['title'],
		shemo_stage17_service_content( $service, 'ar' ),
		'ar',
		$service['title'] . ' - Shemo Studio',
		$service['ar_intro'],
		$services_ar_id
	);

	$en_id = shemo_stage17_upsert_page(
		$service['en_slug'],
		$service['title'],
		shemo_stage17_service_content( $service, 'en' ),
		'en',
		$service['title'] . ' - Shemo Studio',
		$service['en_intro'],
		$services_en_id
	);

	pll_save_post_translations(
		array(
			'ar' => $ar_id,
			'en' => $en_id,
		)
	);

	$built[ $service['ar_slug'] ] = array( 'ar' => $ar_id, 'en' => $en_id );
}

shemo_stage17_enable_polylang_front_page_language_urls();

foreach ( $built as $key => $ids ) {
	WP_CLI::log( $key . ': ar=' . $ids['ar'] . ', en=' . $ids['en'] );
}

WP_CLI::success( 'Stage 17 About and Services pages built and linked in Polylang.' );
