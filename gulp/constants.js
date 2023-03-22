export const ENV_PRODUCTION = 'production';

const locations = {
	global: 'frontend',
	detail: 'UI/Event/assets',
	contacts: 'UI/Contacts/assets',
	rentals: 'UI/Rentals/assets',
};

export const paths = {
	images: {
		global: {
			dist: locations.global + '/dist/images',
			src: locations.global + '/src/images',
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
