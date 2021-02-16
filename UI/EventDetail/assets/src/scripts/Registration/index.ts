import {
	getElementRealHeight,
	scrollToOneThirdOfViewportHeight,
	waitThen
} from './utils';

import {
	ActionButtonClassSelectors,
	FormClassSelectors
} from './ClassSelectors';


// @todo: split to separate components

export default (window: Window, document: Document) => {
	const actionButtonElements = document.querySelectorAll('.' + ActionButtonClassSelectors.BASE) as NodeListOf<HTMLButtonElement>;
	for (let i in actionButtonElements) {
		let actionButtonElement = actionButtonElements[i];

		if (typeof actionButtonElement !== 'object') {
			continue;
		}

		const formElement = findRelatedFormElement(actionButtonElement);
		updateFormElementHeight(window, formElement);
		window.addEventListener('resize', () => updateFormElementHeight(window, formElement));

		actionButtonElement.addEventListener('click', (event) => {
			if ( ! actionButtonElement.classList.contains(ActionButtonClassSelectors.ACTIVATED)) {
				scrollToOneThirdOfViewportHeight(document, actionButtonElement);
			}

			actionButtonElement.classList.toggle(ActionButtonClassSelectors.ACTIVATED);

			const formElement = findRelatedFormElement(event.currentTarget as HTMLButtonElement);
			formElement.classList.toggle(FormClassSelectors.HIDDEN);
		});
	}
};

const findRelatedFormElement = (buttonElement: HTMLButtonElement): HTMLFormElement => {
	const parentElement = buttonElement.parentElement;
	if (parentElement === null) {
		throw new Error('Related form element was not found.');
	}

	const formElement = parentElement.querySelector('.' + FormClassSelectors.BASE);
	if (formElement === null) {
		throw new Error('Related form element was not found.');
	}

	return (formElement as HTMLFormElement);
};

const updateFormElementHeight = (window: Window, formElement: HTMLFormElement) => {
	const isInHiddenState = formElement.classList.contains(FormClassSelectors.HIDDEN);
	formElement.classList.remove(FormClassSelectors.ANIMATED);

	if (isInHiddenState) {
		waitThen(window, () => formElement.classList.remove(FormClassSelectors.HIDDEN)); // display registration for a while
	}

	waitThen(window, () => {
		formElement.style.setProperty('--formHeight', getElementRealHeight(window, formElement) + 'px');

		if  (isInHiddenState) {
			formElement.classList.add(FormClassSelectors.HIDDEN); // hide it again
		}

		waitThen(window, () => {
			formElement.classList.add(FormClassSelectors.ANIMATED);
		});
	});
};
