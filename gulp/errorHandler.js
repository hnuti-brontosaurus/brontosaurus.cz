export const createErrorHandler = (isProduction) => {
	return function (error) {
		console.error(error);

		if (isProduction) {
			// fail hard
			process.exit(1);

		} else {
			// just mark the job as done to keep watch task running
			this.emit('end');
		}
	};
};
