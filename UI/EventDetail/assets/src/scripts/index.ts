import handleflashMessage from './handleFlashMessage';
import handleRegistrationForm from './Registration/index';
import {scrollToErrorIfAny} from './scrollToErrorIfAny';

document.addEventListener('DOMContentLoaded', () => {
	handleflashMessage(document);
	handleRegistrationForm(window, document); // it does not contain any images thus it is safe to trigger on DOMContentLoaded
	scrollToErrorIfAny(document);
});
