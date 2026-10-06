<?php
/**
 * صفحه اختصاصی «تماس با ما» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «cu» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options با
 * سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * این صفحه فصل تاخوردنی/نوار پرش سریع ندارد (کوتاه‌تر از صفحات دیگر است)؛ چهار
 * سکشن واقعی دارد: هیرو + کنسول پاسخگویی زنده، مسیر تماس (بخش‌ها + فرم سه‌فیلدی)،
 * آدرس/تماس/ساعات کاری، و سوالات متداول.
 *
 * تلفن و تلگرام سراسری سایت (inc/helpers.php: uid_phone_raw/uid_phone_display/
 * uid_telegram_url) همان‌جا استفاده می‌شوند؛ ایمیل پشتیبانی و آدرس دفتر مختص این
 * صفحه‌اند و آپشن مستقل خودشان را دارند (چون در گروه سراسری uid_contact_options
 * وجود ندارند).
 *
 * ساعت زنده تهران و منطق باز/بسته‌بودن دفتر (assets/js/contact-us-page.js) با
 * ثابت‌های OPEN_H/CLOSE_H/OPEN_DAYS در همان فایل جاوااسکریپت مشخص می‌شوند؛ متن‌های
 * نمایشی مربوط به ساعات کاری در این فایل مستقل ویرایش می‌شوند و باید با آن ثابت‌ها
 * هماهنگ نگه داشته شوند (با uid_field_notice مستند شده است).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_CU_TEMPLATE', 'template-contact-us.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_cu_sections_registry() {
	return array(
		'curhero'  => array( 'label' => __( 'هیرو + کنسول پاسخگویی زنده', 'uid-theme' ), 'icon' => 'dashicons-phone' ),
		'curouter' => array( 'label' => __( 'با کدام بخش کار دارید؟ (مسیر + فرم)', 'uid-theme' ), 'icon' => 'dashicons-randomize' ),
		'curloc'   => array( 'label' => __( 'آدرس، تماس و ساعات کاری', 'uid-theme' ),        'icon' => 'dashicons-location-alt' ),
		'curfaq'   => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                  'icon' => 'dashicons-editor-help' ),
	);
}

function uid_get_cu_layout() {
	$registry = uid_cu_sections_registry();
	$saved    = get_option( 'uid_cu_layout', array() );

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

function uid_sanitize_cu_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_cu_sections_registry();
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

function uid_render_cu_sections() {
	$layout = uid_get_cu_layout();
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
	uid_render_cu_toast();
}

/* =====================================================================
 * فیلدهای مستقل این صفحه (آدرس/ایمیل دفتر) — تلفن و تلگرام از تنظیمات سراسری
 * (uid_contact_options) می‌آیند و دوباره‌سازی نمی‌شوند.
 * ===================================================================== */
