module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		concat: {
			options: {
				separator: ';' +
					grunt.util.linefeed +
					grunt.util.linefeed,
				stripBanners: false
			},
			js: {
				files: {
					'js/adjusted-bounce-rate.concat.js': [
						'lib/ba-debug.min.js',
						'js/adjusted-bounce-rate.js'
					]
				}
			}
		},

		uglify: {
			options: {
				mangle: false,
				preserveComments: 'some',
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
					'<%= grunt.template.today("yyyy-mm-dd") %> */' +
					grunt.util.linefeed +
					grunt.util.linefeed
			},
			dist: {
				files: {
					'js/adjusted-bounce-rate.min.js': ['js/adjusted-bounce-rate.concat.js']
				}
			}
		},

		watch: {
			grunt: {
				files: ['Gruntfile.js'],
				tasks: ['default']
			},

			js: {
				files: 'js/adjusted-bounce-rate.js',
				tasks: ['build']
			}
		}
	});

	//Load tasks.
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	//Register tasks.
	grunt.registerTask('build', ['concat', 'uglify']);
	grunt.registerTask('default', ['build', 'watch']);

};