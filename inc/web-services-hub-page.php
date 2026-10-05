<?php
/**
 * صفحه اختصاصی «فهرست وب‌سرویس‌های احراز هویت» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی
 * واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «wsh» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options با
 * سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * ۵ سکشن این صفحه (cmp/extra/adv/start/faq) سیستم «فصل تاخوردنی + خواننده فصل» موبایل
 * دارند — دقیقاً مثل inc/ekyc-liveness-page.php. نگاه کنید به
 * uid_wsh_folded_slugs()/uid_render_wsh_foldbar().
 *
 * مسیریاب هیرو (Router)، کنسول مشترک هشت وب‌سرویس (Console) و منطق فیلتر/جست‌وجوی
 * کاتالوگ کاملاً تعاملی و جاوااسکریپتی‌اند و از این صفحه قابل‌ویرایش نیستند — دقیقاً
 * مثل ویجت زنده inc/card-to-iban-page.php (نگاه کنید به assets/js/web-services-hub-page.js).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_WSH_TEMPLATE', 'template-web-services-hub.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_wsh_sections_registry() {
	return array(
		'wshhero'      => array( 'label' => __( 'هیرو + مسیریاب انتخاب سرویس', 'uid-theme' ),       'icon' => 'dashicons-star-filled' ),
		'wshstats'     => array( 'label' => __( 'باند عملیاتی (۴ آمار)', 'uid-theme' ),               'icon' => 'dashicons-chart-bar' ),
		'wshtldr'      => array( 'label' => __( 'کپسول ۳۰ ثانیه‌ای (ویژه موبایل)', 'uid-theme' ),     'icon' => 'dashicons-smartphone' ),
		'wshconsole'   => array( 'label' => __( 'کنسول تست مشترک هشت وب‌سرویس', 'uid-theme' ),        'icon' => 'dashicons-embed-generic' ),
		'wshcatalog'   => array( 'label' => __( 'فهرست/کاتالوگ وب‌سرویس‌های اصلی', 'uid-theme' ),      'icon' => 'dashicons-grid-view' ),
		'wshcmp'       => array( 'label' => __( 'فصل ۱ — جدول مقایسه سرویس‌ها', 'uid-theme' ),         'icon' => 'dashicons-editor-table' ),
		'wshextra'     => array( 'label' => __( 'فصل ۲ — وب‌سرویس‌های مکمل و تخصصی', 'uid-theme' ),    'icon' => 'dashicons-networking' ),
		'wshadv'       => array( 'label' => __( 'فصل ۳ — چرا وب‌سرویس‌های یوآیدی', 'uid-theme' ),      'icon' => 'dashicons-awards' ),
		'wshstart'     => array( 'label' => __( 'فصل ۴ — از درخواست تا استعلام واقعی', 'uid-theme' ),  'icon' => 'dashicons-randomize' ),
		'wshfaq'       => array( 'label' => __( 'فصل ۵ — سوالات متداول', 'uid-theme' ),                'icon' => 'dashicons-editor-help' ),
		'wshcallband'  => array( 'label' => __( 'بند تماس میان‌صفحه', 'uid-theme' ),                   'icon' => 'dashicons-megaphone' ),
		'wshlead'      => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                 'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_wsh_layout() {
	$registry = uid_wsh_sections_registry();
	$saved    = get_option( 'uid_wsh_layout', array() );

	if ( empty( $saved ) || ! is_array( $saved ) ) {
		$layout = array();
		foreach ( $registry as $slug => $meta ) {
			$layout[] = array( 'slug' => $slug, 'enabled' => true );
		}
		return $layout;
	}

	$clean = array();
	$seen  = array();
	foreach ( $saved as $row ) {
		if ( empty( $row['slug'] ) || ! isset( $registry[ $row['slug'] ] ) ) continue;
		$clean[] = array( 'slug' => $row['slug'], 'enabled' => ! empty( $row['enabled'] ) );
		$seen[ $row['slug'] ] = true;
	}
	foreach ( $registry as $slug => $meta ) {
		if ( empty( $seen[ $slug ] ) ) {
			$clean[] = array( 'slug' => $slug, 'enabled' => true );
		}
	}
	return $clean;
}

function uid_sanitize_wsh_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_wsh_sections_registry();
	$clean    = array();
	foreach ( $raw as $row ) {
		if ( empty( $row['slug'] ) || ! isset( $registry[ $row['slug'] ] ) ) continue;
		$clean[] = array(
			'slug'    => sanitize_key( $row['slug'] ),
			'enabled' => ! empty( $row['enabled'] ),
		);
	}
	return $clean;
}

/**
 * اسلاگ‌های سکشن‌هایی که سیستم «فصل تاخوردنی» موبایل رویشان اعمال می‌شود.
 */
function uid_wsh_folded_slugs() {
	return array( 'wshcmp', 'wshextra', 'wshadv', 'wshstart', 'wshfaq' );
}

/**
 * اسلاگ‌های فصل‌هایی که در mockup ویژگی data-fold-hot دارند (نقطه داغ کوچک روی
 * ردیف تاخورده موبایل).
 */
function uid_wsh_hot_slugs() {
	return array( 'wshcmp', 'wshextra', 'wshadv', 'wshfaq' );
}

function uid_render_wsh_sections() {
	uid_render_wsh_jumpbar();

	$folded    = uid_wsh_folded_slugs();
	$layout    = uid_get_wsh_layout();
	$folded_on = 0;
	foreach ( $layout as $row ) {
		if ( ! empty( $row['enabled'] ) && in_array( $row['slug'], $folded, true ) ) $folded_on++;
	}

	$foldbar_done = false;
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$is_folded = in_array( $row['slug'], $folded, true );
		if ( $is_folded && ! $foldbar_done ) {
			echo '<div id="chapters" class="fold-run">';
			uid_render_wsh_foldbar( $folded_on );
			$foldbar_done = true;
		}
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
	if ( $foldbar_done ) {
		echo '</div>';
	}
}

/**
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود.
 * نوار پیشرفت مطالعه (fold-meter) توسط assets/js/web-services-hub-page.js پر می‌شود.
 */
function uid_render_wsh_foldbar( $count ) {
	?>
	<div class="foldbar" id="foldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <button class="fb-filter" id="foldFilter" type="button" aria-pressed="false">
	    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
	    <span><?php esc_html_e( 'فقط بخش‌های ضروری', 'uid-theme' ); ?></span></button>
	  <span class="fold-meter"><i id="foldMeter"></i></span>
	</div>
	<?php
}

/**
 * نوار «پرش سریع به بخش‌ها» — عنصر ساختاری ثابت (نه یک سکشن محتوایی)؛ به لنگرهای
 * سکشن‌های همین صفحه اشاره می‌کند و همیشه نمایش داده می‌شود — دقیقاً مثل
 * inc/postal-address-page.php.
 */
function uid_render_wsh_jumpbar() {
	?>
	<div class="jumpbar" id="jumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jump-rail" id="jumpRail">
	    <button class="jump-chip cta" type="button" data-open-modal><?php esc_html_e( 'درخواست وب‌سرویس', 'uid-theme' ); ?></button>
	    <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	    <button class="jump-chip" type="button" data-jump="#console"><?php esc_html_e( 'کنسول تست', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#catalog"><?php esc_html_e( 'فهرست سرویس‌ها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#chapters"><?php esc_html_e( 'بخش‌ها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#cmp"><?php esc_html_e( 'مقایسه', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#extra"><?php esc_html_e( 'سرویس‌های مکمل', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#adv"><?php esc_html_e( 'مزایا', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#faq"><?php esc_html_e( 'سوالات متداول', 'uid-theme' ); ?></button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_wsh_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_wsh_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_wsh_list_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>';
}
function uid_wsh_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_wsh_info_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>';
}
function uid_wsh_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>';
}
function uid_wsh_chev_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}

/* آیکون‌های ۸ کارت کاتالوگ + رنگ‌بندی — کلید هر ردیف («key») هم برای یافتن آیکون/رنگ و
   هم برای نگاشت به کنسول تست (assets/js/web-services-hub-page.js) و فیلتر کاتالوگ استفاده
   می‌شود؛ نگاه کنید به یادداشت زیر تنظیمات کاتالوگ. */
function uid_wsh_catalog_icon_svg( $key ) {
	$paths = array(
		'ekyc'      => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'shahkar'   => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18.5h2"/><path d="M9 6h6"/>',
		'civil'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'iban'      => '<path d="M3 10l9-6 9 6"/><path d="M5 10v9M19 10v9M9 10v9M15 10v9M3 21h18"/>',
		'postal'    => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'card2iban' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/><path d="M15 15h4"/>',
		'ibanval'   => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/><path d="M12 15v2"/>',
		'cardval'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M14.5 15.5l2 2 4-4.5"/>',
	);
	return $paths[ $key ] ?? $paths['ekyc'];
}
function uid_wsh_catalog_tone( $key ) {
	$tones = array(
		'ekyc' => 'warm', 'shahkar' => '', 'civil' => 'navy', 'iban' => '',
		'postal' => 'navy', 'card2iban' => '', 'ibanval' => '', 'cardval' => 'warm',
	);
	return $tones[ $key ] ?? '';
}
$GLOBALS['uid_wsh_catalog_svg_allowed'] = array(
	'path'    => array( 'd' => true ),
	'circle'  => array( 'cx' => true, 'cy' => true, 'r' => true ),
	'rect'    => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
);

/* آیکون‌های ۶ سرویس مکمل (اسمی) */
function uid_wsh_extra_icon_svg( $key ) {
	$paths = array(
		'card'        => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'deed'        => '<path d="M3 10.5L12 3l9 7.5V20a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 20z"/><path d="M9.5 21v-6h5v6"/>',
		'transaction' => '<path d="M3 17l5-5 4 3 5-7"/><path d="M3 21h18"/><path d="M17 8h4v4"/>',
		'license'     => '<path d="M4 9h16v11a1 1 0 01-1 1H5a1 1 0 01-1-1z"/><path d="M4 9l2-5h12l2 5"/><path d="M10 21v-5h4v5"/>',
		'company'     => '<path d="M3 21h18M5 21V7l7-4 7 4v14"/><path d="M9 21v-5h6v5M9 11h.01M15 11h.01"/>',
		'criminal'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9.5 12l2 2 3.5-4"/>',
	);
	return $paths[ $key ] ?? $paths['card'];
}

/* آیکون‌های ۳ کارت مزایا */
function uid_wsh_adv_icon_svg( $key ) {
	$paths = array(
		'fast'    => '<path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/>',
		'support' => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9"/>',
		'sandbox' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	);
	return $paths[ $key ] ?? $paths['fast'];
}
function uid_wsh_adv_tone( $key ) {
	$tones = array( 'fast' => '', 'support' => 'warm', 'sandbox' => 'navy' );
	return $tones[ $key ] ?? '';
}

/* =====================================================================
 * هیرو + مسیریاب انتخاب سرویس (مسیریاب کاملاً جاوااسکریپتی و غیرقابل‌ویرایش است)
 * ===================================================================== */
