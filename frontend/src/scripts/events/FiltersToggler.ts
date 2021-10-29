import CollapsingHandler from "./CollapsingHandler";

export default class {
	private collapsingHandler: CollapsingHandler;

	constructor(togglerElement: HTMLElement, stateElement: HTMLElement, animatedElement: HTMLElement) {
		this.collapsingHandler = new CollapsingHandler(
			togglerElement,
			stateElement,
			animatedElement,
			{
				opened: 'filters--opened',
			},
		);
	}

	activate(): void {
		this.collapsingHandler.enable();
	}
}
