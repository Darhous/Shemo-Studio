<?php
/**
 * Single Project / Case Study template.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$lang         = shemo_child_current_language();
	$is_ar        = 'ar' === $lang;
	$project_id   = get_the_ID();
	$status_label = shemo_child_demo_project_label( $lang );
	$summary      = get_post_meta( $project_id, 'shemo_short_summary', true );
	$goal         = get_post_meta( $project_id, 'shemo_project_goal', true );
	$challenge    = get_post_meta( $project_id, 'shemo_challenge', true );
	$direction    = get_post_meta( $project_id, 'shemo_creative_direction', true );
	$deliverables = get_post_meta( $project_id, 'shemo_deliverables', true );
	$results      = get_post_meta( $project_id, 'shemo_results', true );
	$credits      = get_post_meta( $project_id, 'shemo_credits', true );
	$related      = get_post_meta( $project_id, 'shemo_related_projects', true );
	$archive_url  = get_post_type_archive_link( 'project' );
	$start_url    = $is_ar ? home_url( '/start-a-project/' ) : home_url( '/en/start-a-project/' );

	$tax_labels = array(
		'service'        => $is_ar ? 'الخدمة' : 'Service',
		'project_type'   => $is_ar ? 'نوع المشروع' : 'Project type',
		'industry'       => $is_ar ? 'القطاع' : 'Industry',
		'platform'       => $is_ar ? 'المنصة' : 'Platform',
		'content_format' => $is_ar ? 'صيغة المحتوى' : 'Content format',
		'client_type'    => $is_ar ? 'حالة العميل' : 'Client type',
		'visual_style'   => $is_ar ? 'الاتجاه البصري' : 'Visual style',
	);
	?>

	<main class="shemo-home shemo-page shemo-case-study">
		<section class="shemo-section shemo-hero shemo-case-hero" aria-labelledby="case-title">
			<div>
				<p class="shemo-kicker"><?php echo esc_html( $is_ar ? 'Case Study' : 'Case Study' ); ?></p>
				<h1 id="case-title"><?php the_title(); ?></h1>
				<span class="shemo-tag"><?php echo esc_html( $status_label ); ?></span>
				<p class="shemo-lead"><?php echo esc_html( $summary ); ?></p>
				<div class="shemo-hero__actions">
					<a class="shemo-button" href="<?php echo esc_url( $start_url ); ?>"><?php echo esc_html( $is_ar ? 'ابدأ مشروعك' : 'Start a Project' ); ?></a>
					<a class="shemo-button shemo-button--secondary" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( $is_ar ? 'كل الأعمال' : 'All work' ); ?></a>
				</div>
			</div>
			<div class="shemo-frame" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
				<div class="shemo-frame__screen">
					<p class="shemo-frame__label">demo / concept / transparent proof</p>
				</div>
			</div>
		</section>

		<section class="shemo-section shemo-case-meta" aria-labelledby="case-summary-title">
			<div>
				<p class="shemo-kicker">01</p>
				<h2 id="case-summary-title"><?php echo esc_html( $is_ar ? 'ملخص المشروع' : 'Project summary' ); ?></h2>
				<p class="shemo-lead"><?php echo esc_html( $goal ); ?></p>
			</div>
			<dl>
				<div><dt><?php echo esc_html( $is_ar ? 'وسم الشفافية' : 'Transparency label' ); ?></dt><dd><?php echo esc_html( $status_label ); ?></dd></div>
				<?php foreach ( $tax_labels as $taxonomy => $label ) : ?>
					<?php $terms = get_the_terms( $project_id, $taxonomy ); ?>
					<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
						<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) ); ?></dd></div>
					<?php endif; ?>
				<?php endforeach; ?>
			</dl>
		</section>

		<section class="shemo-section shemo-narrative" aria-labelledby="context-title">
			<div class="shemo-narrative__body">
				<p class="shemo-kicker">02 / 03</p>
				<h2 id="context-title"><?php echo esc_html( $is_ar ? 'السياق وحالة العميل' : 'Context and client status' ); ?></h2>
				<p>
					<?php
					echo esc_html(
						$is_ar
							? 'هذا مشروع concept داخلي صُمم ليعرض طريقة Shemo Studio في التفكير والتنفيذ. لا يوجد عميل تجاري، ولا اسم علامة حقيقي، ولا وعد بنتيجة سوقية.'
							: 'This is an internal concept project created to show Shemo Studio’s thinking and production method. There is no commercial client, real brand name, or implied market result.'
					);
					?>
				</p>
			</div>
			<aside class="shemo-aside-note"><strong><?php echo esc_html( $is_ar ? 'حالة النشر' : 'Publishing status' ); ?></strong><?php echo esc_html( $status_label ); ?></aside>
		</section>

		<section class="shemo-section shemo-grid--two" aria-labelledby="challenge-title">
			<div>
				<p class="shemo-kicker">04</p>
				<h2 id="challenge-title"><?php echo esc_html( $is_ar ? 'التحدي' : 'Challenge' ); ?></h2>
				<?php echo wp_kses_post( wpautop( $challenge ) ); ?>
			</div>
			<div>
				<p class="shemo-kicker">05</p>
				<h2><?php echo esc_html( $is_ar ? 'الاتجاه الإبداعي' : 'Creative direction' ); ?></h2>
				<?php echo wp_kses_post( wpautop( $direction ) ); ?>
			</div>
		</section>

		<section class="shemo-section shemo-case-visuals" aria-labelledby="sketch-title">
			<div>
				<p class="shemo-kicker">06</p>
				<h2 id="sketch-title"><?php echo esc_html( $is_ar ? 'اسكتش / تصور أولي' : 'Sketch / initial concept' ); ?></h2>
				<p><?php echo esc_html( $is_ar ? 'المرحلة الأولى هنا كانت تحويل الرسالة إلى مشاهد بسيطة: لقطة افتتاح، انتقال، وإطار نهائي يثبت الفكرة.' : 'The first step was turning the message into simple frames: opening shot, transition, and final frame that anchors the idea.' ); ?></p>
			</div>
			<div class="shemo-visual-triptych" aria-hidden="true">
				<span>sketch</span><span>direction</span><span>screen</span>
			</div>
		</section>

		<section class="shemo-section shemo-grid--two" aria-labelledby="process-title">
			<div>
				<p class="shemo-kicker">07</p>
				<h2 id="process-title"><?php echo esc_html( $is_ar ? 'العملية' : 'Process' ); ?></h2>
				<?php the_content(); ?>
			</div>
			<div>
				<p class="shemo-kicker">08</p>
				<h2><?php echo esc_html( $is_ar ? 'قبل وبعد' : 'Before and after' ); ?></h2>
				<p><?php echo esc_html( $is_ar ? 'قبل: فكرة عامة بلا ترتيب بصري. بعد: تسلسل واضح يمكن إنتاجه أو تطويره كقطعة نشر.' : 'Before: a broad idea without visual order. After: a clear sequence that can be produced or developed into a publishable asset.' ); ?></p>
			</div>
		</section>

		<section class="shemo-section shemo-narrative" aria-labelledby="result-title">
			<div class="shemo-narrative__body">
				<p class="shemo-kicker">09 / 10</p>
				<h2 id="result-title"><?php echo esc_html( $is_ar ? 'النتيجة النهائية والفيديو/المعرض' : 'Final result and video/gallery' ); ?></h2>
				<p><?php echo esc_html( $is_ar ? 'الناتج هنا هو عرض دراسة حالة نصي وبصري داخل الموقع، وليس فيديو عميل منشور. أي معرض لاحق سيستخدم أصولًا أصلية أو مواد مسموح بها فقط.' : 'The output here is a textual and visual case-study presentation on the site, not a published client video. Any later gallery will use original or permitted assets only.' ); ?></p>
			</div>
			<aside class="shemo-aside-note"><strong><?php echo esc_html( $is_ar ? 'ملاحظة أمان المحتوى' : 'Content safety note' ); ?></strong><?php echo esc_html( $is_ar ? 'لا توجد لقطات عميل أو شعارات خارجية في هذا المشروع.' : 'No client footage or third-party logos are used in this project.' ); ?></aside>
		</section>

		<section class="shemo-section shemo-grid--two" aria-labelledby="deliverables-title">
			<div>
				<p class="shemo-kicker">11</p>
				<h2 id="deliverables-title"><?php echo esc_html( $is_ar ? 'التسليمات' : 'Deliverables' ); ?></h2>
				<?php if ( is_array( $deliverables ) ) : ?>
					<ul class="shemo-check-list">
						<?php foreach ( $deliverables as $deliverable ) : ?>
							<li><?php echo esc_html( $deliverable ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div>
				<p class="shemo-kicker">12</p>
				<h2><?php echo esc_html( $is_ar ? 'الأدوات المستخدمة' : 'Tools used' ); ?></h2>
				<ul class="shemo-pill-list">
					<?php
					$tools = get_the_terms( $project_id, 'tool' );
					if ( $tools && ! is_wp_error( $tools ) ) :
						foreach ( $tools as $tool ) :
							?>
							<li><?php echo esc_html( $tool->name ); ?></li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>
		</section>

		<section class="shemo-section shemo-grid--two" aria-labelledby="outcome-title">
			<div>
				<p class="shemo-kicker">13</p>
				<h2 id="outcome-title"><?php echo esc_html( $is_ar ? 'المخرجات / Outcome' : 'Outcome' ); ?></h2>
				<?php if ( is_array( $results ) && ! empty( $results ) ) : ?>
					<ul class="shemo-check-list">
						<?php foreach ( $results as $result ) : ?>
							<li><?php echo esc_html( trim( ( $result['metric'] ?? '' ) . ': ' . ( $result['value'] ?? '' ), ': ' ) ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php echo esc_html( $is_ar ? 'لا توجد أرقام أداء أو نتائج تجارية لأن المشروع تجريبي وغير منفّذ لعميل تجاري.' : 'There are no performance metrics or commercial outcomes because this is a demo project, not commissioned by a client.' ); ?></p>
				<?php endif; ?>
			</div>
			<div>
				<p class="shemo-kicker">14</p>
				<h2><?php echo esc_html( $is_ar ? 'الشهادة' : 'Testimonial' ); ?></h2>
				<p><?php echo esc_html( $is_ar ? 'لا توجد شهادة عميل لهذا المشروع، ولن نضيف شهادة وهمية لمشروع Concept.' : 'There is no client testimonial for this project, and no invented quote is used for a concept piece.' ); ?></p>
			</div>
		</section>

		<section class="shemo-section" aria-labelledby="related-title">
			<p class="shemo-kicker">15</p>
			<h2 id="related-title"><?php echo esc_html( $is_ar ? 'مشاريع ذات صلة' : 'Related projects' ); ?></h2>
			<div class="shemo-project-grid shemo-project-grid--compact">
				<?php
				$related_ids = is_array( $related ) ? array_filter( array_map( 'absint', $related ) ) : array();
				if ( empty( $related_ids ) ) {
					$related_query = new WP_Query(
						array(
							'post_type'           => 'project',
							'post__not_in'        => array( $project_id ),
							'posts_per_page'      => 3,
							'ignore_sticky_posts' => true,
						)
					);
				} else {
					$related_query = new WP_Query(
						array(
							'post_type'      => 'project',
							'post__in'       => $related_ids,
							'orderby'        => 'post__in',
							'posts_per_page' => 3,
						)
					);
				}

				if ( $related_query->have_posts() ) :
					while ( $related_query->have_posts() ) :
						$related_query->the_post();
						?>
						<article class="shemo-mini-card">
							<span class="shemo-tag"><?php echo esc_html( $status_label ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( get_post_meta( get_the_ID(), 'shemo_short_summary', true ) ); ?></p>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		</section>

		<section class="shemo-section" aria-labelledby="case-cta-title">
			<div class="shemo-cta-panel">
				<p class="shemo-kicker">16</p>
				<h2 id="case-cta-title"><?php echo esc_html( $is_ar ? 'هل تريد مشروعًا حقيقيًا بنفس الوضوح؟' : 'Want real work with this level of clarity?' ); ?></h2>
				<p class="shemo-lead"><?php echo esc_html( $is_ar ? 'ابدأ بbrief قصير، وسنحوّل الهدف إلى نطاق واضح قبل التنفيذ.' : 'Start with a short brief, and we will turn the goal into a clear scope before production.' ); ?></p>
				<div class="shemo-button-row">
					<a class="shemo-button" href="<?php echo esc_url( $start_url ); ?>"><?php echo esc_html( $is_ar ? 'ابدأ مشروعك' : 'Start a Project' ); ?></a>
					<a class="shemo-button shemo-button--secondary" href="<?php echo esc_url( $archive_url ); ?>"><?php echo esc_html( $is_ar ? 'ارجع للأعمال' : 'Back to work' ); ?></a>
				</div>
			</div>
		</section>
	</main>

	<?php
endwhile;

get_footer();
