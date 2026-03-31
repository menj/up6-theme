<?php
/**
 * UP6 Theme Options — tabbed admin page
 *
 * Reads and writes theme_mod values so it stays in sync
 * with the WordPress Customizer.
 *
 * @package UP6
 * @since   2.3.0
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   DEFAULTS
   ============================================================= */
function up6_option_defaults() {
    return [
        // Footer
        'ss_footer_description' => 'Providing independent, high-quality journalism for the modern digital landscape.',
        'ss_contact_address'    => '123 Newsroom Plaza, Media District, Kuala Lumpur, 50000',
        'ss_contact_phone'      => '+60 3 1234 5678',
        'ss_contact_email'      => 'contact@up6.org',
        'ss_copyright'          => '© ' . date( 'Y' ) . ' UP6. All rights reserved.',
        'ss_legal_notice'       => '',

        // Social
        'ss_social_facebook'    => '',
        'ss_social_x'           => '',
        'ss_social_telegram'    => '',
        'ss_social_instagram'   => '',
        'ss_social_threads'     => '',
        'ss_social_whatsapp'    => '',

        // Homepage
        'up6_homepage_cat_count'       => 4,
        'up6_homepage_posts_per_cat'   => 3,
        'up6_homepage_recent_count'    => 5,
        'up6_homepage_show_empty_cats' => 0,
        'up6_excerpt_length'           => 35,

        // Sidebar
        'up6_sidebar_cat_count'        => 6,
        'up6_related_count'            => 4,

        // General
        'up6_hijri_offset'             => 0,
        'up6_most_viewed_days'         => 5,
        'up6_copy_protect'             => 0,
        'up6_noindex_search_policy'    => 1,
        'up6_festive_occasion'         => '',
        'up6_festive_from'             => '',
        'up6_festive_until'            => '',

        // Voting
        'up6_vote_enabled'             => 1,
        'up6_vote_threshold'           => 1,
        'up6_vote_label'               => '',
        'up6_cat_colours'              => '',

        // Contact page
        'up6_cf7_form_id'              => 0,
        'up6_maps_api_key'             => '',
        'up6_maps_place_id'            => '',
    ];
}

/* =============================================================
   REGISTER ADMIN PAGE
   ============================================================= */
function up6_add_options_page() {
    add_theme_page(
        __( 'UP6 Theme Options', 'up6' ),
        __( 'Theme Options', 'up6' ),
        'edit_theme_options',
        'up6-theme-options',
        'up6_render_options_page'
    );
}
add_action( 'admin_menu', 'up6_add_options_page' );

/* =============================================================
   ENQUEUE ADMIN ASSETS
   ============================================================= */
function up6_admin_assets( $hook ) {
    if ( 'appearance_page_up6-theme-options' !== $hook ) {
        return;
    }

    $dir = get_stylesheet_directory_uri();
    $ver = wp_get_theme()->get( 'Version' );
    $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'up6-admin-options',
        $dir . '/css/admin-options' . $min . '.css',
        [],
        $ver
    );

    wp_enqueue_script(
        'up6-admin-options',
        $dir . '/js/admin-options' . $min . '.js',
        [],
        $ver,
        true
    );

    wp_localize_script( 'up6-admin-options', 'up6Scanner', [
        'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
        'scanNonce'        => wp_create_nonce( 'up6_scanner_scan_now' ),
        'regenNonce'       => wp_create_nonce( 'up6_scanner_regen_checksums' ),
        'scanningText'     => __( 'Scanning…', 'up6' ),
        'regenText'        => __( 'Regenerating…', 'up6' ),
        'errorText'        => __( 'Error — please try again.', 'up6' ),
        'scanNowText'      => __( 'Scan Now', 'up6' ),
        'regenBtnText'     => __( 'Regenerate Baseline', 'up6' ),
        'themesScanned'    => __( 'themes scanned', 'up6' ),
        'allClean'         => __( 'all clean', 'up6' ),
        'flaggedFound'     => __( 'flagged', 'up6' ),
        'integrityOk'      => __( 'UP6 files: ✓ integrity OK', 'up6' ),
        'integrityNoBase'  => __( 'UP6 files: no baseline — generate one below', 'up6' ),
        'completed'        => __( 'Completed:', 'up6' ),
        'baselineUpdated'  => __( 'Baseline updated', 'up6' ),
        'filesHashed'      => __( 'files hashed', 'up6' ),
    ] );
}
add_action( 'admin_enqueue_scripts', 'up6_admin_assets' );

