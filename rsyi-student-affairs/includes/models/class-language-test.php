<?php
/**
 * Language Test model.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Language_Test {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_language_tests';
	}

	public static function upsert( int $student_id, array $data, int $user_id ): int {
		global $wpdb;

		$level  = $data['level'] ?? '';
		$passed = in_array( $level, RSYI_Constants::accepted_language_levels(), true ) ? 1 : 0;

		$existing = self::get_for_student( $student_id );

		$row = array(
			'student_id'  => $student_id,
			'test_date'   => $data['test_date'] ?? current_time( 'Y-m-d' ),
			'level'       => $level,
			'passed'      => $passed,
			'notes'       => $data['notes'] ?? null,
			'recorded_by' => $user_id,
			'created_at'  => current_time( 'mysql' ),
		);

		if ( $existing ) {
			$wpdb->update( self::table(), $row, array( 'id' => (int) $existing['id'] ) );
			$id = (int) $existing['id'];
		} else {
			$wpdb->insert( self::table(), $row );
			$id = (int) $wpdb->insert_id;
		}

		RSYI_Student::change_status( $student_id, RSYI_Constants::STATUS_LANGUAGE_TESTED, $user_id );

		$student = RSYI_Student::get( $student_id );
		$medical = RSYI_Medical_Exam::get_for_student( $student_id );

		$medical_passed = $medical && $medical['overall_result'] === RSYI_Constants::MEDICAL_FIT;

		if ( $passed && $medical_passed ) {
			RSYI_Student::change_status( $student_id, RSYI_Constants::STATUS_FINAL_ACCEPTED, $user_id, __( 'اجتاز جميع المراحل', 'rsyi-student-affairs' ) );
		} elseif ( ! $passed ) {
			RSYI_Student::change_status( $student_id, RSYI_Constants::STATUS_FINAL_REJECTED, $user_id, __( 'مستوى لغة أقل من Elementary', 'rsyi-student-affairs' ) );
		}

		return $id;
	}

	public static function get_for_student( int $student_id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE student_id = %d", $student_id ),
			ARRAY_A
		);
		return $row ?: null;
	}

	public static function level_label( string $level ): string {
		$labels = array(
			RSYI_Constants::LANG_BEGINNER          => __( 'Beginner - مبتدئ', 'rsyi-student-affairs' ),
			RSYI_Constants::LANG_ELEMENTARY        => __( 'Elementary - أساسي', 'rsyi-student-affairs' ),
			RSYI_Constants::LANG_PRE_INTERMEDIATE  => __( 'Pre-Intermediate - قبل المتوسط', 'rsyi-student-affairs' ),
			RSYI_Constants::LANG_INTERMEDIATE      => __( 'Intermediate - متوسط', 'rsyi-student-affairs' ),
			RSYI_Constants::LANG_UPPER_INTERMEDIATE => __( 'Upper-Intermediate - فوق المتوسط', 'rsyi-student-affairs' ),
		);
		return $labels[ $level ] ?? $level;
	}
}
