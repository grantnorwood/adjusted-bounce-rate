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
					grunt.util.linefeed +
					grunt.util.linefeed
			},
			js: {
				files: {
					'js/adjusted-bounce-rate.dist.js': [
						'bower_components/rsvp/rsvp.min.js',
						'bower_components/bootstrap/dist/js/bootstrap.min.js',
						'js/views/OptionsTabView.js',
						'js/adjusted-bounce-rate.js'
					]
				}
			}
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
					'!js/**/*.dist.js'
				],
				tasks: ['concat']
			}
		}
	});

	//Load tasks.
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');

	//Register tasks.
	grunt.registerTask('build', ['less', 'concat']);
	grunt.registerTask('dist', ['build']);
	grunt.registerTask('default', ['build', 'watch']);

};