import Map from './Map';

export default class Filters {
	private /* const */ ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR = 'administrativeUnitsMap__filter--active';

	public constructor(
		private mapInstance: Map,
		private items: NodeListOf<Element>,
	) {
		this.attachListeners();
	}


	public displayAll()
	{
		this.updateFilterActiveState(document.getElementById('mapa-vse')!); // map exists so this should be present as well
		this.mapInstance.displayLayer();
	}

	public displayLayer(item: HTMLElement)
	{
		this.updateFilterActiveState(item);
		this.mapInstance.displayLayer(item.dataset.slug);
	}


	private attachListeners(): void
	{
		this.items.forEach(item =>
			item.addEventListener('click', ev => {
				window.history.pushState(null, '', item.children[0].getAttribute('href'));
				ev.preventDefault();

				this.displayLayer(item as HTMLElement);
			}));
	}

	private updateFilterActiveState(activeItem: HTMLElement): void
	{
		this.items.forEach(item =>
			item.classList.remove(this.ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR));

		activeItem.classList.add(this.ACTIVE_FILTER_ITEM_CSS_CLASS_SELECTOR);
	}

}
