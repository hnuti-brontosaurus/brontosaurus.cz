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
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

    const mapEl = document.getElementById("map");
    const administrativeUnits = JSON.parse(mapEl.getAttribute('data-administrativeUnits'));

    const map = new Map(mapEl, {
        center: { lat: 49.000, lng: 16.000 },
        zoom: 8,
        mapId: "b80d048e42b74f71",
    });

    // todo bulk add?
    for (const unit of administrativeUnits) {
        const color = resolveColor(unit);
        const pinEl = new PinElement({
            background: color,
            glyphColor: color,
        });
        const marker = new AdvancedMarkerElement({
            map: map,
            position: { lat: unit.lat, lng: unit.lng },
            title: unit.name,
            content: pinEl.element,
        });
    }

    map.centerAndZoom();
});

function resolveColor(unit) {
    // todo true colors
    if (unit.isOfTypeClub) return "violet";
    if (unit.isOfTypeBase) return "orange";
    if (unit.isOfTypeRegional) return "blue";
    if (unit.isOfTypeOffice) return "var(--hb-colors-emphasizing)";
    if (unit.isOfTypeChildren) return "pink";
    throw new Error("Unsupported unit type");
}
