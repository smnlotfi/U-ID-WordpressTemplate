<?php
/**
 * صفحه اختصاصی «ثبت‌نام سامانه ثنا ویژه ایرانیان خارج از کشور» — دقیقاً همان
 * الگوی صفحات قبلی: برگه‌ی واقعی خودکارساخته + قالب صفحه + سیستم سکشن
 * قابل‌مدیریت از پیشخوان. اسلاگ‌های سکشن با پیشوند «sa» نام‌گذاری شده‌اند تا در
 * نام آپشن‌های wp_options با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * برخلاف صفحات وب‌سرویس API قبلی (ib/iv/cv/ci)، این صفحه یک صفحه «ثبت سفارش
 * آنلاین» است: ثبت‌نام ثنا برای ایرانیان مقیم خارج از کشور، بدون نیاز به سفر به
 * ایران یا کارت بانکی ایرانی. پرداخت هزینه احراز هویت با کارت بین‌المللی از
 * طریق یوآیدی و تسویه ریالی داخل ایران انجام می‌شود.
 *
 * ۹ سکشن این صفحه (what/steps/docs/igap/pay/team/track/xsell/faq) سیستم «فصل
 * تاخوردنی + کتاب‌خوان فصل» موبایل دارند (دقیقاً مثل inc/card-validate-page.php)
 * و همان فیلتر «فقط بخش‌های ضروری» را هم دارند — نگاه کنید به
 * uid_sa_folded_slugs()/uid_render_sa_foldbar().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_SANA_ABROAD_TEMPLATE', 'template-sana-abroad.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_sa_sections_registry() {
	return array(
		'sahero'   => array( 'label' => __( 'هیرو + کنسول کد ۱۶ رقمی و ساعت تهران', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'satrust'  => array( 'label' => __( 'باند اعتماد کوتاه (۴ آمار)', 'uid-theme' ),               'icon' => 'dashicons-chart-bar' ),
		'sastuck'  => array( 'label' => __( 'نقطه توقف (نقشه ۴ فاز ثبت‌نام)', 'uid-theme' ),           'icon' => 'dashicons-map' ),
		'sacost'   => array( 'label' => __( 'محاسبه‌گر هزینه سفر جایگزین', 'uid-theme' ),               'icon' => 'dashicons-calculator' ),
		'sarouter' => array( 'label' => __( 'مسیریاب یک‌سوالی', 'uid-theme' ),                          'icon' => 'dashicons-randomize' ),
		'sawhat'   => array( 'label' => __( 'فصل ۱ — سامانه ثنا چیست', 'uid-theme' ),                   'icon' => 'dashicons-editor-help' ),
		'sasteps'  => array( 'label' => __( 'فصل ۲ — راهنمای ۲۱ مرحله (ریلی)', 'uid-theme' ),           'icon' => 'dashicons-list-view' ),
		'sadocs'   => array( 'label' => __( 'فصل ۳ — مدارک و اطلاعات لازم', 'uid-theme' ),              'icon' => 'dashicons-media-document' ),
		'saigap'   => array( 'label' => __( 'فصل ۴ — آی‌گپ به‌جای شماره ایرانی', 'uid-theme' ),         'icon' => 'dashicons-smartphone' ),
		'sapay'    => array( 'label' => __( 'فصل ۵ — درگاه پرداخت ارزی یوآیدی', 'uid-theme' ),          'icon' => 'dashicons-money-alt' ),
		'sateam'   => array( 'label' => __( 'فصل ۶ — تیم متخصص', 'uid-theme' ),                         'icon' => 'dashicons-businessperson' ),
		'satrack'  => array( 'label' => __( 'فصل ۷ — پیگیری و استعلام برگه ثنا', 'uid-theme' ),         'icon' => 'dashicons-search' ),
		'saxsell'  => array( 'label' => __( 'فصل ۸ — سرویس‌های مرتبط', 'uid-theme' ),                   'icon' => 'dashicons-networking' ),
		'safaq'    => array( 'label' => __( 'فصل ۹ — سوالات متداول', 'uid-theme' ),                     'icon' => 'dashicons-editor-help' ),
		'saadv'    => array( 'label' => __( 'مشخصات سرویس (پنل تیره)', 'uid-theme' ),                   'icon' => 'dashicons-awards' ),
		'saorder'  => array( 'label' => __( 'نحوه ثبت سفارش (۳ گام)', 'uid-theme' ),                    'icon' => 'dashicons-controls-forward' ),
		'salead'   => array( 'label' => __( 'بنر تماس نهایی (فرم ثبت سفارش)', 'uid-theme' ),            'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_sa_layout() {
	$registry = uid_sa_sections_registry();
	$saved    = get_option( 'uid_sa_layout', array() );

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

function uid_sanitize_sa_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_sa_sections_registry();
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
function uid_sa_folded_slugs() {
	return array( 'sawhat', 'sasteps', 'sadocs', 'saigap', 'sapay', 'sateam', 'satrack', 'saxsell', 'safaq' );
}

/**
 * اسلاگ‌های فصل‌های «کلیدی» (data-fold-hot) — پایه فیلتر «فقط بخش‌های ضروری».
 */
function uid_sa_hot_slugs() {
	return array( 'sawhat', 'sasteps', 'sadocs', 'sapay', 'safaq' );
}

