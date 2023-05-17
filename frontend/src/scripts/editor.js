// overwrites core/heading block to be aligned to center by default
// source https://stackoverflow.com/a/73676310
wp.blocks.registerBlockVariation('core/heading', {
	name: 'heading-center',
	title: 'Nadpis',
	isDefault: true,
	attributes: {textAlign: 'center'},
});
