<?php
/**
 * Plugin activation handler.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Activator {

	public static function activate(): void {
		require_once RSYI_PLUGIN_DIR . 'includes/class-installer.php';
		require_once RSYI_PLUGIN_DIR . 'includes/class-roles.php';

		RSYI_Installer::install();
		RSYI_Roles::install();

		self::ensure_upload_directory();
		self::schedule_cron_events();

		flush_rewrite_rules();
	}

	private static function ensure_upload_directory(): void {
		$uploads = wp_upload_dir();
		$dir = trailingslashit( $uploads['basedir'] ) . RSYI_UPLOADS_SUBDIR;

		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		$htaccess = $dir . '/.htaccess';
		if ( ! file_exists( $htaccess ) ) {
			$rules  = "Order deny,allow\n";
			$rules .= "Deny from all\n";
			file_put_contents( $htaccess, $rules );
		}

		$index = $dir . '/index.php';
		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, "<?php\n// Silence is golden.\n" );
		}
	}

	private static function schedule_cron_events(): void {
		if ( ! wp_next_scheduled( 'rsyi_send_document_reminders' ) ) {
			wp_schedule_event( time(), 'daily', 'rsyi_send_document_reminders' );
		}
	}
}
