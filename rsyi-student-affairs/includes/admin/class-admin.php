<?php
/**
 * Admin bootstrap - registers menu pages.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Admin {

	const MENU_SLUG           = 'rsyi-candidates';
	const CANDIDATE_PAGE_SLUG = 'rsyi-candidate';
	const INTERVIEWS_SLUG     = 'rsyi-interviews';
	const MEDICAL_SLUG        = 'rsyi-medical';
	const REPORTS_SLUG        = 'rsyi-reports';
	const SETTINGS_SLUG       = 'rsyi-settings';

	public static function register(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_pages' ) );
	}

	public static function register_pages(): void {
		add_menu_page(
			__( 'شئون الطلاب', 'rsyi-student-affairs' ),
			__( 'شئون الطلاب', 'rsyi-student-affairs' ),
			'manage_options',
			self::MENU_SLUG,
			array( 'RSYI_Candidates_Page', 'render' ),
			'dashicons-groups',
			25
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'المتقدمون', 'rsyi-student-affairs' ),
			__( 'المتقدمون', 'rsyi-student-affairs' ),
			'manage_options',
			self::MENU_SLUG,
			array( 'RSYI_Candidates_Page', 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'المقابلات', 'rsyi-student-affairs' ),
			__( 'المقابلات', 'rsyi-student-affairs' ),
			'manage_options',
			self::INTERVIEWS_SLUG,
			array( 'RSYI_Interviews_Page', 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'تفاصيل الطالب', 'rsyi-student-affairs' ),
			'',
			'manage_options',
			self::CANDIDATE_PAGE_SLUG,
			array( 'RSYI_Candidate_Detail', 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'الفحص الطبي واختبار اللغة', 'rsyi-student-affairs' ),
			__( 'الفحص الطبي واللغة', 'rsyi-student-affairs' ),
			'manage_options',
			self::MEDICAL_SLUG,
			array( 'RSYI_Medical_Page', 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'التقارير', 'rsyi-student-affairs' ),
			__( 'التقارير', 'rsyi-student-affairs' ),
			'manage_options',
			self::REPORTS_SLUG,
			array( 'RSYI_Reports_Page', 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'الإعدادات', 'rsyi-student-affairs' ),
			__( 'الإعدادات', 'rsyi-student-affairs' ),
			'manage_options',
			self::SETTINGS_SLUG,
			array( __CLASS__, 'render_placeholder' )
		);
	}

	public static function render_placeholder(): void {
		echo '<div class="wrap"><h1>' . esc_html__( 'قيد التطوير', 'rsyi-student-affairs' ) . '</h1></div>';
	}
}
