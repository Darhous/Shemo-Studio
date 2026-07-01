<?php
/**
 * Registers all Project custom fields via the free Meta Box plugin
 * (https://metabox.io — code-based config, no GUI builder used).
 *
 * Field groups mirror the case-study structure in MASTER-PLAN.md §19.
 * "Featured" lives here as a checkbox, not a taxonomy.
 *
 * A single image-or-video field type doesn't exist in free Meta Box, so the
 * "sketch / before / after" slots from §19 are each split into an image
 * field + an optional video (oEmbed) field — same data, two plain fields.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'rwmb_meta_boxes', 'shemo_core_register_fields' );

function shemo_core_register_fields( $meta_boxes ) {

	// 1. Project Details.
	$meta_boxes[] = array(
		'title'      => __( 'Project Details', 'shemo-core' ),
		'id'         => 'shemo_project_details',
		'post_types' => 'project',
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => array(
			array(
				'name'    => __( 'Client Label', 'shemo-core' ),
				'id'      => 'shemo_client_label',
				'type'    => 'select',
				'options' => array(
					'named'        => __( 'Named Client', 'shemo-core' ),
					'confidential' => __( 'Confidential Client (NDA)', 'shemo-core' ),
					'unnamed'      => __( 'Unnamed (sector + outcome only)', 'shemo-core' ),
					'personal'     => __( 'Personal / Concept / Student Project', 'shemo-core' ),
				),
				'std'     => 'named',
			),
			array(
				'name' => __( 'Client Name', 'shemo-core' ),
				'id'   => 'shemo_client_name',
				'type' => 'text',
				'desc' => __( 'Leave blank if Client Label is Confidential or Unnamed.', 'shemo-core' ),
			),
			array(
				'name' => __( 'Short Summary', 'shemo-core' ),
				'id'   => 'shemo_short_summary',
				'type' => 'textarea',
				'rows' => 3,
			),
			array(
				'name' => __( 'Project Date', 'shemo-core' ),
				'id'   => 'shemo_project_date',
				'type' => 'date',
			),
			array(
				'name' => __( 'Project Goal', 'shemo-core' ),
				'id'   => 'shemo_project_goal',
				'type' => 'textarea',
				'rows' => 3,
			),
			array(
				'name' => __( 'Challenge', 'shemo-core' ),
				'id'   => 'shemo_challenge',
				'type' => 'wysiwyg',
			),
			array(
				'name' => __( 'Creative Direction', 'shemo-core' ),
				'id'   => 'shemo_creative_direction',
				'type' => 'wysiwyg',
			),
			array(
				'name'       => __( 'Deliverables', 'shemo-core' ),
				'id'         => 'shemo_deliverables',
				'type'       => 'text',
				'clone'      => true,
				'add_button' => __( '+ Add Deliverable', 'shemo-core' ),
			),
			array(
				'name' => __( 'External Link', 'shemo-core' ),
				'id'   => 'shemo_external_link',
				'type' => 'url',
			),
			array(
				'name' => __( 'Featured Project', 'shemo-core' ),
				'id'   => 'shemo_featured',
				'type' => 'checkbox',
				'desc' => __( 'Mark this project as featured (e.g. homepage highlight).', 'shemo-core' ),
			),
		),
	);

	// 2. Sketch to Screen — the brand-defining narrative section.
	$meta_boxes[] = array(
		'title'      => __( 'Sketch to Screen', 'shemo-core' ),
		'id'         => 'shemo_sketch_to_screen',
		'post_types' => 'project',
		'context'    => 'normal',
		'fields'     => array(
			array(
				'name'             => __( 'Sketch — Image', 'shemo-core' ),
				'id'               => 'shemo_sketch_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name' => __( 'Sketch — Video (optional)', 'shemo-core' ),
				'id'   => 'shemo_sketch_video',
				'type' => 'oembed',
			),
			array(
				'name'             => __( 'Before — Image', 'shemo-core' ),
				'id'               => 'shemo_before_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name' => __( 'Before — Video (optional)', 'shemo-core' ),
				'id'   => 'shemo_before_video',
				'type' => 'oembed',
			),
			array(
				'name'             => __( 'After — Image', 'shemo-core' ),
				'id'               => 'shemo_after_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
			),
			array(
				'name' => __( 'After — Video (optional)', 'shemo-core' ),
				'id'   => 'shemo_after_video',
				'type' => 'oembed',
			),
		),
	);

	// 3. Media.
	$meta_boxes[] = array(
		'title'      => __( 'Media', 'shemo-core' ),
		'id'         => 'shemo_media',
		'post_types' => 'project',
		'context'    => 'normal',
		'fields'     => array(
			array(
				'name' => __( 'Main Video (Vimeo)', 'shemo-core' ),
				'id'   => 'shemo_main_video',
				'type' => 'oembed',
			),
			array(
				'name' => __( 'Gallery', 'shemo-core' ),
				'id'   => 'shemo_gallery',
				'type' => 'image_advanced',
			),
		),
	);

	// 4. Results & Credibility.
	$meta_boxes[] = array(
		'title'      => __( 'Results & Credibility', 'shemo-core' ),
		'id'         => 'shemo_results_credibility',
		'post_types' => 'project',
		'context'    => 'normal',
		'fields'     => array(
			array(
				'name'       => __( 'Results', 'shemo-core' ),
				'id'         => 'shemo_results',
				'type'       => 'group',
				'clone'      => true,
				'add_button' => __( '+ Add Result', 'shemo-core' ),
				'fields'     => array(
					array(
						'name' => __( 'Metric', 'shemo-core' ),
						'id'   => 'metric',
						'type' => 'text',
					),
					array(
						'name' => __( 'Value', 'shemo-core' ),
						'id'   => 'value',
						'type' => 'text',
					),
				),
			),
			array(
				'name'   => __( 'Testimonial', 'shemo-core' ),
				'id'     => 'shemo_testimonial',
				'type'   => 'group',
				'fields' => array(
					array(
						'name' => __( 'Quote', 'shemo-core' ),
						'id'   => 'quote',
						'type' => 'textarea',
						'rows' => 3,
					),
					array(
						'name' => __( 'Author Name', 'shemo-core' ),
						'id'   => 'author_name',
						'type' => 'text',
					),
					array(
						'name' => __( 'Author Role', 'shemo-core' ),
						'id'   => 'author_role',
						'type' => 'text',
					),
					array(
						'name'             => __( 'Author Photo', 'shemo-core' ),
						'id'               => 'author_photo',
						'type'             => 'image_advanced',
						'max_file_uploads' => 1,
					),
				),
			),
			array(
				'name'       => __( 'Credits', 'shemo-core' ),
				'id'         => 'shemo_credits',
				'type'       => 'group',
				'clone'      => true,
				'add_button' => __( '+ Add Credit', 'shemo-core' ),
				'fields'     => array(
					array(
						'name' => __( 'Role', 'shemo-core' ),
						'id'   => 'role',
						'type' => 'text',
					),
					array(
						'name' => __( 'Name', 'shemo-core' ),
						'id'   => 'name',
						'type' => 'text',
					),
				),
			),
		),
	);

	// 5. Relationships.
	$meta_boxes[] = array(
		'title'      => __( 'Related Projects', 'shemo-core' ),
		'id'         => 'shemo_relationships',
		'post_types' => 'project',
		'context'    => 'side',
		'fields'     => array(
			array(
				'name'       => __( 'Related Projects', 'shemo-core' ),
				'id'         => 'shemo_related_projects',
				'type'       => 'post',
				'post_type'  => 'project',
				'field_type' => 'select_advanced',
				'multiple'   => true,
				'query_args' => array(
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				),
			),
		),
	);

	// 6. Package commercial/demo data.
	$meta_boxes[] = array(
		'title'      => __( 'Package Details', 'shemo-core' ),
		'id'         => 'shemo_package_details',
		'post_types' => 'package',
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => array(
			array(
				'name' => __( 'Transparency Label', 'shemo-core' ),
				'id'   => 'shemo_package_label',
				'type' => 'text',
			),
			array(
				'name' => __( 'Price From', 'shemo-core' ),
				'id'   => 'shemo_package_price_from',
				'type' => 'number',
				'min'  => 0,
				'step' => 1,
			),
			array(
				'name' => __( 'Price To', 'shemo-core' ),
				'id'   => 'shemo_package_price_to',
				'type' => 'number',
				'min'  => 0,
				'step' => 1,
			),
			array(
				'name' => __( 'Currency', 'shemo-core' ),
				'id'   => 'shemo_package_currency',
				'type' => 'text',
				'std'  => 'EGP',
			),
			array(
				'name' => __( 'Price Note', 'shemo-core' ),
				'id'   => 'shemo_package_price_note',
				'type' => 'textarea',
				'rows' => 2,
			),
			array(
				'name'       => __( 'Scope Items', 'shemo-core' ),
				'id'         => 'shemo_package_scope',
				'type'       => 'text',
				'clone'      => true,
				'add_button' => __( '+ Add Scope Item', 'shemo-core' ),
			),
			array(
				'name' => __( 'Best For', 'shemo-core' ),
				'id'   => 'shemo_package_best_for',
				'type' => 'textarea',
				'rows' => 2,
			),
			array(
				'name' => __( 'Timeline', 'shemo-core' ),
				'id'   => 'shemo_package_timeline',
				'type' => 'text',
			),
			array(
				'name' => __( 'Revision Count', 'shemo-core' ),
				'id'   => 'shemo_package_revisions',
				'type' => 'number',
				'min'  => 0,
				'step' => 1,
			),
			array(
				'name' => __( 'Deposit Percent', 'shemo-core' ),
				'id'   => 'shemo_package_deposit_percent',
				'type' => 'number',
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			array(
				'name' => __( 'Checkout URL', 'shemo-core' ),
				'id'   => 'shemo_package_checkout_url',
				'type' => 'url',
			),
			array(
				'name' => __( 'Featured Package', 'shemo-core' ),
				'id'   => 'shemo_package_featured',
				'type' => 'checkbox',
			),
		),
	);

	// 7. Demo testimonial data.
	$meta_boxes[] = array(
		'title'      => __( 'Testimonial Details', 'shemo-core' ),
		'id'         => 'shemo_testimonial_details',
		'post_types' => 'testimonial',
		'context'    => 'normal',
		'priority'   => 'high',
		'fields'     => array(
			array(
				'name' => __( 'Transparency Label', 'shemo-core' ),
				'id'   => 'shemo_testimonial_label',
				'type' => 'text',
			),
			array(
				'name' => __( 'Author Name / Demo Persona', 'shemo-core' ),
				'id'   => 'shemo_testimonial_author_name',
				'type' => 'text',
			),
			array(
				'name' => __( 'Author Role', 'shemo-core' ),
				'id'   => 'shemo_testimonial_author_role',
				'type' => 'text',
			),
			array(
				'name' => __( 'Service Focus', 'shemo-core' ),
				'id'   => 'shemo_testimonial_service_focus',
				'type' => 'text',
			),
			array(
				'name' => __( 'Rating', 'shemo-core' ),
				'id'   => 'shemo_testimonial_rating',
				'type' => 'number',
				'min'  => 1,
				'max'  => 5,
				'step' => 1,
			),
			array(
				'name'       => __( 'Related Project', 'shemo-core' ),
				'id'         => 'shemo_testimonial_related_project',
				'type'       => 'post',
				'post_type'  => 'project',
				'field_type' => 'select_advanced',
			),
			array(
				'name' => __( 'Source Note', 'shemo-core' ),
				'id'   => 'shemo_testimonial_source_note',
				'type' => 'textarea',
				'rows' => 2,
			),
		),
	);

	return $meta_boxes;
}
