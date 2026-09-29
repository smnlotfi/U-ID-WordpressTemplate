<?php
/**
 * Template Name: وب سرویس ثبت احوال (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی template-pwa.php: یک «قالب صفحه» که روی هر برگه‌ای
 * (با هر اسلاگ دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. محتوای ویرایشگر خود
 * برگه استفاده نمی‌شود؛ سکشن‌ها طبق ترتیب/نمایشِ تنظیم‌شده در پیشخوان > تنظیمات
 * قالب > صفحه ثبت احوال رندر می‌شوند.
 *
 * هدر/فوتر/منو/مودال درخواست/دکمه‌های شناور تماس همان‌های مشترک سایت‌اند
 * (header.php / footer.php)؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی civil-registration-page.css / civil-registration-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-civil-page">
<?php uid_render_cr_sections(); ?>
<?php uid_render_cr_quick_modal(); ?>
</div>
<?php
get_footer();
