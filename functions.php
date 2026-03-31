<?php
/**
 * UP6 — Child Theme Functions
 *
 * Main orchestrator for the UP6 editorial news theme (child of Twenty Twenty-Five).
 * This file bootstraps all includes, registers hooks, and defines core helpers.
 *
 * Architecture:
 *   functions.php         — enqueue, setup, helpers, hooks (this file)
 *   includes/theme-options.php        — tabbed admin settings page (7 tabs)
 *   includes/hidden-tags.php          — tag-based content filtering
 *   includes/theme-security-scanner.php — malicious theme detection (18 patterns)
 *   includes/schema.php               — JSON-LD, Open Graph, Twitter Card, SEO
 *   includes/view-stats.php           — post view counter + View Stats admin page
 *   includes/admin.php                — meta boxes (subtitle, pin, template), site editor redirect
 *
 * No plugins required. All sidebar widgets use WordPress core.
 *
 * @package    UP6
 * @subpackage Functions
 * @since      2.0
 * @requires   PHP 8.2+, WordPress 6.4+, parent theme Twenty Twenty-Five
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   INCLUDES — load modular components
   Order matters: theme-options first (defines up6_opt() used everywhere),
   then hidden-tags, security scanner, schema, view-stats, admin.
   ============================================================= */
require_once get_stylesheet_directory() . '/includes/theme-options.php';
require_once get_stylesheet_directory() . '/includes/hidden-tags.php';
require_once get_stylesheet_directory() . '/includes/theme-security-scanner.php';

/* =============================================================
   HOMEPAGE FRAGMENT CACHE
   Category grid output is cached in a transient (5-minute TTL).
   Invalidated on save_post so new/edited articles appear promptly.
   @since 2.8
   ============================================================= */
function up6_flush_homepage_cache( $post_id = 0 ) {
    if ( $post_id && get_post_type( $post_id ) !== 'post' ) return;
    delete_transient( 'up6_homepage_cats' );
}
add_action( 'save_post',       'up6_flush_homepage_cache' );
add_action( 'deleted_post',    'up6_flush_homepage_cache' );
add_action( 'trashed_post',    'up6_flush_homepage_cache' );
add_action( 'customize_save_after', function () { delete_transient( 'up6_homepage_cats' ); } );

/* =============================================================
   ENQUEUE STYLES
   ============================================================= */
function up6_enqueue_styles() {
    $ver = wp_get_theme()->get( 'Version' );
    $dir = get_stylesheet_directory_uri();
    $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    // Parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme( 'twentytwentyfive' )->get( 'Version' )
    );

    // Child theme stylesheet
    wp_enqueue_style(
        'up6-style',
        $min ? $dir . '/style' . $min . '.css' : get_stylesheet_uri(),
        [ 'parent-style' ],
        $ver
    );

    // Print stylesheet — loaded with media="print", zero screen cost
    wp_enqueue_style(
        'up6-print',
        $dir . '/css/print' . $min . '.css',
        [ 'up6-style' ],
        $ver,
        'print'
    );

    // Mobile-first patch — scoped responsive fixes (loaded after main stylesheet)
    wp_enqueue_style(
        'up6-mobile-patch',
        $dir . '/css/mobile-patch' . $min . '.css',
        [ 'up6-style' ],
        $ver
    );

    // Self-hosted fonts — DM Sans + Source Serif 4 (no third-party request)
    wp_enqueue_style(
        'up6-fonts',
        $dir . '/css/fonts.css',
        [],
        $ver
    );
}
add_action( 'wp_enqueue_scripts', 'up6_enqueue_styles' );

/* =============================================================
   ENQUEUE SCRIPTS
   ============================================================= */
function up6_enqueue_scripts() {
    $ver = wp_get_theme()->get( 'Version' );
    $dir = get_stylesheet_directory_uri();
    $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    // Navigation (all pages)
    wp_enqueue_script(
        'up6-navigation',
        $dir . '/js/navigation' . $min . '.js',
        [],
        $ver,
        true
    );

    // Masonry + imagesLoaded — archive, search, single
    if ( is_archive() || is_search() || is_single() ) {

        wp_enqueue_script(
            'up6-imagesloaded',
            $dir . '/js/jquery.imagesloaded.min.js',
            [ 'jquery' ],
            '5.0.0',
            true
        );

        wp_enqueue_script(
            'up6-masonry',
            $dir . '/js/jquery.masonry.min.js',
            [ 'jquery', 'up6-imagesloaded' ],
            null,
            true
        );

        wp_enqueue_script(
            'up6-grid',
            $dir . '/js/up6-grid' . $min . '.js',
            [ 'jquery', 'up6-masonry', 'up6-imagesloaded' ],
            $ver,
            true
        );

        wp_localize_script( 'up6-grid', 'up6Grid', [
            'i18n' => [
                'loadMore' => __( 'Load More', 'up6' ),
                'loading'  => __( 'Loading…',  'up6' ),
                'noMore'   => __( 'All caught up!', 'up6' ),
                'error'    => __( 'Failed to load. Try again.', 'up6' ),
            ],
        ] );
    }
}
add_action( 'wp_enqueue_scripts', 'up6_enqueue_scripts' );

/* =============================================================
   EDITOR STYLESHEET
   ============================================================= */
function up6_editor_styles() {
    $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    add_editor_style( 'css/editor-style' . $min . '.css' );
}
add_action( 'after_setup_theme', 'up6_editor_styles' );

/* =============================================================
   THEME SETUP
   ============================================================= */
function up6_setup() {
    // Load text domain for translations
    load_theme_textdomain( 'up6', get_stylesheet_directory() . '/languages' );

    // Custom logo support (editable via Appearance → Customize → Site Identity)
    add_theme_support( 'custom-logo', [
        'height'      => 48,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ] );

    // Post thumbnails
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'ss-card',     640, 360, true );
    add_image_size( 'ss-mobile',   480, 270, true );  // srcset candidate for mobile hero/single
    add_image_size( 'ss-hero',    1200, 675, true );
    add_image_size( 'ss-single',  1200, 560, true );

    // HTML5 markup
    add_theme_support( 'html5', [
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ] );

    // Title tag
    add_theme_support( 'title-tag' );

    // Navigation menus
    register_nav_menus( [
        'primary'   => __( 'Primary Navigation',   'up6' ),
        'secondary' => __( 'Secondary (Footer Bar)', 'up6' ),
    ] );
}
add_action( 'after_setup_theme', 'up6_setup' );

