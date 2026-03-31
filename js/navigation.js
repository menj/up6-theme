/**
 * UP6 — Front-end JavaScript (vanilla, no dependencies)
 *
 * Loaded on all pages. Contains:
 *   1. Mobile drawer — hamburger toggle, overlay, body scroll lock
 *   2. Desktop dropdown menus — hover/focus with aria-expanded
 *   3. Dark mode — toggle button, localStorage persistence, no-flash script in header.php
 *   4. Scroll progress bar — red line at bottom of header, tracks scroll position
 *   5. Copy Link — clipboard API with visual tick confirmation (2s reset)
 *   6. Article voting — thumbs up/down AJAX handler (single posts only)
 *   7. Table of Contents — auto-generated from h2/h3 headings (≥3 required),
 *      desktop expanded+sticky, mobile collapsible, scroll spy highlights current
 *
 * Voting data passed via wp_localize_script as `up6Vote` object.
 * No jQuery dependency — runs in an IIFE to avoid global scope pollution.
 *
 * @package UP6
 * @since   2.4.4
 * @updated 2.8 — added voting, TOC, scroll spy
 */
( function () {
    'use strict';

    /* ── Mobile drawer toggle ── */
    var btn    = document.querySelector( '.menu-toggle' );
    var drawer = document.getElementById( 'mobile-nav' );

    if ( btn && drawer ) {
        btn.addEventListener( 'click', function () {
            var open = drawer.classList.toggle( 'is-open' );
            drawer.setAttribute( 'aria-hidden', String( ! open ) );
            btn.setAttribute( 'aria-expanded', String( open ) );
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' && drawer.classList.contains( 'is-open' ) ) {
                drawer.classList.remove( 'is-open' );
                drawer.setAttribute( 'aria-hidden', 'true' );
                btn.setAttribute( 'aria-expanded', 'false' );
                btn.focus();
            }
        } );
    }

    /* ── Mobile search toggle ── */
    var searchToggle = document.getElementById( 'up6-mobile-search-toggle' );
    var searchBar    = document.getElementById( 'mobile-search-bar' );
    var searchInput  = document.getElementById( 'mobile-search-input' );

    if ( searchToggle && searchBar ) {
        searchToggle.addEventListener( 'click', function () {
            var open = searchBar.classList.toggle( 'is-open' );
            searchBar.setAttribute( 'aria-hidden', String( ! open ) );
            searchToggle.setAttribute( 'aria-expanded', String( open ) );
            searchToggle.classList.toggle( 'is-active', open );
            // Close mobile nav drawer if open
            if ( open && drawer && drawer.classList.contains( 'is-open' ) ) {
                drawer.classList.remove( 'is-open' );
                drawer.setAttribute( 'aria-hidden', 'true' );
                if ( btn ) btn.setAttribute( 'aria-expanded', 'false' );
            }
            if ( open && searchInput ) {
                setTimeout( function () { searchInput.focus(); }, 50 );
            }
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' && searchBar.classList.contains( 'is-open' ) ) {
                searchBar.classList.remove( 'is-open' );
                searchBar.setAttribute( 'aria-hidden', 'true' );
                searchToggle.setAttribute( 'aria-expanded', 'false' );
                searchToggle.classList.remove( 'is-active' );
                searchToggle.focus();
            }
        } );

        // Also close search bar when drawer opens
        if ( btn && drawer ) {
            btn.addEventListener( 'click', function () {
                if ( searchBar.classList.contains( 'is-open' ) ) {
                    searchBar.classList.remove( 'is-open' );
                    searchBar.setAttribute( 'aria-hidden', 'true' );
                    searchToggle.setAttribute( 'aria-expanded', 'false' );
                    searchToggle.classList.remove( 'is-active' );
                }
            } );
        }
    }

    /* ── Desktop dropdown (primary nav) ── */
    var primaryNav = document.getElementById( 'primary-menu' );

    if ( primaryNav ) {
        var parents = primaryNav.querySelectorAll( '.menu-item-has-children' );

        parents.forEach( function ( item ) {
            var link    = item.querySelector( ':scope > a' );
            var submenu = item.querySelector( ':scope > .sub-menu' );
            if ( ! link || ! submenu ) return;

            link.setAttribute( 'aria-haspopup', 'true' );
            link.setAttribute( 'aria-expanded', 'false' );

            function openMenu() {
                item.classList.add( 'is-open' );
                link.setAttribute( 'aria-expanded', 'true' );
            }
            function closeMenu() {
                item.classList.remove( 'is-open' );
                link.setAttribute( 'aria-expanded', 'false' );
            }

            // Click toggle
            link.addEventListener( 'click', function ( e ) {
                if ( item.classList.contains( 'is-open' ) ) {
                    closeMenu();
                } else {
                    parents.forEach( function ( sib ) {
                        if ( sib !== item ) {
                            sib.classList.remove( 'is-open' );
                            var sibLink = sib.querySelector( ':scope > a' );
                            if ( sibLink ) sibLink.setAttribute( 'aria-expanded', 'false' );
                        }
                    } );
                    openMenu();
                    e.preventDefault();
                }
            } );

            // Hover on pointer devices
            item.addEventListener( 'mouseenter', openMenu );
            item.addEventListener( 'mouseleave', closeMenu );

            // Escape from within submenu
            submenu.addEventListener( 'keydown', function ( e ) {
                if ( e.key === 'Escape' ) { closeMenu(); link.focus(); }
            } );

            // Close when focus leaves the item
            item.addEventListener( 'focusout', function ( e ) {
                if ( ! item.contains( e.relatedTarget ) ) closeMenu();
            } );
        } );

        // Escape anywhere in nav
        primaryNav.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Escape' ) {
                parents.forEach( function ( item ) {
                    item.classList.remove( 'is-open' );
                    var l = item.querySelector( ':scope > a' );
                    if ( l ) l.setAttribute( 'aria-expanded', 'false' );
                } );
            }
        } );

        // Close on outside click
        document.addEventListener( 'click', function ( e ) {
            if ( ! primaryNav.contains( e.target ) ) {
                parents.forEach( function ( item ) {
                    item.classList.remove( 'is-open' );
                    var l = item.querySelector( ':scope > a' );
                    if ( l ) l.setAttribute( 'aria-expanded', 'false' );
                } );
            }
        } );
    }

    /* ── Mobile drawer dropdowns ── */
    if ( drawer ) {
        var mobileParents = drawer.querySelectorAll( '.menu-item-has-children' );

        mobileParents.forEach( function ( item ) {
            var link = item.querySelector( ':scope > a' );
            if ( ! link ) return;

            var chevron = document.createElement( 'span' );
            chevron.className = 'mobile-chevron';
            chevron.setAttribute( 'aria-hidden', 'true' );
            link.appendChild( chevron );

            link.addEventListener( 'click', function ( e ) {
                e.preventDefault();
                item.classList.toggle( 'is-open' );
            } );
        } );
    }

    /* ── Dark mode toggle ── */
    var themeToggle = document.getElementById( 'up6-theme-toggle' );
    var html        = document.documentElement;
    var STORAGE_KEY = 'up6_theme';

    // Apply saved preference immediately on load
    if ( localStorage.getItem( STORAGE_KEY ) === 'dark' ) {
        html.classList.add( 'up6-dark' );
    }

    if ( themeToggle ) {
        themeToggle.addEventListener( 'click', function () {
            var isDark = html.classList.toggle( 'up6-dark' );
            localStorage.setItem( STORAGE_KEY, isDark ? 'dark' : 'light' );
            themeToggle.setAttribute( 'aria-label',
                isDark
                    ? themeToggle.getAttribute( 'data-label-light' ) || 'Switch to light mode'
                    : themeToggle.getAttribute( 'data-label-dark' )  || 'Switch to dark mode'
            );
        } );
    }
    /* ── Scroll progress bar (single posts only) ── */
    var progressBar = document.getElementById( 'up6-scroll-progress' );

    if ( progressBar ) {
        var ticking = false;

        function updateProgress() {
            var docHeight = document.documentElement.scrollHeight - window.innerHeight;
            var scrolled  = window.scrollY || document.documentElement.scrollTop;
            var pct       = docHeight > 0 ? Math.min( 100, ( scrolled / docHeight ) * 100 ) : 0;
            progressBar.style.width = pct.toFixed( 2 ) + '%';
            ticking = false;
        }

        window.addEventListener( 'scroll', function () {
            if ( ! ticking ) {
                requestAnimationFrame( updateProgress );
                ticking = true;
            }
        }, { passive: true } );

        updateProgress(); // initialise for anchor-linked loads
    }

    /* ── Copy link button (single post share bar) ── */
    var copyBtn = document.querySelector( '.share-btn--copy' );
    if ( copyBtn ) {
        copyBtn.addEventListener( 'click', function () {
            var url       = this.getAttribute( 'data-url' );
            var iconLink  = this.querySelector( '.icon-link' );
            var iconCheck = this.querySelector( '.icon-check' );

            navigator.clipboard.writeText( url ).then( function () {
                if ( iconLink )  iconLink.style.display  = 'none';
                if ( iconCheck ) iconCheck.style.display  = 'block';
                copyBtn.classList.add( 'is-copied' );

                setTimeout( function () {
                    if ( iconLink )  iconLink.style.display  = '';
                    if ( iconCheck ) iconCheck.style.display  = 'none';
                    copyBtn.classList.remove( 'is-copied' );
                }, 2000 );
            } );
        } );
    }

    /* ── Article voting (single posts) ── */
    var voteWrap = document.querySelector( '.entry-vote' );
    if ( voteWrap && typeof up6Vote !== 'undefined' ) {
        var voteBtns = voteWrap.querySelectorAll( '.vote-btn' );
        voteBtns.forEach( function ( btn ) {
            btn.addEventListener( 'click', function () {
                // Already voted — do nothing
                if ( voteWrap.classList.contains( 'has-voted' ) ) return;

                var type   = btn.getAttribute( 'data-type' );
                var postId = voteWrap.getAttribute( 'data-post-id' );

                btn.disabled = true;

                var fd = new FormData();
                fd.append( 'action', 'up6_vote' );
                fd.append( 'nonce', up6Vote.nonce );
                fd.append( 'post_id', postId );
                fd.append( 'type', type );

                fetch( up6Vote.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' } )
                    .then( function ( r ) { return r.json(); } )
                    .then( function ( res ) {
                        btn.disabled = false;
                        if ( ! res.success ) return;

                        // Update counts — create spans if they don't exist yet (below threshold)
                        var upBtn     = voteWrap.querySelector( '.vote-btn--up' );
                        var downBtn   = voteWrap.querySelector( '.vote-btn--down' );
                        var upCount   = voteWrap.querySelector( '.vote-count--up' );
                        var downCount = voteWrap.querySelector( '.vote-count--down' );
                        if ( ! upCount && upBtn ) {
                            upCount = document.createElement( 'span' );
                            upCount.className = 'vote-count vote-count--up';
                            upBtn.appendChild( upCount );
                        }
                        if ( ! downCount && downBtn ) {
                            downCount = document.createElement( 'span' );
                            downCount.className = 'vote-count vote-count--down';
                            downBtn.appendChild( downCount );
                        }
                        if ( upCount )   upCount.textContent   = res.data.up;
                        if ( downCount ) downCount.textContent  = res.data.down;

                        // Mark as voted
                        voteWrap.classList.add( 'has-voted' );
                        var votedBtn = voteWrap.querySelector( '.vote-btn--' + res.data.voted );
                        if ( votedBtn ) votedBtn.classList.add( 'is-voted' );
                    } )
                    .catch( function () {
                        btn.disabled = false;
                    } );
            } );
        } );

        // If already voted (from cookie/user meta), mark immediately
        if ( up6Vote.voted ) {
            voteWrap.classList.add( 'has-voted' );
        }
    }

    /* ── Auto-generated Table of Contents (single posts) ── */
    var tocNav  = document.querySelector( '.article-toc' );
    var content = document.querySelector( '.entry-content' );
    if ( tocNav && content ) {
        var headings = content.querySelectorAll( 'h2, h3' );
        if ( headings.length >= 3 ) {
            var tocList = tocNav.querySelector( '.article-toc-list' );
            var tocIds  = [];

            headings.forEach( function ( h, i ) {
                // Ensure heading has an ID for anchor linking
                if ( ! h.id ) {
                    h.id = 'section-' + ( i + 1 );
                }
                tocIds.push( h.id );

                var li = document.createElement( 'li' );
                li.className = 'toc-item' + ( h.tagName === 'H3' ? ' toc-item--sub' : '' );
                var a = document.createElement( 'a' );
                a.href = '#' + h.id;
                a.textContent = h.textContent;
                a.className = 'toc-link';
                li.appendChild( a );
                tocList.appendChild( li );
            } );

            // Show the TOC
            tocNav.removeAttribute( 'hidden' );

            // Toggle button (mobile collapsed view)
            var toggleBtn = tocNav.querySelector( '.article-toc-toggle' );
            if ( toggleBtn ) {
                toggleBtn.addEventListener( 'click', function () {
                    var expanded = this.getAttribute( 'aria-expanded' ) === 'true';
                    this.setAttribute( 'aria-expanded', String( ! expanded ) );
                    tocNav.classList.toggle( 'is-open' );
                } );
            }

            // Scroll spy — highlight current section
            var tocLinks = tocList.querySelectorAll( '.toc-link' );
            var spyTicking = false;
            window.addEventListener( 'scroll', function () {
                if ( spyTicking ) return;
                spyTicking = true;
                requestAnimationFrame( function () {
                    var scrollY = window.scrollY + 120;
                    var current = '';
                    headings.forEach( function ( h ) {
                        if ( h.getBoundingClientRect().top + window.scrollY <= scrollY ) {
                            current = h.id;
                        }
                    } );
                    tocLinks.forEach( function ( link ) {
                        if ( link.getAttribute( 'href' ) === '#' + current ) {
                            link.classList.add( 'is-active' );
                        } else {
                            link.classList.remove( 'is-active' );
                        }
                    } );
                    spyTicking = false;
                } );
            }, { passive: true } );
        }
    }

} )();
