const { src, dest, series } = require('gulp');
const rename = require("gulp-rename");
const del = require('del');
const zip = require('gulp-zip');
const fs = require('fs');

/**
 * Updates the file ./web/index.php for Production purposes
 */
function updateIndex() {
    return new Promise((resolve, reject) => {
        const filepath = './build/src/web/index.php';
        fs.readFile(filepath, 'utf-8', (err, data) => {
            if (err) {
                reject(err);
            } else {
                const result = data
                    .replace(
                        "defined('YII_DEBUG') or define('YII_DEBUG', true);",
                        "//defined('YII_DEBUG') or define('YII_DEBUG', true);"
                    )
                    .replace(
                        "defined('YII_ENV') or define('YII_ENV', 'dev');",
                        "//defined('YII_ENV') or define('YII_ENV', 'dev');"
                    );
                fs.writeFile(filepath,result, (err ) => {
                    if(err) {
                        reject(err);
                    } else {
                        resolve(true);
                    }
                })
            }
        })
    });
}

function cleanSource() {
    return del('./build/source/**', { force: true });
}

function copySource() {
    return src([
        './src/**',
        '!./src/runtime/*/**',
        '!./src/vendor/*/**',
        '!./src/tests/**',
        '!./src/web/assets/*/**',
        // removes files from source root
        '!./src/*.yml',
        '!./src/*.json',
        '!./src/*.lock',
        '!./src/*.md',
        '!./src/*.xml',
        '!./src/yii',
        '!./src/yii.bat',
    ], { base: './src/' })
        .pipe(dest('./build/src'));
}

/**
 * Example on how to create an empty folder in build
 */
function createSourceFolders() {
    return src('*.*', { read: false })
        .pipe(dest('./build/src/runtime'))
        .pipe(dest('./build/src/runtime/cache'))
        .pipe(dest('./build/src/runtime/logs'));
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
                path.basename = path.basename.replace(/\.prod$/, '');
            }
        }))
        .pipe(dest('./build/src'));
}

function zipSource() {
    return src([
        './build/src/**'
    ])
        .pipe(zip('source.zip'))
        .pipe(dest('./build/zip'));
}

exports.copySource = copySource;
exports.cleanSource = cleanSource;
exports.zipSource = zipSource;
exports.copyConfig = copyConfig;
exports.updateIndex = updateIndex;

exports.buildSource = series(cleanSource, copySource, updateIndex);