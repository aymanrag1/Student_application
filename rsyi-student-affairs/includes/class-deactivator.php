<?php
/**
 * Plugin deactivation handler.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Deactivator {

	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'rsyi_send_document_reminders' );
		flush_rewrite_rules();
	}
}
