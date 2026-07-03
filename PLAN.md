# خطة تنفيذ بلاجن ووردبريس — شئون طلاب معهد البحر الأحمر لليخوت

> **Red Sea Yacht Institute (RSYI) — Student Affairs WordPress Plugin**
> نظام متكامل لإدارة تقديم الطلاب على المنحة، من التقديم الأولي حتى القبول النهائي.

---

## 📋 نظرة عامة

بلاجن ووردبريس متكامل يدير دورة حياة الطالب المتقدم للمنحة كاملةً:

1. **تقديم أونلاين** على مرحلتين (بيانات مبدئية + رفع أوراق)
2. **فلترة آلية** للاشتراطات (جنسية / سن / ثانوية / تجنيد)
3. **إشعارات تلقائية** واتساب + إيميل (قبول / رفض / تذكير)
4. **جدولة مقابلات** + كشوف أمن الجونة
5. **تصويت اللجنة** بنسب مرجّحة (3 أو 4 أعضاء)
6. **فحص طبي** (6 عناصر) + اختبار تحديد المستوى
7. **تصدير Excel** ونتائج نهائية
8. **REST API** للربط بسيستم خارجي

---

## 🎯 الأهداف

| # | الهدف | مقياس النجاح |
|---|-------|--------------|
| 1 | أتمتة الفلترة | 0% تدخل يدوي في المرحلة 1 |
| 2 | تتبّع كل حالة طالب | State machine واضح 15+ حالة |
| 3 | تذكير تلقائي بالأوراق | Cron كل 4 أيام حتى الرفع |
| 4 | تصويت لجنة موزون | نسب صحيحة 3/4 أعضاء |
| 5 | تكامل واتساب | Driver قابل للتبديل |
| 6 | تصدير موثوق | Excel + كشف أمن |
| 7 | ربط خارجي | REST API مؤمّن بتوكن |
| 8 | دعم عربي / RTL | كامل من أول يوم |

---

## 🛠️ التقنيات (Tech Stack)

| المكوّن | التقنية |
|---------|---------|
| Runtime | PHP 8.1+ |
| CMS | WordPress 6.4+ |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Frontend | Vanilla JS + WordPress Core CSS |
| RTL | CSS مستقل + Bootstrap 5 RTL |
| Excel | PhpSpreadsheet (via Composer) |
| WhatsApp | Driver pattern: Log / UltraMsg / Twilio / Meta Cloud API |
| Email | wp_mail() + قوالب HTML |
| Cron | WP-Cron |
| REST API | WordPress REST API |

---

## 📁 هيكل الملفات

