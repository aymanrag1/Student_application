<?php
/**
 * Database installer - creates all custom tables using dbDelta.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Installer {

	public static function install(): void {
		global $wpdb;

		$installed_version = get_option( 'rsyi_db_version', '0' );
		if ( version_compare( $installed_version, RSYI_DB_VERSION, '>=' ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$prefix          = $wpdb->prefix . 'rsyi_';

		$queries = array(
			self::table_students( $prefix, $charset_collate ),
			self::table_documents( $prefix, $charset_collate ),
			self::table_status_log( $prefix, $charset_collate ),
			self::table_interviews( $prefix, $charset_collate ),
			self::table_committee_votes( $prefix, $charset_collate ),
			self::table_medical_exams( $prefix, $charset_collate ),
			self::table_language_tests( $prefix, $charset_collate ),
			self::table_notifications( $prefix, $charset_collate ),
		);

		foreach ( $queries as $sql ) {
			dbDelta( $sql );
		}

		update_option( 'rsyi_db_version', RSYI_DB_VERSION );
	}

	private static function table_students( string $prefix, string $charset ): string {
		$table = $prefix . 'students';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			application_code VARCHAR(20) NOT NULL,
			full_name VARCHAR(255) NOT NULL,
			national_id VARCHAR(20) NOT NULL,
			date_of_birth DATE NOT NULL,
			email VARCHAR(255) NOT NULL,
			address TEXT NOT NULL,
			mobile_1 VARCHAR(20) NOT NULL,
			mobile_2 VARCHAR(20) DEFAULT NULL,
			whatsapp VARCHAR(20) NOT NULL,
			job VARCHAR(255) NOT NULL,
			nationality VARCHAR(100) NOT NULL,
			reason_for_applying TEXT NOT NULL,
			marital_status VARCHAR(20) NOT NULL,
			gender VARCHAR(10) NOT NULL,
			height DECIMAL(5,2) DEFAULT NULL,
			weight DECIMAL(5,2) DEFAULT NULL,
			high_school_type VARCHAR(100) NOT NULL,
			high_school_track VARCHAR(20) NOT NULL,
			last_qualification VARCHAR(255) DEFAULT NULL,
			military_status VARCHAR(30) NOT NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'submitted_stage_1',
			rejection_reason_code VARCHAR(50) DEFAULT NULL,
			stage_one_submitted_at DATETIME NOT NULL,
			stage_two_submitted_at DATETIME DEFAULT NULL,
			reminder_stopped TINYINT(1) NOT NULL DEFAULT 0,
			last_reminder_sent_at DATETIME DEFAULT NULL,
			reminder_count INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY application_code (application_code),
			UNIQUE KEY national_id (national_id),
			KEY email (email),
			KEY status (status),
			KEY stage_one_submitted_at (stage_one_submitted_at)
		) {$charset};";
	}

	private static function table_documents( string $prefix, string $charset ): string {
		$table = $prefix . 'documents';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			document_type VARCHAR(50) NOT NULL,
			file_path VARCHAR(500) NOT NULL,
			file_name VARCHAR(255) NOT NULL,
			file_size INT NOT NULL,
			mime_type VARCHAR(100) NOT NULL,
			uploaded_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY student_id (student_id),
			KEY document_type (document_type),
			UNIQUE KEY student_doc_type (student_id, document_type)
		) {$charset};";
	}

	private static function table_status_log( string $prefix, string $charset ): string {
		$table = $prefix . 'status_log';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			from_status VARCHAR(50) DEFAULT NULL,
			to_status VARCHAR(50) NOT NULL,
			changed_by BIGINT UNSIGNED DEFAULT NULL,
			reason TEXT DEFAULT NULL,
			changed_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY student_id (student_id),
			KEY changed_at (changed_at)
		) {$charset};";
	}

	private static function table_interviews( string $prefix, string $charset ): string {
		$table = $prefix . 'interviews';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			interview_date DATETIME NOT NULL,
			location VARCHAR(255) NOT NULL DEFAULT 'El Gouna',
			attended TINYINT(1) DEFAULT NULL,
			committee_size TINYINT NOT NULL DEFAULT 4,
			total_acceptance_score DECIMAL(5,2) DEFAULT NULL,
			total_waiting_score DECIMAL(5,2) DEFAULT NULL,
			total_rejection_score DECIMAL(5,2) DEFAULT NULL,
			final_decision VARCHAR(20) DEFAULT NULL,
			notes TEXT DEFAULT NULL,
			scheduled_by BIGINT UNSIGNED NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY student_id (student_id),
			KEY interview_date (interview_date),
			KEY final_decision (final_decision)
		) {$charset};";
	}

	private static function table_committee_votes( string $prefix, string $charset ): string {
		$table = $prefix . 'committee_votes';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			interview_id BIGINT UNSIGNED NOT NULL,
			member_user_id BIGINT UNSIGNED NOT NULL,
			member_number TINYINT NOT NULL,
			decision VARCHAR(20) NOT NULL,
			weight_percentage DECIMAL(5,2) NOT NULL,
			comment TEXT DEFAULT NULL,
			voted_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY interview_member (interview_id, member_user_id),
			KEY interview_id (interview_id),
			KEY member_user_id (member_user_id)
		) {$charset};";
	}

	private static function table_medical_exams( string $prefix, string $charset ): string {
		$table = $prefix . 'medical_exams';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			exam_date DATE NOT NULL,
			internal_exam VARCHAR(10) DEFAULT NULL,
			internal_comment TEXT DEFAULT NULL,
			chest_exam VARCHAR(10) DEFAULT NULL,
			chest_comment TEXT DEFAULT NULL,
			eye_exam VARCHAR(10) DEFAULT NULL,
			eye_comment TEXT DEFAULT NULL,
			toxicology_exam VARCHAR(10) DEFAULT NULL,
			toxicology_comment TEXT DEFAULT NULL,
			virology_exam VARCHAR(10) DEFAULT NULL,
			virology_comment TEXT DEFAULT NULL,
			blood_exam VARCHAR(10) DEFAULT NULL,
			blood_comment TEXT DEFAULT NULL,
			overall_result VARCHAR(10) DEFAULT NULL,
			recorded_by BIGINT UNSIGNED NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY student_id (student_id),
			KEY overall_result (overall_result)
		) {$charset};";
	}

	private static function table_language_tests( string $prefix, string $charset ): string {
		$table = $prefix . 'language_tests';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			test_date DATE NOT NULL,
			level VARCHAR(30) NOT NULL,
			passed TINYINT(1) NOT NULL,
			notes TEXT DEFAULT NULL,
			recorded_by BIGINT UNSIGNED NOT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY student_id (student_id),
			KEY level (level),
			KEY passed (passed)
		) {$charset};";
	}

	private static function table_notifications( string $prefix, string $charset ): string {
		$table = $prefix . 'notifications';
		return "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			student_id BIGINT UNSIGNED NOT NULL,
			channel VARCHAR(20) NOT NULL,
			type VARCHAR(100) NOT NULL,
			recipient VARCHAR(255) NOT NULL,
			subject VARCHAR(255) DEFAULT NULL,
			body TEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			provider_response TEXT DEFAULT NULL,
			error_message TEXT DEFAULT NULL,
			sent_at DATETIME DEFAULT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY student_id (student_id),
			KEY channel (channel),
			KEY type (type),
			KEY status (status)
		) {$charset};";
	}

	public static function get_table_name( string $entity ): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_' . $entity;
	}
}
