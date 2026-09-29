<?php
/**
 * Template Name: فهرست وب‌سرویس‌های احراز هویت (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/مودال/دکمه‌های شناور
 * تماس همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی web-services-hub-page.css / web-services-hub-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-web-services-hub-page">
<?php uid_render_wsh_sections(); ?>
<?php uid_render_wsh_quick_modal(); ?>
</div>
<?php
get_footer();
