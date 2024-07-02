import {Dot} from './Dot';
import {Position} from './Position';
import {selectors} from './selectors';

interface Dots {
	repaint(newPosition: number): void,
}

export class DotsFactory
{
	public static enabled(
		dataStorageEl: HTMLElement,
		position: Position,
		totalSlides: number,
	): OnePerSlide
	{
		return new OnePerSlide(dataStorageEl, position, totalSlides);
	}

	public static disabled(): Noop
	{
		return new Noop();
	}
}

class OnePerSlide implements Dots
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

/**
 * When no dots are needed
 */
class Noop implements Dots
{
	constructor()
	{}

	public repaint(newPosition: number): void
	{}
}