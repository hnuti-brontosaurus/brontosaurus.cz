import {paths} from "./constants.js";


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
