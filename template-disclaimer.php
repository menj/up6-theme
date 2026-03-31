<?php
/**
 * Template Name: Disclaimer
 * Template Post Type: page
 *
 * Legal disclaimer for UP6 Suara Semasa.
 * File naming: en-US per UP6 convention.
 * WordPress page title / slug: set by site owner in ms-MY (e.g. "Penafian").
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

        <h2><?php esc_html_e( 'General', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The information published on UP6 Suara Semasa is provided in good faith for general informational and journalistic purposes only. While Langgam Fikir Enterprise makes every reasonable effort to ensure that content is accurate, timely, and sourced responsibly, we make no representations or warranties of any kind, express or implied, as to the completeness, accuracy, reliability, or suitability of any article, report, or other content appearing on this portal.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Any reliance you place on information published on UP6 Suara Semasa is strictly at your own risk.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Not Professional Advice', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Nothing published on UP6 Suara Semasa constitutes legal, financial, medical, investment, or other professional advice. Content covering law, health, financial matters, or similar specialist subjects is provided for general awareness only and should not be acted upon without first seeking guidance from a qualified professional.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'UP6 Suara Semasa is a news and commentary portal, not a professional advisory service. Readers who require advice on specific personal circumstances should consult the appropriate licensed professional.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Opinion and Commentary', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Articles published under the Opinion, Analysis, and Commentary categories represent the personal views of the named author and do not necessarily reflect the editorial position of UP6 Suara Semasa or Langgam Fikir Enterprise.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Opinion pieces, satire, and editorial commentary are clearly labelled. Readers are encouraged to consider such content in context and to engage with multiple perspectives before forming their own conclusions.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'External Links', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa articles may contain hyperlinks to third-party websites. These links are provided as a convenience and for informational purposes only. The inclusion of any link does not imply endorsement, verification, or sponsorship of the linked website or its contents by Langgam Fikir Enterprise.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We have no control over the nature, content, or availability of external websites. We accept no responsibility for any loss or inconvenience that may arise from your use of third-party content or websites reached through links on this portal.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Limitation of Liability', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'To the fullest extent permitted by applicable Malaysian law, Langgam Fikir Enterprise, its directors, editors, journalists, and contributors shall not be liable for any direct, indirect, incidental, consequential, or special damages arising from:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Access to or use of content published on this portal', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Reliance on any information contained in any article, report, or commentary', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Temporary unavailability, errors, or interruptions to this portal', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Unauthorised access to or alteration of your data or transmissions', 'up6' ); ?></li>
        </ul>

        <h2><?php esc_html_e( 'Accuracy of Reporting', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Journalism by its nature involves working with incomplete and evolving information. UP6 Suara Semasa endeavours to verify facts before publication and to update or correct articles where errors are identified.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Where an article has been materially updated after initial publication, this will be noted within the article. The original publication date and the date of update are recorded for transparency.', 'up6' ); ?></p>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'Readers who identify factual errors are encouraged to bring them to our attention through our %s page. We take correction requests seriously and respond in accordance with our Editorial Policy.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Copyright', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'All original content published on UP6 Suara Semasa — including articles, reports, commentaries, photographs, and multimedia — is the intellectual property of Langgam Fikir Enterprise unless otherwise stated.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Content may not be reproduced, distributed, transmitted, or republished in any form without prior written permission from Langgam Fikir Enterprise, except for personal, non-commercial use with proper attribution. For licensing enquiries, please use the contact details below.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Governing Law', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'This disclaimer is governed by and construed in accordance with the laws of Malaysia. Any dispute arising from or in connection with this disclaimer or the use of this portal shall be subject to the exclusive jurisdiction of the Malaysian courts.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Amendments', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Langgam Fikir Enterprise reserves the right to amend this disclaimer at any time without prior notice. Changes take effect immediately upon publication. The date of the most recent update appears at the top of this page. Continued use of this portal after any changes constitutes acceptance of the revised disclaimer.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Contact Us', 'up6' ); ?></h2>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'If you have any questions about this disclaimer or wish to raise a concern about content published on UP6 Suara Semasa, please reach us through our %s page.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>
        <p><?php esc_html_e( 'Langgam Fikir Enterprise &mdash; Registration No. 202503137729', 'up6' ); ?></p>

      </div><!-- .policy-content -->
      <?php echo up6_brand_inline( ob_get_clean() ); ?>

    </article>

    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
