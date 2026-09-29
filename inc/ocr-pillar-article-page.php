<?php
/**
 * صفحه اختصاصی مقاله ستونی «OCR چیست؟» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی
 * واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «op» (OCR Pillar) نام‌گذاری شده‌اند تا در نام آپشن‌های
 * wp_options با سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * این صفحه از نظر ژانر با صفحات قبلی («درباره ما»، «تماس با ما») فرق دارد: یک
 * مقاله‌ی ستونی/آموزشی بلند است، نه صفحه فروش/معرفی خدمت. به همین دلیل، علاوه بر
 * ۱۰ سکشن محتوایی شماره‌دار (هیرو + کپسول ۳۰ثانیه‌ای + ۷ فصل + کلام‌آخر + پرسش‌وپاسخ
 * + بند تماس نهایی)، سه عنصر ساختاریِ ثابت هم دارد که سکشن محتوایی نیستند و در
 * رجیستری/چیدمان پیشخوان ظاهر نمی‌شوند — درست مثل uid_render_wsh_jumpbar() و
 * uid_wsh_folded_slugs()/uid_render_wsh_foldbar() در inc/web-services-hub-page.php:
 *   ۱) نوار پرش سریع (jumpbar) — روی موبایل، همراه با یک حلقه‌ی درصد مطالعه که
 *      مخصوص همین مقاله‌ی بلند است (نه چیزی مشترک با صفحات دیگر).
 *   ۲) فهرست مطالب دسکتاپ (toc) — فقط بالای ۹۰۰px نمایش داده می‌شود؛ روی موبایل
 *      فصل‌های تاخوردنی (فولد) جایگزین آن می‌شوند.
 *   ۳) نوار فصل‌های تاخوردنی (foldbar) + خودِ سیستم تاخوردن، که ۷ فصل شماره‌دار
 *      (opwhat..opsecurity) را در بر می‌گیرد؛ «کلام آخر»، «پرسش‌وپاسخ» و «بند تماس
 *      نهایی» عمداً بیرون از این سیستم‌اند تا همیشه در دسترس بمانند.
 *
 * سه بلوک تعاملی «امضادار» این مقاله — شبیه‌ساز مراحل OCR (پایپ‌لاین ۶مرحله‌ای در
 * فصل «چگونه کار می‌کند»)، شبیه‌ساز دقت (فصل «عوامل دقت») و انتخابگر/جدول مقایسه
 * پنج نوع OCR (فصل «انواع OCR») — کاملاً به ساختار CSS/JS این صفحه گره خورده‌اند
 * (data-step/data-t/data-f و انتخابگرهای CSS مثل ‎.pipe[data-st="N"]‎) و از پیشخوان
 * قابل‌ویرایش نیستند؛ دقیقاً مثل مسیریاب ثابت uid_cu_router_routes() در
 * inc/contact-us-page.php. برای هرکدام یک یادداشت (uid_field_notice) در تنظیمات
 * قرار داده شده است.
 *
 * مودال درخواست مشاوره (#modal) هم — درست مثل uid_render_ab_quick_modal() در
 * inc/about-us-page.php — کاملاً ثابت است و از پیشخوان قابل‌ویرایش نیست؛ باز/بسته
 * شدنش را assets/js/main.js به‌صورت سراسری مدیریت می‌کند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_OP_TEMPLATE', 'template-ocr-pillar-article.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_op_sections_registry() {
	return array(
		'ophero'     => array( 'label' => __( 'هیرو مقاله (عنوان، لید، متادیتا)', 'uid-theme' ),        'icon' => 'dashicons-media-document' ),
		'optldr'     => array( 'label' => __( 'کپسول ۳۰ ثانیه‌ای (ویژه موبایل)', 'uid-theme' ),          'icon' => 'dashicons-smartphone' ),
		'opwhat'     => array( 'label' => __( 'فصل ۱ — OCR مخفف چیست', 'uid-theme' ),                   'icon' => 'dashicons-editor-help' ),
		'ophow'      => array( 'label' => __( 'فصل ۲ — OCR چگونه کار می‌کند (شبیه‌ساز مراحل)', 'uid-theme' ), 'icon' => 'dashicons-randomize' ),
		'opaccuracy' => array( 'label' => __( 'فصل ۳ — عوامل موثر بر دقت (شبیه‌ساز دقت)', 'uid-theme' ), 'icon' => 'dashicons-chart-bar' ),
		'opoutput'   => array( 'label' => __( 'فصل ۴ — قابلیت و خروجی OCR', 'uid-theme' ),               'icon' => 'dashicons-media-spreadsheet' ),
		'optypes'    => array( 'label' => __( 'فصل ۵ — انواع OCR (انتخابگر + جدول مقایسه)', 'uid-theme' ), 'icon' => 'dashicons-grid-view' ),
		'opdaily'    => array( 'label' => __( 'فصل ۶ — کاربرد در زندگی روزمره', 'uid-theme' ),           'icon' => 'dashicons-admin-users' ),
		'opsecurity' => array( 'label' => __( 'فصل ۷ — امنیت، KYC و احراز هویت دیجیتال', 'uid-theme' ),  'icon' => 'dashicons-shield' ),
		'opfinal'    => array( 'label' => __( 'کلام آخر', 'uid-theme' ),                                 'icon' => 'dashicons-format-quote' ),
		'opfaq'      => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                            'icon' => 'dashicons-editor-help' ),
		'oplead'     => array( 'label' => __( 'بند تماس نهایی (فرم مشاوره)', 'uid-theme' ),              'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_op_layout() {
	$registry = uid_op_sections_registry();
	$saved    = get_option( 'uid_op_layout', array() );

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

function uid_sanitize_op_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_op_sections_registry();
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
 * اسلاگ فصل‌هایی که سیستم «فصل تاخوردنی» موبایل رویشان اعمال می‌شود — دقیقاً
 * همان ۷ فصل شماره‌دار مقاله (بدون هیرو/کپسول/کلام‌آخر/پرسش‌وپاسخ/بند تماس).
 */
function uid_op_folded_slugs() {
	return array( 'opwhat', 'ophow', 'opaccuracy', 'opoutput', 'optypes', 'opdaily', 'opsecurity' );
}

/**
 * اسلاگ فصل‌هایی که در mockup ویژگی data-fold-hot دارند (نقطه داغ کوچک روی
 * ردیف تاخورده موبایل + فیلتر «فقط بخش‌های ضروری»).
 */
function uid_op_hot_slugs() {
	return array( 'opwhat', 'ophow', 'opaccuracy', 'optypes', 'opsecurity' );
}

/**
 * حداقل زمان مطالعه هر فصل (برچسب data-fold-min روی ردیف تاخورده موبایل).
 */
function uid_op_fold_min( $slug ) {
	$map = array(
		'opwhat'     => __( '۲ دقیقه', 'uid-theme' ),
		'ophow'      => __( '۳ دقیقه', 'uid-theme' ),
		'opaccuracy' => __( '۳ دقیقه', 'uid-theme' ),
		'opoutput'   => __( '۲ دقیقه', 'uid-theme' ),
		'optypes'    => __( '۳ دقیقه', 'uid-theme' ),
		'opdaily'    => __( '۲ دقیقه', 'uid-theme' ),
		'opsecurity' => __( '۳ دقیقه', 'uid-theme' ),
	);
	return $map[ $slug ] ?? '';
}

/**
 * عنوان/چکیده فصل تاخورده — عناوینِ ثابتِ ساختاریِ ردیفِ تاخوردهٔ موبایل (متمایز
 * از عنوان h2 قابل‌ویرایش خودِ فصل)؛ دقیقاً هم‌ارز attributeهای data-fold-title/
 * data-fold-teaser در mockup.
 */
function uid_op_fold_meta( $slug ) {
	$map = array(
		'opwhat'     => array( 'title' => __( 'OCR مخفف چیست و دقیقاً چه کاری می‌کند', 'uid-theme' ), 'teaser' => __( 'تعریف، معادل فارسی و مرزی که با «تبدیل عکس به متن» دارد', 'uid-theme' ) ),
		'ophow'      => array( 'title' => __( 'شش مرحله‌ای که تصویر را به متن تبدیل می‌کند', 'uid-theme' ), 'teaser' => __( 'از دریافت تصویر تا اصلاح خطا — مرحله‌به‌مرحله روی یک فاکتور', 'uid-theme' ) ),
		'opaccuracy' => array( 'title' => __( 'چه چیزی دقت OCR را بالا و پایین می‌برد', 'uid-theme' ), 'teaser' => __( 'هشت عامل عمومی، به‌علاوه چهار عامل ویژه زبان فارسی', 'uid-theme' ) ),
		'opoutput'   => array( 'title' => __( 'خروجی OCR چه شکلی است و چه مسائلی را حل می‌کند', 'uid-theme' ), 'teaser' => __( 'از متن خام تا داده ساختاریافته و مختصات کلمات در تصویر', 'uid-theme' ) ),
		'optypes'    => array( 'title' => __( 'پنج نوع OCR و اینکه کدام به کار شما می‌آید', 'uid-theme' ), 'teaser' => __( 'چاپی، دست‌نویس، ابری، آفلاین و سندمحور — همراه با مقایسه', 'uid-theme' ) ),
		'opdaily'    => array( 'title' => __( 'کاربرد OCR در زندگی روزمره', 'uid-theme' ), 'teaser' => __( 'تبدیل عکس به متن، PDF اسکن‌شده، جست‌وجو، ترجمه و دسترس‌پذیری', 'uid-theme' ) ),
		'opsecurity' => array( 'title' => __( 'نقش OCR در امنیت، KYC و احراز هویت دیجیتال', 'uid-theme' ), 'teaser' => __( 'خواندن مدارک، استخراج از فرم‌ها و جای OCR در تایید غیرحضوری', 'uid-theme' ) ),
	);
	return $map[ $slug ] ?? array( 'title' => '', 'teaser' => '' );
}

function uid_render_op_sections() {
	uid_render_op_jumpbar();

	$layout     = uid_get_op_layout();
	$fold_slugs = uid_op_folded_slugs();
	$fold_done  = false; // «فهرست مطالب» فقط یک‌بار، پیش از اولین فصل تاخوردنی رندر شود
	$fold_open  = false; // آیا در حال حاضر داخل #chapters هستیم

	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$slug    = $row['slug'];
		$is_fold = in_array( $slug, $fold_slugs, true );

		if ( $is_fold && ! $fold_done ) {
			uid_render_op_toc();
			echo '<div id="chapters" class="fold-run">';
			uid_render_op_foldbar();
			$fold_done = true;
			$fold_open = true;
		} elseif ( ! $is_fold && $fold_open ) {
			// اولین سکشن غیرِتاخوردنی بعد از فصل‌ها (مثلاً «کلام آخر») — #chapters
			// باید همین‌جا بسته شود تا این سکشن‌ها بیرون از فولد و همیشه در دسترس بمانند.
			echo '</div>';
			$fold_open = false;
		}

		$fn = 'uid_render_section_' . $slug;
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
	if ( $fold_open ) {
		echo '</div>';
	}
}

/* =====================================================================
 * عناصر ساختاری ثابت (سکشن محتوایی نیستند؛ در رجیستری نمی‌آیند)
 * ===================================================================== */

/**
 * نوار «پرش سریع به بخش‌ها» — فقط زیر ۹۰۰px نمایش داده می‌شود؛ برخلاف نمونه‌ی
 * uid_render_wsh_jumpbar()، اینجا یک حلقهٔ دایره‌ای درصدِ مطالعه هم دارد (jbProg/
 * jbRing/jbPct) که با اسکرول صفحه توسط assets/js/ocr-pillar-article-page.js پر
 * می‌شود — چیزی مخصوص یک مقالهٔ بلند، نه یک الگوی مشترک با صفحات دیگر.
 */
function uid_render_op_jumpbar() {
	?>
	<div class="jumpbar" id="jumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jb-row">
	    <div class="jb-prog" id="jbProg" title="<?php esc_attr_e( 'میزان مطالعه‌شده', 'uid-theme' ); ?>">
	      <svg viewBox="0 0 36 36" aria-hidden="true">
	        <circle class="tr" cx="18" cy="18" r="15.5"></circle>
	        <circle class="bar" id="jbRing" cx="18" cy="18" r="15.5"></circle>
	      </svg>
	      <b id="jbPct">۰</b>
	    </div>
	    <div class="jump-rail" id="jumpRail">
	      <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	      <button class="jump-chip" type="button" data-jump="#chapters"><?php esc_html_e( 'فهرست بخش‌ها', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#how"><?php esc_html_e( 'مراحل کار OCR', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#accuracy"><?php esc_html_e( 'عوامل دقت', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#types"><?php esc_html_e( 'انواع OCR', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#daily"><?php esc_html_e( 'کاربردها', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#security"><?php esc_html_e( 'امنیت و احراز هویت', 'uid-theme' ); ?></button>
	      <button class="jump-chip" type="button" data-jump="#faq"><?php esc_html_e( 'سوالات متداول', 'uid-theme' ); ?></button>
	    </div>
	  </div>
	</div>
	<?php
}

/**
 * فهرست مطالب دسکتاپ — فقط بالای ۹۰۰px نمایش داده می‌شود؛ روی موبایل سیستم فصل
 * تاخوردنی زیر جایگزین آن است. لنگرها به همان ۷ فصل + پرسش‌وپاسخ اشاره می‌کنند و
 * برچسب‌ها ثابت‌اند (هم‌راستا با عنوان هر فصل).
 */
function uid_render_op_toc() {
	?>
	<div class="op-wrap" style="padding-block:clamp(26px,3.6vw,44px) 0">
	  <nav class="toc rv" aria-label="<?php esc_attr_e( 'فهرست مطالب', 'uid-theme' ); ?>">
	    <div class="toc-hd">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13M3.5 6h.01M3.5 12h.01M3.5 18h.01"/></svg>
	      <?php esc_html_e( 'فهرست مطالب', 'uid-theme' ); ?>
	    </div>
	    <a href="#what"><i>۱</i><span><?php esc_html_e( 'OCR مخفف چیست', 'uid-theme' ); ?></span></a>
	    <a href="#how"><i>۲</i><span><?php esc_html_e( 'OCR چگونه کار می‌کند', 'uid-theme' ); ?></span></a>
	    <a href="#accuracy"><i>۳</i><span><?php esc_html_e( 'چه عواملی بر دقت OCR اثر می‌گذارند', 'uid-theme' ); ?></span></a>
	    <a href="#output"><i>۴</i><span><?php esc_html_e( 'قابلیت OCR و خروجی آن', 'uid-theme' ); ?></span></a>
	    <a href="#types"><i>۵</i><span><?php esc_html_e( 'انواع OCR و مقایسه آن‌ها', 'uid-theme' ); ?></span></a>
	    <a href="#daily"><i>۶</i><span><?php esc_html_e( 'کاربرد OCR در زندگی روزمره', 'uid-theme' ); ?></span></a>
	    <a href="#security"><i>۷</i><span><?php esc_html_e( 'OCR در امنیت و احراز هویت', 'uid-theme' ); ?></span></a>
	    <a href="#faq"><i>۸</i><span><?php esc_html_e( 'سوالات متداول', 'uid-theme' ); ?></span></a>
	  </nav>
	</div>
	<?php
}

/**
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود.
 * نوار پیشرفت مطالعه (fold-meter) توسط assets/js/ocr-pillar-article-page.js پر
 * می‌شود؛ دقیقاً مثل uid_render_wsh_foldbar().
 */
function uid_render_op_foldbar() {
	$folded = uid_op_folded_slugs();
	$layout = uid_get_op_layout();
	$count  = 0;
	foreach ( $layout as $row ) {
		if ( ! empty( $row['enabled'] ) && in_array( $row['slug'], $folded, true ) ) $count++;
	}
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

/**
 * پوستهٔ مشترک هر فصلِ تاخوردنی — بازکنندهٔ ‎<section data-fold ...>‎ و بستهٔ
 * ‎</div></div></div></section>‎ حول محتوای واقعی فصل، برای پرهیز از تکرار در
 * هر uid_render_section_op*(). $eyebrow_num مثلاً «بخش ۱» است.
 */
function uid_op_fold_open( $slug, $eyebrow_num ) {
	$meta = uid_op_fold_meta( $slug );
	$anchor = array(
		'opwhat'     => 'what',
		'ophow'      => 'how',
		'opaccuracy' => 'accuracy',
		'opoutput'   => 'output',
		'optypes'    => 'types',
		'opdaily'    => 'daily',
		'opsecurity' => 'security',
	);
	$hot = in_array( $slug, uid_op_hot_slugs(), true );
	printf(
		'<section class="sec" id="%1$s" data-fold%2$s data-fold-min="%3$s" data-fold-title="%4$s" data-fold-teaser="%5$s"><div class="fold-body"><div class="op-wrap"><div class="fold-inner">',
		esc_attr( $anchor[ $slug ] ?? $slug ),
		$hot ? ' data-fold-hot' : '',
		esc_attr( uid_op_fold_min( $slug ) ),
		esc_attr( $meta['title'] ),
		esc_attr( $meta['teaser'] )
	);
	echo '<div class="col"><div class="sec-head rule"><span class="op-eyebrow"><i></i>' . esc_html( $eyebrow_num ) . '</span>';
}
function uid_op_fold_close() {
	echo '</div></div></div></section>';
}

/* =====================================================================
 * آیکون کوچک اشتراکی این صفحه — فقط علامت تیک ✓، که ده‌ها بار در فهرست‌های
 * فصل‌های مختلف تکرار می‌شود؛ بقیهٔ آیکون‌ها هرکدام فقط یک‌بار استفاده می‌شوند و
 * مستقیم در همان‌جا نوشته شده‌اند.
 * ===================================================================== */
function uid_op_ic_check() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}

/**
 * مجموعه تگ‌های مجاز برای متن‌های غنیِ ویرایش‌پذیر (تگ span‌های لاتین OCR/API/PDF،
 * تاکید، لینک درون‌متنی) — نظیر همان چیزی که در contact-us-page.php برای
 * heading/answer استفاده شده.
 */
function uid_op_kses_rich() {
	return array(
		'span'   => array( 'class' => true ),
		'b'      => array(),
		'strong' => array(),
		'br'     => array(),
		'mark'   => array(),
		'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
	);
}

/* =====================================================================
 * ۱) هیرو مقاله
 * ===================================================================== */
function uid_render_section_ophero() {
	$tag = uid_section_tag( 'ophero', 'h1' );
	?>
	<div class="op-wrap">
	  <nav class="crumb" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'uid-theme' ); ?>">
	    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a><span class="sep">/</span>
	    <a href="https://blog.u-id.net/"><?php esc_html_e( 'وبلاگ', 'uid-theme' ); ?></a><span class="sep">/</span>
	    <b><?php echo esc_html( uid_section_val( 'ophero', 'crumb_current', __( 'OCR چیست؟', 'uid-theme' ) ) ); ?></b>
	  </nav>
	</div>

	<header class="ahero" id="top">
	  <div class="op-wrap">
	    <div class="ahero-in">
	      <span class="op-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'ophero', 'eyebrow', __( 'راهنمای جامع فناوری', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . '>'; ?><?php echo wp_kses( uid_section_val( 'ophero', 'heading', __( '<span class="lat">OCR</span> چیست؟ آشنایی با فناوری تشخیص متن از تصویر', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo wp_kses( uid_section_val( 'ophero', 'lede', __( 'بعد از دیدن یک عکس، اسکن یا حتی تصویر یک کارت شناسایی، احتمالاً برایتان سؤال شده که سیستم‌ها چگونه می‌توانند متن داخل آن را بخوانند و به داده‌ای قابل استفاده تبدیل کنند؛ اینجاست که می‌فهمیم <span class="lat">OCR</span> چیست و چرا این فناوری به یکی از ابزارهای مهم دنیای دیجیتال تبدیل شده است. در این مقاله می‌بینیم <span class="lat">OCR</span> مخفف چیست، چگونه کار می‌کند، چه انواعی دارد، در زندگی روزمره و فرایندهای امنیت و احراز هویت چه نقشی دارد و دقت آن به چه چیزهایی وابسته است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	      <div class="ameta">
	        <span class="mi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 7v5.2l3.4 2"/></svg><b><?php echo esc_html( uid_section_val( 'ophero', 'meta_read_min', '۱۴' ) ); ?></b> <?php esc_html_e( 'دقیقه مطالعه', 'uid-theme' ); ?></span>
	        <span class="mi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/></svg><b><?php echo esc_html( uid_section_val( 'ophero', 'meta_sections', '۷' ) ); ?></b> <?php esc_html_e( 'بخش', 'uid-theme' ); ?></span>
	        <span class="mi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/></svg><?php echo esc_html( uid_section_val( 'ophero', 'meta_updated', __( 'به‌روزرسانی: شهریور ۱۴۰۵', 'uid-theme' ) ) ); ?></span>
	        <span class="mi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.8-7 10-7 10 7 10 7-3.8 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg><?php echo esc_html( uid_section_val( 'ophero', 'meta_level', __( 'سطح: مقدماتی تا متوسط', 'uid-theme' ) ) ); ?></span>
	      </div>

	      <div class="kstrip">
	        <div class="kcard rv">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5.5A1.5 1.5 0 015.5 4H9M20 7V5.5A1.5 1.5 0 0018.5 4H15M4 17v1.5A1.5 1.5 0 005.5 20H9M20 17v1.5a1.5 1.5 0 01-1.5 1.5H15"/><path d="M8 12h8"/></svg></span>
	          <b><?php echo esc_html( uid_section_val( 'ophero', 'kcard1_title', __( 'معنی و معادل فارسی', 'uid-theme' ) ) ); ?></b>
	          <span><?php echo wp_kses( uid_section_val( 'ophero', 'kcard1_text', __( '<span class="lat">Optical Character Recognition</span> — تشخیص نوری کاراکتر', 'uid-theme' ) ), uid_op_kses_rich() ); ?></span>
	        </div>
	        <div class="kcard rv rv-d1">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l2.5-7 4 14 2.5-7h5"/></svg></span>
	          <b><?php echo esc_html( uid_section_val( 'ophero', 'kcard2_title', __( 'شش مرحله پردازش', 'uid-theme' ) ) ); ?></b>
	          <span><?php echo esc_html( uid_section_val( 'ophero', 'kcard2_text', __( 'از دریافت تصویر تا بازبینی و اصلاح خطاها', 'uid-theme' ) ) ); ?></span>
	        </div>
	        <div class="kcard rv rv-d2">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2.5"/><path d="M7 9h5M7 13h9M7 17h4"/></svg></span>
	          <b><?php echo esc_html( uid_section_val( 'ophero', 'kcard3_title', __( 'پنج نوع OCR', 'uid-theme' ) ) ); ?></b>
	          <span><?php echo esc_html( uid_section_val( 'ophero', 'kcard3_text', __( 'چاپی، دست‌نویس، ابری، آفلاین و سندمحور', 'uid-theme' ) ) ); ?></span>
	        </div>
	        <div class="kcard rv rv-d3">
	          <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></span>
	          <b><?php echo esc_html( uid_section_val( 'ophero', 'kcard4_title', __( 'جای OCR در KYC', 'uid-theme' ) ) ); ?></b>
	          <span><?php echo wp_kses( uid_section_val( 'ophero', 'kcard4_text', __( 'خواندن مدارک در احراز هویت غیرحضوری', 'uid-theme' ) ), uid_op_kses_rich() ); ?></span>
	        </div>
	      </div>
	    </div>
	  </div>
	</header>
	<?php
}

