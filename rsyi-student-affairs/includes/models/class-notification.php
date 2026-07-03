<?php
/**
 * Notification model - logs every notification (whatsapp / email).
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Notification {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_notifications';
	}

	public static function log( array $data ): int {
		global $wpdb;

		$defaults = array(
			'status'     => 'pending',
			'created_at' => current_time( 'mysql' ),
		);

		$wpdb->insert( self::table(), wp_parse_args( $data, $defaults ) );
		return (int) $wpdb->insert_id;
	}

	public static function mark_sent( int $id, ?string $provider_response = null ): void {
		global $wpdb;
		$wpdb->update(
			self::table(),
			array(
				'status'            => 'sent',
				'sent_at'           => current_time( 'mysql' ),
				'provider_response' => $provider_response,
			),
			array( 'id' => $id )
		);
	}

	public static function mark_failed( int $id, string $error, ?string $provider_response = null ): void {
		global $wpdb;
		$wpdb->update(
			self::table(),
			array(
				'status'            => 'failed',
				'error_message'     => $error,
				'provider_response' => $provider_response,
			),
			array( 'id' => $id )
		);
	}

	public static function get_for_student( int $student_id, int $limit = 50 ): array {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . self::table() . " WHERE student_id = %d ORDER BY created_at DESC LIMIT %d",
				$student_id,
				$limit
			),
			ARRAY_A
		);
	}
}
