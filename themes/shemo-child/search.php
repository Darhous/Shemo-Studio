<?php
defined( 'ABSPATH' ) || exit;

get_header();

$lang        = function_exists( 'shemo_child_current_language' ) ? shemo_child_current_language() : ( is_rtl() ? 'ar' : 'en' );
$is_ar       = 'ar' === $lang;
$search_term = get_search_query();
?>

<main class="wp-block-group shemo-home shemo-page shemo-stage20 shemo-search-results">
	<section class="shemo-section shemo-hero" aria-labelledby="shemo-search-title">
		<div>
			<p class="shemo-kicker"><?php echo esc_html( $is_ar ? 'بحث' : 'Search' ); ?></p>
			<h1 id="shemo-search-title">
				<?php
				echo esc_html(
					$search_term
						? ( $is_ar ? 'نتائج البحث' : 'Search results' )
						: ( $is_ar ? 'ابحث داخل الموقع' : 'Search the site' )
				);
				?>
			</h1>
			<p class="shemo-lead">
				<?php
				echo esc_html(
					$search_term
						? sprintf( $is_ar ? 'بحثت عن: %s' : 'You searched for: %s', $search_term )
						: ( $is_ar ? 'اكتب كلمة للعثور على صفحة، خدمة، مشروع، أو سياسة.' : 'Type a term to find a page, service, project, or policy.' )
				);
				?>
			</p>
			<?php get_search_form(); ?>
		</div>
		<div class="shemo-frame" aria-hidden="true">
			<div class="shemo-frame__screen"><p class="shemo-frame__label">query / results / next step</p></div>
		</div>
	</section>

	<section class="shemo-section" aria-label="<?php echo esc_attr( $is_ar ? 'نتائج البحث' : 'Search results' ); ?>">
		<?php if ( have_posts() ) : ?>
			<div class="shemo-search-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="shemo-mini-card">
						<p class="shemo-kicker"><?php echo esc_html( get_post_type() ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<div class="shemo-empty-state">
				<h2><?php echo esc_html( $is_ar ? 'لا توجد نتائج مطابقة.' : 'No matching results.' ); ?></h2>
				<p><?php echo esc_html( $is_ar ? 'جرّب كلمة أبسط، أو ابدأ من صفحة الخدمات أو التواصل.' : 'Try a simpler term, or start from Services or Contact.' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