/* =====================================================================
 * ۲) کپسول ۳۰ ثانیه‌ای (ویژه موبایل)
 * ===================================================================== */
function uid_default_op_tldr_items() {
	return array(
		__( '<b>OCR</b> مخفف <span class="lat">Optical Character Recognition</span> است: خواندن متنِ داخل تصویر و تبدیل آن به متن قابل ویرایش و جست‌وجو.', 'uid-theme' ),
		__( 'کار آن در <b>شش مرحله</b> انجام می‌شود: دریافت تصویر، بهبود کیفیت، شناسایی نواحی متنی، تشخیص حروف، تبدیل به متن دیجیتال و اصلاح خطا.', 'uid-theme' ),
		__( 'دقت آن ثابت نیست: <b>کیفیت تصویر، زاویه، رزولوشن، فونت و پس‌زمینه</b> و در فارسی اتصال حروف و تفاوت‌های نقطه‌ای آن را جابه‌جا می‌کنند.', 'uid-theme' ),
		__( 'در <b>احراز هویت غیرحضوری</b>، OCR همان حلقه‌ای است که مدرک فیزیکی را به داده قابل استعلام تبدیل می‌کند.', 'uid-theme' ),
	);
}
function uid_render_section_optldr() {
	$items_raw = uid_section_val( 'optldr', 'items', implode( "\n", uid_default_op_tldr_items() ) );
	$items     = array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) );
	?>
	<div class="op-wrap" style="padding-block:18px 4px">
	  <div class="tldr">
	    <div class="tldr-hd">
	      <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.5 13.5H11l-1 8.5 8.5-11.5H12z"/></svg></span>
	      <b><?php echo esc_html( uid_section_val( 'optldr', 'heading', __( 'خلاصه ۳۰ ثانیه‌ای', 'uid-theme' ) ) ); ?></b><span><?php echo esc_html( uid_section_val( 'optldr', 'sub', __( 'اگر وقت ندارید', 'uid-theme' ) ) ); ?></span>
	    </div>
	    <?php if ( $items ) : ?>
	    <ul class="tldr-list">
	      <?php foreach ( $items as $item ) : ?>
	      <li><?php echo uid_op_ic_check(); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?>
	        <span><?php echo wp_kses( $item, uid_op_kses_rich() ); ?></span></li>
	      <?php endforeach; ?>
	    </ul>
	    <?php endif; ?>
	    <div class="tldr-acts">
	      <a class="op-btn op-btn-cta op-btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" data-track="call_click"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <?php echo esc_html( uid_section_val( 'optldr', 'call_btn_text', __( 'مشاوره احراز هویت:', 'uid-theme' ) ) ); ?> <span class="mono"><?php echo esc_html( uid_phone_raw() ); ?></span></a>
	      <a class="op-btn op-btn-ghost-d op-btn-block" href="#how"><?php echo esc_html( uid_section_val( 'optldr', 'ghost_btn_text', __( 'رفتن به مراحل کار OCR', 'uid-theme' ) ) ); ?></a>
	    </div>
	    <button class="tldr-more" type="button" data-jump-soft="#chapters">
	      <?php echo esc_html( uid_section_val( 'optldr', 'more_btn_text', __( 'فهرست کامل بخش‌ها', 'uid-theme' ) ) ); ?>
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
	    </button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * ۳) فصل ۱ — OCR مخفف چیست
 * ===================================================================== */
