<?php
/**
 * صفحه اختصاصی «احراز هویت ثنا» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی واقعی
 * خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «sn» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options
 * با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_SANA_TEMPLATE', 'template-sana.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_sn_sections_registry() {
	return array(
		'snhero'     => array( 'label' => __( 'هیرو + چک‌لیست آمادگی', 'uid-theme' ),      'icon' => 'dashicons-star-filled' ),
		'snurgency'  => array( 'label' => __( 'بند فوریت (هشدار)', 'uid-theme' ),           'icon' => 'dashicons-warning' ),
		'snadv'      => array( 'label' => __( 'مزایای غیرحضوری بودن', 'uid-theme' ),        'icon' => 'dashicons-yes-alt' ),
		'snflow'     => array( 'label' => __( 'کاربرد سامانه ثنا (۳ گام)', 'uid-theme' ),   'icon' => 'dashicons-controls-forward' ),
		'snforeign'  => array( 'label' => __( 'بند ایرانیان خارج از کشور', 'uid-theme' ),   'icon' => 'dashicons-admin-site-alt3' ),
		'snsvc'      => array( 'label' => __( 'خدمات پس از کد ثنا (کاشی‌ها)', 'uid-theme' ),'icon' => 'dashicons-grid-view' ),
		'snrisk'     => array( 'label' => __( 'خطاهای رایج و راه‌حل', 'uid-theme' ),        'icon' => 'dashicons-shield' ),
		'sntrust'    => array( 'label' => __( 'باند اعتماد (آمار شرکت)', 'uid-theme' ),     'icon' => 'dashicons-chart-bar' ),
		'snteam'     => array( 'label' => __( 'راه‌های پشتیبانی + تعهدنامه', 'uid-theme' ), 'icon' => 'dashicons-businessperson' ),
		'snsecurity' => array( 'label' => __( 'هشدار پیامک جعلی', 'uid-theme' ),            'icon' => 'dashicons-lock' ),
		'snfaq'      => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),               'icon' => 'dashicons-editor-help' ),
		'snlead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),        'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_sn_layout() {
	$registry = uid_sn_sections_registry();
	$saved    = get_option( 'uid_sn_layout', array() );

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

function uid_sanitize_sn_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_sn_sections_registry();
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

function uid_render_sn_sections() {
	foreach ( uid_get_sn_layout() as $row ) {
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
function uid_sn_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_sn_warn_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
}
function uid_sn_phone_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>';
}
function uid_sn_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_sn_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v5M12 16h.01"/></svg>';
}
function uid_sn_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" transform="scale(-1,1) translate(-24,0)"/></svg>';
}
function uid_sn_adv_icon_svg( $key ) {
	$icons = array(
		'home'    => '<path d="M3 10.5L12 3l9 7.5V20a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 20z"/>',
		'reduce'  => '<path d="M9 20l-5.5-5.5M3.5 14.5L9 9M15 4l5.5 5.5M20.5 9.5L15 15"/>',
		'flex'    => '<path d="M4 16l4-8 4 4 4-9 4 13"/><path d="M4 20h16"/>',
		'clock'   => '<circle cx="12" cy="12" r="9.5"/><path d="M12 7v5l3.5 2"/>',
		'money'   => '<path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
		'hours'   => '<circle cx="12" cy="12" r="9.5"/><path d="M12 6v6l4 2"/>',
		'phone'   => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
		'globe'   => '<path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/>',
	);
	return $icons[ $key ] ?? $icons['home'];
}

/* =====================================================================
 * ۱) هیرو + چک‌لیست آمادگی + وضعیت زنده پشتیبانی (بخش تعاملی هاردکد،
 *    مطابق سابقه صفحات قبلی)
 * ===================================================================== */
