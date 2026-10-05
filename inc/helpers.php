<?php
/**
 * توابع کمکی
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * خواندن یک مقدار از گروه تنظیمات قالب (ذخیره‌شده به‌صورت آرایه در wp_options)
 * گروه‌های موجود: uid_contact_options / uid_header_options / uid_footer_options
 */
function uid_get_option( $group, $key, $default = '' ) {
	$opts = get_option( $group, array() );
	if ( isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	return $default;
}

/**
 * تبدیل ارقام انگلیسی به فارسی — برای نمایش شماره تلفن مطابق طراحی اصلی
 */
function uid_fa_digits( $string ) {
	$en = array( '0','1','2','3','4','5','6','7','8','9' );
	$fa = array( '۰','۱','۲','۳','۴','۵','۶','۷','۸','۹' );
	return str_replace( $en, $fa, (string) $string );
}

/**
 * ساخت لینک tel: از روی شماره ذخیره‌شده در تنظیمات (فقط ارقام و + مجاز است)
 */
function uid_phone_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

function uid_phone_raw() {
	return uid_get_option( 'uid_contact_options', 'phone', '02166123290' );
}

/**
 * نمایش شماره تلفن سراسری سایت با ارقام فارسی
 */
function uid_phone_display() {
	return uid_fa_digits( uid_phone_raw() );
}

function uid_whatsapp_url() {
	return uid_get_option( 'uid_contact_options', 'whatsapp', 'https://wa.me/989120000000' );
}

function uid_telegram_url() {
	return uid_get_option( 'uid_contact_options', 'telegram', 'https://t.me/uid_support' );
}

/**
 * لوگو — از تنظیمات قالب (آپلود مستقیم در صفحه تنظیمات هدر) با بازگشت به آرم SVG پیش‌فرض
 */
function uid_logo_html( $echo = true ) {
	$logo_id = absint( uid_get_option( 'uid_header_options', 'logo_id', 0 ) );

	if ( $logo_id ) {
		$html = wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'uid-logo-img' ) );
	} else {
		$html  = '<svg class="mark" viewBox="0 0 100 100"><polygon class="mark-hex" points="50,3 93,26 93,74 50,97 7,74 7,26"/><path class="arc arc1" d="M20,78 V46 A30,30 0 0,1 80,46 V78"/><path class="arc arc2" d="M33,78 V48 A17,17 0 0,1 67,48 V78"/><path class="arc arc3" d="M46,78 V52 A4,4 0 0,1 54,52 V78"/></svg>';
		$html .= esc_html( get_bloginfo( 'name' ) );
	}

	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped/safe pieces above
		return;
	}
	return $html;
}

/**
 * هدر/فوتر سراسری المنتور (Theme Builder) — به‌جای طراحی پیش‌فرض قالب
 *
 * با ‎?uid_preview_header=1‎ یا ‎=0‎ (فقط برای کاربر لاگین‌شده با دسترسی تنظیمات
 * قالب) می‌توان بدون ذخیره‌ی تنظیمات، نتیجه‌ی هرکدام را از قبل روی سایت دید.
 */
function uid_header_preview_override() {
	if ( ! isset( $_GET['uid_preview_header'] ) || ! current_user_can( 'edit_theme_options' ) ) return null;
	return (bool) absint( $_GET['uid_preview_header'] );
}

function uid_footer_preview_override() {
	if ( ! isset( $_GET['uid_preview_footer'] ) || ! current_user_can( 'edit_theme_options' ) ) return null;
	return (bool) absint( $_GET['uid_preview_footer'] );
}

function uid_use_elementor_header() {
	$override = uid_header_preview_override();
	if ( null !== $override ) return $override;
	return (bool) uid_get_option( 'uid_header_options', 'use_elementor_header', false );
}

function uid_use_elementor_footer() {
	$override = uid_footer_preview_override();
	if ( null !== $override ) return $override;
	return (bool) uid_get_option( 'uid_footer_options', 'use_elementor_footer', false );
}
