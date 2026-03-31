<?php
/**
 * Theme Security Scanner
 *
 * Prevents activation of themes containing malicious code patterns.
 * Provides: activation interception, upload-time scanning, background
 * visual flagging, scan history log, email alerts, checksum integrity
 * verification for UP6's own files, and a Security tab in Theme Options.
 *
 * @package UP6
 * @since   2.7.1
 */

defined( 'ABSPATH' ) || exit;

/* =============================================================
   CONFIGURATION
   ============================================================= */

define( 'UP6_SCANNER_PATTERNS', array(

    // Shell / command execution
    '/\b(shell_exec|passthru|pcntl_exec|proc_open|popen)\s*\(/i'
        => 'Shell execution function',
    '/\bsystem\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'System call with user input',
    '/\bexec\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'Exec call with user input',

    // Eval with dynamic / user-supplied input
    '/\beval\s*\(\s*(\$_|base64_decode|gzinflate|gzuncompress|str_rot13)/i'
        => 'Eval with obfuscated or user input',
    '/\bassert\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'Assert with user input',
    '/\bcreate_function\s*\(\s*[\'"].*[\'"]\s*,\s*(\$_(GET|POST|REQUEST|COOKIE)|base64_decode|gzinflate)/i'
        => 'create_function with dynamic or obfuscated code body',

    // Obfuscation / encoding chains
    '/base64_decode\s*\(\s*(base64_decode|\$_(GET|POST|REQUEST|COOKIE)|gzinflate)/i'
        => 'Nested base64 / obfuscation chain',
    '/\bgzinflate\s*\(\s*base64_decode/i'
        => 'gzinflate + base64 obfuscation',
    '/\bstr_rot13\s*\(\s*base64_decode/i'
        => 'str_rot13 + base64 obfuscation',

    // Deliberate security bypass
    '/ini_set\s*\(\s*[\'"]open_basedir[\'"]\s*,\s*NULL\s*\)/i'
        => 'open_basedir bypass attempt',
    '/ini_set\s*\(\s*[\'"]disable_functions[\'"]\s*,\s*[\'"][\'\"]\s*\)/i'
        => 'disable_functions bypass attempt',
    '/ini_set\s*\(\s*[\'"]suhosin\.executor/i'
        => 'Suhosin bypass attempt',

    // Web shell indicators
    '/\$_(GET|POST|REQUEST|COOKIE)\s*\[\s*[\'"]?(cmd|command|exec|run|shell|eval)[\'"]?\s*\]/i'
        => 'Web shell command parameter',
    '/@error_reporting\s*\(\s*0\s*\).*@ini_set\s*\(\s*[\'"]display_errors/is'
        => 'Error suppression pattern (web shell signature)',

    // File operation with user input
    '/\bmove_uploaded_file\s*\(.*\$_(GET|POST|REQUEST)/i'
        => 'Arbitrary file upload via user input',
    '/file_put_contents\s*\(\s*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'Arbitrary file write via user input',

    // Backdoor function-mapping
    '/\$\w+\s*=\s*array\s*\(\s*[\'"]exec[\'"]\s*=>\s*\[/i'
        => 'Function-alternative mapping (backdoor signature)',

    // Network-based backdoor
    '/\bfsockopen\s*\(.*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'Outbound socket with user input',
    '/\bcurl_exec\s*\(.*\$_(GET|POST|REQUEST|COOKIE)/i'
        => 'cURL execution with user input',
) );

define( 'UP6_SCANNER_THRESHOLD', 1 );
define( 'UP6_SCANNER_LOG_TO_PHP', true );
define( 'UP6_SCANNER_LOG_MAX', 20 );


/* =============================================================
   SCAN LOG — persistent history stored in wp_options
   ============================================================= */

function up6_scanner_log_append( array $entry ) {
    $log   = get_option( 'up6_scanner_log', [] );
    $log[] = $entry;
    if ( count( $log ) > UP6_SCANNER_LOG_MAX ) {
        $log = array_slice( $log, -UP6_SCANNER_LOG_MAX );
    }
    update_option( 'up6_scanner_log', $log, false );
}

