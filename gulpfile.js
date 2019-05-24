const { series, src, dest } = require('gulp');
const { task1 } = require('./gulp-task/task1');
const del = require('del');
const notify = require('gulp-notify');
const rename = require("gulp-rename");

function clean() {
    return del('./build/**', { force: true });
}


function copy() {
    return src([
        './src/assets/**',
        './src/commands/**',
        './src/components/**',
    ], { base: './src/' })
        .pipe(dest('./build'));
}

function copyConfig() {
    return src(
        [
            './src/config/**',
            '!./src/config/db.php', // ignore DB params
        ],
        { base: './src/' }
    )
        .pipe(rename((path) => {
            if (path.basename.endsWith('.prod')) {
                console.log(`   renaming PROD file : ${path.basename}${path.extname}`);
                path.basename = path.basename.replace(/\.prod$/,'');
            }
        }))
        .pipe(dest('./build'));
}

exports.copy = copy;
exports.clean = clean;
exports.default = series(clean, copyConfig);