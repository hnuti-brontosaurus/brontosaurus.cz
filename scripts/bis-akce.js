function setBisAkceFilter( filter ) {
	document.querySelectorAll( '.bis-akce' ).forEach( function( block ) {
		const tabs = Array.from( block.querySelectorAll( '.bis-akce__tab' ) );
		const selectedTab = tabs.find( function( tab ) {
			return tab.dataset.bisAkceFilter === filter;
		} ) || tabs.find( function( tab ) {
			return tab.dataset.bisAkceFilter === '';
		} );

		if ( ! selectedTab ) {
			return;
		}

		tabs.forEach( function( tab ) {
			const isSelected = tab === selectedTab;
			tab.setAttribute( 'aria-pressed', String( isSelected ) );
			const panel = document.getElementById( tab.dataset.bisAkceView );
			if ( panel ) {
				panel.hidden = ! isSelected;
			}
		} );
	} );
}

document.addEventListener( 'click', function( event ) {
	if ( !( event.target instanceof Element ) ) {
		return;
	}

	const button = event.target.closest( '.bis-akce__tab' );
	if ( ! button ) {
		return;
	}

	const filter = button.dataset.bisAkceFilter || '';
	const url = new URL( window.location.href );
	if ( filter ) {
		url.searchParams.set( 'jen', filter );
	} else {
		url.searchParams.delete( 'jen' );
	}
	window.history.pushState( {}, '', url );
	setBisAkceFilter( filter );
} );

window.addEventListener( 'popstate', function() {
	setBisAkceFilter( new URL( window.location.href ).searchParams.get( 'jen' ) || '' );
} );
