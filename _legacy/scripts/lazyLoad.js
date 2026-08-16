document.addEventListener('DOMContentLoaded', () => {
	new LazyLoad({ // @see https://github.com/verlok/lazyload#options for more options
		elements_selector: '.hb-event__image', // lazy load only images in event list
	});
});
