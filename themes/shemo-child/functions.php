<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'shemo_child_enqueue_styles' );
add_action( 'after_setup_theme', 'shemo_child_theme_setup' );
add_action( 'enqueue_block_editor_assets', 'shemo_child_enqueue_editor_assets' );
add_action( 'init', 'shemo_child_register_block_styles' );
add_action( 'pre_get_posts', 'shemo_child_filter_project_archive_query' );
add_action( 'wp_head', 'shemo_child_project_archive_canonical_tag', 1 );
add_filter( 'language_attributes', 'shemo_child_language_attributes_dir', 20 );
add_filter( 'generate_sidebar_layout', 'shemo_child_sidebar_layout' );
add_shortcode( 'shemo_packages', 'shemo_child_packages_shortcode' );
add_shortcode( 'shemo_testimonials', 'shemo_child_testimonials_shortcode' );

require_once get_stylesheet_directory() . '/inc/rank-math-polylang.php';

function shemo_child_theme_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}

function shemo_child_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Aref+Ruqaa:wght@400;700&family=Fraunces:opsz,wght@9..144,600..900&family=Inter:wght@400;600;700;800&family=Noto+Kufi+Arabic:wght@400;600;700;800&display=swap';
}

function shemo_child_enqueue_styles() {
	wp_enqueue_style(
		'generatepress',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		'shemo-fonts',
		shemo_child_fonts_url(),
		array(),
		null
	);

	wp_enqueue_style(
		'shemo-child',
		get_stylesheet_uri(),
		array( 'generatepress', 'shemo-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
}

function shemo_child_enqueue_editor_assets() {
	wp_enqueue_style(
		'shemo-fonts-editor',
		shemo_child_fonts_url(),
		array(),
		null
	);
}

function shemo_child_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-primary',
			'label' => __( 'Shemo Primary', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-secondary',
			'label' => __( 'Shemo Secondary', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'shemo-ghost',
			'label' => __( 'Shemo Ghost', 'shemo-child' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'shemo-card',
			'label' => __( 'Shemo Card', 'shemo-child' ),
		)
	);
}

function shemo_child_current_language(): string {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );

		if ( is_string( $lang ) && '' !== $lang ) {
			return $lang;
		}
	}

	return is_rtl() ? 'ar' : 'en';
}

function shemo_child_language_attributes_dir( string $output ): string {
	if ( false !== strpos( $output, 'dir=' ) ) {
		return $output;
	}

	return trim( $output . ' dir="' . esc_attr( is_rtl() ? 'rtl' : 'ltr' ) . '"' );
}

function shemo_child_project_archive_canonical_tag(): void {
	if ( ! is_post_type_archive( 'project' ) ) {
		return;
	}

	$archive = get_post_type_archive_link( 'project' );
	if ( ! $archive ) {
		return;
	}

	printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( trailingslashit( $archive ) ) );
}

function shemo_child_sidebar_layout( string $layout ): string {
	if ( is_page() || is_post_type_archive( 'project' ) || is_singular( 'project' ) || is_search() || is_404() ) {
		return 'no-sidebar';
	}

	return $layout;
}

function shemo_child_demo_project_label( ?string $lang = null ): string {
	$lang = $lang ?: shemo_child_current_language();

	return 'ar' === $lang
		? 'مشروع تجريبي / Concept - غير منفّذ لعميل تجاري'
		: 'Demo / Concept Project - Not commissioned by a client';
}

function shemo_child_project_archive_filters(): array {
	if ( 'ar' === shemo_child_current_language() ) {
		return array(
			'service'        => 'الخدمة',
			'project_type'   => 'نوع المشروع',
			'industry'       => 'القطاع',
			'platform'       => 'المنصة',
			'tool'           => 'الأداة',
			'content_format' => 'صيغة المحتوى',
			'client_type'    => 'حالة العميل',
			'visual_style'   => 'الاتجاه البصري',
		);
	}

	return array(
		'service'        => 'Service',
		'project_type'   => 'Project Type',
		'industry'       => 'Industry',
		'platform'       => 'Platform',
		'tool'           => 'Tool',
		'content_format' => 'Content Format',
		'client_type'    => 'Client Type',
		'visual_style'   => 'Visual Style',
	);
}

function shemo_child_filter_project_archive_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'project' ) ) {
		return;
	}

	$query->set( 'posts_per_page', 9 );
	$query->set( 'orderby', 'menu_order date' );
	$query->set( 'order', 'DESC' );

	$tax_query = array();

	foreach ( array_keys( shemo_child_project_archive_filters() ) as $taxonomy ) {
		if ( empty( $_GET[ $taxonomy ] ) ) {
			continue;
		}

		$slug = sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) );
		$term = get_term_by( 'slug', $slug, $taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slug,
		);
	}

	if ( ! empty( $tax_query ) ) {
		$tax_query['relation'] = 'AND';
		$query->set( 'tax_query', $tax_query );
	}
}

