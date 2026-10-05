<?php
/**
 * مدیریت دستی برگه‌های اختصاصی قالب — پیشخوان ← تنظیمات قالب ← مدیریت برگه‌ها
 *
 * ساخت این برگه‌ها دیگر خودکار (روی فعال‌سازی قالب یا admin_init) انجام نمی‌شود؛
 * فقط با کلیک دستی از همین تب. قبل از ساخت، وجود برگه چک می‌شود؛ اگر از قبل
 * ساخته شده باشد، به‌جای ساخت نسخه‌ی تکراری، پیام می‌دهد که باید اول حذفش کنید.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function uid_theme_pages_registry() {
	return array(
		'homepage' => array( 'label' => __( 'صفحه اصلی', 'uid-theme' ), 'ensure' => 'uid_ensure_homepage_page', 'get_id' => 'uid_get_homepage_page_id', 'id_option' => 'uid_homepage_page_id' ),
		'pwa'      => array( 'label' => __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ), 'ensure' => 'uid_ensure_pwa_page', 'get_id' => 'uid_get_pwa_page_id', 'id_option' => 'uid_pwa_page_id' ),
		'cr'       => array( 'label' => __( 'وب سرویس ثبت احوال', 'uid-theme' ), 'ensure' => 'uid_ensure_cr_page', 'get_id' => 'uid_get_cr_page_id', 'id_option' => 'uid_cr_page_id' ),
		'ek'       => array( 'label' => __( 'وب‌سرویس احراز هویت تصویری', 'uid-theme' ), 'ensure' => 'uid_ensure_ek_page', 'get_id' => 'uid_get_ek_page_id', 'id_option' => 'uid_ek_page_id' ),
		'sn'       => array( 'label' => __( 'احراز هویت ثنا', 'uid-theme' ), 'ensure' => 'uid_ensure_sn_page', 'get_id' => 'uid_get_sn_page_id', 'id_option' => 'uid_sn_page_id' ),
		'sk'       => array( 'label' => __( 'وب‌سرویس شاهکار', 'uid-theme' ), 'ensure' => 'uid_ensure_sk_page', 'get_id' => 'uid_get_sk_page_id', 'id_option' => 'uid_sk_page_id' ),
		'pa'       => array( 'label' => __( 'استعلام کد پستی و آدرس', 'uid-theme' ), 'ensure' => 'uid_ensure_pa_page', 'get_id' => 'uid_get_pa_page_id', 'id_option' => 'uid_pa_page_id' ),
		'ib'       => array( 'label' => __( 'استعلام اطلاعات مالی (شبا)', 'uid-theme' ), 'ensure' => 'uid_ensure_ib_page', 'get_id' => 'uid_get_ib_page_id', 'id_option' => 'uid_ib_page_id' ),
		'ci'       => array( 'label' => __( 'تبدیل شماره کارت به شبا', 'uid-theme' ), 'ensure' => 'uid_ensure_ci_page', 'get_id' => 'uid_get_ci_page_id', 'id_option' => 'uid_ci_page_id' ),
		'iv'       => array( 'label' => __( 'تطبیق شماره شبا با کد ملی', 'uid-theme' ), 'ensure' => 'uid_ensure_iv_page', 'get_id' => 'uid_get_iv_page_id', 'id_option' => 'uid_iv_page_id' ),
		'cv'       => array( 'label' => __( 'تطبیق شماره کارت با کد ملی', 'uid-theme' ), 'ensure' => 'uid_ensure_cv_page', 'get_id' => 'uid_get_cv_page_id', 'id_option' => 'uid_cv_page_id' ),
		'sa'       => array( 'label' => __( 'ثبت‌نام سامانه ثنا ویژه ایرانیان خارج از کشور', 'uid-theme' ), 'ensure' => 'uid_ensure_sa_page', 'get_id' => 'uid_get_sa_page_id', 'id_option' => 'uid_sa_page_id' ),
		'wsh'      => array( 'label' => __( 'وب‌سرویس‌های احراز هویت', 'uid-theme' ), 'ensure' => 'uid_ensure_wsh_page', 'get_id' => 'uid_get_wsh_page_id', 'id_option' => 'uid_wsh_page_id' ),
		'gx'       => array( 'label' => __( 'واژه‌نامه اصطلاحات احراز هویت', 'uid-theme' ), 'ensure' => 'uid_ensure_gx_page', 'get_id' => 'uid_get_gx_page_id', 'id_option' => 'uid_gx_page_id' ),
		'ab'       => array( 'label' => __( 'درباره ما', 'uid-theme' ), 'ensure' => 'uid_ensure_ab_page', 'get_id' => 'uid_get_ab_page_id', 'id_option' => 'uid_ab_page_id' ),
		'cu'       => array( 'label' => __( 'تماس با ما', 'uid-theme' ), 'ensure' => 'uid_ensure_cu_page', 'get_id' => 'uid_get_cu_page_id', 'id_option' => 'uid_cu_page_id' ),
		'op'       => array( 'label' => __( 'OCR چیست؟', 'uid-theme' ), 'ensure' => 'uid_ensure_op_page', 'get_id' => 'uid_get_op_page_id', 'id_option' => 'uid_op_page_id' ),
	);
}

/**
 * پردازش کلیک «ایجاد برگه» / «حذف برگه»
 */
