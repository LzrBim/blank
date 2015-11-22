// Include gulp
var gulp = require('gulp'); 

// Include Our Plugins
var jshint 		= require('gulp-jshint');
var minifyCss = require('gulp-minify-css');
var concat 		= require('gulp-concat');
var uglify 		= require('gulp-uglify');
var rename 		= require('gulp-rename');

/* FRONT CSS 
----------------------------------------------------------------------------- */
gulp.task('css', function() {
  return gulp.src([
		'public/css/style.css'
	])
	.pipe(concat('all.concat.css'))
	.pipe(gulp.dest('public/css/'))
	.pipe(rename('all.min.css'))
	.pipe(minifyCss())		
	.pipe(gulp.dest('public/css/'));
});



/* FRONT JS
----------------------------------------------------------------------------- */
gulp.task('lintScript', function() {
	return gulp.src([
		'public/js/main.js',
		'public/js/jquery.validate.min.js'
	])
	.pipe(jshint())
	.pipe(jshint.reporter('default'));
});


gulp.task('script', function() {
	return gulp.src([
		'public/js/main.js',
		'public/js/jquery.validate.min.js'
	])
	.pipe(concat('all.concat.js'))
	.pipe(gulp.dest('public/js/'))
	.pipe(rename('all.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('public/js/'));
	
});

/* ADMIN CSS 
----------------------------------------------------------------------------- */
gulp.task('adminCss', function() {
  return gulp.src([
		'public/admin_css/style.css'
	])
	.pipe(concat('all.concat.css'))
	.pipe(gulp.dest('public/admin_css/'))
	.pipe(rename('all.min.css'))
	.pipe(minifyCss())		
	.pipe(gulp.dest('public/admin_css/'));
});

/* ADMIN JS
----------------------------------------------------------------------------- */
gulp.task('lintAdminScript', function() {
	return gulp.src([
		'public/admin_js/main.js'	
	])
	.pipe(jshint())
	.pipe(jshint.reporter('default'));
});


gulp.task('adminScript', function() {
	return gulp.src([
		'public/admin_js/main.js',
		'public/admin_js/plugins/jquery.validate.min.js',
		'public/admin_js/plugins/metisMenu.min.js',
	])
	.pipe(concat('all.concat.js'))
	.pipe(gulp.dest('public/admin_js/'))
	.pipe(rename('all.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('public/admin_js/'));
	
});



/* WATCH
----------------------------------------------------------------------------- */
gulp.task('watch', function() {
	
	//CSS
	gulp.watch('public/css/*.css', ['css']);	
	
	//SCRIPT
	gulp.watch([ 
		'public/js/main.js'
	], ['lintScript', 'script']);	
	
	
	//ADMIN CSS
	gulp.watch([ 
		'public/admin_css/*.css'
	], ['adminCss']);		
	
	//ADMIN	JS
	gulp.watch([
		'public/admin_js/main.js',
	], ['lintAdminScript', 'adminScript']);
		
	
});

// Default Task
gulp.task('default', ['css','lintScript','script', 'adminCss', 'lintAdminScript', 'adminScript','watch']);

gulp.task('refresh', ['css','lintScript','script', 'adminCss', 'lintAdminScript', 'adminScript']);