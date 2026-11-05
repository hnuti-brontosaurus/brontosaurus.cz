document.addEventListener( 'click', function( event ) {
	if ( !( event.target instanceof Element ) ) {
		return;
	}

	const button = event.target.closest( '.bis-akce__tab' );
	if ( ! button ) {
		return;
	}

	const block = button.closest( '.bis-akce' );
	if ( ! block ) {
		return;
	}

	block.querySelectorAll( '.bis-akce__tab' ).forEach( function( tab ) {
		tab.setAttribute( 'aria-pressed', String( tab === button ) );
	} );

	block.querySelectorAll( '.bis-akce__view' ).forEach( function( panel ) {
		panel.hidden = panel.id !== button.dataset.bisAkceView;
	} );
} );