/* =============================================================
   WIDGET AREAS (sidebar + footer)
   All widgets are native WP core — no plugins required.
   Recommended widgets:
     - Recent Posts      → wp-includes/widgets/class-wp-widget-recent-posts.php
     - Categories        → wp-includes/widgets/class-wp-widget-categories.php
     - Tag Cloud         → wp-includes/widgets/class-wp-widget-tag-cloud.php
     - Search            → wp-includes/widgets/class-wp-widget-search.php
     - Recent Comments   → wp-includes/widgets/class-wp-widget-recent-comments.php
   ============================================================= */
function up6_widgets_init() {
    // Main sidebar
    register_sidebar( [
        'name'          => __( 'Main Sidebar', 'up6' ),
        'id'            => 'sidebar-main',
        'description'   => __( 'Widgets here appear on posts and archives. Uses only core WordPress widgets — no plugins needed.', 'up6' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ] );
}
add_action( 'widgets_init', 'up6_widgets_init' );

/* =============================================================
   CUSTOMIZER — Site Identity fields
   (Logo and footer info editable here, no page builder needed)
   ============================================================= */
function up6_customize_register( $wp_customize ) {
    // Site title and tagline are controlled via Settings → General (native WordPress).
    // To upload an image logo: Appearance → Customize → Site Identity → Logo.

    // --- Footer identity ---
    $wp_customize->add_section( 'ss_footer', [
        'title'    => __( 'Footer Identity', 'up6' ),
        'priority' => 120,
    ] );
    $wp_customize->add_setting( 'ss_footer_description', [
        'default'           => 'Providing independent, high-quality journalism for the modern digital landscape.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'ss_footer_description', [
        'label'   => __( 'Footer Tagline / Description', 'up6' ),
        'section' => 'ss_footer',
        'type'    => 'textarea',
    ] );
    $wp_customize->add_setting( 'ss_contact_address', [
        'default'           => '123 Newsroom Plaza, Media District, Kuala Lumpur, 50000',
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'ss_contact_address', [
        'label'   => __( 'Contact Address', 'up6' ),
        'section' => 'ss_footer',
        'type'    => 'textarea',
    ] );
    $wp_customize->add_setting( 'ss_contact_phone', [
        'default'           => '+60 3 1234 5678',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'ss_contact_phone', [
        'label'   => __( 'Contact Phone', 'up6' ),
        'section' => 'ss_footer',
        'type'    => 'text',
    ] );
    $wp_customize->add_setting( 'ss_contact_email', [
        'default'           => 'contact@up6.org',
        'sanitize_callback' => 'sanitize_email',
    ] );
    $wp_customize->add_control( 'ss_contact_email', [
        'label'   => __( 'Contact Email', 'up6' ),
        'section' => 'ss_footer',
        'type'    => 'email',
    ] );
    $wp_customize->add_setting( 'ss_copyright', [
        'default'           => '© ' . date( 'Y' ) . ' UP6. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'ss_copyright', [
        'label'   => __( 'Copyright Line', 'up6' ),
        'section' => 'ss_footer',
        'type'    => 'text',
    ] );

    // --- Social media URLs ---
    $wp_customize->add_section( 'ss_social', [
        'title'    => __( 'Social Media Links', 'up6' ),
        'priority' => 121,
    ] );

    $social_fields = [
        'ss_social_facebook' => [ 'label' => __( 'Facebook URL', 'up6' ), 'default' => '' ],
        'ss_social_x'        => [ 'label' => __( 'X (Twitter) URL', 'up6' ), 'default' => '' ],
        'ss_social_telegram'  => [ 'label' => __( 'Telegram URL', 'up6' ), 'default' => '' ],
    ];

    foreach ( $social_fields as $key => $args ) {
        $wp_customize->add_setting( $key, [
            'default'           => $args['default'],
            'sanitize_callback' => 'esc_url_raw',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $args['label'],
            'section' => 'ss_social',
            'type'    => 'url',
        ] );
    }
}
add_action( 'customize_register', 'up6_customize_register' );

/* =============================================================
   HELPER: render site logo (image or text fallback)
   ============================================================= */
function up6_logo() {
    $name    = get_bloginfo( 'name' );
    $escaped = esc_html( $name );
    // Split into first word (e.g. "UP6") and remainder (e.g. "SUARA SEMASA")
    $parts   = preg_split( '/\s+/', $escaped, 2 );
    $first   = isset( $parts[0] ) ? $parts[0] : $escaped;
    $rest    = isset( $parts[1] ) ? $parts[1] : '';
    // Wrap trailing digits in the first word with accent span
    $first   = preg_replace( '/(\d+)$/', '<span class="site-title-accent">$1</span>', $first );
    // Wrap remainder in subtitle span
    $output  = $rest !== '' ? $first . ' <span class="site-title-sub">' . $rest . '</span>' : $first;
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home" class="site-title-link">';
    echo '<span class="site-title">' . $output . '</span>';
    echo '</a>';
}

/* =============================================================
   HELPER: reading time estimator
   Average adult reading speed: 200 words per minute (Malay/EN)
   ============================================================= */
function up6_reading_time( $post_id = null ) {
    $content    = get_post_field( 'post_content', $post_id ?: get_the_ID() );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes    = max( 1, (int) round( $word_count / 200 ) );
    return $minutes;
}

/* =============================================================
   HELPER: Gregorian → Hijri date conversion (no plugin required)
   Uses the standard astronomical algorithm accurate for modern dates.
   Malaysia follows moon sighting declarations that may differ by +-1 day
   from the astronomical result. Use Theme Options -> General ->
   Hijri Date Offset to correct if needed (-1, 0, or +1).
   ============================================================= */
function up6_hijri_date( $timestamp = null ) {
    // Apply configurable day offset (corrects for moon-sighting vs astronomical)
    $offset = (int) up6_opt( 'up6_hijri_offset' );

    if ( ! $timestamp ) {
        // Nav bar: today in the site's timezone (avoids deprecated current_time('timestamp'))
        $now = new DateTimeImmutable( 'now', wp_timezone() );
        if ( $offset ) {
            $now = $now->modify( $offset . ' days' );
        }
        $day   = (int) $now->format( 'j' );
        $month = (int) $now->format( 'n' );
        $year  = (int) $now->format( 'Y' );
    } else {
        // Post timestamp: already adjusted for site timezone by get_post_time('U', false)
        $timestamp = $timestamp + ( $offset * DAY_IN_SECONDS );
        $day   = (int) date( 'j', $timestamp );
        $month = (int) date( 'n', $timestamp );
        $year  = (int) date( 'Y', $timestamp );
    }

    // Convert Gregorian to Julian Day Number
    $jd = gregoriantojd( $month, $day, $year );

    // Julian Day to Hijri
    $l  = $jd - 1948440 + 10632;
    $n  = (int) ( ( $l - 1 ) / 10631 );
    $l  = $l - 10631 * $n + 354;
    $j  = (int) ( ( 10985 - $l ) / 5316 ) * (int) ( ( 50 * $l ) / 17719 )
        + (int) ( $l / 5670 ) * (int) ( ( 43 * $l ) / 15238 );
    $l  = $l - (int) ( ( 30 - $j ) / 15 ) * (int) ( ( 17719 * $j ) / 50 )
        - (int) ( $j / 16 ) * (int) ( ( 15238 * $j ) / 43 ) + 29;
    $hm = (int) ( ( 24 * $l ) / 709 );
    $hd = $l - (int) ( ( 709 * $hm ) / 24 );
    $hy = 30 * $n + $j - 30;

    $months_ms = [
        1  => 'Muharram',   2  => 'Safar',        3  => 'Rabiulawal',
        4  => 'Rabiulakhir',5  => 'Jamadilawal',  6  => 'Jamadilakhir',
        7  => 'Rejab',      8  => 'Syaaban',       9  => 'Ramadan',
        10 => 'Syawal',     11 => 'Zulkaedah',    12 => 'Zulhijjah',
    ];

    $month_name = isset( $months_ms[ $hm ] ) ? $months_ms[ $hm ] : '';

    return [
        'day'        => $hd,
        'month'      => $hm,
        'month_name' => $month_name,
        'year'       => $hy,
        'formatted'  => $hd . ' ' . $month_name . ' ' . $hy . 'H',
    ];
}

/**
 * Store the Hijri date as post meta when a post is published.
 * This locks the date so future offset changes don't retroactively
 * alter historical articles.
 *
 * @since 2.7.7
 */
add_action( 'transition_post_status', function ( $new, $old, $post ) {
    if ( $post->post_type !== 'post' ) return;
    // Only store on first publish, or if no stored date exists yet
    if ( $new === 'publish' && ! get_post_meta( $post->ID, '_up6_hijri_formatted', true ) ) {
        $hijri = up6_hijri_date( get_post_time( 'U', false, $post->ID ) );
        update_post_meta( $post->ID, '_up6_hijri_formatted', $hijri['formatted'] );
    }
}, 10, 3 );

/**
 * Returns the Hijri date string for a post.
 * Reads from stored meta first (locked at publish time).
 * Falls back to live computation for older posts that predate
 * the storage feature.
 *
 * @param  int|null $post_id  Post ID (defaults to current post).
 * @return string             Formatted Hijri date (e.g. "2 Syawal 1447H").
 * @since  2.7.7
 */
function up6_get_hijri( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $stored = get_post_meta( $post_id, '_up6_hijri_formatted', true );
    if ( $stored ) return $stored;
    // Fallback: compute from publication date (for pre-2.7.7 posts)
    $hijri = up6_hijri_date( get_post_time( 'U', false, $post_id ) );
    return $hijri['formatted'];
}

/* =============================================================
   SS-MOBILE CROP BACKFILL
   Generates the ss-mobile (480x270) crop for all existing image
   attachments that are missing it. Triggered once per theme
   version so it re-runs automatically after each upgrade that
   changes the version string.
   Processes 20 images per admin page load to avoid timeouts.
   Progress stored in up6_ss_mobile_offset; completion flagged
   in up6_ss_mobile_done_{version}.
   @since 2.7.30
   ============================================================= */
add_action( 'admin_init', function () {
    $version  = wp_get_theme()->get( 'Version' );
    $done_key = 'up6_ss_mobile_done_' . sanitize_key( $version );

    if ( get_option( $done_key ) ) return;

    $offset    = (int) get_option( 'up6_ss_mobile_offset', 0 );
    $batch     = 20;
    $upload_dir = wp_upload_dir();

    $attachments = get_posts( [
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => $batch,
        'offset'         => $offset,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ] );

    if ( empty( $attachments ) ) {
        // All done — mark complete, clear offset
        update_option( $done_key, 1, true );
        delete_option( 'up6_ss_mobile_offset' );
        return;
    }

    foreach ( $attachments as $att_id ) {
        $file = get_attached_file( $att_id );
        if ( ! $file || ! file_exists( $file ) ) continue;

        $meta = wp_get_attachment_metadata( $att_id );
        if ( ! $meta ) continue;

        // Check if ss-mobile crop already exists
        $sizes = isset( $meta['sizes'] ) ? $meta['sizes'] : [];
        if ( isset( $sizes['ss-mobile'] ) ) {
            $crop_file = path_join( dirname( $file ), $sizes['ss-mobile']['file'] );
            if ( file_exists( $crop_file ) ) continue;
        }

        // Generate missing crop
        $new_meta = wp_generate_attachment_metadata( $att_id, $file );
        if ( $new_meta && ! is_wp_error( $new_meta ) ) {
            wp_update_attachment_metadata( $att_id, $new_meta );
        }
    }

    update_option( 'up6_ss_mobile_offset', $offset + count( $attachments ), true );
} );

/**
 * One-time backfill: store Hijri dates for all existing published
 * posts that lack the _up6_hijri_formatted meta. Runs once on
 * admin_init, keyed by version so it triggers on upgrade.
 *
 * @since 2.7.8
 */
add_action( 'admin_init', function () {
    $backfill_key = 'up6_hijri_backfill_done';
    if ( get_option( $backfill_key ) ) return;

    $posts = get_posts( [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => '_up6_hijri_formatted',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ] );

    foreach ( $posts as $pid ) {
        $pub_time = get_post_time( 'U', false, $pid );
        if ( ! $pub_time ) continue;
        $hijri = up6_hijri_date( $pub_time );
        update_post_meta( $pid, '_up6_hijri_formatted', $hijri['formatted'] );
    }

    update_option( $backfill_key, 1, true );
} );

/* =============================================================
   HELPER: Pilihan Editor — top recent post per unique category
   Returns an array of WP_Post objects (max 5), each from a
   different category, ordered by publish date DESC.
   Static-cached so both index.php and sidebar.php can call it
   without running the query twice.
   @since 2.6.80
   ============================================================= */
function up6_get_editor_picks( $count = 5 ) {
    static $cached = null;
    if ( $cached !== null ) return $cached;

    $cached = [];
    $seen_cats = [];

    // Fetch a generous pool of recent posts
    $pool = get_posts( [
        'posts_per_page'      => $count * 6,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
    ] );

    foreach ( $pool as $p ) {
        if ( count( $cached ) >= $count ) break;
        $cats = get_the_category( $p->ID );
        if ( ! $cats ) continue;
        $primary_cat_id = $cats[0]->term_id;
        if ( isset( $seen_cats[ $primary_cat_id ] ) ) continue;
        $seen_cats[ $primary_cat_id ] = true;
        $cached[] = $p;
    }

    return $cached;
}

/**
 * Returns just the post IDs from the editor picks.
 */
function up6_get_editor_pick_ids( $count = 5 ) {
    return wp_list_pluck( up6_get_editor_picks( $count ), 'ID' );
}

/* =============================================================
   HELPER: minimal breadcrumb — native WP only, no plugin
   ============================================================= */
function up6_breadcrumb() {
    if ( is_front_page() ) return;

    global $post;

    echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'up6' ) . '">';
    echo '<a href="' . esc_url( home_url() ) . '">' . esc_html__( 'Home', 'up6' ) . '</a>';
    echo '<span class="breadcrumb-sep" aria-hidden="true">›</span>';
    if ( is_category() ) {
        echo '<span class="current">' . esc_html( single_cat_title( '', false ) ) . '</span>';
    } elseif ( is_tag() ) {
        echo '<span class="current">' . esc_html( single_tag_title( '', false ) ) . '</span>';
    } elseif ( is_single() ) {
        $cats = get_the_category();
        if ( $cats ) {
            echo '<a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
            echo '<span class="breadcrumb-sep" aria-hidden="true">›</span>';
        }
        echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_page() ) {
        if ( $post && $post->post_parent ) {
            echo '<a href="' . esc_url( get_permalink( $post->post_parent ) ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a>';
            echo '<span class="breadcrumb-sep" aria-hidden="true">›</span>';
        }
        echo '<span class="current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_search() ) {
        echo '<span class="current">' . sprintf( __( 'Search: %s', 'up6' ), esc_html( get_search_query() ) ) . '</span>';
    }
    echo '</nav>';
}

/* =============================================================
   HELPER: author avatar
   Priority: 1) custom uploaded image (user meta up6_avatar)
             2) Gravatar (d=404 with onerror swap to initials)
             3) initials span (always generated as fallback)
   ============================================================= */
function up6_author_avatar( $author_id = null ) {
    if ( ! $author_id ) $author_id = (int) get_the_author_meta( 'ID' );

    $name    = get_the_author_meta( 'display_name', $author_id );
    $initial = strtoupper( mb_substr( $name, 0, 1 ) );

    // ── 1. Custom uploaded avatar ──────────────────────────────
    $custom = get_user_meta( $author_id, 'up6_avatar', true );
    if ( $custom ) {
        return '<img class="entry-author-avatar entry-author-avatar--img"'
             . ' src="' . esc_url( $custom ) . '"'
             . ' alt="' . esc_attr( $name ) . '"'
             . ' width="40" height="40" loading="lazy">';
    }

    // ── 2. Gravatar with initials onerror fallback ─────────────
    $email = get_the_author_meta( 'user_email', $author_id );
    if ( $email ) {
        $hash     = md5( strtolower( trim( $email ) ) );
        $gravatar = 'https://www.gravatar.com/avatar/' . $hash . '?s=80&d=404&r=pg';
        // onerror: hide the broken image, reveal the sibling initials span
        return '<img class="entry-author-avatar entry-author-avatar--img"'
             . ' src="' . esc_url( $gravatar ) . '"'
             . ' alt="' . esc_attr( $name ) . '"'
             . ' width="40" height="40" loading="lazy"'
             . ' onerror="this.style.display=\'none\';this.nextElementSibling.style.removeProperty(\'display\')">'
             . '<span class="entry-author-avatar entry-author-avatar--initials" style="display:none" aria-hidden="true">'
             . esc_html( $initial ) . '</span>';
    }

    // ── 3. Initials only ───────────────────────────────────────
    return '<span class="entry-author-avatar entry-author-avatar--initials" aria-hidden="true">'
         . esc_html( $initial ) . '</span>';
}

/* =============================================================
   ADMIN: custom avatar field on user profile page
   Saves URL to user meta key: up6_avatar
   ============================================================= */
function up6_avatar_profile_field( $user ) {
    if ( ! current_user_can( 'edit_user', $user->ID ) ) return;
    $current = esc_url( get_user_meta( $user->ID, 'up6_avatar', true ) );
    $has     = ! empty( $current );
    ?>
    <h2><?php esc_html_e( 'UP6 Author Avatar', 'up6' ); ?></h2>
    <table class="form-table" role="presentation">
      <tr class="user-up6-avatar-wrap">
        <th><label for="up6_avatar"><?php esc_html_e( 'Profile Photo', 'up6' ); ?></label></th>
        <td>
          <div id="up6-avatar-wrap" style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;margin-bottom:0.75rem;">

            <?php /* Preview circle — matches WP's own gravatar display size */ ?>
            <div id="up6-avatar-frame" style="
              width:96px;height:96px;border-radius:50%;overflow:hidden;flex-shrink:0;
              background:#ddd;border:3px solid #c3c4c7;
              display:flex;align-items:center;justify-content:center;">
              <img id="up6-avatar-preview"
                   src="<?php echo $current; ?>"
                   alt=""
                   style="width:100%;height:100%;object-fit:cover;<?php echo $has ? '' : 'display:none;'; ?>">
              <?php /* Placeholder icon shown when no image set */ ?>
              <span id="up6-avatar-placeholder" style="<?php echo $has ? 'display:none;' : ''; ?>line-height:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                     fill="none" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
              </span>
            </div>

            <div style="display:flex;flex-direction:column;gap:0.5rem;">
              <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <button type="button" class="button button-primary" id="up6-avatar-upload">
                  <?php $has ? esc_html_e( 'Change Photo', 'up6' ) : esc_html_e( 'Upload Photo', 'up6' ); ?>
                </button>
                <button type="button" class="button button-link-delete" id="up6-avatar-remove"
                        style="<?php echo $has ? '' : 'display:none;'; ?>">
                  <?php esc_html_e( 'Remove', 'up6' ); ?>
                </button>
              </div>
              <p class="description" style="margin:0;">
                <?php esc_html_e( 'Square image, at least 200×200 px. Falls back to Gravatar, then initials if not set.', 'up6' ); ?>
              </p>
            </div>

          </div>
          <input type="hidden" name="up6_avatar" id="up6_avatar" value="<?php echo $current; ?>">
        </td>
      </tr>
    </table>
    <script>
    jQuery(function($){
      var frame;
      $('#up6-avatar-upload').on('click', function(e){
        e.preventDefault();
        if ( frame ) { frame.open(); return; }
        frame = wp.media({
          title    : '<?php echo esc_js( __( 'Select Profile Photo', 'up6' ) ); ?>',
          button   : { text: '<?php echo esc_js( __( 'Use as profile photo', 'up6' ) ); ?>' },
          multiple : false,
          library  : { type: 'image' }
        });
        frame.on('select', function(){
          var url = frame.state().get('selection').first().toJSON().url;
          $('#up6_avatar').val(url);
          $('#up6-avatar-preview').attr('src', url).show();
          $('#up6-avatar-placeholder').hide();
          $('#up6-avatar-frame').css('border-color', '#2271b1');
          $('#up6-avatar-upload').text('<?php echo esc_js( __( 'Change Photo', 'up6' ) ); ?>');
          $('#up6-avatar-remove').show();
        });
        frame.open();
      });
      $('#up6-avatar-remove').on('click', function(e){
        e.preventDefault();
        $('#up6_avatar').val('');
        $('#up6-avatar-preview').attr('src','').hide();
        $('#up6-avatar-placeholder').show();
        $('#up6-avatar-frame').css('border-color','#c3c4c7');
        $('#up6-avatar-upload').text('<?php echo esc_js( __( 'Upload Photo', 'up6' ) ); ?>');
        $(this).hide();
      });
    });
    </script>
    <?php
}
add_action( 'show_user_profile', 'up6_avatar_profile_field' );
add_action( 'edit_user_profile', 'up6_avatar_profile_field' );

/**
 * Enqueue the WP media uploader on profile pages for the custom avatar field.
 */
function up6_avatar_profile_field_enqueue( $hook ) {
    if ( ! in_array( $hook, [ 'profile.php', 'user-edit.php' ] ) ) return;
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'up6_avatar_profile_field_enqueue' );

/**
 * Save the custom avatar URL from the profile page to user meta.
 */
function up6_avatar_profile_save( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) return;
    if ( ! isset( $_POST['up6_avatar'] ) ) return;
    update_user_meta( $user_id, 'up6_avatar', esc_url_raw( $_POST['up6_avatar'] ) );
}
add_action( 'personal_options_update',  'up6_avatar_profile_save' );
add_action( 'edit_user_profile_update', 'up6_avatar_profile_save' );

/* =============================================================
   HELPER: get social link (returns URL or empty string)
   ============================================================= */
function up6_social_url( $key ) {
    return esc_url( get_theme_mod( $key, '' ) );
}

/* =============================================================
   SCHEMA, SEO & OPEN GRAPH — includes/schema.php
   JSON-LD NewsArticle + BreadcrumbList, Open Graph, Twitter Card,
   canonical URLs, wp_robots noindex, pagination prev/next,
   contact page NewsMediaOrganization schema.
   ============================================================= */
require_once get_stylesheet_directory() . '/includes/schema.php';

/* =============================================================
   POST VIEW COUNTER & VIEW STATS — includes/view-stats.php
   Cookie-deduplicated view counter (up6_increment_post_views),
   up6_get_most_viewed_posts() query, View Stats admin page
   (Appearance → View Stats), per-post and global reset handlers.
   ============================================================= */
require_once get_stylesheet_directory() . '/includes/view-stats.php';

/* =============================================================
   CATEGORY COLOUR SYSTEM
   Each top-level category gets a distinct accent colour.
   Default: deterministic palette based on term ID.
   Override: Theme Options → General → Category Colours (slug:hex).
   Used by card badges, hero badge, single post badges, archive headers.
   @since 2.8
   ============================================================= */
function up6_category_colours() {
    // Editorial-appropriate palette — 10 distinct hues
    return [
        '#1B3C53', // deep blue (default brand)
        '#2D6A4F', // forest green
        '#B5651D', // burnt orange
        '#7B2D8E', // plum
        '#0077B6', // ocean blue
        '#C0392B', // accent red
        '#1A7A4C', // emerald
        '#A0522D', // sienna
        '#4A5899', // slate blue
        '#B8860B', // dark goldenrod
    ];
}

/**
 * Get the accent colour for a category.
 *
 * @param  int|WP_Term $cat  Category ID or term object.
 * @return string            Hex colour string.
 */
function up6_category_colour( $cat ) {
    if ( is_object( $cat ) ) {
        $slug = $cat->slug;
        $id   = $cat->term_id;
    } else {
        $term = get_term( $cat );
        if ( ! $term || is_wp_error( $term ) ) return '#C0392B';
        $slug = $term->slug;
        $id   = $term->term_id;
    }

    // Check for custom override: "slug:#hex" per line in theme option
    $overrides = up6_opt( 'up6_cat_colours' );
    if ( $overrides ) {
        foreach ( explode( "\n", $overrides ) as $line ) {
            $line = trim( $line );
            if ( ! $line || strpos( $line, ':' ) === false ) continue;
            [ $s, $c ] = array_map( 'trim', explode( ':', $line, 2 ) );
            if ( $s === $slug && preg_match( '/^#[0-9a-fA-F]{3,6}$/', $c ) ) {
                return $c;
            }
        }
    }

    // Default: pick from palette by term ID
    $palette = up6_category_colours();
    return $palette[ $id % count( $palette ) ];
}

/**
 * Output CSS custom properties for all categories used on the current page.
 * Hooked to wp_head so badges can use var(--up6-cat-{slug}).
 */
add_action( 'wp_head', function () {
    $cats = get_categories( [ 'hide_empty' => true, 'number' => 50 ] );
    if ( ! $cats ) return;
    echo "<style id=\"up6-cat-colours\">\n:root {\n";
    foreach ( $cats as $cat ) {
        $colour = up6_category_colour( $cat );
        echo '  --up6-cat-' . esc_attr( $cat->slug ) . ': ' . esc_attr( $colour ) . ";\n";
    }
    echo "}\n</style>\n";
}, 2 );

/* =============================================================
   BODY CLASS — add 'has-sidebar' when sidebar is active
   ============================================================= */
function up6_body_classes( $classes ) {
    if ( is_active_sidebar( 'sidebar-main' ) && ( is_archive() || is_home() ) ) {
        $classes[] = 'has-sidebar';
    }
    return $classes;
}
add_filter( 'body_class', 'up6_body_classes' );

/* =============================================================
   EXCERPT LENGTH
   ============================================================= */
add_filter( 'excerpt_length', function () {
    $len = absint( up6_opt( 'up6_excerpt_length' ) );
    return $len ? $len : 35;
} );
add_filter( 'excerpt_more', function() { return ''; } );

/* =============================================================
   CUSTOM POST TYPE: up6_faq
   FAQ items — title = question, content = answer.
   Managed at: Posts → FAQ Items in the admin.
   Displayed via: template-faq.php (assign to any static page)
   ============================================================= */
function up6_register_faq_cpt() {
    register_post_type( 'up6_faq', [
        'labels' => [
            'name'               => __( 'FAQ Items',          'up6' ),
            'singular_name'      => __( 'FAQ Item',           'up6' ),
            'add_new_item'       => __( 'Add New FAQ Item',   'up6' ),
            'edit_item'          => __( 'Edit FAQ Item',      'up6' ),
            'new_item'           => __( 'New FAQ Item',       'up6' ),
            'view_item'          => __( 'View FAQ Item',      'up6' ),
            'search_items'       => __( 'Search FAQ Items',   'up6' ),
            'not_found'          => __( 'No FAQ items found', 'up6' ),
            'not_found_in_trash' => __( 'No FAQ items in trash', 'up6' ),
            'menu_name'          => __( 'FAQ Items',          'up6' ),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => false,  // public:false — REST endpoint would expose content
        'supports'            => [ 'title', 'editor', 'page-attributes' ],
        'menu_icon'           => 'dashicons-editor-help',
        'rewrite'             => false,
        'has_archive'         => false,
        'hierarchical'        => false,
        'capability_type'     => 'post',
    ] );
}
add_action( 'init', 'up6_register_faq_cpt' );

/* =============================================================
   RSS FEED — media:content (featured image) + category tags
   Adds media namespace to RSS feed and enriches each item with
   a featured image enclosure and WP category links, making the
   feed richer for aggregators and Telegram channels.
   ============================================================= */

// Declare the media namespace on the RSS channel
add_action( 'rss2_ns', function () {
    echo 'xmlns:media="http://search.yahoo.com/mrss/"' . "\n";
} );

// Add media:content and category elements to each RSS item
add_action( 'rss2_item', function () {
    global $post;

    // ── media:content — featured image ────────────────────────
    if ( has_post_thumbnail( $post->ID ) ) {
        $thumb_id  = get_post_thumbnail_id( $post->ID );
        $thumb_url = get_the_post_thumbnail_url( $post->ID, 'ss-hero' );
        $meta      = wp_get_attachment_metadata( $thumb_id );

        if ( $thumb_url ) {
            $width  = ! empty( $meta['width'] )  ? ' width="'  . (int) $meta['width']  . '"' : '';
            $height = ! empty( $meta['height'] ) ? ' height="' . (int) $meta['height'] . '"' : '';
            $alt    = esc_attr( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title() );

            echo "\t" . '<media:content' . "\n";
            echo "\t\t" . 'url="'    . esc_url( $thumb_url )  . '"' . "\n";
            echo "\t\t" . 'medium="image"' . "\n";
            echo "\t\t" . 'type="image/jpeg"' . $width . $height . ">\n";
            echo "\t\t" . '<media:title><![CDATA[' . esc_html( get_the_title() ) . ']]></media:title>' . "\n";
            if ( $alt ) {
                echo "\t\t" . '<media:description type="plain"><![CDATA[' . $alt . ']]></media:description>' . "\n";
            }
            echo "\t" . '</media:content>' . "\n";
        }
    }

    // ── category — WP categories as RSS category elements ─────
    $cats = get_the_category( $post->ID );
    if ( $cats ) {
        foreach ( $cats as $cat ) {
            echo "\t" . '<category domain="' . esc_url( get_category_link( $cat->term_id ) ) . '">'
                      . esc_html( $cat->name )
                      . '</category>' . "\n";
        }
    }
} );
/* =============================================================
   SEARCH PERMALINK — clean URL rewrite
   Rewrites /?s=query to /carian/query (translatable base).
   Zero-plugin replacement for Pretty Search Permalinks.
   @since 2.6.14
   ============================================================= */
add_action( 'init', function () {
    global $wp_rewrite;
    $wp_rewrite->search_base = __( 'search', 'up6' );
} );

// Flush rewrite rules once on theme activation so WP registers the new search base
add_action( 'after_switch_theme', function () {
    global $wp_rewrite;
    $wp_rewrite->search_base = __( 'search', 'up6' );
    flush_rewrite_rules();
} );

// One-time flush on first load after update (version-keyed)
add_action( 'init', function () {
    $flush_key = 'up6_flush_' . wp_get_theme()->get( 'Version' );
    if ( ! get_option( $flush_key ) ) {
        flush_rewrite_rules();
        update_option( $flush_key, 1, true );
    }
}, 20 );

add_action( 'template_redirect', function () {
    global $wp_rewrite;
    if ( ! isset( $wp_rewrite ) || ! is_object( $wp_rewrite ) || ! $wp_rewrite->using_permalinks() ) {
        return;
    }
    $search_base = $wp_rewrite->search_base;
    if ( empty( $search_base ) ) {
        return;
    }
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    if ( is_search() && ! is_admin() && strpos( $request_uri, "/{$search_base}/" ) === false ) {
        wp_safe_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
        exit;
    }
} );

/* =============================================================
   PHOTO CAPTION — featured image overlay
   Priority order:
     1. _up6_photo_caption post meta (per-article override)
     2. Attachment alt text (travels with the photo)
     3. Nothing — no overlay rendered
   @since 2.7.19
   ============================================================= */

// Meta box in the editor sidebar
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'up6_photo_caption_box',
        __( 'Photo Caption', 'up6' ),
        'up6_photo_caption_meta_box',
        'post',
        'side',
        'high'
    );
} );

