import {Position} from './Position';
import {Dots} from './Dots';
import {selectors} from './selectors';
import {Autoplay} from './Autoplay';

document.addEventListener('DOMContentLoaded', () =>
	document.querySelectorAll<HTMLElement>('[data-references]')
		.forEach(el => initialize(el)));

function initialize(rootEl: HTMLElement): void
{
	const CarousePositionPropertyName = '--carouselPosition';

	const slidesEl = rootEl.querySelector<HTMLElement>('[data-references-slides]');
	const previousButtonEl = rootEl.querySelector<HTMLElement>('[data-references-button="previous"]');
	const nextButtonEl = rootEl.querySelector<HTMLElement>('[data-references-button="next"]');
	if (slidesEl === null || previousButtonEl === null || nextButtonEl === null) {
		return;
	}

	const slidesCount = slidesEl.children.length;
	const defaultPosition = parseInt(window.getComputedStyle(slidesEl).getPropertyValue(CarousePositionPropertyName));
	const allowInfinite = typeof rootEl.dataset.referencesInfinite !== 'undefined';
	const allowAutoplay = typeof rootEl.dataset.referencesAutoplay !== 'undefined';

	const position = new Position(slidesCount, defaultPosition, allowInfinite);
	const dots = new Dots(slidesEl, position, slidesCount);
	const autoplay = new Autoplay(position);

	position.addPositionChangedSubscriber((newPosition) => {
		slidesEl.style.setProperty(CarousePositionPropertyName, newPosition.toString());
		updateButtonVisibility();
		dots.repaint(newPosition);
	});

	previousButtonEl.addEventListener('click', () => {
		if (position.isAtFirst()) {
			return;
		}

		autoplay.disable();
		position.moveToPrevious();
	});

	nextButtonEl.addEventListener('click', () => {
		if (position.isAtLast()) {
			return;
		}

		autoplay.disable();
		position.moveToNext();
	});

	const updateButtonVisibility = () => {
		previousButtonEl.classList.remove(selectors.BUTTON_HIDDEN);
		nextButtonEl.classList.remove(selectors.BUTTON_HIDDEN);

		if (position.isAtFirst()) {
			previousButtonEl.classList.add(selectors.BUTTON_HIDDEN);
		}

		if (position.isAtLast()) {
			nextButtonEl.classList.add(selectors.BUTTON_HIDDEN);
		}
	};


	// on init
	updateButtonVisibility();
	dots.repaint(defaultPosition);

	if (allowAutoplay) {
		autoplay.enable();
	}
}
