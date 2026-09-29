<?php
/**
 * Template Name: استعلام شبا (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/مودال/دکمه‌های شناور
 * تماس همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی iban-sheba-page.css / iban-sheba-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-iban-page">
<?php uid_render_ib_sections(); ?>
<?php uid_render_ib_quick_modal(); ?>
</div>
<?php
get_footer();
