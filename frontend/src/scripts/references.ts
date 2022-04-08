document.addEventListener('DOMContentLoaded', () => {
	const CSS_PROPERTY_NAME = '--carouselPosition';
	const CSS_BUTTON_HIDDEN_SELECTOR = 'references__button--hidden';

	const $dataStorage = document.getElementById('references-data-storage');
	const $previousButton = document.getElementById('references-action-previous');
	const $nextButton = document.getElementById('references-action-next');

	if ($dataStorage === null || $previousButton === null || $nextButton === null) {
		return;
	}

	const slidesCount = $dataStorage.children.length;
	let position = parseInt(window.getComputedStyle($dataStorage).getPropertyValue(CSS_PROPERTY_NAME)); // default carousel position
	const allowInfinite = typeof $dataStorage.dataset.carouselInfinite !== 'undefined';

	$previousButton.addEventListener('click', () => {
		if (isAtFirstPosition()) {
			return;
		}

		if (position === 0) {
			position = slidesCount - 1;
		} else {
			position--;
		}

		$dataStorage.style.setProperty(CSS_PROPERTY_NAME, position.toString());

		updateButtonVisibility();
	});

	$nextButton.addEventListener('click', () => {
		if (isAtLastPosition()) {
			return;
		}

		if (position === (slidesCount - 1)) {
			position = 0;
		} else {
			position++;
		}

		$dataStorage.style.setProperty(CSS_PROPERTY_NAME, position.toString());

		updateButtonVisibility();
	});


	const isAtFirstPosition = () => {
		if (allowInfinite) {
			return false;
		}

		return position === 0;
	};

	const isAtLastPosition = () => {
		if (allowInfinite) {
			return false;
		}

		return position >= (slidesCount - 1);
	};

	const updateButtonVisibility = () => {
		$previousButton.classList.remove(CSS_BUTTON_HIDDEN_SELECTOR);
		$nextButton.classList.remove(CSS_BUTTON_HIDDEN_SELECTOR);

		if (isAtFirstPosition()) {
			$previousButton.classList.add(CSS_BUTTON_HIDDEN_SELECTOR);
		}

		if (isAtLastPosition()) {
			$nextButton.classList.add(CSS_BUTTON_HIDDEN_SELECTOR);
		}
	};


	// on init
	updateButtonVisibility();
});
