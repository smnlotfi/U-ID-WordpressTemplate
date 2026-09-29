<?php
/**
 * قالب فوتر سایت — شامل فوتر اصلی و ابزارک‌های شناور سراسری (دکمه تماس، شیت
 * موبایل، مودال مشاوره)
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<footer>
  <div class="wrap">

    <div class="f-contact">
      <div class="tx">
        <b><?php esc_html_e( 'سؤالی درباره وب‌سرویس‌ها دارید؟', 'uid-theme' ); ?></b>
        <span><?php esc_html_e( 'کارشناس یوآیدی تعرفه و کلید آزمایشی را برایتان آماده می‌کند', 'uid-theme' ); ?></span>
      </div>
      <div class="acts">
        <a class="f-btn phone" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg><span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span></a>
        <button class="f-btn ghost" type="button" data-open-lead-modal><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg><?php esc_html_e( 'مشاوره رایگان', 'uid-theme' ); ?></button>
      </div>
    </div>

    <div class="f-grid">
      <div class="f-brand">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
          <?php uid_logo_html(); ?>
        </a>
        <p><?php echo esc_html( uid_get_option( 'uid_footer_options', 'description', __( 'زیرساخت احراز هویت دیجیتال برای بانک‌ها، صرافی‌ها و فین‌تک‌های ایران — یک API، همه سامانه‌های رسمی کشور.', 'uid-theme' ) ) ); ?></p>
        <div class="f-social">
          <?php $ig = uid_get_option( 'uid_footer_options', 'instagram', '' ); if ( $ig ) : ?>
          <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'اینستاگرام', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg></a>
          <?php endif; ?>
          <?php $li = uid_get_option( 'uid_footer_options', 'linkedin', '' ); if ( $li ) : ?>
          <a href="<?php echo esc_url( $li ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'لینکدین', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="3"/><path d="M7 10v7M7 7v.01M11 17v-4.5a2.5 2.5 0 015 0V17M11 10v7"/></svg></a>
          <?php endif; ?>
          <?php $tg = uid_telegram_url(); if ( $tg ) : ?>
          <a href="<?php echo esc_url( $tg ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'تلگرام', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3L18.6 20c-.25 1.1-.9 1.37-1.83.85l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.46c.4-.36-.09-.56-.63-.2L5.79 12.47.83 10.92c-1.08-.34-1.1-1.08.23-1.6L20.5 2.73c.9-.33 1.69.2 1.4 1.57z"/></svg></a>
          <?php endif; ?>
        </div>
      </div>

      <details class="f-col" open>
        <summary><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col1_title', __( 'سرویس‌های هویتی', 'uid-theme' ) ) ); ?><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></summary>
        <?php uid_render_footer_menu( '1' ); ?>
      </details>

      <details class="f-col" open>
        <summary><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col2_title', __( 'استعلام و فناوری', 'uid-theme' ) ) ); ?><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></summary>
        <?php uid_render_footer_menu( '2' ); ?>
      </details>

      <details class="f-col" open>
        <summary><?php echo esc_html( uid_get_option( 'uid_footer_options', 'col3_title', __( 'یوآیدی و منابع', 'uid-theme' ) ) ); ?><svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></summary>
        <?php uid_render_footer_menu( '3' ); ?>
      </details>
    </div>

    <div class="f-bot">
      <p><?php echo esc_html( uid_get_option( 'uid_footer_options', 'copyright', __( '© ۱۴۰۵ یوآیدی. تمامی حقوق محفوظ است.', 'uid-theme' ) ) ); ?></p>
      <div class="f-legal">
        <a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'uid-theme' ); ?></a>
        <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'قوانین و مقررات', 'uid-theme' ); ?></a>
      </div>
      <!-- TRUST BADGES ▸ نشان‌های ای‌نماد و ساماندهی سایت فعلی اینجا قرار بگیرند -->
      <div class="f-badges"></div>
    </div>
  </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'uid-theme' ); ?>">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>

<!-- ================= STICKY CONTACT FAB (desktop) ================= -->
<div class="cfab-scrim" id="cfabScrim"></div>

<div class="cfab-panel" id="cfabPanel" role="dialog" aria-modal="false" aria-labelledby="cfabTitle">
  <div class="cfab-head">
    <b id="cfabTitle"><?php esc_html_e( 'از چه راهی در ارتباط باشیم؟', 'uid-theme' ); ?></b>
    <span><?php esc_html_e( 'کارشناس یوآیدی در ساعات پاسخگویی همراه شماست', 'uid-theme' ); ?></span>
  </div>
  <div class="cfab-rows">
    <a class="cfab-row" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>">
      <span class="ic ic-phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'تماس تلفنی', 'uid-theme' ); ?></b><span class="sub mono"><?php echo esc_html( uid_phone_display() ); ?></span></span>
      <svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <a class="cfab-row" href="<?php echo esc_url( uid_whatsapp_url() ); ?>" target="_blank" rel="noopener">
      <span class="ic ic-whats"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm0 18a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20zm4.5-5.8c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.6 6.6 0 01-3.2-2.8c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3A3 3 0 006 9.9c0 1.7 1.3 3.4 1.5 3.6a10 10 0 003.8 3.3c1.8.7 1.8.5 2.2.4a2.6 2.6 0 001.7-1.2 2.1 2.1 0 00.2-1.2z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'واتساپ', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'گفتگوی متنی با پشتیبانی', 'uid-theme' ); ?></span></span>
      <svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <a class="cfab-row" href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener">
      <span class="ic ic-tg"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3L18.6 20c-.25 1.1-.9 1.37-1.83.85l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.46c.4-.36-.09-.56-.63-.2L5.79 12.47.83 10.92c-1.08-.34-1.1-1.08.23-1.6L20.5 2.73c.9-.33 1.69.2 1.4 1.57z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'تلگرام', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'ارسال پیام به کانال پشتیبانی', 'uid-theme' ); ?></span></span>
      <svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <button class="cfab-row" type="button" data-open-lead-modal>
      <span class="ic ic-form"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'مشاوره رایگان', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'کلید آزمایشی و تعرفه اختصاصی', 'uid-theme' ); ?></span></span>
      <svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </button>
  </div>
  <!-- TODO ▸ ساعات دقیق پاسخگویی از کلاینت گرفته و جایگزین شود. -->
  <div class="cfab-foot"><i></i><?php esc_html_e( 'کارشناس یوآیدی در ساعات پاسخگویی تماس می‌گیرد', 'uid-theme' ); ?></div>
</div>

<button class="cfab" id="cfabBtn" aria-expanded="false" aria-controls="cfabPanel">
  <span class="cfab-ico">
    <svg class="ico-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/></svg>
    <svg class="ico-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </span>
  <span class="cfab-label"><?php esc_html_e( 'در ارتباط باشید', 'uid-theme' ); ?></span>
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

<!-- ================= MOBILE CONTACT SHEET + BOTTOM NAV ================= -->
<div class="csheet-scrim" id="csheetScrim"></div>

<div class="csheet" id="csheet" role="dialog" aria-modal="true" aria-labelledby="csheetTitle">
  <div class="csheet-grip" aria-hidden="true"></div>
  <div class="csheet-head">
    <b id="csheetTitle"><?php esc_html_e( 'از چه راهی در ارتباط باشیم؟', 'uid-theme' ); ?></b>
    <span><?php esc_html_e( 'یکی از راه‌های زیر را انتخاب کنید', 'uid-theme' ); ?></span>
  </div>

  <a class="csheet-call" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>
    <?php esc_html_e( 'تماس تلفنی', 'uid-theme' ); ?> <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span>
  </a>

  <div class="csheet-grid">
    <a class="csheet-tile" href="<?php echo esc_url( uid_whatsapp_url() ); ?>" target="_blank" rel="noopener">
      <span class="ic ic-whats"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm0 18a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20zm4.5-5.8c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.5.1l-.7.9c-.1.2-.3.2-.5.1a6.6 6.6 0 01-3.2-2.8c-.1-.2 0-.4.1-.5l.4-.5.2-.4v-.4l-.7-1.7c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3A3 3 0 006 9.9c0 1.7 1.3 3.4 1.5 3.6a10 10 0 003.8 3.3c1.8.7 1.8.5 2.2.4a2.6 2.6 0 001.7-1.2 2.1 2.1 0 00.2-1.2z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'واتساپ', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'گفتگوی متنی', 'uid-theme' ); ?></span></span>
    </a>
    <a class="csheet-tile" href="<?php echo esc_url( uid_telegram_url() ); ?>" target="_blank" rel="noopener">
      <span class="ic ic-tg"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3L18.6 20c-.25 1.1-.9 1.37-1.83.85l-5.05-3.72-2.44 2.35c-.27.27-.5.5-1.02.5l.36-5.14 9.36-8.46c.4-.36-.09-.56-.63-.2L5.79 12.47.83 10.92c-1.08-.34-1.1-1.08.23-1.6L20.5 2.73c.9-.33 1.69.2 1.4 1.57z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'تلگرام', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'ارسال پیام', 'uid-theme' ); ?></span></span>
    </a>
    <button class="csheet-tile wide" type="button" data-open-lead-modal>
      <span class="ic ic-form"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8z"/></svg></span>
      <span class="tx"><b><?php esc_html_e( 'مشاوره رایگان و کلید آزمایشی', 'uid-theme' ); ?></b><span class="sub"><?php esc_html_e( 'فرم کوتاه، کارشناس تماس می‌گیرد', 'uid-theme' ); ?></span></span>
    </button>
  </div>

  <!-- TODO ▸ ساعات دقیق پاسخگویی از کلاینت گرفته و جایگزین شود. -->
  <div class="csheet-foot"><i></i><?php esc_html_e( 'کارشناس یوآیدی در ساعات پاسخگویی تماس می‌گیرد', 'uid-theme' ); ?></div>
</div>

<nav class="mobile-navbar" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'uid-theme' ); ?>">
  <a class="mnav-item" href="<?php echo esc_url( home_url( '/' ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5L12 3l9 7.5V20a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 20z"/></svg><span><?php esc_html_e( 'خانه', 'uid-theme' ); ?></span></a>

  <a class="mnav-item" href="<?php echo esc_url( home_url( '/api/' ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg><span><?php esc_html_e( 'وب‌سرویس‌ها', 'uid-theme' ); ?></span></a>

  <button class="mnav-fab" id="mnavFab" aria-expanded="false" aria-controls="csheet" aria-label="<?php esc_attr_e( 'باز کردن راه‌های ارتباطی', 'uid-theme' ); ?>">
    <svg class="ico-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 01-9 8.4 8.9 8.9 0 01-4-.9L3 20.5l1.5-4.4A8.4 8.4 0 013 11.5a8.4 8.4 0 019-8.4 8.4 8.4 0 019 8.4z"/><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/></svg>
    <svg class="ico-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
  </button>

  <a class="mnav-item" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg><span><?php esc_html_e( 'تماس', 'uid-theme' ); ?></span></a>

  <button class="mnav-item" id="mnavMore" aria-label="<?php esc_attr_e( 'باز کردن منو', 'uid-theme' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg><span><?php esc_html_e( 'منو', 'uid-theme' ); ?></span></button>
</nav>

<?php wp_footer(); ?>
</body>
</html>
