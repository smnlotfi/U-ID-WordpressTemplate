<?php
/**
 * Template Name: وب‌سرویس شاهکار (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/مودال/دکمه‌های شناور
 * تماس همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی shahkar-page.css / shahkar-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-shahkar-page">
<?php uid_render_sk_sections(); ?>
<?php uid_render_sk_quick_modal(); ?>
</div>
<?php
get_footer();
