const { series, parallel} = require('gulp');
const {zipVendor, buildVendor, copyComposer, composerInstall } = require('./gulp-task/build-vendor');
const {buildSource, updateIndex, zipSource, copySource, copyConfig} = require('./gulp-task/build-source');
const {cleanSourceVendor, mergeSourceVendor} = require('./gulp-task/merge-source-vendor');

const del = require('del');
const exec = require('child_process').exec;

function clean() {
    return del('./build/**', { force: true });
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

const option1 = series(clean, 
    parallel(buildSource, buildVendor),
    mergeSourceVendor);

exports.cleanSourceVendor = cleanSourceVendor;
exports.mergeSourceVendor = mergeSourceVendor;
exports.updateIndex = updateIndex;

exports.clean = clean;
exports.ping = ping;
exports.copyComposer = copyComposer;
exports.composerInstall = composerInstall;
exports.buildVendor = buildVendor;
exports.zipSource = zipSource;
exports.zipVendor = zipVendor;
exports.copySource = copySource;
exports.default = option1;