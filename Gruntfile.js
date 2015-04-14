	module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		less: {
			all: {
				options: {
					compress: true
				},
				files: {
					'css/adjusted-bounce-rate.css': [
						'less/adjusted-bounce-rate.less'
					]
				}
			}
		},

		concat: {
			options: {
				separator: ';' +
					grunt.util.linefeed +
					grunt.util.linefeed,
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
					'<%= grunt.template.today("yyyy-mm-dd") %> */' +
					grunt.util.linefeed
			},
			js: {
				files: {
					'js/adjusted-bounce-rate.concat.js': [
						'lib/ba-debug.min.js',
						'bower_components/rsvp/rsvp.min.js',
						'bower_components/bootstrap/dist/js/bootstrap.min.js',
						'js/adjusted-bounce-rate.js',
						'js/views/OptionsTabView.js'
					],
					'js/adjusted-bounce-rate-frontend.concat.js': [
						'lib/ba-debug.min.js',
						'js/adjusted-bounce-rate-frontend.js'
					]
				}
			}
		},

		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
					'<%= grunt.template.today("yyyy-mm-dd") %> */' +
					grunt.util.linefeed,
				mangle: false
			},
			dist: {
				files: {
					'js/adjusted-bounce-rate.dist.js': 'js/adjusted-bounce-rate.concat.js',
					'js/adjusted-bounce-rate-frontend.dist.js': 'js/adjusted-bounce-rate-frontend.concat.js'
				}
			}
		},

		clean: {
			dist: ['js/*.concat.js']
		},

		watch: {
			grunt: {
				files: ['Gruntfile.js'],
				tasks: ['default']
			},

			less: {
				files: 'less/**/*.less',
				tasks: ['less']
			},

			js: {
				files: [
					'js/**/*.js',
					'!js/**/*.concat.js',
					'!js/**/*.dist.js'
				],
				tasks: ['concat']
			}
		}
	});

	//Load tasks.
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	//Register tasks.
	grunt.registerTask('build', ['less', 'concat', 'uglify', 'clean']);
	grunt.registerTask('dist', ['build']);
	grunt.registerTask('default', ['build', 'watch']);

};