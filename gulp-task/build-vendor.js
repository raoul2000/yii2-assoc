const { series, src, dest } = require('gulp');
const exec = require('child_process').exec;
const del = require('del');
const zip = require('gulp-zip');

function cleanComposer() {
    return del('./build/composer/**', { force: true });
}

function copyComposer() {
    return src([
        './src/composer.lock',
        './src/composer.json'   
    ], { base: './src/' })
        .pipe(dest('./build/composer'));
}

function composerInstall() {
    return new Promise((resolve, reject) => {
        const composer = exec('composer install --no-dev',
            {
                "cwd": './build/composer'
            });
        composer.stdout.on('data', (data) => {
            console.log(data.toString().replace(/(\n|\r)+$/, ''));
        });
        // note that composer output messages to stderr
        // see https://github.com/composer/composer/issues/3795
        composer.stderr.on('data', (data) => {
            console.log(data.toString().replace(/(\n|\r)+$/, ''));
        });

        composer.on('exit', (code) => {
            if (code == 0) {
                resolve(true);
            } else {
                reject(`composer install failed - exit code = ${code}`);
            }
        });
    });
}

function zipVendor() {
    return src([
        './build/composer/vendor/**'
    ])
    .pipe(zip('vendor.zip'))
    .pipe(dest('./build/zip'));
}


exports.copyComposer = copyComposer;
exports.composerInstall = composerInstall;
exports.cleanComposer = cleanComposer;
exports.zipVendor = zipVendor;
exports.buildVendor = series(cleanComposer, copyComposer, composerInstall);