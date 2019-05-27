const { series, src, dest } = require('gulp');
const del = require('del');

/**
 * Delete the content of the folder ./build/src/vendor
 */
function cleanSourceVendor() {
    return del('./build/src/vendor/*/**', { force: true });
}

/**
 * Copy all vendors files previously created into ./build/src/vendor
 * Prior to this task, the task buildVendor should have been executed
 */
function mergeSourceVendor() {
    return src([
        './build/composer/vendor/**'
    ], { base: './build/composer/vendor/' })
        .pipe(dest('./build/src/vendor'));
}

exports.cleanSourceVendor = cleanSourceVendor;
exports.mergeSourceVendor = mergeSourceVendor;

exports.merge = series( cleanSourceVendor, mergeSourceVendor);
