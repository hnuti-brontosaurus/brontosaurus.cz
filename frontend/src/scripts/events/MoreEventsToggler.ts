import CollapsingHandler from "./CollapsingHandler";

export default class {
	private showMoreButtonElement: HTMLElement;
	private collapsingHandler: CollapsingHandler;

	constructor(showMoreButtonElement: HTMLElement, showMoreContentElement: HTMLElement) {
		this.showMoreButtonElement = showMoreButtonElement;

		this.collapsingHandler = new CollapsingHandler(
			showMoreButtonElement,
			showMoreContentElement,
			showMoreContentElement,
			{
				opened: 'events-event-wrapper--opened',
			},
		);
	}

	public activate() {
		this.collapsingHandler.enable();
		this.showMoreButtonElement.addEventListener('click', (): void => {
			this.collapsingHandler.disable();
			this.showMoreButtonElement.remove();
		});
	}
}
