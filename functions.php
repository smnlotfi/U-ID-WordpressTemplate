<?php
/**
 * UID Theme — functions.php
 *
 * فاز ۱: راه‌اندازی پایه قالب + مدیریت محتوای هدر و فوتر از پیشخوان.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_THEME_VERSION', '0.3.0' );
define( 'UID_THEME_DIR', get_template_directory() );
define( 'UID_THEME_URI', get_template_directory_uri() );

/**
 * تنظیمات پایه قالب
 */
function uid_theme_setup() {
	// لوگو از صفحه اختصاصی «تنظیمات قالب» مدیریت می‌شود (نه از سفارشی‌سازی وردپرس)

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'responsive-embeds' );

	// منوی هدر (مگامنو) و منوهای فوتر از پیشخوان > تنظیمات قالب یوآیدی مدیریت می‌شوند
	// (نه از پیشخوان > ظاهر > منوها) — نگاه کنید به inc/nav-menus.php
}
add_action( 'after_setup_theme', 'uid_theme_setup' );

/**
 * بارگذاری استایل و اسکریپت
 */
function uid_theme_assets() {
	// فونت‌ها از خودِ قالب سرو می‌شوند (نه از fonts.googleapis.com) چون دامنه‌های گوگل
	// برای بخشی از کاربران ایرانی فیلتر/ناپایدار است و باعث لود نشدن فونت می‌شد
	wp_enqueue_style( 'uid-fonts', UID_THEME_URI . '/assets/css/fonts.css', array(), UID_THEME_VERSION );
	wp_enqueue_style( 'uid-style', get_stylesheet_uri(), array(), UID_THEME_VERSION );
	wp_enqueue_script( 'uid-main', UID_THEME_URI . '/assets/js/main.js', array(), UID_THEME_VERSION, true );
	wp_localize_script( 'uid-main', 'UID_FORM_CFG', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'uid_form_submit' ),
	) );

	// فقط روی قالب صفحه یوآیدی‌پلاس — استایل/رفتار اختصاصی این صفحه
	if ( is_page_template( 'template-pwa.php' ) ) {
		wp_enqueue_style( 'uid-pwa-page', UID_THEME_URI . '/assets/css/pwa-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-pwa-page', UID_THEME_URI . '/assets/js/pwa-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه وب سرویس ثبت احوال
	if ( is_page_template( 'template-civil-registration.php' ) ) {
		wp_enqueue_style( 'uid-civil-page', UID_THEME_URI . '/assets/css/civil-registration-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-civil-page', UID_THEME_URI . '/assets/js/civil-registration-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_cr_samples_json' ) ) {
			wp_add_inline_script( 'uid-civil-page', 'window.UID_CR_SAMPLES_OVERRIDE = ' . uid_cr_samples_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه وب‌سرویس احراز هویت تصویری
	if ( is_page_template( 'template-ekyc-liveness.php' ) ) {
		wp_enqueue_style( 'uid-ekyc-page', UID_THEME_URI . '/assets/css/ekyc-liveness-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-ekyc-page', UID_THEME_URI . '/assets/js/ekyc-liveness-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_ek_scenarios_json' ) ) {
			wp_add_inline_script( 'uid-ekyc-page', 'window.UID_EK_SCENARIOS_OVERRIDE = ' . uid_ek_scenarios_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه احراز هویت ثنا
	if ( is_page_template( 'template-sana.php' ) ) {
		wp_enqueue_style( 'uid-sana-page', UID_THEME_URI . '/assets/css/sana-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-sana-page', UID_THEME_URI . '/assets/js/sana-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه وب‌سرویس شاهکار
	if ( is_page_template( 'template-shahkar.php' ) ) {
		wp_enqueue_style( 'uid-shahkar-page', UID_THEME_URI . '/assets/css/shahkar-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-shahkar-page', UID_THEME_URI . '/assets/js/shahkar-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه استعلام کد پستی و آدرس
	if ( is_page_template( 'template-postal-address.php' ) ) {
		wp_enqueue_style( 'uid-postal-address-page', UID_THEME_URI . '/assets/css/postal-address-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-postal-address-page', UID_THEME_URI . '/assets/js/postal-address-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_pa_samples_json' ) ) {
			wp_add_inline_script( 'uid-postal-address-page', 'window.UID_PA_SAMPLES_OVERRIDE = ' . uid_pa_samples_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه استعلام شبا
	if ( is_page_template( 'template-iban-sheba.php' ) ) {
		wp_enqueue_style( 'uid-iban-page', UID_THEME_URI . '/assets/css/iban-sheba-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-iban-page', UID_THEME_URI . '/assets/js/iban-sheba-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه تبدیل شماره کارت به شبا
	if ( is_page_template( 'template-card-to-iban.php' ) ) {
		wp_enqueue_style( 'uid-card-to-iban-page', UID_THEME_URI . '/assets/css/card-to-iban-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-card-to-iban-page', UID_THEME_URI . '/assets/js/card-to-iban-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه تطبیق شماره شبا با کد ملی
	if ( is_page_template( 'template-iban-validate.php' ) ) {
		wp_enqueue_style( 'uid-iban-validate-page', UID_THEME_URI . '/assets/css/iban-validate-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-iban-validate-page', UID_THEME_URI . '/assets/js/iban-validate-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_iv_vtests_json' ) ) {
			wp_add_inline_script( 'uid-iban-validate-page', 'window.UID_IV_VTESTS_OVERRIDE = ' . uid_iv_vtests_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه تطبیق شماره کارت با کد ملی
	if ( is_page_template( 'template-card-validate.php' ) ) {
		wp_enqueue_style( 'uid-card-validate-page', UID_THEME_URI . '/assets/css/card-validate-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-card-validate-page', UID_THEME_URI . '/assets/js/card-validate-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_cv_vtests_json' ) ) {
			wp_add_inline_script( 'uid-card-validate-page', 'window.UID_CV_VTESTS_OVERRIDE = ' . uid_cv_vtests_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه ثبت‌نام ثنا ایرانیان خارج از کشور
	if ( is_page_template( 'template-sana-abroad.php' ) ) {
		wp_enqueue_style( 'uid-sana-abroad-page', UID_THEME_URI . '/assets/css/sana-abroad-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-sana-abroad-page', UID_THEME_URI . '/assets/js/sana-abroad-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه فهرست وب‌سرویس‌های احراز هویت
	if ( is_page_template( 'template-web-services-hub.php' ) ) {
		wp_enqueue_style( 'uid-web-services-hub-page', UID_THEME_URI . '/assets/css/web-services-hub-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-web-services-hub-page', UID_THEME_URI . '/assets/js/web-services-hub-page.js', array(), UID_THEME_VERSION, true );
		if ( function_exists( 'uid_wsh_console_overrides_json' ) ) {
			wp_add_inline_script( 'uid-web-services-hub-page', 'window.UID_WSH_CONSOLE_OVERRIDES = ' . uid_wsh_console_overrides_json() . ';', 'before' );
		}
	}

	// فقط روی قالب صفحه واژه‌نامه اصطلاحات
	if ( is_page_template( 'template-glossary.php' ) ) {
		wp_enqueue_style( 'uid-glossary-page', UID_THEME_URI . '/assets/css/glossary-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-glossary-page', UID_THEME_URI . '/assets/js/glossary-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه درباره ما
	if ( is_page_template( 'template-about-us.php' ) ) {
		wp_enqueue_style( 'uid-about-us-page', UID_THEME_URI . '/assets/css/about-us-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-about-us-page', UID_THEME_URI . '/assets/js/about-us-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه تماس با ما
	if ( is_page_template( 'template-contact-us.php' ) ) {
		wp_enqueue_style( 'uid-contact-us-page', UID_THEME_URI . '/assets/css/contact-us-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-contact-us-page', UID_THEME_URI . '/assets/js/contact-us-page.js', array(), UID_THEME_VERSION, true );
	}

	// فقط روی قالب صفحه مقاله OCR چیست؟
	if ( is_page_template( 'template-ocr-pillar-article.php' ) ) {
		wp_enqueue_style( 'uid-ocr-pillar-article-page', UID_THEME_URI . '/assets/css/ocr-pillar-article-page.css', array( 'uid-style' ), UID_THEME_VERSION );
		wp_enqueue_script( 'uid-ocr-pillar-article-page', UID_THEME_URI . '/assets/js/ocr-pillar-article-page.js', array(), UID_THEME_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'uid_theme_assets' );

/**
 * فایل‌های کمکی
 */
require UID_THEME_DIR . '/inc/helpers.php';
require UID_THEME_DIR . '/inc/nav-menus.php';
require UID_THEME_DIR . '/inc/section-manager.php';
require UID_THEME_DIR . '/inc/form-submissions.php';
require UID_THEME_DIR . '/inc/admin-settings.php';
require UID_THEME_DIR . '/inc/page-manager.php';
require UID_THEME_DIR . '/inc/seo.php';
require UID_THEME_DIR . '/inc/homepage-setup.php';
require UID_THEME_DIR . '/inc/pwa-page.php';
require UID_THEME_DIR . '/inc/civil-registration-page.php';
require UID_THEME_DIR . '/inc/ekyc-liveness-page.php';
require UID_THEME_DIR . '/inc/sana-page.php';
require UID_THEME_DIR . '/inc/shahkar-page.php';
require UID_THEME_DIR . '/inc/postal-address-page.php';
require UID_THEME_DIR . '/inc/iban-sheba-page.php';
require UID_THEME_DIR . '/inc/card-to-iban-page.php';
require UID_THEME_DIR . '/inc/iban-validate-page.php';
require UID_THEME_DIR . '/inc/card-validate-page.php';
require UID_THEME_DIR . '/inc/sana-abroad-page.php';
require UID_THEME_DIR . '/inc/web-services-hub-page.php';
require UID_THEME_DIR . '/inc/glossary-page.php';
require UID_THEME_DIR . '/inc/about-us-page.php';
require UID_THEME_DIR . '/inc/contact-us-page.php';
require UID_THEME_DIR . '/inc/ocr-pillar-article-page.php';

/**
 * ویجت‌های فوتر ستون‌دار (اختیاری برای آینده — فعلاً از منو استفاده می‌کنیم)
 */
function uid_theme_content_width() {
	$GLOBALS['content_width'] = 1220;
}
add_action( 'after_setup_theme', 'uid_theme_content_width', 0 );
