<?php
/**
 * Interview model.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Interview {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_interviews';
	}

	public static function schedule( int $student_id, string $datetime, int $committee_size, int $scheduled_by, string $location = 'El Gouna' ): int {
		global $wpdb;

		$existing = self::get_for_student( $student_id );

		$row = array(
			'student_id'     => $student_id,
			'interview_date' => $datetime,
			'location'       => $location,
			'committee_size' => in_array( $committee_size, array( 3, 4 ), true ) ? $committee_size : 4,
			'scheduled_by'   => $scheduled_by,
			'created_at'     => current_time( 'mysql' ),
		);

		if ( $existing ) {
			$wpdb->update( self::table(), $row, array( 'id' => $existing['id'] ) );
			$id = (int) $existing['id'];
		} else {
			$wpdb->insert( self::table(), $row );
			$id = (int) $wpdb->insert_id;
		}

		RSYI_Student::change_status(
			$student_id,
			RSYI_Constants::STATUS_INTERVIEW_SCHEDULED,
			$scheduled_by,
			sprintf( __( 'موعد المقابلة: %s', 'rsyi-student-affairs' ), $datetime )
		);

		return $id;
	}

	public static function get( int $id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE id = %d", $id ), ARRAY_A );
		return $row ?: null;
	}

	public static function get_for_student( int $student_id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE student_id = %d", $student_id ),
			ARRAY_A
		);
		return $row ?: null;
	}

	public static function upcoming(): array {
		global $wpdb;
		return $wpdb->get_results(
			"SELECT i.*, s.full_name, s.national_id, s.mobile_1, s.application_code
			FROM " . self::table() . " i
			INNER JOIN " . RSYI_Student::table() . " s ON s.id = i.student_id
			WHERE i.interview_date >= NOW()
			ORDER BY i.interview_date ASC",
			ARRAY_A
		);
	}

	public static function on_date( string $date_ymd ): array {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT i.*, s.full_name, s.national_id, s.mobile_1, s.application_code
				FROM " . self::table() . " i
				INNER JOIN " . RSYI_Student::table() . " s ON s.id = i.student_id
				WHERE DATE(i.interview_date) = %s
				ORDER BY i.interview_date ASC",
				$date_ymd
			),
			ARRAY_A
		);
	}

	public static function mark_attended( int $id, int $attended ): bool {
		global $wpdb;
		return false !== $wpdb->update( self::table(), array( 'attended' => $attended ), array( 'id' => $id ) );
	}

	public static function update_totals( int $id, array $totals ): bool {
		global $wpdb;
		return false !== $wpdb->update( self::table(), $totals, array( 'id' => $id ) );
	}
}
