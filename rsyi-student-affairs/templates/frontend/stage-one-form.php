<?php
/**
 * Stage 1 application form template.
 *
 * @package RSYI\StudentAffairs
 */

defined( 'ABSPATH' ) || exit;
?>
<form
	class="rsyi-form rsyi-stage-one-form"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	novalidate
>
	<?php wp_nonce_field( RSYI_Stage_One_Handler::ACTION_NAME, RSYI_Stage_One_Handler::NONCE_NAME ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( RSYI_Stage_One_Handler::ACTION_NAME ); ?>">

	<div class="rsyi-form-intro">
		<h2><?php esc_html_e( 'طلب التقديم على منحة معهد البحر الأحمر لليخوت', 'rsyi-student-affairs' ); ?></h2>
		<p><?php esc_html_e( 'ادخل بياناتك المبدئية. لو استوفيت الشروط هيتم توجيهك لرفع الأوراق.', 'rsyi-student-affairs' ); ?></p>
	</div>

	<fieldset class="rsyi-section">
		<legend><?php esc_html_e( 'القسم الأول: بيانات شخصية', 'rsyi-student-affairs' ); ?></legend>

		<div class="rsyi-field">
			<label for="rsyi_full_name"><?php esc_html_e( 'الاسم بالكامل', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="text" id="rsyi_full_name" name="full_name" required>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_national_id"><?php esc_html_e( 'الرقم القومي', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="text" id="rsyi_national_id" name="national_id" pattern="[0-9]{14}" maxlength="14" required>
			<small><?php esc_html_e( '14 رقم', 'rsyi-student-affairs' ); ?></small>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_dob"><?php esc_html_e( 'تاريخ الميلاد', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="date" id="rsyi_dob" name="date_of_birth" required>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_email"><?php esc_html_e( 'البريد الإلكتروني', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="email" id="rsyi_email" name="email" required>
		</div>
	</fieldset>

	<fieldset class="rsyi-section">
		<legend><?php esc_html_e( 'القسم الثاني: بيانات المراسلات', 'rsyi-student-affairs' ); ?></legend>

		<div class="rsyi-field">
			<label for="rsyi_address"><?php esc_html_e( 'العنوان', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<textarea id="rsyi_address" name="address" rows="2" required></textarea>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_mobile_1"><?php esc_html_e( 'رقم موبايل 1', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="tel" id="rsyi_mobile_1" name="mobile_1" required>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_mobile_2"><?php esc_html_e( 'رقم موبايل 2', 'rsyi-student-affairs' ); ?></label>
			<input type="tel" id="rsyi_mobile_2" name="mobile_2">
		</div>

		<div class="rsyi-field">
			<label for="rsyi_whatsapp"><?php esc_html_e( 'رقم الواتساب', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="tel" id="rsyi_whatsapp" name="whatsapp" required>
		</div>
	</fieldset>

	<fieldset class="rsyi-section">
		<legend><?php esc_html_e( 'القسم الثالث: بيانات عامة', 'rsyi-student-affairs' ); ?></legend>

		<div class="rsyi-field">
			<label for="rsyi_job"><?php esc_html_e( 'الوظيفة الحالية', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<input type="text" id="rsyi_job" name="job" required>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_nationality"><?php esc_html_e( 'الجنسية', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_nationality" name="nationality" required>
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="egyptian"><?php esc_html_e( 'مصري', 'rsyi-student-affairs' ); ?></option>
				<option value="other"><?php esc_html_e( 'أخرى', 'rsyi-student-affairs' ); ?></option>
			</select>
			<small><?php esc_html_e( 'المنحة مخصصة للمصريين فقط.', 'rsyi-student-affairs' ); ?></small>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_reason"><?php esc_html_e( 'سبب التقدم للمنحة', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<textarea id="rsyi_reason" name="reason_for_applying" rows="3" required></textarea>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_marital"><?php esc_html_e( 'الحالة الاجتماعية', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_marital" name="marital_status" required>
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="single"><?php esc_html_e( 'أعزب', 'rsyi-student-affairs' ); ?></option>
				<option value="married"><?php esc_html_e( 'متزوج', 'rsyi-student-affairs' ); ?></option>
			</select>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_gender"><?php esc_html_e( 'الجنس', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_gender" name="gender" required>
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="male"><?php esc_html_e( 'ذكر', 'rsyi-student-affairs' ); ?></option>
				<option value="female"><?php esc_html_e( 'أنثى', 'rsyi-student-affairs' ); ?></option>
			</select>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_height"><?php esc_html_e( 'الطول (سم)', 'rsyi-student-affairs' ); ?></label>
			<input type="number" step="0.1" min="120" max="230" id="rsyi_height" name="height">
		</div>

		<div class="rsyi-field">
			<label for="rsyi_weight"><?php esc_html_e( 'الوزن (كجم)', 'rsyi-student-affairs' ); ?></label>
			<input type="number" step="0.1" min="30" max="200" id="rsyi_weight" name="weight">
		</div>
	</fieldset>

	<fieldset class="rsyi-section">
		<legend><?php esc_html_e( 'القسم الرابع: بيانات الدراسة والتجنيد', 'rsyi-student-affairs' ); ?></legend>

		<div class="rsyi-field">
			<label for="rsyi_hs_type"><?php esc_html_e( 'نوع الثانوية', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_hs_type" name="high_school_type" required>
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="general_egyptian"><?php esc_html_e( 'ثانوية عامة', 'rsyi-student-affairs' ); ?></option>
				<option value="azhari"><?php esc_html_e( 'ثانوية أزهرية', 'rsyi-student-affairs' ); ?></option>
				<option value="american_diploma"><?php esc_html_e( 'دبلومة أمريكية', 'rsyi-student-affairs' ); ?></option>
				<option value="british_igcse"><?php esc_html_e( 'IGCSE بريطانية', 'rsyi-student-affairs' ); ?></option>
				<option value="ib"><?php esc_html_e( 'IB', 'rsyi-student-affairs' ); ?></option>
				<option value="french"><?php esc_html_e( 'ثانوية فرنسية', 'rsyi-student-affairs' ); ?></option>
				<option value="german"><?php esc_html_e( 'ثانوية ألمانية', 'rsyi-student-affairs' ); ?></option>
				<option value="commercial"><?php esc_html_e( 'ثانوية تجارية', 'rsyi-student-affairs' ); ?></option>
				<option value="industrial"><?php esc_html_e( 'ثانوية صناعية', 'rsyi-student-affairs' ); ?></option>
				<option value="agricultural"><?php esc_html_e( 'ثانوية زراعية', 'rsyi-student-affairs' ); ?></option>
				<option value="hotel"><?php esc_html_e( 'ثانوية فندقية', 'rsyi-student-affairs' ); ?></option>
				<option value="other"><?php esc_html_e( 'أخرى', 'rsyi-student-affairs' ); ?></option>
			</select>
		</div>

		<div class="rsyi-field" data-track-container>
			<label for="rsyi_hs_track"><?php esc_html_e( 'الشعبة', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_hs_track" name="high_school_track">
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="scientific"><?php esc_html_e( 'علمي', 'rsyi-student-affairs' ); ?></option>
				<option value="literary"><?php esc_html_e( 'أدبي', 'rsyi-student-affairs' ); ?></option>
			</select>
		</div>

		<div class="rsyi-field">
			<label for="rsyi_last_qual"><?php esc_html_e( 'آخر مؤهل حصلت عليه', 'rsyi-student-affairs' ); ?></label>
			<input type="text" id="rsyi_last_qual" name="last_qualification">
		</div>

		<div class="rsyi-field">
			<label for="rsyi_military"><?php esc_html_e( 'موقفك من الخدمة العسكرية', 'rsyi-student-affairs' ); ?> <span class="req">*</span></label>
			<select id="rsyi_military" name="military_status" required>
				<option value=""><?php esc_html_e( '-- اختر --', 'rsyi-student-affairs' ); ?></option>
				<option value="completed"><?php esc_html_e( 'أدى الخدمة العسكرية', 'rsyi-student-affairs' ); ?></option>
				<option value="exempted"><?php esc_html_e( 'معفى نهائياً', 'rsyi-student-affairs' ); ?></option>
				<option value="postponed"><?php esc_html_e( 'تأجيل', 'rsyi-student-affairs' ); ?></option>
				<option value="in_service_age"><?php esc_html_e( 'في سن التجنيد', 'rsyi-student-affairs' ); ?></option>
				<option value="study_postponement"><?php esc_html_e( 'تأجيل بسبب الدراسة', 'rsyi-student-affairs' ); ?></option>
			</select>
		</div>
	</fieldset>

	<div class="rsyi-form-actions">
		<button type="submit" class="rsyi-btn rsyi-btn-primary">
			<?php esc_html_e( 'إرسال الطلب', 'rsyi-student-affairs' ); ?>
		</button>
	</div>
</form>
