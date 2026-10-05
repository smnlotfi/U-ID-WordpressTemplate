<?php
/**
 * ساخت خودکار «برگه صفحه اصلی» و مدیریت اسلاگ آن از تنظیمات قالب.
 *
 * با فعال‌سازی قالب (یا حتی اگر از قبل فعال بوده و این فایل تازه اضافه شده)،
 * یک برگه واقعی با قالب template-homepage.php ساخته و به‌عنوان صفحه اصلی
 * سایت تنظیم می‌شود؛ اسلاگ آن هم از پیشخوان > تنظیمات قالب > صفحه اصلی
 * قابل‌ویرایش است (دقیقاً همان برگه در پیشخوان > برگه‌ها نیز ویرایش‌پذیر است).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_HOMEPAGE_TEMPLATE', 'template-homepage.php' );

/**
 * شناسه‌ی برگه صفحه اصلی را برمی‌گرداند (۰ یعنی هنوز ساخته نشده)
 */
function uid_get_homepage_page_id() {
	$page_id = (int) get_option( 'uid_homepage_page_id' );
	if ( $page_id && get_post( $page_id ) ) return $page_id;

	// سازگاری با حالتی که برگه از قبل با این قالب ساخته شده ولی آپشن گم شده
	$found = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => 'any',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => UID_HOMEPAGE_TEMPLATE,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $found ) {
		update_option( 'uid_homepage_page_id', $found[0] );
		return (int) $found[0];
	}
	return 0;
}

/**
 * در صورت نبودِ برگه صفحه اصلی، آن را می‌سازد، قالب را رویش تنظیم می‌کند
 * و (فقط اگر صفحه اصلی سایت قبلاً پیکربندی نشده) آن را صفحه اصلی سایت می‌کند.
 */
function uid_ensure_homepage_page() {
	if ( uid_get_homepage_page_id() ) return;

	$page_id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => __( 'صفحه اصلی', 'uid-theme' ),
		'post_name'   => 'home',
	), true );

	if ( is_wp_error( $page_id ) || ! $page_id ) return;

	update_post_meta( $page_id, '_wp_page_template', UID_HOMEPAGE_TEMPLATE );
	update_option( 'uid_homepage_page_id', $page_id );

	// فقط اگر صفحه اصلی سایت هنوز روی هیچ برگه‌ی معتبری تنظیم نشده، خودکار همین برگه را انتخاب کن
	$current_front_id = (int) get_option( 'page_on_front' );
	if ( 'page' !== get_option( 'show_on_front' ) || ! $current_front_id || ! get_post( $current_front_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );
	}
}
// ساخت/حذف این برگه فقط دستی از پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها انجام می‌شود (نه خودکار)

/* ===================== فیلد اسلاگ در تنظیمات قالب ===================== */

function uid_register_homepage_slug_setting() {
	register_setting( 'uid_home_group', 'uid_homepage_slug', array(
		'sanitize_callback' => 'uid_sanitize_homepage_slug',
		'default'           => '',
	) );
	add_settings_section( 'uid_homepage_slug_section', '', '__return_false', 'uid_home_layout' );
	add_settings_field( 'uid_homepage_slug', __( 'آدرس (اسلاگ) صفحه اصلی', 'uid-theme' ), 'uid_field_homepage_slug', 'uid_home_layout', 'uid_homepage_slug_section', array() );
}
add_action( 'admin_init', 'uid_register_homepage_slug_setting' );

function uid_field_homepage_slug( $args ) {
	$page_id = uid_get_homepage_page_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : get_option( 'uid_homepage_slug', '' );
	?>
	<input type="text" class="regular-text" dir="ltr" name="uid_homepage_slug" value="<?php echo esc_attr( $slug ); ?>">
	<?php if ( $page_id ) : ?>
		<p class="description">
			<?php
			printf(
				/* translators: %s: current homepage URL, %s: edit link */
				esc_html__( 'آدرس فعلی: %1$s — این برگه را می‌توانید مستقیماً از پیشخوان ← برگه‌ها هم ویرایش کنید (%2$s).', 'uid-theme' ),
				'<code dir="ltr">' . esc_html( trailingslashit( home_url( '/' . $slug ) ) ) . '</code>',
				'<a href="' . esc_url( get_edit_post_link( $page_id, '' ) ) . '">' . esc_html__( 'ویرایش برگه', 'uid-theme' ) . '</a>'
			);
			?>
		</p>
	<?php else : ?>
		<p class="description" style="color:#b32d2e;"><?php esc_html_e( 'برگه صفحه اصلی هنوز ساخته نشده. صفحه را دوباره بارگذاری کنید؛ اگر باز هم ساخته نشد، یک‌بار قالب را غیرفعال و دوباره فعال کنید.', 'uid-theme' ); ?></p>
	<?php endif;
}

/**
 * ذخیره اسلاگ جدید روی برگه واقعی صفحه اصلی و بازگرداندن اسلاگ نهایی
 * (وردپرس در صورت تکراری‌بودن، خودش آن را یکتا می‌کند)
 */
function uid_sanitize_homepage_slug( $input ) {
	$slug    = sanitize_title( is_string( $input ) ? $input : '' );
	$page_id = uid_get_homepage_page_id();

	if ( $page_id && $slug ) {
		$post = get_post( $page_id );
		if ( $post && $post->post_name !== $slug ) {
			wp_update_post( array( 'ID' => $page_id, 'post_name' => $slug ) );
			flush_rewrite_rules( false );
		}
	}

	return $page_id ? get_post_field( 'post_name', $page_id ) : $slug;
}
