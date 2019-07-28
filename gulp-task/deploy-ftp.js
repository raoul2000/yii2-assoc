const { src } = require('gulp');
const gutil = require('gulp-util');
const ftp = require( 'vinyl-ftp' );

// task for deploying files on the server (FTP or SFTP)

function deployFtp() {
    const config = require('./ftp.json');


    const conn = ftp.create( {
        host:     config.host,
        user:     config.user,
        password: config.password,
        port:     21,
        parallel: 1,
        reload:   true,
        debug:    function(d){console.log(d);},
        log:      gutil.log
    });

    const globs = [
        './build/src/**/*'
    ];

    return src( globs, { base: './build/src', buffer: false } )
        .pipe( conn.dest( config.remote_path) );

}

exports.deployFtp = deployFtp;
