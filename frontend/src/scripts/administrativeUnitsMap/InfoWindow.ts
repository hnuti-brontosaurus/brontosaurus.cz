import {resolveUnitTitle} from './utils';
import {OrganizationalUnit} from './types';


export class InfoWindow
{
	infoWindow: google.maps.InfoWindow;

	public constructor(
		private readonly mapInstance: google.maps.Map,
	)
	{
		// We want only one instance of InfoWindow due to closing previous opened windows
		this.infoWindow = new google.maps.InfoWindow();
	}

	public display(unit: OrganizationalUnit, marker: google.maps.Marker): void
	{
		this.infoWindow.close(); // Close previously opened infowindow
		this.infoWindow.setContent(InfoWindow.buildContent(unit)); // set content of info window
		this.infoWindow.open(this.mapInstance, marker); // open window, which was clicked by user
	}

	private static buildContent(unit: OrganizationalUnit): HTMLDivElement
	{
		// Make string for content of info window
		const contentEl = document.createElement('div');
		contentEl.id = 'infowindow';

		contentEl.innerHTML = resolveUnitTitle(unit);
		contentEl.innerHTML += `<br>Adresa: ${unit.address}`;

		if (unit.chairman !== null) {
			contentEl.innerHTML += `<br>Předseda: ${unit.chairman}`;
		}

		if (unit.website !== null ) {
			contentEl.innerHTML += `<br>Web: <a href="${unit.website}" target="_blank">${unit.website}</a>`
		}

		if (unit.email !== null) {
			contentEl.innerHTML += `<br>E-mail: <a href="mailto:${unit.email}">${unit.email}</a>`;
		}

		return contentEl;
	}
}
