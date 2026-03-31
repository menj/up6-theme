/**
 * UP6 Grid — Load More pagination + Masonry layout
 *
 * Loaded on archive, search, and single post pages only.
 * Dependencies: jQuery, imagesLoaded, Masonry.
 *
 * Contains:
 *   1. Load More (archive/search) — appends next-page cards via $.get(),
 *      fade-in animation, button states: Load More → Loading… → All caught up!
 *      Localised strings passed via wp_localize_script as `up6Grid.i18n`.
 *   2. Masonry (single post Related News grid) — repositions cards after
 *      all images load via imagesLoaded; CSS grid fallback for no-JS.
 *
 * @package UP6
 * @since   2.5.0
 */
( function ( $ ) {
    'use strict';

    /* ── Archive & Search: Load More only (no Masonry) ──────── */

    var $archiveGrid = $( '.archive .articles-grid, .search-results .articles-grid' ).first();

    if ( $archiveGrid.length ) {

        var $pagination = $( '.pagination' );
        $pagination.hide();

        var nextPageUrl = $pagination.find( '.next' ).attr( 'href' );

        if ( nextPageUrl ) {
            var $loadMore = $( '<div class="up6-load-more-wrap"><button class="up6-load-more-btn">' + up6Grid.i18n.loadMore + '</button></div>' );
            $archiveGrid.after( $loadMore );

            var $btn      = $loadMore.find( '.up6-load-more-btn' );
            var isLoading = false;
            var noMore    = false;

            $btn.on( 'click', function () {
                if ( isLoading || noMore ) return;
                isLoading = true;
                $btn.text( up6Grid.i18n.loading ).prop( 'disabled', true );

                $.get( nextPageUrl, function ( data ) {
                    var $newCards = $( data ).find( '.articles-grid .article-card' );
                    var $newNext  = $( data ).find( '.pagination .next' );
                    nextPageUrl   = $newNext.length ? $newNext.attr( 'href' ) : null;

                    $newCards.css( 'opacity', 0 );
                    $archiveGrid.append( $newCards );
                    $newCards.animate( { opacity: 1 }, 300 );

                    if ( ! nextPageUrl ) {
                        noMore = true;
                        $btn.text( up6Grid.i18n.noMore ).prop( 'disabled', true ).addClass( 'up6-load-more-exhausted' );
                    } else {
                        $btn.text( up6Grid.i18n.loadMore ).prop( 'disabled', false );
                    }
                    isLoading = false;
                } ).fail( function () {
                    $btn.text( up6Grid.i18n.error ).prop( 'disabled', false );
                    isLoading = false;
                } );
            } );
        }
    }

    /* ── Related News: CSS Grid (no masonry) ─────────────── */
    /* Equal-height rows handled by grid-template-columns: repeat(3, 1fr) in CSS */

} )( jQuery );
