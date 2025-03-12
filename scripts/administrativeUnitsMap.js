/**
 * administrative unit schema:
{
	name: string;
	description: string|null,
	image: string|null,
	lat: number;
	lng: number;
	address: string;
	chairman: string|null;
	website: string|null;
	email: string|null;
	isOfTypeClub: boolean;
	isOfTypeBase: boolean;
	isOfTypeRegional: boolean;
	isOfTypeOffice: boolean;
	isOfTypeChildren: boolean;
}
*/

document.addEventListener('DOMContentLoaded', async function () {
    const mapEl = document.getElementById("map");
    if (mapEl === null) return;

    const map = await initializeMap(mapEl);
    const filters = initializeFilters(map);

    const getSelectedFilter = () =>{
        const hash = window.location.hash.substring(1);
		return hash !== ''
			? document.querySelector(`.administrativeUnitsMap__filters #${hash}`)
			: null;
    }

	window.addEventListener('load', () => {
		const selectedEl = getSelectedFilter();
		if (selectedEl !== null) {
			filters.displayLayer(selectedEl);

		} else {
			filters.displayAll();
		}
	});

    window.addEventListener('hashchange', () => {
		const selectedEl = getSelectedFilter();
		if (selectedEl === null) {
			return;
		}

        filters.displayLayer(selectedEl);
	});
});


/* MAP */

async function initializeMap(mapEl)
{
    const { Map, Data } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

    const administrativeUnits = JSON.parse(mapEl.getAttribute('data-administrativeUnits'));

    const map = new Map(mapEl, {
        center: { lat: 49.7437572, lng: 15.3386383 }, // Czechia geographic center, see https://en.mapy.cz/s/gupehogeha
        zoom: 7,
        mapId: "b80d048e42b74f71",
    });
    const bounds = new google.maps.LatLngBounds();
    let currentInfoWindow;

    const slugs = [];
    const layersObj = {};

    for (const unit of administrativeUnits) {

        // collect slugs
        const slug = resolveUnitTypeSlug(unit);
        if ( !! slug && ! slugs.includes(slug)) {
            slugs.push(slug);
        }

        // customize pin style
        const color = resolveColor(unit);
        const pinEl = new PinElement({
            background: color,
            borderColor: "#fff",
            glyphColor: color,
        });

        // set marker
        const coords = { lat: unit.lat, lng: unit.lng };
        const marker = new AdvancedMarkerElement({
            map: map,
            position: coords,
            title: unit.name,
            content: pinEl.element,
        });
        bounds.extend(new google.maps.LatLng(unit.lat, unit.lng));

        // create info window
        const infoWindow = new google.maps.InfoWindow({
            content: buildInfoWindow(unit).outerHTML,
            ariaLabel: unit.name,
        });
        marker.addListener('click', () => {
            currentInfoWindow?.close();
            currentInfoWindow = infoWindow;

            infoWindow.open({
                anchor: marker,
                map,
            })
        });

        // add to layer
        if ( ! layersObj.hasOwnProperty(slug)) {
            layersObj[slug] = [];
        }
        layersObj[slug].push(marker);
    }

    map.fitBounds(bounds);

    return {
        displayLayer(filterSlug) {
            const allVisible = typeof filterSlug === 'undefined';
            slugs.forEach(slug => {
                layersObj[slug].forEach(marker => marker.setMap(allVisible || slug === filterSlug ? map : null));
            });
        },
    };
}

function resolveColor(unit)
{
    if (unit.isOfTypeClub) return "#9C0CBE";
    if (unit.isOfTypeBase) return "#FF8F00";
    if (unit.isOfTypeRegional) return "#009BF1";
    if (unit.isOfTypeOffice) return "#00A651";
    if (unit.isOfTypeChildren) return "#D1015F";
    throw new Error("Unsupported unit type");
}


function buildInfoWindow(unit)
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

function resolveUnitTitle(unit)
{
	const type = resolveUnitTypeLabel(unit);
	if (type === null) {
		return unit.name;
	}

	return `${unit.name} – ${type}`;
}

function resolveUnitTypeLabel(unit)
{
	switch (true) {
		case unit.isOfTypeClub:
			return 'klub';

		case unit.isOfTypeBase:
			return 'základní článek';

		case unit.isOfTypeRegional:
			return 'regionální centrum';

		case unit.isOfTypeOffice:
			return 'ústředí';

		case unit.isOfTypeChildren:
			return 'dětský oddíl';
	}

	return null;
}

function resolveUnitTypeSlug(unit)
{
	if (unit.isOfTypeClub) {
		return 'club';

	} else if (unit.isOfTypeBase) {
		return 'base';

	} else if (unit.isOfTypeRegional) {
		return 'regional';

	} else if (unit.isOfTypeOffice) {
		return 'office';

	} else if (unit.isOfTypeChildren) {
		return 'children';

	} else { // no option selected, fall back to Google Maps default marker
		return;
	}
}


/* FILTERS */

function initializeFilters({displayLayer})
{
    const ActiveFilterSelector = 'administrativeUnitsMap__filter--active';

    const filters = document.querySelectorAll('.administrativeUnitsMap__filter');

    const makeActive = (el) => {
		filters.forEach(el => el.classList.remove(ActiveFilterSelector));
		el.classList.add(ActiveFilterSelector);
	}

    // initialize filters (listen to click events)
    filters.forEach(el =>
        el.addEventListener('click', ev => {
            window.history.pushState(null, '', el.children[0].getAttribute('href'));
            ev.preventDefault();

            makeActive(el);
            displayLayer(el.dataset.slug);
        }));

    return {
        displayAll: () => {
            makeActive(document.getElementById('mapa-vse'));
            displayLayer();
        },
        displayLayer: (el) => {
            makeActive(el);
            displayLayer(el.dataset.slug);
        },
    };
}
