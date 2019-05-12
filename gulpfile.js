const { series,  src, dest } = require('gulp');
const { task1 } = require('./gulp-task/task1');
const del = require('del');

function clean() {
    return del('./build/**' , {force:true});
}


function copy() {
    return src([
        './src/assets/**',
        './src/commands/**',
        './src/components/**',
    ]).pipe(dest('./build/src'));
}

exports.copy = copy;
exports.clean = clean;
exports.default = task1