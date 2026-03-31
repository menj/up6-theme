<?php
/**
 * Sidebar template — Featured panel + Categories list + native WP widgets.
 * Persistent right-rail on homepage matching article detail layout.
 * No plugins required.
 *
 * @package UP6
 * @since   2.4.0
 */
?>
<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'up6' ); ?>">

  <!-- Pilihan Editor: most recent post per unique category -->
  <?php
  $editor_picks = up6_get_editor_picks( 5 );
  if ( $editor_picks ) :
  ?>
  <div class="featured-panel">
    <div class="featured-panel-header">
      <span class="featured-panel-bar" aria-hidden="true"></span>
      <h3><?php esc_html_e( "Editor's Pick", 'up6' ); ?></h3>
    </div>
    <ul class="featured-panel-list">
      <?php foreach ( $editor_picks as $ep ) : setup_postdata( $ep ); ?>
      <li class="featured-panel-item">
        <div class="featured-panel-body">
          <?php
          $item_cats = get_the_category( $ep->ID );
          if ( $item_cats ) :
          ?>
          <span class="featured-panel-cat"><?php echo esc_html( strtoupper( $item_cats[0]->name ) ); ?></span>
          <?php endif; ?>
          <a class="featured-panel-title" href="<?php echo esc_url( get_permalink( $ep->ID ) ); ?>">
            <?php echo esc_html( get_the_title( $ep->ID ) ); ?>
          </a>
          <span class="featured-panel-date">
            <?php
            printf(
              esc_html__( '%s ago', 'up6' ),
              esc_html( human_time_diff( get_post_time( 'U', true, $ep->ID ), time() ) )
            );
            ?>
          </span>
        </div>
        <?php $ep_thumb = get_the_post_thumbnail_url( $ep->ID, 'thumbnail' ); ?>
        <?php if ( $ep_thumb ) : ?>
        <a href="<?php echo esc_url( get_permalink( $ep->ID ) ); ?>" class="featured-panel-thumb" tabindex="-1" aria-hidden="true">
          <img src="<?php echo esc_url( $ep_thumb ); ?>" alt="" width="72" height="54" loading="lazy" />
        </a>
        <?php endif; ?>
      </li>
      <?php endforeach; wp_reset_postdata(); ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- Categories panel: live category list with post counts (hidden on single posts) -->
  <?php if ( ! is_single() ) :
  $sidebar_cat_limit = absint( up6_opt( 'up6_sidebar_cat_count' ) ) ?: 5;
  $sidebar_cats = array_filter(
    get_categories( [
      'orderby'    => 'count',
      'order'      => 'DESC',
      'hide_empty' => true,
      'number'     => $sidebar_cat_limit * 4, /* fetch extra to account for stripped top-level */
    ] ),
    function( $c ) { return $c->parent !== 0; }   /* subcategories only */
  );
  $sidebar_cats = array_slice( array_values( $sidebar_cats ), 0, $sidebar_cat_limit );
  if ( $sidebar_cats ) :
  ?>

  <?php
  // ── Most Viewed panel ──────────────────────────────────────
  $up6_most_viewed = up6_get_most_viewed_posts( 5 );
  if ( $up6_most_viewed ) :
    $up6_mv_days = max( 1, (int) up6_opt( 'up6_most_viewed_days' ) );
  ?>
  <div class="widget most-viewed-panel">
    <h3 class="widget-title most-viewed-heading">
      <?php
      printf(
        /* translators: %d: number of days */
        esc_html( _n( 'Most Read Last %d Day', 'Most Read Last %d Days', $up6_mv_days, 'up6' ) ),
        $up6_mv_days
      );
      ?>
    </h3>
    <ol class="most-viewed-list">
      <?php foreach ( $up6_most_viewed as $up6_mv_i => $up6_mv_post ) :
        $up6_mv_views = (int) get_post_meta( $up6_mv_post->ID, '_up6_views', true );
        $up6_mv_thumb = get_the_post_thumbnail_url( $up6_mv_post->ID, 'thumbnail' );
        $up6_mv_time  = human_time_diff( get_post_time( 'U', true, $up6_mv_post->ID ), time() );
        $up6_mv_views_fmt = $up6_mv_views >= 1000
            ? round( $up6_mv_views / 1000, 1 ) . 'k'
            : number_format_i18n( $up6_mv_views );
      ?>
      <li class="most-viewed-item">
        <span class="most-viewed-rank" aria-hidden="true"><?php echo sprintf( '%02d', $up6_mv_i + 1 ); ?></span>
        <div class="most-viewed-body">
          <a class="most-viewed-title" href="<?php echo esc_url( get_permalink( $up6_mv_post->ID ) ); ?>">
            <?php echo esc_html( get_the_title( $up6_mv_post->ID ) ); ?>
          </a>
          <div class="most-viewed-meta">
            <span class="most-viewed-date">
              <?php printf( esc_html__( '%s ago', 'up6' ), esc_html( $up6_mv_time ) ); ?>
            </span>
            <?php if ( $up6_mv_views > 0 ) : ?>
            <span class="most-viewed-views" aria-label="<?php echo esc_attr( number_format_i18n( $up6_mv_views ) . ' ' . __( 'views', 'up6' ) ); ?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <?php echo esc_html( $up6_mv_views_fmt ); ?>
            </span>
            <?php endif; ?>
          </div>
        </div>
        <?php if ( $up6_mv_thumb ) : ?>
        <a href="<?php echo esc_url( get_permalink( $up6_mv_post->ID ) ); ?>" class="most-viewed-thumb" tabindex="-1" aria-hidden="true">
          <img src="<?php echo esc_url( $up6_mv_thumb ); ?>" alt="" width="80" height="60" loading="lazy" />
        </a>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ol>
  </div><!-- .most-viewed-panel -->
  <?php endif; ?>

  <div class="widget sidebar-categories-panel">
    <h3 class="widget-title"><?php esc_html_e( 'Most Active', 'up6' ); ?></h3>
    <ul class="sidebar-categories-list">
      <?php foreach ( $sidebar_cats as $sc ) : ?>
        <li>
          <a href="<?php echo esc_url( get_category_link( $sc->term_id ) ); ?>">
            <span class="sidebar-cat-name"><?php echo esc_html( $sc->name ); ?></span>
            <span class="sidebar-cat-count"><?php echo esc_html( $sc->count ); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
  <?php endif; // ! is_single() ?>

</aside><!-- #secondary -->
