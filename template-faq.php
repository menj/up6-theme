<?php
/**
 * Template Name: FAQ Page
 * Template Post Type: page
 *
 * Assign this template to any static page via Page Attributes → Template.
 * FAQ items are managed at FAQ Items in the admin (up6_faq custom post type).
 * Ordered by Menu Order (Page Attributes → Order) ascending.
 *
 * Schema: FAQPage JSON-LD (Google Rich Results eligible).
 *
 * @package UP6
 * @since   2.5.28
 */

get_header();

// ── Fetch all published FAQ items ordered by menu_order ──────
$faq_items = get_posts( [
    'post_type'      => 'up6_faq',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'post_status'    => 'publish',
] );

// ── Build FAQ schema array for JSON-LD ───────────────────────
$faq_schema_entities = [];
foreach ( $faq_items as $item ) {
    $faq_schema_entities[] = [
        '@type'          => 'Question',
        'name'           => wp_strip_all_tags( $item->post_title ),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => wp_strip_all_tags( apply_filters( 'the_content', $item->post_content ) ),
        ],
    ];
}
?>

<?php if ( ! empty( $faq_schema_entities ) ) : ?>
<!-- UP6 FAQPage Schema -->
<script type="application/ld+json">
<?php echo wp_json_encode( [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $faq_schema_entities,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?>
</script>
<?php endif; ?>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <main id="main" class="site-main faq-main" role="main">
    <?php while ( have_posts() ) : the_post(); ?>

    <header class="entry-header faq-page-header">
      <div class="faq-page-label">
        <span class="section-dot" aria-hidden="true"></span>
        <span><?php esc_html_e( 'FAQ', 'up6' ); ?></span>
      </div>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php if ( has_excerpt() ) : ?>
        <p class="faq-page-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
      <?php endif; ?>
    </header>

    <?php if ( ! empty( $faq_items ) ) : ?>

      <div class="faq-accordion" role="list">
        <?php foreach ( $faq_items as $i => $item ) :
            $answer_html = apply_filters( 'the_content', $item->post_content );
        ?>
        <details class="faq-item" role="listitem">
          <summary class="faq-question">
            <span class="faq-question-number"><?php echo sprintf( '%02d', $i + 1 ); ?></span>
            <span class="faq-question-text"><?php echo esc_html( $item->post_title ); ?></span>
            <span class="faq-chevron" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </span>
          </summary>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <?php echo wp_kses_post( $answer_html ); ?>
            </div>
          </div>
        </details>
        <?php endforeach; ?>
      </div>

    <?php else : ?>

      <div class="faq-empty">
        <p><?php esc_html_e( 'No FAQ items found. Add some via FAQ Items in the admin.', 'up6' ); ?></p>
      </div>

    <?php endif; ?>

    <?php endwhile; ?>
  </main>
</div>

<?php get_footer(); ?>
