<?php
/**
 * Build the bilingual Stage 16 home pages in WordPress.
 *
 * Run with:
 * wp eval-file tools/stage16-build-home.php --path="...\app\public"
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
	WP_CLI::error( 'Polylang functions are not available.' );
}

function shemo_stage16_upsert_page( string $slug, string $title, string $content, string $lang, string $seo_title, string $seo_description ): int {
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

function shemo_stage16_enable_polylang_front_page_language_urls(): void {
	$options = get_option( 'polylang' );

	if ( ! is_array( $options ) ) {
		WP_CLI::warning( 'Polylang options are not an array; skipped redirect_lang update.' );
		return;
	}

	$options['redirect_lang'] = true;
	update_option( 'polylang', $options );

	if ( function_exists( 'PLL' ) && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
		PLL()->model->clean_languages_cache();
	}

	flush_rewrite_rules( false );
}

$ar_content = <<<'HTML'
<!-- wp:group {"tagName":"main","className":"shemo-home","layout":{"type":"default"}} -->
<main class="wp-block-group shemo-home">
<!-- wp:html -->
<section class="shemo-section shemo-hero" aria-labelledby="home-hero-title">
	<div>
		<p class="shemo-kicker">استوديو إبداعي بقيادة شيمو</p>
		<h1 id="home-hero-title">من الاسكتش إلى الشاشة</h1>
		<p class="shemo-lead">Shemo Studio يحوّل الأفكار الخام إلى فيديوهات، هويات، ورسوم جاهزة للنشر. نبدأ من اتجاه بصري واضح، ثم نبني المشهد خطوة بخطوة حتى يصبح المحتوى مفهومًا ومناسبًا للمنصة.</p>
		<div class="shemo-hero__actions">
			<a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a>
			<a class="shemo-button shemo-button--secondary" href="/work/">شاهد عينات العمل</a>
		</div>
		<p class="shemo-hero__meta">استوديو بوتيك: رؤية مؤسس واضحة، نظام تنفيذ مرتب، ومساحة للنمو كفريق عند الحاجة. لا وعود مبالغ فيها؛ فقط وضوح في الفكرة والتنفيذ والتسليم.</p>
	</div>
	<div class="shemo-frame" aria-label="إطار بصري يوضح رحلة من الاسكتش إلى الشاشة">
		<div class="shemo-frame__screen"><p class="shemo-frame__label">Sketch notes / motion frame / final screen</p></div>
	</div>
</section>

<section class="shemo-section shemo-section--compact" aria-labelledby="showreel-title">
	<p class="shemo-kicker">Showreel</p>
	<div class="shemo-showreel">
		<div>
			<h2 id="showreel-title">مكان العرض الرئيسي</h2>
			<p class="shemo-lead">سيُستبدل هذا الـplaceholder بفيديو showreel حقيقي عند تجهيز المواد. حتى ذلك الحين، يوضّح موضع الفيديو ونبرة الصفحة بدون ادعاء وجود عمل منشور غير جاهز.</p>
		</div>
		<span class="shemo-play-mark" aria-hidden="true">▶</span>
	</div>
</section>

<section class="shemo-section" aria-labelledby="work-title">
	<p class="shemo-kicker">Selected Work</p>
	<h2 id="work-title">عينات Demo / Concept واضحة</h2>
	<p class="shemo-lead">إلى أن تتوفر أعمال عملاء حقيقية قابلة للنشر، تُعرض العينات الأولى كمشاريع تجريبية توضّح طريقة التفكير والتنفيذ فقط.</p>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><span class="shemo-tag">مشروع تجريبي / Concept &#45; غير منفّذ لعميل تجاري</span><h3>مشهد افتتاحي لبراند ناشئ</h3><p>دراسة Motion قصيرة تبدأ من سكتش شكل الشعار، ثم تتحول إلى افتتاحية فيديو قابلة للاستخدام في السوشيال.</p></article>
		<article class="shemo-mini-card"><span class="shemo-tag">مشروع تجريبي / Concept &#45; غير منفّذ لعميل تجاري</span><h3>حملة مصغّرة لخدمة رقمية</h3><p>تصميم اتجاه بصري، بوستات، ومقاطع قصيرة تشرح الخدمة بدون ازدحام بصري أو ادعاءات نتائج.</p></article>
		<article class="shemo-mini-card"><span class="shemo-tag">مشروع تجريبي / Concept &#45; غير منفّذ لعميل تجاري</span><h3>Storyboard لفيديو تعريفي</h3><p>تحويل فكرة مجردة إلى لقطات متتابعة توضّح الإيقاع، النص، والانتقالات قبل الإنتاج النهائي.</p></article>
	</div>
</section>

<section class="shemo-section" aria-labelledby="services-title">
	<p class="shemo-kicker">Services</p>
	<h2 id="services-title">خدمات تبني الصورة كاملة</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>Video Editing & Motion</h3><p>مونتاج، إيقاع، انتقالات، وموشن يوضح الرسالة بدل أن يزحمها.</p></article>
		<article class="shemo-mini-card"><h3>Graphic Design</h3><p>تصميمات اجتماعية، عروض، ومواد إطلاق متماسكة مع الهوية.</p></article>
		<article class="shemo-mini-card"><h3>Sketch & Illustration</h3><p>اسكتشات ورسوم تساعد على تحويل الفكرة إلى شكل يمكن مراجعته وتطويره.</p></article>
		<article class="shemo-mini-card"><h3>Storyboarding</h3><p>تخطيط المشاهد قبل التنفيذ حتى يعرف الجميع ما الذي سنبنيه ولماذا.</p></article>
		<article class="shemo-mini-card"><h3>Branding</h3><p>اتجاه بصري، عناصر أساسية، ونظام بسيط يساعد البراند على الظهور بثبات.</p></article>
		<article class="shemo-mini-card"><h3>Creative Direction</h3><p>تنسيق الفكرة، المرجع البصري، ونطاق التسليم للمشاريع التي تحتاج قيادة إبداعية.</p></article>
	</div>
</section>

<section class="shemo-section shemo-split" aria-labelledby="sketch-title">
	<div><p class="shemo-kicker">Sketch to Screen</p><h2 id="sketch-title">الاسكتش ليس ديكورًا. هو طريقة تفكير.</h2></div>
	<div><p class="shemo-lead">نبدأ بتفكيك الفكرة: ما الرسالة؟ من الجمهور؟ أين ستظهر؟ بعدها نرسم اتجاهًا أوليًا، نختبره بسرعة، ثم نحوله إلى تصميم أو فيديو قابل للتسليم.</p><p class="shemo-muted-text">هذه الرحلة تجعل العميل يرى الاختيارات قبل وقت التنفيذ الثقيل، وتقلل الدوران حول “شكل جميل” بدون هدف واضح.</p></div>
</section>

<section class="shemo-section" aria-labelledby="process-title">
	<p class="shemo-kicker">Process</p>
	<h2 id="process-title">عملية قصيرة وواضحة</h2>
	<ol class="shemo-step-list">
		<li><strong>Brief.</strong> نفهم الهدف، الجمهور، المنصة، والقيود.</li>
		<li><strong>Direction.</strong> نثبت mood، مراجع، وسكتشات أولية قبل التنفيذ.</li>
		<li><strong>Build.</strong> ننتج التصميم أو الفيديو مع نقاط مراجعة محددة.</li>
		<li><strong>Deliver.</strong> نسلم الملفات بصيغ مناسبة للنشر والاستخدام.</li>
	</ol>
</section>

<section class="shemo-section" aria-labelledby="packages-title">
	<p class="shemo-kicker">Packages</p>
	<h2 id="packages-title">باقات ستُبنى في محطة مستقلة</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>Launch Visuals</h3><p>حزمة مبدئية لمواد إطلاق أو حملة صغيرة. النطاق والسعر سيُعتمدان لاحقًا.</p></article>
		<article class="shemo-mini-card"><h3>Motion Sprint</h3><p>فيديو أو مجموعة مقاطع قصيرة مبنية حول رسالة واحدة واضحة.</p></article>
		<article class="shemo-mini-card"><h3>Creative Direction</h3><p>قيادة اتجاه بصري لمشروع يحتاج تخطيطًا قبل الإنتاج.</p></article>
	</div>
</section>

<section class="shemo-section shemo-grid--two" aria-labelledby="credibility-title">
	<div><p class="shemo-kicker">Credibility</p><h2 id="credibility-title">ثقة بدون شهادات وهمية</h2><p class="shemo-lead">لا نعرض أسماء عملاء أو نتائج أو شهادات غير موجودة. في المرحلة الحالية، الثقة تُبنى من وضوح العملية، شفافية وسم مشاريع Demo/Concept، والمراجعة المباشرة بقيادة شيمو.</p></div>
	<div class="shemo-mini-card"><h3>ما يمكن توقعه</h3><p>Brief واضح، نطاق تسليم مكتوب، نقاط مراجعة محددة، وملفات منظمة حسب منصة الاستخدام.</p></div>
</section>

<section class="shemo-section shemo-faq" aria-labelledby="faq-title">
	<p class="shemo-kicker">FAQ</p>
	<h2 id="faq-title">أسئلة مختصرة</h2>
	<details><summary>هل المشاريع المعروضة حقيقية لعملاء؟</summary><p>العينات الأولى موسومة بوضوح كمشاريع Demo/Concept وليست منفّذة لعملاء تجاريين.</p></details>
	<details><summary>هل يمكن تنفيذ مشروع عربي وإنجليزي؟</summary><p>نعم، الموقع والخدمة مبنيان من البداية على اتجاه عربي أولًا مع نسخة إنجليزية مقابلة عند الحاجة.</p></details>
	<details><summary>هل توجد أسعار جاهزة؟</summary><p>صفحة الباقات ستُبنى في محطة لاحقة. حاليًا نبدأ بفهم نطاق المشروع ثم نقترح الشكل المناسب.</p></details>
	<details><summary>من يقود العمل؟</summary><p>شيمو يقود الاتجاه الإبداعي والمراجعة الأساسية، مع قابلية توسيع التنفيذ كاستوديو عند الحاجة.</p></details>
</section>

<section class="shemo-section" aria-labelledby="cta-title">
	<div class="shemo-cta-panel">
		<p class="shemo-kicker">ابدأ من الفكرة</p>
		<h2 id="cta-title">لديك فكرة تحتاج أن تصبح مشهدًا واضحًا؟</h2>
		<p class="shemo-lead">اكتب لنا عن الهدف، المنصة، والموعد المتوقع. سنساعدك على تحويلها إلى اتجاه بصري وخطة تنفيذ مناسبة.</p>
		<div class="shemo-button-row"><a class="shemo-button" href="/start-a-project/">ابدأ مشروعك</a><a class="shemo-button shemo-button--secondary" href="/contact/">تواصل معنا</a></div>
	</div>
</section>
<!-- /wp:html -->
</main>
<!-- /wp:group -->
HTML;

$en_content = <<<'HTML'
<!-- wp:group {"tagName":"main","className":"shemo-home","layout":{"type":"default"}} -->
<main class="wp-block-group shemo-home">
<!-- wp:html -->
<section class="shemo-section shemo-hero" aria-labelledby="home-hero-title">
	<div>
		<p class="shemo-kicker">Founder-led creative studio</p>
		<h1 id="home-hero-title">From Sketch to Screen</h1>
		<p class="shemo-lead">Shemo Studio turns raw ideas into publish-ready video, identity, and illustration work. We start with a clear visual direction, then build the frame step by step until the message fits the platform.</p>
		<div class="shemo-hero__actions">
			<a class="shemo-button" href="/en/start-a-project/">Start a Project</a>
			<a class="shemo-button shemo-button--secondary" href="/en/work/">View Work</a>
		</div>
		<p class="shemo-hero__meta">A boutique studio model: visible founder direction, a structured production rhythm, and room to scale when the project needs more hands. No inflated promises, just clear thinking, making, and delivery.</p>
	</div>
	<div class="shemo-frame" aria-label="Visual frame showing the sketch-to-screen process">
		<div class="shemo-frame__screen"><p class="shemo-frame__label">Sketch notes / motion frame / final screen</p></div>
	</div>
</section>

<section class="shemo-section shemo-section--compact" aria-labelledby="showreel-title">
	<p class="shemo-kicker">Showreel</p>
	<div class="shemo-showreel">
		<div>
			<h2 id="showreel-title">Showreel placeholder</h2>
			<p class="shemo-lead">This space is reserved for the real showreel once the material is ready. For now, it sets the intended video placement without pretending that unpublished work already exists.</p>
		</div>
		<span class="shemo-play-mark" aria-hidden="true">▶</span>
	</div>
</section>

<section class="shemo-section" aria-labelledby="work-title">
	<p class="shemo-kicker">Selected Work</p>
	<h2 id="work-title">Clearly labeled Demo / Concept samples</h2>
	<p class="shemo-lead">Until real client work is available to publish, the first samples are presented as demo concepts that show thinking and craft only.</p>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><span class="shemo-tag">Demo / Concept Project &#45; Not commissioned by a client</span><h3>Opening scene for an emerging brand</h3><p>A short motion study that starts with a logo sketch and turns into a social-ready opening frame.</p></article>
		<article class="shemo-mini-card"><span class="shemo-tag">Demo / Concept Project &#45; Not commissioned by a client</span><h3>Mini campaign for a digital service</h3><p>Visual direction, social posts, and short clips that explain a service without crowding the message.</p></article>
		<article class="shemo-mini-card"><span class="shemo-tag">Demo / Concept Project &#45; Not commissioned by a client</span><h3>Storyboard for an explainer video</h3><p>A rough idea translated into sequential frames that clarify pacing, copy, and transitions before production.</p></article>
	</div>
</section>

<section class="shemo-section" aria-labelledby="services-title">
	<p class="shemo-kicker">Services</p>
	<h2 id="services-title">Services that build the whole picture</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>Video Editing & Motion</h3><p>Editing, rhythm, transitions, and motion that clarify the message instead of overpowering it.</p></article>
		<article class="shemo-mini-card"><h3>Graphic Design</h3><p>Social visuals, decks, and launch materials that stay consistent with the brand direction.</p></article>
		<article class="shemo-mini-card"><h3>Sketch & Illustration</h3><p>Sketches and illustrations that turn an idea into something visible, reviewable, and refinable.</p></article>
		<article class="shemo-mini-card"><h3>Storyboarding</h3><p>Scene planning before production, so everyone knows what is being built and why.</p></article>
		<article class="shemo-mini-card"><h3>Branding</h3><p>Visual direction, core elements, and a simple system that helps the brand appear consistently.</p></article>
		<article class="shemo-mini-card"><h3>Creative Direction</h3><p>Idea structure, references, and delivery scope for projects that need a creative lead.</p></article>
	</div>
</section>

<section class="shemo-section shemo-split" aria-labelledby="sketch-title">
	<div><p class="shemo-kicker">Sketch to Screen</p><h2 id="sketch-title">The sketch is not decoration. It is a thinking tool.</h2></div>
	<div><p class="shemo-lead">We break down the idea first: the message, audience, platform, and constraints. Then we sketch a direction, test it quickly, and turn it into a design or video ready to deliver.</p><p class="shemo-muted-text">This gives the client visibility before heavy production begins and keeps the work from circling around “nice visuals” without a clear purpose.</p></div>
</section>

<section class="shemo-section" aria-labelledby="process-title">
	<p class="shemo-kicker">Process</p>
	<h2 id="process-title">A short, clear process</h2>
	<ol class="shemo-step-list">
		<li><strong>Brief.</strong> We understand the goal, audience, platform, and constraints.</li>
		<li><strong>Direction.</strong> We define mood, references, and early sketches before production.</li>
		<li><strong>Build.</strong> We produce the design or video with defined review points.</li>
		<li><strong>Deliver.</strong> We hand over organized files in the formats needed for publishing.</li>
	</ol>
</section>

<section class="shemo-section" aria-labelledby="packages-title">
	<p class="shemo-kicker">Packages</p>
	<h2 id="packages-title">Packages will be shaped in a dedicated stage</h2>
	<div class="shemo-grid">
		<article class="shemo-mini-card"><h3>Launch Visuals</h3><p>A starter package for launch materials or a small campaign. Scope and pricing will be approved later.</p></article>
		<article class="shemo-mini-card"><h3>Motion Sprint</h3><p>A video or set of short clips built around one clear message.</p></article>
		<article class="shemo-mini-card"><h3>Creative Direction</h3><p>Visual leadership for projects that need planning before production.</p></article>
	</div>
</section>

<section class="shemo-section shemo-grid--two" aria-labelledby="credibility-title">
	<div><p class="shemo-kicker">Credibility</p><h2 id="credibility-title">Trust without invented testimonials</h2><p class="shemo-lead">We are not showing client names, metrics, or testimonials that do not exist. At this stage, trust comes from a clear process, transparent Demo / Concept labeling, and direct review led by Shemo.</p></div>
	<div class="shemo-mini-card"><h3>What to expect</h3><p>A clear brief, written delivery scope, defined review points, and organized files for the platform where the work will live.</p></div>
</section>

<section class="shemo-section shemo-faq" aria-labelledby="faq-title">
	<p class="shemo-kicker">FAQ</p>
	<h2 id="faq-title">Short answers</h2>
	<details><summary>Are the displayed projects real client work?</summary><p>The first samples are clearly labeled Demo / Concept Projects and were not commissioned by commercial clients.</p></details>
	<details><summary>Can you work in Arabic and English?</summary><p>Yes. The studio and site are built Arabic-first with a natural English counterpart where needed.</p></details>
	<details><summary>Are package prices ready?</summary><p>The Packages page will be built in a later stage. For now, we start by understanding the project scope and recommending the right shape.</p></details>
	<details><summary>Who leads the creative work?</summary><p>Shemo leads the creative direction and core review, with room to scale production as a studio when needed.</p></details>
</section>

<section class="shemo-section" aria-labelledby="cta-title">
	<div class="shemo-cta-panel">
		<p class="shemo-kicker">Start with the idea</p>
		<h2 id="cta-title">Have an idea that needs to become a clear visual scene?</h2>
		<p class="shemo-lead">Tell us about the goal, platform, and timeline. We will help turn it into a visual direction and a practical production plan.</p>
		<div class="shemo-button-row"><a class="shemo-button" href="/en/start-a-project/">Start a Project</a><a class="shemo-button shemo-button--secondary" href="/en/contact/">Contact</a></div>
	</div>
</section>
<!-- /wp:html -->
</main>
<!-- /wp:group -->
HTML;

$ar_id = shemo_stage16_upsert_page(
	'home',
	'الرئيسية',
	$ar_content,
	'ar',
	'Shemo Studio - من الاسكتش إلى الشاشة',
	'الصفحة الرئيسية العربية لـ Shemo Studio: استوديو إبداعي بوتيك يحوّل الأفكار من اسكتش أولي إلى محتوى جاهز للشاشة.'
);

$en_id = shemo_stage16_upsert_page(
	'home-en',
	'Home',
	$en_content,
	'en',
	'Shemo Studio - From Sketch to Screen',
	'English home page for Shemo Studio, a founder-led boutique creative studio turning sketches into publish-ready screen work.'
);

pll_save_post_translations(
	array(
		'ar' => $ar_id,
		'en' => $en_id,
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $ar_id );
shemo_stage16_enable_polylang_front_page_language_urls();

WP_CLI::success( 'Stage 16 home pages built. Arabic ID: ' . $ar_id . ', English ID: ' . $en_id . '. Polylang redirect_lang enabled.' );
