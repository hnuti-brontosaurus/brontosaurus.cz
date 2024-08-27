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
		if (this.currentPosition === this.getLastPosition()) {
			this.currentPosition = 0;
		} else {
			this.currentPosition++;
		}

		this.positionChangedSubscribers.forEach(subscriber => subscriber(this.currentPosition));
	}

	public moveToPrevious(): void
	{
		if (this.currentPosition === 0) {
			this.currentPosition = this.getLastPosition();
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

		const lastPosition = this.getLastPosition()
		this.currentPosition = position > lastPosition
			? lastPosition
			: position;

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

		const lastPosition = this.getLastPosition();
		return this.currentPosition >= lastPosition;
	}

	private getLastPosition(): number
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
