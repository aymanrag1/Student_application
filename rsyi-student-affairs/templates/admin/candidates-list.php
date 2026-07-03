<?php
/**
 * Candidates list template.
 *
 * @var array $students
 * @var int   $total
 * @var int   $total_pages
 * @var int   $paged
 * @var string $status_filter
 * @var string $search
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

$pipeline = RSYI_Candidates_Page::status_pipeline();
?>
<div class="wrap rsyi-admin-wrap">
	<h1><?php esc_html_e( 'المتقدمون للمنحة', 'rsyi-student-affairs' ); ?>
		<span class="title-count"><?php echo esc_html( number_format_i18n( $total ) ); ?></span>
	</h1>

	<form method="get" class="rsyi-filters">
		<input type="hidden" name="page" value="<?php echo esc_attr( RSYI_Admin::MENU_SLUG ); ?>">
		<select name="status">
			<?php foreach ( $pipeline as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $status_filter, $val ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'اسم / رقم قومي / كود / إيميل', 'rsyi-student-affairs' ); ?>">
		<button class="button"><?php esc_html_e( 'بحث', 'rsyi-student-affairs' ); ?></button>
	</form>

	<table class="wp-list-table widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'الكود', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الاسم', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الرقم القومي', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الموبايل', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'الحالة', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'تاريخ التقديم', 'rsyi-student-affairs' ); ?></th>
				<th><?php esc_html_e( 'إجراءات', 'rsyi-student-affairs' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $students ) ) : ?>
				<tr><td colspan="7"><?php esc_html_e( 'لا يوجد متقدمون.', 'rsyi-student-affairs' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $students as $s ) : ?>
				<?php $detail_url = RSYI_Candidate_Detail::detail_url( (int) $s['id'] ); ?>
				<tr>
					<td><code><?php echo esc_html( $s['application_code'] ); ?></code></td>
					<td><a href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( $s['full_name'] ); ?></a></td>
					<td><?php echo esc_html( $s['national_id'] ); ?></td>
					<td><?php echo esc_html( $s['mobile_1'] ); ?></td>
					<td>
						<span class="rsyi-status-badge rsyi-status-<?php echo esc_attr( $s['status'] ); ?>">
							<?php echo esc_html( RSYI_Constants::status_label( $s['status'] ) ); ?>
						</span>
					</td>
					<td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $s['stage_one_submitted_at'] ) ); ?></td>
					<td><a class="button button-small" href="<?php echo esc_url( $detail_url ); ?>"><?php esc_html_e( 'عرض', 'rsyi-student-affairs' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ( $total_pages > 1 ) : ?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php
				echo paginate_links(
					array(
						'base'    => add_query_arg( 'paged', '%#%' ),
						'format'  => '',
						'current' => $paged,
						'total'   => $total_pages,
					)
				);
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
