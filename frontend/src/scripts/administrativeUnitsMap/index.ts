// @ts-ignore
const loadGoogleMapsApi = require('load-google-maps-api'); // must be this way, because of exporting using `export =`
import Map from './Map';
import Filters from './Filters';

document.addEventListener('DOMContentLoaded', async () => {
	try {
		await loadGoogleMapsApi({key: 'AIzaSyDsxejbWcsI1eb4eoQJq47Eq9qxvSCMXSc'});
		initialize();

	} catch (e) {
		console.error(e);
	}
});

// Initialize and add the mapInstance
function initialize(): void
{
	const mapEl = document.getElementById('map');
	if (mapEl === null) { // don't do anything if no map
		return;
	}

	const map = new Map(mapEl); // initialize map
	const filters = new Filters(map, document.querySelectorAll('.administrativeUnitsMap__filter')); // initialize filters (listen to click events)

	// custom behavior for about-structure page
	// ideally, administrativeUnitsMap should export API, but there's no time play with it now
	const unitBaseLinkEl = document.getElementById('about-structure-unit-base-link');
	if (unitBaseLinkEl !== null) {
		unitBaseLinkEl.addEventListener('click', _ => // listen to base unit link as well
			filters.displayLayer(document.getElementById('mapa-zakladni-clanky')!)); // map exists so this should be present as well
	}

	window.addEventListener('load', () => { // finally once the page is loaded, check if a layer filter should be activated
		const hash = window.location.hash.substring(1);

		const selectedFilterLinkEl = hash !== ''
			? document.querySelector<HTMLElement>(`.administrativeUnitsMap__filters #${hash}`)
			: null;

		if (selectedFilterLinkEl !== null) { // no filtering element with given hash found => do not filter
			filters.displayLayer(selectedFilterLinkEl);

		} else {
			filters.displayAll();
		}
	});
}
