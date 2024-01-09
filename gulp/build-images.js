'use strict';

import flatten from 'gulp-flatten';
import gulp from 'gulp';
import gulpIf from 'gulp-if';
import imageResize from 'gulp-image-resize';
import image from 'gulp-image';
import plumber from 'gulp-plumber';

import {ENV_PRODUCTION} from './constants.js';

/**
 * NOTE ON IMAGE RESIZING
 * ======================
 *
 * There are imagemagick and graphicsmagick programs to work with images
 * but as only imagick works on CI, we explicitely choose this one (`imagemagick: true`).
 * If you have troubles with running production image build on local computer,
 * try to change it to `imagemagick: false` (at least it worked on my Win 10 station).
 * Other possible troubleshootings are listed here: https://stackoverflow.com/a/41091671/3668474
 */


/**
 * Use to aim on view-specific assets only.
 * @param file [Vinyl file](https://github.com/gulpjs/vinyl#instance-properties)
 * @param viewName string
 * @returns boolean
 */
const isAssetOfView = (file, viewName) => {
	const regex = new RegExp('UI[\\\\\/]' + viewName + '[\\\\\/]assets'); // `\\\\` is escaped double backslash on Windows, `\/` is escaped single slash on UNIX
	return file.base.match(regex)
};


export default (cb, dirs) => {
	const isProduction = (process.env.NODE_ENV === ENV_PRODUCTION);

	for (let i in dirs) {
		let dir = dirs[i];

		if (!dir.src || !dir.dist) {
			throw new Error('`src` and `dist` has to be defined.');
		}

		gulp.src(dir.src + '/**/*.{gif,ico,jpg,jpeg,png,svg}')
			.pipe(gulpIf( ! isProduction, plumber()))
			.pipe(gulpIf(isProduction, image({
				pngquant: ['--quality', '49-51'],
				mozjpeg: ['-quality', 75],
				gifsicle: ['-l 2'],
				svgo: true,
				concurrent: 10,
			})))
			// crop images due to performance
			.pipe(gulpIf(isProduction, gulpIf('header/*', imageResize({
				width: 1800,
				upscale: false,
				imageMagick: true,
			}))))
			.on('error', function(error) {
				console.log(error);
			})
			.pipe(gulpIf(isProduction, gulpIf('boxes/*', imageResize({
				width: 1060,
				upscale: false,
				imageMagick: true,
			}))))
			.pipe(gulpIf(isProduction, gulpIf((file) => isAssetOfView(file, 'Contacts'), imageResize({
				width: 850,
				upscale: false,
				imageMagick: true,
			}))))
			.pipe(gulpIf(isProduction, gulpIf((file) => isAssetOfView(file, 'Rentals'), imageResize({
				width: 1024, // it would be better to have multiple files with different dimensions, but build infrastructure is not ready for that yet so this is quick fix (assuming that even opening the file itself in browser would not need more than 1024px wide photo)
				upscale: false,
				imageMagick: true,
			}))))
			.on('error', function(error) {
				console.log(error);
			})
			//gulp-flatten must be here due to matching paths
			.pipe(flatten())
			.pipe(gulpIf( ! isProduction, plumber.stop()))
			.pipe(gulp.dest(dir.dist))
			.on('end', () => {
				if (parseInt(i) === (dirs.length-1)) { // call only on last pipe
					cb();
				}
			});
	}
};
