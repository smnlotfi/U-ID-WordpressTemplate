<?php
/**
 * مدیریت سکشن‌های صفحه اصلی: ثبت سکشن‌ها، ترتیب/نمایش، و رندر هرکدام.
 *
 * برای افزودن سکشن جدید در فازهای بعدی:
 *   ۱) یک آیتم به uid_sections_registry() اضافه کنید
 *   ۲) تنظیمات محتوای آن را در inc/admin-settings.php ثبت کنید (مثل بخش‌های هیرو/مسیر کاربران/برندها)
 *   ۳) تابع uid_render_section_{slug}() را در همین فایل بنویسید
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UID_HEADING_TAGS', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'div' ) );

/**
 * فهرست سکشن‌های قابل‌مدیریت صفحه اصلی
 */
function uid_sections_registry() {
	return array(
		'hero'         => array( 'label' => __( 'هیرو (بالای صفحه)', 'uid-theme' ),                 'icon' => 'dashicons-star-filled' ),
		'audience'     => array( 'label' => __( 'مسیر کاربران (کسب‌وکار/شخص)', 'uid-theme' ),        'icon' => 'dashicons-groups' ),
		'brands'       => array( 'label' => __( 'برندهای همکار', 'uid-theme' ),                      'icon' => 'dashicons-awards' ),
		'help'         => array( 'label' => __( 'راه‌حل به تفکیک صنعت (تب‌ها)', 'uid-theme' ),        'icon' => 'dashicons-networking' ),
		'stats'        => array( 'label' => __( 'آمار در یک نگاه', 'uid-theme' ),                    'icon' => 'dashicons-chart-bar' ),
		'services'     => array( 'label' => __( 'خدمات (کارت‌های ویژه)', 'uid-theme' ),              'icon' => 'dashicons-grid-view' ),
		'advantages'   => array( 'label' => __( 'مزیت رقابتی', 'uid-theme' ),                        'icon' => 'dashicons-layout' ),
		'why'          => array( 'label' => __( 'چرا یوآیدی؟', 'uid-theme' ),                        'icon' => 'dashicons-star-empty' ),
		'feature_rows' => array( 'label' => __( 'ردیف‌های ویژگی محصول', 'uid-theme' ),               'icon' => 'dashicons-images-alt2' ),
		'pwa'          => array( 'label' => __( 'مراحل یوآیدی‌پلاس (هاب)', 'uid-theme' ),            'icon' => 'dashicons-list-view' ),
		'cta'          => array( 'label' => __( 'بنر تماس (CTA)', 'uid-theme' ),                     'icon' => 'dashicons-megaphone' ),
		'process'      => array( 'label' => __( 'روند همکاری (کاروسل)', 'uid-theme' ),               'icon' => 'dashicons-controls-forward' ),
		'testimonial'  => array( 'label' => __( 'نظرات مشتریان', 'uid-theme' ),                      'icon' => 'dashicons-format-quote' ),
		'resources'    => array( 'label' => __( 'وبلاگ و منابع', 'uid-theme' ),                      'icon' => 'dashicons-admin-post' ),
		'faq'          => array( 'label' => __( 'سوالات متداول', 'uid-theme' ),                      'icon' => 'dashicons-editor-help' ),
		'contact'      => array( 'label' => __( 'فرم تماس', 'uid-theme' ),                           'icon' => 'dashicons-email-alt' ),
		'leadband'     => array( 'label' => __( 'بنر درخواست تماس (پیش از فوتر)', 'uid-theme' ),      'icon' => 'dashicons-megaphone' ),
	);
}

/**
 * ترتیب و وضعیت نمایش سکشن‌ها — [ ['slug'=>'hero','enabled'=>true], ... ]
 */
