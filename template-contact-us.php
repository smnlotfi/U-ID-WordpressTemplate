<?php
/**
 * Template Name: تماس با ما (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/دکمه‌های شناور تماس
 * همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه در
 * فایل‌های جداگانه‌ی contact-us-page.css / contact-us-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-contact-us-page">
<?php uid_render_cu_sections(); ?>
</div>
<?php
get_footer();
