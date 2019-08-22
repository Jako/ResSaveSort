module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        banner: '/*!\n' +
            ' * <%= modx.name %> - <%= modx.description %>\n' +
            ' * Version: <%= modx.version %>\n' +
            ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
            ' */\n',
        usebanner: {
            css: {
                options: {
                    position: 'bottom',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/ressavesort/css/mgr/ressavesort.min.css'
                    ]
                }
            },
            js: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/ressavesort/js/mgr/ressavesort.min.js'
                    ]
                }
            }
        },
        uglify: {
            mgr: {
                src: [
                    'source/js/mgr/ressavesort.js',
                    'source/js/mgr/ressavesort.grid.js'
                ],
                dest: 'assets/components/ressavesort/js/mgr/ressavesort.min.js'
            }
        },
        sass: {
            options: {
                implementation: require('node-sass'),
                outputStyle: 'expanded',
                sourcemap: false
            },
            mgr: {
                files: {
                    'source/css/mgr/ressavesort.css': 'source/sass/mgr/ressavesort.scss'
                }
            }
        },
        cssmin: {
            mgr: {
                src: [
                    'source/css/mgr/ressavesort.css'
                ],
                dest: 'assets/components/ressavesort/css/mgr/ressavesort.min.css'
            }
        },
        postcss: {
            options: {
                processors: [
                    require('pixrem')(),
                    require('autoprefixer')()
                ]
            },
            mgr: {
                files: {
                    'source/css/mgr/logrequest.css': 'source/css/mgr/logrequest.css'
                }
            }
        },
        watch: {
            js: {
                files: [
                    'source/**/*.js'
                ],
                tasks: ['uglify', 'usebanner:js']
            },
            css: {
                files: [
                    'source/**/*.scss'
                ],
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner:css']
            },
            config: {
                files: [
                    '_build/config.json'
                ],
                tasks: ['default']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php',
                    dest: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright \d{4}(-\d{4})? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2013 ? '2013-' : '') + new Date().getFullYear() + ' by'
                    }]
                }
            },
            version: {
                files: [{
                    src: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php',
                    dest: 'core/components/ressavesort/model/ressavesort/ressavesort.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'sass', 'postcss', 'cssmin', 'usebanner']);
};
