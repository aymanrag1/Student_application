<?php
/**
 * Interviews admin page template.
 *
 * @var array $upcoming
 * @var array $eligible
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap rsyi-admin-wrap">
	<h1><?php esc_html_e( 'المقابلات', 'rsyi-student-affairs' ); ?></h1>

	<h2><?php esc_html_e( 'المقابلات القادمة', 'rsyi-student-affairs' ); ?></h2>
	<table class="wp-list-table widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'التاريخ / الوقت', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الطالب', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الموبايل', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'المكان', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الحضور', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'اللجنة (n)', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'إجراءات', 'rsyi-student-affairs' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( empty( $upcoming ) ) : ?>
			<tr><td colspan="7"><?php esc_html_e( 'لا توجد مقابلات مجدولة.', 'rsyi-student-affairs' ); ?></td></tr>
		<?php endif; ?>
		<?php foreach ( $upcoming as $i ) : ?>
			<tr>
				<td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $i['interview_date'] ) ); ?></td>
				<td><a href="<?php echo esc_url( RSYI_Candidate_Detail::detail_url( (int) $i['student_id'] ) ); ?>"><?php echo esc_html( $i['full_name'] ); ?></a>
					<small><br><code><?php echo esc_html( $i['application_code'] ); ?></code></small></td>
				<td><?php echo esc_html( $i['mobile_1'] ); ?></td>
				<td><?php echo esc_html( $i['location'] ); ?></td>
				<td>
					<?php if ( is_null( $i['attended'] ) ) : ?>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
							<?php wp_nonce_field( 'rsyi_mark_attendance' ); ?>
							<input type="hidden" name="action" value="rsyi_mark_attendance">
							<input type="hidden" name="interview_id" value="<?php echo (int) $i['id']; ?>">
							<button name="attended" value="1" class="button button-small button-primary">حضر</button>
							<button name="attended" value="0" class="button button-small">غاب</button>
						</form>
					<?php else : ?>
						<span class="rsyi-status-badge <?php echo (int) $i['attended'] ? 'rsyi-status-interview_accepted' : 'rsyi-status-rejected_stage_1'; ?>">
							<?php echo (int) $i['attended'] ? esc_html__( 'حضر', 'rsyi-student-affairs' ) : esc_html__( 'غاب', 'rsyi-student-affairs' ); ?>
						</span>
					<?php endif; ?>
				</td>
				<td><?php echo (int) $i['committee_size']; ?></td>
				<td>
					<a class="button button-small" href="<?php echo esc_url( add_query_arg( array( 'interview_id' => $i['id'] ), admin_url( 'admin.php?page=' . RSYI_Admin::INTERVIEWS_SLUG ) ) ); ?>">
						<?php esc_html_e( 'اللجنة', 'rsyi-student-affairs' ); ?>
					</a>
				</td>
			</tr>
			<?php if ( isset( $_GET['interview_id'] ) && (int) $_GET['interview_id'] === (int) $i['id'] ) : ?>
				<tr><td colspan="7">
					<?php
					$votes = RSYI_Committee_Vote::for_interview( (int) $i['id'] );
					$totals = RSYI_Committee_Service::tally( (int) $i['id'] );
					include RSYI_PLUGIN_DIR . 'templates/admin/committee-panel.php';
					?>
				</td></tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>

	<hr style="margin:30px 0;">

	<h2><?php esc_html_e( 'تحديد موعد مقابلة جديد', 'rsyi-student-affairs' ); ?></h2>
	<?php if ( empty( $eligible ) ) : ?>
		<p><?php esc_html_e( 'لا يوجد متقدمون جاهزون للمقابلة الآن.', 'rsyi-student-affairs' ); ?></p>
	<?php else : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rsyi-schedule-form">
			<?php wp_nonce_field( 'rsyi_schedule_interview' ); ?>
			<input type="hidden" name="action" value="rsyi_schedule_interview">

			<div class="rsyi-field">
				<label><?php esc_html_e( 'الطالب', 'rsyi-student-affairs' ); ?></label>
				<select name="student_id" required>
					<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
					<?php foreach ( $eligible as $s ) : ?>
						<option value="<?php echo (int) $s['id']; ?>"><?php echo esc_html( $s['full_name'] . ' (' . $s['application_code'] . ')' ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="rsyi-field">
				<label><?php esc_html_e( 'التاريخ', 'rsyi-student-affairs' ); ?></label>
				<input type="date" name="interview_date" required>
			</div>

			<div class="rsyi-field">
				<label><?php esc_html_e( 'الوقت', 'rsyi-student-affairs' ); ?></label>
				<input type="time" name="interview_time" required>
			</div>

			<div class="rsyi-field">
				<label><?php esc_html_e( 'حجم اللجنة', 'rsyi-student-affairs' ); ?></label>
				<select name="committee_size">
					<option value="4">4 أعضاء</option>
					<option value="3">3 أعضاء</option>
				</select>
			</div>

			<div class="rsyi-field">
				<label><?php esc_html_e( 'المكان', 'rsyi-student-affairs' ); ?></label>
				<input type="text" name="location" value="El Gouna">
			</div>

			<button class="button button-primary"><?php esc_html_e( 'حفظ الموعد', 'rsyi-student-affairs' ); ?></button>
		</form>
	<?php endif; ?>
</div>
