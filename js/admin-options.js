/**
 * UP6 Theme Options — Admin JavaScript
 *
 * Loaded only on the Theme Options page (appearance_page_up6-theme-options).
 * Contains:
 *   1. Tab switching — click handler for .up6-tab buttons, panel show/hide,
 *      persists active tab via hidden input for post-save retention
 *   2. Save button visibility — hidden on Security tab (read-only, outside form)
 *   3. Hidden Tags search — live filter for tag checkbox list
 *   4. Security Scanner AJAX — "Scan Now" and "Regenerate Baseline" buttons,
 *      results rendering, status card updates, scan log display
 *
 * Localised data passed via wp_localize_script as `up6Scanner` object
 * containing AJAX URL, nonces, and translated UI strings.
 *
 * @package UP6
 * @since   2.3.0
 * @updated 2.8 — security scanner AJAX, save button toggle
 */
( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {
        var tabs   = document.querySelectorAll( '.up6-tab' );
        var panels = document.querySelectorAll( '.up6-tab-panel' );
        var hidden = document.getElementById( 'up6_active_tab' );

        if ( ! tabs.length ) {
            return;
        }

        tabs.forEach( function ( tab ) {
            tab.addEventListener( 'click', function () {
                var target = this.getAttribute( 'data-tab' );

                // Deactivate all
                tabs.forEach( function ( t ) {
                    t.classList.remove( 'is-active' );
                    t.setAttribute( 'aria-selected', 'false' );
                } );
                panels.forEach( function ( p ) {
                    p.classList.remove( 'is-active' );
                } );

                // Activate target
                this.classList.add( 'is-active' );
                this.setAttribute( 'aria-selected', 'true' );

                var panel = document.getElementById( 'tab-' + target );
                if ( panel ) {
                    panel.classList.add( 'is-active' );
                }

                // Hide save button on Security tab (read-only, outside form)
                var saveFooter = document.querySelector( '.up6-options-footer' );
                if ( saveFooter ) {
                    saveFooter.style.display = ( target === 'security' ) ? 'none' : '';
                }

                // Update hidden field so save preserves the active tab
                if ( hidden ) {
                    hidden.value = target;
                }
            } );
        } );

        // Hide save button if Security tab is active on initial load
        var saveFooter = document.querySelector( '.up6-options-footer' );
        if ( saveFooter && hidden && hidden.value === 'security' ) {
            saveFooter.style.display = 'none';
        }

        // ── Hidden Tags tab: live search filter ──────────────────────────
        var tagSearch = document.getElementById( 'up6_tag_search' );
        var tagList   = document.getElementById( 'up6_tag_list' );
        if ( tagSearch && tagList ) {
            tagSearch.addEventListener( 'input', function () {
                var q = this.value.toLowerCase();
                tagList.querySelectorAll( 'label' ).forEach( function ( label ) {
                    label.style.display = label.textContent.toLowerCase().includes( q ) ? '' : 'none';
                } );
            } );
        }

        // ── Security tab: Scan Now ──────────────────────────────────────
        var scanBtn     = document.getElementById( 'up6-scan-now' );
        var scanSpinner = document.getElementById( 'up6-scan-spinner' );
        var scanResults = document.getElementById( 'up6-scan-results' );

        if ( scanBtn && typeof up6Scanner !== 'undefined' ) {
            scanBtn.addEventListener( 'click', function () {
                scanBtn.disabled = true;
                scanBtn.textContent = up6Scanner.scanningText;
                scanSpinner.classList.add( 'is-active' );
                scanResults.style.display = 'none';

                var fd = new FormData();
                fd.append( 'action', 'up6_scanner_scan_now' );
                fd.append( '_nonce', up6Scanner.scanNonce );

                fetch( up6Scanner.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' } )
                    .then( function ( r ) { return r.json(); } )
                    .then( function ( res ) {
                        scanSpinner.classList.remove( 'is-active' );
                        scanBtn.disabled = false;
                        scanBtn.textContent = up6Scanner.scanNowText || 'Scan Now';

                        if ( ! res.success ) {
                            scanResults.innerHTML = '<div class="notice notice-error"><p>' + ( res.data && res.data.message || up6Scanner.errorText ) + '</p></div>';
                            scanResults.style.display = '';
                            return;
                        }

                        var d = res.data;
                        var html = '<div class="up6-scan-summary">';
                        html += '<strong>' + d.total + '</strong> ' + up6Scanner.themesScanned + ' — ';

                        if ( d.flagged === 0 ) {
                            html += '<span style="color:#27ae60;font-weight:700;">' + up6Scanner.allClean + ' ✓</span>';
                        } else {
                            html += '<span style="color:#C0392B;font-weight:700;">' + d.flagged + ' ' + up6Scanner.flaggedFound + ' ⛔</span>';
                        }

                        // Integrity
                        if ( d.integrity ) {
                            html += '<br>';
                            if ( d.integrity.status === 'ok' ) {
                                html += '<span style="color:#27ae60;">' + up6Scanner.integrityOk + '</span>';
                            } else if ( d.integrity.status === 'no_baseline' ) {
                                html += '<span style="color:#888;">' + up6Scanner.integrityNoBase + '</span>';
                            } else {
                                html += '<span style="color:#e67e22;font-weight:600;">UP6 files: ⚠ changes detected</span>';
                            }
                        }

                        html += '<br><span style="font-size:.8rem;color:#888;">' + up6Scanner.completed + ' ' + d.time + '</span>';
                        html += '</div>';

                        // Flagged details
                        if ( d.flagged > 0 && d.details ) {
                            html += '<div style="margin-top:.75rem;">';
                            for ( var ss in d.details ) {
                                var t = d.details[ ss ];
                                html += '<details style="margin-bottom:.5rem;"><summary style="cursor:pointer;font-weight:600;color:#C0392B;">⛔ ' + t.name + ' (' + t.hits + ' patterns)</summary>';
                                html += '<table class="widefat striped" style="margin-top:.5rem;"><thead><tr><th>File</th><th>Line</th><th>Detection</th></tr></thead><tbody>';
                                t.details.forEach( function ( h ) {
                                    html += '<tr><td><code>' + h.file + '</code></td><td>' + h.line + '</td><td>' + h.label + '</td></tr>';
                                } );
                                html += '</tbody></table></details>';
                            }
                            html += '</div>';
                        }

                        scanResults.innerHTML = html;
                        scanResults.style.display = '';
                    } )
                    .catch( function () {
                        scanSpinner.classList.remove( 'is-active' );
                        scanBtn.disabled = false;
                        scanBtn.textContent = up6Scanner.scanNowText || 'Scan Now';
                        scanResults.innerHTML = '<div class="notice notice-error"><p>' + up6Scanner.errorText + '</p></div>';
                        scanResults.style.display = '';
                    } );
            } );
        }

        // ── Security tab: Regenerate Checksums ──────────────────────────
        var regenBtn     = document.getElementById( 'up6-regen-checksums' );
        var regenSpinner = document.getElementById( 'up6-regen-spinner' );
        var regenResult  = document.getElementById( 'up6-regen-result' );

        if ( regenBtn && typeof up6Scanner !== 'undefined' ) {
            regenBtn.addEventListener( 'click', function () {
                regenBtn.disabled = true;
                regenBtn.textContent = up6Scanner.regenText;
                regenSpinner.classList.add( 'is-active' );
                if ( regenResult ) regenResult.textContent = '';

                var fd = new FormData();
                fd.append( 'action', 'up6_scanner_regen_checksums' );
                fd.append( '_nonce', up6Scanner.regenNonce );

                fetch( up6Scanner.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' } )
                    .then( function ( r ) { return r.json(); } )
                    .then( function ( res ) {
                        regenSpinner.classList.remove( 'is-active' );
                        regenBtn.disabled = false;
                        regenBtn.textContent = up6Scanner.regenBtnText || 'Regenerate Baseline';
                        if ( res.success ) {
                            regenResult.innerHTML = '<span style="color:#27ae60;font-weight:600;">✓ ' + up6Scanner.baselineUpdated + ' — ' + res.data.files + ' ' + up6Scanner.filesHashed + ' (v' + res.data.version + ')</span>';
                        } else {
                            regenResult.innerHTML = '<span style="color:#C0392B;">' + up6Scanner.errorText + '</span>';
                        }
                    } )
                    .catch( function () {
                        regenSpinner.classList.remove( 'is-active' );
                        regenBtn.disabled = false;
                        regenBtn.textContent = up6Scanner.regenBtnText || 'Regenerate Baseline';
                        regenResult.innerHTML = '<span style="color:#C0392B;">' + up6Scanner.errorText + '</span>';
                    } );
            } );
        }
    } );
} )();
