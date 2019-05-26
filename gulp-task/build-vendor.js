const { series, src, dest } = require('gulp');
const exec = require('child_process').exec;
const spawn = require('child_process').spawn;


function copyComposer() {
    return src([
        './src/composer.lock',
        './src/composer.json'
    ], { base: './src/' })
        .pipe(dest('./build/vendor'));
}

function composerInstall() {
    return new Promise((resolve, reject) => {
        const composer = exec('composer install --no-dev',
            {
                "cwd": './build/vendor'
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

exports.copyComposer = copyComposer;
exports.composerInstall = composerInstall;