import { ElementsInView } from "./ElementsInView";

export class Position
{
	private currentPosition: number;

	constructor(
		private slidesCount: number,
		defaultPosition: number,
		private allowInfinite: boolean,
		private elementsInView: ElementsInView,
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


		/**
		 * slides	at once		last position
		 * 5		1			4
		 * 5		2			3
		 * 5		3			2
		 */
		const number = this.elementsInView.count();
		let lastPosition;
		// todo make an expression instead of hardcoding
		switch (number) {
			case 1:
				lastPosition = 4;
				break;
			case 2:
				lastPosition = 3
				break;
			case 3:
				lastPosition = 2
				break;
			default:
				throw new Error('Unsupported number of slides at once');
		}

		return this.currentPosition >= lastPosition;
	}
}
