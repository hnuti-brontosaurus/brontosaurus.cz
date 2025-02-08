'use strict';

import gulp from 'gulp';
import taskList from 'gulp-task-listing';

import buildStyles from './gulp.build-styles.js';

const paths = {
	styles: { global: { dist: 'frontend/dist/css', src: 'frontend/src/styles' } },
};

/**
 *
 * @param paths Expects paths.[type] object from ./constants.js
 * @returns {Array}
 */
export const getSourcePathFromPathObject = (paths) => {
	let sourcePaths = [];
	for (let type in paths) {
		const location = paths[type];
		sourcePaths.push(location.src);
	}

	return sourcePaths;
};



// STYLES

const buildStylesTask = (cb) => {
	buildStyles(cb, [
		{
			distPath: paths.styles.global.dist, // folder to save the compiled css file into
			distFileName: 'style.css',
			sourceFileName: paths.styles.global.src + '/style.scss', // name of source file
		},
		{
			distPath: paths.styles.global.dist, // folder to save the compiled css file into
			distFileName: 'editor.css',
			sourceFileName: paths.styles.global.src + '/editor.scss', // name of source file
		},
		{
			distPath: paths.styles.global.dist, // folder to save the compiled css file into
			distFileName: 'events.css',
			sourceFileName: paths.styles.global.src + '/events.scss', // name of source file
		},
	]);
};

gulp.task('build:styles', gulp.series(buildStylesTask));
gulp.task('watch:styles', gulp.series(buildStylesTask, () => {
	gulp.watch(getSourcePathFromPathObject(paths.styles).map((path) => path + '/**/*.scss'), gulp.series(buildStylesTask));
}));


// HELPERS

// todo: finish USER in docker, maybe yarn will be then available for www-data
// todo: then try these things

gulp.task('help', taskList);

gulp.task('watch', gulp.parallel(
	'watch:styles',
));

gulp.task('build', gulp.parallel(
	'build:styles',
));

gulp.task('default', gulp.series('build'));
