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
	const defaultPosition = parseInt(window.getComputedStyle($dataStorage).getPropertyValue(CSS_PROPERTY_NAME));
	const allowInfinite = typeof $dataStorage.dataset.carouselInfinite !== 'undefined';

	const position = new Position(slidesCount, defaultPosition, allowInfinite);
	position.addMoveToPreviousSubscriber((newPosition) => {
		$dataStorage.style.setProperty(CSS_PROPERTY_NAME, newPosition.toString());
		updateButtonVisibility();
	});
	position.addMoveToNextSubscriber((newPosition) => {
		$dataStorage.style.setProperty(CSS_PROPERTY_NAME, newPosition.toString());
		updateButtonVisibility();
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
		$previousButton.classList.remove(CSS_BUTTON_HIDDEN_SELECTOR);
		$nextButton.classList.remove(CSS_BUTTON_HIDDEN_SELECTOR);

		if (position.isAtFirst()) {
			$previousButton.classList.add(CSS_BUTTON_HIDDEN_SELECTOR);
		}

		if (position.isAtLast()) {
			$nextButton.classList.add(CSS_BUTTON_HIDDEN_SELECTOR);
		}
	};


	// on init
	updateButtonVisibility();
});


class Position
{
	private currentPosition: number;

	constructor(
		private slidesCount: number,
		defaultPosition: number,
		private allowInfinite: boolean,
	)
	{
		this.currentPosition = defaultPosition;
	}

	public moveToNext(): void
	{
		if (this.currentPosition === (this.slidesCount - 1)) {
			this.currentPosition = 0;
		} else {
			this.currentPosition++;
		}

		this.moveToNextSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	private moveToNextSubscribers: ((newPosition: number) => void)[] = [];
	public addMoveToNextSubscriber(callback: (newPosition: number) => void): void
	{
		this.moveToNextSubscribers.push(callback);
	}

	public moveToPrevious(): void
	{
		if (this.currentPosition === 0) {
			this.currentPosition = this.slidesCount - 1;
		} else {
			this.currentPosition--;
		}

		this.moveToPreviousSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	private moveToPreviousSubscribers: ((newPosition: number) => void)[] = [];
	public addMoveToPreviousSubscriber(callback: (newPosition: number) => void): void
	{
		this.moveToPreviousSubscribers.push(callback);
	}

	public isAtFirst(): boolean
	{
		if (this.allowInfinite) {
			return false;
		}

		return this.currentPosition === 0;
	}

	public isAtLast(): boolean
	{
		if (this.allowInfinite) {
			return false;
		}

		return this.currentPosition >= (this.slidesCount - 1);
	}
}