function uid_default_wsh_router() {
	return array(
		array( 'key' => 'identity', 'label' => __( 'هویت شخص', 'uid-theme' ) ),
		array( 'key' => 'mobile', 'label' => __( 'شماره موبایل', 'uid-theme' ) ),
		array( 'key' => 'bank', 'label' => __( 'حساب بانکی', 'uid-theme' ) ),
		array( 'key' => 'card', 'label' => __( 'کارت بانکی', 'uid-theme' ) ),
		array( 'key' => 'address', 'label' => __( 'نشانی و کد پستی', 'uid-theme' ) ),
	);
}
function uid_wsh_router_icon_svg( $key ) {
	$paths = array(
		'identity' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
		'mobile'   => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18.5h2"/>',
		'bank'     => '<path d="M3 10l9-6 9 6"/><path d="M5 10v9M19 10v9M9 10v9M15 10v9M3 21h18"/>',
		'card'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'address'  => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
	);
	return $paths[ $key ] ?? $paths['identity'];
}
function uid_wsh_router_key_options() {
	return array(
		'identity' => __( 'هویت شخص', 'uid-theme' ),
		'mobile'   => __( 'شماره موبایل', 'uid-theme' ),
		'bank'     => __( 'حساب بانکی', 'uid-theme' ),
		'card'     => __( 'کارت بانکی', 'uid-theme' ),
		'address'  => __( 'نشانی و کد پستی', 'uid-theme' ),
	);
}
function uid_render_section_wshhero() {
	$tag  = uid_section_tag( 'wshhero', 'h1' );
	$tags = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'wshhero', 'tags', "۸ وب‌سرویس با نمونه تست زنده\n۶ وب‌سرویس مکمل و تخصصی\nیک کلید API برای چند سرویس" ) ) ) );
	$router_items = uid_section_val( 'wshhero', 'router_items', uid_default_wsh_router() );
	if ( ! is_array( $router_items ) || empty( $router_items ) ) $router_items = uid_default_wsh_router();
	?>
	<section class="dark heroA" id="top">
	  <div class="wsh-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <b><?php echo esc_html( get_the_title() ?: __( 'وب‌سرویس‌های احراز هویت', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="wsh-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'wshhero', 'eyebrow', __( 'فهرست کامل وب‌سرویس‌های یوآیدی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'wshhero', 'heading', __( 'خدمات <mark>وب‌سرویس احراز هویت</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?> <span class="lat">API</span><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'wshhero', 'text', __( 'یوآیدی به‌عنوان اولین اپراتور احراز هویت ایران، پس از ارائه انواع API وب‌سرویس‌های احراز هویت دیجیتال و به‌منظور سهولت دسترسی و امنیت کامل کسب‌وکارها، وب‌سرویس احراز هویت بایومتریک را توسعه داده است. در این صفحه همه این وب‌سرویس‌ها را می‌بینید و می‌توانید پیش از هر تماسی، هرکدام را همین‌جا تست کنید.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="wsh-btn btn-cta" type="button" data-jump-soft="#catalog"><?php echo uid_wsh_list_icon(); ?><?php echo esc_html( uid_section_val( 'wshhero', 'btn1_text', __( 'دیدن فهرست وب‌سرویس‌ها', 'uid-theme' ) ) ); ?></button>
	          <a class="wsh-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_wsh_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_wsh_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- مسیریاب انتخاب سرویس — کاملاً تعاملی/جاوااسکریپتی، از این صفحه قابل‌ویرایش نیست -->
	      <div class="router rv rv-d2" id="router">
	        <div class="router-hd">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span>
	          <b><?php esc_html_e( 'چه چیزی را می‌خواهید تایید کنید؟', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'یک انتخاب', 'uid-theme' ); ?></span>
	        </div>

	        <div class="rt-opts" id="rtOpts" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب نوع استعلام', 'uid-theme' ); ?>">
	          <?php foreach ( $router_items as $it ) :
	            $rkey = $it['key'] ?? 'identity';
	          ?>
	          <button class="rt-opt" type="button" role="tab" aria-selected="false" data-rt="<?php echo esc_attr( $rkey ); ?>">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_wsh_router_icon_svg( $rkey ), $GLOBALS['uid_wsh_catalog_svg_allowed'] ); ?></svg>
	            <span><?php echo esc_html( $it['label'] ?? '' ); ?></span></button>
	          <?php endforeach; ?>
	        </div>

	        <div class="rt-res" id="rtRes">
	          <div class="rt-res-hd"><span><?php esc_html_e( 'نتیجه:', 'uid-theme' ); ?></span><b id="rtCount">—</b><span><?php esc_html_e( 'وب‌سرویس پیشنهادی', 'uid-theme' ); ?></span></div>
	          <div id="rtBody">
	            <div class="rt-empty">
	              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16M4 12h10M4 19h6"/></svg>
	              <?php esc_html_e( 'یکی از پنج گزینه بالا را انتخاب کنید تا از میان ۱۴ وب‌سرویس، فقط آن‌هایی که به کار شما می‌آید نشان داده شود.', 'uid-theme' ); ?>
	            </div>
	          </div>
	        </div>

	        <div class="rt-foot">
	          <?php echo uid_wsh_info_icon(); ?>
	          <span><?php esc_html_e( 'مطمئن نیستید کدام سرویس؟ فرم درخواست سرویس را پر کنید یا با کارشناسان ما تماس بگیرید.', 'uid-theme' ); ?></span>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * باند عملیاتی (۴ آمار)
 * ===================================================================== */
function uid_default_wsh_stats() {
	return array(
		array( 'value' => '۹۹.۹٪', 'label' => 'پایداری تضمین‌شده وب‌سرویس‌ها (SLA) روی زیرساخت توزیع‌شده' ),
		array( 'value' => 'پاسخ‌دهی در لحظه', 'label' => 'سرعت بالای پردازش، مناسب سامانه‌های پرتراکنش' ),
		array( 'value' => 'محیط سندباکس', 'label' => 'تست کامل تمام API‌ها پیش از خرید و عملیاتی‌سازی' ),
		array( 'value' => 'یک کلید، چند سرویس', 'label' => 'امکان فعال‌سازی هم‌زمان چند API روی یک API Key' ),
	);
}
function uid_render_section_wshstats() {
	$items = uid_section_val( 'wshstats', 'items', uid_default_wsh_stats() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="wsh-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $items as $it ) :
	        $numeric = preg_match( '/^[۰-۹0-9.%٪]+$/u', trim( $it['value'] ?? '' ) );
	      ?>
	      <div class="qstat"><span class="v<?php echo $numeric ? '' : ' txt'; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $it['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * کپسول ۳۰ ثانیه‌ای (ویژه موبایل)
 * ===================================================================== */
function uid_default_wsh_tldr() {
	return array(
		'یوآیدی <b>هشت وب‌سرویس اصلی</b> و <b>شش وب‌سرویس مکمل</b> برای احراز هویت و استعلام ارائه می‌دهد.',
		'هر هشت سرویس اصلی را می‌توانید <b>همین‌جا و با داده نمونه تست کنید</b> — بدون ثبت‌نام.',
		'امکان فعال‌سازی هم‌زمان <b>چند API روی یک کلید دسترسی</b> وجود دارد.',
		'پیش از خرید، <b>کلید سندباکس رایگان</b> می‌گیرید و همه سرویس‌ها را روی داده خودتان تست می‌کنید.',
	);
}
function uid_render_section_wshtldr() {
	$items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'wshtldr', 'items', implode( "\n", uid_default_wsh_tldr() ) ) ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec" style="padding-block:24px 0">
	  <div class="wsh-wrap">
	    <div class="tldr rv">
	      <div class="tldr-hd">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/></svg></span>
	        <b><?php echo esc_html( uid_section_val( 'wshtldr', 'heading', __( 'اگر عجله دارید، همین چهار خط کافی است', 'uid-theme' ) ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'wshtldr', 'badge', __( '۳۰ ثانیه', 'uid-theme' ) ) ); ?></span>
	      </div>
	      <ul class="tldr-list">
	        <?php foreach ( $items as $line ) : ?>
	        <li><?php echo uid_wsh_check_icon(); ?>
	          <span><?php echo wp_kses( $line, array( 'b' => array() ) ); ?></span></li>
	        <?php endforeach; ?>
	      </ul>
	      <div class="tldr-acts">
	        <a class="wsh-btn btn-cta btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_wsh_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_raw() ); ?></span></a>
	        <button class="wsh-btn btn-ghost-d btn-block" data-open-modal><?php echo esc_html( uid_section_val( 'wshtldr', 'btn_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	      <button class="tldr-more" type="button" data-jump-soft="#catalog"><?php echo esc_html( uid_section_val( 'wshtldr', 'more_text', __( 'و اگر می‌خواهید خودتان فهرست را ببینید', 'uid-theme' ) ) ); ?>
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * کنسول تست مشترک هشت وب‌سرویس (کاملاً جاوااسکریپتی و غیرقابل‌ویرایش)
 * ===================================================================== */
/**
 * برچسب‌ها و مقادیر پیش‌فرض کنسول تست هشت‌سرویسی — این‌ها دقیقاً همان مقادیری‌اند که در
 * assets/js/web-services-hub-page.js به‌صورت سخت‌کد نوشته شده‌اند. فقط متن (برچسب فیلد،
 * جای‌نگه‌دار، مقدار پیش‌فرض، برچسب دکمه تست) از این صفحه قابل‌ویرایش است؛ منطق محاسبه
 * پاسخ (exec) در همان فایل جاوااسکریپت می‌ماند. مقادیر ذخیره‌شده با
 * uid_wsh_console_overrides_json() به یک آبجکت جاوااسکریپت تبدیل و پیش از خودِ فایل اصلی
 * enqueue می‌شوند (نگاه کنید به functions.php) و در ابتدای اجرای اسکریپت با CONSOLE
 * ادغام می‌شوند.
 */
function uid_default_wsh_console_overrides() {
	return array(
		array(
			'key' => 'ekyc', 'name' => 'شبیه‌ساز تشخیص چهره و زنده بودن (Live Test)',
			'sub' => 'دو سناریوی آماده: یک ویدیوی سلفی واقعی و یک تلاش جعل با عکس.',
			'run_label' => 'اجرای تست زنده بودن تصویر', 'go_name' => 'صفحه وب‌سرویس احراز هویت تصویری',
			'f1_label' => 'کد ملی', 'f1_ph' => '۰۰۷۹۱۸۴۶۵۲', 'f1_val' => '0079184652',
			'f2_label' => 'تاریخ تولد', 'f2_ph' => '۱۳۷۰/۰۵/۱۷', 'f2_val' => '1370/05/17',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'تست تصویر واقعی (Liveness Pass)', 't2_label' => 'تست تصویر جعلی (Spoof Attack)',
		),
		array(
			'key' => 'shahkar', 'name' => 'شبیه‌ساز استعلام مالکیت سیم‌کارت',
			'sub' => 'شماره موبایل و کد ملی را وارد کنید یا یکی از دو سناریوی نمونه را بزنید.',
			'run_label' => 'استعلام مالکیت', 'go_name' => 'صفحه وب‌سرویس شاهکار',
			'f1_label' => 'شماره موبایل', 'f1_ph' => '۰۹۱۲۱۲۳۴۵۶۷', 'f1_val' => '09121234567',
			'f2_label' => 'کد ملی', 'f2_ph' => '۰۰۷۹۱۸۴۶۵۲', 'f2_val' => '0079184652',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'نمونه مالکیت تاییدشده', 't2_label' => 'نمونه عدم تطابق',
		),
		array(
			'key' => 'civil', 'name' => 'پیش‌نمایش استعلام هویتی',
			'sub' => 'با یک کلیک، فرم با داده نمونه پر می‌شود و پاسخ استعلام نمایش داده می‌شود.',
			'run_label' => 'تست استعلام هویتی', 'go_name' => 'صفحه وب‌سرویس ثبت احوال',
			'f1_label' => 'کد ملی', 'f1_ph' => 'کد ملی ۱۰ رقمی', 'f1_val' => '',
			'f2_label' => 'تاریخ تولد', 'f2_ph' => '۱۳۷۰/۰۵/۱۷', 'f2_val' => '',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'تست استعلام هویتی', 't2_label' => '',
		),
		array(
			'key' => 'iban', 'name' => 'شبیه‌ساز استعلام حساب بانکی',
			'sub' => 'شماره شبا را وارد کنید و یکی از دو وضعیت نمونه را انتخاب کنید.',
			'run_label' => 'استعلام وضعیت حساب', 'go_name' => 'صفحه وب‌سرویس استعلام شبا',
			'f1_label' => 'شماره شبا', 'f1_ph' => 'IR۰۶۰۱۲۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰', 'f1_val' => 'IR060120000000000000000000',
			'f2_label' => '', 'f2_ph' => '', 'f2_val' => '',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'حساب فعال و معتبر', 't2_label' => 'حساب مسدود / غیرفعال',
		),
		array(
			'key' => 'postal', 'name' => 'شبیه‌ساز دریافت خودکار آدرس',
			'sub' => 'کد پستی ۱۰ رقمی را وارد کنید تا فیلدهای نشانی پر شود.',
			'run_label' => 'دریافت آدرس', 'go_name' => 'صفحه وب‌سرویس استعلام کد پستی',
			'f1_label' => 'کد پستی', 'f1_ph' => '۱۹۶۸۶۵۳۷۶۱', 'f1_val' => '1968653761',
			'f2_label' => '', 'f2_ph' => '', 'f2_val' => '',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'پر کردن با کد پستی نمونه', 't2_label' => '',
		),
		array(
			'key' => 'card2iban', 'name' => 'شبیه‌ساز تبدیل آنی کارت به شبا',
			'sub' => 'شماره کارت ۱۶ رقمی را وارد کنید؛ به‌محض کامل شدن، شبا و بانک صادرکننده ظاهر می‌شود.',
			'run_label' => 'تبدیل به شبا', 'go_name' => 'صفحه وب‌سرویس تبدیل کارت به شبا',
			'f1_label' => 'شماره کارت', 'f1_ph' => '۶۱۰۴ ۳۳۷۷ ۱۲۳۴ ۵۶۷۸', 'f1_val' => '6104337712345678',
			'f2_label' => '', 'f2_ph' => '', 'f2_val' => '',
			'f3_label' => '', 'f3_ph' => '', 'f3_val' => '',
			't1_label' => 'نمونه کارت بانک ملی', 't2_label' => 'نمونه کارت بانک ملت',
		),
		array(
			'key' => 'ibanval', 'name' => 'شبیه‌ساز اعتبارسنجی مالکیت شبا',
			'sub' => 'دو سناریوی آماده: تطابق موفق و عدم تطابق.',
			'run_label' => 'اجرای اعتبارسنجی مالکیت', 'go_name' => 'صفحه وب‌سرویس تطبیق شبا و کد ملی',
			'f1_label' => 'شماره شبا', 'f1_ph' => 'IR۰۶۰۱۲۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰', 'f1_val' => 'IR060120000000000000000000',
			'f2_label' => 'کد ملی', 'f2_ph' => '۰۰۷۹۱۸۴۶۵۲', 'f2_val' => '0079184652',
			'f3_label' => 'تاریخ تولد', 'f3_ph' => '۱۳۷۰/۰۵/۱۷', 'f3_val' => '1370/05/17',
			't1_label' => 'تطابق موفق', 't2_label' => 'تست عدم تطابق',
		),
		array(
			'key' => 'cardval', 'name' => 'شبیه‌ساز صحت‌سنجی کارت بانکی',
			'sub' => 'دو سناریوی آماده، به‌همراه نمایش نام بانک صادرکننده.',
			'run_label' => 'اجرای تطبیق مالکیت کارت', 'go_name' => 'صفحه وب‌سرویس تطبیق کارت و کد ملی',
			'f1_label' => 'شماره کارت', 'f1_ph' => '۶۱۰۴ ۳۳۷۷ ۱۲۳۴ ۵۶۷۸', 'f1_val' => '6104337712345678',
			'f2_label' => 'کد ملی', 'f2_ph' => '۰۰۷۹۱۸۴۶۵۲', 'f2_val' => '0079184652',
			'f3_label' => 'تاریخ تولد', 'f3_ph' => '۱۳۷۰/۰۵/۱۷', 'f3_val' => '1370/05/17',
			't1_label' => 'کارت متعلق به فرد است', 't2_label' => 'کارت متعلق به شخص دیگری است',
		),
	);
}

/**
 * تبدیل ردیف‌های ذخیره‌شده کنسول به آبجکتی که با آبجکت CONSOLE در جاوااسکریپت ادغام
 * می‌شود. فقط زمانی یک مقدار را برمی‌گرداند که با پیش‌فرض تفاوت داشته باشد — تا اگر
 * ادمین چیزی را عوض نکرده، اسکریپت هیچ کاری اضافه انجام ندهد.
 */
function uid_wsh_console_overrides_json() {
	$rows = uid_section_val( 'wshconsole', 'services', uid_default_wsh_console_overrides() );
	if ( ! is_array( $rows ) || empty( $rows ) ) return '{}';

	$out = array();
	foreach ( $rows as $row ) {
		$key = sanitize_key( $row['key'] ?? '' );
		if ( ! $key ) continue;

		$entry = array();
		foreach ( array( 'name' => 'name', 'sub' => 'sub', 'runLabel' => 'run_label', 'goName' => 'go_name' ) as $js_k => $php_k ) {
			if ( ! empty( $row[ $php_k ] ) ) $entry[ $js_k ] = $row[ $php_k ];
		}
		$fields = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$idx = $i - 1;
			$f = array();
			if ( ! empty( $row[ "f{$i}_label" ] ) ) $f['label'] = $row[ "f{$i}_label" ];
			if ( ! empty( $row[ "f{$i}_ph" ] ) )    $f['ph']    = $row[ "f{$i}_ph" ];
			if ( ! empty( $row[ "f{$i}_val" ] ) )   $f['val']   = $row[ "f{$i}_val" ];
			if ( $f ) $fields[ $idx ] = $f;
		}
		if ( $fields ) $entry['fields'] = $fields;

		$tests = array();
		for ( $i = 1; $i <= 2; $i++ ) {
			$idx = $i - 1;
			if ( ! empty( $row[ "t{$i}_label" ] ) ) $tests[ $idx ] = array( 'label' => $row[ "t{$i}_label" ] );
		}
		if ( $tests ) $entry['tests'] = $tests;

		if ( $entry ) $out[ $key ] = $entry;
	}
	return wp_json_encode( (object) $out );
}

function uid_render_section_wshconsole() {
	$tag = uid_section_tag( 'wshconsole', 'h2' );
	?>
	<section class="dark sec" id="console">
	  <div class="wsh-wrap">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'wshconsole', 'eyebrow', __( 'کنسول تست زنده', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshconsole', 'heading', __( 'هشت وب‌سرویس، یک کنسول', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'wshconsole', 'text', __( 'سرویس موردنظرتان را از نوار زیر انتخاب کنید تا ورودی‌ها، سناریوهای تست و پاسخ همان سرویس در همین پنل نمایش داده شود. داده‌ها نمونه است و هیچ استعلام واقعی انجام نمی‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="cons rv rv-d1">
	      <div class="cons-hd">
	        <span class="live"></span>
	        <b><?php esc_html_e( 'کنسول وب‌سرویس‌های یوآیدی', 'uid-theme' ); ?></b>
	        <span class="tag">SANDBOX / DEMO</span>
	      </div>

	      <div class="cons-rail" id="consRail" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب وب‌سرویس', 'uid-theme' ); ?>"></div>

	      <div class="cons-grid">
	        <div class="cons-in">
	          <b class="cons-name" id="consName"><?php esc_html_e( 'در حال آماده‌سازی کنسول…', 'uid-theme' ); ?></b>
	          <span class="cons-sub" id="consSub"><?php esc_html_e( 'برای نمایش کنسول، جاوااسکریپت مرورگر باید فعال باشد.', 'uid-theme' ); ?></span>
	          <div id="consStage"></div>
	          <div class="cons-flds" id="consFlds"></div>
	          <div class="cons-tests" id="consTests"></div>
	          <button class="wsh-btn btn-cta btn-block" type="button" id="consRun">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3l14 9-14 9z"/></svg>
	            <span id="consRunTx"><?php esc_html_e( 'اجرای استعلام نمونه', 'uid-theme' ); ?></span></button>
	          <div class="cons-note">
	            <?php echo uid_wsh_info_icon(); ?>
	            <span><?php esc_html_e( 'این کنسول کاملاً در مرورگر شما اجرا می‌شود؛ هیچ داده‌ای ارسال نمی‌شود و پاسخ‌ها نمونه‌ای از خروجی واقعی سرویس هستند.', 'uid-theme' ); ?></span>
	          </div>
	        </div>

	        <div class="cons-out">
	          <div class="cons-out-hd">
	            <span class="ep" id="consEp">—</span>
	            <span class="idbadge wait" id="consBadge"><?php esc_html_e( 'در انتظار اجرا', 'uid-theme' ); ?></span>
	          </div>
	          <div id="consCard"></div>
	          <pre class="cons-json" id="consJson">{ }</pre>
	        </div>
	      </div>

	      <div class="cons-foot">
	        <span class="tx"><?php esc_html_e( 'نتیجه را دیدید؟ ', 'uid-theme' ); ?><b id="consFootName"><?php esc_html_e( 'صفحه این سرویس', 'uid-theme' ); ?></b><?php esc_html_e( ' جزئیات فنی، پارامترها و نمونه کد را دارد.', 'uid-theme' ); ?></span>
	        <a class="wsh-btn btn-white wsh-btn-sm" id="consGo" href="/api/"><?php esc_html_e( 'مشاهده صفحه این سرویس', 'uid-theme' ); ?></a>
	        <button class="wsh-btn btn-ghost-d wsh-btn-sm" data-open-modal><?php esc_html_e( 'درخواست کلید سندباکس', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فهرست/کاتالوگ وب‌سرویس‌های اصلی (۸ کارت)
 * ===================================================================== */
function uid_default_wsh_catalog_chips() {
	return array(
		array( 'key' => 'all', 'label' => __( 'همه سرویس‌ها', 'uid-theme' ) ),
		array( 'key' => 'identity', 'label' => __( 'هویت شخص', 'uid-theme' ) ),
		array( 'key' => 'mobile', 'label' => __( 'شماره موبایل', 'uid-theme' ) ),
		array( 'key' => 'bank', 'label' => __( 'حساب بانکی', 'uid-theme' ) ),
		array( 'key' => 'card', 'label' => __( 'کارت بانکی', 'uid-theme' ) ),
		array( 'key' => 'address', 'label' => __( 'نشانی', 'uid-theme' ) ),
	);
}
function uid_wsh_catalog_chip_key_options() {
	return array(
		'all'      => __( 'همه (پیش‌فرض)', 'uid-theme' ),
		'identity' => __( 'هویت شخص', 'uid-theme' ),
		'mobile'   => __( 'شماره موبایل', 'uid-theme' ),
		'bank'     => __( 'حساب بانکی', 'uid-theme' ),
		'card'     => __( 'کارت بانکی', 'uid-theme' ),
		'address'  => __( 'نشانی', 'uid-theme' ),
	);
}
function uid_default_wsh_catalog() {
	return array(
		array(
			'key' => 'ekyc', 'cat' => 'identity',
			'name' => 'وب‌سرویس احراز هویت تصویری با ویدیوی سلفی', 'short' => 'احراز هویت تصویری', 'latin' => 'eKYC · Liveness + Face Verification',
			'slug' => '/api/',
			'desc' => 'این وب‌سرویس با دریافت کد ملی، تاریخ تولد و یک ویدیوی کوتاه سلفی، ابتدا با الگوریتم‌های پیشرفته Liveness Detection زنده بودن تصویر را بررسی کرده (جهت جلوگیری از تقلب با عکس، ماسک یا ویدیوی ضبط‌شده) و سپس از طریق الگوریتم Face Verification چهره کاربر را با تصویر مرجع در پایگاه ثبت احوال تطبیق می‌دهد.',
			'use'  => 'ایده‌آل برای کسب‌وکارهای بانکداری و خدمات مالی، صرافی‌های رمزارز، پلتفرم‌های اقتصاد مشارکتی، لندتک‌ها',
			'q'    => 'احراز هویت تصویری ویدیو سلفی لایونس زنده بودن تطبیق چهره بایومتریک ekyc liveness face',
		),
		array(
			'key' => 'shahkar', 'cat' => 'mobile identity',
			'name' => 'وب‌سرویس شاهکار (تطبیق شماره موبایل با کد ملی)', 'short' => 'شاهکار', 'latin' => 'Shahkar · SIM Ownership Match',
			'slug' => '/api-shahkar/',
			'desc' => 'وب‌سرویس شاهکار لایه اولیه و حیاتی احراز هویت در تمامی سامانه‌های آنلاین است. این سرویس مالکیت سیم‌کارت را با کد ملی واردشده در لحظه استعلام کرده و مشخص می‌کند آیا شماره موبایل اعلام‌شده به نام خود فرد ثبت شده است یا خیر. این فرایند جلوی ورود کاربران با هویت مستعار و کلاهبرداری‌های اینترنتی را می‌گیرد.',
			'use'  => 'ایده‌آل برای پلتفرم‌های تجارت الکترونیک و اپلیکیشن‌های ثبت‌نام آنلاین',
			'q'    => 'شاهکار سیم کارت شماره موبایل مالکیت کد ملی ثبت نام هویت مستعار shahkar sim',
		),
		array(
			'key' => 'civil', 'cat' => 'identity',
			'name' => 'وب‌سرویس استعلام ثبت احوال (استعلام اطلاعات هویتی)', 'short' => 'ثبت احوال', 'latin' => 'Civil Registry · Identity Inquiry',
			'slug' => '/api-inquiry-person/',
			'desc' => 'این وب‌سرویس با دریافت کد ملی و تاریخ تولد، اطلاعات هویتی پایه کاربر شامل نام، نام خانوادگی، نام پدر، وضعیت حیات و وضعیت شناسنامه را به‌صورت برخط و مستقیم از پایگاه داده ثبت احوال استعلام می‌کند. این سرویس خطای تایپی کاربران در ثبت‌نام را صفر کرده و صحت اطلاعات واردشده را تضمین می‌کند.',
			'use'  => 'پر کردن خودکار فرم‌های ثبت‌نام، اعتبارسنجی اطلاعات هویتی در سامانه حقوق و دستمزد و سامانه‌های مالی.',
			'q'    => 'ثبت احوال استعلام هویتی نام نام خانوادگی نام پدر شناسنامه وضعیت حیات کد ملی فرم ثبت نام',
		),
		array(
			'key' => 'iban', 'cat' => 'bank',
			'name' => 'وب‌سرویس استعلام شبا', 'short' => 'استعلام شبا', 'latin' => 'IBAN Inquiry · Account Status',
			'slug' => '/api-inquiry-iban/',
			'desc' => 'این وب‌سرویس بر اساس شماره شبای ۲۴ رقمی، وضعیت واقعی حساب بانکی (فعال، مسدود یا راکد)، نام و نام خانوادگی صاحب یا صاحبان حساب و نام بانک صادرکننده را در لحظه بازمی‌گرداند. این استعلام پیش از هرگونه تسویه‌حساب مالی یا واریز وجه، برگشت خوردن تراکنش‌ها و خطاهای مالی را به صفر می‌رساند.',
			'use'  => 'سامانه‌های تسویه حساب فروشندگان، پلتفرم‌های پرداخت، تسویه‌های حقوقی و مالیاتی.',
			'q'    => 'استعلام شبا حساب بانکی فعال مسدود راکد صاحب حساب بانک تسویه واریز iban sheba',
		),
		array(
			'key' => 'postal', 'cat' => 'address',
			'name' => 'وب‌سرویس استعلام آدرس محل سکونت با کد پستی', 'short' => 'کد پستی و آدرس', 'latin' => 'Postal Code · Address Lookup',
			'slug' => '/address-postcode-docs/',
			'desc' => 'این وب‌سرویس با دریافت کد پستی ۱۰ رقمی، نشانی کامل متناظر با آن را به‌صورت تفکیک‌شده (استان، شهر، محله، معبر اصلی، معبر فرعی، پلاک، طبقه و واحد) از دیتابیس مرجع پستی دریافت و ثبت می‌کند. این سرویس نیاز به تایپ طولانی نشانی توسط کاربر را برطرف کرده و داده‌های مکانی کسب‌وکارها را کاملاً استاندارد می‌کند.',
			'use'  => 'فروشگاه‌های اینترنتی، سامانه‌های احراز آدرس برای احراز هویت، خدمات ارسال کالا و لجستیک.',
			'q'    => 'کد پستی آدرس نشانی استان شهر معبر پلاک طبقه واحد لجستیک ارسال کالا postal address',
		),
		array(
			'key' => 'card2iban', 'cat' => 'card bank',
			'name' => 'وب‌سرویس تبدیل شماره کارت به شبا', 'short' => 'کارت به شبا', 'latin' => 'Card → IBAN Conversion',
			'slug' => '/api-inquiry-card/',
			'desc' => 'با توجه به این که اکثر کاربران تنها شماره کارت ۱۶ رقمی خود را در دسترس دارند، این وب‌سرویس به‌محض دریافت شماره کارت، شماره شبای ۲۴ رقمی متصل به همان حساب را به‌صورت برخط استخراج می‌کند. این کار مانع از خروج کاربر از سامانه برای پیدا کردن شماره شبا شده و نرخ تبدیل (Conversion Rate) در مراحل مالی را افزایش می‌دهد.',
			'use'  => 'درگاه‌های برداشت وجه، کیف پول‌های الکترونیک، پلتفرم‌های پرداخت اقساطی.',
			'q'    => 'تبدیل کارت به شبا شماره کارت ۱۶ رقمی شبا برداشت وجه کیف پول اقساطی card iban',
		),
		array(
			'key' => 'ibanval', 'cat' => 'bank identity',
			'name' => 'وب‌سرویس تطبیق کد ملی و شماره شبا', 'short' => 'تطبیق شبا و کد ملی', 'latin' => 'IBAN Ownership · Matched / Mismatched',
			'slug' => '/api-validate-iban/',
			'desc' => 'وب‌سرویس صحت تعلق شماره شبای ۲۴ رقمی به کد ملی و تاریخ تولد کاربر را ارزیابی کرده و پاسخ را به‌صورت وضعیت منطقی (Matched یا Mismatched) بازمی‌گرداند. این سرویس راهکار اصلی مبارزه با اجاره حساب‌های بانکی، پولشویی و واریزهای اشتباه در بستر شبکه بانکی است.',
			'use'  => 'صرافی‌های رمزارز، پلتفرم‌های معاملاتی، شرکت‌های بیمه و پرداخت تسهیلات.',
			'q'    => 'تطبیق شبا کد ملی مالکیت حساب اجاره حساب پولشویی صرافی رمزارز بیمه تسهیلات iban ownership',
		),
		array(
			'key' => 'cardval', 'cat' => 'card identity',
			'name' => 'وب‌سرویس تطبیق شماره کارت با کد ملی', 'short' => 'تطبیق کارت و کد ملی', 'latin' => 'Card Ownership · Matched / Mismatched',
			'slug' => '/api-validate-card/',
			'desc' => 'این وب‌سرویس با دریافت شماره کارت ۱۶ رقمی، کد ملی و تاریخ تولد، مالکیت کارت بانکی را به‌صورت سیستمی بررسی می‌کند. این کار لایه امنیتی مهمی پیش از ثبت کارت جهت تراکنش‌های مالی، برداشت وجه و واریزهای آنلاین ایجاد کرده و ریسک استفاده از کارت دیگران را حذف می‌کند.',
			'use'  => 'درگاه‌های پرداخت، کیف پول‌های دیجیتال، سامانه‌های خرید اقساطی و مارکت‌پلیس‌ها.',
			'q'    => 'تطبیق کارت کد ملی مالکیت کارت بانکی درگاه پرداخت کیف پول خرید اقساطی مارکت پلیس card ownership',
		),
	);
}
function uid_render_section_wshcatalog() {
	$tag   = uid_section_tag( 'wshcatalog', 'h2' );
	$items = uid_section_val( 'wshcatalog', 'items', uid_default_wsh_catalog() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	$allowed = $GLOBALS['uid_wsh_catalog_svg_allowed'];
	$chips = uid_section_val( 'wshcatalog', 'chips', uid_default_wsh_catalog_chips() );
	if ( ! is_array( $chips ) || empty( $chips ) ) $chips = uid_default_wsh_catalog_chips();
	?>
	<section class="sec" id="catalog">
	  <div class="wsh-wrap">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'wshcatalog', 'eyebrow', __( 'هشت وب‌سرویس اصلی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshcatalog', 'heading', __( 'فهرست وب‌سرویس‌های احراز هویت و استعلام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'wshcatalog', 'text', __( 'هر کارت شرح سرویس، کاربرد اصلی آن و لینک مستقیم صفحه تخصصی همان سرویس را دارد. با فیلترها یا جست‌وجو، فهرست را به آنچه لازم دارید کوچک کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="cat-bar rv">
	      <div class="cat-chips" id="catChips" role="tablist" aria-label="<?php esc_attr_e( 'فیلتر دسته سرویس', 'uid-theme' ); ?>">
	        <?php foreach ( $chips as $chip ) :
	          $ckey = $chip['key'] ?? 'all';
	          $is_all = ( 'all' === $ckey );
	        ?>
	        <button class="cat-chip<?php echo $is_all ? ' on' : ''; ?>" type="button" data-cat="<?php echo esc_attr( $ckey ); ?>" role="tab" aria-selected="<?php echo $is_all ? 'true' : 'false'; ?>"><?php echo esc_html( $chip['label'] ?? '' ); ?></button>
	        <?php endforeach; ?>
	      </div>
	      <div class="cat-search">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
	        <input id="catQ" type="search" placeholder="<?php esc_attr_e( 'جست‌وجو: شبا، کد پستی، سیم‌کارت…', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'جست‌وجو در فهرست وب‌سرویس‌ها', 'uid-theme' ); ?>">
	      </div>
	      <span class="cat-count" id="catCount"><?php echo esc_html( uid_fa_digits( count( $items ) ) . ' ' . __( 'سرویس', 'uid-theme' ) ); ?></span>
	    </div>

	    <div class="cat-grid rv" id="catGrid">
	      <?php foreach ( $items as $i => $it ) :
	        $key   = sanitize_key( $it['key'] ?? '' );
	        $tone  = uid_wsh_catalog_tone( $key );
	        $qtext = trim( ( $it['name'] ?? '' ) . ' ' . ( $it['q'] ?? '' ) );
	      ?>
	      <article class="cat-card" data-k="<?php echo esc_attr( $key ); ?>" data-cat="<?php echo esc_attr( $it['cat'] ?? '' ); ?>"
	               data-name="<?php echo esc_attr( $it['name'] ?? '' ); ?>" data-slug="<?php echo esc_attr( $it['slug'] ?? '' ); ?>"
	               data-q="<?php echo esc_attr( $qtext ); ?>">
	        <button class="cat-tile" type="button" aria-expanded="false">
	          <span class="ic<?php echo $tone ? ' ' . esc_attr( $tone ) : ' '; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_wsh_catalog_icon_svg( $key ), $allowed ); ?></svg></span>
	          <span class="tt"><b><?php echo esc_html( $it['name'] ?? '' ); ?></b><span class="tshort"><?php echo esc_html( $it['short'] ?? '' ); ?></span><span><?php echo esc_html( $it['latin'] ?? '' ); ?></span></span>
	          <span class="tn"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span><svg class="tchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
	        </button>
	        <div class="cat-body">
	          <p class="d"><?php echo esc_html( $it['desc'] ?? '' ); ?></p>
	          <div class="cat-use">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/></svg>
	            <span class="u"><b><?php esc_html_e( 'کاربرد اصلی:', 'uid-theme' ); ?></b> <?php echo esc_html( $it['use'] ?? '' ); ?></span>
	          </div>
	          <div class="cat-acts">
	            <a class="wsh-btn wsh-btn-navy" href="<?php echo esc_url( $it['slug'] ?? '#' ); ?>"><?php echo esc_html( sprintf( __( 'مشخصات %s', 'uid-theme' ), $it['short'] ?? '' ) ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
	            <button class="cat-try" type="button" data-try="<?php echo esc_attr( $key ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3l14 9-14 9z"/></svg><?php esc_html_e( 'تست در کنسول', 'uid-theme' ); ?></button>
	          </div>
	          <button class="cat-ask" type="button" data-open-modal data-ask="<?php echo esc_attr( $it['name'] ?? '' ); ?>">
	            <?php esc_html_e( 'یا همین سرویس را درخواست دهید', 'uid-theme' ); ?></button>
	        </div>
	      </article>
	      <?php endforeach; ?>
	    </div>

	    <div class="cat-empty" id="catEmpty">
	      <?php esc_html_e( 'هیچ سرویسی با این جست‌وجو پیدا نشد.', 'uid-theme' ); ?>
	      <button type="button" id="catReset"><?php esc_html_e( 'نمایش همه سرویس‌ها', 'uid-theme' ); ?></button>
	    </div>

	    <div class="dir-hint">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg>
	      <?php esc_html_e( 'روی هر کاشی بزنید تا شرح و لینک همان سرویس باز شود', 'uid-theme' ); ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱ — جدول مقایسه سرویس‌ها
 * ===================================================================== */
function uid_default_wsh_cmp() {
	return array(
		array( 'name' => 'احراز هویت تصویری با ویدیوی سلفی', 'question' => 'آیا این شخص واقعاً همان صاحب کد ملی است و زنده مقابل دوربین است؟', 'input' => 'کد ملی، تاریخ تولد، ویدیوی سلفی', 'link' => '/api/' ),
		array( 'name' => 'شاهکار', 'question' => 'آیا این شماره موبایل به نام همین کد ملی ثبت شده است؟', 'input' => 'شماره موبایل، کد ملی', 'link' => '/api-shahkar/' ),
		array( 'name' => 'استعلام ثبت احوال', 'question' => 'مشخصات هویتی پایه این کد ملی چیست؟', 'input' => 'کد ملی، تاریخ تولد', 'link' => '/api-inquiry-person/' ),
		array( 'name' => 'استعلام شبا', 'question' => 'این حساب بانکی فعال است و به نام چه کسی است؟', 'input' => 'شماره شبای ۲۴ رقمی', 'link' => '/api-inquiry-iban/' ),
		array( 'name' => 'استعلام آدرس با کد پستی', 'question' => 'نشانی کامل متناظر با این کد پستی چیست؟', 'input' => 'کد پستی ۱۰ رقمی', 'link' => '/address-postcode-docs/' ),
		array( 'name' => 'تبدیل شماره کارت به شبا', 'question' => 'شبای متصل به این شماره کارت چیست؟', 'input' => 'شماره کارت ۱۶ رقمی', 'link' => '/api-inquiry-card/' ),
		array( 'name' => 'تطبیق کد ملی و شماره شبا', 'question' => 'آیا این شبا واقعاً به همین کد ملی تعلق دارد؟', 'input' => 'شبا، کد ملی، تاریخ تولد', 'link' => '/api-validate-iban/' ),
		array( 'name' => 'تطبیق شماره کارت با کد ملی', 'question' => 'آیا این کارت بانکی متعلق به همین شخص است؟', 'input' => 'شماره کارت، کد ملی، تاریخ تولد', 'link' => '/api-validate-card/' ),
	);
}
function uid_render_section_wshcmp() {
	$tag   = uid_section_tag( 'wshcmp', 'h2' );
	$items = uid_section_val( 'wshcmp', 'items', uid_default_wsh_cmp() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="cmp" data-fold data-fold-hot data-fold-min="۲ دقیقه"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'wshcmp', 'fold_title', __( 'کدام سرویس به کدام سوال پاسخ می‌دهد', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'wshcmp', 'fold_teaser', __( 'مقایسه ورودی، خروجی و کاربرد هشت وب‌سرویس اصلی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="wsh-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'wshcmp', 'eyebrow', __( 'یک نگاه، هشت سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshcmp', 'heading', __( 'کدام وب‌سرویس به کدام سوال پاسخ می‌دهد؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'wshcmp', 'text', __( 'اگر هنوز نمی‌دانید کدام سرویس را لازم دارید، این جدول کوتاه‌ترین مسیر است: ستون «پاسخ به چه سوالی» را بخوانید و روی همان ردیف کلیک کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="cmp rv">
	      <div class="r hd"><span><?php esc_html_e( 'وب‌سرویس', 'uid-theme' ); ?></span><span><?php esc_html_e( 'پاسخ به چه سوالی', 'uid-theme' ); ?></span><span><?php esc_html_e( 'ورودی اصلی', 'uid-theme' ); ?></span><span><?php esc_html_e( 'صفحه سرویس', 'uid-theme' ); ?></span></div>
	      <?php foreach ( $items as $row ) : ?>
	      <div class="r"><span><b><?php echo esc_html( $row['name'] ?? '' ); ?></b></span>
	        <span><?php echo esc_html( $row['question'] ?? '' ); ?></span>
	        <span class="mono"><?php echo esc_html( $row['input'] ?? '' ); ?></span>
	        <span><a href="<?php echo esc_url( $row['link'] ?? '#' ); ?>"><?php esc_html_e( 'مشاهده', 'uid-theme' ); ?><?php echo uid_wsh_chev_icon(); ?></a></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۲ — وب‌سرویس‌های مکمل و تخصصی (۶ مورد اسمی)
 * ===================================================================== */
function uid_default_wsh_extra() {
	return array(
		array( 'icon' => 'card', 'title' => 'وب‌سرویس استعلام شماره کارت' ),
		array( 'icon' => 'deed', 'title' => 'وب‌سرویس استعلام سند ملکی' ),
		array( 'icon' => 'transaction', 'title' => 'وب‌سرویس اعتبارسنجی معاملاتی' ),
		array( 'icon' => 'license', 'title' => 'وب‌سرویس استعلام جواز کسب' ),
		array( 'icon' => 'company', 'title' => 'وب‌سرویس استعلام اطلاعات ثبتی شرکت' ),
		array( 'icon' => 'criminal', 'title' => 'وب‌سرویس استعلام سوء پیشینه' ),
	);
}
function uid_render_section_wshextra() {
	$tag   = uid_section_tag( 'wshextra', 'h2' );
	$items = uid_section_val( 'wshextra', 'items', uid_default_wsh_extra() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="extra" data-fold data-fold-hot data-fold-min="۱ دقیقه"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'wshextra', 'fold_title', __( 'وب‌سرویس‌های مکمل و تخصصی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'wshextra', 'fold_teaser', __( 'شش سرویس استعلامی دیگر — سند ملکی، جواز کسب، سوء پیشینه و…', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="wsh-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'wshextra', 'eyebrow', __( 'معرفی اسمی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshextra', 'heading', __( 'وب‌سرویس‌های مکمل و تخصصی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'wshextra', 'text', __( 'این شش وب‌سرویس در کنار سرویس‌های اصلی ارائه می‌شوند. برای دریافت مشخصات فنی، نمونه پاسخ و تعرفه هرکدام، درخواست خود را ثبت کنید تا کارشناس یوآیدی با شما تماس بگیرد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="mini-grid rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="mini"><div class="mh">
	        <span class="mic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_wsh_extra_icon_svg( $it['icon'] ?? 'card' ), $GLOBALS['uid_wsh_catalog_svg_allowed'] ); ?></svg></span>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b></div>
	        <button class="mbtn" type="button" data-open-modal data-ask="<?php echo esc_attr( $it['title'] ?? '' ); ?>">
	          <?php echo uid_wsh_submit_icon(); ?><?php esc_html_e( 'درخواست سرویس', 'uid-theme' ); ?></button></div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۳ — چرا وب‌سرویس‌های یوآیدی (۳ کارت مزیت)
 * ===================================================================== */
function uid_default_wsh_adv() {
	return array(
		array( 'icon' => 'fast', 'title' => 'پاسخ‌دهی در لحظه', 'text' => 'سرعت بالای پردازش، مناسب برای سامانه‌های پرتراکنش.', 'tagline' => 'مناسب جریان‌های ثبت‌نام و پرداخت برخط' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی فنی اختصاصی', 'text' => 'همراهی تیم فنی در مراحل تست و پیاده‌سازی.', 'tagline' => 'از اولین فراخوانی تا عملیاتی‌سازی' ),
		array( 'icon' => 'sandbox', 'title' => 'محیط سندباکس (SandBox)', 'text' => 'تست کامل تمام API‌ها پیش از خرید و عملیاتی‌سازی.', 'tagline' => 'کلید آزمایشی رایگان با ثبت‌نام در پنل توسعه‌دهندگان' ),
	);
}
function uid_render_section_wshadv() {
	$tag   = uid_section_tag( 'wshadv', 'h2' );
	$items = uid_section_val( 'wshadv', 'items', uid_default_wsh_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="adv" data-fold data-fold-hot data-fold-min="۱ دقیقه"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'wshadv', 'fold_title', __( 'چرا وب‌سرویس‌های یوآیدی؟', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'wshadv', 'fold_teaser', __( 'پاسخ‌دهی در لحظه، پشتیبانی فنی اختصاصی و محیط سندباکس', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="wsh-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'wshadv', 'eyebrow', __( 'مزایای زیرساختی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshadv', 'heading', __( 'چرا وب‌سرویس‌های یوآیدی؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="grid g3 rail rv" data-rail="adv">
	      <?php foreach ( $items as $it ) :
	        $key = $it['icon'] ?? 'fast'; $tone = uid_wsh_adv_tone( $key );
	      ?>
	      <div class="dcard">
	        <span class="ic<?php echo $tone ? ' ' . esc_attr( $tone ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_wsh_adv_icon_svg( $key ), $GLOBALS['uid_wsh_catalog_svg_allowed'] ); ?></svg></span>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="tagline"><?php echo uid_wsh_check_icon(); ?><?php echo esc_html( $it['tagline'] ?? '' ); ?></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="adv"></div>
	    <div class="dir-hint" style="display:none">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg>
	      <?php esc_html_e( 'برای دیدن کارت بعدی، بکشید', 'uid-theme' ); ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۴ — از درخواست تا اولین استعلام واقعی (۳ مرحله)
 * ===================================================================== */
function uid_default_wsh_start() {
	return array(
		array( 'title' => 'ثبت درخواست سرویس', 'text' => 'فرم درخواست سرویس را پر کنید یا با کارشناسان ما تماس بگیرید تا سرویس یا ترکیب سرویس‌های موردنیاز شما مشخص شود.' ),
		array( 'title' => 'دریافت کلید سندباکس', 'text' => 'با ثبت‌نام در پنل توسعه‌دهندگان یوآیدی، کلید دسترسی به محیط آزمایشی به‌صورت رایگان در اختیار شما قرار می‌گیرد.' ),
		array( 'title' => 'عملیاتی‌سازی روی یک کلید', 'text' => 'پس از تست، چند API به‌صورت هم‌زمان روی یک کلید دسترسی (API Key) فعال می‌شود و سرویس وارد مدار می‌شود.' ),
	);
}
function uid_render_section_wshstart() {
	$tag   = uid_section_tag( 'wshstart', 'h2' );
	$items = uid_section_val( 'wshstart', 'items', uid_default_wsh_start() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="start" data-fold data-fold-min="۱ دقیقه"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'wshstart', 'fold_title', __( 'از درخواست تا اولین استعلام واقعی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'wshstart', 'fold_teaser', __( 'سه مرحله: درخواست سرویس، کلید سندباکس، عملیاتی‌سازی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="wsh-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'wshstart', 'eyebrow', __( 'نحوه شروع', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshstart', 'heading', __( 'از درخواست تا اولین استعلام واقعی، سه مرحله', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="flow3 rv">
	      <?php foreach ( $items as $i => $it ) : ?>
	      <div class="fl-node"><div class="fl-card">
	        <span class="fl-num"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span>
	        <h4><?php echo esc_html( $it['title'] ?? '' ); ?></h4>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۵ — سوالات متداول
 * ===================================================================== */
function uid_default_wsh_faq() {
	return array(
		array( 'q' => 'چگونه می‌توانم وب‌سرویس‌ها را پیش از خرید تست کنم؟', 'a' => 'با ثبت‌نام در پنل توسعه‌دهندگان یوآیدی، کلید دسترسی به محیط آزمایشی (سندباکس) به‌صورت رایگان در اختیار شما قرار می‌گیرد.' ),
		array( 'q' => 'آیا امکان دریافت چند وب‌سرویس در قالب یک پکیج وجود دارد؟', 'a' => 'بله؛ امکان فعال‌سازی هم‌زمان چند API روی یک کلید دسترسی (API Key) فراهم است.' ),
		array( 'q' => 'پایداری وب‌سرویس‌ها (SLA) چگونه تضمین می‌شود؟', 'a' => 'تمامی وب‌سرویس‌ها روی زیرساخت توزیع‌شده با مانیتورینگ ۲۴/۷ قرار دارند و پایداری ۹۹.۹٪ را تضمین می‌کنند.' ),
	);
}
function uid_render_section_wshfaq() {
	$tag   = uid_section_tag( 'wshfaq', 'h2' );
	$items = uid_section_val( 'wshfaq', 'items', uid_default_wsh_faq() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="faq" data-fold data-fold-hot data-fold-min="۱ دقیقه"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'wshfaq', 'fold_title', __( 'سوالات متداول عمومی وب‌سرویس‌ها', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'wshfaq', 'fold_teaser', __( 'تست پیش از خرید، پکیج چندسرویسی و تضمین پایداری', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="wsh-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="wsh-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'wshfaq', 'eyebrow', __( 'پیش از تماس، این‌ها را بخوانید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'wshfaq', 'heading', __( 'سوالات متداول عمومی وب‌سرویس‌ها', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="faq rv" style="max-width:900px;margin-inline:auto">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i"><button class="faq-q" aria-expanded="false"><span class="fq-tx"><?php echo esc_html( $it['q'] ?? '' ); ?></span><span class="pm"></span></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['a'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * بند تماس میان‌صفحه
 * ===================================================================== */
function uid_render_section_wshcallband() {
	?>
	<section class="sec" style="padding-block:var(--sec) 0">
	  <div class="wsh-wrap">
	    <div class="callband rv">
	      <div class="ic"><?php echo uid_wsh_phone_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'wshcallband', 'heading', __( 'هنوز مطمئن نیستید کدام ترکیب از سرویس‌ها را لازم دارید؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'wshcallband', 'text', __( 'برای راهنمایی سرویس‌ها و دریافت اطلاعات بیشتر، از طریق فرم درخواست سرویس یا تماس با کارشناسان ما ارتباط بگیرید. مسیر استعلام شما را با هم بررسی می‌کنیم و همان سرویس‌هایی را پیشنهاد می‌دهیم که واقعاً لازم دارید.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
        <a class="wsh-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_wsh_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_raw() ); ?></span></a>
	        <button class="wsh-btn btn-ghost-d" data-open-modal><?php echo esc_html( uid_section_val( 'wshcallband', 'btn_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم درخواست وب‌سرویس)
 * گزینه‌های کشویی «کدام سرویس را لازم دارید» و منطق مسیریابی افراد حقیقی
 * (data-route) کاملاً جاوااسکریپتی و به همین ترتیب ثابت‌اند — از این صفحه
 * قابل‌ویرایش نیستند.
 * ===================================================================== */
function uid_default_wsh_lead_options() {
	return array(
		array( 'value' => 'ekyc', 'label' => 'احراز هویت تصویری با ویدیوی سلفی' ),
		array( 'value' => 'shahkar', 'label' => 'شاهکار — تطبیق موبایل و کد ملی' ),
		array( 'value' => 'civil', 'label' => 'استعلام ثبت احوال' ),
		array( 'value' => 'iban', 'label' => 'استعلام شبا' ),
		array( 'value' => 'postal', 'label' => 'استعلام آدرس با کد پستی' ),
		array( 'value' => 'card2iban', 'label' => 'تبدیل شماره کارت به شبا' ),
		array( 'value' => 'ibanval', 'label' => 'تطبیق کد ملی و شماره شبا' ),
		array( 'value' => 'cardval', 'label' => 'تطبیق شماره کارت با کد ملی' ),
		array( 'value' => 'extra', 'label' => 'یکی از سرویس‌های مکمل و تخصصی' ),
		array( 'value' => 'unsure', 'label' => 'هنوز مطمئن نیستم — راهنمایی می‌خواهم' ),
		array( 'value' => 'ind', 'label' => 'کاربر شخصی هستم (کسب‌وکار نیستم)' ),
	);
}
function uid_render_section_wshlead() {
	$trust = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'wshlead', 'trust', "مشاوره رایگان، بدون تعهد\nکلید سندباکس رایگان پیش از خرید\nچند API روی یک کلید دسترسی\nپشتیبانی فنی اختصاصی در زمان پیاده‌سازی" ) ) ) );
	$select_placeholder = uid_section_val( 'wshlead', 'select_placeholder', __( 'کدام سرویس را لازم دارید؟…', 'uid-theme' ) );
	$select_options = uid_section_val( 'wshlead', 'select_options', uid_default_wsh_lead_options() );
	if ( ! is_array( $select_options ) || empty( $select_options ) ) $select_options = uid_default_wsh_lead_options();
	?>
	<section class="sec" id="lead">
	  <div class="wsh-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="wsh-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'wshlead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <h2><?php echo esc_html( uid_section_val( 'wshlead', 'heading', __( 'فرم درخواست وب‌سرویس‌های احراز هویت یوآیدی', 'uid-theme' ) ) ); ?></h2>
	          <p><?php echo esc_html( uid_section_val( 'wshlead', 'text', __( 'برای راهنمایی سرویس‌ها و دریافت اطلاعات بیشتر، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی سرویس یا پکیج مناسب کسب‌وکار شما را بررسی می‌کنند و کلید دسترسی محیط آزمایشی را در اختیارتان می‌گذارند.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_wsh_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'wshlead', 'form_heading', __( 'درخواست وب‌سرویس', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'wshlead', 'form_hint', __( 'چهار فیلد کوتاه. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="web-services-hub">
	            <input type="hidden" name="service" id="leadService" value="">
	            <div class="frow">
	              <div class="fld"><input name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	                <span class="err"><?php esc_html_e( 'نام را وارد کنید.', 'uid-theme' ); ?></span></div>
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric" placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	            </div>
	            <div class="fld"><input name="business" type="text" placeholder="<?php esc_attr_e( 'نام کسب‌وکار', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'نام کسب‌وکار را وارد کنید.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><select name="need" data-req data-route>
	                <option value=""><?php echo esc_html( $select_placeholder ); ?></option>
	                <?php foreach ( $select_options as $opt ) : ?>
	                <option value="<?php echo esc_attr( $opt['value'] ?? '' ); ?>"><?php echo esc_html( $opt['label'] ?? '' ); ?></option>
	                <?php endforeach; ?>
	              </select><span class="err"><?php esc_html_e( 'یک گزینه را انتخاب کنید.', 'uid-theme' ); ?></span></div>
	            <div class="route-alert" id="routeAlert"><?php echo uid_wsh_info_icon(); ?>
	              <span><?php esc_html_e( 'این وب‌سرویس‌ها فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی دنبال خدمات احراز هویت هستید، ', 'uid-theme' ); ?><a href="/sana/"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a><?php esc_html_e( ' در دسترس شماست.', 'uid-theme' ); ?></span></div>
	            <button class="wsh-btn btn-cta btn-block" type="button" data-submit><?php echo uid_wsh_submit_icon(); ?><?php echo esc_html( uid_section_val( 'wshlead', 'btn_text', __( 'ارسال درخواست و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_wsh_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'wshlead', 'note', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_wsh_check_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'wshlead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_raw() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ساخت خودکار برگه واقعی وردپرس + تنظیم اسلاگ
 * ===================================================================== */
function uid_get_wsh_page_id() {
	$page_id = (int) get_option( 'uid_wsh_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_WSH_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_wsh_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_wsh_page() {
	if ( uid_get_wsh_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'وب‌سرویس‌های احراز هویت', 'uid-theme' ),
		'post_name'   => 'web-services',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_WSH_TEMPLATE );
	update_option( 'uid_wsh_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_wsh_page' );

function uid_ensure_wsh_page_once() {
	if ( get_option( 'uid_wsh_page_bootstrapped' ) ) return;
	uid_ensure_wsh_page();
	update_option( 'uid_wsh_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_wsh_page_once' );

function uid_register_wsh_slug_setting() {
	register_setting( 'uid_wsh_group', 'uid_wsh_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_wsh_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_wsh_page_slug_section', '', '__return_false', 'uid_wsh_layout' );
	add_settings_field( 'uid_wsh_page_slug', __( 'آدرس (اسلاگ) صفحه فهرست وب‌سرویس‌ها', 'uid-theme' ), 'uid_field_wsh_page_slug', 'uid_wsh_layout', 'uid_wsh_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_wsh_slug_setting' );

function uid_field_wsh_page_slug( $args ) {
	$page_id = uid_get_wsh_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_wsh_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_wsh_page_slug" value="<?php echo esc_attr( $slug ); ?>">
	<?php if ( $page_id ) : ?>
		<p class="description">
			<?php
			printf(
				esc_html__( 'آدرس فعلی: %1$s — این برگه را می‌توانید مستقیماً از پیشخوان ← برگه‌ها هم ویرایش کنید (%2$s).', 'uid-theme' ),
				'<code dir="ltr">' . esc_html( trailingslashit( home_url( '/' . $slug ) ) ) . '</code>',
				'<a href="' . esc_url( get_edit_post_link( $page_id, '' ) ) . '">' . esc_html__( 'ویرایش برگه', 'uid-theme' ) . '</a>'
			);
			?>
		</p>
	<?php else : ?>
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه فهرست وب‌سرویس‌ها هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_wsh_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_wsh_page_id();

	if ( $page_id && $slug ) {
		$post = get_post( $page_id );
		if ( $post && $post->post_name !== $slug ) {
			wp_update_post( array( 'ID' => $page_id, 'post_name' => $slug ) );
			flush_rewrite_rules( false );
		}
	}

	return $page_id ? get_post_field( 'post_name', $page_id ) : $slug;
}

/* =====================================================================
 * گزینه‌های آیکون ریپیترها
 * ===================================================================== */
function uid_wsh_extra_icon_options() {
	return array(
		'card'        => __( 'کارت بانکی', 'uid-theme' ),
		'deed'        => __( 'سند ملکی', 'uid-theme' ),
		'transaction' => __( 'اعتبارسنجی معاملاتی', 'uid-theme' ),
		'license'     => __( 'جواز کسب', 'uid-theme' ),
		'company'     => __( 'اطلاعات ثبتی شرکت', 'uid-theme' ),
		'criminal'    => __( 'سوء پیشینه', 'uid-theme' ),
	);
}
function uid_wsh_adv_icon_options() {
	return array(
		'fast'    => __( 'سرعت پاسخ‌دهی', 'uid-theme' ),
		'support' => __( 'پشتیبانی فنی', 'uid-theme' ),
		'sandbox' => __( 'سندباکس', 'uid-theme' ),
	);
}
function uid_wsh_catalog_key_options() {
	return array(
		'ekyc'      => __( 'احراز هویت تصویری', 'uid-theme' ),
		'shahkar'   => __( 'شاهکار', 'uid-theme' ),
		'civil'     => __( 'ثبت احوال', 'uid-theme' ),
		'iban'      => __( 'استعلام شبا', 'uid-theme' ),
		'postal'    => __( 'کد پستی و آدرس', 'uid-theme' ),
		'card2iban' => __( 'کارت به شبا', 'uid-theme' ),
		'ibanval'   => __( 'تطبیق شبا و کد ملی', 'uid-theme' ),
		'cardval'   => __( 'تطبیق کارت و کد ملی', 'uid-theme' ),
	);
}

/* =====================================================================
 * ثبت تنظیمات پیشخوان برای همه‌ی سکشن‌های این صفحه
 * ===================================================================== */
function uid_register_wsh_settings() {
	register_setting( 'uid_wsh_group', 'uid_wsh_layout', array(
		'sanitize_callback' => 'uid_sanitize_wsh_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_wsh_layout_main', '', '__return_false', 'uid_wsh_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_wsh_layout', 'uid_wsh_layout_main', array(
		'option_name' => 'uid_wsh_layout', 'registry_fn' => 'uid_wsh_sections_registry', 'layout_fn' => 'uid_get_wsh_layout',
	) );

	/* ---------------- هیرو + مسیریاب ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshhero', array( 'sanitize_callback' => 'uid_sanitize_section_wshhero', 'default' => array() ) );
	add_settings_section( 'uid_section_wshhero_main', '', '__return_false', 'uid_section_wshhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'eyebrow', 'default' => 'فهرست کامل وب‌سرویس‌های یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است؛ برچسب لاتین API همیشه بعد از آن می‌آید)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'heading', 'default' => 'خدمات <mark>وب‌سرویس احراز هویت</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'text', 'default' => 'یوآیدی به‌عنوان اولین اپراتور احراز هویت ایران، پس از ارائه انواع API وب‌سرویس‌های احراز هویت دیجیتال و به‌منظور سهولت دسترسی و امنیت کامل کسب‌وکارها، وب‌سرویس احراز هویت بایومتریک را توسعه داده است. در این صفحه همه این وب‌سرویس‌ها را می‌بینید و می‌توانید پیش از هر تماسی، هرکدام را همین‌جا تست کنید.', 'rows' => 4 ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (پرش به فهرست سرویس‌ها)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'btn1_text', 'default' => 'دیدن فهرست وب‌سرویس‌ها' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'group' => 'uid_section_wshhero', 'key' => 'tags', 'default' => "۸ وب‌سرویس با نمونه تست زنده\n۶ وب‌سرویس مکمل و تخصصی\nیک کلید API برای چند سرویس" ) );
	add_settings_field( 'router_items', __( 'گزینه‌های مسیریاب (فقط برچسب قابل‌ویرایش است)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshhero', 'uid_section_wshhero_main', array(
		'group' => 'uid_section_wshhero', 'key' => 'router_items', 'default' => uid_default_wsh_router(), 'add_label' => __( 'افزودن گزینه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'key', 'type' => 'select', 'label' => __( 'نوع', 'uid-theme' ), 'options' => uid_wsh_router_key_options(), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
		),
	) );
	add_settings_field( 'router_notice', '', 'uid_field_notice', 'uid_section_wshhero', 'uid_section_wshhero_main', array( 'text' => __( 'مسیریاب انتخاب سرویس (سمت راست هیرو) تعاملی و جاوااسکریپتی است؛ فقط برچسب/ترتیب گزینه‌ها از این صفحه قابل‌ویرایش است — منطق نمایش نتیجه در assets/js/web-services-hub-page.js تعریف شده و اگر «نوع» یک گزینه را تغییر دهید، ممکن است نتیجه‌اش خالی نمایش داده شود.', 'uid-theme' ) ) );

	/* ---------------- باند عملیاتی ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshstats', array( 'sanitize_callback' => 'uid_sanitize_section_wshstats', 'default' => array() ) );
	add_settings_section( 'uid_section_wshstats_main', '', '__return_false', 'uid_section_wshstats' );
	add_settings_field( 'items', __( 'آمار (خانه‌های باند)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshstats', 'uid_section_wshstats_main', array(
		'group' => 'uid_section_wshstats', 'key' => 'items', 'default' => uid_default_wsh_stats(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار (عدد یا عبارت کوتاه)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- کپسول ۳۰ ثانیه‌ای ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshtldr', array( 'sanitize_callback' => 'uid_sanitize_section_wshtldr', 'default' => array() ) );
	add_settings_section( 'uid_section_wshtldr_main', '', '__return_false', 'uid_section_wshtldr' );
	add_settings_field( 'heading', __( 'عنوان کپسول', 'uid-theme' ), 'uid_field_text', 'uid_section_wshtldr', 'uid_section_wshtldr_main', array( 'group' => 'uid_section_wshtldr', 'key' => 'heading', 'default' => 'اگر عجله دارید، همین چهار خط کافی است' ) );
	add_settings_field( 'badge', __( 'برچسب کوچک (مثل «۳۰ ثانیه»)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshtldr', 'uid_section_wshtldr_main', array( 'group' => 'uid_section_wshtldr', 'key' => 'badge', 'default' => '۳۰ ثانیه' ) );
	add_settings_field( 'items', __( 'خطوط خلاصه (هر خط یک مورد؛ تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshtldr', 'uid_section_wshtldr_main', array( 'group' => 'uid_section_wshtldr', 'key' => 'items', 'default' => implode( "\n", uid_default_wsh_tldr() ), 'rows' => 5 ) );
	add_settings_field( 'btn_text', __( 'متن دکمه دوم (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshtldr', 'uid_section_wshtldr_main', array( 'group' => 'uid_section_wshtldr', 'key' => 'btn_text', 'default' => 'فرم درخواست سرویس' ) );
	add_settings_field( 'more_text', __( 'متن پرش نرم به فهرست سرویس‌ها', 'uid-theme' ), 'uid_field_text', 'uid_section_wshtldr', 'uid_section_wshtldr_main', array( 'group' => 'uid_section_wshtldr', 'key' => 'more_text', 'default' => 'و اگر می‌خواهید خودتان فهرست را ببینید' ) );

	/* ---------------- کنسول تست مشترک ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshconsole', array( 'sanitize_callback' => 'uid_sanitize_section_wshconsole', 'default' => array() ) );
	add_settings_section( 'uid_section_wshconsole_main', '', '__return_false', 'uid_section_wshconsole' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array( 'group' => 'uid_section_wshconsole', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array( 'group' => 'uid_section_wshconsole', 'key' => 'eyebrow', 'default' => 'کنسول تست زنده' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array( 'group' => 'uid_section_wshconsole', 'key' => 'heading', 'default' => 'هشت وب‌سرویس، یک کنسول' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array( 'group' => 'uid_section_wshconsole', 'key' => 'text', 'default' => 'سرویس موردنظرتان را از نوار زیر انتخاب کنید تا ورودی‌ها، سناریوهای تست و پاسخ همان سرویس در همین پنل نمایش داده شود. داده‌ها نمونه است و هیچ استعلام واقعی انجام نمی‌شود.' ) );
	add_settings_field( 'services', __( 'برچسب‌ها و مقادیر پیش‌فرض کنسول (به ازای هر سرویس)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array(
		'group' => 'uid_section_wshconsole', 'key' => 'services', 'default' => uid_default_wsh_console_overrides(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'key', 'type' => 'select', 'label' => __( 'سرویس', 'uid-theme' ), 'options' => uid_wsh_catalog_key_options(), 'required' => true ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'عنوان کنسول', 'uid-theme' ) ),
			array( 'key' => 'sub', 'type' => 'textarea', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
			array( 'key' => 'run_label', 'type' => 'text', 'label' => __( 'متن دکمه اجرا', 'uid-theme' ) ),
			array( 'key' => 'go_name', 'type' => 'text', 'label' => __( 'متن لینک «مشاهده صفحه این سرویس»', 'uid-theme' ) ),
			array( 'key' => 'f1_label', 'type' => 'text', 'label' => __( 'فیلد ۱ — برچسب', 'uid-theme' ) ),
			array( 'key' => 'f1_ph', 'type' => 'text', 'label' => __( 'فیلد ۱ — جای‌نگه‌دار', 'uid-theme' ) ),
			array( 'key' => 'f1_val', 'type' => 'text', 'label' => __( 'فیلد ۱ — مقدار پیش‌فرض', 'uid-theme' ) ),
			array( 'key' => 'f2_label', 'type' => 'text', 'label' => __( 'فیلد ۲ — برچسب (در صورت وجود)', 'uid-theme' ) ),
			array( 'key' => 'f2_ph', 'type' => 'text', 'label' => __( 'فیلد ۲ — جای‌نگه‌دار', 'uid-theme' ) ),
			array( 'key' => 'f2_val', 'type' => 'text', 'label' => __( 'فیلد ۲ — مقدار پیش‌فرض', 'uid-theme' ) ),
			array( 'key' => 'f3_label', 'type' => 'text', 'label' => __( 'فیلد ۳ — برچسب (در صورت وجود)', 'uid-theme' ) ),
			array( 'key' => 'f3_ph', 'type' => 'text', 'label' => __( 'فیلد ۳ — جای‌نگه‌دار', 'uid-theme' ) ),
			array( 'key' => 'f3_val', 'type' => 'text', 'label' => __( 'فیلد ۳ — مقدار پیش‌فرض', 'uid-theme' ) ),
			array( 'key' => 't1_label', 'type' => 'text', 'label' => __( 'دکمه تست نمونه ۱ — برچسب', 'uid-theme' ) ),
			array( 'key' => 't2_label', 'type' => 'text', 'label' => __( 'دکمه تست نمونه ۲ — برچسب (در صورت وجود)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'console_notice', '', 'uid_field_notice', 'uid_section_wshconsole', 'uid_section_wshconsole_main', array( 'text' => __( 'فقط برچسب‌ها، جای‌نگه‌دارها و مقادیر پیش‌فرض فیلدهای کنسول از این صفحه قابل‌ویرایش‌اند؛ تعداد/ترتیب فیلدها و منطق محاسبه پاسخ (سناریوها و JSON خروجی) در assets/js/web-services-hub-page.js می‌ماند. اگر برای سرویسی فیلد یا تست دومی وجود ندارد، آن ردیف را خالی بگذارید.', 'uid-theme' ) ) );

	/* ---------------- فهرست/کاتالوگ ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshcatalog', array( 'sanitize_callback' => 'uid_sanitize_section_wshcatalog', 'default' => array() ) );
	add_settings_section( 'uid_section_wshcatalog_main', '', '__return_false', 'uid_section_wshcatalog' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array( 'group' => 'uid_section_wshcatalog', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array( 'group' => 'uid_section_wshcatalog', 'key' => 'eyebrow', 'default' => 'هشت وب‌سرویس اصلی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array( 'group' => 'uid_section_wshcatalog', 'key' => 'heading', 'default' => 'فهرست وب‌سرویس‌های احراز هویت و استعلام' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array( 'group' => 'uid_section_wshcatalog', 'key' => 'text', 'default' => 'هر کارت شرح سرویس، کاربرد اصلی آن و لینک مستقیم صفحه تخصصی همان سرویس را دارد. با فیلترها یا جست‌وجو، فهرست را به آنچه لازم دارید کوچک کنید.' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array(
		'group' => 'uid_section_wshcatalog', 'key' => 'items', 'default' => uid_default_wsh_catalog(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'key', 'type' => 'select', 'label' => __( 'سرویس (تعیین‌کننده آیکون و اتصال به کنسول تست)', 'uid-theme' ), 'options' => uid_wsh_catalog_key_options(), 'required' => true ),
			array( 'key' => 'cat', 'type' => 'text', 'label' => __( 'دسته‌های فیلتر (جداشده با فاصله؛ از میان identity/mobile/bank/card/address)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام کامل سرویس', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'short', 'type' => 'text', 'label' => __( 'نام کوتاه', 'uid-theme' ) ),
			array( 'key' => 'latin', 'type' => 'text', 'label' => __( 'زیرنویس لاتین', 'uid-theme' ) ),
			array( 'key' => 'slug', 'type' => 'text', 'label' => __( 'آدرس صفحه تخصصی سرویس', 'uid-theme' ) ),
			array( 'key' => 'desc', 'type' => 'textarea', 'label' => __( 'توضیح سرویس', 'uid-theme' ) ),
			array( 'key' => 'use', 'type' => 'textarea', 'label' => __( 'کاربرد اصلی', 'uid-theme' ) ),
			array( 'key' => 'q', 'type' => 'textarea', 'label' => __( 'کلیدواژه‌های جست‌وجو (اختیاری)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'catalog_notice', '', 'uid_field_notice', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array( 'text' => __( 'فیلد «سرویس» هر کارت هم آیکون آن را تعیین می‌کند و هم آن را به سرویس متناظر در کنسول تست بالای صفحه وصل می‌کند؛ لطفاً برای هر کارت یک سرویس متفاوت انتخاب کنید.', 'uid-theme' ) ) );
	add_settings_field( 'chips', __( 'چیپ‌های فیلتر دسته‌بندی (فقط برچسب قابل‌ویرایش است)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshcatalog', 'uid_section_wshcatalog_main', array(
		'group' => 'uid_section_wshcatalog', 'key' => 'chips', 'default' => uid_default_wsh_catalog_chips(), 'add_label' => __( 'افزودن چیپ', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'key', 'type' => 'select', 'label' => __( 'دسته', 'uid-theme' ), 'options' => uid_wsh_catalog_chip_key_options(), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب چیپ', 'uid-theme' ), 'required' => true ),
		),
	) );

	/* ---------------- فصل ۱: جدول مقایسه ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshcmp', array( 'sanitize_callback' => 'uid_sanitize_section_wshcmp', 'default' => array() ) );
	add_settings_section( 'uid_section_wshcmp_main', '', '__return_false', 'uid_section_wshcmp' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'eyebrow', 'default' => 'یک نگاه، هشت سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'heading', 'default' => 'کدام وب‌سرویس به کدام سوال پاسخ می‌دهد؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'text', 'default' => 'اگر هنوز نمی‌دانید کدام سرویس را لازم دارید، این جدول کوتاه‌ترین مسیر است: ستون «پاسخ به چه سوالی» را بخوانید و روی همان ردیف کلیک کنید.' ) );
	add_settings_field( 'items', __( 'ردیف‌های جدول', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array(
		'group' => 'uid_section_wshcmp', 'key' => 'items', 'default' => uid_default_wsh_cmp(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام وب‌سرویس', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'question', 'type' => 'textarea', 'label' => __( 'پاسخ به چه سوالی', 'uid-theme' ) ),
			array( 'key' => 'input', 'type' => 'text', 'label' => __( 'ورودی اصلی', 'uid-theme' ) ),
			array( 'key' => 'link', 'type' => 'text', 'label' => __( 'آدرس صفحه سرویس', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'fold_title', 'default' => 'کدام سرویس به کدام سوال پاسخ می‌دهد' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcmp', 'uid_section_wshcmp_main', array( 'group' => 'uid_section_wshcmp', 'key' => 'fold_teaser', 'default' => 'مقایسه ورودی، خروجی و کاربرد هشت وب‌سرویس اصلی' ) );

	/* ---------------- فصل ۲: سرویس‌های مکمل ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshextra', array( 'sanitize_callback' => 'uid_sanitize_section_wshextra', 'default' => array() ) );
	add_settings_section( 'uid_section_wshextra_main', '', '__return_false', 'uid_section_wshextra' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'eyebrow', 'default' => 'معرفی اسمی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'heading', 'default' => 'وب‌سرویس‌های مکمل و تخصصی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'text', 'default' => 'این شش وب‌سرویس در کنار سرویس‌های اصلی ارائه می‌شوند. برای دریافت مشخصات فنی، نمونه پاسخ و تعرفه هرکدام، درخواست خود را ثبت کنید تا کارشناس یوآیدی با شما تماس بگیرد.' ) );
	add_settings_field( 'items', __( 'سرویس‌های مکمل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshextra', 'uid_section_wshextra_main', array(
		'group' => 'uid_section_wshextra', 'key' => 'items', 'default' => uid_default_wsh_extra(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => uid_wsh_extra_icon_options() ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'نام سرویس', 'uid-theme' ), 'required' => true ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'fold_title', 'default' => 'وب‌سرویس‌های مکمل و تخصصی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshextra', 'uid_section_wshextra_main', array( 'group' => 'uid_section_wshextra', 'key' => 'fold_teaser', 'default' => 'شش سرویس استعلامی دیگر — سند ملکی، جواز کسب، سوء پیشینه و…' ) );

	/* ---------------- فصل ۳: چرا وب‌سرویس‌های یوآیدی ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshadv', array( 'sanitize_callback' => 'uid_sanitize_section_wshadv', 'default' => array() ) );
	add_settings_section( 'uid_section_wshadv_main', '', '__return_false', 'uid_section_wshadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshadv', 'uid_section_wshadv_main', array( 'group' => 'uid_section_wshadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshadv', 'uid_section_wshadv_main', array( 'group' => 'uid_section_wshadv', 'key' => 'eyebrow', 'default' => 'مزایای زیرساختی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshadv', 'uid_section_wshadv_main', array( 'group' => 'uid_section_wshadv', 'key' => 'heading', 'default' => 'چرا وب‌سرویس‌های یوآیدی؟' ) );
	add_settings_field( 'items', __( 'کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshadv', 'uid_section_wshadv_main', array(
		'group' => 'uid_section_wshadv', 'key' => 'items', 'default' => uid_default_wsh_adv(), 'add_label' => __( 'افزودن مزیت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => uid_wsh_adv_icon_options() ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'خط پایانی', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshadv', 'uid_section_wshadv_main', array( 'group' => 'uid_section_wshadv', 'key' => 'fold_title', 'default' => 'چرا وب‌سرویس‌های یوآیدی؟' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshadv', 'uid_section_wshadv_main', array( 'group' => 'uid_section_wshadv', 'key' => 'fold_teaser', 'default' => 'پاسخ‌دهی در لحظه، پشتیبانی فنی اختصاصی و محیط سندباکس' ) );

	/* ---------------- فصل ۴: از درخواست تا استعلام واقعی ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshstart', array( 'sanitize_callback' => 'uid_sanitize_section_wshstart', 'default' => array() ) );
	add_settings_section( 'uid_section_wshstart_main', '', '__return_false', 'uid_section_wshstart' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshstart', 'uid_section_wshstart_main', array( 'group' => 'uid_section_wshstart', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshstart', 'uid_section_wshstart_main', array( 'group' => 'uid_section_wshstart', 'key' => 'eyebrow', 'default' => 'نحوه شروع' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshstart', 'uid_section_wshstart_main', array( 'group' => 'uid_section_wshstart', 'key' => 'heading', 'default' => 'از درخواست تا اولین استعلام واقعی، سه مرحله' ) );
	add_settings_field( 'items', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshstart', 'uid_section_wshstart_main', array(
		'group' => 'uid_section_wshstart', 'key' => 'items', 'default' => uid_default_wsh_start(), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان مرحله', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshstart', 'uid_section_wshstart_main', array( 'group' => 'uid_section_wshstart', 'key' => 'fold_title', 'default' => 'از درخواست تا اولین استعلام واقعی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshstart', 'uid_section_wshstart_main', array( 'group' => 'uid_section_wshstart', 'key' => 'fold_teaser', 'default' => 'سه مرحله: درخواست سرویس، کلید سندباکس، عملیاتی‌سازی' ) );

	/* ---------------- فصل ۵: سوالات متداول ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshfaq', array( 'sanitize_callback' => 'uid_sanitize_section_wshfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_wshfaq_main', '', '__return_false', 'uid_section_wshfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array( 'group' => 'uid_section_wshfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array( 'group' => 'uid_section_wshfaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس، این‌ها را بخوانید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array( 'group' => 'uid_section_wshfaq', 'key' => 'heading', 'default' => 'سوالات متداول عمومی وب‌سرویس‌ها' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array(
		'group' => 'uid_section_wshfaq', 'key' => 'items', 'default' => uid_default_wsh_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'q', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'a', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array( 'group' => 'uid_section_wshfaq', 'key' => 'fold_title', 'default' => 'سوالات متداول عمومی وب‌سرویس‌ها' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_wshfaq', 'uid_section_wshfaq_main', array( 'group' => 'uid_section_wshfaq', 'key' => 'fold_teaser', 'default' => 'تست پیش از خرید، پکیج چندسرویسی و تضمین پایداری' ) );

	/* ---------------- بند تماس میان‌صفحه ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshcallband', array( 'sanitize_callback' => 'uid_sanitize_section_wshcallband', 'default' => array() ) );
	add_settings_section( 'uid_section_wshcallband_main', '', '__return_false', 'uid_section_wshcallband' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcallband', 'uid_section_wshcallband_main', array( 'group' => 'uid_section_wshcallband', 'key' => 'heading', 'default' => 'هنوز مطمئن نیستید کدام ترکیب از سرویس‌ها را لازم دارید؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshcallband', 'uid_section_wshcallband_main', array( 'group' => 'uid_section_wshcallband', 'key' => 'text', 'default' => 'برای راهنمایی سرویس‌ها و دریافت اطلاعات بیشتر، از طریق فرم درخواست سرویس یا تماس با کارشناسان ما ارتباط بگیرید. مسیر استعلام شما را با هم بررسی می‌کنیم و همان سرویس‌هایی را پیشنهاد می‌دهیم که واقعاً لازم دارید.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshcallband', 'uid_section_wshcallband_main', array( 'group' => 'uid_section_wshcallband', 'key' => 'btn_text', 'default' => 'فرم درخواست سرویس' ) );

	/* ---------------- بنر تماس نهایی (فرم) ---------------- */
	register_setting( 'uid_wsh_group', 'uid_section_wshlead', array( 'sanitize_callback' => 'uid_sanitize_section_wshlead', 'default' => array() ) );
	add_settings_section( 'uid_section_wshlead_main', '', '__return_false', 'uid_section_wshlead' );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'heading', 'default' => 'فرم درخواست وب‌سرویس‌های احراز هویت یوآیدی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'text', 'default' => 'برای راهنمایی سرویس‌ها و دریافت اطلاعات بیشتر، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی سرویس یا پکیج مناسب کسب‌وکار شما را بررسی می‌کنند و کلید دسترسی محیط آزمایشی را در اختیارتان می‌گذارند.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nکلید سندباکس رایگان پیش از خرید\nچند API روی یک کلید دسترسی\nپشتیبانی فنی اختصاصی در زمان پیاده‌سازی", 'rows' => 4 ) );
	add_settings_field( 'form_heading', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'form_heading', 'default' => 'درخواست وب‌سرویس' ) );
	add_settings_field( 'form_hint', __( 'راهنمای کوچک بالای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'form_hint', 'default' => 'چهار فیلد کوتاه. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'btn_text', 'default' => 'ارسال درخواست و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note', __( 'یادداشت محرمانگی زیر دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'note', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_text', __( 'متن پیام موفقیت پس از ارسال (شماره تماس به‌صورت خودکار بعد از آن می‌آید)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
	add_settings_field( 'select_placeholder', __( 'برچسب اول کشویی (قبل از انتخاب)', 'uid-theme' ), 'uid_field_text', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'group' => 'uid_section_wshlead', 'key' => 'select_placeholder', 'default' => 'کدام سرویس را لازم دارید؟…' ) );
	add_settings_field( 'select_options', __( 'گزینه‌های کشویی «کدام سرویس را لازم دارید»', 'uid-theme' ), 'uid_field_repeater', 'uid_section_wshlead', 'uid_section_wshlead_main', array(
		'group' => 'uid_section_wshlead', 'key' => 'select_options', 'default' => uid_default_wsh_lead_options(), 'add_label' => __( 'افزودن گزینه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار فنی (value)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب نمایشی', 'uid-theme' ), 'required' => true ),
		),
		'desc' => __( 'مقدار «ind» رزرو شده و کاربرانی که آن را انتخاب کنند به پیام «فقط برای کسب‌وکارها»ی زیر فرم هدایت می‌شوند — این مقدار را روی هیچ گزینه دیگری نگذارید.', 'uid-theme' ),
	) );
	add_settings_field( 'form_notice', '', 'uid_field_notice', 'uid_section_wshlead', 'uid_section_wshlead_main', array( 'text' => __( 'منطق مسیریابی کاربران شخصی (نمایش پیام «فقط برای کسب‌وکارها») فقط با مقدار فنی «ind» کار می‌کند و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );
}
add_action( 'admin_init', 'uid_register_wsh_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_wshhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'router_items' => uid_sanitize_repeater_rows( $input['router_items'] ?? '[]', array(
			array( 'key' => 'key', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
		) ),
	);
}

function uid_sanitize_section_wshstats( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_wshtldr( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'badge'     => sanitize_text_field( $input['badge'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
		'more_text' => sanitize_text_field( $input['more_text'] ?? '' ),
	);
}

function uid_sanitize_section_wshconsole( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'services'  => uid_sanitize_repeater_rows( $input['services'] ?? '[]', array(
			array( 'key' => 'key', 'type' => 'text', 'required' => true ),
			array( 'key' => 'name', 'type' => 'text' ),
			array( 'key' => 'sub', 'type' => 'textarea' ),
			array( 'key' => 'run_label', 'type' => 'text' ),
			array( 'key' => 'go_name', 'type' => 'text' ),
			array( 'key' => 'f1_label', 'type' => 'text' ),
			array( 'key' => 'f1_ph', 'type' => 'text' ),
			array( 'key' => 'f1_val', 'type' => 'text' ),
			array( 'key' => 'f2_label', 'type' => 'text' ),
			array( 'key' => 'f2_ph', 'type' => 'text' ),
			array( 'key' => 'f2_val', 'type' => 'text' ),
			array( 'key' => 'f3_label', 'type' => 'text' ),
			array( 'key' => 'f3_ph', 'type' => 'text' ),
			array( 'key' => 'f3_val', 'type' => 'text' ),
			array( 'key' => 't1_label', 'type' => 'text' ),
			array( 'key' => 't2_label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_wshcatalog( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'key', 'type' => 'text', 'required' => true ),
			array( 'key' => 'cat', 'type' => 'text' ),
			array( 'key' => 'name', 'type' => 'text', 'required' => true ),
			array( 'key' => 'short', 'type' => 'text' ),
			array( 'key' => 'latin', 'type' => 'text' ),
			array( 'key' => 'slug', 'type' => 'text' ),
			array( 'key' => 'desc', 'type' => 'textarea' ),
			array( 'key' => 'use', 'type' => 'textarea' ),
			array( 'key' => 'q', 'type' => 'textarea' ),
		) ),
		'chips'     => uid_sanitize_repeater_rows( $input['chips'] ?? '[]', array(
			array( 'key' => 'key', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
		) ),
	);
}

function uid_sanitize_section_wshcmp( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'name', 'type' => 'text', 'required' => true ),
			array( 'key' => 'question', 'type' => 'textarea' ),
			array( 'key' => 'input', 'type' => 'text' ),
			array( 'key' => 'link', 'type' => 'text' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_wshextra( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_wshadv( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'tagline', 'type' => 'text' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_wshstart( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_wshfaq( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'q', 'type' => 'text', 'required' => true ),
			array( 'key' => 'a', 'type' => 'textarea' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_wshcallband( $input ) {
	return array(
		'heading'  => sanitize_text_field( $input['heading'] ?? '' ),
		'text'     => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text' => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_wshlead( $input ) {
	return array(
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => sanitize_text_field( $input['heading'] ?? '' ),
		'text'         => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'        => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_heading' => sanitize_text_field( $input['form_heading'] ?? '' ),
		'form_hint'    => sanitize_text_field( $input['form_hint'] ?? '' ),
		'btn_text'     => sanitize_text_field( $input['btn_text'] ?? '' ),
		'note'         => sanitize_text_field( $input['note'] ?? '' ),
		'success_text' => sanitize_text_field( $input['success_text'] ?? '' ),
		'select_placeholder' => sanitize_text_field( $input['select_placeholder'] ?? '' ),
		'select_options'     => uid_sanitize_repeater_rows( $input['select_options'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
		) ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_wsh_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی همین امروز تماس بگیرد: انتخاب سرویس مناسب کسب‌وکار شما، کلید آزمایشی و دسترسی سندباکس، مستندات فنی و تعرفه متناسب با حجم استعلام ماهانه.', 'uid-theme' ); ?></p>
	    <form id="modalForm" novalidate>
	<div class="cta-fields">
	  <div class="fld"><label for="m_name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	    <input id="m_name" name="name" type="text" placeholder="<?php esc_attr_e( 'مثلاً علی رضایی', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_biz"><?php esc_html_e( 'نام کسب‌وکار', 'uid-theme' ); ?></label>
	    <input id="m_biz" name="business" type="text" placeholder="<?php esc_attr_e( 'مثلاً فروشگاه اینترنتی...', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_tel"><?php esc_html_e( 'شماره تماس', 'uid-theme' ); ?></label>
	    <input id="m_tel" name="phone" type="tel" inputmode="numeric" placeholder="09xxxxxxxxx" data-req data-tel>
	    <span class="err"><?php esc_html_e( 'شماره موبایل معتبر با فرمت ۰۹xxxxxxxxx وارد کنید.', 'uid-theme' ); ?></span></div>
	  <div class="fld"><label for="m_loc"><?php esc_html_e( 'نوع کسب‌وکار', 'uid-theme' ); ?></label>
	    <select id="m_loc" name="biztype" data-req>
	      <option value=""><?php esc_html_e( 'انتخاب کنید…', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'ثبت‌نام و خدمات مالی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'درگاه و پلتفرم پرداخت', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'کیف پول دیجیتال', 'uid-theme' ); ?></option><option><?php esc_html_e( 'صرافی رمزارز', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سامانه اعتباری و لندتک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'مارکت‌پلیس', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'کارگزاری بورس', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بانک، نئوبانک یا فین‌تک', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سایر کسب‌وکارها', 'uid-theme' ); ?></option>
	    </select></div>
	  <button class="btn btn-cta btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ); ?></button>
	  <p class="tiny" style="margin-top:10px;color:#7D91B4"><?php esc_html_e( 'کارشناس یوآیدی در ساعات پاسخگویی، سریع با شما تماس می‌گیرد.', 'uid-theme' ); ?></p>
	</div>
	<div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
	  <span><?php esc_html_e( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری:', 'uid-theme' ); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div></form>
	    <p class="tiny" style="margin-top:14px;color:#7D91B4;text-align:center">
	      <?php esc_html_e( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ); ?>
	      <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" style="color:#7FE0EC;font-weight:700" class="mono"><?php echo esc_html( uid_phone_display() ); ?></a></p>
	  </div>
	</div>
	<?php
}
