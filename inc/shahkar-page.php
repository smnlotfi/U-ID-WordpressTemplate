<?php
/**
 * صفحه اختصاصی «وب‌سرویس شاهکار» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «sk» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_SHAHKAR_TEMPLATE', 'template-shahkar.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_sk_sections_registry() {
	return array(
		'skhero'    => array( 'label' => __( 'هیرو + پیش‌نمایش زنده استعلام', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'skurgency' => array( 'label' => __( 'بند فوریت (هشدار ریسک)', 'uid-theme' ),         'icon' => 'dashicons-warning' ),
		'skmarquee' => array( 'label' => __( 'مشتریان (نوار متحرک)', 'uid-theme' ),           'icon' => 'dashicons-awards' ),
		'skcode'    => array( 'label' => __( 'ورودی/خروجی API (نمونه‌کد)', 'uid-theme' ),      'icon' => 'dashicons-editor-code' ),
		'skwho'     => array( 'label' => __( 'مناسب چه کسب‌وکارهایی', 'uid-theme' ),           'icon' => 'dashicons-groups' ),
		'skwhy'     => array( 'label' => __( 'چرا یوآیدی', 'uid-theme' ),                      'icon' => 'dashicons-star-empty' ),
		'skrisk'    => array( 'label' => __( 'ریسک‌ها و راه‌حل‌ها', 'uid-theme' ),             'icon' => 'dashicons-shield' ),
		'skflow'    => array( 'label' => __( 'مراحل راه‌اندازی (۳ گام)', 'uid-theme' ),        'icon' => 'dashicons-controls-forward' ),
		'sktrust'   => array( 'label' => __( 'باند اعتماد (آمار شرکت)', 'uid-theme' ),         'icon' => 'dashicons-chart-bar' ),
		'skteam'    => array( 'label' => __( 'تیم متخصص + تعهدنامه', 'uid-theme' ),            'icon' => 'dashicons-businessperson' ),
		'sksecurity'=> array( 'label' => __( 'بند امنیت داده‌ها', 'uid-theme' ),               'icon' => 'dashicons-lock' ),
		'skfaq'     => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                   'icon' => 'dashicons-editor-help' ),
		'sksvc'     => array( 'label' => __( 'سرویس‌های تکمیلی (کاشی‌ها)', 'uid-theme' ),      'icon' => 'dashicons-grid-view' ),
		'sklead'    => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),            'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_sk_layout() {
	$registry = uid_sk_sections_registry();
	$saved    = get_option( 'uid_sk_layout', array() );

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

function uid_sanitize_sk_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_sk_sections_registry();
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

function uid_render_sk_sections() {
	foreach ( uid_get_sk_layout() as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_sk_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_sk_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_sk_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_sk_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_sk_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v5M12 16h.01"/></svg>';
}
function uid_sk_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" transform="scale(-1,1) translate(-24,0)"/></svg>';
}
function uid_sk_who_icon_svg( $key ) {
	$icons = array(
		'fintech' => '<path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'crypto'  => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'shop'    => '<path d="M3 10.5L12 3l9 7.5V20a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 20z"/>',
		'sub'     => '<circle cx="9" cy="7" r="4"/><path d="M2 21v-2a4 4 0 014-4h6a4 4 0 014 4v2"/><path d="M17 3.5a4 4 0 010 7"/>',
		'ins'     => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
		'clock'   => '<circle cx="12" cy="12" r="9.5"/><path d="M12 7v5l3.5 2"/>',
	);
	return $icons[ $key ] ?? $icons['fintech'];
}
function uid_sk_why_icon_svg( $key ) {
	$icons = array(
		'bolt'    => '<path d="M13 2L3 14h7l-1 8 11-14h-7z"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'phone'   => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
		'mfa'     => '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
		'flex'    => '<path d="M4 16l4-8 4 4 4-9 4 13"/><path d="M4 20h16"/>',
	);
	return $icons[ $key ] ?? $icons['bolt'];
}
function uid_sk_team_icon_svg( $key ) {
	$icons = array(
		'phone'   => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
		'doc'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>',
		'support' => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
	);
	return $icons[ $key ] ?? $icons['phone'];
}

/* =====================================================================
 * ۱) هیرو + پیش‌نمایش زنده استعلام (شبیه‌سازی نمایشی، داده آن هاردکد است)
 * ===================================================================== */
