import {InfoWindow} from './InfoWindow';
import {resolveIconFileName, resolveUnitTitle, resolveUnitTypeSlug} from './utils';
import {OverlappingMarkerSpiderfier} from 'ts-overlapping-marker-spiderfier';
import {OrganizationalUnit} from './types';

export default class Map {
	private readonly iconsPath: string;
	map: google.maps.Map;
	mapLayers: google.maps.MVCObject;
	mapBounds: google.maps.LatLngBounds;
	infoWindow: InfoWindow;
	markerCluster: OverlappingMarkerSpiderfier;
	slugs: Array<string>;
	organizationalUnits: Array<OrganizationalUnit>;

	public constructor(mapElement: HTMLElement)
	{
		this.iconsPath = mapElement.dataset.themepath! + '/frontend/dist/images'; // should be present

		// Maps Google map to place where we want display our map
		this.map = new google.maps.Map(mapElement);

		// For layers by type, @source: https://stackoverflow.com/a/23036174
		this.mapLayers = new google.maps.MVCObject();

		// Let's center and zoom map automatically @source:https://coderwall.com/p/hojgtq/auto-center-and-auto-zoom-a-google-map
		this.mapBounds = new google.maps.LatLngBounds();

		this.infoWindow = new InfoWindow(this.map);

		this.markerCluster = new OverlappingMarkerSpiderfier(this.map);

		this.slugs = [];

		const organizationalUnitsInJSON = mapElement.getAttribute('data-organizationalUnits');
		if (organizationalUnitsInJSON === null) {
			throw new Error('Organizational units has not been passed.');
		}
		this.organizationalUnits = JSON.parse(organizationalUnitsInJSON);

		this.placeMarkers();

		// Centering and zooming of map
		this.centerAndZoom();
	}


	// place markers and set layers by type for filter
	public placeMarkers(): void
	{
		this.organizationalUnits.forEach((unit) => {
			const slug = resolveUnitTypeSlug(unit);

			if (slug !== undefined && this.slugs.indexOf(slug) === -1) {
				this.slugs.push(slug);
				this.mapLayers.set(slug, this.map);
			}

			this.placeMarker(unit);
		});
	}

	// Inspiration from: https://stackoverflow.com/a/30013345
	private placeMarker(unit: OrganizationalUnit): void
	{
		this.mapBounds.extend({lat: unit.lat, lng: unit.lng});

		// make marker and set option
		const marker = new google.maps.Marker({
			position: {lat: unit.lat, lng: unit.lng},
			map: this.map,
			title: resolveUnitTitle(unit),
			icon: this.iconsPath + '/' + resolveIconFileName(unit),
		});

		// Bind marker.map to the specific slug property of unitTypesLayers which is set and unset below [1]
		marker.bindTo('map', this.mapLayers, resolveUnitTypeSlug(unit));

		// Add spider listener for opening and closing info window
		this.markerCluster.addMarker(marker, () => {
			this.infoWindow.display(unit, marker);
		});
	}

	public displayLayer(filter: string|null = null): void
	{
		this.slugs.forEach((slug) => {
			this.mapLayers.set(slug, slug === filter || filter === null ? this.map : null);
		});
		this.centerAndZoom();
	}

	public centerAndZoom(): void
	{
		this.map.fitBounds(this.mapBounds);
		this.map.panToBounds(this.mapBounds);
	}
}
