const {createHigherOrderComponent} = wp.compose;
const {addFilter} = wp.hooks;

addFilter('blocks.registerBlockType', 'hb/theme/imageRoundedCorners', (settings, name) => {
	/**
	 * Rewrites styles for image – rounded by default, optionally can be sharp-edge (no rounding)
	 *
	 * resources:
	 * - https://wordpress.stackexchange.com/questions/367124/how-to-add-extra-option-to-image-block-settings
	 * - https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/
	 */
	if (name === 'core/image') {
		settings.styles = [
			{
				name: 'rounded',
				label: 'Zaoblený',
				isDefault: true,
			},
			{
				name: 'sharp',
				label: 'Bez zaoblení',
			},
		];
	}

	return settings;
});