/**
 * Retrieve the scan history log (most recent first).
 *
 * @return array  Array of log entries, each with 'time', 'theme', 'action', 'hits', 'user'.
 */
function up6_scanner_log_get() {
    return array_reverse( get_option( 'up6_scanner_log', [] ) );
}


/* =============================================================
   EMAIL NOTIFICATION
   ============================================================= */

function up6_scanner_send_alert( $theme_name, $action, $hits ) {
    $admin_email = get_option( 'admin_email' );
    if ( ! $admin_email ) return;

    $site_name = get_bloginfo( 'name' );
    $user      = wp_get_current_user();
    $username  = $user->exists() ? $user->user_login : 'unknown';

    $subject = sprintf( '[%s] Theme %s blocked by security scanner', $site_name, $action );

    $lines   = [];
    $lines[] = sprintf( 'The UP6 security scanner blocked a theme %s.', $action );
    $lines[] = '';
    $lines[] = sprintf( 'Theme: %s', $theme_name );
    $lines[] = sprintf( 'Action: %s', $action );
    $lines[] = sprintf( 'User: %s', $username );
    $lines[] = sprintf( 'Time: %s', current_time( 'mysql' ) );
    $lines[] = sprintf( 'Patterns matched: %d', count( $hits ) );
    $lines[] = '';
    $lines[] = 'Details:';
    foreach ( $hits as $hit ) {
        $lines[] = sprintf( '  - %s (line ~%d): %s', $hit['file'], $hit['line'], $hit['label'] );
    }
    $lines[] = '';
    $lines[] = 'Review installed themes: ' . admin_url( 'themes.php' );
    $lines[] = 'Security tab: ' . admin_url( 'themes.php?page=up6-theme-options&tab=security' );

    wp_mail( $admin_email, $subject, implode( "\n", $lines ) );
}


/* =============================================================
   CHECKSUM INTEGRITY — UP6 file manifest
   ============================================================= */

function up6_scanner_generate_checksums() {
    $root     = get_stylesheet_directory();
    $files    = up6_scanner_get_php_files( $root );
    $manifest = [];

    foreach ( $files as $file ) {
        $relative = str_replace( $root . '/', '', $file );
        $hash     = hash_file( 'sha256', $file );
        if ( $hash ) $manifest[ $relative ] = $hash;
    }
    ksort( $manifest );

    update_option( 'up6_scanner_checksum', [
        'generated' => current_time( 'mysql' ),
        'version'   => wp_get_theme()->get( 'Version' ),
        'files'     => $manifest,
    ], false );

    return $manifest;
}

/**
 * Verify UP6 PHP file integrity against the stored SHA-256 baseline.
 *
 * Compares current file hashes against the baseline generated on theme
 * activation. Reports modified, added, and removed files.
 *
 * @return array  Keys: 'status' (ok|no_baseline|mismatch), 'modified', 'added', 'removed', 'generated', 'version'.
 */
function up6_scanner_verify_checksums() {
    $stored = get_option( 'up6_scanner_checksum', null );
    if ( ! $stored || empty( $stored['files'] ) ) {
        return [ 'status' => 'no_baseline', 'modified' => [], 'added' => [], 'removed' => [], 'generated' => '', 'version' => '' ];
    }

    $root    = get_stylesheet_directory();
    $current = [];
    foreach ( up6_scanner_get_php_files( $root ) as $file ) {
        $current[ str_replace( $root . '/', '', $file ) ] = hash_file( 'sha256', $file );
    }

    $baseline = $stored['files'];
    $modified = $added = $removed = [];

    foreach ( $baseline as $rel => $hash ) {
        if ( ! isset( $current[ $rel ] ) )        $removed[]  = $rel;
        elseif ( $current[ $rel ] !== $hash )      $modified[] = $rel;
    }
    foreach ( $current as $rel => $hash ) {
        if ( ! isset( $baseline[ $rel ] ) )        $added[] = $rel;
    }

    return [
        'status'    => ( empty( $modified ) && empty( $added ) && empty( $removed ) ) ? 'ok' : 'modified',
        'modified'  => $modified,
        'added'     => $added,
        'removed'   => $removed,
        'generated' => $stored['generated'] ?? '',
        'version'   => $stored['version'] ?? '',
    ];
}

