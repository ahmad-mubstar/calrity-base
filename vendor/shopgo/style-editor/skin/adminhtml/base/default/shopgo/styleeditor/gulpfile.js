// Load plugins
var
    gulp = require('gulp'),
    less = require('gulp-less'),
    flipper = require('gulp-css-flipper'),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    rimraf = require('gulp-rimraf'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    rename = require("gulp-rename");

// CSS
gulp.task('css', function () {
    var stream = gulp
        .src('src/less/style.less')
        .pipe(less().on('error', notify.onError(function (error) {
            return 'Error compiling LESS: ' + error.message;
        })))
        .pipe(minifycss({processImport: 0}));


    return stream
        .pipe(gulp.dest('css'))
        .pipe(notify({
            message: 'Successfully compiled LESS'
        }));
});


// JS
gulp.task('js', function () {
    var scripts = [
        'bower_components/es5-shim/es5-sham.js',
        'bower_components/jquery/dist/jquery.js',
        'bower_components/jquery-address/src/jquery.address.js',
        'bower_components/angular/angular.js',
        'bower_components/html5shiv/dist/html5shiv.js',
        'bower_components/nprogress/nprogress.js',
        'src/js/spectrum.js',
        'bower_components/sweetalert/lib/sweet-alert.js',
        'bower_components/angular-file-upload/angular-file-upload.js',
        'bower_components/material-design-hamburger/dist/material-design-hamburger.js',
        'src/js/script.js'
    ];

    var stream = gulp
        .src(scripts)
        .pipe(concat('script.js'))
        .pipe(uglify());


    return stream
        .pipe(gulp.dest('js'))
        .pipe(notify({
            message: 'Successfully compiled JavaScript'
        }));
});

// Images
gulp.task('images', function () {
    return gulp
        .src('src/images/**/*')
        .pipe(gulp.dest('images'))
        .pipe(notify({
            message: 'Successfully processed image'
        }));
});

// Fonts
gulp.task('fonts', function () {
    return gulp
        .src(['bower_components/fontawesome/fonts/**/*'])
        .pipe(gulp.dest('fonts'))
        .pipe(notify({
            message: 'Successfully processed font'
        }));
});

// SVG
gulp.task('svg', function () {
    return gulp
        .src(['bower_components/svg-loaders/svg-loaders/**/*'])
        .pipe(gulp.dest('svg-loaders'))
        .pipe(notify({
            message: 'Successfully processed svg loaders'
        }));
});


// Rimraf
gulp.task('rimraf', function () {
    return gulp
        .src(['css', 'js', 'images'], {
            read: false
        })
        .pipe(rimraf());
});

// Default task
gulp.task('default', ['rimraf'], function () {
    gulp.start('css', 'js', 'images', 'fonts');
});

// Watch
gulp.task('watch', function () {

    // Watch .less files
    gulp.watch('src/less/**/*.less', ['css']);

    // Watch .js files
    gulp.watch('src/js/**/*.js', ['js']);

    // Watch image files
    gulp.watch('src/images/**/*', ['images']);

});