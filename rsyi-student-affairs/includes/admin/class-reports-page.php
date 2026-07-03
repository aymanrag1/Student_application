<?php
/**
 * Reports admin page - exports & basic stats.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Reports_Page {

	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية.', 'rsyi-student-affairs' ) );
		}

		$stats = self::compute_stats();
		$pipeline = RSYI_Candidates_Page::status_pipeline();

		include RSYI_PLUGIN_DIR . 'templates/admin/reports-page.php';
	}

	public static function compute_stats(): array {
		global $wpdb;
		$table = RSYI_Student::table();

		$rows = $wpdb->get_results( "SELECT status, COUNT(*) AS cnt FROM {$table} GROUP BY status", ARRAY_A );

		$stats = array();
		foreach ( $rows as $r ) {
			$stats[ $r['status'] ] = (int) $r['cnt'];
		}
		return $stats;
	}
}
