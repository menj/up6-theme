<?php
/**
 * Template Name: About Page
 * Template Post Type: page
 *
 * About / Mengenai UP6 Suara Semasa.
 * File naming: en-US per UP6 convention.
 * Content strings: en-US source, translated via ms_MY.po.
 *
 * @package UP6
 */

get_header();
?>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <main id="main" class="site-main policy-main" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <header class="policy-header">
        <p class="policy-header-label">
          <span class="section-dot" aria-hidden="true"></span>
          <?php esc_html_e( 'About Us', 'up6' ); ?>
        </p>
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <p class="about-tagline"><?php esc_html_e( '&ldquo;Berita Malaysia, Cakrawala Dunia&rdquo;', 'up6' ); ?></p>
      </header>

      <?php ob_start(); ?>
      <div class="policy-content">

        <h2><?php esc_html_e( 'Who We Are', 'up6' ); ?></h2>
        <p>
          <?php
          // Build category links dynamically — if a slug changes, the link follows
          $cat_link = function ( $slug ) {
              $cat = get_category_by_slug( $slug );
              return $cat ? esc_url( get_category_link( $cat->term_id ) ) : '#';
          };
          printf(
            /* translators: %1$s: Malaysia (Wikipedia preview), %2$s: antarabangsa link, %3$s: politik link, %4$s: ekonomi link, %5$s: masyarakat link, %6$s: sukan link, %7$s: pasaran kewangan link, %8$s: Bahasa Melayu (Wikipedia preview) */
            esc_html__( 'UP6 Suara Semasa is a digital news portal focusing on coverage of current affairs in %1$s and %2$s developments, encompassing %3$s, %4$s, technology, %5$s, religion, %6$s and %7$s. The portal was established to provide timely information in %8$s with clear, organised and accessible delivery to readers of all backgrounds.', 'up6' ),
            '<span data-wikipedia-preview data-wp-title="Malaysia" data-wp-lang="ms" class="wmf-wp-with-preview">Malaysia</span>',
            '<a href="' . $cat_link( 'global' )   . '">' . esc_html__( 'international', 'up6' )    . '</a>',
            '<a href="' . $cat_link( 'politik' )  . '">' . esc_html__( 'politics', 'up6' )         . '</a>',
            '<a href="' . $cat_link( 'ekonomi' )  . '">' . esc_html__( 'economics', 'up6' )        . '</a>',
            '<a href="' . $cat_link( 'masyarakat' ) . '">' . esc_html__( 'society', 'up6' )        . '</a>',
            '<a href="' . $cat_link( 'sukan' )    . '">' . esc_html__( 'sports', 'up6' )           . '</a>',
            '<a href="' . $cat_link( 'pasaran' )  . '">' . esc_html__( 'financial markets', 'up6' ) . '</a>',
            '<span data-wikipedia-preview data-wp-title="Bahasa Melayu" data-wp-lang="ms" class="wmf-wp-with-preview">' . esc_html__( 'Bahasa Melayu', 'up6' ) . '</span>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Editorial Approach', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The UP6 editorial approach prioritises factual accuracy, contextual clarity and precise use of language. Every report is prepared from verified sources and thorough research so that readers obtain a comprehensive picture of an event or current issue. Focus is given to domestic developments in Malaysia alongside global coverage that has a direct or indirect impact on this region.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We hold ourselves to the standards set out in our Editorial Policy — accuracy, fairness, editorial independence and transparency in the use of sources. Corrections are published promptly and without reservation. Readers who identify errors are encouraged to write to us.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Literature and Culture', 'up6' ); ?></h2>
        <p>
          <?php
          printf(
            /* translators: %1$s: sastera link, %2$s: puisi link */
            esc_html__( 'Beyond news reporting, UP6 Suara Semasa also opens space for %1$s through the publication of %2$s, short fiction, literary criticism and book reviews. This initiative aims to strengthen discourse on Malay language, culture and thought within the contemporary digital media ecosystem.', 'up6' ),
            '<a href="' . $cat_link( 'sastera' ) . '">' . esc_html__( 'literary content', 'up6' ) . '</a>',
            '<a href="' . $cat_link( 'puisi' )   . '">' . esc_html__( 'poetry', 'up6' )           . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Ownership', 'up6' ); ?></h2>
        <p>
          <?php
          printf(
            /* translators: %s: Langgam Fikir Enterprise link */
            esc_html__( 'UP6 Suara Semasa is owned and operated by %s, a publishing and digital content entity focused on the production of original works, current affairs analysis and the development of intellectual discourse in Bahasa Melayu.', 'up6' ),
            '<a href="https://langgamfikir.my" target="_blank" rel="noopener">Langgam Fikir Enterprise</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'How We Operate', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The portal operates entirely digitally with an emphasis on fast access, high readability and consistent delivery of information to readers in Malaysia and internationally. UP6 Suara Semasa is committed to operating free of interference from any government body, law enforcement agency, regulatory authority or political entity — editorial decisions rest solely with the editorial team.', 'up6' ); ?></p>
        <p>
          <?php
          printf(
            /* translators: %1$s: editorial policy page link, %2$s: privacy policy page link */
            esc_html__( 'For more on how we work, see our %1$s and %2$s.', 'up6' ),
            '<a href="' . esc_url( home_url( '/dasar-editorial-up6-suara-semasa/' ) ) . '">' . esc_html__( 'Editorial Policy', 'up6' ) . '</a>',
            '<a href="' . esc_url( home_url( '/dasar-privasi-up6-suara-semasa/' ) ) . '">'  . esc_html__( 'Privacy Policy', 'up6' )   . '</a>'
          );
          ?>
        </p>

      </div><!-- .policy-content -->
      <?php echo up6_brand_inline( ob_get_clean() ); ?>

    </article>
    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