/* =============================================================
   SAVE HANDLER
   ============================================================= */
function up6_save_options() {
    if ( ! isset( $_POST['up6_options_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['up6_options_nonce'], 'up6_save_options' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $defaults   = up6_option_defaults();
    $sanitizers = [
        'ss_footer_description'        => 'wp_kses_post',
        'ss_contact_address'           => 'sanitize_textarea_field',
        'ss_contact_phone'             => 'sanitize_text_field',
        'ss_contact_email'             => 'sanitize_email',
        'ss_copyright'                 => 'wp_kses_post',
        'ss_legal_notice'              => 'wp_kses_post',
        'ss_social_facebook'           => 'esc_url_raw',
        'ss_social_x'                  => 'esc_url_raw',
        'ss_social_telegram'           => 'esc_url_raw',
        'ss_social_instagram'          => 'esc_url_raw',
        'ss_social_threads'            => 'esc_url_raw',
        'ss_social_whatsapp'           => 'esc_url_raw',
        'up6_homepage_cat_count'       => 'absint',
        'up6_homepage_posts_per_cat'   => 'absint',
        'up6_homepage_recent_count'    => 'absint',
        'up6_homepage_show_empty_cats' => 'absint',
        'up6_excerpt_length'           => 'absint',
        'up6_sidebar_cat_count'        => 'absint',
        'up6_related_count'            => 'absint',
        'up6_hijri_offset'             => 'intval',
        'up6_most_viewed_days'         => 'absint',
        'up6_copy_protect'             => 'absint',
        'up6_noindex_search_policy'    => 'absint',
        'up6_festive_occasion'         => 'sanitize_key',
        'up6_festive_from'             => 'sanitize_text_field',
        'up6_festive_until'            => 'sanitize_text_field',
        'up6_vote_enabled'             => 'absint',
        'up6_vote_threshold'           => 'absint',
        'up6_vote_label'               => 'sanitize_text_field',
        'up6_cat_colours'              => 'sanitize_textarea_field',
        'up6_cf7_form_id'              => 'absint',
        'up6_maps_api_key'             => 'sanitize_text_field',
        'up6_maps_place_id'            => 'sanitize_text_field',
    ];

    // Clamp ranges: [ min, max ] — declared before loop, not inside it
    $clamps = [
        'up6_homepage_cat_count'     => [  1, 20 ],
        'up6_homepage_posts_per_cat' => [  1, 12 ],
        'up6_homepage_recent_count'  => [  0, 50 ],
        'up6_excerpt_length'         => [ 10, 80 ],
        'up6_sidebar_cat_count'      => [  1, 20 ],
        'up6_related_count'          => [  3, 12 ],
        'up6_hijri_offset'           => [ -1,  1 ],
        'up6_most_viewed_days'       => [  1, 30 ],
        'up6_vote_threshold'         => [  1, 100 ],
    ];

    foreach ( $sanitizers as $key => $callback ) {
        $value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : $defaults[ $key ];

        // Checkbox: present = 1, absent = 0
        if ( 'up6_homepage_show_empty_cats' === $key || 'up6_copy_protect' === $key || 'up6_noindex_search_policy' === $key || 'up6_vote_enabled' === $key ) {
            $value = isset( $_POST[ $key ] ) ? 1 : 0;
        }

        $value = call_user_func( $callback, $value );

        if ( isset( $clamps[ $key ] ) ) {
            $value = max( $clamps[ $key ][0], min( $clamps[ $key ][1], $value ) );
        }

        set_theme_mod( $key, $value );
    }

    // Redirect with active tab preserved
    $tab = isset( $_POST['up6_active_tab'] ) ? sanitize_key( $_POST['up6_active_tab'] ) : 'footer';
    wp_safe_redirect( add_query_arg( [
        'page'    => 'up6-theme-options',
        'tab'     => $tab,
        'updated' => 'true',
    ], admin_url( 'themes.php' ) ) );
    exit;
}
add_action( 'admin_init', 'up6_save_options' );

/* =============================================================
   HELPER: get option value (theme_mod with fallback)
   ============================================================= */
function up6_opt( $key ) {
    $defaults = up6_option_defaults();
    return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/* =============================================================
   RENDER: OPTIONS PAGE
   ============================================================= */
function up6_render_options_page() {
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'footer';
    $tabs = [
        'footer'      => __( 'Footer', 'up6' ),
        'social'      => __( 'Social Media', 'up6' ),
        'homepage'    => __( 'Homepage', 'up6' ),
        'contact'     => __( 'Contact', 'up6' ),
        'general'     => __( 'General', 'up6' ),
        'hidden-tags' => __( 'Hidden Tags', 'up6' ),
        'security'    => __( 'Security', 'up6' ),
    ];
    ?>
    <div class="wrap up6-options-wrap">

        <div class="up6-options-header">
            <div class="up6-options-brand">
                <span class="up6-options-dot"></span>
                <h1><?php
                    $name   = get_bloginfo( 'name' );
                    $parts  = preg_split( '/\s+/', esc_html( $name ), 2 );
                    $first  = isset( $parts[0] ) ? $parts[0] : esc_html( $name );
                    $rest   = isset( $parts[1] ) ? $parts[1] : '';
                    // Digit accent: red on white bg (beige #C4B5A5 is unreadable on white)
                    $first  = preg_replace( '/(\d+)$/', '<span style="color:#C4B5A5;font-weight:900;">$1</span>', $first );
                    if ( $rest !== '' ) {
                        echo $first . ' <span style="color:#d4564a;">' . $rest . '</span>';
                    } else {
                        echo $first;
                    }
                ?></h1>
            </div>
            <span class="up6-options-version">v<?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></span>
        </div>

        <?php if ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] ) : ?>
            <div class="up6-notice up6-notice-success">
                <p><?php esc_html_e( 'Settings saved.', 'up6' ); ?></p>
            </div>
        <?php endif; ?>

        <div class="up6-options-body">

            <!-- Tab navigation -->
            <nav class="up6-tabs" role="tablist">
                <?php foreach ( $tabs as $slug => $label ) : ?>
                    <button type="button"
                            class="up6-tab<?php echo $slug === $active_tab ? ' is-active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo $slug === $active_tab ? 'true' : 'false'; ?>"
                            data-tab="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </button>
                <?php endforeach; ?>
            </nav>

            <!-- Form -->
            <form method="post" action="" class="up6-options-form">
                <?php wp_nonce_field( 'up6_save_options', 'up6_options_nonce' ); ?>
                <input type="hidden" name="up6_active_tab" id="up6_active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />

                <!-- ── TAB: Footer ── -->
                <div class="up6-tab-panel<?php echo 'footer' === $active_tab ? ' is-active' : ''; ?>" id="tab-footer" role="tabpanel">

                    <div class="up6-field">
                        <label for="ss_footer_description"><?php esc_html_e( 'Footer Description', 'up6' ); ?></label>
                        <textarea id="ss_footer_description" name="ss_footer_description" rows="3"><?php echo esc_textarea( up6_opt( 'ss_footer_description' ) ); ?></textarea>
                        <p class="up6-field-hint"><?php esc_html_e( 'Shown below the brand name in the footer. Supports HTML (e.g. <a>, <strong>, <br>) and WordPress shortcodes.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="ss_contact_address"><?php esc_html_e( 'Contact Address', 'up6' ); ?></label>
                        <textarea id="ss_contact_address" name="ss_contact_address" rows="2"><?php echo esc_textarea( up6_opt( 'ss_contact_address' ) ); ?></textarea>
                        <p class="up6-field-hint"><?php esc_html_e( 'Street address shown in the footer contact block. Use line breaks for multi-line addresses.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="ss_contact_phone"><?php esc_html_e( 'Phone', 'up6' ); ?></label>
                            <input type="text" id="ss_contact_phone" name="ss_contact_phone" value="<?php echo esc_attr( up6_opt( 'ss_contact_phone' ) ); ?>" />
                            <p class="up6-field-hint"><?php esc_html_e( 'e.g. +60 3-1234 5678', 'up6' ); ?></p>
                        </div>
                        <div class="up6-field">
                            <label for="ss_contact_email"><?php esc_html_e( 'Email', 'up6' ); ?></label>
                            <input type="email" id="ss_contact_email" name="ss_contact_email" value="<?php echo esc_attr( up6_opt( 'ss_contact_email' ) ); ?>" />
                            <p class="up6-field-hint"><?php esc_html_e( 'e.g. editor@up6.org', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field">
                        <label for="ss_copyright"><?php esc_html_e( 'Copyright Line', 'up6' ); ?></label>
                        <textarea id="ss_copyright" name="ss_copyright" rows="2"><?php echo wp_kses_post( up6_opt( 'ss_copyright' ) ); ?></textarea>
                        <p class="up6-field-hint"><?php esc_html_e( 'Shown in the footer bar. Supports HTML — you may use <a href="...">, <strong>, <em>, and <br>. e.g. © 2026 UP6 Suara Semasa. Hak cipta terpelihara.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="ss_legal_notice"><?php esc_html_e( 'Legal / Ownership Notice', 'up6' ); ?></label>
                        <textarea id="ss_legal_notice" name="ss_legal_notice" rows="3"><?php echo wp_kses_post( up6_opt( 'ss_legal_notice' ) ); ?></textarea>
                        <p class="up6-field-hint"><?php esc_html_e( 'Ownership or registration declaration shown in the dark band below the footer bar. Supports HTML — you may use <a href="...">, <strong>, <em>, and <br>. Leave blank to hide.', 'up6' ); ?></p>
                    </div>

                </div>

                <!-- ── TAB: Social Media ── -->
                <div class="up6-tab-panel<?php echo 'social' === $active_tab ? ' is-active' : ''; ?>" id="tab-social" role="tabpanel">

                    <div class="up6-field">
                        <label for="ss_social_facebook"><?php esc_html_e( 'Facebook URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_facebook" name="ss_social_facebook" value="<?php echo esc_attr( up6_opt( 'ss_social_facebook' ) ); ?>" placeholder="https://facebook.com/..." />
                    </div>

                    <div class="up6-field">
                        <label for="ss_social_x"><?php esc_html_e( 'X (Twitter) URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_x" name="ss_social_x" value="<?php echo esc_attr( up6_opt( 'ss_social_x' ) ); ?>" placeholder="https://x.com/..." />
                    </div>

                    <div class="up6-field">
                        <label for="ss_social_instagram"><?php esc_html_e( 'Instagram URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_instagram" name="ss_social_instagram" value="<?php echo esc_attr( up6_opt( 'ss_social_instagram' ) ); ?>" placeholder="https://instagram.com/..." />
                    </div>

                    <div class="up6-field">
                        <label for="ss_social_threads"><?php esc_html_e( 'Threads URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_threads" name="ss_social_threads" value="<?php echo esc_attr( up6_opt( 'ss_social_threads' ) ); ?>" placeholder="https://threads.net/..." />
                    </div>

                    <div class="up6-field">
                        <label for="ss_social_telegram"><?php esc_html_e( 'Telegram URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_telegram" name="ss_social_telegram" value="<?php echo esc_attr( up6_opt( 'ss_social_telegram' ) ); ?>" placeholder="https://t.me/..." />
                    </div>

                    <div class="up6-field">
                        <label for="ss_social_whatsapp"><?php esc_html_e( 'WhatsApp URL', 'up6' ); ?></label>
                        <input type="url" id="ss_social_whatsapp" name="ss_social_whatsapp" value="<?php echo esc_attr( up6_opt( 'ss_social_whatsapp' ) ); ?>" placeholder="https://wa.me/..." />
                    </div>

                    <p class="up6-field-hint"><?php esc_html_e( 'Icons only appear in the footer when a URL is provided. Leave blank to hide.', 'up6' ); ?></p>

                </div>

                <!-- ── TAB: Homepage ── -->
                <div class="up6-tab-panel<?php echo 'homepage' === $active_tab ? ' is-active' : ''; ?>" id="tab-homepage" role="tabpanel">

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="up6_homepage_cat_count"><?php esc_html_e( 'Category sections to display', 'up6' ); ?></label>
                            <input type="number" id="up6_homepage_cat_count" name="up6_homepage_cat_count" value="<?php echo esc_attr( up6_opt( 'up6_homepage_cat_count' ) ); ?>" min="1" max="20" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Number of category sections shown on the homepage.', 'up6' ); ?></p>
                        </div>
                        <div class="up6-field">
                            <label for="up6_homepage_posts_per_cat"><?php esc_html_e( 'Posts per category', 'up6' ); ?></label>
                            <input type="number" id="up6_homepage_posts_per_cat" name="up6_homepage_posts_per_cat" value="<?php echo esc_attr( up6_opt( 'up6_homepage_posts_per_cat' ) ); ?>" min="1" max="12" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Cards shown in each category grid.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="up6_homepage_recent_count"><?php esc_html_e( 'Terbaharu — Most Recent posts', 'up6' ); ?></label>
                            <input type="number" id="up6_homepage_recent_count" name="up6_homepage_recent_count" value="<?php echo esc_attr( up6_opt( 'up6_homepage_recent_count' ) ); ?>" min="0" max="50" style="width:6rem;" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Number of posts shown in the Terbaharu (Most Recent) list on the homepage. Set to 0 to hide the section entirely. Max 50.', 'up6' ); ?></p>
                        </div>
                        <div class="up6-field">
                            <label for="up6_excerpt_length"><?php esc_html_e( 'Excerpt length (words)', 'up6' ); ?></label>
                            <input type="number" id="up6_excerpt_length" name="up6_excerpt_length" value="<?php echo esc_attr( up6_opt( 'up6_excerpt_length' ) ); ?>" min="10" max="80" style="width:6rem;" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Number of words shown in card excerpts. Excerpts always end on a full word. Min 10, max 80.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="up6_sidebar_cat_count"><?php esc_html_e( 'Sidebar categories', 'up6' ); ?></label>
                            <input type="number" id="up6_sidebar_cat_count" name="up6_sidebar_cat_count" value="<?php echo esc_attr( up6_opt( 'up6_sidebar_cat_count' ) ); ?>" min="1" max="20" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Number of categories shown in the sidebar panel.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="up6_related_count"><?php esc_html_e( 'Related News cards', 'up6' ); ?></label>
                            <input type="number" id="up6_related_count" name="up6_related_count" value="<?php echo esc_attr( up6_opt( 'up6_related_count' ) ); ?>" min="3" max="12" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Number of related articles shown at the bottom of each post. Min 3, max 12.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label class="up6-checkbox-label">
                                <input type="checkbox" name="up6_homepage_show_empty_cats" value="1" <?php checked( up6_opt( 'up6_homepage_show_empty_cats' ), 1 ); ?> />
                                <?php esc_html_e( 'Show empty categories', 'up6' ); ?>
                            </label>
                            <p class="up6-field-hint"><?php esc_html_e( 'Display categories that have no articles yet.', 'up6' ); ?></p>
                        </div>
                    </div>

                </div>

                <!-- ── TAB: Contact ── -->
                <div class="up6-tab-panel<?php echo 'contact' === $active_tab ? ' is-active' : ''; ?>" id="tab-contact" role="tabpanel">

                    <div class="up6-field">
                        <label for="up6_cf7_form_id"><?php esc_html_e( 'Contact Form 7 — Form ID', 'up6' ); ?></label>
                        <input type="number" id="up6_cf7_form_id" name="up6_cf7_form_id" value="<?php echo esc_attr( up6_opt( 'up6_cf7_form_id' ) ); ?>" min="0" style="width:8rem;" />
                        <p class="up6-field-hint"><?php esc_html_e( 'The numeric ID of your CF7 form. Find it in Contact → Contact Forms — the ID appears in the shortcode column. Set to 0 if not yet configured.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="up6_maps_api_key"><?php esc_html_e( 'Google Maps Embed API Key', 'up6' ); ?></label>
                        <input type="text" id="up6_maps_api_key" name="up6_maps_api_key" value="<?php echo esc_attr( up6_opt( 'up6_maps_api_key' ) ); ?>" placeholder="AIza..." style="width:100%;max-width:480px;" autocomplete="off" />
                        <p class="up6-field-hint"><?php esc_html_e( 'Requires the Maps Embed API enabled in Google Cloud Console. Restrict the key to this domain.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="up6_maps_place_id"><?php esc_html_e( 'Google Maps Place ID', 'up6' ); ?></label>
                        <input type="text" id="up6_maps_place_id" name="up6_maps_place_id" value="<?php echo esc_attr( up6_opt( 'up6_maps_place_id' ) ); ?>" placeholder="ChIJ..." style="width:100%;max-width:480px;" />
                        <p class="up6-field-hint">
                            <?php
                            printf(
                                /* translators: %s: link to Google Place ID finder */
                                esc_html__( 'Find your Place ID using the %s. Paste it here — it begins with "ChIJ".', 'up6' ),
                                '<a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank" rel="noopener">' . esc_html__( 'Place ID Finder', 'up6' ) . '</a>'
                            );
                            ?>
                        </p>
                    </div>

                </div>

                <!-- ── TAB: General ── -->
                <div class="up6-tab-panel<?php echo 'general' === $active_tab ? ' is-active' : ''; ?>" id="tab-general" role="tabpanel">

                    <div class="up6-field">
                        <label for="up6_hijri_offset"><?php esc_html_e( 'Hijri Date Offset', 'up6' ); ?></label>
                        <select id="up6_hijri_offset" name="up6_hijri_offset">
                            <option value="-1" <?php selected( up6_opt( 'up6_hijri_offset' ), -1 ); ?>>-1 <?php esc_html_e( '(one day behind)', 'up6' ); ?></option>
                            <option value="0"  <?php selected( up6_opt( 'up6_hijri_offset' ),  0 ); ?>><?php esc_html_e( '0 — Astronomical (default)', 'up6' ); ?></option>
                            <option value="1"  <?php selected( up6_opt( 'up6_hijri_offset' ),  1 ); ?>>+1 <?php esc_html_e( '(one day ahead)', 'up6' ); ?></option>
                        </select>
                        <p class="up6-field-hint"><?php esc_html_e( 'The Hijri date is calculated astronomically. Malaysia determines the date by moon sighting, which may differ by ±1 day. Set to -1 if the displayed date is one day ahead of the official Malaysian date.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="up6_most_viewed_days"><?php esc_html_e( 'Most Viewed — Day Range', 'up6' ); ?></label>
                        <input type="number" id="up6_most_viewed_days" name="up6_most_viewed_days" value="<?php echo esc_attr( up6_opt( 'up6_most_viewed_days' ) ); ?>" min="1" max="30" style="width:5rem;" />
                        <p class="up6-field-hint"><?php esc_html_e( 'Number of days to look back when ranking most-viewed posts in the sidebar (1–30). Default: 5.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label class="up6-checkbox-label">
                                <input type="checkbox" name="up6_copy_protect" value="1" <?php checked( up6_opt( 'up6_copy_protect' ), 1 ); ?> />
                                <?php esc_html_e( 'Enable content copy protection', 'up6' ); ?>
                            </label>
                            <p class="up6-field-hint"><?php esc_html_e( 'Disables right-click, text selection, and copy shortcuts for non-admin visitors. Does not affect logged-in editors or administrators. Note: this is a deterrent only — it cannot prevent determined copying.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label class="up6-checkbox-label">
                                <input type="checkbox" name="up6_noindex_search_policy" value="1" <?php checked( up6_opt( 'up6_noindex_search_policy' ), 1 ); ?> />
                                <?php esc_html_e( 'noindex search results and policy pages', 'up6' ); ?>
                            </label>
                            <p class="up6-field-hint"><?php esc_html_e( 'Adds a noindex robots directive to search result pages and the Privacy Policy, Disclaimer, and Corrections page templates. Enabled by default — these pages carry no SEO value. Disable only if you have a specific reason to index them.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <hr class="up6-field-divider" />

                    <div class="up6-field">
                        <label for="up6_festive_occasion"><?php esc_html_e( 'Festive Occasion', 'up6' ); ?></label>
                        <?php
                        $festive_options = up6_festive_occasions();
                        $current_occasion = up6_opt( 'up6_festive_occasion' );
                        ?>
                        <select id="up6_festive_occasion" name="up6_festive_occasion">
                            <option value=""><?php esc_html_e( 'None', 'up6' ); ?></option>
                            <?php foreach ( $festive_options as $slug => $label ) : ?>
                                <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $current_occasion, $slug ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="up6-field-hint"><?php esc_html_e( 'Show a festive icon beside the site logo in the header. Select "None" to hide.', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field-row">
                        <div class="up6-field">
                            <label for="up6_festive_from"><?php esc_html_e( 'Show from', 'up6' ); ?></label>
                            <input type="date" id="up6_festive_from" name="up6_festive_from" value="<?php echo esc_attr( up6_opt( 'up6_festive_from' ) ); ?>" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Optional. Icon appears from this date (inclusive).', 'up6' ); ?></p>
                        </div>
                        <div class="up6-field">
                            <label for="up6_festive_until"><?php esc_html_e( 'Show until', 'up6' ); ?></label>
                            <input type="date" id="up6_festive_until" name="up6_festive_until" value="<?php echo esc_attr( up6_opt( 'up6_festive_until' ) ); ?>" />
                            <p class="up6-field-hint"><?php esc_html_e( 'Optional. Icon disappears after this date. Leave both blank to show until manually changed.', 'up6' ); ?></p>
                        </div>
                    </div>

                    <hr class="up6-field-divider" />

                    <div class="up6-field">
                        <label><?php esc_html_e( 'Article Voting', 'up6' ); ?></label>
                        <label class="up6-checkbox-label">
                            <input type="checkbox" name="up6_vote_enabled" value="1" <?php checked( up6_opt( 'up6_vote_enabled' ), 1 ); ?> />
                            <?php esc_html_e( 'Enable article voting (thumbs up / thumbs down)', 'up6' ); ?>
                        </label>
                        <p class="up6-field-hint"><?php esc_html_e( 'Shows vote buttons below the article content on single posts. Visitors can vote once per article (logged-in users tracked by account, guests by cookie).', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="up6_vote_threshold"><?php esc_html_e( 'Vote count display threshold', 'up6' ); ?></label>
                        <input type="number" id="up6_vote_threshold" name="up6_vote_threshold"
                               value="<?php echo esc_attr( up6_opt( 'up6_vote_threshold' ) ); ?>"
                               min="1" max="100" step="1" style="width:5rem;" />
                        <p class="up6-field-hint"><?php esc_html_e( 'Vote counts are hidden until this many total votes are reached. Prevents bare "0 / 0" on new articles. Default: 1 (always show).', 'up6' ); ?></p>
                    </div>

                    <div class="up6-field">
                        <label for="up6_vote_label"><?php esc_html_e( 'Vote prompt label', 'up6' ); ?></label>
                        <input type="text" id="up6_vote_label" name="up6_vote_label"
                               value="<?php echo esc_attr( up6_opt( 'up6_vote_label' ) ); ?>"
                               placeholder="<?php esc_attr_e( 'Leave blank for no label (recommended for news)', 'up6' ); ?>"
                               style="width:100%;max-width:28rem;" />
                        <p class="up6-field-hint"><?php esc_html_e( 'Optional text shown beside the vote buttons. Leave empty to show only the thumbs (recommended for news sites). Examples: "Adakah artikel ini berguna?" or "Apa pendapat anda?"', 'up6' ); ?></p>
                    </div>

                    <hr class="up6-field-divider" />

                    <div class="up6-field">
                        <label for="up6_cat_colours"><?php esc_html_e( 'Category Colours', 'up6' ); ?></label>
                        <textarea id="up6_cat_colours" name="up6_cat_colours" rows="5" style="width:100%;max-width:28rem;font-family:monospace;font-size:12px;"><?php echo esc_textarea( up6_opt( 'up6_cat_colours' ) ); ?></textarea>
                        <p class="up6-field-hint"><?php esc_html_e( 'Custom colours for category badges. One per line: slug:#hex. Example: politik:#1B3C53. Leave empty to use the automatic palette. Applies to homepage cards, hero badge, article badges, and archive headers.', 'up6' ); ?></p>
                    </div>

                </div>

                <!-- ── TAB: Hidden Tags ── -->
                <div class="up6-tab-panel<?php echo 'hidden-tags' === $active_tab ? ' is-active' : ''; ?>" id="tab-hidden-tags" role="tabpanel">

                    <?php if ( up6_hidden_tags_plugin_active() ) : ?>
                        <div class="up6-field" style="background:#fff8e1;border:1px solid #ffe082;border-radius:4px;padding:.75rem 1rem;">
                            <strong><?php esc_html_e( 'Cipher Gate plugin is active.', 'up6' ); ?></strong><br />
                            <span style="font-size:.8rem;color:#555;"><?php esc_html_e( 'Hidden tag management and filtering is handled by the plugin. Configure at Cipher Gate → Hidden Tags in the admin menu.', 'up6' ); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="up6-field">
                            <p style="margin-top:0;font-size:.8125rem;color:#555;">
                                <?php esc_html_e( 'Tags selected here are hidden from all public discovery — queries, widgets, REST API, XML sitemaps, tag clouds, and rendered tag lists. Term IDs are stored, so renaming a tag slug does not break the registry.', 'up6' ); ?>
                            </p>
                            <label><?php esc_html_e( 'Hidden tags', 'up6' ); ?></label>
                            <?php
                            $up6_all_tags     = get_terms( [ 'taxonomy' => 'post_tag', 'hide_empty' => false, 'number' => 500 ] );
                            $up6_all_tags     = is_wp_error( $up6_all_tags ) ? [] : $up6_all_tags;
                            $up6_hidden_ids   = up6_hidden_tag_ids();
                            ?>
                            <?php if ( empty( $up6_all_tags ) ) : ?>
                                <p style="color:#999;font-size:.8rem;"><?php esc_html_e( 'No tags found. Create some tags first.', 'up6' ); ?></p>
                            <?php else : ?>
                                <div style="border:1px solid #dcdcde;border-radius:4px;max-width:480px;">
                                    <div style="padding:.5rem;background:#f6f7f7;border-bottom:1px solid #dcdcde;">
                                        <input type="text" id="up6_tag_search" placeholder="<?php esc_attr_e( 'Search tags…', 'up6' ); ?>" style="width:100%;padding:.375rem .625rem;border:1px solid #dcdcde;border-radius:3px;font-size:.8125rem;" />
                                    </div>
                                    <div id="up6_tag_list" style="max-height:220px;overflow-y:auto;padding:.5rem;">
                                        <?php foreach ( $up6_all_tags as $up6_tag ) : ?>
                                            <label style="display:flex;align-items:center;gap:.5rem;padding:.25rem .5rem;font-size:.8125rem;cursor:pointer;">
                                                <input type="checkbox"
                                                       name="up6_hidden_tag_ids[]"
                                                       value="<?php echo esc_attr( $up6_tag->term_id ); ?>"
                                                       <?php checked( in_array( $up6_tag->term_id, $up6_hidden_ids, true ) ); ?> />
                                                <?php echo esc_html( $up6_tag->name ); ?>
                                                <code style="font-size:.7rem;color:#aaa;">#<?php echo absint( $up6_tag->term_id ); ?></code>
                                                <span style="color:#aaa;font-size:.7rem;">(<?php echo absint( $up6_tag->count ); ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <p class="up6-field-hint"><?php esc_html_e( 'Unchecking all tags and saving will clear the registry entirely.', 'up6' ); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Save button -->
                <div class="up6-options-footer">
                    <button type="submit" class="up6-btn-save"><?php esc_html_e( 'Save Changes', 'up6' ); ?></button>
                </div>

            </form>

            <!-- ── TAB: Security (read-only, outside form) ── -->
            <div class="up6-tab-panel<?php echo 'security' === $active_tab ? ' is-active' : ''; ?>" id="tab-security" role="tabpanel">
                <?php up6_scanner_render_security_tab(); ?>
            </div>

        </div><!-- .up6-options-body -->

    </div><!-- .up6-options-wrap -->
    <?php
}
