import {Position} from './Position';

export class Autoplay
{
	private intervalId: number|null = null;
	private seconds: number = 5;

	public constructor(
		private position: Position,
	) {}

	public enable(): void
	{
		this.intervalId = window.setInterval(
			() => this.position.moveToNext(),
			this.seconds * 1000,
		);
	}

	public disable(): void
	{
		if (this.intervalId === null) {
			return;
		}

		window.clearInterval(this.intervalId);
		this.intervalId = null;
	}
}
