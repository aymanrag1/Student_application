<?php
/**
 * Roles & capabilities.
 *
 * Custom roles:
 *  - rsyi_admin      : full plugin access
 *  - rsyi_committee  : vote in committee panels only
 *  - rsyi_medical    : record medical exams + language tests
 *  - rsyi_security   : view security access list (day-before interview)
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Roles {

	const CAP_MANAGE     = 'rsyi_manage_candidates';
	const CAP_VOTE       = 'rsyi_vote_committee';
	const CAP_MEDICAL    = 'rsyi_record_medical';
	const CAP_SECURITY   = 'rsyi_view_security_list';
	const CAP_EXPORT     = 'rsyi_export_reports';

	public static function install(): void {
		self::register_roles();
		self::grant_admin();
	}

	public static function register_roles(): void {
		add_role(
			'rsyi_admin',
			__( 'مدير شئون الطلاب', 'rsyi-student-affairs' ),
			array(
				'read'                => true,
				self::CAP_MANAGE      => true,
				self::CAP_VOTE        => true,
				self::CAP_MEDICAL     => true,
				self::CAP_SECURITY    => true,
				self::CAP_EXPORT      => true,
			)
		);

		add_role(
			'rsyi_committee',
			__( 'عضو لجنة مقابلات', 'rsyi-student-affairs' ),
			array(
				'read'          => true,
				self::CAP_VOTE  => true,
			)
		);

		add_role(
			'rsyi_medical',
			__( 'طبيب المعهد', 'rsyi-student-affairs' ),
			array(
				'read'              => true,
				self::CAP_MEDICAL   => true,
			)
		);

		add_role(
			'rsyi_security',
			__( 'موظف أمن', 'rsyi-student-affairs' ),
			array(
				'read'              => true,
				self::CAP_SECURITY  => true,
			)
		);
	}

	public static function grant_admin(): void {
		$admin = get_role( 'administrator' );
		if ( ! $admin ) return;

		$admin->add_cap( self::CAP_MANAGE );
		$admin->add_cap( self::CAP_VOTE );
		$admin->add_cap( self::CAP_MEDICAL );
		$admin->add_cap( self::CAP_SECURITY );
		$admin->add_cap( self::CAP_EXPORT );
	}
}
