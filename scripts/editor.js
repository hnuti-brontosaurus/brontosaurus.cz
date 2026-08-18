( function( wp ) {
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'brontosaurus/default-spacer-height',
		function( settings, name ) {
			if ( name === 'core/spacer' && settings.attributes && settings.attributes.height ) {
				settings.attributes.height.default = 'var(--wp--preset--spacing--normal)';
			}

			return settings;
		}
	);
} )( window.wp );