function uid_get_home_layout() {
	$registry = uid_sections_registry();
	$saved    = get_option( 'uid_home_layout', array() );

	if ( empty( $saved ) || ! is_array( $saved ) ) {
		$layout = array();
		foreach ( $registry as $slug => $meta ) {
			$layout[] = array( 'slug' => $slug, 'enabled' => true );
		}
		return $layout;
	}

	// حذف اسلاگ‌های نامعتبر (سکشن حذف‌شده) و افزودن سکشن‌های جدیدی که هنوز در ترتیب ذخیره‌شده نیستند
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

function uid_sanitize_home_layout( $input ) {
	$raw = is_string( $input ) ? json_decode( $input, true ) : $input;
	if ( ! is_array( $raw ) ) return array();

	$registry = uid_sections_registry();
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
 * خواندن یک مقدار محتوای سکشن
 */
function uid_section_val( $section, $key, $default = '' ) {
	return uid_get_option( 'uid_section_' . $section, $key, $default );
}

function uid_section_tag( $section, $default = 'h2' ) {
	$tag = uid_section_val( $section, 'title_tag', $default );
	return in_array( $tag, UID_HEADING_TAGS, true ) ? $tag : $default;
}

function uid_sanitize_section_tag( $tag, $default = 'h2' ) {
	return in_array( $tag, UID_HEADING_TAGS, true ) ? $tag : $default;
}

/**
 * رندر کل صفحه اصلی طبق ترتیب/وضعیت ذخیره‌شده
 */
function uid_render_home_sections() {
	foreach ( uid_get_home_layout() as $row ) {
		if ( empty( $row['enabled'] ) ) continue;
		$fn = 'uid_render_section_' . $row['slug'];
		if ( function_exists( $fn ) ) {
			call_user_func( $fn );
		}
	}
}

/* =====================================================================
 * هیرو
 * ===================================================================== */
function uid_render_section_hero() {
	$tag = uid_section_tag( 'hero', 'h1' );
	?>
	<section class="hero">
	  <div class="hero-grid-texture"></div>
	  <div class="wrap">
	    <div class="hero-copy">
	      <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'hero', 'eyebrow', __( 'پلتفرم احراز هویت دیجیتال', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'hero', 'heading', __( 'زیرساخت احراز هویتی که بانک‌ها، صرافی‌ها و فین‌تک‌ها در ایران به آن اعتماد دارند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'hero', 'text', __( 'با اپلیکیشن اختصاصی و API یکپارچه یوآیدی، فرآیند احراز هویت مشتریان را در چند ثانیه، مطابق با استانداردهای امنیتی و بدون پیچیدگی فنی انجام دهید.', 'uid-theme' ) ) ); ?></p>
	      <div class="hero-actions">
	        <a href="<?php echo esc_url( uid_section_val( 'hero', 'btn1_url', '#contact' ) ); ?>" class="btn btn-primary js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو سازمانی', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo esc_html( uid_section_val( 'hero', 'btn1_text', __( 'برای کسب‌وکارها — درخواست دمو', 'uid-theme' ) ) ); ?></a>
	        <a href="<?php echo esc_url( uid_section_val( 'hero', 'btn2_url', '#services' ) ); ?>" class="btn btn-navy"><?php echo esc_html( uid_section_val( 'hero', 'btn2_text', __( 'برای افراد — دریافت خدمت ثنا', 'uid-theme' ) ) ); ?></a>
	      </div>
	    </div>
	    <div class="hero-art">
	      <?php
	      $hero_visual_id = absint( uid_section_val( 'hero', 'visual_id', 0 ) );
	      if ( $hero_visual_id ) :
	      	$hero_visual_alt = uid_section_val( 'hero', 'visual_alt', '' );
	      	echo wp_get_attachment_image( $hero_visual_id, 'large', false, array( 'alt' => $hero_visual_alt, 'class' => 'uid-visual-img' ) );
	      else :
	      	?>
	      <div class="ring"></div>
	      <div class="ring2"></div>
	      <div class="core-orb"></div>
	      <div class="hero-card seal-card">
	        <div class="seal-ring">
	          <svg class="seal-dash" viewBox="0 0 38 38" fill="none"><circle cx="19" cy="19" r="17" stroke="var(--orange-500)" stroke-width="2" stroke-dasharray="4 4"/></svg>
	          <div class="seal-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg></div>
	        </div>
	        <div><b><?php bloginfo( 'name' ); ?></b><span><?php esc_html_e( 'هویت تایید شده', 'uid-theme' ); ?></span></div>
	      </div>
	      <div class="hero-card verify-card">
	        <div class="vf-head"><span class="vf-dot"></span><span id="vfStatusLabel"><?php esc_html_e( 'در حال اسکن مدرک', 'uid-theme' ); ?></span></div>
	        <div class="vf-body">
	          <div class="vf-state active" data-state="0"><div class="vf-doc"><i></i><i></i><i></i><div class="vf-beam"></div></div><span class="vf-label"><?php esc_html_e( 'تحلیل سند هویتی...', 'uid-theme' ); ?></span></div>
	          <div class="vf-state" data-state="1"><div class="vf-face-ring"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="9" r="3.2"/><path d="M6 19c0-3.3 2.7-5 6-5s6 1.7 6 5"/></svg></div><span class="vf-label"><?php esc_html_e( 'تطبیق چهره در حال انجام...', 'uid-theme' ); ?></span></div>
	          <div class="vf-state" data-state="2"><div class="vf-check-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg></div><span class="vf-label"><?php esc_html_e( 'هویت با موفقیت تایید شد', 'uid-theme' ); ?></span></div>
	        </div>
	      </div>
	      <div class="hero-card ticker-card">
	        <div class="ticker-row"><div class="ticker-dot-wrap"><i></i><i class="ping"></i></div><span class="ticker-text" id="tickerText"><?php esc_html_e( 'کاربری در تهران هویت خود را تایید کرد', 'uid-theme' ); ?></span></div>
	        <div class="ticker-note"><?php esc_html_e( 'نمونه‌ای از رویدادهای احراز هویت', 'uid-theme' ); ?></div>
	      </div>
	      <?php endif; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مسیر کاربران (کسب‌وکار / شخص) — عنوان و مقدمه از تنظیمات؛ محتوای دو کارت فعلاً ثابت است
 * =====================================================================*/
function uid_render_section_audience() {
	$tag = uid_section_tag( 'audience', 'h2' );
	?>
	<section class="audience" id="audience">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'audience', 'eyebrow', __( 'مسیر خود را انتخاب کنید', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'audience', 'heading', __( 'یوآیدی برای دو نوع مخاطب متفاوت کار می‌کند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'audience', 'text', __( 'چه به‌دنبال یکپارچه‌سازی احراز هویت در سازمان خود باشید، چه نیاز به تکمیل احراز هویت شخصی از طریق ثنا دارید.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="audience-grid">
	      <div class="audience-card biz">
	        <div class="icon-plate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></div>
	        <h3><?php esc_html_e( 'برای کسب‌وکارها', 'uid-theme' ); ?></h3>
	        <p><?php esc_html_e( 'برای بانک‌ها، صرافی‌ها و فین‌تک‌ها — از احراز هویت یکپارچه گرفته تا وب‌سرویس‌های استعلامی سازمانی.', 'uid-theme' ); ?></p>
	        <div class="audience-links">
	          <a href="#pwa" class="al-item">
	            <span class="al-icon" style="background:var(--orange-500);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg></span>
	            <span><b><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></b><span><?php esc_html_e( 'احراز هویت یکپارچه تحت وب — نرخ تکمیل ۳٫۱ برابر بالاتر', 'uid-theme' ); ?></span></span>
	            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 6l-6 6 6 6"/></svg>
	          </a>
	          <a href="#services" class="al-item">
	            <span class="al-icon" style="background:var(--navy-900);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>
	            <span><b><?php esc_html_e( 'وب‌سرویس‌های سازمانی', 'uid-theme' ); ?></b><span><?php esc_html_e( 'شاهکار، ثبت‌احوال، استعلام شبا و کارت، و بیشتر', 'uid-theme' ); ?></span></span>
	            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 6l-6 6 6 6"/></svg>
	          </a>
	        </div>
	        <a href="#contact" class="btn btn-navy js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو سازمانی', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'اطلاعات سازمان خود را ثبت کنید تا کارشناسان یوآیدی با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php esc_html_e( 'درخواست دمو سازمانی', 'uid-theme' ); ?></a>
	      </div>
	      <div class="audience-card person">
	        <div class="icon-plate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg></div>
	        <h3><?php esc_html_e( 'برای افراد', 'uid-theme' ); ?></h3>
	        <p><?php esc_html_e( 'اگر بانک، صرافی یا کارفرمای شما از سامانه ثنا برای احراز هویت شما استفاده می‌کند، اینجا شروع کنید.', 'uid-theme' ); ?></p>
	        <div class="audience-links">
	          <a href="#services" class="al-item">
	            <span class="al-icon" style="background:var(--navy-900);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>
	            <span><b><?php esc_html_e( 'سامانه ثنا', 'uid-theme' ); ?></b><span><?php esc_html_e( 'احراز هویت غیرحضوری در کمتر از چند دقیقه', 'uid-theme' ); ?></span></span>
	            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 6l-6 6 6 6"/></svg>
	          </a>
	          <a href="#" class="al-item">
	            <span class="al-icon" style="background:var(--orange-500);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M2 12h20M12 2c2.5 2.7 4 6.2 4 10s-1.5 7.3-4 10c-2.5-2.7-4-6.2-4-10s1.5-7.3 4-10z"/></svg></span>
	            <span><b><?php esc_html_e( 'ثنا ایرانیان خارج از کشور', 'uid-theme' ); ?></b><span><?php esc_html_e( 'ثبت‌نام ویژه هموطنان مقیم خارج از کشور', 'uid-theme' ); ?></span></span>
	            <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 6l-6 6 6 6"/></svg>
	          </a>
	        </div>
	        <a href="#services" class="btn btn-outline"><?php esc_html_e( 'مشاهده همه خدمات ثنا', 'uid-theme' ); ?></a>
	      </div>
	    </div>
	  </div>
	</section>
	<div class="perforated-divider" aria-hidden="true"></div>
	<?php
}

/* =====================================================================
 * برندهای همکار — هر آیتم می‌تواند لوگوی واقعی (آپلودی) داشته باشد؛
 * در نبود لوگو، آواتار حرف اول با رنگ چرخشی نمایش داده می‌شود.
 * =====================================================================*/
function uid_default_brands() {
	return array(
		array( 'name' => 'دیوار', 'type' => 'پلتفرم آگهی و نیازمندی‌های آنلاین', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'اکسکوینو', 'type' => 'صرافی ارز دیجیتال', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'رمزینکس', 'type' => 'صرافی ارز دیجیتال', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'تبدیل', 'type' => 'صرافی ارز دیجیتال', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'اکسیر', 'type' => 'صرافی ارز دیجیتال', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'آبان‌تتر', 'type' => 'صرافی ارز دیجیتال', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'باشگاه استقلال', 'type' => 'باشگاه ورزشی', 'image_id' => 0, 'image_alt' => '' ),
		array( 'name' => 'بانک گردشگری', 'type' => 'بانک', 'image_id' => 0, 'image_alt' => '' ),
	);
}

function uid_render_section_brands() {
	$tag    = uid_section_tag( 'brands', 'h2' );
	$colors = array( 'var(--navy-900)', 'var(--teal-500)', 'var(--orange-500)', 'var(--navy-700)' );
	$brands = uid_section_val( 'brands', 'items', uid_default_brands() );
	if ( ! is_array( $brands ) || empty( $brands ) ) return;
	?>
	<section class="brands-section" id="brands">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'brands', 'eyebrow', __( 'اعتماد برندهای معتبر', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'brands', 'heading', __( 'مورد اعتماد و همکاری برندهای پیشرو ایران', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'brands', 'text', __( 'تیم‌های ریسک و فناوری در بانک‌ها، صرافی‌ها، فین‌تک‌ها و نهادهای مالی معتبر، از یوآیدی برای احراز هویت مشتریان خود استفاده می‌کنند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="brands-marquee-wrap reveal">
	      <div class="brands-marquee-track" id="brandsGrid">
	        <?php
	        // برای افکت اسکرول بی‌پایان، لیست دوبار چاپ می‌شود
	        for ( $pass = 0; $pass < 2; $pass++ ) :
	        	foreach ( $brands as $i => $b ) :
	        		$color   = $colors[ $i % count( $colors ) ];
	        		$img_id  = ! empty( $b['image_id'] ) ? absint( $b['image_id'] ) : 0;
	        		?>
	        		<div class="brand-card"<?php echo $pass ? ' aria-hidden="true"' : ''; ?>>
	        			<?php if ( $img_id ) :
	        				$b_alt = ! empty( $b['image_alt'] ) ? $b['image_alt'] : $b['name'];
	        				?>
	        				<div class="mark uid-brand-logo"><?php echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => $b_alt ) ); ?></div>
	        			<?php else : ?>
	        				<div class="mark" style="background:<?php echo esc_attr( $color ); ?>;"><span style="color:#fff; font-weight:700; font-size:15px;"><?php echo esc_html( mb_substr( $b['name'], 0, 1 ) ); ?></span></div>
	        			<?php endif; ?>
	        			<span style="display:flex; flex-direction:column;"><span class="name"><?php echo esc_html( $b['name'] ); ?></span><span class="type"><?php echo esc_html( $b['type'] ); ?></span></span>
	        		</div>
	        		<?php
	        	endforeach;
	        endfor;
	        ?>
	      </div>
	    </div>
	    <?php $brands_note = uid_section_val( 'brands', 'note', __( '* نشان‌های بالا آیکون‌های نمونه‌اند و باید پیش از انتشار با لوگوی رسمی هر برند جایگزین شوند — نام‌ها واقعی و از فهرست شرکای یوآیدی هستند.', 'uid-theme' ) ); ?>
	    <?php if ( $brands_note ) : ?>
	    <p class="placeholder-flag" style="text-align:center; margin-top:24px;"><?php echo esc_html( $brands_note ); ?></p>
	    <?php endif; ?>
	  </div>
	</section>
	<?php
}

/**
 * آیکون پیش‌فرض وقتی برای یک ردیف تصویری آپلود نشده (چک‌مارک ساده در یک دایره)
 */
function uid_default_icon_svg() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>';
}

/* =====================================================================
 * آمار در یک نگاه (Stat Band)
 * ===================================================================== */
function uid_default_stats() {
	return array(
		array( 'image_id' => 0, 'value' => '5000000', 'suffix' => '+', 'decimals' => '0', 'label' => 'احراز هویت موفق تاکنون' ),
		array( 'image_id' => 0, 'value' => '60000', 'suffix' => '+', 'decimals' => '0', 'label' => 'احراز هویت در پربازدیدترین روز' ),
		array( 'image_id' => 0, 'value' => '99.5', 'suffix' => '%', 'decimals' => '1', 'label' => 'SLA تعهد آپ‌تایم' ),
		array( 'image_id' => 0, 'value' => '99', 'suffix' => '%', 'decimals' => '0', 'label' => 'دقت تطبیق چهره با هوش مصنوعی' ),
	);
}

function uid_render_section_stats() {
	$tag   = uid_section_tag( 'stats', 'h2' );
	$items = uid_section_val( 'stats', 'items', uid_default_stats() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	$groups = array( 'g1', 'g2', 'g3', 'g4' );
	?>
	<section class="stat-band">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'stats', 'eyebrow', __( 'در یک نگاه', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'stats', 'heading', __( 'عددها گویای همه‌چیز هستند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	    </div>
	    <div class="stat-band-grid reveal">
	      <?php foreach ( $items as $i => $stat ) :
	      	$img_id = ! empty( $stat['image_id'] ) ? absint( $stat['image_id'] ) : 0;
	      	?>
	      	<div class="stat-card <?php echo esc_attr( $groups[ $i % count( $groups ) ] ); ?>">
	      	  <div class="stat-chip">
	      	    <?php if ( $img_id ) : ?>
	      	    	<?php echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'class' => 'stat-icon' ) ); ?>
	      	    <?php else : ?>
	      	    	<?php echo uid_default_icon_svg(); ?>
	      	    <?php endif; ?>
	      	  </div>
	      	  <b class="count-up" data-target="<?php echo esc_attr( $stat['value'] ?? '0' ); ?>" data-decimals="<?php echo esc_attr( $stat['decimals'] ?? '0' ); ?>" data-suffix="<?php echo esc_attr( $stat['suffix'] ?? '' ); ?>">۰</b>
	      	  <span><?php echo esc_html( $stat['label'] ?? '' ); ?></span>
	      	</div>
	      <?php endforeach; ?>
	    </div>
	    <p class="stat-note" style="text-align:center; margin-top:24px; font-size:12.5px; color:var(--neutral-300);"><?php echo esc_html( uid_section_val( 'stats', 'note', __( 'آمار بر اساس داده‌های واقعی معرفی‌نامه محصول یوآیدی‌پلاس.', 'uid-theme' ) ) ); ?></p>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * خدمات (کارت‌های ویژه Spotlight)
 * ===================================================================== */
function uid_default_services() {
	return array(
		array( 'image_id' => 0, 'color' => 'navy', 'tag' => 'تشخیص چهره زنده', 'title' => 'احراز هویت تصویری', 'desc' => 'احراز هویت زنده کاربر از طریق ویدئوی سلفی و تطبیق هوش مصنوعی با تصویر مرجع ثبت‌احوال — بدون نیاز به بارگذاری مدرک.', 'bullets' => "دقت ۹۹٪ در تطبیق چهره\nتشخیص زنده بودن در برابر جعل", 'link_text' => 'مشاهده جزئیات', 'link_url' => '#pwa' ),
		array( 'image_id' => 0, 'color' => 'orange', 'tag' => 'محصول ویژه', 'title' => 'یوآیدی‌پلاس', 'desc' => 'احراز هویت یکپارچه تحت وب با نرخ تکمیل تا ۳٫۱ برابر بالاتر از روش‌های مرحله‌ای API.', 'bullets' => "بدون نیاز به توسعه فنی\nپرداخت فقط برای احراز خاتمه‌یافته", 'link_text' => 'مشاهده جزئیات', 'link_url' => '#pwa' ),
		array( 'image_id' => 0, 'color' => 'teal', 'tag' => 'یکپارچه‌سازی API', 'title' => 'وب‌سرویس‌های استعلامی', 'desc' => 'مجموعه کامل وب‌سرویس‌های استعلام هویتی، بانکی و ثبت‌احوال برای یکپارچه‌سازی مستقیم در سامانه شما.', 'bullets' => "پاسخ در کمتر از ۲ ثانیه\nمستندات کامل API", 'link_text' => 'مشاهده جزئیات', 'link_url' => '#webservices' ),
	);
}

/**
 * آیکون تزئینی ردیف «وب‌سرویس‌های سازمانی» — بر اساس ترتیب، عیناً مطابق نمونه HTML
 */
function uid_reseller_icon_svg( $i ) {
	$icons = array(
		'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
		'<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
		'<path d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 8h5M8 12h8M8 16h5"/>',
		'<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
		'<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h4M7 14h7"/>',
	);
	return $icons[ $i % count( $icons ) ];
}

function uid_render_section_services() {
	$tag    = uid_section_tag( 'services', 'h2' );
	$items  = uid_section_val( 'services', 'items', uid_default_services() );
	$colors = array( 'navy' => 'var(--navy-900)', 'orange' => 'var(--orange-500)', 'teal' => 'var(--teal-500)' );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section id="services">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'services', 'eyebrow', __( 'خدمات یوآیدی', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'services', 'heading', __( 'خدمات احراز هویت و استعلام', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'services', 'text', __( 'سه خدمت اصلی یوآیدی برای احراز هویت غیرحضوری و یکپارچه‌سازی سازمانی.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="services-spotlight-grid reveal">
	      <?php foreach ( $items as $item ) :
	      	$color  = $colors[ $item['color'] ?? 'navy' ] ?? $colors['navy'];
	      	$img_id = ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : 0;
	      	$bullets = array_filter( array_map( 'trim', explode( "\n", $item['bullets'] ?? '' ) ) );
	      	?>
	      	<div class="spotlight-card">
	      	  <div class="spotlight-head" style="background:<?php echo esc_attr( $color ); ?>;">
	      	    <span class="spotlight-tag"><?php echo esc_html( $item['tag'] ?? '' ); ?></span>
	      	    <?php if ( $img_id ) : ?>
	      	    	<?php echo wp_get_attachment_image( $img_id, 'thumbnail' ); ?>
	      	    <?php else : ?>
	      	    	<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg>
	      	    <?php endif; ?>
	      	  </div>
	      	  <div class="spotlight-body">
	      	    <h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4>
	      	    <p><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
	      	    <?php if ( $bullets ) : ?>
	      	    <ul>
	      	      <?php foreach ( $bullets as $b ) : ?>
	      	      	<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( $b ); ?></li>
	      	      <?php endforeach; ?>
	      	    </ul>
	      	    <?php endif; ?>
	      	    <a href="<?php echo esc_url( $item['link_url'] ?? '#' ); ?>" class="spotlight-link"><?php echo esc_html( $item['link_text'] ?? __( 'مشاهده جزئیات', 'uid-theme' ) ); ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5l-7 7 7 7"/></svg></a>
	      	  </div>
	      	</div>
	      <?php endforeach; ?>
	    </div>

	    <?php
	    $reseller_label = uid_section_val( 'services', 'reseller_label', __( 'وب‌سرویس‌های سازمانی:', 'uid-theme' ) );
	    $reseller_items = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'services', 'reseller_items', "شاهکار\nثبت احوال\nآدرس\nاستعلام شبا\nتطابق کارت\nتبدیل کارت به شبا" ) ) ) );
	    if ( $reseller_items ) : ?>
	    <div class="reveal" style="margin-top:32px;" id="webservices">
	      <div class="reseller-line">
	        <?php if ( $reseller_label ) : ?><span class="rl-label"><?php echo esc_html( $reseller_label ); ?></span><?php endif; ?>
	        <?php foreach ( $reseller_items as $i => $ri ) : ?>
	        	<a href="#" class="rl-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo uid_reseller_icon_svg( $i ); ?></svg><?php echo esc_html( $ri ); ?></a>
	        <?php endforeach; ?>
	      </div>
	    </div>
	    <?php endif; ?>

	    <div class="services-footer">
	      <a href="<?php echo esc_url( uid_section_val( 'services', 'footer_button_url', '#contact' ) ); ?>" class="btn btn-navy js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'نوع خدمت مورد نیاز خود را انتخاب کنید تا کارشناسان ما با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo esc_html( uid_section_val( 'services', 'footer_button_text', __( 'درخواست دمو', 'uid-theme' ) ) ); ?></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مزیت رقابتی (Bento Grid)
 * ===================================================================== */
function uid_default_advantages() {
	return array(
		array( 'image_id' => 0, 'color' => 'navy',   'title' => 'پوشش کامل سامانه‌های رسمی کشور', 'desc' => 'یوآیدی تنها پلتفرمی است که ثنا، شاهکار و هویتی را در یک API واحد کنار هم پوشش می‌دهد — بدون نیاز به چند قرارداد و چند یکپارچه‌سازی جدا.' ),
		array( 'image_id' => 0, 'color' => 'teal',   'title' => 'پاسخ‌دهی زیر ۲ ثانیه', 'desc' => 'زیرساخت ابری مقیاس‌پذیر برای ترافیک بالا در ساعات اوج.' ),
		array( 'image_id' => 0, 'color' => 'navy2',  'title' => 'پشتیبانی ۲۴/۷ فارسی', 'desc' => 'پاسخ‌گویی مستقیم تیم فنی.' ),
		array( 'image_id' => 0, 'color' => 'orange', 'title' => 'امنیت end-to-end', 'desc' => 'رمزنگاری کامل داده‌ها.' ),
		array( 'image_id' => 0, 'color' => 'navy2',  'title' => 'انطباق با مقررات بانک مرکزی و ثبت احوال', 'desc' => 'فرآیندها مطابق با آخرین بخشنامه‌های نظارتی به‌روزرسانی می‌شوند.' ),
		array( 'image_id' => 0, 'color' => 'navy',   'title' => 'SDK و مستندات آماده برای توسعه‌دهندگان', 'desc' => 'یکپارچه‌سازی در کم‌تر از یک روز کاری با نمونه‌کدهای آماده.' ),
	);
}

function uid_render_section_advantages() {
	$tag       = uid_section_tag( 'advantages', 'h2' );
	$items     = uid_section_val( 'advantages', 'items', uid_default_advantages() );
	$positions = array( 'bento-a', 'bento-b', 'bento-c', 'bento-d', 'bento-e', 'bento-f' );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section id="advantages">
	  <div class="wrap">
	    <div class="section-head reveal">
	      <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'advantages', 'eyebrow', __( 'مزیت رقابتی', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'advantages', 'heading', __( 'چرا سازمان‌ها یوآیدی را انتخاب می‌کنند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'advantages', 'text', __( 'پوشش کامل سامانه‌های رسمی کشور در کنار سرعت، امنیت و پشتیبانی که تیم‌های ریسک به آن نیاز دارند.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="bento-grid reveal">
	      <?php foreach ( $items as $i => $item ) :
	      	$img_id = ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : 0;
	      	$pos    = $positions[ $i % count( $positions ) ];
	      	$bg     = 'bg-' . ( $item['color'] ?? 'navy' );
	      	?>
	      	<div class="bento-item <?php echo esc_attr( $bg . ' ' . $pos ); ?>">
	      	  <?php if ( $img_id ) : ?>
	      	  	<?php echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'class' => 'b-icon' ) ); ?>
	      	  <?php else : ?>
	      	  	<?php echo uid_default_icon_svg(); ?>
	      	  <?php endif; ?>
	      	  <h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4>
	      	  <p><?php echo esc_html( $item['desc'] ?? '' ); ?></p>
	      	</div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * چرا یوآیدی؟ (متن + نمودار تزئینی ثابت)
 * ===================================================================== */
function uid_render_section_why() {
	$tag = uid_section_tag( 'why', 'h2' );
	?>
	<section class="why" id="why">
	  <div class="wrap">
	    <div class="why-art">
	      <?php
	      $why_visual_id = absint( uid_section_val( 'why', 'visual_id', 0 ) );
	      if ( $why_visual_id ) :
	      	$why_visual_alt = uid_section_val( 'why', 'visual_alt', '' );
	      	echo wp_get_attachment_image( $why_visual_id, 'large', false, array( 'alt' => $why_visual_alt, 'class' => 'uid-visual-img' ) );
	      else :
	      	?>
	      <div class="panel">
	        <b style="font-size:13px; font-weight:700; color:var(--neutral-600);"><?php esc_html_e( 'درخواست‌های احراز هویت', 'uid-theme' ); ?></b>
	        <div class="bars">
	          <i style="height:35%; background:var(--navy-050);"></i>
	          <i style="height:55%; background:var(--teal-500); opacity:.4;"></i>
	          <i style="height:70%; background:var(--navy-900);"></i>
	          <i style="height:50%; background:var(--teal-500); opacity:.4;"></i>
	          <i style="height:85%; background:var(--orange-500);"></i>
	          <i style="height:65%; background:var(--navy-900);"></i>
	          <i style="height:95%; background:var(--navy-900);"></i>
	        </div>
	        <div class="doc-lines">
	          <div class="row"><div style="width:24px;"></div><div style="flex:1;"></div></div>
	          <div class="row"><div style="width:24px;"></div><div style="flex:.6;"></div></div>
	        </div>
	      </div>
	      <div class="float-card">
	        <b>&lt; ۲ ثانیه</b>
	        <span><?php esc_html_e( 'میانگین زمان پاسخ API', 'uid-theme' ); ?></span>
	      </div>
	      <?php endif; ?>
	    </div>
	    <div class="why-copy">
	      <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'why', 'eyebrow', __( 'چرا یوآیدی؟', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'why', 'heading', __( 'دقتی که تیم ریسک به آن نیاز دارد، سرعتی که مشتری انتظار دارد', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'why', 'text', __( 'یوآیدی زیرساخت احراز هویت را برای تیم‌های ریسک، فنی و محصول در کنار هم قابل اتکا می‌کند — با گزارش‌های شفاف و پشتیبانی مستقیم.', 'uid-theme' ) ) ); ?></p>
	      <a href="<?php echo esc_url( uid_section_val( 'why', 'button_url', '#contact' ) ); ?>" class="btn btn-navy js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo esc_html( uid_section_val( 'why', 'button_text', __( 'درخواست دمو', 'uid-theme' ) ) ); ?></a>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * راه‌حل به تفکیک صنعت (تب‌های Persona)
 * ===================================================================== */
function uid_help_tab_icon_svg( $key ) {
	$icons = array(
		'bank'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="10" width="18" height="9" rx="1"/><path d="M3 9l9-6 9 6"/></svg>',
		'phone'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M10 18h4"/></svg>',
		'crypto'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>',
		'handshake' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
		'credit'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
		'cart'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 6h15l-1.5 9h-12z"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>',
	);
	return $icons[ $key ] ?? $icons['bank'];
}

function uid_help_mini_stat_icon_svg( $key ) {
	$icons = array(
		'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>',
		'clock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
		'bolt'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L4 14h6l-1 8 9-12h-6z"/></svg>',
		'person'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>',
		'grid'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 8h5M8 12h8M8 16h5"/></svg>',
		'card'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
		'check'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>',
	);
	return $icons[ $key ] ?? $icons['check'];
}

function uid_default_help_tabs() {
	return array(
		array(
			'tab_label' => 'بانکداری و خدمات مالی', 'icon' => 'bank', 'color' => 'navy',
			'visual_title' => 'راه‌حل بانکی یوآیدی', 'visual_subtitle' => 'افتتاح حساب و خدمات غیرحضوری',
			'eyebrow' => 'بانکداری و خدمات مالی', 'heading' => 'احراز هویت مشتری بدون مراجعه حضوری شعبه',
			'text'    => 'افتتاح حساب و صدور خدمات غیرحضوری با احراز هویت مطابق ضوابط بانک مرکزی — بدون افزایش بار مراجعه حضوری شعب.',
			'bullets' => "اتصال مستقیم به سامانه‌های ثنا و شاهکار\nگزارش انطباق آماده برای واحد ریسک و حسابرسی\nSLA اختصاصی برای سازمان‌های بزرگ",
			'stat1_title' => 'کاهش مراجعه حضوری شعب', 'stat1_desc' => 'برای فرآیندهای افتتاح حساب غیرحضوری',
			'stat2_title' => 'گزارش انطباق شفاف', 'stat2_desc' => 'آماده برای بازرسی و حسابرسی داخلی',
			'badges'  => "مطابق با بانک مرکزی\nاتصال به ثبت احوال\nSLA اختصاصی",
		),
		array(
			'tab_label' => 'اپراتورهای تلفن همراه', 'icon' => 'phone', 'color' => 'teal',
			'visual_title' => 'راه‌حل اپراتوری یوآیدی', 'visual_subtitle' => 'تطبیق مشترک، بدون مراجعه به دفاتر',
			'eyebrow' => 'اپراتورهای تلفن همراه', 'heading' => 'ثبت‌نام و انتقال سیم‌کارت، بدون مراجعه به دفاتر',
			'text'    => 'صدور، تعویض و انتقال سیم‌کارت نیاز به تطبیق دقیق هویت مشترک با سامانه‌های رسمی کشور دارد — یوآیدی این تطبیق را در چند ثانیه انجام می‌دهد.',
			'bullets' => "تطبیق آنی مالکیت خط با کد ملی از طریق شاهکار\nجلوگیری از سوءاستفاده در انتقال و ثبت مجدد سیم‌کارت\nمقیاس‌پذیر برای میلیون‌ها استعلام ماهانه",
			'stat1_title' => 'پاسخ در کمتر از ۲ ثانیه', 'stat1_desc' => 'برای استعلام آنی شاهکار',
			'stat2_title' => 'کاهش مراجعه حضوری دفاتر', 'stat2_desc' => 'برای ثبت و انتقال سیم‌کارت',
			'badges'  => "اتصال به شاهکار\nمقیاس میلیونی\nپاسخ آنی",
		),
		array(
			'tab_label' => 'صرافی‌های رمزارز', 'icon' => 'crypto', 'color' => 'orange',
			'visual_title' => 'راه‌حل صرافی یوآیدی', 'visual_subtitle' => 'KYC سریع، بدون افت تبدیل کاربر',
			'eyebrow' => 'صرافی‌های رمزارز', 'heading' => 'ثبت‌نام سریع، بدون افت نرخ تبدیل کاربر',
			'text'    => 'هر ثانیه تاخیر در KYC یعنی از دست‌دادن کاربر. یوآیدی فرآیند ثبت‌نام را کوتاه و امن نگه می‌دارد.',
			'bullets' => "تشخیص زنده بودن چهره در برابر جعل هویت\nAPI سبک، مناسب اپلیکیشن‌های موبایل\nمقیاس‌پذیر برای رشد ناگهانی ترافیک",
			'stat1_title' => 'ثبت‌نام سریع‌تر', 'stat1_desc' => 'با کاهش گام‌های احراز هویت',
			'stat2_title' => 'کاهش جعل هویت', 'stat2_desc' => 'با تشخیص زنده بودن چهره',
			'badges'  => "Liveness Detection\nAPI سبک موبایل\nمقیاس‌پذیر",
		),
		array(
			'tab_label' => 'پلتفرم‌های اقتصاد مشارکتی', 'icon' => 'handshake', 'color' => 'navy2',
			'visual_title' => 'راه‌حل اقتصاد مشارکتی یوآیدی', 'visual_subtitle' => 'اعتماد دوطرفه در بستر پلتفرم',
			'eyebrow' => 'پلتفرم‌های اقتصاد مشارکتی', 'heading' => 'اعتمادسازی بین کاربران، بدون احراز هویت پیچیده',
			'text'    => 'در پلتفرم‌های اقتصاد مشارکتی، امنیت دو طرف تراکنش — راننده و مسافر، میزبان و مهمان — به احراز هویت سریع و قابل‌اتکا وابسته است.',
			'bullets' => "احراز هویت راننده یا میزبان پیش از فعال‌سازی حساب\nتطبیق چهره برای جلوگیری از جعل پروفایل\nافزایش اعتماد طرفین تراکنش با نشان احراز هویت",
			'stat1_title' => 'تشخیص زنده بودن چهره', 'stat1_desc' => 'جلوگیری از پروفایل جعلی',
			'stat2_title' => 'افزایش نرخ فعال‌سازی حساب', 'stat2_desc' => 'با فرآیند احراز سریع‌تر',
			'badges'  => "Liveness Detection\nاعتماد دوطرفه\nفعال‌سازی سریع",
		),
		array(
			'tab_label' => 'لندتک‌ها', 'icon' => 'credit', 'color' => 'navy',
			'visual_title' => 'راه‌حل لندتک یوآیدی', 'visual_subtitle' => 'احراز هویت پیش از اعتبارسنجی',
			'eyebrow' => 'لندتک‌ها', 'heading' => 'اعتبارسنجی متقاضی وام، پیش از تحلیل اعتباری',
			'text'    => 'پیش از هر تحلیل اعتبارسنجی، لندتک‌ها باید مطمئن شوند متقاضی همان فردی است که ادعا می‌کند — یوآیدی این لایه اول را با اتکا به منابع رسمی کشور فراهم می‌کند.',
			'bullets' => "تطبیق هویت متقاضی با کد ملی و اطلاعات بانکی\nاستعلام شبا و مالکیت حساب پیش از پرداخت تسهیلات\nگزارش قابل‌استناد برای فرآیند اعتبارسنجی",
			'stat1_title' => 'تطبیق مالکیت حساب', 'stat1_desc' => 'از طریق استعلام شبا',
			'stat2_title' => 'کاهش ریسک تقلب در وام‌دهی', 'stat2_desc' => 'با احراز هویت چندلایه',
			'badges'  => "استعلام شبا\nتطبیق بانکی\nگزارش قابل‌استناد",
		),
		array(
			'tab_label' => 'کسب‌وکارهای تجارت الکترونیک', 'icon' => 'cart', 'color' => 'teal',
			'visual_title' => 'راه‌حل تجارت الکترونیک یوآیدی', 'visual_subtitle' => 'اعتبارسنجی فروشندگان بازارگاه',
			'eyebrow' => 'کسب‌وکارهای تجارت الکترونیک', 'heading' => 'احراز هویت فروشندگان و کاهش تقلب در بازارگاه',
			'text'    => 'در مارکت‌پلیس‌ها و فروشگاه‌های آنلاین، احراز هویت فروشنده پیش از فعال‌سازی غرفه، ریسک تقلب و مرجوعی‌های ناشی از هویت جعلی را کاهش می‌دهد.',
			'bullets' => "احراز هویت فروشندگان پیش از فعال‌سازی غرفه\nتطبیق اطلاعات بانکی برای واریز درست وجوه\nکاهش تقلب و شکایات ناشی از هویت نامعتبر",
			'stat1_title' => 'کاهش ریسک تقلب', 'stat1_desc' => 'با احراز هویت فروشنده',
			'stat2_title' => 'تطبیق بانکی دقیق', 'stat2_desc' => 'پیش از واریز وجوه',
			'badges'  => "احراز فروشنده\nتطبیق بانکی\nکاهش مرجوعی",
		),
	);
}

function uid_render_section_help() {
	$tag     = uid_section_tag( 'help', 'h2' );
	$tabs    = uid_section_val( 'help', 'tabs', uid_default_help_tabs() );
	$colors  = array( 'navy' => 'var(--navy-900)', 'teal' => 'var(--teal-500)', 'orange' => 'var(--orange-500)', 'navy2' => 'var(--navy-700)' );
	$mini    = array(
		array( 'briefcase', 'check' ), array( 'clock', 'briefcase' ), array( 'bolt', 'person' ),
		array( 'person', 'check' ), array( 'grid', 'check' ), array( 'check', 'card' ),
	);
	// آیکون پیش‌فرض هر تب اگر ادمین تصویر آپلود نکرده باشد (بر اساس ترتیب، نه یک فیلد قابل‌ویرایش)
	$default_icon_keys = array( 'bank', 'phone', 'crypto', 'handshake', 'credit', 'cart' );
	if ( ! is_array( $tabs ) || empty( $tabs ) ) return;
	?>
	<section style="background:var(--neutral-050);" id="help">
	  <div class="wrap">
	    <div class="section-head reveal">
	      <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'help', 'eyebrow', __( 'راه‌حل به تفکیک نوع کسب‌وکار', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'help', 'heading', __( 'چطور به کسب‌وکار شما کمک می‌کنیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'help', 'text', __( 'نیاز یک بانک با نیاز یک صرافی یا یک استارتاپ فین‌تک یکسان نیست — راه‌حل ما هم نیست.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="tabs-nav reveal" style="justify-content:center;">
	      <?php foreach ( $tabs as $i => $t ) : ?>
	        <button class="tab-btn<?php echo 0 === $i ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $t['tab_label'] ?? '' ); ?></button>
	      <?php endforeach; ?>
	    </div>

	    <?php foreach ( $tabs as $i => $t ) :
	    	$color   = $colors[ $t['color'] ?? 'navy' ] ?? $colors['navy'];
	    	$bullets = array_filter( array_map( 'trim', explode( "\n", $t['bullets'] ?? '' ) ) );
	    	$badges  = array_filter( array_map( 'trim', explode( "\n", $t['badges'] ?? '' ) ) );
	    	$mi      = $mini[ $i % count( $mini ) ];
	    	?>
	    <div class="tab-panel<?php echo 0 === $i ? ' active' : ''; ?> reveal" data-panel="<?php echo esc_attr( $i ); ?>">
	      <div class="tab-copy">
	        <div class="eyebrow"><span class="dot"></span><?php echo esc_html( $t['eyebrow'] ?? '' ); ?></div>
	        <h3><?php echo esc_html( $t['heading'] ?? '' ); ?></h3>
	        <p><?php echo esc_html( $t['text'] ?? '' ); ?></p>
	        <?php if ( $bullets ) : ?>
	        <ul>
	          <?php foreach ( $bullets as $b ) : ?>
	          	<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( $b ); ?></li>
	          <?php endforeach; ?>
	        </ul>
	        <?php endif; ?>
	      </div>
	      <div class="tab-visual">
	        <div class="tab-visual-header">
	          <div class="icon-circle" style="background:<?php echo esc_attr( $color ); ?>;">
	            <?php
	            $icon_img_id = ! empty( $t['icon_id'] ) ? absint( $t['icon_id'] ) : 0;
	            if ( $icon_img_id ) :
	            	$icon_alt = ! empty( $t['icon_alt'] ) ? $t['icon_alt'] : ( $t['tab_label'] ?? '' );
	            	echo wp_get_attachment_image( $icon_img_id, 'thumbnail', false, array( 'alt' => $icon_alt, 'class' => 'uid-icon-img' ) );
	            else :
	            	echo uid_help_tab_icon_svg( $default_icon_keys[ $i % count( $default_icon_keys ) ] );
	            endif;
	            ?>
	          </div>
	          <div><b><?php echo esc_html( $t['visual_title'] ?? '' ); ?></b><span><?php echo esc_html( $t['visual_subtitle'] ?? '' ); ?></span></div>
	        </div>
	        <div class="mini-stat"><?php echo uid_help_mini_stat_icon_svg( $mi[0] ); ?><div><b><?php echo esc_html( $t['stat1_title'] ?? '' ); ?></b><span><?php echo esc_html( $t['stat1_desc'] ?? '' ); ?></span></div></div>
	        <div class="mini-stat"><?php echo uid_help_mini_stat_icon_svg( $mi[1] ); ?><div><b><?php echo esc_html( $t['stat2_title'] ?? '' ); ?></b><span><?php echo esc_html( $t['stat2_desc'] ?? '' ); ?></span></div></div>
	        <?php if ( $badges ) : ?>
	        <div class="tab-badges">
	          <?php foreach ( $badges as $b ) : ?>
	          	<span class="mini-badge"><?php echo esc_html( $b ); ?></span>
	          <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
	      </div>
	    </div>
	    <?php endforeach; ?>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * ردیف‌های ویژگی محصول (Feature Rows)
 * ===================================================================== */
function uid_render_feature_visual( $key ) {
	switch ( $key ) {
		case 'match':
			?>
			<div class="mockup-frame">
			  <div class="bar"><i></i><i></i><i></i></div>
			  <div class="mockup-body">
			    <div class="doc-lines">
			      <div class="row"><div style="width:40px; height:40px; border-radius:8px; background:var(--navy-050);"></div><div style="flex:1;"><div class="code-line short"></div><div class="code-line" style="width:40%;"></div></div></div>
			    </div>
			    <div style="margin-top:18px; display:flex; justify-content:space-between; align-items:center;">
			      <span style="font-size:13px; color:var(--neutral-600);"><?php esc_html_e( 'درصد تطبیق', 'uid-theme' ); ?></span>
			      <b style="color:var(--teal-500); font-family:'IBM Plex Sans'; font-size:20px;">۹۸٫X%</b>
			    </div>
			  </div>
			</div>
			<?php
			break;
		case 'ocr':
			?>
			<div class="mockup-frame" style="width:100%;">
			  <div class="bar"><i></i><i></i><i></i></div>
			  <div class="mockup-body">
			    <div class="code-line" style="width:30%; background:var(--orange-500); opacity:.5;"></div>
			    <div class="code-line"></div>
			    <div class="code-line short"></div>
			    <div class="code-line teal"></div>
			    <div class="code-line orange"></div>
			  </div>
			</div>
			<?php
			break;
		default: // scan
			?>
			<div class="scan-frame">
			  <div class="scan-beam-line"></div>
			  <div class="corner c1"></div>
			  <div class="corner c2"></div>
			  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="9" r="3.2"/><path d="M6 19c0-3.3 2.7-5 6-5s6 1.7 6 5"/></svg>
			</div>
			<?php
	}
}

function uid_default_feature_rows() {
	return array(
		array(
			'eyebrow' => 'تشخیص زنده بودن', 'heading' => 'تشخیص زنده بودن چهره (Liveness Detection)',
			'text'    => 'تشخیص می‌دهیم کاربر واقعی و حاضر در لحظه است، نه تصویر یا ویدیوی از پیش ضبط‌شده — مطابق با استانداردهای ضدجعل بانکی.',
			'link_text' => 'بیشتر بدانید ←', 'link_url' => '#',
		),
		array(
			'eyebrow' => 'تطبیق چهره', 'heading' => 'تطبیق چهره با مدارک هویتی',
			'text'    => 'چهره کاربر در لحظه با تصویر ثبت‌شده روی مدرک هویتی مقایسه می‌شود و درصد اطمینان تطبیق به‌صورت شفاف در گزارش نمایش داده می‌شود.',
			'link_text' => 'بیشتر بدانید ←', 'link_url' => '#',
		),
		array(
			'eyebrow' => 'OCR اسناد هویتی', 'heading' => 'استخراج هوشمند اطلاعات از مدارک',
			'text'    => 'اطلاعات کارت ملی و شناسنامه به‌صورت خودکار استخراج و در سیستم شما ثبت می‌شود — بدون ورود دستی داده.',
			'link_text' => 'مشاهده مستندات API ←', 'link_url' => '#',
		),
	);
}

function uid_render_section_feature_rows() {
	$tag   = uid_section_tag( 'feature_rows', 'h2' );
	$items = uid_section_val( 'feature_rows', 'items', uid_default_feature_rows() );
	// گرافیک پیش‌فرض هر ردیف اگر ادمین تصویر آپلود نکرده باشد (بر اساس ترتیب)
	$default_visual_keys = array( 'scan', 'match', 'ocr' );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section>
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'feature_rows', 'eyebrow', __( 'درون پلتفرم', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'feature_rows', 'heading', __( 'یک زیرساخت، سه لایه احراز هویت', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'feature_rows', 'text', __( 'از تشخیص زنده بودن چهره تا خواندن هوشمند مدارک — همه از طریق یک API یکپارچه.', 'uid-theme' ) ) ); ?></p>
	    </div>

	    <?php foreach ( $items as $i => $item ) : ?>
	    <div class="feature-row<?php echo ( 1 === $i % 2 ) ? ' reverse' : ''; ?>">
	      <div class="feature-copy">
	        <div class="eyebrow"><span class="dot"></span><?php echo esc_html( $item['eyebrow'] ?? '' ); ?></div>
	        <h3><?php echo esc_html( $item['heading'] ?? '' ); ?></h3>
	        <p><?php echo esc_html( $item['text'] ?? '' ); ?></p>
	        <a href="<?php echo esc_url( $item['link_url'] ?? '#' ); ?>" class="link" style="color:var(--navy-900); font-weight:600;"><?php echo esc_html( $item['link_text'] ?? '' ); ?></a>
	      </div>
	      <div class="feature-visual">
	        <?php
	        $visual_img_id = ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : 0;
	        if ( $visual_img_id ) :
	        	$visual_alt = ! empty( $item['image_alt'] ) ? $item['image_alt'] : ( $item['heading'] ?? '' );
	        	echo wp_get_attachment_image( $visual_img_id, 'large', false, array( 'alt' => $visual_alt, 'class' => 'uid-visual-img' ) );
	        else :
	        	uid_render_feature_visual( $default_visual_keys[ $i % count( $default_visual_keys ) ] );
	        endif;
	        ?>
	      </div>
	    </div>
	    <?php endforeach; ?>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * مراحل یوآیدی‌پلاس (هاب امضادار)
 * ===================================================================== */
function uid_pwa_step_icon_svg( $key ) {
	$icons = array(
		'bolt'    => '<path d="M13 2L4 14h6l-1 8 9-12h-6z"/>',
		'phone'   => '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/>',
		'id'      => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
		'address' => '<path d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'card'    => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
		'face'    => '<circle cx="12" cy="9" r="3.2"/><path d="M6 19c0-3.3 2.7-5 6-5s6 1.7 6 5"/>',
		'check'   => '<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>',
	);
	return $icons[ $key ] ?? $icons['check'];
}

function uid_default_pwa_steps() {
	return array(
		array( 'heading' => 'هدایت کاربر به یوآیدی', 'text' => 'پذیرنده سرویس یوآیدی‌پلاس را با پارامترهای مربوطه فراخوانی می‌کند تا فرآیند احراز هویت آغاز شود.', 'badge' => '' ),
		array( 'heading' => 'اعتبارسنجی تلفن همراه کاربر', 'text' => 'شماره همراه کاربر دریافت و از طریق ارسال کد یک‌بارمصرف (OTP) اعتبارسنجی می‌شود.', 'badge' => '' ),
		array( 'heading' => 'دریافت کد ملی و تاریخ تولد', 'text' => 'با دریافت مشخصات هویتی پایه کاربر، اطلاعات وی از سامانه‌های رسمی (ثبت‌احوال) فراخوانی می‌گردد.', 'badge' => 'شاهکار' ),
		array( 'heading' => 'دریافت آدرس و کد پستی', 'text' => 'آدرس کاربر دریافت و کد پستی ثبت‌شده نیز از سامانه‌های مربوطه استعلام می‌شود.', 'badge' => '' ),
		array( 'heading' => 'اعتبارسنجی اطلاعات مالی', 'text' => 'با دریافت شماره کارت، علاوه بر تطبیق مالکیت حساب، شماره شبای مربوطه نیز استعلام می‌شود.', 'badge' => '' ),
		array( 'heading' => 'احراز هویت بیومتریک (ویدئوی سلفی)', 'text' => 'یک ویدئوی پنج‌ثانیه‌ای از کاربر دریافت و با استفاده از هوش مصنوعی با تصویر مرجع ثبت‌احوال تطابق داده می‌شود.', 'badge' => '' ),
		array( 'heading' => 'اعلام «نتیجه احراز هویت» به پذیرنده', 'text' => 'پس از خاتمه فرآیند، نتیجه احراز هویت به‌همراه کلیه اطلاعات کاربر در اختیار پذیرنده قرار می‌گیرد.', 'badge' => '' ),
	);
}

function uid_default_pwa_flow_stats() {
	return array(
		array( 'value' => '< ۵ دقیقه', 'label' => 'میانگین زمان تکمیل فرآیند' ),
		array( 'value' => '۹۹٪', 'label' => 'دقت هوش مصنوعی در تطابق چهره' ),
		array( 'value' => '۷۳٫۲٪', 'label' => 'نرخ تکمیل، در برابر ۲۳٫۶٪ روش API' ),
	);
}

function uid_render_section_pwa() {
	$tag        = uid_section_tag( 'pwa', 'h2' );
	$steps      = uid_section_val( 'pwa', 'steps', uid_default_pwa_steps() );
	$flow_stats = uid_section_val( 'pwa', 'flow_stats', uid_default_pwa_flow_stats() );
	if ( ! is_array( $steps ) || empty( $steps ) ) return;
	$last = count( $steps ) - 1;
	// آیکون پیش‌فرض هر مرحله اگر ادمین تصویر آپلود نکرده باشد (بر اساس ترتیب)
	$default_icon_keys = array( 'bolt', 'phone', 'id', 'address', 'card', 'face', 'check' );
	?>
	<section style="background:var(--neutral-050);" id="pwa">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'pwa', 'eyebrow', __( 'یوآیدی‌پلاس (PWA)', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'pwa', 'heading', __( 'مراحل احراز هویت یکپارچه یوآیدی‌پلاس', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'pwa', 'text', __( 'از فراخوانی سرویس تا اعلام نتیجه به پذیرنده — هفت گام، همگی در یک راهکار تحت وب یکپارچه و بدون بار توسعه فنی برای شما.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="pwa-flow reveal">
	      <?php foreach ( $steps as $i => $s ) :
	      	$pos_class = ( 0 === $i ) ? ' start' : ( ( $i === $last ) ? ' end' : '' );
	      	$color     = ( 0 === $i || $i === $last ) ? 'var(--teal-500)' : 'var(--navy-900)';
	      	?>
	      <div class="pwa-step-row<?php echo esc_attr( $pos_class ); ?>">
	        <div class="pwa-num"><?php echo esc_html( uid_fa_digits( $i ) ); ?></div>
	        <div class="pwa-content">
	          <h4>
	            <?php
	            $step_img_id = ! empty( $s['icon_id'] ) ? absint( $s['icon_id'] ) : 0;
	            if ( $step_img_id ) :
	            	$step_alt = ! empty( $s['icon_alt'] ) ? $s['icon_alt'] : ( $s['heading'] ?? '' );
	            	echo wp_get_attachment_image( $step_img_id, 'thumbnail', false, array( 'alt' => $step_alt, 'class' => 'uid-icon-img', 'style' => 'width:18px;height:18px;' ) );
	            else :
	            	?>
	            	<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:18px;height:18px;color:<?php echo esc_attr( $color ); ?>;"><?php echo uid_pwa_step_icon_svg( $default_icon_keys[ $i % count( $default_icon_keys ) ] ); ?></svg>
	            	<?php
	            endif;
	            ?>
	            <?php echo esc_html( $s['heading'] ?? '' ); ?><?php if ( ! empty( $s['badge'] ) ) : ?> <span class="pwa-badge"><?php echo esc_html( $s['badge'] ); ?></span><?php endif; ?>
	          </h4>
	          <p><?php echo esc_html( $s['text'] ?? '' ); ?></p>
	        </div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	    <?php if ( is_array( $flow_stats ) && $flow_stats ) : ?>
	    <div class="pwa-flow-footer reveal">
	      <?php foreach ( $flow_stats as $fs ) : ?>
	      	<div class="pwa-flow-stat"><b><?php echo esc_html( $fs['value'] ?? '' ); ?></b><span><?php echo esc_html( $fs['label'] ?? '' ); ?></span></div>
	      <?php endforeach; ?>
	      <a href="<?php echo esc_url( uid_section_val( 'pwa', 'button_url', '#contact' ) ); ?>" class="btn btn-navy js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو یوآیدی‌پلاس', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی‌پلاس با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo esc_html( uid_section_val( 'pwa', 'button_text', __( 'درخواست دمو یوآیدی‌پلاس', 'uid-theme' ) ) ); ?></a>
	    </div>
	    <?php endif; ?>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر تماس (CTA)
 * ===================================================================== */
function uid_render_section_cta() {
	$tag = uid_section_tag( 'cta', 'h2' );
	?>
	<section>
	  <div class="cta-banner">
	    <div class="cta-banner-inner">
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'cta', 'heading', __( 'همین امروز فرآیند احراز هویت مشتریان‌تان را ساده کنید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <div class="hero-actions">
	        <a href="<?php echo esc_url( uid_section_val( 'cta', 'btn1_url', '#contact' ) ); ?>" class="btn btn-primary js-demo-btn" data-modal-title="<?php esc_attr_e( 'درخواست دمو', 'uid-theme' ); ?>" data-modal-desc="<?php esc_attr_e( 'مشخصات خود را ثبت کنید تا کارشناسان یوآیدی با شما تماس بگیرند.', 'uid-theme' ); ?>"><?php echo esc_html( uid_section_val( 'cta', 'btn1_text', __( 'درخواست دمو', 'uid-theme' ) ) ); ?></a>
	        <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="btn btn-outline-white phone-btn">
	          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg>
	          <span dir="ltr"><?php echo esc_html( uid_phone_display() ); ?></span>
	        </a>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * روند همکاری (کاروسل سه‌مرحله‌ای)
 * ===================================================================== */
function uid_process_icon_svg( $key ) {
	$icons = array(
		'doc'     => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 3v2h6V3M9 10h6M9 14h4"/>',
		'connect' => '<path d="M8 3L4 7l4 4M16 3l4 4-4 4M14 3l-4 18"/>',
		'rocket'  => '<path d="M13 2L4 14h6l-1 8 9-12h-6z"/>',
	);
	return $icons[ $key ] ?? $icons['doc'];
}

function uid_default_process_steps() {
	return array(
		array( 'title' => 'ثبت درخواست', 'text' => 'فرم درخواست را تکمیل کنید تا تیم ما نیاز شما را بررسی کند.' ),
		array( 'title' => 'یکپارچه‌سازی', 'text' => 'با مستندات API و محیط آزمایشی، تیم فنی شما ادغام را انجام می‌دهد.' ),
		array( 'title' => 'راه‌اندازی و پشتیبانی', 'text' => 'سرویس در محیط عملیاتی فعال می‌شود و پشتیبانی اختصاصی آغاز می‌گردد.' ),
	);
}

function uid_render_section_process() {
	$tag   = uid_section_tag( 'process', 'h2' );
	$steps = uid_section_val( 'process', 'steps', uid_default_process_steps() );
	// آیکون پیش‌فرض هر مرحله اگر ادمین تصویر آپلود نکرده باشد (بر اساس ترتیب)
	$default_icon_keys = array( 'doc', 'connect', 'rocket' );
	if ( ! is_array( $steps ) || empty( $steps ) ) return;
	?>
	<section>
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'process', 'eyebrow', __( 'روند همکاری', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'process', 'heading', __( 'سه گام تا راه‌اندازی', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'process', 'text', __( 'از ثبت درخواست تا اتصال کامل API، مسیر مشخص و کوتاه است.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="process-carousel reveal">
	      <div class="process-slides" id="processSlides">
	        <?php foreach ( $steps as $i => $s ) : ?>
	        <div class="process-slide<?php echo 0 === $i ? ' active' : ''; ?>" data-slide="<?php echo esc_attr( $i ); ?>">
	          <div class="process-text">
	            <div class="num-shape"><?php echo esc_html( uid_fa_digits( $i + 1 ) ); ?></div>
	            <div><h4><?php echo esc_html( $s['title'] ?? '' ); ?></h4><p><?php echo esc_html( $s['text'] ?? '' ); ?></p></div>
	          </div>
	          <div class="process-icon-wrap">
	            <?php
	            $step_img_id = ! empty( $s['icon_id'] ) ? absint( $s['icon_id'] ) : 0;
	            if ( $step_img_id ) :
	            	$step_alt = ! empty( $s['icon_alt'] ) ? $s['icon_alt'] : ( $s['title'] ?? '' );
	            	echo wp_get_attachment_image( $step_img_id, 'thumbnail', false, array( 'alt' => $step_alt, 'class' => 'uid-icon-img' ) );
	            else :
	            	?>
	            	<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><?php echo uid_process_icon_svg( $default_icon_keys[ $i % count( $default_icon_keys ) ] ); ?></svg>
	            	<?php
	            endif;
	            ?>
	          </div>
	        </div>
	        <?php endforeach; ?>
	      </div>
	      <div class="process-controls">
	        <button class="process-arrow" id="processPrev" aria-label="<?php esc_attr_e( 'مرحله قبل', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5l-7 7 7 7"/></svg></button>
	        <div class="process-dots" id="processDots">
	          <?php foreach ( $steps as $i => $s ) : ?>
	          	<button class="process-dot<?php echo 0 === $i ? ' active' : ''; ?>" data-goto="<?php echo esc_attr( $i ); ?>"></button>
	          <?php endforeach; ?>
	        </div>
	        <button class="process-arrow" id="processNext" aria-label="<?php esc_attr_e( 'مرحله بعد', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg></button>
	      </div>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * نظرات مشتریان
 * ===================================================================== */
function uid_default_testimonial_avatar_svg() {
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>';
}

function uid_default_testimonials() {
	return array(
		array(
			'rating' => 5, 'image_id' => 0, 'image_alt' => '',
			'quote'       => 'یکپارچه‌سازی API یوآیدی چند روز بیشتر طول نکشید و از همان هفته اول، گزارش‌های شفافی از وضعیت احراز هویت مشتریان در اختیار تیم ریسک ما قرار گرفت.',
			'person_name' => '[نام و سمت مشتری]', 'person_role' => '[نام سازمان — بانک خصوصی]',
		),
		array(
			'rating' => 5, 'image_id' => 0, 'image_alt' => '',
			'quote'       => 'با اتصال به یوآیدی، ثبت‌نام کاربران جدید در صرافی ما به‌طور محسوسی سریع‌تر شد و نرخ افت کاربر در مرحله احراز هویت به‌شدت کاهش یافت.',
			'person_name' => '[نام و سمت مشتری]', 'person_role' => '[نام سازمان — صرافی ارز دیجیتال]',
		),
		array(
			'rating' => 5, 'image_id' => 0, 'image_alt' => '',
			'quote'       => 'تیم پشتیبانی فنی یوآیدی مستقیم و سریع پاسخ‌گو بود و یکپارچه‌سازی اولیه در کمتر از یک هفته برای تیم ما تکمیل شد.',
			'person_name' => '[نام و سمت مشتری]', 'person_role' => '[نام سازمان — فین‌تک]',
		),
	);
}

function uid_render_section_testimonial() {
	$tag   = uid_section_tag( 'testimonial', 'h2' );
	$items = uid_section_val( 'testimonial', 'items', uid_default_testimonials() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	$star = '<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 15,9 22,9 16.5,13.5 18.5,21 12,17 5.5,21 7.5,13.5 2,9 9,9"/></svg>';
	?>
	<section class="testimonial">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'testimonial', 'eyebrow', __( 'نظر مشتریان', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'testimonial', 'heading', __( 'سازمان‌هایی که به یوآیدی اعتماد کرده‌اند', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <?php $note = uid_section_val( 'testimonial', 'note', __( '* نام‌ها و نظرات نمونه‌اند تا زمان دریافت نظرات واقعی مشتریان.', 'uid-theme' ) ); ?>
	      <?php if ( $note ) : ?><p class="placeholder-flag"><?php echo esc_html( $note ); ?></p><?php endif; ?>
	    </div>
	    <div class="testimonial-card-grid" id="testimonialGrid">
	      <?php foreach ( $items as $item ) :
	      	$rating = max( 1, min( 5, (int) ( $item['rating'] ?? 5 ) ) );
	      	$img_id = ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : 0;
	      	?>
	      	<div class="t-card">
	      	  <div class="stars"><?php echo str_repeat( $star, $rating ); ?></div>
	      	  <p class="t-quote">«<?php echo esc_html( $item['quote'] ?? '' ); ?>»</p>
	      	  <div class="t-person">
	      	    <div class="t-avatar">
	      	      <?php if ( $img_id ) :
	      	      	$t_alt = ! empty( $item['image_alt'] ) ? $item['image_alt'] : ( $item['person_name'] ?? '' );
	      	      	echo wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => $t_alt ) );
	      	      else :
	      	      	echo uid_default_testimonial_avatar_svg();
	      	      endif; ?>
	      	    </div>
	      	    <div><b><?php echo esc_html( $item['person_name'] ?? '' ); ?></b><span><?php echo esc_html( $item['person_role'] ?? '' ); ?></span></div>
	      	  </div>
	      	</div>
	      <?php endforeach; ?>
	    </div>
	    <div class="carousel-dots" id="testimonialDots" style="display:none;"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * وبلاگ و منابع
 * ===================================================================== */
function uid_resource_placeholder_svg( $i ) {
	$variants = array(
		'<svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice"><rect width="400" height="200" fill="#EAF0FB"/><rect x="140" y="40" width="120" height="160" rx="10" fill="#15397C"/><circle cx="200" cy="90" r="24" fill="#fff" opacity=".9"/></svg>',
		'<svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice"><rect width="400" height="200" fill="#E7F8FA"/><polygon points="120,180 200,60 280,180" fill="#29BCCE"/></svg>',
		'<svg viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice"><rect width="400" height="200" fill="#FFF1E0"/><circle cx="200" cy="100" r="60" fill="#F89428" opacity=".85"/></svg>',
	);
	return $variants[ $i % count( $variants ) ];
}

function uid_default_resources() {
	return array(
		array( 'image_id' => 0, 'image_alt' => '', 'pill' => 'سامانه ثنا', 'meta' => 'راهنما', 'title' => 'راهنمای کامل احراز هویت غیرحضوری در سامانه ثنا', 'link_url' => '#' ),
		array( 'image_id' => 0, 'image_alt' => '', 'pill' => 'یوآیدی پلاس', 'meta' => 'معرفی', 'title' => 'یوآیدی پلاس چیست و چه خدماتی را یکجا ارائه می‌دهد؟', 'link_url' => '#' ),
		array( 'image_id' => 0, 'image_alt' => '', 'pill' => 'استعلام', 'meta' => 'مقایسه', 'title' => 'تفاوت استعلام شاهکار و هویتی در چیست؟', 'link_url' => '#' ),
	);
}

function uid_render_section_resources() {
	$tag   = uid_section_tag( 'resources', 'h2' );
	$items = uid_section_val( 'resources', 'items', uid_default_resources() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section id="resources">
	  <div class="wrap">
	    <div class="resources-head">
	      <div class="section-head reveal">
	        <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'resources', 'eyebrow', __( 'وبلاگ و منابع', 'uid-theme' ) ) ); ?></div>
	        <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'resources', 'heading', __( 'راهنماهای احراز هویت', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      </div>
	      <a href="<?php echo esc_url( uid_section_val( 'resources', 'footer_link_url', '#' ) ); ?>" class="btn btn-outline btn-sm"><?php echo esc_html( uid_section_val( 'resources', 'footer_link_text', __( 'مشاهده همه مقالات', 'uid-theme' ) ) ); ?></a>
	    </div>
	    <div class="res-grid" id="resourcesGrid">
	      <?php foreach ( $items as $i => $item ) :
	      	$img_id = ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : 0;
	      	?>
	      	<div class="res-card">
	      	  <div class="res-thumb">
	      	    <?php if ( $img_id ) :
	      	    	$r_alt = ! empty( $item['image_alt'] ) ? $item['image_alt'] : ( $item['title'] ?? '' );
	      	    	echo wp_get_attachment_image( $img_id, 'medium', false, array( 'alt' => $r_alt, 'style' => 'width:100%;height:100%;object-fit:cover;' ) );
	      	    else :
	      	    	echo uid_resource_placeholder_svg( $i );
	      	    endif; ?>
	      	  </div>
	      	  <div class="res-body">
	      	    <div class="res-meta"><span class="pill"><?php echo esc_html( $item['pill'] ?? '' ); ?></span><span><?php echo esc_html( $item['meta'] ?? '' ); ?></span></div>
	      	    <h4><?php echo esc_html( $item['title'] ?? '' ); ?></h4>
	      	    <a href="<?php echo esc_url( $item['link_url'] ?? '#' ); ?>" class="link"><?php esc_html_e( 'ادامه مطلب ←', 'uid-theme' ); ?></a>
	      	  </div>
	      	</div>
	      <?php endforeach; ?>
	    </div>
	    <div class="carousel-dots" id="resourcesDots" style="display:none;"></div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * سوالات متداول
 * ===================================================================== */
function uid_default_faq_items() {
	return array(
		array( 'question' => 'احراز هویت ثنا چقدر طول می‌کشد؟', 'answer' => 'معمولاً کمتر از چند دقیقه، به شرط آماده بودن مدارک هویتی و اتصال اینترنت پایدار.' ),
		array( 'question' => 'آیا احراز هویت برای ایرانیان خارج از کشور هم ممکن است؟', 'answer' => 'بله، سامانه ثنا امکان احراز هویت غیرحضوری برای هموطنان مقیم خارج از کشور را نیز فراهم می‌کند.' ),
		array( 'question' => 'یکپارچه‌سازی API چقدر زمان می‌برد؟', 'answer' => 'با مستندات و نمونه‌کدهای آماده، تیم فنی معمولاً در کمتر از یک روز کاری یکپارچه‌سازی را کامل می‌کند.' ),
		array( 'question' => 'آیا محیط آزمایشی (Sandbox) رایگان است؟', 'answer' => 'بله، پیش از هرگونه قرارداد می‌توانید API را در محیط آزمایشی رایگان بررسی و تست کنید.' ),
		array( 'question' => 'تفاوت استعلام شاهکار و هویتی در چیست؟', 'answer' => 'استعلام شاهکار صحت شماره موبایل را با کد ملی صاحب خط تطبیق می‌دهد؛ استعلام هویتی، اطلاعات هویتی فرد را از منابع رسمی کشور استعلام می‌کند.' ),
		array( 'question' => 'پشتیبانی فنی چگونه ارائه می‌شود؟', 'answer' => 'تیم فنی یوآیدی به‌صورت مستقیم و در ساعات اداری، پاسخ‌گوی سوالات پیاده‌سازی و یکپارچه‌سازی شماست.' ),
	);
}

function uid_render_section_faq() {
	$tag   = uid_section_tag( 'faq', 'h2' );
	$items = uid_section_val( 'faq', 'items', uid_default_faq_items() );
	if ( ! is_array( $items ) || empty( $items ) ) return;
	?>
	<section id="faq">
	  <div class="wrap">
	    <div class="section-head center reveal">
	      <div class="eyebrow" style="margin-inline:auto;"><span class="dot"></span><?php echo esc_html( uid_section_val( 'faq', 'eyebrow', __( 'سوالات متداول', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'faq', 'heading', __( 'سوالاتی که معمولاً پرسیده می‌شود', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p><?php echo esc_html( uid_section_val( 'faq', 'text', __( 'پاسخ ندیدید؟ از طریق فرم پایین صفحه مستقیماً از ما بپرسید.', 'uid-theme' ) ) ); ?></p>
	    </div>
	    <div class="faq-grid reveal">
	      <?php foreach ( $items as $item ) : ?>
	      <div class="faq-item">
	        <button class="faq-question"><span><?php echo esc_html( $item['question'] ?? '' ); ?></span><span class="faq-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg></span></button>
	        <div class="faq-answer"><div class="faq-answer-inner"><?php echo esc_html( $item['answer'] ?? '' ); ?></div></div>
	      </div>
	      <?php endforeach; ?>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * فرم تماس
 * ===================================================================== */
function uid_render_section_contact() {
	$tag      = uid_section_tag( 'contact', 'h2' );
	$services = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'contact', 'service_options', "سامانه ثنا\nاستعلام شاهکار\nاستعلام هویتی\nیکپارچه‌سازی API سازمانی" ) ) ) );
	?>
	<section id="contact">
	  <div class="contact">
	    <div class="contact-grid">
	      <div class="contact-info">
	        <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'contact', 'eyebrow', __( 'درخواست خدمت', 'uid-theme' ) ) ); ?></div>
	        <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'contact', 'heading', __( 'همین حالا با تیم یوآیدی در تماس باشید', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	        <p><?php echo esc_html( uid_section_val( 'contact', 'text', __( 'فرم را تکمیل کنید تا کارشناسان ما در سریع‌ترین زمان با شما تماس بگیرند.', 'uid-theme' ) ) ); ?></p>
	        <div class="contact-methods">
	          <div class="contact-method">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg>
	            <div><b><?php echo esc_html( uid_section_val( 'contact', 'method1_title', __( 'تماس تلفنی', 'uid-theme' ) ) ); ?></b><span><?php echo esc_html( uid_section_val( 'contact', 'method1_desc', __( 'پاسخ‌گویی در ساعات اداری', 'uid-theme' ) ) ); ?></span></div>
	          </div>
	          <div class="contact-method">
	            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
	            <div><b><?php echo esc_html( uid_section_val( 'contact', 'method2_title', __( 'ارسال ایمیل', 'uid-theme' ) ) ); ?></b><span><?php echo esc_html( uid_section_val( 'contact', 'method2_desc', __( 'پاسخ ظرف یک روز کاری', 'uid-theme' ) ) ); ?></span></div>
	          </div>
	        </div>
	      </div>
	      <form class="contact-form" onsubmit="event.preventDefault(); alert('این یک نمونه فرم است.');">
	        <div class="form-row">
	          <div class="field"><label><?php echo esc_html( uid_section_val( 'contact', 'label_name', __( 'نام و نام خانوادگی', 'uid-theme' ) ) ); ?></label><input type="text" placeholder="<?php echo esc_attr( uid_section_val( 'contact', 'ph_name', __( 'مثلاً علی رضایی', 'uid-theme' ) ) ); ?>" required></div>
	          <div class="field"><label><?php echo esc_html( uid_section_val( 'contact', 'label_company', __( 'نام شرکت', 'uid-theme' ) ) ); ?></label><input type="text" placeholder="<?php echo esc_attr( uid_section_val( 'contact', 'ph_company', __( 'نام سازمان شما', 'uid-theme' ) ) ); ?>"></div>
	        </div>
	        <div class="form-row">
	          <div class="field"><label><?php echo esc_html( uid_section_val( 'contact', 'label_email', __( 'ایمیل', 'uid-theme' ) ) ); ?></label><input type="email" placeholder="you@company.com" required></div>
	          <div class="field"><label><?php echo esc_html( uid_section_val( 'contact', 'label_phone', __( 'شماره تماس', 'uid-theme' ) ) ); ?></label><input type="tel" placeholder="09xxxxxxxxx"></div>
	        </div>
	        <div class="form-row">
	          <div class="field full">
	            <label><?php echo esc_html( uid_section_val( 'contact', 'label_service', __( 'نوع خدمت مورد نیاز', 'uid-theme' ) ) ); ?></label>
	            <select>
	              <?php foreach ( $services as $s ) : ?>
	              	<option><?php echo esc_html( $s ); ?></option>
	              <?php endforeach; ?>
	            </select>
	          </div>
	        </div>
	        <div class="form-row">
	          <div class="field full"><label><?php echo esc_html( uid_section_val( 'contact', 'label_message', __( 'توضیحات', 'uid-theme' ) ) ); ?></label><textarea rows="3" placeholder="<?php echo esc_attr( uid_section_val( 'contact', 'ph_message', __( 'نیاز خود را کوتاه شرح دهید', 'uid-theme' ) ) ); ?>"></textarea></div>
	        </div>
	        <button type="submit" class="btn btn-primary"><?php echo esc_html( uid_section_val( 'contact', 'submit_text', __( 'ارسال درخواست', 'uid-theme' ) ) ); ?></button>
	      </form>
	    </div>
	  </div>
	</section>
	<?php
}

/* =====================================================================
 * بنر درخواست تماس (پیش از فوتر) — قبلاً به‌اشتباه داخل footer.php (سراسری،
 * روی همه صفحات از جمله یوآیدی‌پلاس/ثبت احوال/احراز هویت تصویری) بود؛ چون
 * محتوایش مخصوص صفحه اصلی است، حالا یک سکشن معمولی صفحه اصلی است.
 * =====================================================================*/
function uid_render_section_leadband() {
	$tag = uid_section_tag( 'leadband', 'h2' );
	$default_services = "سامانه ثنا\nاستعلام شاهکار\nاستعلام هویتی\nAPI سازمانی";
	$services = array_filter( array_map( 'trim', explode( "\n", uid_section_val( 'leadband', 'service_options', $default_services ) ) ) );
	?>
	<div class="footer-lead-band">
	  <div class="lead-band-grid">
	    <div class="lead-band-copy">
	      <div class="eyebrow"><span class="dot"></span><?php echo esc_html( uid_section_val( 'leadband', 'eyebrow', __( 'آماده شروع همکاری هستید؟', 'uid-theme' ) ) ); ?></div>
	      <?php echo '<' . $tag . '>'; ?><?php echo esc_html( uid_section_val( 'leadband', 'heading', __( 'بیایید زیرساخت احراز هویت سازمان شما را با هم راه‌اندازی کنیم', 'uid-theme' ) ) ); ?><?php echo '</' . $tag . '>'; ?>
	      <p>
	        <?php echo esc_html( uid_section_val( 'leadband', 'text', __( 'فرم را تکمیل کنید تا کارشناسان یوآیدی در سریع‌ترین زمان با شما تماس بگیرند — یا مستقیماً تماس بگیرید.', 'uid-theme' ) ) ); ?>
	        <span dir="ltr"><?php echo esc_html( uid_phone_display() ); ?></span>
	      </p>
	      <div class="lead-band-trust">
	        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( uid_section_val( 'leadband', 'trust1', __( 'پاسخ در کمتر از یک روز کاری', 'uid-theme' ) ) ); ?></span>
	        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( uid_section_val( 'leadband', 'trust2', __( 'مشاوره رایگان یکپارچه‌سازی', 'uid-theme' ) ) ); ?></span>
	      </div>
	    </div>
	    <form class="lead-band-form" onsubmit="event.preventDefault(); alert('این یک نمونه فرم است.');">
	      <div class="frow">
	        <input type="text" placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>" required>
	        <input type="tel" placeholder="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>" required>
	      </div>
	      <div class="frow" style="grid-template-columns:1fr; margin-bottom:14px;">
	        <select>
	          <option><?php echo esc_html( uid_section_val( 'leadband', 'select_placeholder', __( 'نوع خدمت مورد نیاز', 'uid-theme' ) ) ); ?></option>
	          <?php foreach ( $services as $s ) : ?>
	          <option><?php echo esc_html( $s ); ?></option>
	          <?php endforeach; ?>
	        </select>
	      </div>
	      <button type="submit" class="btn btn-primary"><?php echo esc_html( uid_section_val( 'leadband', 'button_text', __( 'درخواست تماس', 'uid-theme' ) ) ); ?></button>
	    </form>
	  </div>
	</div>
	<?php
}
