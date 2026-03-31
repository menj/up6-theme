<?php
/**
 * Static Page Template
 *
 * Renders standard WordPress pages (not posts, not custom page templates).
 * Uses the policy-main layout wrapper shared with page templates.
 * Pages assigned a named template (e.g. template-faq.php) use that
 * template instead — this file is the fallback for unassigned pages.
 *
 * @package UP6
 * @since   2.0
 */
get_header();
?>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>
  <main id="main" class="site-main" role="main">
    <?php while ( have_posts() ) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>
      <div class="entry-content">
        <?php the_content(); ?>
      </div>
    </article>
    <?php endwhile; ?>
  </main>
</div>

<?php get_footer(); ?>
