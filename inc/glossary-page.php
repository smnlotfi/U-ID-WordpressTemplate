<?php
/**
 * صفحه اختصاصی «واژه‌نامه اصطلاحات احراز هویت» — دقیقاً همان الگوی صفحات قبلی: برگه‌ی
 * واقعی خودکارساخته + قالب صفحه + سیستم سکشن قابل‌مدیریت از پیشخوان.
 * اسلاگ‌های سکشن با پیشوند «gx» نام‌گذاری شده‌اند تا در نام آپشن‌های wp_options با
 * سکشن‌های هم‌نام صفحات دیگر تداخل نکنند.
 *
 * چهار سکشن این صفحه (base/bio/fin/dev) سیستم «فصل تاخوردنی + خواننده فصل» موبایل
 * دارند — دقیقاً مثل inc/card-to-iban-page.php. فهرست ۳۰ اصطلاح هر فصل محتوای
 * تخصصی و به‌هم‌مرتبط است (دکمه‌های «اصطلاح مرتبط» به یکدیگر ارجاع می‌دهند) و از
 * پیشخوان قابل‌ویرایش نیست — دقیقاً مثل نمونه‌کدها/جدول پارامترهای فصل فنی صفحات
 * دیگر که با uid_field_notice به همین شکل مستند شده‌اند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_GLOSSARY_TEMPLATE', 'template-glossary.php' );

/* =====================================================================
 * رجیستری سکشن‌ها + ترتیب/نمایش
 * ===================================================================== */
function uid_gx_sections_registry() {
	return array(
		'gxhero'     => array( 'label' => __( 'هیرو + پیش‌نمایش فهرست', 'uid-theme' ),       'icon' => 'dashicons-search' ),
		'gxstats'    => array( 'label' => __( 'باند آمار کوتاه (۴ مورد)', 'uid-theme' ),      'icon' => 'dashicons-chart-bar' ),
		'gxtldr'     => array( 'label' => __( 'کپسول ۳۰ ثانیه‌ای (ویژه موبایل)', 'uid-theme' ), 'icon' => 'dashicons-smartphone' ),
		'gxexplorer' => array( 'label' => __( 'معرفی جعبه جستجو', 'uid-theme' ),              'icon' => 'dashicons-editor-help' ),
		'gxbase'     => array( 'label' => __( 'فصل ۱ — پایه هویت و امنیت', 'uid-theme' ),     'icon' => 'dashicons-shield' ),
		'gxbio'      => array( 'label' => __( 'فصل ۲ — فناوری‌های بایومتریک', 'uid-theme' ),  'icon' => 'dashicons-visibility' ),
		'gxfin'      => array( 'label' => __( 'فصل ۳ — مالی و احراز اطلاعات', 'uid-theme' ),  'icon' => 'dashicons-bank' ),
		'gxdev'      => array( 'label' => __( 'فصل ۴ — فنی برای توسعه‌دهندگان', 'uid-theme' ), 'icon' => 'dashicons-editor-code' ),
		'gxlead'     => array( 'label' => __( 'بنر تماس نهایی (فرم)', 'uid-theme' ),          'icon' => 'dashicons-email-alt' ),
	);
}

function uid_get_gx_layout() {
	$registry = uid_gx_sections_registry();
	$saved    = get_option( 'uid_gx_layout', array() );

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

function uid_sanitize_gx_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_gx_sections_registry();
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
function uid_gx_folded_slugs() {
	return array( 'gxbase', 'gxbio', 'gxfin', 'gxdev' );
}

function uid_render_gx_sections() {
	uid_render_gx_jumpbar();

	$folded    = uid_gx_folded_slugs();
	$layout    = uid_get_gx_layout();
	$folded_on = 0;
	foreach ( $layout as $row ) {
		if ( ! empty( $row['enabled'] ) && in_array( $row['slug'], $folded, true ) ) $folded_on++;
	}

	$foldbar_done = false;
	foreach ( $layout as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$is_folded = in_array( $row['slug'], $folded, true );
		if ( $is_folded && ! $foldbar_done ) {
			echo '<div id="chapters" class="fold-run">';
			uid_render_gx_foldbar( $folded_on );
			$foldbar_done = true;
		}
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
	if ( $foldbar_done ) {
		echo '</div>';
		/* محل الفبایی — نقشه‌بردار JS همین محتوای فصل‌ها را (بدون کپی) این‌جا
		   جابه‌جا می‌کند؛ محل این عنصر در DOM اهمیتی ندارد چون با id پیدا می‌شود. */
		echo '<section class="sec" id="azview" style="padding-block:0 var(--sec)" aria-label="' . esc_attr__( 'فهرست الفبایی اصطلاحات', 'uid-theme' ) . '"></section>';
	}
}

/**
 * نوار «فهرست فصل‌ها» — عنصر ساختاری ثابت، فقط زیر ۹۰۰px نمایش داده می‌شود.
 */
function uid_render_gx_foldbar( $count ) {
	?>
	<div class="foldbar" id="foldbar">
	  <span class="fb-tx"><b><?php echo esc_html( uid_fa_digits( $count ) ); ?></b> <?php esc_html_e( 'دسته — روی هر کدام بزنید تا فهرست اصطلاحاتش باز شود.', 'uid-theme' ); ?></span>
	  <button id="foldAll" type="button"><?php esc_html_e( 'باز کردن همه', 'uid-theme' ); ?></button>
	  <span class="fold-meter"><i id="foldMeter"></i></span>
	</div>
	<?php
}

/**
 * نوار «پرش سریع به بخش‌ها» — عنصر ساختاری ثابت (نه یک سکشن محتوایی)؛ همیشه نمایش
 * داده می‌شود — دقیقاً مثل inc/web-services-hub-page.php.
 */
function uid_render_gx_jumpbar() {
	?>
	<div class="jumpbar" id="jumpbar" aria-label="<?php esc_attr_e( 'پرش سریع به بخش‌ها', 'uid-theme' ); ?>">
	  <div class="jump-rail" id="jumpRail">
	    <button class="jump-chip cta" type="button" data-open-modal><?php esc_html_e( 'مشاوره رایگان', 'uid-theme' ); ?></button>
	    <button class="jump-chip top" type="button" data-jump="#top" aria-label="<?php esc_attr_e( 'بازگشت به بالای صفحه', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg></button>
	    <button class="jump-chip" type="button" data-jump="#explorer"><?php esc_html_e( 'جستجوی اصطلاح', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#base"><?php esc_html_e( 'پایه هویت و امنیت', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#bio"><?php esc_html_e( 'فناوری‌های بایومتریک', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#fin"><?php esc_html_e( 'مالی و احراز اطلاعات', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#dev"><?php esc_html_e( 'فنی برای توسعه‌دهندگان', 'uid-theme' ); ?></button>
	    <button class="jump-chip" type="button" data-jump="#lead"><?php esc_html_e( 'فرم درخواست', 'uid-theme' ); ?></button>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * آیکون‌های کوچک اشتراکی این صفحه
 * ===================================================================== */
function uid_gx_x_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
}
function uid_gx_search_icon() {
	return '<svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6"/></svg>';
}
function uid_gx_submit_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg>';
}
function uid_gx_success_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>';
}
function uid_gx_check_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
}
function uid_gx_info_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16v-4M12 8h.01"/><circle cx="12" cy="12" r="10"/></svg>';
}
function uid_gx_shield_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
}
function uid_gx_arrow_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
}
function uid_gx_chev_icon() {
	return '<svg class="tchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
}
function uid_gx_cat_icon_svg( $cat ) {
	$map = array(
		'base' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
		'bio'  => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'fin'  => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'dev'  => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	);
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ( $map[ $cat ] ?? $map['base'] ) . '</svg>';
}
function uid_gx_cat_color( $cat ) {
	$map = array( 'base' => 'var(--teal)', 'bio' => 'var(--orange)', 'fin' => 'var(--navy)', 'dev' => '#12A06A' );
	return $map[ $cat ] ?? 'var(--navy)';
}
function uid_gx_cat_head_style( $cat ) {
	$map = array(
		'base' => '--cc:var(--teal-d);--cbg:var(--teal-l);--cbd:rgba(41,188,206,.28)',
		'bio'  => '--cc:var(--orange-d);--cbg:var(--orange-l);--cbd:rgba(248,148,40,.3)',
		'fin'  => '--cc:var(--navy);--cbg:#EAF0FB;--cbd:rgba(21,57,124,.2)',
		'dev'  => '--cc:#0E7D52;--cbg:var(--ok-l);--cbd:rgba(18,160,106,.26)',
	);
	return $map[ $cat ] ?? $map['base'];
}

/* =====================================================================
 * صفحه واقعی + اسلاگ
 * ===================================================================== */
