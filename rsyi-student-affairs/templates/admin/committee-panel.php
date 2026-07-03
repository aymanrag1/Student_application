<?php
/**
 * Committee voting panel for an interview (embedded row).
 *
 * @var array $i        Interview joined with student.
 * @var array $votes    Committee votes so far.
 * @var array $totals   Score totals.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

$current_user_id = get_current_user_id();
$current_vote = null;
$member_number = null;
foreach ( $votes as $v ) {
	if ( (int) $v['member_user_id'] === $current_user_id ) {
		$current_vote  = $v;
		$member_number = (int) $v['member_number'];
	}
}

if ( ! $member_number ) {
	$member_number = count( $votes ) + 1;
	if ( $member_number > (int) $i['committee_size'] ) {
		$member_number = (int) $i['committee_size'];
	}
}
?>
<div class="rsyi-committee-panel">
	<h3><?php esc_html_e( 'لوحة اللجنة', 'rsyi-student-affairs' ); ?></h3>

	<div class="rsyi-committee-totals">
		<span class="rsyi-score-badge accepted">
			<?php esc_html_e( 'قبول', 'rsyi-student-affairs' ); ?>: <?php echo esc_html( number_format_i18n( $totals['accepted'], 1 ) ); ?>%
		</span>
		<span class="rsyi-score-badge waiting">
			<?php esc_html_e( 'انتظار', 'rsyi-student-affairs' ); ?>: <?php echo esc_html( number_format_i18n( $totals['waiting'], 1 ) ); ?>%
		</span>
		<span class="rsyi-score-badge rejected">
			<?php esc_html_e( 'رفض', 'rsyi-student-affairs' ); ?>: <?php echo esc_html( number_format_i18n( $totals['rejected'], 1 ) ); ?>%
		</span>
		<span class="rsyi-score-badge">
			<?php esc_html_e( 'أصوات', 'rsyi-student-affairs' ); ?>: <?php echo (int) $totals['count']; ?>/<?php echo (int) $i['committee_size']; ?>
		</span>
	</div>

	<?php if ( ! empty( $votes ) ) : ?>
		<table class="wp-list-table widefat">
			<thead>
				<tr>
					<th><?php esc_html_e( 'رقم العضو', 'rsyi-student-affairs' ); ?></th>
					<th><?php esc_html_e( 'العضو', 'rsyi-student-affairs' ); ?></th>
					<th><?php esc_html_e( 'القرار', 'rsyi-student-affairs' ); ?></th>
					<th><?php esc_html_e( 'الوزن', 'rsyi-student-affairs' ); ?></th>
					<th><?php esc_html_e( 'التعليق', 'rsyi-student-affairs' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $votes as $v ) : ?>
				<tr>
					<td><?php echo (int) $v['member_number']; ?></td>
					<td><?php $u = get_userdata( (int) $v['member_user_id'] ); echo esc_html( $u ? $u->display_name : '-' ); ?></td>
					<td><?php echo esc_html( $v['decision'] ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (float) $v['weight_percentage'], 1 ) ); ?>%</td>
					<td><?php echo esc_html( $v['comment'] ?: '-' ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if ( ! $i['final_decision'] ) : ?>
		<h4><?php echo $current_vote ? esc_html__( 'تعديل صوتك', 'rsyi-student-affairs' ) : esc_html__( 'أدلِ بصوتك', 'rsyi-student-affairs' ); ?></h4>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rsyi-inline-form">
			<?php wp_nonce_field( 'rsyi_cast_vote' ); ?>
			<input type="hidden" name="action" value="rsyi_cast_vote">
			<input type="hidden" name="interview_id" value="<?php echo (int) $i['id']; ?>">
			<label><?php esc_html_e( 'رقم عضويتك:', 'rsyi-student-affairs' ); ?>
				<select name="member_number">
					<?php for ( $n = 1; $n <= (int) $i['committee_size']; $n++ ) : ?>
						<option value="<?php echo (int) $n; ?>" <?php selected( $member_number, $n ); ?>><?php echo (int) $n; ?></option>
					<?php endfor; ?>
				</select>
			</label>
			<select name="decision" required>
				<option value=""><?php esc_html_e( '-- القرار --', 'rsyi-student-affairs' ); ?></option>
				<option value="accepted" <?php selected( $current_vote['decision'] ?? '', 'accepted' ); ?>><?php esc_html_e( 'قبول', 'rsyi-student-affairs' ); ?></option>
				<option value="waiting" <?php selected( $current_vote['decision'] ?? '', 'waiting' ); ?>><?php esc_html_e( 'انتظار', 'rsyi-student-affairs' ); ?></option>
				<option value="rejected" <?php selected( $current_vote['decision'] ?? '', 'rejected' ); ?>><?php esc_html_e( 'رفض', 'rsyi-student-affairs' ); ?></option>
			</select>
			<input type="text" name="comment" placeholder="<?php esc_attr_e( 'تعليق (اختياري)', 'rsyi-student-affairs' ); ?>" value="<?php echo esc_attr( $current_vote['comment'] ?? '' ); ?>">
			<button class="button button-primary"><?php esc_html_e( 'حفظ الصوت', 'rsyi-student-affairs' ); ?></button>
		</form>

		<?php if ( $totals['count'] >= (int) $i['committee_size'] ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:10px;">
				<?php wp_nonce_field( 'rsyi_finalize_interview' ); ?>
				<input type="hidden" name="action" value="rsyi_finalize_interview">
				<input type="hidden" name="interview_id" value="<?php echo (int) $i['id']; ?>">
				<button class="button button-hero button-primary"><?php esc_html_e( 'اعتماد القرار النهائي', 'rsyi-student-affairs' ); ?></button>
			</form>
		<?php endif; ?>
	<?php else : ?>
		<div class="rsyi-final-decision">
			<h4><?php esc_html_e( 'القرار النهائي:', 'rsyi-student-affairs' ); ?> <strong><?php echo esc_html( $i['final_decision'] ); ?></strong></h4>
		</div>
	<?php endif; ?>
</div>
