<?php
/**
 * Stage 1 form submission handler.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Stage_One_Handler {

	const ACTION_NAME = 'rsyi_submit_stage_one';
	const NONCE_NAME  = 'rsyi_stage_one_nonce';

	public static function register(): void {
		add_action( 'admin_post_nopriv_' . self::ACTION_NAME, array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_' . self::ACTION_NAME, array( __CLASS__, 'handle' ) );
	}

	public static function handle(): void {
		check_admin_referer( self::ACTION_NAME, self::NONCE_NAME );

		$redirect_url = wp_get_referer() ?: home_url( '/' );

		$data = self::sanitize_input( $_POST );

		$validation_errors = self::validate( $data );
		if ( ! empty( $validation_errors ) ) {
			self::redirect_with_result( $redirect_url, 'error' );
		}

		if ( RSYI_Student::national_id_exists( $data['national_id'] ) ) {
			self::redirect_with_result( $redirect_url, 'duplicate' );
		}

		$eligibility = RSYI_Eligibility_Service::evaluate( $data );

		if ( $eligibility['eligible'] ) {
			$data['status'] = RSYI_Constants::STATUS_PENDING_DOCUMENTS;
		} else {
			$data['status']                = RSYI_Constants::STATUS_REJECTED_STAGE_1;
			$data['rejection_reason_code'] = implode( ',', $eligibility['rejection_codes'] );
		}

		$student_id = RSYI_Student::create( $data );
		if ( ! $student_id ) {
			self::redirect_with_result( $redirect_url, 'error' );
		}

		$student = RSYI_Student::get( $student_id );

		/**
		 * Fires after a stage 1 application is created (whether accepted or rejected).
		 *
		 * @param int   $student_id
		 * @param array $student
		 * @param array $eligibility
		 */
		do_action( 'rsyi_stage_one_submitted', $student_id, $student, $eligibility );

		if ( $eligibility['eligible'] ) {
			self::redirect_with_result(
				$redirect_url,
				'stage_one_accepted',
				array( 'rsyi_code' => $student['application_code'] )
			);
		}

		self::redirect_with_result(
			$redirect_url,
			'stage_one_rejected',
			array( 'rsyi_reasons' => implode( ',', $eligibility['rejection_codes'] ) )
		);
	}

	private static function sanitize_input( array $post ): array {
		return array(
			'full_name'           => sanitize_text_field( wp_unslash( $post['full_name'] ?? '' ) ),
			'national_id'         => preg_replace( '/[^0-9]/', '', $post['national_id'] ?? '' ),
			'date_of_birth'       => sanitize_text_field( wp_unslash( $post['date_of_birth'] ?? '' ) ),
			'email'               => sanitize_email( wp_unslash( $post['email'] ?? '' ) ),
			'address'             => sanitize_textarea_field( wp_unslash( $post['address'] ?? '' ) ),
			'mobile_1'            => sanitize_text_field( wp_unslash( $post['mobile_1'] ?? '' ) ),
			'mobile_2'            => sanitize_text_field( wp_unslash( $post['mobile_2'] ?? '' ) ),
			'whatsapp'            => sanitize_text_field( wp_unslash( $post['whatsapp'] ?? '' ) ),
			'job'                 => sanitize_text_field( wp_unslash( $post['job'] ?? '' ) ),
			'nationality'         => sanitize_text_field( wp_unslash( $post['nationality'] ?? '' ) ),
			'reason_for_applying' => sanitize_textarea_field( wp_unslash( $post['reason_for_applying'] ?? '' ) ),
			'marital_status'      => sanitize_text_field( wp_unslash( $post['marital_status'] ?? '' ) ),
			'gender'              => sanitize_text_field( wp_unslash( $post['gender'] ?? '' ) ),
			'height'              => is_numeric( $post['height'] ?? null ) ? (float) $post['height'] : null,
			'weight'              => is_numeric( $post['weight'] ?? null ) ? (float) $post['weight'] : null,
			'high_school_type'    => sanitize_text_field( wp_unslash( $post['high_school_type'] ?? '' ) ),
			'high_school_track'   => sanitize_text_field( wp_unslash( $post['high_school_track'] ?? '' ) ),
			'last_qualification'  => sanitize_text_field( wp_unslash( $post['last_qualification'] ?? '' ) ),
			'military_status'     => sanitize_text_field( wp_unslash( $post['military_status'] ?? '' ) ),
		);
	}

	private static function validate( array $data ): array {
		$errors = array();

		$required = array(
			'full_name', 'national_id', 'date_of_birth', 'email', 'address',
			'mobile_1', 'whatsapp', 'job', 'nationality', 'reason_for_applying',
			'marital_status', 'gender', 'high_school_type', 'military_status',
		);

		foreach ( $required as $field ) {
			if ( empty( $data[ $field ] ) ) {
				$errors[ $field ] = __( 'حقل مطلوب', 'rsyi-student-affairs' );
			}
		}

		if ( ! empty( $data['email'] ) && ! is_email( $data['email'] ) ) {
			$errors['email'] = __( 'بريد إلكتروني غير صحيح', 'rsyi-student-affairs' );
		}

		if ( ! empty( $data['national_id'] ) && ! RSYI_Eligibility_Service::validate_national_id( $data['national_id'] ) ) {
			$errors['national_id'] = __( 'رقم قومي غير صحيح', 'rsyi-student-affairs' );
		}

		return $errors;
	}

	private static function redirect_with_result( string $url, string $result, array $extra = array() ): void {
		$args = array_merge( array( 'rsyi_result' => $result ), $extra );
		wp_safe_redirect( add_query_arg( $args, $url ) );
		exit;
	}
}
