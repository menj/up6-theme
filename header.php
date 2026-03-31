<?php
/**
 * Site Header
 *
 * Three-row sticky header:
 *   Row 1 — Top bar: Hijri date (live, today), secondary navigation
 *   Row 2 — Brand bar: logo + festive icon, desktop search, dark mode toggle
 *   Row 3 — Primary navigation: category menu items, mobile hamburger
 *
 * Mobile drawer: hamburger → full-screen nav overlay with search, primary
 * links, secondary links. Scroll progress bar (red line, bottom of header).
 *
 * Dark mode: <script> in <head> applies 'up6-dark' class before first paint
 * to prevent flash-of-light-mode. Preference stored in localStorage.
 *
 * The data-print-footer attribute on <body> provides a translatable string
 * for the print stylesheet's footer line (CSS attr() reads it).
 *
 * @package UP6
 * @since   2.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
  <script>
    // Apply dark mode before paint to prevent flash
    if ( localStorage.getItem( 'up6_theme' ) === 'dark' ) {
      document.documentElement.classList.add( 'up6-dark' );
    }
  </script>
</head>
<body <?php body_class(); ?> data-print-footer="<?php echo esc_attr( sprintf( __( 'Printed from %s', 'up6' ), get_bloginfo( 'name' ) . ' · ' . home_url() ) ); ?>">
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'up6' ); ?></a>

<div id="page" class="site">

  <header id="masthead" class="site-header" role="banner">

    <!-- ── Row 1: Brand + Search ── -->
    <div class="header-brand-row">
      <div class="header-inner">

        <!-- Hamburger (mobile) -->
        <button class="menu-toggle" aria-controls="mobile-nav" aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Toggle navigation', 'up6' ); ?>">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
          </svg>
        </button>

        <!-- Brand -->
        <div class="site-branding">
          <?php if ( has_custom_logo() ) : ?>
            <?php the_custom_logo(); ?>
          <?php else : ?>
          <div class="logo-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
            </svg>
          </div>
          <?php endif; ?>
          <div class="site-branding-text">
            <?php up6_logo(); ?>
            <span class="site-tagline"><?php bloginfo( 'description' ); ?></span>
          </div>
          <?php up6_festive_icon(); ?>
        </div>

        <!-- Search -->
        <div class="header-search">
          <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search news&hellip;', 'up6' ); ?>"
                   value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
            <button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'up6' ); ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                   stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
            </button>
          </form>
        </div>

        <div class="header-icons">

        <!-- Mobile search toggle (hidden on desktop) -->
        <button class="header-icon-btn mobile-search-toggle" id="up6-mobile-search-toggle"
                aria-label="<?php esc_attr_e( 'Search', 'up6' ); ?>"
                aria-expanded="false" aria-controls="mobile-search-bar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
        </button>

        <!-- RSS icon (desktop only) -->
        <a href="<?php echo esc_url( get_feed_link() ); ?>" class="header-icon-btn desktop-only" aria-label="<?php esc_attr_e( 'RSS Feed', 'up6' ); ?>" title="<?php esc_attr_e( 'RSS Feed', 'up6' ); ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/>
          </svg>
        </a>

        <!-- Light/dark mode toggle -->
        <button class="header-icon-btn header-theme-toggle" id="up6-theme-toggle"
                aria-label="<?php esc_attr_e( 'Toggle dark mode', 'up6' ); ?>"
                title="<?php esc_attr_e( 'Toggle dark mode', 'up6' ); ?>">
          <!-- Sun icon (shown in dark mode) -->
          <svg class="icon-sun" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 7a5 5 0 1 1 0 10A5 5 0 0 1 12 7zm0-5a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V3a1 1 0 0 1 1-1zm0 16a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zM4.22 5.64a1 1 0 0 1 1.41-1.41l1.42 1.41a1 1 0 1 1-1.42 1.42L4.22 5.64zm12.72 12.72a1 1 0 0 1 1.41-1.41l1.42 1.41a1 1 0 1 1-1.41 1.41l-1.42-1.41zM3 12a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2H4a1 1 0 0 1-1-1zm16 0a1 1 0 0 1 1-1h2a1 1 0 0 1 0 2h-2a1 1 0 0 1-1-1zM4.22 18.36l1.42-1.41a1 1 0 1 1 1.41 1.41l-1.41 1.42a1 1 0 1 1-1.42-1.42zM17 6.05l1.41-1.42a1 1 0 1 1 1.42 1.41l-1.42 1.42A1 1 0 1 1 17 6.05z"/>
          </svg>
          <!-- Moon icon (shown in light mode) -->
          <svg class="icon-moon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-3.14-9.8c-.44-.06-.9-.1-1.36-.1z"/>
          </svg>
        </button>

        </div><!-- .header-icons -->

      </div><!-- .header-inner -->
    </div><!-- .header-brand-row -->

    <!-- ── Mobile search bar (slide down) ── -->
    <div id="mobile-search-bar" class="mobile-search-bar" aria-hidden="true">
      <div class="mobile-search-inner">
        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
          <input type="search" class="mobile-search-field" id="mobile-search-input"
                 placeholder="<?php esc_attr_e( 'Search news…', 'up6' ); ?>"
                 value="<?php echo esc_attr( get_search_query() ); ?>" name="s"
                 autocomplete="off" />
          <button type="submit" class="mobile-search-submit" aria-label="<?php esc_attr_e( 'Search', 'up6' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </button>
        </form>
      </div>
    </div><!-- .mobile-search-bar -->

    <!-- ── Row 2: Primary category nav + date ── -->
    <div class="header-nav-row">
      <div class="header-inner">
        <div class="primary-nav-scroll">
        <?php
        wp_nav_menu( [
          'theme_location' => 'primary',
          'menu_id'        => 'primary-menu',
          'menu_class'     => 'primary-navigation',
          'container'      => false,
          'depth'          => 0,
          'fallback_cb'    => function() {
            echo '<ul id="primary-menu" class="primary-navigation">';
            $cats = get_categories( [ 'orderby' => 'menu_order', 'order' => 'ASC', 'number' => 8, 'hide_empty' => false ] );
            foreach ( $cats as $cat ) {
              $active = is_category( $cat->term_id ) ? ' class="current-menu-item"' : '';
              echo '<li' . $active . '><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
            }
            echo '</ul>';
          },
        ] );
        ?>
        </div><!-- .primary-nav-scroll -->
        <div class="nav-date" aria-hidden="true">
          <span class="nav-date-ce"><?php echo esc_html( date_i18n( 'l, j F Y' ) ); ?></span>
          <span class="nav-date-sep" aria-hidden="true">●</span>
          <span class="nav-date-hijri"><?php echo esc_html( up6_hijri_date()['formatted'] ); ?></span>
        </div>
      </div>
    </div><!-- .header-nav-row -->

  </header><!-- #masthead -->

  <!-- Mobile nav drawer (toggled by JS) -->
  <div id="mobile-nav" class="mobile-nav-drawer" aria-hidden="true">
    <?php
    wp_nav_menu( [
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'mobile-nav-list',
      'fallback_cb'    => function() {
        echo '<ul class="mobile-nav-list">';
        $cats = get_categories( [ 'number' => 8, 'hide_empty' => false ] );
        foreach ( $cats as $cat ) {
          echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
        }
        echo '</ul>';
      },
    ] );
    ?>
    <?php if ( has_nav_menu( 'secondary' ) ) : ?>
    <div class="mobile-nav-utility">
      <?php
      wp_nav_menu( [
        'theme_location' => 'secondary',
        'container'      => false,
        'menu_class'     => 'mobile-utility-list',
        'depth'          => 1,
        'fallback_cb'    => false,
      ] );
      ?>
    </div>
    <?php endif; ?>
  </div>

  <div id="content" class="site-content">
