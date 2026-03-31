<?php
/**
 * UP6 Post View Counter & View Stats Admin Page
 *
 * Self-contained view tracking, cookie-deduplicated.
 * Powers the Most Viewed sidebar panel and the View Stats admin page.
 *
 * Extracted from functions.php for maintainability.
 *
 * @package UP6
 * @since   2.8
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   POST VIEW COUNTER — self-contained, zero plugin dependency
   Increments `_up6_views` post meta on each singular post view.
   Skips logged-in admins, common bot user-agents, and visitors
   who have already viewed the post (24h cookie dedup).
   Used by the Most Viewed sidebar panel.
   @since 2.5.114
   @updated 2.7.14 — cookie-based dedup
   ============================================================= */
function up6_increment_post_views() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    // Skip admins
    if ( current_user_can( 'edit_posts' ) ) {
        return;
    }
    // Skip common bots
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
    $bots = [ 'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'facebookexternalhit', 'headlesschrome' ];
    foreach ( $bots as $b ) {
        if ( strpos( $ua, $b ) !== false ) {
            return;
        }
    }
    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return;
    }
    // Cookie-based dedup — one count per visitor per post per 24h
    $cookie_key = 'up6_viewed_' . $post_id;
    if ( isset( $_COOKIE[ $cookie_key ] ) ) {
        return;
    }
    $views = (int) get_post_meta( $post_id, '_up6_views', true );
    update_post_meta( $post_id, '_up6_views', $views + 1 );
    setcookie( $cookie_key, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
}
add_action( 'wp', 'up6_increment_post_views' );

/**
 * Returns most-viewed posts within the configured day range.
 * Uses `_up6_views` meta key set by up6_increment_post_views().
 *
 * @param  int $count  Number of posts to return (default 5).
 * @return WP_Post[]
 */
function up6_get_most_viewed_posts( $count = 5 ) {
    $days = max( 1, (int) up6_opt( 'up6_most_viewed_days' ) );
    $args = [
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => $count,
        'meta_key'               => '_up6_views',
        'orderby'                => 'meta_value_num',
        'order'                  => 'DESC',
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_term_cache' => true,
        'date_query'             => [
            [
                'after'     => $days . ' days ago',
                'inclusive' => true,
            ],
        ],
    ];
    $q = new WP_Query( $args );
    return $q->posts;
}
/* =============================================================
   VIEW STATS — admin page, global reset, per-post reset
   C: Appearance → View Stats — ranked table with per-row reset
   A: Reset All button at top of stats page (with confirmation)
   B: Reset Views link in post edit sidebar meta box
   @since 2.5.115
   ============================================================= */

// ── Register admin page ──────────────────────────────────────
add_action( 'admin_menu', function () {
    add_theme_page(
        __( 'View Stats', 'up6' ),
        __( 'View Stats', 'up6' ),
        'edit_posts',
        'up6-view-stats',
        'up6_render_view_stats_page'
    );
} );

// ── Handle POST actions (reset all / reset single) ───────────
add_action( 'admin_init', 'up6_handle_view_stats_actions' );

/**
 * Process view stats form submissions.
 *
 * Handles two actions: 'reset_all' (deletes all _up6_views meta rows)
 * and 'reset_post' (zeroes a single post's count). Both require nonce
 * verification and edit_posts capability. Redirects back to stats page.
 */
