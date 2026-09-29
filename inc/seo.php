<?php
/**
 * سئو از قالب حذف شده — عمداً هیچ تنظیمات یا خروجی سئویی (عنوان متا، توضیحات،
 * Open Graph، Twitter Card، JSON-LD) از قالب تولید نمی‌شود تا با افزونه سئوی
 * فعال روی سایت (Yoast / Rank Math / SEOPress / All in One SEO) تداخل نداشته
 * باشد. تمام تنظیمات سئو باید از همان افزونه انجام شود.
 *
 * تنها کاری که این فایل انجام می‌دهد: تا زمانی که یک افزونه سئوی شناخته‌شده
 * فعال نشده، همه صفحات قالب به‌صورت پیش‌فرض no-index/no-follow هستند تا
 * سایت به‌اشتباه قبل از آماده شدن ایندکس نشود.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * آیا یک افزونه سئوی شناخته‌شده فعال است؟
 */
function uid_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )              // Yoast SEO
		|| class_exists( 'RankMath' )               // Rank Math
		|| defined( 'SEOPRESS_VERSION' )            // SEOPress
		|| defined( 'AIOSEO_VERSION' );             // All in One SEO
}

/**
 * پیش‌فرض no-index برای همه صفحات قالب، فقط در نبود یک افزونه سئوی فعال
 * (که خودش مسئول تگ robots می‌شود).
 */
function uid_seo_output_default_noindex() {
	if ( uid_seo_plugin_active() ) return;

	echo '<meta name="robots" content="noindex, nofollow">' . "\n";
}
add_action( 'wp_head', 'uid_seo_output_default_noindex', 1 );
