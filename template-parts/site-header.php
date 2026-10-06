<?php
/**
 * هدر سراسری سایت — توسط inc/global-chrome.php روی همه‌ی صفحات رندر می‌شود
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="scroll-progress" id="scrollProgress"></div>

<div class="navwrap">
  <nav class="nav" aria-label="<?php esc_attr_e( 'ناوبری اصلی', 'uid-theme' ); ?>">
    <a class="brand logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'یوآیدی — صفحه اصلی', 'uid-theme' ); ?>">
      <?php uid_logo_html(); ?>
    </a>

    <div class="navlinks" id="navlinks">
      <span id="navblob" aria-hidden="true"></span>
      <a class="navlink" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'uid-theme' ); ?></a>
      <?php uid_render_nav_links(); ?>
    </div>

    <?php uid_render_mega_panel(); ?>

    <div class="navcta">
      <?php if ( uid_get_option( 'uid_header_options', 'show_phone', true ) ) : ?>
        <a class="navphone" href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1 1 .4 1.9.7 2.8a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.3-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.8.7a2 2 0 011.7 2z"/></svg>
          <span class="num mono"><?php echo esc_html( uid_phone_display() ); ?></span>
        </a>
      <?php endif; ?>
      <button class="burger" id="burger" aria-label="<?php esc_attr_e( 'باز کردن منو', 'uid-theme' ); ?>" aria-expanded="false"><span></span></button>
    </div>
  </nav>
</div>

<div class="msheet" id="msheet">
  <?php uid_render_mobile_sheet(); ?>
</div>
