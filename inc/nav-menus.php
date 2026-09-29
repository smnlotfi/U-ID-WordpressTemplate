<?php
/**
 * منوی اصلی هدر (مگامنوی سه‌ستونی + لینک‌های ساده) و منوهای ستونی فوتر —
 * هر دو کاملاً از پیشخوان ← تنظیمات قالب یوآیدی ← «هدر»/«فوتر» مدیریت می‌شوند
 * (نه از پیشخوان ← ظاهر ← منوها). هر آیتم مگامنو می‌تواند تصویر اختصاصی
 * (از کتابخانه رسانه) داشته باشد.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * دیفالت لینک‌های ساده نوار منو (کنار دکمه مگامنو «خدمات»)
 */
function uid_default_nav_links() {
	return array(
		array( 'text' => 'فناوری‌ها', 'url' => '/features/' ),
		array( 'text' => 'مستندات', 'url' => '/docs/' ),
		array( 'text' => 'وبلاگ', 'url' => 'https://blog.u-id.net/' ),
		array( 'text' => 'تماس با ما', 'url' => '/contact-us/' ),
	);
}

/**
 * دیفالت آیتم‌های داخل ستون‌های مگامنو (نه کارت ویژه پایین آن — یوآیدی‌پلاس
 * همان‌جا به‌صورت جداگانه و پررنگ‌تر تبلیغ می‌شود، پس در این لیست تکرار نشده)
 */
