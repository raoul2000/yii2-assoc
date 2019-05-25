const { series, src, dest } = require('gulp');
const { task1 } = require('./gulp-task/task1');
const del = require('del');
const notify = require('gulp-notify');
const rename = require("gulp-rename");
const exec = require('child_process').exec;

function clean() {
    return del('./build/**', { force: true });
}


function copy() {
    return src([
        './src/**',
        '!./src/runtime/*/**',
        '!./src/vendor/**',
        '!./src/web/assets/*/**'
    ], { base: './src/' })
        .pipe(dest('./build'));
}

function createFolders() {
    return src('*.*', {read : false})
    .pipe( dest('./build/runtime'))
    .pipe( dest('./build/runtime/cache'))
    .pipe( dest('./build/runtime/logs'));

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

function ping() {
    return new Promise( (resolve, reject) => {
        exec('ping localhost', function (err, stdout, stderr) {
            if(err) {
                reject(err);
            } else {
                console.log(stdout);
                console.log(stderr);
                resolve(true);
            }
          });      
    });
}

exports.copy = copy;
exports.clean = clean;
exports.ping = ping;
//exports.default = series(clean, copyConfig, createFolders);
exports.default = series(clean, copy);