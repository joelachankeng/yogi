const gulp = require('gulp');
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const environments = require('gulp-environments');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const runSequence  = require('run-sequence');
const browserSync = require('browser-sync').create();
const development = environments.development;
const production = environments.production;


gulp.task('run', function() {
    runSequence('serve');
    runSequence('css');
    runSequence('scripts');
});

gulp.task('watch', function() {
    //css changes 
    gulp.watch('./src/sass/**/*', ['css']).on('change', browserSync.reload);

    //js changes 
    gulp.watch(paths.scripts, ['scripts']).on('change', browserSync.reload);
    gulp.watch('./src/js/**/*', ['scripts']).on('change', browserSync.reload);
});

gulp.task('default', ['run', 'watch']);

const paths= {
    scripts: [
        './src/js/vendor/'
    ],
};

gulp.task('css', () => {
    const postCSSopts = [
        autoprefixer({ browsers: ['last 2 versions', '>2%'] }),
        cssnano,
    ];
    return gulp.src('./src/sass/style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(postCSSopts))
        .pipe(rename('styles.min.css'))
        .pipe(development(sourcemaps.write('.')))
        .pipe(gulp.dest(`./dist/`))
        .pipe(browserSync.stream());
});

gulp.task('scripts', () => {
    paths.scripts.push('./src/js/scripts.js');
    return gulp.src(paths.scripts)
        .pipe(sourcemaps.init())
        .pipe(concat('app.js'))
        .pipe(gulp.dest(`./dist/`))
        .pipe(uglify().on('error', function(e) {
            console.log(e);
        }))
        .pipe(rename('app.min.css'))
        .pipe(gulp.dest(`./dist/`))
        .pipe(browserSync.stream());
});

gulp.task('serve', function() {
    browserSync.init({
        proxy: "http://yogi.local"
    });
    
    gulp.watch('./*.php').on('change', browserSync.reload);
})



