<?php
/**
 * Candidate detail template.
 *
 * @var array $student
 * @var array $documents
 * @var array $history
 * @var array $pipeline
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

$list_url = admin_url( 'admin.php?page=' . RSYI_Admin::MENU_SLUG );
?>
<div class="wrap rsyi-admin-wrap">
	<h1>
		<?php echo esc_html( $student['full_name'] ); ?>
		<code style="font-size:14px;"><?php echo esc_html( $student['application_code'] ); ?></code>
		<a href="<?php echo esc_url( $list_url ); ?>" class="page-title-action"><?php esc_html_e( '← الرجوع للقائمة', 'rsyi-student-affairs' ); ?></a>
	</h1>

	<div class="rsyi-detail-grid">
		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'الحالة الحالية', 'rsyi-student-affairs' ); ?></h2>
			<p class="rsyi-current-status">
				<span class="rsyi-status-badge rsyi-status-<?php echo esc_attr( $student['status'] ); ?>">
					<?php echo esc_html( RSYI_Constants::status_label( $student['status'] ) ); ?>
				</span>
			</p>
			<?php if ( $student['rejection_reason_code'] ) : ?>
				<p><strong><?php esc_html_e( 'سبب الرفض:', 'rsyi-student-affairs' ); ?></strong></p>
				<ul>
				<?php foreach ( explode( ',', $student['rejection_reason_code'] ) as $c ) : ?>
					<li><?php echo esc_html( RSYI_Constants::rejection_label( trim( $c ) ) ); ?></li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rsyi-inline-form">
				<?php wp_nonce_field( 'rsyi_change_status' ); ?>
				<input type="hidden" name="action" value="rsyi_change_status">
				<input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">
				<select name="new_status" required>
					<option value=""><?php esc_html_e( '-- تغيير الحالة --', 'rsyi-student-affairs' ); ?></option>
					<?php foreach ( $pipeline as $val => $label ) : ?>
						<?php if ( $val === '' ) continue; ?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $student['status'], $val ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<input type="text" name="reason" placeholder="<?php esc_attr_e( 'سبب التغيير', 'rsyi-student-affairs' ); ?>">
				<button class="button button-primary"><?php esc_html_e( 'تحديث', 'rsyi-student-affairs' ); ?></button>
			</form>
		</div>

		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'البيانات الشخصية', 'rsyi-student-affairs' ); ?></h2>
			<table class="rsyi-kv-table">
				<tr><th><?php esc_html_e( 'الرقم القومي', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['national_id'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'تاريخ الميلاد', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['date_of_birth'] ); ?> (<?php echo (int) RSYI_Eligibility_Service::calculate_age( $student['date_of_birth'] ); ?> <?php esc_html_e( 'سنة', 'rsyi-student-affairs' ); ?>)</td></tr>
				<tr><th><?php esc_html_e( 'الإيميل', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['email'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الموبايل', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['mobile_1'] ); ?><?php if ( $student['mobile_2'] ) echo ' / ' . esc_html( $student['mobile_2'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الواتساب', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['whatsapp'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'العنوان', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['address'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الوظيفة', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['job'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الجنسية', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['nationality'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الحالة الاجتماعية', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['marital_status'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الجنس', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['gender'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الطول / الوزن', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( ( $student['height'] ?: '-' ) . ' سم / ' . ( $student['weight'] ?: '-' ) . ' كجم' ); ?></td></tr>
			</table>
		</div>

		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'الدراسة والتجنيد', 'rsyi-student-affairs' ); ?></h2>
			<table class="rsyi-kv-table">
				<tr><th><?php esc_html_e( 'نوع الثانوية', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['high_school_type'] ); ?></td></tr>
				<tr><th><?php esc_html_e( 'الشعبة', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['high_school_track'] ?: '-' ); ?></td></tr>
				<tr><th><?php esc_html_e( 'آخر مؤهل', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['last_qualification'] ?: '-' ); ?></td></tr>
				<tr><th><?php esc_html_e( 'موقف التجنيد', 'rsyi-student-affairs' ); ?></th><td><?php echo esc_html( $student['military_status'] ); ?></td></tr>
			</table>
			<h3><?php esc_html_e( 'سبب التقدم', 'rsyi-student-affairs' ); ?></h3>
			<p><?php echo esc_html( $student['reason_for_applying'] ); ?></p>
		</div>

		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'الأوراق المرفوعة', 'rsyi-student-affairs' ); ?> (<?php echo (int) count( $documents ); ?>)</h2>
			<?php if ( empty( $documents ) ) : ?>
				<p><?php esc_html_e( 'لم يرفع أي أوراق بعد.', 'rsyi-student-affairs' ); ?></p>
			<?php else : ?>
				<ul class="rsyi-docs-list">
				<?php foreach ( $documents as $doc ) : ?>
					<li>
						<strong><?php echo esc_html( RSYI_Document::type_label( $doc['document_type'] ) ); ?></strong>
						<span class="rsyi-doc-meta">
							<?php echo esc_html( size_format( (int) $doc['file_size'] ) ); ?> ·
							<?php echo esc_html( mysql2date( 'Y-m-d H:i', $doc['uploaded_at'] ) ); ?>
						</span>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $student['status'] === RSYI_Constants::STATUS_PENDING_DOCUMENTS ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'rsyi_mark_docs_received' ); ?>
					<input type="hidden" name="action" value="rsyi_mark_docs_received">
					<input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">
					<button class="button"><?php esc_html_e( 'تسجيل: تم استلام الأوراق يدوياً', 'rsyi-student-affairs' ); ?></button>
				</form>
			<?php endif; ?>

			<?php if ( in_array( $student['status'], array( RSYI_Constants::STATUS_PENDING_DOCUMENTS ), true ) ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:8px;">
					<?php wp_nonce_field( 'rsyi_toggle_reminder' ); ?>
					<input type="hidden" name="action" value="rsyi_toggle_reminder">
					<input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">
					<button class="button">
						<?php echo (int) $student['reminder_stopped']
							? esc_html__( 'استئناف التذكيرات', 'rsyi-student-affairs' )
							: esc_html__( 'إيقاف التذكيرات', 'rsyi-student-affairs' ); ?>
					</button>
					<small>(<?php esc_html_e( 'عدد التذكيرات المرسلة:', 'rsyi-student-affairs' ); ?> <?php echo (int) $student['reminder_count']; ?>)</small>
				</form>
			<?php endif; ?>
		</div>

		<div class="rsyi-detail-card rsyi-full">
			<h2><?php esc_html_e( 'سجل التغيّرات', 'rsyi-student-affairs' ); ?></h2>
			<table class="wp-list-table widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'التاريخ', 'rsyi-student-affairs' ); ?></th>
						<th><?php esc_html_e( 'من', 'rsyi-student-affairs' ); ?></th>
						<th><?php esc_html_e( 'إلى', 'rsyi-student-affairs' ); ?></th>
						<th><?php esc_html_e( 'بواسطة', 'rsyi-student-affairs' ); ?></th>
						<th><?php esc_html_e( 'السبب', 'rsyi-student-affairs' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $history as $h ) : ?>
					<tr>
						<td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $h['changed_at'] ) ); ?></td>
						<td><?php echo esc_html( $h['from_status'] ? RSYI_Constants::status_label( $h['from_status'] ) : '-' ); ?></td>
						<td><?php echo esc_html( RSYI_Constants::status_label( $h['to_status'] ) ); ?></td>
						<td><?php echo esc_html( $h['changed_by'] ? get_userdata( $h['changed_by'] )->display_name ?? '-' : __( 'النظام', 'rsyi-student-affairs' ) ); ?></td>
						<td><?php echo esc_html( $h['reason'] ?: '-' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
