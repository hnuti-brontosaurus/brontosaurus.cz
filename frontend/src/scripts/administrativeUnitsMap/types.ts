export interface OrganizationalUnit
{
	name: string;
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
