( function( wp ) {
    console.log( 'Brontosaurus Editor Script Loaded' );

	const spacerHeight = 'var(--wp--preset--spacing--normal)';

	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'brontosaurus/default-spacer-height',
		function( settings, name ) {
			if ( name === 'core/spacer' && settings.attributes && settings.attributes.height ) {
				settings.attributes.height.default = spacerHeight;
			}

			return settings;
		}
	);

	const editorStore = 'core/block-editor';
	const knownBlockIds = new Set();

	function flattenBlocks( blocks ) {
		return blocks.reduce( function( allBlocks, block ) {
			return allBlocks.concat( block, flattenBlocks( block.innerBlocks || [] ) );
		}, [] );
	}

	function applySpacerDefault() {
		const blocks = flattenBlocks( wp.data.select( editorStore ).getBlocks() );

		blocks.forEach( function( block ) {
			if ( knownBlockIds.has( block.clientId ) ) {
				return;
			}

			knownBlockIds.add( block.clientId );

			if ( block.name === 'core/spacer' ) {
				wp.data.dispatch( editorStore ).updateBlockAttributes( block.clientId, {
					height: spacerHeight,
				} );
			}
		} );
	}

	applySpacerDefault();
	wp.data.subscribe( applySpacerDefault );
} )( window.wp );
