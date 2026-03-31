<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Hubungi Kami — contact page with CF7 form, NAP details,
 * Google Maps embed, and NewsMediaOrganization JSON-LD.
 *
 * Requires in Theme Options → Contact tab:
 *   up6_cf7_form_id    — CF7 numeric form ID
 *   up6_maps_api_key   — Google Maps Embed API key
 *   up6_maps_place_id  — Google Place ID (ChIJ…)
 *
 * Contact details (address/phone/email) are shared with the
 * footer and are set in Theme Options → Footer tab.
 *
 * File naming: en-US per UP6 convention.
 * WordPress page title / slug: set by site owner in ms-MY (e.g. "Hubungi Kami").
 * Content strings: en-US source, translated via ms_MY.po.
 *
 * @package UP6
 * @since   2.5.106
 */

get_header();

// ── Contact details (shared with footer) ─────────────────────
$up6_address = up6_opt( 'ss_contact_address' );
$up6_phone   = up6_opt( 'ss_contact_phone' );
$up6_email   = up6_opt( 'ss_contact_email' );

// ── Maps ──────────────────────────────────────────────────────
$up6_maps_key      = up6_opt( 'up6_maps_api_key' );
$up6_maps_place_id = up6_opt( 'up6_maps_place_id' );
$up6_show_map      = ! empty( $up6_maps_key ) && ! empty( $up6_maps_place_id );

// ── CF7 form ──────────────────────────────────────────────────
$up6_cf7_id   = absint( up6_opt( 'up6_cf7_form_id' ) );
$up6_cf7_ready = $up6_cf7_id > 0 && function_exists( 'wpcf7_contact_form' );
?>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <main id="main" class="site-main contact-main" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <!-- ── Page header ──────────────────────────────────── -->
      <header class="policy-header contact-header">
        <p class="policy-header-label">
          <span class="section-dot" aria-hidden="true"></span>
          <?php esc_html_e( 'Get In Touch', 'up6' ); ?>
        </p>
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>

      <!-- ── Two-column body ──────────────────────────────── -->
      <div class="contact-layout">

        <!-- Left: intro + NAP ─────────────────────────────── -->
        <div class="contact-info-col">

          <p class="contact-intro">
            <?php esc_html_e( 'We welcome questions, tips, corrections, and feedback from readers, sources, and the public. The editorial team reads every message and will respond within three working days.', 'up6' ); ?>
          </p>
          <p class="contact-intro">
            <?php esc_html_e( 'For press enquiries, partnership proposals, or advertising, please indicate the nature of your message in the subject field.', 'up6' ); ?>
          </p>

          <!-- NAP block -->
          <address class="contact-nap" itemscope itemtype="https://schema.org/NewsMediaOrganization">
            <meta itemprop="name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
            <meta itemprop="url"  content="<?php echo esc_attr( home_url( '/' ) ); ?>" />

            <?php if ( $up6_address ) : ?>
            <div class="contact-nap-item" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
              <span class="contact-nap-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </span>
              <span class="contact-nap-text" itemprop="streetAddress"><?php echo nl2br( esc_html( $up6_address ) ); ?></span>
            </div>
            <?php endif; ?>

            <?php if ( $up6_phone ) : ?>
            <div class="contact-nap-item">
              <span class="contact-nap-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </span>
              <a class="contact-nap-text" href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $up6_phone ) ); ?>" itemprop="telephone"><?php echo esc_html( $up6_phone ); ?></a>
            </div>
            <?php endif; ?>

            <?php if ( $up6_email ) : ?>
            <div class="contact-nap-item">
              <span class="contact-nap-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
              </span>
              <a class="contact-nap-text" href="mailto:<?php echo esc_attr( $up6_email ); ?>" itemprop="email"><?php echo esc_html( $up6_email ); ?></a>
            </div>
            <?php endif; ?>

          </address><!-- .contact-nap -->

        </div><!-- .contact-info-col -->

        <!-- Right: CF7 form ───────────────────────────────── -->
        <div class="contact-form-col">
          <?php if ( $up6_cf7_ready ) : ?>
            <div class="contact-form-wrap">
              <?php echo do_shortcode( '[contact-form-7 id="' . $up6_cf7_id . '"]' ); ?>
            </div>
          <?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
            <div class="contact-form-notice">
              <p>
                <strong><?php esc_html_e( 'Admin notice:', 'up6' ); ?></strong>
                <?php
                if ( ! function_exists( 'wpcf7_contact_form' ) ) {
                    esc_html_e( 'Contact Form 7 is not active. Please install and activate the plugin.', 'up6' );
                } else {
                    printf(
                        /* translators: %s: link to Theme Options Contact tab */
                        esc_html__( 'No form ID set. Go to %s and enter your CF7 form ID.', 'up6' ),
                        '<a href="' . esc_url( admin_url( 'themes.php?page=up6-theme-options&tab=contact' ) ) . '">' . esc_html__( 'Theme Options → Contact', 'up6' ) . '</a>'
                    );
                }
                ?>
              </p>
            </div>
          <?php endif; ?>
        </div><!-- .contact-form-col -->

      </div><!-- .contact-layout -->

      <!-- ── Google Maps embed ────────────────────────────── -->
      <?php if ( $up6_show_map ) : ?>
      <div class="contact-map-wrap">
        <iframe
          title="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'Location map', 'up6' ) ); ?>"
          class="contact-map"
          loading="lazy"
          allowfullscreen
          referrerpolicy="no-referrer-when-downgrade"
          src="<?php echo esc_url(
            add_query_arg( [
              'key'     => $up6_maps_key,
              'q'       => 'place_id:' . $up6_maps_place_id,
              'zoom'    => '15',
              'maptype' => 'roadmap',
            ], 'https://www.google.com/maps/embed/v1/place'
            )
          ); ?>">
        </iframe>
      </div>
      <?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
      <div class="contact-map-notice">
        <p>
          <strong><?php esc_html_e( 'Admin notice:', 'up6' ); ?></strong>
          <?php
          printf(
            /* translators: %s: link to Theme Options Contact tab */
            esc_html__( 'Map is hidden. Add your Google Maps API key and Place ID in %s.', 'up6' ),
            '<a href="' . esc_url( admin_url( 'themes.php?page=up6-theme-options&tab=contact' ) ) . '">' . esc_html__( 'Theme Options → Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>
      </div>
      <?php endif; ?>

    </article>

    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
