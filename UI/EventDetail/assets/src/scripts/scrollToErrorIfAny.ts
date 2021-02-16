export function scrollToErrorIfAny(document: Document): void
{
	const errorElement = document.querySelector('.registration__formError');
	if (errorElement !== null) {
		errorElement.scrollIntoView({
			behavior: 'smooth',
			block: 'center', // because of sticky navigation – you never know if it does not keep hanging and overflowing viewport top
		})
	}
}
