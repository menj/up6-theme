<?php
/**
 * Search results template — full width, no sidebar.
 * Both states (results/no-results) share the archive-hero banner.
 * No-results: editorial empty state centred below the hero.
 */
get_header();

$search_query  = get_search_query();
$has_results   = have_posts();
?>

<!-- ── Archive hero — shown on both states ──────────────────── -->
<div class="archive-hero">
  <div class="archive-hero-inner">
    <p class="archive-label"><?php esc_html_e( 'Search Results', 'up6' ); ?></p>
    <h1 class="archive-title">
      <?php
      printf(
        esc_html__( 'Results for: %s', 'up6' ),
        '<em>' . esc_html( $search_query ) . '</em>'
      );
      ?>
    </h1>
    <?php if ( $has_results ) : ?>
    <p class="archive-description">
      <?php
      printf(
        esc_html( _n( '%s article found', '%s articles found', $wp_query->found_posts, 'up6' ) ),
        number_format_i18n( $wp_query->found_posts )
      );
      ?>
    </p>
    <?php endif; ?>
  </div>
</div>

<?php if ( $has_results ) : ?>

<!-- ── Results found ────────────────────────────────────────── -->
<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <main id="main" class="site-main search-main" role="main">

    <div class="articles-grid">
      <?php while ( have_posts() ) : the_post(); ?>
      <?php get_template_part( 'template-parts/card' ); ?>
      <?php endwhile; ?>
    </div>

    <?php the_posts_pagination( [ 'class' => 'pagination' ] ); ?>

  </main>
</div>

<?php else : ?>

<!-- ── No results ───────────────────────────────────────────── -->
<div class="site-content-inner search-empty-wrap">
  <div class="search-empty">
    <p class="search-empty-label">
      <span class="section-dot" aria-hidden="true"></span>
      <?php esc_html_e( 'No Results', 'up6' ); ?>
    </p>
    <p class="search-empty-message">
      <?php esc_html_e( 'No articles matched your search. Try a different spelling or a broader term.', 'up6' ); ?>
    </p>
    <a class="search-empty-home" href="<?php echo esc_url( home_url() ); ?>">
      <?php esc_html_e( '← Back to Home', 'up6' ); ?>
    </a>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