```
rsyi-student-affairs/
├── rsyi-student-affairs.php          # الملف الرئيسي (Plugin Header)
├── uninstall.php                     # حذف كامل عند إلغاء التثبيت
├── readme.txt                        # ملف WordPress readme
├── composer.json                     # PhpSpreadsheet وغيره
├── PLAN.md                           # الخطة الشاملة
├── CHANGELOG.md                      # سجل التعديلات
│
├── languages/
│   ├── rsyi-student-affairs.pot
│   ├── rsyi-student-affairs-ar.po
│   └── rsyi-student-affairs-ar.mo
│
├── includes/
│   ├── class-plugin.php              # Singleton bootstrap
│   ├── class-installer.php           # dbDelta لجداول DB
│   ├── class-activator.php
│   ├── class-deactivator.php
│   ├── class-roles.php               # Roles & capabilities
│   ├── class-autoloader.php
│   │
│   ├── models/
│   │   ├── class-student.php
│   │   ├── class-document.php
│   │   ├── class-status-log.php
│   │   ├── class-interview.php
│   │   ├── class-committee-vote.php
│   │   ├── class-medical-exam.php
│   │   ├── class-language-test.php
│   │   └── class-notification.php
│   │
│   ├── services/
│   │   ├── class-eligibility-service.php     # فلترة الشروط
│   │   ├── class-status-service.php          # State machine
│   │   ├── class-notification-service.php    # إرسال الرسائل
│   │   ├── class-committee-service.php       # حساب نسب اللجنة
│   │   ├── class-export-service.php          # Excel + كشوف
│   │   └── class-upload-service.php          # رفع ملفات آمن
│   │
│   ├── whatsapp/
│   │   ├── interface-whatsapp-driver.php
│   │   ├── class-whatsapp-manager.php        # Factory
│   │   ├── class-log-driver.php              # الافتراضي (يسجّل في DB بس)
│   │   ├── class-ultramsg-driver.php
│   │   ├── class-twilio-driver.php
│   │   └── class-meta-cloud-driver.php
│   │
│   ├── frontend/
│   │   ├── class-shortcodes.php              # [rsyi_application_form]
│   │   ├── class-stage-one-handler.php
│   │   ├── class-stage-two-handler.php
│   │   └── class-status-check-handler.php    # الطالب يعرف حالته
│   │
│   ├── admin/
│   │   ├── class-admin.php                   # Menu + init
│   │   ├── class-candidates-list-table.php   # WP_List_Table
│   │   ├── class-candidate-detail.php
│   │   ├── class-interview-scheduler.php
│   │   ├── class-committee-page.php
│   │   ├── class-medical-page.php
│   │   ├── class-language-page.php
│   │   ├── class-settings-page.php
│   │   ├── class-reports-page.php
│   │   └── class-committee-member-view.php   # شاشة عضو اللجنة
│   │
│   ├── api/
│   │   ├── class-rest-controller.php         # Base
│   │   ├── class-students-api.php
│   │   ├── class-status-api.php
│   │   └── class-webhook-api.php
│   │
│   └── cron/
│       └── class-reminder-cron.php           # كل 4 أيام
│
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   ├── frontend-rtl.css
│   │   ├── admin.css
│   │   └── admin-rtl.css
│   ├── js/
│   │   ├── frontend-stage-one.js
│   │   ├── frontend-stage-two.js
│   │   ├── admin-candidates.js
│   │   ├── admin-committee.js
│   │   └── admin-scheduler.js
│   └── images/
│       └── logo.png
│
└── templates/
    ├── frontend/
    │   ├── stage-one-form.php
    │   ├── stage-two-form.php
    │   ├── rejected.php
    │   ├── under-review.php
    │   └── success.php
    ├── admin/
    │   ├── candidates-list.php
    │   ├── candidate-detail.php
    │   ├── interview-schedule.php
    │   ├── committee-vote.php
    │   ├── medical-form.php
    │   ├── language-form.php
    │   ├── settings.php
    │   └── reports.php
    └── emails/
        ├── header.php
        ├── footer.php
        ├── application-received.php
        ├── stage-one-rejected.php
        ├── documents-pending-reminder.php
        ├── documents-received.php
        ├── interview-scheduled.php
        ├── medical-scheduled.php
        ├── final-accepted.php
        └── final-rejected.php
```

---

## 🗄️ قاعدة البيانات (Database Schema)

كل الجداول ببادئة `{$wpdb->prefix}rsyi_` (مثلاً `wp_rsyi_students`).

### 1. `rsyi_students` — الجدول الرئيسي للطلاب

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `application_code` | VARCHAR(20) UNIQUE | كود تعريفي مثل `RSYI-2026-00001` |
| `full_name` | VARCHAR(255) | |
| `national_id` | VARCHAR(20) UNIQUE | الرقم القومي (14 رقم) |
| `date_of_birth` | DATE | |
| `email` | VARCHAR(255) | |
| `address` | TEXT | |
| `mobile_1` | VARCHAR(20) | |
| `mobile_2` | VARCHAR(20) NULL | |
| `whatsapp` | VARCHAR(20) | |
| `job` | VARCHAR(255) | |
| `nationality` | VARCHAR(100) | |
| `reason_for_applying` | TEXT | |
| `marital_status` | ENUM('single','married') | |
| `gender` | ENUM('male','female') | |
| `height` | DECIMAL(5,2) NULL | بالسم |
| `weight` | DECIMAL(5,2) NULL | بالكجم |
| `high_school_type` | VARCHAR(100) | |
| `high_school_track` | ENUM('scientific','literary') | |
| `last_qualification` | VARCHAR(255) | |
| `military_status` | ENUM('completed','exempted','postponed','in_service_age','study_postponement') | |
| `status` | VARCHAR(50) | الحالة الحالية (انظر State Machine) |
| `stage_one_submitted_at` | DATETIME | |
| `stage_two_submitted_at` | DATETIME NULL | |
| `reminder_stopped` | TINYINT(1) DEFAULT 0 | إيقاف يدوي للتذكير |
| `last_reminder_sent_at` | DATETIME NULL | |
| `created_at` | DATETIME | |
| `updated_at` | DATETIME | |

