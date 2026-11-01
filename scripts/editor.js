( function( wp ) {
	const spacerHeight = 'var(--wp--preset--spacing--normal)';
	const spacerSizes = Array.from( { length: 11 }, function( _, size ) {
		return {
			label: String( size ),
			value:
				size === 0
					? '0'
					: size === 1
						? spacerHeight
						: 'calc(var(--wp--preset--spacing--normal) * ' + size + ')',
		};
	} );

	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'brontosaurus/default-spacer-height',
		function( settings, name ) {
			if (
				name === 'core/spacer' &&
				settings.attributes &&
				settings.attributes.height
			) {
				settings.attributes.height.default = spacerHeight;

				settings.edit = function( props ) {
					const parentLayout = props.__unstableParentLayout || {};
					const parentOrientation = parentLayout.orientation;
					const layoutType =
						parentLayout.type ||
						( parentLayout.default && parentLayout.default.type );
					const isFlexLayout = layoutType === 'flex';
					const orientation = isFlexLayout && ! parentOrientation
						? 'horizontal'
						: parentOrientation || ( props.context && props.context.orientation ) || 'vertical';
					const isHorizontal = orientation === 'horizontal';
					const sizeAttribute = isHorizontal ? 'width' : 'height';
					const attributes = props.attributes || {};
					const blockStyle = attributes.style || {};
					const layout = blockStyle.layout || {};
					const value =
						layout.flexSize ||
						attributes[ sizeAttribute ] ||
						attributes.height ||
						spacerHeight;
					const selectedSize = spacerSizes.findIndex( function( size ) {
						return size.value === value;
					} );
					const rangeValue = selectedSize === -1 ? 1 : selectedSize;
					const isFlexible =
						layout.selfStretch === 'fill' ||
						layout.selfStretch === 'fit';
					const blockProps = wp.blockEditor.useBlockProps( {
						style: isFlexLayout
							? {
								height: isHorizontal ? '24px' : undefined,
								minWidth: isHorizontal ? undefined : '48px',
								flexBasis: isFlexible
									? undefined
									: layout.flexSize || value,
							}
							: isHorizontal
								? { height: '24px', width: value }
								: { height: value },
					} );

					return wp.element.createElement(
						wp.element.Fragment,
						null,
						wp.element.createElement( 'div', {
							...blockProps,
							'aria-hidden': true,
						} ),
						wp.element.createElement(
							wp.blockEditor.InspectorControls,
							null,
							wp.element.createElement(
								wp.components.PanelBody,
								{ title: 'Spacer size', initialOpen: true },
								wp.element.createElement( wp.components.RangeControl, {
									label: 'Size',
									value: rangeValue,
									min: 0,
									max: spacerSizes.length - 1,
									step: 1,
									marks: spacerSizes.map(
										function( size, index ) {
											return {
												value: index,
												label: size.label,
											};
										}
									),
									withInputField: false,
									onChange: function( index ) {
										if ( index !== undefined ) {
											if ( isFlexLayout ) {
												props.setAttributes( {
													[ sizeAttribute ]: '0',
													style: {
														...blockStyle,
														layout: {
															...layout,
															flexSize: spacerSizes[ index ].value,
															selfStretch: 'fixed',
														},
													},
												} );
											} else {
												props.setAttributes( {
													[ sizeAttribute ]: spacerSizes[ index ].value,
												} );
											}
										}
									},
								} )
							)
						)
					);
				};
			}

			return settings;
		}
	);
} )( window.wp );
