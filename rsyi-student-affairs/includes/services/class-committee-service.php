<?php
/**
 * Committee Service - aggregates votes into a final decision.
 *
 * Decision rules (configurable via options later):
 *  - Total accepted score >= 60 → accepted
 *  - Total accepted + waiting >= 30 → waiting
 *  - Else → rejected
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Committee_Service {

	const THRESHOLD_ACCEPT  = 60.0;
	const THRESHOLD_WAITING = 30.0;

	public static function tally( int $interview_id ): array {
		$votes = RSYI_Committee_Vote::for_interview( $interview_id );

		$totals = array(
			'accepted' => 0.0,
			'waiting'  => 0.0,
			'rejected' => 0.0,
			'count'    => count( $votes ),
		);

		foreach ( $votes as $v ) {
			$totals[ $v['decision'] ] += (float) $v['weight_percentage'];
		}

		return $totals;
	}

	public static function finalize( int $interview_id, int $user_id ): ?string {
		$interview = RSYI_Interview::get( $interview_id );
		if ( ! $interview ) {
			return null;
		}

		$totals = self::tally( $interview_id );

		if ( $totals['count'] < (int) $interview['committee_size'] ) {
			return null; // Not all members voted yet.
		}

		$accepted = $totals['accepted'];
		$waiting  = $totals['waiting'];

		if ( $accepted >= self::THRESHOLD_ACCEPT ) {
			$decision = RSYI_Constants::DECISION_ACCEPTED;
			$status   = RSYI_Constants::STATUS_INTERVIEW_ACCEPTED;
		} elseif ( ( $accepted + $waiting ) >= self::THRESHOLD_WAITING ) {
			$decision = RSYI_Constants::DECISION_WAITING;
			$status   = RSYI_Constants::STATUS_INTERVIEW_WAITING;
		} else {
			$decision = RSYI_Constants::DECISION_REJECTED;
			$status   = RSYI_Constants::STATUS_INTERVIEW_REJECTED;
		}

		RSYI_Interview::update_totals(
			$interview_id,
			array(
				'total_acceptance_score' => $accepted,
				'total_waiting_score'    => $waiting,
				'total_rejection_score'  => $totals['rejected'],
				'final_decision'         => $decision,
			)
		);

		RSYI_Student::change_status(
			(int) $interview['student_id'],
			$status,
			$user_id,
			sprintf(
				__( 'قرار اللجنة النهائي: %s (قبول %.1f%%، انتظار %.1f%%)', 'rsyi-student-affairs' ),
				$decision,
				$accepted,
				$waiting
			)
		);

		return $decision;
	}
}
