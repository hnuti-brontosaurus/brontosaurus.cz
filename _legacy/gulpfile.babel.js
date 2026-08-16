'use strict';

import gulp from 'gulp';
import autoprefixer from 'gulp-autoprefixer';
import rename from 'gulp-rename';
import gulpSass from 'gulp-sass';
import * as sassPackage from 'sass';
const sass = gulpSass(sassPackage);
import sourcemaps from 'gulp-sourcemaps';

const stylesPath = {
	dist: 'frontend/dist/css', 
	src: 'frontend/src/styles',
};


// STYLES

const buildTask = (cb) => {
	const files = [
		{
			distPath: stylesPath.dist, // folder to save the compiled css file into
			distFileName: 'style.css',
			sourceFileName: stylesPath.src + '/style.scss', // name of source file
		},
		{
			distPath: stylesPath.dist, // folder to save the compiled css file into
			distFileName: 'editor.css',
			sourceFileName: stylesPath.src + '/editor.scss', // name of source file
		},
		{
			distPath: stylesPath.dist, // folder to save the compiled css file into
			distFileName: 'events.css',
			sourceFileName: stylesPath.src + '/events.scss', // name of source file
		},
	];

	for (let i in files) {
		let file = files[i];

		if (!file.distFileName || !file.distPath || !file.sourceFileName) {
			throw new Error('`distFileName`, `distPath` and `sourceFileName` has to be defined for each item.');
		}

		gulp.src(file.sourceFileName)
			.pipe(rename(file.distFileName)) // must be before sass to keep correct file names between css and sourcemap
			.pipe(sourcemaps.init())
			.pipe(sass({
				noCache: true,
				outputStyle: (process.env.NODE_ENV === 'production' ? 'compressed' : 'expanded')
			}).on('error', sass.logError))
			.pipe(autoprefixer()) // retrieves from `.browserslistrc`
			.pipe(sourcemaps.write('.'))
			.pipe(gulp.dest(file.distPath))
			.on('end', () => {
				if (parseInt(i) === (files.length-1)) { // call only on last pipe
					cb();
				}
			});
	}
};

gulp.task('build', gulp.series(buildTask));
gulp.task('watch', gulp.series(buildTask, () => {
	gulp.watch(stylesPath.src + '/**/*.scss', gulp.series(buildTask));
}));
