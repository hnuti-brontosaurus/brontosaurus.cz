const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { Button, TextControl, SelectControl, PanelBody, ToggleControl } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { compose } = wp.compose;
const { createElement, useEffect } = wp.element;

const isValidUrl = (input) => {
	try {
		new URL(input);
		return true;
	} catch (_) {
		return false;
	}
};

const MetaFields = (props) => {
	const { meta, setMeta, imageUrl } = props;

	return createElement(
		PluginDocumentSettingPanel,
		{ name: 'hb-cover-photo-panel', title: 'Nastavení hlavní fotky', className: 'hb-cover-photo-panel' },

		createElement(
			'div',
			{ style: { marginBotton: '16px' } },

			imageUrl &&
				createElement(
					'div', 
					null,
					
					createElement('img', {
						src: imageUrl,
						style: {
							width: '100%',
							height: 'auto',
							marginBottom: '8px',
							borderRadius: '4px',
						},
					}),

					createElement(
						Button,
						{
							onClick: () => window.confirm('Opravdu odebrat? Fotka zůstane v knihovně médií, je-li vzata z ní') && setMeta({
								'hb_cover_photo_image_id': null,
								'hb_cover_photo_image_external_url': '',
							}),
							isDestructive: true,
						},
						'Odebrat'
					),
				),

			! imageUrl &&
				createElement(
					TextControl,
					{
						label: 'Url',
						value: meta.hb_cover_photo_image_external_url || '',
						onChange: (val) => isValidUrl(val) && setMeta({ 'hb_cover_photo_image_external_url': val })
					}
				),

			! imageUrl &&
				createElement(
					MediaUploadCheck,
					null,
					createElement(
						MediaUpload,
						{
							onSelect: (media) => setMeta({ 'hb_cover_photo_image_id': media.id }),
							allowedTypes: ['image'],
							value: meta.hb_cover_photo_image_id,
							render: ({ open }) => createElement(
								'div',
								{ style: { marginBottom: '16px' } },
		
								createElement(
									Button,
									{ onClick: open, isSecondary: true, style: { marginRight: '8px', marginBottom: '8px' } },
									'Vybrat fotku'
								),
							),
						}
					),
				),
		),
	);
};

const MetaFieldsConnected = compose([
	withSelect((select) => {
		const meta = select('core/editor').getEditedPostAttribute('meta') || {};
		const imageId = meta.hb_cover_photo_image_id;
		const imageExternalUrl = meta.hb_cover_photo_image_external_url;
		
		let imageUrl = '';
		if (imageId) {
			const media = select('core').getMedia(imageId);
			imageUrl = media?.source_url || '';
		} else if (imageExternalUrl) {
			imageUrl = imageExternalUrl;
		}

		return { meta, imageUrl };
	}),
	withDispatch((dispatch) => ({
		setMeta(newMeta) {
			dispatch('core/editor').editPost({ meta: newMeta });
		}
	})),
])(MetaFields);

registerPlugin('hb-cover-photo-panel', {
	render: MetaFieldsConnected
});


// PREVIEW

const CoverPhotoEditorPreview = withSelect((select) => {
	const meta = select('core/editor').getEditedPostAttribute('meta') || {};
		const imageId = meta.hb_cover_photo_image_id;
		const imageExternalUrl = meta.hb_cover_photo_image_external_url;
		
		let imageUrl = '';
		if (imageId) {
			const media = select('core').getMedia(imageId);
			imageUrl = media?.source_url || '';
		} else if (imageExternalUrl) {
			imageUrl = imageExternalUrl;
		}

		return { imageUrl, meta };
})(({ imageUrl, meta }) => {
	useEffect(() => {
		const container = document.querySelector('.edit-post-visual-editor__post-title-wrapper');
		if ( ! container || ! imageUrl) return;

		const existing = document.getElementById('hb-cover-photo-preview');
		if (existing) existing.remove();

		const preview = document.createElement('div');
		preview.id = 'hb-cover-photo-preview';
		preview.classList.add('hb-coverPhoto');
		preview.style.setProperty('--hb-coverPhoto-image', `url('${imageUrl}')`);
		preview.style.marginBlockEnd = '-3rem'; // there is some margin top so this reduces the space

		container.parentNode.insertBefore(preview, container);
	}, [imageUrl, meta]);

	return null;
});

registerPlugin('hb-cover-photo-preview', {
	render: CoverPhotoEditorPreview,
});
