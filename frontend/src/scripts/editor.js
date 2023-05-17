// overwrites core/heading block to be aligned to center by default
wp.blocks.registerBlockVariation('core/heading', {
	name: 'heading-center',
	title: 'Nadpis',
	isDefault: true,
	attributes: {textAlign: 'center'},
});
