import babelify from 'babelify';
import browserify from 'browserify';
import buffer from 'vinyl-buffer';
import gulp from 'gulp';
import gulpIf from 'gulp-if';
import plumber from 'gulp-plumber';
import rename from 'gulp-rename';
import source from 'vinyl-source-stream';
import sourcemaps from 'gulp-sourcemaps';
import tsify from 'tsify';
import uglify from 'gulp-uglify';

import {ENV_PRODUCTION} from './constants';
import {createErrorHandler} from './errorHandler';


export default (cb, files) => {
	const isProduction = (process.env.NODE_ENV === ENV_PRODUCTION);
	const errorHandler = createErrorHandler(isProduction);

	for (let i in files) {
		let bundler;
		let file = files[i];

		if ( ! file.distFileName || ! file.distPath || ! file.sourceFileName || ! file.sourcePath) {
			throw new Error('`distFileName`, `distPath`, `sourceFileName` and `sourcePath` has to be defined for each item.');
		}

		bundler = browserify({
			entries: file.sourcePath + '/' + file.sourceFileName,
			debug: !isProduction
		});
		bundler.plugin('tsify');
		bundler.transform(babelify, {
			extensions: ['.ts'],
			presets: ['env'],
			sourceMaps: true
		});

		bundler.bundle()
			.on('error', errorHandler)
			.pipe(plumber({errorHandler}))
			.pipe(source(file.sourceFileName))
			.pipe(rename(file.distFileName))
			.pipe(buffer())
			.pipe(sourcemaps.init({loadMaps: true}))
			.pipe(gulpIf(isProduction, uglify()))
			.pipe(sourcemaps.write('.'))
			.pipe(plumber.stop())
			.pipe(gulp.dest(file.distPath))
			.on('end', () => {
				if (parseInt(i) === (files.length-1)) { // call only on last pipe
					cb();
				}
			});
	}
};