function uid_render_section_skhero() {
	$tag  = uid_section_tag( 'skhero', 'h1' );
	$tags = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'skhero', 'tags', "پاسخ‌دهی لحظه‌ای، کمتر از ۱ ثانیه\nیکپارچه‌سازی با راهنمایی تیم فنی یوآیدی\nفقط با ۲ پارامتر ورودی، بدون پیچیدگی" ) ) ) );
	?>
	<section class="dark heroA">
	  <div class="sk-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'وب‌سرویس‌ احراز هویت', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'وب‌سرویس شاهکار', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="sk-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'skhero', 'eyebrow', __( 'وب‌سرویس شاهکار · تطبیق شماره موبایل با کد ملی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'skhero', 'heading', __( 'هر ثبت‌نامی که تایید نکنید،<mark>می‌تواند یک هویت جعلی باشد</mark>.', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'skhero', 'text', __( 'وب‌سرویس شاهکار یک راهکار امن و ضروری برای کسب‌وکارهای آنلاین است. این سرویس پس از تطبیق شماره موبایل با کد ملی کاربر، مالکیت سیم‌کارت را در لحظه تأیید می‌کند — اولین و مهم‌ترین لایه برای احراز هویت کاربران و جلوگیری از کلاهبرداری‌های اینترنتی. یوآیدی این سرویس را با تیم فنی متخصص، روی پلتفرم شما راه‌اندازی و یکپارچه می‌کند.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="sk-btn btn-cta" data-open-modal><?php echo uid_sk_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'skhero', 'btn1_text', __( 'درخواست فعال‌سازی شاهکار', 'uid-theme' ) ) ); ?></button>
	          <a class="sk-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sk_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_sk_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- شبیه‌سازی زنده دموی هیرو — تماماً نمایشی، ساختار پاسخ واقعی است -->
	      <div class="engine rv rv-d2" id="demoCard">
	        <div class="engine-hd" style="border:0;margin-block-end:14px;padding-block-end:0">
	          <b><?php esc_html_e( 'پیش‌نمایش زنده api شاهکار', 'uid-theme' ); ?></b><span><?php esc_html_e( 'یک نمونه را همین‌جا امتحان کنید', 'uid-theme' ); ?></span>
	        </div>
	        <div class="demo-tabs">
	          <button class="demo-tab on" type="button" data-demo-tab="try"><?php esc_html_e( 'امتحان کنید', 'uid-theme' ); ?></button>
	          <button class="demo-tab" type="button" data-demo-tab="about"><?php esc_html_e( 'این استعلام چیست؟', 'uid-theme' ); ?></button>
	        </div>
	        <div data-demo-pane="try">
	          <div class="demo-fields">
	            <div class="fld"><input id="d_nid" type="text" inputmode="numeric" maxlength="10" placeholder="<?php esc_attr_e( 'کد ملی، مثلاً 0123456789', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'کد ملی', 'uid-theme' ); ?>"></div>
	            <div class="fld"><input id="d_mob" type="text" inputmode="numeric" maxlength="11" placeholder="<?php esc_attr_e( 'شماره موبایل، مثلاً 09000000000', 'uid-theme' ); ?>" aria-label="<?php esc_attr_e( 'شماره موبایل', 'uid-theme' ); ?>"></div>
	          </div>
	          <button class="sk-btn btn-cta sk-btn-block" type="button" id="d_run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 11-14h-7z"/></svg> <?php esc_html_e( 'بررسی تطبیق', 'uid-theme' ); ?></button>
	          <div class="demo-result">
	            <div class="demo-result-hd"><span><?php esc_html_e( 'خروجی نمونه (isMatched)', 'uid-theme' ); ?></span><span class="demo-badge wait" id="d_badge"><?php esc_html_e( 'در انتظار ورودی', 'uid-theme' ); ?></span></div>
	            <pre class="demo-json" id="d_json">{
  "isMatched": <span class="k">null</span>,
  "responseContext": { "status": { "code": 0, "message": "کد ملی و شماره موبایل را وارد کنید" } }
}</pre>
	          </div>
	          <div class="demo-note"><?php echo uid_sk_shield_icon(); ?>
	            <span><?php esc_html_e( 'این یک شبیه‌سازی نمایشی است. api شاهکار واقعی در محیط عملیاتی شما همین ساختار خروجی را در کمتر از ۱ ثانیه برمی‌گرداند.', 'uid-theme' ); ?></span></div>
	        </div>
	        <div data-demo-pane="about" style="display:none">
	          <p style="font-size:13px;color:#B9C6E0;line-height:1.9">
	            <?php esc_html_e( 'api شاهکار با دریافت شماره موبایل و کد ملی، در لحظه تطابق این دو را بررسی می‌کند. خروجی وب سرویس شاهکار یک پاسخ ساده true یا false است که به شما اطمینان می‌دهد آیا سیم‌کارت متعلق به همان شخص است یا خیر — بدون نیاز به هیچ فرآیند پیچیده‌ی دیگری.', 'uid-theme' ); ?></p>
	          <div class="micro" style="border-block-start:0;padding-block-start:14px;margin-block-start:14px">
	            <p><?php esc_html_e( 'می‌خواهید همین سرویس روی پلتفرم خودتان فعال شود؟', 'uid-theme' ); ?></p>
	            <div class="micro-row">
	              <div class="fld"><input id="q_tel" name="phone" type="tel" inputmode="numeric"
	                   placeholder="۰۹xxxxxxxxx" data-req data-tel aria-label="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>">
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	              <button class="sk-btn btn-cta" type="button" data-submit><?php esc_html_e( 'تماس بگیرید', 'uid-theme' ); ?></button>
	            </div>
	            <div class="form-ok"><?php echo uid_sk_check_icon(); ?><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
	              <span><?php esc_html_e( 'همکار ما به‌زودی تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </div>
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
function uid_render_section_skurgency() {
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="sk-wrap">
	    <div class="callband urgent rv">
	      <div class="ic"><?php echo uid_sk_warn_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'skurgency', 'heading', __( 'بدون شاهکار، ثبت‌نام‌های جعلی از همان قدم اول وارد سیستم شما می‌شوند', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'skurgency', 'text', __( 'یک شماره موبایل بدون تایید مالکیت، دری باز برای هویت جعلی، حساب‌های تقلبی و کلاهبرداری مالی است. تیم متخصص یوآیدی می‌تواند وب‌سرویس شاهکار را همین هفته روی پلتفرم شما فعال کند.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="sk-btn btn-cta sk-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'skurgency', 'btn1_text', __( 'فعال‌سازی سریع', 'uid-theme' ) ) ); ?></button>
	        <a class="sk-btn btn-ghost-d sk-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo esc_html( uid_section_val( 'skurgency', 'btn2_text', __( 'مشاوره رایگان با کارشناس', 'uid-theme' ) ) ); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) مشتریان (نوار متحرک)
 * ===================================================================== */
function uid_default_sk_marquee() {
	return array(
		array( 'letters' => 'دی', 'color' => '#1FB25A', 'name' => 'دیوار', 'type' => 'پلتفرم آگهی آنلاین' ),
		array( 'letters' => 'زر', 'color' => '#F89428', 'name' => 'زرین‌پال', 'type' => 'درگاه پرداخت آنلاین' ),
		array( 'letters' => 'اک', 'color' => '#15397C', 'name' => 'اکسیر', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'آب', 'color' => '#29BCCE', 'name' => 'آبان‌تتر', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'تب', 'color' => '#DE7C13', 'name' => 'تبدیل', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'رز', 'color' => '#1A97A8', 'name' => 'رمزینکس', 'type' => 'صرافی ارز دیجیتال' ),
		array( 'letters' => 'اس', 'color' => '#5A6478', 'name' => 'استقلال', 'type' => 'باشگاه فرهنگی ورزشی' ),
	);
}
function uid_render_section_skmarquee() {
	$items = uid_section_val( 'skmarquee', 'items', uid_default_sk_marquee() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:36px var(--sec)">
	  <div class="sk-wrap">
	    <p class="tiny rv" style="text-align:center;margin-bottom:18px"><?php echo esc_html( uid_section_val( 'skmarquee', 'heading', __( 'مشتریان سرویس شاهکار یوآیدی', 'uid-theme' ) ) ); ?></p>
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
 * ۴) ورودی/خروجی API (متن داینامیک؛ نمونه‌کدها هاردکد)
 * ===================================================================== */
function uid_render_section_skcode() {
	$tag = uid_section_tag( 'skcode', 'h2' );
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="sk-wrap">
	    <div class="codepanel rv">
	      <div>
	        <span class="sk-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'skcode', 'eyebrow', __( 'ورودی و خروجی api شاهکار', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 14px">'; ?><?php echo esc_html( uid_section_val( 'skcode', 'heading', __( 'فقط دو پارامتر؛ یک پاسخ روشن', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede"><?php esc_html_e( 'api شاهکار با دریافت شماره موبایل و کد ملی، در لحظه تطابق این دو را بررسی می‌کند. خروجی وب‌سرویس شاهکار یک پاسخ ساده true یا false است که به شما اطمینان می‌دهد آیا سیم‌کارت متعلق به همان شخص است یا خیر.', 'uid-theme' ); ?></p>
	        <div class="btn-row" style="margin-block-start:22px">
	          <a class="sk-btn btn-navy" href="<?php echo esc_url( uid_section_val( 'skcode', 'btn1_url', '/api-ekyc-docs/' ) ); ?>"><?php echo esc_html( uid_section_val( 'skcode', 'btn1_text', __( 'مستندات فنی کامل', 'uid-theme' ) ) ); ?><?php echo uid_sk_arrow_icon(); ?></a>
	          <button class="sk-btn btn-ghost" type="button" data-open-modal><?php echo esc_html( uid_section_val( 'skcode', 'btn2_text', __( 'درخواست کلید API', 'uid-theme' ) ) ); ?></button>
	        </div>
	      </div>
	      <div class="code-box">
	        <div class="code-tabs">
	          <button class="on" type="button" data-code-tab="req"><?php esc_html_e( 'نمونه درخواست', 'uid-theme' ); ?></button>
	          <button type="button" data-code-tab="res"><?php esc_html_e( 'نمونه پاسخ', 'uid-theme' ); ?></button>
	          <button class="code-copy" type="button" data-copy><?php esc_html_e( 'کپی', 'uid-theme' ); ?></button>
	        </div>
	        <div class="code-body">
<pre class="code-pane on" data-code-pane="req">POST <span class="k">https://json-api.uid.ir/api/inquiry/mobile/owner/v2</span>
Content-Type: application/json;charset=UTF-8

{
  "requestContext": {
    "apiInfo": {
      "businessId": <span class="m">&lt;UID_BUSINESS_ID&gt;</span>,
      "businessToken": <span class="m">&lt;UID_BUSINESS_TOKEN&gt;</span>
    }
  },
  "nationalId": <span class="s">"0123456789"</span>,
  "mobileNumber": <span class="s">"09000000000"</span>
}</pre>
<pre class="code-pane" data-code-pane="res">{
  "isMatched": <span class="m">false</span>,
  "responseContext": {
    "status": {
      "code": 3,
      "message": <span class="s">"کدملی نامعتبر است"</span>,
      "details": []
    },
    "requestId": <span class="s">""</span>,
    "correlationId": <span class="s">""</span>,
    "navigationURI": <span class="s">""</span>,
    "nextStepToken": <span class="s">""</span>,
    "userSessionId": <span class="s">""</span>,
    "custom": {}
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
 * ۵) مناسب چه کسب‌وکارهایی
 * ===================================================================== */
function uid_default_sk_who() {
	return array(
		array( 'icon' => 'fintech', 'title' => 'فین‌تک، پرداخت‌یار و نئوبانک', 'text' => 'برای جلوگیری از پولشویی و کلاهبرداری مالی' ),
		array( 'icon' => 'crypto', 'title' => 'صرافی‌های ارز دیجیتال', 'text' => 'برای احراز هویت کاربران در همان اولین مرحله' ),
		array( 'icon' => 'shop', 'title' => 'فروشگاه‌های اینترنتی', 'text' => 'برای تأیید هویت خریداران و کاهش ریسک تراکنش‌ها' ),
		array( 'icon' => 'sub', 'title' => 'سرویس‌های اشتراکی و پنل کاربری', 'text' => 'جلوگیری از ساخت حساب‌های جعلی با تطابق کدملی و موبایل' ),
		array( 'icon' => 'ins', 'title' => 'بیمه و کارگزاری‌های بورس', 'text' => 'لایه اول احراز هویت پیش از تکمیل قرارداد یا معامله' ),
		array( 'icon' => 'clock', 'title' => 'هر سرویس آنلاین با ثبت‌نام کاربری', 'text' => 'هر پلتفرمی که برای شروع فعالیت، شماره موبایل کاربر را می‌گیرد' ),
	);
}
function uid_render_section_skwho() {
	$tag   = uid_section_tag( 'skwho', 'h2' );
	$items = uid_section_val( 'skwho', 'items', uid_default_sk_who() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'skwho', 'eyebrow', __( 'برای چه کسب‌وکارهایی ضروری است', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'skwho', 'heading', __( 'چه کسب‌وکارهایی به وب‌سرویس شاهکار نیاز دارند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'skwho', 'text', __( 'کلیه کسب‌وکارهای آنلاین و سازمان‌هایی که به احراز هویت دقیق کاربران خود نیاز دارند، از مشتریان اصلی سرویس شاهکار هستند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="adv-grid rv" data-rail="who">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="adv-card"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sk_who_icon_svg( $it['icon'] ?? 'fintech' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b><span><?php echo esc_html( $it['text'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="who"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۶) چرا یوآیدی
 * ===================================================================== */
function uid_default_sk_why() {
	return array(
		array( 'icon' => 'bolt', 'title' => 'استعلام لحظه‌ای', 'text' => 'پاسخ‌دهی آنی و دقیق در کمتر از یک ثانیه' ),
		array( 'icon' => 'shield', 'title' => 'افزایش امنیت', 'text' => 'اولین گام برای جلوگیری از ثبت‌نام‌های جعلی' ),
		array( 'icon' => 'phone', 'title' => 'بررسی مالکیت سیم‌کارت', 'text' => 'تأیید قطعی مالکیت شماره موبایل به صاحب کد ملی' ),
		array( 'icon' => 'mfa', 'title' => 'مناسب احراز هویت چندمرحله‌ای', 'text' => 'اولین گام قابل‌اتکا در فرآیند MFA کاربران' ),
		array( 'icon' => 'flex', 'title' => 'مناسب همه کسب‌وکارها', 'text' => 'برای تمام کسب‌وکارهای آنلاین، با هر اندازه‌ای' ),
	);
}
function uid_render_section_skwhy() {
	$tag   = uid_section_tag( 'skwhy', 'h2' );
	$items = uid_section_val( 'skwhy', 'items', uid_default_sk_why() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'skwhy', 'eyebrow', __( 'چرا سرویس شاهکار یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'skwhy', 'heading', __( 'پنج دلیلی که شاهکار یوآیدی را انتخاب اول کسب‌وکارها می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="adv-grid rv" data-rail="why">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="adv-card"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sk_why_icon_svg( $it['icon'] ?? 'bolt' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b><span><?php echo esc_html( $it['text'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="why"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۷) ریسک‌ها و راه‌حل‌ها
 * ===================================================================== */
function uid_default_sk_risks() {
	return array(
		array( 'title' => 'ثبت‌نام با هویت جعلی', 'bad_text' => 'بدون تایید مالکیت سیم‌کارت، هر کسی می‌تواند با شماره موبایل و کد ملی دیگران حساب کاربری بسازد.', 'fix_text' => 'تأیید قطعی مالکیت شماره موبایل به صاحب کد ملی، در همان لحظه ثبت‌نام.' ),
		array( 'title' => 'کلاهبرداری مالی و پولشویی', 'bad_text' => 'در فین‌تک‌ها و صرافی‌های ارز دیجیتال، عدم تطبیق هویت مسیر باز برای تراکنش‌های مشکوک است.', 'fix_text' => 'بررسی تطابق کد ملی و موبایل، پیش از تکمیل تراکنش یا ثبت‌نام کاربر.' ),
		array( 'title' => 'حساب‌های کاربری جعلی', 'bad_text' => 'در سرویس‌های اشتراکی و پنل‌های کاربری، ساخت حساب با هویت غیرواقعی رایج و پرهزینه است.', 'fix_text' => 'جلوگیری از ایجاد حساب جعلی با تطابق کد ملی و شماره موبایل در همان مرحله ثبت‌نام.' ),
		array( 'title' => 'پیچیدگی احراز هویت چندمرحله‌ای', 'bad_text' => 'طراحی یک فرآیند MFA قابل‌اتکا از صفر، وقت‌گیر است و نیازمند زیرساخت جداگانه.', 'fix_text' => 'شاهکار به‌عنوان زیرساخت آماده و قابل‌اعتماد، گام اول MFA شما را ساده می‌کند.' ),
	);
}
function uid_render_section_skrisk() {
	$tag   = uid_section_tag( 'skrisk', 'h2' );
	$items = uid_section_val( 'skrisk', 'items', uid_default_sk_risks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'skrisk', 'eyebrow', __( 'مشکلی که شاهکار حل می‌کند', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'skrisk', 'heading', __( 'بدون تطبیق شماره موبایل، این ریسک‌ها همیشه باز می‌مانند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'skrisk', 'text', __( 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه شاهکار با آن روبه‌رو می‌شوند — و اینکه وب‌سرویس شاهکار یوآیدی چطور همان لحظه جلویشان را می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="leak-grid rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="leak">
	        <div class="leak-bad"><span class="tag"><?php echo uid_sk_warn_icon(); ?><?php esc_html_e( 'ریسک رایج', 'uid-theme' ); ?></span>
	          <h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
	          <p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p></div>
	        <div class="leak-fix"><span class="tag"><?php echo uid_sk_check_icon(); ?><?php esc_html_e( 'راه‌حل شاهکار', 'uid-theme' ); ?></span>
	          <p><?php echo esc_html( $it['fix_text'] ?? '' ); ?></p></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۸) مراحل راه‌اندازی (۳ گام)
 * ===================================================================== */
function uid_default_sk_flow() {
	return array(
		array( 'title' => 'انتخاب پلن و دریافت کلید API', 'text' => 'پس از انتخاب پلن مورد نظر، مستندات کامل فنی به‌همراه کلید دسترسی (API Key) اختصاصی در اختیار تیم فنی شما قرار می‌گیرد.' ),
		array( 'title' => 'یکپارچه‌سازی روی پلتفرم شما', 'text' => 'توسعه‌دهندگان شما با مطالعه مستندات، این وب‌سرویس تطبیق شماره موبایل با کد ملی را در هر زبان برنامه‌نویسی، به‌راحتی پیاده‌سازی می‌کنند.' ),
		array( 'title' => 'استعلام لحظه‌ای در محیط عملیاتی', 'text' => 'از همان روز، هر ثبت‌نام روی پلتفرم شما در کمتر از ۱ ثانیه با تطبیق کد ملی و موبایل، تایید می‌شود.' ),
	);
}
function uid_render_section_skflow() {
	$tag   = uid_section_tag( 'skflow', 'h2' );
	$steps = uid_section_val( 'skflow', 'steps', uid_default_sk_flow() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'skflow', 'eyebrow', __( 'راه‌اندازی با تیم متخصص یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'skflow', 'heading', __( 'یکپارچه‌سازی api شاهکار، در سه گام ساده', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'skflow', 'text', __( 'یکپارچه‌سازی api شاهکار بسیار ساده و سریع است — و تیم فنی یوآیدی در تمام مراحل کنار توسعه‌دهندگان شماست.', 'uid-theme' ) ) ); ?></p>
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
 * ۹) باند اعتماد
 * ===================================================================== */
function uid_default_sk_trust() {
	return array(
		array( 'type' => 'text', 'value' => '< ۱ ثانیه', 'label' => 'زمان پاسخ‌دهی استعلام شاهکار' ),
		array( 'type' => 'text', 'value' => '۲ پارامتر', 'label' => 'تنها ورودی لازم: کد ملی و موبایل' ),
		array( 'type' => 'text', 'value' => 'HTTPS', 'label' => 'رمزنگاری کامل تمام ارتباطات' ),
		array( 'type' => 'count', 'value' => '5000000', 'label' => 'احراز هویت موفق یوآیدی تاکنون' ),
		array( 'type' => 'text', 'value' => '۹۹٫۵٪', 'label' => 'تعهد پایداری سرویس (SLA)' ),
	);
}
function uid_render_section_sktrust() {
	$tag   = uid_section_tag( 'sktrust', 'h2' );
	$cells = uid_section_val( 'sktrust', 'cells', uid_default_sk_trust() );
	if ( ! is_array( $cells ) || empty( $cells ) ) return;
	?>
	<section class="dark sec">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'sktrust', 'eyebrow', __( 'اعتماد کسب‌وکارها به یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec" style="color:#fff">'; ?><?php echo esc_html( uid_section_val( 'sktrust', 'heading', __( 'یوآیدی، زیرساخت احراز هویت مورد اعتماد کسب‌وکارهای ایرانی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
 * ۱۰) تیم متخصص + تعهدنامه
 * ===================================================================== */
function uid_default_sk_team_rows() {
	return array(
		array( 'icon' => 'phone', 'title' => 'مشاوره انتخاب پلن', 'text' => 'کارشناس یوآیدی بر اساس حجم واقعی درخواست‌های شما، مقرون‌به‌صرفه‌ترین پلن را پیشنهاد می‌دهد' ),
		array( 'icon' => 'doc', 'title' => 'همراهی در یکپارچه‌سازی', 'text' => 'تیم فنی یوآیدی مستندات و کلید API را می‌دهد و در پیاده‌سازی کنار توسعه‌دهندگان شماست' ),
		array( 'icon' => 'support', 'title' => 'پشتیبانی فنی مستمر', 'text' => 'پس از راه‌اندازی هم برای رفع سوال یا افزایش پلن، مستقیم با کارشناس در ارتباط هستید' ),
	);
}
function uid_default_sk_pledge_items() {
	return array(
		'هزینه پلن‌ها از قبل شفاف است؛ بدون هزینه پنهان.',
		'اطلاعات ارسالی (کد ملی و موبایل) ذخیره نمی‌شود.',
		'از انتخاب پلن تا یکپارچه‌سازی، کنار تیم فنی شما می‌مانیم.',
	);
}
function uid_render_section_skteam() {
	$rows = uid_section_val( 'skteam', 'rows', uid_default_sk_team_rows() );
	$pledge_raw = uid_section_val( 'skteam', 'pledge_items', implode( "\n", uid_default_sk_pledge_items() ) );
	$pledge = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec">
	  <div class="sk-wrap">
	    <div class="team rv">
	      <div>
	      <div class="team-list" data-rail="team">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sk_team_icon_svg( $r['icon'] ?? 'phone' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b><p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div></div>
	        <?php endforeach; ?>
	      </div>
	      <div class="dots" data-dots="team"></div>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.6 5.6 6.1.9-4.4 4.3 1 6.1-5.3-2.8-5.3 2.8 1-6.1L3.3 8.5l6.1-.9z"/></svg></span><b><?php echo esc_html( uid_section_val( 'skteam', 'pledge_heading', __( 'تعهد یوآیدی به کسب‌وکار شما', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_sk_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۱) بند امنیت داده‌ها
 * ===================================================================== */
function uid_render_section_sksecurity() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="sk-wrap">
	    <div class="secbox rv">
	      <div class="ic"><?php echo uid_sk_shield_icon(); ?></div>
	      <div><b><?php echo esc_html( uid_section_val( 'sksecurity', 'heading', __( 'امنیت اطلاعات، اولویت اول یوآیدی', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'sksecurity', 'text', __( 'تمام ارتباطات با وب‌سرویس شاهکار از طریق پروتکل‌های رمزنگاری‌شده و امن (HTTPS) انجام می‌شود. اطلاعات ارسالی شما (کد ملی و شماره موبایل) به هیچ‌وجه ذخیره نمی‌شود و صرفاً برای انجام همان استعلام لحظه‌ای و بازگرداندن نتیجه به کار می‌روند.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۲) سوالات متداول
 * ===================================================================== */
function uid_default_sk_faq() {
	return array(
		array( 'question' => 'وب‌سرویس شاهکار چیست و دقیقاً چه کاری انجام می‌دهد؟', 'answer' => 'سرویس شاهکار یک سامانه تطبیق شماره موبایل با کد ملی است که از طریق API در اختیار کسب‌وکارها قرار می‌گیرد. وظیفه اصلی این وب‌سرویس، بررسی و تأیید این موضوع است که آیا شماره موبایل وارد شده توسط کاربر، واقعاً به نام فردی با همان کد ملی ثبت شده است یا خیر. این فرآیند، اولین و مهم‌ترین گام در احراز هویت سرویس شاهکار محسوب می‌شود.' ),
		array( 'question' => 'مهم‌ترین مزایای استفاده از وب‌سرویس شاهکار چیست؟', 'answer' => 'استفاده از api سامانه شاهکار مزایای کلیدی زیر را برای کسب‌وکار شما به همراه دارد: افزایش چشمگیر امنیت با جلوگیری از ثبت‌نام با هویت‌های جعلی، سرقت هویت و کلاهبرداری‌های مالی؛ جلب اعتماد کاربران که با مشاهده این لایه امنیتی، با اطمینان بیشتری ثبت‌نام و فعالیت می‌کنند؛ و بهینه‌سازی فرآیند احراز هویت به‌عنوان یک زیرساخت قابل‌اعتماد که می‌تواند گام اول احراز هویت چندمرحله‌ای (MFA) باشد.' ),
		array( 'question' => 'چه کسب‌وکارهایی به سرویس تطبیق شماره موبایل با کد ملی نیاز دارند؟', 'answer' => 'کلیه کسب‌وکارهای آنلاین و سازمان‌هایی که به احراز هویت دقیق کاربران خود نیاز دارند، از مشتریان اصلی سرویس شاهکار هستند. این سرویس به‌ویژه برای فین‌تک، پرداخت‌یارها و نئوبانک‌ها؛ صرافی‌های ارز دیجیتال؛ فروشگاه‌های اینترنتی و پلتفرم‌های تجارت الکترونیک؛ شرکت‌های بیمه و کارگزاری‌های بورس؛ و ارائه‌دهندگان خدمات آنلاین که نیاز به ثبت‌نام کاربری دارند، ضروری است.' ),
		array( 'question' => 'سرعت پاسخ‌دهی وب‌سرویس شاهکار چقدر است؟', 'answer' => 'وب سرویس شاهکار به‌صورت لحظه‌ای (Real-time) عمل می‌کند. پس از ارسال درخواست به سرور، نتیجه استعلام مبنی بر صحیح بودن یا نبودن تطابق کد ملی و شماره موبایل در کمتر از یک ثانیه به شما بازگردانده می‌شود.' ),
		array( 'question' => 'پارامترهای ورودی برای استعلام از api شاهکار چیست؟', 'answer' => 'برای استفاده از api شاهکار، تنها به دو پارامتر ورودی نیاز دارید: کد ملی کاربر و شماره موبایل کاربر. با ارسال این دو مقدار، سرویس نتیجه را در قالب یک پاسخ ساده (معمولاً true یا false) برمی‌گرداند.' ),
		array( 'question' => 'امنیت اطلاعات کاربران در سرویس شاهکار چگونه تأمین می‌شود؟', 'answer' => 'امنیت اطلاعات اولویت اصلی یوآیدی است. تمام ارتباطات با وب سرویس شاهکار از طریق پروتکل‌های رمزنگاری‌شده و امن (HTTPS) انجام می‌شود. اطلاعات ارسالی شما (کد ملی و شماره موبایل) به هیچ وجه ذخیره نمی‌شود و صرفاً برای انجام همان استعلام لحظه‌ای و بازگرداندن نتیجه به کار می‌روند.' ),
		array( 'question' => 'آیا محدودیتی در تعداد استعلام از سامانه شاهکار وجود دارد؟', 'answer' => 'ما پلن‌های متنوعی را برای کسب‌وکارهای مختلف، از استارتاپ‌های کوچک تا سازمان‌های بزرگ، طراحی کرده‌ایم. شما می‌توانید با توجه به نیاز و حجم درخواست‌های خود، پلن مناسب با تعداد استعلام محدود یا نامحدود را انتخاب کنید.' ),
		array( 'question' => 'فرآیند پیاده‌سازی و یکپارچه‌سازی api شاهکار چگونه است؟', 'answer' => 'یکپارچه‌سازی api شاهکار بسیار ساده و سریع است. پس از انتخاب پلن مورد نظر، مستندات کامل فنی به‌همراه کلید دسترسی (API Key) اختصاصی در اختیار تیم فنی شما قرار می‌گیرد. توسعه‌دهندگان شما می‌توانند با مطالعه مستندات، این وب‌سرویس تطبیق شماره موبایل با کد ملی را به‌راحتی و در کوتاه‌ترین زمان، در هر زبان برنامه‌نویسی روی پلتفرم شما پیاده‌سازی کنند.' ),
		array( 'question' => 'خروجی api شاهکار دقیقاً چه چیزی نشان می‌دهد؟', 'answer' => 'خروجی وب‌سرویس شاهکار یک پاسخ ساده true یا false در فیلد isMatched است که به شما اطمینان می‌دهد آیا سیم‌کارت متعلق به همان صاحب کد ملی است یا خیر؛ در کنار آن، وضعیت و پیام دقیق درخواست هم در قالب کد وضعیت بازگردانده می‌شود.' ),
	);
}
function uid_render_section_skfaq() {
	$tag   = uid_section_tag( 'skfaq', 'h2' );
	$items = uid_section_val( 'skfaq', 'items', uid_default_sk_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'skfaq', 'eyebrow', __( 'پرسش‌های پرتکرار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'skfaq', 'heading', __( 'سوالات متداول درباره وب‌سرویس شاهکار', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۳) سرویس‌های تکمیلی
 * ===================================================================== */
function uid_default_sk_svc() {
	return array(
		'وب‌سرویس تطبیق شماره کارت با کد ملی', 'وب‌سرویس تطبیق شماره شبا با کد ملی', 'وب‌سرویس تبدیل شماره کارت به شبا',
		'وب‌سرویس استعلام شماره کارت', 'وب‌سرویس استعلام شبا', 'سرویس استعلام آدرس و کد پستی',
		'وب‌سرویس ثبت احوال', 'وب‌سرویس احراز هویت تصویری', 'سرویس اعتبارسنجی معاملاتی',
		'سرویس استعلام سند ملکی', 'سرویس استعلام جواز کسب', 'سرویس استعلام اطلاعات ثبتی شرکت',
	);
}
function uid_render_section_sksvc() {
	$tag = uid_section_tag( 'sksvc', 'h2' );
	$items_raw = uid_section_val( 'sksvc', 'items', implode( "\n", uid_default_sk_svc() ) );
	$items = array_values( array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="sk-wrap">
	    <div class="sec-head mid rv">
	      <span class="sk-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'sksvc', 'eyebrow', __( 'یک قدم جلوتر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'sksvc', 'heading', __( 'سرویس‌های دیگری که کسب‌وکارها معمولاً کنار شاهکار نیاز دارند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
 * ۱۴) بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_sklead() {
	$tag = uid_section_tag( 'sklead', 'h2' );
	$trust_raw = uid_section_val( 'sklead', 'trust', "پاسخ‌دهی زیر ۱ ثانیه\nپلن متناسب با حجم کسب‌وکار شما\nهمراهی تیم فنی تا پایان یکپارچه‌سازی" );
	$trust = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'sklead', 'biztype_options', "فین‌تک / پرداخت‌یار\nصرافی ارز دیجیتال\nفروشگاه اینترنتی\nسرویس اشتراکی / پنل کاربری\nبیمه یا کارگزاری بورس\nسایر" );
	$biztypes = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="sk-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="sk-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'sklead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'sklead', 'heading', __( 'وب‌سرویس شاهکار را همین هفته روی پلتفرم خود فعال کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'sklead', 'text', __( 'فرم را پر کنید؛ کارشناس فنی یوآیدی با شما تماس می‌گیرد، پلن مناسب حجم درخواست‌هایتان را پیشنهاد می‌دهد و کلید API را در اختیار تیم فنی شما قرار می‌دهد.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_sk_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'sklead', 'form_title', __( 'درخواست فعال‌سازی شاهکار', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'sklead', 'form_hint', __( 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <div class="frow">
	              <div class="fld"><input name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	                <span class="err"><?php esc_html_e( 'نام را وارد کنید.', 'uid-theme' ); ?></span></div>
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric" placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	            </div>
	            <div class="fld"><input name="business" type="text" placeholder="<?php esc_attr_e( 'نام کسب‌وکار', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'نام کسب‌وکار را وارد کنید.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><select name="biztype" data-req>
	                <option value=""><?php esc_html_e( 'نوع کسب‌وکار…', 'uid-theme' ); ?></option>
	                <?php foreach ( $biztypes as $b ) : ?>
	                <option><?php echo esc_html( $b ); ?></option>
	                <?php endforeach; ?>
	              </select></div>
	            <button class="sk-btn btn-cta sk-btn-block" type="button" data-submit><?php echo uid_sk_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'sklead', 'submit_text', __( 'درخواست فعال‌سازی شاهکار', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_sk_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'sklead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_sk_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'sklead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'sklead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_sk_page_id() {
	$page_id = (int) get_option( 'uid_sk_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_SHAHKAR_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_sk_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_sk_page() {
	if ( uid_get_sk_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'وب‌سرویس شاهکار', 'uid-theme' ),
		'post_name'   => 'shahkar',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_SHAHKAR_TEMPLATE );
	update_option( 'uid_sk_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_sk_page_once' );

function uid_ensure_sk_page_once() {
	if ( get_option( 'uid_sk_page_bootstrapped' ) ) return;
	uid_ensure_sk_page();
	update_option( 'uid_sk_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_sk_page_once' );

function uid_register_sk_slug_setting() {
	register_setting( 'uid_sk_group', 'uid_sk_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_sk_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_sk_page_slug_section', '', '__return_false', 'uid_sk_layout' );
	add_settings_field( 'uid_sk_page_slug', __( 'آدرس (اسلاگ) صفحه شاهکار', 'uid-theme' ), 'uid_field_sk_page_slug', 'uid_sk_layout', 'uid_sk_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_sk_slug_setting' );

function uid_field_sk_page_slug( $args ) {
	$page_id = uid_get_sk_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_sk_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_sk_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه شاهکار هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_sk_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_sk_page_id();

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
function uid_register_sk_settings() {
	register_setting( 'uid_sk_group', 'uid_sk_layout', array(
		'sanitize_callback' => 'uid_sanitize_sk_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_sk_layout_main', '', '__return_false', 'uid_sk_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_sk_layout', 'uid_sk_layout_main', array(
		'option_name' => 'uid_sk_layout', 'registry_fn' => 'uid_sk_sections_registry', 'layout_fn' => 'uid_get_sk_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skhero', array( 'sanitize_callback' => 'uid_sanitize_section_skhero', 'default' => array() ) );
	add_settings_section( 'uid_section_skhero_main', '', '__return_false', 'uid_section_skhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'eyebrow', 'default' => 'وب‌سرویس شاهکار · تطبیق شماره موبایل با کد ملی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'heading', 'default' => 'هر ثبت‌نامی که تایید نکنید،<mark>می‌تواند یک هویت جعلی باشد</mark>.' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'text', 'default' => 'وب‌سرویس شاهکار یک راهکار امن و ضروری برای کسب‌وکارهای آنلاین است. این سرویس پس از تطبیق شماره موبایل با کد ملی کاربر، مالکیت سیم‌کارت را در لحظه تأیید می‌کند — اولین و مهم‌ترین لایه برای احراز هویت کاربران و جلوگیری از کلاهبرداری‌های اینترنتی. یوآیدی این سرویس را با تیم فنی متخصص، روی پلتفرم شما راه‌اندازی و یکپارچه می‌کند.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'btn1_text', 'default' => 'درخواست فعال‌سازی شاهکار' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skhero', 'uid_section_skhero_main', array( 'group' => 'uid_section_skhero', 'key' => 'tags', 'default' => "پاسخ‌دهی لحظه‌ای، کمتر از ۱ ثانیه\nیکپارچه‌سازی با راهنمایی تیم فنی یوآیدی\nفقط با ۲ پارامتر ورودی، بدون پیچیدگی" ) );
	add_settings_field( 'demo_notice', '', 'uid_field_notice', 'uid_section_skhero', 'uid_section_skhero_main', array( 'text' => __( 'پیش‌نمایش زنده استعلام (سمت راست هیرو) یک شبیه‌سازی نمایشی با داده‌های ساختگی و کاملاً تعاملی است؛ محتوای آن از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/shahkar-page.php و assets/js/shahkar-page.js مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- بند فوریت ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skurgency', array( 'sanitize_callback' => 'uid_sanitize_section_skurgency', 'default' => array() ) );
	add_settings_section( 'uid_section_skurgency_main', '', '__return_false', 'uid_section_skurgency' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skurgency', 'uid_section_skurgency_main', array( 'group' => 'uid_section_skurgency', 'key' => 'heading', 'default' => 'بدون شاهکار، ثبت‌نام‌های جعلی از همان قدم اول وارد سیستم شما می‌شوند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skurgency', 'uid_section_skurgency_main', array( 'group' => 'uid_section_skurgency', 'key' => 'text', 'default' => 'یک شماره موبایل بدون تایید مالکیت، دری باز برای هویت جعلی، حساب‌های تقلبی و کلاهبرداری مالی است. تیم متخصص یوآیدی می‌تواند وب‌سرویس شاهکار را همین هفته روی پلتفرم شما فعال کند.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_skurgency', 'uid_section_skurgency_main', array( 'group' => 'uid_section_skurgency', 'key' => 'btn1_text', 'default' => 'فعال‌سازی سریع' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (تماس تلفنی)', 'uid-theme' ), 'uid_field_text', 'uid_section_skurgency', 'uid_section_skurgency_main', array( 'group' => 'uid_section_skurgency', 'key' => 'btn2_text', 'default' => 'مشاوره رایگان با کارشناس' ) );

	/* ---------------- مشتریان ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skmarquee', array( 'sanitize_callback' => 'uid_sanitize_section_skmarquee', 'default' => array() ) );
	add_settings_section( 'uid_section_skmarquee_main', '', '__return_false', 'uid_section_skmarquee' );
	add_settings_field( 'heading', __( 'عنوان بالای نوار', 'uid-theme' ), 'uid_field_text', 'uid_section_skmarquee', 'uid_section_skmarquee_main', array( 'group' => 'uid_section_skmarquee', 'key' => 'heading', 'default' => 'مشتریان سرویس شاهکار یوآیدی' ) );
	add_settings_field( 'items', __( 'فهرست مشتریان', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skmarquee', 'uid_section_skmarquee_main', array(
		'group' => 'uid_section_skmarquee', 'key' => 'items', 'default' => uid_default_sk_marquee(), 'add_label' => __( 'افزودن مشتری', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'letters', 'type' => 'text', 'label' => __( 'حرف/حروف آواتار', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'color', 'type' => 'text', 'label' => __( 'رنگ آواتار (کد Hex)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'type', 'type' => 'text', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ورودی/خروجی API ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skcode', array( 'sanitize_callback' => 'uid_sanitize_section_skcode', 'default' => array() ) );
	add_settings_section( 'uid_section_skcode_main', '', '__return_false', 'uid_section_skcode' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'eyebrow', 'default' => 'ورودی و خروجی api شاهکار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'heading', 'default' => 'فقط دو پارامتر؛ یک پاسخ روشن' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول', 'uid-theme' ), 'uid_field_text', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'btn1_text', 'default' => 'مستندات فنی کامل' ) );
	add_settings_field( 'btn1_url', __( 'لینک دکمه اول', 'uid-theme' ), 'uid_field_text', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'btn1_url', 'default' => '/api-ekyc-docs/' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_skcode', 'uid_section_skcode_main', array( 'group' => 'uid_section_skcode', 'key' => 'btn2_text', 'default' => 'درخواست کلید API' ) );
	add_settings_field( 'code_notice', '', 'uid_field_notice', 'uid_section_skcode', 'uid_section_skcode_main', array( 'text' => __( 'توضیح و نمونه‌کدهای درخواست/پاسخ، مستندات فنی دقیق‌اند و بخش نمونه‌کد از این صفحه قابل‌ویرایش نیست (برای تغییر، به inc/shahkar-page.php مراجعه کنید).', 'uid-theme' ) ) );

	/* ---------------- مناسب چه کسب‌وکارهایی ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skwho', array( 'sanitize_callback' => 'uid_sanitize_section_skwho', 'default' => array() ) );
	add_settings_section( 'uid_section_skwho_main', '', '__return_false', 'uid_section_skwho' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skwho', 'uid_section_skwho_main', array( 'group' => 'uid_section_skwho', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skwho', 'uid_section_skwho_main', array( 'group' => 'uid_section_skwho', 'key' => 'eyebrow', 'default' => 'برای چه کسب‌وکارهایی ضروری است' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skwho', 'uid_section_skwho_main', array( 'group' => 'uid_section_skwho', 'key' => 'heading', 'default' => 'چه کسب‌وکارهایی به وب‌سرویس شاهکار نیاز دارند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skwho', 'uid_section_skwho_main', array( 'group' => 'uid_section_skwho', 'key' => 'text', 'default' => 'کلیه کسب‌وکارهای آنلاین و سازمان‌هایی که به احراز هویت دقیق کاربران خود نیاز دارند، از مشتریان اصلی سرویس شاهکار هستند.' ) );
	add_settings_field( 'items', __( 'کارت‌های کسب‌وکار', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skwho', 'uid_section_skwho_main', array(
		'group' => 'uid_section_skwho', 'key' => 'items', 'default' => uid_default_sk_who(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'fintech' => __( 'فین‌تک', 'uid-theme' ), 'crypto' => __( 'رمزارز', 'uid-theme' ), 'shop' => __( 'فروشگاه', 'uid-theme' ), 'sub' => __( 'اشتراکی', 'uid-theme' ), 'ins' => __( 'بیمه', 'uid-theme' ), 'clock' => __( 'عمومی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- چرا یوآیدی ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skwhy', array( 'sanitize_callback' => 'uid_sanitize_section_skwhy', 'default' => array() ) );
	add_settings_section( 'uid_section_skwhy_main', '', '__return_false', 'uid_section_skwhy' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skwhy', 'uid_section_skwhy_main', array( 'group' => 'uid_section_skwhy', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skwhy', 'uid_section_skwhy_main', array( 'group' => 'uid_section_skwhy', 'key' => 'eyebrow', 'default' => 'چرا سرویس شاهکار یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skwhy', 'uid_section_skwhy_main', array( 'group' => 'uid_section_skwhy', 'key' => 'heading', 'default' => 'پنج دلیلی که شاهکار یوآیدی را انتخاب اول کسب‌وکارها می‌کند' ) );
	add_settings_field( 'items', __( 'کارت‌های دلیل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skwhy', 'uid_section_skwhy_main', array(
		'group' => 'uid_section_skwhy', 'key' => 'items', 'default' => uid_default_sk_why(), 'add_label' => __( 'افزودن دلیل', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'bolt' => __( 'سرعت', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ), 'phone' => __( 'موبایل', 'uid-theme' ), 'mfa' => __( 'MFA', 'uid-theme' ), 'flex' => __( 'انعطاف', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ریسک‌ها ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skrisk', array( 'sanitize_callback' => 'uid_sanitize_section_skrisk', 'default' => array() ) );
	add_settings_section( 'uid_section_skrisk_main', '', '__return_false', 'uid_section_skrisk' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skrisk', 'uid_section_skrisk_main', array( 'group' => 'uid_section_skrisk', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skrisk', 'uid_section_skrisk_main', array( 'group' => 'uid_section_skrisk', 'key' => 'eyebrow', 'default' => 'مشکلی که شاهکار حل می‌کند' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skrisk', 'uid_section_skrisk_main', array( 'group' => 'uid_section_skrisk', 'key' => 'heading', 'default' => 'بدون تطبیق شماره موبایل، این ریسک‌ها همیشه باز می‌مانند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skrisk', 'uid_section_skrisk_main', array( 'group' => 'uid_section_skrisk', 'key' => 'text', 'default' => 'این‌ها رایج‌ترین آسیب‌هایی هستند که کسب‌وکارهای بدون لایه شاهکار با آن روبه‌رو می‌شوند — و اینکه وب‌سرویس شاهکار یوآیدی چطور همان لحظه جلویشان را می‌گیرد.' ) );
	add_settings_field( 'items', __( 'موارد ریسک', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skrisk', 'uid_section_skrisk_main', array(
		'group' => 'uid_section_skrisk', 'key' => 'items', 'default' => uid_default_sk_risks(), 'add_label' => __( 'افزودن ریسک', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان ریسک', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح ریسک', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'راه‌حل شاهکار', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مراحل راه‌اندازی ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skflow', array( 'sanitize_callback' => 'uid_sanitize_section_skflow', 'default' => array() ) );
	add_settings_section( 'uid_section_skflow_main', '', '__return_false', 'uid_section_skflow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skflow', 'uid_section_skflow_main', array( 'group' => 'uid_section_skflow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skflow', 'uid_section_skflow_main', array( 'group' => 'uid_section_skflow', 'key' => 'eyebrow', 'default' => 'راه‌اندازی با تیم متخصص یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skflow', 'uid_section_skflow_main', array( 'group' => 'uid_section_skflow', 'key' => 'heading', 'default' => 'یکپارچه‌سازی api شاهکار، در سه گام ساده' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skflow', 'uid_section_skflow_main', array( 'group' => 'uid_section_skflow', 'key' => 'text', 'default' => 'یکپارچه‌سازی api شاهکار بسیار ساده و سریع است — و تیم فنی یوآیدی در تمام مراحل کنار توسعه‌دهندگان شماست.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skflow', 'uid_section_skflow_main', array(
		'group' => 'uid_section_skflow', 'key' => 'steps', 'default' => uid_default_sk_flow(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );

	/* ---------------- باند اعتماد ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_sktrust', array( 'sanitize_callback' => 'uid_sanitize_section_sktrust', 'default' => array() ) );
	add_settings_section( 'uid_section_sktrust_main', '', '__return_false', 'uid_section_sktrust' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sktrust', 'uid_section_sktrust_main', array( 'group' => 'uid_section_sktrust', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sktrust', 'uid_section_sktrust_main', array( 'group' => 'uid_section_sktrust', 'key' => 'eyebrow', 'default' => 'اعتماد کسب‌وکارها به یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sktrust', 'uid_section_sktrust_main', array( 'group' => 'uid_section_sktrust', 'key' => 'heading', 'default' => 'یوآیدی، زیرساخت احراز هویت مورد اعتماد کسب‌وکارهای ایرانی' ) );
	add_settings_field( 'cells', __( 'آمار (خانه‌های باند اعتماد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sktrust', 'uid_section_sktrust_main', array(
		'group' => 'uid_section_sktrust', 'key' => 'cells', 'default' => uid_default_sk_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'type', 'type' => 'select', 'label' => __( 'نوع', 'uid-theme' ), 'options' => array( 'text' => __( 'متن ثابت', 'uid-theme' ), 'count' => __( 'عدد شمارشی', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- تیم متخصص + تعهدنامه ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skteam', array( 'sanitize_callback' => 'uid_sanitize_section_skteam', 'default' => array() ) );
	add_settings_section( 'uid_section_skteam_main', '', '__return_false', 'uid_section_skteam' );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skteam', 'uid_section_skteam_main', array(
		'group' => 'uid_section_skteam', 'key' => 'rows', 'default' => uid_default_sk_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'phone' => __( 'تماس', 'uid-theme' ), 'doc' => __( 'مستندات', 'uid-theme' ), 'support' => __( 'پشتیبانی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_skteam', 'uid_section_skteam_main', array( 'group' => 'uid_section_skteam', 'key' => 'pledge_heading', 'default' => 'تعهد یوآیدی به کسب‌وکار شما' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_skteam', 'uid_section_skteam_main', array( 'group' => 'uid_section_skteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_sk_pledge_items() ), 'rows' => 4 ) );

	/* ---------------- بند امنیت ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_sksecurity', array( 'sanitize_callback' => 'uid_sanitize_section_sksecurity', 'default' => array() ) );
	add_settings_section( 'uid_section_sksecurity_main', '', '__return_false', 'uid_section_sksecurity' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sksecurity', 'uid_section_sksecurity_main', array( 'group' => 'uid_section_sksecurity', 'key' => 'heading', 'default' => 'امنیت اطلاعات، اولویت اول یوآیدی' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sksecurity', 'uid_section_sksecurity_main', array( 'group' => 'uid_section_sksecurity', 'key' => 'text', 'default' => 'تمام ارتباطات با وب‌سرویس شاهکار از طریق پروتکل‌های رمزنگاری‌شده و امن (HTTPS) انجام می‌شود. اطلاعات ارسالی شما (کد ملی و شماره موبایل) به هیچ‌وجه ذخیره نمی‌شود و صرفاً برای انجام همان استعلام لحظه‌ای و بازگرداندن نتیجه به کار می‌روند.' ) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_skfaq', array( 'sanitize_callback' => 'uid_sanitize_section_skfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_skfaq_main', '', '__return_false', 'uid_section_skfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_skfaq', 'uid_section_skfaq_main', array( 'group' => 'uid_section_skfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_skfaq', 'uid_section_skfaq_main', array( 'group' => 'uid_section_skfaq', 'key' => 'eyebrow', 'default' => 'پرسش‌های پرتکرار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_skfaq', 'uid_section_skfaq_main', array( 'group' => 'uid_section_skfaq', 'key' => 'heading', 'default' => 'سوالات متداول درباره وب‌سرویس شاهکار' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_skfaq', 'uid_section_skfaq_main', array(
		'group' => 'uid_section_skfaq', 'key' => 'items', 'default' => uid_default_sk_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );

	/* ---------------- سرویس‌های تکمیلی ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_sksvc', array( 'sanitize_callback' => 'uid_sanitize_section_sksvc', 'default' => array() ) );
	add_settings_section( 'uid_section_sksvc_main', '', '__return_false', 'uid_section_sksvc' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sksvc', 'uid_section_sksvc_main', array( 'group' => 'uid_section_sksvc', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sksvc', 'uid_section_sksvc_main', array( 'group' => 'uid_section_sksvc', 'key' => 'eyebrow', 'default' => 'یک قدم جلوتر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sksvc', 'uid_section_sksvc_main', array( 'group' => 'uid_section_sksvc', 'key' => 'heading', 'default' => 'سرویس‌های دیگری که کسب‌وکارها معمولاً کنار شاهکار نیاز دارند' ) );
	add_settings_field( 'items', __( 'فهرست سرویس‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sksvc', 'uid_section_sksvc_main', array( 'group' => 'uid_section_sksvc', 'key' => 'items', 'default' => implode( "\n", uid_default_sk_svc() ), 'rows' => 8 ) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_sk_group', 'uid_section_sklead', array( 'sanitize_callback' => 'uid_sanitize_section_sklead', 'default' => array() ) );
	add_settings_section( 'uid_section_sklead_main', '', '__return_false', 'uid_section_sklead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'heading', 'default' => 'وب‌سرویس شاهکار را همین هفته روی پلتفرم خود فعال کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'text', 'default' => 'فرم را پر کنید؛ کارشناس فنی یوآیدی با شما تماس می‌گیرد، پلن مناسب حجم درخواست‌هایتان را پیشنهاد می‌دهد و کلید API را در اختیار تیم فنی شما قرار می‌دهد.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'trust', 'default' => "پاسخ‌دهی زیر ۱ ثانیه\nپلن متناسب با حجم کسب‌وکار شما\nهمراهی تیم فنی تا پایان یکپارچه‌سازی" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'form_title', 'default' => 'درخواست فعال‌سازی شاهکار' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'form_hint', 'default' => 'فرم را پر کنید؛ کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'biztype_options', 'default' => "فین‌تک / پرداخت‌یار\nصرافی ارز دیجیتال\nفروشگاه اینترنتی\nسرویس اشتراکی / پنل کاربری\nبیمه یا کارگزاری بورس\nسایر" ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'submit_text', 'default' => 'درخواست فعال‌سازی شاهکار' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_sklead', 'uid_section_sklead_main', array( 'group' => 'uid_section_sklead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_sk_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_skhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
	);
}

function uid_sanitize_section_skurgency( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_skmarquee( $input ) {
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

function uid_sanitize_section_skcode( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn1_url'  => sanitize_text_field( $input['btn1_url'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_skwho( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_skwhy( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_skrisk( $input ) {
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

function uid_sanitize_section_skflow( $input ) {
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

function uid_sanitize_section_sktrust( $input ) {
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

function uid_sanitize_section_skteam( $input ) {
	return array(
		'rows'           => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'pledge_heading' => sanitize_text_field( $input['pledge_heading'] ?? '' ),
		'pledge_items'   => sanitize_textarea_field( $input['pledge_items'] ?? '' ),
	);
}

function uid_sanitize_section_sksecurity( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'text'    => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_skfaq( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_sksvc( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
	);
}

function uid_sanitize_section_sklead( $input ) {
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
function uid_render_sk_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست فعال‌سازی وب‌سرویس شاهکار', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اطلاعات کسب‌وکارتان را بگذارید؛ کارشناس فنی یوآیدی همین امروز تماس می‌گیرد، پلن مناسب را پیشنهاد می‌دهد و کلید API را برایتان فعال می‌کند.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'فین‌تک / پرداخت‌یار', 'uid-theme' ); ?></option><option><?php esc_html_e( 'صرافی ارز دیجیتال', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'فروشگاه اینترنتی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سرویس اشتراکی / پنل کاربری', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'بیمه یا کارگزاری بورس', 'uid-theme' ); ?></option><option><?php esc_html_e( 'سایر', 'uid-theme' ); ?></option>
	    </select></div>
	  <button class="btn btn-cta btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'درخواست فعال‌سازی شاهکار', 'uid-theme' ); ?></button>
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
