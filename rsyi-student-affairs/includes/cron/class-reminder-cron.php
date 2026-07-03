<?php
/**
 * Reminder Cron - sends WhatsApp/Email reminders every 4 days to students
 * pending document upload.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Reminder_Cron {

	const HOOK = 'rsyi_send_document_reminders';

	public static function register(): void {
		add_action( self::HOOK, array( __CLASS__, 'run' ) );
	}

	public static function run(): void {
		global $wpdb;

		$table = RSYI_Student::table();
		$interval_days = RSYI_Constants::REMINDER_INTERVAL_DAYS;

		$candidates = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table}
				WHERE status = %s
				  AND reminder_stopped = 0
				  AND (
				    last_reminder_sent_at IS NULL
				    OR last_reminder_sent_at <= (NOW() - INTERVAL %d DAY)
				  )",
				RSYI_Constants::STATUS_PENDING_DOCUMENTS,
				$interval_days
			),
			ARRAY_A
		);

		if ( empty( $candidates ) ) {
			return;
		}

		foreach ( $candidates as $student ) {
			$student_id = (int) $student['id'];
			$msg     = RSYI_Notification_Service::render_message( RSYI_Constants::NOTIF_DOCUMENTS_PENDING_REMINDER, $student );
			$subject = __( 'تذكير برفع الأوراق', 'rsyi-student-affairs' );

			RSYI_Notification_Service::send_whatsapp(
				$student_id,
				RSYI_Constants::NOTIF_DOCUMENTS_PENDING_REMINDER,
				$student['whatsapp'],
				$msg
			);

			RSYI_Notification_Service::send_email(
				$student_id,
				RSYI_Constants::NOTIF_DOCUMENTS_PENDING_REMINDER,
				$student['email'],
				$subject,
				RSYI_Notification_Service::wrap_html( $msg )
			);

			RSYI_Student::update(
				$student_id,
				array(
					'last_reminder_sent_at' => current_time( 'mysql' ),
					'reminder_count'        => ( (int) $student['reminder_count'] ) + 1,
				)
			);
		}
	}
}
