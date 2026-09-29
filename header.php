<?php
/**
 * قالب هدر سایت
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="scroll-progress" id="scrollProgress"></div>

<header id="siteHeader">
  <div class="nav-pill">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
      <?php uid_logo_html(); ?>
    </a>

    <nav class="main-nav" id="mainNav">
      <span class="nav-highlight" id="navHighlight"></span>
      <?php uid_render_primary_nav(); ?>
    </nav>

    <div class="nav-actions">
      <?php if ( uid_get_option( 'uid_header_options', 'show_login', true ) ) : ?>
        <a href="<?php echo esc_url( uid_get_option( 'uid_header_options', 'login_url', '#contact' ) ); ?>" class="btn btn-outline btn-sm">
          <?php echo esc_html( uid_get_option( 'uid_header_options', 'login_text', __( 'ورود به پنل', 'uid-theme' ) ) ); ?>
        </a>
      <?php endif; ?>

      <?php if ( uid_get_option( 'uid_header_options', 'show_phone', true ) ) : ?>
        <a href="<?php echo esc_attr( uid_phone_href( uid_phone_raw() ) ); ?>" class="btn btn-primary btn-sm btn-shine phone-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3.1 19.5 19.5 0 01-6-6A19.8 19.8 0 012.1 4.2 2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.6 2.7a2 2 0 01-.4 2.1L8.1 9.7a16 16 0 006 6l1.2-1.2a2 2 0 012.1-.4c.9.3 1.8.5 2.7.6a2 2 0 011.9 2.2z"/></svg>
          <span dir="ltr"><?php echo esc_html( uid_phone_display() ); ?></span>
        </a>
      <?php endif; ?>

      <button class="nav-toggle" id="navToggle" aria-label="<?php esc_attr_e( 'باز کردن منو', 'uid-theme' ); ?>">
        <svg class="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        <svg class="icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
  </div>
</header>
