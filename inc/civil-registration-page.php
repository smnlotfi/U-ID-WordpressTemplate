<?php
/**
 * صفحه اختصاصی «وب سرویس ثبت احوال» — دقیقاً همان الگوی صفحه اصلی/یوآیدی‌پلاس:
 * برگه‌ی واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «cr» نام‌گذاری شده‌اند تا با سکشن‌های هم‌نام صفحات دیگر
 * (مثل «stats») در نام آپشن‌های wp_options تداخل نکنند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_CR_TEMPLATE', 'template-civil-registration.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش (دقیقاً مطابق الگوی uid_sections_registry)
 * ===================================================================== */
function uid_cr_sections_registry() {
	return array(
		'crhero'     => array( 'label' => __( 'هیرو + پیش‌نمایش زنده استعلام', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'crurgency'  => array( 'label' => __( 'بند فوریت (هشدار ریسک)', 'uid-theme' ),         'icon' => 'dashicons-warning' ),
		'crmarquee'  => array( 'label' => __( 'مشتریان (نوار متحرک)', 'uid-theme' ),           'icon' => 'dashicons-awards' ),
		'crcode'     => array( 'label' => __( 'ورودی/خروجی API (نمونه‌کد)', 'uid-theme' ),      'icon' => 'dashicons-editor-code' ),
		'crvs'       => array( 'label' => __( 'مقایسه با سرویس شاهکار', 'uid-theme' ),          'icon' => 'dashicons-editor-table' ),
		'crwho'      => array( 'label' => __( 'مناسب چه کسب‌وکارهایی (کارت‌ها)', 'uid-theme' ), 'icon' => 'dashicons-groups' ),
		'crwhy'      => array( 'label' => __( 'چرا یوآیدی (کارت‌ها)', 'uid-theme' ),            'icon' => 'dashicons-star-empty' ),
		'crrisk'     => array( 'label' => __( 'ریسک‌ها و راه‌حل‌ها', 'uid-theme' ),             'icon' => 'dashicons-shield' ),
		'crstart'    => array( 'label' => __( 'مراحل راه‌اندازی (۳ گام)', 'uid-theme' ),        'icon' => 'dashicons-controls-forward' ),
		'crtrust'    => array( 'label' => __( 'باند اعتماد (آمار شرکت)', 'uid-theme' ),         'icon' => 'dashicons-chart-bar' ),
		'crteam'     => array( 'label' => __( 'تیم متخصص + تعهدنامه', 'uid-theme' ),            'icon' => 'dashicons-businessperson' ),
		'crsecurity' => array( 'label' => __( 'بند امنیت داده‌ها', 'uid-theme' ),               'icon' => 'dashicons-lock' ),
		'crfaq'      => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                   'icon' => 'dashicons-editor-help' ),
		'crsvc'      => array( 'label' => __( 'سرویس‌های تکمیلی (کاشی‌ها)', 'uid-theme' ),      'icon' => 'dashicons-grid-view' ),
		'crlead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),            'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_cr_layout() {
	$registry = uid_cr_sections_registry();
	$saved    = get_option( 'uid_cr_layout', array() );

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

function uid_sanitize_cr_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_cr_sections_registry();
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

function uid_render_cr_sections() {
	uid_render_cr_jumpbar();
	foreach ( uid_get_cr_layout() as $row ) {
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
function uid_render_cr_jumpbar() {
	?>
	<div class="jumpbar" id="crJumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jump-rail" id="crJumpRail">
	    <button class="jump-chip cta" type="button" data-open-modal><?php esc_html_e( 'درخواست سرویس', 'uid-theme' ); ?></button>
	    <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	    <button class="jump-chip" type="button" data-jump="#out"><?php esc_html_e( 'خروجی سرویس', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#vs"><?php esc_html_e( 'تفاوت با شاهکار', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#who"><?php esc_html_e( 'مناسب چه کسب‌وکاری', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#why"><?php esc_html_e( 'چرا یوآیدی', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#risk"><?php esc_html_e( 'ریسک‌ها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#start"><?php esc_html_e( 'مراحل راه‌اندازی', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#faq"><?php esc_html_e( 'سوالات متداول', 'uid-theme' ); ?></button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_cr_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_cr_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9.5"/></svg>';
}
function uid_cr_x_icon() {
	return '<svg class="vs-ic n" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_cr_y_icon() {
	return '<svg class="vs-ic y" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_cr_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_cr_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_cr_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v5M12 16h.01"/></svg>';
}

function uid_cr_who_icon_svg( $key ) {
	$icons = array(
		'bank'   => '<path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'crypto' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'legal'  => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}
function uid_cr_why_icon_svg( $key ) {
	$icons = array(
		'doc'      => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'database' => '<ellipse cx="12" cy="5.5" rx="8" ry="2.8"/><path d="M4 5.5v13c0 1.6 3.6 2.8 8 2.8s8-1.2 8-2.8v-13"/><path d="M4 12c0 1.6 3.6 2.8 8 2.8s8-1.2 8-2.8"/>',
		'kyc'      => '<circle cx="9" cy="8" r="4"/><path d="M2 21v-2a4 4 0 014-4h6a4 4 0 014 4v2"/><path d="M17 11l2 2 4-4"/>',
		'bolt'     => '<path d="M13 2L3 14h7l-1 8 11-14h-7z"/>',
		'support'  => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}
function uid_cr_team_icon_svg( $key ) {
	$icons = array(
		'phone'   => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
		'doc'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>',
		'support' => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
	);
	return $icons[ $key ] ?? $icons['phone'];
}

/* =====================================================================
 * ۱) هیرو + پیش‌نمایش زنده استعلام (شبیه‌سازی نمایشی، داده‌های آن هاردکد است)
 * ===================================================================== */
function uid_default_cr_samples() {
	return array(
		array( 'label' => 'نمونه ۱ را بارگذاری کن', 'nid' => '0079145823', 'bd' => '1372/11/02', 'first' => 'سارا', 'last' => 'محمدی', 'father' => 'رضا', 'gender_fa' => 'زن', 'office' => 'اداره ثبت احوال تهران — منطقه ۳' ),
		array( 'label' => 'نمونه ۲', 'nid' => '1288460397', 'bd' => '1365/04/19', 'first' => 'امیرحسین', 'last' => 'کریمی', 'father' => 'مرتضی', 'gender_fa' => 'مرد', 'office' => 'اداره ثبت احوال اصفهان — مرکزی' ),
		array( 'label' => 'نمونه ۳', 'nid' => '2593017468', 'bd' => '1380/08/25', 'first' => 'نگار', 'last' => 'حسینی', 'father' => 'بهرام', 'gender_fa' => 'زن', 'office' => 'اداره ثبت احوال مشهد — ناحیه ۱' ),
	);
}
function uid_cr_samples_json() {
	$rows = uid_section_val( 'crhero', 'samples', uid_default_cr_samples() );
	if ( ! is_array( $rows ) || empty( $rows ) ) $rows = uid_default_cr_samples();
	$out = array();
	foreach ( $rows as $r ) {
		$out[] = array(
			'nid' => $r['nid'] ?? '', 'bd' => $r['bd'] ?? '', 'first' => $r['first'] ?? '', 'last' => $r['last'] ?? '',
			'father' => $r['father'] ?? '', 'genderFa' => $r['gender_fa'] ?? '', 'gender' => ( 'مرد' === ( $r['gender_fa'] ?? '' ) ? 'GENDER_MALE' : 'GENDER_FEMALE' ),
			'office' => $r['office'] ?? '',
		);
	}
	return wp_json_encode( $out );
}
function uid_render_section_crhero() {
	$tag     = uid_section_tag( 'crhero', 'h1' );
	$tags    = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'crhero', 'tags', "پاسخ آنی، در چند ثانیه\nاتصال مستقیم به پایگاه داده سازمان ثبت احوال\nدو پارامتر ورودی، یک پرونده هویتی کامل" ) ) ) );
	$samples = uid_section_val( 'crhero', 'samples', uid_default_cr_samples() );
	if ( ! is_array( $samples ) || empty( $samples ) ) $samples = uid_default_cr_samples();
	?>
	<section class="dark heroA" id="top">
	  <div class="cr-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌ احراز هویت', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'وب سرویس ثبت احوال', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="cr-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'crhero', 'eyebrow', __( 'وب سرویس ثبت احوال · استعلام هویت با شماره ملی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'crhero', 'heading', __( 'کاربری که فقط کد ملی وارد کرده،<mark>هنوز یک هویت تاییدنشده است</mark>.', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'crhero', 'text', __( 'وب سرویس ثبت احوال با دریافت کد ملی و تاریخ تولد، اطلاعات هویتی معتبری مانند نام، نام خانوادگی و نام پدر را به‌صورت آنی استعلام می‌گیرد و در اختیار کسب‌وکار شما قرار می‌دهد. با API استعلام کد ملی، از صحت اطلاعات کاربران خود اطمینان حاصل کنید و ریسک کلاهبرداری را به حداقل برسانید — با راه‌اندازی و پشتیبانی تیم متخصص یوآیدی.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="cr-btn btn-cta" data-open-modal><?php echo uid_cr_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'crhero', 'btn1_text', __( 'درخواست سرویس ثبت احوال', 'uid-theme' ) ) ); ?></button>
	          <a class="cr-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_cr_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_cr_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- پیش‌نمایش زنده استعلام هویتی — شبیه‌سازی نمایشی با داده‌های ساختگی، تماماً تعاملی و کدنویسی‌شده -->
	      <div class="idw rv rv-d2" id="idw">
	        <div class="idw-hd">
	          <span class="live" aria-hidden="true"></span>
	          <b><?php esc_html_e( 'پیش‌نمایش زنده api ثبت احوال', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'نمونه‌ای را همین‌جا اجرا کنید', 'uid-theme' ); ?></span>
	        </div>
	        <div class="idw-fields">
	          <div class="fld"><input id="i_nid" type="text" inputmode="numeric" maxlength="10"
	                placeholder="<?php esc_attr_e( 'کد ملی — ۱۰ رقم', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'کد ملی', 'uid-theme' ); ?>"></div>
	          <div class="fld"><input id="i_bd" type="text" inputmode="numeric" maxlength="10"
	                placeholder="<?php esc_attr_e( 'تاریخ تولد — 1372/11/02', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'تاریخ تولد', 'uid-theme' ); ?>"></div>
	        </div>
	        <div class="idw-chips">
	          <?php foreach ( $samples as $i => $s ) : ?>
	          <button class="idw-chip" type="button" data-sample="<?php echo (int) $i; ?>"><?php echo esc_html( $s['label'] ?? '' ); ?></button>
	          <?php endforeach; ?>
	        </div>
	        <button class="cr-btn btn-cta cr-btn-block" type="button" id="i_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg> <?php esc_html_e( 'اجرای استعلام هویتی', 'uid-theme' ); ?></button>

	        <div class="idcard">
	          <div class="idcard-hd">
	            <span><?php esc_html_e( 'خروجی ', 'uid-theme' ); ?><span class="lat">inquiry/person/v2</span></span>
	            <span class="idbadge wait" id="i_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span>
	          </div>
	          <div class="idgrid" id="i_grid">
	            <div class="idrow"><span class="k">firstName</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">lastName</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">fatherName</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">gender</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">nationalId</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">birthDate</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">deathStatus</span><span class="v mut">—</span></div>
	            <div class="idrow"><span class="k">officeName</span><span class="v mut">—</span></div>
	          </div>
	          <div class="idcard-ft">
	            <span><?php esc_html_e( 'وضعیت پاسخ: ', 'uid-theme' ); ?><span class="mono" id="i_code">—</span></span>
	            <span><?php esc_html_e( 'زمان پاسخ‌دهی: ', 'uid-theme' ); ?><b id="i_ms">—</b></span>
	          </div>
	        </div>

	        <div class="idw-note"><?php echo uid_cr_shield_icon(); ?>
	          <span><?php esc_html_e( 'این یک شبیه‌سازی نمایشی با داده‌های ساختگی است. api واقعی ثبت احوال در محیط عملیاتی شما دقیقاً همین ساختار پاسخ را از پایگاه داده سازمان ثبت احوال بازمی‌گرداند.', 'uid-theme' ); ?></span></div>

	        <div class="idw-lead" id="idwLead">
	          <form novalidate>
	            <p><?php esc_html_e( 'همین خروجی را روی پلتفرم خودتان می‌خواهید؟ شماره‌تان را بگذارید، کارشناس یوآیدی تماس می‌گیرد.', 'uid-theme' ); ?></p>
	            <div class="micro-row">
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                   placeholder="۰۹xxxxxxxxx" data-req data-tel aria-label="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>">
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	              <button class="cr-btn btn-cta" type="button" data-submit><?php esc_html_e( 'تماس بگیرید', 'uid-theme' ); ?></button>
	            </div>
	            <div class="form-ok"><?php echo uid_cr_check_icon(); ?><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
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
 * ۲) بند فوریت (هشدار ریسک)
 * ===================================================================== */
function uid_render_section_crurgency() {
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="cr-wrap">
	    <div class="callband urgent rv">
	      <div class="ic"><?php echo uid_cr_warn_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'crurgency', 'heading', __( 'هر قرارداد، تراکنش یا اعتبارسنجی روی هویت تاییدنشده، یک ریسک باز است', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'crurgency', 'text', __( 'تا وقتی نام، نام خانوادگی و نام پدر کاربر با پایگاه داده ثبت احوال تطبیق داده نشود، کسب‌وکار شما روی اطلاعاتی کار می‌کند که خودِ کاربر وارد کرده است. تیم متخصص یوآیدی می‌تواند وب سرویس ثبت احوال را همین هفته روی پلتفرم شما فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="cr-btn btn-cta cr-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'crurgency', 'btn1_text', __( 'فعال‌سازی سریع', 'uid-theme' ) ) ); ?></button>
	        <a class="cr-btn btn-ghost-d cr-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo esc_html( uid_section_val( 'crurgency', 'btn2_text', __( 'مشاوره رایگان با کارشناس', 'uid-theme' ) ) ); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) مشتریان (نوار متحرک)
 * ===================================================================== */
function uid_default_cr_marquee() {
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
function uid_render_section_crmarquee() {
	$items = uid_section_val( 'crmarquee', 'items', uid_default_cr_marquee() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:36px var(--sec)">
	  <div class="cr-wrap">
	    <p class="tiny rv" style="text-align:center;margin-bottom:18px"><?php echo esc_html( uid_section_val( 'crmarquee', 'heading', __( 'مشتریان سرویس ثبت احوال یوآیدی', 'uid-theme' ) ) ); ?></p>
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
 * ۴) ورودی/خروجی API (متن داینامیک؛ نمونه‌کدهای فنی هاردکد — مطابق سابقه تب فنی PWA)
 * ===================================================================== */
function uid_default_cr_input_fields() {
	return array(
		array( 'field' => 'nationalId', 'desc' => 'کد ملی کاربر' ),
		array( 'field' => 'birthDate', 'desc' => 'تاریخ تولد YYYY/MM/DD' ),
	);
}
function uid_default_cr_output_fields() {
	return array(
		array( 'field' => 'firstName', 'desc' => 'نام' ),
		array( 'field' => 'lastName', 'desc' => 'نام خانوادگی' ),
		array( 'field' => 'fatherName', 'desc' => 'نام پدر' ),
		array( 'field' => 'gender', 'desc' => 'جنسیت' ),
		array( 'field' => 'birthDate', 'desc' => 'تاریخ تولد' ),
		array( 'field' => 'deathStatus', 'desc' => 'وضعیت حیات' ),
		array( 'field' => 'officeName', 'desc' => 'اداره محل صدور' ),
	);
}
function uid_render_section_crcode() {
	$tag = uid_section_tag( 'crcode', 'h2' );
	$in_fields  = uid_section_val( 'crcode', 'input_fields', uid_default_cr_input_fields() );
	$out_fields = uid_section_val( 'crcode', 'output_fields', uid_default_cr_output_fields() );
	?>
	<section class="sec" style="background:var(--n50)" id="out">
	  <div class="cr-wrap">
	    <div class="codepanel rv">
	      <div>
	        <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crcode', 'eyebrow', __( 'ورودی و خروجی api ثبت احوال', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 14px">'; ?><?php echo esc_html( uid_section_val( 'crcode', 'heading', __( 'دو پارامتر می‌فرستید، یک پرونده هویتی کامل می‌گیرید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede"><?php echo esc_html( uid_section_val( 'crcode', 'text', __( 'api استعلام اطلاعات هویتی با کد ملی یوآیدی با یک درخواست ساده و امن کار می‌کند. کسب‌وکار، کد ملی و تاریخ تولد کاربر را به‌عنوان ورودی ارسال می‌کند و سرویس ثبت احوال یوآیدی در پاسخ، یک آبجکت JSON حاوی اطلاعات کامل هویتی فرد را به‌صورت دسته‌بندی‌شده و خوانا بازمی‌گرداند.', 'uid-theme' ) ) ); ?></p>

	        <div class="fmap-io" style="margin-block-start:24px">
	          <div class="fmap-side inp">
	            <span class="lb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg><?php esc_html_e( 'آنچه شما ارسال می‌کنید — ۲ فیلد', 'uid-theme' ); ?></span>
	            <div class="fmap-tags">
	              <?php foreach ( (array) $in_fields as $f ) : ?>
	              <span class="ftag"><code><?php echo esc_html( $f['field'] ?? '' ); ?></code><span><?php echo esc_html( $f['desc'] ?? '' ); ?></span></span>
	              <?php endforeach; ?>
	            </div>
	          </div>
	          <div class="fmap-side out">
	            <span class="lb"><?php echo uid_cr_check_icon(); ?><?php esc_html_e( 'آنچه یوآیدی بازمی‌گرداند', 'uid-theme' ); ?></span>
	            <div class="fmap-tags">
	              <?php foreach ( (array) $out_fields as $f ) : ?>
	              <span class="ftag"><code><?php echo esc_html( $f['field'] ?? '' ); ?></code><span><?php echo esc_html( $f['desc'] ?? '' ); ?></span></span>
	              <?php endforeach; ?>
	            </div>
	          </div>
	        </div>

	        <div class="btn-row" style="margin-block-start:22px">
	          <button class="cr-btn btn-cta" type="button" data-open-modal><?php echo esc_html( uid_section_val( 'crcode', 'btn1_text', __( 'درخواست کلید API', 'uid-theme' ) ) ); ?></button>
	          <a class="cr-btn btn-ghost" href="<?php echo esc_url( uid_section_val( 'crcode', 'btn2_url', '/inquiry-person-docs/' ) ); ?>"><?php echo esc_html( uid_section_val( 'crcode', 'btn2_text', __( 'مستندات فنی کامل', 'uid-theme' ) ) ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
	        </div>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button class="on" type="button" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button type="button" data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" type="button" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
	          <pre class="code-pane on" data-code-pane="req">POST <span class="k">https://json-api.uid.ir/api/inquiry/person/v2</span>
Content-Type: application/json;charset=UTF-8

{
  "requestContext": {
    "apiInfo": {
      "businessId": <span class="m">&lt;UID_BUSINESS_ID&gt;</span>,
      "businessToken": <span class="m">&lt;UID_BUSINESS_TOKEN&gt;</span>
    }
  },
  "nationalId": <span class="s">"0123456789"</span>,
  "birthDate": <span class="s">"1372/11/02"</span>
}</pre>
	          <pre class="code-pane" data-code-pane="res">{
  "basicInformation": {
    "firstName": <span class="m">&lt;FIRST_NAME&gt;</span>,
    "lastName": <span class="m">&lt;LAST_NAME&gt;</span>,
    "fatherName": <span class="m">&lt;FATHER_NAME&gt;</span>,
    "gender": <span class="s">"GENDER_MALE" | "GENDER_FEMALE"</span>
  },
  "identificationInformation": {
    "nationalId": <span class="m">&lt;NATIONAL_CODE&gt;</span>,
    "birthDate": <span class="m">&lt;BIRTH_DATE&gt;</span>
  },
  "registrationStatus": {
    "deathStatus": <span class="s">"DEATH_STATUS_ALIVE"</span>
  },
  "officeInformation": {
    "officeCode": <span class="m">&lt;OFFICE_CODE&gt;</span>,
    "officeName": <span class="m">&lt;OFFICE_NAME&gt;</span>
  },
  "responseContext": {
    "status": { "code": 1, "message": <span class="s">"SUCCESS."</span> }
  }
}</pre>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۵) مقایسه با سرویس شاهکار
 * ===================================================================== */
function uid_default_cr_vs_rows() {
	return array(
		array( 'label' => 'ورودی مورد نیاز', 'c2_type' => 'text', 'c2_text' => 'کد ملی + شماره موبایل', 'c3_type' => 'text', 'c3_text' => 'کد ملی + تاریخ تولد' ),
		array( 'label' => 'خروجی سرویس', 'c2_type' => 'text', 'c2_text' => 'یک پاسخ بله / خیر', 'c3_type' => 'text', 'c3_text' => 'پرونده کامل اطلاعات هویتی' ),
		array( 'label' => 'نام، نام خانوادگی و نام پدر', 'c2_type' => 'no', 'c2_text' => '', 'c3_type' => 'yes', 'c3_text' => '' ),
		array( 'label' => 'وضعیت حیات فرد', 'c2_type' => 'no', 'c2_text' => '', 'c3_type' => 'yes', 'c3_text' => '' ),
		array( 'label' => 'منبع استعلام', 'c2_type' => 'text', 'c2_text' => 'سامانه شاهکار (اپراتورها)', 'c3_type' => 'text', 'c3_text' => 'پایگاه داده سازمان ثبت احوال کشور' ),
		array( 'label' => 'کاربرد اصلی', 'c2_type' => 'text', 'c2_text' => 'تایید مالکیت سیم‌کارت در ثبت‌نام', 'c3_type' => 'text', 'c3_text' => 'تایید قطعی هویت پیش از قرارداد، تراکنش و اعتبارسنجی' ),
	);
}
function uid_render_section_crvs() {
	$tag  = uid_section_tag( 'crvs', 'h2' );
	$rows = uid_section_val( 'crvs', 'rows', uid_default_cr_vs_rows() );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec" id="vs">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'crvs', 'eyebrow', __( 'یک سوال پرتکرار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crvs', 'heading', __( 'تفاوت سرویس ثبت احوال با سرویس شاهکار در یک نگاه', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'crvs', 'text', __( 'سرویس شاهکار صرفاً تطابق مالکیت سیم‌کارت با کد ملی را بررسی می‌کند. اما سرویس ثبت احوال، اطلاعات هویتی کامل فرد را بر اساس کد ملی و تاریخ تولد ارائه می‌دهد. بیشتر کسب‌وکارها در نهایت به هر دو نیاز پیدا می‌کنند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="vs rv">
	      <div class="vs-row hd"><div class="cr-c1"><?php esc_html_e( 'معیار', 'uid-theme' ); ?></div><div class="cr-c2"><?php echo esc_html( uid_section_val( 'crvs', 'col2_label', __( 'وب‌سرویس شاهکار', 'uid-theme' ) ) ); ?></div><div class="cr-c3"><?php echo esc_html( uid_section_val( 'crvs', 'col3_label', __( 'وب سرویس ثبت احوال', 'uid-theme' ) ) ); ?></div></div>
	      <?php foreach ( $rows as $r ) :
	        $c2t = $r['c2_type'] ?? 'text'; $c3t = $r['c3_type'] ?? 'text';
	      ?>
	      <div class="vs-row"><div class="cr-c1"><?php echo esc_html( $r['label'] ?? '' ); ?></div>
	        <div class="cr-c2"><?php
	          if ( 'yes' === $c2t ) { echo uid_cr_y_icon() . esc_html( $r['c2_text'] ?: __( 'ارائه می‌شود', 'uid-theme' ) ); }
	          elseif ( 'no' === $c2t ) { echo uid_cr_x_icon() . esc_html( $r['c2_text'] ?: __( 'ارائه نمی‌شود', 'uid-theme' ) ); }
	          else { echo esc_html( $r['c2_text'] ?? '' ); }
	        ?></div>
	        <div class="cr-c3"><?php
	          if ( 'yes' === $c3t ) { echo uid_cr_y_icon() . esc_html( $r['c3_text'] ?: __( 'ارائه می‌شود', 'uid-theme' ) ); }
	          elseif ( 'no' === $c3t ) { echo uid_cr_x_icon() . esc_html( $r['c3_text'] ?: __( 'ارائه نمی‌شود', 'uid-theme' ) ); }
	          else { echo esc_html( $r['c3_text'] ?? '' ); }
	        ?></div></div>
	      <?php endforeach; ?>
	      <div class="vs-foot">
	        <p><?php echo wp_kses( uid_section_val( 'crvs', 'foot_text', __( 'در عمل، شاهکار <b>لایه اول</b> و ثبت احوال <b>لایه تایید نهایی هویت</b> است. کارشناس یوآیدی کمک می‌کند ترکیب درست این دو را برای فرآیند کسب‌وکارتان انتخاب کنید.', 'uid-theme' ) ), array( 'b' => array() ) ); ?></p>
	        <button class="cr-btn btn-cta cr-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'crvs', 'btn_text', __( 'مشاوره انتخاب سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۶) مناسب چه کسب‌وکارهایی (دک کارت‌ها)
 * ===================================================================== */
function uid_default_cr_who() {
	return array(
		array( 'icon' => 'bank', 'title' => 'فین‌تک، بانک‌ها و مؤسسات مالی', 'text' => 'برای تکمیل فرآیندهای احراز هویت مشتریان (KYC) و مبارزه با پولشویی (AML) — جایی که اشتباه در هویت، مستقیماً به ریسک تنظیم‌گری تبدیل می‌شود.', 'tagline' => 'پرکاربردترین حوزه استفاده' ),
		array( 'icon' => 'shield', 'title' => 'شرکت‌های بیمه و لیزینگ', 'text' => 'جهت تأیید هویت متقاضیان و صدور بیمه‌نامه یا قرارداد؛ پیش از آنکه تعهد مالی بلندمدتی به نام یک هویت تاییدنشده ثبت شود.', 'tagline' => 'پیش از صدور قرارداد' ),
		array( 'icon' => 'crypto', 'title' => 'صرافی‌های ارز دیجیتال', 'text' => 'برای احراز هویت الزامی کاربران و افزایش امنیت پلتفرم — الزامی که بدون اتصال به منبع رسمی، قابل اتکا نیست.', 'tagline' => 'الزام احراز هویت' ),
		array( 'icon' => 'legal', 'title' => 'پلتفرم‌های حقوقی و ثبتی', 'text' => 'برای اطمینان از صحت اطلاعات هویتی طرفین در قراردادهای آنلاین؛ جایی که یک نام اشتباه، کل سند را بی‌اعتبار می‌کند.', 'tagline' => 'اعتبار حقوقی سند' ),
	);
}
function uid_render_section_crwho() {
	$tag   = uid_section_tag( 'crwho', 'h2' );
	$items = uid_section_val( 'crwho', 'items', uid_default_cr_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)" id="who">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crwho', 'eyebrow', __( 'برای چه کسب‌وکارهایی ضروری است', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crwho', 'heading', __( 'چه کسب‌وکارهایی به سرویس ثبت احوال نیاز دارند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'crwho', 'text', __( 'هر کسب‌وکاری که نیازمند تأیید قطعی و کامل هویت کاربران خود برای فرآیندهای حساس مانند قراردادها، تراکنش‌های مالی و اعتبارسنجی است، به api ثبت احوال نیاز دارد. این سرویس برای پلتفرم‌های زیر یک ابزار حیاتی محسوب می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="deck d4" data-deck="who">
	        <?php foreach ( $items as $i => $it ) : ?>
	        <div class="dcard">
	          <div class="ic <?php echo 0 === $i ? 'navy' : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_cr_who_icon_svg( $it['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <?php if ( ! empty( $it['tagline'] ) ) : ?>
	          <span class="tagline"><?php echo uid_cr_check_icon(); ?><?php echo esc_html( $it['tagline'] ); ?></span>
	          <?php endif; ?>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="who">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( 'برای دیدن بقیه، کارت را بکشید یا روی آن ضربه بزنید', 'uid-theme' ); ?></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۷) چرا یوآیدی (دک کارت‌ها)
 * ===================================================================== */
function uid_default_cr_why() {
	return array(
		array( 'icon' => 'doc', 'title' => 'استعلام جامع اطلاعات', 'text' => 'دریافت پکیج کاملی از اطلاعات هویتی شامل نام، نام خانوادگی، نام پدر، جنسیت، تاریخ تولد و وضعیت حیات فرد — نه فقط یک تایید ساده.' ),
		array( 'icon' => 'shield', 'title' => 'کاهش ریسک و جلوگیری از تقلب', 'text' => 'با تأیید هویت افراد، ریسک کلاهبرداری و جعل هویت را به صفر نزدیک کنید و جلوی هزینه‌های بعد از وقوع را از همان ابتدا بگیرید.' ),
		array( 'icon' => 'database', 'title' => 'اتصال به منبع اصلی', 'text' => 'تمام استعلام‌ها به‌صورت مستقیم از پایگاه داده سازمان ثبت احوال کشور انجام می‌شود؛ بدون واسطه و بدون داده‌ی کهنه.' ),
		array( 'icon' => 'kyc', 'title' => 'احراز هویت دقیق (KYC)', 'text' => 'با اتصال به ثبت احوال، بهترین ابزار برای پیاده‌سازی فرآیندهای شناخت مشتری را در اختیار دارید — قابل ارائه به نهاد ناظر.' ),
		array( 'icon' => 'bolt', 'title' => 'پاسخ‌دهی آنی سرویس', 'text' => 'سرویس ثبت احوال یوآیدی با سرعت پاسخ‌دهی در لحظه، تجربه‌ای روان برای کاربر نهایی شما فراهم می‌کند و فرم ثبت‌نام را متوقف نمی‌کند.' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی اختصاصی ۲۴/۷', 'text' => 'تیم پشتیبانی از طریق ایمیل، تلفن و گروه‌های اختصاصی برای هر کسب‌وکار، به‌صورت شبانه‌روزی در دسترس تیم فنی شماست.' ),
	);
}
function uid_render_section_crwhy() {
	$tag   = uid_section_tag( 'crwhy', 'h2' );
	$items = uid_section_val( 'crwhy', 'items', uid_default_cr_why() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="why">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crwhy', 'eyebrow', __( 'چرا سرویس ثبت احوال یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crwhy', 'heading', __( 'شش دلیلی که کسب‌وکارها ثبت احوال را از یوآیدی می‌گیرند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="why">
	        <?php foreach ( $items as $i => $it ) :
	          $cls = 1 === $i ? 'warm' : ( 2 === $i ? 'navy' : '' );
	        ?>
	        <div class="dcard">
	          <div class="ic <?php echo esc_attr( $cls ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_cr_why_icon_svg( $it['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
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
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( '۶ کارت — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۸) ریسک‌ها و راه‌حل‌ها (دک)
 * ===================================================================== */
function uid_default_cr_risks() {
	return array(
		array( 'title' => 'جعل هویت در ثبت‌نام', 'bad_text' => 'کاربر می‌تواند نام و مشخصات فرد دیگری را وارد کند؛ بدون تطبیق با ثبت احوال، سیستم شما راهی برای تشخیص ندارد.', 'fix_text' => 'تطبیق کد ملی و تاریخ تولد با پایگاه رسمی و بازگرداندن نام واقعی فرد، در همان لحظه ثبت‌نام.' ),
		array( 'title' => 'ضعف در KYC و AML', 'bad_text' => 'فرآیند شناخت مشتری بدون اتصال به منبع رسمی، در بازرسی نهاد ناظر قابل دفاع نیست و جریمه‌ساز می‌شود.', 'fix_text' => 'استعلام مستقیم از پایگاه سازمان ثبت احوال، به‌عنوان مبنای مستند فرآیند KYC شما.' ),
		array( 'title' => 'قرارداد به نام فرد فوت‌شده', 'bad_text' => 'در بیمه، لیزینگ و پلتفرم‌های حقوقی، صدور قرارداد یا تسهیلات به نام فردی که در قید حیات نیست، خسارت مستقیم است.', 'fix_text' => 'فیلد deathStatus وضعیت حیات فرد را پیش از صدور هر سند مشخص می‌کند.' ),
		array( 'title' => 'احراز هویت دستی و کند', 'bad_text' => 'بررسی مدارک به‌صورت انسانی، هم پرهزینه است و هم کاربر را در میانه فرم ثبت‌نام منتظر نگه می‌دارد.', 'fix_text' => 'استعلام آنی و خودکار در چند ثانیه، بدون دخالت اپراتور و بدون توقف مسیر کاربر.' ),
	);
}
function uid_render_section_crrisk() {
	$tag   = uid_section_tag( 'crrisk', 'h2' );
	$items = uid_section_val( 'crrisk', 'items', uid_default_cr_risks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)" id="risk">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'crrisk', 'eyebrow', __( 'مشکلی که ثبت احوال حل می‌کند', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crrisk', 'heading', __( 'بدون استعلام هویتی، این چهار ریسک همیشه باز می‌مانند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'crrisk', 'text', __( 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه استعلام ثبت احوال با آن روبه‌رو می‌شوند — و اینکه api ثبت احوال یوآیدی چطور در همان لحظه جلویشان را می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="leak-grid deck d2" data-deck="risk">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="leak">
	        <div class="leak-bad"><span class="tag"><?php echo uid_cr_warn_icon(); ?><?php esc_html_e( 'ریسک رایج', 'uid-theme' ); ?></span>
	          <h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
	          <p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p></div>
	        <div class="leak-fix"><span class="tag"><?php echo uid_cr_check_icon(); ?><?php esc_html_e( 'راه‌حل ثبت احوال', 'uid-theme' ); ?></span>
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
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( '۴ ریسک — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۹) مراحل راه‌اندازی (۳ گام) + آمار کوتاه
 * ===================================================================== */
function uid_default_cr_start_steps() {
	return array(
		array( 'title' => 'تماس و احراز صلاحیت کسب‌وکار', 'text' => 'فرم را پر می‌کنید یا تماس می‌گیرید. کارشناس یوآیدی نوع کاربرد و حجم درخواست‌های شما را بررسی و صلاحیت کسب‌وکار را احراز می‌کند.' ),
		array( 'title' => 'دریافت مستندات و کلید API', 'text' => 'مستندات کامل فنی به‌همراه businessId و businessToken اختصاصی، در اختیار تیم فنی شما قرار می‌گیرد.' ),
		array( 'title' => 'استعلام آنی در محیط عملیاتی', 'text' => 'توسعه‌دهندگان شما سرویس را در هر زبان برنامه‌نویسی پیاده‌سازی می‌کنند و از همان روز، هر ثبت‌نام با استعلام هویتی تایید می‌شود.' ),
	);
}
function uid_default_cr_qstats() {
	return array(
		array( 'value' => 'آنی', 'label' => 'پاسخ استعلام در چند ثانیه، به‌صورت Real-time' ),
		array( 'value' => '۲ فیلد', 'label' => 'تنها ورودی لازم: کد ملی و تاریخ تولد' ),
		array( 'value' => '۲۴/۷', 'label' => 'پشتیبانی ایمیل، تلفن و گروه اختصاصی' ),
		array( 'value' => 'SSL/TLS', 'label' => 'رمزنگاری کامل تمام تبادلات' ),
	);
}
function uid_render_section_crstart() {
	$tag   = uid_section_tag( 'crstart', 'h2' );
	$steps = uid_section_val( 'crstart', 'steps', uid_default_cr_start_steps() );
	$stats = uid_section_val( 'crstart', 'qstats', uid_default_cr_qstats() );
	if ( ! is_array( $steps ) ) $steps = array();
	if ( ! is_array( $stats ) ) $stats = array();
	?>
	<section class="sec" id="start">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crstart', 'eyebrow', __( 'راه‌اندازی با تیم متخصص یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crstart', 'heading', __( 'از تماس تا اولین استعلام، در سه گام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'crstart', 'text', __( 'کافیست از طریق فرم یا شماره‌های موجود در سایت با ما در ارتباط باشید. پس از احراز صلاحیت کسب‌وکار شما، مستندات و کلید دسترسی (API Key) در اختیارتان قرار خواهد گرفت.', 'uid-theme' ) ) ); ?></p>
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
 * ۱۰) باند اعتماد (آمار شرکت)
 * ===================================================================== */
function uid_default_cr_trust() {
	return array(
		array( 'type' => 'count', 'value' => '5000000', 'label' => 'احراز هویت موفق یوآیدی تاکنون' ),
		array( 'type' => 'text', 'value' => 'از ۱۳۹۶', 'label' => 'سابقه فعالیت در احراز هویت آنلاین' ),
		array( 'type' => 'text', 'value' => 'دانش‌بنیان', 'label' => 'شرکت بینش هوشمند نسل پیشرو' ),
		array( 'type' => 'text', 'value' => 'سجام و ثنا', 'label' => 'کارگزار مورد تایید سامانه‌های رسمی' ),
		array( 'type' => 'text', 'value' => '۱۳ سرویس', 'label' => 'وب‌سرویس استعلامی فعال یوآیدی' ),
	);
}
function uid_render_section_crtrust() {
	$tag   = uid_section_tag( 'crtrust', 'h2' );
	$cells = uid_section_val( 'crtrust', 'cells', uid_default_cr_trust() );
	if ( ! is_array( $cells ) || empty( $cells ) ) return;
	?>
	<section class="dark sec">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'crtrust', 'eyebrow', __( 'اعتماد کسب‌وکارها به یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec" style="color:#fff">'; ?><?php echo esc_html( uid_section_val( 'crtrust', 'heading', __( 'یوآیدی، زیرساخت احراز هویت مورد اعتماد کسب‌وکارهای ایرانی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
 * ۱۱) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_cr_team_rows() {
	return array(
		array( 'icon' => 'phone', 'title' => 'مشاوره انتخاب سرویس و پلن', 'text' => 'کارشناس یوآیدی بر اساس فرآیند واقعی کسب‌وکار شما تعیین می‌کند ثبت احوال به‌تنهایی کافی است یا باید با شاهکار و احراز هویت تصویری ترکیب شود' ),
		array( 'icon' => 'doc', 'title' => 'همراهی در یکپارچه‌سازی', 'text' => 'مستندات و کلید دسترسی را می‌دهیم و تا اولین استعلام موفق در محیط عملیاتی، کنار توسعه‌دهندگان شما می‌مانیم' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی ۲۴/۷ با گروه اختصاصی', 'text' => 'برای هر کسب‌وکار یک کانال اختصاصی ساخته می‌شود؛ رفع اشکال و افزایش پلن بدون صف پشتیبانی عمومی' ),
	);
}
function uid_default_cr_pledge_items() {
	return array(
		'هزینه بر اساس تعداد استعلام یا پلن قراردادی، از ابتدا شفاف و مکتوب.',
		'داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند.',
		'تمام تبادلات از کانال امن SSL/TLS انجام می‌شود.',
		'سرویس کاملاً قانونی و از طریق دسترسی‌های تعریف‌شده به منبع اصلی متصل است.',
	);
}
function uid_render_section_crteam() {
	$tag   = uid_section_tag( 'crteam', 'h2' );
	$rows  = uid_section_val( 'crteam', 'rows', uid_default_cr_team_rows() );
	$pledge_raw = uid_section_val( 'crteam', 'pledge_items', implode( "\n", uid_default_cr_pledge_items() ) );
	$pledge = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec">
	  <div class="cr-wrap">
	    <div class="team rv">
	      <div>
	        <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crteam', 'eyebrow', __( 'تیم متخصص یوآیدی کنار شماست', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 20px">'; ?><?php echo esc_html( uid_section_val( 'crteam', 'heading', __( 'شما فقط یک API نمی‌خرید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <div class="team-list" data-rail="team">
	          <?php foreach ( $rows as $r ) : ?>
	          <div class="team-row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_cr_team_icon_svg( $r['icon'] ?? 'phone' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	            <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b><p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div></div>
	          <?php endforeach; ?>
	        </div>
	        <div class="dots" data-dots="team"></div>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.6 5.6 6.1.9-4.4 4.3 1 6.1-5.3-2.8-5.3 2.8 1-6.1L3.3 8.5l6.1-.9z"/></svg></span><b><?php echo esc_html( uid_section_val( 'crteam', 'pledge_heading', __( 'تعهد یوآیدی به کسب‌وکار شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_cr_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div class="btn-row" style="margin-block-start:20px">
	          <a class="cr-btn cr-btn-navy cr-btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_cr_phone_icon(); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۲) بند امنیت داده‌ها
 * ===================================================================== */
function uid_render_section_crsecurity() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="cr-wrap">
	    <div class="secbox rv">
	      <div class="ic"><?php echo uid_cr_shield_icon(); ?></div>
	      <div><b><?php echo esc_html( uid_section_val( 'crsecurity', 'heading', __( 'امنیت داده‌ها، اولویت اول یوآیدی', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'crsecurity', 'text', __( 'تمام تبادلات با وب سرویس ثبت احوال از طریق کانال‌های امن (SSL/TLS) انجام می‌شود و داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند. استفاده از api ثبت احوال کاملاً قانونی است و برای کسب‌وکارهای مجاز، از طریق دسترسی‌های تعریف‌شده به منبع اصلی متصل می‌شود.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۳) سوالات متداول + بند پایانی
 * ===================================================================== */
function uid_default_cr_faq() {
	return array(
		array( 'question' => 'وب ‌سرویس ثبت احوال چیست؟', 'answer' => 'این سرویس یک api است که به شما اجازه می‌دهد از طریق آن، استعلام هویتی ثبت احوال را انجام دهید و با ارسال کد ملی و تاریخ تولد، اطلاعات کامل و تأییدشده فرد را دریافت کنید.' ),
		array( 'question' => 'از طریق سرویس استعلام هویت با شماره ملی چه اطلاعاتی قابل دریافت است؟', 'answer' => 'اطلاعاتی مانند نام، نام خانوادگی، نام پدر، و در برخی موارد جنسیت و وضعیت حیات فرد قابل دریافت است. خروجی در قالب یک آبجکت JSON دسته‌بندی‌شده بازگردانده می‌شود.' ),
		array( 'question' => 'آیا استفاده از api ثبت احوال قانونی و معتبر است؟', 'answer' => 'بله، این سرویس کاملاً قانونی است و برای کسب‌وکارهای مجاز که نیاز به احراز هویت ثبت احوال دارند، از طریق دسترسی‌های تعریف‌شده به منبع اصلی متصل می‌شود.' ),
		array( 'question' => 'تفاوت سرویس ثبت احوال با سرویس شاهکار چیست؟', 'answer' => 'سرویس شاهکار صرفاً تطابق مالکیت سیم‌کارت با کد ملی را بررسی می‌کند. اما سرویس ثبت احوال، اطلاعات هویتی کامل فرد را بر اساس کد ملی و تاریخ تولد ارائه می‌دهد.' ),
		array( 'question' => 'چگونه می‌توانیم api ثبت احوال را دریافت کنیم؟', 'answer' => 'کافیست از طریق فرم یا شماره‌های موجود در سایت با ما در ارتباط باشید. پس از احراز صلاحیت کسب‌وکار شما، مستندات و کلید دسترسی (API Key) در اختیارتان قرار خواهد گرفت.' ),
		array( 'question' => 'فرآیند استعلام هویتی با کد ملی چگونه انجام می‌شود؟', 'answer' => 'شما کد ملی و تاریخ تولد را به سرویس ما ارسال می‌کنید و API یوآیدی به‌صورت آنی این اطلاعات را با پایگاه داده ثبت احوال تطبیق داده و نتیجه را در قالب یک پاسخ استاندارد و خوانا (JSON) بازمی‌گرداند.' ),
		array( 'question' => 'امنیت داده‌ها چگونه تضمین می‌شود؟', 'answer' => 'تمام تبادلات از طریق کانال‌های امن (SSL/TLS) انجام می‌شود و داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند.' ),
		array( 'question' => 'هزینه استفاده از سرویس ثبت احوال یوآیدی چقدر است؟', 'answer' => 'هزینه بر اساس تعداد درخواست‌های استعلام یا پلن‌های قراردادی محاسبه و در قرارداد مشخص می‌شود. برای دریافت قیمت دقیق متناسب با حجم درخواست‌های خود، با کارشناسان یوآیدی تماس بگیرید.' ),
		array( 'question' => 'زمان پاسخگویی سرویس ثبت احوال یوآیدی چقدر است؟', 'answer' => 'معمولاً پاسخ استعلام به‌صورت آنی (Real-time) و در چند ثانیه ارائه می‌شود.' ),
		array( 'question' => 'پشتیبانی سرویس استعلام هویت با شماره ملی یوآیدی به چه صورت است؟', 'answer' => 'تیم پشتیبانی از طریق ایمیل، تلفن و گروه‌های اختصاصی برای هر کسب‌وکار به‌صورت ۲۴/۷ در دسترس است.' ),
	);
}
function uid_render_section_crfaq() {
	$tag   = uid_section_tag( 'crfaq', 'h2' );
	$items = uid_section_val( 'crfaq', 'items', uid_default_cr_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crfaq', 'eyebrow', __( 'پرسش‌های پرتکرار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crfaq', 'heading', __( 'سوالات متداول درباره وب سرویس ثبت احوال', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
	      <div class="ic"><?php echo uid_cr_phone_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'crfaq', 'closing_heading', __( 'سوال شما اینجا نبود؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'crfaq', 'closing_text', __( 'کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="cr-btn btn-cta cr-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="cr-btn btn-ghost-d cr-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'crfaq', 'closing_btn_text', __( 'درخواست تماس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۴) سرویس‌های تکمیلی (کاشی‌ها)
 * ===================================================================== */
function uid_default_cr_svc() {
	return array(
		'وب‌سرویس شاهکار', 'سرویس استعلام اطلاعات هویتی فیدا (اتباع)', 'وب‌سرویس احراز هویت تصویری',
		'وب‌سرویس تطبیق شماره شبا با کد ملی', 'وب‌سرویس تطبیق شماره کارت با کد ملی', 'وب‌سرویس استعلام شبا',
		'وب‌سرویس تبدیل شماره کارت به شبا', 'وب‌سرویس تبدیل شماره حساب به شبا', 'سرویس استعلام آدرس و کد پستی',
		'سرویس استعلام اعتبار معاملاتی', 'سرویس استعلام پلاک ثبت‌شده', 'سرویس استعلام عدم سوء پیشینه',
	);
}
function uid_render_section_crsvc() {
	$tag = uid_section_tag( 'crsvc', 'h2' );
	$items_raw = uid_section_val( 'crsvc', 'items', implode( "\n", uid_default_cr_svc() ) );
	$items = array_values( array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="cr-wrap">
	    <div class="sec-head mid rv">
	      <span class="cr-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'crsvc', 'eyebrow', __( 'یک قدم جلوتر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'crsvc', 'heading', __( 'سرویس‌های مورد نیاز کسب‌وکارها، کنار ثبت احوال', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'crsvc', 'text', __( 'اغلب کسب‌وکارها ثبت احوال را به‌عنوان نقطه شروع می‌گیرند و بعد سرویس‌های تکمیلی را اضافه می‌کنند. همه از یک قرارداد و یک کلید دسترسی مدیریت می‌شوند.', 'uid-theme' ) ) ); ?></p>
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
 * ۱۵) بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_crlead() {
	$tag = uid_section_tag( 'crlead', 'h2' );
	$trust_raw = uid_section_val( 'crlead', 'trust', "مشاوره رایگان، بدون تعهد\nپلن متناسب با حجم استعلام شما\nهمراهی تیم فنی تا اولین استعلام موفق" );
	$trust = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'crlead', 'biztype_options', "fin|فین‌تک، بانک یا مؤسسه مالی\nins|بیمه یا لیزینگ\ncrypto|صرافی ارز دیجیتال\nlegal|پلتفرم حقوقی و ثبتی\nshop|فروشگاه اینترنتی\nother|سایر کسب‌وکارها\nind|کاربر شخصی هستم (کسب‌وکار نیستم)" );
	$biztype_lines = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="cr-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="cr-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'crlead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'crlead', 'heading', __( 'درخواست سرویس ثبت احوال', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'crlead', 'text', __( 'برای دریافت مشاوره رایگان و فعال‌سازی وب سرویس ثبت احوال، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_cr_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'crlead', 'form_title', __( 'فرم درخواست سرویس ثبت احوال', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'crlead', 'form_hint', __( 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="civil-registration-lp">
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
	                <?php foreach ( $biztype_lines as $line ) :
	                  $parts = explode( '|', $line, 2 );
	                  $val   = sanitize_key( trim( $parts[0] ?? '' ) );
	                  $label = trim( $parts[1] ?? $parts[0] ?? '' );
	                  if ( '' === $val ) continue;
	                ?>
	                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
	                <?php endforeach; ?>
	              </select><span class="err"><?php esc_html_e( 'نوع کسب‌وکار را انتخاب کنید.', 'uid-theme' ); ?></span></div>
	            <div class="route-alert" id="routeAlert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	              <span><?php echo wp_kses( uid_section_val( 'crlead', 'route_alert_text', __( 'وب سرویس ثبت احوال فقط برای کسب‌وکارهای مجاز ارائه می‌شود. اگر به‌صورت شخصی به احراز هویت نیاز دارید، <a href="/uid-plus/">یوآیدی‌پلاس</a> یا <a href="/sana/">احراز هویت ثنا</a> گزینه درست شماست.', 'uid-theme' ) ), array( 'a' => array( 'href' => true ) ) ); ?></span></div>
	            <button class="cr-btn btn-cta cr-btn-block" type="button" data-submit><?php echo uid_cr_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'crlead', 'submit_text', __( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_cr_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'crlead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_cr_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'crlead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'crlead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * برگه واقعی خودکارساخته + اسلاگ قابل‌ویرایش — دقیقاً مطابق الگوی pwa-page.php
 * ===================================================================== */
function uid_get_cr_page_id() {
	$page_id = (int) get_option( 'uid_cr_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_CR_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_cr_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_cr_page() {
	if ( uid_get_cr_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'وب سرویس ثبت احوال', 'uid-theme' ),
		'post_name'   => 'api-inquiry-person',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_CR_TEMPLATE );
	update_option( 'uid_cr_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_cr_slug_setting() {
	register_setting( 'uid_cr_group', 'uid_cr_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_cr_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_cr_page_slug_section', '', '__return_false', 'uid_cr_layout' );
	add_settings_field( 'uid_cr_page_slug', __( 'آدرس (اسلاگ) صفحه ثبت احوال', 'uid-theme' ), 'uid_field_cr_page_slug', 'uid_cr_layout', 'uid_cr_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_cr_slug_setting' );

function uid_field_cr_page_slug( $args ) {
	$page_id = uid_get_cr_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_cr_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_cr_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه ثبت احوال هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_cr_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_cr_page_id();

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
 * ثبت تنظیمات پیشخوان برای همه‌ی سکشن‌های این صفحه — دقیقاً مطابق الگوی
 * uid_register_pwa_settings در inc/pwa-page.php
 * ===================================================================== */
function uid_register_cr_settings() {
	register_setting( 'uid_cr_group', 'uid_cr_layout', array(
		'sanitize_callback' => 'uid_sanitize_cr_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_cr_layout_main', '', '__return_false', 'uid_cr_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_cr_layout', 'uid_cr_layout_main', array(
		'option_name' => 'uid_cr_layout', 'registry_fn' => 'uid_cr_sections_registry', 'layout_fn' => 'uid_get_cr_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crhero', array( 'sanitize_callback' => 'uid_sanitize_section_crhero', 'default' => array() ) );
	add_settings_section( 'uid_section_crhero_main', '', '__return_false', 'uid_section_crhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'eyebrow', 'default' => 'وب سرویس ثبت احوال · استعلام هویت با شماره ملی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'heading', 'default' => 'کاربری که فقط کد ملی وارد کرده،<mark>هنوز یک هویت تاییدنشده است</mark>.', 'desc' => __( 'برای برجسته‌کردن بخشی از عنوان می‌توانید آن را داخل <mark>...</mark> بگذارید.', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'text', 'default' => 'وب سرویس ثبت احوال با دریافت کد ملی و تاریخ تولد، اطلاعات هویتی معتبری مانند نام، نام خانوادگی و نام پدر را به‌صورت آنی استعلام می‌گیرد و در اختیار کسب‌وکار شما قرار می‌دهد. با API استعلام کد ملی، از صحت اطلاعات کاربران خود اطمینان حاصل کنید و ریسک کلاهبرداری را به حداقل برسانید — با راه‌اندازی و پشتیبانی تیم متخصص یوآیدی.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'btn1_text', 'default' => 'درخواست سرویس ثبت احوال' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crhero', 'uid_section_crhero_main', array( 'group' => 'uid_section_crhero', 'key' => 'tags', 'default' => "پاسخ آنی، در چند ثانیه\nاتصال مستقیم به پایگاه داده سازمان ثبت احوال\nدو پارامتر ورودی، یک پرونده هویتی کامل" ) );
	add_settings_field( 'samples', __( 'نمونه‌های پیش‌نمایش زنده', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crhero', 'uid_section_crhero_main', array(
		'group' => 'uid_section_crhero', 'key' => 'samples', 'default' => uid_default_cr_samples(), 'add_label' => __( 'افزودن نمونه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'nid', 'type' => 'text', 'label' => __( 'کد ملی', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bd', 'type' => 'text', 'label' => __( 'تاریخ تولد', 'uid-theme' ) ),
			array( 'key' => 'first', 'type' => 'text', 'label' => __( 'نام', 'uid-theme' ) ),
			array( 'key' => 'last', 'type' => 'text', 'label' => __( 'نام خانوادگی', 'uid-theme' ) ),
			array( 'key' => 'father', 'type' => 'text', 'label' => __( 'نام پدر', 'uid-theme' ) ),
			array( 'key' => 'gender_fa', 'type' => 'select', 'label' => __( 'جنسیت', 'uid-theme' ), 'options' => array( 'زن' => __( 'زن', 'uid-theme' ), 'مرد' => __( 'مرد', 'uid-theme' ) ) ),
			array( 'key' => 'office', 'type' => 'text', 'label' => __( 'اداره ثبت احوال', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_crhero', 'uid_section_crhero_main', array( 'text' => __( 'پیش‌نمایش زنده استعلام فقط در «نمونه‌ها» بالا قابل‌ویرایش است؛ منطق شبیه‌سازی پاسخ در assets/js/civil-registration-page.js می‌ماند.', 'uid-theme' ) ) );

	/* ---------------- بند فوریت ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crurgency', array( 'sanitize_callback' => 'uid_sanitize_section_crurgency', 'default' => array() ) );
	add_settings_section( 'uid_section_crurgency_main', '', '__return_false', 'uid_section_crurgency' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crurgency', 'uid_section_crurgency_main', array( 'group' => 'uid_section_crurgency', 'key' => 'heading', 'default' => 'هر قرارداد، تراکنش یا اعتبارسنجی روی هویت تاییدنشده، یک ریسک باز است' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crurgency', 'uid_section_crurgency_main', array( 'group' => 'uid_section_crurgency', 'key' => 'text', 'default' => 'تا وقتی نام، نام خانوادگی و نام پدر کاربر با پایگاه داده ثبت احوال تطبیق داده نشود، کسب‌وکار شما روی اطلاعاتی کار می‌کند که خودِ کاربر وارد کرده است. تیم متخصص یوآیدی می‌تواند وب سرویس ثبت احوال را همین هفته روی پلتفرم شما فعال کند.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_crurgency', 'uid_section_crurgency_main', array( 'group' => 'uid_section_crurgency', 'key' => 'btn1_text', 'default' => 'فعال‌سازی سریع' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (تماس تلفنی)', 'uid-theme' ), 'uid_field_text', 'uid_section_crurgency', 'uid_section_crurgency_main', array( 'group' => 'uid_section_crurgency', 'key' => 'btn2_text', 'default' => 'مشاوره رایگان با کارشناس' ) );

	/* ---------------- مشتریان (نوار متحرک) ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crmarquee', array( 'sanitize_callback' => 'uid_sanitize_section_crmarquee', 'default' => array() ) );
	add_settings_section( 'uid_section_crmarquee_main', '', '__return_false', 'uid_section_crmarquee' );
	add_settings_field( 'heading', __( 'عنوان بالای نوار', 'uid-theme' ), 'uid_field_text', 'uid_section_crmarquee', 'uid_section_crmarquee_main', array( 'group' => 'uid_section_crmarquee', 'key' => 'heading', 'default' => 'مشتریان سرویس ثبت احوال یوآیدی' ) );
	add_settings_field( 'items', __( 'فهرست مشتریان', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crmarquee', 'uid_section_crmarquee_main', array(
		'group' => 'uid_section_crmarquee', 'key' => 'items', 'default' => uid_default_cr_marquee(), 'add_label' => __( 'افزودن مشتری', 'uid-theme' ),
		'desc'  => __( 'هر آیتم به‌صورت یک آواتار حرفی رنگی نمایش داده می‌شود (بدون آپلود تصویر).', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'letters', 'type' => 'text', 'label' => __( 'حرف/حروف آواتار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'color', 'type' => 'text', 'label' => __( 'رنگ آواتار (کد Hex)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'type', 'type' => 'text', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ورودی/خروجی API ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crcode', array( 'sanitize_callback' => 'uid_sanitize_section_crcode', 'default' => array() ) );
	add_settings_section( 'uid_section_crcode_main', '', '__return_false', 'uid_section_crcode' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'eyebrow', 'default' => 'ورودی و خروجی api ثبت احوال' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'heading', 'default' => 'دو پارامتر می‌فرستید، یک پرونده هویتی کامل می‌گیرید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'text', 'default' => 'api استعلام اطلاعات هویتی با کد ملی یوآیدی با یک درخواست ساده و امن کار می‌کند. کسب‌وکار، کد ملی و تاریخ تولد کاربر را به‌عنوان ورودی ارسال می‌کند و سرویس ثبت احوال یوآیدی در پاسخ، یک آبجکت JSON حاوی اطلاعات کامل هویتی فرد را به‌صورت دسته‌بندی‌شده و خوانا بازمی‌گرداند.' ) );
	add_settings_field( 'input_fields', __( 'فیلدهای ورودی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crcode', 'uid_section_crcode_main', array(
		'group' => 'uid_section_crcode', 'key' => 'input_fields', 'default' => uid_default_cr_input_fields(), 'add_label' => __( 'افزودن فیلد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'field', 'type' => 'text', 'label' => __( 'نام فیلد', 'uid-theme' ) ), array( 'key' => 'desc', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'output_fields', __( 'فیلدهای خروجی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crcode', 'uid_section_crcode_main', array(
		'group' => 'uid_section_crcode', 'key' => 'output_fields', 'default' => uid_default_cr_output_fields(), 'add_label' => __( 'افزودن فیلد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'field', 'type' => 'text', 'label' => __( 'نام فیلد', 'uid-theme' ) ), array( 'key' => 'desc', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'btn1_text', 'default' => 'درخواست کلید API' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'btn2_text', 'default' => 'مستندات فنی کامل' ) );
	add_settings_field( 'btn2_url', __( 'لینک دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_crcode', 'uid_section_crcode_main', array( 'group' => 'uid_section_crcode', 'key' => 'btn2_url', 'default' => '/inquiry-person-docs/' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_crcode', 'uid_section_crcode_main', array( 'text' => __( 'نمونه‌کدهای درخواست/پاسخ، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/civil-registration-page.php مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- مقایسه با شاهکار ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crvs', array( 'sanitize_callback' => 'uid_sanitize_section_crvs', 'default' => array() ) );
	add_settings_section( 'uid_section_crvs_main', '', '__return_false', 'uid_section_crvs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'eyebrow', 'default' => 'یک سوال پرتکرار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'heading', 'default' => 'تفاوت سرویس ثبت احوال با سرویس شاهکار در یک نگاه' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'text', 'default' => 'سرویس شاهکار صرفاً تطابق مالکیت سیم‌کارت با کد ملی را بررسی می‌کند. اما سرویس ثبت احوال، اطلاعات هویتی کامل فرد را بر اساس کد ملی و تاریخ تولد ارائه می‌دهد. بیشتر کسب‌وکارها در نهایت به هر دو نیاز پیدا می‌کنند.' ) );
	add_settings_field( 'col2_label', __( 'عنوان ستون ۲', 'uid-theme' ), 'uid_field_text', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'col2_label', 'default' => 'وب‌سرویس شاهکار' ) );
	add_settings_field( 'col3_label', __( 'عنوان ستون ۳', 'uid-theme' ), 'uid_field_text', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'col3_label', 'default' => 'وب سرویس ثبت احوال' ) );
	add_settings_field( 'rows', __( 'ردیف‌های جدول مقایسه', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crvs', 'uid_section_crvs_main', array(
		'group' => 'uid_section_crvs', 'key' => 'rows', 'default' => uid_default_cr_vs_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'desc'  => __( 'برای ردیف‌هایی که فقط بله/خیر دارند، نوع ستون را «بله» یا «خیر» بگذارید و متن را خالی رها کنید تا برچسب پیش‌فرض نمایش داده شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'معیار (ستون ۱)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'c2_type', 'type' => 'select', 'label' => __( 'نوع ستون ۲', 'uid-theme' ), 'options' => array( 'text' => __( 'متن آزاد', 'uid-theme' ), 'yes' => __( 'بله (تیک سبز)', 'uid-theme' ), 'no' => __( 'خیر (ضربدر)', 'uid-theme' ) ) ),
			array( 'key' => 'c2_text', 'type' => 'text', 'label' => __( 'متن ستون ۲', 'uid-theme' ) ),
			array( 'key' => 'c3_type', 'type' => 'select', 'label' => __( 'نوع ستون ۳', 'uid-theme' ), 'options' => array( 'text' => __( 'متن آزاد', 'uid-theme' ), 'yes' => __( 'بله (تیک سبز)', 'uid-theme' ), 'no' => __( 'خیر (ضربدر)', 'uid-theme' ) ) ),
			array( 'key' => 'c3_text', 'type' => 'text', 'label' => __( 'متن ستون ۳', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'یادداشت پایین جدول (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'foot_text', 'default' => 'در عمل، شاهکار <b>لایه اول</b> و ثبت احوال <b>لایه تایید نهایی هویت</b> است. کارشناس یوآیدی کمک می‌کند ترکیب درست این دو را برای فرآیند کسب‌وکارتان انتخاب کنید.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_crvs', 'uid_section_crvs_main', array( 'group' => 'uid_section_crvs', 'key' => 'btn_text', 'default' => 'مشاوره انتخاب سرویس' ) );

	/* ---------------- مناسب چه کسب‌وکارهایی ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crwho', array( 'sanitize_callback' => 'uid_sanitize_section_crwho', 'default' => array() ) );
	add_settings_section( 'uid_section_crwho_main', '', '__return_false', 'uid_section_crwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crwho', 'uid_section_crwho_main', array( 'group' => 'uid_section_crwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crwho', 'uid_section_crwho_main', array( 'group' => 'uid_section_crwho', 'key' => 'eyebrow', 'default' => 'برای چه کسب‌وکارهایی ضروری است' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crwho', 'uid_section_crwho_main', array( 'group' => 'uid_section_crwho', 'key' => 'heading', 'default' => 'چه کسب‌وکارهایی به سرویس ثبت احوال نیاز دارند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crwho', 'uid_section_crwho_main', array( 'group' => 'uid_section_crwho', 'key' => 'text', 'default' => 'هر کسب‌وکاری که نیازمند تأیید قطعی و کامل هویت کاربران خود برای فرآیندهای حساس مانند قراردادها، تراکنش‌های مالی و اعتبارسنجی است، به api ثبت احوال نیاز دارد. این سرویس برای پلتفرم‌های زیر یک ابزار حیاتی محسوب می‌شود.' ) );
	add_settings_field( 'items', __( 'کارت‌های کسب‌وکار', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crwho', 'uid_section_crwho_main', array(
		'group' => 'uid_section_crwho', 'key' => 'items', 'default' => uid_default_cr_who(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'bank' => __( 'بانک/فین‌تک', 'uid-theme' ), 'shield' => __( 'بیمه/اعتماد', 'uid-theme' ), 'crypto' => __( 'صرافی ارز دیجیتال', 'uid-theme' ), 'legal' => __( 'حقوقی/ثبتی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'برچسب پایین کارت (اختیاری)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- چرا یوآیدی ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crwhy', array( 'sanitize_callback' => 'uid_sanitize_section_crwhy', 'default' => array() ) );
	add_settings_section( 'uid_section_crwhy_main', '', '__return_false', 'uid_section_crwhy' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crwhy', 'uid_section_crwhy_main', array( 'group' => 'uid_section_crwhy', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crwhy', 'uid_section_crwhy_main', array( 'group' => 'uid_section_crwhy', 'key' => 'eyebrow', 'default' => 'چرا سرویس ثبت احوال یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crwhy', 'uid_section_crwhy_main', array( 'group' => 'uid_section_crwhy', 'key' => 'heading', 'default' => 'شش دلیلی که کسب‌وکارها ثبت احوال را از یوآیدی می‌گیرند' ) );
	add_settings_field( 'items', __( 'کارت‌های دلیل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crwhy', 'uid_section_crwhy_main', array(
		'group' => 'uid_section_crwhy', 'key' => 'items', 'default' => uid_default_cr_why(), 'add_label' => __( 'افزودن دلیل', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'doc' => __( 'استعلام جامع', 'uid-theme' ), 'shield' => __( 'کاهش ریسک', 'uid-theme' ), 'database' => __( 'منبع اصلی', 'uid-theme' ), 'kyc' => __( 'احراز KYC', 'uid-theme' ), 'bolt' => __( 'پاسخ آنی', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ریسک‌ها ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crrisk', array( 'sanitize_callback' => 'uid_sanitize_section_crrisk', 'default' => array() ) );
	add_settings_section( 'uid_section_crrisk_main', '', '__return_false', 'uid_section_crrisk' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crrisk', 'uid_section_crrisk_main', array( 'group' => 'uid_section_crrisk', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crrisk', 'uid_section_crrisk_main', array( 'group' => 'uid_section_crrisk', 'key' => 'eyebrow', 'default' => 'مشکلی که ثبت احوال حل می‌کند' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crrisk', 'uid_section_crrisk_main', array( 'group' => 'uid_section_crrisk', 'key' => 'heading', 'default' => 'بدون استعلام هویتی، این چهار ریسک همیشه باز می‌مانند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crrisk', 'uid_section_crrisk_main', array( 'group' => 'uid_section_crrisk', 'key' => 'text', 'default' => 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه استعلام ثبت احوال با آن روبه‌رو می‌شوند — و اینکه api ثبت احوال یوآیدی چطور در همان لحظه جلویشان را می‌گیرد.' ) );
	add_settings_field( 'items', __( 'موارد ریسک', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crrisk', 'uid_section_crrisk_main', array(
		'group' => 'uid_section_crrisk', 'key' => 'items', 'default' => uid_default_cr_risks(), 'add_label' => __( 'افزودن ریسک', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان ریسک', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح ریسک', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'راه‌حل ثبت احوال', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مراحل راه‌اندازی ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crstart', array( 'sanitize_callback' => 'uid_sanitize_section_crstart', 'default' => array() ) );
	add_settings_section( 'uid_section_crstart_main', '', '__return_false', 'uid_section_crstart' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crstart', 'uid_section_crstart_main', array( 'group' => 'uid_section_crstart', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crstart', 'uid_section_crstart_main', array( 'group' => 'uid_section_crstart', 'key' => 'eyebrow', 'default' => 'راه‌اندازی با تیم متخصص یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crstart', 'uid_section_crstart_main', array( 'group' => 'uid_section_crstart', 'key' => 'heading', 'default' => 'از تماس تا اولین استعلام، در سه گام' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crstart', 'uid_section_crstart_main', array( 'group' => 'uid_section_crstart', 'key' => 'text', 'default' => 'کافیست از طریق فرم یا شماره‌های موجود در سایت با ما در ارتباط باشید. پس از احراز صلاحیت کسب‌وکار شما، مستندات و کلید دسترسی (API Key) در اختیارتان قرار خواهد گرفت.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crstart', 'uid_section_crstart_main', array(
		'group' => 'uid_section_crstart', 'key' => 'steps', 'default' => uid_default_cr_start_steps(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'desc'  => __( 'شماره هر گام خودکار از روی ترتیب محاسبه می‌شود.', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'qstats', __( 'آمار کوتاه زیر گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crstart', 'uid_section_crstart_main', array(
		'group' => 'uid_section_crstart', 'key' => 'qstats', 'default' => uid_default_cr_qstats(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array( array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ), array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ) ),
	) );

	/* ---------------- باند اعتماد ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crtrust', array( 'sanitize_callback' => 'uid_sanitize_section_crtrust', 'default' => array() ) );
	add_settings_section( 'uid_section_crtrust_main', '', '__return_false', 'uid_section_crtrust' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crtrust', 'uid_section_crtrust_main', array( 'group' => 'uid_section_crtrust', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crtrust', 'uid_section_crtrust_main', array( 'group' => 'uid_section_crtrust', 'key' => 'eyebrow', 'default' => 'اعتماد کسب‌وکارها به یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crtrust', 'uid_section_crtrust_main', array( 'group' => 'uid_section_crtrust', 'key' => 'heading', 'default' => 'یوآیدی، زیرساخت احراز هویت مورد اعتماد کسب‌وکارهای ایرانی' ) );
	add_settings_field( 'cells', __( 'آمار (خانه‌های باند اعتماد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crtrust', 'uid_section_crtrust_main', array(
		'group' => 'uid_section_crtrust', 'key' => 'cells', 'default' => uid_default_cr_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'desc'  => __( 'نوع «عدد شمارشی» با انیمیشن شمارش صعودی نمایش داده می‌شود (فقط رقم بنویسید)؛ نوع «متن ثابت» عیناً همان‌طور که نوشته‌اید نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'type', 'type' => 'select', 'label' => __( 'نوع', 'uid-theme' ), 'options' => array( 'text' => __( 'متن ثابت', 'uid-theme' ), 'count' => __( 'عدد شمارشی', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- تیم متخصص + تعهدنامه ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crteam', array( 'sanitize_callback' => 'uid_sanitize_section_crteam', 'default' => array() ) );
	add_settings_section( 'uid_section_crteam_main', '', '__return_false', 'uid_section_crteam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crteam', 'uid_section_crteam_main', array( 'group' => 'uid_section_crteam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crteam', 'uid_section_crteam_main', array( 'group' => 'uid_section_crteam', 'key' => 'eyebrow', 'default' => 'تیم متخصص یوآیدی کنار شماست' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crteam', 'uid_section_crteam_main', array( 'group' => 'uid_section_crteam', 'key' => 'heading', 'default' => 'شما فقط یک API نمی‌خرید' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crteam', 'uid_section_crteam_main', array(
		'group' => 'uid_section_crteam', 'key' => 'rows', 'default' => uid_default_cr_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'phone' => __( 'تماس', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_crteam', 'uid_section_crteam_main', array( 'group' => 'uid_section_crteam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به کسب‌وکار شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crteam', 'uid_section_crteam_main', array( 'group' => 'uid_section_crteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_cr_pledge_items() ), 'rows' => 5 ) );

	/* ---------------- بند امنیت ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crsecurity', array( 'sanitize_callback' => 'uid_sanitize_section_crsecurity', 'default' => array() ) );
	add_settings_section( 'uid_section_crsecurity_main', '', '__return_false', 'uid_section_crsecurity' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crsecurity', 'uid_section_crsecurity_main', array( 'group' => 'uid_section_crsecurity', 'key' => 'heading', 'default' => 'امنیت داده‌ها، اولویت اول یوآیدی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crsecurity', 'uid_section_crsecurity_main', array( 'group' => 'uid_section_crsecurity', 'key' => 'text', 'default' => 'تمام تبادلات با وب سرویس ثبت احوال از طریق کانال‌های امن (SSL/TLS) انجام می‌شود و داده‌های شخصی مطابق قوانین حفاظت از حریم خصوصی نگهداری می‌شوند. استفاده از api ثبت احوال کاملاً قانونی است و برای کسب‌وکارهای مجاز، از طریق دسترسی‌های تعریف‌شده به منبع اصلی متصل می‌شود.' ) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crfaq', array( 'sanitize_callback' => 'uid_sanitize_section_crfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_crfaq_main', '', '__return_false', 'uid_section_crfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'eyebrow', 'default' => 'پرسش‌های پرتکرار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'heading', 'default' => 'سوالات متداول درباره وب سرویس ثبت احوال' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_crfaq', 'uid_section_crfaq_main', array(
		'group' => 'uid_section_crfaq', 'key' => 'items', 'default' => uid_default_cr_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'closing_heading', __( 'بند پایانی — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'closing_heading', 'default' => 'سوال شما اینجا نبود؟' ) );
	add_settings_field( 'closing_text', __( 'بند پایانی — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'closing_text', 'default' => 'کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'closing_btn_text', __( 'بند پایانی — متن دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_crfaq', 'uid_section_crfaq_main', array( 'group' => 'uid_section_crfaq', 'key' => 'closing_btn_text', 'default' => 'درخواست تماس' ) );

	/* ---------------- سرویس‌های تکمیلی ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crsvc', array( 'sanitize_callback' => 'uid_sanitize_section_crsvc', 'default' => array() ) );
	add_settings_section( 'uid_section_crsvc_main', '', '__return_false', 'uid_section_crsvc' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crsvc', 'uid_section_crsvc_main', array( 'group' => 'uid_section_crsvc', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crsvc', 'uid_section_crsvc_main', array( 'group' => 'uid_section_crsvc', 'key' => 'eyebrow', 'default' => 'یک قدم جلوتر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crsvc', 'uid_section_crsvc_main', array( 'group' => 'uid_section_crsvc', 'key' => 'heading', 'default' => 'سرویس‌های مورد نیاز کسب‌وکارها، کنار ثبت احوال' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crsvc', 'uid_section_crsvc_main', array( 'group' => 'uid_section_crsvc', 'key' => 'text', 'default' => 'اغلب کسب‌وکارها ثبت احوال را به‌عنوان نقطه شروع می‌گیرند و بعد سرویس‌های تکمیلی را اضافه می‌کنند. همه از یک قرارداد و یک کلید دسترسی مدیریت می‌شوند.' ) );
	add_settings_field( 'items', __( 'فهرست سرویس‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crsvc', 'uid_section_crsvc_main', array( 'group' => 'uid_section_crsvc', 'key' => 'items', 'default' => implode( "\n", uid_default_cr_svc() ), 'rows' => 8, 'desc' => __( 'شماره هر کاشی خودکار از روی ترتیب محاسبه می‌شود.', 'uid-theme' ) ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_cr_group', 'uid_section_crlead', array( 'sanitize_callback' => 'uid_sanitize_section_crlead', 'default' => array() ) );
	add_settings_section( 'uid_section_crlead_main', '', '__return_false', 'uid_section_crlead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'heading', 'default' => 'درخواست سرویس ثبت احوال' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان و فعال‌سازی وب سرویس ثبت احوال، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن برای ارائه راهنمایی کامل با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nپلن متناسب با حجم استعلام شما\nهمراهی تیم فنی تا اولین استعلام موفق" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'form_title', 'default' => 'فرم درخواست سرویس ثبت احوال' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'form_hint', 'default' => 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط: کلید|برچسب)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crlead', 'uid_section_crlead_main', array(
		'group' => 'uid_section_crlead', 'key' => 'biztype_options', 'rows' => 8,
		'default' => "fin|فین‌تک، بانک یا مؤسسه مالی\nins|بیمه یا لیزینگ\ncrypto|صرافی ارز دیجیتال\nlegal|پلتفرم حقوقی و ثبتی\nshop|فروشگاه اینترنتی\nother|سایر کسب‌وکارها\nind|کاربر شخصی هستم (کسب‌وکار نیستم)",
		'desc' => __( 'کلید «ind» (کاربر شخصی) پیام هدایت به یوآیدی‌پلاس/ثنا را نمایش می‌دهد؛ فرمت هر خط: کلید‌انگلیسی|برچسب فارسی.', 'uid-theme' ),
	) );
	add_settings_field( 'route_alert_text', __( 'پیام هدایت کاربران شخصی (تگ a مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'route_alert_text', 'default' => 'وب سرویس ثبت احوال فقط برای کسب‌وکارهای مجاز ارائه می‌شود. اگر به‌صورت شخصی به احراز هویت نیاز دارید، <a href="/uid-plus/">یوآیدی‌پلاس</a> یا <a href="/sana/">احراز هویت ثنا</a> گزینه درست شماست.' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_crlead', 'uid_section_crlead_main', array( 'group' => 'uid_section_crlead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_cr_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_crhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'samples'   => uid_sanitize_repeater_rows( $input['samples'] ?? '[]', array(
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'nid', 'type' => 'text', 'required' => true ),
			array( 'key' => 'bd', 'type' => 'text' ),
			array( 'key' => 'first', 'type' => 'text' ),
			array( 'key' => 'last', 'type' => 'text' ),
			array( 'key' => 'father', 'type' => 'text' ),
			array( 'key' => 'gender_fa', 'type' => 'text' ),
			array( 'key' => 'office', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_crurgency( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_crmarquee( $input ) {
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

function uid_sanitize_section_crcode( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'input_fields'  => uid_sanitize_repeater_rows( $input['input_fields'] ?? '[]', array(
			array( 'key' => 'field', 'type' => 'text' ),
			array( 'key' => 'desc', 'type' => 'text' ),
		) ),
		'output_fields' => uid_sanitize_repeater_rows( $input['output_fields'] ?? '[]', array(
			array( 'key' => 'field', 'type' => 'text' ),
			array( 'key' => 'desc', 'type' => 'text' ),
		) ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'btn2_url'  => sanitize_text_field( $input['btn2_url'] ?? '' ),
	);
}

function uid_sanitize_section_crvs( $input ) {
	return array(
		'title_tag'  => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'    => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'    => sanitize_text_field( $input['heading'] ?? '' ),
		'text'       => sanitize_textarea_field( $input['text'] ?? '' ),
		'col2_label' => sanitize_text_field( $input['col2_label'] ?? '' ),
		'col3_label' => sanitize_text_field( $input['col3_label'] ?? '' ),
		'rows'       => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'c2_type', 'type' => 'select', 'options' => array( 'text', 'yes', 'no' ) ),
			array( 'key' => 'c2_text', 'type' => 'text' ),
			array( 'key' => 'c3_type', 'type' => 'select', 'options' => array( 'text', 'yes', 'no' ) ),
			array( 'key' => 'c3_text', 'type' => 'text' ),
		) ),
		'foot_text' => wp_kses( $input['foot_text'] ?? '', array( 'b' => array() ) ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_crwho( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'select', 'options' => array( 'bank', 'shield', 'crypto', 'legal' ) ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'tagline', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_crwhy( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'select', 'options' => array( 'doc', 'shield', 'database', 'kyc', 'bolt', 'support' ) ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_crrisk( $input ) {
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

function uid_sanitize_section_crstart( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'qstats'    => uid_sanitize_repeater_rows( $input['qstats'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_crtrust( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'cells'     => uid_sanitize_repeater_rows( $input['cells'] ?? '[]', array(
			array( 'key' => 'type', 'type' => 'select', 'options' => array( 'text', 'count' ) ),
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_crteam( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'rows'           => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'select', 'options' => array( 'phone', 'doc', 'support' ) ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'pledge_heading' => sanitize_text_field( $input['pledge_heading'] ?? '' ),
		'pledge_items'   => sanitize_textarea_field( $input['pledge_items'] ?? '' ),
	);
}

function uid_sanitize_section_crsecurity( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'text'    => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_crfaq( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'items'            => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
		'closing_heading'  => sanitize_text_field( $input['closing_heading'] ?? '' ),
		'closing_text'     => sanitize_textarea_field( $input['closing_text'] ?? '' ),
		'closing_btn_text' => sanitize_text_field( $input['closing_btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_crsvc( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
	);
}

function uid_sanitize_section_crlead( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'text'             => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'            => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'       => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'        => sanitize_text_field( $input['form_hint'] ?? '' ),
		'biztype_options'  => sanitize_textarea_field( $input['biztype_options'] ?? '' ),
		'route_alert_text' => wp_kses( $input['route_alert_text'] ?? '', array( 'a' => array( 'href' => true ) ) ),
		'submit_text'      => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'        => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title'    => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'     => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_cr_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب سرویس ثبت احوال', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اطلاعات کسب‌وکارتان را بگذارید؛ کارشناس یوآیدی همین امروز تماس می‌گیرد، پلن مناسب حجم استعلام شما را پیشنهاد می‌دهد و کلید دسترسی API را فعال می‌کند.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'فین‌تک، بانک یا مؤسسه مالی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بیمه یا لیزینگ', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'صرافی ارز دیجیتال', 'uid-theme' ); ?></option><option><?php esc_html_e( 'پلتفرم حقوقی و ثبتی', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'فروشگاه اینترنتی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سایر کسب‌وکارها', 'uid-theme' ); ?></option>
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
