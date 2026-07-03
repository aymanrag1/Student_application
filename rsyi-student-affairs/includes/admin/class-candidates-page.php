<?php
/**
 * Candidates listing admin page.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Candidates_Page {

	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'ليست لديك صلاحية عرض هذه الصفحة.', 'rsyi-student-affairs' ) );
		}

		$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
		$search        = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$paged         = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
		$per_page      = 25;

		$args = array(
			'status'   => $status_filter ?: null,
			'search'   => $search ?: null,
			'per_page' => $per_page,
			'page'     => $paged,
		);

		$students   = RSYI_Student::list( $args );
		$total      = RSYI_Student::count( $args );
		$total_pages = (int) ceil( $total / $per_page );

		include RSYI_PLUGIN_DIR . 'templates/admin/candidates-list.php';
	}

	public static function status_pipeline(): array {
		return array(
			''                                          => __( 'كل الحالات', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_SUBMITTED_STAGE_1    => __( 'قدم البيانات', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_REJECTED_STAGE_1     => __( 'مرفوض (فلترة)', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_PENDING_DOCUMENTS    => __( 'ينتظر الأوراق', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_DOCUMENTS_UPLOADED   => __( 'رفع الأوراق', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_INTERVIEW_SCHEDULED  => __( 'موعد مقابلة', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_INTERVIEW_ACCEPTED   => __( 'مقبول مقابلة', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_INTERVIEW_WAITING    => __( 'انتظار', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_INTERVIEW_REJECTED   => __( 'مرفوض مقابلة', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_MEDICAL_SCHEDULED    => __( 'موعد طبي', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_MEDICAL_PASSED       => __( 'اجتاز الطبي', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_MEDICAL_FAILED       => __( 'فشل الطبي', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_LANGUAGE_TESTED      => __( 'اختبار لغة', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_FINAL_ACCEPTED       => __( 'مقبول نهائي', 'rsyi-student-affairs' ),
			RSYI_Constants::STATUS_FINAL_REJECTED       => __( 'مرفوض نهائي', 'rsyi-student-affairs' ),
		);
	}
}