**Indexes:** `national_id`, `email`, `status`, `application_code`

### 2. `rsyi_documents` — أوراق الطالب

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK | |
| `document_type` | ENUM('national_id_front','national_id_back','birth_certificate','high_school_certificate','last_qualification_certificate','military_status_certificate') | |
| `file_path` | VARCHAR(500) | Relative to `wp-content/uploads/rsyi-docs/` |
| `file_name` | VARCHAR(255) | |
| `file_size` | INT | بالبايت |
| `mime_type` | VARCHAR(100) | |
| `uploaded_at` | DATETIME | |

**Index:** `student_id`, `document_type`

### 3. `rsyi_status_log` — سجل تغيّرات الحالة

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK | |
| `from_status` | VARCHAR(50) NULL | |
| `to_status` | VARCHAR(50) | |
| `changed_by` | BIGINT FK wp_users NULL | NULL لو النظام |
| `reason` | TEXT NULL | |
| `changed_at` | DATETIME | |

### 4. `rsyi_interviews` — مواعيد المقابلات

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK UNIQUE | |
| `interview_date` | DATETIME | |
| `location` | VARCHAR(255) | افتراضي: الجونة |
| `attended` | TINYINT(1) DEFAULT NULL | NULL = مش معروف، 1 = حضر، 0 = غاب |
| `committee_size` | TINYINT | 3 أو 4 |
| `total_acceptance_score` | DECIMAL(5,2) NULL | مجموع نسب القبول |
| `total_waiting_score` | DECIMAL(5,2) NULL | |
| `total_rejection_score` | DECIMAL(5,2) NULL | |
| `final_decision` | ENUM('accepted','waiting','rejected') NULL | |
| `notes` | TEXT NULL | |
| `scheduled_by` | BIGINT FK wp_users | |
| `created_at` | DATETIME | |

### 5. `rsyi_committee_votes` — تصويت أعضاء اللجنة

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `interview_id` | BIGINT FK | |
| `member_user_id` | BIGINT FK wp_users | |
| `member_number` | TINYINT | 1..4 |
| `decision` | ENUM('accepted','waiting','rejected') | |
| `weight_percentage` | DECIMAL(5,2) | النسبة المستحقّة حسب القرار |
| `comment` | TEXT NULL | |
| `voted_at` | DATETIME | |

**Unique:** `(interview_id, member_user_id)`

### 6. `rsyi_medical_exams` — الفحص الطبي

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK UNIQUE | |
| `exam_date` | DATE | |
| `internal_exam` | ENUM('fit','unfit') NULL | باطنة |
| `internal_comment` | TEXT NULL | |
| `chest_exam` | ENUM('fit','unfit') NULL | صدر |
| `chest_comment` | TEXT NULL | |
| `eye_exam` | ENUM('fit','unfit') NULL | رمد |
| `eye_comment` | TEXT NULL | |
| `toxicology_exam` | ENUM('fit','unfit') NULL | سموم |
| `toxicology_comment` | TEXT NULL | |
| `virology_exam` | ENUM('fit','unfit') NULL | فيروسات |
| `virology_comment` | TEXT NULL | |
| `blood_exam` | ENUM('fit','unfit') NULL | صورة دم |
| `blood_comment` | TEXT NULL | |
| `overall_result` | ENUM('fit','unfit') NULL | Auto-calculated |
| `recorded_by` | BIGINT FK wp_users | |
| `created_at` | DATETIME | |