/**
 * Render the Photo Caption meta box content.
 * Outputs a nonce field and a text input for the featured image overlay caption.
 */
function up6_photo_caption_meta_box( $post ) {
    wp_nonce_field( 'up6_photo_caption_nonce', 'up6_photo_caption_nonce' );
    $caption = get_post_meta( $post->ID, '_up6_photo_caption', true );
    $thumb_id = get_post_thumbnail_id( $post->ID );
    $alt      = $thumb_id ? (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '';
    ?>
    <input type="text" name="up6_photo_caption" id="up6_photo_caption"
           value="<?php echo esc_attr( $caption ); ?>"
           placeholder="<?php echo $alt ? esc_attr( $alt ) : esc_attr__( 'Leave blank to use image alt text', 'up6' ); ?>"
           style="width:100%;padding:6px 8px;font-size:13px;" />
    <p class="description" style="margin-top:6px;">
      <?php esc_html_e( 'Caption shown on the featured image. Leave blank to use the image alt text. Leave both blank to show no caption.', 'up6' ); ?>
    </p>
    <?php if ( $alt ) : ?>
    <p class="description" style="margin-top:4px;color:#999;font-size:11px;">
      <?php echo esc_html__( 'Alt text: ', 'up6' ) . esc_html( $alt ); ?>
    </p>
    <?php endif; ?>
    <?php
}

// Save handler
add_action( 'save_post_post', function ( $post_id ) {
    if ( ! isset( $_POST['up6_photo_caption_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['up6_photo_caption_nonce'], 'up6_photo_caption_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $caption = isset( $_POST['up6_photo_caption'] ) ? sanitize_text_field( $_POST['up6_photo_caption'] ) : '';
    update_post_meta( $post_id, '_up6_photo_caption', $caption );
} );

/**
 * Returns the resolved photo caption for a post.
 * Priority: per-article override → attachment alt text → empty string.
 *
 * @param  int|null $post_id  Post ID (defaults to current post).
 * @return string
 */
function up6_get_photo_caption( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();

    // 1. Per-article override
    $override = (string) get_post_meta( $post_id, '_up6_photo_caption', true );
    if ( $override !== '' ) {
        return $override;
    }

    // 2. Attachment alt text
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id ) {
        $alt = (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
        if ( $alt !== '' ) {
            return $alt;
        }
    }

    // 3. Nothing
    return '';
}

/* =============================================================
   ADMIN HELPERS — includes/admin.php
   Subtitle/dek meta box + up6_get_subtitle(), Pin to Homepage
   meta box + admin column, site editor disable + redirect,
   page template registration + selector meta box, metabox CSS.
   ============================================================= */
require_once get_stylesheet_directory() . '/includes/admin.php';

/* =============================================================
   CONTENT COPY PROTECTION (optional)
   Disables right-click, text selection, and copy shortcuts.
   Controlled by Theme Options → General → Copy Protection.
   Off by default. Skips logged-in editors/admins.
   @since 2.6.14
   ============================================================= */
add_action( 'wp_head', function () {
    // Only fire if enabled in Theme Options
    if ( ! (int) up6_opt( 'up6_copy_protect' ) ) {
        return;
    }
    // Skip for editors and admins (they need to select/copy content)
    if ( current_user_can( 'edit_posts' ) ) {
        return;
    }
    // Skip admin pages
    if ( is_admin() ) {
        return;
    }
    ?>
    <style id="up6-copy-protect">
    body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    /* Allow selection in form inputs */
    input, textarea, select, [contenteditable="true"] {
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
        user-select: text;
    }
    </style>
    <script id="up6-copy-protect-js">
    (function(){
        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e){
            if (e.target.closest('input, textarea, select, [contenteditable]')) return;
            e.preventDefault();
        });
        // Disable copy/cut keyboard shortcuts
        document.addEventListener('keydown', function(e){
            if (e.target.closest('input, textarea, select, [contenteditable]')) return;
            if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'a' || e.key === 'A' || e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S')) {
                e.preventDefault();
            }
        });
        // Disable drag
        document.addEventListener('dragstart', function(e){ e.preventDefault(); });
    })();
    </script>
    <?php
}, 99 );

/* =============================================================
   ARTICLE VOTING — thumbs up / thumbs down
   Uses post meta (_up6_votes_up, _up6_votes_down). No custom
   tables. AJAX via wp_ajax_ hooks with nonce verification.
   Cookie-based dedup for guests (24h), user ID dedup for
   logged-in users. Vote counts visible only above threshold.
   @since 2.7.9
   ============================================================= */

/**
 * Get vote counts for a post.
 *
 * @param  int|null $post_id
 * @return array    [ 'up' => int, 'down' => int, 'total' => int ]
 */
function up6_get_votes( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $up   = (int) get_post_meta( $post_id, '_up6_votes_up', true );
    $down = (int) get_post_meta( $post_id, '_up6_votes_down', true );
    return [ 'up' => $up, 'down' => $down, 'total' => $up - $down ];
}

/**
 * Check if the current visitor has already voted on a post.
 *
 * @param  int $post_id
 * @return string|false  'up', 'down', or false
 */
function up6_user_has_voted( $post_id ) {
    // Logged-in users: check user meta
    if ( is_user_logged_in() ) {
        $voted = get_user_meta( get_current_user_id(), '_up6_voted_' . $post_id, true );
        return $voted ?: false;
    }
    // Guests: check cookie
    $cookie_key = 'up6_vote_' . $post_id;
    if ( isset( $_COOKIE[ $cookie_key ] ) ) {
        return sanitize_key( $_COOKIE[ $cookie_key ] );
    }
    return false;
}

// Localize vote data for JS on single posts
add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_singular( 'post' ) ) return;
    $post_id = get_queried_object_id();
    wp_localize_script( 'up6-navigation', 'up6Vote', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'up6_vote_nonce' ),
        'postId'  => $post_id,
        'voted'   => up6_user_has_voted( $post_id ) ?: '',
    ] );
}, 20 );

