# KataWP - القراءات اليومية المتقدمة

## بنية الملفات وارتباطاتها

### الملف الرئيسي: `katawp.php`
هذا هو نقطة الدخول الرئيسية للملحق. يقوم بـ:
- التحقق من أمان WordPress
- تعريف ثوابت الملحق الأساسية
- **ربط وتحميل جميع الملفات الأخرى** باستخدام require_once
- دعم Singleton Pattern للفئة الرئيسية KataWP

### الملفات المرتبطة بالملف الرئيسي:

#### ملفات الأساس (includes/)

1. **class-database.php**
   - إدارة قاعدة البيانات
   - إنشاء الجداول
   - متطلب من activation.php

2. **db-importer.php**
   - استيراد البيانات من ملف SQL
   - يتم بدءه عند تفعيل الملحق

3. **activation.php** [NEW]
   - ربط activation hooks
   - إنشاء الصفحات الأوتوماتيكية
   - مربوط ب_activate() في katawp.php

4. **cache.php** [NEW]
   - نظام التخزين المؤقت
   - Cache groups للقراءات
   - مربوط ب_activate() لتهيئة Cache

5. **seo.php** [NEW]
   - تحسين محركات البحث
   - Meta tags وStructured data
   - XML Sitemap
   - ربط wp_head hooks

6. **api-handlers.php** [NEW]
   - معالجات AJAX
   - معالجات REST API
   - ربط wp_ajax hooks

7. **frontend.php** [NEW]
   - شوروت enqueue CSS/JS
   - وراع عرض البيانات
   - ربط wp_enqueue_scripts

8. **functions.php**
   - الدوال المساعدة

9. **shortcode.php**
   - نظام الشورتكود

10. **widgets.php**
    - الويدجتات

11. **rest-api.php**
    - REST API endpoints

#### ملفات الإدارة (admin/)

1. **admin-panel.php**
   - لوحة العناصر الأساسية

2. **settings.php** [NEW]
   - مربوط بadmin_menu hooks
   - ربط admin_init hooks
   - صفحة الإعدادات

## للحظول على مراجعة كاملة للارتباطات:

1. فتح katawp.php
2. ابحث عن require_once
3. لاحظ عند ربط كل الملفات بالطريقة الصحيحة
