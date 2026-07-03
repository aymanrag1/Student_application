<?php
/**
 * Medical Exam model.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Medical_Exam {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_medical_exams';
	}

	public static function upsert( int $student_id, array $data, int $user_id ): int {
		global $wpdb;

		$existing = self::get_for_student( $student_id );

		$overall = self::compute_overall( $data );

		$row = array(
			'student_id'         => $student_id,
			'exam_date'          => $data['exam_date'] ?? current_time( 'Y-m-d' ),
			'internal_exam'      => $data['internal_exam'] ?? null,
			'internal_comment'   => $data['internal_comment'] ?? null,
			'chest_exam'         => $data['chest_exam'] ?? null,
			'chest_comment'      => $data['chest_comment'] ?? null,
			'eye_exam'           => $data['eye_exam'] ?? null,
			'eye_comment'        => $data['eye_comment'] ?? null,
			'toxicology_exam'    => $data['toxicology_exam'] ?? null,
			'toxicology_comment' => $data['toxicology_comment'] ?? null,
			'virology_exam'      => $data['virology_exam'] ?? null,
			'virology_comment'   => $data['virology_comment'] ?? null,
			'blood_exam'         => $data['blood_exam'] ?? null,
			'blood_comment'      => $data['blood_comment'] ?? null,
			'overall_result'     => $overall,
			'recorded_by'        => $user_id,
			'created_at'         => current_time( 'mysql' ),
		);

		if ( $existing ) {
			$wpdb->update( self::table(), $row, array( 'id' => (int) $existing['id'] ) );
			$id = (int) $existing['id'];
		} else {
			$wpdb->insert( self::table(), $row );
			$id = (int) $wpdb->insert_id;
		}

		if ( $overall === RSYI_Constants::MEDICAL_FIT ) {
			RSYI_Student::change_status( $student_id, RSYI_Constants::STATUS_MEDICAL_PASSED, $user_id );
		} elseif ( $overall === RSYI_Constants::MEDICAL_UNFIT ) {
			RSYI_Student::change_status( $student_id, RSYI_Constants::STATUS_MEDICAL_FAILED, $user_id );
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

	private static function compute_overall( array $data ): ?string {
		$elements = RSYI_Constants::medical_elements();
		$any_null = false;
		$any_unfit = false;
		foreach ( $elements as $el ) {
			$val = $data[ $el . '_exam' ] ?? null;
			if ( $val === null || $val === '' ) {
				$any_null = true;
			} elseif ( $val === RSYI_Constants::MEDICAL_UNFIT ) {
				$any_unfit = true;
			}
		}

		if ( $any_unfit ) {
			return RSYI_Constants::MEDICAL_UNFIT;
		}
		if ( $any_null ) {
			return null;
		}
		return RSYI_Constants::MEDICAL_FIT;
	}
}
