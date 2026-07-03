<?php
/**
 * Notification Service - dispatches WhatsApp + Email notifications.
 *
 * Templates loaded from templates/emails/ (HTML) and inline text via method map.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Notification_Service {

	public static function bootstrap(): void {
		add_action( 'rsyi_stage_one_submitted', array( __CLASS__, 'on_stage_one_submitted' ), 10, 3 );
		add_action( 'rsyi_documents_completed', array( __CLASS__, 'on_documents_completed' ) );
		add_action( 'rsyi_student_status_changed', array( __CLASS__, 'on_status_changed' ), 10, 4 );
	}

	public static function get_driver(): RSYI_WhatsApp_Driver_Interface {
		$name = get_option( 'rsyi_whatsapp_driver', 'log' );

		switch ( $name ) {
			case 'ultramsg':
				return new RSYI_UltraMsg_Driver();
			case 'meta_cloud':
				return new RSYI_Meta_Cloud_Driver();
			case 'log':
			default:
				return new RSYI_Log_Driver();
		}
	}

	public static function send_whatsapp( int $student_id, string $type, string $to, string $message ): bool {
		$notification_id = RSYI_Notification::log(
			array(
				'student_id' => $student_id,
				'channel'    => RSYI_Constants::CHANNEL_WHATSAPP,
				'type'       => $type,
				'recipient'  => $to,
				'body'       => $message,
			)
		);

		$driver = self::get_driver();
		$result = $driver->send( self::normalize_phone( $to ), $message );

		if ( $result['success'] ) {
			RSYI_Notification::mark_sent( $notification_id, $result['response'] );
			return true;
		}

		RSYI_Notification::mark_failed( $notification_id, $result['error'], $result['response'] );
		return false;
	}

	public static function send_email( int $student_id, string $type, string $to, string $subject, string $body_html ): bool {
		$notification_id = RSYI_Notification::log(
			array(
				'student_id' => $student_id,
				'channel'    => RSYI_Constants::CHANNEL_EMAIL,
				'type'       => $type,
				'recipient'  => $to,
				'subject'    => $subject,
				'body'       => $body_html,
			)
		);

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>',
		);

		$sent = wp_mail( $to, $subject, $body_html, $headers );

		if ( $sent ) {
			RSYI_Notification::mark_sent( $notification_id );
			return true;
		}

		RSYI_Notification::mark_failed( $notification_id, 'wp_mail returned false' );
		return false;
	}

	// -----------------------------------------------------------
	// Event handlers
	// -----------------------------------------------------------

	public static function on_stage_one_submitted( int $student_id, array $student, array $eligibility ): void {
		if ( $eligibility['eligible'] ) {
			$msg     = self::render_message( RSYI_Constants::NOTIF_APPLICATION_RECEIVED, $student );
			$subject = __( 'تم استلام طلبك - معهد البحر الأحمر لليخوت', 'rsyi-student-affairs' );
			self::send_whatsapp( $student_id, RSYI_Constants::NOTIF_APPLICATION_RECEIVED, $student['whatsapp'], $msg );
			self::send_email( $student_id, RSYI_Constants::NOTIF_APPLICATION_RECEIVED, $student['email'], $subject, self::wrap_html( $msg ) );
			return;
		}

		$reasons  = implode( "\n- ", array_map( array( 'RSYI_Constants', 'rejection_label' ), $eligibility['rejection_codes'] ) );
		$student['rejection_reasons_text'] = $reasons;
		$msg      = self::render_message( RSYI_Constants::NOTIF_STAGE_ONE_REJECTED, $student );
		$subject  = __( 'نتيجة طلبك - معهد البحر الأحمر لليخوت', 'rsyi-student-affairs' );
		self::send_whatsapp( $student_id, RSYI_Constants::NOTIF_STAGE_ONE_REJECTED, $student['whatsapp'], $msg );
		self::send_email( $student_id, RSYI_Constants::NOTIF_STAGE_ONE_REJECTED, $student['email'], $subject, self::wrap_html( $msg ) );
	}

	public static function on_documents_completed( int $student_id ): void {
		$student = RSYI_Student::get( $student_id );
		if ( ! $student ) return;

		$msg     = self::render_message( RSYI_Constants::NOTIF_DOCUMENTS_RECEIVED, $student );
		$subject = __( 'تم استلام أوراقك - جاري تحديد موعد المقابلة', 'rsyi-student-affairs' );
		self::send_whatsapp( $student_id, RSYI_Constants::NOTIF_DOCUMENTS_RECEIVED, $student['whatsapp'], $msg );
		self::send_email( $student_id, RSYI_Constants::NOTIF_DOCUMENTS_RECEIVED, $student['email'], $subject, self::wrap_html( $msg ) );
	}

	public static function on_status_changed( int $student_id, ?string $from, string $to, ?int $user_id ): void {
		$student = RSYI_Student::get( $student_id );
		if ( ! $student ) return;

		$type    = null;
		$subject = '';

		switch ( $to ) {
			case RSYI_Constants::STATUS_INTERVIEW_SCHEDULED:
				$type    = RSYI_Constants::NOTIF_INTERVIEW_SCHEDULED;
				$subject = __( 'موعد المقابلة الشخصية', 'rsyi-student-affairs' );
				break;
			case RSYI_Constants::STATUS_MEDICAL_SCHEDULED:
				$type    = RSYI_Constants::NOTIF_MEDICAL_SCHEDULED;
				$subject = __( 'موعد الفحص الطبي', 'rsyi-student-affairs' );
				break;
			case RSYI_Constants::STATUS_FINAL_ACCEPTED:
				$type    = RSYI_Constants::NOTIF_FINAL_ACCEPTED;
				$subject = __( 'مبروك! تم قبولك نهائياً', 'rsyi-student-affairs' );
				break;
			case RSYI_Constants::STATUS_FINAL_REJECTED:
			case RSYI_Constants::STATUS_INTERVIEW_REJECTED:
			case RSYI_Constants::STATUS_MEDICAL_FAILED:
				$type    = RSYI_Constants::NOTIF_FINAL_REJECTED;
				$subject = __( 'نتيجة تقديمك', 'rsyi-student-affairs' );
				break;
		}

		if ( ! $type ) return;

		$msg = self::render_message( $type, $student );
		self::send_whatsapp( $student_id, $type, $student['whatsapp'], $msg );
		self::send_email( $student_id, $type, $student['email'], $subject, self::wrap_html( $msg ) );
	}

	// -----------------------------------------------------------
	// Message templates
	// -----------------------------------------------------------

	public static function render_message( string $type, array $student ): string {
		$name = $student['full_name'];
		$code = $student['application_code'];

		switch ( $type ) {
			case RSYI_Constants::NOTIF_APPLICATION_RECEIVED:
				return sprintf(
					__( "أهلاً %s،\n\nتم استلام طلبك على منحة معهد البحر الأحمر لليخوت.\nكود التقديم: %s\n\nالخطوة التالية: ارفع الأوراق المطلوبة على الموقع.", 'rsyi-student-affairs' ),
					$name,
					$code
				);

			case RSYI_Constants::NOTIF_STAGE_ONE_REJECTED:
				return sprintf(
					__( "أهلاً %s،\n\nللأسف، طلبك لا يستوفي شروط المنحة للأسباب التالية:\n- %s\n\nنشكرك على اهتمامك بمعهد البحر الأحمر لليخوت.", 'rsyi-student-affairs' ),
					$name,
					$student['rejection_reasons_text'] ?? ''
				);

			case RSYI_Constants::NOTIF_DOCUMENTS_PENDING_REMINDER:
				return sprintf(
					__( "تذكير: %s،\n\nما زلنا في انتظار رفعك للأوراق المطلوبة لاستكمال طلبك (كود: %s).\nمن فضلك ارفعها في أقرب فرصة.", 'rsyi-student-affairs' ),
					$name,
					$code
				);

			case RSYI_Constants::NOTIF_DOCUMENTS_RECEIVED:
				return sprintf(
					__( "أهلاً %s،\n\nتم استلام كل أوراقك بنجاح. هيتم مراجعتها وتحديد موعد المقابلة قريباً.\nكود التقديم: %s", 'rsyi-student-affairs' ),
					$name,
					$code
				);

			case RSYI_Constants::NOTIF_INTERVIEW_SCHEDULED:
				return sprintf(
					__( "أهلاً %s،\n\nتم تحديد موعد المقابلة الشخصية بتاعك. تفاصيل الموعد هترجع تانية من إدارة المعهد.\nكود التقديم: %s", 'rsyi-student-affairs' ),
					$name,
					$code
				);

			case RSYI_Constants::NOTIF_MEDICAL_SCHEDULED:
				return sprintf(
					__( "أهلاً %s،\n\nمبروك اجتيازك المقابلة! تم تحديد موعد الفحص الطبي واختبار تحديد المستوى.\nالتفاصيل هترجع من الإدارة.", 'rsyi-student-affairs' ),
					$name
				);

			case RSYI_Constants::NOTIF_FINAL_ACCEPTED:
				return sprintf(
					__( "مبروك %s! 🎉\n\nتم قبولك نهائياً في منحة معهد البحر الأحمر لليخوت.\nكود التقديم: %s\n\nهنتواصل معاك قريباً بخطوات الالتحاق.", 'rsyi-student-affairs' ),
					$name,
					$code
				);

			case RSYI_Constants::NOTIF_FINAL_REJECTED:
				return sprintf(
					__( "أهلاً %s،\n\nنشكرك على تقديمك على منحة المعهد. للأسف مش هنقدر نستكمل معاك المشوار في المرحلة دي.\nنتمنى لك التوفيق.", 'rsyi-student-affairs' ),
					$name
				);
		}

		return '';
	}

	public static function wrap_html( string $text ): string {
		$safe = nl2br( esc_html( $text ) );
		return '<div style="font-family:Cairo,Segoe UI,Tahoma,sans-serif;direction:rtl;text-align:right;padding:20px;background:#f8fafc;color:#1a202c;">'
			. '<div style="max-width:600px;margin:0 auto;background:#fff;padding:24px;border-radius:8px;">'
			. '<h2 style="color:#1e40af;">' . esc_html( get_bloginfo( 'name' ) ) . '</h2>'
			. '<div style="line-height:1.7;">' . $safe . '</div>'
			. '</div></div>';
	}

	public static function normalize_phone( string $phone ): string {
		$digits = preg_replace( '/[^0-9]/', '', $phone );
		if ( strpos( $digits, '0' ) === 0 ) {
			$digits = '20' . substr( $digits, 1 );
		} elseif ( strpos( $digits, '20' ) !== 0 && strlen( $digits ) === 10 ) {
			$digits = '20' . $digits;
		}
		return $digits;
	}
}
