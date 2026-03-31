<?php
/**
 * Article Card — reusable partial
 *
 * Used in: index.php, archive.php, search.php, single.php (Related News).
 * Call via: get_template_part( 'template-parts/card' );
 *
 * Must be called inside a WP loop (the_post() already called).
 *
 * @package UP6
 * @since   2.8
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-card' ); ?>>
  <?php if ( has_post_thumbnail() ) : ?>
    <div class="card-thumb">
      <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php the_post_thumbnail( 'ss-card', [ 'loading' => 'lazy' ] ); ?>
      </a>
    </div>
  <?php endif; ?>
  <div class="card-body">
    <div class="card-kicker">
      <?php
      $card_cats = get_the_category();
      if ( $card_cats ) {
        $cat_colour = up6_category_colour( $card_cats[0] );
        echo '<a class="card-category" href="' . esc_url( get_category_link( $card_cats[0]->term_id ) ) . '" style="color:' . esc_attr( $cat_colour ) . '">'
           . esc_html( strtoupper( $card_cats[0]->name ) ) . '</a>';
      }
      ?>
    </div>
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <div class="card-meta">
      <span class="card-author"><?php echo esc_html( get_the_author() ); ?></span>
      <span class="card-kicker-sep" aria-hidden="true">·</span>
      <time class="card-kicker-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
        <?php echo esc_html( get_the_date() ); ?>
      </time>
    </div>
    <p class="card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
  </div>
</article>
