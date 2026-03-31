<?php
/**
 * UP6 Schema, SEO & Open Graph
 *
 * JSON-LD NewsArticle + BreadcrumbList, Open Graph, Twitter Card,
 * canonical URLs, noindex, pagination signals, contact page schema.
 *
 * Extracted from functions.php for maintainability.
 *
 * @package UP6
 * @since   2.8
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   NEWS SCHEMA — JSON-LD NewsArticle + Open Graph + Twitter Card
   Hooked early (priority 5) so it lands before wp_head plugins.
   ============================================================= */
function up6_head_meta() {

    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );

    // ── Shared values ──────────────────────────────────────────
    if ( is_singular( 'post' ) ) {
        $title     = get_the_title();
        $desc      = has_excerpt()
                        ? get_the_excerpt()
                        : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '' );
        $url       = get_permalink();
        $og_type   = 'article';
        $pub_date  = get_the_date( 'c' );
        $mod_date  = get_the_modified_date( 'c' );
        $author    = get_the_author_meta( 'display_name' );
        $author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
        $cats      = get_the_category();
        $section   = $cats ? $cats[0]->name : '';
        $thumb_url = has_post_thumbnail()
                        ? get_the_post_thumbnail_url( null, 'ss-hero' )
                        : '';
    } else {
        $title    = $site_name . ( get_bloginfo( 'description' ) ? ' — ' . get_bloginfo( 'description' ) : '' );
        $desc     = get_bloginfo( 'description' );
        $url      = is_front_page() ? $site_url : get_pagenum_link();
        $og_type  = 'website';
        $thumb_url = '';
    }

    $desc = esc_attr( wp_strip_all_tags( $desc ) );

    // ── Meta description ───────────────────────────────────────
    echo "\n<!-- UP6 SEO -->\n";
    echo '<meta name="description" content="' . $desc . '">' . "\n";

    // ── Open Graph ─────────────────────────────────────────────
    echo "\n<!-- UP6 Open Graph -->\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:title"     content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . $desc . '">' . "\n";
    echo '<meta property="og:url"       content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:type"      content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:locale"    content="' . esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) . '">' . "\n";
    if ( $thumb_url ) {
        echo '<meta property="og:image" content="' . esc_url( $thumb_url ) . '">' . "\n";
        echo '<meta property="og:image:width"  content="1200">' . "\n";
        echo '<meta property="og:image:height" content="675">' . "\n";
    }
    if ( $og_type === 'article' ) {
        echo '<meta property="article:published_time" content="' . esc_attr( $pub_date ) . '">' . "\n";
        echo '<meta property="article:modified_time"  content="' . esc_attr( $mod_date ) . '">' . "\n";
        echo '<meta property="article:author"         content="' . esc_attr( $author ) . '">' . "\n";
        if ( $section ) {
            echo '<meta property="article:section" content="' . esc_attr( $section ) . '">' . "\n";
        }
        $tags = get_the_tags();
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '">' . "\n";
            }
        }
    }

    // ── Twitter Card ───────────────────────────────────────────
    echo "\n<!-- UP6 Twitter Card -->\n";
    $card_type = $thumb_url ? 'summary_large_image' : 'summary';
    echo '<meta name="twitter:card"        content="' . $card_type . '">' . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . $desc . '">' . "\n";
    if ( $thumb_url ) {
        echo '<meta name="twitter:image" content="' . esc_url( $thumb_url ) . '">' . "\n";
    }

    // ── JSON-LD NewsArticle (single posts only) ────────────────
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    // Collect all available image sizes for the schema image array
    $images = [];
    if ( has_post_thumbnail() ) {
        foreach ( [ 'ss-hero', 'ss-single', 'ss-card' ] as $size ) {
            $img = get_the_post_thumbnail_url( null, $size );
            if ( $img ) {
                $images[] = $img;
            }
        }
    }

    // Publisher logo (custom logo upload, or omit)
    $logo_url = '';
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }

    // Keywords from WP tags
    $tag_objects = get_the_tags();
    $keywords    = $tag_objects
                    ? implode( ', ', wp_list_pluck( $tag_objects, 'name' ) )
                    : '';

    // Category sections (array if more than one)
    $cat_objects = get_the_category();
    $sections    = $cat_objects ? wp_list_pluck( $cat_objects, 'name' ) : [];

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'NewsArticle',
        'headline'         => get_the_title(),
        'description'      => wp_strip_all_tags( has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30, '' ) ),
        'url'              => get_permalink(),
        'datePublished'    => get_the_date( 'c' ),
        'dateModified'     => get_the_modified_date( 'c' ),
        'inLanguage'       => get_bloginfo( 'language' ),
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ],
        'author'           => [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name' ),
            'url'   => $author_url,
        ],
        'publisher'        => [
            '@type' => 'Organization',
            'name'  => $site_name,
            'url'   => $site_url,
        ],
    ];

    if ( ! empty( $images ) ) {
        $schema['image'] = count( $images ) === 1 ? $images[0] : $images;
    }
    if ( ! empty( $sections ) ) {
        $schema['articleSection'] = count( $sections ) === 1 ? $sections[0] : $sections;
    }
    if ( $keywords ) {
        $schema['keywords'] = $keywords;
    }
    if ( $logo_url ) {
        $schema['publisher']['logo'] = [
            '@type' => 'ImageObject',
            'url'   => $logo_url,
        ];
    }

    echo "\n<!-- UP6 NewsArticle Schema -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    echo "\n</script>\n";

    // ── JSON-LD BreadcrumbList ─────────────────────────────────
    // Mirrors the visible breadcrumb: Home › Category › Post Title
    $breadcrumb_items = [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => __( 'Home', 'up6' ),
            'item'     => home_url( '/' ),
        ],
    ];
    $position = 2;
    if ( ! empty( $cat_objects ) ) {
        $breadcrumb_items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $cat_objects[0]->name,
            'item'     => get_category_link( $cat_objects[0]->term_id ),
        ];
    }
    $breadcrumb_items[] = [
        '@type'    => 'ListItem',
        'position' => $position,
        'name'     => get_the_title(),
        'item'     => get_permalink(),
    ];

    $breadcrumb_schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumb_items,
    ];

    echo "\n<!-- UP6 BreadcrumbList Schema -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'up6_head_meta', 5 );

