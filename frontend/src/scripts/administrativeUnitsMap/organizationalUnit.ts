export interface OrganizationalUnit {
	name: string;
	lat: number;
	lng: number;
	address: {
		street: string;
		postCode: string;
		city: string;
	};
	chairman: string;
	website: string;
	email: string;
	isOfTypeClub: boolean;
	isOfTypeBase: boolean;
	isOfTypeRegional: boolean;
	isOfTypeOffice: boolean;
	isOfTypeChildren: boolean;
}
