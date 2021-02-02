// @ts-ignore TS reports "could not find declaration file" even it is able to match definitions.
import Headroom from 'headroom.js';


document.addEventListener('DOMContentLoaded', () => {
	handleMenuToggling();
	handleMenuVisibilityWhenScrolling();
	handleSearchBarToggling();
});


const handleMenuToggling = () => {
	const toggleNavigationLinkElement = document.getElementById('header-toggleNavigationLink')!; // is always present
	const headerElement = document.getElementById('header')!; // is always present

	toggleNavigationLinkElement.addEventListener('click', (e) => {
		/**
		 * When viewport is too small on y-axis, we want to jump to footer navigation instead of toggling menu,
		 * because headroom would eat scrolling in opened menu and hide whole header with the menu as well, which is UX bug.
		 * Better solution would be not to let headroom take effect when scrolling on menu element but for quick fix this is enough.
		 */
		if (window.matchMedia('(max-height: 27em)').matches) { // the number represents height of opened menu with all items visible
			return;
		}

		headerElement.classList.toggle('header--isOpened');
		e.preventDefault();
	});
};


const handleSearchBarToggling = () => {
	const SEARCH_BAR_HIDDEN_CLASS_SELECTOR = 'searchForm--hidden';

	const searchBarTogglerElement = document.getElementById('searchBarToggler') as HTMLAnchorElement;
	const searchBarElement = document.getElementById('searchBar') as HTMLElement;
	const searchBarCloseButtonElement = document.getElementById('searchBarCloseButton') as HTMLButtonElement;

	if (searchBarTogglerElement === null || searchBarElement === null || searchBarCloseButtonElement === null) {
		return; // no elements, no handling
	}

	searchBarTogglerElement.addEventListener('click', (event) => {
		event.preventDefault();
		searchBarElement.classList.toggle(SEARCH_BAR_HIDDEN_CLASS_SELECTOR);
	});

	searchBarCloseButtonElement.addEventListener('click', (event) => {
		searchBarElement.classList.add(SEARCH_BAR_HIDDEN_CLASS_SELECTOR);
	});
};


const handleMenuVisibilityWhenScrolling = () => {
	const menuElement = document.getElementById('header-wrapper')!; // is always present
	const headroom = new Headroom(menuElement, {
		tolerance: {
			up: 50,
		},
	});
	headroom.init();
	headroom.update();
};