function shemo_child_packages_shortcode(): string {
	$lang  = shemo_child_current_language();
	$is_ar = 'ar' === $lang;
	$query = new WP_Query(
		array(
			'post_type'           => 'package',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'menu_order date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'lang'                => $lang,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="shemo-package-grid shemo-package-grid--dynamic">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$package_id = get_the_ID();
			$from       = get_post_meta( $package_id, 'shemo_package_price_from', true );
			$to         = get_post_meta( $package_id, 'shemo_package_price_to', true );
			$currency   = get_post_meta( $package_id, 'shemo_package_currency', true );
			$note       = get_post_meta( $package_id, 'shemo_package_price_note', true );
			$scope      = get_post_meta( $package_id, 'shemo_package_scope', true );
			$best_for   = get_post_meta( $package_id, 'shemo_package_best_for', true );
			$timeline   = get_post_meta( $package_id, 'shemo_package_timeline', true );
			$revisions  = get_post_meta( $package_id, 'shemo_package_revisions', true );
			$deposit    = get_post_meta( $package_id, 'shemo_package_deposit_percent', true );
			$checkout   = get_post_meta( $package_id, 'shemo_package_checkout_url', true );
			$featured   = (bool) get_post_meta( $package_id, 'shemo_package_featured', true );
			?>
			<article class="shemo-package-card<?php echo $featured ? ' shemo-package-card--featured' : ''; ?>">
				<span class="shemo-tag"><?php echo esc_html( get_post_meta( $package_id, 'shemo_package_label', true ) ); ?></span>
				<h3><?php the_title(); ?></h3>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<strong class="shemo-price-line">
					<?php echo esc_html( trim( number_format_i18n( (float) $from ) . ' - ' . number_format_i18n( (float) $to ) . ' ' . $currency ) ); ?>
				</strong>
				<?php if ( $note ) : ?>
					<p class="shemo-package-card__fit"><?php echo esc_html( $note ); ?></p>
				<?php endif; ?>
				<?php if ( is_array( $scope ) ) : ?>
					<ul class="shemo-check-list">
						<?php foreach ( $scope as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<dl class="shemo-package-meta">
					<?php if ( $best_for ) : ?>
						<div><dt><?php echo esc_html( $is_ar ? 'مناسب لـ' : 'Best for' ); ?></dt><dd><?php echo esc_html( $best_for ); ?></dd></div>
					<?php endif; ?>
					<?php if ( $timeline ) : ?>
						<div><dt><?php echo esc_html( $is_ar ? 'المدة' : 'Timeline' ); ?></dt><dd><?php echo esc_html( $timeline ); ?></dd></div>
					<?php endif; ?>
					<?php if ( '' !== (string) $revisions ) : ?>
						<div><dt><?php echo esc_html( $is_ar ? 'المراجعات' : 'Revisions' ); ?></dt><dd><?php echo esc_html( $revisions ); ?></dd></div>
					<?php endif; ?>
					<?php if ( '' !== (string) $deposit ) : ?>
						<div><dt><?php echo esc_html( $is_ar ? 'العربون التجريبي' : 'Demo deposit' ); ?></dt><dd><?php echo esc_html( $deposit . '%' ); ?></dd></div>
					<?php endif; ?>
				</dl>
				<div class="shemo-button-row">
					<a class="shemo-button" href="<?php echo esc_url( $checkout ? $checkout : home_url( $is_ar ? '/request-a-quote/' : '/en/request-a-quote-en/' ) ); ?>"><?php echo esc_html( $is_ar ? 'اطلب هذه الباقة' : 'Request this package' ); ?></a>
					<a class="shemo-button shemo-button--secondary" href="<?php echo esc_url( home_url( $is_ar ? '/request-a-quote/' : '/en/request-a-quote-en/' ) ); ?>"><?php echo esc_html( $is_ar ? 'اطلب عرضًا مخصصًا' : 'Request a custom quote' ); ?></a>
				</div>
			</article>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function shemo_child_testimonials_shortcode(): string {
	$lang  = shemo_child_current_language();
	$is_ar = 'ar' === $lang;
	$query = new WP_Query(
		array(
			'post_type'           => 'testimonial',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'orderby'             => 'menu_order date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'lang'                => $lang,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="shemo-testimonial-grid">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$testimonial_id = get_the_ID();
			$rating         = absint( get_post_meta( $testimonial_id, 'shemo_testimonial_rating', true ) );
			$author         = get_post_meta( $testimonial_id, 'shemo_testimonial_author_name', true );
			$role           = get_post_meta( $testimonial_id, 'shemo_testimonial_author_role', true );
			$service        = get_post_meta( $testimonial_id, 'shemo_testimonial_service_focus', true );
			$note           = get_post_meta( $testimonial_id, 'shemo_testimonial_source_note', true );
			?>
			<article class="shemo-testimonial-card">
				<span class="shemo-tag"><?php echo esc_html( get_post_meta( $testimonial_id, 'shemo_testimonial_label', true ) ); ?></span>
				<?php if ( $rating ) : ?>
					<p class="shemo-rating" aria-label="<?php echo esc_attr( sprintf( $is_ar ? 'تقييم %d من 5' : '%d out of 5 rating', $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', min( 5, $rating ) ) ); ?></p>
				<?php endif; ?>
				<blockquote><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></blockquote>
				<footer>
					<strong><?php echo esc_html( $author ); ?></strong>
					<span><?php echo esc_html( $role ); ?></span>
					<?php if ( $service ) : ?>
						<span><?php echo esc_html( $service ); ?></span>
					<?php endif; ?>
				</footer>
				<?php if ( $note ) : ?>
					<p class="shemo-package-card__fit"><?php echo esc_html( $note ); ?></p>
				<?php endif; ?>
			</article>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<?php
	return (string) ob_get_clean();
}
