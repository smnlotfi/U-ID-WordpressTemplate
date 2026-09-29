<?php
/**
 * ذخیره‌سازی و نمایش فرم‌های ارسالی سایت (لید فرم‌ها)
 * جدول اختصاصی دیتابیس + اندپوینت AJAX ثبت + تب «درخواست‌های ارسالی» در تنظیمات قالب
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_FS_DB_VERSION', '1.0' );

function uid_fs_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'uid_form_submissions';
}

/**
 * ساخت جدول دیتابیس (در فعال‌سازی قالب + به‌صورت خودکار اگر قبلاً ساخته نشده باشد)
 */
function uid_fs_install() {
	global $wpdb;
	$table           = uid_fs_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		created_at DATETIME NOT NULL,
		page_title VARCHAR(255) NOT NULL DEFAULT '',
		page_url VARCHAR(255) NOT NULL DEFAULT '',
		form_id VARCHAR(64) NOT NULL DEFAULT '',
		name VARCHAR(191) NOT NULL DEFAULT '',
		phone VARCHAR(64) NOT NULL DEFAULT '',
		fields LONGTEXT NOT NULL,
		ip VARCHAR(45) NOT NULL DEFAULT '',
		user_agent VARCHAR(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (id),
		KEY created_at (created_at)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'uid_fs_db_version', UID_FS_DB_VERSION );
}
add_action( 'after_switch_theme', 'uid_fs_install' );

function uid_fs_maybe_install() {
	if ( get_option( 'uid_fs_db_version' ) !== UID_FS_DB_VERSION ) {
		uid_fs_install();
	}
}
add_action( 'admin_init', 'uid_fs_maybe_install' );

/**
 * گرفتن IP کاربر (با در نظر گرفتن پراکسی/CDN احتمالی)
 */
function uid_fs_client_ip() {
	foreach ( array( 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) );
			$ip    = trim( $parts[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '';
}

/**
 * اندپوینت AJAX ثبت فرم — برای کاربران لاگین و مهمان هر دو فعال است
 */
function uid_fs_handle_submit() {
	check_ajax_referer( 'uid_form_submit', 'nonce' );

	$ip      = uid_fs_client_ip();
	$rl_key  = 'uid_fs_rl_' . md5( $ip );
	$count   = (int) get_transient( $rl_key );
	if ( $count >= 12 ) {
		wp_send_json_error( array( 'message' => __( 'تعداد درخواست‌ها زیاد است، کمی بعد دوباره تلاش کنید.', 'uid-theme' ) ) );
	}
	set_transient( $rl_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$form_id    = isset( $_POST['form_id'] ) ? sanitize_key( wp_unslash( $_POST['form_id'] ) ) : '';
	$page_title = isset( $_POST['page_title'] ) ? sanitize_text_field( wp_unslash( $_POST['page_title'] ) ) : '';
	$page_url   = isset( $_POST['page_url'] ) ? sanitize_text_field( wp_unslash( $_POST['page_url'] ) ) : '';

	$fields = array();
	if ( isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ) {
		foreach ( $_POST['fields'] as $k => $v ) {
			$key = sanitize_key( $k );
			if ( '' === $key ) {
				continue;
			}
			$fields[ $key ] = sanitize_text_field( wp_unslash( $v ) );
		}
	}

	if ( empty( $fields ) ) {
		wp_send_json_error( array( 'message' => __( 'فرم خالی است.', 'uid-theme' ) ) );
	}

	global $wpdb;
	$wpdb->insert(
		uid_fs_table_name(),
		array(
			'created_at' => current_time( 'mysql' ),
			'page_title' => $page_title,
			'page_url'   => $page_url,
			'form_id'    => $form_id,
			'name'       => isset( $fields['name'] ) ? $fields['name'] : '',
			'phone'      => isset( $fields['phone'] ) ? $fields['phone'] : '',
			'fields'     => wp_json_encode( $fields, JSON_UNESCAPED_UNICODE ),
			'ip'         => $ip,
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	$notify_email = get_option( 'admin_email' );
	if ( $notify_email ) {
		$subject = sprintf(
			/* translators: %s: page title */
			__( 'درخواست جدید از %s', 'uid-theme' ),
			$page_title ? $page_title : get_bloginfo( 'name' )
		);
		$lines = array();
		foreach ( $fields as $k => $v ) {
			$lines[] = $k . ': ' . $v;
		}
		$body = implode( "\n", $lines ) . "\n\n" . $page_url;
		wp_mail( $notify_email, $subject, $body );
	}

	wp_send_json_success( array( 'message' => __( 'درخواست شما ثبت شد.', 'uid-theme' ) ) );
}
add_action( 'wp_ajax_uid_submit_form', 'uid_fs_handle_submit' );
add_action( 'wp_ajax_nopriv_uid_submit_form', 'uid_fs_handle_submit' );

/**
 * خروجی CSV همه ارسالی‌ها
 */
function uid_fs_export_csv() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'دسترسی مجاز نیست.', 'uid-theme' ) );
	}
	global $wpdb;
	$rows = $wpdb->get_results( 'SELECT * FROM ' . uid_fs_table_name() . ' ORDER BY id DESC' );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=uid-form-submissions-' . gmdate( 'Y-m-d' ) . '.csv' );

	echo "\xEF\xBB\xBF"; // BOM برای نمایش صحیح فارسی در اکسل
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'id', 'created_at', 'page_title', 'page_url', 'form_id', 'name', 'phone', 'fields', 'ip', 'user_agent' ) );
	foreach ( $rows as $r ) {
		fputcsv( $out, array( $r->id, $r->created_at, $r->page_title, $r->page_url, $r->form_id, $r->name, $r->phone, $r->fields, $r->ip, $r->user_agent ) );
	}
	fclose( $out );
	exit;
}

