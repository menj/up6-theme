<?php
/**
 * UP6 Hidden Tags Architecture
 *
 * Stores hidden tag term IDs centrally in one option.
 * Resolves IDs to slugs through cached helpers.
 * Excludes hidden-tagged content from all public discovery.
 *
 * Compatibility: when the Cipher Gate plugin is active it owns
 * these filters. This file registers them only as a fallback so
 * the theme works standalone.
 *
 * @package UP6
 * @since   2.5.53
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   OPTION KEY
   ============================================================= */

if ( ! defined( 'UP6_HIDDEN_TAGS_OPTION' ) ) {
    define( 'UP6_HIDDEN_TAGS_OPTION', 'up6_hidden_tags' );
}

/* =============================================================
   HELPER: get hidden tag IDs (cached per-request)
   ============================================================= */

function up6_hidden_tag_ids(): array {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }
    $raw = get_option( UP6_HIDDEN_TAGS_OPTION, '' );
    if ( '' === $raw || ! is_string( $raw ) ) {
        return $cache = [];
    }
    $ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
    return $cache = array_values( $ids );
}

/* =============================================================
   HELPER: get hidden tag slugs (resolved from IDs, cached)
   ============================================================= */

function up6_hidden_tag_slugs(): array {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }
    $ids = up6_hidden_tag_ids();
    if ( empty( $ids ) ) {
        return $cache = [];
    }
    $terms = get_terms( [
        'taxonomy'   => 'post_tag',
        'include'    => $ids,
        'hide_empty' => false,
        'fields'     => 'id=>slug',
    ] );
    if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
        return $cache = [];
    }
    return $cache = array_values( $terms );
}

/* =============================================================
   HELPER: check if a term (ID or slug) is a hidden tag
   ============================================================= */

function up6_is_hidden_tag( $term ) {
    if ( is_int( $term ) || ctype_digit( (string) $term ) ) {
        return in_array( absint( $term ), up6_hidden_tag_ids(), true );
    }
    return in_array( (string) $term, up6_hidden_tag_slugs(), true );
}

/* =============================================================
   HELPER: save IDs to option (flushes static caches)
   ============================================================= */

function up6_save_hidden_tag_ids( array $ids ): void {
    $clean = array_values( array_filter( array_map( 'absint', $ids ) ) );
    update_option( UP6_HIDDEN_TAGS_OPTION, implode( ',', $clean ) );
    // Bust static caches
    $GLOBALS['_up6_hidden_tag_ids_cache']   = null;
    $GLOBALS['_up6_hidden_tag_slugs_cache'] = null;
}

/* =============================================================
   COMPATIBILITY GUARD
   When Cipher Gate plugin is active it owns all filtering.
   Theme filters are registered only when the plugin is absent.
   ============================================================= */

function up6_hidden_tags_plugin_active(): bool {
    return defined( 'CG_VERSION' ) || function_exists( 'cg_hidden_tag_ids' );
}

add_action( 'init', function (): void {
    if ( up6_hidden_tags_plugin_active() ) {
        return; // Cipher Gate handles everything — stand down
    }
    up6_register_hidden_tag_filters();
} );

/**
 * Register all hidden tag exclusion filters.
 *
 * Hooks into pre_get_posts (public + widget queries), REST API,
 * XML sitemaps, tag cloud widget, and rendered tag output.
 * Only called when the Cipher Gate plugin is NOT active.
 */
function up6_register_hidden_tag_filters(): void {
    add_action( 'pre_get_posts',                [ 'UP6HiddenTagFilters', 'filter_public_queries'  ], 10 );
    add_action( 'pre_get_posts',                [ 'UP6HiddenTagFilters', 'filter_widget_queries'  ], 10 );
    add_filter( 'rest_post_query',              [ 'UP6HiddenTagFilters', 'filter_rest_queries'    ], 10, 2 );
    add_filter( 'wp_sitemaps_posts_query_args', [ 'UP6HiddenTagFilters', 'filter_sitemap_queries' ], 10 );
    add_filter( 'widget_tag_cloud_args',        [ 'UP6HiddenTagFilters', 'filter_tag_cloud'       ], 10 );
    add_filter( 'the_tags',                     [ 'UP6HiddenTagFilters', 'suppress_from_output'   ], 10, 3 );
}

/* =============================================================
   FILTER CLASS — mirrors the reference theme pattern
   ============================================================= */

class UP6HiddenTagFilters {

    /**
     * Exclude hidden-tagged posts from main front-end queries.
     */
    public static function filter_public_queries( WP_Query $query ): void {
        if ( is_admin() || ! $query->is_main_query() ) {
            return;
        }
        $ids = up6_hidden_tag_ids();
        if ( empty( $ids ) ) {
            return;
        }
        // On a hidden tag archive: suppress silently (treat as empty)
        if ( $query->is_tag() ) {
            $queried = get_queried_object();
            if ( $queried instanceof WP_Term && up6_is_hidden_tag( $queried->term_id ) ) {
                $query->set( 'post__in', [ 0 ] );
                return;
            }
        }
        self::apply_exclusion( $query, $ids );
    }

