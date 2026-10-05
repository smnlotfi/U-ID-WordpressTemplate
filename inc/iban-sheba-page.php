<?php
/**
 * صفحه اختصاصی «استعلام شبا» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «ib» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * ۹ سکشن این صفحه (out/vs/how/who/risk/team/dev/xsell/faq) سیستم «فصل تاخوردنی»
 * موبایل دارند (دقیقاً مثل inc/ekyc-liveness-page.php) — نگاه کنید به
 * uid_ib_folded_slugs()/uid_render_ib_foldbar().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_IBAN_SHEBA_TEMPLATE', 'template-iban-sheba.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_ib_sections_registry() {
	return array(
		'ibhero'     => array( 'label' => __( 'هیرو + پیش‌نمایش زنده استعلام شبا', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'ibtrust'    => array( 'label' => __( 'باند اعتماد کوتاه (۴ آمار)', 'uid-theme' ),          'icon' => 'dashicons-chart-bar' ),
		'ibloss'     => array( 'label' => __( 'محاسبه‌گر زیان واریز اشتباه', 'uid-theme' ),          'icon' => 'dashicons-calculator' ),
		'ibstart'    => array( 'label' => __( 'مسیر اتصال (۳ گام)', 'uid-theme' ),                  'icon' => 'dashicons-controls-forward' ),
		'ibout'      => array( 'label' => __( 'فصل ۱ — ورودی/خروجی سرویس', 'uid-theme' ),           'icon' => 'dashicons-editor-code' ),
		'ibvs'       => array( 'label' => __( 'فصل ۲ — الگوریتم در برابر استعلام واقعی', 'uid-theme' ), 'icon' => 'dashicons-image-flip-horizontal' ),
		'ibhow'      => array( 'label' => __( 'فصل ۳ — نحوه کار سرویس', 'uid-theme' ),              'icon' => 'dashicons-randomize' ),
		'ibwho'      => array( 'label' => __( 'فصل ۴ — کاربردها (انتخابگر صنعت)', 'uid-theme' ),    'icon' => 'dashicons-groups' ),
		'ibrisk'     => array( 'label' => __( 'فصل ۵ — چهار نشتی و راه‌حل', 'uid-theme' ),          'icon' => 'dashicons-shield' ),
		'ibteam'     => array( 'label' => __( 'فصل ۶ — تیم متخصص + تعهدنامه', 'uid-theme' ),        'icon' => 'dashicons-businessperson' ),
		'ibdev'      => array( 'label' => __( 'فصل ۷ — مستندات فنی و نمونه‌کد', 'uid-theme' ),      'icon' => 'dashicons-editor-code' ),
		'ibxsell'    => array( 'label' => __( 'فصل ۸ — سرویس‌های مکمل', 'uid-theme' ),              'icon' => 'dashicons-networking' ),
		'ibfaq'      => array( 'label' => __( 'فصل ۹ — سوالات متداول', 'uid-theme' ),               'icon' => 'dashicons-editor-help' ),
		'ibadv'      => array( 'label' => __( 'مزایای رقابتی (پنل تیره)', 'uid-theme' ),            'icon' => 'dashicons-awards' ),
		'ibbanks'    => array( 'label' => __( 'پوشش بانک‌ها', 'uid-theme' ),                        'icon' => 'dashicons-bank' ),
		'ibprice'    => array( 'label' => __( 'محاسبه‌گر هزینه ماهانه', 'uid-theme' ),               'icon' => 'dashicons-money-alt' ),
		'iblastcall' => array( 'label' => __( 'بند تماس پایانی', 'uid-theme' ),                     'icon' => 'dashicons-megaphone' ),
		'iblead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_ib_layout() {
	$registry = uid_ib_sections_registry();
	$saved    = get_option( 'uid_ib_layout', array() );

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

function uid_sanitize_ib_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_ib_sections_registry();
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
function uid_ib_folded_slugs() {
	return array( 'ibout', 'ibvs', 'ibhow', 'ibwho', 'ibrisk', 'ibteam', 'ibdev', 'ibxsell', 'ibfaq' );
}

function uid_render_ib_sections() {
	$folded    = uid_ib_folded_slugs();
	$layout    = uid_get_ib_layout();
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
			uid_render_ib_foldbar( $folded_on );
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
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود.
 */
