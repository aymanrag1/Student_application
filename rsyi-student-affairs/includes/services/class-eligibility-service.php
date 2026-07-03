<?php
/**
 * Eligibility Service - stage 1 auto-filter.
 *
 * Rules (from spec):
 *  - Nationality: Egyptian only
 *  - Age: 21..29 inclusive
 *  - Gender: male only (currently)
 *  - High school: scientific track of general Egyptian / Azhari,
 *    OR accepted foreign diploma (American / British / IB / etc)
 *  - Military status: completed / exempted / postponed
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Eligibility_Service {

	const MIN_AGE = 21;
	const MAX_AGE = 29;

	/**
	 * Evaluate applicant data against eligibility rules.
	 *
	 * @param array $data Applicant data (from stage 1 form).
	 * @return array {
	 *     @type bool     $eligible        True if passes all rules.
	 *     @type string[] $rejection_codes List of failure codes (empty if eligible).
	 *     @type string[] $rejection_reasons Human-readable Arabic reasons.
	 * }
	 */
	public static function evaluate( array $data ): array {
		$codes = array();

		// Nationality
		$nationality = strtolower( trim( $data['nationality'] ?? '' ) );
		if ( ! self::is_egyptian( $nationality ) ) {
			$codes[] = RSYI_Constants::REJECT_NATIONALITY;
		}

		// Age
		$dob = $data['date_of_birth'] ?? '';
		if ( $dob ) {
			$age = self::calculate_age( $dob );
			if ( $age < self::MIN_AGE ) {
				$codes[] = RSYI_Constants::REJECT_AGE_LOW;
			} elseif ( $age > self::MAX_AGE ) {
				$codes[] = RSYI_Constants::REJECT_AGE_HIGH;
			}
		}

		// Gender
		$gender = strtolower( $data['gender'] ?? '' );
		if ( $gender !== 'male' ) {
			$codes[] = RSYI_Constants::REJECT_GENDER;
		}

		// High school type
		$hs_type = $data['high_school_type'] ?? '';
		if ( ! in_array( $hs_type, RSYI_Constants::accepted_high_school_types(), true ) ) {
			$codes[] = RSYI_Constants::REJECT_HIGH_SCHOOL_TYPE;
		}

		// High school track - only required for Egyptian schools (general/azhari)
		$requires_track = in_array(
			$hs_type,
			array( RSYI_Constants::HS_GENERAL_EGYPTIAN, RSYI_Constants::HS_AZHARI ),
			true
		);

		if ( $requires_track ) {
			$track = $data['high_school_track'] ?? '';
			if ( $track !== RSYI_Constants::TRACK_SCIENTIFIC ) {
				$codes[] = RSYI_Constants::REJECT_HIGH_SCHOOL_TRACK;
			}
		}

		// Military status
		$mil = $data['military_status'] ?? '';
		if ( ! in_array( $mil, RSYI_Constants::accepted_military_statuses(), true ) ) {
			$codes[] = RSYI_Constants::REJECT_MILITARY_STATUS;
		}

		$reasons = array_map(
			array( 'RSYI_Constants', 'rejection_label' ),
			$codes
		);

		return array(
			'eligible'          => empty( $codes ),
			'rejection_codes'   => $codes,
			'rejection_reasons' => $reasons,
		);
	}

	private static function is_egyptian( string $nationality ): bool {
		$aliases = array(
			'egyptian',
			'egypt',
			'مصري',
			'مصرية',
			'مصر',
			'eg',
		);
		return in_array( $nationality, $aliases, true );
	}

	public static function calculate_age( string $dob ): int {
		try {
			$birth = new DateTime( $dob );
		} catch ( Exception $e ) {
			return 0;
		}
		$today = new DateTime( current_time( 'mysql' ) );
		return (int) $today->diff( $birth )->y;
	}

	/**
	 * Validate Egyptian national ID format (14 digits + basic Luhn-ish check).
	 * We don't fully validate here - just format checks.
	 */
	public static function validate_national_id( string $national_id ): bool {
		return (bool) preg_match( '/^[23][0-9]{13}$/', $national_id );
	}
}