function uid_get_gx_page_id() {
	$page_id = (int) get_option( 'uid_gx_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_GLOSSARY_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_gx_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

function uid_ensure_gx_page() {
	if ( uid_get_gx_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'واژه‌نامه اصطلاحات احراز هویت', 'uid-theme' ),
		'post_name'   => 'glossary',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_GLOSSARY_TEMPLATE );
	update_option( 'uid_gx_page_id', $page_id );
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

function uid_register_gx_slug_setting() {
	register_setting( 'uid_gx_group', 'uid_gx_page_slug', array(
		'sanitize_callback' => 'uid_sanitize_gx_page_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_gx_page_slug_section', '', '__return_false', 'uid_gx_layout' );
	add_settings_field( 'uid_gx_page_slug', __( 'آدرس (اسلاگ) صفحه واژه‌نامه', 'uid-theme' ), 'uid_field_gx_page_slug', 'uid_gx_layout', 'uid_gx_page_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_gx_slug_setting' );

function uid_field_gx_page_slug( $args ) {
	$page_id = uid_get_gx_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_gx_page_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_gx_page_slug" value="<?php echo esc_attr( $slug ); ?>">
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
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه واژه‌نامه هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

function uid_sanitize_gx_page_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_gx_page_id();

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
 * پیش‌فرض‌های محتوایی
 * ===================================================================== */
function uid_default_gx_preview() {
	return array(
		array( 'name' => 'e-KYC', 'en' => 'Electronic Know Your Customer', 'color' => 'teal' ),
		array( 'name' => 'تطبیق چهره', 'en' => 'Face Matching', 'color' => 'orange' ),
		array( 'name' => 'تشخیص زنده بودن', 'en' => 'Liveness Detection', 'color' => 'orange' ),
		array( 'name' => 'شاهکار', 'en' => 'Shahkar', 'color' => 'navy' ),
		array( 'name' => 'clientToken', 'en' => 'clientToken', 'color' => 'green' ),
	);
}
function uid_default_gx_stats() {
	return array(
		array( 'value' => 'تعریف‌های دقیق', 'label' => 'تفاوت‌های ظریف نگه داشته شده، نه ساده‌سازی‌شده' ),
		array( 'value' => 'فارسی و انگلیسی', 'label' => 'جستجو روی نام فارسی، معادل لاتین و متن تعریف' ),
		array( 'value' => 'دو دسته‌بندی', 'label' => 'موضوعی یا الفبایی، با یک کلیک' ),
		array( 'value' => 'اتصال به سرویس', 'label' => 'هر واژه به وب‌سرویس متناظرش لینک دارد' ),
	);
}
function uid_default_gx_tldr_items() {
	return array(
		'کادر جستجو را باز کنید و <b>فارسی یا انگلیسی</b> تایپ کنید — روی نام واژه و متن تعریف هم‌زمان فیلتر می‌شود.',
		'فهرست به صورت <b>فقط نام اصطلاح</b> باز می‌شود؛ تعریف هر کدام را خودتان باز می‌کنید.',
		'کلید <b>«برای توسعه‌دهندگان / برای مدیران کسب‌وکار»</b> ترتیب واژه‌ها را به نفع شما عوض می‌کند.',
		'واژه‌هایی که <b>نقطه نارنجی</b> دارند، صفحه وب‌سرویس اختصاصی دارند.',
	);
}

/**
 * پایگاه داده ۳۰ اصطلاح — محتوای تخصصی و به‌هم‌مرتبط (دکمه‌های «اصطلاح مرتبط»)،
 * از پیشخوان قابل‌ویرایش نیست. برای تغییر، همین آرایه‌ها را ویرایش کنید.
 */
function uid_default_gx_base_terms() {
	return array(
		array( 'key' => 'kyc', 'name' => 'KYC', 'en' => 'Know Your Customer', 'aud' => 'biz', 'sortk' => 'KYC',
			'search' => 'KYC Know Your Customer شناخت مشتری know your customer kyc فرایند «شناخت مشتری» که در آن کسب‌وکارها هویت و اطلاعات قانونی کاربر را تایید می‌کنند. این کار پیش‌شرط ارائه خدمات مالی، بانکی و صرافی است. KYC واژه‌ای عام است. هر جا منظور فرایند غیرحضوری و آنلاین باشد، e-KYC واژه دقیق‌تری از KYC است.',
			'def' => 'فرایند «شناخت مشتری» که در آن کسب‌وکارها هویت و اطلاعات قانونی کاربر را تایید می‌کنند. این کار پیش‌شرط ارائه خدمات مالی، بانکی و صرافی است.',
			'note' => 'KYC واژه‌ای عام است. هر جا منظور فرایند غیرحضوری و آنلاین باشد، <b>e-KYC</b> واژه دقیق‌تری از KYC است.',
			'see' => null, 'related' => array( 'ekyc' ) ),
		array( 'key' => 'ekyc', 'name' => 'e-KYC', 'en' => 'Electronic Know Your Customer', 'aud' => 'biz', 'sortk' => 'EKYC',
			'search' => 'e-KYC Electronic Know Your Customer احراز هویت الکترونیکی غیرحضوری آنلاین ekyc electronic know your customer نسخه الکترونیکی و برخط فرایند KYC که در آن احراز هویت مشتری بدون مراجعه حضوری و با ابزارهای دیجیتال (کد ملی، شماره موبایل، ویدیوی سلفی) انجام می‌شود. انجام این فرایند برای بانک‌ها، صرافی‌ها و فین‌تک‌ها پیش از ارائه خدمت الزام قانونی است. در محتوای سایت، هر جا منظور فرایند غیرحضوری و آنلاین است، e-KYC واژه دقیق‌تر از KYC است.',
			'def' => 'نسخه الکترونیکی و برخط فرایند KYC که در آن احراز هویت مشتری بدون مراجعه حضوری و با ابزارهای دیجیتال (کد ملی، شماره موبایل، ویدیوی سلفی) انجام می‌شود. انجام این فرایند برای بانک‌ها، صرافی‌ها و فین‌تک‌ها پیش از ارائه خدمت الزام قانونی است.',
			'note' => 'در محتوای سایت، هر جا منظور فرایند غیرحضوری و آنلاین است، <b>e-KYC</b> واژه دقیق‌تر از <b>KYC</b> است.',
			'see' => array( 'url' => '/api/', 'label' => 'این سرویس را ببینید: وب‌سرویس احراز هویت تصویری' ), 'related' => array( 'kyc', 'pwa' ) ),
		array( 'key' => 'digid', 'name' => 'هویت دیجیتال', 'en' => 'Digital Identity', 'aud' => 'biz', 'sortk' => 'هویت دیجیتال',
			'search' => 'هویت دیجیتال Digital Identity digital identity هویت دیجیتال مجموعه‌ای از داده‌های الکترونیکی که یک فرد را در دنیای آنلاین به صورت یکتا معرفی می‌کند (شامل کد ملی، شماره موبایل، ایمیل و داده‌های زیستی).',
			'def' => 'مجموعه‌ای از داده‌های الکترونیکی که یک فرد را در دنیای آنلاین به صورت یکتا معرفی می‌کند (شامل کد ملی، شماره موبایل، ایمیل و داده‌های زیستی).',
			'note' => null, 'see' => array( 'url' => '/uid-plus/', 'label' => 'این سرویس را ببینید: یوآیدی‌پلاس (PWA)' ), 'related' => array( 'bio' ) ),
		array( 'key' => 'aml', 'name' => 'AML', 'en' => 'Anti-Money Laundering', 'aud' => 'biz', 'sortk' => 'AML',
			'search' => 'AML Anti-Money Laundering مبارزه با پولشویی anti money laundering aml «مبارزه با پولشویی». مجموعه‌ای از قوانین و سیاست‌ها که برای جلوگیری از تبدیل درآمدهای حاصل از فعالیت غیرقانونی به پول تمیز استفاده می‌شود. احراز هویت دقیق، اولین ابزار اجرای این قانون است.',
			'def' => '«مبارزه با پولشویی». مجموعه‌ای از قوانین و سیاست‌ها که برای جلوگیری از تبدیل درآمدهای حاصل از فعالیت غیرقانونی به پول تمیز استفاده می‌شود. احراز هویت دقیق، اولین ابزار اجرای این قانون است.',
			'note' => null, 'see' => null, 'related' => array( 'kyc', 'valid' ) ),
		array( 'key' => 'fdp', 'name' => 'FDP', 'en' => 'Fraud Detection &amp; Prevention', 'aud' => 'biz', 'sortk' => 'FDP',
			'search' => 'FDP Fraud Detection &amp;amp; Prevention کشف تقلب پیشگیری fraud detection prevention fdp سرویس کشف و پیشگیری از تقلب؛ مجموعه‌ای از ابزارها که داده‌های جعلی و الگوهای پرریسک را در فرایند احراز هویت شناسایی می‌کند. این سرویس یوآیدی به صورت رایگان در اختیار پذیرندگان یوآیدی‌پلاس قرار می‌گیرد.',
			'def' => 'سرویس کشف و پیشگیری از تقلب؛ مجموعه‌ای از ابزارها که داده‌های جعلی و الگوهای پرریسک را در فرایند احراز هویت شناسایی می‌کند.',
			'note' => 'این سرویس یوآیدی <b>به صورت رایگان</b> در اختیار پذیرندگان <b>یوآیدی‌پلاس</b> قرار می‌گیرد.',
			'see' => array( 'url' => '/uid-plus/', 'label' => 'این سرویس را ببینید: یوآیدی‌پلاس (PWA)' ), 'related' => array( 'live' ) ),
		array( 'key' => 'mfa', 'name' => 'احراز هویت چندعاملی', 'en' => 'MFA — Multi-Factor Authentication', 'aud' => 'biz', 'sortk' => 'احراز هویت چندعاملی',
			'search' => 'احراز هویت چندعاملی MFA — Multi-Factor Authentication mfa multi factor authentication چندعاملی دو مرحله‌ای روشی که در آن برای ورود کاربر، بیش از یک فاکتور امنیتی (برای نمونه رمز عبور و کد پیامکی) نیاز است تا امنیت افزایش یابد.',
			'def' => 'روشی که در آن برای ورود کاربر، بیش از یک فاکتور امنیتی (برای نمونه رمز عبور و کد پیامکی) نیاز است تا امنیت افزایش یابد.',
			'note' => null, 'see' => null, 'related' => array( 'otp' ) ),
		array( 'key' => 'otp', 'name' => 'رمز یکبار مصرف', 'en' => 'OTP — One-Time Password', 'aud' => 'biz', 'sortk' => 'رمز یکبار مصرف',
			'search' => 'رمز یکبار مصرف OTP — One-Time Password otp one time password رمز یکبار مصرف کد پیامکی رمزی که برای یک بار استفاده تولید می‌شود و مدت اعتبار کوتاهی دارد. در اغلب موارد برای تایید شماره موبایل یا انجام تراکنش کاربرد دارد. پیش از ارسال OTP، سامانه شاهکار بررسی می‌کند که شماره واردشده واقعاً به همان کد ملی تعلق دارد.',
			'def' => 'رمزی که برای یک بار استفاده تولید می‌شود و مدت اعتبار کوتاهی دارد. در اغلب موارد برای تایید شماره موبایل یا انجام تراکنش کاربرد دارد.',
			'note' => 'پیش از ارسال OTP، سامانه <b>شاهکار</b> بررسی می‌کند که شماره واردشده واقعاً به همان کد ملی تعلق دارد.',
			'see' => array( 'url' => '/api-shahkar/', 'label' => 'این سرویس را ببینید: وب‌سرویس شاهکار' ), 'related' => array( 'shahkar', 'mfa' ) ),
	);
}
function uid_default_gx_bio_terms() {
	return array(
		array( 'key' => 'bio', 'name' => 'بایومتریک', 'en' => 'Biometrics', 'aud' => 'biz', 'sortk' => 'بایومتریک',
			'search' => 'بایومتریک Biometrics biometrics بایومتریک زیست‌سنجی عنبیه اثر انگشت علم شناسایی افراد بر اساس ویژگی‌های فیزیولوژیک یا رفتاری (مانند چهره، اثر انگشت، عنبیه یا صدا). در سامانه‌های دیجیتال، این ویژگی‌ها به داده عددی تبدیل شده و با نمونه مرجع مقایسه می‌شوند.',
			'def' => 'علم شناسایی افراد بر اساس ویژگی‌های فیزیولوژیک یا رفتاری (مانند چهره، اثر انگشت، عنبیه یا صدا). در سامانه‌های دیجیتال، این ویژگی‌ها به داده عددی تبدیل شده و با نمونه مرجع مقایسه می‌شوند.',
			'note' => null, 'see' => array( 'url' => '/face-detection/', 'label' => 'این سرویس را ببینید: تطبیق و تشخیص چهره' ), 'related' => array( 'facerec', 'facematch', 'voice' ) ),
		array( 'key' => 'facerec', 'name' => 'تشخیص چهره', 'en' => 'Face Recognition', 'aud' => 'dev', 'sortk' => 'تشخیص چهره',
			'search' => 'تشخیص چهره Face Recognition face recognition تشخیص چهره یک به چند فناوری که نخست چهره انسان را در تصویر یا قاب‌های ویدیو پیدا می‌کند و سپس ویژگی‌های یکتای آن (فاصله چشم‌ها، فرم بینی، خط فک) را به بردار عددی تبدیل می‌کند. این واژه در دو سطح به کار می‌رود: تشخیص حضور چهره در قاب، و شناسایی هویت فرد با جستجو در پایگاه تصاویر. تفاوت آن با تطبیق چهره در نوع مقایسه است: تشخیص چهره جستجوی یک‌به‌چند در پایگاه است؛ تطبیق چهره مقایسه یک‌به‌یک با یک تصویر مرجع مشخص.',
			'def' => 'فناوری که نخست چهره انسان را در تصویر یا قاب‌های ویدیو پیدا می‌کند و سپس ویژگی‌های یکتای آن (فاصله چشم‌ها، فرم بینی، خط فک) را به بردار عددی تبدیل می‌کند. این واژه در دو سطح به کار می‌رود: تشخیص حضور چهره در قاب، و شناسایی هویت فرد با جستجو در پایگاه تصاویر.',
			'note' => 'تفاوت آن با <b>تطبیق چهره</b> در نوع مقایسه است: تشخیص چهره جستجوی <b>یک‌به‌چند</b> در پایگاه است؛ تطبیق چهره مقایسه <b>یک‌به‌یک</b> با یک تصویر مرجع مشخص.',
			'see' => array( 'url' => '/face-detection/', 'label' => 'این سرویس را ببینید: تطبیق و تشخیص چهره' ), 'related' => array( 'facematch' ) ),
		array( 'key' => 'facematch', 'name' => 'تطبیق چهره', 'en' => 'Face Matching / Face Verification', 'aud' => 'biz', 'sortk' => 'تطبیق چهره',
			'search' => 'تطبیق چهره Face Matching / Face Verification face matching face verification تطبیق چهره یک به یک تصویر مرجع مقایسه فریم‌های ویدیوی سلفی کاربر با تصویر مرجع ثبت احوال به کمک هوش مصنوعی. دقت ماژول یوآیدی ۹۹٪ است. در مستندات فنی، معادل رایج دیگر این واژه Face Verification است؛ هر دو به معنای پاسخ به این پرسش‌اند که «آیا چهره کاربر همان فرد در تصویر مرجع است یا نه». مقایسه یک‌به‌یک است، نه جستجوی یک‌به‌چند.',
			'def' => 'مقایسه فریم‌های ویدیوی سلفی کاربر با تصویر مرجع ثبت احوال به کمک هوش مصنوعی. دقت ماژول یوآیدی <b>۹۹٪</b> است.',
			'note' => 'در مستندات فنی، معادل رایج دیگر این واژه <b>Face Verification</b> است؛ هر دو به معنای پاسخ به این پرسش‌اند که «آیا چهره کاربر همان فرد در تصویر مرجع است یا نه». مقایسه <b>یک‌به‌یک</b> است، نه جستجوی یک‌به‌چند.',
			'see' => array( 'url' => '/face-detection/', 'label' => 'این سرویس را ببینید: تطبیق و تشخیص چهره' ), 'related' => array( 'facerec', 'civil' ) ),
		array( 'key' => 'live', 'name' => 'تشخیص زنده بودن', 'en' => 'Liveness Detection', 'aud' => 'biz', 'sortk' => 'تشخیص زنده بودن',
			'search' => 'تشخیص زنده بودن Liveness Detection liveness detection زنده بودن جعل ماسک ضبط شده تشخیص اینکه ویدیوی دریافتی متعلق به یک انسان زنده و حاضر است، نه یک عکس، ویدیوی ضبط‌شده یا ماسک. این لایه اصلی مقابله با جعل است.',
			'def' => 'تشخیص اینکه ویدیوی دریافتی متعلق به یک انسان زنده و حاضر است، نه یک عکس، ویدیوی ضبط‌شده یا ماسک. این لایه اصلی مقابله با جعل است.',
			'note' => null, 'see' => array( 'url' => '/liveness-detection/', 'label' => 'این سرویس را ببینید: الگوریتم تشخیص زنده بودن' ), 'related' => array( 'activel', 'passivel', 'action' ) ),
		array( 'key' => 'activel', 'name' => 'حالت فعال', 'en' => 'Active Liveness', 'aud' => 'dev', 'sortk' => 'حالت فعال',
			'search' => 'حالت فعال Active Liveness active liveness حالت فعال پلک زدن چرخش سر کاربر باید حرکت مشخصی (پلک زدن یا چرخش سر) انجام دهد تا سیستم از حضور انسان زنده مطمئن شود.',
			'def' => 'کاربر باید حرکت مشخصی (پلک زدن یا چرخش سر) انجام دهد تا سیستم از حضور انسان زنده مطمئن شود.',
			'note' => null, 'see' => array( 'url' => '/liveness-detection/', 'label' => 'این سرویس را ببینید: الگوریتم تشخیص زنده بودن' ), 'related' => array( 'live', 'action' ) ),
		array( 'key' => 'passivel', 'name' => 'حالت غیرفعال', 'en' => 'Passive Liveness', 'aud' => 'dev', 'sortk' => 'حالت غیرفعال',
			'search' => 'حالت غیرفعال Passive Liveness passive liveness حالت غیرفعال بافت پوست عمق تصویر هوش مصنوعی بدون نیاز به حرکت اضافه کاربر، شاخص‌های بافت پوست، عمق تصویر و مصنوعات نمایشگر را تحلیل می‌کند؛ نتیجه، تجربه کاربری روان‌تر و زمان کوتاه‌تر است.',
			'def' => 'هوش مصنوعی بدون نیاز به حرکت اضافه کاربر، شاخص‌های بافت پوست، عمق تصویر و مصنوعات نمایشگر را تحلیل می‌کند؛ نتیجه، تجربه کاربری روان‌تر و زمان کوتاه‌تر است.',
			'note' => null, 'see' => array( 'url' => '/liveness-detection/', 'label' => 'این سرویس را ببینید: الگوریتم تشخیص زنده بودن' ), 'related' => array( 'live', 'activel' ) ),
		array( 'key' => 'action', 'name' => 'تشخیص حرکت', 'en' => 'Action Recognition', 'aud' => 'dev', 'sortk' => 'تشخیص حرکت',
			'search' => 'تشخیص حرکت Action Recognition action recognition تشخیص حرکت بینایی ماشین کنش شاخه‌ای از بینایی ماشین که حرکت و کنش انسان را در ویدیو تحلیل می‌کند. در احراز هویت تصویری، از این فناوری برای سنجش انجام حرکت‌های درخواستی (چرخش سر به چپ و راست، پلک زدن، لبخند) در حالت Liveness فعال استفاده می‌شود. این اصطلاح جدا از Liveness است: «تشخیص حضور انسان زنده» یک چیز است و «تشخیص انجام حرکت درخواستی» چیز دیگری. وجود هر دو واژه کمک می‌کند این تفاوت در متن‌ها دقیق منتقل شود.',
			'def' => 'شاخه‌ای از بینایی ماشین که حرکت و کنش انسان را در ویدیو تحلیل می‌کند. در احراز هویت تصویری، از این فناوری برای سنجش انجام حرکت‌های درخواستی (چرخش سر به چپ و راست، پلک زدن، لبخند) در حالت Liveness فعال استفاده می‌شود.',
			'note' => 'این اصطلاح <b>جدا از</b> Liveness است: «تشخیص حضور انسان زنده» یک چیز است و «تشخیص انجام حرکت درخواستی» چیز دیگری. وجود هر دو واژه کمک می‌کند این تفاوت در متن‌ها دقیق منتقل شود.',
			'see' => array( 'url' => '/liveness-detection/', 'label' => 'این سرویس را ببینید: الگوریتم تشخیص زنده بودن' ), 'related' => array( 'live', 'activel' ) ),
		array( 'key' => 'voice', 'name' => 'تشخیص صدا', 'en' => 'Voice Recognition / Speaker Recognition', 'aud' => 'biz', 'sortk' => 'تشخیص صدا',
			'search' => 'تشخیص صدا Voice Recognition / Speaker Recognition voice recognition speaker recognition تشخیص صدا گوینده گفتار بایومتریک صوتی این واژه دو معنا دارد: تشخیص گفتار (تبدیل صدای کاربر به متن) و تشخیص گوینده (Speaker Recognition) که هویت فرد را از ویژگی‌های صوتی مانند زیر و بمی، لحن و طنین صدا استخراج می‌کند. در سامانه‌های احراز هویت، فقط کاربرد دوم — Speaker Recognition — مبنای بایومتریک صوتی است؛ در این روش از کاربر خواسته می‌شود جمله مشخصی را بخواند و نمونه صدا با داده مرجع مقایسه شود.',
			'def' => 'این واژه <b>دو معنا</b> دارد: تشخیص گفتار (تبدیل صدای کاربر به متن) و تشخیص گوینده (Speaker Recognition) که هویت فرد را از ویژگی‌های صوتی مانند زیر و بمی، لحن و طنین صدا استخراج می‌کند.',
			'note' => 'در سامانه‌های احراز هویت، فقط کاربرد دوم — <b>Speaker Recognition</b> — مبنای بایومتریک صوتی است؛ در این روش از کاربر خواسته می‌شود جمله مشخصی را بخواند و نمونه صدا با داده مرجع مقایسه شود.',
			'see' => null, 'related' => array( 'bio' ) ),
	);
}
function uid_default_gx_fin_terms() {
	return array(
		array( 'key' => 'shahkar', 'name' => 'شاهکار', 'en' => 'Shahkar', 'aud' => 'biz', 'sortk' => 'شاهکار',
			'search' => 'شاهکار Shahkar shahkar شاهکار تطابق شماره موبایل کد ملی سامانه‌ای که تطابق شماره تلفن همراه با کد ملی مالک آن را بررسی می‌کند. این سامانه نخستین لایه اطمینان از تعلق شماره واردشده به همان فرد است و پیش از ارسال رمز یکبار مصرف (OTP) در فرایند ثبت‌نام و ورود کاربرد دارد.',
			'def' => 'سامانه‌ای که تطابق شماره تلفن همراه با کد ملی مالک آن را بررسی می‌کند. این سامانه نخستین لایه اطمینان از تعلق شماره واردشده به همان فرد است و پیش از ارسال رمز یکبار مصرف (OTP) در فرایند ثبت‌نام و ورود کاربرد دارد.',
			'note' => null, 'see' => array( 'url' => '/api-shahkar/', 'label' => 'این سرویس را ببینید: وب‌سرویس شاهکار' ), 'related' => array( 'otp' ) ),
		array( 'key' => 'civil', 'name' => 'ثبت احوال', 'en' => 'Civil Registry', 'aud' => 'biz', 'sortk' => 'ثبت احوال',
			'search' => 'ثبت احوال Civil Registry civil registry ثبت احوال تصویر مرجع مشخصات هویتی مرجع رسمی مشخصات هویتی و تصویر مرجع افراد. در یوآیدی‌پلاس، نام و مشخصات کاربر از این مرجع فراخوانی می‌شود، نه از تایپ دستی کاربر؛ به همین دلیل داده‌های هویتی بدون خطای انسانی و با صحت کامل ثبت می‌شوند.',
			'def' => 'مرجع رسمی مشخصات هویتی و تصویر مرجع افراد. در یوآیدی‌پلاس، نام و مشخصات کاربر از این مرجع فراخوانی می‌شود، نه از تایپ دستی کاربر؛ به همین دلیل داده‌های هویتی بدون خطای انسانی و با صحت کامل ثبت می‌شوند.',
			'note' => null, 'see' => array( 'url' => '/api-inquiry-person/', 'label' => 'این سرویس را ببینید: وب‌سرویس ثبت احوال' ), 'related' => array( 'facematch', 'pwa' ) ),
		array( 'key' => 'cardmatch', 'name' => 'تطبیق کارت', 'en' => 'Card Matching', 'aud' => 'biz', 'sortk' => 'تطبیق کارت',
			'search' => 'تطبیق کارت Card Matching card matching تطبیق کارت matched mismatched شماره کارت بانکی فرایندی که بررسی می‌کند شماره کارت بانکی واردشده دقیقاً به نام همان کد ملی است که در سیستم ثبت شده یا نه. خروجی این بررسی به صورت دو حالت تایید (Matched) یا رد (Mismatched) به سامانه پذیرنده برمی‌گردد.',
			'def' => 'فرایندی که بررسی می‌کند شماره کارت بانکی واردشده دقیقاً به نام همان کد ملی است که در سیستم ثبت شده یا نه.',
			'note' => 'خروجی این بررسی به صورت دو حالت <b>تایید (Matched)</b> یا <b>رد (Mismatched)</b> به سامانه پذیرنده برمی‌گردد.',
			'see' => array( 'url' => '/api-validate-card/', 'label' => 'این سرویس را ببینید: تطبیق شماره کارت با کد ملی' ), 'related' => array( 'valid' ) ),
		array( 'key' => 'valid', 'name' => 'اعتبارسنجی', 'en' => 'Validation', 'aud' => 'biz', 'sortk' => 'اعتبارسنجی',
			'search' => 'اعتبارسنجی Validation validation اعتبارسنجی صحت داده شبا فعال بررسی صحت و اعتبار داده‌های واردشده. برای نمونه، بررسی اینکه آیا شماره شبای واردشده در سیستم بانکی فعال است یا خیر.',
			'def' => 'بررسی صحت و اعتبار داده‌های واردشده. برای نمونه، بررسی اینکه آیا شماره شبای واردشده در سیستم بانکی فعال است یا خیر.',
			'note' => null, 'see' => array( 'url' => '/api-validate-iban/', 'label' => 'این سرویس را ببینید: تطبیق شبا با کد ملی' ), 'related' => array( 'cardmatch', 'inquiry' ) ),
		array( 'key' => 'inquiry', 'name' => 'استعلام آنی', 'en' => 'Real-time Inquiry', 'aud' => 'biz', 'sortk' => 'استعلام آنی',
			'search' => 'استعلام آنی Real-time Inquiry real time inquiry استعلام آنی لحظه‌ای دیتابیس مرجع خدماتی که پاسخ را در لحظه و بدون وقفه زمانی از دیتابیس‌های مرجع دریافت می‌کند. در صفحات وب‌سرویس، این عبارت معادل سرعت بالای پاسخ‌دهی است.',
			'def' => 'خدماتی که پاسخ را در لحظه و بدون وقفه زمانی از دیتابیس‌های مرجع دریافت می‌کند. در صفحات وب‌سرویس، این عبارت معادل سرعت بالای پاسخ‌دهی است.',
			'note' => null, 'see' => array( 'url' => '/api-inquiry-iban/', 'label' => 'این سرویس را ببینید: استعلام اطلاعات مالی (شبا)' ), 'related' => array( 'latency', 'multi' ) ),
	);
}
function uid_default_gx_dev_terms() {
	return array(
		array( 'key' => 'api', 'name' => 'API', 'en' => 'Application Programming Interface', 'aud' => 'dev', 'sortk' => 'API',
			'search' => 'API Application Programming Interface api application programming interface رابط برنامه‌نویسی رابط برنامه‌نویسی که به دو نرم‌افزار اجازه می‌دهد با هم گفتگو کنند. در اینجا، رابطی است که سامانه پذیرنده را به سرویس‌های یوآیدی متصل می‌کند.',
			'def' => 'رابط برنامه‌نویسی که به دو نرم‌افزار اجازه می‌دهد با هم گفتگو کنند. در اینجا، رابطی است که سامانه پذیرنده را به سرویس‌های یوآیدی متصل می‌کند.',
			'note' => null, 'see' => array( 'url' => '/api-ekyc-docs/', 'label' => 'این سرویس را ببینید: مستندات API' ), 'related' => array( 'sdk', 'response' ) ),
		array( 'key' => 'sdk', 'name' => 'SDK', 'en' => 'Software Development Kit', 'aud' => 'dev', 'sortk' => 'SDK',
			'search' => 'SDK Software Development Kit sdk software development kit کتابخانه کد دوربین مجموعه ابزارهای آماده (کتابخانه کد، مستندات و نمونه پیاده‌سازی) که توسعه‌دهنده آن را داخل اپلیکیشن خود قرار می‌دهد تا سرویس یوآیدی مستقیم در همان اپ اجرا شود. در احراز هویت تصویری، SDK مدیریت دوربین دستگاه و نمایش راهنمای ویدیوی سلفی را بر عهده دارد؛ کاربر بدون خروج از اپلیکیشن، فرایند را کامل می‌کند. تفاوت آن با API در محل اجراست: API از سمت سرور فراخوانی می‌شود، اما SDK داخل برنامه نصب و اجرا می‌شود.',
			'def' => 'مجموعه ابزارهای آماده (کتابخانه کد، مستندات و نمونه پیاده‌سازی) که توسعه‌دهنده آن را داخل اپلیکیشن خود قرار می‌دهد تا سرویس یوآیدی مستقیم در همان اپ اجرا شود. در احراز هویت تصویری، SDK مدیریت دوربین دستگاه و نمایش راهنمای ویدیوی سلفی را بر عهده دارد؛ کاربر بدون خروج از اپلیکیشن، فرایند را کامل می‌کند.',
			'note' => 'تفاوت آن با <b>API</b> در محل اجراست: API از سمت <b>سرور</b> فراخوانی می‌شود، اما SDK <b>داخل برنامه</b> نصب و اجرا می‌شود.',
			'see' => array( 'url' => '/api/', 'label' => 'این سرویس را ببینید: وب‌سرویس احراز هویت تصویری' ), 'related' => array( 'api', 'pwa' ) ),
		array( 'key' => 'pwa', 'name' => 'PWA', 'en' => 'Progressive Web App', 'aud' => 'dev', 'sortk' => 'PWA',
			'search' => 'PWA Progressive Web App pwa progressive web app اپلیکیشن تحت وب بدون نصب اپلیکیشن تحت وب که بدون نصب و بدون عبور از فروشگاه اپلیکیشن، مثل یک اپ روی موبایل اجرا می‌شود. در e-KYC یعنی حذف کامل مانع نصب؛ کاربر با یک لینک وارد فرایند احراز می‌شود و پس از پایان، به سامانه پذیرنده بازمی‌گردد.',
			'def' => 'اپلیکیشن تحت وب که بدون نصب و بدون عبور از فروشگاه اپلیکیشن، مثل یک اپ روی موبایل اجرا می‌شود. در e-KYC یعنی حذف کامل مانع نصب؛ کاربر با یک لینک وارد فرایند احراز می‌شود و پس از پایان، به سامانه پذیرنده بازمی‌گردد.',
			'note' => null, 'see' => array( 'url' => '/uid-plus/', 'label' => 'این سرویس را ببینید: یوآیدی‌پلاس (PWA)' ), 'related' => array( 'ekyc', 'metadata', 'clienttoken' ) ),
		array( 'key' => 'webhook', 'name' => 'وب‌هوک', 'en' => 'Webhook', 'aud' => 'dev', 'sortk' => 'وب‌هوک',
			'search' => 'وب‌هوک Webhook webhook وب هوک نتیجه خودکار روشی که سرور یوآیدی از طریق آن، نتیجه نهایی احراز هویت را به صورت خودکار برای سامانه پذیرنده ارسال می‌کند (بدون اینکه پذیرنده مدام وضعیت را بپرسد).',
			'def' => 'روشی که سرور یوآیدی از طریق آن، نتیجه نهایی احراز هویت را به صورت خودکار برای سامانه پذیرنده ارسال می‌کند (بدون اینکه پذیرنده مدام وضعیت را بپرسد).',
			'note' => null, 'see' => array( 'url' => '/pwa-ekyc-docs/', 'label' => 'این سرویس را ببینید: مستندات PWA' ), 'related' => array( 'response', 'clienttoken' ) ),
		array( 'key' => 'sandbox', 'name' => 'سندباکس', 'en' => 'Sandbox', 'aud' => 'dev', 'sortk' => 'سندباکس',
			'search' => 'سندباکس Sandbox sandbox سندباکس محیط آزمایشی تست محیط آزمایشی که در آن توسعه‌دهندگان می‌توانند بدون ریسک و بدون استفاده از داده واقعی، اتصال به وب‌سرویس را تست کنند.',
			'def' => 'محیط آزمایشی که در آن توسعه‌دهندگان می‌توانند بدون ریسک و بدون استفاده از داده واقعی، اتصال به وب‌سرویس را تست کنند.',
			'note' => null, 'see' => array( 'url' => '/docs/', 'label' => 'این سرویس را ببینید: مستندات فنی و سندباکس' ), 'related' => array( 'api' ) ),
		array( 'key' => 'latency', 'name' => 'زمان پاسخ', 'en' => 'Latency', 'aud' => 'dev', 'sortk' => 'زمان پاسخ',
			'search' => 'زمان پاسخ Latency latency زمان پاسخ تاخیر میلی ثانیه مدت زمانی که طول می‌کشد تا درخواست ارسالی از سمت پذیرنده به سرور برسد و پاسخ دریافت شود. در سرویس‌های یوآیدی این زمان زیر ۲۰۰ میلی‌ثانیه است.',
			'def' => 'مدت زمانی که طول می‌کشد تا درخواست ارسالی از سمت پذیرنده به سرور برسد و پاسخ دریافت شود.',
			'note' => 'در سرویس‌های یوآیدی این زمان <b>زیر ۲۰۰ میلی‌ثانیه</b> است.',
			'see' => array( 'url' => '/api/', 'label' => 'این سرویس را ببینید: وب‌سرویس احراز هویت تصویری' ), 'related' => array( 'inquiry', 'response' ) ),
		array( 'key' => 'response', 'name' => 'پاسخ', 'en' => 'Response', 'aud' => 'dev', 'sortk' => 'پاسخ',
			'search' => 'پاسخ Response response پاسخ json وضعیت تایید رد نتیجه‌ای که وب‌سرویس پس از پردازش درخواست برمی‌گرداند؛ معمولاً در قالب فرمت JSON شامل وضعیت تایید یا رد و اطلاعات تکمیلی.',
			'def' => 'نتیجه‌ای که وب‌سرویس پس از پردازش درخواست برمی‌گرداند؛ معمولاً در قالب فرمت JSON شامل وضعیت تایید یا رد و اطلاعات تکمیلی.',
			'note' => null, 'see' => array( 'url' => '/api-ekyc-docs/', 'label' => 'این سرویس را ببینید: مستندات API' ), 'related' => array( 'api', 'webhook' ) ),
		array( 'key' => 'clienttoken', 'name' => 'clientToken', 'en' => 'clientToken', 'aud' => 'dev', 'sortk' => 'CLIENTTOKEN',
			'search' => 'clientToken clientToken clienttoken client token شناسه خاتمه فرایند شناسه‌ای که پس از خاتمه فرایند احراز به پذیرنده داده می‌شود. اگر این مقدار پر باشد یعنی مراحل کامل شده و پذیرنده با ارسال همین شناسه در فراخوانی بعدی می‌تواند اطلاعات کاربر را دریافت کند.',
			'def' => 'شناسه‌ای که پس از خاتمه فرایند احراز به پذیرنده داده می‌شود.',
			'note' => 'اگر این مقدار <b>پر باشد</b> یعنی مراحل کامل شده و پذیرنده با ارسال همین شناسه در <b>فراخوانی بعدی</b> می‌تواند اطلاعات کاربر را دریافت کند.',
			'see' => array( 'url' => '/pwa-ekyc-docs/', 'label' => 'این سرویس را ببینید: مستندات PWA' ), 'related' => array( 'pwa', 'metadata', 'webhook' ) ),
		array( 'key' => 'metadata', 'name' => 'metaData', 'en' => 'metaData — تگ سفارشی پذیرنده', 'aud' => 'dev', 'sortk' => 'METADATA',
			'search' => 'metaData metaData — تگ سفارشی پذیرنده metadata meta data تگ سفارشی user_id پذیرنده تگ منحصربه‌فردی که پذیرنده هنگام فراخوانی PWA می‌فرستد (برای نمونه user_id سیستم خودش). یوآیدی این مقدار را ذخیره می‌کند و بدون تغییر در پاسخ‌ها بازمی‌گرداند تا پذیرنده هر درخواست را با کاربر خودش متناظر کند.',
			'def' => 'تگ منحصربه‌فردی که پذیرنده هنگام فراخوانی PWA می‌فرستد (برای نمونه user_id سیستم خودش).',
			'note' => 'یوآیدی این مقدار را ذخیره می‌کند و <b>بدون تغییر</b> در پاسخ‌ها بازمی‌گرداند تا پذیرنده هر درخواست را با کاربر خودش متناظر کند.',
			'see' => array( 'url' => '/pwa-ekyc-docs/', 'label' => 'این سرویس را ببینید: مستندات PWA' ), 'related' => array( 'pwa', 'clienttoken' ) ),
		array( 'key' => 'multi', 'name' => 'Multi-Vendor', 'en' => 'Multi-Vendor', 'aud' => 'dev', 'sortk' => 'MULTIVENDOR',
			'search' => 'Multi-Vendor Multi-Vendor multi vendor چند تامین کننده failover جایگزین قطعی استفاده از چند تامین‌کننده برای هر سرویس استعلامی؛ به این ترتیب قطعی یا خطای یک تامین‌کننده، کل فرآیند احراز هویت پذیرنده را متوقف نمی‌کند. در این ساختار اگر تامین‌کننده اصلی در دسترس نباشد، درخواست به صورت خودکار به تامین‌کننده جایگزین هدایت می‌شود.',
			'def' => 'استفاده از چند تامین‌کننده برای هر سرویس استعلامی؛ به این ترتیب قطعی یا خطای یک تامین‌کننده، کل فرآیند احراز هویت پذیرنده را متوقف نمی‌کند.',
			'note' => 'در این ساختار اگر تامین‌کننده اصلی در دسترس نباشد، درخواست به صورت <b>خودکار</b> به تامین‌کننده جایگزین هدایت می‌شود.',
			'see' => array( 'url' => '/uid-plus/', 'label' => 'این سرویس را ببینید: یوآیدی‌پلاس (PWA)' ), 'related' => array( 'inquiry' ) ),
	);
}
function uid_gx_terms_by_cat( $cat ) {
	switch ( $cat ) {
		case 'base': return uid_default_gx_base_terms();
		case 'bio':  return uid_default_gx_bio_terms();
		case 'fin':  return uid_default_gx_fin_terms();
		case 'dev':  return uid_default_gx_dev_terms();
	}
	return array();
}
function uid_gx_all_terms_index() {
	static $all = null;
	if ( null !== $all ) return $all;
	$all = array();
	foreach ( array( 'base', 'bio', 'fin', 'dev' ) as $cat ) {
		foreach ( uid_gx_terms_by_cat( $cat ) as $t ) {
			$all[ $t['key'] ] = array( 'name' => $t['name'], 'cat' => $cat );
		}
	}
	return $all;
}

/**
 * چاپ HTML یک ردیف اصطلاح — مشترک بین چهار فصل
 */
function uid_gx_render_term( $t ) {
	$index = uid_gx_all_terms_index();
	?>
	<div class="term" data-key="<?php echo esc_attr( $t['key'] ); ?>" data-cat="<?php echo esc_attr( $t['cat'] ); ?>" data-aud="<?php echo esc_attr( $t['aud'] ); ?>" data-sortk="<?php echo esc_attr( $t['sortk'] ); ?>" data-search="<?php echo esc_attr( $t['search'] ); ?>" style="--tc:<?php echo esc_attr( uid_gx_cat_color( $t['cat'] ) ); ?>">
	  <button class="term-hd" type="button" aria-expanded="false" aria-controls="t-<?php echo esc_attr( $t['key'] ); ?>">
	    <span class="tdot"></span>
	    <span class="term-nm"><b><?php echo esc_html( $t['name'] ); ?></b><span class="en"><?php echo esc_html( $t['en'] ); ?></span></span>
	    <?php if ( ! empty( $t['see'] ) ) : ?><span class="tsvc" aria-hidden="true"></span><?php endif; ?><?php echo uid_gx_chev_icon(); ?>
	  </button>
	  <div class="term-bd" id="t-<?php echo esc_attr( $t['key'] ); ?>"><div class="term-in">
	    <p class="term-def"><?php echo esc_html( $t['def'] ); ?></p>
	    <?php if ( ! empty( $t['note'] ) ) : ?>
	    <div class="term-note"><?php echo uid_gx_info_icon(); ?><span><?php echo wp_kses( $t['note'], array( 'b' => array() ) ); ?></span></div>
	    <?php endif; ?>
	    <div class="term-acts">
	      <?php if ( ! empty( $t['see'] ) ) : ?><a class="term-see" href="<?php echo esc_url( $t['see']['url'] ); ?>"><?php echo uid_gx_arrow_icon(); ?><span><?php echo esc_html( $t['see']['label'] ); ?></span></a><?php endif; ?>
	      <?php foreach ( (array) $t['related'] as $rel_key ) :
	        if ( empty( $index[ $rel_key ] ) ) continue;
	        $rel = $index[ $rel_key ];
	        ?>
	      <button class="term-rel" type="button" data-rel="<?php echo esc_attr( $rel_key ); ?>" style="--rc:<?php echo esc_attr( uid_gx_cat_color( $rel['cat'] ) ); ?>"><i></i><?php echo esc_html( $rel['name'] ); ?></button>
	      <?php endforeach; ?>
	    </div>
	  </div></div>
	</div>
	<?php
}

/* =====================================================================
 * سکشن‌ها
 * ===================================================================== */
function uid_render_section_gxhero() {
	$tag     = uid_section_tag( 'gxhero', 'h1' );
	$preview = uid_section_val( 'gxhero', 'preview', uid_default_gx_preview() );
	if ( ! is_array( $preview ) ) $preview = array();
	$tags_raw = uid_section_val( 'gxhero', 'tags', "<b>۳۰</b> اصطلاح\n<b>۴</b> دسته موضوعی\n<b>۱۲</b> صفحه سرویس مرتبط\nجستجوی فارسی و انگلیسی" );
	$tags     = array_filter( array_map( 'trim', explode( "\n", $tags_raw ) ) );
	?>
	<section class="dark heroA" id="top">
	  <div class="gx-wrap">
	    <div class="gxhero">
	      <div class="rv">
	        <span class="gx-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'gxhero', 'eyebrow', __( 'منابع یوآیدی', 'uid-theme' ) ) ); ?></span>
	        <?php echo '<' . $tag . ' class="h-hero">'; ?><?php echo wp_kses( uid_section_val( 'gxhero', 'heading', __( 'واژه‌نامه اصطلاحات <mark>احراز هویت</mark>', 'uid-theme' ) ), array( 'mark' => array() ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p class="lede on-dark"><?php echo wp_kses( uid_section_val( 'gxhero', 'text', __( 'هر واژه‌ای که در قرارداد، مستندات فنی یا جلسه ارزیابی ریسک با آن روبه‌رو می‌شوید — با تعریف دقیق، معادل انگلیسی، و لینک مستقیم به وب‌سرویسی که همان واژه را عملی می‌کند. تفاوت‌های ظریف را هم نگه داشته‌ایم: <b>e-KYC</b> واژه دقیق‌تر از <b>KYC</b> است، و <b>تطبیق چهره</b> همان <b>تشخیص چهره</b> نیست.', 'uid-theme' ) ), array( 'b' => array() ) ); ?></p>
	        <div class="gx-btn-row">
	          <button class="gx-btn gx-btn-cta" data-jump-soft="#explorer">
	            <?php echo uid_gx_search_icon(); ?>
	            <?php echo esc_html( uid_section_val( 'gxhero', 'btn1_text', __( 'جستجو در ۳۰ اصطلاح', 'uid-theme' ) ) ); ?></button>
	          <a class="gx-btn gx-btn-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>">
	            <?php echo uid_gx_search_icon(); ?>
	            <span class="num"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        </div>
	        <?php if ( $tags ) : ?>
	        <div class="gxhero-tags">
	          <?php foreach ( $tags as $tg ) : ?>
	          <span class="gxtag"><?php echo wp_kses( $tg, array( 'b' => array() ) ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>

	      <div class="gxcard rv">
	        <div class="gxcard-hd">
	          <span class="dot"></span>
	          <span><?php esc_html_e( 'نمونه‌ای از فهرست اصطلاحات', 'uid-theme' ); ?></span>
	          <span class="k lat">A–Z</span>
	        </div>
	        <div class="gxprev">
	        <?php foreach ( $preview as $i => $p ) :
	          $color = array( 'teal' => 'var(--teal)', 'orange' => 'var(--orange)', 'navy' => 'var(--navy)', 'green' => '#12A06A' );
	          $cv = $color[ $p['color'] ?? 'navy' ] ?? 'var(--navy)';
	          ?>
	        <div class="gxprev-row<?php echo 0 === $i ? ' on' : ''; ?>" style="--pc:<?php echo esc_attr( $cv ); ?>"><i></i><span class="t"><?php echo esc_html( $p['name'] ?? '' ); ?></span><span class="e"><?php echo esc_html( $p['en'] ?? '' ); ?></span></div>
	        <?php endforeach; ?>
	        </div>
	        <p class="gxcard-ft"><?php esc_html_e( 'هر اصطلاحی که صفحه سرویس دارد، با یک نقطه نارنجی مشخص شده و داخل تعریفش لینک «این سرویس را ببینید» می‌گیرد.', 'uid-theme' ); ?></p>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

function uid_render_section_gxstats() {
	$items = uid_section_val( 'gxstats', 'items', uid_default_gx_stats() );
	if ( ! is_array( $items ) ) $items = array();
	?>
	<section class="sec" style="padding-block:44px 0">
	  <div class="gx-wrap">
	    <div class="qstats rv">
	      <?php foreach ( $items as $it ) : ?>
	      <div class="qstat"><span class="v txt"><?php echo esc_html( $it['value'] ?? '' ); ?></span><span class="l"><?php echo esc_html( $it['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

function uid_render_section_gxtldr() {
	$items_raw = uid_section_val( 'gxtldr', 'items', implode( "\n", uid_default_gx_tldr_items() ) );
	$items     = array_filter( array_map( 'trim', explode( "\n", $items_raw ) ) );
	?>
	<section class="sec" style="padding-block:24px 0">
	  <div class="gx-wrap">
	    <div class="tldr rv">
	      <div class="tldr-hd">
	        <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4.1 13.4a1 1 0 00.8 1.6H11l-1 7 8.9-11.4a1 1 0 00-.8-1.6H12z"/></svg></span>
	        <b><?php echo esc_html( uid_section_val( 'gxtldr', 'heading', __( 'اگر دنبال یک واژه مشخص هستید', 'uid-theme' ) ) ); ?></b>
	        <span><?php echo esc_html( uid_section_val( 'gxtldr', 'badge', __( '۳۰ ثانیه', 'uid-theme' ) ) ); ?></span>
	      </div>
	      <ul class="tldr-list">
	        <?php foreach ( $items as $it ) : ?>
	        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>
	          <span><?php echo wp_kses( $it, array( 'b' => array() ) ); ?></span></li>
	        <?php endforeach; ?>
	      </ul>
	      <div class="tldr-acts">
	        <a class="gx-btn gx-btn-cta gx-btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .3 1.9.6 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.1a2 2 0 012.1-.5c.9.3 1.8.5 2.8.6a2 2 0 011.7 2z"/></svg>
	          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	        <button class="gx-btn gx-btn-ghost-d gx-btn-block" data-open-modal><?php echo esc_html( uid_section_val( 'gxtldr', 'btn_text', __( 'مشاوره رایگان و کلید آزمایشی', 'uid-theme' ) ) ); ?></button>
	      </div>
	      <button class="tldr-more" type="button" data-jump-soft="#explorer"><?php echo esc_html( uid_section_val( 'gxtldr', 'more_text', __( 'رفتن به کادر جستجو و فهرست اصطلاحات', 'uid-theme' ) ) ); ?>
	        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12l7 7 7-7"/></svg></button>
	    </div>
	  </div>
	</section>
	<?php
}

function uid_render_section_gxexplorer() {
	$tag = uid_section_tag( 'gxexplorer', 'h2' );
	$cats = array(
		'base' => array( 'label' => __( 'پایه هویت و امنیت', 'uid-theme' ), 'color' => 'var(--teal)' ),
		'bio'  => array( 'label' => __( 'فناوری‌های بایومتریک', 'uid-theme' ), 'color' => 'var(--orange)' ),
		'fin'  => array( 'label' => __( 'مالی و احراز اطلاعات', 'uid-theme' ), 'color' => 'var(--navy)' ),
		'dev'  => array( 'label' => __( 'فنی برای توسعه‌دهندگان', 'uid-theme' ), 'color' => '#12A06A' ),
	);
	$total = 0;
	foreach ( $cats as $c => $meta ) $total += count( uid_gx_terms_by_cat( $c ) );
	?>
	<section class="sec" id="explorer" style="padding-block:clamp(40px,5vw,64px) 28px">
	  <div class="gx-wrap">
	    <div class="sec-head rv" style="margin-bottom:22px">
	      <span class="gx-eyebrow"><i></i><?php echo esc_html( uid_section_val( 'gxexplorer', 'eyebrow', __( 'فهرست اصطلاحات', 'uid-theme' ) ) ); ?></span>
	      <?php echo '<' . $tag . ' class="h-sec">'; ?><?php echo esc_html( uid_section_val( 'gxexplorer', 'heading', __( 'واژه را پیدا کنید، تعریفش را باز کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p class="lede"><?php echo esc_html( uid_section_val( 'gxexplorer', 'text', __( 'جستجو هم‌زمان روی نام فارسی، معادل انگلیسی و متن تعریف کار می‌کند. فهرست در حالت بسته فقط نام اصطلاح‌ها را نشان می‌دهد تا صفحه کوتاه بماند.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <div class="gx-tools rv" role="search">
	      <label class="sr" for="gxSearch"><?php esc_html_e( 'جستجو در اصطلاحات احراز هویت', 'uid-theme' ); ?></label>
	      <div class="gx-field" id="gxField">
	        <?php echo uid_gx_search_icon(); ?>
	        <input id="gxSearch" type="search" autocomplete="off" enterkeyhint="search"
	               placeholder="<?php esc_attr_e( 'مثلاً: تطبیق چهره، liveness، شاهکار، clientToken…', 'uid-theme' ); ?>">
	        <button class="gx-clear" id="gxClear" type="button" aria-label="<?php esc_attr_e( 'پاک کردن جستجو', 'uid-theme' ); ?>">
	          <?php echo uid_gx_x_icon(); ?></button>
	      </div>

	      <div class="gx-switches">
	        <span class="gx-seglbl"><?php esc_html_e( 'نمایش برای:', 'uid-theme' ); ?></span>
	        <div class="gx-seg" role="group" aria-label="<?php esc_attr_e( 'مخاطب', 'uid-theme' ); ?>">
	          <button type="button" data-aud-set="all" class="on" aria-pressed="true"><?php esc_html_e( 'همه', 'uid-theme' ); ?></button>
	          <button type="button" data-aud-set="dev" aria-pressed="false"><?php esc_html_e( 'توسعه‌دهندگان', 'uid-theme' ); ?></button>
	          <button type="button" data-aud-set="biz" aria-pressed="false"><?php esc_html_e( 'مدیران کسب‌وکار', 'uid-theme' ); ?></button>
	        </div>
	        <div class="gx-seg" role="group" aria-label="<?php esc_attr_e( 'نوع دسته‌بندی', 'uid-theme' ); ?>">
	          <button type="button" data-view-set="cat" class="on" aria-pressed="true"><?php esc_html_e( 'موضوعی', 'uid-theme' ); ?></button>
	          <button type="button" data-view-set="az" aria-pressed="false"><?php esc_html_e( 'الفبایی', 'uid-theme' ); ?></button>
	        </div>
	        <span class="gx-count" id="gxCount" aria-live="polite"></span>
	      </div>

	      <div class="gx-cats" role="group" aria-label="<?php esc_attr_e( 'دسته‌های موضوعی', 'uid-theme' ); ?>">
	        <button class="gx-cat on" type="button" data-cat="all" aria-pressed="true" style="--cc:var(--navy)">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
	          <?php esc_html_e( 'همه دسته‌ها', 'uid-theme' ); ?><span class="n"><?php echo esc_html( uid_fa_digits( $total ) ); ?></span></button>
	      <?php foreach ( $cats as $c => $meta ) : ?>
	      <button class="gx-cat" type="button" data-cat="<?php echo esc_attr( $c ); ?>" aria-pressed="false" style="--cc:<?php echo esc_attr( $meta['color'] ); ?>"><?php echo uid_gx_cat_icon_svg( $c ); ?><?php echo esc_html( $meta['label'] ); ?><span class="n"><?php echo esc_html( uid_fa_digits( count( uid_gx_terms_by_cat( $c ) ) ) ); ?></span></button>
	      <?php endforeach; ?>
	      </div>
	    </div>

	    <div class="gx-empty" id="gxEmpty" role="status">
	      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.6-3.6M9 11h4"/></svg>
	      <b><?php esc_html_e( 'اصطلاحی با «', 'uid-theme' ); ?><span class="q lat"></span><?php esc_html_e( '» پیدا نشد', 'uid-theme' ); ?></b>
	      <p class="small"><?php esc_html_e( 'املای دیگری را امتحان کنید، یا معادل انگلیسی واژه را بنویسید. اگر اصطلاحی که دنبالش هستید در این فهرست نیست،', 'uid-theme' ); ?>
	        <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="tlink" style="display:inline-flex"><?php esc_html_e( 'با ما تماس بگیرید', 'uid-theme' ); ?>
	          <?php echo uid_gx_arrow_icon(); ?></a></p>
	    </div>
	  </div>
	</section>
	<?php
}

/**
 * چاپ یک فصل موضوعی (پایه/بایومتریک/مالی/فنی) — عنوان و توضیح از پیشخوان قابل‌
 * ویرایش است؛ فهرست اصطلاحات محتوای تخصصی ثابت است (اصطلاح‌ها به هم ارجاع
 * می‌دهند و از این صفحه قابل‌ویرایش نیست).
 */
function uid_gx_render_category_section( $cat, $section_slug, $default_heading, $default_text, $default_fold_title, $default_fold_teaser ) {
	$terms = uid_gx_terms_by_cat( $cat );
	$count = count( $terms );
	?>
	<section class="sec catsec" id="<?php echo esc_attr( $cat ); ?>" data-cat="<?php echo esc_attr( $cat ); ?>" data-fold
	         data-fold-min="<?php echo esc_attr( sprintf( /* translators: %s: term count */ __( '%s اصطلاح', 'uid-theme' ), uid_fa_digits( $count ) ) ); ?>"
	         data-fold-title="<?php echo esc_attr( uid_section_val( $section_slug, 'fold_title', $default_fold_title ) ); ?>"
	         data-fold-teaser="<?php echo esc_attr( uid_section_val( $section_slug, 'fold_teaser', $default_fold_teaser ) ); ?>">
	  <div class="fold-body"><div class="gx-wrap"><div class="fold-inner">
	    <div class="cat-head rv" style="<?php echo esc_attr( uid_gx_cat_head_style( $cat ) ); ?>">
	      <span class="cat-ic"><?php echo uid_gx_cat_icon_svg( $cat ); ?></span>
	      <div class="tx">
	        <h2 class="h-sub"><?php echo esc_html( uid_section_val( $section_slug, 'heading', $default_heading ) ); ?></h2>
	        <p class="small"><?php echo esc_html( uid_section_val( $section_slug, 'text', $default_text ) ); ?></p>
	      </div>
	      <span class="cat-n"><?php echo esc_html( sprintf( /* translators: %s: term count */ __( '%s اصطلاح', 'uid-theme' ), uid_fa_digits( $count ) ) ); ?></span>
	    </div>
	    <div class="term-list" id="list-<?php echo esc_attr( $cat ); ?>">
	      <div class="term-sep"><span class="l-dev"><?php esc_html_e( 'اصطلاحات بالا برای تیم فنی است — بقیه واژه‌ها همچنان اینجا هستند', 'uid-theme' ); ?></span><span class="l-biz"><?php esc_html_e( 'اصطلاحات بالا برای تصمیم کسب‌وکار است — بقیه واژه‌ها همچنان اینجا هستند', 'uid-theme' ); ?></span><i></i></div>
	      <?php foreach ( $terms as $t ) : $t['cat'] = $cat; uid_gx_render_term( $t ); endforeach; ?>
	    </div>
	  </div></div></div>
	</section>
	<?php
}

function uid_render_section_gxbase() {
	uid_gx_render_category_section( 'base', 'gxbase',
		__( 'اصطلاحات پایه هویت و امنیت', 'uid-theme' ),
		__( 'واژه‌هایی که چارچوب قانونی و امنیتی احراز هویت را می‌سازند — از KYC و AML تا رمز یکبار مصرف و احراز چندعاملی.', 'uid-theme' ),
		__( 'اصطلاحات پایه هویت و امنیت', 'uid-theme' ),
		__( 'KYC، e-KYC، هویت دیجیتال، AML، FDP', 'uid-theme' )
	);
}
function uid_render_section_gxbio() {
	uid_gx_render_category_section( 'bio', 'gxbio',
		__( 'اصطلاحات فناوری‌های بایومتریک', 'uid-theme' ),
		__( 'چهره، صدا و زنده‌بودن. دقیق‌ترین بخش این واژه‌نامه، چون تفاوت‌های ظریفش در مستندات فنی معمولاً جابه‌جا استفاده می‌شوند.', 'uid-theme' ),
		__( 'اصطلاحات فناوری‌های بایومتریک', 'uid-theme' ),
		__( 'بایومتریک، تشخیص چهره، تطبیق چهره، تشخیص زنده بودن، حالت فعال', 'uid-theme' )
	);
}
function uid_render_section_gxfin() {
	uid_gx_render_category_section( 'fin', 'gxfin',
		__( 'اصطلاحات مالی و احراز اطلاعات', 'uid-theme' ),
		__( 'سامانه‌های مرجعی که پاسخ قطعی می‌دهند: شاهکار، ثبت احوال، تطبیق کارت و اعتبارسنجی.', 'uid-theme' ),
		__( 'اصطلاحات مالی و احراز اطلاعات', 'uid-theme' ),
		__( 'شاهکار، ثبت احوال، تطبیق کارت، اعتبارسنجی، استعلام آنی', 'uid-theme' )
	);
}
function uid_render_section_gxdev() {
	uid_gx_render_category_section( 'dev', 'gxdev',
		__( 'اصطلاحات فنی برای توسعه‌دهندگان', 'uid-theme' ),
		__( 'هر چیزی که تیم فنی پیش از اتصال باید بداند — از API و SDK تا clientToken و metaData.', 'uid-theme' ),
		__( 'اصطلاحات فنی برای توسعه‌دهندگان', 'uid-theme' ),
		__( 'API، SDK، PWA، وب‌هوک، سندباکس', 'uid-theme' )
	);
}

function uid_render_section_gxlead() {
	$trust_raw   = uid_section_val( 'gxlead', 'trust', "مشاوره رایگان، بدون تعهد\nکلید آزمایشی و سندباکس پیش از قرارداد\nمستندات کامل فارسی برای تیم فنی\nپشتیبانی اختصاصی سازمانی" );
	$trust       = array_filter( array_map( 'trim', explode( "\n", $trust_raw ) ) );
	$biztype_raw = uid_section_val( 'gxlead', 'biztype_options', "ثبت‌نام و خدمات مالی\nدرگاه و پلتفرم پرداخت\nکیف پول دیجیتال\nصرافی رمزارز\nسامانه اعتباری و لندتک\nمارکت‌پلیس\nکارگزاری بورس\nسایر کسب‌وکارها" );
	$biztypes    = array_filter( array_map( 'trim', explode( "\n", $biztype_raw ) ) );
	$biztype_keys = array( 'fin', 'psp', 'wallet', 'crypto', 'lend', 'market', 'broker', 'other' );
	?>
	<section class="sec" style="padding-block-start:0" id="lead">
	  <div class="gx-wrap">
	    <div class="lead-band rv">
	      <div class="lb-grid">
	        <div class="lb-copy">
	          <span class="gx-eyebrow on-dark"><i></i><?php echo esc_html( uid_section_val( 'gxlead', 'eyebrow', __( 'از واژه تا پیاده‌سازی', 'uid-theme' ) ) ); ?></span>
	          <h2><?php echo esc_html( uid_section_val( 'gxlead', 'heading', __( 'کدام‌یک از این سرویس‌ها به کار شما می‌آید؟', 'uid-theme' ) ) ); ?></h2>
	          <p><?php echo wp_kses( uid_section_val( 'gxlead', 'text', __( 'اگر تعریف‌ها را خواندید و هنوز مطمئن نیستید کدام ترکیب برای فرایند ثبت‌نام شما درست است — <span class="lat">PWA</span> یا <span class="lat">API</span>، تطبیق چهره یا تطبیق کارت، شاهکار پیش از <span class="lat">OTP</span> یا بعد از آن — شماره‌تان را بگذارید تا کارشناس یوآیدی معماری مناسب کسب‌وکارتان را با شما مرور کند.', 'uid-theme' ) ), array( 'span' => array( 'class' => array() ) ) ); ?></p>
	          <?php if ( $trust ) : ?>
	          <div class="lb-trust">
	            <?php foreach ( $trust as $t ) : ?>
	            <span><?php echo uid_gx_check_icon(); ?><?php echo esc_html( $t ); ?></span>
	            <?php endforeach; ?>
	          </div>
	          <?php endif; ?>
	        </div>
	        <div class="lb-form">
	          <h3><?php echo esc_html( uid_section_val( 'gxlead', 'form_title', __( 'درخواست مشاوره و کلید آزمایشی', 'uid-theme' ) ) ); ?></h3>
	          <p class="hint"><?php echo esc_html( uid_section_val( 'gxlead', 'form_hint', __( 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.', 'uid-theme' ) ) ); ?></p>
	          <form id="leadForm" novalidate>
	            <input type="hidden" name="source" value="glossary-lp">
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
	            <div class="route-alert" id="routeAlert"><?php echo uid_gx_info_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'gxlead', 'route_text', __( 'وب‌سرویس‌های این واژه‌نامه به کسب‌وکارها ارائه می‌شوند. اگر به‌صورت شخصی دنبال احراز هویت هستید، ', 'uid-theme' ) ) ); ?><a href="<?php echo esc_url( home_url( '/sana/' ) ); ?>"><?php esc_html_e( 'احراز هویت سامانه ثنا', 'uid-theme' ); ?></a> <?php esc_html_e( 'در دسترس شماست.', 'uid-theme' ); ?></span></div>
	            <button class="gx-btn gx-btn-cta gx-btn-block" type="button" data-submit><?php echo uid_gx_submit_icon(); ?><?php echo esc_html( uid_section_val( 'gxlead', 'submit_text', __( 'ارسال درخواست و دریافت مشاوره رایگان', 'uid-theme' ) ) ); ?></button>
	            <div class="lb-note"><?php echo uid_gx_shield_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'gxlead', 'note_text', __( 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.', 'uid-theme' ) ) ); ?></span></div>
	            <div class="form-ok"><?php echo uid_gx_success_icon(); ?>
	              <span><?php echo esc_html( uid_section_val( 'gxlead', 'success_text', __( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ', 'uid-theme' ) ) ); ?><span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div>
	          </form>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/**
 * مودال درخواست سریع — همه دکمه‌های [data-open-modal] این صفحه همین را باز می‌کنند
 */
function uid_render_gx_quick_modal() {
	?>
	<div class="modal" id="modal" data-open="0" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
	  <div class="modal-bg" data-close-modal></div>
	  <div class="modal-box">
	    <button class="modal-x" data-close-modal aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><?php echo uid_gx_x_icon(); ?></button>
	    <h3 id="modalTitle"><?php esc_html_e( 'مشاوره رایگان و دریافت کلید آزمایشی', 'uid-theme' ); ?></h3>
	    <p><?php esc_html_e( 'شماره‌تان را بگذارید تا کارشناس یوآیدی همین امروز تماس بگیرد: کلید آزمایشی، دسترسی', 'uid-theme' ); ?> <span class="lat">Sandbox</span>، <?php esc_html_e( 'مستندات کامل وب‌سرویس‌ها و تعرفه متناسب با حجم استعلام ماهانه شما.', 'uid-theme' ); ?></p>
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
	  <button class="gx-btn gx-btn-cta gx-btn-block" type="button" data-submit><?php echo uid_gx_submit_icon(); ?> <?php esc_html_e( 'ارسال درخواست و مشاوره رایگان', 'uid-theme' ); ?></button>
	  <p class="tiny" style="margin-top:10px;color:#7D91B4"><?php esc_html_e( 'کارشناس یوآیدی در ساعات پاسخگویی، سریع با شما تماس می‌گیرد.', 'uid-theme' ); ?></p>
	</div>
	<div class="form-ok"><?php echo uid_gx_success_icon(); ?><b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b>
	  <span><?php esc_html_e( 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری:', 'uid-theme' ); ?> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></span></div></form>
	    <p class="tiny" style="margin-top:14px;color:#7D91B4;text-align:center">
	      <?php esc_html_e( 'ترجیح می‌دهید همین حالا صحبت کنید؟', 'uid-theme' ); ?>
	      <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" style="color:#7FE0EC;font-weight:700" class="mono"><?php echo esc_html( uid_phone_display() ); ?></a></p>
	  </div>
	</div>
	<?php
}

/* =====================================================================
 * ثبت تنظیمات پیشخوان برای همه‌ی سکشن‌های این صفحه
 * ===================================================================== */
function uid_register_gx_settings() {
	register_setting( 'uid_gx_group', 'uid_gx_layout', array(
		'sanitize_callback' => 'uid_sanitize_gx_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_gx_layout_main', '', '__return_false', 'uid_gx_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_gx_layout', 'uid_gx_layout_main', array(
		'option_name' => 'uid_gx_layout', 'registry_fn' => 'uid_gx_sections_registry', 'layout_fn' => 'uid_get_gx_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_gx_group', 'uid_section_gxhero', array( 'sanitize_callback' => 'uid_sanitize_section_gxhero', 'default' => array() ) );
	add_settings_section( 'uid_section_gxhero_main', '', '__return_false', 'uid_section_gxhero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'eyebrow', 'default' => 'منابع یوآیدی' ) );
	add_settings_field( 'heading', __( 'عنوان اصلی (تگ mark مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'heading', 'default' => 'واژه‌نامه اصطلاحات <mark>احراز هویت</mark>' ) );
	add_settings_field( 'text', __( 'توضیح (تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'text', 'default' => 'هر واژه‌ای که در قرارداد، مستندات فنی یا جلسه ارزیابی ریسک با آن روبه‌رو می‌شوید — با تعریف دقیق، معادل انگلیسی، و لینک مستقیم به وب‌سرویسی که همان واژه را عملی می‌کند. تفاوت‌های ظریف را هم نگه داشته‌ایم: <b>e-KYC</b> واژه دقیق‌تر از <b>KYC</b> است، و <b>تطبیق چهره</b> همان <b>تشخیص چهره</b> نیست.', 'rows' => 4 ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه جستجو', 'uid-theme' ), 'uid_field_text', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'btn1_text', 'default' => 'جستجو در ۳۰ اصطلاح' ) );
	add_settings_field( 'tags', __( 'برچسب‌های زیر دکمه‌ها (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxhero', 'uid_section_gxhero_main', array( 'group' => 'uid_section_gxhero', 'key' => 'tags', 'default' => "<b>۳۰</b> اصطلاح\n<b>۴</b> دسته موضوعی\n<b>۱۲</b> صفحه سرویس مرتبط\nجستجوی فارسی و انگلیسی" ) );
	add_settings_field( 'preview', __( 'ردیف‌های پیش‌نمایش (کارت کنار هیرو)', 'uid-theme' ), 'uid_field_repeater', 'uid_section_gxhero', 'uid_section_gxhero_main', array(
		'group' => 'uid_section_gxhero', 'key' => 'preview', 'default' => uid_default_gx_preview(), 'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام فارسی/لاتین', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'en', 'type' => 'text', 'label' => __( 'معادل انگلیسی', 'uid-theme' ) ),
			array( 'key' => 'color', 'type' => 'select', 'label' => __( 'رنگ نقطه', 'uid-theme' ), 'options' => array( 'teal' => __( 'فیروزه‌ای', 'uid-theme' ), 'orange' => __( 'نارنجی', 'uid-theme' ), 'navy' => __( 'سرمه‌ای', 'uid-theme' ), 'green' => __( 'سبز', 'uid-theme' ) ) ),
		),
	) );

	/* ---------------- باند آمار کوتاه ---------------- */
	register_setting( 'uid_gx_group', 'uid_section_gxstats', array( 'sanitize_callback' => 'uid_sanitize_section_gxstats', 'default' => array() ) );
	add_settings_section( 'uid_section_gxstats_main', '', '__return_false', 'uid_section_gxstats' );
	add_settings_field( 'items', __( 'خانه‌های آمار', 'uid-theme' ), 'uid_field_repeater', 'uid_section_gxstats', 'uid_section_gxstats_main', array(
		'group' => 'uid_section_gxstats', 'key' => 'items', 'default' => uid_default_gx_stats(), 'add_label' => __( 'افزودن مورد', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عنوان کوتاه', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- کپسول ۳۰ ثانیه‌ای ---------------- */
	register_setting( 'uid_gx_group', 'uid_section_gxtldr', array( 'sanitize_callback' => 'uid_sanitize_section_gxtldr', 'default' => array() ) );
	add_settings_section( 'uid_section_gxtldr_main', '', '__return_false', 'uid_section_gxtldr' );
	add_settings_field( 'heading', __( 'عنوان کپسول', 'uid-theme' ), 'uid_field_text', 'uid_section_gxtldr', 'uid_section_gxtldr_main', array( 'group' => 'uid_section_gxtldr', 'key' => 'heading', 'default' => 'اگر دنبال یک واژه مشخص هستید' ) );
	add_settings_field( 'badge', __( 'برچسب کوچک (مثل «۳۰ ثانیه»)', 'uid-theme' ), 'uid_field_text', 'uid_section_gxtldr', 'uid_section_gxtldr_main', array( 'group' => 'uid_section_gxtldr', 'key' => 'badge', 'default' => '۳۰ ثانیه' ) );
	add_settings_field( 'items', __( 'خطوط خلاصه (هر خط یک مورد؛ تگ b مجاز است)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxtldr', 'uid_section_gxtldr_main', array( 'group' => 'uid_section_gxtldr', 'key' => 'items', 'default' => implode( "\n", uid_default_gx_tldr_items() ), 'rows' => 5 ) );
	add_settings_field( 'btn_text', __( 'متن دکمه (باز کردن مودال)', 'uid-theme' ), 'uid_field_text', 'uid_section_gxtldr', 'uid_section_gxtldr_main', array( 'group' => 'uid_section_gxtldr', 'key' => 'btn_text', 'default' => 'مشاوره رایگان و کلید آزمایشی' ) );
	add_settings_field( 'more_text', __( 'متن پرش نرم به جستجو', 'uid-theme' ), 'uid_field_text', 'uid_section_gxtldr', 'uid_section_gxtldr_main', array( 'group' => 'uid_section_gxtldr', 'key' => 'more_text', 'default' => 'رفتن به کادر جستجو و فهرست اصطلاحات' ) );

	/* ---------------- معرفی جعبه جستجو ---------------- */
	register_setting( 'uid_gx_group', 'uid_section_gxexplorer', array( 'sanitize_callback' => 'uid_sanitize_section_gxexplorer', 'default' => array() ) );
	add_settings_section( 'uid_section_gxexplorer_main', '', '__return_false', 'uid_section_gxexplorer' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_gxexplorer', 'uid_section_gxexplorer_main', array( 'group' => 'uid_section_gxexplorer', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_gxexplorer', 'uid_section_gxexplorer_main', array( 'group' => 'uid_section_gxexplorer', 'key' => 'eyebrow', 'default' => 'فهرست اصطلاحات' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_gxexplorer', 'uid_section_gxexplorer_main', array( 'group' => 'uid_section_gxexplorer', 'key' => 'heading', 'default' => 'واژه را پیدا کنید، تعریفش را باز کنید' ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxexplorer', 'uid_section_gxexplorer_main', array( 'group' => 'uid_section_gxexplorer', 'key' => 'text', 'default' => 'جستجو هم‌زمان روی نام فارسی، معادل انگلیسی و متن تعریف کار می‌کند. فهرست در حالت بسته فقط نام اصطلاح‌ها را نشان می‌دهد تا صفحه کوتاه بماند.' ) );

	/* ---------------- چهار فصل موضوعی ---------------- */
	$chapters = array(
		'gxbase' => array( 'heading' => 'اصطلاحات پایه هویت و امنیت', 'text' => 'واژه‌هایی که چارچوب قانونی و امنیتی احراز هویت را می‌سازند — از KYC و AML تا رمز یکبار مصرف و احراز چندعاملی.', 'fold_title' => 'اصطلاحات پایه هویت و امنیت', 'fold_teaser' => 'KYC، e-KYC، هویت دیجیتال، AML، FDP' ),
		'gxbio'  => array( 'heading' => 'اصطلاحات فناوری‌های بایومتریک', 'text' => 'چهره، صدا و زنده‌بودن. دقیق‌ترین بخش این واژه‌نامه، چون تفاوت‌های ظریفش در مستندات فنی معمولاً جابه‌جا استفاده می‌شوند.', 'fold_title' => 'اصطلاحات فناوری‌های بایومتریک', 'fold_teaser' => 'بایومتریک، تشخیص چهره، تطبیق چهره، تشخیص زنده بودن، حالت فعال' ),
		'gxfin'  => array( 'heading' => 'اصطلاحات مالی و احراز اطلاعات', 'text' => 'سامانه‌های مرجعی که پاسخ قطعی می‌دهند: شاهکار، ثبت احوال، تطبیق کارت و اعتبارسنجی.', 'fold_title' => 'اصطلاحات مالی و احراز اطلاعات', 'fold_teaser' => 'شاهکار، ثبت احوال، تطبیق کارت، اعتبارسنجی، استعلام آنی' ),
		'gxdev'  => array( 'heading' => 'اصطلاحات فنی برای توسعه‌دهندگان', 'text' => 'هر چیزی که تیم فنی پیش از اتصال باید بداند — از API و SDK تا clientToken و metaData.', 'fold_title' => 'اصطلاحات فنی برای توسعه‌دهندگان', 'fold_teaser' => 'API، SDK، PWA، وب‌هوک، سندباکس' ),
	);
	foreach ( $chapters as $slug => $d ) {
		register_setting( 'uid_gx_group', 'uid_section_' . $slug, array( 'sanitize_callback' => 'uid_sanitize_section_gxchapter', 'default' => array() ) );
		add_settings_section( 'uid_section_' . $slug . '_main', '', '__return_false', 'uid_section_' . $slug );
		add_settings_field( 'heading', __( 'عنوان فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_' . $slug, 'uid_section_' . $slug . '_main', array( 'group' => 'uid_section_' . $slug, 'key' => 'heading', 'default' => $d['heading'] ) );
		add_settings_field( 'text', __( 'توضیح فصل', 'uid-theme' ), 'uid_field_textarea', 'uid_section_' . $slug, 'uid_section_' . $slug . '_main', array( 'group' => 'uid_section_' . $slug, 'key' => 'text', 'default' => $d['text'] ) );
		add_settings_field( 'fold_title', __( 'عنوان فصل (نمای تاخورده موبایل)', 'uid-theme' ), 'uid_field_text', 'uid_section_' . $slug, 'uid_section_' . $slug . '_main', array( 'group' => 'uid_section_' . $slug, 'key' => 'fold_title', 'default' => $d['fold_title'] ) );
		add_settings_field( 'fold_teaser', __( 'معرفی کوتاه فصل', 'uid-theme' ), 'uid_field_text', 'uid_section_' . $slug, 'uid_section_' . $slug . '_main', array( 'group' => 'uid_section_' . $slug, 'key' => 'fold_teaser', 'default' => $d['fold_teaser'] ) );
		add_settings_field( 'terms_notice', '', 'uid_field_notice', 'uid_section_' . $slug, 'uid_section_' . $slug . '_main', array( 'text' => __( 'فهرست اصطلاحات این فصل، محتوای تخصصی و به‌هم‌مرتبط است (دکمه‌های «اصطلاح مرتبط» به یکدیگر ارجاع می‌دهند) و از این صفحه قابل‌ویرایش نیست — برای تغییر به inc/glossary-page.php مراجعه کنید.', 'uid-theme' ) ) );
	}

	/* ---------------- بنر تماس نهایی ---------------- */
	register_setting( 'uid_gx_group', 'uid_section_gxlead', array( 'sanitize_callback' => 'uid_sanitize_section_gxlead', 'default' => array() ) );
	add_settings_section( 'uid_section_gxlead_main', '', '__return_false', 'uid_section_gxlead' );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'eyebrow', 'default' => 'از واژه تا پیاده‌سازی' ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'heading', 'default' => 'کدام‌یک از این سرویس‌ها به کار شما می‌آید؟' ) );
	add_settings_field( 'text', __( 'توضیح (span class=lat مجاز)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'text', 'default' => 'اگر تعریف‌ها را خواندید و هنوز مطمئن نیستید کدام ترکیب برای فرایند ثبت‌نام شما درست است — <span class="lat">PWA</span> یا <span class="lat">API</span>، تطبیق چهره یا تطبیق کارت، شاهکار پیش از <span class="lat">OTP</span> یا بعد از آن — شماره‌تان را بگذارید تا کارشناس یوآیدی معماری مناسب کسب‌وکارتان را با شما مرور کند.' ) );
	add_settings_field( 'trust', __( 'برچسب‌های اطمینان (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'trust', 'default' => "مشاوره رایگان، بدون تعهد\nکلید آزمایشی و سندباکس پیش از قرارداد\nمستندات کامل فارسی برای تیم فنی\nپشتیبانی اختصاصی سازمانی" ) );
	add_settings_field( 'form_title', __( 'عنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'form_title', 'default' => 'درخواست مشاوره و کلید آزمایشی' ) );
	add_settings_field( 'form_hint', __( 'زیرعنوان فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'form_hint', 'default' => 'فقط سه فیلد. کارشناس ما در سریع‌ترین زمان ممکن تماس می‌گیرد.' ) );
	add_settings_field( 'biztype_options', __( 'گزینه‌های نوع کسب‌وکار (هر خط یک مورد)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'biztype_options', 'default' => "ثبت‌نام و خدمات مالی\nدرگاه و پلتفرم پرداخت\nکیف پول دیجیتال\nصرافی رمزارز\nسامانه اعتباری و لندتک\nمارکت‌پلیس\nکارگزاری بورس\nسایر کسب‌وکارها", 'rows' => 8 ) );
	add_settings_field( 'route_text', __( 'متن هشدار کاربر شخصی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'route_text', 'default' => 'وب‌سرویس‌های این واژه‌نامه به کسب‌وکارها ارائه می‌شوند. اگر به‌صورت شخصی دنبال احراز هویت هستید، ' ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'submit_text', 'default' => 'ارسال درخواست و دریافت مشاوره رایگان' ) );
	add_settings_field( 'note_text', __( 'متن محرمانگی', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'note_text', 'default' => 'اطلاعات شما محرمانه می‌ماند و فقط برای همین درخواست استفاده می‌شود.' ) );
	add_settings_field( 'success_text', __( 'متن پیام موفقیت', 'uid-theme' ), 'uid_field_text', 'uid_section_gxlead', 'uid_section_gxlead_main', array( 'group' => 'uid_section_gxlead', 'key' => 'success_text', 'default' => 'تیم یوآیدی به‌زودی با شما تماس می‌گیرد. برای پیگیری فوری: ' ) );
}
add_action( 'admin_init', 'uid_register_gx_settings' );

/* =====================================================================
 * توابع پاک‌سازی (sanitize)
 * ===================================================================== */

/**
 * مثل sanitize_textarea_field ولی تگ <b> را روی هر خط نگه می‌دارد — برای
 * فیلدهای چندخطی که «تگ b مجاز است».
 */
function uid_sanitize_multiline_b( $input ) {
	$lines = explode( "\n", (string) $input );
	$out   = array();
	foreach ( $lines as $line ) {
		$out[] = wp_kses( trim( $line ), array( 'b' => array() ) );
	}
	return implode( "\n", $out );
}

function uid_sanitize_section_gxhero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => wp_kses( $input['heading'] ?? '', array( 'mark' => array() ) ),
		'text'      => wp_kses( $input['text'] ?? '', array( 'b' => array() ) ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'tags'      => uid_sanitize_multiline_b( $input['tags'] ?? '' ),
		'preview'   => uid_sanitize_repeater_rows( $input['preview'] ?? '[]', array(
			array( 'key' => 'name', 'type' => 'text', 'required' => true ),
			array( 'key' => 'en', 'type' => 'text' ),
			array( 'key' => 'color', 'type' => 'text' ),
		) ),
	);
}
function uid_sanitize_section_gxstats( $input ) {
	return array(
		'items' => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}
function uid_sanitize_section_gxtldr( $input ) {
	return array(
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'badge'     => sanitize_text_field( $input['badge'] ?? '' ),
		'items'     => uid_sanitize_multiline_b( $input['items'] ?? '' ),
		'btn_text'  => sanitize_text_field( $input['btn_text'] ?? '' ),
		'more_text' => sanitize_text_field( $input['more_text'] ?? '' ),
	);
}
function uid_sanitize_section_gxexplorer( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}
function uid_sanitize_section_gxchapter( $input ) {
	return array(
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'fold_title'  => sanitize_text_field( $input['fold_title'] ?? '' ),
		'fold_teaser' => sanitize_text_field( $input['fold_teaser'] ?? '' ),
	);
}
function uid_sanitize_section_gxlead( $input ) {
	return array(
		'eyebrow'         => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'         => sanitize_text_field( $input['heading'] ?? '' ),
		'text'            => wp_kses( $input['text'] ?? '', array( 'span' => array( 'class' => array() ) ) ),
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
