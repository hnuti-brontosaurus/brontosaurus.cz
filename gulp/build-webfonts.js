'use strict';

import gulp from 'gulp';

export default (paths) => {
	if (!paths.sourcePath || !paths.distPath) {
		throw new Error('`sourcePath` and `distPath` have to be defined.');
	}

	return gulp.src(paths.sourcePath + '/*')
		.pipe(gulp.dest(paths.distPath));
};
