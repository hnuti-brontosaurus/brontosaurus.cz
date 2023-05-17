'use strict';

import gulp from 'gulp';
import taskList from 'gulp-task-listing';

import {paths} from './gulp/constants';
import buildImages from './gulp/build-images';
import buildScripts from './gulp/build-scripts';
import buildStyles from './gulp/build-styles';
import buildWebfonts from './gulp/build-webfonts';
import {getSourcePathFromPathObject} from "./gulp/utils";


// SCRIPTS

const buildScriptsTask = (cb) => {
	buildScripts(cb, [
		{
			distFileName: 'index.js',
			distPath: paths.scripts.detail.dist, // folder to save the compiled js file into
			sourceFileName: 'index.ts',
			sourcePath: paths.scripts.detail.src,
		},
		{
			distFileName: 'administrativeUnitsMap.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'index.ts',
			sourcePath: paths.scripts.global.src + '/administrativeUnitsMap',
		},
		{
			distFileName: 'events.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'index.ts',
			sourcePath: paths.scripts.global.src + '/events',
		},
		{
			distFileName: 'lazyLoad.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'lazyLoad.ts',
			sourcePath: paths.scripts.global.src,
		},
		{
			distFileName: 'lightbox.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'lightbox.ts',
			sourcePath: paths.scripts.global.src,
		},
		{
			distFileName: 'menuHandler.js',
			distPath: paths.scripts.global.dist, // folder to save the compiled js file into
			sourceFileName: 'menuHandler.ts',
			sourcePath: paths.scripts.global.src,
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
			distFileName: 'content.css',
			sourceFileName: paths.styles.global.src + '/content.scss', // name of source file
		},
	]);
};

gulp.task('build:styles', gulp.series(buildStylesTask));
gulp.task('watch:styles', gulp.series(buildStylesTask, () => {
	gulp.watch(getSourcePathFromPathObject(paths.styles).map((path) => path + '/**/*.scss'), gulp.series(buildStylesTask));
}));


// WEBFONTS

const buildWebfontsTask = () => {
	return buildWebfonts(
		{
			distPath: paths.webfonts.global.dist,
			sourcePath: paths.webfonts.global.src,
		},
	);
};

gulp.task('build:webfonts', gulp.series(buildWebfontsTask));
gulp.task('watch:webfonts', gulp.series(buildWebfontsTask, () => {
	gulp.watch(getSourcePathFromPathObject(paths.webfonts).map((path) => path + '/*'), gulp.series(buildWebfontsTask));
}));


// IMAGES

const buildImagesTask = (cb) => {
	return buildImages(cb, [
		paths.images.global,
		// paths.images.rentals,
	]);
};

gulp.task('build:images', gulp.series(buildImagesTask));
gulp.task('watch:images', gulp.series(buildImagesTask, () => {
	gulp.watch(getSourcePathFromPathObject(paths.images).map((path) => path + '/**/*.{gif,ico,jpg,jpeg,png,svg}'), gulp.series(buildImagesTask)); // @todo: add custom
}));


// HELPERS

gulp.task('help', taskList);

gulp.task('watch', gulp.parallel(
	'watch:scripts',
	'watch:styles',
	'watch:webfonts',
	'watch:images',
));

gulp.task('build', gulp.parallel(
	'build:scripts',
	'build:styles',
	'build:webfonts',
	'build:images',
));

gulp.task('default', gulp.series('build'));
