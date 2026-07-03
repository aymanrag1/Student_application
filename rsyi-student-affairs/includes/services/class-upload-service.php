<?php
/**
 * Upload Service - handles secure file uploads for student documents.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Upload_Service {

	const MAX_SIZE_BYTES = 5242880; // 5 MB

	public static function allowed_mimes(): array {
		return array(
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'pdf'  => 'application/pdf',
		);
	}

	/**
	 * Handle a single uploaded file.
	 *
	 * @param array  $file       $_FILES['x'] array.
	 * @param int    $student_id Student ID.
	 * @param string $doc_type   Document type constant.
	 * @return array {file_path, file_name, file_size, mime_type} | WP_Error
	 */
	public static function handle_upload( array $file, int $student_id, string $doc_type ) {
		if ( ! isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) ) {
			return new WP_Error( 'no_file', __( 'لم يتم رفع أي ملف', 'rsyi-student-affairs' ) );
		}

		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			return new WP_Error( 'upload_error', __( 'خطأ أثناء رفع الملف', 'rsyi-student-affairs' ) );
		}

		if ( $file['size'] > self::MAX_SIZE_BYTES ) {
			return new WP_Error( 'too_large', __( 'حجم الملف أكبر من الحد المسموح (5 ميجا)', 'rsyi-student-affairs' ) );
		}

		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'invalid_upload', __( 'ملف غير صالح', 'rsyi-student-affairs' ) );
		}

		$check = wp_check_filetype_and_ext(
			$file['tmp_name'],
			$file['name'],
			self::allowed_mimes()
		);

		if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
			return new WP_Error( 'invalid_type', __( 'نوع الملف غير مسموح (JPG / PNG / PDF فقط)', 'rsyi-student-affairs' ) );
		}

		$dir = self::get_student_dir( $student_id );
		if ( ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'mkdir_failed', __( 'تعذر إنشاء مجلد الرفع', 'rsyi-student-affairs' ) );
		}

		$safe_name = sanitize_file_name( $doc_type . '-' . wp_generate_password( 8, false ) . '.' . $check['ext'] );
		$dest      = trailingslashit( $dir ) . $safe_name;

		if ( ! move_uploaded_file( $file['tmp_name'], $dest ) ) {
			return new WP_Error( 'move_failed', __( 'فشل حفظ الملف', 'rsyi-student-affairs' ) );
		}

		@chmod( $dest, 0644 );

		return array(
			'file_path' => $dest,
			'file_name' => $safe_name,
			'file_size' => (int) $file['size'],
			'mime_type' => $check['type'],
		);
	}

	public static function get_student_dir( int $student_id ): string {
		$uploads = wp_upload_dir();
		return trailingslashit( $uploads['basedir'] )
			. RSYI_UPLOADS_SUBDIR . '/'
			. $student_id;
	}

	public static function secure_url( int $document_id ): string {
		return add_query_arg(
			array(
				'action' => 'rsyi_download_document',
				'id'     => $document_id,
			),
			admin_url( 'admin-post.php' )
		);
	}
}
