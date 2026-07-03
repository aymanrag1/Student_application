# تثبيت وإعداد البلاجن

## المتطلبات

- WordPress 6.4 أو أحدث
- PHP 8.1 أو أحدث
- MySQL 5.7+ أو MariaDB 10.3+
- SSL certificate (للربط بـ WhatsApp API + REST API)

## خطوات التثبيت

### 1. تحميل البلاجن

انسخ مجلد `rsyi-student-affairs` إلى:
```
/wp-content/plugins/
```

أو ارفعه كملف zip من: **الإضافات → إضافة جديدة → رفع الإضافة**

### 2. تفعيل البلاجن

من قائمة الإضافات في لوحة الأدمن اضغط "تفعيل" على "RSYI Student Affairs".

عند التفعيل يتم:
- إنشاء 8 جداول في قاعدة البيانات
- إنشاء مجلد `wp-content/uploads/rsyi-docs/` (محمي بـ `.htaccess`)
- تسجيل الأدوار المخصصة (rsyi_admin, rsyi_committee, rsyi_medical, rsyi_security)
- جدولة مهمة WP-Cron اليومية للتذكير

### 3. إضافة فورم التقديم

أنشئ صفحة جديدة "التقديم على المنحة" وضع فيها الشورت كود:

```
[rsyi_application_form]
```

للاستعلام عن حالة الطلب أنشئ صفحة أخرى:

```
[rsyi_application_status]
```

### 4. إعداد الواتساب (اختياري)

في **wp-config.php** أو من قاعدة البيانات (`wp_options`) اضبط:

```sql
-- استخدام UltraMsg
UPDATE wp_options SET option_value='ultramsg' WHERE option_name='rsyi_whatsapp_driver';
UPDATE wp_options SET option_value='a:2:{s:11:"instance_id";s:10:"instance99";s:5:"token";s:20:"YOUR_ULTRAMSG_TOKEN";}'
WHERE option_name='rsyi_whatsapp_config';

-- أو Meta Cloud API الرسمي
UPDATE wp_options SET option_value='meta_cloud' WHERE option_name='rsyi_whatsapp_driver';
```

الخيار الافتراضي `log` بيسجل الرسائل في `error_log` بدون إرسال فعلي.

### 5. توليد REST API Token

```sql
UPDATE wp_options SET option_value='YOUR_SECRET_TOKEN_HERE' WHERE option_name='rsyi_api_token';
```

## استخدام REST API

```bash
curl -H "Authorization: Bearer YOUR_SECRET_TOKEN_HERE" \
  https://yoursite.com/wp-json/rsyi/v1/students?status=final_accepted
```

## الأدوار والمستخدمين

بعد التثبيت أنشئ مستخدمين وامنحهم الأدوار المناسبة:

| الدور | من يستخدمه |
|-------|-----------|
| `rsyi_admin` | مدير شئون الطلاب |
| `rsyi_committee` | أعضاء لجنة المقابلة |
| `rsyi_medical` | طبيب المعهد |
| `rsyi_security` | الأمن |

## التحقق من الـ Cron

في صفحة **الأدوات → Cron Events** لازم تشوف:
- `rsyi_send_document_reminders` بيشتغل يومياً

## استكشاف الأخطاء

- **لا يتم إرسال رسائل الواتساب:** تأكد إن `rsyi_whatsapp_driver` مضبوط على `ultramsg` أو `meta_cloud` (مش `log`) وإن الـ config صحيح
- **الفورم مش شغال:** تأكد إن الشورت كود صحيح `[rsyi_application_form]`
- **الأوراق مش برفع:** تأكد إن مجلد `wp-content/uploads/rsyi-docs/` موجود ومسموح بالكتابة
