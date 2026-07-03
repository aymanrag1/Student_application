<?php
/**
 * Frontend shortcodes registration.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Shortcodes {

	public static function register(): void {
		add_shortcode( 'rsyi_application_form', array( __CLASS__, 'render_application_form' ) );
		add_shortcode( 'rsyi_application_status', array( __CLASS__, 'render_status_check' ) );
	}

	public static function render_application_form( $atts ): string {
		$atts = shortcode_atts(
			array( 'stage' => 'auto' ),
			$atts,
			'rsyi_application_form'
		);

		$stage      = $atts['stage'];
		$app_code   = isset( $_GET['rsyi_code'] ) ? sanitize_text_field( wp_unslash( $_GET['rsyi_code'] ) ) : '';
		$result_msg = isset( $_GET['rsyi_result'] ) ? sanitize_text_field( wp_unslash( $_GET['rsyi_result'] ) ) : '';

		if ( $result_msg ) {
			return self::render_result_message( $result_msg, $app_code );
		}

		if ( 'stage_two' === $stage || ( 'auto' === $stage && $app_code ) ) {
			$student = $app_code ? RSYI_Student::get_by_application_code( $app_code ) : null;
			if ( $student && $student['status'] === RSYI_Constants::STATUS_PENDING_DOCUMENTS ) {
				return self::render_template(
					'stage-two-form.php',
					array( 'student' => $student )
				);
			}
			if ( $student ) {
				return self::render_status_check_output( $student );
			}
		}

		return self::render_template( 'stage-one-form.php', array() );
	}

	public static function render_status_check( $atts ): string {
		$app_code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		if ( ! $app_code ) {
			return self::render_template( 'status-lookup.php', array() );
		}

		$student = RSYI_Student::get_by_application_code( $app_code );
		if ( ! $student ) {
			return '<div class="rsyi-notice rsyi-notice-error">' . esc_html__( 'كود التقديم غير موجود.', 'rsyi-student-affairs' ) . '</div>';
		}

		return self::render_status_check_output( $student );
	}

	private static function render_result_message( string $result, string $app_code ): string {
		switch ( $result ) {
			case 'stage_one_accepted':
				return '<div class="rsyi-notice rsyi-notice-success">'
					. '<h3>' . esc_html__( 'تم استلام طلبك بنجاح!', 'rsyi-student-affairs' ) . '</h3>'
					. '<p>' . esc_html__( 'كود التقديم بتاعك:', 'rsyi-student-affairs' ) . ' <strong>' . esc_html( $app_code ) . '</strong></p>'
					. '<p>' . esc_html__( 'استكمل الخطوة التالية برفع الأوراق المطلوبة.', 'rsyi-student-affairs' ) . '</p>'
					. '<p><a class="rsyi-btn rsyi-btn-primary" href="' . esc_url( add_query_arg( array( 'rsyi_code' => $app_code ) ) ) . '">' . esc_html__( 'رفع الأوراق الآن', 'rsyi-student-affairs' ) . '</a></p>'
					. '</div>';

			case 'stage_one_rejected':
				$codes  = isset( $_GET['rsyi_reasons'] ) ? sanitize_text_field( wp_unslash( $_GET['rsyi_reasons'] ) ) : '';
				$reasons = array_filter( array_map( array( 'RSYI_Constants', 'rejection_label' ), explode( ',', $codes ) ) );
				$html = '<div class="rsyi-notice rsyi-notice-error">'
					. '<h3>' . esc_html__( 'للأسف، طلبك لا يستوفي الشروط', 'rsyi-student-affairs' ) . '</h3>'
					. '<ul>';
				foreach ( $reasons as $r ) {
					$html .= '<li>' . esc_html( $r ) . '</li>';
				}
				$html .= '</ul>'
					. '<p>' . esc_html__( 'شكراً لاهتمامك بمعهد البحر الأحمر لليخوت.', 'rsyi-student-affairs' ) . '</p>'
					. '</div>';
				return $html;

			case 'stage_two_success':
				return '<div class="rsyi-notice rsyi-notice-success">'
					. '<h3>' . esc_html__( 'تم رفع الأوراق بنجاح!', 'rsyi-student-affairs' ) . '</h3>'
					. '<p>' . esc_html__( 'هيتم مراجعة الأوراق وتحديد موعد المقابلة، وهيوصلك إشعار على الواتساب والإيميل.', 'rsyi-student-affairs' ) . '</p>'
					. '</div>';

			case 'duplicate':
				return '<div class="rsyi-notice rsyi-notice-warning">'
					. '<p>' . esc_html__( 'الرقم القومي ده مسجّل قبل كده. لو نسيت كود التقديم، تواصل مع إدارة المعهد.', 'rsyi-student-affairs' ) . '</p>'
					. '</div>';

			case 'error':
				return '<div class="rsyi-notice rsyi-notice-error">'
					. '<p>' . esc_html__( 'حصل خطأ. حاول تاني أو تواصل مع الدعم.', 'rsyi-student-affairs' ) . '</p>'
					. '</div>';
		}

		return '';
	}

	private static function render_status_check_output( array $student ): string {
		$status_label = RSYI_Constants::status_label( $student['status'] );
		return '<div class="rsyi-status-card">'
			. '<h3>' . esc_html__( 'حالة طلبك', 'rsyi-student-affairs' ) . '</h3>'
			. '<p><strong>' . esc_html__( 'الاسم:', 'rsyi-student-affairs' ) . '</strong> ' . esc_html( $student['full_name'] ) . '</p>'
			. '<p><strong>' . esc_html__( 'كود التقديم:', 'rsyi-student-affairs' ) . '</strong> ' . esc_html( $student['application_code'] ) . '</p>'
			. '<p><strong>' . esc_html__( 'الحالة:', 'rsyi-student-affairs' ) . '</strong> ' . esc_html( $status_label ) . '</p>'
			. '</div>';
	}

	public static function render_template( string $template, array $vars = array() ): string {
		$path = RSYI_PLUGIN_DIR . 'templates/frontend/' . $template;
		if ( ! file_exists( $path ) ) {
			return '';
		}

		extract( $vars, EXTR_SKIP );
		ob_start();
		include $path;
		return ob_get_clean();
	}
}