function up6_handle_view_stats_actions() {
    if ( ! isset( $_POST['up6_stats_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['up6_stats_nonce'], 'up6_stats_action' ) ) return;
    if ( ! current_user_can( 'edit_posts' ) ) return;

    $action = isset( $_POST['up6_stats_action'] ) ? sanitize_key( $_POST['up6_stats_action'] ) : '';

    // A — Reset all
    if ( 'reset_all' === $action ) {
        global $wpdb;
        $wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_up6_views' ] );
        wp_safe_redirect( add_query_arg( [
            'page'    => 'up6-view-stats',
            'notice'  => 'reset_all',
        ], admin_url( 'themes.php' ) ) );
        exit;
    }

    // A / C — Reset single post
    if ( 'reset_post' === $action && ! empty( $_POST['up6_stats_post_id'] ) ) {
        $post_id = absint( $_POST['up6_stats_post_id'] );
        if ( get_post( $post_id ) ) {
            update_post_meta( $post_id, '_up6_views', 0 );
        }
        wp_safe_redirect( add_query_arg( [
            'page'   => 'up6-view-stats',
            'notice' => 'reset_post',
        ], admin_url( 'themes.php' ) ) );
        exit;
    }
}

// ── Render stats page ────────────────────────────────────────

/**
 * Render the View Stats admin page (Appearance → View Stats).
 *
 * Displays a ranked table of posts by view count within the configured
 * day range. Includes a global "Reset All" button and per-row "Reset"
 * buttons. Shows success notices after reset actions.
 */
function up6_render_view_stats_page() {
    if ( ! current_user_can( 'edit_posts' ) ) return;

    $days        = max( 1, (int) up6_opt( 'up6_most_viewed_days' ) );
    $notice      = isset( $_GET['notice'] ) ? sanitize_key( $_GET['notice'] ) : '';

    // Fetch all posts with view data within the day window
    $args = [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 50,
        'meta_key'            => '_up6_views',
        'orderby'             => 'meta_value_num',
        'order'               => 'DESC',
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'date_query'          => [ [ 'after' => $days . ' days ago', 'inclusive' => true ] ],
    ];
    $posts = ( new WP_Query( $args ) )->posts;

    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'UP6 View Stats', 'up6' ); ?></h1>

      <?php if ( 'reset_all' === $notice ) : ?>
      <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'All view counts have been reset.', 'up6' ); ?></p></div>
      <?php elseif ( 'reset_post' === $notice ) : ?>
      <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'View count reset for that post.', 'up6' ); ?></p></div>
      <?php endif; ?>

      <p style="color:#666;">
        <?php
        printf(
            /* translators: 1: day count, 2: Theme Options link */
            esc_html__( 'Showing posts published in the last %1$d day(s) ranked by views. Day range is set in %2$s.', 'up6' ),
            $days,
            '<a href="' . esc_url( admin_url( 'themes.php?page=up6-theme-options&tab=general' ) ) . '">' . esc_html__( 'Theme Options → General', 'up6' ) . '</a>'
        );
        ?>
      </p>

      <?php if ( $posts ) : ?>

      <!-- Reset All form -->
      <form method="post" id="up6-reset-all-form" style="margin-bottom:1.5rem;">
        <?php wp_nonce_field( 'up6_stats_action', 'up6_stats_nonce' ); ?>
        <input type="hidden" name="up6_stats_action" value="reset_all" />
        <button type="submit" class="button button-secondary"
          onclick="return confirm('<?php esc_attr_e( 'Reset ALL view counts? This cannot be undone.', 'up6' ); ?>')"
        ><?php esc_html_e( 'Reset All Counts', 'up6' ); ?></button>
      </form>

      <table class="wp-list-table widefat fixed striped posts" style="max-width:900px;">
        <thead>
          <tr>
            <th style="width:3rem;">#</th>
            <th><?php esc_html_e( 'Title', 'up6' ); ?></th>
            <th style="width:9rem;"><?php esc_html_e( 'Category', 'up6' ); ?></th>
            <th style="width:9rem;"><?php esc_html_e( 'Published', 'up6' ); ?></th>
            <th style="width:6rem;text-align:right;"><?php esc_html_e( 'Views', 'up6' ); ?></th>
            <th style="width:7rem;"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $posts as $i => $post ) :
            $cats     = get_the_category( $post->ID );
            $cat_name = $cats ? esc_html( $cats[0]->name ) : '—';
            $views    = (int) get_post_meta( $post->ID, '_up6_views', true );
          ?>
          <tr>
            <td style="color:#aaa;font-weight:700;"><?php echo $i + 1; ?></td>
            <td>
              <a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>" style="font-weight:600;">
                <?php echo esc_html( get_the_title( $post->ID ) ); ?>
              </a>
              <div style="margin-top:2px;">
                <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" target="_blank" style="font-size:0.75rem;color:#999;">
                  <?php esc_html_e( 'View post ↗', 'up6' ); ?>
                </a>
              </div>
            </td>
            <td style="color:#555;"><?php echo $cat_name; ?></td>
            <td style="color:#555;"><?php echo esc_html( get_the_date( 'j M Y', $post->ID ) ); ?></td>
            <td style="text-align:right;font-weight:700;font-size:1.1rem;"><?php echo number_format_i18n( $views ); ?></td>
            <td>
              <form method="post" style="display:inline;">
                <?php wp_nonce_field( 'up6_stats_action', 'up6_stats_nonce' ); ?>
                <input type="hidden" name="up6_stats_action" value="reset_post" />
                <input type="hidden" name="up6_stats_post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
                <button type="submit" class="button button-small"
                  onclick="return confirm('<?php esc_attr_e( 'Reset view count for this post?', 'up6' ); ?>')"
                ><?php esc_html_e( 'Reset', 'up6' ); ?></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php else : ?>
      <div class="notice notice-info" style="max-width:600px;">
        <p>
          <?php
          printf(
              esc_html__( 'No view data yet for the last %d day(s). The counter populates as readers visit individual posts.', 'up6' ),
              $days
          );
          ?>
        </p>
      </div>
      <?php endif; ?>

    </div><!-- .wrap -->
    <?php
}