function uid_default_mega_items() {
	return array(
		array( 'column' => '1', 'icon' => 'code', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'وب‌سرویس احراز هویت', 'subtitle' => 'یکپارچه‌سازی مستقیم با API', 'url' => '/api/' ),
		array( 'column' => '1', 'icon' => 'crypto', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت صرافی ارز دیجیتال', 'subtitle' => 'مخصوص پلتفرم‌های رمزارز', 'url' => '/authentication-digital-currency-exchange/' ),

		array( 'column' => '2', 'icon' => 'doc', 'hot' => '1', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت ثنا', 'subtitle' => 'سامانه ابلاغ الکترونیک قضایی', 'url' => '/sana/' ),
		array( 'column' => '2', 'icon' => 'medal', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'احراز هویت سجام', 'subtitle' => 'بازار سرمایه', 'url' => '/sejamauthentication/' ),
		array( 'column' => '2', 'icon' => 'doc', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'وب سرویس ثبت احوال', 'subtitle' => 'استعلام هویت با کد ملی و تاریخ تولد', 'url' => '/api-inquiry-person/' ),
		array( 'column' => '2', 'icon' => 'pin', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'استعلام کد پستی و آدرس', 'subtitle' => 'نشانی کامل با یک کد پستی', 'url' => '/address-postcode-docs/' ),
		array( 'column' => '2', 'icon' => 'card', 'hot' => '1', 'image_id' => 0, 'image_alt' => '', 'title' => 'استعلام اطلاعات مالی (شبا)', 'subtitle' => 'مشخصات صاحب حساب با شماره شبا', 'url' => '/api-inquiry-iban/' ),
		array( 'column' => '2', 'icon' => 'people', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'ثنا ویژه ایرانیان خارج از کشور', 'subtitle' => 'ثبت سفارش آنلاین', 'url' => '/sana-register-foreign-form/' ),

		array( 'column' => '3', 'icon' => 'scan', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تطبیق و تشخیص چهره', 'subtitle' => 'دقت ۹۹٪ در تطابق با تصویر مرجع', 'url' => '/face-detection/' ),
		array( 'column' => '3', 'icon' => 'wave', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'تشخیص زنده‌بودن', 'subtitle' => 'جلوگیری از جعل با ویدئوی سلفی', 'url' => '/liveness-detection/' ),
		array( 'column' => '3', 'icon' => 'book', 'hot' => '', 'image_id' => 0, 'image_alt' => '', 'title' => 'مستندات فنی', 'subtitle' => 'راهنمای PWA و تمام APIها', 'url' => '/docs/' ),
	);
}

/**
 * آیکون‌های SVG هر آیتم مگامنو — فقط وقتی ادمین تصویر اختصاصی آپلود نکرده باشد
 */
function uid_mega_icon_svg( $key ) {
	$icons = array(
		'code'   => '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
		'crypto' => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>',
		'doc'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>',
		'medal'  => '<circle cx="12" cy="9" r="6"/><path d="M8.2 14.3L7 22l5-3 5 3-1.2-7.7"/>',
		'pin'    => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1116 0z"/><circle cx="12" cy="10" r="3"/>',
		'card'   => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
		'people' => '<path d="M16 20v-1.5a4 4 0 00-4-4H6a4 4 0 00-4 4V20"/><circle cx="9" cy="7" r="3.5"/><path d="M22 20v-1.5a4 4 0 00-3-3.9M16.5 3.6a4 4 0 010 7"/>',
		'scan'   => '<path d="M3 8V5a2 2 0 012-2h3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M21 16v3a2 2 0 01-2 2h-3"/><circle cx="12" cy="11" r="1"/><path d="M9 9v1M15 9v1M9.5 14.5a4 4 0 005 0"/>',
		'wave'   => '<path d="M12 2a8 8 0 018 8c0 2-.3 4-1 6"/><path d="M4 10a8 8 0 014-6.9"/><path d="M12 6a4 4 0 014 4c0 3-.5 6-1.5 8.5"/><path d="M8 10a4 4 0 011-2.6"/><path d="M12 10v3c0 2.5-.4 5-1.2 7"/><path d="M7 20c1-2 1.5-4.5 1.5-7v-2"/>',
		'book'   => '<path d="M4 4.5A2.5 2.5 0 016.5 2H20v18H6.5A2.5 2.5 0 004 22.5z"/><path d="M4 17.5A2.5 2.5 0 016.5 15H20"/>',
		'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
	);
	return $icons[ $key ] ?? $icons['shield'];
}

/**
 * دیفالت لینک‌های ستون‌های فوتر
 */
function uid_default_footer_links() {
	return array(
		array( 'column' => '1', 'text' => 'یوآیدی‌پلاس (PWA)', 'url' => '/uid-plus/' ),
		array( 'column' => '1', 'text' => 'وب‌سرویس احراز هویت تصویری', 'url' => '/api/' ),
		array( 'column' => '1', 'text' => 'وب سرویس ثبت احوال', 'url' => '/api-inquiry-person/' ),
		array( 'column' => '1', 'text' => 'احراز هویت سجام', 'url' => '/sejamauthentication/' ),
		array( 'column' => '1', 'text' => 'احراز هویت ثنا', 'url' => '/sana/' ),
		array( 'column' => '1', 'text' => 'احراز هویت صرافی ارز دیجیتال', 'url' => '/authentication-digital-currency-exchange/' ),

		array( 'column' => '2', 'text' => 'تطبیق و تشخیص چهره', 'url' => '/face-detection/' ),
		array( 'column' => '2', 'text' => 'تشخیص زنده‌بودن', 'url' => '/liveness-detection/' ),
		array( 'column' => '2', 'text' => 'مستندات PWA', 'url' => '/pwa-ekyc-docs/' ),
		array( 'column' => '2', 'text' => 'مستندات API', 'url' => '/api-ekyc-docs/' ),
		array( 'column' => '2', 'text' => 'سوالات متداول', 'url' => '/faq/' ),

		array( 'column' => '3', 'text' => 'همه محصولات', 'url' => '/products/' ),
		array( 'column' => '3', 'text' => 'وبلاگ', 'url' => 'https://blog.u-id.net/' ),
		array( 'column' => '3', 'text' => 'منابع', 'url' => '/resources/' ),
		array( 'column' => '3', 'text' => 'تماس با ما', 'url' => '/contact-us/' ),
	);
}

/**
 * نوار منوی اصلی هدر: دکمه مگامنو «خدمات» (با تصاویر اختصاصی هر آیتم) + لینک‌های ساده
 */
function uid_render_primary_nav() {
	$mega_label = uid_get_option( 'uid_header_options', 'mega_label', __( 'خدمات', 'uid-theme' ) );
	$items      = uid_get_option( 'uid_header_options', 'mega_items', uid_default_mega_items() );
	if ( ! is_array( $items ) ) $items = array();

	$cols = array( '1' => array(), '2' => array(), '3' => array() );
	foreach ( $items as $it ) {
		$c = isset( $it['column'] ) && isset( $cols[ $it['column'] ] ) ? $it['column'] : '1';
		$cols[ $c ][] = $it;
	}
	$col_titles = array(
		'1' => uid_get_option( 'uid_header_options', 'mega_col1_title', __( 'راهکار یکپارچه', 'uid-theme' ) ),
		'2' => uid_get_option( 'uid_header_options', 'mega_col2_title', __( 'سرویس‌های هویتی', 'uid-theme' ) ),
		'3' => uid_get_option( 'uid_header_options', 'mega_col3_title', __( 'فناوری‌ها', 'uid-theme' ) ),
	);

	$flag_title    = uid_get_option( 'uid_header_options', 'mega_flagship_title', __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ) );
	$flag_subtitle = uid_get_option( 'uid_header_options', 'mega_flagship_subtitle', __( 'نرخ تکمیل احراز هویت تا ۳٫۱ برابر بالاتر از روش API', 'uid-theme' ) );
	$flag_url      = uid_get_option( 'uid_header_options', 'mega_flagship_url', '/uid-plus/' );
	$flag_image_id = absint( uid_get_option( 'uid_header_options', 'mega_flagship_image_id', 0 ) );

	?>
	<div class="nav-item-wrap">
		<a href="#" class="mega-trigger" onclick="return false;">
			<?php echo esc_html( $mega_label ); ?>
			<svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
		</a>
		<div class="mega-menu">
			<?php foreach ( array( '1', '2', '3' ) as $c ) :
				if ( empty( $cols[ $c ] ) ) continue;
				?>
				<div class="mm-col">
					<div class="mm-col-head"><?php echo esc_html( $col_titles[ $c ] ); ?></div>
					<?php foreach ( $cols[ $c ] as $link ) :
						$img_id = absint( $link['image_id'] ?? 0 );
						$is_hot = ! empty( $link['hot'] );
						?>
						<a href="<?php echo esc_url( $link['url'] ?? '#' ); ?>" class="mm-link<?php echo $is_hot ? ' hot' : ''; ?>">
							<span class="mm-chip">
								<?php if ( $img_id ) :
									echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => $link['image_alt'] ?? ( $link['title'] ?? '' ) ) );
								else :
									echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . wp_kses( uid_mega_icon_svg( $link['icon'] ?? 'shield' ), array( 'path' => array( 'd' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ) ) ) . '</svg>';
								endif; ?>
							</span>
							<b><?php echo esc_html( $link['title'] ?? '' ); ?></b>
							<?php if ( ! empty( $link['subtitle'] ) ) : ?><span><?php echo esc_html( $link['subtitle'] ); ?></span><?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>

			<a href="<?php echo esc_url( $flag_url ); ?>" class="mm-flagship">
				<div class="mm-flagship-left">
					<div class="mm-flag-icon">
						<?php if ( $flag_image_id ) :
							echo wp_get_attachment_image( $flag_image_id, 'thumbnail', false, array( 'alt' => uid_get_option( 'uid_header_options', 'mega_flagship_image_alt', $flag_title ) ) );
						else :
							echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg>';
						endif; ?>
					</div>
					<div class="mm-flag-copy">
						<b><?php echo esc_html( $flag_title ); ?></b>
						<?php if ( $flag_subtitle ) : ?><span><?php echo esc_html( $flag_subtitle ); ?></span><?php endif; ?>
					</div>
				</div>
				<span class="mm-flagship-badge"><?php esc_html_e( 'محصول ویژه', 'uid-theme' ); ?></span>
			</a>
		</div>
	</div>
	<?php

	$nav_links = uid_get_option( 'uid_header_options', 'nav_links', uid_default_nav_links() );
	if ( ! is_array( $nav_links ) ) $nav_links = array();
	foreach ( $nav_links as $link ) {
		$url = $link['url'] ?? '#';
		$url = ( '' !== $url && '/' === $url[0] ) ? home_url( $url ) : $url;
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $link['text'] ?? '' ) . '</a>';
	}
}

/**
 * یک ستون از لینک‌های فوتر (شماره ستون: 1، 2 یا 3)
 */
function uid_render_footer_menu( $column ) {
	$links = uid_get_option( 'uid_footer_options', 'footer_links', uid_default_footer_links() );
	if ( ! is_array( $links ) ) $links = array();

	$column = (string) $column;
	echo '<ul>';
	$printed = false;
	foreach ( $links as $link ) {
		if ( ( $link['column'] ?? '1' ) !== $column ) continue;
		$printed = true;
		$url = $link['url'] ?? '#';
		$url = ( '' !== $url && '/' === $url[0] ) ? home_url( $url ) : $url;
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $link['text'] ?? '' ) . '</a></li>';
	}
	if ( ! $printed && current_user_can( 'edit_theme_options' ) ) {
		echo '<li><span class="placeholder-flag">' . esc_html__( 'لینکی برای این ستون تنظیم نشده', 'uid-theme' ) . '</span></li>';
	}
	echo '</ul>';
}
