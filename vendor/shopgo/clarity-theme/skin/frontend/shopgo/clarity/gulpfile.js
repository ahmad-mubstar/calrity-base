// Load plugins
var
    gulp = require('gulp'),
    less = require('gulp-less'),
    flipper = require('gulp-css-flipper'),
    rename = require("gulp-rename"),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    rimraf = require('gulp-rimraf'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    sourcemaps = require('gulp-sourcemaps'),
    gulpif = require('gulp-if'),
    browserSync = require('browser-sync'),
    env = process.env.NODE_ENV || 'production'; // production or development


// CSS
gulp.task('css', function () {
    var stream = gulp
        .src('src/less/style.less')
        .pipe(gulpif(env === 'development', sourcemaps.init()))
        .pipe(less().on('error', notify.onError(function (error) {
            return 'Error compiling LESS: ' + error.message;
        })))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('css'));

    return stream
        .pipe(gulp.dest('css'))
        .pipe(notify({message: 'Successfully compiled LESS'}))
        .pipe(browserSync.reload({stream: true}));
});

// CSS Flip
gulp.task('flip', ['css'], function () {
    var stream = gulp
        .src('css/style.css')
        .pipe(rename({
            suffix: "-rtl"
        }))
        .pipe(flipper())
        .pipe(gulpif(env === 'production', minifycss({processImport: 0})))
        .pipe(gulp.dest('css'));

    return stream
        .pipe(gulp.dest('css'))
        .pipe(notify({
            message: 'Successfully flip CSS'
        }))
        .pipe(browserSync.reload({stream: true}));
});

// minify css
gulp.task('minifyless', function () {
    var stream = gulp
        .src('css/style.css')
        .pipe(gulpif(env === 'production', minifycss({processImport: 0})))
        .pipe(gulp.dest('css'));

    return stream
        .pipe(gulp.dest('css'))
        .pipe(notify({
            message: 'Successfully Minify LESS'
        }))
});

// JS
gulp.task('js', function () {
    var scripts = [
        'bower_components/bootstrap/js/transition.js',
        'bower_components/bootstrap/js/collapse.js',
        'bower_components/bootstrap/js/carousel.js',
        'bower_components/bootstrap/js/dropdown.js',
        'bower_components/bootstrap/js/modal.js',
        'bower_components/bootstrap/js/tab.js',
        'src/js/jquery.flexslider.js',
        'src/js/script.js'
    ];

    var stream = gulp
        .src(scripts)
        .pipe(gulpif(env === 'development', sourcemaps.init()))
        .pipe(gulpif(env === 'production', uglify()))
        .pipe(concat('script.js'))
        .pipe(gulpif(env === 'development', sourcemaps.write()));


    return stream
        .pipe(gulp.dest('js'))
        .pipe(notify({message: 'Successfully compiled JavaScript'}))
        .pipe(browserSync.reload({stream: true}));
});

// BrowserSync
gulp.task('browser-sync', function () {
    browserSync({
        proxy: "localhost/clarity/",
        reloadOnRestart: false,
        browser: "firefox"
    });
});

// Images
gulp.task('images', function () {
    return gulp
        .src('src/images/**/*')
        .pipe(gulp.dest('images'))
        .pipe(notify({message: 'Successfully processed image'}));
});

// Fonts
gulp.task('fonts', function () {
    return gulp
        .src([
            'bower_components/bootstrap/fonts/**/*',
            'bower_components/font-awesome/fonts/**/*',
            'src/fonts/**/*'
        ])
        .pipe(gulp.dest('fonts'))
        .pipe(notify({message: 'Successfully processed font'}));
});

// Rimraf
gulp.task('rimraf', function () {
    return gulp
        .src(['css', 'js', 'images'], {read: false})
        .pipe(rimraf());
});

// Default task
gulp.task('default', ['rimraf'], function () {
    gulp.start('flip', 'js', 'images', 'fonts');
});

// Watch
gulp.task('watch', function () {

    // Watch .less files
    gulp.watch('src/less/**/*.less', ['flip']);

    // Watch .js files
    gulp.watch('src/js/**/*.js', ['js']);

    // Watch image files
    gulp.watch('src/images/**/*', ['images']);

    // Watch fonts
    gulp.watch('bower_components/bootstrap/fonts/**/*', ['fonts']);

});

// Default task
gulp.task('default', ['browser-sync', 'watch'], function () {

});
