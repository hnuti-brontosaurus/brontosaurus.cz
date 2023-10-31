import {OrganizationalUnit} from './types';

export const resolveUnitTypeSlug = (unit: OrganizationalUnit): string|undefined => {
	let slug = null;

	switch (true) {
		case unit.isOfTypeClub:
			slug = 'club';
			break;

		case unit.isOfTypeBase:
			slug = 'base';
			break;

		case unit.isOfTypeRegional:
			slug = 'regional';
			break;

		case unit.isOfTypeOffice:
			slug = 'office';
			break;

		case unit.isOfTypeChildren:
			slug = 'children';
			break;
	}

	if (slug === null) { // no option selected, fall back to Google Maps default marker
		return;
	}

	return slug;
};

export const resolveIconFileName = function (unit: OrganizationalUnit): string {
	return 'icon-marker-' + resolveUnitTypeSlug(unit) + '.svg';
};

export const resolveUnitTypeLabel = function (unit: OrganizationalUnit): string|null {
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
};

export const resolveUnitTitle = (unit: OrganizationalUnit): string => {
	const unitType = resolveUnitTypeLabel(unit);
	return unit.name + (unitType !== null ? ' – ' + unitType : '');
};
