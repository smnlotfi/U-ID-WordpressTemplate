<?php
/**
 * صفحه اختصاصی «یوآیدی‌پلاس (PWA)» — دقیقاً همان الگوی صفحه اصلی:
 * برگه‌ی واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «pwa» نام‌گذاری شده‌اند تا با سکشن‌های هم‌نام صفحه اصلی
 * (مثل «stats») در نام آپشن‌های wp_options تداخل نکنند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_PWA_TEMPLATE', 'template-pwa.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش (دقیقاً مطابق الگوی uid_sections_registry)
 * ===================================================================== */
function uid_pwa_sections_registry() {
	return array(
		'pwahero'     => array( 'label' => __( 'هیرو + محاسبه‌گر هزینه ریزش', 'uid-theme' ), 'icon' => 'dashicons-star-filled' ),
		'pwaproof'    => array( 'label' => __( 'مقایسه قیف تبدیل (مدرک)', 'uid-theme' ),      'icon' => 'dashicons-chart-area' ),
		'pwaleaks'    => array( 'label' => __( 'نقاط ریزش کاربر', 'uid-theme' ),               'icon' => 'dashicons-warning' ),
		'pwacase'     => array( 'label' => __( 'مطالعه موردی', 'uid-theme' ),                  'icon' => 'dashicons-analytics' ),
		'pwacallband' => array( 'label' => __( 'بنر تماس تلفنی', 'uid-theme' ),                 'icon' => 'dashicons-phone' ),
		'pwateam'     => array( 'label' => __( 'تیم و اعتمادسازی', 'uid-theme' ),               'icon' => 'dashicons-groups' ),
		'pwastats'    => array( 'label' => __( 'آمار عملیاتی', 'uid-theme' ),                   'icon' => 'dashicons-chart-bar' ),
		'pwaflow'     => array( 'label' => __( 'مسیر کاربر (استپر ۷مرحله‌ای)', 'uid-theme' ),    'icon' => 'dashicons-list-view' ),
		'pwadetail'   => array( 'label' => __( 'جزئیات سرویس (تب‌ها)', 'uid-theme' ),           'icon' => 'dashicons-grid-view' ),
		'pwapricing'  => array( 'label' => __( 'هزینه سرویس', 'uid-theme' ),                    'icon' => 'dashicons-tag' ),
		'pwabrands'   => array( 'label' => __( 'پذیرندگان یوآیدی', 'uid-theme' ),               'icon' => 'dashicons-awards' ),
		'pwafaq'      => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                  'icon' => 'dashicons-editor-help' ),
		'pwalead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),           'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_pwa_layout() {
	$registry = uid_pwa_sections_registry();
	$saved    = get_option( 'uid_pwa_layout', array() );

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

function uid_sanitize_pwa_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_pwa_sections_registry();
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

function uid_render_pwa_sections() {
	foreach ( uid_get_pwa_layout() as $row ) {
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
function uid_pwa_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_pwa_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3L2 20h20L12 3z"/><path d="M12 9.5v4.5M12 17.5h.01"/></svg>';
}
function uid_pwa_fix_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>';
}
function uid_pwa_demo_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_pwa_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
/**
 * تبدیل عدد لاتین (با نقطه/کاما) به شمایل فارسی رایج در طراحی این صفحه —
 * رقم‌های فارسی + ممیز اعشار «٫» + جداکننده هزارگان «٬» (مثل «۲۳٫۵۹» یا «۲۹٬۴۷۲٬۰۰۰٬۰۰۰»)
 */
function uid_pwa_fa_number( $value ) {
	$value = str_replace( array( '.', ',' ), array( '٫', '٬' ), (string) $value );
	return uid_fa_digits( $value );
}

function uid_pwa_team_icon_svg( $key ) {
	$icons = array(
		'shield'  => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'people'  => '<path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="2" y="13" width="4.5" height="7" rx="1.6"/><rect x="17.5" y="13" width="4.5" height="7" rx="1.6"/><path d="M20 20v.5a3 3 0 01-3 3h-3"/>',
		'gavel'   => '<path d="M12 3v18M7 21h10M5 7l-3 7h6zM19 7l-3 7h6z"/><path d="M4 7h16"/>',
		'lock'    => '<rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}
function uid_pwa_stat_icon_svg( $key ) {
	$icons = array(
		'people'  => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'peak'    => '<path d="M22 7l-8.5 8.5-4-4L2 19"/><path d="M16 7h6v6"/>',
		'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'face'    => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'gavel'   => '<path d="M12 3v18M7 21h10M5 7l-3 7h6zM19 7l-3 7h6z"/><path d="M4 7h16"/>',
		'clock'   => '<circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}
function uid_pwa_bento_icon_svg( $i ) {
	$icons = array(
		'<path d="M4 20c0-2 1-3 3-3s3 1 3 3-1 2-3 2-3 0-3-2z"/><path d="M9.5 15.5L19 6a2.1 2.1 0 00-3-3l-9.5 9.5"/>',
		'<path d="M12 2l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5M3 17l9 5 9-5"/>',
		'<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8.5 6H15a3 3 0 013 3v6.5"/>',
		'<path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="2" y="13" width="4.5" height="7" rx="1.6"/><rect x="17.5" y="13" width="4.5" height="7" rx="1.6"/><path d="M20 20v.5a3 3 0 01-3 3h-3"/>',
	);
	return $icons[ $i % count( $icons ) ];
}
function uid_pwa_adv_icon_svg( $i ) {
	$icons = array(
		'<rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/>',
		'<path d="M12 3v18M7 21h10M5 7l-3 7h6zM19 7l-3 7h6z"/><path d="M4 7h16"/>',
		'<path d="M4 14v-2a8 8 0 0116 0v2"/><rect x="2" y="13" width="4.5" height="7" rx="1.6"/><rect x="17.5" y="13" width="4.5" height="7" rx="1.6"/><path d="M20 20v.5a3 3 0 01-3 3h-3"/>',
		'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'<circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/>',
	);
	return $icons[ $i % count( $icons ) ];
}

/* =====================================================================
 * هیرو + محاسبه‌گر هزینه ریزش
 * ===================================================================== */
function uid_render_section_pwahero() {
	$tag = uid_section_tag( 'pwahero', 'h1' );
	$tags = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'pwahero', 'tags', "پرداخت فقط بابت احراز خاتمه‌یافته\nبا تضمین نرخ تبدیل\nبدون نیاز به نصب اپلیکیشن" ) ) ) );
	$rate_api = uid_section_val( 'pwahero', 'rate_api', '23.59' );
	$rate_pwa = uid_section_val( 'pwahero', 'rate_pwa', '73.19' );
	?>
	<section class="dark heroA">
	  <div class="pwa-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'محصولات', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php echo esc_html( get_the_title() ?: __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ) ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="pwa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'pwahero', 'eyebrow', __( 'یوآیدی‌پلاس · احراز هویت یکپارچه (PWA)', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo esc_html( uid_section_val( 'pwahero', 'heading', __( 'از هر ۱۰۰ کاربری که احراز هویت را شروع می‌کنند، ۷۶ نفر تمام نمی‌کنند.', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'pwahero', 'text', __( 'این عدد فرض نیست — نرخ تبدیل واقعی یک صرافی ایرانی روی مسیر API است. همان قیف، با یوآیدی‌پلاس به ۷۳٫۱۹٪ رسید. محاسبه کنید همین حالا چقدر از دست می‌دهید.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="pwa-btn btn-cta js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو اختصاصی یوآیدی‌پلاس', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی‌پلاس با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo uid_pwa_demo_icon(); ?> <?php echo esc_html( uid_section_val( 'pwahero', 'btn1_text', __( 'درخواست دمو اختصاصی', 'uid-theme' ) ) ); ?></button>
	          <a class="pwa-btn btn-flow" href="<?php echo esc_url( uid_section_val( 'pwahero', 'btn2_url', '#flow' ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M10.2 8.4v7.2l6-3.6z"/></svg> <?php echo esc_html( uid_section_val( 'pwahero', 'btn2_text', __( 'مشاهده جریان محصول', 'uid-theme' ) ) ); ?></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          	<span class="hero-tag"><?php echo uid_pwa_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <div class="engine rv rv-d2" id="engine" data-rate-api="<?php echo esc_attr( (float) $rate_api / 100 ); ?>" data-rate-pwa="<?php echo esc_attr( (float) $rate_pwa / 100 ); ?>">
	        <div class="engine-hd">
	          <span class="live" aria-hidden="true"></span>
	          <b><?php esc_html_e( 'محاسبه‌گر هزینه ریزش', 'uid-theme' ); ?></b><span><?php esc_html_e( 'بر پایه داده واقعی', 'uid-theme' ); ?></span>
	        </div>
	        <div class="eng-fld">
	          <div class="top"><label for="vol"><?php esc_html_e( 'احراز هویت آغازشده در ماه', 'uid-theme' ); ?></label>
	            <output id="volOut">۱۰٬۰۰۰<small><?php esc_html_e( 'نفر', 'uid-theme' ); ?></small></output></div>
	          <input type="range" id="vol" min="500" max="100000" step="500" value="10000" aria-label="<?php esc_attr_e( 'تعداد احراز هویت آغازشده در ماه', 'uid-theme' ); ?>">
	        </div>
	        <div class="eng-fld">
	          <div class="top"><label for="cac"><?php esc_html_e( 'هزینه جذب هر کاربر (CAC)', 'uid-theme' ); ?></label>
	            <output id="cacOut">۱۰۰٬۰۰۰<small><?php esc_html_e( 'تومان', 'uid-theme' ); ?></small></output></div>
	          <input type="range" id="cac" min="10000" max="600000" step="10000" value="100000" aria-label="<?php esc_attr_e( 'هزینه جذب هر کاربر به تومان', 'uid-theme' ); ?>">
	        </div>
	        <div class="eng-out">
	          <div class="eng-cell bad"><span class="k"><?php echo esc_html( sprintf( __( 'تکمیل‌شده با مسیر فعلی (%s٪)', 'uid-theme' ), uid_pwa_fa_number( $rate_api ) ) ); ?></span>
	            <span class="v" id="oApi">۲٬۳۵۹</span></div>
	          <div class="eng-cell good"><span class="k"><?php echo esc_html( sprintf( __( 'تکمیل‌شده با یوآیدی‌پلاس (%s٪)', 'uid-theme' ), uid_pwa_fa_number( $rate_pwa ) ) ); ?></span>
	            <span class="v" id="oPwa">۷٬۳۱۹</span></div>
	          <div class="eng-cell" style="grid-column:1/-1">
	            <span class="k"><?php esc_html_e( 'کاربرانی که ماهانه از دست می‌دهید', 'uid-theme' ); ?></span>
	            <span class="v amt" style="color:#FFC178"><span class="num" id="oGap">۴٬۹۶۰</span><span class="vu"><?php esc_html_e( 'نفر', 'uid-theme' ); ?></span></span></div>
	        </div>
	        <div class="eng-total">
	          <span class="k"><?php esc_html_e( 'هزینه فرصت از دست‌رفته در سال', 'uid-theme' ); ?></span>
	          <span class="v amt"><span class="num" id="oYear">۵٬۹۵۲٬۰۰۰٬۰۰۰</span><span class="vu"><?php esc_html_e( 'تومان', 'uid-theme' ); ?></span></span>
	        </div>
	        <div class="eng-daily"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/></svg>
	          <span><?php esc_html_e( 'هر روزی که تصمیم به تعویق می‌افتد، حدود', 'uid-theme' ); ?> <b id="oDay">۱۶٬۳۰۰٬۰۰۰</b> <?php esc_html_e( 'تومان بودجه جذب کاربر می‌سوزد.', 'uid-theme' ); ?></span>
	        </div>

	        <form class="micro" id="microForm" novalidate>
	          <div class="micro-fields">
	            <p><?php esc_html_e( 'این عدد را روی داده واقعی کسب‌وکار خودتان دقیق کنیم — فقط شماره‌تان را بگذارید.', 'uid-theme' ); ?></p>
	            <div class="micro-row">
	              <div class="fld"><input id="q_tel" name="phone" type="tel" inputmode="numeric" placeholder="۰۹xxxxxxxxx" data-req data-tel aria-label="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>">
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	              <button class="pwa-btn btn-cta" type="button" data-submit><?php esc_html_e( 'محاسبه دقیق', 'uid-theme' ); ?></button>
	            </div>
	          </div>
	          <div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><b><?php esc_html_e( 'ثبت شد', 'uid-theme' ); ?></b>
	            <span><?php esc_html_e( 'کارشناس ما با همین شماره تماس می‌گیرد.', 'uid-theme' ); ?></span></div>
	        </form>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مقایسه قیف تبدیل (مدرک)
 * ===================================================================== */
function uid_default_pwa_funnel_steps( $which ) {
	if ( 'api' === $which ) {
		return array(
			array( 'label' => 'شاهکار ← اطلاعات هویتی', 'percent' => '45.98' ),
			array( 'label' => 'اطلاعات هویتی ← تطبیق چهره', 'percent' => '51.3' ),
			array( 'label' => 'اعتبارسنجی مالی', 'percent' => '' ), // مرحله‌ای که کاربران مسیر API معمولاً به آن نمی‌رسند
		);
	}
	return array(
		array( 'label' => 'شاهکار ← اطلاعات هویتی', 'percent' => '92.01' ),
		array( 'label' => 'اطلاعات هویتی ← اطلاعات بانکی', 'percent' => '86.94' ),
		array( 'label' => 'اطلاعات بانکی ← تطبیق چهره', 'percent' => '91.48' ),
	);
}

function uid_render_section_pwaproof() {
	$tag = uid_section_tag( 'pwaproof', 'h2' );
	$api_steps = uid_section_val( 'pwaproof', 'api_steps', uid_default_pwa_funnel_steps( 'api' ) );
	$pwa_steps = uid_section_val( 'pwaproof', 'pwa_steps', uid_default_pwa_funnel_steps( 'pwa' ) );
	?>
	<section class="sec">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwaproof', 'eyebrow', __( 'مدرک، نه ادعا', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwaproof', 'heading', __( 'همان کاربران، همان بازه زمانی، دو مسیر متفاوت', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pwaproof', 'text', __( 'این ارقام از مقایسه مستقیم عملکرد یک صرافی ارز دیجیتال روی مسیر API با یک پذیرنده روی نسخه PWA استخراج شده است.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="fnl rv" data-rail="fnl">
	      <div class="fnl-track api">
	        <div class="fnl-hd"><b><?php echo esc_html( uid_section_val( 'pwaproof', 'api_track_title', __( 'مسیر فعلی — یکپارچه‌سازی API', 'uid-theme' ) ) ); ?></b><span class="tiny"><?php echo esc_html( uid_section_val( 'pwaproof', 'api_track_note', __( 'داده واقعی رمزینکس', 'uid-theme' ) ) ); ?></span></div>
	        <span class="fnl-tot"><?php echo esc_html( uid_pwa_fa_number( uid_section_val( 'pwaproof', 'api_total', '23.59' ) ) ); ?>٪</span>
	        <span class="fnl-cap"><?php esc_html_e( 'نرخ تبدیل کل قیف احراز هویت', 'uid-theme' ); ?></span>
	        <div class="fnl-steps">
	          <?php foreach ( $api_steps as $s ) : ?>
	          <?php $has_pct = '' !== trim( (string) ( $s['percent'] ?? '' ) ); ?>
	          <div class="fstep"<?php echo $has_pct ? '' : ' style="opacity:.4"'; ?>><div class="lb"><span><?php echo esc_html( $s['label'] ?? '' ); ?></span><em><?php echo $has_pct ? esc_html( $s['percent'] ) . '%' : '—'; ?></em></div>
	            <div class="fbar" style="--w:<?php echo $has_pct ? esc_attr( $s['percent'] ) . '%' : '0%'; ?>"><i></i></div></div>
	          <?php endforeach; ?>
	        </div>
	      </div>
	      <div class="fnl-track pwa">
	        <div class="fnl-hd"><b><?php echo esc_html( uid_section_val( 'pwaproof', 'pwa_track_title', __( 'مسیر یوآیدی‌پلاس — PWA', 'uid-theme' ) ) ); ?></b><span class="tiny"><?php echo esc_html( uid_section_val( 'pwaproof', 'pwa_track_note', __( 'همان بازه زمانی', 'uid-theme' ) ) ); ?></span></div>
	        <span class="fnl-tot"><?php echo esc_html( uid_pwa_fa_number( uid_section_val( 'pwaproof', 'pwa_total', '73.19' ) ) ); ?>٪</span>
	        <span class="fnl-cap"><?php esc_html_e( 'نرخ تبدیل کل قیف احراز هویت', 'uid-theme' ); ?></span>
	        <div class="fnl-steps">
	          <?php foreach ( $pwa_steps as $s ) : ?>
	          <?php $has_pct = '' !== trim( (string) ( $s['percent'] ?? '' ) ); ?>
	          <div class="fstep"<?php echo $has_pct ? '' : ' style="opacity:.4"'; ?>><div class="lb"><span><?php echo esc_html( $s['label'] ?? '' ); ?></span><em><?php echo $has_pct ? esc_html( $s['percent'] ) . '%' : '—'; ?></em></div>
	            <div class="fbar" style="--w:<?php echo $has_pct ? esc_attr( $s['percent'] ) . '%' : '0%'; ?>"><i></i></div></div>
	          <?php endforeach; ?>
	        </div>
	      </div>
	      <div class="fnl-note">
	        <b class="lat"><?php echo esc_html( uid_section_val( 'pwaproof', 'multiplier_value', '3.1×' ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'pwaproof', 'multiplier_text', __( 'نرخ تبدیل نسخه PWA نسبت به یکپارچه‌سازی مستقیم API — روی حجم واقعی یک صرافی ایرانی، در یک بازه یکسان.', 'uid-theme' ) ) ); ?></span>
	      </div>
	    </div>
	    <div class="dots" data-dots="fnl"></div>
	    <div class="fnl-note-m"><b class="lat"><?php echo esc_html( uid_section_val( 'pwaproof', 'multiplier_value', '3.1×' ) ); ?></b><span><?php echo esc_html( uid_section_val( 'pwaproof', 'multiplier_text', __( 'نرخ تبدیل نسخه PWA نسبت به یکپارچه‌سازی مستقیم API — روی حجم واقعی یک صرافی ایرانی.', 'uid-theme' ) ) ); ?></span></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * نقاط ریزش کاربر
 * ===================================================================== */
function uid_default_pwa_leaks() {
	return array(
		array(
			'title' => 'اعتبارسنجی شماره موبایل، دو بار تکرار می‌شود',
			'bad_text' => 'در مسیر API، ثبت و استعلام شماره تلفن همراه برای ارتقای سطح کاربری دو مرتبه انجام می‌شود. نتیجه: تجربه‌ای خسته‌کننده برای کاربر و دو برابر شدن هزینه سرویس شاهکار برای شما.',
			'fix_text' => 'کاربر یک بار فرآیند احراز را طی می‌کند و شرایط لازم برای استفاده از تمام سطوح کاربری را به‌دست می‌آورد. یک بار استعلام، یک بار هزینه.',
		),
		array(
			'title' => 'کاربر باید نام و نام خانوادگی را دستی تایپ کند',
			'bad_text' => 'با پراکندگی بازه سنی کاربران، غلط املایی رایج است و هر غلط املایی یعنی یک احراز ناموفق یا یک تیکت پشتیبانی.',
			'fix_text' => 'کاربر فقط کد ملی و تاریخ تولد را وارد می‌کند؛ باقی اطلاعات هویتی مستقیماً از سازمان ثبت احوال فراخوانی و به‌صورت خودکار در سامانه ثبت می‌شود.',
		),
		array(
			'title' => 'بارگذاری تصویر مدارک، خطاهای زنجیره‌ای می‌سازد',
			'bad_text' => 'بی‌کیفیت بودن تصویر، قاب‌بندی نامناسب هنگام اسکن، یا عدم تطابق مدرک به‌دلیل تغییر محل سکونت و کدپستی — همگی به خطای سامانه و رهاشدن فرآیند ختم می‌شوند.',
			'fix_text' => 'با درج کد ملی در ابتدای فرآیند، تمام این موارد به‌صورت خودکار استعلام می‌شود. اخذ استعلام از پلیس فتا، بارگذاری تصویر کارت ملی و سایر مدارک شناسایی را کاملاً حذف می‌کند.',
		),
		array(
			'title' => 'خطای سرویس شاهکار، مسیر را به بن‌بست می‌رساند',
			'bad_text' => 'در بسیاری از موارد ادامه فرآیند به‌دلیل خطای شاهکار ممکن نیست و تنها راه باقی‌مانده، ارسال ویدئوی سلفی است — نقطه‌ای که بخش بزرگی از کاربران کنار می‌کشند.',
			'fix_text' => 'در نسخه PWA بارگذاری مدارک در این مرحله لازم نیست و معماری Multi-Vendor باعث می‌شود قطعی یا خطای یک تامین‌کننده، کل فرآیند شما را متوقف نکند.',
		),
	);
}

function uid_render_section_pwaleaks() {
	$tag   = uid_section_tag( 'pwaleaks', 'h2' );
	$items = uid_section_val( 'pwaleaks', 'items', uid_default_pwa_leaks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="pwa-wrap">
	    <div class="sec-head rv">
	      <span class="pwa-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'pwaleaks', 'eyebrow', __( 'چهار نقطه‌ای که کاربر شما را ترک می‌کند', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwaleaks', 'heading', __( 'ریزش، تصادفی نیست. چهار دلیل مشخص دارد.', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pwaleaks', 'text', __( 'هر مورد زیر یک علت تکرارشونده و مستند برای رهاکردن فرآیند احراز هویت است — نه یک فرضیه.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="leak-grid rail rv rv-d1" data-rail="leaks">
	      <?php foreach ( $items as $i => $it ) : ?>
	      <article class="leak">
	        <div class="leak-bad">
	          <div class="leak-hd">
	            <div><span class="tag"><?php echo uid_pwa_warn_icon(); ?> <?php esc_html_e( 'نقطه ریزش', 'uid-theme' ); ?></span><h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3></div>
	            <span class="leak-n"><?php echo esc_html( uid_fa_digits( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
	          </div><p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p>
	        </div>
	        <div class="leak-fix"><span class="tag"><?php echo uid_pwa_fix_icon(); ?> <?php echo esc_html( $it['fix_label'] ?? __( 'در یوآیدی‌پلاس', 'uid-theme' ) ); ?></span><p><?php echo esc_html( $it['fix_text'] ?? '' ); ?></p></div>
	      </article>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="leaks"></div>
	    <div class="swipe-hint"><?php echo uid_pwa_arrow_icon(); ?> <?php esc_html_e( 'برای دیدن موارد بعدی، بکشید', 'uid-theme' ); ?></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مطالعه موردی
 * ===================================================================== */
function uid_default_pwa_case_rows() {
	return array(
		array( 'label' => 'ورودی شاهکار', 'api_value' => '48,653', 'pwa_value' => '48,653' ),
		array( 'label' => 'تکمیل اطلاعات هویتی', 'api_value' => '22,373', 'pwa_value' => '44,765' ),
		array( 'label' => 'تکمیل احراز تصویری', 'api_value' => '11,479', 'pwa_value' => '40,951' ),
	);
}

function uid_render_section_pwacase() {
	$tag  = uid_section_tag( 'pwacase', 'h2' );
	$rows = uid_section_val( 'pwacase', 'rows', uid_default_pwa_case_rows() );
	?>
	<section class="sec">
	  <div class="pwa-wrap">
	    <div class="cs">
	      <div class="rv">
	        <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwacase', 'eyebrow', __( 'مطالعه موردی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 14px">'; ?><?php echo esc_html( uid_section_val( 'pwacase', 'heading', __( 'وقتی همین اعداد را روی حجم واقعی یک صرافی بگذاریم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede"><?php echo esc_html( uid_section_val( 'pwacase', 'text', __( 'با اعمال نرخ تبدیل نسخه PWA روی حجم واقعی شاهکار رمزینکس، تعداد کاربرانی که تا انتهای فرآیند می‌رسند بیش از دو برابر می‌شود — و شکاف بین این دو عدد، دقیقاً همان بودجه جذب کاربری است که امروز هدر می‌رود.', 'uid-theme' ) ) ); ?></p>
	        <div class="cs-big">
	          <span class="k"><?php echo esc_html( uid_section_val( 'pwacase', 'big_label', __( 'هزینه فرصت از دست‌رفته، به ازای CAC معادل ۱٬۰۰۰٬۰۰۰ ریال', 'uid-theme' ) ) ); ?></span>
	          <span class="amt"><span class="v num" data-count="<?php echo esc_attr( uid_section_val( 'pwacase', 'big_value', '29472000000' ) ); ?>"><?php echo esc_html( uid_pwa_fa_number( number_format( (float) uid_section_val( 'pwacase', 'big_value', '29472000000' ) ) ) ); ?></span><span class="u"><?php echo esc_html( uid_section_val( 'pwacase', 'big_suffix', __( 'ریال', 'uid-theme' ) ) ); ?></span></span>
	          <small><?php echo esc_html( uid_section_val( 'pwacase', 'big_note', __( 'یعنی نزدیک به ۲٬۹۴۷ میلیون تومان بودجه جذب کاربر، صرف کاربرانی شده که از یک قیفِ قابل‌اصلاح بیرون افتاده‌اند.', 'uid-theme' ) ) ); ?></small>
	        </div>
	      </div>
	      <div class="rv rv-d1">
	        <div class="cs-tbl">
	          <div class="row hd"><span><?php esc_html_e( 'مرحله', 'uid-theme' ); ?></span><span class="b2"><?php esc_html_e( 'مسیر API', 'uid-theme' ); ?></span><span class="b3"><?php esc_html_e( 'با یوآیدی‌پلاس', 'uid-theme' ); ?></span></div>
	          <?php foreach ( $rows as $r ) : ?>
	          <div class="row"><span><?php echo esc_html( $r['label'] ?? '' ); ?></span><span class="b2"><?php echo esc_html( $r['api_value'] ?? '' ); ?></span><span class="b3"><?php echo esc_html( $r['pwa_value'] ?? '' ); ?></span></div>
	          <?php endforeach; ?>
	          <div class="row tot"><span><?php esc_html_e( 'نرخ تبدیل کل', 'uid-theme' ); ?></span><span class="b2"><?php echo esc_html( uid_section_val( 'pwaproof', 'api_total', '23.59' ) ); ?>%</span><span class="b3"><?php echo esc_html( uid_section_val( 'pwaproof', 'pwa_total', '73.19' ) ); ?>%</span></div>
	        </div>
	        <p class="tiny" style="margin-top:14px"><?php echo esc_html( uid_section_val( 'pwacase', 'cost_note_prefix', __( 'هزینه احراز کامل در سناریوی PWA:', 'uid-theme' ) ) ); ?> <b class="mono"><?php echo esc_html( uid_section_val( 'pwacase', 'cost_value', '8,067,347,000' ) ); ?></b> <?php echo esc_html( uid_section_val( 'pwacase', 'cost_note_suffix', __( 'ریال — در برابر هزینه فرصتی که در ستون بالا از دست می‌رود.', 'uid-theme' ) ) ); ?></p>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس تلفنی
 * ===================================================================== */
function uid_render_section_pwacallband() {
	?>
	<section style="padding-block:0 var(--sec)">
	  <div class="pwa-wrap">
	    <div class="callband rv">
	      <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg></span>
	      <div class="tx">
	        <b><?php echo esc_html( uid_section_val( 'pwacallband', 'heading', __( 'می‌خواهید همین حالا عدد دقیق کسب‌وکار خودتان را بدانید؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'pwacallband', 'text', __( 'یک تماس کوتاه کافی است. کارشناس فنی یوآیدی نرخ تبدیل فعلی و حجم ماهانه شما را می‌گیرد و همان‌جا محاسبه می‌کند که یوآیدی‌پلاس چند کاربر و چقدر بودجه برایتان برمی‌گرداند.', 'uid-theme' ) ) ); ?></p>
	      </div>
	      <div class="acts">
	        <a class="pwa-btn btn-cta" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="pwa-btn btn-ghost-d js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست تماس از کارشناسان یوآیدی‌پلاس', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا در سریع‌ترین زمان با شما تماس بگیریم.', 'uid-theme' ); ?>"><?php echo uid_pwa_demo_icon(); ?> <?php echo esc_html( uid_section_val( 'pwacallband', 'btn_text', __( 'شما با من تماس بگیرید', 'uid-theme' ) ) ); ?></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * تیم و اعتمادسازی
 * ===================================================================== */
function uid_default_pwa_team_rows() {
	return array(
		array( 'icon' => 'shield', 'title' => 'اپراتور اول احراز هویت ایران، از سال ۱۳۹۶', 'text' => 'شرکت دانش‌بنیان بینش هوشمند نسل پیشرو، کارگزار مورد تایید سامانه‌های سجام و ثنا و نهادهای مهم کشور.' ),
		array( 'icon' => 'people', 'title' => 'راهبری کاربران، به‌جای تیم پشتیبانی شما', 'text' => 'هدایت کاربران تا تکمیل فرآیند احراز هویت توسط یوآیدی انجام می‌شود — پشتیبانی ۲۴/۷، همیشه در دسترس.' ),
		array( 'icon' => 'gavel', 'title' => 'پاسخ‌گویی استعلامات قضائی در کمتر از ۱ ساعت', 'text' => 'ارتباط با پلیس فتا و مراجع قضائی، همراه با دسترسی رایگان به سرویس کشف و پیشگیری از تقلب (FDP).' ),
		array( 'icon' => 'lock', 'title' => 'تعهد به SLA و رعایت استانداردهای امنیتی', 'text' => 'معماری Multi-Vendor برای هر سرویس، تا قطعی یک تامین‌کننده کسب‌وکار شما را متوقف نکند.' ),
	);
}

function uid_render_section_pwateam() {
	$tag  = uid_section_tag( 'pwateam', 'h2' );
	$rows = uid_section_val( 'pwateam', 'rows', uid_default_pwa_team_rows() );
	if ( ! is_array( $rows ) || empty( $rows ) ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="pwa-wrap">
	    <div class="team">
	      <div class="rv">
	        <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwateam', 'eyebrow', __( 'تیمی که پشت این عدد ایستاده', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec" style="margin-block:16px 14px">'; ?><?php echo esc_html( uid_section_val( 'pwateam', 'heading', __( 'شما یک ابزار نمی‌خرید؛ یک تیم متخصص را کنار خودتان می‌آورید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede" style="margin-block-end:8px"><?php echo esc_html( uid_section_val( 'pwateam', 'text', __( 'مأموریت این سرویس، «برون‌سپاری احراز هویت» است — یعنی مسئولیت راهبری کاربر، پاسخ‌گویی به استعلامات قضایی، و بار عملیاتی مرکز تماس از دوش تیم شما برداشته می‌شود.', 'uid-theme' ) ) ); ?></p>
	        <div class="team-list" data-rail="team">
	          <?php foreach ( $rows as $r ) : ?>
	          <div class="team-row"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo uid_pwa_team_icon_svg( $r['icon'] ?? 'shield' ); ?></svg></span><div>
	            <b><?php echo esc_html( $r['title'] ?? '' ); ?></b>
	            <p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div></div>
	          <?php endforeach; ?>
	        </div>
	        <div class="dots" data-dots="team"></div>
	      </div>
	      <div class="rv rv-d1">
	        <div class="team-badge">
	          <p class="q"><?php echo esc_html( uid_section_val( 'pwateam', 'quote_text', __( '«چه می‌شد اگر می‌توانستیم هویتمان را در اینترنت ضمانت کنیم؟ یک انقلاب بزرگ رخ می‌داد؛ دیگر لازم نبود برای ثبت‌نام کاری جز گرفتن یک ویدئوی سلفی انجام دهیم.»', 'uid-theme' ) ) ); ?></p>
	          <div class="by"><span class="av">یو</span><div><b><?php echo esc_html( uid_section_val( 'pwateam', 'quote_by_name', __( 'تیم یوآیدی', 'uid-theme' ) ) ); ?></b><span><?php echo esc_html( uid_section_val( 'pwateam', 'quote_by_role', __( 'هویت امن دیجیتال', 'uid-theme' ) ) ); ?></span></div></div>
	        </div>
	        <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap">
	          <button class="pwa-btn pwa-btn-navy js-demo-btn" style="flex:1;min-width:180px" data-modal-title="<?php esc_attr_e( 'صحبت با کارشناس فنی یوآیدی‌پلاس', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان فنی با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo uid_pwa_demo_icon(); ?> <?php echo esc_html( uid_section_val( 'pwateam', 'btn_text', __( 'صحبت با کارشناس فنی', 'uid-theme' ) ) ); ?></button>
	          <a class="pwa-btn btn-ghost" style="flex:1;min-width:180px" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <?php esc_html_e( 'تماس مستقیم', 'uid-theme' ); ?></a>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * آمار عملیاتی (opanel)
 * ===================================================================== */
function uid_default_pwa_stats() {
	return array(
		array( 'icon' => 'people', 'featured' => '1', 'value' => '+۵٬۰۰۰٬۰۰۰', 'label' => 'کل احراز هویت موفق انجام‌شده از ۱۳۹۶ تا امروز' ),
		array( 'icon' => 'peak', 'featured' => '1', 'value' => '+۶۰٬۰۰۰', 'label' => 'بیشترین احراز هویت موفق در یک روز' ),
		array( 'icon' => 'shield', 'featured' => '', 'value' => '۹۹٫۵٪', 'label' => 'آپ‌تایم تعهدی سرویس‌های یوآیدی‌پلاس', 'meter' => '99.5' ),
		array( 'icon' => 'face', 'featured' => '', 'value' => '۹۹٪', 'label' => 'دقت هوش مصنوعی در تطبیق چهره با تصویر مرجع', 'meter' => '99' ),
		array( 'icon' => 'gavel', 'featured' => '', 'value' => 'زیر ۳۰ دقیقه', 'label' => 'ارسال استعلام به پلیس فتا و مراجع قضائی' ),
		array( 'icon' => 'clock', 'featured' => '', 'value' => 'زیر ۵ دقیقه', 'label' => 'اعلام نتیجه نهایی احراز شامل آدیت و پردازش گروهی' ),
	);
}

function uid_render_section_pwastats() {
	$tag   = uid_section_tag( 'pwastats', 'h2' );
	$items = uid_section_val( 'pwastats', 'items', uid_default_pwa_stats() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec dark">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'pwastats', 'eyebrow', __( 'از نگاه آمار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwastats', 'heading', __( 'زیرساختی که سال‌هاست زیر بار واقعی کار می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'pwastats', 'text', __( 'این اعداد سقف تبلیغاتی نیستند؛ تعهد سرویس و کارنامه عملیاتی یوآیدی‌پلاس هستند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="opanel rv rv-d1">
	      <?php foreach ( $items as $it ) : ?>
	      <article class="ostat<?php echo ! empty( $it['featured'] ) ? ' feat' : ''; ?>">
	        <span class="oic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo uid_pwa_stat_icon_svg( $it['icon'] ?? 'shield' ); ?></svg></span>
	        <span class="ov<?php echo ! is_numeric( str_replace( array( '٪', '%', '٫', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), array( '', '', '.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $it['value'] ?? '' ) ) ? ' txt' : ''; ?>"><?php echo esc_html( $it['value'] ?? '' ); ?></span>
	        <span class="ol"><?php echo esc_html( $it['label'] ?? '' ); ?></span>
	        <?php if ( ! empty( $it['meter'] ) ) : ?><div class="ometer" style="--p:<?php echo esc_attr( $it['meter'] ); ?>%"><i></i></div><?php endif; ?>
	      </article>
	      <?php endforeach; ?>
	    </div>
	    <div class="opanel-foot rv rv-d2">
	      <span><?php echo esc_html( uid_section_val( 'pwastats', 'foot_text', __( 'می‌خواهید SLA و جزئیات فنی را ببینید؟', 'uid-theme' ) ) ); ?></span>
	      <a class="pwa-btn btn-ghost-d pwa-btn-sm" href="<?php echo esc_url( uid_section_val( 'pwastats', 'foot_link_url', '/pwa-ekyc-docs/' ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/></svg> <?php echo esc_html( uid_section_val( 'pwastats', 'foot_link_text', __( 'مستندات فنی', 'uid-theme' ) ) ); ?></a>
	      <a class="pwa-btn btn-cta pwa-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مسیر کاربر (استپر ۷مرحله‌ای) — نمای گوشی، تعامل و تصاویر Mock ثابت می‌مانند؛
 * فقط متن هر گام از تنظیمات می‌آید.
 * ===================================================================== */
function uid_default_pwa_flow_steps() {
	return array(
		array( 'cap' => 'شروع', 'title' => 'هدایت کاربر به یوآیدی', 'text' => 'شما لینک وب‌اپلیکیشن یوآیدی را با پارامترهای مشخص می‌سازید و کلاینت کاربر آن را فراخوانی می‌کند. از این لحظه، کل فرآیند احراز درون راهکار یوآیدی طی می‌شود و تیم شما دیگر درگیر هیچ مرحله‌ای نیست.', 'chips' => "اقدام کاربر: یک کلیک\nپارامتر الزامی: requestBusinessId" ),
		array( 'cap' => 'شماره موبایل', 'title' => 'اعتبارسنجی تلفن همراه کاربر', 'text' => 'تلفن همراه کاربر پس از دریافت، از طریق ارسال OTP اعتبارسنجی می‌گردد. اگر شماره را در پارامتر فراخوانی بفرستید، این فیلد به‌صورت Auto-fill و غیرقابل ویرایش تکمیل می‌شود — یک مرحله کمتر برای کاربر.', 'chips' => "ورودی کاربر: کد یک‌بارمصرف\nپارامتر اختیاری: phoneNumber" ),
		array( 'cap' => 'هویت', 'title' => 'دریافت کد ملی و تاریخ تولد', 'text' => 'با دریافت مشخصات هویتی پایه، اطلاعات کاربر از سامانه‌های مربوطه فراخوانی می‌شود. کاربر نام و نام خانوادگی خود را تایپ نمی‌کند — همین یک تغییر، بزرگ‌ترین نقطه ریزش مسیر API را حذف می‌کند.', 'chips' => "ورودی کاربر: کد ملی + تاریخ تولد\nمنبع: شاهکار\nمنبع: سازمان ثبت احوال" ),
		array( 'cap' => 'آدرس', 'title' => 'دریافت آدرس و کد پستی', 'text' => 'آدرس کاربر دریافت و علاوه بر آن، کد پستی ثبت‌شده توسط وی نیز از سامانه‌های مربوطه استعلام می‌گردد. نیازی به بارگذاری قبض یا مدرک سکونت نیست.', 'chips' => "ورودی کاربر: آدرس\nمنبع: سامانه کد پستی" ),
		array( 'cap' => 'اطلاعات مالی', 'title' => 'اعتبارسنجی اطلاعات مالی', 'text' => 'با دریافت یک شماره کارت از کاربر، علاوه بر تطبیق مالکیت حساب، شماره شبای مربوطه نیز استعلام خواهد شد. یک ورودی، سه تاییدیه.', 'chips' => "ورودی کاربر: شماره کارت\nمنبع: سامانه‌های بانکی" ),
		array( 'cap' => 'بیومتریک', 'title' => 'احراز هویت بیومتریک (ویدئوی سلفی)', 'text' => 'یک ویدئوی ۵ ثانیه‌ای از کاربر دریافت و چند فریم آن با استفاده از هوش مصنوعی با تصویر مرجع ثبت احوال تطابق داده می‌شود. دقت ماژول تطبیق چهره ۹۹٪ است.', 'chips' => "ورودی کاربر: ویدئوی ۵ ثانیه‌ای\nمنبع: تصویر مرجع ثبت احوال\nتشخیص زنده‌بودن" ),
		array( 'cap' => 'تحویل نتیجه', 'title' => 'اعلام «نتیجه احراز هویت» به پذیرنده', 'text' => 'پس از خاتمه فرآیند، نتیجه احراز هویت به همراه کلیه اطلاعات کاربر — اطلاعات هویتی، بانکی و آدرس — در اختیار شما قرار می‌گیرد.', 'chips' => "خروجی: clientToken + status\nسرویس: getUserInfo" ),
	);
}

function uid_render_section_pwaflow() {
	$tag   = uid_section_tag( 'pwaflow', 'h2' );
	$steps = uid_section_val( 'pwaflow', 'steps', uid_default_pwa_flow_steps() );
	if ( ! is_array( $steps ) || empty( $steps ) ) return;
	$last = count( $steps ) - 1;
	?>
	<section class="sec" id="flow">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwaflow', 'eyebrow', __( 'مسیر کاربر', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwaflow', 'heading', __( 'هفت گام، یک نشست، بدون خروج از فضای شما', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pwaflow', 'text', __( 'روی هر گام بزنید تا ببینید کاربر چه چیزی وارد می‌کند و اطلاعات از کدام مرجع رسمی استعلام می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="stepper rv rv-d1">
	      <div class="st-track" id="stTrack" role="tablist" aria-label="<?php esc_attr_e( 'مراحل احراز هویت', 'uid-theme' ); ?>">
	        <?php foreach ( $steps as $i => $s ) : ?>
	        	<button class="st-node<?php echo 0 === $i ? ' on done' : ''; ?>" data-step="<?php echo esc_attr( $i ); ?>" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"><span class="st-dot"><?php echo esc_html( uid_fa_digits( $i ) ); ?></span><span class="st-cap"><?php echo esc_html( $s['cap'] ?? '' ); ?></span></button>
	        <?php endforeach; ?>
	      </div>
	      <div class="st-stage">
	        <div class="st-device">
	          <?php uid_pwa_render_phone_mock(); ?>
	          <span class="st-device-cap"><?php esc_html_e( 'نمای واقعی صفحه‌ای که کاربر شما می‌بیند', 'uid-theme' ); ?></span>
	        </div>
	        <div class="st-panel">
	          <div class="st-body" id="stBody">
	            <?php foreach ( $steps as $i => $s ) :
	            	$chips = array_filter( array_map( 'trim', explode( "\n", $s['chips'] ?? '' ) ) );
	            	?>
	            	<div class="st-pane" data-pane="<?php echo esc_attr( $i ); ?>"<?php echo 0 === $i ? '' : ' hidden'; ?>>
	            	  <span class="st-eyebrow"><?php echo esc_html( sprintf( __( 'گام %1$s از %2$s', 'uid-theme' ), uid_fa_digits( $i ), uid_fa_digits( $last ) ) ); ?></span>
	            	  <h3><?php echo esc_html( $s['title'] ?? '' ); ?></h3>
	            	  <p><?php echo esc_html( $s['text'] ?? '' ); ?></p>
	            	  <?php if ( $chips ) : ?>
	            	  <div class="st-chips">
	            	    <?php foreach ( $chips as $c ) : ?>
	            	    	<span><?php echo esc_html( $c ); ?></span>
	            	    <?php endforeach; ?>
	            	  </div>
	            	  <?php endif; ?>
	            	</div>
	            <?php endforeach; ?>
	          </div>
	          <div class="st-nav">
	            <button id="stPrev" aria-label="<?php esc_attr_e( 'مرحله قبلی', 'uid-theme' ); ?>" disabled><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></button>
	            <span class="st-count" id="stCount"><?php echo esc_html( uid_fa_digits( '1 / ' . count( $steps ) ) ); ?></span><span class="st-autoplay"><i></i><?php esc_html_e( 'پخش خودکار', 'uid-theme' ); ?></span>
	            <button id="stNext" aria-label="<?php esc_attr_e( 'مرحله بعدی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg></button>
	          </div>
	        </div>
	      </div>
	      <div class="st-foot">
	        <span class="small"><?php esc_html_e( 'کل این هفت گام در یک نشست و بدون خروج کاربر از فضای شما انجام می‌شود.', 'uid-theme' ); ?></span>
	        <a class="tlink" href="<?php echo esc_url( uid_section_val( 'pwaflow', 'foot_link_url', '/pwa-ekyc-docs/' ) ); ?>"><?php echo esc_html( uid_section_val( 'pwaflow', 'foot_link_text', __( 'مستندات فنی فراخوانی PWA', 'uid-theme' ) ) ); ?> <?php echo uid_pwa_arrow_icon(); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/**
 * موکاپ تزئینی گوشی با ۷ صفحه‌نمای محصول — دقیقاً مطابق نمونه HTML، ثابت (مثل
 * hero-art صفحه اصلی)؛ فقط با شماره گام (data-scr) با استپر بالا همگام می‌شود.
 */
function uid_pwa_render_phone_mock() {
	?>
	<div class="phone" id="phone">
	  <div class="scr on" data-scr="0">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۰/۶</span></div>
	    <div class="sdone"><div><span class="spill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/></svg> <?php esc_html_e( 'اتصال امن', 'uid-theme' ); ?></span>
	      <div class="rg" style="background:rgba(41,188,206,.14);border-color:#29BCCE;color:#7FE0EC"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></div>
	      <h4 style="text-align:center"><?php esc_html_e( 'در حال انتقال به یوآیدی', 'uid-theme' ); ?></h4>
	      <p style="text-align:center"><?php esc_html_e( 'فرآیند احراز هویت شما در محیط امن یوآیدی انجام می‌شود.', 'uid-theme' ); ?></p></div></div>
	  </div>
	  <div class="scr" data-scr="1">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۱/۶</span></div>
	    <h4><?php esc_html_e( 'کد تایید را وارد کنید', 'uid-theme' ); ?></h4>
	    <p><?php esc_html_e( 'کد پنج‌رقمی به شماره', 'uid-theme' ); ?> <span class="mono">۰۹۱۲···۴۵۶۷</span> <?php esc_html_e( 'ارسال شد.', 'uid-theme' ); ?></p>
	    <div class="sotp"><i class="f">۴</i><i>۸</i><i>۱</i><i>۹</i><i>۲</i></div>
	    <span class="spill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/></svg> <?php esc_html_e( 'ارسال مجدد تا ۰۰:۴۵', 'uid-theme' ); ?></span>
	    <div class="sbtn"><?php esc_html_e( 'تایید و ادامه', 'uid-theme' ); ?></div>
	  </div>
	  <div class="scr" data-scr="2">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۲/۶</span></div>
	    <h4><?php esc_html_e( 'مشخصات هویتی', 'uid-theme' ); ?></h4>
	    <p><?php esc_html_e( 'فقط کد ملی و تاریخ تولد لازم است.', 'uid-theme' ); ?></p>
	    <div class="sfield"><span class="lb"><?php esc_html_e( 'کد ملی', 'uid-theme' ); ?></span><span class="vl">۰۰۱۲۳۴۵۶۷۸</span></div>
	    <div class="sfield"><span class="lb"><?php esc_html_e( 'تاریخ تولد', 'uid-theme' ); ?></span><span class="vl">۱۳۷۴/۰۹/۰۶</span></div>
	    <div class="sfield auto"><span class="lb"><?php esc_html_e( 'نام و نام خانوادگی — از ثبت احوال', 'uid-theme' ); ?></span><span class="vl fa"><?php esc_html_e( 'سارا محمدی', 'uid-theme' ); ?></span></div>
	    <div class="sbtn"><?php esc_html_e( 'تایید و ادامه', 'uid-theme' ); ?></div>
	  </div>
	  <div class="scr" data-scr="3">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۳/۶</span></div>
	    <h4><?php esc_html_e( 'آدرس محل سکونت', 'uid-theme' ); ?></h4>
	    <p><?php esc_html_e( 'کد پستی به‌صورت خودکار استعلام می‌شود.', 'uid-theme' ); ?></p>
	    <div class="sfield"><span class="lb"><?php esc_html_e( 'کد پستی', 'uid-theme' ); ?></span><span class="vl">۱۹۶۸۹۳۳۱۱۹</span></div>
	    <div class="sfield auto"><span class="lb"><?php esc_html_e( 'آدرس تاییدشده — استعلام‌شده', 'uid-theme' ); ?></span>
	      <span class="vl fa"><?php esc_html_e( 'تهران، ونک، خیابان نمونه، پلاک ۱۲', 'uid-theme' ); ?></span></div>
	    <div class="sbtn"><?php esc_html_e( 'تایید و ادامه', 'uid-theme' ); ?></div>
	  </div>
	  <div class="scr" data-scr="4">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۴/۶</span></div>
	    <h4><?php esc_html_e( 'اطلاعات بانکی', 'uid-theme' ); ?></h4>
	    <p><?php esc_html_e( 'یک شماره کارت کافی است.', 'uid-theme' ); ?></p>
	    <div class="sfield"><span class="lb"><?php esc_html_e( 'شماره کارت', 'uid-theme' ); ?></span><span class="vl">6037 ···· ···· 4821</span></div>
	    <div class="sfield auto"><span class="lb"><?php esc_html_e( 'شبا — استعلام‌شده', 'uid-theme' ); ?></span><span class="vl">IR·· ···· ···· 8421</span></div>
	    <div class="sfield auto"><span class="lb"><?php esc_html_e( 'مالکیت حساب', 'uid-theme' ); ?></span><span class="vl fa"><?php esc_html_e( 'تاییدشده — سارا محمدی', 'uid-theme' ); ?></span></div>
	    <div class="sbtn"><?php esc_html_e( 'تایید و ادامه', 'uid-theme' ); ?></div>
	  </div>
	  <div class="scr" data-scr="5">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۵/۶</span></div>
	    <h4><?php esc_html_e( 'ویدئوی سلفی', 'uid-theme' ); ?></h4>
	    <p><?php esc_html_e( 'صورتتان را داخل کادر نگه دارید.', 'uid-theme' ); ?></p>
	    <div class="sface"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/></svg></div>
	    <span class="spill"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.5 13.5H11L10 22l8.5-11.5H12z"/></svg> <?php esc_html_e( 'تشخیص زنده‌بودن فعال', 'uid-theme' ); ?></span>
	    <div class="sbtn"><?php esc_html_e( 'ضبط ۵ ثانیه‌ای', 'uid-theme' ); ?></div>
	  </div>
	  <div class="scr" data-scr="6">
	    <div class="scr-top"><span class="lg"><?php esc_html_e( 'یو', 'uid-theme' ); ?></span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span class="st">۶/۶</span></div>
	    <div class="sdone"><div><div class="rg"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></div>
	      <h4 style="text-align:center"><?php esc_html_e( 'احراز هویت تکمیل شد', 'uid-theme' ); ?></h4>
	      <p style="text-align:center"><?php esc_html_e( 'در حال بازگشت به سایت پذیرنده…', 'uid-theme' ); ?></p>
	      <div class="stoken">clientToken: 8f2c··4e7a<br>status: 1 — SUCCESS</div></div></div>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * جزئیات سرویس (تب‌ها: ویژگی‌ها / مزایا / یکپارچه‌سازی فنی)
 * نمونه‌کدها و جدول پارامترها مستندات فنی دقیق‌اند و ثابت می‌مانند؛ فقط
 * عنوان/توضیح هر تب و کارت‌های ویژگی/مزیت از تنظیمات می‌آیند.
 * ===================================================================== */
function uid_default_pwa_bento() {
	return array(
		array( 'title' => 'شخصی‌سازی بصری', 'title_lat' => 'Customization', 'text' => 'امکان درج لوگو، بنر و انطباق بر هویت بصری پذیرنده — کاربر احساس نمی‌کند به یک سرویس غریبه منتقل شده است.' ),
		array( 'title' => 'اطمینان‌پذیری بالا', 'title_lat' => 'Multi-Vendor', 'text' => 'استفاده از چند تامین‌کننده برای هر سرویس، جهت پایداری محصول.' ),
		array( 'title' => 'مدیریت فرآیند', 'title_lat' => 'Dynamic Flow', 'text' => 'امکان مدیریت مراحل احراز بر اساس درخواست پذیرنده.' ),
		array( 'title' => 'راهبری کاربران و کاهش بار Operation پذیرنده', 'title_lat' => '', 'text' => 'هدایت کاربران تا تکمیل فرآیند احراز هویت توسط یوآیدی انجام می‌شود — تیم پشتیبانی شما درگیر پیگیری کاربران نیمه‌کاره نمی‌شود.' ),
	);
}
function uid_default_pwa_adv() {
	return array(
		array( 'title' => 'پایداری و امنیت سرویس', 'text' => 'تعهد به SLA فیمابین و رعایت استانداردهای امنیتی.' ),
		array( 'title' => 'استعلامات قضائی', 'text' => 'پاسخ‌گویی استعلامات قضائی (فتا) در کمتر از ۱ ساعت.' ),
		array( 'title' => 'برون‌سپاری واقعی', 'text' => 'آسودگی خاطر از مرکز تماس و راهبری مشتریان.' ),
		array( 'title' => 'دسترسی رایگان FDP', 'text' => 'امکان استفاده از سرویس کشف و جلوگیری از تقلب یوآیدی.' ),
		array( 'title' => 'پشتیبانی ۲۴/۷', 'text' => 'همیشه هستیم؛ هر زمان شما بخواهید.' ),
	);
}

function uid_pwa_render_integration_code() {
	// مستندات فنی دقیق API — عمداً ثابت (مثل کد نمونه‌های صفحه اصلی)
	?>
	<div class="codewrap rv rv-d1">
	  <div class="codetabs" role="tablist">
	    <button class="on" data-code="0" role="tab">۱. فراخوانی PWA</button>
	    <button data-code="1" role="tab">۲. بازگشت به سایت شما</button>
	    <button data-code="2" role="tab">۳. ورود پذیرنده</button>
	    <button data-code="3" role="tab">۴. دریافت اطلاعات کاربر</button>
	  </div>
	  <div class="codepane on" data-pane="0">
<pre><span class="c">// آدرس وب‌اپلیکیشن را با پارامترها بسازید و کاربر را به آن هدایت کنید</span>
https://cloud.uid.ir/crypto/?requestBusinessId=<span class="m">UID_BUSINESS_ID</span>
                            &amp;metaData=<span class="s">METADATA</span>
                            &amp;phoneNumber=<span class="s">PHONE_NUMBER</span></pre>
	    <div class="codenote"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg><span><b>requestBusinessId</b> <?php esc_html_e( 'الزامی است و توسط یوآیدی در اختیار شما قرار می‌گیرد.', 'uid-theme' ); ?> <b>phoneNumber</b> <?php esc_html_e( 'محدودکننده نیست و صرفاً تسهیل‌گر است — کاربر می‌تواند با ویرایش URL آن را تغییر دهد.', 'uid-theme' ); ?></span></div>
	  </div>
	  <div class="codepane" data-pane="1">
<pre><span class="c">// پس از خاتمه فرآیند، کاربر به آدرس اعلامی شما بازمی‌گردد</span>
<span class="m">BUSINESS_REDIRECT_URL</span>/?clientToken=<span class="s">CLIENT_TOKEN</span>
                       &amp;metaData=<span class="s">METADATA</span>
                       &amp;status=<span class="k">STATUS</span>

<span class="c">// نمونه status ها</span>
<span class="k">1</span>   SUCCESS                       احراز هویت با موفقیت به پایان رسید
<span class="k">2</span>   RESULT_CAMERA_NOT_SUPPORTED   دوربین پشتیبانی نمی‌شود
<span class="k">3</span>   RESULT_PERMISSIONS_NOT_GRANTED دسترسی دوربین داده نشد
<span class="k">7</span>   RESULT_ALREADY_APPROVED       کاربر قبلاً فرآیند را طی کرده
<span class="k">10</span>  RESULT_LIVENESS               ویدئوی سلفی ناموفق بود
<span class="k">11</span>  RESULT_FACE_MATCHING          عدم تطابق با ثبت احوال</pre>
	    <div class="codenote"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3L2 20h20L12 3z"/><path d="M12 9.5v4.5M12 17.5h.01"/></svg><span><?php esc_html_e( 'این وضعیت‌ها مربوط به «فرآیند احراز هویت» هستند، نه نتیجه آن. برای اطلاع از نتیجه، باید با یک فاصله زمانی سرویس getUserInfo فراخوانی شود.', 'uid-theme' ); ?></span></div>
	  </div>
	  <div class="codepane" data-pane="2">
<pre><span class="k">POST</span> https://api-crypto.uid.ir/business/login

Content-Type: application/json;charset=UTF-8
{
  <span class="s">"id"</span>: &lt;<span class="m">UID_BUSINESS_ID</span>&gt;,
  <span class="s">"secretKey"</span>: &lt;<span class="m">UID_SECRET_KEY</span>&gt;
}

<span class="c">// پاسخ</span>
{
  <span class="s">"message"</span>: { <span class="s">"oauthInformation"</span>: {
      <span class="s">"access_token"</span>: <span class="m">ACCESS_TOKEN</span>,
      <span class="s">"token_type"</span>: <span class="s">"bearer"</span>,
      <span class="s">"scope"</span>: <span class="s">"[read, write]"</span> } }
}</pre>
	    <div class="codenote"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/></svg><span><?php esc_html_e( 'شناسه کسب‌وکار و کلید محرمانه توسط یوآیدی در اختیار شما قرار می‌گیرد. تمام وب‌سرویس‌ها RESTful هستند.', 'uid-theme' ); ?></span></div>
	  </div>
	  <div class="codepane" data-pane="3">
<pre><span class="k">POST</span> https://api-crypto.uid.ir/getUserInfo

Authorization: bearer <span class="m">access_token</span>
Content-Type: application/json;charset=UTF-8
{ <span class="s">"clientToken"</span>: &lt;<span class="m">CLIENT_TOKEN</span>&gt; }

<span class="c">// پاسخ — اطلاعات کامل و تاییدشده کاربر</span>
{
  <span class="s">"status"</span>: <span class="k">SUCCESS</span>,
  <span class="s">"name"</span>, <span class="s">"family"</span>, <span class="s">"nationalCode"</span>,
  <span class="s">"mobileNumber"</span>, <span class="s">"birthDate"</span>, <span class="s">"metaData"</span>,
  <span class="s">"bankInfo"</span>:    { shabaNumber, bankName, cardNumber, accountNumber },
  <span class="s">"addressInfo"</span>: [ { state, city, userAaddress, postalCode } ]
}</pre>
	    <div class="codenote"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/></svg><span><?php esc_html_e( 'به دلیل محدودیت‌های سازمان ثبت احوال، دریافت اطلاعات کاربر پس از حداقل ۱۰ دقیقه امکان‌پذیر است. برای دریافت گروهی، سرویس', 'uid-theme' ); ?> <b>getUserInfo/bulk</b> <?php esc_html_e( 'با آرایه‌ای از clientTokenها در دسترس است.', 'uid-theme' ); ?></span></div>
	  </div>
	</div>
	<div class="tblwrap rv rv-d2"><table class="ptbl">
	  <thead><tr><th><?php esc_html_e( 'پارامتر فراخوانی', 'uid-theme' ); ?></th><th><?php esc_html_e( 'نوع', 'uid-theme' ); ?></th><th><?php esc_html_e( 'الزام', 'uid-theme' ); ?></th><th><?php esc_html_e( 'توضیح', 'uid-theme' ); ?></th></tr></thead>
	  <tbody>
	    <tr><td class="mono">requestBusinessId</td><td class="mono">string</td><td><span class="req">Required</span></td>
	      <td><?php esc_html_e( 'شناسه کسب‌وکار (BusinessID) که توسط یوآیدی در اختیار شما قرار می‌گیرد.', 'uid-theme' ); ?></td></tr>
	    <tr><td class="mono">metaData</td><td class="mono">string</td><td><span class="opt">Optional</span></td>
	      <td><?php esc_html_e( 'تگ یونیک کاربر؛ مثلاً user_id سیستم خودتان. یوآیدی آن را ذخیره و در پاسخ‌ها بازمی‌گرداند.', 'uid-theme' ); ?></td></tr>
	    <tr><td class="mono">phoneNumber</td><td class="mono">string</td><td><span class="opt">Optional</span></td>
	      <td><?php esc_html_e( 'تلفن همراه کاربر با فرمت ۰۹xxxxxxxxx؛ در صورت ارسال، فیلد شماره در گام اول به‌صورت Auto-fill و غیرقابل ویرایش تکمیل می‌شود.', 'uid-theme' ); ?></td></tr>
	  </tbody>
	</table></div>
	<?php
}

function uid_render_section_pwadetail() {
	$tag   = uid_section_tag( 'pwadetail', 'h2' );
	$bento = uid_section_val( 'pwadetail', 'bento', uid_default_pwa_bento() );
	$adv   = uid_section_val( 'pwadetail', 'adv', uid_default_pwa_adv() );
	?>
	<section class="sec" id="detail" style="background:var(--n50)">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwadetail', 'eyebrow', __( 'جزئیات سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwadetail', 'heading', __( 'هر آنچه پیش از جلسه فنی لازم است بدانید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pwadetail', 'text', __( 'امکانات محصول، آنچه فراتر از خودِ فرآیند تحویل می‌گیرید، و مسیر یکپارچه‌سازی — در یک نگاه.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="dtabs rv rv-d1">
	      <div class="dtab-bar" role="tablist" aria-label="<?php esc_attr_e( 'جزئیات سرویس', 'uid-theme' ); ?>">
	        <button class="dtab on" data-dtab="0" role="tab" aria-selected="true"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab1_label', __( 'ویژگی‌ها و امکانات', 'uid-theme' ) ) ); ?></button>
	        <button class="dtab" data-dtab="1" role="tab" aria-selected="false"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab2_label', __( 'مزایای سرویس یکپارچه', 'uid-theme' ) ) ); ?></button>
	        <button class="dtab" data-dtab="2" role="tab" aria-selected="false"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab3_label', __( 'یکپارچه‌سازی فنی', 'uid-theme' ) ) ); ?></button>
	      </div>

	      <div class="dtab-pane on" data-dpane="0">
	        <p class="dtab-lede"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab1_lede', __( 'سرویسی که با کسب‌وکار شما تطبیق پیدا می‌کند، نه برعکس.', 'uid-theme' ) ) ); ?></p>
	        <div class="bento rv rv-d1">
	          <?php foreach ( $bento as $i => $c ) : ?>
	          <article class="card"><span class="card-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo uid_pwa_bento_icon_svg( $i ); ?></svg></span>
	            <h3 class="h-card"><?php echo esc_html( $c['title'] ?? '' ); ?><?php if ( ! empty( $c['title_lat'] ) ) : ?> <span class="lat" style="font-size:12px;color:var(--n400)"><?php echo esc_html( $c['title_lat'] ); ?></span><?php endif; ?></h3>
	            <p><?php echo esc_html( $c['text'] ?? '' ); ?></p></article>
	          <?php endforeach; ?>
	        </div>
	        <div class="swipe-hint"><?php echo uid_pwa_arrow_icon(); ?> <?php esc_html_e( 'برای دیدن موارد بعدی، بکشید', 'uid-theme' ); ?></div>
	      </div>

	      <div class="dtab-pane" data-dpane="1">
	        <p class="dtab-lede"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab2_lede', __( 'آنچه فراتر از خودِ فرآیند احراز، از یوآیدی تحویل می‌گیرید.', 'uid-theme' ) ) ); ?></p>
	        <div class="adv rv rv-d1" data-rail="adv">
	          <?php foreach ( $adv as $i => $c ) : ?>
	          <article class="card"><span class="card-ic navy"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo uid_pwa_adv_icon_svg( $i ); ?></svg></span><h3 class="h-card"><?php echo esc_html( $c['title'] ?? '' ); ?></h3>
	            <p><?php echo esc_html( $c['text'] ?? '' ); ?></p></article>
	          <?php endforeach; ?>
	        </div>
	        <div class="swipe-hint"><?php echo uid_pwa_arrow_icon(); ?> <?php esc_html_e( 'برای دیدن موارد بعدی، بکشید', 'uid-theme' ); ?></div>
	      </div>

	      <div class="dtab-pane" data-dpane="2">
	        <p class="dtab-lede"><?php echo esc_html( uid_section_val( 'pwadetail', 'tab3_lede', __( 'تمام وب‌سرویس‌ها RESTful هستند. برای شروع فقط به یک شناسه کسب‌وکار و یک آدرس بازگشتی نیاز دارید.', 'uid-theme' ) ) ); ?></p>
	        <?php uid_pwa_render_integration_code(); ?>
	        <div style="margin-top:26px" class="rv rv-d3">
	          <a class="tlink" href="<?php echo esc_url( uid_section_val( 'pwadetail', 'doc_link_url', '/pwa-ekyc-docs/' ) ); ?>"><?php echo esc_html( uid_section_val( 'pwadetail', 'doc_link_text', __( 'مشاهده مستندات کامل PWA — نسخه ۱٫۱٫۱', 'uid-theme' ) ) ); ?> <?php echo uid_pwa_arrow_icon(); ?></a>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * هزینه سرویس
 * ===================================================================== */
function uid_default_pwa_pricing() {
	return array(
		array(
			'featured' => '1', 'title' => 'پیش‌پرداخت (بسته‌ای)', 'sub' => 'مناسب حجم‌های مشخص و قابل پیش‌بینی',
			'price_prefix' => 'از', 'price' => '۱۸٬۱۰۰', 'price_unit' => 'تومان به ازای هر احراز خاتمه‌یافته',
			'bullets' => "بر اساس حجم بسته، بین ۱۸٬۱۰۰ تا ۲۲٬۵۹۰ تومان متغیر است\nبا تضمین نرخ تبدیل\nفرآیند ناتمام = بدون هزینه\nدسترسی رایگان به سرویس FDP (کشف و پیشگیری از تقلب)",
			'btn_text' => 'دریافت پیشنهاد قیمت', 'btn_type' => 'modal',
		),
		array(
			'featured' => '', 'title' => 'پس‌پرداخت (On-Demand)', 'sub' => 'بدون تعهد حجمی، پرداخت بر اساس مصرف',
			'price_prefix' => '', 'price' => '۲۳٬۷۷۵', 'price_unit' => 'تومان به ازای هر احراز خاتمه‌یافته',
			'bullets' => "بدون نیاز به خرید بسته و پیش‌پرداخت\nبا تضمین نرخ تبدیل\nفرآیند ناتمام = بدون هزینه\nمناسب شروع سریع و تست واقعی روی ترافیک زنده",
			'btn_text' => 'گفت‌وگو با کارشناس', 'btn_type' => 'phone',
		),
	);
}

function uid_render_section_pwapricing() {
	$tag   = uid_section_tag( 'pwapricing', 'h2' );
	$cards = uid_section_val( 'pwapricing', 'cards', uid_default_pwa_pricing() );
	if ( ! is_array( $cards ) || empty( $cards ) ) return;
	?>
	<section class="sec" id="pricing" style="background:var(--n50)">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'pwapricing', 'eyebrow', __( 'هزینه سرویس', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwapricing', 'heading', __( 'فقط بابت احراز هویتِ خاتمه‌یافته پول می‌دهید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'pwapricing', 'text', __( 'فرآیندهایی که کاربر به هر دلیلی نیمه‌کاره رها می‌کند، هیچ هزینه‌ای برای شما ندارند. این یعنی ریسکِ ریزش از دوش شما برداشته می‌شود.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="price rv rv-d1" data-rail="price">
	      <?php foreach ( $cards as $c ) :
	      	$bullets = array_filter( array_map( 'trim', explode( "\n", $c['bullets'] ?? '' ) ) );
	      	?>
	      <div class="pcard<?php echo ! empty( $c['featured'] ) ? ' feat' : ''; ?>">
	        <div class="pcard-hd"><h3><?php echo esc_html( $c['title'] ?? '' ); ?></h3><span class="sub"><?php echo esc_html( $c['sub'] ?? '' ); ?></span></div>
	        <div class="pcard-amt"><?php if ( ! empty( $c['price_prefix'] ) ) : ?><span class="from"><?php echo esc_html( $c['price_prefix'] ); ?></span><?php endif; ?><b class="lat"><?php echo esc_html( $c['price'] ?? '' ); ?></b><span class="unit"><?php echo esc_html( $c['price_unit'] ?? '' ); ?></span></div>
	        <?php if ( $bullets ) : ?>
	        <ul>
	          <?php foreach ( $bullets as $b ) : ?>
	          	<li><?php echo uid_pwa_check_icon(); ?><span><?php echo esc_html( $b ); ?></span></li>
	          <?php endforeach; ?>
	        </ul>
	        <?php endif; ?>
	        <div class="pcard-ft">
	          <?php if ( 'phone' === ( $c['btn_type'] ?? 'modal' ) ) : ?>
	          	<a class="pwa-btn btn-ghost pwa-btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <?php echo esc_html( $c['btn_text'] ?? '' ); ?></a>
	          <?php else : ?>
	          	<button class="pwa-btn btn-cta pwa-btn-block js-demo-btn" data-modal-title="<?php echo esc_attr( $c['title'] ?? '' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی‌پلاس پیشنهاد قیمت اختصاصی ارسال کنند.', 'uid-theme' ); ?>"><?php echo uid_pwa_demo_icon(); ?> <?php echo esc_html( $c['btn_text'] ?? '' ); ?></button>
	          <?php endif; ?>
	        </div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="price"></div>
	    <div class="price-note rv rv-d2">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	      <span><b><?php echo esc_html( uid_section_val( 'pwapricing', 'note_title', __( 'تضمین نرخ تبدیل چه معنایی دارد؟', 'uid-theme' ) ) ); ?></b> <?php echo esc_html( uid_section_val( 'pwapricing', 'note_text', __( 'یوآیدی کاربرانی را که فرآیند را نیمه‌کاره رها کرده‌اند، از طریق کانال‌های خودش — پیامک، تماس تلفنی و کمپین‌های هدفمند — تا خاتمه فرآیند راهبری می‌کند. به مبالغ فوق مالیات بر ارزش افزوده اضافه می‌شود.', 'uid-theme' ) ) ); ?></span>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * پذیرندگان یوآیدی (برندها)
 * ===================================================================== */
function uid_default_pwa_brands() {
	return array(
		array( 'name' => 'دیوار', 'type' => 'پذیرنده یوآیدی', 'color' => '#E23E3E', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'رمزینکس', 'type' => 'پذیرنده یوآیدی', 'color' => '#1F6FEB', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'اکسکوینو', 'type' => 'پذیرنده یوآیدی', 'color' => '#0FA97E', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'تبدیل', 'type' => 'پذیرنده یوآیدی', 'color' => '#7A4DE0', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'اکسیر', 'type' => 'پذیرنده یوآیدی', 'color' => '#E0761A', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'آبان‌تتر', 'type' => 'پذیرنده یوآیدی', 'color' => '#0D9BB5', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'بانک گردشگری', 'type' => 'پذیرنده یوآیدی', 'color' => '#146B4E', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'باشگاه استقلال', 'type' => 'پذیرنده یوآیدی', 'color' => '#1D4ED8', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'سجام', 'type' => 'پذیرنده یوآیدی', 'color' => '#B03060', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'قوه قضائیه — ثنا', 'type' => 'پذیرنده یوآیدی', 'color' => '#2C5F8A', 'image_id' => 0, 'image_alt' => '' ),
	);
}

function uid_render_section_pwabrands() {
	$tag   = uid_section_tag( 'pwabrands', 'h2' );
	$items = uid_section_val( 'pwabrands', 'items', uid_default_pwa_brands() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="padding-block:clamp(44px,5.5vw,76px)">
	  <div class="pwa-wrap">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwabrands', 'eyebrow', __( 'پذیرندگان یوآیدی', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwabrands', 'heading', __( 'صرافی‌ها، بانک‌ها و پلتفرم‌هایی که هویت کاربرانشان را به ما سپرده‌اند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	  </div>
	  <div class="rv rv-d1"><div class="mq"><div class="mq-tr">
	    <?php for ( $pass = 0; $pass < 2; $pass++ ) :
	    	foreach ( $items as $b ) :
	    		$img_id = ! empty( $b['image_id'] ) ? absint( $b['image_id'] ) : 0;
	    		?>
	    		<div class="mq-item">
	    			<?php if ( $img_id ) :
	    				$alt = ! empty( $b['image_alt'] ) ? $b['image_alt'] : $b['name'];
	    				echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => $alt, 'class' => 'dot', 'style' => 'object-fit:cover;' ) );
	    			else :
	    				?>
	    				<span class="dot" style="background:<?php echo esc_attr( $b['color'] ?? '#15397C' ); ?>"><?php echo esc_html( mb_substr( $b['name'] ?? '', 0, 1 ) ); ?></span>
	    			<?php endif; ?>
	    			<span><b><?php echo esc_html( $b['name'] ?? '' ); ?></b><small><?php echo esc_html( $b['type'] ?? '' ); ?></small></span>
	    		</div>
	    	<?php endforeach;
	    endfor; ?>
	  </div></div></div>
	</section>
	<?php
}

/* =====================================================================
 * سوالات متداول
 * ===================================================================== */
function uid_default_pwa_faq() {
	return array(
		array( 'question' => 'راه‌اندازی یوآیدی‌پلاس چقدر طول می‌کشد و چه بار فنی روی تیم ما می‌گذارد؟', 'answer' => 'یوآیدی‌پلاس یک راهکار تحت وب است که با کمترین بار توسعه فنی و عملیاتی طراحی شده. شما فقط یک URL را با پارامترهای مشخص فراخوانی می‌کنید؛ کل فرآیند احراز درون راهکار یوآیدی طی می‌شود و در پایان، کاربر به آدرس بازگشتی شما هدایت و نتیجه اعلام می‌شود. عملاً یک لینک، نه یک پروژه توسعه.' ),
		array( 'question' => 'اگر کاربر فرآیند را نیمه‌کاره رها کند، هزینه‌ای پرداخت می‌کنیم؟', 'answer' => 'خیر. ملاک محاسبه هزینه، احراز هویت خاتمه‌یافته است و بابت فرآیندهایی که به هر دلیلی نیمه‌کاره رها شده‌اند هیچ هزینه‌ای دریافت نمی‌شود. علاوه بر این، یوآیدی کاربران رهاکرده را از طریق پیامک، تماس تلفنی و کانال‌های دیگر تا خاتمه فرآیند راهبری می‌کند.' ),
		array( 'question' => 'آیا ظاهر سرویس با برند ما هماهنگ می‌شود یا کاربر به یک سایت غریبه منتقل می‌شود؟', 'answer' => 'شخصی‌سازی بصری بخشی از سرویس است: امکان درج لوگو، بنر و انطباق بر هویت بصری پذیرنده وجود دارد. همچنین با قابلیت Dynamic Flow، مراحل احراز بر اساس درخواست شما قابل مدیریت است — یعنی می‌توانید فقط مراحلی را فعال کنید که برای کسب‌وکارتان الزامی است.' ),
		array( 'question' => 'اگر یکی از سرویس‌های استعلامی قطع شود، فرآیند ما متوقف می‌شود؟', 'answer' => 'خیر. معماری Multi-Vendor یعنی برای هر سرویس از چند تامین‌کننده استفاده می‌شود تا پایداری محصول حفظ شود. آپ‌تایم تعهدی سرویس‌های مرتبط با احراز یکپارچه ۹۹٫۵٪ است و به SLA فیمابین متعهد هستیم.' ),
		array( 'question' => 'پاسخ‌گویی به استعلامات قضایی و پلیس فتا بر عهده کیست؟', 'answer' => 'بر عهده یوآیدی. پاسخ‌گویی استعلامات قضائی (فتا) در کمتر از ۱ ساعت انجام می‌شود و ارسال استعلامات احراز هویت افراد به پلیس فتا یا مراجع قضائی در کمتر از ۳۰ دقیقه صورت می‌گیرد. علاوه بر این، دسترسی به سرویس کشف و پیشگیری از تقلب (FDP) یوآیدی رایگان در اختیار شماست.' ),
		array( 'question' => 'نتیجه احراز هویت را چطور و چه زمانی دریافت می‌کنیم؟', 'answer' => 'پس از پایان فرآیند، یک clientToken به آدرس بازگشتی شما ارسال می‌شود و با فراخوانی سرویس getUserInfo می‌توانید اطلاعات کامل کاربر شامل مشخصات هویتی، اطلاعات بانکی و آدرس را دریافت کنید. برای دریافت گروهی، سرویس getUserInfo/bulk در دسترس است.' ),
	);
}

function uid_render_section_pwafaq() {
	$tag   = uid_section_tag( 'pwafaq', 'h2' );
	$items = uid_section_val( 'pwafaq', 'items', uid_default_pwa_faq() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="pwa-wrap" style="max-width:900px">
	    <div class="sec-head mid rv">
	      <span class="pwa-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'pwafaq', 'eyebrow', __( 'پیش از تصمیم', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'pwafaq', 'heading', __( 'سوال‌هایی که معمولاً قبل از امضا پرسیده می‌شود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="faq rv rv-d1">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="faq-i">
	        <button class="faq-q" aria-expanded="false"><span><?php echo esc_html( $it['question'] ?? '' ); ?></span><span class="pm" aria-hidden="true"></span></button>
	        <div class="faq-a"><p><?php echo esc_html( $it['answer'] ?? '' ); ?></p></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس نهایی (فرم)
 * ===================================================================== */
function uid_render_section_pwalead() {
	$tag   = uid_section_tag( 'pwalead', 'h2' );
	$trust = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'pwalead', 'trust', "پاسخ در کمتر از یک روز کاری\nمشاوره رایگان یکپارچه‌سازی\nبدون تعهد و بدون هزینه اولیه" ) ) ) );
	$opts  = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'pwalead', 'intent_options', "راه‌اندازی یوآیدی‌پلاس برای کسب‌وکار من\nیکپارچه‌سازی API احراز هویت\nمشاوره و دریافت پیشنهاد قیمت\n---\nاحراز هویت شخصی من در سامانه ثنا" ) ) ) );
	?>
	<section class="sec" id="lead" style="padding-block:0 var(--sec)">
	  <div class="pwa-wrap" style="padding-inline:0">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="pwa-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'pwalead', 'eyebrow', __( 'آماده شروع همکاری هستید؟', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'pwalead', 'heading', __( 'بیایید احراز هویت کسب‌وکار شما را با هم راه‌اندازی کنیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'pwalead', 'text', __( 'فرم را تکمیل کنید تا کارشناسان یوآیدی در سریع‌ترین زمان با شما تماس بگیرند — یا مستقیماً با شماره', 'uid-theme' ) ) ); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span> <?php esc_html_e( 'تماس بگیرید.', 'uid-theme' ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            	<span><?php echo uid_pwa_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>

	        <form class="lb-form" id="bandForm" novalidate>
	          <h3><?php echo esc_html( uid_section_val( 'pwalead', 'form_title', __( 'درخواست تماس', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'pwalead', 'form_hint', __( 'فقط سه فیلد — کمتر از ۲۰ ثانیه وقت می‌گیرد.', 'uid-theme' ) ) ); ?></p>

	          <div class="route-alert" id="routeAlert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 11v5M12 8h.01"/></svg>
	            <span><?php echo esc_html( uid_section_val( 'pwalead', 'route_alert_text', __( 'اگر برای خودتان دنبال احراز هویت ثنا هستید، این فرم مخصوص کسب‌وکارهاست. سریع‌ترین مسیر شما صفحه سامانه ثنا است.', 'uid-theme' ) ) ); ?></span></div>

	          <div class="frow">
	            <div class="fld"><input id="b_name" name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'این فیلد الزامی است.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><input id="b_tel" name="phone" type="tel" inputmode="numeric" placeholder="<?php esc_attr_e( 'شماره تماس — ۰۹xxxxxxxxx', 'uid-theme' ); ?>" data-req data-tel>
	              <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	          </div>
	          <div class="fld">
	            <select id="b_type" name="intent" data-req data-route>
	              <option value=""><?php esc_html_e( 'این درخواست برای چیست؟', 'uid-theme' ); ?></option>
	              <?php foreach ( $opts as $o ) :
	              	if ( '---' === $o ) continue;
	              	$is_individual = ( end( $opts ) === $o );
	              	?>
	              	<option value="<?php echo $is_individual ? 'ind' : 'biz'; ?>"><?php echo esc_html( $o ); ?></option>
	              <?php endforeach; ?>
	            </select>
	            <span class="err"><?php esc_html_e( 'لطفاً یک گزینه انتخاب کنید.', 'uid-theme' ); ?></span>
	          </div>
	          <input type="hidden" name="source" value="uid-plus-pwa-conversion">

	          <button class="pwa-btn btn-cta pwa-btn-block" type="button" data-submit>
	            <?php echo uid_pwa_demo_icon(); ?> <?php echo esc_html( uid_section_val( 'pwalead', 'submit_text', __( 'درخواست تماس', 'uid-theme' ) ) ); ?></button>

	          <div class="lb-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10.5" width="16" height="11" rx="2.4"/><path d="M8 10.5V7a4 4 0 018 0v3.5"/></svg>
	            <span><?php echo esc_html( uid_section_val( 'pwalead', 'note_text', __( 'شماره شما فقط برای تماس کارشناس فروش استفاده می‌شود و در اختیار شخص ثالث قرار نمی‌گیرد.', 'uid-theme' ) ) ); ?></span></div>

	          <div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg><b><?php echo esc_html( uid_section_val( 'pwalead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	            <span><?php echo esc_html( uid_section_val( 'pwalead', 'success_text', __( 'تیم یوآیدی به‌زودی تماس می‌گیرد. برای پیگیری فوری:', 'uid-theme' ) ) ); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </form>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ساخت خودکار «برگه یوآیدی‌پلاس» + مدیریت اسلاگ آن از تنظیمات قالب
 * (دقیقاً همان الگوی inc/homepage-setup.php)
 * ===================================================================== */
function uid_get_pwa_page_id() {
	$page_id = (int) get_option( 'uid_pwa_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_PWA_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_pwa_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_pwa_page() {
	if ( uid_get_pwa_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ),
		'post_name'   => 'uid-plus',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_PWA_TEMPLATE );
	update_option( 'uid_pwa_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_pwa_page_once' );

function uid_ensure_pwa_page_once() {
	if ( get_option( 'uid_pwa_page_bootstrapped' ) ) return;
	uid_ensure_pwa_page();
	update_option( 'uid_pwa_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_pwa_page_once' );

function uid_register_pwa_slug_setting() {
	register_setting( 'uid_pwa_group', 'uid_pwa_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_pwa_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_pwa_page_slug_section', '', '__return_false', 'uid_pwa_layout' );
	add_settings_field( 'uid_pwa_page_slug', __( 'آدرس (اسلاگ) صفحه یوآیدی‌پلاس', 'uid-theme' ), 'uid_field_pwa_page_slug', 'uid_pwa_layout', 'uid_pwa_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_pwa_slug_setting' );

function uid_field_pwa_page_slug( $args ) {
	$page_id = uid_get_pwa_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_pwa_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_pwa_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه یوآیدی‌پلاس هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_pwa_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_pwa_page_id();

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
 * uid_register_settings در inc/admin-settings.php
 * ===================================================================== */
function uid_register_pwa_settings() {
	register_setting( 'uid_pwa_group', 'uid_pwa_layout', array(
		'sanitize_callback' => 'uid_sanitize_pwa_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_pwa_layout_main', '', '__return_false', 'uid_pwa_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_pwa_layout', 'uid_pwa_layout_main', array(
		'option_name' => 'uid_pwa_layout', 'registry_fn' => 'uid_pwa_sections_registry', 'layout_fn' => 'uid_get_pwa_layout',
	) );

	/* ---------------- هیرو + محاسبه‌گر ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwahero', array( 'sanitize_callback' => 'uid_sanitize_section_pwahero', 'default' => array() ) );
	add_settings_section( 'uid_section_pwahero_main', '', '__return_false', 'uid_section_pwahero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'eyebrow', 'default' => 'یوآیدی‌پلاس · احراز هویت یکپارچه (PWA)' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'heading', 'default' => 'از هر ۱۰۰ کاربری که احراز هویت را شروع می‌کنند، ۷۶ نفر تمام نمی‌کنند.' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'text', 'default' => 'این عدد فرض نیست — نرخ تبدیل واقعی یک صرافی ایرانی روی مسیر API است. همان قیف، با یوآیدی‌پلاس به ۷۳٫۱۹٪ رسید. محاسبه کنید همین حالا چقدر از دست می‌دهید.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال دمو)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'btn1_text', 'default' => 'درخواست دمو اختصاصی' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'btn2_text', 'default' => 'مشاهده جریان محصول' ) );
	add_settings_field( 'btn2_url', __( 'لینک دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'btn2_url', 'default' => '#flow' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'tags', 'default' => "پرداخت فقط بابت احراز خاتمه‌یافته\nبا تضمین نرخ تبدیل\nبدون نیاز به نصب اپلیکیشن" ) );
	add_settings_field( 'rate_api', __( 'نرخ تبدیل مسیر فعلی API (درصد)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'rate_api', 'default' => '23.59', 'desc' => __( 'در محاسبه‌گر هزینه ریزش استفاده می‌شود.', 'uid-theme' ) ) );
	add_settings_field( 'rate_pwa', __( 'نرخ تبدیل یوآیدی‌پلاس (درصد)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwahero', 'uid_section_pwahero_main', array( 'group' => 'uid_section_pwahero', 'key' => 'rate_pwa', 'default' => '73.19' ) );

	/* ---------------- مقایسه قیف تبدیل ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwaproof', array( 'sanitize_callback' => 'uid_sanitize_section_pwaproof', 'default' => array() ) );
	add_settings_section( 'uid_section_pwaproof_main', '', '__return_false', 'uid_section_pwaproof' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'eyebrow', 'default' => 'مدرک، نه ادعا' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'heading', 'default' => 'همان کاربران، همان بازه زمانی، دو مسیر متفاوت' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'text', 'default' => 'این ارقام از مقایسه مستقیم عملکرد یک صرافی ارز دیجیتال روی مسیر API با یک پذیرنده روی نسخه PWA استخراج شده است.' ) );
	add_settings_field( 'api_track_title', __( 'مسیر API — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'api_track_title', 'default' => 'مسیر فعلی — یکپارچه‌سازی API' ) );
	add_settings_field( 'api_track_note', __( 'مسیر API — یادداشت', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'api_track_note', 'default' => 'داده واقعی رمزینکس' ) );
	add_settings_field( 'api_total', __( 'مسیر API — درصد کل', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'api_total', 'default' => '23.59' ) );
	add_settings_field( 'api_steps', __( 'مسیر API — مراحل قیف', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array(
		'group' => 'uid_section_pwaproof', 'key' => 'api_steps', 'default' => uid_default_pwa_funnel_steps( 'api' ), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array( array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ), array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'pwa_track_title', __( 'مسیر یوآیدی‌پلاس — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'pwa_track_title', 'default' => 'مسیر یوآیدی‌پلاس — PWA' ) );
	add_settings_field( 'pwa_track_note', __( 'مسیر یوآیدی‌پلاس — یادداشت', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'pwa_track_note', 'default' => 'همان بازه زمانی' ) );
	add_settings_field( 'pwa_total', __( 'مسیر یوآیدی‌پلاس — درصد کل', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'pwa_total', 'default' => '73.19' ) );
	add_settings_field( 'pwa_steps', __( 'مسیر یوآیدی‌پلاس — مراحل قیف', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array(
		'group' => 'uid_section_pwaproof', 'key' => 'pwa_steps', 'default' => uid_default_pwa_funnel_steps( 'pwa' ), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array( array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ), array( 'key' => 'percent', 'type' => 'text', 'label' => __( 'درصد', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'multiplier_value', __( 'عدد ضریب برجسته (مثل 3.1×)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'multiplier_value', 'default' => '3.1×' ) );
	add_settings_field( 'multiplier_text', __( 'توضیح ضریب', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwaproof', 'uid_section_pwaproof_main', array( 'group' => 'uid_section_pwaproof', 'key' => 'multiplier_text', 'default' => 'نرخ تبدیل نسخه PWA نسبت به یکپارچه‌سازی مستقیم API — روی حجم واقعی یک صرافی ایرانی، در یک بازه یکسان.' ) );

	/* ---------------- نقاط ریزش ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwaleaks', array( 'sanitize_callback' => 'uid_sanitize_section_pwaleaks', 'default' => array() ) );
	add_settings_section( 'uid_section_pwaleaks_main', '', '__return_false', 'uid_section_pwaleaks' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwaleaks', 'uid_section_pwaleaks_main', array( 'group' => 'uid_section_pwaleaks', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaleaks', 'uid_section_pwaleaks_main', array( 'group' => 'uid_section_pwaleaks', 'key' => 'eyebrow', 'default' => 'چهار نقطه‌ای که کاربر شما را ترک می‌کند' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaleaks', 'uid_section_pwaleaks_main', array( 'group' => 'uid_section_pwaleaks', 'key' => 'heading', 'default' => 'ریزش، تصادفی نیست. چهار دلیل مشخص دارد.' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwaleaks', 'uid_section_pwaleaks_main', array( 'group' => 'uid_section_pwaleaks', 'key' => 'text', 'default' => 'هر مورد زیر یک علت تکرارشونده و مستند برای رهاکردن فرآیند احراز هویت است — نه یک فرضیه.' ) );
	add_settings_field( 'items', __( 'کارت‌های نقطه ریزش', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwaleaks', 'uid_section_pwaleaks_main', array(
		'group' => 'uid_section_pwaleaks', 'key' => 'items', 'default' => uid_default_pwa_leaks(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'desc'  => __( 'شماره هر مورد خودکار از روی ترتیب محاسبه می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان مشکل', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح مشکل', 'uid-theme' ) ),
			array( 'key' => 'fix_label', 'type' => 'text', 'label' => __( 'برچسب راه‌حل', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'توضیح راه‌حل', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مطالعه موردی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwacase', array( 'sanitize_callback' => 'uid_sanitize_section_pwacase', 'default' => array() ) );
	add_settings_section( 'uid_section_pwacase_main', '', '__return_false', 'uid_section_pwacase' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'eyebrow', 'default' => 'مطالعه موردی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'heading', 'default' => 'وقتی همین اعداد را روی حجم واقعی یک صرافی بگذاریم' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'text', 'default' => 'با اعمال نرخ تبدیل نسخه PWA روی حجم واقعی شاهکار رمزینکس، تعداد کاربرانی که تا انتهای فرآیند می‌رسند بیش از دو برابر می‌شود — و شکاف بین این دو عدد، دقیقاً همان بودجه جذب کاربری است که امروز هدر می‌رود.' ) );
	add_settings_field( 'big_label', __( 'برچسب عدد بزرگ', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'big_label', 'default' => 'هزینه فرصت از دست‌رفته، به ازای CAC معادل ۱٬۰۰۰٬۰۰۰ ریال' ) );
	add_settings_field( 'big_value', __( 'مقدار عدد بزرگ (فقط رقم)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'big_value', 'default' => '29472000000' ) );
	add_settings_field( 'big_suffix', __( 'واحد عدد بزرگ', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'big_suffix', 'default' => 'ریال' ) );
	add_settings_field( 'big_note', __( 'یادداشت زیر عدد بزرگ', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'big_note', 'default' => 'یعنی نزدیک به ۲٬۹۴۷ میلیون تومان بودجه جذب کاربر، صرف کاربرانی شده که از یک قیفِ قابل‌اصلاح بیرون افتاده‌اند.' ) );
	add_settings_field( 'rows', __( 'جدول مقایسه مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwacase', 'uid_section_pwacase_main', array(
		'group' => 'uid_section_pwacase', 'key' => 'rows', 'default' => uid_default_pwa_case_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'مرحله', 'uid-theme' ) ),
			array( 'key' => 'api_value', 'type' => 'text', 'label' => __( 'مقدار — مسیر API', 'uid-theme' ) ),
			array( 'key' => 'pwa_value', 'type' => 'text', 'label' => __( 'مقدار — یوآیدی‌پلاس', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'cost_note_prefix', __( 'یادداشت هزینه — پیش‌متن', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'cost_note_prefix', 'default' => 'هزینه احراز کامل در سناریوی PWA:' ) );
	add_settings_field( 'cost_value', __( 'یادداشت هزینه — مقدار', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'cost_value', 'default' => '8,067,347,000' ) );
	add_settings_field( 'cost_note_suffix', __( 'یادداشت هزینه — پس‌متن', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacase', 'uid_section_pwacase_main', array( 'group' => 'uid_section_pwacase', 'key' => 'cost_note_suffix', 'default' => 'ریال — در برابر هزینه فرصتی که در ستون بالا از دست می‌رود.' ) );

	/* ---------------- بنر تماس تلفنی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwacallband', array( 'sanitize_callback' => 'uid_sanitize_section_pwacallband', 'default' => array() ) );
	add_settings_section( 'uid_section_pwacallband_main', '', '__return_false', 'uid_section_pwacallband' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacallband', 'uid_section_pwacallband_main', array( 'group' => 'uid_section_pwacallband', 'key' => 'heading', 'default' => 'می‌خواهید همین حالا عدد دقیق کسب‌وکار خودتان را بدانید؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwacallband', 'uid_section_pwacallband_main', array( 'group' => 'uid_section_pwacallband', 'key' => 'text', 'default' => 'یک تماس کوتاه کافی است. کارشناس فنی یوآیدی نرخ تبدیل فعلی و حجم ماهانه شما را می‌گیرد و همان‌جا محاسبه می‌کند که یوآیدی‌پلاس چند کاربر و چقدر بودجه برایتان برمی‌گرداند.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه دوم (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwacallband', 'uid_section_pwacallband_main', array( 'group' => 'uid_section_pwacallband', 'key' => 'btn_text', 'default' => 'شما با من تماس بگیرید' ) );

	/* ---------------- تیم و اعتمادسازی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwateam', array( 'sanitize_callback' => 'uid_sanitize_section_pwateam', 'default' => array() ) );
	add_settings_section( 'uid_section_pwateam_main', '', '__return_false', 'uid_section_pwateam' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'eyebrow', 'default' => 'تیمی که پشت این عدد ایستاده' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'heading', 'default' => 'شما یک ابزار نمی‌خرید؛ یک تیم متخصص را کنار خودتان می‌آورید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'text', 'default' => 'مأموریت این سرویس، «برون‌سپاری احراز هویت» است — یعنی مسئولیت راهبری کاربر، پاسخ‌گویی به استعلامات قضایی، و بار عملیاتی مرکز تماس از دوش تیم شما برداشته می‌شود.' ) );
	add_settings_field( 'rows', __( 'ردیف‌های اعتمادسازی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwateam', 'uid_section_pwateam_main', array(
		'group' => 'uid_section_pwateam', 'key' => 'rows', 'default' => uid_default_pwa_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'shield' => __( 'اعتماد', 'uid-theme' ), 'people' => __( 'کاربران', 'uid-theme' ), 'gavel' => __( 'قضائی', 'uid-theme' ), 'lock' => __( 'امنیت', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'quote_text', __( 'متن نقل‌قول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'quote_text', 'default' => '«چه می‌شد اگر می‌توانستیم هویتمان را در اینترنت ضمانت کنیم؟ یک انقلاب بزرگ رخ می‌داد؛ دیگر لازم نبود برای ثبت‌نام کاری جز گرفتن یک ویدئوی سلفی انجام دهیم.»' ) );
	add_settings_field( 'quote_by_name', __( 'گوینده نقل‌قول', 'uid-theme' ), 'uid_field_text', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'quote_by_name', 'default' => 'تیم یوآیدی' ) );
	add_settings_field( 'quote_by_role', __( 'سمت گوینده', 'uid-theme' ), 'uid_field_text', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'quote_by_role', 'default' => 'هویت امن دیجیتال' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwateam', 'uid_section_pwateam_main', array( 'group' => 'uid_section_pwateam', 'key' => 'btn_text', 'default' => 'صحبت با کارشناس فنی' ) );

	/* ---------------- آمار عملیاتی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwastats', array( 'sanitize_callback' => 'uid_sanitize_section_pwastats', 'default' => array() ) );
	add_settings_section( 'uid_section_pwastats_main', '', '__return_false', 'uid_section_pwastats' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'eyebrow', 'default' => 'از نگاه آمار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'heading', 'default' => 'زیرساختی که سال‌هاست زیر بار واقعی کار می‌کند' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'text', 'default' => 'این اعداد سقف تبلیغاتی نیستند؛ تعهد سرویس و کارنامه عملیاتی یوآیدی‌پلاس هستند.' ) );
	add_settings_field( 'items', __( 'کارت‌های آماری', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwastats', 'uid_section_pwastats_main', array(
		'group' => 'uid_section_pwastats', 'key' => 'items', 'default' => uid_default_pwa_stats(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'desc'  => __( 'برای آمارهایی که به‌صورت درصد هستند، «میزان نوار پیشرفت» را هم پر کنید تا نوار زیرشان نمایش داده شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'people' => __( 'کاربران', 'uid-theme' ), 'peak' => __( 'اوج', 'uid-theme' ), 'shield' => __( 'اعتماد', 'uid-theme' ), 'face' => __( 'تشخیص چهره', 'uid-theme' ), 'gavel' => __( 'قضائی', 'uid-theme' ), 'clock' => __( 'زمان', 'uid-theme' ) ) ),
			array( 'key' => 'featured', 'type' => 'select', 'label' => __( 'بزرگ‌نمایی شود؟', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عدد/مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
			array( 'key' => 'meter', 'type' => 'text', 'label' => __( 'میزان نوار پیشرفت (فقط برای درصدها، مثل 99.5)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_text', __( 'متن پایین', 'uid-theme' ), 'uid_field_text', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'foot_text', 'default' => 'می‌خواهید SLA و جزئیات فنی را ببینید؟' ) );
	add_settings_field( 'foot_link_text', __( 'متن لینک مستندات', 'uid-theme' ), 'uid_field_text', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'foot_link_text', 'default' => 'مستندات فنی' ) );
	add_settings_field( 'foot_link_url', __( 'لینک مستندات', 'uid-theme' ), 'uid_field_text', 'uid_section_pwastats', 'uid_section_pwastats_main', array( 'group' => 'uid_section_pwastats', 'key' => 'foot_link_url', 'default' => '/pwa-ekyc-docs/' ) );

	/* ---------------- مسیر کاربر (استپر) ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwaflow', array( 'sanitize_callback' => 'uid_sanitize_section_pwaflow', 'default' => array() ) );
	add_settings_section( 'uid_section_pwaflow_main', '', '__return_false', 'uid_section_pwaflow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'eyebrow', 'default' => 'مسیر کاربر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'heading', 'default' => 'هفت گام، یک نشست، بدون خروج از فضای شما' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'text', 'default' => 'روی هر گام بزنید تا ببینید کاربر چه چیزی وارد می‌کند و اطلاعات از کدام مرجع رسمی استعلام می‌شود.' ) );
	add_settings_field( 'steps', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array(
		'group' => 'uid_section_pwaflow', 'key' => 'steps', 'default' => uid_default_pwa_flow_steps(), 'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'desc'  => __( 'نمای گوشی سمت راست، ثابت و تزئینی است و همیشه با شماره مرحله همگام می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'cap', 'type' => 'text', 'label' => __( 'برچسب کوتاه (نوار بالا)', 'uid-theme' ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان مرحله', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'chips', 'type' => 'textarea', 'label' => __( 'برچسب‌های کوچک (هر خط یک مورد)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'foot_link_text', __( 'متن لینک پایین', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'foot_link_text', 'default' => 'مستندات فنی فراخوانی PWA' ) );
	add_settings_field( 'foot_link_url', __( 'لینک پایین', 'uid-theme' ), 'uid_field_text', 'uid_section_pwaflow', 'uid_section_pwaflow_main', array( 'group' => 'uid_section_pwaflow', 'key' => 'foot_link_url', 'default' => '/pwa-ekyc-docs/' ) );

	/* ---------------- جزئیات سرویس (تب‌ها) ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwadetail', array( 'sanitize_callback' => 'uid_sanitize_section_pwadetail', 'default' => array() ) );
	add_settings_section( 'uid_section_pwadetail_main', '', '__return_false', 'uid_section_pwadetail' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'eyebrow', 'default' => 'جزئیات سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'heading', 'default' => 'هر آنچه پیش از جلسه فنی لازم است بدانید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'text', 'default' => 'امکانات محصول، آنچه فراتر از خودِ فرآیند تحویل می‌گیرید، و مسیر یکپارچه‌سازی — در یک نگاه.' ) );
	add_settings_field( 'tab1_label', __( 'تب ۱ — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab1_label', 'default' => 'ویژگی‌ها و امکانات' ) );
	add_settings_field( 'tab1_lede', __( 'تب ۱ — توضیح کوتاه', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab1_lede', 'default' => 'سرویسی که با کسب‌وکار شما تطبیق پیدا می‌کند، نه برعکس.' ) );
	add_settings_field( 'bento', __( 'تب ۱ — کارت‌های ویژگی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array(
		'group' => 'uid_section_pwadetail', 'key' => 'bento', 'default' => uid_default_pwa_bento(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'title_lat', 'type' => 'text', 'label' => __( 'عنوان لاتین (اختیاری)', 'uid-theme' ) ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'tab2_label', __( 'تب ۲ — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab2_label', 'default' => 'مزایای سرویس یکپارچه' ) );
	add_settings_field( 'tab2_lede', __( 'تب ۲ — توضیح کوتاه', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab2_lede', 'default' => 'آنچه فراتر از خودِ فرآیند احراز، از یوآیدی تحویل می‌گیرید.' ) );
	add_settings_field( 'adv', __( 'تب ۲ — کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array(
		'group' => 'uid_section_pwadetail', 'key' => 'adv', 'default' => uid_default_pwa_adv(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'tab3_label', __( 'تب ۳ — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab3_label', 'default' => 'یکپارچه‌سازی فنی' ) );
	add_settings_field( 'tab3_lede', __( 'تب ۳ — توضیح کوتاه', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'tab3_lede', 'default' => 'تمام وب‌سرویس‌ها RESTful هستند. برای شروع فقط به یک شناسه کسب‌وکار و یک آدرس بازگشتی نیاز دارید.' ) );
	add_settings_field( 'tab3_notice', '', 'uid_field_notice', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'text' => __( 'نمونه‌کدها و جدول پارامترهای API، مستندات فنی دقیق‌اند و از این صفحه قابل‌ویرایش نیستند (برای تغییر، به inc/pwa-page.php مراجعه کنید).', 'uid-theme' ) ) );
	add_settings_field( 'doc_link_text', __( 'متن لینک مستندات کامل', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'doc_link_text', 'default' => 'مشاهده مستندات کامل PWA — نسخه ۱٫۱٫۱' ) );
	add_settings_field( 'doc_link_url', __( 'لینک مستندات کامل', 'uid-theme' ), 'uid_field_text', 'uid_section_pwadetail', 'uid_section_pwadetail_main', array( 'group' => 'uid_section_pwadetail', 'key' => 'doc_link_url', 'default' => '/pwa-ekyc-docs/' ) );

	/* ---------------- هزینه سرویس ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwapricing', array( 'sanitize_callback' => 'uid_sanitize_section_pwapricing', 'default' => array() ) );
	add_settings_section( 'uid_section_pwapricing_main', '', '__return_false', 'uid_section_pwapricing' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'eyebrow', 'default' => 'هزینه سرویس' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'heading', 'default' => 'فقط بابت احراز هویتِ خاتمه‌یافته پول می‌دهید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'text', 'default' => 'فرآیندهایی که کاربر به هر دلیلی نیمه‌کاره رها می‌کند، هیچ هزینه‌ای برای شما ندارند. این یعنی ریسکِ ریزش از دوش شما برداشته می‌شود.' ) );
	add_settings_field( 'cards', __( 'کارت‌های قیمت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array(
		'group' => 'uid_section_pwapricing', 'key' => 'cards', 'default' => uid_default_pwa_pricing(), 'add_label' => __( 'افزودن کارت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'featured', 'type' => 'select', 'label' => __( 'برجسته؟', 'uid-theme' ), 'options' => array( '' => __( 'خیر', 'uid-theme' ), '1' => __( 'بله', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'sub', 'type' => 'text', 'label' => __( 'زیرعنوان', 'uid-theme' ) ),
			array( 'key' => 'price_prefix', 'type' => 'text', 'label' => __( 'پیشوند قیمت (مثل «از»، اختیاری)', 'uid-theme' ) ),
			array( 'key' => 'price', 'type' => 'text', 'label' => __( 'قیمت', 'uid-theme' ) ),
			array( 'key' => 'price_unit', 'type' => 'text', 'label' => __( 'واحد قیمت', 'uid-theme' ) ),
			array( 'key' => 'bullets', 'type' => 'textarea', 'label' => __( 'موارد لیست (هر خط یک مورد)', 'uid-theme' ) ),
			array( 'key' => 'btn_text', 'type' => 'text', 'label' => __( 'متن دکمه', 'uid-theme' ) ),
			array( 'key' => 'btn_type', 'type' => 'select', 'label' => __( 'نوع دکمه', 'uid-theme' ), 'options' => array( 'modal' => __( 'باز کردن مودال دمو', 'uid-theme' ), 'phone' => __( 'تماس تلفنی مستقیم', 'uid-theme' ) ) ),
		),
	) );
	add_settings_field( 'note_title', __( 'یادداشت پایین — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'note_title', 'default' => 'تضمین نرخ تبدیل چه معنایی دارد؟' ) );
	add_settings_field( 'note_text', __( 'یادداشت پایین — متن', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwapricing', 'uid_section_pwapricing_main', array( 'group' => 'uid_section_pwapricing', 'key' => 'note_text', 'default' => 'یوآیدی کاربرانی را که فرآیند را نیمه‌کاره رها کرده‌اند، از طریق کانال‌های خودش — پیامک، تماس تلفنی و کمپین‌های هدفمند — تا خاتمه فرآیند راهبری می‌کند. به مبالغ فوق مالیات بر ارزش افزوده اضافه می‌شود.' ) );

	/* ---------------- پذیرندگان یوآیدی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwabrands', array( 'sanitize_callback' => 'uid_sanitize_section_pwabrands', 'default' => array() ) );
	add_settings_section( 'uid_section_pwabrands_main', '', '__return_false', 'uid_section_pwabrands' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwabrands', 'uid_section_pwabrands_main', array( 'group' => 'uid_section_pwabrands', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwabrands', 'uid_section_pwabrands_main', array( 'group' => 'uid_section_pwabrands', 'key' => 'eyebrow', 'default' => 'پذیرندگان یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwabrands', 'uid_section_pwabrands_main', array( 'group' => 'uid_section_pwabrands', 'key' => 'heading', 'default' => 'صرافی‌ها، بانک‌ها و پلتفرم‌هایی که هویت کاربرانشان را به ما سپرده‌اند' ) );
	add_settings_field( 'items', __( 'فهرست برندها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwabrands', 'uid_section_pwabrands_main', array(
		'group' => 'uid_section_pwabrands', 'key' => 'items', 'default' => uid_default_pwa_brands(), 'add_label' => __( 'افزودن برند', 'uid-theme' ),
		'desc'  => __( 'برای هر برند می‌توانید لوگو آپلود کنید؛ در نبود لوگو، آواتار حرف اول با رنگ انتخابی نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'لوگو', 'uid-theme' ), 'pick_label' => __( 'انتخاب لوگو', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام برند', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'type', 'type' => 'text', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
			array( 'key' => 'color', 'type' => 'text', 'label' => __( 'رنگ آواتار (کد Hex، مثل ‎#E23E3E)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwafaq', array( 'sanitize_callback' => 'uid_sanitize_section_pwafaq', 'default' => array() ) );
	add_settings_section( 'uid_section_pwafaq_main', '', '__return_false', 'uid_section_pwafaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwafaq', 'uid_section_pwafaq_main', array( 'group' => 'uid_section_pwafaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwafaq', 'uid_section_pwafaq_main', array( 'group' => 'uid_section_pwafaq', 'key' => 'eyebrow', 'default' => 'پیش از تصمیم' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwafaq', 'uid_section_pwafaq_main', array( 'group' => 'uid_section_pwafaq', 'key' => 'heading', 'default' => 'سوال‌هایی که معمولاً قبل از امضا پرسیده می‌شود' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwafaq', 'uid_section_pwafaq_main', array(
		'group' => 'uid_section_pwafaq', 'key' => 'items', 'default' => uid_default_pwa_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_pwa_group', 'uid_section_pwalead', array( 'sanitize_callback' => 'uid_sanitize_section_pwalead', 'default' => array() ) );
	add_settings_section( 'uid_section_pwalead_main', '', '__return_false', 'uid_section_pwalead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'eyebrow', 'default' => 'آماده شروع همکاری هستید؟' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'heading', 'default' => 'بیایید احراز هویت کسب‌وکار شما را با هم راه‌اندازی کنیم' ) );
	add_settings_field( 'text', __( 'توضیح (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'text', 'default' => 'فرم را تکمیل کنید تا کارشناسان یوآیدی در سریع‌ترین زمان با شما تماس بگیرند — یا مستقیماً با شماره' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'trust', 'default' => "پاسخ در کمتر از یک روز کاری\nمشاوره رایگان یکپارچه‌سازی\nبدون تعهد و بدون هزینه اولیه" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'form_title', 'default' => 'درخواست تماس' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'form_hint', 'default' => 'فقط سه فیلد — کمتر از ۲۰ ثانیه وقت می‌گیرد.' ) );
	add_settings_field( 'route_alert_text', __( 'پیام هدایت کاربران شخصی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'route_alert_text', 'default' => 'اگر برای خودتان دنبال احراز هویت ثنا هستید، این فرم مخصوص کسب‌وکارهاست. سریع‌ترین مسیر شما صفحه سامانه ثنا است.' ) );
	add_settings_field( 'intent_options', __( 'گزینه‌های نوع درخواست (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwalead', 'uid_section_pwalead_main', array(
		'group' => 'uid_section_pwalead', 'key' => 'intent_options', 'default' => "راه‌اندازی یوآیدی‌پلاس برای کسب‌وکار من\nیکپارچه‌سازی API احراز هویت\nمشاوره و دریافت پیشنهاد قیمت\n---\nاحراز هویت شخصی من در سامانه ثنا",
		'desc' => __( 'آخرین خط همیشه گزینه‌ی «کاربر شخصی» است و با انتخابش پیام هدایت به سامانه ثنا نمایش داده می‌شود؛ خط «---» فقط یک جداکننده بصری است.', 'uid-theme' ),
	) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'submit_text', 'default' => 'درخواست تماس' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'note_text', 'default' => 'شماره شما فقط برای تماس کارشناس فروش استفاده می‌شود و در اختیار شخص ثالث قرار نمی‌گیرد.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_pwalead', 'uid_section_pwalead_main', array( 'group' => 'uid_section_pwalead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی تماس می‌گیرد. برای پیگیری فوری:' ) );
}
add_action( 'admin_init', 'uid_register_pwa_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_pwahero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'btn2_url'  => sanitize_text_field( $input['btn2_url'] ?? '' ),
		'tags'      => sanitize_textarea_field( $input['tags'] ?? '' ),
		'rate_api'  => sanitize_text_field( $input['rate_api'] ?? '' ),
		'rate_pwa'  => sanitize_text_field( $input['rate_pwa'] ?? '' ),
	);
}

function uid_sanitize_section_pwaproof( $input ) {
	$step_schema = array( array( 'key' => 'label', 'type' => 'text' ), array( 'key' => 'percent', 'type' => 'text' ) );
	return array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => sanitize_text_field( $input['heading'] ?? '' ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'api_track_title' => sanitize_text_field( $input['api_track_title'] ?? '' ),
		'api_track_note'  => sanitize_text_field( $input['api_track_note'] ?? '' ),
		'api_total'       => sanitize_text_field( $input['api_total'] ?? '' ),
		'api_steps'       => uid_sanitize_repeater_rows( $input['api_steps'] ?? '[]', $step_schema ),
		'pwa_track_title' => sanitize_text_field( $input['pwa_track_title'] ?? '' ),
		'pwa_track_note'  => sanitize_text_field( $input['pwa_track_note'] ?? '' ),
		'pwa_total'       => sanitize_text_field( $input['pwa_total'] ?? '' ),
		'pwa_steps'       => uid_sanitize_repeater_rows( $input['pwa_steps'] ?? '[]', $step_schema ),
		'multiplier_value'=> sanitize_text_field( $input['multiplier_value'] ?? '' ),
		'multiplier_text' => sanitize_textarea_field( $input['multiplier_text'] ?? '' ),
	);
}

function uid_sanitize_section_pwaleaks( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea' ),
			array( 'key' => 'fix_label', 'type' => 'text' ),
			array( 'key' => 'fix_text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_pwacase( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'text'             => sanitize_textarea_field( $input['text'] ?? '' ),
		'big_label'        => sanitize_text_field( $input['big_label'] ?? '' ),
		'big_value'        => sanitize_text_field( $input['big_value'] ?? '' ),
		'big_suffix'       => sanitize_text_field( $input['big_suffix'] ?? '' ),
		'big_note'         => sanitize_textarea_field( $input['big_note'] ?? '' ),
		'rows'             => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'label', 'type' => 'text' ), array( 'key' => 'api_value', 'type' => 'text' ), array( 'key' => 'pwa_value', 'type' => 'text' ),
		) ),
		'cost_note_prefix' => sanitize_text_field( $input['cost_note_prefix'] ?? '' ),
		'cost_value'       => sanitize_text_field( $input['cost_value'] ?? '' ),
		'cost_note_suffix' => sanitize_text_field( $input['cost_note_suffix'] ?? '' ),
	);
}

function uid_sanitize_section_pwacallband( $input ) {
	return array(
		'heading'  => sanitize_text_field( $input['heading'] ?? '' ),
		'text'     => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text' => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_pwateam( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'rows'          => uid_sanitize_repeater_rows( $input['rows'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ), array( 'key' => 'title', 'type' => 'text', 'required' => true ), array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'quote_text'    => sanitize_textarea_field( $input['quote_text'] ?? '' ),
		'quote_by_name' => sanitize_text_field( $input['quote_by_name'] ?? '' ),
		'quote_by_role' => sanitize_text_field( $input['quote_by_role'] ?? '' ),
		'btn_text'      => sanitize_text_field( $input['btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_pwastats( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'text'           => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'          => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'icon', 'type' => 'text' ), array( 'key' => 'featured', 'type' => 'text' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ), array( 'key' => 'label', 'type' => 'text' ), array( 'key' => 'meter', 'type' => 'text' ),
		) ),
		'foot_text'      => sanitize_text_field( $input['foot_text'] ?? '' ),
		'foot_link_text' => sanitize_text_field( $input['foot_link_text'] ?? '' ),
		'foot_link_url'  => sanitize_text_field( $input['foot_link_url'] ?? '' ),
	);
}

function uid_sanitize_section_pwaflow( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'text'           => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'          => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'cap', 'type' => 'text' ), array( 'key' => 'title', 'type' => 'text', 'required' => true ), array( 'key' => 'text', 'type' => 'textarea' ), array( 'key' => 'chips', 'type' => 'textarea' ),
		) ),
		'foot_link_text' => sanitize_text_field( $input['foot_link_text'] ?? '' ),
		'foot_link_url'  => sanitize_text_field( $input['foot_link_url'] ?? '' ),
	);
}

function uid_sanitize_section_pwadetail( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'tab1_label'    => sanitize_text_field( $input['tab1_label'] ?? '' ),
		'tab1_lede'     => sanitize_text_field( $input['tab1_lede'] ?? '' ),
		'bento'         => uid_sanitize_repeater_rows( $input['bento'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ), array( 'key' => 'title_lat', 'type' => 'text' ), array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'tab2_label'    => sanitize_text_field( $input['tab2_label'] ?? '' ),
		'tab2_lede'     => sanitize_text_field( $input['tab2_lede'] ?? '' ),
		'adv'           => uid_sanitize_repeater_rows( $input['adv'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ), array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'tab3_label'    => sanitize_text_field( $input['tab3_label'] ?? '' ),
		'tab3_lede'     => sanitize_text_field( $input['tab3_lede'] ?? '' ),
		'doc_link_text' => sanitize_text_field( $input['doc_link_text'] ?? '' ),
		'doc_link_url'  => sanitize_text_field( $input['doc_link_url'] ?? '' ),
	);
}

function uid_sanitize_section_pwapricing( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'cards'     => uid_sanitize_repeater_rows( $input['cards'] ?? '[]', array(
			array( 'key' => 'featured', 'type' => 'text' ), array( 'key' => 'title', 'type' => 'text', 'required' => true ), array( 'key' => 'sub', 'type' => 'text' ),
			array( 'key' => 'price_prefix', 'type' => 'text' ), array( 'key' => 'price', 'type' => 'text' ), array( 'key' => 'price_unit', 'type' => 'text' ),
			array( 'key' => 'bullets', 'type' => 'textarea' ), array( 'key' => 'btn_text', 'type' => 'text' ), array( 'key' => 'btn_type', 'type' => 'text' ),
		) ),
		'note_title'=> sanitize_text_field( $input['note_title'] ?? '' ),
		'note_text' => sanitize_textarea_field( $input['note_text'] ?? '' ),
	);
}

function uid_sanitize_section_pwabrands( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ), array( 'key' => 'image_alt', 'type' => 'text' ),
			array( 'key' => 'name', 'type' => 'text', 'required' => true ), array( 'key' => 'type', 'type' => 'text' ), array( 'key' => 'color', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_pwafaq( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_pwalead( $input ) {
	$text_fields = array( 'eyebrow', 'heading', 'form_title', 'form_hint', 'submit_text', 'note_text', 'success_title', 'success_text' );
	$out = array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'text'             => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'            => sanitize_textarea_field( $input['trust'] ?? '' ),
		'route_alert_text' => sanitize_textarea_field( $input['route_alert_text'] ?? '' ),
		'intent_options'   => sanitize_textarea_field( $input['intent_options'] ?? '' ),
	);
	foreach ( $text_fields as $f ) {
		$out[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
	}
	return $out;
}
