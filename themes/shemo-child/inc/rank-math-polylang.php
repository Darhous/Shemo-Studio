<?php
/**
 * Rank Math compatibility for the Polylang directory setup used by Shemo.
 */

defined( 'ABSPATH' ) || exit;

function shemo_child_rank_math_polylang_bootstrap() {
	if ( ! defined( 'RANK_MATH_VERSION' ) || ! function_exists( 'PLL' ) ) {
		return;
	}

	if ( did_action( 'pll_init' ) ) {
		shemo_child_rank_math_polylang_init();
		return;
	}

	add_action( 'pll_init', 'shemo_child_rank_math_polylang_init' );
}

function shemo_child_rank_math_polylang_init() {
	static $initialized = false;

	if ( $initialized ) {
		return;
	}

	if ( ! function_exists( 'pll_is_translated_post_type' ) ) {
		return;
	}

	$initialized = true;

	add_filter( 'rank_math/sitemap/enable_caching', '__return_false' );
	add_filter( 'rank_math/sitemap/post_count/join', 'shemo_child_rank_math_sitemap_join', 10, 2 );
	add_filter( 'rank_math/sitemap/post_count/where', 'shemo_child_rank_math_sitemap_where', 10, 2 );
	add_filter( 'rank_math/sitemap/get_posts/join', 'shemo_child_rank_math_sitemap_join', 10, 2 );
	add_filter( 'rank_math/sitemap/get_posts/where', 'shemo_child_rank_math_sitemap_where', 10, 2 );
	add_filter( 'get_terms_args', 'shemo_child_rank_math_sitemap_terms_args' );
	add_filter( 'rank_math/frontend/canonical', 'shemo_child_rank_math_home_canonical' );
	add_filter( 'pll_copy_post_meta', 'shemo_child_rank_math_copy_post_meta', 10, 4 );
	add_filter( 'pll_translate_post_meta', 'shemo_child_rank_math_translate_post_meta', 10, 3 );
	add_filter( 'pll_post_metas_to_export', 'shemo_child_rank_math_export_post_meta' );
	add_action( 'wp_loaded', 'shemo_child_rank_math_register_option_translations' );
}

function shemo_child_rank_math_sitemap_join( $join, $post_type ) {
	if ( ! pll_is_translated_post_type( $post_type ) || empty( PLL()->model->post ) ) {
		return $join;
	}

	return $join . PLL()->model->post->join_clause( 'p' );
}

function shemo_child_rank_math_sitemap_where( $where, $post_type ) {
	if ( ! pll_is_translated_post_type( $post_type ) || empty( PLL()->model->post ) ) {
		return $where;
	}

	$languages = function_exists( 'pll_languages_list' ) ? pll_languages_list() : array();

	return empty( $languages ) ? $where : $where . PLL()->model->post->where_clause( $languages );
}

function shemo_child_rank_math_sitemap_terms_args( $args ) {
	if ( isset( $GLOBALS['wp_query']->query['sitemap'] ) && function_exists( 'pll_languages_list' ) ) {
		$args['lang'] = implode( ',', pll_languages_list() );
	}

	return $args;
}

function shemo_child_rank_math_home_canonical( $canonical ) {
	if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_home_url' ) ) {
		return $canonical;
	}

	if ( is_front_page() || is_home() ) {
		return trailingslashit( pll_home_url( pll_current_language( 'slug' ) ) );
	}

	return $canonical;
}

function shemo_child_rank_math_copy_post_meta( $metas, $sync, $from, $to ) {
	if ( $sync ) {
		return $metas;
	}

	$metas = array_merge(
		$metas,
		shemo_child_rank_math_translatable_meta_keys(),
		array(
			'rank_math_facebook_image',
			'rank_math_facebook_image_id',
			'rank_math_twitter_use_facebook',
			'rank_math_twitter_image',
			'rank_math_twitter_image_id',
			'rank_math_robots',
		)
	);

	foreach ( get_taxonomies( array( 'public' => true ), 'names' ) as $taxonomy ) {
		if ( function_exists( 'pll_is_translated_taxonomy' ) && pll_is_translated_taxonomy( $taxonomy ) ) {
			$metas[] = 'rank_math_primary_' . $taxonomy;
		}
	}

	return array_values( array_unique( $metas ) );
}

function shemo_child_rank_math_translate_post_meta( $value, $key, $lang ) {
	if ( 0 !== strpos( (string) $key, 'rank_math_primary_' ) || ! function_exists( 'pll_get_term' ) ) {
		return $value;
	}

	$taxonomy = substr( (string) $key, strlen( 'rank_math_primary_' ) );
	if ( ! function_exists( 'pll_is_translated_taxonomy' ) || ! pll_is_translated_taxonomy( $taxonomy ) ) {
		return $value;
	}

	$translated = pll_get_term( (int) $value, $lang );

	return $translated ? $translated : $value;
}

function shemo_child_rank_math_export_post_meta( $metas ) {
	return array_merge( $metas, array_fill_keys( shemo_child_rank_math_translatable_meta_keys(), 1 ) );
}

function shemo_child_rank_math_translatable_meta_keys() {
	return array(
		'rank_math_title',
		'rank_math_description',
		'rank_math_facebook_title',
		'rank_math_facebook_description',
		'rank_math_twitter_title',
		'rank_math_twitter_description',
		'rank_math_focus_keyword',
	);
}

function shemo_child_rank_math_register_option_translations() {
	if ( ! class_exists( 'PLL_Translate_Option' ) ) {
		return;
	}

	new PLL_Translate_Option(
		'rank-math-options-general',
		array_fill_keys(
			array(
				'breadcrumbs_separator',
				'breadcrumbs_home_label',
				'breadcrumbs_archive_format',
				'breadcrumbs_search_format',
				'breadcrumbs_404_label',
				'toc_block_title',
			),
			1
		),
		array( 'context' => 'rank-math' )
	);

	new PLL_Translate_Option(
		'rank-math-options-titles',
		array_fill_keys(
			array(
				'website_name',
				'knowledgegraph_name',
				'homepage_title',
				'author_archive_title',
				'date_archive_title',
				'search_title',
				'404_title',
				'pt_post_title',
				'pt_post_description',
				'pt_page_title',
				'pt_page_description',
				'pt_attachment_title',
				'pt_attachment_description',
				'pt_project_title',
				'pt_project_description',
			),
			1
		),
		array( 'context' => 'rank-math' )
	);
}

shemo_child_rank_math_polylang_bootstrap();
