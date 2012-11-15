module.exports = function(grunt) {
	
	// Concat and minify files
	grunt.initConfig({
		pkg:'<json:package.json>',
		meta: {
			banner: "/*! <%= pkg.name %> - <%= grunt.template.today('yyyy-mm-dd') %>\n" +
					"	author: <%= pkg.author %>\n" +
					"	dependencies: <%= pkg.dependencies %>\n" +
					"	description: <%= pkg.description %> */"
		},
		lint: {
			files: ['src/*.js', 'src/view/*.js']
		},
		min: {
			dist: {
				src: ['<banner>', 'src/main.js', 'src/*.js', 'src/view/*.js'],
				dest: '../li3_filemanager.min.js'
			}
		}
	});
	
	// Default tasks
	grunt.registerTask('default', 'lint min');
	
};