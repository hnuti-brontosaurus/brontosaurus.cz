document.addEventListener('DOMContentLoaded', () => {

	// toggle menu on mobile
	document.getElementById('header-toggleNavigationLink').addEventListener('click', (ev) => {
		/**
		 * When viewport is too small on y-axis, we want to jump to footer navigation instead of toggling menu,
		 * because headroom would eat scrolling in opened menu and hide whole header with the menu as well, which is UX bug.
		 * Better solution would be not to let headroom take effect when scrolling on menu element but for quick fix this is enough.
		 */
		if (window.matchMedia('(max-height: 27em)').matches) { // the number represents height of opened menu with all items visible
			return;
		}

		document.getElementById('header').classList.toggle('header--isOpened');
		ev.preventDefault();
	});


	// init headroom for sticky navigation
	const headroom = new Headroom(document.getElementById('header-wrapper'), {
		tolerance: {
			up: 50,
			down: 0,
		},
	});
	headroom.init();


	// toggle search
	const searchBarTogglerEl = document.getElementById('searchBarToggler');
	const searchBarEl = document.getElementById('searchBar');
	const searchBarCloseButtonEl = document.getElementById('searchBarCloseButton');

	if (searchBarTogglerEl === null || searchBarEl === null || searchBarCloseButtonEl === null) {
		return; // no elements, no handling
	}

	searchBarTogglerEl.addEventListener('click', ev => {
		ev.preventDefault();
		searchBarEl.classList.toggle('searchForm--hidden');
	});

	searchBarCloseButtonEl.addEventListener('click', () => searchBarEl.classList.add('searchForm--hidden'));

});
