<?php
/**
 * UP6 Admin Helpers
 *
 * Subtitle/dek meta box, Pin to Homepage meta box,
 * site editor redirect, page template selector meta box,
 * metabox CSS enqueue.
 *
 * Extracted from functions.php for maintainability.
 *
 * @package UP6
 * @since   2.8
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   SECONDARY TITLE (SUBTITLE / DEK)
   Stores a subtitle in _up6_subtitle post meta.
   Displayed below the headline on single posts.
   @since 2.6.14
   ============================================================= */

// Meta box in the editor sidebar
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'up6_subtitle_box',
        __( 'Subtitle', 'up6' ),
        'up6_subtitle_meta_box',
        'post',
        'side',
        'high'
    );
} );

/**
 * Render the Subtitle/Dek meta box content.
 *
 * Outputs a nonce, a text input for _up6_subtitle post meta, and a
 * help description. Shown in the editor sidebar on all posts.
 */
function up6_subtitle_meta_box( $post ) {
    wp_nonce_field( 'up6_subtitle_nonce', 'up6_subtitle_nonce' );
    $subtitle = get_post_meta( $post->ID, '_up6_subtitle', true );
    ?>
    <input type="text" name="up6_subtitle" id="up6_subtitle"
           value="<?php echo esc_attr( $subtitle ); ?>"
           placeholder="<?php esc_attr_e( 'Enter subtitle / dek line…', 'up6' ); ?>"
           style="width:100%;padding:6px 8px;font-size:13px;" />
    <p class="description" style="margin-top:6px;">
      <?php esc_html_e( 'Short explanatory line shown below the headline. Leave blank to omit.', 'up6' ); ?>
    </p>
    <?php
}

// Save handler
add_action( 'save_post_post', function ( $post_id ) {
    if ( ! isset( $_POST['up6_subtitle_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['up6_subtitle_nonce'], 'up6_subtitle_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $subtitle = isset( $_POST['up6_subtitle'] ) ? sanitize_text_field( $_POST['up6_subtitle'] ) : '';
    update_post_meta( $post_id, '_up6_subtitle', $subtitle );
} );

/**
 * Returns the subtitle for a post, or empty string if not set.
 *
 * @param  int|null $post_id  Post ID (defaults to current post).
 * @return string
 */
function up6_get_subtitle( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    return (string) get_post_meta( $post_id, '_up6_subtitle', true );
}

/* =============================================================
   PIN TO HOMEPAGE — sticky post management
   Adds a visible "Pin to Homepage" checkbox meta box in the
   editor sidebar. Uses native WP stick_post() / unstick_post()
   so the hero section and all sticky queries work automatically.
   Also adds a 📌 column in the admin posts list.
   @since 2.6.81
   ============================================================= */

// Meta box in the editor sidebar
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'up6_pin_homepage_box',
        __( 'Pin to Homepage', 'up6' ),
        'up6_pin_homepage_meta_box',
        'post',
        'side',
        'high'
    );
} );

/**
 * Render the Pin to Homepage meta box content.
 *
 * Checkbox that wraps native WordPress stick_post()/unstick_post().
 * When checked, the post becomes the hero card on the homepage.
 * Shows a red "Sedang disemat" indicator when currently pinned.
 */
function up6_pin_homepage_meta_box( $post ) {
    wp_nonce_field( 'up6_pin_homepage_nonce', 'up6_pin_homepage_nonce' );
    $is_sticky = is_sticky( $post->ID );
    ?>
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="up6_pin_homepage" value="1" <?php checked( $is_sticky ); ?> />
        <span style="font-size:13px;">
            <?php esc_html_e( 'Pin this post to the homepage hero', 'up6' ); ?>
        </span>
    </label>
    <p class="description" style="margin-top:8px;">
        <?php esc_html_e( 'Pinned posts appear as the hero card at the top of the homepage. If multiple posts are pinned, the most recent one is shown.', 'up6' ); ?>
    </p>
    <?php if ( $is_sticky ) : ?>
    <p style="margin-top:6px;padding:4px 8px;background:rgba(192,57,43,.08);border-radius:4px;font-size:12px;color:#C0392B;font-weight:600;">
        📌 <?php esc_html_e( 'Currently pinned', 'up6' ); ?>
    </p>
    <?php endif; ?>
    <?php
}

