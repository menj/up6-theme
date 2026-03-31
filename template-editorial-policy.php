<?php
/**
 * Template Name: Editorial Policy
 * Template Post Type: page
 *
 * Assign this template to the page at the editorial policy slug.
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

        <h2><?php esc_html_e( 'Core Principles', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa operates on a commitment to accuracy, honesty, and transparency in every aspect of its reporting. This editorial policy sets out the standards applied by all journalists, editors, and contributors working under the UP6 name.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We believe that the responsibility of media is not merely to convey information, but to build public trust through responsible journalistic practice.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Accuracy and Fact Verification', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Every fact published by UP6 Suara Semasa must be verified through at least two credible sources before publication. Preferred primary sources include:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Official government, court, or statutory body documents', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Media statements or official statements from the parties involved', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Recognised international newswires such as Reuters, AFP, and AP', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Identified and accountable subject-matter experts', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'Reports that rely on a single source will be clearly stated as such. Information that cannot be verified will not be published as fact.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Editorial Independence', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa maintains a strict separation between editorial operations and commercial interests. Advertisers, sponsors, and owners have no authority over coverage decisions, headline selection, or article content.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'UP6 journalists and editors do not accept payment, gifts, or benefits from any party that may create a conflict of interest. Where a conflict of interest arises or is identified, it will be disclosed to readers or the journalist concerned will be recused from that coverage.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Balance and Fairness', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 endeavours to provide right of reply to all parties mentioned critically in any report. Parties approached for comment will be given reasonable time to respond before publication. If a response is not obtained, this will be explicitly stated in the article.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Opposing views and relevant context will be included in analysis and opinion pieces to ensure readers receive a complete picture.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Separation of News and Opinion', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa clearly distinguishes between news reporting and opinion content:', 'up6' ); ?></p>
        <ul>
          <li><strong><?php esc_html_e( 'News reports', 'up6' ); ?></strong> &mdash; <?php esc_html_e( "present facts, events, and current developments without the writer's personal assessment", 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Analysis', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'explains context and implications based on available facts', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Opinion', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'expresses a position or editorial viewpoint, clearly labelled as such', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'Opinion articles published on UP6 represent the views of the author alone and do not necessarily reflect the editorial position of UP6 Suara Semasa.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Corrections and Updates', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa acknowledges that errors may occur and is committed to correcting them promptly and transparently.', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Factual corrections will be displayed prominently at the top or bottom of the relevant article', 'up6' ); ?></li>
          <li><?php esc_html_e( 'The date and details of updates will be recorded', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Articles will not be deleted merely because they contain an error — a correction is the more responsible approach', 'up6' ); ?></li>
        </ul>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'Readers may report errors or request corrections through our %s page.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Use of Anonymous Sources', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa prioritises sources willing to be identified openly. Anonymous sources are accepted only when:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'The information provided is of significant public interest', 'up6' ); ?></li>
          <li><?php esc_html_e( 'The source faces genuine risk if their identity is revealed', 'up6' ); ?></li>
          <li><?php esc_html_e( "The source's identity has been verified and is known to the editor", 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'The use of anonymous sources will be stated in the article, and the reasons for their use will be carefully considered by the editor.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Sensitive Content', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa complies with the Communications and Multimedia Act 1998, the Sedition Act 1948, and other applicable Malaysian legislation in all publishing matters.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We exercise particular care when reporting on:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'National security and inter-ethnic relations', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Victims of sexual crime, children, and vulnerable individuals', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Mental health and suicide — we adhere to responsible reporting guidelines', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Religion and belief — reported with respect and accurate context', 'up6' ); ?></li>
        </ul>

        <h2><?php esc_html_e( 'Press Freedom', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa asserts its right to report independently of interference from any government body, law enforcement agency, regulatory authority, or political entity. Editorial decisions are made solely by the editorial team and are not subject to direction, suppression, or prior restraint by any external party.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We will not reveal the identity of confidential sources under pressure from any authority. Journalists working under the UP6 name have the right to protect their sources, and UP6 will provide reasonable support to any journalist who faces legal or institutional pressure as a result of their work for this publication.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We recognise that a free press is not a courtesy extended by those in power — it is a right exercised in the public interest, regardless of whether that exercise is convenient for the powerful.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Copyright and External Sources', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'All original content published by UP6 Suara Semasa is the property of Langgam Fikir Enterprise. Quotations or references to external sources will be credited with a direct link to the original. UP6 does not republish content in full without written permission from the copyright holder.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Press Freedom', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa asserts its right to report independently of interference from any government body, law enforcement agency, regulatory authority, or political entity. Editorial decisions are made solely by the editorial team and are not subject to direction, suppression, or prior restraint by any external party.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We will not reveal the identity of confidential sources under pressure from any authority. Journalists working under the UP6 name have the right to protect their sources, and UP6 will provide reasonable support to any journalist who faces legal or institutional pressure as a result of their work for this publication.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We recognise that a free press is not a courtesy extended by those in power — it is a right exercised in the public interest, regardless of whether that exercise is convenient for the powerful.', 'up6' ); ?></p>
        <p>
          <?php
          printf(
            /* translators: %s: link to Polis Raja di Malaysia essay */
            esc_html__( 'Further reading: %s', 'up6' ),
            '<a href="https://langgamfikir.my/publications/polis-raja-di-malaysia/" target="_blank" rel="noopener">'
            . esc_html__( 'Polis Raja di Malaysia', 'up6' )
            . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Complaints and Feedback', 'up6' ); ?></h2>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'Readers who are dissatisfied with any UP6 Suara Semasa report may contact the editorial team through our %s page. Every complaint will be taken seriously and will receive a response within three working days.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
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