// Generate checksums on theme activation.
add_action( 'after_switch_theme', 'up6_scanner_generate_checksums' );

// Generate checksums once per version.
add_action( 'admin_init', function () {
    $key = 'up6_checksum_' . wp_get_theme()->get( 'Version' );
    if ( ! get_option( $key ) ) {
        up6_scanner_generate_checksums();
        update_option( $key, 1, true );
    }
}, 25 );


/* =============================================================
   SCAN ENGINE
   ============================================================= */

function up6_scanner_scan_theme( WP_Theme $theme ) {
    $results = [ 'is_blocked' => false, 'hits' => [], 'files_scanned' => 0 ];
    $root = $theme->get_stylesheet_directory();
    if ( ! is_dir( $root ) ) return $results;

    foreach ( up6_scanner_get_php_files( $root ) as $file ) {
        $results['files_scanned']++;
        $contents = @file_get_contents( $file );
        if ( false === $contents ) continue;
        $lines = explode( "\n", $contents );

        foreach ( UP6_SCANNER_PATTERNS as $pattern => $label ) {
            if ( ! preg_match( $pattern, $contents ) ) continue;
            $matched_line = 0;
            foreach ( $lines as $num => $lc ) {
                if ( preg_match( $pattern, $lc ) ) { $matched_line = $num + 1; break; }
            }
            $results['hits'][] = [ 'file' => str_replace( $root . '/', '', $file ), 'line' => $matched_line, 'label' => $label ];
        }
    }

    if ( count( $results['hits'] ) >= UP6_SCANNER_THRESHOLD ) $results['is_blocked'] = true;
    return $results;
}

/**
 * Recursively discover all .php files under a directory.
 *
 * @param  string   $dir  Absolute path to scan.
 * @return string[]       Array of absolute file paths.
 */
function up6_scanner_get_php_files( $dir ) {
    $files = [];
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
    } catch ( Exception $e ) { return $files; }
    foreach ( $it as $f ) {
        if ( $f->isFile() && 'php' === strtolower( $f->getExtension() ) ) $files[] = $f->getPathname();
    }
    return $files;
}


/* =============================================================
   INTERCEPT THEME ACTIVATION
   ============================================================= */
add_action( 'admin_init', 'up6_scanner_intercept_activation', 1 );

/**
 * Block theme activation if malicious code patterns are detected.
 *
 * Fires on switch_theme. Scans the target theme's PHP files against
 * 18 backdoor/shell/obfuscation patterns. If blocked: reverts to the
 * previous theme, logs the event, sends an email alert to the admin,
 * and sets a transient for the admin notice.
 */
function up6_scanner_intercept_activation() {
    if ( ! isset( $_GET['action'] ) || 'activate' !== $_GET['action'] ) return;
    global $pagenow;
    if ( 'themes.php' !== $pagenow ) return;

    $target = isset( $_GET['stylesheet'] ) ? sanitize_text_field( wp_unslash( $_GET['stylesheet'] ) ) : '';
    if ( empty( $target ) || $target === get_stylesheet() ) return;

    $theme = wp_get_theme( $target );
    if ( ! $theme->exists() ) return;

    $results = up6_scanner_scan_theme( $theme );
    if ( ! $results['is_blocked'] ) return;

    $user = wp_get_current_user();

    set_transient( 'up6_scanner_blocked', [
        'theme' => $theme->get( 'Name' ), 'hits' => $results['hits'],
        'scanned' => $results['files_scanned'], 'time' => current_time( 'mysql' ),
    ], 300 );

    up6_scanner_log_append( [
        'theme' => $theme->get( 'Name' ), 'action' => 'activation_blocked',
        'hits' => $results['hits'], 'scanned' => $results['files_scanned'],
        'user' => $user->exists() ? $user->user_login : 'unknown',
        'time' => current_time( 'mysql' ),
    ] );

    up6_scanner_send_alert( $theme->get( 'Name' ), 'activation', $results['hits'] );

    if ( UP6_SCANNER_LOG_TO_PHP ) {
        error_log( sprintf( '[UP6 Security Scanner] BLOCKED activation of "%s" — %d pattern(s) in %d file(s).',
            $theme->get( 'Name' ), count( $results['hits'] ), $results['files_scanned'] ) );
        foreach ( $results['hits'] as $hit ) {
            error_log( sprintf( '  → %s (line ~%d): %s', $hit['file'], $hit['line'], $hit['label'] ) );
        }
    }

    wp_safe_redirect( admin_url( 'themes.php?up6_blocked=1' ) );
    exit;
}


