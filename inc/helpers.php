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
 * تبدیل تاریخ میلادی به شمسی (الگوریتم استاندارد و رایج در پروژه‌های فارسی)
 */
function uid_gregorian_to_jalali( $gy, $gm, $gd ) {
	$g_days_in_month = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$jy = ( $gy <= 1600 ) ? 0 : 979;
	$gy -= ( $gy <= 1600 ) ? 621 : 1600;
	$gy2 = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
	$days = ( 365 * $gy ) + (int) ( ( $gy2 + 3 ) / 4 ) - (int) ( ( $gy2 + 99 ) / 100 ) + (int) ( ( $gy2 + 399 ) / 400 ) - 80 + $gd + $g_days_in_month[ $gm - 1 ];
	$jy  += 33 * (int) ( $days / 12053 );
	$days = $days % 12053;
	$jy  += 4 * (int) ( $days / 1461 );
	$days = $days % 1461;
	if ( $days > 365 ) {
		$jy  += (int) ( ( $days - 1 ) / 365 );
		$days = ( $days - 1 ) % 365;
	}
	if ( $days < 186 ) {
		$jm = 1 + (int) ( $days / 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + (int) ( ( $days - 186 ) / 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}
	return array( $jy, $jm, $jd );
}

/**
 * نمایش یک تاریخ‌-ساعت ذخیره‌شده با current_time('mysql') به‌صورت شمسی با ارقام فارسی.
 * توجه: از mysql2date() با $translate=false استفاده می‌شود چون مقدار ورودی از قبل
 * به وقت محلی سایت تبدیل شده؛ حالت translate=true دوباره افست منطقه زمانی را اعمال
 * می‌کند و ساعت/تاریخ نمایشی را اشتباه می‌کند (افست دوبار اعمال می‌شود).
 */
function uid_fa_datetime( $mysql_datetime ) {
	if ( empty( $mysql_datetime ) ) {
		return '';
	}
	$local = mysql2date( 'Y-m-d H:i', $mysql_datetime, false );
	if ( ! $local ) {
		return '';
	}
	list( $date_part, $time_part ) = explode( ' ', $local );
	list( $gy, $gm, $gd )          = array_map( 'intval', explode( '-', $date_part ) );
	list( $jy, $jm, $jd )          = uid_gregorian_to_jalali( $gy, $gm, $gd );
	return uid_fa_digits( sprintf( '%04d/%02d/%02d %s', $jy, $jm, $jd, $time_part ) );
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
