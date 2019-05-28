const { src } = require('gulp');
const GulpSSH = require('gulp-ssh')

// task for deploying files on the server SFTP
// ERROR after a lot of files have been uploaded

function deploySFtp() {
    const config = require('./sftp-prod.json');

    var gulpSSH = new GulpSSH({
        ignoreErrors: false,
        sshConfig: {
            host: config.host,
            username: config.user,
            password : config.password,
            port: config.port
        }
      })
    const globs = [
        './build/src/**/*'
    ];

    return  src(globs)
    .pipe(gulpSSH.dest('/dev1'));
}

exports.deploySFtp = deploySFtp;