### 7. `rsyi_language_tests` — اختبار تحديد المستوى

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK UNIQUE | |
| `test_date` | DATE | |
| `level` | ENUM('beginner','elementary','pre_intermediate','intermediate','upper_intermediate') | |
| `passed` | TINYINT(1) | 1 لو `elementary` أو أعلى |
| `notes` | TEXT NULL | |
| `recorded_by` | BIGINT FK wp_users | |
| `created_at` | DATETIME | |

### 8. `rsyi_notifications` — سجل الإشعارات المرسلة

| الحقل | النوع | ملاحظات |
|-------|------|---------|
| `id` | BIGINT PK AI | |
| `student_id` | BIGINT FK | |
| `channel` | ENUM('whatsapp','email') | |
| `type` | VARCHAR(100) | مثل `stage_one_rejected` |
| `recipient` | VARCHAR(255) | رقم/إيميل |
| `subject` | VARCHAR(255) NULL | للإيميل |
| `body` | TEXT | نص الرسالة |
| `status` | ENUM('pending','sent','failed') | |
| `provider_response` | TEXT NULL | JSON من مزود الخدمة |
| `error_message` | TEXT NULL | |
| `sent_at` | DATETIME NULL | |
| `created_at` | DATETIME | |

---

## 🔄 State Machine — دورة حياة الطالب

```
[Start]
   ↓
submitted_stage_1
   ├─→ rejected_stage_1        (فشل الفلترة الآلية) → [END]
   └─→ pending_documents
          ├─→ (كل 4 أيام تذكير)
          └─→ documents_uploaded
                 ↓
              interview_scheduled
                 ↓
              interview_attended
                 ├─→ interview_rejected     → [END]
                 ├─→ interview_waiting
                 └─→ interview_accepted
                        ↓
                     medical_scheduled
                        ↓
                     medical_completed
                        ├─→ medical_failed  → [END]
                        └─→ medical_passed
                               ↓
                            language_tested
                               ├─→ final_rejected  (< elementary) → [END]
                               └─→ final_accepted  → [END]
```

**كل انتقال بيتسجّل في `rsyi_status_log`.**

---

## ✅ شروط المرحلة 1 (Eligibility Rules)

```
✔ الجنسية: مصري فقط
✔ السن: 21 ≤ age ≤ 29 (محسوبة من date_of_birth)
✔ الجنس: ذكر فقط (حالياً)
✔ نوع الثانوية:
    - "ثانوية عامة" AND track = scientific  ← مقبول
    - "ثانوية أزهرية" AND track = scientific  ← مقبول
    - "دبلومة أمريكية" / "دبلومة بريطانية" / شهادة أجنبية معتمدة  ← مقبول
    - أي نوع تاني (تجارية / فنية / زراعية...)  ← مرفوض
✔ موقف التجنيد:
    - completed / exempted / postponed  ← مقبول
    - in_service_age / study_postponement  ← مرفوض
```

**Rejection Reasons Codes:**
- `RJ_NATIONALITY` — جنسية غير مصرية
- `RJ_AGE_LOW` — أقل من 21
- `RJ_AGE_HIGH` — أكبر من 29
- `RJ_GENDER` — أنثى
- `RJ_HIGH_SCHOOL_TYPE` — نوع ثانوية غير مقبول
- `RJ_HIGH_SCHOOL_TRACK` — شعبة أدبي
- `RJ_MILITARY_STATUS` — موقف تجنيد غير واضح

---

## 🧮 حساب نسب اللجنة

### حالة 4 أعضاء

| العضو | قبول | انتظار | رفض |
|-------|------|--------|-----|
| عضو 1 | 25% | 15% | 0% |
| عضو 2 | 25% | 15% | 0% |
| عضو 3 | 25% | 15% | 0% |
| عضو 4 | 25% | 15% | 0% |
| **الحد الأقصى** | **100%** | **60%** | **0%** |

### حالة 3 أعضاء

