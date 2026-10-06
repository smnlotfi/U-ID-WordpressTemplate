<?php
/**
 * صفحه اختصاصی «درباره ما» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «ab» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options با
 * سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * این صفحه هم سیستم «فصل تاخوردنی + نوار پرش سریع» موبایل را دارد — دقیقاً مثل
 * inc/glossary-page.php / inc/card-to-iban-page.php. شش فصل میانی صفحه (مأموریت،
 * هویت برند، ارزش‌ها، نقشه توانمندی، کارنامه، تعهدها) داخل این سیستم قرار می‌گیرند؛
 * هیرو، مسیریابی مخاطب و بنر تماس نهایی بیرون از آن و همیشه باز هستند.
 *
 * چند بخش کاملاً تعاملی/تصویری (شکل هندسی هویت برند در فصل «هویت برند» و نقشه
 * لایه‌های نقشه توانمندی در فصل «نقشه توانمندی») برای هماهنگی دقیق با کد جاوااسکریپت
 * اختصاصی این صفحه، از پیشخوان قابل‌ویرایش نیستند — دقیقاً مثل پایگاه‌داده اصطلاحات
 * inc/glossary-page.php که با uid_field_notice به همین شکل مستند شده است.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_AB_TEMPLATE', 'template-about-us.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_ab_sections_registry() {
	return array(
		'abhero'     => array( 'label' => __( 'هیرو + کپسول آمار ۳۰ ثانیه‌ای', 'uid-theme' ),   'icon' => 'dashicons-star-filled' ),
		'abrouter'   => array( 'label' => __( 'انتخاب مسیر مخاطب (تب‌های مسیریابی)', 'uid-theme' ), 'icon' => 'dashicons-randomize' ),
		'abmission'  => array( 'label' => __( 'مأموریت شرکت', 'uid-theme' ),                    'icon' => 'dashicons-flag' ),
		'abidentity' => array( 'label' => __( 'هویت برند (نظم و دگرگونی)', 'uid-theme' ),       'icon' => 'dashicons-admin-customizer' ),
		'abvalues'   => array( 'label' => __( 'ارزش‌های کلیدی (۵ مورد)', 'uid-theme' ),         'icon' => 'dashicons-awards' ),
		'abplatform' => array( 'label' => __( 'نقشه توانمندی پلتفرم (۴ لایه)', 'uid-theme' ),   'icon' => 'dashicons-layout' ),
		'abrecord'   => array( 'label' => __( 'کارنامه عملیاتی (آمار)', 'uid-theme' ),          'icon' => 'dashicons-chart-bar' ),
		'abvows'     => array( 'label' => __( 'تعهدهای یوآیدی به مشتری (۶ مورد)', 'uid-theme' ), 'icon' => 'dashicons-shield-alt' ),
		'ablead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),            'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_ab_layout() {
	$registry = uid_ab_sections_registry();
	$saved    = get_option( 'uid_ab_layout', array() );

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

function uid_sanitize_ab_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_ab_sections_registry();
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
function uid_ab_folded_slugs() {
	return array( 'abmission', 'abidentity', 'abvalues', 'abplatform', 'abrecord', 'abvows' );
}

function uid_render_ab_sections() {
	uid_render_ab_jumpbar();

	$folded = uid_ab_folded_slugs();
	$layout = uid_get_ab_layout();

	$foldbar_done = false;
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$is_folded = in_array( $row['slug'], $folded, true );
		if ( $is_folded && ! $foldbar_done ) {
			echo '<div id="chapters" class="fold-run">';
			uid_render_ab_foldbar();
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
 */
function uid_render_ab_foldbar() {
	$count = count( uid_ab_folded_slugs() );
	?>
	<div class="foldbar" id="foldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'بخش — روی هر کدام بزنید تا باز شود. لازم نیست همه را بخوانید.', 'uid-theme' ); ?></span>
	  <button class="fb-filter" id="foldFilter" type="button" aria-pressed="false">
	    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
	    <span><?php esc_html_e( 'فقط بخش‌های ضروری', 'uid-theme' ); ?></span></button>
	  <span class="fold-meter"><i id="foldMeter"></i></span>
	</div>
	<?php
}

/**
 * نوار «پرش سریع به بخش‌ها» — عنصر ساختاری ثابت (نه یک سکشن محتوایی)؛ همیشه نمایش
 * داده می‌شود — دقیقاً مثل inc/glossary-page.php.
 */
