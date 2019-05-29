const { series, parallel} = require('gulp');
const {zipVendor, buildVendor, copyComposer, composerInstall } = require('./gulp-task/build-vendor');
const {buildSource, updateIndex, zipSource, copySource, copyConfig} = require('./gulp-task/build-source');
const {cleanSourceVendor, mergeSourceVendor} = require('./gulp-task/merge-source-vendor');
const {deploySFtp} = require('./gulp-task/deploy-sftp');
const {deployFtp} = require('./gulp-task/deploy-ftp');

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



//exports.deploySFtp = deploySFtp; // not working
exports.deployFtp = deployFtp;

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

exports.buildSource = buildSource;

// default task : build source and vendor and produce a folder ready to deploy
exports.default = series(
    clean, 
    parallel(buildSource, buildVendor),
    mergeSourceVendor
);