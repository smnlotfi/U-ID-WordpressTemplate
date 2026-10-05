<?php
/**
 * صفحه اختصاصی «تبدیل شماره کارت به شبا» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «ci» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * ۹ سکشن این صفحه (what/vs/how/who/gain/team/dev/xsell/faq) سیستم «فصل تاخوردنی +
 * خواننده فصل» موبایل دارند — دقیقاً مثل inc/ekyc-liveness-page.php (نوار چسبان بالای
 * فصل باز، دکمه‌های قبلی/بعدی و نوار پیشرفت مطالعه). نگاه کنید به
 * uid_ci_folded_slugs()/uid_render_ci_foldbar().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_CARD_TO_IBAN_TEMPLATE', 'template-card-to-iban.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_ci_sections_registry() {
	return array(
		'cihero'     => array( 'label' => __( 'هیرو + ویجت زنده تبدیل کارت به شبا', 'uid-theme' ),    'icon' => 'dashicons-star-filled' ),
		'citrust'    => array( 'label' => __( 'باند اعتماد کوتاه (۴ آمار)', 'uid-theme' ),             'icon' => 'dashicons-chart-bar' ),
		'citldr'     => array( 'label' => __( 'کپسول ۳۰ ثانیه‌ای (ویژه موبایل)', 'uid-theme' ),        'icon' => 'dashicons-smartphone' ),
		'ciloss'     => array( 'label' => __( 'محاسبه‌گر ریزش فیلد شبا', 'uid-theme' ),                 'icon' => 'dashicons-calculator' ),
		'ciwhat'     => array( 'label' => __( 'فصل ۱ — معرفی سرویس', 'uid-theme' ),                    'icon' => 'dashicons-editor-code' ),
		'civs'       => array( 'label' => __( 'فصل ۲ — مقایسه سناریوی کاربر', 'uid-theme' ),           'icon' => 'dashicons-image-flip-horizontal' ),
		'cihow'      => array( 'label' => __( 'فصل ۳ — نحوه کار سرویس (مراحل تعاملی)', 'uid-theme' ),  'icon' => 'dashicons-randomize' ),
		'ciwho'      => array( 'label' => __( 'فصل ۴ — کاربردها (انتخابگر صنعت)', 'uid-theme' ),       'icon' => 'dashicons-groups' ),
		'cigain'     => array( 'label' => __( 'فصل ۵ — مزایا + پوشش بانکی', 'uid-theme' ),             'icon' => 'dashicons-awards' ),
		'citeam'     => array( 'label' => __( 'فصل ۶ — تیم متخصص + تعهدنامه', 'uid-theme' ),           'icon' => 'dashicons-businessperson' ),
		'cidev'      => array( 'label' => __( 'فصل ۷ — مستندات فنی و نمونه‌کد', 'uid-theme' ),         'icon' => 'dashicons-editor-code' ),
		'cixsell'    => array( 'label' => __( 'فصل ۸ — سرویس‌های مکمل', 'uid-theme' ),                 'icon' => 'dashicons-networking' ),
		'cifaq'      => array( 'label' => __( 'فصل ۹ — سوالات متداول', 'uid-theme' ),                  'icon' => 'dashicons-editor-help' ),
		'ciadv'      => array( 'label' => __( 'مشخصات عملیاتی (پنل تیره)', 'uid-theme' ),              'icon' => 'dashicons-shield' ),
		'ciprice'    => array( 'label' => __( 'محاسبه‌گر تعرفه ماهانه', 'uid-theme' ),                  'icon' => 'dashicons-money-alt' ),
		'cilastcall' => array( 'label' => __( 'بند تماس میان‌صفحه', 'uid-theme' ),                     'icon' => 'dashicons-megaphone' ),
		'cilead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                   'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_ci_layout() {
	$registry = uid_ci_sections_registry();
	$saved    = get_option( 'uid_ci_layout', array() );

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

function uid_sanitize_ci_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_ci_sections_registry();
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
function uid_ci_folded_slugs() {
	return array( 'ciwhat', 'civs', 'cihow', 'ciwho', 'cigain', 'citeam', 'cidev', 'cixsell', 'cifaq' );
}

/**
 * اسلاگ‌های فصل‌هایی که در mockup ویژگی data-fold-hot دارند (نقطه داغ کوچک روی
 * ردیف تاخورده موبایل).
 */
function uid_ci_hot_slugs() {
	return array( 'ciwhat', 'civs', 'ciwho', 'cidev' );
}

function uid_render_ci_sections() {
	$folded    = uid_ci_folded_slugs();
	$layout    = uid_get_ci_layout();
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
			uid_render_ci_foldbar( $folded_on );
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
 * نوار پیشرفت مطالعه (fold-meter) توسط assets/js/card-to-iban-page.js پر می‌شود.
 */
function uid_render_ci_foldbar( $count ) {
	?>
	<div class="foldbar" id="ciFoldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <button type="button" id="ciFoldAll"><?php esc_html_e( 'باز کردن همه', 'uid-theme' ); ?></button>
	  <span class="fold-meter"><i id="ciFoldMeter"></i></span>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_ci_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_ci_x_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_ci_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_ci_info_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>';
}
function uid_ci_clock_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>';
}
function uid_ci_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .3 1.9.6 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.1a2 2 0 012.1-.5c.9.3 1.8.5 2.8.6a2 2 0 011.7 2z"/></svg>';
}
function uid_ci_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_ci_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
}
function uid_ci_shield_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
function uid_ci_success_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>';
}
function uid_ci_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
function uid_ci_chev_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/></svg>';
}
function uid_ci_who_icon_svg( $key ) {
	$icons = array(
		'crypto' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
		'market' => '<path d="M3 3h2l2.7 12.4a2 2 0 002 1.6h7.7a2 2 0 002-1.6L21 8H6"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/>',
		'psp'    => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'refund' => '<path d="M4 4v6h6"/><path d="M20 20v-6h-6"/><path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/>',
		'lend'   => '<path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3"/><path d="M7 10v11M12 10v11M17 10v11"/>',
	);
	return $icons[ $key ] ?? $icons['crypto'];
}
function uid_ci_who_color( $key ) {
	$colors = array( 'crypto' => 'warm', 'market' => '', 'psp' => 'navy', 'refund' => '', 'lend' => 'navy' );
	return $colors[ $key ] ?? '';
}
function uid_ci_gain_icon_svg( $key ) {
	$icons = array(
		'churn'   => '<path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/>',
		'human'   => '<path d="M20 6L9 17l-5-5"/>',
		'scale'   => '<path d="M4 20V10M10 20V4M16 20v-8M22 20v-5"/>',
		'fast'    => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'support' => '<path d="M16 20v-1.5a4 4 0 00-8 0V20"/><circle cx="12" cy="8" r="4"/>',
		'free'    => '<path d="M12 2v20M17 6H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
	);
	return $icons[ $key ] ?? $icons['churn'];
}
function uid_ci_gain_color( $key ) {
	$colors = array( 'churn' => 'warm', 'human' => '', 'scale' => 'navy', 'fast' => '', 'support' => 'navy', 'free' => 'warm' );
	return $colors[ $key ] ?? '';
}
function uid_ci_team_icon_svg( $key ) {
	$icons = array(
		'connect' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'report'  => '<path d="M4 20V10M10 20V4M16 20v-8M22 20v-5"/>',
		'doc'     => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v20H6.5A2.5 2.5 0 014 19.5z"/><path d="M8 7h8M8 11h6"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
	);
	return $icons[ $key ] ?? $icons['connect'];
}
function uid_ci_xsell_icon_svg( $key ) {
	$icons = array(
		'iban'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h5"/>',
		'match'  => '<path d="M20 6L9 17l-5-5"/><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>',
		'cardid' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="15" r="1.4"/>',
	);
	return $icons[ $key ] ?? $icons['iban'];
}
function uid_ci_adv_icon_svg( $key ) {
	$icons = array(
		'card'  => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'doc'   => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v20H6.5A2.5 2.5 0 014 19.5z"/><path d="M8 7h8"/>',
		'coin'  => '<path d="M12 2v20M17 6H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'clock' => '<path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/>',
		'bank'  => '<path d="M4 20V10M10 20V4M16 20v-8M22 20v-5"/>',
	);
	return $icons[ $key ] ?? $icons['card'];
}

/* =====================================================================
 * ۱) هیرو + ویجت زنده تبدیل کارت به شبا (شبیه‌سازی نمایشی، داده آن هاردکد است)
 * ===================================================================== */
