// @ts-ignore no types
import GLightbox from 'glightbox';

document.addEventListener('DOMContentLoaded', () => {
	GLightbox({
		selector: '[data-glightbox]',
		loop: true,
	});
});
