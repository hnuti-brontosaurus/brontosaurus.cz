// scrolling

export const scrollToOneThirdOfViewportHeight = (document: Document, targetElement: HTMLElement) => {
	document.documentElement.scrollTo({
		left: 0,
		top: (targetElement.offsetTop - getPercentageOfViewportHeightInPixels(30)),
		behavior: 'smooth',
	});
};

const getPercentageOfViewportHeightInPixels = (percentage: number): number => window.innerHeight / 100 * percentage;


// element dimensions

export const getElementRealHeight = (window: Window, element: HTMLElement) => {
	const paddingTop = parseInt(window.getComputedStyle(element).paddingTop!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
	const paddingBottom = parseInt(window.getComputedStyle(element).paddingBottom!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
	return element.scrollHeight + paddingTop + paddingBottom;
};


// hacks

/**
 * Postpones callback execution after previous execution is finished.
 */
export const waitThen = (window: Window, cb: () => void) => window.setTimeout(cb, 0);
