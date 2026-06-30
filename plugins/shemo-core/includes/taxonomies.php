<?php
/**
 * Registers the Project taxonomies.
 *
 * "Featured" is intentionally NOT a taxonomy — it's a boolean field on the
 * project itself (see includes/fields.php), per MASTER-PLAN.md §19.
 *
 * @see MASTER-PLAN.md §19 — Portfolio and Case-Study System
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'shemo_core_register_taxonomies', 11 );

function shemo_core_register_taxonomies() {
	$taxonomies = array(
		'service'        => array(
			'singular' => __( 'Service', 'shemo-core' ),
			'plural'   => __( 'Services', 'shemo-core' ),
			'hierarchical' => true,
		),
		'project_type'   => array(
			'singular' => __( 'Project Type', 'shemo-core' ),
			'plural'   => __( 'Project Types', 'shemo-core' ),
			'hierarchical' => true,
		),
		'industry'       => array(
			'singular' => __( 'Industry', 'shemo-core' ),
			'plural'   => __( 'Industries', 'shemo-core' ),
			'hierarchical' => true,
		),
		'platform'       => array(
			'singular' => __( 'Platform', 'shemo-core' ),
			'plural'   => __( 'Platforms', 'shemo-core' ),
			'hierarchical' => true,
		),
		'tool'           => array(
			'singular' => __( 'Tool', 'shemo-core' ),
			'plural'   => __( 'Tools', 'shemo-core' ),
			// Free-form, multiple per project (e.g. Premiere Pro, Figma) — tag-style, not a fixed tree.
			'hierarchical' => false,
		),
		'content_format' => array(
			'singular' => __( 'Content Format', 'shemo-core' ),
			'plural'   => __( 'Content Formats', 'shemo-core' ),
			'hierarchical' => true,
		),
		'client_type'    => array(
			'singular' => __( 'Client Type', 'shemo-core' ),
			'plural'   => __( 'Client Types', 'shemo-core' ),
			'hierarchical' => true,
		),
		'visual_style'   => array(
			'singular' => __( 'Visual Style', 'shemo-core' ),
			'plural'   => __( 'Visual Styles', 'shemo-core' ),
			'hierarchical' => true,
		),
	);

	foreach ( $taxonomies as $slug => $tax ) {
		register_taxonomy(
			$slug,
			array( 'project' ),
			array(
				'labels'            => array(
					'name'          => $tax['plural'],
					'singular_name' => $tax['singular'],
					'search_items'  => sprintf( __( 'Search %s', 'shemo-core' ), $tax['plural'] ),
					'all_items'     => sprintf( __( 'All %s', 'shemo-core' ), $tax['plural'] ),
					'edit_item'     => sprintf( __( 'Edit %s', 'shemo-core' ), $tax['singular'] ),
					'add_new_item'  => sprintf( __( 'Add New %s', 'shemo-core' ), $tax['singular'] ),
					'menu_name'     => $tax['plural'],
				),
				'public'            => true,
				'hierarchical'      => $tax['hierarchical'],
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'work/' . str_replace( '_', '-', $slug ) ),
			)
		);
	}
}
