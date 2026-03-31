<?php
/**
 * Archive Template — category, tag, date, and author archives
 *
 * Coloured hero banner (uses category colour system for category archives),
 * breadcrumb trail, article grid with Load More pagination, and sidebar.
 * Cards rendered via template-parts/card.php partial.
 *
 * @package UP6
 * @since   2.0
 */
get_header();

$term_name  = '';
$term_desc  = '';
$arch_label = __( 'Archive', 'up6' );

if ( is_category() ) {
    $term_name  = single_cat_title( '', false );
    $term_desc  = category_description();
    $arch_label = __( 'Category Archive', 'up6' );
} elseif ( is_tag() ) {
    $term_name  = single_tag_title( '', false );
    $term_desc  = tag_description();
    $arch_label = __( 'Tag Archive', 'up6' );
} elseif ( is_year() ) {
    $term_name  = get_the_date( 'Y' );
    $arch_label = __( 'Yearly Archive', 'up6' );
} elseif ( is_month() ) {
    $term_name  = get_the_date( 'F Y' );
    $arch_label = __( 'Monthly Archive', 'up6' );
} elseif ( is_day() ) {
    $term_name  = get_the_date();
    $arch_label = __( 'Daily Archive', 'up6' );
} elseif ( is_author() ) {
    $term_name  = get_the_author();
    $arch_label = __( 'Author Archive', 'up6' );
} elseif ( is_post_type_archive() ) {
    $term_name  = post_type_archive_title( '', false );
    $arch_label = __( 'Archive', 'up6' );
}
?>

<!-- Archive hero banner -->
<?php
$archive_colour = '';
if ( is_category() ) {
    $archive_cat    = get_queried_object();
    $archive_colour = $archive_cat ? up6_category_colour( $archive_cat ) : '';
}
?>
<div class="archive-hero"<?php echo $archive_colour ? ' style="background:' . esc_attr( $archive_colour ) . '"' : ''; ?>>
  <div class="archive-hero-inner">
    <p class="archive-label"><?php echo esc_html( $arch_label ); ?></p>
    <h1 class="archive-title"><?php echo esc_html( $term_name ); ?></h1>
    <?php if ( $term_desc ) : ?>
      <p class="archive-description"><?php echo wp_kses_post( $term_desc ); ?></p>
    <?php endif; ?>
  </div>
</div>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <div class="content-area-wrap">
    <main id="main" class="site-main" role="main">

      <?php if ( have_posts() ) : ?>
        <div class="section-header">
          <div class="section-header-label">
            <span class="section-dot" aria-hidden="true"></span>
            <h2>
              <?php
              printf(
                esc_html( _n( '%s Article', '%s Articles', $wp_query->found_posts, 'up6' ) ),
                number_format_i18n( $wp_query->found_posts )
              );
              ?>
            </h2>
          </div>
        </div>

        <div class="articles-grid">
          <?php while ( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'template-parts/card' ); ?>
          <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( [ 'class' => 'pagination' ] ); ?>

      <?php else : ?>
        <p><?php esc_html_e( 'No articles found in this archive.', 'up6' ); ?></p>
      <?php endif; ?>

    </main>

    <?php get_sidebar(); ?>

  </div>
</div>

<?php get_footer(); ?>
