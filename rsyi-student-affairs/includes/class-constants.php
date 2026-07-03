<?php
/**
 * Global constants and enum values used across the plugin.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Constants {

	// -----------------------------------------------------------
	// Student status lifecycle (State Machine)
	// -----------------------------------------------------------
	const STATUS_SUBMITTED_STAGE_1     = 'submitted_stage_1';
	const STATUS_REJECTED_STAGE_1      = 'rejected_stage_1';
	const STATUS_PENDING_DOCUMENTS     = 'pending_documents';
	const STATUS_DOCUMENTS_UPLOADED    = 'documents_uploaded';
	const STATUS_INTERVIEW_SCHEDULED   = 'interview_scheduled';
	const STATUS_INTERVIEW_ATTENDED    = 'interview_attended';
	const STATUS_INTERVIEW_ACCEPTED    = 'interview_accepted';
	const STATUS_INTERVIEW_WAITING     = 'interview_waiting';
	const STATUS_INTERVIEW_REJECTED    = 'interview_rejected';
	const STATUS_MEDICAL_SCHEDULED     = 'medical_scheduled';
	const STATUS_MEDICAL_PASSED        = 'medical_passed';
	const STATUS_MEDICAL_FAILED        = 'medical_failed';
	const STATUS_LANGUAGE_TESTED       = 'language_tested';
	const STATUS_FINAL_ACCEPTED        = 'final_accepted';
	const STATUS_FINAL_REJECTED        = 'final_rejected';

	// -----------------------------------------------------------
	// Rejection reason codes (Stage 1 auto-filter)
	// -----------------------------------------------------------
	const REJECT_NATIONALITY        = 'RJ_NATIONALITY';
	const REJECT_AGE_LOW            = 'RJ_AGE_LOW';
	const REJECT_AGE_HIGH           = 'RJ_AGE_HIGH';
	const REJECT_GENDER             = 'RJ_GENDER';
	const REJECT_HIGH_SCHOOL_TYPE   = 'RJ_HIGH_SCHOOL_TYPE';
	const REJECT_HIGH_SCHOOL_TRACK  = 'RJ_HIGH_SCHOOL_TRACK';
	const REJECT_MILITARY_STATUS    = 'RJ_MILITARY_STATUS';

	// -----------------------------------------------------------
	// Document types
	// -----------------------------------------------------------
	const DOC_NATIONAL_ID_FRONT              = 'national_id_front';
	const DOC_NATIONAL_ID_BACK               = 'national_id_back';
	const DOC_BIRTH_CERTIFICATE              = 'birth_certificate';
	const DOC_HIGH_SCHOOL_CERTIFICATE        = 'high_school_certificate';
	const DOC_LAST_QUALIFICATION_CERTIFICATE = 'last_qualification_certificate';
	const DOC_MILITARY_STATUS_CERTIFICATE    = 'military_status_certificate';

	// -----------------------------------------------------------
	// High school types
	// -----------------------------------------------------------
	const HS_GENERAL_EGYPTIAN   = 'general_egyptian';   // ثانوية عامة
	const HS_AZHARI             = 'azhari';             // ثانوية أزهرية
	const HS_AMERICAN_DIPLOMA   = 'american_diploma';
	const HS_BRITISH_IGCSE      = 'british_igcse';
	const HS_IB                 = 'ib';
	const HS_FRENCH             = 'french';
	const HS_GERMAN             = 'german';
	const HS_COMMERCIAL         = 'commercial';         // تجارية (مرفوضة)
	const HS_INDUSTRIAL         = 'industrial';         // صناعية (مرفوضة)
	const HS_AGRICULTURAL       = 'agricultural';       // زراعية (مرفوضة)
	const HS_HOTEL              = 'hotel';              // فنادق (مرفوضة)
	const HS_OTHER              = 'other';

	// High school types accepted by scholarship
	public static function accepted_high_school_types(): array {
		return array(
			self::HS_GENERAL_EGYPTIAN,
			self::HS_AZHARI,
			self::HS_AMERICAN_DIPLOMA,
			self::HS_BRITISH_IGCSE,
			self::HS_IB,
			self::HS_FRENCH,
			self::HS_GERMAN,
		);
	}

	// -----------------------------------------------------------
	// High school tracks
	// -----------------------------------------------------------
	const TRACK_SCIENTIFIC = 'scientific';
	const TRACK_LITERARY   = 'literary';

	// -----------------------------------------------------------
	// Military status
	// -----------------------------------------------------------
	const MIL_COMPLETED           = 'completed';           // أدى الخدمة
	const MIL_EXEMPTED            = 'exempted';            // معفى نهائياً
	const MIL_POSTPONED           = 'postponed';           // تأجيل
	const MIL_IN_SERVICE_AGE      = 'in_service_age';      // في سن التجنيد
	const MIL_STUDY_POSTPONEMENT  = 'study_postponement';  // تأجيل دراسة

	public static function accepted_military_statuses(): array {
		return array( self::MIL_COMPLETED, self::MIL_EXEMPTED, self::MIL_POSTPONED );
	}

	// -----------------------------------------------------------
	// Committee decisions
	// -----------------------------------------------------------
	const DECISION_ACCEPTED = 'accepted';
	const DECISION_WAITING  = 'waiting';
	const DECISION_REJECTED = 'rejected';

	// -----------------------------------------------------------
	// Committee weight percentages
	// -----------------------------------------------------------
	public static function committee_weights( int $committee_size, int $member_number, string $decision ): float {
		if ( $committee_size === 4 ) {
			$table = array(
				1 => array( 'accepted' => 25.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
				2 => array( 'accepted' => 25.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
				3 => array( 'accepted' => 25.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
				4 => array( 'accepted' => 25.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
			);
		} elseif ( $committee_size === 3 ) {
			$table = array(
				1 => array( 'accepted' => 40.0, 'waiting' => 20.0, 'rejected' => 0.0 ),
				2 => array( 'accepted' => 30.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
				3 => array( 'accepted' => 30.0, 'waiting' => 15.0, 'rejected' => 0.0 ),
			);
		} else {
			return 0.0;
		}

		return $table[ $member_number ][ $decision ] ?? 0.0;
	}

	// -----------------------------------------------------------
	// Language proficiency levels
	// -----------------------------------------------------------
	const LANG_BEGINNER          = 'beginner';
	const LANG_ELEMENTARY        = 'elementary';
	const LANG_PRE_INTERMEDIATE  = 'pre_intermediate';
	const LANG_INTERMEDIATE      = 'intermediate';
	const LANG_UPPER_INTERMEDIATE = 'upper_intermediate';

	public static function accepted_language_levels(): array {
		return array(
			self::LANG_ELEMENTARY,
			self::LANG_PRE_INTERMEDIATE,
			self::LANG_INTERMEDIATE,
			self::LANG_UPPER_INTERMEDIATE,
		);
	}

	// -----------------------------------------------------------
	// Medical exam elements
	// -----------------------------------------------------------
	public static function medical_elements(): array {
		return array( 'internal', 'chest', 'eye', 'toxicology', 'virology', 'blood' );
	}

	const MEDICAL_FIT   = 'fit';
	const MEDICAL_UNFIT = 'unfit';

	// -----------------------------------------------------------
	// Notification channels & types
	// -----------------------------------------------------------
	const CHANNEL_WHATSAPP = 'whatsapp';
	const CHANNEL_EMAIL    = 'email';

	const NOTIF_APPLICATION_RECEIVED         = 'application_received';
	const NOTIF_STAGE_ONE_REJECTED           = 'stage_one_rejected';
	const NOTIF_DOCUMENTS_PENDING_REMINDER   = 'documents_pending_reminder';
	const NOTIF_DOCUMENTS_RECEIVED           = 'documents_received';
	const NOTIF_INTERVIEW_SCHEDULED          = 'interview_scheduled';
	const NOTIF_MEDICAL_SCHEDULED            = 'medical_scheduled';
	const NOTIF_FINAL_ACCEPTED               = 'final_accepted';
	const NOTIF_FINAL_REJECTED               = 'final_rejected';

	// -----------------------------------------------------------
	// Reminder configuration
	// -----------------------------------------------------------
	const REMINDER_INTERVAL_DAYS = 4;

	// -----------------------------------------------------------
	// Localized labels
	// -----------------------------------------------------------
	public static function status_label( string $status ): string {
		$labels = array(
			self::STATUS_SUBMITTED_STAGE_1     => __( 'قدم البيانات المبدئية', 'rsyi-student-affairs' ),
			self::STATUS_REJECTED_STAGE_1      => __( 'مرفوض (فشل الفلترة)', 'rsyi-student-affairs' ),
			self::STATUS_PENDING_DOCUMENTS     => __( 'في انتظار رفع الأوراق', 'rsyi-student-affairs' ),
			self::STATUS_DOCUMENTS_UPLOADED    => __( 'تم رفع الأوراق', 'rsyi-student-affairs' ),
			self::STATUS_INTERVIEW_SCHEDULED   => __( 'تم تحديد موعد المقابلة', 'rsyi-student-affairs' ),
			self::STATUS_INTERVIEW_ATTENDED    => __( 'حضر المقابلة', 'rsyi-student-affairs' ),
			self::STATUS_INTERVIEW_ACCEPTED    => __( 'مقبول من اللجنة', 'rsyi-student-affairs' ),
			self::STATUS_INTERVIEW_WAITING     => __( 'قائمة انتظار', 'rsyi-student-affairs' ),
			self::STATUS_INTERVIEW_REJECTED    => __( 'مرفوض من اللجنة', 'rsyi-student-affairs' ),
			self::STATUS_MEDICAL_SCHEDULED     => __( 'تم تحديد موعد الفحص الطبي', 'rsyi-student-affairs' ),
			self::STATUS_MEDICAL_PASSED        => __( 'اجتاز الفحص الطبي', 'rsyi-student-affairs' ),
			self::STATUS_MEDICAL_FAILED        => __( 'رسب في الفحص الطبي', 'rsyi-student-affairs' ),
			self::STATUS_LANGUAGE_TESTED       => __( 'تم اختبار المستوى', 'rsyi-student-affairs' ),
			self::STATUS_FINAL_ACCEPTED        => __( 'مقبول نهائياً', 'rsyi-student-affairs' ),
			self::STATUS_FINAL_REJECTED        => __( 'مرفوض نهائياً', 'rsyi-student-affairs' ),
		);

		return $labels[ $status ] ?? $status;
	}

	public static function rejection_label( string $code ): string {
		$labels = array(
			self::REJECT_NATIONALITY       => __( 'المنحة مخصصة للمصريين فقط', 'rsyi-student-affairs' ),
			self::REJECT_AGE_LOW           => __( 'السن أقل من 21 سنة', 'rsyi-student-affairs' ),
			self::REJECT_AGE_HIGH          => __( 'السن أكبر من 29 سنة', 'rsyi-student-affairs' ),
			self::REJECT_GENDER            => __( 'حالياً المعهد غير مهيّأ لاستقبال الإناث', 'rsyi-student-affairs' ),
			self::REJECT_HIGH_SCHOOL_TYPE  => __( 'نوع الثانوية غير مقبول', 'rsyi-student-affairs' ),
			self::REJECT_HIGH_SCHOOL_TRACK => __( 'يشترط شعبة علمي', 'rsyi-student-affairs' ),
			self::REJECT_MILITARY_STATUS   => __( 'يشترط موقف واضح من التجنيد', 'rsyi-student-affairs' ),
		);

		return $labels[ $code ] ?? $code;
	}
}
