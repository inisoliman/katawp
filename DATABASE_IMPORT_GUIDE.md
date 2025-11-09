# KataWP - Database Import Guide

## Problem: SQL File Not Being Imported

### الم​شكلة: ملف قاعدة البيانات لا يتم استيراده

When you activate the KataWP plugin, it doesn't automatically import the data from the SQL file in the `data/` folder.

---

## Root Cause Analysis

### المشاكل السابقة:

1. **Single Location Search Only**
   - الكود كان يبحث عن الملف في مكان واحد فقط
   - إذا كان الملف موجوداً في مكان آخر، لم يتم العثور عليه

2. **No Error Logging**
   - لا توجد رسائل خطأ تساعد في تتبع المشكلة
   - لا يتم تسجيل ما إذا تم العثور على الملف أم لا

3. **Silent Failures**
   - الاستيراد يفشل بصمت دون إظهار رسائل خطأ

---

## Solution: Enhanced File Detection

تم تحسين ملف `db-importer.php` للبحث في عدة مواقع:

### Locations Checked (بالترتيب):

```
1. KATAWP_PLUGIN_DIR/data/katamars.sql
2. KATAWP_PLUGIN_DIR/data/katamars.sql0
3. __FILE__/../data/katamars.sql
4. __FILE__/../data/katamars.sql0
5. wp_upload_dir()/katawp/katamars.sql
```

### Improvements Made:

✅ **find_sql_file()** - متعدد المسارات
- يبحث في عدة مواقع
- يدعم نسختي الملف (.sql و .sql0)
- يسجل المكان الذي تم العثور عليه

✅ **Detailed Logging**
- تسجيل رسائل الخطأ في `wp-content/debug.log`
- إظهار أي استعلامات SQL فشلت
- عد الاستعلامات المنفذة والفاشلة

✅ **Better Error Handling**
- التعامل الآمن مع الملفات الكبيرة
- معالجة أخطاء SQL بشكل صحيح

---

## Troubleshooting Guide

### If Import Still Doesn't Work:

#### 1. **Check Debug Log**
```
Navigate to: wp-content/debug.log
```

Look for messages like:
- `KataWP: SQL ملف وجد: /path/to/katamars.sql` ✅ (Found)
- `KataWP: SQL ملف لم يتم العثور عليه` ❌ (Not Found)
- `KataWP: لم قراءة محتويات SQL` ❌ (Cannot Read)

#### 2. **Check File Permissions**
```
The SQL file must be readable:
- File permissions should be 644 or 755
- Owned by web server user (www-data or apache)
```

#### 3. **Check File Location**
```
The SQL file should be in one of these locations:
- /wp-content/plugins/katawp-main/data/katamars.sql
- /wp-content/uploads/katawp/katamars.sql
```

#### 4. **Check Database Permissions**
```
WordPress database user must have:
- CREATE TABLE
- INSERT
- ALTER TABLE
- SELECT
```

#### 5. **Manual Import Alternative**
If automatic import fails, manually import via phpMyAdmin:
```
1. Go to phpMyAdmin
2. Select your WordPress database
3. Click "Import"
4. Choose katamars.sql file
5. Update table names if needed (see TABLE MAPPING below)
```

---

## Table Name Mapping

The importer automatically maps old table names to new ones:

| Old Table | New Table |
|-----------|----------|
| bible_ar | wp_katawp_daily_readings |
| bible_en | wp_katawp_daily_readings |
| gr_days | wp_katawp_synaxarium |
| gr_lent | wp_katawp_synaxarium |
| gr_nineveh | wp_katawp_synaxarium |
| gr_pentecost | wp_katawp_synaxarium |
| gr_sundays | wp_katawp_synaxarium |
| wp_katawp_synaxarium | wp_katawp_synaxarium |

---

## Enabling Debug Logging

Add this to `wp-config.php` to see detailed import logs:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check: `wp-content/debug.log`

---

## Version History

### v2.0 (Enhanced)
✅ Multi-location file detection
✅ Comprehensive error logging
✅ Support for .sql and .sql0 files
✅ Query execution tracking
✅ Better debugging information

### v1.0 (Original)
- Single location search
- Minimal error handling

---

## Support

If import still fails after following this guide:

1. Check the WordPress debug log for specific error messages
2. Verify the SQL file exists and is readable
3. Ensure database user has proper permissions
4. Contact support with the debug log content
