/**
 * UP6 Suara Semasa — Navigation A11y.
 *
 * Handles focus management for the mobile navigation overlay:
 * - Traps focus inside overlay when open
 * - Closes on Escape key
 * - Returns focus to toggle button on close
 *
 * @package UP6_Suara_Semasa
 * @since   1.0.0
 */
( function () {
	'use strict';

	/** Focusable element selectors. */
	const FOCUSABLE = [
		'a[href]',
		'button:not([disabled])',
		'input:not([disabled]):not([type="hidden"])',
		'textarea:not([disabled])',
		'select:not([disabled])',
		'[tabindex]:not([tabindex="-1"])',
	].join( ', ' );

	/**
	 * Initialise once the responsive navigation block is in the DOM.
	 */
	function init() {
		const navBlock = document.querySelector(
			'.wp-block-navigation__responsive-container'
		);
		if ( ! navBlock ) {
			return;
		}

		const toggleOpen = document.querySelector(
			'.wp-block-navigation__responsive-container-open'
		);
		const toggleClose = document.querySelector(
			'.wp-block-navigation__responsive-container-close'
		);

		if ( ! toggleOpen || ! toggleClose ) {
			return;
		}

		/** Observe the `is-menu-open` class on the container. */
		const observer = new MutationObserver( function ( mutations ) {
			mutations.forEach( function ( m ) {
				if ( m.type !== 'attributes' || m.attributeName !== 'class' ) {
					return;
				}
				const isOpen = navBlock.classList.contains( 'is-menu-open' );
				if ( isOpen ) {
					onOpen( navBlock, toggleClose );
				}
			} );
		} );

		observer.observe( navBlock, { attributes: true } );

		/** Close overlay on Escape. */
		document.addEventListener( 'keydown', function ( e ) {
			if (
				e.key === 'Escape' &&
				navBlock.classList.contains( 'is-menu-open' )
			) {
				toggleClose.click();
				toggleOpen.focus();
			}
		} );
	}

	/**
	 * When overlay opens: move focus and set up tab trap.
	 *
	 * @param {HTMLElement} container - The overlay container.
	 * @param {HTMLElement} closeBtn  - The close button.
	 */
	function onOpen( container, closeBtn ) {
		// Move focus to close button.
		requestAnimationFrame( function () {
			closeBtn.focus();
		} );

		// Tab trap.
		container.addEventListener( 'keydown', function trap( e ) {
			if ( e.key !== 'Tab' ) {
				return;
			}

			const focusable = Array.from(
				container.querySelectorAll( FOCUSABLE )
			);
			if ( focusable.length === 0 ) {
				return;
			}

			const first = focusable[ 0 ];
			const last = focusable[ focusable.length - 1 ];

			if ( e.shiftKey ) {
				if ( document.activeElement === first ) {
					e.preventDefault();
					last.focus();
				}
			} else {
				if ( document.activeElement === last ) {
					e.preventDefault();
					first.focus();
				}
			}

			// Clean up when overlay is closed.
			if ( ! container.classList.contains( 'is-menu-open' ) ) {
				container.removeEventListener( 'keydown', trap );
			}
		} );
	}

	/** Boot. */
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
