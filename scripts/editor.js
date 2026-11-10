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
				{
					name: 'paw',
					label: 'S tlapou',
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
					customClassName: name === 'core/button',
					customCSS: false,
					typography: false,
				};
			}

			return settings;
		}
	);

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
			filters: { type: 'array', default: [] },
		},
		edit: function( props ) {
			const blockProps = wp.blockEditor.useBlockProps();
			const [ expandedFilterIndex, setExpandedFilterIndex ] = wp.element.useState( null );
			const [ dragState, setDragState ] = wp.element.useState( null );
			const filterListRef = wp.element.useRef( null );
			const filters = [
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
					attribute: 'tag',
					label: 'Štítek',
					options: [
						{ label: 'Retro akce', value: 'retro_event' },
						{ label: 'Regionální akce', value: 'region_event' },
					],
				},
				{
					attribute: 'region',
					label: 'Kraj konání',
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
			];

			const renderFilterControls = function( values, onChange, prefix, storeAsArray ) {
				return filters.map( function( filter ) {
					const currentValue = values[ filter.attribute ];
					const selectedValues = Array.isArray( currentValue )
						? currentValue
						: typeof currentValue === 'string'
							? currentValue.split( ',' ).filter( Boolean )
							: [];

					return wp.element.createElement( wp.components.SelectControl, {
						key: prefix + filter.attribute,
						className: 'bis-akce-filter-select',
						label: filter.label,
						value: selectedValues,
						options: filter.options,
						multiple: true,
						onChange: function( value ) {
							const selected = Array.isArray( value )
								? value
								: value
									? [ value ]
									: [];
							onChange( {
								[ filter.attribute ]: storeAsArray
									? selected
									: selected.join( ',' ),
							} );
						},
					} );
				} );
			};
			const customFilters = Array.isArray( props.attributes.filters )
				? props.attributes.filters.filter( function( filter ) {
					return filter && typeof filter === 'object' && ! Array.isArray( filter );
				} )
				: [];
			const updateFilter = function( index, updates ) {
				props.setAttributes( {
					filters: customFilters.map( function( filter, filterIndex ) {
						return filterIndex === index
							? Object.assign( {}, filter, updates )
							: filter;
					} ),
				} );
			};
			const addFilter = function() {
				const newFilter = { name: 'Nový filtr' };
				filters.forEach( function( filter ) {
					newFilter[ filter.attribute ] = [];
				} );
				setExpandedFilterIndex( customFilters.length );
				props.setAttributes( {
					filters: customFilters.concat( [ newFilter ] ),
				} );
			};
			const duplicateFilter = function( source, name ) {
				const newFilter = { name: name + ' (kopie)' };
				filters.forEach( function( filter ) {
					const currentValue = source[ filter.attribute ];
					newFilter[ filter.attribute ] = Array.isArray( currentValue )
						? currentValue.slice()
						: typeof currentValue === 'string'
							? currentValue.split( ',' ).filter( Boolean )
							: [];
				} );
				setExpandedFilterIndex( customFilters.length );
				props.setAttributes( {
					filters: customFilters.concat( [ newFilter ] ),
				} );
			};
			const removeFilter = function( index ) {
				props.setAttributes( {
					filters: customFilters.filter( function( filter, filterIndex ) {
						return filterIndex !== index;
					} ),
				} );
				setExpandedFilterIndex( null );
			};
			const moveFilter = function( sourceIndex, destinationIndex ) {
				if (
					sourceIndex === destinationIndex ||
					sourceIndex < 0 ||
					sourceIndex >= customFilters.length ||
					destinationIndex < 0 ||
					destinationIndex >= customFilters.length
				) {
					return;
				}

				const reorderedFilters = customFilters.slice();
				const movedFilter = reorderedFilters.splice( sourceIndex, 1 )[ 0 ];
				reorderedFilters.splice( destinationIndex, 0, movedFilter );
				props.setAttributes( { filters: reorderedFilters } );

				if ( expandedFilterIndex !== null ) {
					if ( expandedFilterIndex === sourceIndex ) {
						setExpandedFilterIndex( destinationIndex );
					} else if ( sourceIndex < expandedFilterIndex && destinationIndex >= expandedFilterIndex ) {
						setExpandedFilterIndex( expandedFilterIndex - 1 );
					} else if ( sourceIndex > expandedFilterIndex && destinationIndex <= expandedFilterIndex ) {
						setExpandedFilterIndex( expandedFilterIndex + 1 );
					}
				}
			};
			const filterEntries = customFilters.map( function( filter, index ) {
				return { filter: filter, index: index };
			} );
			const orderedFilterEntries = filterEntries.slice();
			if ( dragState ) {
				const draggedEntry = orderedFilterEntries.splice( dragState.sourceIndex, 1 )[ 0 ];
				orderedFilterEntries.splice( dragState.destinationIndex, 0, draggedEntry );
			}
			const startFilterDrag = function( event, index ) {
				if ( event.button !== 0 ) {
					return;
				}
				const item = event.currentTarget.closest( '.bis-akce-filter-item' );
				if ( ! item ) {
					return;
				}

				event.preventDefault();
				const bounds = item.getBoundingClientRect();
				setDragState( {
					sourceIndex: index,
					destinationIndex: index,
					pointerY: event.clientY,
					offsetY: event.clientY - bounds.top,
					left: bounds.left,
					width: bounds.width,
					height: bounds.height,
				} );
			};
			const updateFilterDrag = function( event ) {
				if ( ! dragState ) {
					return;
				}

				let destinationIndex = 0;
				if ( filterListRef.current ) {
					const remainingItems = filterListRef.current.querySelectorAll(
						'.bis-akce-filter-item[data-filter-index]:not(.is-dragging)'
					);
					remainingItems.forEach( function( item ) {
						const bounds = item.getBoundingClientRect();
						if ( event.clientY > bounds.top + bounds.height / 2 ) {
							destinationIndex++;
						}
					} );
					}

				setDragState( Object.assign( {}, dragState, {
					destinationIndex: destinationIndex,
					pointerY: event.clientY,
				} ) );
			};
			const finishFilterDrag = function() {
				if ( dragState ) {
					moveFilter( dragState.sourceIndex, dragState.destinationIndex );
					setDragState( null );
				}
			};
			const cancelFilterDrag = function() {
				setDragState( null );
			};
			wp.element.useEffect( function() {
				if ( ! dragState ) {
					return undefined;
				}

				document.addEventListener( 'pointermove', updateFilterDrag, true );
				document.addEventListener( 'pointerup', finishFilterDrag, true );
				document.addEventListener( 'pointercancel', cancelFilterDrag, true );
				return function() {
					document.removeEventListener( 'pointermove', updateFilterDrag, true );
					document.removeEventListener( 'pointerup', finishFilterDrag, true );
					document.removeEventListener( 'pointercancel', cancelFilterDrag, true );
				};
			}, [ dragState ] );

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
						{ title: 'Výpis akcí' },
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
					),
					wp.element.createElement(
						wp.components.PanelBody,
						{ title: 'Nastavení filtrů', initialOpen: false },
						wp.element.createElement(
							'p',
							null,
							'U každé možnosti lze vybrat více položek. Bez výběru se možnost nepoužije.'
						),
						wp.element.createElement(
							'div',
							{ className: 'bis-akce-filter-item bis-akce-filter-item--all' },
							wp.element.createElement(
								wp.components.PanelBody,
								{ title: 'vše', initialOpen: false },
								renderFilterControls(
									props.attributes,
									function( updates ) {
										props.setAttributes( updates );
									},
									'all-',
									false
								),
								wp.element.createElement(
									wp.components.Button,
									{
										variant: 'secondary',
										onClick: function() {
											duplicateFilter( props.attributes, 'vše' );
										},
									},
									'Duplikovat'
								)
							),
						),
						wp.element.createElement(
							'div',
							{ className: 'bis-akce-filter-list', ref: filterListRef },
							orderedFilterEntries.map( function( entry ) {
							const filter = entry.filter;
							const index = entry.index;
							const filterName = typeof filter.name === 'string' && filter.name
								? filter.name
								: 'Nový filtr';
							const isDragging = dragState && dragState.sourceIndex === index;

							return wp.element.createElement(
								wp.element.Fragment,
								{ key: 'filter-' + index },
								isDragging && wp.element.createElement( 'div', {
									className: 'bis-akce-filter-item bis-akce-filter-drop-slot',
									style: { height: dragState.height + 'px' },
									'aria-hidden': 'true',
								} ),
								wp.element.createElement(
									'div',
									{
										'data-filter-index': index,
										className: 'bis-akce-filter-item' + (
											isDragging ? ' is-dragging' : ''
										),
										style: isDragging
											? {
												position: 'fixed',
												top: ( dragState.pointerY - dragState.offsetY ) + 'px',
												left: dragState.left + 'px',
												width: dragState.width + 'px',
												height: dragState.height + 'px',
												zIndex: 100000,
												pointerEvents: 'none',
											}
											: undefined,
									},
									wp.element.createElement(
										'button',
										{
											type: 'button',
											className: 'bis-akce-filter-drag-handle',
											'aria-label': 'Přetáhnout filtr „' + filterName + '“',
											title: 'Přetáhnout pro změnu pořadí',
											onPointerDown: function( event ) {
												startFilterDrag( event, index );
											},
										},
										'⋮'
									),
									wp.element.createElement(
										wp.components.PanelBody,
										{
											title: filterName,
											initialOpen: expandedFilterIndex === index,
											className: 'bis-akce-filter-panel',
										},
										wp.element.createElement( wp.components.TextControl, {
											label: 'Název filtru',
											value: typeof filter.name === 'string' ? filter.name : '',
											onChange: function( name ) {
												updateFilter( index, { name: name } );
											},
										} ),
										renderFilterControls(
											filter,
											function( updates ) {
												updateFilter( index, updates );
											},
											'custom-' + index + '-',
											true
										),
										wp.element.createElement(
											'div',
											{ className: 'bis-akce-filter-buttons' },
											wp.element.createElement(
												wp.components.Button,
												{
													variant: 'secondary',
													isDestructive: true,
													onClick: function() {
														if ( window.confirm( 'Opravdu chcete odebrat filtr „' + filterName + '“?' ) ) {
															removeFilter( index );
														}
													},
												},
												'Odebrat filtr'
											),
											wp.element.createElement(
												wp.components.Button,
												{
													variant: 'secondary',
													onClick: function() {
														duplicateFilter( filter, filterName );
													},
												},
												'Duplikovat'
											)
										)
									)
								)
							);
							} )
						),
						wp.element.createElement(
							wp.components.Button,
							{
								variant: 'secondary',
								className: 'bis-akce-filter-add-button',
								onClick: addFilter,
							},
							'Přidat filtr'
						)
					)
				)
			);
		},
		save: function() {
			return null;
		},
	} );
} )( window.wp );