| العضو | قبول | انتظار | رفض |
|-------|------|--------|-----|
| عضو 1 | 40% | 20% | 0% |
| عضو 2 | 30% | 15% | 0% |
| عضو 3 | 30% | 15% | 0% |
| **الحد الأقصى** | **100%** | **50%** | **0%** |

### قاعدة القرار النهائي (Configurable من الإعدادات)

- **≥ 60% قبول** → `accepted`
- **≥ 30% قبول + انتظار** → `waiting`
- **باقي الحالات** → `rejected`

---

## 📱 معمارية خدمة الواتساب (Driver Pattern)

```php
interface WhatsApp_Driver {
    public function send( string $to, string $message ): array;
    public function get_name(): string;
}
```

**Drivers متاحة:**
| Driver | الاستخدام |
|--------|-----------|
| `LogDriver` | افتراضي — يسجّل في DB فقط بدون إرسال (للتطوير) |
| `UltraMsgDriver` | UltraMsg.com API |
| `TwilioDriver` | Twilio WhatsApp Business |
| `MetaCloudDriver` | WhatsApp Cloud API الرسمي من Meta |

**اختيار الـ Driver من إعدادات البلاجن** — بدون تعديل أي كود.

---

## 📧 قوالب الإيميلات

| النوع | التوقيت | المحتوى |
|-------|---------|---------|
| `application_received` | فور التقديم | تأكيد الاستلام + كود التقديم |
| `stage_one_rejected` | بعد فشل الفلترة | سبب الرفض |
| `documents_pending_reminder` | Cron كل 4 أيام | تذكير برفع الأوراق |
| `documents_received` | فور اكتمال الأوراق | تأكيد الترشيح |
| `interview_scheduled` | عند تحديد الموعد | تاريخ + مكان المقابلة |
| `medical_scheduled` | بعد قبول اللجنة | موعد الفحص الطبي |
| `final_accepted` | بعد اكتمال كل المراحل | تهنئة + الخطوات التالية |
| `final_rejected` | عند رفض في أي مرحلة | إشعار الرفض |

كل قالب: نسخة **HTML** (RTL) + نسخة **plain text** للواتساب.

---

## 👥 Roles & Capabilities

### `rsyi_admin` — مدير النظام
- كل الصلاحيات
- إدارة المتقدمين
- تحديد مواعيد المقابلات
- إدخال الفحوصات الطبية واختبار اللغة
- إدارة الإعدادات
- تصدير التقارير

### `rsyi_committee` — عضو لجنة
- شاشة تصويت خاصة (يشوف بس الطلاب اللي عنده مقابلة معاهم)
- يقدر يدخّل قراره + تعليق
- ما يقدرش يشوف قرارات باقي الأعضاء قبل التصويت (يمنع التأثر)

### `rsyi_security` — الأمن
- يشوف كشف الأمن يوم قبل المقابلة (اسم، رقم قومي، تليفون)
- تصدير الكشف PDF/CSV

### `rsyi_medical` — الطبيب
- يدخّل نتائج الفحص الطبي فقط

---

## 🌐 REST API — للربط بسيستم خارجي

**Base URL:** `/wp-json/rsyi/v1/`