function uid_handle_theme_page_action() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'دسترسی لازم را ندارید.', 'uid-theme' ) );
	}

	$key = isset( $_POST['uid_page_key'] ) ? sanitize_key( wp_unslash( $_POST['uid_page_key'] ) ) : '';
	check_admin_referer( 'uid_theme_page_action_' . $key );

	$registry = uid_theme_pages_registry();
	$action   = isset( $_POST['uid_page_action'] ) ? sanitize_key( wp_unslash( $_POST['uid_page_action'] ) ) : '';

	$notice = 'invalid';

	if ( isset( $registry[ $key ] ) && in_array( $action, array( 'create', 'delete' ), true ) ) {
		$entry       = $registry[ $key ];
		$existing_id = call_user_func( $entry['get_id'] );

		if ( 'create' === $action ) {
			if ( $existing_id ) {
				$notice = 'exists';
			} else {
				call_user_func( $entry['ensure'] );
				$notice = call_user_func( $entry['get_id'] ) ? 'created' : 'failed';
			}
		} elseif ( 'delete' === $action ) {
			if ( $existing_id ) {
				wp_delete_post( $existing_id, true );
				delete_option( $entry['id_option'] );
				$notice = 'deleted';
			} else {
				$notice = 'not_found';
			}
		}
	}

	wp_safe_redirect( add_query_arg( array(
		'page'       => 'uid-theme-settings',
		'tab'        => 'pages',
		'uid_notice' => $notice,
		'uid_key'    => $key,
	), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_uid_theme_page_action', 'uid_handle_theme_page_action' );

/**
 * رندر تب «مدیریت برگه‌ها»
 */
function uid_render_page_manager_tab() {
	$registry = uid_theme_pages_registry();

	$notice = isset( $_GET['uid_notice'] ) ? sanitize_key( wp_unslash( $_GET['uid_notice'] ) ) : '';
	$key    = isset( $_GET['uid_key'] ) ? sanitize_key( wp_unslash( $_GET['uid_key'] ) ) : '';
	$label  = isset( $registry[ $key ] ) ? $registry[ $key ]['label'] : '';

	if ( $notice && $label ) {
		$messages = array(
			'created'   => array( 'success', sprintf( __( 'برگه «%s» ساخته شد.', 'uid-theme' ), $label ) ),
			'deleted'   => array( 'success', sprintf( __( 'برگه «%s» حذف شد.', 'uid-theme' ), $label ) ),
			'exists'    => array( 'error', sprintf( __( 'برگه «%s» از قبل وجود دارد — برای ساخت دوباره، اول باید همین برگه را حذف کنید.', 'uid-theme' ), $label ) ),
			'not_found' => array( 'error', sprintf( __( 'برگه «%s» از قبل وجود نداشت؛ چیزی برای حذف نبود.', 'uid-theme' ), $label ) ),
			'failed'    => array( 'error', sprintf( __( 'ساخت برگه «%s» ناموفق بود.', 'uid-theme' ), $label ) ),
		);
		if ( isset( $messages[ $notice ] ) ) {
			list( $type, $text ) = $messages[ $notice ];
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $type ), esc_html( $text ) );
		}
	}
	?>
	<p class="description">
		<?php esc_html_e( 'ساخت و حذف هر برگه فقط با کلیک دستی شما انجام می‌شود — قالب دیگر خودکار (نه روی فعال‌سازی، نه در پس‌زمینه) برگه نمی‌سازد. اگر برگه‌ای از قبل وجود داشته باشد، دکمه «ایجاد» آن را دوباره نمی‌سازد و باید اول حذفش کنید.', 'uid-theme' ); ?>
	</p>
	<table class="widefat striped" style="max-width:900px;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'برگه', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'وضعیت', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'عملیات', 'uid-theme' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $registry as $row_key => $entry ) :
				$page_id = call_user_func( $entry['get_id'] );
				?>
				<tr>
					<td><strong><?php echo esc_html( $entry['label'] ); ?></strong></td>
					<td>
						<?php if ( $page_id ) : ?>
							<span style="color:#1a7e3c;">●</span> <?php esc_html_e( 'موجود است', 'uid-theme' ); ?>
							— <a href="<?php echo esc_url( get_edit_post_link( $page_id, '' ) ); ?>"><?php esc_html_e( 'ویرایش', 'uid-theme' ); ?></a>
							· <a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>" target="_blank"><?php esc_html_e( 'نمایش', 'uid-theme' ); ?></a>
						<?php else : ?>
							<span style="color:#9a9a9a;">●</span> <?php esc_html_e( 'ساخته نشده', 'uid-theme' ); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( $page_id ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;" onsubmit="return confirm('<?php echo esc_js( sprintf( __( 'برگه «%s» برای همیشه حذف شود؟', 'uid-theme' ), $entry['label'] ) ); ?>');">
								<?php wp_nonce_field( 'uid_theme_page_action_' . $row_key ); ?>
								<input type="hidden" name="action" value="uid_theme_page_action">
								<input type="hidden" name="uid_page_key" value="<?php echo esc_attr( $row_key ); ?>">
								<input type="hidden" name="uid_page_action" value="delete">
								<button type="submit" class="button button-link-delete"><?php esc_html_e( 'حذف برگه', 'uid-theme' ); ?></button>
							</form>
						<?php else : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
								<?php wp_nonce_field( 'uid_theme_page_action_' . $row_key ); ?>
								<input type="hidden" name="action" value="uid_theme_page_action">
								<input type="hidden" name="uid_page_key" value="<?php echo esc_attr( $row_key ); ?>">
								<input type="hidden" name="uid_page_action" value="create">
								<button type="submit" class="button button-primary"><?php esc_html_e( 'ایجاد برگه', 'uid-theme' ); ?></button>
							</form>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}