/* =============================================================
   ADMIN NOTICE — blocked activation
   ============================================================= */
add_action( 'admin_notices', 'up6_scanner_blocked_notice' );

/**
 * Display an admin notice when a theme activation was blocked.
 *
 * Reads from a transient set by up6_scanner_intercept_activation().
 * Shows the theme name, number of suspicious files, and matched patterns.
 */
function up6_scanner_blocked_notice() {
    if ( ! isset( $_GET['up6_blocked'] ) ) return;
    $data = get_transient( 'up6_scanner_blocked' );
    if ( ! $data ) return;
    delete_transient( 'up6_scanner_blocked' );
    $count = count( $data['hits'] );

    printf(
        '<div class="notice notice-error is-dismissible"><p><strong>⛔ %s</strong></p><p>%s</p>'
        . '<details style="margin:6px 0 10px"><summary style="cursor:pointer;font-weight:600">%s</summary>'
        . '<table class="widefat striped" style="margin-top:8px"><thead><tr><th>%s</th><th>%s</th><th>%s</th></tr></thead><tbody>',
        esc_html__( 'Theme Activation Blocked', 'up6' ),
        sprintf( esc_html__( 'The theme "%1$s" was prevented from activating — %2$d malicious pattern(s) across %3$d file(s).', 'up6' ),
            '<strong>' . esc_html( $data['theme'] ) . '</strong>', $count, $data['scanned'] ),
        esc_html__( 'View details', 'up6' ),
        esc_html__( 'File', 'up6' ), esc_html__( 'Line', 'up6' ), esc_html__( 'Detection', 'up6' )
    );
    foreach ( $data['hits'] as $hit ) {
        printf( '<tr><td><code>%s</code></td><td>%d</td><td>%s</td></tr>',
            esc_html( $hit['file'] ), intval( $hit['line'] ), esc_html( $hit['label'] ) );
    }
    echo '</tbody></table></details></div>';
}


/* =============================================================
   BACKGROUND SCAN — flag dangerous themes on Themes page
   ============================================================= */
add_action( 'admin_init', 'up6_scanner_background_scan' );

/**
 * Background scan for visual flagging on the Themes page.
 *
 * Scans all installed themes (cached via transient, 10-min TTL)
 * and marks flagged themes in the transient for the flag UI to consume.
 */
function up6_scanner_background_scan() {
    global $pagenow;
    if ( 'themes.php' !== $pagenow ) return;
    if ( false !== get_transient( 'up6_scanner_flagged' ) ) return;

    $flagged = [];
    foreach ( wp_get_themes() as $ss => $theme ) {
        if ( $ss === get_stylesheet() ) continue;
        $scan = up6_scanner_scan_theme( $theme );
        if ( $scan['is_blocked'] ) $flagged[ $ss ] = [ 'name' => $theme->get( 'Name' ), 'hits' => count( $scan['hits'] ) ];
    }
    set_transient( 'up6_scanner_flagged', $flagged, 600 );
}


/* =============================================================
   VISUAL FLAGGING — red overlay + disabled Activate button
   ============================================================= */
