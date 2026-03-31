<?php
/**
 * Template Name: Corrections
 * Template Post Type: page
 *
 * Pembetulan — corrections policy page.
 * File naming: en-US per UP6 convention.
 * WordPress page title / slug: set by site owner in ms-MY (e.g. "Pembetulan").
 * Content strings: en-US source, translated via ms_MY.po.
 *
 * @package UP6
 * @since   2.5.106
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
          <?php esc_html_e( 'Policy &amp; Standards', 'up6' ); ?>
        </p>
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <p class="policy-updated">
          <?php
          printf(
            /* translators: %s: last modified date */
            esc_html__( 'Last updated: %s', 'up6' ),
            esc_html( get_the_modified_date() )
          );
          ?>
        </p>
      </header>

      <?php ob_start(); ?>
      <div class="policy-content">

        <h2><?php esc_html_e( 'Our Commitment', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa is committed to correcting errors promptly, transparently, and without equivocation. We believe that the credibility of a news organisation rests not on the absence of mistakes, but on how honestly it handles them.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'When an error is identified — whether by our own team or brought to our attention by a reader — we will correct it. We will not quietly delete or rewrite content. We will not obscure the fact that a correction was made. We will say what was wrong, what the correct information is, and when the correction was published.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'What We Correct', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa issues corrections for the following categories of error:', 'up6' ); ?></p>
        <ul>
          <li><strong><?php esc_html_e( 'Factual errors', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'incorrect names, dates, figures, or descriptions of events', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Errors of context', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'where the framing of a fact created a materially misleading impression', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Attribution errors', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'where a quote or claim was incorrectly attributed to a person or source', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Translation or language errors', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'where a Malay or English rendering introduced inaccuracy into the substance of a report', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'We do not issue corrections for matters of editorial judgement, word choice, or differences of opinion. If a report is disputed, we will consider the dispute on its merits and correct only where the record is demonstrably wrong.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'How Corrections Are Made', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Corrections are appended to the original article with a clearly marked correction notice stating the date and the nature of the change. The original headline and publication date are preserved. Where a correction materially changes the substance of a report, a note will also be carried at the top of the article.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We do not scrub errors from the public record. The correction notice remains visible permanently alongside the corrected content, so that readers who encountered the original version are aware that it has been updated.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Significant Corrections', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Where a correction is significant — involving a material factual error, an incorrect identity, a figure with public consequence, or content that may have caused harm — the correction will be handled with additional care:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'The affected party will be notified directly where possible', 'up6' ); ?></li>
          <li><?php esc_html_e( 'A senior editor will review and approve the correction notice before publication', 'up6' ); ?></li>
          <li><?php esc_html_e( 'The correction will be noted prominently rather than appended quietly', 'up6' ); ?></li>
        </ul>

        <h2><?php esc_html_e( 'How to Submit a Correction Request', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'If you believe an article published by UP6 Suara Semasa contains a factual error, we want to hear from you. When submitting a correction request, please include:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'The URL or title of the article in question', 'up6' ); ?></li>
          <li><?php esc_html_e( 'The specific claim or passage you believe is incorrect', 'up6' ); ?></li>
          <li><?php esc_html_e( 'The evidence or source you are relying on to dispute the claim', 'up6' ); ?></li>
        </ul>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'Correction requests can be submitted through our %s page. We review every request and will respond within three working days.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Right of Reply', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Any person or organisation who considers themselves to have been inaccurately or unfairly represented in a UP6 Suara Semasa report has the right to request a response. We will publish a response or correction of record where the complaint is substantiated. Requests that do not identify a specific inaccuracy and are instead directed at editorial judgement will not be upheld, but will receive acknowledgement.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Editorial Accountability', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The final decision on whether a correction is warranted rests with the senior editorial team. UP6 Suara Semasa does not accept external direction on corrections from advertisers, government bodies, or other commercial or institutional interests. Pressure to correct or retract content that is accurate will be resisted and, where appropriate, reported.', 'up6' ); ?></p>

      </div><!-- .policy-content -->
      <?php echo up6_brand_inline( ob_get_clean() ); ?>

    </article>

    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
