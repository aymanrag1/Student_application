<?php
/**
 * Committee Vote model.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Committee_Vote {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'rsyi_committee_votes';
	}

	public static function cast(
		int $interview_id,
		int $member_user_id,
		int $member_number,
		string $decision,
		int $committee_size,
		?string $comment = null
	): int {
		global $wpdb;

		$weight = RSYI_Constants::committee_weights( $committee_size, $member_number, $decision );

		$row = array(
			'interview_id'      => $interview_id,
			'member_user_id'    => $member_user_id,
			'member_number'     => $member_number,
			'decision'          => $decision,
			'weight_percentage' => $weight,
			'comment'           => $comment,
			'voted_at'          => current_time( 'mysql' ),
		);

		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id FROM " . self::table() . " WHERE interview_id = %d AND member_user_id = %d",
				$interview_id,
				$member_user_id
			),
			ARRAY_A
		);

		if ( $existing ) {
			$wpdb->update( self::table(), $row, array( 'id' => (int) $existing['id'] ) );
			return (int) $existing['id'];
		}

		$wpdb->insert( self::table(), $row );
		return (int) $wpdb->insert_id;
	}

	public static function for_interview( int $interview_id ): array {
		global $wpdb;
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM " . self::table() . " WHERE interview_id = %d ORDER BY member_number ASC", $interview_id ),
			ARRAY_A
		);
	}

	public static function for_member( int $interview_id, int $member_user_id ): ?array {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . self::table() . " WHERE interview_id = %d AND member_user_id = %d",
				$interview_id,
				$member_user_id
			),
			ARRAY_A
		);
		return $row ?: null;
	}
}
