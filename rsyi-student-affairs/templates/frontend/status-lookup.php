<?php
/**
 * Simple status lookup form.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="rsyi-form" method="get">
	<div class="rsyi-form-intro">
		<h2><?php esc_html_e( 'الاستعلام عن حالة طلبك', 'rsyi-student-affairs' ); ?></h2>
	</div>
	<div class="rsyi-field">
		<label for="rsyi_code_lookup"><?php esc_html_e( 'كود التقديم', 'rsyi-student-affairs' ); ?></label>
		<input type="text" id="rsyi_code_lookup" name="code" placeholder="RSYI-2026-00001" required>
	</div>
	<div class="rsyi-form-actions">
		<button type="submit" class="rsyi-btn rsyi-btn-primary"><?php esc_html_e( 'استعلام', 'rsyi-student-affairs' ); ?></button>
	</div>
</form>
