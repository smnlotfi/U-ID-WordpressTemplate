<?php
/**
 * صفحه تنظیمات مستقل قالب — جدا از «سفارشی‌سازی» وردپرس
 * پیشخوان > تنظیمات قالب یوآیدی
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * افزودن آیتم منو در پیشخوان
 */
function uid_add_settings_menu() {
	add_menu_page(
		__( 'تنظیمات قالب یوآیدی', 'uid-theme' ),
		__( 'تنظیمات قالب', 'uid-theme' ),
		'edit_theme_options',
		'uid-theme-settings',
		'uid_render_settings_page',
		'dashicons-admin-customizer',
		61
	);
}
add_action( 'admin_menu', 'uid_add_settings_menu' );

/**
 * ثبت تنظیمات (Settings API) — سه گروه: تماس / هدر / فوتر
 */
function uid_register_settings() {

	/* ---------------- تماس ---------------- */
	register_setting( 'uid_contact_group', 'uid_contact_options', array(
		'sanitize_callback' => 'uid_sanitize_contact_options',
		'default'           => array(),
	) );

	add_settings_section( 'uid_contact_main', '', '__return_false', 'uid_contact_options' );

	add_settings_field( 'phone', __( 'شماره تلفن (فقط ارقام انگلیسی، بدون فاصله)', 'uid-theme' ), 'uid_field_text', 'uid_contact_options', 'uid_contact_main', array( 'group' => 'uid_contact_options', 'key' => 'phone', 'default' => '02166123290', 'desc' => __( 'در هدر، فوتر، دکمه‌های شناور و پنجره گفتگو استفاده می‌شود.', 'uid-theme' ) ) );
	add_settings_field( 'whatsapp', __( 'لینک واتساپ', 'uid-theme' ), 'uid_field_text', 'uid_contact_options', 'uid_contact_main', array( 'group' => 'uid_contact_options', 'key' => 'whatsapp', 'default' => 'https://wa.me/989120000000' ) );
	add_settings_field( 'telegram', __( 'لینک تلگرام', 'uid-theme' ), 'uid_field_text', 'uid_contact_options', 'uid_contact_main', array( 'group' => 'uid_contact_options', 'key' => 'telegram', 'default' => 'https://t.me/uid_support' ) );

	/* ---------------- هدر ---------------- */
	register_setting( 'uid_header_group', 'uid_header_options', array(
		'sanitize_callback' => 'uid_sanitize_header_options',
		'default'           => array(),
	) );

	add_settings_section( 'uid_header_main', '', '__return_false', 'uid_header_options' );

	add_settings_field( 'logo_id', __( 'لوگو', 'uid-theme' ), 'uid_field_logo', 'uid_header_options', 'uid_header_main', array( 'group' => 'uid_header_options', 'key' => 'logo_id', 'desc' => __( 'در صورت خالی بودن، آرم پیش‌فرض SVG سایت نمایش داده می‌شود.', 'uid-theme' ) ) );
	add_settings_field( 'show_phone', __( 'دکمه تماس تلفنی', 'uid-theme' ), 'uid_field_checkbox', 'uid_header_options', 'uid_header_main', array( 'group' => 'uid_header_options', 'key' => 'show_phone', 'default' => 1, 'label' => __( 'نمایش داده شود (از شماره تنظیم‌شده در تب «اطلاعات تماس» استفاده می‌کند)', 'uid-theme' ) ) );
	add_settings_field( 'global_enabled', __( 'نمایش سراسری', 'uid-theme' ), 'uid_field_checkbox', 'uid_header_options', 'uid_header_main', array( 'group' => 'uid_header_options', 'key' => 'global_enabled', 'default' => 1, 'label' => __( 'هدر این قالب روی همه‌ی صفحات نمایش داده شود (برای برداشتن این اولویت، تیک را بردارید و ذخیره کنید)', 'uid-theme' ) ) );


	/* ---------------- لینک‌های ساده منوی هدر ---------------- */
	add_settings_section( 'uid_header_nav', __( 'لینک‌های ساده منو (کنار دکمه مگامنو)', 'uid-theme' ), '__return_false', 'uid_header_options' );
	add_settings_field( 'nav_links', __( 'لینک‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_header_options', 'uid_header_nav', array(
		'group' => 'uid_header_options', 'key' => 'nav_links', 'default' => uid_default_nav_links(), 'add_label' => __( 'افزودن لینک', 'uid-theme' ),
		'desc'  => __( 'این لینک‌ها بعد از دکمه مگامنو «خدمات» نمایش داده می‌شوند. برای لینک داخلی سایت، فقط مسیر را با / وارد کنید (مثل ‎/docs/‏)؛ برای لینک خارجی، آدرس کامل بنویسید.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'text', 'type' => 'text', 'label' => __( 'متن', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مگامنو (دکمه «خدمات») ---------------- */
	add_settings_section( 'uid_header_mega', __( 'مگامنو (دکمه «خدمات» در هدر)', 'uid-theme' ), '__return_false', 'uid_header_options' );
	add_settings_field( 'mega_label', __( 'متن دکمه مگامنو', 'uid-theme' ), 'uid_field_text', 'uid_header_options', 'uid_header_mega', array( 'group' => 'uid_header_options', 'key' => 'mega_label', 'default' => __( 'خدمات', 'uid-theme' ) ) );
	add_settings_field( 'mega_col1_title', __( 'عنوان ستون اول (کارت‌های ویژه)', 'uid-theme' ), 'uid_field_text', 'uid_header_options', 'uid_header_mega', array( 'group' => 'uid_header_options', 'key' => 'mega_col1_title', 'default' => __( 'راهکارهای یکپارچه', 'uid-theme' ) ) );
	add_settings_field( 'mega_col2_title', __( 'عنوان ستون دوم', 'uid-theme' ), 'uid_field_text', 'uid_header_options', 'uid_header_mega', array( 'group' => 'uid_header_options', 'key' => 'mega_col2_title', 'default' => __( 'احراز هویت و هویت فردی', 'uid-theme' ) ) );
	add_settings_field( 'mega_col3_title', __( 'عنوان ستون سوم', 'uid-theme' ), 'uid_field_text', 'uid_header_options', 'uid_header_mega', array( 'group' => 'uid_header_options', 'key' => 'mega_col3_title', 'default' => __( 'استعلام و تطبیق مالی', 'uid-theme' ) ) );
	add_settings_field( 'mega_col4_title', __( 'عنوان ستون چهارم', 'uid-theme' ), 'uid_field_text', 'uid_header_options', 'uid_header_mega', array( 'group' => 'uid_header_options', 'key' => 'mega_col4_title', 'default' => __( 'فناوری‌ها و منابع', 'uid-theme' ) ) );
	add_settings_field( 'mega_items', __( 'لینک‌های داخل ستون‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_header_options', 'uid_header_mega', array(
		'group' => 'uid_header_options', 'key' => 'mega_items', 'default' => uid_default_mega_items(), 'add_label' => __( 'افزودن لینک مگامنو', 'uid-theme' ),
		'desc'  => __( 'هر لینک می‌تواند تصویر اختصاصی از کتابخانه رسانه داشته باشد؛ در نبود تصویر، آیکون انتخابی نمایش داده می‌شود. آیتم‌های «ستون ۱» به‌صورت کارت بزرگ با زیرنویس نمایش داده می‌شوند؛ ستون‌های ۲ تا ۴ ردیف فشرده‌اند.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'column', 'type' => 'select', 'label' => __( 'ستون', 'uid-theme' ), 'options' => array( '1' => __( 'ستون ۱ (کارت ویژه)', 'uid-theme' ), '2' => __( 'ستون ۲', 'uid-theme' ), '3' => __( 'ستون ۳', 'uid-theme' ), '4' => __( 'ستون ۴', 'uid-theme' ) ) ),
			array( 'key' => 'icon', 'type' => 'select', 'label' => __( 'آیکون پیش‌فرض (در نبود تصویر)', 'uid-theme' ), 'options' => array( 'code' => __( 'کد/API', 'uid-theme' ), 'crypto' => __( 'ارز دیجیتال', 'uid-theme' ), 'doc' => __( 'سند', 'uid-theme' ), 'medal' => __( 'نشان', 'uid-theme' ), 'pin' => __( 'موقعیت', 'uid-theme' ), 'card' => __( 'کارت بانکی', 'uid-theme' ), 'card-convert' => __( 'تبدیل کارت', 'uid-theme' ), 'card-check' => __( 'تطبیق کارت', 'uid-theme' ), 'lock' => __( 'تطبیق/قفل', 'uid-theme' ), 'people' => __( 'افراد', 'uid-theme' ), 'scan' => __( 'اسکن چهره', 'uid-theme' ), 'wave' => __( 'امواج/زنده‌بودن', 'uid-theme' ), 'book' => __( 'مستندات', 'uid-theme' ), 'glossary' => __( 'واژه‌نامه', 'uid-theme' ), 'simcard' => __( 'سیم‌کارت/شاهکار', 'uid-theme' ), 'shield' => __( 'امنیت', 'uid-theme' ) ) ),
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'تصویر اختصاصی (اختیاری)', 'uid-theme' ), 'pick_label' => __( 'انتخاب تصویر', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'subtitle', 'type' => 'text', 'label' => __( 'زیرنویس (فقط برای ستون ۱ نمایش داده می‌شود)', 'uid-theme' ) ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فوتر ---------------- */
	register_setting( 'uid_footer_group', 'uid_footer_options', array(
		'sanitize_callback' => 'uid_sanitize_footer_options',
		'default'           => array(),
	) );

	add_settings_section( 'uid_footer_brand', __( 'معرفی و شبکه‌های اجتماعی', 'uid-theme' ), '__return_false', 'uid_footer_options' );
	add_settings_field( 'description', __( 'توضیح کوتاه زیر لوگو', 'uid-theme' ), 'uid_field_textarea', 'uid_footer_options', 'uid_footer_brand', array( 'group' => 'uid_footer_options', 'key' => 'description', 'default' => __( 'زیرساخت احراز هویت دیجیتال برای بانک‌ها، صرافی‌ها و فین‌تک‌های ایران — یک API، همه سامانه‌های رسمی کشور.', 'uid-theme' ) ) );
	add_settings_field( 'instagram', __( 'لینک اینستاگرام (خالی = مخفی)', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_brand', array( 'group' => 'uid_footer_options', 'key' => 'instagram', 'default' => '' ) );
	add_settings_field( 'linkedin', __( 'لینک لینکدین (خالی = مخفی)', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_brand', array( 'group' => 'uid_footer_options', 'key' => 'linkedin', 'default' => '' ) );
	add_settings_field( 'global_enabled', __( 'نمایش سراسری', 'uid-theme' ), 'uid_field_checkbox', 'uid_footer_options', 'uid_footer_brand', array( 'group' => 'uid_footer_options', 'key' => 'global_enabled', 'default' => 1, 'label' => __( 'فوتر این قالب روی همه‌ی صفحات نمایش داده شود (برای برداشتن این اولویت، تیک را بردارید و ذخیره کنید)', 'uid-theme' ) ) );

	add_settings_section( 'uid_footer_columns', __( 'ستون‌های لینک فوتر', 'uid-theme' ), '__return_false', 'uid_footer_options' );
	add_settings_field( 'col1_title', __( 'عنوان ستون اول', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_columns', array( 'group' => 'uid_footer_options', 'key' => 'col1_title', 'default' => __( 'سرویس‌های هویتی', 'uid-theme' ) ) );
	add_settings_field( 'col2_title', __( 'عنوان ستون دوم', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_columns', array( 'group' => 'uid_footer_options', 'key' => 'col2_title', 'default' => __( 'استعلام و فناوری', 'uid-theme' ) ) );
	add_settings_field( 'col3_title', __( 'عنوان ستون سوم', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_columns', array( 'group' => 'uid_footer_options', 'key' => 'col3_title', 'default' => __( 'یوآیدی و منابع', 'uid-theme' ) ) );
	add_settings_field( 'footer_links', __( 'لینک‌های داخل ستون‌ها', 'uid-theme' ), 'uid_field_repeater', 'uid_footer_options', 'uid_footer_columns', array(
		'group' => 'uid_footer_options', 'key' => 'footer_links', 'default' => uid_default_footer_links(), 'add_label' => __( 'افزودن لینک', 'uid-theme' ),
		'desc'  => __( 'برای لینک داخلی سایت، فقط مسیر را با / وارد کنید (مثل ‎/faq/‏)؛ برای لینک خارجی یا شماره تلفن (‎tel:...‏)، آدرس کامل بنویسید.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'column', 'type' => 'select', 'label' => __( 'ستون', 'uid-theme' ), 'options' => array( '1' => __( 'ستون ۱', 'uid-theme' ), '2' => __( 'ستون ۲', 'uid-theme' ), '3' => __( 'ستون ۳', 'uid-theme' ) ) ),
			array( 'key' => 'text', 'type' => 'text', 'label' => __( 'متن', 'uid-theme' ), 'required' => true ),
			array( 'key' => 'url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
			array( 'key' => 'tag', 'type' => 'text', 'label' => __( 'برچسب کوچک (اختیاری، مثل «آرشیو»)', 'uid-theme' ) ),
		),
	) );

	add_settings_section( 'uid_footer_bottom', __( 'پایین فوتر', 'uid-theme' ), '__return_false', 'uid_footer_options' );
	add_settings_field( 'copyright', __( 'متن کپی‌رایت', 'uid-theme' ), 'uid_field_text', 'uid_footer_options', 'uid_footer_bottom', array( 'group' => 'uid_footer_options', 'key' => 'copyright', 'default' => __( '© ۱۴۰۵ یوآیدی. تمامی حقوق محفوظ است.', 'uid-theme' ) ) );

	/* ---------------- ترتیب و نمایش سکشن‌های صفحه اصلی ---------------- */
	register_setting( 'uid_home_group', 'uid_home_layout', array(
		'sanitize_callback' => 'uid_sanitize_home_layout',
		'default'           => array(),
	) );
	add_settings_section( 'uid_layout_main', '', '__return_false', 'uid_home_layout' );
	add_settings_field( 'layout', '', 'uid_field_layout_sortable', 'uid_home_layout', 'uid_layout_main', array(
		'option_name' => 'uid_home_layout', 'registry_fn' => 'uid_sections_registry', 'layout_fn' => 'uid_get_home_layout',
	) );

	/* ---------------- هیرو ---------------- */
	register_setting( 'uid_home_group', 'uid_section_hero', array(
		'sanitize_callback' => 'uid_sanitize_section_hero',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_hero_main', '', '__return_false', 'uid_section_hero' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'title_tag', 'default' => 'h1', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'eyebrow', 'default' => __( 'پلتفرم احراز هویت دیجیتال', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان اصلی', 'uid-theme' ), 'uid_field_textarea', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'heading', 'default' => __( 'زیرساخت احراز هویتی که بانک‌ها، صرافی‌ها و فین‌تک‌ها در ایران به آن اعتماد دارند', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'text', 'default' => __( 'با اپلیکیشن اختصاصی و API یکپارچه یوآیدی، فرآیند احراز هویت مشتریان را در چند ثانیه، مطابق با استانداردهای امنیتی و بدون پیچیدگی فنی انجام دهید.', 'uid-theme' ) ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه اول', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'btn1_text', 'default' => __( 'برای کسب‌وکارها — درخواست دمو', 'uid-theme' ) ) );
	add_settings_field( 'btn1_url', __( 'لینک دکمه اول', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'btn1_url', 'default' => '#contact' ) );
	add_settings_field( 'btn2_text', __( 'متن دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'btn2_text', 'default' => __( 'برای افراد — دریافت خدمت ثنا', 'uid-theme' ) ) );
	add_settings_field( 'btn2_url', __( 'لینک دکمه دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'btn2_url', 'default' => '#services' ) );
	add_settings_field( 'visual_id', __( 'تصویر/گیف کنار هیرو (اختیاری)', 'uid-theme' ), 'uid_field_image', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'visual_id', 'desc' => __( 'اگر تصویر یا گیف آپلود کنید، جای گرافیک متحرک پیش‌فرض (حلقه‌ها/کارت‌های شناور) را می‌گیرد.', 'uid-theme' ) ) );
	add_settings_field( 'visual_alt', __( 'متن جایگزین تصویر/گیف (Alt)', 'uid-theme' ), 'uid_field_text', 'uid_section_hero', 'uid_section_hero_main', array( 'group' => 'uid_section_hero', 'key' => 'visual_alt', 'default' => '' ) );

	/* ---------------- مسیر کاربران ---------------- */
	register_setting( 'uid_home_group', 'uid_section_audience', array(
		'sanitize_callback' => 'uid_sanitize_section_audience',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_audience_main', '', '__return_false', 'uid_section_audience' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_audience', 'uid_section_audience_main', array( 'group' => 'uid_section_audience', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_audience', 'uid_section_audience_main', array( 'group' => 'uid_section_audience', 'key' => 'eyebrow', 'default' => __( 'مسیر خود را انتخاب کنید', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_audience', 'uid_section_audience_main', array( 'group' => 'uid_section_audience', 'key' => 'heading', 'default' => __( 'یوآیدی برای دو نوع مخاطب متفاوت کار می‌کند', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_audience', 'uid_section_audience_main', array( 'group' => 'uid_section_audience', 'key' => 'text', 'default' => __( 'چه به‌دنبال یکپارچه‌سازی احراز هویت در سازمان خود باشید، چه نیاز به تکمیل احراز هویت شخصی از طریق ثنا دارید.', 'uid-theme' ) ) );
	add_settings_field( 'cards_notice', '', 'uid_field_notice', 'uid_section_audience', 'uid_section_audience_main', array( 'text' => __( 'محتوای دو کارت (کسب‌وکارها/افراد) در این فاز ثابت است و در فاز بعدی قابل ویرایش خواهد شد.', 'uid-theme' ) ) );

	/* ---------------- برندها ---------------- */
	register_setting( 'uid_home_group', 'uid_section_brands', array(
		'sanitize_callback' => 'uid_sanitize_section_brands',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_brands_main', '', '__return_false', 'uid_section_brands' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_brands', 'uid_section_brands_main', array( 'group' => 'uid_section_brands', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_brands', 'uid_section_brands_main', array( 'group' => 'uid_section_brands', 'key' => 'eyebrow', 'default' => __( 'اعتماد برندهای معتبر', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_brands', 'uid_section_brands_main', array( 'group' => 'uid_section_brands', 'key' => 'heading', 'default' => __( 'مورد اعتماد و همکاری برندهای پیشرو ایران', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_brands', 'uid_section_brands_main', array( 'group' => 'uid_section_brands', 'key' => 'text', 'default' => __( 'تیم‌های ریسک و فناوری در بانک‌ها، صرافی‌ها، فین‌تک‌ها و نهادهای مالی معتبر، از یوآیدی برای احراز هویت مشتریان خود استفاده می‌کنند.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'فهرست برندها', 'uid-theme' ), 'uid_field_repeater', 'uid_section_brands', 'uid_section_brands_main', array(
		'group' => 'uid_section_brands', 'key' => 'items', 'default' => uid_default_brands(),
		'add_label' => __( 'افزودن برند', 'uid-theme' ),
		'desc'  => __( 'برای هر برند می‌توانید لوگو آپلود کنید؛ در نبود لوگو، آواتار حرف اول نام نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'لوگو', 'uid-theme' ), 'pick_label' => __( 'انتخاب لوگو', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
			array( 'key' => 'name', 'type' => 'text', 'label' => __( 'نام برند', 'uid-theme' ) ),
			array( 'key' => 'type', 'type' => 'text', 'label' => __( 'توضیح کوتاه', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'note', __( 'یادداشت پایین برندها (خالی = مخفی)', 'uid-theme' ), 'uid_field_text', 'uid_section_brands', 'uid_section_brands_main', array( 'group' => 'uid_section_brands', 'key' => 'note', 'default' => __( '* نشان‌های بالا آیکون‌های نمونه‌اند و باید پیش از انتشار با لوگوی رسمی هر برند جایگزین شوند — نام‌ها واقعی و از فهرست شرکای یوآیدی هستند.', 'uid-theme' ) ) );

	/* ---------------- آمار در یک نگاه ---------------- */
	register_setting( 'uid_home_group', 'uid_section_stats', array(
		'sanitize_callback' => 'uid_sanitize_section_stats',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_stats_main', '', '__return_false', 'uid_section_stats' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_stats', 'uid_section_stats_main', array( 'group' => 'uid_section_stats', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_stats', 'uid_section_stats_main', array( 'group' => 'uid_section_stats', 'key' => 'eyebrow', 'default' => __( 'در یک نگاه', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_stats', 'uid_section_stats_main', array( 'group' => 'uid_section_stats', 'key' => 'heading', 'default' => __( 'عددها گویای همه‌چیز هستند', 'uid-theme' ) ) );
	add_settings_field( 'note', __( 'یادداشت پایین آمار', 'uid-theme' ), 'uid_field_text', 'uid_section_stats', 'uid_section_stats_main', array( 'group' => 'uid_section_stats', 'key' => 'note', 'default' => __( 'آمار بر اساس داده‌های واقعی معرفی‌نامه محصول یوآیدی‌پلاس.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'کارت‌های آماری', 'uid-theme' ), 'uid_field_repeater', 'uid_section_stats', 'uid_section_stats_main', array(
		'group' => 'uid_section_stats', 'key' => 'items', 'default' => uid_default_stats(),
		'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'desc'  => __( 'در نبود آیکون، یک آیکون پیش‌فرض نمایش داده می‌شود. تعداد اعشار را برای اعداد صحیح روی ۰ بگذارید.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'آیکون', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'مقدار عددی', 'uid-theme' ), 'placeholder' => '5000000' ),
			array( 'key' => 'suffix', 'type' => 'text', 'label' => __( 'پسوند (٪ یا +)', 'uid-theme' ), 'placeholder' => '+' ),
			array( 'key' => 'decimals', 'type' => 'text', 'label' => __( 'تعداد اعشار', 'uid-theme' ), 'placeholder' => '0' ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );

	/* ---------------- خدمات (کارت‌های ویژه) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_services', array(
		'sanitize_callback' => 'uid_sanitize_section_services',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_services_main', '', '__return_false', 'uid_section_services' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'eyebrow', 'default' => __( 'خدمات یوآیدی', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'heading', 'default' => __( 'خدمات احراز هویت و استعلام', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'text', 'default' => __( 'سه خدمت اصلی یوآیدی برای احراز هویت غیرحضوری و یکپارچه‌سازی سازمانی.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'کارت‌های خدمات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_services', 'uid_section_services_main', array(
		'group' => 'uid_section_services', 'key' => 'items', 'default' => uid_default_services(),
		'add_label' => __( 'افزودن کارت خدمت', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'آیکون', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'color', 'type' => 'select', 'label' => __( 'رنگ سربرگ', 'uid-theme' ), 'options' => array( 'navy' => __( 'سرمه‌ای', 'uid-theme' ), 'orange' => __( 'نارنجی', 'uid-theme' ), 'teal' => __( 'فیروزه‌ای', 'uid-theme' ) ) ),
			array( 'key' => 'tag', 'type' => 'text', 'label' => __( 'برچسب کوچک سربرگ', 'uid-theme' ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان کارت', 'uid-theme' ) ),
			array( 'key' => 'desc', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'bullets', 'type' => 'textarea', 'label' => __( 'نکات (هر خط یک نکته)', 'uid-theme' ) ),
			array( 'key' => 'link_text', 'type' => 'text', 'label' => __( 'متن لینک', 'uid-theme' ) ),
			array( 'key' => 'link_url', 'type' => 'text', 'label' => __( 'آدرس لینک', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'footer_button_text', __( 'متن دکمه پایین سکشن', 'uid-theme' ), 'uid_field_text', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'footer_button_text', 'default' => __( 'درخواست دمو', 'uid-theme' ) ) );
	add_settings_field( 'footer_button_url', __( 'لینک دکمه پایین سکشن', 'uid-theme' ), 'uid_field_text', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'footer_button_url', 'default' => '#contact' ) );
	add_settings_field( 'reseller_label', __( 'برچسب ردیف وب‌سرویس‌ها', 'uid-theme' ), 'uid_field_text', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'reseller_label', 'default' => __( 'وب‌سرویس‌های سازمانی:', 'uid-theme' ) ) );
	add_settings_field( 'reseller_items', __( 'موارد ردیف وب‌سرویس‌ها (هر خط یک مورد؛ خالی = مخفی)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_services', 'uid_section_services_main', array( 'group' => 'uid_section_services', 'key' => 'reseller_items', 'default' => "شاهکار\nثبت احوال\nآدرس\nاستعلام شبا\nتطابق کارت\nتبدیل کارت به شبا" ) );

	/* ---------------- مزیت رقابتی ---------------- */
	register_setting( 'uid_home_group', 'uid_section_advantages', array(
		'sanitize_callback' => 'uid_sanitize_section_advantages',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_advantages_main', '', '__return_false', 'uid_section_advantages' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_advantages', 'uid_section_advantages_main', array( 'group' => 'uid_section_advantages', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_advantages', 'uid_section_advantages_main', array( 'group' => 'uid_section_advantages', 'key' => 'eyebrow', 'default' => __( 'مزیت رقابتی', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_advantages', 'uid_section_advantages_main', array( 'group' => 'uid_section_advantages', 'key' => 'heading', 'default' => __( 'چرا سازمان‌ها یوآیدی را انتخاب می‌کنند', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_advantages', 'uid_section_advantages_main', array( 'group' => 'uid_section_advantages', 'key' => 'text', 'default' => __( 'پوشش کامل سامانه‌های رسمی کشور در کنار سرعت، امنیت و پشتیبانی که تیم‌های ریسک به آن نیاز دارند.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'کارت‌های مزیت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_advantages', 'uid_section_advantages_main', array(
		'group' => 'uid_section_advantages', 'key' => 'items', 'default' => uid_default_advantages(),
		'add_label' => __( 'افزودن کارت مزیت', 'uid-theme' ),
		'desc'  => __( 'طرح‌بندی گرید برای ۶ کارت بهینه شده است؛ افزودن بیش از ۶ مورد ممکن است چیدمان را کمی تغییر دهد.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'آیکون', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'color', 'type' => 'select', 'label' => __( 'رنگ پس‌زمینه', 'uid-theme' ), 'options' => array( 'navy' => __( 'سرمه‌ای تیره', 'uid-theme' ), 'navy2' => __( 'سرمه‌ای روشن', 'uid-theme' ), 'teal' => __( 'فیروزه‌ای', 'uid-theme' ), 'orange' => __( 'نارنجی', 'uid-theme' ) ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ) ),
			array( 'key' => 'desc', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
		),
	) );

	/* ---------------- چرا یوآیدی؟ ---------------- */
	register_setting( 'uid_home_group', 'uid_section_why', array(
		'sanitize_callback' => 'uid_sanitize_section_why',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_why_main', '', '__return_false', 'uid_section_why' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'eyebrow', 'default' => __( 'چرا یوآیدی؟', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'heading', 'default' => __( 'دقتی که تیم ریسک به آن نیاز دارد، سرعتی که مشتری انتظار دارد', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'text', 'default' => __( 'یوآیدی زیرساخت احراز هویت را برای تیم‌های ریسک، فنی و محصول در کنار هم قابل اتکا می‌کند — با گزارش‌های شفاف و پشتیبانی مستقیم.', 'uid-theme' ) ) );
	add_settings_field( 'button_text', __( 'متن دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'button_text', 'default' => __( 'درخواست دمو', 'uid-theme' ) ) );
	add_settings_field( 'button_url', __( 'لینک دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'button_url', 'default' => '#contact' ) );
	add_settings_field( 'visual_id', __( 'تصویر/گیف کنار متن (اختیاری)', 'uid-theme' ), 'uid_field_image', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'visual_id', 'desc' => __( 'اگر تصویر یا گیف آپلود کنید، جای نمودار تزئینی پیش‌فرض را می‌گیرد.', 'uid-theme' ) ) );
	add_settings_field( 'visual_alt', __( 'متن جایگزین تصویر/گیف (Alt)', 'uid-theme' ), 'uid_field_text', 'uid_section_why', 'uid_section_why_main', array( 'group' => 'uid_section_why', 'key' => 'visual_alt', 'default' => '' ) );

	/* ---------------- راه‌حل به تفکیک صنعت (تب‌ها) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_help', array(
		'sanitize_callback' => 'uid_sanitize_section_help',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_help_main', '', '__return_false', 'uid_section_help' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_help', 'uid_section_help_main', array( 'group' => 'uid_section_help', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_help', 'uid_section_help_main', array( 'group' => 'uid_section_help', 'key' => 'eyebrow', 'default' => __( 'راه‌حل به تفکیک نوع کسب‌وکار', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_help', 'uid_section_help_main', array( 'group' => 'uid_section_help', 'key' => 'heading', 'default' => __( 'چطور به کسب‌وکار شما کمک می‌کنیم', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_help', 'uid_section_help_main', array( 'group' => 'uid_section_help', 'key' => 'text', 'default' => __( 'نیاز یک بانک با نیاز یک صرافی یا یک استارتاپ فین‌تک یکسان نیست — راه‌حل ما هم نیست.', 'uid-theme' ) ) );
	add_settings_field( 'tabs', __( 'تب‌های صنعت', 'uid-theme' ), 'uid_field_repeater', 'uid_section_help', 'uid_section_help_main', array(
		'group' => 'uid_section_help', 'key' => 'tabs', 'default' => uid_default_help_tabs(),
		'add_label' => __( 'افزودن تب', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'tab_label', 'type' => 'text', 'label' => __( 'برچسب تب', 'uid-theme' ) ),
			array( 'key' => 'icon_id', 'type' => 'image', 'label' => __( 'آیکون تب', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'icon_alt', 'type' => 'text', 'label' => __( 'متن جایگزین آیکون (Alt)', 'uid-theme' ) ),
			array( 'key' => 'color', 'type' => 'select', 'label' => __( 'رنگ پس‌زمینه آیکون', 'uid-theme' ), 'options' => array( 'navy' => __( 'سرمه‌ای', 'uid-theme' ), 'teal' => __( 'فیروزه‌ای', 'uid-theme' ), 'orange' => __( 'نارنجی', 'uid-theme' ), 'navy2' => __( 'سرمه‌ای روشن', 'uid-theme' ) ) ),
			array( 'key' => 'eyebrow', 'type' => 'text', 'label' => __( 'برچسب کوچک', 'uid-theme' ) ),
			array( 'key' => 'heading', 'type' => 'text', 'label' => __( 'عنوان تب', 'uid-theme' ) ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'visual_title', 'type' => 'text', 'label' => __( 'نام راه‌حل (داخل کادر کنار تب)', 'uid-theme' ) ),
			array( 'key' => 'visual_subtitle', 'type' => 'text', 'label' => __( 'توضیح کوتاه راه‌حل (زیر نام)', 'uid-theme' ) ),
			array( 'key' => 'bullets', 'type' => 'textarea', 'label' => __( 'موارد لیست (هر خط یک مورد)', 'uid-theme' ) ),
			array( 'key' => 'stat1_title', 'type' => 'text', 'label' => __( 'آمار اول — عنوان', 'uid-theme' ) ),
			array( 'key' => 'stat1_desc', 'type' => 'text', 'label' => __( 'آمار اول — توضیح', 'uid-theme' ) ),
			array( 'key' => 'stat2_title', 'type' => 'text', 'label' => __( 'آمار دوم — عنوان', 'uid-theme' ) ),
			array( 'key' => 'stat2_desc', 'type' => 'text', 'label' => __( 'آمار دوم — توضیح', 'uid-theme' ) ),
			array( 'key' => 'badges', 'type' => 'textarea', 'label' => __( 'برچسب‌های کوچک (هر خط یک مورد)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- ردیف‌های ویژگی محصول ---------------- */
	register_setting( 'uid_home_group', 'uid_section_feature_rows', array(
		'sanitize_callback' => 'uid_sanitize_section_feature_rows',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_feature_rows_main', '', '__return_false', 'uid_section_feature_rows' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_feature_rows', 'uid_section_feature_rows_main', array( 'group' => 'uid_section_feature_rows', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_feature_rows', 'uid_section_feature_rows_main', array( 'group' => 'uid_section_feature_rows', 'key' => 'eyebrow', 'default' => __( 'درون پلتفرم', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_feature_rows', 'uid_section_feature_rows_main', array( 'group' => 'uid_section_feature_rows', 'key' => 'heading', 'default' => __( 'یک زیرساخت، سه لایه احراز هویت', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_feature_rows', 'uid_section_feature_rows_main', array( 'group' => 'uid_section_feature_rows', 'key' => 'text', 'default' => __( 'از تشخیص زنده بودن چهره تا خواندن هوشمند مدارک — همه از طریق یک API یکپارچه.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'ردیف‌های ویژگی', 'uid-theme' ), 'uid_field_repeater', 'uid_section_feature_rows', 'uid_section_feature_rows_main', array(
		'group' => 'uid_section_feature_rows', 'key' => 'items', 'default' => uid_default_feature_rows(),
		'add_label' => __( 'افزودن ردیف', 'uid-theme' ),
		'desc'  => __( 'اگر تصویری آپلود نکنید، به‌ترتیب یکی از سه گرافیک آماده (اسکن چهره / تطبیق مدرک / OCR) نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'eyebrow', 'type' => 'text', 'label' => __( 'برچسب کوچک', 'uid-theme' ) ),
			array( 'key' => 'heading', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ) ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'link_text', 'type' => 'text', 'label' => __( 'متن لینک', 'uid-theme' ) ),
			array( 'key' => 'link_url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'تصویر (اختیاری)', 'uid-theme' ), 'pick_label' => __( 'انتخاب تصویر', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- مراحل یوآیدی‌پلاس (هاب) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_pwa', array(
		'sanitize_callback' => 'uid_sanitize_section_pwa',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_pwa_main', '', '__return_false', 'uid_section_pwa' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'eyebrow', 'default' => __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'heading', 'default' => __( 'مراحل احراز هویت یکپارچه یوآیدی‌پلاس', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'text', 'default' => __( 'از فراخوانی سرویس تا اعلام نتیجه به پذیرنده — هفت گام، همگی در یک راهکار تحت وب یکپارچه و بدون بار توسعه فنی برای شما.', 'uid-theme' ) ) );
	add_settings_field( 'steps', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwa', 'uid_section_pwa_main', array(
		'group' => 'uid_section_pwa', 'key' => 'steps', 'default' => uid_default_pwa_steps(),
		'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'desc'  => __( 'شماره هر مرحله خودکار از روی ترتیب محاسبه می‌شود؛ مرحله اول و آخر رنگ فیروزه‌ای می‌گیرند.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'icon_id', 'type' => 'image', 'label' => __( 'آیکون مرحله', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'icon_alt', 'type' => 'text', 'label' => __( 'متن جایگزین آیکون (Alt)', 'uid-theme' ) ),
			array( 'key' => 'heading', 'type' => 'text', 'label' => __( 'عنوان مرحله', 'uid-theme' ) ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'badge', 'type' => 'text', 'label' => __( 'برچسب کوچک (اختیاری، مثل «شاهکار»)', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'flow_stats', __( 'آمار پایین جدول مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_pwa', 'uid_section_pwa_main', array(
		'group' => 'uid_section_pwa', 'key' => 'flow_stats', 'default' => uid_default_pwa_flow_stats(),
		'add_label' => __( 'افزودن آمار', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'value', 'type' => 'text', 'label' => __( 'عدد/مقدار', 'uid-theme' ) ),
			array( 'key' => 'label', 'type' => 'text', 'label' => __( 'برچسب', 'uid-theme' ) ),
		),
	) );
	add_settings_field( 'button_text', __( 'متن دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'button_text', 'default' => __( 'درخواست دمو یوآیدی‌پلاس', 'uid-theme' ) ) );
	add_settings_field( 'button_url', __( 'لینک دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_pwa', 'uid_section_pwa_main', array( 'group' => 'uid_section_pwa', 'key' => 'button_url', 'default' => '#contact' ) );

	/* ---------------- بنر تماس (CTA) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_cta', array(
		'sanitize_callback' => 'uid_sanitize_section_cta',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_cta_main', '', '__return_false', 'uid_section_cta' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_cta', 'uid_section_cta_main', array( 'group' => 'uid_section_cta', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_cta', 'uid_section_cta_main', array( 'group' => 'uid_section_cta', 'key' => 'heading', 'default' => __( 'همین امروز فرآیند احراز هویت مشتریان‌تان را ساده کنید', 'uid-theme' ) ) );
	add_settings_field( 'btn1_text', __( 'متن دکمه (دکمه تلفن به‌صورت خودکار از تب «اطلاعات تماس» می‌آید)', 'uid-theme' ), 'uid_field_text', 'uid_section_cta', 'uid_section_cta_main', array( 'group' => 'uid_section_cta', 'key' => 'btn1_text', 'default' => __( 'درخواست دمو', 'uid-theme' ) ) );
	add_settings_field( 'btn1_url', __( 'لینک دکمه', 'uid-theme' ), 'uid_field_text', 'uid_section_cta', 'uid_section_cta_main', array( 'group' => 'uid_section_cta', 'key' => 'btn1_url', 'default' => '#contact' ) );

	/* ---------------- روند همکاری (کاروسل) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_process', array(
		'sanitize_callback' => 'uid_sanitize_section_process',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_process_main', '', '__return_false', 'uid_section_process' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_process', 'uid_section_process_main', array( 'group' => 'uid_section_process', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_process', 'uid_section_process_main', array( 'group' => 'uid_section_process', 'key' => 'eyebrow', 'default' => __( 'روند همکاری', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_process', 'uid_section_process_main', array( 'group' => 'uid_section_process', 'key' => 'heading', 'default' => __( 'سه گام تا راه‌اندازی', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_process', 'uid_section_process_main', array( 'group' => 'uid_section_process', 'key' => 'text', 'default' => __( 'از ثبت درخواست تا اتصال کامل API، مسیر مشخص و کوتاه است.', 'uid-theme' ) ) );
	add_settings_field( 'steps', __( 'مراحل', 'uid-theme' ), 'uid_field_repeater', 'uid_section_process', 'uid_section_process_main', array(
		'group' => 'uid_section_process', 'key' => 'steps', 'default' => uid_default_process_steps(),
		'add_label' => __( 'افزودن مرحله', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ) ),
			array( 'key' => 'text', 'type' => 'textarea', 'label' => __( 'توضیح', 'uid-theme' ) ),
			array( 'key' => 'icon_id', 'type' => 'image', 'label' => __( 'آیکون مرحله', 'uid-theme' ), 'pick_label' => __( 'انتخاب آیکون', 'uid-theme' ) ),
			array( 'key' => 'icon_alt', 'type' => 'text', 'label' => __( 'متن جایگزین آیکون (Alt)', 'uid-theme' ) ),
		),
	) );

	/* ---------------- نظرات مشتریان ---------------- */
	register_setting( 'uid_home_group', 'uid_section_testimonial', array(
		'sanitize_callback' => 'uid_sanitize_section_testimonial',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_testimonial_main', '', '__return_false', 'uid_section_testimonial' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_testimonial', 'uid_section_testimonial_main', array( 'group' => 'uid_section_testimonial', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_testimonial', 'uid_section_testimonial_main', array( 'group' => 'uid_section_testimonial', 'key' => 'eyebrow', 'default' => __( 'نظر مشتریان', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_testimonial', 'uid_section_testimonial_main', array( 'group' => 'uid_section_testimonial', 'key' => 'heading', 'default' => __( 'سازمان‌هایی که به یوآیدی اعتماد کرده‌اند', 'uid-theme' ) ) );
	add_settings_field( 'note', __( 'یادداشت زیر عنوان (مثلاً درباره نمونه‌بودن نظرات)', 'uid-theme' ), 'uid_field_text', 'uid_section_testimonial', 'uid_section_testimonial_main', array( 'group' => 'uid_section_testimonial', 'key' => 'note', 'default' => __( '* نام‌ها و نظرات نمونه‌اند تا زمان دریافت نظرات واقعی مشتریان.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'نظرات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_testimonial', 'uid_section_testimonial_main', array(
		'group' => 'uid_section_testimonial', 'key' => 'items', 'default' => uid_default_testimonials(),
		'add_label' => __( 'افزودن نظر', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'rating', 'type' => 'select', 'label' => __( 'امتیاز', 'uid-theme' ), 'options' => array( '5' => '۵', '4' => '۴', '3' => '۳', '2' => '۲', '1' => '۱' ) ),
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'تصویر مشتری', 'uid-theme' ), 'pick_label' => __( 'انتخاب تصویر', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
			array( 'key' => 'quote', 'type' => 'textarea', 'label' => __( 'متن نظر', 'uid-theme' ) ),
			array( 'key' => 'person_name', 'type' => 'text', 'label' => __( 'نام و سمت', 'uid-theme' ) ),
			array( 'key' => 'person_role', 'type' => 'text', 'label' => __( 'نام سازمان', 'uid-theme' ) ),
		),
	) );

	/* ---------------- وبلاگ و منابع ---------------- */
	register_setting( 'uid_home_group', 'uid_section_resources', array(
		'sanitize_callback' => 'uid_sanitize_section_resources',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_resources_main', '', '__return_false', 'uid_section_resources' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_resources', 'uid_section_resources_main', array( 'group' => 'uid_section_resources', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_resources', 'uid_section_resources_main', array( 'group' => 'uid_section_resources', 'key' => 'eyebrow', 'default' => __( 'وبلاگ و منابع', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_resources', 'uid_section_resources_main', array( 'group' => 'uid_section_resources', 'key' => 'heading', 'default' => __( 'راهنماهای احراز هویت', 'uid-theme' ) ) );
	add_settings_field( 'footer_link_text', __( 'متن لینک «مشاهده همه»', 'uid-theme' ), 'uid_field_text', 'uid_section_resources', 'uid_section_resources_main', array( 'group' => 'uid_section_resources', 'key' => 'footer_link_text', 'default' => __( 'مشاهده همه مقالات', 'uid-theme' ) ) );
	add_settings_field( 'footer_link_url', __( 'لینک «مشاهده همه»', 'uid-theme' ), 'uid_field_text', 'uid_section_resources', 'uid_section_resources_main', array( 'group' => 'uid_section_resources', 'key' => 'footer_link_url', 'default' => '#' ) );
	add_settings_field( 'items', __( 'کارت‌های منابع', 'uid-theme' ), 'uid_field_repeater', 'uid_section_resources', 'uid_section_resources_main', array(
		'group' => 'uid_section_resources', 'key' => 'items', 'default' => uid_default_resources(),
		'add_label' => __( 'افزودن مطلب', 'uid-theme' ),
		'desc'  => __( 'در نبود تصویر، یک گرافیک تزئینی پیش‌فرض نمایش داده می‌شود.', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'image_id', 'type' => 'image', 'label' => __( 'تصویر بندانگشتی', 'uid-theme' ), 'pick_label' => __( 'انتخاب تصویر', 'uid-theme' ) ),
			array( 'key' => 'image_alt', 'type' => 'text', 'label' => __( 'متن جایگزین تصویر (Alt)', 'uid-theme' ) ),
			array( 'key' => 'pill', 'type' => 'text', 'label' => __( 'برچسب دسته', 'uid-theme' ) ),
			array( 'key' => 'meta', 'type' => 'text', 'label' => __( 'نوع محتوا (راهنما/معرفی/...)', 'uid-theme' ) ),
			array( 'key' => 'title', 'type' => 'text', 'label' => __( 'عنوان', 'uid-theme' ) ),
			array( 'key' => 'link_url', 'type' => 'text', 'label' => __( 'لینک', 'uid-theme' ) ),
		),
	) );

	/* ---------------- سوالات متداول ---------------- */
	register_setting( 'uid_home_group', 'uid_section_faq', array(
		'sanitize_callback' => 'uid_sanitize_section_faq',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_faq_main', '', '__return_false', 'uid_section_faq' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_faq', 'uid_section_faq_main', array( 'group' => 'uid_section_faq', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_faq', 'uid_section_faq_main', array( 'group' => 'uid_section_faq', 'key' => 'eyebrow', 'default' => __( 'سوالات متداول', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_faq', 'uid_section_faq_main', array( 'group' => 'uid_section_faq', 'key' => 'heading', 'default' => __( 'سوالاتی که معمولاً پرسیده می‌شود', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_faq', 'uid_section_faq_main', array( 'group' => 'uid_section_faq', 'key' => 'text', 'default' => __( 'پاسخ ندیدید؟ از طریق فرم پایین صفحه مستقیماً از ما بپرسید.', 'uid-theme' ) ) );
	add_settings_field( 'items', __( 'سوالات', 'uid-theme' ), 'uid_field_repeater', 'uid_section_faq', 'uid_section_faq_main', array(
		'group' => 'uid_section_faq', 'key' => 'items', 'default' => uid_default_faq_items(),
		'add_label' => __( 'افزودن سوال', 'uid-theme' ),
		'fields' => array(
			array( 'key' => 'question', 'type' => 'text', 'label' => __( 'سوال', 'uid-theme' ) ),
			array( 'key' => 'answer', 'type' => 'textarea', 'label' => __( 'پاسخ', 'uid-theme' ) ),
		),
	) );

	/* ---------------- فرم تماس ---------------- */
	register_setting( 'uid_home_group', 'uid_section_contact', array(
		'sanitize_callback' => 'uid_sanitize_section_contact',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_contact_main', '', '__return_false', 'uid_section_contact' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'eyebrow', 'default' => __( 'درخواست خدمت', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'heading', 'default' => __( 'همین حالا با تیم یوآیدی در تماس باشید', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح', 'uid-theme' ), 'uid_field_textarea', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'text', 'default' => __( 'فرم را تکمیل کنید تا کارشناسان ما در سریع‌ترین زمان با شما تماس بگیرند.', 'uid-theme' ) ) );
	add_settings_field( 'method1_title', __( 'روش تماس اول — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'method1_title', 'default' => __( 'تماس تلفنی', 'uid-theme' ) ) );
	add_settings_field( 'method1_desc', __( 'روش تماس اول — توضیح', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'method1_desc', 'default' => __( 'پاسخ‌گویی در ساعات اداری', 'uid-theme' ) ) );
	add_settings_field( 'method2_title', __( 'روش تماس دوم — عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'method2_title', 'default' => __( 'ارسال ایمیل', 'uid-theme' ) ) );
	add_settings_field( 'method2_desc', __( 'روش تماس دوم — توضیح', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'method2_desc', 'default' => __( 'پاسخ ظرف یک روز کاری', 'uid-theme' ) ) );
	add_settings_field( 'label_name', __( 'برچسب فیلد نام', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_name', 'default' => __( 'نام و نام خانوادگی', 'uid-theme' ) ) );
	add_settings_field( 'ph_name', __( 'مثال فیلد نام', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'ph_name', 'default' => __( 'مثلاً علی رضایی', 'uid-theme' ) ) );
	add_settings_field( 'label_company', __( 'برچسب فیلد نام شرکت', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_company', 'default' => __( 'نام شرکت', 'uid-theme' ) ) );
	add_settings_field( 'ph_company', __( 'مثال فیلد نام شرکت', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'ph_company', 'default' => __( 'نام سازمان شما', 'uid-theme' ) ) );
	add_settings_field( 'label_email', __( 'برچسب فیلد ایمیل', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_email', 'default' => __( 'ایمیل', 'uid-theme' ) ) );
	add_settings_field( 'label_phone', __( 'برچسب فیلد تلفن', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_phone', 'default' => __( 'شماره تماس', 'uid-theme' ) ) );
	add_settings_field( 'label_service', __( 'برچسب فیلد نوع خدمت', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_service', 'default' => __( 'نوع خدمت مورد نیاز', 'uid-theme' ) ) );
	add_settings_field( 'service_options', __( 'گزینه‌های نوع خدمت (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'service_options', 'default' => "سامانه ثنا\nاستعلام شاهکار\nاستعلام هویتی\nیکپارچه‌سازی API سازمانی" ) );
	add_settings_field( 'label_message', __( 'برچسب فیلد توضیحات', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'label_message', 'default' => __( 'توضیحات', 'uid-theme' ) ) );
	add_settings_field( 'ph_message', __( 'مثال فیلد توضیحات', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'ph_message', 'default' => __( 'نیاز خود را کوتاه شرح دهید', 'uid-theme' ) ) );
	add_settings_field( 'submit_text', __( 'متن دکمه ارسال', 'uid-theme' ), 'uid_field_text', 'uid_section_contact', 'uid_section_contact_main', array( 'group' => 'uid_section_contact', 'key' => 'submit_text', 'default' => __( 'ارسال درخواست', 'uid-theme' ) ) );

	/* ---------------- بنر درخواست تماس (پیش از فوتر) ---------------- */
	register_setting( 'uid_home_group', 'uid_section_leadband', array(
		'sanitize_callback' => 'uid_sanitize_section_leadband',
		'default'           => array(),
	) );
	add_settings_section( 'uid_section_leadband_main', '', '__return_false', 'uid_section_leadband' );
	add_settings_field( 'title_tag', __( 'تگ عنوان اصلی', 'uid-theme' ), 'uid_field_select', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'title_tag', 'default' => 'h2', 'options' => uid_heading_tag_options() ) );
	add_settings_field( 'eyebrow', __( 'برچسب کوچک بالای عنوان', 'uid-theme' ), 'uid_field_text', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'eyebrow', 'default' => __( 'آماده شروع همکاری هستید؟', 'uid-theme' ) ) );
	add_settings_field( 'heading', __( 'عنوان', 'uid-theme' ), 'uid_field_textarea', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'heading', 'default' => __( 'بیایید زیرساخت احراز هویت سازمان شما را با هم راه‌اندازی کنیم', 'uid-theme' ) ) );
	add_settings_field( 'text', __( 'توضیح (شماره تلفن به‌صورت خودکار از تب «اطلاعات تماس» بعد از این متن می‌آید)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'text', 'default' => __( 'فرم را تکمیل کنید تا کارشناسان یوآیدی در سریع‌ترین زمان با شما تماس بگیرند — یا مستقیماً تماس بگیرید.', 'uid-theme' ) ) );
	add_settings_field( 'trust1', __( 'نکته اطمینان اول', 'uid-theme' ), 'uid_field_text', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'trust1', 'default' => __( 'پاسخ در کمتر از یک روز کاری', 'uid-theme' ) ) );
	add_settings_field( 'trust2', __( 'نکته اطمینان دوم', 'uid-theme' ), 'uid_field_text', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'trust2', 'default' => __( 'مشاوره رایگان یکپارچه‌سازی', 'uid-theme' ) ) );
	add_settings_field( 'select_placeholder', __( 'برچسب اول کشویی نوع خدمت', 'uid-theme' ), 'uid_field_text', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'select_placeholder', 'default' => __( 'نوع خدمت مورد نیاز', 'uid-theme' ) ) );
	add_settings_field( 'service_options', __( 'گزینه‌های نوع خدمت (هر خط یک گزینه)', 'uid-theme' ), 'uid_field_textarea', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'service_options', 'default' => "سامانه ثنا\nاستعلام شاهکار\nاستعلام هویتی\nAPI سازمانی" ) );
	add_settings_field( 'button_text', __( 'متن دکمه فرم', 'uid-theme' ), 'uid_field_text', 'uid_section_leadband', 'uid_section_leadband_main', array( 'group' => 'uid_section_leadband', 'key' => 'button_text', 'default' => __( 'درخواست تماس', 'uid-theme' ) ) );
}
add_action( 'admin_init', 'uid_register_settings' );

function uid_heading_tag_options() {
	$opts = array();
	foreach ( UID_HEADING_TAGS as $t ) {
		$opts[ $t ] = strtoupper( $t );
	}
	return $opts;
}

/* ===================== رندر فیلدها ===================== */

function uid_current_value( $group, $key, $default = '' ) {
	$opts = get_option( $group, array() );
	return isset( $opts[ $key ] ) ? $opts[ $key ] : $default;
}

function uid_field_text( $args ) {
	$val = uid_current_value( $args['group'], $args['key'], $args['default'] ?? '' );
	printf(
		'<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s">',
		esc_attr( $args['group'] ),
		esc_attr( $args['key'] ),
		esc_attr( $val )
	);
	if ( ! empty( $args['desc'] ) ) {
		echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
	}
}

function uid_field_textarea( $args ) {
	$val  = uid_current_value( $args['group'], $args['key'], $args['default'] ?? '' );
	$rows = $args['rows'] ?? 3;
	printf(
		'<textarea class="large-text" rows="%4$d" name="%1$s[%2$s]">%3$s</textarea>',
		esc_attr( $args['group'] ),
		esc_attr( $args['key'] ),
		esc_textarea( $val ),
		(int) $rows
	);
	if ( ! empty( $args['desc'] ) ) {
		echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
	}
}

function uid_field_select( $args ) {
	$val = uid_current_value( $args['group'], $args['key'], $args['default'] ?? '' );
	printf( '<select name="%1$s[%2$s]">', esc_attr( $args['group'] ), esc_attr( $args['key'] ) );
	foreach ( $args['options'] as $opt_val => $opt_label ) {
		printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $opt_val ), selected( $val, $opt_val, false ), esc_html( $opt_label ) );
	}
	echo '</select>';
	if ( ! empty( $args['desc'] ) ) {
		echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
	}
}

function uid_field_checkbox( $args ) {
	$val = uid_current_value( $args['group'], $args['key'], $args['default'] ?? 0 );
	printf(
		'<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s> %4$s</label>',
		esc_attr( $args['group'] ),
		esc_attr( $args['key'] ),
		checked( (bool) $val, true, false ),
		esc_html( $args['label'] ?? '' )
	);
}

function uid_field_notice( $args ) {
	echo '<p class="description">' . esc_html( $args['text'] ) . '</p>';
}

function uid_field_logo( $args ) {
	$id  = absint( uid_current_value( $args['group'], $args['key'], 0 ) );
	$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	?>
	<div class="uid-logo-field">
		<img src="<?php echo esc_url( $url ); ?>" id="uid-logo-preview" style="max-height:60px; display:<?php echo $url ? 'block' : 'none'; ?>; margin-bottom:10px; border:1px solid #dcdcde; padding:6px; background:#fff;">
		<input type="hidden" name="uid_header_options[logo_id]" id="uid-logo-id" value="<?php echo esc_attr( $id ); ?>">
		<p>
			<button type="button" class="button" id="uid-logo-upload"><?php esc_html_e( 'انتخاب لوگو', 'uid-theme' ); ?></button>
			<button type="button" class="button" id="uid-logo-remove" style="<?php echo $url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'حذف لوگو', 'uid-theme' ); ?></button>
		</p>
		<?php if ( ! empty( $args['desc'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * فیلد انتخاب یک تصویر عمومی (برای هر group/key) — بر خلاف uid_field_logo که فقط برای لوگوی هدر است
 */
function uid_field_image( $args ) {
	$id  = absint( uid_current_value( $args['group'], $args['key'], 0 ) );
	$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	?>
	<div class="uid-image-field">
		<div class="uid-image-preview" style="display:<?php echo $url ? 'block' : 'none'; ?>; margin-bottom:10px;">
			<img src="<?php echo esc_url( $url ); ?>" style="max-height:120px; border:1px solid #dcdcde; padding:6px; background:#fff;">
		</div>
		<input type="hidden" class="uid-image-value" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>]" value="<?php echo esc_attr( $id ); ?>">
		<p>
			<button type="button" class="button uid-image-pick"><?php echo esc_html( $args['pick_label'] ?? __( 'انتخاب تصویر', 'uid-theme' ) ); ?></button>
			<button type="button" class="button uid-image-remove" style="<?php echo $url ? '' : 'display:none;'; ?>"><?php esc_html_e( 'حذف تصویر', 'uid-theme' ); ?></button>
		</p>
		<?php if ( ! empty( $args['desc'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * لیست واحد ترتیب/نمایش سکشن‌ها — هر ردیف همزمان قابل‌کشیدن (جابه‌جایی ترتیب)
 * و قابل‌بازشدن (برای ویرایش محتوای همان سکشن) است؛ به‌صورت پیش‌فرض بسته است
 * تا فهرست جابه‌جایی شلوغ نشود.
 */
/**
 * لیست یکپارچه‌ی قابل‌استفاده برای هر صفحه‌ای که سیستم سکشن دارد (صفحه اصلی، PWA و...)
 * $args: option_name (نام آپشنِ ترتیب/نمایش)، registry_fn، layout_fn (نام توابع)
 */
function uid_field_layout_sortable( $args ) {
	$option_name = $args['option_name'] ?? 'uid_home_layout';
	$layout      = call_user_func( $args['layout_fn'] ?? 'uid_get_home_layout' );
	$registry    = call_user_func( $args['registry_fn'] ?? 'uid_sections_registry' );
	?>
	<ul id="uid-layout-list" class="uid-layout-list">
		<?php foreach ( $layout as $row ) :
			$slug = $row['slug'];
			$meta = $registry[ $slug ] ?? null;
			if ( ! $meta ) continue;
			$page_slug = 'uid_section_' . $slug;
			?>
			<li class="uid-layout-item" data-slug="<?php echo esc_attr( $slug ); ?>">
				<div class="uid-layout-row">
					<span class="uid-drag-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'برای جابه‌جایی بکشید', 'uid-theme' ); ?>"></span>
					<span class="dashicons <?php echo esc_attr( $meta['icon'] ); ?>"></span>
					<span class="uid-layout-label"><?php echo esc_html( $meta['label'] ); ?></span>
					<label class="uid-layout-toggle" title="<?php esc_attr_e( 'نمایش در این صفحه', 'uid-theme' ); ?>">
						<input type="checkbox" class="uid-layout-enabled" <?php checked( $row['enabled'] ); ?>>
						<span class="uid-switch"></span>
					</label>
					<button type="button" class="uid-layout-expand" aria-expanded="false">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'باز/بسته‌کردن تنظیمات این سکشن', 'uid-theme' ); ?></span>
					</button>
				</div>
				<div class="uid-layout-fields" hidden>
					<table class="form-table" role="presentation"><tbody>
						<?php do_settings_fields( $page_slug, $page_slug . '_main' ); ?>
					</tbody></table>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="<?php echo esc_attr( $option_name ); ?>" id="uid-layout-value" value="">
	<p class="description"><?php esc_html_e( 'برای تغییر ترتیب، هر ردیف را از دستگیره کنار آن بکشید (نیازی به باز کردنش نیست). با کلیک روی فلش، تنظیمات محتوای همان سکشن باز/بسته می‌شود. با خاموش‌کردن کلید کنار هر سکشن، آن سکشن از این صفحه مخفی می‌شود.', 'uid-theme' ); ?></p>
	<?php
}



/**
 * فیلد ریپیتر عمومی — برای هر آیتم می‌تواند فیلدهای متنی، ناحیه متن، انتخابی یا تصویر داشته باشد.
 * $args['fields'] = آرایه‌ای از تعریف زیرفیلدها: ['key'=>..,'type'=>text|textarea|select|image,'label'=>..,'options'=>[...]]
 */
function uid_field_repeater( $args ) {
	$schema = $args['fields'];
	$rows   = uid_current_value( $args['group'], $args['key'], $args['default'] ?? array() );
	if ( ! is_array( $rows ) ) $rows = array();

	$rows_out = array();
	foreach ( array_values( $rows ) as $row ) {
		$r = array();
		foreach ( $schema as $f ) {
			if ( 'image' === $f['type'] ) {
				$id = ! empty( $row[ $f['key'] ] ) ? absint( $row[ $f['key'] ] ) : 0;
				$r[ $f['key'] ]            = $id;
				$r[ $f['key'] . '_url' ]   = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
			} else {
				$r[ $f['key'] ] = $row[ $f['key'] ] ?? '';
			}
		}
		$rows_out[] = $r;
	}

	$uid = 'uid-rep-' . substr( md5( $args['group'] . $args['key'] ), 0, 10 );
	?>
	<div class="uid-repeater" id="<?php echo esc_attr( $uid ); ?>-list" data-schema='<?php echo esc_attr( wp_json_encode( $schema ) ); ?>' data-initial='<?php echo esc_attr( wp_json_encode( $rows_out ) ); ?>'></div>
	<button type="button" class="button uid-repeater-add" data-target="<?php echo esc_attr( $uid ); ?>"><?php echo esc_html( $args['add_label'] ?? __( 'افزودن ردیف', 'uid-theme' ) ); ?></button>
	<input type="hidden" name="<?php echo esc_attr( $args['group'] ); ?>[<?php echo esc_attr( $args['key'] ); ?>]" id="<?php echo esc_attr( $uid ); ?>-value" value="">
	<?php if ( ! empty( $args['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
	<?php endif; ?>
	<?php
}


function uid_sanitize_contact_options( $input ) {
	return array(
		'phone'    => sanitize_text_field( $input['phone'] ?? '' ),
		'whatsapp' => esc_url_raw( $input['whatsapp'] ?? '' ),
		'telegram' => esc_url_raw( $input['telegram'] ?? '' ),
	);
}

function uid_sanitize_header_options( $input ) {
	return array(
		'logo_id'         => absint( $input['logo_id'] ?? 0 ),
		'show_phone'      => ! empty( $input['show_phone'] ),
		'global_enabled'  => ! empty( $input['global_enabled'] ),
		'nav_links'       => uid_sanitize_repeater_rows( $input['nav_links'] ?? '[]', array(
			array( 'key' => 'text', 'type' => 'text', 'required' => true ),
			array( 'key' => 'url', 'type' => 'text' ),
		) ),
		'mega_label'      => sanitize_text_field( $input['mega_label'] ?? '' ),
		'mega_col1_title' => sanitize_text_field( $input['mega_col1_title'] ?? '' ),
		'mega_col2_title' => sanitize_text_field( $input['mega_col2_title'] ?? '' ),
		'mega_col3_title' => sanitize_text_field( $input['mega_col3_title'] ?? '' ),
		'mega_col4_title' => sanitize_text_field( $input['mega_col4_title'] ?? '' ),
		'mega_items'      => uid_sanitize_repeater_rows( $input['mega_items'] ?? '[]', array(
			array( 'key' => 'column', 'type' => 'text' ),
			array( 'key' => 'icon', 'type' => 'text' ),
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'image_alt', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'subtitle', 'type' => 'text' ),
			array( 'key' => 'url', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_footer_options( $input ) {
	$text_fields = array( 'col1_title', 'col2_title', 'col3_title', 'copyright' );
	$textarea_fields = array( 'description' );
	$url_fields = array( 'instagram', 'linkedin' );

	$out = array();
	$out['global_enabled'] = ! empty( $input['global_enabled'] );
	foreach ( $text_fields as $f ) {
		$out[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
	}
	foreach ( $textarea_fields as $f ) {
		$out[ $f ] = sanitize_textarea_field( $input[ $f ] ?? '' );
	}
	foreach ( $url_fields as $f ) {
		$out[ $f ] = esc_url_raw( $input[ $f ] ?? '' );
	}
	$out['footer_links'] = uid_sanitize_repeater_rows( $input['footer_links'] ?? '[]', array(
		array( 'key' => 'column', 'type' => 'text' ),
		array( 'key' => 'text', 'type' => 'text', 'required' => true ),
		array( 'key' => 'url', 'type' => 'text' ),
		array( 'key' => 'tag', 'type' => 'text' ),
	) );
	return $out;
}

function uid_sanitize_section_hero( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h1' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn1_url'  => sanitize_text_field( $input['btn1_url'] ?? '' ),
		'btn2_text' => sanitize_text_field( $input['btn2_text'] ?? '' ),
		'btn2_url'  => sanitize_text_field( $input['btn2_url'] ?? '' ),
		'visual_id' => absint( $input['visual_id'] ?? 0 ),
		'visual_alt'=> sanitize_text_field( $input['visual_alt'] ?? '' ),
	);
}

function uid_sanitize_section_audience( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
	);
}

function uid_sanitize_section_brands( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'image_alt', 'type' => 'text' ),
			array( 'key' => 'name', 'type' => 'text', 'required' => true ),
			array( 'key' => 'type', 'type' => 'text' ),
		) ),
		'note'      => sanitize_text_field( $input['note'] ?? '' ),
	);
}

/**
 * پاک‌سازی عمومی خروجی JSON یک فیلد ریپیتر بر اساس اسکیمای فیلدها
 */
function uid_sanitize_repeater_rows( $json, $schema ) {
	// وردپرس گاهی sanitize_callback را روی همان مقدارِ قبلاً-پاک‌سازی‌شده دوباره صدا می‌زند
	// (یک‌بار در options.php و یک‌بار داخل update_option) — آن‌جا $json دیگر رشته JSON نیست،
	// بلکه خودِ آرایه‌ی PHP آماده است؛ باید هر دو حالت را پشتیبانی کنیم تا داده گم نشود.
	if ( is_array( $json ) ) {
		$raw = $json;
	} else {
		$raw = json_decode( is_string( $json ) ? $json : '[]', true );
	}
	if ( ! is_array( $raw ) ) return array();

	$rows = array();
	foreach ( $raw as $row ) {
		$clean = array();
		$ok    = true;
		foreach ( $schema as $f ) {
			$val = $row[ $f['key'] ] ?? '';
			switch ( $f['type'] ) {
				case 'image':
					$clean[ $f['key'] ] = absint( $val );
					break;
				case 'textarea':
					$clean[ $f['key'] ] = sanitize_textarea_field( $val );
					break;
				case 'url':
					$clean[ $f['key'] ] = esc_url_raw( $val );
					break;
				default:
					$clean[ $f['key'] ] = sanitize_text_field( $val );
			}
			if ( ! empty( $f['required'] ) && '' === trim( (string) $clean[ $f['key'] ] ) ) {
				$ok = false;
			}
		}
		if ( $ok ) $rows[] = $clean;
	}
	return $rows;
}

function uid_sanitize_section_stats( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'note'      => sanitize_text_field( $input['note'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'suffix', 'type' => 'text' ),
			array( 'key' => 'decimals', 'type' => 'text' ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_services( $input ) {
	return array(
		'title_tag'           => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'             => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'             => sanitize_text_field( $input['heading'] ?? '' ),
		'text'                => sanitize_textarea_field( $input['text'] ?? '' ),
		'footer_button_text'  => sanitize_text_field( $input['footer_button_text'] ?? '' ),
		'footer_button_url'   => sanitize_text_field( $input['footer_button_url'] ?? '' ),
		'items'               => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'color', 'type' => 'text' ),
			array( 'key' => 'tag', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'desc', 'type' => 'textarea' ),
			array( 'key' => 'bullets', 'type' => 'textarea' ),
			array( 'key' => 'link_text', 'type' => 'text' ),
			array( 'key' => 'link_url', 'type' => 'text' ),
		) ),
		'reseller_label' => sanitize_text_field( $input['reseller_label'] ?? '' ),
		'reseller_items' => sanitize_textarea_field( $input['reseller_items'] ?? '' ),
	);
}

function uid_sanitize_section_advantages( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'color', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'desc', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_why( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'button_text' => sanitize_text_field( $input['button_text'] ?? '' ),
		'button_url'  => sanitize_text_field( $input['button_url'] ?? '' ),
		'visual_id'   => absint( $input['visual_id'] ?? 0 ),
		'visual_alt'  => sanitize_text_field( $input['visual_alt'] ?? '' ),
	);
}

function uid_sanitize_section_help( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'tabs'      => uid_sanitize_repeater_rows( $input['tabs'] ?? '[]', array(
			array( 'key' => 'tab_label', 'type' => 'text', 'required' => true ),
			array( 'key' => 'icon_id', 'type' => 'image' ),
			array( 'key' => 'icon_alt', 'type' => 'text' ),
			array( 'key' => 'color', 'type' => 'text' ),
			array( 'key' => 'eyebrow', 'type' => 'text' ),
			array( 'key' => 'heading', 'type' => 'text' ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'visual_title', 'type' => 'text' ),
			array( 'key' => 'visual_subtitle', 'type' => 'text' ),
			array( 'key' => 'bullets', 'type' => 'textarea' ),
			array( 'key' => 'stat1_title', 'type' => 'text' ),
			array( 'key' => 'stat1_desc', 'type' => 'text' ),
			array( 'key' => 'stat2_title', 'type' => 'text' ),
			array( 'key' => 'stat2_desc', 'type' => 'text' ),
			array( 'key' => 'badges', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_feature_rows( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'eyebrow', 'type' => 'text' ),
			array( 'key' => 'heading', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'link_text', 'type' => 'text' ),
			array( 'key' => 'link_url', 'type' => 'text' ),
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'image_alt', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_pwa( $input ) {
	return array(
		'title_tag'   => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'     => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'     => sanitize_text_field( $input['heading'] ?? '' ),
		'text'        => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'       => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'icon_id', 'type' => 'image' ),
			array( 'key' => 'icon_alt', 'type' => 'text' ),
			array( 'key' => 'heading', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'badge', 'type' => 'text' ),
		) ),
		'flow_stats'  => uid_sanitize_repeater_rows( $input['flow_stats'] ?? '[]', array(
			array( 'key' => 'value', 'type' => 'text', 'required' => true ),
			array( 'key' => 'label', 'type' => 'text' ),
		) ),
		'button_text' => sanitize_text_field( $input['button_text'] ?? '' ),
		'button_url'  => sanitize_text_field( $input['button_url'] ?? '' ),
	);
}

function uid_sanitize_section_cta( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'heading'   => sanitize_textarea_field( $input['heading'] ?? '' ),
		'btn1_text' => sanitize_text_field( $input['btn1_text'] ?? '' ),
		'btn1_url'  => sanitize_text_field( $input['btn1_url'] ?? '' ),
	);
}

function uid_sanitize_section_process( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'steps'     => uid_sanitize_repeater_rows( $input['steps'] ?? '[]', array(
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'text', 'type' => 'textarea' ),
			array( 'key' => 'icon_id', 'type' => 'image' ),
			array( 'key' => 'icon_alt', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_testimonial( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'note'      => sanitize_text_field( $input['note'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'rating', 'type' => 'text' ),
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'image_alt', 'type' => 'text' ),
			array( 'key' => 'quote', 'type' => 'textarea', 'required' => true ),
			array( 'key' => 'person_name', 'type' => 'text' ),
			array( 'key' => 'person_role', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_resources( $input ) {
	return array(
		'title_tag'        => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'          => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'          => sanitize_text_field( $input['heading'] ?? '' ),
		'footer_link_text' => sanitize_text_field( $input['footer_link_text'] ?? '' ),
		'footer_link_url'  => sanitize_text_field( $input['footer_link_url'] ?? '' ),
		'items'            => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'image_id', 'type' => 'image' ),
			array( 'key' => 'image_alt', 'type' => 'text' ),
			array( 'key' => 'pill', 'type' => 'text' ),
			array( 'key' => 'meta', 'type' => 'text' ),
			array( 'key' => 'title', 'type' => 'text', 'required' => true ),
			array( 'key' => 'link_url', 'type' => 'text' ),
		) ),
	);
}

function uid_sanitize_section_faq( $input ) {
	return array(
		'title_tag' => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'   => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'   => sanitize_text_field( $input['heading'] ?? '' ),
		'text'      => sanitize_textarea_field( $input['text'] ?? '' ),
		'items'     => uid_sanitize_repeater_rows( $input['items'] ?? '[]', array(
			array( 'key' => 'question', 'type' => 'text', 'required' => true ),
			array( 'key' => 'answer', 'type' => 'textarea' ),
		) ),
	);
}

function uid_sanitize_section_contact( $input ) {
	$text_fields = array(
		'eyebrow', 'heading', 'method1_title', 'method1_desc', 'method2_title', 'method2_desc',
		'label_name', 'ph_name', 'label_company', 'ph_company', 'label_email', 'label_phone',
		'label_service', 'label_message', 'ph_message', 'submit_text',
	);
	$out = array(
		'title_tag'       => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'text'            => sanitize_textarea_field( $input['text'] ?? '' ),
		'service_options' => sanitize_textarea_field( $input['service_options'] ?? '' ),
	);
	foreach ( $text_fields as $f ) {
		$out[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
	}
	return $out;
}

function uid_sanitize_section_leadband( $input ) {
	return array(
		'title_tag'          => uid_sanitize_section_tag( $input['title_tag'] ?? '', 'h2' ),
		'eyebrow'            => sanitize_text_field( $input['eyebrow'] ?? '' ),
		'heading'            => sanitize_textarea_field( $input['heading'] ?? '' ),
		'text'               => sanitize_textarea_field( $input['text'] ?? '' ),
		'trust1'             => sanitize_text_field( $input['trust1'] ?? '' ),
		'trust2'             => sanitize_text_field( $input['trust2'] ?? '' ),
		'select_placeholder' => sanitize_text_field( $input['select_placeholder'] ?? '' ),
		'service_options'    => sanitize_textarea_field( $input['service_options'] ?? '' ),
		'button_text'        => sanitize_text_field( $input['button_text'] ?? '' ),
	);
}

/* ===================== صفحه تنظیمات (تب‌بندی‌شده) ===================== */

function uid_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) return;

	$tabs = array(
		'submissions' => array( 'label' => __( 'درخواست‌های ارسالی', 'uid-theme' ), 'icon' => 'dashicons-email-alt' ),
		'pages'       => array( 'label' => __( 'مدیریت برگه‌ها', 'uid-theme' ), 'icon' => 'dashicons-admin-page' ),
		'home'    => array( 'label' => __( 'صفحه اصلی', 'uid-theme' ), 'icon' => 'dashicons-admin-home' ),
		'pwa'     => array( 'label' => __( 'صفحه یوآیدی‌پلاس', 'uid-theme' ), 'icon' => 'dashicons-smartphone' ),
		'civil'   => array( 'label' => __( 'صفحه ثبت احوال', 'uid-theme' ), 'icon' => 'dashicons-id-alt' ),
		'ekyc'    => array( 'label' => __( 'صفحه احراز هویت تصویری', 'uid-theme' ), 'icon' => 'dashicons-camera' ),
		'sana'    => array( 'label' => __( 'صفحه احراز هویت ثنا', 'uid-theme' ), 'icon' => 'dashicons-id' ),
		'shahkar' => array( 'label' => __( 'صفحه وب‌سرویس شاهکار', 'uid-theme' ), 'icon' => 'dashicons-smartphone' ),
		'postal'  => array( 'label' => __( 'صفحه استعلام کد پستی', 'uid-theme' ), 'icon' => 'dashicons-location-alt' ),
		'iban'    => array( 'label' => __( 'صفحه استعلام شبا', 'uid-theme' ), 'icon' => 'dashicons-bank' ),
		'cardiban' => array( 'label' => __( 'صفحه تبدیل کارت به شبا', 'uid-theme' ), 'icon' => 'dashicons-money-alt' ),
		'ibanvalidate' => array( 'label' => __( 'صفحه تطبیق شبا با کد ملی', 'uid-theme' ), 'icon' => 'dashicons-yes-alt' ),
		'cardvalidate' => array( 'label' => __( 'صفحه تطبیق کارت با کد ملی', 'uid-theme' ), 'icon' => 'dashicons-forms' ),
		'sanaabroad' => array( 'label' => __( 'صفحه ثنا ایرانیان خارج از کشور', 'uid-theme' ), 'icon' => 'dashicons-earth' ),
		'webservices' => array( 'label' => __( 'صفحه فهرست وب‌سرویس‌ها', 'uid-theme' ), 'icon' => 'dashicons-grid-view' ),
		'glossary' => array( 'label' => __( 'صفحه واژه‌نامه اصطلاحات', 'uid-theme' ), 'icon' => 'dashicons-book-alt' ),
		'aboutus' => array( 'label' => __( 'صفحه درباره ما', 'uid-theme' ), 'icon' => 'dashicons-info' ),
		'contactus' => array( 'label' => __( 'صفحه تماس با ما', 'uid-theme' ), 'icon' => 'dashicons-phone' ),
		'ocrarticle' => array( 'label' => __( 'مقاله OCR چیست؟', 'uid-theme' ), 'icon' => 'dashicons-media-document' ),
		'header'  => array( 'label' => __( 'هدر', 'uid-theme' ), 'icon' => 'dashicons-align-right' ),
		'footer'  => array( 'label' => __( 'فوتر', 'uid-theme' ), 'icon' => 'dashicons-align-left' ),
		'contact' => array( 'label' => __( 'اطلاعات تماس', 'uid-theme' ), 'icon' => 'dashicons-phone' ),
	);
	$active_tab = isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ? sanitize_key( $_GET['tab'] ) : 'home';
	$page_map   = array(
		'header'  => 'uid_header_options',
		'footer'  => 'uid_footer_options',
		'contact' => 'uid_contact_options',
	);
	$group_map  = array(
		'header'  => 'uid_header_group',
		'footer'  => 'uid_footer_group',
		'contact' => 'uid_contact_group',
	);
	/**
	 * تب‌هایی که «لیست یکپارچه ترتیب/نمایش سکشن‌ها + اسلاگ برگه» دارند (نه فرم ساده تک‌بخشی)
	 */
	$layout_tabs = array(
		'home' => array(
			'group'            => 'uid_home_group',
			'layout_page'      => 'uid_home_layout',
			'slug_section'     => 'uid_homepage_slug_section',
			'sections_section' => 'uid_layout_main',
			'slug_heading'     => __( 'آدرس صفحه اصلی', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه اصلی', 'uid-theme' ),
		),
		'pwa' => array(
			'group'            => 'uid_pwa_group',
			'layout_page'      => 'uid_pwa_layout',
			'slug_section'     => 'uid_pwa_page_slug_section',
			'sections_section' => 'uid_pwa_layout_main',
			'slug_heading'     => __( 'آدرس صفحه یوآیدی‌پلاس', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه یوآیدی‌پلاس', 'uid-theme' ),
		),
		'civil' => array(
			'group'            => 'uid_cr_group',
			'layout_page'      => 'uid_cr_layout',
			'slug_section'     => 'uid_cr_page_slug_section',
			'sections_section' => 'uid_cr_layout_main',
			'slug_heading'     => __( 'آدرس صفحه ثبت احوال', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه ثبت احوال', 'uid-theme' ),
		),
		'ekyc' => array(
			'group'            => 'uid_ek_group',
			'layout_page'      => 'uid_ek_layout',
			'slug_section'     => 'uid_ek_page_slug_section',
			'sections_section' => 'uid_ek_layout_main',
			'slug_heading'     => __( 'آدرس صفحه احراز هویت تصویری', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه احراز هویت تصویری', 'uid-theme' ),
		),
		'sana' => array(
			'group'            => 'uid_sn_group',
			'layout_page'      => 'uid_sn_layout',
			'slug_section'     => 'uid_sn_page_slug_section',
			'sections_section' => 'uid_sn_layout_main',
			'slug_heading'     => __( 'آدرس صفحه ثنا', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه ثنا', 'uid-theme' ),
		),
		'shahkar' => array(
			'group'            => 'uid_sk_group',
			'layout_page'      => 'uid_sk_layout',
			'slug_section'     => 'uid_sk_page_slug_section',
			'sections_section' => 'uid_sk_layout_main',
			'slug_heading'     => __( 'آدرس صفحه شاهکار', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه شاهکار', 'uid-theme' ),
		),
		'postal' => array(
			'group'            => 'uid_pa_group',
			'layout_page'      => 'uid_pa_layout',
			'slug_section'     => 'uid_pa_page_slug_section',
			'sections_section' => 'uid_pa_layout_main',
			'slug_heading'     => __( 'آدرس صفحه استعلام کد پستی', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه استعلام کد پستی', 'uid-theme' ),
		),
		'iban' => array(
			'group'            => 'uid_ib_group',
			'layout_page'      => 'uid_ib_layout',
			'slug_section'     => 'uid_ib_page_slug_section',
			'sections_section' => 'uid_ib_layout_main',
			'slug_heading'     => __( 'آدرس صفحه استعلام شبا', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه استعلام شبا', 'uid-theme' ),
		),
		'cardiban' => array(
			'group'            => 'uid_ci_group',
			'layout_page'      => 'uid_ci_layout',
			'slug_section'     => 'uid_ci_page_slug_section',
			'sections_section' => 'uid_ci_layout_main',
			'slug_heading'     => __( 'آدرس صفحه تبدیل کارت به شبا', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه تبدیل کارت به شبا', 'uid-theme' ),
		),
		'ibanvalidate' => array(
			'group'            => 'uid_iv_group',
			'layout_page'      => 'uid_iv_layout',
			'slug_section'     => 'uid_iv_page_slug_section',
			'sections_section' => 'uid_iv_layout_main',
			'slug_heading'     => __( 'آدرس صفحه تطبیق شبا با کد ملی', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه تطبیق شبا با کد ملی', 'uid-theme' ),
		),
		'cardvalidate' => array(
			'group'            => 'uid_cv_group',
			'layout_page'      => 'uid_cv_layout',
			'slug_section'     => 'uid_cv_page_slug_section',
			'sections_section' => 'uid_cv_layout_main',
			'slug_heading'     => __( 'آدرس صفحه تطبیق کارت با کد ملی', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه تطبیق کارت با کد ملی', 'uid-theme' ),
		),
		'sanaabroad' => array(
			'group'            => 'uid_sa_group',
			'layout_page'      => 'uid_sa_layout',
			'slug_section'     => 'uid_sa_page_slug_section',
			'sections_section' => 'uid_sa_layout_main',
			'slug_heading'     => __( 'آدرس صفحه ثنا ایرانیان خارج از کشور', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه ثنا ایرانیان خارج از کشور', 'uid-theme' ),
		),
		'webservices' => array(
			'group'            => 'uid_wsh_group',
			'layout_page'      => 'uid_wsh_layout',
			'slug_section'     => 'uid_wsh_page_slug_section',
			'sections_section' => 'uid_wsh_layout_main',
			'slug_heading'     => __( 'آدرس صفحه فهرست وب‌سرویس‌ها', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه فهرست وب‌سرویس‌ها', 'uid-theme' ),
		),
		'glossary' => array(
			'group'            => 'uid_gx_group',
			'layout_page'      => 'uid_gx_layout',
			'slug_section'     => 'uid_gx_page_slug_section',
			'sections_section' => 'uid_gx_layout_main',
			'slug_heading'     => __( 'آدرس صفحه واژه‌نامه اصطلاحات', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه واژه‌نامه اصطلاحات', 'uid-theme' ),
		),
		'aboutus' => array(
			'group'            => 'uid_ab_group',
			'layout_page'      => 'uid_ab_layout',
			'slug_section'     => 'uid_ab_page_slug_section',
			'sections_section' => 'uid_ab_layout_main',
			'slug_heading'     => __( 'آدرس صفحه درباره ما', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه درباره ما', 'uid-theme' ),
		),
		'contactus' => array(
			'group'            => 'uid_cu_group',
			'layout_page'      => 'uid_cu_layout',
			'slug_section'     => 'uid_cu_page_slug_section',
			'sections_section' => 'uid_cu_layout_main',
			'slug_heading'     => __( 'آدرس صفحه تماس با ما', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه تماس با ما', 'uid-theme' ),
		),
		'ocrarticle' => array(
			'group'            => 'uid_op_group',
			'layout_page'      => 'uid_op_layout',
			'slug_section'     => 'uid_op_page_slug_section',
			'sections_section' => 'uid_op_layout_main',
			'slug_heading'     => __( 'آدرس صفحه مقاله OCR', 'uid-theme' ),
			'sections_heading' => __( 'سکشن‌های صفحه مقاله OCR', 'uid-theme' ),
		),
	);
	?>
	<div class="wrap" id="uid-settings-page">
		<div class="uid-settings-head">
			<span class="uid-settings-icon"><span class="dashicons dashicons-admin-customizer"></span></span>
			<div>
				<h1><?php esc_html_e( 'تنظیمات قالب یوآیدی', 'uid-theme' ); ?></h1>
				<p><?php esc_html_e( 'این صفحه مستقل از «سفارشی‌سازی» وردپرس است. تغییرات بلافاصله پس از ذخیره در سایت اعمال می‌شوند.', 'uid-theme' ); ?></p>
			</div>
		</div>

		<div class="uid-settings-body">
			<nav class="uid-settings-sidebar">
				<?php foreach ( $tabs as $slug => $tab ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'uid-theme-settings', 'tab' => $slug ), admin_url( 'admin.php' ) ) ); ?>" class="uid-settings-nav-item <?php echo $active_tab === $slug ? 'is-active' : ''; ?>">
						<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
						<?php echo esc_html( $tab['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="uid-settings-content">
				<?php if ( 'submissions' === $active_tab ) :
					uid_render_form_submissions_tab();
				elseif ( 'pages' === $active_tab ) :
					uid_render_page_manager_tab();
				elseif ( isset( $layout_tabs[ $active_tab ] ) ) :
					$lt = $layout_tabs[ $active_tab ];
					?>
					<form method="post" action="options.php">
						<?php settings_fields( $lt['group'] ); ?>
						<?php submit_button( __( 'ذخیره تغییرات', 'uid-theme' ), 'primary', 'submit_top' ); ?>

						<h2><?php echo esc_html( $lt['slug_heading'] ); ?></h2>
						<table class="form-table" role="presentation"><tbody>
							<?php do_settings_fields( $lt['layout_page'], $lt['slug_section'] ); ?>
						</tbody></table>

						<h2><?php echo esc_html( $lt['sections_heading'] ); ?></h2>
						<table class="form-table" role="presentation"><tbody>
							<?php do_settings_fields( $lt['layout_page'], $lt['sections_section'] ); ?>
						</tbody></table>

						<?php submit_button( __( 'ذخیره تغییرات', 'uid-theme' ) ); ?>
					</form>
				<?php else : ?>
					<form method="post" action="options.php">
						<?php
						settings_fields( $group_map[ $active_tab ] );
						submit_button( __( 'ذخیره تغییرات', 'uid-theme' ), 'primary', 'submit_top' );
						do_settings_sections( $page_map[ $active_tab ] );
						submit_button( __( 'ذخیره تغییرات', 'uid-theme' ) );
						?>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * بارگذاری رسانه (media uploader) فقط در همین صفحه تنظیمات
 */
function uid_admin_enqueue( $hook ) {
	if ( 'toplevel_page_uid-theme-settings' !== $hook ) return;

	wp_enqueue_style( 'uid-admin-font', UID_THEME_URI . '/assets/css/fonts.css', array(), UID_THEME_VERSION );
	wp_enqueue_style( 'uid-admin-settings', UID_THEME_URI . '/assets/css/admin.css', array(), UID_THEME_VERSION );

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_add_inline_script( 'jquery-ui-sortable', "
		jQuery(function($){
			var \$list = $('#uid-layout-list');
			if(!\$list.length) return;
			var \$hidden = $('#uid-layout-value');
			var \$form   = \$list.closest('form');

			function syncHidden(){
				var data = [];
				\$list.find('.uid-layout-item').each(function(){
					data.push({
						slug: $(this).data('slug'),
						enabled: $(this).find('.uid-layout-enabled').is(':checked')
					});
				});
				\$hidden.val(JSON.stringify(data));
			}

			\$list.sortable({ handle: '.uid-drag-handle', axis: 'y', update: syncHidden });
			\$list.on('change', '.uid-layout-enabled', syncHidden);
			\$form.on('submit', syncHidden);
			syncHidden();

			// باز/بسته‌کردن تنظیمات هر سکشن — با کلیک روی کل ردیف (نه فقط فلش کوچک)
			function toggleLayoutItem(\$item){
				var \$btn    = \$item.find('.uid-layout-expand');
				var \$fields = \$item.find('.uid-layout-fields');
				var isOpen  = \$btn.attr('aria-expanded') === 'true';
				\$btn.attr('aria-expanded', isOpen ? 'false' : 'true');
				\$fields.prop('hidden', isOpen);
			}
			\$list.on('click', '.uid-layout-row', function(e){
				// جابه‌جایی (دستگیره) و کلید روشن/خاموش، رفتار مستقل خودشان را دارند
				if($(e.target).closest('.uid-drag-handle, .uid-layout-toggle').length) return;
				toggleLayoutItem($(this).closest('.uid-layout-item'));
			});
		});
	" );

	wp_add_inline_script( 'jquery-ui-sortable', "
		jQuery(function($){
			var \$repeaters = $('.uid-repeater');
			if(!\$repeaters.length) return;
			var \$form = \$repeaters.first().closest('form');

			function esc(s){ return $('<div>').text(s == null ? '' : s).html(); }

			function fieldHtml(f, val){
				var label = f.label || '';
				var ph    = f.placeholder || label;
				if(f.type === 'textarea'){
					return '<div class=\"uid-rep-field uid-rep-field-textarea\"><label>' + esc(label) + '</label><textarea class=\"uid-rep-input\" data-key=\"' + f.key + '\" placeholder=\"' + esc(ph) + '\" rows=\"2\">' + esc(val) + '</textarea></div>';
				}
				if(f.type === 'select'){
					var opts = '';
					$.each(f.options || {}, function(ov, ol){
						opts += '<option value=\"' + ov + '\"' + (ov === val ? ' selected' : '') + '>' + esc(ol) + '</option>';
					});
					return '<div class=\"uid-rep-field uid-rep-field-select\"><label>' + esc(label) + '</label><select class=\"uid-rep-input\" data-key=\"' + f.key + '\">' + opts + '</select></div>';
				}
				if(f.type === 'image'){
					return '<div class=\"uid-rep-field uid-rep-field-image\">' +
						'<div class=\"uid-rep-thumb\" style=\"display:none;\"><img src=\"\" alt=\"\"></div>' +
						'<button type=\"button\" class=\"button uid-rep-pick\">' + esc(f.pick_label || '" . esc_js( __( 'انتخاب تصویر', 'uid-theme' ) ) . "') + '</button>' +
						'<input type=\"hidden\" class=\"uid-rep-input\" data-key=\"' + f.key + '\" value=\"0\">' +
					'</div>';
				}
				return '<div class=\"uid-rep-field uid-rep-field-text\"><label>' + esc(label) + '</label><input type=\"text\" class=\"uid-rep-input\" data-key=\"' + f.key + '\" placeholder=\"' + esc(ph) + '\" value=\"' + esc(val) + '\"></div>';
			}

			function rowHtml(schema, row){
				var html = '<div class=\"uid-rep-row\">';
				schema.forEach(function(f){ html += fieldHtml(f, row[f.key]); });
				html += '<button type=\"button\" class=\"button-link uid-rep-remove\" aria-label=\"" . esc_js( __( 'حذف', 'uid-theme' ) ) . "\"><span class=\"dashicons dashicons-trash\"></span></button>';
				html += '</div>';
				return html;
			}

			function applyRowValues(\$row, schema, row){
				schema.forEach(function(f){
					if(f.type === 'image'){
						var url = row[f.key + '_url'];
						var id  = row[f.key] || 0;
						\$row.find('.uid-rep-field-image .uid-rep-input').val(id);
						if(url){ \$row.find('.uid-rep-field-image .uid-rep-thumb').show().find('img').attr('src', url); }
					} else if(f.type === 'select'){
						\$row.find('select[data-key=\"' + f.key + '\"]').val(row[f.key] || '');
					}
				});
			}

			\$repeaters.each(function(){
				var \$el = $(this);
				var schema, rows;
				try { schema = JSON.parse(\$el.attr('data-schema')) || []; } catch(e){ schema = []; }
				try { rows   = JSON.parse(\$el.attr('data-initial')) || []; } catch(e){ rows = []; }
				\$el.data('uid-schema', schema);
				rows.forEach(function(row){
					\$el.append(rowHtml(schema, row));
				});
				\$el.find('.uid-rep-row').each(function(i){
					applyRowValues($(this), schema, rows[i] || {});
				});
			});

			$(document).on('click', '.uid-repeater-add', function(){
				var target = $(this).data('target');
				var \$el = $('#' + target + '-list');
				var schema = \$el.data('uid-schema') || [];
				\$el.append(rowHtml(schema, {}));
			});

			$(document).on('click', '.uid-rep-remove', function(){
				$(this).closest('.uid-rep-row').remove();
			});

			$(document).on('click', '.uid-rep-pick', function(e){
				e.preventDefault();
				var \$field = $(this).closest('.uid-rep-field-image');
				var frame = wp.media({
					title: '" . esc_js( __( 'انتخاب تصویر', 'uid-theme' ) ) . "',
					button: { text: '" . esc_js( __( 'استفاده از این تصویر', 'uid-theme' ) ) . "' },
					multiple: false
				});
				frame.on('select', function(){
					var att = frame.state().get('selection').first().toJSON();
					var url = att.sizes && att.sizes.thumbnail ? att.sizes.thumbnail.url : att.url;
					\$field.find('.uid-rep-input').val(att.id);
					\$field.find('.uid-rep-thumb').show().find('img').attr('src', url);
				});
				frame.open();
			});

			if(\$form.length){
				\$form.on('submit', function(){
					\$repeaters.each(function(){
						var \$el = $(this);
						var id = \$el.attr('id');
						var baseId = id.replace(/-list$/, '');
						var schema = \$el.data('uid-schema') || [];
						var data = [];
						\$el.find('.uid-rep-row').each(function(){
							var \$row = $(this);
							var obj = {};
							var hasContent = false;
							schema.forEach(function(f){
								var \$input = \$row.find('[data-key=\"' + f.key + '\"]');
								var v = \$input.val();
								if(f.type === 'image'){
									obj[f.key] = parseInt(v, 10) || 0;
									if(obj[f.key]) hasContent = true;
								} else {
									obj[f.key] = v;
									if(v) hasContent = true;
								}
							});
							if(hasContent) data.push(obj);
						});
						$('#' + baseId + '-value').val(JSON.stringify(data));
					});
				});
			}
		});
	" );

	wp_add_inline_script( 'media-editor', "
		document.addEventListener('DOMContentLoaded', function(){
			var uploadBtn = document.getElementById('uid-logo-upload');
			var removeBtn = document.getElementById('uid-logo-remove');
			var idInput   = document.getElementById('uid-logo-id');
			var preview   = document.getElementById('uid-logo-preview');
			if(!uploadBtn) return;
			var frame;
			uploadBtn.addEventListener('click', function(e){
				e.preventDefault();
				if(frame){ frame.open(); return; }
				frame = wp.media({
					title: '" . esc_js( __( 'انتخاب لوگو', 'uid-theme' ) ) . "',
					button: { text: '" . esc_js( __( 'استفاده از این تصویر', 'uid-theme' ) ) . "' },
					multiple: false
				});
				frame.on('select', function(){
					var att = frame.state().get('selection').first().toJSON();
					idInput.value = att.id;
					preview.src = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
					preview.style.display = 'block';
					removeBtn.style.display = 'inline-block';
				});
				frame.open();
			});
			if(removeBtn){
				removeBtn.addEventListener('click', function(e){
					e.preventDefault();
					idInput.value = '';
					preview.style.display = 'none';
					removeBtn.style.display = 'none';
				});
			}
		});
	" );

	// انتخاب‌گر عمومی یک‌تصویره (مثل تصویر اشتراک‌گذاری سئو) — بر پایه کلاس، برای هر تعداد فیلد کار می‌کند
	wp_add_inline_script( 'media-editor', "
		jQuery(function($){
			$(document).on('click', '.uid-image-pick', function(e){
				e.preventDefault();
				var \$field = $(this).closest('.uid-image-field');
				var frame = wp.media({
					title: '" . esc_js( __( 'انتخاب تصویر', 'uid-theme' ) ) . "',
					button: { text: '" . esc_js( __( 'استفاده از این تصویر', 'uid-theme' ) ) . "' },
					multiple: false
				});
				frame.on('select', function(){
					var att = frame.state().get('selection').first().toJSON();
					var url = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
					\$field.find('.uid-image-value').val(att.id);
					\$field.find('.uid-image-preview').show().find('img').attr('src', url);
					\$field.find('.uid-image-remove').show();
				});
				frame.open();
			});
			$(document).on('click', '.uid-image-remove', function(e){
				e.preventDefault();
				var \$field = $(this).closest('.uid-image-field');
				\$field.find('.uid-image-value').val('0');
				\$field.find('.uid-image-preview').hide();
				$(this).hide();
			});
		});
	" );
}
add_action( 'admin_enqueue_scripts', 'uid_admin_enqueue' );
