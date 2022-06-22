import {Position} from './Position';
import {selectors} from './selectors';

export class Dot
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