function uid_default_sn_ready_items() {
	return array(
		array( 'title' => 'کد ملی و شماره شناسنامه', 'text' => 'برای تکمیل اطلاعات هویتی لازم است' ),
		array( 'title' => 'دوربین سالم و نور کافی محیط', 'text' => 'رایج‌ترین دلیل خطای ثبت‌نام، دوربین و نور کم است' ),
		array( 'title' => 'فیلترشکن خاموش باشد', 'text' => 'سامانه ثنا با فیلترشکن روشن باز نمی‌شود' ),
		array( 'title' => 'مرورگر مناسب', 'text' => 'اندروید: Chrome — iOS: Safari' ),
	);
}
function uid_render_section_snhero() {
	$tag   = uid_section_tag( 'snhero', 'h1' );
	$tags  = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'snhero', 'tags', "صدور کد ثنا در کمتر از ۳ دقیقه\nهمراه با تیم متخصص، نه تنها یک راهنما\nکارگزار مورد تایید سامانه ثنا" ) ) ) );
	$ready = uid_section_val( 'snhero', 'ready_items', uid_default_sn_ready_items() );
	if ( ! is_array( $ready ) ) $ready = array();
	?>
	<section class="dark heroA">
	  <div class="sn-wrap">
	    <div style="padding-block-start:80px">
	      <div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	        <a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت ثنا', 'uid-theme' ); ?></a><span class="sep">/</span><b><?php esc_html_e( 'ثبت‌نام غیرحضوری', 'uid-theme' ); ?></b></div>
	    </div>
	    <div class="heroA-grid">
	      <div class="rv">
	        <span class="sn-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'snhero', 'eyebrow', __( 'احراز هویت سامانه ثنا · غیرحضوری با یوآیدی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'snhero', 'heading', __( 'تا وقتی کد ثنا نگیرید،<mark>ابلاغیه قضایی‌تان دیده نمی‌شود</mark>.', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo esc_html( uid_section_val( 'snhero', 'text', __( 'سامانه ثنا تنها دروازه ورود به خدمات غیرحضوری قوه قضاییه است. یوآیدی، ثبت‌نام و احراز هویت ثنای شما را در کمتر از ۵ دقیقه، با نصف هزینه‌ی حضوری و همراه با تیم متخصص انجام می‌دهد — بدون صف، بدون رفت‌وآمد.', 'uid-theme' ) ) ); ?></p>
	        <div class="btn-row">
	          <button class="sn-btn btn-cta" data-open-modal><?php echo uid_sn_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'snhero', 'btn1_text', __( 'ثبت‌نام و احراز هویت ثنا', 'uid-theme' ) ) ); ?></button>
	          <a class="sn-btn btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo uid_sn_phone_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="hero-tags">
	          <?php foreach ( $tags as $t ) : ?>
	          <span class="hero-tag"><?php echo uid_sn_check_icon(); ?> <?php echo esc_html( $t ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <!-- چک‌لیست آمادگی + وضعیت زنده پشتیبانی — کاملاً تعاملی، سمت کلاینت -->
	      <div class="engine rv rv-d2" id="readyCard">
	        <div class="sup-status" id="supStatus">
	          <span class="dot" aria-hidden="true"></span>
	          <div><b id="supTitle"><?php esc_html_e( 'در حال بررسی وضعیت پشتیبانی…', 'uid-theme' ); ?></b><span id="supSub"><?php esc_html_e( 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر', 'uid-theme' ); ?></span></div>
	        </div>
	        <div class="engine-hd" style="border:0;margin-block-end:14px;padding-block-end:0">
	          <b><?php echo esc_html( uid_section_val( 'snhero', 'checklist_title', __( 'چک‌لیست آمادگی ۵ دقیقه‌ای', 'uid-theme' ) ) ); ?></b><span><?php esc_html_e( 'قبل از شروع تیک بزنید', 'uid-theme' ); ?></span>
	        </div>
	        <div class="ready-list" id="readyList">
	          <?php foreach ( $ready as $r ) : ?>
	          <label class="ready-item"><input type="checkbox" data-ready><span class="ready-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
	            <span class="tx"><b><?php echo esc_html( $r['title'] ?? '' ); ?></b><span><?php echo esc_html( $r['text'] ?? '' ); ?></span></span></label>
	          <?php endforeach; ?>
	        </div>
	        <div class="ready-progress"><i id="readyBar"></i></div>
	        <div class="ready-note"><span id="readyMsg">۰ <?php esc_html_e( 'از', 'uid-theme' ); ?> <?php echo esc_html( uid_fa_digits( count( $ready ) ) ); ?> <?php esc_html_e( 'آماده', 'uid-theme' ); ?></span><span><?php esc_html_e( 'تیم ما هرمرحله را با شما چک می‌کند', 'uid-theme' ); ?></span></div>

	        <form class="micro" id="microForm" novalidate>
	          <div class="micro-fields">
	            <p><?php esc_html_e( 'آماده‌اید؟ شماره‌تان را بگذارید، همکار ما همین امروز تماس می‌گیرد و مرحله‌به‌مرحله همراهی می‌کند.', 'uid-theme' ); ?></p>
	            <div class="micro-row">
	              <div class="fld"><input id="q_tel" name="phone" type="tel" inputmode="numeric"
	                   placeholder="۰۹xxxxxxxxx" data-req data-tel aria-label="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>">
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	              <button class="sn-btn btn-cta" type="button" data-submit><?php esc_html_e( 'شروع ثبت‌نام', 'uid-theme' ); ?></button>
	            </div>
	          </div>
	          <div class="form-ok"><?php echo uid_sn_check_icon(); ?><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
	            <span><?php esc_html_e( 'همکار ما به‌زودی تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	        </form>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۲) بند فوریت
 * ===================================================================== */
function uid_render_section_snurgency() {
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="sn-wrap">
	    <div class="callband urgent rv">
	      <div class="ic"><?php echo uid_sn_warn_icon(); ?></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'snurgency', 'heading', __( 'ثبت‌نام را عقب نیندازید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'snurgency', 'text', __( 'بدون کد ثنا نه ابلاغیه قضایی می‌بینید، نه می‌توانید شکایت یا دادخواست ثبت کنید — و رسیدگی به پرونده‌تان معطل می‌ماند. پشتیبانی تیم متخصص یوآیدی فقط شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوست؛ همین حالا وقت بگیرید.', 'uid-theme' ) ) ); ?></p></div>
	      <div class="acts">
	        <button class="sn-btn btn-cta sn-btn-sm" data-open-modal><?php echo esc_html( uid_section_val( 'snurgency', 'btn1_text', __( 'ثبت‌نام همین حالا', 'uid-theme' ) ) ); ?></button>
	        <a class="sn-btn btn-ghost-d sn-btn-sm" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php echo esc_html( uid_section_val( 'snurgency', 'btn2_text', __( 'تماس با پشتیبانی', 'uid-theme' ) ) ); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۳) مزایای غیرحضوری بودن
 * ===================================================================== */
function uid_default_sn_adv() {
	return array(
		array( 'icon' => 'home', 'title' => 'احراز هویت ثنا در منزل', 'text' => 'کل فرآیند از همان‌جایی که هستید انجام می‌شود' ),
		array( 'icon' => 'reduce', 'title' => 'کاهش تردد و سفرهای درون‌شهری', 'text' => 'نیازی به مراجعه حضوری به دفاتر خدمات قضایی نیست' ),
		array( 'icon' => 'flex', 'title' => 'انعطاف و مقیاس‌پذیری بالا', 'text' => 'هر زمان که برایتان مناسب است، شروع کنید' ),
		array( 'icon' => 'clock', 'title' => 'ثبت‌نام در کمتر از ۵ دقیقه', 'text' => 'از شروع تا صدور، یک فرآیند کوتاه و ساده' ),
		array( 'icon' => 'money', 'title' => 'با نصف هزینه نسبت به حضوری', 'text' => 'صرفه‌جویی در هزینه بدون افت کیفیت خدمت' ),
		array( 'icon' => 'hours', 'title' => 'ارائه خدمات ۲۴ ساعته', 'text' => 'سامانه ثبت‌نام همیشه در دسترس شماست' ),
		array( 'icon' => 'phone', 'title' => 'احراز هویت ثنا با گوشی', 'text' => 'فقط با موبایل هوشمند، بدون نیاز به کامپیوتر' ),
		array( 'icon' => 'globe', 'title' => 'دسترسی هموطنان خارج از کشور', 'text' => 'ایرانیان مقیم خارج هم می‌توانند ثبت‌نام کنند' ),
	);
}
function uid_render_section_snadv() {
	$tag   = uid_section_tag( 'snadv', 'h2' );
	$items = uid_section_val( 'snadv', 'items', uid_default_sn_adv() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec">
	  <div class="sn-wrap">
	    <div class="sec-head mid rv">
	      <span class="sn-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'snadv', 'eyebrow', __( 'چرا غیرحضوری؟', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'snadv', 'heading', __( 'مزایای احراز هویت ثنا در مقایسه با ثبت‌نام حضوری', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'snadv', 'text', __( 'همان کد ثنا، همان اعتبار قانونی — بدون رفت‌وآمد به دفاتر خدمات قضایی.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="adv-grid rv" data-rail="adv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="adv-card"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sn_adv_icon_svg( $it['icon'] ?? 'home' ), array( 'path' => array( 'd' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) ); ?></svg></div>
	        <b><?php echo esc_html( $it['title'] ?? '' ); ?></b><span><?php echo esc_html( $it['text'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	    <div class="dots" data-dots="adv"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۴) کاربرد سامانه ثنا (۳ گام)
 * ===================================================================== */
function uid_default_sn_flow() {
	return array(
		array( 'title' => 'ثبت‌نام و احراز هویت', 'text' => 'یوآیدی مراحل احراز هویت غیرحضوری سامانه ثنا را برایتان انجام می‌دهد؛ کمتر از ۵ دقیقه.' ),
		array( 'title' => 'دریافت رمز شخصی (کد ثنا)', 'text' => 'در کمتر از ۳ دقیقه بعد از پایان احراز هویت، رمز شخصی شما صادر می‌شود.' ),
		array( 'title' => 'استفاده در adliran.ir', 'text' => 'با کد ثنا به سامانه عدل ایران مراجعه و از تمام خدمات الکترونیکی قوه قضاییه بهره‌مند شوید.' ),
	);
}
function uid_render_section_snflow() {
	$tag   = uid_section_tag( 'snflow', 'h2' );
	$steps = uid_section_val( 'snflow', 'steps', uid_default_sn_flow() );
	if ( ! is_array( $steps ) ) $steps = array();
	?>
	<section class="sec" style="background:var(--n50);padding-block:var(--sec)">
	  <div class="sn-wrap">
	    <div class="sec-head rv">
	      <span class="sn-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'snflow', 'eyebrow', __( 'سامانه ثنا چیست', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'snflow', 'heading', __( 'سامانه ثنا چه کاربردی دارد؟', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'snflow', 'text', __( 'سامانه احراز هویت ثنا، پنجره ورودی قوه قضاییه است. برای استفاده از هر خدمت الکترونیکی و غیرحضوری قوه قضاییه، ابتدا باید ثبت‌نام و احراز هویت ثنا را انجام دهید و رمز شخصی (کد ثنا) دریافت کنید.', 'uid-theme' ) ) ); ?></p>
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
	    <p class="tiny rv" style="margin-top:18px;text-align:center"><?php echo esc_html( uid_section_val( 'snflow', 'note', __( 'توجه: احراز هویت غیرحضوری ثنا فقط برای اشخاص حقیقی فعال است. اشخاص حقوقی همچنان باید حضوری به دفاتر خدمات قضایی مراجعه کنند.', 'uid-theme' ) ) ); ?></p>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۵) بند ایرانیان خارج از کشور
 * ===================================================================== */
function uid_render_section_snforeign() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="sn-wrap">
	    <div class="forn rv">
	      <div class="glob"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M2.5 12h19M12 2.5c2.6 2.6 4 6 4 9.5s-1.4 6.9-4 9.5c-2.6-2.6-4-6-4-9.5s1.4-6.9 4-9.5z"/></svg></div>
	      <div class="tx"><b><?php echo esc_html( uid_section_val( 'snforeign', 'heading', __( 'هموطن خارج از کشور هستید؟', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'snforeign', 'text', __( 'ایرانیان مقیم خارج از کشور هم می‌توانند بدون مراجعه حضوری، ثبت‌نام و احراز هویت غیرحضوری سامانه ثنا را از طریق یوآیدی انجام دهند.', 'uid-theme' ) ) ); ?></p></div>
	      <a class="sn-btn btn-navy" href="<?php echo esc_url( uid_section_val( 'snforeign', 'btn_url', '/sana-register-foreign-form/' ) ); ?>"><?php echo esc_html( uid_section_val( 'snforeign', 'btn_text', __( 'ثبت سفارش ثنا برای ایرانیان خارج از کشور', 'uid-theme' ) ) ); ?>
	        <?php echo uid_sn_arrow_icon(); ?></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۶) خدمات پس از کد ثنا
 * ===================================================================== */
function uid_default_sn_svc() {
	return array(
		'ارائه و پیگیری دادخواست عفو', 'ارائه و پیگیری شکواییه', 'ارائه و پیگیری اظهارنامه', 'محاسبه هزینه دادرسی',
		'ارائه لایحه', 'درخواست گواهی عدم سوء پیشینه', 'اطلاع‌رسانی پرونده', 'استعلام وضعیت کارشناس',
		'ارائه مدارک و مستندات پرونده', 'استعلام اصالت مدارک قضایی', 'درخواست صدور سند مالکیت', 'شکایت از ارگان‌ها و دستگاه‌های اجرایی',
	);
}
function uid_render_section_snsvc() {
	$tag = uid_section_tag( 'snsvc', 'h2' );
	$items_raw = uid_section_val( 'snsvc', 'items', implode( "\n", uid_default_sn_svc() ) );
	$items = array_values( array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) ) );
	if ( ! $items ) return;
	?>
	<section class="sec">
	  <div class="sn-wrap">
	    <div class="sec-head mid rv">
	      <span class="sn-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'snsvc', 'eyebrow', __( 'بعد از دریافت کد ثنا', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'snsvc', 'heading', __( 'خدماتی که با کد ثنا، غیرحضوری در دسترستان قرار می‌گیرد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo wp_kses( uid_section_val( 'snsvc', 'text', __( 'بعد از دریافت رمز شخصی، با مراجعه به <span class="lat mono" style="font-size:14px">adliran.ir</span> به این خدمات دسترسی خواهید داشت.', 'uid-theme' ) ), array( 'span' => array( 'class' => true, 'style' => true ) ) ); ?></p>
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
 * ۷) خطاهای رایج و راه‌حل
 * ===================================================================== */
function uid_default_sn_risks() {
	return array(
		array( 'title' => 'خطای عدم پشتیبانی دوربین', 'bad_text' => 'کیفیت پایین دوربین یا تاریکی و نور کم محیط، رایج‌ترین دلیل شکست ثبت‌نام است.', 'fix_text' => 'پیش از شروع، دستگاه و نور محیط را با شما بررسی می‌کنیم تا از همان مرحله اول درست پیش برود.' ),
		array( 'title' => 'خطا در ثبت‌نام و انتظار ۳۰ دقیقه‌ای', 'bad_text' => 'در صورت بروز خطا، باید به صفحه اول بازگشت و بعد از ۳۰ دقیقه دوباره تلاش کرد.', 'fix_text' => 'فرآیند را طوری هدایت می‌کنیم که خطا از ابتدا رخ ندهد و نیازی به تکرار ۳۰ دقیقه‌ای نباشد.' ),
		array( 'title' => 'خطای «عدم وجود عکس» برای مقیمان خارج از کشور', 'bad_text' => 'هموطنان ساکن خارج از کشور گاهی در سامانه ثبت احوال با این خطا مواجه می‌شوند.', 'fix_text' => 'مسیر رفع این خطا از طریق سامانه میخک را هم همراه شما پیش می‌بریم.' ),
		array( 'title' => 'مرورگر یا سیستم‌عامل نامناسب', 'bad_text' => 'استفاده از مرورگر یا سیستم‌عامل اشتباه، باعث خطا یا کندی فرآیند می‌شود.', 'fix_text' => 'پیشنهاد می‌کنیم: اندروید با Chrome، iOS با Safari — و ترجیحاً با گوشی هوشمند اندرویدی.' ),
	);
}
function uid_render_section_snrisk() {
	$tag   = uid_section_tag( 'snrisk', 'h2' );
	$items = uid_section_val( 'snrisk', 'items', uid_default_sn_risks() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section class="sec" style="background:var(--n50)">
	  <div class="sn-wrap">
	    <div class="sec-head mid rv">
	      <span class="sn-eyebrow warm"><i></i><?php echo esc_html( uid_section_val( 'snrisk', 'eyebrow', __( 'چرا با تیم متخصص، نه تنها خودتان', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'snrisk', 'heading', __( 'چرا بیشتر خطاهای ثبت‌نام ثنا، با یک همراه متخصص از بین می‌رود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'snrisk', 'text', __( 'این‌ها رایج‌ترین دلایلی هستند که ثبت‌نام شخصی افراد در سامانه ثنا با خطا مواجه می‌شود — و اینکه تیم یوآیدی چطور همان لحظه جلویش را می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="leak-grid rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="leak">
	        <div class="leak-bad"><span class="tag"><?php echo uid_sn_warn_icon(); ?><?php esc_html_e( 'خطای رایج', 'uid-theme' ); ?></span>
	          <h3><?php echo esc_html( $it['title'] ?? '' ); ?></h3>
	          <p><?php echo esc_html( $it['bad_text'] ?? '' ); ?></p></div>
	        <div class="leak-fix"><span class="tag"><?php echo uid_sn_check_icon(); ?><?php esc_html_e( 'راه‌حل یوآیدی', 'uid-theme' ); ?></span>
	          <p><?php echo esc_html( $it['fix_text'] ?? '' ); ?></p></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۸) باند اعتماد
 * ===================================================================== */
function uid_default_sn_trust() {
	return array(
		array( 'type' => 'count', 'value' => '5000000', 'label' => 'احراز هویت موفق تاکنون' ),
		array( 'type' => 'count', 'value' => '60000', 'label' => 'احراز هویت در پرترافیک‌ترین روز' ),
		array( 'type' => 'text', 'value' => '۹۹٫۵٪', 'label' => 'تعهد پایداری سرویس (SLA)' ),
		array( 'type' => 'text', 'value' => '۹۹٪', 'label' => 'دقت تطبیق چهره با هوش مصنوعی' ),
		array( 'type' => 'text', 'value' => '< ۳ دقیقه', 'label' => 'صدور رمز شخصی ثنا' ),
	);
}
function uid_render_section_sntrust() {
	$tag   = uid_section_tag( 'sntrust', 'h2' );
	$cells = uid_section_val( 'sntrust', 'cells', uid_default_sn_trust() );
	if ( ! is_array( $cells ) || empty( $cells ) ) return;
	?>
	<section class="dark sec">
	  <div class="sn-wrap">
	    <div class="sec-head mid rv">
	      <span class="sn-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'sntrust', 'eyebrow', __( 'اعتماد شما، اولویت ماست', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec" style="color:#fff">'; ?><?php echo esc_html( uid_section_val( 'sntrust', 'heading', __( 'یوآیدی، کارگزار مورد تایید سامانه‌های ثنا و سجام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
 * ۹) راه‌های پشتیبانی + تعهدنامه
 * ===================================================================== */
function uid_default_sn_team_rows() {
	return array(
		array( 'icon' => 'phone', 'title' => 'تماس تلفنی مستقیم', 'text' => 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر — بدون منشی صوتی، مستقیم با کارشناس' ),
		array( 'icon' => 'support', 'title' => 'واتساپ و تلگرام', 'text' => 'راهنمایی مرحله‌به‌مرحله، همراه با عکس و راهنمای تصویری' ),
		array( 'icon' => 'doc', 'title' => 'پیگیری تا صدور کد ثنا', 'text' => 'کار شما با ثبت درخواست تمام نمی‌شود؛ تا دریافت رمز شخصی همراه‌تان هستیم' ),
	);
}
function uid_default_sn_pledge_items() {
	return array(
		'هیچ هزینه‌ی پنهانی درخواست نمی‌کنیم؛ هزینه از قبل شفاف است.',
		'اطلاعات هویتی شما فقط برای همین فرآیند استفاده می‌شود.',
		'تا صدور کد ثنا و رفع هر خطا، کنار شما می‌مانیم.',
	);
}
function uid_sn_team_icon_svg( $key ) {
	$icons = array(
		'phone'   => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/>',
		'support' => '<path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/>',
		'doc'     => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>',
	);
	return $icons[ $key ] ?? $icons['phone'];
}
function uid_render_section_snteam() {
	$rows = uid_section_val( 'snteam', 'rows', uid_default_sn_team_rows() );
	$pledge_raw = uid_section_val( 'snteam', 'pledge_items', implode( "\n", uid_default_sn_pledge_items() ) );
	$pledge = array_filter( array_map( 'trim', explode( "\n", $pledge_raw ) ) );
	if ( ! is_array( $rows ) ) $rows = array();
	?>
	<section class="sec">
	  <div class="sn-wrap">
	    <div class="team rv">
	      <div>
	      <div class="team-list" data-rail="team">
	        <?php foreach ( $rows as $r ) : ?>
	        <div class="team-row"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><?php echo wp_kses( uid_sn_team_icon_svg( $r['icon'] ?? 'phone' ), array( 'path' => array( 'd' => true ) ) ); ?></svg></div>
	          <div><b><?php echo esc_html( $r['title'] ?? '' ); ?></b><p><?php echo esc_html( $r['text'] ?? '' ); ?></p></div></div>
	        <?php endforeach; ?>
	      </div>
	      <div class="dots" data-dots="team"></div>
	      </div>
	      <div class="pledge">
	        <div class="pl-hd"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.6 5.6 6.1.9-4.4 4.3 1 6.1-5.3-2.8-5.3 2.8 1-6.1L3.3 8.5l6.1-.9z"/></svg></span><b><?php echo esc_html( uid_section_val( 'snteam', 'pledge_heading', __( 'تعهد تیم پشتیبانی یوآیدی', 'uid-theme' ) ) ); ?></b></div>
	        <ul>
	          <?php foreach ( $pledge as $p ) : ?>
	          <li><?php echo uid_sn_check_icon(); ?><?php echo esc_html( $p ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۰) هشدار پیامک جعلی
 * ===================================================================== */
function uid_render_section_snsecurity() {
	?>
	<section class="sec" style="padding-block:0 var(--sec)">
	  <div class="sn-wrap">
	    <div class="secbox rv">
	      <div class="ic"><?php echo uid_sn_shield_icon(); ?></div>
	      <div><b><?php echo esc_html( uid_section_val( 'snsecurity', 'heading', __( 'هشدار: مراقب پیامک‌های جعلی باشید', 'uid-theme' ) ) ); ?></b>
	        <p><?php echo esc_html( uid_section_val( 'snsecurity', 'text', __( 'سامانه ثنا هیچ‌گونه پیامکی برای پرداخت وجه جهت مشاهده ابلاغیه قضایی ارسال نمی‌کند. دریافت وجه فقط هنگام انجام فرایند احراز هویت صورت می‌گیرد. اگر پیامکی از این دست دریافت کردید، جعلی است.', 'uid-theme' ) ) ); ?></p></div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۱) سوالات متداول
 * ===================================================================== */
function uid_default_sn_faq() {
	return array(
		array( 'question' => 'سامانه ثنا چیست؟', 'answer' => 'این سامانه توسط قوه قضاییه و به منظور ارائه خدمات غیرحضوری قضایی مانند مشاهده ابلاغ اوراق قضایی و ثبت شکایات و دادخواست‌ها ایجاد شده است.' ),
		array( 'question' => 'صدور رمز شخصی در سامانه ثنا چقدر طول می‌کشد؟', 'answer' => 'پس از اتمام احراز هویت سامانه ثنا با یوآیدی، طی کمتر از ۳ دقیقه رمز شخصی کاربر صادر خواهد شد.' ),
		array( 'question' => 'پیامکی برای پرداخت وجه جهت دریافت ابلاغیه قضایی گرفتم، چه کنم؟', 'answer' => 'سامانه ثنا هیچ‌گونه پیامی برای ثبت‌نام و احراز هویت به کاربر ارسال نمی‌کند. دریافت وجه فقط هنگام انجام فرایند احراز هویت انجام می‌پذیرد. این‌گونه پیامک‌ها جعلی هستند.' ),
		array( 'question' => 'بهترین وسیله برای انجام این فرآیند کدام است؟', 'answer' => 'توصیه ما این است که با گوشی هوشمند دارای سیستم‌عامل اندروید این فرآیند را انجام دهید.' ),
		array( 'question' => 'از چه مرورگرهایی استفاده کنیم؟', 'answer' => 'ترجیحاً برای iOS از مرورگر Safari و برای Android از مرورگر Chrome استفاده شود.' ),
		array( 'question' => 'ساکنین خارج از کشور با خطای «عدم وجود عکس در ثبت احوال» چه کنند؟', 'answer' => 'هموطنان ساکن خارج از کشور می‌توانند برای حل این مشکل به سامانه میخک به آدرس mikhak.mfa.gov.ir مراجعه کنند.' ),
		array( 'question' => 'در صورت بروز خطا هنگام ثبت‌نام چه کنیم؟', 'answer' => 'به صفحه اول سامانه ثنا بازگردید و بعد از ۳۰ دقیقه فرآیند احراز هویت را دوباره انجام دهید.' ),
		array( 'question' => 'بعد از ثبت‌نام و احراز هویت چه اتفاقی می‌افتد؟', 'answer' => 'بعد از اتمام ثبت‌نام، تمام ابلاغیه‌های قضایی کاربر به نام کاربری او ارسال و با پیامک به وی اطلاع داده می‌شود.' ),
		array( 'question' => 'دلیل خطای عدم پشتیبانی دوربین چیست؟', 'answer' => 'این خطا معمولاً به دو دلیل رخ می‌دهد: کیفیت پایین دوربین، یا تاریکی و نور کم محیط.' ),
		array( 'question' => 'آیا سامانه ثنا همان سامانه سنا است؟', 'answer' => 'خیر. سامانه سنا به‌منظور ثبت روزمره مبادلات ارزی راه‌اندازی شده و ارتباطی به سامانه ثنای قوه قضاییه ندارد.' ),
	);
}
function uid_render_section_snfaq() {
	$tag   = uid_section_tag( 'snfaq', 'h2' );
	$items = uid_section_val( 'snfaq', 'items', uid_default_sn_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq">
	  <div class="sn-wrap">
	    <div class="sec-head mid rv">
	      <span class="sn-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'snfaq', 'eyebrow', __( 'پرسش‌های پرتکرار', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'snfaq', 'heading', __( 'سوالات متداول درباره احراز هویت غیرحضوری ثنا', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
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
 * ۱۲) بنر تماس نهایی (فرم لید)
 * ===================================================================== */
function uid_render_section_snlead() {
	$tag = uid_section_tag( 'snlead', 'h2' );
	$trust_raw = uid_section_val( 'snlead', 'trust', "کارگزار مورد تایید سامانه ثنا\n+۵٬۰۰۰٬۰۰۰ احراز هویت موفق\nپشتیبانی تا صدور کد ثنا" );
	$trust = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$topic_raw = uid_section_val( 'snlead', 'topic_options', "ثبت‌نام و احراز هویت ثنا (داخل کشور)\nثبت‌نام و احراز هویت ثنا (خارج از کشور)\nسوال یا مشکل در ثبت‌نام قبلی" );
	$topics = array_filter( array_map( 'trim', explode( "\n", $topic_raw ) ) );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="sn-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="sn-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'snlead', 'eyebrow', __( 'همین حالا شروع کنید', 'uid-theme' ) ) ); ?></span>
	          <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'snlead', 'heading', __( 'ثبت‌نام و احراز هویت سامانه ثنا را به تیم متخصص یوآیدی بسپارید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	          <p><?php echo esc_html( uid_section_val( 'snlead', 'text', __( 'کمتر از ۵ دقیقه، با نصف هزینه‌ی حضوری، و بدون نگرانی از خطاهای رایج. همکار ما شماره‌تان را می‌گیرد و کل مسیر تا صدور کد ثنا را همراهی می‌کند.', 'uid-theme' ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_sn_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'snlead', 'form_title', __( 'ثبت‌نام سریع', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'snlead', 'form_hint', __( 'فرم را پر کنید؛ کارشناس ما شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <div class="frow">
	              <div class="fld"><input name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	                <span class="err"><?php esc_html_e( 'نام را وارد کنید.', 'uid-theme' ); ?></span></div>
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric" placeholder="۰۹xxxxxxxxx" data-req data-tel>
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	            </div>
	            <div class="fld"><select name="topic" data-req>
	                <option value=""><?php esc_html_e( 'موضوع درخواست…', 'uid-theme' ); ?></option>
	                <?php foreach ( $topics as $t ) : ?>
	                <option><?php echo esc_html( $t ); ?></option>
	                <?php endforeach; ?>
	              </select></div>
	            <button class="sn-btn btn-cta sn-btn-block" type="button" data-submit><?php echo uid_sn_submit_icon(); ?> <?php echo esc_html( uid_section_val( 'snlead', 'submit_text', __( 'ثبت‌نام و احراز هویت ثنا', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_sn_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'snlead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_sn_check_icon(); ?><b><?php echo esc_html( uid_section_val( 'snlead', 'success_title', __( 'درخواست شما ثبت شد', 'uid-theme' ) ) ); ?></b>
	              <span><?php echo esc_html( uid_section_val( 'snlead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
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
function uid_get_sn_page_id() {
	$page_id = (int) get_option( 'uid_sn_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_SANA_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_sn_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_sn_page() {
	if ( uid_get_sn_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'احراز هویت ثنا', 'uid-theme' ),
		'post_name'   => 'sana',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_SANA_TEMPLATE );
	update_option( 'uid_sn_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_sn_page_once' );

function uid_ensure_sn_page_once() {
	if ( get_option( 'uid_sn_page_bootstrapped' ) ) return;
	uid_ensure_sn_page();
	update_option( 'uid_sn_page_bootstrapped', 1 );
}
add_action( 'admin_init', 'uid_ensure_sn_page_once' );

function uid_register_sn_slug_setting() {
	register_setting( 'uid_sn_group', 'uid_sn_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_sn_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_sn_page_slug_section', '', '__return_false', 'uid_sn_layout' );
	add_settings_field( 'uid_sn_page_slug', __( 'آدرس (اسلاگ) صفحه ثنا', 'uid-theme' ), 'uid_field_sn_page_slug', 'uid_sn_layout', 'uid_sn_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_sn_slug_setting' );

function uid_field_sn_page_slug( $args ) {
	$page_id = uid_get_sn_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_sn_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_sn_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه ثنا هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_sn_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_sn_page_id();

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
function uid_register_sn_settings() {
	register_setting( 'uid_sn_group', 'uid_sn_layout', array(
		'sanitize_callback' => 'uid_sanitize_sn_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_sn_layout_main', '', '__return_false', 'uid_sn_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_sn_layout', 'uid_sn_layout_main', array(
		'option_name' => 'uid_sn_layout', 'registry_fn' => 'uid_sn_sections_registry', 'layout_fn' => 'uid_get_sn_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snhero', array( 'sanitize_callback' => 'uid_sanitize_section_snhero', 'default' => array() ) );
	add_settings_section( 'uid_section_snhero_main', '', '__return_false', 'uid_section_snhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'eyebrow', 'default' => 'احراز هویت سامانه ثنا · غیرحضوری با یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'heading', 'default' => 'تا وقتی کد ثنا نگیرید،<mark>ابلاغیه قضایی‌تان دیده نمی‌شود</mark>.' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'text', 'default' => 'سامانه ثنا تنها دروازه ورود به خدمات غیرحضوری قوه قضاییه است. یوآیدی، ثبت‌نام و احراز هویت ثنای شما را در کمتر از ۵ دقیقه، با نصف هزینه‌ی حضوری و همراه با تیم متخصص انجام می‌دهد — بدون صف، بدون رفت‌وآمد.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'btn1_text', 'default' => 'ثبت‌نام و احراز هویت ثنا' ) );
	add_settings_field( 'tags', __( 'برچسب‌های اطمینان زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'tags', 'default' => "صدور کد ثنا در کمتر از ۳ دقیقه\nهمراه با تیم متخصص، نه تنها یک راهنما\nکارگزار مورد تایید سامانه ثنا" ) );
	add_settings_field( 'checklist_title', __( 'عنوان چک‌لیست آمادگی', 'uid-theme' ), 'uid_field_text', 'uid_section_snhero', 'uid_section_snhero_main', array( 'group' => 'uid_section_snhero', 'key' => 'checklist_title', 'default' => 'چک‌لیست آمادگی ۵ دقیقه‌ای' ) );
	add_settings_field( 'ready_items', __( 'موارد چک‌لیست آمادگی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snhero', 'uid_section_snhero_main', array(
		'group' => 'uid_section_snhero', 'key' => 'ready_items', 'default' => uid_default_sn_ready_items(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'sup_notice', '', 'uid_field_notice', 'uid_section_snhero', 'uid_section_snhero_main', array( 'text' => __( 'وضعیت زنده پشتیبانی (بالای کارت) خودکار و بر اساس ساعت واقعی (شنبه تا چهارشنبه، ۹ تا ۱۸ به وقت تهران) محاسبه می‌شود و از این صفحه قابل‌ویرایش نیست.', 'uid-theme' ) ) );

	/* ---------------- بند فوریت ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snurgency', array( 'sanitize_callback' => 'uid_sanitize_section_snurgency', 'default' => array() ) );
	add_settings_section( 'uid_section_snurgency_main', '', '__return_false', 'uid_section_snurgency' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snurgency', 'uid_section_snurgency_main', array( 'group' => 'uid_section_snurgency', 'key' => 'heading', 'default' => 'ثبت‌نام را عقب نیندازید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snurgency', 'uid_section_snurgency_main', array( 'group' => 'uid_section_snurgency', 'key' => 'text', 'default' => 'بدون کد ثنا نه ابلاغیه قضایی می‌بینید، نه می‌توانید شکایت یا دادخواست ثبت کنید — و رسیدگی به پرونده‌تان معطل می‌ماند. پشتیبانی تیم متخصص یوآیدی فقط شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر پاسخگوست؛ همین حالا وقت بگیرید.' ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_snurgency', 'uid_section_snurgency_main', array( 'group' => 'uid_section_snurgency', 'key' => 'btn1_text', 'default' => 'ثبت‌نام همین حالا' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم (تماس تلفنی)', 'uid-theme' ), 'uid_field_text', 'uid_section_snurgency', 'uid_section_snurgency_main', array( 'group' => 'uid_section_snurgency', 'key' => 'btn2_text', 'default' => 'تماس با پشتیبانی' ) );

	/* ---------------- مزایای غیرحضوری بودن ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snadv', array( 'sanitize_callback' => 'uid_sanitize_section_snadv', 'default' => array() ) );
	add_settings_section( 'uid_section_snadv_main', '', '__return_false', 'uid_section_snadv' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snadv', 'uid_section_snadv_main', array( 'group' => 'uid_section_snadv', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snadv', 'uid_section_snadv_main', array( 'group' => 'uid_section_snadv', 'key' => 'eyebrow', 'default' => 'چرا غیرحضوری؟' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snadv', 'uid_section_snadv_main', array( 'group' => 'uid_section_snadv', 'key' => 'heading', 'default' => 'مزایای احراز هویت ثنا در مقایسه با ثبت‌نام حضوری' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snadv', 'uid_section_snadv_main', array( 'group' => 'uid_section_snadv', 'key' => 'text', 'default' => 'همان کد ثنا، همان اعتبار قانونی — بدون رفت‌وآمد به دفاتر خدمات قضایی.' ) );
	add_settings_field( 'items', __( 'کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snadv', 'uid_section_snadv_main', array(
		'group' => 'uid_section_snadv', 'key' => 'items', 'default' => uid_default_sn_adv(), 'add_label' => __( 'افزودن مزیت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'home' => __( 'خانه', 'uid-theme' ), 'reduce' => __( 'کاهش تردد', 'uid-theme' ), 'flex' => __( 'انعطاف', 'uid-theme' ), 'clock' => __( 'زمان', 'uid-theme' ), 'money' => __( 'هزینه', 'uid-theme' ), 'hours' => __( 'ساعت کاری', 'uid-theme' ), 'phone' => __( 'موبایل', 'uid-theme' ), 'globe' => __( 'جهانی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- کاربرد سامانه ثنا ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snflow', array( 'sanitize_callback' => 'uid_sanitize_section_snflow', 'default' => array() ) );
	add_settings_section( 'uid_section_snflow_main', '', '__return_false', 'uid_section_snflow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snflow', 'uid_section_snflow_main', array( 'group' => 'uid_section_snflow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snflow', 'uid_section_snflow_main', array( 'group' => 'uid_section_snflow', 'key' => 'eyebrow', 'default' => 'سامانه ثنا چیست' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snflow', 'uid_section_snflow_main', array( 'group' => 'uid_section_snflow', 'key' => 'heading', 'default' => 'سامانه ثنا چه کاربردی دارد؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snflow', 'uid_section_snflow_main', array( 'group' => 'uid_section_snflow', 'key' => 'text', 'default' => 'سامانه احراز هویت ثنا، پنجره ورودی قوه قضاییه است. برای استفاده از هر خدمت الکترونیکی و غیرحضوری قوه قضاییه، ابتدا باید ثبت‌نام و احراز هویت ثنا را انجام دهید و رمز شخصی (کد ثنا) دریافت کنید.' ) );
	add_settings_field( 'steps', __( 'گام‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snflow', 'uid_section_snflow_main', array(
		'group' => 'uid_section_snflow', 'key' => 'steps', 'default' => uid_default_sn_flow(), 'add_label' => __( 'افزودن گام', 'uid-theme' ),
		'fields' => array( array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان گام', 'uid-theme' ), 'required' => true ), array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ) ),
	) );
	add_settings_field( 'note', __( 'یادداشت پایین', 'uid-theme' ), 'uid_field_text', 'uid_section_snflow', 'uid_section_snflow_main', array( 'group' => 'uid_section_snflow', 'key' => 'note', 'default' => 'توجه: احراز هویت غیرحضوری ثنا فقط برای اشخاص حقیقی فعال است. اشخاص حقوقی همچنان باید حضوری به دفاتر خدمات قضایی مراجعه کنند.' ) );

	/* ---------------- بند ایرانیان خارج از کشور ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snforeign', array( 'sanitize_callback' => 'uid_sanitize_section_snforeign', 'default' => array() ) );
	add_settings_section( 'uid_section_snforeign_main', '', '__return_false', 'uid_section_snforeign' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snforeign', 'uid_section_snforeign_main', array( 'group' => 'uid_section_snforeign', 'key' => 'heading', 'default' => 'هموطن خارج از کشور هستید؟' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snforeign', 'uid_section_snforeign_main', array( 'group' => 'uid_section_snforeign', 'key' => 'text', 'default' => 'ایرانیان مقیم خارج از کشور هم می‌توانند بدون مراجعه حضوری، ثبت‌نام و احراز هویت غیرحضوری سامانه ثنا را از طریق یوآیدی انجام دهند.' ) );
	add_settings_field( 'btn_text', __( 'متن دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_snforeign', 'uid_section_snforeign_main', array( 'group' => 'uid_section_snforeign', 'key' => 'btn_text', 'default' => 'ثبت سفارش ثنا برای ایرانیان خارج از کشور' ) );
	add_settings_field( 'btn_url', __( 'لینک دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_snforeign', 'uid_section_snforeign_main', array( 'group' => 'uid_section_snforeign', 'key' => 'btn_url', 'default' => '/sana-register-foreign-form/' ) );

	/* ---------------- خدمات پس از کد ثنا ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snsvc', array( 'sanitize_callback' => 'uid_sanitize_section_snsvc', 'default' => array() ) );
	add_settings_section( 'uid_section_snsvc_main', '', '__return_false', 'uid_section_snsvc' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snsvc', 'uid_section_snsvc_main', array( 'group' => 'uid_section_snsvc', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snsvc', 'uid_section_snsvc_main', array( 'group' => 'uid_section_snsvc', 'key' => 'eyebrow', 'default' => 'بعد از دریافت کد ثنا' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snsvc', 'uid_section_snsvc_main', array( 'group' => 'uid_section_snsvc', 'key' => 'heading', 'default' => 'خدماتی که با کد ثنا، غیرحضوری در دسترستان قرار می‌گیرد' ) );
	add_settings_field( 'text', __( 'توضیح (تگ span مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snsvc', 'uid_section_snsvc_main', array( 'group' => 'uid_section_snsvc', 'key' => 'text', 'default' => 'بعد از دریافت رمز شخصی، با مراجعه به <span class="lat mono" style="font-size:14px">adliran.ir</span> به این خدمات دسترسی خواهید داشت.' ) );
	add_settings_field( 'items', __( 'فهرست خدمات (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snsvc', 'uid_section_snsvc_main', array( 'group' => 'uid_section_snsvc', 'key' => 'items', 'default' => implode( "\n", uid_default_sn_svc() ), 'rows' => 8 ) );

	/* ---------------- خطاهای رایج و راه‌حل ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snrisk', array( 'sanitize_callback' => 'uid_sanitize_section_snrisk', 'default' => array() ) );
	add_settings_section( 'uid_section_snrisk_main', '', '__return_false', 'uid_section_snrisk' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snrisk', 'uid_section_snrisk_main', array( 'group' => 'uid_section_snrisk', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snrisk', 'uid_section_snrisk_main', array( 'group' => 'uid_section_snrisk', 'key' => 'eyebrow', 'default' => 'چرا با تیم متخصص، نه تنها خودتان' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snrisk', 'uid_section_snrisk_main', array( 'group' => 'uid_section_snrisk', 'key' => 'heading', 'default' => 'چرا بیشتر خطاهای ثبت‌نام ثنا، با یک همراه متخصص از بین می‌رود' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snrisk', 'uid_section_snrisk_main', array( 'group' => 'uid_section_snrisk', 'key' => 'text', 'default' => 'این‌ها رایج‌ترین دلایلی هستند که ثبت‌نام شخصی افراد در سامانه ثنا با خطا مواجه می‌شود — و اینکه تیم یوآیدی چطور همان لحظه جلویش را می‌گیرد.' ) );
	add_settings_field( 'items', __( 'موارد خطا', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snrisk', 'uid_section_snrisk_main', array(
		'group' => 'uid_section_snrisk', 'key' => 'items', 'default' => uid_default_sn_risks(), 'add_label' => __( 'افزودن خطا', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان خطا', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'bad_text', 'type' => 'textarea', 'label' => __( 'توضیح خطا', 'uid-theme' ) ),
			array( 'key' => 'fix_text', 'type' => 'textarea', 'label' => __( 'راه‌حل یوآیدی', 'uid-theme' ) ),
		),
	) );

	/* ---------------- باند اعتماد ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_sntrust', array( 'sanitize_callback' => 'uid_sanitize_section_sntrust', 'default' => array() ) );
	add_settings_section( 'uid_section_sntrust_main', '', '__return_false', 'uid_section_sntrust' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_sntrust', 'uid_section_sntrust_main', array( 'group' => 'uid_section_sntrust', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_sntrust', 'uid_section_sntrust_main', array( 'group' => 'uid_section_sntrust', 'key' => 'eyebrow', 'default' => 'اعتماد شما، اولویت ماست' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_sntrust', 'uid_section_sntrust_main', array( 'group' => 'uid_section_sntrust', 'key' => 'heading', 'default' => 'یوآیدی، کارگزار مورد تایید سامانه‌های ثنا و سجام' ) );
	add_settings_field( 'cells', __( 'آمار (خانه‌های باند اعتماد)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_sntrust', 'uid_section_sntrust_main', array(
		'group' => 'uid_section_sntrust', 'key' => 'cells', 'default' => uid_default_sn_trust(), 'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'type', 'type' => 'select', 'label' => __( 'نوع', 'uid-theme' ), 'options' => array( 'text' => __( 'متن ثابت', 'uid-theme' ), 'count' => __( 'عدد شمارشی', 'uid-theme' ) ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- راه‌های پشتیبانی + تعهدنامه ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snteam', array( 'sanitize_callback' => 'uid_sanitize_section_snteam', 'default' => array() ) );
	add_settings_section( 'uid_section_snteam_main', '', '__return_false', 'uid_section_snteam' );
	add_settings_field( 'rows', __( 'راه‌های پشتیبانی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snteam', 'uid_section_snteam_main', array(
		'group' => 'uid_section_snteam', 'key' => 'rows', 'default' => uid_default_sn_team_rows(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون', 'uid-theme' ), 'options' => array( 'phone' => __( 'تماس', 'uid-theme' ), 'support' => __( 'پیام‌رسان', 'uid-theme' ), 'doc' => __( 'پیگیری', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pledge_heading', __( 'عنوان کادر تعهدنامه', 'uid-theme' ), 'uid_field_text', 'uid_section_snteam', 'uid_section_snteam_main', array( 'group' => 'uid_section_snteam', 'key' => 'pledge_heading', 'default' => 'تعهد تیم پشتیبانی یوآیدی' ) );
	add_settings_field( 'pledge_items', __( 'موارد تعهدنامه (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snteam', 'uid_section_snteam_main', array( 'group' => 'uid_section_snteam', 'key' => 'pledge_items', 'default' => implode( "\n", uid_default_sn_pledge_items() ), 'rows' => 4 ) );

	/* ---------------- هشدار پیامک جعلی ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snsecurity', array( 'sanitize_callback' => 'uid_sanitize_section_snsecurity', 'default' => array() ) );
	add_settings_section( 'uid_section_snsecurity_main', '', '__return_false', 'uid_section_snsecurity' );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snsecurity', 'uid_section_snsecurity_main', array( 'group' => 'uid_section_snsecurity', 'key' => 'heading', 'default' => 'هشدار: مراقب پیامک‌های جعلی باشید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snsecurity', 'uid_section_snsecurity_main', array( 'group' => 'uid_section_snsecurity', 'key' => 'text', 'default' => 'سامانه ثنا هیچ‌گونه پیامکی برای پرداخت وجه جهت مشاهده ابلاغیه قضایی ارسال نمی‌کند. دریافت وجه فقط هنگام انجام فرایند احراز هویت صورت می‌گیرد. اگر پیامکی از این دست دریافت کردید، جعلی است.' ) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snfaq', array( 'sanitize_callback' => 'uid_sanitize_section_snfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_snfaq_main', '', '__return_false', 'uid_section_snfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snfaq', 'uid_section_snfaq_main', array( 'group' => 'uid_section_snfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snfaq', 'uid_section_snfaq_main', array( 'group' => 'uid_section_snfaq', 'key' => 'eyebrow', 'default' => 'پرسش‌های پرتکرار' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snfaq', 'uid_section_snfaq_main', array( 'group' => 'uid_section_snfaq', 'key' => 'heading', 'default' => 'سوالات متداول درباره احراز هویت غیرحضوری ثنا' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_snfaq', 'uid_section_snfaq_main', array(
		'group' => 'uid_section_snfaq', 'key' => 'items', 'default' => uid_default_sn_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array( array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ), array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ) ),
	) );

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_sn_group', 'uid_section_snlead', array( 'sanitize_callback' => 'uid_sanitize_section_snlead', 'default' => array() ) );
	add_settings_section( 'uid_section_snlead_main', '', '__return_false', 'uid_section_snlead' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'eyebrow', 'default' => 'همین حالا شروع کنید' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'heading', 'default' => 'ثبت‌نام و احراز هویت سامانه ثنا را به تیم متخصص یوآیدی بسپارید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'text', 'default' => 'کمتر از ۵ دقیقه، با نصف هزینه‌ی حضوری، و بدون نگرانی از خطاهای رایج. همکار ما شماره‌تان را می‌گیرد و کل مسیر تا صدور کد ثنا را همراهی می‌کند.' ) );
	add_settings_field( 'trust', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'trust', 'default' => "کارگزار مورد تایید سامانه ثنا\n+۵٬۰۰۰٬۰۰۰ احراز هویت موفق\nپشتیبانی تا صدور کد ثنا" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'form_title', 'default' => 'ثبت‌نام سریع' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'form_hint', 'default' => 'فرم را پر کنید؛ کارشناس ما شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر تماس می‌گیرد.' ) );
	add_settings_field( 'topic_options', __( 'گزینه‌های موضوع درخواست (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'topic_options', 'default' => "ثبت‌نام و احراز هویت ثنا (داخل کشور)\nثبت‌نام و احراز هویت ثنا (خارج از کشور)\nسوال یا مشکل در ثبت‌نام قبلی" ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'submit_text', 'default' => 'ثبت‌نام و احراز هویت ثنا' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'success_title', 'default' => 'درخواست شما ثبت شد' ) );
	add_settings_field( 'success_text', __( 'پیام موفقیت — متن (قبل از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_snlead', 'uid_section_snlead_main', array( 'group' => 'uid_section_snlead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_sn_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_snhero( $input ) {
	return array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text'       => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'            => sanitize_textarea_field( $input['tags'] ?? '' ),
		'checklist_title' => sanitize_text_field( $input['checklist_title'] ?? '' ),
		'ready_items'     => uid_sanitize_repeater_rows( $input['ready_items'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_snurgency( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
	);
}

function uid_sanitize_section_snadv( $input ) {
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

function uid_sanitize_section_snflow( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'note'      => sanitize_text_field( $input['note'] ?? '' ),
	);
}

function uid_sanitize_section_snforeign( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'text'    => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn_text'=> sanitize_text_field( $input['btn_text'] ?? '' ),
		'btn_url' => sanitize_text_field( $input['btn_url'] ?? '' ),
	);
}

function uid_sanitize_section_snsvc( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => wp_kses( $input['text'] ?? '', array( 'span' => array( 'class' => true, 'style' => true ) ) ),
		'items'     => sanitize_textarea_field( $input['items'] ?? '' ),
	);
}

function uid_sanitize_section_snrisk( $input ) {
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

function uid_sanitize_section_sntrust( $input ) {
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

function uid_sanitize_section_snteam( $input ) {
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

function uid_sanitize_section_snsecurity( $input ) {
	return array(
		'heading' => sanitize_text_field( $input['heading'] ?? '' ),
		'text'    => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_snfaq( $input ) {
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

function uid_sanitize_section_snlead( $input ) {
	return array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => sanitize_text_field( $input['heading'] ?? '' ),
		'text'          => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust'         => sanitize_textarea_field( $input['trust'] ?? '' ),
		'form_title'    => sanitize_text_field( $input['form_title'] ?? '' ),
		'form_hint'     => sanitize_text_field( $input['form_hint'] ?? '' ),
		'topic_options' => sanitize_textarea_field( $input['topic_options'] ?? '' ),
		'submit_text'   => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'     => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title' => sanitize_text_field( $input['success_title'] ?? '' ),
		'success_text'  => sanitize_text_field( $input['success_text'] ?? '' ),
	);
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_sn_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'ثبت‌نام و احراز هویت سامانه ثنا', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی همین امروز تماس بگیرد و مرحله‌به‌مرحله احراز هویت ثنا را با شما انجام دهد.', 'uid-theme' ); ?></p>
	    <form id="modalForm" novalidate>
	<div class="cta-fields">
	  <div class="fld"><label for="m_name"><?php esc_html_e( 'نام و نام خانوادگی', 'uid-theme' ); ?></label>
	    <input id="m_name" name="name" type="text" placeholder="<?php esc_attr_e( 'مثلاً سارا محمدی', 'uid-theme' ); ?>" data-req></div>
	  <div class="fld"><label for="m_tel"><?php esc_html_e( 'شماره تماس', 'uid-theme' ); ?></label>
	    <input id="m_tel" name="phone" type="tel" inputmode="numeric" placeholder="09xxxxxxxxx" data-req data-tel>
	    <span class="err"><?php esc_html_e( 'شماره موبایل معتبر با فرمت ۰۹xxxxxxxxx وارد کنید.', 'uid-theme' ); ?></span></div>
	  <div class="fld"><label for="m_loc"><?php esc_html_e( 'محل سکونت', 'uid-theme' ); ?></label>
	    <select id="m_loc" name="location" data-req>
	      <option value=""><?php esc_html_e( 'انتخاب کنید…', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'داخل ایران', 'uid-theme' ); ?></option><option><?php esc_html_e( 'خارج از کشور', 'uid-theme' ); ?></option>
	    </select></div>
	  <button class="btn btn-cta btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'ارسال درخواست', 'uid-theme' ); ?></button>
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
