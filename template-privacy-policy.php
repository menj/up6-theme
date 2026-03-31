<?php
/**
 * Template Name: Privacy Policy
 * Template Post Type: page
 *
 * PDPA-compliant privacy policy for UP6 Suara Semasa.
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

        <h2><?php esc_html_e( 'Introduction', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa, owned and operated by Langgam Fikir Enterprise, is committed to protecting your personal information. This Privacy Policy explains how we collect, use, store, and disclose personal data in accordance with the Personal Data Protection Act 2010 (PDPA) of Malaysia.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'By accessing and using this portal, you agree to the terms set out in this policy. If you do not agree, please discontinue use of this portal.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Information We Collect', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'We may collect the following information when you interact with UP6 Suara Semasa:', 'up6' ); ?></p>
        <ul>
          <li><strong><?php esc_html_e( 'Contact information', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'your name and email address provided voluntarily through contact forms or comments', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Usage data', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'IP address, browser type, pages visited, and session duration collected automatically via analytics tools', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Cookies', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'small text files stored on your device to improve your browsing experience', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Submitted content', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'comments, feedback, or submissions you send through this portal', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'We do not collect identity card numbers, bank account details, or other sensitive information without your explicit consent.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'How We Use Your Information', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Information collected is used solely for the following purposes:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Processing and responding to your enquiries, complaints, or feedback', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Analysing usage patterns to improve portal content and performance', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Ensuring the security and integrity of the portal', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Fulfilling legal obligations under PDPA and applicable Malaysian legislation', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'We do not use your information for direct marketing without your prior consent.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Cookies and Tracking Technologies', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa uses first-party and third-party cookies for the following purposes:', 'up6' ); ?></p>
        <ul>
          <li><strong><?php esc_html_e( 'Functional cookies', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'retain your preferences such as display settings and login sessions', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Analytics cookies', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'collect anonymous usage statistics via services such as Google Analytics', 'up6' ); ?></li>
          <li><strong><?php esc_html_e( 'Embedded content cookies', 'up6' ); ?></strong> &mdash; <?php esc_html_e( 'set automatically when you view videos or social content embedded within articles', 'up6' ); ?></li>
        </ul>
        <p><?php esc_html_e( 'You may control or delete cookies through your browser settings. Please note that disabling cookies may affect certain functions of this portal.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Sharing Information with Third Parties', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa does not sell, rent, or trade your personal information to any third party. Your information may be shared only in the following circumstances:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Service providers who assist in the technical operation of this portal, subject to confidentiality agreements', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Statutory authorities where required by law or court order', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Business asset transfers, should Langgam Fikir Enterprise be involved in a merger or acquisition — you will be notified in advance', 'up6' ); ?></li>
        </ul>

        <h2><?php esc_html_e( 'Links to External Websites', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Articles on UP6 Suara Semasa may contain links to external websites. We are not responsible for the privacy practices of those websites and encourage you to read their privacy policies separately.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Data Security', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'We take reasonable technical and organisational measures to protect your personal information from unauthorised access, loss, or disclosure. However, no data transmission over the internet can be guaranteed as fully secure.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'Should a data breach occur that affects your rights and freedoms, we will take appropriate action and notify affected parties as required by law.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Your Rights Under PDPA', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Under the Personal Data Protection Act 2010, you have the right to:', 'up6' ); ?></p>
        <ul>
          <li><?php esc_html_e( 'Access the personal information we hold about you', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Request correction of inaccurate or incomplete information', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Withdraw your consent at any time, though this may affect certain services', 'up6' ); ?></li>
          <li><?php esc_html_e( 'Object to the processing of your data for specific purposes', 'up6' ); ?></li>
        </ul>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'To make any request regarding your personal data, please contact us through our %s page. We will process your request within 21 days as stipulated by PDPA.', 'up6' ),
            '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'up6' ) . '</a>'
          );
          ?>
        </p>

        <h2><?php esc_html_e( 'Data Retention', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'We retain your personal information only for as long as necessary to fulfil the purposes stated in this policy, or as required by law. Anonymous analytics data is retained for a maximum period of 26 months.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Children', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 Suara Semasa is not directed at children under the age of 18. We do not knowingly collect personal information from children. If you believe we have done so inadvertently, please contact us for immediate deletion.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Amendments to This Policy', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Langgam Fikir Enterprise reserves the right to amend this Privacy Policy at any time. Any changes will take effect immediately upon publication on this page. We encourage you to review this page periodically. The date of the most recent update appears at the top of this page.', 'up6' ); ?></p>

        <h2><?php esc_html_e( 'Contact Us', 'up6' ); ?></h2>
        <p>
          <?php
          printf(
            /* translators: %s: link to contact page */
            esc_html__( 'If you have any questions, concerns, or complaints regarding this Privacy Policy or the way we handle your personal data, please contact us through our %s page.', 'up6' ),
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
