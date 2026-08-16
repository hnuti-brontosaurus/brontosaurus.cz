const {createHigherOrderComponent} = wp.compose;
const {addFilter} = wp.hooks;
const {subscribe, select} = wp.data;

const unsubscribe = subscribe(() => {
	const editor = select('core/block-editor');
	if (  ! editor || editor.getBlocks().length === 0) return;
    const editorEl = document.querySelector('.editor-styles-wrapper');
	if (editorEl === null) return;
	editorEl.classList.add('hb-wp-content');
    unsubscribe();
});

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