function uid_render_ib_foldbar( $count ) {
	?>
	<div class="foldbar" id="foldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <button type="button" id="foldAll"><?php esc_html_e( 'باز کردن همه', 'uid-theme' ); ?></button>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_ib_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_ib_x_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_ib_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg>';
}
function uid_ib_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_ib_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_ib_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
function uid_ib_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg>';
}
function uid_ib_who_icon_svg( $key ) {
	$icons = array(
		'crypto'  => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'market'  => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 01-8 0"/>',
		'refund'  => '<path d="M3 12a9 9 0 109-9"/><path d="M3 3v6h6"/><path d="M12 7v5l3 2"/>',
		'lend'    => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'ins'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'payroll' => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9"/>',
	);
	return $icons[ $key ] ?? $icons['crypto'];
}
function uid_ib_who_color( $key ) {
	$colors = array( 'crypto' => 'warm', 'market' => '', 'refund' => 'navy', 'lend' => '', 'ins' => 'navy', 'payroll' => 'warm' );
	return $colors[ $key ] ?? '';
}
function uid_ib_how_icon_svg( $key ) {
	$icons = array(
		'send'  => '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/>',
		'bank'  => '<path d="M3 21h18M5 21V8l7-5 7 5v13"/><path d="M9 21v-6h6v6"/>',
		'reply' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	);
	return $icons[ $key ] ?? $icons['send'];
}
function uid_ib_how_color( $key ) {
	$colors = array( 'send' => 'navy', 'bank' => '', 'reply' => 'warm' );
	return $colors[ $key ] ?? '';
}
function uid_ib_team_icon_svg( $key ) {
	$icons = array(
		'connect' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'doc'     => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/>',
		'report'  => '<path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['connect'];
}
function uid_ib_xsell_icon_svg( $key ) {
	$icons = array(
		'card'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
		'match'  => '<path d="M20 6L9 17l-5-5"/><circle cx="12" cy="12" r="9.5" opacity=".35"/>',
		'cardid' => '<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="12" r="2.2"/><path d="M13 11h5M13 15h3"/>',
	);
	return $icons[ $key ] ?? $icons['card'];
}
function uid_ib_adv_icon_svg( $key ) {
	$icons = array(
		'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'redundant' => '<path d="M4 6h16M4 12h16M4 18h16"/><circle cx="8" cy="6" r="2" fill="currentColor" stroke="none"/><circle cx="16" cy="12" r="2" fill="currentColor" stroke="none"/><circle cx="10" cy="18" r="2" fill="currentColor" stroke="none"/>',
		'fast'      => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'report'    => '<path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/>',
		'lock'      => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 018 0v3"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}

/* =====================================================================
 * ۱) هیرو + پیش‌نمایش زنده استعلام شبا (شبیه‌سازی نمایشی، داده آن هاردکد است)
 * ===================================================================== */
function uid_default_ib_samples() {
	return array(
		array( 'iban' => '820540102680020817909002', 'label' => 'نمونه حقیقی — پارسیان', 'blocked' => '' ),
		array( 'iban' => '120620000000203608707002', 'label' => 'نمونه حقوقی — آینده', 'blocked' => '' ),
		array( 'iban' => '190570028180010930000101', 'label' => 'نمونه حساب مسدود', 'blocked' => '1' ),
	);
}
function uid_render_section_ibhero() {
	$tag     = uid_section_tag( 'ibhero', 'h1' );
	$tags    = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ibhero', 'tags', "پاسخ لحظه‌ای\nهزینه فقط بابت استعلام موفق\nاتصال در کمتر از یک روز کاری" ) ) ) );
	$samples = uid_section_val( 'ibhero', 'samples', uid_default_ib_samples() );
	if ( ! is_array( $samples ) || empty( $samples ) ) $samples = uid_default_ib_samples();
	?>
	<section class="dark heroA" id="top">
	  <div class="ib-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌ احراز هویت', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'وب‌سرویس استعلام شبا', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="ib-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ibhero', 'eyebrow', __( 'وب‌سرویس استعلام اطلاعات مالی (شبا) · inquiry/iban/v2', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'ibhero', 'heading', __( 'هر واریز به شبای اشتباه،<mark>دو بار از جیب شما می‌رود.</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'ibhero', 'text', __( 'یک‌بار بابت پول بلوکه‌شده و پیگیری بانکی، یک‌بار بابت اعتمادی که از کاربر گرفته‌اید. وب‌سرویس استعلام شبای یوآیدی با دریافت شماره شبا، نام و نام خانوادگی صاحب حساب، کد ملی، شماره حساب، نام بانک و وضعیت فعال یا مسدود بودن حساب را در لحظه از شبکه بانکی برمی‌گرداند — پیش از آنکه ریالی جابه‌جا شود.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="ib-btn btn-cta" data-open-modal><?php echo uid_ib_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ibhero', 'btn1_text', __( 'درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ib-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ib_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_ib_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- live inquiry widget: real request shape, real response shape -->
	      <div class="idw rv rv-d2" id="idw">
	        <div class="idw-hd"><span class="live"></span><b><?php esc_html_e( 'استعلام تستی شبا', 'uid-theme' ); ?></b>
	          <span class="mono">POST inquiry/iban/v2</span></div>

	        <div class="ibfield" id="ib_field">
	          <span class="pre">IR</span>
	          <input id="ib_code" type="text" inputmode="numeric" maxlength="24"
	                 placeholder="<?php esc_attr_e( '۲۴ رقم شبا را وارد کنید', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'شماره شبا بدون پیشوند IR', 'uid-theme' ); ?>"
	                 autocomplete="off" spellcheck="false">
	          <span class="cnt" id="ib_cnt">۰ / ۲۴</span>
	          <span class="bar"><i id="ib_bar"></i></span>
	        </div>

	        <div class="ibsamples" id="ib_samples">
	          <?php foreach ( $samples as $s ) :
	            $digits = preg_replace( '/\D/', '', $s['iban'] ?? '' );
	          ?>
	          <button class="ibsample<?php echo ! empty( $s['blocked'] ) ? ' blocked' : ''; ?>" type="button" data-iban="<?php echo esc_attr( $digits ); ?>"><i></i><?php echo esc_html( $s['label'] ?? '' ); ?></button>
	          <?php endforeach; ?>
	        </div>

	        <button class="ib-btn btn-cta ib-btn-block" type="button" id="ib_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg> <?php esc_html_e( 'اجرای استعلام تستی', 'uid-theme' ); ?></button>

	        <div style="margin-block-start:16px">
	          <div class="rswitch">
	            <button type="button" class="on" data-rview="g"><?php esc_html_e( 'نمای ساختاری', 'uid-theme' ); ?></button>
	            <button type="button" data-rview="j"><?php esc_html_e( 'خروجی خام', 'uid-theme' ); ?> <span class="lat">JSON</span></button>
	            <span class="idbadge wait" id="ib_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span>
	          </div>

	          <div data-rpane="g" class="on">
	            <div class="idgrid" id="i_grid">
	              <div class="idrow"><span class="k"><?php esc_html_e( 'نام صاحب حساب', 'uid-theme' ); ?></span><span class="v fa mut" id="ib_fn">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'نام خانوادگی', 'uid-theme' ); ?></span><span class="v fa mut" id="ib_ln">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'کد ملی صاحب حساب', 'uid-theme' ); ?></span><span class="v mut" id="ib_nid">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'نام بانک', 'uid-theme' ); ?></span><span class="v fa mut" id="ib_bank">—</span></div>
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'شماره حساب', 'uid-theme' ); ?></span><span class="v mut" id="ib_acc">—</span></div>
	              <div class="idrow hl"><span class="k"><?php esc_html_e( 'وضعیت حساب', 'uid-theme' ); ?></span><span class="v mut" id="ib_st">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'نوع مشتری', 'uid-theme' ); ?></span><span class="v mut" id="ib_ct">—</span></div>
	            </div>
	          </div>

	          <div data-rpane="j">
	            <pre class="rjson" id="ib_json">{
  "responseContext": { "status": { "code": 0, "message": "SUCCESS." } },
  "accountBasicInformation": { … },
  "accountStatus": "…",
  "owners": [ { … } ]
}</pre>
	          </div>

	          <div class="idmeta">
	            <span><?php esc_html_e( 'وضعیت پاسخ: ', 'uid-theme' ); ?><span class="mono" id="ib_status">—</span></span>
	            <span><?php esc_html_e( 'زمان پاسخ‌دهی: ', 'uid-theme' ); ?><b id="ib_ms">—</b></span>
	          </div>
	        </div>

	        <div class="idw-note"><?php echo uid_ib_shield_icon(); ?>
	          <span><?php esc_html_e( 'این نمایش با داده نمونه کار می‌کند و صرفاً ساختار واقعی پاسخ سرویس را نشان می‌دهد. برای استعلام واقعی، کلید API و دسترسی سندباکس لازم است.', 'uid-theme' ); ?></span></div>

	        <!-- highest-intent moment on the page: a single field, right here -->
	        <div class="idw-lead" id="idwLead">
	          <p><?php esc_html_e( 'پاسخ را دیدید؟ همین ساختار را روی داده واقعی می‌خواهید؟ شماره‌تان را بگذارید، کارشناس یوآیدی دسترسی سندباکس را برایتان فعال می‌کند.', 'uid-theme' ); ?></p>
	          <form class="micro" id="microForm" novalidate>
	            <div class="micro-fields">
	              <input type="hidden" name="source" value="iban-lp-hero">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ib-btn btn-cta" type="button" data-submit><?php esc_html_e( 'دریافت دسترسی تست', 'uid-theme' ); ?></button>
	              </div>
	            </div>
	            <div class="form-ok"><?php echo uid_ib_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	              <span><?php esc_html_e( 'کارشناس یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۲) باند اعتماد کوتاه (۴ آمار)
 * ===================================================================== */
function uid_default_ib_trust() {
	return array(
		array( 'value' => '۷', 'label' => 'فیلد اطلاعاتی که با یک شماره شبا برمی‌گردد', 'numeric' => '1' ),
		array( 'value' => 'لحظه‌ای', 'label' => 'پاسخ سرویس، بدون صف و بدون انتظار', 'numeric' => '' ),
		array( 'value' => 'فقط موفق', 'label' => 'استعلام ناموفق یا اشتباه هزینه‌ای ندارد', 'numeric' => '' ),
		array( 'value' => '۱۳۹۶', 'label' => 'سال شروع فعالیت یوآیدی در احراز هویت آنلاین', 'numeric' => '1' ),
	);
}
function uid_render_section_ibtrust() {
	$items = uid_section_val( 'ibtrust', 'items', uid_default_ib_trust() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="ib-wrap">
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
 * ۳) محاسبه‌گر زیان واریز اشتباه (LOSS ENGINE) — منطق محاسبه در JS هاردکد است
 * ===================================================================== */
function uid_default_ib_loss_notes() {
	return array(
		array( 'heading' => 'درست بودن ساختار شبا، هیچ چیزی را تضمین نمی‌کند', 'text' => 'الگوریتم IBAN فقط می‌گوید ۲۴ رقم شما از نظر ریاضی معتبر است. نمی‌گوید حساب باز است، مسدود نیست، یا اصلاً متعلق به همان کسی است که ادعا می‌کند.' ),
		array( 'heading' => 'یک فراخوانی POST، پیش از هر تراکنش', 'text' => 'وب‌سرویس یوآیدی وضعیت لحظه‌ای حساب و هویت دارنده شبا را از شبکه بانکی می‌گیرد. اگر حساب مسدود بود یا نام صاحب حساب با کاربر شما نمی‌خواند، پول اصلاً حرکت نمی‌کند.' ),
	);
}
function uid_render_section_ibloss() {
	$tag   = uid_section_tag( 'ibloss', 'h2' );
	$notes = uid_section_val( 'ibloss', 'notes', uid_default_ib_loss_notes() );
	if ( ! is_array( $notes ) ) $notes = array();
	?>
	<section class="dark sec" id="loss">
	  <div class="ib-wrap">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ibloss', 'eyebrow', __( 'قبل از اینکه ادامه بدهید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibloss', 'heading', __( 'شبای اشتباه، ماهانه چقدر از شما می‌گیرد؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibloss', 'text', __( 'اعداد خودتان را وارد کنید. ما فقط حساب می‌کنیم. هر واریز ناموفق یعنی پول بلوکه، یک تیکت پشتیبانی، یک پیگیری بانکی و یک کاربر ناراضی.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="cs rv" style="align-items:stretch">
	      <div class="engine">
	        <div class="engine-hd"><span class="live"></span><b><?php esc_html_e( 'محاسبه‌گر زیان واریز اشتباه', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'اعداد شما', 'uid-theme' ); ?></span></div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_tx"><?php esc_html_e( 'تعداد واریز، تسویه یا بازپرداخت در ماه', 'uid-theme' ); ?></label>
	            <output id="ls_tx_v">۱۲٬۰۰۰</output></div>
	          <input id="ls_tx" type="range" min="100" max="200000" step="100" value="12000">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_rate"><?php esc_html_e( 'درصد شبای اشتباه، مسدود یا متعلق به شخص دیگر', 'uid-theme' ); ?></label>
	            <output id="ls_rate_v">۲٫۵<small><?php esc_html_e( 'درصد', 'uid-theme' ); ?></small></output></div>
	          <input id="ls_rate" type="range" min="2" max="120" step="1" value="25">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_cost"><?php esc_html_e( 'هزینه میانگین پیگیری هر واریز ناموفق (تومان)', 'uid-theme' ); ?></label>
	            <output id="ls_cost_v">۲۵۰٬۰۰۰</output></div>
	          <input id="ls_cost" type="range" min="20000" max="2000000" step="10000" value="250000">
	        </div>
	        <p class="eng-hint"><?php esc_html_e( 'شامل زمان پشتیبانی مالی، مکاتبه با بانک، مغایرت‌گیری، بلوکه ماندن وجه و ریسک از دست دادن کاربر.', 'uid-theme' ); ?></p>

	        <div class="eng-out">
	          <div class="eng-cell bad"><span class="k"><?php esc_html_e( 'واریز ناموفق در ماه', 'uid-theme' ); ?></span>
	            <span class="v" id="ls_fail">۳۰۰</span></div>
	          <div class="eng-cell good"><span class="k"><?php esc_html_e( 'هزینه استعلام همان تعداد شبا', 'uid-theme' ); ?></span>
	            <span class="v amt"><span class="num" id="ls_api">—</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span></div>
	        </div>
	        <div class="eng-total">
	          <span class="k"><?php esc_html_e( 'زیان ماهانه شما بدون استعلام شبا', 'uid-theme' ); ?></span>
	          <span class="v amt"><span class="num" id="ls_total">—</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span>
	        </div>
	        <div class="eng-daily"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 7v5l3.2 2"/></svg>
	          <span><?php esc_html_e( 'یعنی روزانه حدود ', 'uid-theme' ); ?><b id="ls_daily">—</b><?php esc_html_e( ' تومان — و هر روز تأخیر، همین عدد است.', 'uid-theme' ); ?></span></div>

	        <div class="micro" id="lossMicro">
	          <p><?php esc_html_e( 'می‌خواهید همین عدد را صفر کنید؟ شماره‌تان را بگذارید تا کارشناس یوآیدی پلن متناسب با حجم شما را همین امروز اعلام کند.', 'uid-theme' ); ?></p>
	          <form id="lossForm" novalidate>
	            <div class="micro-fields">
	              <input type="hidden" name="source" value="iban-lp-loss">
	              <input type="hidden" name="volume" id="lossVolume" value="">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ib-btn btn-cta" type="button" data-submit><?php esc_html_e( 'مشاوره رایگان', 'uid-theme' ); ?></button>
	              </div>
	            </div>
	            <div class="form-ok"><?php echo uid_ib_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	              <span><?php esc_html_e( 'تیم یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>

	      <div style="display:flex;flex-direction:column;gap:16px;justify-content:center">
	        <?php foreach ( $notes as $i => $n ) :
	          $bg = 0 === $i ? 'rgba(214,69,80,.18)' : 'rgba(41,188,206,.18)';
	          $fg = 0 === $i ? '#FF9AA2' : '#7FE0EC';
	          $svg = 0 === $i
	            ? '<path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/><path d="M12 9v4M12 17h.01"/>'
	            : '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>';
	        ?>
	        <div class="secbox" style="background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.16)">
	          <div class="ic" style="background:<?php echo esc_attr( $bg ); ?>;color:<?php echo esc_attr( $fg ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( $svg, array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b style="color:#fff"><?php echo esc_html( $n['heading'] ?? '' ); ?></b>
	            <p style="color:#B9C6E0"><?php echo esc_html( $n['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	        <div class="callband" style="border-color:rgba(41,188,206,.4)">
	          <div class="ic"><?php echo uid_ib_phone_icon(); ?></div>
	          <div class="tx"><b><?php echo esc_html( uid_section_val( 'ibloss', 'cta_heading', __( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'ibloss', 'cta_text', __( 'کارشناس فنی یوآیدی در یک تماس، حجم استعلام و سناریوی شما را بررسی می‌کند.', 'uid-theme' ) ) ); ?></p></div>
	          <div class="acts"><a class="ib-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a></div>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) مسیر اتصال (۳ گام)
 * ===================================================================== */
function uid_default_ib_start() {
	return array(
		array( 'title' => 'ثبت درخواست دمو و مشاوره', 'text' => 'فرم اطلاعات کسب‌وکار را پر می‌کنید. کارشناس یوآیدی سناریوی شما را بررسی و ظرفیت استعلام ماهانه را برآورد می‌کند.' ),
		array( 'title' => 'دریافت کلید دسترسی و مستندات', 'text' => 'توکن محیط آزمایشی (سندباکس)، businessId و businessToken، به‌همراه نمونه‌کدهای آماده در اختیار تیم فنی شما قرار می‌گیرد.' ),
		array( 'title' => 'تست، پیاده‌سازی و ورود به عملیات', 'text' => 'سناریوها را شبیه‌سازی می‌کنید، اتصال را نهایی می‌کنید و با پشتیبانی اختصاصی یوآیدی وارد محیط عملیاتی می‌شوید.' ),
	);
}
function uid_render_section_ibstart() {
	$tag   = uid_section_tag( 'ibstart', 'h2' );
	$steps = uid_section_val( 'ibstart', 'steps', uid_default_ib_start() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" id="start">
	  <div class="ib-wrap">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibstart', 'eyebrow', __( 'مسیر اتصال', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibstart', 'heading', __( 'از تماس تا اولین استعلام واقعی، کمتر از یک روز کاری', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibstart', 'text', __( 'تیم فنی یوآیدی مسیر را برای شما کوتاه می‌کند: مستندات شفاف، نمونه‌کد آماده و پشتیبانی اختصاصی در تمام مراحل.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="flow3 rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="fl-node">
	        <div class="fl-card"><span class="fl-num"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span>
	          <h4><?php echo esc_html( $s['title'] ?? '' ); ?></h4>
	          <p><?php echo esc_html( $s['text'] ?? '' ); ?></p></div>
	        <?php if ( $i < count( $steps ) - 1 ) : ?>
	        <span class="fl-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></span>
	        <?php endif; ?>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-block-start:26px">
	      <button class="ib-btn btn-cta" data-open-modal><?php echo uid_ib_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ibstart', 'btn1_text', __( 'شروع مرحله اول — همین حالا', 'uid-theme' ) ) ); ?></button>
	      <a class="ib-btn btn-ghost" href="#dev"><?php esc_html_e( 'مشاهده مستندات فنی', 'uid-theme' ); ?> <span class="lat">API</span></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱) ورودی/خروجی سرویس
 * ===================================================================== */
function uid_render_section_ibout() {
	$tag = uid_section_tag( 'ibout', 'h2' );
	?>
	<section class="sec" id="out" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibout', 'fold_title', __( 'ورودی و خروجی سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibout', 'fold_teaser', __( 'یک شماره شبا وارد می‌شود، هفت فیلد برمی‌گردد', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibout', 'eyebrow', __( 'داده‌های خروجی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibout', 'heading', __( 'یک عدد می‌فرستید، یک پرونده مالی می‌گیرید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php esc_html_e( 'وب‌سرویس استعلام شبا با یک فراخوانی POST، هویت دارنده حساب و وضعیت لحظه‌ای آن را در قالب استاندارد JSON به سامانه شما تحویل می‌دهد.', 'uid-theme' ); ?></p>
	    </div>

	    <div class="fmap rv">
	      <div class="fmap-side">
	        <span class="lbl"><?php esc_html_e( 'ورودی — تنها چیزی که باید بفرستید', 'uid-theme' ); ?></span>
	        <div class="fmap-in">IR12 3456 7890 1234 5678 9012 34</div>
	        <p><?php esc_html_e( 'شماره شبای ۲۴ رقمی همراه با پیشوند IR، در بدنه درخواست. همین. نه کد ملی لازم است، نه شماره حساب، نه هیچ فیلد اضافه‌ای.', 'uid-theme' ); ?></p>
	      </div>
	      <div>
	        <span class="lbl" style="font-size:12px;font-weight:700;color:var(--n400);display:block;margin-block-end:12px"><?php esc_html_e( 'خروجی — آنچه سرویس برمی‌گرداند', 'uid-theme' ); ?></span>
	        <div class="ftags">
	          <span class="ftag"><span class="key">firstName</span><?php esc_html_e( 'نام صاحب حساب', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">lastName</span><?php esc_html_e( 'نام خانوادگی صاحب حساب', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">nationalIdentifier</span><?php esc_html_e( 'کد ملی صاحب حساب', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">accountNumber</span><?php esc_html_e( 'شماره حساب متناظر', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">bankName</span><?php esc_html_e( 'نام بانک صادرکننده', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">accountStatus</span><?php esc_html_e( 'وضعیت حساب (فعال / مسدود)', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">customerType</span><?php esc_html_e( 'نوع مشتری (حقیقی / حقوقی)', 'uid-theme' ); ?></span>
	        </div>
	        <div class="secbox" style="margin-block-start:20px;background:var(--teal-l);border-color:rgba(41,188,206,.3)">
	          <div class="ic" style="color:var(--teal-d)"><?php echo uid_ib_arrow_icon(); ?></div>
	          <div><b style="color:#0E5A66"><?php echo esc_html( uid_section_val( 'ibout', 'shared_heading', __( 'حساب‌های مشترک هم پشتیبانی می‌شوند', 'uid-theme' ) ) ); ?></b>
	            <p style="color:#12707E"><?php echo esc_html( uid_section_val( 'ibout', 'shared_text', __( 'اطلاعات صاحبان حساب در آرایه‌ای با عنوان owners برگردانده می‌شود. اگر حساب یک صاحب داشته باشد، این آرایه تک‌عضوی است؛ برای حساب‌های مشترک، همه دارندگان فهرست می‌شوند.', 'uid-theme' ) ) ); ?></p></div>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۲) الگوریتم در برابر استعلام واقعی
 * ===================================================================== */
function uid_default_ib_vs_bad_items() {
	return array(
		'نمی‌داند حساب باز است یا بسته شده',
		'نمی‌داند حساب مسدود است یا نه',
		'نمی‌داند شبا متعلق به چه کسی است',
		'یک شبای معتبرِ متعلق به شخص دیگر را با آغوش باز قبول می‌کند',
	);
}
function uid_default_ib_vs_good_items() {
	return array(
		'وضعیت لحظه‌ای حساب: ACCOUNT_STATUS_ACTIVE یا مسدود',
		'نام، نام خانوادگی و کد ملی صاحب حساب',
		'نام بانک عامل و شماره حساب متناظر',
		'امکان تطبیق نام صاحب حساب با هویت کاربر شما پیش از واریز',
	);
}
function uid_render_section_ibvs() {
	$tag        = uid_section_tag( 'ibvs', 'h2' );
	$bad_items  = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ibvs', 'bad_items', implode( "\n", uid_default_ib_vs_bad_items() ) ) ) ) );
	$good_items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ibvs', 'good_items', implode( "\n", uid_default_ib_vs_good_items() ) ) ) ) );
	?>
	<section class="sec" id="vs" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibvs', 'fold_title', __( 'چرا بررسی فرمت شبا کافی نیست', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibvs', 'fold_teaser', __( 'تفاوت اعتبارسنجی ریاضی با استعلام واقعی از بانک', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibvs', 'eyebrow', __( 'یک اشتباه رایج و پرهزینه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibvs', 'heading', __( 'الگوریتم شبا فقط املا را چک می‌کند، نه واقعیت را', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibvs', 'text', __( 'بسیاری از تیم‌های فنی فکر می‌کنند بررسی چک‌دیجیت IBAN یعنی «شبا معتبر است». این دقیقاً جایی است که پول‌ها گم می‌شوند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="vs2 rv">
	      <article class="vscard bad">
	        <span class="tag"><?php echo uid_ib_x_icon(); ?><?php echo esc_html( uid_section_val( 'ibvs', 'bad_tag', __( 'فقط بررسی ساختار و چک‌دیجیت', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'ibvs', 'bad_title', __( 'اعتبارسنجی محلی (سمت خودتان)', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'ibvs', 'bad_text', __( 'یک تابع چند خطی در بک‌اند شما تأیید می‌کند که ۲۴ رقم از نظر ریاضی درست چیده شده‌اند. همین و بس.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $bad_items as $li ) : ?>
	          <li><?php echo uid_ib_x_icon(); ?><?php echo esc_html( $li ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	      <article class="vscard good">
	        <span class="tag"><?php echo uid_ib_check_icon(); ?><?php echo esc_html( uid_section_val( 'ibvs', 'good_tag', __( 'استعلام لحظه‌ای از شبکه بانکی', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'ibvs', 'good_title', __( 'وب‌سرویس استعلام شبا یوآیدی', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'ibvs', 'good_text', __( 'شبا را به شبکه بانکی می‌سپارد و وضعیت واقعی حساب به‌همراه مشخصات هویتی دارنده آن را برمی‌گرداند.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $good_items as $li ) : ?>
	          <li><?php echo uid_ib_check_icon(); ?><?php echo esc_html( $li ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۳) نحوه کار سرویس (سه‌گانه)
 * ===================================================================== */
function uid_default_ib_how() {
	return array(
		array( 'icon' => 'send', 'title' => '۱ — ارسال شماره شبا', 'text' => 'شبای واردشده توسط کاربر با فرمت استاندارد و پیشوند IR، از طریق متد امن POST به وب‌سرویس یوآیدی ارسال می‌شود.', 'tagline' => 'POST /api/inquiry/iban/v2' ),
		array( 'icon' => 'bank', 'title' => '۲ — بررسی و اتصال به شبکه بانکی', 'text' => 'ساختار شبا بررسی و فرمت اعتبارسنجی می‌شود، سپس وضعیت حساب به‌صورت بلادرنگ از زیرساخت بانکی کشور استعلام می‌گردد.', 'tagline' => 'چند مسیر بانکی پشتیبان' ),
		array( 'icon' => 'reply', 'title' => '۳ — دریافت پاسخ ساختاریافته', 'text' => 'اطلاعات کامل دارنده حساب و وضعیت شبا در قالب استاندارد JSON به سامانه پذیرنده بازگردانده می‌شود.', 'tagline' => 'پاسخ لحظه‌ای' ),
	);
}
function uid_render_section_ibhow() {
	$tag   = uid_section_tag( 'ibhow', 'h2' );
	$items = uid_section_val( 'ibhow', 'items', uid_default_ib_how() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="how" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibhow', 'fold_title', __( 'سه مرحله عملکرد سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibhow', 'fold_teaser', __( 'ارسال شبا، اتصال به شبکه بانکی، پاسخ ساختاریافته', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibhow', 'eyebrow', __( 'پشت صحنه سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibhow', 'heading', __( 'از ارسال شبا تا پاسخ ساختاریافته', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="how">
	        <?php foreach ( $items as $it ) :
	          $color = uid_ib_how_color( $it['icon'] ?? 'send' );
	        ?>
	        <article class="dcard">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ib_how_icon_svg( $it['icon'] ?? 'send' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="tagline"><?php echo uid_ib_check_icon(); ?><span class="lat"><?php echo esc_html( $it['tagline'] ?? '' ); ?></span></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="how">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( '۳ مرحله — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۴) کاربردها (انتخابگر صنعت)
 * ===================================================================== */
function uid_default_ib_who() {
	return array(
		array( 'icon' => 'crypto', 'label' => 'صرافی و بروکر', 'title' => 'تسویه‌حساب با کاربران پلتفرم', 'text' => 'بررسی شماره شبا پیش از واریز درآمد یا موجودی کاربران در صرافی‌های رمزارز، بروکرها و وب‌سایت‌های فریلنسری — جایی که هر برداشت ناموفق مستقیماً به تیکت پشتیبانی تبدیل می‌شود.', 'win' => 'برداشت‌های ناموفق و پیگیری‌های مالی به صفر می‌رسد' ),
		array( 'icon' => 'market', 'label' => 'مارکت‌پلیس', 'title' => 'پرداخت به فروشندگان و تامین‌کنندگان', 'text' => 'اعتبارسنجی شبای فروشندگان در مارکت‌پلیس‌ها پیش از تسویه‌های دوره‌ای و پرداخت مبالغ سفارش‌ها، تا هیچ دسته‌واریزی به‌خاطر یک رکورد اشتباه متوقف نشود.', 'win' => 'تسویه دسته‌ای بدون برگشت خوردن حواله' ),
		array( 'icon' => 'refund', 'label' => 'فروشگاه اینترنتی', 'title' => 'بازپرداخت وجه (Refund)', 'text' => 'بررسی شبای مشتری پیش از عودت وجه سفارش‌های لغو‌شده، ودیعه‌ها یا مبالغ اضافه واریز‌شده — همان‌جایی که مشتری عصبانی است و اشتباه دوم را نمی‌بخشد.', 'win' => 'بازپرداخت درست، بار اول' ),
		array( 'icon' => 'lend', 'label' => 'لندتک و نئوبانک', 'title' => 'پلتفرم‌های لندتک و نئوبانک‌ها', 'text' => 'کنترل دقیق اطلاعات شبا در فرایندهای پرداخت تسهیلات، اعتبارسنجی و مدیریت تراکنش‌های مالی، با امکان تطبیق کد ملی دارنده حساب با پرونده متقاضی.', 'win' => 'انطباق هویت متقاضی با حساب مقصد' ),
		array( 'icon' => 'ins', 'label' => 'بیمه', 'title' => 'بیمه و پرداخت خسارت', 'text' => 'استعلام شبای بیمه‌گذاران و ذی‌نفعان پیش از صدور حواله‌های خسارت و غرامت، تا پرداخت به حساب مسدود یا حساب شخص ثالث انجام نشود.', 'win' => 'حواله خسارت بدون مغایرت' ),
		array( 'icon' => 'payroll', 'label' => 'حقوق و دستمزد', 'title' => 'حقوق و پرداخت‌های سازمانی', 'text' => 'راستی‌آزمایی شبای پرسنل و پیمانکاران پیش از واریز ماهانه حقوق، پاداش و مطالبات سازمانی — به‌ویژه در سازمان‌هایی که فهرست حقوق را دسته‌ای به بانک می‌فرستند.', 'win' => 'لیست حقوق پاک، پیش از ارسال به بانک' ),
	);
}
function uid_render_section_ibwho() {
	$tag   = uid_section_tag( 'ibwho', 'h2' );
	$items = uid_section_val( 'ibwho', 'items', uid_default_ib_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="who" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibwho', 'fold_title', __( 'کاربردهای وب‌سرویس استعلام شبا', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibwho', 'fold_teaser', __( 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibwho', 'eyebrow', __( 'کجا به کار می‌آید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibwho', 'heading', __( 'هر جا که پول از سامانه شما بیرون می‌رود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibwho', 'text', __( 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="rv">
	      <div class="pick-chips" data-pick-chips="ind" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب صنعت', 'uid-theme' ); ?>"></div>
	      <div class="pick" data-pick="ind">
	        <?php foreach ( $items as $it ) :
	          $color = uid_ib_who_color( $it['icon'] ?? 'crypto' );
	        ?>
	        <article class="pick-card" data-label="<?php echo esc_attr( $it['label'] ?? '' ); ?>">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ib_who_icon_svg( $it['icon'] ?? 'crypto' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="win"><?php echo uid_ib_check_icon(); ?><?php echo esc_html( $it['win'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="pick-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg><?php esc_html_e( 'صنعت خود را از نوار بالا انتخاب کنید یا بکشید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۵) چهار نشتی و راه‌حل
 * ===================================================================== */
function uid_default_ib_risks() {
	return array(
		array( 'title' => 'واریز به حساب مسدود', 'bad_text' => 'وجه ارسال می‌شود، در حساب مسدود می‌ماند، و آزادسازی آن هفته‌ها مکاتبه بانکی می‌برد — در حالی که کاربر هر روز پیگیر است.', 'fix_text' => 'فیلد accountStatus پیش از هر تراکنش بررسی می‌شود. حساب غیرفعال، همان‌جا رد می‌شود و پول اصلاً حرکت نمی‌کند.' ),
		array( 'title' => 'شبای متعلق به شخص دیگر', 'bad_text' => 'کاربر شبای فرد دیگری را وارد می‌کند — گاهی سهوی، گاهی عمدی. ساختار شبا کاملاً معتبر است و هیچ اعتبارسنجی محلی جلوی آن را نمی‌گیرد.', 'fix_text' => 'نام، نام خانوادگی و کد ملی صاحب حساب برمی‌گردد و می‌توانید آن را با هویت کاربر ثبت‌شده در سامانه خودتان تطبیق دهید.' ),
		array( 'title' => 'غلط تایپی در ۲۴ رقم', 'bad_text' => 'یک رقم جابه‌جا، و حواله یا برگشت می‌خورد یا — بدتر — به حساب دیگری می‌نشیند. کاربر شماره را از روی کارت تایپ می‌کند و اشتباه اجتناب‌ناپذیر است.', 'fix_text' => 'در همان لحظه‌ی پر کردن فرم، نام صاحب حساب را به کاربر نشان می‌دهید و از او تأیید می‌گیرید. اشتباه، پیش از ثبت گرفته می‌شود.' ),
		array( 'title' => 'بار پشتیبانی و مغایرت حساب', 'bad_text' => 'هر واریز ناموفق یعنی یک تیکت، یک تماس، یک مکاتبه با بانک و یک ردیف مغایرت که باید سر ماه بسته شود.', 'fix_text' => 'حذف کامل خطاهای واریز و برگشت وجه، کاهش بار پشتیبانی مالی، و گزارش‌های دقیق از تعداد تراکنش‌ها و لاگ‌ها برای بستن حساب.' ),
	);
}
function uid_render_section_ibrisk() {
	$tag   = uid_section_tag( 'ibrisk', 'h2' );
	$items = uid_section_val( 'ibrisk', 'items', uid_default_ib_risks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="risk" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibrisk', 'fold_title', __( 'چهار نشتی که این سرویس می‌بندد', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibrisk', 'fold_teaser', __( 'از حساب مسدود تا کلاهبرداری با شبای شخص ثالث', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibrisk', 'eyebrow', __( 'ریسک‌هایی که امروز باز هستند', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibrisk', 'heading', __( 'هر کدام از این چهار مورد، یک بار هم که اتفاق بیفتد گران است', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="leak-grid deck d2" data-deck="risk">
	        <?php foreach ( $items as $i => $it ) : ?>
	        <article class="leak">
	          <div class="leak-bad">
	            <div class="leak-hd"><span class="tag"><?php echo uid_ib_x_icon(); ?><?php esc_html_e( 'بدون استعلام', 'uid-theme' ); ?></span><span class="leak-n"><?php echo esc_html( uid_fa_digits( sprintf( '%02d', $i + 1 ) ) ); ?></span></div>
	            <h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
	            <p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p>
	          </div>
	          <div class="leak-fix"><span class="tag"><?php echo uid_ib_check_icon(); ?><?php esc_html_e( 'با یوآیدی', 'uid-theme' ); ?></span>
	            <p><?php echo esc_html( $it['fix_text'] ?? '' ); ?></p></div>
	        </article>
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
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۶) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_ib_team_rows() {
	return array(
		array( 'icon' => 'connect', 'title' => 'کارشناس فنی اختصاصی برای اتصال', 'text' => 'از اولین تماس تا ورود به محیط عملیاتی، یک نفر مشخص پاسخگوی تیم فنی شماست — نه یک صف تیکت.' ),
		array( 'icon' => 'doc', 'title' => 'مستندات شفاف و نمونه‌کد استاندارد', 'text' => 'ساختار درخواست و پاسخ، کدهای وضعیت و سناریوهای خطا مستند شده‌اند؛ پیاده‌سازی در کمتر از یک روز کاری ممکن است.' ),
		array( 'icon' => 'report', 'title' => 'گزارش‌دهی دوره‌ای تراکنش‌ها و لاگ‌ها', 'text' => 'گزارش دقیق از تعداد استعلام‌ها، نرخ موفقیت و لاگ فراخوانی‌ها، برای تسویه شفاف و بستن مغایرت‌ها.' ),
		array( 'icon' => 'shield', 'title' => 'امنیت داده و انطباق قانونی', 'text' => 'پردازش داده‌ها در محیط امن و منطبق بر پروتکل‌های استاندارد شبکه بانکی.' ),
	);
}
function uid_default_ib_pledge_items() {
	return array(
		'دسترسی به محیط سندباکس پیش از هرگونه قرارداد',
		'هزینه فقط بابت استعلام‌های موفق؛ استعلام ناموفق رایگان است',
		'تعرفه پلکانی و تخفیف ویژه برای پلتفرم‌های پرتراکنش',
		'زیرساخت Redundant با چند مسیر بانکی پشتیبان برای ساعات پیک',
	);
}
function uid_render_section_ibteam() {
	$rows       = uid_section_val( 'ibteam', 'rows', uid_default_ib_team_rows() );
	$pledge_raw = uid_section_val( 'ibteam', 'pledge_items', implode( "\n", uid_default_ib_pledge_items() ) );
	$pledge     = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	$tag = uid_section_tag( 'ibteam', 'h2' );
	?>
	<section class="sec" id="team" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibteam', 'fold_title', __( 'تیم متخصص و تعهد پشتیبانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibteam', 'fold_teaser', __( 'با چه کسانی وصل می‌شوید و چه چیزی تضمین می‌شود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibteam', 'eyebrow', __( 'کنار چه تیمی وصل می‌شوید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php esc_html_e( 'یک ', 'uid-theme' ); ?><span class="lat">API</span><?php echo esc_html( uid_section_val( 'ibteam', 'heading', __( ' نمی‌خرید؛ یک تیم فنی کنارتان می‌گذارید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="team rv">
	      <div class="team-list">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ib_team_icon_svg( $r['icon'] ?? 'connect' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><?php echo uid_ib_check_icon(); ?></span>
	          <b><?php echo esc_html( uid_section_val( 'ibteam', 'pledge_heading', __( 'تعهد یوآیدی به مشتریان وب‌سرویس', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_ib_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div style="margin-block-start:18px">
	          <a class="ib-btn btn-ghost btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ib_phone_icon(); ?>
	            <?php esc_html_e( 'گفتگو با کارشناس فنی · ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
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
function uid_render_section_ibdev() {
	$tag = uid_section_tag( 'ibdev', 'h2' );
	?>
	<section class="sec" id="dev" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibdev', 'fold_title', __( 'مستندات فنی و نمونه کد', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibdev', 'fold_teaser', __( 'نمونه درخواست و پاسخ واقعی سرویس، با دکمه کپی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibdev', 'eyebrow', __( 'برای تیم فنی شما', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibdev', 'heading', __( 'یک متد، یک فیلد ورودی، یک پاسخ استاندارد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php esc_html_e( 'وب‌سرویس استعلام شبا یوآیدی از طریق متد استاندارد POST و با فرمت JSON عمل می‌کند.', 'uid-theme' ); ?></p>
	    </div>

	    <div class="codepanel rv">
	      <div>
	        <div class="secbox" style="background:var(--n50);border-color:var(--n200);margin-block-end:18px">
	          <div class="ic" style="color:var(--navy)"><?php echo uid_ib_arrow_icon(); ?></div>
	          <div><b style="color:var(--ink)"><?php esc_html_e( 'نقطه انتهایی سرویس', 'uid-theme' ); ?></b>
	            <p style="color:var(--n600)" class="mono" dir="ltr">POST https://json-api.uid.ir/api/inquiry/iban/v2</p></div>
	        </div>
	        <p style="font-size:14px;color:var(--n600);line-height:1.95;margin-block-end:18px">
	          <?php echo esc_html( uid_section_val( 'ibdev', 'text', __( 'با ارسال شماره شبا در بدنه درخواست، اطلاعات کامل حساب در بدنه پاسخ بازگردانده می‌شود. احراز هویت کسب‌وکار از طریق businessId و businessToken انجام می‌شود که هر دو توسط یوآیدی در اختیار شما قرار می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	        <div style="display:flex;gap:10px;flex-wrap:wrap">
	          <button class="ib-btn btn-cta" data-open-modal><?php echo uid_ib_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ibdev', 'btn1_text', __( 'دریافت کلید وب‌سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ib-btn btn-ghost" href="<?php echo esc_url( home_url( '/api-inquiry-iban/' ) ); ?>"><?php esc_html_e( 'مشاهده مستندات کامل', 'uid-theme' ); ?> <span class="lat">API</span></a>
	        </div>
	        <p style="font-size:12px;color:var(--n400);margin-block-start:14px;line-height:1.8">
	          <?php esc_html_e( 'نسخه مستندات: ', 'uid-theme' ); ?><span class="mono">1.1.0</span></p>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button type="button" class="on" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button type="button" data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" type="button" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req">{
  <span class="k">"requestContext"</span>: {
    <span class="k">"apiInfo"</span>: {
      <span class="k">"businessId"</span>: <span class="m">&lt;UID_BUSINESS_ID&gt;</span>,
      <span class="k">"businessToken"</span>: <span class="m">&lt;UID_BUSINESS_TOKEN&gt;</span>
    }
  },
  <span class="k">"iban"</span>: <span class="s">"IR123456789012345678901234"</span>
}</pre>
<pre class="code-pane" data-code-pane="res">{
  <span class="k">"responseContext"</span>: {
    <span class="k">"status"</span>: {
      <span class="k">"code"</span>: <span class="s">0</span>,
      <span class="k">"message"</span>: <span class="s">"SUCCESS."</span>,
      <span class="k">"details"</span>: []
    },
    <span class="k">"requestId"</span>: <span class="s">""</span>,
    <span class="k">"correlationId"</span>: <span class="s">""</span>
  },
  <span class="k">"accountBasicInformation"</span>: {
    <span class="k">"iban"</span>: <span class="m">&lt;IBAN_NUMBER&gt;</span>,
    <span class="k">"accountNumber"</span>: <span class="m">&lt;ACCOUNT_NUMBER&gt;</span>,
    <span class="k">"bankInformation"</span>: {
      <span class="k">"bankName"</span>: <span class="m">&lt;BANK_NAME&gt;</span>
    }
  },
  <span class="k">"accountStatus"</span>: <span class="m">&lt;ACCOUNT_STATUS&gt;</span>,
  <span class="k">"owners"</span>: [
    {
      <span class="k">"firstName"</span>: <span class="m">&lt;FIRST_NAME&gt;</span>,
      <span class="k">"lastName"</span>: <span class="m">&lt;LAST_NAME&gt;</span>,
      <span class="k">"nationalIdentifier"</span>: <span class="m">&lt;NATIONAL_IDENTIFIER&gt;</span>,
      <span class="k">"customerType"</span>: <span class="m">&lt;CUSTOMER_TYPE&gt;</span>
    }
  ]
}</pre>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۸) سرویس‌های مکمل
 * ===================================================================== */
function uid_default_ib_xsell() {
	return array(
		array( 'icon' => 'card', 'title' => 'وب‌سرویس تبدیل شماره کارت به شبا', 'text' => 'دریافت شماره شبا و شماره حساب با استفاده از شماره ۱۶ رقمی کارت بانکی — وقتی کاربر فقط کارتش را در دست دارد.', 'url' => '/api-card-to-iban/' ),
		array( 'icon' => 'match', 'title' => 'وب‌سرویس تطبیق شماره شبا با کد ملی', 'text' => 'بررسی و اعتبارسنجی تعلق شماره شبا به کد ملی ثبت‌شده کاربر (سرویس شاهکار مالی) — پاسخ صریح تطابق دارد / ندارد.', 'url' => '/api-iban-nationalid/' ),
		array( 'icon' => 'cardid', 'title' => 'وب‌سرویس تطبیق شماره کارت با کد ملی', 'text' => 'احراز تطابق مالکیت شماره کارت بانکی با شماره ملی خریدار در درگاه‌های پرداخت — سپر ضد کلاهبرداری در لحظه پرداخت.', 'url' => '/api-card-nationalid/' ),
	);
}
function uid_render_section_ibxsell() {
	$tag   = uid_section_tag( 'ibxsell', 'h2' );
	$items = uid_section_val( 'ibxsell', 'items', uid_default_ib_xsell() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="xsell" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibxsell', 'fold_title', __( 'سرویس‌های مکمل و مرتبط', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibxsell', 'fold_teaser', __( 'تبدیل کارت به شبا، تطبیق شبا و کد ملی، تطبیق کارت و کد ملی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibxsell', 'eyebrow', __( 'در کنار این سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibxsell', 'heading', __( 'سه وب‌سرویسی که معمولاً با استعلام شبا با هم فعال می‌شوند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibxsell', 'text', __( 'اگر بیش از یکی را نیاز دارید، در همان مشاوره اول پلن ترکیبی با تعرفه بهتر برایتان بسته می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="xsell rv">
	      <?php foreach ( $items as $it ) : ?>
	      <a class="xtile" href="<?php echo esc_url( $it['url'] ?? '#' ); ?>">
	        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ib_xsell_icon_svg( $it['icon'] ?? 'card' ), array( 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'opacity' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="go"><?php esc_html_e( 'مشاهده سرویس', 'uid-theme' ); ?> <?php echo uid_ib_arrow_icon(); ?></span>
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
function uid_default_ib_faq() {
	return array(
		array( 'question' => 'برای استعلام شبا چه اطلاعاتی باید ارسال شود؟', 'answer' => 'تنها کافی است شماره شبای ۲۴ رقمی همراه با پیشوند IR در بدنه درخواست API ارسال شود. هیچ فیلد اضافه‌ای لازم نیست.' ),
		array( 'question' => 'وب‌سرویس استعلام شبا چه اطلاعاتی را برمی‌گرداند؟', 'answer' => 'نام و نام خانوادگی دارنده حساب، کد ملی، شماره حساب، نام بانک عامل و وضعیت فعال یا غیرفعال بودن حساب بانکی در خروجی ارائه می‌شود. نوع مشتری (حقیقی یا حقوقی) نیز در پاسخ وجود دارد.' ),
		array( 'question' => 'آیا بررسی الگوریتم و ساختار شبا برای اعتبارسنجی کافی نیست؟', 'answer' => 'خیر. درست بودن ساختار شبا فقط رعایت فرمت را تایید می‌کند؛ وب‌سرویس وضعیت لحظه‌ای، مسدود نبودن حساب و هویت صاحب آن را در شبکه بانکی استعلام می‌کند.' ),
		array( 'question' => 'اگر شماره شبای ارسالی نادرست باشد، هزینه‌ای کسر می‌شود؟', 'answer' => 'خیر. هزینه تنها برای استعلام‌های موفق و بازگشت رکورد معتبر از شبکه بانکی محاسبه می‌شود. استعلام‌های ناموفق یا اشتباه هزینه‌ای ندارند.' ),
		array( 'question' => 'مدت زمان پاسخ‌دهی سرویس چقدر است؟', 'answer' => 'پاسخ وب‌سرویس یوآیدی به‌صورت لحظه‌ای ارسال می‌شود. برای جلوگیری از قطعی در ساعات پیک، سرویس روی زیرساخت Redundant با چند مسیر بانکی پشتیبان اجرا می‌شود.' ),
		array( 'question' => 'اگر حساب چند صاحب داشته باشد چه اتفاقی می‌افتد؟', 'answer' => 'اطلاعات صاحبان حساب در آرایه‌ای با عنوان owners برگردانده می‌شود. برای حساب‌های تک‌نفره این آرایه یک عضو دارد و برای حساب‌های مشترک، مشخصات همه دارندگان در همان آرایه قرار می‌گیرد.' ),
		array( 'question' => 'چقدر طول می‌کشد تا سرویس روی سامانه ما فعال شود؟', 'answer' => 'پس از تکمیل فرم درخواست، کارشناس یوآیدی با شما تماس می‌گیرد و دسترسی سندباکس به‌همراه businessId و businessToken در اختیار تیم فنی شما قرار می‌گیرد. با مستندات شفاف و نمونه‌کدهای آماده، اتصال معمولاً در کمتر از یک روز کاری انجام می‌شود.' ),
		array( 'question' => 'آیا این سرویس برای کاربران شخصی هم ارائه می‌شود؟', 'answer' => 'خیر. وب‌سرویس استعلام شبا فقط برای کسب‌وکارهای مجاز و با قرارداد فعال می‌شود. اگر به‌صورت شخصی به خدمات احراز هویت نیاز دارید، یوآیدی‌پلاس یا احراز هویت ثنا گزینه درست شماست.' ),
	);
}
function uid_render_section_ibfaq() {
	$tag   = uid_section_tag( 'ibfaq', 'h2' );
	$items = uid_section_val( 'ibfaq', 'items', uid_default_ib_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ibfaq', 'fold_title', __( 'سوالات متداول', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ibfaq', 'fold_teaser', __( 'ورودی، خروجی، هزینه استعلام ناموفق و زمان پاسخ‌دهی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ib-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibfaq', 'eyebrow', __( 'پیش از تماس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibfaq', 'heading', __( 'سوالات متداول وب‌سرویس استعلام شبا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i">
	        <button class="faq-q" aria-expanded="false"><?php echo esc_html( $it['question'] ?? '' ); ?>
	          <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></button>
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
 * مزایای رقابتی (پنل تیره)
 * ===================================================================== */
function uid_default_ib_adv() {
	return array(
		array( 'icon' => 'shield', 'value' => 'حذف کامل خطای واریز', 'text' => 'جلوگیری از ارسال حواله‌های ناموفق و کاهش بار پشتیبانی مالی و پیگیری‌های مغایرت حساب — پیش از آنکه پول از حساب شما خارج شود.', 'percent' => '100', 'feat' => '1', 'wide' => '' ),
		array( 'icon' => 'redundant', 'value' => 'زیرساخت Redundant', 'text' => 'چند مسیر بانکی پشتیبان برای جلوگیری از قطعی استعلام در ساعات پیک.', 'percent' => '94', 'feat' => '', 'wide' => '' ),
		array( 'icon' => 'fast', 'value' => 'پیاده‌سازی سریع', 'text' => 'مستندات فنی شفاف، نمونه کدهای استاندارد و امکان اتصال در کمتر از یک روز کاری.', 'percent' => '88', 'feat' => '', 'wide' => '' ),
		array( 'icon' => 'report', 'value' => 'پشتیبانی و گزارش‌دهی', 'text' => 'پشتیبانی فنی مستقیم برای کسب‌وکارها و ارائه گزارش‌های دقیق از تعداد تراکنش‌ها و لاگ‌ها.', 'percent' => '92', 'feat' => '', 'wide' => '1' ),
		array( 'icon' => 'lock', 'value' => 'امنیت و انطباق', 'text' => 'پردازش داده‌ها در محیط امن و منطبق بر پروتکل‌های استاندارد شبکه بانکی.', 'percent' => '96', 'feat' => '', 'wide' => '1' ),
	);
}
function uid_render_section_ibadv() {
	$tag   = uid_section_tag( 'ibadv', 'h2' );
	$items = uid_section_val( 'ibadv', 'items', uid_default_ib_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="dark sec" id="adv">
	  <div class="ib-wrap">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ibadv', 'eyebrow', __( 'مزایای رقابتی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibadv', 'heading', __( 'چرا کسب‌وکارها استعلام شبا را از یوآیدی می‌گیرند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="opanel rv">
	      <?php foreach ( $items as $it ) :
	        $classes = 'ostat';
	        if ( ! empty( $it['feat'] ) ) $classes .= ' feat';
	        if ( ! empty( $it['wide'] ) ) $classes .= ' w2';
	        $pct = absint( $it['percent'] ?? 90 );
	      ?>
	      <div class="<?php echo esc_attr( $classes ); ?>">
	        <div class="oic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ib_adv_icon_svg( $it['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ) ) ); ?></svg></div>
	        <span class="ov txt"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	        <span class="ol"><?php echo esc_html( $it['text'] ?? '' ); ?></span>
	        <div class="ometer" style="--p:<?php echo esc_attr( $pct ); ?>%"><i></i></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="opanel-foot">
	      <span><?php echo esc_html( uid_section_val( 'ibadv', 'foot_text', __( 'هر پنج مورد بالا در همان اولین جلسه فنی قابل بررسی است.', 'uid-theme' ) ) ); ?></span>
	      <a class="ib-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ib_phone_icon(); ?>
	        <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * پوشش بانک‌ها
 * ===================================================================== */
function uid_default_ib_banks() {
	return array(
		'ملی', 'ملت', 'صادرات', 'تجارت', 'سپه', 'پارسیان', 'پاسارگاد', 'سامان',
		'اقتصاد نوین', 'کشاورزی', 'مسکن', 'رفاه', 'آینده', 'شهر', 'سینا', 'دی',
		'مهر ایران', 'بلوبانک',
	);
}
function uid_render_section_ibbanks() {
	$tag       = uid_section_tag( 'ibbanks', 'h2' );
	$items_raw = uid_section_val( 'ibbanks', 'items', implode( "\n", uid_default_ib_banks() ) );
	$items     = array_values( array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) ) );
	?>
	<section class="sec" style="padding-block:calc(var(--sec) * .62) 0">
	  <div class="ib-wrap">
	    <div class="sec-head mid rv" style="margin-bottom:22px">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibbanks', 'eyebrow', __( 'پوشش سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec" style="font-size:clamp(20px,2.6vw,30px)">'; ?><?php echo esc_html( uid_section_val( 'ibbanks', 'heading', __( 'شبای صادرشده از بانک‌ها و مؤسسات کشور', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="banks rv">
	      <?php foreach ( $items as $b ) : ?>
	      <span class="bankchip"><?php echo esc_html( $b ); ?></span>
	      <?php endforeach; ?>
	      <span class="bankchip more"><?php echo esc_html( uid_section_val( 'ibbanks', 'more_text', __( 'و سایر بانک‌ها و مؤسسات اعتباری', 'uid-theme' ) ) ); ?></span>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * محاسبه‌گر هزینه ماهانه
 * ===================================================================== */
function uid_render_section_ibprice() {
	$tag = uid_section_tag( 'ibprice', 'h2' );
	?>
	<section class="sec" id="price">
	  <div class="ib-wrap">
	    <div class="sec-head mid rv">
	      <span class="ib-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ibprice', 'eyebrow', __( 'تعرفه و مدل محاسبه هزینه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ibprice', 'heading', __( 'فقط بابت استعلام‌های موفق پرداخت می‌کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ibprice', 'text', __( 'حجم استعلام ماهانه خود را مشخص کنید تا پله تعرفه و تخفیف پلکانی متناسب با آن را ببینید. استعلام‌های ناموفق یا اشتباه هزینه‌ای ندارند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="calc rv">
	      <div class="calc-in">
	        <span class="lb"><?php esc_html_e( 'تعداد استعلام تخمینی ماهانه', 'uid-theme' ); ?></span>
	        <div class="calc-num"><span class="v" id="cl_qty">۵۰٬۰۰۰</span><span class="u"><?php esc_html_e( 'استعلام موفق در ماه', 'uid-theme' ); ?></span></div>
	        <div class="calc-slider">
	          <input id="cl_range" type="range" min="0" max="1000" value="560"
	                 aria-label="<?php esc_attr_e( 'تعداد استعلام ماهانه', 'uid-theme' ); ?>">
	          <div class="calc-ends"><span>۱٬۰۰۰</span><span>+۲٬۰۰۰٬۰۰۰</span></div>
	        </div>
	        <div class="calc-presets" id="cl_presets">
	          <button class="calc-preset" type="button" data-qty="5000"><?php esc_html_e( '۵ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="25000"><?php esc_html_e( '۲۵ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="100000"><?php esc_html_e( '۱۰۰ هزار', 'uid-theme' ); ?></button>
	          <button class="calc-preset" type="button" data-qty="500000"><?php esc_html_e( '۵۰۰ هزار', 'uid-theme' ); ?></button>
	        </div>
	        <div class="calc-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'اعداد این محاسبه‌گر نمایشی و برای برآورد اولیه است. تعرفه نهایی و تخفیف پلکانی در مشاوره تخصصی و بر اساس حجم و سناریوی شما اعلام می‌شود.', 'uid-theme' ); ?></span></div>
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
	        <div class="calc-free"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
	          <span><?php esc_html_e( 'استعلام ناموفق، رایگان است — فقط رکوردهای معتبر بازگشتی محاسبه می‌شوند.', 'uid-theme' ); ?></span></div>
	        <div class="calc-cta">
	          <button class="ib-btn btn-cta ib-btn-block" data-open-modal><?php echo uid_ib_submit_icon(); ?>
	            <?php esc_html_e( 'دریافت تعرفه دقیق برای این حجم', 'uid-theme' ); ?></button>
	          <p class="calc-dis"><?php esc_html_e( 'با ثبت این درخواست، حجم انتخابی شما به‌صورت خودکار برای کارشناس ارسال می‌شود تا پلن پیشنهادی دقیق‌تر باشد.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بند تماس پایانی
 * ===================================================================== */
function uid_render_section_iblastcall() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="ib-wrap">
	    <div class="callband rv">
	      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9.5"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'iblastcall', 'heading', __( 'هر روزی که این سرویس فعال نیست، هزینه‌اش را پرداخت می‌کنید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'iblastcall', 'text', __( 'واریزهای ناموفق منتظر تصمیم شما نمی‌مانند. یک تماس کوتاه کافی است تا کارشناس یوآیدی حجم استعلام و سناریوی شما را بررسی کند و دسترسی سندباکس را فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="ib-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ib_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="ib-btn btn-ghost" data-open-modal><?php echo esc_html( uid_section_val( 'iblastcall', 'btn2_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_iblead() {
	$tag         = uid_section_tag( 'iblead', 'h2' );
	$trust_raw   = uid_section_val( 'iblead', 'trust', "مشاوره رایگان، بدون تعهد\nدسترسی به محیط سندباکس پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nتعرفه پلکانی متناسب با حجم استعلام شما" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'iblead', 'biztype_options', "صرافی ارز دیجیتال یا بروکر\nمارکت‌پلیس و فروشگاه اینترنتی\nبانک، فین‌تک یا لندتک\nدرگاه پرداخت و PSP\nبیمه و خدمات مالی\nسازمان، حقوق و دستمزد\nسایر کسب‌وکارها" );
	$biztypes    = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="ib-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="ib-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'iblead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'iblead', 'heading', __( 'فرم درخواست فعال‌سازی وب‌سرویس استعلام شبا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'iblead', 'text', __( 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس استعلام اطلاعات مالی (شبا)، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_ib_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'iblead', 'form_title', __( 'درخواست وب‌سرویس استعلام شبا', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'iblead', 'form_hint', __( 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="iban-sheba-lp">
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
	              <span><?php esc_html_e( 'وب‌سرویس استعلام شبا فقط برای کسب‌وکارهای مجاز ارائه می‌شود. اگر به‌صورت شخصی به خدمات احراز هویت نیاز دارید، ', 'uid-theme' ); ?><a href="<?php echo esc_url( home_url( '/uid-plus/' ) ); ?>"><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></a> <?php esc_html_e( 'یا', 'uid-theme' ); ?>
	                <a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'گزینه درست شماست.', 'uid-theme' ); ?></span></div>
	            <button class="ib-btn btn-cta ib-btn-block" type="button" data-submit><?php echo uid_ib_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'iblead', 'submit_text', __( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_ib_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'iblead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_ib_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'iblead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'iblead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_ib_page_id() {
	$page_id = (int) get_option( 'uid_ib_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_IBAN_SHEBA_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_ib_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_ib_page() {
	if ( uid_get_ib_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'استعلام اطلاعات مالی (شبا)', 'uid-theme' ),
		'post_name'   => 'api-inquiry-iban',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_IBAN_SHEBA_TEMPLATE );
	update_option( 'uid_ib_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_ib_slug_setting() {
	register_setting( 'uid_ib_group', 'uid_ib_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_ib_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_ib_page_slug_section', '', '__return_false', 'uid_ib_layout' );
	add_settings_field( 'uid_ib_page_slug', __( 'آدرس (اسلاگ) صفحه استعلام شبا', 'uid-theme' ), 'uid_field_ib_page_slug', 'uid_ib_layout', 'uid_ib_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_ib_slug_setting' );

function uid_field_ib_page_slug( $args ) {
	$page_id = uid_get_ib_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_ib_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_ib_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه استعلام شبا هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_ib_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_ib_page_id();

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
function uid_register_ib_settings() {
	register_setting( 'uid_ib_group', 'uid_ib_layout', array(
		'sanitize_callback' => 'uid_sanitize_ib_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_ib_layout_main', '', '__return_false', 'uid_ib_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_ib_layout', 'uid_ib_layout_main', array(
		'option_name' => 'uid_ib_layout', 'registry_fn' => 'uid_ib_sections_registry', 'layout_fn' => 'uid_get_ib_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibhero', array( 'sanitize_callback' => 'uid_sanitize_section_ibhero', 'default' => array() ) );
	add_settings_section( 'uid_section_ibhero_main', '', '__return_false', 'uid_section_ibhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس استعلام اطلاعات مالی (شبا) · inquiry/iban/v2' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'heading', 'default' => 'هر واریز به شبای اشتباه،<mark>دو بار از جیب شما می‌رود.</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'text', 'default' => 'یک‌بار بابت پول بلوکه‌شده و پیگیری بانکی، یک‌بار بابت اعتمادی که از کاربر گرفته‌اید. وب‌سرویس استعلام شبای یوآیدی با دریافت شماره شبا، نام و نام خانوادگی صاحب حساب، کد ملی، شماره حساب، نام بانک و وضعیت فعال یا مسدود بودن حساب را در لحظه از شبکه بانکی برمی‌گرداند — پیش از آنکه ریالی جابه‌جا شود.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'btn1_text', 'default' => 'درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'group' => 'uid_section_ibhero', 'key' => 'tags', 'default' => "پاسخ لحظه‌ای\nهزینه فقط بابت استعلام موفق\nاتصال در کمتر از یک روز کاری" ) );
	add_settings_field( 'samples', __( 'شبای‌های نمونه ویجت زنده', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibhero', 'uid_section_ibhero_main', array(
		'group' => 'uid_section_ibhero', 'key' => 'samples', 'default' => uid_default_ib_samples(), 'add_label' => __( 'افزودن شبای نمونه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'iban', 'type' => 'text', 'label' => __( 'شماره شبا (بدون IR)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'blocked', 'type' => 'select', 'label' => __( 'نمایش به‌عنوان نمونه مسدود', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_ibhero', 'uid_section_ibhero_main', array( 'text' => __( 'ویجت زنده استعلام شبا فقط در «شبای‌های نمونه» بالا قابل‌ویرایش است؛ منطق محاسبه پاسخ در assets/js/iban-sheba-page.js می‌ماند.', 'uid-theme' ) ) );

	/* ---------------- باند اعتماد کوتاه ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibtrust', array( 'sanitize_callback' => 'uid_sanitize_section_ibtrust', 'default' => array() ) );
	add_settings_section( 'uid_section_ibtrust_main', '', '__return_false', 'uid_section_ibtrust' );
	add_settings_field( 'items', __( 'آمار (خانه‌های باند)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibtrust', 'uid_section_ibtrust_main', array(
		'group' => 'uid_section_ibtrust', 'key' => 'items', 'default' => uid_default_ib_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'numeric', 'type' => 'select', 'label' => __( 'استایل عددی بزرگ', 'uid-theme' ), 'options' => array( '' => __( 'خیر (متن)', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );

	/* ---------------- محاسبه‌گر زیان ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibloss', array( 'sanitize_callback' => 'uid_sanitize_section_ibloss', 'default' => array() ) );
	add_settings_section( 'uid_section_ibloss_main', '', '__return_false', 'uid_section_ibloss' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'eyebrow', 'default' => 'قبل از اینکه ادامه بدهید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'heading', 'default' => 'شبای اشتباه، ماهانه چقدر از شما می‌گیرد؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'text', 'default' => 'اعداد خودتان را وارد کنید. ما فقط حساب می‌کنیم. هر واریز ناموفق یعنی پول بلوکه، یک تیکت پشتیبانی، یک پیگیری بانکی و یک کاربر ناراضی.' ) );
	add_settings_field( 'notes', __( 'کادرهای توضیحی کنار محاسبه‌گر', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibloss', 'uid_section_ibloss_main', array(
		'group' => 'uid_section_ibloss', 'key' => 'notes', 'default' => uid_default_ib_loss_notes(), 'add_label' => __( 'افزودن کادر', 'uid-theme' ),
		'fields' => array( array( 'key' => 'heading', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'cta_heading', 'default' => 'ترجیح می‌دهید همین حالا صحبت کنید؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'group' => 'uid_section_ibloss', 'key' => 'cta_text', 'default' => 'کارشناس فنی یوآیدی در یک تماس، حجم استعلام و سناریوی شما را بررسی می‌کند.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ibloss', 'uid_section_ibloss_main', array( 'text' => __( 'منطق محاسبه‌گر زیان (اسلایدرها) در assets/js/iban-sheba-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- مسیر اتصال ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibstart', array( 'sanitize_callback' => 'uid_sanitize_section_ibstart', 'default' => array() ) );
	add_settings_section( 'uid_section_ibstart_main', '', '__return_false', 'uid_section_ibstart' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibstart', 'uid_section_ibstart_main', array( 'group' => 'uid_section_ibstart', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibstart', 'uid_section_ibstart_main', array( 'group' => 'uid_section_ibstart', 'key' => 'eyebrow', 'default' => 'مسیر اتصال' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibstart', 'uid_section_ibstart_main', array( 'group' => 'uid_section_ibstart', 'key' => 'heading', 'default' => 'از تماس تا اولین استعلام واقعی، کمتر از یک روز کاری' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibstart', 'uid_section_ibstart_main', array( 'group' => 'uid_section_ibstart', 'key' => 'text', 'default' => 'تیم فنی یوآیدی مسیر را برای شما کوتاه می‌کند: مستندات شفاف، نمونه‌کد آماده و پشتیبانی اختصاصی در تمام مراحل.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibstart', 'uid_section_ibstart_main', array(
		'group' => 'uid_section_ibstart', 'key' => 'steps', 'default' => uid_default_ib_start(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ibstart', 'uid_section_ibstart_main', array( 'group' => 'uid_section_ibstart', 'key' => 'btn1_text', 'default' => 'شروع مرحله اول — همین حالا' ) );

	/* ---------------- فصل ۱: ورودی/خروجی ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibout', array( 'sanitize_callback' => 'uid_sanitize_section_ibout', 'default' => array() ) );
	add_settings_section( 'uid_section_ibout_main', '', '__return_false', 'uid_section_ibout' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'eyebrow', 'default' => 'داده‌های خروجی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'heading', 'default' => 'یک عدد می‌فرستید، یک پرونده مالی می‌گیرید' ) );
	add_settings_field( 'shared_heading', __( 'عنوان کادر حساب‌های مشترک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'shared_heading', 'default' => 'حساب‌های مشترک هم پشتیبانی می‌شوند' ) );
	add_settings_field( 'shared_text', __( 'توضیح کادر حساب‌های مشترک', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'shared_text', 'default' => 'اطلاعات صاحبان حساب در آرایه‌ای با عنوان owners برگردانده می‌شود. اگر حساب یک صاحب داشته باشد، این آرایه تک‌عضوی است؛ برای حساب‌های مشترک، همه دارندگان فهرست می‌شوند.' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'fold_title', 'default' => 'ورودی و خروجی سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibout', 'uid_section_ibout_main', array( 'group' => 'uid_section_ibout', 'key' => 'fold_teaser', 'default' => 'یک شماره شبا وارد می‌شود، هفت فیلد برمی‌گردد' ) );

	/* ---------------- فصل ۲: الگوریتم در برابر واقعیت ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibvs', array( 'sanitize_callback' => 'uid_sanitize_section_ibvs', 'default' => array() ) );
	add_settings_section( 'uid_section_ibvs_main', '', '__return_false', 'uid_section_ibvs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'eyebrow', 'default' => 'یک اشتباه رایج و پرهزینه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'heading', 'default' => 'الگوریتم شبا فقط املا را چک می‌کند، نه واقعیت را' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'text', 'default' => 'بسیاری از تیم‌های فنی فکر می‌کنند بررسی چک‌دیجیت IBAN یعنی «شبا معتبر است». این دقیقاً جایی است که پول‌ها گم می‌شوند.' ) );
	add_settings_field( 'bad_tag', __( 'برچسب کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'bad_tag', 'default' => 'فقط بررسی ساختار و چک‌دیجیت' ) );
	add_settings_field( 'bad_title', __( 'عنوان کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'bad_title', 'default' => 'اعتبارسنجی محلی (سمت خودتان)' ) );
	add_settings_field( 'bad_text', __( 'توضیح کارت بد', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'bad_text', 'default' => 'یک تابع چند خطی در بک‌اند شما تأیید می‌کند که ۲۴ رقم از نظر ریاضی درست چیده شده‌اند. همین و بس.' ) );
	add_settings_field( 'bad_items', __( 'موارد کارت بد (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'bad_items', 'default' => implode( "\n", uid_default_ib_vs_bad_items() ), 'rows' => 4 ) );
	add_settings_field( 'good_tag', __( 'برچسب کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'good_tag', 'default' => 'استعلام لحظه‌ای از شبکه بانکی' ) );
	add_settings_field( 'good_title', __( 'عنوان کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'good_title', 'default' => 'وب‌سرویس استعلام شبا یوآیدی' ) );
	add_settings_field( 'good_text', __( 'توضیح کارت خوب', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'good_text', 'default' => 'شبا را به شبکه بانکی می‌سپارد و وضعیت واقعی حساب به‌همراه مشخصات هویتی دارنده آن را برمی‌گرداند.' ) );
	add_settings_field( 'good_items', __( 'موارد کارت خوب (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'good_items', 'default' => implode( "\n", uid_default_ib_vs_good_items() ), 'rows' => 4 ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'fold_title', 'default' => 'چرا بررسی فرمت شبا کافی نیست' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibvs', 'uid_section_ibvs_main', array( 'group' => 'uid_section_ibvs', 'key' => 'fold_teaser', 'default' => 'تفاوت اعتبارسنجی ریاضی با استعلام واقعی از بانک' ) );

	/* ---------------- فصل ۳: نحوه کار سرویس ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibhow', array( 'sanitize_callback' => 'uid_sanitize_section_ibhow', 'default' => array() ) );
	add_settings_section( 'uid_section_ibhow_main', '', '__return_false', 'uid_section_ibhow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibhow', 'uid_section_ibhow_main', array( 'group' => 'uid_section_ibhow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhow', 'uid_section_ibhow_main', array( 'group' => 'uid_section_ibhow', 'key' => 'eyebrow', 'default' => 'پشت صحنه سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhow', 'uid_section_ibhow_main', array( 'group' => 'uid_section_ibhow', 'key' => 'heading', 'default' => 'از ارسال شبا تا پاسخ ساختاریافته' ) );
	add_settings_field( 'items', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibhow', 'uid_section_ibhow_main', array(
		'group' => 'uid_section_ibhow', 'key' => 'items', 'default' => uid_default_ib_how(), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'send' => __( 'ارسال', 'uid-theme' ), 'bank' => __( 'بانک', 'uid-theme' ), 'reply' => __( 'پاسخ', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'برچسب کوتاه پایین کارت', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhow', 'uid_section_ibhow_main', array( 'group' => 'uid_section_ibhow', 'key' => 'fold_title', 'default' => 'سه مرحله عملکرد سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibhow', 'uid_section_ibhow_main', array( 'group' => 'uid_section_ibhow', 'key' => 'fold_teaser', 'default' => 'ارسال شبا، اتصال به شبکه بانکی، پاسخ ساختاریافته' ) );

	/* ---------------- فصل ۴: کاربردها ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibwho', array( 'sanitize_callback' => 'uid_sanitize_section_ibwho', 'default' => array() ) );
	add_settings_section( 'uid_section_ibwho_main', '', '__return_false', 'uid_section_ibwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'eyebrow', 'default' => 'کجا به کار می‌آید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'heading', 'default' => 'هر جا که پول از سامانه شما بیرون می‌رود' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'text', 'default' => 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.' ) );
	add_settings_field( 'items', __( 'کارت‌های صنعت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibwho', 'uid_section_ibwho_main', array(
		'group' => 'uid_section_ibwho', 'key' => 'items', 'default' => uid_default_ib_who(), 'add_label' => __( 'افزودن صنعت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'crypto' => __( 'صرافی/بروکر', 'uid-theme' ), 'market' => __( 'مارکت‌پلیس', 'uid-theme' ), 'refund' => __( 'بازپرداخت', 'uid-theme' ), 'lend' => __( 'لندتک/نئوبانک', 'uid-theme' ), 'ins' => __( 'بیمه', 'uid-theme' ), 'payroll' => __( 'حقوق و دستمزد', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب کوتاه (روی تب)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'win', 'type' => 'text', 'label' => __( 'نتیجه/دستاورد', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'fold_title', 'default' => 'کاربردهای وب‌سرویس استعلام شبا' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibwho', 'uid_section_ibwho_main', array( 'group' => 'uid_section_ibwho', 'key' => 'fold_teaser', 'default' => 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید' ) );

	/* ---------------- فصل ۵: ریسک‌ها ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibrisk', array( 'sanitize_callback' => 'uid_sanitize_section_ibrisk', 'default' => array() ) );
	add_settings_section( 'uid_section_ibrisk_main', '', '__return_false', 'uid_section_ibrisk' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array( 'group' => 'uid_section_ibrisk', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array( 'group' => 'uid_section_ibrisk', 'key' => 'eyebrow', 'default' => 'ریسک‌هایی که امروز باز هستند' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array( 'group' => 'uid_section_ibrisk', 'key' => 'heading', 'default' => 'هر کدام از این چهار مورد، یک بار هم که اتفاق بیفتد گران است' ) );
	add_settings_field( 'items', __( 'موارد ریسک', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array(
		'group' => 'uid_section_ibrisk', 'key' => 'items', 'default' => uid_default_ib_risks(), 'add_label' => __( 'افزودن ریسک', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان ریسک', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح ریسک', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'راه‌حل با یوآیدی', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array( 'group' => 'uid_section_ibrisk', 'key' => 'fold_title', 'default' => 'چهار نشتی که این سرویس می‌بندد' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibrisk', 'uid_section_ibrisk_main', array( 'group' => 'uid_section_ibrisk', 'key' => 'fold_teaser', 'default' => 'از حساب مسدود تا کلاهبرداری با شبای شخص ثالث' ) );

	/* ---------------- فصل ۶: تیم متخصص ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibteam', array( 'sanitize_callback' => 'uid_sanitize_section_ibteam', 'default' => array() ) );
	add_settings_section( 'uid_section_ibteam_main', '', '__return_false', 'uid_section_ibteam' );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'eyebrow', 'default' => 'کنار چه تیمی وصل می‌شوید' ) );
	add_settings_field( 'heading', __( 'عنوان (بعد از «یک API»)', 'uid-theme' ), 'uid_field_text', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'heading', 'default' => ' نمی‌خرید؛ یک تیم فنی کنارتان می‌گذارید' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibteam', 'uid_section_ibteam_main', array(
		'group' => 'uid_section_ibteam', 'key' => 'rows', 'default' => uid_default_ib_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'connect' => __( 'اتصال', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'report' => __( 'گزارش', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به مشتریان وب‌سرویس' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_ib_pledge_items() ), 'rows' => 4 ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'fold_title', 'default' => 'تیم متخصص و تعهد پشتیبانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibteam', 'uid_section_ibteam_main', array( 'group' => 'uid_section_ibteam', 'key' => 'fold_teaser', 'default' => 'با چه کسانی وصل می‌شوید و چه چیزی تضمین می‌شود' ) );

	/* ---------------- فصل ۷: مستندات فنی ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibdev', array( 'sanitize_callback' => 'uid_sanitize_section_ibdev', 'default' => array() ) );
	add_settings_section( 'uid_section_ibdev_main', '', '__return_false', 'uid_section_ibdev' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'eyebrow', 'default' => 'برای تیم فنی شما' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'heading', 'default' => 'یک متد، یک فیلد ورودی، یک پاسخ استاندارد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'text', 'default' => 'با ارسال شماره شبا در بدنه درخواست، اطلاعات کامل حساب در بدنه پاسخ بازگردانده می‌شود. احراز هویت کسب‌وکار از طریق businessId و businessToken انجام می‌شود که هر دو توسط یوآیدی در اختیار شما قرار می‌گیرد.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'btn1_text', 'default' => 'دریافت کلید وب‌سرویس' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'text' => __( 'نمونه‌کدهای درخواست/پاسخ، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/iban-sheba-page.php مراجعه کنید).', 'uid-theme' ) ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'fold_title', 'default' => 'مستندات فنی و نمونه کد' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibdev', 'uid_section_ibdev_main', array( 'group' => 'uid_section_ibdev', 'key' => 'fold_teaser', 'default' => 'نمونه درخواست و پاسخ واقعی سرویس، با دکمه کپی' ) );

	/* ---------------- فصل ۸: سرویس‌های مکمل ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibxsell', array( 'sanitize_callback' => 'uid_sanitize_section_ibxsell', 'default' => array() ) );
	add_settings_section( 'uid_section_ibxsell_main', '', '__return_false', 'uid_section_ibxsell' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'eyebrow', 'default' => 'در کنار این سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'heading', 'default' => 'سه وب‌سرویسی که معمولاً با استعلام شبا با هم فعال می‌شوند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'text', 'default' => 'اگر بیش از یکی را نیاز دارید، در همان مشاوره اول پلن ترکیبی با تعرفه بهتر برایتان بسته می‌شود.' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array(
		'group' => 'uid_section_ibxsell', 'key' => 'items', 'default' => uid_default_ib_xsell(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'card' => __( 'کارت', 'uid-theme' ), 'match' => __( 'تطابق', 'uid-theme' ), 'cardid' => __( 'کارت + کد ملی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'fold_title', 'default' => 'سرویس‌های مکمل و مرتبط' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibxsell', 'uid_section_ibxsell_main', array( 'group' => 'uid_section_ibxsell', 'key' => 'fold_teaser', 'default' => 'تبدیل کارت به شبا، تطبیق شبا و کد ملی، تطبیق کارت و کد ملی' ) );

	/* ---------------- فصل ۹: سوالات متداول ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibfaq', array( 'sanitize_callback' => 'uid_sanitize_section_ibfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_ibfaq_main', '', '__return_false', 'uid_section_ibfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array( 'group' => 'uid_section_ibfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array( 'group' => 'uid_section_ibfaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array( 'group' => 'uid_section_ibfaq', 'key' => 'heading', 'default' => 'سوالات متداول وب‌سرویس استعلام شبا' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array(
		'group' => 'uid_section_ibfaq', 'key' => 'items', 'default' => uid_default_ib_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array( 'group' => 'uid_section_ibfaq', 'key' => 'fold_title', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibfaq', 'uid_section_ibfaq_main', array( 'group' => 'uid_section_ibfaq', 'key' => 'fold_teaser', 'default' => 'ورودی، خروجی، هزینه استعلام ناموفق و زمان پاسخ‌دهی' ) );

	/* ---------------- مزایای رقابتی ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibadv', array( 'sanitize_callback' => 'uid_sanitize_section_ibadv', 'default' => array() ) );
	add_settings_section( 'uid_section_ibadv_main', '', '__return_false', 'uid_section_ibadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibadv', 'uid_section_ibadv_main', array( 'group' => 'uid_section_ibadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibadv', 'uid_section_ibadv_main', array( 'group' => 'uid_section_ibadv', 'key' => 'eyebrow', 'default' => 'مزایای رقابتی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibadv', 'uid_section_ibadv_main', array( 'group' => 'uid_section_ibadv', 'key' => 'heading', 'default' => 'چرا کسب‌وکارها استعلام شبا را از یوآیدی می‌گیرند' ) );
	add_settings_field( 'items', __( 'آیتم‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ibadv', 'uid_section_ibadv_main', array(
		'group' => 'uid_section_ibadv', 'key' => 'items', 'default' => uid_default_ib_adv(), 'add_label' => __( 'افزودن مزیت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'shield' => __( 'امنیت', 'uid-theme' ), 'redundant' => __( 'زیرساخت پشتیبان', 'uid-theme' ), 'fast' => __( 'سرعت', 'uid-theme' ), 'report' => __( 'گزارش', 'uid-theme' ), 'lock' => __( 'قفل/انطباق', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عنوان کوتاه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد نوار پیشرفت (عدد بین ۰ تا ۱۰۰)', 'uid-theme' ) ),
			array( 'key' => 'feat', 'type' => 'select', 'label' => __( 'کارت برجسته (بزرگ‌تر)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'wide', 'type' => 'select', 'label' => __( 'عرض دوبرابر', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_ibadv', 'uid_section_ibadv_main', array( 'group' => 'uid_section_ibadv', 'key' => 'foot_text', 'default' => 'هر پنج مورد بالا در همان اولین جلسه فنی قابل بررسی است.' ) );

	/* ---------------- پوشش بانک‌ها ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibbanks', array( 'sanitize_callback' => 'uid_sanitize_section_ibbanks', 'default' => array() ) );
	add_settings_section( 'uid_section_ibbanks_main', '', '__return_false', 'uid_section_ibbanks' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibbanks', 'uid_section_ibbanks_main', array( 'group' => 'uid_section_ibbanks', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibbanks', 'uid_section_ibbanks_main', array( 'group' => 'uid_section_ibbanks', 'key' => 'eyebrow', 'default' => 'پوشش سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibbanks', 'uid_section_ibbanks_main', array( 'group' => 'uid_section_ibbanks', 'key' => 'heading', 'default' => 'شبای صادرشده از بانک‌ها و مؤسسات کشور' ) );
	add_settings_field( 'items', __( 'فهرست بانک‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibbanks', 'uid_section_ibbanks_main', array( 'group' => 'uid_section_ibbanks', 'key' => 'items', 'default' => implode( "\n", uid_default_ib_banks() ), 'rows' => 10 ) );
	add_settings_field( 'more_text', __( 'برچسب پایانی', 'uid-theme' ), 'uid_field_text', 'uid_section_ibbanks', 'uid_section_ibbanks_main', array( 'group' => 'uid_section_ibbanks', 'key' => 'more_text', 'default' => 'و سایر بانک‌ها و مؤسسات اعتباری' ) );

	/* ---------------- محاسبه‌گر هزینه ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_ibprice', array( 'sanitize_callback' => 'uid_sanitize_section_ibprice', 'default' => array() ) );
	add_settings_section( 'uid_section_ibprice_main', '', '__return_false', 'uid_section_ibprice' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ibprice', 'uid_section_ibprice_main', array( 'group' => 'uid_section_ibprice', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ibprice', 'uid_section_ibprice_main', array( 'group' => 'uid_section_ibprice', 'key' => 'eyebrow', 'default' => 'تعرفه و مدل محاسبه هزینه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ibprice', 'uid_section_ibprice_main', array( 'group' => 'uid_section_ibprice', 'key' => 'heading', 'default' => 'فقط بابت استعلام‌های موفق پرداخت می‌کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ibprice', 'uid_section_ibprice_main', array( 'group' => 'uid_section_ibprice', 'key' => 'text', 'default' => 'حجم استعلام ماهانه خود را مشخص کنید تا پله تعرفه و تخفیف پلکانی متناسب با آن را ببینید. استعلام‌های ناموفق یا اشتباه هزینه‌ای ندارند.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ibprice', 'uid_section_ibprice_main', array( 'text' => __( 'منطق محاسبه‌گر (پله‌های تخفیف و قیمت پایه) در assets/js/iban-sheba-page.js تعریف شده و برای انتشار نهایی باید با تعرفه واقعی جایگزین شود؛ از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- بند تماس پایانی ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_iblastcall', array( 'sanitize_callback' => 'uid_sanitize_section_iblastcall', 'default' => array() ) );
	add_settings_section( 'uid_section_iblastcall_main', '', '__return_false', 'uid_section_iblastcall' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_iblastcall', 'uid_section_iblastcall_main', array( 'group' => 'uid_section_iblastcall', 'key' => 'heading', 'default' => 'هر روزی که این سرویس فعال نیست، هزینه‌اش را پرداخت می‌کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_iblastcall', 'uid_section_iblastcall_main', array( 'group' => 'uid_section_iblastcall', 'key' => 'text', 'default' => 'واریزهای ناموفق منتظر تصمیم شما نمی‌مانند. یک تماس کوتاه کافی است تا کارشناس یوآیدی حجم استعلام و سناریوی شما را بررسی کند و دسترسی سندباکس را فعال کند.' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_iblastcall', 'uid_section_iblastcall_main', array( 'group' => 'uid_section_iblastcall', 'key' => 'btn2_text', 'default' => 'فرم درخواست سرویس' ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_ib_group', 'uid_section_iblead', array( 'sanitize_callback' => 'uid_sanitize_section_iblead', 'default' => array() ) );
	add_settings_section( 'uid_section_iblead_main', '', '__return_false', 'uid_section_iblead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'heading', 'default' => 'فرم درخواست فعال‌سازی وب‌سرویس استعلام شبا' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس استعلام اطلاعات مالی (شبا)، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nدسترسی به محیط سندباکس پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nتعرفه پلکانی متناسب با حجم استعلام شما" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'form_title', 'default' => 'درخواست وب‌سرویس استعلام شبا' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'form_hint', 'default' => 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'biztype_options', 'default' => "صرافی ارز دیجیتال یا بروکر\nمارکت‌پلیس و فروشگاه اینترنتی\nبانک، فین‌تک یا لندتک\nدرگاه پرداخت و PSP\nبیمه و خدمات مالی\nسازمان، حقوق و دستمزد\nسایر کسب‌وکارها" ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_iblead', 'uid_section_iblead_main', array( 'group' => 'uid_section_iblead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_ib_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_ibhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'samples'   => uid_sanitize_repeater_rows( $input['samples'] ?? '[]', array(
			array( 'key' => 'iban', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'blocked', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ibtrust( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'numeric', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ibloss( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'notes'       => uid_sanitize_repeater_rows( $input['notes'] ?? '[]', array(
			array( 'key' => 'heading', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_ibstart( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
	);
}

function uid_sanitize_section_ibout( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'shared_heading' => sanitize_text_field( $input['shared_heading'] ?? '' ),
		'shared_text'    => sanitize_textarea_field( $input['shared_text'] ?? '' ),
		'fold_title'     => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'    => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ibvs( $input ) {
	return array(
		'title_tag'  => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'    => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'    => sanitize_text_field( $input['heading'] ?? '' ),
		'text'       => sanitize_textarea_field( $input['text'] ?? '' ),
		'bad_tag'    => sanitize_text_field( $input['bad_tag'] ?? '' ),
		'bad_title'  => sanitize_text_field( $input['bad_title'] ?? '' ),
		'bad_text'   => sanitize_textarea_field( $input['bad_text'] ?? '' ),
		'bad_items'  => sanitize_textarea_field( $input['bad_items'] ?? '' ),
		'good_tag'   => sanitize_text_field( $input['good_tag'] ?? '' ),
		'good_title' => sanitize_text_field( $input['good_title'] ?? '' ),
		'good_text'  => sanitize_textarea_field( $input['good_text'] ?? '' ),
		'good_items' => sanitize_textarea_field( $input['good_items'] ?? '' ),
		'fold_title' => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ibhow( $input ) {
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

function uid_sanitize_section_ibwho( $input ) {
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

function uid_sanitize_section_ibrisk( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea' ),
			array( 'key' => 'fix_text', 'type' => 'textarea' ),
		) ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ibteam( $input ) {
	return array(
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
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

function uid_sanitize_section_ibdev( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text'   => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ibxsell( $input ) {
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

function uid_sanitize_section_ibfaq( $input ) {
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

function uid_sanitize_section_ibadv( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'percent', 'type' => 'text' ),
			array( 'key' => 'feat', 'type' => 'text' ),
			array( 'key' => 'wide', 'type' => 'text' ),
		) ),
		'foot_text' => sanitize_text_field( $input['foot_text'] ?? '' ),
	);
}

function uid_sanitize_section_ibbanks( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
		'more_text' => sanitize_text_field( $input['more_text'] ?? '' ),
	);
}

function uid_sanitize_section_ibprice( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_iblastcall( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_iblead( $input ) {
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
function uid_render_ib_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس استعلام شبا', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اطلاعات کسب‌وکارتان را بگذارید؛ کارشناس یوآیدی همین امروز تماس می‌گیرد، پلن متناسب با حجم استعلام ماهانه شما را پیشنهاد می‌دهد و دسترسی به محیط سندباکس و کلید API استعلام شبا را فعال می‌کند.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'صرافی ارز دیجیتال یا بروکر', 'uid-theme' ); ?></option><option><?php esc_html_e( 'مارکت‌پلیس و فروشگاه اینترنتی', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بانک، فین‌تک یا لندتک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'درگاه پرداخت و PSP', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بیمه و خدمات مالی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سازمان، حقوق و دستمزد', 'uid-theme' ); ?></option>
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
