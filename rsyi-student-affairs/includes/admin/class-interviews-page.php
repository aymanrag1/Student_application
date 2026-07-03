<?php
/**
 * Interviews admin page - list + schedule.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Interviews_Page {

	public static function register(): void {
		add_action( 'admin_post_rsyi_schedule_interview', array( __CLASS__, 'handle_schedule' ) );
		add_action( 'admin_post_rsyi_mark_attendance', array( __CLASS__, 'handle_attendance' ) );
		add_action( 'admin_post_rsyi_cast_vote', array( __CLASS__, 'handle_vote' ) );
		add_action( 'admin_post_rsyi_finalize_interview', array( __CLASS__, 'handle_finalize' ) );
	}

	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية.', 'rsyi-student-affairs' ) );
		}

		$view      = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : 'list';
		$upcoming  = RSYI_Interview::upcoming();

		global $wpdb;
		$eligible = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . RSYI_Student::table() . " WHERE status = %s ORDER BY stage_two_submitted_at ASC",
				RSYI_Constants::STATUS_DOCUMENTS_UPLOADED
			),
			ARRAY_A
		);

		include RSYI_PLUGIN_DIR . 'templates/admin/interviews-list.php';
	}

	public static function handle_schedule(): void {
		check_admin_referer( 'rsyi_schedule_interview' );

		$student_id     = (int) ( $_POST['student_id'] ?? 0 );
		$date           = sanitize_text_field( wp_unslash( $_POST['interview_date'] ?? '' ) );
		$time           = sanitize_text_field( wp_unslash( $_POST['interview_time'] ?? '' ) );
		$committee_size = (int) ( $_POST['committee_size'] ?? 4 );
		$location       = sanitize_text_field( wp_unslash( $_POST['location'] ?? 'El Gouna' ) );

		if ( $student_id && $date && $time ) {
			$datetime = $date . ' ' . $time . ':00';
			RSYI_Interview::schedule( $student_id, $datetime, $committee_size, get_current_user_id(), $location );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . RSYI_Admin::INTERVIEWS_SLUG ) );
		exit;
	}

	public static function handle_attendance(): void {
		check_admin_referer( 'rsyi_mark_attendance' );
		$id       = (int) ( $_POST['interview_id'] ?? 0 );
		$attended = (int) ( $_POST['attended'] ?? 0 );

		if ( $id ) {
			RSYI_Interview::mark_attended( $id, $attended );
			if ( $attended === 1 ) {
				$interview = RSYI_Interview::get( $id );
				if ( $interview ) {
					RSYI_Student::change_status(
						(int) $interview['student_id'],
						RSYI_Constants::STATUS_INTERVIEW_ATTENDED,
						get_current_user_id()
					);
				}
			}
		}

		wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=' . RSYI_Admin::INTERVIEWS_SLUG ) );
		exit;
	}

	public static function handle_vote(): void {
		check_admin_referer( 'rsyi_cast_vote' );

		$interview_id  = (int) ( $_POST['interview_id'] ?? 0 );
		$member_number = (int) ( $_POST['member_number'] ?? 0 );
		$decision      = sanitize_text_field( wp_unslash( $_POST['decision'] ?? '' ) );
		$comment       = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );

		$interview = RSYI_Interview::get( $interview_id );
		if ( ! $interview ) {
			wp_die( esc_html__( 'المقابلة غير موجودة.', 'rsyi-student-affairs' ) );
		}

		if ( ! in_array( $decision, array( 'accepted', 'waiting', 'rejected' ), true ) ) {
			wp_die( esc_html__( 'قرار غير صالح.', 'rsyi-student-affairs' ) );
		}

		RSYI_Committee_Vote::cast(
			$interview_id,
			get_current_user_id(),
			$member_number,
			$decision,
			(int) $interview['committee_size'],
			$comment
		);

		wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=' . RSYI_Admin::INTERVIEWS_SLUG ) );
		exit;
	}

	public static function handle_finalize(): void {
		check_admin_referer( 'rsyi_finalize_interview' );
		$id = (int) ( $_POST['interview_id'] ?? 0 );

		if ( $id ) {
			RSYI_Committee_Service::finalize( $id, get_current_user_id() );
		}

		wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=' . RSYI_Admin::INTERVIEWS_SLUG ) );
		exit;
	}
}
