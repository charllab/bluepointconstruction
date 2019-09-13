const babel = require('gulp-babel');
const browserSync = require('browser-sync');
const concat = require('gulp-concat');
const connect = require('gulp-connect-php');
const cssnano = require('cssnano');
const cssnext = require('postcss-cssnext');
const del = require('del');
const gulp = require('gulp');
const gutil = require('gulp-util');
const partialimport = require('postcss-easy-import');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const sass = require('gulp-sass');
const rename = require("gulp-rename");
const replace = require('gulp-replace');
const dotenv = require('dotenv').config({
    path: process.env.PWD + '/.env'
});

const publicDir = 'public_html/';

/* -------------------------------------------------------------------------------------------------
PostCSS Plugins
-------------------------------------------------------------------------------------------------- */

const pluginsDev = [
    partialimport,
    cssnext({
        features: {
            colorHexAlpha: false
        }
    })
];
const pluginsProd = [
    partialimport,
    cssnano,
    cssnext({
        features: {
            colorHexAlpha: false
        }
    })
];

/* -------------------------------------------------------------------------------------------------
Header & Footer JavaScript Boundles
-------------------------------------------------------------------------------------------------- */

const headerJS = [
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/moment/min/moment.min.js',
    'node_modules/parsleyjs/dist/parsley.min.js',
    'node_modules/list.js/dist/list.min.js',
    'node_modules/clipboard/dist/clipboard.min.js',
    'node_modules/flatpickr/dist/flatpickr.min.js',
    'node_modules/holderjs/holder.js',
    'node_modules/fontfaceobserver/fontfaceobserver.js',
    'node_modules/magnific-popup/dist/jquery.magnific-popup.min.js'
];

const footerJS = [
    'src/js/app.js'
];

/* -------------------------------------------------------------------------------------------------
Installation and Other Tasks
-------------------------------------------------------------------------------------------------- */

gulp.task('default');

gulp.task('cleanup', () => {
    return del([publicDir + 'assets/**/*']);
});

gulp.task('cache-bust', function(){
    return gulp.src(['templates/_layouts/base.twig'])
        .pipe(replace('@@hash', ((new Date()).valueOf().toString()) + (Math.floor((Math.random()*1000000)+1).toString())))
        .pipe(rename("base-dist.twig"))
        .pipe(gulp.dest('templates/_layouts'));
});

/* -------------------------------------------------------------------------------------------------
Development Tasks
-------------------------------------------------------------------------------------------------- */

gulp.task('copy-images-dev', () => {
    return gulp.src('src/images/**')
        .pipe(gulp.dest(publicDir + 'assets/images'))
});

gulp.task('copy-fonts-dev', () => {
    return gulp.src('src/fonts/**')
        .pipe(gulp.dest(publicDir + 'assets/fonts'))
});

gulp.task('style-dev', () => {
    return gulp
        .src("src/style/style.scss")
        .pipe(sourcemaps.init())
        .pipe(sass().on("error", sass.logError))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(publicDir + 'assets/style'))
        .pipe(browserSync.stream({ match: "**/*.css" }));
});

gulp.task('header-scripts-dev', () => {
    return gulp.src(headerJS)
        .pipe(plumber({ errorHandler: onError }))
        .pipe(concat('header-bundle.js'))
        .pipe(gulp.dest(publicDir + 'assets/js'));
});

gulp.task('footer-scripts-dev', () => {
    return gulp.src(footerJS)
        .pipe(plumber({ errorHandler: onError }))
        .pipe(babel({
            presets: ['env']
        }))
        .pipe(concat('footer-bundle.js'))
        .pipe(gulp.dest(publicDir + 'assets/js'));
});