/**
 * محتوای تب «درخواست‌های ارسالی» در پیشخوان > تنظیمات قالب یوآیدی
 */
function uid_render_form_submissions_tab() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	global $wpdb;
	$table = uid_fs_table_name();

	if ( isset( $_GET['uid_fs_export'] ) && check_admin_referer( 'uid_fs_export' ) ) {
		uid_fs_export_csv();
	}

	if ( isset( $_POST['uid_fs_action'] ) && 'delete' === $_POST['uid_fs_action'] && check_admin_referer( 'uid_fs_delete' ) ) {
		$id = isset( $_POST['uid_fs_id'] ) ? absint( $_POST['uid_fs_id'] ) : 0;
		if ( $id ) {
			$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'حذف شد.', 'uid-theme' ) . '</p></div>';
		}
	}

	$per_page = 20;
	$paged    = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	$where  = '1=1';
	$params = array();
	if ( '' !== $search ) {
		$like   = '%' . $wpdb->esc_like( $search ) . '%';
		$where .= ' AND (page_title LIKE %s OR name LIKE %s OR phone LIKE %s OR fields LIKE %s)';
		$params = array( $like, $like, $like, $like );
	}

	$total_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
	$total     = (int) ( $params ? $wpdb->get_var( $wpdb->prepare( $total_sql, $params ) ) : $wpdb->get_var( $total_sql ) );

	$offset      = ( $paged - 1 ) * $per_page;
	$rows_sql    = "SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
	$rows_params = array_merge( $params, array( $per_page, $offset ) );
	$rows        = $wpdb->get_results( $wpdb->prepare( $rows_sql, $rows_params ) );

	$total_pages = (int) ceil( $total / $per_page );
	?>
	<h2><?php esc_html_e( 'درخواست‌های ارسالی از فرم‌های سایت', 'uid-theme' ); ?></h2>
	<p><?php esc_html_e( 'هر بار که یکی از فرم‌های موجود در صفحات سرویس‌ها (یا فرم درخواست تماس) با موفقیت ارسال شود، در این لیست ذخیره می‌شود.', 'uid-theme' ); ?></p>

	<form method="get" style="margin:14px 0;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
		<input type="hidden" name="page" value="uid-theme-settings">
		<input type="hidden" name="tab" value="submissions">
		<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'جست‌وجو در نام، موبایل، صفحه…', 'uid-theme' ); ?>" style="min-width:260px">
		<?php submit_button( __( 'جست‌وجو', 'uid-theme' ), 'secondary', '', false ); ?>
		<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'uid-theme-settings', 'tab' => 'submissions', 'uid_fs_export' => 1 ), admin_url( 'admin.php' ) ), 'uid_fs_export' ) ); ?>">
			<?php esc_html_e( 'خروجی CSV', 'uid-theme' ); ?>
		</a>
		<span style="color:#777"><?php echo esc_html( sprintf( /* translators: %d: total count */ __( '%d مورد', 'uid-theme' ), $total ) ); ?></span>
	</form>

	<table class="widefat striped">
		<thead>
			<tr>
				<th style="width:110px"><?php esc_html_e( 'تاریخ', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'صفحه', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'فرم', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'نام', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'موبایل', 'uid-theme' ); ?></th>
				<th><?php esc_html_e( 'سایر فیلدها', 'uid-theme' ); ?></th>
				<th style="width:110px">IP</th>
				<th style="width:60px"></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( ! $rows ) : ?>
			<tr><td colspan="8"><?php esc_html_e( 'هنوز هیچ درخواستی ثبت نشده است.', 'uid-theme' ); ?></td></tr>
		<?php else : ?>
			<?php foreach ( $rows as $row ) :
				$fields = json_decode( $row->fields, true );
				$extra  = array();
				if ( is_array( $fields ) ) {
					foreach ( $fields as $k => $v ) {
						if ( in_array( $k, array( 'name', 'phone' ), true ) || '' === $v ) {
							continue;
						}
						$extra[] = $k . ': ' . $v;
					}
				}
				$path = wp_parse_url( $row->page_url, PHP_URL_PATH );
				?>
				<tr>
					<td><?php echo esc_html( mysql2date( 'Y/m/d H:i', $row->created_at ) ); ?></td>
					<td>
						<?php echo esc_html( $row->page_title ); ?>
						<?php if ( $row->page_url ) : ?>
							<br><a href="<?php echo esc_url( $row->page_url ); ?>" target="_blank" rel="noopener" style="font-size:11px"><?php echo esc_html( $path ? $path : $row->page_url ); ?></a>
						<?php endif; ?>
					</td>
					<td><code><?php echo esc_html( $row->form_id ); ?></code></td>
					<td><?php echo esc_html( $row->name ); ?></td>
					<td><?php echo esc_html( $row->phone ? uid_fa_digits( $row->phone ) : '' ); ?></td>
					<td style="font-size:12px"><?php echo esc_html( implode( ' · ', $extra ) ); ?></td>
					<td style="font-size:11px;color:#888"><?php echo esc_html( $row->ip ); ?></td>
					<td>
						<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'این درخواست حذف شود؟', 'uid-theme' ) ); ?>');">
							<?php wp_nonce_field( 'uid_fs_delete' ); ?>
							<input type="hidden" name="uid_fs_action" value="delete">
							<input type="hidden" name="uid_fs_id" value="<?php echo (int) $row->id; ?>">
							<button type="submit" class="button-link-delete" style="color:#b32d2e;background:none;border:none;cursor:pointer"><?php esc_html_e( 'حذف', 'uid-theme' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $total_pages > 1 ) : ?>
		<div class="tablenav" style="margin-top:12px"><div class="tablenav-pages">
			<?php
			echo paginate_links( array(
				'base'    => add_query_arg( 'paged', '%#%' ),
				'format'  => '',
				'current' => $paged,
				'total'   => $total_pages,
			) );
			?>
		</div></div>
	<?php endif; ?>
	<?php
}
