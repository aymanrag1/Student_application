<?php
/**
 * Medical & language test admin page.
 *
 * @var array      $candidates
 * @var array|null $student
 * @var array|null $medical
 * @var array|null $language
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap rsyi-admin-wrap">
	<h1><?php esc_html_e( 'الفحص الطبي واختبار اللغة', 'rsyi-student-affairs' ); ?></h1>

	<div class="rsyi-detail-grid">
		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'المتقدمون', 'rsyi-student-affairs' ); ?></h2>
			<ul class="rsyi-docs-list">
			<?php if ( empty( $candidates ) ) : ?>
				<li><?php esc_html_e( 'لا يوجد متقدمون مرحّلين للفحص.', 'rsyi-student-affairs' ); ?></li>
			<?php endif; ?>
			<?php foreach ( $candidates as $c ) : ?>
				<li>
					<a href="<?php echo esc_url( add_query_arg( array( 'student' => $c['id'] ) ) ); ?>">
						<strong><?php echo esc_html( $c['full_name'] ); ?></strong>
						<small><br><?php echo esc_html( $c['application_code'] ); ?> · <?php echo esc_html( RSYI_Constants::status_label( $c['status'] ) ); ?></small>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( $student ) : ?>
			<div class="rsyi-detail-card rsyi-full">
				<h2><?php echo esc_html( $student['full_name'] ); ?> · <?php echo esc_html( $student['application_code'] ); ?></h2>

				<h3><?php esc_html_e( 'الفحص الطبي (6 عناصر)', 'rsyi-student-affairs' ); ?></h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'rsyi_save_medical' ); ?>
					<input type="hidden" name="action" value="rsyi_save_medical">
					<input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">

					<div class="rsyi-field">
						<label><?php esc_html_e( 'تاريخ الفحص', 'rsyi-student-affairs' ); ?></label>
						<input type="date" name="exam_date" value="<?php echo esc_attr( $medical['exam_date'] ?? current_time( 'Y-m-d' ) ); ?>">
					</div>

					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'العنصر', 'rsyi-student-affairs' ); ?></th>
								<th><?php esc_html_e( 'النتيجة', 'rsyi-student-affairs' ); ?></th>
								<th><?php esc_html_e( 'تعليق', 'rsyi-student-affairs' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$elements = array(
								'internal'   => __( 'باطنة', 'rsyi-student-affairs' ),
								'chest'      => __( 'صدر', 'rsyi-student-affairs' ),
								'eye'        => __( 'رمد', 'rsyi-student-affairs' ),
								'toxicology' => __( 'سموم', 'rsyi-student-affairs' ),
								'virology'   => __( 'فيروسات', 'rsyi-student-affairs' ),
								'blood'      => __( 'صورة دم', 'rsyi-student-affairs' ),
							);
							foreach ( $elements as $key => $label ) :
								$current_val = $medical[ $key . '_exam' ] ?? '';
								$current_com = $medical[ $key . '_comment' ] ?? '';
							?>
							<tr>
								<td><strong><?php echo esc_html( $label ); ?></strong></td>
								<td>
									<select name="<?php echo esc_attr( $key . '_exam' ); ?>">
										<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
										<option value="fit"   <?php selected( $current_val, 'fit' ); ?>><?php esc_html_e( 'لائق', 'rsyi-student-affairs' ); ?></option>
										<option value="unfit" <?php selected( $current_val, 'unfit' ); ?>><?php esc_html_e( 'غير لائق', 'rsyi-student-affairs' ); ?></option>
									</select>
								</td>
								<td><input type="text" name="<?php echo esc_attr( $key . '_comment' ); ?>" value="<?php echo esc_attr( $current_com ); ?>" style="width:100%;"></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<?php if ( $medical && $medical['overall_result'] ) : ?>
						<p><strong><?php esc_html_e( 'النتيجة الإجمالية:', 'rsyi-student-affairs' ); ?></strong>
							<span class="rsyi-status-badge <?php echo $medical['overall_result'] === 'fit' ? 'rsyi-status-medical_passed' : 'rsyi-status-medical_failed'; ?>">
								<?php echo esc_html( $medical['overall_result'] === 'fit' ? __( 'لائق', 'rsyi-student-affairs' ) : __( 'غير لائق', 'rsyi-student-affairs' ) ); ?>
							</span>
						</p>
					<?php endif; ?>

					<button class="button button-primary"><?php esc_html_e( 'حفظ الفحص الطبي', 'rsyi-student-affairs' ); ?></button>
				</form>

				<hr style="margin:24px 0;">

				<h3><?php esc_html_e( 'اختبار تحديد المستوى', 'rsyi-student-affairs' ); ?></h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'rsyi_save_language' ); ?>
					<input type="hidden" name="action" value="rsyi_save_language">
					<input type="hidden" name="student_id" value="<?php echo (int) $student['id']; ?>">

					<div class="rsyi-field">
						<label><?php esc_html_e( 'تاريخ الاختبار', 'rsyi-student-affairs' ); ?></label>
						<input type="date" name="test_date" value="<?php echo esc_attr( $language['test_date'] ?? current_time( 'Y-m-d' ) ); ?>">
					</div>

					<div class="rsyi-field">
						<label><?php esc_html_e( 'المستوى', 'rsyi-student-affairs' ); ?></label>
						<select name="level" required>
							<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
							<?php foreach ( array( 'beginner', 'elementary', 'pre_intermediate', 'intermediate', 'upper_intermediate' ) as $lvl ) : ?>
								<option value="<?php echo esc_attr( $lvl ); ?>" <?php selected( $language['level'] ?? '', $lvl ); ?>>
									<?php echo esc_html( RSYI_Language_Test::level_label( $lvl ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<small><?php esc_html_e( 'يُقبل من Elementary فأعلى.', 'rsyi-student-affairs' ); ?></small>
					</div>

					<div class="rsyi-field">
						<label><?php esc_html_e( 'ملاحظات', 'rsyi-student-affairs' ); ?></label>
						<textarea name="notes" rows="2" style="width:100%;"><?php echo esc_textarea( $language['notes'] ?? '' ); ?></textarea>
					</div>

					<button class="button button-primary"><?php esc_html_e( 'حفظ نتيجة الاختبار', 'rsyi-student-affairs' ); ?></button>
				</form>
			</div>
		<?php endif; ?>
	</div>
</div>
