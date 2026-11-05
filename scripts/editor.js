( function( wp ) {
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'brontosaurus/button-customizations',
		function( settings, name ) {
			if ( name !== 'core/button' ) {
				return settings;
			}

			settings.styles = [
				{
					name: 'primary',
					label: 'Hlavní',
					isDefault: true,
				},
				{
					name: 'secondary',
					label: 'Vedlejší',
				},
			];
			settings.supports = {
				...settings.supports,
				background: false,
				color: false,
				dimensions: false,
				border: false,
				__experimentalBorder: false,
				shadow: false,
				spacing: false,
				typography: false,
			};

			return settings;
		}
	);

	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'brontosaurus/disable-core-advanced-and-typography-controls',
		function( settings, name ) {
			if ( name.startsWith( 'core/' ) && settings.supports ) {
				settings.supports = {
					...settings.supports,
					anchor: false,
					customClassName: false,
					customCSS: false,
					typography: false,
				};
			}

			return settings;
		}
	);

	const linkFormatName = 'brontosaurus/link-style';

	wp.richText.registerFormatType( linkFormatName, {
		title: 'Vzhled odkazu',
		tagName: 'a',
		className: 'hb-link--paw',
		edit: function( props ) {
			if ( ! wp.richText.getActiveFormat( props.value, 'core/link' ) ) {
				return null;
			}

			const isPaw = props.isActive;

			return wp.element.createElement( wp.components.ToolbarDropdownMenu, {
				icon: isPaw ? 'admin-links' : 'editor-link',
				label: 'Vzhled odkazu',
				controls: [
					{
						title: 'Normální',
						isActive: ! isPaw,
						onClick: function() {
							props.onChange(
								wp.richText.removeFormat( props.value, linkFormatName )
							);
						},
					},
					{
						title: 'S tlapou',
						isActive: isPaw,
						onClick: function() {
							props.onChange(
								wp.richText.toggleFormat( props.value, {
									type: linkFormatName,
								} )
							);
						},
					},
				],
			} );
		},
	} );

	const spacerHeight = 'var(--wp--preset--spacing--normal)';
	const spacerSizes = Array.from( { length: 10 }, function( _, index ) {
		const size = index + 1;

		return {
			label: String( size ),
			value: size === 1
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
					const rangeValue = selectedSize === -1 ? 1 : selectedSize + 1;
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
									min: 1,
									max: spacerSizes.length,
									step: 1,
									marks: spacerSizes.map(
										function( size, index ) {
											return {
												value: index + 1,
												label: size.label,
											};
										}
									),
									withInputField: false,
									onChange: function( size ) {
										if ( size !== undefined ) {
											const index = size - 1;

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

	wp.blocks.registerBlockType( 'brontosaurus/bis-akce', {
		apiVersion: 3,
		title: 'BIS akce',
		description: 'Zobrazí akce z Brontosauřího informačního systému.',
		icon: 'calendar-alt',
		category: 'widgets',
		attributes: {
			limit: { type: 'number', default: 9 },
			tag: { type: 'string', default: '' },
			program: { type: 'string', default: '' },
			intendedFor: { type: 'string', default: '' },
			category: { type: 'string', default: '' },
			group: { type: 'string', default: '' },
			region: { type: 'string', default: '' },
		},
		edit: function( props ) {
			const blockProps = wp.blockEditor.useBlockProps();
			const filters = [
				{
					attribute: 'program',
					label: 'Program',
					options: [
						{ label: 'Bez programu', value: 'none' },
						{ label: 'Příroda', value: 'nature' },
						{ label: 'Památky', value: 'monuments' },
						{ label: 'Děti', value: 'kids' },
						{ label: 'Ekostan', value: 'eco_tent' },
						{ label: 'Prázdniny s Brontosaurem', value: 'holidays_with_brontosaurus' },
						{ label: 'Vzdělávání', value: 'education' },
						{ label: 'Mezinárodní', value: 'international' },
					],
				},
				{
					attribute: 'intendedFor',
					label: 'Cílová skupina',
					options: [
						{ label: 'Pro všechny', value: 'for_all' },
						{ label: 'Mládež a dospělí', value: 'for_young_and_adult' },
						{ label: 'Děti', value: 'for_kids' },
						{ label: 'Rodiče s dětmi', value: 'for_parents_with_kids' },
						{ label: 'Účastníci poprvé', value: 'for_first_time_participant' },
					],
				},
				{
					attribute: 'category',
					label: 'Kategorie akce',
					options: [
						{ label: 'Výukový program (EVP)', value: 'evp' },
						{ label: 'Zážitková akce', value: 'experiential' },
						{ label: 'Interní akce', value: 'internal' },
						{ label: 'Organizátorské vzdělávání', value: 'internal_educational' },
						{ label: 'OHB a Cestičky', value: 'internal_educational_full' },
						{ label: 'Prezentační akce', value: 'presentation' },
						{ label: 'Odborné vzdělávání', value: 'public_educational' },
						{ label: 'Oddílová schůzka', value: 'section_meeting' },
						{ label: 'Dobrovolnická akce', value: 'volunteering' },
					],
				},
				{
					attribute: 'group',
					label: 'Typ akce',
					options: [
						{ label: 'Tábor', value: 'camp' },
						{ label: 'Víkendová akce', value: 'weekend_event' },
						{ label: 'Ostatní', value: 'other' },
					],
				},
				{
					attribute: 'region',
					label: 'Kraj',
					options: [
						{ label: 'Praha', value: '1' },
						{ label: 'Středočeský kraj', value: '2' },
						{ label: 'Jihočeský kraj', value: '3' },
						{ label: 'Plzeňský kraj', value: '4' },
						{ label: 'Karlovarský kraj', value: '5' },
						{ label: 'Ústecký kraj', value: '6' },
						{ label: 'Liberecký kraj', value: '7' },
						{ label: 'Královéhradecký kraj', value: '8' },
						{ label: 'Pardubický kraj', value: '9' },
						{ label: 'Kraj Vysočina', value: '10' },
						{ label: 'Jihomoravský kraj', value: '11' },
						{ label: 'Olomoucký kraj', value: '12' },
						{ label: 'Moravskoslezský kraj', value: '13' },
						{ label: 'Zlínský kraj', value: '14' },
					],
				},
				{
					attribute: 'tag',
					label: 'Štítek',
					options: [
						{ label: 'Retro akce', value: 'retro_event' },
						{ label: 'Regionální akce', value: 'region_event' },
					],
				},
			];

			return wp.element.createElement(
				wp.element.Fragment,
				null,
				wp.element.createElement(
					'div',
					blockProps,
					wp.element.createElement(
						wp.components.Disabled,
						null,
						wp.element.createElement( wp.serverSideRender, {
							block: 'brontosaurus/bis-akce',
							attributes: props.attributes,
						} )
					)
				),
				wp.element.createElement(
					wp.blockEditor.InspectorControls,
					null,
					wp.element.createElement(
						wp.components.PanelBody,
						{ title: 'Nastavení akcí' },
						wp.element.createElement(
							'p',
							null,
							'U každého filtru lze vybrat více možností. Bez výběru se filtr nepoužije.'
						),
						filters.map( function( filter ) {
							return wp.element.createElement( wp.components.SelectControl, {
								key: filter.attribute,
								label: filter.label,
								value: ( props.attributes[ filter.attribute ] || '' )
									.split( ',' )
									.filter( Boolean ),
								options: filter.options,
								multiple: true,
								onChange: function( value ) {
									props.setAttributes( {
										[ filter.attribute ]: value.join( ',' ),
									} );
								},
							} );
						} ),
						wp.element.createElement( wp.components.RangeControl, {
							label: 'Počet akcí',
							value: props.attributes.limit,
							min: 3,
							max: 15,
							onChange: function( limit ) {
								if ( limit !== undefined ) {
									props.setAttributes( { limit: limit } );
								}
							},
						} )
					)
				)
			);
		},
		save: function() {
			return null;
		},
	} );
} )( window.wp );