    /**
     * Exclude hidden-tagged posts from secondary/widget queries.
     */
    public static function filter_widget_queries( WP_Query $query ): void {
        if ( is_admin() || $query->is_main_query() ) {
            return;
        }
        $ids = up6_hidden_tag_ids();
        if ( empty( $ids ) ) {
            return;
        }
        self::apply_exclusion( $query, $ids );
    }

    /**
     * Exclude locked hidden-tagged posts from public REST requests.
     */
    public static function filter_rest_queries( array $args, WP_REST_Request $request ): array {
        if ( current_user_can( 'edit_others_posts' ) ) {
            return $args; // Editors see everything
        }
        $ids = up6_hidden_tag_ids();
        if ( empty( $ids ) ) {
            return $args;
        }
        $tax_query   = (array) ( $args['tax_query'] ?? [] );
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $ids,
            'operator' => 'NOT IN',
        ];
        $args['tax_query'] = $tax_query;
        return $args;
    }

    /**
     * Exclude hidden-tagged posts from XML sitemaps.
     */
    public static function filter_sitemap_queries( array $args ): array {
        $ids = up6_hidden_tag_ids();
        if ( empty( $ids ) ) {
            return $args;
        }
        $tax_query   = (array) ( $args['tax_query'] ?? [] );
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $ids,
            'operator' => 'NOT IN',
        ];
        $args['tax_query'] = $tax_query;
        return $args;
    }

    /**
     * Exclude hidden tags from the tag cloud widget.
     */
    public static function filter_tag_cloud( array $args ): array {
        $ids = up6_hidden_tag_ids();
        if ( empty( $ids ) ) {
            return $args;
        }
        $args['exclude'] = array_unique(
            array_merge( (array) ( $args['exclude'] ?? [] ), $ids )
        );
        return $args;
    }

    /**
     * Strip hidden tag links from rendered post tag output.
     */
    public static function suppress_from_output( ?string $tag_list, string $before, string $sep ): ?string {
        if ( null === $tag_list || '' === $tag_list ) {
            return $tag_list;
        }
        $slugs = up6_hidden_tag_slugs();
        if ( empty( $slugs ) ) {
            return $tag_list;
        }
        foreach ( $slugs as $slug ) {
            $pattern  = '~<a[^>]+/tag/' . preg_quote( $slug, '~' ) . '/[^>]*>.*?</a>~i';
            $tag_list = preg_replace( $pattern, '', $tag_list );
        }
        // Clean orphaned separators
        if ( '' !== $sep ) {
            $esc_sep  = preg_quote( trim( $sep ), '~' );
            $tag_list = preg_replace( '~(' . $esc_sep . '\s*){2,}~', $sep, $tag_list );
            $tag_list = trim( $tag_list, " \t\n\r\0\x0B" . trim( $sep ) );
        }
        return '' === trim( $tag_list ) ? null : $tag_list;
    }

    // ── Private helper ────────────────────────────────────────────────────

    /**
     * @param int[] $tag_ids
     */
    private static function apply_exclusion( WP_Query $query, array $tag_ids ): void {
        $tax_query   = (array) $query->get( 'tax_query' );
        $tax_query[] = [
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $tag_ids,
            'operator' => 'NOT IN',
        ];
        $query->set( 'tax_query', $tax_query );
    }
}

/* =============================================================
   BODY CLASS — strip hidden tag slugs (always, even with plugin)
   Applied here because the theme owns its own body_class output.
   ============================================================= */

add_filter( 'body_class', function ( array $classes ): array {
    $slugs = up6_hidden_tag_slugs();
    if ( empty( $slugs ) ) {
        return $classes;
    }
    return array_values( array_filter( $classes, function ( string $class ) use ( $slugs ): bool {
        foreach ( $slugs as $slug ) {
            if ( $class === 'tag-' . $slug || false !== strpos( $class, $slug ) ) {
                return false;
            }
        }
        return true;
    } ) );
}, 99 );

/* =============================================================
   ADMIN: save handler for Theme Options → Hidden Tags tab
   ============================================================= */

add_action( 'admin_init', function (): void {
    if ( ! isset( $_POST['up6_options_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['up6_options_nonce'], 'up6_save_options' ) ) {
        return;
    }
    if ( isset( $_POST['up6_hidden_tag_ids'] ) ) {
        $raw = array_map( 'absint', (array) $_POST['up6_hidden_tag_ids'] );
        up6_save_hidden_tag_ids( $raw );
    }
}, 5 ); // Priority 5 — before the main options save at default priority
