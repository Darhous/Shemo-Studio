<?php
defined( 'ABSPATH' ) || exit;

get_header();

$lang    = function_exists( 'shemo_child_current_language' ) ? shemo_child_current_language() : ( is_rtl() ? 'ar' : 'en' );
$is_ar   = 'ar' === $lang;
$home    = $is_ar ? home_url( '/' ) : home_url( '/en/' );
$search  = $is_ar ? home_url( '/search/' ) : home_url( '/en/search-en/' );
$contact = $is_ar ? home_url( '/contact/' ) : home_url( '/en/contact-en/' );
?>

<main class="wp-block-group shemo-home shemo-page shemo-stage20 shemo-error-page">
	<section class="shemo-section shemo-hero" aria-labelledby="shemo-404-title">
		<div>
			<p class="shemo-kicker"><?php echo esc_html( $is_ar ? '404' : '404' ); ?></p>
			<h1 id="shemo-404-title"><?php echo esc_html( $is_ar ? 'هذه الصفحة غير موجودة.' : 'This page is not here.' ); ?></h1>
			<p class="shemo-lead"><?php echo esc_html( $is_ar ? 'ربما تغير الرابط أو لم يتم بناء الصفحة بعد. يمكنك الرجوع للرئيسية أو البحث داخل الموقع.' : 'The link may have changed, or the page may not be built yet. You can go home or search the site.' ); ?></p>
			<div class="shemo-hero__actions">
				<a class="shemo-button" href="<?php echo esc_url( $home ); ?>"><?php echo esc_html( $is_ar ? 'العودة للرئيسية' : 'Back Home' ); ?></a>
				<a class="shemo-button shemo-button--secondary" href="<?php echo esc_url( $search ); ?>"><?php echo esc_html( $is_ar ? 'بحث' : 'Search' ); ?></a>
				<a class="shemo-button shemo-button--ghost" href="<?php echo esc_url( $contact ); ?>"><?php echo esc_html( $is_ar ? 'تواصل معنا' : 'Contact' ); ?></a>
			</div>
		</div>
		<div class="shemo-frame" aria-hidden="true">
			<div class="shemo-frame__screen"><p class="shemo-frame__label">missing frame / find path</p></div>
		</div>
	</section>
</main>

<?php
get_footer();
