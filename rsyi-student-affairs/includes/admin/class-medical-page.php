<?php
/**
 * Medical exam & Language test admin page.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Medical_Page {

	public static function register(): void {
		add_action( 'admin_post_rsyi_save_medical', array( __CLASS__, 'handle_medical' ) );
		add_action( 'admin_post_rsyi_save_language', array( __CLASS__, 'handle_language' ) );
	}

	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية.', 'rsyi-student-affairs' ) );
		}

		global $wpdb;
		$candidates = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . RSYI_Student::table() . "
				WHERE status IN (%s, %s, %s, %s, %s)
				ORDER BY id DESC",
				RSYI_Constants::STATUS_INTERVIEW_ACCEPTED,
				RSYI_Constants::STATUS_MEDICAL_SCHEDULED,
				RSYI_Constants::STATUS_MEDICAL_PASSED,
				RSYI_Constants::STATUS_MEDICAL_FAILED,
				RSYI_Constants::STATUS_LANGUAGE_TESTED
			),
			ARRAY_A
		);

		$selected_id = (int) ( $_GET['student'] ?? 0 );
		$student     = $selected_id ? RSYI_Student::get( $selected_id ) : null;
		$medical     = $student ? RSYI_Medical_Exam::get_for_student( $selected_id ) : null;
		$language    = $student ? RSYI_Language_Test::get_for_student( $selected_id ) : null;

		include RSYI_PLUGIN_DIR . 'templates/admin/medical-page.php';
	}

	public static function handle_medical(): void {
		check_admin_referer( 'rsyi_save_medical' );

		$student_id = (int) ( $_POST['student_id'] ?? 0 );
		if ( ! $student_id ) {
			wp_die( 'Missing student ID' );
		}

		$data = array(
			'exam_date'          => sanitize_text_field( wp_unslash( $_POST['exam_date'] ?? '' ) ),
			'internal_exam'      => sanitize_text_field( wp_unslash( $_POST['internal_exam'] ?? '' ) ),
			'internal_comment'   => sanitize_textarea_field( wp_unslash( $_POST['internal_comment'] ?? '' ) ),
			'chest_exam'         => sanitize_text_field( wp_unslash( $_POST['chest_exam'] ?? '' ) ),
			'chest_comment'      => sanitize_textarea_field( wp_unslash( $_POST['chest_comment'] ?? '' ) ),
			'eye_exam'           => sanitize_text_field( wp_unslash( $_POST['eye_exam'] ?? '' ) ),
			'eye_comment'        => sanitize_textarea_field( wp_unslash( $_POST['eye_comment'] ?? '' ) ),
			'toxicology_exam'    => sanitize_text_field( wp_unslash( $_POST['toxicology_exam'] ?? '' ) ),
			'toxicology_comment' => sanitize_textarea_field( wp_unslash( $_POST['toxicology_comment'] ?? '' ) ),
			'virology_exam'      => sanitize_text_field( wp_unslash( $_POST['virology_exam'] ?? '' ) ),
			'virology_comment'   => sanitize_textarea_field( wp_unslash( $_POST['virology_comment'] ?? '' ) ),
			'blood_exam'         => sanitize_text_field( wp_unslash( $_POST['blood_exam'] ?? '' ) ),
			'blood_comment'      => sanitize_textarea_field( wp_unslash( $_POST['blood_comment'] ?? '' ) ),
		);

		RSYI_Medical_Exam::upsert( $student_id, $data, get_current_user_id() );

		wp_safe_redirect( add_query_arg( array( 'student' => $student_id ), admin_url( 'admin.php?page=rsyi-medical' ) ) );
		exit;
	}

	public static function handle_language(): void {
		check_admin_referer( 'rsyi_save_language' );

		$student_id = (int) ( $_POST['student_id'] ?? 0 );
		if ( ! $student_id ) {
			wp_die( 'Missing student ID' );
		}

		$data = array(
			'test_date' => sanitize_text_field( wp_unslash( $_POST['test_date'] ?? '' ) ),
			'level'     => sanitize_text_field( wp_unslash( $_POST['level'] ?? '' ) ),
			'notes'     => sanitize_textarea_field( wp_unslash( $_POST['notes'] ?? '' ) ),
		);

		RSYI_Language_Test::upsert( $student_id, $data, get_current_user_id() );

		wp_safe_redirect( add_query_arg( array( 'student' => $student_id ), admin_url( 'admin.php?page=rsyi-medical' ) ) );
		exit;
	}
}
