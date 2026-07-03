<?php
/**
 * Document model.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Document {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_documents';
	}

	public static function required_types(): array {
		return array(
			RSYI_Constants::DOC_NATIONAL_ID_FRONT,
			RSYI_Constants::DOC_NATIONAL_ID_BACK,
			RSYI_Constants::DOC_BIRTH_CERTIFICATE,
			RSYI_Constants::DOC_HIGH_SCHOOL_CERTIFICATE,
			RSYI_Constants::DOC_MILITARY_STATUS_CERTIFICATE,
		);
	}

	public static function optional_types(): array {
		return array( RSYI_Constants::DOC_LAST_QUALIFICATION_CERTIFICATE );
	}

	public static function type_label( string $type ): string {
		$labels = array(
			RSYI_Constants::DOC_NATIONAL_ID_FRONT              => __( 'وجه بطاقة الرقم القومي', 'rsyi-student-affairs' ),
			RSYI_Constants::DOC_NATIONAL_ID_BACK               => __( 'ظهر بطاقة الرقم القومي', 'rsyi-student-affairs' ),
			RSYI_Constants::DOC_BIRTH_CERTIFICATE              => __( 'شهادة الميلاد', 'rsyi-student-affairs' ),
			RSYI_Constants::DOC_HIGH_SCHOOL_CERTIFICATE        => __( 'شهادة الثانوية العامة', 'rsyi-student-affairs' ),
			RSYI_Constants::DOC_LAST_QUALIFICATION_CERTIFICATE => __( 'شهادة آخر مؤهل (إن وجدت)', 'rsyi-student-affairs' ),
			RSYI_Constants::DOC_MILITARY_STATUS_CERTIFICATE    => __( 'شهادة موقف التجنيد', 'rsyi-student-affairs' ),
		);
		return $labels[ $type ] ?? $type;
	}

	public static function upsert( int $student_id, string $type, array $file_info ): int {
		global $wpdb;

		$existing = self::get_for_student( $student_id, $type );

		$row = array(
			'student_id'    => $student_id,
			'document_type' => $type,
			'file_path'     => $file_info['file_path'],
			'file_name'     => $file_info['file_name'],
			'file_size'     => (int) $file_info['file_size'],
			'mime_type'     => $file_info['mime_type'],
			'uploaded_at'   => current_time( 'mysql' ),
		);

		if ( $existing ) {
			if ( file_exists( $existing['file_path'] ) ) {
				@unlink( $existing['file_path'] );
			}
			$wpdb->update( self::table(), $row, array( 'id' => $existing['id'] ) );
			return (int) $existing['id'];
		}

		$wpdb->insert( self::table(), $row );
		return (int) $wpdb->insert_id;
	}

	public static function get_for_student( int $student_id, string $type ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . self::table() . " WHERE student_id = %d AND document_type = %s",
				$student_id,
				$type
			),
			ARRAY_A
		);
		return $row ?: null;
	}

	public static function all_for_student( int $student_id ): array {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE student_id = %d", $student_id ),
			ARRAY_A
		);
	}

	public static function has_all_required( int $student_id ): bool {
		$uploaded = wp_list_pluck( self::all_for_student( $student_id ), 'document_type' );
		foreach ( self::required_types() as $required ) {
			if ( ! in_array( $required, $uploaded, true ) ) {
				return false;
			}
		}
		return true;
	}
}
