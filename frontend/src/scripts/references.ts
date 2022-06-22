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
	dots.repaint(defaultPosition);
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

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	public moveToPrevious(): void
	{
		if (this.currentPosition === 0) {
			this.currentPosition = this.slidesCount - 1;
		} else {
			this.currentPosition--;
		}

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	public moveTo(position: number): void
	{
		if (position < 0 || position > (this.slidesCount - 1)) {
			throw new Error('Position out of range');
		}

		this.currentPosition = position;

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	private positionChangedSubscribers: ((newPosition: number) => void)[] = [];
	public addPositionChangedSubscriber(callback: (newPosition: number) => void): void
	{
		this.positionChangedSubscribers.push(callback);
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


const selectors = {
	DOTS: 'references__dots',
	DOT: 'references__dot',
	DOT_ACTIVE: 'references__dot--active',
}

class Dots
{
	private dots: Dot[] = [];

	constructor(
		dataStorageEl: HTMLElement,
		private position: Position,
		totalSlides: number,
	)
	{
		const containerEl = document.createElement('div');
		containerEl.classList.add(selectors.DOTS);

		for (let i = 0; i < totalSlides; i++) {
			this.dots.push(new Dot(containerEl, this.position, i));
		}

		dataStorageEl.insertAdjacentElement("afterend", containerEl);
	}

	public repaint(newPosition: number): void
	{
		this.dots.forEach(dot => dot.repaint(newPosition));
	}
}

class Dot
{
	private readonly el: HTMLElement;

	constructor(
		containerEl: HTMLElement,
		private position: Position,
		private offset: number,
	) {
		this.el = document.createElement('div');
		this.el.classList.add(selectors.DOT);
		containerEl.appendChild(this.el);

		this.el.addEventListener('click', () => position.moveTo(this.offset));
	}


	public repaint(newPosition: number): void
	{
		if (this.offset === newPosition) {
			this.el.classList.add(selectors.DOT_ACTIVE);
		} else {
			this.el.classList.remove(selectors.DOT_ACTIVE);
		}
	}

}