// AJAX handler — logged-in users
add_action( 'wp_ajax_up6_vote', 'up6_handle_vote' );
// AJAX handler — guests
add_action( 'wp_ajax_nopriv_up6_vote', 'up6_handle_vote' );

/**
 * AJAX handler for article voting.
 *
 * Validates nonce, checks dedup (user meta for logged-in, cookie for guests),
 * increments _up6_votes_up or _up6_votes_down post meta, sets dedup marker,
 * and returns updated vote counts as JSON.
 */
function up6_handle_vote() {
    check_ajax_referer( 'up6_vote_nonce', 'nonce' );

    $post_id = absint( $_POST['post_id'] ?? 0 );
    $type    = sanitize_key( $_POST['type'] ?? '' );

    if ( ! $post_id || ! in_array( $type, [ 'up', 'down' ], true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid request.' ] );
    }

    if ( ! get_post( $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Post not found.' ] );
    }

    // Check for duplicate vote
    $already = up6_user_has_voted( $post_id );
    if ( $already ) {
        $votes = up6_get_votes( $post_id );
        wp_send_json_success( [
            'up'    => $votes['up'],
            'down'  => $votes['down'],
            'voted' => $already,
            'dup'   => true,
        ] );
    }

    // Record the vote
    $meta_key = '_up6_votes_' . $type;
    $current  = (int) get_post_meta( $post_id, $meta_key, true );
    update_post_meta( $post_id, $meta_key, $current + 1 );

    // Store dedup marker
    if ( is_user_logged_in() ) {
        update_user_meta( get_current_user_id(), '_up6_voted_' . $post_id, $type );
    } else {
        // Set cookie for 24 hours
        setcookie( 'up6_vote_' . $post_id, $type, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    }

    $votes = up6_get_votes( $post_id );
    wp_send_json_success( [
        'up'    => $votes['up'],
        'down'  => $votes['down'],
        'voted' => $type,
        'dup'   => false,
    ] );
}

/* =============================================================
   FESTIVE OCCASION ICON
   Shows a colourful SVG icon beside the header logo for
   Malaysian public holidays. Controlled from Theme Options →
   General tab. Hybrid: manual selection + optional date range.
   @since 2.6.39
   ============================================================= */

/**
 * Return the full list of available festive occasions.
 * Keyed by SVG filename (without extension), valued by
 * translatable display label.
 */
function up6_festive_occasions() {
    return [
        'aidilfitri'    => __( 'Hari Raya Aidilfitri', 'up6' ),
        'aidiladha'     => __( 'Hari Raya Haji', 'up6' ),
        'ramadan'       => __( 'Ramadan', 'up6' ),
        'maal-hijrah'   => __( 'Maal Hijrah', 'up6' ),
        'israk-mikraj'  => __( 'Israk & Mikraj', 'up6' ),
        'nuzul-quran'   => __( 'Nuzul al-Quran', 'up6' ),
        'mawlid'        => __( 'Maulid Nabi', 'up6' ),
        'merdeka'       => __( 'Hari Kebangsaan', 'up6' ),
        'malaysia-day'  => __( 'Hari Malaysia', 'up6' ),
        'agong-birthday'=> __( 'Hari Keputeraan YDP Agong', 'up6' ),
        'cny'           => __( 'Tahun Baru Cina', 'up6' ),
        'deepavali'     => __( 'Deepavali', 'up6' ),
        'thaipusam'     => __( 'Thaipusam', 'up6' ),
        'wesak'         => __( 'Hari Wesak', 'up6' ),
        'christmas'     => __( 'Krismas', 'up6' ),
        'new-year'      => __( 'Tahun Baharu', 'up6' ),
        'labour-day'    => __( 'Hari Pekerja', 'up6' ),
    ];
}

/**
 * Render the festive icon inside the header .site-branding.
 * Reads from theme_mod, validates slug against whitelist,
 * checks optional date range, loads SVG from /icons/ directory.
 */
function up6_festive_icon() {
    $slug = get_theme_mod( 'up6_festive_occasion', '' );
    if ( ! $slug ) {
        return;
    }

    // Validate slug against known occasions
    $known = up6_festive_occasions();
    if ( ! isset( $known[ $slug ] ) ) {
        return;
    }

    // Check optional date range (site timezone)
    $from  = get_theme_mod( 'up6_festive_from', '' );
    $until = get_theme_mod( 'up6_festive_until', '' );
    $today = wp_date( 'Y-m-d' );

    if ( $from && $today < $from ) {
        return;
    }
    if ( $until && $today > $until ) {
        return;
    }

    // Load SVG file
    $file = get_stylesheet_directory() . '/icons/' . $slug . '.svg';
    if ( ! file_exists( $file ) ) {
        return;
    }

    $svg = file_get_contents( $file );
    if ( ! $svg ) {
        return;
    }

    echo '<span class="festive-icon" aria-hidden="true" title="' . esc_attr( $known[ $slug ] ) . '">' . $svg . '</span>';
}

/* =============================================================
   INLINE BRAND STYLISATION
   Replaces every occurrence of "UP6 Suara Semasa" (any case)
   in post/page content with a branded inline chip that mirrors
   the header logo exactly: dark-blue pill, UP white, 6 beige,
   SUARA SEMASA salmon-red.
   Applied to: the_content, the_excerpt, widget_text_content.
   ============================================================= */
function up6_brand_inline( $content ) {
    if ( is_admin() ) return $content;

    // Pattern: "UP6" followed by optional space/nbsp and "Suara Semasa" (case-insensitive)
    // Negative lookbehind prevents re-processing already-wrapped instances
    $pattern = '/(?<!up6-brand-inline">)\bUP6[\s\xc2\xa0]+Suara[\s\xc2\xa0]+Semasa\b/iu';

    $replacement =
        '<span class="up6-brand-inline" aria-label="UP6 Suara Semasa">'
        . 'UP<span class="up6-brand-accent">6</span>'
        . '&nbsp;<span class="up6-brand-sub">SUARA SEMASA</span>'
        . '</span>';

    return preg_replace( $pattern, $replacement, $content );
}
add_filter( 'the_content',          'up6_brand_inline' );
add_filter( 'the_excerpt',          'up6_brand_inline' );
add_filter( 'widget_text_content',  'up6_brand_inline' );

