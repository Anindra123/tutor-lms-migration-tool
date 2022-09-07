<?php
/*
Plugin Name: Tutor LMS - Migration Tool
Plugin URI: https://www.themeum.com/
Description: A migration toolkit that allows you to migrate data from other LMS platforms to Tutor LMS.
Author: Themeum
Version: 2.0.1
Author URI: http://themeum.com
Requires at least: 5.3
Tested up to: 6.0
Requires PHP: 7.2
License: GPLv2 or later
Text Domain: tutor-lms-migration-tool
*/
include('classes/Dependency.php');

use TutorLMSMigrationTool\TLMT\Dependency;

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Defining Constant
 * @since v.1.0.0
 */

define('TLMT_VERSION', '2.0.0');
define('TLMT_FILE', __FILE__);
define('TLMT_PATH', plugin_dir_path( TLMT_FILE ));
define('TLMT_URL', plugin_dir_url( TLMT_FILE ));
define('TLMT_BASENAME', plugin_basename( TLMT_FILE ));
define('TLMT_PLUGIN_NAME', 'Tutor LMS - Migration Tool');
define('TLMT_TUTOR_CORE_REQ_VERSION', '2.0.0-rc');
define('TLMT_TUTOR_CORE_LATEST_VERSION', 'v2.0.0-rc');

register_activation_hook(__FILE__, 'tutor_migration_tool_activate');

function tutor_migration_tool_activate () {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}tutor_migration` (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`migration_type` varchar(48) NOT NULL DEFAULT '',
		`migration_vendor` varchar(48) NOT NULL DEFAULT '',
		`created_by` bigint(20) unsigned NOT NULL,
		`created_at` datetime NOT NULL,
		PRIMARY KEY (`id`)
	) $charset_collate";

	if(!function_exists('dbDelta')) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	}

	dbDelta($schema);
}

function tutor_migration_tool_deleted(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'tutor_migration';
		$query = "DROP TABLE IF EXISTS {$table_name}";
		$wpdb->query($query);
}

register_uninstall_hook(__FILE__, 'tutor_migration_tool_deleted');

if ( ! class_exists('TutorLMSMigrationTool')){

	$dependency = new Dependency;
	if ( ! $dependency->is_tutor_core_has_req_verion() ) {
		add_action( 'admin_notices', array( $dependency, 'show_admin_notice' ) );
		return;
	}

	include_once 'classes/TutorLMSMigrationTool.php';
	TutorLMSMigrationTool::instance();
}