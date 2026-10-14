/**
 * Mobile menu overlay: open/close, Escape to close, focus trap, focus return. Vanilla JS, no dependencies.
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '.bungry-menu-toggle' );
	var menu = document.getElementById( 'bungry-mobile-menu' );
	if ( ! toggle || ! menu ) {
		return;
	}

	var closeButton = menu.querySelector( '.bungry-menu-close' );
	var lastFocus = null;

	function focusable() {
		return Array.prototype.slice.call( menu.querySelectorAll( 'a[href], button:not([disabled])' ) );
	}

	function open() {
		lastFocus = document.activeElement;
		menu.hidden = false;
		toggle.setAttribute( 'aria-expanded', 'true' );
		document.body.classList.add( 'bungry-menu-open' );
		var items = focusable();
		if ( items.length ) {
			items[ 0 ].focus();
		}
	}

	function close() {
		menu.hidden = true;
		toggle.setAttribute( 'aria-expanded', 'false' );
		document.body.classList.remove( 'bungry-menu-open' );
		if ( lastFocus && typeof lastFocus.focus === 'function' ) {
			lastFocus.focus();
		}
	}

	toggle.addEventListener( 'click', open );
	if ( closeButton ) {
		closeButton.addEventListener( 'click', close );
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( menu.hidden ) {
			return;
		}
		if ( event.key === 'Escape' ) {
			close();
			return;
		}
		if ( event.key !== 'Tab' ) {
			return;
		}
		var items = focusable();
		if ( ! items.length ) {
			return;
		}
		var first = items[ 0 ];
		var last = items[ items.length - 1 ];
		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	} );

	// Leaving the mobile breakpoint should not leave the overlay open.
	var desktop = window.matchMedia( '(min-width: 768px)' );
	var onChange = function ( event ) {
		if ( event.matches && ! menu.hidden ) {
			close();
		}
	};
	if ( desktop.addEventListener ) {
		desktop.addEventListener( 'change', onChange );
	} else if ( desktop.addListener ) {
		desktop.addListener( onChange );
	}
} )();