function uid_render_section_opwhat() {
	uid_op_fold_open( 'opwhat', uid_section_val( 'opwhat', 'eyebrow', __( 'بخش ۱', 'uid-theme' ) ) );
	$tag = uid_section_tag( 'opwhat', 'h2' );
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opwhat', 'heading', __( '<span class="lat">OCR</span> مخفف چیست', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'opwhat', 'p1', __( '<span class="lat">OCR</span> مخفف <span class="lat">Optical Character Recognition</span> است؛ اصطلاحی که در فارسی معمولاً به «تشخیص نوری کاراکتر» یا «تشخیص متن از تصویر» ترجمه می‌شود. این فناوری به سیستم‌ها کمک می‌کند متن موجود در تصاویر، فایل‌های اسکن‌شده، <span class="lat">PDF</span>های تصویری و حتی برخی دست‌نوشته‌ها را شناسایی کرده و آن را به متنی قابل ویرایش، جست‌وجو و پردازش تبدیل کنند. به زبان ساده، اگر بخواهیم بدانیم <span class="lat">OCR</span> چیست، باید بگوییم <span class="lat">OCR</span> پلی میان تصویر و متن دیجیتال است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <p class="pull"><?php echo wp_kses( uid_section_val( 'opwhat', 'pull', __( 'تا وقتی متن داخل یک عکس است، فقط دیده می‌شود؛ بعد از <span class="lat">OCR</span> می‌شود آن را جست‌وجو کرد، ویرایش کرد و به سیستم‌های دیگر فرستاد.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <p><?php echo wp_kses( uid_section_val( 'opwhat', 'p2', __( 'در بسیاری از منابع، <span class="lat">OCR</span> فقط به‌عنوان ابزاری برای تبدیل عکس به متن معرفی می‌شود؛ اما مفهوم آن گسترده‌تر است. این فناوری در کسب‌وکارها، سازمان‌ها، اپلیکیشن‌های موبایل و حتی فرایندهای امنیتی استفاده می‌شود تا اطلاعات متنی سریع‌تر و دقیق‌تر از روی اسناد استخراج شوند. همین تفاوت است که باعث می‌شود <span class="lat">OCR</span> در یک تلفن همراه یک ابزار کوچک به نظر برسد و در یک بانک یا صرافی، بخشی از زیرساخت.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <div class="callout plain">
	    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
	    <p><?php echo wp_kses( uid_section_val( 'opwhat', 'callout_text', __( '<b>یک تفکیک ساده:</b> «تبدیل عکس به متن» نتیجه‌ای است که کاربر می‌بیند؛ <span class="lat">OCR</span> فرایندی است که آن نتیجه را می‌سازد. هر ابزار تبدیل عکس به متن روی <span class="lat">OCR</span> بنا شده، اما هر <span class="lat">OCR</span>ی فقط برای این کار ساخته نشده است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	  </div>
	</div>
	<?php
	echo '</div>';
	uid_op_fold_close();
}

/* =====================================================================
 * ۴) فصل ۲ — OCR چگونه کار می‌کند (شبیه‌ساز پایپ‌لاین شش‌مرحله‌ای)
 * شبیه‌ساز کاملاً ثابت است (پیوندخورده به data-step/‎.pipe[data-st]‎ در
 * assets/css/ocr-pillar-article-page.css و assets/js/ocr-pillar-article-page.js)
 * و از پیشخوان قابل‌ویرایش نیست — دقیقاً مثل مسیریاب ثابت صفحه تماس با ما.
 * ===================================================================== */
function uid_render_section_ophow() {
	uid_op_fold_open( 'ophow', uid_section_val( 'ophow', 'eyebrow', __( 'بخش ۲', 'uid-theme' ) ) );
	$tag = uid_section_tag( 'ophow', 'h2' );
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'ophow', 'heading', __( '<span class="lat">OCR</span> چگونه کار می‌کند؟', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'ophow', 'p1', __( '<span class="lat">OCR</span> با تحلیل تصویر و شناسایی الگوهای حروف، متن موجود در عکس یا سند اسکن‌شده را به متن دیجیتال تبدیل می‌کند. این فرایند فقط خواندن ظاهری حروف نیست، بلکه شامل چند مرحله برای تشخیص دقیق‌تر، اصلاح خطا و بازسازی ساختار متن هم می‌شود. فرایند کار <span class="lat">OCR</span> معمولاً به‌صورت مرحله‌به‌مرحله انجام می‌شود تا متن از دل تصویر استخراج شود؛ هرچه کیفیت این مراحل بهتر باشد، خروجی نهایی هم دقیق‌تر خواهد بود.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	  <p><?php echo wp_kses( uid_section_val( 'ophow', 'p2', __( 'مثلاً اگر از یک فاکتور عکس بگیرید، <span class="lat">OCR</span> ابتدا کیفیت عکس را بهتر می‌کند، بعد ناحیه‌های متنی را پیدا می‌کند و در نهایت اطلاعاتی مثل نام کالا، مبلغ و تاریخ را استخراج می‌کند. پایین، همین اتفاق روی یک فاکتور نمونه مرحله‌به‌مرحله اجرا شده است — روی هر مرحله بزنید تا ببینید در آن لحظه چه چیزی سر تصویر می‌آید.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	</div>
	</div>

	<div class="col wide" style="margin-block-start:26px">
	  <div class="pipe" id="pipe" data-st="1">
	    <div class="pipe-hd">
	      <span class="dot"></span>
	      <b><?php esc_html_e( 'مراحل اصلی پردازش', 'uid-theme' ); ?> <span class="lat">OCR</span> — <?php esc_html_e( 'روی یک فاکتور نمونه', 'uid-theme' ); ?></b>
	      <span class="sp">
	        <button type="button" id="pipePlay">
	          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5l11 7-11 7z"/></svg>
	          <span><?php esc_html_e( 'پخش خودکار', 'uid-theme' ); ?></span></button>
	        <button type="button" id="pipeReset" aria-label="<?php esc_attr_e( 'بازگشت به مرحله اول', 'uid-theme' ); ?>">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8"/><path d="M3 4v4h4"/></svg>
	          <span><?php esc_html_e( 'از ابتدا', 'uid-theme' ); ?></span></button>
	      </span>
	    </div>

	    <div class="pipe-steps" role="tablist" aria-label="<?php esc_attr_e( 'مراحل پردازش OCR', 'uid-theme' ); ?>">
	      <button class="pipe-step on" type="button" role="tab" aria-selected="true" data-step="1"><span class="n">۱</span><span class="t"><?php esc_html_e( 'دریافت تصویر', 'uid-theme' ); ?></span></button>
	      <button class="pipe-step" type="button" role="tab" aria-selected="false" data-step="2"><span class="n">۲</span><span class="t"><?php esc_html_e( 'بهبود کیفیت', 'uid-theme' ); ?></span></button>
	      <button class="pipe-step" type="button" role="tab" aria-selected="false" data-step="3"><span class="n">۳</span><span class="t"><?php esc_html_e( 'نواحی متنی', 'uid-theme' ); ?></span></button>
	      <button class="pipe-step" type="button" role="tab" aria-selected="false" data-step="4"><span class="n">۴</span><span class="t"><?php esc_html_e( 'تشخیص حروف', 'uid-theme' ); ?></span></button>
	      <button class="pipe-step" type="button" role="tab" aria-selected="false" data-step="5"><span class="n">۵</span><span class="t"><?php esc_html_e( 'متن دیجیتال', 'uid-theme' ); ?></span></button>
	      <button class="pipe-step" type="button" role="tab" aria-selected="false" data-step="6"><span class="n">۶</span><span class="t"><?php esc_html_e( 'اصلاح خطا', 'uid-theme' ); ?></span></button>
	    </div>

	    <div class="pipe-body">
	      <div class="pipe-stage">
	        <div class="pst" data-i="1">
	          <span class="st-n"><?php esc_html_e( 'مرحله ۱ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'دریافت تصویر یا سند', 'uid-theme' ); ?></h4>
	          <p><?php echo wp_kses( __( 'ورودی <span class="lat">OCR</span> می‌تواند یک عکس موبایلی، فایل اسکن‌شده، <span class="lat">PDF</span> تصویری، کارت شناسایی یا فرم اداری باشد. در این نمونه، ورودی یک عکس موبایلی از فاکتور است — کج، کم‌نور و کمی تار، دقیقاً همان چیزی که در عمل به سامانه می‌رسد.', 'uid-theme' ), uid_op_kses_rich() ); ?></p>
	        </div>
	        <div class="pst" data-i="2" hidden>
	          <span class="st-n"><?php esc_html_e( 'مرحله ۲ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'بهبود کیفیت تصویر', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'سیستم ابتدا تصویر را از نظر نور، کنتراست، وضوح، چرخش، نویز و زاویه بررسی و اصلاح می‌کند تا متن خواناتر شود. تصویر صاف می‌شود، نویز حذف می‌شود و کنتراست بالا می‌رود؛ هنوز هیچ حرفی خوانده نشده است.', 'uid-theme' ); ?></p>
	        </div>
	        <div class="pst" data-i="3" hidden>
	          <span class="st-n"><?php esc_html_e( 'مرحله ۳ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'شناسایی نواحی متنی', 'uid-theme' ); ?></h4>
	          <p><?php echo wp_kses( __( 'در این مرحله، <span class="lat">OCR</span> تشخیص می‌دهد کدام بخش‌های تصویر شامل متن هستند و کدام بخش‌ها تصویر، لوگو یا پس‌زمینه‌اند. هر ناحیه‌ای که متن دارد جدا می‌شود تا بقیه تصویر بی‌دلیل پردازش نشود.', 'uid-theme' ), uid_op_kses_rich() ); ?></p>
	        </div>
	        <div class="pst" data-i="4" hidden>
	          <span class="st-n"><?php esc_html_e( 'مرحله ۴ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'تشخیص حروف و کلمات', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'نرم‌افزار با استفاده از الگوهای از پیش‌آموخته‌شده یا مدل‌های هوش مصنوعی، حروف، اعداد و نمادها را شناسایی می‌کند. اینجا همان جایی است که ویژگی‌های زبان فارسی — چسبیدگی حروف و تفاوت‌های نقطه‌ای — بیشترین اثر را می‌گذارند.', 'uid-theme' ); ?></p>
	        </div>
	        <div class="pst" data-i="5" hidden>
	          <span class="st-n"><?php esc_html_e( 'مرحله ۵ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'تبدیل به متن دیجیتال', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'داده‌های تصویری به متن قابل کپی، جست‌وجو و ویرایش تبدیل می‌شوند. توجه کنید که خروجی این مرحله هنوز خام است: دو خطای متداول فارسی — «ى» عربی به‌جای «ی» و جابه‌جایی رقم — در متن باقی مانده‌اند.', 'uid-theme' ); ?></p>
	        </div>
	        <div class="pst" data-i="6" hidden>
	          <span class="st-n"><?php esc_html_e( 'مرحله ۶ از ۶', 'uid-theme' ); ?></span>
	          <h4><?php esc_html_e( 'بازبینی و اصلاح خطاها', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'برخی سیستم‌های پیشرفته با کمک فرهنگ لغات، مدل زبانی یا ساختار سند، خطاهای احتمالی را کاهش می‌دهند. چون سند یک فاکتور است، ساختار آن هم کمک می‌کند: عددی که جلوی «مبلغ کل» می‌آید باید مبلغ باشد، نه یک رشته تصادفی.', 'uid-theme' ); ?></p>
	        </div>

	        <div class="pipe-out">
	          <div class="oh">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
	            <?php esc_html_e( 'متن استخراج‌شده', 'uid-theme' ); ?>
	          </div>
	          <div class="ob">
	            <div class="o-empty">
	              <span class="empty"><?php esc_html_e( 'هنوز متنی وجود ندارد — تا پایان مرحله ۴ آنچه داریم فقط پیکسل است. متن دیجیتال از مرحله ۵ ساخته می‌شود.', 'uid-theme' ); ?></span>
	            </div>
	            <div class="o-raw" hidden>
	              <span class="row"><?php esc_html_e( 'فاکتور فروش — شماره', 'uid-theme' ); ?> <span class="bad">۱۴۰۳۲۷</span></span>
	              <span class="row"><?php esc_html_e( 'نام کالا: کارتن بسته‌بند', 'uid-theme' ); ?><span class="bad">ى</span></span>
	              <span class="row"><?php esc_html_e( 'تعداد: ۱۲۰', 'uid-theme' ); ?></span>
	              <span class="row"><?php esc_html_e( 'مبلغ کل:', 'uid-theme' ); ?> <span class="bad">۱٬۲۵۰٬۵۰۰</span> <?php esc_html_e( 'ریال', 'uid-theme' ); ?></span>
	            </div>
	            <div class="o-fixed" hidden>
	              <span class="row"><?php esc_html_e( 'فاکتور فروش — شماره ۱۴۰۳۲۷', 'uid-theme' ); ?></span>
	              <span class="row"><?php esc_html_e( 'نام کالا: کارتن بسته‌بند', 'uid-theme' ); ?><span class="fixed">ی</span><span class="was">ى</span></span>
	              <span class="row"><?php esc_html_e( 'تعداد: ۱۲۰', 'uid-theme' ); ?></span>
	              <span class="row"><?php esc_html_e( 'مبلغ کل:', 'uid-theme' ); ?> <span class="fixed">۱٬۲۵۰٬۰۰۰</span><span class="was">۱٬۲۵۰٬۵۰۰</span> <?php esc_html_e( 'ریال', 'uid-theme' ); ?></span>
	            </div>
	          </div>
	        </div>
	        <p class="pipe-note"><?php echo wp_kses( __( 'این نمایش یک شبیه‌سازی آموزشی از مراحل کار <span class="lat">OCR</span> است و اندازه‌گیری هیچ سرویس مشخصی نیست.', 'uid-theme' ), uid_op_kses_rich() ); ?></p>
	      </div>

	      <div class="pipe-view">
	        <div class="sheet" aria-hidden="true">
	          <div class="sh-hd"><b><?php esc_html_e( 'فاکتور فروش', 'uid-theme' ); ?></b><span>۱۴۰۳۲۷</span></div>
	          <div class="sh-line"><w><?php esc_html_e( 'نام', 'uid-theme' ); ?></w><w><?php esc_html_e( 'فروشنده:', 'uid-theme' ); ?></w><w><?php esc_html_e( 'بازرگانی', 'uid-theme' ); ?></w><w><?php esc_html_e( 'آریا', 'uid-theme' ); ?></w></div>
	          <div class="sh-line"><w><?php esc_html_e( 'نام', 'uid-theme' ); ?></w><w><?php esc_html_e( 'کالا:', 'uid-theme' ); ?></w><w><?php esc_html_e( 'کارتن', 'uid-theme' ); ?></w><w><?php esc_html_e( 'بسته‌بندی', 'uid-theme' ); ?></w></div>
	          <div class="sh-line"><w><?php esc_html_e( 'تعداد:', 'uid-theme' ); ?></w><w>۱۲۰</w><w><?php esc_html_e( 'عدد', 'uid-theme' ); ?></w></div>
	          <div class="sh-line"><w><?php esc_html_e( 'تاریخ:', 'uid-theme' ); ?></w><w>۱۴۰۵/۰۴/۲۲</w></div>
	          <div class="sh-total">
	            <div class="sh-line"><w><?php esc_html_e( 'مبلغ', 'uid-theme' ); ?></w><w><?php esc_html_e( 'کل:', 'uid-theme' ); ?></w><w>۱٬۲۵۰٬۰۰۰</w><w><?php esc_html_e( 'ریال', 'uid-theme' ); ?></w></div>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="col" style="margin-block-start:26px">
	  <div class="prose">
	    <p><?php echo wp_kses( uid_section_val( 'ophow', 'p3', __( 'در مجموع، اگر بخواهیم ساده بگوییم <span class="lat">OCR</span> چگونه کار می‌کند، باید گفت این فناوری با آماده‌سازی تصویر، تشخیص متن و تبدیل آن به داده دیجیتال عمل می‌کند؛ اما دقت نهایی آن به کیفیت ورودی و پیچیدگی متن وابسته است — و این دقیقاً موضوع بخش بعدی است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	  </div>
	</div>
	<?php
	uid_op_fold_close();
}

/* =====================================================================
 * ۵) فصل ۳ — عوامل موثر بر دقت (شبیه‌ساز دقت — ثابت)
 * ===================================================================== */
function uid_default_op_accuracy_deflist() {
	return array(
		array( 'k' => __( 'کیفیت تصویر', 'uid-theme' ), 'v' => __( 'تصاویر تار، کم‌نور یا نویزی دقت تشخیص را پایین می‌آورند.', 'uid-theme' ) ),
		array( 'k' => __( 'زاویه عکس', 'uid-theme' ), 'v' => __( 'اگر سند کج یا با پرسپکتیو نامناسب ثبت شده باشد، خواندن متن سخت‌تر می‌شود.', 'uid-theme' ) ),
		array( 'k' => __( 'وضوح و رزولوشن', 'uid-theme' ), 'v' => __( 'هرچه رزولوشن بالاتر باشد، جزئیات حروف بهتر دیده می‌شوند.', 'uid-theme' ) ),
		array( 'k' => __( 'نوع فونت', 'uid-theme' ), 'v' => __( 'فونت‌های پیچیده، فانتزی یا بسیار ریز معمولاً برای OCR چالش‌برانگیزترند.', 'uid-theme' ) ),
		array( 'k' => __( 'فاصله و چیدمان حروف', 'uid-theme' ), 'v' => __( 'چسبیدگی زیاد حروف یا فاصله‌گذاری نامنظم می‌تواند باعث خطا شود.', 'uid-theme' ) ),
		array( 'k' => __( 'زبان متن', 'uid-theme' ), 'v' => __( 'بعضی زبان‌ها مثل فارسی به‌دلیل راست‌به‌چپ بودن و شباهت حروف، پیچیدگی بیشتری دارند.', 'uid-theme' ) ),
		array( 'k' => __( 'دست‌نویس یا چاپی بودن', 'uid-theme' ), 'v' => __( 'OCR معمولاً روی متن چاپی بهتر از دست‌خط عمل می‌کند.', 'uid-theme' ) ),
		array( 'k' => __( 'پس‌زمینه تصویر', 'uid-theme' ), 'v' => __( 'شلوغی، سایه، مهر، لکه یا طرح‌دار بودن پس‌زمینه دقت را کاهش می‌دهد.', 'uid-theme' ) ),
	);
}
function uid_default_op_accuracy_farsi() {
	return array(
		array( 'lead' => __( 'اتصال حروف در کلمات فارسی:', 'uid-theme' ), 'text' => __( 'برخلاف خط لاتین، حروف فارسی به هم می‌چسبند و مرز میان دو حرف همیشه آشکار نیست.', 'uid-theme' ) ),
		array( 'lead' => __( 'شباهت بعضی حروف از نظر ظاهری:', 'uid-theme' ), 'text' => __( 'شکل پایه بسیاری از حروف یکسان است.', 'uid-theme' ) ),
		array( 'lead' => __( 'تفاوت فقط در نقطه‌ها، مثل ب، پ، ت و ث:', 'uid-theme' ), 'text' => __( 'یک نقطهٔ گم‌شده یا اضافه، کل کلمه را عوض می‌کند.', 'uid-theme' ) ),
		array( 'lead' => __( 'ترکیب متن فارسی با عدد و کلمات انگلیسی در یک سند:', 'uid-theme' ), 'text' => __( 'جهت نوشتار در یک خط عوض می‌شود و همین، ترتیب خروجی را به هم می‌ریزد.', 'uid-theme' ) ),
	);
}
function uid_render_section_opaccuracy() {
	uid_op_fold_open( 'opaccuracy', uid_section_val( 'opaccuracy', 'eyebrow', __( 'بخش ۳', 'uid-theme' ) ) );
	$tag      = uid_section_tag( 'opaccuracy', 'h2' );
	$deflist  = uid_section_val( 'opaccuracy', 'deflist', uid_default_op_accuracy_deflist() );
	if ( ! is_array( $deflist ) ) $deflist = array();
	$farsi    = uid_section_val( 'opaccuracy', 'farsi_items', uid_default_op_accuracy_farsi() );
	if ( ! is_array( $farsi ) ) $farsi = array();
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opaccuracy', 'heading', __( 'چه عواملی بر دقت <span class="lat">OCR</span> اثر می‌گذارند', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'opaccuracy', 'intro_p', __( 'دقت <span class="lat">OCR</span> همیشه یکسان نیست و به عوامل مختلفی بستگی دارد. اگر این عوامل مناسب باشند، احتمال تشخیص درست متن بسیار بیشتر می‌شود. مهم‌ترین عوامل مؤثر بر دقت <span class="lat">OCR</span> این‌هاست:', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <?php if ( $deflist ) : ?>
	  <div class="deflist">
	    <?php foreach ( $deflist as $row ) : ?>
	    <div class="defrow"><div class="k"><?php echo esc_html( $row['k'] ?? '' ); ?></div>
	      <div class="v"><?php echo wp_kses( $row['v'] ?? '', uid_op_kses_rich() ); ?></div></div>
	    <?php endforeach; ?>
	  </div>
	  <?php endif; ?>
	</div>
	</div>

	<div class="col wide" style="margin-block-start:26px">
	  <div class="sim" id="sim">
	    <div class="sim-l">
	      <h4><?php esc_html_e( 'شرایط تصویر را خودتان عوض کنید', 'uid-theme' ); ?></h4>
	      <p class="sub"><?php esc_html_e( 'هر کلید، یکی از همان عوامل بالاست. آن را روشن کنید و ببینید نمونه سمت دیگر چطور خوانده می‌شود.', 'uid-theme' ); ?></p>
	      <div class="togs">
	        <button class="tog" type="button" aria-pressed="false" data-f="blur">
	          <span class="sw"></span><span><?php esc_html_e( 'تصویر تار', 'uid-theme' ); ?></span></button>
	        <button class="tog" type="button" aria-pressed="false" data-f="dark">
	          <span class="sw"></span><span><?php esc_html_e( 'نور کم', 'uid-theme' ); ?></span></button>
	        <button class="tog" type="button" aria-pressed="false" data-f="skew">
	          <span class="sw"></span><span><?php esc_html_e( 'سند کج', 'uid-theme' ); ?></span></button>
	        <button class="tog" type="button" aria-pressed="false" data-f="res">
	          <span class="sw"></span><span><?php esc_html_e( 'رزولوشن پایین', 'uid-theme' ); ?></span></button>
	        <button class="tog" type="button" aria-pressed="false" data-f="bg">
	          <span class="sw"></span><span><?php esc_html_e( 'پس‌زمینه شلوغ', 'uid-theme' ); ?></span></button>
	        <button class="tog hand" type="button" aria-pressed="false" data-f="hand">
	          <span class="sw"></span><span><?php esc_html_e( 'دست‌نویس به‌جای چاپی', 'uid-theme' ); ?></span></button>
	      </div>
	      <button class="sim-reset" type="button" id="simReset">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8"/><path d="M3 4v4h4"/></svg>
	        <?php esc_html_e( 'بازگشت به شرایط ایده‌آل', 'uid-theme' ); ?>
	      </button>
	    </div>

	    <div class="sim-r">
	      <div class="gauge">
	        <div class="gauge-top">
	          <span><?php esc_html_e( 'شاخص خوانایی متن برای', 'uid-theme' ); ?> <span class="lat">OCR</span><br><span class="tiny" style="font-weight:500"><?php esc_html_e( 'شبیه‌سازی آموزشی — نه اندازه‌گیری', 'uid-theme' ); ?></span></span>
	          <b id="simPct">۱۰۰٪</b>
	        </div>
	        <div class="gauge-bar"><i id="simBar"></i></div>
	        <p class="gauge-tag" id="simTag"><b><?php esc_html_e( 'شرایط ایده‌آل:', 'uid-theme' ); ?></b> <?php esc_html_e( 'سند چاپی، صاف، پرنور و با رزولوشن کافی.', 'uid-theme' ); ?></p>
	      </div>

	      <div class="sim-prev">
	        <div class="ph">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-4.5 4.5"/></svg>
	          <?php esc_html_e( 'تصویر ورودی', 'uid-theme' ); ?>
	        </div>
	        <div class="pb" id="simPb">
	          <div class="sim-card" id="simCard">
	            <div class="t1"><?php esc_html_e( 'کارتن بسته‌بندی سه‌لایه', 'uid-theme' ); ?></div>
	            <div class="t2">A-1250 / 120</div>
	          </div>
	        </div>
	      </div>

	      <div class="sim-out">
	        <div class="ph">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5h16v2M12 5v14M9 19h6"/></svg>
	          <?php esc_html_e( 'متنی که خوانده می‌شود', 'uid-theme' ); ?>
	        </div>
	        <div class="pb" id="simOut"></div>
	      </div>

	      <div class="sim-note">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
	        <span><?php echo wp_kses( __( 'این پنل یک <b>نمایش آموزشی</b> از عواملی است که همین مقاله فهرست کرده است؛ عدد آن یک اندازه‌گیری واقعی نیست و به هیچ سرویس یا محصول مشخصی مربوط نمی‌شود.', 'uid-theme' ), uid_op_kses_rich() ); ?></span>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="col" style="margin-block-start:26px">
	  <div class="prose">
	    <h3><?php echo wp_kses( uid_section_val( 'opaccuracy', 'farsi_heading', __( 'در <span class="lat">OCR</span> فارسی، چند عامل اهمیت بیشتری دارند', 'uid-theme' ) ), uid_op_kses_rich() ); ?></h3>
	    <?php if ( $farsi ) : ?>
	    <ul class="plist">
	      <?php foreach ( $farsi as $it ) : ?>
	      <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16"/><path d="M9 7c2 3 2 7 0 10"/></svg>
	        <span><b><?php echo esc_html( $it['lead'] ?? '' ); ?></b> <?php echo esc_html( $it['text'] ?? '' ); ?></span></li>
	      <?php endforeach; ?>
	    </ul>
	    <?php endif; ?>

	    <div class="callout warm">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.6L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg>
	      <p><?php echo wp_kses( uid_section_val( 'opaccuracy', 'callout_text', __( 'به همین دلیل، مقایسه دقت <span class="lat">OCR</span> میان زبان‌ها معنای چندانی ندارد. عددی که برای متن انگلیسی چاپ‌شده گزارش می‌شود، برای یک فرم دست‌نویس فارسی با پس‌زمینه مهردار تکرار نخواهد شد.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	    </div>
	  </div>
	</div>
	<?php
	uid_op_fold_close();
}

/* =====================================================================
 * ۶) فصل ۴ — قابلیت و خروجی OCR
 * ===================================================================== */
function uid_default_op_output_deflist() {
	return array(
		array( 'k' => __( 'متن ساده', 'uid-theme' ), 'v' => __( 'متن استخراج‌شده به‌صورت خام و قابل کپی.', 'uid-theme' ) ),
		array( 'k' => __( 'فایل قابل ویرایش', 'uid-theme' ), 'v' => __( 'تبدیل تصویر یا اسکن به فایل Word، TXT یا مشابه.', 'uid-theme' ) ),
		array( 'k' => __( 'PDF قابل جست‌وجو', 'uid-theme' ), 'v' => __( 'ظاهراً همان فایل اسکن‌شده باقی می‌ماند، اما متن داخل آن قابل سرچ می‌شود.', 'uid-theme' ) ),
		array( 'k' => __( 'داده ساختاریافته', 'uid-theme' ), 'v' => __( 'استخراج اطلاعات مشخص مثل نام، تاریخ، مبلغ، کد ملی یا شماره فاکتور.', 'uid-theme' ) ),
		array( 'k' => __( 'مختصات متن در تصویر', 'uid-theme' ), 'v' => __( 'مشخص شدن محل دقیق کلمات و خطوط در تصویر برای استفاده در نرم‌افزارها و APIها.', 'uid-theme' ) ),
	);
}
function uid_default_op_output_solves() {
	return array(
		array( 'lead' => __( 'حذف ورود دستی داده‌ها —', 'uid-theme' ), 'text' => __( 'به‌جای تایپ دوباره اطلاعات، متن مستقیماً از تصویر یا سند استخراج می‌شود.', 'uid-theme' ) ),
		array( 'lead' => __( 'صرفه‌جویی در زمان —', 'uid-theme' ), 'text' => __( 'پردازش اسناد، فرم‌ها و فاکتورها سریع‌تر انجام می‌شود.', 'uid-theme' ) ),
		array( 'lead' => __( 'کاهش خطای انسانی —', 'uid-theme' ), 'text' => __( 'احتمال اشتباه در وارد کردن اطلاعات کمتر می‌شود.', 'uid-theme' ) ),
		array( 'lead' => __( 'قابل جست‌وجو کردن اسناد —', 'uid-theme' ), 'text' => __( 'فایل‌های اسکن‌شده و آرشیوهای تصویری به محتوای قابل سرچ تبدیل می‌شوند.', 'uid-theme' ) ),
		array( 'lead' => __( 'دیجیتالی‌سازی بایگانی —', 'uid-theme' ), 'text' => __( 'اسناد کاغذی راحت‌تر ذخیره، دسته‌بندی و بازیابی می‌شوند.', 'uid-theme' ) ),
		array( 'lead' => __( 'استخراج اطلاعات کلیدی —', 'uid-theme' ), 'text' => __( 'داده‌هایی مثل تاریخ، شماره سند، مبلغ یا نام اشخاص سریع‌تر جدا می‌شوند.', 'uid-theme' ) ),
	);
}
function uid_render_section_opoutput() {
	uid_op_fold_open( 'opoutput', uid_section_val( 'opoutput', 'eyebrow', __( 'بخش ۴', 'uid-theme' ) ) );
	$tag      = uid_section_tag( 'opoutput', 'h2' );
	$deflist  = uid_section_val( 'opoutput', 'deflist', uid_default_op_output_deflist() );
	if ( ! is_array( $deflist ) ) $deflist = array();
	$solves   = uid_section_val( 'opoutput', 'solves_items', uid_default_op_output_solves() );
	if ( ! is_array( $solves ) ) $solves = array();
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opoutput', 'heading', __( 'قابلیت <span class="lat">OCR</span> چیست و چه کاری انجام می‌دهد؟', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'opoutput', 'intro_p', __( '<span class="lat">OCR</span> فقط ابزاری برای خواندن متن از روی تصویر نیست، بلکه فناوری‌ای است که اطلاعات متنی را از حالت غیرقابل استفاده به داده‌ای قابل جست‌وجو، ویرایش و پردازش تبدیل می‌کند. به همین دلیل، کاربرد آن از کارهای ساده روزمره تا فرایندهای سازمانی و امنیتی گسترده شده است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <h3><?php echo wp_kses( uid_section_val( 'opoutput', 'shape_heading', __( 'خروجی <span class="lat">OCR</span> چه شکلی است', 'uid-theme' ) ), uid_op_kses_rich() ); ?></h3>
	  <p><?php echo wp_kses( uid_section_val( 'opoutput', 'shape_p', __( 'خروجی <span class="lat">OCR</span> بسته به نوع ابزار و هدف استفاده می‌تواند ساده یا پیشرفته باشد. در حالت پایه، نتیجه فقط یک متن استخراج‌شده است؛ اما در ابزارهای حرفه‌ای‌تر، ساختار و جایگاه متن هم حفظ می‌شود.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <?php if ( $deflist ) : ?>
	  <div class="deflist">
	    <?php foreach ( $deflist as $row ) : ?>
	    <div class="defrow"><div class="k"><?php echo wp_kses( $row['k'] ?? '', uid_op_kses_rich() ); ?></div>
	      <div class="v"><?php echo wp_kses( $row['v'] ?? '', uid_op_kses_rich() ); ?></div></div>
	    <?php endforeach; ?>
	  </div>
	  <?php endif; ?>

	  <p class="pull"><?php echo wp_kses( uid_section_val( 'opoutput', 'pull', __( 'به زبان ساده، <span class="lat">OCR</span> می‌تواند فقط متن را تحویل بدهد یا متن را همراه با ساختار و موقعیت آن ارائه کند.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <h3><?php echo wp_kses( uid_section_val( 'opoutput', 'solves_heading', __( '<span class="lat">OCR</span> چه مسائلی را حل می‌کند', 'uid-theme' ) ), uid_op_kses_rich() ); ?></h3>
	  <p><?php echo wp_kses( uid_section_val( 'opoutput', 'solves_p', __( 'اگر بخواهیم دقیق‌تر بگوییم قابلیت <span class="lat">OCR</span> چیست و چه کاری انجام می‌دهد، باید به مسئله‌هایی اشاره کنیم که این فناوری حل می‌کند. <span class="lat">OCR</span> در اصل مشکل ورود دستی اطلاعات، کندی پردازش اسناد و غیرقابل جست‌وجو بودن متن داخل تصاویر را برطرف می‌کند.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>

	  <?php if ( $solves ) : ?>
	  <ul class="plist num">
	    <?php foreach ( $solves as $it ) : ?>
	    <li><span><b><?php echo esc_html( $it['lead'] ?? '' ); ?></b> <?php echo esc_html( $it['text'] ?? '' ); ?></span></li>
	    <?php endforeach; ?>
	  </ul>
	  <?php endif; ?>
	</div>
	<?php
	echo '</div>';
	uid_op_fold_close();
}

/* =====================================================================
 * ۷) فصل ۵ — انواع OCR (انتخابگر ۵تایی + جدول مقایسه سریع — کاملاً ثابت)
 * ===================================================================== */
function uid_render_section_optypes() {
	uid_op_fold_open( 'optypes', uid_section_val( 'optypes', 'eyebrow', __( 'بخش ۵', 'uid-theme' ) ) );
	$tag = uid_section_tag( 'optypes', 'h2' );
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'optypes', 'heading', __( 'انواع <span class="lat">OCR</span>', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'optypes', 'intro_p', __( 'فناوری <span class="lat">OCR</span> فقط یک مدل ثابت ندارد و بسته به نوع متن، محل اجرا و هدف استفاده، در چند دسته مختلف قرار می‌گیرد. شناخت انواع <span class="lat">OCR</span> کمک می‌کند بدانیم برای هر نیاز، از تبدیل عکس به متن تا پردازش اسناد سازمانی، چه راهکاری مناسب‌تر است. با اینکه انواع <span class="lat">OCR</span> از نظر عملکرد به هم نزدیک هستند، هرکدام برای سناریوی مشخصی طراحی شده‌اند و انتخاب درست آن‌ها روی دقت، سرعت و هزینه اثر می‌گذارد.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	</div>
	</div>

	<div class="col wide" style="margin-block-start:24px">
	  <div class="tsel" id="tsel">
	    <div class="tsel-rail" role="tablist" aria-label="<?php esc_attr_e( 'انواع OCR', 'uid-theme' ); ?>">
	      <button class="tchip on" type="button" role="tab" aria-selected="true" data-t="1"><i>۱</i><?php esc_html_e( 'مبتنی بر تصویر چاپی', 'uid-theme' ); ?></button>
	      <button class="tchip" type="button" role="tab" aria-selected="false" data-t="2"><i>۲</i><?php esc_html_e( 'دست‌نویس', 'uid-theme' ); ?></button>
	      <button class="tchip" type="button" role="tab" aria-selected="false" data-t="3"><i>۳</i><?php esc_html_e( 'ابری و API‌محور', 'uid-theme' ); ?></button>
	      <button class="tchip" type="button" role="tab" aria-selected="false" data-t="4"><i>۴</i><?php esc_html_e( 'آفلاین و محلی', 'uid-theme' ); ?></button>
	      <button class="tchip" type="button" role="tab" aria-selected="false" data-t="5"><i>۵</i><?php esc_html_e( 'هوشمند و سندمحور', 'uid-theme' ); ?></button>
	    </div>

	    <div class="tpanel" data-p="1" role="tabpanel">
	      <div class="tp-top">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5V5a2 2 0 012-2h12a2 2 0 012 2v14.5"/><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M8 7h8M8 11h8"/></svg></span>
	        <div>
	          <h4><span class="lat">OCR</span> <?php esc_html_e( 'مبتنی بر تصویر چاپی', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'این نوع، رایج‌ترین مدل OCR است و برای شناسایی متن‌های چاپی در اسناد، کتاب‌ها، فرم‌ها و فایل‌های اسکن‌شده استفاده می‌شود. دقت آن معمولاً از سایر مدل‌ها بیشتر است، چون حروف چاپی ساختار منظم‌تر و خواناتری دارند.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	      <div class="tp-cells">
	        <div class="tp-cell"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'کتاب، قرارداد، فرم و اسناد تایپ‌شده', 'uid-theme' ); ?></div></div>
	        <div class="tp-cell win"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'دقت بالا روی متن چاپی', 'uid-theme' ); ?></div></div>
	      </div>
	      <div class="tp-lists">
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg><?php esc_html_e( 'ویژگی‌ها', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'مناسب برای متن‌های تایپ‌شده و چاپی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'قابل استفاده برای اسکن کتاب، قرارداد، گزارش و فرم', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'دقت بالا در صورت کیفیت مناسب تصویر', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'مناسب برای تبدیل PDF تصویری به متن قابل جست‌وجو', 'uid-theme' ); ?></li>
	          </ul></div>
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="2.6"/></svg><?php esc_html_e( 'کاربردهای رایج', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'دیجیتالی‌سازی آرشیو اسناد', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'استخراج متن از کتاب و جزوه', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'تبدیل اسناد کاغذی به فایل قابل ویرایش', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'جست‌وجو در پرونده‌های اسکن‌شده', 'uid-theme' ); ?></li>
	          </ul></div>
	      </div>
	    </div>

	    <div class="tpanel" data-p="2" role="tabpanel" hidden>
	      <div class="tp-top">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 3.5a2.1 2.1 0 013 3L8 18l-4 1 1-4z"/><path d="M14 6l3 3"/></svg></span>
	        <div>
	          <h4><span class="lat">OCR</span> <?php esc_html_e( 'دست‌نویس', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'OCR دست‌نویس برای تشخیص نوشته‌هایی به کار می‌رود که با دست نوشته شده‌اند. این نوع از OCR پیچیده‌تر است، چون سبک نوشتار افراد، فاصله حروف و خوانایی متن ثابت نیست.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	      <div class="tp-cells">
	        <div class="tp-cell"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'یادداشت، فرم دستی، اسناد نوشته‌شده با دست', 'uid-theme' ); ?></div></div>
	        <div class="tp-cell win"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'امکان خواندن متن غیرتایپی', 'uid-theme' ); ?></div></div>
	      </div>
	      <div class="tp-lists">
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg><?php esc_html_e( 'ویژگی‌ها', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'برای خواندن متن‌های دست‌نویس طراحی شده است', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'نسبت به OCR چاپی خطای بیشتری دارد', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'به مدل‌های هوش مصنوعی و آموزش‌دیده‌تر نیاز دارد', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'عملکرد آن به خوانا بودن دست‌خط وابسته است', 'uid-theme' ); ?></li>
	          </ul></div>
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="2.6"/></svg><?php esc_html_e( 'کاربردهای معمول', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'خواندن فرم‌های دستی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'پردازش یادداشت‌ها و نسخه‌های نوشته‌شده', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'استخراج اطلاعات از اسناد قدیمی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'استفاده در برخی سامانه‌های آموزشی و اداری', 'uid-theme' ); ?></li>
	          </ul></div>
	      </div>
	    </div>

	    <div class="tpanel" data-p="3" role="tabpanel" hidden>
	      <div class="tp-top">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 18a4 4 0 00.5-8 6 6 0 00-11.6-1.3A3.6 3.6 0 006 18z"/><path d="M12 12v5M9.7 14.5L12 12l2.3 2.5"/></svg></span>
	        <div>
	          <h4><span class="lat">OCR</span> <?php esc_html_e( 'ابری و', 'uid-theme' ); ?> <span class="lat">API</span><?php esc_html_e( '‌محور', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'در این مدل، پردازش OCR از طریق سرویس‌های آنلاین یا API انجام می‌شود. یعنی تصویر یا سند به یک سرویس ابری ارسال می‌شود و نتیجه به‌صورت متن یا داده ساختاریافته برمی‌گردد.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	      <div class="tp-cells">
	        <div class="tp-cell"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'اپلیکیشن‌ها، سایت‌ها، سامانه‌های آنلاین', 'uid-theme' ); ?></div></div>
	        <div class="tp-cell win"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'مقیاس‌پذیری و پیاده‌سازی سریع', 'uid-theme' ); ?></div></div>
	      </div>
	      <div class="tp-lists">
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg><?php esc_html_e( 'مزایا', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'راه‌اندازی سریع و ساده', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'مناسب برای وب‌سایت‌ها، اپلیکیشن‌ها و سامانه‌ها', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'مقیاس‌پذیری بالا برای پردازش حجم زیاد اسناد', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'به‌روزرسانی و بهبود مداوم توسط ارائه‌دهنده سرویس', 'uid-theme' ); ?></li>
	          </ul></div>
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="2.6"/></svg><?php esc_html_e( 'معمولاً مناسب برای', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'استارتاپ‌ها و سرویس‌های آنلاین', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'پردازش خودکار مدارک کاربران', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'استخراج اطلاعات از کارت شناسایی، فاکتور و فرم', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'اتصال به فرایندهای امنیتی و احراز هویت', 'uid-theme' ); ?></li>
	          </ul></div>
	      </div>
	    </div>

	    <div class="tpanel" data-p="4" role="tabpanel" hidden>
	      <div class="tp-top">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7.5a4 4 0 018 0V11"/></svg></span>
	        <div>
	          <h4><span class="lat">OCR</span> <?php esc_html_e( 'آفلاین و محلی', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'OCR آفلاین یا محلی روی سیستم، سرور داخلی یا زیرساخت سازمان اجرا می‌شود و برای پردازش، وابسته به اینترنت یا سرویس بیرونی نیست. این مدل برای سازمان‌هایی که روی امنیت داده حساس هستند، اهمیت زیادی دارد.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	      <div class="tp-cells">
	        <div class="tp-cell"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'سازمان‌های حساس به امنیت داده', 'uid-theme' ); ?></div></div>
	        <div class="tp-cell win"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'کنترل بیشتر روی اطلاعات', 'uid-theme' ); ?></div></div>
	      </div>
	      <div class="tp-lists">
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg><?php esc_html_e( 'ویژگی‌ها', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'اجرا روی دستگاه یا سرور داخلی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'کنترل بیشتر روی داده‌ها', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'مناسب برای محیط‌های با محدودیت اینترنت', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'قابل سفارشی‌سازی برای نیازهای خاص', 'uid-theme' ); ?></li>
	          </ul></div>
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><?php esc_html_e( 'مزایای اصلی', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'حفظ محرمانگی اطلاعات', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'کاهش وابستگی به سرویس‌های خارجی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'مناسب برای اسناد حساس، مالی یا هویتی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'امکان استفاده در زیرساخت‌های بسته سازمانی', 'uid-theme' ); ?></li>
	          </ul></div>
	      </div>
	    </div>

	    <div class="tpanel" data-p="5" role="tabpanel" hidden>
	      <div class="tp-top">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/><path d="M12.5 13h5M12.5 16.5h3"/></svg></span>
	        <div>
	          <h4><span class="lat">OCR</span> <?php esc_html_e( 'هوشمند و سندمحور', 'uid-theme' ); ?></h4>
	          <p><?php esc_html_e( 'OCR هوشمند و سندمحور فقط متن را استخراج نمی‌کند، بلکه ساختار سند را هم تا حد زیادی می‌فهمد. این نوع راهکارها می‌توانند بخش‌هایی مثل جدول، فرم، فیلدهای کلیدی، شماره‌ها و داده‌های مهم را جدا و دسته‌بندی کنند.', 'uid-theme' ); ?></p>
	        </div>
	      </div>
	      <div class="tp-cells">
	        <div class="tp-cell"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'فاکتور، فرم، جدول، مدارک ساختاریافته', 'uid-theme' ); ?></div></div>
	        <div class="tp-cell win"><div class="k"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div>
	          <div class="v"><?php esc_html_e( 'استخراج متن همراه با ساختار و فیلدها', 'uid-theme' ); ?></div></div>
	      </div>
	      <div class="tp-lists">
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg><?php esc_html_e( 'چه کارهایی انجام می‌دهد', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'تشخیص متن', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'شناسایی ساختار صفحه', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'تفکیک جدول، فرم و بخش‌های مختلف سند', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'استخراج فیلدهای مهم مثل نام، تاریخ، مبلغ و شماره سند', 'uid-theme' ); ?></li>
	          </ul></div>
	        <div><h5><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h4l2.5-7 4 14 2.5-7h5"/></svg><?php esc_html_e( 'مزایا', 'uid-theme' ); ?></h5>
	          <ul>
	            <li><?php esc_html_e( 'مناسب برای اسناد پیچیده', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'کاهش نیاز به بررسی دستی', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'سرعت بیشتر در پردازش اطلاعات ساختاریافته', 'uid-theme' ); ?></li>
	            <li><?php esc_html_e( 'کاربرد بالا در فرایندهای سازمانی و اتوماسیون', 'uid-theme' ); ?></li>
	          </ul></div>
	      </div>
	    </div>
	  </div>

	  <div class="cmp">
	    <div class="r hd"><div><?php esc_html_e( 'نوع', 'uid-theme' ); ?> <span class="lat">OCR</span></div><div><?php esc_html_e( 'کاربرد', 'uid-theme' ); ?></div><div><?php esc_html_e( 'مزیت اصلی', 'uid-theme' ); ?></div></div>
	    <div class="r"><div class="c1" data-l="<?php esc_attr_e( 'نوع OCR', 'uid-theme' ); ?>"><?php esc_html_e( 'مبتنی بر تصویر چاپی', 'uid-theme' ); ?></div>
	      <div class="c2" data-l="<?php esc_attr_e( 'کاربرد', 'uid-theme' ); ?>"><?php esc_html_e( 'کتاب، قرارداد، فرم و اسناد تایپ‌شده', 'uid-theme' ); ?></div>
	      <div class="c3" data-l="<?php esc_attr_e( 'مزیت اصلی', 'uid-theme' ); ?>"><?php esc_html_e( 'دقت بالا روی متن چاپی', 'uid-theme' ); ?></div></div>
	    <div class="r"><div class="c1" data-l="<?php esc_attr_e( 'نوع OCR', 'uid-theme' ); ?>"><?php esc_html_e( 'دست‌نویس', 'uid-theme' ); ?></div>
	      <div class="c2" data-l="<?php esc_attr_e( 'کاربرد', 'uid-theme' ); ?>"><?php esc_html_e( 'یادداشت، فرم دستی، اسناد نوشته‌شده با دست', 'uid-theme' ); ?></div>
	      <div class="c3" data-l="<?php esc_attr_e( 'مزیت اصلی', 'uid-theme' ); ?>"><?php esc_html_e( 'امکان خواندن متن غیرتایپی', 'uid-theme' ); ?></div></div>
	    <div class="r"><div class="c1" data-l="<?php esc_attr_e( 'نوع OCR', 'uid-theme' ); ?>"><?php esc_html_e( 'ابری و', 'uid-theme' ); ?> <span class="lat">API</span><?php esc_html_e( '‌محور', 'uid-theme' ); ?></div>
	      <div class="c2" data-l="<?php esc_attr_e( 'کاربرد', 'uid-theme' ); ?>"><?php esc_html_e( 'اپلیکیشن‌ها، سایت‌ها، سامانه‌های آنلاین', 'uid-theme' ); ?></div>
	      <div class="c3" data-l="<?php esc_attr_e( 'مزیت اصلی', 'uid-theme' ); ?>"><?php esc_html_e( 'مقیاس‌پذیری و پیاده‌سازی سریع', 'uid-theme' ); ?></div></div>
	    <div class="r"><div class="c1" data-l="<?php esc_attr_e( 'نوع OCR', 'uid-theme' ); ?>"><?php esc_html_e( 'آفلاین و محلی', 'uid-theme' ); ?></div>
	      <div class="c2" data-l="<?php esc_attr_e( 'کاربرد', 'uid-theme' ); ?>"><?php esc_html_e( 'سازمان‌های حساس به امنیت داده', 'uid-theme' ); ?></div>
	      <div class="c3" data-l="<?php esc_attr_e( 'مزیت اصلی', 'uid-theme' ); ?>"><?php esc_html_e( 'کنترل بیشتر روی اطلاعات', 'uid-theme' ); ?></div></div>
	    <div class="r"><div class="c1" data-l="<?php esc_attr_e( 'نوع OCR', 'uid-theme' ); ?>"><?php esc_html_e( 'هوشمند و سندمحور', 'uid-theme' ); ?></div>
	      <div class="c2" data-l="<?php esc_attr_e( 'کاربرد', 'uid-theme' ); ?>"><?php esc_html_e( 'فاکتور، فرم، جدول، مدارک ساختاریافته', 'uid-theme' ); ?></div>
	      <div class="c3" data-l="<?php esc_attr_e( 'مزیت اصلی', 'uid-theme' ); ?>"><?php esc_html_e( 'استخراج متن همراه با ساختار و فیلدها', 'uid-theme' ); ?></div></div>
	  </div>
	</div>
	<?php
	uid_op_fold_close();
}

/* =====================================================================
 * ۸) فصل ۶ — کاربرد OCR در زندگی روزمره (۵ کارت — آیکون ثابت، عنوان/متن/فهرست
 * قابل‌ویرایش؛ هر فهرست به‌صورت «یک مورد در هر خط» ذخیره می‌شود)
 * ===================================================================== */
function uid_op_daily_icon( $n ) {
	switch ( $n ) {
		case 1:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2.5"/><circle cx="12" cy="13" r="3.5"/><path d="M8 6l1.5-2.5h5L16 6"/></svg>';
		case 2:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8.5 13.5h7M8.5 17h4"/></svg>';
		case 3:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>';
		case 4:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M9 4v2c0 4-2.5 7-5 8.5"/><path d="M6.5 10.5c1.5 2.5 3.5 4 6 5"/><path d="M13 21l4.5-11L22 21M14.8 17.4h5.4"/></svg>';
		case 5:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5L6 9H3v6h3l5 4z"/><path d="M15.5 8.5a5 5 0 010 7M18.5 5.5a9 9 0 010 13"/></svg>';
	}
	return '';
}
function uid_default_op_daily_cards() {
	return array(
		1 => array(
			'title' => __( 'تبدیل عکس به متن', 'uid-theme' ),
			'text'  => __( 'این پرکاربردترین شکل استفاده از OCR است که به شما اجازه می‌دهد از هر تصویر یا نوشته‌ای عکس بگیرید و آن را به متن قابل ویرایش تبدیل کنید.', 'uid-theme' ),
			'items' => __( "کپی کردن متن از روی عکس‌های کتاب، جزوه یا نوشته‌های داخل کلاس\nاستخراج اطلاعات از بیلبوردها و تابلوهای تبلیغاتی بدون یادداشت‌برداری دستی\nذخیره یادداشت‌های دست‌نویس یا تایپ‌شده از روی تخته وایت‌برد\nتبدیل سریع عکس کارت ویزیت به مخاطب در گوشی", 'uid-theme' ),
		),
		2 => array(
			'title' => __( 'استخراج متن از PDF اسکن‌شده', 'uid-theme' ),
			'text'  => __( 'بسیاری از فایل‌های PDF فقط تصاویر اسکن‌شده هستند و قابلیت انتخاب یا کپی متن ندارند؛ OCR این فایل‌های تصویری را به اسنادی زنده و کاربردی تبدیل می‌کند.', 'uid-theme' ),
			'items' => __( "ویرایش محتوای PDF بدون نیاز به تایپ دوباره متن\nحفظ ظاهر و قالب‌بندی سند در کنار متن استخراج‌شده\nآماده‌سازی اسناد برای تبدیل به فرمت‌هایی مثل Word یا Excel", 'uid-theme' ),
		),
		3 => array(
			'title' => __( 'جست‌وجو داخل اسناد', 'uid-theme' ),
			'text'  => __( 'با کمک OCR می‌توانید در میان هزاران صفحه اسناد اسکن‌شده، کلمه یا عبارت خاصی را درست مثل یک فایل متنی جست‌وجو کنید.', 'uid-theme' ),
			'items' => __( "جست‌وجوی سریع در بایگانی پرونده‌های اداری، حقوقی یا مالی\nپیدا کردن شماره فاکتور، کد رهگیری یا نام شخص در میان رسیدهای قدیمی\nمدیریت هوشمندانه حجم زیادی از اطلاعات که قبلاً فقط به‌صورت عکس بودند", 'uid-theme' ),
		),
		4 => array(
			'title' => __( 'ترجمه سریع متن', 'uid-theme' ),
			'text'  => __( 'ترکیب OCR با ابزارهای ترجمه باعث شده تا زبان دیگر مانعی برای ارتباط نباشد؛ کافی است دوربین را روی متن بگیرید تا ترجمه فوری آن را ببینید.', 'uid-theme' ),
			'items' => __( "ترجمه منوی رستوران در کشورهای خارجی تنها با گرفتن دوربین روی منو\nفهمیدن تابلوهای راهنما یا علائم ایمنی هنگام سفر\nترجمه فوری مقاله‌ها، کتاب‌ها یا بروشورهای چاپ‌شده به زبان‌های دیگر", 'uid-theme' ),
		),
		5 => array(
			'title' => __( 'کمک به افراد نابینا یا کم‌بینا', 'uid-theme' ),
			'text'  => __( 'OCR یکی از بزرگ‌ترین تحولات در حوزه دسترس‌پذیری است که به افراد دارای اختلال بینایی کمک می‌کند با جهان اطرافشان تعامل بهتری داشته باشند.', 'uid-theme' ),
			'items' => __( "خواندن متون چاپ‌شده، برچسب کالاها یا نامه‌ها از طریق تبدیل متن به صدا (TTS)\nافزایش استقلال در مطالعه کتاب، روزنامه و نشریات چاپی\nتشخیص محتوای محیط اطراف با اپلیکیشن‌های موبایلی که متن‌ها را بلند می‌خوانند", 'uid-theme' ),
		),
	);
}
function uid_render_section_opdaily() {
	uid_op_fold_open( 'opdaily', uid_section_val( 'opdaily', 'eyebrow', __( 'بخش ۶', 'uid-theme' ) ) );
	$tag     = uid_section_tag( 'opdaily', 'h2' );
	$default = uid_default_op_daily_cards();
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opdaily', 'heading', __( 'کاربرد فناوری <span class="lat">OCR</span> در زندگی روزمره', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'opdaily', 'intro_p', __( 'فناوری <span class="lat">OCR</span> فراتر از کاربردهای پیچیده سازمانی، در کارهای روزمره ما نیز حضور دارد و با ساده کردن تعامل با متن‌های تصویری، سرعت انجام کارها را به‌شدت افزایش داده است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	</div>
	</div>

	<div class="col wide" style="margin-block-start:22px">
	  <div class="uc">
	    <?php for ( $n = 1; $n <= 5; $n++ ) :
	      $d     = $default[ $n ];
	      $title = uid_section_val( 'opdaily', 'card' . $n . '_title', $d['title'] );
	      $text  = uid_section_val( 'opdaily', 'card' . $n . '_text', $d['text'] );
	      $items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'opdaily', 'card' . $n . '_items', $d['items'] ) ) ) );
	      $rv    = ( 1 === $n ) ? ' rv' : ' rv rv-d' . min( $n - 1, 5 );
	    ?>
	    <div class="ucard<?php echo esc_attr( $rv ); ?>">
	      <span class="ic"><?php echo uid_op_daily_icon( $n ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	      <h4><?php echo esc_html( $title ); ?></h4>
	      <p><?php echo wp_kses( $text, uid_op_kses_rich() ); ?></p>
	      <?php if ( $items ) : ?>
	      <ul>
	        <?php foreach ( $items as $item ) : ?>
	        <li><?php echo uid_op_ic_check(); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><?php echo wp_kses( $item, uid_op_kses_rich() ); ?></li>
	        <?php endforeach; ?>
	      </ul>
	      <?php endif; ?>
	    </div>
	    <?php endfor; ?>
	  </div>
	</div>
	<?php
	uid_op_fold_close();
}

/* =====================================================================
 * ۹) فصل ۷ — امنیت، KYC و احراز هویت دیجیتال (شامل تنها CTA زمینه‌ای مقاله —
 * فقط به وب‌سرویس احراز هویت تصویری واقعی یوآیدی اشاره می‌کند، نه محصولی برای
 * خودِ OCR که یوآیدی ارائه نمی‌دهد)
 * ===================================================================== */
function uid_op_security_icon( $n ) {
	switch ( $n ) {
		case 1:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2.5"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5.5 16c.6-1.5 1.8-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4.5M14.5 13.5h3"/></svg>';
		case 2:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8.5 13h7M8.5 16.5h4"/></svg>';
		case 3:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
		case 4:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8V5.5A2.5 2.5 0 015.5 3H8M21 8V5.5A2.5 2.5 0 0018.5 3H16M3 16v2.5A2.5 2.5 0 005.5 21H8M21 16v2.5a2.5 2.5 0 01-2.5 2.5H16"/><circle cx="12" cy="10.5" r="2.4"/><path d="M8.6 16a4 4 0 016.8 0"/></svg>';
	}
	return '';
}
function uid_default_op_security_cards() {
	return array(
		1 => array(
			'title' => __( 'خواندن کارت شناسایی و مدارک', 'uid-theme' ),
			'text'  => __( 'این قابلیت به سیستم‌ها اجازه می‌دهد تا بدون دخالت اپراتور، اطلاعات حساس را از روی مدارک هویتی بخوانند.', 'uid-theme' ),
			'items' => __( "اسکن خودکار: خواندن سریع اطلاعات از روی کارت ملی، گذرنامه و گواهینامه.\nبررسی اصالت: تطبیق خودکار داده‌های استخراج‌شده با دیتابیس‌های معتبر.\nافزایش سرعت: کاهش چشمگیر زمان انتظار کاربر برای تایید مدارک در سازمان‌ها.", 'uid-theme' ),
		),
		2 => array(
			'title' => __( 'استخراج اطلاعات از فرم‌ها', 'uid-theme' ),
			'text'  => __( 'OCR کمک می‌کند تا فرم‌های کاغذی پرشده توسط افراد، به داده‌های متنی قابل جست‌وجو و ذخیره در سیستم تبدیل شوند.', 'uid-theme' ),
			'items' => __( "دقت بالا: خواندن داده‌های کلیدی مانند نام، نام خانوادگی و کد ملی از فرم‌ها.\nانتقال مستقیم: ارسال داده‌ها به پایگاه داده مرکزی برای پردازش‌های بعدی.\nحذف خطا: جلوگیری از اشتباهات تایپی اپراتورها در حین ورود اطلاعات.", 'uid-theme' ),
		),
		3 => array(
			'title' => __( 'نقش OCR در KYC', 'uid-theme' ),
			'text'  => __( 'در فرایندهای احراز هویت مشتری (KYC)، این فناوری وظیفه اصلی تطبیق مدارک با واقعیت را بر عهده دارد.', 'uid-theme' ),
			'items' => __( "تایید غیرحضوری: امکان ثبت‌نام مشتریان در سرویس‌های مالی بدون نیاز به حضور فیزیکی.\nتطبیق بیومتریک: بررسی تصویر سند و تطبیق آن با سلفی زنده‌ای که کاربر ارسال می‌کند.\nبهبود تجربه: ساده‌سازی فرایندهای بانکی و صرافی‌ها برای کاربران.", 'uid-theme' ),
		),
		4 => array(
			'title' => __( 'ارتباط OCR با احراز هویت دیجیتال', 'uid-theme' ),
			'text'  => __( 'OCR به‌عنوان حلقه گم‌شده در تبدیل اسناد فیزیکی به داده‌های دیجیتال، پایه و اساس امنیت در دنیای آنلاین است.', 'uid-theme' ),
			'items' => __( "اعتمادسازی: تبدیل مدارک فیزیکی به فرمت دیجیتال قابل استعلام.\nامنیت تراکنش: تضمین اینکه فرد پشت سیستم همان شخصی است که مدارکش را ارائه داده.\nاستفاده از سیستم‌های <a href=\"/api/\">احراز هویت دیجیتال</a> به کسب‌وکارها کمک می‌کند تا ریسک جعل هویت را به کمترین میزان برسانند و اعتماد کاربران را جلب کنند.", 'uid-theme' ),
		),
	);
}
function uid_render_section_opsecurity() {
	uid_op_fold_open( 'opsecurity', uid_section_val( 'opsecurity', 'eyebrow', __( 'بخش ۷', 'uid-theme' ) ) );
	$tag     = uid_section_tag( 'opsecurity', 'h2' );
	$default = uid_default_op_security_cards();
	?>
	<?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opsecurity', 'heading', __( 'کاربرد <span class="lat">OCR</span> در امنیت و احراز هویت', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	</div>
	<div class="prose">
	  <p><?php echo wp_kses( uid_section_val( 'opsecurity', 'intro_p', __( 'فناوری <span class="lat">OCR</span> در حوزه امنیت نقش کلیدی در کاهش خطای انسانی و سرعت‌بخشیدن به تایید هویت دارد. این تکنولوژی با خودکارسازی ورود داده‌ها، سطح امنیت سیستم‌ها را به میزان قابل توجهی ارتقا می‌دهد.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	</div>
	</div>

	<div class="col wide" style="margin-block-start:22px">
	  <div class="sec-grid">
	    <?php for ( $n = 1; $n <= 4; $n++ ) :
	      $d     = $default[ $n ];
	      $title = uid_section_val( 'opsecurity', 'card' . $n . '_title', $d['title'] );
	      $text  = uid_section_val( 'opsecurity', 'card' . $n . '_text', $d['text'] );
	      $items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'opsecurity', 'card' . $n . '_items', $d['items'] ) ) ) );
	      $rv    = ( 1 === $n ) ? ' rv' : ' rv rv-d' . ( $n - 1 );
	    ?>
	    <div class="scard<?php echo esc_attr( $rv ); ?>">
	      <div class="top"><span class="ic"><?php echo uid_op_security_icon( $n ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?></span>
	        <h4><?php echo wp_kses( $title, uid_op_kses_rich() ); ?></h4></div>
	      <p><?php echo wp_kses( $text, uid_op_kses_rich() ); ?></p>
	      <?php if ( $items ) : ?>
	      <ul>
	        <?php foreach ( $items as $item ) : ?>
	        <li><?php echo uid_op_ic_check(); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><span><?php echo wp_kses( $item, uid_op_kses_rich() ); ?></span></li>
	        <?php endforeach; ?>
	      </ul>
	      <?php endif; ?>
	    </div>
	    <?php endfor; ?>
	  </div>

	  <!-- «مقاله پیشنهادی» — نکته: آدرس فعلی یک جانگهدار است؛ پیش از انتشار باید
	       با آدرس واقعی مطلب KYC در blog.u-id.net هماهنگ شود (همان‌طور که در
	       mockup اصلی هم یادداشت شده بود). -->
	  <div class="readnext">
	    <span class="lb">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/></svg>
	      <?php echo esc_html( uid_section_val( 'opsecurity', 'readnext_label', __( 'مقاله پیشنهادی', 'uid-theme' ) ) ); ?>
	    </span>
	    <a href="<?php echo esc_url( uid_section_val( 'opsecurity', 'readnext_url', 'https://blog.u-id.net/kyc/' ) ); ?>"><?php echo wp_kses( uid_section_val( 'opsecurity', 'readnext_title', __( '<span class="lat">KYC</span> چیست و چرا کسب‌وکارهای مالی به آن نیاز دارند؟', 'uid-theme' ) ), uid_op_kses_rich() ); ?>
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
	  </div>
	</div>

	<div class="col" style="margin-block-start:24px">
	  <div class="prose">
	    <h3><?php echo esc_html( uid_section_val( 'opsecurity', 'webservice_heading', __( 'استفاده در وب‌سرویس احراز هویت', 'uid-theme' ) ) ); ?></h3>
	    <p><?php echo wp_kses( uid_section_val( 'opsecurity', 'webservice_p', __( 'ترکیب <span class="lat">OCR</span> با وب‌سرویس‌ها، امکان ایجاد سیستم‌های تایید هویت هوشمند و کاملاً خودکار را برای توسعه‌دهندگان فراهم می‌کند. دو چیز در این ترکیب اهمیت دارد: <b>پردازش آنی</b> — استخراج لحظه‌ای اطلاعات مدارک ارسال‌شده توسط کاربر از طریق <span class="lat">API</span> — و <b>یکپارچه‌سازی</b>، یعنی اتصال بدون دردسر به سیستم‌های استعلام‌دهی دولتی یا سازمانی.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	  </div>
	</div>

	<!-- ══════ تنها CTA زمینه‌ای این مقاله — دقیقاً جایی که سند منبع لینک خودش را
	     می‌گذارد، و فقط به سرویسی که یوآیدی واقعاً ارائه می‌دهد اشاره می‌کند: وب‌سرویس
	     احراز هویت تصویری. هیچ محصول/قیمت/API مستقلی برای «OCR» فرض نشده است. ══════ -->
	<div class="col wide">
	  <div class="ctx">
	    <div>
	      <span class="op-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'opsecurity', 'ctx_eyebrow', __( 'از این مقاله به سرویس', 'uid-theme' ) ) ); ?></span>
	      <h4><?php echo wp_kses( uid_section_val( 'opsecurity', 'ctx_heading', __( 'برای پیاده‌سازی تایید هویت در اپلیکیشن یا وب‌سایت خود، از <span class="lat">وب‌سرویس احراز هویت</span> یوآیدی استفاده کنید', 'uid-theme' ) ), uid_op_kses_rich() ); ?></h4>
	      <p><?php echo wp_kses( uid_section_val( 'opsecurity', 'ctx_text', __( 'یوآیدی وب‌سرویس احراز هویت تصویری ارائه می‌دهد: تطبیق چهره کاربر با تصویر مرجع و تشخیص زنده‌بودن، قابل اتصال از طریق <span class="lat">API</span> به سامانه‌های استعلام. اگر می‌خواهید بدانید این سرویس برای فرایند ثبت‌نام شما مناسب است یا نه، کارشناسان ما پاسخ می‌دهند.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	    </div>
	    <div class="acts">
	      <a class="op-btn op-btn-cta" href="/api/">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/></svg>
	        <?php echo esc_html( uid_section_val( 'opsecurity', 'ctx_btn_text', __( 'وب‌سرویس احراز هویت تصویری', 'uid-theme' ) ) ); ?></a>
	      <a class="op-btn op-btn-ghost-d" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>">
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>
	        <span class="mono"><?php echo esc_html( uid_phone_raw() ); ?></span></a>
	      <p class="tiny"><?php echo esc_html( uid_section_val( 'opsecurity', 'ctx_note', __( 'مشاوره رایگان — بدون تعهد خرید', 'uid-theme' ) ) ); ?></p>
	    </div>
	  </div>
	</div>
	<?php
	uid_op_fold_close();
}

/* =====================================================================
 * ۱۰) کلام آخر — بیرون از سیستم فصل تاخوردنی، همیشه در دسترس
 * ===================================================================== */
function uid_render_section_opfinal() {
	$tag = uid_section_tag( 'opfinal', 'h2' );
	?>
	<section class="sec" id="final" style="padding-block-start:clamp(40px,5vw,70px)">
	  <div class="op-wrap">
	    <div class="col">
	      <div class="closing rv">
	        <span class="op-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'opfinal', 'eyebrow', __( 'کلام آخر', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sub" style="margin-block:14px 12px">'; ?><?php echo wp_kses( uid_section_val( 'opfinal', 'heading', __( 'چرا فناوری <span class="lat">OCR</span> کلید آینده دیجیتال ماست؟', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	        <div class="prose">
	          <p><?php echo wp_kses( uid_section_val( 'opfinal', 'p', __( 'تصور کنید دنیایی که در آن هر کاغذ، رسید یا مدرک فیزیکی، تنها با یک نگاه دوربین به داده‌ای زنده و هوشمند تبدیل می‌شود؛ این دقیقاً همان جادویی است که <span class="lat">OCR</span> در زندگی و کسب‌وکار ما رقم زده است. این فناوری نه‌تنها مانع هدررفت زمان برای تایپ‌های دستی می‌شود، بلکه امنیت و دقت را در احراز هویت دیجیتال به سطحی تازه رسانده است.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	        </div>
	        <div class="q">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/></svg>
	          <p><?php echo wp_kses( uid_section_val( 'opfinal', 'q_text', __( 'حالا که با زوایای مختلف این ابزار قدرتمند آشنا شدید، به نظر شما بزرگ‌ترین چالش یا بهترین کاربرد <span class="lat">OCR</span> در زندگی روزمره چیست؟ در بخش دیدگاه‌ها بنویسید که دوست دارید در کدام بخش از کسب‌وکار خود از این تکنولوژی استفاده کنید.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۱) سوالات متداول
 * ===================================================================== */
function uid_default_op_faq() {
	return array(
		array(
			'question' => __( 'OCR چیست؟', 'uid-theme' ),
			'answer'   => __( 'OCR فناوری تشخیص متن از تصویر است که متن موجود در عکس، اسکن و PDF را به متن قابل ویرایش تبدیل می‌کند.', 'uid-theme' ),
		),
		array(
			'question' => __( 'OCR مخفف چیست؟', 'uid-theme' ),
			'answer'   => __( 'OCR مخفف Optical Character Recognition است و به معنی تشخیص نوری کاراکتر می‌باشد.', 'uid-theme' ),
		),
		array(
			'question' => __( 'OCR چگونه کار می‌کند؟', 'uid-theme' ),
			'answer'   => __( 'ابتدا تصویر آماده‌سازی می‌شود، سپس متن شناسایی و در نهایت خروجی به متن دیجیتال تبدیل می‌شود.', 'uid-theme' ),
		),
		array(
			'question' => __( 'آیا OCR فارسی دقیق است؟', 'uid-theme' ),
			'answer'   => __( 'بله، اما دقت آن به کیفیت تصویر، فونت، نور و اتصال حروف فارسی وابسته است.', 'uid-theme' ),
		),
	);
}
function uid_render_section_opfaq() {
	$tag   = uid_section_tag( 'opfaq', 'h2' );
	$items = uid_section_val( 'opfaq', 'items', uid_default_op_faq() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" id="faq" style="padding-block-start:clamp(34px,4.4vw,56px)">
	  <div class="op-wrap">
	    <div class="col">
	      <div class="sec-head rule">
	        <span class="op-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'opfaq', 'eyebrow', __( 'پرسش و پاسخ', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo wp_kses( uid_section_val( 'opfaq', 'heading', __( 'سوالات متداول درباره <span class="lat">OCR</span>', 'uid-theme' ) ), uid_op_kses_rich() ); ?><?php echo '</' . $tag . '>'; ?>
	      </div>
	      <?php if ( $items ) : ?>
	      <div class="faq">
	        <?php foreach ( $items as $it ) : ?>
	        <div class="faq-i">
	          <button class="faq-q" type="button" aria-expanded="false"><span class="fq-tx"><?php echo wp_kses( $it['question'] ?? '', uid_op_kses_rich() ); ?></span><span class="pm" aria-hidden="true"></span></button>
	          <div class="faq-a"><p><?php echo wp_kses( $it['answer'] ?? '', uid_op_kses_rich() ); ?></p></div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <?php endif; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ۱۲) بند تماس نهایی (فرم مشاوره) — تنها فرم صفحه؛ بدون فوریت/کمیابی ساختگی،
 * دقیقاً مطابق یادداشت خودِ mockup («این صفحه آموزشی است، نه فروش»)
 * ===================================================================== */
function uid_default_op_lead_trust() {
	return implode( "\n", array(
		__( 'مشاوره رایگان، بدون تعهد', 'uid-theme' ),
		__( 'کلید آزمایشی و سندباکس پیش از قرارداد', 'uid-theme' ),
		__( 'مستندات فنی کامل', 'uid-theme' ),
		__( 'پشتیبانی اختصاصی سازمانی', 'uid-theme' ),
	) );
}
function uid_default_op_lead_biztypes() {
	return implode( "\n", array(
		__( 'ثبت‌نام و خدمات مالی', 'uid-theme' ),
		__( 'درگاه و پلتفرم پرداخت', 'uid-theme' ),
		__( 'کیف پول دیجیتال', 'uid-theme' ),
		__( 'صرافی رمزارز', 'uid-theme' ),
		__( 'سامانه اعتباری و لندتک', 'uid-theme' ),
		__( 'مارکت‌پلیس', 'uid-theme' ),
		__( 'کارگزاری بورس', 'uid-theme' ),
		__( 'سایر کسب‌وکارها', 'uid-theme' ),
	) );
}
function uid_render_section_oplead() {
	$trust    = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'oplead', 'trust_items', uid_default_op_lead_trust() ) ) ) );
	$biztypes = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'oplead', 'biztype_options', uid_default_op_lead_biztypes() ) ) ) );
	?>
	<section class="sec" style="padding-block-start:clamp(34px,4.4vw,56px)" id="lead">
	  <div class="op-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="op-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'oplead', 'eyebrow', __( 'مشاوره رایگان', 'uid-theme' ) ) ); ?></span>
	          <h2><?php echo esc_html( uid_section_val( 'oplead', 'heading', __( 'سوالی درباره احراز هویت دیجیتال دارید؟', 'uid-theme' ) ) ); ?></h2>
	          <p><?php echo wp_kses( uid_section_val( 'oplead', 'p', __( 'این مقاله درباره خود فناوری <span class="lat">OCR</span> است، نه محصولی از یوآیدی. اما اگر در کسب‌وکارتان با احراز هویت غیرحضوری سروکار دارید — ثبت‌نام کاربر، تایید مدارک یا تطبیق چهره — کارشناسان یوآیدی می‌توانند بگویند کدام سرویس به کار شما می‌آید و کدام نه.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_op_ic_check(); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline svg ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'oplead', 'form_heading', __( 'درخواست مشاوره احراز هویت', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'oplead', 'form_hint', __( 'فقط چهار فیلد. کارشناس ما در ساعات کاری تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="ocr-pillar-article">
	            <div class="frow">
	              <div class="fld"><input name="name" type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" data-req>
	                <span class="err"><?php esc_html_e( 'نام را وارد کنید.', 'uid-theme' ); ?></span></div>
	              <div class="fld"><input name="phone" type="tel" inputmode="numeric" placeholder="<?php echo esc_attr( uid_fa_digits( '۰۹xxxxxxxxx' ) ); ?>" data-req data-tel>
	                <span class="err"><?php esc_html_e( 'شماره موبایل معتبر وارد کنید.', 'uid-theme' ); ?></span></div>
	            </div>
	            <div class="fld"><input name="business" type="text" placeholder="<?php esc_attr_e( 'نام کسب‌وکار', 'uid-theme' ); ?>" data-req>
	              <span class="err"><?php esc_html_e( 'نام کسب‌وکار را وارد کنید.', 'uid-theme' ); ?></span></div>
	            <div class="fld"><select name="biztype" data-req data-route>
	                <option value=""><?php esc_html_e( 'نوع کسب‌وکار…', 'uid-theme' ); ?></option>
	                <?php foreach ( $biztypes as $bt ) : ?>
	                <option value="<?php echo esc_attr( sanitize_title( $bt ) ); ?>"><?php echo esc_html( $bt ); ?></option>
	                <?php endforeach; ?>
	                <option value="ind"><?php esc_html_e( 'کاربر شخصی هستم (کسب‌وکار نیستم)', 'uid-theme' ); ?></option>
	              </select><span class="err"><?php esc_html_e( 'نوع کسب‌وکار را انتخاب کنید.', 'uid-theme' ); ?></span></div>
	            <div class="route-alert" id="routeAlert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>
	              <span><?php echo wp_kses( uid_section_val( 'oplead', 'route_alert_text', __( 'وب‌سرویس‌های یوآیدی به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی دنبال خدمات هویتی هستید، <a href="/sana/">احراز هویت ثنا</a> در دسترس شماست.', 'uid-theme' ) ), uid_op_kses_rich() ); ?></span></div>
	            <button class="op-btn op-btn-cta op-btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg><?php echo esc_html( uid_section_val( 'oplead', 'submit_text', __( 'ارسال درخواست و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
	              <span><?php echo esc_html( uid_section_val( 'oplead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
	              <span><?php echo esc_html( uid_section_val( 'oplead', 'success_title', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری:', 'uid-theme' ) ) ); ?> <span class="mono"><?php echo esc_html( uid_phone_raw() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/**
 * مودال گفت‌وگوی سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز
 * می‌کنند؛ باز/بسته‌شدنش سراسری در assets/js/main.js مدیریت می‌شود — درست مثل
 * uid_render_ab_quick_modal() در inc/about-us-page.php. کاملاً ثابت است.
 */
function uid_render_op_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'درخواست مشاوره احراز هویت', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'اگر می‌خواهید بدانید احراز هویت غیرحضوری برای فرایند ثبت‌نام کسب‌وکار شما چطور پیاده‌سازی می‌شود، شماره‌تان را بگذارید تا کارشناس یوآیدی تماس بگیرد. مشاوره رایگان است و تعهدی ایجاد نمی‌کند.', 'uid-theme' ); ?></p>
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
	      <option><?php esc_html_e( 'ثبت‌نام و خدمات مالی', 'uid-theme' ); ?></option><option><?php esc_html_e( 'درگاه و پلتفرم پرداخت', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'کیف پول دیجیتال', 'uid-theme' ); ?></option><option><?php esc_html_e( 'صرافی رمزارز', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سامانه اعتباری و لندتک', 'uid-theme' ); ?></option><option><?php esc_html_e( 'مارکت‌پلیس', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'کارگزاری بورس', 'uid-theme' ); ?></option><option><?php esc_html_e( 'بانک، نئوبانک یا فین‌تک', 'uid-theme' ); ?></option>
	      <option><?php esc_html_e( 'سایر کسب‌وکارها', 'uid-theme' ); ?></option>
	    </select></div>
	  <button class="op-btn op-btn-cta op-btn-block" type="button" data-submit><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'ارسال و دریافت مشاوره رایگان', 'uid-theme' ); ?></button>
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
function uid_get_op_page_id() {
	$page_id = (int) get_option( 'uid_op_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_OP_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_op_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_op_page() {
	if ( uid_get_op_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'OCR چیست؟', 'uid-theme' ),
		'post_name'   => 'ocr-pillar-article',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_OP_TEMPLATE );
	update_option( 'uid_op_page_id', $page_id );
}
add_action( 'after_switch_theme', 'uid_ensure_op_page' );
add_action( 'admin_init', 'uid_ensure_op_page' );

function uid_register_op_slug_setting() {
	register_setting( 'uid_op_group', 'uid_op_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_op_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_op_page_slug_section', '', '__return_false', 'uid_op_layout' );
	add_settings_field( 'uid_op_page_slug', __( 'آدرس (اسلاگ) صفحه مقاله OCR', 'uid-theme' ), 'uid_field_op_page_slug', 'uid_op_layout', 'uid_op_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_op_slug_setting' );

function uid_field_op_page_slug( $args ) {
	$page_id = uid_get_op_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_op_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_op_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه مقاله OCR هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_op_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_op_page_id();

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
function uid_register_op_settings() {
	register_setting( 'uid_op_group', 'uid_op_layout', array(
		'sanitize_callback' => 'uid_sanitize_op_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_op_layout_main', '', '__return_false', 'uid_op_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_op_layout', 'uid_op_layout_main', array(
		'option_name' => 'uid_op_layout', 'registry_fn' => 'uid_op_sections_registry', 'layout_fn' => 'uid_get_op_layout',
	) );

	/* ---------------- ۱) هیرو مقاله ---------------- */
	register_setting( 'uid_op_group', 'uid_section_ophero', array( 'sanitize_callback' => 'uid_sanitize_section_ophero', 'default' => array() ) );
	add_settings_section( 'uid_section_ophero_main', '', '__return_false', 'uid_section_ophero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'eyebrow', 'default' => 'راهنمای جامع فناوری' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی H1 (تگ span.lat مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'heading', 'default' => 'OCR چیست؟ آشنایی با فناوری تشخیص متن از تصویر' ) );
	add_settings_field( 'lede', __( 'توضیح زیر عنوان (تگ span.lat مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'lede', 'default' => 'بعد از دیدن یک عکس، اسکن یا حتی تصویر یک کارت شناسایی، احتمالاً برایتان سؤال شده که سیستم‌ها چگونه می‌توانند متن داخل آن را بخوانند و به داده‌ای قابل استفاده تبدیل کنند؛ اینجاست که می‌فهمیم OCR چیست و چرا این فناوری به یکی از ابزارهای مهم دنیای دیجیتال تبدیل شده است. در این مقاله می‌بینیم OCR مخفف چیست، چگونه کار می‌کند، چه انواعی دارد، در زندگی روزمره و فرایندهای امنیت و احراز هویت چه نقشی دارد و دقت آن به چه چیزهایی وابسته است.', 'rows' => 5 ) );
	add_settings_field( 'crumb_current', __( 'متن فعلیِ مسیر صفحه (بردکرامب)', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'crumb_current', 'default' => 'OCR چیست؟' ) );
	add_settings_field( 'meta_read_min', __( 'عدد دقیقه مطالعه', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'meta_read_min', 'default' => '۱۴' ) );
	add_settings_field( 'meta_sections', __( 'عدد تعداد بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'meta_sections', 'default' => '۷' ) );
	add_settings_field( 'meta_updated', __( 'برچسب به‌روزرسانی', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'meta_updated', 'default' => 'به‌روزرسانی: شهریور ۱۴۰۵' ) );
	add_settings_field( 'meta_level', __( 'برچسب سطح مقاله', 'uid-theme' ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'meta_level', 'default' => 'سطح: مقدماتی تا متوسط' ) );
	foreach ( array(
		1 => array( 'معنی و معادل فارسی', 'Optical Character Recognition — تشخیص نوری کاراکتر' ),
		2 => array( 'شش مرحله پردازش', 'از دریافت تصویر تا بازبینی و اصلاح خطاها' ),
		3 => array( 'پنج نوع OCR', 'چاپی، دست‌نویس، ابری، آفلاین و سندمحور' ),
		4 => array( 'جای OCR در KYC', 'خواندن مدارک در احراز هویت غیرحضوری' ),
	) as $n => $row ) {
		add_settings_field( 'kcard' . $n . '_title', sprintf( /* translators: %d: card number */ __( 'کارت کلیدی %d — عنوان', 'uid-theme' ), $n ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'kcard' . $n . '_title', 'default' => $row[0] ) );
		add_settings_field( 'kcard' . $n . '_text', sprintf( /* translators: %d: card number */ __( 'کارت کلیدی %d — توضیح (تگ span.lat مجاز است)', 'uid-theme' ), $n ), 'uid_field_text', 'uid_section_ophero', 'uid_section_ophero_main', array( 'group' => 'uid_section_ophero', 'key' => 'kcard' . $n . '_text', 'default' => $row[1] ) );
	}

	/* ---------------- ۲) کپسول ۳۰ ثانیه‌ای ---------------- */
	register_setting( 'uid_op_group', 'uid_section_optldr', array( 'sanitize_callback' => 'uid_sanitize_section_optldr', 'default' => array() ) );
	add_settings_section( 'uid_section_optldr_main', '', '__return_false', 'uid_section_optldr' );
	add_settings_field( 'heading', __( 'عنوان کپسول', 'uid-theme' ), 'uid_field_text', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'heading', 'default' => 'خلاصه ۳۰ ثانیه‌ای' ) );
	add_settings_field( 'sub', __( 'زیرنویس کوچک کنار عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'sub', 'default' => 'اگر وقت ندارید' ) );
	add_settings_field( 'items', __( 'موارد فهرست (هر خط یک مورد؛ تگ‌های b و span مجازند — ممکن است هنگام ذخیره حذف شوند)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'items', 'default' => implode( "\n", uid_default_op_tldr_items() ), 'rows' => 6 ) );
	add_settings_field( 'call_btn_text', __( 'متن دکمه تماس (پیش از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'call_btn_text', 'default' => 'مشاوره احراز هویت:' ) );
	add_settings_field( 'ghost_btn_text', __( 'متن دکمه دوم (پرش به فصل «چگونه کار می‌کند»)', 'uid-theme' ), 'uid_field_text', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'ghost_btn_text', 'default' => 'رفتن به مراحل کار OCR' ) );
	add_settings_field( 'more_btn_text', __( 'متن دکمه «فهرست کامل بخش‌ها»', 'uid-theme' ), 'uid_field_text', 'uid_section_optldr', 'uid_section_optldr_main', array( 'group' => 'uid_section_optldr', 'key' => 'more_btn_text', 'default' => 'فهرست کامل بخش‌ها' ) );

	/* ---------------- ۳) فصل ۱ — OCR مخفف چیست ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opwhat', array( 'sanitize_callback' => 'uid_sanitize_section_opwhat', 'default' => array() ) );
	add_settings_section( 'uid_section_opwhat_main', '', '__return_false', 'uid_section_opwhat' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'eyebrow', 'default' => 'بخش ۱' ) );
	add_settings_field( 'heading', __( 'عنوان فصل (تگ span.lat مجاز است)', 'uid-theme' ), 'uid_field_text', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'heading', 'default' => 'OCR مخفف چیست' ) );
	add_settings_field( 'p1', __( 'پاراگراف اول (تگ span.lat مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'p1', 'rows' => 5, 'default' => 'OCR مخفف Optical Character Recognition است؛ اصطلاحی که در فارسی معمولاً به «تشخیص نوری کاراکتر» یا «تشخیص متن از تصویر» ترجمه می‌شود.' ) );
	add_settings_field( 'pull', __( 'جمله برجسته (pull quote)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'pull', 'default' => 'تا وقتی متن داخل یک عکس است، فقط دیده می‌شود؛ بعد از OCR می‌شود آن را جست‌وجو کرد، ویرایش کرد و به سیستم‌های دیگر فرستاد.' ) );
	add_settings_field( 'p2', __( 'پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'p2', 'rows' => 5, 'default' => 'در بسیاری از منابع، OCR فقط به‌عنوان ابزاری برای تبدیل عکس به متن معرفی می‌شود؛ اما مفهوم آن گسترده‌تر است.' ) );
	add_settings_field( 'callout_text', __( 'متن جعبه یادداشت پایانی (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opwhat', 'uid_section_opwhat_main', array( 'group' => 'uid_section_opwhat', 'key' => 'callout_text', 'default' => 'یک تفکیک ساده: «تبدیل عکس به متن» نتیجه‌ای است که کاربر می‌بیند؛ OCR فرایندی است که آن نتیجه را می‌سازد.' ) );

	/* ---------------- ۴) فصل ۲ — چگونه کار می‌کند ---------------- */
	register_setting( 'uid_op_group', 'uid_section_ophow', array( 'sanitize_callback' => 'uid_sanitize_section_ophow', 'default' => array() ) );
	add_settings_section( 'uid_section_ophow_main', '', '__return_false', 'uid_section_ophow' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'eyebrow', 'default' => 'بخش ۲' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'heading', 'default' => 'OCR چگونه کار می‌کند؟' ) );
	add_settings_field( 'p1', __( 'پاراگراف اول', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'p1', 'rows' => 5, 'default' => 'OCR با تحلیل تصویر و شناسایی الگوهای حروف، متن موجود در عکس یا سند اسکن‌شده را به متن دیجیتال تبدیل می‌کند.' ) );
	add_settings_field( 'p2', __( 'پاراگراف دوم', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'p2', 'rows' => 4, 'default' => 'مثلاً اگر از یک فاکتور عکس بگیرید، OCR ابتدا کیفیت عکس را بهتر می‌کند، بعد ناحیه‌های متنی را پیدا می‌کند و در نهایت اطلاعاتی مثل نام کالا، مبلغ و تاریخ را استخراج می‌کند.' ) );
	add_settings_field( 'pipe_notice', '', 'uid_field_notice', 'uid_section_ophow', 'uid_section_ophow_main', array( 'text' => __( 'شبیه‌ساز شش‌مرحله‌ای پایین همین فصل (فاکتور نمونه + دکمه‌های پخش/بازنشانی) کاملاً ثابت است و از پیشخوان قابل‌ویرایش نیست — برای تغییر، uid_render_section_ophow() در inc/ocr-pillar-article-page.php را ویرایش کنید.', 'uid-theme' ) ) );
	add_settings_field( 'p3', __( 'پاراگراف پایانی (بعد از شبیه‌ساز)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_ophow', 'uid_section_ophow_main', array( 'group' => 'uid_section_ophow', 'key' => 'p3', 'rows' => 4, 'default' => 'در مجموع، اگر بخواهیم ساده بگوییم OCR چگونه کار می‌کند، باید گفت این فناوری با آماده‌سازی تصویر، تشخیص متن و تبدیل آن به داده دیجیتال عمل می‌کند.' ) );

	/* ---------------- ۵) فصل ۳ — عوامل دقت ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opaccuracy', array( 'sanitize_callback' => 'uid_sanitize_section_opaccuracy', 'default' => array() ) );
	add_settings_section( 'uid_section_opaccuracy_main', '', '__return_false', 'uid_section_opaccuracy' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'eyebrow', 'default' => 'بخش ۳' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'heading', 'default' => 'چه عواملی بر دقت OCR اثر می‌گذارند' ) );
	add_settings_field( 'intro_p', __( 'پاراگراف مقدمه', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'intro_p', 'default' => 'دقت OCR همیشه یکسان نیست و به عوامل مختلفی بستگی دارد.' ) );
	add_settings_field( 'deflist', __( 'فهرست عوامل موثر بر دقت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array(
		'group' => 'uid_section_opaccuracy', 'key' => 'deflist', 'default' => uid_default_op_accuracy_deflist(), 'add_label' => __( 'افزودن عامل', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'k', 'type' => 'text', 'label' => __( 'عنوان عامل', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'v', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'sim_notice', '', 'uid_field_notice', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'text' => __( 'شبیه‌ساز دقت پایین همین فصل (کلیدهای تصویر تار/نور کم/سند کج/... + شاخص خوانایی) کاملاً ثابت است و از پیشخوان قابل‌ویرایش نیست — برای تغییر، uid_render_section_opaccuracy() در inc/ocr-pillar-article-page.php و assets/js/ocr-pillar-article-page.js را ویرایش کنید.', 'uid-theme' ) ) );
	add_settings_field( 'farsi_heading', __( 'عنوان بخش «عوامل ویژه فارسی»', 'uid-theme' ), 'uid_field_text', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'farsi_heading', 'default' => 'در OCR فارسی، چند عامل اهمیت بیشتری دارند' ) );
	add_settings_field( 'farsi_items', __( 'فهرست عوامل ویژه فارسی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array(
		'group' => 'uid_section_opaccuracy', 'key' => 'farsi_items', 'default' => uid_default_op_accuracy_farsi(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'lead', 'type' => 'text', 'label' => __( 'عبارت پررنگ ابتدایی', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'ادامه توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'callout_text', __( 'متن جعبه یادداشت پایانی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opaccuracy', 'uid_section_opaccuracy_main', array( 'group' => 'uid_section_opaccuracy', 'key' => 'callout_text', 'default' => 'به همین دلیل، مقایسه دقت OCR میان زبان‌ها معنای چندانی ندارد.' ) );

	/* ---------------- ۶) فصل ۴ — قابلیت و خروجی ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opoutput', array( 'sanitize_callback' => 'uid_sanitize_section_opoutput', 'default' => array() ) );
	add_settings_section( 'uid_section_opoutput_main', '', '__return_false', 'uid_section_opoutput' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'eyebrow', 'default' => 'بخش ۴' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'heading', 'default' => 'قابلیت OCR چیست و چه کاری انجام می‌دهد؟' ) );
	add_settings_field( 'intro_p', __( 'پاراگراف مقدمه', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'intro_p', 'default' => 'OCR فقط ابزاری برای خواندن متن از روی تصویر نیست.' ) );
	add_settings_field( 'shape_heading', __( 'زیرعنوان «خروجی چه شکلی است»', 'uid-theme' ), 'uid_field_text', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'shape_heading', 'default' => 'خروجی OCR چه شکلی است' ) );
	add_settings_field( 'shape_p', __( 'پاراگراف زیر آن زیرعنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'shape_p', 'default' => 'خروجی OCR بسته به نوع ابزار و هدف استفاده می‌تواند ساده یا پیشرفته باشد.' ) );
	add_settings_field( 'deflist', __( 'فهرست شکل‌های خروجی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_opoutput', 'uid_section_opoutput_main', array(
		'group' => 'uid_section_opoutput', 'key' => 'deflist', 'default' => uid_default_op_output_deflist(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'k', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'v', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'pull', __( 'جمله برجسته', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'pull', 'default' => 'به زبان ساده، OCR می‌تواند فقط متن را تحویل بدهد یا متن را همراه با ساختار و موقعیت آن ارائه کند.' ) );
	add_settings_field( 'solves_heading', __( 'زیرعنوان «چه مسائلی را حل می‌کند»', 'uid-theme' ), 'uid_field_text', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'solves_heading', 'default' => 'OCR چه مسائلی را حل می‌کند' ) );
	add_settings_field( 'solves_p', __( 'پاراگراف زیر آن زیرعنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opoutput', 'uid_section_opoutput_main', array( 'group' => 'uid_section_opoutput', 'key' => 'solves_p', 'default' => 'اگر بخواهیم دقیق‌تر بگوییم قابلیت OCR چیست و چه کاری انجام می‌دهد، باید به مسئله‌هایی اشاره کنیم که این فناوری حل می‌کند.' ) );
	add_settings_field( 'solves_items', __( 'فهرست شماره‌دار مسائل حل‌شده', 'uid-theme' ), 'uid_field_repeater', 'uid_section_opoutput', 'uid_section_opoutput_main', array(
		'group' => 'uid_section_opoutput', 'key' => 'solves_items', 'default' => uid_default_op_output_solves(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'lead', 'type' => 'text', 'label' => __( 'عبارت پررنگ ابتدایی', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'ادامه توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ۷) فصل ۵ — انواع OCR ---------------- */
	register_setting( 'uid_op_group', 'uid_section_optypes', array( 'sanitize_callback' => 'uid_sanitize_section_optypes', 'default' => array() ) );
	add_settings_section( 'uid_section_optypes_main', '', '__return_false', 'uid_section_optypes' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_optypes', 'uid_section_optypes_main', array( 'group' => 'uid_section_optypes', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_optypes', 'uid_section_optypes_main', array( 'group' => 'uid_section_optypes', 'key' => 'eyebrow', 'default' => 'بخش ۵' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_optypes', 'uid_section_optypes_main', array( 'group' => 'uid_section_optypes', 'key' => 'heading', 'default' => 'انواع OCR' ) );
	add_settings_field( 'intro_p', __( 'پاراگراف مقدمه', 'uid-theme' ), 'uid_field_textarea', 'uid_section_optypes', 'uid_section_optypes_main', array( 'group' => 'uid_section_optypes', 'key' => 'intro_p', 'default' => 'فناوری OCR فقط یک مدل ثابت ندارد و بسته به نوع متن، محل اجرا و هدف استفاده، در چند دسته مختلف قرار می‌گیرد.' ) );
	add_settings_field( 'tsel_notice', '', 'uid_field_notice', 'uid_section_optypes', 'uid_section_optypes_main', array( 'text' => __( 'انتخابگر پنج‌گانه انواع OCR و جدول مقایسه سریع زیر آن کاملاً ثابت‌اند و از پیشخوان قابل‌ویرایش نیستند — برای تغییر، uid_render_section_optypes() در inc/ocr-pillar-article-page.php را ویرایش کنید.', 'uid-theme' ) ) );

	/* ---------------- ۸) فصل ۶ — کاربرد روزمره ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opdaily', array( 'sanitize_callback' => 'uid_sanitize_section_opdaily', 'default' => array() ) );
	add_settings_section( 'uid_section_opdaily_main', '', '__return_false', 'uid_section_opdaily' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'eyebrow', 'default' => 'بخش ۶' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'heading', 'default' => 'کاربرد فناوری OCR در زندگی روزمره' ) );
	add_settings_field( 'intro_p', __( 'پاراگراف مقدمه', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'intro_p', 'default' => 'فناوری OCR فراتر از کاربردهای پیچیده سازمانی، در کارهای روزمره ما نیز حضور دارد.' ) );
	$daily_defaults = uid_default_op_daily_cards();
	foreach ( $daily_defaults as $n => $d ) {
		add_settings_field( 'card' . $n . '_title', sprintf( /* translators: %d: card number */ __( 'کارت %d — عنوان', 'uid-theme' ), $n ), 'uid_field_text', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'card' . $n . '_title', 'default' => $d['title'] ) );
		add_settings_field( 'card' . $n . '_text', sprintf( /* translators: %d: card number */ __( 'کارت %d — توضیح', 'uid-theme' ), $n ), 'uid_field_textarea', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'card' . $n . '_text', 'default' => $d['text'] ) );
		add_settings_field( 'card' . $n . '_items', sprintf( /* translators: %d: card number */ __( 'کارت %d — فهرست (هر خط یک مورد)', 'uid-theme' ), $n ), 'uid_field_textarea', 'uid_section_opdaily', 'uid_section_opdaily_main', array( 'group' => 'uid_section_opdaily', 'key' => 'card' . $n . '_items', 'default' => $d['items'], 'rows' => 4 ) );
	}

	/* ---------------- ۹) فصل ۷ — امنیت و احراز هویت ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opsecurity', array( 'sanitize_callback' => 'uid_sanitize_section_opsecurity', 'default' => array() ) );
	add_settings_section( 'uid_section_opsecurity_main', '', '__return_false', 'uid_section_opsecurity' );
	add_settings_field( 'title_tag', __( 'تگ عنوان فصل', 'uid-theme' ), 'uid_field_select', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب شماره بخش', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'eyebrow', 'default' => 'بخش ۷' ) );
	add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'heading', 'default' => 'کاربرد OCR در امنیت و احراز هویت' ) );
	add_settings_field( 'intro_p', __( 'پاراگراف مقدمه', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'intro_p', 'default' => 'فناوری OCR در حوزه امنیت نقش کلیدی در کاهش خطای انسانی و سرعت‌بخشیدن به تایید هویت دارد.' ) );
	$sec_defaults = uid_default_op_security_cards();
	foreach ( $sec_defaults as $n => $d ) {
		add_settings_field( 'card' . $n . '_title', sprintf( /* translators: %d: card number */ __( 'کارت %d — عنوان', 'uid-theme' ), $n ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'card' . $n . '_title', 'default' => $d['title'] ) );
		add_settings_field( 'card' . $n . '_text', sprintf( /* translators: %d: card number */ __( 'کارت %d — توضیح', 'uid-theme' ), $n ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'card' . $n . '_text', 'default' => $d['text'] ) );
		add_settings_field( 'card' . $n . '_items', sprintf( /* translators: %d: card number */ __( 'کارت %d — فهرست (هر خط یک مورد؛ تگ‌های b/a مجازند — ممکن است هنگام ذخیره حذف شوند)', 'uid-theme' ), $n ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'card' . $n . '_items', 'default' => $d['items'], 'rows' => 4 ) );
	}
	add_settings_field( 'readnext_label', __( 'برچسب کوچک «مقاله پیشنهادی»', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'readnext_label', 'default' => 'مقاله پیشنهادی' ) );
	add_settings_field( 'readnext_title', __( 'عنوان مقاله پیشنهادی', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'readnext_title', 'default' => 'KYC چیست و چرا کسب‌وکارهای مالی به آن نیاز دارند؟' ) );
	add_settings_field( 'readnext_url', __( 'آدرس مقاله پیشنهادی', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array(
		'group' => 'uid_section_opsecurity', 'key' => 'readnext_url', 'default' => 'https://blog.u-id.net/kyc/',
		'desc'  => __( 'توجه: این آدرس یک جانگهدار است — پیش از انتشار باید با آدرس واقعی مطلب KYC در blog.u-id.net هماهنگ شود.', 'uid-theme' ),
	) );
	add_settings_field( 'webservice_heading', __( 'زیرعنوان «استفاده در وب‌سرویس احراز هویت»', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'webservice_heading', 'default' => 'استفاده در وب‌سرویس احراز هویت' ) );
	add_settings_field( 'webservice_p', __( 'پاراگراف زیر آن زیرعنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'webservice_p', 'default' => 'ترکیب OCR با وب‌سرویس‌ها، امکان ایجاد سیستم‌های تایید هویت هوشمند و کاملاً خودکار را برای توسعه‌دهندگان فراهم می‌کند.' ) );
	add_settings_field( 'ctx_eyebrow', __( 'برچسب کوچک بالای CTA', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'ctx_eyebrow', 'default' => 'از این مقاله به سرویس' ) );
	add_settings_field( 'ctx_heading', __( 'عنوان CTA', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'ctx_heading', 'default' => 'برای پیاده‌سازی تایید هویت در اپلیکیشن یا وب‌سایت خود، از وب‌سرویس احراز هویت یوآیدی استفاده کنید' ) );
	add_settings_field( 'ctx_text', __( 'توضیح CTA', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'ctx_text', 'default' => 'یوآیدی وب‌سرویس احراز هویت تصویری ارائه می‌دهد: تطبیق چهره کاربر با تصویر مرجع و تشخیص زنده‌بودن.' ) );
	add_settings_field( 'ctx_btn_text', __( 'متن دکمه CTA (لینک ثابت به /api/)', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'ctx_btn_text', 'default' => 'وب‌سرویس احراز هویت تصویری' ) );
	add_settings_field( 'ctx_note', __( 'یادداشت کوچک زیر دکمه‌های CTA', 'uid-theme' ), 'uid_field_text', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'group' => 'uid_section_opsecurity', 'key' => 'ctx_note', 'default' => 'مشاوره رایگان — بدون تعهد خرید' ) );
	add_settings_field( 'ctx_phone_notice', '', 'uid_field_notice', 'uid_section_opsecurity', 'uid_section_opsecurity_main', array( 'text' => __( 'شماره تلفن دکمه دوم CTA از تب «اطلاعات تماس» (تنظیمات سراسری قالب) خوانده می‌شود.', 'uid-theme' ) ) );

	/* ---------------- ۱۰) کلام آخر ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opfinal', array( 'sanitize_callback' => 'uid_sanitize_section_opfinal', 'default' => array() ) );
	add_settings_section( 'uid_section_opfinal_main', '', '__return_false', 'uid_section_opfinal' );
	add_settings_field( 'title_tag', __( 'تگ عنوان', 'uid-theme' ), 'uid_field_select', 'uid_section_opfinal', 'uid_section_opfinal_main', array( 'group' => 'uid_section_opfinal', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_opfinal', 'uid_section_opfinal_main', array( 'group' => 'uid_section_opfinal', 'key' => 'eyebrow', 'default' => 'کلام آخر' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_opfinal', 'uid_section_opfinal_main', array( 'group' => 'uid_section_opfinal', 'key' => 'heading', 'default' => 'چرا فناوری OCR کلید آینده دیجیتال ماست؟' ) );
	add_settings_field( 'p', __( 'پاراگراف اصلی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opfinal', 'uid_section_opfinal_main', array( 'group' => 'uid_section_opfinal', 'key' => 'p', 'default' => 'تصور کنید دنیایی که در آن هر کاغذ، رسید یا مدرک فیزیکی، تنها با یک نگاه دوربین به داده‌ای زنده و هوشمند تبدیل می‌شود.' ) );
	add_settings_field( 'q_text', __( 'متن سوال پایانی (کادر نقل‌قول)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_opfinal', 'uid_section_opfinal_main', array( 'group' => 'uid_section_opfinal', 'key' => 'q_text', 'default' => 'حالا که با زوایای مختلف این ابزار قدرتمند آشنا شدید، به نظر شما بزرگ‌ترین چالش یا بهترین کاربرد OCR در زندگی روزمره چیست؟' ) );

	/* ---------------- ۱۱) سوالات متداول ---------------- */
	register_setting( 'uid_op_group', 'uid_section_opfaq', array( 'sanitize_callback' => 'uid_sanitize_section_opfaq', 'default' => array() ) );
	add_settings_section( 'uid_section_opfaq_main', '', '__return_false', 'uid_section_opfaq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان', 'uid-theme' ), 'uid_field_select', 'uid_section_opfaq', 'uid_section_opfaq_main', array( 'group' => 'uid_section_opfaq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_opfaq', 'uid_section_opfaq_main', array( 'group' => 'uid_section_opfaq', 'key' => 'eyebrow', 'default' => 'پرسش و پاسخ' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_opfaq', 'uid_section_opfaq_main', array( 'group' => 'uid_section_opfaq', 'key' => 'heading', 'default' => 'سوالات متداول درباره OCR' ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_opfaq', 'uid_section_opfaq_main', array(
		'group' => 'uid_section_opfaq', 'key' => 'items', 'default' => uid_default_op_faq(), 'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ۱۲) بند تماس نهایی ---------------- */
	register_setting( 'uid_op_group', 'uid_section_oplead', array( 'sanitize_callback' => 'uid_sanitize_section_oplead', 'default' => array() ) );
	add_settings_section( 'uid_section_oplead_main', '', '__return_false', 'uid_section_oplead' );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'eyebrow', 'default' => 'مشاوره رایگان' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'heading', 'default' => 'سوالی درباره احراز هویت دیجیتال دارید؟' ) );
	add_settings_field( 'p', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'p', 'default' => 'این مقاله درباره خود فناوری OCR است، نه محصولی از یوآیدی.' ) );
	add_settings_field( 'trust_items', __( 'نکات اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'trust_items', 'default' => uid_default_op_lead_trust(), 'rows' => 5 ) );
	add_settings_field( 'form_heading', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'form_heading', 'default' => 'درخواست مشاوره احراز هویت' ) );
	add_settings_field( 'form_hint', __( 'راهنمای فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'form_hint', 'default' => 'فقط چهار فیلد. کارشناس ما در ساعات کاری تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'biztype_options', 'default' => uid_default_op_lead_biztypes(), 'rows' => 9 ) );
	add_settings_field( 'route_alert_text', __( 'پیام هشدار برای «کاربر شخصی» (تگ a مجاز است — ممکن است هنگام ذخیره حذف شود)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'route_alert_text', 'default' => 'وب‌سرویس‌های یوآیدی به کسب‌وکارها ارائه می‌شود. اگر به‌صورت شخصی دنبال خدمات هویتی هستید، احراز هویت ثنا در دسترس شماست.' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'یادداشت حریم خصوصی زیر دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_title', __( 'پیام موفقیت (پیش از شماره تلفن)', 'uid-theme' ), 'uid_field_text', 'uid_section_oplead', 'uid_section_oplead_main', array( 'group' => 'uid_section_oplead', 'key' => 'success_title', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری:' ) );
}
add_action( 'admin_init', 'uid_register_op_settings' );

/* =====================================================================
 * توابع پاک‌سازی — یکی به‌ازای هر سکشن
 * ===================================================================== */
function uid_sanitize_section_ophero( $input ) {
	$out = array(
		'title_tag'     => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'       => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'       => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'lede'          => sanitize_textarea_field( $input['lede'] ?? '' ),
		'crumb_current' => sanitize_text_field( $input['crumb_current'] ?? '' ),
		'meta_read_min' => sanitize_text_field( $input['meta_read_min'] ?? '' ),
		'meta_sections' => sanitize_text_field( $input['meta_sections'] ?? '' ),
		'meta_updated'  => sanitize_text_field( $input['meta_updated'] ?? '' ),
		'meta_level'    => sanitize_text_field( $input['meta_level'] ?? '' ),
	);
	for ( $n = 1; $n <= 4; $n++ ) {
		$out[ 'kcard' . $n . '_title' ] = sanitize_text_field( $input[ 'kcard' . $n . '_title' ] ?? '' );
		$out[ 'kcard' . $n . '_text' ]  = sanitize_text_field( $input[ 'kcard' . $n . '_text' ] ?? '' );
	}
	return $out;
}

function uid_sanitize_section_optldr( $input ) {
	return array(
		'heading'        => sanitize_text_field( $input['heading'] ?? '' ),
		'sub'            => sanitize_text_field( $input['sub'] ?? '' ),
		'items'          => sanitize_textarea_field( $input['items'] ?? '' ),
		'call_btn_text'  => sanitize_text_field( $input['call_btn_text'] ?? '' ),
		'ghost_btn_text' => sanitize_text_field( $input['ghost_btn_text'] ?? '' ),
		'more_btn_text'  => sanitize_text_field( $input['more_btn_text'] ?? '' ),
	);
}

function uid_sanitize_section_opwhat( $input ) {
	return array(
		'title_tag'    => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'p1'           => sanitize_textarea_field( $input['p1'] ?? '' ),
		'pull'         => sanitize_textarea_field( $input['pull'] ?? '' ),
		'p2'           => sanitize_textarea_field( $input['p2'] ?? '' ),
		'callout_text' => sanitize_textarea_field( $input['callout_text'] ?? '' ),
	);
}

function uid_sanitize_section_ophow( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'p1'        => sanitize_textarea_field( $input['p1'] ?? '' ),
		'p2'        => sanitize_textarea_field( $input['p2'] ?? '' ),
		'p3'        => sanitize_textarea_field( $input['p3'] ?? '' ),
	);
}

function uid_sanitize_section_opaccuracy( $input ) {
	return array(
		'title_tag'    => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'      => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'      => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'intro_p'      => sanitize_textarea_field( $input['intro_p'] ?? '' ),
		'deflist'      => uid_sanitize_repeater_rows( $input['deflist'] ?? '[]', array(
			array( 'key' => 'k', 'type' => 'text', 'required' => true ),
			array( 'key' => 'v', 'type' => 'textarea' ),
		) ),
		'farsi_heading' => wp_kses( $input['farsi_heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'farsi_items'  => uid_sanitize_repeater_rows( $input['farsi_items'] ?? '[]', array(
			array( 'key' => 'lead', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
		'callout_text' => sanitize_textarea_field( $input['callout_text'] ?? '' ),
	);
}

function uid_sanitize_section_opoutput( $input ) {
	return array(
		'title_tag'      => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'        => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'        => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'intro_p'        => sanitize_textarea_field( $input['intro_p'] ?? '' ),
		'shape_heading'  => wp_kses( $input['shape_heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'shape_p'        => sanitize_textarea_field( $input['shape_p'] ?? '' ),
		'deflist'        => uid_sanitize_repeater_rows( $input['deflist'] ?? '[]', array(
			array( 'key' => 'k', 'type' => 'text', 'required' => true ),
			array( 'key' => 'v', 'type' => 'textarea' ),
		) ),
		'pull'           => sanitize_textarea_field( $input['pull'] ?? '' ),
		'solves_heading' => wp_kses( $input['solves_heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'solves_p'       => sanitize_textarea_field( $input['solves_p'] ?? '' ),
		'solves_items'   => uid_sanitize_repeater_rows( $input['solves_items'] ?? '[]', array(
			array( 'key' => 'lead', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_optypes( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'intro_p'   => sanitize_textarea_field( $input['intro_p'] ?? '' ),
	);
}

function uid_sanitize_section_opdaily( $input ) {
	$out = array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'intro_p'   => sanitize_textarea_field( $input['intro_p'] ?? '' ),
	);
	for ( $n = 1; $n <= 5; $n++ ) {
		$out[ 'card' . $n . '_title' ] = sanitize_text_field( $input[ 'card' . $n . '_title' ] ?? '' );
		$out[ 'card' . $n . '_text' ]  = sanitize_textarea_field( $input[ 'card' . $n . '_text' ] ?? '' );
		$out[ 'card' . $n . '_items' ] = sanitize_textarea_field( $input[ 'card' . $n . '_items' ] ?? '' );
	}
	return $out;
}

function uid_sanitize_section_opsecurity( $input ) {
	$out = array(
		'title_tag'          => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'            => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'            => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'intro_p'            => sanitize_textarea_field( $input['intro_p'] ?? '' ),
		'readnext_label'     => sanitize_text_field( $input['readnext_label'] ?? '' ),
		'readnext_title'     => wp_kses( $input['readnext_title'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'readnext_url'       => esc_url_raw( $input['readnext_url'] ?? '' ),
		'webservice_heading' => sanitize_text_field( $input['webservice_heading'] ?? '' ),
		'webservice_p'       => sanitize_textarea_field( $input['webservice_p'] ?? '' ),
		'ctx_eyebrow'        => sanitize_text_field( $input['ctx_eyebrow'] ?? '' ),
		'ctx_heading'        => wp_kses( $input['ctx_heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'ctx_text'           => wp_kses( $input['ctx_text'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'ctx_btn_text'       => sanitize_text_field( $input['ctx_btn_text'] ?? '' ),
		'ctx_note'           => sanitize_text_field( $input['ctx_note'] ?? '' ),
	);
	for ( $n = 1; $n <= 4; $n++ ) {
		$out[ 'card' . $n . '_title' ] = sanitize_text_field( $input[ 'card' . $n . '_title' ] ?? '' );
		$out[ 'card' . $n . '_text' ]  = sanitize_textarea_field( $input[ 'card' . $n . '_text' ] ?? '' );
		$out[ 'card' . $n . '_items' ] = sanitize_textarea_field( $input[ 'card' . $n . '_items' ] ?? '' );
	}
	return $out;
}

function uid_sanitize_section_opfinal( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'p'         => sanitize_textarea_field( $input['p'] ?? '' ),
		'q_text'    => sanitize_textarea_field( $input['q_text'] ?? '' ),
	);
}

function uid_sanitize_section_opfaq( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_oplead( $input ) {
	return array(
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'p'                => wp_kses( $input['p'] ?? '', array( 'span' => array( 'class' => true ) ) ),
		'trust_items'      => sanitize_textarea_field( $input['trust_items'] ?? '' ),
		'form_heading'     => sanitize_text_field( $input['form_heading'] ?? '' ),
		'form_hint'        => sanitize_text_field( $input['form_hint'] ?? '' ),
		'biztype_options'  => sanitize_textarea_field( $input['biztype_options'] ?? '' ),
		'route_alert_text' => sanitize_textarea_field( $input['route_alert_text'] ?? '' ),
		'submit_text'      => sanitize_text_field( $input['submit_text'] ?? '' ),
		'note_text'        => sanitize_text_field( $input['note_text'] ?? '' ),
		'success_title'    => sanitize_text_field( $input['success_title'] ?? '' ),
	);
}
