import 'nodelist-foreach-polyfill';
// @ts-ignore
const loadGoogleMapsApi = require('load-google-maps-api'); // must be this way, because of exporting using `export =`
import Map from './Map';
import Filters from './Filters';

loadGoogleMapsApi({
	key: 'AIzaSyDsxejbWcsI1eb4eoQJq47Eq9qxvSCMXSc',
}).then(() => {
	initMap();

}).catch((error: any) => {
	console.error(error);
});

// Initialize and add the mapInstance
function initMap(): void {
	const mapElement = document.getElementById('map');
	if (mapElement === null) { // if no map, do not do anything
		return;
	}

	// but once there is map, all elements should be present

	const map = new Map(mapElement); // initialize map
	const filters = new Filters(map, document.querySelectorAll('.about-structure-map-filters-item')); // initialize filters (listen to click events)

	const unitBaseLinkElement = document.getElementById('about-structure-unit-base-link');
	if (unitBaseLinkElement !== null) {
		unitBaseLinkElement.addEventListener('click', () => { // listen to base unit link as well
			filters.displayLayer(document.getElementById('mapa-zakladni-clanky')!); // map exists so this should be present as well
		});
	}

	window.addEventListener('load', () => { // finally once the page is loaded, check if a layer filter should be activated
		const hash = window.location.hash.substr(1);

		const selectedFilterLinkElement = document.getElementById(hash);
		if (selectedFilterLinkElement !== null) { // no filtering element with given hash found => do not filter
			filters.displayLayer(selectedFilterLinkElement);
		}
		else {
			filters.displayAll();
		}
	});
}
