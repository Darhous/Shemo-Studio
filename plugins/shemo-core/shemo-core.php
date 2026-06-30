<?php
/**
 * Plugin Name:       Shemo Core
 * Description:       In-house content model for Shemo Studio — Projects CPT, taxonomies, and custom fields. Keeps portfolio data portable and independent of any theme or page builder.
 * Version:           1.0.0
 * Requires Plugins:  meta-box
 * Author:            Shemo Studio
 * Text Domain:       shemo-core
 */

defined( 'ABSPATH' ) || exit;

define( 'SHEMO_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SHEMO_CORE_VERSION', '1.0.0' );

require_once SHEMO_CORE_PATH . 'includes/post-types.php';
require_once SHEMO_CORE_PATH . 'includes/taxonomies.php';
require_once SHEMO_CORE_PATH . 'includes/fields.php';

/**
 * Custom fields depend on the free Meta Box plugin. Warn instead of failing
 * silently if it's missing or deactivated, since project data would still
 * register (CPT + taxonomies) but the field UI would not appear.
 */
add_action( 'admin_notices', 'shemo_core_dependency_notice' );
function shemo_core_dependency_notice() {
	if ( class_exists( 'RWMB_Loader' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Shemo Core requires the free "Meta Box" plugin to display Project fields. Please install and activate it.', 'shemo-core' );
	echo '</p></div>';
}
