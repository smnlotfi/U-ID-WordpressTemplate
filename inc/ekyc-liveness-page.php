<?php
/**
 * صفحه اختصاصی «وب‌سرویس احراز هویت تصویری» — دقیقاً همان الگوی صفحات قبلی:
 * برگه‌ی واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «ek» نام‌گذاری شده‌اند تا با سکشن‌های هم‌نام صفحات دیگر
 * در نام آپشن‌های wp_options تداخل نکنند.
 *
 * ده سکشن این صفحه (avp تا faq) در موبایل زیر ۹۰۰px به یک سیستم «فصل تاخوردنی»
 * تبدیل می‌شوند (نگاه کنید به assets/js/ekyc-liveness-page.js) — این رفتار کاملاً
 * سمت کلاینت و مبتنی بر ویژگی‌های data-fold/data-fold-title/data-fold-teaser است؛
 * در دسکتاپ این سکشن‌ها دقیقاً مثل بقیه سکشن‌ها رندر و نمایش داده می‌شوند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_EKYC_TEMPLATE', 'template-ekyc-liveness.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_ek_sections_registry() {
	return array(
		'ekhero'     => array( 'label' => __( 'هیرو + شبیه‌سازی جلسه احراز هویت', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'ekstats'    => array( 'label' => __( 'نوار آمار منتشرشده', 'uid-theme' ),                'icon' => 'dashicons-chart-bar' ),
		'ekloss'     => array( 'label' => __( 'محاسبه‌گر ریزش (قبل از فصل‌ها)', 'uid-theme' ),      'icon' => 'dashicons-chart-area' ),
		'ekavp'      => array( 'label' => __( 'فصل ۱ — Passive در برابر Active', 'uid-theme' ),    'icon' => 'dashicons-controls-repeat' ),
		'ekface'     => array( 'label' => __( 'فصل ۲ — تطبیق چهره با ثبت‌احوال', 'uid-theme' ),     'icon' => 'dashicons-visibility' ),
		'ekflow'     => array( 'label' => __( 'فصل ۳ — مراحل عملکرد وب‌سرویس', 'uid-theme' ),       'icon' => 'dashicons-list-view' ),
		'ekvs'       => array( 'label' => __( 'فصل ۴ — هوش مصنوعی در برابر انسان', 'uid-theme' ),   'icon' => 'dashicons-editor-table' ),
		'ekwho'      => array( 'label' => __( 'فصل ۵ — کاربردها (انتخابگر صنعت)', 'uid-theme' ),    'icon' => 'dashicons-store' ),
		'ekspoof'    => array( 'label' => __( 'فصل ۶ — مقابله با جعل هویت', 'uid-theme' ),          'icon' => 'dashicons-shield' ),
		'ekteam'     => array( 'label' => __( 'فصل ۷ — تیم متخصص و پشتیبانی', 'uid-theme' ),        'icon' => 'dashicons-businessperson' ),
		'ekdev'      => array( 'label' => __( 'فصل ۸ — بخش فنی و نمونه‌کد', 'uid-theme' ),          'icon' => 'dashicons-editor-code' ),
		'ekxsell'    => array( 'label' => __( 'فصل ۹ — سرویس‌های مکمل', 'uid-theme' ),              'icon' => 'dashicons-networking' ),
		'ekfaq'      => array( 'label' => __( 'فصل ۱۰ — سوالات متداول', 'uid-theme' ),              'icon' => 'dashicons-editor-help' ),
		'ekadv'      => array( 'label' => __( 'شاخص‌های عملکرد (پنل تیره)', 'uid-theme' ),          'icon' => 'dashicons-awards' ),
		'ekprice'    => array( 'label' => __( 'محاسبه‌گر تعرفه', 'uid-theme' ),                     'icon' => 'dashicons-calculator' ),
		'ekcallband' => array( 'label' => __( 'بند میانی تماس تلفنی', 'uid-theme' ),                'icon' => 'dashicons-phone' ),
		'eklead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),                'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_ek_layout() {
	$registry = uid_ek_sections_registry();
	$saved    = get_option( 'uid_ek_layout', array() );

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

function uid_sanitize_ek_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_ek_sections_registry();
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
function uid_ek_folded_slugs() {
	return array( 'ekavp', 'ekface', 'ekflow', 'ekvs', 'ekwho', 'ekspoof', 'ekteam', 'ekdev', 'ekxsell', 'ekfaq' );
}

function uid_render_ek_sections() {
	$folded      = uid_ek_folded_slugs();
	$layout      = uid_get_ek_layout();
	$folded_on   = 0;
	foreach ( $layout as $row ) {
		if ( ! empty( $row['enabled'] ) && in_array( $row['slug'], $folded, true ) ) $folded_on++;
	}

	$foldbar_done = false;
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		if ( ! $foldbar_done && in_array( $row['slug'], $folded, true ) ) {
			uid_render_ek_foldbar( $folded_on );
			$foldbar_done = true;
		}
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
}

/**
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود
 * (نگاه کنید به assets/css/ekyc-liveness-page.css). دکمه «باز کردن همه» و
 * نوار پیشرفت مطالعه توسط JS مدیریت می‌شوند.
 */
function uid_render_ek_foldbar( $count ) {
	?>
	<div class="foldbar" id="ekFoldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <button type="button" id="ekFoldAll"><?php esc_html_e( 'باز کردن همه', 'uid-theme' ); ?></button>
	  <span class="fold-meter"><i id="ekFoldMeter"></i></span>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_ek_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_ek_x_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_ek_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_ek_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_ek_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
function uid_ek_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_ek_flow_icon_svg( $key ) {
	$icons = array(
		'doc'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'video'  => '<path d="M23 7l-7 5 7 5z"/><rect x="1" y="5" width="15" height="14" rx="2"/>',
		'shield' => '<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M8 10a4 4 0 011-2.6"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/>',
		'face'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'send'   => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	);
	return $icons[ $key ] ?? $icons['doc'];
}
function uid_ek_who_icon_svg( $key ) {
	$icons = array(
		'bank'   => '<path d="M3 21h18M4 10h16M5 10V7l7-4 7 4v3M6 10v11M10 10v11M14 10v11M18 10v11"/>',
		'crypto' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'lend'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'share'  => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'sign'   => '<path d="M3 17.5c3.5 0 3.5-11 7-11s3.5 11 7 11c1.6 0 2.6-2.3 3.2-4.5"/><path d="M3 21h18"/>',
	);
	return $icons[ $key ] ?? $icons['bank'];
}
function uid_ek_spoof_icon_svg( $key ) {
	$icons = array(
		'print'  => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="M21 15l-5-5L5 21"/>',
		'screen' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
		'mask'   => '<path d="M4 8c0-3 3.6-5 8-5s8 2 8 5c0 6-3 12-8 12S4 14 4 8z"/><path d="M9 11h.01M15 11h.01M9.5 15.5c1.6 1 3.4 1 5 0"/>',
		'video'  => '<path d="M23 7l-7 5 7 5z"/><rect x="1" y="5" width="15" height="14" rx="2"/><path d="M3 3l18 18"/>',
	);
	return $icons[ $key ] ?? $icons['print'];
}
function uid_ek_xsell_icon_svg( $key ) {
	$icons = array(
		'pwa'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'doc'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'crypto' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
	);
	return $icons[ $key ] ?? $icons['pwa'];
}
function uid_ek_team_icon_svg( $key ) {
	$icons = array(
		'expert' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'chart'  => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
		'docs'   => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/>',
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
	);
	return $icons[ $key ] ?? $icons['expert'];
}

/* =====================================================================
 * ۱) هیرو + شبیه‌سازی جلسه احراز هویت (شبیه‌سازی نمایشی، داده آن هاردکد است)
 * ===================================================================== */
function uid_default_ek_scenarios() {
	return array(
		array( 'sc' => 'ok', 'label' => 'کاربر واقعی', 'nid' => '0079542318', 'birth' => '1372/04/19', 'cap_live' => 'بافت پوست، بازتاب نور و عمق تصویر طبیعی است.', 'cap_end' => 'هویت تایید شد — کاربر همان صاحب مدرک است.' ),
		array( 'sc' => 'print', 'label' => 'عکس چاپ‌شده', 'nid' => '0079542318', 'birth' => '1372/04/19', 'cap_live' => 'بافت کاغذ و نبود عمق تصویر تشخیص داده شد.', 'cap_end' => 'حمله با عکس چاپ‌شده مسدود شد.' ),
		array( 'sc' => 'screen', 'label' => 'نمایش روی مانیتور', 'nid' => '0079542318', 'birth' => '1372/04/19', 'cap_live' => 'الگوی نوری و نوسان صفحه نمایش تشخیص داده شد.', 'cap_end' => 'پخش تصویر روی مانیتور مسدود شد.' ),
		array( 'sc' => 'nomatch', 'label' => 'چهره غیرمنطبق', 'nid' => '0064912877', 'birth' => '1368/11/02', 'cap_live' => 'فرد زنده است، اما هنوز هویتش تایید نشده.', 'cap_end' => 'چهره با تصویر متناظر این کد ملی مطابقت ندارد.' ),
	);
}
function uid_ek_scenarios_json() {
	$rows = uid_section_val( 'ekhero', 'scenarios', uid_default_ek_scenarios() );
	if ( ! is_array( $rows ) || empty( $rows ) ) $rows = uid_default_ek_scenarios();
	$out = array();
	foreach ( $rows as $r ) {
		$sc = sanitize_key( $r['sc'] ?? '' );
		if ( ! $sc ) continue;
		$out[ $sc ] = array(
			'nid' => $r['nid'] ?? '', 'birth' => $r['birth'] ?? '',
			'capLive' => $r['cap_live'] ?? '', 'capEnd' => $r['cap_end'] ?? '',
		);
	}
	return wp_json_encode( $out );
}
function uid_render_section_ekhero() {
	$tag       = uid_section_tag( 'ekhero', 'h1' );
	$tags      = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ekhero', 'tags', "بدون نصب اپلیکیشن\nهزینه فقط بابت احراز موفق\nپاسخ در کمتر از ۵ ثانیه\nپشتیبانی REST و gRPC" ) ) ) );
	$scenarios = uid_section_val( 'ekhero', 'scenarios', uid_default_ek_scenarios() );
	if ( ! is_array( $scenarios ) || empty( $scenarios ) ) $scenarios = uid_default_ek_scenarios();
	?>
	<section class="dark heroA" id="top">
	  <div class="ek-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌ احراز هویت', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'وب‌سرویس احراز هویت تصویری', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="ek-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ekhero', 'eyebrow', __( 'وب‌سرویس احراز هویت تصویری · e-KYC API', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'ekhero', 'heading', __( 'کاربرتان را وادار نکنید سرش را بچرخاند.<mark>او ثبت‌نام را رها می‌کند.</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'ekhero', 'text', __( 'هر فرمان اضافه در احراز هویت — پلک بزن، سرت را بچرخان، این متن را بخوان — یک پله ریزش است. وب‌سرویس احراز هویت تصویری یوآیدی با فناوری Passive Liveness فقط ۵ ثانیه نگاه ثابت به دوربین می‌خواهد، و چهره کاربر را با تصویر رسمی پایگاه ثبت‌احوال کشور با دقت ۹۸.۱۷ درصد تطبیق می‌دهد.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="ek-btn btn-cta" data-open-modal><?php echo uid_ek_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ekhero', 'btn1_text', __( 'درخواست فعال‌سازی سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ek-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ek_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_ek_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- شبیه‌سازی زنده یک جلسه احراز هویت — تماماً نمایشی، داده‌ها ساختگی، ساختار پاسخ واقعی است -->
	      <div class="kyw rv rv-d2" id="kyw" data-phase="idle" data-result="">
	        <div class="kyw-hd"><span class="live"></span><b><?php esc_html_e( 'شبیه‌سازی یک جلسه احراز هویت', 'uid-theme' ); ?></b>
	          <span class="mono">v2/authenticate-user-info</span></div>

	        <div class="kyw-chips" id="kyw_chips" role="tablist" aria-label="<?php esc_attr_e( 'سناریوی احراز هویت', 'uid-theme' ); ?>">
	          <?php foreach ( $scenarios as $row ) :
	            $sc   = sanitize_key( $row['sc'] ?? '' );
	            $is_ok = ( 'ok' === $sc );
	          ?>
	          <button class="kyw-chip<?php echo $is_ok ? ' on' : ' risk'; ?>" type="button" data-sc="<?php echo esc_attr( $sc ); ?>" role="tab" aria-selected="<?php echo $is_ok ? 'true' : 'false'; ?>"><i></i><?php echo esc_html( $row['label'] ?? '' ); ?></button>
	          <?php endforeach; ?>
	        </div>

	        <div class="kyw-stage">
	          <span class="kyw-br tl"></span><span class="kyw-br tr"></span>
	          <span class="kyw-br bl"></span><span class="kyw-br br"></span>
	          <span class="kyw-scan"></span>

	          <svg class="face" viewBox="0 0 160 190" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="<?php esc_attr_e( 'نمای شماتیک چهره در کادر دوربین', 'uid-theme' ); ?>">
	            <g class="outline">
	              <path d="M80 14c-27 0-44 19-44 46 0 10 1 19 3 27 3 13 9 25 17 34 8 9 16 14 24 14s16-5 24-14c8-9 14-21 17-34 2-8 3-17 3-27 0-27-17-46-44-46z"/>
	              <path d="M56 74c4-4 12-4 16 0M88 74c4-4 12-4 16 0"/>
	              <path d="M80 84v18c0 4-3 6-7 6"/>
	              <path d="M66 128c8 6 20 6 28 0"/>
	              <path d="M40 176c6-14 22-22 40-22s34 8 40 22"/>
	            </g>
	            <g class="mesh">
	              <path d="M36 60h88M32 84h96M36 108h88M44 132h72"/>
	              <path d="M80 20v140M56 24v128M104 24v128"/>
	              <path d="M36 60l44 48 44-48M36 108l44-48 44 48"/>
	            </g>
	            <g class="pt">
	              <circle cx="64" cy="74" r="3.4"/><circle cx="96" cy="74" r="3.4"/>
	              <circle cx="80" cy="102" r="3"/><circle cx="66" cy="128" r="3"/>
	              <circle cx="94" cy="128" r="3"/><circle cx="80" cy="18" r="3"/>
	              <circle cx="36" cy="84" r="3"/><circle cx="124" cy="84" r="3"/>
	              <circle cx="80" cy="158" r="3"/>
	            </g>
	          </svg>

	          <span class="kyw-ring" id="kyw_ring">
	            <svg viewBox="0 0 46 46"><circle class="bg" cx="23" cy="23" r="20"/><circle class="fg" id="kyw_arc" cx="23" cy="23" r="20"/></svg>
	            <b id="kyw_sec">۵</b>
	          </span>

	          <span class="kyw-stamp">
	            <span class="disc" id="kyw_disc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
	            <span class="pct" id="kyw_pct">۹۸٫۱۷٪</span>
	          </span>

	          <span class="kyw-cap"><i></i><span id="kyw_cap"><?php esc_html_e( 'برای شروع، سناریو را انتخاب و دکمه زیر را بزنید.', 'uid-theme' ); ?></span></span>
	        </div>

	        <div class="kyw-steps" id="kyw_steps">
	          <div class="kyw-step" data-st="0"><span class="n"><em>۱</em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></span><span class="t"><?php esc_html_e( 'اطلاعات پایه', 'uid-theme' ); ?></span></div>
	          <div class="kyw-step" data-st="1"><span class="n"><em>۲</em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></span><span class="t"><?php esc_html_e( 'ویدیوی سلفی', 'uid-theme' ); ?></span></div>
	          <div class="kyw-step" data-st="2"><span class="n"><em>۳</em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></span><span class="t"><?php esc_html_e( 'تشخیص زنده‌بودن', 'uid-theme' ); ?></span></div>
	          <div class="kyw-step" data-st="3"><span class="n"><em>۴</em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></span><span class="t"><?php esc_html_e( 'تطبیق ثبت‌احوال', 'uid-theme' ); ?></span></div>
	          <div class="kyw-step" data-st="4"><span class="n"><em>۵</em><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></span><span class="t"><?php esc_html_e( 'ارسال نتیجه', 'uid-theme' ); ?></span></div>
	        </div>

	        <div style="margin-block-start:13px">
	          <button class="ek-btn btn-cta ek-btn-block" type="button" id="kyw_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg> <?php esc_html_e( 'اجرای جلسه احراز هویت', 'uid-theme' ); ?></button>
	        </div>

	        <div class="kyw-out">
	          <div class="rswitch">
	            <button type="button" class="on" data-rview="g"><?php esc_html_e( 'نمای ساختاری', 'uid-theme' ); ?></button>
	            <button type="button" data-rview="j"><?php esc_html_e( 'پاسخ کال‌بک ', 'uid-theme' ); ?><span class="lat">JSON</span></button>
	            <span class="kyw-badge wait" id="kyw_badge"><?php esc_html_e( 'در انتظار اجرا', 'uid-theme' ); ?></span>
	          </div>

	          <div data-rpane="g" class="on">
	            <div class="idgrid" id="k_grid">
	              <div class="idrow"><span class="k"><?php esc_html_e( 'وضعیت احراز هویت', 'uid-theme' ); ?></span><span class="v mut" id="k_state">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'درصد تطابق چهره', 'uid-theme' ); ?></span><span class="v mut" id="k_score">—</span></div>
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'علت وضعیت (reason)', 'uid-theme' ); ?></span><span class="v mut" id="k_reason">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'تشخیص زنده‌بودن', 'uid-theme' ); ?></span><span class="v mut" id="k_live">—</span></div>
	              <div class="idrow"><span class="k"><?php esc_html_e( 'کد ملی کاربر', 'uid-theme' ); ?></span><span class="v mut" id="k_nid">—</span></div>
	              <div class="idrow wide"><span class="k"><?php esc_html_e( 'شناسه جلسه (sessionId)', 'uid-theme' ); ?></span><span class="v mut" id="k_sid">—</span></div>
	            </div>
	          </div>

	          <div data-rpane="j">
	            <pre class="rjson" id="k_json">{
  "sessionId": "…",
  "status": { "code": "…", "message": "…" },
  "userInformation": { "nationalId": "…", "date": "…", "sex": "…" },
  "authenticationStatus": { "state": "…", "reason": "…" }
}</pre>
	          </div>

	          <div class="kyw-meta">
	            <span><?php esc_html_e( 'زمان کل پردازش: ', 'uid-theme' ); ?><b id="k_ms">—</b></span>
	            <span><?php esc_html_e( 'موتور: ', 'uid-theme' ); ?><b>Passive Liveness + Face Verification</b></span>
	          </div>
	        </div>

	        <div class="kyw-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'این شبیه‌سازی با داده نمونه کار می‌کند و ساختار واقعی پاسخ کال‌بک سرویس را نشان می‌دهد. برای اجرای واقعی روی کاربران خودتان، کلید API و دسترسی سندباکس لازم است.', 'uid-theme' ); ?></span></div>

	        <div class="idw-lead" id="kywLead">
	          <p><?php esc_html_e( 'همین جریان را روی ثبت‌نام کاربران خودتان می‌خواهید؟ ', 'uid-theme' ); ?><b><?php esc_html_e( 'شماره‌تان را بگذارید', 'uid-theme' ); ?></b><?php esc_html_e( '، کارشناس یوآیدی دسترسی سندباکس را امروز فعال می‌کند.', 'uid-theme' ); ?></p>
	          <form class="micro" id="microForm" novalidate>
	            <div class="micro-fields">
	              <input type="hidden" name="source" value="ekyc-lp-hero">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ek-btn btn-cta" type="button" data-submit><?php esc_html_e( 'دریافت دسترسی تست', 'uid-theme' ); ?></button>
	              </div>
	            </div>
	            <div class="form-ok"><?php echo uid_ek_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
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
 * ۲) نوار آمار منتشرشده
 * ===================================================================== */