function uid_render_sa_sections() {
	$folded    = uid_sa_folded_slugs();
	$layout    = uid_get_sa_layout();
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
			uid_render_sa_foldbar( $folded_on );
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
 * شامل دکمه «فقط بخش‌های ضروری» (فیلتر data-fold-hot) و نوار پیشرفت مطالعه.
 */
function uid_render_sa_foldbar( $count ) {
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

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_sa_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_sa_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_sa_telegram_icon() {
	return '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3L18.6 20c-.25 1.1-.9 1.37-1.83.85l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.46c.4-.36-.09-.56-.63-.2L5.79 12.47.83 10.92c-1.08-.34-1.1-1.08.23-1.6L20.5 2.73c.9-.33 1.69.2 1.4 1.57z"/></svg>';
}
function uid_sa_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_sa_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L2.4 18a2 2 0 001.7 3h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_sa_info_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9.5"/></svg>';
}
function uid_sa_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
function uid_sa_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
/**
 * آیکون‌های ثابتِ کارت‌های تزئینی سه‌تایی (ریسک‌های نبود ثنا) — بر اساس اندیس.
 */
function uid_sa_risk_icon_svg( $i ) {
	$icons = array(
		'<path d="M4 4h16v13H7l-3 3z"/><path d="M9 9h6M9 12.5h4"/>',
		'<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><path d="M9.5 14.5l1.8 1.8 3.5-3.8"/>',
		'<path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>',
	);
	return $icons[ $i ] ?? $icons[0];
}
/**
 * آیکون‌های ثابتِ مدارک لازم (هشت مورد) — بر اساس اندیس.
 */
function uid_sa_doc_icon_svg( $i ) {
	$icons = array(
		'<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8.5" cy="12" r="2.3"/><path d="M14 10h5M14 14h3"/>',
		'<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
		'<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'<path d="M22 9L12 3 2 9l10 6 10-6z"/><path d="M6 11.5V16c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.5"/>',
		'<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 6 10-6"/>',
	);
	return $icons[ $i ] ?? $icons[0];
}
/**
 * آیکون‌های ثابتِ ردیف‌های تیم (چهار مورد) — بر اساس اندیس.
 */
function uid_sa_crew_icon_svg( $i ) {
	$icons = array(
		'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
		'<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
		'<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M8 10a4 4 0 011-2.6"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/>',
	);
	return $icons[ $i ] ?? $icons[0];
}
function uid_sa_xsell_icon_svg( $key ) {
	$icons = array(
		'sana'   => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'sejam'  => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'pwa'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['sana'];
}
function uid_sa_adv_icon_svg( $key ) {
	$icons = array(
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'card'    => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'pin'     => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'support' => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
		'mail'    => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 6 10-6"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}

/* =====================================================================
 * ۱) هیرو + کنسول کد ۱۶ رقمی و ساعت تهران (شبیه‌سازی نمایشی، منطق آن در JS هاردکد است)
 * ===================================================================== */
function uid_default_sa_hero_tags() {
	return array(
		'بدون نیاز به سفر به ایران',
		'بدون نیاز به کارت بانکی ایرانی',
		'پرداخت ریالی داخل ایران توسط یوآیدی',
		'پشتیبانی تلگرام کارشناسان ثنای خارج از کشور',
	);
}
function uid_render_section_sahero() {
	$tag  = uid_section_tag( 'sahero', 'h1' );
	$tags = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'sahero', 'tags', implode( "\n", uid_default_sa_hero_tags() ) ) ) ) );
	?>
	<section class="dark heroA" id="top">
	  <div class="sa-wrap">
	    <div style="padding-block-start:80px">
	      <nav class="crumb" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'uid-theme' ); ?>">
	        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span>/</span>
	        <a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'محصولات', 'uid-theme' ); ?></a><span>/</span>
	        <b><?php echo esc_html( get_the_title() ?: __( 'ثنا ویژه ایرانیان خارج از کشور', 'uid-theme' ) ); ?></b>
	      </nav>

	      <div class="heroA-grid" style="margin-block-start:26px">
	        <div>
	          <span class="eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'sahero', 'eyebrow', __( 'اپراتور اول احراز هویت ایران · کارگزار مورد تایید قوه قضائیه', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'sahero', 'heading', __( 'ثبت‌نام سامانه ثنا،<br><mark>از هر کجای دنیا</mark> که هستید', 'uid-theme' ) ), array( 'br' => array(), 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p class="lede on-dark"><?php echo wp_kses( uid_section_val( 'sahero', 'text', __( 'یوآیدی مفتخر است برای اولین بار در ایران، خدمت ثبت‌نام و احراز هویت ایرانیان خارج از کشور در سامانه ثنا را به‌صورت آنلاین ارائه نماید. هزینه سرویس را با ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی از طریق درگاه پرداخت ارزی یوآیدی می‌پردازید و پرداخت ریالی آن، به‌صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع‌رسانی می‌شود.', 'uid-theme' ) ), array( 'b' => array( 'style' => array(), 'font-weight' => array() ) ) ); ?></p>

	          <div class="btn-row">
	            <a class="btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sa_phone_icon(); ?>
	              <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	            <button class="btn btn-cta" data-open-modal><?php echo uid_sa_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'sahero', 'btn1_text', __( 'ثبت سفارش و مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	          </div>

	          <?php if ( $tags ) : ?>
	          <div class="hero-tags">
	            <?php foreach ( $tags as $t ) : ?>
	            <span class="hero-tag"><?php echo uid_sa_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>

	        <!-- ── the console (Tehran clock + 16-digit code console) ──────── -->
	        <div class="codew rv">
	          <div class="thclock" id="thclock">
	            <span class="dot"></span>
	            <span class="tx" id="thclockTx"><b><?php esc_html_e( 'باجه پرداخت ریالی یوآیدی باز است', 'uid-theme' ); ?></b></span>
	            <span class="now"><span><?php esc_html_e( 'ساعت تهران', 'uid-theme' ); ?></span><b id="thclockNow">--:--</b></span>
	          </div>

	          <div class="codebox">
	            <div class="codebox-hd">
	              <span class="stepno">STEP 14</span>
	              <b><?php esc_html_e( 'کد ۱۶ رقمی صفحه پرداخت ثنا', 'uid-theme' ); ?></b>
	            </div>

	            <label class="code-label" for="sanaCode">
	              <?php esc_html_e( 'در مرحله پرداخت، سامانه ثنا یک کد ۱۶ رقمی به شما نشان می‌دهد. همان کد را اینجا وارد کنید تا ببینیم آماده ثبت سفارش هستید یا نه.', 'uid-theme' ); ?>
	            </label>

	            <div class="code-field" id="codeField">
	              <input id="sanaCode" type="text" inputmode="numeric" autocomplete="off"
	                     spellcheck="false" maxlength="19" aria-label="<?php esc_attr_e( 'کد ۱۶ رقمی صفحه پرداخت سامانه ثنا', 'uid-theme' ); ?>">
	              <div class="code-cells" id="codeCells" aria-hidden="true"></div>
	            </div>

	            <div class="code-meta">
	              <span class="cnt"><b id="codeCnt">۰</b> / ۱۶ <?php esc_html_e( 'رقم', 'uid-theme' ); ?></span>
	              <button class="code-paste" type="button" id="codePaste">
	                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
	                <?php esc_html_e( 'چسباندن از حافظه', 'uid-theme' ); ?></button>
	            </div>

	            <div class="code-verdict" id="codeVerdict">
	              <span class="vic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 8v5M12 16.5h.01"/></svg></span>
	              <span class="vtx">
	                <b><?php esc_html_e( 'منتظر کد شما هستیم', 'uid-theme' ); ?></b>
	                <span><?php esc_html_e( 'اگر هنوز به مرحله پرداخت نرسیده‌اید هم اشکالی ندارد — دکمه پایین را بزنید تا از ابتدا راهنمایی‌تان کنیم.', 'uid-theme' ); ?></span>
	              </span>
	            </div>

	            <div class="code-acts">
	              <button class="btn btn-cta btn-block" id="codeGo" data-open-modal>
	                <?php echo uid_sa_submit_icon(); ?>
	                <?php esc_html_e( 'ثبت سفارش پرداخت ارزی', 'uid-theme' ); ?></button>
	            </div>

	            <div class="code-try">
	              <button type="button" data-code-demo="ok">
	                <?php echo uid_sa_check_icon(); ?>
	                <?php esc_html_e( 'یک کد نمونه را ببینید', 'uid-theme' ); ?></button>
	              <button type="button" data-code-demo="none" data-jump-soft="#steps">
	                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.7-2 1.9-2.3 3.6M12 17h.01"/></svg>
	                <?php esc_html_e( 'هنوز به این مرحله نرسیده‌ام', 'uid-theme' ); ?></button>
	            </div>
	          </div>

	          <!-- 30-second capsule: phones only -->
	          <div class="tldr" style="margin-block-start:18px">
	            <div class="tldr-hd">
	              <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/></svg></span>
	              <b><?php esc_html_e( 'خلاصه در ۳۰ ثانیه', 'uid-theme' ); ?></b><span><?php esc_html_e( 'لازم نیست کل صفحه را بخوانید', 'uid-theme' ); ?></span>
	            </div>
	            <ul class="tldr-list">
	              <li><?php echo uid_sa_check_icon(); ?><span><?php esc_html_e( 'ثبت‌نام ثنا از خارج از کشور ', 'uid-theme' ); ?><b><?php esc_html_e( 'ممکن است', 'uid-theme' ); ?></b><?php esc_html_e( ' — از طریق سامانه ', 'uid-theme' ); ?><span class="lat">international.adliran.ir</span>.</span></li>
	              <li><?php echo uid_sa_check_icon(); ?><span><?php esc_html_e( 'تنها جایی که گیر می‌کنید ', 'uid-theme' ); ?><b><?php esc_html_e( 'مرحله ۱۴', 'uid-theme' ); ?></b><?php esc_html_e( ' است: پرداخت ریالی هزینه احراز هویت.', 'uid-theme' ); ?></span></li>
	              <li><?php echo uid_sa_check_icon(); ?><span><?php esc_html_e( 'یوآیدی همان مرحله را با ', 'uid-theme' ); ?><b><?php esc_html_e( 'ویزا/مستر کارت', 'uid-theme' ); ?></b><?php esc_html_e( ' از شما می‌گیرد و پرداخت ریالی را داخل ایران انجام می‌دهد.', 'uid-theme' ); ?></span></li>
	              <li><?php echo uid_sa_check_icon(); ?><span><?php esc_html_e( 'مدارک لازم: ', 'uid-theme' ); ?><b><?php esc_html_e( 'کد ملی، شماره سریال شناسنامه، آیدی آی‌گپ', 'uid-theme' ); ?></b><?php esc_html_e( ' و آدرس محل سکونتتان در خارج از کشور.', 'uid-theme' ); ?></span></li>
	            </ul>
	            <div class="tldr-acts">
	              <a class="btn btn-cta btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sa_phone_icon(); ?>
	                <?php esc_html_e( 'تماس با کارشناس ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	              <button class="btn btn-ghost-d btn-block" data-open-modal><?php esc_html_e( 'ثبت سفارش آنلاین', 'uid-theme' ); ?></button>
	            </div>
	            <button class="tldr-more" type="button" data-jump-soft="#stuck">
	              <?php esc_html_e( 'ادامه صفحه را ببینم', 'uid-theme' ); ?>
	              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
	          </div>
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
function uid_default_sa_trust() {
	return array(
		array( 'value' => 'اپراتور اول', 'label' => 'اپراتور اول احراز هویت ایران', 'numeric' => '' ),
		array( 'value' => 'قوه قضائیه', 'label' => 'کارگزار مورد تایید قوه قضائیه', 'numeric' => '' ),
		array( 'value' => '۱۰۰٪', 'label' => 'آنلاین — بدون سفر به ایران و بدون مراجعه حضوری', 'numeric' => '1' ),
		array( 'value' => 'Visa / Master', 'label' => 'و سایر کارت‌های معتبر بین‌المللی از طریق درگاه ارزی یوآیدی', 'numeric' => '' ),
	);
}
function uid_render_section_satrust() {
	$items = uid_section_val( 'satrust', 'items', uid_default_sa_trust() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="sa-wrap">
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
 * ۳) نقطه توقف — نقشه ۴ فاز ثبت‌نام (کارت‌های تزئینی، قابل کشیدن روی موبایل)
 * ===================================================================== */
function uid_default_sa_phases() {
	return array(
		array( 'pn' => 'PHASE 1 · گام ۱ تا ۴', 'title' => 'ورود به سامانه', 'text' => 'ورود به آدرس اختصاصی ایرانیان خارج از کشور، انتخاب «سامانه ثبت نام الکترونیک قضایی» و سپس «ثبت نام برخط شخص حقیقی».', 'who' => 'خودتان انجام می‌دهید', 'mine' => '' ),
		array( 'pn' => 'PHASE 2 · گام ۵ تا ۱۳', 'title' => 'اطلاعات و احراز هویت', 'text' => 'کد ملی، تاریخ تولد، شماره سریال شناسنامه، اطلاعات شناسنامه‌ای، آیدی آی‌گپ، نشانی محل سکونت در خارج از کشور، تحصیلات و شغل، و سپس احراز هویت آنلاین.', 'who' => 'خودتان انجام می‌دهید', 'mine' => '' ),
		array( 'pn' => 'PHASE 3 · گام ۱۴ تا ۱۷', 'title' => 'پرداخت ارزی هزینه احراز هویت', 'text' => 'کد ۱۶ رقمی صفحه پرداخت ثنا را کپی می‌کنید، در فرم ثبت سفارش یوآیدی وارد می‌کنید، با کارت بین‌المللی می‌پردازید و پرداخت ریالی داخل ایران توسط یوآیدی انجام می‌شود.', 'who' => 'یوآیدی برای شما انجام می‌دهد', 'mine' => '1' ),
		array( 'pn' => 'PHASE 4 · گام ۱۸ تا ۲۱', 'title' => 'بازگشت و تکمیل ثبت‌نام', 'text' => 'لینک از طریق ایمیل می‌رسد، به سامانه ثنا برمی‌گردید، «به‌روزرسانی شرایط پرداخت» را می‌زنید و باقی مراحل احراز هویت را تا پایان انجام می‌دهید.', 'who' => 'خودتان، با پشتیبانی یوآیدی', 'mine' => '' ),
	);
}
function uid_render_section_sastuck() {
	$tag    = uid_section_tag( 'sastuck', 'h2' );
	$phases = uid_section_val( 'sastuck', 'phases', uid_default_sa_phases() );
	if ( ! is_array( $phases ) ) $phases = array();
	?>
	<section class="sec" id="stuck" style="padding-block:clamp(48px,6vw,84px) clamp(40px,4.5vw,64px)">
	  <div class="sa-wrap">
	    <div class="sec-head mid rv">
	      <span class="eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'sastuck', 'eyebrow', __( 'نقطه توقف', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sastuck', 'heading', __( '۲۰ مرحله را خودتان می‌روید. سر یک مرحله می‌مانید.', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sastuck', 'text', __( 'فرآیند ثبت‌نام ثنا برای ایرانیان خارج از کشور ۲۱ مرحله دارد و تقریباً همه آن‌ها را می‌توانید از هر کجای دنیا انجام دهید. اما در مرحله چهاردهم، سامانه ثنا برای انجام احراز هویت یک هزینه ریالی مطالبه می‌کند — و اینجاست که بدون کارت بانکی ایرانی، مسیر بسته می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="phases rv" data-rail="ph">
	      <?php foreach ( $phases as $p ) :
	        $classes = 'phase' . ( ! empty( $p['mine'] ) ? ' mine blocked' : '' );
	      ?>
	      <div class="<?php echo esc_attr( $classes ); ?>">
	        <?php if ( ! empty( $p['mine'] ) ) : ?>
	        <span class="phase-tag"><?php esc_html_e( 'اینجا با یوآیدی', 'uid-theme' ); ?></span>
	        <?php endif; ?>
	        <span class="pn"<?php echo ! empty( $p['mine'] ) ? ' style="margin-block-start:26px"' : ''; ?>><?php echo esc_html( $p['pn'] ?? '' ); ?></span>
	        <b><?php echo esc_html( $p['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $p['text'] ?? '' ); ?></p>
	        <span class="beads"><?php echo str_repeat( '<i></i>', ! empty( $p['mine'] ) ? 4 : ( strpos( $p['pn'] ?? '', '۵ تا ۱۳' ) !== false ? 9 : 4 ) ); ?></span>
	        <span class="who"><?php echo ! empty( $p['mine'] ) ? uid_sa_shield_icon() : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 016-6h4a6 6 0 016 6v1"/></svg>'; ?><?php echo esc_html( $p['who'] ?? '' ); ?></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="ph"></div>
	    <p class="swipe-hint">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
	      <?php esc_html_e( 'برای دیدن چهار فاز، بکشید', 'uid-theme' ); ?></p>

	    <div class="callband urgent rv" style="margin-block-start:26px">
	      <div class="ic"><?php echo uid_sa_warn_icon(); ?></div>
	      <div class="tx">
	        <b><?php echo esc_html( uid_section_val( 'sastuck', 'cta_heading', __( 'در همان مرحله ۱۴ گیر کرده‌اید؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'sastuck', 'cta_text', __( 'کد ۱۶ رقمی را بردارید و همین حالا تماس بگیرید — کارشناس بخش ثنای خارج از کشور یوآیدی مرحله به مرحله همراهتان است.', 'uid-theme' ) ) ); ?></p>
	      </div>
	      <div class="acts">
	        <a class="btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sa_phone_icon(); ?>
	          <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="btn btn-ghost-d" data-open-modal><?php esc_html_e( 'ثبت سفارش آنلاین', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) محاسبه‌گر هزینه سفر جایگزین (منطق محاسبه در JS هاردکد است)
 * ===================================================================== */
function uid_render_section_sacost() {
	$tag = uid_section_tag( 'sacost', 'h2' );
	?>
	<section class="dark sec" id="cost">
	  <div class="sa-wrap">
	    <div class="sec-head mid rv">
	      <span class="eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'sacost', 'eyebrow', __( 'گزینه دیگر روی میز', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sacost', 'heading', __( 'اگر بخواهید برای همین یک کار به ایران سفر کنید، چقدر خرج برمی‌دارد؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sacost', 'text', __( 'اعداد زیر را خودتان تعیین می‌کنید؛ این ماشین‌حساب هیچ آمار از پیش تعیین‌شده‌ای ندارد و فقط همان چیزی را جمع می‌زند که شما وارد کرده‌اید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="engine rv" style="max-width:940px;margin-inline:auto">
	      <div class="engine-hd">
	        <span class="live"></span>
	        <b><?php esc_html_e( 'برآورد هزینه سفر برای انجام حضوری', 'uid-theme' ); ?></b>
	        <span><?php esc_html_e( 'واحد پول را خودتان انتخاب کنید', 'uid-theme' ); ?></span>
	      </div>

	      <div class="cur-row" id="curRow" role="group" aria-label="<?php esc_attr_e( 'واحد پول', 'uid-theme' ); ?>">
	        <button class="cur-chip on" type="button" data-cur="EUR">EUR €</button>
	        <button class="cur-chip" type="button" data-cur="USD">USD $</button>
	        <button class="cur-chip" type="button" data-cur="GBP">GBP £</button>
	        <button class="cur-chip" type="button" data-cur="CAD">CAD $</button>
	        <button class="cur-chip" type="button" data-cur="AUD">AUD $</button>
	        <button class="cur-chip" type="button" data-cur="AED">AED د.إ</button>
	      </div>

	      <div class="eng-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:0 26px">
	        <div class="eng-fld">
	          <div class="top"><label for="tc_fly"><?php esc_html_e( 'بلیت رفت‌وبرگشت', 'uid-theme' ); ?></label><output id="tc_fly_v">۹۰۰</output></div>
	          <div class="calc-slider"><input id="tc_fly" type="range" min="200" max="4000" step="50" value="900"></div>
	        </div>
	        <div class="eng-fld">
	          <div class="top"><label for="tc_days"><?php esc_html_e( 'تعداد روز سفر', 'uid-theme' ); ?></label><output id="tc_days_v">۱۰</output></div>
	          <div class="calc-slider"><input id="tc_days" type="range" min="3" max="45" step="1" value="10"></div>
	        </div>
	        <div class="eng-fld">
	          <div class="top"><label for="tc_stay"><?php esc_html_e( 'هزینه اقامت و ایاب‌وذهاب روزانه', 'uid-theme' ); ?></label><output id="tc_stay_v">۶۰</output></div>
	          <div class="calc-slider"><input id="tc_stay" type="range" min="0" max="400" step="10" value="60"></div>
	        </div>
	        <div class="eng-fld">
	          <div class="top"><label for="tc_wage"><?php esc_html_e( 'درآمد روزانه‌ای که از دست می‌دهید', 'uid-theme' ); ?></label><output id="tc_wage_v">۱۵۰</output></div>
	          <div class="calc-slider"><input id="tc_wage" type="range" min="0" max="1200" step="10" value="150"></div>
	        </div>
	      </div>

	      <p class="eng-hint" style="margin-block-start:2px"><?php esc_html_e( 'هزینه ویزا، بیمه مسافرتی و مرخصی بدون حقوق در این برآورد لحاظ نشده است؛ عدد واقعی معمولاً از این بیشتر است.', 'uid-theme' ); ?></p>

	      <div class="eng-out2">
	        <div><span class="k"><?php esc_html_e( 'مجموع هزینه سفر', 'uid-theme' ); ?></span><span class="v hi" id="tc_total">—</span></div>
	        <div><span class="k"><?php esc_html_e( 'معادل هر روز سفر', 'uid-theme' ); ?></span><span class="v" id="tc_perday">—</span></div>
	        <div><span class="k"><?php esc_html_e( 'زمانی که صرف می‌کنید', 'uid-theme' ); ?></span><span class="v ok" id="tc_time">—</span></div>
	      </div>

	      <div class="btn-row" style="margin-block-start:20px">
	        <button class="btn btn-cta" data-open-modal>
	          <?php echo uid_sa_submit_icon(); ?>
	          <?php esc_html_e( 'همین را آنلاین انجام بدهم', 'uid-theme' ); ?></button>
	        <a class="btn btn-ghost-d" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sa_phone_icon(); ?>
	          <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	      </div>
	      <p class="tiny" style="margin-block-start:12px;color:#8FA3C4"><?php esc_html_e( 'همه ارقام بالا ورودی شماست. یوآیدی هیچ عددی به این محاسبه اضافه نمی‌کند.', 'uid-theme' ); ?></p>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۵) مسیریاب یک‌سوالی (محتوای سه گزینه در JS هاردکد است)
 * ===================================================================== */
function uid_render_section_sarouter() {
	?>
	<section class="sec" style="padding-block:clamp(44px,5vw,72px) 0">
	  <div class="sa-wrap">
	    <div class="router rv" style="max-width:940px;margin-inline:auto">
	      <div class="router-q">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
	        <b><?php echo esc_html( uid_section_val( 'sarouter', 'heading', __( 'کجای مسیر هستید؟ یک گزینه بزنید تا فقط همان بخش را ببینید.', 'uid-theme' ) ) ); ?></b>
	      </div>
	      <div class="router-opts" id="routerOpts">
	        <button class="ropt" type="button" data-r="new">
	          <b><?php esc_html_e( 'هنوز شروع نکرده‌ام', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'می‌خواهم بدانم ثنا چیست، چه مدارکی لازم است و از کجا شروع کنم.', 'uid-theme' ); ?></span></button>
	        <button class="ropt" type="button" data-r="stuck">
	          <b><?php esc_html_e( 'تا مرحله پرداخت رفته‌ام', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'کد ۱۶ رقمی را دارم ولی کارت بانکی ایرانی ندارم و نمی‌توانم پرداخت کنم.', 'uid-theme' ); ?></span></button>
	        <button class="ropt" type="button" data-r="done">
	          <b><?php esc_html_e( 'ثبت‌نام کرده‌ام، پیگیری می‌کنم', 'uid-theme' ); ?></b>
	          <span><?php esc_html_e( 'می‌خواهم برگه ثنا را استعلام یا اطلاعاتم را اصلاح کنم.', 'uid-theme' ); ?></span></button>
	      </div>
	      <div class="router-out" id="routerOut">
	        <b id="routerOutT"></b>
	        <p id="routerOutP"></p>
	        <div class="racts" id="routerActs"></div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فصل ۱) سامانه ثنا چیست
 * ===================================================================== */
function uid_default_sa_risks() {
	return array(
		array( 'title' => 'ابلاغ‌های الکترونیک به دستتان نمی‌رسد', 'text' => 'ابلاغ‌های قضایی در بستر الکترونیک ثنا انجام می‌شود. بدون حساب ثنا، کانالی برای دیدن آن‌ها ندارید.' ),
		array( 'title' => 'کارهای اداری‌تان معطل می‌ماند', 'text' => 'بسیاری از فرآیندهای قضایی و اداری، ثبت‌نام در ثنا را به‌عنوان پیش‌نیاز می‌خواهند.' ),
		array( 'title' => 'فاصله جغرافیایی تبدیل به هزینه می‌شود', 'text' => 'تنها جایگزین قدیمی، سفر به ایران یا سپردن کار به دیگری بود — هر دو گران و کند.' ),
	);
}
function uid_render_section_sawhat() {
	$tag   = uid_section_tag( 'sawhat', 'h2' );
	$risks = uid_section_val( 'sawhat', 'risks', uid_default_sa_risks() );
	if ( ! is_array( $risks ) ) $risks = array();
	?>
	<section class="sec" id="what" data-fold data-fold-hot data-fold-min="<?php echo esc_attr( uid_section_val( 'sawhat', 'fold_min', __( '۲ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'sawhat', 'fold_title', __( 'سامانه ثنا چیست و چرا از خارج از کشور مهم است', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'sawhat', 'fold_teaser', __( 'سامانه ابلاغ الکترونیک قضایی — و اینکه بدون آن چه چیزی از دستتان می‌رود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'sawhat', 'eyebrow', __( 'معرفی سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sawhat', 'heading', __( 'ثنا، سامانه ابلاغ الکترونیک قضایی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo wp_kses( uid_section_val( 'sawhat', 'text', __( 'سامانه ثنا (سامانه ثبت نام الکترونیک قضایی) بستری است که قوه قضائیه از طریق آن با اشخاص در ارتباط است. تا زمانی که در ثنا ثبت‌نام نکرده باشید، به این بستر دسترسی ندارید — و این برای کسی که خارج از ایران زندگی می‌کند یعنی تمام کارهای قضایی‌اش به یک وکیل و یک سفر گره می‌خورد. قوه قضاییه با در نظر گرفتن این نیاز، سامانه ثنا اینترنشنال را به آدرس <span class="lat">international.adliran.ir</span> راه‌اندازی کرده است.', 'uid-theme' ) ), array( 'span' => array( 'class' => array() ) ) ); ?></p>
	    </div>

	    <div class="risks rv">
	      <?php foreach ( $risks as $i => $r ) : ?>
	      <div class="risk">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sa_risk_icon_svg( $i ), array( 'path' => array( 'd' => true ) ) ); ?></svg></span>
	        <b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $r['text'] ?? '' ); ?></p>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <p class="swipe-hint" style="margin-block-start:12px">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
	      <?php esc_html_e( 'برای دیدن بقیه موارد، بکشید', 'uid-theme' ); ?></p>

	    <div class="tgcard rv" style="margin-block-start:26px">
	      <span class="ic"><?php echo uid_sa_telegram_icon(); ?></span>
	      <span class="tx">
	        <b><?php echo esc_html( uid_section_val( 'sawhat', 'tg_heading', __( 'سوال دارید؟ با کارشناسان بخش ثنای خارج از کشور یوآیدی حرف بزنید', 'uid-theme' ) ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'sawhat', 'tg_text', __( 'در صورت نیاز به پشتیبانی، راهنمایی یا کسب اطلاعات بیشتر، از طریق پیام‌رسان تلگرام در ارتباط باشید.', 'uid-theme' ) ) ); ?></span>
	      </span>
	      <a class="id" href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener">@uid_support</a>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۲) راهنمای کامل ۲۱ مرحله (ریلی) — محتوای مراحل در JS هاردکد است
 * ===================================================================== */
function uid_render_section_sasteps() {
	$tag = uid_section_tag( 'sasteps', 'h2' );
	?>
	<section class="sec" id="steps" data-fold data-fold-hot data-fold-min="<?php echo esc_attr( uid_section_val( 'sasteps', 'fold_min', __( '۴ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'sasteps', 'fold_title', __( 'راهنمای کامل ۲۱ مرحله ثبت‌نام', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'sasteps', 'fold_teaser', __( 'از ورود به سامانه تا صدور برگه ثنا — قدم به قدم', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'sasteps', 'eyebrow', __( 'راهنمای گام‌به‌گام', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sasteps', 'heading', __( 'نحوه ثبت‌نام ثنا برای افراد خارج از کشور', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sasteps', 'text', __( 'مراحل ثبت‌نام سامانه ثنا برای ایرانیان خارج از کشور دقیقاً همین‌هاست. با نوار پایین بین مراحل جابه‌جا شوید یا یکی از چهار فاز را انتخاب کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="reel rv" id="reel">
	      <div class="reel-top">
	        <div class="reel-phbar" id="reelPh" role="tablist" aria-label="<?php esc_attr_e( 'فازهای ثبت‌نام', 'uid-theme' ); ?>">
	          <button class="reel-ph on" type="button" data-ph="0" role="tab" aria-selected="true"><?php esc_html_e( '۱–۴ ورود به سامانه', 'uid-theme' ); ?></button>
	          <button class="reel-ph" type="button" data-ph="4" role="tab" aria-selected="false"><?php esc_html_e( '۵–۱۳ اطلاعات و احراز هویت', 'uid-theme' ); ?></button>
	          <button class="reel-ph pay" type="button" data-ph="13" role="tab" aria-selected="false"><?php esc_html_e( '۱۴–۱۷ پرداخت با یوآیدی', 'uid-theme' ); ?></button>
	          <button class="reel-ph" type="button" data-ph="17" role="tab" aria-selected="false"><?php esc_html_e( '۱۸–۲۱ تکمیل ثبت‌نام', 'uid-theme' ); ?></button>
	        </div>
	        <div class="reel-scrub" id="reelScrub" aria-hidden="true"></div>
	      </div>

	      <div class="reel-body">
	        <div class="reel-main">
	          <span class="reel-eye" id="reelEye">STEP 01</span>
	          <h3 id="reelTitle"></h3>
	          <p id="reelText"></p>
	          <div id="reelNote"></div>
	        </div>
	        <div class="reel-nav">
	          <button type="button" id="reelPrev" aria-label="<?php esc_attr_e( 'مرحله قبل', 'uid-theme' ); ?>">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	          <span class="reel-count" id="reelCount">۱ / ۲۱</span>
	          <button type="button" id="reelNext" aria-label="<?php esc_attr_e( 'مرحله بعد', 'uid-theme' ); ?>">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	        </div>
	      </div>

	      <div class="reel-foot">
	        <span class="rf-tx"><?php echo esc_html( uid_section_val( 'sasteps', 'foot_text', __( 'مرحله ۱۴ تا ۱۷ همان بخشی است که یوآیدی برایتان انجام می‌دهد.', 'uid-theme' ) ) ); ?></span>
	        <button class="btn btn-cta btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'sasteps', 'btn_text', __( 'ثبت سفارش پرداخت ارزی', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	    <div class="secbox rv" style="margin-block-start:20px;background:var(--n50);border-color:var(--n200)">
	      <div class="ic" style="color:var(--navy)"><?php echo uid_sa_info_icon(); ?></div>
	      <div><p style="color:var(--n600)"><?php esc_html_e( 'متن دقیق هر یک از ۲۱ مرحله در فایل قالب صفحه هاردکد شده و از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/sana-abroad-page.php و assets/js/sana-abroad-page.js مراجعه کنید).', 'uid-theme' ); ?></p></div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۳) مدارک و اطلاعات لازم
 * ===================================================================== */
function uid_default_sa_docs() {
	return array(
		array( 'title' => 'کد ملی', 'text' => 'کد ملی ده رقمی، همان که در مرحله پنجم از شما خواسته می‌شود.' ),
		array( 'title' => 'تاریخ تولد', 'text' => 'تاریخ تولد دقیق مطابق شناسنامه.' ),
		array( 'title' => 'شماره سریال شناسنامه', 'text' => 'و در مرحله هشتم، بقیه اطلاعات شناسنامه‌ای.' ),
		array( 'title' => 'آیدی پیام‌رسان آی‌گپ', 'text' => 'در نسخه بین‌المللی، به‌جای شماره تلفن همراه و ثابت ایرانی، آیدی آی‌گپ وارد می‌شود. کد تایید هم به همین آیدی می‌آید.' ),
		array( 'title' => 'آدرس محل سکونت یا محل کار', 'text' => 'توجه داشته باشید که آدرس محل سکونت شما همان آدرسی است که اکنون در خارج از کشور در آن سکونت دارید.' ),
		array( 'title' => 'میزان تحصیلات و شغل', 'text' => 'در مرحله یازدهم، پیش از دکمه «ثبت اطلاعات اولیه».' ),
		array( 'title' => 'یک کارت معتبر بین‌المللی', 'text' => 'ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی، برای پرداخت ارزی هزینه سرویس.' ),
		array( 'title' => 'یک ایمیل در دسترس', 'text' => 'پس از انجام پرداخت، لینک ادامه کار از طریق ایمیل در اختیارتان قرار می‌گیرد.' ),
	);
}
function uid_render_section_sadocs() {
	$tag  = uid_section_tag( 'sadocs', 'h2' );
	$docs = uid_section_val( 'sadocs', 'items', uid_default_sa_docs() );
	if ( ! is_array( $docs ) ) $docs = array();
	?>
	<section class="sec" id="docs" data-fold data-fold-hot data-fold-min="<?php echo esc_attr( uid_section_val( 'sadocs', 'fold_min', __( '۲ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'sadocs', 'fold_title', __( 'مدارک و اطلاعات لازم', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'sadocs', 'fold_teaser', __( 'قبل از شروع، این هفت مورد را کنار دستتان بگذارید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'sadocs', 'eyebrow', __( 'پیش از شروع', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sadocs', 'heading', __( 'چه چیزهایی لازم دارید؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sadocs', 'text', __( 'ایرانیانی که در خارج از کشور اقامت دارند، جهت ثبت‌نام در سامانه ثنا کافی است اطلاعات زیر را در دسترس داشته باشند. نیازی به مراجعه حضوری، ارسال پستی مدارک یا حضور در سفارت نیست.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="docs rv">
	      <?php foreach ( $docs as $i => $doc ) :
	        $warm = in_array( $i, array( 3, 6 ), true );
	      ?>
	      <div class="doc"><span class="ic<?php echo $warm ? ' warm' : ''; ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sa_doc_icon_svg( $i ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></span>
	        <span class="tx"><b><?php echo esc_html( $doc['title'] ?? '' ); ?></b><span><?php echo esc_html( $doc['text'] ?? '' ); ?></span></span></div>
	      <?php endforeach; ?>
	    </div>
	    <p class="swipe-hint" style="margin-block-start:12px">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
	      <?php esc_html_e( 'برای دیدن بقیه موارد، بکشید', 'uid-theme' ); ?></p>

	    <div class="callband rv" style="margin-block-start:26px">
	      <div class="ic"><?php echo uid_sa_info_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'sadocs', 'cta_heading', __( 'مطمئن نیستید مدارکتان کافی است؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'sadocs', 'cta_text', __( 'قبل از اینکه وقت بگذارید، یک تماس کوتاه با کارشناس یوآیدی بگیرید تا با هم چک کنیم.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <a class="btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="btn btn-ghost-d" data-open-modal><?php esc_html_e( 'مشاوره رایگان', 'uid-theme' ); ?></button>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۴) آی‌گپ به‌جای شماره تلفن ایرانی
 * ===================================================================== */
function uid_default_sa_igap_steps() {
	return array(
		array( 'title' => 'در آی‌گپ ثبت‌نام کنید', 'text' => 'برای دریافت آیدی آی‌گپ، کافی است که در این پلتفرم ثبت‌نام کرده باشید. همین یک قدم، شرط لازم را برایتان فراهم می‌کند.' ),
		array( 'title' => 'آیدی را در فرم ثنا وارد کنید', 'text' => 'در نسخه بین‌المللی، شما می‌توانید به‌جای شماره تلفن همراه و ثابت، آیدی پیام‌رسان آی‌گپ خود را وارد نمایید.' ),
		array( 'title' => 'کد تایید را دریافت کنید', 'text' => 'یک کد از طرف ثنا به آیدی که درج کرده‌اید ارسال می‌شود؛ کد را در قسمت مربوطه وارد می‌کنید و به مرحله بعد می‌روید.' ),
	);
}
function uid_render_section_saigap() {
	$tag   = uid_section_tag( 'saigap', 'h2' );
	$steps = uid_section_val( 'saigap', 'steps', uid_default_sa_igap_steps() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" id="igap" data-fold data-fold-min="<?php echo esc_attr( uid_section_val( 'saigap', 'fold_min', __( '۲ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'saigap', 'fold_title', __( 'آی‌گپ به‌جای شماره تلفن ایرانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'saigap', 'fold_teaser', __( 'جزئیاتی که بیشترین سوال را ایجاد می‌کند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'saigap', 'eyebrow', __( 'نکته کلیدی نسخه بین‌المللی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'saigap', 'heading', __( 'شماره تلفن ایرانی ندارید؟ لازم نیست داشته باشید.', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo wp_kses( uid_section_val( 'saigap', 'text', __( 'بزرگ‌ترین مانع فنی ثبت‌نام از خارج از کشور این بود که سامانه، شماره تلفن همراه و ثابت ایرانی می‌خواست. در نسخه <span class="lat">international.adliran.ir</span> این مشکل حل شده است.', 'uid-theme' ) ), array( 'span' => array( 'class' => array() ) ) ); ?></p>
	    </div>

	    <div class="paylane rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="plane-cell<?php echo 1 === $i ? ' mid' : ''; ?>">
	        <span class="step"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span>
	        <b><?php echo esc_html( $s['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $s['text'] ?? '' ); ?></p>
	      </div>
	      <?php endforeach; ?>
	    </div>

	    <div class="reel-note rv" style="margin-block-start:20px;max-width:840px;margin-inline:auto">
	      <?php echo uid_sa_warn_icon(); ?>
	      <span><?php echo esc_html( uid_section_val( 'saigap', 'note_text', __( 'اگر در همین مرحله به مشکل خوردید، لازم نیست از اول شروع کنید — کارشناسان یوآیدی از طریق تلگرام @uid_support یا شماره پشتیبانی همراهی‌تان می‌کنند.', 'uid-theme' ) ) ); ?></span>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۵) درگاه پرداخت ارزی یوآیدی (قلب سرویس)
 * ===================================================================== */
function uid_default_sa_pay_steps() {
	return array(
		array( 'step' => 'STEP 14', 'title' => 'کد ۱۶ رقمی را بردارید', 'text' => 'وارد صفحه پرداخت سامانه ثنا شوید و کد ۱۶ رقمی نمایش داده شده را کپی کنید. این کد، کلید تسویه پرونده شماست.' ),
		array( 'step' => 'STEP 15–16', 'title' => 'در صفحه پرداخت یوآیدی، ارزی بپردازید', 'text' => 'کلیه اطلاعات درخواست‌شده را وارد نمایید تا عملیات پرداخت ریالی شما به‌صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع‌رسانی شود.' ),
		array( 'step' => 'STEP 17–20', 'title' => 'لینک را در ایمیل بگیرید و برگردید', 'text' => 'بعد از صورت گرفتن پرداخت، لینکی از طریق ایمیل در اختیارتان قرار داده می‌شود؛ روی آن کلیک می‌کنید، «به‌روزرسانی شرایط پرداخت» را می‌زنید و پیغام تایید پرداخت هزینه احراز هویت را می‌بینید.' ),
	);
}
function uid_render_section_sapay() {
	$tag   = uid_section_tag( 'sapay', 'h2' );
	$steps = uid_section_val( 'sapay', 'steps', uid_default_sa_pay_steps() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" id="pay" data-fold data-fold-hot data-fold-min="<?php echo esc_attr( uid_section_val( 'sapay', 'fold_min', __( '۳ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'sapay', 'fold_title', __( 'درگاه پرداخت ارزی یوآیدی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'sapay', 'fold_teaser', __( 'دقیقاً همان کاری که یوآیدی برای شما انجام می‌دهد', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'sapay', 'eyebrow', __( 'قلب سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sapay', 'heading', __( 'شما ارزی می‌پردازید، یوآیدی ریالی را داخل ایران تسویه می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sapay', 'text', __( 'در صورتی که قصد ثبت‌نام در سامانه ثنا را دارید و در خارج از ایران هستید، می‌توانید به آسانی با استفاده از درگاه پرداخت ارزی یوآیدی، هزینه سرویس را به‌صورت ارزی و با ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی پرداخت نمائید و فرآیند ثبت‌نام خود را به‌صورت آنلاین تکمیل کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="paylane rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="plane-cell<?php echo 1 === $i ? ' mid' : ''; ?>">
	        <span class="step"><?php echo esc_html( $s['step'] ?? '' ); ?></span>
	        <b><?php echo esc_html( $s['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $s['text'] ?? '' ); ?></p>
	        <?php if ( 1 === $i ) : ?>
	        <div class="cards-row">
	          <span class="cardchip">Visa</span>
	          <span class="cardchip">Mastercard</span>
	          <span class="cardchip"><?php esc_html_e( 'سایر کارت‌های معتبر بین‌المللی', 'uid-theme' ); ?></span>
	        </div>
	        <?php endif; ?>
	      </div>
	      <?php endforeach; ?>
	    </div>

	    <div class="callband urgent rv" style="margin-block-start:26px">
	      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M14.5 15.5l2 2 4-4.5"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'sapay', 'cta_heading', __( 'کد ۱۶ رقمی را همین حالا دارید؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'sapay', 'cta_text', __( 'فرم ثبت سفارش را باز کنید، کد را وارد کنید و باقی کار را به کارشناسان ما بسپارید.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="btn btn-cta" data-open-modal><?php esc_html_e( 'باز کردن فرم ثبت سفارش', 'uid-theme' ); ?></button>
	        <a class="btn btn-ghost-d" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	      </div>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۶) تیم متخصص
 * ===================================================================== */
function uid_default_sa_crew() {
	return array(
		array( 'title' => 'کارگزار مورد تایید قوه قضائیه', 'text' => 'خدمت ثبت‌نام و احراز هویت ایرانیان خارج از کشور در سامانه ثنا، برای اولین بار در ایران توسط یوآیدی به‌صورت آنلاین ارائه شده است.', 'kpi' => 'اپراتور اول' ),
		array( 'title' => 'کارشناسان اختصاصی بخش ثنای خارج از کشور', 'text' => 'در صورت نیاز به پشتیبانی، راهنمایی و یا کسب اطلاعات بیشتر، از طریق پیام‌رسان تلگرام با کارشناسان این بخش در ارتباط باشید.', 'kpi' => '@uid_support' ),
		array( 'title' => 'پشتیبانی تلفنی از داخل ایران', 'text' => 'اگر ترجیح می‌دهید صحبت کنید، شماره پشتیبانی یوآیدی در ساعات کاری ایران پاسخگوی شماست.', 'kpi' => '02166123290' ),
		array( 'title' => 'تخصص احراز هویت، نه واسطه‌گری', 'text' => 'یوآیدی سازنده زیرساخت احراز هویت آنلاین است: تشخیص زنده‌بودن، تطبیق چهره، سجام، ثنا و وب‌سرویس‌های استعلام هویتی و مالی.', 'kpi' => 'از ۱۳۹۶' ),
	);
}
function uid_render_section_sateam() {
	$tag  = uid_section_tag( 'sateam', 'h2' );
	$crew = uid_section_val( 'sateam', 'crew', uid_default_sa_crew() );
	if ( ! is_array( $crew ) ) $crew = array();
	?>
	<section class="sec" id="team" data-fold data-fold-min="<?php echo esc_attr( uid_section_val( 'sateam', 'fold_min', __( '۲ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'sateam', 'fold_title', __( 'تیم متخصص و پشتیبانی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'sateam', 'fold_teaser', __( 'با چه کسانی طرف هستید و چه چیزی تعهد می‌شود', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'sateam', 'eyebrow', __( 'پشت این سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sateam', 'heading', __( 'یک تیم تخصصی، فقط برای ثنای خارج از کشور', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'sateam', 'text', __( 'یوآیدی نام تجاری شرکت دانش‌بنیان بینش هوشمند نسل پیشرو است؛ از سال ۱۳۹۶ در حوزه احراز هویت آنلاین فعال و به‌عنوان کارگزار مورد تایید سامانه‌های سجام و ثنا مشغول به کار است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="crew rv">
	      <?php foreach ( $crew as $i => $c ) :
	        $kpi_class = false !== strpos( (string) ( $c['kpi'] ?? '' ), '0' ) && preg_match( '/^\d+$/', (string) ( $c['kpi'] ?? '' ) ) ? ' mono' : '';
	      ?>
	      <div class="crew-row">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sa_crew_icon_svg( $i ), array( 'path' => array( 'd' => true ) ) ); ?></svg></span>
	        <span class="tx"><b><?php echo esc_html( $c['title'] ?? '' ); ?></b>
	          <span><?php echo esc_html( $c['text'] ?? '' ); ?></span></span>
	        <span class="kpi<?php echo esc_attr( $kpi_class ); ?>"><?php echo esc_html( $c['kpi'] ?? '' ); ?></span>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۷) پیگیری، استعلام و دریافت برگه ثنا
 * ===================================================================== */
function uid_default_sa_track() {
	return array(
		array( 'title' => 'به سامانه بین‌المللی برگردید', 'text' => 'برای استعلام برگه ثنا می‌توانید مجدد به آدرس international.adliran.ir مراجعه کنید.' ),
		array( 'title' => '«چاپ اطلاعات ثبت نام» را بزنید', 'text' => 'در قسمت «تغییر اطلاعات»، گزینه «چاپ اطلاعات ثبت نام» را انتخاب نمایید تا اطلاعات ثبت‌شده شما نمایش داده شود.' ),
	);
}
function uid_render_section_satrack() {
	$tag   = uid_section_tag( 'satrack', 'h2' );
	$cards = uid_section_val( 'satrack', 'items', uid_default_sa_track() );
	if ( ! is_array( $cards ) ) $cards = array();
	?>
	<section class="sec" id="track" data-fold data-fold-min="<?php echo esc_attr( uid_section_val( 'satrack', 'fold_min', __( '۱ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'satrack', 'fold_title', __( 'پیگیری، استعلام و دریافت برگه ثنا', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'satrack', 'fold_teaser', __( 'بعد از ثبت‌نام، سراغ اطلاعاتتان چطور بروید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'satrack', 'eyebrow', __( 'بعد از ثبت‌نام', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'satrack', 'heading', __( 'چطور برگه ثنا را استعلام کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'satrack', 'text', __( 'ثبت‌نام که تمام شد، دسترسی به اطلاعات ثبت‌شده‌تان با دو کلیک ممکن است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="track rv">
	      <?php foreach ( $cards as $i => $c ) : ?>
	      <div class="track-card">
	        <span class="n"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span>
	        <b><?php echo esc_html( $c['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $c['text'] ?? '' ); ?></p>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۸) سرویس‌های مرتبط
 * ===================================================================== */
function uid_default_sa_xsell() {
	return array(
		array( 'icon' => 'sana',  'title' => 'احراز هویت ثنا (داخل ایران)', 'text' => 'اگر داخل ایران هستید، مسیر ثبت‌نام و احراز هویت ثنا متفاوت و ساده‌تر است.', 'url' => '/sana/' ),
		array( 'icon' => 'sejam', 'title' => 'احراز هویت سجام', 'text' => 'برای فعالیت در بازار سرمایه، احراز هویت سجام یوآیدی مسیر آنلاین شماست.', 'url' => '/sejamauthentication/' ),
		array( 'icon' => 'pwa',   'title' => 'یوآیدی‌پلاس (PWA)', 'text' => 'برای کسب‌وکارها: برون‌سپاری کامل احراز هویت آنلاین کاربران، بدون توسعه اپلیکیشن.', 'url' => '/uid-plus/' ),
	);
}
function uid_render_section_saxsell() {
	$tag   = uid_section_tag( 'saxsell', 'h2' );
	$items = uid_section_val( 'saxsell', 'items', uid_default_sa_xsell() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="xsell" data-fold data-fold-min="<?php echo esc_attr( uid_section_val( 'saxsell', 'fold_min', __( '۱ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'saxsell', 'fold_title', __( 'سرویس‌های مرتبط یوآیدی', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'saxsell', 'fold_teaser', __( 'اگر داخل ایران هستید یا کسب‌وکار دارید، این‌ها به کارتان می‌آید', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'saxsell', 'eyebrow', __( 'سرویس‌های مرتبط', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'saxsell', 'heading', __( 'اگر مورد شما چیز دیگری است', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="xsell rv" data-rail="xs">
	      <?php foreach ( $items as $it ) : ?>
	      <a class="xtile" href="<?php echo esc_url( $it['url'] ?? '#' ); ?>">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sa_xsell_icon_svg( $it['icon'] ?? 'sana' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></span>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	        <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        <span class="go"><?php esc_html_e( 'مشاهده سرویس', 'uid-theme' ); ?> <?php echo uid_sa_arrow_icon(); ?></span>
	      </a>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="xs"></div>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * فصل ۹) سوالات متداول
 * ===================================================================== */
function uid_default_sa_faq() {
	return array(
		array( 'question' => 'مدارک لازم برای ثبت ثنا در خارج از کشور، چیست؟', 'answer' => 'ایرانیانی که در خارج از کشور اقامت دارند، جهت ثبت‌نام در سامانه ثنا کافی است اطلاعاتی مثل کد ملی، شماره شناسنامه، آیدی آی‌گپ و برخی اطلاعات دیگر — تاریخ تولد، اطلاعات شناسنامه‌ای، آدرس محل سکونت در خارج از کشور و میزان تحصیلات و شغل — را وارد کنند.' ),
		array( 'question' => 'آیا امکان ثبت نام در سامانه ثنا از خارج از کشور وجود دارد؟', 'answer' => 'بله. قوه قضاییه با در نظر گرفتن این نیاز ایرانیان، سامانه ثنا اینترنشنال به آدرس international.adliran.ir را راه‌اندازی کرده است. اگر تابعیت ایرانی داشته باشید، ثبت‌نام سامانه ثنا برای ایرانیان خارج از کشور به‌راحتی از طریق سامانه ثبت‌نام الکترونیک قضایی قابل انجام بوده و پرداخت آن نیز به‌صورت آنلاین امکان‌پذیر است.' ),
		array( 'question' => 'امکان پرداخت هزینه ثبت نام ثنا در خارج از کشور وجود دارد؟', 'answer' => 'در صورتی که قصد ثبت‌نام در سامانه ثنا را دارید و در خارج از ایران هستید می‌توانید به آسانی با استفاده از درگاه پرداخت ارزی یوآیدی، هزینه سرویس را به‌صورت ارزی پرداخت و فرآیند ثبت‌نام خود را به‌صورت آنلاین تکمیل نمایید.' ),
		array( 'question' => 'جهت پرداخت هزینه احراز هویت، باید چه کار کنیم؟', 'answer' => 'وارد صفحه پرداخت سامانه ثنا شوید، کد ۱۶ رقمی نمایش داده شده را کپی کنید و سپس فرم ثبت سفارش سامانه ثنا با یوآیدی (ویژه ایرانیان خارج از کشور) را تکمیل نمایید تا عملیات پرداخت ریالی شما به‌صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع‌رسانی شود.' ),
		array( 'question' => 'ثبت نام ثنا برای ایرانیان خارج چگونه قابل استعلام و پیگیری است؟', 'answer' => 'برای استعلام برگه ثنا می‌توانید مجدد به آدرس international.adliran.ir مراجعه کنید و در قسمت «تغییر اطلاعات»، گزینه «چاپ اطلاعات ثبت نام» را انتخاب نمایید.' ),
		array( 'question' => 'با چه کارت‌هایی می‌توانم هزینه را پرداخت کنم؟', 'answer' => 'از طریق درگاه پرداخت ارزی یوآیدی می‌توانید هزینه سرویس را به‌صورت ارزی و با ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی پرداخت نمایید.' ),
		array( 'question' => 'آیدی آی‌گپ چیست و چرا در فرم خواسته می‌شود؟', 'answer' => 'در نسخه جدید international.adliran.ir، شما می‌توانید به جای شماره تلفن همراه و ثابت، آیدی پیام‌رسان آی‌گپ خود را وارد نمایید. برای دریافت آیدی آی گپ، کافی است که در این پلتفرم ثبت نام کرده باشید. کد تایید ثنا نیز به همین آیدی ارسال می‌شود.' ),
		array( 'question' => 'برای این کار باید به ایران سفر کنم؟', 'answer' => 'خیر. یوآیدی به‌عنوان اپراتور اول احراز هویت ایران و کارگزار مورد تایید قوه قضائیه، برای اولین بار در ایران خدمت ثبت نام و احراز هویت ایرانیان خارج از کشور در سامانه ثنا را به‌صورت آنلاین ارائه می‌نماید؛ پرداخت ریالی مربوطه هم در داخل خاک ایران توسط یوآیدی انجام می‌شود.' ),
	);
}
function uid_render_section_safaq() {
	$tag   = uid_section_tag( 'safaq', 'h2' );
	$items = uid_section_val( 'safaq', 'items', uid_default_sa_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" data-fold data-fold-hot data-fold-min="<?php echo esc_attr( uid_section_val( 'safaq', 'fold_min', __( '۳ دقیقه', 'uid-theme' ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( 'safaq', 'fold_title', __( 'سوالات متداول', 'uid-theme' ) ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( 'safaq', 'fold_teaser', __( 'هشت سوالی که بیشترین تکرار را دارند', 'uid-theme' ) ) ); ?>">
	  <div class="fold-body"><div class="sa-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <span class="eyebrow"><i></i><?php echo esc_html( uid_section_val( 'safaq', 'eyebrow', __( 'سوالات متداول', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'safaq', 'heading', __( 'سوالات متداول ثبت‌نام سامانه ثنا برای ایرانیان خارج از کشور', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv" style="max-width:880px;margin-inline:auto">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i"><button class="faq-q" aria-expanded="false">
	        <?php echo esc_html( $it['question'] ?? '' ); ?>
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div></div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div></div></div>
	  </section>
	<?php
}

/* =====================================================================
 * مشخصات سرویس (پنل تیره)
 * ===================================================================== */
function uid_default_sa_adv() {
	return array(
		array( 'icon' => 'shield',  'value' => 'اپراتور اول احراز هویت ایران', 'text' => 'کارگزار مورد تایید قوه قضائیه، ارائه‌دهنده این خدمت برای اولین بار در ایران', 'percent' => '100', 'feat' => '1', 'wide' => '' ),
		array( 'icon' => 'card',    'value' => 'پرداخت ارزی', 'text' => 'ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی، از طریق درگاه پرداخت ارزی یوآیدی', 'percent' => '98', 'feat' => '', 'wide' => '' ),
		array( 'icon' => 'pin',     'value' => 'تسویه داخل ایران', 'text' => 'عملیات پرداخت ریالی به‌صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع‌رسانی می‌شود', 'percent' => '100', 'feat' => '', 'wide' => '' ),
		array( 'icon' => 'support', 'value' => 'پشتیبانی اختصاصی', 'text' => 'کارشناسان بخش ثنای خارج از کشور یوآیدی، از طریق تلگرام @uid_support و شماره پشتیبانی', 'percent' => '96', 'feat' => '', 'wide' => '1' ),
		array( 'icon' => 'mail',    'value' => 'لینک ادامه کار در ایمیل', 'text' => 'بعد از صورت گرفتن پرداخت، لینکی از طریق ایمیل در اختیارتان قرار می‌گیرد تا ثبت‌نام را تمام کنید', 'percent' => '94', 'feat' => '', 'wide' => '1' ),
	);
}
function uid_render_section_saadv() {
	$tag   = uid_section_tag( 'saadv', 'h2' );
	$items = uid_section_val( 'saadv', 'items', uid_default_sa_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="dark sec" id="adv">
	  <div class="sa-wrap">
	    <div class="sec-head mid rv">
	      <span class="eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'saadv', 'eyebrow', __( 'مشخصات سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'saadv', 'heading', __( 'پنج چیزی که با ثبت سفارش به دست می‌آورید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'saadv', 'text', __( 'هر مورد زیر مستقیماً از شرح خدمت ثبت‌نام ثنای ایرانیان خارج از کشور یوآیدی آمده است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="opanel rv">
	      <?php foreach ( $items as $it ) :
	        $classes = 'ostat';
	        if ( ! empty( $it['feat'] ) ) $classes .= ' feat';
	        if ( ! empty( $it['wide'] ) ) $classes .= ' w2';
	        $pct = absint( $it['percent'] ?? 90 );
	      ?>
	      <div class="<?php echo esc_attr( $classes ); ?>">
	        <div class="oic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sa_adv_icon_svg( $it['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	        <span class="ov txt"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	        <span class="ol"><?php echo esc_html( $it['text'] ?? '' ); ?></span>
	        <span class="ometer" style="--p:<?php echo esc_attr( $pct ); ?>%"><i></i></span>
	      </div>
	      <?php endforeach; ?>
	    </div>

	    <div class="opanel-foot rv">
	      <span><?php echo esc_html( uid_section_val( 'saadv', 'foot_text', __( 'سوالی مانده که اینجا جوابش نیست؟', 'uid-theme' ) ) ); ?></span>
	      <button class="btn btn-white btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'saadv', 'btn_text', __( 'مشاوره رایگان بگیرید', 'uid-theme' ) ) ); ?></button>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * نحوه ثبت سفارش (۳ گام)
 * ===================================================================== */
function uid_default_sa_order() {
	return array(
		array( 'title' => 'فرم ثبت سفارش را پر کنید', 'text' => 'نام و نام خانوادگی، آیدی تلگرام و در صورتی که به مرحله پرداخت رسیده‌اید، کد ۱۶ رقمی صفحه پرداخت ثنا. کمتر از یک دقیقه وقت می‌گیرد.' ),
		array( 'title' => 'کارشناس با شما تماس می‌گیرد', 'text' => 'کارشناس بخش ثنای خارج از کشور یوآیدی، وضعیت پرونده‌تان را بررسی می‌کند و مسیر پرداخت ارزی را در اختیارتان می‌گذارد.' ),
		array( 'title' => 'پرداخت می‌کنید، ما تسویه می‌کنیم', 'text' => 'با ویزا یا مستر کارت پرداخت می‌کنید، پرداخت ریالی داخل ایران انجام می‌شود و لینک ادامه کار به ایمیل شما می‌رسد.' ),
	);
}
function uid_render_section_saorder() {
	$tag   = uid_section_tag( 'saorder', 'h2' );
	$steps = uid_section_val( 'saorder', 'steps', uid_default_sa_order() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" id="order" style="padding-block-end:0">
	  <div class="sa-wrap">
	    <div class="sec-head mid rv">
	      <span class="eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'saorder', 'eyebrow', __( 'نحوه ثبت سفارش', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'saorder', 'heading', __( 'از فرم تا تایید پرداخت، سه قدم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'saorder', 'text', __( 'لازم نیست کارت بانکی ایرانی داشته باشید، لازم نیست کسی را در ایران زحمت بدهید و لازم نیست سفر کنید.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="flow3 rv">
	      <?php foreach ( $steps as $i => $s ) : ?>
	      <div class="fl-card">
	        <span class="fl-num"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></span>
	        <h4><?php echo esc_html( $s['title'] ?? '' ); ?></h4>
	        <p><?php echo esc_html( $s['text'] ?? '' ); ?></p>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم ثبت سفارش)
 * ===================================================================== */
function uid_render_section_salead() {
	$tag         = uid_section_tag( 'salead', 'h2' );
	$trust_raw   = uid_section_val( 'salead', 'trust', "مشاوره رایگان\nبدون نیاز به کارت ایرانی\nپشتیبانی تلگرام" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$stage_raw   = uid_section_val( 'salead', 'stage_options', "هنوز شروع نکرده‌ام\nدر حال تکمیل اطلاعات هستم\nبه مرحله پرداخت رسیده‌ام و کد ۱۶ رقمی را دارم\nپرداخت شده، برای ادامه راهنمایی می‌خواهم" );
	$stages      = array_filter( array_map( 'trim', explode( "\n", $stage_raw ) ) );
	$stage_keys  = array( 'new', 'mid', 'pay', 'after' );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="sa-wrap" style="padding-inline:0">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'salead', 'eyebrow', __( 'فرم ثبت سفارش', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'salead', 'heading', __( 'ثبت‌نام ثنای خود را همین امروز تمام کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'salead', 'text', __( 'فرم را پر کنید تا کارشناس بخش ثنای خارج از کشور یوآیدی با شما در ارتباط باشد: بررسی وضعیت پرونده، مسیر پرداخت ارزی و راهنمایی تا صدور برگه ثنا.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_sa_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>

	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'salead', 'form_title', __( 'فرم مشاوره و ثبت سفارش', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'salead', 'form_hint', __( 'سه فیلد، کمتر از یک دقیقه. کد ۱۶ رقمی فقط اگر به مرحله پرداخت رسیده‌اید لازم است.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <div class="cta-fields">
	              <div class="fld"><label class="sr" for="l_name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	                <input id="l_name" name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req></div>
	              <div class="fld"><label class="sr" for="l_tg"><?php esc_html_e( 'آیدی تلگرام', 'uid-theme' ); ?></label>
	                <input id="l_tg" name="telegram" type="text" placeholder="<?php esc_attr_e( 'آیدی تلگرام — مثلاً @uid_support', 'uid-theme' ); ?>" data-req></div>
	              <div class="fld"><label class="sr" for="l_stage"><?php esc_html_e( 'در چه مرحله‌ای هستید؟', 'uid-theme' ); ?></label>
	                <select id="l_stage" name="stage" data-req data-route>
	                  <option value=""><?php esc_html_e( 'در چه مرحله‌ای هستید؟', 'uid-theme' ); ?></option>
	                  <?php foreach ( $stages as $i => $s ) : ?>
	                  <option value="<?php echo esc_attr( $stage_keys[ $i ] ?? sanitize_title( $s ) ); ?>"><?php echo esc_html( $s ); ?></option>
	                  <?php endforeach; ?>
	                  <option value="ind"><?php esc_html_e( 'داخل ایران هستم', 'uid-theme' ); ?></option>
	                </select></div>

	              <div class="route-alert" id="routeAlert">
	                <?php echo uid_sa_info_icon(); ?>
	                <span><?php esc_html_e( 'این صفحه مخصوص ایرانیان مقیم خارج از کشور است. اگر داخل ایران هستید، مسیر ساده‌تری دارید: ', 'uid-theme' ); ?><a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'صفحه احراز هویت ثنا', 'uid-theme' ); ?></a>.</span>
	              </div>

	              <div class="fld"><label class="sr" for="l_code"><?php esc_html_e( 'کد ۱۶ رقمی صفحه پرداخت ثنا (اختیاری)', 'uid-theme' ); ?></label>
	                <input id="l_code" name="sanacode" type="text" inputmode="numeric"
	                       placeholder="<?php esc_attr_e( 'کد ۱۶ رقمی صفحه پرداخت ثنا (اختیاری)', 'uid-theme' ); ?>" maxlength="19"></div>

	              <button class="btn btn-cta btn-block" type="button" data-submit>
	                <?php echo uid_sa_submit_icon(); ?>
	                <?php echo esc_html( uid_section_val( 'salead', 'submit_text', __( 'ارسال و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            </div>
	            <div class="form-ok"><?php echo uid_sa_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'salead', 'success_title', __( 'سفارش شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'salead', 'success_text', __( 'کارشناس بخش ثنای خارج از کشور یوآیدی به‌زودی با شما در ارتباط خواهد بود. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	          <div class="lb-note">
	            <?php echo uid_sa_shield_icon(); ?>
	            <span><?php echo esc_html( uid_section_val( 'salead', 'note_text', __( 'اطلاعات شما فقط برای پیگیری همین سفارش استفاده می‌شود.', 'uid-theme' ) ) ); ?></span>
	          </div>
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
function uid_get_sa_page_id() {
	$page_id = (int) get_option( 'uid_sa_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_SANA_ABROAD_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_sa_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_sa_page() {
	if ( uid_get_sa_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'ثبت‌نام سامانه ثنا ویژه ایرانیان خارج از کشور', 'uid-theme' ),
		'post_name'   => 'sana-register-foreign-form',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_SANA_ABROAD_TEMPLATE );
	update_option( 'uid_sa_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_sa_slug_setting() {
	register_setting( 'uid_sa_group', 'uid_sa_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_sa_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_sa_page_slug_section', '', '__return_false', 'uid_sa_layout' );
	add_settings_field( 'uid_sa_page_slug', __( 'آدرس (اسلاگ) صفحه ثنا ایرانیان خارج از کشور', 'uid-theme' ), 'uid_field_sa_page_slug', 'uid_sa_layout', 'uid_sa_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_sa_slug_setting' );

function uid_field_sa_page_slug( $args ) {
	$page_id = uid_get_sa_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_sa_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_sa_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه ثنا ایرانیان خارج از کشور هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_sa_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_sa_page_id();

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
function uid_register_sa_settings() {
	register_setting( 'uid_sa_group', 'uid_sa_layout', array(
		'sanitize_callback' => 'uid_sanitize_sa_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_sa_layout_main', '', '__return_false', 'uid_sa_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_sa_layout', 'uid_sa_layout_main', array(
		'option_name' => 'uid_sa_layout', 'registry_fn' => 'uid_sa_sections_registry', 'layout_fn' => 'uid_get_sa_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sahero', array( 'sanitize_callback' => 'uid_sanitize_section_sahero', 'default' => array() ) );
	add_settings_section( 'uid_section_sahero_main', '', '__return_false', 'uid_section_sahero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'eyebrow', 'default' => 'اپراتور اول احراز هویت ایران · کارگزار مورد تایید قوه قضائیه' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ‌های br و mark مجازند)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'heading', 'default' => 'ثبت‌نام سامانه ثنا،<br><mark>از هر کجای دنیا</mark> که هستید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'text', 'default' => 'یوآیدی مفتخر است برای اولین بار در ایران، خدمت ثبت‌نام و احراز هویت ایرانیان خارج از کشور در سامانه ثنا را به‌صورت آنلاین ارائه نماید. هزینه سرویس را با ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی از طریق درگاه پرداخت ارزی یوآیدی می‌پردازید و پرداخت ریالی آن، به‌صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع‌رسانی می‌شود.', 'rows' => 5 ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'btn1_text', 'default' => 'ثبت سفارش و مشاوره رایگان' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sahero', 'uid_section_sahero_main', array( 'group' => 'uid_section_sahero', 'key' => 'tags', 'default' => implode( "\n", uid_default_sa_hero_tags() ), 'rows' => 5 ) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_sahero', 'uid_section_sahero_main', array( 'text' => __( 'ساعت تهران، کنسول کد ۱۶ رقمی و کپسول ۳۰ ثانیه‌ای (سمت راست هیرو) شبیه‌سازی نمایشی هستند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/sana-abroad-page.php و assets/js/sana-abroad-page.js مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- باند اعتماد کوتاه ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_satrust', array( 'sanitize_callback' => 'uid_sanitize_section_satrust', 'default' => array() ) );
	add_settings_section( 'uid_section_satrust_main', '', '__return_false', 'uid_section_satrust' );
	add_settings_field( 'items', __( 'آمار (خانه‌های باند)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_satrust', 'uid_section_satrust_main', array(
		'group' => 'uid_section_satrust', 'key' => 'items', 'default' => uid_default_sa_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'numeric', 'type' => 'select', 'label' => __( 'استایل عددی بزرگ', 'uid-theme' ), 'options' => array( '' => __( 'خیر (متن)', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );

	/* ---------------- نقطه توقف (فازها) ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sastuck', array( 'sanitize_callback' => 'uid_sanitize_section_sastuck', 'default' => array() ) );
	add_settings_section( 'uid_section_sastuck_main', '', '__return_false', 'uid_section_sastuck' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'eyebrow', 'default' => 'نقطه توقف' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'heading', 'default' => '۲۰ مرحله را خودتان می‌روید. سر یک مرحله می‌مانید.' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'text', 'default' => 'فرآیند ثبت‌نام ثنا برای ایرانیان خارج از کشور ۲۱ مرحله دارد و تقریباً همه آن‌ها را می‌توانید از هر کجای دنیا انجام دهید. اما در مرحله چهاردهم، سامانه ثنا برای انجام احراز هویت یک هزینه ریالی مطالبه می‌کند — و اینجاست که بدون کارت بانکی ایرانی، مسیر بسته می‌شود.' ) );
	add_settings_field( 'phases', __( 'فازهای نقشه ثبت‌نام (۴ فاز)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sastuck', 'uid_section_sastuck_main', array(
		'group' => 'uid_section_sastuck', 'key' => 'phases', 'default' => uid_default_sa_phases(), 'add_label' => __( 'افزودن فاز', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'pn', 'type' => 'text', 'label' => __( 'برچسب فاز (مثل PHASE 1 · گام ۱ تا ۴)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'who', 'type' => 'text', 'label' => __( 'چه کسی انجام می‌دهد', 'uid-theme' ) ),
			array( 'key' => 'mine', 'type' => 'select', 'label' => __( 'فاز اختصاصی یوآیدی (پررنگ)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'cta_heading', 'default' => 'در همان مرحله ۱۴ گیر کرده‌اید؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sastuck', 'uid_section_sastuck_main', array( 'group' => 'uid_section_sastuck', 'key' => 'cta_text', 'default' => 'کد ۱۶ رقمی را بردارید و همین حالا تماس بگیرید — کارشناس بخش ثنای خارج از کشور یوآیدی مرحله به مرحله همراهتان است.' ) );

	/* ---------------- محاسبه‌گر هزینه سفر ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sacost', array( 'sanitize_callback' => 'uid_sanitize_section_sacost', 'default' => array() ) );
	add_settings_section( 'uid_section_sacost_main', '', '__return_false', 'uid_section_sacost' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sacost', 'uid_section_sacost_main', array( 'group' => 'uid_section_sacost', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sacost', 'uid_section_sacost_main', array( 'group' => 'uid_section_sacost', 'key' => 'eyebrow', 'default' => 'گزینه دیگر روی میز' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sacost', 'uid_section_sacost_main', array( 'group' => 'uid_section_sacost', 'key' => 'heading', 'default' => 'اگر بخواهید برای همین یک کار به ایران سفر کنید، چقدر خرج برمی‌دارد؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sacost', 'uid_section_sacost_main', array( 'group' => 'uid_section_sacost', 'key' => 'text', 'default' => 'اعداد زیر را خودتان تعیین می‌کنید؛ این ماشین‌حساب هیچ آمار از پیش تعیین‌شده‌ای ندارد و فقط همان چیزی را جمع می‌زند که شما وارد کرده‌اید.' ) );
	add_settings_field( 'calc_notice', '', 'uid_field_notice', 'uid_section_sacost', 'uid_section_sacost_main', array( 'text' => __( 'منطق محاسبه‌گر هزینه سفر (اسلایدرها و تبدیل ارز) در assets/js/sana-abroad-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- مسیریاب یک‌سوالی ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sarouter', array( 'sanitize_callback' => 'uid_sanitize_section_sarouter', 'default' => array() ) );
	add_settings_section( 'uid_section_sarouter_main', '', '__return_false', 'uid_section_sarouter' );
	add_settings_field( 'heading', __( 'متن سوال', 'uid-theme' ), 'uid_field_text', 'uid_section_sarouter', 'uid_section_sarouter_main', array( 'group' => 'uid_section_sarouter', 'key' => 'heading', 'default' => 'کجای مسیر هستید؟ یک گزینه بزنید تا فقط همان بخش را ببینید.' ) );
	add_settings_field( 'router_notice', '', 'uid_field_notice', 'uid_section_sarouter', 'uid_section_sarouter_main', array( 'text' => __( 'متن سه گزینه و پاسخ هر کدام در assets/js/sana-abroad-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- فصل ۱: سامانه ثنا چیست ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sawhat', array( 'sanitize_callback' => 'uid_sanitize_section_sawhat', 'default' => array() ) );
	add_settings_section( 'uid_section_sawhat_main', '', '__return_false', 'uid_section_sawhat' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'eyebrow', 'default' => 'معرفی سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'heading', 'default' => 'ثنا، سامانه ابلاغ الکترونیک قضایی' ) );
	add_settings_field( 'text', __( 'توضیح (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'text', 'default' => 'سامانه ثنا (سامانه ثبت نام الکترونیک قضایی) بستری است که قوه قضائیه از طریق آن با اشخاص در ارتباط است. تا زمانی که در ثنا ثبت‌نام نکرده باشید، به این بستر دسترسی ندارید — و این برای کسی که خارج از ایران زندگی می‌کند یعنی تمام کارهای قضایی‌اش به یک وکیل و یک سفر گره می‌خورد. قوه قضاییه با در نظر گرفتن این نیاز، سامانه ثنا اینترنشنال را به آدرس international.adliran.ir راه‌اندازی کرده است.', 'rows' => 6 ) );
	add_settings_field( 'risks', __( 'ریسک‌های نبود ثبت‌نام (سه مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sawhat', 'uid_section_sawhat_main', array(
		'group' => 'uid_section_sawhat', 'key' => 'risks', 'default' => uid_default_sa_risks(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'tg_heading', __( 'عنوان کادر تلگرام', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'tg_heading', 'default' => 'سوال دارید؟ با کارشناسان بخش ثنای خارج از کشور یوآیدی حرف بزنید' ) );
	add_settings_field( 'tg_text', __( 'توضیح کادر تلگرام', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'tg_text', 'default' => 'در صورت نیاز به پشتیبانی، راهنمایی یا کسب اطلاعات بیشتر، از طریق پیام‌رسان تلگرام در ارتباط باشید.' ) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'fold_min', 'default' => '۲ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'fold_title', 'default' => 'سامانه ثنا چیست و چرا از خارج از کشور مهم است' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sawhat', 'uid_section_sawhat_main', array( 'group' => 'uid_section_sawhat', 'key' => 'fold_teaser', 'default' => 'سامانه ابلاغ الکترونیک قضایی — و اینکه بدون آن چه چیزی از دستتان می‌رود' ) );

	/* ---------------- فصل ۲: راهنمای ۲۱ مرحله ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sasteps', array( 'sanitize_callback' => 'uid_sanitize_section_sasteps', 'default' => array() ) );
	add_settings_section( 'uid_section_sasteps_main', '', '__return_false', 'uid_section_sasteps' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'eyebrow', 'default' => 'راهنمای گام‌به‌گام' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'heading', 'default' => 'نحوه ثبت‌نام ثنا برای افراد خارج از کشور' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'text', 'default' => 'مراحل ثبت‌نام سامانه ثنا برای ایرانیان خارج از کشور دقیقاً همین‌هاست. با نوار پایین بین مراحل جابه‌جا شوید یا یکی از چهار فاز را انتخاب کنید.' ) );
	add_settings_field( 'foot_text', __( 'متن پایین ریل', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'foot_text', 'default' => 'مرحله ۱۴ تا ۱۷ همان بخشی است که یوآیدی برایتان انجام می‌دهد.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'btn_text', 'default' => 'ثبت سفارش پرداخت ارزی' ) );
	add_settings_field( 'steps_notice', '', 'uid_field_notice', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'text' => __( 'متن دقیق هر یک از ۲۱ مرحله ریل در assets/js/sana-abroad-page.js تعریف شده و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'fold_min', 'default' => '۴ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'fold_title', 'default' => 'راهنمای کامل ۲۱ مرحله ثبت‌نام' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sasteps', 'uid_section_sasteps_main', array( 'group' => 'uid_section_sasteps', 'key' => 'fold_teaser', 'default' => 'از ورود به سامانه تا صدور برگه ثنا — قدم به قدم' ) );

	/* ---------------- فصل ۳: مدارک لازم ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sadocs', array( 'sanitize_callback' => 'uid_sanitize_section_sadocs', 'default' => array() ) );
	add_settings_section( 'uid_section_sadocs_main', '', '__return_false', 'uid_section_sadocs' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'eyebrow', 'default' => 'پیش از شروع' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'heading', 'default' => 'چه چیزهایی لازم دارید؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'text', 'default' => 'ایرانیانی که در خارج از کشور اقامت دارند، جهت ثبت‌نام در سامانه ثنا کافی است اطلاعات زیر را در دسترس داشته باشند. نیازی به مراجعه حضوری، ارسال پستی مدارک یا حضور در سفارت نیست.' ) );
	add_settings_field( 'items', __( 'مدارک لازم (هشت مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sadocs', 'uid_section_sadocs_main', array(
		'group' => 'uid_section_sadocs', 'key' => 'items', 'default' => uid_default_sa_docs(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'cta_heading', 'default' => 'مطمئن نیستید مدارکتان کافی است؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'cta_text', 'default' => 'قبل از اینکه وقت بگذارید، یک تماس کوتاه با کارشناس یوآیدی بگیرید تا با هم چک کنیم.' ) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'fold_min', 'default' => '۲ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'fold_title', 'default' => 'مدارک و اطلاعات لازم' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sadocs', 'uid_section_sadocs_main', array( 'group' => 'uid_section_sadocs', 'key' => 'fold_teaser', 'default' => 'قبل از شروع، این هفت مورد را کنار دستتان بگذارید' ) );

	/* ---------------- فصل ۴: آی‌گپ ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_saigap', array( 'sanitize_callback' => 'uid_sanitize_section_saigap', 'default' => array() ) );
	add_settings_section( 'uid_section_saigap_main', '', '__return_false', 'uid_section_saigap' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'eyebrow', 'default' => 'نکته کلیدی نسخه بین‌المللی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'heading', 'default' => 'شماره تلفن ایرانی ندارید؟ لازم نیست داشته باشید.' ) );
	add_settings_field( 'text', __( 'توضیح (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'text', 'default' => 'بزرگ‌ترین مانع فنی ثبت‌نام از خارج از کشور این بود که سامانه، شماره تلفن همراه و ثابت ایرانی می‌خواست. در نسخه international.adliran.ir این مشکل حل شده است.' ) );
	add_settings_field( 'steps', __( 'گام‌ها (سه مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_saigap', 'uid_section_saigap_main', array(
		'group' => 'uid_section_saigap', 'key' => 'steps', 'default' => uid_default_sa_igap_steps(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'note_text', __( 'یادداشت پایانی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'note_text', 'default' => 'اگر در همین مرحله به مشکل خوردید، لازم نیست از اول شروع کنید — کارشناسان یوآیدی از طریق تلگرام @uid_support یا شماره پشتیبانی همراهی‌تان می‌کنند.' ) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'fold_min', 'default' => '۲ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'fold_title', 'default' => 'آی‌گپ به‌جای شماره تلفن ایرانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_saigap', 'uid_section_saigap_main', array( 'group' => 'uid_section_saigap', 'key' => 'fold_teaser', 'default' => 'جزئیاتی که بیشترین سوال را ایجاد می‌کند' ) );

	/* ---------------- فصل ۵: درگاه پرداخت ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sapay', array( 'sanitize_callback' => 'uid_sanitize_section_sapay', 'default' => array() ) );
	add_settings_section( 'uid_section_sapay_main', '', '__return_false', 'uid_section_sapay' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'eyebrow', 'default' => 'قلب سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'heading', 'default' => 'شما ارزی می‌پردازید، یوآیدی ریالی را داخل ایران تسویه می‌کند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'text', 'default' => 'در صورتی که قصد ثبت‌نام در سامانه ثنا را دارید و در خارج از ایران هستید، می‌توانید به آسانی با استفاده از درگاه پرداخت ارزی یوآیدی، هزینه سرویس را به‌صورت ارزی و با ویزا کارت، مستر کارت یا سایر کارت‌های معتبر بین‌المللی پرداخت نمائید و فرآیند ثبت‌نام خود را به‌صورت آنلاین تکمیل کنید.' ) );
	add_settings_field( 'steps', __( 'گام‌های پرداخت (سه مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sapay', 'uid_section_sapay_main', array(
		'group' => 'uid_section_sapay', 'key' => 'steps', 'default' => uid_default_sa_pay_steps(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'step', 'type' => 'text', 'label' => __( 'برچسب گام (مثل STEP 14)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'cta_heading', __( 'عنوان بند تماس', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'cta_heading', 'default' => 'کد ۱۶ رقمی را همین حالا دارید؟' ) );
	add_settings_field( 'cta_text', __( 'توضیح بند تماس', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'cta_text', 'default' => 'فرم ثبت سفارش را باز کنید، کد را وارد کنید و باقی کار را به کارشناسان ما بسپارید.' ) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'fold_min', 'default' => '۳ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'fold_title', 'default' => 'درگاه پرداخت ارزی یوآیدی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sapay', 'uid_section_sapay_main', array( 'group' => 'uid_section_sapay', 'key' => 'fold_teaser', 'default' => 'دقیقاً همان کاری که یوآیدی برای شما انجام می‌دهد' ) );

	/* ---------------- فصل ۶: تیم متخصص ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_sateam', array( 'sanitize_callback' => 'uid_sanitize_section_sateam', 'default' => array() ) );
	add_settings_section( 'uid_section_sateam_main', '', '__return_false', 'uid_section_sateam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'eyebrow', 'default' => 'پشت این سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'heading', 'default' => 'یک تیم تخصصی، فقط برای ثنای خارج از کشور' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'text', 'default' => 'یوآیدی نام تجاری شرکت دانش‌بنیان بینش هوشمند نسل پیشرو است؛ از سال ۱۳۹۶ در حوزه احراز هویت آنلاین فعال و به‌عنوان کارگزار مورد تایید سامانه‌های سجام و ثنا مشغول به کار است.' ) );
	add_settings_field( 'crew', __( 'ردیف‌های تیم (چهار مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sateam', 'uid_section_sateam_main', array(
		'group' => 'uid_section_sateam', 'key' => 'crew', 'default' => uid_default_sa_crew(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'kpi', 'type' => 'text', 'label' => __( 'برچسب کوتاه سمت راست', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'fold_min', 'default' => '۲ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'fold_title', 'default' => 'تیم متخصص و پشتیبانی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_sateam', 'uid_section_sateam_main', array( 'group' => 'uid_section_sateam', 'key' => 'fold_teaser', 'default' => 'با چه کسانی طرف هستید و چه چیزی تعهد می‌شود' ) );

	/* ---------------- فصل ۷: پیگیری و استعلام ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_satrack', array( 'sanitize_callback' => 'uid_sanitize_section_satrack', 'default' => array() ) );
	add_settings_section( 'uid_section_satrack_main', '', '__return_false', 'uid_section_satrack' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'eyebrow', 'default' => 'بعد از ثبت‌نام' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'heading', 'default' => 'چطور برگه ثنا را استعلام کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'text', 'default' => 'ثبت‌نام که تمام شد، دسترسی به اطلاعات ثبت‌شده‌تان با دو کلیک ممکن است.' ) );
	add_settings_field( 'items', __( 'مراحل پیگیری (دو مورد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_satrack', 'uid_section_satrack_main', array(
		'group' => 'uid_section_satrack', 'key' => 'items', 'default' => uid_default_sa_track(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'fold_min', 'default' => '۱ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'fold_title', 'default' => 'پیگیری، استعلام و دریافت برگه ثنا' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_satrack', 'uid_section_satrack_main', array( 'group' => 'uid_section_satrack', 'key' => 'fold_teaser', 'default' => 'بعد از ثبت‌نام، سراغ اطلاعاتتان چطور بروید' ) );

	/* ---------------- فصل ۸: سرویس‌های مرتبط ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_saxsell', array( 'sanitize_callback' => 'uid_sanitize_section_saxsell', 'default' => array() ) );
	add_settings_section( 'uid_section_saxsell_main', '', '__return_false', 'uid_section_saxsell' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'eyebrow', 'default' => 'سرویس‌های مرتبط' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'heading', 'default' => 'اگر مورد شما چیز دیگری است' ) );
	add_settings_field( 'items', __( 'کارت‌های سرویس', 'uid-theme' ), 'uid_field_repeater', 'uid_section_saxsell', 'uid_section_saxsell_main', array(
		'group' => 'uid_section_saxsell', 'key' => 'items', 'default' => uid_default_sa_xsell(), 'add_label' => __( 'افزودن سرویس', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'sana' => __( 'ثنا', 'uid-theme' ), 'sejam' => __( 'سجام', 'uid-theme' ), 'pwa' => __( 'یوآیدی‌پلاس', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'fold_min', 'default' => '۱ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'fold_title', 'default' => 'سرویس‌های مرتبط یوآیدی' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_saxsell', 'uid_section_saxsell_main', array( 'group' => 'uid_section_saxsell', 'key' => 'fold_teaser', 'default' => 'اگر داخل ایران هستید یا کسب‌وکار دارید، این‌ها به کارتان می‌آید' ) );

	/* ---------------- فصل ۹: سوالات متداول ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_safaq', array( 'sanitize_callback' => 'uid_sanitize_section_safaq', 'default' => array() ) );
	add_settings_section( 'uid_section_safaq_main', '', '__return_false', 'uid_section_safaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'eyebrow', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'heading', 'default' => 'سوالات متداول ثبت‌نام سامانه ثنا برای ایرانیان خارج از کشور' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_safaq', 'uid_section_safaq_main', array(
		'group' => 'uid_section_safaq', 'key' => 'items', 'default' => uid_default_sa_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'fold_min', __( 'زمان مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'fold_min', 'default' => '۳ دقیقه' ) );
	add_settings_field( 'fold_title', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'fold_title', 'default' => 'سوالات متداول' ) );
	add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_safaq', 'uid_section_safaq_main', array( 'group' => 'uid_section_safaq', 'key' => 'fold_teaser', 'default' => 'هشت سوالی که بیشترین تکرار را دارند' ) );

	/* ---------------- مشخصات سرویس (پنل تیره) ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_saadv', array( 'sanitize_callback' => 'uid_sanitize_section_saadv', 'default' => array() ) );
	add_settings_section( 'uid_section_saadv_main', '', '__return_false', 'uid_section_saadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'eyebrow', 'default' => 'مشخصات سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'heading', 'default' => 'پنج چیزی که با ثبت سفارش به دست می‌آورید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'text', 'default' => 'هر مورد زیر مستقیماً از شرح خدمت ثبت‌نام ثنای ایرانیان خارج از کشور یوآیدی آمده است.' ) );
	add_settings_field( 'items', __( 'آیتم‌های پنل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_saadv', 'uid_section_saadv_main', array(
		'group' => 'uid_section_saadv', 'key' => 'items', 'default' => uid_default_sa_adv(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'shield' => __( 'اعتبار/کارگزار', 'uid-theme' ), 'card' => __( 'کارت بانکی', 'uid-theme' ), 'pin' => __( 'موقعیت/تسویه', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ), 'mail' => __( 'ایمیل', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عنوان کوتاه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد نوار پیشرفت (عدد بین ۰ تا ۱۰۰)', 'uid-theme' ) ),
			array( 'key' => 'feat', 'type' => 'select', 'label' => __( 'کارت برجسته (بزرگ‌تر)', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'wide', 'type' => 'select', 'label' => __( 'عرض دوبرابر', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین پنل', 'uid-theme' ), 'uid_field_text', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'foot_text', 'default' => 'سوالی مانده که اینجا جوابش نیست؟' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_saadv', 'uid_section_saadv_main', array( 'group' => 'uid_section_saadv', 'key' => 'btn_text', 'default' => 'مشاوره رایگان بگیرید' ) );

	/* ---------------- نحوه ثبت سفارش ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_saorder', array( 'sanitize_callback' => 'uid_sanitize_section_saorder', 'default' => array() ) );
	add_settings_section( 'uid_section_saorder_main', '', '__return_false', 'uid_section_saorder' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_saorder', 'uid_section_saorder_main', array( 'group' => 'uid_section_saorder', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_saorder', 'uid_section_saorder_main', array( 'group' => 'uid_section_saorder', 'key' => 'eyebrow', 'default' => 'نحوه ثبت سفارش' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_saorder', 'uid_section_saorder_main', array( 'group' => 'uid_section_saorder', 'key' => 'heading', 'default' => 'از فرم تا تایید پرداخت، سه قدم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_saorder', 'uid_section_saorder_main', array( 'group' => 'uid_section_saorder', 'key' => 'text', 'default' => 'لازم نیست کارت بانکی ایرانی داشته باشید، لازم نیست کسی را در ایران زحمت بدهید و لازم نیست سفر کنید.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_saorder', 'uid_section_saorder_main', array(
		'group' => 'uid_section_saorder', 'key' => 'steps', 'default' => uid_default_sa_order(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_sa_group', 'uid_section_salead', array( 'sanitize_callback' => 'uid_sanitize_section_salead', 'default' => array() ) );
	add_settings_section( 'uid_section_salead_main', '', '__return_false', 'uid_section_salead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'eyebrow', 'default' => 'فرم ثبت سفارش' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'heading', 'default' => 'ثبت‌نام ثنای خود را همین امروز تمام کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'text', 'default' => 'فرم را پر کنید تا کارشناس بخش ثنای خارج از کشور یوآیدی با شما در ارتباط باشد: بررسی وضعیت پرونده، مسیر پرداخت ارزی و راهنمایی تا صدور برگه ثنا.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'trust', 'default' => "مشاوره رایگان\nبدون نیاز به کارت ایرانی\nپشتیبانی تلگرام" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'form_title', 'default' => 'فرم مشاوره و ثبت سفارش' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'form_hint', 'default' => 'سه فیلد، کمتر از یک دقیقه. کد ۱۶ رقمی فقط اگر به مرحله پرداخت رسیده‌اید لازم است.' ) );
	add_settings_field( 'stage_options', __( 'گزینه‌های «در چه مرحله‌ای هستید» (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'stage_options', 'default' => "هنوز شروع نکرده‌ام\nدر حال تکمیل اطلاعات هستم\nبه مرحله پرداخت رسیده‌ام و کد ۱۶ رقمی را دارم\nپرداخت شده، برای ادامه راهنمایی می‌خواهم", 'desc' => __( 'گزینه «داخل ایران هستم» همیشه به‌صورت ثابت در انتهای فهرست اضافه می‌شود.', 'uid-theme' ) ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'submit_text', 'default' => 'ارسال و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'note_text', 'default' => 'اطلاعات شما فقط برای پیگیری همین سفارش استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'success_title', 'default' => 'سفارش شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_salead', 'uid_section_salead_main', array( 'group' => 'uid_section_salead', 'key' => 'success_text', 'default' => 'کارشناس بخش ثنای خارج از کشور یوآیدی به‌زودی با شما در ارتباط خواهد بود. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_sa_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_sahero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'br' => array(), 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
	);
}

function uid_sanitize_section_satrust( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'numeric', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_sastuck( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'phases'      => uid_sanitize_repeater_rows( $input['phases'] ?? '[]', array(
			array( 'key' => 'pn', 'type' => 'text', 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'who', 'type' => 'text' ),
			array( 'key' => 'mine', 'type' => 'text' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
	);
}

function uid_sanitize_section_sacost( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_sarouter( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
	);
}

function uid_sanitize_section_sawhat( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'risks'       => uid_sanitize_repeater_rows( $input['risks'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'tg_heading'  => sanitize_text_field( $input['tg_heading'] ?? '' ),
		'tg_text'     => sanitize_textarea_field( $input['tg_text'] ?? '' ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_sasteps( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'foot_text'   => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn_text'    => sanitize_text_field( $input['btn_text'] ?? '' ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_sadocs( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_saigap( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'       => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'note_text'   => sanitize_textarea_field( $input['note_text'] ?? '' ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_sapay( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'       => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'step', 'type' => 'text', 'required' => true ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'cta_heading' => sanitize_text_field( $input['cta_heading'] ?? '' ),
		'cta_text'    => sanitize_textarea_field( $input['cta_text'] ?? '' ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_sateam( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'crew'        => uid_sanitize_repeater_rows( $input['crew'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'kpi', 'type' => 'text' ),
		) ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_satrack( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_saxsell( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'url', 'type' => 'text' ),
		) ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_safaq( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'items'       => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
		'fold_min'    => sanitize_text_field( $input['fold_min'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}

function uid_sanitize_section_saadv( $input ) {
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
		) ),
		'foot_text' => sanitize_text_field( $input['foot_text'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_saorder( $input ) {
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

function uid_sanitize_section_salead( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'         => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'    => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'     => sanitize_text_field( $input['form_hint'] ?? '' ),
		'stage_options' => sanitize_textarea_field( $input['stage_options'] ?? '' ),
		'submit_text'   => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'     => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title' => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'  => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_sa_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'ثبت سفارش ثنا ویژه ایرانیان خارج از کشور', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'نام و آیدی تلگرامتان را بگذارید تا کارشناس بخش ثنای خارج از کشور یوآیدی با شما در ارتباط باشد: بررسی وضعیت پرونده، مسیر پرداخت ارزی با ویزا و مستر کارت، و راهنمایی تا صدور برگه ثنا.', 'uid-theme' ); ?></p>
	    <form id="modalForm" novalidate>
	<div class="cta-fields">
	  <div class="fld"><label for="m_name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	    <input id="m_name" name="name" type="text" placeholder="<?php esc_attr_e( 'مثلاً علی رضایی', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_tg"><?php esc_html_e( 'آیدی تلگرام', 'uid-theme' ); ?></label>
	    <input id="m_tg" name="telegram" type="text" placeholder="<?php esc_attr_e( 'مثلاً @uid_support', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_stage"><?php esc_html_e( 'در چه مرحله‌ای هستید؟', 'uid-theme' ); ?></label>
	    <select id="m_stage" name="stage" data-req>
	      <option value=""><?php esc_html_e( 'انتخاب کنید…', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'هنوز شروع نکرده‌ام', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'در حال تکمیل اطلاعات در سامانه ثنا هستم', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'به مرحله پرداخت رسیده‌ام و کد ۱۶ رقمی را دارم', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'پرداخت انجام شده، برای ادامه راهنمایی می‌خواهم', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'فقط سوال دارم', 'uid-theme' ); ?></option>
	    </select></div>
	  <div class="fld"><label for="m_code"><?php esc_html_e( 'کد ۱۶ رقمی صفحه پرداخت ثنا (اختیاری)', 'uid-theme' ); ?></label>
	    <input id="m_code" name="sanacode" type="text" inputmode="numeric" maxlength="19"
	           placeholder="<?php esc_attr_e( 'اگر به مرحله پرداخت رسیده‌اید', 'uid-theme' ); ?>"></div>
	  <button class="btn btn-cta btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'ارسال سفارش و مشاوره', 'uid-theme' ); ?></button>
	  <p class="tiny" style="margin-top:10px;color:#7D91B4"><?php esc_html_e( 'کارشناس یوآیدی در ساعات کاری ایران، سریع با شما در ارتباط خواهد بود.', 'uid-theme' ); ?></p>
	</div>
	<div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><b><?php esc_html_e( 'سفارش شما ثبت شد', 'uid-theme' ); ?></b>
	  <span><?php esc_html_e( 'کارشناس بخش ثنای خارج از کشور یوآیدی به‌زودی با شما در ارتباط خواهد بود. برای پیگیری فوری:', 'uid-theme' ); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div></form>
	    <p class="tiny" style="margin-top:14px;color:#7D91B4;text-align:center">
	      <?php esc_html_e( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ); ?>
	      <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" style="color:#7FE0EC;font-weight:700" class="mono"><?php echo esc_html( uid_phone_display() ); ?></a></p>
	  </div>
	</div>
	<?php
}
