'use strict';

import gulp from 'gulp';
import autoprefixer from 'gulp-autoprefixer';
import plumber from 'gulp-plumber';
import rename from 'gulp-rename';
const sass = require('gulp-sass')(require('sass'));
import sourcemaps from 'gulp-sourcemaps';

import {ENV_PRODUCTION} from './constants';


export default (cb, files) => {
	const isProduction = (process.env.NODE_ENV === ENV_PRODUCTION);

	for (let i in files) {
		let file = files[i];

		if (!file.distFileName || !file.distPath || !file.sourceFileName) {
			throw new Error('`distFileName`, `distPath` and `sourceFileName` has to be defined for each item.');
		}

		gulp.src(file.sourceFileName)
			.pipe(plumber())
			.pipe(rename(file.distFileName)) // must be before sass to keep correct file names between css and sourcemap
			.pipe(sourcemaps.init())
			.pipe(sass({
				noCache: true,
				outputStyle: (isProduction ? 'compressed' : 'expanded')
			}))
			.pipe(autoprefixer()) // retrieves from `.browserslistrc`
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
