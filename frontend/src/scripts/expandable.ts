document.addEventListener('DOMContentLoaded', () =>
	document.querySelectorAll<HTMLElement>('.hb-expandable').forEach(rootEl =>
		initializeExpandable(rootEl)));

const initializeExpandable = (rootEl: HTMLElement) => {
	const togglerEl = rootEl.querySelector<HTMLElement>('[data-hb-expandable-toggler]');
	if (togglerEl === null) {
		throw new Error('Toggler element is not set. Check that you have "data-hb-expandable-toggler" attribute set on your toggling button');
	}

	const contentEl = rootEl.querySelector<HTMLElement>('[data-hb-expandable-content]');
	if (contentEl === null) {
		throw new Error('Expanding content element is not set. Check that you have "data-hb-expandable-content" attribute set on element you want to expand');
	}

	const content = initializeContent(contentEl);
	const root = initializeRoot(rootEl, content);
	initializeToggler(togglerEl, root);
}

interface RootApi {
	toggleExpanded: () => void,
	removeResizeListener: () => void,
}

const initializeRoot = (el: HTMLElement, content: ContentApi): RootApi => {
	const ExpandedSelector = 'hb-expandable--expanded';

	const shouldExpandOnStart = () => !! el.dataset.hbExpandableExpanded
	const isExpanded = () => el.classList.contains(ExpandedSelector);

	const setExpanded = () => {
		el.classList.add(ExpandedSelector);
		content.updateHeight();
	}
	const setCollapsed = () => {
		el.classList.remove(ExpandedSelector);
		content.removeHeight();
	}


	if (shouldExpandOnStart()) {
		setExpanded();
	}

	const onResize = () => isExpanded() && content.updateHeight()
	window.addEventListener('resize', onResize);

	return {
		toggleExpanded: () => isExpanded() ? setCollapsed() : setExpanded(),
		removeResizeListener: () => window.removeEventListener('resize', onResize),
	};
}

const initializeToggler = (el: HTMLElement, root: RootApi) => {
	const onClick = () => {
		root.toggleExpanded();

		if ( !! el.dataset.hbExpandableTogglerRemoveOnExpand) {
			el.removeEventListener('click', onClick);
			root.removeResizeListener();
			el.remove();
		}
	}

	el.addEventListener('click', onClick);
}

interface ContentApi {
	updateHeight: () => void,
	removeHeight: () => void,
}

const initializeContent = (el: HTMLElement): ContentApi => {
	const countRealHeightOf = (el: HTMLElement): number => {
		const paddingTop = parseInt(window.getComputedStyle(el).paddingTop!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
		const paddingBottom = parseInt(window.getComputedStyle(el).paddingBottom!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
		return el.scrollHeight + paddingTop + paddingBottom;
	}

	return {
		updateHeight: () => el.style.setProperty('--height', countRealHeightOf(el) + 'px'),
		removeHeight: () => el.style.removeProperty('--height'),
	}
}

