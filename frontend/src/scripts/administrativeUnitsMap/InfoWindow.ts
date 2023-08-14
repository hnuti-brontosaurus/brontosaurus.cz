import {resolveUnitTitle, resolveUnitTypeLabel} from "./utils";
import {OrganizationalUnit} from "./organizationalUnit";


export class InfoWindow {
	infoWindow: google.maps.InfoWindow;
	mapInstance: google.maps.Map;

	constructor(mapInstance: google.maps.Map) {
		// We want only one instance of InfoWindow due to closing previous opened windows
		this.infoWindow = new google.maps.InfoWindow();
		this.mapInstance = mapInstance;
	}

	display(unit: OrganizationalUnit, marker: google.maps.Marker): void {
		this.infoWindow.close(); // Close previously opened infowindow
		this.infoWindow.setContent(InfoWindow.buildContent(unit)); // set content of info window
		this.infoWindow.open(this.mapInstance, marker); // open window, which was clicked by user
	}

	static buildContent(unit: OrganizationalUnit): HTMLDivElement {
		const unitType = resolveUnitTypeLabel(unit);

		// Make string for content of info window
		const contentElement = document.createElement('div');
		contentElement.id = 'infowindow';
		contentElement.innerHTML = resolveUnitTitle(unit);

		contentElement.innerHTML += "<br>";
		contentElement.innerHTML += `Adresa: ${unit.address}`;
		if (unit.chairman !== null) {
			contentElement.innerHTML += `<br>Předseda: ${unit.chairman}`;
		}

		if (unit.website !== null ) {
			contentElement.innerHTML += `<br>Web: <a href="${unit.website}" target="_blank">${unit.website}</a>`
		}

		if (unit.email !== null) {
			contentElement.innerHTML += `<br>E-mail: <a href="mailto:${unit.email}">${unit.email}</a>`;
		}

		return contentElement;
	}
}