// Save handler — uses native WP sticky functions
add_action( 'save_post_post', function ( $post_id ) {
    if ( ! isset( $_POST['up6_pin_homepage_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['up6_pin_homepage_nonce'], 'up6_pin_homepage_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( ! empty( $_POST['up6_pin_homepage'] ) ) {
        stick_post( $post_id );
    } else {
        unstick_post( $post_id );
    }
}, 20 );

// Admin posts list: add 📌 column
add_filter( 'manage_posts_columns', function ( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'cb' ) {
            $new['up6_pinned'] = '<span title="' . esc_attr__( 'Pinned to Homepage', 'up6' ) . '">📌</span>';
        }
    }
    return $new;
} );

add_action( 'manage_posts_custom_column', function ( $column, $post_id ) {
    if ( $column === 'up6_pinned' && is_sticky( $post_id ) ) {
        echo '<span style="color:#C0392B;font-size:16px;" title="' . esc_attr__( 'Pinned to Homepage', 'up6' ) . '">📌</span>';
    }
}, 10, 2 );

// Narrow the pin column
add_action( 'admin_head', function () {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'edit-post' ) {
        echo '<style>.column-up6_pinned { width: 2.5em; text-align: center; }</style>';
    }
} );
/* =============================================================
   SITE EDITOR — disable & redirect
   UP6 uses classic PHP templates (single.php, index.php, etc.)
   and has no block templates/ directory. The FSE site editor is
   inherited from the TT25 parent but is not applicable here.
   We remove it from the menu and redirect direct URL access to
   the Customizer, with an admin notice explaining why.
   ============================================================= */

// Remove "Editor" (site editor) from Appearance menu
add_action( 'admin_menu', function () {
    remove_submenu_page( 'themes.php', 'site-editor.php' );
}, 999 );

// Redirect site-editor.php to the Customizer with a notice flag
add_action( 'current_screen', function ( $screen ) {
    if ( ! is_admin() ) return;
    // site-editor screen id is 'site-editor'
    if ( $screen->id !== 'site-editor' ) return;
    if ( ! current_user_can( 'edit_theme_options' ) ) return;

    wp_redirect( add_query_arg(
        [ 'up6_editor_notice' => '1' ],
        admin_url( 'customize.php' )
    ) );
    exit;
} );

// Admin notice on the Customizer explaining the redirect
add_action( 'admin_notices', function () {
    if ( empty( $_GET['up6_editor_notice'] ) ) return;
    if ( ! current_user_can( 'edit_theme_options' ) ) return;
    echo '<div class="notice notice-info is-dismissible">'
       . '<p><strong>' . esc_html__( 'Site Editor not available', 'up6' ) . '</strong> — '
       . esc_html__( 'UP6 uses classic PHP templates and does not use the block-based Site Editor. Use the Customizer (here) to edit site identity, colours, menus, and footer details. To edit page content, use the standard post/page editor.', 'up6' )
       . '</p></div>';
} );

/* =============================================================
   PAGE TEMPLATE REGISTRATION
   Force PHP page templates to appear in the block editor
   template dropdown. Required because TT25 is a block theme
   and the block editor otherwise surfaces only .html templates.
   ============================================================= */
