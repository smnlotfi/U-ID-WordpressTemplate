<?php
/**
 * صفحه اختصاصی «تطبیق شماره شبا با کد ملی» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی
 * واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «iv» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر (از جمله «ib» در استعلام شبا) تداخل نکنند.
 *
 * این صفحه با «استعلام شبا» (inc/iban-sheba-page.php) اشتباه گرفته نشود: آن سرویس
 * فقط با گرفتن شماره شبا تمام مشخصات صاحب حساب را برمی‌گرداند؛ این صفحه سرویس
 * دیگری است — با گرفتن شماره شبا + کد ملی + تاریخ تولد، فقط یک پاسخ صریح
 * تطابق‌دارد/ندارد (isMatched: true/false) برمی‌گرداند، بدون افشای هیچ داده هویتی.
 *
 * ۹ سکشن این صفحه (what/vs/how/who/gain/team/dev/xsell/faq) سیستم «فصل تاخوردنی +
 * کتاب‌خوان فصل» موبایل دارند (دقیقاً مثل inc/ekyc-liveness-page.php) — نگاه کنید
 * به uid_iv_folded_slugs()/uid_render_iv_foldbar().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_IBAN_VALIDATE_TEMPLATE', 'template-iban-validate.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_iv_sections_registry() {
	return array(
		'ivhero'     => array( 'label' => __( 'هیرو + کنسول زنده تطبیق مالکیت', 'uid-theme' ),        'icon' => 'dashicons-star-filled' ),
		'ivtrust'    => array( 'label' => __( 'باند اعتماد کوتاه (۴ آمار)', 'uid-theme' ),             'icon' => 'dashicons-chart-bar' ),
		'ivtldr'     => array( 'label' => __( 'کپسول ۳۰ ثانیه‌ای (فقط موبایل)', 'uid-theme' ),         'icon' => 'dashicons-clock' ),
		'ivloss'     => array( 'label' => __( 'محاسبه‌گر ریسک واریز به حساب تاییدنشده', 'uid-theme' ), 'icon' => 'dashicons-calculator' ),
		'ivwhat'     => array( 'label' => __( 'فصل ۱ — این سرویس چیست', 'uid-theme' ),                'icon' => 'dashicons-editor-help' ),
		'ivvs'       => array( 'label' => __( 'فصل ۲ — تطبیق شبا در برابر استعلام شبا', 'uid-theme' ), 'icon' => 'dashicons-image-flip-horizontal' ),
		'ivhow'      => array( 'label' => __( 'فصل ۳ — نحوه کار سرویس (استپر ۴مرحله‌ای)', 'uid-theme' ), 'icon' => 'dashicons-randomize' ),
		'ivwho'      => array( 'label' => __( 'فصل ۴ — کاربردها (انتخابگر صنعت)', 'uid-theme' ),       'icon' => 'dashicons-groups' ),
		'ivgain'     => array( 'label' => __( 'فصل ۵ — مزایای رقابتی + پوشش بانک‌ها', 'uid-theme' ),   'icon' => 'dashicons-awards' ),
		'ivteam'     => array( 'label' => __( 'فصل ۶ — تیم متخصص + تعهدنامه', 'uid-theme' ),           'icon' => 'dashicons-businessperson' ),
		'ivdev'      => array( 'label' => __( 'فصل ۷ — مستندات فنی و نمونه‌کد', 'uid-theme' ),         'icon' => 'dashicons-editor-code' ),
		'ivxsell'    => array( 'label' => __( 'فصل ۸ — سرویس‌های مکمل', 'uid-theme' ),                 'icon' => 'dashicons-networking' ),
		'ivfaq'      => array( 'label' => __( 'فصل ۹ — سوالات متداول', 'uid-theme' ),                  'icon' => 'dashicons-editor-help' ),
		'ivadv'      => array( 'label' => __( 'مشخصات عملیاتی (پنل تیره)', 'uid-theme' ),              'icon' => 'dashicons-shield' ),
		'ivprice'    => array( 'label' => __( 'محاسبه‌گر هزینه ماهانه', 'uid-theme' ),                 'icon' => 'dashicons-money-alt' ),
		'ivlastcall' => array( 'label' => __( 'بند تماس میانی', 'uid-theme' ),                         'icon' => 'dashicons-megaphone' ),
		'ivlead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                   'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_iv_layout() {
	$registry = uid_iv_sections_registry();
	$saved    = get_option( 'uid_iv_layout', array() );

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

function uid_sanitize_iv_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_iv_sections_registry();
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
 * اسلاگ‌های سکشن‌هایی که سیستم «فصل تاخوردنی + کتاب‌خوان فصل» موبایل رویشان اعمال می‌شود.
 */
function uid_iv_folded_slugs() {
	return array( 'ivwhat', 'ivvs', 'ivhow', 'ivwho', 'ivgain', 'ivteam', 'ivdev', 'ivxsell', 'ivfaq' );
}

function uid_render_iv_sections() {
	$folded    = uid_iv_folded_slugs();
	$layout    = uid_get_iv_layout();
	$folded_on = 0;
	foreach ( $layout as $row ) {
		if ( ! empty( $row['enabled'] ) && in_array( $row['slug'], $folded, true ) ) $folded_on++;
	}

	$foldbar_done = false;
	echo '<div id="chapters" class="fold-run">';
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$is_folded = in_array( $row['slug'], $folded, true );
		if ( $is_folded && ! $foldbar_done ) {
			uid_render_iv_foldbar( $folded_on );
			$foldbar_done = true;
		}
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
	echo '</div>';
}

/**
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود
 * (نگاه کنید به assets/css/iban-validate-page.css). نوار پیشرفت مطالعه توسط
 * JS مدیریت می‌شود (دقیقاً مثل ekyc-liveness-page.js).
 */
function uid_render_iv_foldbar( $count ) {
	?>
	<div class="foldbar" id="foldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <span class="fold-meter"><i id="foldMeter"></i></span>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه (فقط آیکون‌هایی که در چند جا تکرار می‌شوند)
 * ===================================================================== */
function uid_iv_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_iv_x_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_iv_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_iv_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .3 1.9.6 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.1a2 2 0 012.1-.5c.9.3 1.8.5 2.8.6a2 2 0 011.7 2z"/></svg>';
}
function uid_iv_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
function uid_iv_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_iv_info_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>';
}
function uid_iv_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
function uid_iv_bank_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3M7 10v11M12 10v11M17 10v11"/></svg>';
}

