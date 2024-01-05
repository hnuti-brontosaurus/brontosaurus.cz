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
		const containerEl = document.createElement('div');
		containerEl.id = 'infowindow';
		containerEl.classList.add('administrativeUnitsMap__infoWindow')

		if (unit.image !== null) {
			const imageContainerEl = containerEl.appendChild(document.createElement('div'));
			imageContainerEl.classList.add('administrativeUnitsMap__infoWindowImageContainer');
			const imageEl = imageContainerEl.appendChild(document.createElement('img'));
			imageEl.classList.add('administrativeUnitsMap__infoWindowImage');
			imageEl.src = unit.image;
		}

		const contentEl = containerEl.appendChild(document.createElement('div'));
		const metaEl = contentEl.appendChild(document.createElement('div'));

		metaEl.innerHTML = resolveUnitTitle(unit);
		metaEl.innerHTML += `<br>Adresa: ${unit.address}`;

		if (unit.chairman !== null) {
			metaEl.innerHTML += `<br>Předseda: ${unit.chairman}`;
		}

		if (unit.website !== null ) {
			metaEl.innerHTML += `<br>Web: <a href="${unit.website}" target="_blank">${unit.website}</a>`
		}

		if (unit.email !== null) {
			metaEl.innerHTML += `<br>E-mail: <a href="mailto:${unit.email}">${unit.email}</a>`;
		}

		if (unit.description !== null) {
			const descriptionEl = contentEl.appendChild(document.createElement('p'));
			descriptionEl.classList.add('administrativeUnitsMap__infoWindowDescription')
			descriptionEl.innerHTML += unit.description;
		}

		return containerEl;
	}
}
