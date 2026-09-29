<?php
/**
 * منوی اصلی هدر (مگامنوی چهارستونی + لینک‌های ساده + شیت موبایل) و منوهای ستونی
 * فوتر — هر دو کاملاً از پیشخوان ← تنظیمات قالب یوآیدی ← «هدر»/«فوتر» مدیریت
 * می‌شوند (نه از پیشخوان ← ظاهر ← منوها). هر آیتم مگامنو می‌تواند تصویر
 * اختصاصی (از کتابخانه رسانه) داشته باشد.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * دیفالت لینک‌های ساده نوار منو (کنار دکمه مگامنو «خدمات» و ردیف «وب‌سرویس‌ها»)
 */
function uid_default_nav_links() {
	return array(
		array( 'text' => 'مستندات', 'url' => '/docs/' ),
		array( 'text' => 'وبلاگ', 'url' => 'https://blog.u-id.net/' ),
		array( 'text' => 'تماس با ما', 'url' => '/contact-us/' ),
	);
}

/**
 * دیفالت آیتم‌های داخل ستون‌های مگامنو. ستون ۱ («راهکارهای یکپارچه») به‌صورت
 * کارت‌های بزرگ‌تر با توضیح نمایش داده می‌شود؛ ستون‌های ۲ تا ۴ ردیف‌های فشرده‌اند.
 */
