<?php
/**
 * Status Log model - tracks every status transition.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Status_Log {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_status_log';
	}

	public static function log( int $student_id, ?string $from, string $to, ?int $user_id = null, ?string $reason = null ): int {
		global $wpdb;

		$wpdb->insert(
			self::table(),
			array(
				'student_id' => $student_id,
				'from_status' => $from,
				'to_status'   => $to,
				'changed_by'  => $user_id,
				'reason'      => $reason,
				'changed_at'  => current_time( 'mysql' ),
			)
		);

		return (int) $wpdb->insert_id;
	}

	public static function get_history( int $student_id ): array {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . self::table() . " WHERE student_id = %d ORDER BY changed_at ASC",
				$student_id
			),
			ARRAY_A
		);
	}
}
