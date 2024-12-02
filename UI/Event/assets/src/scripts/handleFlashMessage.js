export default (document) => {
	let flashMessageElement = document.querySelector('.detail__flashMessage');
	if (flashMessageElement === null) {
		return;
	}

	let closeButtonElement = flashMessageElement.querySelector('.detail__flashMessage-closeButton');
	if (closeButtonElement === null) {
		return;
	}

	closeButtonElement.addEventListener('click', () => {
		flashMessageElement.classList.add('detail__flashMessage--hidden');
	});
}
