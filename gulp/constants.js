export const ENV_PRODUCTION = 'production';

const locations = {
	global: 'frontend',
	structure: 'UI/AboutStructure/assets',
	detail: 'UI/EventDetail/assets',
	contacts: 'UI/Contacts/assets',
	rentals: 'UI/Rentals/assets',
};

export const paths = {
	images: {
		global: {
			dist: locations.global + '/dist/images',
			src: locations.global + '/src/images',
		},
		structure: {
			dist: locations.structure + '/dist/images',
			src: locations.structure + '/src/images',
		},
		contacts: {
			dist: locations.contacts + '/dist/images',
			src: locations.contacts + '/src/images',
		},
		rentals: {
			dist: locations.rentals + '/dist/images',
			src: locations.rentals + '/src/images',
		}
	},

	scripts: {
		global: {
			dist: locations.global + '/dist/js',
			src: locations.global + '/src/scripts',
		},
		structure: {
			dist: locations.structure + '/dist/js',
			src: locations.structure + '/src/scripts',
		},
		detail: {
			dist: locations.detail + '/dist/js',
			src: locations.detail + '/src/scripts',
		}
	},

	styles: {
		global: {
			dist: locations.global + '/dist/css',
			src: locations.global + '/src/styles',
		},
	},

	webfonts: {
		global: {
			dist: locations.global + '/dist/webfonts',
			src: locations.global + '/src/webfonts',
		},
	},
};