/* =============================================================
   SEO — Canonical URL
   ============================================================= */
add_action( 'wp_head', function (): void {
    if ( function_exists( 'wpseo_init' ) || function_exists( 'rankmath' ) ) {
        return;
    }
    $canonical = wp_get_canonical_url();
    if ( $canonical ) {
        echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
    }
}, 5 );

/* =============================================================
   SEO — noindex on search results and policy page templates
   Controlled by Theme Options → General → noindex search/policy.
   On by default. Disable only if you need these pages indexed.
   ============================================================= */
add_filter( 'wp_robots', function ( array $robots ): array {
    // Defer to Yoast SEO or RankMath — they manage robots directives themselves
    if ( function_exists( 'wpseo_init' ) || function_exists( 'rankmath' ) ) {
        return $robots;
    }
    if ( ! (int) up6_opt( 'up6_noindex_search_policy' ) ) {
        return $robots;
    }
    if ( is_search() ) {
        $robots['noindex'] = true;
        unset( $robots['max-snippet'], $robots['max-image-preview'], $robots['max-video-preview'] );
        return $robots;
    }
    $noindex_templates = [
        'template-privacy-policy.php',
        'template-disclaimer.php',
        'template-corrections.php',
    ];
    if ( is_page() && in_array( get_page_template_slug(), $noindex_templates, true ) ) {
        $robots['noindex'] = true;
        unset( $robots['max-snippet'], $robots['max-image-preview'], $robots['max-video-preview'] );
    }
    return $robots;
} );

/* =============================================================
   SEO — rel prev/next on paginated archives
   ============================================================= */
add_action( 'wp_head', function (): void {
    if ( ! is_archive() && ! is_search() && ! is_home() ) {
        return;
    }
    global $paged, $wp_query;
    $max = (int) $wp_query->max_num_pages;
    $cur = max( 1, (int) $paged );
    if ( $cur > 1 ) {
        echo '<link rel="prev" href="' . esc_url( get_pagenum_link( $cur - 1 ) ) . '">' . "\n";
    }
    if ( $cur < $max ) {
        echo '<link rel="next" href="' . esc_url( get_pagenum_link( $cur + 1 ) ) . '">' . "\n";
    }
}, 5 );

/* =============================================================
   CONTACT PAGE — NewsMediaOrganization JSON-LD
   Fires on wp_head only when the Contact page template is active.
   NAP data pulled from Theme Options (shared with footer).
   sameAs array built from non-empty social URLs.
   ============================================================= */
function up6_contact_schema_json_ld() {
    // Only fire on the contact page template
    if ( ! is_page_template( 'template-contact.php' ) ) {
        return;
    }

    $site_name = get_bloginfo( 'name' );
    $site_url  = home_url( '/' );
    $address   = up6_opt( 'ss_contact_address' );
    $phone     = up6_opt( 'ss_contact_phone' );
    $email     = up6_opt( 'ss_contact_email' );

    // Logo
    $logo_url       = '';
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }

    // sameAs — only include non-empty social URLs
    $social_keys = [
        'ss_social_facebook',
        'ss_social_x',
        'ss_social_instagram',
        'ss_social_telegram',
        'ss_social_threads',
        'ss_social_whatsapp',
    ];
    $same_as = [];
    foreach ( $social_keys as $key ) {
        $url = up6_opt( $key );
        if ( ! empty( $url ) ) {
            $same_as[] = esc_url_raw( $url );
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'NewsMediaOrganization',
        '@id'      => $site_url . '#organization',
        'name'     => $site_name,
        'url'      => $site_url,
    ];

    // Contact point
    if ( $email ) {
        $schema['contactPoint'] = [
            '@type'       => 'ContactPoint',
            'contactType' => 'customer support',
            'email'       => sanitize_email( $email ),
            'areaServed'  => 'MY',
            'availableLanguage' => [ 'Malay', 'English' ],
        ];
    }

    // Address (PostalAddress) — split on last comma for locality/country
    if ( $address ) {
        $schema['address'] = [
            '@type'          => 'PostalAddress',
            'streetAddress'  => sanitize_text_field( $address ),
            'addressLocality' => 'Kuala Lumpur',
            'addressCountry' => 'MY',
        ];
    }

    if ( $phone ) {
        $schema['telephone'] = sanitize_text_field( $phone );
    }

    if ( $email ) {
        $schema['email'] = sanitize_email( $email );
    }

    if ( ! empty( $same_as ) ) {
        $schema['sameAs'] = $same_as;
    }

    if ( $logo_url ) {
        $schema['logo'] = [
            '@type' => 'ImageObject',
            'url'   => esc_url_raw( $logo_url ),
        ];
    }

    echo "\n<!-- UP6 NewsMediaOrganization Schema -->\n";
    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'up6_contact_schema_json_ld', 6 );

