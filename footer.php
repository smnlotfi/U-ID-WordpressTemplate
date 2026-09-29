<?php
/**
 * قالب فوتر سایت — شامل فوتر اصلی و ابزارک‌های شناور سراسری (دکمه تماس، چت، مودال‌ها)
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<footer>
  <svg class="footer-hex-pattern" viewBox="0 0 100 100"><polygon points="50,3 93,26 93,74 50,97 7,74 7,26" fill="none" stroke="#29BCCE" stroke-width="1"/></svg>

  <div class="wrap footer-main">
    <div class="footer-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
        <?php uid_logo_html(); ?>
      </a>
      <p><?php echo esc_html( uid_get_option( 'uid_footer_options', 'description', __( 'زیرساخت احراز هویت دیجیتال برای بانک‌ها، صرافی‌ها و فین‌تک‌های ایران — یک API، همه سامانه‌های رسمی کشور.', 'uid-theme' ) ) ); ?></p>
      <div class="socials">
        <?php $ig = uid_get_option( 'uid_footer_options', 'instagram', '' ); if ( $ig ) : ?>
        <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'اینستاگرام', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
        <?php endif; ?>
        <?php $li = uid_get_option( 'uid_footer_options', 'linkedin', '' ); if ( $li ) : ?>
        <a href="<?php echo esc_url( $li ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'لینکدین', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="3"/><path d="M7 10v7M7 7v.01M11 17v-4.5a2.5 2.5 0 015 0V17M11 10v7"/></svg></a>
        <?php endif; ?>
        <?php $tg = uid_telegram_url(); if ( $tg ) : ?>
        <a href="<?php echo esc_url( $tg ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'تلگرام', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 5L2 12.5l6 2M21 5l-3 15-8-6M21 5L8 14.5v5.5"/></svg></a>
        <?php endif; ?>
      </div>
    </div>
    <div class="footer-col">
      <h5><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col1_title', __( 'محصولات', 'uid-theme' ) ) ); ?></h5>
      <?php uid_render_footer_menu( '1' ); ?>
    </div>
    <div class="footer-col">
      <h5><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col2_title', __( 'فناوری و مستندات', 'uid-theme' ) ) ); ?></h5>
      <?php uid_render_footer_menu( '2' ); ?>
    </div>
    <div class="footer-col">
      <h5><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col3_title', __( 'یوآیدی', 'uid-theme' ) ) ); ?></h5>
      <?php uid_render_footer_menu( '3' ); ?>
    </div>
    <div class="footer-col">
      <div class="newsletter-box">
        <h5><?php echo esc_html( uid_get_option( 'uid_footer_options', 'newsletter_heading', __( 'عضویت در خبرنامه', 'uid-theme' ) ) ); ?></h5>
        <p><?php echo esc_html( uid_get_option( 'uid_footer_options', 'newsletter_text', __( 'اخبار محصول و به‌روزرسانی‌های API را دریافت کنید.', 'uid-theme' ) ) ); ?></p>
        <form class="newsletter-form" onsubmit="event.preventDefault();">
          <input type="email" placeholder="<?php esc_attr_e( 'ایمیل شما', 'uid-theme' ); ?>">
          <button class="btn btn-primary btn-sm" type="submit"><?php echo esc_html( uid_get_option( 'uid_footer_options', 'newsletter_button', __( 'ثبت', 'uid-theme' ) ) ); ?></button>
        </form>
      </div>
    </div>
  </div>

  <div class="wrap footer-bottom">
    <span><?php echo esc_html( uid_get_option( 'uid_footer_options', 'copyright', __( '© ۱۴۰۵ یوآیدی. تمامی حقوق محفوظ است.', 'uid-theme' ) ) ); ?></span>
  </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'uid-theme' ); ?>">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<!-- ================= FLOATING ACTION CLUSTER (desktop) ================= -->
<div class="fab-cluster" id="fabCluster">
  <div class="fab-item" style="--d:0;">
    <span class="fab-label"><?php esc_html_e( 'واتساپ', 'uid-theme' ); ?></span>
    <a href="<?php echo esc_url( uid_whatsapp_url() ); ?>" target="_blank" rel="noopener" class="fab-circle fab-whatsapp" aria-label="<?php esc_attr_e( 'واتساپ', 'uid-theme' ); ?>">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.6.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 2 3.1 4.9 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4-.1-.1-.3-.2-.6-.3z"/><path d="M12 2a10 10 0 00-8.5 15.2L2 22l4.9-1.3A10 10 0 1012 2z" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
    </a>
  </div>
  <div class="fab-item" style="--d:1;">
    <span class="fab-label"><?php esc_html_e( 'تلگرام', 'uid-theme' ); ?></span>
    <a href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener" class="fab-circle fab-telegram" aria-label="<?php esc_attr_e( 'تلگرام', 'uid-theme' ); ?>">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.5 3.5L2.7 10.9c-1.2.5-1.2 1.2-.2 1.5l4.8 1.5 1.9 5.7c.2.6.4.8.8.8.4 0 .6-.2.8-.5l2-1.9 4.2 3.1c.7.4 1.2.2 1.4-.7l3-13.9c.3-1.1-.4-1.6-1.4-1.2zM8.3 14.1l9.2-5.8c.4-.3.8-.1.5.2l-7.6 6.9-.3 3-1.4-4.3z"/></svg>
    </a>
  </div>
  <div class="fab-item" style="--d:2;">
    <span class="fab-label"><?php esc_html_e( 'تماس تلفنی', 'uid-theme' ); ?></span>
    <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="fab-circle fab-phone" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'uid-theme' ); ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg>
    </a>
  </div>
  <div class="fab-item" style="--d:3;">
    <span class="fab-label"><?php esc_html_e( 'درخواست مشاوره', 'uid-theme' ); ?></span>
    <button type="button" class="fab-circle fab-consult" id="fabConsultBtn" aria-label="<?php esc_attr_e( 'درخواست مشاوره', 'uid-theme' ); ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H8l-4 4z"/></svg>
    </button>
  </div>
  <div class="fab-item" style="--d:4;">
    <span class="fab-label"><?php esc_html_e( 'گفتگوی آنلاین', 'uid-theme' ); ?></span>
    <button type="button" class="fab-circle fab-chat" id="fabChatBtn" aria-label="<?php esc_attr_e( 'گفتگوی آنلاین', 'uid-theme' ); ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.6 2.6 0 015 .9c0 1.7-2.5 2.1-2.5 4"/><circle cx="12" cy="17.5" r=".7" fill="currentColor"/></svg>
    </button>
  </div>
</div>

<button class="lead-fab" id="leadFabBtn" aria-expanded="false" aria-label="<?php esc_attr_e( 'باز کردن منوی تماس', 'uid-theme' ); ?>">
  <svg class="fab-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg>
  <svg class="fab-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 6l12 12M18 6L6 18"/></svg>
  <span><?php esc_html_e( 'در ارتباط باشید', 'uid-theme' ); ?></span>
</button>

<!-- ================= LEAD / CONSULT / DEMO MODAL ================= -->
<div class="lead-modal-overlay" id="leadModalOverlay">
  <div class="lead-modal">
    <button class="lead-modal-close" id="leadModalClose" aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
    <h3 id="leadModalTitle"><?php esc_html_e( 'درخواست تماس از کارشناسان یوآیدی', 'uid-theme' ); ?></h3>
    <p id="leadModalDesc"><?php esc_html_e( 'شماره تماس خود را ثبت کنید تا در سریع‌ترین زمان ممکن با شما تماس بگیریم.', 'uid-theme' ); ?></p>
    <form id="leadModalForm" novalidate>
      <div class="lmf-fields">
        <div class="field"><input type="text" name="name" data-req placeholder="<?php esc_attr_e( 'نام و نام خانوادگی', 'uid-theme' ); ?>"></div>
        <div class="field"><input type="tel" name="phone" inputmode="numeric" data-req data-tel placeholder="<?php esc_attr_e( 'شماره تماس', 'uid-theme' ); ?>"></div>
        <div class="field">
          <select name="service">
            <option value=""><?php esc_html_e( 'نوع خدمت مورد نیاز', 'uid-theme' ); ?></option>
            <option value="sana"><?php esc_html_e( 'سامانه ثنا', 'uid-theme' ); ?></option>
            <option value="pwa"><?php esc_html_e( 'یوآیدی‌پلاس', 'uid-theme' ); ?></option>
            <option value="shahkar"><?php esc_html_e( 'استعلام شاهکار', 'uid-theme' ); ?></option>
            <option value="enterprise"><?php esc_html_e( 'وب‌سرویس‌های سازمانی', 'uid-theme' ); ?></option>
            <option value="other"><?php esc_html_e( 'سایر', 'uid-theme' ); ?></option>
          </select>
        </div>
        <button type="button" class="btn btn-primary" data-submit><?php esc_html_e( 'ارسال درخواست', 'uid-theme' ); ?></button>
      </div>
      <div class="lmf-ok" style="display:none;text-align:center;padding:20px 0">
        <b><?php esc_html_e( 'درخواست شما ثبت شد', 'uid-theme' ); ?></b><br>
        <span><?php esc_html_e( 'کارشناسان یوآیدی به‌زودی با شما تماس می‌گیرند.', 'uid-theme' ); ?></span>
      </div>
    </form>
  </div>
</div>

<!-- ================= LIVE CHAT MODAL (placeholder for existing Raychat widget) ================= -->
<div class="chat-modal-overlay" id="chatModalOverlay">
  <div class="chat-modal">
    <div class="chat-modal-head">
      <div class="chat-agent">
        <span class="chat-avatar">پ</span>
        <div><b><?php esc_html_e( 'سیستم پشتیبانی آنلاین', 'uid-theme' ); ?></b><span><?php esc_html_e( 'معمولاً در چند دقیقه پاسخ می‌دهیم', 'uid-theme' ); ?></span></div>
      </div>
      <div class="chat-head-actions">
        <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="chat-icon-btn" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg></a>
        <button class="chat-icon-btn" id="chatModalClose" aria-label="<?php esc_attr_e( 'بستن', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
      </div>
    </div>
    <div class="chat-modal-body">
      <div class="chat-bubble"><?php esc_html_e( 'سلام 👋', 'uid-theme' ); ?><br><?php esc_html_e( 'چطور می‌توانم کمکتان کنم؟', 'uid-theme' ); ?></div>
    </div>
    <div class="chat-modal-input">
      <button class="chat-icon-btn sm" aria-label="<?php esc_attr_e( 'پیوست', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5l-8.5 8.5a4 4 0 01-5.7-5.7l9-9a2.7 2.7 0 013.8 3.8l-8.5 8.5a1.3 1.3 0 01-1.9-1.9l7.8-7.8"/></svg></button>
      <input type="text" placeholder="<?php esc_attr_e( 'اینجا تایپ کنید...', 'uid-theme' ); ?>">
      <button class="chat-icon-btn sm send" aria-label="<?php esc_attr_e( 'ارسال', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4z"/></svg></button>
    </div>
    <div class="chat-modal-foot"><?php esc_html_e( 'قدرت‌گرفته از سامانه پشتیبانی یوآیدی', 'uid-theme' ); ?></div>
  </div>
</div>

<!-- ================= MOBILE CONTACT SHEET ================= -->
<div class="contact-sheet-overlay" id="contactSheetOverlay">
  <div class="contact-sheet">
    <div class="cs-handle"></div>
    <h4><?php esc_html_e( 'چطور در ارتباط باشیم؟', 'uid-theme' ); ?></h4>
    <button type="button" class="cs-item" id="csChat">
      <span class="cs-icon" style="background:var(--teal-500);"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.6 2.6 0 015 .9c0 1.7-2.5 2.1-2.5 4"/><circle cx="12" cy="17.5" r=".7" fill="#fff"/></svg></span>
      <span><?php esc_html_e( 'گفتگوی آنلاین', 'uid-theme' ); ?></span>
    </button>
    <button type="button" class="cs-item" id="csConsult">
      <span class="cs-icon" style="background:var(--orange-500);"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M4 4h16v12H8l-4 4z"/></svg></span>
      <span><?php esc_html_e( 'درخواست مشاوره', 'uid-theme' ); ?></span>
    </button>
    <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="cs-item">
      <span class="cs-icon" style="background:var(--navy-900);"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg></span>
      <span><?php esc_html_e( 'تماس تلفنی', 'uid-theme' ); ?> — <span dir="ltr"><?php echo esc_html( uid_phone_display() ); ?></span></span>
    </a>
    <a href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener" class="cs-item">
      <span class="cs-icon" style="background:#29A9E0;"><svg viewBox="0 0 24 24" fill="#fff"><path d="M21.5 3.5L2.7 10.9c-1.2.5-1.2 1.2-.2 1.5l4.8 1.5 1.9 5.7c.2.6.4.8.8.8.4 0 .6-.2.8-.5l2-1.9 4.2 3.1c.7.4 1.2.2 1.4-.7l3-13.9c.3-1.1-.4-1.6-1.4-1.2zM8.3 14.1l9.2-5.8c.4-.3.8-.1.5.2l-7.6 6.9-.3 3-1.4-4.3z"/></svg></span>
      <span><?php esc_html_e( 'تلگرام', 'uid-theme' ); ?></span>
    </a>
    <a href="<?php echo esc_url( uid_whatsapp_url() ); ?>" target="_blank" rel="noopener" class="cs-item">
      <span class="cs-icon" style="background:#25D366;"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2a10 10 0 00-8.5 15.2L2 22l4.9-1.3A10 10 0 1012 2z"/></svg></span>
      <span><?php esc_html_e( 'واتساپ', 'uid-theme' ); ?></span>
    </a>
    <button type="button" class="cs-cancel" id="csCancel"><?php esc_html_e( 'انصراف', 'uid-theme' ); ?></button>
  </div>
</div>

<nav class="mobile-navbar">
  <button class="mnav-item active" data-target="#hero"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 11l8-7 8 7M6 10v10h12V10"/></svg><span><?php esc_html_e( 'خانه', 'uid-theme' ); ?></span></button>
  <button class="mnav-item" data-target="#services"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg><span><?php esc_html_e( 'خدمات', 'uid-theme' ); ?></span></button>
  <button class="mnav-item raised" id="mnavLead"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg><span><?php esc_html_e( 'تماس', 'uid-theme' ); ?></span></button>
  <button class="mnav-item" data-target="#resources"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 016.5 22H20V2H6.5A2.5 2.5 0 004 4.5v15z"/></svg><span><?php esc_html_e( 'وبلاگ', 'uid-theme' ); ?></span></button>
  <button class="mnav-item" id="mnavMore"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg><span><?php esc_html_e( 'منو', 'uid-theme' ); ?></span></button>
</nav>

<?php wp_footer(); ?>
</body>
</html>
