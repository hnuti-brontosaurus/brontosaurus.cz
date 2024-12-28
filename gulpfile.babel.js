'use strict';

import gulp from 'gulp';
import taskList from 'gulp-task-listing';

import buildScripts from './gulp/build-scripts.js';
import buildStyles from './gulp/build-styles.js';
import {getSourcePathFromPathObject} from './gulp/utils.js';

const paths = {
	scripts: { global: { dist: 'frontend/dist/js', src: 'frontend/src/scripts' } },
	styles: { global: { dist: 'frontend/dist/css', src: 'frontend/src/styles' } },
};


// SCRIPTS

const buildScriptsTask = (cb) => {
	buildScripts(cb, [
		{
			distFileName: 'administrativeUnitsMap.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'index.ts',
			sourcePath: paths.scripts.global.src + '/administrativeUnitsMap',
		},
		{
			distFileName: 'references.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'index.ts',
			sourcePath: paths.scripts.global.src + '/references',
		},
	]);
};

gulp.task('build:scripts', gulp.series(buildScriptsTask));
gulp.task('watch:scripts', gulp.series(buildScriptsTask, () => {
	gulp.watch(getSourcePathFromPathObject(paths.scripts).map((path) => path + '/**/*.ts'), gulp.series(buildScriptsTask));
}));


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
	'watch:scripts',
	'watch:styles',
));

gulp.task('build', gulp.parallel(
	'build:scripts',
	'build:styles',
));

gulp.task('default', gulp.series('build'));
