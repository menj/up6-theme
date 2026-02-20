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
		const navBlocks = document.querySelectorAll(
			'.wp-block-navigation__responsive-container'
		);

		if ( navBlocks.length === 0 ) {
			return;
		}

		navBlocks.forEach( function ( navBlock ) {
			setupNavBlock( navBlock );
		} );
	}

	/**
	 * Bind a11y handlers to a single responsive nav block.
	 *
	 * @param {HTMLElement} navBlock Responsive navigation container.
	 */
	function setupNavBlock( navBlock ) {
		const navParent = navBlock.closest( '.wp-block-navigation' ) || document;
		const toggleOpen = navParent.querySelector(
			'.wp-block-navigation__responsive-container-open'
		);
		const toggleClose = navBlock.querySelector(
			'.wp-block-navigation__responsive-container-close'
		);

		if ( ! toggleOpen || ! toggleClose ) {
			return;
		}

		let removeTrap = null;

		const observer = new MutationObserver( function ( mutations ) {
			mutations.forEach( function ( mutation ) {
				if (
					mutation.type !== 'attributes' ||
					mutation.attributeName !== 'class'
				) {
					return;
				}

				const isOpen = navBlock.classList.contains( 'is-menu-open' );
				if ( isOpen ) {
					if ( removeTrap ) {
						removeTrap();
					}
					removeTrap = onOpen( navBlock, toggleClose );
				} else if ( removeTrap ) {
					removeTrap();
					removeTrap = null;
				}
			} );
		} );

		observer.observe( navBlock, { attributes: true } );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key !== 'Escape' ) {
				return;
			}

			if ( navBlock.classList.contains( 'is-menu-open' ) ) {
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
	 * @return {Function} Cleanup callback.
	 */
	function onOpen( container, closeBtn ) {
		requestAnimationFrame( function () {
			closeBtn.focus();
		} );

		const trap = function ( event ) {
			if ( event.key !== 'Tab' ) {
				return;
			}

			const focusable = Array.from( container.querySelectorAll( FOCUSABLE ) );
			if ( focusable.length === 0 ) {
				return;
			}

			const first = focusable[ 0 ];
			const last = focusable[ focusable.length - 1 ];

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
				return;
			}

			if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		};

		container.addEventListener( 'keydown', trap );

		return function () {
			container.removeEventListener( 'keydown', trap );
		};
	}

	/** Boot. */
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
