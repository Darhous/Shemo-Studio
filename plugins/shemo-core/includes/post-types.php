<?php
/**
 * Registers the `project` Custom Post Type (the Portfolio / Case-Study system).
 *
 * @see MASTER-PLAN.md §19 — Portfolio and Case-Study System
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'shemo_core_register_post_types' );

function shemo_core_register_post_types() {
	$labels = array(
		'name'                  => __( 'Projects', 'shemo-core' ),
		'singular_name'         => __( 'Project', 'shemo-core' ),
		'menu_name'             => __( 'Projects', 'shemo-core' ),
		'add_new_item'          => __( 'Add New Project', 'shemo-core' ),
		'edit_item'             => __( 'Edit Project', 'shemo-core' ),
		'new_item'              => __( 'New Project', 'shemo-core' ),
		'view_item'             => __( 'View Project', 'shemo-core' ),
		'view_items'            => __( 'View Projects', 'shemo-core' ),
		'search_items'          => __( 'Search Projects', 'shemo-core' ),
		'not_found'             => __( 'No projects found', 'shemo-core' ),
		'not_found_in_trash'    => __( 'No projects found in Trash', 'shemo-core' ),
		'all_items'             => __( 'All Projects', 'shemo-core' ),
		'archives'              => __( 'Project Archives', 'shemo-core' ),
		'featured_image'        => __( 'Project Cover Image', 'shemo-core' ),
		'set_featured_image'    => __( 'Set cover image', 'shemo-core' ),
		'remove_featured_image' => __( 'Remove cover image', 'shemo-core' ),
	);

	register_post_type(
		'project',
		array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-portfolio',
			'menu_position'      => 5,
			'hierarchical'       => false,
			'has_archive'        => 'work',
			'rewrite'            => array(
				'slug'       => 'work',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);
}
