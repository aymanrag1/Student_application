<?php
/**
 * Stage 2 documents upload form.
 *
 * @var array $student Student array.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

$student_id = (int) $student['id'];
$all_types  = array_merge( RSYI_Document::required_types(), RSYI_Document::optional_types() );
$uploaded   = RSYI_Document::all_for_student( $student_id );
$uploaded_map = array();
foreach ( $uploaded as $u ) {
	$uploaded_map[ $u['document_type'] ] = $u;
}
?>
<form
	class="rsyi-form rsyi-stage-two-form"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	enctype="multipart/form-data"
>
	<?php wp_nonce_field( RSYI_Stage_Two_Handler::ACTION_NAME, RSYI_Stage_Two_Handler::NONCE_NAME ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( RSYI_Stage_Two_Handler::ACTION_NAME ); ?>">
	<input type="hidden" name="application_code" value="<?php echo esc_attr( $student['application_code'] ); ?>">

	<div class="rsyi-form-intro">
		<h2><?php esc_html_e( 'رفع الأوراق المطلوبة', 'rsyi-student-affairs' ); ?></h2>
		<p>
			<?php
			printf(
				esc_html__( 'أهلاً %s. من فضلك ارفع الأوراق التالية بصيغة JPG / PNG / PDF (حد أقصى 5 ميجا لكل ملف).', 'rsyi-student-affairs' ),
				'<strong>' . esc_html( $student['full_name'] ) . '</strong>'
			);
			?>
		</p>
	</div>

	<?php foreach ( $all_types as $doc_type ) : ?>
		<?php
		$is_required = in_array( $doc_type, RSYI_Document::required_types(), true );
		$existing    = $uploaded_map[ $doc_type ] ?? null;
		?>
		<div class="rsyi-field">
			<label for="rsyi_doc_<?php echo esc_attr( $doc_type ); ?>">
				<?php echo esc_html( RSYI_Document::type_label( $doc_type ) ); ?>
				<?php if ( $is_required ) : ?>
					<span class="req">*</span>
				<?php endif; ?>
			</label>
			<?php if ( $existing ) : ?>
				<div class="rsyi-uploaded-badge">
					<?php esc_html_e( 'تم الرفع', 'rsyi-student-affairs' ); ?> ✓
					<small><?php echo esc_html( $existing['file_name'] ); ?></small>
				</div>
			<?php endif; ?>
			<input
				type="file"
				id="rsyi_doc_<?php echo esc_attr( $doc_type ); ?>"
				name="<?php echo esc_attr( $doc_type ); ?>"
				accept=".jpg,.jpeg,.png,.pdf"
				<?php echo ( $is_required && ! $existing ) ? 'required' : ''; ?>
			>
		</div>
	<?php endforeach; ?>

	<div class="rsyi-form-actions">
		<button type="submit" class="rsyi-btn rsyi-btn-primary">
			<?php esc_html_e( 'حفظ الأوراق', 'rsyi-student-affairs' ); ?>
		</button>
	</div>
</form>