function uid_cu_email() {
	return uid_section_val( 'curloc', 'email', 'info@uid.ir' );
}
function uid_cu_address() {
	return uid_section_val( 'curloc', 'address', __( 'فرصت شیرازی', 'uid-theme' ) );
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه — فقط مسیر داخلی svg (بدون تگ wrapper)؛ همه‌ی
 * این آیکون‌ها کاملاً ثابت‌اند (نه از پیشخوان)، پس نیازی به wp_kses ندارند.
 * ===================================================================== */
function uid_cu_ic( $key ) {
	switch ( $key ) {
		case 'phone':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
		case 'telegram':
			return '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3L18.6 20c-.25 1.1-.9 1.37-1.83.85l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.46c.4-.36-.09-.56-.63-.2L5.79 12.47.83 10.92c-1.08-.34-1.1-1.08.23-1.6L20.5 2.73c.9-.33 1.69.2 1.4 1.57z"/></svg>';
		case 'mail':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3 7l9 6 9-6"/></svg>';
		case 'formdoc':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>';
		case 'pin':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/></svg>';
		case 'clock':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/></svg>';
		case 'chevron':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
		case 'copy':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>';
		case 'lock':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>';
		case 'send':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
		case 'alert':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 7.5v5M12 16v.1"/></svg>';
		case 'check-circle':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M8 12.5l2.6 2.6L16 9.5"/></svg>';
		default:
			return '';
	}
}

/* =====================================================================
 * ۱) هیرو + کنسول پاسخگویی زنده
 * ===================================================================== */
function uid_render_section_curhero() {
	$tag = uid_section_tag( 'curhero', 'h1' );
	?>
	<section class="dark chero" id="top">
	  <div class="cu-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php esc_html_e( 'تماس با ما', 'uid-theme' ); ?></b></div>
	    </div>
	    <div class="chero-grid">
	      <div class="rv">
	        <span class="cu-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'curhero', 'eyebrow', __( 'تماس با ما', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'curhero', 'heading', __( 'راه‌های تماس با یوآیدی<br><mark>سریع‌ترین راه، یک تماس تلفنی است</mark>', 'uid-theme' ) ), array( 'br' => array(), 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'curhero', 'lede', __( 'تلفن، تلگرام، ایمیل یا فرم — هر کدام که برای شما راحت‌تر است. پیش از اینکه چیزی بنویسید، ببینید همین حالا چه کسی پاسخ می‌دهد.', 'uid-theme' ) ) ); ?></p>

	        <a class="telcard" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" data-track="call_click" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: phone number */ __( 'تماس با شماره %s', 'uid-theme' ), uid_phone_display() ) ); ?>">
	          <span class="tic"><?php echo uid_cu_ic( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <span class="ttx">
	            <span class="tlb"><?php echo esc_html( uid_section_val( 'curhero', 'telcard_label', __( 'تماس مستقیم با دفتر یوآیدی', 'uid-theme' ) ) ); ?></span>
	            <span class="tnum"><?php echo esc_html( uid_phone_display() ); ?></span>
	          </span>
	          <span class="tgo"><?php echo uid_cu_ic( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	        </a>

	        <p class="chero-sub"><?php echo uid_cu_ic( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	          <?php echo esc_html( uid_section_val( 'curhero', 'hours_text', __( 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوی شما هستیم', 'uid-theme' ) ) ); ?></p>

	        <div class="btn-row" style="margin-block-start:22px">
	          <a class="cu-btn cu-btn-ghost-d" href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener"><?php echo uid_cu_ic( 'telegram' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?> <?php echo esc_html( uid_section_val( 'curhero', 'telegram_btn_text', __( 'پشتیبانی تلگرام', 'uid-theme' ) ) ); ?></a>
	          <a class="cu-btn cu-btn-ghost-d" href="#router"><?php echo uid_cu_ic( 'formdoc' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?> <?php echo esc_html( uid_section_val( 'curhero', 'msg_btn_text', __( 'ارسال پیام', 'uid-theme' ) ) ); ?></a>
	        </div>
	      </div>

	      <!-- کنسول پاسخگویی زنده: ساعت و وضعیت باز/بسته توسط جاوااسکریپت این صفحه محاسبه می‌شود -->
	      <div class="rv rv-d2">
	        <div class="avail" id="avail">
	          <div class="avail-hd">
	            <span class="dot" aria-hidden="true"></span>
	            <b><?php echo esc_html( uid_section_val( 'curhero', 'avail_title', __( 'وضعیت پاسخگویی', 'uid-theme' ) ) ); ?></b>
	            <span class="avail-clock" id="clock" aria-label="<?php esc_attr_e( 'ساعت تهران', 'uid-theme' ); ?>">--:--:--</span>
	          </div>

	          <div class="avail-state">
	            <span class="sic" id="stateIcon"><?php echo uid_cu_ic( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	            <span class="stx">
	              <b id="stateTitle"><?php esc_html_e( 'در حال بررسی ساعت تهران…', 'uid-theme' ); ?></b>
	              <span id="stateNote"><?php esc_html_e( 'ساعت دفتر بر اساس وقت تهران نمایش داده می‌شود.', 'uid-theme' ); ?></span>
	            </span>
	          </div>

	          <p class="avail-hours"><?php echo uid_cu_ic( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	            <?php echo esc_html( uid_section_val( 'curhero', 'avail_hours_line', __( 'ساعات کاری: شنبه تا چهارشنبه، ۹:۰۰ تا ۱۸:۰۰ به وقت تهران', 'uid-theme' ) ) ); ?></p>

	          <div class="chans">
	            <a class="chan" id="chanPhone" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" data-track="call_click">
	              <span class="ctop">
	                <span class="cic"><?php echo uid_cu_ic( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	                <b><?php echo esc_html( uid_section_val( 'curhero', 'phone_label', __( 'تلفن', 'uid-theme' ) ) ); ?></b>
	                <span class="cbadge" id="phoneBadge">—</span>
	              </span>
	              <span class="cval"><?php echo esc_html( uid_phone_raw() ); ?></span>
	              <span class="cnote" id="phoneNote"><?php echo esc_html( uid_section_val( 'curhero', 'phone_note', __( 'سریع‌ترین راه، در ساعات کاری', 'uid-theme' ) ) ); ?></span>
	            </a>

	            <a class="chan" href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener">
	              <span class="ctop">
	                <span class="cic"><?php echo uid_cu_ic( 'telegram' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	                <b><?php echo esc_html( uid_section_val( 'curhero', 'telegram_label', __( 'تلگرام', 'uid-theme' ) ) ); ?></b>
	                <span class="cbadge"><?php esc_html_e( 'هر ساعت', 'uid-theme' ); ?></span>
	              </span>
	              <span class="cval"><?php echo esc_html( uid_section_val( 'curhero', 'telegram_handle', 'Uid_poshtiban' ) ); ?></span>
	              <span class="cnote"><?php echo esc_html( uid_section_val( 'curhero', 'telegram_note', __( 'پشتیبان آنلاین یوآیدی', 'uid-theme' ) ) ); ?></span>
	            </a>

	            <a class="chan" href="<?php echo esc_url( 'mailto:' . uid_cu_email() ); ?>">
	              <span class="ctop">
	                <span class="cic"><?php echo uid_cu_ic( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	                <b><?php echo esc_html( uid_section_val( 'curhero', 'email_label', __( 'ایمیل', 'uid-theme' ) ) ); ?></b>
	                <span class="cbadge"><?php esc_html_e( 'هر ساعت', 'uid-theme' ); ?></span>
	              </span>
	              <span class="cval"><?php echo esc_html( uid_cu_email() ); ?></span>
	              <span class="cnote"><?php echo esc_html( uid_section_val( 'curhero', 'email_note', __( 'مکاتبات رسمی و ارسال مدارک', 'uid-theme' ) ) ); ?></span>
	            </a>

	            <a class="chan" href="#router">
	              <span class="ctop">
	                <span class="cic"><?php echo uid_cu_ic( 'formdoc' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	                <b><?php echo esc_html( uid_section_val( 'curhero', 'form_chan_label', __( 'فرم تماس', 'uid-theme' ) ) ); ?></b>
	                <span class="cbadge"><?php esc_html_e( 'هر ساعت', 'uid-theme' ); ?></span>
	              </span>
	              <span class="cval fa"><?php echo esc_html( uid_section_val( 'curhero', 'form_chan_value', __( 'پایین همین صفحه', 'uid-theme' ) ) ); ?></span>
	              <span class="cnote"><?php echo esc_html( uid_section_val( 'curhero', 'form_chan_note', __( 'پیام کتبی، با پاسخ به موبایل یا ایمیل شما', 'uid-theme' ) ) ); ?></span>
	            </a>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۲) با کدام بخش کار دارید؟ — مسیریابی + فرم سه‌فیلدی
 * ===================================================================== */

/**
 * ترتیب ثابت تب‌ها (کلید => آیکون کارت‌های مسیر). محتوای متنی هر تب از پیشخوان
 * می‌آید؛ خودِ کارت‌های مسیر (آیکون/عنوان/توضیح/لینک) و پیکربندی رفتاری جاوااسکریپت
 * (عنوان/راهنما/پیام موفقیت فرم به ازای هر تب، حالت فیلد «نام کسب‌وکار») ثابت‌اند —
 * دقیقاً مطابق طرح اصلی — چون مستقیماً با assets/js/contact-us-page.js هماهنگ‌اند.
 */
function uid_cu_router_panes() {
	return array( 'sana', 'biz', 'dev', 'support', 'press' );
}

/**
 * کارت‌های مسیر ثابت هر تب: [icon, title, text, href, teal?]
 */
function uid_cu_router_routes() {
	$tel_href = uid_phone_href( uid_phone_raw() );
	$tel_num  = uid_phone_display();
	$tg_href  = uid_telegram_url();
	$mail     = uid_cu_email();
	return array(
		'sana' => array(
			array( 'icon' => 'telegram', 'title' => __( 'پشتیبان آنلاین یوآیدی', 'uid-theme' ), 'text' => __( 'Uid_poshtiban — پیام در تلگرام', 'uid-theme' ), 'href' => $tg_href, 'teal' => true ),
			array( 'icon' => 'phone', 'title' => $tel_num, 'text' => __( 'شنبه تا چهارشنبه، ۹ تا ۱۸', 'uid-theme' ), 'href' => $tel_href ),
		),
		'biz' => array(
			array( 'icon' => 'phone', 'title' => $tel_num, 'text' => __( 'گفت‌وگوی مستقیم با کارشناس', 'uid-theme' ), 'href' => $tel_href, 'teal' => true ),
			array( 'icon' => 'mail', 'title' => $mail, 'text' => __( 'برای مکاتبات رسمی و ارسال مدارک', 'uid-theme' ), 'href' => 'mailto:' . $mail ),
		),
		'dev' => array(
			array( 'icon' => 'mail', 'title' => $mail, 'text' => __( 'برای ارسال فایل و تصویر خطا', 'uid-theme' ), 'href' => 'mailto:' . $mail, 'teal' => true ),
			array( 'icon' => 'phone', 'title' => $tel_num, 'text' => __( 'برای موارد فوری، در ساعات کاری', 'uid-theme' ), 'href' => $tel_href ),
		),
		'support' => array(
			array( 'icon' => 'phone', 'title' => $tel_num, 'text' => __( 'در ساعات کاری', 'uid-theme' ), 'href' => $tel_href, 'teal' => true ),
			array( 'icon' => 'telegram', 'title' => __( 'پشتیبان آنلاین یوآیدی', 'uid-theme' ), 'text' => __( 'Uid_poshtiban — ارسال تصویر و پیگیری', 'uid-theme' ), 'href' => $tg_href ),
		),
		'press' => array(
			array( 'icon' => 'mail', 'title' => $mail, 'text' => __( 'مکاتبات رسمی و ارسال پیوست', 'uid-theme' ), 'href' => 'mailto:' . $mail, 'teal' => true ),
		),
		'none' => array(
			array( 'icon' => 'phone', 'title' => $tel_num, 'text' => __( 'شنبه تا چهارشنبه، ۹ تا ۱۸', 'uid-theme' ), 'href' => $tel_href ),
		),
	);
}

function uid_cu_router_svg_kses() {
	return array(
		'path'     => array( 'd' => true ),
		'rect'     => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
		'circle'   => array( 'cx' => true, 'cy' => true, 'r' => true ),
	);
}

function uid_render_cu_router_pane( $key, $is_default = false ) {
	$routes = uid_cu_router_routes();
	$tag_default = array(
		'sana'    => __( 'کاربر شخصی', 'uid-theme' ),
		'biz'     => __( 'کسب‌وکارها', 'uid-theme' ),
		'dev'     => __( 'پشتیبانی فنی', 'uid-theme' ),
		'support' => __( 'مشتریان فعلی', 'uid-theme' ),
		'press'   => __( 'رسانه و همکاری', 'uid-theme' ),
	);
	$heading_default = array(
		'sana'    => __( 'پرسش‌های ثنا و سجام را پشتیبانی پاسخ می‌دهد', 'uid-theme' ),
		'biz'     => __( 'گفت‌وگو درباره همکاری و قرارداد', 'uid-theme' ),
		'dev'     => __( 'مشکل فنی یا پرسش درباره اتصال', 'uid-theme' ),
		'support' => __( 'پیگیری سرویس فعال', 'uid-theme' ),
		'press'   => __( 'رسانه، همکاری و پیشنهاد کاری', 'uid-theme' ),
	);
	$text_default = array(
		'sana'    => __( 'اگر برای خودتان ثنا یا سجام را دنبال می‌کنید، پاسخ از تیم پشتیبانی می‌آید و نه از تیم فروش. سریع‌ترین راه، پیام در تلگرام پشتیبانی است؛ در ساعات کاری می‌توانید تلفنی هم تماس بگیرید.', 'uid-theme' ),
		'biz'     => __( 'برای صحبت درباره شرایط همکاری و قرارداد، تلفن سریع‌ترین راه است. اگر ترجیح می‌دهید کتبی بنویسید، فرم را پر کنید تا کارشناس در ساعات کاری با شما تماس بگیرد.', 'uid-theme' ),
		'dev'     => __( 'متن دقیق خطا و زمان رخ دادن آن را در فرم بنویسید یا برای ما ایمیل کنید تا سریع‌تر بررسی شود. برای موارد فوری، تماس تلفنی کوتاه‌ترین راه است.', 'uid-theme' ),
		'support' => __( 'برای پیگیری یک مورد باز، تلفن و تلگرام سریع‌ترین راه‌ها هستند. اگر می‌خواهید درخواست‌تان کتبی ثبت شود، از فرم استفاده کنید.', 'uid-theme' ),
		'press'   => __( 'درخواست مصاحبه، پوشش خبری، همکاری یا ارسال رزومه را برای ما ایمیل کنید. برای پیام کوتاه‌تر، فرم هم در دسترس است.', 'uid-theme' ),
	);

	if ( $is_default ) {
		$pane_key = 'none';
		$tag      = __( 'هنوز انتخابی نکرده‌اید', 'uid-theme' );
		$heading  = __( 'یکی از بخش‌های بالا را انتخاب کنید', 'uid-theme' );
		$text     = __( 'راه تماس همان بخش اینجا نمایش داده می‌شود. اگر مطمئن نیستید به کدام بخش کار دارید، تماس تلفنی یا فرم پایین همیشه به ما می‌رسد.', 'uid-theme' );
	} else {
		$pane_key = $key;
		$tag      = uid_section_val( 'curouter', $key . '_tag', $tag_default[ $key ] );
		$heading  = uid_section_val( 'curouter', $key . '_heading', $heading_default[ $key ] );
		$text     = uid_section_val( 'curouter', $key . '_text', $text_default[ $key ] );
	}
	?>
	<div class="rpanel<?php echo $is_default ? ' on' : ''; ?>" id="p-<?php echo esc_attr( $pane_key ); ?>" role="tabpanel" data-p="<?php echo esc_attr( $pane_key ); ?>">
	  <div class="rp-copy">
	    <span class="tag"><?php echo esc_html( $tag ); ?></span>
	    <h3><?php echo esc_html( $heading ); ?></h3>
	    <p><?php echo esc_html( $text ); ?></p>
	    <div class="rp-routes">
	      <?php foreach ( $routes[ $pane_key ] as $i => $r ) : ?>
	      <a class="rp-route<?php echo ! empty( $r['teal'] ) ? ' teal' : ''; ?>" href="<?php echo esc_url( $r['href'] ); ?>"<?php echo ( 'telegram' === $r['icon'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
	        <span class="hx"><?php echo wp_kses( uid_cu_ic( $r['icon'] ), uid_cu_router_svg_kses() ); ?></span>
	        <span class="tx"><b><?php echo esc_html( $r['title'] ); ?></b><span><?php echo esc_html( $r['text'] ); ?></span></span>
	        <span class="ch"><?php echo uid_cu_ic( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	      </a>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</div>
	<?php
}

function uid_render_section_curouter() {
	$tag   = uid_section_tag( 'curouter', 'h2' );
	$panes = uid_cu_router_panes();
	$chip_default = array(
		'sana'    => __( 'کاربر شخصی — ثنا و سجام', 'uid-theme' ),
		'biz'     => __( 'کسب‌وکارها', 'uid-theme' ),
		'dev'     => __( 'پشتیبانی فنی', 'uid-theme' ),
		'support' => __( 'مشتریان فعلی', 'uid-theme' ),
		'press'   => __( 'رسانه و همکاری', 'uid-theme' ),
	);
	?>
	<section class="sec" id="router" style="padding-block:clamp(48px,6vw,88px)">
	  <div class="cu-wrap">
	    <div class="sec-head rv">
	      <span class="cu-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'curouter', 'eyebrow', __( 'بخش‌ها', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'curouter', 'heading', __( 'با کدام بخش کار دارید؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'curouter', 'lede', __( 'انتخاب کنید تا راه تماس همان بخشی که پاسخ می‌دهد را ببینید.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="router rv" id="routerBox">
	      <div class="router-hd">
	        <p class="q"><?php echo esc_html( uid_section_val( 'curouter', 'q_text', __( 'کدام گزینه به شما نزدیک‌تر است؟', 'uid-theme' ) ) ); ?></p>
	        <p class="qs"><?php echo esc_html( uid_section_val( 'curouter', 'qs_text', __( 'اگر مطمئن نیستید، تماس تلفنی یا فرم پایین همیشه به ما می‌رسد.', 'uid-theme' ) ) ); ?></p>
	      </div>

	      <div class="rchips" id="rchips" role="tablist" aria-label="<?php esc_attr_e( 'بخش مورد نظر', 'uid-theme' ); ?>">
	        <?php foreach ( $panes as $key ) : ?>
	        <button class="rchip" type="button" role="tab" aria-selected="false" aria-controls="p-<?php echo esc_attr( $key ); ?>" data-r="<?php echo esc_attr( $key ); ?>"><span class="rdot"></span><span><?php echo esc_html( uid_section_val( 'curouter', $key . '_chip', $chip_default[ $key ] ) ); ?></span></button>
	        <?php endforeach; ?>
	      </div>

	      <div class="rbody">
	        <div class="rcopy-slot">
	          <?php foreach ( $panes as $key ) : uid_render_cu_router_pane( $key, false ); endforeach; ?>
	          <?php uid_render_cu_router_pane( 'none', true ); ?>
	        </div>

	        <div class="rform-slot">
	          <form class="cform" id="cform" novalidate>
	            <div class="cform-body">
	              <h4 id="formTitle"><?php echo esc_html( uid_section_val( 'curouter', 'form_title_default', __( 'ارسال پیام', 'uid-theme' ) ) ); ?></h4>
	              <p class="hint" id="formHint"><?php echo esc_html( uid_section_val( 'curouter', 'form_hint_default', __( 'نام، یک راه ارتباطی و توضیح کوتاه — همین برای تماس گرفتن با شما کافی است.', 'uid-theme' ) ) ); ?></p>

	              <div class="fld" id="f-name">
	                <label for="i-name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	                <input id="i-name" name="name" type="text" autocomplete="name" placeholder="<?php esc_attr_e( 'مثلاً مریم رضایی', 'uid-theme' ); ?>" required>
	                <span class="err"><?php echo uid_cu_ic( 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'نام خود را وارد کنید.', 'uid-theme' ); ?></span></span>
	              </div>

	              <div class="fld" id="f-contact">
	                <label for="i-contact"><?php esc_html_e( 'شماره موبایل یا ایمیل', 'uid-theme' ); ?></label>
	                <input id="i-contact" name="contact" type="text" inputmode="email" autocomplete="tel" placeholder="<?php echo esc_attr( uid_fa_digits( '۰۹۱۲۳۴۵۶۷۸۹' ) . ' یا name@company.com' ); ?>" required>
	                <span class="tipline"><?php esc_html_e( 'پاسخ را از همین راه برای شما می‌فرستیم.', 'uid-theme' ); ?></span>
	                <span class="err"><?php echo uid_cu_ic( 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span class="errtx"><?php esc_html_e( 'یک شماره موبایل یا ایمیل معتبر وارد کنید.', 'uid-theme' ); ?></span></span>
	              </div>

	              <div class="fld fld-extra" id="f-org">
	                <label for="i-org"><?php esc_html_e( 'نام کسب‌وکار', 'uid-theme' ); ?> <span class="opt" id="orgOpt">(<?php esc_html_e( 'اختیاری', 'uid-theme' ); ?>)</span></label>
	                <input id="i-org" name="org" type="text" autocomplete="organization" placeholder="<?php esc_attr_e( 'نام شرکت یا پلتفرم', 'uid-theme' ); ?>">
	                <span class="err"><?php echo uid_cu_ic( 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'نام کسب‌وکار را وارد کنید.', 'uid-theme' ); ?></span></span>
	              </div>

	              <div class="fld" id="f-msg">
	                <label for="i-msg"><?php esc_html_e( 'پیغام', 'uid-theme' ); ?></label>
	                <textarea id="i-msg" name="message" placeholder="<?php esc_attr_e( 'در یک یا دو جمله بنویسید به چه چیزی نیاز دارید.', 'uid-theme' ); ?>" required></textarea>
	                <span class="err"><?php echo uid_cu_ic( 'alert' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'پیغام خود را بنویسید.', 'uid-theme' ); ?></span></span>
	              </div>

	              <input type="hidden" name="intent" id="i-intent" value="">
	              <input type="hidden" name="source" value="contact-us">
	              <!-- honeypot: ربات این فیلد را پر می‌کند، انسان اصلاً آن را نمی‌بیند -->
	              <div class="sr" aria-hidden="true"><label for="i-hp"><?php esc_html_e( 'این فیلد را خالی بگذارید', 'uid-theme' ); ?></label><input id="i-hp" name="company_url" type="text" tabindex="-1" autocomplete="off"></div>

	              <div class="cform-foot">
	                <button class="cu-btn cu-btn-cta cu-btn-block" type="submit" data-track="form_submit">
	                  <?php echo uid_cu_ic( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	                  <?php echo esc_html( uid_section_val( 'curouter', 'submit_text', __( 'ارسال پیام', 'uid-theme' ) ) ); ?>
	                </button>
	                <p class="privacy"><?php echo uid_cu_ic( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	                  <span><?php echo esc_html( uid_section_val( 'curouter', 'privacy_text', __( 'اطلاعات شما فقط برای پاسخ به همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></p>
	              </div>
	            </div>

	            <div class="form-ok" role="status">
	              <?php echo uid_cu_ic( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	              <b><?php echo esc_html( uid_section_val( 'curouter', 'success_title', __( 'پیام شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span id="okNote"><?php echo esc_html( uid_section_val( 'curouter', 'success_text', __( 'در ساعات کاری با شما تماس می‌گیریم. اگر عجله دارید، ۰۲۱۶۶۱۲۳۲۹۰ سریع‌تر است.', 'uid-theme' ) ) ); ?></span>
	            </div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) آدرس، تماس و ساعات کاری
 * ===================================================================== */
function uid_render_section_curloc() {
	$address = uid_cu_address();
	$email   = uid_cu_email();
	?>
	<section class="sec" id="loc" style="padding-block:0 clamp(48px,6vw,88px)">
	  <div class="cu-wrap">
	    <div class="loc rv">
	      <div class="loc-card">
	        <span class="cu-eyebrow" style="align-self:flex-start;margin-block-end:16px"><i></i><?php echo esc_html( uid_section_val( 'curloc', 'eyebrow', __( 'دفتر یوآیدی', 'uid-theme' ) ) ); ?></span>

	        <div class="loc-row">
	          <span class="hx"><?php echo uid_cu_ic( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <span class="tx"><span class="k"><?php esc_html_e( 'آدرس', 'uid-theme' ); ?></span><span class="v" id="addrText"><?php echo esc_html( $address ); ?></span></span>
	          <button class="copybtn" type="button" data-copy="<?php echo esc_attr( $address ); ?>" data-label="<?php esc_attr_e( 'آدرس', 'uid-theme' ); ?>">
	            <?php echo uid_cu_ic( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'کپی', 'uid-theme' ); ?></span>
	          </button>
	        </div>

	        <div class="loc-row">
	          <span class="hx"><?php echo uid_cu_ic( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <span class="tx"><span class="k"><?php esc_html_e( 'تلفن', 'uid-theme' ); ?></span><a class="v mono" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" data-track="call_click"><?php echo esc_html( uid_phone_raw() ); ?></a></span>
	          <button class="copybtn" type="button" data-copy="<?php echo esc_attr( uid_phone_raw() ); ?>" data-label="<?php esc_attr_e( 'شماره تلفن', 'uid-theme' ); ?>">
	            <?php echo uid_cu_ic( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'کپی', 'uid-theme' ); ?></span>
	          </button>
	        </div>

	        <div class="loc-row">
	          <span class="hx"><?php echo uid_cu_ic( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <span class="tx"><span class="k"><?php esc_html_e( 'ایمیل', 'uid-theme' ); ?></span><a class="v mono" href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></span>
	          <button class="copybtn" type="button" data-copy="<?php echo esc_attr( $email ); ?>" data-label="<?php esc_attr_e( 'ایمیل', 'uid-theme' ); ?>">
	            <?php echo uid_cu_ic( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php esc_html_e( 'کپی', 'uid-theme' ); ?></span>
	          </button>
	        </div>

	        <div class="loc-row">
	          <span class="hx"><?php echo uid_cu_ic( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <span class="tx"><span class="k"><?php esc_html_e( 'ساعات کاری', 'uid-theme' ); ?></span><span class="v"><?php echo esc_html( uid_section_val( 'curloc', 'hours_text', __( 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوی شما هستیم', 'uid-theme' ) ) ); ?></span></span>
	        </div>

	        <!-- توجه: این نشان یک جانگهدار است — باید با انکر و تصویر رسمی eNAMAD صادرشده
	             برای دامنه u-id.net جایگزین شود (نشان به دامنه گره خورده و قابل بازسازی
	             در این‌جا نیست). در uid_register_cu_settings() هم یادداشتی برای این مورد ثبت شده. -->
	        <div class="namad">
	          <div class="namad-seal" role="img" aria-label="<?php esc_attr_e( 'نماد اعتماد الکترونیکی', 'uid-theme' ); ?>">
	            <div><span class="e">e</span><small>eNAMAD.ir</small></div>
	          </div>
	          <div class="namad-tx">
	            <b><?php echo esc_html( uid_section_val( 'curloc', 'namad_title', __( 'نماد اعتماد الکترونیکی', 'uid-theme' ) ) ); ?></b>
	            <p><?php echo esc_html( uid_section_val( 'curloc', 'namad_text', __( 'یوآیدی دارای نماد اعتماد الکترونیکی است. برای دیدن اطلاعات ثبت‌شده نماد، روی آن کلیک کنید.', 'uid-theme' ) ) ); ?></p>
	          </div>
	        </div>
	      </div>

	      <div class="loc-fig" aria-hidden="true">
	        <svg class="rings" viewBox="0 0 400 320" preserveAspectRatio="xMidYMid slice" focusable="false">
	          <defs>
	            <linearGradient id="cuRg" x1="0" y1="0" x2="1" y2="1">
	              <stop offset="0" stop-color="#29BCCE" stop-opacity=".55"/>
	              <stop offset="1" stop-color="#29BCCE" stop-opacity="0"/>
	            </linearGradient>
	          </defs>
	          <g fill="none" stroke="url(#cuRg)" stroke-width="1.2">
	            <path d="M200 56 L262 92 L262 164 L200 200 L138 164 L138 92 Z"/>
	            <path d="M200 20 L293 74 L293 182 L200 236 L107 182 L107 74 Z" stroke-opacity=".7"/>
	            <path d="M200 -16 L324 56 L324 200 L200 272 L76 200 L76 56 Z" stroke-opacity=".45"/>
	            <path d="M200 -52 L355 38 L355 218 L200 308 L45 218 L45 38 Z" stroke-opacity=".25"/>
	          </g>
	        </svg>
	        <div class="pin">
	          <span class="hx"><?php echo uid_cu_ic( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	          <b><?php echo esc_html( $address ); ?></b>
	          <span><?php echo esc_html( uid_section_val( 'curloc', 'pin_sub', __( 'دفتر یوآیدی — شرکت بینش هوشمند نسل پیشرو', 'uid-theme' ) ) ); ?></span>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) سوالات متداول
 * ===================================================================== */
function uid_default_cu_faq() {
	return array(
		array(
			'question' => 'خارج از ساعات کاری چطور با شما تماس بگیرم؟',
			'answer'   => 'تلگرام، ایمیل و فرم در هر ساعتی باز هستند و پیام‌ها در نخستین روز کاری بررسی می‌شوند. کادر «وضعیت پاسخگویی» بالای صفحه، ساعت تهران را نشان می‌دهد و می‌گوید دفتر همین حالا باز است یا نه.',
		),
		array(
			'question' => 'برای ثنا و سجام با کدام بخش تماس بگیرم؟',
			'answer'   => 'با پشتیبانی، نه با فروش. پیام در تلگرام <span class="lat">Uid_poshtiban</span> سریع‌ترین راه است و در ساعات کاری می‌توانید با ۰۲۱۶۶۱۲۳۲۹۰ هم تماس بگیرید.',
		),
		array(
			'question' => 'پیامی که در فرم می‌نویسم به کجا می‌رسد؟',
			'answer'   => 'به همان بخشی که در «با کدام بخش کار دارید؟» انتخاب می‌کنید. اگر گزینه‌ای انتخاب نکنید، پیام به تیم پشتیبانی می‌رسد و در صورت نیاز به بخش مربوطه ارجاع داده می‌شود.',
		),
		array(
			'question' => 'پاسخ را از چه راهی دریافت می‌کنم؟',
			'answer'   => 'از همان راهی که در فرم وارد کرده‌اید — اگر شماره موبایل بنویسید تماس می‌گیریم و اگر ایمیل بنویسید پاسخ را ایمیل می‌کنیم. وارد کردن یکی از این دو کافی است.',
		),
	);
}

function uid_render_section_curfaq() {
	$tag   = uid_section_tag( 'curfaq', 'h2' );
	$items = uid_section_val( 'curfaq', 'items', uid_default_cu_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" style="padding-block:0 clamp(52px,6.5vw,92px)">
	  <div class="cu-wrap" style="max-width:900px">
	    <div class="sec-head mid rv">
	      <span class="cu-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'curfaq', 'eyebrow', __( 'پیش از تماس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'curfaq', 'heading', __( 'چهار پرسش پرتکرار', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <?php if ( $items ) : ?>
	    <div class="faq rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i">
	        <button class="faq-q" type="button" aria-expanded="false"><span class="fq-tx"><?php echo esc_html( $it['question'] ?? '' ); ?></span><span class="pm" aria-hidden="true"></span></button>
	        <div class="faq-a"><p><?php echo wp_kses( $it['answer'] ?? '', array( 'span' => array( 'class' => true ) ) ); ?></p></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <?php endif; ?>
	  </div>
	</section>
	<?php
}

/**
 * جعبه اعلان کپی‌شدن (کپی آدرس/تلفن/ایمیل) — عنصر ساختاری ثابت صفحه، نه یک سکشن محتوایی.
 */
function uid_render_cu_toast() {
	?>
	<div class="toast" id="toast" aria-live="polite"><span id="toastTx"></span></div>
	<?php
}

/* =====================================================================
 * برگه واقعی خودکارساخته + اسلاگ قابل‌ویرایش
 * ===================================================================== */
function uid_get_cu_page_id() {
	$page_id = (int) get_option( 'uid_cu_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_CU_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_cu_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_cu_page() {
	if ( uid_get_cu_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'تماس با ما', 'uid-theme' ),
		'post_name'   => 'contact-us-new',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_CU_TEMPLATE );
	update_option( 'uid_cu_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_cu_slug_setting() {
	register_setting( 'uid_cu_group', 'uid_cu_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_cu_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_cu_page_slug_section', '', '__return_false', 'uid_cu_layout' );
	add_settings_field( 'uid_cu_page_slug', __( 'آدرس (اسلاگ) صفحه تماس با ما', 'uid-theme' ), 'uid_field_cu_page_slug', 'uid_cu_layout', 'uid_cu_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_cu_slug_setting' );

function uid_field_cu_page_slug( $args ) {
	$page_id = uid_get_cu_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_cu_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_cu_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه تماس با ما هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_cu_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_cu_page_id();

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
function uid_register_cu_settings() {
	register_setting( 'uid_cu_group', 'uid_cu_layout', array(
		'sanitize_callback' => 'uid_sanitize_cu_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_cu_layout_main', '', '__return_false', 'uid_cu_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_cu_layout', 'uid_cu_layout_main', array(
		'option_name' => 'uid_cu_layout', 'registry_fn' => 'uid_cu_sections_registry', 'layout_fn' => 'uid_get_cu_layout',
	) );

	/* ---------------- هیرو + کنسول پاسخگویی زنده ---------------- */
	register_setting( 'uid_cu_group', 'uid_section_curhero', array( 'sanitize_callback' => 'uid_sanitize_section_curhero', 'default' => array() ) );
	add_settings_section( 'uid_section_curhero_main', '', '__return_false', 'uid_section_curhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'eyebrow', 'default' => 'تماس با ما' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ‌های br و mark مجازند)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'heading', 'default' => "راه‌های تماس با یوآیدی<br><mark>سریع‌ترین راه، یک تماس تلفنی است</mark>" ) );
	add_settings_field( 'lede', __( 'توضیح زیر عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'lede', 'default' => 'تلفن، تلگرام، ایمیل یا فرم — هر کدام که برای شما راحت‌تر است. پیش از اینکه چیزی بنویسید، ببینید همین حالا چه کسی پاسخ می‌دهد.' ) );
	add_settings_field( 'telcard_label', __( 'برچسب کارت تلفن بزرگ', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'telcard_label', 'default' => 'تماس مستقیم با دفتر یوآیدی' ) );
	add_settings_field( 'hours_text', __( 'خط ساعات کاری زیر کارت تلفن', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'hours_text', 'default' => 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوی شما هستیم' ) );
	add_settings_field( 'telegram_btn_text', __( 'متن دکمه «پشتیبانی تلگرام»', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'telegram_btn_text', 'default' => 'پشتیبانی تلگرام' ) );
	add_settings_field( 'msg_btn_text', __( 'متن دکمه «ارسال پیام» (پرش به فرم)', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'msg_btn_text', 'default' => 'ارسال پیام' ) );
	add_settings_field( 'avail_title', __( 'عنوان کادر «وضعیت پاسخگویی»', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'avail_title', 'default' => 'وضعیت پاسخگویی' ) );
	add_settings_field( 'avail_hours_line', __( 'خط ساعات کاری داخل کادر وضعیت پاسخگویی', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array(
		'group' => 'uid_section_curhero', 'key' => 'avail_hours_line', 'default' => 'ساعات کاری: شنبه تا چهارشنبه، ۹:۰۰ تا ۱۸:۰۰ به وقت تهران',
		'desc'  => __( 'توجه: ساعت باز/بسته‌بودن واقعی توسط ثابت‌های OPEN_H/CLOSE_H/OPEN_DAYS در assets/js/contact-us-page.js محاسبه می‌شود؛ اگر ساعات کاری واقعی تغییر کرد، آن ثابت‌ها را هم دستی به‌روزرسانی کنید تا با این متن هماهنگ بماند.', 'uid-theme' ),
	) );
	add_settings_field( 'phone_label', __( 'برچسب کانال تلفن', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'phone_label', 'default' => 'تلفن' ) );
	add_settings_field( 'phone_note', __( 'یادداشت کانال تلفن', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'phone_note', 'default' => 'سریع‌ترین راه، در ساعات کاری' ) );
	add_settings_field( 'telegram_label', __( 'برچسب کانال تلگرام', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'telegram_label', 'default' => 'تلگرام' ) );
	add_settings_field( 'telegram_handle', __( 'آی‌دی نمایشی تلگرام (لینک از تب «اطلاعات تماس» می‌آید)', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'telegram_handle', 'default' => 'Uid_poshtiban' ) );
	add_settings_field( 'telegram_note', __( 'یادداشت کانال تلگرام', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'telegram_note', 'default' => 'پشتیبان آنلاین یوآیدی' ) );
	add_settings_field( 'email_label', __( 'برچسب کانال ایمیل', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'email_label', 'default' => 'ایمیل' ) );
	add_settings_field( 'email_note', __( 'یادداشت کانال ایمیل', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'email_note', 'default' => 'مکاتبات رسمی و ارسال مدارک' ) );
	add_settings_field( 'form_chan_label', __( 'برچسب کانال فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'form_chan_label', 'default' => 'فرم تماس' ) );
	add_settings_field( 'form_chan_value', __( 'مقدار نمایشی کانال فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'form_chan_value', 'default' => 'پایین همین صفحه' ) );
	add_settings_field( 'form_chan_note', __( 'یادداشت کانال فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_curhero', 'uid_section_curhero_main', array( 'group' => 'uid_section_curhero', 'key' => 'form_chan_note', 'default' => 'پیام کتبی، با پاسخ به موبایل یا ایمیل شما' ) );
	add_settings_field( 'phone_notice', '', 'uid_field_notice', 'uid_section_curhero', 'uid_section_curhero_main', array( 'text' => __( 'شماره تلفن و لینک تلگرام از تب «اطلاعات تماس» (تنظیمات سراسری قالب) خوانده می‌شوند — اینجا فقط برچسب‌ها و یادداشت‌های کنار آن‌ها قابل ویرایش‌اند.', 'uid-theme' ) ) );

	/* ---------------- با کدام بخش کار دارید؟ ---------------- */
	register_setting( 'uid_cu_group', 'uid_section_curouter', array( 'sanitize_callback' => 'uid_sanitize_section_curouter', 'default' => array() ) );
	add_settings_section( 'uid_section_curouter_main', '', '__return_false', 'uid_section_curouter' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'eyebrow', 'default' => 'بخش‌ها' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'heading', 'default' => 'با کدام بخش کار دارید؟' ) );
	add_settings_field( 'lede', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'lede', 'default' => 'انتخاب کنید تا راه تماس همان بخشی که پاسخ می‌دهد را ببینید.' ) );
	add_settings_field( 'q_text', __( 'سوال بالای تب‌ها', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'q_text', 'default' => 'کدام گزینه به شما نزدیک‌تر است؟' ) );
	add_settings_field( 'qs_text', __( 'زیرنویس سوال بالای تب‌ها', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'qs_text', 'default' => 'اگر مطمئن نیستید، تماس تلفنی یا فرم پایین همیشه به ما می‌رسد.' ) );

	$panes = array(
		'sana'    => array( 'چیپ', 'کاربر شخصی — ثنا و سجام', 'کاربر شخصی', 'پرسش‌های ثنا و سجام را پشتیبانی پاسخ می‌دهد', 'اگر برای خودتان ثنا یا سجام را دنبال می‌کنید، پاسخ از تیم پشتیبانی می‌آید و نه از تیم فروش. سریع‌ترین راه، پیام در تلگرام پشتیبانی است؛ در ساعات کاری می‌توانید تلفنی هم تماس بگیرید.' ),
		'biz'     => array( 'چیپ', 'کسب‌وکارها', 'کسب‌وکارها', 'گفت‌وگو درباره همکاری و قرارداد', 'برای صحبت درباره شرایط همکاری و قرارداد، تلفن سریع‌ترین راه است. اگر ترجیح می‌دهید کتبی بنویسید، فرم را پر کنید تا کارشناس در ساعات کاری با شما تماس بگیرد.' ),
		'dev'     => array( 'چیپ', 'پشتیبانی فنی', 'پشتیبانی فنی', 'مشکل فنی یا پرسش درباره اتصال', 'متن دقیق خطا و زمان رخ دادن آن را در فرم بنویسید یا برای ما ایمیل کنید تا سریع‌تر بررسی شود. برای موارد فوری، تماس تلفنی کوتاه‌ترین راه است.' ),
		'support' => array( 'چیپ', 'مشتریان فعلی', 'مشتریان فعلی', 'پیگیری سرویس فعال', 'برای پیگیری یک مورد باز، تلفن و تلگرام سریع‌ترین راه‌ها هستند. اگر می‌خواهید درخواست‌تان کتبی ثبت شود، از فرم استفاده کنید.' ),
		'press'   => array( 'چیپ', 'رسانه و همکاری', 'رسانه و همکاری', 'رسانه، همکاری و پیشنهاد کاری', 'درخواست مصاحبه، پوشش خبری، همکاری یا ارسال رزومه را برای ما ایمیل کنید. برای پیام کوتاه‌تر، فرم هم در دسترس است.' ),
	);
	$pane_titles = array(
		'sana'    => __( 'کاربر شخصی — ثنا و سجام', 'uid-theme' ),
		'biz'     => __( 'کسب‌وکارها', 'uid-theme' ),
		'dev'     => __( 'پشتیبانی فنی', 'uid-theme' ),
		'support' => __( 'مشتریان فعلی', 'uid-theme' ),
		'press'   => __( 'رسانه و همکاری', 'uid-theme' ),
	);
	foreach ( $panes as $key => $row ) {
		list( , $chip_default, $tag_default, $heading_default, $text_default ) = $row;
		$label = $pane_titles[ $key ];
		add_settings_field( $key . '_chip', sprintf( /* translators: %s: pane label */ __( 'تب «%s» — برچسب دکمه', 'uid-theme' ), $label ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => $key . '_chip', 'default' => $chip_default ) );
		add_settings_field( $key . '_tag', sprintf( /* translators: %s: pane label */ __( 'تب «%s» — برچسب کوچک بالای متن', 'uid-theme' ), $label ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => $key . '_tag', 'default' => $tag_default ) );
		add_settings_field( $key . '_heading', sprintf( /* translators: %s: pane label */ __( 'تب «%s» — عنوان', 'uid-theme' ), $label ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => $key . '_heading', 'default' => $heading_default ) );
		add_settings_field( $key . '_text', sprintf( /* translators: %s: pane label */ __( 'تب «%s» — توضیح', 'uid-theme' ), $label ), 'uid_field_textarea', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => $key . '_text', 'default' => $text_default ) );
	}
	add_settings_field( 'routes_notice', '', 'uid_field_notice', 'uid_section_curouter', 'uid_section_curouter_main', array( 'text' => __( 'کارت‌های راه تماس هر تب (از شماره تلفن/تلگرام/ایمیل سراسری ساخته می‌شوند) و پیکربندی رفتار فرم به ازای هر تب (عنوان/راهنما/پیام موفقیت و نمایش فیلد «نام کسب‌وکار») ثابت‌اند و از پیشخوان قابل‌ویرایش نیستند — برای تغییر، به‌ترتیب uid_cu_router_routes() در inc/contact-us-page.php و آرایه ROUTES در assets/js/contact-us-page.js را ویرایش کنید.', 'uid-theme' ) ) );
	add_settings_field( 'form_title_default', __( 'عنوان اولیه فرم (پیش از انتخاب تب)', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'form_title_default', 'default' => 'ارسال پیام' ) );
	add_settings_field( 'form_hint_default', __( 'راهنمای اولیه فرم (پیش از انتخاب تب)', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'form_hint_default', 'default' => 'نام، یک راه ارتباطی و توضیح کوتاه — همین برای تماس گرفتن با شما کافی است.' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'submit_text', 'default' => 'ارسال پیام' ) );
	add_settings_field( 'privacy_text', __( 'یادداشت حریم خصوصی زیر دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'privacy_text', 'default' => 'اطلاعات شما فقط برای پاسخ به همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'success_title', 'default' => 'پیام شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت اولیه (پیش از انتخاب تب)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_curouter', 'uid_section_curouter_main', array( 'group' => 'uid_section_curouter', 'key' => 'success_text', 'default' => 'در ساعات کاری با شما تماس می‌گیریم. اگر عجله دارید، ۰۲۱۶۶۱۲۳۲۹۰ سریع‌تر است.' ) );

	/* ---------------- آدرس، تماس و ساعات کاری ---------------- */
	register_setting( 'uid_cu_group', 'uid_section_curloc', array( 'sanitize_callback' => 'uid_sanitize_section_curloc', 'default' => array() ) );
	add_settings_section( 'uid_section_curloc_main', '', '__return_false', 'uid_section_curloc' );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای کارت آدرس', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'eyebrow', 'default' => 'دفتر یوآیدی' ) );
	add_settings_field( 'address', __( 'آدرس دفتر', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'address', 'default' => 'فرصت شیرازی' ) );
	add_settings_field( 'email', __( 'ایمیل پشتیبانی', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'email', 'default' => 'info@uid.ir' ) );
	add_settings_field( 'hours_text', __( 'ساعات کاری (متن نمایشی)', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'hours_text', 'default' => 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوی شما هستیم' ) );
	add_settings_field( 'pin_sub', __( 'زیرنویس نشانگر نقشه تزئینی', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'pin_sub', 'default' => 'دفتر یوآیدی — شرکت بینش هوشمند نسل پیشرو' ) );
	add_settings_field( 'namad_title', __( 'عنوان نماد اعتماد الکترونیکی', 'uid-theme' ), 'uid_field_text', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'namad_title', 'default' => 'نماد اعتماد الکترونیکی' ) );
	add_settings_field( 'namad_text', __( 'توضیح نماد اعتماد الکترونیکی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_curloc', 'uid_section_curloc_main', array( 'group' => 'uid_section_curloc', 'key' => 'namad_text', 'default' => 'یوآیدی دارای نماد اعتماد الکترونیکی است. برای دیدن اطلاعات ثبت‌شده نماد، روی آن کلیک کنید.' ) );
	add_settings_field( 'namad_notice', '', 'uid_field_notice', 'uid_section_curloc', 'uid_section_curloc_main', array( 'text' => __( 'نشان eNAMAD فعلاً یک جانگهدار است (باید با انکر و تصویر رسمی صادرشده برای دامنه u-id.net جایگزین شود). نقشه تزئینی کنار کارت آدرس هم ثابت است — از پیشخوان قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_cu_group', 'uid_section_curfaq', array( 'sanitize_callback' => 'uid_sanitize_section_curfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_curfaq_main', '', '__return_false', 'uid_section_curfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_curfaq', 'uid_section_curfaq_main', array( 'group' => 'uid_section_curfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_curfaq', 'uid_section_curfaq_main', array( 'group' => 'uid_section_curfaq', 'key' => 'eyebrow', 'default' => 'پیش از تماس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_curfaq', 'uid_section_curfaq_main', array( 'group' => 'uid_section_curfaq', 'key' => 'heading', 'default' => 'چهار پرسش پرتکرار' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_curfaq', 'uid_section_curfaq_main', array(
		'group' => 'uid_section_curfaq', 'key' => 'items', 'default' => uid_default_cu_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ (تگ span مجاز است — ممکن است هنگام ذخیره حذف شود)', 'uid-theme' ) ),
		),
	) );
}
add_action( 'admin_init', 'uid_register_cu_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_curhero( $input ) {
	return array(
		'title_tag'         => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'           => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'           => wp_kses( $input['heading'] ?? '', array( 'br' => array(), 'mark' => array() ) ),
		'lede'              => sanitize_textarea_field( $input['lede'] ?? '' ),
		'telcard_label'     => sanitize_text_field( $input['telcard_label'] ?? '' ),
		'hours_text'        => sanitize_text_field( $input['hours_text'] ?? '' ),
		'telegram_btn_text' => sanitize_text_field( $input['telegram_btn_text'] ?? '' ),
		'msg_btn_text'      => sanitize_text_field( $input['msg_btn_text'] ?? '' ),
		'avail_title'       => sanitize_text_field( $input['avail_title'] ?? '' ),
		'avail_hours_line'  => sanitize_text_field( $input['avail_hours_line'] ?? '' ),
		'phone_label'       => sanitize_text_field( $input['phone_label'] ?? '' ),
		'phone_note'        => sanitize_text_field( $input['phone_note'] ?? '' ),
		'telegram_label'    => sanitize_text_field( $input['telegram_label'] ?? '' ),
		'telegram_handle'   => sanitize_text_field( $input['telegram_handle'] ?? '' ),
		'telegram_note'     => sanitize_text_field( $input['telegram_note'] ?? '' ),
		'email_label'       => sanitize_text_field( $input['email_label'] ?? '' ),
		'email_note'        => sanitize_text_field( $input['email_note'] ?? '' ),
		'form_chan_label'   => sanitize_text_field( $input['form_chan_label'] ?? '' ),
		'form_chan_value'   => sanitize_text_field( $input['form_chan_value'] ?? '' ),
		'form_chan_note'    => sanitize_text_field( $input['form_chan_note'] ?? '' ),
	);
}

function uid_sanitize_section_curouter( $input ) {
	$out = array(
		'title_tag'          => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'            => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'            => sanitize_text_field( $input['heading'] ?? '' ),
		'lede'               => sanitize_textarea_field( $input['lede'] ?? '' ),
		'q_text'             => sanitize_text_field( $input['q_text'] ?? '' ),
		'qs_text'            => sanitize_text_field( $input['qs_text'] ?? '' ),
		'form_title_default' => sanitize_text_field( $input['form_title_default'] ?? '' ),
		'form_hint_default'  => sanitize_text_field( $input['form_hint_default'] ?? '' ),
		'submit_text'        => sanitize_text_field( $input['submit_text'] ?? '' ),
		'privacy_text'       => sanitize_text_field( $input['privacy_text'] ?? '' ),
		'success_title'      => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'       => sanitize_textarea_field( $input['success_text'] ?? '' ),
	);
	foreach ( array( 'sana', 'biz', 'dev', 'support', 'press' ) as $key ) {
		$out[ $key . '_chip' ]    = sanitize_text_field( $input[ $key . '_chip' ] ?? '' );
		$out[ $key . '_tag' ]     = sanitize_text_field( $input[ $key . '_tag' ] ?? '' );
		$out[ $key . '_heading' ] = sanitize_text_field( $input[ $key . '_heading' ] ?? '' );
		$out[ $key . '_text' ]    = sanitize_textarea_field( $input[ $key . '_text' ] ?? '' );
	}
	return $out;
}

function uid_sanitize_section_curloc( $input ) {
	return array(
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'address'     => sanitize_text_field( $input['address'] ?? '' ),
		'email'       => sanitize_email( $input['email'] ?? '' ),
		'hours_text'  => sanitize_text_field( $input['hours_text'] ?? '' ),
		'pin_sub'     => sanitize_text_field( $input['pin_sub'] ?? '' ),
		'namad_title' => sanitize_text_field( $input['namad_title'] ?? '' ),
		'namad_text'  => sanitize_textarea_field( $input['namad_text'] ?? '' ),
	);
}

function uid_sanitize_section_curfaq( $input ) {
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
