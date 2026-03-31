<?php
/**
 * Front page template
 *
 * Overrides the parent block theme's templates/front-page.html.
 * Handles both Reading settings: "Your latest posts" (loads blog
 * layout) and "A static page" (loads page content).
 *
 * @package UP6
 * @since   2.3.1
 */

if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
    // Static front page — render as a page
    require __DIR__ . '/page.php';
} else {
    // Latest posts — render the blog homepage
    require __DIR__ . '/index.php';
}
