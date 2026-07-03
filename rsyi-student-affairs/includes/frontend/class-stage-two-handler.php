<?php
/**
 * Stage 2 (documents upload) submission handler.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Stage_Two_Handler {

	const ACTION_NAME = 'rsyi_submit_stage_two';
	const NONCE_NAME  = 'rsyi_stage_two_nonce';

	public static function register(): void {
		add_action( 'admin_post_nopriv_' . self::ACTION_NAME, array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_' . self::ACTION_NAME, array( __CLASS__, 'handle' ) );
	}

	public static function handle(): void {
		check_admin_referer( self::ACTION_NAME, self::NONCE_NAME );

		$redirect_url = wp_get_referer() ?: home_url( '/' );

		$app_code   = sanitize_text_field( wp_unslash( $_POST['application_code'] ?? '' ) );
		$student    = $app_code ? RSYI_Student::get_by_application_code( $app_code ) : null;

		if ( ! $student ) {
			self::redirect( $redirect_url, 'error' );
		}

		if ( $student['status'] !== RSYI_Constants::STATUS_PENDING_DOCUMENTS
			&& $student['status'] !== RSYI_Constants::STATUS_DOCUMENTS_UPLOADED ) {
			self::redirect( $redirect_url, 'error' );
		}

		$student_id = (int) $student['id'];
		$doc_types  = array_merge( RSYI_Document::required_types(), RSYI_Document::optional_types() );
		$errors     = array();
		$uploaded   = 0;

		foreach ( $doc_types as $doc_type ) {
			if ( empty( $_FILES[ $doc_type ]['name'] ) ) {
				continue;
			}

			$result = RSYI_Upload_Service::handle_upload( $_FILES[ $doc_type ], $student_id, $doc_type );
			if ( is_wp_error( $result ) ) {
				$errors[ $doc_type ] = $result->get_error_message();
				continue;
			}

			RSYI_Document::upsert( $student_id, $doc_type, $result );
			$uploaded++;
		}

		if ( ! empty( $errors ) ) {
			self::redirect(
				$redirect_url,
				'error',
				array( 'rsyi_code' => $student['application_code'] )
			);
		}

		if ( RSYI_Document::has_all_required( $student_id ) ) {
			RSYI_Student::change_status(
				$student_id,
				RSYI_Constants::STATUS_DOCUMENTS_UPLOADED,
				null,
				__( 'اكتمل رفع كل الأوراق المطلوبة', 'rsyi-student-affairs' )
			);

			RSYI_Student::update(
				$student_id,
				array( 'stage_two_submitted_at' => current_time( 'mysql' ) )
			);

			do_action( 'rsyi_documents_completed', $student_id );

			self::redirect( $redirect_url, 'stage_two_success' );
		}

		self::redirect(
			$redirect_url,
			'partial',
			array( 'rsyi_code' => $student['application_code'] )
		);
	}

	private static function redirect( string $url, string $result, array $extra = array() ): void {
		$args = array_merge( array( 'rsyi_result' => $result ), $extra );
		wp_safe_redirect( add_query_arg( $args, $url ) );
		exit;
	}
}
