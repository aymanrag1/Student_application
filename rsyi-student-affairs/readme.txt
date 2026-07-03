=== RSYI Student Affairs ===
Contributors: rsyi
Tags: education, student, scholarship, applications, arabic, rtl
Requires at least: 6.4
Tested up to: 6.5
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

نظام إدارة شئون طلاب معهد البحر الأحمر لليخوت - تقديم المنحة، الفلترة، المقابلات، الفحص الطبي، تحديد المستوى.

== Description ==

بلاجن متكامل لإدارة تقديم الطلاب على منحة معهد البحر الأحمر لليخوت.

**الميزات الرئيسية:**

* فورم تقديم على مرحلتين (بيانات مبدئية + رفع أوراق)
* فلترة آلية للاشتراطات (جنسية، سن، ثانوية، تجنيد)
* إشعارات واتساب + إيميل (قبول، رفض، تذكير)
* جدولة مقابلات وكشوف الأمن
* تصويت لجنة موزون (3 أو 4 أعضاء)
* فحص طبي (6 عناصر) واختبار تحديد المستوى
* تصدير Excel ونتائج نهائية
* REST API للربط بسيستم خارجي
* دعم كامل للعربية RTL

== Installation ==

1. ارفع مجلد `rsyi-student-affairs` إلى `/wp-content/plugins/`
2. فعّل البلاجن من قائمة "الإضافات" في لوحة الأدمن
3. شغّل: `composer install` داخل مجلد البلاجن لتنصيب PhpSpreadsheet
4. أضف الشورت كود `[rsyi_application_form]` في أي صفحة

== Changelog ==

= 0.1.0 =
* الإصدار الأولي - Phase 0 & 1: Scaffolding + Database schema

== Upgrade Notice ==

= 0.1.0 =
الإصدار الأولي.
