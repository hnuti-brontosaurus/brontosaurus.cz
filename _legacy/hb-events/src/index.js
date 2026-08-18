import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { Disabled, PanelBody, RadioControl } from '@wordpress/components';
import metadata from '../block.json';

registerBlockType( 'hb/events', {
	edit: function ( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		return <div { ...blockProps }>
			<Disabled>
				<ServerSideRender
					block={ metadata.name }
					attributes={ attributes }
				/>
			</Disabled>

			<InspectorControls>
				<PanelBody>
					<RadioControl
						label="Štítek"
						selected={ attributes.tags }
						options={ metadata.attributes.tags.enum.map(value => ({label: value, value: value})) }
						onChange={ value => setAttributes( {tags: value} ) }
					/>
				</PanelBody>
			</InspectorControls>
		</div>;
	},
} );
