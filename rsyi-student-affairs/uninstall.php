<?php
/**
 * Uninstall handler - deletes all plugin data.
 *
 * IMPORTANT: only runs if the site admin has "Delete plugin" from admin UI.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

$keep_data = get_option( 'rsyi_keep_data_on_uninstall', false );
if ( $keep_data ) {
	return;
}

$tables = array(
	'rsyi_notifications',
	'rsyi_language_tests',
	'rsyi_medical_exams',
	'rsyi_committee_votes',
	'rsyi_interviews',
	'rsyi_status_log',
	'rsyi_documents',
	'rsyi_students',
);

foreach ( $tables as $table ) {
	$full = $wpdb->prefix . $table;
	$wpdb->query( "DROP TABLE IF EXISTS `{$full}`" );
}

delete_option( 'rsyi_db_version' );
delete_option( 'rsyi_settings' );
delete_option( 'rsyi_keep_data_on_uninstall' );
delete_option( 'rsyi_whatsapp_driver' );
delete_option( 'rsyi_whatsapp_config' );
delete_option( 'rsyi_api_token' );

$roles = array( 'rsyi_admin', 'rsyi_committee', 'rsyi_security', 'rsyi_medical' );
foreach ( $roles as $role ) {
	remove_role( $role );
}

$uploads = wp_upload_dir();
$dir = trailingslashit( $uploads['basedir'] ) . 'rsyi-docs';
if ( is_dir( $dir ) ) {
	rsyi_recursive_rmdir( $dir );
}

function rsyi_recursive_rmdir( string $dir ): void {
	if ( ! is_dir( $dir ) ) {
		return;
	}
	$items = scandir( $dir );
	foreach ( $items as $item ) {
		if ( $item === '.' || $item === '..' ) {
			continue;
		}
		$path = $dir . '/' . $item;
		if ( is_dir( $path ) ) {
			rsyi_recursive_rmdir( $path );
		} else {
			@unlink( $path );
		}
	}
	@rmdir( $dir );
}
