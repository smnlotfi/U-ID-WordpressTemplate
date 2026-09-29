<?php
/**
 * Template Name: تبدیل شماره کارت به شبا (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/مودال/دکمه‌های شناور
 * تماس همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی card-to-iban-page.css / card-to-iban-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-card-to-iban-page">
<?php uid_render_ci_sections(); ?>
<?php uid_render_ci_quick_modal(); ?>
</div>
<?php
get_footer();
