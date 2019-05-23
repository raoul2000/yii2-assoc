const gutil = require('gulp-util');
const ftp = require( 'vinyl-ftp' );
const sftp = require('gulp-sftp');

// task for deploying files on the server (FTP or SFTP)

gulp.task('deploy', function() {
    const config = require('./sftp-config.json');

    const globs = [
        'folder/file',
        'folder/file',
        'folder/file',
    ];

    if (config.type == 'ftp') {
        //  FTP version
        const conn = ftp.create( {
            host:     config.host,
            user:     config.user,
            password: config.password,
            port:     config.port,
            parallel: 10,
            reload:   true,
            debug:    function(d){console.log(d);},
            log:      gutil.log
        });
        return gulp.src( globs, { base: '.', buffer: false } )
            .pipe( conn.newer( '/dest_folder/' ) ) // only upload newer files
            .pipe( conn.dest( '/dest_folder/' ) );
    } else {
        // SFTP version
        const conn = sftp({
                host: config.host,
                user: config.user,
                pass: config.password,
                port: config.port,
                remotePath: config.remote_path,
            });
        return gulp.src(globs, { base: '.', buffer: false } )
            .pipe(conn);
    }
});