**Auth:** Bearer token (يتولّد من إعدادات البلاجن)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/students` | قائمة الطلاب (مع فلاتر) |
| GET | `/students/{id}` | تفاصيل طالب |
| GET | `/students/{id}/documents` | أوراق الطالب |
| GET | `/students?status=final_accepted` | المقبولين نهائياً (للتصدير للسيستم الخارجي) |
| POST | `/webhooks/status-changed` | يبعت webhook عند تغيّر حالة |
| GET | `/statistics` | إحصائيات عامة |

---

## 🔐 Security Checklist

- ✅ Nonces على كل form submission
- ✅ `current_user_can()` على كل admin action
- ✅ `sanitize_text_field()` / `sanitize_email()` / `wp_kses_post()` لكل input
- ✅ `esc_html()` / `esc_attr()` / `esc_url()` في كل output
- ✅ Prepared statements فقط (`$wpdb->prepare`)
- ✅ File upload validation (mime + size + extension whitelist)
- ✅ رفع الملفات في `wp-content/uploads/rsyi-docs/` مع `.htaccess` يمنع الوصول المباشر
- ✅ Rate limiting على forms الفرونت اند
- ✅ CSRF protection
- ✅ SQL injection protection
- ✅ XSS protection
- ✅ REST API token authentication
- ✅ Log كل عمليات الأدمن (audit trail)

---

## 🌍 Localization

- ملف `.pot` رئيسي: `languages/rsyi-student-affairs.pot`
- ترجمة عربية كاملة: `rsyi-student-affairs-ar.po/.mo`
- RTL CSS تلقائي عند تفعيل locale عربي
- كل النصوص لازم تكون داخل `__()` أو `_e()`

---

## 🧪 استراتيجية الاختبار

### Manual QA Scenarios

1. **Happy path كامل:** تقديم صحيح → قبول نهائي
2. **رفض المرحلة 1:** جنسية أجنبية / سن غلط / ثانوي أدبي
3. **تذكير الأوراق:** فورس Cron 3 مرات
4. **تصويت لجنة 4 أعضاء:** كل السيناريوهات
5. **تصويت لجنة 3 أعضاء:** كل السيناريوهات
6. **فحص طبي:** فشل عنصر واحد → غير لائق
7. **اختبار لغة:** Beginner → رفض
8. **تصدير Excel:** التحقق من صحة البيانات
9. **REST API:** كل الـ endpoints مع/بدون auth

### Edge Cases

- طالب يقدّم مرتين بنفس الرقم القومي → رفض
- ملف رفع أكبر من الحد → رسالة خطأ واضحة
- ملف بامتداد غير مسموح → رفض
- عضو لجنة يصوّت مرتين → تحديث بدل تكرار

---

## 📅 مسار التنفيذ (Phases)

راجع قائمة المهام في TaskList للـ 20 phase.

**ترتيب مقترح للتنفيذ:**

| مرحلة | المهام | Deliverable |
|-------|--------|-------------|
| **Foundation** | Phase 0, 1, 16, 17 | بلاجن قابل للتفعيل + DB + Roles + عربي |
| **Application** | Phase 2, 3, 4 | تقديم الطالب كامل بمرحلتيه |
| **Admin Core** | Phase 5, 6 | لوحة أدمن أساسية |
| **Interview** | Phase 7, 8 | نظام المقابلات كامل |
| **Medical & Language** | Phase 9, 10 | فحص طبي + اختبار لغة |
| **Notifications** | Phase 11, 12, 13 | واتساب + إيميل + Cron |
| **Exports & API** | Phase 14, 15 | Excel + REST |
| **Polish** | Phase 18, 19, 20 | Security + Testing + Packaging |

---

## 🚀 التسليم النهائي

### ما يستلمه المستخدم

1. **بلاجن zip جاهز للتثبيت** على WordPress
2. **دليل تثبيت** بالعربي (Installation guide)
3. **دليل استخدام** للأدمن + عضو اللجنة + الطبيب
4. **REST API docs** للسيستم الخارجي
5. **Test data seeder** لتجربة سريعة
6. **CHANGELOG** بكل التعديلات

### متطلبات السيرفر

- PHP 8.1+
- MySQL 5.7+ أو MariaDB 10.3+
- WordPress 6.4+
- Extensions: `mbstring`, `xml`, `zip`, `gd`, `curl`, `openssl`
- Composer (لتنصيب PhpSpreadsheet)
- SSL certificate (مطلوب لواتساب API + REST API)

---

## 📝 ملاحظات مهمة

- كل commit على branch `claude/arabic-greeting-qbfi8`
- كل phase تخلص → commit مستقل + push
- الكود يتبع WordPress Coding Standards
- كل ملف PHP يبدأ بـ `defined( 'ABSPATH' ) || exit;`
- استخدام Prefix `rsyi_` لكل الدوال والـ hooks
- استخدام Namespace `RSYI\` للكلاسات المستقبلية

---

**آخر تحديث:** يحدّث تلقائياً مع كل commit
