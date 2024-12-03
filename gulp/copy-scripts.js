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

import {ENV_PRODUCTION} from './constants.js';
import {createErrorHandler} from './errorHandler.js';


export default (cb, files) => {
	for (let i in files) {
		let file = files[i];

		if ( ! file.distFileName || ! file.distPath || ! file.sourceFileName || ! file.sourcePath) {
			throw new Error('`distFileName`, `distPath`, `sourceFileName` and `sourcePath` has to be defined for each item.');
		}

		gulp.src(file.sourcePath + '/' + file.sourceFileName)
			.pipe(rename(file.distFileName))
			.pipe(gulp.dest(file.distPath))
			.on('end', () => {
				if (parseInt(i) === (files.length-1)) { // call only on last pipe
					cb();
				}
			});
	}
};