function uid_default_mega_items() {
	return array(
		// ستون ۱ — راهکارهای یکپارچه (کارت‌های ویژه)
		array( 'column' => '1', 'icon' => 'shield', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'یوآیدی‌پلاس (PWA)', 'subtitle' => 'برون‌سپاری کامل احراز هویت', 'url' => '/uid-plus/' ),
		array( 'column' => '1', 'icon' => 'code', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت تصویری (e-KYC)', 'subtitle' => 'Passive Liveness و تطبیق چهره', 'url' => '/api-ekyc/' ),
		array( 'column' => '1', 'icon' => 'crypto', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت صرافی ارز دیجیتال', 'subtitle' => 'مخصوص پلتفرم‌های رمزارز', 'url' => '/authentication-digital-currency-exchange/' ),

		// ستون ۲ — احراز هویت و هویت فردی
		array( 'column' => '2', 'icon' => 'doc', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت ثنا', 'subtitle' => '', 'url' => '/sana/' ),
		array( 'column' => '2', 'icon' => 'people', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'ثنا ویژه ایرانیان خارج از کشور', 'subtitle' => '', 'url' => '/sana-register-foreign-form/' ),
		array( 'column' => '2', 'icon' => 'medal', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت سجام', 'subtitle' => '', 'url' => '/sejamauthentication/' ),
		array( 'column' => '2', 'icon' => 'doc', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'وب‌سرویس ثبت احوال', 'subtitle' => '', 'url' => '/api-inquiry-person/' ),
		array( 'column' => '2', 'icon' => 'simcard', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'وب‌سرویس شاهکار', 'subtitle' => '', 'url' => '/api-shahkar/' ),
		array( 'column' => '2', 'icon' => 'pin', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'استعلام کد پستی و آدرس', 'subtitle' => '', 'url' => '/address-postcode-docs/' ),

		// ستون ۳ — استعلام و تطبیق مالی
		array( 'column' => '3', 'icon' => 'card', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'استعلام اطلاعات مالی (شبا)', 'subtitle' => '', 'url' => '/api-inquiry-iban/' ),
		array( 'column' => '3', 'icon' => 'card-convert', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تبدیل کارت به شبا', 'subtitle' => '', 'url' => '/api-inquiry-card/' ),
		array( 'column' => '3', 'icon' => 'lock', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تطبیق شبا با کد ملی', 'subtitle' => '', 'url' => '/api-validate-iban/' ),
		array( 'column' => '3', 'icon' => 'card-check', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تطبیق شماره کارت با کد ملی', 'subtitle' => '', 'url' => '/api-validate-card/' ),

		// ستون ۴ — فناوری‌ها و منابع
		array( 'column' => '4', 'icon' => 'scan', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تطبیق و تشخیص چهره', 'subtitle' => '', 'url' => '/face-detection/' ),
		array( 'column' => '4', 'icon' => 'wave', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تشخیص زنده‌بودن', 'subtitle' => '', 'url' => '/liveness-detection/' ),
		array( 'column' => '4', 'icon' => 'book', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'مستندات فنی', 'subtitle' => '', 'url' => '/docs/' ),
		array( 'column' => '4', 'icon' => 'glossary', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'واژه‌نامه اصطلاحات', 'subtitle' => '', 'url' => '/glossary/' ),
	);
}

/**
 * آیکون‌های SVG هر آیتم مگامنو — فقط وقتی ادمین تصویر اختصاصی آپلود نکرده باشد
 */
function uid_mega_icon_svg( $key ) {
	$icons = array(
		'code'         => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'crypto'       => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'doc'          => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'medal'        => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'pin'          => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'card'         => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'card-convert' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M15 15h4"/>',
		'card-check'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M14.5 15.5l2 2 4-4.5"/>',
		'lock'         => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
		'people'       => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'scan'         => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'wave'         => '<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M8 10a4 4 0 011-2.6"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/><path d="M7 20c1-2 1.5-4.5 1.5-7v-2"/>',
		'book'         => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/>',
		'glossary'     => '<path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/><path d="M9 7h6M9 11h4"/>',
		'simcard'      => '<rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18.5h2"/>',
		'shield'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}

/**
 * مگامنو را روی چهار ستون گروه‌بندی می‌کند و عنوان هر ستون را برمی‌گرداند —
 * منبع مشترک برای پنل دسکتاپ (uid_render_mega_panel) و شیت موبایل
 * (uid_render_mobile_sheet) تا این دو هیچ‌وقت از هم عقب نیفتند.
 */
function uid_get_mega_columns() {
	$items = uid_get_option( 'uid_header_options', 'mega_items', uid_default_mega_items() );
	if ( ! is_array( $items ) ) $items = array();

	$cols = array( '1' => array(), '2' => array(), '3' => array(), '4' => array() );
	foreach ( $items as $it ) {
		$c = isset( $it['column'] ) && isset( $cols[ $it['column'] ] ) ? $it['column'] : '1';
		$cols[ $c ][] = $it;
	}

	$titles = array(
		'1' => uid_get_option( 'uid_header_options', 'mega_col1_title', __( 'راهکارهای یکپارچه', 'uid-theme' ) ),
		'2' => uid_get_option( 'uid_header_options', 'mega_col2_title', __( 'احراز هویت و هویت فردی', 'uid-theme' ) ),
		'3' => uid_get_option( 'uid_header_options', 'mega_col3_title', __( 'استعلام و تطبیق مالی', 'uid-theme' ) ),
		'4' => uid_get_option( 'uid_header_options', 'mega_col4_title', __( 'فناوری‌ها و منابع', 'uid-theme' ) ),
	);

	return array( 'cols' => $cols, 'titles' => $titles );
}

/**
 * لینک‌های ردیف اصلی نوار (دکمه مگامنو + ردیف آرشیو وب‌سرویس‌ها + لینک‌های ساده
 * قابل‌ویرایش). خروجی داخل ‎.navlinks‎ چاپ می‌شود؛ پنل مگامنو جدا و با
 * uid_render_mega_panel() چاپ می‌شود تا فرزند مستقیم ‎.nav‎ بماند و روی کل پیل
 * وسط‌چین شود (نه فقط زیر دکمه).
 */
function uid_render_nav_links() {
	$mega_label = uid_get_option( 'uid_header_options', 'mega_label', __( 'خدمات', 'uid-theme' ) );
	?>
	<button type="button" class="navlink" id="megaBtn" aria-expanded="false" aria-controls="mega">
		<?php echo esc_html( $mega_label ); ?>
		<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
	</button>
	<a class="navlink" href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><span class="pip"></span><?php esc_html_e( 'وب‌سرویس‌ها', 'uid-theme' ); ?></a>
	<?php
	$nav_links = uid_get_option( 'uid_header_options', 'nav_links', uid_default_nav_links() );
	if ( ! is_array( $nav_links ) ) $nav_links = array();
	foreach ( $nav_links as $link ) {
		$url = $link['url'] ?? '#';
		$url = ( '' !== $url && '/' === $url[0] ) ? home_url( $url ) : $url;
		echo '<a class="navlink" href="' . esc_url( $url ) . '">' . esc_html( $link['text'] ?? '' ) . '</a>';
	}
}

/**
 * پنل مگامنو (چهار ستون). ستون ۱ به‌صورت کارت‌های بزرگ (mega-feat) و ستون‌های
 * ۲ تا ۴ به‌صورت ردیف‌های فشرده (mega-link) چاپ می‌شوند. آیتم اول ستون ۱ با
 * رنگ پیش‌فرض (نارنجی) و بقیه با رنگ فیروزه‌ای مشخص می‌شوند.
 */
function uid_render_mega_panel() {
	$data   = uid_get_mega_columns();
	$cols   = $data['cols'];
	$titles = $data['titles'];
	$icon_allowed = array(
		'path'    => array( 'd' => true ),
		'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ),
		'circle'  => array( 'cx' => true, 'cy' => true, 'r' => true ),
		'rect'    => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
	);
	?>
	<div class="mega" id="mega" data-open="0" role="region" aria-label="<?php esc_attr_e( 'خدمات یوآیدی', 'uid-theme' ); ?>">
		<div class="mega-grid">
			<?php foreach ( array( '1', '2', '3', '4' ) as $c ) :
				if ( empty( $cols[ $c ] ) ) continue;
				$is_feat = ( '1' === $c );
				?>
				<div class="mega-col<?php echo '4' === $c ? ' tech' : ''; ?>">
					<h4><?php echo esc_html( $titles[ $c ] ); ?></h4>
					<?php foreach ( $cols[ $c ] as $i => $link ) :
						$img_id = absint( $link['image_id'] ?? 0 );
						$icon   = $img_id
							? wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => $link['image_alt'] ?? ( $link['title'] ?? '' ) ) )
							: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . wp_kses( uid_mega_icon_svg( $link['icon'] ?? 'shield' ), $icon_allowed ) . '</svg>';
						$url = esc_url( $link['url'] ?? '#' );
						if ( $is_feat ) : ?>
							<a class="mega-feat<?php echo $i > 0 ? ' teal' : ''; ?>" href="<?php echo $url; ?>">
								<span class="ic"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped/kses'd pieces above ?></span>
								<span><b><?php echo esc_html( $link['title'] ?? '' ); ?></b><?php if ( ! empty( $link['subtitle'] ) ) : ?><span><?php echo esc_html( $link['subtitle'] ); ?></span><?php endif; ?></span>
							</a>
						<?php else : ?>
							<a class="mega-link" href="<?php echo $url; ?>"><span class="ic"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $link['title'] ?? '' ); ?></a>
						<?php endif;
					endforeach;
					if ( '4' === $c ) : ?>
						<a class="mega-all" href="<?php echo esc_url( home_url( '/features/' ) ); ?>"><?php esc_html_e( 'همه فناوری‌ها', 'uid-theme' ); ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="mega-foot">
			<a class="mega-arch" href="<?php echo esc_url( home_url( '/api/' ) ); ?>">
				<span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></span>
				<span><b><?php esc_html_e( 'همه وب‌سرویس‌ها در یک صفحه', 'uid-theme' ); ?></b><span><?php esc_html_e( 'مقایسه، تعرفه و مستندات هر سرویس', 'uid-theme' ); ?></span></span>
			</a>
			<a class="tlink" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><?php esc_html_e( 'در حین ثبت‌نام به مشکل خوردید؟ تماس بگیرید', 'uid-theme' ); ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></a>
		</div>
	</div>
	<?php
}

/**
 * شیت تمام‌صفحه موبایل: همان داده‌ی مگامنو، اما به‌صورت فهرست تخت و بخش‌بندی‌شده
 * (نه گرید کارتی) چون روی موبایل هاور معنا ندارد و اسکرول عمودی طبیعی‌تر است.
 */
function uid_render_mobile_sheet() {
	$data   = uid_get_mega_columns();
	$cols   = $data['cols'];
	$titles = $data['titles'];
	$arrow  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>';
	?>
	<h5><?php esc_html_e( 'دسترسی سریع', 'uid-theme' ); ?></h5>
	<a class="mrow" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?> <?php echo $arrow; ?></a>
	<a class="mrow hot" href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><?php esc_html_e( 'همه وب‌سرویس‌ها', 'uid-theme' ); ?> <span class="tag"><?php esc_html_e( 'آرشیو', 'uid-theme' ); ?></span></a>
	<?php if ( ! empty( $cols['1'] ) ) : $first = $cols['1'][0]; ?>
		<a class="mrow hot" href="<?php echo esc_url( $first['url'] ?? '/uid-plus/' ); ?>"><?php echo esc_html( $first['title'] ?? '' ); ?> <?php echo $arrow; ?></a>
	<?php endif;

	foreach ( array( '2', '3', '4' ) as $c ) :
		if ( empty( $cols[ $c ] ) ) continue;
		?>
		<h5><?php echo esc_html( $titles[ $c ] ); ?></h5>
		<?php foreach ( $cols[ $c ] as $link ) : ?>
			<a class="mrow" href="<?php echo esc_url( $link['url'] ?? '#' ); ?>"><?php echo esc_html( $link['title'] ?? '' ); ?> <?php echo $arrow; ?></a>
		<?php endforeach;
		if ( '4' === $c ) : ?>
			<a class="mrow" href="<?php echo esc_url( home_url( '/features/' ) ); ?>"><?php esc_html_e( 'همه فناوری‌ها', 'uid-theme' ); ?> <?php echo $arrow; ?></a>
		<?php endif;
	endforeach;
	?>
	<h5><?php esc_html_e( 'یوآیدی', 'uid-theme' ); ?></h5>
	<a class="mrow" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'درباره ما', 'uid-theme' ); ?> <?php echo $arrow; ?></a>
	<?php
	$nav_links = uid_get_option( 'uid_header_options', 'nav_links', uid_default_nav_links() );
	if ( ! is_array( $nav_links ) ) $nav_links = array();
	foreach ( $nav_links as $link ) {
		$url = $link['url'] ?? '#';
		$url = ( '' !== $url && '/' === $url[0] ) ? home_url( $url ) : $url;
		echo '<a class="mrow" href="' . esc_url( $url ) . '">' . esc_html( $link['text'] ?? '' ) . ' ' . $arrow . '</a>';
	}
	?>
	<div class="mfoot">
		<button type="button" class="btn btn-cta btn-block" data-open-lead-modal><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg> <?php esc_html_e( 'مشاوره رایگان و دریافت کلید آزمایشی', 'uid-theme' ); ?></button>
		<a class="btn btn-ghost btn-block" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg> <span class="mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
	</div>
	<?php
}

/**
 * دیفالت لینک‌های ستون‌های فوتر — سه ستون: سرویس‌های هویتی / استعلام و فناوری /
 * یوآیدی و منابع (به‌جای تقسیم قبلی که «محصولات» را در یک ستون ۱۱‌ردیفی می‌ریخت)
 */
function uid_default_footer_links() {
	return array(
		array( 'column' => '1', 'text' => 'یوآیدی‌پلاس (PWA)', 'url' => '/uid-plus/' ),
		array( 'column' => '1', 'text' => 'وب‌سرویس احراز هویت تصویری', 'url' => '/api-ekyc/' ),
		array( 'column' => '1', 'text' => 'احراز هویت ثنا', 'url' => '/sana/' ),
		array( 'column' => '1', 'text' => 'ثنا ویژه ایرانیان خارج از کشور', 'url' => '/sana-register-foreign-form/' ),
		array( 'column' => '1', 'text' => 'احراز هویت سجام', 'url' => '/sejamauthentication/' ),
		array( 'column' => '1', 'text' => 'وب‌سرویس ثبت احوال', 'url' => '/api-inquiry-person/' ),
		array( 'column' => '1', 'text' => 'وب‌سرویس شاهکار', 'url' => '/api-shahkar/' ),
		array( 'column' => '1', 'text' => 'احراز هویت صرافی ارز دیجیتال', 'url' => '/authentication-digital-currency-exchange/' ),

		array( 'column' => '2', 'text' => 'استعلام اطلاعات مالی (شبا)', 'url' => '/api-inquiry-iban/' ),
		array( 'column' => '2', 'text' => 'تبدیل کارت به شبا', 'url' => '/api-inquiry-card/' ),
		array( 'column' => '2', 'text' => 'تطبیق شبا با کد ملی', 'url' => '/api-validate-iban/' ),
		array( 'column' => '2', 'text' => 'تطبیق شماره کارت با کد ملی', 'url' => '/api-validate-card/' ),
		array( 'column' => '2', 'text' => 'استعلام کد پستی و آدرس', 'url' => '/address-postcode-docs/' ),
		array( 'column' => '2', 'text' => 'تطبیق و تشخیص چهره', 'url' => '/face-detection/' ),
		array( 'column' => '2', 'text' => 'تشخیص زنده‌بودن', 'url' => '/liveness-detection/' ),

		array( 'column' => '3', 'text' => 'همه وب‌سرویس‌ها', 'url' => '/api/', 'tag' => 'آرشیو' ),
		array( 'column' => '3', 'text' => 'مستندات فنی', 'url' => '/docs/' ),
		array( 'column' => '3', 'text' => 'واژه‌نامه اصطلاحات', 'url' => '/glossary/' ),
		array( 'column' => '3', 'text' => 'سوالات متداول', 'url' => '/faq/' ),
		array( 'column' => '3', 'text' => 'وبلاگ', 'url' => 'https://blog.u-id.net/' ),
		array( 'column' => '3', 'text' => 'درباره ما', 'url' => '/about-us/' ),
		array( 'column' => '3', 'text' => 'تماس با ما', 'url' => '/contact-us/' ),
	);
}

/**
 * یک ستون از لینک‌های فوتر (شماره ستون: 1، 2 یا 3) — فهرست تخت از ‎<a>‎هاست
 * (نه ‎<ul><li>‎) چون هر ستون داخل ‎<details class="f-col">‎ چاپ می‌شود.
 */
function uid_render_footer_menu( $column ) {
	$links = uid_get_option( 'uid_footer_options', 'footer_links', uid_default_footer_links() );
	if ( ! is_array( $links ) ) $links = array();

	$column = (string) $column;
	echo '<div class="f-links">';
	$printed = false;
	foreach ( $links as $link ) {
		if ( ( $link['column'] ?? '1' ) !== $column ) continue;
		$printed = true;
		$url = $link['url'] ?? '#';
		$url = ( '' !== $url && '/' === $url[0] ) ? home_url( $url ) : $url;
		$tag = trim( (string) ( $link['tag'] ?? '' ) );
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $link['text'] ?? '' );
		if ( '' !== $tag ) echo '<span class="tag">' . esc_html( $tag ) . '</span>';
		echo '</a>';
	}
	if ( ! $printed && current_user_can( 'edit_theme_options' ) ) {
		echo '<span class="placeholder-flag">' . esc_html__( 'لینکی برای این ستون تنظیم نشده', 'uid-theme' ) . '</span>';
	}
	echo '</div>';
}
