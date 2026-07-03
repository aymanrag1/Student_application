<?php
/**
 * Reports page template.
 *
 * @var array $stats
 * @var array $pipeline
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

$total = array_sum( $stats );
?>
<div class="wrap rsyi-admin-wrap">
	<h1><?php esc_html_e( 'التقارير والتصدير', 'rsyi-student-affairs' ); ?></h1>

	<div class="rsyi-detail-grid">
		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'إحصائيات', 'rsyi-student-affairs' ); ?></h2>
			<p><strong><?php esc_html_e( 'إجمالي المتقدمين:', 'rsyi-student-affairs' ); ?></strong> <?php echo esc_html( number_format_i18n( $total ) ); ?></p>
			<table class="rsyi-kv-table">
			<?php foreach ( $pipeline as $status => $label ) : ?>
				<?php if ( '' === $status ) continue; ?>
				<tr>
					<th><?php echo esc_html( $label ); ?></th>
					<td><?php echo esc_html( number_format_i18n( $stats[ $status ] ?? 0 ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</table>
		</div>

		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'تصدير كشف المتقدمين (CSV)', 'rsyi-student-affairs' ); ?></h2>
			<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'rsyi_export_candidates' ); ?>
				<input type="hidden" name="action" value="rsyi_export_candidates">
				<div class="rsyi-field">
					<label><?php esc_html_e( 'فلترة بالحالة', 'rsyi-student-affairs' ); ?></label>
					<select name="status">
						<?php foreach ( $pipeline as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<button class="button button-primary"><?php esc_html_e( 'تحميل CSV', 'rsyi-student-affairs' ); ?></button>
			</form>
		</div>

		<div class="rsyi-detail-card">
			<h2><?php esc_html_e( 'كشف الأمن (قبل المقابلة بيوم)', 'rsyi-student-affairs' ); ?></h2>
			<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'rsyi_export_security_list' ); ?>
				<input type="hidden" name="action" value="rsyi_export_security_list">
				<div class="rsyi-field">
					<label><?php esc_html_e( 'تاريخ المقابلات', 'rsyi-student-affairs' ); ?></label>
					<input type="date" name="date" required>
				</div>
				<button class="button button-primary"><?php esc_html_e( 'تحميل كشف الأمن', 'rsyi-student-affairs' ); ?></button>
			</form>
		</div>
	</div>
</div>
