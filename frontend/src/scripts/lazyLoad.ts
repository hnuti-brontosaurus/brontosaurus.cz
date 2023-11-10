// @ts-ignore
import LazyLoad from "vanilla-lazyload";

document.addEventListener('DOMContentLoaded', (): void => {
	new LazyLoad({ // @see https://github.com/verlok/lazyload#options for more options
		elements_selector: '.hb-event-image', // lazy load only images in event list
	});
});
