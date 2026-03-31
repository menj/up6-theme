<?php
/**
 * Template Name: Advertise
 * Template Post Type: page
 *
 * Media kit / advertising information page.
 *
 * @package UP6
 * @since   2.8
 */
get_header();
?>

<div class="site-content-inner policy-main">
  <article class="policy-content">

    <header class="policy-header">
      <h1 class="entry-title"><?php esc_html_e( 'Advertise with UP6', 'up6' ); ?></h1>
    </header>

    <h2><?php esc_html_e( 'Why Advertise with Us', 'up6' ); ?></h2>
    <p><?php esc_html_e( 'UP6 Suara Semasa reaches an engaged Malaysian readership interested in politics, economics, society, sports, technology, and culture. Our audience consists of educated, digitally active readers who value independent journalism and in-depth analysis.', 'up6' ); ?></p>

    <h2><?php esc_html_e( 'Available Formats', 'up6' ); ?></h2>
    <p><?php esc_html_e( 'We offer a range of advertising opportunities designed to integrate naturally with our editorial content while maintaining clear separation between news and commercial material:', 'up6' ); ?></p>
    <p>
      <?php esc_html_e( 'Display advertising (banner, sidebar, interstitial), sponsored content and branded articles (clearly labelled), newsletter sponsorship, and event partnership. All advertising is subject to editorial review and must comply with our advertising standards.', 'up6' ); ?>
    </p>

    <h2><?php esc_html_e( 'Editorial Independence', 'up6' ); ?></h2>
    <p><?php esc_html_e( 'Advertising does not influence editorial decisions. Sponsored content is always clearly labelled. Our editorial team operates independently from our commercial team. Advertisers do not receive advance notice of editorial coverage, favourable treatment in news reporting, or the ability to review or approve editorial content.', 'up6' ); ?></p>

    <h2><?php esc_html_e( 'Contact', 'up6' ); ?></h2>
    <p>
      <?php
      printf(
        esc_html__( 'For advertising enquiries, rate cards, and media kit requests, please contact us at %s or use the contact form on our %s.', 'up6' ),
        '<a href="mailto:' . esc_attr( up6_opt( 'ss_contact_email' ) ) . '">' . esc_html( up6_opt( 'ss_contact_email' ) ) . '</a>',
        '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact page', 'up6' ) . '</a>'
      );
      ?>
    </p>

  </article>
</div>

<?php get_footer(); ?>