add_filter( 'theme_page_templates', function ( $templates ) {
    $templates['template-faq.php']              = __( 'FAQ Page',          'up6' );
    $templates['template-about.php']            = __( 'About',            'up6' );
    $templates['template-meaning-of-6.php']     = __( 'The Meaning of 6',   'up6' );
    $templates['template-editorial-policy.php'] = __( 'Editorial Policy',   'up6' );
    $templates['template-privacy-policy.php']   = __( 'Privacy Policy',     'up6' );
    $templates['template-disclaimer.php']       = __( 'Disclaimer',          'up6' );
    $templates['template-contact.php']          = __( 'Contact',            'up6' );
    $templates['template-corrections.php']      = __( 'Corrections',        'up6' );
    $templates['template-advertise.php']       = __( 'Advertise',          'up6' );
    return $templates;
} );

/* =============================================================
   PHP TEMPLATE SELECTOR META BOX
   The block editor's "Change template" UI in a block theme only
   lists .html block templates — it ignores theme_page_templates.
   This meta box writes directly to _wp_page_template post meta,
   which is what WordPress uses at template_include time.
   Shows in both the classic editor and the block editor sidebar.
   ============================================================= */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style(
            'up6-metabox',
            get_stylesheet_directory_uri() . '/css/admin-options' . $min . '.css',
            [],
            wp_get_theme()->get( 'Version' )
        );
    }
} );

add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'up6_page_template',
        __( 'Page Template', 'up6' ),
        'up6_page_template_meta_box',
        'page',
        'side',
        'high'
    );
} );

/**
 * Render the Page Template selector meta box.
 *
 * Dropdown of all registered UP6 page templates. Writes to the native
 * _wp_page_template post meta so WordPress loads the correct template
 * at template_include time. Shown on Page edit screens only.
 */
function up6_page_template_meta_box( $post ) {
    $current  = get_post_meta( $post->ID, '_wp_page_template', true ) ?: 'default';
    $templates = [
        'default'                           => __( 'Default',          'up6' ),
        'template-faq.php'                  => __( 'FAQ',              'up6' ),
        'template-about.php'                => __( 'About',            'up6' ),
        'template-meaning-of-6.php'         => __( 'The Meaning of 6', 'up6' ),
        'template-editorial-policy.php'     => __( 'Editorial Policy', 'up6' ),
        'template-privacy-policy.php'       => __( 'Privacy Policy',   'up6' ),
        'template-disclaimer.php'           => __( 'Disclaimer',        'up6' ),
        'template-contact.php'              => __( 'Contact',           'up6' ),
        'template-corrections.php'          => __( 'Corrections',       'up6' ),
        'template-advertise.php'            => __( 'Advertise',         'up6' ),
    ];
    wp_nonce_field( 'up6_page_template_nonce', 'up6_page_template_nonce' );
    echo '<div class="up6-tmpl-list">';
    foreach ( $templates as $file => $label ) {
        $id      = 'up6_tmpl_' . sanitize_key( $file );
        $checked = checked( $current, $file, false );
        printf(
            '<label class="up6-tmpl-option%s" for="%s">'
            . '<input type="radio" id="%s" name="up6_page_template" value="%s"%s>'
            . '<span>%s</span>'
            . '</label>',
            ( $current === $file ? ' up6-tmpl-active' : '' ),
            esc_attr( $id ),
            esc_attr( $id ),
            esc_attr( $file ),
            $checked,
            esc_html( $label )
        );
    }
    echo '</div>';
}

add_action( 'save_post_page', function ( $post_id ) {
    if ( ! isset( $_POST['up6_page_template_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['up6_page_template_nonce'], 'up6_page_template_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $template = sanitize_text_field( $_POST['up6_page_template'] ?? 'default' );
    $allowed  = [ 'default', 'template-faq.php', 'template-about.php', 'template-meaning-of-6.php', 'template-editorial-policy.php', 'template-privacy-policy.php', 'template-disclaimer.php', 'template-contact.php', 'template-corrections.php', 'template-advertise.php' ];
    if ( in_array( $template, $allowed, true ) ) {
        update_post_meta( $post_id, '_wp_page_template', $template );
    }
} );
