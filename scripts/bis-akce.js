const bisAkceRequests = new WeakMap();

function loadBisAkcePanel( block, button, panel ) {
	if ( bisAkceRequests.has( panel ) ) {
		return bisAkceRequests.get( panel );
	}

	let filters;
	try {
		filters = JSON.parse( button.dataset.bisAkceFilters || '{}' );
	} catch ( error ) {
		panel.textContent = 'Omlouváme se, filtr se nepodařilo načíst.';
		console.error( 'Invalid BIS filter data.', error );
		return Promise.resolve();
	}

	panel.setAttribute( 'aria-busy', 'true' );
	panel.innerHTML = '<p class="bis-akce__loading" role="status"><span class="bis-akce__spinner" aria-hidden="true"></span><span>Načítání akcí…</span></p>';
	const request = fetch( block.dataset.bisAkceEndpoint, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify( {
			filters: filters,
			limit: block.dataset.bisAkceLimit,
		} ),
	} )
		.then( function( response ) {
			if ( ! response.ok ) {
				throw new Error( 'BIS events request failed with status ' + response.status );
			}
			return response.json();
		} )
		.then( function( response ) {
			if ( typeof response.markup !== 'string' ) {
				throw new Error( 'BIS events response did not contain markup.' );
			}
			panel.innerHTML = response.markup;
		} )
		.catch( function( error ) {
			panel.innerHTML = '<p class="bis-akce__message" role="alert">Omlouváme se, akce se teď nepodařilo načíst. Zkuste to prosím později.</p>';
			console.error( 'Unable to load BIS events.', error );
		} )
		.finally( function() {
			panel.removeAttribute( 'aria-busy' );
			bisAkceRequests.delete( panel );
		} );

	bisAkceRequests.set( panel, request );
	return request;
}

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

		const selectedPanel = document.getElementById( selectedTab.dataset.bisAkceView );
		if ( selectedPanel ) {
			loadBisAkcePanel( block, selectedTab, selectedPanel );
		}
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
