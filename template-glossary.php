<?php
/**
 * Template Name: واژه‌نامه اصطلاحات احراز هویت (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/دکمه‌های شناور تماس
 * همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه در
 * فایل‌های جداگانه‌ی glossary-page.css / glossary-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-glossary-page">
<?php uid_render_gx_sections(); ?>
<?php uid_render_gx_quick_modal(); ?>
</div>
<?php
get_footer();
