<?php
/**
 * Export Service - CSV exports (works without PhpSpreadsheet dependency).
 *
 * If PhpSpreadsheet is available (composer install), users can extend to XLSX.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Export_Service {

	public static function register(): void {
		add_action( 'admin_post_rsyi_export_candidates', array( __CLASS__, 'export_candidates_csv' ) );
		add_action( 'admin_post_rsyi_export_security_list', array( __CLASS__, 'export_security_list_csv' ) );
	}

	public static function export_candidates_csv(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية.', 'rsyi-student-affairs' ) );
		}
		check_admin_referer( 'rsyi_export_candidates' );

		global $wpdb;
		$status = sanitize_text_field( wp_unslash( $_GET['status'] ?? '' ) );

		$where  = '1=1';
		$params = array();
		if ( $status ) {
			$where  .= ' AND status = %s';
			$params[] = $status;
		}

		$sql = "SELECT * FROM " . RSYI_Student::table() . " WHERE {$where} ORDER BY id ASC";
		if ( $params ) {
			$sql = $wpdb->prepare( $sql, $params );
		}
		$students = $wpdb->get_results( $sql, ARRAY_A );

		$filename = 'rsyi-candidates-' . gmdate( 'Ymd-His' ) . '.csv';
		self::send_csv_headers( $filename );

		$out = fopen( 'php://output', 'w' );
		fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM for Excel

		fputcsv( $out, array(
			'ID', 'كود التقديم', 'الاسم', 'الرقم القومي', 'تاريخ الميلاد',
			'الإيميل', 'الموبايل', 'واتساب', 'الجنسية', 'نوع الثانوية',
			'الشعبة', 'موقف التجنيد', 'الحالة', 'تاريخ تقديم المرحلة 1', 'تاريخ رفع الأوراق',
		) );

		foreach ( $students as $s ) {
			fputcsv( $out, array(
				$s['id'],
				$s['application_code'],
				$s['full_name'],
				$s['national_id'],
				$s['date_of_birth'],
				$s['email'],
				$s['mobile_1'],
				$s['whatsapp'],
				$s['nationality'],
				$s['high_school_type'],
				$s['high_school_track'],
				$s['military_status'],
				RSYI_Constants::status_label( $s['status'] ),
				$s['stage_one_submitted_at'],
				$s['stage_two_submitted_at'],
			) );
		}

		fclose( $out );
		exit;
	}

	public static function export_security_list_csv(): void {
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( RSYI_Roles::CAP_SECURITY ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية.', 'rsyi-student-affairs' ) );
		}
		check_admin_referer( 'rsyi_export_security_list' );

		$date = sanitize_text_field( wp_unslash( $_GET['date'] ?? '' ) );
		if ( ! $date ) {
			wp_die( 'Missing date' );
		}

		$interviews = RSYI_Interview::on_date( $date );

		$filename = 'security-list-' . $date . '.csv';
		self::send_csv_headers( $filename );

		$out = fopen( 'php://output', 'w' );
		fputs( $out, "\xEF\xBB\xBF" );

		fputcsv( $out, array( 'الاسم', 'الرقم القومي', 'رقم الموبايل', 'كود التقديم', 'موعد المقابلة' ) );

		foreach ( $interviews as $i ) {
			fputcsv( $out, array(
				$i['full_name'],
				$i['national_id'],
				$i['mobile_1'],
				$i['application_code'],
				mysql2date( 'H:i', $i['interview_date'] ),
			) );
		}

		fclose( $out );
		exit;
	}

	private static function send_csv_headers( string $filename ): void {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	}
}
