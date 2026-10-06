<?php
/**
 * هدر و فوتر سراسری — همیشه روی همه‌ی صفحات (حتی صفحات تمام‌صفحه‌ی المنتور) رندر می‌شوند.
 *
 * هر کدام فقط یک بار در هر درخواست چاپ می‌شوند: از header.php/footer.php صدا زده می‌شوند
 * و هم از قلاب‌های wp_body_open/wp_footer، تا صفحاتی که get_header/get_footer صدا نمی‌زنند
 * هم هدر و فوتر را داشته باشند.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function uid_render_site_header() {
	static $done = false;
	if ( $done || ! uid_current_value( 'uid_header_options', 'global_enabled', true ) ) return;
	$done = true;
	get_template_part( 'template-parts/site-header' );
}

function uid_render_site_footer() {
	static $done = false;
	if ( $done || ! uid_current_value( 'uid_footer_options', 'global_enabled', true ) ) return;
	$done = true;
	get_template_part( 'template-parts/site-footer' );
}

add_action( 'wp_body_open', 'uid_render_site_header', 1 );
add_action( 'wp_footer', 'uid_render_site_footer', 1 );