add_action( 'admin_footer-themes.php', 'up6_scanner_flag_ui' );

/**
 * Inject CSS/JS to visually flag malicious themes on the Themes page.
 *
 * Adds a red overlay and disables the Activate button for flagged themes.
 * Only runs on themes.php admin page.
 */
function up6_scanner_flag_ui() {
    $flagged = get_transient( 'up6_scanner_flagged' );
    if ( empty( $flagged ) || ! is_array( $flagged ) ) return;
    ?>
    <style>.up6-flagged .theme-screenshot{position:relative}.up6-flagged .theme-screenshot::after{content:'⛔ MALICIOUS CODE DETECTED';position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(192,57,43,.88);color:#fff;font-weight:700;font-size:13px;letter-spacing:.5px;text-transform:uppercase;pointer-events:none}.up6-flagged .theme-actions .activate{display:none!important}</style>
    <script>(function(){var f=<?php echo wp_json_encode($flagged); ?>;if(!f||typeof f!=='object')return;document.querySelectorAll('.theme').forEach(function(el){var s=el.getAttribute('data-slug');if(s&&f.hasOwnProperty(s))el.classList.add('up6-flagged');});document.addEventListener('click',function(e){var btn=e.target.closest('.theme-actions .activate');if(!btn)return;var t=btn.closest('.theme'),s=t?t.getAttribute('data-slug'):null;if(s&&f.hasOwnProperty(s)){e.preventDefault();e.stopImmediatePropagation();alert('This theme has been flagged as malicious and cannot be activated.');}},true);})();</script>
    <?php
}


/* =============================================================
   SCAN ON THEME UPLOAD / UPDATE
   ============================================================= */
add_filter( 'upgrader_post_install', 'up6_scanner_post_upload', 10, 3 );

/**
 * Scan newly uploaded themes and delete them if malicious patterns are found.
 *
 * Fires on the upgrader_post_install filter. If the uploaded theme contains
 * suspicious code, it is deleted immediately and the admin is notified.
 *
 * @param  bool  $response    Installation response.
 * @param  array $hook_extra  Extra args from the upgrader.
 * @param  array $result      Installation result with destination path.
 * @return bool               Original $response (or triggers deletion).
 */
function up6_scanner_post_upload( $response, $hook_extra, $result ) {
    if ( ! isset( $hook_extra['type'] ) || 'theme' !== $hook_extra['type'] ) return $response;
    if ( ! isset( $result['destination'] ) || ! is_dir( $result['destination'] ) ) return $response;

    $stylesheet = basename( $result['destination'] );
    $theme      = wp_get_theme( $stylesheet );
    if ( ! $theme->exists() ) return $response;

    $scan = up6_scanner_scan_theme( $theme );
    if ( ! $scan['is_blocked'] ) return $response;

    $user = wp_get_current_user();
    up6_scanner_log_append( [
        'theme' => $theme->get( 'Name' ), 'action' => 'upload_blocked',
        'hits' => $scan['hits'], 'scanned' => $scan['files_scanned'],
        'user' => $user->exists() ? $user->user_login : 'unknown',
        'time' => current_time( 'mysql' ),
    ] );
    up6_scanner_send_alert( $theme->get( 'Name' ), 'upload', $scan['hits'] );

    if ( UP6_SCANNER_LOG_TO_PHP ) {
        error_log( sprintf( '[UP6 Security Scanner] Malicious theme uploaded and deleted: "%s" — %d pattern(s).',
            $theme->get( 'Name' ), count( $scan['hits'] ) ) );
    }
    delete_theme( $stylesheet );

    return new WP_Error( 'up6_scanner_blocked', sprintf(
        'Theme "%1$s" was deleted — the UP6 security scanner detected %2$d malicious code pattern(s).',
        $theme->get( 'Name' ), count( $scan['hits'] ) ) );
}


/* =============================================================
   CLEAR SCAN CACHE
   ============================================================= */
add_action( 'upgrader_process_complete', 'up6_scanner_clear_cache', 10, 0 );
add_action( 'deleted_theme',            'up6_scanner_clear_cache', 10, 0 );
add_action( 'switch_theme',             'up6_scanner_clear_cache', 10, 0 );
/**
 * Clear the scan result transient. Called when themes are switched, installed, or deleted.
 */
function up6_scanner_clear_cache() { delete_transient( 'up6_scanner_flagged' ); }


/* =============================================================
   AJAX — Manual "Scan Now"
   ============================================================= */
add_action( 'wp_ajax_up6_scanner_scan_now', 'up6_scanner_ajax_scan_now' );

/**
 * AJAX handler for the "Scan Now" button on the Security tab.
 *
 * Scans all installed themes against the pattern library, verifies
 * UP6 file integrity, and returns a JSON response with results
 * for each theme plus the integrity check outcome.
 */
function up6_scanner_ajax_scan_now() {
    check_ajax_referer( 'up6_scanner_scan_now', '_nonce' );
    if ( ! current_user_can( 'edit_theme_options' ) ) wp_send_json_error( [ 'message' => 'Permission denied.' ] );

    delete_transient( 'up6_scanner_flagged' );

    $own = get_stylesheet();
    $total = $clean = 0;
    $flagged = [];

    foreach ( wp_get_themes() as $ss => $theme ) {
        if ( $ss === $own ) continue;
        $total++;
        $scan = up6_scanner_scan_theme( $theme );
        if ( $scan['is_blocked'] ) {
            $flagged[ $ss ] = [ 'name' => $theme->get( 'Name' ), 'hits' => count( $scan['hits'] ), 'details' => $scan['hits'] ];
        } else {
            $clean++;
        }
    }

    // Cache for visual overlay.
    $simple = [];
    foreach ( $flagged as $ss => $d ) $simple[ $ss ] = [ 'name' => $d['name'], 'hits' => $d['hits'] ];
    set_transient( 'up6_scanner_flagged', $simple, 600 );

    // Log flagged.
    if ( ! empty( $flagged ) ) {
        $user = wp_get_current_user();
        foreach ( $flagged as $d ) {
            up6_scanner_log_append( [
                'theme' => $d['name'], 'action' => 'manual_scan', 'hits' => $d['details'],
                'scanned' => 0, 'user' => $user->exists() ? $user->user_login : 'unknown',
                'time' => current_time( 'mysql' ),
            ] );
        }
    }

    $integrity = up6_scanner_verify_checksums();

    wp_send_json_success( [
        'total' => $total, 'clean' => $clean, 'flagged' => count( $flagged ),
        'details' => $flagged, 'integrity' => $integrity, 'time' => current_time( 'mysql' ),
    ] );
}


/* =============================================================
   AJAX — Regenerate checksum baseline
   ============================================================= */
add_action( 'wp_ajax_up6_scanner_regen_checksums', 'up6_scanner_ajax_regen_checksums' );

/**
 * AJAX handler for the "Regenerate Baseline" button on the Security tab.
 *
 * Regenerates the SHA-256 checksum baseline for all UP6 PHP files.
 * Returns the new baseline timestamp and file count as JSON.
 */
function up6_scanner_ajax_regen_checksums() {
    check_ajax_referer( 'up6_scanner_regen_checksums', '_nonce' );
    if ( ! current_user_can( 'edit_theme_options' ) ) wp_send_json_error( [ 'message' => 'Permission denied.' ] );

    $manifest = up6_scanner_generate_checksums();
    wp_send_json_success( [
        'files' => count( $manifest ), 'time' => current_time( 'mysql' ),
        'version' => wp_get_theme()->get( 'Version' ),
    ] );
}


/* =============================================================
   SECURITY TAB — render function (called from theme-options.php)
   ============================================================= */
function up6_scanner_render_security_tab() {

    $all_themes  = wp_get_themes();
    $theme_count = count( $all_themes ) - 1;
    $flagged     = get_transient( 'up6_scanner_flagged' );
    $flagged     = is_array( $flagged ) ? $flagged : [];
    $integrity   = up6_scanner_verify_checksums();
    $log         = up6_scanner_log_get();

    ?>
    <!-- Status cards -->
    <div class="up6-security-cards">
        <div class="up6-sec-card">
            <div class="up6-sec-card-icon" style="color:#27ae60;">🛡</div>
            <div class="up6-sec-card-body">
                <span class="up6-sec-card-value"><?php esc_html_e( 'Active', 'up6' ); ?></span>
                <span class="up6-sec-card-label"><?php esc_html_e( 'Scanner Status', 'up6' ); ?></span>
            </div>
        </div>
        <div class="up6-sec-card">
            <div class="up6-sec-card-icon">📦</div>
            <div class="up6-sec-card-body">
                <span class="up6-sec-card-value"><?php echo intval( $theme_count ); ?></span>
                <span class="up6-sec-card-label"><?php esc_html_e( 'Installed Themes', 'up6' ); ?></span>
            </div>
        </div>
        <div class="up6-sec-card">
            <div class="up6-sec-card-icon" style="color:<?php echo empty( $flagged ) ? '#27ae60' : '#C0392B'; ?>;"><?php echo empty( $flagged ) ? '✓' : '⛔'; ?></div>
            <div class="up6-sec-card-body">
                <span class="up6-sec-card-value"><?php echo count( $flagged ); ?></span>
                <span class="up6-sec-card-label"><?php esc_html_e( 'Flagged Themes', 'up6' ); ?></span>
            </div>
        </div>
        <div class="up6-sec-card">
            <div class="up6-sec-card-icon"><?php echo count( UP6_SCANNER_PATTERNS ); ?></div>
            <div class="up6-sec-card-body">
                <span class="up6-sec-card-value"><?php esc_html_e( 'Patterns', 'up6' ); ?></span>
                <span class="up6-sec-card-label"><?php esc_html_e( 'Detection Rules', 'up6' ); ?></span>
            </div>
        </div>
    </div>

    <!-- Scan Now -->
    <div class="up6-sec-section">
        <h3><?php esc_html_e( 'Manual Scan', 'up6' ); ?></h3>
        <p class="up6-field-hint" style="margin-top:0;"><?php esc_html_e( 'Scan all installed themes for malicious code and verify UP6 file integrity.', 'up6' ); ?></p>
        <button type="button" class="button button-primary" id="up6-scan-now"><?php esc_html_e( 'Scan Now', 'up6' ); ?></button>
        <span id="up6-scan-spinner" class="spinner" style="float:none;margin-top:0;"></span>
        <div id="up6-scan-results" style="display:none;margin-top:1rem;"></div>
    </div>

    <!-- Checksum Integrity -->
    <div class="up6-sec-section">
        <h3><?php esc_html_e( 'UP6 File Integrity', 'up6' ); ?></h3>
        <?php if ( 'no_baseline' === $integrity['status'] ) : ?>
            <p class="up6-field-hint" style="margin-top:0;"><?php esc_html_e( 'No baseline checksum exists yet. Click below to generate one.', 'up6' ); ?></p>
        <?php else : ?>
            <div class="up6-sec-integrity up6-sec-integrity-<?php echo esc_attr( $integrity['status'] ); ?>">
                <?php if ( 'ok' === $integrity['status'] ) : ?>
                    <strong>✓ <?php esc_html_e( 'All files match baseline', 'up6' ); ?></strong>
                <?php else : ?>
                    <strong>⚠ <?php esc_html_e( 'Changes detected since baseline', 'up6' ); ?></strong>
                    <?php if ( ! empty( $integrity['modified'] ) ) : ?>
                        <div style="margin-top:.5rem;"><em><?php esc_html_e( 'Modified:', 'up6' ); ?></em>
                        <?php foreach ( $integrity['modified'] as $f ) : ?><code style="display:inline-block;margin:2px 4px;"><?php echo esc_html( $f ); ?></code><?php endforeach; ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $integrity['added'] ) ) : ?>
                        <div style="margin-top:.25rem;"><em><?php esc_html_e( 'New files:', 'up6' ); ?></em>
                        <?php foreach ( $integrity['added'] as $f ) : ?><code style="display:inline-block;margin:2px 4px;"><?php echo esc_html( $f ); ?></code><?php endforeach; ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $integrity['removed'] ) ) : ?>
                        <div style="margin-top:.25rem;"><em><?php esc_html_e( 'Missing:', 'up6' ); ?></em>
                        <?php foreach ( $integrity['removed'] as $f ) : ?><code style="display:inline-block;margin:2px 4px;"><?php echo esc_html( $f ); ?></code><?php endforeach; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <p class="up6-field-hint" style="margin-top:.5rem;"><?php printf( esc_html__( 'Baseline: %1$s (v%2$s)', 'up6' ), esc_html( $integrity['generated'] ), esc_html( $integrity['version'] ) ); ?></p>
            </div>
        <?php endif; ?>
        <div style="margin-top:.75rem;">
            <button type="button" class="button" id="up6-regen-checksums"><?php esc_html_e( 'Regenerate Baseline', 'up6' ); ?></button>
            <span id="up6-regen-spinner" class="spinner" style="float:none;margin-top:0;"></span>
            <span id="up6-regen-result" style="margin-left:.5rem;"></span>
        </div>
        <p class="up6-field-hint"><?php esc_html_e( 'Regenerate after intentional theme updates. Creates a new SHA-256 hash baseline of all UP6 PHP files.', 'up6' ); ?></p>
    </div>

    <!-- Scan Log -->
    <div class="up6-sec-section">
        <h3><?php esc_html_e( 'Scan Log', 'up6' ); ?></h3>
        <?php if ( empty( $log ) ) : ?>
            <p class="up6-field-hint" style="margin-top:0;"><?php esc_html_e( 'No events recorded yet. Blocked activations, uploads, and manual scans will appear here.', 'up6' ); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:100%;">
                <thead><tr>
                    <th style="width:11rem;"><?php esc_html_e( 'Time', 'up6' ); ?></th>
                    <th><?php esc_html_e( 'Theme', 'up6' ); ?></th>
                    <th style="width:10rem;"><?php esc_html_e( 'Action', 'up6' ); ?></th>
                    <th style="width:4rem;"><?php esc_html_e( 'Hits', 'up6' ); ?></th>
                    <th style="width:7rem;"><?php esc_html_e( 'User', 'up6' ); ?></th>
                </tr></thead>
                <tbody>
                <?php foreach ( $log as $entry ) :
                    $badges = [
                        'activation_blocked' => [ '🚫', __( 'Activation blocked', 'up6' ), '#C0392B' ],
                        'upload_blocked'     => [ '🗑', __( 'Upload deleted', 'up6' ), '#C0392B' ],
                        'manual_scan'        => [ '🔍', __( 'Manual scan', 'up6' ), '#2E5871' ],
                    ];
                    $b = $badges[ $entry['action'] ?? '' ] ?? [ '—', $entry['action'] ?? '—', '#666' ];
                ?>
                <tr>
                    <td style="font-size:.8rem;color:#666;"><?php echo esc_html( $entry['time'] ?? '—' ); ?></td>
                    <td><strong><?php echo esc_html( $entry['theme'] ?? '—' ); ?></strong></td>
                    <td><span style="font-size:.75rem;font-weight:600;color:<?php echo esc_attr( $b[2] ); ?>;"><?php echo $b[0] . ' ' . esc_html( $b[1] ); ?></span></td>
                    <td style="text-align:center;font-weight:700;"><?php echo intval( count( $entry['hits'] ?? [] ) ); ?></td>
                    <td style="font-size:.8rem;color:#666;"><?php echo esc_html( $entry['user'] ?? '—' ); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p class="up6-field-hint"><?php printf( esc_html__( 'Showing last %d events.', 'up6' ), UP6_SCANNER_LOG_MAX ); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
