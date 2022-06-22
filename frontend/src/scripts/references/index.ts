import {Position} from './Position';
import {Dots} from './Dots';
import {selectors} from './selectors';

document.addEventListener('DOMContentLoaded', () => {
	const CSS_PROPERTY_NAME = '--carouselPosition';

	const $dataStorage = document.getElementById('references-data-storage');
	const $previousButton = document.getElementById('references-action-previous');
	const $nextButton = document.getElementById('references-action-next');

	if ($dataStorage === null || $previousButton === null || $nextButton === null) {
		return;
	}

	const slidesCount = $dataStorage.children.length;
	const defaultPosition = parseInt(window.getComputedStyle($dataStorage).getPropertyValue(CSS_PROPERTY_NAME));
	const allowInfinite = typeof $dataStorage.dataset.carouselInfinite !== 'undefined';

	const position = new Position(slidesCount, defaultPosition, allowInfinite);
	const dots = new Dots($dataStorage, position, slidesCount);

	position.addPositionChangedSubscriber((newPosition) => {
		$dataStorage.style.setProperty(CSS_PROPERTY_NAME, newPosition.toString());
		updateButtonVisibility();
		dots.repaint(newPosition);
	});

	$previousButton.addEventListener('click', () => {
		if (position.isAtFirst()) {
			return;
		}

		position.moveToPrevious();
	});

	$nextButton.addEventListener('click', () => {
		if (position.isAtLast()) {
			return;
		}

		position.moveToNext();
	});

	const updateButtonVisibility = () => {
		$previousButton.classList.remove(selectors.BUTTON_HIDDEN);
		$nextButton.classList.remove(selectors.BUTTON_HIDDEN);

		if (position.isAtFirst()) {
			$previousButton.classList.add(selectors.BUTTON_HIDDEN);
		}

		if (position.isAtLast()) {
			$nextButton.classList.add(selectors.BUTTON_HIDDEN);
		}
	};


	// on init
	updateButtonVisibility();
	dots.repaint(defaultPosition);
});
