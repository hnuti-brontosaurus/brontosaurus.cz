interface StateSelectors {
	opened: string,
}

export default class {
	private togglerElement: HTMLElement;
	private stateElement: HTMLElement;
	private animatedElement: HTMLElement;
	private stateSelectors: StateSelectors;

	constructor(togglerElement: HTMLElement, stateElement: HTMLElement, animatedElement: HTMLElement, stateSelectors: StateSelectors) {
		this.togglerElement = togglerElement;
		this.stateElement = stateElement;
		this.animatedElement = animatedElement;
		this.stateSelectors = stateSelectors;
	}

	enable(): void {
		this.registerToggleListener();
		this.registerResizeListener();

		if (this.stateElement.dataset.collapseOpenedonload) {
			this.openCollapsingElement();
		}
	}

	disable(): void {
		this.unregisterToggleListener();
		this.unregisterResizeListener();
	}


	// toggling

	private openCollapsingElement(): void {
		this.stateElement.classList.add(this.stateSelectors.opened);
		this.updateElementHeight();
	}

	private closeCollapsingElement(): void {
		this.stateElement.classList.remove(this.stateSelectors.opened);
		this.animatedElement.style.removeProperty('--height');
	}

	private registerToggleListener(): void {
		this.togglerElement.addEventListener('click', this.repaint.bind(this));
	};

	private unregisterToggleListener(): void {
		this.togglerElement.removeEventListener('click', () => {});
	}

	private repaint(): void {
		if (this.isOpened()) {
			this.closeCollapsingElement();

		} else {
			this.openCollapsingElement();

		}
	};


	// resize listener

	private onResize(): void {
		if (this.isOpened()) {
			this.updateElementHeight();
		}
	}

	private registerResizeListener(): void {
		window.addEventListener('resize', this.onResize.bind(this));
	};

	private unregisterResizeListener(): void {
		window.removeEventListener('resize', () => {});
	}



	// helpers

	private isOpened(): boolean {
		return this.stateElement.classList.contains(this.stateSelectors.opened);
	}

	private updateElementHeight(): void {
		this.animatedElement.style.setProperty('--height', this.getElementRealHeight() + 'px');
	}

	private getElementRealHeight(): number {
		const paddingTop = parseInt(window.getComputedStyle(this.animatedElement).paddingTop!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
		const paddingBottom = parseInt(window.getComputedStyle(this.animatedElement).paddingBottom!); // there should be always something; parseInt() strips `px` from the end of the string and removes floating stuff as well
		return this.animatedElement.scrollHeight + paddingTop + paddingBottom;
	};

}
