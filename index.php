<?php
/**
 * قالب پیش‌فرض — برای نوشته‌ها و برگه‌هایی که قالب اختصاصی ندارند.
 * صفحه اصلی و سایر صفحات با طراحی اختصاصی، هرکدام یک «قالب صفحه» جداگانه
 * دارند (مثل template-homepage.php) که از بخش «ویژگی‌ها»ی برگه در پیشخوان
 * قابل‌انتخاب است.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main class="wrap" style="padding:160px 0 96px;">
	<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
		<p class="placeholder-flag">
			<?php esc_html_e( 'این فاز اول قالب است: فقط هدر و فوتر فعال شده‌اند. بخش‌های محتوایی صفحه اصلی در فاز بعدی اضافه می‌شوند.', 'uid-theme' ); ?>
		</p>
	<?php endif; ?>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div><?php the_content(); ?></div>
		</article>
	<?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