/* ---- آیکون‌های کلید‌دار مربوط به آرایه‌های تکراری (فیلد select «آیکون» در ادمین) ---- */
function uid_iv_who_icon_svg( $key ) {
	$icons = array(
		'crypto'  => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
		'market'  => '<path d="M3 3h2l2.7 12.4a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>',
		'lend'    => '<path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3"/><path d="M7 10v11M12 10v11M17 10v11"/>',
		'insure'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'payroll' => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'shop'    => '<path d="M4 4v6h6"/><path d="M20 20v-6h-6"/><path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/>',
	);
	return $icons[ $key ] ?? $icons['crypto'];
}
function uid_iv_who_color( $key ) {
	$colors = array( 'crypto' => 'warm', 'market' => '', 'lend' => 'navy', 'insure' => '', 'payroll' => 'navy', 'shop' => '' );
	return $colors[ $key ] ?? '';
}
function uid_iv_gain_icon_svg( $key ) {
	$icons = array(
		'fast'    => '<path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/>',
		'bank'    => '<path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3M7 10v11M12 10v11M17 10v11"/>',
		'bday'    => '<rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4M9 14l2 2 4-4"/>',
		'support' => '<path d="M16 20v-1.5a4 4 0 00-8 0V20"/><circle cx="12" cy="8" r="4"/>',
		'uptime'  => '<path d="M4 20V10M10 20V4M16 20v-8M22 20v-5"/>',
		'costfair'=> '<path d="M12 2v20M17 6H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
	);
	return $icons[ $key ] ?? $icons['fast'];
}
function uid_iv_gain_color( $key ) {
	$colors = array( 'fast' => 'warm', 'bank' => '', 'bday' => 'navy', 'support' => '', 'uptime' => 'navy', 'costfair' => 'warm' );
	return $colors[ $key ] ?? '';
}
function uid_iv_team_icon_svg( $key ) {
	$icons = array(
		'connect' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'monitor' => '<path d="M4 20V10M10 20V4M16 20v-8M22 20v-5"/>',
		'doc'     => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v20H6.5A2.5 2.5 0 014 19.5z"/><path d="M8 7h8M8 11h6"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
	);
	return $icons[ $key ] ?? $icons['connect'];
}
function uid_iv_xsell_icon_svg( $key ) {
	$icons = array(
		'card'      => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h5"/>',
		'cardcheck' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M15 15l2 2 4-4"/>',
		'convert'   => '<path d="M4 4v6h6"/><path d="M20 20v-6h-6"/><path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/>',
		'sim'       => '<rect x="6" y="2.5" width="12" height="19" rx="2.5"/><path d="M10.5 18.5h3"/>',
	);
	return $icons[ $key ] ?? $icons['card'];
}
function uid_iv_adv_icon_svg( $key ) {
	$icons = array(
		'uptime' => '<path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/>',
		'fast'   => '<path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/>',
		'bank'   => '<path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3M7 10v11M12 10v11M17 10v11"/>',
		'legal'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'doc'    => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v20H6.5A2.5 2.5 0 014 19.5z"/><path d="M8 7h8"/>',
	);
	return $icons[ $key ] ?? $icons['uptime'];
}
function uid_iv_proof_icon_svg( $key ) {
	$icons = array(
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'sync'   => '<path d="M4 4v6h6M20 20v-6h-6"/><path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/>',
		'doc'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 15l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}
function uid_iv_probs_icon_svg( $key ) {
	$icons = array(
		'personx' => '<path d="M16 20v-1.5a4 4 0 00-8 0V20"/><circle cx="12" cy="8" r="4"/><path d="M3 3l18 18"/>',
		'warn'    => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['personx'];
}

/* =====================================================================
 * ۱) هیرو + کنسول زنده تطبیق مالکیت (شبیه‌سازی نمایشی، منطق آن در JS هاردکد است)
 * ===================================================================== */
function uid_default_iv_vtests() {
	return array(
		'ok_label' => 'تست تطابق موفق', 'no_label' => 'تست عدم تطابق',
		'ok_iban' => '780170100014852301004521', 'ok_nid' => '0079845612', 'ok_bd' => '1370/05/17',
		'no_iban' => '780170100014852301004521', 'no_nid' => '1288394576', 'no_bd' => '1365/11/02',
	);
}
function uid_iv_vtests_json() {
	$v = uid_section_val( 'ivhero', 'vtests', uid_default_iv_vtests() );
	if ( ! is_array( $v ) ) $v = uid_default_iv_vtests();
	$out = array(
		'ok' => array( 'iban' => $v['ok_iban'] ?? '', 'nid' => $v['ok_nid'] ?? '', 'bd' => $v['ok_bd'] ?? '' ),
		'no' => array( 'iban' => $v['no_iban'] ?? '', 'nid' => $v['no_nid'] ?? '', 'bd' => $v['no_bd'] ?? '' ),
	);
	return wp_json_encode( $out );
}
function uid_field_iv_vtests( $args ) {
	$v = uid_current_value( $args['group'], $args['key'], $args['default'] );
	if ( ! is_array( $v ) ) $v = $args['default'];
	$rows = array( 'ok' => __( 'دکمه تست تطابق موفق', 'uid-theme' ), 'no' => __( 'دکمه تست عدم تطابق', 'uid-theme' ) );
	foreach ( $rows as $prefix => $row_label ) : ?>
		<p><b><?php echo esc_html( $row_label ); ?></b></p>
		<p><label><?php esc_html_e( 'برچسب دکمه:', 'uid-theme' ); ?> <input type="text" class="regular-text" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>][<?php echo esc_attr( $prefix ); ?>_label]" value="<?php echo esc_attr( $v[ $prefix . '_label' ] ?? '' ); ?>"></label></p>
		<p>
			<label><?php esc_html_e( 'شبای نمونه:', 'uid-theme' ); ?> <input type="text" dir="ltr" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>][<?php echo esc_attr( $prefix ); ?>_iban]" value="<?php echo esc_attr( $v[ $prefix . '_iban' ] ?? '' ); ?>"></label>
			<label><?php esc_html_e( 'کد ملی نمونه:', 'uid-theme' ); ?> <input type="text" dir="ltr" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>][<?php echo esc_attr( $prefix ); ?>_nid]" value="<?php echo esc_attr( $v[ $prefix . '_nid' ] ?? '' ); ?>"></label>
			<label><?php esc_html_e( 'تاریخ تولد نمونه:', 'uid-theme' ); ?> <input type="text" dir="ltr" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>][<?php echo esc_attr( $prefix ); ?>_bd]" value="<?php echo esc_attr( $v[ $prefix . '_bd' ] ?? '' ); ?>"></label>
		</p>
		<hr>
	<?php endforeach;
}
function uid_render_section_ivhero() {
	$tag    = uid_section_tag( 'ivhero', 'h1' );
	$tags   = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ivhero', 'tags', implode( "\n", uid_default_iv_hero_tags() ) ) ) ) );
	$vtests = uid_section_val( 'ivhero', 'vtests', uid_default_iv_vtests() );
	if ( ! is_array( $vtests ) ) $vtests = uid_default_iv_vtests();
	?>
	<section class="dark heroA" id="top">
	  <div class="iv-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌های استعلام', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'تطبیق شماره شبا با کد ملی', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="iv-eyebrow on-dark"><i></i><?php echo wp_kses( uid_section_val( 'ivhero', 'eyebrow', __( 'وب‌سرویس تطبیق شبا و کد ملی · <span class="lat">IBAN Validate API</span>', 'uid-theme' ) ), array( 'span' => array( 'class' => array() ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'ivhero', 'heading', __( 'شماره شبا درست است.<mark>ولی مال خودش است؟</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo wp_kses( uid_section_val( 'ivhero', 'text', __( 'یک شبای معتبر و فعال هیچ چیزی درباره صاحبش نمی‌گوید. تا لحظه‌ای که پول واریز شود، شما نمی‌دانید حساب مقصد متعلق به همان کاربری است که احراز هویتش کرده‌اید یا به نام شخص دیگری. این وب‌سرویس با گرفتن <b style="color:#7FE0EC">شبا، کد ملی و تاریخ تولد</b>، مالکیت حساب را <b style="color:#7FE0EC">پیش از واریز</b> تایید یا رد می‌کند.', 'uid-theme' ) ), array( 'b' => array( 'style' => array() ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="iv-btn btn-cta" data-open-modal><?php echo uid_iv_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ivhero', 'btn1_text', __( 'فرم درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="iv-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_iv_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_iv_check_icon(); ?><?php echo wp_kses( $t, array( 'span' => array( 'class' => array() ) ) ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- ownership match console -->
	      <div class="idw rv rv-d2" id="vw">
	        <div class="idw-hd"><span class="live"></span><b><?php esc_html_e( 'تست زنده سرویس', 'uid-theme' ); ?></b>
	          <span class="mono">validate/iban/ownership</span></div>

	        <div class="vtests" id="v_tests">
	          <button class="vtest ok" type="button" data-vtest="ok">
	            <i><?php echo uid_iv_check_icon(); ?></i>
	            <span><?php echo esc_html( $vtests['ok_label'] ?? '' ); ?></span></button>
	          <button class="vtest no" type="button" data-vtest="no">
	            <i><?php echo uid_iv_x_icon(); ?></i>
	            <span><?php echo esc_html( $vtests['no_label'] ?? '' ); ?></span></button>
	        </div>

	        <div class="vstage" id="v_stage">
	          <div class="vplate">
	            <div class="vplate-hd">
	              <span class="pic"><?php echo uid_iv_bank_icon(); ?></span>
	              <b><?php esc_html_e( 'سمت حساب بانکی', 'uid-theme' ); ?></b>
	              <span class="tail" id="v_bank"><?php esc_html_e( 'بانک شناسایی نشده', 'uid-theme' ); ?></span>
	            </div>
	            <div class="vfld" id="f_iban">
	              <label for="v_iban"><?php esc_html_e( 'شماره شبا (', 'uid-theme' ); ?><span class="lat">iban</span><?php esc_html_e( ') — ۲۴ رقم', 'uid-theme' ); ?>
	                <span class="cnt" id="v_ibcnt">۰/۲۴</span></label>
	              <span class="vin"><span class="pre">IR</span>
	                <input id="v_iban" type="text" inputmode="numeric" autocomplete="off"
	                       maxlength="29" placeholder="000000000000000000000000" dir="ltr"></span>
	            </div>
	          </div>

	          <div class="vlock" id="v_lock">
	            <span class="beam" aria-hidden="true"></span>
	            <span class="ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg></span>
	            <span class="tx"><b id="v_lockt"><?php esc_html_e( 'تطبیق مالکیت انجام نشده', 'uid-theme' ); ?></b>
	              <?php esc_html_e( 'سرویس بررسی می‌کند این حساب به همین شخص تعلق دارد یا نه.', 'uid-theme' ); ?></span>
	          </div>

	          <div class="vplate idn">
	            <div class="vplate-hd">
	              <span class="pic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20v-1.5a4 4 0 00-8 0V20"/><circle cx="12" cy="8" r="4"/><rect x="2.5" y="3" width="19" height="18" rx="3"/></svg></span>
	              <b><?php esc_html_e( 'سمت اطلاعات هویتی', 'uid-theme' ); ?></b>
	              <span class="tail"><?php esc_html_e( 'دارنده ادعایی حساب', 'uid-theme' ); ?></span>
	            </div>
	            <div class="vrow2">
	              <div class="vfld" id="f_nid">
	                <label for="v_nid"><?php esc_html_e( 'کد ملی (', 'uid-theme' ); ?><span class="lat">nationalId</span>)
	                  <span class="cnt" id="v_ncnt">۰/۱۰</span></label>
	                <span class="vin"><input id="v_nid" type="text" inputmode="numeric" autocomplete="off"
	                       maxlength="10" placeholder="0123456789" dir="ltr"></span>
	              </div>
	              <div class="vfld" id="f_bd">
	                <label for="v_bd"><?php esc_html_e( 'تاریخ تولد (', 'uid-theme' ); ?><span class="lat">birthDate</span>)</label>
	                <span class="vin"><input id="v_bd" type="text" inputmode="numeric" autocomplete="off"
	                       maxlength="10" placeholder="1370/05/17" dir="ltr"></span>
	              </div>
	            </div>
	          </div>
	        </div>

	        <div style="margin-block-start:12px">
	          <button class="iv-btn btn-cta btn-block" type="button" id="v_run"><?php echo uid_iv_shield_icon(); ?><?php esc_html_e( 'اجرای تطبیق مالکیت', 'uid-theme' ); ?></button>
	        </div>

	        <div class="vverdict" id="v_verdict">
	          <div class="vv-hd"><span class="ic" id="v_vic"></span><b id="v_vtx"></b>
	            <span class="lat" id="v_vlat"></span></div>
	          <p class="vv-tx" id="v_vsub"></p>
	        </div>

	        <div style="margin-block-start:16px">
	          <div class="rswitch">
	            <button type="button" class="on" data-rview="g"><?php esc_html_e( 'نمای ساختاری', 'uid-theme' ); ?></button>
	            <button type="button" data-rview="j"><?php esc_html_e( 'پاسخ', 'uid-theme' ); ?> <span class="lat">JSON</span></button>
	            <span class="idbadge wait" id="v_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span>
	          </div>

	          <div data-rpane="g" class="on">
	            <div class="idgrid" id="v_grid">
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'نتیجه تطابق مالکیت (', 'uid-theme' ); ?><span class="lat">isMatched</span>)</span><span class="v mut" id="o_match">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'بانک صادرکننده شبا', 'uid-theme' ); ?></span><span class="v mut" id="o_bank">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'کد ملی ارسالی', 'uid-theme' ); ?></span><span class="v mut" id="o_nid">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'کد وضعیت پاسخ', 'uid-theme' ); ?></span><span class="v mut" id="o_code">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'پیام وضعیت', 'uid-theme' ); ?></span><span class="v mut" id="o_msg">—</span></div>
	            </div>
	          </div>

	          <div data-rpane="j">
	            <pre class="rjson" id="o_json">{
  "responseContext": {
    "status": { "code": …, "message": "…" }
  },
  "isMatched": …
}</pre>
	          </div>

	          <div class="idmeta">
	            <span><?php esc_html_e( 'متد فراخوانی: ', 'uid-theme' ); ?><b>POST</b></span>
	            <span><?php esc_html_e( 'سرویس: ', 'uid-theme' ); ?><span class="mono">validate/iban/ownership</span></span>
	          </div>
	        </div>

	        <div class="idw-note"><?php echo uid_iv_info_icon(); ?>
	          <span><?php esc_html_e( 'این نمایش با داده نمونه کار می‌کند؛ ساختار پاسخ دقیقاً همان چیزی است که سرویس برمی‌گرداند. برای تطبیق واقعی روی زیرساخت بین‌بانکی، کلید ', 'uid-theme' ); ?><span class="lat">API</span><?php esc_html_e( ' لازم است.', 'uid-theme' ); ?></span></div>

	        <!-- highest-intent second on the page: one field, right here -->
	        <div class="idw-lead" id="vwLead">
	          <p><?php esc_html_e( 'همین بررسی را می‌خواهید پیش از واریز در سامانه خودتان اجرا کنید؟ ', 'uid-theme' ); ?><b><?php esc_html_e( 'شماره‌تان را بگذارید', 'uid-theme' ); ?></b><?php esc_html_e( '، کارشناس یوآیدی دسترسی تست را همین امروز فعال می‌کند.', 'uid-theme' ); ?></p>
	          <form class="micro" id="microForm" novalidate>
	            <div class="micro-fields">
	              <input type="hidden" name="source" value="iban-validate-lp-hero">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="iv-btn btn-cta" type="button" data-submit><?php esc_html_e( 'دریافت کلید تست', 'uid-theme' ); ?></button>
	              </div>
	            </div>
	            <div class="form-ok"><?php echo uid_iv_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	              <span><?php esc_html_e( 'کارشناس یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}
function uid_default_iv_hero_tags() {
	return array(
		'تطابق در لحظه، پاسخ <span class="lat">true</span> یا <span class="lat">false</span>',
		'اتصال مستقیم به شبکه شتاب و شاپرک',
		'انطباق با الزامات پلیس فتا و <span class="lat">AML</span>',
		'آپ‌تایم ۹۹.۹٪ با مانیتورینگ ۲۴ ساعته',
	);
}

/* =====================================================================
 * ۲) باند اعتماد کوتاه (۴ آمار)
 * ===================================================================== */
function uid_default_iv_trust() {
	return array(
		array( 'value' => '۹۹.۹٪', 'label' => 'نرخ آپ‌تایم و پایداری سرویس با مانیتورینگ ۲۴ ساعته', 'numeric' => '1' ),
		array( 'value' => 'تطابق در لحظه', 'label' => 'میانگین زمان پاسخ‌دهی سرویس در جریان‌های پرتراکنش', 'numeric' => '' ),
		array( 'value' => 'شتاب و شاپرک', 'label' => 'اتصال مستقیم به تمامی بانک‌های عضو شبکه بانکی', 'numeric' => '' ),
		array( 'value' => 'فتا و AML', 'label' => 'انطباق با الزامات پلیس فتا و دستورالعمل‌های مبارزه با پولشویی', 'numeric' => '' ),
	);
}
function uid_render_section_ivtrust() {
	$items = uid_section_val( 'ivtrust', 'items', uid_default_iv_trust() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="iv-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $items as $it ) :
	        $is_num = ! empty( $it['numeric'] );
	      ?>
	      <div class="qstat"><span class="v<?php echo $is_num ? '' : ' txt'; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $it['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) کپسول ۳۰ ثانیه‌ای — فقط موبایل (چهار خلاصه + دو دکمه + پرش نرم به محاسبه‌گر)
 * ===================================================================== */
function uid_default_iv_tldr_items() {
	return array(
		'سه ورودی می‌فرستید — <b>شبا، کد ملی و تاریخ تولد</b> — پاسخ فقط <b>تطابق دارد یا ندارد</b> است.',
		'هیچ اطلاعات محرمانه‌ای افشا نمی‌شود؛ سرویس <b>نام دارنده حساب را برنمی‌گرداند</b>.',
		'اجرای بدون دردسر قانون <b>«تطابق هویت واریزکننده و دارنده حساب»</b> و الزامات <span class="lat">AML</span>.',
		'اتصال کامل معمولاً <b>کمتر از یک روز کاری</b>، با سندباکس پیش از هر تعهد مالی.',
	);
}
function uid_render_section_ivtldr() {
	$items_raw = uid_section_val( 'ivtldr', 'items', implode( "\n", uid_default_iv_tldr_items() ) );
	$items     = array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec" style="padding-block:24px 0">
	  <div class="iv-wrap">
	    <div class="tldr rv">
	      <div class="tldr-hd">
	        <span class="ic"><?php echo uid_iv_submit_icon(); ?></span>
	        <b><?php echo esc_html( uid_section_val( 'ivtldr', 'heading', __( 'اگر عجله دارید، همین چهار خط کافی است', 'uid-theme' ) ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'ivtldr', 'badge_text', __( '۳۰ ثانیه', 'uid-theme' ) ) ); ?></span>
	      </div>
	      <ul class="tldr-list">
	        <?php foreach ( $items as $li ) : ?>
	        <li><?php echo uid_iv_check_icon(); ?>
	          <span><?php echo wp_kses( $li, array( 'b' => array(), 'span' => array( 'class' => array() ) ) ); ?></span></li>
	        <?php endforeach; ?>
	      </ul>
	      <div class="tldr-acts">
	        <a class="iv-btn btn-cta btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_iv_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="iv-btn btn-ghost-d btn-block" data-open-modal><?php echo esc_html( uid_section_val( 'ivtldr', 'btn2_text', __( 'فرم درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	      <button class="tldr-more" type="button" data-jump-soft="#loss"><?php echo esc_html( uid_section_val( 'ivtldr', 'more_text', __( 'و اگر می‌خواهید عدد ریسک خودتان را ببینید', 'uid-theme' ) ) ); ?>
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) محاسبه‌گر ریسک واریز به حساب تاییدنشده (RISK ENGINE) — منطق محاسبه در JS هاردکد است
 * ===================================================================== */
function uid_default_iv_proof() {
	return array(
		array( 'icon' => 'shield', 'title' => 'جلوگیری از اجاره حساب و پولشویی', 'text' => 'کاربر متخلف نمی‌تواند حساب شخص دیگری را به نام خودش ثبت کند؛ سرویس همان لحظه مالکیت را رد می‌کند و مسیر واریز بسته می‌ماند.' ),
		array( 'icon' => 'sync', 'title' => 'حذف خطای انسانی در تسویه‌حساب', 'text' => 'پایان دادن به برگشت وجه، مسدودی حساب و واریز اشتباه به افراد ناآشنا — چون حساب مقصد پیش از واریز با هویت مقصد تطبیق داده می‌شود.' ),
		array( 'icon' => 'doc', 'title' => 'انطباق کامل با قوانین پرداخت', 'text' => 'اجرای بدون دردسر قانون «تطابق هویت واریزکننده و دارنده حساب» و دستورالعمل‌های مبارزه با پولشویی، با یک لاگ قابل ارائه به ناظر.' ),
	);
}
function uid_render_section_ivloss() {
	$tag   = uid_section_tag( 'ivloss', 'h2' );
	$proof = uid_section_val( 'ivloss', 'proof', uid_default_iv_proof() );
	if ( ! is_array( $proof ) ) $proof = array();
	?>
	<section class="dark sec" id="loss">
	  <div class="iv-wrap">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ivloss', 'eyebrow', __( 'قبل از اینکه ادامه بدهید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivloss', 'heading', __( 'هر واریز به حساب تاییدنشده، یک پرونده باز است', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivloss', 'text', __( 'حساب اجاره‌ای، کد ملی شخص دیگر، یک رقم اشتباه در شبا — نتیجه همه‌شان یکی است: پول رفته، حساب مسدود شده و پرونده روی میز شماست. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر ریسک روی حساب‌های تاییدنشده نشسته است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="heroA-grid rv" style="align-items:stretch">
	      <div class="engine">
	        <div class="engine-hd"><span class="live"></span><b><?php esc_html_e( 'محاسبه‌گر ریسک واریز به حساب تاییدنشده', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'عددها را جابه‌جا کنید', 'uid-theme' ); ?></span></div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_tx"><?php esc_html_e( 'واریز، برداشت یا تسویه ماهانه به حساب کاربران', 'uid-theme' ); ?></label>
	            <output id="ls_tx_v">۴٬۰۰۰</output></div>
	          <input type="range" id="ls_tx" min="200" max="60000" step="200" value="4000">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_rate"><?php esc_html_e( 'سهم حساب‌هایی که متعلق به خود کاربر نیست', 'uid-theme' ); ?></label>
	            <output id="ls_rate_v">۲٫۵<small><?php esc_html_e( 'درصد', 'uid-theme' ); ?></small></output></div>
	          <input type="range" id="ls_rate" min="5" max="300" step="5" value="25">
	        </div>
	        <p class="eng-hint"><?php esc_html_e( 'اینجا سه چیز با هم جمع می‌شود: اجاره حساب، خطای انسانی در ثبت شبا، و کلاهبرداری فیشینگ. اگر عدد دقیق خودتان را دارید، جایگزینش کنید.', 'uid-theme' ); ?></p>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_cost"><?php esc_html_e( 'هزینه هر مورد برای شما (برگشت وجه، مسدودی، پیگیری و جریمه انطباق)', 'uid-theme' ); ?></label>
	            <output id="ls_cost_v">۳٬۵۰۰٬۰۰۰</output></div>
	          <input type="range" id="ls_cost" min="200000" max="60000000" step="100000" value="3500000">
	        </div>

	        <div class="eng-out">
	          <div class="eng-cell bad"><span class="k"><?php esc_html_e( 'تراکنش‌های پرریسکی که ماهانه از فیلتر شما رد می‌شوند', 'uid-theme' ); ?></span>
	            <span class="v" id="ls_fail">۱۰۰</span></div>
	          <div class="eng-cell good"><span class="k"><?php esc_html_e( 'قبل از واریز متوقف می‌شود ', 'uid-theme' ); ?><small style="opacity:.75"><?php esc_html_e( '(برآورد ۹۵٪)', 'uid-theme' ); ?></small></span>
	            <span class="v" id="ls_save">۹۵</span></div>
	        </div>
	        <div class="eng-total">
	          <span class="k"><?php esc_html_e( 'ارزشی که ماهانه پشت یک بررسی نکرده می‌ماند', 'uid-theme' ); ?></span>
	          <span class="v amt"><span class="num" id="ls_total">۳۳۲٬۵۰۰٬۰۰۰</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span>
	        </div>
	        <div class="eng-daily"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>
	          <span><?php esc_html_e( 'یعنی روزانه حدود ', 'uid-theme' ); ?><b id="ls_daily">۱۱٬۰۸۳٬۳۳۳</b><?php esc_html_e( ' تومان — تا لحظه‌ای که این سرویس فعال شود.', 'uid-theme' ); ?></span></div>

	        <div class="micro" id="lossMicro">
	          <div class="micro-fields">
	            <p><?php esc_html_e( 'می‌خواهید همین عدد را روی داده واقعی تسویه‌های خودتان دقیق‌تر حساب کنیم؟ کارشناس یوآیدی رایگان بررسی می‌کند.', 'uid-theme' ); ?></p>
	            <form id="lossForm" novalidate>
	              <input type="hidden" name="source" value="iban-validate-lp-risk">
	              <input type="hidden" name="volume" id="lossVolume" value="">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="iv-btn btn-cta" type="button" data-submit><?php esc_html_e( 'بررسی رایگان جریان واریز من', 'uid-theme' ); ?></button>
	              </div>
	            </form>
	          </div>
	          <div class="form-ok"><?php echo uid_iv_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	            <span><?php esc_html_e( 'کارشناس یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </div>
	      </div>

	      <div class="deck-wrap">
	        <div class="proof deck on-dark" data-deck="proof">
	          <?php foreach ( $proof as $p ) : ?>
	          <article class="pfcard">
	            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_proof_icon_svg( $p['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	            <b><?php echo esc_html( $p['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $p['text'] ?? '' ); ?></p>
	          </article>
	          <?php endforeach; ?>
	        </div>
	        <div class="deck-ui" data-deck-ui="proof">
	          <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	          <span class="deck-bar"><i></i></span>
	          <span class="deck-count"></span>
	          <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	        </div>
	        <div class="callband urgent" style="margin-block-start:16px">
	          <div class="ic"><?php echo uid_iv_phone_icon(); ?></div>
	          <div class="tx"><b><?php echo esc_html( uid_section_val( 'ivloss', 'cta_heading', __( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'ivloss', 'cta_text', __( 'یک تماس کوتاه کافی است تا جریان واریز و تسویه شما بررسی و مسیر اتصال مشخص شود.', 'uid-theme' ) ) ); ?></p></div>
	          <div class="acts">
	            <a class="iv-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱) این سرویس چیست
 * ===================================================================== */
function uid_default_iv_probs() {
	return array(
		array( 'icon' => 'personx', 'title' => 'جلوگیری از اجاره حساب و پولشویی', 'text' => 'جلوگیری از ثبت حساب‌های دیگران توسط کاربران متخلف؛ حسابی که به نام کاربر نیست، پیش از هر تراکنشی رد می‌شود.' ),
		array( 'icon' => 'warn', 'title' => 'حذف خطای انسانی در تسویه‌حساب', 'text' => 'پایان دادن به برگشت وجه، مسدودی حساب یا واریز اشتباه به افراد ناآشنا — حتی وقتی کاربر خودش یک رقم را اشتباه وارد کرده است.' ),
		array( 'icon' => 'shield', 'title' => 'انطباق کامل با قوانین پرداخت', 'text' => 'اجرای بدون دردسر قانون «تطابق هویت واریزکننده و دارنده حساب» و الزامات مبارزه با پولشویی، بدون افزودن یک مرحله دستی به فرایند.' ),
	);
}
function uid_render_section_ivwhat() {
	$tag   = uid_section_tag( 'ivwhat', 'h2' );
	$probs = uid_section_val( 'ivwhat', 'probs', uid_default_iv_probs() );
	if ( ! is_array( $probs ) ) $probs = array();
	?>
	<section class="sec" id="what" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivwhat', 'fold_title', __( 'سه ورودی می‌فرستید، یک پاسخ قطعی می‌گیرید', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivwhat', 'fold_teaser', __( 'این وب‌سرویس دقیقاً چه کاری انجام می‌دهد', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivwhat', 'eyebrow', __( 'معرفی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivwhat', 'heading', __( 'وب‌سرویس تطبیق کد ملی و شماره شبا چیست؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo wp_kses( uid_section_val( 'ivwhat', 'text', __( 'این وب‌سرویس یک راهکار اعتبارسنجی مالی آنلاین است که مشخص می‌کند آیا شماره شبای ۲۴ رقمی ارائه‌شده، دقیقاً به همان کد ملی و تاریخ تولد ثبت‌شده تعلق دارد یا خیر. سیستم نتیجه را به‌صورت <span class="lat">true</span> یا <span class="lat">false</span> در پاسخ برمی‌گرداند.', 'uid-theme' ) ), array( 'span' => array( 'class' => array() ) ) ); ?></p>
	    </div>

	    <div class="tri rv">
	      <div class="tri-in">
	        <div class="tri-row">
	          <span class="ic"><?php echo uid_iv_bank_icon(); ?></span>
	          <span class="tx"><span class="k">iban</span><span class="v">IR78 0170 1000 1485 2301 0045 21</span></span>
	        </div>
	        <div class="tri-row">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4" width="19" height="16" rx="3"/><circle cx="9" cy="11" r="2.5"/><path d="M5 17c.6-1.8 2.2-2.6 4-2.6s3.4.8 4 2.6M15 10h4M15 14h4"/></svg></span>
	          <span class="tx"><span class="k">nationalId</span><span class="v">0079845612</span></span>
	        </div>
	        <div class="tri-row">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></svg></span>
	          <span class="tx"><span class="k">birthDate</span><span class="v">1370/05/17</span></span>
	        </div>
	      </div>
	      <div class="tri-mid">
	        <span class="pill"><?php esc_html_e( 'استعلام برخط بین‌بانکی', 'uid-theme' ); ?></span>
	        <span class="ar"><?php echo uid_iv_arrow_icon(); ?></span>
	        <span class="pill"><?php esc_html_e( 'پاسخ ', 'uid-theme' ); ?><span class="lat">JSON</span></span>
	      </div>
	      <div class="tri-out">
	        <span class="cap">"isMatched"</span>
	        <span class="bool"><?php echo uid_iv_check_icon(); ?>true</span>
	        <span class="alt"><?php esc_html_e( 'و اگر حساب متعلق به این شخص نباشد، همان فیلد ', 'uid-theme' ); ?><span class="lat">false</span><?php esc_html_e( ' برمی‌گردد. همین. نه نامی، نه موجودی‌ای، نه اطلاعات حسابی.', 'uid-theme' ); ?></span>
	      </div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(28px,4vw,44px)">
	      <h3 class="h-sub" style="margin-block-end:16px;text-align:center"><?php esc_html_e( 'چه مسئله‌ای را حل می‌کند؟', 'uid-theme' ); ?></h3>
	      <div class="probs">
	        <?php foreach ( $probs as $p ) : ?>
	        <article class="prob">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_probs_icon_svg( $p['icon'] ?? 'personx' ), array( 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $p['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $p['text'] ?? '' ); ?></p>
	        </article>
	        <?php endforeach; ?>
	      </div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(26px,4vw,42px)">
	      <h3 class="h-sub" style="margin-block-end:14px"><?php esc_html_e( 'پاسخ سرویس چه چیزهایی برمی‌گرداند؟', 'uid-theme' ); ?></h3>
	      <div class="ftags">
	        <span class="ftag warm"><span class="key">isMatched</span><?php esc_html_e( 'تطابق مالکیت: ', 'uid-theme' ); ?><span class="lat">true</span> / <?php esc_html_e( 'عدم تطابق: ', 'uid-theme' ); ?><span class="lat">false</span></span>
	        <span class="ftag"><span class="key">status.code</span><?php esc_html_e( 'کد وضعیت یا خطای عملیات', 'uid-theme' ); ?></span>
	        <span class="ftag"><span class="key">status.message</span><?php esc_html_e( 'توضیح وضعیت یا خطا', 'uid-theme' ); ?></span>
	        <span class="ftag"><span class="key">requestId</span><?php esc_html_e( 'شناسه پیگیری یکتای درخواست', 'uid-theme' ); ?></span>
	      </div>
	    </div>

	    <div class="secbox rv" style="margin-block-start:22px;background:var(--teal-l);border-color:rgba(41,188,206,.3)">
	      <div class="ic" style="color:var(--teal-d)"><?php echo uid_iv_shield_icon(); ?></div>
	      <div><b style="color:#0E5A66"><?php echo esc_html( uid_section_val( 'ivwhat', 'privacy_heading', __( 'محرمانگی داده، بخشی از طراحی سرویس است', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#136B4C"><?php echo esc_html( uid_section_val( 'ivwhat', 'privacy_text', __( 'این وب‌سرویس صرفاً وضعیت تطابق یا عدم تطابق را بازمی‌گرداند و اطلاعات حساب را افشا نمی‌کند. یعنی می‌توانید مالکیت را تایید کنید بدون آنکه نام، موجودی یا هیچ داده حساس دیگری از دارنده حساب وارد سامانه شما شود.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۲) تطبیق شبا در برابر استعلام شبا
 * ===================================================================== */
function uid_default_iv_vs_bad_items() {
	return array(
		'خروجی، داده هویتی صاحب حساب است',
		'مقایسه نام با کاربر باید سمت شما انجام شود',
		'تشابه اسمی و اختلاف املا، تصمیم را مبهم می‌کند',
		'داده‌ای وارد سامانه شما می‌شود که لازم نبود نگهش دارید',
	);
}
function uid_default_iv_vs_good_items() {
	return array(
		'خروجی یک تصمیم قطعی است: <span class="lat">true</span> یا <span class="lat">false</span>',
		'منطق مقایسه سمت یوآیدی انجام می‌شود، نه سمت شما',
		'تاریخ تولد به‌عنوان پارامتر سوم، ضریب دقت را بالا می‌برد',
		'هیچ داده محرمانه‌ای به سامانه شما منتقل نمی‌شود',
	);
}
function uid_render_section_ivvs() {
	$tag        = uid_section_tag( 'ivvs', 'h2' );
	$bad_items  = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ivvs', 'bad_items', implode( "\n", uid_default_iv_vs_bad_items() ) ) ) ) );
	$good_items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ivvs', 'good_items', implode( "\n", uid_default_iv_vs_good_items() ) ) ) ) );
	?>
	<section class="sec" id="vs" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivvs', 'fold_title', __( 'تفاوت تطبیق شبا با استعلام شبا', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivvs', 'fold_teaser', __( 'دو سرویس شبیه به هم که دو سوال کاملاً متفاوت را جواب می‌دهند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ivvs', 'eyebrow', __( 'پرتکرارترین سوال فنی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivvs', 'heading', __( '«استعلام شبا» و «تطبیق شبا» یکی نیستند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo wp_kses( uid_section_val( 'ivvs', 'text', __( 'هر دو سرویس با شماره شبا کار می‌کنند، ولی یکی اطلاعات را <b>می‌آورد</b> و دیگری ادعای کاربر را <b>تایید یا رد می‌کند</b>. انتخاب اشتباه بین این دو، یا شما را دچار افشای غیرلازم داده می‌کند یا بی‌دفاع در برابر اجاره حساب.', 'uid-theme' ) ), array( 'b' => array() ) ); ?></p>
	    </div>

	    <div class="vs2 rv">
	      <article class="vscard bad">
	        <span class="tag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21l-4.3-4.3"/><circle cx="11" cy="11" r="7"/></svg><?php echo esc_html( uid_section_val( 'ivvs', 'bad_tag', __( 'وب‌سرویس استعلام شبا', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'ivvs', 'bad_title', __( 'سوال: این حساب مال کیست؟', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'ivvs', 'bad_text', __( 'نام صاحب حساب و فعال بودن شماره شبا را استخراج می‌کند و به سامانه شما تحویل می‌دهد.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $bad_items as $li ) : ?>
	          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 12h14"/></svg><?php echo wp_kses( $li, array( 'span' => array( 'class' => array() ) ) ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	      <article class="vscard good">
	        <span class="tag"><?php echo uid_iv_check_icon(); ?><?php echo esc_html( uid_section_val( 'ivvs', 'good_tag', __( 'وب‌سرویس تطبیق شبا و کد ملی', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'ivvs', 'good_title', __( 'سوال: این حساب مال همین شخص است؟', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'ivvs', 'good_text', __( 'به‌صورت سیستمی بررسی می‌کند که آیا این حساب متعلق به یک کد ملی مشخص هست یا خیر.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $good_items as $li ) : ?>
	          <li><?php echo uid_iv_check_icon(); ?><?php echo wp_kses( $li, array( 'span' => array( 'class' => array() ) ) ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	    </div>

	    <div class="secbox rv" style="margin-block-start:20px;background:var(--orange-l);border-color:rgba(248,148,40,.32)">
	      <div class="ic" style="color:var(--orange-d)"><?php echo uid_iv_warn_icon(); ?></div>
	      <div><b style="color:#8A5410"><?php echo esc_html( uid_section_val( 'ivvs', 'note_heading', __( 'در جریان‌های حساس، «تصمیم» لازم است نه «داده»', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#9A6A22"><?php echo esc_html( uid_section_val( 'ivvs', 'note_text', __( 'وقتی پول در حال خارج شدن از سامانه شماست، تیم فنی نباید درگیر مقایسه رشته‌ای نام‌ها شود. یک فیلد بولین که پیش از فراخوانی درگاه بررسی می‌شود، هم سریع‌تر است، هم قابل ممیزی، هم برای تیم انطباق قابل دفاع.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۳) نحوه کار سرویس (استپر ۴مرحله‌ای)
 * ===================================================================== */
function uid_default_iv_how() {
	return array(
		array(
			'cap'     => 'ارسال داده‌های ورودی',
			'eyebrow' => 'مرحله ۱ — سمت سامانه شما',
			'title'   => 'ارسال کد ملی، شماره شبا و تاریخ تولد',
			'text'    => 'سامانه شما کد ملی ۱۰ رقمی، شماره شبا (با یا بدون پیشوند IR) و تاریخ تولد کاربر را از طریق API امن ارسال می‌کند. این همان داده‌هایی است که در فرایند احراز هویت قبلاً از کاربر گرفته‌اید — فیلد جدیدی به فرم اضافه نمی‌شود.',
			'chips'   => "POST\nسه پارامتر\nارتباط امن",
		),
		array(
			'cap'     => 'استعلام برخط بانکی',
			'eyebrow' => 'مرحله ۲ — سمت یوآیدی',
			'title'   => 'استعلام برخط از زیرساخت بین‌بانکی',
			'text'    => 'درخواست به‌صورت امن به زیرساخت بین‌بانکی ارسال و داده‌های دریافتی با اطلاعات صاحب حساب تطبیق داده می‌شود. اتصال مستقیم به تمامی بانک‌های عضو شبکه شتاب و شاپرک، پوشش سرویس را کامل نگه می‌دارد.',
			'chips'   => "شبکه شتاب\nشاپرک\nاستعلام برخط",
		),
		array(
			'cap'     => 'تحلیل و اعتبارسنجی',
			'eyebrow' => 'مرحله ۳ — منطق تصمیم',
			'title'   => 'پردازش شرط تطابق مالکیت و فعال بودن حساب',
			'text'    => 'دو چیز هم‌زمان بررسی می‌شود: اینکه حساب مقصد واقعاً به این هویت تعلق دارد، و اینکه حساب بانکی مقصد فعال است. اگر شبا ساختار درستی نداشته باشد یا حساب مسدود باشد، پاسخ در قالب کدهای خطای استاندارد برمی‌گردد.',
			'chips'   => "تطابق مالکیت\nوضعیت حساب\nکد خطای استاندارد",
		),
		array(
			'cap'     => 'ارسال خروجی ساختاریافته',
			'eyebrow' => 'مرحله ۴ — بازگشت پاسخ',
			'title'   => 'دریافت پاسخ در لحظه در قالب JSON',
			'text'    => 'پاسخ شامل فیلد وضعیت isMatched و کد و پیام وضعیت است. سامانه شما بر اساس همین یک فیلد تصمیم می‌گیرد که واریز انجام شود یا مسیر به بررسی دستی برود — بدون آنکه داده حساسی ذخیره کرده باشد.',
			'chips'   => "JSON\nisMatched\nstatus.code\nstatus.message",
		),
	);
}
function uid_render_section_ivhow() {
	$tag   = uid_section_tag( 'ivhow', 'h2' );
	$steps = uid_section_val( 'ivhow', 'steps', uid_default_iv_how() );
	if ( ! is_array( $steps ) || empty( $steps ) ) return;
	?>
	<section class="sec" id="how" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivhow', 'fold_title', __( 'مراحل عملکرد وب‌سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivhow', 'fold_teaser', __( 'از ارسال داده تا دریافت پاسخ ساختاریافته در چهار قدم', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivhow', 'eyebrow', __( 'پشت صحنه یک فراخوانی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivhow', 'heading', __( 'از سه ورودی تا یک تصمیم، در چهار قدم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivhow', 'text', __( 'روی هر مرحله بزنید تا ببینید در آن لحظه دقیقاً چه اتفاقی می‌افتد و چه چیزی رد و بدل می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="stepper rv" id="howStep">
	      <div class="st-track" id="st_track" role="tablist" aria-label="<?php esc_attr_e( 'مراحل عملکرد سرویس', 'uid-theme' ); ?>">
	        <?php foreach ( $steps as $i => $s ) : ?>
	        <button class="st-node<?php echo 0 === $i ? ' on' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" data-st="<?php echo esc_attr( $i ); ?>">
	          <span class="st-dot"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span><span class="st-cap"><?php echo esc_html( $s['cap'] ?? '' ); ?></span></button>
	        <?php endforeach; ?>
	      </div>

	      <div class="st-panel">
	        <div class="st-body">
	          <?php foreach ( $steps as $i => $s ) :
	            $chips = array_filter( array_map( 'trim', explode( "\n", $s['chips'] ?? '' ) ) );
	          ?>
	          <div data-st-pane="<?php echo esc_attr( $i ); ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
	            <span class="st-eyebrow"><?php echo esc_html( $s['eyebrow'] ?? '' ); ?></span>
	            <h3><?php echo wp_kses( $s['title'] ?? '', array( 'span' => array( 'class' => array() ) ) ); ?></h3>
	            <p><?php echo wp_kses( $s['text'] ?? '', array( 'span' => array( 'class' => array() ) ) ); ?></p>
	            <?php if ( $chips ) : ?>
	            <div class="st-chips">
	              <?php foreach ( $chips as $j => $c ) : ?>
	              <span<?php echo 0 === $j ? ' class="src"' : ''; ?>><?php echo esc_html( $c ); ?></span>
	              <?php endforeach; ?>
	            </div>
	            <?php endif; ?>
	          </div>
	          <?php endforeach; ?>
	        </div>
	        <div class="st-nav">
	          <button type="button" id="st_prev" aria-label="<?php esc_attr_e( 'مرحله قبل', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	          <span class="st-count" id="st_count"><?php echo esc_html( uid_fa_digits( '1 / ' . count( $steps ) ) ); ?></span>
	          <button type="button" id="st_next" aria-label="<?php esc_attr_e( 'مرحله بعد', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	        </div>
	      </div>

	      <div class="st-foot">
	        <span class="small"><?php echo esc_html( uid_section_val( 'ivhow', 'foot_text', __( 'کل این چرخه در یک فراخوانی انجام می‌شود؛ تیم فنی شما فقط یک درخواست می‌نویسد.', 'uid-theme' ) ) ); ?></span>
	        <button class="iv-btn iv-btn-navy iv-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'ivhow', 'btn1_text', __( 'دریافت کلید و مستندات', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۴) کاربردها (انتخابگر صنعت)
 * ===================================================================== */
function uid_default_iv_who() {
	return array(
		array( 'icon' => 'crypto', 'label' => 'صرافی رمزارز و معاملات', 'title' => 'صرافی‌های رمزارز و پلتفرم‌های معاملاتی', 'text' => 'احراز مالکیت حساب بانکی جهت واریز و برداشت ریالی امن و جلوگیری از تخلفات فیشینگ — پیش از آنکه ریال از کیف پول کاربر خارج شود.', 'win' => 'برداشت ریالی امن، بدون حساب اجاره‌ای' ),
		array( 'icon' => 'market', 'label' => 'مارکت‌پلیس و اقتصاد مشارکتی', 'title' => 'مارکت‌پلیس‌ها و سیستم‌های اقتصاد مشارکتی', 'text' => 'تایید اطلاعات بانکی فروشندگان، تامین‌کنندگان، رانندگان و تحویل‌دهندگان کالا پیش از تسویه دوره‌ای.', 'win' => 'تسویه دوره‌ای بدون برگشتی' ),
		array( 'icon' => 'lend', 'label' => 'لندتک و تسهیلات اقساطی', 'title' => 'پلتفرم‌های لندتک و تسهیلات اقساطی', 'text' => 'اطمینان از واریز وام یا اعتبار به حساب شخصی خودِ متقاضی — نه به حسابی که شخص ثالثی در اختیار او گذاشته است.', 'win' => 'واریز تسهیلات به حساب خود متقاضی' ),
		array( 'icon' => 'insure', 'label' => 'بیمه و پرداخت خسارت', 'title' => 'شرکت‌های بیمه و پرداخت خسارت', 'text' => 'پرداخت مستقیم مبالغ خسارت به شماره شبای تاییدشده بیمه‌گذار، بدون رفت‌وبرگشت اداری و بدون ریسک واریز به شخص دیگر.', 'win' => 'پرداخت خسارت به شبای تاییدشده' ),
		array( 'icon' => 'payroll', 'label' => 'حقوق و دستمزد سازمانی', 'title' => 'سیستم‌های حقوق و دستمزد سازمانی', 'text' => 'صحت‌سنجی شماره شبای کارکنان و پیمانکاران جهت تسویه گروهی بدون برگشتی — پیش از آنکه فایل پرداخت دسته‌ای به بانک برود.', 'win' => 'پرداخت دسته‌ای بدون ردیف برگشتی' ),
		array( 'icon' => 'shop', 'label' => 'فروشگاه اینترنتی و مرجوعی', 'title' => 'فروشگاه‌های اینترنتی در فرایند مرجوعی کالا', 'text' => 'واریز وجه مرجوعی به حساب شخصی خریدارِ همان سفارش، نه به هر شبایی که در تیکت پشتیبانی اعلام شده است.', 'win' => 'عودت وجه فقط به خودِ خریدار' ),
	);
}
function uid_render_section_ivwho() {
	$tag   = uid_section_tag( 'ivwho', 'h2' );
	$items = uid_section_val( 'ivwho', 'items', uid_default_iv_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="who" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivwho', 'fold_title', __( 'کاربرد در کسب‌وکارها', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivwho', 'fold_teaser', __( 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivwho', 'eyebrow', __( 'کجا به کار می‌آید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivwho', 'heading', __( 'هر جا که پول از سامانه شما بیرون می‌رود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivwho', 'text', __( 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="rv">
	      <div class="pick-chips" data-pick-chips="ind" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب صنعت', 'uid-theme' ); ?>"></div>
	      <div class="pick n6" data-pick="ind">
	        <?php foreach ( $items as $it ) :
	          $color = uid_iv_who_color( $it['icon'] ?? 'crypto' );
	        ?>
	        <article class="pick-card" data-label="<?php echo esc_attr( $it['label'] ?? '' ); ?>">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_who_icon_svg( $it['icon'] ?? 'crypto' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="win"><?php echo uid_iv_check_icon(); ?><?php echo esc_html( $it['win'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="pick-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( 'برای دیدن صنعت‌های دیگر، از نوارِ بالا انتخاب کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۵) مزایای رقابتی (کارت‌دک) + پوشش شبکه بانکی
 * ===================================================================== */
function uid_default_iv_gain() {
	return array(
		array( 'icon' => 'fast',     'title' => 'پاسخ‌دهی در لحظه',                'text' => 'مناسب جریان‌های کاربری حساس و ثبت‌نام‌های پرتراکنش؛ بررسی مالکیت وسط فرم انجام می‌شود بدون آنکه کاربر منتظر بماند.', 'tagline' => 'بدون افت تجربه کاربری' ),
		array( 'icon' => 'bank',     'title' => 'شناسایی خودکار بانک صادرکننده',   'text' => 'استخراج هوشمند نام و کد بانک از روی شماره شبا؛ یعنی می‌توانید بانک مقصد را در همان صفحه به کاربر نشان بدهید و خطای انتخاب حساب را کم کنید.', 'tagline' => 'بدون فراخوانی اضافه' ),
		array( 'icon' => 'bday',     'title' => 'امکان ورود تاریخ تولد',           'text' => 'پارامتر سوم، ضریب دقت تطبیق را بالا می‌برد و سرویس را با آخرین الزامات سرویس‌های بانکی منطبق نگه می‌دارد.', 'tagline' => 'دقت بالاتر در تصمیم نهایی' ),
		array( 'icon' => 'support',  'title' => 'پشتیبانی اختصاصی سازمانی',        'text' => 'همراهی تیم فنی یوآیدی از مرحله اتصال تا مانیتورینگ روزمره؛ یک کارشناس مشخص پاسخگوی تیم شماست، نه یک صف تیکت.', 'tagline' => 'کارشناس مشخص، نه صف تیکت' ),
		array( 'icon' => 'uptime',   'title' => 'آپ‌تایم ۹۹.۹٪ با مانیتورینگ ۲۴ ساعته', 'text' => 'سرویسی که روی مسیر تسویه می‌نشیند نباید خودش تبدیل به گلوگاه شود؛ پایداری سرویس به‌صورت شبانه‌روزی رصد می‌شود.', 'tagline' => 'پایداری قابل اتکا در ساعات اوج' ),
		array( 'icon' => 'costfair', 'title' => 'هزینه فقط بابت استعلام موفق و معتبر', 'text' => 'محاسبه هزینه فقط بر اساس استعلام‌های موفق است و برای حجم‌های بالای ماهانه تعرفه پلکانی و پلن سازمانی اختصاصی در نظر گرفته شده.', 'tagline' => 'ریسک مالی صفر برای شروع' ),
	);
}
function uid_default_iv_banks() {
	return array(
		'ملی', 'ملت', 'صادرات', 'تجارت', 'سپه', 'کشاورزی', 'مسکن', 'رفاه کارگران',
		'پاسارگاد', 'سامان', 'پارسیان', 'اقتصاد نوین', 'کارآفرین', 'شهر', 'دی',
		'سینا', 'آینده', 'پست بانک',
	);
}
function uid_render_section_ivgain() {
	$tag        = uid_section_tag( 'ivgain', 'h2' );
	$items      = uid_section_val( 'ivgain', 'items', uid_default_iv_gain() );
	if ( ! is_array( $items ) || empty( $items ) ) $items = array();
	$banks_raw  = uid_section_val( 'ivgain', 'banks_items', implode( "\n", uid_default_iv_banks() ) );
	$banks      = array_values( array_filter( array_map( 'trim', explode( "\n", $banks_raw ) ) ) );
	?>
	<section class="sec" id="gain" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivgain', 'fold_title', __( 'مزایای رقابتی سرویس یوآیدی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivgain', 'fold_teaser', __( 'شش دلیلی که تیم فنی و تیم انطباق هر دو قبول می‌کنند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivgain', 'eyebrow', __( 'چرا یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivgain', 'heading', __( 'مزایای رقابتی وب‌سرویس تطبیق شبا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivgain', 'text', __( 'این سرویس روی مسیر پول می‌نشیند؛ بنابراین سرعت، دقت و پشتیبانی آن مستقیماً روی ریسک عملیاتی شما اثر می‌گذارد.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="gain">
	        <?php foreach ( $items as $it ) :
	          $color = uid_iv_gain_color( $it['icon'] ?? 'fast' );
	        ?>
	        <article class="dcard">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_gain_icon_svg( $it['icon'] ?? 'fast' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="tagline"><?php echo uid_iv_check_icon(); ?><?php echo esc_html( $it['tagline'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="gain">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( 'برای دیدن کارت بعدی، بکشید یا روی کارت بزنید', 'uid-theme' ); ?></div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(28px,4vw,44px)">
	      <h3 class="h-sub" style="margin-block-end:14px;text-align:center"><?php echo esc_html( uid_section_val( 'ivgain', 'banks_heading', __( 'پوشش شبکه بانکی سرویس', 'uid-theme' ) ) ); ?></h3>
	      <p class="small" style="text-align:center;max-width:66ch;margin:0 auto 18px"><?php echo esc_html( uid_section_val( 'ivgain', 'banks_text', __( 'اتصال مستقیم به تمامی بانک‌های عضو شبکه شتاب و شاپرک؛ شماره شبای صادرشده توسط بانک‌ها و موسسات مالی مجاز کشور پشتیبانی می‌شود.', 'uid-theme' ) ) ); ?></p>
	      <div class="banks">
	        <?php foreach ( $banks as $b ) : ?>
	        <span class="bankchip"><?php echo esc_html( $b ); ?></span>
	        <?php endforeach; ?>
	        <span class="bankchip more"><?php echo esc_html( uid_section_val( 'ivgain', 'banks_more', __( 'و سایر بانک‌ها و موسسات مجاز عضو شتاب', 'uid-theme' ) ) ); ?></span>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۶) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_iv_team_rows() {
	return array(
		array( 'icon' => 'connect', 'title' => 'کارشناس فنی اختصاصی برای دوره اتصال', 'text' => 'از تحویل کلید API و دسترسی سندباکس تا اولین تطبیق موفق روی محیط عملیاتی، یک نفر مشخص پاسخگوی تیم شماست.' ),
		array( 'icon' => 'monitor', 'title' => 'مانیتورینگ روزمره و گزارش‌دهی دوره‌ای', 'text' => 'پایداری سرویس به‌صورت ۲۴ ساعته رصد می‌شود و گزارش تعداد استعلام‌ها، نرخ پاسخ موفق و لاگ تراکنش‌ها در اختیار شما قرار می‌گیرد تا هزینه و عملکرد همیشه قابل راستی‌آزمایی باشد.' ),
		array( 'icon' => 'doc', 'title' => 'مستندات دقیق به همراه محیط آزمایشی', 'text' => 'ساختار درخواست، ساختار پاسخ و جدول پارامترها همگی مستند و در دسترس تیم توسعه شماست؛ به همین دلیل ادغام کامل معمولاً در کمتر از یک روز کاری انجام می‌شود.' ),
		array( 'icon' => 'shield', 'title' => 'سابقه از سال ۱۳۹۶ در زیرساخت هویت دیجیتال', 'text' => 'شرکت دانش‌بنیان بینش هوشمند نسل پیشرو، کارگزار مورد تایید سامانه‌های سجام و ثنا، با تمرکز اختصاصی روی سرویس‌های استعلام و احراز هویت.' ),
	);
}
function uid_default_iv_pledge_items() {
	return array(
		'دسترسی به محیط سندباکس و کلید آزمایشی پیش از هر تعهد مالی',
		'محاسبه هزینه فقط بر اساس استعلام‌های موفق و معتبر',
		'تعرفه پلکانی و پلن سازمانی اختصاصی برای حجم بالای ماهانه',
		'همراهی در طراحی جریان واریز و تسویه برای کمترین ریسک و کمترین اصطکاک',
	);
}
function uid_render_section_ivteam() {
	$rows       = uid_section_val( 'ivteam', 'rows', uid_default_iv_team_rows() );
	$pledge_raw = uid_section_val( 'ivteam', 'pledge_items', implode( "\n", uid_default_iv_pledge_items() ) );
	$pledge     = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	$tag = uid_section_tag( 'ivteam', 'h2' );
	?>
	<section class="sec" id="team" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivteam', 'fold_title', __( 'تیم متخصص و پشتیبانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivteam', 'fold_teaser', __( 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivteam', 'eyebrow', __( 'پشت این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php esc_html_e( 'یک ', 'uid-theme' ); ?><span class="lat">API</span><?php echo esc_html( uid_section_val( 'ivteam', 'heading', __( ' نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivteam', 'text', __( 'این سرویس دقیقاً روی نقطه‌ای می‌نشیند که پول از سامانه شما خارج می‌شود. به همین دلیل تیم فنی یوآیدی از اولین تماس تا اولین تطبیق موفق روی محیط عملیاتی همراه شماست.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="team rv">
	      <div class="team-list">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_team_icon_svg( $r['icon'] ?? 'connect' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><?php echo uid_iv_check_icon(); ?></span>
	          <b><?php echo esc_html( uid_section_val( 'ivteam', 'pledge_heading', __( 'تعهد یوآیدی به تیم فنی شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_iv_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div class="btn-row" style="margin-block-start:18px">
	          <a class="iv-btn iv-btn-navy" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_iv_phone_icon(); ?>
	            <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	          <button class="iv-btn btn-ghost" data-open-modal><?php esc_html_e( 'درخواست کلید آزمایشی', 'uid-theme' ); ?></button>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۷) مستندات فنی و نمونه کد
 * ===================================================================== */
function uid_render_section_ivdev() {
	$tag = uid_section_tag( 'ivdev', 'h2' );
	?>
	<section class="sec" id="dev" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivdev', 'fold_title', __( 'بخش فنی و نمونه کدها', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivdev', 'fold_teaser', __( 'نمونه درخواست، نمونه پاسخ و جدول پارامترها', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivdev', 'eyebrow', __( 'برای تیم توسعه شما', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivdev', 'heading', __( 'یک فراخوانی، یک تصمیم، تمام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php esc_html_e( 'برای استفاده از سرویس ', 'uid-theme' ); ?><span class="lat">API</span><?php esc_html_e( ' تطابقی اطلاعات مالی (شماره شبا یا ', 'uid-theme' ); ?><span class="lat">IBAN</span><?php esc_html_e( ' با اطلاعات هویتی) یوآیدی می‌بایست سرویس زیر را با پارامترهای درخواستی فراخوانی نمایید. این سرویس با دریافت شماره شبای کاربر، کدملی و تاریخ تولد، صحت اطلاعات کاربر (', 'uid-theme' ); ?><span class="lat">true</span><?php esc_html_e( ' یا ', 'uid-theme' ); ?><span class="lat">false</span><?php esc_html_e( ') را در پاسخ برمی‌گرداند.', 'uid-theme' ); ?></p>
	    </div>

	    <div class="codepanel rv">
	      <div>
	        <div class="ftags" style="margin-block-end:18px">
	          <span class="ftag"><span class="key">businessId</span><?php esc_html_e( 'شناسه کسب‌وکار', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">businessToken</span><?php esc_html_e( 'توکن کسب‌وکار', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">iban</span><?php esc_html_e( 'شماره شبای کاربر', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">nationalId</span><?php esc_html_e( 'کد ملی کاربر', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">birthDate</span><?php esc_html_e( 'تاریخ تولد کاربر', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">isMatched</span><?php esc_html_e( 'نتیجه تطابق مالکیت', 'uid-theme' ); ?></span>
	        </div>
	        <div class="secbox" style="background:var(--n50);border-color:var(--n200)">
	          <div class="ic" style="color:var(--navy)"><?php echo uid_iv_info_icon(); ?></div>
	          <div><b style="color:var(--ink)"><?php esc_html_e( 'درباره شناسه و توکن کسب‌وکار', 'uid-theme' ); ?></b>
	            <p style="color:var(--n600)"><?php esc_html_e( 'مقادیر ', 'uid-theme' ); ?><span class="lat">businessId</span><?php esc_html_e( ' و ', 'uid-theme' ); ?><span class="lat">businessToken</span><?php esc_html_e( ' توسط یوآیدی در اختیار شما قرار می‌گیرد و هر دو الزامی هستند. نسخه فعلی مستندات سرویس ', 'uid-theme' ); ?><span class="mono">1.1.0</span><?php esc_html_e( ' است.', 'uid-theme' ); ?></p></div>
	        </div>
	        <div class="btn-row" style="margin-block-start:20px">
	          <button class="iv-btn btn-cta" data-open-modal><?php echo uid_iv_submit_icon(); ?><?php echo esc_html( uid_section_val( 'ivdev', 'btn1_text', __( 'دریافت کلید وب‌سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="iv-btn btn-ghost" href="<?php echo esc_url( home_url( '/iban-validate-docs/' ) ); ?>"><?php esc_html_e( 'مشاهده مستندات کامل ', 'uid-theme' ); ?><span class="lat">API</span></a>
	        </div>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button type="button" class="on" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button type="button" data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" type="button" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req"><span class="m">POST</span> https://json-api.uid.ir/api/validate/iban/ownership
<span class="k">Content-Type</span>: application/json;charset=UTF-8

{
  <span class="k">"requestContext"</span>: {
    <span class="k">"apiInfo"</span>: {
      <span class="k">"businessId"</span>: <span class="s">"&lt;UID_BUSINESS_ID&gt;"</span>,
      <span class="k">"businessToken"</span>: <span class="s">"&lt;UID_BUSINESS_TOKEN&gt;"</span>
    }
  },
  <span class="k">"iban"</span>: <span class="s">"IR123456789012345678901234"</span>,
<span class="m">  // String - 26 Digits with IR</span>
  <span class="k">"nationalId"</span>: <span class="s">"0123456789"</span>,
  <span class="k">"birthDate"</span>: <span class="s">"1370/05/17"</span>
}</pre>
<pre class="code-pane" data-code-pane="res">{
  <span class="k">"responseContext"</span>: {
    <span class="k">"status"</span>: {
      <span class="k">"code"</span>: <span class="s">0</span>,
      <span class="k">"message"</span>: <span class="s">"SUCCESS."</span>,
      <span class="k">"details"</span>: []
    },
    <span class="k">"requestId"</span>: <span class="s">""</span>,
    <span class="k">"correlationId"</span>: <span class="s">""</span>,
    <span class="k">"navigationURI"</span>: <span class="s">""</span>,
    <span class="k">"nextStepToken"</span>: <span class="s">""</span>,
    <span class="k">"userSessionId"</span>: <span class="s">""</span>,
    <span class="k">"custom"</span>: {}
  },
  <span class="k">"isMatched"</span>: <span class="s">true</span>
}</pre>
	        </div>
	      </div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(28px,4vw,44px)">
	      <h3 class="h-sub" style="margin-block-end:14px"><?php esc_html_e( 'پارامترهای درخواست', 'uid-theme' ); ?></h3>
	      <div class="ptbl">
	        <div class="r hd"><span><?php esc_html_e( 'فیلد', 'uid-theme' ); ?></span><span><?php esc_html_e( 'نوع', 'uid-theme' ); ?></span><span><?php esc_html_e( 'وضعیت', 'uid-theme' ); ?></span><span><?php esc_html_e( 'توضیح', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">businessId</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'شناسه کسب‌وکار (توسط یوآیدی در اختیار قرار می‌گیرد)', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">businessToken</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'توکن کسب‌وکار (توسط یوآیدی در اختیار قرار می‌گیرد)', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">iban</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'شماره شبای کاربر', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">nationalId</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'کد ملی کاربر', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">birthDate</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'تاریخ تولد کاربر', 'uid-theme' ); ?></span></div>
	      </div>

	      <h3 class="h-sub" style="margin-block:26px 14px"><?php esc_html_e( 'پارامترهای پاسخ', 'uid-theme' ); ?></h3>
	      <div class="ptbl">
	        <div class="r hd"><span><?php esc_html_e( 'فیلد', 'uid-theme' ); ?></span><span><?php esc_html_e( 'نوع', 'uid-theme' ); ?></span><span><?php esc_html_e( 'بخش', 'uid-theme' ); ?></span><span><?php esc_html_e( 'توضیح', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">code</span><span class="t">int</span><span class="k res">status</span><span class="d"><?php esc_html_e( 'کد خطا', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">message</span><span class="t">string</span><span class="k res">status</span><span class="d"><?php esc_html_e( 'توضیحات خطا', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">isMatched</span><span class="t">bool</span><span class="k res">root</span><span class="d"><?php esc_html_e( 'تطابق مالکیت: ', 'uid-theme' ); ?><b class="lat">true</b><?php esc_html_e( ' — عدم تطابق مالکیت: ', 'uid-theme' ); ?><b class="lat">false</b></span></div>
	      </div>

	      <div class="price-note" style="margin-block-start:16px">
	        <?php echo uid_iv_info_icon(); ?>
	        <span><?php esc_html_e( 'مقدار ', 'uid-theme' ); ?><span class="lat">iban</span><?php esc_html_e( ' یک رشته ۲۶ کاراکتری همراه با پیشوند ', 'uid-theme' ); ?><span class="lat">IR</span><?php esc_html_e( ' است. کل تصمیم سرویس در یک فیلد بولین (', 'uid-theme' ); ?><span class="lat">isMatched</span><?php esc_html_e( ') خلاصه می‌شود؛ بنابراین شرط سمت شما یک ', 'uid-theme' ); ?><span class="lat">if</span><?php esc_html_e( ' ساده است، نه مقایسه رشته‌ای نام‌ها.', 'uid-theme' ); ?></span>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۸) سرویس‌های مکمل
 * ===================================================================== */
function uid_default_iv_xsell() {
	return array(
		array( 'icon' => 'card',      'title' => 'وب‌سرویس استعلام شماره شبا', 'text' => 'با ارسال شماره شبا، اطلاعات حساب شامل نام دارنده حساب و وضعیت حساب دریافت می‌شود — وقتی به خودِ داده نیاز دارید، نه فقط به تایید مالکیت.', 'url' => '/api-inquiry-iban/' ),
		array( 'icon' => 'cardcheck', 'title' => 'وب‌سرویس تطبیق شماره کارت با کد ملی', 'text' => 'همان منطق تطبیق مالکیت، این بار روی شماره کارت — برای جریان‌هایی که کاربر کارت را وارد می‌کند، نه شبا را.', 'url' => '/api-validate-card/' ),
		array( 'icon' => 'convert',   'title' => 'وب‌سرویس تبدیل شماره کارت به شبا', 'text' => 'کاربر شماره کارتش را حفظ است، شبا را نه. شماره ۱۶ رقمی کارت را بفرستید و شبای متصل به همان حساب را بگیرید، سپس مالکیتش را با همین سرویس تایید کنید.', 'url' => '/api-inquiry-card/' ),
		array( 'icon' => 'sim',       'title' => 'وب‌سرویس شاهکار', 'text' => 'تطبیق شماره موبایل با کد ملی؛ در کنار تطبیق شبا، حلقه سوم هویت کاربر را می‌بندد و امکان جعل در ثبت‌نام را به حداقل می‌رساند.', 'url' => '/shahkar/' ),
	);
}
function uid_render_section_ivxsell() {
	$tag   = uid_section_tag( 'ivxsell', 'h2' );
	$items = uid_section_val( 'ivxsell', 'items', uid_default_iv_xsell() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="xsell" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivxsell', 'fold_title', __( 'سرویس‌های مکمل و مرتبط', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivxsell', 'fold_teaser', __( 'استعلام شبا، تطبیق کارت با کد ملی، تبدیل کارت به شبا و شاهکار', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivxsell', 'eyebrow', __( 'در کنار این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php esc_html_e( 'اگر به چیزی بیش از یک ', 'uid-theme' ); ?><span class="lat">true/false</span><?php echo esc_html( uid_section_val( 'ivxsell', 'heading', __( ' نیاز دارید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivxsell', 'text', __( 'خروجی این متد یک تصمیم است. اگر لازم است داده دارنده حساب را ببینید، از شماره کارت شروع کنید، یا مالکیت شماره موبایل را هم بررسی کنید، سرویس‌های زیر مکمل همین فراخوانی هستند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="xsell rv">
	      <?php foreach ( $items as $it ) : ?>
	      <a class="xtile" href="<?php echo esc_url( $it['url'] ?? '#' ); ?>">
	        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_xsell_icon_svg( $it['icon'] ?? 'card' ), array( 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'path' => array( 'd' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="go"><?php esc_html_e( 'مشاهده سرویس', 'uid-theme' ); ?> <?php echo uid_iv_arrow_icon(); ?></span>
	      </a>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۹) سوالات متداول
 * ===================================================================== */
function uid_default_iv_faq() {
	return array(
		array( 'question' => 'برای ارسال استعلام تطبیق شبا و کد ملی چه پارامترهایی لازم است؟', 'answer' => 'ارسال شماره شبای ۲۴ رقمی (همراه با یا بدون پیشوند IR)، کد ملی ۱۰ رقمی معتبر و تاریخ تولد دارنده حساب الزامی است.' ),
		array( 'question' => 'نتیجه وب‌سرویس شامل چه مواردی است؟', 'answer' => 'نتیجه مشخص می‌کند که آیا شماره شبا متعلق به صاحب کد ملی است یا خیر (isMatched برابر true یا false). همچنین نام بانک صادرکننده و شناسه پیگیری یکتا در پاسخ قرار دارد.' ),
		array( 'question' => 'آیا این سرویس اطلاعات محرمانه یا نام کامل دارنده حساب را نمایش می‌دهد؟', 'answer' => 'خیر؛ طبق الزامات محرمانگی داده‌ها، این وب‌سرویس صرفاً وضعیت تطابق یا عدم تطابق را بازمی‌گرداند و اطلاعات حساب را افشا نمی‌کند.' ),
		array( 'question' => 'تفاوت وب‌سرویس تطبیق شبا با وب‌سرویس استعلام شبا چیست؟', 'answer' => 'وب‌سرویس استعلام شبا تنها نام صاحب حساب و فعال بودن شماره شبا را استخراج می‌کند؛ اما وب‌سرویس تطبیق شبا به‌صورت سیستمی بررسی می‌کند که آیا این حساب متعلق به یک کد ملی مشخص هست یا خیر.' ),
		array( 'question' => 'در صورت مسدود بودن حساب یا نامعتبر بودن شماره شبا چه پاسخی برمی‌گردد؟', 'answer' => 'سیستم خطای ساختار شبا یا پیام عدم فعال بودن حساب را در قالب کدهای خطای استاندارد بازمی‌گرداند تا برنامه شما بتواند پیام مناسب را به کاربر نشان دهد.' ),
		array( 'question' => 'پیاده‌سازی و اتصال این وب‌سرویس چقدر زمان می‌برد؟', 'answer' => 'با توجه به استاندارد بودن REST API و ارائه مستندات فنی دقیق به همراه محیط آزمایشی (سندباکس)، ادغام کامل این سرویس معمولاً در کمتر از یک روز کاری توسط تیم فنی انجام می‌شود.' ),
		array( 'question' => 'مدل محاسبه هزینه و تعرفه به چه صورت است؟', 'answer' => 'محاسبه هزینه فقط بر اساس استعلام‌های موفق و معتبر انجام می‌شود. پلن استارتاپ تا ۵٬۰۰۰ استعلام ماهانه، پلن رشد برای ۵٬۰۰۰ تا ۵۰٬۰۰۰ استعلام با تخفیف پله‌ای، و پلن سازمانی برای بالای ۵۰٬۰۰۰ استعلام با تعرفه اختصاصی و پشتیبانی ۲۴ ساعته اختصاصی ارائه می‌شود. برای دریافت نرخ دقیق کافی است با شماره 02166123290 تماس بگیرید.' ),
	);
}
function uid_render_section_ivfaq() {
	$tag   = uid_section_tag( 'ivfaq', 'h2' );
	$items = uid_section_val( 'ivfaq', 'items', uid_default_iv_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ivfaq', 'fold_title', __( 'سوالات متداول', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ivfaq', 'fold_teaser', __( 'پارامترها، محرمانگی، تفاوت با استعلام شبا و زمان اتصال', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="iv-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ivfaq', 'eyebrow', __( 'پیش از تماس، این‌ها را بخوانید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivfaq', 'heading', __( 'سوالات متداول وب‌سرویس تطبیق شبا و کد ملی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv" style="max-width:900px;margin-inline:auto">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i">
	        <button class="faq-q" aria-expanded="false"><span class="fq-tx"><?php echo esc_html( $it['question'] ?? '' ); ?></span><span class="pm"></span></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * مشخصات عملیاتی (پنل تیره، هرگز تا نمی‌خورد)
 * ===================================================================== */
function uid_default_iv_adv() {
	return array(
		array( 'icon' => 'uptime', 'value' => '۹۹.۹٪',            'label' => 'نرخ آپ‌تایم و پایداری سرویس (SLA) با مانیتورینگ ۲۴ ساعته', 'percent' => '99', 'feat' => '1', 'wide' => '', 'numeric' => '1' ),
		array( 'icon' => 'fast',   'value' => 'تطابق در لحظه',      'label' => 'میانگین زمان پاسخ‌دهی، مناسب جریان‌های پرتراکنش و ثبت‌نام برخط', 'percent' => '94', 'feat' => '', 'wide' => '', 'numeric' => '' ),
		array( 'icon' => 'bank',   'value' => 'شتاب و شاپرک',       'label' => 'اتصال مستقیم به تمامی بانک‌های عضو شبکه بانکی کشور', 'percent' => '100', 'feat' => '', 'wide' => '', 'numeric' => '' ),
		array( 'icon' => 'legal',  'value' => 'فتا و AML',          'label' => 'انطباق قانونی با الزامات پلیس فتا و دستورالعمل‌های مبارزه با پولشویی', 'percent' => '97', 'feat' => '', 'wide' => '1', 'numeric' => '' ),
		array( 'icon' => 'doc',    'value' => 'کمتر از یک روز کاری', 'label' => 'زمان معمول ادغام کامل، به‌دلیل استاندارد بودن REST API، مستندات دقیق و محیط سندباکس', 'percent' => '92', 'feat' => '', 'wide' => '1', 'numeric' => '' ),
	);
}
function uid_render_section_ivadv() {
	$tag   = uid_section_tag( 'ivadv', 'h2' );
	$items = uid_section_val( 'ivadv', 'items', uid_default_iv_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="dark sec" id="adv">
	  <div class="iv-wrap">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ivadv', 'eyebrow', __( 'مشخصات عملیاتی وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivadv', 'heading', __( 'پنج نکته‌ای که در ارزیابی فنی از شما می‌پرسند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivadv', 'text', __( 'هر مورد زیر مستقیماً از شاخص‌های سرویس تطابقی اطلاعات مالی یوآیدی آمده است — نه یک ادعای تبلیغاتی.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="opanel rv">
	      <?php foreach ( $items as $it ) :
	        $classes = 'ostat';
	        if ( ! empty( $it['feat'] ) ) $classes .= ' feat';
	        if ( ! empty( $it['wide'] ) ) $classes .= ' w2';
	        $pct    = absint( $it['percent'] ?? 90 );
	        $is_num = ! empty( $it['numeric'] );
	        $value  = (string) ( $it['value'] ?? '' );
	      ?>
	      <div class="<?php echo esc_attr( $classes ); ?>">
	        <div class="oic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_iv_adv_icon_svg( $it['icon'] ?? 'uptime' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	        <?php if ( $is_num && '٪' === mb_substr( $value, -1 ) ) : ?>
	        <span class="ov"><?php echo esc_html( mb_substr( $value, 0, -1 ) ); ?><span style="font-size:.5em">٪</span></span>
	        <?php else : ?>
	        <span class="ov<?php echo $is_num ? '' : ' txt'; ?>"><?php echo esc_html( $value ); ?></span>
	        <?php endif; ?>
	        <span class="ol"><?php echo esc_html( $it['label'] ?? '' ); ?></span>
	        <span class="ometer" style="--p:<?php echo esc_attr( $pct ); ?>%"><i></i></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="opanel-foot rv">
	      <span><?php echo esc_html( uid_section_val( 'ivadv', 'foot_text', __( 'می‌خواهید این سرویس را روی داده واقعی خودتان تست کنید؟', 'uid-theme' ) ) ); ?></span>
	      <button class="iv-btn btn-white iv-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'ivadv', 'btn1_text', __( 'درخواست دسترسی سندباکس', 'uid-theme' ) ) ); ?></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * محاسبه‌گر هزینه ماهانه — منطق محاسبه در JS هاردکد است
 * ===================================================================== */
function uid_render_section_ivprice() {
	$tag = uid_section_tag( 'ivprice', 'h2' );
	?>
	<section class="sec" id="price">
	  <div class="iv-wrap">
	    <div class="sec-head mid rv">
	      <span class="iv-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ivprice', 'eyebrow', __( 'مدل محاسبه هزینه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ivprice', 'heading', __( 'محاسبه هزینه فقط بر اساس استعلام‌های موفق و معتبر', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ivprice', 'text', __( 'تعداد استعلام تخمینی ماهانه خود را جابه‌جا کنید تا پلن متناسب و پله تخفیف آن را ببینید. سه پلن وجود دارد: استارتاپ، رشد و سازمانی.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="calc rv">
	      <div class="calc-in">
	        <span class="lb"><?php esc_html_e( 'تعداد استعلام موفق تطبیق در ماه', 'uid-theme' ); ?></span>
	        <div class="calc-num"><span class="v" id="cl_qty">۲۰٬۰۰۰</span><span class="u"><?php esc_html_e( 'استعلام', 'uid-theme' ); ?></span></div>
	        <div class="calc-slider">
	          <input type="range" id="cl_range" min="0" max="1000" value="482" aria-label="<?php esc_attr_e( 'تعداد استعلام ماهانه', 'uid-theme' ); ?>">
	          <div class="calc-ends"><span>۱٬۰۰۰</span><span>+۵۰۰٬۰۰۰</span></div>
	        </div>
	        <div class="calc-presets" id="cl_presets">
	          <button class="calc-preset" type="button" data-qty="5000">۵٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="20000">۲۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="50000">۵۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="100000">۱۰۰٬۰۰۰</button>
	        </div>
	        <div class="calc-note"><?php echo uid_iv_info_icon(); ?>
	          <span><?php esc_html_e( 'ارقام این محاسبه‌گر نمایشی و صرفاً برای درک ساختار تعرفه پلکانی است. نرخ نهایی پس از بررسی حجم و سناریوی کسب‌وکار شما توسط کارشناس یوآیدی اعلام می‌شود.', 'uid-theme' ); ?></span></div>
	      </div>

	      <div class="calc-out">
	        <div class="calc-plan"><span class="tag" id="cl_plan"><?php esc_html_e( 'پلن رشد', 'uid-theme' ); ?></span><span class="tag off" id="cl_off"><?php esc_html_e( '۱۴٪ تخفیف پلکانی', 'uid-theme' ); ?></span></div>
	        <div class="calc-rows">
	          <div class="calc-row"><span><?php esc_html_e( 'استعلام موفق ماهانه', 'uid-theme' ); ?></span><b id="cl_n">۲۰٬۰۰۰</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'نرخ هر استعلام در این پله', 'uid-theme' ); ?></span><b id="cl_unit">—</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'هزینه بدون تخفیف پلکانی', 'uid-theme' ); ?></span><b id="cl_gross">—</b></div>
	          <div class="calc-row total"><span><?php esc_html_e( 'برآورد هزینه ماهانه', 'uid-theme' ); ?></span><b id="cl_total">—</b></div>
	        </div>
	        <div class="calc-free"><?php echo uid_iv_check_icon(); ?>
	          <span id="cl_hint"><?php esc_html_e( 'استعلام‌های ناموفق و شباهای نامعتبر در این محاسبه لحاظ نمی‌شوند.', 'uid-theme' ); ?></span></div>
	        <div class="calc-cta">
	          <button class="iv-btn btn-cta btn-block" data-open-modal><?php echo uid_iv_submit_icon(); ?><?php esc_html_e( 'دریافت تعرفه دقیق این حجم', 'uid-theme' ); ?></button>
	          <p class="calc-dis"><?php esc_html_e( 'فرم را پر کنید تا کارشناس یوآیدی نرخ واقعی این پله را همراه با شرایط قرارداد برای شما ارسال کند.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بند تماس میانی
 * ===================================================================== */
function uid_render_section_ivlastcall() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="iv-wrap">
	    <div class="callband rv">
	      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'ivlastcall', 'heading', __( 'هر روزی که این سرویس فعال نیست، روی حساب‌های تاییدنشده واریز می‌کنید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'ivlastcall', 'text', __( 'پولی که امروز به حساب اشتباه رفت، فردا با یک پرونده برمی‌گردد. یک تماس کوتاه کافی است تا کارشناس یوآیدی جریان واریز شما را بررسی و دسترسی سندباکس را فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="iv-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_iv_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="iv-btn btn-ghost-d" data-open-modal><?php echo esc_html( uid_section_val( 'ivlastcall', 'btn2_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_ivlead() {
	$tag         = uid_section_tag( 'ivlead', 'h2' );
	$trust_raw   = uid_section_val( 'ivlead', 'trust', "مشاوره رایگان، بدون تعهد\nکلید آزمایشی و سندباکس پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nپشتیبانی اختصاصی سازمانی" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'ivlead', 'biztype_options', "صرافی رمزارز و پلتفرم معاملاتی\nمارکت‌پلیس و اقتصاد مشارکتی\nلندتک و تسهیلات اقساطی\nبیمه و پرداخت خسارت\nحقوق و دستمزد سازمانی\nفروشگاه اینترنتی و مرجوعی کالا\nسایر کسب‌وکارها" );
	$biztypes    = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="iv-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="iv-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ivlead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'ivlead', 'heading', __( 'فرم درخواست فعال‌سازی وب‌سرویس تطبیق شبا با کد ملی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'ivlead', 'text', __( 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس تطبیق شماره شبا با اطلاعات هویتی، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_iv_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'ivlead', 'form_title', __( 'درخواست وب‌سرویس تطبیق شبا و کد ملی', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'ivlead', 'form_hint', __( 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="iban-validate-api-lp">
	            <input type="hidden" name="volume" id="leadVolume" value="">
	            <div class="frow">
	              <div class="fld"><input name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	                <span class="err"><?php esc_html_e( 'نام را وارد کنید.', 'uid-theme' ); ?></span></div>
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric" placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	            </div>
	            <div class="fld"><input name="business" type="text" placeholder="<?php esc_attr_e( 'نام کسب‌وکار', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'نام کسب‌وکار را وارد کنید.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><select name="biztype" data-req data-route>
	                <option value=""><?php esc_html_e( 'نوع کسب‌وکار…', 'uid-theme' ); ?></option>
	                <?php foreach ( $biztypes as $b ) : ?>
	                <option value="<?php echo esc_attr( sanitize_title( $b ) ); ?>"><?php echo esc_html( $b ); ?></option>
	                <?php endforeach; ?>
	                <option value="ind"><?php esc_html_e( 'کاربر شخصی هستم (کسب‌وکار نیستم)', 'uid-theme' ); ?></option>
	              </select><span class="err"><?php esc_html_e( 'نوع کسب‌وکار را انتخاب کنید.', 'uid-theme' ); ?></span></div>
	            <div class="route-alert" id="routeAlert"><?php echo uid_iv_info_icon(); ?>
	              <span><?php esc_html_e( 'این وب‌سرویس فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی می‌خواهید شماره شبای خودتان را ببینید، آن را در اینترنت‌بانک یا اپلیکیشن بانک خودتان پیدا کنید؛ برای خدمات فردی ', 'uid-theme' ); ?><a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'در دسترس شماست.', 'uid-theme' ); ?></span></div>
	            <button class="iv-btn btn-cta btn-block" type="button" data-submit><?php echo uid_iv_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ivlead', 'submit_text', __( 'ارسال درخواست و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_iv_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'ivlead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_iv_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'ivlead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'ivlead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * برگه واقعی خودکارساخته + اسلاگ قابل‌ویرایش
 * ===================================================================== */
function uid_get_iv_page_id() {
	$page_id = (int) get_option( 'uid_iv_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_IBAN_VALIDATE_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_iv_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_iv_page() {
	if ( uid_get_iv_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'تطبیق شماره شبا با کد ملی', 'uid-theme' ),
		'post_name'   => 'api-iban-nationalid',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_IBAN_VALIDATE_TEMPLATE );
	update_option( 'uid_iv_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_iv_page' );

function uid_ensure_iv_page_once() {
	if ( get_option( 'uid_iv_page_bootstrapped' ) ) return;
	uid_ensure_iv_page();
	update_option( 'uid_iv_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_iv_page_once' );

function uid_register_iv_slug_setting() {
	register_setting( 'uid_iv_group', 'uid_iv_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_iv_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_iv_page_slug_section', '', '__return_false', 'uid_iv_layout' );
	add_settings_field( 'uid_iv_page_slug', __( 'آدرس (اسلاگ) صفحه تطبیق شبا با کد ملی', 'uid-theme' ), 'uid_field_iv_page_slug', 'uid_iv_layout', 'uid_iv_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_iv_slug_setting' );

function uid_field_iv_page_slug( $args ) {
	$page_id = uid_get_iv_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_iv_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_iv_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه تطبیق شبا با کد ملی هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_iv_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_iv_page_id();

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
 * ثبت تنظیمات پیشخوان برای همه‌ی سکشن‌های این صفحه
 * ===================================================================== */
function uid_register_iv_settings() {
	register_setting( 'uid_iv_group', 'uid_iv_layout', array(
		'sanitize_callback' => 'uid_sanitize_iv_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_iv_layout_main', '', '__return_false', 'uid_iv_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_iv_layout', 'uid_iv_layout_main', array(
		'option_name' => 'uid_iv_layout', 'registry_fn' => 'uid_iv_sections_registry', 'layout_fn' => 'uid_get_iv_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivhero', array( 'sanitize_callback' => 'uid_sanitize_section_ivhero', 'default' => array() ) );
	add_settings_section( 'uid_section_ivhero_main', '', '__return_false', 'uid_section_ivhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان (تگ span مجاز است)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس تطبیق شبا و کد ملی · <span class="lat">IBAN Validate API</span>' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'heading', 'default' => 'شماره شبا درست است.<mark>ولی مال خودش است؟</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'text', 'default' => 'یک شبای معتبر و فعال هیچ چیزی درباره صاحبش نمی‌گوید. تا لحظه‌ای که پول واریز شود، شما نمی‌دانید حساب مقصد متعلق به همان کاربری است که احراز هویتش کرده‌اید یا به نام شخص دیگری. این وب‌سرویس با گرفتن شبا، کد ملی و تاریخ تولد، مالکیت حساب را پیش از واریز تایید یا رد می‌کند.', 'rows' => 4 ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'btn1_text', 'default' => 'فرم درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'tags', 'default' => implode( "\n", uid_default_iv_hero_tags() ), 'rows' => 5 ) );
	add_settings_field( 'vtests', __( 'دکمه‌های تست نمونه (برچسب + مقادیر پرشونده)', 'uid-theme' ), 'uid_field_iv_vtests', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'group' => 'uid_section_ivhero', 'key' => 'vtests', 'default' => uid_default_iv_vtests() ) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_ivhero', 'uid_section_ivhero_main', array( 'text' => __( 'کنسول زنده تطبیق مالکیت فقط در برچسب دکمه‌ها و مقادیر نمونه بالا قابل‌ویرایش است؛ منطق محاسبه mod-97 و ساخت پاسخ در assets/js/iban-validate-page.js می‌ماند.', 'uid-theme' ) ) );

	/* ---------------- باند اعتماد کوتاه ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivtrust', array( 'sanitize_callback' => 'uid_sanitize_section_ivtrust', 'default' => array() ) );
	add_settings_section( 'uid_section_ivtrust_main', '', '__return_false', 'uid_section_ivtrust' );
	add_settings_field( 'items', __( 'آمار (خانه‌های باند)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivtrust', 'uid_section_ivtrust_main', array(
		'group' => 'uid_section_ivtrust', 'key' => 'items', 'default' => uid_default_iv_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'numeric', 'type' => 'select', 'label' => __( 'استایل عددی بزرگ', 'uid-theme' ), 'options' => array( '' => __( 'خیر (متن)', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );

	/* ---------------- کپسول ۳۰ ثانیه‌ای ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivtldr', array( 'sanitize_callback' => 'uid_sanitize_section_ivtldr', 'default' => array() ) );
	add_settings_section( 'uid_section_ivtldr_main', '', '__return_false', 'uid_section_ivtldr' );
	add_settings_field( 'heading', __( 'عنوان کپسول', 'uid-theme' ), 'uid_field_text', 'uid_section_ivtldr', 'uid_section_ivtldr_main', array( 'group' => 'uid_section_ivtldr', 'key' => 'heading', 'default' => 'اگر عجله دارید، همین چهار خط کافی است' ) );
	add_settings_field( 'badge_text', __( 'برچسب کوچک (مثل «۳۰ ثانیه»)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivtldr', 'uid_section_ivtldr_main', array( 'group' => 'uid_section_ivtldr', 'key' => 'badge_text', 'default' => '۳۰ ثانیه' ) );
	add_settings_field( 'items', __( 'خطوط خلاصه (هر خط یک مورد؛ تگ‌های b/span مجازند)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivtldr', 'uid_section_ivtldr_main', array( 'group' => 'uid_section_ivtldr', 'key' => 'items', 'default' => implode( "\n", uid_default_iv_tldr_items() ), 'rows' => 5 ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivtldr', 'uid_section_ivtldr_main', array( 'group' => 'uid_section_ivtldr', 'key' => 'btn2_text', 'default' => 'فرم درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'more_text', __( 'متن پرش نرم به محاسبه‌گر', 'uid-theme' ), 'uid_field_text', 'uid_section_ivtldr', 'uid_section_ivtldr_main', array( 'group' => 'uid_section_ivtldr', 'key' => 'more_text', 'default' => 'و اگر می‌خواهید عدد ریسک خودتان را ببینید' ) );

	/* ---------------- محاسبه‌گر ریسک ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivloss', array( 'sanitize_callback' => 'uid_sanitize_section_ivloss', 'default' => array() ) );
	add_settings_section( 'uid_section_ivloss_main', '', '__return_false', 'uid_section_ivloss' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'eyebrow', 'default' => 'قبل از اینکه ادامه بدهید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'heading', 'default' => 'هر واریز به حساب تاییدنشده، یک پرونده باز است' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'text', 'default' => 'حساب اجاره‌ای، کد ملی شخص دیگر، یک رقم اشتباه در شبا — نتیجه همه‌شان یکی است: پول رفته، حساب مسدود شده و پرونده روی میز شماست. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر ریسک روی حساب‌های تاییدنشده نشسته است.' ) );
	add_settings_field( 'proof', __( 'کارت‌های اثبات (دکِ کنار محاسبه‌گر)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivloss', 'uid_section_ivloss_main', array(
		'group' => 'uid_section_ivloss', 'key' => 'proof', 'default' => uid_default_iv_proof(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'shield' => __( 'امنیت', 'uid-theme' ), 'sync' => __( 'تطبیق', 'uid-theme' ), 'doc' => __( 'سند', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'cta_heading', 'default' => 'ترجیح می‌دهید همین حالا صحبت کنید؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'group' => 'uid_section_ivloss', 'key' => 'cta_text', 'default' => 'یک تماس کوتاه کافی است تا جریان واریز و تسویه شما بررسی و مسیر اتصال مشخص شود.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ivloss', 'uid_section_ivloss_main', array( 'text' => __( 'منطق محاسبه‌گر ریسک (اسلایدرها و اعداد خروجی) در assets/js/iban-validate-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- فصل ۱: این سرویس چیست ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivwhat', array( 'sanitize_callback' => 'uid_sanitize_section_ivwhat', 'default' => array() ) );
	add_settings_section( 'uid_section_ivwhat_main', '', '__return_false', 'uid_section_ivwhat' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'eyebrow', 'default' => 'معرفی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'heading', 'default' => 'وب‌سرویس تطبیق کد ملی و شماره شبا چیست؟' ) );
	add_settings_field( 'text', __( 'توضیح (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'text', 'default' => 'این وب‌سرویس یک راهکار اعتبارسنجی مالی آنلاین است که مشخص می‌کند آیا شماره شبای ۲۴ رقمی ارائه‌شده، دقیقاً به همان کد ملی و تاریخ تولد ثبت‌شده تعلق دارد یا خیر. سیستم نتیجه را به‌صورت true یا false در پاسخ برمی‌گرداند.' ) );
	add_settings_field( 'probs', __( 'مسائلی که حل می‌کند', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array(
		'group' => 'uid_section_ivwhat', 'key' => 'probs', 'default' => uid_default_iv_probs(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'personx' => __( 'اجاره حساب', 'uid-theme' ), 'warn' => __( 'هشدار', 'uid-theme' ), 'shield' => __( 'امنیت/انطباق', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'privacy_heading', __( 'عنوان کادر محرمانگی', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'privacy_heading', 'default' => 'محرمانگی داده، بخشی از طراحی سرویس است' ) );
	add_settings_field( 'privacy_text', __( 'توضیح کادر محرمانگی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'privacy_text', 'default' => 'این وب‌سرویس صرفاً وضعیت تطابق یا عدم تطابق را بازمی‌گرداند و اطلاعات حساب را افشا نمی‌کند. یعنی می‌توانید مالکیت را تایید کنید بدون آنکه نام، موجودی یا هیچ داده حساس دیگری از دارنده حساب وارد سامانه شما شود.' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'fold_title', 'default' => 'سه ورودی می‌فرستید، یک پاسخ قطعی می‌گیرید' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwhat', 'uid_section_ivwhat_main', array( 'group' => 'uid_section_ivwhat', 'key' => 'fold_teaser', 'default' => 'این وب‌سرویس دقیقاً چه کاری انجام می‌دهد' ) );

	/* ---------------- فصل ۲: تطبیق شبا در برابر استعلام شبا ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivvs', array( 'sanitize_callback' => 'uid_sanitize_section_ivvs', 'default' => array() ) );
	add_settings_section( 'uid_section_ivvs_main', '', '__return_false', 'uid_section_ivvs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'eyebrow', 'default' => 'پرتکرارترین سوال فنی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'heading', 'default' => '«استعلام شبا» و «تطبیق شبا» یکی نیستند' ) );
	add_settings_field( 'text', __( 'توضیح (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'text', 'default' => 'هر دو سرویس با شماره شبا کار می‌کنند، ولی یکی اطلاعات را می‌آورد و دیگری ادعای کاربر را تایید یا رد می‌کند. انتخاب اشتباه بین این دو، یا شما را دچار افشای غیرلازم داده می‌کند یا بی‌دفاع در برابر اجاره حساب.' ) );
	add_settings_field( 'bad_tag', __( 'برچسب کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'bad_tag', 'default' => 'وب‌سرویس استعلام شبا' ) );
	add_settings_field( 'bad_title', __( 'عنوان کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'bad_title', 'default' => 'سوال: این حساب مال کیست؟' ) );
	add_settings_field( 'bad_text', __( 'توضیح کارت بد', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'bad_text', 'default' => 'نام صاحب حساب و فعال بودن شماره شبا را استخراج می‌کند و به سامانه شما تحویل می‌دهد.' ) );
	add_settings_field( 'bad_items', __( 'موارد کارت بد (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'bad_items', 'default' => implode( "\n", uid_default_iv_vs_bad_items() ), 'rows' => 4 ) );
	add_settings_field( 'good_tag', __( 'برچسب کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'good_tag', 'default' => 'وب‌سرویس تطبیق شبا و کد ملی' ) );
	add_settings_field( 'good_title', __( 'عنوان کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'good_title', 'default' => 'سوال: این حساب مال همین شخص است؟' ) );
	add_settings_field( 'good_text', __( 'توضیح کارت خوب', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'good_text', 'default' => 'به‌صورت سیستمی بررسی می‌کند که آیا این حساب متعلق به یک کد ملی مشخص هست یا خیر.' ) );
	add_settings_field( 'good_items', __( 'موارد کارت خوب (هر خط یک مورد؛ تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'good_items', 'default' => implode( "\n", uid_default_iv_vs_good_items() ), 'rows' => 4 ) );
	add_settings_field( 'note_heading', __( 'عنوان کادر هشدار', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'note_heading', 'default' => 'در جریان‌های حساس، «تصمیم» لازم است نه «داده»' ) );
	add_settings_field( 'note_text', __( 'توضیح کادر هشدار', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'note_text', 'default' => 'وقتی پول در حال خارج شدن از سامانه شماست، تیم فنی نباید درگیر مقایسه رشته‌ای نام‌ها شود. یک فیلد بولین که پیش از فراخوانی درگاه بررسی می‌شود، هم سریع‌تر است، هم قابل ممیزی، هم برای تیم انطباق قابل دفاع.' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'fold_title', 'default' => 'تفاوت تطبیق شبا با استعلام شبا' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivvs', 'uid_section_ivvs_main', array( 'group' => 'uid_section_ivvs', 'key' => 'fold_teaser', 'default' => 'دو سرویس شبیه به هم که دو سوال کاملاً متفاوت را جواب می‌دهند' ) );

	/* ---------------- فصل ۳: نحوه کار سرویس (استپر داده‌محور) ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivhow', array( 'sanitize_callback' => 'uid_sanitize_section_ivhow', 'default' => array() ) );
	add_settings_section( 'uid_section_ivhow_main', '', '__return_false', 'uid_section_ivhow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'eyebrow', 'default' => 'پشت صحنه یک فراخوانی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'heading', 'default' => 'از سه ورودی تا یک تصمیم، در چهار قدم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'text', 'default' => 'روی هر مرحله بزنید تا ببینید در آن لحظه دقیقاً چه اتفاقی می‌افتد و چه چیزی رد و بدل می‌شود.' ) );
	add_settings_field( 'steps', __( 'مراحل استپر', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivhow', 'uid_section_ivhow_main', array(
		'group' => 'uid_section_ivhow', 'key' => 'steps', 'default' => uid_default_iv_how(), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'cap', 'type' => 'text', 'label' => __( 'برچسب کوتاه (روی نوار مراحل)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'eyebrow', 'type' => 'text', 'label' => __( 'برچسب کوچک بالای عنوان مرحله', 'uid-theme' ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان مرحله', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح مرحله', 'uid-theme' ) ),
			array( 'key' => 'chips', 'type' => 'textarea', 'label' => __( 'برچسب‌های کوچک زیر توضیح (هر خط یک مورد)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین استپر', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'foot_text', 'default' => 'کل این چرخه در یک فراخوانی انجام می‌شود؛ تیم فنی شما فقط یک درخواست می‌نویسد.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'btn1_text', 'default' => 'دریافت کلید و مستندات' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'fold_title', 'default' => 'مراحل عملکرد وب‌سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivhow', 'uid_section_ivhow_main', array( 'group' => 'uid_section_ivhow', 'key' => 'fold_teaser', 'default' => 'از ارسال داده تا دریافت پاسخ ساختاریافته در چهار قدم' ) );

	/* ---------------- فصل ۴: کاربردها ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivwho', array( 'sanitize_callback' => 'uid_sanitize_section_ivwho', 'default' => array() ) );
	add_settings_section( 'uid_section_ivwho_main', '', '__return_false', 'uid_section_ivwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'eyebrow', 'default' => 'کجا به کار می‌آید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'heading', 'default' => 'هر جا که پول از سامانه شما بیرون می‌رود' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'text', 'default' => 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.' ) );
	add_settings_field( 'items', __( 'کارت‌های صنعت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivwho', 'uid_section_ivwho_main', array(
		'group' => 'uid_section_ivwho', 'key' => 'items', 'default' => uid_default_iv_who(), 'add_label' => __( 'افزودن صنعت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'crypto' => __( 'صرافی/معاملات', 'uid-theme' ), 'market' => __( 'مارکت‌پلیس', 'uid-theme' ), 'lend' => __( 'لندتک', 'uid-theme' ), 'insure' => __( 'بیمه', 'uid-theme' ), 'payroll' => __( 'حقوق و دستمزد', 'uid-theme' ), 'shop' => __( 'فروشگاه اینترنتی', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب کوتاه (روی تب)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'win', 'type' => 'text', 'label' => __( 'نتیجه/دستاورد', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'fold_title', 'default' => 'کاربرد در کسب‌وکارها' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivwho', 'uid_section_ivwho_main', array( 'group' => 'uid_section_ivwho', 'key' => 'fold_teaser', 'default' => 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید' ) );

	/* ---------------- فصل ۵: مزایای رقابتی + پوشش بانکی ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivgain', array( 'sanitize_callback' => 'uid_sanitize_section_ivgain', 'default' => array() ) );
	add_settings_section( 'uid_section_ivgain_main', '', '__return_false', 'uid_section_ivgain' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'eyebrow', 'default' => 'چرا یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'heading', 'default' => 'مزایای رقابتی وب‌سرویس تطبیق شبا' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'text', 'default' => 'این سرویس روی مسیر پول می‌نشیند؛ بنابراین سرعت، دقت و پشتیبانی آن مستقیماً روی ریسک عملیاتی شما اثر می‌گذارد.' ) );
	add_settings_field( 'items', __( 'کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivgain', 'uid_section_ivgain_main', array(
		'group' => 'uid_section_ivgain', 'key' => 'items', 'default' => uid_default_iv_gain(), 'add_label' => __( 'افزودن مزیت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'fast' => __( 'سرعت', 'uid-theme' ), 'bank' => __( 'بانک', 'uid-theme' ), 'bday' => __( 'تاریخ تولد', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ), 'uptime' => __( 'پایداری', 'uid-theme' ), 'costfair' => __( 'هزینه منصفانه', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'برچسب کوتاه پایین کارت', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'banks_heading', __( 'عنوان بخش پوشش بانکی', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'banks_heading', 'default' => 'پوشش شبکه بانکی سرویس' ) );
	add_settings_field( 'banks_text', __( 'توضیح بخش پوشش بانکی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'banks_text', 'default' => 'اتصال مستقیم به تمامی بانک‌های عضو شبکه شتاب و شاپرک؛ شماره شبای صادرشده توسط بانک‌ها و موسسات مالی مجاز کشور پشتیبانی می‌شود.' ) );
	add_settings_field( 'banks_items', __( 'فهرست بانک‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'banks_items', 'default' => implode( "\n", uid_default_iv_banks() ), 'rows' => 10 ) );
	add_settings_field( 'banks_more', __( 'برچسب پایانی', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'banks_more', 'default' => 'و سایر بانک‌ها و موسسات مجاز عضو شتاب' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'fold_title', 'default' => 'مزایای رقابتی سرویس یوآیدی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivgain', 'uid_section_ivgain_main', array( 'group' => 'uid_section_ivgain', 'key' => 'fold_teaser', 'default' => 'شش دلیلی که تیم فنی و تیم انطباق هر دو قبول می‌کنند' ) );

	/* ---------------- فصل ۶: تیم متخصص ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivteam', array( 'sanitize_callback' => 'uid_sanitize_section_ivteam', 'default' => array() ) );
	add_settings_section( 'uid_section_ivteam_main', '', '__return_false', 'uid_section_ivteam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'eyebrow', 'default' => 'پشت این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان (بعد از «یک API»)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'heading', 'default' => ' نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'text', 'default' => 'این سرویس دقیقاً روی نقطه‌ای می‌نشیند که پول از سامانه شما خارج می‌شود. به همین دلیل تیم فنی یوآیدی از اولین تماس تا اولین تطبیق موفق روی محیط عملیاتی همراه شماست.' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivteam', 'uid_section_ivteam_main', array(
		'group' => 'uid_section_ivteam', 'key' => 'rows', 'default' => uid_default_iv_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'connect' => __( 'اتصال', 'uid-theme' ), 'monitor' => __( 'مانیتورینگ', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به تیم فنی شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_iv_pledge_items() ), 'rows' => 4 ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'fold_title', 'default' => 'تیم متخصص و پشتیبانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivteam', 'uid_section_ivteam_main', array( 'group' => 'uid_section_ivteam', 'key' => 'fold_teaser', 'default' => 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود' ) );

	/* ---------------- فصل ۷: مستندات فنی ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivdev', array( 'sanitize_callback' => 'uid_sanitize_section_ivdev', 'default' => array() ) );
	add_settings_section( 'uid_section_ivdev_main', '', '__return_false', 'uid_section_ivdev' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'eyebrow', 'default' => 'برای تیم توسعه شما' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'heading', 'default' => 'یک فراخوانی، یک تصمیم، تمام' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'btn1_text', 'default' => 'دریافت کلید وب‌سرویس' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'text' => __( 'توضیح معرفی، نمونه‌کدهای درخواست/پاسخ و جدول پارامترها، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/iban-validate-page.php مراجعه کنید).', 'uid-theme' ) ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'fold_title', 'default' => 'بخش فنی و نمونه کدها' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivdev', 'uid_section_ivdev_main', array( 'group' => 'uid_section_ivdev', 'key' => 'fold_teaser', 'default' => 'نمونه درخواست، نمونه پاسخ و جدول پارامترها' ) );

	/* ---------------- فصل ۸: سرویس‌های مکمل ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivxsell', array( 'sanitize_callback' => 'uid_sanitize_section_ivxsell', 'default' => array() ) );
	add_settings_section( 'uid_section_ivxsell_main', '', '__return_false', 'uid_section_ivxsell' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'eyebrow', 'default' => 'در کنار این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان (بعد از «اگر به چیزی بیش از یک true/false»)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'heading', 'default' => ' نیاز دارید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'text', 'default' => 'خروجی این متد یک تصمیم است. اگر لازم است داده دارنده حساب را ببینید، از شماره کارت شروع کنید، یا مالکیت شماره موبایل را هم بررسی کنید، سرویس‌های زیر مکمل همین فراخوانی هستند.' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array(
		'group' => 'uid_section_ivxsell', 'key' => 'items', 'default' => uid_default_iv_xsell(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'card' => __( 'شبا', 'uid-theme' ), 'cardcheck' => __( 'تطبیق کارت', 'uid-theme' ), 'convert' => __( 'تبدیل کارت به شبا', 'uid-theme' ), 'sim' => __( 'سیم‌کارت/شاهکار', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'fold_title', 'default' => 'سرویس‌های مکمل و مرتبط' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivxsell', 'uid_section_ivxsell_main', array( 'group' => 'uid_section_ivxsell', 'key' => 'fold_teaser', 'default' => 'استعلام شبا، تطبیق کارت با کد ملی، تبدیل کارت به شبا و شاهکار' ) );

	/* ---------------- فصل ۹: سوالات متداول ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivfaq', array( 'sanitize_callback' => 'uid_sanitize_section_ivfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_ivfaq_main', '', '__return_false', 'uid_section_ivfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array( 'group' => 'uid_section_ivfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array( 'group' => 'uid_section_ivfaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس، این‌ها را بخوانید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array( 'group' => 'uid_section_ivfaq', 'key' => 'heading', 'default' => 'سوالات متداول وب‌سرویس تطبیق شبا و کد ملی' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array(
		'group' => 'uid_section_ivfaq', 'key' => 'items', 'default' => uid_default_iv_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array( 'group' => 'uid_section_ivfaq', 'key' => 'fold_title', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivfaq', 'uid_section_ivfaq_main', array( 'group' => 'uid_section_ivfaq', 'key' => 'fold_teaser', 'default' => 'پارامترها، محرمانگی، تفاوت با استعلام شبا و زمان اتصال' ) );

	/* ---------------- مشخصات عملیاتی (پنل تیره) ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivadv', array( 'sanitize_callback' => 'uid_sanitize_section_ivadv', 'default' => array() ) );
	add_settings_section( 'uid_section_ivadv_main', '', '__return_false', 'uid_section_ivadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'eyebrow', 'default' => 'مشخصات عملیاتی وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'heading', 'default' => 'پنج نکته‌ای که در ارزیابی فنی از شما می‌پرسند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'text', 'default' => 'هر مورد زیر مستقیماً از شاخص‌های سرویس تطابقی اطلاعات مالی یوآیدی آمده است — نه یک ادعای تبلیغاتی.' ) );
	add_settings_field( 'items', __( 'آیتم‌های پنل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ivadv', 'uid_section_ivadv_main', array(
		'group' => 'uid_section_ivadv', 'key' => 'items', 'default' => uid_default_iv_adv(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'uptime' => __( 'پایداری', 'uid-theme' ), 'fast' => __( 'سرعت', 'uid-theme' ), 'bank' => __( 'بانک', 'uid-theme' ), 'legal' => __( 'انطباق قانونی', 'uid-theme' ), 'doc' => __( 'سند', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عنوان کوتاه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد نوار پیشرفت (عدد بین ۰ تا ۱۰۰)', 'uid-theme' ) ),
			array( 'key' => 'feat', 'type' => 'select', 'label' => __( 'کارت برجسته (بزرگ‌تر)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'wide', 'type' => 'select', 'label' => __( 'عرض دوبرابر', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'numeric', 'type' => 'select', 'label' => __( 'مقدار عددی بزرگ (نه متن)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'foot_text', 'default' => 'می‌خواهید این سرویس را روی داده واقعی خودتان تست کنید؟' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivadv', 'uid_section_ivadv_main', array( 'group' => 'uid_section_ivadv', 'key' => 'btn1_text', 'default' => 'درخواست دسترسی سندباکس' ) );

	/* ---------------- محاسبه‌گر هزینه ماهانه ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivprice', array( 'sanitize_callback' => 'uid_sanitize_section_ivprice', 'default' => array() ) );
	add_settings_section( 'uid_section_ivprice_main', '', '__return_false', 'uid_section_ivprice' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivprice', 'uid_section_ivprice_main', array( 'group' => 'uid_section_ivprice', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivprice', 'uid_section_ivprice_main', array( 'group' => 'uid_section_ivprice', 'key' => 'eyebrow', 'default' => 'مدل محاسبه هزینه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivprice', 'uid_section_ivprice_main', array( 'group' => 'uid_section_ivprice', 'key' => 'heading', 'default' => 'محاسبه هزینه فقط بر اساس استعلام‌های موفق و معتبر' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivprice', 'uid_section_ivprice_main', array( 'group' => 'uid_section_ivprice', 'key' => 'text', 'default' => 'تعداد استعلام تخمینی ماهانه خود را جابه‌جا کنید تا پلن متناسب و پله تخفیف آن را ببینید. سه پلن وجود دارد: استارتاپ، رشد و سازمانی.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ivprice', 'uid_section_ivprice_main', array( 'text' => __( 'منطق محاسبه‌گر (پله‌های تخفیف و قیمت پایه) در assets/js/iban-validate-page.js تعریف شده و برای انتشار نهایی باید با تعرفه واقعی جایگزین شود؛ از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- بند تماس میانی ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivlastcall', array( 'sanitize_callback' => 'uid_sanitize_section_ivlastcall', 'default' => array() ) );
	add_settings_section( 'uid_section_ivlastcall_main', '', '__return_false', 'uid_section_ivlastcall' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlastcall', 'uid_section_ivlastcall_main', array( 'group' => 'uid_section_ivlastcall', 'key' => 'heading', 'default' => 'هر روزی که این سرویس فعال نیست، روی حساب‌های تاییدنشده واریز می‌کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivlastcall', 'uid_section_ivlastcall_main', array( 'group' => 'uid_section_ivlastcall', 'key' => 'text', 'default' => 'پولی که امروز به حساب اشتباه رفت، فردا با یک پرونده برمی‌گردد. یک تماس کوتاه کافی است تا کارشناس یوآیدی جریان واریز شما را بررسی و دسترسی سندباکس را فعال کند.' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlastcall', 'uid_section_ivlastcall_main', array( 'group' => 'uid_section_ivlastcall', 'key' => 'btn2_text', 'default' => 'فرم درخواست سرویس' ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_iv_group', 'uid_section_ivlead', array( 'sanitize_callback' => 'uid_sanitize_section_ivlead', 'default' => array() ) );
	add_settings_section( 'uid_section_ivlead_main', '', '__return_false', 'uid_section_ivlead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'heading', 'default' => 'فرم درخواست فعال‌سازی وب‌سرویس تطبیق شبا با کد ملی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس تطبیق شماره شبا با اطلاعات هویتی، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nکلید آزمایشی و سندباکس پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nپشتیبانی اختصاصی سازمانی" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'form_title', 'default' => 'درخواست وب‌سرویس تطبیق شبا و کد ملی' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'form_hint', 'default' => 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'biztype_options', 'default' => "صرافی رمزارز و پلتفرم معاملاتی\nمارکت‌پلیس و اقتصاد مشارکتی\nلندتک و تسهیلات اقساطی\nبیمه و پرداخت خسارت\nحقوق و دستمزد سازمانی\nفروشگاه اینترنتی و مرجوعی کالا\nسایر کسب‌وکارها" ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_ivlead', 'uid_section_ivlead_main', array( 'group' => 'uid_section_ivlead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_iv_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_ivhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => wp_kses( $input['eyebrow'] ?? '', array( 'span' => array( 'class' => array() ) ) ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'vtests'    => array_map( 'sanitize_text_field', wp_parse_args( is_array( $input['vtests'] ?? null ) ? $input['vtests'] : array(), uid_default_iv_vtests() ) ),
	);
}

function uid_sanitize_section_ivtrust( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'numeric', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ivtldr( $input ) {
	return array(
		'heading'    => sanitize_text_field( $input['heading'] ?? '' ),
		'badge_text' => sanitize_text_field( $input['badge_text'] ?? '' ),
		'items'      => sanitize_textarea_field( $input['items'] ?? '' ),
		'btn2_text'  => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'more_text'  => sanitize_text_field( $input['more_text'] ?? '' ),
	);
}

function uid_sanitize_section_ivloss( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'proof'       => uid_sanitize_repeater_rows( $input['proof'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_ivwhat( $input ) {
	return array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => sanitize_text_field( $input['heading'] ?? '' ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'probs'           => uid_sanitize_repeater_rows( $input['probs'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'privacy_heading' => sanitize_text_field( $input['privacy_heading'] ?? '' ),
		'privacy_text'    => sanitize_textarea_field( $input['privacy_text'] ?? '' ),
		'fold_title'      => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'     => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivvs( $input ) {
	return array(
		'title_tag'    => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => sanitize_text_field( $input['heading'] ?? '' ),
		'text'         => sanitize_textarea_field( $input['text'] ?? '' ),
		'bad_tag'      => sanitize_text_field( $input['bad_tag'] ?? '' ),
		'bad_title'    => sanitize_text_field( $input['bad_title'] ?? '' ),
		'bad_text'     => sanitize_textarea_field( $input['bad_text'] ?? '' ),
		'bad_items'    => sanitize_textarea_field( $input['bad_items'] ?? '' ),
		'good_tag'     => sanitize_text_field( $input['good_tag'] ?? '' ),
		'good_title'   => sanitize_text_field( $input['good_title'] ?? '' ),
		'good_text'    => sanitize_textarea_field( $input['good_text'] ?? '' ),
		'good_items'   => sanitize_textarea_field( $input['good_items'] ?? '' ),
		'note_heading' => sanitize_text_field( $input['note_heading'] ?? '' ),
		'note_text'    => sanitize_textarea_field( $input['note_text'] ?? '' ),
		'fold_title'   => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'  => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivhow( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'       => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'cap', 'type' => 'text', 'required' => true ),
			array( 'key' => 'eyebrow', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'chips', 'type' => 'textarea' ),
		) ),
		'foot_text'   => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn1_text'   => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivwho( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'win', 'type' => 'text' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivgain( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'         => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'tagline', 'type' => 'text' ),
		) ),
		'banks_heading' => sanitize_text_field( $input['banks_heading'] ?? '' ),
		'banks_text'    => sanitize_textarea_field( $input['banks_text'] ?? '' ),
		'banks_items'   => sanitize_textarea_field( $input['banks_items'] ?? '' ),
		'banks_more'    => sanitize_text_field( $input['banks_more'] ?? '' ),
		'fold_title'    => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'   => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivteam( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'text'           => sanitize_textarea_field( $input['text'] ?? '' ),
		'rows'           => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'pledge_heading' => sanitize_text_field( $input['pledge_heading'] ?? '' ),
		'pledge_items'   => sanitize_textarea_field( $input['pledge_items'] ?? '' ),
		'fold_title'     => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'    => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivdev( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'btn1_text'   => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivxsell( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'url', 'type' => 'text' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivfaq( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ivadv( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'textarea' ),
			array( 'key' => 'percent', 'type' => 'text' ),
			array( 'key' => 'feat', 'type' => 'text' ),
			array( 'key' => 'wide', 'type' => 'text' ),
			array( 'key' => 'numeric', 'type' => 'text' ),
		) ),
		'foot_text' => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
	);
}

function uid_sanitize_section_ivprice( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_ivlastcall( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_ivlead( $input ) {
	return array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => sanitize_text_field( $input['heading'] ?? '' ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'           => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'      => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'       => sanitize_text_field( $input['form_hint'] ?? '' ),
		'biztype_options' => sanitize_textarea_field( $input['biztype_options'] ?? '' ),
		'submit_text'     => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'       => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title'   => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'    => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_iv_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس تطبیق شبا و کد ملی', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی همین امروز تماس بگیرد: کلید آزمایشی، دسترسی سندباکس، مستندات سرویس', 'uid-theme' ); ?> <span class="lat">validate/iban/ownership</span> <?php esc_html_e( 'و تعرفه متناسب با حجم استعلام ماهانه شما.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'صرافی رمزارز و پلتفرم معاملاتی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'مارکت‌پلیس و اقتصاد مشارکتی', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'لندتک و تسهیلات اقساطی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بیمه و پرداخت خسارت', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'حقوق و دستمزد سازمانی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'فروشگاه اینترنتی و مرجوعی کالا', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بانک، نئوبانک یا فین‌تک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سایر کسب‌وکارها', 'uid-theme' ); ?></option>
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
