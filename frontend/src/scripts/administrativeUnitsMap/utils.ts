import {OrganizationalUnit} from './types';

export function resolveUnitTypeSlug(unit: OrganizationalUnit): string | undefined
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

export function resolveIconFileName(unit: OrganizationalUnit): string
{
	return `icon-marker-${resolveUnitTypeSlug(unit)}.svg`;
}

export function resolveUnitTitle(unit: OrganizationalUnit): string
{
	const type = resolveUnitTypeLabel(unit);
	if (type === null) {
		return unit.name;
	}

	return `${unit.name} – ${type}`;
}

function resolveUnitTypeLabel(unit: OrganizationalUnit): string|null
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
