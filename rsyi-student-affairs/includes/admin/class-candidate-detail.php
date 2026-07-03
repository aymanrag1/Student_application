<?php
/**
 * Individual candidate detail page.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Candidate_Detail {

	public static function register(): void {
		add_action( 'admin_post_rsyi_toggle_reminder', array( __CLASS__, 'handle_toggle_reminder' ) );
		add_action( 'admin_post_rsyi_mark_docs_received', array( __CLASS__, 'handle_mark_docs_received' ) );
		add_action( 'admin_post_rsyi_change_status', array( __CLASS__, 'handle_change_status' ) );
	}

	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية عرض هذه الصفحة.', 'rsyi-student-affairs' ) );
		}

		$id      = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
		$student = $id ? RSYI_Student::get( $id ) : null;

		if ( ! $student ) {
			echo '<div class="wrap"><h1>' . esc_html__( 'الطالب غير موجود', 'rsyi-student-affairs' ) . '</h1></div>';
			return;
		}

		$documents = RSYI_Document::all_for_student( $id );
		$history   = RSYI_Status_Log::get_history( $id );
		$pipeline  = RSYI_Candidates_Page::status_pipeline();

		include RSYI_PLUGIN_DIR . 'templates/admin/candidate-detail.php';
	}

	public static function handle_toggle_reminder(): void {
		check_admin_referer( 'rsyi_toggle_reminder' );
		$id = (int) ( $_POST['student_id'] ?? 0 );
		$student = RSYI_Student::get( $id );
		if ( $student ) {
			RSYI_Student::update( $id, array( 'reminder_stopped' => (int) ! (bool) $student['reminder_stopped'] ) );
		}
		wp_safe_redirect( self::detail_url( $id ) );
		exit;
	}

	public static function handle_mark_docs_received(): void {
		check_admin_referer( 'rsyi_mark_docs_received' );
		$id = (int) ( $_POST['student_id'] ?? 0 );
		$student = RSYI_Student::get( $id );
		if ( $student && $student['status'] === RSYI_Constants::STATUS_PENDING_DOCUMENTS ) {
			RSYI_Student::change_status(
				$id,
				RSYI_Constants::STATUS_DOCUMENTS_UPLOADED,
				get_current_user_id(),
				__( 'تسجيل يدوي: تم استلام الأوراق', 'rsyi-student-affairs' )
			);
			RSYI_Student::update(
				$id,
				array(
					'stage_two_submitted_at' => current_time( 'mysql' ),
					'reminder_stopped'       => 1,
				)
			);
		}
		wp_safe_redirect( self::detail_url( $id ) );
		exit;
	}

	public static function handle_change_status(): void {
		check_admin_referer( 'rsyi_change_status' );
		$id = (int) ( $_POST['student_id'] ?? 0 );
		$new_status = sanitize_text_field( wp_unslash( $_POST['new_status'] ?? '' ) );
		$reason = sanitize_textarea_field( wp_unslash( $_POST['reason'] ?? '' ) );

		if ( $id && $new_status && array_key_exists( $new_status, RSYI_Candidates_Page::status_pipeline() ) ) {
			RSYI_Student::change_status( $id, $new_status, get_current_user_id(), $reason );
		}

		wp_safe_redirect( self::detail_url( $id ) );
		exit;
	}

	public static function detail_url( int $id ): string {
		return admin_url( 'admin.php?page=' . RSYI_Admin::CANDIDATE_PAGE_SLUG . '&id=' . $id );
	}
}
