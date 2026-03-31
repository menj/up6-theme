<?php
/**
 * Front Page / Blog Index Template
 *
 * Homepage layout for UP6 Suara Semasa. Two-column structure:
 *
 * MAIN COLUMN (left):
 *   1. Hero card — pinned sticky post, or most recent post as fallback.
 *      Uses <img> with fetchpriority="high" for LCP optimisation.
 *   2. Category sections — top N categories sorted by post count, each
 *      with an articles grid. Wrapped in transient cache (5-min TTL,
 *      invalidated on save_post) for performance under load.
 *   3. Most Recent — latest posts excluding hero + editor picks.
 *
 * SIDEBAR (right):
 *   Pilihan Editor (editor picks), category list, Most Read panel.
 *   Rendered by sidebar.php via get_sidebar().
 *
 * Cards rendered via template-parts/card.php (single source of truth).
 * Category colours applied via up6_category_colour() on section dots
 * and hero badge.
 *
 * All data from native WP — no plugins needed.
 *
 * @package UP6
 * @since   2.4.0
 * @updated 2.8 — card partial, transient caching, category colours, LCP hero
 */
get_header();
?>

<div class="site-content-inner">
  <div class="content-area-wrap">

    <!-- ===================== MAIN COLUMN ===================== -->
    <main id="main" class="site-main" role="main">

      <?php
      // =============================================================
      // HERO: sticky/featured post → fallback to most recent
      // =============================================================
      $hero_post_id = 0;
      $hero_query   = new WP_Query( [
        'posts_per_page'      => 1,
        'post__in'            => get_option( 'sticky_posts' ),
        'ignore_sticky_posts' => 1,
      ] );
      if ( ! $hero_query->have_posts() ) {
        $hero_query = new WP_Query( [ 'posts_per_page' => 1 ] );
      }

      if ( $hero_query->have_posts() ) :
        $hero_query->the_post();
        $hero_post_id = get_the_ID();
        $hero_cats    = get_the_category();
      ?>
      <section class="section-hero" aria-label="<?php esc_attr_e( 'Featured Story', 'up6' ); ?>">
        <a href="<?php the_permalink(); ?>" class="hero-card">
          <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'ss-hero', [
              'class'         => 'hero-card-img',
              'fetchpriority' => 'high',
              'loading'       => 'eager',
              'decoding'      => 'async',
              'alt'           => get_the_title(),
              'sizes'         => '(max-width: 640px) 100vw, (max-width: 1200px) 100vw, 1200px',
            ] ); ?>
          <?php endif; ?>
          <div class="hero-overlay"></div>
          <div class="hero-body">
            <?php if ( $hero_cats ) : ?>
              <span class="hero-category" style="background:<?php echo esc_attr( up6_category_colour( $hero_cats[0] ) ); ?>">
                <?php echo esc_html( $hero_cats[0]->name ); ?>
              </span>
            <?php endif; ?>
            <h2 class="hero-title"><?php the_title(); ?></h2>
            <div class="hero-meta">
              <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
            </div>
          </div>
        </a>
      </section>
      <?php
      wp_reset_postdata();

      else :
        // --- Clean empty state: no articles at all ---
      ?>
      <section class="section-hero section-hero-empty" aria-label="<?php esc_attr_e( 'Welcome', 'up6' ); ?>">
        <div class="hero-empty-state">
          <h2><?php esc_html_e( 'Welcome to UP6', 'up6' ); ?></h2>
          <p><?php esc_html_e( 'No articles have been published yet. Check back soon.', 'up6' ); ?></p>
        </div>
      </section>
      <?php endif; ?>

      <?php
      // =============================================================
      // CATEGORY SECTIONS: sorted by post count, top N populated
      // Cached in a transient (5-min TTL) — invalidated on save_post
      // =============================================================
      $exclude_ids   = (array) get_option( 'sticky_posts' );
      if ( $hero_post_id ) {
        $exclude_ids[] = $hero_post_id;
      }
      // Exclude Pilihan Editor posts from category grids
      $exclude_ids = array_merge( $exclude_ids, up6_get_editor_pick_ids() );

      $cache_key = 'up6_homepage_cats';
      $cached_html = get_transient( $cache_key );

      if ( false === $cached_html ) :
        ob_start();

        $cat_limit     = absint( up6_opt( 'up6_homepage_cat_count' ) );
        $posts_per_cat = absint( up6_opt( 'up6_homepage_posts_per_cat' ) );
        $show_empty    = (bool) up6_opt( 'up6_homepage_show_empty_cats' );

        $display_cats = get_categories( [
          'orderby'    => 'count',
          'order'      => 'DESC',
          'hide_empty' => true,
          'number'     => $cat_limit,
        ] );

        foreach ( $display_cats as $cat ) :
          $cat_query = new WP_Query( [
            'cat'            => $cat->term_id,
            'posts_per_page' => $posts_per_cat,
            'post__not_in'   => $exclude_ids,
          ] );

        // Skip empty sections unless explicitly enabled
        if ( ! $cat_query->have_posts() && ! $show_empty ) {
          wp_reset_postdata();
          continue;
        }
      ?>
      <section class="category-section" aria-label="<?php echo esc_attr( $cat->name ); ?>">

        <div class="section-header">
          <div class="section-header-label">
            <span class="section-dot" aria-hidden="true" style="background:<?php echo esc_attr( up6_category_colour( $cat ) ); ?>"></span>
            <h2><?php echo esc_html( $cat->name ); ?></h2>
          </div>
          <a class="view-all" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
            <?php esc_html_e( 'View All', 'up6' ); ?> →
          </a>
        </div>

        <?php if ( $cat_query->have_posts() ) : ?>
          <div class="articles-grid">
            <?php while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
            <?php get_template_part( 'template-parts/card' ); ?>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        <?php elseif ( $show_empty ) : ?>
          <p class="category-empty"><?php esc_html_e( 'No articles in this category yet.', 'up6' ); ?></p>
        <?php endif; ?>

        <div role="separator" class="section-divider-ornament" aria-hidden="true">
          <span class="sdo-line"></span>
          <span class="sdo-motif">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="22" viewBox="0 0 40 22" fill="none" focusable="false">
              <path d="M20 6 L24 11 L20 16 L16 11 Z" stroke="currentColor" stroke-width="1"/>
              <line x1="20" y1="6" x2="20" y2="3" stroke="currentColor" stroke-width="0.75"/>
              <line x1="20" y1="16" x2="20" y2="19" stroke="currentColor" stroke-width="0.75"/>
              <line x1="16" y1="11" x2="12" y2="8.5" stroke="currentColor" stroke-width="0.75"/>
              <line x1="16" y1="11" x2="12" y2="13.5" stroke="currentColor" stroke-width="0.75"/>
              <line x1="24" y1="11" x2="28" y2="8.5" stroke="currentColor" stroke-width="0.75"/>
              <line x1="24" y1="11" x2="28" y2="13.5" stroke="currentColor" stroke-width="0.75"/>
              <circle cx="20" cy="2" r="1.25" fill="currentColor"/>
              <circle cx="20" cy="20" r="1.25" fill="currentColor"/>
              <circle cx="11" cy="8" r="0.875" fill="currentColor"/>
              <circle cx="11" cy="14" r="0.875" fill="currentColor"/>
              <circle cx="29" cy="8" r="0.875" fill="currentColor"/>
              <circle cx="29" cy="14" r="0.875" fill="currentColor"/>
            </svg>
          </span>
          <span class="sdo-line"></span>
        </div>

      </section>
      <?php endforeach; ?>

      <?php
        $cached_html = ob_get_clean();
        set_transient( $cache_key, $cached_html, 5 * MINUTE_IN_SECONDS );
        echo $cached_html;
      else :
        // Serve cached category sections
        echo $cached_html;
      endif;
      ?>

      <?php
      // =============================================================
      // MOST RECENT
      // =============================================================
      $recent_count = absint( up6_opt( 'up6_homepage_recent_count' ) );
      if ( $recent_count > 0 ) :
        $recent_query = new WP_Query( [
          'posts_per_page' => $recent_count,
          'post__not_in'   => $exclude_ids,
        ] );
        if ( $recent_query->have_posts() ) :
      ?>
      <section class="section-recent">
        <div class="section-header">
          <div class="section-header-label">
            <span class="section-dot" aria-hidden="true"></span>
            <h2><?php esc_html_e( 'Most Recent', 'up6' ); ?></h2>
          </div>
        </div>
        <div class="recent-list">
          <?php while ( $recent_query->have_posts() ) : $recent_query->the_post(); ?>
          <a href="<?php the_permalink(); ?>" class="recent-item">
            <?php if ( has_post_thumbnail() ) : ?>
              <div class="recent-thumb">
                <?php the_post_thumbnail( 'thumbnail' ); ?>
              </div>
            <?php endif; ?>
            <div class="recent-body">
              <div class="card-kicker">
                <span class="card-dot-sm" aria-hidden="true"></span>
                <?php
                $r_cats = get_the_category();
                if ( $r_cats ) {
                  echo '<span class="card-category">' . esc_html( strtoupper( $r_cats[0]->name ) ) . '</span>';
                }
                ?>
              </div>
              <span class="recent-title"><?php the_title(); ?></span>
            </div>
            <time class="recent-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
              <?php echo esc_html( get_the_date() ); ?>
            </time>
          </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </section>
      <?php endif; // have_posts ?>
      <?php endif; // recent_count > 0 ?>

    </main><!-- #main -->

    <!-- ===================== PERSISTENT SIDEBAR ===================== -->
    <?php get_sidebar(); ?>

  </div><!-- .content-area-wrap -->
</div><!-- .site-content-inner -->

<?php get_footer(); ?>