// ── B: Per-post reset meta box in the post edit screen ───────
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'up6_view_count_box',
        __( 'View Count', 'up6' ),
        'up6_render_view_count_meta_box',
        'post',
        'side',
        'low'
    );
} );

/**
 * Render the View Count meta box in the post editor sidebar.
 *
 * Shows the current view count and a "Reset" link that zeroes the count
 * via a nonce-protected GET request. Displayed on all post edit screens.
 */
function up6_render_view_count_meta_box( $post ) {
    $views = (int) get_post_meta( $post->ID, '_up6_views', true );
    $reset_url = wp_nonce_url(
        add_query_arg( [
            'up6_reset_views' => '1',
            'post_id'         => $post->ID,
        ], admin_url( 'post.php' ) ),
        'up6_reset_views_' . $post->ID
    );
    ?>
    <p style="margin:0 0 0.5rem;">
      <strong style="font-size:1.5rem;"><?php echo number_format_i18n( $views ); ?></strong>
      <span style="color:#999;font-size:0.8rem;display:block;margin-top:2px;"><?php esc_html_e( 'total views recorded', 'up6' ); ?></span>
    </p>
    <a href="<?php echo esc_url( $reset_url ); ?>"
       class="button button-small"
       onclick="return confirm('<?php esc_attr_e( 'Reset view count for this post?', 'up6' ); ?>')"
       style="margin-top:0.5rem;"
    ><?php esc_html_e( 'Reset count', 'up6' ); ?></a>
    <?php
}

// Handle per-post reset from edit screen GET request
add_action( 'admin_init', function () {
    if ( empty( $_GET['up6_reset_views'] ) || empty( $_GET['post_id'] ) ) return;
    $post_id = absint( $_GET['post_id'] );
    if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'up6_reset_views_' . $post_id ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    update_post_meta( $post_id, '_up6_views', 0 );
    wp_safe_redirect( add_query_arg( [
        'post'    => $post_id,
        'action'  => 'edit',
        'message' => 'up6_views_reset',
    ], admin_url( 'post.php' ) ) );
    exit;
} );

// Admin notice after per-post reset from edit screen
add_action( 'admin_notices', function () {
    if ( empty( $_GET['message'] ) || 'up6_views_reset' !== $_GET['message'] ) return;
    echo '<div class="notice notice-success is-dismissible"><p>'
       . esc_html__( 'View count reset to zero.', 'up6' )
       . '</p></div>';
} );
