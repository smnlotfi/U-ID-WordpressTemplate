<?php
/**
 * Template Name: مقاله OCR چیست؟ (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/دکمه‌های شناور تماس
 * همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه در
 * فایل‌های جداگانه‌ی ocr-pillar-article-page.css / ocr-pillar-article-page.js
 * بارگذاری می‌شود. مودال درخواست مشاوره (#modal) هم — مثل صفحه «درباره ما» —
 * جداگانه بعد از سکشن‌ها رندر می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-ocr-pillar-article-page">
<?php uid_render_op_sections(); ?>
<?php uid_render_op_quick_modal(); ?>
</div>
<?php
get_footer();
