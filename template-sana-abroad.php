<?php
/**
 * Template Name: ثبت‌نام ثنا ایرانیان خارج از کشور (طراحی اختصاصی)
 * Template Post Type: page
 *
 * دقیقاً همان الگوی صفحات قبلی: یک «قالب صفحه» که روی هر برگه‌ای (با هر اسلاگ
 * دلخواه) از بخش «ویژگی‌ها» قابل‌انتخاب است. هدر/فوتر/منو/مودال/دکمه‌های شناور
 * تماس همان‌های مشترک سایت‌اند؛ فقط استایل و رفتار تعاملی خودِ محتوای این صفحه
 * در فایل‌های جداگانه‌ی sana-abroad-page.css / sana-abroad-page.js بارگذاری می‌شود.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="uid-sana-abroad-page">
<?php uid_render_sa_sections(); ?>
<?php uid_render_sa_quick_modal(); ?>
</div>
<?php
get_footer();