function uid_default_ci_samples() {
	return array(
		array( 'card' => '6037991234567890', 'label' => 'کارت نمونه بانک ملی', 'blocked' => '' ),
		array( 'card' => '6104331234567890', 'label' => 'کارت نمونه بانک ملت', 'blocked' => '' ),
		array( 'card' => '6219861234567890', 'label' => 'کارت نمونه بانک سامان', 'blocked' => '' ),
		array( 'card' => '1111222233334444', 'label' => 'کارت نامعتبر', 'blocked' => '1' ),
	);
}
function uid_render_section_cihero() {
	$tag     = uid_section_tag( 'cihero', 'h1' );
	$tags    = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'cihero', 'tags', "کارت تمام بانک‌های عضو شتاب\nهزینه فقط بابت پاسخ موفق\nخروجی استاندارد JSON\nاتصال فنی در کمتر از یک روز کاری" ) ) ) );
	$samples = uid_section_val( 'cihero', 'samples', uid_default_ci_samples() );
	if ( ! is_array( $samples ) || empty( $samples ) ) $samples = uid_default_ci_samples();
	?>
	<section class="dark heroA" id="top">
	  <div class="ci-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌های استعلام', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'تبدیل شماره کارت به شبا', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="ci-eyebrow on-dark"><i></i><span><?php echo esc_html( uid_section_val( 'cihero', 'eyebrow', __( 'وب‌سرویس تبدیل شماره کارت به شبا · ', 'uid-theme' ) ) ); ?><span class="lat">Card Inquiry API</span></span></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'cihero', 'heading', __( 'کاربر شماره کارتش را حفظ است.<mark>شماره شبا را نه.</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo wp_kses( uid_section_val( 'cihero', 'text', __( 'همان ثانیه‌ای که از او شماره ۲۶ رقمی شبا را می‌خواهید، از سامانه شما بیرون می‌رود تا در اینترنت‌بانک دنبالش بگردد — و بخش بزرگی از آن‌ها دیگر برنمی‌گردند. وب‌سرویس تبدیل کارت به شبای یوآیدی شماره <b style="color:#7FE0EC">۱۶ رقمی کارت</b> را می‌گیرد و شبای متصل به همان حساب را <b style="color:#7FE0EC">مستقیم از شبکه بانکی</b> برمی‌گرداند.', 'uid-theme' ) ), array( 'b' => array( 'style' => array() ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="ci-btn btn-cta" data-open-modal><?php echo uid_ci_submit_icon(); ?><?php echo esc_html( uid_section_val( 'cihero', 'btn1_text', __( 'فرم درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ci-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ci_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- live card → IBAN widget -->
	      <div class="idw rv rv-d2" id="cdw">
	        <div class="idw-hd"><span class="live"></span><b><?php esc_html_e( 'تست زنده سرویس', 'uid-theme' ); ?></b>
	          <span class="mono">inquiry/card</span></div>

	        <div class="bsamples" id="cd_samples" aria-label="<?php esc_attr_e( 'نمونه شماره کارت', 'uid-theme' ); ?>">
	          <?php foreach ( $samples as $s ) :
	            $digits = preg_replace( '/\D/', '', $s['card'] ?? '' );
	          ?>
	          <button class="bsample<?php echo ! empty( $s['blocked'] ) ? ' blocked' : ''; ?>" type="button" data-card="<?php echo esc_attr( $digits ); ?>"><i></i><?php echo esc_html( $s['label'] ?? '' ); ?></button>
	          <?php endforeach; ?>
	        </div>

	        <div class="bstage">
	          <div class="bcard" id="cd_card">
	            <!-- FRONT: the sixteen digits -->
	            <div class="bface front">
	              <svg class="mesh" viewBox="0 0 320 200" preserveAspectRatio="none" aria-hidden="true">
	                <path d="M-10 60h120l30-30h190M-10 140h90l34 34h206M210 -10v46l30 30v134"/>
	                <path d="M60 210v-40l26-26h234M0 96h72"/>
	              </svg>
	              <span class="gloss"></span>

	              <div class="brow">
	                <span class="bchip" aria-hidden="true"><svg viewBox="0 0 42 32" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="1" y="1" width="40" height="30" rx="5"/><path d="M14 1v30M28 1v30M1 11h40M1 21h40"/></svg></span>
	                <span class="bmark"><span class="sh"><i></i><?php esc_html_e( 'شبکه شتاب', 'uid-theme' ); ?></span></span>
	              </div>

	              <div class="bnum">
	                <label class="sr" for="cd_in"><?php esc_html_e( 'شماره ۱۶ رقمی کارت', 'uid-theme' ); ?></label>
	                <input id="cd_in" type="text" inputmode="numeric" autocomplete="off"
	                       maxlength="19" placeholder="0000 0000 0000 0000" dir="ltr">
	                <span class="ul"><i id="cd_ul"></i></span>
	              </div>

	              <div class="bfoot">
	                <span><span class="lbl"><?php esc_html_e( 'بانک صادرکننده کارت', 'uid-theme' ); ?></span>
	                  <span class="bank mut" id="cd_bank"><?php esc_html_e( 'با ورود ۶ رقم اول شناسایی می‌شود', 'uid-theme' ); ?></span></span>
	                <span class="bcount" id="cd_cnt">۰/۱۶</span>
	              </div>
	              <div class="berr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 8v5M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
	                <span id="cd_err"><?php esc_html_e( 'این شماره کارت متعلق به هیچ بانک عضو شتابی نیست.', 'uid-theme' ); ?></span></div>
	            </div>

	            <!-- BACK: the IBAN -->
	            <div class="bface back">
	              <span class="bstripe" aria-hidden="true"></span>
	              <div class="bback-in">
	                <span class="bib-lbl"><i></i><?php esc_html_e( 'شماره شبا متصل به این کارت', 'uid-theme' ); ?></span>
	                <span class="bib" id="cd_iban"><span class="ir">IR</span> — </span>
	                <div class="bback-act">
	                  <button class="bcopy" type="button" id="cd_copy"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg><?php esc_html_e( 'کپی شبا', 'uid-theme' ); ?></button>
	                  <span class="bback-meta">۲۶ <?php esc_html_e( 'کاراکتر ·', 'uid-theme' ); ?> <b id="cd_bank2">—</b></span>
	                </div>
	              </div>
	            </div>
	          </div>
	        </div>

	        <div style="margin-block-start:2px">
	          <button class="ci-btn btn-cta btn-block" type="button" id="cd_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v6h6M20 20v-6h-6"/><path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/></svg><?php esc_html_e( 'تبدیل به شبا', 'uid-theme' ); ?></button>
	        </div>

	        <div style="margin-block-start:16px">
	          <div class="rswitch">
	            <button type="button" class="on" data-rview="g"><?php esc_html_e( 'نمای ساختاری', 'uid-theme' ); ?></button>
	            <button type="button" data-rview="j"><?php esc_html_e( 'پاسخ', 'uid-theme' ); ?> <span class="lat">JSON</span></button>
	            <span class="idbadge wait" id="cd_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span>
	          </div>

	          <div data-rpane="g" class="on">
	            <div class="idgrid" id="c_grid">
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'شماره شبا (خروجی سرویس)', 'uid-theme' ); ?></span><span class="v mut" id="c_iban">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'شماره کارت ارسالی', 'uid-theme' ); ?></span><span class="v mut" id="c_card">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'بانک صادرکننده', 'uid-theme' ); ?></span><span class="v mut" id="c_bank">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'کد وضعیت پاسخ', 'uid-theme' ); ?></span><span class="v mut" id="c_code">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'پیام وضعیت', 'uid-theme' ); ?></span><span class="v mut" id="c_msg">—</span></div>
	            </div>
	          </div>

	          <div data-rpane="j">
	            <pre class="rjson" id="c_json">{
  "responseContext": {
    "status": { "code": …, "message": "…" }
  },
  "cardNumber": "…",
  "iban": "…"
}</pre>
	          </div>

	          <div class="idmeta">
	            <span><?php esc_html_e( 'متد فراخوانی: ', 'uid-theme' ); ?><b>POST</b></span>
	            <span><?php esc_html_e( 'سرویس: ', 'uid-theme' ); ?><span class="mono">inquiry/card</span></span>
	          </div>
	        </div>

	        <div class="idw-note"><?php echo uid_ci_info_icon(); ?>
	          <span><?php esc_html_e( 'خروجی این نمایش با داده نمونه ساخته می‌شود؛ ساختار پاسخ دقیقاً همان چیزی است که سرویس برمی‌گرداند. برای استعلام واقعی از شبکه بانکی، کلید ', 'uid-theme' ); ?><span class="lat">API</span> <?php esc_html_e( 'لازم است.', 'uid-theme' ); ?></span></div>

		<!-- highest-intent moment on the page: a single field, right here -->
	        <div class="idw-lead" id="cdwLead">
	          <p><?php esc_html_e( 'همین تبدیل را داخل فرم برداشت یا تسویه خودتان می‌خواهید؟ ', 'uid-theme' ); ?><b><?php esc_html_e( 'شماره‌تان را بگذارید', 'uid-theme' ); ?></b><?php esc_html_e( '، کارشناس یوآیدی دسترسی تست را همین امروز فعال می‌کند.', 'uid-theme' ); ?></p>
	          <form class="micro" id="microForm" novalidate>
	            <div class="micro-fields">
	              <input type="hidden" name="source" value="card-iban-lp-hero">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ci-btn btn-cta" type="button" data-submit><?php esc_html_e( 'دریافت کلید تست', 'uid-theme' ); ?></button>
	              </div>
	            </div>
	            <div class="form-ok"><?php echo uid_ci_success_icon(); ?>
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
function uid_default_ci_trust() {
	return array(
		array( 'value' => '۱۶ رقم', 'label' => 'تنها داده‌ای که از کاربر می‌گیرید', 'numeric' => '' ),
		array( 'value' => '۲۶ رقم', 'label' => 'شبای استاندارد با پیشوند IR در پاسخ', 'numeric' => '' ),
		array( 'value' => '۰ تومان', 'label' => 'هزینه استعلام ناموفق یا کارت نامعتبر', 'numeric' => '' ),
		array( 'value' => 'کمتر از یک روز کاری', 'label' => 'زمان معمول اتصال فنی به سرویس', 'numeric' => '' ),
	);
}
function uid_render_section_citrust() {
	$items = uid_section_val( 'citrust', 'items', uid_default_ci_trust() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="ci-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $items as $it ) :
	        $is_num = ! empty( $it['numeric'] );
	      ?>
	      <div class="qstat"><span class="v<?php echo $is_num ? '' : ' txt'; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span><span class="l"><?php echo wp_kses( $it['label'] ?? '', array( 'span' => array( 'class' => array() ) ) ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) کپسول ۳۰ ثانیه‌ای — ویژه موبایل، چهار خط خلاصه + دکمه پرش نرم به محاسبه‌گر
 * ===================================================================== */
function uid_default_ci_tldr() {
	return array(
		'شماره کارت را با متد <b>POST</b> می‌فرستید، <b>شبای همان حساب</b> در پاسخ برمی‌گردد.',
		'کارت <b>تمام بانک‌ها و موسسات مالی مجاز</b> عضو شتاب پشتیبانی می‌شود.',
		'هزینه <b>فقط بابت استعلام موفق</b>؛ کارت نامعتبر و پاسخ ناموفق رایگان است.',
		'اتصال فنی معمولاً <b>کمتر از یک روز کاری</b> طول می‌کشد؛ سندباکس پیش از قرارداد فعال می‌شود.',
	);
}
function uid_render_section_citldr() {
	$items = uid_section_val( 'citldr', 'items', implode( "\n", uid_default_ci_tldr() ) );
	$items = array_filter( array_map( 'trim', explode( "\n", $items ) ) );
	?>
	<section class="sec" style="padding-block:24px 0">
	  <div class="ci-wrap">
	    <div class="tldr rv">
	      <div class="tldr-hd">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/></svg></span>
	        <b><?php echo esc_html( uid_section_val( 'citldr', 'heading', __( 'اگر عجله دارید، همین چهار خط کافی است', 'uid-theme' ) ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'citldr', 'badge', __( '۳۰ ثانیه', 'uid-theme' ) ) ); ?></span>
	      </div>
	      <ul class="tldr-list">
	        <?php foreach ( $items as $li ) : ?>
	        <li><?php echo uid_ci_check_icon(); ?>
	          <span><?php echo wp_kses( $li, array( 'b' => array() ) ); ?></span></li>
	        <?php endforeach; ?>
	      </ul>
	      <div class="tldr-acts">
	        <a class="ci-btn btn-cta btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ci_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="ci-btn btn-ghost-d btn-block" data-open-modal><?php echo esc_html( uid_section_val( 'citldr', 'btn_text', __( 'فرم درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	      <button class="tldr-more" type="button" data-jump-soft="#loss"><?php echo esc_html( uid_section_val( 'citldr', 'more_text', __( 'و اگر می‌خواهید عدد ریزش خودتان را ببینید', 'uid-theme' ) ) ); ?>
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) محاسبه‌گر ریزش فیلد شبا (DROP-OFF ENGINE) — منطق محاسبه در JS هاردکد است
 * ===================================================================== */
function uid_default_ci_dropoff_proof() {
	return array(
		array( 'heading' => 'کاربر از سامانه شما بیرون نمی‌رود', 'text' => 'شماره شبا را در اینترنت‌بانک، اپلیکیشن بانک یا با تماس با پشتیبانی پیدا نمی‌کند — چون اصلاً از او پرسیده نمی‌شود. کارتی که در دستش است کافی است.' ),
		array( 'heading' => '۲۴ رقمی که دیگر دستی تایپ نمی‌شود', 'text' => 'خطای انسانی در تایپ شبا، تسویه را به حساب اشتباه می‌برد یا تراکنش را برمی‌گرداند. وقتی شبا از خود شبکه بانکی می‌آید، این خطا از بین می‌رود.' ),
		array( 'heading' => 'تسویه پایا و ساتنا بدون معطلی', 'text' => 'سقف کارت‌به‌کارت محدود است و مبالغ بالاتر به شبا نیاز دارند. با تبدیل خودکار، پرونده مالی کاربر همان لحظه کامل و آماده تسویه می‌شود.' ),
	);
}
function uid_render_section_ciloss() {
	$tag   = uid_section_tag( 'ciloss', 'h2' );
	$proof = uid_section_val( 'ciloss', 'proof', uid_default_ci_dropoff_proof() );
	if ( ! is_array( $proof ) ) $proof = array();
	?>
	<section class="dark sec" id="loss">
	  <div class="ci-wrap">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ciloss', 'eyebrow', __( 'قبل از اینکه ادامه بدهید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ciloss', 'heading', __( 'فیلد «شماره شبا» گران‌ترین فیلد فرم شماست', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ciloss', 'text', __( 'کاربر کارت را در جیبش دارد؛ شبا را باید در اینترنت‌بانک پیدا کند. هر کاربری که برای پیدا کردن ۲۴ رقم از سامانه شما بیرون می‌رود، یک برداشت، یک تسویه یا یک ثبت‌نام ناتمام است. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="heroA-grid rv" style="align-items:stretch">
	      <div class="engine">
	        <div class="engine-hd"><span class="live"></span><b><?php esc_html_e( 'محاسبه‌گر ریزش فیلد شبا', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'عددها را جابه‌جا کنید', 'uid-theme' ); ?></span></div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_tx"><?php esc_html_e( 'درخواست‌های ماهانه‌ای که به فیلد شماره شبا می‌رسند', 'uid-theme' ); ?></label>
	            <output id="ls_tx_v">۴٬۰۰۰</output></div>
	          <input type="range" id="ls_tx" min="200" max="60000" step="200" value="4000">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_rate"><?php esc_html_e( 'نرخ رهاکردن فرم در همین مرحله', 'uid-theme' ); ?></label>
	            <output id="ls_rate_v">۴۴٫۰<small><?php esc_html_e( 'درصد', 'uid-theme' ); ?></small></output></div>
	          <input type="range" id="ls_rate" min="30" max="700" step="5" value="440">
	        </div>
	        <p class="eng-hint"><?php esc_html_e( 'پیش‌فرض روی ۴۴٪ تنظیم شده است — همان نسبتی که در سنجش رفتار فرم‌های یوآیدی میان «شروع فرم» و «ارسال فرم» اندازه‌گیری شده. عدد خودتان را جایگزین کنید.', 'uid-theme' ); ?></p>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_cost"><?php esc_html_e( 'ارزش هر درخواست تکمیل‌شده برای کسب‌وکار شما', 'uid-theme' ); ?></label>
	            <output id="ls_cost_v">۲۵۰٬۰۰۰</output></div>
	          <input type="range" id="ls_cost" min="20000" max="4000000" step="10000" value="250000">
	        </div>

	        <div class="eng-out">
	          <div class="eng-cell bad"><span class="k"><?php esc_html_e( 'درخواست‌هایی که ماهانه در همین فیلد رها می‌شوند', 'uid-theme' ); ?></span>
	            <span class="v" id="ls_fail">۱٬۷۶۰</span></div>
	          <div class="eng-cell good"><span class="k"><?php esc_html_e( 'با تبدیل خودکار برمی‌گردند ', 'uid-theme' ); ?><small style="opacity:.75"><?php esc_html_e( '(برآورد ۷۰٪)', 'uid-theme' ); ?></small></span>
	            <span class="v" id="ls_save">۱٬۲۳۲</span></div>
	        </div>
	        <div class="eng-total">
	          <span class="k"><?php esc_html_e( 'ارزشی که ماهانه پشت یک فیلد از دست می‌رود', 'uid-theme' ); ?></span>
	          <span class="v amt"><span class="num" id="ls_total">۳۰۸٬۰۰۰٬۰۰۰</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span>
	        </div>
	        <div class="eng-daily"><?php echo uid_ci_clock_icon(); ?>
	          <span><?php esc_html_e( 'یعنی روزانه حدود ', 'uid-theme' ); ?><b id="ls_daily">۱۰٬۲۶۶٬۶۶۷</b><?php esc_html_e( ' تومان — تا لحظه‌ای که این سرویس فعال شود.', 'uid-theme' ); ?></span></div>

	        <div class="micro" id="lossMicro">
	          <div class="micro-fields">
	            <p><?php esc_html_e( 'می‌خواهید همین عدد را روی داده واقعی فرم خودتان دقیق‌تر حساب کنیم؟ کارشناس یوآیدی رایگان بررسی می‌کند.', 'uid-theme' ); ?></p>
	            <form id="lossForm" novalidate>
	              <input type="hidden" name="source" value="card-iban-lp-dropoff">
	              <input type="hidden" name="volume" id="lossVolume" value="">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ci-btn btn-cta" type="button" data-submit><?php esc_html_e( 'بررسی رایگان فرم من', 'uid-theme' ); ?></button>
	              </div>
	            </form>
	          </div>
	          <div class="form-ok"><?php echo uid_ci_success_icon(); ?>
	            <span><?php esc_html_e( 'کارشناس یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </div>
	      </div>

	      <div class="deck-wrap">
	        <div class="proof deck on-dark" data-deck="proof">
	          <?php foreach ( $proof as $i => $p ) :
	            $icons = array(
	              '<path d="M9 18l-6-6 6-6M15 6l6 6-6 6"/>',
	              '<path d="M20 6L9 17l-5-5"/>',
	              '<path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9"/>',
	            );
	            $svg = $icons[ $i ] ?? $icons[0];
	          ?>
	          <article class="pfcard">
	            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( $svg, array( 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	            <b><?php echo esc_html( $p['heading'] ?? '' ); ?></b>
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
	          <div class="ic"><?php echo uid_ci_phone_icon(); ?></div>
	          <div class="tx"><b><?php echo esc_html( uid_section_val( 'ciloss', 'cta_heading', __( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'ciloss', 'cta_text', __( 'یک تماس کوتاه کافی است تا فرم برداشت یا تسویه شما بررسی و مسیر اتصال مشخص شود.', 'uid-theme' ) ) ); ?></p></div>
	          <div class="acts">
	            <a class="ci-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ci_phone_icon(); ?>
	              <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱) معرفی سرویس — ورودی/خروجی سرویس
 * ===================================================================== */
function uid_render_section_ciwhat() {
	$tag = uid_section_tag( 'ciwhat', 'h2' );
	?>
	<section class="sec" id="what" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ciwhat', 'fold_title', __( '۱۶ رقم می‌فرستید، ۲۶ رقم می‌گیرید', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ciwhat', 'fold_teaser', __( 'این وب‌سرویس دقیقاً چه کاری انجام می‌دهد', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ciwhat', 'eyebrow', __( 'معرفی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ciwhat', 'heading', __( 'وب‌سرویس تبدیل کارت به شبا چیست؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ciwhat', 'text', __( 'بیشتر کاربران شماره ۱۶ رقمی کارت بانکی خود را به خاطر دارند یا روی کارت می‌بینند، اما شماره ۲۶ رقمی شبا را در دسترس ندارند. از طرف دیگر سقف انتقال کارت‌به‌کارت محدود است و سامانه‌های مالی برای مبالغ بالاتر، تسویه‌های پایا و ساتنا به شماره شبا نیاز دارند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="conv rv">
	      <div class="convbox">
	        <span class="cap"><?php esc_html_e( 'ورودی درخواست (', 'uid-theme' ); ?><span class="lat">cardNumber</span>)</span>
	        <span class="dig">6037 9912 3456 7890</span>
	        <span class="cnt"><?php esc_html_e( '۱۶ رقم — همان چیزی که کاربر دارد', 'uid-theme' ); ?></span>
	      </div>
	      <div class="conv-mid">
	        <span class="pill"><?php esc_html_e( 'استعلام از شبکه بانکی', 'uid-theme' ); ?></span>
	        <span class="ar"><?php echo uid_ci_arrow_icon(); ?></span>
	        <span class="pill"><?php esc_html_e( 'پاسخ در قالب ', 'uid-theme' ); ?><span class="lat">JSON</span></span>
	      </div>
	      <div class="convbox out">
	        <span class="cap"><?php esc_html_e( 'خروجی پاسخ (', 'uid-theme' ); ?><span class="lat">iban</span>)</span>
	        <span class="dig"><span class="ir">IR</span>12 3456 7890 1234 5678 9012 34</span>
	        <span class="cnt"><?php esc_html_e( '۲۶ کاراکتر — شبای متصل به همان حساب', 'uid-theme' ); ?></span>
	      </div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(26px,4vw,42px)">
	      <h3 class="h-sub" style="margin-block-end:14px"><?php esc_html_e( 'پاسخ سرویس چه چیزهایی برمی‌گرداند؟', 'uid-theme' ); ?></h3>
	      <div class="ftags">
	        <span class="ftag"><span class="key">iban</span><?php esc_html_e( 'شماره ۲۶ رقمی شبا با پیشوند ', 'uid-theme' ); ?><span class="lat">IR</span></span>
	        <span class="ftag"><span class="key">cardNumber</span><?php esc_html_e( 'شماره کارت ارسالی، جهت تطبیق رکورد', 'uid-theme' ); ?></span>
	        <span class="ftag"><span class="key">status.code</span><?php esc_html_e( 'کد وضعیت عملیات', 'uid-theme' ); ?></span>
	        <span class="ftag"><span class="key">status.message</span><?php esc_html_e( 'پیام و توضیح وضعیت', 'uid-theme' ); ?></span>
	      </div>
	    </div>

	    <div class="secbox rv" style="margin-block-start:22px;background:var(--teal-l);border-color:rgba(41,188,206,.3)">
	      <div class="ic" style="color:var(--teal-d)"><?php echo uid_ci_clock_icon(); ?></div>
	      <div><b style="color:#0E5A66"><?php echo esc_html( uid_section_val( 'ciwhat', 'box_heading', __( 'نتیجه عملی: یک مرحله کمتر در قیف مالی شما', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#136B4C"><?php echo esc_html( uid_section_val( 'ciwhat', 'box_text', __( 'سامانه پذیرنده شماره کارت را از کاربر می‌گیرد و شماره شبای استاندارد متصل به همان حساب را مستقیم از شبکه بانکی دریافت می‌کند. این کار نرخ ریزش کاربران در مراحل تسویه‌حساب و ثبت اطلاعات مالی را کاهش می‌دهد.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۲) مقایسه سناریوی کاربر — فرم دستی در برابر تبدیل خودکار
 * ===================================================================== */
function uid_default_ci_vs_bad_items() {
	return array(
		'خروج از سامانه شما برای باز کردن اینترنت‌بانک یا اپلیکیشن بانک',
		'تایپ دستی ۲۴ رقم و احتمال بالای خطای انسانی',
		'تسویه به حساب اشتباه یا برگشت تراکنش و تیکت پشتیبانی',
		'بخش بزرگی از کاربران در همان لحظه فرم را رها می‌کنند',
	);
}
function uid_default_ci_vs_good_items() {
	return array(
		'کاربر در جریان شما می‌ماند و فرم را همان‌جا تمام می‌کند',
		'شبا مستقیم از شبکه بانکی می‌آید، پس خطای تایپ صفر است',
		'پرونده مالی کاربر همان لحظه برای پایا و ساتنا کامل می‌شود',
		'تیم پشتیبانی شما دیگر شبای دستی از کاربر نمی‌گیرد',
	);
}
function uid_render_section_civs() {
	$tag        = uid_section_tag( 'civs', 'h2' );
	$bad_items  = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'civs', 'bad_items', implode( "\n", uid_default_ci_vs_bad_items() ) ) ) ) );
	$good_items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'civs', 'good_items', implode( "\n", uid_default_ci_vs_good_items() ) ) ) ) );
	?>
	<section class="sec" id="vs" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'civs', 'fold_title', __( 'دو مسیر برای گرفتن شبا', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'civs', 'fold_teaser', __( 'فرم دستی در برابر تبدیل خودکار', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'civs', 'eyebrow', __( 'مقایسه سناریوی کاربر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'civs', 'heading', __( 'یک فیلد، دو نتیجه کاملاً متفاوت', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'civs', 'text', __( 'تفاوت این دو ستون، تفاوت میان یک تسویه انجام‌شده و یک تسویه نیمه‌کاره است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="vs2 rv">
	      <article class="vscard bad">
	        <span class="tag"><?php echo uid_ci_x_icon(); ?><?php echo esc_html( uid_section_val( 'civs', 'bad_tag', __( 'وضعیت فعلی — پرسیدن شبا از کاربر', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'civs', 'bad_title', __( 'کاربر باید کار شما را انجام دهد', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'civs', 'bad_text', __( 'فرم از کاربر ۲۴ رقم می‌خواهد که در ذهنش نیست و روی هیچ کارتی نوشته نشده.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $bad_items as $li ) : ?>
	          <li><?php echo uid_ci_x_icon(); ?><?php echo esc_html( $li ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	      <article class="vscard good">
	        <span class="tag"><?php echo uid_ci_check_icon(); ?><?php echo esc_html( uid_section_val( 'civs', 'good_tag', __( 'با وب‌سرویس یوآیدی — پرسیدن شماره کارت', 'uid-theme' ) ) ); ?></span>
	        <h3><?php echo esc_html( uid_section_val( 'civs', 'good_title', __( 'سامانه شما کار را انجام می‌دهد', 'uid-theme' ) ) ); ?></h3>
	        <p><?php echo esc_html( uid_section_val( 'civs', 'good_text', __( 'کاربر فقط شماره کارتی را وارد می‌کند که در دستش است؛ باقی ماجرا پشت صحنه اتفاق می‌افتد.', 'uid-theme' ) ) ); ?></p>
	        <ul class="vslist">
	          <?php foreach ( $good_items as $li ) : ?>
	          <li><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $li ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </article>
	    </div>

	    <div class="secbox rv" style="margin-block-start:20px;background:var(--orange-l);border-color:rgba(248,148,40,.32)">
	      <div class="ic" style="color:var(--orange-d)"><?php echo uid_ci_warn_icon(); ?></div>
	      <div><b style="color:#8A5410"><?php echo esc_html( uid_section_val( 'civs', 'box_heading', __( 'این ریزش، ریزش در گران‌ترین نقطه قیف است', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#9A6A22"><?php echo esc_html( uid_section_val( 'civs', 'box_text', __( 'کاربری که تا مرحله برداشت وجه یا تسویه رسیده، تمام هزینه جذب و اعتمادسازی‌اش پرداخت شده است. رها کردن فرم در این نقطه، از دست دادن یک مشتری آماده پرداخت است.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۳) نحوه کار سرویس — پله‌ی تعاملی چهارمرحله‌ای (محتوای پله‌ها هاردکد است)
 * ===================================================================== */
function uid_render_section_cihow() {
	$tag = uid_section_tag( 'cihow', 'h2' );
	?>
	<section class="sec" id="how" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'cihow', 'fold_title', __( 'مراحل عملکرد سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'cihow', 'fold_teaser', __( 'از ورود شماره کارت تا دریافت شبا در چهار قدم', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'cihow', 'eyebrow', __( 'پشت صحنه یک فراخوانی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'cihow', 'heading', __( 'از شماره کارت تا شماره شبا، در چهار قدم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'cihow', 'text', __( 'روی هر مرحله بزنید تا ببینید در آن لحظه دقیقاً چه اتفاقی می‌افتد و چه چیزی رد و بدل می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="stepper rv" id="howStep">
	      <div class="st-track" id="st_track" role="tablist" aria-label="<?php esc_attr_e( 'مراحل عملکرد سرویس', 'uid-theme' ); ?>">
	        <button class="st-node on" type="button" role="tab" aria-selected="true" data-st="0">
	          <span class="st-dot">۱</span><span class="st-cap"><?php esc_html_e( 'ورود شماره کارت', 'uid-theme' ); ?></span></button>
	        <button class="st-node" type="button" role="tab" aria-selected="false" data-st="1">
	          <span class="st-dot">۲</span><span class="st-cap"><?php esc_html_e( 'فراخوانی وب‌سرویس', 'uid-theme' ); ?></span></button>
	        <button class="st-node" type="button" role="tab" aria-selected="false" data-st="2">
	          <span class="st-dot">۳</span><span class="st-cap"><?php esc_html_e( 'استعلام از شبکه بانکی', 'uid-theme' ); ?></span></button>
	        <button class="st-node" type="button" role="tab" aria-selected="false" data-st="3">
	          <span class="st-dot">۴</span><span class="st-cap"><?php esc_html_e( 'دریافت خودکار شبا', 'uid-theme' ); ?></span></button>
	      </div>

	      <div class="st-panel">
	        <div class="st-body">
	          <div data-st-pane="0">
	            <span class="st-eyebrow"><?php esc_html_e( 'مرحله ۱ — سمت کاربر', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'کاربر شماره ۱۶ رقمی کارتش را وارد می‌کند', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'در فرم برداشت، تسویه یا ثبت اطلاعات مالی سامانه پذیرنده، تنها فیلدی که پر می‌شود شماره کارت است — همان عددی که کاربر روی کارت مقابلش می‌بیند. هیچ خروجی از سامانه شما لازم نیست.', 'uid-theme' ); ?></p>
	            <div class="st-chips"><span class="usr"><?php esc_html_e( 'اقدام کاربر', 'uid-theme' ); ?></span><span><?php esc_html_e( 'یک فیلد', 'uid-theme' ); ?></span><span><?php esc_html_e( '۱۶ رقم', 'uid-theme' ); ?></span></div>
	          </div>
	          <div data-st-pane="1" hidden>
	            <span class="st-eyebrow"><?php esc_html_e( 'مرحله ۲ — سمت سامانه شما', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'شماره کارت با متد ', 'uid-theme' ); ?><span class="lat">POST</span><?php esc_html_e( ' به یوآیدی ارسال می‌شود', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'سامانه شما سرویس ', 'uid-theme' ); ?><span class="mono">inquiry/card</span><?php esc_html_e( ' را با شناسه و توکن کسب‌وکار (', 'uid-theme' ); ?><span class="lat">businessId</span><?php esc_html_e( ' و ', 'uid-theme' ); ?><span class="lat">businessToken</span><?php esc_html_e( ') و پارامتر ', 'uid-theme' ); ?><span class="lat">cardNumber</span><?php esc_html_e( ' روی بستر امن فراخوانی می‌کند.', 'uid-theme' ); ?></p>
	            <div class="st-chips"><span class="src">POST</span><span><?php esc_html_e( 'ارتباط امن', 'uid-theme' ); ?></span><span><?php esc_html_e( 'احراز با توکن کسب‌وکار', 'uid-theme' ); ?></span></div>
	          </div>
	          <div data-st-pane="2" hidden>
	            <span class="st-eyebrow"><?php esc_html_e( 'مرحله ۳ — سمت یوآیدی', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'بانک صادرکننده شناسایی و شبای حساب دریافت می‌شود', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'یوآیدی بانک صادرکننده کارت را تشخیص می‌دهد و شماره شبای متصل به همان حساب را از زیرساخت بانکی دریافت می‌کند. اتصال چندگانه بانکی باعث می‌شود ترافیک سنگین بدون افت سرعت مدیریت شود.', 'uid-theme' ); ?></p>
	            <div class="st-chips"><span class="src"><?php esc_html_e( 'شبکه شتاب', 'uid-theme' ); ?></span><span><?php esc_html_e( 'اتصال چندگانه بانکی', 'uid-theme' ); ?></span><span><?php esc_html_e( 'زیرساخت مقیاس‌پذیر', 'uid-theme' ); ?></span></div>
	          </div>
	          <div data-st-pane="3" hidden>
	            <span class="st-eyebrow"><?php esc_html_e( 'مرحله ۴ — بازگشت پاسخ', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'شبا در قالب استاندارد ', 'uid-theme' ); ?><span class="lat">JSON</span><?php esc_html_e( ' برمی‌گردد', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'پاسخ شامل ', 'uid-theme' ); ?><span class="lat">iban</span><?php esc_html_e( '، همان ', 'uid-theme' ); ?><span class="lat">cardNumber</span><?php esc_html_e( ' ارسالی جهت تطبیق رکورد، و کد و پیام وضعیت است. سامانه شما فیلد شبا را خودکار پر می‌کند و فرایند مالی همان‌جا تمام می‌شود.', 'uid-theme' ); ?></p>
	            <div class="st-chips"><span class="src">JSON</span><span class="lat">iban</span><span class="lat">status.code</span><span class="lat">status.message</span></div>
	          </div>
	        </div>
	        <div class="st-nav">
	          <button type="button" id="st_prev" aria-label="<?php esc_attr_e( 'مرحله قبل', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	          <span class="st-count" id="st_count">۱ / ۴</span>
	          <button type="button" id="st_next" aria-label="<?php esc_attr_e( 'مرحله بعد', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	        </div>
	      </div>

	      <div class="st-foot">
	        <span class="small"><?php esc_html_e( 'کل این چرخه در یک فراخوانی انجام می‌شود؛ تیم فنی شما فقط یک درخواست می‌نویسد.', 'uid-theme' ); ?></span>
	        <button class="ci-btn ci-btn-navy ci-btn-sm" data-open-modal><?php esc_html_e( 'دریافت کلید و مستندات', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۴) کاربردها (انتخابگر صنعت)
 * ===================================================================== */
function uid_default_ci_who() {
	return array(
		array( 'icon' => 'crypto', 'label' => 'صرافی رمزارز و بروکر', 'title' => 'صرافی‌های رمزارز و بروکرها', 'text' => 'ثبت شبای کاربر هنگام درخواست برداشت ریالی، تنها با گرفتن شماره کارت مبدأ — بدون آنکه کاربر وسط فرآیند برداشت، سامانه را ترک کند.', 'win' => 'تکمیل برداشت ریالی در همان نشست' ),
		array( 'icon' => 'market', 'label' => 'مارکت‌پلیس و فروشگاه آنلاین', 'title' => 'مارکت‌پلیس‌ها و فروشگاه‌های آنلاین', 'text' => 'تبدیل شماره کارت فروشندگان به شبا برای تسویه‌حساب‌های دوره‌ای و گروهی پایا، بدون جمع‌آوری دستی شبا از هر فروشنده.', 'win' => 'تسویه گروهی بدون پیگیری دستی' ),
		array( 'icon' => 'psp', 'label' => 'پرداخت‌یاری و فین‌تک', 'title' => 'پلتفرم‌های پرداخت‌یاری و فین‌تک', 'text' => 'تکمیل خودکار پرونده‌های مالی و انتقال وجوه، بدون آنکه برای کاربر نهایی زحمتی ایجاد شود یا مرحله‌ای به فرم اضافه گردد.', 'win' => 'پرونده مالی کامل بدون فیلد اضافه' ),
		array( 'icon' => 'refund', 'label' => 'بازگشت وجه (Refund)', 'title' => 'سامانه‌های بازگشت وجه', 'text' => 'عودت سریع وجه سفارش‌های مرجوعی، بدون نیاز به تماس پشتیبانی با مشتری برای گرفتن شماره شبا.', 'win' => 'حذف تماس پشتیبانی از مسیر عودت' ),
		array( 'icon' => 'lend', 'label' => 'لندتک و وام آنلاین', 'title' => 'پلتفرم‌های وام‌دهی و لندتک', 'text' => 'ثبت حساب بانکی متقاضیان تسهیلات تنها بر اساس شماره کارت فعال آن‌ها، پیش از واریز مبلغ تسهیلات.', 'win' => 'واریز تسهیلات بدون رفت‌وبرگشت' ),
	);
}
function uid_render_section_ciwho() {
	$tag   = uid_section_tag( 'ciwho', 'h2' );
	$items = uid_section_val( 'ciwho', 'items', uid_default_ci_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="who" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ciwho', 'fold_title', __( 'کاربردهای وب‌سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ciwho', 'fold_teaser', __( 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ciwho', 'eyebrow', __( 'کجا به کار می‌آید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ciwho', 'heading', __( 'هر جا که پول باید به حساب کاربر برگردد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ciwho', 'text', __( 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="rv">
	      <div class="pick-chips" data-pick-chips="ind" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب صنعت', 'uid-theme' ); ?>"></div>
	      <div class="pick n5" data-pick="ind">
	        <?php foreach ( $items as $it ) :
	          $color = uid_ci_who_color( $it['icon'] ?? 'crypto' );
	        ?>
	        <article class="pick-card" data-label="<?php echo esc_attr( $it['label'] ?? '' ); ?>">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ci_who_icon_svg( $it['icon'] ?? 'crypto' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="win"><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $it['win'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="pick-hint"><?php echo uid_ci_chev_icon(); ?><?php esc_html_e( 'برای دیدن صنعت‌های دیگر، از نوارِ بالا انتخاب کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۵) مزایای استفاده از سرویس + پوشش بانکی
 * ===================================================================== */
function uid_default_ci_gain() {
	return array(
		array( 'icon' => 'churn',   'title' => 'کاهش چشمگیر نرخ ریزش کاربران', 'text' => 'فرایند خروج کاربر از سامانه برای پیدا کردن شماره شبا در اینترنت‌بانک، کاملاً از مسیر حذف می‌شود.', 'tagline' => 'مستقیم روی نرخ تبدیل اثر می‌گذارد' ),
		array( 'icon' => 'human',   'title' => 'جلوگیری از خطای انسانی', 'text' => 'خطاهای ناشی از تایپ دستی ۲۴ رقم شبا — و تسویه‌های اشتباه و تیکت‌هایی که به دنبالش می‌آید — به‌کلی حذف می‌شود.', 'tagline' => 'داده مستقیم از شبکه بانکی' ),
		array( 'icon' => 'scale',   'title' => 'زیرساخت مقیاس‌پذیر و پایدار', 'text' => 'مدیریت ترافیک‌های سنگین بدون افت سرعت، با اتصال چندگانه بانکی — حتی در ساعات اوج برداشت.', 'tagline' => 'اتصال چندگانه بانکی' ),
		array( 'icon' => 'fast',    'title' => 'یکپارچه‌سازی سریع', 'text' => 'مستندات شفاف، خروجی استاندارد JSON و امکان راه‌اندازی سریع در سامانه پذیرنده — معمولاً کمتر از یک روز کاری.', 'tagline' => 'یک درخواست، یک پاسخ' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی اختصاصی و گزارش‌دهی دوره‌ای', 'text' => 'همراهی تیم فنی یوآیدی و ارائه گزارش‌های دقیق از حجم درخواست‌ها و لاگ تراکنش‌های شما.', 'tagline' => 'کارشناس مشخص، نه صف تیکت' ),
		array( 'icon' => 'free',    'title' => 'هزینه فقط بابت تراکنش موفق', 'text' => 'پاسخ‌های ناموفق یا کارت‌های نامعتبر هیچ هزینه‌ای ندارند، و برای حجم‌های بالای ماهانه تعرفه پلکانی ترجیحی اعمال می‌شود.', 'tagline' => 'ریسک مالی صفر برای شروع' ),
	);
}
function uid_default_ci_banks() {
	return array(
		'ملی', 'ملت', 'صادرات', 'تجارت', 'سپه', 'کشاورزی', 'مسکن', 'رفاه کارگران',
		'پاسارگاد', 'سامان', 'پارسیان', 'اقتصاد نوین', 'کارآفرین', 'شهر', 'دی',
		'سینا', 'آینده', 'پست بانک',
	);
}
function uid_render_section_cigain() {
	$tag   = uid_section_tag( 'cigain', 'h2' );
	$items = uid_section_val( 'cigain', 'items', uid_default_ci_gain() );
	if ( ! is_array( $items ) ) $items = array();
	$banks_raw = uid_section_val( 'cigain', 'banks', implode( "\n", uid_default_ci_banks() ) );
	$banks     = array_values( array_filter( array_map( 'trim', explode( "\n", $banks_raw ) ) ) );
	?>
	<section class="sec" id="gain" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'cigain', 'fold_title', __( 'مزایای استفاده از این وب‌سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'cigain', 'fold_teaser', __( 'شش دلیلی که تیم فنی و تیم مالی هر دو قبول می‌کنند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'cigain', 'eyebrow', __( 'چرا یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'cigain', 'heading', __( 'مزایای استفاده از وب‌سرویس یوآیدی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'cigain', 'text', __( 'این وب‌سرویس یک تبدیل ساده نیست؛ یک مرحله کامل را از مسیر کاربر و از میز پشتیبانی شما حذف می‌کند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="gain">
	        <?php foreach ( $items as $it ) :
	          $color = uid_ci_gain_color( $it['icon'] ?? 'churn' );
	        ?>
	        <article class="dcard">
	          <div class="ic<?php echo $color ? ' ' . esc_attr( $color ) : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ci_gain_icon_svg( $it['icon'] ?? 'churn' ), array( 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="tagline"><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $it['tagline'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="gain">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><?php echo uid_ci_chev_icon(); ?><?php esc_html_e( 'برای دیدن کارت بعدی، بکشید یا روی کارت بزنید', 'uid-theme' ); ?></div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(28px,4vw,44px)">
	      <h3 class="h-sub" style="margin-block-end:14px;text-align:center"><?php echo esc_html( uid_section_val( 'cigain', 'banks_heading', __( 'پوشش بانکی سرویس', 'uid-theme' ) ) ); ?></h3>
	      <p class="small" style="text-align:center;max-width:66ch;margin:0 auto 18px"><?php echo esc_html( uid_section_val( 'cigain', 'banks_text', __( 'این وب‌سرویس به شبکه شتاب و زیرساخت بانکی متصل است و از کارت‌های تمام بانک‌ها و موسسات مالی مجاز کشور پشتیبانی می‌کند.', 'uid-theme' ) ) ); ?></p>
	      <div class="banks">
	        <?php foreach ( $banks as $b ) : ?>
	        <span class="bankchip"><?php echo esc_html( $b ); ?></span>
	        <?php endforeach; ?>
	        <span class="bankchip more"><?php echo esc_html( uid_section_val( 'cigain', 'banks_more', __( 'و سایر بانک‌ها و موسسات مجاز عضو شتاب', 'uid-theme' ) ) ); ?></span>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۶) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_ci_team_rows() {
	return array(
		array( 'icon' => 'connect', 'title' => 'کارشناس فنی اختصاصی برای دوره اتصال', 'text' => 'از تحویل کلید API و دسترسی سندباکس تا اولین استعلام موفق روی محیط عملیاتی، یک نفر مشخص پاسخگوی تیم شماست.' ),
		array( 'icon' => 'report',  'title' => 'گزارش‌دهی دوره‌ای از حجم و لاگ تراکنش‌ها', 'text' => 'گزارش دقیق تعداد استعلام‌ها، نرخ پاسخ موفق و لاگ تراکنش‌ها در اختیار شما قرار می‌گیرد تا هزینه و عملکرد سرویس همیشه قابل راستی‌آزمایی باشد.' ),
		array( 'icon' => 'doc',     'title' => 'مستندات شفاف و نمونه درخواست و پاسخ', 'text' => 'ساختار درخواست، ساختار پاسخ و جدول پارامترها همگی مستند و در دسترس تیم توسعه شماست؛ به همین دلیل اتصال معمولاً کمتر از یک روز کاری طول می‌کشد.' ),
		array( 'icon' => 'shield',  'title' => 'سابقه از سال ۱۳۹۶ در زیرساخت هویت دیجیتال', 'text' => 'شرکت دانش‌بنیان بینش هوشمند نسل پیشرو، کارگزار مورد تایید سامانه‌های سجام و ثنا، با تمرکز اختصاصی روی سرویس‌های استعلام و احراز هویت.' ),
	);
}
function uid_default_ci_pledge_items() {
	return array(
		'دسترسی به محیط سندباکس و کلید آزمایشی پیش از هر تعهد مالی',
		'هزینه فقط بابت استعلام‌های موفق؛ پاسخ ناموفق رایگان است',
		'تعرفه پلکانی و نرخ ترجیحی برای حجم بالای درخواست ماهانه',
		'همراهی در طراحی فرم برداشت و تسویه برای کمترین ریزش',
	);
}
function uid_render_section_citeam() {
	$rows       = uid_section_val( 'citeam', 'rows', uid_default_ci_team_rows() );
	$pledge_raw = uid_section_val( 'citeam', 'pledge_items', implode( "\n", uid_default_ci_pledge_items() ) );
	$pledge     = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	$tag = uid_section_tag( 'citeam', 'h2' );
	?>
	<section class="sec" id="team" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'citeam', 'fold_title', __( 'تیم متخصص و پشتیبانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'citeam', 'fold_teaser', __( 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'citeam', 'eyebrow', __( 'پشت این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php esc_html_e( 'یک ', 'uid-theme' ); ?><span class="lat">API</span><?php echo esc_html( uid_section_val( 'citeam', 'heading', __( ' نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'citeam', 'text', __( 'این سرویس مستقیماً روی مسیر پول کاربران شما می‌نشیند. به همین دلیل تیم فنی یوآیدی از اولین تماس تا اولین تسویه موفق روی محیط عملیاتی همراه شماست.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="team rv">
	      <div class="team-list">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ci_team_icon_svg( $r['icon'] ?? 'connect' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><?php echo uid_ci_check_icon(); ?></span>
	          <b><?php echo esc_html( uid_section_val( 'citeam', 'pledge_heading', __( 'تعهد یوآیدی به تیم فنی شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div class="btn-row" style="margin-block-start:18px">
	          <a class="ci-btn ci-btn-navy" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ci_phone_icon(); ?>
	            <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	          <button class="ci-btn btn-ghost" data-open-modal><?php echo esc_html( uid_section_val( 'citeam', 'btn_text', __( 'درخواست کلید آزمایشی', 'uid-theme' ) ) ); ?></button>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۷) مستندات فنی و نمونه کد (نمونه‌کدها و جدول پارامترها هاردکد است)
 * ===================================================================== */
function uid_render_section_cidev() {
	$tag = uid_section_tag( 'cidev', 'h2' );
	?>
	<section class="sec" id="dev" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'cidev', 'fold_title', __( 'بخش فنی و نمونه کد', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'cidev', 'fold_teaser', __( 'نمونه درخواست، نمونه پاسخ و جدول پارامترها', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'cidev', 'eyebrow', __( 'برای تیم توسعه شما', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'cidev', 'heading', __( 'یک فراخوانی، یک پاسخ، تمام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'cidev', 'text', __( 'برای استفاده از سرویس API تبدیل شماره کارت به شماره شبای یوآیدی می‌بایست سرویس زیر را با پارامترهای درخواستی فراخوانی نمایید. این سرویس با دریافت شماره کارت کاربر، شماره شبای کاربر را در پاسخ برمی‌گرداند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="codepanel rv">
	      <div>
	        <div class="ftags" style="margin-block-end:18px">
	          <span class="ftag"><span class="key">businessId</span><?php esc_html_e( 'شناسه کسب‌وکار', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">businessToken</span><?php esc_html_e( 'توکن کسب‌وکار', 'uid-theme' ); ?></span>
	          <span class="ftag warm"><span class="key">cardNumber</span><?php esc_html_e( 'شماره کارت کاربر', 'uid-theme' ); ?></span>
	          <span class="ftag"><span class="key">iban</span><?php esc_html_e( 'شماره شبای کاربر', 'uid-theme' ); ?></span>
	        </div>
	        <div class="secbox" style="background:var(--n50);border-color:var(--n200)">
	          <div class="ic" style="color:var(--navy)"><?php echo uid_ci_info_icon(); ?></div>
	          <div><b style="color:var(--ink)"><?php esc_html_e( 'درباره شناسه و توکن کسب‌وکار', 'uid-theme' ); ?></b>
	            <p style="color:var(--n600)"><?php esc_html_e( 'مقادیر ', 'uid-theme' ); ?><span class="lat">businessId</span> <?php esc_html_e( 'و', 'uid-theme' ); ?>
	              <span class="lat">businessToken</span> <?php esc_html_e( 'توسط یوآیدی در اختیار شما قرار می‌گیرد و هر دو الزامی هستند. نسخه فعلی مستندات سرویس ', 'uid-theme' ); ?><span class="mono">1.1.0</span> <?php esc_html_e( 'است.', 'uid-theme' ); ?></p></div>
	        </div>
	        <div class="btn-row" style="margin-block-start:20px">
	          <button class="ci-btn btn-cta" data-open-modal><?php echo uid_ci_submit_icon(); ?><?php echo esc_html( uid_section_val( 'cidev', 'btn1_text', __( 'دریافت کلید وب‌سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ci-btn btn-ghost" href="<?php echo esc_url( home_url( '/card-inquiry-docs/' ) ); ?>"><?php esc_html_e( 'مشاهده مستندات کامل ', 'uid-theme' ); ?><span class="lat">API</span></a>
	        </div>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button class="on" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req"><span class="m">POST</span> https://json-api.uid.ir/api/inquiry/card
<span class="k">Content-Type</span>: application/json;charset=UTF-8

{
  <span class="k">"requestContext"</span>: {
    <span class="k">"apiInfo"</span>: {
      <span class="k">"businessId"</span>: <span class="s">"&lt;UID_BUSINESS_ID&gt;"</span>,
      <span class="k">"businessToken"</span>: <span class="s">"&lt;UID_BUSINESS_TOKEN&gt;"</span>
    }
  },
  <span class="k">"cardNumber"</span>: <span class="s">"1234123412341234"</span>
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
  <span class="k">"cardNumber"</span>: <span class="s">"1234123412341234"</span>,
  <span class="k">"iban"</span>: <span class="s">"IR123456789012345678901234"</span>
<span class="m">// String - 26 Digits with IR</span>
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
	        <div class="r"><span class="f">cardNumber</span><span class="t">string</span><span class="k req">Required</span><span class="d"><?php esc_html_e( 'شماره کارت کاربر', 'uid-theme' ); ?></span></div>
	      </div>

	      <h3 class="h-sub" style="margin-block:26px 14px"><?php esc_html_e( 'پارامترهای پاسخ', 'uid-theme' ); ?></h3>
	      <div class="ptbl">
	        <div class="r hd"><span><?php esc_html_e( 'فیلد', 'uid-theme' ); ?></span><span><?php esc_html_e( 'نوع', 'uid-theme' ); ?></span><span><?php esc_html_e( 'بخش', 'uid-theme' ); ?></span><span><?php esc_html_e( 'توضیح', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">code</span><span class="t">int</span><span class="k res">status</span><span class="d"><?php esc_html_e( 'کد خطا', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">message</span><span class="t">string</span><span class="k res">status</span><span class="d"><?php esc_html_e( 'توضیحات خطا', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">cardNumber</span><span class="t">string</span><span class="k res">root</span><span class="d"><?php esc_html_e( 'شماره کارت کاربر', 'uid-theme' ); ?></span></div>
	        <div class="r"><span class="f">iban</span><span class="t">string</span><span class="k res">root</span><span class="d"><?php esc_html_e( 'شماره شبای کاربر', 'uid-theme' ); ?></span></div>
	      </div>

	      <div class="price-note" style="margin-block-start:16px">
	        <?php echo uid_ci_info_icon(); ?>
	        <span><?php esc_html_e( 'مقدار ', 'uid-theme' ); ?><span class="lat">iban</span> <?php esc_html_e( 'یک رشته ۲۶ کاراکتری همراه با پیشوند ', 'uid-theme' ); ?>
	          <span class="lat">IR</span> <?php esc_html_e( 'است و ', 'uid-theme' ); ?><span class="lat">cardNumber</span> <?php esc_html_e( 'در پاسخ تکرار می‌شود تا رکورد سمت شما بدون ابهام تطبیق داده شود.', 'uid-theme' ); ?></span>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۸) سرویس‌های مکمل
 * ===================================================================== */
function uid_default_ci_xsell() {
	return array(
		array( 'icon' => 'iban',   'title' => 'وب‌سرویس استعلام شبا', 'text' => 'با ارسال شماره شبا، اطلاعات کامل حساب شامل نام دارنده حساب، کد ملی و وضعیت حساب دریافت می‌شود — همان چیزی که این متد به‌تنهایی برنمی‌گرداند.', 'url' => '/api-inquiry-iban/' ),
		array( 'icon' => 'match',  'title' => 'تطبیق شماره شبا با کد ملی', 'text' => 'بررسی اینکه شماره شبای اعلام‌شده واقعاً متعلق به همان کد ملی است — لازمه انطباق در پرداخت‌های حساس و الزامات مبارزه با پول‌شویی.', 'url' => '/api-inquiry-iban/' ),
		array( 'icon' => 'cardid', 'title' => 'تطبیق شماره کارت با کد ملی', 'text' => 'اطمینان از اینکه کارت واردشده متعلق به خود کاربر است، پیش از آنکه وجهی به حساب متصل به آن کارت واریز شود.', 'url' => '/api/' ),
	);
}
function uid_render_section_cixsell() {
	$tag   = uid_section_tag( 'cixsell', 'h2' );
	$items = uid_section_val( 'cixsell', 'items', uid_default_ci_xsell() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="xsell" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'cixsell', 'fold_title', __( 'سرویس‌های مکمل و مرتبط', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'cixsell', 'fold_teaser', __( 'استعلام شبا، تطبیق شبا و تطبیق کارت با کد ملی', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'cixsell', 'eyebrow', __( 'در کنار این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'cixsell', 'heading', __( 'اگر به چیزی بیش از شماره شبا نیاز دارید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'cixsell', 'text', __( 'خروجی مستقیم این متد شماره شبا است. اگر لازم است نام دارنده حساب، کد ملی یا تطابق مالکیت هم بررسی شود، سرویس‌های زیر مکمل همین فراخوانی هستند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="xsell rv">
	      <?php foreach ( $items as $it ) : ?>
	      <a class="xtile" href="<?php echo esc_url( $it['url'] ?? '#' ); ?>">
	        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ci_xsell_icon_svg( $it['icon'] ?? 'iban' ), array( 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="go"><?php esc_html_e( 'مشاهده سرویس', 'uid-theme' ); ?> <?php echo uid_ci_arrow_icon(); ?></span>
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
function uid_default_ci_faq() {
	return array(
		array( 'question' => 'آیا این وب‌سرویس برای تمام بانک‌های کشور کار می‌کند؟', 'answer' => 'بله؛ این وب‌سرویس به شبکه شتاب و زیرساخت بانکی متصل است و از کارت‌های تمام بانک‌ها و موسسات مالی مجاز کشور پشتیبانی می‌کند.' ),
		array( 'question' => 'در صورت اشتباه بودن شماره کارت، آیا هزینه‌ای کسر می‌شود؟', 'answer' => 'خیر؛ تنها استعلام‌هایی که با موفقیت انجام شوند و شماره شبای معتبر برگردانند، مشمول هزینه خواهند بود.' ),
		array( 'question' => 'آیا با تبدیل شماره کارت به شبا، نام دارنده حساب هم بازگردانده می‌شود؟', 'answer' => 'خروجی مستقیم این متد شماره شبا است. برای دریافت هم‌زمان نام دارنده حساب، کد ملی و وضعیت حساب، می‌توان از «وب‌سرویس استعلام شبا» استفاده کرد.' ),
		array( 'question' => 'اگر یک شماره کارت چند حساب متصل داشته باشد چه می‌شود؟', 'answer' => 'وب‌سرویس شماره شبای مربوط به حساب اصلی متصل به کارت را برمی‌گرداند.' ),
		array( 'question' => 'فرایند اتصال فنی به این سرویس چقدر زمان می‌برد؟', 'answer' => 'با توجه به استاندارد بودن وب‌سرویس و وجود مستندات دقیق، اتصال فنی معمولاً در کمتر از یک روز کاری انجام می‌شود.' ),
		array( 'question' => 'خروجی سرویس در چه قالبی برمی‌گردد؟', 'answer' => 'پاسخ در قالب استاندارد JSON برمی‌گردد و شامل شماره شبا (iban)، شماره کارت ارسالی جهت تطبیق رکورد (cardNumber) و کد و پیام وضعیت عملیات (status.code و status.message) است.' ),
		array( 'question' => 'مدل محاسبه هزینه و تعرفه به چه صورت است؟', 'answer' => 'هزینه تنها به ازای تراکنش‌های موفق دریافت می‌شود و برای کسب‌وکارهایی با حجم بالای درخواست ماهانه، تعرفه پلکانی و قیمت‌های ترجیحی در نظر گرفته شده است. برای دریافت نرخ دقیق کافی است با شماره ۰۲۱۶۶۱۲۳۲۹۰ تماس بگیرید.' ),
	);
}
function uid_render_section_cifaq() {
	$tag   = uid_section_tag( 'cifaq', 'h2' );
	$items = uid_section_val( 'cifaq', 'items', uid_default_ci_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'cifaq', 'fold_title', __( 'سوالات متداول', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'cifaq', 'fold_teaser', __( 'پوشش بانکی، هزینه، چند حسابه و زمان اتصال', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ci-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'cifaq', 'eyebrow', __( 'پیش از تماس، این‌ها را بخوانید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'cifaq', 'heading', __( 'سوالات متداول وب‌سرویس تبدیل کارت به شبا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv" style="max-width:900px;margin-inline:auto">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i"><button class="faq-q" aria-expanded="false"><span class="fq-tx"><?php echo esc_html( $it['question'] ?? '' ); ?></span><span class="pm"></span></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * مشخصات عملیاتی وب‌سرویس (پنل تیره)
 * ===================================================================== */
function uid_default_ci_adv() {
	return array(
		array( 'icon' => 'card',  'value' => 'تمام بانک‌های عضو شتاب', 'text' => 'پشتیبانی از کارت‌های تمام بانک‌ها و موسسات مالی مجاز کشور', 'percent' => '100', 'feat' => '1', 'wide' => '', 'txt' => '1' ),
		array( 'icon' => 'doc',   'value' => '۲۶', 'text' => 'کاراکتر شبای استاندارد با پیشوند IR در خروجی', 'percent' => '88', 'feat' => '', 'wide' => '', 'txt' => '' ),
		array( 'icon' => 'coin',  'value' => '۰', 'text' => 'هزینه استعلام ناموفق — فقط پاسخ موفق محاسبه می‌شود', 'percent' => '8', 'feat' => '', 'wide' => '', 'txt' => '' ),
		array( 'icon' => 'clock', 'value' => 'کمتر از یک روز کاری', 'text' => 'زمان معمول اتصال فنی، به‌دلیل استاندارد بودن سرویس و مستندات دقیق', 'percent' => '92', 'feat' => '', 'wide' => '1', 'txt' => '1' ),
		array( 'icon' => 'bank',  'value' => 'اتصال چندگانه بانکی', 'text' => 'مدیریت ترافیک سنگین بدون افت سرعت، همراه با گزارش دوره‌ای لاگ تراکنش‌ها', 'percent' => '96', 'feat' => '', 'wide' => '1', 'txt' => '1' ),
	);
}
function uid_render_section_ciadv() {
	$tag   = uid_section_tag( 'ciadv', 'h2' );
	$items = uid_section_val( 'ciadv', 'items', uid_default_ci_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="dark sec" id="adv">
	  <div class="ci-wrap">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ciadv', 'eyebrow', __( 'مشخصات عملیاتی وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ciadv', 'heading', __( 'پنج نکته‌ای که در ارزیابی فنی از شما می‌پرسند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ciadv', 'text', __( 'هر مورد زیر مستقیماً از مستندات سرویس تبدیل شماره کارت به شبا آمده است — نه یک ادعای تبلیغاتی.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="opanel rv">
	      <?php foreach ( $items as $it ) :
	        $classes = 'ostat';
	        if ( ! empty( $it['feat'] ) ) $classes .= ' feat';
	        if ( ! empty( $it['wide'] ) ) $classes .= ' w2';
	        $pct = absint( $it['percent'] ?? 90 );
	      ?>
	      <div class="<?php echo esc_attr( $classes ); ?>">
	        <div class="oic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ci_adv_icon_svg( $it['icon'] ?? 'card' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	        <span class="ov<?php echo empty( $it['txt'] ) ? '' : ' txt'; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	        <span class="ol"><?php echo esc_html( $it['text'] ?? '' ); ?></span>
	        <span class="ometer" style="--p:<?php echo esc_attr( $pct ); ?>%"><i></i></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="opanel-foot rv">
	      <span><?php echo esc_html( uid_section_val( 'ciadv', 'foot_text', __( 'می‌خواهید این سرویس را روی داده واقعی خودتان تست کنید؟', 'uid-theme' ) ) ); ?></span>
	      <button class="ci-btn btn-white ci-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'ciadv', 'btn_text', __( 'درخواست دسترسی سندباکس', 'uid-theme' ) ) ); ?></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * محاسبه‌گر تعرفه ماهانه — منطق پله‌های تخفیف در JS هاردکد است
 * ===================================================================== */
function uid_render_section_ciprice() {
	$tag = uid_section_tag( 'ciprice', 'h2' );
	?>
	<section class="sec" id="price">
	  <div class="ci-wrap">
	    <div class="sec-head mid rv">
	      <span class="ci-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ciprice', 'eyebrow', __( 'مدل محاسبه هزینه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ciprice', 'heading', __( 'فقط بابت تبدیل‌های موفق هزینه می‌دهید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ciprice', 'text', __( 'پاسخ‌های ناموفق و کارت‌های نامعتبر هزینه‌ای ندارند. تعداد تبدیل تخمینی ماهانه خود را جابه‌جا کنید تا پله تعرفه متناسب با کسب‌وکارتان را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="calc rv">
	      <div class="calc-in">
	        <span class="lb"><?php esc_html_e( 'تعداد تبدیل موفق کارت به شبا در ماه', 'uid-theme' ); ?></span>
	        <div class="calc-num"><span class="v" id="cl_qty">۲۰٬۰۰۰</span><span class="u"><?php esc_html_e( 'درخواست', 'uid-theme' ); ?></span></div>
	        <div class="calc-slider">
	          <input type="range" id="cl_range" min="0" max="1000" value="482" aria-label="<?php esc_attr_e( 'تعداد تبدیل ماهانه', 'uid-theme' ); ?>">
	          <div class="calc-ends"><span>۱٬۰۰۰</span><span>+۵۰۰٬۰۰۰</span></div>
	        </div>
	        <div class="calc-presets" id="cl_presets">
	          <button class="calc-preset" type="button" data-qty="5000">۵٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="20000">۲۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="50000">۵۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="100000">۱۰۰٬۰۰۰</button>
	        </div>
	        <div class="calc-note"><?php echo uid_ci_info_icon(); ?>
	          <span><?php esc_html_e( 'ارقام این محاسبه‌گر نمایشی و صرفاً برای درک ساختار تعرفه پلکانی است. نرخ نهایی پس از بررسی حجم و سناریوی کسب‌وکار شما توسط کارشناس یوآیدی اعلام می‌شود.', 'uid-theme' ); ?></span></div>
	      </div>
	      <div class="calc-out">
	        <div class="calc-plan"><span class="tag" id="cl_plan"><?php esc_html_e( 'پلن رشد', 'uid-theme' ); ?></span><span class="tag off" id="cl_off"><?php esc_html_e( '۱۰٪ تخفیف پلکانی', 'uid-theme' ); ?></span></div>
	        <div class="calc-rows">
	          <div class="calc-row"><span><?php esc_html_e( 'تبدیل موفق ماهانه', 'uid-theme' ); ?></span><b id="cl_n">۲۰٬۰۰۰</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'نرخ هر تبدیل در این پله', 'uid-theme' ); ?></span><b id="cl_unit">—</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'هزینه بدون تخفیف پلکانی', 'uid-theme' ); ?></span><b id="cl_gross">—</b></div>
	          <div class="calc-row total"><span><?php esc_html_e( 'برآورد هزینه ماهانه', 'uid-theme' ); ?></span><b id="cl_total">—</b></div>
	        </div>
	        <div class="calc-free"><?php echo uid_ci_check_icon(); ?>
	          <span><?php esc_html_e( 'استعلام‌های ناموفق و کارت‌های نامعتبر در این محاسبه لحاظ نمی‌شوند.', 'uid-theme' ); ?></span></div>
	        <div class="calc-cta">
	          <button class="ci-btn btn-cta btn-block" data-open-modal><?php echo uid_ci_submit_icon(); ?><?php echo esc_html( uid_section_val( 'ciprice', 'btn_text', __( 'دریافت تعرفه دقیق این حجم', 'uid-theme' ) ) ); ?></button>
	          <p class="calc-dis"><?php esc_html_e( 'فرم را پر کنید تا کارشناس یوآیدی نرخ واقعی این پله را همراه با شرایط قرارداد برای شما ارسال کند.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بند تماس میان‌صفحه
 * ===================================================================== */
function uid_render_section_cilastcall() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="ci-wrap">
	    <div class="callband rv">
	      <div class="ic"><?php echo uid_ci_clock_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'cilastcall', 'heading', __( 'هر روزی که این سرویس فعال نیست، ریزش فیلد شبا را پرداخت می‌کنید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'cilastcall', 'text', __( 'کاربری که امروز وسط فرم برداشت رها می‌کند، فردا برنمی‌گردد. یک تماس کوتاه کافی است تا کارشناس یوآیدی فرم شما را بررسی و دسترسی سندباکس را فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="ci-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ci_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="ci-btn btn-ghost-d" data-open-modal><?php echo esc_html( uid_section_val( 'cilastcall', 'btn_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_cilead() {
	$tag         = uid_section_tag( 'cilead', 'h2' );
	$trust_raw   = uid_section_val( 'cilead', 'trust', "مشاوره رایگان، بدون تعهد\nکلید آزمایشی پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nتعرفه پلکانی متناسب با حجم" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'cilead', 'biztype_options', "صرافی ارز دیجیتال یا بروکر\nمارکت‌پلیس و فروشگاه آنلاین\nپرداخت‌یاری، فین‌تک و نئوبانک\nسامانه بازگشت وجه و پشتیبانی سفارش\nلندتک و وام آنلاین\nسایر کسب‌وکارها" );
	$biztypes    = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	$biztype_keys = array( 'crypto', 'market', 'psp', 'refund', 'lend', 'other' );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="ci-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="ci-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'cilead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'cilead', 'heading', __( 'فرم درخواست فعال‌سازی وب‌سرویس تبدیل کارت به شبا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'cilead', 'text', __( 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس تبدیل شماره کارت به شبا، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_ci_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'cilead', 'form_title', __( 'درخواست وب‌سرویس تبدیل کارت به شبا', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'cilead', 'form_hint', __( 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="card-iban-api-lp">
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
	                <?php foreach ( $biztypes as $i => $b ) : ?>
	                <option value="<?php echo esc_attr( $biztype_keys[ $i ] ?? sanitize_title( $b ) ); ?>"><?php echo esc_html( $b ); ?></option>
	                <?php endforeach; ?>
	                <option value="ind"><?php esc_html_e( 'کاربر شخصی هستم (کسب‌وکار نیستم)', 'uid-theme' ); ?></option>
	              </select><span class="err"><?php esc_html_e( 'نوع کسب‌وکار را انتخاب کنید.', 'uid-theme' ); ?></span></div>
	            <div class="route-alert" id="routeAlert"><?php echo uid_ci_info_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'cilead', 'route_text', __( 'این وب‌سرویس فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی به شماره شبای خود نیاز دارید، آن را در اینترنت‌بانک یا اپلیکیشن بانک خودتان ببینید؛ برای خدمات فردی ', 'uid-theme' ) ) ); ?><a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'در دسترس شماست.', 'uid-theme' ); ?></span></div>
	            <button class="ci-btn btn-cta btn-block" type="button" data-submit><?php echo uid_ci_submit_icon(); ?><?php echo esc_html( uid_section_val( 'cilead', 'submit_text', __( 'ارسال درخواست و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_ci_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'cilead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_ci_success_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'cilead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_ci_page_id() {
	$page_id = (int) get_option( 'uid_ci_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_CARD_TO_IBAN_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_ci_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_ci_page() {
	if ( uid_get_ci_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'تبدیل شماره کارت به شبا', 'uid-theme' ),
		'post_name'   => 'api-card-to-iban',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_CARD_TO_IBAN_TEMPLATE );
	update_option( 'uid_ci_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_ci_page_once' );

function uid_ensure_ci_page_once() {
	if ( get_option( 'uid_ci_page_bootstrapped' ) ) return;
	uid_ensure_ci_page();
	update_option( 'uid_ci_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_ci_page_once' );

function uid_register_ci_slug_setting() {
	register_setting( 'uid_ci_group', 'uid_ci_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_ci_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_ci_page_slug_section', '', '__return_false', 'uid_ci_layout' );
	add_settings_field( 'uid_ci_page_slug', __( 'آدرس (اسلاگ) صفحه تبدیل کارت به شبا', 'uid-theme' ), 'uid_field_ci_page_slug', 'uid_ci_layout', 'uid_ci_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_ci_slug_setting' );

function uid_field_ci_page_slug( $args ) {
	$page_id = uid_get_ci_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_ci_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_ci_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه تبدیل کارت به شبا هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_ci_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_ci_page_id();

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
function uid_register_ci_settings() {
	register_setting( 'uid_ci_group', 'uid_ci_layout', array(
		'sanitize_callback' => 'uid_sanitize_ci_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_ci_layout_main', '', '__return_false', 'uid_ci_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_ci_layout', 'uid_ci_layout_main', array(
		'option_name' => 'uid_ci_layout', 'registry_fn' => 'uid_ci_sections_registry', 'layout_fn' => 'uid_get_ci_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cihero', array( 'sanitize_callback' => 'uid_sanitize_section_cihero', 'default' => array() ) );
	add_settings_section( 'uid_section_cihero_main', '', '__return_false', 'uid_section_cihero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس تبدیل شماره کارت به شبا · ', 'desc' => __( 'بعد از این متن، برچسب ثابت «Card Inquiry API» همیشه نمایش داده می‌شود.', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'heading', 'default' => 'کاربر شماره کارتش را حفظ است.<mark>شماره شبا را نه.</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'text', 'default' => 'همان ثانیه‌ای که از او شماره ۲۶ رقمی شبا را می‌خواهید، از سامانه شما بیرون می‌رود تا در اینترنت‌بانک دنبالش بگردد — و بخش بزرگی از آن‌ها دیگر برنمی‌گردند. وب‌سرویس تبدیل کارت به شبای یوآیدی شماره ۱۶ رقمی کارت را می‌گیرد و شبای متصل به همان حساب را مستقیم از شبکه بانکی برمی‌گرداند.', 'rows' => 4 ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'btn1_text', 'default' => 'فرم درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cihero', 'uid_section_cihero_main', array( 'group' => 'uid_section_cihero', 'key' => 'tags', 'default' => "کارت تمام بانک‌های عضو شتاب\nهزینه فقط بابت پاسخ موفق\nخروجی استاندارد JSON\nاتصال فنی در کمتر از یک روز کاری" ) );
	add_settings_field( 'samples', __( 'کارت‌های نمونه ویجت زنده', 'uid-theme' ), 'uid_field_repeater', 'uid_section_cihero', 'uid_section_cihero_main', array(
		'group' => 'uid_section_cihero', 'key' => 'samples', 'default' => uid_default_ci_samples(), 'add_label' => __( 'افزودن کارت نمونه', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'card', 'type' => 'text', 'label' => __( 'شماره کارت (۱۶ رقم)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'blocked', 'type' => 'select', 'label' => __( 'نمایش به‌عنوان نمونه نامعتبر', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_cihero', 'uid_section_cihero_main', array( 'text' => __( 'ویجت زنده تبدیل کارت به شبا فقط در «کارت‌های نمونه» بالا قابل‌ویرایش است؛ منطق تشخیص بانک و محاسبه شبای نمایشی در assets/js/card-to-iban-page.js می‌ماند — اگر شماره کارتی با ۶ رقم اول ناشناس وارد کنید، ویجت «بانک شناسایی نشد» نشان می‌دهد.', 'uid-theme' ) ) );

	/* ---------------- باند اعتماد کوتاه ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_citrust', array( 'sanitize_callback' => 'uid_sanitize_section_citrust', 'default' => array() ) );
	add_settings_section( 'uid_section_citrust_main', '', '__return_false', 'uid_section_citrust' );
	add_settings_field( 'items', __( 'آمار (خانه‌های باند)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_citrust', 'uid_section_citrust_main', array(
		'group' => 'uid_section_citrust', 'key' => 'items', 'default' => uid_default_ci_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'numeric', 'type' => 'select', 'label' => __( 'استایل عددی بزرگ', 'uid-theme' ), 'options' => array( '' => __( 'خیر (متن)', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );

	/* ---------------- کپسول ۳۰ ثانیه‌ای ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_citldr', array( 'sanitize_callback' => 'uid_sanitize_section_citldr', 'default' => array() ) );
	add_settings_section( 'uid_section_citldr_main', '', '__return_false', 'uid_section_citldr' );
	add_settings_field( 'heading', __( 'عنوان کپسول', 'uid-theme' ), 'uid_field_text', 'uid_section_citldr', 'uid_section_citldr_main', array( 'group' => 'uid_section_citldr', 'key' => 'heading', 'default' => 'اگر عجله دارید، همین چهار خط کافی است' ) );
	add_settings_field( 'badge', __( 'برچسب کوچک (مثل «۳۰ ثانیه»)', 'uid-theme' ), 'uid_field_text', 'uid_section_citldr', 'uid_section_citldr_main', array( 'group' => 'uid_section_citldr', 'key' => 'badge', 'default' => '۳۰ ثانیه' ) );
	add_settings_field( 'items', __( 'خطوط خلاصه (هر خط یک مورد؛ تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_citldr', 'uid_section_citldr_main', array( 'group' => 'uid_section_citldr', 'key' => 'items', 'default' => implode( "\n", uid_default_ci_tldr() ), 'rows' => 5 ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_citldr', 'uid_section_citldr_main', array( 'group' => 'uid_section_citldr', 'key' => 'btn_text', 'default' => 'فرم درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'more_text', __( 'متن پرش نرم به محاسبه‌گر', 'uid-theme' ), 'uid_field_text', 'uid_section_citldr', 'uid_section_citldr_main', array( 'group' => 'uid_section_citldr', 'key' => 'more_text', 'default' => 'و اگر می‌خواهید عدد ریزش خودتان را ببینید' ) );

	/* ---------------- محاسبه‌گر ریزش فیلد شبا ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_ciloss', array( 'sanitize_callback' => 'uid_sanitize_section_ciloss', 'default' => array() ) );
	add_settings_section( 'uid_section_ciloss_main', '', '__return_false', 'uid_section_ciloss' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'eyebrow', 'default' => 'قبل از اینکه ادامه بدهید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'heading', 'default' => 'فیلد «شماره شبا» گران‌ترین فیلد فرم شماست' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'text', 'default' => 'کاربر کارت را در جیبش دارد؛ شبا را باید در اینترنت‌بانک پیدا کند. هر کاربری که برای پیدا کردن ۲۴ رقم از سامانه شما بیرون می‌رود، یک برداشت، یک تسویه یا یک ثبت‌نام ناتمام است. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر است.' ) );
	add_settings_field( 'proof', __( 'کارت‌های اثبات (دکِ کنار محاسبه‌گر)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ciloss', 'uid_section_ciloss_main', array(
		'group' => 'uid_section_ciloss', 'key' => 'proof', 'default' => uid_default_ci_dropoff_proof(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array( array( 'key' => 'heading', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'cta_heading', 'default' => 'ترجیح می‌دهید همین حالا صحبت کنید؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'group' => 'uid_section_ciloss', 'key' => 'cta_text', 'default' => 'یک تماس کوتاه کافی است تا فرم برداشت یا تسویه شما بررسی و مسیر اتصال مشخص شود.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ciloss', 'uid_section_ciloss_main', array( 'text' => __( 'منطق محاسبه‌گر ریزش (اسلایدرها و اعداد خروجی) در assets/js/card-to-iban-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- فصل ۱: معرفی سرویس ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_ciwhat', array( 'sanitize_callback' => 'uid_sanitize_section_ciwhat', 'default' => array() ) );
	add_settings_section( 'uid_section_ciwhat_main', '', '__return_false', 'uid_section_ciwhat' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'eyebrow', 'default' => 'معرفی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'heading', 'default' => 'وب‌سرویس تبدیل کارت به شبا چیست؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'text', 'default' => 'بیشتر کاربران شماره ۱۶ رقمی کارت بانکی خود را به خاطر دارند یا روی کارت می‌بینند، اما شماره ۲۶ رقمی شبا را در دسترس ندارند. از طرف دیگر سقف انتقال کارت‌به‌کارت محدود است و سامانه‌های مالی برای مبالغ بالاتر، تسویه‌های پایا و ساتنا به شماره شبا نیاز دارند.' ) );
	add_settings_field( 'box_heading', __( 'عنوان کادر نتیجه عملی', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'box_heading', 'default' => 'نتیجه عملی: یک مرحله کمتر در قیف مالی شما' ) );
	add_settings_field( 'box_text', __( 'توضیح کادر نتیجه عملی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'box_text', 'default' => 'سامانه پذیرنده شماره کارت را از کاربر می‌گیرد و شماره شبای استاندارد متصل به همان حساب را مستقیم از شبکه بانکی دریافت می‌کند. این کار نرخ ریزش کاربران در مراحل تسویه‌حساب و ثبت اطلاعات مالی را کاهش می‌دهد.' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'fold_title', 'default' => '۱۶ رقم می‌فرستید، ۲۶ رقم می‌گیرید' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwhat', 'uid_section_ciwhat_main', array( 'group' => 'uid_section_ciwhat', 'key' => 'fold_teaser', 'default' => 'این وب‌سرویس دقیقاً چه کاری انجام می‌دهد' ) );

	/* ---------------- فصل ۲: مقایسه سناریوی کاربر ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_civs', array( 'sanitize_callback' => 'uid_sanitize_section_civs', 'default' => array() ) );
	add_settings_section( 'uid_section_civs_main', '', '__return_false', 'uid_section_civs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'eyebrow', 'default' => 'مقایسه سناریوی کاربر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'heading', 'default' => 'یک فیلد، دو نتیجه کاملاً متفاوت' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'text', 'default' => 'تفاوت این دو ستون، تفاوت میان یک تسویه انجام‌شده و یک تسویه نیمه‌کاره است.' ) );
	add_settings_field( 'bad_tag', __( 'برچسب کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'bad_tag', 'default' => 'وضعیت فعلی — پرسیدن شبا از کاربر' ) );
	add_settings_field( 'bad_title', __( 'عنوان کارت بد', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'bad_title', 'default' => 'کاربر باید کار شما را انجام دهد' ) );
	add_settings_field( 'bad_text', __( 'توضیح کارت بد', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'bad_text', 'default' => 'فرم از کاربر ۲۴ رقم می‌خواهد که در ذهنش نیست و روی هیچ کارتی نوشته نشده.' ) );
	add_settings_field( 'bad_items', __( 'موارد کارت بد (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'bad_items', 'default' => implode( "\n", uid_default_ci_vs_bad_items() ), 'rows' => 4 ) );
	add_settings_field( 'good_tag', __( 'برچسب کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'good_tag', 'default' => 'با وب‌سرویس یوآیدی — پرسیدن شماره کارت' ) );
	add_settings_field( 'good_title', __( 'عنوان کارت خوب', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'good_title', 'default' => 'سامانه شما کار را انجام می‌دهد' ) );
	add_settings_field( 'good_text', __( 'توضیح کارت خوب', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'good_text', 'default' => 'کاربر فقط شماره کارتی را وارد می‌کند که در دستش است؛ باقی ماجرا پشت صحنه اتفاق می‌افتد.' ) );
	add_settings_field( 'good_items', __( 'موارد کارت خوب (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'good_items', 'default' => implode( "\n", uid_default_ci_vs_good_items() ), 'rows' => 4 ) );
	add_settings_field( 'box_heading', __( 'عنوان کادر هشدار', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'box_heading', 'default' => 'این ریزش، ریزش در گران‌ترین نقطه قیف است' ) );
	add_settings_field( 'box_text', __( 'توضیح کادر هشدار', 'uid-theme' ), 'uid_field_textarea', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'box_text', 'default' => 'کاربری که تا مرحله برداشت وجه یا تسویه رسیده، تمام هزینه جذب و اعتمادسازی‌اش پرداخت شده است. رها کردن فرم در این نقطه، از دست دادن یک مشتری آماده پرداخت است.' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'fold_title', 'default' => 'دو مسیر برای گرفتن شبا' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_civs', 'uid_section_civs_main', array( 'group' => 'uid_section_civs', 'key' => 'fold_teaser', 'default' => 'فرم دستی در برابر تبدیل خودکار' ) );

	/* ---------------- فصل ۳: نحوه کار سرویس (پله تعاملی هاردکد) ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cihow', array( 'sanitize_callback' => 'uid_sanitize_section_cihow', 'default' => array() ) );
	add_settings_section( 'uid_section_cihow_main', '', '__return_false', 'uid_section_cihow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'eyebrow', 'default' => 'پشت صحنه یک فراخوانی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'heading', 'default' => 'از شماره کارت تا شماره شبا، در چهار قدم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'text', 'default' => 'روی هر مرحله بزنید تا ببینید در آن لحظه دقیقاً چه اتفاقی می‌افتد و چه چیزی رد و بدل می‌شود.' ) );
	add_settings_field( 'steps_notice', '', 'uid_field_notice', 'uid_section_cihow', 'uid_section_cihow_main', array( 'text' => __( 'محتوای چهار مرحله پله تعاملی، متن ثابت و از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/card-to-iban-page.php مراجعه کنید).', 'uid-theme' ) ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'fold_title', 'default' => 'مراحل عملکرد سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cihow', 'uid_section_cihow_main', array( 'group' => 'uid_section_cihow', 'key' => 'fold_teaser', 'default' => 'از ورود شماره کارت تا دریافت شبا در چهار قدم' ) );

	/* ---------------- فصل ۴: کاربردها ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_ciwho', array( 'sanitize_callback' => 'uid_sanitize_section_ciwho', 'default' => array() ) );
	add_settings_section( 'uid_section_ciwho_main', '', '__return_false', 'uid_section_ciwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'eyebrow', 'default' => 'کجا به کار می‌آید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'heading', 'default' => 'هر جا که پول باید به حساب کاربر برگردد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'text', 'default' => 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.' ) );
	add_settings_field( 'items', __( 'کارت‌های صنعت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ciwho', 'uid_section_ciwho_main', array(
		'group' => 'uid_section_ciwho', 'key' => 'items', 'default' => uid_default_ci_who(), 'add_label' => __( 'افزودن صنعت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'crypto' => __( 'صرافی/بروکر', 'uid-theme' ), 'market' => __( 'مارکت‌پلیس', 'uid-theme' ), 'psp' => __( 'پرداخت‌یاری/فین‌تک', 'uid-theme' ), 'refund' => __( 'بازپرداخت', 'uid-theme' ), 'lend' => __( 'لندتک/وام آنلاین', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب کوتاه (روی تب)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'win', 'type' => 'text', 'label' => __( 'نتیجه/دستاورد', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'fold_title', 'default' => 'کاربردهای وب‌سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ciwho', 'uid_section_ciwho_main', array( 'group' => 'uid_section_ciwho', 'key' => 'fold_teaser', 'default' => 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید' ) );

	/* ---------------- فصل ۵: مزایا + پوشش بانکی ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cigain', array( 'sanitize_callback' => 'uid_sanitize_section_cigain', 'default' => array() ) );
	add_settings_section( 'uid_section_cigain_main', '', '__return_false', 'uid_section_cigain' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'eyebrow', 'default' => 'چرا یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'heading', 'default' => 'مزایای استفاده از وب‌سرویس یوآیدی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'text', 'default' => 'این وب‌سرویس یک تبدیل ساده نیست؛ یک مرحله کامل را از مسیر کاربر و از میز پشتیبانی شما حذف می‌کند.' ) );
	add_settings_field( 'items', __( 'کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_cigain', 'uid_section_cigain_main', array(
		'group' => 'uid_section_cigain', 'key' => 'items', 'default' => uid_default_ci_gain(), 'add_label' => __( 'افزودن مزیت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'churn' => __( 'ریزش', 'uid-theme' ), 'human' => __( 'خطای انسانی', 'uid-theme' ), 'scale' => __( 'مقیاس‌پذیری', 'uid-theme' ), 'fast' => __( 'سرعت', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ), 'free' => __( 'هزینه', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'برچسب کوتاه پایین کارت', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'banks_heading', __( 'عنوان بخش پوشش بانکی', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'banks_heading', 'default' => 'پوشش بانکی سرویس' ) );
	add_settings_field( 'banks_text', __( 'توضیح بخش پوشش بانکی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'banks_text', 'default' => 'این وب‌سرویس به شبکه شتاب و زیرساخت بانکی متصل است و از کارت‌های تمام بانک‌ها و موسسات مالی مجاز کشور پشتیبانی می‌کند.' ) );
	add_settings_field( 'banks', __( 'فهرست بانک‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'banks', 'default' => implode( "\n", uid_default_ci_banks() ), 'rows' => 10 ) );
	add_settings_field( 'banks_more', __( 'برچسب پایانی', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'banks_more', 'default' => 'و سایر بانک‌ها و موسسات مجاز عضو شتاب' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'fold_title', 'default' => 'مزایای استفاده از این وب‌سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cigain', 'uid_section_cigain_main', array( 'group' => 'uid_section_cigain', 'key' => 'fold_teaser', 'default' => 'شش دلیلی که تیم فنی و تیم مالی هر دو قبول می‌کنند' ) );

	/* ---------------- فصل ۶: تیم متخصص ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_citeam', array( 'sanitize_callback' => 'uid_sanitize_section_citeam', 'default' => array() ) );
	add_settings_section( 'uid_section_citeam_main', '', '__return_false', 'uid_section_citeam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'eyebrow', 'default' => 'پشت این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان (بعد از «یک API»)', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'heading', 'default' => ' نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'text', 'default' => 'این سرویس مستقیماً روی مسیر پول کاربران شما می‌نشیند. به همین دلیل تیم فنی یوآیدی از اولین تماس تا اولین تسویه موفق روی محیط عملیاتی همراه شماست.' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_citeam', 'uid_section_citeam_main', array(
		'group' => 'uid_section_citeam', 'key' => 'rows', 'default' => uid_default_ci_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'connect' => __( 'اتصال', 'uid-theme' ), 'report' => __( 'گزارش', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به تیم فنی شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_ci_pledge_items() ), 'rows' => 4 ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'btn_text', 'default' => 'درخواست کلید آزمایشی' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'fold_title', 'default' => 'تیم متخصص و پشتیبانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_citeam', 'uid_section_citeam_main', array( 'group' => 'uid_section_citeam', 'key' => 'fold_teaser', 'default' => 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود' ) );

	/* ---------------- فصل ۷: مستندات فنی ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cidev', array( 'sanitize_callback' => 'uid_sanitize_section_cidev', 'default' => array() ) );
	add_settings_section( 'uid_section_cidev_main', '', '__return_false', 'uid_section_cidev' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'eyebrow', 'default' => 'برای تیم توسعه شما' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'heading', 'default' => 'یک فراخوانی، یک پاسخ، تمام' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'text', 'default' => 'برای استفاده از سرویس API تبدیل شماره کارت به شماره شبای یوآیدی می‌بایست سرویس زیر را با پارامترهای درخواستی فراخوانی نمایید. این سرویس با دریافت شماره کارت کاربر، شماره شبای کاربر را در پاسخ برمی‌گرداند.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'btn1_text', 'default' => 'دریافت کلید وب‌سرویس' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_cidev', 'uid_section_cidev_main', array( 'text' => __( 'نمونه‌کدهای درخواست/پاسخ و جدول پارامترها، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/card-to-iban-page.php مراجعه کنید).', 'uid-theme' ) ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'fold_title', 'default' => 'بخش فنی و نمونه کد' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cidev', 'uid_section_cidev_main', array( 'group' => 'uid_section_cidev', 'key' => 'fold_teaser', 'default' => 'نمونه درخواست، نمونه پاسخ و جدول پارامترها' ) );

	/* ---------------- فصل ۸: سرویس‌های مکمل ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cixsell', array( 'sanitize_callback' => 'uid_sanitize_section_cixsell', 'default' => array() ) );
	add_settings_section( 'uid_section_cixsell_main', '', '__return_false', 'uid_section_cixsell' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'eyebrow', 'default' => 'در کنار این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'heading', 'default' => 'اگر به چیزی بیش از شماره شبا نیاز دارید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'text', 'default' => 'خروجی مستقیم این متد شماره شبا است. اگر لازم است نام دارنده حساب، کد ملی یا تطابق مالکیت هم بررسی شود، سرویس‌های زیر مکمل همین فراخوانی هستند.' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس', 'uid-theme' ), 'uid_field_repeater', 'uid_section_cixsell', 'uid_section_cixsell_main', array(
		'group' => 'uid_section_cixsell', 'key' => 'items', 'default' => uid_default_ci_xsell(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'iban' => __( 'شبا', 'uid-theme' ), 'match' => __( 'تطابق', 'uid-theme' ), 'cardid' => __( 'کارت + کد ملی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'fold_title', 'default' => 'سرویس‌های مکمل و مرتبط' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cixsell', 'uid_section_cixsell_main', array( 'group' => 'uid_section_cixsell', 'key' => 'fold_teaser', 'default' => 'استعلام شبا، تطبیق شبا و تطبیق کارت با کد ملی' ) );

	/* ---------------- فصل ۹: سوالات متداول ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cifaq', array( 'sanitize_callback' => 'uid_sanitize_section_cifaq', 'default' => array() ) );
	add_settings_section( 'uid_section_cifaq_main', '', '__return_false', 'uid_section_cifaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cifaq', 'uid_section_cifaq_main', array( 'group' => 'uid_section_cifaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cifaq', 'uid_section_cifaq_main', array( 'group' => 'uid_section_cifaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس، این‌ها را بخوانید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cifaq', 'uid_section_cifaq_main', array( 'group' => 'uid_section_cifaq', 'key' => 'heading', 'default' => 'سوالات متداول وب‌سرویس تبدیل کارت به شبا' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_cifaq', 'uid_section_cifaq_main', array(
		'group' => 'uid_section_cifaq', 'key' => 'items', 'default' => uid_default_ci_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cifaq', 'uid_section_cifaq_main', array( 'group' => 'uid_section_cifaq', 'key' => 'fold_title', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_cifaq', 'uid_section_cifaq_main', array( 'group' => 'uid_section_cifaq', 'key' => 'fold_teaser', 'default' => 'پوشش بانکی، هزینه، چند حسابه و زمان اتصال' ) );

	/* ---------------- مشخصات عملیاتی (پنل تیره) ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_ciadv', array( 'sanitize_callback' => 'uid_sanitize_section_ciadv', 'default' => array() ) );
	add_settings_section( 'uid_section_ciadv_main', '', '__return_false', 'uid_section_ciadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'eyebrow', 'default' => 'مشخصات عملیاتی وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'heading', 'default' => 'پنج نکته‌ای که در ارزیابی فنی از شما می‌پرسند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'text', 'default' => 'هر مورد زیر مستقیماً از مستندات سرویس تبدیل شماره کارت به شبا آمده است — نه یک ادعای تبلیغاتی.' ) );
	add_settings_field( 'items', __( 'آیتم‌های پنل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ciadv', 'uid_section_ciadv_main', array(
		'group' => 'uid_section_ciadv', 'key' => 'items', 'default' => uid_default_ci_adv(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'card' => __( 'کارت', 'uid-theme' ), 'doc' => __( 'سند', 'uid-theme' ), 'coin' => __( 'سکه/هزینه', 'uid-theme' ), 'clock' => __( 'زمان', 'uid-theme' ), 'bank' => __( 'بانک', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عنوان کوتاه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد نوار پیشرفت (عدد بین ۰ تا ۱۰۰)', 'uid-theme' ) ),
			array( 'key' => 'feat', 'type' => 'select', 'label' => __( 'کارت برجسته (بزرگ‌تر)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'wide', 'type' => 'select', 'label' => __( 'عرض دوبرابر', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'txt', 'type' => 'select', 'label' => __( 'مقدار متنی (نه عدد بزرگ)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'foot_text', 'default' => 'می‌خواهید این سرویس را روی داده واقعی خودتان تست کنید؟' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ciadv', 'uid_section_ciadv_main', array( 'group' => 'uid_section_ciadv', 'key' => 'btn_text', 'default' => 'درخواست دسترسی سندباکس' ) );

	/* ---------------- محاسبه‌گر تعرفه ماهانه ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_ciprice', array( 'sanitize_callback' => 'uid_sanitize_section_ciprice', 'default' => array() ) );
	add_settings_section( 'uid_section_ciprice_main', '', '__return_false', 'uid_section_ciprice' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'group' => 'uid_section_ciprice', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'group' => 'uid_section_ciprice', 'key' => 'eyebrow', 'default' => 'مدل محاسبه هزینه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'group' => 'uid_section_ciprice', 'key' => 'heading', 'default' => 'فقط بابت تبدیل‌های موفق هزینه می‌دهید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'group' => 'uid_section_ciprice', 'key' => 'text', 'default' => 'پاسخ‌های ناموفق و کارت‌های نامعتبر هزینه‌ای ندارند. تعداد تبدیل تخمینی ماهانه خود را جابه‌جا کنید تا پله تعرفه متناسب با کسب‌وکارتان را ببینید.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'group' => 'uid_section_ciprice', 'key' => 'btn_text', 'default' => 'دریافت تعرفه دقیق این حجم' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ciprice', 'uid_section_ciprice_main', array( 'text' => __( 'منطق محاسبه‌گر (پله‌های تخفیف و قیمت پایه) در assets/js/card-to-iban-page.js تعریف شده و برای انتشار نهایی باید با تعرفه واقعی جایگزین شود؛ از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- بند تماس میان‌صفحه ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cilastcall', array( 'sanitize_callback' => 'uid_sanitize_section_cilastcall', 'default' => array() ) );
	add_settings_section( 'uid_section_cilastcall_main', '', '__return_false', 'uid_section_cilastcall' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cilastcall', 'uid_section_cilastcall_main', array( 'group' => 'uid_section_cilastcall', 'key' => 'heading', 'default' => 'هر روزی که این سرویس فعال نیست، ریزش فیلد شبا را پرداخت می‌کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cilastcall', 'uid_section_cilastcall_main', array( 'group' => 'uid_section_cilastcall', 'key' => 'text', 'default' => 'کاربری که امروز وسط فرم برداشت رها می‌کند، فردا برنمی‌گردد. یک تماس کوتاه کافی است تا کارشناس یوآیدی فرم شما را بررسی و دسترسی سندباکس را فعال کند.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_cilastcall', 'uid_section_cilastcall_main', array( 'group' => 'uid_section_cilastcall', 'key' => 'btn_text', 'default' => 'فرم درخواست سرویس' ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_ci_group', 'uid_section_cilead', array( 'sanitize_callback' => 'uid_sanitize_section_cilead', 'default' => array() ) );
	add_settings_section( 'uid_section_cilead_main', '', '__return_false', 'uid_section_cilead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'heading', 'default' => 'فرم درخواست فعال‌سازی وب‌سرویس تبدیل کارت به شبا' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس تبدیل شماره کارت به شبا، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nکلید آزمایشی پیش از قرارداد\nهزینه فقط بابت استعلام موفق\nتعرفه پلکانی متناسب با حجم" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'form_title', 'default' => 'درخواست وب‌سرویس تبدیل کارت به شبا' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'form_hint', 'default' => 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'biztype_options', 'default' => "صرافی ارز دیجیتال یا بروکر\nمارکت‌پلیس و فروشگاه آنلاین\nپرداخت‌یاری، فین‌تک و نئوبانک\nسامانه بازگشت وجه و پشتیبانی سفارش\nلندتک و وام آنلاین\nسایر کسب‌وکارها" ) );
	add_settings_field( 'route_text', __( 'متن هشدار کاربر شخصی (قبل از لینک ثنا)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'route_text', 'default' => 'این وب‌سرویس فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی به شماره شبای خود نیاز دارید، آن را در اینترنت‌بانک یا اپلیکیشن بانک خودتان ببینید؛ برای خدمات فردی ' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_cilead', 'uid_section_cilead_main', array( 'group' => 'uid_section_cilead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_ci_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_cihero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'samples'   => uid_sanitize_repeater_rows( $input['samples'] ?? '[]', array(
			array( 'key' => 'card', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'blocked', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_citrust( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'numeric', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_citldr( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'badge'     => sanitize_text_field( $input['badge'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
		'more_text' => sanitize_text_field( $input['more_text'] ?? '' ),
	);
}

function uid_sanitize_section_ciloss( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'proof'       => uid_sanitize_repeater_rows( $input['proof'] ?? '[]', array(
			array( 'key' => 'heading', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_ciwhat( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'box_heading' => sanitize_text_field( $input['box_heading'] ?? '' ),
		'box_text'    => sanitize_textarea_field( $input['box_text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_civs( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'bad_tag'     => sanitize_text_field( $input['bad_tag'] ?? '' ),
		'bad_title'   => sanitize_text_field( $input['bad_title'] ?? '' ),
		'bad_text'    => sanitize_textarea_field( $input['bad_text'] ?? '' ),
		'bad_items'   => sanitize_textarea_field( $input['bad_items'] ?? '' ),
		'good_tag'    => sanitize_text_field( $input['good_tag'] ?? '' ),
		'good_title'  => sanitize_text_field( $input['good_title'] ?? '' ),
		'good_text'   => sanitize_textarea_field( $input['good_text'] ?? '' ),
		'good_items'  => sanitize_textarea_field( $input['good_items'] ?? '' ),
		'box_heading' => sanitize_text_field( $input['box_heading'] ?? '' ),
		'box_text'    => sanitize_textarea_field( $input['box_text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_cihow( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_ciwho( $input ) {
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

function uid_sanitize_section_cigain( $input ) {
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
		'banks'         => sanitize_textarea_field( $input['banks'] ?? '' ),
		'banks_more'    => sanitize_text_field( $input['banks_more'] ?? '' ),
		'fold_title'    => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'   => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_citeam( $input ) {
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
		'btn_text'       => sanitize_text_field( $input['btn_text'] ?? '' ),
		'fold_title'     => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'    => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_cidev( $input ) {
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

function uid_sanitize_section_cixsell( $input ) {
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

function uid_sanitize_section_cifaq( $input ) {
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

function uid_sanitize_section_ciadv( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'percent', 'type' => 'text' ),
			array( 'key' => 'feat', 'type' => 'text' ),
			array( 'key' => 'wide', 'type' => 'text' ),
			array( 'key' => 'txt', 'type' => 'text' ),
		) ),
		'foot_text' => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_ciprice( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_cilastcall( $input ) {
	return array(
		'heading'  => sanitize_text_field( $input['heading'] ?? '' ),
		'text'     => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text' => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_cilead( $input ) {
	return array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => sanitize_text_field( $input['heading'] ?? '' ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'           => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'      => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'       => sanitize_text_field( $input['form_hint'] ?? '' ),
		'biztype_options' => sanitize_textarea_field( $input['biztype_options'] ?? '' ),
		'route_text'      => sanitize_textarea_field( $input['route_text'] ?? '' ),
		'submit_text'     => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'       => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_text'    => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_ci_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس تبدیل کارت به شبا', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی همین امروز تماس بگیرد: کلید آزمایشی، مستندات سرویس', 'uid-theme' ); ?> <span class="lat">inquiry/card</span> <?php esc_html_e( 'و تعرفه متناسب با حجم درخواست ماهانه شما.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'صرافی ارز دیجیتال یا بروکر', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بانک، نئوبانک یا فین‌تک', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'لندتک و وام آنلاین', 'uid-theme' ); ?></option><option><?php esc_html_e( 'اقتصاد مشارکتی و پلتفرم خدماتی', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'امضای دیجیتال و قرارداد الکترونیک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بیمه و خدمات مالی', 'uid-theme' ); ?></option>
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
