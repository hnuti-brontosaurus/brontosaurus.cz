import FiltersToggler from './FiltersToggler';
import MoreEventsToggler from './MoreEventsToggler';

document.addEventListener('DOMContentLoaded', (): void => {
	initializeFiltersToggler();
	initializeMoreEventsToggler();
});

const initializeFiltersToggler = () => {
	const togglerElement = document.getElementById('events-filters-toggler');
	const stateElement = document.getElementById('events-filters');
	const animatedElement = document.getElementById('events-filters-list');
	if (togglerElement === null || stateElement === null || animatedElement === null) {
		return;
	}

	const filtersToggler = new FiltersToggler(togglerElement, stateElement, animatedElement);
	filtersToggler.activate();
};

const initializeMoreEventsToggler = () => {
	const showMoreButtonElement = document.getElementById('events-showMore-button');
	if (showMoreButtonElement === null) {
		return;
	}

	const showMoreContentElement = document.getElementById('events-showMore-content');
	if (showMoreContentElement === null) {
		return;
	}

	const moreEventsToggler = new MoreEventsToggler(showMoreButtonElement, showMoreContentElement);
	moreEventsToggler.activate();
};