gulp.task('app-scripts-dev', () => {
    var glob = [publicDir + 'assets/js/header-bundle.js', publicDir + 'assets/js/footer-bundle.js'];

    return gulp.src(glob)
        .pipe(sourcemaps.init())
        .pipe(concat('app-bundle.js'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(publicDir + 'assets/js'))
        .on('end', function() {
            del(glob)
        });
});

gulp.task('reload-js', gulp.series('footer-scripts-dev', 'header-scripts-dev', 'app-scripts-dev'), (done) => {
    browserSync.reload();
    done();
});

gulp.task('reload-fonts', gulp.series('copy-fonts-dev'), (done) => {
    browserSync.reload();
    done();
});

gulp.task('reload-images', gulp.series('copy-images-dev'), (done) => {
    browserSync.reload();
    done();
});

gulp.task('watch', () => {
    gulp.watch(['src/style/**/*.scss'], gulp.series('style-dev'));
    gulp.watch(['src/js/**'], gulp.series('reload-js'));
    gulp.watch(['src/fonts/**'], gulp.series('reload-fonts'));
    gulp.watch(['src/images/**'], gulp.series('reload-images'));
    gulp.watch(['templates/**', '!templates/**/*-dist.twig'], gulp.series('cache-bust'));
});

gulp.task('connect-sync', () => {
    return connect.server({
        base: publicDir,
        port: '3020'
    }, () => {
        browserSync({
            proxy: process.env.SITE_URL,
            open: false
        });
    });
});

gulp.task('dev', gulp.series(
    'cleanup',
    gulp.parallel(
        'copy-images-dev',
        'copy-fonts-dev',
        'style-dev',
        'header-scripts-dev',
        'footer-scripts-dev'
    ),
    'app-scripts-dev',
    'cache-bust',
    gulp.parallel(
        'watch',
        'connect-sync'
    )
));

/* -------------------------------------------------------------------------------------------------
Production Tasks
-------------------------------------------------------------------------------------------------- */

gulp.task('copy-fonts-prod', () => {
    return gulp.src('src/fonts/**/*')
        .pipe(gulp.dest(publicDir + 'assets/fonts'))
});

gulp.task('copy-images-prod', () => {
    return gulp.src('src/images/**/*')
        .pipe(gulp.dest(publicDir + 'assets/images'))
});

gulp.task('style-prod', () => {
    return gulp.src('src/style/style.scss')
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sass().on("error", sass.logError))
        .pipe(postcss(pluginsProd))
        .pipe(gulp.dest(publicDir + 'assets/style'))
});

gulp.task('header-scripts-prod', () => {
    return gulp.src(headerJS)
        .pipe(plumber({ errorHandler: onError }))
        .pipe(concat('header-bundle.js'))
        .pipe(gulp.dest(publicDir + 'assets/js'))
});

gulp.task('footer-scripts-prod', () => {
    return gulp.src(footerJS)
        .pipe(plumber({ errorHandler: onError }))
        .pipe(babel({
            presets: ['env']
        }))
        .pipe(concat('footer-bundle.js'))
        .pipe(gulp.dest(publicDir + 'assets/js'))
});

gulp.task('app-scripts-prod', () => {
    var glob = [publicDir + 'assets/js/header-bundle.js', publicDir + 'assets/js/footer-bundle.js'];

    return gulp.src(glob)
        .pipe(concat('app-bundle.js'))
        .pipe(uglify())
        .pipe(gulp.dest(publicDir + 'assets/js'))
        .on('end', function() {
            del(glob)
        });
});

gulp.task('prod', gulp.series(
    'cleanup',
    gulp.parallel(
        'copy-fonts-prod',
        'copy-images-prod',
        'style-prod',
        'header-scripts-prod',
        'footer-scripts-prod'
    ),
    'app-scripts-prod',
    'cache-bust'
));

/* -------------------------------------------------------------------------------------------------
Utility Tasks
-------------------------------------------------------------------------------------------------- */

const onError = (err) => {
    gutil.beep();
    gutil.log(errorMsg + ' ' + err.toString());
    this.emit('end');
};

const errorMsg = '\x1b[41mError\x1b[0m';