function uid_render_ab_jumpbar() {
	?>
	<div class="jumpbar" id="jumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jump-rail" id="jumpRail">
	    <a class="jump-chip cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php esc_html_e( 'تماس با ما', 'uid-theme' ); ?></a>
	    <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	    <button class="jump-chip" type="button" data-jump="#router"><?php esc_html_e( 'مسیر شما', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#chapters"><?php esc_html_e( 'فهرست بخش‌ها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#platform"><?php esc_html_e( 'نقشه توانمندی', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#identity"><?php esc_html_e( 'ما که هستیم', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#record"><?php esc_html_e( 'کارنامه', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#vows"><?php esc_html_e( 'تعهدها', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#lead"><?php esc_html_e( 'فرم تماس', 'uid-theme' ); ?></button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه — فقط مسیر داخلی svg (بدون تگ wrapper)
 * ===================================================================== */
function uid_ab_icon_svg( $key ) {
	$icons = array(
		'clock'         => '<circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/>',
		'shield-check'  => '<path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/>',
		'doc-id'        => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'medal'         => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'crime-shield'  => '<path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/><path d="M15 9l-4 4-2-2"/>',
		'trust-people'  => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'smile-ease'    => '<circle cx="12" cy="12" r="9.5"/><path d="M8.5 14.5a4.5 4.5 0 007 0M9 9.5v.01M15 9.5v.01"/>',
		'cost-arrow'    => '<path d="M22 7l-8.5 8.5-4-4L2 19"/><path d="M16 7h6v6"/>',
		'speed-bolt'    => '<path d="M13 2L4 14h6l-1 8 9-12h-6z"/>',
		'lock-shield'   => '<rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/>',
		'squares4'      => '<rect x="3" y="3" width="7" height="7" rx="1.6"/><rect x="14" y="3" width="7" height="7" rx="1.6"/><rect x="3" y="14" width="7" height="7" rx="1.6"/><rect x="14" y="14" width="7" height="7" rx="1.6"/>',
		'scale'         => '<path d="M12 3v18M7 21h10M5 7l-3 7h6zM19 7l-3 7h6z"/><path d="M4 7h16"/>',
		'face-scan'     => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'multivendor'   => '<path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5M3 17l9 5 9-5"/>',
		'fdp-check'     => '<path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/><path d="M9.5 12.5l1.8 1.8 3.5-3.8"/>',
		'support24'     => '<path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="2" y="13" width="4.5" height="7" rx="1.6"/><rect x="17.5" y="13" width="4.5" height="7" rx="1.6"/><path d="M20 20v.5a3 3 0 01-3 3h-3"/>',
		'sandbox-brack' => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	);
	return $icons[ $key ] ?? $icons['shield-check'];
}

function uid_ab_svg_kses() {
	return array(
		'path'   => array( 'd' => true ),
		'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
		'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ),
	);
}

/**
 * نشان کوچک شش‌ضلعی + شماره فصل، بالای عنوان هر بخش (تزئینی و ثابت)
 */
function uid_ab_sec_mark( $number ) {
	return '<span class="ab-mark"><svg viewBox="0 0 52 56"><polygon class="o1" points="26,1 50,15 50,41 26,55 2,41 2,15"/><polygon class="o2" points="26,7 45,18 45,38 26,49 7,38 7,18"/></svg><b>' . esc_html( uid_fa_digits( $number ) ) . '</b></span>';
}

/* =====================================================================
 * ۱) هیرو + کپسول آمار ۳۰ ثانیه‌ای
 * ===================================================================== */
function uid_default_ab_honey() {
	return array(
		array( 'icon' => 'clock',   'value' => '۱۳۹۶',        'label' => 'آغاز فعالیت در حوزه احراز هویت آنلاین' ),
		array( 'icon' => 'danesh',  'value' => 'دانش‌بنیان',   'label' => 'بینش هوشمند نسل پیشرو' ),
		array( 'icon' => 'sejam',   'value' => 'سجام و ثنا',   'label' => 'کارگزار مورد تایید سامانه‌های رسمی' ),
		array( 'icon' => 'medal',   'value' => 'اپراتور اول',  'label' => 'احراز هویت ایران' ),
	);
}
function uid_ab_honey_icon_key( $key ) {
	$map = array( 'clock' => 'clock', 'danesh' => 'shield-check', 'sejam' => 'doc-id', 'medal' => 'medal' );
	return $map[ $key ] ?? 'clock';
}
function uid_default_ab_capsule() {
	return array(
		array( 'value' => '۱۳۹۶',        'label' => 'سال آغاز فعالیت یوآیدی در احراز هویت آنلاین' ),
		array( 'value' => 'سجام و ثنا',  'label' => 'کارگزار مورد تایید سامانه‌های رسمی کشور' ),
		array( 'value' => '+۵٬۰۰۰٬۰۰۰',  'label' => 'احراز هویت موفق انجام‌شده تا امروز' ),
		array( 'value' => '۹۹٫۵٪',       'label' => 'آپ‌تایم تعهدی سرویس، با معماری چندتامین‌کننده' ),
	);
}
function uid_render_section_abhero() {
	$tag    = uid_section_tag( 'abhero', 'h1' );
	$tags_raw = uid_section_val( 'abhero', 'hero_tags', "شرکت دانش‌بنیان\nکارگزار مورد تایید سجام و ثنا\nاولین ارائه‌دهنده ثنای آنلاین برای ایرانیان خارج از کشور" );
	$tags   = array_filter( array_map( 'trim', explode( "\n", $tags_raw ) ) );
	$honey  = uid_section_val( 'abhero', 'honey', uid_default_ab_honey() );
	if ( ! is_array( $honey ) ) $honey = array();
	$capsule = uid_section_val( 'abhero', 'capsule', uid_default_ab_capsule() );
	if ( ! is_array( $capsule ) ) $capsule = array();
	?>
	<section class="dark ab-hero" id="top">
	  <div class="ab-lat" aria-hidden="true">
	    <svg width="100%" height="100%">
	      <defs>
	        <pattern id="hexlat" width="60" height="104" patternUnits="userSpaceOnUse">
	          <path d="M30 2 L58 18 L58 50 L30 66 L2 50 L2 18 Z
	                   M0 54 L28 70 L28 102 L0 118 L-28 102 L-28 70 Z
	                   M60 54 L88 70 L88 102 L60 118 L32 102 L32 70 Z"
	                fill="none" stroke="#29BCCE" stroke-width="1" stroke-linejoin="round"/>
	        </pattern>
	      </defs>
	      <rect width="100%" height="100%" fill="url(#hexlat)" opacity=".5"/>
	    </svg>
	  </div>
	  <div class="ab-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php esc_html_e( 'درباره ما', 'uid-theme' ); ?></b></div>
	    </div>
	    <div class="ab-hero-grid">
	      <div class="rv">
	        <span class="ab-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'abhero', 'eyebrow', __( 'درباره یوآیدی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'abhero', 'heading', __( 'اولین پلتفرم احراز هویت دیجیتال ایران، <mark>از ۱۳۹۶ تا امروز</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo wp_kses( uid_section_val( 'abhero', 'lede', __( 'یوآیدی نام تجاری <b style="color:#fff">شرکت دانش‌بنیان بینش هوشمند نسل پیشرو</b> است — کارگزار مورد تایید سامانه‌های سجام و ثنا. هویت افراد را بدون مراجعه حضوری و با اتکا به هوش مصنوعی، فناوری‌های <span class="lat">OCR</span>، تطبیق چهره و تشخیص زنده‌بودن احراز می‌کنیم.', 'uid-theme' ) ), array( 'b' => array( 'style' => true ), 'span' => array( 'class' => true ) ) ); ?></p>
	        <div class="ab-btn-row">
	          <a class="ab-btn ab-btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	          <a class="ab-btn ab-btn-ghost-d" href="#platform" data-jump-soft="#platform"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5M3 17l9 5 9-5"/></svg>
	            <?php echo esc_html( uid_section_val( 'abhero', 'btn2_text', __( 'نقشه توانمندی پلتفرم', 'uid-theme' ) ) ); ?></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <div class="rv rv-d2">
	        <div class="ab-honey">
	          <?php foreach ( $honey as $i => $h ) :
	          	$is_feat = 3 === $i;
	          	$val_class = 0 === $i ? '' : ' class="fa"';
	          	?>
	          <div class="ab-hex<?php echo $is_feat ? ' feat' : ''; ?>"><div class="in">
	            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_icon_svg( uid_ab_honey_icon_key( $h['icon'] ?? 'clock' ) ), uid_ab_svg_kses() ); ?></svg></span>
	            <b<?php echo $val_class; ?>><?php echo esc_html( $h['value'] ?? '' ); ?></b><span><?php echo esc_html( $h['label'] ?? '' ); ?></span></div></div>
	          <?php endforeach; ?>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>

	<section class="sec" style="padding-block:44px 0">
	  <div class="ab-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $capsule as $i => $c ) :
	      	$stripped = str_replace( array( '٬', ',', '+', '٪', '٫', ' ' ), '', (string) ( $c['value'] ?? '' ) );
	      	$is_txt   = '' === $stripped || ! preg_match( '/^[0-9۰-۹]+$/u', $stripped );
	      	?>
	      <div class="qstat"><span class="v<?php echo $is_txt ? ' txt' : ''; ?>"><?php echo esc_html( $c['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $c['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۲) انتخاب مسیر مخاطب (تب‌های مسیریابی)
 * ===================================================================== */
function uid_ab_router_route_icon( $key ) {
	$icons = array(
		'pwa'      => '<path d="M13 2L4 14h6l-1 8 9-12h-6z"/>',
		'api'      => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'card'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
		'crypto'   => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'sana'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'sejam'    => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'globe'    => '<circle cx="12" cy="12" r="9.5"/><path d="M2.5 12h19M12 2.5c2.5 2.7 4 6 4 9.5s-1.5 6.8-4 9.5c-2.5-2.7-4-6-4-9.5s1.5-6.8 4-9.5z"/>',
		'liveness' => '<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/>',
		'face'     => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'doc'      => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
	);
	return $icons[ $key ] ?? $icons['api'];
}

/**
 * کارت‌های مسیریابی هر تب — محتوای ثابت (بدون امکان ویرایش از پیشخوان)؛ برای تغییر
 * همین آرایه را ویرایش کنید. آیکون‌ها و لینک‌ها باید با صفحات واقعی سایت هماهنگ بمانند.
 */
function uid_ab_router_routes() {
	return array(
		'biz' => array(
			array( 'icon' => 'pwa', 'title' => 'یوآیدی‌پلاس (PWA)', 'text' => 'برون‌سپاری کامل احراز هویت، بدون بار توسعه فنی', 'href' => '/uid-plus/' ),
			array( 'icon' => 'api', 'title' => 'وب‌سرویس احراز هویت تصویری', 'text' => 'تشخیص زنده‌بودن و تطبیق چهره از طریق API', 'href' => '/api/' ),
			array( 'icon' => 'card', 'title' => 'سرویس‌های اعتبارسنجی مالی', 'text' => 'تطبیق کارت و شبا با کد ملی، پیش از تراکنش', 'href' => '/api-validate-card/' ),
			array( 'icon' => 'crypto', 'title' => 'احراز هویت صرافی ارز دیجیتال', 'text' => 'راهکار آماده برای پلتفرم‌های رمزارز', 'href' => '/authentication-digital-currency-exchange/' ),
		),
		'ind' => array(
			array( 'icon' => 'sana', 'title' => 'احراز هویت ثنا', 'text' => 'سامانه ابلاغ الکترونیک قضایی', 'href' => '/sana/' ),
			array( 'icon' => 'sejam', 'title' => 'احراز هویت سجام', 'text' => 'ورود به بازار سرمایه', 'href' => '/sejamauthentication/' ),
			array( 'icon' => 'globe', 'title' => 'ثنا ویژه ایرانیان خارج از کشور', 'text' => 'ثبت سفارش آنلاین، بدون مراجعه حضوری', 'href' => '/sana-register-foreign-form/' ),
		),
		'ceo' => array(
			array( 'icon' => 'sejam', 'title' => 'احراز هویت سجام', 'text' => 'مسیر رسمی ورود به بازار سرمایه', 'href' => '/sejamauthentication/' ),
			array( 'icon' => 'liveness', 'title' => 'تشخیص زنده‌بودن', 'text' => 'اطمینان از حضور واقعی امضاکننده در لحظه امضا', 'href' => '/liveness-detection/' ),
			array( 'icon' => 'api', 'title' => 'احراز هویت تصویری', 'text' => 'تطبیق چهره با تصویر مرجع ثبت احوال', 'href' => '/api/' ),
		),
		'emp' => array(
			array( 'icon' => 'pwa', 'title' => 'یوآیدی‌پلاس (PWA)', 'text' => 'یک فرآیند یکپارچه، بدون نیاز به توسعه داخلی', 'href' => '/uid-plus/' ),
			array( 'icon' => 'face', 'title' => 'تطبیق و تشخیص چهره', 'text' => 'دقت ۹۹٪ در تطابق با تصویر مرجع', 'href' => '/face-detection/' ),
			array( 'icon' => 'doc', 'title' => 'وب‌سرویس ثبت احوال', 'text' => 'استعلام هویت با کد ملی و تاریخ تولد', 'href' => '/api-inquiry-person/' ),
		),
	);
}

function uid_render_ab_router_pane( $key, $tab_index, $is_on ) {
	$routes = uid_ab_router_routes();
	$tag    = uid_section_tag( 'abrouter', 'h2' );
	?>
	<div class="ab-rt-pane<?php echo $is_on ? ' on' : ''; ?>" data-rt-pane="<?php echo esc_attr( $tab_index ); ?>" role="tabpanel">
	  <div class="ab-rt-copy">
	    <span class="tag"><?php echo esc_html( uid_section_val( 'abrouter', $key . '_tag', '' ) ); ?></span>
	    <h3><?php echo esc_html( uid_section_val( 'abrouter', $key . '_heading', '' ) ); ?></h3>
	    <p><?php echo esc_html( uid_section_val( 'abrouter', $key . '_p1', '' ) ); ?></p>
	    <p><?php echo esc_html( uid_section_val( 'abrouter', $key . '_p2', '' ) ); ?></p>
	  </div>
	  <div class="ab-rt-routes">
	    <?php foreach ( $routes[ $key ] as $i => $r ) : ?>
	    <a class="ab-rt-route<?php echo 1 === $i % 2 ? ' teal' : ''; ?>" href="<?php echo esc_url( $r['href'] ); ?>">
	      <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_router_route_icon( $r['icon'] ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></span>
	      <span class="tx"><b><?php echo esc_html( $r['title'] ); ?></b><span><?php echo esc_html( $r['text'] ); ?></span></span>
	      <svg class="ch" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg></a>
	    <?php endforeach; ?>
	  </div>
	  <?php if ( 'ind' === $key ) :
	  	$note = uid_section_val( 'abrouter', 'ind_note', __( 'فرم انتهای این صفحه مخصوص کسب‌وکارهاست. برای خدمات شخصی، مستقیم به <a href="/sana/">صفحه احراز هویت ثنا</a> بروید — سریع‌تر به نتیجه می‌رسید.', 'uid-theme' ) );
	  	if ( $note ) : ?>
	  <div class="ab-rt-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	    <span><?php echo wp_kses( $note, array( 'a' => array( 'href' => true ) ) ); ?></span></div>
	  <?php endif; endif; ?>
	</div>
	<?php
}

function uid_render_section_abrouter() {
	$tag = uid_section_tag( 'abrouter', 'h2' );
	?>
	<section class="sec" id="router" style="padding-block:clamp(52px,6vw,88px) 0">
	  <div class="ab-wrap">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 1 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abrouter', 'eyebrow', __( 'مسیر خود را انتخاب کنید', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abrouter', 'heading', __( 'کدام بخش از یوآیدی به شما مربوط می‌شود؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'abrouter', 'text', __( 'نیاز یک بانک با نیاز یک فرد یکسان نیست — و مسیرشان هم نباید یکسان باشد. انتخاب کنید کدام هستید تا مستقیم به همان چیزی برسید که به شما مربوط است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="ab-router rv rv-d1">
	      <div class="ab-rt-bar" role="tablist" aria-label="<?php esc_attr_e( 'انتخاب مخاطب', 'uid-theme' ); ?>">
	        <button class="ab-rt-tab on" data-rt="0" role="tab" aria-selected="true">
	          <svg class="hx" viewBox="0 0 17 19" fill="currentColor"><polygon points="8.5,0 17,4.75 17,14.25 8.5,19 0,14.25 0,4.75"/></svg><?php esc_html_e( 'کسب‌وکارها', 'uid-theme' ); ?></button>
	        <button class="ab-rt-tab" data-rt="1" role="tab" aria-selected="false">
	          <svg class="hx" viewBox="0 0 17 19" fill="currentColor"><polygon points="8.5,0 17,4.75 17,14.25 8.5,19 0,14.25 0,4.75"/></svg><?php esc_html_e( 'افراد حقیقی', 'uid-theme' ); ?></button>
	        <button class="ab-rt-tab" data-rt="2" role="tab" aria-selected="false">
	          <svg class="hx" viewBox="0 0 17 19" fill="currentColor"><polygon points="8.5,0 17,4.75 17,14.25 8.5,19 0,14.25 0,4.75"/></svg><?php esc_html_e( 'مدیران عامل', 'uid-theme' ); ?></button>
	        <button class="ab-rt-tab" data-rt="3" role="tab" aria-selected="false">
	          <svg class="hx" viewBox="0 0 17 19" fill="currentColor"><polygon points="8.5,0 17,4.75 17,14.25 8.5,19 0,14.25 0,4.75"/></svg><?php esc_html_e( 'کارمندان', 'uid-theme' ); ?></button>
	      </div>

	      <?php
	      uid_render_ab_router_pane( 'biz', 0, true );
	      uid_render_ab_router_pane( 'ind', 1, false );
	      uid_render_ab_router_pane( 'ceo', 2, false );
	      uid_render_ab_router_pane( 'emp', 3, false );
	      ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) مأموریت شرکت
 * ===================================================================== */
function uid_default_ab_mission_rows() {
	return array(
		array( 'icon' => 'crime',  'title' => 'کاهش جرایم اینترنتی', 'text' => 'وقتی هویت طرف مقابل پیش از تراکنش تایید شود، بخش بزرگی از مسیرهای سوءاستفاده پیش از آنکه باز شوند بسته می‌مانند.' ),
		array( 'icon' => 'trust',  'title' => 'افزایش اعتماد در فضای آنلاین', 'text' => 'اعتماد میان دو غریبه، همان چیزی است که بیشتر مدل‌های کسب‌وکار دیجیتال روی آن بنا شده‌اند. احراز هویت، پایه آن اعتماد است.' ),
		array( 'icon' => 'ease',   'title' => 'ایجاد تجربه کاربری آسان', 'text' => 'هر گامی که از مسیر احراز هویت حذف شود، یک نقطه ریزش کمتر است — برای کاربر و برای کسب‌وکاری که منتظر اوست.' ),
		array( 'icon' => 'cost',   'title' => 'کاهش هزینه‌ها', 'text' => 'احراز هویت غیرحضوری، هزینه شعبه، اپراتور، آموزش و بازبینی دستی را از معادله بیرون می‌برد.' ),
		array( 'icon' => 'speed',  'title' => 'افزایش سرعت تراکنش‌ها', 'text' => 'آنچه پیش‌تر چند روز کاری طول می‌کشید، امروز در یک نشست و بدون خروج کاربر از فضای شما انجام می‌شود.' ),
	);
}
function uid_ab_mission_icon_key( $key ) {
	$map = array( 'crime' => 'crime-shield', 'trust' => 'trust-people', 'ease' => 'smile-ease', 'cost' => 'cost-arrow', 'speed' => 'speed-bolt' );
	return $map[ $key ] ?? 'crime-shield';
}
function uid_render_section_abmission() {
	$tag  = uid_section_tag( 'abmission', 'h2' );
	$rows = uid_section_val( 'abmission', 'rows', uid_default_ab_mission_rows() );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec" id="mission" data-fold data-fold-min="۲ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'چرا این شرکت وجود دارد', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'مأموریت یوآیدی و پنج هدفی که دنبال می‌کند', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head rv">
	      <?php echo uid_ab_sec_mark( 2 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abmission', 'eyebrow', __( 'مأموریت', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abmission', 'heading', __( 'احراز هویتی که هم امن باشد، هم آسان، هم سریع', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'abmission', 'text', __( 'مأموریت یوآیدی، ارائه راهکاری امن، آسان و سریع برای احراز هویت در فضای آنلاین است — به منظور تسهیل و امن‌تر کردن تراکنش‌ها و فعالیت‌های آنلاین برای افراد و کسب‌وکارها.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="ab-mission">
	      <div class="ab-purpose rv">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="ab-prow">
	          <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_icon_svg( uid_ab_mission_icon_key( $r['icon'] ?? 'crime' ) ), uid_ab_svg_kses() ); ?></svg></span>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>

	      <div class="ab-quote rv rv-d1">
	        <p class="q">«<?php echo esc_html( uid_section_val( 'abmission', 'quote_text', __( 'در تلاشیم به‌عنوان پلتفرم پیشرو احراز هویت دیجیتال در ایران شناخته شویم و نقشی اساسی در توسعه تجارت الکترونیک و اقتصاد دیجیتال ایفا کنیم.', 'uid-theme' ) ) ); ?>»</p>
	        <div class="by">
	          <span class="hx"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span>
	          <div><b><?php echo esc_html( uid_section_val( 'abmission', 'by_name', __( 'تیم یوآیدی', 'uid-theme' ) ) ); ?></b><span><?php echo esc_html( uid_section_val( 'abmission', 'by_role', __( 'بینش هوشمند نسل پیشرو', 'uid-theme' ) ) ); ?></span></div>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) هویت برند (نظم و دگرگونی) — شکل هندسی و سه پنل توضیح، ثابت/غیرقابل‌ویرایش
 * ===================================================================== */
function uid_render_section_abidentity() {
	$tag = uid_section_tag( 'abidentity', 'h2' );
	?>
	<section class="sec" id="identity" data-fold data-fold-hot data-fold-min="۳ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'نظم، و دگرگونی — هم‌زمان', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'چرا هم‌زمان قابل اتکا و نوآوریم', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 3 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abidentity', 'eyebrow', __( 'ما که هستیم', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abidentity', 'heading', __( 'یک شرکت زیرساختی، دو نیروی متضاد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'abidentity', 'text', __( 'اغلب شرکت‌ها یکی از این دو را انتخاب می‌کنند: یا قابل اتکا و بی‌تحرک، یا نوآور و بی‌ثبات. ما از ابتدا روی هر دو بنا شده‌ایم — و همان نقطه‌ای که این دو به هم می‌رسند، جایی است که یوآیدی ایستاده است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="ab-arch">
	      <div class="rv">
	        <div class="ab-arch-fig" id="archFig">
	          <svg viewBox="0 0 470 320" role="group" aria-label="<?php esc_attr_e( 'ترکیب هویت برند یوآیدی', 'uid-theme' ); ?>">
	            <g class="hxb" data-arch="0" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'نظم و امنیت', 'uid-theme' ); ?>">
	              <polygon points="300,20 421,90 421,230 300,300 179,230 179,90"
	                       fill="#15397C" stroke="#15397C" stroke-width="10" stroke-linejoin="round"/>
	              <text x="350" y="160" fill="#fff" font-size="21"><?php esc_html_e( 'نظم و امنیت', 'uid-theme' ); ?></text>
	            </g>
	            <g class="hxb" data-arch="1" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'دانش و تحول', 'uid-theme' ); ?>">
	              <polygon points="185,60 272,110 272,210 185,260 98,210 98,110"
	                       fill="#29BCCE" stroke="#29BCCE" stroke-width="9" stroke-linejoin="round"
	                       fill-opacity=".93"/>
	              <text x="140" y="160" fill="#06263A" font-size="16"><?php esc_html_e( 'دانش و تحول', 'uid-theme' ); ?></text>
	            </g>
	            <g class="hxb" data-arch="2" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'یوآیدی', 'uid-theme' ); ?>">
	              <polygon points="228,118 264,139 264,181 228,202 192,181 192,139"
	                       fill="#F89428" stroke="#F89428" stroke-width="7" stroke-linejoin="round"/>
	              <text x="228" y="161" fill="#3A2003" font-size="15"><?php esc_html_e( 'یوآیدی', 'uid-theme' ); ?></text>
	            </g>
	          </svg>
	        </div>
	        <div class="ab-arch-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11.5V6a1.8 1.8 0 013.6 0v5M12.6 11V4.8a1.8 1.8 0 013.6 0V11M16.2 11.5V7.5a1.8 1.8 0 013.6 0V15a6.5 6.5 0 01-6.5 6.5h-1.6a6.5 6.5 0 01-5.6-3.2l-2.4-4a1.8 1.8 0 013-2l1.3 1.7"/></svg>
	          <?php esc_html_e( 'روی هر یک از سه شکل بزنید', 'uid-theme' ); ?></div>
	      </div>

	      <div class="rv rv-d1">
	        <div class="ab-arch-panel" id="archPanel" data-on="2">
	          <div data-arch-pane="0" hidden>
	            <span class="ab-eyebrow"><i></i><?php esc_html_e( 'نیروی اول', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'آن‌که نظم می‌آورد', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'برقراری نظم و ساختار، ارائه رهبری و هدایت، ایجاد حس امنیت و ثبات، اتکا به تخصص و دانش.', 'uid-theme' ); ?></p>
	            <p><?php esc_html_e( 'ما بر نظم و امنیت در فضای آنلاین تأکید داریم. با ارائه خدمات احراز هویت دقیق و مطمئن، بستری امن و قابل اعتماد برای تراکنش‌ها و فعالیت‌های آنلاین افراد و کسب‌وکارها می‌سازیم.', 'uid-theme' ); ?></p>
	            <div class="ab-arch-traits"><span><?php esc_html_e( 'نظم و ساختار', 'uid-theme' ); ?></span><span><?php esc_html_e( 'رهبری و هدایت', 'uid-theme' ); ?></span><span><?php esc_html_e( 'امنیت و ثبات', 'uid-theme' ); ?></span><span><?php esc_html_e( 'اتکا به تخصص', 'uid-theme' ); ?></span></div>
	          </div>
	          <div data-arch-pane="1" hidden>
	            <span class="ab-eyebrow"><i></i><?php esc_html_e( 'نیروی دوم', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'آن‌که دگرگون می‌کند', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'تسلط بر دانش و رازها، ارائه راه‌حل‌های خلاقانه، ایجاد تحول و دگرگونی، الهام بخشیدن به دیگران.', 'uid-theme' ); ?></p>
	            <p><?php esc_html_e( 'با هوش مصنوعی و فناوری‌های نوین، راهکارهایی می‌سازیم که پیش از این وجود نداشتند. هدفمان تغییر همان چیزی است که سال‌ها «روال عادی» به حساب می‌آمد: اینکه احراز هویت یعنی صف، مدرک و مراجعه حضوری.', 'uid-theme' ); ?></p>
	            <div class="ab-arch-traits"><span><?php esc_html_e( 'تسلط بر دانش', 'uid-theme' ); ?></span><span><?php esc_html_e( 'راه‌حل خلاقانه', 'uid-theme' ); ?></span><span><?php esc_html_e( 'تحول و دگرگونی', 'uid-theme' ); ?></span><span><?php esc_html_e( 'الهام‌بخشی', 'uid-theme' ); ?></span></div>
	          </div>
	          <div data-arch-pane="2">
	            <span class="ab-eyebrow warm"><i></i><?php esc_html_e( 'نقطه تلاقی', 'uid-theme' ); ?></span>
	            <h3><?php esc_html_e( 'جایی که این دو به هم می‌رسند', 'uid-theme' ); ?></h3>
	            <p><?php esc_html_e( 'ترکیب این دو یعنی: ما هم فضای احراز هویت دیجیتال ایران را اداره می‌کنیم و هم آن را تغییر می‌دهیم. زیرساختی که یک تیم ریسک می‌تواند به آن تکیه کند، بدون اینکه ناچار باشد به روش‌های ده سال پیش تن بدهد.', 'uid-theme' ); ?></p>
	            <p><?php esc_html_e( 'نتیجه، امنیت و ثباتی است که به فعالیت‌های آنلاین می‌آید، و فضایی که افراد و کسب‌وکارها می‌توانند در آن به یکدیگر اعتماد کنند. در سه کلمه: قابل اعتماد، مدرن و حرفه‌ای.', 'uid-theme' ); ?></p>
	            <div class="ab-arch-traits"><span><?php esc_html_e( 'قابل اعتماد', 'uid-theme' ); ?></span><span><?php esc_html_e( 'مدرن', 'uid-theme' ); ?></span><span><?php esc_html_e( 'حرفه‌ای', 'uid-theme' ); ?></span></div>
	          </div>
	        </div>

	        <div class="ab-msg">
	          <p class="stmt"><?php echo esc_html( uid_section_val( 'abidentity', 'stmt', __( 'هر کاری که می‌کنیم به یک جمله برمی‌گردد: احراز هویت امن و آسان در فضای آنلاین.', 'uid-theme' ) ) ); ?></p>
	          <p class="sig"><span class="hx" aria-hidden="true"></span><?php echo esc_html( uid_section_val( 'abidentity', 'sig', __( 'یوآیدی، اپراتور اول احراز هویت ایران', 'uid-theme' ) ) ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۵) ارزش‌های کلیدی
 * ===================================================================== */
function uid_default_ab_values() {
	return array(
		array( 'icon' => 'security',    'title' => 'امنیت', 'text' => 'یوآیدی به امنیت اطلاعات کاربران خود متعهد است و از بالاترین سطح امنیت برای احراز هویت آن‌ها استفاده می‌کند.' ),
		array( 'icon' => 'speed',       'title' => 'سرعت', 'text' => 'فرآیند احراز هویت به سرعت و به آسانی انجام می‌شود تا کاربران بتوانند در کمترین زمان ممکن به خدمات مورد نیاز خود دسترسی پیدا کنند.' ),
		array( 'icon' => 'ease',        'title' => 'آسانی استفاده', 'text' => 'یوآیدی رابط کاربری ساده و آسانی را برای کاربران خود ارائه می‌دهد تا فرآیند احراز هویت را به آسانی انجام دهند.' ),
		array( 'icon' => 'reliability', 'title' => 'قابلیت اطمینان', 'text' => 'یوآیدی پلتفرمی پایدار و قابل اعتماد است که کاربران می‌توانند به آن اعتماد کنند.' ),
		array( 'icon' => 'variety',     'title' => 'تنوع خدمات', 'text' => 'یوآیدی طیف گسترده‌ای از خدمات احراز هویت را برای نیازهای مختلف کاربران ارائه می‌دهد.' ),
	);
}
function uid_ab_val_icon_key( $key ) {
	$map = array( 'security' => 'lock-shield', 'speed' => 'speed-bolt', 'ease' => 'smile-ease', 'reliability' => 'shield-check', 'variety' => 'squares4' );
	return $map[ $key ] ?? 'lock-shield';
}
function uid_render_section_abvalues() {
	$tag   = uid_section_tag( 'abvalues', 'h2' );
	$items = uid_section_val( 'abvalues', 'items', uid_default_ab_values() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" id="values" data-fold data-fold-min="۲ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'پنج ارزشی که سرویس را شکل می‌دهد', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'امنیت، سرعت، آسانی استفاده، قابلیت اطمینان، تنوع خدمات', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 4 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abvalues', 'eyebrow', __( 'ارزش‌های کلیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abvalues', 'heading', __( 'پنج چیزی که سرشان کوتاه نمی‌آییم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'abvalues', 'text', __( 'هر تصمیمی که درباره سرویس می‌گیریم — از طراحی یک فرم تا معماری زیرساخت — با همین پنج معیار سنجیده می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="deck-wrap rv rv-d1">
	      <div class="ab-vals deck" data-deck="vals">
	        <?php foreach ( $items as $it ) : ?>
	        <article class="ab-val">
	          <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_icon_svg( uid_ab_val_icon_key( $it['icon'] ?? 'security' ) ), uid_ab_svg_kses() ); ?></svg></span>
	          <b><?php echo esc_html( $it['title'] ?? '' ); ?></b>
	          <p><?php echo esc_html( $it['text'] ?? '' ); ?></p>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="deck-ui" data-deck-ui="vals">
	        <button class="deck-btn" type="button" data-deck-prev aria-label="<?php esc_attr_e( 'کارت قبلی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	        <span class="deck-bar"><i></i></span>
	        <span class="deck-count"></span>
	        <button class="deck-btn" type="button" data-deck-next aria-label="<?php esc_attr_e( 'کارت بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	      </div>
	      <div class="deck-hint"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> <?php esc_html_e( 'برای دیدن ارزش بعدی، بکشید', 'uid-theme' ); ?></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۶) نقشه توانمندی پلتفرم — لایه‌ها و پنل جزئیات، ثابت/غیرقابل‌ویرایش (باید با
 * LAYERS در about-us-page.js دقیقاً هماهنگ بماند)
 * ===================================================================== */
function uid_ab_platform_bands() {
	return array(
		array( 'class' => 'l0 on', 'icon' => '<rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 21h8M12 18v3"/>', 'title' => 'سطح تحویل', 'text' => 'راهی که کسب‌وکار شما به این زیرساخت وصل می‌شود', 'count' => 3 ),
		array( 'class' => 'l1',    'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>', 'title' => 'اعتبارسنجی مالی', 'text' => 'تطبیق مالکیت حساب و کارت با هویت', 'count' => 4 ),
		array( 'class' => 'l2',    'icon' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>', 'title' => 'سرویس‌های هویتی', 'text' => 'اتصال مستقیم به سامانه‌های رسمی کشور', 'count' => 5 ),
		array( 'class' => 'l3 base', 'icon' => '<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/>', 'title' => 'موتورهای زیستی و پردازش تصویر', 'text' => 'پایه‌ای که سه لایه بالا روی آن ایستاده‌اند', 'count' => 3 ),
	);
}
function uid_render_section_abplatform() {
	$tag   = uid_section_tag( 'abplatform', 'h2' );
	$bands = uid_ab_platform_bands();
	?>
	<section class="sec" id="platform" data-fold data-fold-hot data-fold-min="۳ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'پانزده سرویس، یک زیرساخت', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'نقشه لایه‌های پلتفرم یوآیدی', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 5 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abplatform', 'eyebrow', __( 'نقشه توانمندی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abplatform', 'heading', __( 'ما یک فرم احراز هویت نیستیم؛ یک زیرساخت هویت هستیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'abplatform', 'text', __( 'پانزده سرویس یوآیدی، پانزده محصول جدا نیستند. چهار لایه از یک زیرساخت واحدند که روی هم سوار شده‌اند. روی هر لایه بزنید تا ببینید داخلش چیست.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="ab-cmap rv rv-d1">
	      <div>
	        <div class="ab-stack" id="cmapStack" role="tablist" aria-label="<?php esc_attr_e( 'لایه‌های پلتفرم', 'uid-theme' ); ?>">
	          <?php foreach ( $bands as $i => $b ) : ?>
	          <button class="ab-band <?php echo esc_attr( $b['class'] ); ?>" data-band="<?php echo esc_attr( $i ); ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>">
	            <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( $b['icon'], array( 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'path' => array( 'd' => true ) ) ); ?></svg></span>
	            <span class="tx"><b><?php echo esc_html( $b['title'] ); ?></b><span><?php echo esc_html( $b['text'] ); ?></span></span>
	            <span class="cnt"><?php echo esc_html( uid_fa_digits( $b['count'] ) ); ?></span></button>
	          <?php endforeach; ?>
	        </div>
	        <div class="ab-stack-foot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	          <span><?php echo esc_html( uid_section_val( 'abplatform', 'foot_text', __( 'هر لایه روی لایه زیرین خود کار می‌کند. به همین دلیل یک قرارداد و یک یکپارچه‌سازی، به تمام لایه‌ها دسترسی می‌دهد.', 'uid-theme' ) ) ); ?></span></div>
	      </div>

	      <div class="ab-cdetail" id="cmapDetail" aria-live="polite"></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۷) کارنامه عملیاتی
 * ===================================================================== */
function uid_default_ab_record() {
	return array(
		array( 'icon' => 'trust',    'value' => '+۵٬۰۰۰٬۰۰۰', 'value_type' => 'number', 'label' => 'کل احراز هویت موفق انجام‌شده از ۱۳۹۶ تا امروز', 'meter' => '' ),
		array( 'icon' => 'growth',   'value' => '+۶۰٬۰۰۰',    'value_type' => 'number', 'label' => 'بیشترین احراز هویت موفق در یک روز', 'meter' => '' ),
		array( 'icon' => 'uptime',   'value' => '۹۹٫۵٪',       'value_type' => 'number', 'label' => 'آپ‌تایم تعهدی سرویس', 'meter' => '99.5' ),
		array( 'icon' => 'accuracy', 'value' => '۹۹٪',         'value_type' => 'number', 'label' => 'دقت هوش مصنوعی در تطبیق چهره با تصویر مرجع', 'meter' => '99' ),
		array( 'icon' => 'legal',    'value' => 'زیر ۳۰ دقیقه', 'value_type' => 'text',   'label' => 'ارسال استعلام به پلیس فتا و مراجع قضائی', 'meter' => '' ),
		array( 'icon' => 'latency',  'value' => 'زیر ۲ ثانیه',  'value_type' => 'text',   'label' => 'میانگین زمان پاسخ وب‌سرویس‌های استعلامی', 'meter' => '' ),
	);
}
function uid_ab_rec_icon_key( $key ) {
	$map = array( 'trust' => 'trust-people', 'growth' => 'cost-arrow', 'uptime' => 'shield-check', 'accuracy' => 'face-scan', 'legal' => 'scale', 'latency' => 'clock' );
	return $map[ $key ] ?? 'trust-people';
}
function uid_render_section_abrecord() {
	$tag   = uid_section_tag( 'abrecord', 'h2' );
	$items = uid_section_val( 'abrecord', 'items', uid_default_ab_record() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="record" data-fold data-fold-hot data-fold-min="۲ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'کارنامه عملیاتی', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'اعدادی که تیم ریسک شما می‌پرسد', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 6 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abrecord', 'eyebrow', __( 'کارنامه عملیاتی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abrecord', 'heading', __( 'زیرساختی که سال‌هاست زیر بار واقعی کار می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>

	    <div class="ab-rec rv rv-d1">
	      <div class="ab-recs">
	        <?php foreach ( $items as $it ) :
	        	$is_txt = 'text' === ( $it['value_type'] ?? 'number' );
	        	$meter  = trim( (string) ( $it['meter'] ?? '' ) );
	        	?>
	        <article class="ab-rst">
	          <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_icon_svg( uid_ab_rec_icon_key( $it['icon'] ?? 'trust' ) ), uid_ab_svg_kses() ); ?></svg></span>
	          <span class="v<?php echo $is_txt ? ' txt' : ''; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	          <span class="l"><?php echo esc_html( $it['label'] ?? '' ); ?></span>
	          <?php if ( '' !== $meter && is_numeric( $meter ) ) : ?>
	          <span class="meter" style="--p:<?php echo esc_attr( $meter ); ?>%"><i></i></span>
	          <?php endif; ?>
	        </article>
	        <?php endforeach; ?>
	      </div>
	      <div class="ab-rec-foot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	        <span><?php echo wp_kses( uid_section_val( 'abrecord', 'foot_text', __( 'آپ‌تایم و زمان پاسخ هر سرویس، در قرارداد <span class="lat">SLA</span> فیمابین به‌صورت جداگانه تعیین و تضمین می‌شود.', 'uid-theme' ) ), array( 'span' => array( 'class' => true ) ) ); ?></span></div>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۸) تعهدهای یوآیدی به مشتری
 * ===================================================================== */
function uid_default_ab_vows() {
	return array(
		array( 'icon' => 'security',    'title' => 'پایداری و امنیت سرویس', 'text' => 'تعهد به <span class="lat">SLA</span> فیمابین و رعایت استانداردهای امنیتی، با ارتباط رمزنگاری‌شده در تمام مسیر درخواست و پاسخ.' ),
		array( 'icon' => 'multivendor', 'title' => 'معماری چندتامین‌کننده', 'text' => 'برای هر سرویس از چند تامین‌کننده استفاده می‌شود (<span class="lat">Multi-Vendor</span>)، تا قطعی یا خطای یکی، کل فرآیند شما را متوقف نکند.' ),
		array( 'icon' => 'legal',       'title' => 'پاسخ‌گویی استعلامات قضائی', 'text' => 'پاسخ‌گویی استعلامات قضائی (فتا) در کمتر از یک ساعت — این بار از دوش تیم حقوقی و پشتیبانی شما برداشته می‌شود.' ),
		array( 'icon' => 'fdp',         'title' => 'دسترسی رایگان به <span class="lat">FDP</span>', 'text' => 'سرویس کشف و پیشگیری از تقلب یوآیدی، بدون هزینه جداگانه در اختیار پذیرندگان یوآیدی‌پلاس قرار می‌گیرد.' ),
		array( 'icon' => 'support24',   'title' => 'پشتیبانی ۲۴ ساعته و راهبری کاربران', 'text' => 'هدایت کاربران تا تکمیل فرآیند احراز هویت را یوآیدی انجام می‌دهد؛ مرکز تماس شما درگیر پیگیری کاربران نیمه‌کاره نمی‌شود.' ),
		array( 'icon' => 'sandbox',     'title' => 'سندباکس پیش از هر تعهد مالی', 'text' => 'دسترسی به محیط آزمایشی و مستندات کامل، پیش از امضای قرارداد — تا تیم فنی شما پیش از تصمیم، سرویس را روی ترافیک واقعی خودش بسنجد.' ),
	);
}
function uid_ab_vow_icon_key( $key ) {
	$map = array( 'security' => 'lock-shield', 'multivendor' => 'multivendor', 'legal' => 'scale', 'fdp' => 'fdp-check', 'support24' => 'support24', 'sandbox' => 'sandbox-brack' );
	return $map[ $key ] ?? 'lock-shield';
}
function uid_ab_vow_kses() {
	return array( 'span' => array( 'class' => true ) );
}
function uid_render_section_abvows() {
	$tag   = uid_section_tag( 'abvows', 'h2' );
	$items = uid_section_val( 'abvows', 'items', uid_default_ab_vows() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="vows" data-fold data-fold-hot data-fold-min="۲ دقیقه"
	         data-fold-title="<?php esc_attr_e( 'تعهد ما به تیم شما', 'uid-theme' ); ?>"
	         data-fold-teaser="<?php esc_attr_e( 'آنچه فراتر از خودِ احراز هویت تحویل می‌گیرید', 'uid-theme' ); ?>">
	  <div class="fold-body"><div class="ab-wrap"><div class="fold-inner">
	    <div class="sec-head mid rv">
	      <?php echo uid_ab_sec_mark( 7 ); ?>
	      <span class="ab-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'abvows', 'eyebrow', __( 'تعهدها', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'abvows', 'heading', __( 'آنچه تیم ریسک و تیم فنی شما واقعاً تحویل می‌گیرد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede" style="margin-inline:auto"><?php echo esc_html( uid_section_val( 'abvows', 'text', __( 'شما یک ابزار نمی‌خرید؛ یک تیم متخصص را کنار خودتان می‌آورید. آنچه در ادامه می‌آید، تعهد ما به هر سازمانی است که با ما کار می‌کند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="ab-vows rail rv rv-d1" data-rail="vows">
	      <?php foreach ( $items as $it ) : ?>
	      <article class="ab-vow">
	        <span class="hx"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_ab_icon_svg( uid_ab_vow_icon_key( $it['icon'] ?? 'security' ) ), uid_ab_svg_kses() ); ?></svg></span>
	        <b><?php echo wp_kses( $it['title'] ?? '', uid_ab_vow_kses() ); ?></b>
	        <p><?php echo wp_kses( $it['text'] ?? '', uid_ab_vow_kses() ); ?></p></article>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="vows"></div>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * ۹) بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_ablead() {
	$tag = uid_section_tag( 'ablead', 'h2' );
	$trust_raw = uid_section_val( 'ablead', 'trust', "مشاوره رایگان، بدون تعهد\nدسترسی به سندباکس پیش از قرارداد\nمستندات فنی کامل برای تیم توسعه\nپشتیبانی اختصاصی سازمانی" );
	$trust = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$topic_raw = uid_section_val( 'ablead', 'topic_options', "ارزیابی زیرساخت برای بانک یا موسسه مالی\nارزیابی برای صرافی رمزارز یا فین‌تک\nیکپارچه‌سازی وب‌سرویس‌های استعلامی\nبرون‌سپاری کامل احراز هویت (یوآیدی‌پلاس)\nهمکاری، رسانه یا سایر موارد سازمانی\nاحراز هویت شخصی من در سامانه ثنا" );
	$topics = array_values( array_filter( array_map( 'trim', explode( "\n", $topic_raw ) ) ) );
	$topic_count = count( $topics );
	?>
	<section class="sec" id="lead" style="padding-block:clamp(52px,6vw,88px) var(--sec)">
	  <div class="ab-wrap" style="padding-inline:0">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="ab-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'ablead', 'eyebrow', __( 'گفت‌وگو با تیم یوآیدی', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'ablead', 'heading', __( 'اگر ارزیابی فنی یا انطباقی در دستور کار شماست، از همین‌جا شروع کنیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo wp_kses( uid_section_val( 'ablead', 'text', __( 'شماره‌تان را بگذارید تا کارشناس یوآیدی تماس بگیرد: معرفی زیرساخت، دسترسی به محیط سندباکس، مستندات فنی و شرایط <span class="lat">SLA</span> — پیش از هر تعهد مالی. یا مستقیم با <span class="mono">02166123290</span> تماس بگیرید.', 'uid-theme' ) ), array( 'span' => array( 'class' => true ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg> <?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>

	        <form class="lb-form" id="bandForm" novalidate>
	          <h3><?php echo esc_html( uid_section_val( 'ablead', 'form_title', __( 'درخواست گفت‌وگو', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'ablead', 'form_hint', __( 'سه فیلد — کمتر از ۲۰ ثانیه وقت می‌گیرد.', 'uid-theme' ) ) ); ?></p>

	          <div class="route-alert" id="routeAlert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	            <span><?php esc_html_e( 'اگر برای', 'uid-theme' ); ?> <b><?php esc_html_e( 'خودتان', 'uid-theme' ); ?></b> <?php esc_html_e( 'دنبال احراز هویت ثنا هستید، این فرم مخصوص کسب‌وکارهاست. سریع‌ترین مسیر شما', 'uid-theme' ); ?> <a href="/sana/"><?php esc_html_e( 'صفحه سامانه ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'است.', 'uid-theme' ); ?></span></div>

	          <div class="frow">
	            <div class="fld"><input id="b_name" name="name" type="text"
	                 placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'این فیلد الزامی است.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><input id="b_tel" name="phone" type="tel" inputmode="numeric"
	                 placeholder="<?php esc_attr_e( 'شماره تماس — ۰۹xxxxxxxxx', 'uid-theme' ); ?>" data-req data-tel>
	              <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	          </div>
	          <div class="fld">
	            <select id="b_type" name="intent" data-req data-route>
	              <option value=""><?php esc_html_e( 'این درخواست برای چیست؟', 'uid-theme' ); ?></option>
	              <?php foreach ( $topics as $i => $t ) :
	              	$is_last = ( $i === $topic_count - 1 );
	              	?>
	              <option value="<?php echo $is_last ? 'ind' : 'biz'; ?>"><?php echo esc_html( $t ); ?></option>
	              <?php endforeach; ?>
	            </select>
	            <span class="err"><?php esc_html_e( 'لطفاً یک گزینه انتخاب کنید.', 'uid-theme' ); ?></span>
	          </div>
	          <input type="hidden" name="source" value="about-us">

	          <button class="ab-btn ab-btn-cta ab-btn-block" type="button" data-submit>
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'درخواست تماس', 'uid-theme' ); ?></button>

	          <div class="lb-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/></svg>
	            <span><?php echo esc_html( uid_section_val( 'ablead', 'note_text', __( 'شماره شما فقط برای تماس کارشناس استفاده می‌شود و در اختیار شخص ثالث قرار نمی‌گیرد.', 'uid-theme' ) ) ); ?></span></div>

	          <div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><b><?php echo esc_html( uid_section_val( 'ablead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	            <span><?php echo esc_html( uid_section_val( 'ablead', 'success_text', __( 'تیم یوآیدی به‌زودی تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </form>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/**
 * مودال گفت‌وگوی سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_ab_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'گفت‌وگو با کارشناس یوآیدی', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی تماس بگیرد: معرفی زیرساخت، دسترسی به محیط سندباکس، مستندات فنی و شرایط', 'uid-theme' ); ?> <span class="lat">SLA</span> <?php esc_html_e( '— پیش از هر تعهد مالی.', 'uid-theme' ); ?></p>
	    <form id="modalForm" novalidate>
	<div class="cta-fields">
	  <div class="fld"><label for="m_name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	    <input id="m_name" name="name" type="text" placeholder="<?php esc_attr_e( 'مثلاً علی رضایی', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_biz"><?php esc_html_e( 'نام کسب‌وکار', 'uid-theme' ); ?></label>
	    <input id="m_biz" name="business" type="text" placeholder="<?php esc_attr_e( 'مثلاً فروشگاه اینترنتی...', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_tel"><?php esc_html_e( 'شماره تماس', 'uid-theme' ); ?></label>
	    <input id="m_tel" name="phone" type="tel" inputmode="numeric" placeholder="09xxxxxxxxx" data-req data-tel>
	    <span class="err"><?php esc_html_e( 'شماره موبایل معتبر با فرمت ۰۹xxxxxxxxx وارد کنید.', 'uid-theme' ); ?></span></div>
	  <div class="fld"><label for="m_loc"><?php esc_html_e( 'نوع سازمان', 'uid-theme' ); ?></label>
	    <select id="m_loc" name="biztype" data-req>
	      <option value=""><?php esc_html_e( 'انتخاب کنید…', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بانک، نئوبانک یا فین‌تک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'کارگزاری بورس', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'صرافی رمزارز', 'uid-theme' ); ?></option><option><?php esc_html_e( 'شرکت بیمه', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'اپراتور تلفن همراه', 'uid-theme' ); ?></option><option><?php esc_html_e( 'درگاه و پلتفرم پرداخت', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سامانه اعتباری و لندتک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'مارکت‌پلیس', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سازمان دولتی یا عمومی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سایر سازمان‌ها', 'uid-theme' ); ?></option>
	    </select></div>
	  <button class="btn btn-cta btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'درخواست تماس و مشاوره رایگان', 'uid-theme' ); ?></button>
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

/* =====================================================================
 * برگه واقعی خودکارساخته + اسلاگ قابل‌ویرایش
 * ===================================================================== */
function uid_get_ab_page_id() {
	$page_id = (int) get_option( 'uid_ab_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_AB_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_ab_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_ab_page() {
	if ( uid_get_ab_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'درباره ما', 'uid-theme' ),
		'post_name'   => 'about-us-new',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_AB_TEMPLATE );
	update_option( 'uid_ab_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_ab_slug_setting() {
	register_setting( 'uid_ab_group', 'uid_ab_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_ab_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_ab_page_slug_section', '', '__return_false', 'uid_ab_layout' );
	add_settings_field( 'uid_ab_page_slug', __( 'آدرس (اسلاگ) صفحه درباره ما', 'uid-theme' ), 'uid_field_ab_page_slug', 'uid_ab_layout', 'uid_ab_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_ab_slug_setting' );

function uid_field_ab_page_slug( $args ) {
	$page_id = uid_get_ab_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_ab_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_ab_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه درباره ما هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_ab_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_ab_page_id();

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
function uid_register_ab_settings() {
	register_setting( 'uid_ab_group', 'uid_ab_layout', array(
		'sanitize_callback' => 'uid_sanitize_ab_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_ab_layout_main', '', '__return_false', 'uid_ab_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_ab_layout', 'uid_ab_layout_main', array(
		'option_name' => 'uid_ab_layout', 'registry_fn' => 'uid_ab_sections_registry', 'layout_fn' => 'uid_get_ab_layout',
	) );

	$icon_opts_honey = array( 'clock' => __( 'ساعت', 'uid-theme' ), 'danesh' => __( 'دانش‌بنیان', 'uid-theme' ), 'sejam' => __( 'سند رسمی', 'uid-theme' ), 'medal' => __( 'مدال', 'uid-theme' ) );
	$icon_opts_mission = array( 'crime' => __( 'کاهش جرم', 'uid-theme' ), 'trust' => __( 'اعتماد', 'uid-theme' ), 'ease' => __( 'آسانی', 'uid-theme' ), 'cost' => __( 'هزینه', 'uid-theme' ), 'speed' => __( 'سرعت', 'uid-theme' ) );
	$icon_opts_values = array( 'security' => __( 'امنیت', 'uid-theme' ), 'speed' => __( 'سرعت', 'uid-theme' ), 'ease' => __( 'آسانی', 'uid-theme' ), 'reliability' => __( 'اطمینان', 'uid-theme' ), 'variety' => __( 'تنوع', 'uid-theme' ) );
	$icon_opts_record = array( 'trust' => __( 'اعتماد', 'uid-theme' ), 'growth' => __( 'رشد', 'uid-theme' ), 'uptime' => __( 'آپ‌تایم', 'uid-theme' ), 'accuracy' => __( 'دقت', 'uid-theme' ), 'legal' => __( 'حقوقی', 'uid-theme' ), 'latency' => __( 'زمان پاسخ', 'uid-theme' ) );
	$icon_opts_vows = array( 'security' => __( 'امنیت', 'uid-theme' ), 'multivendor' => __( 'چندتامین‌کننده', 'uid-theme' ), 'legal' => __( 'حقوقی', 'uid-theme' ), 'fdp' => __( 'ضدتقلب', 'uid-theme' ), 'support24' => __( 'پشتیبانی ۲۴س', 'uid-theme' ), 'sandbox' => __( 'سندباکس', 'uid-theme' ) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abhero', array( 'sanitize_callback' => 'uid_sanitize_section_abhero', 'default' => array() ) );
	add_settings_section( 'uid_section_abhero_main', '', '__return_false', 'uid_section_abhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'eyebrow', 'default' => 'درباره یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'heading', 'default' => 'اولین پلتفرم احراز هویت دیجیتال ایران، <mark>از ۱۳۹۶ تا امروز</mark>' ) );
	add_settings_field( 'lede', __( 'توضیح (تگ‌های b و span مجازند)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'lede', 'default' => 'یوآیدی نام تجاری <b style="color:#fff">شرکت دانش‌بنیان بینش هوشمند نسل پیشرو</b> است — کارگزار مورد تایید سامانه‌های سجام و ثنا. هویت افراد را بدون مراجعه حضوری و با اتکا به هوش مصنوعی، فناوری‌های <span class="lat">OCR</span>، تطبیق چهره و تشخیص زنده‌بودن احراز می‌کنیم.' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (پرش نرم به نقشه توانمندی)', 'uid-theme' ), 'uid_field_text', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'btn2_text', 'default' => 'نقشه توانمندی پلتفرم' ) );
	add_settings_field( 'hero_tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abhero', 'uid_section_abhero_main', array( 'group' => 'uid_section_abhero', 'key' => 'hero_tags', 'default' => "شرکت دانش‌بنیان\nکارگزار مورد تایید سجام و ثنا\nاولین ارائه‌دهنده ثنای آنلاین برای ایرانیان خارج از کشور" ) );
	add_settings_field( 'honey', __( 'لانه‌زنبوری ۴ واقعیت (کنار هیرو)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abhero', 'uid_section_abhero_main', array(
		'group' => 'uid_section_abhero', 'key' => 'honey', 'default' => uid_default_ab_honey(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => $icon_opts_honey ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'capsule', __( 'کپسول ۴ آمار ۳۰ ثانیه‌ای (زیر هیرو)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abhero', 'uid_section_abhero_main', array(
		'group' => 'uid_section_abhero', 'key' => 'capsule', 'default' => uid_default_ab_capsule(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مسیریابی مخاطب ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abrouter', array( 'sanitize_callback' => 'uid_sanitize_section_abrouter', 'default' => array() ) );
	add_settings_section( 'uid_section_abrouter_main', '', '__return_false', 'uid_section_abrouter' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'eyebrow', 'default' => 'مسیر خود را انتخاب کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'heading', 'default' => 'کدام بخش از یوآیدی به شما مربوط می‌شود؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'text', 'default' => 'نیاز یک بانک با نیاز یک فرد یکسان نیست — و مسیرشان هم نباید یکسان باشد. انتخاب کنید کدام هستید تا مستقیم به همان چیزی برسید که به شما مربوط است.' ) );

	add_settings_field( 'biz_tag', __( 'تب کسب‌وکارها — برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'biz_tag', 'default' => 'بانک · صرافی · بیمه · اپراتور' ) );
	add_settings_field( 'biz_heading', __( 'تب کسب‌وکارها — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'biz_heading', 'default' => 'کسب‌وکارها' ) );
	add_settings_field( 'biz_p1', __( 'تب کسب‌وکارها — پاراگراف اول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'biz_p1', 'default' => 'یوآیدی برای طیف گسترده‌ای از کسب‌وکارها، از جمله بانک‌ها، کارگزاری‌ها، صرافی‌های رمز ارز، شرکت‌های بیمه، اپراتورهای تلفن همراه و… مناسب است.' ) );
	add_settings_field( 'biz_p2', __( 'تب کسب‌وکارها — پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'biz_p2', 'default' => 'اگر مسئول ریسک، انطباق یا فناوری در یکی از این سازمان‌ها هستید، مسیر کوتاه این است: یا کل فرآیند احراز هویت را برون‌سپاری کنید، یا وب‌سرویس‌های استعلامی را مستقیم در سامانه خودتان بنشانید.' ) );

	add_settings_field( 'ind_tag', __( 'تب افراد حقیقی — برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ind_tag', 'default' => 'خدمات فردی' ) );
	add_settings_field( 'ind_heading', __( 'تب افراد حقیقی — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ind_heading', 'default' => 'افراد حقیقی' ) );
	add_settings_field( 'ind_p1', __( 'تب افراد حقیقی — پاراگراف اول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ind_p1', 'default' => 'یوآیدی برای تمام افرادی که نیاز به احراز هویت در فضای آنلاین دارند، مانند افتتاح حساب بانکی، ثبت نام در سجام، احراز هویت در صرافی‌های رمز ارز و… مناسب است.' ) );
	add_settings_field( 'ind_p2', __( 'تب افراد حقیقی — پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ind_p2', 'default' => 'اگر برای خودتان دنبال احراز هویت هستید، لازم نیست با ما تماس بگیرید — مسیر مستقیم همین پایین است.' ) );
	add_settings_field( 'ind_note', __( 'تب افراد حقیقی — یادداشت هدایت (تگ a مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ind_note', 'default' => 'فرم انتهای این صفحه مخصوص کسب‌وکارهاست. برای خدمات شخصی، مستقیم به <a href="/sana/">صفحه احراز هویت ثنا</a> بروید — سریع‌تر به نتیجه می‌رسید.' ) );

	add_settings_field( 'ceo_tag', __( 'تب مدیران عامل — برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ceo_tag', 'default' => 'امضای الکترونیکی' ) );
	add_settings_field( 'ceo_heading', __( 'تب مدیران عامل — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ceo_heading', 'default' => 'مدیران عامل' ) );
	add_settings_field( 'ceo_p1', __( 'تب مدیران عامل — پاراگراف اول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ceo_p1', 'default' => 'یوآیدی راهکاری امن برای احراز هویت مدیران عامل و انجام امضاهای الکترونیکی است.' ) );
	add_settings_field( 'ceo_p2', __( 'تب مدیران عامل — پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'ceo_p2', 'default' => 'احراز هویتی که پای یک امضای الکترونیکی می‌نشیند باید ثابت کند امضاکننده در همان لحظه واقعاً حاضر بوده است — نه اینکه صرفاً یک کد پیامکی را وارد کرده باشد.' ) );

	add_settings_field( 'emp_tag', __( 'تب کارمندان — برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'emp_tag', 'default' => 'احراز هویت سازمانی' ) );
	add_settings_field( 'emp_heading', __( 'تب کارمندان — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'emp_heading', 'default' => 'کارمندان' ) );
	add_settings_field( 'emp_p1', __( 'تب کارمندان — پاراگراف اول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'emp_p1', 'default' => 'یوآیدی می‌تواند برای احراز هویت کارمندان و ارائه خدمات داخلی به آن‌ها در سازمان‌ها و شرکت‌ها مورد استفاده قرار گیرد.' ) );
	add_settings_field( 'emp_p2', __( 'تب کارمندان — پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'group' => 'uid_section_abrouter', 'key' => 'emp_p2', 'default' => 'همان زیرساختی که هویت مشتری را احراز می‌کند، برای احراز هویت نیروی داخلی و دسترسی‌های سازمانی هم به کار می‌آید — بدون قرارداد و یکپارچه‌سازی جداگانه.' ) );

	add_settings_field( 'routes_notice', '', 'uid_field_notice', 'uid_section_abrouter', 'uid_section_abrouter_main', array( 'text' => __( 'کارت‌های مسیریابی هر تب (آیکون، عنوان، لینک) ثابت‌اند و از پیشخوان قابل‌ویرایش نیستند — برای تغییر، تابع uid_ab_router_routes() در inc/about-us-page.php را ویرایش کنید.', 'uid-theme' ) ) );

	/* ---------------- مأموریت ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abmission', array( 'sanitize_callback' => 'uid_sanitize_section_abmission', 'default' => array() ) );
	add_settings_section( 'uid_section_abmission_main', '', '__return_false', 'uid_section_abmission' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'eyebrow', 'default' => 'مأموریت' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'heading', 'default' => 'احراز هویتی که هم امن باشد، هم آسان، هم سریع' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'text', 'default' => 'مأموریت یوآیدی، ارائه راهکاری امن، آسان و سریع برای احراز هویت در فضای آنلاین است — به منظور تسهیل و امن‌تر کردن تراکنش‌ها و فعالیت‌های آنلاین برای افراد و کسب‌وکارها.' ) );
	add_settings_field( 'rows', __( 'ردیف‌های هدف مأموریت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abmission', 'uid_section_abmission_main', array(
		'group' => 'uid_section_abmission', 'key' => 'rows', 'default' => uid_default_ab_mission_rows(), 'add_label' => __( 'افزودن هدف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => $icon_opts_mission ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'quote_text', __( 'متن نقل‌قول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'quote_text', 'default' => 'در تلاشیم به‌عنوان پلتفرم پیشرو احراز هویت دیجیتال در ایران شناخته شویم و نقشی اساسی در توسعه تجارت الکترونیک و اقتصاد دیجیتال ایفا کنیم.' ) );
	add_settings_field( 'by_name', __( 'نقل‌قول از — نام', 'uid-theme' ), 'uid_field_text', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'by_name', 'default' => 'تیم یوآیدی' ) );
	add_settings_field( 'by_role', __( 'نقل‌قول از — نقش/سازمان', 'uid-theme' ), 'uid_field_text', 'uid_section_abmission', 'uid_section_abmission_main', array( 'group' => 'uid_section_abmission', 'key' => 'by_role', 'default' => 'بینش هوشمند نسل پیشرو' ) );

	/* ---------------- هویت برند ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abidentity', array( 'sanitize_callback' => 'uid_sanitize_section_abidentity', 'default' => array() ) );
	add_settings_section( 'uid_section_abidentity_main', '', '__return_false', 'uid_section_abidentity' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'eyebrow', 'default' => 'ما که هستیم' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'heading', 'default' => 'یک شرکت زیرساختی، دو نیروی متضاد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'text', 'default' => 'اغلب شرکت‌ها یکی از این دو را انتخاب می‌کنند: یا قابل اتکا و بی‌تحرک، یا نوآور و بی‌ثبات. ما از ابتدا روی هر دو بنا شده‌ایم — و همان نقطه‌ای که این دو به هم می‌رسند، جایی است که یوآیدی ایستاده است.' ) );
	add_settings_field( 'fig_notice', '', 'uid_field_notice', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'text' => __( 'شکل هندسی سه‌وجهی (نظم/دانش/یوآیدی) و متن هر سه پنل، ثابت و از پیشخوان قابل‌ویرایش نیست — برای تغییر، uid_render_section_abidentity() در inc/about-us-page.php را ویرایش کنید.', 'uid-theme' ) ) );
	add_settings_field( 'stmt', __( 'جمله پایانی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'stmt', 'default' => 'هر کاری که می‌کنیم به یک جمله برمی‌گردد: احراز هویت امن و آسان در فضای آنلاین.' ) );
	add_settings_field( 'sig', __( 'امضای زیر جمله پایانی', 'uid-theme' ), 'uid_field_text', 'uid_section_abidentity', 'uid_section_abidentity_main', array( 'group' => 'uid_section_abidentity', 'key' => 'sig', 'default' => 'یوآیدی، اپراتور اول احراز هویت ایران' ) );

	/* ---------------- ارزش‌های کلیدی ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abvalues', array( 'sanitize_callback' => 'uid_sanitize_section_abvalues', 'default' => array() ) );
	add_settings_section( 'uid_section_abvalues_main', '', '__return_false', 'uid_section_abvalues' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abvalues', 'uid_section_abvalues_main', array( 'group' => 'uid_section_abvalues', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abvalues', 'uid_section_abvalues_main', array( 'group' => 'uid_section_abvalues', 'key' => 'eyebrow', 'default' => 'ارزش‌های کلیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abvalues', 'uid_section_abvalues_main', array( 'group' => 'uid_section_abvalues', 'key' => 'heading', 'default' => 'پنج چیزی که سرشان کوتاه نمی‌آییم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abvalues', 'uid_section_abvalues_main', array( 'group' => 'uid_section_abvalues', 'key' => 'text', 'default' => 'هر تصمیمی که درباره سرویس می‌گیریم — از طراحی یک فرم تا معماری زیرساخت — با همین پنج معیار سنجیده می‌شود.' ) );
	add_settings_field( 'items', __( 'کارت‌های ارزش', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abvalues', 'uid_section_abvalues_main', array(
		'group' => 'uid_section_abvalues', 'key' => 'items', 'default' => uid_default_ab_values(), 'add_label' => __( 'افزودن ارزش', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => $icon_opts_values ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- نقشه توانمندی ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abplatform', array( 'sanitize_callback' => 'uid_sanitize_section_abplatform', 'default' => array() ) );
	add_settings_section( 'uid_section_abplatform_main', '', '__return_false', 'uid_section_abplatform' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'group' => 'uid_section_abplatform', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'group' => 'uid_section_abplatform', 'key' => 'eyebrow', 'default' => 'نقشه توانمندی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'group' => 'uid_section_abplatform', 'key' => 'heading', 'default' => 'ما یک فرم احراز هویت نیستیم؛ یک زیرساخت هویت هستیم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'group' => 'uid_section_abplatform', 'key' => 'text', 'default' => 'پانزده سرویس یوآیدی، پانزده محصول جدا نیستند. چهار لایه از یک زیرساخت واحدند که روی هم سوار شده‌اند. روی هر لایه بزنید تا ببینید داخلش چیست.' ) );
	add_settings_field( 'bands_notice', '', 'uid_field_notice', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'text' => __( 'چهار لایه (عنوان/توضیح/شمار/سرویس‌های داخل هر لایه) ثابت‌اند و باید با assets/js/about-us-page.js هماهنگ بمانند؛ از پیشخوان قابل‌ویرایش نیستند — برای تغییر uid_ab_platform_bands() در inc/about-us-page.php و آرایه LAYERS در فایل جاوااسکریپت را با هم ویرایش کنید.', 'uid-theme' ) ) );
	add_settings_field( 'foot_text', __( 'یادداشت زیر لایه‌ها', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abplatform', 'uid_section_abplatform_main', array( 'group' => 'uid_section_abplatform', 'key' => 'foot_text', 'default' => 'هر لایه روی لایه زیرین خود کار می‌کند. به همین دلیل یک قرارداد و یک یکپارچه‌سازی، به تمام لایه‌ها دسترسی می‌دهد.' ) );

	/* ---------------- کارنامه عملیاتی ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abrecord', array( 'sanitize_callback' => 'uid_sanitize_section_abrecord', 'default' => array() ) );
	add_settings_section( 'uid_section_abrecord_main', '', '__return_false', 'uid_section_abrecord' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abrecord', 'uid_section_abrecord_main', array( 'group' => 'uid_section_abrecord', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abrecord', 'uid_section_abrecord_main', array( 'group' => 'uid_section_abrecord', 'key' => 'eyebrow', 'default' => 'کارنامه عملیاتی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abrecord', 'uid_section_abrecord_main', array( 'group' => 'uid_section_abrecord', 'key' => 'heading', 'default' => 'زیرساختی که سال‌هاست زیر بار واقعی کار می‌کند' ) );
	add_settings_field( 'items', __( 'کارت‌های آمار', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abrecord', 'uid_section_abrecord_main', array(
		'group' => 'uid_section_abrecord', 'key' => 'items', 'default' => uid_default_ab_record(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => $icon_opts_record ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'value_type', 'type' => 'select', 'label' => __( 'نوع مقدار', 'uid-theme' ), 'options' => array( 'number' => __( 'عددی/درصدی', 'uid-theme' ), 'text' => __( 'متنی', 'uid-theme' ) ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'meter', 'type' => 'text', 'label' => __( 'درصد نوار پیشرفت (خالی = بدون نوار؛ فقط عدد، مثلاً 99.5)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'یادداشت زیر آمار (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abrecord', 'uid_section_abrecord_main', array( 'group' => 'uid_section_abrecord', 'key' => 'foot_text', 'default' => 'آپ‌تایم و زمان پاسخ هر سرویس، در قرارداد <span class="lat">SLA</span> فیمابین به‌صورت جداگانه تعیین و تضمین می‌شود.' ) );

	/* ---------------- تعهدها ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_abvows', array( 'sanitize_callback' => 'uid_sanitize_section_abvows', 'default' => array() ) );
	add_settings_section( 'uid_section_abvows_main', '', '__return_false', 'uid_section_abvows' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_abvows', 'uid_section_abvows_main', array( 'group' => 'uid_section_abvows', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_abvows', 'uid_section_abvows_main', array( 'group' => 'uid_section_abvows', 'key' => 'eyebrow', 'default' => 'تعهدها' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_abvows', 'uid_section_abvows_main', array( 'group' => 'uid_section_abvows', 'key' => 'heading', 'default' => 'آنچه تیم ریسک و تیم فنی شما واقعاً تحویل می‌گیرد' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_abvows', 'uid_section_abvows_main', array( 'group' => 'uid_section_abvows', 'key' => 'text', 'default' => 'شما یک ابزار نمی‌خرید؛ یک تیم متخصص را کنار خودتان می‌آورید. آنچه در ادامه می‌آید، تعهد ما به هر سازمانی است که با ما کار می‌کند.' ) );
	add_settings_field( 'items', __( 'کارت‌های تعهد', 'uid-theme' ), 'uid_field_repeater', 'uid_section_abvows', 'uid_section_abvows_main', array(
		'group' => 'uid_section_abvows', 'key' => 'items', 'default' => uid_default_ab_vows(), 'add_label' => __( 'افزودن تعهد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => $icon_opts_vows ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان (تگ span مجاز است)', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح (تگ span مجاز است)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_ab_group', 'uid_section_ablead', array( 'sanitize_callback' => 'uid_sanitize_section_ablead', 'default' => array() ) );
	add_settings_section( 'uid_section_ablead_main', '', '__return_false', 'uid_section_ablead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'eyebrow', 'default' => 'گفت‌وگو با تیم یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'heading', 'default' => 'اگر ارزیابی فنی یا انطباقی در دستور کار شماست، از همین‌جا شروع کنیم' ) );
	add_settings_field( 'text', __( 'توضیح (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'text', 'default' => 'شماره‌تان را بگذارید تا کارشناس یوآیدی تماس بگیرد: معرفی زیرساخت، دسترسی به محیط سندباکس، مستندات فنی و شرایط <span class="lat">SLA</span> — پیش از هر تعهد مالی. یا مستقیم با <span class="mono">02166123290</span> تماس بگیرید.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nدسترسی به سندباکس پیش از قرارداد\nمستندات فنی کامل برای تیم توسعه\nپشتیبانی اختصاصی سازمانی" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'form_title', 'default' => 'درخواست گفت‌وگو' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'form_hint', 'default' => 'سه فیلد — کمتر از ۲۰ ثانیه وقت می‌گیرد.' ) );
	add_settings_field( 'topic_options', __( 'گزینه‌های موضوع درخواست — هر خط یک گزینه؛ آخرین خط به‌صورت خودکار «احراز هویت شخصی» در نظر گرفته می‌شود و پیام هدایت به صفحه ثنا را نشان می‌دهد', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'topic_options', 'default' => "ارزیابی زیرساخت برای بانک یا موسسه مالی\nارزیابی برای صرافی رمزارز یا فین‌تک\nیکپارچه‌سازی وب‌سرویس‌های استعلامی\nبرون‌سپاری کامل احراز هویت (یوآیدی‌پلاس)\nهمکاری، رسانه یا سایر موارد سازمانی\nاحراز هویت شخصی من در سامانه ثنا", 'rows' => 6 ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'note_text', 'default' => 'شماره شما فقط برای تماس کارشناس استفاده می‌شود و در اختیار شخص ثالث قرار نمی‌گیرد.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_ablead', 'uid_section_ablead_main', array( 'group' => 'uid_section_ablead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_ab_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_abhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'lede'      => wp_kses( $input['lede'] ?? '', array( 'b' => array( 'style' => true ), 'span' => array( 'class' => true ) ) ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'hero_tags' => sanitize_textarea_field( $input['hero_tags'] ?? '' ),
		'honey'     => uid_sanitize_repeater_rows( $input['honey'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
		'capsule'   => uid_sanitize_repeater_rows( $input['capsule'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_abrouter( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'biz_tag'     => sanitize_text_field( $input['biz_tag'] ?? '' ),
		'biz_heading' => sanitize_text_field( $input['biz_heading'] ?? '' ),
		'biz_p1'      => sanitize_textarea_field( $input['biz_p1'] ?? '' ),
		'biz_p2'      => sanitize_textarea_field( $input['biz_p2'] ?? '' ),
		'ind_tag'     => sanitize_text_field( $input['ind_tag'] ?? '' ),
		'ind_heading' => sanitize_text_field( $input['ind_heading'] ?? '' ),
		'ind_p1'      => sanitize_textarea_field( $input['ind_p1'] ?? '' ),
		'ind_p2'      => sanitize_textarea_field( $input['ind_p2'] ?? '' ),
		'ind_note'    => wp_kses( $input['ind_note'] ?? '', array( 'a' => array( 'href' => true ) ) ),
		'ceo_tag'     => sanitize_text_field( $input['ceo_tag'] ?? '' ),
		'ceo_heading' => sanitize_text_field( $input['ceo_heading'] ?? '' ),
		'ceo_p1'      => sanitize_textarea_field( $input['ceo_p1'] ?? '' ),
		'ceo_p2'      => sanitize_textarea_field( $input['ceo_p2'] ?? '' ),
		'emp_tag'     => sanitize_text_field( $input['emp_tag'] ?? '' ),
		'emp_heading' => sanitize_text_field( $input['emp_heading'] ?? '' ),
		'emp_p1'      => sanitize_textarea_field( $input['emp_p1'] ?? '' ),
		'emp_p2'      => sanitize_textarea_field( $input['emp_p2'] ?? '' ),
	);
}

function uid_sanitize_section_abmission( $input ) {
	return array(
		'title_tag'  => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'    => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'    => sanitize_text_field( $input['heading'] ?? '' ),
		'text'       => sanitize_textarea_field( $input['text'] ?? '' ),
		'rows'       => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'quote_text' => sanitize_textarea_field( $input['quote_text'] ?? '' ),
		'by_name'    => sanitize_text_field( $input['by_name'] ?? '' ),
		'by_role'    => sanitize_text_field( $input['by_role'] ?? '' ),
	);
}

function uid_sanitize_section_abidentity( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'stmt'      => sanitize_textarea_field( $input['stmt'] ?? '' ),
		'sig'       => sanitize_text_field( $input['sig'] ?? '' ),
	);
}

function uid_sanitize_section_abvalues( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_abplatform( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'foot_text' => sanitize_textarea_field( $input['foot_text'] ?? '' ),
	);
}

function uid_sanitize_section_abrecord( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'value_type', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
			array( 'key' => 'meter', 'type' => 'text' ),
		) ),
		'foot_text' => wp_kses( $input['foot_text'] ?? '', array( 'span' => array( 'class' => true ) ) ),
	);
}

function uid_sanitize_section_abvows( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_ablead( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => wp_kses( $input['text'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'trust'         => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'    => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'     => sanitize_text_field( $input['form_hint'] ?? '' ),
		'topic_options' => sanitize_textarea_field( $input['topic_options'] ?? '' ),
		'note_text'     => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title' => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'  => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}
