<?php
/**
 * صفحه اختصاصی «استعلام کد پستی و آدرس» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «pa» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_POSTAL_ADDRESS_TEMPLATE', 'template-postal-address.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_pa_sections_registry() {
	return array(
		'pahero'     => array( 'label' => __( 'هیرو + پیش‌نمایش زنده استعلام آدرس', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'paurgency'  => array( 'label' => __( 'بند فوریت (هشدار ریسک)', 'uid-theme' ),               'icon' => 'dashicons-warning' ),
		'pamarquee'  => array( 'label' => __( 'مشتریان (نوار متحرک)', 'uid-theme' ),                 'icon' => 'dashicons-awards' ),
		'paform'     => array( 'label' => __( 'فرمی که جمع می‌شود (قبل/بعد)', 'uid-theme' ),         'icon' => 'dashicons-forms' ),
		'paout'      => array( 'label' => __( 'ورودی/خروجی API (نمونه‌کد)', 'uid-theme' ),           'icon' => 'dashicons-editor-code' ),
		'pahow'      => array( 'label' => __( 'نحوه کار سرویس + آمار کوتاه', 'uid-theme' ),          'icon' => 'dashicons-randomize' ),
		'pawho'      => array( 'label' => __( 'کاربرد بر اساس صنعت (انتخابگر)', 'uid-theme' ),       'icon' => 'dashicons-groups' ),
		'parisk'     => array( 'label' => __( 'ریسک‌ها و راه‌حل‌ها', 'uid-theme' ),                  'icon' => 'dashicons-shield' ),
		'paprice'    => array( 'label' => __( 'محاسبه‌گر هزینه ماهانه', 'uid-theme' ),               'icon' => 'dashicons-calculator' ),
		'patrust'    => array( 'label' => __( 'باند اعتماد (آمار شرکت)', 'uid-theme' ),              'icon' => 'dashicons-chart-bar' ),
		'pawhy'      => array( 'label' => __( 'چرا یوآیدی', 'uid-theme' ),                           'icon' => 'dashicons-star-empty' ),
		'pastart'    => array( 'label' => __( 'مراحل راه‌اندازی (۳ گام)', 'uid-theme' ),             'icon' => 'dashicons-controls-forward' ),
		'pateam'     => array( 'label' => __( 'تیم متخصص + تعهدنامه', 'uid-theme' ),                 'icon' => 'dashicons-businessperson' ),
		'pasecurity' => array( 'label' => __( 'بند امنیت داده‌ها', 'uid-theme' ),                    'icon' => 'dashicons-lock' ),
		'pafaq'      => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                        'icon' => 'dashicons-editor-help' ),
		'pasvc'      => array( 'label' => __( 'سرویس‌های تکمیلی (کاشی‌ها)', 'uid-theme' ),            'icon' => 'dashicons-grid-view' ),
		'palead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                 'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_pa_layout() {
	$registry = uid_pa_sections_registry();
	$saved    = get_option( 'uid_pa_layout', array() );

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

function uid_sanitize_pa_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_pa_sections_registry();
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

function uid_render_pa_sections() {
	uid_render_pa_jumpbar();
	foreach ( uid_get_pa_layout() as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
}

/**
 * نوار پرش سریع موبایل — عنصر ساختاری ثابت (نه یک سکشن محتوایی)؛ به لنگرهای
 * سکشن‌های همین صفحه اشاره می‌کند و همیشه نمایش داده می‌شود.
 */
