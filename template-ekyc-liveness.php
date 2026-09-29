<?php
/**
 * Template Name: وب‌سرویس احراز هویت تصویری (طراحی اختصاصی e-KYC)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی template-civil-registration.php: یک «قالب صفحه» که روی هر
 * برگه‌ای (با هر اسلاگ دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. محتوای ویرایشگر
 * خود برگه استفاده نمی‌شود؛ سکشن‌ها طبق ترتیب/نمایشِ تنظیم‌شده در پیشخوان > تنظیمات
 * قالب > صفحه احراز هویت تصویری رندر می‌شوند.
 *
 * هدر/فوتر/منو/مودال درخواست/دکمه‌های شناور تماس همان‌های مشترک سایت‌اند
 * (header.php / footer.php)؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی ekyc-liveness-page.css / ekyc-liveness-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-ekyc-page">
<?php uid_render_ek_sections(); ?>
<?php uid_render_ek_quick_modal(); ?>
</div>
<?php
get_footer();
