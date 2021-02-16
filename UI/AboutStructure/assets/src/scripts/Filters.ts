import Map from './Map';

export default class Filters {
	private /* const */ ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR = 'about-structure-map-filters-item--active';

	private items: NodeListOf<Element>;
	private mapInstance: Map;

	constructor(mapInstance: Map, itemElements: NodeListOf<Element>) {
		this.items = itemElements;
		this.mapInstance = mapInstance;

		this.attachListeners();
	}


	public displayAll() {
		this.updateFilterActiveState(document.getElementById('mapa-vse')!); // map exists so this should be present as well
		this.mapInstance.displayLayer();
	}

	public displayLayer(item: HTMLElement) {
		this.updateFilterActiveState(item);
		this.mapInstance.displayLayer(item.dataset.slug);
	}


	private attachListeners(): void {
		this.items.forEach((item: Element): void => {
			item.addEventListener('click', (event: Event) => {
				window.history.pushState(null, '', item.children[0].getAttribute('href'));
				event.preventDefault();

				this.displayLayer(item as HTMLElement);
			});
		});
	}

	private updateFilterActiveState(activeItem: HTMLElement): void {
		// remove active class from all items
		this.items.forEach((item: Element) => {
			item.classList.remove(this.ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR);
		});

		// add active class to item, which was click
		activeItem.classList.add(this.ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR);
	}

}