function uid_render_pa_jumpbar() {
	?>
	<div class="jumpbar" id="jumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jump-rail" id="jumpRail">
	    <button class="jump-chip cta" type="button" data-open-modal><?php esc_html_e( 'درخواست سرویس', 'uid-theme' ); ?></button>
	    <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	    <button class="jump-chip" type="button" data-jump="#form"><?php esc_html_e( 'قبل و بعد فرم', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#out"><?php esc_html_e( 'ورودی و خروجی', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#how"><?php esc_html_e( 'نحوه کار', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#who"><?php esc_html_e( 'صنعت شما', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#risk"><?php esc_html_e( 'ریسک‌ها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#price"><?php esc_html_e( 'محاسبه هزینه', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#faq"><?php esc_html_e( 'سوالات متداول', 'uid-theme' ); ?></button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_pa_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_pa_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_pa_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_pa_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_pa_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v5M12 16h.01"/></svg>';
}
function uid_pa_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
function uid_pa_who_icon_svg( $key ) {
	$icons = array(
		'shop'      => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 01-8 0"/>',
		'logistics' => '<path d="M1 3h13v13H1zM14 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="17.5" cy="18.5" r="2"/>',
		'fin'       => '<path d="M3 10l9-6 9 6M5 10v9h14v-9M9 19v-5h6v5"/>',
		'ins'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'ondemand'  => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'erp'       => '<ellipse cx="12" cy="5.5" rx="8" ry="2.8"/><path d="M4 5.5v13c0 1.6 3.6 2.8 8 2.8s8-1.2 8-2.8v-13"/><path d="M4 12c0 1.6 3.6 2.8 8 2.8s8-1.2 8-2.8"/>',
	);
	return $icons[ $key ] ?? $icons['shop'];
}
function uid_pa_who_color( $key ) {
	$colors = array( 'shop' => 'warm', 'logistics' => 'navy', 'fin' => '', 'ins' => '', 'ondemand' => 'warm', 'erp' => 'navy' );
	return $colors[ $key ] ?? '';
}
function uid_pa_why_icon_svg( $key ) {
	$icons = array(
		'reduce'    => '<path d="M9 11l3 3 8-8"/><path d="M20 12v7a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h9"/>',
		'ux'        => '<path d="M3 17l6-6 4 4 7-7"/><path d="M14 8h6v6"/>',
		'logistics' => '<path d="M1 3h13v13H1zM14 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="17.5" cy="18.5" r="2"/>',
		'dev'       => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'support'   => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
	);
	return $icons[ $key ] ?? $icons['reduce'];
}
function uid_pa_why_color( $key ) {
	$colors = array( 'reduce' => '', 'ux' => 'warm', 'logistics' => 'navy', 'dev' => '', 'support' => 'warm' );
	return $colors[ $key ] ?? '';
}
function uid_pa_team_icon_svg( $key ) {
	$icons = array(
		'phone'  => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
		'doc'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>',
		'report' => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
	);
	return $icons[ $key ] ?? $icons['phone'];
}

/* =====================================================================
 * ۱) هیرو + پیش‌نمایش زنده استعلام کد پستی (شبیه‌سازی نمایشی، داده آن هاردکد است)
 * ===================================================================== */
function uid_default_pa_samples() {
	return array(
		array(
			'label' => 'نمونه تهران', 'zip' => '1993774511', 'province' => 'تهران', 'county' => 'تهران', 'city' => 'تهران',
			'district' => 'منطقه ۳', 'hood' => 'ونک', 'main' => 'خیابان ولیعصر', 'side' => 'کوچه شهید نصیری',
			'plaque' => '۲۴', 'unit' => 'طبقه ۲ — واحد ۳',
		),
		array(
			'label' => 'نمونه اصفهان', 'zip' => '8163745192', 'province' => 'اصفهان', 'county' => 'اصفهان', 'city' => 'اصفهان',
			'district' => 'منطقه ۵', 'hood' => 'مرداویج', 'main' => 'بلوار کشاورز', 'side' => 'خیابان شهید قندی',
			'plaque' => '۷', 'unit' => 'طبقه همکف',
		),
		array(
			'label' => 'نمونه روستایی', 'zip' => '4761836254', 'province' => 'مازندران', 'county' => 'بابل', 'city' => 'روستای درزیکلا',
			'district' => 'بخش مرکزی', 'hood' => 'محله بالا', 'main' => 'جاده اصلی درزیکلا', 'side' => 'کوچه گلستان ۲',
			'plaque' => '۱۱', 'unit' => '—',
		),
	);
}
function uid_pa_samples_json() {
	$rows = uid_section_val( 'pahero', 'samples', uid_default_pa_samples() );
	if ( ! is_array( $rows ) || empty( $rows ) ) $rows = uid_default_pa_samples();
	$out = array();
	foreach ( $rows as $r ) {
		$out[] = array(
			'zip' => $r['zip'] ?? '', 'province' => $r['province'] ?? '', 'county' => $r['county'] ?? '', 'city' => $r['city'] ?? '',
			'district' => $r['district'] ?? '', 'hood' => $r['hood'] ?? '', 'main' => $r['main'] ?? '', 'side' => $r['side'] ?? '',
			'plaque' => $r['plaque'] ?? '', 'unit' => $r['unit'] ?? '',
		);
	}
	return wp_json_encode( $out );
}
function uid_render_section_pahero() {
	$tag     = uid_section_tag( 'pahero', 'h1' );
	$tags    = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'pahero', 'tags', "استعلام در لحظه\nپایداری ۹۹.۹٪ برای تراکنش بالا\nپوشش سراسری شهری و روستایی" ) ) ) );
	$samples = uid_section_val( 'pahero', 'samples', uid_default_pa_samples() );
	if ( ! is_array( $samples ) || empty( $samples ) ) $samples = uid_default_pa_samples();
	?>
	<section class="dark heroA">
	  <div class="pa-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌ احراز هویت', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'وب‌سرویس استعلام کد پستی و آدرس', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="pa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'pahero', 'eyebrow', __( 'وب‌سرویس استعلام کد پستی و آدرس · inquiry/address/v2', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'pahero', 'heading', __( 'کاربر شما ۸ فیلد نشانی را پر نمی‌کند.<mark>یک کد پستی را می‌کند.</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'pahero', 'text', __( 'وب‌سرویس استعلام آدرس یوآیدی با دریافت کد پستی ۱۰ رقمی، جزئیات محل سکونت کاربر — استان، شهر، محله، معبر اصلی و فرعی و پلاک — را در لحظه بازمی‌گرداند و فرم نشانی شما را خودکار پر می‌کند. ایده‌آل برای ارسال کالا در تجارت الکترونیک، لجستیک و هر فرایندی که نشانی دقیق لازم دارد — با راه‌اندازی و پشتیبانی تیم متخصص یوآیدی.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="pa-btn btn-cta" data-open-modal><?php echo uid_pa_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'pahero', 'btn1_text', __( 'درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="pa-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_pa_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_pa_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- شبیه‌سازی زنده کد پستی → آدرس — تماماً نمایشی، ساختار پاسخ واقعی است -->
	      <div class="idw rv rv-d2" id="idw">
	        <div class="idw-hd">
	          <span class="live" aria-hidden="true"></span>
	          <b><?php esc_html_e( 'تست زنده استعلام کد پستی', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'یک نمونه را همین‌جا اجرا کنید', 'uid-theme' ); ?></span>
	        </div>

	        <div class="zipfield" id="z_field">
	          <input id="z_code" type="text" inputmode="numeric" maxlength="10"
	                 placeholder="<?php esc_attr_e( 'کد پستی ۱۰ رقمی را وارد کنید', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'کد پستی ۱۰ رقمی', 'uid-theme' ); ?>">
	          <span class="cnt" id="z_cnt">۰ / ۱۰</span>
	          <span class="bar"><i id="z_bar"></i></span>
	        </div>

	        <div class="idw-chips">
	          <?php foreach ( $samples as $i => $s ) : ?>
	          <button class="idw-chip" type="button" data-zip="<?php echo (int) $i; ?>"><?php echo esc_html( $s['label'] ?? '' ); ?></button>
	          <?php endforeach; ?>
	        </div>

	        <button class="pa-btn btn-cta pa-btn-block" type="button" id="z_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg> <?php esc_html_e( 'اجرای استعلام آدرس', 'uid-theme' ); ?></button>

	        <div class="idcard">
	          <div class="idcard-hd">
	            <span class="idbadge wait" id="z_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span>
	            <div class="rswitch" role="tablist" aria-label="<?php esc_attr_e( 'نمای خروجی', 'uid-theme' ); ?>">
	              <button type="button" class="on" data-rview="ui"><?php esc_html_e( 'نمای ساختاری', 'uid-theme' ); ?></button>
	              <button type="button" data-rview="json"><span class="lat">JSON</span></button>
	            </div>
	          </div>

	          <div class="rpane on" data-rpane="ui">
	            <div class="idgrid" id="z_grid">
	              <div class="idrow"><span class="k"><?php esc_html_e( 'استان', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'شهرستان', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'شهر / روستا', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'منطقه پستی', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'محله', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'معبر اصلی', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'معبر فرعی', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'پلاک', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'طبقه و واحد', 'uid-theme' ); ?></span><span class="v mut">—</span></div>
	            </div>
	            <div class="addr-line"><span class="k">address</span>
	              <span class="v mut" id="z_full"><?php esc_html_e( 'نشانی کامل پس از استعلام اینجا ساخته می‌شود', 'uid-theme' ); ?></span></div>
	          </div>

	          <div class="rpane" data-rpane="json">
	            <pre class="rjson" id="z_json">{
  <span class="k">"responseContext"</span>: {
    <span class="k">"status"</span>: { <span class="k">"code"</span>: <span class="n">0</span>, <span class="k">"message"</span>: <span class="s">"—"</span> }
  },
  <span class="k">"postalCode"</span>: <span class="s">"—"</span>,
  <span class="k">"address"</span>: <span class="s">"—"</span>
}</pre>
	          </div>

	          <div class="idcard-ft">
	            <span><?php esc_html_e( 'وضعیت پاسخ: ', 'uid-theme' ); ?><span class="mono" id="z_status">—</span></span>
	            <span><?php esc_html_e( 'زمان پاسخ‌دهی: ', 'uid-theme' ); ?><b id="z_ms">—</b></span>
	          </div>
	        </div>

	        <div class="idw-note"><?php echo uid_pa_shield_icon(); ?>
	          <span><?php esc_html_e( 'این یک شبیه‌سازی نمایشی با داده‌های ساختگی است. در محیط عملیاتی، سرویس inquiry/address/v2 اطلاعات را از پایگاه داده مرجع پستی کشور بازمی‌گرداند.', 'uid-theme' ); ?></span></div>

	        <div class="idw-lead" id="idwLead">
	          <form novalidate>
	            <p><?php esc_html_e( 'همین تجربه را روی فرم پرداخت خودتان می‌خواهید؟ شماره‌تان را بگذارید، کارشناس یوآیدی تماس می‌گیرد.', 'uid-theme' ); ?></p>
	            <div class="micro-row">
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                   placeholder="۰۹xxxxxxxxx" data-req data-tel aria-label="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>">
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	              <button class="pa-btn btn-cta" type="button" data-submit><?php esc_html_e( 'تماس بگیرید', 'uid-theme' ); ?></button>
	            </div>
	            <div class="form-ok"><?php echo uid_pa_check_icon(); ?><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
	              <span><?php esc_html_e( 'همکار ما به‌زودی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۲) بند فوریت
 * ===================================================================== */
function uid_render_section_paurgency() {
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="pa-wrap">
	    <div class="callband urgent rv">
	      <div class="ic"><?php echo uid_pa_warn_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'paurgency', 'heading', __( 'هر بسته‌ای که به‌خاطر نشانی اشتباه برمی‌گردد، دو بار هزینه ارسال دارد', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'paurgency', 'text', __( 'تا وقتی نشانی را کاربر دستی تایپ می‌کند، غلط املایی، نام اشتباه کوچه و پلاک نامشخص در پایگاه داده شما ثبت می‌شود — و هزینه‌اش را انبار، پیک و پشتیبانی می‌پردازند. تیم متخصص یوآیدی می‌تواند وب‌سرویس استعلام کد پستی را همین هفته روی پلتفرم شما فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="pa-btn btn-cta pa-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'paurgency', 'btn1_text', __( 'فعال‌سازی سریع', 'uid-theme' ) ) ); ?></button>
	        <a class="pa-btn btn-ghost-d pa-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo esc_html( uid_section_val( 'paurgency', 'btn2_text', __( 'مشاوره رایگان با کارشناس', 'uid-theme' ) ) ); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) مشتریان (نوار متحرک)
 * ===================================================================== */
function uid_default_pa_marquee() {
	return array(
		array( 'letters' => 'پ', 'color' => '#7B4FD8', 'name' => 'پرداخت نوین', 'type' => 'ارائه‌دهنده خدمات پرداخت' ),
		array( 'letters' => 'از', 'color' => '#0AA3E0', 'name' => 'ازکی‌وام', 'type' => 'پلتفرم تسهیلات و وام' ),
		array( 'letters' => 'با', 'color' => '#1FB25A', 'name' => 'بازار', 'type' => 'فروشگاه اپلیکیشن' ),
		array( 'letters' => 'رز', 'color' => '#1A97A8', 'name' => 'رمزینکس', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'اک', 'color' => '#15397C', 'name' => 'اکسیر', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'آب', 'color' => '#29BCCE', 'name' => 'آبان‌تتر', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'تب', 'color' => '#DE7C13', 'name' => 'تبدیل', 'type' => 'صرافی ارز دیجیتال' ),
	);
}
function uid_render_section_pamarquee() {
	$items = uid_section_val( 'pamarquee', 'items', uid_default_pa_marquee() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:36px var(--sec)">
	  <div class="pa-wrap">
	    <p class="tiny rv" style="text-align:center;margin-bottom:18px"><?php echo esc_html( uid_section_val( 'pamarquee', 'heading', __( 'کسب‌وکارهایی که از وب‌سرویس‌های استعلامی یوآیدی استفاده می‌کنند', 'uid-theme' ) ) ); ?></p>
	    <div class="mq rv">
	      <div class="mq-tr">
	        <?php foreach ( array_merge( $items, $items ) as $it ) : ?>
	        <div class="mq-item"><span class="dot" style="background:<?php echo esc_attr( $it['color'] ?? '#15397C' ); ?>"><?php echo esc_html( $it['letters'] ?? '' ); ?></span><b><?php echo esc_html( $it['name'] ?? '' ); ?></b><small><?php echo esc_html( $it['type'] ?? '' ); ?></small></div>
	        <?php endforeach; ?>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) فرمی که جمع می‌شود — مقایسه قبل/بعد (محتوای ثابت نمایشی + متن دینامیک)
 * ===================================================================== */
function uid_render_section_paform() {
	$tag = uid_section_tag( 'paform', 'h2' );
	?>
	<section class="sec" style="background:var(--n50)" id="form">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'paform', 'eyebrow', __( 'همان فرم، قبل و بعد از اتصال', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'paform', 'heading', __( 'فرم نشانی شما، دو حالت دارد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'paform', 'text', __( 'این تنها تفاوت عملی وب‌سرویس استعلام کد پستی است — و همان چیزی است که کاربر در لحظه پرداخت حس می‌کند. سمت راست وضعیت امروز اکثر فرم‌هاست؛ سمت چپ همان فرم پس از اتصال به api استعلام آدرس یوآیدی.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="fcut rv" id="fcut">
	      <div class="fcut-card bad">
	        <div class="fcut-hd"><b><?php esc_html_e( 'فرم نشانی دستی', 'uid-theme' ); ?></b><span class="pill"><?php esc_html_e( '۸ فیلد ورودی', 'uid-theme' ); ?></span></div>
	        <div class="fcut-body">
	          <div class="fr"><span class="lb"><?php esc_html_e( 'استان', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'شهرستان', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'شهر', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'محله', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'خیابان اصلی', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'کوچه', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'پلاک', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	          <div class="fr"><span class="lb"><?php esc_html_e( 'واحد', 'uid-theme' ); ?></span><span class="ghost"></span></div>
	        </div>
	        <div class="fcut-ft"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'هر فیلد اضافه، یک فرصت برای رها کردن فرم و یک احتمال تازه برای نشانی غلط.', 'uid-theme' ); ?></span></div>
	      </div>

	      <div class="fcut-card good">
	        <div class="fcut-hd"><b><?php esc_html_e( 'فرم متصل به وب‌سرویس یوآیدی', 'uid-theme' ); ?></b><span class="pill"><?php esc_html_e( '۱ فیلد ورودی', 'uid-theme' ); ?></span></div>
	        <div class="fcut-body">
	          <div class="fr key"><span class="lb"><?php esc_html_e( 'کد پستی', 'uid-theme' ); ?></span><span class="val" id="fcutZip">۱۹۹۳۷۷۴۵۱۱</span></div>
	          <div class="fr auto" data-auto="0"><span class="lb"><?php esc_html_e( 'استان', 'uid-theme' ); ?></span><span class="val">تهران</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="1"><span class="lb"><?php esc_html_e( 'شهرستان', 'uid-theme' ); ?></span><span class="val">تهران</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="2"><span class="lb"><?php esc_html_e( 'شهر', 'uid-theme' ); ?></span><span class="val">تهران</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="3"><span class="lb"><?php esc_html_e( 'محله', 'uid-theme' ); ?></span><span class="val">ونک</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="4"><span class="lb"><?php esc_html_e( 'معبر اصلی', 'uid-theme' ); ?></span><span class="val">خیابان ولیعصر</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="5"><span class="lb"><?php esc_html_e( 'معبر فرعی', 'uid-theme' ); ?></span><span class="val">کوچه شهید نصیری</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="6"><span class="lb"><?php esc_html_e( 'پلاک', 'uid-theme' ); ?></span><span class="val">۲۴</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	          <div class="fr auto" data-auto="7"><span class="lb"><?php esc_html_e( 'واحد', 'uid-theme' ); ?></span><span class="val">۳</span><svg class="tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	        </div>
	        <div class="fcut-ft"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
	          <span><?php esc_html_e( 'هشت فیلد از پایگاه مرجع پستی پر می‌شود؛ کاربر فقط تایید می‌کند.', 'uid-theme' ); ?></span></div>
	      </div>
	    </div>

	    <div class="fcut-mid rv"><span class="ln"></span>
	      <span><?php echo wp_kses( uid_section_val( 'paform', 'stat_text', __( 'در داده‌های واقعی سایت یوآیدی، از <b>۵٬۴۹۷</b> کاربری که فرم را شروع کردند، فقط <b>۳٬۰۵۰</b> نفر آن را تمام کردند — <b>۴۴٪</b> ریزش در میانه فرم.', 'uid-theme' ) ), array( 'b' => array() ) ); ?></span>
	      <span class="ln"></span></div>

	    <div class="callband rv" style="margin-block-start:26px">
	      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'paform', 'cta_heading', __( 'کوتاه کردن فرم، ارزان‌ترین راه افزایش نرخ تبدیل است', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'paform', 'cta_text', __( 'کارشناس یوآیدی فرم فعلی شما را بررسی می‌کند و می‌گوید دقیقاً کدام فیلدها با استعلام کد پستی حذف می‌شوند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="pa-btn btn-cta pa-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'paform', 'cta_btn_text', __( 'بررسی رایگان فرم من', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۵) ورودی/خروجی API (متن داینامیک؛ نمونه‌کدها هاردکد)
 * ===================================================================== */
function uid_render_section_paout() {
	$tag = uid_section_tag( 'paout', 'h2' );
	?>
	<section class="sec" id="out">
	  <div class="pa-wrap">
	    <div class="codepanel rv">
	      <div>
	        <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'paout', 'eyebrow', __( 'ورودی و خروجی api استعلام آدرس', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 14px">'; ?><?php echo esc_html( uid_section_val( 'paout', 'heading', __( 'یک عدد می‌فرستید، یک نشانی کامل می‌گیرید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede"><?php esc_html_e( 'اتصال به وب‌سرویس یوآیدی از طریق متد استاندارد POST انجام می‌شود و تنها با ارسال کد پستی، اطلاعات نشانی در قالب JSON ساختاریافته دریافت می‌گردد. تیم فنی شما می‌تواند با مطالعه مستندات و استفاده از محیط سندباکس، این وب‌سرویس را در کمتر از چند ساعت در سامانه خود پیاده‌سازی کند.', 'uid-theme' ); ?></p>

	        <div class="fmap-io" style="margin-block-start:24px">
	          <div class="fmap-side inp">
	            <span class="lb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg><?php esc_html_e( 'آنچه شما ارسال می‌کنید — ۱ فیلد', 'uid-theme' ); ?></span>
	            <div class="fmap-tags">
	              <span class="ftag"><code>postalCode</code><span><?php esc_html_e( 'کد پستی ۱۰ رقمی کاربر', 'uid-theme' ); ?></span></span>
	            </div>
	          </div>
	          <div class="fmap-side out">
	            <span class="lb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'آنچه در نشانی بازگشتی وجود دارد', 'uid-theme' ); ?></span>
	            <div class="fmap-tags">
	              <span class="ftag"><code>province</code><span><?php esc_html_e( 'استان', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>county</code><span><?php esc_html_e( 'شهرستان', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>district</code><span><?php esc_html_e( 'بخش و منطقه پستی', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>city</code><span><?php esc_html_e( 'شهر یا روستا', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>neighbourhood</code><span><?php esc_html_e( 'نام محله', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>mainStreet</code><span><?php esc_html_e( 'معبر اصلی', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>sideStreet</code><span><?php esc_html_e( 'معبر فرعی', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>plaque</code><span><?php esc_html_e( 'شماره پلاک', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>floor / unit</code><span><?php esc_html_e( 'طبقه و واحد', 'uid-theme' ); ?></span></span>
	              <span class="ftag"><code>building</code><span><?php esc_html_e( 'نام ساختمان یا مجتمع', 'uid-theme' ); ?></span></span>
	            </div>
	          </div>
	        </div>
	        <p class="tiny" style="margin-block-start:12px"><?php esc_html_e( 'طبقه، واحد و نام ساختمان تنها در صورت وجود رکورد در پایگاه داده مرجع پستی بازگردانده می‌شوند.', 'uid-theme' ); ?></p>

	        <div class="btn-row" style="margin-block-start:22px">
	          <button class="pa-btn btn-cta" type="button" data-open-modal><?php esc_html_e( 'فرم درخواست کلید API', 'uid-theme' ); ?></button>
	          <a class="pa-btn btn-ghost" href="<?php echo esc_url( home_url( '/address-postcode-docs/' ) ); ?>"><?php esc_html_e( 'مشاهده مستندات فنی API', 'uid-theme' ); ?><?php echo uid_pa_arrow_icon(); ?></a>
	        </div>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button class="on" type="button" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button type="button" data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" type="button" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req">POST <span class="k">https://json-api.uid.ir/api/inquiry/address/v2</span>
Content-Type: application/json;charset=UTF-8

{
  "requestContext": {
    "apiInfo": {
      "businessId": <span class="m">&lt;UID_BUSINESS_ID&gt;</span>,
      "businessToken": <span class="m">&lt;UID_BUSINESS_TOKEN&gt;</span>
    }
  },
  "postalCode": <span class="s">"0123456789"</span>
}</pre>
<pre class="code-pane" data-code-pane="res">{
  "responseContext": {
    "status": {
      "code": <span class="s">0</span>,
      "message": <span class="s">"SUCCESS."</span>,
      "details": []
    },
    "requestId": <span class="s">""</span>,
    "correlationId": <span class="s">""</span>,
    "navigationURI": <span class="s">""</span>,
    "nextStepToken": <span class="s">""</span>,
    "userSessionId": <span class="s">""</span>,
    "custom": {}
  },
  "postalCode": <span class="m">&lt;POSTAL_CODE&gt;</span>,
  "address": <span class="m">&lt;ADDRESS&gt;</span>
}</pre>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۶) نحوه کار سرویس + آمار کوتاه
 * ===================================================================== */
function uid_default_pa_flow_how() {
	return array(
		array( 'title' => 'ارسال کد پستی', 'text' => 'کد پستی کاربر از سمت نرم‌افزار، وب‌سایت یا اپلیکیشن شما از طریق وب‌سرویس امن به یوآیدی ارسال می‌شود.' ),
		array( 'title' => 'اعتبارسنجی و پردازش', 'text' => 'سامانه صحت ساختار کد پستی را بررسی کرده و اطلاعات مرتبط با آن را از پایگاه مرجع فراخوانی می‌کند.' ),
		array( 'title' => 'بازگشت داده‌های ساختاریافته', 'text' => 'تمام فیلدهای نشانی به تفکیک، در قالب یک پاسخ ساختاریافته به سیستم شما تحویل داده می‌شود تا فرم‌ها به‌صورت خودکار پر شوند.' ),
	);
}
function uid_default_pa_qstats() {
	return array(
		array( 'value' => '۹۹.۹٪', 'is_html' => false, 'label' => 'پایداری سرویس (Uptime) برای کسب‌وکارهای با حجم تراکنش بالا' ),
		array( 'value' => 'در لحظه', 'is_html' => false, 'label' => 'سرعت پاسخ‌دهی (Latency)؛ استعلام بدون توقف فرم' ),
		array( 'value' => 'سراسری', 'is_html' => false, 'label' => 'اتصال مستقیم به پایگاه جامع آدرس‌های شهری و روستایی کشور' ),
		array( 'value' => 'SSL/TLS', 'is_html' => false, 'label' => 'رمزنگاری کامل تمام تبادلات وب‌سرویس' ),
	);
}
function uid_render_section_pahow() {
	$tag   = uid_section_tag( 'pahow', 'h2' );
	$steps = uid_section_val( 'pahow', 'steps', uid_default_pa_flow_how() );
	$stats = uid_section_val( 'pahow', 'stats', uid_default_pa_qstats() );
	if ( ! is_array( $steps ) ) $steps = array();
	if ( ! is_array( $stats ) ) $stats = array();
	?>
	<section class="sec" style="background:var(--n50)" id="how">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pahow', 'eyebrow', __( 'مراحل عملیاتی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pahow', 'heading', __( 'استعلام کد پستی چگونه کار می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pahow', 'text', __( 'کل چرخه، از لحظه‌ای که کاربر ده رقم را تایپ می‌کند تا لحظه‌ای که فرم شما پر می‌شود، سه گام دارد و در کسری از ثانیه اتفاق می‌افتد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="flow3 rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="fl-node">
	        <div class="fl-card"><div class="fl-num"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></div>
	          <h4><?php echo esc_html( $s['title'] ?? '' ); ?></h4>
	          <p><?php echo esc_html( $s['text'] ?? '' ); ?></p></div>
	        <?php if ( $i < count( $steps ) - 1 ) : ?>
	        <div class="fl-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 6l6 6-6 6M4 12h16" transform="scale(-1,1) translate(-24,0)"/></svg></div>
	        <?php endif; ?>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <?php if ( $stats ) : ?>
	    <div class="qstats rv" style="margin-block-start:34px">
	      <?php foreach ( $stats as $q ) : ?>
	      <div class="qstat"><span class="v txt"><?php echo esc_html( $q['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $q['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۷) کاربرد بر اساس صنعت (انتخابگر)
 * ===================================================================== */
function uid_default_pa_who() {
	return array(
		array( 'icon' => 'shop', 'label' => 'فروشگاه اینترنتی', 'title' => 'فروشگاه‌های اینترنتی و تجارت الکترونیک', 'text' => 'تکمیل آنی فیلدهای آدرس در مرحله پرداخت، جلوگیری از انصراف کاربر به دلیل طولانی بودن فرم‌ها و کاهش زمان ثبت سفارش.', 'win' => 'سبد خرید کمتری در مرحله نشانی رها می‌شود' ),
		array( 'icon' => 'logistics', 'label' => 'لجستیک و پست', 'title' => 'شرکت‌های لجستیک، پست و حمل‌ونقل', 'text' => 'اصلاح نشانی‌های ناخوانا، کاهش چشمگیر بسته‌های برگشتی و استانداردسازی اطلاعات برای مسیریابی ناوگان توزیع.', 'win' => 'هزینه ارسال مجدد مرجوعی‌ها کم می‌شود' ),
		array( 'icon' => 'fin', 'label' => 'بانک و فین‌تک', 'title' => 'بانک‌ها، فین‌تک‌ها و سامانه‌های اعتبارسنجی', 'text' => 'تایید و ثبت نشانی محل سکونت متقاضیان در فرایند افتتاح حساب دیجیتال، صدور چک و اعطای تسهیلات.', 'win' => 'پرونده اعتبارسنجی با نشانی معتبر بسته می‌شود' ),
		array( 'icon' => 'ins', 'label' => 'بیمه و خدمات مالی', 'title' => 'شرکت‌های بیمه و خدمات مالی', 'text' => 'ثبت دقیق نشانی اموال غیرمنقول، بیمه‌نامه‌های آتش‌سوزی و خودرو بدون نیاز به ورود کاغذی و بازخوانی دستی.', 'win' => 'نشانی مورد بیمه، قابل استناد ثبت می‌شود' ),
		array( 'icon' => 'ondemand', 'label' => 'خدمات در محل', 'title' => 'پلتفرم‌های خدمات در محل', 'text' => 'اعزام سریع و بدون اشتباه کادر فنی، درمانی یا خدماتی به نشانی مشتریان — بدون تماس تلفنی برای پیدا کردن آدرس.', 'win' => 'زمان رسیدن نیرو به محل کوتاه می‌شود' ),
		array( 'icon' => 'erp', 'label' => 'ERP و CRM', 'title' => 'سامانه‌های ERP و مدیریت مشتریان (CRM)', 'text' => 'یکپارچه‌سازی و جلوگیری از ثبت داده‌های تکراری و متناقض در پرونده کارکنان و شرکای تجاری.', 'win' => 'یک نشانی استاندارد در همه ماژول‌ها' ),
	);
}
function uid_render_section_pawho() {
	$tag   = uid_section_tag( 'pawho', 'h2' );
	$items = uid_section_val( 'pawho', 'items', uid_default_pa_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="who">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pawho', 'eyebrow', __( 'کاربرد وب‌سرویس بر اساس صنایع', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pawho', 'heading', __( 'در صنعت شما، استعلام کد پستی چه چیزی را حل می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pawho', 'text', __( 'صنعت خودتان را انتخاب کنید تا دقیقاً ببینید این وب‌سرویس در فرآیند شما کجا می‌نشیند و چه هزینه‌ای را حذف می‌کند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="rv">
	      <div class="pick-chips" data-pick-chips="ind" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب صنعت', 'uid-theme' ); ?>"></div>
	      <div class="pick" data-pick="ind">
	        <?php foreach ( $items as $it ) :
	          $color = uid_pa_who_color( $it['icon'] ?? 'shop' );
	        ?>
	        <article class="pick-card" data-label="<?php echo esc_attr( $it['label'] ?? '' ); ?>">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_pa_who_icon_svg( $it['icon'] ?? 'shop' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="win"><?php echo uid_pa_check_icon(); ?><?php echo esc_html( $it['win'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="pick-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( 'صنعت خود را از نوار بالا انتخاب کنید یا کارت را بکشید', 'uid-theme' ); ?></div>
	    </div>

	    <div class="callband rv" style="margin-block-start:34px">
	      <div class="ic"><?php echo uid_pa_phone_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'pawho', 'cta_heading', __( 'صنعت شما در این فهرست نبود؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'pawho', 'cta_text', __( 'هر فرآیندی که نشانی دقیق لازم دارد، از این سرویس سود می‌برد. کارشناس یوآیدی سناریوی شما را بررسی می‌کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="pa-btn btn-cta pa-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="pa-btn btn-ghost-d pa-btn-sm" data-open-modal><?php esc_html_e( 'درخواست تماس', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۸) ریسک‌ها و راه‌حل‌ها
 * ===================================================================== */
function uid_default_pa_risks() {
	return array(
		array( 'title' => 'خطای انسانی و نشانی ناقص', 'bad_text' => 'غلط املایی، اسامی اشتباه کوچه‌ها و پلاک‌های نامشخص، مستقیماً وارد پایگاه داده شما می‌شوند و بعداً قابل اصلاح نیستند.', 'fix_text' => 'نشانی مستقیماً از پایگاه مرجع پستی خوانده می‌شود؛ املا و ساختار آن استاندارد است.' ),
		array( 'title' => 'رها شدن فرم و افت نرخ تبدیل', 'bad_text' => 'فرم نشانی طولانی، یکی از پرریزش‌ترین مراحل مسیر خرید است؛ کاربر در میانه راه بیرون می‌رود و سبد خرید باز می‌ماند.', 'fix_text' => 'کاربر تنها با نوشتن ۱۰ رقم، کل فرم آدرس را تکمیل‌شده می‌بیند و مسیر خرید ادامه پیدا می‌کند.' ),
		array( 'title' => 'بسته‌های مرجوعی و ارسال مجدد', 'bad_text' => 'نشانی نادرست یعنی مرجوعی، یعنی هزینه ارسال دوباره، تماس پشتیبانی و یک مشتری ناراضی برای یک سفارش.', 'fix_text' => 'صرفه‌جویی مستقیم در هزینه‌های لجستیک با نشانی قابل اتکا برای مسیریابی ناوگان توزیع.' ),
		array( 'title' => 'داده‌های تکراری و متناقض', 'bad_text' => 'یک مشتری با سه نگارش مختلف از یک نشانی در CRM ثبت می‌شود و گزارش‌های شما را بی‌اعتبار می‌کند.', 'fix_text' => 'ثبت اطلاعات مکانی در پایگاه داده سازمان شما استاندارد و یکدست می‌شود.' ),
	);
}
function uid_render_section_parisk() {
	$tag   = uid_section_tag( 'parisk', 'h2' );
	$items = uid_section_val( 'parisk', 'items', uid_default_pa_risks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)" id="risk">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'parisk', 'eyebrow', __( 'مشکلی که این وب‌سرویس حل می‌کند', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'parisk', 'heading', __( 'بدون استعلام کد پستی، این چهار هزینه هر ماه تکرار می‌شود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'parisk', 'text', __( 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه استعلام نشانی با آن روبه‌رو می‌شوند — و اینکه api استعلام آدرس یوآیدی چطور در همان لحظه جلویشان را می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="leak-grid deck d2" data-deck="risk">
	        <?php foreach ( $items as $it ) : ?>
	        <div class="leak">
	          <div class="leak-bad"><span class="tag"><?php echo uid_pa_warn_icon(); ?><?php esc_html_e( 'هزینه رایج', 'uid-theme' ); ?></span>
	            <h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
	            <p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p></div>
	          <div class="leak-fix"><span class="tag"><?php echo uid_pa_check_icon(); ?><?php esc_html_e( 'راه‌حل استعلام کد پستی', 'uid-theme' ); ?></span>
	            <p><?php echo esc_html( $it['fix_text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="risk">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( '۴ مورد — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۹) محاسبه‌گر هزینه ماهانه (متن دینامیک؛ منطق محاسبه در JS هاردکد است)
 * ===================================================================== */
function uid_render_section_paprice() {
	$tag = uid_section_tag( 'paprice', 'h2' );
	?>
	<section class="sec" id="price">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'paprice', 'eyebrow', __( 'سازوکار محاسبه هزینه و تعرفه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'paprice', 'heading', __( 'محاسبه‌گر هزینه ماهانه استعلام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo wp_kses( uid_section_val( 'paprice', 'text', __( 'هزینه بر اساس <b>تعداد استعلام‌های موفق</b> محاسبه می‌شود؛ استعلام‌های ناموفق یا کدهای پستی فاقد رکورد هزینه‌ای ندارند. هرچه حجم استعلام ماهانه بالاتر باشد، تعرفه هر استعلام پایین‌تر می‌آید. تخمین حجم خود را جابه‌جا کنید تا پلن مناسبتان را ببینید.', 'uid-theme' ) ), array( 'b' => array() ) ); ?></p>
	    </div>

	    <div class="calc rv">
	      <div class="calc-in">
	        <span class="lb"><?php esc_html_e( 'تعداد استعلام تخمینی ماهانه', 'uid-theme' ); ?></span>
	        <div class="calc-num"><span class="v" id="cl_qty">۵۰٬۰۰۰</span><span class="u"><?php esc_html_e( 'استعلام موفق در ماه', 'uid-theme' ); ?></span></div>
	        <div class="calc-slider">
	          <input id="cl_range" type="range" min="0" max="1000" value="560"
	                 aria-label="<?php esc_attr_e( 'تعداد استعلام تخمینی ماهانه', 'uid-theme' ); ?>">
	          <div class="calc-ends"><span>۱٬۰۰۰</span><span>۲٬۰۰۰٬۰۰۰+</span></div>
	        </div>
	        <div class="calc-presets" id="cl_presets">
	          <button class="calc-preset" type="button" data-qty="5000"><?php esc_html_e( '۵ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="50000"><?php esc_html_e( '۵۰ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="200000"><?php esc_html_e( '۲۰۰ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="1000000"><?php esc_html_e( '۱ میلیون', 'uid-theme' ); ?></button>
	        </div>
	        <div class="calc-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'ارقام این محاسبه‌گر جهت برآورد اولیه است. تعرفه نهایی و پله تخفیف، پس از بررسی حجم و نوع کاربرد کسب‌وکار شما، توسط کارشناس یوآیدی در پیش‌فاکتور رسمی اعلام می‌شود.', 'uid-theme' ); ?></span></div>
	      </div>

	      <div class="calc-out">
	        <div class="calc-plan">
	          <span class="tag" id="cl_plan"><?php esc_html_e( 'پلن رشد', 'uid-theme' ); ?></span>
	          <span class="tag off" id="cl_off"><?php esc_html_e( '۱۵٪ تخفیف پلکانی', 'uid-theme' ); ?></span>
	        </div>
	        <div class="calc-rows">
	          <div class="calc-row"><span><?php esc_html_e( 'تعداد استعلام موفق', 'uid-theme' ); ?></span><b id="cl_n">۵۰٬۰۰۰</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'تعرفه هر استعلام', 'uid-theme' ); ?></span><b id="cl_unit">—</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'هزینه پیش از تخفیف', 'uid-theme' ); ?></span><b id="cl_gross">—</b></div>
	          <div class="calc-row total"><span><?php esc_html_e( 'هزینه ماهانه با تخفیف پلکانی', 'uid-theme' ); ?></span><b id="cl_total">—</b></div>
	        </div>
	        <div class="calc-free"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
	          <span><?php esc_html_e( 'استعلام‌های ناموفق و کدهای پستی فاقد رکورد، رایگان محاسبه می‌شوند.', 'uid-theme' ); ?></span></div>
	        <div class="calc-cta">
	          <button class="pa-btn btn-cta pa-btn-block" data-open-modal><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg> <?php esc_html_e( 'دریافت پیش‌فاکتور رسمی این پلن', 'uid-theme' ); ?></button>
	          <p class="calc-dis"><?php esc_html_e( 'برای حجم‌های بالاتر از یک میلیون استعلام در ماه، تعرفه اختصاصی و قرارداد سازمانی تعریف می‌شود — با ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span><?php esc_html_e( ' تماس بگیرید.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۰) باند اعتماد
 * ===================================================================== */
function uid_default_pa_trust() {
	return array(
		array( 'type' => 'count', 'value' => '5000000', 'label' => 'احراز هویت موفق یوآیدی تاکنون' ),
		array( 'type' => 'text', 'value' => 'از ۱۳۹۶', 'label' => 'سابقه فعالیت در احراز هویت آنلاین' ),
		array( 'type' => 'text', 'value' => 'دانش‌بنیان', 'label' => 'شرکت بینش هوشمند نسل پیشرو' ),
		array( 'type' => 'text', 'value' => 'سجام و ثنا', 'label' => 'کارگزار مورد تایید سامانه‌های رسمی' ),
		array( 'type' => 'text', 'value' => '۱۳ سرویس', 'label' => 'وب‌سرویس استعلامی فعال یوآیدی' ),
	);
}
function uid_render_section_patrust() {
	$tag   = uid_section_tag( 'patrust', 'h2' );
	$cells = uid_section_val( 'patrust', 'cells', uid_default_pa_trust() );
	if ( ! is_array( $cells ) || empty( $cells ) ) return;
	?>
	<section class="dark sec">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'patrust', 'eyebrow', __( 'اعتماد کسب‌وکارها به یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec" style="color:#fff">'; ?><?php echo esc_html( uid_section_val( 'patrust', 'heading', __( 'یوآیدی، زیرساخت احراز هویت و استعلام مورد اعتماد کسب‌وکارهای ایرانی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="trust-band rv">
	      <?php foreach ( $cells as $c ) :
	        $is_count = 'count' === ( $c['type'] ?? 'text' );
	      ?>
	      <div class="trust-cell">
	        <?php if ( $is_count ) : ?>
	        <span class="v" data-count="<?php echo esc_attr( absint( $c['value'] ?? 0 ) ); ?>">۰</span>
	        <?php else : ?>
	        <span class="v ov txt"><?php echo esc_html( $c['value'] ?? '' ); ?></span>
	        <?php endif; ?>
	        <span class="l"><?php echo esc_html( $c['label'] ?? '' ); ?></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۱) چرا یوآیدی
 * ===================================================================== */
function uid_default_pa_why() {
	return array(
		array( 'icon' => 'reduce', 'title' => 'کاهش خطای انسانی و ثبت ناقص نشانی', 'text' => 'حذف غلط‌های املایی، اسامی اشتباه کوچه‌ها و پلاک‌های نامشخص — نشانی از منبع رسمی می‌آید، نه از حافظه کاربر.' ),
		array( 'icon' => 'ux', 'title' => 'بهبود تجربه کاربری و افزایش نرخ تبدیل', 'text' => 'کاربر تنها با نوشتن ۱۰ رقم کد پستی، کل فرم آدرس را تکمیل‌شده می‌بیند؛ کوتاه‌ترین مسیر ممکن تا پرداخت.' ),
		array( 'icon' => 'logistics', 'title' => 'صرفه‌جویی در هزینه‌های لجستیک', 'text' => 'کاهش هزینه‌های ارسال مجدد بسته‌های مرجوعی به دلیل آدرس نادرست — صرفه‌جویی مستقیم و قابل اندازه‌گیری.' ),
		array( 'icon' => 'dev', 'title' => 'پیاده‌سازی سریع و مستندات شفاف', 'text' => 'دارای نمونه کدهای آماده به زبان‌های مختلف برنامه‌نویسی و SDKهای استاندارد؛ راه‌اندازی در کمتر از چند ساعت.' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی اختصاصی و گزارش‌دهی دوره‌ای', 'text' => 'تیم پشتیبانی ویژه برای هر کسب‌وکار، به‌همراه گزارش دقیق تعداد تراکنش‌ها، لاگ‌های موفق و ناموفق و وضعیت مصرف اعتبار.' ),
	);
}
function uid_render_section_pawhy() {
	$tag   = uid_section_tag( 'pawhy', 'h2' );
	$items = uid_section_val( 'pawhy', 'items', uid_default_pa_why() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pawhy', 'eyebrow', __( 'مزایای استفاده از وب‌سرویس کد پستی یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pawhy', 'heading', __( 'پنج دلیلی که کسب‌وکارها این سرویس را از یوآیدی می‌گیرند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="why">
	        <?php foreach ( $items as $it ) :
	          $color = uid_pa_why_color( $it['icon'] ?? 'reduce' );
	        ?>
	        <div class="dcard">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_pa_why_icon_svg( $it['icon'] ?? 'reduce' ), array( 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="why">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( '۵ مزیت — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۲) مراحل اتصال و پیاده‌سازی (۳ گام)
 * ===================================================================== */
function uid_default_pa_start() {
	return array(
		array( 'title' => 'ثبت درخواست دمو و مشاوره', 'text' => 'مشخصات کسب‌وکار را در فرم سایت ارسال می‌کنید و کارشناس یوآیدی راهنمایی تخصصی و پلن متناسب با حجم استعلام شما را ارائه می‌دهد.' ),
		array( 'title' => 'دریافت کلید دسترسی و مستندات', 'text' => 'دسترسی به محیط آزمایشی (Sandbox)، کدهای نمونه و کلید API اختصاصی جهت ادغام، در اختیار تیم فنی شما قرار می‌گیرد.' ),
		array( 'title' => 'تست، یکپارچه‌سازی و اتصال نهایی', 'text' => 'پیاده‌سازی وب‌سرویس توسط تیم فنی شما، تست سناریوها و ورود به محیط عملیاتی — به همراه پشتیبانی اختصاصی یوآیدی.' ),
	);
}
function uid_render_section_pastart() {
	$tag   = uid_section_tag( 'pastart', 'h2' );
	$steps = uid_section_val( 'pastart', 'steps', uid_default_pa_start() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" style="background:var(--n50)" id="start">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pastart', 'eyebrow', __( 'مراحل اتصال و پیاده‌سازی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pastart', 'heading', __( 'از تماس تا اولین استعلام، در سه گام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pastart', 'text', __( 'راه‌اندازی این وب‌سرویس با همراهی تیم متخصص یوآیدی انجام می‌شود؛ از انتخاب پلن تا اولین استعلام موفق در محیط عملیاتی.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="flow3 rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="fl-node">
	        <div class="fl-card"><div class="fl-num"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></div>
	          <h4><?php echo esc_html( $s['title'] ?? '' ); ?></h4>
	          <p><?php echo esc_html( $s['text'] ?? '' ); ?></p></div>
	        <?php if ( $i < count( $steps ) - 1 ) : ?>
	        <div class="fl-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 6l6 6-6 6M4 12h16" transform="scale(-1,1) translate(-24,0)"/></svg></div>
	        <?php endif; ?>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۳) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_pa_team_rows() {
	return array(
		array( 'icon' => 'phone', 'title' => 'مشاوره انتخاب پلن و نقطه اتصال', 'text' => 'کارشناس یوآیدی فرم و فرآیند واقعی کسب‌وکار شما را بررسی می‌کند و تعیین می‌کند استعلام کد پستی در کدام مرحله بیشترین اثر را روی نرخ تبدیل می‌گذارد' ),
		array( 'icon' => 'doc', 'title' => 'همراهی در یکپارچه‌سازی', 'text' => 'مستندات، محیط سندباکس و کلید دسترسی را می‌دهیم و تا اولین استعلام موفق در محیط عملیاتی، کنار توسعه‌دهندگان شما می‌مانیم' ),
		array( 'icon' => 'report', 'title' => 'گزارش‌دهی دوره‌ای مصرف', 'text' => 'از طریق پنل کاربری اختصاصی، گزارش لحظه‌ای استعلام‌های موفق و ناموفق و نمودارهای مصرف اعتبار در دسترس شماست' ),
	);
}
function uid_default_pa_pledge_items() {
	return array(
		'هزینه فقط بابت استعلام موفق؛ استعلام ناموفق رایگان است.',
		'تخفیف پلکانی شفاف برای حجم‌های بالای ماهانه، مکتوب در قرارداد.',
		'تمام تبادلات از کانال امن SSL/TLS انجام می‌شود.',
		'گزارش لحظه‌ای تراکنش‌ها و مصرف اعتبار در پنل اختصاصی شما.',
	);
}
function uid_render_section_pateam() {
	$rows       = uid_section_val( 'pateam', 'rows', uid_default_pa_team_rows() );
	$pledge_raw = uid_section_val( 'pateam', 'pledge_items', implode( "\n", uid_default_pa_pledge_items() ) );
	$pledge     = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	$tag = uid_section_tag( 'pateam', 'h2' );
	?>
	<section class="sec">
	  <div class="pa-wrap">
	    <div class="team rv">
	      <div>
	        <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pateam', 'eyebrow', __( 'تیم متخصص یوآیدی کنار شماست', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 20px">'; ?><?php echo esc_html( uid_section_val( 'pateam', 'heading', __( 'شما فقط یک API نمی‌خرید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <div class="team-list" data-rail="team">
	          <?php foreach ( $rows as $r ) : ?>
	          <div class="team-row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_pa_team_icon_svg( $r['icon'] ?? 'phone' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	            <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b><p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div></div>
	          <?php endforeach; ?>
	        </div>
	        <div class="dots" data-dots="team"></div>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.6 5.6 6.1.9-4.4 4.3 1 6.1-5.3-2.8-5.3 2.8 1-6.1L3.3 8.5l6.1-.9z"/></svg></span><b><?php echo esc_html( uid_section_val( 'pateam', 'pledge_heading', __( 'تعهد یوآیدی به کسب‌وکار شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_pa_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div class="btn-row" style="margin-block-start:20px">
	          <a class="pa-btn pa-btn-navy btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_pa_phone_icon(); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۴) بند امنیت داده‌ها
 * ===================================================================== */
function uid_render_section_pasecurity() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="pa-wrap">
	    <div class="secbox rv">
	      <div class="ic"><?php echo uid_pa_shield_icon(); ?></div>
	      <div><b><?php echo esc_html( uid_section_val( 'pasecurity', 'heading', __( 'امنیت داده‌ها، اولویت اول یوآیدی', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'pasecurity', 'text', __( 'تمام تبادلات با وب‌سرویس استعلام کد پستی و آدرس از طریق کانال‌های امن (SSL/TLS) انجام می‌شود و داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند. دسترسی به سرویس تنها برای کسب‌وکارهای مجاز و از طریق businessId و businessToken اختصاصی فعال می‌شود.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۵) سوالات متداول
 * ===================================================================== */
function uid_default_pa_faq() {
	return array(
		array( 'question' => 'برای ارسال درخواست به وب‌سرویس چه داده‌ای لازم است؟', 'answer' => 'تنها یک کد پستی معتبر ۱۰ رقمی کافی است. درخواست با متد POST و به‌همراه businessId و businessToken اختصاصی کسب‌وکار شما ارسال می‌شود.' ),
		array( 'question' => 'پاسخ وب‌سرویس شامل چه جزئیاتی از نشانی است؟', 'answer' => 'اطلاعات شامل استان، شهرستان، شهر یا روستا، منطقه، محله، معبر اصلی، معبر فرعی، پلاک، طبقه و سایر اطلاعات ثبت‌شده برای آن کد پستی است.' ),
		array( 'question' => 'آیا تمام فیلدهای نشانی برای همه کدهای پستی پر هستند؟', 'answer' => 'خیر؛ خروجی بر اساس اطلاعات موجود در پایگاه داده مرجع پستی ارسال می‌شود. برای ساختمان‌های تک‌واحدی یا مناطق کمتر توسعه‌یافته ممکن است برخی فیلدها مانند طبقه بازگردانده نشوند.' ),
		array( 'question' => 'آیا این سرویس طول و عرض جغرافیایی (لوکیشن روی نقشه) ارائه می‌دهد؟', 'answer' => 'تمرکز اصلی این سرویس بر ارائه نشانی متنی ساختاریافته است. برای نیازهای نقشه‌محور، امکان بررسی و افزودن سرویس‌های مکمل جغرافیایی وجود دارد؛ با کارشناسان یوآیدی در میان بگذارید.' ),
		array( 'question' => 'نحوه دسترسی به لاگ‌ها و گزارش تراکنش‌ها چگونه است؟', 'answer' => 'کسب‌وکارها از طریق پنل کاربری اختصاصی خود در یوآیدی، به‌صورت لحظه‌ای گزارش کامل تعداد استعلام‌های موفق، ناموفق و نمودارهای مصرف را مشاهده می‌کنند.' ),
		array( 'question' => 'آیا استعلام ناموفق مشمول هزینه می‌شود؟', 'answer' => 'خیر؛ تنها استعلام‌هایی که اطلاعات نشانی معتبر بازگردانند به عنوان تراکنش موفق محاسبه می‌شوند.' ),
		array( 'question' => 'پیاده‌سازی این وب‌سرویس چقدر طول می‌کشد؟', 'answer' => 'تیم فنی شما می‌تواند با مطالعه مستندات و استفاده از محیط سندباکس، این وب‌سرویس را در کمتر از چند ساعت در سامانه خود پیاده‌سازی کند. نمونه کدهای آماده به زبان‌های مختلف برنامه‌نویسی و SDKهای استاندارد نیز ارائه می‌شود.' ),
		array( 'question' => 'هزینه سرویس چگونه محاسبه می‌شود؟', 'answer' => 'محاسبه هزینه بر اساس تعداد استعلام‌های موفق انجام می‌شود و برای کسب‌وکارهای با حجم استعلام بالا، تعرفه پلکانی و تخفیف‌های ویژه در نظر گرفته می‌شود. برای دریافت پیش‌فاکتور دقیق، از محاسبه‌گر بالای همین صفحه یا تماس با کارشناسان استفاده کنید.' ),
	);
}
function uid_render_section_pafaq() {
	$tag   = uid_section_tag( 'pafaq', 'h2' );
	$items = uid_section_val( 'pafaq', 'items', uid_default_pa_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pafaq', 'eyebrow', __( 'پرسش‌های پرتکرار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pafaq', 'heading', __( 'سوالات متداول درباره وب‌سرویس استعلام کد پستی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i"><button class="faq-q" aria-expanded="false"><?php echo esc_html( $it['question'] ?? '' ); ?>
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	    <div class="callband rv" style="margin-block-start:36px">
	      <div class="ic"><?php echo uid_pa_phone_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'pafaq', 'cta_heading', __( 'سوال شما اینجا نبود؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'pafaq', 'cta_text', __( 'کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="pa-btn btn-cta pa-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="pa-btn btn-ghost-d pa-btn-sm" data-open-modal><?php esc_html_e( 'درخواست تماس', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۶) سرویس‌های دیگر — کاشی‌ها (یک قدم جلوتر)
 * ===================================================================== */
function uid_default_pa_svc() {
	return array(
		'وب‌سرویس ثبت احوال', 'وب‌سرویس شاهکار', 'سرویس استعلام اطلاعات هویتی فیدا (اتباع)',
		'وب‌سرویس احراز هویت تصویری', 'وب‌سرویس تطبیق شماره شبا با کد ملی', 'وب‌سرویس تطبیق شماره کارت با کد ملی',
		'وب‌سرویس استعلام شبا', 'وب‌سرویس تبدیل شماره کارت به شبا', 'وب‌سرویس تبدیل شماره حساب به شبا',
		'سرویس استعلام اعتبار معاملاتی', 'سرویس استعلام پلاک ثبت‌شده', 'سرویس استعلام عدم سوء پیشینه',
	);
}
function uid_render_section_pasvc() {
	$tag       = uid_section_tag( 'pasvc', 'h2' );
	$items_raw = uid_section_val( 'pasvc', 'items', implode( "\n", uid_default_pa_svc() ) );
	$items     = array_values( array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="pa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pasvc', 'eyebrow', __( 'یک قدم جلوتر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pasvc', 'heading', __( 'سرویس‌های مورد نیاز کسب‌وکارها، کنار استعلام کد پستی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pasvc', 'text', __( 'اغلب کسب‌وکارها استعلام آدرس را به‌عنوان نقطه شروع می‌گیرند و بعد سرویس‌های تکمیلی را اضافه می‌کنند. همه از یک قرارداد و یک کلید دسترسی مدیریت می‌شوند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="svc-grid rv" data-rail="svc">
	      <?php foreach ( $items as $i => $label ) : ?>
	      <div class="svc-tile"><span class="n"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span><span><?php echo esc_html( $label ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="svc"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۷) بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_palead() {
	$tag         = uid_section_tag( 'palead', 'h2' );
	$trust_raw   = uid_section_val( 'palead', 'trust', "مشاوره رایگان، بدون تعهد\nدسترسی به محیط سندباکس پیش از قرارداد\nپلن و تخفیف پلکانی متناسب با حجم استعلام شما" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'palead', 'biztype_options', "فروشگاه اینترنتی و تجارت الکترونیک\nلجستیک، پست و حمل‌ونقل\nبانک، فین‌تک یا اعتبارسنجی\nبیمه و خدمات مالی\nخدمات در محل\nسامانه ERP یا CRM\nسایر کسب‌وکارها" );
	$biztypes    = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="pa-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="pa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'palead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'palead', 'heading', __( 'فرم درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'palead', 'text', __( 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس استعلام کد پستی و آدرس، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_pa_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'palead', 'form_title', __( 'درخواست وب‌سرویس استعلام کد پستی', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'palead', 'form_hint', __( 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="postal-address-lp">
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
	            <div class="route-alert" id="routeAlert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	              <span><?php esc_html_e( 'وب‌سرویس استعلام کد پستی فقط برای کسب‌وکارهای مجاز ارائه می‌شود. اگر به‌صورت شخصی به خدمات احراز هویت نیاز دارید، ', 'uid-theme' ); ?><a href="<?php echo esc_url( home_url( '/uid-plus/' ) ); ?>"><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></a> <?php esc_html_e( 'یا', 'uid-theme' ); ?>
	                <a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'گزینه درست شماست.', 'uid-theme' ); ?></span></div>
	            <button class="pa-btn btn-cta pa-btn-block" type="button" data-submit><?php echo uid_pa_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'palead', 'submit_text', __( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_pa_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'palead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_pa_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'palead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'palead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_pa_page_id() {
	$page_id = (int) get_option( 'uid_pa_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_POSTAL_ADDRESS_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_pa_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_pa_page() {
	if ( uid_get_pa_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'استعلام کد پستی و آدرس', 'uid-theme' ),
		'post_name'   => 'address-postcode-docs',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_POSTAL_ADDRESS_TEMPLATE );
	update_option( 'uid_pa_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_pa_page' );
add_action( 'admin_init', 'uid_ensure_pa_page' );

function uid_register_pa_slug_setting() {
	register_setting( 'uid_pa_group', 'uid_pa_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_pa_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_pa_page_slug_section', '', '__return_false', 'uid_pa_layout' );
	add_settings_field( 'uid_pa_page_slug', __( 'آدرس (اسلاگ) صفحه استعلام کد پستی', 'uid-theme' ), 'uid_field_pa_page_slug', 'uid_pa_layout', 'uid_pa_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_pa_slug_setting' );

function uid_field_pa_page_slug( $args ) {
	$page_id = uid_get_pa_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_pa_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_pa_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه استعلام کد پستی هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_pa_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_pa_page_id();

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
function uid_register_pa_settings() {
	register_setting( 'uid_pa_group', 'uid_pa_layout', array(
		'sanitize_callback' => 'uid_sanitize_pa_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_pa_layout_main', '', '__return_false', 'uid_pa_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_pa_layout', 'uid_pa_layout_main', array(
		'option_name' => 'uid_pa_layout', 'registry_fn' => 'uid_pa_sections_registry', 'layout_fn' => 'uid_get_pa_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pahero', array( 'sanitize_callback' => 'uid_sanitize_section_pahero', 'default' => array() ) );
	add_settings_section( 'uid_section_pahero_main', '', '__return_false', 'uid_section_pahero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس استعلام کد پستی و آدرس · inquiry/address/v2' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'heading', 'default' => 'کاربر شما ۸ فیلد نشانی را پر نمی‌کند.<mark>یک کد پستی را می‌کند.</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'text', 'default' => 'وب‌سرویس استعلام آدرس یوآیدی با دریافت کد پستی ۱۰ رقمی، جزئیات محل سکونت کاربر — استان، شهر، محله، معبر اصلی و فرعی و پلاک — را در لحظه بازمی‌گرداند و فرم نشانی شما را خودکار پر می‌کند. ایده‌آل برای ارسال کالا در تجارت الکترونیک، لجستیک و هر فرایندی که نشانی دقیق لازم دارد — با راه‌اندازی و پشتیبانی تیم متخصص یوآیدی.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'btn1_text', 'default' => 'درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pahero', 'uid_section_pahero_main', array( 'group' => 'uid_section_pahero', 'key' => 'tags', 'default' => "استعلام در لحظه\nپایداری ۹۹.۹٪ برای تراکنش بالا\nپوشش سراسری شهری و روستایی" ) );
	add_settings_field( 'samples', __( 'نمونه‌های کد پستی ویجت زنده', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pahero', 'uid_section_pahero_main', array(
		'group' => 'uid_section_pahero', 'key' => 'samples', 'default' => uid_default_pa_samples(), 'add_label' => __( 'افزودن نمونه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'zip', 'type' => 'text', 'label' => __( 'کد پستی', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'province', 'type' => 'text', 'label' => __( 'استان', 'uid-theme' ) ),
			array( 'key' => 'county', 'type' => 'text', 'label' => __( 'شهرستان', 'uid-theme' ) ),
			array( 'key' => 'city', 'type' => 'text', 'label' => __( 'شهر/روستا', 'uid-theme' ) ),
			array( 'key' => 'district', 'type' => 'text', 'label' => __( 'منطقه پستی', 'uid-theme' ) ),
			array( 'key' => 'hood', 'type' => 'text', 'label' => __( 'محله', 'uid-theme' ) ),
			array( 'key' => 'main', 'type' => 'text', 'label' => __( 'معبر اصلی', 'uid-theme' ) ),
			array( 'key' => 'side', 'type' => 'text', 'label' => __( 'معبر فرعی', 'uid-theme' ) ),
			array( 'key' => 'plaque', 'type' => 'text', 'label' => __( 'پلاک', 'uid-theme' ) ),
			array( 'key' => 'unit', 'type' => 'text', 'label' => __( 'طبقه و واحد', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_pahero', 'uid_section_pahero_main', array( 'text' => __( 'ویجت زنده استعلام کد پستی فقط در «نمونه‌های کد پستی» بالا قابل‌ویرایش است؛ منطق شبیه‌سازی پاسخ در assets/js/postal-address-page.js می‌ماند.', 'uid-theme' ) ) );

	/* ---------------- بند فوریت ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_paurgency', array( 'sanitize_callback' => 'uid_sanitize_section_paurgency', 'default' => array() ) );
	add_settings_section( 'uid_section_paurgency_main', '', '__return_false', 'uid_section_paurgency' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_paurgency', 'uid_section_paurgency_main', array( 'group' => 'uid_section_paurgency', 'key' => 'heading', 'default' => 'هر بسته‌ای که به‌خاطر نشانی اشتباه برمی‌گردد، دو بار هزینه ارسال دارد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_paurgency', 'uid_section_paurgency_main', array( 'group' => 'uid_section_paurgency', 'key' => 'text', 'default' => 'تا وقتی نشانی را کاربر دستی تایپ می‌کند، غلط املایی، نام اشتباه کوچه و پلاک نامشخص در پایگاه داده شما ثبت می‌شود — و هزینه‌اش را انبار، پیک و پشتیبانی می‌پردازند. تیم متخصص یوآیدی می‌تواند وب‌سرویس استعلام کد پستی را همین هفته روی پلتفرم شما فعال کند.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_paurgency', 'uid_section_paurgency_main', array( 'group' => 'uid_section_paurgency', 'key' => 'btn1_text', 'default' => 'فعال‌سازی سریع' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (تماس تلفنی)', 'uid-theme' ), 'uid_field_text', 'uid_section_paurgency', 'uid_section_paurgency_main', array( 'group' => 'uid_section_paurgency', 'key' => 'btn2_text', 'default' => 'مشاوره رایگان با کارشناس' ) );

	/* ---------------- مشتریان ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pamarquee', array( 'sanitize_callback' => 'uid_sanitize_section_pamarquee', 'default' => array() ) );
	add_settings_section( 'uid_section_pamarquee_main', '', '__return_false', 'uid_section_pamarquee' );
	add_settings_field( 'heading', __( 'عنوان بالای نوار', 'uid-theme' ), 'uid_field_text', 'uid_section_pamarquee', 'uid_section_pamarquee_main', array( 'group' => 'uid_section_pamarquee', 'key' => 'heading', 'default' => 'کسب‌وکارهایی که از وب‌سرویس‌های استعلامی یوآیدی استفاده می‌کنند' ) );
	add_settings_field( 'items', __( 'فهرست مشتریان', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pamarquee', 'uid_section_pamarquee_main', array(
		'group' => 'uid_section_pamarquee', 'key' => 'items', 'default' => uid_default_pa_marquee(), 'add_label' => __( 'افزودن مشتری', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'letters', 'type' => 'text', 'label' => __( 'حرف/حروف آواتار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'color', 'type' => 'text', 'label' => __( 'رنگ آواتار (کد Hex)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'type', 'type' => 'text', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فرمی که جمع می‌شود ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_paform', array( 'sanitize_callback' => 'uid_sanitize_section_paform', 'default' => array() ) );
	add_settings_section( 'uid_section_paform_main', '', '__return_false', 'uid_section_paform' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'eyebrow', 'default' => 'همان فرم، قبل و بعد از اتصال' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'heading', 'default' => 'فرم نشانی شما، دو حالت دارد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'text', 'default' => 'این تنها تفاوت عملی وب‌سرویس استعلام کد پستی است — و همان چیزی است که کاربر در لحظه پرداخت حس می‌کند. سمت راست وضعیت امروز اکثر فرم‌هاست؛ سمت چپ همان فرم پس از اتصال به api استعلام آدرس یوآیدی.' ) );
	add_settings_field( 'stat_text', __( 'آمار ریزش فرم (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'stat_text', 'default' => 'در داده‌های واقعی سایت یوآیدی، از <b>۵٬۴۹۷</b> کاربری که فرم را شروع کردند، فقط <b>۳٬۰۵۰</b> نفر آن را تمام کردند — <b>۴۴٪</b> ریزش در میانه فرم.' ) );
	add_settings_field( 'cta_heading', __( 'عنوان بند فراخوان پایینی', 'uid-theme' ), 'uid_field_text', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'cta_heading', 'default' => 'کوتاه کردن فرم، ارزان‌ترین راه افزایش نرخ تبدیل است' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند فراخوان پایینی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'cta_text', 'default' => 'کارشناس یوآیدی فرم فعلی شما را بررسی می‌کند و می‌گوید دقیقاً کدام فیلدها با استعلام کد پستی حذف می‌شوند.' ) );
	add_settings_field( 'cta_btn_text', __( 'متن دکمه بند فراخوان (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_paform', 'uid_section_paform_main', array( 'group' => 'uid_section_paform', 'key' => 'cta_btn_text', 'default' => 'بررسی رایگان فرم من' ) );
	add_settings_field( 'form_notice', '', 'uid_field_notice', 'uid_section_paform', 'uid_section_paform_main', array( 'text' => __( 'مقایسه دو فرم و انیمیشن پرشدن خودکار فیلدها، محتوای نمایشی ثابت است و از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/postal-address-page.php مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- ورودی/خروجی API ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_paout', array( 'sanitize_callback' => 'uid_sanitize_section_paout', 'default' => array() ) );
	add_settings_section( 'uid_section_paout_main', '', '__return_false', 'uid_section_paout' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_paout', 'uid_section_paout_main', array( 'group' => 'uid_section_paout', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_paout', 'uid_section_paout_main', array( 'group' => 'uid_section_paout', 'key' => 'eyebrow', 'default' => 'ورودی و خروجی api استعلام آدرس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_paout', 'uid_section_paout_main', array( 'group' => 'uid_section_paout', 'key' => 'heading', 'default' => 'یک عدد می‌فرستید، یک نشانی کامل می‌گیرید' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_paout', 'uid_section_paout_main', array( 'text' => __( 'توضیح و نمونه‌کدهای درخواست/پاسخ، مستندات فنی دقیق‌اند و بخش نمونه‌کد از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/postal-address-page.php مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- نحوه کار سرویس + آمار کوتاه ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pahow', array( 'sanitize_callback' => 'uid_sanitize_section_pahow', 'default' => array() ) );
	add_settings_section( 'uid_section_pahow_main', '', '__return_false', 'uid_section_pahow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pahow', 'uid_section_pahow_main', array( 'group' => 'uid_section_pahow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pahow', 'uid_section_pahow_main', array( 'group' => 'uid_section_pahow', 'key' => 'eyebrow', 'default' => 'مراحل عملیاتی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pahow', 'uid_section_pahow_main', array( 'group' => 'uid_section_pahow', 'key' => 'heading', 'default' => 'استعلام کد پستی چگونه کار می‌کند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pahow', 'uid_section_pahow_main', array( 'group' => 'uid_section_pahow', 'key' => 'text', 'default' => 'کل چرخه، از لحظه‌ای که کاربر ده رقم را تایپ می‌کند تا لحظه‌ای که فرم شما پر می‌شود، سه گام دارد و در کسری از ثانیه اتفاق می‌افتد.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pahow', 'uid_section_pahow_main', array(
		'group' => 'uid_section_pahow', 'key' => 'steps', 'default' => uid_default_pa_flow_how(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'stats', __( 'آمار کوتاه', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pahow', 'uid_section_pahow_main', array(
		'group' => 'uid_section_pahow', 'key' => 'stats', 'default' => uid_default_pa_qstats(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array( array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ), array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ) ),
	) );

	/* ---------------- کاربرد بر اساس صنعت ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pawho', array( 'sanitize_callback' => 'uid_sanitize_section_pawho', 'default' => array() ) );
	add_settings_section( 'uid_section_pawho_main', '', '__return_false', 'uid_section_pawho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'eyebrow', 'default' => 'کاربرد وب‌سرویس بر اساس صنایع' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'heading', 'default' => 'در صنعت شما، استعلام کد پستی چه چیزی را حل می‌کند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'text', 'default' => 'صنعت خودتان را انتخاب کنید تا دقیقاً ببینید این وب‌سرویس در فرآیند شما کجا می‌نشیند و چه هزینه‌ای را حذف می‌کند.' ) );
	add_settings_field( 'items', __( 'کارت‌های صنعت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pawho', 'uid_section_pawho_main', array(
		'group' => 'uid_section_pawho', 'key' => 'items', 'default' => uid_default_pa_who(), 'add_label' => __( 'افزودن صنعت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'shop' => __( 'فروشگاه', 'uid-theme' ), 'logistics' => __( 'لجستیک', 'uid-theme' ), 'fin' => __( 'بانک/فین‌تک', 'uid-theme' ), 'ins' => __( 'بیمه', 'uid-theme' ), 'ondemand' => __( 'خدمات در محل', 'uid-theme' ), 'erp' => __( 'ERP/CRM', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب کوتاه (روی تب)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'win', 'type' => 'text', 'label' => __( 'نتیجه/دستاورد', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند فراخوان پایینی', 'uid-theme' ), 'uid_field_text', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'cta_heading', 'default' => 'صنعت شما در این فهرست نبود؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند فراخوان پایینی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pawho', 'uid_section_pawho_main', array( 'group' => 'uid_section_pawho', 'key' => 'cta_text', 'default' => 'هر فرآیندی که نشانی دقیق لازم دارد، از این سرویس سود می‌برد. کارشناس یوآیدی سناریوی شما را بررسی می‌کند.' ) );

	/* ---------------- ریسک‌ها ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_parisk', array( 'sanitize_callback' => 'uid_sanitize_section_parisk', 'default' => array() ) );
	add_settings_section( 'uid_section_parisk_main', '', '__return_false', 'uid_section_parisk' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_parisk', 'uid_section_parisk_main', array( 'group' => 'uid_section_parisk', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_parisk', 'uid_section_parisk_main', array( 'group' => 'uid_section_parisk', 'key' => 'eyebrow', 'default' => 'مشکلی که این وب‌سرویس حل می‌کند' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_parisk', 'uid_section_parisk_main', array( 'group' => 'uid_section_parisk', 'key' => 'heading', 'default' => 'بدون استعلام کد پستی، این چهار هزینه هر ماه تکرار می‌شود' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_parisk', 'uid_section_parisk_main', array( 'group' => 'uid_section_parisk', 'key' => 'text', 'default' => 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه استعلام نشانی با آن روبه‌رو می‌شوند — و اینکه api استعلام آدرس یوآیدی چطور در همان لحظه جلویشان را می‌گیرد.' ) );
	add_settings_field( 'items', __( 'موارد ریسک', 'uid-theme' ), 'uid_field_repeater', 'uid_section_parisk', 'uid_section_parisk_main', array(
		'group' => 'uid_section_parisk', 'key' => 'items', 'default' => uid_default_pa_risks(), 'add_label' => __( 'افزودن ریسک', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان ریسک', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح ریسک', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'راه‌حل استعلام کد پستی', 'uid-theme' ) ),
		),
	) );

	/* ---------------- محاسبه‌گر هزینه ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_paprice', array( 'sanitize_callback' => 'uid_sanitize_section_paprice', 'default' => array() ) );
	add_settings_section( 'uid_section_paprice_main', '', '__return_false', 'uid_section_paprice' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_paprice', 'uid_section_paprice_main', array( 'group' => 'uid_section_paprice', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_paprice', 'uid_section_paprice_main', array( 'group' => 'uid_section_paprice', 'key' => 'eyebrow', 'default' => 'سازوکار محاسبه هزینه و تعرفه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_paprice', 'uid_section_paprice_main', array( 'group' => 'uid_section_paprice', 'key' => 'heading', 'default' => 'محاسبه‌گر هزینه ماهانه استعلام' ) );
	add_settings_field( 'text', __( 'توضیح (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_paprice', 'uid_section_paprice_main', array( 'group' => 'uid_section_paprice', 'key' => 'text', 'default' => 'هزینه بر اساس <b>تعداد استعلام‌های موفق</b> محاسبه می‌شود؛ استعلام‌های ناموفق یا کدهای پستی فاقد رکورد هزینه‌ای ندارند. هرچه حجم استعلام ماهانه بالاتر باشد، تعرفه هر استعلام پایین‌تر می‌آید. تخمین حجم خود را جابه‌جا کنید تا پلن مناسبتان را ببینید.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_paprice', 'uid_section_paprice_main', array( 'text' => __( 'منطق محاسبه‌گر (پله‌های تخفیف و قیمت پایه) در assets/js/postal-address-page.js تعریف شده و برای انتشار نهایی باید با تعرفه واقعی جایگزین شود؛ از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- باند اعتماد ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_patrust', array( 'sanitize_callback' => 'uid_sanitize_section_patrust', 'default' => array() ) );
	add_settings_section( 'uid_section_patrust_main', '', '__return_false', 'uid_section_patrust' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_patrust', 'uid_section_patrust_main', array( 'group' => 'uid_section_patrust', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_patrust', 'uid_section_patrust_main', array( 'group' => 'uid_section_patrust', 'key' => 'eyebrow', 'default' => 'اعتماد کسب‌وکارها به یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_patrust', 'uid_section_patrust_main', array( 'group' => 'uid_section_patrust', 'key' => 'heading', 'default' => 'یوآیدی، زیرساخت احراز هویت و استعلام مورد اعتماد کسب‌وکارهای ایرانی' ) );
	add_settings_field( 'cells', __( 'آمار (خانه‌های باند اعتماد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_patrust', 'uid_section_patrust_main', array(
		'group' => 'uid_section_patrust', 'key' => 'cells', 'default' => uid_default_pa_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'type', 'type' => 'select', 'label' => __( 'نوع', 'uid-theme' ), 'options' => array( 'text' => __( 'متن ثابت', 'uid-theme' ), 'count' => __( 'عدد شمارشی', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- چرا یوآیدی ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pawhy', array( 'sanitize_callback' => 'uid_sanitize_section_pawhy', 'default' => array() ) );
	add_settings_section( 'uid_section_pawhy_main', '', '__return_false', 'uid_section_pawhy' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pawhy', 'uid_section_pawhy_main', array( 'group' => 'uid_section_pawhy', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pawhy', 'uid_section_pawhy_main', array( 'group' => 'uid_section_pawhy', 'key' => 'eyebrow', 'default' => 'مزایای استفاده از وب‌سرویس کد پستی یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pawhy', 'uid_section_pawhy_main', array( 'group' => 'uid_section_pawhy', 'key' => 'heading', 'default' => 'پنج دلیلی که کسب‌وکارها این سرویس را از یوآیدی می‌گیرند' ) );
	add_settings_field( 'items', __( 'کارت‌های دلیل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pawhy', 'uid_section_pawhy_main', array(
		'group' => 'uid_section_pawhy', 'key' => 'items', 'default' => uid_default_pa_why(), 'add_label' => __( 'افزودن دلیل', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'reduce' => __( 'کاهش خطا', 'uid-theme' ), 'ux' => __( 'تجربه کاربری', 'uid-theme' ), 'logistics' => __( 'لجستیک', 'uid-theme' ), 'dev' => __( 'توسعه', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مراحل راه‌اندازی ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pastart', array( 'sanitize_callback' => 'uid_sanitize_section_pastart', 'default' => array() ) );
	add_settings_section( 'uid_section_pastart_main', '', '__return_false', 'uid_section_pastart' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pastart', 'uid_section_pastart_main', array( 'group' => 'uid_section_pastart', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pastart', 'uid_section_pastart_main', array( 'group' => 'uid_section_pastart', 'key' => 'eyebrow', 'default' => 'مراحل اتصال و پیاده‌سازی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pastart', 'uid_section_pastart_main', array( 'group' => 'uid_section_pastart', 'key' => 'heading', 'default' => 'از تماس تا اولین استعلام، در سه گام' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pastart', 'uid_section_pastart_main', array( 'group' => 'uid_section_pastart', 'key' => 'text', 'default' => 'راه‌اندازی این وب‌سرویس با همراهی تیم متخصص یوآیدی انجام می‌شود؛ از انتخاب پلن تا اولین استعلام موفق در محیط عملیاتی.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pastart', 'uid_section_pastart_main', array(
		'group' => 'uid_section_pastart', 'key' => 'steps', 'default' => uid_default_pa_start(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );

	/* ---------------- تیم متخصص + تعهدنامه ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pateam', array( 'sanitize_callback' => 'uid_sanitize_section_pateam', 'default' => array() ) );
	add_settings_section( 'uid_section_pateam_main', '', '__return_false', 'uid_section_pateam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pateam', 'uid_section_pateam_main', array( 'group' => 'uid_section_pateam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pateam', 'uid_section_pateam_main', array( 'group' => 'uid_section_pateam', 'key' => 'eyebrow', 'default' => 'تیم متخصص یوآیدی کنار شماست' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pateam', 'uid_section_pateam_main', array( 'group' => 'uid_section_pateam', 'key' => 'heading', 'default' => 'شما فقط یک API نمی‌خرید' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pateam', 'uid_section_pateam_main', array(
		'group' => 'uid_section_pateam', 'key' => 'rows', 'default' => uid_default_pa_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'phone' => __( 'تماس', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'report' => __( 'گزارش', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_pateam', 'uid_section_pateam_main', array( 'group' => 'uid_section_pateam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به کسب‌وکار شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pateam', 'uid_section_pateam_main', array( 'group' => 'uid_section_pateam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_pa_pledge_items() ), 'rows' => 4 ) );

	/* ---------------- بند امنیت ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pasecurity', array( 'sanitize_callback' => 'uid_sanitize_section_pasecurity', 'default' => array() ) );
	add_settings_section( 'uid_section_pasecurity_main', '', '__return_false', 'uid_section_pasecurity' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pasecurity', 'uid_section_pasecurity_main', array( 'group' => 'uid_section_pasecurity', 'key' => 'heading', 'default' => 'امنیت داده‌ها، اولویت اول یوآیدی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pasecurity', 'uid_section_pasecurity_main', array( 'group' => 'uid_section_pasecurity', 'key' => 'text', 'default' => 'تمام تبادلات با وب‌سرویس استعلام کد پستی و آدرس از طریق کانال‌های امن (SSL/TLS) انجام می‌شود و داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند. دسترسی به سرویس تنها برای کسب‌وکارهای مجاز و از طریق businessId و businessToken اختصاصی فعال می‌شود.' ) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pafaq', array( 'sanitize_callback' => 'uid_sanitize_section_pafaq', 'default' => array() ) );
	add_settings_section( 'uid_section_pafaq_main', '', '__return_false', 'uid_section_pafaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pafaq', 'uid_section_pafaq_main', array( 'group' => 'uid_section_pafaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pafaq', 'uid_section_pafaq_main', array( 'group' => 'uid_section_pafaq', 'key' => 'eyebrow', 'default' => 'پرسش‌های پرتکرار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pafaq', 'uid_section_pafaq_main', array( 'group' => 'uid_section_pafaq', 'key' => 'heading', 'default' => 'سوالات متداول درباره وب‌سرویس استعلام کد پستی' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pafaq', 'uid_section_pafaq_main', array(
		'group' => 'uid_section_pafaq', 'key' => 'items', 'default' => uid_default_pa_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند فراخوان پایانی', 'uid-theme' ), 'uid_field_text', 'uid_section_pafaq', 'uid_section_pafaq_main', array( 'group' => 'uid_section_pafaq', 'key' => 'cta_heading', 'default' => 'سوال شما اینجا نبود؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند فراخوان پایانی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pafaq', 'uid_section_pafaq_main', array( 'group' => 'uid_section_pafaq', 'key' => 'cta_text', 'default' => 'کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.' ) );

	/* ---------------- سرویس‌های تکمیلی ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_pasvc', array( 'sanitize_callback' => 'uid_sanitize_section_pasvc', 'default' => array() ) );
	add_settings_section( 'uid_section_pasvc_main', '', '__return_false', 'uid_section_pasvc' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pasvc', 'uid_section_pasvc_main', array( 'group' => 'uid_section_pasvc', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pasvc', 'uid_section_pasvc_main', array( 'group' => 'uid_section_pasvc', 'key' => 'eyebrow', 'default' => 'یک قدم جلوتر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pasvc', 'uid_section_pasvc_main', array( 'group' => 'uid_section_pasvc', 'key' => 'heading', 'default' => 'سرویس‌های مورد نیاز کسب‌وکارها، کنار استعلام کد پستی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pasvc', 'uid_section_pasvc_main', array( 'group' => 'uid_section_pasvc', 'key' => 'text', 'default' => 'اغلب کسب‌وکارها استعلام آدرس را به‌عنوان نقطه شروع می‌گیرند و بعد سرویس‌های تکمیلی را اضافه می‌کنند. همه از یک قرارداد و یک کلید دسترسی مدیریت می‌شوند.' ) );
	add_settings_field( 'items', __( 'فهرست سرویس‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pasvc', 'uid_section_pasvc_main', array( 'group' => 'uid_section_pasvc', 'key' => 'items', 'default' => implode( "\n", uid_default_pa_svc() ), 'rows' => 8 ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_pa_group', 'uid_section_palead', array( 'sanitize_callback' => 'uid_sanitize_section_palead', 'default' => array() ) );
	add_settings_section( 'uid_section_palead_main', '', '__return_false', 'uid_section_palead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'heading', 'default' => 'فرم درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس استعلام کد پستی و آدرس، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nدسترسی به محیط سندباکس پیش از قرارداد\nپلن و تخفیف پلکانی متناسب با حجم استعلام شما" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'form_title', 'default' => 'درخواست وب‌سرویس استعلام کد پستی' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'form_hint', 'default' => 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'biztype_options', 'default' => "فروشگاه اینترنتی و تجارت الکترونیک\nلجستیک، پست و حمل‌ونقل\nبانک، فین‌تک یا اعتبارسنجی\nبیمه و خدمات مالی\nخدمات در محل\nسامانه ERP یا CRM\nسایر کسب‌وکارها" ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_palead', 'uid_section_palead_main', array( 'group' => 'uid_section_palead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_pa_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_pahero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'samples'   => uid_sanitize_repeater_rows( $input['samples'] ?? '[]', array(
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'zip', 'type' => 'text', 'required' => true ),
			array( 'key' => 'province', 'type' => 'text' ),
			array( 'key' => 'county', 'type' => 'text' ),
			array( 'key' => 'city', 'type' => 'text' ),
			array( 'key' => 'district', 'type' => 'text' ),
			array( 'key' => 'hood', 'type' => 'text' ),
			array( 'key' => 'main', 'type' => 'text' ),
			array( 'key' => 'side', 'type' => 'text' ),
			array( 'key' => 'plaque', 'type' => 'text' ),
			array( 'key' => 'unit', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_paurgency( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_pamarquee( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'items'   => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'letters', 'type' => 'text' ),
			array( 'key' => 'color', 'type' => 'text' ),
			array( 'key' => 'name', 'type' => 'text', 'required' => true ),
			array( 'key' => 'type', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_paform( $input ) {
	return array(
		'title_tag'    => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => sanitize_text_field( $input['heading'] ?? '' ),
		'text'         => sanitize_textarea_field( $input['text'] ?? '' ),
		'stat_text'    => wp_kses( $input['stat_text'] ?? '', array( 'b' => array() ) ),
		'cta_heading'  => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'     => sanitize_textarea_field( $input['cta_text'] ?? '' ),
		'cta_btn_text' => sanitize_text_field( $input['cta_btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_paout( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
	);
}

function uid_sanitize_section_pahow( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'stats'     => uid_sanitize_repeater_rows( $input['stats'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_pawho( $input ) {
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
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_parisk( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea' ),
			array( 'key' => 'fix_text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_paprice( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => wp_kses( $input['text'] ?? '', array( 'b' => array() ) ),
	);
}

function uid_sanitize_section_patrust( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'cells'     => uid_sanitize_repeater_rows( $input['cells'] ?? '[]', array(
			array( 'key' => 'type', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_pawhy( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_pastart( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_pateam( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'rows'           => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'pledge_heading' => sanitize_text_field( $input['pledge_heading'] ?? '' ),
		'pledge_items'   => sanitize_textarea_field( $input['pledge_items'] ?? '' ),
	);
}

function uid_sanitize_section_pasecurity( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'text'    => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_pafaq( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_pasvc( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
	);
}

function uid_sanitize_section_palead( $input ) {
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
function uid_render_pa_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس استعلام کد پستی و آدرس', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اطلاعات کسب‌وکارتان را بگذارید؛ کارشناس یوآیدی همین امروز تماس می‌گیرد، پلن متناسب با حجم استعلام ماهانه شما را پیشنهاد می‌دهد و دسترسی به محیط سندباکس و کلید API را فعال می‌کند.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'فروشگاه اینترنتی و تجارت الکترونیک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'لجستیک، پست و حمل‌ونقل', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بانک، فین‌تک یا اعتبارسنجی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بیمه و خدمات مالی', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'خدمات در محل', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سامانه ERP یا CRM', 'uid-theme' ); ?></option>
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
