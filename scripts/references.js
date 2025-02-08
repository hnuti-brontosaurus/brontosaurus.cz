const selectors = {
	BUTTON_HIDDEN: 'references__button--hidden',
	DOTS: 'references__dots',
	DOT: 'references__dot',
	DOT_ACTIVE: 'references__dot--active',
}

document.addEventListener('DOMContentLoaded', () =>
	document.querySelectorAll('[data-references]')
		.forEach(el => initialize(el)));

function initialize(rootEl) {
	const CarousePositionPropertyName = '--carouselPosition';

	const slidesEl = rootEl.querySelector('[data-references-slides]');
	const previousButtonEl = rootEl.querySelector('[data-references-button="previous"]');
	const nextButtonEl = rootEl.querySelector('[data-references-button="next"]');
	if (slidesEl === null || previousButtonEl === null || nextButtonEl === null) {
		return;
	}

	const slidesCount = slidesEl.children.length;
	const defaultPosition = parseInt(window.getComputedStyle(slidesEl).getPropertyValue(CarousePositionPropertyName));
	const allowInfinite = typeof rootEl.dataset.referencesInfinite !== 'undefined';
	const allowAutoplay = typeof rootEl.dataset.referencesAutoplay !== 'undefined';
	const noDots = typeof rootEl.dataset.referencesNoDots !== 'undefined';

	const elementsInView = new ElementsInView(slidesEl);
	const position = new Position(slidesCount, defaultPosition, allowInfinite, elementsInView);
	const dots = noDots
		? DotsFactory.disabled() // todo listen to elements in view, display only if count == 1
		: DotsFactory.enabled(slidesEl, position, slidesCount);
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

	// todo: resize, orientationchange


	// on init
	updateButtonVisibility();
	dots.repaint(defaultPosition);

	if (allowAutoplay) {
		autoplay.enable();
	}
}


/* ELEMENTS IN VIEW */

class ElementsInView {

    /** @private */
    slidesEl;

    constructor(slidesEl) {
        this.slidesEl = slidesEl;
    }

    count() {
        const containerWidth = this.slidesEl.getBoundingClientRect().width;
        const slideWidth = this.slidesEl.children[0].getBoundingClientRect().width;
        const count = Math.floor(containerWidth / slideWidth);
        return count;
    }

}


/* POSITION */

class Position {
    /** @private */
    slidesCount; // number
	currentPosition; // number
    /** @private */
    allowInfinite; // boolean
    /** @private */
    elementsInView;

	constructor(slidesCount, defaultPosition, allowInfinite, elementsInView) {
        this.slidesCount = slidesCount;
		this.currentPosition = defaultPosition;
        this.allowInfinite = allowInfinite;
        this.elementsInView = elementsInView;
	}

	moveToNext() {
		if (this.currentPosition === this.getLastPosition()) {
			this.currentPosition = 0;
		} else {
			this.currentPosition++;
		}

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	moveToPrevious() {
		if (this.currentPosition === 0) {
			this.currentPosition = this.getLastPosition();
		} else {
			this.currentPosition--;
		}

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	moveTo(position) {
		if (position < 0 || position > (this.slidesCount - 1)) {
			throw new Error('Position out of range');
		}

		const lastPosition = this.getLastPosition()
		this.currentPosition = position > lastPosition
			? lastPosition
			: position;

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

    /** @private */
	positionChangedSubscribers = [];

	addPositionChangedSubscriber(callback) {
		this.positionChangedSubscribers.push(callback);
	}

	isAtFirst() {
		if (this.allowInfinite) {
			return false;
		}

		return this.currentPosition === 0;
	}

	isAtLast() {
		if (this.allowInfinite) {
			return false;
		}

		const lastPosition = this.getLastPosition();
		return this.currentPosition >= lastPosition;
	}

    /** @private */
	getLastPosition()
	{
		/**
		 * slides	at once		last position
		 * 5		1			4
		 * 5		2			3
		 * 5		3			2
		 */
		const number = this.elementsInView.count();
		// todo make an expression instead of hardcoding
		switch (number) {
			case 1:
				return 4;

			case 2:
				return 3;

			case 3:
				return 2;

			default:
				throw new Error('Unsupported count of slides displayed at once');
		}
	}
}


/* DOTS */

/**
@interface Dots {
	repaint(newPosition: number): void,
}
*/

class DotsFactory
{
	static enabled(dataStorageEl, position, totalSlides) {
		return new OnePerSlide(dataStorageEl, position, totalSlides);
	}

	static disabled() {
		return new Noop();
	}
}

/** @implements Dots */
class OnePerSlide {
    /** @private */
	dots = [];
    /** @private */
    position;

	constructor(dataStorageEl, position, totalSlides) {
        this.position = position;

		const containerEl = document.createElement('div');
		containerEl.classList.add(selectors.DOTS);

		for (let i = 0; i < totalSlides; i++) {
			this.dots.push(new Dot(containerEl, this.position, i));
		}

		dataStorageEl.insertAdjacentElement("afterend", containerEl);
	}

	repaint(newPosition) {
		this.dots.forEach(dot => dot.repaint(newPosition));
	}
}

/**
 * When no dots are needed
 * @implements Dots
 */
class Noop {
	constructor() {}
	repaint(newPosition) {}
}

class Dot
{
    /** @private @readonly */
	el;
    /** @private */
    position;
    /** @private */
    offset;

	constructor(containerEl, position, offset) {
        this.position = position;
        this.offset = offset;

		this.el = document.createElement('div');
		this.el.classList.add(selectors.DOT);
		containerEl.appendChild(this.el);

		this.el.addEventListener('click', () => position.moveTo(this.offset));
	}

	repaint(newPosition) {
		if (this.offset === newPosition) {
			this.el.classList.add(selectors.DOT_ACTIVE);
		} else {
			this.el.classList.remove(selectors.DOT_ACTIVE);
		}
	}
}


/* AUTOPLAY */

class Autoplay {
    /** @private */
	intervalId = null;
    /** @private */
	seconds = 5;
    /** @private */
    position;

	constructor(position) {
        this.position = position;
    }

	enable() {
		this.intervalId = window.setInterval(
			() => this.position.moveToNext(),
			this.seconds * 1000,
		);
	}

	disable() {
		if (this.intervalId === null) {
			return;
		}

		window.clearInterval(this.intervalId);
		this.intervalId = null;
	}
}