function uid_default_ek_stats() {
	return array(
		array( 'value' => '۹۸٫۱۷٪', 'is_text' => '', 'label' => 'دقت تطبیق چهره با تصویر ثبت‌احوال' ),
		array( 'value' => '۰٫۴٪', 'is_text' => '', 'label' => 'نرخ رد نادرست (FRR) — کاربر واقعی کمتر رد می‌شود' ),
		array( 'value' => '۰٫۸۵٪', 'is_text' => '', 'label' => 'نرخ پذیرش نادرست (FAR) — هویت جعلی عبور نمی‌کند' ),
		array( 'value' => 'زیر ۵ ثانیه', 'is_text' => '1', 'label' => 'زمان پردازش و پاسخ‌دهی سرویس' ),
	);
}
function uid_render_section_ekstats() {
	$items = uid_section_val( 'ekstats', 'items', uid_default_ek_stats() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="ek-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="qstat"><span class="v<?php echo ! empty( $it['is_text'] ) ? ' txt' : ''; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $it['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) محاسبه‌گر ریزش (بند تیره پیش از فصل‌ها) — محاسبه‌گر تماماً هاردکد/تعاملی،
 *    کارت‌های اثبات و بند تماس داینامیک هستند
 * ===================================================================== */
function uid_default_ek_proof() {
	return array(
		array( 'title' => 'هیچ فرمانی به کاربر داده نمی‌شود', 'text' => 'الگوریتم با تحلیل بافت پوست، الگوهای نوری، عمق تصویر و ریزحرکات طبیعی چهره، زنده‌بودن را تشخیص می‌دهد. کاربر فقط ۵ ثانیه به دوربین نگاه می‌کند.' ),
		array( 'title' => 'مرجع تطبیق، پایگاه ملی ثبت‌احوال است', 'text' => 'چهره کاربر با تصویر رسمی متناظر کد ملی در سازمان ثبت‌احوال کشور مقایسه می‌شود — نه با یک عکس آپلودی یا یک پایگاه داخلی.' ),
		array( 'title' => 'پاسخ در کمتر از ۵ ثانیه، ۲۴ ساعته', 'text' => 'بدون صف اپراتور، بدون ساعت کاری و بدون سقف تعداد درخواست هم‌زمان. کاربر نیمه‌شب هم ثبت‌نامش را تمام می‌کند.' ),
	);
}
function uid_render_section_ekloss() {
	$tag   = uid_section_tag( 'ekloss', 'h2' );
	$proof = uid_section_val( 'ekloss', 'proof', uid_default_ek_proof() );
	if ( ! is_array( $proof ) ) $proof = array();
	?>
	<section class="dark sec" id="loss">
	  <div class="ek-wrap">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ekloss', 'eyebrow', __( 'قبل از اینکه ادامه بدهید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekloss', 'heading', __( 'ریزش در مرحله احراز هویت، گران‌ترین ریزش کل قیف شماست', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekloss', 'text', __( 'کاربری که تا صفحه احراز هویت آمده، تمام هزینه جذبش پرداخت شده است. اگر همان‌جا به‌خاطر فرمان‌های آزاردهنده Active Liveness رها کند، آن هزینه سوخته است. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="heroA-grid rv" style="align-items:start">
	      <div class="engine">
	        <div class="engine-hd"><span class="live"></span><b><?php esc_html_e( 'محاسبه‌گر ریزش احراز هویت', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'عددها را جابه‌جا کنید', 'uid-theme' ); ?></span></div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_tx"><?php esc_html_e( 'ثبت‌نام‌های ماهانه‌ای که به مرحله احراز هویت می‌رسند', 'uid-theme' ); ?></label>
	            <output id="ls_tx_v">۵٬۰۰۰</output></div>
	          <input type="range" id="ls_tx" min="200" max="60000" step="200" value="5000">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_rate"><?php esc_html_e( 'نرخ ریزش فعلی در همین مرحله', 'uid-theme' ); ?></label>
	            <output id="ls_rate_v">۲۲٫۰<small><?php esc_html_e( 'درصد', 'uid-theme' ); ?></small></output></div>
	          <input type="range" id="ls_rate" min="30" max="600" step="5" value="220">
	        </div>

	        <div class="eng-fld">
	          <div class="top"><label for="ls_cost"><?php esc_html_e( 'ارزش هر کاربر جذب‌شده برای کسب‌وکار شما', 'uid-theme' ); ?></label>
	            <output id="ls_cost_v">۳۵۰٬۰۰۰</output></div>
	          <input type="range" id="ls_cost" min="20000" max="4000000" step="10000" value="350000">
	        </div>

	        <div class="eng-out">
	          <div class="eng-cell bad"><span class="k"><?php esc_html_e( 'کاربرانی که ماهانه در همین مرحله می‌روند', 'uid-theme' ); ?></span>
	            <span class="v" id="ls_fail">۱٬۱۰۰</span></div>
	          <div class="eng-cell good"><span class="k"><?php esc_html_e( 'با Passive برمی‌گردند ', 'uid-theme' ); ?><small style="opacity:.75">(<?php esc_html_e( 'برآورد ۷۵٪', 'uid-theme' ); ?>)</small></span>
	            <span class="v" id="ls_save">۸۲۵</span></div>
	        </div>
	        <div class="eng-total">
	          <span class="k"><?php esc_html_e( 'ارزشی که ماهانه در همین یک مرحله از دست می‌رود', 'uid-theme' ); ?></span>
	          <span class="v amt"><span class="num" id="ls_total">۲۸۸٬۷۵۰٬۰۰۰</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span>
	        </div>
	        <div class="eng-daily"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'یعنی روزانه حدود ', 'uid-theme' ); ?><b id="ls_daily">۹٬۶۲۵٬۰۰۰</b><?php esc_html_e( ' تومان — تا لحظه‌ای که این سرویس فعال شود.', 'uid-theme' ); ?></span></div>

	        <div class="micro" id="lossMicro">
	          <div class="micro-fields">
	            <p><?php esc_html_e( 'می‌خواهید همین عدد را روی داده واقعی خودتان دقیق‌تر حساب کنیم؟ کارشناس یوآیدی رایگان بررسی می‌کند.', 'uid-theme' ); ?></p>
	            <form id="lossForm" novalidate>
	              <input type="hidden" name="source" value="ekyc-lp-dropoff">
	              <input type="hidden" name="volume" id="lossVolume" value="">
	              <div class="micro-row">
	                <div class="fld"><input name="phone" type="tel" inputmode="numeric"
	                  placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                  <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	                <button class="ek-btn btn-cta" type="button" data-submit><?php esc_html_e( 'بررسی رایگان قیف من', 'uid-theme' ); ?></button>
	              </div>
	            </form>
	          </div>
	          <div class="form-ok"><?php echo uid_ek_check_icon(); ?><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	            <span><?php esc_html_e( 'کارشناس یوآیدی تماس می‌گیرد. پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </div>
	      </div>

	      <div class="deck-wrap">
	        <div class="proof deck on-dark" data-deck="proof">
	          <?php foreach ( $proof as $p ) : ?>
	          <article class="pfcard">
	            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></div>
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
	          <div class="ic"><?php echo uid_ek_phone_icon(); ?></div>
	          <div class="tx"><b><?php echo esc_html( uid_section_val( 'ekloss', 'callband_heading', __( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'ekloss', 'callband_text', __( 'یک تماس کوتاه کافی است تا سناریوی ثبت‌نام شما بررسی و مسیر اتصال مشخص شود.', 'uid-theme' ) ) ); ?></p></div>
	          <div class="acts">
	            <a class="ek-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ek_phone_icon(); ?>
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
 * فصل ۱ — Passive در برابر Active
 * ===================================================================== */
function uid_default_ek_avp_old() {
	return array(
		'«سرت را به چپ بچرخان»|خطا',
		'«پلک بزن»|تکرار',
		'«لبخند بزن»|خطا',
		'«این متن را با صدای بلند بخوان»|انصراف',
	);
}
function uid_default_ek_avp_new() {
	return array(
		'تحلیل بافت پوست',
		'بررسی الگوهای نوری و بازتاب',
		'سنجش عمق تصویر',
		'تشخیص ریزحرکات طبیعی چهره',
	);
}
function uid_render_section_ekavp() {
	$tag  = uid_section_tag( 'ekavp', 'h2' );
	$old_lines = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ekavp', 'old_commands', implode( "\n", uid_default_ek_avp_old() ) ) ) ) );
	$new_lines = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'ekavp', 'new_commands', implode( "\n", uid_default_ek_avp_new() ) ) ) ) );
	?>
	<section class="sec" id="avp" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekavp', 'fold_title', __( 'Passive در برابر Active', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekavp', 'fold_teaser', __( 'چرا روش سنتی کاربر را فراری می‌دهد', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ekavp', 'eyebrow', __( 'مقایسه سناریوی کاربر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekavp', 'heading', __( 'دو تجربه، دو نتیجه کاملاً متفاوت', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekavp', 'text', __( 'در روش سنتی، کاربر باید به دستور سیستم سرش را بچرخاند، پلک بزند، لبخند بزند یا متنی را بخواند. در روش یوآیدی، فقط نگاه می‌کند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="avp rv">
	      <article class="avp-card old">
	        <div class="avp-hd">
	          <span class="tag"><?php echo uid_ek_x_icon(); ?><?php esc_html_e( 'روش سنتی و آزاردهنده — ', 'uid-theme' ); ?><span class="lat">Active Liveness</span></span>
	          <h3><?php echo esc_html( uid_section_val( 'ekavp', 'old_title', __( 'کاربر باید برای سیستم کار کند', 'uid-theme' ) ) ); ?></h3>
	          <p><?php echo esc_html( uid_section_val( 'ekavp', 'old_text', __( 'هر فرمان یک فرصت برای خطاست: اینترنت کند می‌شود، کاربر دستور را نمی‌فهمد، نور کافی نیست و دوباره از اول.', 'uid-theme' ) ) ); ?></p>
	        </div>
	        <div class="avp-reel">
	          <?php foreach ( $old_lines as $line ) : $parts = explode( '|', $line, 2 ); ?>
	          <div class="cmd"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 109-9"/><path d="M3 3v6h6"/></svg></span><span class="tx"><?php echo esc_html( $parts[0] ?? '' ); ?></span><span class="st err"><?php echo esc_html( $parts[1] ?? '' ); ?></span></div>
	          <?php endforeach; ?>
	        </div>
	        <div class="avp-ft"><span class="k"><?php echo esc_html( uid_section_val( 'ekavp', 'old_footer', __( 'پیامد: نرخ ریزش بسیار بالا و تجربه کاربری خسته‌کننده', 'uid-theme' ) ) ); ?></span>
	          <span class="v">↑</span></div>
	      </article>

	      <article class="avp-card new">
	        <div class="avp-hd">
	          <span class="tag"><?php echo uid_ek_check_icon(); ?><?php esc_html_e( 'روش نوین یوآیدی — ', 'uid-theme' ); ?><span class="lat">Passive Liveness</span></span>
	          <h3><?php echo esc_html( uid_section_val( 'ekavp', 'new_title', __( 'کاربر فقط ۵ ثانیه نگاه می‌کند', 'uid-theme' ) ) ); ?></h3>
	          <p><?php echo esc_html( uid_section_val( 'ekavp', 'new_text', __( 'هیچ اقدام اجباری و هیچ حرکت اضافه‌ای در کار نیست. الگوریتم کار سخت را پشت صحنه انجام می‌دهد.', 'uid-theme' ) ) ); ?></p>
	        </div>
	        <div class="avp-reel">
	          <?php foreach ( $new_lines as $line ) : ?>
	          <div class="cmd"><span class="ic"><?php echo uid_ek_check_icon(); ?></span><span class="tx"><?php echo esc_html( $line ); ?></span><span class="st ok"><?php esc_html_e( 'خودکار', 'uid-theme' ); ?></span></div>
	          <?php endforeach; ?>
	        </div>
	        <div class="avp-ft"><span class="k"><?php echo esc_html( uid_section_val( 'ekavp', 'new_footer', __( 'پیامد: حفظ نرخ تبدیل و تاییدیه آنی برای کاربر', 'uid-theme' ) ) ); ?></span>
	          <span class="v">↓</span></div>
	      </article>
	    </div>

	    <div class="secbox rv" style="margin-block-start:20px;background:var(--orange-l);border-color:rgba(248,148,40,.32)">
	      <div class="ic" style="color:var(--orange-d)"><?php echo uid_ek_warn_icon(); ?></div>
	      <div><b style="color:#8A5410"><?php echo esc_html( uid_section_val( 'ekavp', 'note_heading', __( 'تفاوت در جایی رخ می‌دهد که بیشترین هزینه را دارد', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#9A6A22"><?php echo esc_html( uid_section_val( 'ekavp', 'note_text', __( 'کاربری که تا مرحله احراز هویت آمده، تمام هزینه بازاریابی و جذبش پرداخت شده است. ریزش در این نقطه، گران‌ترین ریزش ممکن در کل قیف شماست.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۲ — تطبیق چهره با ثبت‌احوال
 * ===================================================================== */
function uid_render_section_ekface() {
	$tag = uid_section_tag( 'ekface', 'h2' );
	?>
	<section class="sec" id="face" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekface', 'fold_title', __( 'تطبیق چهره با ثبت‌احوال', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekface', 'fold_teaser', __( 'تفاوت Verification یک‌به‌یک با Recognition یک‌به‌چند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekface', 'eyebrow', __( 'فناوری تطبیق چهره', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekface', 'heading', __( '«این شخص، همان صاحب مدرک است»', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekface', 'text', __( 'دو مفهوم که مدام با هم اشتباه گرفته می‌شوند و کاربرد کاملاً متفاوتی دارند. آنچه در احراز هویت لازم دارید، اولی است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="mtch rv" style="margin-block-end:22px">
	      <article class="mcard hot">
	        <span class="badge"><?php echo esc_html( uid_section_val( 'ekface', 'card1_badge', __( 'Face Verification — ۱:۱', 'uid-theme' ) ) ); ?></span>
	        <b class="h"><?php echo esc_html( uid_section_val( 'ekface', 'card1_title', __( 'تطبیق یک به یک', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'ekface', 'card1_text', __( 'چهره حاضر در ویدیو با عکس متناظر همان کد ملی در پایگاه ثبت‌احوال مقایسه می‌شود تا تایید شود «این شخص همان صاحب مدرک است». پاسخ یک بله یا خیر قطعی است.', 'uid-theme' ) ) ); ?></p>
	        <span class="use"><?php echo uid_ek_check_icon(); ?><?php echo esc_html( uid_section_val( 'ekface', 'card1_use', __( 'همان چیزی که وب‌سرویس احراز هویت تصویری یوآیدی انجام می‌دهد', 'uid-theme' ) ) ); ?></span>
	      </article>
	      <article class="mcard">
	        <span class="badge"><?php echo esc_html( uid_section_val( 'ekface', 'card2_badge', __( 'Face Recognition — ۱:N', 'uid-theme' ) ) ); ?></span>
	        <b class="h"><?php echo esc_html( uid_section_val( 'ekface', 'card2_title', __( 'شناسایی یک به چند', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'ekface', 'card2_text', __( 'جستجوی یک چهره در میان انبوهی از تصاویر برای یافتن هویت. کاربردش شناسایی است، نه تایید هویت یک فرد مشخص در فرآیند ثبت‌نام.', 'uid-theme' ) ) ); ?></p>
	        <span class="use" style="color:var(--n400)"><?php echo uid_ek_x_icon(); ?><?php echo esc_html( uid_section_val( 'ekface', 'card2_use', __( 'برای احراز هویت کاربر در ثبت‌نام، ابزار درستی نیست', 'uid-theme' ) ) ); ?></span>
	      </article>
	    </div>

	    <div class="overlay-viz rv">
	      <div class="ovf">
	        <span class="lb"><?php esc_html_e( 'تصویر مرجع — پایگاه ثبت‌احوال', 'uid-theme' ); ?></span>
	        <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
	          <g class="outline"><path d="M60 12c-20 0-33 14-33 34 0 8 1 15 2 21 3 10 7 19 13 25 6 6 12 11 18 11s12-5 18-11c6-6 10-15 13-25 1-6 2-13 2-21 0-20-13-34-33-34z"/><path d="M44 56c3-3 9-3 12 0M64 56c3-3 9-3 12 0"/><path d="M50 96c6 5 14 5 20 0"/></g>
	          <g class="mesh"><path d="M27 48h66M24 68h72M30 88h60M60 16v96"/></g>
	          <g class="pt"><circle cx="50" cy="56" r="2.6"/><circle cx="70" cy="56" r="2.6"/><circle cx="60" cy="78" r="2.4"/></g>
	        </svg>
	      </div>
	      <div class="ovmid">
	        <span class="ring"><?php echo esc_html( uid_section_val( 'ekface', 'overlap_value', '۹۸٫۱۷٪' ) ); ?><small><?php esc_html_e( 'هم‌پوشانی', 'uid-theme' ); ?></small></span>
	        <span class="cap"><?php esc_html_e( 'استخراج ویژگی‌های بیومتریک', 'uid-theme' ); ?><br><?php esc_html_e( 'و مقایسه با آستانه استاندارد', 'uid-theme' ); ?></span>
	      </div>
	      <div class="ovf">
	        <span class="lb"><?php esc_html_e( 'فریم استخراج‌شده از ویدیوی سلفی', 'uid-theme' ); ?></span>
	        <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
	          <g class="outline"><path d="M60 12c-20 0-33 14-33 34 0 8 1 15 2 21 3 10 7 19 13 25 6 6 12 11 18 11s12-5 18-11c6-6 10-15 13-25 1-6 2-13 2-21 0-20-13-34-33-34z"/><path d="M44 56c3-3 9-3 12 0M64 56c3-3 9-3 12 0"/><path d="M50 96c6 5 14 5 20 0"/></g>
	          <g class="mesh"><path d="M27 48h66M24 68h72M30 88h60M60 16v96"/></g>
	          <g class="pt"><circle cx="50" cy="56" r="2.6"/><circle cx="70" cy="56" r="2.6"/><circle cx="60" cy="78" r="2.4"/></g>
	        </svg>
	      </div>
	    </div>

	    <div class="secbox rv" style="margin-block-start:20px;background:var(--teal-l);border-color:rgba(41,188,206,.3)">
	      <div class="ic" style="color:var(--teal-d)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7.5"/><path d="M21 21l-4.3-4.3"/></svg></div>
	      <div><b style="color:#0E5A66"><?php echo esc_html( uid_section_val( 'ekface', 'note_heading', __( 'تغییر ظاهر مانع تشخیص نمی‌شود', 'uid-theme' ) ) ); ?></b>
	        <p style="color:#12707E"><?php echo esc_html( uid_section_val( 'ekface', 'note_text', __( 'الگوریتم‌های تطبیق چهره بر اساس ساختار بیومتریک و فاصله‌های هندسی صورت آموزش دیده‌اند. عینک طبی، تغییر مدل مو، ریش یا گذشت زمان نسبت به عکس کارت ملی، مانع از تشخیص دقیق چهره نمی‌شود.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۳ — مراحل عملکرد وب‌سرویس (دک ۵ مرحله‌ای)
 * ===================================================================== */
function uid_default_ek_flow() {
	return array(
		array( 'icon' => 'doc', 'title' => '۱ — ارسال اطلاعات پایه', 'text' => 'کاربر کد ملی و تاریخ تولد خود را ثبت می‌کند و پذیرنده این اطلاعات را به‌همراه سریال کارت ملی و جنسیت به یوآیدی ارسال می‌کند.', 'tagline' => 'سمت پذیرنده' ),
		array( 'icon' => 'video', 'title' => '۲ — ضبط ویدیوی سلفی', 'text' => 'یک ویدیوی کوتاه ۵ ثانیه‌ای از طریق وب‌کم یا دوربین گوشی، بدون نیاز به نصب هیچ برنامه اضافه‌ای. فرمت‌های پذیرفته‌شده mp4 و webm است.', 'tagline' => 'سمت کاربر — ۵ ثانیه' ),
		array( 'icon' => 'shield', 'title' => '۳ — تحلیل Liveness و ضد جعل', 'text' => 'بررسی فاکتورهای عمق، نور و بافت چهره جهت تایید زنده بودن واقعی فرد و مسدود کردن تلاش‌های جعل با عکس، ماسک یا ویدیوی از پیش ضبط‌شده.', 'tagline' => 'موتور یوآیدی' ),
		array( 'icon' => 'face', 'title' => '۴ — تطبیق بیومتریک با ثبت‌احوال', 'text' => 'یک فریم باکیفیت به‌صورت سیستمی از ویدیو استخراج، ویژگی‌های بیومتریک آن محاسبه و با تصویر رسمی موجود در پایگاه ملی ثبت‌احوال مقایسه می‌شود.', 'tagline' => 'مرجع رسمی' ),
		array( 'icon' => 'send', 'title' => '۵ — ارسال نتیجه نهایی', 'text' => 'یوآیدی آدرس بازگشت اعلامی پذیرنده (callback_url) را فراخوانی می‌کند و درصد تطابق و پاسخ نهایی را در قالب داده ساختاریافته به سامانه شما می‌رساند.', 'tagline' => 'پاسخ کال‌بک' ),
	);
}
function uid_render_section_ekflow() {
	$tag   = uid_section_tag( 'ekflow', 'h2' );
	$items = uid_section_val( 'ekflow', 'items', uid_default_ek_flow() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="flow" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekflow', 'fold_title', __( 'مراحل عملکرد وب‌سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekflow', 'fold_teaser', __( 'از کد ملی تا پاسخ ساختاریافته، در پنج گام', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekflow', 'eyebrow', __( 'پشت صحنه سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekflow', 'heading', __( 'پنج مرحله، بدون هیچ دخالت انسانی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="deck-wrap rv">
	      <div class="deck d3" data-deck="flow">
	        <?php foreach ( $items as $i => $it ) :
	          $cls = 0 === $i ? 'navy' : ( 1 === $i ? 'warm' : ( 4 === $i ? 'navy' : '' ) );
	        ?>
	        <article class="dcard">
	          <div class="ic <?php echo esc_attr( $cls ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ek_flow_icon_svg( $it['icon'] ?? 'doc' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="tagline"><?php echo uid_ek_check_icon(); ?><?php echo esc_html( $it['tagline'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="flow">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><?php echo uid_ek_arrow_icon(); ?><?php esc_html_e( '۵ مرحله — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۴ — هوش مصنوعی در برابر انسان (جدول مقایسه)
 * ===================================================================== */
function uid_default_ek_vs_rows() {
	return array(
		array( 'label' => 'میزان خطا', 'b2' => 'تا ۱۴٪ خطای تشخیص', 'b3' => 'دقت ۹۸٫۱۷٪' ),
		array( 'label' => 'سرعت بررسی', 'b2' => 'چند ساعت تا چند روز کاری', 'b3' => 'کمتر از ۵ ثانیه' ),
		array( 'label' => 'مقیاس‌پذیری', 'b2' => 'استخدام و آموزش پرهزینه اپراتور', 'b3' => 'پردازش نامحدود هم‌زمان' ),
		array( 'label' => 'دسترس‌پذیری', 'b2' => 'وابسته به ساعات کاری پرسنل', 'b3' => '۲۴ ساعته' ),
		array( 'label' => 'ریسک تقلب', 'b2' => 'تقلب یا سهل‌انگاری اپراتور', 'b3' => 'الگوریتم خودکار' ),
	);
}
function uid_render_section_ekvs() {
	$tag  = uid_section_tag( 'ekvs', 'h2' );
	$rows = uid_section_val( 'ekvs', 'rows', uid_default_ek_vs_rows() );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec" id="vs" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekvs', 'fold_title', __( 'هوش مصنوعی در برابر نیروی انسانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekvs', 'fold_teaser', __( 'خطا، سرعت، مقیاس و ریسک تقلب، کنار هم', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ekvs', 'eyebrow', __( 'یک مقایسه بی‌تعارف', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekvs', 'heading', __( 'اپراتور انسانی، گران‌ترین و کندترین گزینه ممکن است', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekvs', 'text', __( 'بسیاری از کسب‌وکارها هنوز ویدیوها و مدارک را دستی بررسی می‌کنند. این جدول نشان می‌دهد چه چیزی را با چه چیزی عوض می‌کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="cs-tbl rv" style="max-width:900px;margin-inline:auto">
	      <div class="row hd"><span><?php esc_html_e( 'شاخص', 'uid-theme' ); ?></span><span class="b2"><?php echo esc_html( uid_section_val( 'ekvs', 'col2_label', __( 'بررسی توسط نیروی انسانی', 'uid-theme' ) ) ); ?></span><span class="b3"><?php echo esc_html( uid_section_val( 'ekvs', 'col3_label', __( 'هوش مصنوعی یوآیدی', 'uid-theme' ) ) ); ?></span></div>
	      <?php foreach ( $rows as $i => $r ) : ?>
	      <div class="row<?php echo ( $i === count( $rows ) - 1 ) ? ' tot' : ''; ?>"><span><?php echo esc_html( $r['label'] ?? '' ); ?></span><span class="b2"><?php echo esc_html( $r['b2'] ?? '' ); ?></span><span class="b3"><?php echo esc_html( $r['b3'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>

	    <div class="cs-big rv" style="max-width:900px;margin-inline:auto">
	      <span class="k"><?php echo esc_html( uid_section_val( 'ekvs', 'big_label', __( 'فاصله واقعی میان دو ستون', 'uid-theme' ) ) ); ?></span>
	      <span class="v"><?php echo esc_html( uid_section_val( 'ekvs', 'big_value1', __( 'چند روز کاری', 'uid-theme' ) ) ); ?> <span class="u"><?php esc_html_e( 'در برابر', 'uid-theme' ); ?></span> <?php echo esc_html( uid_section_val( 'ekvs', 'big_value2', __( '۵ ثانیه', 'uid-theme' ) ) ); ?></span>
	      <small><?php echo esc_html( uid_section_val( 'ekvs', 'big_note', __( 'و این فقط تفاوت سرعت است — تفاوت هزینه نیروی انسانی، آموزش، جایگزینی و ریسک تقلب به آن اضافه می‌شود.', 'uid-theme' ) ) ); ?></small>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۵ — کاربردها (انتخابگر صنعت)
 * ===================================================================== */
function uid_default_ek_who() {
	return array(
		array( 'icon' => 'bank', 'label' => 'بانکداری دیجیتال', 'title' => 'بانکداری دیجیتال و نئوبانک‌ها', 'text' => 'افتتاح حساب آنلاین و صدور کارت بدون نیاز به مراجعه به شعبه. کاربر کل فرآیند را از موبایل خودش تمام می‌کند و شعبه از مسیر حذف می‌شود.', 'win' => 'حذف مراجعه حضوری از مسیر افتتاح حساب' ),
		array( 'icon' => 'crypto', 'label' => 'صرافی رمزارز', 'title' => 'صرافی‌های رمزارز و پلتفرم‌های سرمایه‌گذاری', 'text' => 'اجرای سریع الزامات احراز هویت کاربر (KYC) برای واریز و برداشت مبالغ بالا — بدون آنکه کاربر در صف بررسی دستی مدارک معطل بماند.', 'win' => 'ارتقای سطح کاربر در لحظه، نه در چند روز' ),
		array( 'icon' => 'lend', 'label' => 'لندتک و وام آنلاین', 'title' => 'پلتفرم‌های لندتک و دریافت وام آنلاین', 'text' => 'اعتبارسنجی هویت متقاضیان تسهیلات و صدور سفته الکترونیک، با اطمینان از اینکه متقاضی واقعاً همان فردی است که مدارک به نامش صادر شده.', 'win' => 'کاهش ریسک تسهیلات با هویت جعلی' ),
		array( 'icon' => 'share', 'label' => 'اقتصاد مشارکتی', 'title' => 'اقتصاد مشارکتی و اشتراک خودرو یا اقامتگاه', 'text' => 'احراز هویت رانندگان، مسافران، میزبانان و تحویل‌دهندگان کالا — همان‌جایی که اعتماد میان دو غریبه، تمام مدل کسب‌وکار شماست.', 'win' => 'اعتماد دوطرفه، پیش از اولین تعامل' ),
		array( 'icon' => 'sign', 'label' => 'امضای دیجیتال', 'title' => 'سامانه‌های امضای دیجیتال و قراردادهای الکترونیک', 'text' => 'اطمینان از حضور واقعی امضاکننده سند در لحظه امضا — چیزی که یک کد پیامکی هرگز نمی‌تواند اثبات کند.', 'win' => 'سند با پشتوانه هویت زنده و تاییدشده' ),
	);
}
function uid_render_section_ekwho() {
	$tag   = uid_section_tag( 'ekwho', 'h2' );
	$items = uid_section_val( 'ekwho', 'items', uid_default_ek_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="who" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekwho', 'fold_title', __( 'کاربردهای وب‌سرویس', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekwho', 'fold_teaser', __( 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekwho', 'eyebrow', __( 'کجا به کار می‌آید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekwho', 'heading', __( 'هر جا که کاربر باید بدون مراجعه حضوری ثبت‌نام کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekwho', 'text', __( 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="rv">
	      <div class="pick-chips" data-pick-chips="ekind" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب صنعت', 'uid-theme' ); ?>"></div>
	      <div class="pick" data-pick="ekind">
	        <?php foreach ( $items as $i => $it ) : ?>
	        <article class="pick-card" data-label="<?php echo esc_attr( $it['label'] ?? '' ); ?>">
	          <div class="ic <?php echo 0 === $i % 2 ? 'navy' : ( 1 === $i % 3 ? 'warm' : '' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ek_who_icon_svg( $it['icon'] ?? 'bank' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="win"><?php echo uid_ek_check_icon(); ?><?php echo esc_html( $it['win'] ?? '' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="pick-hint"><?php echo uid_ek_arrow_icon(); ?><?php esc_html_e( 'بکشید یا از چیپ‌های بالا انتخاب کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۶ — مقابله با جعل هویت (دک ۴ حمله)
 * ===================================================================== */
function uid_default_ek_spoof() {
	return array(
		array( 'icon' => 'print', 'title' => 'عکس چاپ‌شده', 'text' => 'یک تصویر کاغذی مقابل دوربین گرفته می‌شود. بافت کاغذ، نبود عمق و بازتاب یکنواخت نور بلافاصله آن را لو می‌دهد.' ),
		array( 'icon' => 'screen', 'title' => 'نمایش روی مانیتور یا گوشی', 'text' => 'پخش تصویر یا ویدیو روی صفحه نمایش، الگوی نوری و نرخ نوسان مخصوص خودش را دارد که با نور طبیعی چهره یکسان نیست.' ),
		array( 'icon' => 'mask', 'title' => 'ماسک سیلیکونی و ماکت چهره', 'text' => 'پیشرفته‌ترین شکل حمله. تحلیل بافت پوست و ریزحرکات طبیعی عضلات صورت، تفاوت پوست زنده با سطح مصنوعی را تشخیص می‌دهد.' ),
		array( 'icon' => 'video', 'title' => 'ویدیوی از پیش ضبط‌شده', 'text' => 'ویدیویی که قبلاً از فرد گرفته شده و دوباره پخش می‌شود، ویژگی‌های زنده‌بودن لحظه‌ای را ندارد و با کد VIDEO_NOT_LIVE رد می‌شود.' ),
	);
}
function uid_render_section_ekspoof() {
	$tag   = uid_section_tag( 'ekspoof', 'h2' );
	$items = uid_section_val( 'ekspoof', 'items', uid_default_ek_spoof() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="spoof" data-fold data-fold-hot
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekspoof', 'fold_title', __( 'مقابله با جعل هویت', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekspoof', 'fold_teaser', __( 'عکس چاپی، ماسک، مانیتور و ویدیوی ضبط‌شده', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ekspoof', 'eyebrow', __( 'موتور ضدجعل — Anti-Spoofing', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekspoof', 'heading', __( 'چهار حمله‌ای که سیستم‌های ساده را رد می‌کنند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekspoof', 'text', __( 'هر سامانه‌ای که فقط «یک صورت» را در تصویر می‌بیند، با یک عکس چاپی هم فریب می‌خورد. موتور ضدجعل یوآیدی به بافت پوست، بازتاب نور، عمق میدان و ریزحرکات طبیعی چهره نگاه می‌کند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="deck-wrap rv">
	      <div class="spoof deck" data-deck="spoof">
	        <?php foreach ( $items as $it ) : ?>
	        <article class="spcard">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ek_spoof_icon_svg( $it['icon'] ?? 'print' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	          <span class="blocked"><?php echo uid_ek_check_icon(); ?><?php esc_html_e( 'مسدود می‌شود', 'uid-theme' ); ?></span>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="spoof">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><?php echo uid_ek_arrow_icon(); ?><?php esc_html_e( '۴ نوع حمله — بکشید یا از دکمه‌ها استفاده کنید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۷ — تیم متخصص و پشتیبانی
 * ===================================================================== */
function uid_default_ek_team_rows() {
	return array(
		array( 'icon' => 'expert', 'title' => 'کارشناس فنی اختصاصی برای دوره اتصال', 'text' => 'از تحویل کلید API و دسترسی سندباکس تا اولین احراز هویت موفق روی محیط عملیاتی، یک نفر مشخص پاسخگوی تیم شماست.' ),
		array( 'icon' => 'chart', 'title' => 'مانیتورینگ عملکرد سرویس', 'text' => 'نرخ تایید، نرخ رد و علت ردها زیر نظر گرفته می‌شود. اگر الگوی غیرعادی در ردشدن کاربران شما دیده شود، پیش از آنکه به تیکت تبدیل شود بررسی می‌شود.' ),
		array( 'icon' => 'docs', 'title' => 'مستندات کامل REST و gRPC', 'text' => 'نمونه درخواست، نمونه پاسخ، ساختار کال‌بک و جدول کامل کدهای وضعیت، همگی مستند و در دسترس تیم توسعه شما.' ),
		array( 'icon' => 'shield', 'title' => 'سابقه از سال ۱۳۹۶ در احراز هویت آنلاین', 'text' => 'شرکت دانش‌بنیان بینش هوشمند نسل پیشرو، کارگزار مورد تایید سامانه‌های سجام و ثنا، با تمرکز اختصاصی روی زیرساخت هویت دیجیتال.' ),
	);
}
function uid_default_ek_pledge_items() {
	return array(
		'دسترسی به محیط سندباکس پیش از هر تعهد مالی',
		'هزینه فقط بابت احراز هویت‌های تاییدشده محاسبه می‌شود',
		'تعرفه پلکانی و نرخ ترجیحی برای کسب‌وکارهای پرتراکنش',
		'راهنمایی در طراحی تجربه ثبت‌نام، نه فقط تحویل نقطه پایانی',
	);
}
function uid_render_section_ekteam() {
	$tag  = uid_section_tag( 'ekteam', 'h2' );
	$rows = uid_section_val( 'ekteam', 'rows', uid_default_ek_team_rows() );
	$pledge_raw = uid_section_val( 'ekteam', 'pledge_items', implode( "\n", uid_default_ek_pledge_items() ) );
	$pledge = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec" id="team" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekteam', 'fold_title', __( 'تیم متخصص و پشتیبانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekteam', 'fold_teaser', __( 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekteam', 'eyebrow', __( 'پشت این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekteam', 'heading', __( 'یک API نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekteam', 'text', __( 'احراز هویت تصویری حساس‌ترین نقطه تماس شما با کاربر است. به همین دلیل تیم فنی یوآیدی از اولین تماس تا مانیتورینگ عملکرد سرویس همراه شماست.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="team rv">
	      <div class="team-list">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ek_team_icon_svg( $r['icon'] ?? 'expert' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>

	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><?php echo uid_ek_check_icon(); ?></span>
	          <b><?php echo esc_html( uid_section_val( 'ekteam', 'pledge_heading', __( 'تعهد یوآیدی به تیم فنی شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_ek_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <div class="btn-row" style="margin-block-start:18px">
	          <a class="ek-btn ek-btn-navy" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ek_phone_icon(); ?>
	            <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۸ — بخش فنی و نمونه‌کد (متن داینامیک، نمونه‌کدها و جدول وضعیت هاردکد
 *          — مطابق سابقه تب فنی PWA)
 * ===================================================================== */
function uid_default_ek_input_fields() {
	return array(
		array( 'field' => 'nationalId', 'desc' => 'کد ملی کاربر' ),
		array( 'field' => 'nationalIdSerial', 'desc' => 'سریال پشت کارت ملی' ),
		array( 'field' => 'birthDate', 'desc' => 'تاریخ تولد yyyy/mm/dd' ),
		array( 'field' => 'gender', 'desc' => 'SEX_MALE یا SEX_FEMALE' ),
	);
}
function uid_render_section_ekdev() {
	$tag        = uid_section_tag( 'ekdev', 'h2' );
	$in_fields  = uid_section_val( 'ekdev', 'input_fields', uid_default_ek_input_fields() );
	?>
	<section class="sec" id="dev" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekdev', 'fold_title', __( 'بخش فنی و نمونه کد', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekdev', 'fold_teaser', __( 'درخواست، پاسخ، کال‌بک و جدول کدهای وضعیت', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekdev', 'eyebrow', __( 'برای تیم توسعه شما', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekdev', 'heading', __( 'دو مرحله، دو فراخوانی، تمام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekdev', 'text', __( 'در مرحله اول اطلاعات دریافتی از کاربر توسط پذیرنده به یوآیدی ارسال می‌شود. در ادامه و پس از احراز هویت کاربر، یوآیدی آدرس بازگشت اعلامی پذیرنده (callback_url) را با پارامترهای مربوطه فراخوانی کرده و نتیجه را به اطلاع پذیرنده می‌رساند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="codepanel rv">
	      <div>
	        <div class="ftags" style="margin-block-end:18px">
	          <?php foreach ( (array) $in_fields as $f ) : ?>
	          <span class="ftag"><span class="key"><?php echo esc_html( $f['field'] ?? '' ); ?></span><?php echo esc_html( $f['desc'] ?? '' ); ?></span>
	          <?php endforeach; ?>
	          <span class="ftag warm"><span class="key">downloadLink</span><?php esc_html_e( 'لینک ویدیوی سلفی کاربر', 'uid-theme' ); ?></span>
	        </div>
	        <div class="secbox" style="background:var(--leak-l);border-color:rgba(214,69,80,.25)">
	          <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 9v4M12 16h.01"/></svg></div>
	          <div><b><?php echo esc_html( uid_section_val( 'ekdev', 'security_heading', __( 'نکته امنیتی درباره لینک ویدیو', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'ekdev', 'security_text', __( 'ویدیوی سلفی احراز هویت کاربر توسط پذیرنده دریافت و لینک آن جهت احراز هویت به یوآیدی ارسال می‌شود. رعایت جوانب امنیتی در ایجاد این لینک بسیار حائز اهمیت است؛ توصیه می‌شود مدت زمان اعتبار لینک نیز محدود گردد. فرمت‌های پذیرفته‌شده mp4 و webm است.', 'uid-theme' ) ) ); ?></p></div>
	        </div>
	        <div class="btn-row" style="margin-block-start:20px">
	          <button class="ek-btn btn-cta" data-open-modal><?php echo uid_ek_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ekdev', 'btn1_text', __( 'دریافت کلید وب‌سرویس', 'uid-theme' ) ) ); ?></button>
	          <a class="ek-btn btn-ghost" href="<?php echo esc_url( uid_section_val( 'ekdev', 'btn2_url', '/api-ekyc-docs/' ) ); ?>"><?php echo esc_html( uid_section_val( 'ekdev', 'btn2_text', __( 'مشاهده مستندات کامل API', 'uid-theme' ) ) ); ?></a>
	        </div>
	      </div>

	      <div class="code-box">
	        <div class="code-tabs">
	          <button class="on" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button data-code-tab="cb"><?php esc_html_e( 'فراخوانی کال‌بک', 'uid-theme' ); ?></button>
	          <button class="code-copy" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req"><span class="m">POST</span> https://internal-auth-api.uid.ir/v2/authenticate-user-info
<span class="k">Content-Type</span>: application/json;charset=UTF-8

{
  <span class="k">"businessId"</span>: <span class="s">"&lt;business_id&gt;"</span>,
  <span class="k">"businessToken"</span>: <span class="s">"&lt;business_token&gt;"</span>,
  <span class="k">"nationalId"</span>: <span class="s">"&lt;national_id&gt;"</span>,
  <span class="k">"nationalIdSerial"</span>: <span class="s">"&lt;NATIONAL_ID_SERIAL&gt;"</span>,
  <span class="k">"gender"</span>: <span class="s">"SEX_MALE"</span>,
  <span class="k">"birthDate"</span>: <span class="s">"&lt;yyyy/mm/dd&gt;"</span>,
  <span class="k">"downloadLink"</span>: <span class="s">"&lt;VIDEO_DOWNLOAD_LINK&gt;"</span>
}</pre>
<pre class="code-pane" data-code-pane="res">{
  <span class="k">"sessionId"</span>: <span class="s">"USER_SESSION_ID"</span>,
  <span class="k">"error"</span>: <span class="s">"ERROR"</span>
}

<span class="m">//</span> sessionId یک مقدار یونیک است که یوآیدی
<span class="m">//</span> به هر کاربر اختصاص می‌دهد.</pre>
<pre class="code-pane" data-code-pane="cb"><span class="m">POST</span> BUSINESS_CALLBACK_URL
<span class="k">Content-Type</span>: application/json;charset=UTF-8

{
  <span class="k">"sessionId"</span>: <span class="s">"USER_SESSION_ID"</span>,
  <span class="k">"status"</span>: { <span class="k">"code"</span>: <span class="s">"CODE"</span>, <span class="k">"message"</span>: <span class="s">"MESSAGE"</span> },
  <span class="k">"userInformation"</span>: {
    <span class="k">"nationalId"</span>: <span class="s">"NATIONAL_ID"</span>,
    <span class="k">"date"</span>: <span class="s">"DATE"</span>,
    <span class="k">"sex"</span>: <span class="s">"SEX"</span>
  },
  <span class="k">"authenticationStatus"</span>: {
    <span class="k">"state"</span>: <span class="s">"AUTHENTICATION_STATE_ACCEPTED"</span>,
    <span class="k">"reason"</span>: <span class="s">"AUTHENTICATION_RESPONSE_REASON_ACCEPT"</span>
  }
}</pre>
	        </div>
	      </div>
	    </div>

	    <div class="rv" style="margin-block-start:clamp(28px,4vw,44px)">
	      <h3 class="h-sub" style="margin-block-end:14px"><?php esc_html_e( 'جدول وضعیت‌ها (', 'uid-theme' ); ?><span class="lat">reason</span>)</h3>
	      <p class="small" style="margin-block-end:16px;max-width:70ch"><?php esc_html_e( 'هر پاسخ کال‌بک یک وضعیت (state) و یک علت (reason) دارد. با همین دو مقدار، سامانه شما می‌تواند دقیقاً به کاربر بگوید چه اتفاقی افتاده و چه کاری باید انجام دهد.', 'uid-theme' ); ?></p>
	      <div class="enum-tbl">
	        <div class="r hd"><span><?php esc_html_e( 'مقدار reason', 'uid-theme' ); ?></span><span><?php esc_html_e( 'نتیجه', 'uid-theme' ); ?></span><span><?php esc_html_e( 'توضیح', 'uid-theme' ); ?></span></div>
	        <div class="r und"><span class="c1">AUTHENTICATION_RESPONSE_REASON_UNDEFINED</span><span class="c2">UNDEFINED</span><span class="c3"><?php esc_html_e( 'در حال انتظار', 'uid-theme' ); ?></span></div>
	        <div class="r acc"><span class="c1">AUTHENTICATION_RESPONSE_REASON_ACCEPT</span><span class="c2">ACCEPTED</span><span class="c3"><?php esc_html_e( 'تایید شده', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_FACE_NOT_MATCH_ID</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'عدم تطبیق چهره با ثبت‌احوال', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_VIDEO_NOT_LIVE</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'زنده نبودن ویدیوی ارسالی', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_VIDEO_BAD_LIGHT</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'نور نامناسب', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_VIDEO_BAD_QUALITY</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'عدم وضوح تصویر', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_PERSON_TOO_FAR_AWAY</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'فاصله بیش از حد با دوربین', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_MORE_THAN_ONE_PERSON</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'وجود چند چهره در تصویر', 'uid-theme' ); ?></span></div>
	        <div class="r rej"><span class="c1">..._REJECT_NO_PERSON_DETECTED</span><span class="c2">REJECTED</span><span class="c3"><?php esc_html_e( 'عدم وجود چهره در تصویر', 'uid-theme' ); ?></span></div>
	      </div>
	      <div class="price-note" style="margin-block-start:16px">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	        <span><?php esc_html_e( 'مقادیر state عبارت‌اند از AUTHENTICATION_STATE_UNDEFINED (در انتظار)، AUTHENTICATION_STATE_ACCEPTED (تایید شده) و AUTHENTICATION_STATE_REJECTED (رد شده). امکان استفاده از این سرویس بر بستر gRPC نیز فراهم است.', 'uid-theme' ); ?></span>
	      </div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۹ — سرویس‌های مکمل و مرتبط
 * ===================================================================== */
function uid_default_ek_xsell() {
	return array(
		array( 'icon' => 'pwa', 'title' => 'یوآیدی‌پلاس (PWA)', 'text' => 'برون‌سپاری کامل فرآیند احراز هویت. کاربر به یک صفحه آماده هدایت می‌شود و شما فقط نتیجه نهایی را تحویل می‌گیرید — بدون نیاز به پیاده‌سازی رابط ضبط ویدیو.', 'url' => '/uid-plus/' ),
		array( 'icon' => 'doc', 'title' => 'وب‌سرویس ثبت‌احوال', 'text' => 'استعلام اطلاعات هویتی با کد ملی و تاریخ تولد. مکمل طبیعی احراز هویت تصویری، وقتی پیش از ضبط ویدیو می‌خواهید صحت اطلاعات پایه را تایید کنید.', 'url' => '/api-inquiry-person/' ),
		array( 'icon' => 'crypto', 'title' => 'احراز هویت صرافی ارز دیجیتال', 'text' => 'بسته آماده مخصوص پلتفرم‌های رمزارز، با در نظر گرفتن الزامات KYC این صنعت و سطح‌بندی کاربران.', 'url' => '/authentication-digital-currency-exchange/' ),
	);
}
function uid_render_section_ekxsell() {
	$tag   = uid_section_tag( 'ekxsell', 'h2' );
	$items = uid_section_val( 'ekxsell', 'items', uid_default_ek_xsell() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="xsell" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekxsell', 'fold_title', __( 'سرویس‌های مکمل و مرتبط', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekxsell', 'fold_teaser', __( 'یوآیدی‌پلاس، یوآیدی پرو و وب‌سرویس ثبت‌احوال', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekxsell', 'eyebrow', __( 'در کنار این وب‌سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekxsell', 'heading', __( 'اگر نمی‌خواهید سمت خودتان چیزی پیاده‌سازی کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekxsell', 'text', __( 'وب‌سرویس API برای تیم‌هایی است که می‌خواهند تجربه ثبت‌نام کاملاً در کنترل خودشان بماند. اگر ترجیح می‌دهید کل فرآیند برون‌سپاری شود، مسیرهای دیگری هم هست.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="xsell rv">
	      <?php foreach ( $items as $it ) : ?>
	      <a class="xtile" href="<?php echo esc_url( $it['url'] ?? '#' ); ?>">
	        <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ek_xsell_icon_svg( $it['icon'] ?? 'pwa' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="go"><?php esc_html_e( 'مشاهده سرویس', 'uid-theme' ); ?> <?php echo uid_ek_arrow_icon(); ?></span>
	      </a>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱۰ — سوالات متداول
 * ===================================================================== */
function uid_default_ek_faq() {
	return array(
		array( 'question' => 'فناوری تشخیص زنده بودن غیرفعال (Passive Liveness) چه تفاوتی با روش فعال (Active) دارد؟', 'answer' => 'در روش سنتی یا فعال، کاربر باید دستورهایی مثل چرخاندن سر، پلک زدن یا خواندن متن را انجام دهد که باعث خستگی و خطای بالا می‌شود. در روش غیرفعال، کاربر تنها چند ثانیه روبه‌روی دوربین قرار می‌گیرد و الگوریتم‌های هوش مصنوعی بدون نیاز به هیچ حرکت اضافه‌ای، زنده بودن و ضدجعل بودن تصویر را تایید می‌کنند.' ),
		array( 'question' => 'دقت تطبیق چهره و عملکرد سیستم در برابر تغییرات ظاهری (مثل ریش، عینک یا پیری) چگونه است؟', 'answer' => 'الگوریتم‌های تطبیق چهره بر اساس ساختار بیومتریک و فاصله‌های هندسی صورت آموزش دیده‌اند و دقت ۹۸٫۱۷ درصدی دارند. تغییراتی مثل استفاده از عینک طبی، تغییر مدل مو، ریش یا گذشت زمان نسبت به عکس کارت ملی، مانع از تشخیص دقیق چهره نمی‌شود.' ),
		array( 'question' => 'سیستم چگونه با تلاش‌های جعل هویت (حملات Spoofing) مقابله می‌کند؟', 'answer' => 'موتور ضدجعل یوآیدی با تحلیل بافت پوست، بازتاب نور، عمق میدان و ریزحرکات طبیعی چهره، مواردی مثل استفاده از عکس چاپی، ماسک‌های سیلیکونی، ویدیوی از پیش ضبط‌شده یا تصاویر روی مانیتور و گوشی را شناسایی و مسدود می‌کند.' ),
		array( 'question' => 'مدل محاسبه هزینه و تعرفه استعلام‌ها به چه صورت است؟', 'answer' => 'هزینه خدمات تنها به ازای استعلام‌های موفق محاسبه می‌شود و برای سازمان‌ها و پلتفرم‌های با تراکنش بالا، بسته‌های تعرفه پلکانی همراه با تخفیف‌های حجمی در نظر گرفته شده است.' ),
		array( 'question' => 'کاربر برای احراز هویت باید اپلیکیشن نصب کند؟', 'answer' => 'خیر. ویدیوی سلفی ۵ ثانیه‌ای از طریق وب‌کم یا دوربین گوشی و بدون نیاز به نصب برنامه اضافه ضبط می‌شود. اگر ترجیح می‌دهید حتی رابط ضبط ویدیو را هم پیاده‌سازی نکنید، سرویس یوآیدی‌پلاس (PWA) کل این مرحله را برون‌سپاری می‌کند.' ),
		array( 'question' => 'نتیجه احراز هویت چطور به سامانه ما می‌رسد؟', 'answer' => 'فرآیند دو مرحله‌ای است. ابتدا شما اطلاعات کاربر و لینک ویدیو را به سرویس authenticate-user-info ارسال می‌کنید و یک sessionId دریافت می‌کنید. پس از پایان فرآیند، یوآیدی آدرس کال‌بک اعلامی شما را با متد POST فراخوانی می‌کند و وضعیت (state) و علت (reason) نهایی را برمی‌گرداند.' ),
		array( 'question' => 'اگر کاربری به‌اشتباه رد شود چه اتفاقی می‌افتد؟', 'answer' => 'نرخ رد نادرست (FRR) این سرویس ۰٫۴ درصد است. در همان پاسخ کال‌بک، علت دقیق رد شدن با یک reason مشخص برمی‌گردد — مثلاً نور نامناسب، فاصله زیاد با دوربین یا وجود چند چهره در تصویر — و سامانه شما می‌تواند دقیقاً همان نکته را به کاربر بگوید تا در تلاش بعدی موفق شود.' ),
		array( 'question' => 'راه‌اندازی سرویس چقدر طول می‌کشد؟', 'answer' => 'پس از تماس اولیه و بررسی سناریوی کسب‌وکار شما، دسترسی به محیط سندباکس و کلید API در اختیار تیم فنی شما قرار می‌گیرد تا پیش از هر تعهد مالی، یکپارچه‌سازی را آزمایش کنید. برای شروع کافی است با شماره ۰۲۱۶۶۱۲۳۲۹۰ تماس بگیرید.' ),
	);
}
function uid_render_section_ekfaq() {
	$tag   = uid_section_tag( 'ekfaq', 'h2' );
	$items = uid_section_val( 'ekfaq', 'items', uid_default_ek_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" data-fold
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'ekfaq', 'fold_title', __( 'سوالات متداول', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'ekfaq', 'fold_teaser', __( 'دقت، ضدجعل، تعرفه و تفاوت روش‌ها', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="ek-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ekfaq', 'eyebrow', __( 'پیش از تماس، این‌ها را بخوانید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekfaq', 'heading', __( 'سوالات متداول وب‌سرویس احراز هویت تصویری', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv" style="max-width:900px;margin-inline:auto">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i"><button class="faq-q" aria-expanded="false"><?php echo esc_html( $it['question'] ?? '' ); ?><span class="pm"></span></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * شاخص‌های عملکرد (پنل تیره خارج از فصل‌ها)
 * ===================================================================== */
function uid_default_ek_adv() {
	return array(
		array( 'featured' => '1', 'wide' => '', 'value' => '۹۸٫۱۷٪', 'is_text' => '', 'label' => 'دقت تطبیق چهره با تصویر رسمی پایگاه ثبت‌احوال کشور', 'meter' => '98' ),
		array( 'featured' => '', 'wide' => '', 'value' => '۰٫۴٪', 'is_text' => '', 'label' => 'نرخ رد نادرست (FRR) — کاربر واقعی کمتر رد می‌شود', 'meter' => '12' ),
		array( 'featured' => '', 'wide' => '', 'value' => '۰٫۸۵٪', 'is_text' => '', 'label' => 'نرخ پذیرش نادرست (FAR) — هویت جعلی عبور نمی‌کند', 'meter' => '9' ),
		array( 'featured' => '', 'wide' => '1', 'value' => 'کمتر از ۵ ثانیه', 'is_text' => '1', 'label' => 'زمان پردازش و پاسخ‌دهی، ۲۴ ساعته و بدون سقف درخواست هم‌زمان', 'meter' => '94' ),
		array( 'featured' => '', 'wide' => '1', 'value' => 'ثبت‌احوال کشور', 'is_text' => '1', 'label' => 'انطباق قانونی — مرجع تطبیق، پایگاه داده سازمان ثبت‌احوال است', 'meter' => '100' ),
	);
}
function uid_render_section_ekadv() {
	$tag   = uid_section_tag( 'ekadv', 'h2' );
	$items = uid_section_val( 'ekadv', 'items', uid_default_ek_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="dark sec" id="adv">
	  <div class="ek-wrap">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ekadv', 'eyebrow', __( 'مزایای رقابتی وب‌سرویس یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekadv', 'heading', __( 'چهار عددی که تصمیم را می‌سازند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekadv', 'text', __( 'این‌ها شاخص‌های منتشرشده عملکرد موتور احراز هویت تصویری یوآیدی هستند — همان اعدادی که در ارزیابی فنی از شما پرسیده می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="opanel rv">
	      <?php foreach ( $items as $it ) :
	        $cls = trim( ( ! empty( $it['featured'] ) ? ' feat' : '' ) . ( ! empty( $it['wide'] ) ? ' w2' : '' ) );
	      ?>
	      <div class="ostat<?php echo $cls ? ' ' . esc_attr( $cls ) : ''; ?>">
	        <div class="oic"><?php echo uid_ek_check_icon(); ?></div>
	        <span class="ov<?php echo ! empty( $it['is_text'] ) ? ' txt' : ''; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	        <span class="ol"><?php echo esc_html( $it['label'] ?? '' ); ?></span>
	        <span class="ometer" style="--p:<?php echo esc_attr( absint( $it['meter'] ?? 0 ) ); ?>%"><i></i></span>
	      </div>
	      <?php endforeach; ?>
	    </div>

	    <div class="opanel-foot rv">
	      <span><?php echo esc_html( uid_section_val( 'ekadv', 'foot_text', __( 'می‌خواهید این اعداد را روی داده واقعی کاربران خودتان ببینید؟', 'uid-theme' ) ) ); ?></span>
	      <button class="ek-btn btn-white ek-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'ekadv', 'btn_text', __( 'درخواست دسترسی سندباکس', 'uid-theme' ) ) ); ?></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * محاسبه‌گر تعرفه (اعداد پایه/پله‌ها در JS نمایشی است — ⚠ برای انتشار
 * نهایی با تعرفه واقعی جایگزین شود؛ نگاه کنید به assets/js/ekyc-liveness-page.js)
 * ===================================================================== */
function uid_render_section_ekprice() {
	$tag = uid_section_tag( 'ekprice', 'h2' );
	?>
	<section class="sec" id="price">
	  <div class="ek-wrap">
	    <div class="sec-head mid rv">
	      <span class="ek-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'ekprice', 'eyebrow', __( 'مدل محاسبه هزینه', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'ekprice', 'heading', __( 'فقط بابت احراز هویت‌های تاییدشده هزینه می‌دهید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'ekprice', 'text', __( 'هیچ حق اشتراک ثابتی بابت تلاش‌های ناموفق پرداخت نمی‌کنید. تعداد ثبت‌نام تخمینی ماهانه خود را جابه‌جا کنید تا پلن متناسب با کسب‌وکارتان را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="calc rv">
	      <div class="calc-in">
	        <span class="lb"><?php esc_html_e( 'تعداد احراز هویت موفق در ماه', 'uid-theme' ); ?></span>
	        <div class="calc-num"><span class="v" id="cl_qty">۱۰٬۰۰۰</span><span class="u"><?php esc_html_e( 'احراز هویت', 'uid-theme' ); ?></span></div>
	        <div class="calc-slider">
	          <input type="range" id="cl_range" min="0" max="1000" value="430" aria-label="<?php esc_attr_e( 'تعداد احراز هویت ماهانه', 'uid-theme' ); ?>">
	          <div class="calc-ends"><span>۵۰۰</span><span>+۵۰۰٬۰۰۰</span></div>
	        </div>
	        <div class="calc-presets" id="cl_presets">
	          <button class="calc-preset" type="button" data-qty="2000">۲٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="10000">۱۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="50000">۵۰٬۰۰۰</button>
	          <button class="calc-preset" type="button" data-qty="200000">۲۰۰٬۰۰۰</button>
	        </div>
	        <div class="calc-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="9.5"/></svg>
	          <span><?php esc_html_e( 'ارقام این محاسبه‌گر نمایشی و صرفاً برای درک ساختار تعرفه پلکانی است. نرخ نهایی پس از بررسی حجم و سناریوی کسب‌وکار شما توسط کارشناس یوآیدی اعلام می‌شود.', 'uid-theme' ); ?></span></div>
	      </div>
	      <div class="calc-out">
	        <div class="calc-plan"><span class="tag" id="cl_plan"><?php esc_html_e( 'پلن رشد', 'uid-theme' ); ?></span><span class="tag off" id="cl_off">۱۰٪ <?php esc_html_e( 'تخفیف پلکانی', 'uid-theme' ); ?></span></div>
	        <div class="calc-rows">
	          <div class="calc-row"><span><?php esc_html_e( 'احراز هویت موفق ماهانه', 'uid-theme' ); ?></span><b id="cl_n">۱۰٬۰۰۰</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'نرخ هر احراز هویت در این پله', 'uid-theme' ); ?></span><b id="cl_unit">—</b></div>
	          <div class="calc-row"><span><?php esc_html_e( 'هزینه بدون تخفیف پلکانی', 'uid-theme' ); ?></span><b id="cl_gross">—</b></div>
	          <div class="calc-row total"><span><?php esc_html_e( 'برآورد هزینه ماهانه', 'uid-theme' ); ?></span><b id="cl_total">—</b></div>
	        </div>
	        <div class="calc-free"><?php echo uid_ek_check_icon(); ?>
	          <span><?php esc_html_e( 'احراز هویت‌های ناموفق و رد شده در این محاسبه لحاظ نمی‌شوند.', 'uid-theme' ); ?></span></div>
	        <div class="calc-cta">
	          <button class="ek-btn btn-cta ek-btn-block" data-open-modal><?php echo uid_ek_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'ekprice', 'btn_text', __( 'دریافت تعرفه دقیق برای این حجم', 'uid-theme' ) ) ); ?></button>
	          <p class="calc-dis"><?php echo esc_html( uid_section_val( 'ekprice', 'btn_note', __( 'فرم را پر کنید تا کارشناس یوآیدی نرخ واقعی این پله را همراه با شرایط قرارداد برای شما ارسال کند.', 'uid-theme' ) ) ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بند میانی تماس تلفنی
 * ===================================================================== */
function uid_render_section_ekcallband() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="ek-wrap">
	    <div class="callband rv">
	      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5l3 2"/><circle cx="12" cy="12" r="9.5"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'ekcallband', 'heading', __( 'هر روزی که این سرویس فعال نیست، ریزشش را پرداخت می‌کنید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'ekcallband', 'text', __( 'کاربرانی که امروز در مرحله احراز هویت رها می‌کنند، فردا برنمی‌گردند. یک تماس کوتاه کافی است تا کارشناس یوآیدی سناریوی ثبت‌نام شما را بررسی و دسترسی سندباکس را فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="ek-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_ek_phone_icon(); ?>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="ek-btn btn-ghost" data-open-modal><?php echo esc_html( uid_section_val( 'ekcallband', 'btn_text', __( 'فرم درخواست سرویس', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_eklead() {
	$tag = uid_section_tag( 'eklead', 'h2' );
	$trust_raw = uid_section_val( 'eklead', 'trust', "مشاوره رایگان، بدون تعهد\nدسترسی به سندباکس پیش از قرارداد\nهزینه فقط بابت احراز موفق\nتعرفه پلکانی متناسب با حجم شما" );
	$trust = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'eklead', 'biztype_options', "crypto|صرافی ارز دیجیتال یا بروکر\nbank|بانک، نئوبانک یا فین‌تک\nlend|لندتک و وام آنلاین\nshare|اقتصاد مشارکتی و پلتفرم خدماتی\nsign|امضای دیجیتال و قرارداد الکترونیک\nins|بیمه و خدمات مالی\nother|سایر کسب‌وکارها\nind|کاربر شخصی هستم (کسب‌وکار نیستم)" );
	$biztype_lines = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="ek-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="ek-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'eklead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'eklead', 'heading', __( 'فرم درخواست فعال‌سازی وب‌سرویس احراز هویت تصویری', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'eklead', 'text', __( 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس احراز هویت تصویری (Passive Liveness و تطبیق چهره)، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_ek_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'eklead', 'form_title', __( 'درخواست وب‌سرویس احراز هویت تصویری', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'eklead', 'form_hint', __( 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="ekyc-api-lp">
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
	              <span><?php echo wp_kses( uid_section_val( 'eklead', 'route_alert_text', __( 'وب‌سرویس احراز هویت تصویری فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی به احراز هویت نیاز دارید، <a href="/sana/">احراز هویت ثنا</a> یا <a href="/uid-plus/">یوآیدی‌پلاس</a> گزینه درست شماست.', 'uid-theme' ) ), array( 'a' => array( 'href' => true ) ) ); ?></span></div>
	            <button class="ek-btn btn-cta ek-btn-block" type="button" data-submit><?php echo uid_ek_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'eklead', 'submit_text', __( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
	              <span><?php echo esc_html( uid_section_val( 'eklead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_ek_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'eklead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'eklead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_ek_page_id() {
	$page_id = (int) get_option( 'uid_ek_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_EKYC_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_ek_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_ek_page() {
	if ( uid_get_ek_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'وب‌سرویس احراز هویت تصویری', 'uid-theme' ),
		'post_name'   => 'api',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_EKYC_TEMPLATE );
	update_option( 'uid_ek_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_ek_page' );

function uid_ensure_ek_page_once() {
	if ( get_option( 'uid_ek_page_bootstrapped' ) ) return;
	uid_ensure_ek_page();
	update_option( 'uid_ek_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_ek_page_once' );

function uid_register_ek_slug_setting() {
	register_setting( 'uid_ek_group', 'uid_ek_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_ek_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_ek_page_slug_section', '', '__return_false', 'uid_ek_layout' );
	add_settings_field( 'uid_ek_page_slug', __( 'آدرس (اسلاگ) صفحه احراز هویت تصویری', 'uid-theme' ), 'uid_field_ek_page_slug', 'uid_ek_layout', 'uid_ek_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_ek_slug_setting' );

function uid_field_ek_page_slug( $args ) {
	$page_id = uid_get_ek_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_ek_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_ek_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه احراز هویت تصویری هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_ek_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_ek_page_id();

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
function uid_register_ek_settings() {
	register_setting( 'uid_ek_group', 'uid_ek_layout', array(
		'sanitize_callback' => 'uid_sanitize_ek_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_ek_layout_main', '', '__return_false', 'uid_ek_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_ek_layout', 'uid_ek_layout_main', array(
		'option_name' => 'uid_ek_layout', 'registry_fn' => 'uid_ek_sections_registry', 'layout_fn' => 'uid_get_ek_layout',
	) );

	$fold_fields = array(
		array( 'key' => 'fold_title', 'type' => 'text' ),
		array( 'key' => 'fold_teaser', 'type' => 'text' ),
	);

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekhero', array( 'sanitize_callback' => 'uid_sanitize_section_ekhero', 'default' => array() ) );
	add_settings_section( 'uid_section_ekhero_main', '', '__return_false', 'uid_section_ekhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس احراز هویت تصویری · e-KYC API' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'heading', 'default' => 'کاربرتان را وادار نکنید سرش را بچرخاند.<mark>او ثبت‌نام را رها می‌کند.</mark>' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'text', 'default' => 'هر فرمان اضافه در احراز هویت — پلک بزن، سرت را بچرخان، این متن را بخوان — یک پله ریزش است. وب‌سرویس احراز هویت تصویری یوآیدی با فناوری Passive Liveness فقط ۵ ثانیه نگاه ثابت به دوربین می‌خواهد، و چهره کاربر را با تصویر رسمی پایگاه ثبت‌احوال کشور با دقت ۹۸.۱۷ درصد تطبیق می‌دهد.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'btn1_text', 'default' => 'درخواست فعال‌سازی سرویس' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'group' => 'uid_section_ekhero', 'key' => 'tags', 'default' => "بدون نصب اپلیکیشن\nهزینه فقط بابت احراز موفق\nپاسخ در کمتر از ۵ ثانیه\nپشتیبانی REST و gRPC" ) );
	add_settings_field( 'scenarios', __( 'سناریوهای شبیه‌سازی جلسه (برچسب + کد ملی/تاریخ تولد/توضیح نمونه)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekhero', 'uid_section_ekhero_main', array(
		'group' => 'uid_section_ekhero', 'key' => 'scenarios', 'default' => uid_default_ek_scenarios(),
		'fields' => array(
			array( 'key' => 'sc', 'type' => 'select', 'label' => __( 'سناریو', 'uid-theme' ), 'options' => array( 'ok' => __( 'کاربر واقعی (موفق)', 'uid-theme' ), 'print' => __( 'عکس چاپ‌شده', 'uid-theme' ), 'screen' => __( 'نمایش روی مانیتور', 'uid-theme' ), 'nomatch' => __( 'چهره غیرمنطبق', 'uid-theme' ) ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب دکمه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'nid', 'type' => 'text', 'label' => __( 'کد ملی نمونه', 'uid-theme' ) ),
			array( 'key' => 'birth', 'type' => 'text', 'label' => __( 'تاریخ تولد نمونه', 'uid-theme' ) ),
			array( 'key' => 'cap_live', 'type' => 'text', 'label' => __( 'توضیح مرحله تشخیص زنده‌بودن', 'uid-theme' ) ),
			array( 'key' => 'cap_end', 'type' => 'text', 'label' => __( 'توضیح نتیجه نهایی', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_ekhero', 'uid_section_ekhero_main', array( 'text' => __( 'شبیه‌سازی جلسه احراز هویت فقط در «سناریوها» بالا (برچسب، کد ملی/تاریخ تولد و توضیح نمونه) قابل‌ویرایش است؛ برای هر سناریو یک ردیف کافی است، ردیف‌های تکراری با یک «سناریو» یکسان جایگزین هم می‌شوند. کدهای وضعیت/دلیل پاسخ (status/reason) دقیقاً منطبق با ساختار واقعی سرویس‌اند و در assets/js/ekyc-liveness-page.js می‌مانند.', 'uid-theme' ) ) );

	/* ---------------- نوار آمار ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekstats', array( 'sanitize_callback' => 'uid_sanitize_section_ekstats', 'default' => array() ) );
	add_settings_section( 'uid_section_ekstats_main', '', '__return_false', 'uid_section_ekstats' );
	add_settings_field( 'items', __( 'آمار', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekstats', 'uid_section_ekstats_main', array(
		'group' => 'uid_section_ekstats', 'key' => 'items', 'default' => uid_default_ek_stats(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'is_text', 'type' => 'select', 'label' => __( 'نوع نمایش', 'uid-theme' ), 'options' => array( '' => __( 'عدد/درصد', 'uid-theme' ), '1' => __( 'متن آزاد', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- محاسبه‌گر ریزش ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekloss', array( 'sanitize_callback' => 'uid_sanitize_section_ekloss', 'default' => array() ) );
	add_settings_section( 'uid_section_ekloss_main', '', '__return_false', 'uid_section_ekloss' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'eyebrow', 'default' => 'قبل از اینکه ادامه بدهید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'heading', 'default' => 'ریزش در مرحله احراز هویت، گران‌ترین ریزش کل قیف شماست' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'text', 'default' => 'کاربری که تا صفحه احراز هویت آمده، تمام هزینه جذبش پرداخت شده است. اگر همان‌جا به‌خاطر فرمان‌های آزاردهنده Active Liveness رها کند، آن هزینه سوخته است. عددهای خودتان را وارد کنید و ببینید ماهانه چقدر است.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'text' => __( 'محاسبه‌گر ریزش (اسلایدرها) کاملاً تعاملی است و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );
	add_settings_field( 'proof', __( 'کارت‌های اثبات (دک کنار محاسبه‌گر)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekloss', 'uid_section_ekloss_main', array(
		'group' => 'uid_section_ekloss', 'key' => 'proof', 'default' => uid_default_ek_proof(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'callband_heading', __( 'بند تماس — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'callband_heading', 'default' => 'ترجیح می‌دهید همین حالا صحبت کنید؟' ) );
	add_settings_field( 'callband_text', __( 'بند تماس — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekloss', 'uid_section_ekloss_main', array( 'group' => 'uid_section_ekloss', 'key' => 'callband_text', 'default' => 'یک تماس کوتاه کافی است تا سناریوی ثبت‌نام شما بررسی و مسیر اتصال مشخص شود.' ) );

	/* ---------------- فصل ۱ — Passive vs Active ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekavp', array( 'sanitize_callback' => 'uid_sanitize_section_ekavp', 'default' => array() ) );
	add_settings_section( 'uid_section_ekavp_main', '', '__return_false', 'uid_section_ekavp' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'fold_title', 'default' => 'Passive در برابر Active' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'fold_teaser', 'default' => 'چرا روش سنتی کاربر را فراری می‌دهد' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'eyebrow', 'default' => 'مقایسه سناریوی کاربر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'heading', 'default' => 'دو تجربه، دو نتیجه کاملاً متفاوت' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'text', 'default' => 'در روش سنتی، کاربر باید به دستور سیستم سرش را بچرخاند، پلک بزند، لبخند بزند یا متنی را بخواند. در روش یوآیدی، فقط نگاه می‌کند.' ) );
	add_settings_field( 'old_title', __( 'کارت روش سنتی — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'old_title', 'default' => 'کاربر باید برای سیستم کار کند' ) );
	add_settings_field( 'old_text', __( 'کارت روش سنتی — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'old_text', 'default' => 'هر فرمان یک فرصت برای خطاست: اینترنت کند می‌شود، کاربر دستور را نمی‌فهمد، نور کافی نیست و دوباره از اول.' ) );
	add_settings_field( 'old_commands', __( 'کارت روش سنتی — فرمان‌ها (هر خط: متن|وضعیت)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'old_commands', 'default' => implode( "\n", uid_default_ek_avp_old() ), 'rows' => 5 ) );
	add_settings_field( 'old_footer', __( 'کارت روش سنتی — پیامد', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'old_footer', 'default' => 'پیامد: نرخ ریزش بسیار بالا و تجربه کاربری خسته‌کننده' ) );
	add_settings_field( 'new_title', __( 'کارت روش نوین — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'new_title', 'default' => 'کاربر فقط ۵ ثانیه نگاه می‌کند' ) );
	add_settings_field( 'new_text', __( 'کارت روش نوین — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'new_text', 'default' => 'هیچ اقدام اجباری و هیچ حرکت اضافه‌ای در کار نیست. الگوریتم کار سخت را پشت صحنه انجام می‌دهد.' ) );
	add_settings_field( 'new_commands', __( 'کارت روش نوین — موارد خودکار (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'new_commands', 'default' => implode( "\n", uid_default_ek_avp_new() ), 'rows' => 5 ) );
	add_settings_field( 'new_footer', __( 'کارت روش نوین — پیامد', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'new_footer', 'default' => 'پیامد: حفظ نرخ تبدیل و تاییدیه آنی برای کاربر' ) );
	add_settings_field( 'note_heading', __( 'یادداشت پایین — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'note_heading', 'default' => 'تفاوت در جایی رخ می‌دهد که بیشترین هزینه را دارد' ) );
	add_settings_field( 'note_text', __( 'یادداشت پایین — متن', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekavp', 'uid_section_ekavp_main', array( 'group' => 'uid_section_ekavp', 'key' => 'note_text', 'default' => 'کاربری که تا مرحله احراز هویت آمده، تمام هزینه بازاریابی و جذبش پرداخت شده است. ریزش در این نقطه، گران‌ترین ریزش ممکن در کل قیف شماست.' ) );

	/* ---------------- فصل ۲ — تطبیق چهره ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekface', array( 'sanitize_callback' => 'uid_sanitize_section_ekface', 'default' => array() ) );
	add_settings_section( 'uid_section_ekface_main', '', '__return_false', 'uid_section_ekface' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'fold_title', 'default' => 'تطبیق چهره با ثبت‌احوال' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'fold_teaser', 'default' => 'تفاوت Verification یک‌به‌یک با Recognition یک‌به‌چند' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'eyebrow', 'default' => 'فناوری تطبیق چهره' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'heading', 'default' => '«این شخص، همان صاحب مدرک است»' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'text', 'default' => 'دو مفهوم که مدام با هم اشتباه گرفته می‌شوند و کاربرد کاملاً متفاوتی دارند. آنچه در احراز هویت لازم دارید، اولی است.' ) );
	add_settings_field( 'card1_badge', __( 'کارت ۱ — برچسب', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card1_badge', 'default' => 'Face Verification — ۱:۱' ) );
	add_settings_field( 'card1_title', __( 'کارت ۱ — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card1_title', 'default' => 'تطبیق یک به یک' ) );
	add_settings_field( 'card1_text', __( 'کارت ۱ — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card1_text', 'default' => 'چهره حاضر در ویدیو با عکس متناظر همان کد ملی در پایگاه ثبت‌احوال مقایسه می‌شود تا تایید شود «این شخص همان صاحب مدرک است». پاسخ یک بله یا خیر قطعی است.' ) );
	add_settings_field( 'card1_use', __( 'کارت ۱ — برچسب کاربرد', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card1_use', 'default' => 'همان چیزی که وب‌سرویس احراز هویت تصویری یوآیدی انجام می‌دهد' ) );
	add_settings_field( 'card2_badge', __( 'کارت ۲ — برچسب', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card2_badge', 'default' => 'Face Recognition — ۱:N' ) );
	add_settings_field( 'card2_title', __( 'کارت ۲ — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card2_title', 'default' => 'شناسایی یک به چند' ) );
	add_settings_field( 'card2_text', __( 'کارت ۲ — توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card2_text', 'default' => 'جستجوی یک چهره در میان انبوهی از تصاویر برای یافتن هویت. کاربردش شناسایی است، نه تایید هویت یک فرد مشخص در فرآیند ثبت‌نام.' ) );
	add_settings_field( 'card2_use', __( 'کارت ۲ — برچسب کاربرد', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'card2_use', 'default' => 'برای احراز هویت کاربر در ثبت‌نام، ابزار درستی نیست' ) );
	add_settings_field( 'overlap_value', __( 'درصد هم‌پوشانی نمودار', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'overlap_value', 'default' => '۹۸٫۱۷٪' ) );
	add_settings_field( 'note_heading', __( 'یادداشت پایین — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'note_heading', 'default' => 'تغییر ظاهر مانع تشخیص نمی‌شود' ) );
	add_settings_field( 'note_text', __( 'یادداشت پایین — متن', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekface', 'uid_section_ekface_main', array( 'group' => 'uid_section_ekface', 'key' => 'note_text', 'default' => 'الگوریتم‌های تطبیق چهره بر اساس ساختار بیومتریک و فاصله‌های هندسی صورت آموزش دیده‌اند. عینک طبی، تغییر مدل مو، ریش یا گذشت زمان نسبت به عکس کارت ملی، مانع از تشخیص دقیق چهره نمی‌شود.' ) );

	/* ---------------- فصل ۳ — مراحل عملکرد ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekflow', array( 'sanitize_callback' => 'uid_sanitize_section_ekflow', 'default' => array() ) );
	add_settings_section( 'uid_section_ekflow_main', '', '__return_false', 'uid_section_ekflow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekflow', 'uid_section_ekflow_main', array( 'group' => 'uid_section_ekflow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekflow', 'uid_section_ekflow_main', array( 'group' => 'uid_section_ekflow', 'key' => 'fold_title', 'default' => 'مراحل عملکرد وب‌سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekflow', 'uid_section_ekflow_main', array( 'group' => 'uid_section_ekflow', 'key' => 'fold_teaser', 'default' => 'از کد ملی تا پاسخ ساختاریافته، در پنج گام' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekflow', 'uid_section_ekflow_main', array( 'group' => 'uid_section_ekflow', 'key' => 'eyebrow', 'default' => 'پشت صحنه سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekflow', 'uid_section_ekflow_main', array( 'group' => 'uid_section_ekflow', 'key' => 'heading', 'default' => 'پنج مرحله، بدون هیچ دخالت انسانی' ) );
	add_settings_field( 'items', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekflow', 'uid_section_ekflow_main', array(
		'group' => 'uid_section_ekflow', 'key' => 'items', 'default' => uid_default_ek_flow(), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'doc' => __( 'سند', 'uid-theme' ), 'video' => __( 'ویدیو', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ), 'face' => __( 'چهره', 'uid-theme' ), 'send' => __( 'ارسال', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'tagline', 'type' => 'text', 'label' => __( 'برچسب کوچک', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فصل ۴ — AI vs انسان ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekvs', array( 'sanitize_callback' => 'uid_sanitize_section_ekvs', 'default' => array() ) );
	add_settings_section( 'uid_section_ekvs_main', '', '__return_false', 'uid_section_ekvs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'fold_title', 'default' => 'هوش مصنوعی در برابر نیروی انسانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'fold_teaser', 'default' => 'خطا، سرعت، مقیاس و ریسک تقلب، کنار هم' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'eyebrow', 'default' => 'یک مقایسه بی‌تعارف' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'heading', 'default' => 'اپراتور انسانی، گران‌ترین و کندترین گزینه ممکن است' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'text', 'default' => 'بسیاری از کسب‌وکارها هنوز ویدیوها و مدارک را دستی بررسی می‌کنند. این جدول نشان می‌دهد چه چیزی را با چه چیزی عوض می‌کنید.' ) );
	add_settings_field( 'col2_label', __( 'عنوان ستون ۲', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'col2_label', 'default' => 'بررسی توسط نیروی انسانی' ) );
	add_settings_field( 'col3_label', __( 'عنوان ستون ۳', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'col3_label', 'default' => 'هوش مصنوعی یوآیدی' ) );
	add_settings_field( 'rows', __( 'ردیف‌های جدول', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekvs', 'uid_section_ekvs_main', array(
		'group' => 'uid_section_ekvs', 'key' => 'rows', 'default' => uid_default_ek_vs_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'desc'  => __( 'آخرین ردیف به‌صورت خودکار برجسته نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'شاخص', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'b2', 'type' => 'text', 'label' => __( 'ستون ۲', 'uid-theme' ) ),
			array( 'key' => 'b3', 'type' => 'text', 'label' => __( 'ستون ۳', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'big_label', __( 'برچسب عدد بزرگ', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'big_label', 'default' => 'فاصله واقعی میان دو ستون' ) );
	add_settings_field( 'big_value1', __( 'عدد بزرگ — مقدار ۱', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'big_value1', 'default' => 'چند روز کاری' ) );
	add_settings_field( 'big_value2', __( 'عدد بزرگ — مقدار ۲', 'uid-theme' ), 'uid_field_text', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'big_value2', 'default' => '۵ ثانیه' ) );
	add_settings_field( 'big_note', __( 'یادداشت زیر عدد بزرگ', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekvs', 'uid_section_ekvs_main', array( 'group' => 'uid_section_ekvs', 'key' => 'big_note', 'default' => 'و این فقط تفاوت سرعت است — تفاوت هزینه نیروی انسانی، آموزش، جایگزینی و ریسک تقلب به آن اضافه می‌شود.' ) );

	/* ---------------- فصل ۵ — کاربردها (انتخابگر صنعت) ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekwho', array( 'sanitize_callback' => 'uid_sanitize_section_ekwho', 'default' => array() ) );
	add_settings_section( 'uid_section_ekwho_main', '', '__return_false', 'uid_section_ekwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'fold_title', 'default' => 'کاربردهای وب‌سرویس' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'fold_teaser', 'default' => 'صنعت خودتان را انتخاب کنید تا فقط همان را ببینید' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'eyebrow', 'default' => 'کجا به کار می‌آید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'heading', 'default' => 'هر جا که کاربر باید بدون مراجعه حضوری ثبت‌نام کند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekwho', 'uid_section_ekwho_main', array( 'group' => 'uid_section_ekwho', 'key' => 'text', 'default' => 'صنعت خود را انتخاب کنید تا سناریوی دقیق شما را ببینید.' ) );
	add_settings_field( 'items', __( 'صنایع', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekwho', 'uid_section_ekwho_main', array(
		'group' => 'uid_section_ekwho', 'key' => 'items', 'default' => uid_default_ek_who(), 'add_label' => __( 'افزودن صنعت', 'uid-theme' ),
		'desc'  => __( 'برچسب همان چیزی است که در نوار چیپ‌های بالای این بخش نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'bank' => __( 'بانک', 'uid-theme' ), 'crypto' => __( 'رمزارز', 'uid-theme' ), 'lend' => __( 'وام', 'uid-theme' ), 'share' => __( 'اقتصاد مشارکتی', 'uid-theme' ), 'sign' => __( 'امضای دیجیتال', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب چیپ', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'win', 'type' => 'text', 'label' => __( 'برچسب نتیجه', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فصل ۶ — مقابله با جعل هویت ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekspoof', array( 'sanitize_callback' => 'uid_sanitize_section_ekspoof', 'default' => array() ) );
	add_settings_section( 'uid_section_ekspoof_main', '', '__return_false', 'uid_section_ekspoof' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'fold_title', 'default' => 'مقابله با جعل هویت' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'fold_teaser', 'default' => 'عکس چاپی، ماسک، مانیتور و ویدیوی ضبط‌شده' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'eyebrow', 'default' => 'موتور ضدجعل — Anti-Spoofing' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'heading', 'default' => 'چهار حمله‌ای که سیستم‌های ساده را رد می‌کنند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array( 'group' => 'uid_section_ekspoof', 'key' => 'text', 'default' => 'هر سامانه‌ای که فقط «یک صورت» را در تصویر می‌بیند، با یک عکس چاپی هم فریب می‌خورد. موتور ضدجعل یوآیدی به بافت پوست، بازتاب نور، عمق میدان و ریزحرکات طبیعی چهره نگاه می‌کند.' ) );
	add_settings_field( 'items', __( 'انواع حمله', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekspoof', 'uid_section_ekspoof_main', array(
		'group' => 'uid_section_ekspoof', 'key' => 'items', 'default' => uid_default_ek_spoof(), 'add_label' => __( 'افزودن حمله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'print' => __( 'عکس چاپی', 'uid-theme' ), 'screen' => __( 'صفحه نمایش', 'uid-theme' ), 'mask' => __( 'ماسک', 'uid-theme' ), 'video' => __( 'ویدیو', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فصل ۷ — تیم متخصص ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekteam', array( 'sanitize_callback' => 'uid_sanitize_section_ekteam', 'default' => array() ) );
	add_settings_section( 'uid_section_ekteam_main', '', '__return_false', 'uid_section_ekteam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'fold_title', 'default' => 'تیم متخصص و پشتیبانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'fold_teaser', 'default' => 'با چه کسانی وصل می‌شوید و چه چیزی تعهد می‌شود' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'eyebrow', 'default' => 'پشت این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'heading', 'default' => 'یک API نمی‌فروشیم؛ راه‌اندازی‌اش را تحویل می‌دهیم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'text', 'default' => 'احراز هویت تصویری حساس‌ترین نقطه تماس شما با کاربر است. به همین دلیل تیم فنی یوآیدی از اولین تماس تا مانیتورینگ عملکرد سرویس همراه شماست.' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekteam', 'uid_section_ekteam_main', array(
		'group' => 'uid_section_ekteam', 'key' => 'rows', 'default' => uid_default_ek_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'expert' => __( 'کارشناس', 'uid-theme' ), 'chart' => __( 'مانیتورینگ', 'uid-theme' ), 'docs' => __( 'مستندات', 'uid-theme' ), 'shield' => __( 'سابقه/اعتماد', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به تیم فنی شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekteam', 'uid_section_ekteam_main', array( 'group' => 'uid_section_ekteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_ek_pledge_items() ), 'rows' => 5 ) );

	/* ---------------- فصل ۸ — فنی و نمونه‌کد ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekdev', array( 'sanitize_callback' => 'uid_sanitize_section_ekdev', 'default' => array() ) );
	add_settings_section( 'uid_section_ekdev_main', '', '__return_false', 'uid_section_ekdev' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'fold_title', 'default' => 'بخش فنی و نمونه کد' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'fold_teaser', 'default' => 'درخواست، پاسخ، کال‌بک و جدول کدهای وضعیت' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'eyebrow', 'default' => 'برای تیم توسعه شما' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'heading', 'default' => 'دو مرحله، دو فراخوانی، تمام' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'text', 'default' => 'در مرحله اول اطلاعات دریافتی از کاربر توسط پذیرنده به یوآیدی ارسال می‌شود. در ادامه و پس از احراز هویت کاربر، یوآیدی آدرس بازگشت اعلامی پذیرنده (callback_url) را با پارامترهای مربوطه فراخوانی کرده و نتیجه را به اطلاع پذیرنده می‌رساند.' ) );
	add_settings_field( 'input_fields', __( 'فیلدهای ورودی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekdev', 'uid_section_ekdev_main', array(
		'group' => 'uid_section_ekdev', 'key' => 'input_fields', 'default' => uid_default_ek_input_fields(), 'add_label' => __( 'افزودن فیلد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'field', 'type' => 'text', 'label' => __( 'نام فیلد', 'uid-theme' ) ), array( 'key' => 'desc', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'security_heading', __( 'نکته امنیتی — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'security_heading', 'default' => 'نکته امنیتی درباره لینک ویدیو' ) );
	add_settings_field( 'security_text', __( 'نکته امنیتی — متن', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'security_text', 'default' => 'ویدیوی سلفی احراز هویت کاربر توسط پذیرنده دریافت و لینک آن جهت احراز هویت به یوآیدی ارسال می‌شود. رعایت جوانب امنیتی در ایجاد این لینک بسیار حائز اهمیت است؛ توصیه می‌شود مدت زمان اعتبار لینک نیز محدود گردد. فرمت‌های پذیرفته‌شده mp4 و webm است.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'btn1_text', 'default' => 'دریافت کلید وب‌سرویس' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'btn2_text', 'default' => 'مشاهده مستندات کامل API' ) );
	add_settings_field( 'btn2_url', __( 'لینک دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'group' => 'uid_section_ekdev', 'key' => 'btn2_url', 'default' => '/api-ekyc-docs/' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_ekdev', 'uid_section_ekdev_main', array( 'text' => __( 'نمونه‌کدها و جدول کدهای وضعیت، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/ekyc-liveness-page.php مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- فصل ۹ — سرویس‌های مکمل ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekxsell', array( 'sanitize_callback' => 'uid_sanitize_section_ekxsell', 'default' => array() ) );
	add_settings_section( 'uid_section_ekxsell_main', '', '__return_false', 'uid_section_ekxsell' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'fold_title', 'default' => 'سرویس‌های مکمل و مرتبط' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'fold_teaser', 'default' => 'یوآیدی‌پلاس، یوآیدی پرو و وب‌سرویس ثبت‌احوال' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'eyebrow', 'default' => 'در کنار این وب‌سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'heading', 'default' => 'اگر نمی‌خواهید سمت خودتان چیزی پیاده‌سازی کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array( 'group' => 'uid_section_ekxsell', 'key' => 'text', 'default' => 'وب‌سرویس API برای تیم‌هایی است که می‌خواهند تجربه ثبت‌نام کاملاً در کنترل خودشان بماند. اگر ترجیح می‌دهید کل فرآیند برون‌سپاری شود، مسیرهای دیگری هم هست.' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس مرتبط', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekxsell', 'uid_section_ekxsell_main', array(
		'group' => 'uid_section_ekxsell', 'key' => 'items', 'default' => uid_default_ek_xsell(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'pwa' => __( 'یوآیدی‌پلاس', 'uid-theme' ), 'doc' => __( 'سند', 'uid-theme' ), 'crypto' => __( 'رمزارز', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فصل ۱۰ — سوالات متداول ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekfaq', array( 'sanitize_callback' => 'uid_sanitize_section_ekfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_ekfaq_main', '', '__return_false', 'uid_section_ekfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array( 'group' => 'uid_section_ekfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array( 'group' => 'uid_section_ekfaq', 'key' => 'fold_title', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array( 'group' => 'uid_section_ekfaq', 'key' => 'fold_teaser', 'default' => 'دقت، ضدجعل، تعرفه و تفاوت روش‌ها' ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array( 'group' => 'uid_section_ekfaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس، این‌ها را بخوانید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array( 'group' => 'uid_section_ekfaq', 'key' => 'heading', 'default' => 'سوالات متداول وب‌سرویس احراز هویت تصویری' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekfaq', 'uid_section_ekfaq_main', array(
		'group' => 'uid_section_ekfaq', 'key' => 'items', 'default' => uid_default_ek_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );

	/* ---------------- شاخص‌های عملکرد ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekadv', array( 'sanitize_callback' => 'uid_sanitize_section_ekadv', 'default' => array() ) );
	add_settings_section( 'uid_section_ekadv_main', '', '__return_false', 'uid_section_ekadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'eyebrow', 'default' => 'مزایای رقابتی وب‌سرویس یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'heading', 'default' => 'چهار عددی که تصمیم را می‌سازند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'text', 'default' => 'این‌ها شاخص‌های منتشرشده عملکرد موتور احراز هویت تصویری یوآیدی هستند — همان اعدادی که در ارزیابی فنی از شما پرسیده می‌شود.' ) );
	add_settings_field( 'items', __( 'شاخص‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_ekadv', 'uid_section_ekadv_main', array(
		'group' => 'uid_section_ekadv', 'key' => 'items', 'default' => uid_default_ek_adv(), 'add_label' => __( 'افزودن شاخص', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'featured', 'type' => 'select', 'label' => __( 'بزرگ‌نمایی شود؟', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'wide', 'type' => 'select', 'label' => __( 'دو ستونی نمایش داده شود؟', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'is_text', 'type' => 'select', 'label' => __( 'نوع مقدار', 'uid-theme' ), 'options' => array( '' => __( 'عدد/درصد', 'uid-theme' ), '1' => __( 'متن آزاد', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'meter', 'type' => 'text', 'label' => __( 'میزان نوار پیشرفت (۰ تا ۱۰۰)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'foot_text', 'default' => 'می‌خواهید این اعداد را روی داده واقعی کاربران خودتان ببینید؟' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_ekadv', 'uid_section_ekadv_main', array( 'group' => 'uid_section_ekadv', 'key' => 'btn_text', 'default' => 'درخواست دسترسی سندباکس' ) );

	/* ---------------- محاسبه‌گر تعرفه ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekprice', array( 'sanitize_callback' => 'uid_sanitize_section_ekprice', 'default' => array() ) );
	add_settings_section( 'uid_section_ekprice_main', '', '__return_false', 'uid_section_ekprice' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'eyebrow', 'default' => 'مدل محاسبه هزینه' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'heading', 'default' => 'فقط بابت احراز هویت‌های تاییدشده هزینه می‌دهید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'text', 'default' => 'هیچ حق اشتراک ثابتی بابت تلاش‌های ناموفق پرداخت نمی‌کنید. تعداد ثبت‌نام تخمینی ماهانه خود را جابه‌جا کنید تا پلن متناسب با کسب‌وکارتان را ببینید.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'btn_text', 'default' => 'دریافت تعرفه دقیق برای این حجم' ) );
	add_settings_field( 'btn_note', __( 'توضیح زیر دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'group' => 'uid_section_ekprice', 'key' => 'btn_note', 'default' => 'فرم را پر کنید تا کارشناس یوآیدی نرخ واقعی این پله را همراه با شرایط قرارداد برای شما ارسال کند.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_ekprice', 'uid_section_ekprice_main', array( 'text' => __( 'اعداد پایه و پله‌های تخفیف این محاسبه‌گر نمایشی هستند و از assets/js/ekyc-liveness-page.js (ثابت‌های BASE_UNIT و TIERS) تنظیم می‌شوند، نه از این صفحه.', 'uid-theme' ) ) );

	/* ---------------- بند میانی تماس ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_ekcallband', array( 'sanitize_callback' => 'uid_sanitize_section_ekcallband', 'default' => array() ) );
	add_settings_section( 'uid_section_ekcallband_main', '', '__return_false', 'uid_section_ekcallband' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ekcallband', 'uid_section_ekcallband_main', array( 'group' => 'uid_section_ekcallband', 'key' => 'heading', 'default' => 'هر روزی که این سرویس فعال نیست، ریزشش را پرداخت می‌کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ekcallband', 'uid_section_ekcallband_main', array( 'group' => 'uid_section_ekcallband', 'key' => 'text', 'default' => 'کاربرانی که امروز در مرحله احراز هویت رها می‌کنند، فردا برنمی‌گردند. یک تماس کوتاه کافی است تا کارشناس یوآیدی سناریوی ثبت‌نام شما را بررسی و دسترسی سندباکس را فعال کند.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_ekcallband', 'uid_section_ekcallband_main', array( 'group' => 'uid_section_ekcallband', 'key' => 'btn_text', 'default' => 'فرم درخواست سرویس' ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_ek_group', 'uid_section_eklead', array( 'sanitize_callback' => 'uid_sanitize_section_eklead', 'default' => array() ) );
	add_settings_section( 'uid_section_eklead_main', '', '__return_false', 'uid_section_eklead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'heading', 'default' => 'فرم درخواست فعال‌سازی وب‌سرویس احراز هویت تصویری' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'text', 'default' => 'برای دریافت مشاوره رایگان، کلید API و فعال‌سازی وب‌سرویس احراز هویت تصویری (Passive Liveness و تطبیق چهره)، اطلاعات خود را در این فرم وارد کنید. کارشناسان یوآیدی در کوتاه‌ترین زمان ممکن با شما تماس خواهند گرفت.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nدسترسی به سندباکس پیش از قرارداد\nهزینه فقط بابت احراز موفق\nتعرفه پلکانی متناسب با حجم شما" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'form_title', 'default' => 'درخواست وب‌سرویس احراز هویت تصویری' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'form_hint', 'default' => 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط: کلید|برچسب)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_eklead', 'uid_section_eklead_main', array(
		'group' => 'uid_section_eklead', 'key' => 'biztype_options', 'rows' => 8,
		'default' => "crypto|صرافی ارز دیجیتال یا بروکر\nbank|بانک، نئوبانک یا فین‌تک\nlend|لندتک و وام آنلاین\nshare|اقتصاد مشارکتی و پلتفرم خدماتی\nsign|امضای دیجیتال و قرارداد الکترونیک\nins|بیمه و خدمات مالی\nother|سایر کسب‌وکارها\nind|کاربر شخصی هستم (کسب‌وکار نیستم)",
		'desc' => __( 'کلید «ind» (کاربر شخصی) پیام هدایت به صفحات دیگر را نمایش می‌دهد؛ فرمت هر خط: کلید‌انگلیسی|برچسب فارسی.', 'uid-theme' ),
	) );
	add_settings_field( 'route_alert_text', __( 'پیام هدایت کاربران شخصی (تگ a مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'route_alert_text', 'default' => 'وب‌سرویس احراز هویت تصویری فقط به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی به احراز هویت نیاز دارید، <a href="/sana/">احراز هویت ثنا</a> یا <a href="/uid-plus/">یوآیدی‌پلاس</a> گزینه درست شماست.' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_eklead', 'uid_section_eklead_main', array( 'group' => 'uid_section_eklead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_ek_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_ekhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'scenarios' => uid_sanitize_repeater_rows( $input['scenarios'] ?? '[]', array(
			array( 'key' => 'sc', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'nid', 'type' => 'text' ),
			array( 'key' => 'birth', 'type' => 'text' ),
			array( 'key' => 'cap_live', 'type' => 'text' ),
			array( 'key' => 'cap_end', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ekstats( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'is_text', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ekloss( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'text'             => sanitize_textarea_field( $input['text'] ?? '' ),
		'proof'            => uid_sanitize_repeater_rows( $input['proof'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'callband_heading' => sanitize_text_field( $input['callband_heading'] ?? '' ),
		'callband_text'    => sanitize_textarea_field( $input['callband_text'] ?? '' ),
	);
}

function uid_sanitize_section_ekavp( $input ) {
	return array(
		'title_tag'    => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'   => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'  => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => sanitize_text_field( $input['heading'] ?? '' ),
		'text'         => sanitize_textarea_field( $input['text'] ?? '' ),
		'old_title'    => sanitize_text_field( $input['old_title'] ?? '' ),
		'old_text'     => sanitize_textarea_field( $input['old_text'] ?? '' ),
		'old_commands' => sanitize_textarea_field( $input['old_commands'] ?? '' ),
		'old_footer'   => sanitize_text_field( $input['old_footer'] ?? '' ),
		'new_title'    => sanitize_text_field( $input['new_title'] ?? '' ),
		'new_text'     => sanitize_textarea_field( $input['new_text'] ?? '' ),
		'new_commands' => sanitize_textarea_field( $input['new_commands'] ?? '' ),
		'new_footer'   => sanitize_text_field( $input['new_footer'] ?? '' ),
		'note_heading' => sanitize_text_field( $input['note_heading'] ?? '' ),
		'note_text'    => sanitize_textarea_field( $input['note_text'] ?? '' ),
	);
}

function uid_sanitize_section_ekface( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'    => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'   => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'card1_badge'   => sanitize_text_field( $input['card1_badge'] ?? '' ),
		'card1_title'   => sanitize_text_field( $input['card1_title'] ?? '' ),
		'card1_text'    => sanitize_textarea_field( $input['card1_text'] ?? '' ),
		'card1_use'     => sanitize_text_field( $input['card1_use'] ?? '' ),
		'card2_badge'   => sanitize_text_field( $input['card2_badge'] ?? '' ),
		'card2_title'   => sanitize_text_field( $input['card2_title'] ?? '' ),
		'card2_text'    => sanitize_textarea_field( $input['card2_text'] ?? '' ),
		'card2_use'     => sanitize_text_field( $input['card2_use'] ?? '' ),
		'overlap_value' => sanitize_text_field( $input['overlap_value'] ?? '' ),
		'note_heading'  => sanitize_text_field( $input['note_heading'] ?? '' ),
		'note_text'     => sanitize_textarea_field( $input['note_text'] ?? '' ),
	);
}

function uid_sanitize_section_ekflow( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'tagline', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ekvs( $input ) {
	return array(
		'title_tag'  => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title' => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'=> sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'    => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'    => sanitize_text_field( $input['heading'] ?? '' ),
		'text'       => sanitize_textarea_field( $input['text'] ?? '' ),
		'col2_label' => sanitize_text_field( $input['col2_label'] ?? '' ),
		'col3_label' => sanitize_text_field( $input['col3_label'] ?? '' ),
		'rows'       => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'b2', 'type' => 'text' ),
			array( 'key' => 'b3', 'type' => 'text' ),
		) ),
		'big_label'  => sanitize_text_field( $input['big_label'] ?? '' ),
		'big_value1' => sanitize_text_field( $input['big_value1'] ?? '' ),
		'big_value2' => sanitize_text_field( $input['big_value2'] ?? '' ),
		'big_note'   => sanitize_textarea_field( $input['big_note'] ?? '' ),
	);
}

function uid_sanitize_section_ekwho( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
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
	);
}

function uid_sanitize_section_ekspoof( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_ekteam( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'     => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'    => sanitize_text_field( $input['fold_teaser'] ?? '' ),
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
	);
}

function uid_sanitize_section_ekdev( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'       => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser'      => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'text'             => sanitize_textarea_field( $input['text'] ?? '' ),
		'input_fields'     => uid_sanitize_repeater_rows( $input['input_fields'] ?? '[]', array(
			array( 'key' => 'field', 'type' => 'text' ),
			array( 'key' => 'desc', 'type' => 'text' ),
		) ),
		'security_heading' => sanitize_text_field( $input['security_heading'] ?? '' ),
		'security_text'    => sanitize_textarea_field( $input['security_text'] ?? '' ),
		'btn1_text'        => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text'        => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'btn2_url'         => sanitize_text_field( $input['btn2_url'] ?? '' ),
	);
}

function uid_sanitize_section_ekxsell( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'url', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_ekfaq( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_ekadv( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'featured', 'type' => 'text' ),
			array( 'key' => 'wide', 'type' => 'text' ),
			array( 'key' => 'is_text', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'meter', 'type' => 'text' ),
		) ),
		'foot_text' => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_ekprice( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
		'btn_note'  => sanitize_text_field( $input['btn_note'] ?? '' ),
	);
}

function uid_sanitize_section_ekcallband( $input ) {
	return array(
		'heading'  => sanitize_text_field( $input['heading'] ?? '' ),
		'text'     => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text' => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_eklead( $input ) {
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
function uid_render_ek_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست وب‌سرویس احراز هویت تصویری', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اطلاعات کسب‌وکارتان را بگذارید؛ کارشناس یوآیدی همین امروز تماس می‌گیرد، پلن متناسب با حجم ثبت‌نام ماهانه شما را پیشنهاد می‌دهد و دسترسی به محیط سندباکس و کلید API احراز هویت تصویری را فعال می‌کند.', 'uid-theme' ); ?></p>
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
