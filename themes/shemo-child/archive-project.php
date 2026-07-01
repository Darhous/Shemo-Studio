<?php
/**
 * Project archive with taxonomy filters.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$lang       = shemo_child_current_language();
$is_ar      = 'ar' === $lang;
$archive    = get_post_type_archive_link( 'project' );
$reset_url  = $archive ? $archive : home_url( $is_ar ? '/work/' : '/en/work/' );
$filters    = shemo_child_project_archive_filters();
$has_filter = false;

foreach ( array_keys( $filters ) as $taxonomy ) {
	if ( ! empty( $_GET[ $taxonomy ] ) ) {
		$has_filter = true;
		break;
	}
}
?>

<main class="shemo-home shemo-page shemo-work-archive">
	<section class="shemo-section shemo-hero" aria-labelledby="work-title">
		<div>
			<p class="shemo-kicker"><?php echo esc_html( $is_ar ? 'Work Archive' : 'Work Archive' ); ?></p>
			<h1 id="work-title"><?php echo esc_html( $is_ar ? 'أعمال توضّح طريقة التفكير والتنفيذ' : 'Work that shows the thinking and the build' ); ?></h1>
			<p class="shemo-lead">
				<?php
				echo esc_html(
					$is_ar
						? 'هذا الأرشيف يبدأ بمشاريع Demo/Concept أصلية وموسومة بوضوح، إلى أن تتوفر أعمال عملاء حقيقية قابلة للنشر.'
						: 'This archive starts with clearly labeled original Demo/Concept projects until publishable client work is available.'
				);
				?>
			</p>
			<span class="shemo-tag"><?php echo esc_html( shemo_child_demo_project_label( $lang ) ); ?></span>
		</div>
		<div class="shemo-frame" aria-label="<?php echo esc_attr( $is_ar ? 'إطار أرشيف الأعمال' : 'Work archive frame' ); ?>">
			<div class="shemo-frame__screen">
				<p class="shemo-frame__label">service / type / industry / platform</p>
			</div>
		</div>
	</section>

	<section class="shemo-section shemo-work-filters" aria-labelledby="work-filter-title">
		<div class="shemo-section__head">
			<p class="shemo-kicker"><?php echo esc_html( $is_ar ? 'Filters' : 'Filters' ); ?></p>
			<h2 id="work-filter-title"><?php echo esc_html( $is_ar ? 'فلترة حسب تصنيفات المشروع' : 'Filter by project taxonomies' ); ?></h2>
		</div>
		<form class="shemo-filter-form" method="get" action="<?php echo esc_url( $reset_url ); ?>">
			<?php foreach ( $filters as $taxonomy => $label ) : ?>
				<?php
				$selected = isset( $_GET[ $taxonomy ] ) ? sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) ) : '';
				$terms    = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
						'lang'       => $lang,
					)
				);

				if ( is_wp_error( $terms ) || empty( $terms ) ) {
					continue;
				}
				?>
				<label>
					<span><?php echo esc_html( $label ); ?></span>
					<select name="<?php echo esc_attr( $taxonomy ); ?>">
						<option value=""><?php echo esc_html( $is_ar ? 'الكل' : 'All' ); ?></option>
						<?php foreach ( $terms as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected, $term->slug ); ?>>
								<?php echo esc_html( $term->name ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
			<?php endforeach; ?>
			<div class="shemo-filter-form__actions">
				<button type="submit" class="shemo-button"><?php echo esc_html( $is_ar ? 'طبّق الفلتر' : 'Apply filters' ); ?></button>
				<a class="shemo-button shemo-button--secondary" href="<?php echo esc_url( $reset_url ); ?>"><?php echo esc_html( $is_ar ? 'إعادة ضبط' : 'Reset' ); ?></a>
			</div>
		</form>
	</section>

	<section class="shemo-section" aria-labelledby="work-results-title">
		<div class="shemo-section__head shemo-section__head--inline">
			<div>
				<p class="shemo-kicker"><?php echo esc_html( $is_ar ? 'Projects' : 'Projects' ); ?></p>
				<h2 id="work-results-title"><?php echo esc_html( $is_ar ? 'نتائج الأرشيف' : 'Archive results' ); ?></h2>
			</div>
			<p class="shemo-work-count">
				<?php
				printf(
					esc_html( $is_ar ? '%d مشروع ظاهر' : '%d project(s) shown' ),
					(int) $GLOBALS['wp_query']->found_posts
				);
				?>
			</p>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="shemo-project-grid">
				<?php while ( have_posts() ) : ?>
					<?php
					the_post();
					$summary = get_post_meta( get_the_ID(), 'shemo_short_summary', true );
					$service = get_the_terms( get_the_ID(), 'service' );
					$type    = get_the_terms( get_the_ID(), 'project_type' );
					?>
					<article <?php post_class( 'shemo-project-card' ); ?>>
						<a class="shemo-project-card__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<span><?php echo esc_html( $service && ! is_wp_error( $service ) ? $service[0]->name : ( $is_ar ? 'مشروع' : 'Project' ) ); ?></span>
						</a>
						<div class="shemo-project-card__body">
							<span class="shemo-tag"><?php echo esc_html( shemo_child_demo_project_label( $lang ) ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( $summary ? $summary : get_the_excerpt() ); ?></p>
							<ul class="shemo-pill-list">
								<?php if ( $type && ! is_wp_error( $type ) ) : ?>
									<li><?php echo esc_html( $type[0]->name ); ?></li>
								<?php endif; ?>
								<?php if ( $service && ! is_wp_error( $service ) ) : ?>
									<li><?php echo esc_html( $service[0]->name ); ?></li>
								<?php endif; ?>
							</ul>
							<a class="shemo-button shemo-button--secondary" href="<?php the_permalink(); ?>"><?php echo esc_html( $is_ar ? 'اقرأ دراسة الحالة' : 'Read case study' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="shemo-empty-state">
				<h3><?php echo esc_html( $is_ar ? 'لا توجد مشاريع بهذا الفلتر بعد.' : 'No projects match this filter yet.' ); ?></h3>
				<p><?php echo esc_html( $is_ar ? 'جرّب تصنيفًا آخر أو أعد ضبط الفلاتر.' : 'Try another taxonomy value or reset the filters.' ); ?></p>
				<a class="shemo-button" href="<?php echo esc_url( $reset_url ); ?>"><?php echo esc_html( $is_ar ? 'إعادة ضبط' : 'Reset filters' ); ?></a>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
