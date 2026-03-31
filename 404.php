<?php
/**
 * 404 — Page Not Found
 *
 * Displayed when a requested URL does not match any published content.
 * Shows a branded error message with a search form and link to homepage.
 * Inherits the site header and footer via get_header() / get_footer().
 *
 * @package UP6
 * @since   2.0
 */
get_header();
?>

<div class="site-content-inner error-404-content">
  <h1 class="error-404-code">404</h1>
  <h2><?php esc_html_e( 'Page Not Found', 'up6' ); ?></h2>
  <p><?php esc_html_e( 'The article or page you were looking for could not be found.', 'up6' ); ?></p>
  <a class="error-404-btn" href="<?php echo esc_url( home_url() ); ?>">
    <?php esc_html_e( '← Back to Home', 'up6' ); ?>
  </a>
</div>

<?php get_footer(); ?>
